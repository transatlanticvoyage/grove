<?php
/**
 * Plugin Name: Grove
 * Plugin URI: https://github.com/transatlanticvoyage/grove-wp-plugin
 * Description: Grove - WordPress plugin for zen data fallback and shortcode management
 * Version: 1.0.0
 * Author: Grove Team
 * License: GPL v2 or later
 * Text Domain: grove
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GROVE_PLUGIN_VERSION', '1.0.0');
define('GROVE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GROVE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Main plugin class
class GrovePlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-admin.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-tax-exports.php';
    }
    
    private function init_hooks() {
        new Grove_Admin();
    }
    
    public function activate() {
        // Plugin activation tasks
    }
    
    public function deactivate() {
        // Plugin deactivation tasks  
    }
}

// Initialize the plugin
new GrovePlugin();