# Admin Menu Matrix by License Tier

Complete visibility matrix for all admin pages in Submittal & Spec Sheet Builder.

## Menu Registration Location
**File:** `Includes/class-sfb-admin.php`
**Function:** `SFB_Admin::register_menus()` (line 53)
**Hook:** `admin_menu` (line 20)

## License State Mapping
**Function:** `SFB_Admin::get_license_state()` (lines 29-46)

Maps license status to one of four states:
- `free` - No active license (default)
- `expired` - License expired or invalid
- `pro` - Active Pro license
- `agency` - Active Agency license

## Complete Menu Matrix

| Page                   | Free | Expired | Pro | Agency | Slug                  | Registration Line | Visibility Logic                                          |
|------------------------|------|---------|-----|--------|-----------------------|-------------------|-----------------------------------------------------------|
| **Submittal Builder**  | ✅   | ✅      | ✅  | ✅     | `sfb`                 | Line 68-76        | Always visible (top-level menu, `manage_options`)        |
| **Welcome**            | ✅   | ✅      | ✅  | ✅     | `sfb-onboarding`      | Line 87-95        | Always visible (`manage_options`)                         |
| **Tracking**           | ❌   | ❌      | ✅  | ✅     | `sfb-tracking`        | Line 102-110      | `$license_status === 'active'` OR `sfb_is_pro_active()`  |
| **Leads**              | ❌   | ❌      | ✅* | ✅*    | `sfb-leads`           | Line 117-125      | Pro + `sfb_lead_capture_enabled` option must be true      |
| **Branding**           | ✅   | ✅      | ✅  | ✅     | `sfb-branding`        | Line 133-141      | Always visible (`manage_options`)                         |
| **Settings**           | ✅   | ✅      | ✅  | ✅     | `sfb-settings`        | Line 144-152      | Always visible (`manage_options`)                         |
| **💼 Agency**          | ❌   | ❌      | ❌  | ✅     | `sfb-agency`          | Line 156-164      | `sfb_is_agency_license()` (line 155)                      |
| **📊 Agency Analytics**| ❌   | ❌      | ❌  | ✅     | `sfb-agency-analytics`| Line 167-175      | `sfb_is_agency_license()` (line 155)                      |
| **Utilities**          | ✅   | ✅      | ✅  | ✅     | `sfb-tools`           | Line 183-191      | Always visible (`manage_options`)                         |
| **Demo Tools**         | ❌   | ❌      | 🔒  | 🔒     | `sfb-demo-tools`      | Line 196-204      | `SFB_SHOW_DEMO_TOOLS` constant + `manage_options`        |
| **⭐ Upgrade**         | ✅   | ✅      | ❌  | ❌     | `sfb-upgrade`         | Line 215-223, 237-245 | `in_array($license_state, ['free','expired'])`       |
| **License & Support**  | ❌   | ❌      | ✅  | ✅     | `sfb-license`         | Line 226-234      | `in_array($license_state, ['pro','agency'])`              |

**Legend:**
- ✅ Always visible for this tier
- ❌ Never visible for this tier
- 🔒 Visible only if `SFB_SHOW_DEMO_TOOLS` constant is true (dev mode)
- ✅* Visible if Pro/Agency AND setting is enabled

## Special Cases

### Demo Tools Safeguard
**Lines:** 248-254

Even if `SFB_SHOW_DEMO_TOOLS` is enabled, Demo Tools is forcibly removed for Free/Expired tiers via `admin_head` hook:

```php
add_action('admin_head', function() use ($license_state) {
  if (in_array($license_state, ['free', 'expired'], true)) {
    remove_submenu_page('sfb', 'sfb-demo-tools');
  }
});
```

This belt-and-suspenders approach ensures production safety.

### Leads Page Condition
The Leads page requires TWO conditions:
1. Pro or Agency license: `sfb_is_pro_active()` returns true
2. Lead capture enabled: `get_option('sfb_lead_capture_enabled', false)` returns true

If lead capture is disabled in Settings, the Leads page is hidden even for Pro/Agency users.

### Expired License Behavior
Expired licenses show the "⭐ Upgrade" menu item instead of "License & Support". This encourages renewal while still allowing access to all Free-tier pages.

## Direct URL Access

All menu pages can be accessed via:
```
/wp-admin/admin.php?page={slug}
```

