<?php
/**
 * Grove Page Bender - Page Template Management
 * 
 * Manages page template assignments for Grove plugin
 */

class Grove_PageBender {
    
    public function __construct() {
        // Add AJAX handlers
        add_action('wp_ajax_grove_pagebender_get_all_pages', array($this, 'grove_pagebender_get_all_pages'));
        add_action('wp_ajax_grove_pagebender_save_oshabi', array($this, 'grove_pagebender_save_oshabi'));
        add_action('wp_ajax_grove_pagebender_get_current_assignments', array($this, 'grove_pagebender_get_current_assignments'));
    }
    
    /**
     * AJAX: Get all pages for the page selector modal
     */
    public function grove_pagebender_get_all_pages() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        // Get all pages and posts
        $pages = get_posts(array(
            'post_type' => array('page', 'post'),
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        $pages_data = array();
        foreach ($pages as $page) {
            $pages_data[] = array(
                'ID' => $page->ID,
                'post_title' => $page->post_title,
                'post_type' => $page->post_type,
                'post_status' => $page->post_status,
                'post_date' => $page->post_date
            );
        }
        
        wp_send_json_success($pages_data);
    }
    
    /**
     * AJAX: Save oshabi page assignment
     */
    public function grove_pagebender_save_oshabi() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $page_id = intval($_POST['page_id']);
        
        if (!$page_id) {
            wp_send_json_error('Invalid page ID');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_earth_page_designations';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            wp_send_json_error('Earth page designations table does not exist. Please create it first.');
            return;
        }
        
        // Check if a row exists, if not create one
        $existing_row = $wpdb->get_row("SELECT * FROM {$table_name} LIMIT 1");
        
        if (!$existing_row) {
            // Insert new row with oshabi assignment
            $result = $wpdb->insert(
                $table_name,
                array('oshabi' => $page_id),
                array('%d')
            );
        } else {
            // Update existing row
            $result = $wpdb->update(
                $table_name,
                array('oshabi' => $page_id),
                array('id' => $existing_row->id),
                array('%d'),
                array('%d')
            );
        }
        
        if ($result !== false) {
            $post_title = get_the_title($page_id);
            wp_send_json_success(array(
                'message' => 'Oshabi page assignment saved successfully',
                'page_id' => $page_id,
                'page_title' => $post_title
            ));
        } else {
            wp_send_json_error('Failed to save oshabi assignment: ' . $wpdb->last_error);
        }
    }
    
    /**
     * AJAX: Get current page assignments
     */
    public function grove_pagebender_get_current_assignments() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'grove_pagebender_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_earth_page_designations';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            wp_send_json_success(array(
                'oshabi' => null,
                'venshabi' => null
            ));
            return;
        }
        
        $row = $wpdb->get_row("SELECT * FROM {$table_name} LIMIT 1");
        
        $assignments = array(
            'oshabi' => null,
            'oshabi_title' => null,
            'venshabi' => null,
            'venshabi_title' => null
        );
        
        if ($row) {
            if ($row->oshabi) {
                $assignments['oshabi'] = $row->oshabi;
                $assignments['oshabi_title'] = get_the_title($row->oshabi);
            }
            if ($row->venshabi) {
                $assignments['venshabi'] = $row->venshabi;
                $assignments['venshabi_title'] = get_the_title($row->venshabi);
            }
        }
        
        wp_send_json_success($assignments);
    }
}