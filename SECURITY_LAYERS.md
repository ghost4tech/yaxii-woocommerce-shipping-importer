# Security & License Validation Layers

## 🔐 Multi-Layer Security Architecture

This plugin implements **10 layers of security** to ensure only valid Yaxii Smart Form license holders can use it.

---

## 🛡️ License Validation Layers

### **Layer 1: Plugin Activation Check**

**Location:** `yaxii-woocommerce-shipping-importer.php`

```php
// Direct access prevention
if (!defined('ABSPATH')) exit;
if (!function_exists('add_action')) exit;
if (!defined('WPINC')) die;
```

### **Layer 2: Dependency Checker - WordPress Version**

**Location:** `includes/class-dependency-checker.php`

```php
check_wordpress_version()
// Requires WordPress 5.8+
```

### **Layer 3: Dependency Checker - PHP Version**

```php
check_php_version()
// Requires PHP 7.4+
```

### **Layer 4: Dependency Checker - WooCommerce**

```php
check_woocommerce()
// Requires WooCommerce 5.0+ active
```

### **Layer 5: Dependency Checker - Yaxii Plugin**

```php
check_yaxii_smart_form()
// Verifies:
// - YAXII_VERSION constant exists
// - YaxiiSmartForm\Core\Plugin class exists
// - YaxiiSmartForm\Admin\ShippingManager class exists
```

### **Layer 6: Primary License Function**

```php
check_yaxii_license()
// Calls: yaxii_can_use_premium_code()
```

### **Layer 7: Freemius SDK Validation**

```php
verify_freemius_license()
// Checks:
// - yaxii_fs() function exists
// - is_paying() or is_trial()
// - is_plan('professional') or is_plan('business')
```

### **Layer 8: License Data Option**

```php
get_option('yaxii_smart_form_license_data')
// Verifies license data exists in database
```

### **Layer 9: Periodic Runtime Check**

**Location:** Main plugin file

```php
periodic_license_check()
// Runs every hour via transient
// Disables menu if license invalid
// Shows admin notice
```

### **Layer 10: AJAX Request Validation**

**Location:** `admin/class-ajax-handler.php`

```php
verify_request()
// On EVERY AJAX call:
// - Nonce verification
// - Capability check (manage_options)
// - verify_license() with 4 sub-layers
// - Transient caching (1 hour)
```

---

## 🔒 Security Flow

```
User Activates Plugin
        ↓
Layer 1: Direct Access Check ✓
        ↓
Layer 2-4: WordPress/PHP/WC Checks ✓
        ↓
Layer 5: Yaxii Plugin Check ✓
        ↓
Layer 6: Primary License Function ✓
        ↓
Layer 7: Freemius SDK Validation ✓
        ↓
Layer 8: Database License Data ✓
        ↓
Layer 9: Periodic Runtime Checks ✓
        ↓
User Makes AJAX Request
        ↓
Layer 10: AJAX Validation ✓
        ↓
✅ Action Allowed
```

---

## 🚫 What Happens on Failure

### At Activation

- Plugin **deactivates automatically**
- Error message displayed
- "Get Yaxii Smart Form" button shown
- Links to: `https://plugins.yaxii.dev`

### During Runtime

- Admin menu **removed**
- AJAX requests **blocked**
- Error notice displayed
- User redirected to activate license

### On AJAX Calls

- Request **rejected immediately**
- Error response sent
- License required flag set
- Upgrade URL provided

---

## 🧪 Bypass Prevention Techniques

### 1. **Multiple Validation Points**

- Can't bypass one check without triggering others
- Redundant validation at different levels

### 2. **Function Existence Checks**

```php
if (!function_exists('yaxii_can_use_premium_code'))
if (!function_exists('yaxii_fs'))
if (!class_exists('YaxiiSmartForm\Core\Plugin'))
```

### 3. **Freemius Direct Checks**

```php
$fs->is_paying()
$fs->is_trial()
$fs->is_plan('professional', true)
```

### 4. **Database Validation**

```php
get_option('yaxii_smart_form_license_data')
```

### 5. **Transient Caching**

```php
set_transient('yaxii_wc_importer_license_check', 'valid', 3600)
// Prevents constant re-checks
// Expires after 1 hour
```

### 6. **Periodic Re-validation**

- Checks license every hour
- Uses `admin_init` hook
- Can't be easily disabled

### 7. **AJAX-Level Protection**

- Every AJAX endpoint protected
- No bypass through direct calls
- License re-checked on each request

### 8. **Class/Constant Checks**

- Verifies actual code exists
- Not just option flags
- Harder to fake

---

## 💻 For Developers

### How to Test License Validation

#### Test 1: No Yaxii Plugin

```bash
# Deactivate Yaxii Smart Form
# Try to activate WC Importer
# Expected: Auto-deactivation + error
```

#### Test 2: No License

```bash
# Install Yaxii but don't activate license
# Try to use WC Importer
# Expected: Error + upgrade link
```

#### Test 3: Expired License

```bash
# Use expired license
# Try AJAX operations
# Expected: Blocked with error
```

#### Test 4: Runtime Check

```bash
# Delete transient: yaxii_wc_importer_periodic_check
# Wait and refresh admin
# Should re-validate license
```

---

## 🔧 Maintenance

### Transients Used

| Transient Key                      | Duration | Purpose             |
| ---------------------------------- | -------- | ------------------- |
| `yaxii_wc_importer_license_check`  | 1 hour   | AJAX license cache  |
| `yaxii_wc_importer_periodic_check` | 1 hour   | Periodic validation |

### Options Used

| Option Key                      | Purpose            |
| ------------------------------- | ------------------ |
| `yaxii_smart_form_license_data` | License data check |
| `yaxii_wc_importer_backup_*`    | Backup storage     |
| `yaxii_wc_importer_logs`        | Migration logs     |

---

## ⚠️ Important Notes

1. **Do not remove license checks** - They're essential for compliance
2. **Multiple layers work together** - Removing one doesn't bypass others
3. **Freemius integration** - Relies on Yaxii's Freemius implementation
4. **Automatic deactivation** - Plugin deactivates if requirements not met
5. **Hourly re-validation** - Can't bypass by temporarily enabling license

---

## 🎯 License Holder Benefits

Valid license holders get:

- ✅ Full access to all features
- ✅ Automatic updates
- ✅ Priority support
- ✅ Free for unlimited sites
- ✅ No restrictions

---

## 📞 Support

License issues? Contact:

- 📧 support@yaxii.com
- 🌐 https://plugins.yaxii.dev
- 📚 License management docs

---

**Security Level:** 🔒🔒🔒🔒🔒 Maximum (10 layers)  
**Bypass Difficulty:** ⭐⭐⭐⭐⭐ Very Hard  
**Compliance:** ✅ Freemius Compatible
