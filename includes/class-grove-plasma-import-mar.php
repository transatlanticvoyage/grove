<?php

/**
 * Grove Plasma Import Mar Page Class
 * Handles the Plasma Import Mar admin page functionality
 */
class Grove_Plasma_Import_Mar {
    
    public function __construct() {
        // Constructor can be used for initialization if needed
    }
    
    /**
     * Display the Plasma Import Mar admin page
     */
    public function plasma_import_mar_page() {
        global $wpdb;
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <img src="<?php echo plugin_dir_url(__FILE__) . '../grove-shenzi-asset-mirror/grove-logo-1.png'; ?>" alt="Grove Logo" style="height: 40px; width: auto;">
                        Plasma Import Mar
                    </h1>
                </div>
                
                <!-- JSON Upload Section -->
                <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">Import JSON File</h3>
                    <form id="plasma-upload-form" method="post" enctype="multipart/form-data" style="margin-bottom: 15px;">
                        <?php wp_nonce_field('plasma_import_nonce', 'plasma_import_nonce'); ?>
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <input type="file" id="plasma-json-file" name="plasma_json_file" accept=".json" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <button type="button" id="import-json-btn" class="button button-primary">Import JSON File</button>
                        </div>
                    </form>
                    <div id="upload-status" style="margin-top: 10px;"></div>
                </div>

                <!-- Data Preview Section (Hidden initially) -->
                <div id="data-preview-section" style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px; display: none;">
                    <h3 style="margin: 0 0 15px 0;">Preview Imported Data</h3>
                    <div style="margin-bottom: 15px;">
                        <button id="create-pages-btn" class="button button-primary">f47 - Create Selected Pages/Posts in WP</button>
                    </div>
                    
                    <div id="data-table-container">
                        <!-- Table will be populated via JavaScript -->
                    </div>
                </div>

                <!-- Driggs Data Preview Section (Hidden initially) -->
                <div id="driggs-preview-section" style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-top: 20px; display: none;">
                    <h3 style="margin: 0 0 15px 0;">Preview Imported Data - driggs data</h3>
                    
                    <div id="driggs-table-container">
                        <!-- Driggs data table will be populated via JavaScript -->
                    </div>
                </div>
                
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            let importedData = null;

            // Handle file import
            $('#import-json-btn').click(function() {
                const fileInput = $('#plasma-json-file')[0];
                if (!fileInput.files[0]) {
                    alert('Please select a JSON file first.');
                    return;
                }

                const file = fileInput.files[0];
                if (!file.name.endsWith('.json')) {
                    alert('Please select a valid JSON file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const jsonData = JSON.parse(e.target.result);
                        
                        if (!jsonData.pages || !Array.isArray(jsonData.pages)) {
                            alert('Invalid JSON format. Expected "pages" array.');
                            return;
                        }

                        importedData = jsonData;
                        displayDataTable(jsonData.pages);
                        
                        // Handle driggs data if present
                        if (jsonData.driggs_data) {
                            displayDriggsDataTable(jsonData.driggs_data);
                            $('#driggs-preview-section').show();
                        }
                        
                        $('#upload-status').html('<span style="color: green;">✓ JSON file imported successfully! ' + jsonData.pages.length + ' pages found.</span>');
                        $('#data-preview-section').show();

                    } catch (error) {
                        alert('Error parsing JSON file: ' + error.message);
                        $('#upload-status').html('<span style="color: red;">✗ Error parsing JSON file.</span>');
                    }
                };
                reader.readAsText(file);
            });

            // Display data in table
            function displayDataTable(pages) {
                if (!pages || pages.length === 0) {
                    $('#data-table-container').html('<p>No pages found in the JSON file.</p>');
                    return;
                }

                // Get all unique column names from all pages
                const allColumns = new Set();
                pages.forEach(page => {
                    Object.keys(page).forEach(key => allColumns.add(key));
                });
                
                const columns = Array.from(allColumns);

                // Build table
                let tableHTML = '<table class="widefat striped" style="margin-top: 15px;">';
                
                // Header
                tableHTML += '<thead><tr>';
                tableHTML += '<th style="width: 40px;"><input type="checkbox" id="select-all-pages"></th>';
                columns.forEach(col => {
                    tableHTML += '<th style="padding: 8px; font-weight: bold;">' + escapeHtml(col) + '</th>';
                });
                tableHTML += '</tr></thead>';
                
                // Body
                tableHTML += '<tbody>';
                pages.forEach((page, index) => {
                    tableHTML += '<tr>';
                    tableHTML += '<td style="padding: 8px;"><input type="checkbox" class="page-checkbox" data-index="' + index + '"></td>';
                    columns.forEach(col => {
                        let value = page[col] || '';
                        
                        // Truncate long content
                        if (typeof value === 'string' && value.length > 20) {
                            value = value.substring(0, 20) + '...';
                        } else if (typeof value === 'object') {
                            value = JSON.stringify(value).substring(0, 20) + '...';
                        }
                        
                        tableHTML += '<td style="padding: 8px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + escapeHtml(page[col] || '') + '">';
                        tableHTML += escapeHtml(String(value));
                        tableHTML += '</td>';
                    });
                    tableHTML += '</tr>';
                });
                tableHTML += '</tbody></table>';

                $('#data-table-container').html(tableHTML);

                // Handle select all checkbox
                $('#select-all-pages').change(function() {
                    $('.page-checkbox').prop('checked', this.checked);
                });
            }

            // Display driggs data in table
            function displayDriggsDataTable(driggsData) {
                if (!driggsData || Object.keys(driggsData).length === 0) {
                    $('#driggs-table-container').html('<p>No driggs data found in the JSON file.</p>');
                    return;
                }

                // Define the sitespren fields in order
                const sitesprenFields = [
                    'sitespren_base',
                    'driggs_brand_name',
                    'driggs_revenue_goal',
                    'driggs_phone_country_code',
                    'driggs_phone1_platform_id',
                    'driggs_phone_1',
                    'driggs_address_species_id',
                    'driggs_address_species_note',
                    'driggs_address_full',
                    'driggs_street_1',
                    'driggs_street_2',
                    'driggs_city',
                    'driggs_state_code',
                    'driggs_zip',
                    'driggs_state_full',
                    'driggs_country',
                    'driggs_gmaps_widget_location_1',
                    'driggs_cgig_id',
                    'driggs_citations_done',
                    'driggs_social_profiles_done',
                    'driggs_industry',
                    'driggs_keywords',
                    'driggs_category',
                    'driggs_site_type_purpose',
                    'driggs_email_1',
                    'driggs_hours',
                    'driggs_owner_name',
                    'driggs_short_descr',
                    'driggs_long_descr',
                    'driggs_year_opened',
                    'driggs_employees_qty',
                    'driggs_payment_methods',
                    'driggs_special_note_for_ai_tool',
                    'driggs_social_media_links'
                ];

                // Build table
                let tableHTML = '<table class="widefat striped" style="margin-top: 15px;">';
                
                // Header
                tableHTML += '<thead><tr>';
                tableHTML += '<th style="width: 40px;"><input type="checkbox" id="select-all-driggs"></th>';
                tableHTML += '<th style="padding: 8px; font-weight: bold;">Actual Field</th>';
                tableHTML += '<th style="padding: 8px; font-weight: bold;">Your Values</th>';
                tableHTML += '<th style="padding: 8px; font-weight: bold; text-align: center;">-</th>';
                tableHTML += '</tr></thead>';
                
                // Body
                tableHTML += '<tbody>';
                sitesprenFields.forEach((field, index) => {
                    let value = driggsData[field] || '';
                    
                    // Truncate long content
                    let displayValue = value;
                    if (typeof value === 'string' && value.length > 50) {
                        displayValue = value.substring(0, 50) + '...';
                    } else if (typeof value === 'object') {
                        displayValue = JSON.stringify(value).substring(0, 50) + '...';
                    }
                    
                    tableHTML += '<tr>';
                    tableHTML += '<td style="padding: 8px;"><input type="checkbox" class="driggs-checkbox" data-field="' + field + '"></td>';
                    tableHTML += '<td style="padding: 8px; font-weight: bold; font-size: 14px; text-transform: lowercase;">' + escapeHtml(field) + '</td>';
                    tableHTML += '<td style="padding: 8px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + escapeHtml(String(value)) + '">';
                    tableHTML += escapeHtml(String(displayValue));
                    tableHTML += '</td>';
                    tableHTML += '<td style="padding: 8px; text-align: center;">-</td>';
                    tableHTML += '</tr>';
                });
                tableHTML += '</tbody></table>';

                $('#driggs-table-container').html(tableHTML);

                // Handle select all checkbox for driggs data
                $('#select-all-driggs').change(function() {
                    $('.driggs-checkbox').prop('checked', this.checked);
                });
            }

            // Handle create pages button
            $('#create-pages-btn').click(function() {
                const selectedIndexes = [];
                $('.page-checkbox:checked').each(function() {
                    selectedIndexes.push(parseInt($(this).data('index')));
                });

                if (selectedIndexes.length === 0) {
                    alert('Please select at least one page to create.');
                    return;
                }

                if (!confirm('Create ' + selectedIndexes.length + ' pages/posts in WordPress?')) {
                    return;
                }

                // TODO: Implement AJAX call to create WordPress pages
                alert('Page creation functionality will be implemented next. Selected: ' + selectedIndexes.length + ' pages.');
            });

            // Utility function to escape HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
        </script>

        <style>
        .widefat th, .widefat td {
            border: 1px solid #ddd;
        }
        .widefat th {
            background: #f9f9f9;
        }
        .widefat tr:nth-child(even) {
            background: #f9f9f9;
        }
        .widefat tr:hover {
            background: #f0f8ff;
        }
        </style>
        <?php
    }
    
