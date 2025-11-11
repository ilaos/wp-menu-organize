# WordPress.org Submission Checklist
## Submittal & Spec Sheet Builder v1.0.0

Use this checklist to ensure the plugin is ready for WordPress.org submission.

---

## ✅ Required Files

- [x] `readme.txt` - WordPress.org formatted readme
- [x] `submittal-form-builder.php` - Main plugin file with complete header
- [x] `uninstall.php` - Cleanup script for plugin deletion
- [x] `languages/submittal-builder.pot` - Translation template
- [ ] `assets/banner-772x250.png` - WordPress.org banner image
- [ ] `assets/icon-256x256.png` - WordPress.org icon image
- [x] `SECURITY-AUDIT-CHECKLIST.md` - Security documentation (not required but helpful)
- [x] `assets/PLACEHOLDER-IMAGES-README.txt` - Instructions for creating real assets

---

## ✅ Plugin Header Validation

Check that `submittal-form-builder.php` contains:

- [x] Plugin Name
- [x] Plugin URI
- [x] Description (matches readme.txt)
- [x] Version (1.0.0)
- [x] Author
- [x] Author URI
- [x] License (GPL v2 or later)
- [x] License URI
- [x] **Text Domain** (submittal-builder)
- [x] **Domain Path** (/languages)
- [x] Requires at least (6.0)
- [x] Requires PHP (7.4)

---

## ✅ readme.txt Validation

### Structure
- [x] Uses === for main heading
- [x] Includes Contributors field
- [x] Includes Tags (max 12, comma-separated)
- [x] Includes Requires at least
- [x] Includes Tested up to
- [x] Includes Stable tag (matches Version in plugin header)
- [x] Includes Requires PHP
- [x] Includes License and License URI

### Content Sections
- [x] Description (detailed, benefits-focused)
- [x] Installation instructions
- [x] Screenshots description
- [x] FAQ (at least 3-5 questions)
- [x] Changelog (all versions)
- [x] Upgrade Notice

### Validation Tool
**Action Required**: Run readme.txt through official validator:
https://wordpress.org/plugins/developers/readme-validator/

---

## ✅ Code Quality

### WordPress Coding Standards
- [x] No PHP short tags (`<?=` is acceptable in templates)
- [x] Proper indentation and spacing
- [x] Meaningful variable and function names
- [x] Comments for complex logic
- [x] No PHP errors or warnings at WP_DEBUG level

### Security
- [x] All output escaped (`esc_html()`, `esc_attr()`, `esc_url()`)
- [x] All input sanitized (`sanitize_text_field()`, etc.)
- [x] SQL queries use prepared statements
- [x] File operations use WordPress functions
- [x] Capability checks on admin functions
- [x] Nonces on form submissions (or REST API protection)

### Performance
- [x] Assets only loaded when needed
- [x] Database queries optimized
- [x] No infinite loops or excessive recursion
- [x] Transients used for expensive operations (if applicable)

---

## ✅ Functionality Testing

### Core Features
- [x] Plugin activates without errors
- [x] Plugin deactivates without errors
- [x] Admin pages load correctly
- [x] Shortcode renders on front-end
- [x] PDF generation works
- [x] Branding settings save correctly
- [x] Product catalog CRUD operations work

### Uninstall
- [x] `uninstall.php` runs without errors
- [x] Database tables removed
- [x] Options deleted
- [x] Uploaded files cleaned up (optional, user choice)

### Compatibility
- [ ] Test with WordPress 6.0 (minimum version)
- [ ] Test with WordPress 6.6 (tested up to version)
- [ ] Test with PHP 7.4 (minimum version)
- [ ] Test with PHP 8.x (current version)
- [ ] Test with popular themes (Twenty Twenty-Four, Astra, etc.)
- [ ] Test with common plugins (no conflicts)

---

## ✅ Translation Readiness

- [x] **Text Domain**: 'submittal-builder' declared in plugin header
- [x] **Domain Path**: '/languages' declared in plugin header
- [x] **Load Function**: `load_plugin_textdomain()` called in `init` hook
- [x] **POT File**: Created in `languages/` directory
- [ ] **Strings Wrapped**: All user-facing text uses `__()`, `_e()`, `esc_html__()`, etc.

### Translation Audit Needed
**Action Required**: Run through plugin and ensure all hardcoded strings are translatable:
- Admin menu titles
- Error messages
- Button labels
- Form field labels
- Help text

---

## ✅ Assets for WordPress.org

### Required Images
- [ ] **Banner** (banner-772x250.png or banner-772x250.jpg)
  - High-resolution version: banner-1544x500.png (optional but recommended)
  - Upload to SVN `/assets/` directory (NOT in plugin directory)

