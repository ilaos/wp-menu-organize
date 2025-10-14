# License Tier Feature Map

Complete feature inventory by tier with implementation status.

---

## Related Documentation

**📚 See also:**
- **[FEATURE-INVENTORY.md](../FEATURE-INVENTORY.md)** - Comprehensive technical feature documentation with implementation details
- **[FEATURE-STATUS.md](../FEATURE-STATUS.md)** - Feature implementation status tracker with verification dates
- **[API-REFERENCE.md](../API-REFERENCE.md)** - Complete REST API endpoint reference
- **[DEVELOPER-HOOKS.md](../DEVELOPER-HOOKS.md)** - Hooks and filters documentation
- **[marketing_bullets.md](marketing_bullets.md)** - Marketing copy and tier-specific messaging

---

## Quick Reference

| Tier    | Description                              | Key Features                                             |
|---------|------------------------------------------|----------------------------------------------------------|
| Free    | No active license                        | Basic catalog, local drafts, PDF generation              |
| Expired | License expired or invalid               | Same as Free (encourages renewal)                        |
| Pro     | Active Pro license                       | + Server drafts, tracking, lead capture, auto-email      |
| Agency  | Active Agency license                    | + All Pro + White-label, presets, lead routing, analytics|

## Complete Feature Matrix