    /**
     * AGGRESSIVE NOTICE SUPPRESSION
     * Removes all WordPress admin notices to prevent interference with our custom interface
     */
    private function suppress_all_admin_notices() {
        // Remove all admin notices immediately
        add_action('admin_print_styles', function() {
            // Remove all notice actions
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            remove_all_actions('user_admin_notices');
            
            // Remove specific plugin notices
            remove_all_actions('admin_notices', 10);
            remove_all_actions('admin_notices', 20);
            
            // Hide notices with CSS as backup
            echo '<style>
                .notice, .error, .updated, .update-nag, 
                .admin-notice, .plugin-update-tr,
                div[class*="notice"], div[class*="error"], div[class*="updated"],
                .wrap > .notice, .wrap > .error, .wrap > .updated,
                #message, .message, .settings-error {
                    display: none !important;
                }
                
                /* Hide specific plugin update notices */
                .plugin-update-tr, .plugin-install-php-update-required,
                .update-message, .updating-message,
                .plugin-update-tr td, .plugin-update-tr th {
                    display: none !important;
                }
                
                /* Hide theme notices */
                .theme-update, .theme-info, .available-theme,
                .theme-overlay, .theme-screenshot {
                    display: none !important;
                }
                
                /* Hide core update notices */
                .update-core-php, .update-php,
                .update-core, .core-updates {
                    display: none !important;
                }
            </style>';
        }, 1);
        
        // Additional suppression for late-loading notices
        add_action('admin_head', function() {
            echo '<style>
                .notice, .error, .updated, .update-nag,
                .admin-notice, .settings-error {
                    display: none !important;
                }
            </style>';
        }, 999);
        
        // Remove notices via JavaScript as final fallback
        add_action('admin_footer', function() {
            echo '<script>
                jQuery(document).ready(function($) {
                    // Hide all notice elements
                    $(".notice, .error, .updated, .update-nag, .admin-notice, .settings-error").hide();
                    
                    // Remove notice elements completely
                    setTimeout(function() {
                        $(".notice, .error, .updated, .update-nag, .admin-notice, .settings-error").remove();
                    }, 100);
                });
            </script>';
        }, 999);
    }
}