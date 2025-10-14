<?php

/**
 * Grove Zen Shortcodes Class
 * Handles WordPress shortcodes for zen_services and zen_locations data
 */
class Grove_Zen_Shortcodes {
    
    public function __construct() {
        // Don't auto-register shortcodes - let the commander control it
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('init', array($this, 'maybe_register_shortcodes'), 15); // Later priority
    }
    
    /**
     * Maybe register shortcodes based on Grove settings
     */
    public function maybe_register_shortcodes() {
        $mode = get_option('grove_shortcode_mode', 'automatic');
        
        switch ($mode) {
            case 'automatic':
                // Default: Only if Snefuruplin not active
                if (!function_exists('is_plugin_active')) {
                    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }
                if (!is_plugin_active('snefuruplin/snefuruplin.php')) {
                    $this->register_shortcodes();
                }
                break;
                
            case 'force_active':
                // Always register (override Snefuruplin)
                $this->register_shortcodes();
                break;
                
            case 'disabled':
                // Never register
                break;
        }
    }
    
    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        // Services shortcodes
        add_shortcode('zen_services', array($this, 'render_services'));
        add_shortcode('zen_service', array($this, 'render_single_service'));
        add_shortcode('zen_service_image', array($this, 'render_service_image'));
        
        // Dynamic shortcodes from database
        $this->register_dynamic_shortcodes();
        
        // Locations shortcodes
        add_shortcode('zen_locations', array($this, 'render_locations'));
        add_shortcode('zen_location', array($this, 'render_single_location'));
        add_shortcode('zen_location_image', array($this, 'render_location_image'));
        
        // Utility shortcodes
        add_shortcode('zen_pinned_services', array($this, 'render_pinned_services'));
        add_shortcode('zen_pinned_locations', array($this, 'render_pinned_locations'));
        
        // Sitespren shortcode
        add_shortcode('sitespren', array($this, 'render_sitespren'));
        
        // Buffalo shortcodes  
        add_shortcode('buffalo_phone_number', array($this, 'render_buffalo_phone_number'));
        
        // Phone shortcodes
        add_shortcode('phone_local', array($this, 'render_phone_local'));
        add_shortcode('phone_international', array($this, 'render_phone_international'));
        add_shortcode('phone_link', array($this, 'render_phone_link'));
        add_shortcode('beginning_a_code_moose', array($this, 'render_beginning_a_code_moose'));
        
        // Special phone href shortcode for Elementor Dynamic Tags
        add_shortcode('special_phone_href_value_1', array($this, 'render_special_phone_href_value_1'));
        
        // Special phone href shortcode for raw HTML (closing shortcode)
        add_shortcode('special_phone_href_for_raw_html', array($this, 'render_special_phone_href_for_raw_html'));
        
        // Raven contact link shortcode for Elementor Dynamic Tags
        add_shortcode('raven_contact_link', array($this, 'render_raven_contact_link'));
        
        // Factory codes shortcodes
        add_shortcode('sitespren_phone_link', array($this, 'render_sitespren_phone_link'));
        
        // Fragment shortcodes  
        add_shortcode('panama_fragment_insert', array($this, 'render_panama_fragment'));
        add_shortcode('senegal_fragment_insert', array($this, 'render_senegal_fragment'));
        
        // Sitemap shortcode
        add_shortcode('sitemap_method_1', array($this, 'render_sitemap_method_1'));
        
