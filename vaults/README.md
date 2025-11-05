# 🏛️ GROVE VAULTS - Sacred Text Repository

## Overview
The Grove Vaults system is a centralized repository for storing large, important static text content. These texts are considered "sacred" and can be accessed by ANY plugin in the WordPress installation through Grove's vault API.

## Directory Structure
```
vaults/
├── README.md (this file)
├── class-grove-vault-keeper.php (the vault access manager)
├── papyrus/
│   ├── papyrus-1.vault.php
│   ├── papyrus-2.vault.php (future)
│   └── papyrus-3.vault.php (future)
├── scrolls/ (future - for other sacred texts)
├── tablets/ (future - for configuration texts)
└── codex/ (future - for documentation texts)
```

## Cross-Plugin Access

### Method 1: Direct PHP Access (Recommended)
```php
// From ANY plugin (Ruplin, Klyra, Veyra, etc.)
if (function_exists('grove_vault')) {
    $papyrus_text = grove_vault('papyrus/papyrus-1');
    echo $papyrus_text;
}
```

### Method 2: Using Class Method
```php
// Check if Grove is active and vault keeper exists
if (class_exists('Grove_Vault_Keeper')) {
    $papyrus_text = Grove_Vault_Keeper::retrieve('papyrus/papyrus-1');
    echo $papyrus_text;
}
```

### Method 3: Via WordPress Filters
```php
// Apply filters to vault content
add_filter('grove_vault_content_papyrus_papyrus-1', function($content) {
    // Modify vault content if needed
    return $content;
});
```

### Method 4: AJAX Access (for JavaScript)
```javascript
jQuery.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'grove_vault_retrieve',
        vault_name: 'papyrus/papyrus-1',
        nonce: grove_vault_nonce
    },
    success: function(response) {
        if (response.success) {
            console.log(response.data); // Vault content
        }
    }
});
```

## Naming Convention
- **Vault Files**: `[name].vault.php`
- **Vault Categories**: 
  - `papyrus/` - Elementor injection templates and flows
  - `scrolls/` - Long-form content and documentation
  - `tablets/` - Configuration and settings templates
  - `codex/` - Code snippets and technical documentation

## Adding New Vaults

### Vault File Template
```php
<?php
/**
 * [VAULT NAME]
 * Sacred Text Repository - Grove Plugin Custodian
 * 
 * Description: [What this vault contains]
 * Last Modified: [Date]
 * Custodian: Grove Plugin Vault System
 * Classification: Static Sacred Text
 * Access: Cross-Plugin Available
 */

if (!defined('ABSPATH')) {
    exit;
}

return <<<'VAULT_TEXT'
[Your content here]
VAULT_TEXT;
```

## Available Vaults

### Papyrus Vaults
- `papyrus/papyrus-1` - Main papyrus flow for Elementor injection
- `papyrus/papyrus-2` - (Future) Secondary template
- `papyrus/papyrus-3` - (Future) Tertiary template

## Benefits of Grove Vault System
1. **Cross-Plugin Access**: Any plugin can retrieve Grove vault content
2. **Centralized Management**: All sacred texts in one location
3. **Version Control**: Changes tracked in git
4. **Performance**: Content cached after first retrieval
5. **Security**: Path validation prevents directory traversal
6. **Extensibility**: Filter hooks for content modification
7. **Sacred Custody**: Grove acts as the guardian of important texts

## Security Notes
- Vaults are PHP files that return strings only
- Grove Vault Keeper validates all file paths
- Content is sanitized before output
- AJAX access requires nonce verification
- Direct file access blocked by ABSPATH check

## Best Practices
1. Store only static, reusable text in vaults
2. Use meaningful vault names and categories
3. Document each vault's purpose in its header
4. Don't store sensitive data (passwords, API keys)
5. Clear cache when updating vault contents
6. Use appropriate category folders for organization

## Example: Ruplin Accessing Grove Vault
```php
// In Ruplin's Thunder popup or any other location
if (function_exists('grove_vault')) {
    $papyrus_content = grove_vault('papyrus/papyrus-1');
    if ($papyrus_content) {
        // Use the papyrus content
        echo '<textarea>' . esc_textarea($papyrus_content) . '</textarea>';
    }
}
```

## Vault Keeper API

### Methods
- `Grove_Vault_Keeper::retrieve($vault_name)` - Get vault content
- `Grove_Vault_Keeper::list_vaults()` - List all available vaults
- `Grove_Vault_Keeper::vault_exists($vault_name)` - Check if vault exists
- `Grove_Vault_Keeper::get_metadata($vault_name)` - Get vault metadata
- `Grove_Vault_Keeper::clear_cache()` - Clear vault cache

### Global Function
- `grove_vault($vault_name)` - Simplified access to vault content

## Future Enhancements
- Vault versioning with rollback
- Vault change logging and audit trail
- Import/Export functionality
- Vault content validation
- Multi-language vault support
- Vault access permissions system