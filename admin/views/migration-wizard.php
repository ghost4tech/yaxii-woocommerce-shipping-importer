<?php
/**
 * Migration Wizard View
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap yaxii-wc-importer-wrap">
    
    <div class="yaxii-wc-importer-container">
        
        <!-- Header Info -->
        <div class="yaxii-wc-header">
            <div class="yaxii-wc-logo">
                <div class="yaxii-brand">
                    <span class="brand-name">YAXII</span>
                    <span class="brand-tagline"><?php _e('WooCommerce Shipping Importer', 'yaxii-wc-importer'); ?></span>
                </div>
            </div>
            <p class="description">
                <?php _e('Import your WooCommerce shipping zones and costs to Yaxii Smart Form Shipping Manager. Simple, fast, and safe.', 'yaxii-wc-importer'); ?>
            </p>
            
            <!-- Yaxii Smart Form Upsell -->
            <?php if (!defined('YAXII_VERSION')): ?>
            <div class="yaxii-upsell-banner">
                <div class="upsell-content">
                    <span class="dashicons dashicons-star-filled"></span>
                    <div>
                        <strong><?php _e('Need Yaxii Smart Form?', 'yaxii-wc-importer'); ?></strong>
                        <p><?php _e('Get the most powerful form plugin for WooCommerce', 'yaxii-wc-importer'); ?></p>
                    </div>
                </div>
                <a href="https://plugins.yaxii.dev" target="_blank" class="button button-primary">
                    <?php _e('Learn More', 'yaxii-wc-importer'); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Progress Steps -->
        <div class="yaxii-wc-steps">
            <div class="yaxii-wc-step active" data-step="1">
                <div class="step-indicator">
                    <div class="step-circle">
                        <span class="step-number">1</span>
                        <span class="step-check dashicons dashicons-yes"></span>
                    </div>
                    <span class="step-label"><?php _e('Scan', 'yaxii-wc-importer'); ?></span>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="yaxii-wc-step" data-step="2">
                <div class="step-indicator">
                    <div class="step-circle">
                        <span class="step-number">2</span>
                        <span class="step-check dashicons dashicons-yes"></span>
                    </div>
                    <span class="step-label"><?php _e('Map Methods', 'yaxii-wc-importer'); ?></span>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="yaxii-wc-step" data-step="3">
                <div class="step-indicator">
                    <div class="step-circle">
                        <span class="step-number">3</span>
                        <span class="step-check dashicons dashicons-yes"></span>
                    </div>
                    <span class="step-label"><?php _e('Import', 'yaxii-wc-importer'); ?></span>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="yaxii-wc-step" data-step="4">
                <div class="step-indicator">
                    <div class="step-circle">
                        <span class="step-number">4</span>
                        <span class="step-check dashicons dashicons-yes"></span>
                    </div>
                    <span class="step-label"><?php _e('Complete', 'yaxii-wc-importer'); ?></span>
                </div>
            </div>
        </div>

        <!-- Step 1: Scan & Preview -->
        <div class="yaxii-wc-content" id="step-1">
            <div class="yaxii-wc-card">
                <h3><?php _e('Step 1: Scan WooCommerce Data', 'yaxii-wc-importer'); ?></h3>
                <p><?php _e('Click the button below to scan your WooCommerce shipping zones and methods.', 'yaxii-wc-importer'); ?></p>
                
                <div class="yaxii-wc-actions">
                    <button type="button" class="button button-primary button-large" id="btn-scan">
                        <span class="dashicons dashicons-search"></span>
                        <?php _e('Scan WooCommerce Data', 'yaxii-wc-importer'); ?>
                    </button>
                </div>

                <div id="scan-results" style="display: none;">
                    <div class="yaxii-wc-stats">
                        <div class="stat-item">
                            <span class="stat-icon dashicons dashicons-location"></span>
                            <div class="stat-content">
                                <span class="stat-number" id="stat-zones">0</span>
                                <span class="stat-label"><?php _e('Zones Found', 'yaxii-wc-importer'); ?></span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon dashicons dashicons-admin-settings"></span>
                            <div class="stat-content">
                                <span class="stat-number" id="stat-methods">0</span>
                                <span class="stat-label"><?php _e('Shipping Methods', 'yaxii-wc-importer'); ?></span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon dashicons dashicons-admin-site"></span>
                            <div class="stat-content">
                                <span class="stat-number" id="stat-states">0</span>
                                <span class="stat-label"><?php _e('States Covered', 'yaxii-wc-importer'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="yaxii-wc-actions">
                        <button type="button" class="button button-primary button-large" id="btn-continue-step2">
                            <?php _e('Continue to Method Mapping', 'yaxii-wc-importer'); ?>
                            <span class="dashicons dashicons-arrow-right-alt"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Method Mapping -->
        <div class="yaxii-wc-content" id="step-2" style="display: none;">
            <div class="yaxii-wc-card">
                <h3><?php _e('Step 2: Map Shipping Methods', 'yaxii-wc-importer'); ?></h3>
                <p><?php _e('Map your WooCommerce shipping methods to Yaxii Smart Form methods.', 'yaxii-wc-importer'); ?></p>
                
                <!-- Sticky action bar for step 2 -->
                <div class="yaxii-wc-sticky-actions" id="sticky-actions-step2" style="display: none;">
                    <div class="sticky-actions-content">
                        <span class="sticky-actions-label"><?php _e('Method Mapping', 'yaxii-wc-importer'); ?></span>
                        <div class="sticky-actions-buttons">
                            <button type="button" class="button" id="btn-back-step1-sticky">
                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                <?php _e('Back', 'yaxii-wc-importer'); ?>
                            </button>
                            <button type="button" class="button button-primary button-large" id="btn-continue-step3-sticky">
                                <?php _e('Continue to Import', 'yaxii-wc-importer'); ?>
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="method-mapping-container">
                    <!-- Dynamically populated -->
                </div>

                <div class="yaxii-wc-actions">
                    <button type="button" class="button" id="btn-back-step1">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Back', 'yaxii-wc-importer'); ?>
                    </button>
                    <button type="button" class="button button-primary button-large" id="btn-continue-step3">
                        <?php _e('Continue to Import', 'yaxii-wc-importer'); ?>
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Conflict Resolution & Import -->
        <div class="yaxii-wc-content" id="step-3" style="display: none;">
            <div class="yaxii-wc-card">
                <h3><?php _e('Step 3: Import Settings', 'yaxii-wc-importer'); ?></h3>
                <p><?php _e('Choose how to handle existing Yaxii shipping costs.', 'yaxii-wc-importer'); ?></p>
                
                <div class="yaxii-wc-form">
                    <div class="form-group">
                        <label><?php _e('Conflict Resolution Strategy:', 'yaxii-wc-importer'); ?></label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="strategy" value="overwrite" checked>
                                <strong><?php _e('Overwrite', 'yaxii-wc-importer'); ?></strong>
                                <span class="description"><?php _e('Replace all existing prices with WooCommerce data', 'yaxii-wc-importer'); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="strategy" value="skip">
                                <strong><?php _e('Skip Existing', 'yaxii-wc-importer'); ?></strong>
                                <span class="description"><?php _e('Only import states that don\'t have prices yet', 'yaxii-wc-importer'); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="strategy" value="merge_lower">
                                <strong><?php _e('Keep Lower Prices', 'yaxii-wc-importer'); ?></strong>
                                <span class="description"><?php _e('Use the lower price between existing and WooCommerce', 'yaxii-wc-importer'); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="strategy" value="merge_higher">
                                <strong><?php _e('Keep Higher Prices', 'yaxii-wc-importer'); ?></strong>
                                <span class="description"><?php _e('Use the higher price between existing and WooCommerce', 'yaxii-wc-importer'); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="create-backup" checked>
                            <strong><?php _e('Create Backup Before Import', 'yaxii-wc-importer'); ?></strong>
                            <span class="description"><?php _e('Recommended: Create a backup that you can restore if needed', 'yaxii-wc-importer'); ?></span>
                        </label>
                    </div>
                </div>

                <div class="yaxii-wc-notice notice-info">
                    <span class="dashicons dashicons-info"></span>
                    <div>
                        <p><strong><?php _e('Important:', 'yaxii-wc-importer'); ?></strong></p>
                        <p><?php _e('This will modify your Yaxii Smart Form shipping costs. A backup will be created automatically.', 'yaxii-wc-importer'); ?></p>
                    </div>
                </div>

                <div class="yaxii-wc-actions">
                    <button type="button" class="button" id="btn-back-step2">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php _e('Back', 'yaxii-wc-importer'); ?>
                    </button>
                    <button type="button" class="button button-hero button-import-primary" id="btn-import">
                        <span class="dashicons dashicons-database-import"></span>
                        <?php _e('Start Import', 'yaxii-wc-importer'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: Success & Report -->
        <div class="yaxii-wc-content" id="step-4" style="display: none;">
            <div class="yaxii-wc-card success">
                <div class="success-icon">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <h3><?php _e('Import Completed Successfully!', 'yaxii-wc-importer'); ?></h3>
                
                <div id="import-report">
                    <!-- Dynamically populated -->
                </div>

                <div class="yaxii-wc-actions">
                    <a href="<?php echo admin_url('admin.php?page=yaxii-shipping-manager'); ?>" class="button button-primary button-large button-view-results">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Go to Yaxii Shipping Manager', 'yaxii-wc-importer'); ?>
                    </a>
                    <button type="button" class="button button-secondary" id="btn-start-over">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Import Again', 'yaxii-wc-importer'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="btn-export-csv">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export to CSV', 'yaxii-wc-importer'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div class="yaxii-wc-loading" id="loading-overlay" style="display: none;">
            <div class="spinner-container">
                <div class="spinner"></div>
                <p id="loading-message"><?php _e('Processing...', 'yaxii-wc-importer'); ?></p>
            </div>
        </div>

    </div>

    <!-- Backup Manager Section -->
    <div class="yaxii-wc-container" style="margin-top: 30px;">
        <div class="yaxii-wc-card">
            <h3><?php _e('Backup Manager', 'yaxii-wc-importer'); ?></h3>
            <p><?php _e('Manage your Yaxii shipping data backups.', 'yaxii-wc-importer'); ?></p>
            
            <div class="yaxii-wc-actions">
                <button type="button" class="button" id="btn-create-manual-backup">
                    <span class="dashicons dashicons-backup"></span>
                    <?php _e('Create Backup Now', 'yaxii-wc-importer'); ?>
                </button>
                <button type="button" class="button" id="btn-refresh-backups">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh List', 'yaxii-wc-importer'); ?>
                </button>
            </div>

            <div id="backups-list">
                <p class="description"><?php _e('Click refresh to load backups list.', 'yaxii-wc-importer'); ?></p>
            </div>
        </div>
    </div>
</div>

