<?php
/**
 * Grove Chimp Function - Post Title Update System
 * 
 * Updates WordPress post titles based on service data
 * Format: (service_name) in (driggs_city), (driggs_state_code)
 */

if (!defined('ABSPATH')) {
    exit;
}

class Grove_Chimp {
    
    public function __construct() {
        // Add AJAX handler for chimp function
        add_action('wp_ajax_grove_run_chimp_function', array($this, 'ajax_run_chimp_function'));
    }
    
    /**
     * AJAX handler for running the chimp function
     */
    public function ajax_run_chimp_function() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_services_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $service_ids = isset($_POST['service_ids']) ? array_map('intval', $_POST['service_ids']) : array();
        
        if (empty($service_ids)) {
            wp_send_json_error('No services selected');
            return;
        }
        
        $results = array();
        $success_count = 0;
        $error_count = 0;
        $skipped_count = 0;
        
        foreach ($service_ids as $service_id) {
            $result = $this->process_service_chimp($service_id);
            $results[] = $result;
            
            if ($result['status'] === 'success') {
                $success_count++;
            } elseif ($result['status'] === 'error') {
                $error_count++;
            } else {
                $skipped_count++;
            }
        }
        
        $total_count = count($service_ids);
        $message = "Chimp function completed: {$success_count} updated, {$error_count} errors, {$skipped_count} skipped out of {$total_count} total.";
        
        wp_send_json_success(array(
            'message' => $message,
            'results' => $results,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'skipped_count' => $skipped_count,
            'total_count' => $total_count
        ));
    }
    
    /**
     * Process a single service with the chimp function
     * 
     * @param int $service_id The service ID
     * @return array Result array with status and message
     */
    private function process_service_chimp($service_id) {
        global $wpdb;
        
        // Get service data from zen_services table
        $service_table = $wpdb->prefix . 'zen_services';
        $service = $wpdb->get_row($wpdb->prepare(
            "SELECT service_id, service_name, asn_service_page_id 
             FROM {$service_table} 
             WHERE service_id = %d",
            $service_id
        ));
        
        if (!$service) {
            return array(
                'status' => 'error',
                'service_id' => $service_id,
                'message' => 'Service not found'
            );
        }
        
        // Check if asn_service_page_id is set
        if (empty($service->asn_service_page_id)) {
            return array(
                'status' => 'skipped',
                'service_id' => $service_id,
                'service_name' => $service->service_name,
                'message' => 'No assigned page (asn_service_page_id is null)'
            );
        }
        
        // Check if the post exists
        $post = get_post($service->asn_service_page_id);
        if (!$post) {
            return array(
                'status' => 'skipped',
                'service_id' => $service_id,
                'service_name' => $service->service_name,
                'message' => 'Assigned post ID ' . $service->asn_service_page_id . ' does not exist'
            );
        }
        
        // Get driggs data from zen_sitespren table
        $sitespren_table = $wpdb->prefix . 'zen_sitespren';
        $sitespren = $wpdb->get_row("SELECT driggs_city, driggs_state_code FROM {$sitespren_table} WHERE wppma_id = 1");
        
        $driggs_city = '';
        $driggs_state_code = '';
        
        if ($sitespren) {
            $driggs_city = $sitespren->driggs_city ?: '';
            $driggs_state_code = $sitespren->driggs_state_code ?: '';
        }
        
        // Fallback values if still empty
        if (empty($driggs_city)) {
            $driggs_city = 'City';
        }
        if (empty($driggs_state_code)) {
            $driggs_state_code = 'ST';
        }
        
        // Build the new post title
        $new_title = sprintf(
            '%s in %s, %s',
            $service->service_name,
            $driggs_city,
            $driggs_state_code
        );
        
        // Build the new post name (URL slug) from service name
        $new_name = sanitize_title($service->service_name);
        
        // Store old values for logging
        $old_title = $post->post_title;
        $old_name = $post->post_name;
        
        // Update the post title and post name
        $update_result = wp_update_post(array(
            'ID' => $service->asn_service_page_id,
            'post_title' => $new_title,
            'post_name' => $new_name
        ), true);
        
        if (is_wp_error($update_result)) {
            return array(
                'status' => 'error',
                'service_id' => $service_id,
                'service_name' => $service->service_name,
                'message' => 'Failed to update post: ' . $update_result->get_error_message()
            );
        }
        
        return array(
            'status' => 'success',
            'service_id' => $service_id,
            'service_name' => $service->service_name,
            'post_id' => $service->asn_service_page_id,
            'old_title' => $old_title,
            'new_title' => $new_title,
            'old_name' => $old_name,
            'new_name' => $new_name,
            'message' => "Updated: Title \"{$old_title}\" → \"{$new_title}\" | Slug \"{$old_name}\" → \"{$new_name}\""
        );
    }
    
    /**
     * Get driggs data with fallback
     * Helper method to retrieve city and state code
     * 
     * @return array City and state code
     */
    private function get_driggs_data() {
        global $wpdb;
        
        $driggs_table = $wpdb->prefix . 'zen_driggs';
        $driggs = $wpdb->get_row("SELECT city, state_code FROM {$driggs_table} WHERE id = 1");
        
        if ($driggs) {
            return array(
                'city' => $driggs->city ?: 'City',
                'state_code' => $driggs->state_code ?: 'ST'
            );
        }
        
        return array(
            'city' => 'City',
            'state_code' => 'ST'
        );
    }
}