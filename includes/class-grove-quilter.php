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
        add_action('wp_ajax_grove_quilter_get_duplication_history', array($this, 'ajax_get_duplication_history'));
        add_action('wp_ajax_grove_quilter_simple_duplicate', array($this, 'ajax_simple_duplicate'));
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
        
        // Log duplication history if context is provided
        if (isset($args['log_history']) && $args['log_history']) {
            $this->log_duplication_history($source_page_id, $new_post_id, $args);
        }
        
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
     * This method handles critical Elementor processing after page duplication
     * 
     * @param int $target_id Target post ID
     */
    private function handle_elementor_post_duplication($target_id) {
        // Check if Elementor is active
        if (!class_exists('\Elementor\Plugin')) {
            return;
        }
        
        // Verify that this post has Elementor data
        $elementor_data = get_post_meta($target_id, '_elementor_data', true);
        if (empty($elementor_data)) {
            return;
        }
        
        // Set the page to be edited with Elementor
        update_post_meta($target_id, '_elementor_edit_mode', 'builder');
        
        // Ensure Elementor version meta is set
        if (!get_post_meta($target_id, '_elementor_version', true)) {
            update_post_meta($target_id, '_elementor_version', ELEMENTOR_VERSION);
        }
        
        // Clear and regenerate Elementor CSS files for this post
        $this->regenerate_elementor_css($target_id);
        
        // Clear global Elementor cache
        $this->clear_elementor_global_cache();
        
        // Add hook to regenerate CSS after the page is fully processed
        add_action('wp_loaded', function() use ($target_id) {
            $this->delayed_elementor_css_regeneration($target_id);
        });
    }
    
    /**
     * Regenerate Elementor CSS for a specific post
     * 
     * @param int $post_id Post ID
     */
    private function regenerate_elementor_css($post_id) {
        if (!class_exists('\Elementor\Core\Files\CSS\Post')) {
            return;
        }
        
        try {
            // Delete existing CSS files
            $css_file = new \Elementor\Core\Files\CSS\Post($post_id);
            $css_file->delete();
            
            // Regenerate CSS files
            $css_file->update();
            
        } catch (Exception $e) {
            error_log('Grove Quilter: Elementor CSS regeneration failed for post ' . $post_id . ': ' . $e->getMessage());
        }
    }
    
    /**
     * Clear global Elementor cache
     */
    private function clear_elementor_global_cache() {
        try {
            if (method_exists('\Elementor\Plugin', 'instance')) {
                $elementor = \Elementor\Plugin::instance();
                
                // Clear files manager cache
                if (isset($elementor->files_manager)) {
                    $elementor->files_manager->clear_cache();
                }
                
                // Clear general cache
                if (method_exists($elementor, 'clear_cache')) {
                    $elementor->clear_cache();
                }
                
                // Clear CSS cache specifically
                if (isset($elementor->frontend)) {
                    $elementor->frontend->print_css = true;
                }
            }
            
        } catch (Exception $e) {
            error_log('Grove Quilter: Elementor global cache clearing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Delayed Elementor CSS regeneration (runs after wp_loaded)
     * This ensures all Elementor systems are fully initialized
     * 
     * @param int $post_id Post ID
     */
    private function delayed_elementor_css_regeneration($post_id) {
        if (!class_exists('\Elementor\Plugin')) {
            return;
        }
        
        try {
            // Force regenerate CSS and data
            if (class_exists('\Elementor\Core\Base\Document')) {
                $document = \Elementor\Plugin::$instance->documents->get($post_id);
                if ($document) {
                    $document->save_template_type();
                }
            }
            
            // Final CSS regeneration
            $this->regenerate_elementor_css($post_id);
            
        } catch (Exception $e) {
            error_log('Grove Quilter: Delayed Elementor CSS regeneration failed for post ' . $post_id . ': ' . $e->getMessage());
        }
    }
    
    /**
     * Log duplication history to database
     * 
     * @param int $source_page_id Source page ID
     * @param int $new_page_id New page ID  
     * @param array $args Duplication arguments
     */
    private function log_duplication_history($source_page_id, $new_page_id, $args) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_quilter_page_duplication_history';
        
        // Check if history table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            error_log('Grove Quilter: History table does not exist - ' . $table_name);
            return;
        }
        
        $source_post = get_post($source_page_id);
        $new_post = get_post($new_page_id);
        
        if (!$source_post || !$new_post) {
            return;
        }
        
        $is_elementor = get_post_meta($new_page_id, '_elementor_edit_mode', true) === 'builder';
        
        $history_data = array(
            'source_page_id' => $source_page_id,
            'source_page_title' => $source_post->post_title,
            'duplicated_page_id' => $new_page_id,
            'duplicated_page_title' => $new_post->post_title,
            'assigned_service_id' => isset($args['service_id']) ? $args['service_id'] : null,
            'assigned_service_name' => isset($args['service_name']) ? $args['service_name'] : null,
            'operation_type' => isset($args['operation_type']) ? $args['operation_type'] : 'oshabi_duplication',
            'method' => isset($args['method']) ? $args['method'] : 'Main Quilter Method 1',
            'is_elementor_page' => $is_elementor,
            'duplication_args' => wp_json_encode($args),
            'user_id' => get_current_user_id()
        );
        
        $wpdb->insert($table_name, $history_data);
    }
    
    /**
     * Get duplication history records
     * 
     * @param array $args Query arguments
     * @return array History records
     */
    public function get_duplication_history($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'order_by' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $table_name = $wpdb->prefix . 'zen_quilter_page_duplication_history';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            return array();
        }
        
        $posts_table = $wpdb->prefix . 'posts';
        
        $sql = "SELECT h.*, p.post_status 
                FROM {$table_name} h 
                LEFT JOIN {$posts_table} p ON h.duplicated_page_id = p.ID 
                ORDER BY h.{$args['order_by']} {$args['order']} 
                LIMIT {$args['limit']} OFFSET {$args['offset']}";
        
        return $wpdb->get_results($sql);
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
            
            // Duplicate the oshabi page using Panzer Method
            $service_name_clean = sanitize_title($service->service_name);
            $duplicate_args = array(
                'post_title_suffix' => " - {$service->service_name} Service",
                'post_status' => 'publish',
                'log_history' => true,
                'service_id' => $service_id,
                'service_name' => $service->service_name,
                'operation_type' => 'oshabi_duplication',
                'method' => 'Panzer Method'
            );
            
            // Use Panzer method for duplication
            $panzer = new Grove_Panzer();
            $new_page_id = $panzer->PanzerDuplicatePage($oshabi_page_id, $duplicate_args);
            
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
                $page_title = get_the_title($new_page_id);
                $is_elementor = get_post_meta($new_page_id, '_elementor_edit_mode', true) === 'builder';
                $elementor_note = $is_elementor ? ' (Elementor page - CSS regenerated)' : '';
                $results[] = "Service '{$service->service_name}': Successfully created page '{$page_title}' (ID: {$new_page_id}){$elementor_note}";
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
     * AJAX: Get duplication history data
     */
    public function ajax_get_duplication_history() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $history_records = $this->get_duplication_history();
        
        // Format the data for display
        $formatted_records = array();
        foreach ($history_records as $record) {
            $formatted_records[] = array(
                'history_id' => $record->history_id,
                'source_page_id' => $record->source_page_id,
                'source_page_title' => $record->source_page_title,
                'duplicated_page_id' => $record->duplicated_page_id,
                'duplicated_page_title' => $record->duplicated_page_title,
                'assigned_service_id' => $record->assigned_service_id,
                'assigned_service_name' => $record->assigned_service_name,
                'operation_type' => $record->operation_type,
                'method' => isset($record->method) ? $record->method : 'Main Quilter Method 1',
                'is_elementor_page' => $record->is_elementor_page,
                'post_status' => $record->post_status,
                'user_id' => $record->user_id,
                'created_at' => $record->created_at
            );
        }
        
        wp_send_json_success($formatted_records);
    }
    
    /**
     * AJAX: Simple page duplication (Main Quilter Method 1)
     */
    public function ajax_simple_duplicate() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // Get the current oshabi page assignment
        $oshabi_page_id = $this->get_current_oshabi_page();
        
        if (!$oshabi_page_id) {
            wp_send_json_error('No oshabi page assigned. Please assign an oshabi page first using the Select button above.');
            return;
        }
        
        // Get the source page title for naming
        $source_page = get_post($oshabi_page_id);
        if (!$source_page) {
            wp_send_json_error('Source oshabi page not found.');
            return;
        }
        
        // Check if user wants to publish the page
        $publish_page = isset($_POST['publish_page']) && $_POST['publish_page'] === 'true';
        $post_status = $publish_page ? 'publish' : 'draft';
        
        // Duplicate the oshabi page with simple naming
        $duplicate_args = array(
            'post_title_suffix' => ' - Copy',
            'post_status' => $post_status,
            'log_history' => true,
            'operation_type' => 'main_quilter_method_1',
            'method' => 'Main Quilter Method 1'
        );
        
        $new_page_id = $this->QuilterDuplicatePage($oshabi_page_id, $duplicate_args);
        
        if (!$new_page_id) {
            wp_send_json_error('Failed to duplicate the oshabi page. Please try again.');
            return;
        }
        
        // Get details about the new page
        $new_page_title = get_the_title($new_page_id);
        $is_elementor = get_post_meta($new_page_id, '_elementor_edit_mode', true) === 'builder';
        $elementor_note = $is_elementor ? ' (Elementor page - CSS regenerated)' : '';
        
        // Prepare success response
        $message = "✅ Page duplicated successfully!";
        $details = array(
            "Source: {$source_page->post_title}",
            "New Page: {$new_page_title} (ID: {$new_page_id})",
            "Status: Draft{$elementor_note}",
            "Method: Main Quilter Method 1"
        );
        
        wp_send_json_success(array(
            'message' => $message,
            'new_page_id' => $new_page_id,
            'new_page_title' => $new_page_title,
            'details' => $details
        ));
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