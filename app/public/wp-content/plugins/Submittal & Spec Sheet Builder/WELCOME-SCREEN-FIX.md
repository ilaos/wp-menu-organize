# Welcome Screen Polish - CSS & Logo Fix

## Issue Summary

The Welcome screen wasn't displaying the new layout (grid/cards) and the Webstuffguy Labs logo wasn't showing. Root causes:
1. CSS wasn't reliably enqueued on the welcome page (screen ID mismatch)
2. Logo URL used unreliable path resolution
3. Template path resolution wasn't using stable constant

## Fixes Applied

### 1. Defined Plugin File Constant
**File:** `submittal-form-builder.php` (lines 19-22)

Added `SFB_PLUGIN_FILE` constant at plugin initialization for reliable path resolution:

```php
if (!defined('SFB_PLUGIN_FILE')) {
  define('SFB_PLUGIN_FILE', __FILE__);
}
```

### 2. Fixed Admin CSS Enqueue
**File:** `submittal-form-builder.php` (lines 7381-7407)

**Before:** Used `$_GET['page']` parameter checking, which could miss pages
**After:** Uses `get_current_screen()->id` for reliable screen detection

Key changes:
- Uses `get_current_screen()` to detect any plugin page
- Checks if screen ID contains 'sfb' or 'submittal-builder'
- Uses `SFB_PLUGIN_FILE` constant for CSS path
- Changed CSS path from `assets/admin.css` to `assets/css/admin.css`
- Only loads React dependencies on pages that need them

```php
function enqueue_admin($hook) {
  $screen = get_current_screen();
  if (!$screen) return;

  $is_sfb_page = (strpos($screen->id, 'sfb') !== false ||
                  strpos($screen->id, 'submittal-builder') !== false);
  if (!$is_sfb_page) return;

  // ... load assets with SFB_PLUGIN_FILE constant
}
```

### 3. Fixed Logo URL
**File:** `templates/admin/welcome.php` (lines 11-21)

**Before:** Used `plugin_dir_url(dirname(__FILE__, 2))` - unreliable
**After:** Uses `plugins_url()` with `SFB_PLUGIN_FILE` constant

```php
$logo_files = ['webstuffguy-labs.png', 'custom-wordpress-plugins-1.png'];
$logo_url = '';
foreach ($logo_files as $logo_file) {
  $logo_path = plugin_dir_path(SFB_PLUGIN_FILE) . 'assets/img/' . $logo_file;
  if (file_exists($logo_path)) {
    $logo_url = plugins_url('assets/img/' . $logo_file, SFB_PLUGIN_FILE);
    break;
  }
}
```

### 4. Improved Template Loading
**File:** `submittal-form-builder.php` (lines 3789-3806)

**Before:** Basic file_exists check with `__FILE__`
**After:** Uses `SFB_PLUGIN_FILE` constant with error handling

```php
function render_onboarding_page() {
  $welcome_template = plugin_dir_path(SFB_PLUGIN_FILE) . 'templates/admin/welcome.php';
  $onboarding_template = plugin_dir_path(SFB_PLUGIN_FILE) . 'templates/admin/onboarding.php';

  if (file_exists($welcome_template)) {
    include $welcome_template;
  } elseif (file_exists($onboarding_template)) {
    include $onboarding_template;
  } else {
    echo '<div class="notice notice-error">...</div>';
  }
}
```

### 5. Added Debug Marker
**File:** `templates/admin/welcome.php` (line 44)

Temporary HTML comment to verify template is loading:
```html
<!-- SFB: welcome.php loaded (remove after verifying) -->
```

**Action Required:** Remove this comment after confirming the page loads correctly.

## Testing Checklist

### Before Testing
1. Clear browser cache (Ctrl+Shift+Delete)
2. If using cache plugin (SG Cache visible in toolbar), purge it
3. Hard refresh page (Ctrl+F5)

### What to Check

1. **Navigate to:** Submittal Builder → Welcome

2. **Verify Template Loading:**
   - View page source (Ctrl+U)
   - Search for: `<!-- SFB: welcome.php loaded`
   - Should appear near top of content
   - ✅ If present = new template is loading

