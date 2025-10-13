# Production Ready Checklist - v1.0.0

## ✅ Completed

### 1. Version & Constants
- ✅ Plugin header updated to `Version: 1.0.0` (line 6)
- ✅ `SFB_Plugin::VERSION` constant set to `1.0.0` (line 78)
- ✅ Matches readme.txt `Stable tag: 1.0.0`

### 2. Dev Flags Removed
- ✅ All `SFB_DEV_MODE` checks disabled/removed
- ✅ All `SFB_AGENCY_DEV` checks removed
- ✅ Demo Tools menu hidden in production (line 479: `if (false)`)

### 3. Agency Features - Default OFF
- ✅ Weekly Lead Export: `get_option('sfb_lead_weekly_export_enabled', false)` - OFF by default
- ✅ Lead Routing: `get_option('sfb_lead_routing_enabled', false)` - OFF by default
- ✅ Analytics: Agency license required, no auto-opt-in

### 4. Security
- ✅ All AJAX endpoints have nonce verification
- ✅ All admin actions require `manage_options` capability
- ✅ Webhook URLs validated for HTTPS-only
- ✅ Email addresses validated with `is_email()`
- ✅ Input sanitization: `sanitize_text_field()`, `esc_url_raw()`
- ✅ Output escaping: `esc_html()`, `esc_attr()`, `wp_json_encode()`

### 5. Text Domain
- ✅ Plugin header: `Text Domain: submittal-builder`
- ✅ Domain Path: `/languages`
- ✅ Consistent usage throughout: `'submittal-builder'`

### 6. Privacy
- ✅ Analytics opt-out filter documented: `add_filter('sfb_enable_remote_analytics', '__return_false')`
- ✅ Webhook privacy: only configured fields sent
- ✅ readme.txt Privacy section complete

---

## ⚠️ Remaining Tasks

### 1. Console Logs - REQUIRES MANUAL CLEANUP

**Critical Files with console.log/warn/error:**

#### `assets/admin.js` (50+ instances)
- **Keep for production** (error handling):
  - Line 22: `console.error('[SFB] Auth/nonce error:'` - User needs to see this
  - Line 198, 225, 234: Field/collapse errors - debugging OK

- **REMOVE or COMMENT OUT** (debug noise):
  - Line 1647: `console.log('[SFB] toggleCollapse'` - debug only
  - Line 1673: `console.log('[SFB] collapseAll'` - debug only
  - Line 1679: `console.log('[SFB] expandAll'` - debug only
  - Line 1698: `console.log('[SFB] collapseByType'` - debug only
  - Line 1706: `console.log('[SFB] drag-drop'` - debug only
  - Line 3277: `console.log('[SFB] Initializing branding'` - debug only
  - Line 3385: `console.log('[SFB] Preview updated'` - debug only
  - Line 3404: `console.log('[SFB] Autosaving brand'` - debug only
  - Line 3436: `console.log('[SFB] Brand settings saved'` - debug only
  - Line 3472: `console.log('[SFB] Applying preset'` - debug only
  - Line 3649: `console.log('[SFB] Branding panel initialized'` - debug only

**Strategy:** Either:
1. **Comment out** debug console.log lines (keep error/warn for production debugging)
2. **Remove entirely** if not needed
3. **Wrap in DEV check**: `if (window.SFB_DEBUG) console.log(...)`

#### Other JS Files:
- `assets/js/review.js` - Check for console logs
- `assets/js/lead-capture.js` - Check for console logs
- `assets/js/frontend.js` - Check for console logs

**Command to find all:**
```bash
grep -rn "console\.\(log\|warn\|info\|debug\)" assets/*.js assets/js/*.js
```

---

### 2. PHP error_log Cleanup

**Current usage:**
- `submittal-form-builder.php` line 5185: `error_log('[SFB] Weekly export skipped')` - **KEEP** (important)
- `agency-lead-routing.php` lines 420, 480, 498: `error_log('SFB Lead Routing:')` - **KEEP** (webhook debugging)

