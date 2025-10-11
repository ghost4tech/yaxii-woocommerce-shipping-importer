# 🔧 Troubleshooting Guide

Common issues and solutions for the Yaxii WooCommerce Shipping Importer.

---

## ❌ **Error: "فشل استيراد البيانات" (Failed to import data)**

### **Possible Causes:**

1. **Empty Data**

   - WooCommerce has no shipping zones configured
   - No states detected in zones
   - All shipping methods are disabled

2. **Validation Error**

   - Data structure doesn't match Yaxii format
   - Missing required fields
   - Invalid state codes

3. **Permission Issue**
   - WordPress can't write to options table
   - Database connection problem

### **Solutions:**

#### **Solution 1: Check WooCommerce Zones**

```
1. Go to: WooCommerce → Settings → Shipping → Shipping Zones
2. Verify you have zones configured
3. Ensure zones have:
   - State locations (e.g., DZ:16 for Algiers)
   - At least one enabled shipping method
   - Costs configured for methods
```

#### **Solution 2: Enable WordPress Debug**

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check: `/wp-content/debug.log` for detailed errors

#### **Solution 3: Verify Data Format**

```
1. Scan WooCommerce data
2. Check browser console (F12)
3. Look for JavaScript errors
4. Check Network tab for AJAX response
```

#### **Solution 4: Manual Database Check**

```sql
-- Check if option exists
SELECT * FROM wp_options WHERE option_name = 'yaxii_shipping_costs';

-- Check if we can write
UPDATE wp_options SET option_value = 'test' WHERE option_name = 'yaxii_shipping_costs';
```

#### **Solution 5: Clear Caches**

```
1. Clear WordPress object cache
2. Clear any page caching plugins
3. Try import again
```

---

## ⚠️ **Warning: WooCommerce HPOS Incompatibility**

### **Full Message:**

```
⚠ هذه الإضافة غير متوافقة مع ميزة WooCommerce التي تم تمكينها
"تخزين الطلبات فائق الأداء"، وينبغي عدم تفعيلها.
```

### **What This Means:**

This warning appeared in older versions that didn't declare HPOS compatibility.

### **Solution:**

✅ **FIXED in current version!** The plugin now includes:

```php
// Declared in main plugin file
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            YAXII_WC_IMPORTER_FILE,
            true
        );
    }
});
```

**To fix existing installations:**

1. Deactivate the plugin
2. Upload the updated version
3. Reactivate
4. Warning should disappear ✅

---

## 🔐 **Error: License Validation Failed**

### **Symptoms:**

- "Invalid or expired license"
- "License could not be verified"
- Plugin auto-deactivates

### **Solutions:**

#### **Check 1: Yaxii Smart Form Installed?**

```
WordPress Admin → Plugins
→ Look for "Yaxii Smart Form"
→ Must be active
```

#### **Check 2: License Activated?**

```
1. Go to Yaxii Smart Form settings
2. Find License tab
3. Verify license is activated
4. Check expiration date
```

#### **Check 3: License Type**

```
Required: Professional or Business plan
Not supported: Free plan

Check at: https://plugins.yaxii.dev
```

#### **Check 4: Clear License Cache**

```sql
-- Delete cached license checks
DELETE FROM wp_options WHERE option_name LIKE 'yaxii_wc_importer_%';

-- Delete transients
DELETE FROM wp_options WHERE option_name LIKE '_transient_yaxii_wc_importer_%';
```

Then try again.

---

## 📊 **Issue: No Statistics After Scan**

### **Symptoms:**

- "0 zones found"
- "0 shipping methods"
- "0 states covered"

### **Causes:**

1. WooCommerce zones not configured
2. Zones have "All locations" instead of specific states
3. All shipping methods are disabled
4. Wrong country selected

### **Solutions:**

#### **Verify WooCommerce Zones:**

```
WooCommerce → Settings → Shipping → Shipping Zones

Each zone should have:
✓ Zone locations = States (e.g., DZ:16, not "All locations")
✓ Shipping methods = At least one enabled
✓ Costs = Configured for each method
```

#### **Check State Format:**

```
Correct: DZ:16 (Country:State)
Wrong: Just "16"
Wrong: "Algiers" (name instead of code)
```