3. **Verify CSS Loading:**
   - Open DevTools (F12) → Network tab
   - Reload page
   - Search for: `admin.css`
   - Should see: `.../assets/css/admin.css` with 200 status
   - ❌ If 404 = CSS file missing or path wrong

4. **Verify Layout:**
   - Hero section should have gradient background
   - Logo should appear (Webstuffguy Labs or fallback)
   - "What's Next?" section should show 4-column grid
   - On mobile/narrow screen: grid should collapse to 2 then 1 column

5. **Verify Logo:**
   - Logo should display in top-left of hero section
   - Check DevTools → Network for logo request
   - Should see either `webstuffguy-labs.png` or `custom-wordpress-plugins-1.png`
   - ❌ If 404 = logo file missing from `assets/img/`

6. **Test Functionality:**
   - Click "Copy" button on shortcode → should show "Copied!" message
   - Click "Open Builder" → should navigate to builder page
   - Click "Branding Settings" → should navigate to branding page

## Expected Results

### Visual Layout
```
┌─────────────────────────────────────────────────────┐
│ [Logo] Welcome to Submittal...  [Open Builder]     │
│        Built by Webstuffguy Labs [Branding]         │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│ ✓ What's Next?                                      │
│                                                      │
│ [📦 Build]  [🎨 Brand]  [📄 Publish]  [📋 PDF]     │
│ Catalog     Settings    Form          Generate      │
└─────────────────────────────────────────────────────┘
```

### Console (No Errors)
- No 404 errors for CSS or images
- No JavaScript errors
- Clean console output

## Troubleshooting

### CSS Not Loading
**Symptom:** Page looks plain, no grid layout, no cards
**Fixes:**
1. Check file exists: `assets/css/admin.css` (not `assets/admin.css`)
2. Clear all caches (browser + server)
3. Check screen ID matches: DevTools → `get_current_screen()->id` in console

### Logo Not Showing
**Symptom:** Empty space where logo should be
**Fixes:**
1. Upload `webstuffguy-labs.png` to `assets/img/`
2. Or ensure `custom-wordpress-plugins-1.png` exists as fallback
3. Check file permissions (should be readable)
4. Check DevTools Network tab for 404

### Old Template Still Loading
**Symptom:** Don't see debug comment in page source
**Fixes:**
1. Check `templates/admin/welcome.php` exists
2. Verify `SFB_PLUGIN_FILE` constant is defined
3. Clear OpCache if using PHP caching: `wp_cache_flush()`

## Next Steps (After Verification)

1. **Remove Debug Marker**
   Edit `templates/admin/welcome.php` line 44, remove:
   ```html
   <!-- SFB: welcome.php loaded (remove after verifying) -->
   ```

2. **Upload Logo (Optional)**
   If you want the official Webstuffguy Labs logo:
   - Create/upload `assets/img/webstuffguy-labs.png`
   - Recommended size: ~200px width, transparent PNG
   - Will auto-detect and use it

3. **Verify Mobile Layout**
   - Test on mobile viewport (<782px)
   - Hero should stack vertically
   - Grid should show 1 column

## Files Modified

1. `submittal-form-builder.php`
   - Line 19-22: Added SFB_PLUGIN_FILE constant
   - Line 7381-7407: Updated enqueue_admin() method
   - Line 3789-3806: Updated render_onboarding_page() method

2. `templates/admin/welcome.php`
   - Line 11-21: Fixed logo URL resolution
   - Line 44: Added debug marker (temporary)

## Rollback Instructions

If issues occur, restore from these line ranges:
- `submittal-form-builder.php` lines 7381-7450 (old enqueue logic)
- `templates/admin/welcome.php` lines 11-21 (old logo logic)

Or use git:
```bash
git diff HEAD~1 submittal-form-builder.php templates/admin/welcome.php
```

---

**Implementation Date:** 2025-01-13
**Status:** ✅ Complete - Ready for Testing
**Estimated Testing Time:** 5-10 minutes
