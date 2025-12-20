<?php
/**
 * Plugin Name: Grove (Elementor-Safe v1.2)
 * Plugin URI: https://github.com/transatlanticvoyage/grove
 * Description: Grove - WordPress plugin for zen data fallback and shortcode management - Works with or without Elementor
 * Version: 1.2.0
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
define('GROVE_PLUGIN_VERSION', '1.2.0');
define('GROVE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GROVE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Main plugin class
class GrovePlugin {
    
    private $elementor_available = false;
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Check if Elementor is available and active
     */
    public function is_elementor_available() {
        return class_exists('Elementor\\Plugin') && is_plugin_active('elementor/elementor.php');
    }
    
    public function init() {
        // Check Elementor availability
        $this->elementor_available = $this->is_elementor_available();
        
        // Define constant for global access
        if (!defined('GROVE_ELEMENTOR_AVAILABLE')) {
            define('GROVE_ELEMENTOR_AVAILABLE', $this->elementor_available);
        }
        
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-admin.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-pagebender.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-quilter.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-panzer.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-tax-exports.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-zen-shortcodes.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-database.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-buffalor.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-chimp.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-raven-mar.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-streamflow-mar.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-plasma-import-mar.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-plasma-import-processor.php';
        
        // Load the Vault Keeper for cross-plugin sacred text access
        require_once GROVE_PLUGIN_PATH . 'vaults/class-grove-vault-keeper.php';
        require_once GROVE_PLUGIN_PATH . 'includes/class-grove-silk-dynamic-menu.php';
    }
    
    private function init_hooks() {
        new Grove_Admin();
        new Grove_Zen_Shortcodes();
        new Grove_Database();
        new Grove_Quilter();
        new Grove_Panzer();
        new Grove_Chimp();
        
        // Initialize plasma import processor and AJAX handlers
        $this->init_plasma_import();
    }
    
    /**
     * Initialize plasma import functionality
     */
    private function init_plasma_import() {
        $processor = new Grove_Plasma_Import_Processor();
        
        // Register AJAX handlers
        add_action('wp_ajax_grove_plasma_import', array($processor, 'handle_ajax_import'));
        add_action('wp_ajax_nopriv_grove_plasma_import', array($processor, 'handle_ajax_import'));
        add_action('wp_ajax_grove_driggs_data_import', array($processor, 'handle_ajax_driggs_import'));
        add_action('wp_ajax_nopriv_grove_driggs_data_import', array($processor, 'handle_ajax_driggs_import'));
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