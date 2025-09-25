<?php

/**
 * Grove Tax Exports Class
 * Handles sharkintax and walrustax export formats for zen_sitespren data
 */
class Grove_Tax_Exports {
    
    /**
     * Generate sharkintax format output
     * Format: Field name with separators, followed by value
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @return string Formatted sharkintax output
     */
    public static function generate_sharkintax($data, $fields = null) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        $output = '';
        
        foreach ($fields as $field) {
            // Check if this is a section header (doesn't start with 'driggs_' or 'sitespren_')
            $isSection = strpos($field, 'driggs_') !== 0 && 
                         strpos($field, 'sitespren_') !== 0 && 
                         strpos($field, 'section') !== false;
            
            $output .= "===================================\n";
            $output .= $field . "\n";
            $output .= "-----------------------------------\n";
            
            if (!$isSection) {
                // Get the actual value from data
                $value = isset($data[$field]) ? $data[$field] : '';
                $output .= $value . "\n";
            } else {
                $output .= "\n"; // Empty line for sections
            }
        }
        
        return $output;
    }
    
    /**
     * Generate walrustax format output
     * Format: Tab-separated field name and value
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @return string Formatted walrustax output
     */
    public static function generate_walrustax($data, $fields = null) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        $output = '';
        
        foreach ($fields as $field) {
            // Check if this is a section header
            $isSection = strpos($field, 'driggs_') !== 0 && 
                         strpos($field, 'sitespren_') !== 0 && 
                         strpos($field, 'section') !== false;
            
            if ($isSection) {
                $output .= $field . "\n";
            } else {
                // Get the actual value from data
                $value = isset($data[$field]) ? $data[$field] : '';
                $output .= $field . "\t" . $value . "\n";
            }
        }
        
        return $output;
    }
    
    /**
     * Generate CSV format output
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @return string CSV formatted output
     */
    public static function generate_csv($data, $fields = null) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        $output = '';
        
        // Add header row
        $output .= implode(',', array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $fields)) . "\n";
        
        // Add data row
        $values = array();
        foreach ($fields as $field) {
            $value = isset($data[$field]) ? $data[$field] : '';
            $values[] = '"' . str_replace('"', '""', $value) . '"';
        }
        $output .= implode(',', $values) . "\n";
        
        return $output;
    }
    
    /**
     * Generate SQL INSERT statement
     * 
     * @param array $data The sitespren data array
     * @param string $table_name The table name (defaults to wp_zen_sitespren)
     * @return string SQL INSERT statement
     */
    public static function generate_sql($data, $table_name = null) {
        global $wpdb;
        
        if (!$data) {
            return '';
        }
        
        if (!$table_name) {
            $table_name = $wpdb->prefix . 'zen_sitespren';
        }
        
        $fields = array();
        $values = array();
        
        foreach ($data as $key => $value) {
            // Skip WordPress-specific fields
            if (strpos($key, 'wppma_') === 0) {
                continue;
            }
            
            $fields[] = '`' . $key . '`';
            
            if ($value === null) {
                $values[] = 'NULL';
            } elseif (is_bool($value)) {
                $values[] = $value ? '1' : '0';
            } elseif (is_numeric($value)) {
                $values[] = $value;
            } else {
                $values[] = "'" . esc_sql($value) . "'";
            }
        }
        
        $sql = "INSERT INTO `{$table_name}` (\n    " . 
               implode(",\n    ", $fields) . 
               "\n) VALUES (\n    " . 
               implode(",\n    ", $values) . 
               "\n);";
        
        return $sql;
    }
    
    /**
     * Generate Excel-compatible XML (for .xls export)
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @return string Excel XML format
     */
    public static function generate_xls($data, $fields = null) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        $xml = '<?xml version="1.0"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="header">' . "\n";
        $xml .= '<Font ss:Bold="1"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '</Styles>' . "\n";
        
        $xml .= '<Worksheet ss:Name="Sitespren Data">' . "\n";
        $xml .= '<Table>' . "\n";
        
        // Header row
        $xml .= '<Row ss:StyleID="header">' . "\n";
        foreach ($fields as $field) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($field) . '</Data></Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";
        
        // Data row
        $xml .= '<Row>' . "\n";
        foreach ($fields as $field) {
            $value = isset($data[$field]) ? $data[$field] : '';
            $type = is_numeric($value) ? 'Number' : 'String';
            $xml .= '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
        }
        $xml .= '</Row>' . "\n";
        
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>' . "\n";
        
        return $xml;
    }
    
    /**
     * Get default field list for exports
     * This defines the standard order and grouping of fields
     * 
     * @return array Field list with section headers
     */
    public static function get_default_field_list() {
        return array(
            // Basic Information Section
            'basic_info_section',
            'driggs_brand_name',
            'driggs_industry',
            'driggs_city',
            'driggs_site_type_purpose',
            'driggs_short_descr',
            'driggs_long_descr',
            
            // Contact Section
            'contact_section',
            'driggs_email_1',
            'driggs_phone_1',
            'driggs_phone1_platform_id',
            'driggs_owner_name',
            
            // Address Section
            'address_section',
            'driggs_address_full',
            'driggs_street_1',
            'driggs_street_2',
            'driggs_city',
            'driggs_state_code',
            'driggs_state_full',
            'driggs_zip',
            'driggs_country',
            'driggs_address_species_id',
            'driggs_address_species_note',
            
            // Business Details Section
            'business_section',
            'driggs_year_opened',
            'driggs_employees_qty',
            'driggs_revenue_goal',
            'driggs_hours',
            'driggs_payment_methods',
            
            // Marketing Section
            'marketing_section',
            'driggs_keywords',
            'driggs_category',
            'driggs_cgig_id',
            'driggs_social_media_links',
            'driggs_citations_done',
            'driggs_social_profiles_done',
            
            // AI Notes Section
            'ai_section',
            'driggs_special_note_for_ai_tool',
            
            // Site Information Section
            'site_section',
            'sitespren_base',
            'true_root_domain',
            'full_subdomain',
            'webproperty_type'
        );
    }
    
    /**
     * Generate Nova Beluga format output with both field name sets
     * Format: Friendly name - Field name with separators, followed by value
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @param bool $omit_no_friendly Skip fields without lighthouse friendly names
     * @param bool $use_custom_position Order by custom_position_a field
     * @return string Formatted nova beluga output with both names
     */
    public static function generate_nova_beluga_both($data, $fields = null, $omit_no_friendly = false, $use_custom_position = false) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        global $wpdb;
        
        // If using custom position ordering, reorder the fields based on lighthouse data
        if ($use_custom_position) {
            $fields_with_positions = array();
            $fields_without_positions = array();
            
            foreach ($fields as $field) {
                // Skip section headers for position lookup
                $isSection = strpos($field, 'driggs_') !== 0 && 
                             strpos($field, 'sitespren_') !== 0 && 
                             strpos($field, 'section') !== false;
                
                if ($isSection) {
                    $fields_without_positions[] = array('field' => $field, 'position' => 999);
                } else {
                    $table_name = str_replace('zen_', '', 'sitespren');
                    $position_result = $wpdb->get_var($wpdb->prepare(
                        "SELECT friendly_name_1_custom_position_a FROM {$wpdb->prefix}zen_lighthouse_friendly_names 
                         WHERE db_table_name = %s AND db_column_name = %s",
                        $table_name, $field
                    ));
                    
                    if ($position_result !== null && is_numeric($position_result)) {
                        $fields_with_positions[] = array('field' => $field, 'position' => intval($position_result));
                    } else {
                        $fields_without_positions[] = array('field' => $field, 'position' => 999);
                    }
                }
            }
            
            // Combine and sort by position
            $all_fields = array_merge($fields_with_positions, $fields_without_positions);
            usort($all_fields, function($a, $b) {
                return $a['position'] - $b['position'];
            });
            
            // Extract just the field names
            $fields = array_column($all_fields, 'field');
        }
        
        $output = '';
        
        foreach ($fields as $field) {
            // Check if this is a section header (doesn't start with 'driggs_' or 'sitespren_')
            $isSection = strpos($field, 'driggs_') !== 0 && 
                         strpos($field, 'sitespren_') !== 0 && 
                         strpos($field, 'section') !== false;
            
            if ($isSection) {
                $output .= "===================================\n";
                $output .= $field . "\n";
                $output .= "-----------------------------------\n";
                $output .= "\n"; // Empty line for sections
            } else {
                // Get friendly name from lighthouse table
                $table_name = str_replace('zen_', '', 'sitespren'); // Remove zen_ prefix if present
                $friendly_name = Grove_Database::get_friendly_name($table_name, $field);
                
                // Skip this field if omit_no_friendly is true and there's no friendly name
                if ($omit_no_friendly && empty($friendly_name)) {
                    continue;
                }
                
                // Format: Friendly name - Original field name (or just field name if no friendly name)
                $display_name = $friendly_name ? ($friendly_name . ' - ' . $field) : $field;
                
                $output .= "===================================\n";
                $output .= $display_name . "\n";
                $output .= "-----------------------------------\n";
                
                // Get the actual value from data
                $value = isset($data[$field]) ? $data[$field] : '';
                $output .= $value . "\n";
            }
        }
        
        return $output;
    }
    
    /**
     * Generate Nova Beluga format output with friendly names only
     * Format: Friendly name with separators, followed by value
     * 
     * @param array $data The sitespren data array
     * @param array $fields Optional array of specific fields to include
     * @param bool $omit_no_friendly Skip fields without lighthouse friendly names
     * @param bool $use_custom_position Order by custom_position_a field
     * @return string Formatted nova beluga output with friendly names only
     */
    public static function generate_nova_beluga_friendly($data, $fields = null, $omit_no_friendly = false, $use_custom_position = false) {
        if (!$data) {
            return '';
        }
        
        // If no fields specified, use all driggs_ and sitespren_ fields
        if (!$fields) {
            $fields = array();
            foreach ($data as $key => $value) {
                if (strpos($key, 'driggs_') === 0 || strpos($key, 'sitespren_') === 0) {
                    $fields[] = $key;
                }
            }
        }
        
        global $wpdb;
        
        // If using custom position ordering, reorder the fields based on lighthouse data
        if ($use_custom_position) {
            $fields_with_positions = array();
            $fields_without_positions = array();
            
            foreach ($fields as $field) {
                // Skip section headers for position lookup
                $isSection = strpos($field, 'driggs_') !== 0 && 
                             strpos($field, 'sitespren_') !== 0 && 
                             strpos($field, 'section') !== false;
                
                if ($isSection) {
                    $fields_without_positions[] = array('field' => $field, 'position' => 999);
                } else {
                    $table_name = str_replace('zen_', '', 'sitespren');
                    $position_result = $wpdb->get_var($wpdb->prepare(
                        "SELECT friendly_name_1_custom_position_a FROM {$wpdb->prefix}zen_lighthouse_friendly_names 
                         WHERE db_table_name = %s AND db_column_name = %s",
                        $table_name, $field
                    ));
                    
                    if ($position_result !== null && is_numeric($position_result)) {
                        $fields_with_positions[] = array('field' => $field, 'position' => intval($position_result));
                    } else {
                        $fields_without_positions[] = array('field' => $field, 'position' => 999);
                    }
                }
            }
            
            // Combine and sort by position
            $all_fields = array_merge($fields_with_positions, $fields_without_positions);
            usort($all_fields, function($a, $b) {
                return $a['position'] - $b['position'];
            });
            
            // Extract just the field names
            $fields = array_column($all_fields, 'field');
        }
        
        $output = '';
        
        foreach ($fields as $field) {
            // Check if this is a section header (doesn't start with 'driggs_' or 'sitespren_')
            $isSection = strpos($field, 'driggs_') !== 0 && 
                         strpos($field, 'sitespren_') !== 0 && 
                         strpos($field, 'section') !== false;
            
            if ($isSection) {
                $output .= "===================================\n";
                $output .= $field . "\n";
                $output .= "-----------------------------------\n";
                $output .= "\n"; // Empty line for sections
            } else {
                // Get friendly name from lighthouse table
                $table_name = str_replace('zen_', '', 'sitespren'); // Remove zen_ prefix if present
                $friendly_name = Grove_Database::get_friendly_name($table_name, $field);
                
                // Apply omit logic - either the original logic (always omit if no friendly) or the checkbox setting
                $should_omit = $omit_no_friendly ? empty($friendly_name) : !$friendly_name;
                
                // Only output if there is a friendly name, otherwise skip this field entirely
                if (!$should_omit && $friendly_name) {
                    $output .= "===================================\n";
                    $output .= $friendly_name . "\n";
                    $output .= "-----------------------------------\n";
                    
                    // Get the actual value from data
                    $value = isset($data[$field]) ? $data[$field] : '';
                    $output .= $value . "\n";
                }
            }
        }
        
        return $output;
    }
}