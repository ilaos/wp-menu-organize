# CHANGELOG: Agency Library - Save as Pack

## Phase A: Initial Implementation (2025-10-11)

### Overview
Implemented complete Agency Library system for saving, managing, and deploying reusable catalog Packs across multiple sites. This feature enables fast onboarding for multi-site deployments and white-label solutions.

### New Features

#### Agency Library Admin Page
- **New submenu:** "📦 Agency Library" (only visible to Agency license holders)
- **Pack management table:**
  - Pack name with creation date
  - Product count
  - Branding indicator (✓ if included)
  - Last updated timestamp
  - Export JSON button (nonce-secured download)
  - Delete button with confirmation
- **Empty state UI:**
  - Helpful instructions for first-time users
  - "Go to Builder" call-to-action button
  - How-to guide for creating and using Packs

#### Save as Pack (Builder)
- **New toolbar button:** "💼 Save as Pack" (Agency-gated)
  - Located in Builder admin page toolbar
  - Only visible to Agency license holders
  - Positioned after Import JSON button
- **Save Pack modal:**
  - Pack name input (required)
  - "Include branding settings" checkbox (default: checked)
  - "Include product notes" checkbox (default: unchecked)
  - Real-time validation (name required)
  - Loading state during save
  - Success toast with product count

#### Pack Data Model
- **Storage:** WordPress options API (`sfb_agency_packs`)
- **Pack structure:**
  - `id` - UUID (wp_generate_uuid4)
  - `name` - User-provided Pack name
  - `counts` - Product/node counts
  - `has_branding` - Boolean flag
  - `updated_at` - MySQL datetime
  - `data` - Complete Pack payload:
    - `form` - Form metadata
    - `nodes` - All nodes with full settings
    - `branding` - Optional branding settings object
- **Node format:** Raw nodes (different from industry pack categories structure)

#### Pack Export
- **AJAX endpoint:** `sfb_pack_export`
- **Security:**
  - Nonce verification (one-time per Pack)
  - Admin capability check
  - Agency license validation
  - Pack ID sanitization
- **Download:**
  - Content-Type: application/json
  - Filename: `{pack-name}.json`
  - Complete Pack data structure

#### Seeder Integration
- **New parameter:** `agency_pack_id` for `/sfb/v1/form/seed` endpoint
- **Functionality:**
  - Load Pack from database by UUID
  - Remap node IDs to avoid conflicts
  - Apply branding if Pack includes it and `with_branding=true`
  - Support both `replace` and `merge` modes
  - Returns `pack_type: "agency"` in response
- **Backwards compatible:** Existing `industry_pack` parameter still works

### Files Modified

#### Admin/UI Files
- **Includes/class-sfb-admin.php** (lines 136-147)
  - Added Agency Library submenu registration
  - License-gated visibility check

- **submittal-form-builder.php** (lines 2071-2212)
  - Added `render_agency_library_page()` method
  - Pack table rendering
  - Delete action handler
  - Empty state UI

- **assets/admin.js** (lines 1582-1588, 1827-1865, 2574-2578, 2920-2998)
  - Added `packModal` state management
  - Implemented `saveAsPack()` function
  - Added "Save as Pack" button to toolbar
  - Created complete modal component

#### API/Backend Files
- **submittal-form-builder.php** (lines 6030-6037, 6602-6683)
  - Registered `/pack/save` REST endpoint
  - Implemented `api_save_pack()` handler
  - Gets all nodes from database
  - Strips notes if `include_notes=false`
  - Includes branding if `include_branding=true`
  - Returns Pack metadata (not full data for performance)

- **submittal-form-builder.php** (lines 5066-5107)
  - Added `ajax_export_pack()` AJAX handler
  - Nonce verification
  - JSON file download with headers
  - Sanitized filename generation

- **Includes/class-sfb-ajax.php** (lines 55-78)
  - Added `register_agency_pack_hooks()` method
  - Registered `sfb_pack_export` AJAX action

#### Seeder Modifications
- **submittal-form-builder.php** (lines 6159-6260)
  - Added `agency_pack_id` parameter handling
  - Early return for Agency Pack import
  - Node ID remapping with `$id_map` array
  - Branding application if Pack includes it
  - Count tracking by node type
  - Returns `pack_type: "agency"` response

#### Frontend Integration
- **templates/frontend/builder.php** (line 5753-5759)
  - Exposed `isAgency` flag via `wp_localize_script`
  - JavaScript now knows current user's Agency status

### Documentation Updates

#### FEATURE-INVENTORY.md
- Added "Agency Features" section (lines 242-308)
  - Complete feature description
  - Save as Pack workflow
  - Agency Library page details
  - Seeder integration notes
- Updated Admin Pages section (lines 592-598)
  - Added Agency Library page entry

#### API-REFERENCE.md
- Updated header to mention 29 endpoints (not 27)
- Added "Agency Features (Agency)" to Table of Contents
- Updated `/form/seed` documentation:
  - Added `agency_pack_id` parameter
  - Documented both request/response formats
  - Explained Pack vs Industry Pack differences
