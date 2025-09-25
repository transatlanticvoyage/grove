<?php
/**
 * Grove Quilter - Page Duplication System
 * 
 * Handles page duplication functionality throughout the Grove plugin
 * Includes Elementor compatibility for proper duplication of Elementor pages
 */

class Grove_Quilter {
    
    public function __construct() {
        // Add AJAX handlers
        add_action('wp_ajax_grove_quilter_duplicate_oshabi_for_services', array($this, 'ajax_duplicate_oshabi_for_services'));
    }
    
    /**
     * Main page duplication function - QuilterDuplicatePage
     * 
     * @param int $source_page_id The ID of the page to duplicate
     * @param array $args Optional arguments for customization
     * @return int|false The ID of the duplicated page or false on failure
     */
    public function QuilterDuplicatePage($source_page_id, $args = array()) {
        global $wpdb;
        
        $source_post = get_post($source_page_id);
        
        if (!$source_post) {
            return false;
        }
        
        // Default arguments
        $defaults = array(
            'post_title_suffix' => ' - Copy',
            'post_status' => 'draft',
            'meta_exclude' => array('_edit_lock', '_edit_last', '_wp_old_slug'), // Meta keys to exclude
            'update_title' => true
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Prepare new post data - using all source post fields
        $new_post_data = array(
            'post_title' => $args['update_title'] ? $source_post->post_title . $args['post_title_suffix'] : $source_post->post_title,
            'post_content' => $source_post->post_content,
            'post_excerpt' => $source_post->post_excerpt,
            'post_type' => $source_post->post_type,
            'post_status' => $args['post_status'],
            'post_author' => get_current_user_id(),
            'post_parent' => $source_post->post_parent,
            'menu_order' => $source_post->menu_order,
            'comment_status' => $source_post->comment_status,
            'ping_status' => $source_post->ping_status,
            'post_password' => $source_post->post_password,
            'to_ping' => $source_post->to_ping,
            'pinged' => $source_post->pinged
        );
        
        // Create the new post
        $new_post_id = wp_insert_post($new_post_data);
        
        if (is_wp_error($new_post_id) || !$new_post_id) {
            return false;
        }
        
        // Duplicate ALL post meta using wpdb to capture underscore fields
        $this->duplicate_all_post_meta($source_page_id, $new_post_id, $args['meta_exclude']);
        
        // Duplicate taxonomies
        $this->duplicate_post_taxonomies($source_page_id, $new_post_id);
        
        // Handle Elementor post-duplication tasks (CSS regeneration, cache clearing)
        $this->handle_elementor_post_duplication($new_post_id);
        
        // Clear any caches
        clean_post_cache($new_post_id);
        
        return $new_post_id;
    }
    
    /**
     * Duplicate ALL post meta data using wpdb to capture underscore fields
     * This method captures ALL meta fields including those starting with underscores
     * which are often used by page builders like Elementor
     * 
     * @param int $source_id Source post ID
     * @param int $target_id Target post ID
     * @param array $exclude Meta keys to exclude
     */
    private function duplicate_all_post_meta($source_id, $target_id, $exclude = array()) {
        global $wpdb;
        
        // Get ALL post meta using wpdb (this captures underscore-prefixed fields)
        $post_meta_infos = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d",
                $source_id
            )
        );
        
        if (empty($post_meta_infos)) {
            return;
        }
        