**Decision:** Keep production error_log for critical failures (webhooks, cron). Remove verbose debug logs.

---

### 3. Text Domain - i18n Setup

**Verify load_plugin_textdomain():**

Check if this exists in `submittal-form-builder.php`:
```php
add_action('plugins_loaded', function() {
  load_plugin_textdomain('submittal-builder', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
```

If missing, ADD IT around line 80-90.

**Generate POT file:**
```bash
wp i18n make-pot . languages/submittal-builder.pot
```

Or use Poedit/Loco Translate to generate `languages/submittal-builder.pot`.

---

### 4. Performance - Cron Cleanup

**Add to `uninstall.php` or deactivation hook:**

```php
// Unschedule all cron events on deactivation
register_deactivation_hook(__FILE__, function() {
  wp_clear_scheduled_hook('sfb_weekly_lead_export');
  wp_clear_scheduled_hook('sfb_retry_webhook_delivery');
  wp_clear_scheduled_hook('sfb_analytics_heartbeat');
});
```

**Check existing uninstall.php:**
- Verify it removes ALL custom tables
- Verify it removes ALL options (or respects "keep data" setting)
- Add cron cleanup

---

### 5. Uninstall Strategy

**Current approach:** Check `uninstall.php` for:
- Option: `sfb_delete_data_on_uninstall` (checkbox in Settings)
- If enabled: delete tables, options, uploads/sfb/ directory
- If disabled: keep data

**Recommendation:** Add Settings checkbox under Tools → Advanced:
```php
<label>
  <input type="checkbox" name="sfb_delete_data_on_uninstall" <?php checked(get_option('sfb_delete_data_on_uninstall', false)); ?>>
  Delete all data on uninstall (cannot be undone)
</label>
```

---

### 6. Database Tables - Activation Safety

**Verify activation hook** doesn't fatal on re-run:
- Use `CREATE TABLE IF NOT EXISTS`
- Use `dbDelta()` for upgrades
- Add version check: `get_option('sfb_db_version')` vs `SFB_Plugin::VERSION`

Check around line 8000-9000 for `register_activation_hook()`.

---

### 7. Final Smoke Test (Manual)

**Builder Flow:**
- [ ] Select products → Review step (specs show)
- [ ] Generate PDF → Downloads correctly
- [ ] PDF has proper branding (logo, colors, footer)

**Leads:**
- [ ] Submit lead form → Appears in admin list
- [ ] CSV export works
- [ ] Lead capture modal respects white-label

**Agency Features (with Agency license):**
- [ ] Weekly export: "Send now" → Email arrives with CSV
- [ ] Agency Library: Save Pack → Export JSON → Seed pack on new site
- [ ] Brand Presets: Default-to-PDF toggle works; Review switcher shows preset
- [ ] White-Label: ON/OFF reflected in PDFs and emails
- [ ] Client Handoff: Operator role enforced; agency menus hidden
- [ ] Analytics: Counts show; heartbeat works
- [ ] Lead Routing: Rule match → Email/webhook fire → Log entry → Retry on failure

**Permissions:**
- [ ] Non-admin cannot access Builder admin
- [ ] Operator can access Builder but not Agency settings (if handoff mode ON)

---

### 8. WordPress.org Assets

**Required in `/assets/` (SVN root, NOT trunk):**

1. **Icons:**
   - `icon-128x128.png`
   - `icon-256x256.png`
   - Simple "S" monogram + document/sheet icon
   - High contrast, clean design

2. **Banners:**
   - `banner-772x250.png` (standard resolution)
   - `banner-1544x500.png` (retina 2x)
   - Left: Builder UI screenshot
   - Right: PDF preview + tagline "Create submittals fast"
   - No text overload, professional look