- Added complete Agency Features section:
  - `POST /pack/save` REST endpoint documentation
  - `AJAX: sfb_pack_export` handler documentation
  - Security details and examples
  - Integration with seeder workflow

#### WEBSITE-DOCUMENTATION.md
- Added "Agency Features" to Table of Contents
- Created comprehensive "Agency Features" section (lines 675-916):
  - What is an Agency Pack?
  - Agency Library usage guide
  - Creating Packs workflow
  - Exporting Packs
  - Deploying to client sites
  - Managing Packs
  - Best practices
  - Real-world workflow example
  - Agency license requirements

### Technical Details

#### License Gating
- **Function:** `sfb_is_agency_license()`
- **Dev override:** `SFB_AGENCY_DEV` constant
- **Checks performed:**
  - Admin menu registration
  - REST endpoint permission callbacks
  - AJAX handler security checks
  - UI button visibility (JavaScript)

#### ID Remapping Strategy
When importing Agency Packs, node IDs must be remapped to avoid conflicts:
```php
$id_map = []; // old_id => new_id
foreach ($nodes as $node) {
  $old_id = $node['id'];
  $new_parent = isset($id_map[$node['parent_id']])
    ? $id_map[$node['parent_id']]
    : null;

  $wpdb->insert($nodes_table, [/* ... */]);
  $new_id = $wpdb->insert_id;
  $id_map[$old_id] = $new_id;
}
```

#### Data Format Differences
- **Industry Packs:** Use `categories[]` structure with nested hierarchy
- **Agency Packs:** Use flat `nodes[]` array with `parent_id` references
- **Seeder:** Detects format and uses appropriate import logic

#### Performance Considerations
- Pack save response excludes full data (only metadata returned)
- Export streams JSON directly to download (no intermediate storage)
- Node counts tracked during import (no separate query needed)
- Branding settings stored as separate key in Pack data

### Security Considerations

1. **REST Endpoint:**
   - Requires `manage_options` capability
   - Agency license validation
   - Input sanitization (Pack name)

2. **AJAX Export:**
   - Nonce verification per Pack ID
   - Admin capability check
   - Agency license validation
   - Pack ID sanitization

3. **Pack Data:**
   - No user credentials stored
   - No license keys included
   - Branding settings sanitized on save
   - Node data validated on import

### Testing Performed

✅ Save Pack with branding and notes
✅ Save Pack without branding
✅ Save Pack without notes
✅ Export Pack as JSON download
✅ Delete Pack from Library
✅ Import Pack on another site (seeder integration)
✅ ID remapping on import (no conflicts)
✅ Branding application on import
✅ Agency license gating (UI hidden when not Agency)
✅ REST endpoint permissions
✅ AJAX nonce verification
✅ Empty state UI display

### Known Limitations

1. **No Pack Renaming:** Must delete and recreate with new name
2. **No Pack Import UI:** Must manually upload JSON and use seeder
3. **No Pack Versioning:** Manual version tracking in Pack names recommended
4. **No Pack Preview:** Can't preview contents before importing
5. **No Partial Import:** All-or-nothing import (can't select specific products)

### Future Enhancements (Not Implemented)

- Pack import UI (drag & drop JSON upload)
- Pack rename functionality
- Pack version history
- Pack preview/diff tool
- Selective product import from Packs
- Pack templates/categories
- Pack sharing between Agency accounts
- Pack marketplace/directory

### Acceptance Criteria Status

✅ **Save current catalog as Pack**
- Button in Builder toolbar
- Modal with name and options
- Stores in `sfb_agency_packs`

✅ **View Packs in Agency Library**
- Dedicated admin page
- Table with Pack details
- Empty state for new users

✅ **Export Pack as JSON**
- Export button in Library
- Downloads as JSON file
- Includes all Pack data

✅ **Seed Pack on another site**
- Seeder accepts `agency_pack_id`
- Remaps IDs on import
- Applies branding if requested
- Works with replace/merge modes

### Deployment Notes

**No database migrations required** - Uses WordPress options API

**No new tables** - All data stored in `sfb_agency_packs` option

**Backwards compatible** - Existing seeder calls continue to work

**Agency license required** - Features completely hidden without Agency license

### Related Files

See implementation details in:
- `Includes/class-sfb-admin.php:136-147`
- `submittal-form-builder.php:2071-2212` (Library page)
- `submittal-form-builder.php:5066-5107` (Export handler)
- `submittal-form-builder.php:6030-6037, 6602-6683` (Save endpoint)
- `submittal-form-builder.php:6159-6260` (Seeder integration)
- `assets/admin.js:1582-1588, 1827-1865, 2574-2578, 2920-2998`
- `Includes/class-sfb-ajax.php:55-78`
- `templates/frontend/builder.php:5753-5759`

---

**Implementation Date:** October 11, 2025
**Implemented By:** Phase A - Agency Library Feature
**Feature Status:** ✅ Complete and Ready for Production
