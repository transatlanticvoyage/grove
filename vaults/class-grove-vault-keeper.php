<?php
/**
 * Grove Vault Keeper - Sacred Text Repository Manager
 * 
 * This class manages access to all vault content within the Grove plugin.
 * Vaults can be accessed by ANY plugin through the Grove vault API.
 * 
 * @package Grove
 * @subpackage Vaults
 */

if (!defined('ABSPATH')) {
    exit;
}

class Grove_Vault_Keeper {
    
    /**
     * Vault Key Constants - Use these to reference vaults
     */
    const grove_vault_papyrus_1 = 'papyrus/papyrus-1';
    const grove_vault_papyrus_2 = 'papyrus/papyrus-2';
    const grove_vault_papyrus_3 = 'papyrus/papyrus-3';
    
    /**
     * Vault directory path
     */
    private static $vault_path;
    
    /**
     * Cached vault contents
     */
    private static $vault_cache = array();
    
    /**
     * Cache timestamps
     */
    private static $cache_timestamps = array();
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        self::$vault_path = plugin_dir_path(__FILE__);
        
        // Register global function for cross-plugin access
        if (!function_exists('grove_vault')) {
            function grove_vault($vault_name, $bypass_cache = false) {
                return Grove_Vault_Keeper::retrieve($vault_name, $bypass_cache);
            }
        }
    }
    
    /**
     * Retrieve content from a specific vault
     * This is the main method that other plugins will call
     * 
     * @param string $vault_name The name of the vault (e.g., 'papyrus/papyrus-1')
     * @return string|false The vault content or false if not found
     */
    public static function retrieve($vault_name, $bypass_cache = false) {
        $instance = self::get_instance();
        
        // Construct vault file path
        $vault_file = self::$vault_path . $vault_name . '.vault.php';
        
        // Check if file exists first
        if (!file_exists($vault_file)) {
            error_log('Grove Vault Keeper Error: Vault not found - ' . $vault_name);
            return false;
        }
        
        // Get current file modification time
        $current_mtime = filemtime($vault_file);
        
        // Check cache (unless bypassing or file has been modified)
        if (!$bypass_cache && isset(self::$vault_cache[$vault_name])) {
            // Check if file has been modified since caching
            if (isset(self::$cache_timestamps[$vault_name]) && 
                self::$cache_timestamps[$vault_name] == $current_mtime) {
                return self::$vault_cache[$vault_name];
            }
        }
        
        // Security check - ensure we're not accessing files outside the vault
        $real_vault_path = realpath(self::$vault_path);
        $real_file_path = realpath(dirname($vault_file));
        
        if ($real_file_path === false || strpos($real_file_path, $real_vault_path) !== 0) {
            error_log('Grove Vault Keeper Security Error: Attempted to access file outside vault directory');
            return false;
        }
        
        // Clear PHP's opcache for this file to ensure we get fresh content
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($vault_file, true);
        }
        
        // Load the vault content
        $content = include $vault_file;
        
        // Cache the content and timestamp (only if not bypassing cache)
        if (!$bypass_cache) {
            self::$vault_cache[$vault_name] = $content;
            self::$cache_timestamps[$vault_name] = $current_mtime;
        }
        
        // Allow filtering of vault content
        $content = apply_filters('grove_vault_content', $content, $vault_name);
        $content = apply_filters('grove_vault_content_' . str_replace('/', '_', $vault_name), $content);
        
        return $content;
    }
    
    /**
     * List all available vaults
     * 
     * @return array List of vault names
     */
    public static function list_vaults() {
        $vaults = array();
        $vault_pattern = self::$vault_path . '*/*.vault.php';
        
        foreach (glob($vault_pattern) as $vault_file) {
            $relative_path = str_replace(self::$vault_path, '', $vault_file);
            $vault_name = str_replace('.vault.php', '', $relative_path);
            $vaults[] = $vault_name;
        }
        
        return $vaults;
    }
    
    /**
     * Check if a vault exists
     * 
     * @param string $vault_name
     * @return bool
     */
    public static function vault_exists($vault_name) {
        $vault_file = self::$vault_path . $vault_name . '.vault.php';
        return file_exists($vault_file);
    }
    
    /**
     * Get vault metadata
     * 
     * @param string $vault_name The name of the vault
     * @return array Metadata about the vault
     */
    public static function get_metadata($vault_name) {
        $instance = self::get_instance();
        $vault_file = self::$vault_path . $vault_name . '.vault.php';
        
        if (!file_exists($vault_file)) {
            return false;
        }
        
        $metadata = array(
            'name' => $vault_name,
            'path' => $vault_file,
            'plugin' => 'grove',
            'modified' => filemtime($vault_file),
            'modified_date' => date('Y-m-d H:i:s', filemtime($vault_file)),
            'size' => filesize($vault_file),
            'readable_size' => self::format_bytes(filesize($vault_file))
        );
        
        return $metadata;
    }
    
    /**
     * Format bytes into human readable format
     * 
     * @param int $bytes
     * @return string
     */
    private static function format_bytes($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        
        return $bytes;
    }
    
    /**
     * Clear the vault cache
     */
    public static function clear_cache() {
        self::$vault_cache = array();
    }
    
    /**
     * Register AJAX endpoints for cross-plugin access
     */
    public static function register_ajax_endpoints() {
        add_action('wp_ajax_grove_vault_retrieve', array(__CLASS__, 'ajax_retrieve_vault'));
        add_action('wp_ajax_nopriv_grove_vault_retrieve', array(__CLASS__, 'ajax_retrieve_vault'));
    }
    
    /**
     * AJAX handler for retrieving vault content
     */
    public static function ajax_retrieve_vault() {
        check_ajax_referer('grove-vault-nonce', 'nonce');
        
        $vault_name = sanitize_text_field($_POST['vault_name']);
        $content = self::retrieve($vault_name);
        
        if ($content === false) {
            wp_send_json_error('Vault not found');
        }
        
        wp_send_json_success($content);
    }
}

// Initialize the Vault Keeper when Grove loads
add_action('plugins_loaded', function() {
    if (class_exists('Grove_Vault_Keeper')) {
        Grove_Vault_Keeper::get_instance();
        Grove_Vault_Keeper::register_ajax_endpoints();
    }
}, 5); // Priority 5 to load early