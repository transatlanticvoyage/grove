<?php
/**
 * Grove Hoof Manager Class
 */

class Grove_Hoof_Mar {
    
    /**
     * Render the Grove Hoof Manager page
     */
    public static function render_page() {
        ?>
        <div class="wrap">
            <h1>Grove Hoof Manager</h1>
            <p>Manage dynamic hoof shortcodes that can contain HTML and other shortcodes.</p>
            
            <div style="margin-top: 20px;">
                <button type="button" id="add-new-hoof" class="button button-primary">Add New Hoof Code</button>
            </div>
            
            <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Slug</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Content</th>
                        <th>Active</th>
                        <th>System</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="hoof-codes-list">
                    <?php
                    global $wpdb;
                    $table = $wpdb->prefix . 'zen_hoof_codes';
                    $hoof_codes = $wpdb->get_results("SELECT * FROM $table ORDER BY position_order ASC, hoof_id ASC");
                    
                    if ($hoof_codes) {
                        foreach ($hoof_codes as $hoof) {
                            ?>
                            <tr data-hoof-id="<?php echo $hoof->hoof_id; ?>">
                                <td><?php echo $hoof->hoof_id; ?></td>
                                <td>
                                    <input type="text" class="hoof-slug" value="<?php echo esc_attr($hoof->hoof_slug); ?>" <?php echo $hoof->is_system ? 'readonly' : ''; ?>>
                                    <br><small>[<?php echo esc_html($hoof->hoof_slug); ?>]</small>
                                </td>
                                <td>
                                    <input type="text" class="hoof-title" value="<?php echo esc_attr($hoof->hoof_title); ?>">
                                </td>
                                <td>
                                    <textarea class="hoof-description" rows="2" style="width: 100%;"><?php echo esc_textarea($hoof->hoof_description); ?></textarea>
                                </td>
                                <td>
                                    <textarea class="hoof-content" rows="3" style="width: 100%; font-family: monospace; font-size: 11px;"><?php echo esc_textarea($hoof->hoof_content); ?></textarea>
                                </td>
                                <td>
                                    <input type="checkbox" class="hoof-active" <?php checked($hoof->is_active, 1); ?>>
                                </td>
                                <td>
                                    <?php echo $hoof->is_system ? '✓' : ''; ?>
                                </td>
                                <td>
                                    <button type="button" class="button save-hoof" data-hoof-id="<?php echo $hoof->hoof_id; ?>">Save</button>
                                    <?php if (!$hoof->is_system) : ?>
                                        <button type="button" class="button delete-hoof" data-hoof-id="<?php echo $hoof->hoof_id; ?>" style="color: red;">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="8">No hoof codes found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- Add New Hoof Code Modal -->
            <div id="new-hoof-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; width: 500px;">
                <h2>Add New Hoof Code</h2>
                <div style="margin-bottom: 10px;">
                    <label>Slug (no spaces, lowercase):</label><br>
                    <input type="text" id="new-hoof-slug" style="width: 100%;" placeholder="my_custom_shortcode">
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Title:</label><br>
                    <input type="text" id="new-hoof-title" style="width: 100%;" placeholder="My Custom Shortcode">
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Description:</label><br>
                    <textarea id="new-hoof-description" style="width: 100%;" rows="2"></textarea>
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Content (can include HTML and shortcodes):</label><br>
                    <textarea id="new-hoof-content" style="width: 100%; font-family: monospace;" rows="4"></textarea>
                </div>
                <div>
                    <button type="button" id="create-hoof" class="button button-primary">Create</button>
                    <button type="button" id="cancel-new-hoof" class="button">Cancel</button>
                </div>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                // Add new hoof code
                $('#add-new-hoof').on('click', function() {
                    $('#new-hoof-modal').show();
                });
                
                $('#cancel-new-hoof').on('click', function() {
                    $('#new-hoof-modal').hide();
                });
                
                $('#create-hoof').on('click', function() {
                    var slug = $('#new-hoof-slug').val();
                    var title = $('#new-hoof-title').val();
                    var description = $('#new-hoof-description').val();
                    var content = $('#new-hoof-content').val();
                    
                    if (!slug) {
                        alert('Slug is required');
                        return;
                    }
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_hoof_create',
                            slug: slug,
                            title: title,
                            description: description,
                            content: content,
                            _ajax_nonce: '<?php echo wp_create_nonce('grove_hoof_admin_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + response.data);
                            }
                        }
                    });
                });
                
                // Save hoof code
                $('.save-hoof').on('click', function() {
                    var button = $(this);
                    var row = button.closest('tr');
                    var hoofId = button.data('hoof-id');
                    
                    var data = {
                        action: 'grove_hoof_update',
                        hoof_id: hoofId,
                        slug: row.find('.hoof-slug').val(),
                        title: row.find('.hoof-title').val(),
                        description: row.find('.hoof-description').val(),
                        content: row.find('.hoof-content').val(),
                        is_active: row.find('.hoof-active').is(':checked') ? 1 : 0,
                        _ajax_nonce: '<?php echo wp_create_nonce('grove_hoof_admin_nonce'); ?>'
                    };
                    
                    button.prop('disabled', true).text('Saving...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                button.text('Saved!');
                                setTimeout(function() {
                                    button.prop('disabled', false).text('Save');
                                }, 2000);
                            } else {
                                alert('Error: ' + response.data);
                                button.prop('disabled', false).text('Save');
                            }
                        }
                    });
                });
                
                // Delete hoof code
                $('.delete-hoof').on('click', function() {
                    if (!confirm('Are you sure you want to delete this hoof code?')) {
                        return;
                    }
                    
                    var button = $(this);
                    var hoofId = button.data('hoof-id');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'grove_hoof_delete',
                            hoof_id: hoofId,
                            _ajax_nonce: '<?php echo wp_create_nonce('grove_hoof_admin_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                button.closest('tr').fadeOut(function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Error: ' + response.data);
                            }
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }
}