Examples:
- Branding: `/wp-admin/admin.php?page=sfb-branding`
- Agency: `/wp-admin/admin.php?page=sfb-agency` (Agency only)
- Upgrade: `/wp-admin/admin.php?page=sfb-upgrade` (Free/Expired only)

Direct URL access is protected by:
1. WordPress capability check (`manage_options` or custom capability)
2. Template-level license checks (show upsell if tier insufficient)
3. AJAX/REST endpoint guards

## Capability Requirements

| Capability             | Description                          | Required For                           |
|------------------------|--------------------------------------|----------------------------------------|
| `manage_options`       | WordPress admin (default)            | All admin pages                        |
| `edit_sfb_catalog`     | Edit catalog nodes                   | Catalog editor operations              |
| `edit_sfb_branding`    | Edit branding settings               | Branding page saves                    |
| `view_sfb_tracking`    | View tracking page                   | Tracking page (Pro)                    |
| `access_sfb_utilities` | Access utilities page                | Utilities page                         |
| `access_sfb_agency`    | Access Agency features               | Agency page, Brand presets (Agency)    |

All capabilities are granted to Administrator role by default (lines 187-212 in submittal-form-builder.php).

## License Check Functions

### Primary Functions
1. **`sfb_is_pro_active()`** - Lines 61-78, `Includes/pro/registry.php`
   - Returns `true` for Pro or Agency
   - Dev override: `SFB_PRO_DEV` constant
   - Checks WooCommerce license API
   - Fallback to `sfb_license` option

2. **`sfb_is_agency_license()`** - Lines 192-225, `Includes/pro/registry.php`
   - Returns `true` for Agency only
   - Dev override: `SFB_AGENCY_DEV` constant
   - Checks `SFB_Branding::is_agency_license()`
   - Checks license data `tier` field
   - Checks product name for "agency"

3. **`SFB_Admin::get_license_state()`** - Lines 29-46, `Includes/class-sfb-admin.php`
   - Maps to: `free`, `expired`, `pro`, `agency`
   - Used for menu logic

## Constants

| Constant              | Default | Purpose                                    | Location                    |
|-----------------------|---------|--------------------------------------------|------------------------------|
| `SFB_SHOW_DEMO_TOOLS` | `false` | Enable Demo Tools menu                     | submittal-form-builder.php:20|
| `SFB_PRO_DEV`         | N/A     | Developer override for Pro features        | (user-defined)               |
| `SFB_AGENCY_DEV`      | N/A     | Developer override for Agency features     | (user-defined)               |

## Testing Scenarios

### Test Free Tier
- Clear license data: `delete_option('sfb_license')`
- Expect: Welcome, Submittal Builder, Branding, Settings, Utilities, ⭐ Upgrade
- Hidden: Tracking, Leads, Agency, License & Support, Demo Tools

### Test Expired Tier
- Set license: `update_option('sfb_license', ['status' => 'expired'])`
- Expect: Same as Free tier
- Hidden: Same as Free tier

### Test Pro Tier
- Set license: `update_option('sfb_license', ['status' => 'active'])`
- Expect: All Free + Tracking, License & Support
- Conditionally: Leads (if enabled), Demo Tools (if constant true)
- Hidden: ⭐ Upgrade, Agency menus

### Test Agency Tier
- Set license: `update_option('sfb_license', ['status' => 'active', 'tier' => 'agency'])`
- OR: Define `SFB_AGENCY_DEV` constant
- Expect: All Pro + 💼 Agency, 📊 Agency Analytics
- Hidden: ⭐ Upgrade

### Test Demo Tools
- Define: `define('SFB_SHOW_DEMO_TOOLS', true);` in `wp-config.php`
- Free/Expired: Still hidden (safeguard at line 248-254)
- Pro/Agency: Visible

## Menu Order

Menus appear in this order (position parameter):

1. **(auto)** Submittal Builder (parent copy)
2. **0** Welcome
3. **3** Tracking (Pro/Agency)
4. **4** Leads (Pro/Agency + enabled)
5. **5** Branding
6. **6** Settings
7. **7** Agency (Agency)
8. **8** Agency Analytics (Agency)
9. **9** Utilities
10. **10** Demo Tools (dev mode)
11. **999** ⭐ Upgrade (Free/Expired) OR License & Support (Pro/Agency)

Position `999` ensures the last item always appears at the bottom of the menu.
