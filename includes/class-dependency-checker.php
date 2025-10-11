<?php
/**
 * Dependency Checker
 * 
 * Checks if all required plugins and versions are available
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Importer_Dependency_Checker {
    
    private $errors = [];
    
    /**
     * Check all dependencies
     * 
     * @return bool True if all dependencies are met
     */
    public function check() {
        $this->errors = [];
        
        // Check WordPress version
        $this->check_wordpress_version();
        
        // Check PHP version
        $this->check_php_version();
        
        // Check WooCommerce
        $this->check_woocommerce();
        
        // Check Yaxii Smart Form
        $this->check_yaxii_smart_form();
        
        // Check Yaxii license
        $this->check_yaxii_license();
        
        return empty($this->errors);
    }
    
    /**
     * Check WordPress version
     */
    private function check_wordpress_version() {
        global $wp_version;
        
        if (version_compare($wp_version, '5.8', '<')) {
            $this->errors[] = sprintf(
                __('WordPress 5.8 or higher is required. You are running version %s.', 'yaxii-wc-importer'),
                $wp_version
            );
        }
    }
    
    /**
     * Check PHP version
     */
    private function check_php_version() {
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->errors[] = sprintf(
                __('PHP 7.4 or higher is required. You are running version %s.', 'yaxii-wc-importer'),
                PHP_VERSION
            );
        }
    }
    
    /**
     * Check WooCommerce
     */
    private function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            $this->errors[] = __('WooCommerce plugin is required. Please install and activate WooCommerce.', 'yaxii-wc-importer');
            return;
        }
        
        if (defined('WC_VERSION') && version_compare(WC_VERSION, '5.0', '<')) {
            $this->errors[] = sprintf(
                __('WooCommerce 5.0 or higher is required. You are running version %s.', 'yaxii-wc-importer'),
                WC_VERSION
            );
        }
    }
    
    /**
     * Check Yaxii Smart Form
     */
    private function check_yaxii_smart_form() {
        if (!defined('YAXII_VERSION')) {
            $this->errors[] = __('Yaxii Smart Form plugin is required. Please install and activate Yaxii Smart Form.', 'yaxii-wc-importer');
            return;
        }
        
        // Check if shipping manager exists
        if (!class_exists('YaxiiSmartForm\Admin\ShippingManager')) {
            $this->errors[] = __('Yaxii Smart Form Shipping Manager is not available. Please update Yaxii Smart Form to the latest version.', 'yaxii-wc-importer');
        }
    }
    
    /**
     * Check Yaxii license with enhanced validation
     */
    private function check_yaxii_license() {
        // Multiple layers of validation for security
        
        // Layer 1: Check if Freemius function exists
        if (!function_exists('yaxii_can_use_premium_code')) {
            $this->errors[] = __('Yaxii Smart Form license validation function not found.', 'yaxii-wc-importer');
            return;
        }
        
        // Layer 2: Check if user has valid license
        if (!yaxii_can_use_premium_code()) {
            $this->errors[] = __('A valid Yaxii Smart Form license is required to use this importer. Please activate your license at:', 'yaxii-wc-importer') . ' <a href="https://plugins.yaxii.dev" target="_blank">plugins.yaxii.dev</a>';
            return;
        }
        
        // Layer 3: Verify Yaxii is properly installed
        if (!class_exists('YaxiiSmartForm\Core\Plugin')) {
            $this->errors[] = __('Yaxii Smart Form core plugin not properly installed.', 'yaxii-wc-importer');
            return;
        }
        
        // Layer 4: Check if shipping manager is available
        if (!class_exists('YaxiiSmartForm\Admin\ShippingManager')) {
            $this->errors[] = __('Yaxii Smart Form Shipping Manager is not available. Please update to the latest version.', 'yaxii-wc-importer');
            return;
        }
        
        // Layer 5: Additional license validation through option check
        $yaxii_license_data = get_option('yaxii_smart_form_license_data');
        if (empty($yaxii_license_data) || !is_array($yaxii_license_data)) {
            // Double check with freemius
            if (!$this->verify_freemius_license()) {
                $this->errors[] = __('Yaxii Smart Form license could not be verified. Please reactivate your license.', 'yaxii-wc-importer');
                return;
            }
        }
    }
    
    /**
     * Verify Freemius license status
     * 
     * @return bool
     */
    private function verify_freemius_license() {
        // Check if Freemius SDK is loaded
        if (!function_exists('yaxii_fs')) {
            return false;
        }
        
        try {
            $fs = yaxii_fs();
            
            // Check if user is paying or in trial
            if (!$fs->is_paying() && !$fs->is_trial()) {
                return false;
            }
            
            // Check if license is active
            if (!$fs->is_plan('professional', true) && !$fs->is_plan('business', true)) {
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get errors
     * 
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }
    
    /**
     * Check if specific dependency is met
     * 
     * @param string $dependency
     * @return bool
     */
    public function has_dependency($dependency) {
        switch ($dependency) {
            case 'woocommerce':
                return class_exists('WooCommerce');
                
            case 'yaxii':
                return defined('YAXII_VERSION');
                
            case 'yaxii_license':
                return function_exists('yaxii_can_use_premium_code') && yaxii_can_use_premium_code();
                
            default:
                return false;
        }
    }
}

