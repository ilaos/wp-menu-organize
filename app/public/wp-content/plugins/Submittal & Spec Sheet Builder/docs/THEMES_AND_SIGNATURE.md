# PDF Themes & Approval Signature Block

Quick reference for using PDF themes and approval signature features.

**Last Updated:** 2025-10-13
**Features:** Pro tier only
**Implementation Status:** ✅ Fully implemented

---

## PDF Themes (Pro)

### Overview
Pro and Agency users can select from three PDF color themes that apply across all PDF templates (cover, TOC, summary, and model sheets).

### Available Themes

| Theme          | Color      | Hex Code  | Applied To                              |
|----------------|------------|-----------|-----------------------------------------|
| **Engineering** (default) | Dark Gray/Custom | #111827 or primary_color | H2 headers, table headers, borders |
| **Architectural** | Sky Blue   | #0ea5e9   | H2 headers, table headers, borders     |
| **Corporate**     | Emerald Green | #10b981 | H2 headers, table headers, borders   |

### How to Toggle Themes

**For Users:**
1. Navigate to **Branding** settings page
2. Select theme from dropdown (Free users see only "Engineering")
3. Save settings
4. Generate a new PDF to see the theme applied

**For Developers:**
```php
// Theme is stored in brand settings
$brand = sfb_get_brand_settings();
$theme = $brand['theme'] ?? 'engineering';

// Theme gate (enforced at PDF generation)
if (!sfb_feature_enabled('themes')) {
  $brand['theme'] = 'engineering'; // Force free users to default
}
```

### Visual Differences