---

## 🏠🏢 **Issue: Only One Delivery Type Imported**

### **Symptoms:**

- Only home_delivery has prices
- office_delivery is empty (or vice versa)

### **Cause:**

Both methods in WooCommerce have similar names and weren't detected separately.

### **Solution:**

#### **Method 1: Check Auto-Detection**

```
1. Go to Step 2 (Method Mapping)
2. Look for detection badges:
   🏠 = Home delivery detected
   🏢 = Office delivery detected
   ⚠️ = Not detected, manual selection needed
3. Manually adjust mappings if needed
```

#### **Method 2: Rename WooCommerce Methods**

```
Make titles more distinct:
✓ "سعر التوصيل للمنزل" (contains منزل)
✓ "سعر التوصيل للمكتب" (contains مكتب)

Or English:
✓ "Home Delivery"
✓ "Office Pickup"
```

#### **Method 3: Manual Mapping**

```
1. In Step 2, use the dropdown
2. Map first flat_rate → Home Delivery
3. Map second flat_rate → Office Delivery
4. Continue with import
```

---

## 🎯 **Issue: Sticky Bar Not Appearing**

### **Symptoms:**

- Have 100+ methods but no sticky bar
- Need to scroll to bottom

### **Causes:**

1. Less than 5 shipping methods
2. JavaScript error preventing execution
3. CSS not loaded

### **Solutions:**

#### **Check 1: Method Count**

```
Sticky bar only appears if:
- 5+ shipping methods detected
- User scrolls down
- Regular buttons are below viewport
```

#### **Check 2: Browser Console**

```
1. Press F12
2. Go to Console tab
3. Look for JavaScript errors
4. Fix any jQuery or script errors
```

#### **Check 3: Assets Loading**

```
1. Press F12 → Network tab
2. Reload page
3. Verify admin.css and admin.js load
4. Check for 404 errors
```

---

## 🌍 **Issue: Translations Not Working**

### **Symptoms:**

- Interface still in English
- Arabic/French not showing

### **Solutions:**

#### **Check 1: WordPress Language**

```
Settings → General → Site Language
→ Select "العربية" or "Français"
→ Save
→ Reload plugin page
```

#### **Check 2: .mo Files Compiled**

```
Check if these files exist:
- languages/yaxii-wc-importer-ar.mo
- languages/yaxii-wc-importer-fr_FR.mo

If not, compile them:
1. Download Poedit (https://poedit.net/)
2. Open .po file
3. Save (compiles .mo automatically)
```

#### **Check 3: File Permissions**

```bash
chmod 644 languages/*.mo
chmod 644 languages/*.po
```

---

## 💾 **Issue: Backup Failed**

### **Symptoms:**

- "Failed to create backup"
- "No data to backup"

### **Causes:**

1. No existing Yaxii data (first import)
2. Database write permission issue

### **Solutions:**

#### **If First Import:**

```
This is normal! There's no data to backup yet.
→ The import will proceed anyway
→ Future imports will create backups
```

#### **If Not First Import:**

```
1. Check database permissions
2. Verify wp_options table is writable
3. Check disk space on server
4. Try manual backup first
```

---

## 🔄 **Issue: Can't Restore Backup**

### **Symptoms:**

- "Backup not found"
- Restore fails silently

### **Solutions:**

#### **Verify Backup Exists:**

```sql
SELECT * FROM wp_options
WHERE option_name LIKE 'yaxii_wc_importer_backup_%';
```

#### **Check Backup ID:**

```
Format: YYYYMMDD_HHMMSS_XXXXXX
Example: 20241011_143022_a1b2c3

Verify you're using correct backup ID
```

#### **Manual Restore:**

```php
// Get backup data
$backup = get_option('yaxii_wc_importer_backup_20241011_143022_a1b2c3');

// Restore
if ($backup && isset($backup['data'])) {
    update_option('yaxii_shipping_costs', $backup['data']);
}
```

---

## 📥 **Issue: CSV Export Empty**

### **Symptoms:**

- CSV file downloads but is empty
- Only headers, no data

### **Causes:**

1. No data to export
2. Data conversion failed

### **Solutions:**

#### **Check Data:**

