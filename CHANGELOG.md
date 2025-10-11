# Changelog

## Version 1.0.1 (Latest Updates)

### 🎨 **UI/UX Improvements**

#### Top-Level Menu

- ✅ Moved from `Tools` submenu to **top-level menu item**
- ✅ Menu appears in WordPress admin sidebar with upload icon (dashicons-upload)
- ✅ Position: After Settings menu (position 59)
- ✅ Highlighted and easy to find

#### Sticky Action Bar

- ✅ Added **floating sticky action bar** on Step 2 (Method Mapping)
- ✅ Appears automatically when there are 5+ shipping methods
- ✅ Shows when regular buttons are below the fold
- ✅ Includes: Back button + Continue button
- ✅ Smooth slide-up animation
- ✅ Responsive for mobile devices

### 🌍 **Translation Support**

#### Languages Added

- ✅ **Arabic (ar)** - Full translation (`yaxii-wc-importer-ar.po`)
- ✅ **French (fr_FR)** - Full translation (`yaxii-wc-importer-fr_FR.po`)
- ✅ **English** - Default language (built-in)

#### Translation Files

- `languages/yaxii-wc-importer.pot` - Template file
- `languages/yaxii-wc-importer-ar.po` - Arabic source
- `languages/yaxii-wc-importer-fr_FR.po` - French source
- `languages/README.md` - Translation guide
- `compile-translations.sh` - Linux/Mac compile script
- `compile-translations.bat` - Windows compile script

### 🔧 **Technical Improvements**

#### Smart Detection

- ✅ Enhanced shipping method detection
- ✅ Analyzes Arabic method titles (منزل، مكتب)
- ✅ Supports English keywords (home, office)
- ✅ French keyword support (domicile, bureau)
- ✅ Auto-detects home vs office delivery
- ✅ Visual badges: 🏠 Auto-detected / 🏢 Auto-detected / ⚠️ Please select

#### URL Corrections

- ✅ Fixed Yaxii Shipping Manager URL
- ✅ Changed from: `admin.php?page=yaxii-smart-form-shipping`
- ✅ Changed to: `admin.php?page=yaxii-shipping-manager`

### 📝 **Documentation Updates**

- ✅ Updated all documentation with correct menu location
- ✅ Fixed URLs throughout documentation
- ✅ Added translation compilation guide
- ✅ Updated QUICKSTART.md
- ✅ Updated README.md
- ✅ Updated readme.txt (WordPress format)

---

## Version 1.0.0 (Initial Release)

### ✨ **Core Features**

- 4-step migration wizard
- Automatic WooCommerce data scanning
- Method mapping interface
- Multiple merge strategies
- Automatic backup system
- CSV export functionality
- License validation
- Security features

### 🎯 **Functionality**

- Extract WooCommerce shipping zones and costs
- Map methods to Yaxii format
- Import with conflict resolution
- Backup and restore system
- Activity logging
- Error handling

### 🔐 **Security**

- Nonce verification
- Capability checks
- License validation
- Data sanitization
- XSS protection
- CSRF protection

---

## Upgrade Notes

### From 1.0.0 to 1.0.1

**No breaking changes!** Simply update the plugin files.

**New Features:**

- Top-level menu instead of Tools submenu
- Sticky action bar for better UX
- Arabic and French translations
- Smarter method detection
- Corrected URLs

**What Stays The Same:**

- All core functionality
- Database structure
- Backup system
- Security features
- API compatibility

---

## Future Roadmap

### Planned for v1.1

- [ ] Selective state import (checkboxes)
- [ ] Import preview with diff view
- [ ] Advanced mapping rules
- [ ] Email notifications
- [ ] CLI commands

### Planned for v1.2

- [ ] Scheduled automatic syncs
- [ ] Export/import mapping presets
- [ ] Integration with more providers
- [ ] Machine learning-based mapping suggestions

---

## Support

For support or questions:

- 📧 Email: support@yaxii.com
- 🌐 Website: https://yaxii.com/support
- 📚 Docs: https://docs.yaxii.com/wc-importer
