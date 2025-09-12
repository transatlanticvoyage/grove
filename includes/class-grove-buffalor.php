<?php
/**
 * Grove Buffalo Manager Class
 */

class Grove_Buffalor {
    
    public function __construct() {
        // Constructor for future functionality
    }
    
    /**
     * Render the Grove Buffalo Manager page
     */
    public static function render_page() {
        // AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
        self::suppress_all_admin_notices();
        
        ?>
        <div class="wrap" style="margin: 0; padding: 0;">
            <!-- Allow space for WordPress notices -->
            <div style="height: 20px;"></div>
            
            <div style="padding: 20px;">
                <h1>Grove Buffalo Manager Page</h1>
                
                <div style="margin-top: 20px;">
                    <label for="antler-box-1" style="display: block; font-weight: bold; margin-bottom: 5px;">antler box 1</label>
                    <input type="text" id="antler-box-1" name="antler-box-1" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                
                <div style="margin-top: 30px;">
                    <label for="shortcode-registration-code" style="display: block; font-weight: bold; margin-bottom: 5px;">Shortcode Registration Code</label>
                    <textarea id="shortcode-registration-code" readonly style="width: 100%; max-width: 800px; height: 150px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 13px; background-color: #f9f9f9;"><?php echo esc_textarea(self::get_shortcode_registration_code()); ?></textarea>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="shortcode-usage" style="display: block; font-weight: bold; margin-bottom: 5px;">Shortcode Usage</label>
                    <input type="text" id="shortcode-usage" readonly value="[buffalo phone number]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="example-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Example Output (using phone: 1234567890)</label>
                    <textarea id="example-output" readonly style="width: 100%; max-width: 600px; height: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 13px; background-color: #f9f9f9;"><?php echo esc_textarea(self::get_example_output()); ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AGGRESSIVE NOTICE SUPPRESSION - Remove ALL WordPress admin notices
     * Based on proven Snefuruplin implementation
     */
    private static function suppress_all_admin_notices() {
        // Remove all admin notices immediately
        add_action('admin_print_styles', function() {
            // Remove all notice actions
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            
            // Remove user admin notices
            global $wp_filter;
            if (isset($wp_filter['user_admin_notices'])) {
                unset($wp_filter['user_admin_notices']);
            }
        }, 0);
        
        // Additional cleanup for persistent notices
        add_action('admin_head', function() {
            // Hide any notices that slip through via CSS
            echo '<style type="text/css">
                .notice, .notice-warning, .notice-error, .notice-success, .notice-info,
                .updated, .error, .update-nag, .admin-notice,
                .plugin-update-tr, .update-message, .updating-message,
                #wpbody-content > .wrap > .notice,
                #wpbody-content > .notice,
                .wrap > .notice,
                div.notice, div.error, div.updated {
                    display: none !important;
                }
            </style>';
        }, 0);
        
        // Remove notices from output buffer
        add_action('admin_footer', function() {
            if (ob_get_level()) {
                $output = ob_get_contents();
                if ($output) {
                    $output = preg_replace('/<div[^>]*class="[^"]*notice[^"]*"[^>]*>.*?<\/div>/s', '', $output);
                    $output = preg_replace('/<div[^>]*class="[^"]*error[^"]*"[^>]*>.*?<\/div>/s', '', $output);
                    $output = preg_replace('/<div[^>]*class="[^"]*updated[^"]*"[^>]*>.*?<\/div>/s', '', $output);
                    ob_clean();
                    echo $output;
                }
            }
        }, PHP_INT_MAX);
    }
    
    /**
     * Get shortcode registration code
     */
    private static function get_shortcode_registration_code() {
        return 'function buffalo_phone_number_shortcode($atts) {
    $atts = shortcode_atts(array(
        \'phone\' => \'1234567890\',
        \'prefix\' => \'+1\',
        \'text\' => \'Call us: \'
    ), $atts);
    
    return \'<div class="phone-number"><a href="tel:\' . $atts[\'prefix\'] . $atts[\'phone\'] . \'">\' . $atts[\'text\'] . $atts[\'phone\'] . \'</a></div>\';
}
add_shortcode(\'buffalo phone number\', \'buffalo_phone_number_shortcode\');';
    }
    
    /**
     * Get example output
     */
    private static function get_example_output() {
        return '<div class="phone-number">
  <a href="tel:+11234567890">Call us: 1234567890</a>
</div>';
    }
}