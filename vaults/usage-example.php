<?php
/**
 * Grove Vault Usage Examples
 * 
 * This file demonstrates how to reference and use vault keys
 */

// ============================================
// OPTION 1: Using the constant from the class
// ============================================
if (class_exists('Grove_Vault_Keeper')) {
    $content = grove_vault(Grove_Vault_Keeper::grove_vault_papyrus_1);
}

// ============================================
// OPTION 2: Using a local variable
// ============================================
$grove_vault_papyrus_1 = 'papyrus/papyrus-1';
$content = grove_vault($grove_vault_papyrus_1);

// ============================================
// OPTION 3: Define once, use everywhere
// ============================================
// In your plugin's main file:
if (!defined('grove_vault_papyrus_1')) {
    define('grove_vault_papyrus_1', 'papyrus/papyrus-1');
}

// Then anywhere in your code:
$content = grove_vault(grove_vault_papyrus_1);

// ============================================
// PRACTICAL EXAMPLE: In Ruplin's Thunder popup
// ============================================
// Instead of querying database:
// $papyrus = $wpdb->get_var("SELECT papyrus_content FROM...");

// You now use:
if (function_exists('grove_vault')) {
    $papyrus = grove_vault(Grove_Vault_Keeper::grove_vault_papyrus_1);
    echo '<textarea>' . esc_textarea($papyrus) . '</textarea>';
}