<?php

/**
 * Grove Raven Mar Page Class
 * Handles the Grove Raven Mar admin page functionality
 */
class Grove_Raven_Mar {
    
    public function __construct() {
        // Constructor can be used for initialization if needed
    }
    
    /**
     * Display the Grove Raven Mar admin page
     */
    public function grove_raven_mar_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_raven_page_spaces';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        // Get all raven page spaces from database
        $raven_spaces = $wpdb->get_results("SELECT * FROM $table_name ORDER BY space_id ASC");
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;"><img src="<?php echo plugin_dir_url(__FILE__) . '../grove-shenzi-asset-mirror/grove-logo-1.png'; ?>" alt="Grove Logo" style="height: 40px; width: auto;">Grove Raven Mar</h1>
                </div>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 16px; font-weight: bold;">raven_chamber</span>
                    <button id="create-popup-btn" class="button button-primary">Create New Page Space</button>
                    <button id="delete-selected-btn" class="button" style="background: #dc3545; color: white; border-color: #dc3545;">Delete Selected</button>
                    <div style="position: relative; margin-left: 18px;">
                        <input type="text" id="search-box" placeholder="Search page spaces..." style="padding: 8px 40px 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; font-size: 14px;">
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
                                                    Showing <span style="font-weight: bold;" id="raven-showing"><?php echo count($raven_spaces); ?></span> of <span style="font-weight: bold;" id="raven-total"><?php echo count($raven_spaces); ?></span> page spaces
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
                                                    Showing <span style="font-weight: bold;" id="columns-showing">6</span> columns of <span style="font-weight: bold;">6</span> total columns
                                                </span>
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
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces">(ch)</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces">wp_zen_raven_page_spaces</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces">wp_zen_raven_page_spaces</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces">wp_zen_raven_page_spaces</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #e0e0e0;"><div class="cell_inner_wrapper_div for_db_table_wp_posts_according_to_asn_page_id">wp_posts</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; background: #e0e0e0;"><div class="cell_inner_wrapper_div">(filler)</div></th>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: auto; white-space: nowrap;">
                                <div class="cell_inner_wrapper_div"><input type="checkbox" id="select-all" style="width: 20px; height: 20px;"></div>
                            </th>
                            <th class="for_db_table_zen_raven_page_spaces" data-sort="space_id" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces for_db_column_space_id">space_id</div></th>
                            <th class="for_db_table_zen_raven_page_spaces" data-sort="is_default_data" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces for_db_column_is_default_data">is_default_data</div></th>
                            <th class="for_db_table_zen_raven_page_spaces" data-sort="space_name" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces for_db_column_space_name">space_name</div></th>
                            <th class="for_db_table_zen_raven_page_spaces" data-sort="asn_page_id" style="border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces for_db_column_asn_page_id">asn_page_id</div></th>
                            <th style="border: 1px solid #ddd; font-weight: bold; background: #f8f9fa; width: auto; white-space: nowrap;"><div class="cell_inner_wrapper_div">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php foreach ($raven_spaces as $space): ?>
                        <tr data-id="<?php echo esc_attr($space->space_id); ?>">
                            <td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;">
                                <input type="checkbox" class="row-checkbox" value="<?php echo esc_attr($space->space_id); ?>" style="width: 18px; height: 18px;">
                            </td>
                            <td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;"><?php echo esc_html($space->space_id); ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;">
                                <span style="color: <?php echo $space->is_default_data ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $space->is_default_data ? 'Yes' : 'No'; ?>
                                </span>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold; white-space: nowrap;"><?php echo esc_html($space->space_name); ?></td>
                            <td class="for_db_table_zen_raven_page_spaces" style="border: 1px solid #ddd;">
                                <div class="cell_inner_wrapper_div for_db_table_zen_raven_page_spaces for_db_column_asn_page_id">
                                    <button class="button button-small choose-page-btn" data-space-id="<?php echo esc_attr($space->space_id); ?>" style="margin-right: 8px;">Choose Page</button>
                                    <?php if ($space->asn_page_id): ?>
                                        <button class="button button-small clear-page-btn" data-space-id="<?php echo esc_attr($space->space_id); ?>" style="margin-right: 8px; font-size: 11px; padding: 2px 6px; background: #dc3545; color: white; border-color: #dc3545;">Clear</button>
                                        <span class="page-info" data-page-id="<?php echo esc_attr($space->asn_page_id); ?>">
                                            (<?php echo esc_html($space->asn_page_id); ?>) | <span class="page-title-text">Loading...</span>
                                        </span>
                                        <div style="margin-top: 8px; display: flex; gap: 4px;">
                                            <a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo esc_attr($space->asn_page_id); ?>&action=edit" target="_blank" class="button" style="font-size: 14px; padding: 3px; text-decoration: none;">pendulum</a>
                                            <a href="<?php echo site_url(); ?>/wp-admin/post.php?post=<?php echo esc_attr($space->asn_page_id); ?>&action=elementor" target="_blank" class="button" style="font-size: 14px; padding: 3px; text-decoration: none;">elementor</a>
                                            <a href="#" class="button frontend-link" data-page-id="<?php echo esc_attr($space->asn_page_id); ?>" target="_blank" style="font-size: 14px; padding: 3px; text-decoration: none;">frontend</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 8px; white-space: nowrap;">
                                <button class="button button-small edit-btn" data-id="<?php echo esc_attr($space->space_id); ?>">Edit</button>
                                <button class="button button-small delete-btn" data-id="<?php echo esc_attr($space->space_id); ?>" style="background: #dc3545; color: white; border-color: #dc3545; margin-left: 5px;">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Create/Edit Modal -->
        <div id="create-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 500px; max-height: 80vh; overflow-y: auto;">
                <h2 style="margin-top: 0;" id="modal-title">Create New Page Space</h2>
                <form id="create-form">
                    <input type="hidden" id="space-id" name="space_id">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Space Name:</label>
                        <input type="text" id="space-name" name="space_name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Assigned Page:</label>
                        <select id="asn-page-id" name="asn_page_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="">Select a page...</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                echo '<option value="' . esc_attr($page->ID) . '">' . esc_html($page->post_title) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">
                            <input type="checkbox" id="is-default-data" name="is_default_data" style="margin-right: 8px;">
                            Is Default Data
                        </label>
                    </div>
                    <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
                        <button type="button" id="cancel-btn" style="padding: 10px 20px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 10px 20px; background: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer;">Save</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Page Selector Modal -->
        <div id="raven-page-selector-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 80%; max-width: 1200px; max-height: 80vh; overflow-y: auto;">
                <h2 style="margin-top: 0;">Select a Page or Post</h2>
                
