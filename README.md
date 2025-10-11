# Yaxii WooCommerce Shipping Importer

**Version:** 1.0.0  
**Author:** Yaxii Team  
**License:** GPL v2 or later

## 📋 Description

A free addon plugin for **Yaxii Smart Form** license holders that enables one-click migration of WooCommerce shipping zones and costs to the Yaxii Smart Form Shipping Manager.

## ✨ Features

- **🚀 Simple Wizard Interface** - 4-step migration process that anyone can follow
- **🔄 Automatic Data Mapping** - Intelligently maps WooCommerce methods to Yaxii methods
- **💾 Automatic Backups** - Creates backups before import for safe rollback
- **🎯 Flexible Import Options** - Multiple strategies for handling existing data
- **📊 Detailed Reports** - See exactly what was imported
- **📥 CSV Export** - Export WooCommerce data to CSV format
- **🔐 License Protected** - Free for valid Yaxii Smart Form license holders
- **🌍 Translation Ready** - Supports Arabic, French, and English

## 📦 Requirements

- **WordPress:** 5.8 or higher
- **PHP:** 7.4 or higher
- **WooCommerce:** 5.0 or higher
- **Yaxii Smart Form:** Latest version with Shipping Manager
- **Valid Yaxii Smart Form License:** Required for use

## 🔧 Installation

### Method 1: Manual Installation

1. Download the plugin folder `yaxii-woocommerce-shipping-importer`
2. Upload to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **WC Importer** in the admin sidebar (highlighted menu) to start migration

### Method 2: From Zip File

1. Download the plugin as a ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click "Install Now"
4. Activate the plugin
5. Go to **WC Importer** in the admin sidebar (highlighted menu) to start migration

## 🎯 How to Use

### Step 1: Scan WooCommerce Data

1. Navigate to **WC Importer** from the admin sidebar menu
2. Click "Scan WooCommerce Data" button
3. Review the scan results showing:
   - Number of shipping zones found
   - Number of shipping methods
   - Number of states covered

### Step 2: Map Shipping Methods

The wizard will automatically map WooCommerce methods to Yaxii methods:

- **Flat Rate** → Home Delivery (التوصيل للمنزل)
- **Local Pickup** → Office Delivery (التوصيل للمكتب)
- **Free Shipping** → Home Delivery (with 0 cost)

You can customize these mappings if needed.

### Step 3: Choose Import Strategy

Select how to handle existing Yaxii shipping costs:

- **Overwrite:** Replace all existing prices (recommended for first import)
- **Skip Existing:** Only import new states
- **Keep Lower Prices:** Use the cheaper option
- **Keep Higher Prices:** Use the more expensive option

### Step 4: Import & Complete

- Click "Start Import"
- View the detailed import report
- Access your data in Yaxii Shipping Manager

## 🔄 Data Mapping

### WooCommerce Format

```
Zone: Algeria
├── State: DZ:16 (Algiers)
├── Shipping Method: Flat Rate
└── Cost: 500 DZD
```

### Yaxii Format (After Import)

```php
'16' => [
    'enabled' => true,
    'costs' => [
        'home_delivery' => 500
    ],
    'cities' => []
]
```

## 💾 Backup & Restore

### Automatic Backups

- Created automatically before each import
- Stored in WordPress options table
- Up to 10 backups kept automatically

### Manual Backups

1. Scroll to "Backup Manager" section
2. Click "Create Backup Now"
3. View all available backups
4. Restore any backup with one click

### Backup Structure

Each backup contains:

- Timestamp
- Full Yaxii shipping costs data
- User ID who created the backup
- State count

## 📊 CSV Export

Export your WooCommerce shipping data as CSV:

1. Complete the migration wizard
2. Click "Export to CSV" on success page
3. CSV file will download automatically

**CSV Format:**

```csv
State Code,State Name,Method,Cost
16,Algiers,home_delivery,500
16,Algiers,office_delivery,300
```

## 🛡️ Security Features

- **Nonce Verification** - All AJAX requests are secured
- **Capability Checks** - Requires `manage_options` permission
- **License Validation** - Checks for valid Yaxii license
- **Data Sanitization** - All inputs are sanitized
- **Backup System** - Automatic backups before modifications