**Engineering** (Free/Default):
- Uses primary brand color or dark gray (#111827)
- Professional, neutral appearance
- Default for all free tier users

**Architectural** (Pro):
- Sky blue accent color (#0ea5e9)
- Light, modern aesthetic
- Suitable for architecture, design, tech industries

**Corporate** (Pro):
- Emerald green accent color (#10b981)
- Fresh, professional appearance
- Suitable for sustainability, healthcare, corporate sectors

### Where Themes Appear

Themes affect color styling in these PDF elements:

1. **Cover Page**
   - LEED badge border and text color (cover.html.php:59-60)

2. **Table of Contents**
   - Section heading color and border-bottom (toc.html.php:26)

3. **Summary Page**
   - Category heading color and border-bottom (summary.html.php:39)
   - Table header backgrounds (summary.html.php:53, 56)

4. **Model Sheets** (Product Pages)
   - Product title color (model-sheet.html.php:32)
   - Specification table header backgrounds (model-sheet.html.php:61, 64)

### Implementation Files

- **Gate:** submittal-form-builder.php:8585-8587
- **Templates:**
  - templates/pdf/cover.html.php:15-21
  - templates/pdf/toc.html.php:15-21
  - templates/pdf/summary.html.php:17-20
  - templates/pdf/model-sheet.html.php:17-22

### Free Tier Behavior

- **Locked to:** Engineering theme (default)
- **Cannot change:** Theme selector disabled or hidden in UI
- **Enforcement:** Gate at PDF generation time forces `theme = 'engineering'`
- **Upsell opportunity:** Show theme selector with "Pro only" badge

---

## Approval Signature Block (Pro)

### Overview
Pro and Agency users can add a professional approval signature block to the end of each model sheet (product page) in generated PDFs.

### Visual Layout

```
┌─────────────────────────────────────────────────────────────┐
│ APPROVED BY              │ TITLE              │ DATE         │
│ ─────────────────────    │ ─────────────────  │ ─────────── │
│ [approve_name]           │ [approve_title]    │ [approve_date]│
└─────────────────────────────────────────────────────────────┘
```

### How to Enable

**For Users:**
1. Navigate to **Frontend Builder** (or Review page in app)
2. Check **"Include Approval Signature Block"** option
3. Fill in fields:
   - **Approved By:** Name of approver
   - **Title:** Job title or role
   - **Date:** Approval date
4. Generate PDF
5. Signature block appears at end of each model sheet

**For Developers:**
```php
// Signature data stored in $meta array
$meta = [
  'approve_block' => true,           // Enable/disable signature
  'approve_name'  => 'John Doe',     // Approver name
  'approve_title' => 'Project Manager', // Job title
  'approve_date'  => '2025-10-13',   // Approval date
];

// Gate at PDF generation (submittal-form-builder.php:8580-8582)
if (!sfb_feature_enabled('signature')) {
  $meta['approve_block'] = false; // Force free users to false
}

// Template conditional (model-sheet.html.php:95)
if (sfb_is_pro_enabled() && !empty($meta['approve_block'])) {
  // Render signature block
}
```

### Implementation Details

**Template:** templates/pdf/model-sheet.html.php:95-132

**Structure:**
- 3-column table layout
- Column widths: 40% / 35% / 25%
- Inline CSS (required for DomPDF)
- Page-break-inside:avoid (prevents split across pages)
- Min-height:930px on .model-content wrapper (ensures signature doesn't orphan)

**Styling Classes:**
- `.sig-wrap` - Outer wrapper with top margin
- `.sig-table` - Table with borders
- `.sig-label` - Uppercase label text (10px, gray)
- `.sig-line` - Bottom-bordered value area (18px min-height)

**Variable Names:**
- `$meta['approve_name']` - NOT `approved_by`
- `$meta['approve_title']` - NOT `approved_title`
- `$meta['approve_date']` - NOT `approved_date`

### Free Tier Behavior

- **Hidden:** Signature block does not render
- **Setting disabled:** `approve_block` forced to `false` at generation time
- **UI state:** Setting checkbox disabled or hidden for free users
- **Upsell opportunity:** Show setting with "Pro only" badge

### Placement & Flow

1. Signature block appears **after** product specs table and notes
2. Signature block appears **before** footer text (if present)
3. Uses `page-break-inside:avoid` to prevent split
4. `.model-content` wrapper has `min-height:930px` to ensure space
5. Each model sheet gets its own signature block (repeated per product)

### Common Issues

**Issue:** Signature splits across pages on short products
**Solution:** Min-height:930px on .model-content ensures adequate space

**Issue:** Signature not showing
**Solution:** Verify Pro license active, `approve_block` is true, and all three fields populated

**Issue:** Using wrong variable names
**Solution:** Use `approve_name/approve_title/approve_date` not `approved_*` variants

---

## Testing Instructions

### Testing PDF Themes

1. **Free Tier Test:**
   ```php
   delete_option('sfb_license');
   ```
   - Branding page theme selector should be disabled/hidden
   - Generated PDFs should use engineering theme only
   - Attempting to set other themes in code should be overridden

2. **Pro Tier Test:**
   ```php
   update_option('sfb_license', [
     'key' => 'test-key',
     'email' => 'test@example.com',
     'status' => 'active'
   ]);
   ```
   - Branding page theme selector should be enabled
   - Test all three themes (engineering, architectural, corporate)
   - Verify color changes in headers, tables, borders across all PDF pages
   - Compare PDFs side-by-side to confirm obvious visual differences

### Testing Signature Block

1. **Free Tier Test:**
   ```php
   delete_option('sfb_license');
   ```
   - Review page signature checkbox should be disabled/hidden
   - Generated PDFs should NOT show signature block
   - Manually setting `approve_block` in code should be overridden

2. **Pro Tier Test:**
   ```php
   update_option('sfb_license', [
     'key' => 'test-key',
     'email' => 'test@example.com',
     'status' => 'active'
   ]);
   ```
   - Review page signature checkbox should be enabled
   - Test with all three fields populated
   - Test with empty fields (should show empty signature lines)
   - Test with short product (1-2 specs) - verify no page break split
   - Test with long product (20+ specs) - verify signature at end
   - Verify signature appears on EACH model sheet, not just first/last

---

## QA Checklist

### PDF Themes
- [ ] Free users locked to engineering theme
- [ ] Pro users can select all three themes
- [ ] Architectural theme shows sky blue (#0ea5e9)
- [ ] Corporate theme shows emerald green (#10b981)
- [ ] Theme colors apply to cover page LEED badge
- [ ] Theme colors apply to TOC headings
- [ ] Theme colors apply to summary headings and table headers
- [ ] Theme colors apply to model sheet product titles and table headers
- [ ] Visual differences are "obvious" (not subtle)
- [ ] No layout regressions across themes

### Approval Signature Block
- [ ] Free users cannot enable signature block
- [ ] Pro users can enable signature block via checkbox
- [ ] Signature block renders at end of each model sheet
- [ ] Signature block does NOT split across pages (page-break-inside:avoid)
- [ ] Three columns display correctly (Approved By, Title, Date)
- [ ] Labels are uppercase and styled correctly
- [ ] Signature lines have bottom borders
- [ ] Empty fields show empty signature lines (not hidden)
- [ ] Signature appears AFTER specs table and notes
- [ ] Signature appears BEFORE footer text
- [ ] Min-height on .model-content prevents orphaning on short pages

---

## Developer Notes

### Extending Themes

To add a new theme:

1. Add theme option to branding settings UI
2. Update theme color logic in all four PDF templates:
   ```php
   $bar = ($theme === 'architectural') ? '#0ea5e9' :
          (($theme === 'corporate')    ? '#10b981' :
          (($theme === 'your_theme')   ? '#yourcolor' : $accent));
   ```
3. Test across all PDF pages (cover, toc, summary, model-sheet)
4. Update this documentation with new theme details

### Extending Signature Block

To add fields to signature block:

1. Add new `$meta['approve_*']` fields to app.js settings
2. Update PDF template table to add columns
3. Adjust column widths (ensure total = 100%)
4. Update this documentation with new field names

### DomPDF Constraints

- **No JavaScript:** All styling must be inline CSS or `<style>` blocks
- **Limited CSS:** Avoid flexbox, grid, advanced positioning
- **Page breaks:** Use `page-break-inside:avoid` or `page-break-after:always`
- **Fixed positioning:** Only for watermarks, not flow content
- **Images:** Must be base64-encoded or absolute file paths

---

## Support

For questions or issues:
- **Documentation:** See [FEATURE-INVENTORY.md](../FEATURE-INVENTORY.md)
- **Technical:** See [tier_map.json](tier_map.json) for implementation details
- **Marketing:** See [marketing_bullets.md](marketing_bullets.md) for feature copy

---

_Generated: 2025-10-13 • Submittal & Spec Sheet Builder v1.0.0_
