<?php
/**
 * Plugin Name: Yaxii WooCommerce Shipping Importer
 * Plugin URI: https://yaxii.dev/wc-shipping-importer
 * Description: Migrate WooCommerce shipping zones and costs to Yaxii Smart Form Shipping Manager. Simple, fast, and free for Yaxii Smart Form license holders.
 * Version: 1.0.0
 * Author: Yaxii Dev
 * Author URI: https://yaxii.dev
 * Text Domain: yaxii-wc-importer
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('YAXII_WC_IMPORTER_VERSION', '1.0.0');
define('YAXII_WC_IMPORTER_FILE', __FILE__);
define('YAXII_WC_IMPORTER_PATH', plugin_dir_path(__FILE__));
define('YAXII_WC_IMPORTER_URL', plugin_dir_url(__FILE__));
define('YAXII_WC_IMPORTER_BASENAME', plugin_basename(__FILE__));

// Security: Verify this is a legitimate installation
if (!function_exists('add_action')) {
    exit('Direct access not allowed');
}

// Additional security layer
if (!defined('WPINC')) {
    die('WordPress not loaded');
}

/**
 * Main Plugin Class
 */
class Yaxii_WC_Shipping_Importer {
    
    /**
     * Single instance of the class
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
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-dependency-checker.php';
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-wc-data-extractor.php';
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-data-mapper.php';
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-yaxii-data-importer.php';
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-backup-manager.php';
        require_once YAXII_WC_IMPORTER_PATH . 'includes/class-migration-logger.php';
        
        if (is_admin()) {
            require_once YAXII_WC_IMPORTER_PATH . 'admin/class-admin.php';
            require_once YAXII_WC_IMPORTER_PATH . 'admin/class-ajax-handler.php';
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', [$this, 'check_dependencies'], 10);
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('init', [$this, 'init'], 20);
        
        // Declare WooCommerce HPOS compatibility
        add_action('before_woocommerce_init', [$this, 'declare_hpos_compatibility']);
    }
    
    /**
     * Declare WooCommerce HPOS (High-Performance Order Storage) compatibility
     */
    public function declare_hpos_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'custom_order_tables',
                YAXII_WC_IMPORTER_FILE,
                true
            );
        }
    }
    
    /**
     * Check dependencies with enhanced security
     */
    public function check_dependencies() {
        $checker = new Yaxii_WC_Importer_Dependency_Checker();
        
        if (!$checker->check()) {
            add_action('admin_notices', function() use ($checker) {
                $errors = $checker->get_errors();
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong><?php _e('Yaxii WooCommerce Shipping Importer:', 'yaxii-wc-importer'); ?></strong></p>
                    <ul style="list-style: disc; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo wp_kses_post($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>
                        <a href="https://plugins.yaxii.dev" target="_blank" class="button button-primary">
                            <?php _e('Get Yaxii Smart Form', 'yaxii-wc-importer'); ?>
                        </a>
                    </p>
                </div>
                <?php
            });
            
            // Deactivate the plugin if dependencies not met
            add_action('admin_init', function() {
                deactivate_plugins(YAXII_WC_IMPORTER_BASENAME);
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            });
            
            return;
        }
        
        // Additional runtime validation every hour
        add_action('admin_init', [$this, 'periodic_license_check']);
    }
    
    /**
     * Periodic license validation
     */
    public function periodic_license_check() {
        $last_check = get_transient('yaxii_wc_importer_periodic_check');
        
        if ($last_check === false) {
            // Re-validate license
            if (!function_exists('yaxii_can_use_premium_code') || !yaxii_can_use_premium_code()) {
                // Disable plugin functionality
                remove_action('admin_menu', [Yaxii_WC_Importer_Admin::instance(), 'add_admin_menu']);
                
                add_action('admin_notices', function() {
                    ?>
                    <div class="notice notice-error">
                        <p>
                            <strong><?php _e('Yaxii WC Importer:', 'yaxii-wc-importer'); ?></strong>
                            <?php _e('Your Yaxii Smart Form license could not be verified. Please reactivate your license.', 'yaxii-wc-importer'); ?>
                            <a href="https://plugins.yaxii.dev" target="_blank"><?php _e('Learn More', 'yaxii-wc-importer'); ?></a>
                        </p>
                    </div>
                    <?php
                });
            }
            
            set_transient('yaxii_wc_importer_periodic_check', time(), 3600); // Check every hour
        }
    }
    
    /**
     * Load translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'yaxii-wc-importer',
            false,
            dirname(YAXII_WC_IMPORTER_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Only initialize if dependencies are met
        $checker = new Yaxii_WC_Importer_Dependency_Checker();
        if (!$checker->check()) {
            return;
        }
        
        // Initialize admin
        if (is_admin()) {
            Yaxii_WC_Importer_Admin::instance();
            Yaxii_WC_Importer_Ajax_Handler::instance();
        }
        
        do_action('yaxii_wc_importer_init');
    }
}

/**
 * Initialize the plugin
 */
function yaxii_wc_importer() {
    return Yaxii_WC_Shipping_Importer::instance();
}

// Let's go!
yaxii_wc_importer();

