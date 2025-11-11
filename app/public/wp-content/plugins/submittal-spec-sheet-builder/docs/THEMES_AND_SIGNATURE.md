# PDF Themes, Watermarks & Approval Signature Block

Quick reference for using PDF themes, watermarks, and approval signature features.

**Last Updated:** 2025-01-27
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

## PDF Watermark (Pro)

### Overview
Pro and Agency users can add a custom text watermark that appears diagonally across all pages of generated PDFs. Perfect for marking documents as "DRAFT", "CONFIDENTIAL", "FOR REVIEW ONLY", or any custom text.

### Visual Appearance

```
┌─────────────────────────────────────────────────────────┐
│                                                           │
│                    D R A F T                              │
│                  (diagonal, semi-transparent)             │
│          [Your PDF content remains readable below]        │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

**Characteristics:**
- Diagonal orientation (−20° rotation)
- Semi-transparent (6% opacity = rgba(0,0,0,0.06))
- Large font size (64px)
- Fixed positioning at 38% from top
- Centered horizontally
- Appears on ALL pages (cover, TOC, summary, model sheets)

### How to Enable

**For Users:**
1. Navigate to **Settings → Branding**
2. Scroll to **"PDF Watermark"** section (Pro only)
3. Enter custom watermark text (e.g., "DRAFT", "CONFIDENTIAL", "PRELIMINARY")
4. Leave blank to disable watermark
5. Click **"Save Changes"**
6. Generate a new PDF to see watermark

**Common Watermark Text Examples:**
- `DRAFT` - Work in progress
- `CONFIDENTIAL` - Sensitive information
- `FOR REVIEW ONLY` - Review purposes
- `PRELIMINARY` - Not finalized
- `SAMPLE` - Example document
- `VOID` - Obsolete/cancelled
- `COPY` - Duplicate for reference

**For Developers:**
```php
// Watermark text stored in brand settings
$brand = sfb_get_brand_settings();
$watermark = $brand['watermark'] ?? '';

// Feature gate at PDF generation (submittal-form-builder.php)
if (!sfb_feature_enabled('watermark')) {
  $brand['watermark'] = ''; // Force free users to no watermark
}

// Template rendering (all PDF templates)
if ($watermark !== '') {
  // Render fixed-position diagonal watermark div
}
```

### Implementation Details

**Templates:** All four PDF template files include watermark support:
- templates/pdf/cover.html.php:19-24
- templates/pdf/toc.html.php (if applicable)
- templates/pdf/summary.html.php (if applicable)
- templates/pdf/model-sheet.html.php (if applicable)

**CSS Styling:**
```css
position: fixed;
top: 38%;
left: 10%;
right: 10%;
text-align: center;
font-size: 64px;
color: rgba(0, 0, 0, 0.06); /* 6% opacity */
transform: rotate(-20deg);
z-index: 0; /* Behind content */
```

**Variable Name:** `$watermark` (NOT `watermark_text`)

**Empty String Behavior:**
- If `$watermark === ''`, watermark div is not rendered
- No empty watermark placeholder appears
- Clean conditional rendering

### Styling Options (Customizable by Developers)

**Opacity:**
```php
// Default: 0.06 (6% - very subtle)
color: rgba(0, 0, 0, 0.06);

// More visible: 0.12 (12%)
color: rgba(0, 0, 0, 0.12);

// High contrast: 0.20 (20%)
color: rgba(0, 0, 0, 0.20);
```

**Rotation:**
```php
// Default: -20deg (diagonal upward right)
transform: rotate(-20deg);

// Steeper: -30deg
transform: rotate(-30deg);

// Horizontal: 0deg
transform: rotate(0deg);
```

**Position:**
```php
// Default: 38% from top (center of page)
top: 38%;

// Higher on page: 25%
top: 25%;

// Lower on page: 50%
top: 50%;
```

**Font Size:**
```php
// Default: 64px (large but readable)
font-size: 64px;

// Smaller: 48px
font-size: 48px;

