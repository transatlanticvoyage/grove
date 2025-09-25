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
}