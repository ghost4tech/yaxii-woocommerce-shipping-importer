<?php
/**
 * WooCommerce Data Extractor
 * 
 * Extracts shipping zones and costs from WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Data_Extractor {
    
    /**
     * Get all shipping zones from WooCommerce
     * 
     * @return array
     */
    public function get_zones() {
        if (!class_exists('WC_Shipping_Zones')) {
            return [];
        }
        
        return WC_Shipping_Zones::get_zones();
    }
    
    /**
     * Get shipping data structured for migration
     * 
     * @return array
     */
    public function get_shipping_data() {
        $zones = $this->get_zones();
        $data = [
            'zones' => [],
            'methods' => [],
            'states' => []
        ];
        
        foreach ($zones as $zone) {
            $zone_data = $this->process_zone($zone);
            
            if (!empty($zone_data)) {
                $data['zones'][] = $zone_data;
                
                // Collect unique methods with smart grouping
                foreach ($zone_data['methods'] as $method) {
                    // Use detected type + method_id as key for better grouping
                    $detected_type = $method['detected_type'];
                    $method_key = $detected_type . '_' . $method['method_id'] . '_' . $method['instance_id'];
                    
                    if (!isset($data['methods'][$method_key])) {
                        $data['methods'][$method_key] = [
                            'id' => $method['method_id'],
                            'instance_id' => $method['instance_id'],
                            'title' => $method['method_title'],
                            'detected_type' => $detected_type,
                            'count' => 0
                        ];
                    }
                    $data['methods'][$method_key]['count']++;
                }
                
                // Collect states
                foreach ($zone_data['states'] as $state) {
                    $state_code = $this->extract_state_code($state['code']);
                    if ($state_code && !isset($data['states'][$state_code])) {
                        $data['states'][$state_code] = [
                            'code' => $state_code,
                            'name' => $this->get_state_name($state_code),
                            'zones' => []
                        ];
                    }
                    
                    if ($state_code) {
                        $data['states'][$state_code]['zones'][] = [
                            'zone_id' => $zone_data['id'],
                            'zone_name' => $zone_data['name'],
                            'methods' => $zone_data['methods']
                        ];
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Process a single zone
     * 
     * @param array $zone
     * @return array
     */
    private function process_zone($zone) {
        $zone_id = $zone['id'];
        $zone_obj = new WC_Shipping_Zone($zone_id);
        
        $zone_data = [
            'id' => $zone_id,
            'name' => $zone['zone_name'],
            'order' => $zone['zone_order'],
            'states' => [],
            'methods' => []
        ];
        
        // Get zone locations (states)
        foreach ($zone['zone_locations'] as $location) {
            if ($location->type === 'state') {
                $zone_data['states'][] = [
                    'code' => $location->code,
                    'type' => $location->type
                ];
            }
        }
        
        // Get shipping methods for this zone
        $shipping_methods = $zone_obj->get_shipping_methods();
        
        foreach ($shipping_methods as $method) {
            if (!$method->enabled) {
                continue;
            }
            
            $method_title = $method->get_title();
            $detected_type = $this->detect_method_type($method_title);
            
            $zone_data['methods'][] = [
                'instance_id' => $method->instance_id,
                'method_id' => $method->id,
                'method_title' => $method_title,
                'cost' => $this->extract_cost($method),
                'enabled' => $method->enabled === 'yes',
                'detected_type' => $detected_type // Add detected type
            ];
        }
        
        return $zone_data;
    }
    
    /**
     * Detect method type from title (home or office delivery)
     * 
     * @param string $title
     * @return string 'home_delivery', 'office_delivery', or 'unknown'
     */
    private function detect_method_type($title) {
        // Convert to lowercase for comparison
        $title_lower = mb_strtolower($title);
        
        // Arabic keywords for home delivery
        $home_keywords = ['منزل', 'بيت', 'دار', 'home', 'domicile', 'house'];
        
        // Arabic keywords for office delivery
        $office_keywords = ['مكتب', 'وكالة', 'مركز', 'office', 'bureau', 'agency', 'center', 'centre', 'pickup', 'stopdesk'];
        
        // Check for office delivery first (more specific)
        foreach ($office_keywords as $keyword) {
            if (strpos($title_lower, $keyword) !== false) {
                return 'office_delivery';
            }
        }
        
        // Then check for home delivery
        foreach ($home_keywords as $keyword) {
            if (strpos($title_lower, $keyword) !== false) {
                return 'home_delivery';
            }
        }
        
        // Default to unknown
        return 'unknown';
    }
    
    /**
     * Extract cost from shipping method
     * 
     * @param object $method
     * @return float
     */
    private function extract_cost($method) {
        // Try to get cost property
        if (isset($method->cost)) {
            return floatval($method->cost);
        }
        
        // Try to get from settings
        if (isset($method->instance_settings['cost'])) {
            return floatval($method->instance_settings['cost']);
        }
        
        // Free shipping
        if ($method->id === 'free_shipping') {
            return 0;
        }
        
        return 0;
    }
    
    /**
     * Extract state code from WooCommerce format
     * 
     * WooCommerce format: DZ:16 (country:state)
     * Yaxii format: 16 (just state code)
     * 
     * @param string $wc_code
     * @return string|null
     */
    private function extract_state_code($wc_code) {
        $parts = explode(':', $wc_code);
        
        if (count($parts) === 2) {
            return $parts[1]; // Return state code
        }
        
        return null;
    }
    
    /**
     * Get state name from code
     * 
     * @param string $state_code
     * @return string
     */
    private function get_state_name($state_code) {
        // Try to get from Yaxii location handler
        if (class_exists('YaxiiSmartForm\Frontend\LocationHandler')) {
            $location_handler = new YaxiiSmartForm\Frontend\LocationHandler();
            $country = $location_handler->get_default_country();
            $states = $location_handler->load_states($country);
            
            if (isset($states[$state_code])) {
                return $states[$state_code];
            }
        }
        
        return $state_code;
    }
    
    /**
     * Get statistics
     * 
     * @return array
     */
    public function get_statistics() {
        $data = $this->get_shipping_data();
        
        return [
            'total_zones' => count($data['zones']),
            'total_methods' => array_sum(array_column($data['methods'], 'count')),
            'unique_methods' => count($data['methods']),
            'total_states' => count($data['states']),
            'methods_list' => array_values($data['methods'])
        ];
    }
}

