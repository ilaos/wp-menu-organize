# User Guide

[← Back to Documentation](./index.md)

**Complete walkthrough of the Submittal Builder interface**

---

## Overview

This guide walks you through the complete process of creating a submittal packet using the frontend builder interface. The builder follows a simple 3-step workflow designed to be intuitive for contractors, engineers, and sales teams.

---

## The Three-Step Process

```
Step 1: Select Products → Step 2: Review & Customize → Step 3: Generate PDF
```

Each step is designed to be straightforward while offering powerful functionality for organizing and customizing your submittal packets.

---

## Step 1: Select Products

The product selection interface allows users to browse your catalog and select items for their submittal packet.

### Interface Overview

**Top Bar:**
- Search box (searches all product data)
- Results count
- Expand All / Collapse All button
- Selection counter

**Main Area:**
- Collapsible product group sections (dark blue headers)
- Type subsections (bright blue bars) within each product
- Product cards organized by type
- Real-time filtering

**Bottom:**
- Selected products tray (sticky)
- "Continue to Review" button

### Browsing Products

#### Product Organization

Products are organized in a 4-level hierarchy for easy navigation:

**Hierarchy:**
1. **Category** (sidebar filters) - e.g., "Framing - Drywall"
2. **Product** (dark blue collapsible headers) - e.g., "25 GAUGE 18 MIL"
3. **Type** (bright blue subsections) - e.g., "2-1/2" Flange", "Slotted Tracks"
4. **Models** (individual product cards) - e.g., "400T300-18", "362SLT250-18"

**Collapsible Groups:**
- Click any dark blue product header to expand/collapse that section
- Use the "Expand All" / "Collapse All" button to toggle all sections at once
- Product groups start collapsed by default to reduce visual clutter
- Your collapse/expand preferences are saved and remembered

#### Gallery View (Default)

Product cards displayed in a grid layout, grouped by type:

**Each Card Shows:**
- Model name/number
- Category breadcrumb (at top)
- Key specifications (Size, Thickness, KSI, etc.)
- Configurable dropdown fields (if applicable)
- Visual thumbnail (if available)

**To Select a Product:**
1. Click anywhere on the card
2. Card highlights with "✓ ADDED" indicator
3. Counter updates in real-time
4. Click again to deselect

#### List View

Compact table-style display:

**Columns:**
- Model Number
- Category
- Type
- Specifications (abbreviated)
- Select checkbox

**To Select a Product:**
1. Click checkbox on the right
2. Row highlights
3. Counter updates

**Toggle Views:**
Click the view toggle button in the top right (grid/list icons).

### Using Search

The search box performs real-time filtering across all product data.

**Searches:**
- Model numbers
- Product names
- Categories
- Types
- All specification values (Size, KSI, Thickness, etc.)

**How to Search:**
1. Click search box
2. Start typing (e.g., "362" or "20 gauge")
3. Results filter instantly
4. Clear search to see all products again

