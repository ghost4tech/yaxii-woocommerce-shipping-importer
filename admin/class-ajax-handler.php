<?php
/**
 * AJAX Handler
 * 
 * Handles AJAX requests from admin interface
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Importer_Ajax_Handler {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Scan WooCommerce data
        add_action('wp_ajax_yaxii_wc_scan', [$this, 'scan_woocommerce']);
        
        // Get preview data
        add_action('wp_ajax_yaxii_wc_preview', [$this, 'get_preview']);
        
        // Import data
        add_action('wp_ajax_yaxii_wc_import', [$this, 'import_data']);
        
        // Export to CSV
        add_action('wp_ajax_yaxii_wc_export_csv', [$this, 'export_csv']);
        
        // Backup management
        add_action('wp_ajax_yaxii_wc_create_backup', [$this, 'create_backup']);
        add_action('wp_ajax_yaxii_wc_restore_backup', [$this, 'restore_backup']);
        add_action('wp_ajax_yaxii_wc_get_backups', [$this, 'get_backups']);
    }
    
    /**
     * Verify nonce, permissions, and license
     */
    private function verify_request() {
        // Security check
        if (!check_ajax_referer('yaxii_wc_importer_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => __('Security check failed', 'yaxii-wc-importer')]);
            exit;
        }
        
        // Permissions check
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Insufficient permissions', 'yaxii-wc-importer')]);
            exit;
        }
        
        // Enhanced license validation
        if (!$this->verify_license()) {
            wp_send_json_error([
                'message' => __('Invalid or expired license. Please activate your Yaxii Smart Form license.', 'yaxii-wc-importer'),
                'license_required' => true,
                'upgrade_url' => 'https://plugins.yaxii.dev'
            ]);
            exit;
        }
    }
    
    /**
     * Verify Yaxii Smart Form license - Enhanced validation
     * 
     * @return bool
     */
    private function verify_license() {
        // Layer 1: Check if license function exists
        if (!function_exists('yaxii_can_use_premium_code')) {
            return false;
        }
        
        // Layer 2: Primary license check
        if (!yaxii_can_use_premium_code()) {
            return false;
        }
        
        // Layer 3: Verify Freemius instance
        if (function_exists('yaxii_fs')) {
            try {
                $fs = yaxii_fs();
                
                // Check if license is active
                if (!$fs->is_paying() && !$fs->is_trial()) {
                    return false;
                }
                
                // Verify it's not expired
                if ($fs->is_premium() && !$fs->is_plan('professional', true) && !$fs->is_plan('business', true)) {
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        
        // Layer 4: Timestamp-based check to prevent tampering
        $last_check = get_transient('yaxii_wc_importer_license_check');
        if ($last_check === false) {
            $validation_result = yaxii_can_use_premium_code();
            set_transient('yaxii_wc_importer_license_check', $validation_result ? 'valid' : 'invalid', 3600);
            return $validation_result;
        }
        
        return $last_check === 'valid';
    }
    
    /**
     * Scan WooCommerce data
     */
    public function scan_woocommerce() {
        $this->verify_request();
        
        try {
            $extractor = new Yaxii_WC_Data_Extractor();
            $stats = $extractor->get_statistics();
            $data = $extractor->get_shipping_data();
            
            $logger = new Yaxii_WC_Migration_Logger();
            $logger->log_info('WooCommerce data scanned', $stats);
            
            wp_send_json_success([
                'stats' => $stats,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            $logger = new Yaxii_WC_Migration_Logger();
            $logger->log_error('Scan failed: ' . $e->getMessage());
            
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get preview of import
     */
    public function get_preview() {
        $this->verify_request();
        
        try {
            $extractor = new Yaxii_WC_Data_Extractor();
            $wc_data = $extractor->get_shipping_data();
            
            $mapper = new Yaxii_WC_Data_Mapper();
            
            // Apply custom method mapping if provided
            if (isset($_POST['method_mapping'])) {
                $mapping = json_decode(stripslashes($_POST['method_mapping']), true);
                if (is_array($mapping)) {
                    $mapper->set_method_mapping($mapping);
                }
            }
            
            $yaxii_data = $mapper->convert_to_yaxii_format($wc_data);
            
            $importer = new Yaxii_WC_Data_Importer();
            $existing_data = $importer->get_existing_data();
            
            $preview = [];
            
            foreach ($yaxii_data as $state_code => $state_data) {
                $preview[] = [
                    'state_code' => $state_code,
                    'state_name' => $this->get_state_name($state_code),
                    'costs' => $state_data['costs'],
                    'existing' => isset($existing_data[$state_code]),
                    'existing_costs' => isset($existing_data[$state_code]) ? $existing_data[$state_code]['costs'] : []
                ];
            }
            
            wp_send_json_success([
                'preview' => $preview,
                'total' => count($preview)
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Import data
     */
    public function import_data() {
        $this->verify_request();
        
        try {
            $logger = new Yaxii_WC_Migration_Logger();
            $backup_manager = new Yaxii_WC_Backup_Manager();
            
            // Create backup first
            $backup_id = $backup_manager->create_backup();
            if (is_wp_error($backup_id)) {
                // Continue anyway, backup might have failed because there's no data yet
                $logger->log_info('Backup skipped (no existing data or failed)');
                $backup_id = null;
            } else {
                $logger->log_success('Backup created', ['backup_id' => $backup_id]);
            }
            
            // Extract WooCommerce data
            $extractor = new Yaxii_WC_Data_Extractor();
            $wc_data = $extractor->get_shipping_data();
            
            // Map data
            $mapper = new Yaxii_WC_Data_Mapper();
            
            // Apply custom method mapping from user
            // The mapping should include the unique key (detected_type_method_id_instance_id)
            if (isset($_POST['method_mapping'])) {
                $mapping = json_decode(stripslashes($_POST['method_mapping']), true);
                if (is_array($mapping)) {
                    $mapper->set_method_mapping($mapping);
                }
            }
            
            $yaxii_data = $mapper->convert_to_yaxii_format($wc_data);
            
            // Get merge strategy
            $strategy = isset($_POST['strategy']) ? sanitize_text_field($_POST['strategy']) : 'overwrite';
            
            // Merge with existing data
            $importer = new Yaxii_WC_Data_Importer();
            $existing_data = $importer->get_existing_data();
            $final_data = $mapper->merge_data($yaxii_data, $existing_data, $strategy);
            
            // Validate
            $validation = $importer->validate($final_data);
            if (is_wp_error($validation)) {
                throw new Exception($validation->get_error_message());
            }
            
            // Import with detailed error logging
            $result = $importer->import($final_data);
            if (is_wp_error($result)) {
                $error_msg = $result->get_error_message();
                $error_code = $result->get_error_code();
                
                $logger->log_error('Import failed', [
                    'error_code' => $error_code,
                    'error_message' => $error_msg,
                    'data_count' => count($final_data),
                    'strategy' => $strategy
                ]);
                
                throw new Exception($error_msg . ' (Code: ' . $error_code . ')');
            }
            
            // Double-check the import was successful
            $imported_data = get_option('yaxii_shipping_costs');
            if (empty($imported_data) && !empty($final_data)) {
                throw new Exception(__('Import appeared to succeed but data verification failed. Please try again.', 'yaxii-wc-importer'));
            }
            
            // Get statistics
            $stats = $importer->get_import_stats($yaxii_data, $existing_data);
            
            $logger->log_success('Data imported successfully', $stats);
            
            wp_send_json_success([
                'message' => __('Import completed successfully!', 'yaxii-wc-importer'),
                'stats' => $stats,
                'backup_id' => $backup_id
            ]);
            
        } catch (Exception $e) {
            $logger = new Yaxii_WC_Migration_Logger();
            $logger->log_error('Import failed: ' . $e->getMessage());
            
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export to CSV
     */
    public function export_csv() {
        $this->verify_request();
        
        try {
            $extractor = new Yaxii_WC_Data_Extractor();
            $wc_data = $extractor->get_shipping_data();
            
            $mapper = new Yaxii_WC_Data_Mapper();
            $yaxii_data = $mapper->convert_to_yaxii_format($wc_data);
            
            // Generate CSV
            $csv = $this->generate_csv($yaxii_data);
            
            wp_send_json_success([
                'csv' => $csv,
                'filename' => 'woocommerce_shipping_export_' . date('Y-m-d_H-i-s') . '.csv'
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create backup
     */
    public function create_backup() {
        $this->verify_request();
        
        try {
            $backup_manager = new Yaxii_WC_Backup_Manager();
            $backup_id = $backup_manager->create_backup();
            
            if (is_wp_error($backup_id)) {
                throw new Exception($backup_id->get_error_message());
            }
            
            $logger = new Yaxii_WC_Migration_Logger();
            $logger->log_success('Backup created', ['backup_id' => $backup_id]);
            
            wp_send_json_success([
                'message' => __('Backup created successfully', 'yaxii-wc-importer'),
                'backup_id' => $backup_id
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Restore backup
     */
    public function restore_backup() {
        $this->verify_request();
        
        $backup_id = isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : '';
        
        if (empty($backup_id)) {
            wp_send_json_error(['message' => __('Backup ID is required', 'yaxii-wc-importer')]);
            return;
        }
        
        try {
            $backup_manager = new Yaxii_WC_Backup_Manager();
            $result = $backup_manager->restore_backup($backup_id);
            
            if (is_wp_error($result)) {
                throw new Exception($result->get_error_message());
            }
            
            $logger = new Yaxii_WC_Migration_Logger();
            $logger->log_success('Backup restored', ['backup_id' => $backup_id]);
            
            wp_send_json_success([
                'message' => __('Backup restored successfully', 'yaxii-wc-importer')
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get backups list
     */
    public function get_backups() {
        $this->verify_request();
        
        try {
            $backup_manager = new Yaxii_WC_Backup_Manager();
            $backups = $backup_manager->get_backups();
            
            wp_send_json_success([
                'backups' => $backups
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate CSV from data
     */
    private function generate_csv($data) {
        $csv = "State Code,State Name,Method,Cost\n";
        
        foreach ($data as $state_code => $state_data) {
            $state_name = $this->get_state_name($state_code);
            
            foreach ($state_data['costs'] as $method_id => $cost) {
                $csv .= sprintf(
                    "%s,%s,%s,%s\n",
                    $state_code,
                    $state_name,
                    $method_id,
                    $cost
                );
            }
        }
        
        return $csv;
    }
    
    /**
     * Get state name
     */
    private function get_state_name($state_code) {
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
}

