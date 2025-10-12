# Phase 6 QA Acceptance Tests

## Overview
Phase 6 centralized PDF generation entry points through the SFB_Pdf facade. All PDF generation now routes through `SFB_Pdf::generate_frontend_pdf()` (AJAX) and `SFB_Pdf::generate_packet()` (REST API).

**Critical Requirement:** PDFs must look and work EXACTLY the same as before. No behavior changes allowed.

---

## Test 1: Frontend Builder PDF Generation (AJAX)

### Goal
Verify that the frontend submittal builder can generate PDFs through the new AJAX facade.

### Steps
1. Navigate to the page with the `[submittal_builder]` shortcode
2. Select 2-3 products from the product selection interface
3. Fill in project details (project name, contact info)
4. Click the "Generate PDF" button
5. Wait for PDF generation to complete

### Expected Results
- ✅ No JavaScript errors in browser console
- ✅ No PHP errors in debug.log
- ✅ AJAX request to `admin-ajax.php?action=sfb_generate_frontend_pdf` succeeds
- ✅ PDF file downloads or opens successfully
- ✅ PDF contains all selected products
- ✅ PDF formatting looks identical to pre-Phase-6 PDFs
- ✅ Cover page renders correctly
- ✅ Table of contents is accurate
- ✅ Product spec sheets display properly
- ✅ Summary page shows correct totals

### How to Verify
- Open browser DevTools Console tab
- Check `wp-content/debug.log` for PHP errors
- Visually compare PDF to a pre-Phase-6 PDF (rollback tag: `pre-refactor-phase-6`)

---

## Test 2: REST API PDF Generation

### Goal
Verify that the REST API endpoint `/wp-json/sfb/v1/generate` generates PDFs through the new facade.

### Steps
1. Open a REST API client (Postman, Insomnia, or curl)
2. Send a POST request to: `https://your-site.local/wp-json/sfb/v1/generate`
3. Include JSON payload with products and metadata:
   ```json
   {
     "products": [
       {"id": 123, "name": "Product A"},
       {"id": 456, "name": "Product B"}
     ],
     "meta": {
       "project_name": "Test Project",
       "contact_name": "John Doe"
     }
   }
   ```
4. Submit the request

### Expected Results
- ✅ HTTP 200 response
- ✅ JSON response contains `success: true`
- ✅ Response includes PDF `url` field
- ✅ Response includes PDF `path` field
- ✅ PDF file exists at the returned path
- ✅ PDF URL is accessible
- ✅ PDF content matches payload (contains specified products)
- ✅ PDF formatting is identical to pre-Phase-6 REST-generated PDFs

### How to Verify
```bash
# Example curl command
curl -X POST https://your-site.local/wp-json/sfb/v1/generate \
  -H "Content-Type: application/json" \
  -d '{"products":[...], "meta":{...}}'
```

- Check response status code and JSON structure
- Visit the returned PDF URL in browser
- Check `wp-content/debug.log` for PHP errors

---

## Test 3: Error Handling

### Goal
Verify that the facade handles errors gracefully when the plugin instance is unavailable.

### Steps
1. Temporarily modify `submittal-form-builder.php` to prevent plugin initialization
2. Attempt to generate a PDF via frontend or REST API
3. Restore the plugin code

### Expected Results
- ✅ Frontend AJAX returns error: "PDF generator not available"
- ✅ REST API returns WP_Error with status 500
- ✅ No fatal PHP errors
- ✅ User-friendly error message displayed

### How to Verify
- Check browser console for error response
- Check REST API response JSON structure
- Verify no white screens or fatal errors

---

## Test 4: PDF File System Integrity

### Goal
Verify PDFs are saved to the correct location with proper permissions.

### Steps
1. Generate a PDF via frontend
2. Generate a PDF via REST API
3. Check the upload directory

### Expected Results
- ✅ PDFs saved to `wp-content/uploads/sfb/`
- ✅ Filename format: `Submittal_Packet_YYYY-MM-DD.pdf`
- ✅ Files are readable (not corrupted)
- ✅ Files have correct permissions (644 or similar)
- ✅ Directory structure matches pre-Phase-6

### How to Verify
```bash
# Check upload directory
ls -la wp-content/uploads/sfb/

# Verify file permissions
stat wp-content/uploads/sfb/Submittal_Packet_*.pdf

# Test PDF readability
file wp-content/uploads/sfb/Submittal_Packet_*.pdf
```

---

## Test 5: Visual Regression Check

### Goal
Ensure PDFs look pixel-perfect identical to pre-Phase-6 versions.

### Steps
1. Checkout tag `pre-refactor-phase-6`
2. Generate a PDF with specific products (note which ones)
3. Checkout master branch
4. Generate a PDF with the SAME products
5. Compare the two PDFs side-by-side

### Expected Results
- ✅ Cover pages are identical
- ✅ Table of contents match
- ✅ Product spec sheets are identical
- ✅ Summary pages match
- ✅ Fonts, colors, spacing unchanged
- ✅ Page breaks occur at same locations
- ✅ Headers/footers are identical

### How to Verify
- Open both PDFs in separate windows
- Compare page-by-page visually
- Use a PDF diff tool if available
- Check CSS classes and inline styles (use browser dev tools on HTML before PDF conversion if needed)

---

## Test 6: Pro Features (If Applicable)

### Goal
Verify that Pro features still work after facade refactoring.

### Steps
1. If Pro features are enabled (theming, watermarks, signatures)
2. Generate a PDF with Pro features active
3. Verify all Pro features render correctly

### Expected Results
- ✅ Custom themes apply correctly
- ✅ Watermarks display properly
- ✅ Signature fields work as expected
- ✅ Pro-only template elements render

### How to Verify
- Compare to pre-Phase-6 Pro PDF
- Check that all premium features are functional

---

## Rollback Plan

If any test fails:

```bash
# Rollback to pre-Phase-6 state
git checkout pre-refactor-phase-6

# Or revert the Phase 6 commit
git revert 7a53180
```

---

## Sign-Off

### Tests Completed By: ___________________
### Date: ___________________
### All Tests Passed: ☐ Yes  ☐ No

### Notes:
_________________________________________
_________________________________________
_________________________________________
