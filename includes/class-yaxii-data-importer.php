<?php
/**
 * Yaxii Data Importer
 * 
 * Imports data into Yaxii Smart Form shipping costs option
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Data_Importer {
    
    /**
     * Yaxii shipping costs option name
     */
    private $costs_option = 'yaxii_shipping_costs';
    
    /**
     * Import data into Yaxii
     * 
     * @param array $data Data to import
     * @return bool|WP_Error
     */
    public function import($data) {
        // Validate data
        if (!is_array($data)) {
            return new WP_Error('invalid_data', __('Invalid data format', 'yaxii-wc-importer'));
        }
        
        // Check if data is empty
        if (empty($data)) {
            return new WP_Error('empty_data', __('No data to import', 'yaxii-wc-importer'));
        }
        
        // Get existing data to compare
        $existing_data = $this->get_existing_data();
        
        // Update option - force update even if data is the same
        $result = update_option($this->costs_option, $data, true);
        
        // Check if update was successful
        // Note: update_option returns false if data is same, so we need to verify differently
        $updated_data = get_option($this->costs_option);
        
        if ($updated_data === $data || json_encode($updated_data) === json_encode($data)) {
            // Data matches, import was successful
            return true;
        }
        
        if (!$result) {
            // Log for debugging
            error_log('Yaxii WC Importer: Failed to update option. Data count: ' . count($data));
            error_log('Yaxii WC Importer: Sample data: ' . print_r(array_slice($data, 0, 2, true), true));
            
            return new WP_Error('import_failed', __('Failed to import data. Please check WordPress debug log for details.', 'yaxii-wc-importer'));
        }
        
        return true;
    }
    
    /**
     * Get existing Yaxii data
     * 
     * @return array
     */
    public function get_existing_data() {
        return get_option($this->costs_option, []);
    }
    
    /**
     * Check if state exists in Yaxii data
     * 
     * @param string $state_code
     * @return bool
     */
    public function state_exists($state_code) {
        $data = $this->get_existing_data();
        return isset($data[$state_code]);
    }
    
    /**
     * Get state data
     * 
     * @param string $state_code
     * @return array|null
     */
    public function get_state_data($state_code) {
        $data = $this->get_existing_data();
        return isset($data[$state_code]) ? $data[$state_code] : null;
    }
    
    /**
     * Get import statistics
     * 
     * @param array $imported_data
     * @param array $existing_data
     * @return array
     */
    public function get_import_stats($imported_data, $existing_data) {
        $stats = [
            'total_states' => count($imported_data),
            'new_states' => 0,
            'updated_states' => 0,
            'total_costs' => 0,
            'methods' => []
        ];
        
        foreach ($imported_data as $state_code => $state_data) {
            if (isset($existing_data[$state_code])) {
                $stats['updated_states']++;
            } else {
                $stats['new_states']++;
            }
            
            if (isset($state_data['costs'])) {
                $stats['total_costs'] += count($state_data['costs']);
                
                foreach ($state_data['costs'] as $method_id => $cost) {
                    if (!isset($stats['methods'][$method_id])) {
                        $stats['methods'][$method_id] = 0;
                    }
                    $stats['methods'][$method_id]++;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Validate data before import
     * 
     * @param array $data
     * @return bool|WP_Error
     */
    public function validate($data) {
        if (!is_array($data)) {
            return new WP_Error('invalid_format', __('Data must be an array', 'yaxii-wc-importer'));
        }
        
        if (empty($data)) {
            return new WP_Error('empty_data', __('Data array is empty', 'yaxii-wc-importer'));
        }
        
        foreach ($data as $state_code => $state_data) {
            // Check state structure
            if (!is_array($state_data)) {
                return new WP_Error(
                    'invalid_state',
                    sprintf(__('Invalid state data for: %s', 'yaxii-wc-importer'), $state_code)
                );
            }
            
            // Check required fields - 'enabled' and 'costs' are required
            if (!array_key_exists('enabled', $state_data)) {
                return new WP_Error(
                    'missing_enabled',
                    sprintf(__('Missing "enabled" field for state: %s', 'yaxii-wc-importer'), $state_code)
                );
            }
            
            if (!array_key_exists('costs', $state_data)) {
                return new WP_Error(
                    'missing_costs',
                    sprintf(__('Missing "costs" field for state: %s', 'yaxii-wc-importer'), $state_code)
                );
            }
            
            // Validate costs array
            if (!is_array($state_data['costs'])) {
                return new WP_Error(
                    'invalid_costs',
                    sprintf(__('Invalid costs format for state: %s', 'yaxii-wc-importer'), $state_code)
                );
            }
            
            // Ensure cities key exists (can be empty array)
            if (!isset($state_data['cities'])) {
                $data[$state_code]['cities'] = [];
            }
        }
        
        return true;
    }
}

