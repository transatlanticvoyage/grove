<?php
/**
 * Grove Vault Keys Registry
 * 
 * Centralized definition of all vault keys for easy reference.
 * These keys are used to retrieve sacred texts from the Grove vault system.
 * 
 * Usage:
 *   $content = grove_vault($grove_vault_papyrus_1);
 */

if (!defined('ABSPATH')) {
    exit;
}

// Papyrus Vault Keys - Elementor injection templates
$grove_vault_papyrus_1 = 'papyrus/papyrus-1';
$grove_vault_papyrus_2 = 'papyrus/papyrus-2';
$grove_vault_papyrus_3 = 'papyrus/papyrus-3';

// Future Vault Keys (examples)
$grove_vault_scrolls_config = 'scrolls/config';
$grove_vault_tablets_settings = 'tablets/settings';
$grove_vault_codex_documentation = 'codex/documentation';