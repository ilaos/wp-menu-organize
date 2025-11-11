# Installation Guide

[← Back to Documentation](./index.md)

**Complete installation instructions for Submittal & Spec Sheet Builder**

---

## Quick Installation (Recommended)

The fastest way to install the plugin is through the WordPress admin dashboard.

### Step-by-Step

1. **Download the Plugin**
   - Purchase and download the plugin ZIP file from your account
   - Keep the ZIP file - don't extract it

2. **Upload to WordPress**
   ```
   WordPress Admin → Plugins → Add New → Upload Plugin
   ```
   - Click "Choose File" and select the plugin ZIP
   - Click "Install Now"
   - Wait for upload to complete

3. **Activate**
   - Click "Activate Plugin" when installation completes
   - You'll see "Submittal Builder" appear in your admin menu

4. **Verify Installation**
   ```
   WordPress Admin → Submittal Builder → Dashboard
   ```
   - If you see the dashboard, installation was successful

**Time Required:** 2-3 minutes

---

## Manual Installation (FTP)

If automatic installation doesn't work, use manual FTP installation.

### Prerequisites

- FTP client (FileZilla, Cyberduck, or similar)
- FTP credentials for your WordPress site
- Plugin ZIP file

### Step-by-Step

1. **Extract Plugin ZIP**
   - Unzip the plugin file on your computer
   - You should see a folder named `submittal-spec-sheet-builder`

2. **Connect via FTP**
   - Open your FTP client
   - Connect to your WordPress site
   - Navigate to `/wp-content/plugins/`

3. **Upload Plugin Folder**
   - Upload the entire `submittal-spec-sheet-builder` folder
   - Ensure all files transfer successfully
   - Check that folder structure is intact

4. **Activate in WordPress**
   ```
   WordPress Admin → Plugins → Installed Plugins
   ```
   - Find "Submittal & Spec Sheet Builder"
   - Click "Activate"

5. **Verify Installation**
   - Check that "Submittal Builder" appears in admin menu
   - Visit Dashboard to confirm

**Time Required:** 5-10 minutes

---

## WP-CLI Installation (Advanced)

For developers and system administrators who prefer command line.

### Prerequisites

- SSH access to your server
- WP-CLI installed
- Plugin ZIP file on server or URL

### Commands

```bash
# Navigate to WordPress root
cd /path/to/wordpress

# Install from ZIP file
wp plugin install /path/to/submittal-spec-sheet-builder.zip

# Or install from URL
wp plugin install https://yoursite.com/path/to/plugin.zip

# Activate plugin
wp plugin activate submittal-spec-sheet-builder

# Verify installation
wp plugin list | grep submittal
```

**Expected Output:**
```
submittal-spec-sheet-builder  active  1.2.1
```

---

## Server Requirements

Ensure your server meets these minimum requirements before installing.

### Minimum Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| **WordPress** | 6.1+ | Latest version |
| **PHP** | 7.4+ | 8.0+ |
| **MySQL** | 5.6+ | 5.7+ / MariaDB 10.3+ |
| **Memory** | 128 MB | 256 MB+ |
| **Upload Size** | 10 MB | 64 MB+ |
| **Execution Time** | 60 seconds | 120 seconds+ |

### PHP Extensions Required

The following PHP extensions must be enabled:

- ✅ **mbstring** - Required for PDF generation
- ✅ **gd** or **imagick** - Required for image processing
- ✅ **json** - Required for API functionality
- ✅ **dom** - Required for HTML parsing
- ✅ **libxml** - Required for XML processing

### How to Check Requirements

**Via WordPress:**
```
WordPress Admin → Tools → Site Health → Info → Server
```

**Via WP-CLI:**
```bash
wp core check-update
wp eval 'echo PHP_VERSION;'
```

**Via PHP:**
Create a file called `phpinfo.php` in your WordPress root:
```php
<?php phpinfo(); ?>
```
Visit `yoursite.com/phpinfo.php` to view server info.
**Important:** Delete this file after checking!

---

## Compatibility

### WordPress Versions

