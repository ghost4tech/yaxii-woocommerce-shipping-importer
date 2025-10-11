<?php
/**
 * Backup Manager
 * 
 * Manages backups of Yaxii shipping data
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Backup_Manager {
    
    /**
     * Backup option prefix
     */
    private $backup_prefix = 'yaxii_wc_importer_backup_';
    
    /**
     * Create backup of current Yaxii data
     * 
     * @return string|WP_Error Backup ID or error
     */
    public function create_backup() {
        $current_data = get_option('yaxii_shipping_costs', []);
        
        if (empty($current_data)) {
            return new WP_Error('no_data', __('No data to backup', 'yaxii-wc-importer'));
        }
        
        $backup_id = $this->generate_backup_id();
        $backup_data = [
            'timestamp' => current_time('timestamp'),
            'date' => current_time('mysql'),
            'data' => $current_data,
            'user_id' => get_current_user_id()
        ];
        
        $result = update_option($this->backup_prefix . $backup_id, $backup_data);
        
        if (!$result) {
            return new WP_Error('backup_failed', __('Failed to create backup', 'yaxii-wc-importer'));
        }
        
        // Clean old backups (keep only last 10)
        $this->clean_old_backups();
        
        return $backup_id;
    }
    
    /**
     * Restore from backup
     * 
     * @param string $backup_id
     * @return bool|WP_Error
     */
    public function restore_backup($backup_id) {
        $backup_data = get_option($this->backup_prefix . $backup_id);
        
        if (!$backup_data || !isset($backup_data['data'])) {
            return new WP_Error('backup_not_found', __('Backup not found', 'yaxii-wc-importer'));
        }
        
        $result = update_option('yaxii_shipping_costs', $backup_data['data']);
        
        if (!$result) {
            return new WP_Error('restore_failed', __('Failed to restore backup', 'yaxii-wc-importer'));
        }
        
        return true;
    }
    
    /**
     * Get all backups
     * 
     * @return array
     */
    public function get_backups() {
        global $wpdb;
        
        $backups = [];
        $prefix = $wpdb->esc_like($this->backup_prefix) . '%';
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s ORDER BY option_id DESC",
                $prefix
            ),
            ARRAY_A
        );
        
        foreach ($results as $row) {
            $backup_id = str_replace($this->backup_prefix, '', $row['option_name']);
            $backup_data = maybe_unserialize($row['option_value']);
            
            if (is_array($backup_data) && isset($backup_data['timestamp'])) {
                $backups[] = [
                    'id' => $backup_id,
                    'timestamp' => $backup_data['timestamp'],
                    'date' => $backup_data['date'],
                    'user_id' => isset($backup_data['user_id']) ? $backup_data['user_id'] : 0,
                    'size' => count($backup_data['data'])
                ];
            }
        }
        
        return $backups;
    }
    
    /**
     * Delete backup
     * 
     * @param string $backup_id
     * @return bool
     */
    public function delete_backup($backup_id) {
        return delete_option($this->backup_prefix . $backup_id);
    }
    
    /**
     * Generate unique backup ID
     * 
     * @return string
     */
    private function generate_backup_id() {
        return date('Ymd_His') . '_' . wp_generate_password(6, false);
    }
    
    /**
     * Clean old backups (keep only last 10)
     */
    private function clean_old_backups() {
        $backups = $this->get_backups();
        
        if (count($backups) > 10) {
            // Sort by timestamp descending
            usort($backups, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
            
            // Delete old backups
            $backups_to_delete = array_slice($backups, 10);
            
            foreach ($backups_to_delete as $backup) {
                $this->delete_backup($backup['id']);
            }
        }
    }
    
    /**
     * Export backup as JSON
     * 
     * @param string $backup_id
     * @return string|WP_Error
     */
    public function export_backup($backup_id) {
        $backup_data = get_option($this->backup_prefix . $backup_id);
        
        if (!$backup_data) {
            return new WP_Error('backup_not_found', __('Backup not found', 'yaxii-wc-importer'));
        }
        
        return json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

