# Branding & Credit Inventory

**Plugin:** Submittal & Spec Sheet Builder
**Audit Date:** 2025-10-12
**Purpose:** Document all branding/credit mentions to enable centralized management via `sfb_brand_credit()` helper

---

## Summary

This document inventories every location where the plugin displays branding, credits, or attribution text. The goal is to centralize all credits through the `sfb_brand_credit()` helper function so they can be controlled by a single white-label toggle in the future.

**Current State:**
- ✅ Helper functions created (`sfb_brand_credit()`, `sfb_brand_credit_plain()`, `sfb_is_white_label_enabled()`)
- ✅ Minimal CSS added for all credit contexts
- ⚠️ Mixed implementation (some hardcoded, some using conditionals, some using helpers)
- ⚠️ White-label stub exists but always returns `false` (needs Agency license integration)

---

## 1. PDF Generation

### 1.1 Cover Page Footer
**File:** `Includes/pdf-generator.php:667-687`
**Current Credit:** "Generated using Submittal & Spec Sheet Builder for WordPress"
**Context:** Bottom of PDF cover page (1st page)
**Has White-Label Check:** ✅ Yes (`sfb_is_pro_active() && get_option('sfb_white_label_enabled')`)
**Action:** ✅ Keep as-is (already has proper conditional logic)

### 1.2 Table of Contents Attribution
**File:** `Includes/pdf-generator.php:745-754`
**Current Credit:** "Generated using Submittal & Spec Sheet Builder for WordPress"
**Context:** Bottom of Table of Contents page
**Has White-Label Check:** ✅ Yes (`!sfb_is_pro_active() || !get_option('sfb_white_label_enabled')`)
**Action:** ✅ Keep as-is (already has proper conditional logic)

### 1.3 PDF Page Footer (All Pages)
**File:** `Includes/pdf-generator.php:948-977`
**Current Credit:** "Generated using Submittal & Spec Sheet Builder for WordPress"
**Context:** Fixed footer on every PDF page (appears in DomPDF script)
**Has White-Label Check:** ⚠️ Partial (checks `$pro_active && !empty($branding['footer_text'])`)
**Action:** 🔧 **CENTRALIZE** - Replace hardcoded fallback with `sfb_brand_credit_plain('pdf')`

**Change Required:**
```php
// BEFORE (line 954)
$footerText = __('Generated using Submittal & Spec Sheet Builder for WordPress', 'submittal-builder');

// AFTER
$footerText = sfb_brand_credit_plain('pdf');
```

### 1.4 PDF Templates (New System)
**Files:**
- `templates/pdf/cover.html.php:83-88`
- `templates/pdf/summary.html.php:94-98`
- `templates/pdf/toc.html.php:45-50`
- `templates/pdf/model-sheet.html.php:113-118`

**Current Credit:** Uses `$brand['footer_text']` variable
**Context:** New template-based PDF system
**Has White-Label Check:** ✅ Yes (uses branding variable correctly)
**Action:** ✅ Keep as-is (no hardcoded credits, uses variable)

---

## 2. Emails

### 2.1 Lead Confirmation Email
**File:** `Includes/lead-capture.php:121-157`
**Current Credit:** ❌ None
**Context:** Plain-text email sent to users after lead capture
**Has White-Label Check:** ❌ N/A
**Action:** 🔧 **ADD NEW** - Optional subtle credit in email footer

**Change Required:**
```php
// AFTER line 157 (end of message)
// Add small, tasteful credit for Free tier
if (!sfb_is_white_label_enabled()) {
  $message .= "\n\n---\n" . sfb_brand_credit_plain('email');
}
```