**Mockup ideas:**
- Color scheme: Blue (#4f46e5) + white
- Font: Bold sans-serif for tagline
- Screenshots: Actual plugin UI (anonymized)

---

### 9. Coding Standards (Optional)

Run PHPCS with WordPress ruleset:
```bash
phpcs --standard=WordPress submittal-form-builder.php Includes/
```

Common issues to fix:
- Indentation (tabs vs spaces)
- Line length (max 80-120 chars)
- Yoda conditions (`$value === $var` instead of `$var === $value`)
- phpDoc blocks for all public methods

Run Plugin Check plugin:
- Install from wordpress.org/plugins/plugin-check
- Activate and scan "Submittal & Spec Sheet Builder"
- Fix any ERROR or WARNING issues

---

### 10. SVN Submission Steps

1. **Create plugin at wordpress.org**
   - Choose slug: `submittal-spec-builder` (matches text domain)
   - Wait for SVN access email

2. **Checkout SVN repo:**
   ```bash
   svn co https://plugins.svn.wordpress.org/submittal-spec-builder
   cd submittal-spec-builder
   ```

3. **Add trunk files:**
   ```bash
   cp -r /path/to/plugin/* trunk/
   svn add trunk/*
   ```

4. **Add assets:**
   ```bash
   cp /path/to/icons/* assets/
   cp /path/to/banners/* assets/
   svn add assets/*
   ```

5. **Commit trunk:**
   ```bash
   svn ci -m "Initial commit: v1.0.0 - Submittal & Spec Sheet Builder with Agency features"
   ```

6. **Tag release:**
   ```bash
   svn cp trunk tags/1.0.0
   svn ci -m "Tagging version 1.0.0"
   ```

7. **Wait for build** (15-30 minutes)
   - Check https://wordpress.org/plugins/submittal-spec-builder/
   - Verify listing renders correctly

---

### 11. Post-Launch

**Welcome Notice:**
Add to `submittal-form-builder.php` around line 500:
```php
add_action('admin_notices', function() {
  if (!get_option('sfb_welcome_dismissed') && current_user_can('manage_options')) {
    echo '<div class="notice notice-info is-dismissible">';
    echo '<p><strong>Welcome to Submittal & Spec Sheet Builder!</strong></p>';
    echo '<p>Get started: <a href="' . admin_url('admin.php?page=sfb-branding') . '">Set up branding</a> | ';
    echo '<a href="' . admin_url('admin.php?page=sfb') . '">Build catalog</a> | ';
    echo '<a href="https://example.com/docs" target="_blank">View docs</a></p>';
    echo '</div>';
  }
});

// Dismiss notice
add_action('wp_ajax_sfb_dismiss_welcome', function() {
  update_option('sfb_welcome_dismissed', true);
  wp_die();
});
```

**Plugin row meta:**
Add links in plugin list:
```php
add_filter('plugin_row_meta', function($links, $file) {
  if (strpos($file, 'submittal-form-builder.php') !== false) {
    $links[] = '<a href="https://example.com/docs" target="_blank">Docs</a>';
    $links[] = '<a href="https://example.com/support" target="_blank">Support</a>';
  }
  return $links;
}, 10, 2);
```

---

## Summary

**Ready for Production:**
- ✅ Version 1.0.0
- ✅ Dev flags removed
- ✅ Agency features OFF by default
- ✅ Security hardened

**Before Submitting:**
1. Clean up console.log statements (see section 1)
2. Verify i18n setup + generate POT file
3. Add cron cleanup to deactivation hook
4. Run final smoke tests (section 7)
5. Create WordPress.org assets (icons + banners)
6. Run PHPCS + Plugin Check (optional but recommended)

**Estimated time:** 2-4 hours for remaining cleanup + testing.

---

## Quick Commands Reference

```bash
# Find all console logs
grep -rn "console\.\(log\|warn\|info\|debug\)" assets/*.js assets/js/*.js

# Generate POT file
wp i18n make-pot . languages/submittal-builder.pot

# Run PHPCS
phpcs --standard=WordPress submittal-form-builder.php Includes/

# SVN checkout
svn co https://plugins.svn.wordpress.org/submittal-spec-builder

# SVN commit
svn ci -m "Initial commit: v1.0.0"

# SVN tag
svn cp trunk tags/1.0.0
svn ci -m "Tagging version 1.0.0"
```
