# Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Prerequisites Checklist

- [ ] WordPress 5.8+ installed
- [ ] WooCommerce plugin active with shipping zones configured
- [ ] Yaxii Smart Form plugin installed and active
- [ ] Valid Yaxii Smart Form license activated
- [ ] Administrator access to WordPress

### Installation Steps

1. **Upload the Plugin**

   ```
   Upload 'yaxii-woocommerce-shipping-importer' folder to:
   /wp-content/plugins/
   ```

2. **Activate**

   ```
   WordPress Admin → Plugins → Activate "Yaxii WooCommerce Shipping Importer"
   ```

3. **Access the Tool**

   ```
   WordPress Admin → WC Importer (in sidebar menu)
   ```

   You'll see a highlighted menu item in the WordPress admin sidebar with an upload icon.

### Migration Process (4 Steps)

#### Step 1: Scan (30 seconds)

1. Click **"Scan WooCommerce Data"** button
2. Wait for scan to complete
3. Review statistics:
   - Zones found
   - Shipping methods
   - States covered
4. Click **"Continue to Method Mapping"**

#### Step 2: Map Methods (1 minute)

1. Review automatic mappings:
   - Flat Rate → Home Delivery
   - Local Pickup → Office Delivery
   - Free Shipping → Home Delivery (0 cost)
2. Adjust mappings if needed using dropdown menus
3. Click **"Continue to Import"**

#### Step 3: Import Settings (30 seconds)

1. Choose conflict resolution strategy:
   - **Overwrite** (recommended for first import)
   - Skip Existing
   - Keep Lower Prices
   - Keep Higher Prices
2. Ensure **"Create Backup Before Import"** is checked
3. Click **"Start Import"**

#### Step 4: Complete (Review)

1. Review import statistics
2. Note the backup ID for rollback if needed
3. Click **"Go to Yaxii Shipping Manager"** to verify data

### What Gets Imported?

**Before (WooCommerce):**

```
Zone: Algeria
├── Algiers (DZ:16)
│   ├── Flat Rate: 500 DZD
│   └── Local Pickup: 300 DZD
└── Oran (DZ:31)
    ├── Flat Rate: 600 DZD
    └── Local Pickup: 350 DZD
```

**After (Yaxii Smart Form):**

```
State: 16 (Algiers)
├── Home Delivery: 500 DZD
└── Office Delivery: 300 DZD

State: 31 (Oran)
├── Home Delivery: 600 DZD
└── Office Delivery: 350 DZD
```

### Common Scenarios

#### Scenario 1: Fresh Migration

**Situation:** First time importing, no existing Yaxii data  
**Strategy:** Overwrite  
**Result:** All WooCommerce data imported cleanly

#### Scenario 2: Update Prices

**Situation:** Already have Yaxii data, want to update from WooCommerce  
**Strategy:** Overwrite or Keep Lower Prices  
**Result:** Prices updated based on strategy

#### Scenario 3: Selective Import

**Situation:** Only want to import new states  
**Strategy:** Skip Existing  
**Result:** Only states without prices get imported

### Backup & Restore

#### Automatic Backup

- Created before every import
- Stored for up to 10 backups
- No action required

#### Restore Backup

1. Scroll to **"Backup Manager"** section
2. Click **"Refresh List"**
3. Find desired backup
4. Click **"Restore"** button
5. Confirm restoration

### Troubleshooting

#### "License Required" Error

**Solution:** Activate your Yaxii Smart Form license in Yaxii settings

#### "No Zones Found"

**Solution:** Ensure WooCommerce has shipping zones with state locations configured

#### Import Failed

**Solution:**

1. Check WordPress debug log
2. Try creating manual backup first
3. Contact support with error details

### Export to CSV

1. Complete migration wizard
2. On success screen, click **"Export to CSV"**
3. CSV file downloads automatically
4. Open in Excel/Google Sheets for review or editing

### After Migration

**Success! Now you can:**

- ✅ Deactivate this plugin (data stays in Yaxii)
- ✅ Go to **Yaxii Shipping Manager** at: `admin.php?page=yaxii-shipping-manager`
- ✅ Configure additional Yaxii shipping features
- ✅ Set up office-specific pricing
- ✅ Add city-level pricing overrides

**Remember:**

- Keep at least one backup
- Test on staging site first (if possible)
- Document any custom mappings you created

### Need Help?

- 📧 Email: support@yaxii.com
- 🌐 Website: https://yaxii.com/support
- 📚 Docs: https://docs.yaxii.com

---

**Estimated Total Time:** 5-10 minutes  
**Difficulty:** ⭐ Easy  
**Recommended:** ✅ Test on staging first