### 2.2 Admin Notification Email
**File:** Not implemented
**Current Credit:** ❌ N/A
**Action:** ❌ None (feature doesn't exist yet)

---

## 3. Front-End

### 3.1 Review Step - Brand Preview
**File:** `templates/frontend/partials/step-products.php`
**Current Credit:** ❌ None
**Context:** Review step where users see their selections
**Has White-Label Check:** ❌ N/A
**Action:** 🔧 **ADD NEW** - Small credit below "Generate PDF" button

**Change Required:**
```php
// After Generate PDF button
<div class="sfb-frontend-credit">
  <?php echo sfb_brand_credit('frontend'); ?>
</div>
```

### 3.2 Success Page
**File:** `templates/frontend/partials/step-generate.php` (if exists)
**Current Credit:** ❌ None
**Context:** Success page after PDF generation
**Has White-Label Check:** ❌ N/A
**Action:** 🔧 **ADD NEW** - Optional subtle credit on success page

### 3.3 Public Tracking Page
**File:** Not implemented
**Current Credit:** ❌ N/A
**Action:** 🔧 **ADD WHEN IMPLEMENTED** - Footer credit with `sfb_brand_credit('tracking')`

### 3.4 Frontend Header (Help Link)
**File:** `templates/frontend/partials/header.php:58-62`
**Current Credit:** "Need help?" link to documentation
**Context:** Header navigation on all builder pages
**Has White-Label Check:** ❌ No
**Action:** ✅ Keep as-is (helpful documentation link, not intrusive branding)

---

## 4. Admin Pages

### 4.1 Branding Settings Page
**File:** Admin templates (various)
**Current Credit:** ❌ None
**Context:** Admin settings pages
**Has White-Label Check:** ❌ N/A
**Action:** 🔧 **ADD NEW** - Small footer credit on Branding and Demo Tools pages

**Change Required:**
```php
// At bottom of branding settings template
<div class="sfb-admin-footer">
  <?php echo sfb_brand_credit('admin'); ?>
</div>
```

### 4.2 Admin Footer
**File:** Not implemented (uses WP default)
**Current Credit:** ❌ Uses WordPress default
**Action:** ❌ None (keep WordPress default footer)

---

## 5. CSV Exports

### 5.1 Lead Export CSV
**File:** `Includes/lead-capture.php:266-302`
**Current Credit:** ❌ None (just data columns)
**Context:** Admin-only CSV export of captured leads
**Has White-Label Check:** ❌ N/A
**Action:** 🔧 **ADD NEW** - Optional comment line or filename suffix

**Change Required:**
```php
// OPTION A: Add comment line at end of CSV (if CSV readers support it)
if (!sfb_is_white_label_enabled()) {
  fputcsv($output, ['# ' . sfb_brand_credit_plain('generic')]);
}

// OPTION B: Append to filename
$filename = 'sfb-leads-' . date('Y-m-d') . '-export.csv';
// Keep simple filename, don't add branding
```

**Recommendation:** Skip CSV branding (data exports should be clean)

---

## 6. JavaScript

### 6.1 Review JavaScript
**File:** `assets/js/review.js`
**Current Credit:** ❌ None
**Context:** Frontend JavaScript for review step
**Action:** ❌ None (no user-facing text in JS)

---

## 7. Implementation Summary

### Changes Required

| Priority | Location | Action | Status |
|----------|----------|--------|--------|
| **P1** | `Includes/pdf-generator.php:954` | Centralize footer text via helper | ⏳ Pending |
| **P2** | `templates/frontend/partials/step-products.php` | Add review page credit | ⏳ Pending |
| **P3** | `Includes/lead-capture.php:157` | Add email footer credit | ⏳ Pending |
| **P4** | Admin branding template | Add admin page footer | ⏳ Pending |
| **P5** | CSV export | Skip (keep clean) | ✅ Decision: No branding |

### No Changes Needed

| Location | Reason |
|----------|--------|
| PDF Cover Page (pdf-generator.php:667-687) | Already has white-label conditional |
| PDF TOC Attribution (pdf-generator.php:745-754) | Already has white-label conditional |
| PDF Templates (templates/pdf/*.php) | Uses branding variable correctly |
| Frontend Help Link (partials/header.php:58-62) | Documentation link, not branding |

---

## 8. Helper Function Reference

### Available Functions

```php
// HTML credit (for web pages)
sfb_brand_credit($context = 'generic')
// Returns: <span class="sfb-credit sfb-credit--{$context}">Generated with Submittal & Spec Sheet Builder</span>

// Plain-text credit (for emails, PDFs, plain text)
sfb_brand_credit_plain($context = 'generic')
// Returns: "Generated with Submittal & Spec Sheet Builder"

// White-label check (stub for future)
sfb_is_white_label_enabled()
// Returns: false (always, until Agency license + toggle implemented)
```

### Context Values

| Context | Usage | CSS Class |
|---------|-------|-----------|
| `pdf` | PDF footer text | `.sfb-credit--pdf` |
| `email` | Email footers | `.sfb-credit--email` |
| `frontend` | Review/success pages | `.sfb-credit--frontend` |
| `tracking` | Public tracking page | `.sfb-credit--tracking` |
| `admin` | Admin page footers | `.sfb-credit--admin` |
| `generic` | Fallback/other | `.sfb-credit` |

---

## 9. White-Label Feature (Future)

### Current Stub
```php
function sfb_is_white_label_enabled() {
  // Future: Check for Agency license + white-label toggle
  // For now, always return false (show credits)
  return false;
}
```

### Future Implementation
```php
function sfb_is_white_label_enabled() {
  // Check Agency license exists
  if (!class_exists('SFB_Branding') || !SFB_Branding::is_agency_license()) {
    return false;
  }

  // Check white-label toggle enabled
  return (bool) get_option('sfb_white_label_enabled', false);
}
```

---

## 10. Testing Checklist

Before marking this ticket complete, verify:

- [ ] PDF cover page shows credit (Free tier)
- [ ] PDF cover page hides credit (Pro with white-label)
- [ ] PDF page footer shows credit on every page (Free tier)
- [ ] PDF page footer respects white-label setting
- [ ] Email confirmation includes small footer credit (Free tier)
- [ ] Review page shows credit below Generate button (Free tier)
- [ ] Admin pages show footer credit (all tiers)
- [ ] CSV exports remain clean (no branding)
- [ ] All credits use centralized helper functions
- [ ] White-label stub returns false correctly
- [ ] CSS styles apply correctly to all credit contexts

---

## External Links Inventory

All external links point to `https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/`:

1. PDF Cover Page Credit (pdf-generator.php:680)
2. PDF TOC Attribution (pdf-generator.php:749)
3. Frontend Help Link (partials/header.php:58)

**Action:** ✅ Keep as-is (appropriate documentation/support links)

---

**Document Version:** 1.0
**Last Updated:** 2025-10-12
**Status:** Audit Complete, Implementation In Progress