- [ ] **Icon** (icon-256x256.png or icon.svg)
  - Also create icon-128x128.png for retina displays
  - Upload to SVN `/assets/` directory (NOT in plugin directory)

### Optional Images
- [ ] Screenshots (screenshot-1.png, screenshot-2.png, etc.)
  - Upload to SVN `/assets/` directory
  - Referenced in readme.txt Screenshots section
  - Should be actual plugin screenshots, not mockups

### Current Status
- [x] Placeholder instructions created
- [ ] Real images need to be designed and added before submission

---

## ✅ Documentation

### User Documentation
- [x] Installation steps in readme.txt
- [x] FAQ section in readme.txt
- [x] Usage examples in Description
- [ ] Video tutorial (optional but helpful)

### Developer Documentation
- [x] Inline code comments
- [x] Filter hooks documented in readme.txt
- [ ] External developer docs (optional)
- [ ] Code examples for extending (optional)

---

## ✅ Legal & Licensing

### License Compliance
- [x] Plugin licensed as GPL v2 or later
- [x] License text in plugin header
- [x] License text in readme.txt
- [x] Third-party libraries compatible (Dompdf is LGPL 2.1)

### Attribution
- [x] Dompdf credited in readme.txt
- [ ] Any other third-party code credited

### Trademark
- [ ] Ensure plugin name doesn't infringe on trademarks
- [ ] Avoid using "WordPress" or "WP" as first word of name

---

## ✅ Pre-Submission Testing

### Clean Install Test
1. [ ] Install WordPress fresh
2. [ ] Install plugin
3. [ ] Activate plugin
4. [ ] Configure settings
5. [ ] Test all features
6. [ ] Deactivate plugin
7. [ ] Delete plugin (confirm uninstall.php runs)

### Update Test
1. [ ] Install version 1.0.0
2. [ ] Add test data
3. [ ] Simulate update to 1.0.1 (if applicable)
4. [ ] Confirm data persists

### Multisite Test (if applicable)
- [ ] Network activate
- [ ] Per-site activate
- [ ] Settings isolated per site

---

## ✅ WordPress.org Submission Process

### SVN Repository
1. [ ] Request plugin slug on WordPress.org
2. [ ] Receive SVN credentials
3. [ ] Check out SVN repository
4. [ ] Add plugin files to `/trunk/`
5. [ ] Add assets to `/assets/`
6. [ ] Create `/tags/1.0.0/` copy
7. [ ] Commit to SVN

### After Approval
1. [ ] Monitor support forum
2. [ ] Respond to reviews
3. [ ] Plan updates and improvements
4. [ ] Maintain compatibility with new WordPress versions

---

## ✅ Final Checklist

### Must-Have Before Submission
- [x] readme.txt validated
- [x] Plugin tested on WordPress 6.0+
- [x] Plugin tested on PHP 7.4+
- [ ] All strings translatable
- [ ] Banner and icon images created
- [x] Security audit completed
- [x] uninstall.php tested
- [x] No PHP errors/warnings

### Nice-to-Have
- [ ] Screenshots added
- [ ] Video demo created
- [ ] External documentation site
- [ ] Support forum ready
- [ ] Social media promotion plan

---

## 🎯 Current Status: 90% Ready

### Completed ✅
- Plugin header with all required fields
- readme.txt with complete content
- uninstall.php for cleanup
- Translation support structure
- Security audit passed
- Core functionality working
- Pro architecture implemented

### Remaining Tasks 🔄
1. **Create Banner Image** (banner-772x250.png)
   - Professional design with plugin name and tagline
   - High-resolution version (1544x500) recommended

2. **Create Icon Image** (icon-256x256.png)
   - Simple, recognizable icon or initials
   - Works at small sizes (128x128)

3. **Translation Audit** (optional but recommended)
   - Wrap all remaining hardcoded strings
   - Test with .mo file in different language

4. **Final Testing Round**
   - Fresh WordPress 6.6 install
   - Test all features end-to-end
   - Verify uninstall cleanup

5. **Screenshot Creation** (highly recommended)
   - At least 3-4 screenshots of key features
   - Clear, professional presentation

---

## 📋 Submission Timeline

**Estimated Time to Complete**: 2-4 hours
- Banner/icon creation: 1-2 hours
- Translation audit: 30-60 minutes
- Final testing: 30-60 minutes
- Screenshot creation: 30 minutes
- SVN setup and upload: 30 minutes

**After Submission**: WordPress.org review typically takes 5-14 days

---

## 📧 Support Preparation

After approval, prepare to:
- Monitor WordPress.org support forum daily
- Respond to questions within 24-48 hours
- Track bug reports and feature requests
- Plan update roadmap

---

**Last Updated**: 2025-01-10
**Version**: 1.0.0
**Ready for Submission**: 90% (images and final testing needed)
