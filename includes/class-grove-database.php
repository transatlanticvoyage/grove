<?php

/**
 * Grove Database Class
 * Handles shared database schema creation and management
 * Compatible with Snefuruplin database structure
 */
class Grove_Database {
    
    const ZEN_DB_VERSION = '1.3';
    
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
        
        // Insert default factory codes if table is empty
        $factory_codes_table = $wpdb->prefix . 'zen_factory_codes';
        $existing_codes_count = $wpdb->get_var("SELECT COUNT(*) FROM $factory_codes_table");
        
        if ($existing_codes_count == 0) {
            $phone_link_code = <<<'PHP'
// Sitespren Phone Link Shortcode Recovery Code
// Add this to your theme's functions.php if Grove/Snefuruplin plugins are removed

if (!function_exists('sitespren_phone_link_shortcode')) {
    function sitespren_phone_link_shortcode($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'prefix' => '+1',
            'text' => 'Call us: ',
            'class' => 'phone-link'
        ), $atts, 'sitespren_phone_link');
        
        // Direct database query fallback
        $table = $wpdb->prefix . 'zen_sitespren';
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_1 FROM $table WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if (empty($phone)) {
            return '<!-- No phone number found -->';
        }
        
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        
        return sprintf(
            '<a href="tel:%s%s" class="%s">%s%s</a>',
            esc_attr($atts['prefix']),
            esc_attr($clean_phone),
            esc_attr($atts['class']),
            esc_html($atts['text']),
            esc_html($phone)
        );
    }
    add_shortcode('sitespren_phone_link', 'sitespren_phone_link_shortcode');
}
PHP;

            $wpdb->insert(
                $factory_codes_table,
                array(
                    'code_slug' => 'sitespren_phone_link',
                    'code_type' => 'shortcode',
                    'code_title' => 'Sitespren Phone Link Shortcode',
                    'code_description' => 'Creates a clickable phone link using data from zen_sitespren table. Works as standalone recovery code if plugins are removed.',
                    'code_snippet' => $phone_link_code,
                    'usage_example' => '[sitespren_phone_link] or [sitespren_phone_link prefix="+1" text="Call now: "]',
                    'is_active' => 1,
                    'is_core' => 1,
                    'plugin_source' => 'both'
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s')
            );
        }
        
        // Insert default hoof codes if table is empty
        $hoof_codes_table = $wpdb->prefix . 'zen_hoof_codes';
        $existing_hoof_count = $wpdb->get_var("SELECT COUNT(*) FROM $hoof_codes_table");
        
        if ($existing_hoof_count == 0) {
            // Insert antelope_phone_piece
            $wpdb->insert(
                $hoof_codes_table,
                array(
                    'hoof_slug' => 'antelope_phone_piece',
                    'hoof_title' => 'Antelope Phone Piece',
                    'hoof_content' => '<div class="phone-number">[beginning_a_code_moose] Call us: [phone_local]</a></div>',
                    'hoof_description' => 'A complete phone link with local formatting wrapped in a div',
                    'is_active' => 1,
                    'is_system' => 1,
                    'position_order' => 1
                ),
                array('%s', '%s', '%s', '%s', '%d', '%d', '%d')
            );
            
            // Insert lamb_phone_piece
            $wpdb->insert(
                $hoof_codes_table,
                array(
                    'hoof_slug' => 'lamb_phone_piece',
                    'hoof_title' => 'Lamb Phone Piece',
                    'hoof_content' => '<button class="phone-button">[beginning_a_code_moose]📞 [phone_international]</a></button>',
                    'hoof_description' => 'A phone button with international formatting and emoji',
                    'is_active' => 1,
                    'is_system' => 1,
                    'position_order' => 2
                ),
                array('%s', '%s', '%s', '%s', '%d', '%d', '%d')
            );
        }
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
}