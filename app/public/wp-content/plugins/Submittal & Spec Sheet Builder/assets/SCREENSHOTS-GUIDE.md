# Screenshot Capture Guide for WordPress.org

## Required Screenshots (6 Total)

WordPress.org requires numbered screenshots referenced in `readme.txt`. Each screenshot should be **1200-2000px wide** for optimal display on retina screens.

### Screenshot Naming Convention
- `screenshot-1.png` → Dashboard/Builder view
- `screenshot-2.png` → Sidebar details
- `screenshot-3.png` → Branding settings
- `screenshot-4.png` → Upgrade page
- `screenshot-5.png` → PDF preview (packet mode)
- `screenshot-6.png` → PDF product sheet

---

## Screenshot 1: Dashboard → Builder Interface
**File:** `screenshot-1.png`

**What to Show:**
- Main Builder admin page (WP Admin → Submittal Builder → Builder)
- Category/Type collapsible sections (expand 2-3 categories)
- Product grid with visible items (at least 6-8 products shown)
- Right sidebar with "Selected Items" group (3-5 items added)
- Sidebar shows product titles, categories, and delete icon

**Setup Before Capture:**
1. Add 3-4 sample categories (e.g., "Framing", "Fasteners", "Insulation")
2. Add 6-10 sample products with realistic names
3. Select 3-5 items so sidebar is populated
4. Expand at least 2 categories to show products

