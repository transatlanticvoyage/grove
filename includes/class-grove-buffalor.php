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
                    <input type="text" id="shortcode-usage" readonly value="[buffalo_phone_number]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="example-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Example Output (using phone: 1234567890, country code: 1)</label>
                    <textarea id="example-output" readonly style="width: 100%; max-width: 600px; height: 60px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 13px; background-color: #f9f9f9;"><?php echo esc_textarea(self::get_example_output()); ?></textarea>
                </div>
                
                <!-- Black HR separator with 10px weight -->
                <hr style="margin-top: 40px; margin-bottom: 30px; border: none; height: 10px; background-color: black;">
                
                <!-- New Phone Shortcodes Section -->
                <h2 style="margin-bottom: 20px;">Phone Number Shortcodes</h2>
                
                <div style="margin-top: 20px;">
                    <label for="phone-local-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">Local Phone Format Shortcode</label>
                    <input type="text" id="phone-local-shortcode" readonly value="[phone_local]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="phone-local-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="phone-local-output" style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-weight: bold;"><?php echo do_shortcode('[phone_local]'); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="phone-international-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">International Phone Format Shortcode</label>
                    <input type="text" id="phone-international-shortcode" readonly value="[phone_international]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="phone-international-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="phone-international-output" style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-weight: bold;"><?php echo do_shortcode('[phone_international]'); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="phone-link-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">Clickable Phone Link Shortcode</label>
                    <input type="text" id="phone-link-shortcode" readonly value="[phone_link]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="phone-link-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="phone-link-output" style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-weight: bold;"><?php echo do_shortcode('[phone_link]'); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="phone-link-custom-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">Custom Text Phone Link Shortcode</label>
                    <input type="text" id="phone-link-custom-shortcode" readonly value='[phone_link text="📞 Call Now!" format="international"]' style="width: 500px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="phone-link-custom-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="phone-link-custom-output" style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-weight: bold;"><?php echo do_shortcode('[phone_link text="📞 Call Now!" format="international"]'); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="sitespren-country-code-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">Country Code Shortcode</label>
                    <input type="text" id="sitespren-country-code-shortcode" readonly value='[sitespren dbcol="driggs_phone_country_code"]' style="width: 400px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="sitespren-country-code-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="sitespren-country-code-output" style="width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-weight: bold;"><?php echo do_shortcode('[sitespren dbcol="driggs_phone_country_code"]'); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label for="beginning-a-code-moose-shortcode" style="display: block; font-weight: bold; margin-bottom: 5px;">Opening Tel Link Tag Shortcode</label>
                    <input type="text" id="beginning-a-code-moose-shortcode" readonly value="[beginning_a_code_moose]" style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f9f9f9;">
                </div>
                
                <div style="margin-top: 15px;">
                    <label for="beginning-a-code-moose-output" style="display: block; font-weight: bold; margin-bottom: 5px;">Live Output:</label>
                    <div id="beginning-a-code-moose-output" style="width: 500px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; font-family: monospace; font-size: 12px;"><?php echo esc_html(do_shortcode('[beginning_a_code_moose]')); ?></div>
                </div>
                
                <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
                    <h3 style="margin-top: 0; color: #856404;">Usage Examples:</h3>
                    <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
                        <li><strong>[phone_local]</strong> - Displays: (123) 456-7890</li>
                        <li><strong>[phone_international]</strong> - Displays: +1 (123) 456-7890</li>
                        <li><strong>[phone_link]</strong> - Creates clickable link with local format</li>
                        <li><strong>[phone_link format="international"]</strong> - Clickable link with international format</li>
                        <li><strong>[phone_link text="Call Us!"]</strong> - Clickable link with custom text</li>
                        <li><strong>[sitespren dbcol="driggs_phone_country_code"]</strong> - Displays country code: 1</li>
                        <li><strong>[beginning_a_code_moose]</strong> - Outputs opening &lt;a href="tel:+11234567890"&gt; tag</li>
                    </ul>
                    <div style="margin-top: 15px; padding: 10px; background-color: #e7f3ff; border-left: 4px solid #2196F3; color: #1976D2;">
                        <strong>Note:</strong> [beginning_a_code_moose] outputs only the opening &lt;a&gt; tag. You must manually add the closing &lt;/a&gt; tag in your content.
                    </div>
                </div>
                
                <!-- Black HR separator with 10px weight -->
                <hr style="margin-top: 40px; margin-bottom: 30px; border: none; height: 10px; background-color: black;">
                
                <!-- Dynamic Hoof Shortcodes Section -->
                <h2 style="margin-bottom: 20px;">Dynamic Hoof Shortcodes</h2>
                
                <?php
                // Get all hoof codes from database
                $hoof_codes = Grove_Zen_Shortcodes::get_hoof_codes();
                
                if ($hoof_codes) {
                    foreach ($hoof_codes as $hoof) {
                        ?>
                        <div style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                            <div style="margin-bottom: 10px;">
                                <label for="hoof-shortcode-<?php echo $hoof->hoof_id; ?>" style="display: block; font-weight: bold; margin-bottom: 5px;">
                                    <?php echo esc_html($hoof->hoof_title); ?> Shortcode
                                </label>
                                <input type="text" 
                                       id="hoof-shortcode-<?php echo $hoof->hoof_id; ?>" 
                                       readonly 
                                       value="[<?php echo esc_attr($hoof->hoof_slug); ?>]" 
                                       style="width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff;">
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <label for="hoof-content-<?php echo $hoof->hoof_id; ?>" style="display: block; font-weight: bold; margin-bottom: 5px;">
                                    Shortcode Content (editable - can contain other shortcodes)
                                </label>
                                <textarea id="hoof-content-<?php echo $hoof->hoof_id; ?>" 
                                          data-hoof-id="<?php echo $hoof->hoof_id; ?>"
                                          class="hoof-content-editor"
                                          style="width: 100%; max-width: 800px; height: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 13px;"><?php echo esc_textarea($hoof->hoof_content); ?></textarea>
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <button type="button" 
                                        class="save-hoof-btn button button-primary" 
                                        data-hoof-id="<?php echo $hoof->hoof_id; ?>"
                                        style="margin-right: 10px;">
                                    Save Changes
                                </button>
                                <span class="save-status" id="save-status-<?php echo $hoof->hoof_id; ?>" style="color: green; display: none;">✓ Saved</span>
                            </div>
                            
                            <div style="margin-top: 10px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Live Preview:</label>
                                <div id="hoof-preview-<?php echo $hoof->hoof_id; ?>" 
                                     style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #fff;">
                                    <?php echo do_shortcode('[' . $hoof->hoof_slug . ']'); ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($hoof->hoof_description)) : ?>
                                <div style="margin-top: 10px; padding: 8px; background-color: #f0f8ff; border-radius: 4px;">
                                    <small style="color: #666;"><?php echo esc_html($hoof->hoof_description); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No hoof codes found. They will be created on plugin activation.</p>';
                }
                ?>
                
                <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
                    <h3 style="margin-top: 0; color: #856404;">About Hoof Shortcodes</h3>
                    <p style="color: #856404;">Hoof shortcodes are dynamic, editable shortcodes that can contain HTML and other shortcodes. They are stored in the database and can be modified here without changing code files.</p>
                    <p style="color: #856404;">To manage all hoof codes (add, delete, reorder), visit <a href="<?php echo admin_url('admin.php?page=grove_hoof_mar'); ?>">Grove Hoof Manager</a>.</p>
                </div>
                
                <script>
                jQuery(document).ready(function($) {
                    // Save hoof content on button click
                    $('.save-hoof-btn').on('click', function() {
                        var hoofId = $(this).data('hoof-id');
                        var content = $('#hoof-content-' + hoofId).val();
                        var statusSpan = $('#save-status-' + hoofId);
                        var previewDiv = $('#hoof-preview-' + hoofId);
                        var button = $(this);
                        
                        button.prop('disabled', true).text('Saving...');
                        
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'grove_update_hoof_code',
                                hoof_id: hoofId,
                                content: content,
                                _ajax_nonce: '<?php echo wp_create_nonce('grove_hoof_nonce'); ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    statusSpan.show().delay(2000).fadeOut();
                                    // Update preview with the processed content
                                    if (response.data.preview) {
                                        previewDiv.html(response.data.preview);
                                    }
                                } else {
                                    alert('Error saving: ' + response.data);
                                }
                                button.prop('disabled', false).text('Save Changes');
                            },
                            error: function() {
                                alert('Error saving changes');
                                button.prop('disabled', false).text('Save Changes');
                            }
                        });
                    });
                });
                </script>
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
    global $wpdb;
    
    $atts = shortcode_atts(array(
        \'wppma_id\' => \'1\',
        \'prefix\' => \'\', // Will be set dynamically from database
        \'text\' => \'Call us: \'
    ), $atts, \'buffalo_phone_number\');
    
    // Get phone and country code from zen_sitespren table
    $table_name = $wpdb->prefix . \'zen_sitespren\';
    $sitespren = $wpdb->get_row($wpdb->prepare(
        "SELECT driggs_phone_1, driggs_phone_country_code FROM $table_name WHERE wppma_id = %d",
        intval($atts[\'wppma_id\'])
    ));
    
    if (!$sitespren || empty($sitespren->driggs_phone_1)) {
        return \'<!-- No phone number found -->\';
    }
    
    $phone = $sitespren->driggs_phone_1;
    
    // Use dynamic country code from database, fallback to 1
    $country_code = !empty($sitespren->driggs_phone_country_code) ? $sitespren->driggs_phone_country_code : \'1\';
    
    // Override prefix if not manually set
    if (empty($atts[\'prefix\'])) {
        $atts[\'prefix\'] = \'+\' . $country_code;
    }
    
    return \'<div class="phone-number"><a href="tel:\' . esc_attr($atts[\'prefix\']) . esc_attr($phone) . \'">\' . esc_html($atts[\'text\']) . esc_html($phone) . \'</a></div>\';
}
add_shortcode(\'buffalo_phone_number\', \'buffalo_phone_number_shortcode\');';
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