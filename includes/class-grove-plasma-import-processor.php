<?php

/**
 * Grove Plasma Import Processor Class
 * Handles the actual import functionality for plasma pages
 */
class Grove_Plasma_Import_Processor {
    
    /**
     * WordPress database object
     */
    private $wpdb;
    
    /**
     * Mapping of plasma fields to wp_posts fields
     */
    private $wp_posts_mapping = [
        'page_status' => 'post_status',
        'page_type' => 'post_type',
        'page_title' => 'post_title',
        'page_content' => 'post_content',
        'page_date' => 'post_date',
        'page_name' => 'post_name'
    ];
    
    /**
     * Mapping of plasma fields to wp_pylons fields (for different names only)
     */
    private $wp_pylons_mapping = [
        'page_archetype' => 'pylon_archetype'
    ];
    
    /**
     * Explicit same-name mappings for wp_pylons (ensures these fields are always mapped)
     */
    private $wp_pylons_explicit_same_name = [
        'staircase_page_template_desired' => 'staircase_page_template_desired'
    ];
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Check if empty fields should be updated based on user setting
     * 
     * @return bool True if empty fields should be set to empty in database
     */
    private function should_update_empty_fields() {
        // Check if the setting was passed in the request
        return isset($_POST['update_empty_fields']) && $_POST['update_empty_fields'] === 'true';
    }
    
    /**
     * Main import method - processes array of pages from JSON
     * 
     * @param array $pages_data Array of page objects from plasma export
     * @return array Results with success/error information
     */
    public function import_pages($pages_data) {
        $results = [
            'success' => [],
            'errors' => [],
            'total' => count($pages_data),
            'homepage_id' => null
        ];
        
        // Check if we should set homepage
        $should_set_homepage = isset($_POST['set_homepage_option']) && $_POST['set_homepage_option'] === 'true';
        $homepage_post_id = null;
        
        foreach ($pages_data as $index => $page) {
            try {
                // Create WordPress post/page
                $post_id = $this->create_wordpress_post($page);
                
                if ($post_id && !is_wp_error($post_id)) {
                    // Create pylon record
                    $pylon_result = $this->create_pylon_record($post_id, $page);
                    
                    if ($pylon_result) {
                        $results['success'][] = [
                            'index' => $index,
                            'post_id' => $post_id,
                            'title' => $page['page_title'] ?? 'Untitled',
                            'page_id' => $page['page_id'] ?? null
                        ];
                        
                        // Check if this is the homepage
                        if ($should_set_homepage && isset($page['page_archetype']) && $page['page_archetype'] === 'homepage') {
                            $homepage_post_id = $post_id;
                            $results['homepage_id'] = $post_id;
                        }
                        
                        // TODO: Future - assign page template based on staircase_page_template_desired
                        // $this->assign_page_template($post_id, $page);
                    } else {
                        // Post created but pylon record failed
                        $results['errors'][] = [
                            'index' => $index,
                            'message' => 'Post created but pylon record failed',
                            'post_id' => $post_id
                        ];
                    }
                } else {
                    // Post creation failed
                    $error_message = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown error creating post';
                    $results['errors'][] = [
                        'index' => $index,
                        'message' => $error_message,
                        'title' => $page['page_title'] ?? 'Untitled'
                    ];
                }
            } catch (Exception $e) {
                $results['errors'][] = [
                    'index' => $index,
                    'message' => $e->getMessage(),
                    'title' => $page['page_title'] ?? 'Untitled'
                ];
            }
        }
        
        // Set the homepage if we found one
        if ($should_set_homepage && $homepage_post_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $homepage_post_id);
            $results['homepage_set'] = true;
            $results['homepage_message'] = 'Homepage successfully set to page ID: ' . $homepage_post_id;
        }
        
