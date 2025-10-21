<?php

/**
 * Grove Streamflow Mar Page Class
 * Handles the Streamflow Mar admin page functionality
 */
class Grove_Streamflow_Mar {
    
    public function __construct() {
        // Constructor can be used for initialization if needed
    }
    
    /**
     * Display the Streamflow Mar admin page
     */
    public function streamflow_mar_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_streamflow_menus';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;"><img src="<?php echo plugin_dir_url(__FILE__) . '../grove-shenzi-asset-mirror/grove-logo-1.png'; ?>" alt="Grove Logo" style="height: 40px; width: auto;">Streamflow Mar</h1>
                </div>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 16px; font-weight: bold;">streamflow_chamber</span>
                    <button id="create-popup-btn" class="button button-primary">Create New</button>
                    <button id="delete-selected-btn" class="button" style="background: #dc3545; color: white; border-color: #dc3545;">Delete Selected</button>
                    <div style="position: relative; margin-left: 18px;">
                        <input type="text" id="search-box" placeholder="Search menus..." style="padding: 8px 40px 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; font-size: 14px;">
                        <button id="clear-search" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #ffeb3b; border: none; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 3px; cursor: pointer;">CL</button>
                    </div>
                </div>
            </div>
            
            <!-- Rocket Chamber Div - Contains the pagination controls and search -->
            <div class="rocket_chamber_div" style="border: 1px solid black; padding: 0; margin: 20px 0; position: relative;">
                <div style="position: absolute; top: 4px; left: 4px; font-size: 16px; font-weight: bold; display: flex; align-items: center; gap: 6px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="black" style="transform: rotate(15deg);">
                        <!-- Rocket body -->
                        <ellipse cx="12" cy="8" rx="3" ry="6" fill="black"/>
                        <!-- Rocket nose cone -->
                        <path d="M12 2 L15 8 L9 8 Z" fill="black"/>
                        <!-- Left fin -->
                        <path d="M9 12 L7 14 L9 16 Z" fill="black"/>
                        <!-- Right fin -->
                        <path d="M15 12 L17 14 L15 16 Z" fill="black"/>
                        <!-- Exhaust flames -->
                        <path d="M10 14 L9 18 L10.5 16 L12 20 L13.5 16 L15 18 L14 14 Z" fill="black"/>
                        <!-- Window -->
                        <circle cx="12" cy="6" r="1" fill="white"/>
                    </svg>
                    rocket_chamber
                </div>
                <div style="margin-top: 24px; padding-top: 4px; padding-bottom: 0; padding-left: 8px; padding-right: 8px;">
                    <div style="display: flex; align-items: end; justify-content: space-between;">
                        <div style="display: flex; align-items: end; gap: 32px;">
                            <!-- Row pagination, search box, and column pagination table -->
                            <table style="border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 4px; text-align: center;">
                                            <div style="font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                <span style="font-weight: bold;">row pagination</span>
                                                <span style="font-size: 14px; font-weight: normal;">
                                                    Showing <span style="font-weight: bold;" id="streamflow-showing">0</span> of <span style="font-weight: bold;" id="streamflow-total">0</span> menus
                                                </span>
                                            </div>
                                        </td>
                                        <td style="border: 1px solid black; padding: 4px; text-align: center;">
                                            <div style="font-size: 16px; font-weight: bold;">
                                                search box 2
                                            </div>
                                        </td>
                                        <td style="border: 1px solid black; padding: 4px; text-align: center;">
                                            <div style="font-size: 16px; font-weight: bold;">
                                                wolf exclusion band
                                            </div>
                                        </td>
                                        <td style="border: 1px solid black; padding: 4px; text-align: center;">
                                            <div style="font-size: 16px; font-weight: bold;">
                                                column templates
                                            </div>
                                        </td>
                                        <td style="border: 1px solid black; padding: 4px; text-align: center;">
                                            <div style="font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                <span style="font-weight: bold;">column pagination</span>
                                                <span style="font-size: 14px; font-weight: normal;">
                                                    Showing <span style="font-weight: bold;" id="columns-showing">3</span> columns of <span style="font-weight: bold;">3</span> total columns
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 4px;">
                                            <div style="display: flex; align-items: end; gap: 16px;">
                                                <!-- Row Pagination Bar 1: Items per page selector -->
                                                <div style="display: flex; align-items: center;">
                                                    <span style="font-size: 12px; color: #4B5563; margin-right: 8px;">Rows/page:</span>
                                                    <div style="display: inline-flex; border-radius: 6px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                                        <button type="button" data-rows="3" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; border-radius: 6px 0 0 6px; margin-right: -1px; cursor: pointer; background: white;">3</button>
                                                        <button type="button" data-rows="4" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">4</button>
                                                        <button type="button" data-rows="5" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">5</button>
                                                        <button type="button" data-rows="10" class="grove-rows-per-page-btn active" style="padding: 10px 8px; font-size: 14px; border: 1px solid #3B82F6; background: #3B82F6; color: white; margin-right: -1px; cursor: pointer;">10</button>
                                                        <button type="button" data-rows="25" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">25</button>
                                                        <button type="button" data-rows="50" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">50</button>
                                                        <button type="button" data-rows="100" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">100</button>
                                                        <button type="button" data-rows="200" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; margin-right: -1px; cursor: pointer; background: white;">200</button>
                                                        <button type="button" data-rows="all" class="grove-rows-per-page-btn" style="padding: 10px 8px; font-size: 14px; border: 1px solid #D1D5DB; border-radius: 0 6px 6px 0; cursor: pointer; background: white;">All</button>
                                                    </div>
                                                </div>
                                                <!-- Row Pagination Bar 2: Page navigation -->
                                                <div style="display: flex; align-items: center;">
                                                    <span style="font-size: 12px; color: #4B5563; margin-right: 8px;">Row page:</span>
                                                    <nav style="display: inline-flex; border-radius: 6px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                                        <button type="button" id="grove-first-row-page" style="position: relative; display: inline-flex; align-items: center; border-radius: 6px 0 0 0; padding: 8px; font-size: 14px; padding-top: 10px; padding-bottom: 10px; color: #9CA3AF; border: 1px solid #D1D5DB; cursor: pointer; background: white;">≪</button>
                                                        <button type="button" id="grove-prev-row-page" style="position: relative; display: inline-flex; align-items: center; padding: 8px; font-size: 14px; padding-top: 10px; padding-bottom: 10px; color: #9CA3AF; border: 1px solid #D1D5DB; margin-left: -1px; cursor: pointer; background: white;">‹</button>
                                                        <span id="grove-current-row-page" style="position: relative; display: inline-flex; align-items: center; padding: 8px; font-size: 14px; padding-top: 10px; padding-bottom: 10px; color: #000; border: 1px solid #D1D5DB; margin-left: -1px; background: #F9FAFB;">1</span>
                                                        <button type="button" id="grove-next-row-page" style="position: relative; display: inline-flex; align-items: center; padding: 8px; font-size: 14px; padding-top: 10px; padding-bottom: 10px; color: #9CA3AF; border: 1px solid #D1D5DB; margin-left: -1px; cursor: pointer; background: white;">›</button>
                                                        <button type="button" id="grove-last-row-page" style="position: relative; display: inline-flex; align-items: center; border-radius: 0 6px 6px 0; padding: 8px; font-size: 14px; padding-top: 10px; padding-bottom: 10px; color: #9CA3AF; border: 1px solid #D1D5DB; margin-left: -1px; cursor: pointer; background: white;">≫</button>
                                                    </nav>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Table -->
            <div style="overflow-x: auto; border: 1px solid #ddd;">
                <table style="width: auto; border-collapse: collapse; background: white; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus">(ch)</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus">wp_zen_streamflow_menus</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus">wp_zen_streamflow_menus</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; background: #e0e0e0;"><div class="cell_inner_wrapper_div">(filler)</div></th>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: auto; white-space: nowrap;">
                                <div class="cell_inner_wrapper_div"><input type="checkbox" id="select-all" style="width: 20px; height: 20px;"></div>
                            </th>
                            <th class="for_db_table_zen_streamflow_menus" data-sort="menu_id" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus for_db_column_menu_id">menu_id</div></th>
                            <th class="for_db_table_zen_streamflow_menus" data-sort="menu_datum" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus for_db_column_menu_datum">menu_datum</div></th>
                            <th class="for_db_table_zen_streamflow_menus" data-sort="menu_description" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_streamflow_menus for_db_column_menu_description">menu_description</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Create/Edit Modal -->
        <div id="create-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 700px; max-height: 80vh; overflow-y: auto;">
                <h2 style="margin-top: 0;" id="modal-title">Create New Menu</h2>
                <form id="create-form">
                    <input type="hidden" id="menu-id" name="menu_id">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Menu Datum:</label>
                        <textarea id="menu-datum" name="menu_datum" rows="10" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace;" placeholder="Enter menu data..."></textarea>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Menu Description:</label>
                        <textarea id="menu-description" name="menu_description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Enter menu description..."></textarea>
                    </div>
                    <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
                        <button type="button" id="cancel-btn" style="padding: 10px 20px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 10px 20px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Load initial data
            loadStreamflowData();
            
