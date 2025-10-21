<?php

/**
 * Grove Silk Dynamic Menu Class
 * 
 * Dynamically replaces WordPress menu item URLs based on page assignments 
 * stored in the zen_raven_page_spaces custom table.
 * 
 * Usage: Add CSS class "silk-menu-link-{space_name}" to Custom Link menu items
 * Example: "silk-menu-link-privacy_policy" will link to the assigned privacy policy page
 */
class Grove_Silk_Dynamic_Menu {
    
    /**
     * Constructor - Initialize the dynamic menu system
     */
    public function __construct() {
        // Hook into WordPress menu rendering to replace URLs dynamically
        add_filter('wp_nav_menu_objects', array($this, 'replace_silk_menu_urls'), 20, 2);
    }
    
    /**
     * Get the assigned page ID for a given space name from zen_raven_page_spaces table
     * Uses lightweight caching to avoid repeated database queries per page load
     * 
     * @param string $space_name The space name (e.g., 'privacy_policy', 'tos', etc.)
     * @return int The assigned page ID, or 0 if not found/assigned
     */
    private function get_silk_page_id_for_space($space_name) {
        if (empty($space_name)) {
            return 0;
        }
        
        // Cache lookup per space_name to avoid repeated queries per page load
        $cache_key = 'grove_silk_menu_pageid_' . $space_name;
        $cached = wp_cache_get($cache_key, 'grove_silk_menu');
        if (false !== $cached) {
            return (int) $cached;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_raven_page_spaces';
        
        $page_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT asn_page_id FROM $table_name WHERE space_name = %s LIMIT 1",
            $space_name
        ));
        
        // Cache for 30 seconds (adjust as needed)
        wp_cache_set($cache_key, $page_id, 'grove_silk_menu', 30);
        
        return $page_id;
    }
    
    /**
     * Extract space name from CSS classes
     * Looks for classes like "silk-menu-link-privacy_policy" and returns "privacy_policy"
     * 
     * @param array $classes Array of CSS classes from menu item
     * @return string The extracted space name, or empty string if not found
     */
    private function extract_silk_space_name_from_classes($classes) {
        if (empty($classes) || !is_array($classes)) {
            return '';
        }
        
        $silk_prefix = 'silk-menu-link-';
        
        foreach ($classes as $class) {
            if (strpos($class, $silk_prefix) === 0) {
                $space_name = sanitize_key(substr($class, strlen($silk_prefix)));
                return $space_name;
            }
        }
        
        return '';
    }
    
    /**
     * Replace menu URLs for items tagged with silk-menu-link-{space_name} classes
     * 
     * This function is called by WordPress when rendering navigation menus.
     * It checks each menu item for silk CSS classes and replaces the URL
     * with the permalink of the assigned page from zen_raven_page_spaces table.
     * 
     * @param array $items Array of menu item objects
     * @param object $args Menu arguments (not used, but required by filter)
     * @return array Modified array of menu item objects
     */
    public function replace_silk_menu_urls($items, $args) {
        if (empty($items)) {
            return $items;
        }
        
        foreach ($items as $item) {
            // Extract space name from CSS classes
            $space_name = $this->extract_silk_space_name_from_classes($item->classes);
            
            if (empty($space_name)) {
                continue; // No silk class found, skip this item
            }
            
            // Get the assigned page ID for this space
            $page_id = $this->get_silk_page_id_for_space($space_name);
            
            if ($page_id > 0) {
                // Verify the page exists and is published
                $post_status = get_post_status($page_id);
                
                if ($post_status === 'publish') {
                    // Get the permalink and replace the menu item URL
                    $permalink = get_permalink($page_id);
                    
                    if ($permalink) {
                        $item->url = $permalink;
                        
                        // Optional: Update current page highlighting
                        // Set current = true if we're viewing this page
                        $item->current = (get_queried_object_id() === $page_id);
                        
                        // Also check if this page is an ancestor of the current page
                        if (!$item->current) {
                            $current_post_id = get_queried_object_id();
                            if ($current_post_id && is_singular()) {
                                $ancestors = get_post_ancestors($current_post_id);
                                if (in_array($page_id, $ancestors)) {
                                    $item->current_item_ancestor = true;
                                    $item->current_item_parent = true;
                                }
                            }
                        }
                    }
                } else {
                    // Page exists but is not published
                    // Optional: You could set a fallback URL or leave as-is
                    // For now, we'll leave the original placeholder URL
                }
            } else {
                // No page assigned to this space
                // Optional: You could set a fallback URL or leave as-is
                // For now, we'll leave the original placeholder URL
            }
        }
        
        return $items;
    }
    
    /**
     * Clear the silk menu cache
     * Useful when page assignments are updated
     * 
     * @param string $space_name Optional specific space name to clear, or leave empty to clear all
     */
    public function clear_silk_cache($space_name = '') {
        if (!empty($space_name)) {
            // Clear specific space cache
            $cache_key = 'grove_silk_menu_pageid_' . $space_name;
            wp_cache_delete($cache_key, 'grove_silk_menu');
        } else {
            // Clear all silk menu cache
            // Note: WordPress doesn't have a built-in way to clear cache by group,
            // so we'll rely on the 30-second timeout for now
            // You could implement a more sophisticated cache clearing system if needed
        }
    }
    
    /**
     * Get all available space names from the zen_raven_page_spaces table
     * Useful for documentation or admin interfaces
     * 
     * @return array Array of space names
     */
    public function get_available_silk_space_names() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zen_raven_page_spaces';
        
        $space_names = $wpdb->get_col("SELECT DISTINCT space_name FROM $table_name ORDER BY space_name ASC");
        
        return $space_names ? $space_names : array();
    }
}

// Initialize the Grove Silk Dynamic Menu system
new Grove_Silk_Dynamic_Menu();