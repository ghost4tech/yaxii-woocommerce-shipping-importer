=== Yaxii WooCommerce Shipping Importer ===
Contributors: yaxiiteam
Tags: woocommerce, shipping, import, yaxii, migration
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Migrate WooCommerce shipping zones and costs to Yaxii Smart Form Shipping Manager. Free for Yaxii Smart Form license holders.

== Description ==

**Yaxii WooCommerce Shipping Importer** is a free addon plugin that enables one-click migration of your WooCommerce shipping zones and costs to the Yaxii Smart Form Shipping Manager.

### 🌟 Key Features

* **Simple 4-Step Wizard** - Easy-to-follow migration process
* **Automatic Mapping** - Intelligently maps WooCommerce methods to Yaxii methods
* **Safe Migration** - Automatic backups before every import
* **Flexible Options** - Multiple strategies for handling existing data
* **Detailed Reports** - See exactly what was imported
* **CSV Export** - Export your shipping data to CSV
* **License Protected** - Free for valid Yaxii Smart Form license holders

### 📋 Requirements

* WooCommerce 5.0 or higher
* Yaxii Smart Form (latest version)
* Valid Yaxii Smart Form License

### 🚀 How It Works

1. **Scan** - Automatically scans your WooCommerce shipping zones
2. **Map** - Maps WooCommerce shipping methods to Yaxii methods
3. **Import** - Migrates data with your chosen strategy
4. **Complete** - View detailed import report and manage backups

### 💡 Use Cases

* Migrating from WooCommerce to Yaxii Smart Form
* Syncing shipping costs between systems
* Bulk importing shipping data
* Creating backups of shipping configuration

== Installation ==

### Automatic Installation

1. Go to Plugins → Add New
2. Search for "Yaxii WooCommerce Shipping Importer"
3. Click "Install Now" and then "Activate"
4. Go to WC Importer in the admin sidebar

### Manual Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through the 'Plugins' menu in WordPress
3. Go to WC Importer in the admin sidebar

== Frequently Asked Questions ==

= Do I need a Yaxii Smart Form license? =

Yes, a valid Yaxii Smart Form license is required to use this importer. The plugin is free for license holders.

= Will it modify my WooCommerce data? =

No, this plugin only reads from WooCommerce. It does not modify any WooCommerce settings or data.

= What happens to existing Yaxii shipping costs? =

You can choose from multiple strategies:
* Overwrite - Replace all existing prices
* Skip Existing - Only import new states
* Keep Lower Prices - Use the cheaper option
* Keep Higher Prices - Use the more expensive option

= Can I restore if something goes wrong? =

Yes! The plugin automatically creates a backup before each import. You can restore any backup with one click.

= What WooCommerce shipping methods are supported? =

All WooCommerce shipping methods are supported:
* Flat Rate → Maps to Home Delivery
* Local Pickup → Maps to Office Delivery
* Free Shipping → Maps to Home Delivery (0 cost)
* Custom methods → Can be mapped manually

= Can I use this multiple times? =

Yes! You can run the import multiple times. It's perfect for keeping data in sync or updating prices.

= Is it safe to deactivate after import? =

Yes! Once you've imported your data successfully, you can safely deactivate this plugin. All imported data remains in Yaxii Smart Form.

== Screenshots ==

1. Migration wizard step 1 - Scan WooCommerce data
2. Step 2 - Map shipping methods
3. Step 3 - Choose import strategy
4. Step 4 - Import success with detailed report
5. Backup manager interface
6. Method mapping interface

== Changelog ==

= 1.0.0 =
* Initial release
* 4-step migration wizard
* Automatic backup system
* Method mapping interface
* Import statistics and reports
* CSV export functionality
* License validation
* Translation support (English, Arabic, French)

== Upgrade Notice ==

= 1.0.0 =
Initial release of Yaxii WooCommerce Shipping Importer.

== Additional Information ==

### Support

For support, please contact:
* Email: support@yaxii.com
* Website: https://yaxii.com/support

### Documentation

Full documentation available at: https://docs.yaxii.com/wc-importer

### Privacy

This plugin does not collect or transmit any user data. All operations are performed locally on your WordPress site.

== Credits ==

Developed by the Yaxii Team for the Yaxii Smart Form plugin ecosystem.

