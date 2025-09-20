<?php

/**
 * Grove Database Class
 * Handles shared database schema creation and management
 * Compatible with Snefuruplin database structure
 */
class Grove_Database {
    
    const ZEN_DB_VERSION = '1.7';
    
    public function __construct() {
        // Check and create tables on initialization
        add_action('init', array($this, 'maybe_create_tables'));
        
        // Hook into plugin activation
        register_activation_hook(GROVE_PLUGIN_PATH . 'grove.php', array($this, 'on_activation'));
    }
    
    /**
     * Plugin activation hook
     */
    public function on_activation() {
        $this->create_zen_tables();
        $this->maybe_insert_default_data();
    }
    
    /**
     * Check if tables need to be created or updated
     */
    public function maybe_create_tables() {
        $installed_version = get_option('zen_db_version', '0');
        
        if (version_compare($installed_version, self::ZEN_DB_VERSION, '<')) {
            $this->create_zen_tables();
            update_option('zen_db_version', self::ZEN_DB_VERSION);
        }
    }
    
    /**
     * Create all zen tables with shared schema
     * Uses CREATE TABLE IF NOT EXISTS for safe operation
     */
    public function create_zen_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Include WordPress upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Create zen_sitespren table
        $this->create_sitespren_table($charset_collate);
        
        // Create zen_services table
        $this->create_services_table($charset_collate);
        
        // Create zen_locations table
        $this->create_locations_table($charset_collate);
        
        // Create zen_factory_codes table
        $this->create_factory_codes_table($charset_collate);
        
        // Create zen_hoof_codes table
        $this->create_hoof_codes_table($charset_collate);
        
        // Create zen_lighthouse_friendly_names table
        $this->create_lighthouse_friendly_names_table($charset_collate);
        
