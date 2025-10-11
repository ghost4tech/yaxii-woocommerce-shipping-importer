<?php
/**
 * Admin Class
 * 
 * Handles admin interface and menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class Yaxii_WC_Importer_Admin {
    
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
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Yaxii WC Importer', 'yaxii-wc-importer'),
            __('Yaxii WC Importer', 'yaxii-wc-importer'),
            'manage_options',
            'yaxii-wc-importer',
            [$this, 'render_admin_page'],
            'dashicons-upload', // Icon
            59 // Position (after Settings, before separator)
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        // Only load on our admin page
        if ($hook !== 'toplevel_page_yaxii-wc-importer') {
            return;
        }
        
        // CSS
        wp_enqueue_style(
            'yaxii-wc-importer-admin',
            YAXII_WC_IMPORTER_URL . 'admin/assets/css/admin.css',
            [],
            YAXII_WC_IMPORTER_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'yaxii-wc-importer-admin',
            YAXII_WC_IMPORTER_URL . 'admin/assets/js/admin.js',
            ['jquery'],
            YAXII_WC_IMPORTER_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('yaxii-wc-importer-admin', 'yaxiiWcImporter', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('yaxii_wc_importer_nonce'),
            'strings' => [
                'scanning' => __('Scanning WooCommerce data...', 'yaxii-wc-importer'),
                'importing' => __('Importing data...', 'yaxii-wc-importer'),
                'success' => __('Success!', 'yaxii-wc-importer'),
                'error' => __('Error', 'yaxii-wc-importer'),
                'confirm_import' => __('Are you sure you want to import? This will modify your Yaxii shipping costs.', 'yaxii-wc-importer'),
                'confirm_restore' => __('Are you sure you want to restore from backup?', 'yaxii-wc-importer'),
            ]
        ]);
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include YAXII_WC_IMPORTER_PATH . 'admin/views/migration-wizard.php';
    }
}