```
1. Go to Yaxii Shipping Manager
2. Verify shipping costs exist
3. If empty, run import first
```

#### **Try Again:**

```
1. Complete a successful import
2. On success screen, click "Export to CSV"
3. CSV should contain all data
```

---

## 🔒 **Issue: "Security Check Failed"**

### **Symptoms:**

- All AJAX requests fail
- Error: "Security check failed"

### **Causes:**

1. Nonce expired (page open too long)
2. Caching plugin interference
3. AJAX URL mismatch

### **Solutions:**

#### **Refresh Page:**

```
1. Reload the importer page
2. Try operation again
3. Nonces are regenerated
```

#### **Disable Caching:**

```
Temporarily disable:
- WP Super Cache
- W3 Total Cache
- Other caching plugins

Test import again
```

#### **Check AJAX URL:**

```javascript
// In browser console
console.log(yaxiiWcImporter.ajaxUrl);
// Should output: /wp-admin/admin-ajax.php
```

---

## 🎨 **Issue: UI Looks Broken**

### **Symptoms:**

- No styling
- Elements misaligned
- Missing icons

### **Causes:**

1. CSS not loading
2. Theme conflict
3. Plugin conflict

### **Solutions:**

#### **Check CSS Loading:**

```
1. View page source
2. Search for: admin.css
3. Verify file exists and loads
4. Check for 404 errors
```

#### **Test with Default Theme:**

```
1. Temporarily switch to Twenty Twenty-Four
2. Test plugin
3. If works, your theme has a conflict
```

#### **Disable Other Plugins:**

```
1. Deactivate all plugins except:
   - WooCommerce
   - Yaxii Smart Form
   - This importer
2. Test again
```

---

## 📱 **Issue: Mobile View Problems**

### **Symptoms:**

- Layout broken on mobile
- Buttons not clickable
- Text overlapping

### **Solutions:**

#### **Clear Mobile Cache:**

```
1. Clear browser cache
2. Hard reload (Ctrl+Shift+R)
3. Try again
```

#### **Check Viewport:**

```
The plugin is responsive for:
- Desktop: > 782px
- Tablet: 600-782px
- Mobile: < 600px
```

---

## 🐛 **Debug Mode**

### **Enable Full Debugging:**

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

### **Check Logs:**

```
Location: /wp-content/debug.log

Look for:
- "Yaxii WC Importer:" prefix
- PHP errors
- AJAX errors
- Validation errors
```

### **Browser Console:**

```
1. Press F12
2. Console tab → Look for errors
3. Network tab → Check AJAX calls
4. Response data → View error details
```

---

## 📞 **Still Having Issues?**

### **Collect This Information:**

1. **WordPress Info:**

   - WordPress version
   - PHP version
   - WooCommerce version
   - Yaxii Smart Form version

2. **Error Details:**

   - Exact error message
   - When it occurs (which step)
   - Browser console errors
   - Debug log entries

3. **Environment:**
   - Number of shipping zones
   - Number of shipping methods
   - Server environment (Apache/Nginx)

### **Contact Support:**

```
Email: support@yaxii.com

Include:
- Error message
- Screenshots
- debug.log excerpt
- WP/WC/PHP versions
```

### **Useful Links:**

- 🌐 Get Help: https://plugins.yaxii.dev
- 📚 Docs: README.md
- 🚀 Quick Start: QUICKSTART.md

---

## ✅ **Common Fixes Summary**

| Issue                | Quick Fix                                        |
| -------------------- | ------------------------------------------------ |
| Import fails         | Enable WP_DEBUG, check debug.log                 |
| HPOS warning         | Update plugin, includes HPOS compatibility       |
| License error        | Reactivate Yaxii license                         |
| No zones found       | Configure WooCommerce shipping zones with states |
| Sticky bar missing   | Need 5+ methods, scroll down to trigger          |
| Translation issues   | Compile .mo files, check WP language setting     |
| Security check fails | Refresh page to regenerate nonces                |

---

**Most issues are solved by:**

1. ✅ Enabling WordPress debug mode
2. ✅ Checking debug.log file
3. ✅ Verifying WooCommerce zones are configured correctly

---

_Need more help? → support@yaxii.com_ 📧