        // Create zen_general_shortcodes table
        $this->create_general_shortcodes_table($charset_collate);
    }
    
    /**
     * Create zen_sitespren table
     */
    private function create_sitespren_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_sitespren';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            wppma_id int(11) NOT NULL AUTO_INCREMENT,
            wppma_db_only_created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            wppma_db_only_updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            id varchar(255) NOT NULL,
            created_at timestamp NULL,
            sitespren_base varchar(255) DEFAULT NULL,
            true_root_domain varchar(255) DEFAULT NULL,
            full_subdomain varchar(255) DEFAULT NULL,
            webproperty_type varchar(255) DEFAULT NULL,
            fk_users_id varchar(255) DEFAULT NULL,
            updated_at timestamp NULL,
            wpuser1 varchar(255) DEFAULT NULL,
            wppass1 varchar(255) DEFAULT NULL,
            wp_plugin_installed1 tinyint(1) DEFAULT NULL,
            wp_plugin_connected2 tinyint(1) DEFAULT NULL,
            fk_domreg_hostaccount varchar(255) DEFAULT NULL,
            is_wp_site tinyint(1) DEFAULT NULL,
            wp_rest_app_pass varchar(255) DEFAULT NULL,
            driggs_industry varchar(255) DEFAULT NULL,
            driggs_city varchar(255) DEFAULT NULL,
            driggs_brand_name varchar(255) DEFAULT NULL,
            driggs_site_type_purpose varchar(255) DEFAULT NULL,
            driggs_email_1 varchar(255) DEFAULT NULL,
            driggs_address_full text DEFAULT NULL,
            driggs_address_species_id int(11) DEFAULT NULL,
            driggs_phone_country_code int(11) DEFAULT 1,
            driggs_phone_1 varchar(255) DEFAULT NULL,
            driggs_phone1_platform_id int(11) DEFAULT NULL,
            driggs_cgig_id int(11) DEFAULT NULL,
            driggs_citations_done tinyint(1) DEFAULT NULL,
            driggs_social_profiles_done tinyint(1) DEFAULT NULL,
            driggs_special_note_for_ai_tool text DEFAULT NULL,
            driggs_hours text DEFAULT NULL,
            driggs_owner_name varchar(255) DEFAULT NULL,
            driggs_short_descr text DEFAULT NULL,
            driggs_long_descr text DEFAULT NULL,
            driggs_year_opened int(11) DEFAULT NULL,
            driggs_employees_qty int(11) DEFAULT NULL,
            driggs_keywords text DEFAULT NULL,
            driggs_category varchar(255) DEFAULT NULL,
            driggs_address_species_note text DEFAULT NULL,
            driggs_payment_methods text DEFAULT NULL,
            driggs_social_media_links text DEFAULT NULL,
            driggs_street_1 varchar(255) DEFAULT NULL,
            driggs_street_2 varchar(255) DEFAULT NULL,
            driggs_state_code varchar(10) DEFAULT NULL,
            driggs_zip varchar(20) DEFAULT NULL,
            driggs_state_full varchar(255) DEFAULT NULL,
            driggs_country varchar(255) DEFAULT NULL,
            driggs_gmaps_widget_location_1 text DEFAULT NULL,
            driggs_revenue_goal decimal(15,2) DEFAULT NULL,
            ns_full varchar(255) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            is_starred1 varchar(10) DEFAULT NULL,
            icon_name varchar(255) DEFAULT NULL,
            icon_color varchar(50) DEFAULT NULL,
            is_bulldozer tinyint(1) DEFAULT 0,
            is_competitor tinyint(1) DEFAULT 0,
            is_external tinyint(1) DEFAULT 0,
            is_internal tinyint(1) DEFAULT 0,
            is_ppx tinyint(1) DEFAULT 0,
            is_ms tinyint(1) DEFAULT 0,
            is_wayback_rebuild tinyint(1) DEFAULT 0,
            is_naked_wp_build tinyint(1) DEFAULT 0,
            is_rnr tinyint(1) DEFAULT 0,
            is_aff tinyint(1) DEFAULT 0,
            is_other1 tinyint(1) DEFAULT 0,
            is_other2 tinyint(1) DEFAULT 0,
            snailimage varchar(255) DEFAULT NULL,
            snail_image_url text DEFAULT NULL,
            snail_image_status varchar(50) DEFAULT NULL,
            snail_image_error text DEFAULT NULL,
            screenshot_url text DEFAULT NULL,
            screenshot_taken_at timestamp NULL,
            screenshot_status varchar(50) DEFAULT NULL,
            rel_cncglub_id int(11) DEFAULT NULL,
            rel_city_id int(11) DEFAULT NULL,
            driggs_logo_url text DEFAULT NULL,
            driggs_social_profiles_done tinyint(1) DEFAULT 0,
            driggs_hours text DEFAULT NULL,
            driggs_owner_name varchar(255) DEFAULT NULL,
            driggs_short_descr text DEFAULT NULL,
            driggs_long_descr text DEFAULT NULL,
            driggs_year_opened int(11) DEFAULT NULL,
            driggs_employees_qty int(11) DEFAULT NULL,
            rel_industry_id int(11) DEFAULT NULL,
            PRIMARY KEY (wppma_id),
            UNIQUE KEY unique_id (id),
            INDEX idx_sitespren_base (sitespren_base),
            INDEX idx_fk_users_id (fk_users_id),
            INDEX idx_is_external (is_external),
            INDEX idx_is_internal (is_internal)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_services table
     */
    private function create_services_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_services';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            service_id int(11) NOT NULL AUTO_INCREMENT,
            service_name varchar(255) NOT NULL,
            service_placard varchar(255) DEFAULT NULL,
            service_moniker varchar(255) DEFAULT NULL,
            description1_short text DEFAULT NULL,
            description1_long text DEFAULT NULL,
            service_image_url text DEFAULT NULL,
            service_image_id int(11) DEFAULT NULL,
            is_pinned_service tinyint(1) DEFAULT 0,
            position_in_custom_order int(11) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (service_id),
            INDEX idx_service_name (service_name),
            INDEX idx_is_pinned (is_pinned_service),
            INDEX idx_position (position_in_custom_order)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_locations table
     */
    private function create_locations_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_locations';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            location_id int(11) NOT NULL AUTO_INCREMENT,
            location_name varchar(255) NOT NULL,
            location_placard varchar(255) DEFAULT NULL,
            location_moniker varchar(255) DEFAULT NULL,
            location_sobriquet varchar(255) DEFAULT NULL,
            street varchar(255) DEFAULT NULL,
            city varchar(255) DEFAULT NULL,
            state_code varchar(10) DEFAULT NULL,
            zip_code varchar(20) DEFAULT NULL,
            country varchar(255) DEFAULT 'USA',
            location_image_url text DEFAULT NULL,
            location_image_id int(11) DEFAULT NULL,
            is_pinned_location tinyint(1) DEFAULT 0,
            position_in_custom_order int(11) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (location_id),
            INDEX idx_location_name (location_name),
            INDEX idx_is_pinned (is_pinned_location),
            INDEX idx_position (position_in_custom_order),
            INDEX idx_city_state (city, state_code)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_factory_codes table
     */
    private function create_factory_codes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            code_id int(11) NOT NULL AUTO_INCREMENT,
            code_slug varchar(100) NOT NULL,
            code_type varchar(50) NOT NULL,
            code_title varchar(255) DEFAULT NULL,
            code_description text DEFAULT NULL,
            code_snippet mediumtext NOT NULL,
            dependencies text DEFAULT NULL,
            usage_example text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            is_core tinyint(1) DEFAULT 0,
            plugin_source varchar(100) DEFAULT NULL,
            wp_version_min varchar(10) DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (code_id),
            UNIQUE KEY unique_slug (code_slug),
            INDEX idx_type (code_type),
            INDEX idx_active (is_active),
            INDEX idx_source (plugin_source)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_hoof_codes table for dynamic shortcodes
     */
    private function create_hoof_codes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_hoof_codes';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            hoof_id int(11) NOT NULL AUTO_INCREMENT,
            hoof_slug varchar(100) NOT NULL,
            hoof_title varchar(255) DEFAULT NULL,
            hoof_content mediumtext NOT NULL,
            hoof_description text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            is_system tinyint(1) DEFAULT 0,
            position_order int(11) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (hoof_id),
            UNIQUE KEY unique_slug (hoof_slug),
            INDEX idx_active (is_active),
            INDEX idx_system (is_system),
            INDEX idx_position (position_order)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_lighthouse_friendly_names table for field label mapping
     */
    private function create_lighthouse_friendly_names_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_lighthouse_friendly_names';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            fname_id int(11) NOT NULL AUTO_INCREMENT,
            db_table_name text NOT NULL,
            db_column_name text NOT NULL,
            friendly_name_1_datum text NOT NULL,
            friendly_name_1_custom_position_a int(11) DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (fname_id),
            INDEX idx_table_column (db_table_name(100), db_column_name(100))
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Create zen_general_shortcodes table for user-defined shortcodes
     */
    private function create_general_shortcodes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            shortcode_id int(11) NOT NULL AUTO_INCREMENT,
            shortcode_name text NOT NULL,
            shortcode_slug text NOT NULL,
            shortcode_content longtext NOT NULL,
            shortcode_description text DEFAULT NULL,
            shortcode_category text DEFAULT NULL,
            shortcode_type text DEFAULT 'custom',
            shortcode_usage_example text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            is_system tinyint(1) DEFAULT 0,
            is_global tinyint(1) DEFAULT 0,
            is_adminpublic tinyint(1) DEFAULT 0,
            position_order int(11) DEFAULT 0,
            author_user_id bigint(20) DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (shortcode_id),
            INDEX idx_shortcode_slug (shortcode_slug(100)),
            INDEX idx_active (is_active),
            INDEX idx_system (is_system),
            INDEX idx_global (is_global),
            INDEX idx_adminpublic (is_adminpublic),
            INDEX idx_category (shortcode_category(100)),
            INDEX idx_type (shortcode_type(50)),
            INDEX idx_position (position_order),
            INDEX idx_author (author_user_id)
        ) $charset_collate;";
        
        dbDelta($sql);
    }
    
    /**
     * Insert default data if tables are empty
     */
    private function maybe_insert_default_data() {
        global $wpdb;
        
        // Create default sitespren record for current site
        $sitespren_table = $wpdb->prefix . 'zen_sitespren';
        $existing_count = $wpdb->get_var("SELECT COUNT(*) FROM $sitespren_table");
        
        if ($existing_count == 0) {
            $site_url = get_site_url();
            $parsed_url = parse_url($site_url);
            $domain = isset($parsed_url['host']) ? $parsed_url['host'] : 'localhost';
            
            $wpdb->insert(
                $sitespren_table,
                array(
                    'id' => wp_generate_uuid4(),
                    'sitespren_base' => $domain,
                    'driggs_brand_name' => get_option('blogname', 'My Site'),
                    'driggs_site_type_purpose' => 'WordPress Site',
                    'driggs_phone_country_code' => 1,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s', '%s', '%d', '%s', '%s')
            );
        }
        
        // Factory codes table creation - no default PHP code insertion
        
        // Insert default hoof codes using version-based migration
        $this->migrate_default_hoof_codes();
        
        // Insert default friendly names using version-based migration
        $this->migrate_default_friendly_names();
        
        // Insert default general shortcodes using version-based migration
        $this->migrate_default_general_shortcodes();
    }
    
    /**
     * Get services data (compatible with Snefuruplin format)
     */
    public static function get_services($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'position_in_custom_order',
            'order' => 'ASC',
            'pinned_first' => true,
            'limit' => -1
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_services';
        
        $sql = "SELECT * FROM $table_name";
        
        // Add ORDER BY clause
        if ($args['pinned_first']) {
            $sql .= " ORDER BY is_pinned_service DESC, " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        } else {
            $sql .= " ORDER BY " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        }
        
        // Add LIMIT if specified
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get locations data (compatible with Snefuruplin format)
     */
    public static function get_locations($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'position_in_custom_order',
            'order' => 'ASC',
            'pinned_first' => true,
            'limit' => -1
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_locations';
        
        $sql = "SELECT * FROM $table_name";
        
        // Add ORDER BY clause
        if ($args['pinned_first']) {
            $sql .= " ORDER BY is_pinned_location DESC, " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        } else {
            $sql .= " ORDER BY " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        }
        
        // Add LIMIT if specified
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get single service by ID
     */
    public static function get_service($service_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_services';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE service_id = %d",
            $service_id
        ));
    }
    
    /**
     * Get single location by ID
     */
    public static function get_location($location_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_locations';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE location_id = %d",
            $location_id
        ));
    }
    
    /**
     * Get sitespren data
     */
    public static function get_sitespren($wppma_id = 1) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_sitespren';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE wppma_id = %d",
            $wppma_id
        ));
    }
    
    /**
     * Get factory codes
     */
    public static function get_factory_codes($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'code_id',
            'order' => 'ASC',
            'type' => '',
            'source' => '',
            'active_only' => false,
            'limit' => -1
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        
        $sql = "SELECT * FROM $table_name WHERE 1=1";
        
        // Add filters
        if (!empty($args['type'])) {
            $sql .= $wpdb->prepare(" AND code_type = %s", $args['type']);
        }
        
        if (!empty($args['source'])) {
            $sql .= $wpdb->prepare(" AND plugin_source = %s", $args['source']);
        }
        
        if ($args['active_only']) {
            $sql .= " AND is_active = 1";
        }
        
        // Add ORDER BY clause
        $sql .= " ORDER BY " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        
        // Add LIMIT if specified
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get single factory code by ID
     */
    public static function get_factory_code($code_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE code_id = %d",
            $code_id
        ));
    }
    
    /**
     * Get factory code by slug
     */
    public static function get_factory_code_by_slug($code_slug) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE code_slug = %s",
            $code_slug
        ));
    }
    
    /**
     * Insert factory code
     */
    public static function insert_factory_code($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        return $wpdb->insert($table_name, $data);
    }
    
    /**
     * Update factory code
     */
    public static function update_factory_code($code_id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        return $wpdb->update($table_name, $data, array('code_id' => $code_id));
    }
    
    /**
     * Delete factory code
     */
    public static function delete_factory_code($code_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_factory_codes';
        return $wpdb->delete($table_name, array('code_id' => $code_id));
    }
    
    /**
     * Version-based migration system for default hoof codes
     * Tracks which default entries have been installed and adds new ones on updates
     */
    private function migrate_default_hoof_codes() {
        global $wpdb;
        
        $hoof_codes_table = $wpdb->prefix . 'zen_hoof_codes';
        $installed_defaults = get_option('grove_hoof_defaults_version', '0.0.0');
        
        // Define default hoof codes with their introduction versions
        $default_codes = array(
            '1.0.0' => array(
                array(
                    'hoof_slug' => 'antelope_phone_piece',
                    'hoof_title' => 'Antelope Phone Piece',
                    'hoof_content' => '<div class="phone-number">[beginning_a_code_moose] Call us: [phone_local]</a></div>',
                    'hoof_description' => 'A complete phone link with local formatting wrapped in a div',
                    'is_active' => 1,
                    'is_system' => 1,
                    'position_order' => 1
                ),
                array(
                    'hoof_slug' => 'lamb_phone_piece',
                    'hoof_title' => 'Lamb Phone Piece',
                    'hoof_content' => '<button class="phone-button">[beginning_a_code_moose]📞 [phone_international]</a></button>',
                    'hoof_description' => 'A phone button with international formatting and emoji',
                    'is_active' => 1,
                    'is_system' => 1,
                    'position_order' => 2
                )
            ),
            '1.1.0' => array(
                array(
                    'hoof_slug' => 'muskox_phone_hub',
                    'hoof_title' => 'Muskox Phone Hub',
                    'hoof_content' => '<div class="phone-number">[beginning_a_code_moose] Call us: [phone_local]</a></div>',
                    'hoof_description' => 'A phone hub with local formatting and call-to-action text',
                    'is_active' => 1,
                    'is_system' => 1,
                    'position_order' => 3
                )
            )
        );
        
        // Install missing default codes from each version
        foreach ($default_codes as $version => $codes) {
            if (version_compare($installed_defaults, $version, '<')) {
                foreach ($codes as $code) {
                    // Check if this specific code already exists
                    $exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $hoof_codes_table WHERE hoof_slug = %s",
                        $code['hoof_slug']
                    ));
                    
                    if (!$exists) {
                        $wpdb->insert(
                            $hoof_codes_table,
                            $code,
                            array('%s', '%s', '%s', '%s', '%d', '%d', '%d')
                        );
                    }
                }
            }
        }
        
        // Update the installed defaults version
        update_option('grove_hoof_defaults_version', '1.1.0');
    }
    
    /**
     * Version-based migration system for default friendly names
     * Tracks which default entries have been installed and adds new ones on updates
     */
    private function migrate_default_friendly_names() {
        global $wpdb;
        
        $friendly_names_table = $wpdb->prefix . 'zen_lighthouse_friendly_names';
        $installed_defaults_version = get_option('grove_friendly_names_defaults_version', '0.0.0');
        
        // Define default friendly names with their introduction versions
        $default_friendly_names = array(
            '1.1.0' => array(
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_brand_name',
                    'friendly_name_1_datum' => 'Business Name',
                    'friendly_name_1_custom_position_a' => 1
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_street_1',
                    'friendly_name_1_datum' => 'Street Address',
                    'friendly_name_1_custom_position_a' => 2
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_city',
                    'friendly_name_1_datum' => 'City',
                    'friendly_name_1_custom_position_a' => 3
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_state_full',
                    'friendly_name_1_datum' => 'State',
                    'friendly_name_1_custom_position_a' => 4
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_zip',
                    'friendly_name_1_datum' => 'Zip / Postal code',
                    'friendly_name_1_custom_position_a' => 5
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_country',
                    'friendly_name_1_datum' => 'Country',
                    'friendly_name_1_custom_position_a' => 6
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_phone_1',
                    'friendly_name_1_datum' => 'Phone',
                    'friendly_name_1_custom_position_a' => 7
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'sitespren_base',
                    'friendly_name_1_datum' => 'Website',
                    'friendly_name_1_custom_position_a' => 8
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_email_1',
                    'friendly_name_1_datum' => 'Business Email',
                    'friendly_name_1_custom_position_a' => 9
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_short_descr',
                    'friendly_name_1_datum' => 'Short Description (less than 250 word)',
                    'friendly_name_1_custom_position_a' => 10
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_long_descr',
                    'friendly_name_1_datum' => 'Full Description',
                    'friendly_name_1_custom_position_a' => 11
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_hours',
                    'friendly_name_1_datum' => 'Operating Hours',
                    'friendly_name_1_custom_position_a' => 12
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_keywords',
                    'friendly_name_1_datum' => 'Keywords',
                    'friendly_name_1_custom_position_a' => 13
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_category',
                    'friendly_name_1_datum' => 'Category',
                    'friendly_name_1_custom_position_a' => 14
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_owner_name',
                    'friendly_name_1_datum' => 'Owner Name',
                    'friendly_name_1_custom_position_a' => 15
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_year_opened',
                    'friendly_name_1_datum' => 'Starting year of the business',
                    'friendly_name_1_custom_position_a' => 16
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_employees_qty',
                    'friendly_name_1_datum' => 'Number of Employee',
                    'friendly_name_1_custom_position_a' => 17
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_payment_methods',
                    'friendly_name_1_datum' => 'Payment Method',
                    'friendly_name_1_custom_position_a' => 18
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_social_media_links',
                    'friendly_name_1_datum' => 'Social Media Links (GooglePlus, Facebook, Twitter etc..)',
                    'friendly_name_1_custom_position_a' => 19
                ),
                array(
                    'db_table_name' => 'sitespren',
                    'db_column_name' => 'driggs_logo_url',
                    'friendly_name_1_datum' => 'Logo and images',
                    'friendly_name_1_custom_position_a' => 20
                )
            )
        );
        
        // Install missing default friendly names from each version
        foreach ($default_friendly_names as $version => $names) {
            if (version_compare($installed_defaults_version, $version, '<')) {
                foreach ($names as $name_data) {
                    // Check if this specific friendly name already exists
                    $exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $friendly_names_table 
                         WHERE db_table_name = %s AND db_column_name = %s",
                        $name_data['db_table_name'],
                        $name_data['db_column_name']
                    ));
                    
                    if (!$exists) {
                        // Prepare format array based on available fields
                        $formats = array('%s', '%s', '%s');
                        if (isset($name_data['friendly_name_1_custom_position_a'])) {
                            $formats[] = '%d';
                        }
                        
                        $wpdb->insert(
                            $friendly_names_table,
                            $name_data,
                            $formats
                        );
                    } else {
                        // Update existing records with new data if version changed
                        $update_data = array('friendly_name_1_datum' => $name_data['friendly_name_1_datum']);
                        $update_formats = array('%s');
                        
                        if (isset($name_data['friendly_name_1_custom_position_a'])) {
                            $update_data['friendly_name_1_custom_position_a'] = $name_data['friendly_name_1_custom_position_a'];
                            $update_formats[] = '%d';
                        }
                        
                        $wpdb->update(
                            $friendly_names_table,
                            $update_data,
                            array(
                                'db_table_name' => $name_data['db_table_name'],
                                'db_column_name' => $name_data['db_column_name']
                            ),
                            $update_formats,
                            array('%s', '%s')
                        );
                    }
                }
            }
        }
        
        // Update the installed defaults version
        update_option('grove_friendly_names_defaults_version', '1.1.0');
    }
    
    /**
     * Version-based migration system for default general shortcodes
     * Tracks which default entries have been installed and adds new ones on updates
     */
    private function migrate_default_general_shortcodes() {
        global $wpdb;
        
        $general_shortcodes_table = $wpdb->prefix . 'zen_general_shortcodes';
        $installed_defaults_version = get_option('grove_general_shortcodes_defaults_version', '0.0.0');
        
        // Define default general shortcodes with their introduction versions
        $default_shortcodes = array(
            '1.0.0' => array(
                array(
                    'shortcode_name' => 'Zen Service Box',
                    'shortcode_slug' => 'zen_service_box',
                    'shortcode_content' => '<div class="zen-service-box" style="border: 1px solid #ddd; padding: 20px; margin: 10px; border-radius: 8px; text-align: center;">
    <div class="service-image" style="margin-bottom: 15px;">
        [zen_service id="{id}" field="service_image"]
    </div>
    <h3 class="service-name" style="margin-bottom: 10px; color: #333;">
        [zen_service id="{id}" field="service_name"]
    </h3>
    <p class="service-description" style="margin-bottom: 15px; color: #666;">
        [zen_service id="{id}" field="description1_short"]
    </p>
    <a href="#" class="service-button" style="background: #0073aa; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; display: inline-block;">
        Learn More
    </a>
</div>',
                    'shortcode_description' => 'A complete service box with image, heading, description and button for Elementor. Replace {id} with actual service ID.',
                    'shortcode_category' => 'elementor',
                    'shortcode_type' => 'template',
                    'shortcode_usage_example' => '[zen_service_box id="1"]',
                    'is_active' => 1,
                    'is_system' => 1,
                    'is_global' => 1,
                    'is_adminpublic' => 1,
                    'position_order' => 1
                ),
            )
        );
        
        // Install missing default shortcodes from each version
        foreach ($default_shortcodes as $version => $shortcodes) {
            if (version_compare($installed_defaults_version, $version, '<')) {
                foreach ($shortcodes as $shortcode) {
                    // Check if this specific shortcode already exists
                    $exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $general_shortcodes_table WHERE shortcode_slug = %s",
                        $shortcode['shortcode_slug']
                    ));
                    
                    if (!$exists) {
                        $wpdb->insert(
                            $general_shortcodes_table,
                            $shortcode,
                            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d')
                        );
                    }
                }
            }
        }
        
        // Update the installed defaults version
        update_option('grove_general_shortcodes_defaults_version', '1.0.0');
    }
    
    /**
     * Get friendly name for a database field
     */
    public static function get_friendly_name($table_name, $column_name) {
        global $wpdb;
        
        // Remove zen_ prefix if present for table name lookup
        $table_name = str_replace('zen_', '', $table_name);
        
        $friendly_names_table = $wpdb->prefix . 'zen_lighthouse_friendly_names';
        
        $friendly_name = $wpdb->get_var($wpdb->prepare(
            "SELECT friendly_name_1_datum FROM $friendly_names_table 
             WHERE db_table_name = %s AND db_column_name = %s",
            $table_name,
            $column_name
        ));
        
        // Return friendly name if found, otherwise return blank
        return $friendly_name ? $friendly_name : '';
    }
    
    /**
     * Get all friendly names for a table
     */
    public static function get_table_friendly_names($table_name) {
        global $wpdb;
        
        // Remove zen_ prefix if present for table name lookup
        $table_name = str_replace('zen_', '', $table_name);
        
        $friendly_names_table = $wpdb->prefix . 'zen_lighthouse_friendly_names';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $friendly_names_table 
             WHERE db_table_name = %s 
             ORDER BY fname_id ASC",
            $table_name
        ));
    }
    
    /**
     * Update or insert a friendly name
     */
    public static function upsert_friendly_name($table_name, $column_name, $friendly_name) {
        global $wpdb;
        
        // Remove zen_ prefix if present for table name lookup
        $table_name = str_replace('zen_', '', $table_name);
        
        $friendly_names_table = $wpdb->prefix . 'zen_lighthouse_friendly_names';
        
        // Check if entry exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT fname_id FROM $friendly_names_table 
             WHERE db_table_name = %s AND db_column_name = %s",
            $table_name,
            $column_name
        ));
        
        if ($exists) {
            // Update existing
            return $wpdb->update(
                $friendly_names_table,
                array('friendly_name_1_datum' => $friendly_name),
                array(
                    'db_table_name' => $table_name,
                    'db_column_name' => $column_name
                ),
                array('%s'),
                array('%s', '%s')
            );
        } else {
            // Insert new
            return $wpdb->insert(
                $friendly_names_table,
                array(
                    'db_table_name' => $table_name,
                    'db_column_name' => $column_name,
                    'friendly_name_1_datum' => $friendly_name
                ),
                array('%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Get general shortcodes
     */
    public static function get_general_shortcodes($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'position_order',
            'order' => 'ASC',
            'type' => '',
            'category' => '',
            'active_only' => false,
            'system_only' => false,
            'limit' => -1,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        $sql = "SELECT * FROM $table_name WHERE 1=1";
        
        // Add filters
        if (!empty($args['type'])) {
            $sql .= $wpdb->prepare(" AND shortcode_type = %s", $args['type']);
        }
        
        if (!empty($args['category'])) {
            $sql .= $wpdb->prepare(" AND shortcode_category = %s", $args['category']);
        }
        
        if ($args['active_only']) {
            $sql .= " AND is_active = 1";
        }
        
        if ($args['system_only']) {
            $sql .= " AND is_system = 1";
        }
        
        // Add ORDER BY clause
        $sql .= " ORDER BY " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        
        // Add LIMIT and OFFSET if specified
        if ($args['limit'] > 0) {
            $sql .= " LIMIT " . intval($args['limit']);
            if ($args['offset'] > 0) {
                $sql .= " OFFSET " . intval($args['offset']);
            }
        }
        
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get single general shortcode by ID
     */
    public static function get_general_shortcode($shortcode_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE shortcode_id = %d",
            $shortcode_id
        ));
    }
    
    /**
     * Get general shortcode by slug
     */
    public static function get_general_shortcode_by_slug($shortcode_slug) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE shortcode_slug = %s",
            $shortcode_slug
        ));
    }
    
    /**
     * Insert general shortcode
     */
    public static function insert_general_shortcode($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        return $wpdb->insert($table_name, $data);
    }
    
    /**
     * Update general shortcode
     */
    public static function update_general_shortcode($shortcode_id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        return $wpdb->update($table_name, $data, array('shortcode_id' => $shortcode_id));
    }
    
    /**
     * Delete general shortcode
     */
    public static function delete_general_shortcode($shortcode_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        return $wpdb->delete($table_name, array('shortcode_id' => $shortcode_id));
    }
    
    /**
     * Get general shortcodes count
     */
    public static function get_general_shortcodes_count($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'type' => '',
            'category' => '',
            'active_only' => false,
            'system_only' => false
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        $sql = "SELECT COUNT(*) FROM $table_name WHERE 1=1";
        
        // Add filters
        if (!empty($args['type'])) {
            $sql .= $wpdb->prepare(" AND shortcode_type = %s", $args['type']);
        }
        
        if (!empty($args['category'])) {
            $sql .= $wpdb->prepare(" AND shortcode_category = %s", $args['category']);
        }
        
        if ($args['active_only']) {
            $sql .= " AND is_active = 1";
        }
        
        if ($args['system_only']) {
            $sql .= " AND is_system = 1";
        }
        
        return $wpdb->get_var($sql);
    }
}