        // Register dynamic hoof shortcodes
        $this->register_hoof_shortcodes();
    }
    
    /**
     * Check if Grove is controlling shortcodes
     */
    public function is_grove_controlling_shortcodes() {
        $mode = get_option('grove_shortcode_mode', 'automatic');
        
        if ($mode === 'force_active') {
            return true;
        } elseif ($mode === 'automatic') {
            if (!function_exists('is_plugin_active')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            return !is_plugin_active('snefuruplin/snefuruplin.php');
        }
        
        return false;
    }
    
    /**
     * Force register all shortcodes (for testing/debugging)
     */
    public static function force_register_all() {
        $instance = new self();
        $instance->register_shortcodes();
    }
    
    
    /**
     * Enqueue shortcodes CSS only when Grove is controlling
     */
    public function enqueue_styles() {
        if ($this->is_grove_controlling_shortcodes()) {
            wp_enqueue_style(
                'grove-zen-shortcodes',
                GROVE_PLUGIN_URL . 'assets/zen-shortcodes.css',
                array(),
                GROVE_PLUGIN_VERSION
            );
        }
    }
    
    /**
     * Render services list
     * Usage: [zen_services limit="5" template="grid" show_images="true" pinned_first="true"]
     */
    public function render_services($atts) {
        $atts = shortcode_atts(array(
            // Single service parameters
            'id' => '', // Legacy parameter
            'service_id' => '', // New parameter name
            'field' => '', // Legacy parameter for column
            'dbcol' => '', // New parameter for column
            // List parameters
            'limit' => -1,
            'template' => 'list', // list, grid, cards, full
            'show_images' => 'false',
            'show_image' => 'true', // For single service compatibility
            'pinned_first' => 'true',
            'order' => 'ASC',
            'orderby' => 'position_in_custom_order',
            'class' => '',
            'image_size' => 'thumbnail'
        ), $atts, 'zen_services');
        
        // Check if this is a single service request
        $service_id = !empty($atts['service_id']) ? $atts['service_id'] : $atts['id'];
        if (!empty($service_id)) {
            // Call the single service handler
            return $this->render_single_service($atts);
        }
        
        $services = Grove_Database::get_services(array(
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'pinned_first' => $atts['pinned_first'] === 'true'
        ));
        
        if (empty($services)) {
            return '<p>No services found.</p>';
        }
        
        // Apply limit if specified
        if ($atts['limit'] > 0) {
            $services = array_slice($services, 0, $atts['limit']);
        }
        
        $output = '<div class="zen-services-wrapper ' . esc_attr($atts['class']) . ' zen-template-' . esc_attr($atts['template']) . '">';
        
        foreach ($services as $service) {
            $output .= $this->render_service_item($service, $atts);
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render single service
     * Usage: [zen_service id="1" dbcol="service_name"] or [zen_service service_id="1" field="service_name"]
     */
    public function render_single_service($atts) {
        $atts = shortcode_atts(array(
            'id' => '', // Legacy parameter
            'service_id' => '', // New parameter name
            'field' => '', // Legacy parameter for column
            'dbcol' => '', // New parameter for column
            'template' => 'full',
            'show_image' => 'true',
            'image_size' => 'medium',
            'class' => ''
        ), $atts, 'zen_service');
        
        // Support both 'service_id' (new) and 'id' (legacy) parameters
        $service_id = !empty($atts['service_id']) ? $atts['service_id'] : $atts['id'];
        
        // Support both 'dbcol' (new) and 'field' (legacy) parameters
        $column = !empty($atts['dbcol']) ? $atts['dbcol'] : $atts['field'];
        
        if (empty($service_id)) {
            return '<p>Service ID is required.</p>';
        }
        
        $service = Grove_Database::get_service($service_id);
        
        if (!$service) {
            return '<p>Service not found.</p>';
        }
        
        // If specific field requested, return just that field
        if (!empty($column)) {
            return isset($service->{$column}) ? esc_html($service->{$column}) : '';
        }
        
        return $this->render_service_item($service, $atts);
    }
    
    /**
     * Render service image
     * Usage: [zen_service_image id="1" size="medium" alt="Custom alt text"]
     */
    public function render_service_image($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'size' => 'medium',
            'alt' => '',
            'class' => 'zen-service-image',
            'link' => 'false'
        ), $atts, 'zen_service_image');
        
        if (empty($atts['id'])) {
            return '';
        }
        
        $service = $this->get_service($atts['id']);
        
        if (!$service || !$service->rel_image1_id) {
            return '';
        }
        
        $image_html = wp_get_attachment_image(
            $service->rel_image1_id, 
            $atts['size'], 
            false, 
            array(
                'class' => $atts['class'],
                'alt' => !empty($atts['alt']) ? $atts['alt'] : $service->service_name
            )
        );
        
        if ($atts['link'] === 'true') {
            $image_url = wp_get_attachment_image_url($service->rel_image1_id, 'full');
            $image_html = '<a href="' . esc_url($image_url) . '" target="_blank">' . $image_html . '</a>';
        }
        
        return $image_html;
    }
    
    /**
     * Render locations list
     * Usage: [zen_locations limit="3" template="grid" show_images="true"]
     */
    public function render_locations($atts) {
        $atts = shortcode_atts(array(
            // Single location parameters
            'id' => '', // Legacy parameter
            'location_id' => '', // New parameter name
            'field' => '', // Legacy parameter for column
            'dbcol' => '', // New parameter for column
            // List parameters
            'limit' => -1,
            'template' => 'list', // list, grid, cards, full
            'show_images' => 'false',
            'show_image' => 'true', // For single location compatibility
            'pinned_first' => 'true',
            'order' => 'ASC',
            'orderby' => 'position_in_custom_order',
            'class' => '',
            'image_size' => 'thumbnail'
        ), $atts, 'zen_locations');
        
        // Check if this is a single location request
        $location_id = !empty($atts['location_id']) ? $atts['location_id'] : $atts['id'];
        if (!empty($location_id)) {
            // Call the single location handler
            return $this->render_single_location($atts);
        }
        
        $locations = Grove_Database::get_locations(array(
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'pinned_first' => $atts['pinned_first'] === 'true'
        ));
        
        if (empty($locations)) {
            return '<p>No locations found.</p>';
        }
        
        if ($atts['limit'] > 0) {
            $locations = array_slice($locations, 0, $atts['limit']);
        }
        
        $output = '<div class="zen-locations-wrapper ' . esc_attr($atts['class']) . ' zen-template-' . esc_attr($atts['template']) . '">';
        
        foreach ($locations as $location) {
            $output .= $this->render_location_item($location, $atts);
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render single location
     * Usage: [zen_location id="1" dbcol="location_name"] or [zen_location location_id="1" field="location_name"]
     */
    public function render_single_location($atts) {
        $atts = shortcode_atts(array(
            'id' => '', // Legacy parameter
            'location_id' => '', // New parameter name
            'field' => '', // Legacy parameter for column
            'dbcol' => '', // New parameter for column
            'template' => 'full',
            'show_image' => 'true',
            'image_size' => 'medium',
            'class' => ''
        ), $atts, 'zen_location');
        
        // Support both 'location_id' (new) and 'id' (legacy) parameters
        $location_id = !empty($atts['location_id']) ? $atts['location_id'] : $atts['id'];
        
        // Support both 'dbcol' (new) and 'field' (legacy) parameters
        $column = !empty($atts['dbcol']) ? $atts['dbcol'] : $atts['field'];
        
        if (empty($location_id)) {
            return '<p>Location ID is required.</p>';
        }
        
        $location = Grove_Database::get_location($location_id);
        
        if (!$location) {
            return '<p>Location not found.</p>';
        }
        
        if (!empty($column)) {
            return isset($location->{$column}) ? esc_html($location->{$column}) : '';
        }
        
        return $this->render_location_item($location, $atts);
    }
    
    /**
     * Render location image
     * Usage: [zen_location_image id="1" size="large"]
     */
    public function render_location_image($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'size' => 'medium',
            'alt' => '',
            'class' => 'zen-location-image',
            'link' => 'false'
        ), $atts, 'zen_location_image');
        
        if (empty($atts['id'])) {
            return '';
        }
        
        $location = $this->get_location($atts['id']);
        
        if (!$location || !$location->rel_image1_id) {
            return '';
        }
        
        $image_html = wp_get_attachment_image(
            $location->rel_image1_id, 
            $atts['size'], 
            false, 
            array(
                'class' => $atts['class'],
                'alt' => !empty($atts['alt']) ? $atts['alt'] : $location->location_name
            )
        );
        
        if ($atts['link'] === 'true') {
            $image_url = wp_get_attachment_image_url($location->rel_image1_id, 'full');
            $image_html = '<a href="' . esc_url($image_url) . '" target="_blank">' . $image_html . '</a>';
        }
        
        return $image_html;
    }
    
    /**
     * Render only pinned services
     * Usage: [zen_pinned_services template="cards" limit="3"]
     */
    public function render_pinned_services($atts) {
        $atts = shortcode_atts(array(
            'limit' => -1,
            'template' => 'list',
            'show_images' => 'true',
            'class' => 'zen-pinned-services'
        ), $atts, 'zen_pinned_services');
        
        global $wpdb;
        $table = $wpdb->prefix . 'zen_services';
        $services = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_pinned_service = 1 ORDER BY position_in_custom_order ASC"
        );
        
        if (empty($services)) {
            return '<p>No pinned services found.</p>';
        }
        
        if ($atts['limit'] > 0) {
            $services = array_slice($services, 0, $atts['limit']);
        }
        
        $output = '<div class="zen-services-wrapper ' . esc_attr($atts['class']) . ' zen-template-' . esc_attr($atts['template']) . '">';
        
        foreach ($services as $service) {
            $output .= $this->render_service_item($service, $atts);
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render only pinned locations
     * Usage: [zen_pinned_locations template="grid"]
     */
    public function render_pinned_locations($atts) {
        $atts = shortcode_atts(array(
            'limit' => -1,
            'template' => 'list',
            'show_images' => 'true',
            'class' => 'zen-pinned-locations'
        ), $atts, 'zen_pinned_locations');
        
        global $wpdb;
        $table = $wpdb->prefix . 'zen_locations';
        $locations = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_pinned_location = 1 ORDER BY position_in_custom_order ASC"
        );
        
        if (empty($locations)) {
            return '<p>No pinned locations found.</p>';
        }
        
        if ($atts['limit'] > 0) {
            $locations = array_slice($locations, 0, $atts['limit']);
        }
        
        $output = '<div class="zen-locations-wrapper ' . esc_attr($atts['class']) . ' zen-template-' . esc_attr($atts['template']) . '">';
        
        foreach ($locations as $location) {
            $output .= $this->render_location_item($location, $atts);
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render sitespren data
     * Usage: [sitespren dbcol="driggs_phone_1"] or [sitespren wppma_id="1" field="driggs_phone_1"]
     */
    public function render_sitespren($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1', // Default to 1 since table only has one row
            'field' => '', // Legacy parameter
            'dbcol' => '', // New parameter name
            'default' => '',
            'format' => '',
            'class' => ''
        ), $atts, 'sitespren');
        
        // Support both 'dbcol' (new) and 'field' (legacy) parameters
        $column = !empty($atts['dbcol']) ? $atts['dbcol'] : $atts['field'];
        
        // Validate required parameters
        if (empty($column)) {
            return '<span class="sitespren-error">Sitespren dbcol or field is required.</span>';
        }
        
        // Get the sitespren record
        $sitespren = Grove_Database::get_sitespren(intval($atts['wppma_id']));
        
        if (!$sitespren) {
            return !empty($atts['default']) ? esc_html($atts['default']) : '<span class="sitespren-not-found">Sitespren record not found.</span>';
        }
        
        // Check if field exists
        if (!property_exists($sitespren, $column)) {
            return !empty($atts['default']) ? esc_html($atts['default']) : '<span class="sitespren-no-field">Field not found.</span>';
        }
        
        // Get the field value
        $value = $sitespren->{$column};
        
        // Return default if value is empty
        if (empty($value) && !empty($atts['default'])) {
            $value = $atts['default'];
        }
        
        // Apply formatting if specified
        if (!empty($atts['format'])) {
            $value = $this->format_sitespren_value($value, $atts['format']);
        }
        
        // Return the formatted value
        $class = !empty($atts['class']) ? ' class="' . esc_attr($atts['class']) . '"' : '';
        return '<span' . $class . '>' . esc_html($value) . '</span>';
    }
    
    /**
     * Database helper methods - Direct SQL queries
     */
    private function get_services($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'zen_services';
        
        $defaults = array(
            'orderby' => 'position_in_custom_order',
            'order' => 'ASC',
            'pinned_first' => true
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $order_clause = '';
        if ($args['pinned_first']) {
            $order_clause = "ORDER BY is_pinned_service DESC, {$args['orderby']} {$args['order']}";
        } else {
            $order_clause = "ORDER BY {$args['orderby']} {$args['order']}";
        }
        
        return $wpdb->get_results("SELECT * FROM $table $order_clause");
    }
    
    private function get_service($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'zen_services';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE service_id = %d", $id));
    }
    
    private function get_locations($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'zen_locations';
        
        $defaults = array(
            'orderby' => 'position_in_custom_order',
            'order' => 'ASC',
            'pinned_first' => true
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $order_clause = '';
        if ($args['pinned_first']) {
            $order_clause = "ORDER BY is_pinned_location DESC, {$args['orderby']} {$args['order']}";
        } else {
            $order_clause = "ORDER BY {$args['orderby']} {$args['order']}";
        }
        
        return $wpdb->get_results("SELECT * FROM $table $order_clause");
    }
    
    private function get_location($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'zen_locations';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE location_id = %d", $id));
    }
    
    /**
     * Render individual service item based on template
     */
    private function render_service_item($service, $atts) {
        $template = $atts['template'];
        $show_images = $atts['show_images'] === 'true';
        $image_size = $atts['image_size'];
        
        $output = '<div class="zen-service-item">';
        
        // Add pinned indicator
        if ($service->is_pinned_service) {
            $output .= '<span class="zen-pinned-badge">📌 Pinned</span>';
        }
        
        switch ($template) {
            case 'grid':
            case 'cards':
                $output .= '<div class="zen-service-card">';
                if ($show_images && $service->rel_image1_id) {
                    $output .= '<div class="zen-service-image-wrapper">';
                    $output .= wp_get_attachment_image($service->rel_image1_id, $image_size, false, array('class' => 'zen-service-image'));
                    $output .= '</div>';
                }
                $output .= '<div class="zen-service-content">';
                $output .= '<h3 class="zen-service-name">' . esc_html($service->service_name) . '</h3>';
                if ($service->service_placard) {
                    $output .= '<div class="zen-service-placard">' . esc_html($service->service_placard) . '</div>';
                }
                if ($service->description1_short) {
                    $output .= '<div class="zen-service-description">' . esc_html($service->description1_short) . '</div>';
                }
                $output .= '</div></div>';
                break;
                
            case 'list':
            default:
                if ($show_images && $service->rel_image1_id) {
                    $output .= '<div class="zen-service-image-wrapper">';
                    $output .= wp_get_attachment_image($service->rel_image1_id, $image_size, false, array('class' => 'zen-service-image'));
                    $output .= '</div>';
                }
                $output .= '<div class="zen-service-content">';
                $output .= '<h3 class="zen-service-name">' . esc_html($service->service_name) . '</h3>';
                if ($service->service_placard) {
                    $output .= '<div class="zen-service-placard">' . esc_html($service->service_placard) . '</div>';
                }
                if ($service->service_moniker) {
                    $output .= '<div class="zen-service-moniker">' . esc_html($service->service_moniker) . '</div>';
                }
                if ($service->description1_short) {
                    $output .= '<div class="zen-service-description-short">' . esc_html($service->description1_short) . '</div>';
                }
                if ($service->description1_long && $template === 'full') {
                    $output .= '<div class="zen-service-description-long">' . esc_html($service->description1_long) . '</div>';
                }
                $output .= '</div>';
                break;
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render individual location item based on template
     */
    private function render_location_item($location, $atts) {
        $template = $atts['template'];
        $show_images = $atts['show_images'] === 'true';
        $image_size = $atts['image_size'];
        
        $output = '<div class="zen-location-item">';
        
        if ($location->is_pinned_location) {
            $output .= '<span class="zen-pinned-badge">📌 Pinned</span>';
        }
        
        switch ($template) {
            case 'grid':
            case 'cards':
                $output .= '<div class="zen-location-card">';
                if ($show_images && $location->rel_image1_id) {
                    $output .= '<div class="zen-location-image-wrapper">';
                    $output .= wp_get_attachment_image($location->rel_image1_id, $image_size, false, array('class' => 'zen-location-image'));
                    $output .= '</div>';
                }
                $output .= '<div class="zen-location-content">';
                $output .= '<h3 class="zen-location-name">' . esc_html($location->location_name) . '</h3>';
                if ($location->location_placard) {
                    $output .= '<div class="zen-location-placard">' . esc_html($location->location_placard) . '</div>';
                }
                $address = $this->format_address($location);
                if ($address) {
                    $output .= '<div class="zen-location-address">' . $address . '</div>';
                }
                $output .= '</div></div>';
                break;
                
            case 'list':
            default:
                if ($show_images && $location->rel_image1_id) {
                    $output .= '<div class="zen-location-image-wrapper">';
                    $output .= wp_get_attachment_image($location->rel_image1_id, $image_size, false, array('class' => 'zen-location-image'));
                    $output .= '</div>';
                }
                $output .= '<div class="zen-location-content">';
                $output .= '<h3 class="zen-location-name">' . esc_html($location->location_name) . '</h3>';
                if ($location->location_placard) {
                    $output .= '<div class="zen-location-placard">' . esc_html($location->location_placard) . '</div>';
                }
                if ($location->location_moniker) {
                    $output .= '<div class="zen-location-moniker">' . esc_html($location->location_moniker) . '</div>';
                }
                $address = $this->format_address($location);
                if ($address) {
                    $output .= '<div class="zen-location-address">' . $address . '</div>';
                }
                $output .= '</div>';
                break;
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Format location address
     */
    private function format_address($location) {
        $address_parts = array();
        
        if ($location->street) $address_parts[] = $location->street;
        if ($location->city) $address_parts[] = $location->city;
        if ($location->state_code) $address_parts[] = $location->state_code;
        if ($location->zip_code) $address_parts[] = $location->zip_code;
        if ($location->country && $location->country !== 'US') $address_parts[] = $location->country;
        
        return implode(', ', $address_parts);
    }
    
    /**
     * Format sitespren value based on type
     */
    private function format_sitespren_value($value, $format) {
        switch ($format) {
            case 'phone':
                $cleaned = preg_replace('/[^0-9]/', '', $value);
                if (strlen($cleaned) === 10) {
                    return sprintf('(%s) %s-%s', 
                        substr($cleaned, 0, 3),
                        substr($cleaned, 3, 3),
                        substr($cleaned, 6, 4)
                    );
                }
                break;
                
            case 'currency':
                if (is_numeric($value)) {
                    return '$' . number_format((float)$value, 2);
                }
                break;
                
            case 'date':
                $timestamp = strtotime($value);
                if ($timestamp !== false) {
                    return date('F j, Y', $timestamp);
                }
                break;
                
            case 'url':
                if (!preg_match('/^https?:\/\//', $value)) {
                    return 'https://' . $value;
                }
                break;
        }
        
        return $value;
    }
    
    /**
     * Render sitespren phone link
     * Usage: [sitespren_phone_link] or [sitespren_phone_link prefix="+1" text="Call now: "]
     */
    public function render_sitespren_phone_link($atts) {
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'prefix' => '+1',
            'text' => 'Call us: ',
            'class' => 'phone-link'
        ), $atts, 'sitespren_phone_link');
        
        // Get phone number from sitespren data
        $sitespren = Grove_Database::get_sitespren(intval($atts['wppma_id']));
        
        if (!$sitespren || empty($sitespren->driggs_phone_1)) {
            return '<!-- No phone number found -->';
        }
        
        $phone = $sitespren->driggs_phone_1;
        
        // Clean phone for tel: link (remove non-digits)
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
    
    /**
     * Render buffalo phone number
     * Usage: [buffalo_phone_number] or [buffalo_phone_number prefix="+1" text="Call us: "]
     */
    public function render_buffalo_phone_number($atts) {
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'prefix' => '', // Will be set dynamically from database
            'text' => 'Call us: '
        ), $atts, 'buffalo_phone_number');
        
        // Get phone number from sitespren data
        $sitespren = Grove_Database::get_sitespren(intval($atts['wppma_id']));
        
        if (!$sitespren || empty($sitespren->driggs_phone_1)) {
            return '<!-- No phone number found -->';
        }
        
        $phone = $sitespren->driggs_phone_1;
        
        // Use dynamic country code from database, fallback to 1
        $country_code = !empty($sitespren->driggs_phone_country_code) ? $sitespren->driggs_phone_country_code : '1';
        
        // Override prefix if not manually set
        if (empty($atts['prefix'])) {
            $atts['prefix'] = '+' . $country_code;
        }
        
        return '<div class="phone-number"><a href="tel:' . esc_attr($atts['prefix']) . esc_attr($phone) . '">' . esc_html($atts['text']) . esc_html($phone) . '</a></div>';
    }
    
    /**
     * Render special phone href value for Elementor Dynamic Tags
     * Returns a clean tel: URL for use in link fields
     * Usage: [special_phone_href_value_1]
     */
    public function render_special_phone_href_value_1($atts) {
        $atts = shortcode_atts(array(
            'wppma_id' => '1'
        ), $atts, 'special_phone_href_value_1');
        
        // Get sitespren data
        $sitespren = Grove_Database::get_sitespren(intval($atts['wppma_id']));
        
        if (!$sitespren) {
            return '';
        }
        
        // Get country code and phone number
        $country_code = isset($sitespren->driggs_phone_country_code) ? $sitespren->driggs_phone_country_code : '1';
        $phone_number = isset($sitespren->driggs_phone_1) ? $sitespren->driggs_phone_1 : '';
        
        if (empty($phone_number)) {
            return '';
        }
        
        // Normalize: keep only digits from both country code and phone number
        $cc_digits = preg_replace('/[^0-9]/', '', $country_code);
        $phone_digits = preg_replace('/[^0-9]/', '', $phone_number);
        
        // Combine and ensure we have digits
        $full_digits = $cc_digits . $phone_digits;
        
        if (empty($full_digits)) {
            return '';
        }
        
        // Return clean tel: URL with + prefix
        return 'tel:+' . $full_digits;
    }
    
    /**
     * Render special phone href for raw HTML (closing shortcode)
     * Creates a clickable phone link wrapping inner content
     * Usage: [special_phone_href_for_raw_html class="btn btn-primary"]Call us![/special_phone_href_for_raw_html]
     */
    public function render_special_phone_href_for_raw_html($atts, $content = null) {
        // Default attributes
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'class' => '',
            'style' => '',
            'rel' => '',
            'target' => '',
            'aria_label' => ''
        ), $atts, 'special_phone_href_for_raw_html');
        
        // Get sitespren data using existing shortcodes
        $country_code = do_shortcode('[sitespren dbcol="driggs_phone_country_code" wppma_id="' . $atts['wppma_id'] . '"]');
        $phone_number = do_shortcode('[sitespren dbcol="driggs_phone_1" wppma_id="' . $atts['wppma_id'] . '"]');
        
        // Normalize: keep only digits from both country code and phone number
        $cc_digits = preg_replace('/[^0-9]/', '', $country_code);
        $phone_digits = preg_replace('/[^0-9]/', '', $phone_number);
        
        // Combine and ensure we have digits
        $full_digits = $cc_digits . $phone_digits;
        
        if (empty($full_digits)) {
            // If no phone number found, just return the processed content without link
            return do_shortcode($content);
        }
        
        // Build tel: URL
        $href = 'tel:+' . $full_digits;
        
        // Process inner content (allows nested shortcodes like [phone_local])
        $label = do_shortcode($content);
        
        // Build link attributes
        $attrs = array();
        $attrs[] = 'href="' . esc_url($href) . '"';
        
        if (!empty($atts['class'])) {
            $attrs[] = 'class="' . esc_attr($atts['class']) . '"';
        }
        if (!empty($atts['style'])) {
            $attrs[] = 'style="' . esc_attr($atts['style']) . '"';
        }
        if (!empty($atts['rel'])) {
            $attrs[] = 'rel="' . esc_attr($atts['rel']) . '"';
        }
        if (!empty($atts['target'])) {
            $attrs[] = 'target="' . esc_attr($atts['target']) . '"';
        }
        if (!empty($atts['aria_label'])) {
            $attrs[] = 'aria-label="' . esc_attr($atts['aria_label']) . '"';
        }
        
        // Return complete anchor tag
        return '<a ' . implode(' ', $attrs) . '>' . wp_kses_post($label) . '</a>';
    }
    
    /**
     * Render raven contact link for Elementor Dynamic Tags
     * Returns a complete URL based on method selection
     * Usage: [raven_contact_link]
     */
    public function render_raven_contact_link($atts) {
        $atts = shortcode_atts(array(
            'wppma_id' => '1'
        ), $atts, 'raven_contact_link');
        
        // Get sitespren data
        $sitespren = Grove_Database::get_sitespren(intval($atts['wppma_id']));
        
        if (!$sitespren) {
            return '';
        }
        
        // Get method (return empty if not set - user must configure)
        $method = isset($sitespren->driggs_raven_method) ? $sitespren->driggs_raven_method : '';
        
        // If method not set, return empty (user must configure on misc options page)
        if (empty($method)) {
            return '';
        }
        
        if ($method === 'long_url') {
            // Method 2: Return full URL directly
            $long_url = isset($sitespren->driggs_raven_contact_long_url) ? $sitespren->driggs_raven_contact_long_url : '';
            return !empty($long_url) ? $long_url : '';
        } else {
            // Method 1: Build from short path using home_url()
            $short_path = isset($sitespren->driggs_raven_contact_short_path) ? $sitespren->driggs_raven_contact_short_path : '';
            
            if (empty($short_path)) {
                return '';
            }
            
            // Build complete URL: https://example.com/contact-us/
            return trailingslashit(home_url()) . trailingslashit($short_path);
        }
    }
    
    /**
     * Phone Local Shortcode: [phone_local]
     * Displays phone number in local format: (123) 456-7890
     */
    public function render_phone_local($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'format' => 'us'
        ), $atts, 'phone_local');
        
        // Get phone from database
        $table = $wpdb->prefix . 'zen_sitespren';
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_1 FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if (!$phone) return '';
        
        // Clean phone (remove all non-digits)
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Format for US: (123) 456-7890
        if (strlen($phone_clean) == 10) {
            return '(' . substr($phone_clean, 0, 3) . ') ' . 
                   substr($phone_clean, 3, 3) . '-' . 
                   substr($phone_clean, 6, 4);
        }
        
        return $phone; // fallback
    }
    
    /**
     * Phone International Shortcode: [phone_international]
     * Displays phone number in international format: +1 (123) 456-7890
     */
    public function render_phone_international($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'show_plus' => 'yes'
        ), $atts, 'phone_international');
        
        // Get phone from database first
        $table = $wpdb->prefix . 'zen_sitespren';
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_1 FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if (!$phone) return '';
        
        // Try to get country code, fallback to 1 if column doesn't exist or is null
        $country_code = '1'; // Default
        $country_code_result = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_country_code FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if ($country_code_result) {
            $country_code = $country_code_result;
        }
        
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        $plus = ($atts['show_plus'] === 'yes') ? '+' : '';
        
        // Format: +1 (123) 456-7890
        if (strlen($phone_clean) == 10) {
            return $plus . $country_code . ' (' . 
                   substr($phone_clean, 0, 3) . ') ' . 
                   substr($phone_clean, 3, 3) . '-' . 
                   substr($phone_clean, 6, 4);
        }
        
        return $plus . $country_code . ' ' . $phone_clean;
    }
    
    /**
     * Phone Link Shortcode: [phone_link]
     * Creates clickable phone link with customizable display
     */
    public function render_phone_link($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'text' => '',
            'format' => 'local',
            'class' => 'phone-link'
        ), $atts, 'phone_link');
        
        // Get phone from database first
        $table = $wpdb->prefix . 'zen_sitespren';
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_1 FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if (!$phone) return '';
        
        // Try to get country code, fallback to 1 if column doesn't exist or is null
        $country_code = '1'; // Default
        $country_code_result = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_country_code FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if ($country_code_result) {
            $country_code = $country_code_result;
        }
        
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        // tel: link (always international format)
        $tel_link = '+' . $country_code . $phone_clean;
        
        // Display text
        if ($atts['text']) {
            $display_text = $atts['text'];
        } else if ($atts['format'] === 'international') {
            $display_text = '+' . $country_code . ' (' . substr($phone_clean, 0, 3) . ') ' . 
                           substr($phone_clean, 3, 3) . '-' . substr($phone_clean, 6, 4);
        } else {
            $display_text = '(' . substr($phone_clean, 0, 3) . ') ' . 
                           substr($phone_clean, 3, 3) . '-' . substr($phone_clean, 6, 4);
        }
        
        return '<a href="tel:' . esc_attr($tel_link) . '" class="' . esc_attr($atts['class']) . '">' . 
               esc_html($display_text) . '</a>';
    }
    
    /**
     * Beginning A Code Moose Shortcode: [beginning_a_code_moose]
     * Outputs opening <a> tag with tel: link using dynamic phone data
     * User must manually close with </a> tag
     */
    public function render_beginning_a_code_moose($atts) {
        global $wpdb;
        
        $atts = shortcode_atts(array(
            'wppma_id' => '1',
            'class' => ''
        ), $atts, 'beginning_a_code_moose');
        
        // Get phone from database
        $table = $wpdb->prefix . 'zen_sitespren';
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_1 FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if (!$phone) return '<!-- No phone number found -->';
        
        // Try to get country code, fallback to 1 if column doesn't exist or is null
        $country_code = '1'; // Default
        $country_code_result = $wpdb->get_var($wpdb->prepare(
            "SELECT driggs_phone_country_code FROM {$table} WHERE wppma_id = %d",
            $atts['wppma_id']
        ));
        
        if ($country_code_result) {
            $country_code = $country_code_result;
        }
        
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Create tel: link (always international format)
        $tel_link = '+' . $country_code . $phone_clean;
        
        // Build opening <a> tag
        $class_attr = !empty($atts['class']) ? ' class="' . esc_attr($atts['class']) . '"' : '';
        
        return '<a href="tel:' . esc_attr($tel_link) . '"' . $class_attr . '>';
    }
    
    /**
     * Register dynamic hoof shortcodes from database
     */
    private function register_hoof_shortcodes() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            return;
        }
        
        // Get all active hoof codes
        $hoof_codes = $wpdb->get_results("SELECT hoof_slug FROM $table WHERE is_active = 1");
        
        if ($hoof_codes) {
            foreach ($hoof_codes as $code) {
                add_shortcode($code->hoof_slug, array($this, 'render_hoof_shortcode'));
            }
        }
    }
    
    /**
     * Render dynamic hoof shortcode
     */
    public function render_hoof_shortcode($atts, $content = null, $tag = '') {
        global $wpdb;
        
        // Get the hoof code content from database
        $table = $wpdb->prefix . 'zen_hoof_codes';
        $hoof_content = $wpdb->get_var($wpdb->prepare(
            "SELECT hoof_content FROM $table WHERE hoof_slug = %s AND is_active = 1",
            $tag
        ));
        
        if (!$hoof_content) {
            return '<!-- Hoof code not found: ' . esc_html($tag) . ' -->';
        }
        
        // Process any nested shortcodes in the content
        return do_shortcode($hoof_content);
    }
    
    /**
     * Get all hoof codes for admin display
     */
    public static function get_hoof_codes() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            return array();
        }
        
        return $wpdb->get_results("SELECT * FROM $table ORDER BY position_order ASC, hoof_id ASC");
    }
    
    /**
     * Update hoof code content
     */
    public static function update_hoof_code($hoof_id, $content) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'zen_hoof_codes';
        
        return $wpdb->update(
            $table,
            array('hoof_content' => $content),
            array('hoof_id' => $hoof_id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Register dynamic shortcodes from the zen_general_shortcodes table
     */
    public function register_dynamic_shortcodes() {
        // Register all shortcodes from the database
        $this->register_all_general_shortcodes();
    }
    
    /**
     * Register all shortcodes from the general shortcodes table
     * Similar to how driggs_mar shortcodes are registered
     */
    private function register_all_general_shortcodes() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return;
        }
        
        // Get all active shortcode slugs
        $shortcodes = $wpdb->get_col(
            "SELECT shortcode_slug FROM $table_name WHERE is_active = 1"
        );
        
        if (!$shortcodes) {
            return;
        }
        
        // Register each shortcode to use our universal handler
        foreach ($shortcodes as $slug) {
            add_shortcode($slug, array($this, 'render_general_shortcode'));
        }
    }
    
    /**
     * Universal handler for general shortcodes - works like render_sitespren
     * Looks up content from database and processes it
     */
    public function render_general_shortcode($atts, $content = '', $tag = '') {
        global $wpdb;
        
        // The $tag parameter contains the actual shortcode used
        $shortcode_slug = $tag;
        
        // Get the shortcode content from database
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return '<!-- General shortcodes table not found -->';
        }
        
        $shortcode_data = $wpdb->get_row($wpdb->prepare(
            "SELECT shortcode_content, shortcode_type FROM $table_name WHERE shortcode_slug = %s AND is_active = 1",
            $shortcode_slug
        ));
        
        if (!$shortcode_data || empty($shortcode_data->shortcode_content)) {
            return '<!-- Shortcode not found or inactive: ' . esc_html($shortcode_slug) . ' -->';
        }
        
        // Process the content with placeholders
        return $this->process_shortcode_template($shortcode_data->shortcode_content, $atts);
    }
    
    /**
     * Process shortcode template with dynamic placeholders
     */
    private function process_shortcode_template($template, $atts) {
        // Parse attributes with common defaults
        $atts = shortcode_atts(array(
            'id' => '1',
            'service_id' => '',
            'location_id' => '1',
            'wppma_id' => '1',
            'category' => '',
            'type' => 'default',
            'class' => '',
            'limit' => '5'
        ), $atts);
        
        // Replace all placeholders with their values
        $content = $template;
        foreach ($atts as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        
        // Fix escaped quotes in shortcodes - stripslashes to clean them
        $content = stripslashes($content);
        
        // Check for CSS at the end and wrap it in <style> tags
        $content = $this->wrap_css_in_style_tags($content);
        
        // Process any nested shortcodes (like [zen_service], [zen_location], etc.)
        $content = do_shortcode($content);
        
        return $content;
    }
    
    /**
     * Wrap any CSS found in content with proper <style> tags
     */
    private function wrap_css_in_style_tags($content) {
        // Look for CSS patterns at the end of content - multiple CSS rules
        if (preg_match('/\n\s*([.#][\w-]+[^{]*\{[^}]*\}[\s\S]*?)$/m', $content, $matches)) {
            $css_part = trim($matches[1]);
            
            // Remove the CSS from the main content
            $content = preg_replace('/\n\s*([.#][\w-]+[^{]*\{[^}]*\}[\s\S]*?)$/m', '', $content);
            $content = trim($content);
            
            // Wrap CSS in style tags and append
            $content .= "\n<style>\n" . $css_part . "\n</style>";
        }
        
        return $content;
    }

    /**
     * Register a single shortcode immediately (for real-time admin updates)
     * Now uses the same approach as driggs_mar shortcodes
     */
    public static function register_single_shortcode($shortcode_slug, $shortcode_content) {
        // Get instance to access non-static methods
        $instance = new self();
        
        // Register using the same handler as all general shortcodes
        add_shortcode($shortcode_slug, array($instance, 'render_general_shortcode'));
    }
    
    /**
     * Unregister a shortcode immediately (for real-time admin updates)
     */
    public static function unregister_single_shortcode($shortcode_slug) {
        global $shortcode_tags;
        if (isset($shortcode_tags[$shortcode_slug])) {
            unset($shortcode_tags[$shortcode_slug]);
        }
    }
    
    /**
     * Refresh all dynamic shortcode registrations (useful for debugging)
     */
    public function refresh_dynamic_shortcodes() {
        global $wpdb, $shortcode_tags;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // Get all existing dynamic shortcodes from the database
        $existing_shortcodes = $wpdb->get_results(
            "SELECT shortcode_slug FROM $table_name"
        );
        
        // Unregister all existing dynamic shortcodes
        if ($existing_shortcodes) {
            foreach ($existing_shortcodes as $shortcode) {
                if (isset($shortcode_tags[$shortcode->shortcode_slug])) {
                    unset($shortcode_tags[$shortcode->shortcode_slug]);
                }
            }
        }
        
        // Re-register all active dynamic shortcodes
        $this->register_dynamic_shortcodes();
    }
    
    /**
     * Render panama fragment shortcode
     */
    public function render_panama_fragment($atts) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_fragments';
        
        // Get panama fragment content from database
        $result = $wpdb->get_var("SELECT panama_fragment_datum FROM $table_name WHERE id = 1");
        
        return $result ? $result : '';
    }
    
    /**
     * Render senegal fragment shortcode  
     */
    public function render_senegal_fragment($atts) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_fragments';
        
        // Get senegal fragment content from database
        $result = $wpdb->get_var("SELECT senegal_fragment_datum FROM $table_name WHERE id = 1");
        
        return $result ? $result : '';
    }
    
    /**
     * Render sitemap method 1 shortcode
     * Displays all pages and posts organized by section
     */
    public function render_sitemap_method_1($atts) {
        global $wpdb;
        
        $output = '<div class="grove-sitemap">';
        
        // Section 1: Main Pages
        $output .= '<div class="sitemap-section">';
        $output .= '<h3>Main Pages:</h3>';
        $output .= '<ul>';
        
        // Home page
        $home_url = home_url('/');
        $home_title = get_bloginfo('name');
        $output .= '<li><a href="' . esc_url($home_url) . '">' . esc_html($home_title) . ' (Home)</a></li>';
        
        // Blog posts page
        $posts_page_id = get_option('page_for_posts');
        if ($posts_page_id) {
            $posts_page_url = get_permalink($posts_page_id);
            $posts_page_title = get_the_title($posts_page_id);
            $output .= '<li><a href="' . esc_url($posts_page_url) . '">' . esc_html($posts_page_title) . '</a></li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>';
        
        // Section 2: Service Pages
        $output .= '<div class="sitemap-section">';
        $output .= '<h3>Service Pages:</h3>';
        $output .= '<ul>';
        
        // Get service pages from _zen_services table
        $services_table = $wpdb->prefix . 'zen_services';
        $service_pages = $wpdb->get_results("
            SELECT DISTINCT asn_service_page_id 
            FROM $services_table 
            WHERE asn_service_page_id IS NOT NULL 
            AND asn_service_page_id != 0
        ");
        
        $service_page_ids = array();
        foreach ($service_pages as $service) {
            $page_id = $service->asn_service_page_id;
            if ($page_id && get_post_status($page_id) === 'publish') {
                $service_page_ids[] = $page_id;
                $page_url = get_permalink($page_id);
                $page_title = get_the_title($page_id);
                $output .= '<li><a href="' . esc_url($page_url) . '">' . esc_html($page_title) . '</a></li>';
            }
        }
        
        if (empty($service_page_ids)) {
            $output .= '<li>No service pages found</li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>';
        
        // Section 3: Other Pages
        $output .= '<div class="sitemap-section">';
        $output .= '<h3>Other Pages:</h3>';
        $output .= '<ul>';
        
        // Get all published pages except service pages and posts page
        $exclude_ids = array_merge($service_page_ids, array($posts_page_id));
        
        $args = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post__not_in' => $exclude_ids
        );
        
        $pages = get_posts($args);
        
        if ($pages) {
            foreach ($pages as $page) {
                $page_url = get_permalink($page->ID);
                $page_title = $page->post_title;
                $output .= '<li><a href="' . esc_url($page_url) . '">' . esc_html($page_title) . '</a></li>';
            }
        } else {
            $output .= '<li>No other pages found</li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>';
        
        // Section 4: Blog Posts
        $output .= '<div class="sitemap-section">';
        $output .= '<h3>Blog Posts:</h3>';
        $output .= '<ul>';
        
        // Get all published blog posts
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $posts = get_posts($args);
        
        if ($posts) {
            foreach ($posts as $post) {
                $post_url = get_permalink($post->ID);
                $post_title = $post->post_title;
                $output .= '<li><a href="' . esc_url($post_url) . '">' . esc_html($post_title) . '</a></li>';
            }
        } else {
            $output .= '<li>No blog posts found</li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>';
        
        $output .= '</div>'; // Close grove-sitemap
        
        // Add some basic CSS
        $output .= '<style>
            .grove-sitemap {
                padding: 20px 0;
            }
            .grove-sitemap .sitemap-section {
                margin-bottom: 30px;
            }
            .grove-sitemap h3 {
                font-size: 1.2em;
                margin-bottom: 15px;
                font-weight: bold;
            }
            .grove-sitemap ul {
                list-style-type: none;
                padding-left: 0;
                margin: 0;
            }
            .grove-sitemap li {
                margin-bottom: 8px;
            }
            .grove-sitemap a {
                text-decoration: none;
            }
            .grove-sitemap a:hover {
                text-decoration: underline;
            }
        </style>';
        
        return $output;
    }
    
}