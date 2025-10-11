# 🚀 Yaxii WooCommerce Shipping Importer

[![Version](https://img.shields.io/badge/version-1.0.1-blue.svg)](https://github.com/ghost4tech/yaxii-wc-shipping-import-tool)
[![License](https://img.shields.io/badge/license-GPL%20v2+-green.svg)](LICENSE)
[![WordPress](https://img.shields.io/badge/WordPress-5.8+-orange.svg)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-5.0+-purple.svg)](https://woocommerce.com)
[![HPOS Compatible](https://img.shields.io/badge/HPOS-Compatible-success.svg)](https://woocommerce.com/document/high-performance-order-storage/)

> **Migrate WooCommerce shipping zones and costs to Yaxii Smart Form Shipping Manager in 5 minutes!**

Free for Yaxii Smart Form license holders | Simple 4-step wizard | Smart auto-detection | Multi-language support

---

## ✨ Features

### 🎯 **Core Functionality**
- **One-Click Migration** - Migrate all WooCommerce shipping zones with one click
- **Smart Detection** - Automatically detects home & office delivery methods
- **Automatic Backup** - Creates backups before every import
- **4-Step Wizard** - Simple, guided migration process
- **CSV Export** - Export your WooCommerce shipping data to CSV

### 🎨 **User Experience**
- **Modern UI** - Beautiful, professional interface with animations
- **Top-Level Menu** - Highlighted menu item in WordPress admin sidebar
- **Sticky Action Bar** - No endless scrolling with 100+ shipping methods
- **Progress Indicator** - Modern circular design with checkmarks
- **Mobile Responsive** - Works perfectly on all devices

### 🌍 **Multi-Language**
- **Arabic** (العربية) - 100% translated
- **French** (Français) - 100% translated  
- **English** - Default language
- Auto-loads based on WordPress settings

### 🔐 **Security**
- **10-Layer License Validation** - Enterprise-grade security
- **HPOS Compatible** - Full WooCommerce 8.0+ support
- **Freemius Integration** - Seamless license checking
- **Periodic Validation** - Hourly license re-checks
- **AJAX Protection** - Nonce & capability checks on all requests

---

## 📦 Installation

### **Requirements:**
- WordPress 5.8 or higher
- PHP 7.4 or higher
- WooCommerce 5.0 or higher
- Yaxii Smart Form (latest version)
- **Valid Yaxii Smart Form License** (Professional or Business)

### **Quick Install:**

1. **Download** the latest release
2. **Upload** to WordPress: `Plugins → Add New → Upload`
3. **Activate** the plugin
4. **Access** via: `Yaxii WC Importer` in admin sidebar
5. **Follow** the 4-step wizard

---

## 🚀 Quick Start

```
1. Click "Scan WooCommerce Data"      (30 seconds)
2. Review auto-detected methods        (1 minute)
3. Choose "Overwrite" strategy         (30 seconds)
4. Click the GREEN "Start Import"      (1 minute)
5. Success! ✅                         
```

**Total Time:** ~5 minutes  
**Difficulty:** ⭐ Very Easy

---

## 📊 What Gets Imported

### **WooCommerce Format:**
```
Zone: Algeria
├── State: DZ:16 (Algiers)
├── Shipping Method: سعر توصيل للمنزل
└── Cost: 500 DZD
```

### **Yaxii Format (After Import):**
```php
'16' => [
    'enabled' => true,
    'costs' => [
        'home_delivery' => 500,
        'office_delivery' => 300
    ],
    'cities' => []
]
```

---

## 🎨 Screenshots

### **Top-Level Menu**
Prominent menu item in WordPress admin sidebar with upload icon

### **Modern Progress Indicator**
Circular design with checkmarks for completed steps

### **Smart Detection**
Auto-detects home (🏠) and office (🏢) delivery methods

### **Sticky Action Bar**
Appears automatically when there are many methods - no scrolling needed!

### **Import Button**
Green, glowing, and impossible to miss!

---

## 🧠 Smart Detection

The plugin intelligently detects method types based on keywords:

### **Home Delivery:**
- Arabic: منزل, بيت, دار
- English: home, domicile, house
- French: domicile, maison

### **Office Delivery:**
- Arabic: مكتب, وكالة, مركز
- English: office, bureau, agency, center, pickup
- French: bureau, agence, centre

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| [START_HERE.md](START_HERE.md) | Complete overview and guide |
| [QUICKSTART.md](QUICKSTART.md) | 5-minute migration guide |
| [README.md](README.md) | Full documentation |
| [VISUAL_GUIDE.md](VISUAL_GUIDE.md) | UI/UX walkthrough |
| [SECURITY_LAYERS.md](SECURITY_LAYERS.md) | Security architecture |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Common issues & solutions |
| [CHANGELOG.md](CHANGELOG.md) | Version history |

---

## 🔐 Security Features

### **10-Layer License Validation:**
1. ✅ Direct access prevention
2. ✅ WordPress version check
3. ✅ PHP version check
4. ✅ WooCommerce validation
5. ✅ Yaxii plugin verification
6. ✅ Primary license function
7. ✅ Freemius SDK validation
8. ✅ Database license check
9. ✅ Periodic runtime validation
10. ✅ AJAX request validation

**Result:** Very hard to bypass without valid license 🔒

---

## 🛠️ Technical Stack

- **Backend:** PHP 7.4+ (OOP, WordPress API)
- **Frontend:** JavaScript (jQuery), CSS3
- **Database:** WordPress Options API
- **Security:** Freemius SDK integration
- **i18n:** WordPress translation system
- **HPOS:** Full WooCommerce 8.0+ compatibility

---

## 📈 Stats

| Metric | Value |
|--------|-------|
| Total Files | 34 |
| Lines of Code | 9,500+ |
| Languages | 3 |
| Security Layers | 10 |
| Documentation Pages | 10 |
| Translation Strings | 160+ |

---

## 🤝 Contributing

This is a proprietary plugin for Yaxii Smart Form license holders. 

For bugs or feature requests:
- 📧 Email: support@yaxii.com
- 🌐 Website: https://plugins.yaxii.dev

---

## 📝 License

GPL v2 or later

**Free for valid Yaxii Smart Form license holders**

---

## 🎯 Use Cases

- ✅ Migrating from WooCommerce to Yaxii Smart Form
- ✅ Bulk importing shipping costs
- ✅ Syncing pricing between systems
- ✅ Setting up regional pricing quickly
- ✅ Backing up shipping configuration

---

## 💡 Why This Plugin?

### **Problem It Solves:**
Setting up shipping costs for 48+ Algerian states manually is time-consuming. If you already have WooCommerce zones configured, why do it twice?

### **Solution:**
One-click import that:
- Scans WooCommerce automatically
- Detects method types intelligently  
- Maps everything correctly
- Imports both home & office delivery
- Creates automatic backups
- Takes only 5 minutes!

---

## 🌟 Highlights

### **User Experience:**
- 🎨 Beautiful modern UI
- 🌍 Multi-language support
- 📱 Mobile responsive
- ⚡ Fast performance
- 🔒 Secure & reliable

### **Developer Experience:**
- 📖 Comprehensive documentation
- 🔧 Easy to maintain
- 🧪 Well-tested
- 💾 Clean code structure
- 🛡️ Security-first approach

---

## 📞 Support

- **Email:** support@yaxii.com
- **Website:** https://plugins.yaxii.dev
- **Documentation:** See docs folder
- **Issues:** GitHub Issues (for license holders)

---

## 🏆 Credits

Developed by the Yaxii Team for the Yaxii Smart Form plugin ecosystem.

---

## ⚠️ Important Notes

1. **License Required:** Valid Yaxii Smart Form license (Professional/Business plan)
2. **Dependencies:** WooCommerce & Yaxii Smart Form must be active
3. **Safe:** Creates automatic backups, can be deactivated after use
4. **No Modifications:** Zero changes required to main Yaxii plugin
5. **Free:** No additional cost for license holders

---

## 🚀 Get Started

1. **Download** the latest release
2. **Read** [QUICKSTART.md](QUICKSTART.md)
3. **Install** and activate
4. **Migrate** your shipping data
5. **Enjoy** automated shipping costs!

---

**Made with ❤️ for the Yaxii ecosystem**

[Get Yaxii Smart Form](https://plugins.yaxii.dev) | [Documentation](README.md) | [Support](mailto:support@yaxii.com)

