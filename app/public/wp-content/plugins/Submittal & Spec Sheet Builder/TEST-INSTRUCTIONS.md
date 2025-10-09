# Frontend Builder MVP - Test Instructions

## Overview
The new 3-step frontend builder is ready for testing. This replaces the old form UI with a modern, mobile-first guided flow.

## What Changed
- **New Template**: `templates/frontend/builder.php` and partials
- **New CSS**: `assets/css/frontend.css` (mobile-first, responsive)
- **New JS**: `assets/js/frontend.js` (state machine, AJAX)
- **AJAX Handlers**: `sfb_list_products`, `sfb_generate_frontend_pdf`
- **Shortcode Updated**: Now loads new template instead of old form

## Test Steps

### 1. Add Products (Required)
First, make sure you have some products in your database:
- Go to **WP Admin → Submittal Builder → Products**
- Add at least 3-5 products with names, SKUs, and specs
- Assign them to different categories if possible

### 2. Add Shortcode to Page
- Create or edit a WordPress page/post
- Add the shortcode: `[submittal_builder]`
- Publish and view the page

### 3. Test Step 1: Pick Products
**Mobile (320px-640px):**
- Open browser dev tools (F12)
- Toggle device toolbar (Ctrl+Shift+M)
- Resize to 375px width (iPhone)

**Check:**
- [ ] Products load and display in grid (1 column on mobile)
- [ ] Search box filters products by name/SKU
- [ ] Categories filter products when clicked
- [ ] "Clear all filters" button resets filters
- [ ] Clicking "+ Add" selects product (button turns green "✓ Added")
- [ ] Selected products tray appears at bottom with count
- [ ] "Continue to Review" button enables when products selected

### 4. Test Step 2: Review & Brand
**Click "Continue to Review"**

**Check:**
- [ ] Progress pills update (Step 2 active, Step 1 complete)
- [ ] Selected products list shows all chosen products
- [ ] "Remove" button removes products from list
- [ ] Brand preview shows company name/logo (if set)
- [ ] Project name and notes fields accept input
- [ ] "Back" button returns to Step 1
- [ ] "Generate PDF" button is clickable

### 5. Test Step 3: Generate
**Click "Generate PDF"**

**Check:**
- [ ] Loading overlay appears with spinner
- [ ] After 2-10 seconds, Step 3 appears
- [ ] Success checkmark and message display
- [ ] "Open PDF" button opens PDF in new tab
- [ ] PDF contains selected products
- [ ] "Start Over" button returns to Step 1 with confirmation

### 6. Test Responsive Breakpoints
**Tablet (640px-1024px):**
- Products grid: 2 columns
- Review layout: Cards stack properly

**Desktop (1024px+):**
- Products grid: 3 columns
- Review layout: Sidebar layout with brand preview

### 7. Test Edge Cases
- [ ] Select 0 products → "Continue" button stays disabled
- [ ] Remove all products in review → Empty state shows
- [ ] Search with no results → "No products found" message
- [ ] Generate PDF with no products → Alert prevents action

## Expected Behavior

### Step Flow
1. **Step 1**: Pick Products (default active)
2. **Step 2**: Review & Brand (after "Continue")
3. **Step 3**: Generate (after "Generate PDF")

### State Persistence
- Selected products persist when going back/forward between steps
- Project name syncs between header and review step
- PDF URL stored for "Open PDF" button

### UI/UX
- Mobile-first: Works at 320px width
- Smooth transitions between steps
- Clear visual feedback on selections
- Loading states during AJAX calls

## Common Issues

### Products Don't Load
- Check browser console for errors
- Verify products exist in database
- Check AJAX URL in Network tab (should be `/wp-admin/admin-ajax.php`)

### PDF Generation Fails
- Check error message in alert
- Verify Dompdf is installed
- Check `wp-content/uploads/submittal-builder/` folder exists and is writable

### Styling Looks Broken
- Hard refresh (Ctrl+F5) to clear cache
- Check `assets/css/frontend.css` is enqueued
- Inspect element to verify CSS classes exist

## Success Criteria
✅ All products load on Step 1
✅ Product selection works smoothly
✅ Step navigation flows naturally
✅ PDF generates successfully
✅ Mobile responsive (320px+)
✅ No JavaScript errors in console

## Next Phase (Not in MVP)
- Lead capture modal
- Email functionality
- Advanced animations
- Quantity fields
- Server-side search

---

**Questions?** Check browser console for errors and inspect Network tab for failed AJAX requests.