| Feature                      | Free | Expired | Pro | Agency | Status      | Notes                                          |
|------------------------------|------|---------|-----|--------|-------------|------------------------------------------------|
| **Core Features**            |      |         |     |        |             |                                                |
| Catalog Management           | ✅   | ✅      | ✅  | ✅     | Implemented | Full CRUD via REST API                         |
| PDF Generation               | ✅   | ✅      | ✅  | ✅     | Implemented | Dompdf with custom templates                   |
| Summary Page                 | ✅   | ✅      | ✅  | ✅     | Implemented | Category-grouped product summary               |
| Table of Contents            | ✅   | ✅      | ✅  | ✅     | Implemented | Clickable internal navigation                  |
| Local Autosave               | ✅   | ✅      | ✅  | ✅     | Implemented | Browser localStorage, 20s throttle             |
| Email Notifications          | ✅   | ✅      | ✅  | ✅     | Implemented | With plugin credit (Free) or custom (Pro+)     |
| Basic Branding               | ✅   | ✅      | ✅  | ✅     | Implemented | Logo, colors, footer text, company info        |
| Frontend Builder             | ✅   | ✅      | ✅  | ✅     | Implemented | Public shortcode [submittal_builder]           |
| Admin Catalog Editor         | ✅   | ✅      | ✅  | ✅     | Implemented | Drag-drop tree editor                          |
| **Pro Features**             |      |         |     |        |             |                                                |
| Server-Side Shareable Drafts | ❌   | ❌      | ✅  | ✅     | Implemented | Save to server, share URL, 45-day expiry       |
| Tracking Links               | ❌   | ❌      | ✅  | ✅     | Implemented | Public tracking URLs, view timestamps, IP hash |
| Lead Capture & CRM           | ❌   | ❌      | ✅  | ✅     | Implemented | Email/phone capture, CSV export, UTM tracking  |
| Auto-Email Packets           | ❌   | ❌      | ✅  | ✅     | Implemented | Send PDFs with tracking links                  |
| Auto-Archive to History      | ❌   | ❌      | ✅  | ✅     | Implemented | Archive by project/date                        |
| PDF Themes (Arch/Corp)       | ❌   | ❌      | ✅  | ✅     | Partial     | Themes exist, needs Pro gate enforcement       |
| PDF Watermark                | ❌   | ❌      | ✅  | ✅     | Implemented | Fixed-position overlay on all PDF pages        |
| Approval Signature Block     | ❌   | ❌      | ✅  | ✅     | Partial     | Setting exists, needs full implementation      |
| **Agency Features**          |      |         |     |        |             |                                                |
| White-Label Branding         | ❌   | ❌      | ❌  | ✅     | Implemented | Remove plugin credit, custom email sender      |
| Brand Presets Library        | ❌   | ❌      | ❌  | ✅     | Implemented | Save/load/apply brand configurations           |
| Default Preset Auto-Apply    | ❌   | ❌      | ❌  | ✅     | Implemented | Auto-apply brand preset to PDFs                |
| Review Screen Preset Switcher| ❌   | ❌      | ❌  | ✅     | Implemented | Session-only preset preview on Review step     |
| Lead Routing & Webhooks      | ❌   | ❌      | ❌  | ✅     | Implemented | Rules engine, domain/UTM matching, webhooks    |
| Weekly Lead Export Scheduler | ❌   | ❌      | ❌  | ✅     | Implemented | Automated weekly CSV email with Send Now       |
| Agency Analytics             | ❌   | ❌      | ❌  | ✅     | Implemented | Aggregated non-PII metrics                     |
| Agency Library - Save as Pack| ❌   | ❌      | ❌  | ✅     | Implemented | Save catalog as Pack, export JSON              |
| Client Handoff Mode          | ❌   | ❌      | ❌  | ✅     | Implemented | Hide agency-specific UI for client sites       |
| Operator Role & Capabilities | ❌   | ❌      | ❌  | ✅     | Implemented | Custom WordPress role for limited access       |
| Agency Library Pages         | ❌   | ❌      | ❌  | ✅     | Implemented | 💼 Agency, 📊 Agency Analytics menus           |
| **UI/UX Features**           |      |         |     |        |             |                                                |
| Drag-Drop Node Reordering    | ✅   | ✅      | ✅  | ✅     | Implemented | Tree structure with visual feedback            |
| Bulk Operations              | ✅   | ✅      | ✅  | ✅     | Implemented | Delete, move, duplicate, export                |
| Search & Filter              | ✅   | ✅      | ✅  | ✅     | Implemented | Real-time catalog search                       |
| Node History                 | ✅   | ✅      | ✅  | ✅     | Implemented | Track node changes (admin only)                |
| Toast Notifications          | ✅   | ✅      | ✅  | ✅     | Implemented | Success/error feedback                         |
| Modal Dialogs                | ✅   | ✅      | ✅  | ✅     | Implemented | Confirm deletes, upsells                       |
| **Security Features**        |      |         |     |        |             |                                                |
| Rate Limiting                | ✅   | ✅      | ✅  | ✅     | Implemented | Draft saves, lead submissions                  |
| Honeypot Anti-Bot            | ✅   | ✅      | ✅  | ✅     | Implemented | Lead capture form protection                   |
| IP Hashing (Privacy)         | ✅   | ✅      | ✅  | ✅     | Implemented | Tracking & lead capture                        |
| Nonce Verification           | ✅   | ✅      | ✅  | ✅     | Implemented | All AJAX/REST endpoints                        |
| Capability Checks            | ✅   | ✅      | ✅  | ✅     | Implemented | WordPress role-based access                    |
| **Developer Features**       |      |         |     |        |             |                                                |
| Demo Tools Page              | ❌   | ❌      | 🔒  | 🔒     | Implemented | Gated by SFB_SHOW_DEMO_TOOLS constant         |
| REST API Endpoints           | ✅   | ✅      | ✅  | ✅     | Implemented | Full REST API for catalog, settings, drafts    |
| WordPress Hooks & Filters    | ✅   | ✅      | ✅  | ✅     | Implemented | Extensive hook system                          |
| Debug Logging                | ✅   | ✅      | ✅  | ✅     | Implemented | PHP error_log integration                      |
| **Utilities**                |      |         |     |        |             |                                                |
| Database Optimization        | ✅   | ✅      | ✅  | ✅     | Implemented | Clean orphans, optimize tables                 |
| Expired Drafts Cleanup       | ✅   | ✅      | ✅  | ✅     | Implemented | Manual + cron purge                            |
| Smoke Test                   | ✅   | ✅      | ✅  | ✅     | Implemented | Test PDF generation                            |
| Test Email                   | ✅   | ✅      | ✅  | ✅     | Implemented | Test email delivery                            |
| Import/Export                | ✅   | ✅      | ✅  | ✅     | Implemented | JSON catalog backup/restore                    |
| License Management           | ✅   | ✅      | ✅  | ✅     | Implemented | Activate/deactivate via WooCommerce API        |

**Legend:**
- ✅ Fully implemented and tested
- ⚠️ Partial implementation (missing backend or frontend)
- ❌ Not available for this tier
- 🔒 Available only with constant enabled (dev mode)

## Implementation Status Detail

