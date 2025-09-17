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
        // Factory codes handlers
        add_action('wp_ajax_grove_factory_codes_get_data', array($this, 'grove_factory_codes_get_data'));
        add_action('wp_ajax_grove_factory_codes_update_field', array($this, 'grove_factory_codes_update_field'));
        add_action('wp_ajax_grove_factory_codes_delete', array($this, 'grove_factory_codes_delete'));
        add_action('wp_ajax_grove_factory_codes_create', array($this, 'grove_factory_codes_create'));
        // Export handlers
        add_action('wp_ajax_grove_export_sharkintax', array($this, 'grove_export_sharkintax'));
        add_action('wp_ajax_grove_export_walrustax', array($this, 'grove_export_walrustax'));
        add_action('wp_ajax_grove_export_csv', array($this, 'grove_export_csv'));
        add_action('wp_ajax_grove_export_nova_beluga_both', array($this, 'grove_export_nova_beluga_both'));
        add_action('wp_ajax_grove_export_nova_beluga_friendly', array($this, 'grove_export_nova_beluga_friendly'));
        add_action('wp_ajax_grove_get_friendly_name', array($this, 'grove_get_friendly_name'));
        add_action('wp_ajax_grove_get_all_friendly_names', array($this, 'grove_get_all_friendly_names'));
        // Hoof codes handlers
        add_action('wp_ajax_grove_update_hoof_code', array($this, 'grove_update_hoof_code'));
        add_action('wp_ajax_grove_hoof_create', array($this, 'grove_hoof_create'));
        add_action('wp_ajax_grove_hoof_update', array($this, 'grove_hoof_update'));
        add_action('wp_ajax_grove_hoof_delete', array($this, 'grove_hoof_delete'));
        add_action('wp_ajax_grove_export_xls', array($this, 'grove_export_xls'));
        add_action('wp_ajax_grove_export_sql', array($this, 'grove_export_sql'));
        // General shortcodes handlers
        add_action('wp_ajax_grove_generalshortcodes_load', array($this, 'grove_generalshortcodes_load'));
        add_action('wp_ajax_grove_generalshortcodes_create', array($this, 'grove_generalshortcodes_create'));
        add_action('wp_ajax_grove_generalshortcodes_update', array($this, 'grove_generalshortcodes_update'));
        add_action('wp_ajax_grove_generalshortcodes_delete', array($this, 'grove_generalshortcodes_delete'));
        // WordPress native settings handlers
        add_action('wp_ajax_grove_update_site_settings', array($this, 'grove_update_site_settings'));
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
        
        add_submenu_page(
            'grovehub',
            'Grove Factory Codes',
            'grove_factory_codes',
            'manage_options',
            'grove_factory_codes',
            array($this, 'grove_factory_codes_page')
        );
        
        add_submenu_page(
            'grovehub',
            'Grove Buffalo Manager',
            'grove_buffalor',
            'manage_options',
            'grove_buffalor',
            array($this, 'grove_buffalor_page')
        );
        
        add_submenu_page(
            'grovehub',
            'Grove Hoof Manager',
            'grove_hoof_mar',
            'manage_options',
            'grove_hoof_mar',
            array($this, 'grove_hoof_mar_page')
        );
        
        add_submenu_page(
            'grovehub',
            'General Shortcodes Manager',
            'grove_generalshortcodes_mar',
            'manage_options',
            'grove_generalshortcodes_mar',
            array($this, 'grove_generalshortcodes_mar_page')
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
        
        // Enqueue WordPress media scripts for site icon functionality
        wp_enqueue_media();
        
        ?>
        <div class="wrap grove-content" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <!-- Grove Title -->
                <div style="margin-bottom: 15px;">
                    <h1 style="margin: 0;">🌳 Grove Hub - Driggs Site Manager</h1>
                </div>
                
                <!-- WordPress Native Settings - Horizontal Bar -->
                <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 12px 20px; margin-bottom: 20px; border-radius: 5px;">
                    <form id="grove-site-settings-form">
                        <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
                            
                            <!-- Site Title Field -->
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label for="grove_blogname" style="font-weight: 600; white-space: nowrap; font-size: 13px;">Site Title:</label>
                                <input 
                                    name="grove_blogname" 
                                    id="grove_blogname" 
                                    type="text" 
                                    value="<?php echo esc_attr(get_option('blogname')); ?>" 
                                    style="width: 180px; padding: 4px 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 13px;"
                                />
                            </div>
                            
                            <!-- Tagline Field -->
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label for="grove_blogdescription" style="font-weight: 600; white-space: nowrap; font-size: 13px;">Tagline:</label>
                                <input 
                                    name="grove_blogdescription" 
                                    id="grove_blogdescription" 
                                    type="text" 
                                    value="<?php echo esc_attr(get_option('blogdescription')); ?>" 
                                    style="width: 200px; padding: 4px 8px; border: 1px solid #ccc; border-radius: 3px; font-size: 13px;"
                                />
                            </div>
                            
                            <!-- Site Icon Field -->
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label style="font-weight: 600; white-space: nowrap; font-size: 13px;">Site Icon:</label>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div id="grove-site-icon-preview" style="width: 24px; height: 24px; display: flex; align-items: center;">
                                        <?php
                                        $site_icon_id = get_option('site_icon');
                                        if ($site_icon_id) {
                                            $site_icon_img = wp_get_attachment_image($site_icon_id, array(24, 24), false, array('id' => 'grove-site-icon-img', 'style' => 'width: 24px; height: 24px; border-radius: 2px;'));
                                            echo $site_icon_img;
                                        } else {
                                            echo '<img id="grove-site-icon-img" src="" style="display: none; width: 24px; height: 24px; border-radius: 2px;">';
                                        }
                                        ?>
                                    </div>
                                    <button type="button" id="grove-choose-site-icon" class="button button-small" style="padding: 2px 8px; font-size: 12px; height: auto;">
                                        <?php echo $site_icon_id ? 'Change' : 'Select'; ?>
                                    </button>
                                    <button type="button" id="grove-remove-site-icon" class="button button-small" style="padding: 2px 6px; font-size: 12px; height: auto; <?php echo !$site_icon_id ? 'display: none;' : ''; ?>">×</button>
                                    <input type="hidden" id="grove_site_icon" name="grove_site_icon" value="<?php echo esc_attr($site_icon_id); ?>" />
                                </div>
                            </div>
                            
                            <!-- Save Button -->
                            <div style="margin-left: auto;">
                                <button type="button" id="grove-save-settings" class="button button-primary button-small" style="padding: 4px 12px; font-size: 13px;">Save Changes</button>
                            </div>
                            
                        </div>
                    </form>
                </div>
                
                <!-- Control Bar -->
                <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div>
                                <strong>Current Site:</strong> <?php echo esc_html($current_site_url); ?>
                            </div>
                            <!-- Export Button Bar -->
                            <div style="display: flex; gap: 5px; align-items: center; border-left: 2px solid #ddd; padding-left: 20px;">
                                <button id="export-sharkintax-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">copy all sharkintax</button>
                                <button id="export-walrustax-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">copy all walrustax</button>
                                <button id="export-xls-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">xls</button>
                                <button id="export-csv-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">csv</button>
                                <button id="export-sql-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">sql</button>
                                <div style="border-left: 1px solid #ccc; height: 25px; margin: 0 10px;"></div>
                                <div style="position: relative; display: flex; align-items: center; gap: 10px;">
                                    <button id="nova-beluga-btn" class="button button-secondary" style="padding: 5px 12px; font-size: 13px;">nova beluga</button>
                                    <label style="font-size: 11px; display: flex; align-items: center; gap: 3px;">
                                        <input type="checkbox" id="omit-no-friendly" checked style="margin: 0;"> 
                                        omit items w/o lighthouse friendly name 1
                                    </label>
                                    <label style="font-size: 11px; display: flex; align-items: center; gap: 3px;">
                                        <input type="checkbox" id="use-custom-position" checked style="margin: 0;"> 
                                        use custom_position_a to order
                                    </label>
                                    <div id="nova-beluga-dropdown" style="
                                        position: absolute;
                                        top: 100%;
                                        left: 0;
                                        background: white;
                                        border: 1px solid #ddd;
                                        border-radius: 4px;
                                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                        z-index: 1000;
                                        min-width: 320px;
                                        display: none;
                                        margin-top: 2px;
                                    ">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr style="cursor: pointer;" class="nova-option" data-action="both">
                                                <td style="padding: 8px 12px; border: 1px solid #eee; width: 40%;">copy with both field name sets</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center; width: 20%;">sharkintax</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center; width: 20%;">-</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center; width: 20%;">-</td>
                                            </tr>
                                            <tr style="cursor: pointer;" class="nova-option" data-action="friendly">
                                                <td style="padding: 8px 12px; border: 1px solid #eee;">copy with friendly_name_1_datum only</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center;">sharkintax</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center;">-</td>
                                                <td style="padding: 8px 12px; border: 1px solid #eee; text-align: center;">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
                        <table id="driggs-table" style="width: auto; border-collapse: collapse; font-size: 14px;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: 50px;">
                                        <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
                                    </th>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa;">Field Name</th>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa;">Value</th>
                                    <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa;">shortcode 1</th>
                                    <th style="padding: 0; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa; width: 20px;">stuff3</th>
                                    <th class="dogsdogs" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; background: #f8f9fa;">_zen_lighthouse_friendly_names.friendly_name_1_datum</th>
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
            color: #666 !important;
            font-style: italic !important;
            cursor: default !important;
            user-select: text !important;
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            pointer-events: auto !important;
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
                
                // Field definitions matching /drom app order exactly - preserve top 5 system fields
                const fields = [
                    // System fields (keep at top with gray background)
                    {key: 'wppma_id', label: 'wppma_id', type: 'number'},
                    {key: 'wppma_db_only_created_at', label: 'wppma_db_only_created_at', type: 'datetime'},
                    {key: 'wppma_db_only_updated_at', label: 'wppma_db_only_updated_at', type: 'datetime'},
                    {key: 'id', label: 'id', type: 'text'},
                    {key: 'created_at', label: 'created_at', type: 'datetime'},
                    
                    // Core site information (matching /drom order)
                    {key: 'sitespren_base', label: 'sitespren_base', type: 'text'},
                    {key: 'driggs_brand_name', label: 'driggs_brand_name', type: 'text'},
                    {key: 'driggs_revenue_goal', label: 'driggs_revenue_goal', type: 'number'},
                    
                    // phone section
                    {key: 'phone_section_separator', label: 'phone section', type: 'separator'},
                    {key: 'driggs_phone_country_code', label: 'driggs_phone_country_code', type: 'number'},
                    {key: 'driggs_phone1_platform_id', label: 'driggs_phone1_platform_id', type: 'number'},
                    {key: 'driggs_phone_1', label: 'driggs_phone_1', type: 'text'},
                    
                    // address section
                    {key: 'address_section_separator', label: 'address section', type: 'separator'},
                    {key: 'driggs_address_species_id', label: 'driggs_address_species_id', type: 'number'},
                    {key: 'driggs_address_species_note', label: 'driggs_address_species_note', type: 'textarea'},
                    {key: 'driggs_address_full', label: 'driggs_address_full', type: 'textarea'},
                    {key: 'driggs_street_1', label: 'driggs_street_1', type: 'text'},
                    {key: 'driggs_street_2', label: 'driggs_street_2', type: 'text'},
                    {key: 'driggs_city', label: 'driggs_city', type: 'text'},
                    {key: 'driggs_state_code', label: 'driggs_state_code', type: 'text'},
                    {key: 'driggs_zip', label: 'driggs_zip', type: 'text'},
                    {key: 'driggs_state_full', label: 'driggs_state_full', type: 'text'},
                    {key: 'driggs_country', label: 'driggs_country', type: 'text'},
                    
                    // backlinks section
                    {key: 'backlinks_section_separator', label: 'backlinks section', type: 'separator'},
                    {key: 'driggs_cgig_id', label: 'driggs_cgig_id', type: 'number'},
                    {key: 'driggs_citations_done', label: 'driggs_citations_done', type: 'boolean'},
                    {key: 'driggs_social_profiles_done', label: 'driggs_social_profiles_done', type: 'boolean'},
                    
                    // basics section
                    {key: 'basics_section_separator', label: 'basics section', type: 'separator'},
                    {key: 'driggs_industry', label: 'driggs_industry', type: 'text'},
                    {key: 'driggs_keywords', label: 'driggs_keywords', type: 'textarea'},
                    {key: 'driggs_category', label: 'driggs_category', type: 'text'},
                    {key: 'driggs_site_type_purpose', label: 'driggs_site_type_purpose', type: 'text'},
                    {key: 'driggs_email_1', label: 'driggs_email_1', type: 'email'},
                    {key: 'driggs_hours', label: 'driggs_hours', type: 'textarea'},
                    {key: 'driggs_owner_name', label: 'driggs_owner_name', type: 'text'},
                    {key: 'driggs_short_descr', label: 'driggs_short_descr', type: 'textarea'},
                    {key: 'driggs_long_descr', label: 'driggs_long_descr', type: 'textarea'},
                    {key: 'driggs_year_opened', label: 'driggs_year_opened', type: 'number'},
                    {key: 'driggs_employees_qty', label: 'driggs_employees_qty', type: 'number'},
                    {key: 'driggs_payment_methods', label: 'driggs_payment_methods', type: 'textarea'},
                    {key: 'driggs_special_note_for_ai_tool', label: 'driggs_special_note_for_ai_tool', type: 'textarea'},
                    {key: 'driggs_social_media_links', label: 'driggs_social_media_links', type: 'textarea'},
                    {key: 'driggs_logo_url', label: 'driggs_logo_url', type: 'logo_url'},
                    
                    // misc section
                    {key: 'misc_section_separator', label: 'misc section', type: 'separator'},
                    {key: 'updated_at', label: 'updated_at', type: 'datetime'},
                    {key: 'fk_users_id', label: 'fk_users_id', type: 'text'},
                    {key: 'true_root_domain', label: 'true_root_domain', type: 'text'},
                    {key: 'full_subdomain', label: 'full_subdomain', type: 'text'},
                    {key: 'webproperty_type', label: 'webproperty_type', type: 'text'},
                    {key: 'ns_full', label: 'ns_full', type: 'text'},
                    {key: 'ip_address', label: 'ip_address', type: 'text'},
                    {key: 'is_wp_site', label: 'is_wp_site', type: 'boolean'},
                    {key: 'wpuser1', label: 'wpuser1', type: 'text'},
                    {key: 'wppass1', label: 'wppass1', type: 'password'},
                    {key: 'wp_plugin_installed1', label: 'wp_plugin_installed1', type: 'boolean'},
                    {key: 'wp_plugin_connected2', label: 'wp_plugin_connected2', type: 'boolean'},
                    {key: 'wp_rest_app_pass', label: 'wp_rest_app_pass', type: 'textarea'},
                    {key: 'fk_domreg_hostaccount', label: 'fk_domreg_hostaccount', type: 'text'},
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
                    {key: 'is_flylocal', label: 'is_flylocal', type: 'boolean'},
                    
                    // Additional Grove-specific fields not in /drom
                    {key: 'snailimage', label: 'snailimage', type: 'text'},
                    {key: 'snail_image_url', label: 'snail_image_url', type: 'text'},
                    {key: 'snail_image_status', label: 'snail_image_status', type: 'text'},
                    {key: 'snail_image_error', label: 'snail_image_error', type: 'textarea'},
                    {key: 'screenshot_url', label: 'screenshot_url', type: 'text'},
                    {key: 'screenshot_taken_at', label: 'screenshot_taken_at', type: 'datetime'},
                    {key: 'screenshot_status', label: 'screenshot_status', type: 'text'},
                    {key: 'rel_cncglub_id', label: 'rel_cncglub_id', type: 'number'},
                    {key: 'rel_city_id', label: 'rel_city_id', type: 'number'},
                    {key: 'rel_industry_id', label: 'rel_industry_id', type: 'number'}
                ];
                
                fields.forEach(function(field) {
                    // Handle separator rows differently
                    if (field.type === 'separator') {
                        let separatorTr = $('<tr style="background-color: #333; color: white;"></tr>');
                        
                        // Empty checkbox cell
                        let separatorCheckboxTd = $('<td style="padding: 8px; border: 1px solid #ddd; text-align: center; background-color: #333;"></td>');
                        separatorTr.append(separatorCheckboxTd);
                        
                        // Separator label spanning remaining columns
                        let separatorLabelTd = $('<td colspan="5" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: center; background-color: #333; color: white; font-size: 14px;"></td>');
                        separatorLabelTd.text(field.label);
                        separatorTr.append(separatorLabelTd);
                        
                        tbody.append(separatorTr);
                        return; // Skip normal row processing for separators
                    }
                    
                    let tr = $('<tr style="cursor: pointer;"></tr>');
                    
                    // Apply special background color to specific rows
                    const specialBgFields = ['wppma_id', 'wppma_db_only_created_at', 'wppma_db_only_updated_at', 'id', 'created_at'];
                    const isSpecialBg = specialBgFields.includes(field.key);
                    
                    if (isSpecialBg) {
                        tr.css('background-color', '#d5d5d5');
                        tr.hover(function() {
                            $(this).css('background-color', '#c5c5c5');
                        }, function() {
                            $(this).css('background-color', '#d5d5d5');
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
                    if (isSpecialBg) checkboxTd.css('background-color', '#d5d5d5');
                    let checkbox = $('<input type="checkbox" style="width: 20px; height: 20px;" data-field="' + field.key + '">');
                    checkboxTd.append(checkbox);
                    tr.append(checkboxTd);
                    
                    // Field name column - bold DB field names
                    let fieldNameTd = $('<td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #23282d; font-family: monospace;"></td>');
                    if (isSpecialBg) fieldNameTd.css('background-color', '#d5d5d5');
                    fieldNameTd.text(field.label);
                    tr.append(fieldNameTd);
                    
                    // Value column
                    let valueTd = $('<td style="padding: 8px; border: 1px solid #ddd;"></td>');
                    if (isSpecialBg) valueTd.css('background-color', '#d5d5d5');
                    // Handle null values properly for different field types
                    let value = currentData[field.key];
                    if (value === null || value === undefined) {
                        // For number fields, use empty string (will display as blank but work correctly)
                        // For other fields, use empty string as well
                        value = '';
                    }
                    
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
                        // Ensure special background color is preserved
                        if (isSpecialBg) {
                            valueTd.css('background-color', '#d5d5d5');
                        }
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
                        if (field.type === 'textarea') {
                            // For textarea fields, preserve linebreaks by using a div with pre-wrap
                            let contentDiv = $('<div style="white-space: pre-wrap; word-wrap: break-word; max-width: 300px;"></div>');
                            contentDiv.text(value); // Use .text() to safely escape HTML but preserve linebreaks with CSS
                            valueTd.append(contentDiv);
                        } else {
                            // For other fields, use normal text display
                            valueTd.text(value);
                        }
                        valueTd.attr('data-field', field.key);
                        valueTd.attr('data-type', field.type);
                        valueTd.css('cursor', 'text');
                        // Use closure to capture current value properly
                        valueTd.click(function(capturedValue, capturedKey, capturedType) {
                            return function() {
                                // Debug logging for number fields
                                if (capturedType === 'number') {
                                    console.log('Number field clicked:', {
                                        fieldKey: capturedKey,
                                        capturedValue: capturedValue,
                                        actualCurrentDataValue: currentData[capturedKey]
                                    });
                                }
                                startInlineEdit($(this), capturedValue, capturedKey, capturedType);
                            };
                        }(value, field.key, field.type));
                    }
                    
                    tr.append(valueTd);
                    
                    // Add new shortcode 1 column
                    let shortcodeTd = $('<td style="padding: 8px; border: 1px solid #ddd; position: relative; white-space: nowrap;"></td>');
                    if (isSpecialBg) shortcodeTd.css('background-color', '#d5d5d5');
                    
                    // Create copy button (now on the left)
                    let copyBtn = $('<button style="padding: 4px 8px; background: #f0f0f0; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; font-size: 12px; color: #333; transition: background-color 0.2s; margin-right: 8px;">Copy</button>');
                    
                    // Add hover effect with yellow background
                    copyBtn.hover(
                        function() { $(this).css('background-color', '#ffeb3b'); },
                        function() { $(this).css('background-color', '#f0f0f0'); }
                    );
                    
                    // Create shortcode text (now on the right)
                    let shortcodeText = '[sitespren dbcol="' + field.key + '"]';
                    let shortcodeSpan = $('<span style="font-family: monospace; font-size: 12px; color: #333;"></span>');
                    shortcodeSpan.text(shortcodeText);
                    
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
                    
                    shortcodeTd.append(copyBtn);
                    shortcodeTd.append(shortcodeSpan);
                    tr.append(shortcodeTd);
                    
                    // Add stuff3 column with roaring_div as shortcode copy button
                    let stuff3Td = $('<td class="stuff3-cell" style="padding: 0; border: 1px solid #ddd; width: 20px;"></td>');
                    if (isSpecialBg) stuff3Td.css('background-color', '#d5d5d5');
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
                    
                    // Add new lighthouse friendly name column
                    let friendlyNameTd = $('<td style="padding: 8px; border: 1px solid #ddd; text-align: left;"></td>');
                    if (isSpecialBg) friendlyNameTd.css('background-color', '#d5d5d5');
                    
                    // Placeholder for friendly name - will be populated later
                    friendlyNameTd.text('');
                    tr.append(friendlyNameTd);
                    
                    tbody.append(tr);
                });
                
                // After table is built, populate friendly names
                populateFriendlyNames();
            }
            
            function populateFriendlyNames() {
                // Get all field keys from the table
                let fieldKeys = [];
                $('#table-body tr').each(function() {
                    let row = $(this);
                    if (!row.find('td[colspan]').length) { // Skip separator rows
                        let fieldNameCell = row.find('td:nth-child(2)'); // Field name is 2nd column
                        if (fieldNameCell.length) {
                            let fieldName = fieldNameCell.text().trim();
                            if (fieldName && !fieldName.includes('section')) {
                                fieldKeys.push({
                                    element: row.find('td:last-child'), // Last column is friendly name
                                    fieldName: fieldName
                                });
                            }
                        }
                    }
                });
                
                // Make single AJAX call to get all friendly names
                if (fieldKeys.length > 0) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_get_all_friendly_names',
                            nonce: '<?php echo wp_create_nonce('grove_export_nonce'); ?>',
                            field_names: fieldKeys.map(fk => fk.fieldName)
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                fieldKeys.forEach(function(fieldKey, index) {
                                    let friendlyName = response.data[fieldKey.fieldName] || '';
                                    fieldKey.element.text(friendlyName);
                                });
                            }
                        },
                        error: function() {
                            console.log('Failed to load friendly names');
                        }
                    });
                }
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
                // Debug logging for number fields
                if (fieldType === 'number') {
                    console.log('startInlineEdit called for number field:', {
                        fieldKey: fieldKey,
                        currentValue: currentValue,
                        fieldType: fieldType,
                        cellText: cell.text()
                    });
                }
                
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
                
                // Position cursor at end of text instead of selecting all
                // Note: Number inputs don't support text selection APIs
                if (fieldType !== 'number') {
                    try {
                        if (input[0].setSelectionRange) {
                            let len = currentValue ? currentValue.length : 0;
                            input[0].setSelectionRange(len, len);
                        } else if (input[0].createTextRange) {
                            let range = input[0].createTextRange();
                            range.collapse(true);
                            range.moveEnd('character', currentValue ? currentValue.length : 0);
                            range.moveStart('character', currentValue ? currentValue.length : 0);
                            range.select();
                        }
                    } catch (e) {
                        // Ignore selection errors for input types that don't support it
                        console.log('Selection not supported for this input type:', fieldType);
                    }
                }
                
                // Save on blur or enter
                function saveEdit() {
                    let newValue = input.val();
                    cell.removeClass('editing');
                    
                    // Normalize values for comparison (handle null/empty cases)
                    let normalizedCurrentValue = currentValue === null || currentValue === undefined || currentValue === '' ? '' : String(currentValue);
                    let normalizedNewValue = newValue === null || newValue === undefined ? '' : String(newValue);
                    
                    // Always save the value, including empty strings (which become null)
                    // Only skip if user didn't actually change anything
                    if (normalizedNewValue !== normalizedCurrentValue) {
                        // Convert empty string to null for database storage
                        let valueToSave = newValue.trim() === '' ? '' : newValue;
                        updateField(fieldKey, valueToSave);
                        
                        // Display empty cells as empty, not "null" text
                        let displayValue = newValue.trim() === '' ? '' : newValue;
                        if (fieldType === 'textarea') {
                            // For textarea fields, update the content div to preserve linebreaks
                            let contentDiv = cell.find('div');
                            if (contentDiv.length > 0) {
                                contentDiv.text(displayValue);
                            } else {
                                // Fallback: create new content div if not found
                                cell.empty();
                                let newContentDiv = $('<div style="white-space: pre-wrap; word-wrap: break-word; max-width: 300px;"></div>');
                                newContentDiv.text(displayValue);
                                cell.append(newContentDiv);
                            }
                        } else {
                            // For other fields, use normal text display
                            cell.text(displayValue);
                        }
                        currentData[fieldKey] = newValue.trim() === '' ? null : newValue;
                        hasChanges = true;
                        
                        // Debug logging for number fields
                        if (fieldType === 'number') {
                            console.log('Number field saved:', {
                                fieldKey: fieldKey,
                                currentValue: currentValue,
                                newValue: newValue,
                                normalizedCurrentValue: normalizedCurrentValue,
                                normalizedNewValue: normalizedNewValue,
                                valueToSave: valueToSave
                            });
                        }
                    } else {
                        // No changes, restore original content
                        if (fieldType === 'textarea') {
                            // For textarea fields, restore the content div
                            let contentDiv = cell.find('div');
                            if (contentDiv.length > 0) {
                                contentDiv.text(currentValue || '');
                            } else {
                                // Fallback: create new content div
                                cell.empty();
                                let newContentDiv = $('<div style="white-space: pre-wrap; word-wrap: break-word; max-width: 300px;"></div>');
                                newContentDiv.text(currentValue || '');
                                cell.append(newContentDiv);
                            }
                        } else {
                            cell.text(originalText);
                        }
                        
                        // Debug logging when no save occurs
                        if (fieldType === 'number') {
                            console.log('Number field NOT saved (no change):', {
                                fieldKey: fieldKey,
                                currentValue: currentValue,
                                newValue: newValue,
                                normalizedCurrentValue: normalizedCurrentValue,
                                normalizedNewValue: normalizedNewValue
                            });
                        }
                    }
                }
                
                // Cancel on escape
                function cancelEdit() {
                    cell.removeClass('editing');
                    
                    // Restore original content based on field type
                    if (fieldType === 'textarea') {
                        // For textarea fields, restore the content div
                        cell.empty();
                        let contentDiv = $('<div style="white-space: pre-wrap; word-wrap: break-word; max-width: 300px;"></div>');
                        contentDiv.text(currentValue || '');
                        cell.append(contentDiv);
                    } else {
                        cell.text(originalText);
                    }
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
            
            // Export button handlers
            $('#export-sharkintax-btn').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_export_sharkintax',
                        nonce: '<?php echo wp_create_nonce('grove_export_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Copy to clipboard
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(response.data).then(function() {
                                    alert('Sharkintax data copied to clipboard!');
                                }).catch(function(err) {
                                    console.error('Failed to copy:', err);
                                    fallbackCopy(response.data, 'Sharkintax');
                                });
                            } else {
                                fallbackCopy(response.data, 'Sharkintax');
                            }
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Export failed. Please try again.');
                    }
                });
            });
            
            $('#export-walrustax-btn').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_export_walrustax',
                        nonce: '<?php echo wp_create_nonce('grove_export_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Copy to clipboard
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(response.data).then(function() {
                                    alert('Walrustax data copied to clipboard!');
                                }).catch(function(err) {
                                    console.error('Failed to copy:', err);
                                    fallbackCopy(response.data, 'Walrustax');
                                });
                            } else {
                                fallbackCopy(response.data, 'Walrustax');
                            }
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Export failed. Please try again.');
                    }
                });
            });
            
            $('#export-csv-btn').on('click', function() {
                // Create form and submit for file download
                let form = $('<form>', {
                    action: ajaxurl,
                    method: 'POST'
                });
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: 'grove_export_csv'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'nonce',
                    value: '<?php echo wp_create_nonce('grove_export_nonce'); ?>'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'download',
                    value: '1'
                }));
                $('body').append(form);
                form.submit();
                form.remove();
            });
            
            $('#export-xls-btn').on('click', function() {
                // Create form and submit for file download
                let form = $('<form>', {
                    action: ajaxurl,
                    method: 'POST'
                });
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: 'grove_export_xls'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'nonce',
                    value: '<?php echo wp_create_nonce('grove_export_nonce'); ?>'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'download',
                    value: '1'
                }));
                $('body').append(form);
                form.submit();
                form.remove();
            });
            
            $('#export-sql-btn').on('click', function() {
                // Create form and submit for file download
                let form = $('<form>', {
                    action: ajaxurl,
                    method: 'POST'
                });
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: 'grove_export_sql'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'nonce',
                    value: '<?php echo wp_create_nonce('grove_export_nonce'); ?>'
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'download',
                    value: '1'
                }));
                $('body').append(form);
                form.submit();
                form.remove();
            });
            
            // Nova Beluga dropdown handlers
            $('#nova-beluga-btn').on('click', function(e) {
                e.stopPropagation();
                $('#nova-beluga-dropdown').toggle();
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function() {
                $('#nova-beluga-dropdown').hide();
            });
            
            // Prevent dropdown from closing when clicking inside it
            $('#nova-beluga-dropdown').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Handle option selection
            $('.nova-option').on('click', function() {
                const action = $(this).data('action');
                $('#nova-beluga-dropdown').hide();
                
                if (action === 'both') {
                    // Copy with both field name sets
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_export_nova_beluga_both',
                            nonce: '<?php echo wp_create_nonce('grove_export_nonce'); ?>',
                            omit_no_friendly: $('#omit-no-friendly').is(':checked'),
                            use_custom_position: $('#use-custom-position').is(':checked')
                        },
                        success: function(response) {
                            if (response.success) {
                                // Copy to clipboard
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(response.data).then(function() {
                                        alert('Nova Beluga data (both field sets) copied to clipboard!');
                                    }).catch(function(err) {
                                        console.error('Failed to copy:', err);
                                        fallbackCopy(response.data, 'Nova Beluga Both');
                                    });
                                } else {
                                    fallbackCopy(response.data, 'Nova Beluga Both');
                                }
                            } else {
                                alert('Error: ' + response.data);
                            }
                        },
                        error: function() {
                            alert('AJAX error occurred');
                        }
                    });
                } else if (action === 'friendly') {
                    // Copy with friendly_name_1_datum only
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_export_nova_beluga_friendly',
                            nonce: '<?php echo wp_create_nonce('grove_export_nonce'); ?>',
                            omit_no_friendly: $('#omit-no-friendly').is(':checked'),
                            use_custom_position: $('#use-custom-position').is(':checked')
                        },
                        success: function(response) {
                            if (response.success) {
                                // Copy to clipboard
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(response.data).then(function() {
                                        alert('Nova Beluga data (friendly names only) copied to clipboard!');
                                    }).catch(function(err) {
                                        console.error('Failed to copy:', err);
                                        fallbackCopy(response.data, 'Nova Beluga Friendly');
                                    });
                                } else {
                                    fallbackCopy(response.data, 'Nova Beluga Friendly');
                                }
                            } else {
                                alert('Error: ' + response.data);
                            }
                        },
                        error: function() {
                            alert('AJAX error occurred');
                        }
                    });
                }
            });
            
            // Add hover effects to dropdown options
            $('.nova-option').on('mouseenter', function() {
                $(this).css('background-color', '#f0f0f0');
            }).on('mouseleave', function() {
                $(this).css('background-color', '');
            });
            
            // Fallback copy function for older browsers
            function fallbackCopy(text, type) {
                let tempInput = $('<textarea>');
                $('body').append(tempInput);
                tempInput.val(text).select();
                document.execCommand('copy');
                tempInput.remove();
                alert(type + ' data copied to clipboard!');
            }
            
            // Select all checkbox
            $('#select-all').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('#table-body input[type="checkbox"]').prop('checked', isChecked);
            });
            
            // WordPress native settings functionality
            var groveMediaFrame;
            
            // Site Icon chooser
            $('#grove-choose-site-icon').on('click', function(e) {
                e.preventDefault();
                
                // If the media frame already exists, reopen it
                if (groveMediaFrame) {
                    groveMediaFrame.open();
                    return;
                }
                
                // Create the media frame
                groveMediaFrame = wp.media({
                    title: 'Choose Site Icon',
                    button: {
                        text: 'Use as Site Icon'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });
                
                // When an image is selected, run a callback
                groveMediaFrame.on('select', function() {
                    var attachment = groveMediaFrame.state().get('selection').first().toJSON();
                    
                    // Set the hidden field value
                    $('#grove_site_icon').val(attachment.id);
                    
                    // Update the preview image
                    $('#grove-site-icon-img').attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
                    $('#grove-site-icon-img').css({
                        'width': '24px',
                        'height': '24px',
                        'display': 'inline-block',
                        'border-radius': '2px'
                    });
                    
                    // Update button text and show remove button
                    $('#grove-choose-site-icon').text('Change Site Icon');
                    $('#grove-remove-site-icon').show();
                });
                
                // Open the modal
                groveMediaFrame.open();
            });
            
            // Remove site icon
            $('#grove-remove-site-icon').on('click', function(e) {
                e.preventDefault();
                
                // Clear the hidden field
                $('#grove_site_icon').val('');
                
                // Hide the preview image
                $('#grove-site-icon-img').hide();
                
                // Update button text and hide remove button
                $('#grove-choose-site-icon').text('Select Site Icon');
                $(this).hide();
            });
            
            // Save settings
            $('#grove-save-settings').on('click', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var originalText = button.text();
                button.text('Saving...').prop('disabled', true);
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_update_site_settings',
                        nonce: '<?php echo wp_create_nonce('grove_site_settings_nonce'); ?>',
                        blogname: $('#grove_blogname').val(),
                        blogdescription: $('#grove_blogdescription').val(),
                        site_icon: $('#grove_site_icon').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text('Saved!').css({
                                'background-color': '#46b450',
                                'border-color': '#46b450',
                                'color': '#fff'
                            });
                            
                            setTimeout(function() {
                                button.text(originalText).css({
                                    'background-color': '',
                                    'border-color': '',
                                    'color': ''
                                }).prop('disabled', false);
                            }, 2000);
                        } else {
                            alert('Error saving settings: ' + response.data);
                            button.text(originalText).prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error saving settings. Please try again.');
                        button.text(originalText).prop('disabled', false);
                    }
                });
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
        
        // Sanitize phone number fields - remove all non-numeric characters
        if ($field === 'driggs_phone_1') {
            $value = preg_replace('/[^0-9]/', '', $value);
        }
        
        // Validate field name - all editable fields from zen_sitespren table (matching /drom order)
        $allowed_fields = [
            // Core site fields
            'created_at', 'sitespren_base', 'true_root_domain', 'full_subdomain', 'webproperty_type',
            'fk_users_id', 'updated_at',
            
            // WordPress fields
            'wpuser1', 'wppass1', 'wp_plugin_installed1', 'wp_plugin_connected2',
            'fk_domreg_hostaccount', 'is_wp_site', 'wp_rest_app_pass',
            
            // Business information
            'driggs_industry', 'driggs_city', 'driggs_brand_name', 'driggs_site_type_purpose', 'driggs_email_1',
            
            // Address fields
            'driggs_address_full', 'driggs_address_species_id', 'driggs_street_1', 'driggs_street_2',
            'driggs_state_code', 'driggs_zip', 'driggs_state_full', 'driggs_country', 'driggs_address_species_note',
            
            // Contact fields
            'driggs_phone_country_code', 'driggs_phone_1', 'driggs_phone1_platform_id',
            
            // Business details (some may need DB columns added)
            'driggs_hours', 'driggs_owner_name', 'driggs_short_descr', 'driggs_long_descr', 
            'driggs_year_opened', 'driggs_employees_qty', 'driggs_keywords', 'driggs_category',
            'driggs_payment_methods', 'driggs_social_media_links',
            
            // Project management
            'driggs_cgig_id', 'driggs_citations_done', 'driggs_social_profiles_done', 'driggs_special_note_for_ai_tool',
            'driggs_revenue_goal', 'driggs_logo_url',
            
            // Technical fields
            'ns_full', 'ip_address', 'is_starred1', 'icon_name', 'icon_color',
            
            // Classification flags
            'is_bulldozer', 'is_competitor', 'is_external', 'is_internal', 'is_ppx', 'is_ms',
            'is_wayback_rebuild', 'is_naked_wp_build', 'is_rnr', 'is_aff', 'is_other1', 'is_other2', 'is_flylocal',
            
            // Media management (existing in DB)
            'snailimage', 'snail_image_url', 'snail_image_status', 'snail_image_error',
            'screenshot_url', 'screenshot_taken_at', 'screenshot_status',
            
            // Relationships (some may need DB columns added)
            'rel_cncglub_id', 'rel_city_id', 'rel_industry_id'
        ];
        
        if (!in_array($field, $allowed_fields)) {
            wp_send_json_error('Invalid field name');
            return;
        }
        
        // Sanitize value based on field type (updated for all new fields)
        $number_fields = ['driggs_phone_country_code', 'driggs_phone1_platform_id', 'driggs_cgig_id', 'driggs_revenue_goal', 'driggs_address_species_id', 
                         'driggs_year_opened', 'driggs_employees_qty', 'rel_cncglub_id', 'rel_city_id', 'rel_industry_id'];
        $boolean_fields = ['wp_plugin_installed1', 'wp_plugin_connected2', 'is_wp_site', 'is_bulldozer', 'driggs_citations_done', 
                          'driggs_social_profiles_done', 'is_competitor', 'is_external', 'is_internal', 'is_ppx', 'is_ms', 
                          'is_wayback_rebuild', 'is_naked_wp_build', 'is_rnr', 'is_aff', 'is_other1', 'is_other2', 'is_flylocal'];
        $email_fields = ['driggs_email_1'];
        $datetime_fields = ['created_at', 'updated_at', 'screenshot_taken_at'];
        
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
        
        // Determine the correct format for the field value
        $data_format = '%s'; // Default to string
        if (in_array($field, $number_fields)) {
            $data_format = '%d';
        } elseif (in_array($field, $boolean_fields)) {
            $data_format = '%d';
        }
        
        try {
            // Update the single record (should only be one record per site)
            $result = $wpdb->update(
                $table_name,
                array($field => $value),
                array('wppma_id' => 1), // Update the first/only record
                array($data_format), // Correct format for the data
                array('%d') // Format for the WHERE clause
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
     * AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
     * Based on proven Snefuruplin implementation
     */
    private function suppress_all_admin_notices() {
        // Remove all admin notices immediately
        add_action('admin_print_styles', function() {
            // Remove all notice actions
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            
            // Remove user admin notices
            global $wp_filter;
            if (isset($wp_filter['user_admin_notices'])) {
                unset($wp_filter['user_admin_notices']);
            }
        }, 0);
        
        // Additional cleanup for persistent notices
        add_action('admin_head', function() {
            // Hide any notices that slip through via CSS
            echo '<style type="text/css">
                .notice, .notice-warning, .notice-error, .notice-success, .notice-info,
                .updated, .error, .update-nag, .admin-notice,
                div.notice, div.updated, div.error, div.update-nag,
                .wrap > .notice, .wrap > .updated, .wrap > .error,
                #adminmenu + .notice, #adminmenu + .updated, #adminmenu + .error,
                .update-php, .php-update-nag,
                .plugin-update-tr, .theme-update-message,
                .update-message, .updating-message,
                #update-nag, #deprecation-warning {
                    display: none !important;
                }
                
                /* Hide WordPress core update notices */
                .update-core-php, .notice-alt {
                    display: none !important;
                }
                
                /* Hide plugin activation/deactivation notices */
                .activated, .deactivated {
                    display: none !important;
                }
                
                /* Hide file permission and other system warnings */
                .notice-warning, .notice-error {
                    display: none !important;
                }
                
                /* Hide any remaining notices in common locations */
                .wrap .notice:first-child,
                .wrap > div.notice,
                .wrap > div.updated,
                .wrap > div.error {
                    display: none !important;
                }
                
                /* Nuclear option - hide anything that looks like a notice */
                [class*="notice"], [class*="updated"], [class*="error"],
                [id*="notice"], [id*="message"] {
                    display: none !important;
                }
                
                /* Restore our legitimate content */
                .grove-content, .grove-content * {
                    display: block !important;
                }
            </style>';
        });
        
        // Nuclear option - JavaScript cleanup for dynamic notices
        add_action('admin_footer', function() {
            echo '<script type="text/javascript">
                (function($) {
                    $(document).ready(function() {
                        // Remove any notices that appear after page load
                        $(".notice, .updated, .error, .update-nag, .admin-notice").remove();
                        
                        // Set up mutation observer to catch dynamically added notices
                        if (window.MutationObserver) {
                            var observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    if (mutation.addedNodes.length > 0) {
                                        $(mutation.addedNodes).find(".notice, .updated, .error, .update-nag").remove();
                                        $(mutation.addedNodes).filter(".notice, .updated, .error, .update-nag").remove();
                                    }
                                });
                            });
                            
                            observer.observe(document.body, {
                                childList: true,
                                subtree: true
                            });
                        }
                        
                        // Periodic cleanup every 500ms
                        setInterval(function() {
                            $(".notice, .updated, .error, .update-nag, .admin-notice").not(".grove-content, .grove-content *").remove();
                        }, 500);
                    });
                })(jQuery);
            </script>';
        });
    }
    
    /**
     * Export data in sharkintax format
     */
    public function grove_export_sharkintax() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_send_json_error('No data found');
        }
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate sharkintax format
        $output = Grove_Tax_Exports::generate_sharkintax($sitespren_data, array_column($fields, 'key'));
        
        wp_send_json_success($output);
    }
    
    /**
     * Export data in walrustax format
     */
    public function grove_export_walrustax() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_send_json_error('No data found');
        }
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate walrustax format
        $output = Grove_Tax_Exports::generate_walrustax($sitespren_data, array_column($fields, 'key'));
        
        wp_send_json_success($output);
    }
    
    /**
     * Export data with Nova Beluga format (both field name sets)
     */
    public function grove_export_nova_beluga_both() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_send_json_error('No data found');
        }
        
        // Get checkbox options
        $omit_no_friendly = isset($_POST['omit_no_friendly']) && $_POST['omit_no_friendly'] === 'true';
        $use_custom_position = isset($_POST['use_custom_position']) && $_POST['use_custom_position'] === 'true';
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate nova beluga format with both field sets
        $output = Grove_Tax_Exports::generate_nova_beluga_both($sitespren_data, array_column($fields, 'key'), $omit_no_friendly, $use_custom_position);
        
        wp_send_json_success($output);
    }
    
    /**
     * Export data with Nova Beluga format (friendly names only)
     */
    public function grove_export_nova_beluga_friendly() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_send_json_error('No data found');
        }
        
        // Get checkbox options
        $omit_no_friendly = isset($_POST['omit_no_friendly']) && $_POST['omit_no_friendly'] === 'true';
        $use_custom_position = isset($_POST['use_custom_position']) && $_POST['use_custom_position'] === 'true';
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate nova beluga format with friendly names only
        $output = Grove_Tax_Exports::generate_nova_beluga_friendly($sitespren_data, array_column($fields, 'key'), $omit_no_friendly, $use_custom_position);
        
        wp_send_json_success($output);
    }
    
    /**
     * Get friendly name from lighthouse table
     */
    public function grove_get_friendly_name() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $table_name = sanitize_text_field($_POST['table_name'] ?? '');
        $field_name = sanitize_text_field($_POST['field_name'] ?? '');
        
        if (empty($table_name) || empty($field_name)) {
            wp_send_json_error('Missing parameters');
        }
        
        // Get friendly name from database
        $friendly_name = Grove_Database::get_friendly_name($table_name, $field_name);
        
        wp_send_json_success($friendly_name);
    }
    
    /**
     * Get all friendly names from lighthouse table
     */
    public function grove_get_all_friendly_names() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $field_names = $_POST['field_names'] ?? array();
        
        if (!is_array($field_names) || empty($field_names)) {
            wp_send_json_error('Missing field names');
        }
        
        $friendly_names = array();
        $table_name = 'sitespren';
        
        foreach ($field_names as $field_name) {
            $field_name = sanitize_text_field($field_name);
            $friendly_name = Grove_Database::get_friendly_name($table_name, $field_name);
            $friendly_names[$field_name] = $friendly_name;
        }
        
        wp_send_json_success($friendly_names);
    }
    
    /**
     * Export data as CSV file
     */
    public function grove_export_csv() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_die('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_die('No data found');
        }
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate CSV with field name and value columns
        $output = "Field Name,Value\n";
        foreach ($fields as $field) {
            $field_name = $field['key'];
            $value = isset($sitespren_data[$field_name]) ? $sitespren_data[$field_name] : '';
            // Escape values for CSV
            $field_name = '"' . str_replace('"', '""', $field_name) . '"';
            $value = '"' . str_replace('"', '""', $value) . '"';
            $output .= $field_name . ',' . $value . "\n";
        }
        
        // Generate filename with new convention
        $filename = $this->generate_export_filename('csv');
        
        // Send file download headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $output;
        exit;
    }
    
    /**
     * Export data as XLS file
     */
    public function grove_export_xls() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_die('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_die('No data found');
        }
        
        // Get field order from the UI (same as displayed in table)
        $fields = $this->get_driggs_field_order();
        
        // Generate Excel XML with field name and value columns
        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="header">' . "\n";
        $xml .= '<Font ss:Bold="1"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        
        $xml .= '<Worksheet ss:Name="Sitespren Data">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Header row
        $xml .= '<Row ss:StyleID="header">' . "\n";
        $xml .= '<Cell><Data ss:Type="String">Field Name</Data></Cell>' . "\n";
        $xml .= '<Cell><Data ss:Type="String">Value</Data></Cell>' . "\n";
        $xml .= '</Row>' . "\n";
        
        // Data rows - one row per field
        foreach ($fields as $field) {
            $field_name = $field['key'];
            $value = isset($sitespren_data[$field_name]) ? $sitespren_data[$field_name] : '';
            
            $xml .= '<Row>' . "\n";
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($field_name) . '</Data></Cell>' . "\n";
            
            // Determine data type for value
            if (is_numeric($value) && !preg_match('/^0[0-9]/', $value)) {
                $xml .= '<Cell><Data ss:Type="Number">' . $value . '</Data></Cell>' . "\n";
            } else {
                $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
        }
        
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>' . "\n";
        
        // Generate filename with new convention
        $filename = $this->generate_export_filename('xls');
        
        // Send file download headers
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $xml;
        exit;
    }
    
    /**
     * Export data as SQL file
     */
    public function grove_export_sql() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_export_nonce')) {
            wp_die('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        // Get the sitespren data
        $sitespren_data = $wpdb->get_row("SELECT * FROM $table_name WHERE wppma_id = 1", ARRAY_A);
        
        if (!$sitespren_data) {
            wp_die('No data found');
        }
        
        // Generate SQL INSERT statement
        $output = Grove_Tax_Exports::generate_sql($sitespren_data, $table_name);
        
        // Add some helpful comments
        $sql = "-- Sitespren Data Export\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Site: " . get_site_url() . "\n\n";
        $sql .= $output;
        
        // Generate filename with new convention
        $filename = $this->generate_export_filename('sql');
        
        // Send file download headers
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $sql;
        exit;
    }
    
    /**
     * Generate export filename with proper convention
     * Format: grove_export_(MM-DD-YY-TIME)_(domain_with_underscores)_1.ext
     */
    private function generate_export_filename($extension) {
        // Get the current site URL
        $site_url = get_site_url();
        
        // Parse the URL to get the domain
        $parsed_url = parse_url($site_url);
        $domain = isset($parsed_url['host']) ? $parsed_url['host'] : 'unknown';
        
        // Remove www. prefix if present
        $domain = preg_replace('/^www\./i', '', $domain);
        
        // Replace periods with underscores in the domain
        $domain_formatted = str_replace('.', '_', $domain);
        
        // Generate date/time string in MM-DD-YY-TIME format
        // Using 24-hour format for time (HHMM)
        $datetime = date('m-d-y-Hi');
        
        // Construct the filename
        $filename = 'grove_export_' . $datetime . '_' . $domain_formatted . '_1.' . $extension;
        
        return $filename;
    }
    
    /**
     * AJAX handler to update WordPress native site settings
     */
    public function grove_update_site_settings() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_site_settings_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $blogname = sanitize_text_field($_POST['blogname']);
        $blogdescription = sanitize_text_field($_POST['blogdescription']);
        $site_icon = absint($_POST['site_icon']);
        
        // Update WordPress options
        $updated = array();
        
        if (update_option('blogname', $blogname)) {
            $updated[] = 'Site Title';
        }
        
        if (update_option('blogdescription', $blogdescription)) {
            $updated[] = 'Tagline';
        }
        
        // Handle site icon
        if ($site_icon) {
            // Verify the attachment exists and is an image
            $attachment = get_post($site_icon);
            if ($attachment && wp_attachment_is_image($site_icon)) {
                if (update_option('site_icon', $site_icon)) {
                    $updated[] = 'Site Icon';
                }
            } else {
                wp_send_json_error('Invalid site icon');
            }
        } else {
            // Remove site icon if empty
            if (delete_option('site_icon')) {
                $updated[] = 'Site Icon (removed)';
            }
        }
        
        if (!empty($updated)) {
            wp_send_json_success('Settings updated: ' . implode(', ', $updated));
        } else {
            wp_send_json_success('No changes made');
        }
    }
    
    /**
     * Get the field order as displayed in the UI
     */
    private function get_driggs_field_order() {
        $fields = array(
            array('key' => 'wppma_id', 'label' => 'wppma_id', 'type' => 'number'),
            array('key' => 'wppma_db_only_created_at', 'label' => 'wppma_db_only_created_at', 'type' => 'datetime'),
            array('key' => 'wppma_db_only_updated_at', 'label' => 'wppma_db_only_updated_at', 'type' => 'datetime'),
            array('key' => 'id', 'label' => 'id', 'type' => 'text'),
            array('key' => 'created_at', 'label' => 'created_at', 'type' => 'datetime'),
            array('key' => 'sitespren_base', 'label' => 'sitespren_base', 'type' => 'text'),
            array('key' => 'driggs_industry', 'label' => 'driggs_industry', 'type' => 'text'),
            array('key' => 'driggs_city', 'label' => 'driggs_city', 'type' => 'text'),
            array('key' => 'driggs_brand_name', 'label' => 'driggs_brand_name', 'type' => 'text'),
            array('key' => 'driggs_site_type_purpose', 'label' => 'driggs_site_type_purpose', 'type' => 'text'),
            array('key' => 'driggs_email_1', 'label' => 'driggs_email_1', 'type' => 'email'),
            array('key' => 'driggs_address_full', 'label' => 'driggs_address_full', 'type' => 'textarea'),
            array('key' => 'driggs_phone_country_code', 'label' => 'driggs_phone_country_code', 'type' => 'number'),
            array('key' => 'driggs_phone_1', 'label' => 'driggs_phone_1', 'type' => 'text'),
            array('key' => 'driggs_special_note_for_ai_tool', 'label' => 'driggs_special_note_for_ai_tool', 'type' => 'textarea'),
            array('key' => 'driggs_phone1_platform_id', 'label' => 'driggs_phone1_platform_id', 'type' => 'number'),
            array('key' => 'driggs_cgig_id', 'label' => 'driggs_cgig_id', 'type' => 'number'),
            array('key' => 'driggs_address_species_id', 'label' => 'driggs_address_species_id', 'type' => 'number'),
            array('key' => 'driggs_citations_done', 'label' => 'driggs_citations_done', 'type' => 'boolean'),
            array('key' => 'driggs_revenue_goal', 'label' => 'driggs_revenue_goal', 'type' => 'number'),
            array('key' => 'true_root_domain', 'label' => 'true_root_domain', 'type' => 'text'),
            array('key' => 'full_subdomain', 'label' => 'full_subdomain', 'type' => 'text'),
            array('key' => 'webproperty_type', 'label' => 'webproperty_type', 'type' => 'text'),
            array('key' => 'fk_users_id', 'label' => 'fk_users_id', 'type' => 'text'),
            array('key' => 'updated_at', 'label' => 'updated_at', 'type' => 'datetime'),
            array('key' => 'wpuser1', 'label' => 'wpuser1', 'type' => 'text'),
            array('key' => 'wppass1', 'label' => 'wppass1', 'type' => 'password'),
            array('key' => 'wp_plugin_installed1', 'label' => 'wp_plugin_installed1', 'type' => 'boolean'),
            array('key' => 'wp_plugin_connected2', 'label' => 'wp_plugin_connected2', 'type' => 'boolean'),
            array('key' => 'fk_domreg_hostaccount', 'label' => 'fk_domreg_hostaccount', 'type' => 'text'),
            array('key' => 'is_wp_site', 'label' => 'is_wp_site', 'type' => 'boolean'),
            array('key' => 'wp_rest_app_pass', 'label' => 'wp_rest_app_pass', 'type' => 'password'),
            array('key' => 'driggs_social_profiles_done', 'label' => 'driggs_social_profiles_done', 'type' => 'boolean'),
            array('key' => 'driggs_hours', 'label' => 'driggs_hours', 'type' => 'textarea'),
            array('key' => 'driggs_owner_name', 'label' => 'driggs_owner_name', 'type' => 'text'),
            array('key' => 'driggs_short_descr', 'label' => 'driggs_short_descr', 'type' => 'textarea'),
            array('key' => 'driggs_long_descr', 'label' => 'driggs_long_descr', 'type' => 'textarea'),
            array('key' => 'driggs_year_opened', 'label' => 'driggs_year_opened', 'type' => 'number'),
            array('key' => 'driggs_employees_qty', 'label' => 'driggs_employees_qty', 'type' => 'number'),
            array('key' => 'driggs_keywords', 'label' => 'driggs_keywords', 'type' => 'textarea'),
            array('key' => 'driggs_category', 'label' => 'driggs_category', 'type' => 'text'),
            array('key' => 'driggs_address_species_note', 'label' => 'driggs_address_species_note', 'type' => 'text'),
            array('key' => 'driggs_payment_methods', 'label' => 'driggs_payment_methods', 'type' => 'textarea'),
            array('key' => 'driggs_social_media_links', 'label' => 'driggs_social_media_links', 'type' => 'textarea'),
            array('key' => 'driggs_logo_url', 'label' => 'driggs_logo_url', 'type' => 'logo_url'),
            array('key' => 'driggs_street_1', 'label' => 'driggs_street_1', 'type' => 'text'),
            array('key' => 'driggs_street_2', 'label' => 'driggs_street_2', 'type' => 'text'),
            array('key' => 'driggs_state_code', 'label' => 'driggs_state_code', 'type' => 'text'),
            array('key' => 'driggs_zip', 'label' => 'driggs_zip', 'type' => 'text'),
            array('key' => 'driggs_state_full', 'label' => 'driggs_state_full', 'type' => 'text'),
            array('key' => 'driggs_country', 'label' => 'driggs_country', 'type' => 'text'),
            array('key' => 'ns_full', 'label' => 'ns_full', 'type' => 'text'),
            array('key' => 'ip_address', 'label' => 'ip_address', 'type' => 'text'),
            array('key' => 'is_starred1', 'label' => 'is_starred1', 'type' => 'text'),
            array('key' => 'icon_name', 'label' => 'icon_name', 'type' => 'text'),
            array('key' => 'icon_color', 'label' => 'icon_color', 'type' => 'text'),
            array('key' => 'is_bulldozer', 'label' => 'is_bulldozer', 'type' => 'boolean'),
            array('key' => 'is_competitor', 'label' => 'is_competitor', 'type' => 'boolean'),
            array('key' => 'is_external', 'label' => 'is_external', 'type' => 'boolean'),
            array('key' => 'is_internal', 'label' => 'is_internal', 'type' => 'boolean'),
            array('key' => 'is_ppx', 'label' => 'is_ppx', 'type' => 'boolean'),
            array('key' => 'is_ms', 'label' => 'is_ms', 'type' => 'boolean'),
            array('key' => 'is_wayback_rebuild', 'label' => 'is_wayback_rebuild', 'type' => 'boolean'),
            array('key' => 'is_naked_wp_build', 'label' => 'is_naked_wp_build', 'type' => 'boolean'),
            array('key' => 'is_rnr', 'label' => 'is_rnr', 'type' => 'boolean'),
            array('key' => 'is_aff', 'label' => 'is_aff', 'type' => 'boolean'),
            array('key' => 'is_other1', 'label' => 'is_other1', 'type' => 'boolean'),
            array('key' => 'is_other2', 'label' => 'is_other2', 'type' => 'boolean'),
            array('key' => 'snailimage', 'label' => 'snailimage', 'type' => 'text'),
            array('key' => 'snail_image_url', 'label' => 'snail_image_url', 'type' => 'text'),
            array('key' => 'snail_image_status', 'label' => 'snail_image_status', 'type' => 'text'),
            array('key' => 'snail_image_error', 'label' => 'snail_image_error', 'type' => 'text'),
            array('key' => 'screenshot_url', 'label' => 'screenshot_url', 'type' => 'text'),
            array('key' => 'screenshot_taken_at', 'label' => 'screenshot_taken_at', 'type' => 'datetime'),
            array('key' => 'screenshot_status', 'label' => 'screenshot_status', 'type' => 'text'),
            array('key' => 'rel_cncglub_id', 'label' => 'rel_cncglub_id', 'type' => 'number'),
            array('key' => 'rel_city_id', 'label' => 'rel_city_id', 'type' => 'number')
        );
        
        // Add friendly names to each field
        foreach ($fields as $index => $field) {
            if ($field['type'] !== 'separator') {
                $table_name = 'sitespren';
                $friendly_name = Grove_Database::get_friendly_name($table_name, $field['key']);
                $fields[$index]['friendly_name'] = $friendly_name;
            }
        }
        
        return $fields;
    }
    
    /**
     * Grove Factory Codes Manager Page
     */
    public function grove_factory_codes_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🌳🏭 Grove Factory Codes Manager</h1>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button id="create-popup-btn" class="button button-primary">Create New Factory Code</button>
                        <button id="delete-selected-btn" class="button" style="background: #dc3545; color: white; border-color: #dc3545;">Delete Selected</button>
                        <button id="export-recovery-php" class="button button-secondary">Export Recovery PHP</button>
                    </div>
                </div>
                
                <!-- Search -->
                <div style="display: flex; justify-content: flex-end; align-items: center;">
                    <div style="position: relative;">
                        <input type="text" id="search-box" placeholder="Search factory codes..." style="padding: 8px 40px 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px; font-size: 14px;">
                        <button id="clear-search" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #ffeb3b; border: none; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 3px; cursor: pointer;">CL</button>
                    </div>
                </div>
            </div>
            
            <!-- Main Table -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table id="factory-codes-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: left; background: #f0f0f0; width: 50px;">
                                    <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
                                </th>
                                <th data-sort="code_id" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">code_id</th>
                                <th data-sort="code_slug" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">code_slug</th>
                                <th data-sort="code_type" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">code_type</th>
                                <th data-sort="code_title" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">code_title</th>
                                <th data-sort="code_description" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">code_description</th>
                                <th data-sort="usage_example" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">usage_example</th>
                                <th data-sort="is_active" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">is_active</th>
                                <th data-sort="is_core" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">is_core</th>
                                <th data-sort="plugin_source" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">plugin_source</th>
                                <th data-sort="created_at" style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-transform: lowercase; cursor: pointer; background: #f8f9fa;">created_at</th>
                                <th style="padding: 12px 8px; border: 1px solid #ddd; font-weight: bold; text-align: center; background: #f8f9fa;">Actions</th>
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
                    <li>Click on any value to edit it inline (except code_id)</li>
                    <li>Toggle switches control boolean fields</li>
                    <li>Changes are saved automatically when you click out of a field</li>
                    <li>Use "Export Recovery PHP" to generate functions.php code for manual recovery</li>
                    <li>Factory codes preserve critical shortcodes in case plugins need to be removed</li>
                </ul>
            </div>
            
        </div>
        
        <!-- Create/Edit Popup Modal -->
        <div id="factory-code-popup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999999;">
            <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 80%; max-width: 800px; max-height: 90vh; overflow-y: auto;">
                <h2 id="popup-title">Create New Factory Code</h2>
                <form id="factory-code-form">
                    <input type="hidden" id="code_id" name="code_id" value="">
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px; font-weight: bold; width: 150px;">code_slug:</td>
                            <td style="padding: 10px;">
                                <input type="text" id="code_slug" name="code_slug" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">code_type:</td>
                            <td style="padding: 10px;">
                                <select id="code_type" name="code_type" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="shortcode">shortcode</option>
                                    <option value="function">function</option>
                                    <option value="filter">filter</option>
                                    <option value="action">action</option>
                                    <option value="class">class</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">code_title:</td>
                            <td style="padding: 10px;">
                                <input type="text" id="code_title" name="code_title" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">code_description:</td>
                            <td style="padding: 10px;">
                                <textarea id="code_description" name="code_description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">code_snippet:</td>
                            <td style="padding: 10px;">
                                <textarea id="code_snippet" name="code_snippet" rows="10" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 13px; resize: vertical;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">usage_example:</td>
                            <td style="padding: 10px;">
                                <textarea id="usage_example" name="usage_example" rows="2" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">plugin_source:</td>
                            <td style="padding: 10px;">
                                <select id="plugin_source" name="plugin_source" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="grove">grove</option>
                                    <option value="snefuruplin">snefuruplin</option>
                                    <option value="both">both</option>
                                    <option value="custom">custom</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">is_active:</td>
                            <td style="padding: 10px;">
                                <input type="checkbox" id="is_active" name="is_active" checked style="transform: scale(1.5);">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; font-weight: bold;">is_core:</td>
                            <td style="padding: 10px;">
                                <input type="checkbox" id="is_core" name="is_core" style="transform: scale(1.5);">
                            </td>
                        </tr>
                    </table>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" id="cancel-btn" class="button" style="margin-right: 10px;">Cancel</button>
                        <button type="submit" id="save-btn" class="button button-primary">Save Factory Code</button>
                    </div>
                </form>
            </div>
        </div>
        
        <style type="text/css">
        /* Factory Codes Table Styling */
        #factory-codes-table th {
            position: relative;
        }
        
        #factory-codes-table th[data-sort]:hover {
            background-color: #e9ecef !important;
        }
        
        #factory-codes-table th[data-sort]:after {
            content: '↕';
            position: absolute;
            right: 8px;
            color: #999;
            font-size: 10px;
        }
        
        #factory-codes-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .factory-code-checkbox {
            transform: scale(1.2);
        }
        
        .factory-code-active-true {
            color: #28a745;
            font-weight: bold;
        }
        
        .factory-code-active-false {
            color: #dc3545;
            font-weight: bold;
        }
        
        .factory-code-core-true {
            color: #007bff;
            font-weight: bold;
        }
        
        .factory-code-snippet-preview {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: monospace;
            font-size: 12px;
            color: #666;
        }
        
        .factory-code-action-btn {
            padding: 4px 8px;
            font-size: 12px;
            margin: 0 2px;
            border-radius: 3px;
        }
        
        /* Popup styling */
        #factory-code-popup textarea {
            font-family: 'Courier New', monospace;
        }
        
        #factory-code-popup input[type="text"], 
        #factory-code-popup textarea,
        #factory-code-popup select {
            font-size: 14px;
        }
        </style>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            let currentData = [];
            let currentSort = { column: 'code_id', direction: 'asc' };
            let searchTerm = '';
            
            // Load initial data
            loadFactoryCodesData();
            
            function loadFactoryCodesData() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'grove_factory_codes_get_data',
                        nonce: '<?php echo wp_create_nonce('grove_factory_codes_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            currentData = response.data;
                            displayData();
                        } else {
                            alert('Error loading factory codes data: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error loading factory codes data');
                    }
                });
            }
            
            function displayData() {
                let tbody = $('#table-body');
                tbody.empty();
                
                // Filter and sort data
                let filteredData = currentData.filter(function(item) {
                    if (!searchTerm) return true;
                    
                    return (
                        (item.code_slug && item.code_slug.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (item.code_title && item.code_title.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (item.code_type && item.code_type.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (item.code_description && item.code_description.toLowerCase().includes(searchTerm.toLowerCase()))
                    );
                });
                
                // Sort data
                filteredData.sort(function(a, b) {
                    let aVal = a[currentSort.column] || '';
                    let bVal = b[currentSort.column] || '';
                    
                    // Convert to string for comparison
                    aVal = aVal.toString().toLowerCase();
                    bVal = bVal.toString().toLowerCase();
                    
                    if (currentSort.direction === 'asc') {
                        return aVal < bVal ? -1 : (aVal > bVal ? 1 : 0);
                    } else {
                        return aVal > bVal ? -1 : (aVal < bVal ? 1 : 0);
                    }
                });
                
                // Display data
                filteredData.forEach(function(item) {
                    let row = $('<tr>');
                    
                    // Checkbox
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><input type="checkbox" class="row-select" data-id="' + item.code_id + '" style="width: 20px; height: 20px;"></td>');
                    
                    // code_id (read-only)
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; color: #666; font-style: italic;">' + (item.code_id || '') + '</td>');
                    
                    // code_slug (editable)
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="code_slug" data-id="' + item.code_id + '" class="editable-field">' + (item.code_slug || '') + '</td>');
                    
                    // code_type (editable select)
                    let typeClass = 'factory-code-type-' + (item.code_type || 'unknown');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="code_type" data-id="' + item.code_id + '" class="editable-select ' + typeClass + '">' + (item.code_type || '') + '</td>');
                    
                    // code_title (editable)
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="code_title" data-id="' + item.code_id + '" class="editable-field">' + (item.code_title || '') + '</td>');
                    
                    // code_description (editable textarea)
                    let description = item.code_description || '';
                    let shortDescription = description.length > 50 ? description.substring(0, 50) + '...' : description;
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="code_description" data-id="' + item.code_id + '" class="editable-textarea" title="' + $('<div>').text(description).html() + '">' + $('<div>').text(shortDescription).html() + '</td>');
                    
                    // usage_example (editable textarea)
                    let usage = item.usage_example || '';
                    let shortUsage = usage.length > 30 ? usage.substring(0, 30) + '...' : usage;
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="usage_example" data-id="' + item.code_id + '" class="editable-textarea" title="' + $('<div>').text(usage).html() + '">' + $('<div>').text(shortUsage).html() + '</td>');
                    
                    // is_active (toggle)
                    let activeChecked = item.is_active == 1 ? 'checked' : '';
                    let activeClass = item.is_active == 1 ? 'factory-code-active-true' : 'factory-code-active-false';
                    let activeText = item.is_active == 1 ? 'Yes' : 'No';
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><input type="checkbox" class="toggle-field factory-code-checkbox" data-field="is_active" data-id="' + item.code_id + '" ' + activeChecked + '> <span class="' + activeClass + '">' + activeText + '</span></td>');
                    
                    // is_core (toggle)
                    let coreChecked = item.is_core == 1 ? 'checked' : '';
                    let coreClass = item.is_core == 1 ? 'factory-code-core-true' : '';
                    let coreText = item.is_core == 1 ? 'Yes' : 'No';
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><input type="checkbox" class="toggle-field factory-code-checkbox" data-field="is_core" data-id="' + item.code_id + '" ' + coreChecked + '> <span class="' + coreClass + '">' + coreText + '</span></td>');
                    
                    // plugin_source (editable select)
                    let sourceClass = 'factory-code-source-' + (item.plugin_source || 'unknown');
                    row.append('<td style="padding: 8px; border: 1px solid #ddd;" data-field="plugin_source" data-id="' + item.code_id + '" class="editable-select ' + sourceClass + '">' + (item.plugin_source || '') + '</td>');
                    
                    // created_at (read-only)
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; color: #666; font-size: 12px;">' + (item.created_at || '') + '</td>');
                    
                    // Actions
                    let actions = '<button class="button button-small factory-code-action-btn edit-btn" data-id="' + item.code_id + '">Edit</button>';
                    actions += '<button class="button button-small factory-code-action-btn view-code-btn" data-id="' + item.code_id + '" style="background: #17a2b8; color: white; border-color: #17a2b8;">View Code</button>';
                    actions += '<button class="button button-small factory-code-action-btn copy-code-btn" data-id="' + item.code_id + '" style="background: #28a745; color: white; border-color: #28a745;">Copy</button>';
                    row.append('<td style="padding: 8px; border: 1px solid #ddd; text-align: center;">' + actions + '</td>');
                    
                    tbody.append(row);
                });
                
                if (filteredData.length === 0) {
                    tbody.append('<tr><td colspan="12" style="padding: 20px; text-align: center; color: #666;">No factory codes found.</td></tr>');
                }
            }
            
            // Event handlers will go here...
            
        });
        </script>
        
        <?php
    }
    
    /**
     * AJAX handler to get factory codes data
     */
    public function grove_factory_codes_get_data() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_factory_codes_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $factory_codes = Grove_Database::get_factory_codes();
        wp_send_json_success($factory_codes);
    }
    
    /**
     * AJAX handler to update factory codes field
     */
    public function grove_factory_codes_update_field() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_factory_codes_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $code_id = intval($_POST['code_id']);
        $field = sanitize_text_field($_POST['field']);
        $value = $_POST['value']; // Don't sanitize code snippets
        
        // Validate field name
        $allowed_fields = array('code_slug', 'code_type', 'code_title', 'code_description', 'code_snippet', 'dependencies', 'usage_example', 'is_active', 'is_core', 'plugin_source', 'wp_version_min');
        if (!in_array($field, $allowed_fields)) {
            wp_send_json_error('Invalid field');
        }
        
        // Handle boolean fields
        if (in_array($field, array('is_active', 'is_core'))) {
            $value = $value ? 1 : 0;
        }
        
        $data = array($field => $value);
        $result = Grove_Database::update_factory_code($code_id, $data);
        
        if ($result !== false) {
            wp_send_json_success('Field updated successfully');
        } else {
            wp_send_json_error('Failed to update field');
        }
    }
    
    /**
     * AJAX handler to create factory code
     */
    public function grove_factory_codes_create() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_factory_codes_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $data = array(
            'code_slug' => sanitize_text_field($_POST['code_slug']),
            'code_type' => sanitize_text_field($_POST['code_type']),
            'code_title' => sanitize_text_field($_POST['code_title']),
            'code_description' => sanitize_textarea_field($_POST['code_description']),
            'code_snippet' => $_POST['code_snippet'], // Don't sanitize code
            'dependencies' => sanitize_textarea_field($_POST['dependencies']),
            'usage_example' => sanitize_textarea_field($_POST['usage_example']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_core' => isset($_POST['is_core']) ? 1 : 0,
            'plugin_source' => sanitize_text_field($_POST['plugin_source']),
            'wp_version_min' => sanitize_text_field($_POST['wp_version_min'])
        );
        
        $result = Grove_Database::insert_factory_code($data);
        
        if ($result !== false) {
            wp_send_json_success('Factory code created successfully');
        } else {
            wp_send_json_error('Failed to create factory code');
        }
    }
    
    /**
     * AJAX handler to delete factory codes
     */
    public function grove_factory_codes_delete() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_factory_codes_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $code_ids = array_map('intval', $_POST['code_ids']);
        $deleted_count = 0;
        
        foreach ($code_ids as $code_id) {
            if (Grove_Database::delete_factory_code($code_id)) {
                $deleted_count++;
            }
        }
        
        if ($deleted_count > 0) {
            wp_send_json_success("Deleted $deleted_count factory codes successfully");
        } else {
            wp_send_json_error('Failed to delete factory codes');
        }
    }
    
    /**
     * Grove Buffalo Manager Page
     */
    public function grove_buffalor_page() {
        Grove_Buffalor::render_page();
    }
    
    /**
     * AJAX handler for updating hoof code content
     */
    public function grove_update_hoof_code() {
        // Verify nonce
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'grove_hoof_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $hoof_id = isset($_POST['hoof_id']) ? intval($_POST['hoof_id']) : 0;
        $content = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
        
        if (!$hoof_id) {
            wp_send_json_error('Invalid hoof ID');
        }
        
        // Update the hoof code
        $result = Grove_Zen_Shortcodes::update_hoof_code($hoof_id, $content);
        
        if ($result !== false) {
            // Get the shortcode slug to process the preview
            global $wpdb;
            $table = $wpdb->prefix . 'zen_hoof_codes';
            $slug = $wpdb->get_var($wpdb->prepare(
                "SELECT hoof_slug FROM $table WHERE hoof_id = %d",
                $hoof_id
            ));
            
            // Generate the preview
            $preview = '';
            if ($slug) {
                $preview = do_shortcode('[' . $slug . ']');
            }
            
            wp_send_json_success(array(
                'message' => 'Hoof code updated successfully',
                'preview' => $preview
            ));
        } else {
            wp_send_json_error('Failed to update hoof code');
        }
    }
    
    /**
     * Grove Hoof Manager Page
     */
    public function grove_hoof_mar_page() {
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-hoof-mar.php';
        Grove_Hoof_Mar::render_page();
    }
    
    /**
     * AJAX handler for creating new hoof code
     */
    public function grove_hoof_create() {
        // Verify nonce
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'grove_hoof_admin_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // Validate required fields
        if (empty($_POST['slug'])) {
            wp_send_json_error('Slug is required');
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            wp_send_json_error('Hoof codes table does not exist. Please reactivate the plugin.');
            return;
        }
        
        $slug = sanitize_title($_POST['slug']);
        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $content = wp_unslash($_POST['content'] ?? '');
        
        // Validate slug
        if (empty($slug)) {
            wp_send_json_error('Invalid slug format');
            return;
        }
        
        // Check if slug already exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE hoof_slug = %s",
            $slug
        ));
        
        if ($exists) {
            wp_send_json_error('Slug already exists: ' . $slug);
            return;
        }
        
        $result = $wpdb->insert(
            $table,
            array(
                'hoof_slug' => $slug,
                'hoof_title' => $title,
                'hoof_description' => $description,
                'hoof_content' => $content,
                'is_active' => 1,
                'is_system' => 0,
                'position_order' => 999
            ),
            array('%s', '%s', '%s', '%s', '%d', '%d', '%d')
        );
        
        if ($result !== false) {
            wp_send_json_success('Hoof code created successfully');
        } else {
            wp_send_json_error('Database error: ' . $wpdb->last_error);
        }
    }
    
    /**
     * AJAX handler for updating hoof code
     */
    public function grove_hoof_update() {
        // Verify nonce
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'grove_hoof_admin_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        $hoof_id = intval($_POST['hoof_id']);
        $slug = sanitize_title($_POST['slug']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);
        $content = wp_unslash($_POST['content']);
        $is_active = intval($_POST['is_active']);
        
        $result = $wpdb->update(
            $table,
            array(
                'hoof_slug' => $slug,
                'hoof_title' => $title,
                'hoof_description' => $description,
                'hoof_content' => $content,
                'is_active' => $is_active
            ),
            array('hoof_id' => $hoof_id),
            array('%s', '%s', '%s', '%s', '%d'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success('Hoof code updated successfully');
        } else {
            wp_send_json_error('Failed to update hoof code');
        }
    }
    
    /**
     * AJAX handler for deleting hoof code
     */
    public function grove_hoof_delete() {
        // Verify nonce
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'grove_hoof_admin_nonce')) {
            wp_send_json_error('Invalid nonce');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        $hoof_id = intval($_POST['hoof_id']);
        
        // Check if it's a system code
        $is_system = $wpdb->get_var($wpdb->prepare(
            "SELECT is_system FROM $table WHERE hoof_id = %d",
            $hoof_id
        ));
        
        if ($is_system) {
            wp_send_json_error('Cannot delete system hoof codes');
        }
        
        $result = $wpdb->delete(
            $table,
            array('hoof_id' => $hoof_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success('Hoof code deleted successfully');
        } else {
            wp_send_json_error('Failed to delete hoof code');
        }
    }
    
    /**
     * General Shortcodes Manager Page - Matches snefuruplin services page styling
     */
    public function grove_generalshortcodes_mar_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        // Handle AJAX requests
        if (isset($_POST['action'])) {
            $this->handle_generalshortcodes_ajax();
            return;
        }
        
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        
        // Suppress all admin notices except those directly related to this page
        add_action('admin_print_scripts', function() {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        });
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1 style="margin-bottom: 20px;">🔧 General Shortcodes Manager</h1>
            
            <!-- Control Bar -->
            <div style="background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <button id="create-inline-btn" class="button button-primary">Create New (Inline)</button>
                        <button id="create-popup-btn" class="button button-secondary">Create New (Popup)</button>
                    </div>
                </div>
                
                <!-- Nubra Tableface Kite -->
                <div id="nubra-tableface-kite" style="margin-bottom: 15px;"></div>
                
                <!-- Pagination and Search Controls -->
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <!-- Items per page -->
                        <div style="display: flex; gap: 0; border: 1px solid #ccc;">
                            <button class="per-page-btn" data-value="10" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px;">10</button>
                            <button class="per-page-btn" data-value="20" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">20</button>
                            <button class="per-page-btn" data-value="50" style="border: none; padding: 10px 15px; background: #f9f9f9; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">50</button>
                            <button class="per-page-btn active" data-value="100" style="border: none; padding: 10px 15px; background: #0073aa; color: white; cursor: pointer; font-size: 14px; border-left: 1px solid #ccc;">100</button>
                        </div>
                        
                        <!-- Search box -->
                        <input type="text" id="search-box" placeholder="Search shortcodes..." style="padding: 8px; border: 1px solid #ccc; border-radius: 3px; width: 200px;">
                        <button id="search-btn" class="button">Search</button>
                        <button id="clear-search-btn" class="button">Clear</button>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="pagination-controls" style="display: flex; align-items: center; gap: 10px;">
                        <button id="prev-page" class="button" disabled>« Previous</button>
                        <span id="page-info">Page 1 of 1</span>
                        <button id="next-page" class="button" disabled>Next »</button>
                    </div>
                </div>
            </div>
            
            <!-- Table Container -->
            <div id="table-container" style="background: white; border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                <table id="shortcodes-table" class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><b>shortcode_id</b></th>
                            <th style="width: 200px;"><b>shortcode_name</b></th>
                            <th style="width: 150px;"><b>shortcode_slug</b></th>
                            <th><b>shortcode_content</b></th>
                            <th style="width: 120px;"><b>shortcode_type</b></th>
                            <th style="width: 100px;"><b>shortcode_category</b></th>
                            <th style="width: 80px;"><b>is_active</b></th>
                            <th style="width: 100px;"><b>is_adminpublic</b></th>
                            <th style="width: 180px;"><b>copy shortcode</b></th>
                            <th style="width: 250px;"><b>full usage example</b></th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="shortcodes-tbody">
                        <!-- Table content will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Loading indicator -->
            <div id="loading-indicator" style="text-align: center; padding: 20px; display: none;">
                <div style="font-size: 16px;">Loading...</div>
            </div>
            
        </div>
        </div>
        
        <!-- Inline Edit Row Template -->
        <script type="text/template" id="inline-edit-template">
            <tr class="inline-edit-row">
                <td colspan="11">
                    <div style="padding: 15px; background: #f9f9f9;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label>Name:</label>
                                <input type="text" name="shortcode_name" style="width: 100%; margin-top: 5px;">
                            </div>
                            <div>
                                <label>Slug:</label>
                                <input type="text" name="shortcode_slug" style="width: 100%; margin-top: 5px;">
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label>Content:</label>
                                <textarea name="shortcode_content" style="width: 100%; height: 100px; margin-top: 5px;"></textarea>
                            </div>
                            <div>
                                <label>Description:</label>
                                <textarea name="shortcode_description" style="width: 100%; height: 60px; margin-top: 5px;"></textarea>
                            </div>
                            <div>
                                <label>Category:</label>
                                <input type="text" name="shortcode_category" style="width: 100%; margin-top: 5px;">
                            </div>
                            <div>
                                <label>Type:</label>
                                <select name="shortcode_type" style="width: 100%; margin-top: 5px;">
                                    <option value="custom">Custom</option>
                                    <option value="system">System</option>
                                    <option value="template">Template</option>
                                </select>
                            </div>
                            <div>
                                <label>Usage Example:</label>
                                <input type="text" name="shortcode_usage_example" style="width: 100%; margin-top: 5px;">
                            </div>
                        </div>
                        <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="is_active"> Active
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="is_global"> Global
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="is_adminpublic"> Admin Public
                            </label>
                            <div style="margin-left: auto;">
                                <button class="save-inline button button-primary">Save</button>
                                <button class="cancel-inline button">Cancel</button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </script>
        
        <script>
        jQuery(document).ready(function($) {
            let currentPage = 1;
            let itemsPerPage = 100;
            let searchQuery = '';
            let editingRow = null;
            
            // Load initial data
            loadShortcodes();
            
            // Items per page buttons
            $('.per-page-btn').on('click', function() {
                $('.per-page-btn').removeClass('active').css({
                    'background': '#f9f9f9',
                    'color': '#333'
                });
                $(this).addClass('active').css({
                    'background': '#0073aa',
                    'color': 'white'
                });
                itemsPerPage = parseInt($(this).data('value'));
                currentPage = 1;
                loadShortcodes();
            });
            
            // Search functionality
            $('#search-btn').on('click', function() {
                searchQuery = $('#search-box').val();
                currentPage = 1;
                loadShortcodes();
            });
            
            $('#clear-search-btn').on('click', function() {
                searchQuery = '';
                $('#search-box').val('');
                currentPage = 1;
                loadShortcodes();
            });
            
            // Enter key in search box
            $('#search-box').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#search-btn').click();
                }
            });
            
            // Pagination
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadShortcodes();
                }
            });
            
            $('#next-page').on('click', function() {
                currentPage++;
                loadShortcodes();
            });
            
            // Create new inline
            $('#create-inline-btn').on('click', function() {
                if (editingRow) {
                    editingRow.remove();
                }
                
                const template = $('#inline-edit-template').html();
                const newRow = $(template);
                newRow.find('.save-inline').text('Create');
                $('#shortcodes-tbody').prepend(newRow);
                editingRow = newRow;
                
                // Focus on first input
                newRow.find('input[name="shortcode_name"]').focus();
            });
            
            // Save inline (create or update)
            $(document).on('click', '.save-inline', function() {
                const row = $(this).closest('.inline-edit-row');
                const isCreate = $(this).text() === 'Create';
                const shortcodeId = isCreate ? null : row.data('shortcode-id');
                
                const data = {
                    action: isCreate ? 'grove_generalshortcodes_create' : 'grove_generalshortcodes_update',
                    shortcode_name: row.find('input[name="shortcode_name"]').val(),
                    shortcode_slug: row.find('input[name="shortcode_slug"]').val(),
                    shortcode_content: row.find('textarea[name="shortcode_content"]').val(),
                    shortcode_description: row.find('textarea[name="shortcode_description"]').val(),
                    shortcode_category: row.find('input[name="shortcode_category"]').val(),
                    shortcode_type: row.find('select[name="shortcode_type"]').val(),
                    shortcode_usage_example: row.find('input[name="shortcode_usage_example"]').val(),
                    is_active: row.find('input[name="is_active"]').is(':checked') ? 1 : 0,
                    is_global: row.find('input[name="is_global"]').is(':checked') ? 1 : 0,
                    is_adminpublic: row.find('input[name="is_adminpublic"]').is(':checked') ? 1 : 0
                };
                
                if (!isCreate) {
                    data.shortcode_id = shortcodeId;
                }
                
                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        editingRow = null;
                        loadShortcodes();
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            });
            
            // Cancel inline
            $(document).on('click', '.cancel-inline', function() {
                $(this).closest('.inline-edit-row').remove();
                editingRow = null;
            });
            
            // Copy shortcode button
            $(document).on('click', '.copy-shortcode-btn', function() {
                const shortcode = $(this).data('shortcode');
                
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        // Visual feedback
                        const btn = $(this);
                        const originalText = btn.text();
                        btn.text('✓ Copied!').css('background', '#28a745');
                        setTimeout(function() {
                            btn.text(originalText).css('background', '#0073aa');
                        }, 2000);
                    }.bind(this)).catch(function(err) {
                        console.error('Failed to copy:', err);
                        fallbackCopy(shortcode);
                    });
                } else {
                    fallbackCopy(shortcode);
                }
                
                function fallbackCopy(text) {
                    // Fallback for older browsers
                    const tempInput = $('<textarea>');
                    $('body').append(tempInput);
                    tempInput.val(text).select();
                    document.execCommand('copy');
                    tempInput.remove();
                    
                    // Visual feedback
                    const btn = $(this);
                    const originalText = btn.text();
                    btn.text('✓ Copied!').css('background', '#28a745');
                    setTimeout(function() {
                        btn.text(originalText).css('background', '#0073aa');
                    }, 2000);
                }
            });
            
            // Copy usage example button
            $(document).on('click', '.copy-example-btn', function() {
                const example = $(this).data('example');
                const btn = $(this);
                
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(example).then(function() {
                        // Visual feedback
                        const originalText = btn.text();
                        btn.text('✓ Copied!').css('background', '#228B22');
                        setTimeout(function() {
                            btn.text(originalText).css('background', '#8B4513');
                        }, 2000);
                    }).catch(function(err) {
                        console.error('Failed to copy:', err);
                        fallbackExampleCopy(example, btn);
                    });
                } else {
                    fallbackExampleCopy(example, btn);
                }
                
                function fallbackExampleCopy(text, button) {
                    // Fallback for older browsers
                    const tempInput = $('<textarea>');
                    $('body').append(tempInput);
                    tempInput.val(text).select();
                    document.execCommand('copy');
                    tempInput.remove();
                    
                    // Visual feedback
                    const originalText = button.text();
                    button.text('✓ Copied!').css('background', '#228B22');
                    setTimeout(function() {
                        button.text(originalText).css('background', '#8B4513');
                    }, 2000);
                }
            });
            
            // Edit button
            $(document).on('click', '.edit-shortcode', function() {
                if (editingRow) {
                    editingRow.remove();
                }
                
                const shortcodeId = $(this).data('shortcode-id');
                const row = $(this).closest('tr');
                const template = $('#inline-edit-template').html();
                const editRow = $(template);
                
                // Populate fields
                editRow.find('input[name="shortcode_name"]').val(row.find('.shortcode-name').text());
                editRow.find('input[name="shortcode_slug"]').val(row.find('.shortcode-slug').text());
                editRow.find('textarea[name="shortcode_content"]').val(row.find('.shortcode-content').data('full-content'));
                editRow.find('textarea[name="shortcode_description"]').val(row.data('description'));
                editRow.find('input[name="shortcode_category"]').val(row.find('.shortcode-category').text());
                editRow.find('select[name="shortcode_type"]').val(row.find('.shortcode-type').text());
                editRow.find('input[name="shortcode_usage_example"]').val(row.data('usage'));
                editRow.find('input[name="is_active"]').prop('checked', row.find('.shortcode-active').text() === 'Yes');
                editRow.find('input[name="is_global"]').prop('checked', row.data('global') == '1');
                editRow.find('input[name="is_adminpublic"]').prop('checked', row.find('.shortcode-adminpublic').data('adminpublic') == '1');
                
                editRow.data('shortcode-id', shortcodeId);
                row.after(editRow);
                editingRow = editRow;
            });
            
            // Delete button
            $(document).on('click', '.delete-shortcode', function() {
                if (confirm('Are you sure you want to delete this shortcode?')) {
                    const shortcodeId = $(this).data('shortcode-id');
                    
                    $.post(ajaxurl, {
                        action: 'grove_generalshortcodes_delete',
                        shortcode_id: shortcodeId
                    }, function(response) {
                        if (response.success) {
                            loadShortcodes();
                        } else {
                            alert('Error: ' + response.data);
                        }
                    });
                }
            });
            
            function loadShortcodes() {
                $('#loading-indicator').show();
                $('#table-container').hide();
                
                $.post(ajaxurl, {
                    action: 'grove_generalshortcodes_load',
                    page: currentPage,
                    per_page: itemsPerPage,
                    search: searchQuery
                }, function(response) {
                    $('#loading-indicator').hide();
                    $('#table-container').show();
                    
                    if (response.success) {
                        const data = response.data;
                        renderTable(data.shortcodes);
                        updatePagination(data.total_pages, data.total_items);
                    } else {
                        $('#shortcodes-tbody').html('<tr><td colspan="11">Error loading shortcodes: ' + response.data + '</td></tr>');
                    }
                });
            }
            
            function renderTable(shortcodes) {
                let html = '';
                shortcodes.forEach(function(shortcode) {
                    // Ensure all fields have at least empty string values
                    shortcode.shortcode_name = shortcode.shortcode_name || '';
                    shortcode.shortcode_slug = shortcode.shortcode_slug || '';
                    shortcode.shortcode_content = shortcode.shortcode_content || '';
                    shortcode.shortcode_type = shortcode.shortcode_type || 'custom';
                    shortcode.shortcode_category = shortcode.shortcode_category || '';
                    shortcode.shortcode_description = shortcode.shortcode_description || '';
                    shortcode.shortcode_usage_example = shortcode.shortcode_usage_example || '';
                    
                    const truncatedContent = shortcode.shortcode_content.length > 50 
                        ? shortcode.shortcode_content.substring(0, 50) + '...'
                        : shortcode.shortcode_content;
                        
                    const adminPublicBadge = shortcode.is_adminpublic == '1' 
                        ? '<span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold;">yes adminpublic</span>'
                        : '';
                        
                    html += `
                        <tr data-description="${(shortcode.shortcode_description || '').replace(/"/g, '&quot;')}" 
                            data-usage="${(shortcode.shortcode_usage_example || '').replace(/"/g, '&quot;')}" 
                            data-global="${shortcode.is_global}">
                            <td>${shortcode.shortcode_id}</td>
                            <td class="shortcode-name">${shortcode.shortcode_name}</td>
                            <td class="shortcode-slug">${shortcode.shortcode_slug}</td>
                            <td class="shortcode-content" data-full-content="${shortcode.shortcode_content.replace(/"/g, '&quot;')}">${truncatedContent}</td>
                            <td class="shortcode-type">${shortcode.shortcode_type}</td>
                            <td class="shortcode-category">${shortcode.shortcode_category || ''}</td>
                            <td class="shortcode-active">${shortcode.is_active == '1' ? 'Yes' : 'No'}</td>
                            <td class="shortcode-adminpublic" data-adminpublic="${shortcode.is_adminpublic}">${adminPublicBadge}</td>
                            <td style="padding: 8px;">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <button class="copy-shortcode-btn button button-small" 
                                            data-shortcode="[${shortcode.shortcode_slug}]" 
                                            style="background: #0073aa; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;"
                                            title="Copy shortcode to clipboard">
                                        📋 Copy
                                    </button>
                                    <input type="text" 
                                           value="[${shortcode.shortcode_slug}]" 
                                           readonly 
                                           style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9; font-size: 11px; font-family: monospace;">
                                </div>
                            </td>
                            <td style="padding: 8px;">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <button class="copy-example-btn button button-small" 
                                            data-example="${shortcode.shortcode_usage_example || '[' + shortcode.shortcode_slug + ']'}" 
                                            style="background: #8B4513; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;"
                                            title="Copy full usage example">
                                        📝 Copy
                                    </button>
                                    <input type="text" 
                                           value="${shortcode.shortcode_usage_example || '[' + shortcode.shortcode_slug + ']'}" 
                                           readonly 
                                           style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; background: #fffbf0; font-size: 11px; font-family: monospace;">
                                </div>
                            </td>
                            <td>
                                <button class="edit-shortcode button button-small" data-shortcode-id="${shortcode.shortcode_id}">Edit</button>
                                <button class="delete-shortcode button button-small" data-shortcode-id="${shortcode.shortcode_id}">Delete</button>
                            </td>
                        </tr>
                    `;
                });
                $('#shortcodes-tbody').html(html);
            }
            
            function updatePagination(totalPages, totalItems) {
                $('#page-info').text(`Page ${currentPage} of ${totalPages} (${totalItems} items)`);
                $('#prev-page').prop('disabled', currentPage <= 1);
                $('#next-page').prop('disabled', currentPage >= totalPages);
            }
        });
        </script>
        
        <style>
        .inline-edit-row {
            background: #f9f9f9 !important;
        }
        .inline-edit-row td {
            border-top: 2px solid #0073aa !important;
            border-bottom: 2px solid #0073aa !important;
        }
        .per-page-btn.active {
            background: #0073aa !important;
            color: white !important;
        }
        #shortcodes-table th {
            background: #f1f1f1;
            font-weight: bold;
        }
        #shortcodes-table td {
            vertical-align: middle;
        }
        .button-small {
            padding: 2px 8px !important;
            font-size: 11px !important;
            line-height: 1.4 !important;
            height: auto !important;
        }
        </style>
        
        <?php
    }
    
    /**
     * Handle General Shortcodes AJAX requests
     */
    private function handle_generalshortcodes_ajax() {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'grove_generalshortcodes_load':
                $this->grove_generalshortcodes_load();
                break;
            case 'grove_generalshortcodes_create':
                $this->grove_generalshortcodes_create();
                break;
            case 'grove_generalshortcodes_update':
                $this->grove_generalshortcodes_update();
                break;
            case 'grove_generalshortcodes_delete':
                $this->grove_generalshortcodes_delete();
                break;
        }
    }
    
    /**
     * Load general shortcodes for table display
     */
    public function grove_generalshortcodes_load() {
        global $wpdb;
        
        $page = intval($_POST['page'] ?? 1);
        $per_page = intval($_POST['per_page'] ?? 100);
        $search = sanitize_text_field($_POST['search'] ?? '');
        $offset = ($page - 1) * $per_page;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // Build search condition
        $search_condition = '';
        if (!empty($search)) {
            $search_condition = $wpdb->prepare(
                " AND (shortcode_name LIKE %s OR shortcode_slug LIKE %s OR shortcode_content LIKE %s OR shortcode_category LIKE %s)",
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            );
        }
        
        // Get total count
        $total_query = "SELECT COUNT(*) FROM $table_name WHERE 1=1 $search_condition";
        $total_items = $wpdb->get_var($total_query);
        $total_pages = ceil($total_items / $per_page);
        
        // Get shortcodes
        $query = "SELECT * FROM $table_name WHERE 1=1 $search_condition ORDER BY position_order ASC, shortcode_id DESC LIMIT $per_page OFFSET $offset";
        $shortcodes = $wpdb->get_results($query);
        
        wp_send_json_success(array(
            'shortcodes' => $shortcodes,
            'total_pages' => $total_pages,
            'total_items' => $total_items,
            'current_page' => $page
        ));
    }
    
    /**
     * Create new general shortcode
     */
    public function grove_generalshortcodes_create() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        $data = array(
            'shortcode_name' => sanitize_text_field($_POST['shortcode_name']),
            'shortcode_slug' => sanitize_text_field($_POST['shortcode_slug']),
            'shortcode_content' => wp_kses_post($_POST['shortcode_content']),
            'shortcode_description' => sanitize_textarea_field($_POST['shortcode_description']),
            'shortcode_category' => sanitize_text_field($_POST['shortcode_category']),
            'shortcode_type' => sanitize_text_field($_POST['shortcode_type']),
            'shortcode_usage_example' => sanitize_text_field($_POST['shortcode_usage_example']),
            'is_active' => intval($_POST['is_active']),
            'is_global' => intval($_POST['is_global']),
            'is_adminpublic' => intval($_POST['is_adminpublic']),
            'author_user_id' => get_current_user_id()
        );
        
        $result = $wpdb->insert($table_name, $data);
        
        if ($result) {
            // If the shortcode is active, register it immediately
            if ($data['is_active'] == 1) {
                // Include plugin.php to ensure is_plugin_active is available
                if (!function_exists('is_plugin_active')) {
                    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                Grove_Zen_Shortcodes::register_single_shortcode($data['shortcode_slug'], $data['shortcode_content']);
            }
            wp_send_json_success('Shortcode created successfully');
        } else {
            wp_send_json_error('Failed to create shortcode');
        }
    }
    
    /**
     * Update existing general shortcode
     */
    public function grove_generalshortcodes_update() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        $shortcode_id = intval($_POST['shortcode_id']);
        
        // Get the old shortcode data first
        $old_shortcode = $wpdb->get_row($wpdb->prepare(
            "SELECT shortcode_slug, is_active FROM $table_name WHERE shortcode_id = %d",
            $shortcode_id
        ));
        
        $data = array(
            'shortcode_name' => sanitize_text_field($_POST['shortcode_name']),
            'shortcode_slug' => sanitize_text_field($_POST['shortcode_slug']),
            'shortcode_content' => wp_kses_post($_POST['shortcode_content']),
            'shortcode_description' => sanitize_textarea_field($_POST['shortcode_description']),
            'shortcode_category' => sanitize_text_field($_POST['shortcode_category']),
            'shortcode_type' => sanitize_text_field($_POST['shortcode_type']),
            'shortcode_usage_example' => sanitize_text_field($_POST['shortcode_usage_example']),
            'is_active' => intval($_POST['is_active']),
            'is_global' => intval($_POST['is_global']),
            'is_adminpublic' => intval($_POST['is_adminpublic'])
        );
        
        $result = $wpdb->update(
            $table_name,
            $data,
            array('shortcode_id' => $shortcode_id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d'),
            array('%d')
        );
        
        if ($result !== false) {
            // Handle shortcode re-registration
            if ($old_shortcode) {
                // Include plugin.php to ensure is_plugin_active is available
                if (!function_exists('is_plugin_active')) {
                    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                
                // If the old shortcode was active, unregister it first
                if ($old_shortcode->is_active == 1) {
                    Grove_Zen_Shortcodes::unregister_single_shortcode($old_shortcode->shortcode_slug);
                }
                
                // If the new shortcode is active, register it
                if ($data['is_active'] == 1) {
                    Grove_Zen_Shortcodes::register_single_shortcode($data['shortcode_slug'], $data['shortcode_content']);
                }
            }
            
            wp_send_json_success('Shortcode updated successfully');
        } else {
            wp_send_json_error('Failed to update shortcode');
        }
    }
    
    /**
     * Delete general shortcode
     */
    public function grove_generalshortcodes_delete() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        $shortcode_id = intval($_POST['shortcode_id']);
        
        // Get the shortcode slug before deleting
        $shortcode = $wpdb->get_row($wpdb->prepare(
            "SELECT shortcode_slug, is_active FROM $table_name WHERE shortcode_id = %d",
            $shortcode_id
        ));
        
        $result = $wpdb->delete(
            $table_name,
            array('shortcode_id' => $shortcode_id),
            array('%d')
        );
        
        if ($result) {
            // If the shortcode was active, unregister it
            if ($shortcode && $shortcode->is_active == 1) {
                // Include plugin.php to ensure is_plugin_active is available
                if (!function_exists('is_plugin_active')) {
                    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                Grove_Zen_Shortcodes::unregister_single_shortcode($shortcode->shortcode_slug);
            }
            
            wp_send_json_success('Shortcode deleted successfully');
        } else {
            wp_send_json_error('Failed to delete shortcode');
        }
    }
}