## 🔍 Troubleshooting

### Plugin Won't Activate

**Error:** "Yaxii Smart Form plugin is required"  
**Solution:** Install and activate Yaxii Smart Form plugin first

**Error:** "A valid Yaxii Smart Form license is required"  
**Solution:** Activate your Yaxii Smart Form license

### No Zones Found

**Issue:** Scan returns 0 zones  
**Solution:**

- Check that WooCommerce shipping zones are configured
- Ensure zones have state locations (not "All locations")
- Verify shipping methods are enabled

### Import Failed

**Issue:** Import returns error  
**Solution:**

- Check WordPress debug log
- Verify you have `manage_options` capability
- Try creating a manual backup first
- Contact support with error message

## 📝 Development

### File Structure

```
yaxii-woocommerce-shipping-importer/
├── yaxii-woocommerce-shipping-importer.php  # Main plugin file
├── includes/
│   ├── class-dependency-checker.php          # Dependency validation
│   ├── class-wc-data-extractor.php          # WC data extraction
│   ├── class-data-mapper.php                 # Data mapping logic
│   ├── class-yaxii-data-importer.php        # Yaxii data import
│   ├── class-backup-manager.php              # Backup/restore
│   └── class-migration-logger.php            # Activity logging
├── admin/
│   ├── class-admin.php                       # Admin interface
│   ├── class-ajax-handler.php                # AJAX handlers
│   ├── views/
│   │   └── migration-wizard.php              # Wizard UI
│   └── assets/
│       ├── css/
│       │   └── admin.css                     # Admin styles
│       └── js/
│           └── admin.js                      # Admin JavaScript
├── languages/
│   └── yaxii-wc-importer.pot                # Translation template
└── README.md                                  # This file
```

### Hooks & Filters

#### Actions

```php
// After plugin initialization
do_action('yaxii_wc_importer_init');

// Before data import
do_action('yaxii_wc_before_import', $data);

// After successful import
do_action('yaxii_wc_after_import', $data, $stats);

// After backup creation
do_action('yaxii_wc_backup_created', $backup_id);
```

#### Filters

```php
// Customize default method mapping
apply_filters('yaxii_wc_default_method_mapping', $mapping);

// Customize merge strategy
apply_filters('yaxii_wc_merge_strategy', $strategy, $new_data, $existing_data);

// Customize backup retention (default: 10)
apply_filters('yaxii_wc_max_backups', 10);
```

## 🌍 Translation

The plugin is translation-ready and includes:

- English (default)
- Arabic translation support
- French translation support

To translate:

1. Copy `languages/yaxii-wc-importer.pot`
2. Use Poedit or similar tool
3. Save as `yaxii-wc-importer-{locale}.po`
4. Compile to `.mo` file
5. Place in `/languages/` directory

## 🤝 Support

For support, please contact:

- **Email:** support@yaxii.com
- **Website:** https://yaxii.com/support
- **Documentation:** https://docs.yaxii.com

## 📄 License

This plugin is licensed under GPL v2 or later.

```
Copyright (C) 2024 Yaxii Team

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 📜 Changelog

### Version 1.0.0 (2024-10-11)

- ✨ Initial release
- 🚀 4-step migration wizard
- 💾 Automatic backup system
- 🔄 Method mapping interface
- 📊 Import statistics and reports
- 📥 CSV export functionality
- 🔐 License validation
- 🌍 Translation support (EN, AR, FR)

## 🎯 Roadmap

Future enhancements planned:

- [ ] Scheduled automatic syncs
- [ ] Selective state import
- [ ] Import preview with diff view
- [ ] Advanced mapping rules
- [ ] Import/export mapping presets
- [ ] Email notifications
- [ ] Integration with more shipping providers

## ⚠️ Important Notes

1. **This plugin is FREE for Yaxii Smart Form license holders**
2. **Always create a backup before importing** (done automatically)
3. **Test on staging site first** before production
4. **No modifications to Yaxii Smart Form plugin required**
5. **Can be deactivated after migration**

## 👥 Credits

Developed by the Yaxii Team for the Yaxii Smart Form plugin ecosystem.

---

Made with ❤️ by [Yaxii Team](https://yaxii.com)
