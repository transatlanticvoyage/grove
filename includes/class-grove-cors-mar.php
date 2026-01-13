<?php

/**
 * Grove CORS Mar Page Class
 * Handles CORS configuration for Grove plugin endpoints
 */
class Grove_Cors_Mar {
    
    public function __construct() {
        // Initialize CORS settings handling
        $this->init_cors_headers();
    }
    
    /**
     * Initialize CORS headers based on settings
     */
    private function init_cors_headers() {
        // Only add CORS headers if enabled in settings
        $cors_enabled = get_option('grove_cors_enabled', 'no');
        
        if ($cors_enabled === 'yes') {
            // Add CORS headers for specific Grove AJAX actions
            $enabled_endpoints = get_option('grove_cors_endpoints', array());
            
            foreach ($enabled_endpoints as $endpoint) {
                add_action('wp_ajax_' . $endpoint, array($this, 'add_cors_headers'), 5);
                add_action('wp_ajax_nopriv_' . $endpoint, array($this, 'add_cors_headers'), 5);
            }
        }
    }
    
    /**
     * Add CORS headers to response
     */
    public function add_cors_headers() {
        $allowed_origins = get_option('grove_cors_allowed_origins', 'http://localhost:3000');
        $allowed_origins_array = array_map('trim', explode("\n", $allowed_origins));
        
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        // Check if origin is in allowed list
        if (in_array($origin, $allowed_origins_array)) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Allow-Credentials: true');
        } elseif (in_array('*', $allowed_origins_array)) {
            // Allow all origins if * is specified (use with caution!)
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
        }
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }
    
    /**
     * Display the CORS configuration page
     */
    public function grove_cors_mar_page() {
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        $this->suppress_all_admin_notices();
        
        // Handle form submission
        if (isset($_POST['grove_cors_submit'])) {
            $this->save_cors_settings();
        }
        
        // Get current settings
        $cors_enabled = get_option('grove_cors_enabled', 'no');
        $allowed_origins = get_option('grove_cors_allowed_origins', "http://localhost:3000\nhttp://localhost:3001");
        $enabled_endpoints = get_option('grove_cors_endpoints', array());
        
        // Available Grove endpoints
        $available_endpoints = array(
            'grove_plasma_import' => 'Plasma Import (f47)',
            'grove_driggs_data_import' => 'Driggs Data Import (f51)',
            'grove_upload_json_file' => 'File Upload',
            'grove_process_json_file' => 'File Processing',
            'grove_bulk_operations' => 'Bulk Operations',
        );
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px;">
                    <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <img src="<?php echo plugin_dir_url(__FILE__) . '../grove-shenzi-asset-mirror/grove-logo-1.png'; ?>" alt="Grove Logo" style="height: 40px; width: auto;">
                        Grove CORS Mar
                    </h1>
                </div>
                
                <!-- Main Settings Section -->
                <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">CORS Configuration Settings</h3>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('grove_cors_settings', 'grove_cors_nonce'); ?>
                        
                        <!-- Enable/Disable CORS -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: center; font-weight: bold; margin-bottom: 10px;">
                                <input type="checkbox" name="grove_cors_enabled" value="yes" 
                                       <?php checked($cors_enabled, 'yes'); ?> style="margin-right: 8px;">
                                Enable CORS Headers for Grove Plugin Endpoints
                            </label>
                            <p style="color: #666; margin: 5px 0 0 24px; font-size: 13px;">
                                When enabled, adds CORS headers to selected Grove AJAX endpoints to allow cross-origin requests.
                            </p>
                        </div>
                        
                        <!-- Allowed Origins -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 8px;">
                                Allowed Origins (one per line)
                            </label>
                            <textarea name="grove_cors_allowed_origins" 
                                      rows="4" 
                                      style="width: 100%; max-width: 500px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace;"
                                      placeholder="http://localhost:3000"><?php echo esc_textarea($allowed_origins); ?></textarea>
                            <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">
                                Enter allowed origins that can make requests to your Grove endpoints.<br>
                                <strong>Security Warning:</strong> Only add trusted domains. Never use * in production!<br>
                                <code style="background: #f0f0f0; padding: 2px 4px;">Example: http://localhost:3000</code>
                            </p>
                        </div>
                        
                        <!-- Endpoint Selection -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 8px;">
                                Enable CORS for these endpoints:
                            </label>
                            <div style="padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; max-width: 600px;">
                                <?php foreach ($available_endpoints as $endpoint => $description): ?>
                                    <label style="display: flex; align-items: center; margin-bottom: 8px; cursor: pointer;">
                                        <input type="checkbox" 
                                               name="grove_cors_endpoints[]" 
                                               value="<?php echo esc_attr($endpoint); ?>"
                                               <?php echo in_array($endpoint, $enabled_endpoints) ? 'checked' : ''; ?>
                                               style="margin-right: 8px;">
                                        <strong><?php echo esc_html($endpoint); ?></strong>
                                        <span style="color: #666; margin-left: 10px;">- <?php echo esc_html($description); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Save Button -->
                        <div style="margin-top: 20px;">
                            <button type="submit" name="grove_cors_submit" class="button button-primary">
                                Save CORS Settings
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Code Example Section -->
                <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <h3 style="margin-top: 0;">Generated CORS Headers</h3>
                    
                    <div style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px; overflow-x: auto;">
                        <div style="color: #608b4e;">// This code is automatically added when CORS is enabled</div>
                        <div style="color: #608b4e;">// Location: Applied dynamically via WordPress hooks</div>
                        <div style="margin-top: 10px;">
                            <span style="color: #569cd6;">add_action</span>(<span style="color: #ce9178;">'wp_ajax_<?php 
                                echo esc_html(!empty($enabled_endpoints) ? $enabled_endpoints[0] : 'grove_plasma_import'); 
                            ?>'</span>, <span style="color: #569cd6;">function</span>() {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #9cdcfe;">$origin</span> = <span style="color: #9cdcfe;">$_SERVER</span>[<span style="color: #ce9178;">'HTTP_ORIGIN'</span>] ?? <span style="color: #ce9178;">''</span>;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #9cdcfe;">$allowed</span> = [<span style="color: #ce9178;"><?php 
                                $origins_array = array_map('trim', explode("\n", $allowed_origins));
                                echo "'" . implode("', '", array_slice($origins_array, 0, 2)) . "'";
                            ?></span>];<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #c586c0;">if</span> (<span style="color: #dcdcaa;">in_array</span>(<span style="color: #9cdcfe;">$origin</span>, <span style="color: #9cdcfe;">$allowed</span>)) {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #dcdcaa;">header</span>(<span style="color: #ce9178;">"Access-Control-Allow-Origin: </span><span style="color: #9cdcfe;">$origin</span><span style="color: #ce9178;">"</span>);<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #dcdcaa;">header</span>(<span style="color: #ce9178;">'Access-Control-Allow-Methods: POST, GET, OPTIONS'</span>);<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #dcdcaa;">header</span>(<span style="color: #ce9178;">'Access-Control-Allow-Headers: Content-Type'</span>);<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            }, <span style="color: #b5cea8;">5</span>); <span style="color: #608b4e;">// Priority 5 - runs before main handler</span>
                        </div>
                    </div>
                    
                    <p style="color: #666; margin: 15px 0 0 0; font-size: 13px;">
                        <strong>Note:</strong> This code is automatically applied via WordPress action hooks when CORS is enabled.<br>
                        No manual file editing required. Settings are stored in the WordPress database.
                    </p>
                </div>
                
                <!-- Security Notice -->
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; border-left: 4px solid #ff9800;">
                    <h4 style="margin: 0 0 10px 0; color: #856404;">⚠️ Security Considerations</h4>
                    <ul style="margin: 5px 0; padding-left: 20px; color: #856404;">
                        <li>Only enable CORS for local development or trusted domains</li>
                        <li>Never use wildcard (*) origins on production sites</li>
                        <li>Disable CORS when not actively needed</li>
                        <li>Consider using authentication tokens for additional security</li>
                        <li>These settings affect server-to-server and browser-to-server communications</li>
                    </ul>
                </div>
                
                <!-- Current Status -->
                <div style="background: <?php echo $cors_enabled === 'yes' ? '#d4edda' : '#f8d7da'; ?>; 
                            border: 1px solid <?php echo $cors_enabled === 'yes' ? '#28a745' : '#dc3545'; ?>; 
                            padding: 15px; border-radius: 5px; margin-top: 20px;">
                    <strong>Current Status:</strong> CORS is <?php echo $cors_enabled === 'yes' ? '✅ ENABLED' : '❌ DISABLED'; ?>
                    <?php if ($cors_enabled === 'yes' && !empty($enabled_endpoints)): ?>
                        <br>Active on <?php echo count($enabled_endpoints); ?> endpoint(s)
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save CORS settings
     */
    private function save_cors_settings() {
        // Verify nonce
        if (!isset($_POST['grove_cors_nonce']) || !wp_verify_nonce($_POST['grove_cors_nonce'], 'grove_cors_settings')) {
            return;
        }
        
        // Save enabled status
        $cors_enabled = isset($_POST['grove_cors_enabled']) && $_POST['grove_cors_enabled'] === 'yes' ? 'yes' : 'no';
        update_option('grove_cors_enabled', $cors_enabled);
        
        // Save allowed origins
        $allowed_origins = isset($_POST['grove_cors_allowed_origins']) ? sanitize_textarea_field($_POST['grove_cors_allowed_origins']) : '';
        update_option('grove_cors_allowed_origins', $allowed_origins);
        
        // Save enabled endpoints
        $endpoints = isset($_POST['grove_cors_endpoints']) && is_array($_POST['grove_cors_endpoints']) 
            ? array_map('sanitize_text_field', $_POST['grove_cors_endpoints']) 
            : array();
        update_option('grove_cors_endpoints', $endpoints);
        
        // Show success message
        echo '<div class="notice notice-success" style="margin: 20px;"><p>CORS settings saved successfully!</p></div>';
    }
    
    /**
     * Suppress all admin notices
     */
    private function suppress_all_admin_notices() {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        
        // Additional aggressive suppression
        add_action('admin_notices', function() {
            // Return empty to prevent any notices
        }, PHP_INT_MAX);
        
        add_action('all_admin_notices', function() {
            // Return empty to prevent any notices
        }, PHP_INT_MAX);
    }
}