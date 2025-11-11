# Final Screenshot Guide - WordPress.org Submission

**Plugin:** Submittal & Spec Sheet Builder
**Target Audience:** Construction/HVAC/Electrical/Plumbing manufacturers
**Total Screenshots:** 10
**Focus:** Construction industry submittal packets

---

## Overview

These 10 screenshots showcase the complete customer journey + backend management:
- **Screenshots 1-5:** Customer experience (frontend)
- **Screenshots 6-7:** PDF output quality
- **Screenshots 8-10:** Backend catalog management

---

## Screenshot 1: Frontend Product Browser

**Filename:** `screenshot-1.png`

**What to Capture:**
- Frontend page with `[submittal_builder]` shortcode
- Gallery view showing 6-8 HVAC or Electrical products
- Product cards with images and spec previews
- Search bar and category filters visible at top
- Clean, professional layout

**Setup:**
- Use HVAC products (e.g., "Variable Speed Drive - 10HP", "Air Handler - 5 Ton")
- OR Electrical products (e.g., "Panelboard - 225A", "Circuit Breaker - 100A")
- Show category dropdown expanded with options
- Make sure search bar is visible
- Gallery view (not list view)

**Technical Settings:**
- Browser: Chrome (100% zoom)
- Viewport: 1400px+ wide
- Include: Full page from header to product grid

**Caption for readme.txt:**
```
1. Frontend product browser - Contractors search and browse your catalog with live filtering by category and specifications
```

---

## Screenshot 2: Product Selection Interface

**Filename:** `screenshot-2.png`

**What to Capture:**
- Same frontend page
- Multiple products selected (checkboxes checked or highlighted)
- Selection counter showing "5 items selected" or similar
- "Continue" or "Review Selection" button visible
- Mix of categories selected

**Setup:**
- Select 4-6 products from different categories
- Show selection tray/cart if visible
- Make sure counter is obvious
- Keep layout clean

**Technical Settings:**
- Same view as Screenshot 1
- Just with selections made
- Highlight/checkbox states visible

**Caption for readme.txt:**
```
2. Product selection - Customers select products for their project with one-click selection and see live count
```

---

## Screenshot 3: Review & Project Details

**Filename:** `screenshot-3.png`

**What to Capture:**
- Review page showing selected products in a list
- Project information form with fields:
  - Project Name: "Downtown Office Tower Renovation"
  - Contractor: "ABC Construction"
  - Submittal #: "SUB-001"
- Selected products list with specs visible
- "Generate PDF" button prominent at bottom

**Setup:**
- Show 4-5 selected products with:
  - Product names
  - Brief specs visible
  - Category breadcrumbs
- Fill in all project fields with realistic data
- Make sure "Generate PDF" button is clearly visible

**Technical Settings:**
- Scroll to show both product list AND project form
- Or capture in two sections if needed
- Keep it clean and organized

**Caption for readme.txt:**
```
3. Review and project info - Customers review selections and add project details for professional documentation
```

---

## Screenshot 4: Lead Capture Modal (Pro Feature)

**Filename:** `screenshot-4.png`

**What to Capture:**
- Modal overlay with "Get your PDF" heading
- Email field (required) with label "Work Email"
- Phone field (optional)
- Project name visible (auto-filled or user-entered)
- Consent checkbox: "Email me updates about products and projects"
- "Send me the PDF" button
- Modal centered on screen with overlay darkening background

