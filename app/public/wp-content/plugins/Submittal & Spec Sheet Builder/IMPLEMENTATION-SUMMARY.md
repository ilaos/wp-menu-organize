# Implementation Summary: Demo Tools & Upgrade Menu Visibility

## Overview
This implementation adds proper license state-based menu visibility controls for the "Demo Tools" and "⭐ Upgrade" menu items in the Submittal & Spec Sheet Builder plugin.

## Changes Made

### 1. Added SFB_SHOW_DEMO_TOOLS Constant
**File:** `submittal-form-builder.php` (lines 19-22)

```php
// Global toggle for Demo Tools (production: false, development: true)
if (!defined('SFB_SHOW_DEMO_TOOLS')) {
  define('SFB_SHOW_DEMO_TOOLS', false);
}
```

- Default value: `false` (production mode)
- Can be overridden in `wp-config.php` by adding: `define('SFB_SHOW_DEMO_TOOLS', true);`
- Controls whether Demo Tools menu appears for admin users

### 2. Added License State Mapping
**File:** `Includes/class-sfb-admin.php` (lines 23-46)

Added `get_license_state()` method that maps license status to one of four states:
- `free` - No active license
- `expired` - License expired or invalid
- `pro` - Active Pro license
- `agency` - Active Agency license

### 3. Updated Admin Menu Registration
**File:** `Includes/class-sfb-admin.php` (lines 48-233)

#### Demo Tools (lines 171-183)
- Only shows when `SFB_SHOW_DEMO_TOOLS === true` AND user has `manage_options` capability
- Replaced old `SFB_DEV_MODE` check

#### Upgrade Menu (lines 185-224)
- Shows only for `free` and `expired` license states
- Hidden for `pro` and `agency` license states
- Labeled as "⭐ Upgrade"

#### License & Support Menu
- Shows for `pro` and `agency` license states
- Hidden for `free` and `expired` states

#### Belt-and-Suspenders Safeguard (lines 226-232)
- Automatically removes Demo Tools menu for Free/Expired states
- Ensures production safety even if constant is accidentally enabled
- Runs on `admin_head` action

## Expected Behavior

### Free License State
**Visible Menus:**
- Welcome
- Submittal Builder
- Branding
- Settings
- Utilities
- ⭐ Upgrade

**Hidden Menus:**
- Demo Tools (even if `SFB_SHOW_DEMO_TOOLS` is true)
- Tracking
- Leads
- Agency
- Agency Analytics
- License & Support

### Expired License State
**Visible Menus:**
- Welcome
- Submittal Builder
- Branding
- Settings
- Utilities
- ⭐ Upgrade

**Hidden Menus:**
- Demo Tools (even if `SFB_SHOW_DEMO_TOOLS` is true)
- Tracking
- Leads
- Agency
- Agency Analytics
- License & Support

### Pro License State
**Visible Menus:**
- Welcome
- Submittal Builder
- Tracking (if active)
- Leads (if enabled)
- Branding
- Settings
- Utilities
- License & Support

**Conditionally Visible:**
- Demo Tools (only if `SFB_SHOW_DEMO_TOOLS` is true)

**Hidden Menus:**
- ⭐ Upgrade
- Agency
- Agency Analytics

### Agency License State
**Visible Menus:**
- Welcome
- Submittal Builder
- Tracking (if active)
- Leads (if enabled)
- Branding
- Settings
- 💼 Agency
- 📊 Agency Analytics
- Utilities
- License & Support

**Conditionally Visible:**
- Demo Tools (only if `SFB_SHOW_DEMO_TOOLS` is true)

**Hidden Menus:**
- ⭐ Upgrade

## Testing

To test the implementation:

1. **Test Free State:** Clear license data, verify no Demo Tools and ⭐ Upgrade appears
2. **Test Expired State:** Set expired license, verify no Demo Tools and ⭐ Upgrade appears
3. **Test Pro State:** Set active Pro license, verify no ⭐ Upgrade, License & Support appears
4. **Test Agency State:** Set active Agency license, verify Agency menus appear
5. **Test Demo Tools Toggle:** Add `define('SFB_SHOW_DEMO_TOOLS', true);` to wp-config.php
   - Verify Demo Tools appears for Pro/Agency
   - Verify Demo Tools is still hidden for Free/Expired (safeguard)

## Files Modified

1. `submittal-form-builder.php` - Added SFB_SHOW_DEMO_TOOLS constant
2. `Includes/class-sfb-admin.php` - Added license state logic and updated menu registration

## Acceptance Criteria - All Met ✓

- ✓ Free (no license): Shows Welcome, Submittal Builder, Branding, Settings, Utilities, ⭐ Upgrade
- ✓ Expired: Shows same as Free with ⭐ Upgrade
- ✓ Pro: Shows License & Support, no ⭐ Upgrade
- ✓ Agency: Shows Agency features, no ⭐ Upgrade
- ✓ Demo Tools hidden for Free/Expired even with constant enabled
- ✓ Demo Tools can be shown for Pro/Agency when constant is true
- ✓ No PHP warnings or notices expected
- ✓ Menu items remain in existing order
