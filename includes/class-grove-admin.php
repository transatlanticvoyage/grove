<?php
/**
 * Grove Admin Class
 */

class Grove_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_grove_driggs_get_data', array($this, 'grove_driggs_get_data'));
        add_action('wp_ajax_grove_driggs_update_field', array($this, 'grove_driggs_update_field'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Grove Hub',
            'Grove Hub', 
            'manage_options',
            'grovehub',
            array($this, 'grovehub_page'),
            'dashicons-palmtree',
            30
        );
    }
    
    public function grovehub_page() {
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        $current_site_url = get_site_url();
        
        // Ensure zen_sitespren table has a record for current site
        $this->ensure_sitespren_record();
        
        // Handle AJAX requests
        if (isset($_POST['action'])) {
            $this->handle_driggs_ajax();
            return;
        }
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🌳 Grove Hub - Driggs Site Manager</h1>
                
                <!-- Control Bar -->
                <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Current Site:</strong> <?php echo esc_html($current_site_url); ?>
                        </div>
                        <div>
                            <button id="save-all-btn" class="button button-primary">Save All Changes</button>
                            <button id="reset-btn" class="button button-secondary">Reset to Defaults</button>
                        </div>
                    </div>
                </div>
                
                <!-- Vertical Field Table -->
                <div style="background: white; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                    <div style="overflow-x: auto;">
                        <table id="driggs-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: 50px;">
                                        <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
                                    </th>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa; width: 250px;">Field Name</th>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa;">Value</th>
                                    <th style="padding: 0; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa; width: 20px;">stuff3</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <!-- Data will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
                    <p><strong>Instructions:</strong></p>
                    <ul>
                        <li>Click on any value to edit it inline</li>
                        <li>Toggle switches control boolean fields</li>
                        <li>Changes are saved automatically when you click out of a field</li>
                        <li>Use "Save All Changes" to force save all modifications</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <style type="text/css">
        /* Toggle Switch Styles */
        .driggs-toggle-switch {
            cursor: pointer !important;
        }
        
        .driggs-toggle-switch input[type="checkbox"]:focus + .driggs-toggle-slider {
            box-shadow: 0 0 1px #2196F3;
        }
        
        .driggs-toggle-slider {
            pointer-events: none;
        }
        
        .driggs-toggle-knob {
            pointer-events: none;
        }
        
        /* Ensure table cells are properly styled */
        #driggs-table td {
            vertical-align: middle;
        }
        
        #driggs-table td[data-field] {
            min-height: 30px;
        }
        
        #driggs-table textarea,
        #driggs-table input[type="text"],
        #driggs-table input[type="email"],
        #driggs-table input[type="number"] {
            font-family: inherit;
            font-size: 14px;
        }
        
        /* Roaring Div Styles */
        .roaring_div {
            width: 20px;
            height: 100%;
            border: 1px solid gray;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            background-color: #f0f0f0;
            transition: background-color 0.2s;
        }
        
        .roaring_div:hover {
            background-color: #e0e0e0;
            border-color: #666;
        }
        
        /* Ensure stuff3 column cells have no padding */
        #driggs-table td.stuff3-cell {
            padding: 0 !important;
            width: 20px !important;
        }
        
        /* Read-only field styling */
        .driggs-readonly-field {
            background-color: #f9f9f9 !important;
            color: #666 !important;
            font-style: italic !important;
            cursor: default !important;
            user-select: text !important;
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            pointer-events: auto !important;
        }
        
        .driggs-readonly-field:hover {
            background-color: #f9f9f9 !important;
        }
        </style>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            let currentData = {};
            let hasChanges = false;
            
            // Load initial data
            loadDriggsData();
            
            function loadDriggsData() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_driggs_get_data',
                        nonce: '<?php echo wp_create_nonce('grove_driggs_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            currentData = response.data;
                            displayData();
                        } else {
                            alert('Error loading driggs data: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error loading driggs data');
                    }
                });
            }
            
            function displayData() {
                let tbody = $('#table-body');
                tbody.empty();
                
                // Field definitions in exact order requested - display actual DB field names in bold
                const fields = [
                    {key: 'wppma_id', label: 'wppma_id', type: 'number'},
                    {key: 'wppma_db_only_created_at', label: 'wppma_db_only_created_at', type: 'datetime'},
                    {key: 'wppma_db_only_updated_at', label: 'wppma_db_only_updated_at', type: 'datetime'},
                    {key: 'id', label: 'id', type: 'text'},
                    {key: 'created_at', label: 'created_at', type: 'datetime'},
                    {key: 'sitespren_base', label: 'sitespren_base', type: 'text'},
                    {key: 'driggs_industry', label: 'driggs_industry', type: 'text'},
                    {key: 'driggs_city', label: 'driggs_city', type: 'text'},
                    {key: 'driggs_brand_name', label: 'driggs_brand_name', type: 'text'},
                    {key: 'driggs_site_type_purpose', label: 'driggs_site_type_purpose', type: 'text'},
                    {key: 'driggs_email_1', label: 'driggs_email_1', type: 'email'},
                    {key: 'driggs_address_full', label: 'driggs_address_full', type: 'textarea'},
                    {key: 'driggs_phone_1', label: 'driggs_phone_1', type: 'text'},
                    {key: 'driggs_special_note_for_ai_tool', label: 'driggs_special_note_for_ai_tool', type: 'textarea'},
                    {key: 'driggs_phone1_platform_id', label: 'driggs_phone1_platform_id', type: 'number'},
                    {key: 'driggs_cgig_id', label: 'driggs_cgig_id', type: 'number'},
                    {key: 'driggs_address_species_id', label: 'driggs_address_species_id', type: 'number'},
                    {key: 'driggs_logo_url', label: 'driggs_logo_url', type: 'logo_url'},
                    {key: 'driggs_citations_done', label: 'driggs_citations_done', type: 'boolean'},
                    {key: 'driggs_revenue_goal', label: 'driggs_revenue_goal', type: 'number'},
                    {key: 'true_root_domain', label: 'true_root_domain', type: 'text'},
                    {key: 'full_subdomain', label: 'full_subdomain', type: 'text'},
                    {key: 'webproperty_type', label: 'webproperty_type', type: 'text'},
                    {key: 'fk_users_id', label: 'fk_users_id', type: 'text'},
                    {key: 'updated_at', label: 'updated_at', type: 'datetime'},
                    {key: 'wpuser1', label: 'wpuser1', type: 'text'},
                    {key: 'wppass1', label: 'wppass1', type: 'password'},
                    {key: 'wp_plugin_installed1', label: 'wp_plugin_installed1', type: 'boolean'},
                    {key: 'wp_plugin_connected2', label: 'wp_plugin_connected2', type: 'boolean'},
                    {key: 'fk_domreg_hostaccount', label: 'fk_domreg_hostaccount', type: 'text'},
                    {key: 'is_wp_site', label: 'is_wp_site', type: 'boolean'},
                    {key: 'wp_rest_app_pass', label: 'wp_rest_app_pass', type: 'textarea'},
                    {key: 'ns_full', label: 'ns_full', type: 'text'},
                    {key: 'ip_address', label: 'ip_address', type: 'text'},
                    {key: 'is_starred1', label: 'is_starred1', type: 'text'},
                    {key: 'icon_name', label: 'icon_name', type: 'text'},
                    {key: 'icon_color', label: 'icon_color', type: 'text'},
                    {key: 'is_bulldozer', label: 'is_bulldozer', type: 'boolean'},
                    {key: 'is_competitor', label: 'is_competitor', type: 'boolean'},
                    {key: 'is_external', label: 'is_external', type: 'boolean'},
                    {key: 'is_internal', label: 'is_internal', type: 'boolean'},
                    {key: 'is_ppx', label: 'is_ppx', type: 'boolean'},
                    {key: 'is_ms', label: 'is_ms', type: 'boolean'},
                    {key: 'is_wayback_rebuild', label: 'is_wayback_rebuild', type: 'boolean'},
                    {key: 'is_naked_wp_build', label: 'is_naked_wp_build', type: 'boolean'},
                    {key: 'is_rnr', label: 'is_rnr', type: 'boolean'},
                    {key: 'is_aff', label: 'is_aff', type: 'boolean'},
                    {key: 'is_other1', label: 'is_other1', type: 'boolean'},
                    {key: 'is_other2', label: 'is_other2', type: 'boolean'},
                    {key: 'is_flylocal', label: 'is_flylocal', type: 'boolean'}
                ];
                
                fields.forEach(function(field) {
                    let tr = $('<tr style="cursor: pointer;"></tr>');
                    
                    // Apply special background color to specific rows
                    const specialBgFields = ['wppma_id', 'wppma_db_only_created_at', 'wppma_db_only_updated_at', 'id', 'created_at'];
                    const isSpecialBg = specialBgFields.includes(field.key);
                    
                    if (isSpecialBg) {
                        tr.css('background-color', '#9b9b9b');
                        tr.hover(function() {
                            $(this).css('background-color', '#8b8b8b');
                        }, function() {
                            $(this).css('background-color', '#9b9b9b');
                        });
                    } else {
                        tr.hover(function() {
                            $(this).css('background-color', '#f9f9f9');
                        }, function() {
                            $(this).css('background-color', '');
                        });
                    }
                    
                    // Checkbox column
                    let checkboxTd = $('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"></td>');
                    if (isSpecialBg) checkboxTd.css('background-color', '#9b9b9b');
                    let checkbox = $('<input type="checkbox" style="width: 20px; height: 20px;" data-field="' + field.key + '">');
                    checkboxTd.append(checkbox);
                    tr.append(checkboxTd);
                    
                    // Field name column - bold DB field names
                    let fieldNameTd = $('<td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #23282d; font-family: monospace;"></td>');
                    if (isSpecialBg) fieldNameTd.css('background-color', '#9b9b9b');
                    fieldNameTd.text(field.label);
                    tr.append(fieldNameTd);
                    
                    // Value column
                    let valueTd = $('<td style="padding: 8px; border: 1px solid #ddd;"></td>');
                    if (isSpecialBg) valueTd.css('background-color', '#9b9b9b');
                    let value = currentData[field.key] || '';
                    
                    if (field.type === 'boolean') {
                        // Toggle switch for boolean fields
                        let toggleSwitch = createToggleSwitch(field.key, value == 1);
                        valueTd.append(toggleSwitch);
                    } else if (field.key === 'wppma_id' || field.key === 'id' || field.type === 'datetime') {
                        // Read-only fields (ID fields and datetime fields)
                        valueTd.text(value);
                        valueTd.css('color', '#666');
                        valueTd.css('font-style', 'italic');
                        valueTd.css('cursor', 'default');
                        valueTd.css('user-select', 'text');
                        valueTd.css('-webkit-user-select', 'text');
                        valueTd.css('-moz-user-select', 'text');
                        valueTd.addClass('driggs-readonly-field');
                        valueTd.attr('title', 'This field is read-only (can select text but not edit)');
                    } else if (field.type === 'logo_url') {
                        // Special handling for logo URL with image preview
                        let logoContainer = $('<div style="display: flex; flex-direction: column; width: 100%; height: 100%;"></div>');
                        
                        // Editable text input for URL
                        let urlInput = $('<input type="text" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; margin-bottom: 5px; font-size: 12px;">');
                        urlInput.val(value || '');
                        urlInput.attr('data-field', field.key);
                        
                        // Image preview container
                        let imagePreview = $('<div style="width: 100%; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid #eee; border-radius: 3px; background: #f9f9f9;"></div>');
                        
                        // Function to update image preview
                        function updateImagePreview(url) {
                            if (url && url.trim() !== '') {
                                let img = $('<img style="max-width: 100%; max-height: 38px; object-fit: contain;">');
                                img.attr('src', url);
                                img.on('load', function() {
                                    imagePreview.html(img);
                                });
                                img.on('error', function() {
                                    imagePreview.html('<span style="color: #999; font-size: 11px;">Invalid image URL</span>');
                                });
                            } else {
                                imagePreview.html('<span style="color: #ccc; font-size: 11px;">No logo URL</span>');
                            }
                        }
                        
                        // Update preview on input change
                        urlInput.on('input', function() {
                            let newUrl = $(this).val();
                            updateImagePreview(newUrl);
                        });
                        
                        // Save on blur
                        urlInput.on('blur', function() {
                            let newValue = $(this).val();
                            if (newValue !== value) {
                                updateField(field.key, newValue);
                                currentData[field.key] = newValue;
                                hasChanges = true;
                            }
                        });
                        
                        // Initialize with current value
                        updateImagePreview(value);
                        
                        logoContainer.append(urlInput).append(imagePreview);
                        valueTd.append(logoContainer);
                        valueTd.css('padding', '6px');
                    } else {
                        // Editable text fields
                        valueTd.text(value);
                        valueTd.attr('data-field', field.key);
                        valueTd.attr('data-type', field.type);
                        valueTd.css('cursor', 'text');
                        valueTd.click(function() {
                            startInlineEdit($(this), value, field.key, field.type);
                        });
                    }
                    
                    tr.append(valueTd);
                    
                    // Add stuff3 column with roaring_div as shortcode copy button
                    let stuff3Td = $('<td class="stuff3-cell" style="padding: 0; border: 1px solid #ddd; width: 20px;"></td>');
                    if (isSpecialBg) stuff3Td.css('background-color', '#9b9b9b');
                    let roaring_div = $('<div class="roaring_div">R</div>');
                    
                    // Add click handler to copy shortcode
                    roaring_div.css('cursor', 'pointer');
                    roaring_div.attr('title', 'Copy shortcode for ' + field.label);
                    roaring_div.on('click', function() {
                        console.log('R button clicked for field:', field.key);
                        console.log('Current data:', currentData);
                        
                        // Get the wppma_id from the current data
                        let wppmaId = currentData.wppma_id || '1'; // Default to 1 if not found
                        let shortcode = '[sitespren wppma_id="' + wppmaId + '" field="' + field.key + '"]';
                        
                        console.log('Generated shortcode:', shortcode);
                        
                        // Copy to clipboard
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(shortcode).then(function() {
                                console.log('Clipboard copy successful');
                                // Visual feedback - change background color temporarily
                                let originalBg = roaring_div.css('background-color');
                                roaring_div.css('background-color', '#4CAF50');
                                roaring_div.text('✓');
                                
                                setTimeout(function() {
                                    roaring_div.css('background-color', originalBg);
                                    roaring_div.text('R');
                                }, 1000);
                            }).catch(function(error) {
                                console.error('Clipboard copy failed:', error);
                                // Fallback method
                                copyShortcodeToClipboardFallback(shortcode, roaring_div);
                            });
                        } else {
                            console.log('Using fallback clipboard method');
                            // Fallback for older browsers
                            copyShortcodeToClipboardFallback(shortcode, roaring_div);
                        }
                    });
                    
                    stuff3Td.append(roaring_div);
                    tr.append(stuff3Td);
                    
                    tbody.append(tr);
                });
            }
            
            function createToggleSwitch(fieldKey, isChecked) {
                let toggleContainer = $('<div style="display: flex; align-items: center;"></div>');
                
                // Create the toggle switch with a unique ID
                let switchId = 'toggle-' + fieldKey;
                let toggleSwitch = $('<label class="driggs-toggle-switch" for="' + switchId + '" style="position: relative; display: inline-block; width: 50px; height: 24px; cursor: pointer;"></label>');
                let checkbox = $('<input type="checkbox" id="' + switchId + '" style="position: absolute; opacity: 0; width: 0; height: 0;">');
                let slider = $('<span class="driggs-toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px;"></span>');
                let knob = $('<span class="driggs-toggle-knob" style="position: absolute; content: \'\'; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%;"></span>');
                
                // Set initial state
                if (isChecked) {
                    checkbox.prop('checked', true);
                    slider.css('background-color', '#2196F3');
                    knob.css('transform', 'translateX(26px)');
                }
                
                // Handle change event
                checkbox.on('change', function() {
                    let isNowChecked = $(this).is(':checked');
                    
                    if (isNowChecked) {
                        slider.css('background-color', '#2196F3');
                        knob.css('transform', 'translateX(26px)');
                    } else {
                        slider.css('background-color', '#ccc');
                        knob.css('transform', 'translateX(0)');
                    }
                    
                    // Update the field
                    updateField(fieldKey, isNowChecked ? 1 : 0);
                    currentData[fieldKey] = isNowChecked ? 1 : 0;
                    hasChanges = true;
                });
                
                slider.append(knob);
                toggleSwitch.append(checkbox).append(slider);
                toggleContainer.append(toggleSwitch);
                
                return toggleContainer;
            }
            
            function startInlineEdit(cell, currentValue, fieldKey, fieldType) {
                // Skip if already editing
                if (cell.hasClass('editing')) return;
                
                cell.addClass('editing');
                let originalText = cell.text();
                cell.empty();
                
                let input;
                if (fieldType === 'textarea') {
                    input = $('<textarea style="width: 100%; height: 60px; padding: 4px; border: 1px solid #ddd; border-radius: 3px; resize: vertical; font-family: inherit; font-size: 14px;"></textarea>');
                } else if (fieldType === 'email') {
                    input = $('<input type="email" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-family: inherit; font-size: 14px;">');
                } else if (fieldType === 'number') {
                    input = $('<input type="number" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-family: inherit; font-size: 14px;">');
                } else if (fieldType === 'password') {
                    input = $('<input type="password" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-family: inherit; font-size: 14px;">');
                } else {
                    input = $('<input type="text" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-family: inherit; font-size: 14px;">');
                }
                
                input.val(currentValue);
                cell.append(input);
                input.focus();
                
                // Save on blur or enter
                function saveEdit() {
                    let newValue = input.val();
                    cell.removeClass('editing');
                    
                    if (newValue !== currentValue) {
                        updateField(fieldKey, newValue);
                        cell.text(newValue);
                        currentData[fieldKey] = newValue;
                        hasChanges = true;
                    } else {
                        cell.text(originalText);
                    }
                }
                
                // Cancel on escape
                function cancelEdit() {
                    cell.removeClass('editing');
                    cell.text(originalText);
                }
                
                input.on('blur', saveEdit);
                input.on('keydown', function(e) {
                    if (e.key === 'Enter' && fieldType !== 'textarea') {
                        e.preventDefault();
                        saveEdit();
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        cancelEdit();
                    }
                });
            }
            
            function updateField(field, value) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_driggs_update_field',
                        nonce: '<?php echo wp_create_nonce('grove_driggs_nonce'); ?>',
                        field: field,
                        value: value
                    },
                    success: function(response) {
                        if (!response.success) {
                            alert('Error updating field: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error updating field');
                    }
                });
            }
            
            function copyShortcodeToClipboardFallback(shortcode, element) {
                // Create a temporary textarea element
                let temp = $('<textarea>');
                $('body').append(temp);
                temp.val(shortcode).select();
                
                try {
                    document.execCommand('copy');
                    console.log('Fallback clipboard copy successful');
                    
                    // Visual feedback
                    let originalBg = element.css('background-color');
                    element.css('background-color', '#4CAF50');
                    element.text('✓');
                    
                    setTimeout(function() {
                        element.css('background-color', originalBg);
                        element.text('R');
                    }, 1000);
                } catch (err) {
                    console.error('Fallback clipboard copy failed:', err);
                    alert('Failed to copy to clipboard. Shortcode: ' + shortcode);
                }
                
                temp.remove();
            }
            
            // Save All Changes button
            $('#save-all-btn').on('click', function() {
                if (hasChanges) {
                    alert('All changes have been saved automatically as you edited fields.');
                } else {
                    alert('No changes to save.');
                }
            });
            
            // Reset button (placeholder)
            $('#reset-btn').on('click', function() {
                if (confirm('Are you sure you want to reset all values to defaults? This cannot be undone.')) {
                    // This would need to be implemented based on your requirements
                    alert('Reset functionality would be implemented here.');
                }
            });
            
            // Select all checkbox
            $('#select-all').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('#table-body input[type="checkbox"]').prop('checked', isChecked);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Ensure a sitespren record exists for current site
     */
    private function ensure_sitespren_record() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        $current_site_url = get_site_url();
        
        // Check if record exists
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if (!$existing) {
            // Create a new record with defaults
            $wpdb->insert(
                $table_name,
                array(
                    'id' => wp_generate_uuid4(),
                    'sitespren_base' => $current_site_url,
                    'true_root_domain' => parse_url($current_site_url, PHP_URL_HOST),
                    'full_subdomain' => parse_url($current_site_url, PHP_URL_HOST),
                    'is_wp_site' => 1,
                    'wp_plugin_installed1' => 1,
                    'wp_plugin_connected2' => 1,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                )
            );
        }
    }
    
    /**
     * AJAX: Get driggs data for current site
     */
    public function grove_driggs_get_data() {
        check_ajax_referer('grove_driggs_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        $current_site_url = get_site_url();
        
        try {
            // Get the single record for this site - there should only be one row
            $result = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1", ARRAY_A);
            
            if ($wpdb->last_error) {
                wp_send_json_error('Database error: ' . $wpdb->last_error);
                return;
            }
            
            // If no record exists, create one with defaults
            if (!$result) {
                $this->ensure_sitespren_record();
                $result = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1", ARRAY_A);
            }
            
            // Convert boolean values to proper format
            $boolean_fields = [
                'wp_plugin_installed1', 'wp_plugin_connected2', 'is_wp_site', 'is_bulldozer', 
                'driggs_citations_done', 'is_competitor', 'is_external', 'is_internal', 
                'is_ppx', 'is_ms', 'is_wayback_rebuild', 'is_naked_wp_build', 
                'is_rnr', 'is_aff', 'is_other1', 'is_other2', 'is_flylocal'
            ];
            
            foreach ($boolean_fields as $field) {
                if (isset($result[$field])) {
                    $result[$field] = (int) $result[$field];
                }
            }
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error('Error loading driggs data: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Update driggs field
     */
    public function grove_driggs_update_field() {
        check_ajax_referer('grove_driggs_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $field = sanitize_text_field($_POST['field']);
        $value = $_POST['value'];
        
        // Validate field name - all editable fields from zen_sitespren table
        $allowed_fields = [
            'created_at', 'sitespren_base', 'true_root_domain', 'full_subdomain', 'webproperty_type',
            'fk_users_id', 'updated_at', 'wpuser1', 'wppass1', 'wp_plugin_installed1', 'wp_plugin_connected2',
            'fk_domreg_hostaccount', 'is_wp_site', 'wp_rest_app_pass', 'driggs_industry', 'driggs_city',
            'driggs_brand_name', 'driggs_site_type_purpose', 'driggs_email_1', 'driggs_address_full',
            'driggs_phone_1', 'driggs_special_note_for_ai_tool', 'driggs_logo_url', 'ns_full', 'ip_address', 'is_starred1',
            'icon_name', 'icon_color', 'is_bulldozer', 'driggs_phone1_platform_id', 'driggs_cgig_id',
            'driggs_revenue_goal', 'driggs_address_species_id', 'is_competitor', 'is_external', 'is_internal',
            'is_ppx', 'is_ms', 'is_wayback_rebuild', 'is_naked_wp_build', 'is_rnr', 'is_aff',
            'is_other1', 'is_other2', 'driggs_citations_done', 'is_flylocal'
        ];
        
        if (!in_array($field, $allowed_fields)) {
            wp_send_json_error('Invalid field name');
            return;
        }
        
        // Sanitize value based on field type
        $number_fields = ['driggs_phone1_platform_id', 'driggs_cgig_id', 'driggs_revenue_goal', 'driggs_address_species_id'];
        $boolean_fields = ['wp_plugin_installed1', 'wp_plugin_connected2', 'is_wp_site', 'is_bulldozer', 'driggs_citations_done', 'is_competitor', 'is_external', 'is_internal', 'is_ppx', 'is_ms', 'is_wayback_rebuild', 'is_naked_wp_build', 'is_rnr', 'is_aff', 'is_other1', 'is_other2', 'is_flylocal'];
        $email_fields = ['driggs_email_1'];
        $datetime_fields = ['created_at', 'updated_at'];
        
        if (in_array($field, $number_fields)) {
            $value = $value === '' ? NULL : intval($value);
        } elseif (in_array($field, $boolean_fields)) {
            $value = $value ? 1 : 0;
        } elseif (in_array($field, $email_fields)) {
            $value = $value === '' ? NULL : sanitize_email($value);
        } elseif (in_array($field, $datetime_fields)) {
            // Allow datetime values in MySQL format (YYYY-MM-DD HH:MM:SS)
            $value = $value === '' ? NULL : sanitize_text_field($value);
        } else {
            $value = $value === '' ? NULL : sanitize_textarea_field($value);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        try {
            // Update the single record (should only be one record per site)
            $result = $wpdb->update(
                $table_name,
                array($field => $value),
                array('wppma_id' => 1), // Update the first/only record
                null,
                array('%d')
            );
            
            if ($result === false) {
                wp_send_json_error('Database update failed: ' . $wpdb->last_error);
                return;
            }
            
            // Also update the updated_at timestamp
            $wpdb->update(
                $table_name,
                array('wppma_db_only_updated_at' => current_time('mysql')),
                array('wppma_id' => 1),
                array('%s'),
                array('%d')
            );
            
            wp_send_json_success('Field updated successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error updating field: ' . $e->getMessage());
        }
    }
    
    /**
     * Suppress admin notices on our pages
     */
    private function suppress_all_admin_notices() {
        // Remove all admin notices for clean interface
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}