<?php
/**
 * Plugin Name: Grove
 * Plugin URI: https://github.com/transatlanticvoyage/grove
 * Description: Grove - WordPress plugin for zen data fallback and shortcode management
 * Version: 1.1.0
 * Author: Grove Team
 * License: GPL v2 or later
 * Text Domain: grove
 * 
 * Test comment: VSCode source control test - 2025-09-19 15:33
 * Test comment 8: Grove dual visibility test - 2025-09-19 16:48
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GROVE_PLUGIN_VERSION', '1.1.0');
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
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-zen-shortcodes.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-database.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-buffalor.php';
    }
    
    private function init_hooks() {
        new Grove_Admin();
        new Grove_Zen_Shortcodes();
        new Grove_Database();
    }
    
    public function activate() {
        // Create database tables
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-database.php';
        $database = new Grove_Database();
        $database->on_activation();
        
        // Set default shortcode mode if not already set
        if (!get_option('grove_shortcode_mode')) {
            add_option('grove_shortcode_mode', 'automatic');
        }
    }
    
    public function deactivate() {
        // Plugin deactivation tasks  
    }
}

// Initialize the plugin
new GrovePlugin();