// Larger: 80px
font-size: 80px;
```

### Free Tier Behavior

- **Disabled:** Watermark field hidden or disabled in settings
- **Setting forced:** `watermark` always set to empty string
- **Gate enforcement:** `sfb_feature_enabled('watermark')` returns false
- **No rendering:** Watermark div never appears in PDF
- **Upsell opportunity:** Show watermark setting with "Pro only" badge

### Use Cases

**Draft Documents:**
```
Watermark: "DRAFT"
Purpose: Indicate work in progress, not final
```

**Confidential Submittals:**
```
Watermark: "CONFIDENTIAL"
Purpose: Mark sensitive pricing or proprietary specs
```

**Review Packets:**
```
Watermark: "FOR REVIEW ONLY"
Purpose: Distinguish review copies from official submittals
```

**Preliminary Designs:**
```
Watermark: "PRELIMINARY - NOT FOR CONSTRUCTION"
Purpose: Prevent use of early-stage designs
```

**Sample Documents:**
```
Watermark: "SAMPLE"
Purpose: Demo or training materials
```

### Common Issues

**Issue:** Watermark too light, barely visible
**Solution:** Increase opacity in template CSS (change 0.06 to 0.12 or 0.15)

**Issue:** Watermark obscures important content
**Solution:** Reduce opacity, adjust positioning, or use shorter text

**Issue:** Watermark doesn't appear on all pages
**Solution:** Verify watermark rendering code exists in ALL template files (cover, toc, summary, model-sheet)

**Issue:** Free users can set watermark text
**Solution:** Verify feature gate is enforced at PDF generation time, not just UI level

**Issue:** Watermark text too long, wraps or overflows
**Solution:** Recommend keeping text under 20 characters; implement character limit in UI

### Testing Instructions

1. **Free Tier Test:**
   ```php
   delete_option('sfb_license');
   update_option('sfb_brand_settings', ['watermark' => 'TEST']);
   ```
   - Watermark field should be disabled/hidden in settings
   - Generated PDFs should NOT show watermark
   - Manual setting should be overridden at generation time

2. **Pro Tier Test:**
   ```php
   update_option('sfb_license', [
     'key' => 'test-key',
     'email' => 'test@example.com',
     'status' => 'active'
   ]);
   ```
   - Watermark field should be enabled in settings
   - Test with "DRAFT" text
   - Test with "CONFIDENTIAL" text
   - Test with empty string (no watermark)
   - Test with very long text (should handle gracefully)
   - Verify watermark appears on ALL pages (cover, TOC, summary, each model sheet)
   - Verify watermark is semi-transparent (content visible underneath)
   - Verify watermark is diagonal (~-20° rotation)

### QA Checklist

- [ ] Free users cannot enable watermark
- [ ] Pro users can set custom watermark text
- [ ] Watermark appears on ALL PDF pages
- [ ] Watermark is semi-transparent (content readable beneath)
- [ ] Watermark is diagonal (~-20° rotation)
- [ ] Empty watermark text = no watermark rendered
- [ ] Watermark text escapes HTML properly (no XSS)
- [ ] Long watermark text doesn't break layout
- [ ] Watermark doesn't obscure critical content
- [ ] Watermark styling is consistent across all pages

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

### Testing Watermark

1. **Free Tier Test:**
   ```php
   delete_option('sfb_license');
   ```
   - Watermark field should be disabled/hidden in settings
   - Generated PDFs should NOT show watermark
   - Manually setting watermark in settings should be overridden

2. **Pro Tier Test:**
   ```php
   update_option('sfb_license', [
     'key' => 'test-key',
     'email' => 'test@example.com',
     'status' => 'active'
   ]);
   ```
   - Watermark field should be enabled in settings
   - Test with "DRAFT" text → verify diagonal watermark appears
   - Test with "CONFIDENTIAL" text → verify appears on all pages
   - Test with empty string → verify no watermark
   - Test with very long text (30+ chars) → verify layout remains stable
   - Verify watermark is semi-transparent (can read content beneath)
   - Verify watermark appears on cover, TOC, summary, and each model sheet

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

### PDF Watermark
- [ ] Free users cannot enable watermark
- [ ] Pro users can set custom watermark text
- [ ] Watermark appears on ALL pages (cover, TOC, summary, model sheets)
- [ ] Watermark is diagonal (~-20° rotation)
- [ ] Watermark is semi-transparent (6% opacity)
- [ ] Watermark doesn't obscure critical content
- [ ] Empty watermark text = no watermark rendered
- [ ] Long text (20+ chars) handles gracefully without overflow
- [ ] Watermark text escapes HTML (no XSS vulnerability)
- [ ] Watermark styling is consistent across all pages

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
