# Pre-Launch QA Testing Checklist

## Overview
This checklist ensures the Submittal Builder plugin is ready for WordPress.org submission. Complete all sections before tagging version 1.0.0.

---

## 1. Functional Testing

### PDF Generation - All Three Presets

#### ✅ Technical Preset
- [ ] Navigate to Builder → Select 3-5 products
- [ ] Generate PDF with "Technical" layout
- [ ] **Verify:**
  - [ ] Cover page displays with company logo/name
  - [ ] Product sheets show title, breadcrumb, specs table
  - [ ] No summary or TOC pages (technical mode skips these)
  - [ ] Page numbers appear in footer
  - [ ] PDF downloads successfully

#### ✅ Branded Preset
- [ ] Generate PDF with "Branded" layout (same as Technical for Free version)
- [ ] **Verify:**
  - [ ] Cover page with branding
  - [ ] Product sheets with specs
  - [ ] No Pro-only features triggered
  - [ ] PDF downloads successfully

#### ✅ Packet Preset
- [ ] Generate PDF with "Packet" layout
- [ ] **Verify:**
  - [ ] Cover page displays
  - [ ] Summary page groups products by `path[0]` (top-level category)
  - [ ] Table of Contents lists all products
  - [ ] Product detail sheets follow TOC
  - [ ] Page numbers show "Page X of Y" format
  - [ ] PDF downloads successfully

---

### TOC Anchor Links & Page Numbers

- [ ] Generate Packet PDF with 5+ products
- [ ] Open in Adobe Acrobat or browser PDF viewer
- [ ] **Test TOC Links:**
  - [ ] Click on first TOC link → jumps to product sheet
  - [ ] Click on third TOC link → jumps to correct product
  - [ ] All anchor IDs (`prod-{id}`) are unique
- [ ] **Test Page Numbers:**
  - [ ] Footer shows "Page 1 of 8" (or actual count)
  - [ ] Page numbers increment correctly on each page
  - [ ] Last page shows correct total (e.g., "Page 8 of 8")

---

### Summary Page Grouping

- [ ] Add products across 3+ categories (e.g., Framing, Fasteners, Insulation)
- [ ] Generate Packet PDF
- [ ] **Verify Summary Page:**
  - [ ] Products grouped by `path[0]` (first category segment)
  - [ ] Each group shows product count (e.g., "Framing (4)")
  - [ ] Key specs displayed (Size, Thickness, Flange, KSI)
  - [ ] Missing specs show "No specifications available" (not errors)
  - [ ] No duplicate groups

---

### Free Mode - Pro Feature Handling

- [ ] Ensure `SFB_PRO_DEV` constant is NOT defined
- [ ] Attempt to use Pro features in admin:
  - [ ] Email Delivery checkbox (should be disabled or hidden)
  - [ ] Tracking Links (should show upgrade prompt)
  - [ ] Custom Watermarks (Free should only allow text, not graphics)
  - [ ] Approval Blocks (should be disabled)
- [ ] **Verify:**
  - [ ] No PHP errors or warnings
  - [ ] Pro features gracefully disabled (no JS console errors)
  - [ ] "Upgrade to Pro" messages appear where appropriate
  - [ ] PDF generation still works (Pro fields ignored)

---

### Pro Dev Flag Test

- [ ] Add `define('SFB_PRO_DEV', true);` to `wp-config.php`
- [ ] Refresh admin page
- [ ] **Verify:**
  - [ ] Pro features now unlocked (email, tracking, watermarks, themes)
  - [ ] No errors when saving Pro settings
  - [ ] PDF generates with Pro features (custom theme, watermark)
- [ ] Remove `SFB_PRO_DEV` definition and test again (should revert to Free)

---

## 2. Compatibility Testing

### WordPress Versions

Test on **local environments** or **staging servers** (not production):

- [ ] **WordPress 6.0** (minimum required)
  - [ ] Install and activate plugin
  - [ ] Generate PDF successfully
  - [ ] No fatal errors in debug log