                <!-- Search Box -->
                <div style="margin-bottom: 20px;">
                    <input type="text" id="raven-page-search" placeholder="Search pages and posts..." style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <button id="clear-raven-page-search" class="button button-small" style="margin-left: 10px;">Clear Search</button>
                </div>
                
                <!-- Pages Table -->
                <div style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                    <table id="raven-pages-table" style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Select</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">ID</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Title</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Type</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Date</th>
                            </tr>
                        </thead>
                        <tbody id="raven-pages-tbody">
                            <!-- Pages will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" id="cancel-raven-page-select" class="button button-secondary" style="margin-right: 10px;">Cancel</button>
                    <button type="button" id="assign-raven-page-select" class="button button-primary">Assign Selected Page</button>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Load page titles and frontend links after page loads
            loadPageTitles();
            loadFrontendLinks();
            
            // Load page titles for existing assignments
            function loadPageTitles() {
                $('.page-info').each(function() {
                    let pageId = $(this).data('page-id');
                    let titleElement = $(this).find('.page-title-text');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_get_page_title',
                            nonce: '<?php echo wp_create_nonce('grove_raven_nonce'); ?>',
                            page_id: pageId
                        },
                        success: function(response) {
                            if (response.success) {
                                titleElement.text(response.data.title);
                            } else {
                                titleElement.text('(Not found)');
                            }
                        },
                        error: function() {
                            titleElement.text('(Error loading)');
                        }
                    });
                });
            }
            
            // Load frontend links for navigation buttons
            function loadFrontendLinks() {
                $('.frontend-link').each(function() {
                    let link = $(this);
                    let pageId = link.data('page-id');
                    
                    if (pageId) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'grove_get_page_permalink',
                                nonce: '<?php echo wp_create_nonce('grove_raven_nonce'); ?>',
                                page_id: pageId
                            },
                            success: function(response) {
                                if (response.success) {
                                    link.attr('href', response.data.permalink);
                                } else {
                                    link.attr('href', '#').text('frontend (error)');
                                }
                            },
                            error: function() {
                                link.attr('href', '#').text('frontend (error)');
                            }
                        });
                    }
                });
            }
            
            // Open create modal
            $('#create-popup-btn').click(function() {
                $('#modal-title').text('Create New Page Space');
                $('#create-form')[0].reset();
                $('#space-id').val('');
                $('#create-modal').show();
            });

            // Open edit modal
            $('.edit-btn').click(function() {
                const spaceId = $(this).data('id');
                const row = $(this).closest('tr');
                
                $('#modal-title').text('Edit Page Space');
                $('#space-id').val(spaceId);
                $('#space-name').val(row.find('td:nth-child(4)').text());
                $('#is-default-data').prop('checked', row.find('td:nth-child(3)').text().trim() === 'Yes');
                
                // Set selected page if exists
                const pageCell = row.find('td:nth-child(5)').text();
                if (pageCell !== 'Not assigned') {
                    // You might need to get the actual page ID here
                }
                
                $('#create-modal').show();
            });

            // Close modal
            $('#cancel-btn').click(function() {
                $('#create-modal').hide();
            });

            // Handle form submission
            $('#create-form').submit(function(e) {
                e.preventDefault();
                // Add AJAX handling here
                alert('Form submission functionality would be implemented here with AJAX');
                $('#create-modal').hide();
            });

            // Select all checkbox
            $('#select-all').change(function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Delete selected
            $('#delete-selected-btn').click(function() {
                const selected = $('.row-checkbox:checked');
                if (selected.length === 0) {
                    alert('Please select items to delete.');
                    return;
                }
                
                if (confirm('Are you sure you want to delete the selected page spaces?')) {
                    // Add AJAX delete functionality here
                    alert('Delete functionality would be implemented here with AJAX');
                }
            });

            // Individual delete
            $('.delete-btn').click(function() {
                if (confirm('Are you sure you want to delete this page space?')) {
                    // Add AJAX delete functionality here
                    alert('Delete functionality would be implemented here with AJAX');
                }
            });

            // Search functionality
            $('#search-box').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#table-body tr').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Clear search
            $('#clear-search').click(function() {
                $('#search-box').val('');
                $('#table-body tr').show();
            });
            
            // Page selector functionality
            let currentSpaceId = null;
            let allRavenPagesData = [];
            
            // Open page selector modal
            $(document).on('click', '.choose-page-btn', function() {
                currentSpaceId = $(this).data('space-id');
                
                // Load pages via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_get_all_pages',
                        nonce: '<?php echo wp_create_nonce('grove_raven_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            allRavenPagesData = response.data;
                            displayRavenPages(allRavenPagesData);
                            $('#raven-page-selector-modal').show();
                        }
                    }
                });
            });
            
            // Display pages in modal
            function displayRavenPages(pages) {
                let tbody = $('#raven-pages-tbody');
                tbody.empty();
                
                pages.forEach(function(page) {
                    let row = $('<tr>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;"><input type="radio" name="raven-page-select" value="' + page.ID + '" data-title="' + page.post_title + '"></td>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;">' + page.ID + '</td>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;">' + page.post_title + '</td>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;">' + page.post_type + '</td>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;">' + page.post_status + '</td>');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;">' + page.post_date + '</td>');
                    tbody.append(row);
                });
            }
            
            // Search pages in modal
            $('#raven-page-search').on('input', function() {
                let searchTerm = $(this).val().toLowerCase();
                let filteredPages = allRavenPagesData.filter(function(page) {
                    return page.post_title.toLowerCase().includes(searchTerm) ||
                           page.ID.toString().includes(searchTerm) ||
                           page.post_type.toLowerCase().includes(searchTerm);
                });
                displayRavenPages(filteredPages);
            });
            
            // Clear search in modal
            $('#clear-raven-page-search').click(function() {
                $('#raven-page-search').val('');
                displayRavenPages(allRavenPagesData);
            });
            
            // Cancel page selection
            $('#cancel-raven-page-select').click(function() {
                $('#raven-page-selector-modal').hide();
            });
            
            // Assign selected page
            $('#assign-raven-page-select').click(function() {
                let selectedPageId = $('input[name="raven-page-select"]:checked').val();
                
                if (!selectedPageId) {
                    alert('Please select a page first.');
                    return;
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_update_raven_page',
                        nonce: '<?php echo wp_create_nonce('grove_raven_nonce'); ?>',
                        space_id: currentSpaceId,
                        page_id: selectedPageId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#raven-page-selector-modal').hide();
                            location.reload(); // Reload to show updated data
                            // After reload, the loadPageTitles() and loadFrontendLinks() will run automatically
                        } else {
                            alert('Error updating page: ' + response.data);
                        }
                    }
                });
            });
            
            // Clear page assignment
            $(document).on('click', '.clear-page-btn', function() {
                let spaceId = $(this).data('space-id');
                
                if (confirm('Are you sure you want to clear the page assignment for this space?')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_update_raven_page',
                            nonce: '<?php echo wp_create_nonce('grove_raven_nonce'); ?>',
                            space_id: spaceId,
                            page_id: ''
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload(); // Reload to show updated data
                            } else {
                                alert('Error clearing page assignment: ' + response.data);
                            }
                        }
                    });
                }
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