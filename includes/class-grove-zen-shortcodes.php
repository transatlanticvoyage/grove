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
        
        // Factory codes shortcodes
        add_shortcode('sitespren_phone_link', array($this, 'render_sitespren_phone_link'));
        
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
            return !is_plugin_active('snefuruplin/snefuruplin.php');
        }
        
        return false;
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
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_general_shortcodes';
        
        // Get all active shortcodes
        $shortcodes = $wpdb->get_results(
            "SELECT shortcode_slug, shortcode_content FROM $table_name WHERE is_active = 1"
        );
        
        if (!$shortcodes) {
            return;
        }
        
        foreach ($shortcodes as $shortcode) {
            // Register each shortcode dynamically
            add_shortcode($shortcode->shortcode_slug, function($atts) use ($shortcode) {
                return $this->render_dynamic_shortcode($shortcode->shortcode_content, $atts);
            });
        }
    }
    
    /**
     * Render dynamic shortcode content with attribute processing
     */
    public function render_dynamic_shortcode($content, $atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'id' => '1'
        ), $atts);
        
        // Replace {id} placeholder with actual ID
        $content = str_replace('{id}', $atts['id'], $content);
        
        // Process any nested shortcodes (like [zen_service])
        $content = do_shortcode($content);
        
        return $content;
    }
}