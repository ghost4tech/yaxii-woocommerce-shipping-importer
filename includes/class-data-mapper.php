<?php
/**
 * Data Mapper
 * 
 * Maps WooCommerce shipping data to Yaxii Smart Form format
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Data_Mapper {
    
    /**
     * Default method mapping
     */
    private $default_method_mapping = [
        'flat_rate' => 'home_delivery',
        'free_shipping' => 'home_delivery',
        'local_pickup' => 'office_delivery',
    ];
    
    /**
     * Custom method mapping (can be set by user)
     */
    private $custom_method_mapping = [];
    
    /**
     * Set custom method mapping
     * 
     * @param array $mapping
     */
    public function set_method_mapping($mapping) {
        $this->custom_method_mapping = $mapping;
    }
    
    /**
     * Get method mapping
     * 
     * @return array
     */
    public function get_method_mapping() {
        return array_merge($this->default_method_mapping, $this->custom_method_mapping);
    }
    
    /**
     * Map WooCommerce method to Yaxii method
     * 
     * @param string $wc_method_id Method ID
     * @param string $detected_type Detected type from title analysis
     * @param int $instance_id Instance ID for specific mapping
     * @return string
     */
    public function map_method($wc_method_id, $detected_type = 'unknown', $instance_id = null) {
        // Build unique key for this specific method instance
        $unique_key = $detected_type . '_' . $wc_method_id . '_' . $instance_id;
        
        // Check if there's a custom mapping for this specific instance
        if (isset($this->custom_method_mapping[$unique_key])) {
            return $this->custom_method_mapping[$unique_key];
        }
        
        // Use detected type if available
        if ($detected_type === 'home_delivery' || $detected_type === 'office_delivery') {
            return $detected_type;
        }
        
        // Fallback to standard mapping
        $mapping = $this->get_method_mapping();
        
        if (isset($mapping[$wc_method_id])) {
            return $mapping[$wc_method_id];
        }
        
        // Default to home_delivery for unknown methods
        return 'home_delivery';
    }
    
    /**
     * Convert WooCommerce data to Yaxii format
     * 
     * @param array $wc_data WooCommerce shipping data
     * @return array Yaxii format data
     */
    public function convert_to_yaxii_format($wc_data) {
        $yaxii_data = [];
        
        if (!isset($wc_data['states']) || !is_array($wc_data['states'])) {
            error_log('Yaxii WC Importer: No states data found in WC data');
            return $yaxii_data;
        }
        
        foreach ($wc_data['states'] as $state_code => $state_data) {
            $costs = $this->calculate_state_costs($state_data);
            
            // Only add state if it has costs
            if (!empty($costs)) {
                $yaxii_data[$state_code] = [
                    'enabled' => true,
                    'costs' => $costs,
                    'cities' => []
                ];
            }
        }
        
        // Log conversion results
        error_log('Yaxii WC Importer: Converted ' . count($yaxii_data) . ' states');
        
        return $yaxii_data;
    }
    
    /**
     * Calculate costs for a state
     * 
     * @param array $state_data
     * @return array
     */
    private function calculate_state_costs($state_data) {
        $costs = [];
        
        // Get all zones for this state
        foreach ($state_data['zones'] as $zone) {
            foreach ($zone['methods'] as $method) {
                $detected_type = isset($method['detected_type']) ? $method['detected_type'] : 'unknown';
                $instance_id = isset($method['instance_id']) ? $method['instance_id'] : null;
                
                $yaxii_method = $this->map_method(
                    $method['method_id'], 
                    $detected_type,
                    $instance_id
                );
                
                $cost = floatval($method['cost']);
                
                // If multiple zones have different costs, use the lowest
                // But make sure we're tracking both home_delivery and office_delivery separately
                if (!isset($costs[$yaxii_method]) || $cost < $costs[$yaxii_method]) {
                    $costs[$yaxii_method] = $cost;
                }
            }
        }
        
        return $costs;
    }
    
    /**
     * Merge with existing Yaxii data
     * 
     * @param array $new_data New data to import
     * @param array $existing_data Existing Yaxii data
     * @param string $strategy Merge strategy: 'overwrite', 'skip', 'merge_higher', 'merge_lower'
     * @return array
     */
    public function merge_data($new_data, $existing_data, $strategy = 'overwrite') {
        $merged = $existing_data;
        
        foreach ($new_data as $state_code => $state_data) {
            switch ($strategy) {
                case 'overwrite':
                    // Replace entirely with new data
                    $merged[$state_code] = $state_data;
                    break;
                    
                case 'skip':
                    // Only add if state doesn't exist
                    if (!isset($merged[$state_code])) {
                        $merged[$state_code] = $state_data;
                    }
                    break;
                    
                case 'merge_higher':
                    // Keep higher prices
                    $merged[$state_code] = $this->merge_state_costs(
                        $state_data,
                        isset($merged[$state_code]) ? $merged[$state_code] : [],
                        'higher'
                    );
                    break;
                    
                case 'merge_lower':
                    // Keep lower prices
                    $merged[$state_code] = $this->merge_state_costs(
                        $state_data,
                        isset($merged[$state_code]) ? $merged[$state_code] : [],
                        'lower'
                    );
                    break;
            }
        }
        
        return $merged;
    }
    
    /**
     * Merge state costs
     * 
     * @param array $new_state
     * @param array $existing_state
     * @param string $preference 'higher' or 'lower'
     * @return array
     */
    private function merge_state_costs($new_state, $existing_state, $preference = 'higher') {
        $merged = $existing_state;
        
        // Merge costs
        $merged['costs'] = isset($merged['costs']) ? $merged['costs'] : [];
        
        foreach ($new_state['costs'] as $method_id => $cost) {
            if (!isset($merged['costs'][$method_id])) {
                $merged['costs'][$method_id] = $cost;
            } else {
                if ($preference === 'higher') {
                    $merged['costs'][$method_id] = max($merged['costs'][$method_id], $cost);
                } else {
                    $merged['costs'][$method_id] = min($merged['costs'][$method_id], $cost);
                }
            }
        }
        
        // Keep enabled status from new data
        $merged['enabled'] = $new_state['enabled'];
        
        // Preserve cities if they exist
        if (!isset($merged['cities'])) {
            $merged['cities'] = [];
        }
        
        return $merged;
    }
    
    /**
     * Get available Yaxii methods
     * 
     * @return array
     */
    public function get_yaxii_methods() {
        return [
            'home_delivery' => __('Home Delivery - التوصيل للمنزل', 'yaxii-wc-importer'),
            'office_delivery' => __('Office Delivery - التوصيل للمكتب', 'yaxii-wc-importer'),
        ];
    }
}

