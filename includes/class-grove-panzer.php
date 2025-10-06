<?php
/**
 * Grove Panzer - Advanced Page Duplication System
 * 
 * Specialized duplication method for Elementor pages using proven techniques
 * This class will implement a different approach to page duplication
 * based on working plugin methodologies
 */

class Grove_Panzer {
    
    public function __construct() {
        // Add AJAX handlers
        add_action('wp_ajax_grove_panzer_duplicate_page', array($this, 'ajax_panzer_duplicate_page'));
    }
    
    /**
     * Main Panzer duplication function - PanzerDuplicatePage
     * Implements the proven methodology from the "Duplicate Page" WordPress plugin
     * 
     * @param int $source_page_id The ID of the page to duplicate
     * @param array $args Optional arguments for customization
     * @return int|false The ID of the duplicated page or false on failure
     */
    public function PanzerDuplicatePage($source_page_id, $args = array()) {
        global $wpdb;
        
        // Default arguments
        $defaults = array(
            'post_title_suffix' => ' - Copy',
            'post_status' => 'draft',
            'post_author' => get_current_user_id(),
            'log_history' => false
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Get the original post
        $post = get_post($source_page_id);
        
        if (!$post || is_wp_error($post)) {
            return false;
        }
        
        // Prepare new post data using the proven method
        $new_post_args = array(
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => $args['post_author'],
            'post_content' => wp_slash($post->post_content), // Use wp_slash for Gutenberg/Elementor compatibility
            'post_excerpt' => $post->post_excerpt,
            'post_parent' => $post->post_parent,
            'post_password' => $post->post_password,
            'post_status' => $args['post_status'],
            'post_title' => $post->post_title . $args['post_title_suffix'],
            'post_type' => $post->post_type,
            'to_ping' => $post->to_ping,
            'menu_order' => $post->menu_order,
        );
        
        // Insert the new post
        $new_post_id = wp_insert_post($new_post_args);
        
        if (is_wp_error($new_post_id) || !$new_post_id) {
            return false;
        }
        
        // Duplicate taxonomies (categories, tags, custom taxonomies)
        $this->duplicate_panzer_taxonomies($source_page_id, $new_post_id);
        
        // Duplicate post meta using the proven method
        $this->duplicate_panzer_post_meta($source_page_id, $new_post_id);
        
        // Handle Elementor-specific processing
        $this->handle_panzer_elementor_processing($source_page_id, $new_post_id);
        
        // Log to history if requested
        if ($args['log_history']) {
            $this->log_panzer_history($source_page_id, $new_post_id, $args);
        }
        
        return $new_post_id;
    }
    
    /**
     * Duplicate taxonomies using the proven method from Duplicate Page plugin
     * 
     * @param int $source_id Source post ID
     * @param int $target_id Target post ID
     */
    private function duplicate_panzer_taxonomies($source_id, $target_id) {
        $post = get_post($source_id);
        $taxonomies = array_map('sanitize_text_field', get_object_taxonomies($post->post_type));
        
        if (!empty($taxonomies) && is_array($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($source_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($target_id, $post_terms, $taxonomy, false);
            }
        }
    }
    
    /**
     * Duplicate post meta using the proven method from Duplicate Page plugin
     * This method captures ALL meta fields including Elementor data
     * 
     * @param int $source_id Source post ID
     * @param int $target_id Target post ID
     */
    private function duplicate_panzer_post_meta($source_id, $target_id) {
        // Use get_post_custom_keys to get ALL meta keys (including underscore fields)
        $post_meta_keys = get_post_custom_keys($source_id);
        
        if (!empty($post_meta_keys)) {
            foreach ($post_meta_keys as $meta_key) {
                $meta_values = get_post_custom_values($meta_key, $source_id);
                foreach ($meta_values as $meta_value) {
                    $meta_value = maybe_unserialize($meta_value);
                    update_post_meta($target_id, $meta_key, wp_slash($meta_value));
                }
            }
        }
    }
    
    /**
     * AJAX: Panzer page duplication using proven methodology
     */
    public function ajax_panzer_duplicate_page() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        // Get current oshabi page from Grove system
        $oshabi_page_id = $this->get_current_oshabi_page();
        
        if (!$oshabi_page_id) {
            wp_send_json_error('No oshabi page assigned. Please assign an oshabi page first using the Select button above.');
            return;
        }
        
        // Get the source page details
        $source_page = get_post($oshabi_page_id);
        if (!$source_page) {
            wp_send_json_error('Source oshabi page not found.');
            return;
        }
        
        // Check if user wants to publish the page
        $publish_page = isset($_POST['publish_page']) && $_POST['publish_page'] === 'true';
        $post_status = $publish_page ? 'publish' : 'draft';
        
        // Prepare Panzer duplication arguments
        $duplicate_args = array(
            'post_title_suffix' => ' - Panzer Copy',
            'post_status' => $post_status,
            'log_history' => true,
            'operation_type' => 'panzer_method',
            'method' => 'Panzer Method'
        );
        
        // Execute Panzer duplication
        $new_page_id = $this->PanzerDuplicatePage($oshabi_page_id, $duplicate_args);
        
        if (!$new_page_id) {
            wp_send_json_error('Failed to duplicate the oshabi page using Panzer method. Please try again.');
            return;
        }
        
        // Get details about the new page
        $new_page_title = get_the_title($new_page_id);
        $is_elementor = get_post_meta($new_page_id, '_elementor_edit_mode', true) === 'builder';
        $elementor_note = $is_elementor ? ' (Elementor page - CSS regenerated)' : '';
        
        // Prepare success response
        $message = "✅ Page duplicated successfully using Panzer Method!";
        $details = array(
            "Source: {$source_page->post_title}",
            "New Page: {$new_page_title} (ID: {$new_page_id})",
            "Status: Draft{$elementor_note}",
            "Method: Panzer Method (Proven WordPress.org Plugin Logic)"
        );
        
        wp_send_json_success(array(
            'message' => $message,
            'new_page_id' => $new_page_id,
            'new_page_title' => $new_page_title,
            'details' => $details
        ));
    }
    
    /**
     * Handle Elementor-specific processing using the proven method from Duplicate Page plugin
     * 
     * @param int $source_id Source post ID
     * @param int $target_id Target post ID
     */
    private function handle_panzer_elementor_processing($source_id, $target_id) {
        // Check if Elementor is active (same check as the working plugin)
        if (is_plugin_active('elementor/elementor.php')) {
            // Use the exact same method as the working plugin
            $css = \Elementor\Core\Files\CSS\Post::create($target_id);
            $css->update();
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
     * Log Panzer duplication to history using Grove_Quilter logging system
     * 
     * @param int $source_id Source page ID
     * @param int $target_id Target page ID
     * @param array $args Duplication arguments
     */
    private function log_panzer_history($source_id, $target_id, $args) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zen_quilter_page_duplication_history';
        
        // Check if history table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            error_log('Grove Panzer: History table does not exist - ' . $table_name);
            return;
        }
        
        $source_post = get_post($source_id);
        $new_post = get_post($target_id);
        
        if (!$source_post || !$new_post) {
            return;
        }
        
        $is_elementor = get_post_meta($target_id, '_elementor_edit_mode', true) === 'builder';
        
        $history_data = array(
            'source_page_id' => $source_id,
            'source_page_title' => $source_post->post_title,
            'duplicated_page_id' => $target_id,
            'duplicated_page_title' => $new_post->post_title,
            'assigned_service_id' => isset($args['service_id']) ? $args['service_id'] : null,
            'assigned_service_name' => isset($args['service_name']) ? $args['service_name'] : null,
            'operation_type' => isset($args['operation_type']) ? $args['operation_type'] : 'panzer_method',
            'method' => isset($args['method']) ? $args['method'] : 'Panzer Method',
            'is_elementor_page' => $is_elementor,
            'duplication_args' => wp_json_encode($args),
            'user_id' => get_current_user_id()
        );
        
        $wpdb->insert($table_name, $history_data);
    }
}