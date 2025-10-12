# Database Cleanup - wp_sfb_shares Table Removal

**Date:** 2025-10-10
**Version:** 1.0.2

## Changes Made

### Removed Orphaned wp_sfb_shares Table

**File:** `submittal-form-builder.php:204-218`

**What Was Removed:**
```php
// Old code (lines 204-218):
$shares = $wpdb->prefix . 'sfb_shares';

$sql_shares = "
  CREATE TABLE $shares (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    form_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL,
    payload_json LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    PRIMARY KEY  (id),
    KEY form_id (form_id),
    UNIQUE KEY token (token)
  ) $charset;
";
dbDelta($sql_shares);
```

**Replaced With:**
```php
// Line 204-205:
// 3) Shares table REMOVED - Shareable Drafts uses custom post type 'sfb_draft' instead
// Legacy table removed in v1.0.2 - was created but never used
```

**Also Removed:**
- Variable declaration: `$shares = $wpdb->prefix . 'sfb_shares';` (line 159)

---

## Why This Change?

### Problem
The `wp_sfb_shares` table was created on plugin activation but **never actually used** by the Shareable Drafts feature.

### Analysis
1. ✅ **Table created** - Code existed to create the table
2. ❌ **Table never used** - Zero SQL queries reference it
3. ✅ **Custom post type used instead** - All draft operations use `sfb_draft` post type with post meta
4. ✅ **30+ references to custom post type** - Fully implemented and working

### Evidence
```bash
# Search for table usage - ZERO results
grep -r "INSERT INTO.*sfb_shares" .
grep -r "SELECT.*FROM.*sfb_shares" .
grep -r "wp_sfb_shares" . | grep -v "\.md"  # Only in documentation

# Search for custom post type - 30+ results
grep -r "sfb_draft" . | wc -l
# Result: 30+
```

### Decision: Remove Table
**Reasons:**
1. Custom post type design is **superior** for WordPress:
   - Leverages native WordPress APIs (no raw SQL)
   - Automatic cleanup via `uninstall.php`
   - Better integration with WordPress ecosystem
   - Easier to query and maintain

2. Migration would require rewriting 30+ code references with zero benefit

3. Table serves no purpose and creates confusion

---

## What Shareable Drafts Actually Uses

### Custom Post Type: `sfb_draft`
**Registration:** `submittal-form-builder.php:6206-6212`

```php
register_post_type('sfb_draft', [
  'public' => false,
  'show_ui' => false,
  'show_in_rest' => false,
  'supports' => [],
  'capability_type' => 'post',
]);
```

### Post Meta Storage
**Draft data stored in post meta:**
- `_sfb_draft_id` - The 12-char token (e.g., "aBc123XyZ456")
- `_sfb_draft_payload` - Selected items JSON
- `_sfb_draft_created_at` - Creation timestamp
- `_sfb_draft_expires_at` - Expiry timestamp

### Draft Operations
**Code locations:**
- Create: `submittal-form-builder.php:6400-6420`
- Read: `submittal-form-builder.php:6438-6456`
- Update: `submittal-form-builder.php:6506-6529`
- Delete: `submittal-form-builder.php:6791-6801`
- Purge: `submittal-form-builder.php:6222-6242` (cron job)

---

## Cleanup on Uninstall

**File:** `uninstall.php:178`

The table is already listed for cleanup when plugin is deleted:

```php
$tables = [
    $wpdb->prefix . 'sfb_forms',
    $wpdb->prefix . 'sfb_nodes',
    $wpdb->prefix . 'sfb_shares',  // ← Cleanup handled
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
}
```

**Note:** This cleanup will handle any existing `wp_sfb_shares` tables created before v1.0.2.

---

## Documentation Updates

### FEATURE-STATUS.md
**Updated sections:**
1. **Header:** Updated "Last Updated" to reflect database cleanup
2. **Database Tables:** Removed `wp_sfb_shares` row, added custom post type note
3. **Shareable Drafts:** Changed from "⚠️ Uses post type instead of table" to "✅ Uses custom post type for better WordPress integration"
4. **Summary Statistics:** Changed from "3/4 tables actively used (1 orphaned)" to "3/3 tables actively used"
5. **Action Items:** Marked "Fix Database Inconsistency" as completed

### FEATURE-INVENTORY.md
**Updated sections:**
1. **Shareable Drafts feature:** Added note about custom post type usage
2. **Database Tables section:** Removed entire wp_sfb_shares table documentation
3. **Summary Statistics:** Changed from "4 tables" to "3 tables (wp_sfb_shares removed in v1.0.2)"

---

## Impact Assessment

### ✅ No Breaking Changes
- Feature continues to work exactly as before
- All draft operations use custom post type (no change)
- Existing installations: Table will be dropped on uninstall (if present)

### ✅ No Migration Needed
- Nothing to migrate (table was never used)
- No data loss (no data was ever stored in table)

### ✅ Better Code Clarity
- Removes confusion about which storage method is used
- Aligns code with actual implementation
- Cleaner database schema

---

## Files Modified

1. **submittal-form-builder.php**
   - Lines 159: Removed `$shares` variable declaration
   - Lines 204-218: Removed table creation code, added explanatory comment

2. **FEATURE-STATUS.md**
   - Line 5: Updated "Last Updated" field
   - Lines 465-478: Updated database tables section
   - Line 310: Updated Shareable Drafts note
   - Lines 633-635: Updated infrastructure statistics
   - Lines 653-657: Marked action item as completed

3. **FEATURE-INVENTORY.md**
   - Lines 155-157: Added custom post type note to Shareable Drafts
   - Lines 331-356: Removed wp_sfb_shares table documentation
   - Lines 775-776: Updated infrastructure statistics

---

## Testing Checklist

- [x] Table creation code removed from activation
- [x] No references to table in code (except uninstall cleanup)
- [x] Shareable Drafts still works via custom post type
- [x] Uninstall cleanup still drops table (if exists)
- [x] Documentation updated to reflect change
- [x] No breaking changes introduced

---

## Conclusion

This cleanup removes technical debt by eliminating an orphaned database table that was created but never used. The Shareable Drafts feature continues to function correctly using the custom post type `sfb_draft`, which is the superior WordPress-native approach.

**Result:** Cleaner codebase, clearer documentation, no functional changes.