        foreach ($post_meta_infos as $meta_info) {
            $meta_key = $meta_info->meta_key;
            
            // Skip excluded keys
            if (in_array($meta_key, $exclude)) {
                continue;
            }
            
            // Sanitize and unserialize meta value
            $meta_value = maybe_unserialize($meta_info->meta_value);
            
            // Use add_post_meta to properly handle serialization and duplicates
            add_post_meta($target_id, $meta_key, $meta_value);
        }
    }
    
    /**
     * Duplicate post taxonomies (categories, tags, custom taxonomies)
     * 
     * @param int $source_id Source post ID
     * @param int $target_id Target post ID
     */
    private function duplicate_post_taxonomies($source_id, $target_id) {
        $taxonomies = get_object_taxonomies(get_post_type($source_id));
        
        foreach ($taxonomies as $taxonomy) {
            // Get term IDs instead of slugs for better accuracy
            $term_ids = wp_get_object_terms($source_id, $taxonomy, array('fields' => 'ids'));
            
            if (!is_wp_error($term_ids) && !empty($term_ids)) {
                wp_set_object_terms($target_id, $term_ids, $taxonomy);
            }
        }
    }
    
    /**
     * Handle Elementor-specific post-duplication tasks
     * Note: Elementor meta data is now automatically captured by duplicate_all_post_meta()
     * This method handles any post-duplication cleanup tasks
     * 
     * @param int $target_id Target post ID
     */
    private function handle_elementor_post_duplication($target_id) {
        // Check if Elementor is active
        if (!class_exists('\Elementor\Plugin')) {
            return;
        }
        
        // Regenerate Elementor CSS for the new page
        if (class_exists('\Elementor\Core\Files\CSS\Post')) {
            $css_file = new \Elementor\Core\Files\CSS\Post($target_id);
            $css_file->update();
        }
        
        // Clear Elementor cache for this post
        if (method_exists('\Elementor\Plugin', 'instance')) {
            $elementor = \Elementor\Plugin::instance();
            if (isset($elementor->files_manager)) {
                $elementor->files_manager->clear_cache();
            }
        }
    }
    
    /**
     * AJAX: Duplicate oshabi page and assign to selected services
     */
    public function ajax_duplicate_oshabi_for_services() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_services_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // Get selected service IDs
        $selected_services = isset($_POST['selected_services']) ? $_POST['selected_services'] : array();
        
        if (empty($selected_services)) {
            wp_send_json_error('No services selected. Please select one or more services from the table.');
            return;
        }
        
        // Get the current oshabi page assignment
        $oshabi_page_id = $this->get_current_oshabi_page();
        
        if (!$oshabi_page_id) {
            wp_send_json_error('No oshabi page assigned. Please assign an oshabi page first using Grove Page Bender.');
            return;
        }
        
        global $wpdb;
        $services_table = $wpdb->prefix . 'zen_services';
        $results = array();
        $successful_count = 0;
        $failed_count = 0;
        
        foreach ($selected_services as $service_id) {
            $service_id = intval($service_id);
            
            // Get service details for naming
            $service = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$services_table} WHERE service_id = %d",
                $service_id
            ));
            
            if (!$service) {
                $results[] = "Service ID {$service_id}: Service not found";
                $failed_count++;
                continue;
            }
            
            // Duplicate the oshabi page
            $service_name_clean = sanitize_title($service->service_name);
            $duplicate_args = array(
                'post_title_suffix' => " - {$service->service_name} Service",
                'post_status' => 'publish'
            );
            
            $new_page_id = $this->QuilterDuplicatePage($oshabi_page_id, $duplicate_args);
            
            if (!$new_page_id) {
                $results[] = "Service '{$service->service_name}': Failed to duplicate page";
                $failed_count++;
                continue;
            }
            
            // Update the service's asn_service_page_id
            $update_result = $wpdb->update(
                $services_table,
                array('asn_service_page_id' => $new_page_id),
                array('service_id' => $service_id),
                array('%d'),
                array('%d')
            );
            
            if ($update_result !== false) {
                $results[] = "Service '{$service->service_name}': Successfully created page (ID: {$new_page_id})";
                $successful_count++;
            } else {
                $results[] = "Service '{$service->service_name}': Page created but failed to update service assignment";
                $failed_count++;
            }
        }
        
        // Prepare response
        $summary = "Processed " . count($selected_services) . " services: {$successful_count} successful, {$failed_count} failed";
        
        if ($failed_count > 0) {
            wp_send_json_success(array(
                'message' => "⚠️ PARTIALLY COMPLETED - {$summary}",
                'results' => $results,
                'has_errors' => true
            ));
        } else {
            wp_send_json_success(array(
                'message' => "✅ ALL SUCCESSFUL - {$summary}",
                'results' => $results
            ));
        }
    }
    
    /**
     * Get the current oshabi page ID from the designations table
     * 
     * @return int|null The oshabi page ID or null if not set
     */
    private function get_current_oshabi_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_earth_page_designations';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            return null;
        }
        
        $oshabi_page_id = $wpdb->get_var("SELECT oshabi FROM {$table_name} LIMIT 1");
        
        return $oshabi_page_id ? intval($oshabi_page_id) : null;
    }
    
    /**
     * Get page title by ID (helper function)
     * 
     * @param int $page_id
     * @return string
     */
    private function get_page_title($page_id) {
        return get_the_title($page_id) ?: 'Unknown Page';
    }
}