| WordPress Version | Status |
|-------------------|--------|
| 6.7+ (latest) | ✅ Fully compatible |
| 6.5 - 6.6 | ✅ Tested and compatible |
| 6.1 - 6.4 | ✅ Compatible |
| 6.0 and below | ❌ Not supported |

### PHP Versions

| PHP Version | Status |
|-------------|--------|
| 8.3 | ✅ Recommended |
| 8.2 | ✅ Recommended |
| 8.1 | ✅ Fully compatible |
| 8.0 | ✅ Compatible |
| 7.4 | ⚠️ Works but end-of-life |
| 7.3 and below | ❌ Not supported |

### Page Builders

The `[submittal_builder]` shortcode works with:

- ✅ **Gutenberg** (WordPress Block Editor)
- ✅ **Classic Editor**
- ✅ **Elementor** (Free & Pro)
- ✅ **Beaver Builder**
- ✅ **Divi Builder**
- ✅ **WPBakery** (Visual Composer)
- ✅ **Oxygen Builder**
- ✅ **Bricks Builder**

### Themes

Compatible with any properly coded WordPress theme, including:

- ✅ Default WordPress themes (Twenty Twenty-Five, etc.)
- ✅ Popular theme frameworks (Genesis, Avada, etc.)
- ✅ Custom themes
- ✅ Block themes (FSE)

### Hosting Providers

Tested and working on:

- ✅ **Managed WordPress Hosts** (WP Engine, Kinsta, Flywheel, Pressable)
- ✅ **Shared Hosting** (SiteGround, Bluehost, HostGator)
- ✅ **VPS/Cloud** (DigitalOcean, Linode, AWS, Google Cloud)
- ✅ **Local Development** (Local by Flywheel, XAMPP, MAMP, Docker)

---

## Post-Installation Setup

After successful installation, complete these initial setup steps.

### 1. Configure Branding (5 minutes)

```
Submittal Builder → Settings → Branding
```

**Required Settings:**
- Upload logo (PNG recommended, 300×100px)
- Set brand color (hex code or color picker)
- Add company name
- Add contact information

**Optional Settings:**
- Footer text
- Website URL
- Additional branding elements

### 2. Create Builder Page (2 minutes)

**Option A: Auto-Create (Easiest)**
```
Submittal Builder → Settings → Frontend
→ Click "Auto-Create Builder Page"
```

**Option B: Manual Creation**
```
Pages → Add New
→ Title: "Spec Sheet Builder" (or your choice)
→ Add block → Shortcode
→ Enter: [submittal_builder]
→ Publish
```

### 3. Add Test Products (5 minutes)

```
Submittal Builder → Catalog Builder
```

Follow the [Product Management Guide](./product-management.md) to add your first products.

**Quick Test Structure:**
- Add 1 category
- Add 1 product
- Add 1 type
- Add 2-3 models with specifications

### 4. Generate Test PDF (2 minutes)

1. Visit your builder page (frontend)
2. Select 2-3 test products
3. Click "Continue" or "VIEW →"
4. Add test project name
5. Click "Generate PDF"
6. Download and review PDF

**Check For:**
- Logo appears correctly
- Brand color applied
- All selected products included
- Specifications display properly

---

## Upgrading from Free to Pro

If you're upgrading from the free version to Pro or Agency license.

### Step-by-Step

1. **Install Pro Version**
   - Follow standard installation steps above
   - WordPress will recognize it as an update

2. **Enter License Key**
   ```
   Submittal Builder → Settings → License
   ```
   - Paste your license key
   - Click "Activate License"
   - Verify status shows "Active"

3. **Pro Features Unlocked**
   - Lead Capture
   - PDF Tracking
   - Auto Email
   - Server Drafts
   - White-Label Mode
   - Brand Themes
   - And more (see [Pro Features](./tracking.md))

4. **Your Data Remains**
   - All products preserved
   - Settings retained
   - Generated PDFs remain accessible

---

## Troubleshooting Installation

### "The plugin does not have a valid header"

**Cause:** Plugin ZIP file corrupted or incorrect folder structure.

**Solution:**
1. Re-download plugin ZIP from your account
2. Don't extract before uploading
3. Ensure ZIP contains plugin folder with correct structure