        return $results;
    }
    
    /**
     * Create a WordPress post/page from plasma data
     * 
     * @param array $page_data Single page data from plasma export
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private function create_wordpress_post($page_data) {
        // Map plasma fields to WordPress post fields
        $post_data = [];
        
        // Apply explicit field mappings
        foreach ($this->wp_posts_mapping as $plasma_field => $wp_field) {
            if (isset($page_data[$plasma_field]) && !empty($page_data[$plasma_field])) {
                $post_data[$wp_field] = $page_data[$plasma_field];
            }
        }
        
        // Set defaults if not provided
        if (!isset($post_data['post_status']) || empty($post_data['post_status'])) {
            $post_data['post_status'] = 'publish';
        }
        
        if (!isset($post_data['post_type']) || empty($post_data['post_type'])) {
            // Default to 'page', but check for special conditions
            $post_data['post_type'] = 'page';
            
            // If page_type is blank/null and page_archetype has value of "blogpost", create as post
            if ((!isset($page_data['page_type']) || empty($page_data['page_type'])) && 
                (isset($page_data['page_archetype']) && $page_data['page_archetype'] === 'blogpost')) {
                $post_data['post_type'] = 'post';
            }
        }
        
        if (!isset($post_data['post_title'])) {
            $post_data['post_title'] = 'Imported Page - ' . current_time('mysql');
        }
        
        // Set post author to current user
        $post_data['post_author'] = get_current_user_id();
        
        // Handle post_date - convert from ISO format if needed
        if (isset($post_data['post_date'])) {
            $post_data['post_date'] = date('Y-m-d H:i:s', strtotime($post_data['post_date']));
            $post_data['post_date_gmt'] = get_gmt_from_date($post_data['post_date']);
        }
        
        // Insert the post
        $post_id = wp_insert_post($post_data);
        
        return $post_id;
    }
    
    /**
     * Create a wp_pylons record for the imported page
     * 
     * @param int $post_id WordPress post ID
     * @param array $page_data Original plasma page data
     * @return bool|int Insert ID on success, false on failure
     */
    private function create_pylon_record($post_id, $page_data) {
        $pylons_table = $this->wpdb->prefix . 'pylons';
        
        // Get the schema of wp_pylons table to enable dynamic mapping
        $pylons_columns = $this->get_table_columns($pylons_table);
        
        // Start with required relational fields
        $pylon_data = [
            'rel_wp_post_id' => $post_id,
            'rel_plasma_page_id' => isset($page_data['page_id']) ? intval($page_data['page_id']) : null
        ];
        
        // Apply explicit field mappings (for fields with different names)
        foreach ($this->wp_pylons_mapping as $plasma_field => $pylon_field) {
            if (isset($page_data[$plasma_field])) {
                $value = $page_data[$plasma_field];
                // Map non-empty values, or empty values if explicitly allowed
                if (!empty($value) || $value === '0' || (empty($value) && $this->should_update_empty_fields())) {
                    $pylon_data[$pylon_field] = $value;
                }
            }
        }
        
        // Apply explicit same-name mappings (ensures these critical fields are always mapped)
        foreach ($this->wp_pylons_explicit_same_name as $plasma_field => $pylon_field) {
            if (isset($page_data[$plasma_field])) {
                $value = $page_data[$plasma_field];
                // Map non-empty values, or empty values if explicitly allowed
                if (!empty($value) || $value === '0' || (empty($value) && $this->should_update_empty_fields())) {
                    $pylon_data[$pylon_field] = $value;
                }
            }
        }
        
        // Auto-map exact column name matches (future-proof for new columns)
        foreach ($page_data as $field => $value) {
            // Skip if already mapped or if field doesn't exist in schema
            if (!isset($pylon_data[$field]) && in_array($field, $pylons_columns)) {
                // Map non-empty values, or empty values if explicitly allowed
                if (!empty($value) || $value === '0' || (empty($value) && $this->should_update_empty_fields())) {
                    $pylon_data[$field] = $value;
                }
            }
        }
        
        // Insert the pylon record
        $result = $this->wpdb->insert(
            $pylons_table,
            $pylon_data
        );
        
        if ($result === false) {
            error_log('Grove Plasma Import - Failed to insert pylon record: ' . $this->wpdb->last_error);
            return false;
        }
        
        return $this->wpdb->insert_id;
    }
    
    /**
     * Get column names for a database table
     * 
     * @param string $table_name Full table name with prefix
     * @return array Array of column names
     */
    private function get_table_columns($table_name) {
        $columns = $this->wpdb->get_col("SHOW COLUMNS FROM `{$table_name}`");
        return $columns ? $columns : [];
    }
    
    /**
     * AJAX handler for import request
     */
    public function handle_ajax_import() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_plasma_import')) {
            wp_die('Security check failed');
        }
        
        // Check user permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }
        
        // Get the pages data from POST
        $pages_data = isset($_POST['pages']) ? $_POST['pages'] : [];
        
        if (empty($pages_data)) {
            wp_send_json_error(['message' => 'No pages data provided']);
            return;
        }
        
        // Process the import
        $results = $this->import_pages($pages_data);
        
        // Check if F582 date processing is requested
        $run_f582_option = isset($_POST['run_f582_option']) && $_POST['run_f582_option'] === 'true';
        
        if ($run_f582_option && count($results['success']) > 0) {
            // Run F582 date processing on posts that were imported
            $f582_result = $this->run_f582_date_processing($results);
            if ($f582_result['success']) {
                $results['f582_message'] = $f582_result['message'];
            } else {
                $results['f582_error'] = $f582_result['message'];
            }
        }
        
        // Return results
        if (count($results['errors']) === 0) {
            $message = sprintf('Successfully imported %d pages', count($results['success']));
            if (isset($results['homepage_set']) && $results['homepage_set']) {
                $message .= '. ' . $results['homepage_message'];
            }
            if (isset($results['f582_message'])) {
                $message .= '. ' . $results['f582_message'];
            }
            wp_send_json_success([
                'message' => $message,
                'details' => $results
            ]);
        } else if (count($results['success']) > 0) {
            $message = sprintf('Imported %d of %d pages with some errors', 
                count($results['success']), 
                $results['total']
            );
            if (isset($results['homepage_set']) && $results['homepage_set']) {
                $message .= '. ' . $results['homepage_message'];
            }
            if (isset($results['f582_message'])) {
                $message .= '. ' . $results['f582_message'];
            }
            wp_send_json_success([
                'message' => $message,
                'details' => $results
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Import failed for all pages',
                'details' => $results
            ]);
        }
    }
    
    /**
     * Template assignment is now handled automatically via wp_pylons
     * 
     * The staircase_page_template_desired field is stored in wp_pylons during import,
     * and the theme's staircase_get_current_template() function reads from wp_pylons first.
     * No additional template assignment needed during import.
     * 
     * @param int $post_id WordPress post ID
     * @param array $page_data Original plasma page data
     */
    private function assign_page_template($post_id, $page_data) {
        // Template assignment is handled automatically via wp_pylons table
        // The theme reads staircase_page_template_desired from wp_pylons on page load
        // and normalizes the template name to match available templates
        
        // No action needed here - template assignment happens at render time
    }
    
    /**
     * AJAX handler for driggs data import request
     */
    public function handle_ajax_driggs_import() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'grove_driggs_import')) {
            wp_die('Security check failed');
        }
        
        // Check user permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }
        
        // Get the driggs data from POST
        $driggs_data = isset($_POST['driggs_data']) ? $_POST['driggs_data'] : [];
        
        if (empty($driggs_data)) {
            wp_send_json_error(['message' => 'No driggs data provided']);
            return;
        }
        
        // Process the driggs data import
        $results = $this->import_driggs_data($driggs_data);
        
        // Return results
        if ($results['success']) {
            wp_send_json_success([
                'message' => sprintf('Successfully imported %d driggs data fields to wp_zen_sitespren', count($driggs_data)),
                'details' => $results
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Driggs data import failed: ' . $results['error'],
                'details' => $results
            ]);
        }
    }
    
    /**
     * Import driggs data into wp_zen_sitespren table
     * 
     * @param array $driggs_data Array of driggs field => value pairs
     * @return array Results with success/error information
     */
    private function import_driggs_data($driggs_data) {
        global $wpdb;
        
        $sitespren_table = $wpdb->prefix . 'zen_sitespren';
        
        try {
            // Check if the sitespren record exists (assuming we work with ID 1)
            $existing_record = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $sitespren_table WHERE wppma_id = %d",
                1
            ));
            
            if ($existing_record) {
                // Update existing record
                $update_data = [];
                foreach ($driggs_data as $field => $value) {
                    // Sanitize the field name to prevent SQL injection
                    if (preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
                        $update_data[$field] = $value;
                    }
                }
                
                if (!empty($update_data)) {
                    $result = $wpdb->update(
                        $sitespren_table,
                        $update_data,
                        ['wppma_id' => 1],
                        null,
                        ['%d']
                    );
                    
                    if ($result === false) {
                        return [
                            'success' => false,
                            'error' => 'Database update failed: ' . $wpdb->last_error
                        ];
                    }
                    
                    return [
                        'success' => true,
                        'updated_fields' => array_keys($update_data),
                        'record_id' => 1
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'No valid fields to update'
                    ];
                }
            } else {
                // Create new record with driggs data
                $insert_data = ['wppma_id' => 1];
                foreach ($driggs_data as $field => $value) {
                    // Sanitize the field name to prevent SQL injection
                    if (preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
                        $insert_data[$field] = $value;
                    }
                }
                
                $result = $wpdb->insert(
                    $sitespren_table,
                    $insert_data
                );
                
                if ($result === false) {
                    return [
                        'success' => false,
                        'error' => 'Database insert failed: ' . $wpdb->last_error
                    ];
                }
                
                return [
                    'success' => true,
                    'inserted_fields' => array_keys($insert_data),
                    'record_id' => $wpdb->insert_id
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Run F582 date processing on imported posts
     * This is a centralized version that can be called from different import functions
     */
    private function run_f582_date_processing($import_results) {
        try {
            // Get only the posts (page_type = 'post') from the successfully imported items
            $post_ids = [];
            foreach ($import_results['success'] as $item) {
                if (isset($item['page_type']) && $item['page_type'] === 'post' && isset($item['post_id'])) {
                    $post_ids[] = $item['post_id'];
                }
            }
            
            if (empty($post_ids)) {
                return [
                    'success' => true,
                    'message' => 'No blog posts found for F582 processing'
                ];
            }
            
            global $wpdb;
            
            // F582 settings - using the same defaults as the Date Worshipper
            $backdate_count = min(8, count($post_ids)); // Default to 8 or total posts if less
            $future_count = count($post_ids) - $backdate_count;
            $interval_from = 4; // Default interval from 4 days
            $interval_to = 11;  // Default interval to 11 days
            
            // Shuffle the post IDs for random distribution
            shuffle($post_ids);
            
            // Split posts into backdate and future groups
            $backdate_posts = array_slice($post_ids, 0, $backdate_count);
            $future_posts = array_slice($post_ids, $backdate_count, $future_count);
            
            $current_time = current_time('timestamp');
            $updated_count = 0;
            
            // Process backdate posts (going backward in time)
            $backdate_time = $current_time;
            foreach ($backdate_posts as $post_id) {
                // Generate random interval in seconds
                $min_seconds = $interval_from * 24 * 60 * 60;
                $max_seconds = $interval_to * 24 * 60 * 60;
                $random_seconds = rand($min_seconds, $max_seconds);
                
                // Add random hours, minutes, seconds for more natural distribution
                $random_seconds += rand(0, 24 * 60 * 60);
                
                // Go back in time
                $backdate_time -= $random_seconds;
                
                $new_date = date('Y-m-d H:i:s', $backdate_time);
                
                // Update post date
                $result = $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_date' => $new_date,
                        'post_date_gmt' => get_gmt_from_date($new_date),
                        'post_status' => 'publish',
                        'post_modified' => current_time('mysql'),
                        'post_modified_gmt' => current_time('mysql', 1)
                    ),
                    array('ID' => $post_id),
                    array('%s', '%s', '%s', '%s', '%s'),
                    array('%d')
                );
                
                if ($result !== false) {
                    $updated_count++;
                    clean_post_cache($post_id);
                }
            }
            
            // Process future posts (going forward in time)
            $future_time = $current_time;
            foreach ($future_posts as $post_id) {
                // Generate random interval in seconds
                $min_seconds = $interval_from * 24 * 60 * 60;
                $max_seconds = $interval_to * 24 * 60 * 60;
                $random_seconds = rand($min_seconds, $max_seconds);
                
                // Add random hours, minutes, seconds
                $random_seconds += rand(0, 24 * 60 * 60);
                
                // Go forward in time
                $future_time += $random_seconds;
                
                $new_date = date('Y-m-d H:i:s', $future_time);
                
                // Update post date and set as scheduled
                $result = $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_date' => $new_date,
                        'post_date_gmt' => get_gmt_from_date($new_date),
                        'post_status' => 'future',
                        'post_modified' => current_time('mysql'),
                        'post_modified_gmt' => current_time('mysql', 1)
                    ),
                    array('ID' => $post_id),
                    array('%s', '%s', '%s', '%s', '%s'),
                    array('%d')
                );
                
                if ($result !== false) {
                    $updated_count++;
                    clean_post_cache($post_id);
                    
                    // Schedule the post to be published
                    wp_schedule_single_event($future_time, 'publish_future_post', array($post_id));
                }
            }
            
            return [
                'success' => true,
                'message' => sprintf('F582 processed %d posts (%d backdated, %d scheduled)', 
                    $updated_count, count($backdate_posts), count($future_posts))
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'F582 processing failed: ' . $e->getMessage()
            ];
        }
    }
}