- [ ] **WordPress 6.3** (mid-range)
  - [ ] Same tests as above
- [ ] **WordPress 6.6** (latest tested)
  - [ ] Same tests as above

**Tools:**
- Use **Local by Flywheel** or **Docker** to test multiple WP versions
- Enable `WP_DEBUG` and `WP_DEBUG_LOG` in `wp-config.php`

---

### PHP Versions

Test plugin on different PHP versions:

- [ ] **PHP 7.4** (minimum required)
  - [ ] Activate plugin → No deprecation warnings
  - [ ] Generate PDF → Success
- [ ] **PHP 8.0**
  - [ ] No null parameter warnings (we fixed these!)
  - [ ] PDF generation works
- [ ] **PHP 8.1**
  - [ ] No "Passing null to parameter" errors
  - [ ] All features functional
- [ ] **PHP 8.2**
  - [ ] Same tests as 8.1

**How to Test:**
- Use **XAMPP**, **MAMP**, or **Docker** with different PHP versions
- Or use hosting panel (cPanel/Plesk) PHP version switcher

---

### Hosting Environments

Test on at least **two hosting types**:

- [ ] **LiteSpeed Server** (common shared hosting)
  - [ ] Install plugin
  - [ ] Upload sample logo via Branding page
  - [ ] Generate PDF → Verify download works
  - [ ] Check for `.htaccess` conflicts
- [ ] **Nginx/Apache Server**
  - [ ] Same tests as LiteSpeed
  - [ ] Test with and without pretty permalinks (see below)

---

### Pretty Permalinks (URL Rewriting)

- [ ] **With Pretty Permalinks:**
  - [ ] Go to Settings → Permalinks
  - [ ] Select "Post name" (enables pretty URLs)
  - [ ] Generate tracking link (if Pro feature active)
  - [ ] Verify link redirects correctly
- [ ] **Without Pretty Permalinks (Default):**
  - [ ] Select "Plain" permalinks (`?p=123`)
  - [ ] Generate PDF → Verify download
  - [ ] Ensure no 404 errors on admin pages

---

## 3. Security & Cleanup

### Activation & Onboarding

- [ ] **Fresh Install:**
  - [ ] Deactivate plugin (if active)
  - [ ] Delete plugin files
  - [ ] Re-install from ZIP
  - [ ] **On Activation:**
    - [ ] Onboarding page opens automatically
    - [ ] Welcome notice shows on admin pages
    - [ ] No PHP errors or warnings
    - [ ] Database options created (`sfb_branding`, `sfb_onboarding_shown`)

---

### Save Settings - No Notices

- [ ] Navigate to Branding page
- [ ] Fill in all fields (logo, colors, contact info)
- [ ] Click "Save Branding"
- [ ] **Verify:**
  - [ ] Success notice appears ("Settings saved")
  - [ ] No PHP notices/warnings
  - [ ] Settings persist after page reload
  - [ ] Logo upload works (file saved to `/uploads/`)

---

### Uninstall - Data Cleanup

- [ ] **Test Uninstall Script:**
  - [ ] Add some branding settings
  - [ ] Generate 2-3 PDFs (saved to `/uploads/sfb/`)
  - [ ] Deactivate plugin
  - [ ] **Delete** plugin (triggers `uninstall.php`)
  - [ ] **Verify:**
    - [ ] `sfb_*` options removed from database:
      - [ ] `SELECT * FROM wp_options WHERE option_name LIKE 'sfb_%'` → Returns 0 rows
    - [ ] `/wp-content/uploads/sfb/` directory deleted
    - [ ] No orphaned files in uploads

**Manual Check:**
```sql
-- Run in phpMyAdmin or DB tool
SELECT * FROM wp_options WHERE option_name LIKE 'sfb_%';
```
Should return **empty** after uninstall.

---

### PHP Error Log

