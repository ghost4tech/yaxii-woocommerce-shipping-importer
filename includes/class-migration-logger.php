<?php
/**
 * Migration Logger
 * 
 * Logs migration activities
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Migration_Logger {
    
    /**
     * Log option name
     */
    private $log_option = 'yaxii_wc_importer_logs';
    
    /**
     * Max log entries
     */
    private $max_logs = 50;
    
    /**
     * Log an activity
     * 
     * @param string $action
     * @param string $message
     * @param array $data
     */
    public function log($action, $message, $data = []) {
        $logs = get_option($this->log_option, []);
        
        $log_entry = [
            'timestamp' => current_time('timestamp'),
            'date' => current_time('mysql'),
            'action' => $action,
            'message' => $message,
            'data' => $data,
            'user_id' => get_current_user_id()
        ];
        
        array_unshift($logs, $log_entry);
        
        // Keep only last N logs
        $logs = array_slice($logs, 0, $this->max_logs);
        
        update_option($this->log_option, $logs);
    }
    
    /**
     * Get all logs
     * 
     * @param int $limit
     * @return array
     */
    public function get_logs($limit = 20) {
        $logs = get_option($this->log_option, []);
        return array_slice($logs, 0, $limit);
    }
    
    /**
     * Clear all logs
     * 
     * @return bool
     */
    public function clear_logs() {
        return update_option($this->log_option, []);
    }
    
    /**
     * Log success
     * 
     * @param string $message
     * @param array $data
     */
    public function log_success($message, $data = []) {
        $this->log('success', $message, $data);
    }
    
    /**
     * Log error
     * 
     * @param string $message
     * @param array $data
     */
    public function log_error($message, $data = []) {
        $this->log('error', $message, $data);
    }
    
    /**
     * Log info
     * 
     * @param string $message
     * @param array $data
     */
    public function log_info($message, $data = []) {
        $this->log('info', $message, $data);
    }
    
    /**
     * Get last log
     * 
     * @return array|null
     */
    public function get_last_log() {
        $logs = $this->get_logs(1);
        return !empty($logs) ? $logs[0] : null;
    }
}