**Capture Settings:**
- Browser: Chrome or Firefox (clean UI)
- Zoom: 100% (or 90% if content doesn't fit)
- Viewport: 1400px+ wide
- Include: WordPress admin sidebar, top admin bar

**Post-Processing:**
- Crop to remove unnecessary whitespace
- Add subtle 1px dark border (#e5e7eb) around entire image
- Export as PNG @ 1200-1600px width

**readme.txt Caption:**
```
== Screenshots ==

1. **Builder Interface** - Browse categories, select products, and build your submittal packet with drag-and-drop ease.
```

---

## Screenshot 2: Sidebar Details (Expanded Item)
**File:** `screenshot-2.png`

**What to Show:**
- Same Builder page as Screenshot 1
- One item in sidebar **expanded** to show full details:
  - Product title
  - Category breadcrumb (e.g., "Framing › Studs")
  - Specifications table (Size, Thickness, Flange, KSI fields visible)
  - Trash/delete icon
- Other sidebar items visible but collapsed

**Setup Before Capture:**
1. Click to expand one sidebar item
2. Ensure specs are filled in (not empty)
3. Show at least 3-4 spec fields (Size, Thickness, etc.)

**Capture Settings:**
- Focus on right sidebar (can crop main content area tighter)
- Show enough context to see it's the same Builder interface

**readme.txt Caption:**
```
2. **Product Details** - Expand items in your selection to review specs, categories, and notes before generating PDF.
```

---

## Screenshot 3: Branding Settings Page
**File:** `screenshot-3.png`

**What to Show:**
- Branding admin page (WP Admin → Submittal Builder → Branding)
- Form fields visible:
  - Company Name input (filled)
  - Logo upload area (with sample logo if possible)
  - Primary Color picker (showing color swatch)
  - Company Address textarea (filled)
  - Company Phone/Website fields (filled)
  - Footer Text input (filled)
  - Theme dropdown (showing "Engineering", "Architectural", "Corporate" options)
- "Save Branding" button

**Setup Before Capture:**
1. Fill in all branding fields with realistic sample data
2. Upload a sample logo (or show the upload area)
3. Select a primary color (use #7c3aed purple or #0ea5e9 blue for visual appeal)

**Capture Settings:**
- Scroll to show top section with logo and colors
- Include Save button at bottom for context

**readme.txt Caption:**
```
3. **Branding Settings** - Add your company logo, colors, contact info, and footer text to brand every PDF.
```

---

## Screenshot 4: Upgrade to Pro Page
**File:** `screenshot-4.png`

**What to Show:**
- Upgrade admin page (WP Admin → Submittal Builder → ⭐ Upgrade)
- Feature comparison cards visible:
  - Free vs Pro columns side-by-side
  - Grouped sections: PDF Features, Workflow & Delivery, Customization, Support
  - Checkmarks (✓) and lock icons (🔒) clearly visible
- "Upgrade to Pro" CTA button
- Optional: Changelog section at bottom showing version history

**Setup Before Capture:**
1. Scroll to show feature comparison grid
2. Ensure CTA button is visible

**Capture Settings:**
- Capture full page or scroll to show most features
- Include pricing note ("$69/year" visible)

**readme.txt Caption:**
```
4. **Pro Features** - Compare Free vs Pro features including email delivery, tracking links, custom themes, and white-label mode.
```

---

## Screenshot 5: PDF Preview (Packet Mode)
**File:** `screenshot-5.png`

**What to Show:**
- Generated PDF opened in browser or PDF viewer
- Multiple pages visible (as thumbnails or scrolled view):
  - Cover page with company logo and project info
  - Summary page with product grouping table
  - Table of Contents with clickable links
- Show branded colors (header stripe, accent colors)

**Setup Before Capture:**
1. Generate a test PDF with "Packet" layout
2. Use realistic branding (logo, company name, colors)
3. Add 4-6 sample products so summary table looks populated
4. Open PDF in Chrome PDF viewer or Adobe Acrobat

**Capture Settings:**
- Use browser PDF viewer or Adobe Acrobat
- Show at least 2-3 pages (cover + summary + TOC)
- Zoom: 100-125% to show detail

**Post-Processing:**
- Crop to focus on PDF content (remove excessive browser chrome)
- Add 1px border

**readme.txt Caption:**
```
5. **PDF Packet Preview** - Generate professional packets with branded cover, summary, table of contents, and product sheets.
```

---

## Screenshot 6: PDF Product Sheet
**File:** `screenshot-6.png`

**What to Show:**
- Single product detail page from PDF
- Elements visible:
  - Product title (bold, large font)
  - Category breadcrumb (e.g., "Framing › Studs › Cold-Formed")
  - Specifications table with labeled rows:
    - Specification column (bold labels like "Size", "Thickness", "Flange")
    - Value column (actual data like "3.5"", "43 mil", "1.625"")
  - Optional: Notes section with sample text
  - Header/footer with company name, page numbers

**Setup Before Capture:**
1. Generate PDF with detailed product specs
2. Navigate to a product detail page (page 3-5 usually)
3. Ensure specs table is fully visible

**Capture Settings:**
- Zoom: 125-150% to show table detail
- Focus on single page (crop other pages out)

**readme.txt Caption:**
```
6. **Product Spec Sheet** - Each product gets a detailed page with specifications table, notes, and optional approval blocks.
```

---

## Screenshot Best Practices

### Before Capturing
- [ ] Use realistic sample data (not "test" or "asdf")
- [ ] Fill in all visible form fields
- [ ] Use consistent branding (same colors/logo across screenshots)
- [ ] Clear browser cache/disable extensions that add UI clutter
- [ ] Maximize browser window (1400px+ width recommended)

### During Capture
- [ ] Use **Snagit**, **ShareX**, **Greenshot**, or macOS Screenshot (Cmd+Shift+4)
- [ ] Capture at 100% browser zoom for sharpness
- [ ] Include enough context (WP admin sidebar, page title visible)
- [ ] Avoid capturing personal data (use fake company names/addresses)

### After Capture
- [ ] Resize to **1200-1600px width** (keeps file size reasonable)
- [ ] Add **1px border** (#e5e7eb or #cccccc) for cleaner WP.org display
- [ ] Optimize with **TinyPNG** or **ImageOptim** (target <200KB per screenshot)
- [ ] Rename files: `screenshot-1.png`, `screenshot-2.png`, etc.
- [ ] Save to plugin's `assets/` directory (for SVN upload)

### Accessibility Notes
- Screenshots should have descriptive captions in readme.txt
- Avoid relying solely on color to convey meaning
- Use high contrast (dark text on light background)

---

## Tools for Screenshots

| Tool | Platform | Cost | Best For |
|------|----------|------|----------|
| **Snagit** | Win/Mac | Paid | Advanced editing, annotations |
| **ShareX** | Windows | Free | Powerful, customizable |
| **Greenshot** | Win/Mac | Free | Simple, lightweight |
| **macOS Screenshot** | macOS | Free | Built-in (Cmd+Shift+4) |
| **Browser DevTools** | All | Free | Device emulation, specific sizes |

---

## Validation Checklist

Before uploading to WordPress.org:

- [ ] 6 screenshots numbered sequentially (1-6)
- [ ] All files are PNG format (not JPG)
- [ ] Width: 1200-2000px (retina-ready)
- [ ] File size: <500KB each (preferably <200KB)
- [ ] 1px border added for visual clarity
- [ ] No personal/sensitive data visible
- [ ] Captions added to `readme.txt` under `== Screenshots ==`
- [ ] Screenshots saved to `assets/` directory (not plugin `trunk/`)

---

## readme.txt Caption Template

Add this section to your `readme.txt`:

```
== Screenshots ==

1. **Builder Interface** - Browse categories, select products, and build your submittal packet with drag-and-drop ease.
2. **Product Details** - Expand items in your selection to review specs, categories, and notes before generating PDF.
3. **Branding Settings** - Add your company logo, colors, contact info, and footer text to brand every PDF.
4. **Pro Features** - Compare Free vs Pro features including email delivery, tracking links, custom themes, and white-label mode.
5. **PDF Packet Preview** - Generate professional packets with branded cover, summary, table of contents, and product sheets.
6. **Product Spec Sheet** - Each product gets a detailed page with specifications table, notes, and optional approval blocks.
```

---

## WordPress.org Screenshot Guidelines

Official documentation:
https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/#screenshots

**Key Requirements:**
- Screenshots go in SVN `assets/` directory (separate from plugin code)
- Number sequentially (screenshot-1.png, screenshot-2.png, etc.)
- Reference in readme.txt with captions
- Recommended dimensions: 1200px+ wide for retina displays
- Format: PNG or JPG (PNG preferred for UI screenshots)

---

## Next Steps

1. Populate plugin with sample data (categories, products, branding)
2. Capture all 6 screenshots following guidelines above
3. Resize/optimize images (<200KB each)
4. Add 1px border for visual polish
5. Save to `assets/` directory: `screenshot-1.png` through `screenshot-6.png`
6. Update `readme.txt` with captions under `== Screenshots ==` section
7. Commit to SVN `assets/` folder (separate from `trunk/`)