**Setup:**
- Enable Pro features or use SFB_PRO_DEV constant
- Fill in realistic data:
  - Email: `contractor@example.com`
  - Phone: `(555) 123-4567`
  - Consent checkbox unchecked (shows it's optional)
- Project name should be visible somewhere

**Technical Settings:**
- Capture full screen to show modal overlay
- Modal should be centered and prominent
- Background should be slightly darkened

**Caption for readme.txt:**
```
4. Lead capture (Pro) - Optionally require email before PDF download to capture qualified leads with project details
```

---

## Screenshot 5: PDF Generated Success

**Filename:** `screenshot-5.png`

**What to Capture:**
- Success page with "Your PDF is Ready!" or similar message
- Large "Download PDF" button
- Optional: "Open in Browser" link
- "Start Over" or "Create Another" button
- Clean, celebratory design
- Maybe show file details (name, size) if visible

**Setup:**
- Generate a test PDF
- Capture the success screen immediately after generation
- Make sure download button is prominent
- Keep the design clean

**Technical Settings:**
- Center the success message
- Capture full page context
- Show any relevant metadata

**Caption for readme.txt:**
```
5. PDF generated - Instant submittal packet generation with download link - no waiting, no manual work required
```

---

## Screenshot 6: PDF Cover Page

**Filename:** `screenshot-6.png`

**What to Capture:**
- PDF opened in browser/viewer showing COVER PAGE ONLY
- Company logo (centered at top)
- Company name and contact information
- Project name: "Downtown Office Tower Renovation"
- Date and submittal number
- Brand color stripe/header visible
- Professional, clean layout

**Setup:**
1. Configure branding in Settings:
   - Upload a professional logo (300x100px recommended)
   - Set primary color (e.g., #7c3aed purple or #0ea5e9 blue)
   - Fill in company info
2. Generate PDF with project details
3. Open in Chrome PDF viewer or Adobe Acrobat
4. Navigate to page 1 (cover page)

**Technical Settings:**
- Zoom: 100-125% to show detail
- Capture just the cover page (crop other pages out)
- Center the page in frame
- Add 1px border to the screenshot

**Caption for readme.txt:**
```
6. Branded PDF cover page - Professional cover with your logo, company info, and project details
```

---

## Screenshot 7: PDF Product Specification Sheet

**Filename:** `screenshot-7.png`

**What to Capture:**
- Single product page from PDF (usually page 3-5)
- Product title: e.g., "Variable Speed Drive - 50HP"
- Category breadcrumb: "HVAC > Drives > Variable Speed"
- **Specifications table** with industry-specific fields:
  - For HVAC: Voltage, Amperage, Phase, HP Rating, BTU
  - For Electrical: Voltage, Amperage, Interrupt Rating, Poles
  - For Plumbing: Diameter, PSI, Material, GPM
- Page number in footer
- Company name/logo in header/footer

**Setup:**
1. Use same PDF from Screenshot 6
2. Navigate to a product detail page (page 3-5)
3. Make sure specifications table has data filled in
4. Zoom to show table clearly

**Technical Settings:**
- Zoom: 125-150% to show specification table detail
- Crop to focus on ONE page only
- Center the content
- Add 1px border

**Caption for readme.txt:**
```
7. Product specification sheet - Detailed spec sheets with industry-specific fields ready for code compliance and bid submittal
```

---

## Screenshot 8: Catalog Builder - Tree View

**Filename:** `screenshot-8.png`

**What to Capture:**
- WordPress Admin: Submittal Builder → Catalog Builder
- Tree view with hierarchical structure:
  - 2-3 Categories expanded (e.g., "HVAC Equipment", "Electrical Panels")
  - Products/Types/Models visible underneath
- Inspector modal OPEN on the right showing a model being edited
- Toolbar visible at top with:
  - "+ New" button
  - "⚙️ Manage Fields" button
  - Search bar
- Drag handles (⋮⋮) visible on items

**Setup:**
1. Build sample HVAC or Electrical catalog with:
   - 3 categories
   - 2-3 products per category
   - 1-2 types per product
   - 2-3 models per type
2. Click on a model to open Inspector modal
3. Expand at least 2 categories
4. Show 8-12 total items in tree

**Technical Settings:**
- Browser: Chrome at 100% zoom
- Capture: Full WordPress admin page
- Include: WP admin sidebar, top admin bar, plugin interface
- Make sure modal is fully visible

**Caption for readme.txt:**
```
8. Catalog builder - Manage your product catalog with hierarchical tree view and drag-and-drop organization
```

---

## Screenshot 9: Field Management Modal

**Filename:** `screenshot-9.png`

**What to Capture:**
- "⚙️ Manage Fields" modal OPEN in center of screen
- Modal title: "Manage Model Fields"
- Description text explaining field customization
- Field list showing 4-5 fields with:
  - Drag handles (⋮⋮)
  - Editable input boxes (e.g., "Voltage", "Amperage", "Wattage", "Phase")
  - Remove button (×) for each field
- "+ Add Field" button below field list
- **"Quick Presets" section** showing buttons:
  - Steel/Construction
  - HVAC
  - Electrical
  - Plumbing
- "Save Changes" and "Cancel" buttons at bottom

**Setup:**
1. Click "⚙️ Manage Fields" button in toolbar
2. Select HVAC or Electrical preset (fields should match industry)
3. Make sure all 4 preset buttons are visible
4. Leave modal centered and prominent
5. Background catalog should be slightly visible behind overlay

**Technical Settings:**
- Capture full screen to show modal overlay
- Modal should be centered
- Background dimmed/blurred
- All preset buttons visible

**Caption for readme.txt:**
```
9. Customizable fields - Industry presets for HVAC, Electrical, Plumbing, and Steel, or create custom specification fields
```

---

## Screenshot 10: Inspector Modal - Adding a Model

**Filename:** `screenshot-10.png`

**What to Capture:**
- Inspector modal open showing the **Details tab**
- Click on a **Type** node (NOT a Model - Models can't have children)
- Modal shows:
  - **Top:** Title field (e.g., "20 Gauge" or "Variable Speed Drives")
  - **Middle:** Node Type display ("Type")
  - **Middle:** Actions buttons (↑ Up, ↓ Down, 🗑️ Delete)
  - **Bottom (MOST IMPORTANT):** "Add Child" section with:
    - Label: "Add Child"
    - Button: "+ Model"
- Scroll to make sure "Add Child" section is fully visible

**Setup:**
1. Click on a **Type** node in the catalog tree (e.g., "20 Gauge", "Variable Speed Drives")
2. Inspector modal opens automatically
3. Make sure you're on the **Details** tab (first tab)
4. **Scroll down** to show the "Add Child" section at the bottom
5. The "+ Model" button should be clearly visible

**Technical Settings:**
- Capture full modal from top to bottom
- Or crop to focus on "Add Child" section if modal is too tall
- Make sure button is prominent
- Add annotation/arrow if needed to highlight the section

**Caption for readme.txt:**
```
10. Add products easily - Click any node to open inspector, scroll to "Add Child" section to add products, types, and models
```

---

## Technical Specifications for All Screenshots

### File Format & Size
- **Format:** PNG (not JPG)
- **Width:** 1200-1600px (retina-ready)
- **File Size:** <500KB each (optimize with TinyPNG or ImageOptim)
- **Border:** Add 1px #e5e7eb or #cccccc border around entire image

### Browser Settings
- **Browser:** Chrome or Firefox (clean UI, no extensions visible)
- **Zoom:** 100% for sharpness
- **Viewport:** 1400px+ wide for admin screenshots
- **Extensions:** Disable or use incognito mode

### Data Guidelines
- **Use realistic names:** Not "test", "asdf", "lorem ipsum"
- **Industry context:** HVAC, Electrical, or Plumbing examples
- **Consistent branding:** Same logo/colors across all PDF screenshots
- **Real-looking data:** Actual voltage values, realistic project names

### Post-Processing
1. Capture screenshot at full resolution
2. Resize to 1200-1600px width (maintain aspect ratio)
3. Add 1px border (#e5e7eb)
4. Optimize with TinyPNG (<200KB ideal)
5. Rename: `screenshot-1.png` through `screenshot-10.png`
6. Save to plugin's `assets/` directory

---

## WordPress.org readme.txt Section

Add this exact section to your `readme.txt` file:

```
== Screenshots ==

1. Frontend product browser - Contractors search and browse your catalog with live filtering by category and specifications
2. Product selection - Customers select products for their project with one-click selection and see live count
3. Lead capture (Pro) - Optionally require email before PDF download to capture qualified leads with project details
4. Review and project info - Customers review selections and add project details for professional documentation
5. PDF generated - Instant submittal packet generation with download link - no waiting, no manual work required
6. Branded PDF cover page - Professional cover with your logo, company info, and project details
7. Product specification sheet - Detailed spec sheets with industry-specific fields ready for code compliance and bid submittal
8. Catalog builder - Manage your product catalog with hierarchical tree view and drag-and-drop organization
9. Customizable fields - Industry presets for HVAC, Electrical, Plumbing, and Steel, or create custom specification fields
10. Add products easily - Click any node to open inspector, scroll to "Add Child" section to add products, types, and models
```

---

## File Upload Checklist

Before uploading to WordPress.org SVN:

- [ ] All 10 screenshots captured and edited
- [ ] Files named correctly: `screenshot-1.png` through `screenshot-10.png`
- [ ] All files are PNG format (not JPG)
- [ ] All files are 1200-1600px wide
- [ ] All files are <500KB (preferably <200KB)
- [ ] 1px border added to all screenshots
- [ ] No personal/sensitive data visible
- [ ] Consistent branding across PDF screenshots
- [ ] Captions added to `readme.txt` under `== Screenshots ==`
- [ ] Screenshots uploaded to SVN `assets/` directory (NOT `trunk/`)

---

## SVN Upload Location

Upload screenshots to:
```
https://plugins.svn.wordpress.org/submittal-builder/assets/
```

**NOT** to `trunk/` or `tags/` directories!

Files should be:
```
assets/
  ├── screenshot-1.png
  ├── screenshot-2.png
  ├── screenshot-3.png
  ├── screenshot-4.png
  ├── screenshot-5.png
  ├── screenshot-6.png
  ├── screenshot-7.png
  ├── screenshot-8.png
  ├── screenshot-9.png
  └── screenshot-10.png
```

---

## Recommended Tools

### Screenshot Capture
- **Windows:** ShareX (free), Snagit (paid)
- **Mac:** macOS Screenshot (Cmd+Shift+4), Snagit (paid)
- **Cross-platform:** Browser DevTools (F12 → Device toolbar)

### Image Optimization
- **TinyPNG:** https://tinypng.com (web-based, free)
- **ImageOptim:** https://imageoptim.com (Mac, free)
- **Squoosh:** https://squoosh.app (web-based, free)

### Border Addition
- **Photoshop:** Stroke layer style
- **GIMP:** Filters → Decor → Border
- **Online:** Canva (free tier)

---

## WordPress.org Guidelines Reference

Official screenshot documentation:
https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/#screenshots

**Key points:**
- Screenshots are optional but highly recommended
- PNG or JPG format accepted
- 1200px+ width recommended for retina displays
- Uploaded to SVN `assets/` directory
- Referenced in `readme.txt` with numbered captions
- Update screenshots separately from plugin releases

---

## Quality Checklist

### Visual Quality
- [ ] Screenshots are sharp and clear (not blurry)
- [ ] Text is readable at thumbnail size
- [ ] Colors are accurate (not washed out)
- [ ] No UI glitches or broken layouts visible
- [ ] Consistent aspect ratio across similar screenshots

### Content Quality
- [ ] Screenshots show actual features (not mockups)
- [ ] Data looks realistic and professional
- [ ] No typos in visible text
- [ ] UI elements are properly aligned
- [ ] No debug/development messages visible

### Strategic Quality
- [ ] Screenshots tell a coherent story (1-10 flow makes sense)
- [ ] Value proposition is clear from screenshots alone
- [ ] Target audience can see themselves using it
- [ ] Both frontend and backend are represented
- [ ] Pro features are clearly marked

---

## Tips for Best Results

### Before Capturing
1. **Seed realistic data** - Use HVAC/Electrical products with real specs
2. **Configure branding** - Upload logo, set colors, fill company info
3. **Clear browser state** - Log out of other accounts, close unrelated tabs
4. **Disable extensions** - Use incognito mode or disable extensions
5. **Maximize window** - 1400px+ width for admin, responsive for frontend

### During Capture
1. **100% zoom** - For sharpest text rendering
2. **Include context** - Show enough UI to orient viewers
3. **Focus on key features** - Crop unnecessary whitespace
4. **Capture at exact moment** - Buttons visible, modals centered
5. **Check for consistency** - Same branding across PDF shots

### After Capture
1. **Review at thumbnail size** - Can you see key details when small?
2. **Test on retina display** - Does it look sharp on high-DPI screens?
3. **Get feedback** - Show to someone unfamiliar with the plugin
4. **Optimize file size** - Target <200KB without sacrificing quality
5. **Document what you captured** - Note any special setup needed

---

## Common Mistakes to Avoid

❌ **Using Lorem Ipsum or "Test" data** - Makes plugin look unprofessional
✅ Use realistic industry data (Variable Speed Drive - 50HP)

❌ **Screenshots too small** (<1000px wide) - Looks blurry on retina
✅ 1200-1600px width for crisp display

❌ **Uploading to wrong SVN directory** (trunk/ or tags/)
✅ Upload to assets/ directory only

❌ **Inconsistent branding** - Different logos/colors in PDF shots
✅ Configure once, use for all screenshots

❌ **Forgetting 1px border** - Screenshots blend into white background
✅ Add subtle border for visual separation

❌ **File size too large** (>1MB per screenshot)
✅ Optimize with TinyPNG to <200KB

❌ **No context shown** - Just cropping to a single button
✅ Show enough interface for viewers to understand location

❌ **Screenshots don't match captions** - Caption says one thing, image shows another
✅ Double-check caption accuracy before submitting

---

## Final Validation

Before marking this task complete:

### Technical Validation
- [ ] All 10 files are PNG format
- [ ] All filenames match exactly: `screenshot-1.png` through `screenshot-10.png`
- [ ] All files are 1200-1600px wide
- [ ] All files are under 500KB (ideally under 200KB)
- [ ] All have 1px border added

### Content Validation
- [ ] Screenshot 1-2: Frontend works (product browsing + selection)
- [ ] Screenshot 3: Lead capture modal visible (Pro feature)
- [ ] Screenshot 4-5: Review page + success screen
- [ ] Screenshot 6-7: PDF output (cover + spec sheet)
- [ ] Screenshot 8: Catalog builder tree view
- [ ] Screenshot 9: Field management modal
- [ ] Screenshot 10: Inspector with "Add Child" section

### Strategic Validation
- [ ] Screenshots tell construction industry story
- [ ] HVAC/Electrical examples used consistently
- [ ] Branding looks professional across all PDFs
- [ ] Both customer journey and admin backend shown
- [ ] Pro features are indicated (Screenshot 3)
- [ ] Captions are clear and benefit-focused

---

## Success Criteria

You've succeeded when:

1. ✅ All 10 screenshots are captured and optimized
2. ✅ Captions are added to `readme.txt`
3. ✅ Files are uploaded to SVN `assets/` directory
4. ✅ WordPress.org review team approves them
5. ✅ Screenshots display properly on plugin page
6. ✅ Visitors can understand the plugin from screenshots alone

---

**Good luck with your WordPress.org submission!** 🚀

These screenshots will make a strong first impression for potential users.