### ✅ Fully Implemented (No Gaps)

#### Core (All Tiers)
- **Catalog Management** - REST API + React UI (class-sfb-rest.php, admin.js)
- **PDF Generation** - Dompdf integration (pdf-generator.php)
- **Summary & TOC** - Built into PDF generator
- **Local Autosave** - Browser localStorage (app.js:634-689)
- **Email Notifications** - WordPress mail integration (lead-capture.php:130-213)
- **Basic Branding** - Settings page + REST API (class-sfb-branding.php)
- **Frontend Builder** - Shortcode + React app (templates/frontend/builder.php)

#### Pro Features
- **Server-Side Drafts**
  - Backend: submittal-form-builder.php:6370-6665
  - Frontend: assets/app.js:553-608
  - REST: class-sfb-rest.php:161-177
  - Upsell modal: app.js:759-783 (now 180-205)
  - Status: ✅ Complete with URL sharing, expiry, rate limiting

- **Tracking Links**
  - Backend: submittal-form-builder.php:294-330
  - Storage: `sfb_packets` option
  - Redirect: `?sfb_track={hash}` endpoint
  - Status: ✅ Complete with IP hashing, timestamps

- **Lead Capture & CRM**
  - Backend: Includes/lead-capture.php (full implementation)
  - Features: Email/phone, UTM tracking, honeypot, rate limiting
  - Export: CSV download (lines 301-337)
  - Admin UI: Leads page (Pro + enabled)
  - Status: ✅ Complete with BCC admin, white-label email support

- **Auto-Email**
  - Implementation: lead-capture.php:130-213
  - Tracking links: submittal-form-builder.php:294-330
  - Status: ✅ Complete

- **Auto-Archive**
  - Implementation: History tracking in node operations
  - Status: ✅ Complete

#### Agency Features
- **White-Label Branding**
  - Backend: branding-helpers.php:207-308
  - PDF integration: pdf-generator.php:674, 749
  - Email integration: lead-capture.php:143-151
  - Status: ✅ Complete with footer/email customization

- **Brand Presets Library**
  - Backend: class-sfb-branding.php:77-469
  - AJAX handlers: Lines 303-469
  - Features: Create, apply, rename, delete, set default
  - Status: ✅ Complete with auto-apply option

- **Lead Routing & Webhooks**
  - Backend: agency-lead-routing.php (full implementation)
  - Rules engine: Lines 249-304
  - Email routing: Lines 348-424
  - Webhook delivery: Lines 434-526 with retry logic
  - Testing: Lines 610-663
  - Status: ✅ Complete with fallback routing, delivery logging

- **Agency Analytics**
  - Backend: agency-analytics.php
  - Tracks: Leads, PDFs, drafts, tracking views
  - Privacy: Non-PII aggregates only
  - Status: ✅ Complete

- **Client Handoff Mode**
  - Implementation: branding-helpers.php:185-193
  - Hides: Agency-specific UI elements
  - Status: ✅ Complete

- **Default Preset Auto-Apply**
  - Implementation: Branding page toggle + PDF generator integration
  - Option: `sfb_brand_use_default_on_pdf`
  - Behavior: Auto-applies default preset to Review page and PDFs
  - Status: ✅ Complete