- [ ] Enable debugging in `wp-config.php`:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
  ```
- [ ] Use plugin for 10 minutes (navigate pages, generate PDFs, save settings)
- [ ] Check `/wp-content/debug.log` for errors
- [ ] **Verify:**
  - [ ] No `E_ERROR`, `E_WARNING`, or `E_NOTICE` from plugin
  - [ ] No "Deprecated" messages
  - [ ] No "Undefined variable" errors

---

### Site Health Check

- [ ] Go to **Tools → Site Health**
- [ ] Review **Info** tab
- [ ] **Verify:**
  - [ ] No critical issues related to plugin
  - [ ] PHP version shows 7.4+ (meets requirement)
  - [ ] No file permission warnings for `/uploads/sfb/`

---

## 4. Readme & Validation

### Readme.txt Validation

- [ ] Copy contents of `readme.txt`
- [ ] Go to: https://wordpress.org/plugins/developers/readme-validator/
- [ ] Paste and validate
- [ ] **Fix all errors:**
  - [ ] "Stable tag" format correct (e.g., `1.0.0`)
  - [ ] "Tested up to" version valid (e.g., `6.6`)
  - [ ] "Requires at least" and "Requires PHP" set
  - [ ] Screenshot captions numbered correctly
  - [ ] Changelog section exists
  - [ ] No HTML tags (use Markdown instead)

---

### Plugin Header Check

- [ ] Open `submittal-form-builder.php`
- [ ] Verify plugin header:
  ```php
  /**
   * Plugin Name: Submittal & Spec Sheet Builder
   * Text Domain: submittal-builder
   * Domain Path: /languages
   * Version: 1.0.0
   * Requires at least: 6.0
   * Requires PHP: 7.4
   */
  ```
- [ ] **Confirm:**
  - [ ] `Text Domain` matches slug (`submittal-builder`)
  - [ ] `Domain Path` is `/languages`
  - [ ] `Version` matches `readme.txt` → `Stable tag`

---

### Translation Readiness

- [ ] Verify `.pot` file exists: `languages/submittal-builder.pot`
- [ ] Check key strings are translatable:
  - [ ] Admin page titles use `__()` or `esc_html_e()`
  - [ ] Form labels use `esc_html__()` or `esc_attr__()`
  - [ ] All user-facing text has 'submittal-builder' text domain
- [ ] **Manual Spot Check:**
  ```php
  // GOOD:
  esc_html_e('Ready to upgrade?', 'submittal-builder');

  // BAD (missing text domain):
  esc_html_e('Ready to upgrade?');
  ```

---

## 5. SVN Preparation

### File Structure

- [ ] Ensure plugin directory structure is clean:
  ```
  submittal-builder/
  ├── submittal-form-builder.php
  ├── readme.txt
  ├── uninstall.php
  ├── assets/ (CSS, JS only)
  ├── includes/
  ├── languages/
  ├── templates/
  └── vendor/
  ```
- [ ] **Exclude from SVN trunk:**
  - [ ] `.git/`, `.gitignore`
  - [ ] `node_modules/`
  - [ ] `.DS_Store`, `Thumbs.db`
  - [ ] Development docs (this QA checklist, design specs)
  - [ ] `composer.json`, `package.json` (unless needed)

---

### Assets Ready for SVN

- [ ] Banner: `assets/banner-772x250.png` exists and optimized (<100KB)
- [ ] Icon: `assets/icon-256x256.png` exists and optimized (<50KB)
- [ ] Screenshots: `assets/screenshot-1.png` through `screenshot-6.png` exist
- [ ] All images are PNG format (not JPG)
- [ ] Filenames match exactly (lowercase, no spaces)

---

### Version Consistency

- [ ] Plugin header `Version:` = `1.0.0`
- [ ] `readme.txt` → `Stable tag:` = `1.0.0`
- [ ] Changelog in `readme.txt` lists version `1.0.0`
- [ ] SVN tag will be `tags/1.0.0/` (matching version)

---

## 6. Final Pre-Launch Checks

### User Experience

- [ ] Install plugin as a first-time user
- [ ] Follow onboarding flow
- [ ] **Can you:**
  - [ ] Add a category and product in <5 minutes?
  - [ ] Upload a logo and set branding in <3 minutes?
  - [ ] Generate a PDF successfully on first try?
- [ ] **Are error messages helpful?**
  - [ ] If DOMPDF missing → Clear instructions
  - [ ] If logo upload fails → Helpful error message

---

### Performance

- [ ] Generate PDF with 10 products
- [ ] **Time from "Generate" click to download:**
  - [ ] Should be <10 seconds on average hosting
  - [ ] No timeouts (30-second PHP limit)
- [ ] Check memory usage (enable `WP_DEBUG`):
  - [ ] No "Memory exhausted" errors
  - [ ] PDF generation stays under 128MB memory limit

---

### Accessibility (Basic Check)

- [ ] Admin forms use proper `<label>` tags
- [ ] Color contrast sufficient (text readable on backgrounds)
- [ ] Keyboard navigation works (Tab through form fields)
- [ ] No reliance on color alone (use text labels + icons)

---

### Documentation

- [ ] `readme.txt` has complete installation instructions
- [ ] FAQ section answers common questions (at least 3-5 FAQs)
- [ ] Changelog shows version 1.0.0 with "Initial release" note
- [ ] External links in readme.txt work (not 404)

---

## 7. Compatibility Matrix Summary

Fill in after testing:

| Environment | WP Version | PHP Version | Server | Permalink | Result |
|-------------|-----------|-------------|--------|-----------|--------|
| Local | 6.0 | 7.4 | Apache | Plain | ✅ Pass |
| Local | 6.3 | 8.0 | Nginx | Pretty | ✅ Pass |
| Local | 6.6 | 8.1 | Apache | Pretty | ✅ Pass |
| Staging | 6.6 | 8.2 | LiteSpeed | Pretty | ✅ Pass |

**Minimum to Pass:** Test on at least **2 environments** with different PHP versions (7.4 and 8.1+).

---

## 8. WordPress.org Submission Checklist

Before submitting to WordPress.org:

- [ ] Plugin tested on WordPress 6.0+ and PHP 7.4+
- [ ] No GPL-incompatible code or libraries
- [ ] No "phone home" or external API calls (except DOMPDF CDN fonts)
- [ ] No obfuscated code or encrypted files
- [ ] Plugin slug is unique (`submittal-builder`)
- [ ] readme.txt passes validator
- [ ] Screenshots reference in readme.txt
- [ ] All code follows WordPress Coding Standards (80%+ compliance)
- [ ] User data sanitized (use `sanitize_text_field()`, etc.)
- [ ] Output escaped (use `esc_html()`, `esc_attr()`, etc.)
- [ ] Database queries use `$wpdb->prepare()` (if applicable)

---

## 9. Known Issues / Warnings

Document any known issues to address in future versions:

- [ ] **Issue:** None identified ✅
- [ ] **Workaround:** N/A

*Add any issues found during testing here for transparency in changelog.*

---

## 10. Sign-Off

**Tested By:** ____________________
**Date:** ____________________
**WordPress Version:** ____________________
**PHP Version:** ____________________
**Server:** ____________________

**Final Approval:**
- [ ] All functional tests passed
- [ ] Compatibility verified (2+ environments)
- [ ] Security checks completed (no errors, clean uninstall)
- [ ] Readme validated
- [ ] Assets ready for SVN
- [ ] Ready for WordPress.org submission 🚀

---

## Resources

- **WordPress.org Plugin Handbook:** https://developer.wordpress.org/plugins/
- **Plugin Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- **Readme Validator:** https://wordpress.org/plugins/developers/readme-validator/
- **SVN Guide:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Plugin Review Queue:** https://wordpress.org/plugins/developers/

---

**Next Steps After QA:**
1. Address any failing checklist items
2. Create banner, icon, and screenshots
3. Tag version 1.0.0 in SVN
4. Submit plugin to WordPress.org
5. Monitor review queue (typically 1-2 weeks)