---

### "Upload: Failed to write file to disk"

**Cause:** Server permissions issue or disk space full.

**Solution:**
1. Check disk space on server
2. Verify `/wp-content/uploads/` folder permissions (755)
3. Contact hosting support if issue persists

---

### "Fatal error: Maximum execution time exceeded"

**Cause:** PHP execution time too low for plugin installation.

**Solution:**
1. Increase `max_execution_time` in php.ini to 120 seconds
2. Or use manual FTP installation method
3. Contact hosting support for assistance

---

### "The plugin requires PHP 7.4 or higher"

**Cause:** Server running outdated PHP version.

**Solution:**
1. Contact hosting provider to upgrade PHP
2. Or upgrade via hosting control panel (cPanel, Plesk)
3. Test site after upgrade to ensure compatibility

---

### Plugin installs but doesn't appear in menu

**Cause:** Permissions issue or theme conflict.

**Solution:**
1. Ensure you're logged in as Administrator
2. Try a different browser/clear cache
3. Temporarily switch to default WordPress theme
4. Deactivate other plugins to check for conflicts

---

## Multisite Installation

Special instructions for WordPress Multisite networks.

### Network Activation

To enable plugin across all sites:

```
Network Admin → Plugins → Installed Plugins
→ Network Activate "Submittal & Spec Sheet Builder"
```

### Per-Site Activation

To enable plugin on specific sites only:

```
Site Admin → Plugins → Installed Plugins
→ Activate "Submittal & Spec Sheet Builder"
```

**License Notes:**
- **Pro Single Site:** Activates on 1 site only
- **Pro Agency:** Activates on up to 5 sites
- Each site requires separate license activation

---

## Security Considerations

### File Permissions

After installation, verify permissions:

```
Plugin folder: 755
Plugin files: 644
Uploads folder: 755
Generated PDFs: 644 (temporary, auto-deleted)
```

### Security Hardening

**Recommended:**
- Keep WordPress, PHP, and plugin updated
- Use strong admin passwords
- Install security plugin (Wordfence, Sucuri, etc.)
- Enable HTTPS (SSL certificate)
- Regular backups

**Plugin Security:**
- All inputs sanitized and validated
- REST API endpoints use nonces and capabilities
- PDF files use temporary, non-guessable URLs
- No sensitive data stored in database

---

## Uninstallation

If you need to remove the plugin completely.

### Standard Removal

```
Plugins → Installed Plugins
→ Deactivate "Submittal & Spec Sheet Builder"
→ Delete
```

**What Gets Removed:**
- Plugin files
- Generated PDFs (temporary files auto-cleaned)

**What Remains:**
- Product catalog data (database)
- Settings (database)
- Lead capture data (database)

### Complete Removal (Including Data)

To remove all plugin data from database:

1. Deactivate and delete plugin (as above)
2. Run this SQL query in phpMyAdmin or WP-CLI:

```sql
DELETE FROM wp_options WHERE option_name LIKE 'sfb_%';
DELETE FROM wp_options WHERE option_name LIKE 'submittal_%';
```

**Warning:** This permanently deletes all products, settings, and leads.

---

## Next Steps

Now that installation is complete:

1. ✅ **Configure Branding** → [Branding Guide](./branding-pdfs.md)
2. ✅ **Add Products** → [Product Management](./product-management.md)
3. ✅ **Set Up Builder Page** → [User Guide](./user-guide.md)
4. ✅ **Customize Settings** → [Admin Settings](./admin-settings.md)

---

## Need Help?

**Support Channels:**

- **Documentation:** You're reading it!
- **WordPress.org Forum:** https://wordpress.org/support/plugin/submittal-builder/
- **Email Support:** support@webstuffguylabs.com (Pro users)

**Before Requesting Support:**
1. Check server requirements above
2. Review troubleshooting section
3. Test with default theme and no other plugins
4. Enable WordPress debug mode for error logs

---

[← Back to Documentation](./index.md) | [Next: User Guide →](./user-guide.md)

---

© 2025 Webstuffguy Labs. All rights reserved.
