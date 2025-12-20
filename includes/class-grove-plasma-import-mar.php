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
                
                <!-- Error Reporting Section (initially hidden) -->
                <div id="error-reporting-container" style="margin-bottom: 20px; display: none;">
                    <div style="background: #fff2cd; border: 1px solid #f1c40f; padding: 15px; border-radius: 5px; border-left: 4px solid #f39c12;">
                        <h4 style="margin: 0 0 10px 0; color: #d68910;">Import Error Details</h4>
                        <textarea id="error-details-text" readonly style="width: 100%; height: 200px; font-family: monospace; font-size: 12px; background: #fefefe; border: 1px solid #ddd; padding: 10px; resize: vertical;" placeholder="Error details will appear here..."></textarea>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #666;">
                            You can copy this error information to share with support or for debugging.
                        </p>
                    </div>
                </div>
                
                <!-- Tab Navigation -->
                <div style="border-bottom: 1px solid #ddd; margin-bottom: 20px;">
                    <div style="display: flex; gap: 0;">
                        <button id="tab-main" class="plasma-tab active" style="padding: 12px 20px; background: #fff; border: 1px solid #ddd; border-bottom: none; border-top-left-radius: 5px; border-top-right-radius: 5px; cursor: pointer; font-weight: 500; color: #0073aa;">
                            Main Tab 1
                        </button>
                        <button id="tab-error" class="plasma-tab" style="padding: 12px 20px; background: #f1f1f1; border: 1px solid #ddd; border-bottom: none; border-top-left-radius: 5px; border-top-right-radius: 5px; cursor: pointer; font-weight: 500; color: #333; margin-left: -1px;">
                            Error Reporting
                        </button>
                    </div>
                </div>
                
                <!-- Tab Content Container -->
                <div id="tab-content">
                    
                    <!-- Main Tab Content -->
                    <div id="main-tab-content" class="tab-content active">
                
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
                    
                    <div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #007cba; border-radius: 3px;">
                        <label style="font-weight: 500; display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="update-empty-fields" checked style="margin-right: 8px;">
                            Update all selected items to empty (if empty)
                        </label>
                        <div style="font-size: 12px; color: #666; margin-top: 5px; margin-left: 20px;">
                            When checked: Empty values from import will set database columns to empty<br>
                            When unchecked: Empty values from import will be ignored (existing data preserved)
                        </div>
                    </div>
                    
                    <div id="driggs-table-container">
                        <!-- Driggs data table will be populated via JavaScript -->
                    </div>
                    </div>
                    
                    <!-- Error Reporting Tab Content -->
                    <div id="error-tab-content" class="tab-content" style="display: none;">
                        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px;">
                            <h3 style="margin-top: 0;">Detailed Error Information</h3>
                            <div style="margin-bottom: 15px;">
                                <p>This tab shows detailed error information from import attempts. Error details will automatically appear here when imports fail.</p>
                            </div>
                            
                            <div id="error-log-container">
                                <textarea id="error-log-display" readonly style="width: 100%; height: 400px; font-family: 'Courier New', monospace; font-size: 12px; background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; resize: vertical; line-height: 1.4;" placeholder="No errors recorded yet. Import errors will appear here with full details including:&#10;- Error messages&#10;- Stack traces&#10;- Request data&#10;- Server responses&#10;- Timestamps"></textarea>
                            </div>
                            
                            <div style="margin-top: 15px; display: flex; gap: 10px;">
                                <button id="clear-error-log" class="button" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 3px;">Clear Error Log</button>
                                <button id="copy-error-log" class="button" style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 3px;">Copy to Clipboard</button>
                                <span id="copy-feedback" style="color: #28a745; font-size: 12px; display: none; align-self: center;">Copied to clipboard!</span>
                            </div>
                        </div>
                    </div>
                    
                </div> <!-- End tab-content -->
                
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            let importedData = null;
            let errorLog = [];
            
            // Tab switching functionality
            $('.plasma-tab').click(function() {
                const tabId = $(this).attr('id');
                
                // Remove active class from all tabs and content
                $('.plasma-tab').removeClass('active').css({
                    'background': '#f1f1f1',
                    'color': '#333'
                });
                $('.tab-content').removeClass('active').hide();
                
                // Add active class to clicked tab
                $(this).addClass('active').css({
                    'background': '#fff',
                    'color': '#0073aa'
                });
                
                // Show corresponding content
                if (tabId === 'tab-main') {
                    $('#main-tab-content').addClass('active').show();
                } else if (tabId === 'tab-error') {
                    $('#error-tab-content').addClass('active').show();
                }
            });
            
            // Error reporting functions
            function addErrorToLog(error) {
                const timestamp = new Date().toISOString();
                const errorEntry = {
                    timestamp: timestamp,
                    error: error,
                    userAgent: navigator.userAgent,
                    url: window.location.href
                };
                
                errorLog.push(errorEntry);
                updateErrorDisplay();
                
                // Also show the compact error at top
                showCompactError(error);
            }
            
            function updateErrorDisplay() {
                let logText = '';
                errorLog.forEach((entry, index) => {
                    logText += `=== ERROR ${index + 1} [${entry.timestamp}] ===\n`;
                    logText += `URL: ${entry.url}\n`;
                    logText += `User Agent: ${entry.userAgent}\n`;
                    logText += `\nERROR DETAILS:\n`;
                    if (typeof entry.error === 'object') {
                        logText += JSON.stringify(entry.error, null, 2);
                    } else {
                        logText += entry.error;
                    }
                    logText += '\n\n' + '='.repeat(80) + '\n\n';
                });
                
                $('#error-log-display').val(logText);
            }
            
            function showCompactError(error) {
                let errorText = '';
                if (typeof error === 'object') {
                    errorText = JSON.stringify(error, null, 2);
                } else {
                    errorText = error;
                }
                
                $('#error-details-text').val(errorText);
                $('#error-reporting-container').show();
            }
            
            // Clear error log
            $('#clear-error-log').click(function() {
                if (confirm('Are you sure you want to clear the error log?')) {
                    errorLog = [];
                    updateErrorDisplay();
                    $('#error-reporting-container').hide();
                }
            });
            
            // Copy error log to clipboard
            $('#copy-error-log').click(function() {
                const logText = $('#error-log-display').val();
                navigator.clipboard.writeText(logText).then(function() {
                    $('#copy-feedback').show().delay(2000).fadeOut();
                });
            });

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

                // Prepare the data for import
                const selectedPages = [];
                selectedIndexes.forEach(function(index) {
                    if (importedData.pages[index]) {
                        selectedPages.push(importedData.pages[index]);
                    }
                });

                // Disable button during processing
                const $btn = $('#create-pages-btn');
                const originalText = $btn.text();
                $btn.prop('disabled', true).text('Creating pages...');

                // Get the empty fields update setting
                const updateEmptyFields = $('#update-empty-fields').is(':checked');

                // Make AJAX call to import pages
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_plasma_import',
                        pages: selectedPages,
                        update_empty_fields: updateEmptyFields ? 'true' : 'false',
                        nonce: '<?php echo wp_create_nonce("grove_plasma_import"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('✅ Success: ' + response.data.message);
                            
                            // Show detailed results if available
                            if (response.data.details) {
                                const details = response.data.details;
                                let detailMsg = '';
                                if (details.success && details.success.length > 0) {
                                    detailMsg += 'Successfully created:\n';
                                    details.success.forEach(function(item) {
                                        detailMsg += '- ' + item.title + ' (Post ID: ' + item.post_id + ')\n';
                                    });
                                }
                                if (details.errors && details.errors.length > 0) {
                                    detailMsg += '\nErrors:\n';
                                    details.errors.forEach(function(item) {
                                        detailMsg += '- ' + (item.title || 'Unknown') + ': ' + item.message + '\n';
                                    });
                                    
                                    // If there were errors, log them for detailed analysis
                                    addErrorToLog({
                                        type: 'Import Partial Success with Errors',
                                        message: response.data.message,
                                        details: details,
                                        requestData: {
                                            pages: selectedPages,
                                            update_empty_fields: updateEmptyFields
                                        }
                                    });
                                }
                                if (detailMsg) {
                                    console.log('Import Details:', detailMsg);
                                }
                            }
                        } else {
                            // Import failed - log detailed error
                            const errorData = {
                                type: 'Import Failed',
                                message: response.data ? response.data.message : 'Unknown error occurred',
                                fullResponse: response,
                                requestData: {
                                    pages: selectedPages,
                                    update_empty_fields: updateEmptyFields
                                }
                            };
                            
                            addErrorToLog(errorData);
                            alert('❌ Error: ' + errorData.message + '\n\nDetailed error information has been logged. Check the Error Reporting tab for full details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // AJAX request failed - log comprehensive error
                        const errorData = {
                            type: 'AJAX Request Failed',
                            status: status,
                            error: error,
                            statusCode: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            requestData: {
                                pages: selectedPages,
                                update_empty_fields: updateEmptyFields
                            }
                        };
                        
                        addErrorToLog(errorData);
                        alert('❌ AJAX Error: ' + error + '\n\nFull error details have been logged. Check the Error Reporting tab for complete information.');
                        console.error('AJAX Error:', xhr, status, error);
                    },
                    complete: function() {
                        // Re-enable button
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
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
        
        /* Tab styling */
        .plasma-tab {
            transition: all 0.2s ease;
        }
        .plasma-tab:hover {
            background: #e9ecef !important;
            color: #0073aa !important;
        }
        .plasma-tab.active:hover {
            background: #fff !important;
        }
        
        /* Tab content animation */
        .tab-content {
            animation: fadeIn 0.2s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Error reporting styles */
        #error-details-text, #error-log-display {
            font-family: 'Courier New', Consolas, 'Monaco', monospace !important;
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