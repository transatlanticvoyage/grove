<?php
/**
 * Grove Admin Class
 */

class Grove_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_grove_driggs_get_data', array($this, 'grove_driggs_get_data'));
        add_action('wp_ajax_grove_driggs_update_field', array($this, 'grove_driggs_update_field'));
        add_action('wp_ajax_grove_locations_get_data', array($this, 'grove_locations_get_data'));
        add_action('wp_ajax_grove_locations_update_field', array($this, 'grove_locations_update_field'));
        add_action('wp_ajax_grove_locations_delete', array($this, 'grove_locations_delete'));
        add_action('wp_ajax_grove_locations_create', array($this, 'grove_locations_create'));
        add_action('wp_ajax_grove_services_get_data', array($this, 'grove_services_get_data'));
        add_action('wp_ajax_grove_services_update_field', array($this, 'grove_services_update_field'));
        add_action('wp_ajax_grove_services_delete', array($this, 'grove_services_delete'));
        add_action('wp_ajax_grove_services_create', array($this, 'grove_services_create'));
        add_action('wp_ajax_grove_shortcode_update_mode', array($this, 'grove_shortcode_update_mode'));
        add_action('wp_ajax_grove_shortcode_test', array($this, 'grove_shortcode_test'));
    }
    
    public function add_admin_menu() {
        // Main menu page (parent only - no actual page)
        add_menu_page(
            'Grove Hub',
            'Grove Hub', 
            'manage_options',
            'grovehub',
            '', // No callback - makes it non-clickable
            'dashicons-palmtree',
            30
        );
        
        // Remove the auto-generated submenu link to the parent page
        global $submenu;
        if (isset($submenu['grovehub'])) {
            foreach ($submenu['grovehub'] as $key => $menu_item) {
                if ($menu_item[2] === 'grovehub') {
                    unset($submenu['grovehub'][$key]);
                    break;
                }
            }
        }
        
        // Submenu pages
        add_submenu_page(
            'grovehub',
            'Grove Driggs Manager',
            'grove_driggs_mar',
            'manage_options',
            'grove_driggs_mar',
            array($this, 'grove_driggs_mar_page')
        );
        
        add_submenu_page(
            'grovehub',
            'Grove Locations Manager', 
            'grove_locations_mar',
            'manage_options',
            'grove_locations_mar',
            array($this, 'grove_locations_mar_page')
        );
        
        add_submenu_page(
            'grovehub',
            'Grove Services Manager',
            'grove_services_mar', 
            'manage_options',
            'grove_services_mar',
            array($this, 'grove_services_mar_page')
        );
        
        add_submenu_page(
            'grovehub',
            'Shortcode Commander',
            'shortcodecommander',
            'manage_options',
            'shortcodecommander',
            array($this, 'shortcodecommander_page')
        );
    }
    
    public function grove_driggs_mar_page() {
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
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa; width: 300px;">shortcode 1</th>
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
                    
                    // Add new shortcode 1 column
                    let shortcodeTd = $('<td style="padding: 8px; border: 1px solid #ddd; position: relative;"></td>');
                    if (isSpecialBg) shortcodeTd.css('background-color', '#9b9b9b');
                    
                    // Create shortcode text
                    let shortcodeText = '[sitespren dbcol="' + field.key + '"]';
                    let shortcodeSpan = $('<span style="font-family: monospace; font-size: 12px; color: #333; margin-right: 10px;"></span>');
                    shortcodeSpan.text(shortcodeText);
                    
                    // Create copy button
                    let copyBtn = $('<button style="padding: 4px 8px; background: #f0f0f0; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; font-size: 12px; color: #333; transition: background-color 0.2s;">Copy</button>');
                    
                    // Add hover effect with yellow background
                    copyBtn.hover(
                        function() { $(this).css('background-color', '#ffeb3b'); },
                        function() { $(this).css('background-color', '#f0f0f0'); }
                    );
                    
                    // Add click handler for copy functionality
                    copyBtn.on('click', function() {
                        // Copy to clipboard
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(shortcodeText).then(function() {
                                // Success feedback
                                let originalText = copyBtn.text();
                                copyBtn.text('Copied!');
                                copyBtn.css('background-color', '#4caf50');
                                copyBtn.css('color', '#fff');
                                setTimeout(function() {
                                    copyBtn.text(originalText);
                                    copyBtn.css('background-color', '#f0f0f0');
                                    copyBtn.css('color', '#333');
                                }, 1500);
                            }).catch(function(err) {
                                console.error('Failed to copy:', err);
                                alert('Failed to copy shortcode');
                            });
                        } else {
                            // Fallback for older browsers
                            let tempInput = $('<input>');
                            $('body').append(tempInput);
                            tempInput.val(shortcodeText).select();
                            document.execCommand('copy');
                            tempInput.remove();
                            
                            // Success feedback
                            let originalText = copyBtn.text();
                            copyBtn.text('Copied!');
                            copyBtn.css('background-color', '#4caf50');
                            copyBtn.css('color', '#fff');
                            setTimeout(function() {
                                copyBtn.text(originalText);
                                copyBtn.css('background-color', '#f0f0f0');
                                copyBtn.css('color', '#333');
                            }, 1500);
                        }
                    });
                    
                    shortcodeTd.append(shortcodeSpan);
                    shortcodeTd.append(copyBtn);
                    tr.append(shortcodeTd);
                    
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
     * Grove Locations Manager Page
     */
    public function grove_locations_mar_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_locations';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        // Handle AJAX requests
        if (isset($_POST['action'])) {
            return;
        }
        
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🌳📍 Grove Locations Manager</h1>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button id="create-inline-btn" class="button button-primary">Create New (Inline)</button>
                        <button id="create-popup-btn" class="button button-secondary">Create New (Popup)</button>
                        <button id="delete-selected-btn" class="button" style="background: #dc3545; color: white; border-color: #dc3545;">Delete Selected</button>
                    </div>
                </div>
                
                <!-- Pagination and Search Controls -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <!-- Items per page -->
                        <div style="display: flex; gap: 0; border: 1px solid #ccc;">
                            <button class="per-page-btn" data-value="10" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px;">10</button>
                            <button class="per-page-btn" data-value="20" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">20</button>
                            <button class="per-page-btn" data-value="50" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">50</button>
                            <button class="per-page-btn active" data-value="100" style="border: none; padding: 10px 15px; background: #0073aa; color: white; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">100</button>
                            <button class="per-page-btn" data-value="200" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">200</button>
                            <button class="per-page-btn" data-value="500" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">500</button>
                            <button class="per-page-btn" data-value="all" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">All</button>
                        </div>
                        
                        <!-- Page navigation -->
                        <div id="page-nav" style="display: flex; gap: 0; border: 1px solid #ccc;">
                            <!-- Dynamic page buttons will go here -->
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <div style="position: relative;">
                        <input type="text" id="search-box" placeholder="Search locations..." style="padding: 8px 40px 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; font-size: 14px;">
                        <button id="clear-search" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #ffeb3b; border: none; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 3px; cursor: pointer;">CL</button>
                    </div>
                </div>
            </div>
            
            <!-- Main Table -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table id="locations-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: 50px;">
                                    <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
                                </th>
                                <th data-sort="location_id" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">location_id</th>
                                <th data-sort="location_name" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">location_name</th>
                                <th data-sort="location_placard" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">location_placard</th>
                                <th data-sort="location_moniker" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">location_moniker</th>
                                <th data-sort="location_sobriquet" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">location_sobriquet</th>
                                <th data-sort="street" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">street</th>
                                <th data-sort="city" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">city</th>
                                <th data-sort="state_code" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">state_code</th>
                                <th data-sort="zip_code" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">zip_code</th>
                                <th data-sort="country" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">country</th>
                                <th data-sort="is_pinned_location" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">is_pinned_location</th>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; background: #f8f9fa;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Create Popup Modal -->
        <div id="create-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 600px; max-height: 80vh; overflow-y: auto;">
                <h2 style="margin-top: 0;">Create New Location</h2>
                <form id="create-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Location Name:</label>
                            <input type="text" name="location_name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Location Placard:</label>
                            <input type="text" name="location_placard" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Street:</label>
                            <input type="text" name="street" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">City:</label>
                            <input type="text" name="city" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">State Code:</label>
                            <input type="text" name="state_code" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Zip Code:</label>
                            <input type="text" name="zip_code" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" id="cancel-btn" class="button button-secondary" style="margin-right: 10px;">Cancel</button>
                        <button type="submit" class="button button-primary">Create Location</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            let currentData = [];
            let filteredData = [];
            
            // Load initial data
            loadLocationsData();
            
            function loadLocationsData() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_locations_get_data',
                        nonce: '<?php echo wp_create_nonce('grove_locations_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            currentData = response.data;
                            filteredData = currentData;
                            displayData();
                        } else {
                            alert('Error loading locations data: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error loading locations data');
                    }
                });
            }
            
            function displayData() {
                let tbody = $('#table-body');
                tbody.empty();
                
                filteredData.forEach(function(location) {
                    let tr = $('<tr></tr>');
                    
                    // Checkbox
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><input type="checkbox" class="row-select" data-id="' + location.location_id + '"></td>');
                    
                    // Data columns
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd;">' + (location.location_id || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="location_name" data-id="' + location.location_id + '">' + (location.location_name || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="location_placard" data-id="' + location.location_id + '">' + (location.location_placard || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="location_moniker" data-id="' + location.location_id + '">' + (location.location_moniker || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="location_sobriquet" data-id="' + location.location_id + '">' + (location.location_sobriquet || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="street" data-id="' + location.location_id + '">' + (location.street || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="city" data-id="' + location.location_id + '">' + (location.city || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="state_code" data-id="' + location.location_id + '">' + (location.state_code || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="zip_code" data-id="' + location.location_id + '">' + (location.zip_code || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="country" data-id="' + location.location_id + '">' + (location.country || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;">' + (location.is_pinned_location ? 'Yes' : 'No') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd;"><button class="button button-small delete-btn" data-id="' + location.location_id + '">Delete</button></td>');
                    
                    tbody.append(tr);
                });
                
                // Inline editing
                $('[data-field]').click(function() {
                    let cell = $(this);
                    let field = cell.data('field');
                    let id = cell.data('id');
                    let currentValue = cell.text();
                    
                    let input = $('<input type="text" style="width: 100%; padding: 4px;">');
                    input.val(currentValue);
                    cell.html(input);
                    input.focus();
                    
                    input.blur(function() {
                        let newValue = input.val();
                        updateField(id, field, newValue, cell);
                    });
                    
                    input.keydown(function(e) {
                        if (e.key === 'Enter') {
                            input.blur();
                        } else if (e.key === 'Escape') {
                            cell.text(currentValue);
                        }
                    });
                });
            }
            
            function updateField(id, field, value, cell) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_locations_update_field',
                        nonce: '<?php echo wp_create_nonce('grove_locations_nonce'); ?>',
                        id: id,
                        field: field,
                        value: value
                    },
                    success: function(response) {
                        if (response.success) {
                            cell.text(value);
                        } else {
                            alert('Error updating field: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error updating field');
                    }
                });
            }
            
            // Create popup modal
            $('#create-popup-btn').click(function() {
                $('#create-modal').show();
            });
            
            $('#cancel-btn, #create-modal').click(function(e) {
                if (e.target === this) {
                    $('#create-modal').hide();
                    $('#create-form')[0].reset();
                }
            });
            
            // Create form submission
            $('#create-form').submit(function(e) {
                e.preventDefault();
                
                let formData = {
                    action: 'grove_locations_create',
                    nonce: '<?php echo wp_create_nonce('grove_locations_nonce'); ?>'
                };
                
                $(this).serializeArray().forEach(function(field) {
                    formData[field.name] = field.value;
                });
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#create-modal').hide();
                            $('#create-form')[0].reset();
                            loadLocationsData();
                        } else {
                            alert('Error creating location: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error creating location');
                    }
                });
            });
            
            // Delete functionality
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                if (confirm('Are you sure you want to delete this location?')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_locations_delete',
                            nonce: '<?php echo wp_create_nonce('grove_locations_nonce'); ?>',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                loadLocationsData();
                            } else {
                                alert('Error deleting location: ' + response.data);
                            }
                        }
                    });
                }
            });
            
            // Search functionality
            $('#search-box').on('input', function() {
                let searchTerm = $(this).val().toLowerCase();
                if (searchTerm === '') {
                    filteredData = currentData;
                } else {
                    filteredData = currentData.filter(function(location) {
                        return Object.values(location).some(function(value) {
                            return String(value).toLowerCase().includes(searchTerm);
                        });
                    });
                }
                displayData();
            });
            
            $('#clear-search').click(function() {
                $('#search-box').val('');
                filteredData = currentData;
                displayData();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Grove Services Manager Page
     */
    public function grove_services_mar_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_services';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🌳🔧 Grove Services Manager</h1>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button id="create-popup-btn" class="button button-primary">Create New Service</button>
                        <button id="delete-selected-btn" class="button" style="background: #dc3545; color: white; border-color: #dc3545;">Delete Selected</button>
                    </div>
                </div>
                
                <!-- Search -->
                <div style="display: flex; justify-content: flex-end; align-items: center;">
                    <div style="position: relative;">
                        <input type="text" id="search-box" placeholder="Search services..." style="padding: 8px 40px 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; font-size: 14px;">
                        <button id="clear-search" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #ffeb3b; border: none; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 3px; cursor: pointer;">CL</button>
                    </div>
                </div>
            </div>
            
            <!-- Main Table -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table id="services-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: 50px;">
                                    <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
                                </th>
                                <th data-sort="service_id" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">service_id</th>
                                <th data-sort="service_name" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">service_name</th>
                                <th data-sort="service_placard" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">service_placard</th>
                                <th data-sort="service_moniker" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">service_moniker</th>
                                <th data-sort="description1_short" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">description1_short</th>
                                <th data-sort="description1_long" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">description1_long</th>
                                <th data-sort="is_pinned_service" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">is_pinned_service</th>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; background: #f8f9fa;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Create Popup Modal -->
        <div id="create-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 700px; max-height: 80vh; overflow-y: auto;">
                <h2 style="margin-top: 0;">Create New Service</h2>
                <form id="create-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service Name:</label>
                            <input type="text" name="service_name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service Placard:</label>
                            <input type="text" name="service_placard" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service Moniker:</label>
                            <input type="text" name="service_moniker" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service Sobriquet:</label>
                            <input type="text" name="service_sobriquet" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Short Description:</label>
                        <textarea name="description1_short" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Long Description:</label>
                        <textarea name="description1_long" rows="5" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" id="cancel-btn" class="button button-secondary" style="margin-right: 10px;">Cancel</button>
                        <button type="submit" class="button button-primary">Create Service</button>
                    </div>
                </form>
            </div>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            let currentData = [];
            let filteredData = [];
            
            // Load initial data
            loadServicesData();
            
            function loadServicesData() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_services_get_data',
                        nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            currentData = response.data;
                            filteredData = currentData;
                            displayData();
                        } else {
                            alert('Error loading services data: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error loading services data');
                    }
                });
            }
            
            function displayData() {
                let tbody = $('#table-body');
                tbody.empty();
                
                filteredData.forEach(function(service) {
                    let tr = $('<tr></tr>');
                    
                    // Checkbox
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><input type="checkbox" class="row-select" data-id="' + service.service_id + '"></td>');
                    
                    // Data columns
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd;">' + (service.service_id || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="service_name" data-id="' + service.service_id + '">' + (service.service_name || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="service_placard" data-id="' + service.service_id + '">' + (service.service_placard || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="service_moniker" data-id="' + service.service_id + '">' + (service.service_moniker || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="description1_short" data-id="' + service.service_id + '">' + (service.description1_short || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; cursor: pointer;" data-field="description1_long" data-id="' + service.service_id + '">' + (service.description1_long || '') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;">' + (service.is_pinned_service ? 'Yes' : 'No') + '</td>');
                    tr.append('<td style="padding: 8px; border: 1px solid #ddd;"><button class="button button-small delete-btn" data-id="' + service.service_id + '">Delete</button></td>');
                    
                    tbody.append(tr);
                });
                
                // Inline editing
                $('[data-field]').click(function() {
                    let cell = $(this);
                    let field = cell.data('field');
                    let id = cell.data('id');
                    let currentValue = cell.text();
                    
                    let input;
                    if (field.includes('description')) {
                        input = $('<textarea style="width: 100%; height: 60px; padding: 4px; resize: vertical;"></textarea>');
                    } else {
                        input = $('<input type="text" style="width: 100%; padding: 4px;">');
                    }
                    
                    input.val(currentValue);
                    cell.html(input);
                    input.focus();
                    
                    input.blur(function() {
                        let newValue = input.val();
                        updateField(id, field, newValue, cell);
                    });
                    
                    input.keydown(function(e) {
                        if (e.key === 'Enter' && !field.includes('description')) {
                            input.blur();
                        } else if (e.key === 'Escape') {
                            cell.text(currentValue);
                        }
                    });
                });
            }
            
            function updateField(id, field, value, cell) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_services_update_field',
                        nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                        id: id,
                        field: field,
                        value: value
                    },
                    success: function(response) {
                        if (response.success) {
                            cell.text(value);
                        } else {
                            alert('Error updating field: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error updating field');
                    }
                });
            }
            
            // Modal functionality
            $('#create-popup-btn').click(function() {
                $('#create-modal').show();
            });
            
            $('#cancel-btn, #create-modal').click(function(e) {
                if (e.target === this) {
                    $('#create-modal').hide();
                    $('#create-form')[0].reset();
                }
            });
            
            // Create form submission
            $('#create-form').submit(function(e) {
                e.preventDefault();
                
                let formData = {
                    action: 'grove_services_create',
                    nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>'
                };
                
                $(this).serializeArray().forEach(function(field) {
                    formData[field.name] = field.value;
                });
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#create-modal').hide();
                            $('#create-form')[0].reset();
                            loadServicesData();
                        } else {
                            alert('Error creating service: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error creating service');
                    }
                });
            });
            
            // Delete functionality
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                if (confirm('Are you sure you want to delete this service?')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_services_delete',
                            nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                loadServicesData();
                            } else {
                                alert('Error deleting service: ' + response.data);
                            }
                        }
                    });
                }
            });
            
            // Search functionality
            $('#search-box').on('input', function() {
                let searchTerm = $(this).val().toLowerCase();
                if (searchTerm === '') {
                    filteredData = currentData;
                } else {
                    filteredData = currentData.filter(function(service) {
                        return Object.values(service).some(function(value) {
                            return String(value).toLowerCase().includes(searchTerm);
                        });
                    });
                }
                displayData();
            });
            
            $('#clear-search').click(function() {
                $('#search-box').val('');
                filteredData = currentData;
                displayData();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Shortcode Commander Page
     */
    public function shortcodecommander_page() {
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        $current_mode = get_option('grove_shortcode_mode', 'automatic');
        $snefuruplin_active = is_plugin_active('snefuruplin/snefuruplin.php');
        
        // Get shortcode status
        $grove_shortcodes = new Grove_Zen_Shortcodes();
        $grove_controlling = $grove_shortcodes->is_grove_controlling_shortcodes();
        
        // Get database stats
        global $wpdb;
        $services_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}zen_services");
        $locations_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}zen_locations");
        $sitespren_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}zen_sitespren");
        
        // Check which shortcodes are registered
        $shortcodes_to_check = ['zen_services', 'zen_locations', 'zen_service', 'zen_location', 'sitespren', 'zen_pinned_services', 'zen_pinned_locations', 'zen_service_image', 'zen_location_image'];
        $registered_shortcodes = 0;
        foreach ($shortcodes_to_check as $shortcode) {
            if (shortcode_exists($shortcode)) {
                $registered_shortcodes++;
            }
        }
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🌳📡 Shortcode Commander</h1>
                
                <!-- Status Dashboard -->
                <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
                    <h2 style="margin-top: 0;">System Status</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <?php if ($grove_controlling): ?>
                                    <span style="color: #46b450; font-size: 18px; margin-right: 10px;">🟢</span>
                                    <strong>Shortcode Status: ACTIVE</strong>
                                    <span style="background: #46b450; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 10px;">GROVE CONTROLLING</span>
                                <?php else: ?>
                                    <span style="color: #dc3232; font-size: 18px; margin-right: 10px;">🔴</span>
                                    <strong>Shortcode Status: INACTIVE</strong>
                                    <?php if ($snefuruplin_active): ?>
                                        <span style="background: #0073aa; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 10px;">SNEFURUPLIN ACTIVE</span>
                                    <?php else: ?>
                                        <span style="background: #dc3232; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 10px;">DISABLED</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <strong>Registered Shortcodes:</strong> <?php echo $registered_shortcodes; ?>/<?php echo count($shortcodes_to_check); ?> found
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <strong>Current Mode:</strong> <?php echo ucwords(str_replace('_', ' ', $current_mode)); ?>
                            </div>
                        </div>
                        
                        <div>
                            <div style="margin-bottom: 10px;">
                                <strong>Snefuruplin Status:</strong> <?php echo $snefuruplin_active ? '<span style="color: #46b450;">✅ Active</span>' : '<span style="color: #666;">❌ Not detected</span>'; ?>
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <strong>Database Tables:</strong><br>
                                <small>
                                    • zen_services: <?php echo $services_count ? '<span style="color: #46b450;">✅ ' . $services_count . ' records</span>' : '<span style="color: #dc3232;">❌ No data</span>'; ?><br>
                                    • zen_locations: <?php echo $locations_count ? '<span style="color: #46b450;">✅ ' . $locations_count . ' records</span>' : '<span style="color: #dc3232;">❌ No data</span>'; ?><br>
                                    • zen_sitespren: <?php echo $sitespren_count ? '<span style="color: #46b450;">✅ ' . $sitespren_count . ' records</span>' : '<span style="color: #dc3232;">❌ No data</span>'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Control Panel -->
                <div style="background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
                    <h2 style="margin-top: 0;">Shortcode Control</h2>
                    
                    <form id="shortcode-mode-form">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: bold; margin-bottom: 10px;">Control Mode:</label>
                            
                            <div style="margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="radio" name="grove_shortcode_mode" value="automatic" <?php checked($current_mode, 'automatic'); ?> style="margin-right: 10px;">
                                    <div>
                                        <strong>Automatic (Recommended)</strong><br>
                                        <small style="color: #666;">Take control only when Snefuruplin is missing. Seamless failover protection.</small>
                                    </div>
                                </label>
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="radio" name="grove_shortcode_mode" value="force_active" <?php checked($current_mode, 'force_active'); ?> style="margin-right: 10px;">
                                    <div>
                                        <strong>Force Active</strong><br>
                                        <small style="color: #666;">Always control shortcodes, even if Snefuruplin is active. Override mode.</small>
                                    </div>
                                </label>
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="radio" name="grove_shortcode_mode" value="disabled" <?php checked($current_mode, 'disabled'); ?> style="margin-right: 10px;">
                                    <div>
                                        <strong>Disabled</strong><br>
                                        <small style="color: #666;">Never register shortcodes. Completely disable Grove shortcode system.</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="button button-primary">Save Settings</button>
                        <button type="button" id="test-shortcode-btn" class="button button-secondary" style="margin-left: 10px;">Test Shortcodes</button>
                    </form>
                </div>
                
                <!-- Test Results -->
                <div id="test-results" style="display: none; background: white; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
                    <h2 style="margin-top: 0;">Shortcode Test Results</h2>
                    <div id="test-output"></div>
                </div>
                
                <!-- Information Panel -->
                <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; border-radius: 5px;">
                    <h3 style="margin-top: 0;">How It Works</h3>
                    <ul>
                        <li><strong>Automatic Mode:</strong> Grove monitors for Snefuruplin and only takes control when it's not present. This provides seamless backup protection without conflicts.</li>
                        <li><strong>Force Active Mode:</strong> Grove always controls shortcodes, overriding Snefuruplin. Use for testing or when you want Grove to be the primary shortcode provider.</li>
                        <li><strong>Disabled Mode:</strong> Grove never registers shortcodes. Use when you want Snefuruplin to handle all shortcodes exclusively.</li>
                    </ul>
                    
                    <h3>Available Shortcodes</h3>
                    <p><small>When Grove is controlling shortcodes, these are available:</small></p>
                    <code style="background: white; padding: 10px; display: block; margin: 10px 0;">
                        [zen_services], [zen_service], [zen_service_image]<br>
                        [zen_locations], [zen_location], [zen_location_image]<br>
                        [zen_pinned_services], [zen_pinned_locations]<br>
                        [sitespren wppma_id="1" field="field_name"]
                    </code>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#shortcode-mode-form').on('submit', function(e) {
                e.preventDefault();
                
                var mode = $('input[name="grove_shortcode_mode"]:checked').val();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_shortcode_update_mode',
                        nonce: '<?php echo wp_create_nonce('grove_shortcode_nonce'); ?>',
                        mode: mode
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Settings saved successfully! Please refresh the page to see status updates.');
                            location.reload();
                        } else {
                            alert('Error saving settings: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error saving settings');
                    }
                });
            });
            
            $('#test-shortcode-btn').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_shortcode_test',
                        nonce: '<?php echo wp_create_nonce('grove_shortcode_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#test-output').html(response.data);
                            $('#test-results').show();
                        } else {
                            $('#test-output').html('<div style="color: #dc3232;">Test failed: ' + response.data + '</div>');
                            $('#test-results').show();
                        }
                    },
                    error: function() {
                        $('#test-output').html('<div style="color: #dc3232;">Test request failed</div>');
                        $('#test-results').show();
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX: Get locations data
     */
    public function grove_locations_get_data() {
        check_ajax_referer('grove_locations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_locations';
        
        try {
            $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY location_id", ARRAY_A);
            
            if ($wpdb->last_error) {
                wp_send_json_error('Database error: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success($results);
        } catch (Exception $e) {
            wp_send_json_error('Error loading locations data: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Update locations field
     */
    public function grove_locations_update_field() {
        check_ajax_referer('grove_locations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $id = intval($_POST['id']);
        $field = sanitize_text_field($_POST['field']);
        $value = sanitize_text_field($_POST['value']);
        
        $allowed_fields = ['location_name', 'location_placard', 'location_moniker', 'location_sobriquet', 'street', 'city', 'state_code', 'zip_code', 'country'];
        
        if (!in_array($field, $allowed_fields)) {
            wp_send_json_error('Invalid field name');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_locations';
        
        try {
            $result = $wpdb->update(
                $table_name,
                array($field => $value),
                array('location_id' => $id),
                array('%s'),
                array('%d')
            );
            
            if ($result === false) {
                wp_send_json_error('Database update failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Field updated successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error updating field: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Create new location
     */
    public function grove_locations_create() {
        check_ajax_referer('grove_locations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_locations';
        
        $data = array(
            'location_name' => sanitize_text_field($_POST['location_name']),
            'location_placard' => sanitize_text_field($_POST['location_placard']),
            'street' => sanitize_text_field($_POST['street']),
            'city' => sanitize_text_field($_POST['city']),
            'state_code' => sanitize_text_field($_POST['state_code']),
            'zip_code' => sanitize_text_field($_POST['zip_code']),
            'country' => sanitize_text_field($_POST['country'] ?: 'USA'),
            'is_pinned_location' => 0,
            'position_in_custom_order' => 0
        );
        
        try {
            $result = $wpdb->insert($table_name, $data);
            
            if ($result === false) {
                wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Location created successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error creating location: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Delete location
     */
    public function grove_locations_delete() {
        check_ajax_referer('grove_locations_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $id = intval($_POST['id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_locations';
        
        try {
            $result = $wpdb->delete(
                $table_name,
                array('location_id' => $id),
                array('%d')
            );
            
            if ($result === false) {
                wp_send_json_error('Database delete failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Location deleted successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error deleting location: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Get services data
     */
    public function grove_services_get_data() {
        check_ajax_referer('grove_services_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_services';
        
        try {
            $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY service_id", ARRAY_A);
            
            if ($wpdb->last_error) {
                wp_send_json_error('Database error: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success($results);
        } catch (Exception $e) {
            wp_send_json_error('Error loading services data: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Update services field
     */
    public function grove_services_update_field() {
        check_ajax_referer('grove_services_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $id = intval($_POST['id']);
        $field = sanitize_text_field($_POST['field']);
        $value = sanitize_textarea_field($_POST['value']);
        
        $allowed_fields = ['service_name', 'service_placard', 'service_moniker', 'service_sobriquet', 'description1_short', 'description1_long'];
        
        if (!in_array($field, $allowed_fields)) {
            wp_send_json_error('Invalid field name');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_services';
        
        try {
            $result = $wpdb->update(
                $table_name,
                array($field => $value),
                array('service_id' => $id),
                array('%s'),
                array('%d')
            );
            
            if ($result === false) {
                wp_send_json_error('Database update failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Field updated successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error updating field: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Create new service
     */
    public function grove_services_create() {
        check_ajax_referer('grove_services_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_services';
        
        $data = array(
            'service_name' => sanitize_text_field($_POST['service_name']),
            'service_placard' => sanitize_text_field($_POST['service_placard']),
            'service_moniker' => sanitize_text_field($_POST['service_moniker']),
            'service_sobriquet' => sanitize_text_field($_POST['service_sobriquet']),
            'description1_short' => sanitize_textarea_field($_POST['description1_short']),
            'description1_long' => sanitize_textarea_field($_POST['description1_long']),
            'is_pinned_service' => 0,
            'position_in_custom_order' => 0
        );
        
        try {
            $result = $wpdb->insert($table_name, $data);
            
            if ($result === false) {
                wp_send_json_error('Database insert failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Service created successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error creating service: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Delete service
     */
    public function grove_services_delete() {
        check_ajax_referer('grove_services_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $id = intval($_POST['id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_services';
        
        try {
            $result = $wpdb->delete(
                $table_name,
                array('service_id' => $id),
                array('%d')
            );
            
            if ($result === false) {
                wp_send_json_error('Database delete failed: ' . $wpdb->last_error);
                return;
            }
            
            wp_send_json_success('Service deleted successfully');
        } catch (Exception $e) {
            wp_send_json_error('Error deleting service: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Update shortcode mode
     */
    public function grove_shortcode_update_mode() {
        check_ajax_referer('grove_shortcode_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $mode = sanitize_text_field($_POST['mode']);
        $allowed_modes = ['automatic', 'force_active', 'disabled'];
        
        if (!in_array($mode, $allowed_modes)) {
            wp_send_json_error('Invalid mode');
            return;
        }
        
        update_option('grove_shortcode_mode', $mode);
        wp_send_json_success('Mode updated to: ' . $mode);
    }
    
    /**
     * AJAX: Test shortcode functionality
     */
    public function grove_shortcode_test() {
        check_ajax_referer('grove_shortcode_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $output = '<h4>Shortcode Test Results</h4>';
        
        // Test each shortcode
        $shortcodes_to_test = [
            'zen_services' => '[zen_services limit="1"]',
            'zen_locations' => '[zen_locations limit="1"]',
            'sitespren' => '[sitespren wppma_id="1" field="driggs_brand_name"]'
        ];
        
        foreach ($shortcodes_to_test as $name => $shortcode) {
            $exists = shortcode_exists($name);
            $result = '';
            
            if ($exists) {
                $result = do_shortcode($shortcode);
                $status = '<span style="color: #46b450;">✅ Active</span>';
                if (empty(trim($result))) {
                    $status .= ' <small>(No data returned)</small>';
                }
            } else {
                $status = '<span style="color: #dc3232;">❌ Not registered</span>';
            }
            
            $output .= '<div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 3px;">';
            $output .= '<strong>' . $shortcode . '</strong> ' . $status;
            
            if ($exists && !empty(trim($result))) {
                $output .= '<div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-radius: 3px; font-size: 12px;">'; 
                $output .= '<strong>Output:</strong><br>' . wp_kses_post($result);
                $output .= '</div>';
            }
            $output .= '</div>';
        }
        
        wp_send_json_success($output);
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