- **Review Screen Preset Switcher**
  - Frontend: assets/js/review.js:299-589
  - Session storage: `sfb_session_preset` (doesn't persist on reload)
  - Localization: templates/frontend/builder.php:107-149
  - Status: ✅ Complete with session-only preview

- **Weekly Lead Export Scheduler**
  - Core functions: submittal-form-builder.php:4546-4757
  - Cron hook: `sfb_weekly_lead_export`
  - Settings UI: submittal-form-builder.php:2413-2568
  - Features: Day/time picker, manual Send Now button, duplicate prevention
  - Status: ✅ Complete with automated weekly emails

- **Agency Library - Save as Pack**
  - Save handler: submittal-form-builder.php:6602-6683
  - Export handler: submittal-form-builder.php:5066-5107
  - Library page: submittal-form-builder.php:2071-2212
  - AJAX: `sfb_pack_save`, `sfb_pack_export`
  - Option: `sfb_agency_packs`
  - Status: ✅ Complete with JSON export/import

- **Operator Role & Capabilities**
  - Role creation: submittal-form-builder.php:171-195
  - Capability mapping: submittal-form-builder.php:6488-6543
  - Custom caps: `use_sfb_builder`, `view_sfb_leads`, `view_sfb_tracking`, `edit_sfb_catalog`, `edit_sfb_branding`, `access_sfb_agency`
  - User assignment UI: submittal-form-builder.php:2372-2433
  - Status: ✅ Complete with multi-user support

### ⚠️ Partially Implemented (Needs Work)

#### PDF Themes (Pro)
- **Status:** Theme code exists but no Pro license gate
- **File:** pdf-generator.php (theme selection logic present)
- **Gap:** No enforcement preventing Free users from using themes
- **Fix Needed:** Add `sfb_is_pro_active()` check before applying non-default themes

#### Approval Signature Block (Pro)
- **Status:** Setting exists (`approve_block`) but needs full implementation
- **File:** app.js:477 defines setting
- **Gap:** PDF generator doesn't render signature block
- **Fix Needed:** Add signature section to PDF template

### ❌ Not Implemented (No Code)

None identified. All features listed in registry have at least partial implementation.

## Feature Entry Points

### Admin Pages
| Feature                    | Menu Slug              | Tier      | File                           |
|----------------------------|------------------------|-----------|--------------------------------|
| Catalog Editor             | `sfb`                  | All       | submittal-form-builder.php     |
| Welcome/Onboarding         | `sfb-onboarding`       | All       | templates/admin/onboarding.php |
| Tracking Dashboard         | `sfb-tracking`         | Pro+      | submittal-form-builder.php     |
| Leads CRM                  | `sfb-leads`            | Pro+      | submittal-form-builder.php     |
| Branding Settings          | `sfb-branding`         | All       | submittal-form-builder.php     |
| General Settings           | `sfb-settings`         | All       | submittal-form-builder.php     |
| Agency Library             | `sfb-agency`           | Agency    | submittal-form-builder.php     |
| Agency Analytics           | `sfb-agency-analytics` | Agency    | submittal-form-builder.php     |
| Utilities & Tools          | `sfb-tools`            | All       | submittal-form-builder.php     |
| Demo Tools                 | `sfb-demo-tools`       | Dev       | submittal-form-builder.php     |
| Upgrade Page               | `sfb-upgrade`          | Free/Exp  | templates/admin/upgrade.php    |
| License & Support          | `sfb-license`          | Pro+      | submittal-form-builder.php     |

### Frontend Shortcodes
| Shortcode            | Purpose                      | Tier | File                            |
|----------------------|------------------------------|------|---------------------------------|
| `[submittal_builder]`| Public product selector      | All  | class-sfb-render.php            |

### REST API Endpoints
See [API-REFERENCE.md](../API-REFERENCE.md) for complete REST API documentation.

Key gated endpoints:
- **POST `/sfb/v1/drafts`** - Create server draft (Pro+)
- **POST `/sfb/v1/generate`** - Generate PDF (All, with tier-based features)

### AJAX Handlers
| Action                        | Tier    | File                        | Line       |
|-------------------------------|---------|------------------------------|------------|
| `sfb_save_brand`              | All     | class-sfb-ajax.php           | 54         |
| `sfb_preset_create`           | Agency  | class-sfb-branding.php       | 303-330    |
| `sfb_preset_apply`            | Agency  | class-sfb-branding.php       | 356-380    |
| `sfb_routing_save`            | Agency  | class-sfb-ajax.php           | 129-163    |
| `sfb_routing_test`            | Agency  | class-sfb-ajax.php           | 169-194    |
| `sfb_submit_lead`             | All*    | lead-capture.php             | 25-125     |
| `sfb_pack_export`             | Agency  | submittal-form-builder.php   | 84         |

*Public but requires Pro+ to enable

## License Gates by File

### Primary Gate Functions
**File:** `Includes/pro/registry.php`

1. **`sfb_is_pro_active()`** (Lines 61-78)
   - Returns: `true` for Pro or Agency
   - Dev override: `SFB_PRO_DEV`
   - Agency check: Calls `sfb_is_agency_license()` (Agency includes Pro)
   - WooCommerce API: Checks `sfb_is_license_active()`
   - Fallback: `get_option('sfb_license')['status'] === 'active'`
   - Filter: `sfb_is_pro_active`

2. **`sfb_is_agency_license()`** (Lines 192-225)
   - Returns: `true` for Agency only
   - Dev override: `SFB_AGENCY_DEV`
   - SFB_Branding check: `SFB_Branding::is_agency_license()`
   - Tier field: `$license['tier'] === 'agency'`
   - Product name: `stripos($license['product_name'], 'agency')`
   - Fallback: `get_option('sfb_license')['tier'] === 'agency'`

3. **`sfb_feature_enabled(string $key)`** (Lines 151-157)
   - Checks if feature exists and is enabled for license
   - Pro features blocked if `!sfb_is_pro_active()`

### Gate Usage Examples

#### REST API (class-sfb-rest.php)
```php
// Line 239, 243 - Draft server status
'server_enabled' => (bool)$settings['drafts_server_enabled'] &&
                    (sfb_is_pro_active() || defined('SFB_PRO_DEV'))
```

#### Settings (submittal-form-builder.php)
```php
// Line 3542 - Draft server toggle
$sanitized['drafts_server_enabled'] = !empty($input['drafts_server_enabled']) &&
                                       (sfb_is_pro_active() || defined('SFB_PRO_DEV'));
```

#### Brand Presets (class-sfb-branding.php)
```php
// Line 312 - AJAX handler guard
if (!self::is_agency_license()) {
  wp_send_json_error(['message' => __('Brand Presets require an Agency license.')], 403);
}
```

#### White-Label (branding-helpers.php)
```php
// Line 209 - White-label check
function sfb_is_white_label_enabled() {
  if (!SFB_Branding::is_agency_license()) return false;
  $brand = sfb_get_brand_settings();
  return !empty($brand['white_label']['enabled']);
}
```

#### Menu Registration (class-sfb-admin.php)
```php
// Line 155 - Agency menu
if (sfb_is_agency_license()) {
  add_submenu_page('sfb', __('Agency'), __('💼 Agency'), 'manage_options', 'sfb-agency', ...);
}

// Line 98 - Pro tracking
$show_tracking = ($license_status === 'active') ||
                 (defined('SFB_PRO_DEV') && SFB_PRO_DEV) ||
                 (function_exists('sfb_is_pro_active') && sfb_is_pro_active());
```

## JavaScript License Detection

### Frontend (app.js)
```javascript
// Localized from PHP (submittal-form-builder.php:344)
const SFB_APP = {
  pro_active: <?php echo sfb_is_pro_active() ? 'true' : 'false'; ?>,
  upgrade_url: '<?php echo admin_url('admin.php?page=sfb-upgrade'); ?>',
  ...
};

// Usage (app.js:587-588)
if (res.code === 'pro_required') {
  showUpsellModal(); // Line 759 (now 180)
}
```

### Admin (admin.js)
```javascript
// Localized from PHP (submittal-form-builder.php:388, 390)
const SFB = {
  drafts_server_enabled: <?php echo sfb_is_pro_active() ? 'true' : 'false'; ?>,
  isAgency: <?php echo sfb_is_agency_license() ? 'true' : 'false'; ?>,
  ...
};

// Usage (admin.js:2608)
if (window.SFB && SFB.isAgency) {
  // Show Agency pack export button
}
```

## Upsell/Lock Points

### Modal Upsells
1. **Server Draft Save** (app.js:587-588, 759-783)
   - Trigger: User clicks "Save & Share" without Pro
   - Response: `{ code: 'pro_required' }` from REST
   - Modal: "✨ Pro Feature" with benefits list
   - CTA: "Upgrade to Pro" button → `sfb-upgrade` page

### UI Element Locks
2. **Agency Pack Export** (admin.js:2608)
   - Render condition: `window.SFB && SFB.isAgency`
   - Hidden for non-Agency users
   - No explicit lock icon (feature not rendered)

### Setting Locks
3. **Draft Server Toggle** (Settings page)
   - Disabled for Free/Expired
   - Checked via: `sfb_is_pro_active()` on save (line 3542)
   - UI shows toggle but saves as `false` if not Pro

4. **Lead Capture Toggle** (Settings page)
   - Available to all (toggle exists)
   - Gated by enabling setting + Pro check in menu (line 114-115)
   - No explicit Pro-only label (feature-level gate)

5. **White-Label Settings** (Branding page)
   - Section only shown if Agency: `sfb_is_agency_license()`
   - Hidden for Pro/Free users
   - No upsell shown (section not rendered)

### Page-Level Locks
6. **Tracking Page** (Pro+)
   - Menu hidden for Free/Expired
   - Direct URL access shows admin page (WordPress handles capability)

7. **Leads Page** (Pro+ with setting enabled)
   - Menu hidden unless Pro + `sfb_lead_capture_enabled`
   - Direct URL access shows admin page

8. **Agency Pages** (Agency only)
   - Menu hidden for Pro/Free
   - Direct URL access shows admin page

### Upgrade Page Messaging
**File:** templates/admin/upgrade.php

Shows tier-appropriate messaging:
- Free: "Upgrade to Pro" with feature comparison
- Expired: "Renew License" with renewal CTA
- Pro: "Already Pro" with support info (shouldn't see page normally)

## Settings & Options

### License Data
**Option:** `sfb_license`
**Structure:**
```php
[
  'key'        => 'license-key-here',
  'email'      => 'user@example.com',
  'status'     => 'active|expired|invalid|inactive',
  'tier'       => 'pro|agency',  // Optional
  'product_id' => 12345,          // WooCommerce product ID
  'expires_at' => '2025-12-31',   // Optional expiry date
]
```

### Feature Settings (All Tiers)
| Option                          | Type    | Default | Description                              |
|---------------------------------|---------|---------|------------------------------------------|
| `sfb_brand_settings`            | array   | []      | Logo, colors, footer, company info       |
| `sfb_drafts_autosave_enabled`   | bool    | true    | Browser autosave                         |
| `sfb_drafts_expiry_days`        | int     | 45      | Draft expiration period                  |
| `sfb_drafts_rate_limit_sec`     | int     | 20      | Rate limit for draft saves               |
| `sfb_drafts_privacy_note`       | string  | ''      | Privacy notice for drafts                |
| `sfb_auto_deactivate_on_deactivate` | bool | true   | Deactivate license on plugin deactivate  |
| `sfb_remove_data_on_uninstall`  | bool    | false   | Remove all data on uninstall             |

### Pro Settings
| Option                        | Type | Default | Gated By                        |
|-------------------------------|------|---------|----------------------------------|
| `sfb_drafts_server_enabled`   | bool | false   | `sfb_is_pro_active()`           |
| `sfb_lead_capture_enabled`    | bool | false   | No hard gate (Pro feature)      |
| `sfb_lead_bcc_admin`          | bool | false   | Lead capture enabled            |

### Agency Settings
| Option                             | Type  | Default | Gated By                            |
|------------------------------------|-------|---------|-------------------------------------|
| `sfb_brand_presets`                | array | []      | `sfb_is_agency_license()`           |
| `sfb_brand_use_default_on_pdf`     | bool  | false   | Agency license                      |
| `sfb_lead_routing_enabled`         | bool  | false   | `sfb_is_agency_license()`           |
| `sfb_lead_routing_rules`           | array | []      | Agency license                      |
| `sfb_lead_routing_fallback`        | array | []      | Agency license                      |
| `sfb_client_handoff_mode`          | bool  | false   | Agency license                      |
| `sfb_handoff_previous_role`        | string| ''      | Temporary during handoff            |
| `sfb_agency_packs`                 | array | []      | Agency license                      |
| `sfb_lead_weekly_export_enabled`   | bool  | false   | Agency license                      |
| `sfb_lead_weekly_export_email`     | string| ''      | Agency license                      |
| `sfb_lead_weekly_export_day`       | string| 'monday'| Agency license                      |
| `sfb_lead_weekly_export_time`      | string| '09:00' | Agency license                      |

### White-Label Settings (Agency)
Stored in `sfb_brand_settings['white_label']`:
```php
[
  'enabled'              => false,
  'custom_footer'        => '',
  'email_from_name'      => '',
  'email_from_address'   => '',
  'show_subtle_credit'   => true,
]
```

## Custom Capabilities

**File:** submittal-form-builder.php Lines 187-212

All capabilities granted to Administrator by default:
- `edit_sfb_catalog` - Edit catalog nodes (REST/AJAX operations)
- `edit_sfb_branding` - Save branding settings
- `view_sfb_tracking` - View tracking page (Pro)
- `access_sfb_utilities` - Access utilities page
- `access_sfb_agency` - Access Agency features (Agency)

## Constants

| Constant              | Type | Default | Purpose                                       |
|-----------------------|------|---------|-----------------------------------------------|
| `SFB_SHOW_DEMO_TOOLS` | bool | false   | Enable Demo Tools menu (submittal-form-builder.php:20) |
| `SFB_PRO_DEV`         | bool | N/A     | Dev override for Pro features (user-defined)  |
| `SFB_AGENCY_DEV`      | bool | N/A     | Dev override for Agency features (user-defined)|

**Usage:**
```php
// wp-config.php (development only)
define('SFB_SHOW_DEMO_TOOLS', true);
define('SFB_PRO_DEV', true);
define('SFB_AGENCY_DEV', true);
```

## Filters & Hooks

### License Filters
```php
// Modify Pro active check
apply_filters('sfb_is_pro_active', bool $valid, array $license);

// Modify feature map
apply_filters('sfb_features_map', array $features);

// Modify changelog
apply_filters('sfb_pro_changelog', array $changelog);
```

### Usage Examples
```php
// Force Pro active for specific user
add_filter('sfb_is_pro_active', function($valid, $license) {
  if (get_current_user_id() === 1) return true;
  return $valid;
}, 10, 2);

// Add custom feature
add_filter('sfb_features_map', function($features) {
  $features['custom_export'] = [
    'label' => 'Custom Export',
    'group' => 'Data',
    'pro'   => true,
    'desc'  => 'Export to custom format',
    'since' => '1.1.0',
  ];
  return $features;
});
```

## Testing License States

### Via Constants (Development)
```php
// wp-config.php
define('SFB_PRO_DEV', true);        // Enables all Pro features
define('SFB_AGENCY_DEV', true);     // Enables all Agency features
define('SFB_SHOW_DEMO_TOOLS', true); // Shows Demo Tools menu
```

### Via Options (Production Simulation)
```php
// Free (default)
delete_option('sfb_license');

// Expired
update_option('sfb_license', [
  'key' => 'test-key',
  'email' => 'test@example.com',
  'status' => 'expired'
]);

// Pro
update_option('sfb_license', [
  'key' => 'test-key',
  'email' => 'test@example.com',
  'status' => 'active'
]);

// Agency
update_option('sfb_license', [
  'key' => 'test-key',
  'email' => 'test@example.com',
  'status' => 'active',
  'tier' => 'agency'
]);
```

### Debug License State
**File:** `debug-license-check.php`

View this file in browser to see current license state:
```
/wp-content/plugins/submittal-form-builder/debug-license-check.php
```

Shows:
- License option data
- `sfb_is_pro_active()` result
- `sfb_is_agency_license()` result
- Constants status
- Enabled features list

## Summary by Group

### Core (All Tiers) - 16 features
All basic functionality: catalog, PDF, branding, frontend builder, admin editor, utilities

### Pro (Pro + Agency) - 8 features
Server drafts, tracking, lead capture, auto-email, auto-archive, watermark, themes†, signature†
†Partial implementation

### Agency (Agency Only) - 11 features
White-label, brand presets, default preset auto-apply, preset switcher, lead routing, weekly export, analytics, agency library, client handoff, operator role, agency pages

### Total Features: 35
- Fully Implemented: 33 (94.3%)
- Partially Implemented: 2 (5.7%)
- Not Implemented: 0 (0%)

## Next Steps

### High Priority (Complete Partial Features)
1. **PDF Themes Gate** - Add `sfb_is_pro_active()` check to theme selection
2. **PDF Watermark** - Implement watermark overlay in pdf-generator.php
3. **Signature Block** - Add signature section rendering to PDF template

### Medium Priority (Enhancements)
1. Add explicit "Pro Only" badges in Settings UI
2. Add upsell tooltips on disabled Pro settings for Free users
3. Add lock icons (🔒) to Pro/Agency menu items in free tier

### Low Priority (Polish)
1. Improve upgrade page with video/screenshots (see [marketing_bullets.md](marketing_bullets.md) for copy templates)
2. Add feature comparison table to upgrade page
3. Add testimonials/social proof to upgrade page

---

_Docs synchronized on: 2025-10-13 • Source parity confirmed_