**Tips:**
- Search is case-insensitive
- Searches partial matches (e.g., "36" finds "362S162-20")
- Works with spec values (e.g., "6 inch" finds all 6" products)

### Category Filters

Filter products by category using dropdown or tag buttons.

**To Filter by Category:**
1. Click category dropdown or filter button
2. Select desired category
3. Only products in that category show
4. Select "All Categories" to reset

**Combined Filtering:**
You can use category filter + search together for precise results.

### Selection Counter

The selection counter shows how many products you've selected.

**Display Format:**
```
"5 Products Selected" or "VIEW 5 →"
```

**Click Counter to:**
- View selected products (quick preview)
- Remove selections
- Proceed to next step

### Keyboard Shortcuts (Step 1)

| Key | Action |
|-----|--------|
| **/** or **Ctrl+F** | Focus search box |
| **Arrow Keys** | Navigate cards |
| **Space** | Select/deselect focused card |
| **Enter** | Proceed to next step |
| **Esc** | Clear search |

---

## Step 2: Review & Customize

Review your selections, reorder items, add project information, and prepare for PDF generation.

### Interface Overview

**Left Panel: Selected Products**
- Drag handles for reordering
- Product details
- Remove buttons
- Count indicator

**Right Panel: Project Information**
- Project name field
- Company/contact info (optional)
- Additional notes (optional)
- Pro PDF options (theme, watermark, signature block)
- Generate PDF button

### Reviewing Selections

All selected products are listed with full details.

**Each Product Shows:**
- Model number
- Category and type
- All specifications
- Remove button (X icon)

**To Remove a Product:**
1. Click X icon next to product
2. Product removed from list
3. Count updates

**To Go Back:**
Click "← Back to Products" to return to Step 1 and modify selections.

### Reordering Products

Change the order products appear in the PDF.

**How to Reorder:**
1. Hover over product item
2. Drag handle appears (≡ icon)
3. Click and drag to new position
4. Release to drop
5. Order saved automatically

**Why Reorder?**
- Group related products together
- Organize by installation sequence
- Place most important items first
- Match architect's spec order

**PDF Order:**
Products will appear in the PDF in the exact order shown in this list.

### Adding Project Information

Provide context for the submittal packet.

#### Project Name (Recommended)

**Field:** "Project Name"

**Examples:**
- "Miller Hall – West Wing Renovation"
- "Acme Office Building – 3rd Floor"
- "Residential – 123 Main St"

**Usage:**
- Appears on PDF cover page
- Helps recipient identify project
- Used in tracking/analytics (Pro)
- Included in lead data (Pro)

#### Company Information (Optional)

If lead capture is enabled (Pro), additional fields may appear:
- Company name
- Contact name
- Email address
- Phone number

#### Notes Field (Optional)

Add any additional context or instructions.

**Examples:**
- "Per architect specifications revision 3"
- "Alternate products for value engineering"
- "For submittal package #5"

### Pro PDF Options (Pro)

Customize your PDF appearance and add professional elements with Pro features.

#### PDF Theme Selection (Pro)

Choose from three professionally designed color schemes that apply across your entire PDF.

**Available Themes:**
- **Engineering** (Default) - Professional blue color scheme
- **Architectural** - Modern gray/silver palette
- **Corporate** - Elegant navy/charcoal styling

**How to Select:**
1. Look for "PDF Theme" dropdown in right panel (Pro users only)
2. Select your preferred theme
3. Theme applies to cover page, TOC, summary table, and product sheets

**Theme Colors:**
Each theme provides consistent color coding throughout the PDF, affecting:
- Header backgrounds
- Accent colors
- Table styling
- Section dividers

#### PDF Watermark (Pro)

Add a custom diagonal text watermark across all pages.

**Common Uses:**
- "DRAFT" - For review copies
- "CONFIDENTIAL" - For sensitive documents
- "FOR REVIEW ONLY" - For preliminary submittals
- "PRELIMINARY" - For early-stage documents

**How to Add:**
1. Find "PDF Watermark" field in right panel (Pro users only)
2. Enter your custom text (e.g., "DRAFT")
3. Leave blank to disable watermark
4. Watermark appears diagonally across all PDF pages

**Display:**
- Appears in semi-transparent gray
- Rotated 45 degrees diagonally
- Large text spanning center of each page
- Does not obscure content readability

#### Approval Signature Block (Pro)

Add a professional 3-column approval table at the end of each product sheet.

**How to Enable:**
1. Check "Include Approval Signature Block" checkbox (Pro users only)
2. Optionally pre-fill approval fields:
   - **Approved By:** Name of approver
   - **Title:** Job title or role
   - **Date:** Approval date
3. Leave fields blank to provide empty signature lines
4. Block appears at bottom of each product sheet

**Use Cases:**
- AHJ (Authority Having Jurisdiction) approval
- Engineering review and sign-off
- Architect/designer approval
- Code compliance verification
- Quality assurance documentation

**Layout:**
```
┌──────────────┬──────────────┬──────────────┐
│ Approved By  │    Title     │     Date     │
├──────────────┼──────────────┼──────────────┤
│ [Signature]  │ [Title/Role] │ [Date/Stamp] │
└──────────────┴──────────────┴──────────────┘
```

### Branding Preview

The right panel shows a preview of your branding:
- Logo
- Brand color
- Company name
- Contact information

This is what will appear on the PDF cover page.

**Pro Users:** The selected PDF theme will affect the color scheme throughout your PDF, while the branding preview shows your logo and company information.

### Generating the PDF

Once you're satisfied with your selections and project info:

**Click "Generate PDF" Button**

**What Happens:**
1. Loading indicator appears
2. PDF generation begins on server
3. Progress shown (typically 3-10 seconds)
4. Success page displays when complete

**During Generation:**
- Do not close browser window
- Do not navigate away
- Wait for completion message

---

## Step 3: Success & Download

PDF generation complete - download and share your submittal packet.

### Success Page

**Displays:**
- Success message
- PDF file name
- File size
- Action buttons

**Available Actions:**
- **Open PDF** - View in browser
- **Download PDF** - Save to device
- **Start Over** - Create another packet
- **Copy Link** - Share URL (Pro feature with tracking)

### Opening the PDF

**Click "Open PDF" to:**
- View in browser tab
- Verify contents before downloading
- Print directly from browser
- Share URL from address bar (temporary)

**Browser Compatibility:**
- Chrome: Opens in built-in PDF viewer
- Firefox: Opens in built-in viewer
- Safari: Opens in preview
- Edge: Opens in built-in viewer

### Downloading the PDF

**Click "Download PDF" to:**
- Save to your device
- File downloads to default folder (usually "Downloads")

**Default Filename Format:**
```
submittal-{project-name}-{date}.pdf
```

**Example:**
```
submittal-miller-hall-2025-10-15.pdf
```

### PDF Contents

Your generated PDF includes:

**1. Cover Page**
- Your logo
- Brand color scheme (customized by PDF theme if Pro)
- Project name
- Date generated
- Company contact information
- Watermark overlay (if enabled - Pro)

**2. Table of Contents**
- List of all products
- Page numbers
- Quick navigation

**3. Summary Table**
- All selected products organized by product group
- Columns: Qty, Type, Model, Key Specifications, Notes
- Type column with prominent blue styling for easy scanning
- Quick reference overview of entire packet
- Watermark overlay (if enabled - Pro)

**4. Individual Spec Sheets**
- One page per product model
- Full breadcrumb navigation (Category / Product / Type)
- Complete specifications
- Product images and details
- Formatted for compliance and code requirements
- Approval signature block at bottom (if enabled - Pro)
- Watermark overlay (if enabled - Pro)

**5. Footer**
- Page numbers
- Company information
- Date generated

### Sharing the PDF

**Email:**
Attach downloaded PDF to email for clients, architects, or inspectors.

**File Size:**
Typical PDFs are 200-500 KB per product (email-friendly).

**Print:**
PDFs are optimized for standard letter-size (8.5" × 11") printing.

**Cloud Storage:**
Upload to Dropbox, Google Drive, or project management tools.

**Tracking (Pro):**
Use the "Copy Link" feature to share a trackable URL and monitor when recipients view the packet.

---

## Common Workflows

### Workflow 1: Simple Submittal (5 products)

**Time:** 2-3 minutes

1. Visit builder page
2. Search for products by model number
3. Select 5 products
4. Click "Continue"
5. Add project name
6. Generate PDF
7. Download and email

**Best For:** Quick submittals, simple projects

---

### Workflow 2: Large Packet (25+ products)

**Time:** 10-15 minutes

1. Use category filters to narrow down
2. Select products systematically
3. Review selections carefully
4. Reorder by installation sequence
5. Add detailed project notes
6. Generate PDF
7. Verify all products included

**Best For:** Comprehensive submittals, complex projects

---

### Workflow 3: Repeat Customer

**Time:** 1-2 minutes

1. Search for previously used products
2. Select quickly (familiar with catalog)
3. Add project name
4. Generate and send

**Best For:** Regular customers, standard configurations

---

## Tips & Best Practices

### For Contractors

✅ **Do:**
- Add project name for easy reference
- Review selections before generating
- Download PDF immediately (temporary storage)
- Keep organized folders for project submittals

❌ **Don't:**
- Skip project name (makes tracking difficult)
- Generate PDFs with wrong products (slows approvals)
- Forget to download (links expire after 24 hours)

### For Sales Teams

✅ **Do:**
- Use consistent project naming conventions
- Reorder products by customer priority
- Include notes for clarification
- Save PDFs to CRM or project folders

❌ **Don't:**
- Rush through selections (causes errors)
- Skip reviewing before generating
- Forget to track sent packets (Pro: use tracking feature)

### For Engineers

✅ **Do:**
- Organize products by specification section
- Reorder by CSI division or installation sequence
- Add detailed notes for compliance
- Verify all specs match project requirements

❌ **Don't:**
- Mix incompatible product lines
- Skip specification verification
- Generate without double-checking selections

---

## Accessibility Features

The builder is designed to be accessible to all users.

### Keyboard Navigation

- **Tab** - Move between interactive elements
- **Space/Enter** - Activate buttons and select products
- **Arrow Keys** - Navigate product cards
- **Esc** - Close modals and clear search

### Screen Reader Support

- All interactive elements have proper labels
- Search results announced dynamically
- Selection changes announced
- Error messages announced

### Visual Accessibility

- High contrast mode supported
- Focus indicators visible
- Large touch targets (44px minimum)
- Color is not the only indicator

### Mobile Responsiveness

- Touch-friendly interface
- Optimized for small screens
- Swipe gestures supported
- Responsive PDF generation

---

## Troubleshooting

### "No products found"

**Cause:** Catalog is empty or filters too restrictive.

**Solution:**
1. Clear search box
2. Reset category filters
3. Check with administrator if catalog is populated

---

### Search not working

**Cause:** JavaScript error or slow server response.

**Solution:**
1. Refresh page
2. Try different browser
3. Clear browser cache
4. Check browser console for errors

---

### Can't select products

**Cause:** JavaScript disabled or browser compatibility issue.

**Solution:**
1. Enable JavaScript in browser settings
2. Update browser to latest version
3. Try different browser (Chrome, Firefox, Edge)

---

### PDF generation fails

**Cause:** Server timeout, memory limit, or file generation error.

**Solution:**
1. Try generating with fewer products
2. Refresh and try again
3. Contact site administrator
4. See [Troubleshooting Guide](./troubleshooting.md#pdf-generation-fails)

---

### PDF download link doesn't work

**Cause:** Link expired (24-hour limit) or file deleted.

**Solution:**
1. Generate PDF again
2. Download immediately
3. Save to device before link expires

---

### Branding doesn't appear in PDF

**Cause:** Branding not configured in admin settings.

**Solution:**
1. Contact site administrator
2. Admin should configure: Submittal Builder → Settings → Branding
3. See [Branding Guide](./branding-pdfs.md)

---

## Mobile Usage

The builder works on mobile devices (phones and tablets).

### Best Practices for Mobile

**Portrait Mode:**
- Better for browsing products
- Easier to read specs
- More comfortable for one-handed use

**Landscape Mode:**
- Better for reviewing selections
- More products visible at once

**Touch Gestures:**
- Tap to select products
- Swipe to scroll
- Pinch to zoom (on PDFs)

**Performance:**
- Generating large PDFs (20+ products) may be slower on mobile
- Use Wi-Fi for best results
- Keep browser app updated

---

## Advanced Features

### Bulk Selection (Coming Soon)

Select multiple products at once using checkboxes or Shift+Click.

### Saved Configurations (Pro)

Save frequently used product sets for quick generation.

### Collaborative Selection (Agency)

Share selection link with team members for collaborative packet building.

### Custom Templates (Agency)

Apply different PDF templates based on project type or client.

---

© 2025 Webstuffguy Labs. All rights reserved.