            // Load streamflow menus data
            function loadStreamflowData() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_get_streamflow_menus',
                        nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            displayStreamflowData(response.data);
                            updateCounts(response.data.length);
                        }
                    }
                });
            }
            
            // Display data in table
            function displayStreamflowData(menus) {
                let tbody = $('#table-body');
                tbody.empty();
                
                menus.forEach(function(menu) {
                    let row = $('<tr data-id="' + menu.menu_id + '">');
                    
                    // Checkbox column
                    row.append('<td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;"><input type="checkbox" class="row-checkbox" value="' + menu.menu_id + '" style="width: 18px; height: 18px;"></td>');
                    
                    // Menu ID column
                    row.append('<td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;">' + menu.menu_id + '</td>');
                    
                    // Menu Datum column (truncated)
                    let datumPreview = menu.menu_datum ? (menu.menu_datum.length > 50 ? menu.menu_datum.substring(0, 50) + '...' : menu.menu_datum) : '';
                    row.append('<td style="border: 1px solid #ddd; padding: 8px; max-width: 300px; word-wrap: break-word;">' + datumPreview + '</td>');
                    
                    // Menu Description column
                    let descriptionPreview = menu.menu_description ? (menu.menu_description.length > 50 ? menu.menu_description.substring(0, 50) + '...' : menu.menu_description) : '';
                    row.append('<td style="border: 1px solid #ddd; padding: 8px; max-width: 200px; word-wrap: break-word;">' + descriptionPreview + '</td>');
                    
                    // Actions column
                    row.append('<td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;"><button class="button button-small edit-btn" data-id="' + menu.menu_id + '">Edit</button> <button class="button button-small delete-btn" data-id="' + menu.menu_id + '" style="background: #dc3545; color: white; border-color: #dc3545; margin-left: 5px;">Delete</button></td>');
                    
                    tbody.append(row);
                });
            }
            
            // Update counts
            function updateCounts(total) {
                $('#streamflow-showing').text(total);
                $('#streamflow-total').text(total);
            }
            
            // Open create modal
            $('#create-popup-btn').click(function() {
                $('#modal-title').text('Create New Menu');
                $('#create-form')[0].reset();
                $('#menu-id').val('');
                $('#create-modal').show();
            });

            // Open edit modal
            $(document).on('click', '.edit-btn', function() {
                let menuId = $(this).data('id');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_get_streamflow_menu',
                        nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                        menu_id: menuId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modal-title').text('Edit Menu');
                            $('#menu-id').val(response.data.menu_id);
                            $('#menu-datum').val(response.data.menu_datum);
                            $('#menu-description').val(response.data.menu_description);
                            $('#create-modal').show();
                        }
                    }
                });
            });

            // Close modal
            $('#cancel-btn').click(function() {
                $('#create-modal').hide();
            });

            // Handle form submission
            $('#create-form').submit(function(e) {
                e.preventDefault();
                
                let menuId = $('#menu-id').val();
                let action = menuId ? 'grove_update_streamflow_menu' : 'grove_create_streamflow_menu';
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: action,
                        nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                        menu_id: menuId,
                        menu_datum: $('#menu-datum').val(),
                        menu_description: $('#menu-description').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#create-modal').hide();
                            loadStreamflowData(); // Reload data
                        } else {
                            alert('Error: ' + response.data);
                        }
                    }
                });
            });

            // Select all checkbox
            $('#select-all').change(function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Delete selected
            $('#delete-selected-btn').click(function() {
                let selected = $('.row-checkbox:checked');
                if (selected.length === 0) {
                    alert('Please select items to delete.');
                    return;
                }
                
                if (confirm('Are you sure you want to delete the selected menus?')) {
                    let ids = [];
                    selected.each(function() {
                        ids.push($(this).val());
                    });
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_delete_streamflow_menus',
                            nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                            ids: ids
                        },
                        success: function(response) {
                            if (response.success) {
                                loadStreamflowData(); // Reload data
                            } else {
                                alert('Error: ' + response.data);
                            }
                        }
                    });
                }
            });

            // Individual delete
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this menu?')) {
                    let menuId = $(this).data('id');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_delete_streamflow_menus',
                            nonce: '<?php echo wp_create_nonce('grove_services_nonce'); ?>',
                            ids: [menuId]
                        },
                        success: function(response) {
                            if (response.success) {
                                loadStreamflowData(); // Reload data
                            } else {
                                alert('Error: ' + response.data);
                            }
                        }
                    });
                }
            });

            // Search functionality
            $('#search-box').on('input', function() {
                let searchTerm = $(this).val().toLowerCase();
                $('#table-body tr').each(function() {
                    let text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Clear search
            $('#clear-search').click(function() {
                $('#search-box').val('');
                $('#table-body tr').show();
            });
        });
        </script>

        <?php
    }
    
    /**
     * AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
     * Based on proven implementation from main admin class
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
                .grove-content {
                    margin-top: 0 !important;
                }
            </style>';
        }, 1);
        
        // Remove notices after they've been added
        add_action('admin_footer', function() {
            echo '<script>
                jQuery(document).ready(function($) {
                    $(".notice, .error, .updated, .update-nag, .admin-notice, .plugin-update-tr").remove();
                    $("div[class*=\"notice\"], div[class*=\"error\"], div[class*=\"updated\"]").remove();
                    $("#message, .message, .settings-error").remove();
                });
            </script>';
        });
    }
}