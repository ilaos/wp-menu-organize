# Submittal & Spec Sheet Builder - Documentation
## Professional Submittal Packet Generation for WordPress

**Version:** 1.0.2
**Last Updated:** October 9, 2025
**WordPress Required:** 6.0+
**PHP Required:** 7.4+

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Features Overview](#features-overview)
3. [Installation Guide](#installation-guide)
4. [User Guide](#user-guide)
5. [Admin Settings](#admin-settings)
6. [Product Management](#product-management)
7. [Branding Customization](#branding-customization)
8. [PDF Generation](#pdf-generation)
9. [Keyboard Shortcuts](#keyboard-shortcuts)
10. [Troubleshooting](#troubleshooting)
11. [Developer Resources](#developer-resources)
12. [FAQ](#faq)
13. [Support](#support)

---

## Getting Started

Submittal & Spec Sheet Builder is a WordPress plugin designed specifically for manufacturers and distributors who need to create professional submittal packets and specification sheets quickly and efficiently.

### What is a Submittal Packet?

A submittal packet is a collection of product specification sheets, typically required for:
- **Construction Projects** - Architects and contractors need detailed product specs
- **Bid Proposals** - Suppliers need to submit product documentation
- **Code Compliance** - Building inspectors require certified specifications
- **Project Documentation** - Engineers need technical data sheets

### How It Works

1. **Import Your Product Catalog** - Bulk import products with specifications
2. **Customers Select Products** - Interactive product browser with search and filters
3. **Generate PDF Packets** - Professional, branded PDFs with all specifications
4. **Download & Share** - Instant download of complete submittal packets

---

## Features Overview

### 🎯 Core Features

#### Product Catalog Management
- Import products from CSV/Excel
- Hierarchical category organization (Categories → Types → Products)
- Unlimited custom specifications per product
- Bulk editing and updates
- Product search and filtering

#### Interactive Product Browser
- **Gallery & List Views** - Toggle between visual grid or compact list
- **Live Search** - Instant filtering across model numbers, SKUs, and specs
- **Category Filters** - Drill down through product hierarchy
- **Sticky Selection Counter** - Always see how many products are selected
- **Keyboard Navigation** - Full keyboard accessibility support

#### Professional PDF Generation
- **Branded Cover Page** - Your logo, colors, and company info
- **Table of Contents** - Auto-generated with page numbers
- **Summary Page** - Quick reference table of all selected products
- **Individual Spec Sheets** - One page per product with full specifications
- **Navigation Bookmarks** - PDF bookmarks for easy jumping
- **Print-Ready** - Optimized for both screen and print

#### Branding Customization
- Upload company logo
- Custom brand color (used throughout PDFs)
- Company contact information
- Custom tagline/description
- Footer customization

### ⚡ Performance Features

- **Fast PDF Generation** - Server-side rendering with DomPDF
- **Smart Caching** - Reduces server load for repeat generations
- **Optimized Search** - Instant product filtering
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile
- **Progressive Enhancement** - Degrades gracefully in older browsers

### ♿ Accessibility Features

- **WCAG AA Compliant** - Meets accessibility standards
- **Keyboard Navigation** - Tab, Enter, Space key support
- **Screen Reader Support** - Comprehensive ARIA labels
- **Focus Indicators** - Clear visual focus states
- **High Contrast** - Readable color combinations

---

## Installation Guide

### Standard Installation

1. **Download the Plugin**
   - Purchase and download from our website
   - Or download from WordPress.org (free version)

2. **Upload to WordPress**
   ```
   1. Log into your WordPress admin dashboard
   2. Navigate to Plugins → Add New
   3. Click "Upload Plugin"
   4. Choose the downloaded ZIP file
   5. Click "Install Now"
   6. Click "Activate"
   ```

3. **Initial Setup**
   - Navigate to **Submittal Builder** in the admin menu
   - Complete the Setup Wizard:
     - Import your product catalog
     - Configure branding settings
     - Test PDF generation

### Manual Installation

For advanced users or custom server configurations:

1. **Extract Files**
   ```bash
   unzip submittal-builder.zip
   ```

2. **Upload via FTP**
   ```
   Upload to: /wp-content/plugins/submittal-builder/
   ```

3. **Set Permissions**
   ```bash
   chmod 755 /wp-content/plugins/submittal-builder/
   chmod 755 /wp-content/uploads/submittal-packets/
   ```

4. **Activate**
   - Go to Plugins in WordPress admin
   - Click "Activate" under Submittal & Spec Sheet Builder

### Server Requirements

**Minimum Requirements:**
- **WordPress:** 6.0 or higher
- **PHP:** 7.4 or higher (8.1+ recommended)
- **Memory:** 128MB PHP memory limit (256MB recommended)
- **Extensions:** dom, gd, mbstring
- **Disk Space:** 50MB for plugin + space for generated PDFs

**Recommended Configuration:**
- **PHP:** 8.1 or 8.2
- **Memory:** 256MB or higher
- **Max Execution Time:** 60 seconds
- **Upload Directory:** Writable permissions
- **Permalinks:** Pretty permalinks enabled

**Tested Environments:**
- ✅ Apache 2.4+
- ✅ Nginx 1.18+
- ✅ LiteSpeed 5.4+
- ✅ Shared hosting (Bluehost, SiteGround, WP Engine)
- ✅ VPS/Dedicated servers
- ✅ Managed WordPress hosting

---

## User Guide

### Accessing the Builder

**For Site Visitors:**
- Navigate to the page where you've placed the `[submittal_builder]` shortcode
- Or use the direct URL: `yoursite.com/builder/` (if configured)

**For Logged-in Users:**
- Admin menu: **Submittal Builder → Create New**
- Or use the admin bar shortcut

### Step 1: Select Products

#### Using the Product Browser

**Gallery View (Default):**
- Products displayed as cards in a 2-column grid
- Each card shows:
  - Product model number (bold)
  - Type badge (colored chip)
  - Category (gray breadcrumb)
  - Key specifications (inline format)

**List View:**
- Compact single-column list
- Shows same information in horizontal layout
- Better for browsing many products quickly

#### Selecting Products

**Click-Anywhere Selection:**
- Click anywhere on a product card to add it
- Click again to remove it
- No need to find a specific "Add" button

**Visual Feedback:**
- Selected cards show green border
- "✓ ADDED" pill appears in top-right corner
- Background gets subtle green tint
- Selection counter updates instantly

**Keyboard Selection:**
1. Press `Tab` to navigate between products
2. Press `Enter` or `Space` to select/deselect
3. Focus indicator shows current position
4. Screen readers announce selection state

#### Searching & Filtering

**Search Box:**
- Type model numbers, SKUs, or spec values
- Results update as you type
- Searches across all product data
- Clear with "X" button or `Esc` key

**Category Filters:**
- Click a category to see only those products
- Active category highlighted in soft blue
- Click again to show all products
- Product count shown next to each category

**Clear All Filters:**
- Click "Clear all filters" button
- Resets search and category filters
- Returns to full product list

#### Selection Management

**Sticky Selection Counter:**
- Shows "Selected: [number]" at all times
- Click "VIEW →" to go to review step
- Always visible as you scroll

**Selected Products Tray (Optional):**
- Slide-out panel on desktop
- Shows all selected products
- Remove individual items
- Organized by category
- Click "Continue" to proceed

### Step 2: Review & Customize

#### Review Selected Products

**Product List:**
- Grouped by category
- Drag handles for reordering (⋮)
- Remove button (×) for each product
- Shows model number and key specs

**Reordering Products:**
- **Mouse:** Click and drag products up/down
- **Keyboard:**
  - Focus on a product (Tab)
  - Hold `Alt` + `↑` to move up
  - Hold `Alt` + `↓` to move down

**Remove Products:**
- Click the (×) button
- Or press `Delete` when focused
- Confirm if prompted

#### Project Information (Optional)

**Project Name:**
- Appears on PDF cover page
- Examples: "Building A Renovation", "North Wing Project"
- Optional but recommended
- Max 100 characters

**Project Notes:**
- Appears on PDF summary page
- Add special instructions, notes, or requirements
- Examples: "All products in stainless steel finish"
- Optional, up to 500 characters
- Supports line breaks

#### Branding Preview

**Preview Card Shows:**
- Your company logo
- Brand color (used in PDF)
- Company name
- Website URL
- Primary color swatch

**To Change Branding:**
- Click "Edit Branding Settings"
- Opens admin settings page
- Make changes and save
- Return to builder to continue

### Step 3: Generate PDF

#### Final Review

Before generating, verify:
- All desired products are selected
- Products are in correct order
- Project name is correct (if provided)
- Notes are accurate (if provided)

#### Generate Button

**Click "Generate PDF":**
1. Loading overlay appears
2. Server generates PDF (usually 5-15 seconds)
3. Success page appears when ready

**Generation Time:**
- Small packets (1-10 products): 3-5 seconds
- Medium packets (11-50 products): 8-15 seconds
- Large packets (51+ products): 20-30 seconds

**What's Happening:**
1. Server compiles product data
2. Applies branding styles
3. Generates cover, TOC, summary, and spec sheets
4. Creates PDF bookmarks for navigation
5. Saves PDF to secure location
6. Returns download link

#### Success Page

**Options:**

**Open PDF:**
- Opens PDF in new browser tab
- Preview before downloading
- Check formatting and content
- Can still download from preview

**Download PDF:**
- Browser's default download location
- Filename: `submittal-packet-YYYY-MM-DD-HHMMSS.pdf`
- Example: `submittal-packet-2025-10-09-143022.pdf`

**Start Over:**
- Clears all selections
- Returns to Step 1
- Fresh builder session

**Share PDF:**
- Copy the temporary download link
- Valid for 45 days (configurable)
- Send to clients, architects, or contractors

---

## Admin Settings

### Accessing Settings

**Location:** WordPress Admin → **Submittal Builder** → **Settings**

### General Settings

#### Product Catalog

**Import Products:**
- Click "Import CSV/Excel"
- Map columns to fields
- Review preview
- Import products

**Product Table:**
- View all products
- Edit inline
- Bulk actions (delete, update)
- Export to CSV

#### Frontend Settings

**Builder Page:**
- Select page for `[submittal_builder]` shortcode
- Or create new page automatically
- Set custom slug (default: `/builder/`)

**Access Control:**
- **Public:** Anyone can use builder
- **Logged-in Users:** Require login
- **Specific Roles:** Limit to certain user roles

**Product Display:**
- Default view (Gallery or List)
- Products per page
- Show/hide specifications
- Enable/disable search

### Branding Settings

#### Company Information

**Company Name:**
- Required field
- Appears on PDF cover page
- Max 100 characters

**Logo Upload:**
- Recommended size: 300×100px (3:1 ratio)
- Formats: PNG (transparent), JPG, SVG
- Max file size: 2MB
- Used on PDF cover page

**Primary Brand Color:**
- Color picker interface
- Used for:
  - PDF cover page background
  - Section headers
  - Table borders
  - Accent elements

**Contact Information:**
- Website URL
- Phone number
- Email address
- Physical address
- Appears on PDF cover page

**Tagline/Description:**
- Short company description
- Max 200 characters
- Appears below logo on cover

#### PDF Settings

**Header & Footer:**
- Show/hide page numbers
- Footer text customization
- Left, center, right alignment

**Page Layout:**
- Portrait or Landscape
- Margins (top, right, bottom, left)
- Paper size (Letter, A4, Legal)

**Content Options:**
- Include cover page (checkbox)
- Include table of contents (checkbox)
- Include summary page (checkbox)
- Include individual spec sheets (checkbox)

### Advanced Settings

#### Performance

**PDF Generation:**
- Enable/disable caching
- Cache duration (hours)
- Max concurrent generations
- Timeout (seconds)

**Search Indexing:**
- Rebuild search index
- Index custom fields
- Stop words configuration

#### Storage

**Draft Management:**
- Auto-save drafts (checkbox)
- Draft expiry (days)
- Auto-purge old drafts

**PDF Storage:**
- Upload directory path
- Max storage size
- Old file cleanup

#### API Access

**REST API:**
- Enable/disable API
- Generate API keys
- View API documentation
- Rate limiting settings

---

## Product Management

### Product Structure

**Hierarchy:**
```
Category (e.g., "C-Studs")
  └── Type (e.g., "20 Gauge")
      └── Products (e.g., "362S162-20", "600S162-20")
```

**Composite Key:**
- Format: `category-slug:type-slug:product-slug`
- Example: `c-studs:20-gauge:362s162-20`
- Used for uniqueness and relationships

### Adding Products

#### Manual Entry

1. **Navigate:** Submittal Builder → Products → Add New
2. **Fill in Details:**
   - Model Number (required)
   - Category (dropdown)
   - Type (dropdown)
   - SKU/Part Number
   - Specifications (key-value pairs)
3. **Click "Save Product"**

#### Bulk Import

**CSV Format:**
```csv
model,category,type,sku,size,thickness,flange,ksi
362S162-20,C-Studs,20 Gauge,CS-362-20,3-5/8",33 mil,1-5/8",50
```

**Import Steps:**
1. Click "Import Products"
2. Upload CSV file
3. Map columns to fields
4. Preview import (first 5 rows)
5. Click "Import"
6. Review results

**Best Practices:**
- First row should be column headers
- Use consistent category/type names
- Include SKU for easier searching
- Add as many specs as needed

### Editing Products

#### Individual Edit

1. Go to: Submittal Builder → Products
2. Click "Edit" on any product
3. Modify fields
4. Click "Update Product"

#### Bulk Edit

1. Select multiple products (checkboxes)
2. Choose "Bulk Edit" from dropdown
3. Select fields to update
4. Apply changes
5. Click "Update"

**Bulk Actions:**
- Edit selected
- Delete selected
- Change category
- Change type
- Export to CSV

### Product Specifications

#### Common Spec Fields

**Structural Products:**
- Size/Dimensions
- Gauge/Thickness
- Flange width
- KSI (yield strength)
- Material (steel, aluminum)
- Coating/Finish

**How Specs Appear:**

**In Product Cards:**
```
Size: 3-5/8" · Thick: 33 mil (20 ga)
KSI: 50 · Flange: 1-5/8"
```

**In PDF Spec Sheets:**
- Table format with labels
- Two-column layout
- Bold labels, regular values

#### Custom Specifications

Add any specification you need:
- Weight
- Load capacity
- Dimensions (L×W×H)
- Material composition
- Certifications (UL, CSA)
- Fire rating
- Acoustic rating
- Thermal properties

**Adding Custom Specs:**
1. Edit product
2. Scroll to "Specifications" section
3. Click "Add Specification"
4. Enter Label (e.g., "Fire Rating")
5. Enter Value (e.g., "2-hour")
6. Click "Save"

---

## Branding Customization

### Logo Guidelines

**Recommended Specifications:**
- **Format:** PNG with transparent background
- **Size:** 300×100 pixels (3:1 ratio)
- **File Size:** Under 2MB
- **DPI:** 150 dpi or higher for print quality

**Logo Placement:**
- PDF cover page (centered)
- Scales to fit max width: 180px
- Maintains aspect ratio

**Best Practices:**
- Use vector format (SVG) if available
- Ensure logo is legible at small sizes
- High contrast with cover background color

### Color Selection

**Choosing Your Brand Color:**
1. Go to: Submittal Builder → Settings → Branding
2. Click color picker
3. Enter hex code or choose visually
4. Preview in real-time
5. Save changes

**Where Color is Used:**
- PDF cover page background
- Section headers
- Table borders and headers
- Accent lines and dividers
- Bookmark icons (in PDF viewers)

**Color Tips:**
- Dark colors work best for cover pages
- Ensure text contrast (white text should be readable)
- Test PDF generation after changing
- Consider print appearance (may differ from screen)

### Cover Page Customization

**Elements You Control:**
- Company logo (top center)
- Company name (below logo)
- Tagline/description (optional)
- Brand color (background)
- Contact information (bottom)

**Fixed Elements:**
- "Submittal Packet" title
- Date generated
- Number of products included
- Layout structure

### Contact Information

**What to Include:**

**Website:**
- Full URL with https://
- Example: `https://yourcompany.com`

**Phone:**
- Format as preferred
- Examples: `(555) 123-4567` or `+1-555-123-4567`

**Email:**
- Company email (not personal)
- Example: `info@yourcompany.com`

**Address:**
- Street address (optional)
- City, State, ZIP
- Multi-line format supported

---

## PDF Generation

### PDF Structure

Every generated PDF includes:

#### 1. Cover Page
- Company logo and name
- Brand color background
- "Submittal Packet" title
- Project name (if provided)
- Generation date
- Product count
- Contact information footer

#### 2. Table of Contents
- Clickable page links
- Section headers
- Product list with page numbers
- Auto-generated from selected products

#### 3. Summary Page
- Table of all products
- Columns: Model, Category, Type, Key Specs
- Compact one-page reference
- Perfect for quick scanning

#### 4. Individual Product Sheets
- One page per product
- Large model number heading
- Category and type badges
- Full specifications table
- Company branding footer

### PDF Features

#### Navigation Bookmarks

**What are Bookmarks?**
- Sidebar navigation in PDF viewers
- Click to jump to sections
- Hierarchical structure

**Bookmark Structure:**
```
📄 Cover Page
📄 Table of Contents
📄 Summary
📁 Product Specifications
  └── Model 362S162-20
  └── Model 600S162-20
  └── Model 800S162-20
```

#### Print Optimization

**Print Settings:**
- Standard paper sizes (Letter, A4)
- Proper margins for binding
- Page breaks avoid splitting specs
- Headers/footers on every page

**Print Tips:**
- Use "Actual Size" not "Fit to Page"
- Enable headers and footers
- Check "Print Backgrounds" for brand color
- Use high-quality print settings

#### File Size Optimization

**Typical Sizes:**
- Small packet (5 products): 200-300KB
- Medium packet (20 products): 500KB-1MB
- Large packet (50 products): 1-2MB

**Size Factors:**
- Logo file size
- Number of products
- Specification count
- Page layout complexity

**Optimization Tips:**
- Optimize logo file (compress PNG)
- Use SVG for smallest file size
- Limit unnecessary specifications

### Download & Sharing

#### Temporary Download Links

**Link Characteristics:**
- Generated after each PDF creation
- Valid for 45 days (default, configurable)
- Accessible without login
- Unique, non-guessable URL

**Link Format:**
```
https://yoursite.com/wp-content/uploads/submittal-packets/
submittal-packet-2025-10-09-143022-a8f9e3c2.pdf
```

**Security:**
- Random token appended to filename
- Directory listing disabled
- Direct linking blocked (except via builder)
- Automatic cleanup after expiry

#### Sharing Best Practices

**Email Sharing:**
- Download PDF first
- Attach to email (not link)
- Links expire after 45 days
- Include project context

**Cloud Storage:**
- Upload to Dropbox, Google Drive, etc.
- Share via cloud service links
- Better for long-term storage
- No expiration concerns

**Client Portals:**
- Upload to project management systems
- Include in bid packages
- Archive with project documents

---

## Keyboard Shortcuts

### Product Selection (Step 1)

| Key | Action |
|-----|--------|
| `Tab` | Navigate to next product card |
| `Shift + Tab` | Navigate to previous product card |
| `Enter` | Select/deselect focused product |
| `Space` | Select/deselect focused product |
| `/` | Focus search box |
| `Esc` | Clear search, remove focus |

### Review & Reorder (Step 2)

| Key | Action |
|-----|--------|
| `Tab` | Navigate between products |
| `Alt + ↑` | Move focused product up |
| `Alt + ↓` | Move focused product down |
| `Delete` | Remove focused product |
| `Enter` | Toggle focus on product |

### Navigation

| Key | Action |
|-----|--------|
| `Tab` | Navigate through interactive elements |
| `Enter` | Activate buttons and links |
| `Esc` | Close dialogs and trays |
| `Ctrl/Cmd + Click` | Open links in new tab |

### Accessibility Tips

**For Screen Reader Users:**
- Products announce as buttons with state
- "Not selected" or "Selected" status
- Instructions included in labels
- Remove buttons labeled clearly

**For Keyboard-Only Users:**
- All features accessible via keyboard
- Focus indicators clearly visible
- Skip links available (if theme supports)
- Tab order is logical

**For Low Vision Users:**
- High contrast mode supported
- Text scales with browser zoom
- Focus indicators are 3px thick
- Color is not the only indicator

---

## Troubleshooting

### Common Issues

#### PDF Generation Fails

**Symptoms:**
- Error message after clicking "Generate"
- Infinite loading spinner
- Blank page or timeout

**Solutions:**

1. **Check PHP Memory Limit**
   ```php
   // In wp-config.php, add:
   define('WP_MEMORY_LIMIT', '256M');
   ```

2. **Increase Max Execution Time**
   ```php
   // In wp-config.php, add:
   set_time_limit(120);
   ```

3. **Check File Permissions**
   ```bash
   chmod 755 /wp-content/uploads/submittal-packets/
   ```

4. **Enable Debug Mode**
   ```php
   // In wp-config.php, add:
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   // Check wp-content/debug.log for errors
   ```

5. **Reduce Packet Size**
   - Try generating fewer products
   - Check if specific products cause issues
   - Remove unnecessary specifications

#### Products Not Displaying

**Symptoms:**
- Empty product grid
- "No products found" message
- Categories not loading

**Solutions:**

1. **Verify Products Exist**
   - Check: Submittal Builder → Products
   - Ensure products are published
   - Check category assignments

2. **Clear Browser Cache**
   ```
   Ctrl + F5 (Windows)
   Cmd + Shift + R (Mac)
   ```

3. **Check Shortcode**
   ```
   Correct: [submittal_builder]
   Incorrect: [submittal-builder] or [builder]
   ```

4. **Rebuild Search Index**
   - Go to: Settings → Advanced
   - Click "Rebuild Search Index"
   - Wait for completion

5. **Check JavaScript Console**
   ```
   Press F12 → Console tab
   Look for error messages
   ```

#### Search Not Working

**Symptoms:**
- Search returns no results
- Search box doesn't respond
- Results don't update

**Solutions:**

1. **Check Search Tokens**
   - Products need searchable data
   - Re-import products if needed
   - Rebuild search index

2. **Clear Browser Cache**
   - Hard refresh (Ctrl+F5)
   - Clear cookies and site data

3. **Test Search Terms**
   - Try exact model numbers
   - Use fewer words
   - Check spelling

4. **Disable Browser Extensions**
   - Try incognito/private mode
   - Disable ad blockers
   - Disable script blockers

#### Branding Not Showing in PDF

**Symptoms:**
- Logo missing from cover page
- Wrong brand color
- Old company name

**Solutions:**

1. **Check File Upload**
   - Re-upload logo
   - Verify file format (PNG, JPG, SVG)
   - Check file size (under 2MB)

2. **Clear PDF Cache**
   - Go to: Settings → Advanced
   - Click "Clear PDF Cache"
   - Generate new PDF

3. **Check Branding Settings**
   - Verify all fields saved
   - Click "Save Changes" button
   - Refresh builder page

4. **Test Logo File**
   - Open logo file directly
   - Ensure it displays correctly
   - Try different file format

#### Slow Performance

**Symptoms:**
- Slow product loading
- Laggy search
- Slow PDF generation

**Solutions:**

1. **Optimize Product Count**
   - Limit displayed products
   - Use category filters
   - Enable pagination

2. **Optimize Images**
   - Compress logo file
   - Use appropriate file size
   - Consider SVG format

3. **Server Resources**
   - Upgrade hosting plan
   - Enable caching plugin
   - Use CDN for static assets

4. **Database Optimization**
   - Run database cleanup
   - Optimize tables
   - Remove old drafts

### Browser Compatibility Issues

#### Older Browsers

**Internet Explorer 11:**
- ⚠️ Limited support
- Basic functionality works
- Animations may not work
- Use Edge or Chrome instead

**Workaround:**
```html
<!-- Add to theme header -->
<script src="focus-visible-polyfill.js"></script>
```

#### Mobile Browsers

**iOS Safari:**
- ✅ Fully supported iOS 14+
- ⚠️ localStorage issues in Private Mode
- Use regular mode for best experience

**Android Chrome:**
- ✅ Fully supported
- Performance may vary on older devices

### Getting More Help

**Check Debug Log:**
```
Location: wp-content/debug.log
Enable with: WP_DEBUG and WP_DEBUG_LOG
```

**System Info:**
```
WordPress Admin → Tools → Site Health
Check for server issues
```

**Contact Support:**
- See [Support](#support) section below

---

## Developer Resources

### For Theme Developers

#### Shortcode Usage

**Basic Shortcode:**
```php
[submittal_builder]
```

**With Parameters:**
```php
[submittal_builder view="list" category="c-studs"]
```

**Parameters:**
- `view` - Default view: `grid` or `list`
- `category` - Pre-filter by category slug
- `type` - Pre-filter by type slug
- `hide_search` - Hide search box: `true` or `false`

#### Template Overrides

Copy templates to your theme:
```
Theme Location:
your-theme/submittal-builder/templates/

Available Templates:
- frontend/builder.php
- frontend/partials/header.php
- frontend/partials/step-products.php
- frontend/partials/step-review.php
- frontend/partials/step-generate.php
```

#### Styling Hooks

**CSS Classes Available:**
```css
.sfb-builder-wrapper { }
.sfb-product-card { }
.sfb-product-card-selected { }
.sfb-products-toolbar { }
.sfb-sidebar { }
.sfb-category-item-active { }
```

**CSS Custom Properties:**
```css
:root {
  --sfb-primary: #3b49df;
  --sfb-ok: #18a865;
  --sfb-badge-bg: #eef2ff;
  --sfb-focus: #5b9cff;
  /* Override in your theme */
}
```

### For Plugin Developers

#### REST API Endpoints

**Authentication:**
```php
// All requests require WordPress nonce or API key
X-WP-Nonce: [nonce]
// Or
Authorization: Bearer [api_key]
```

**List Products:**
```http
GET /wp-json/submittal-builder/v1/products
```

**Create Product:**
```http
POST /wp-json/submittal-builder/v1/products
Content-Type: application/json

{
  "model": "362S162-20",
  "category": "C-Studs",
  "type": "20 Gauge",
  "specs": {
    "size": "3-5/8\"",
    "thickness": "33 mil"
  }
}
```

**Full API Documentation:**
→ [API-REFERENCE.md](./API-REFERENCE.md) (27 endpoints)

#### WordPress Hooks

**Filters:**
```php
// Customize PDF theme
add_filter('sfb_pdf_theme', function($theme) {
  return 'custom-theme';
});

// Override brand color
add_filter('sfb_pdf_color', function($color) {
  return '#FF0000';
});

// Modify feature availability
add_filter('sfb_features_map', function($features) {
  $features['custom_feature'] = true;
  return $features;
});
```

**Actions:**
```php
// After PDF generation
add_action('sfb_after_pdf_generated', function($pdf_path, $products) {
  // Send notification, log analytics, etc.
}, 10, 2);

// Before product import
add_action('sfb_before_product_import', function($data) {
  // Validate or modify import data
});
```

**Full Hooks Documentation:**
→ [DEVELOPER-HOOKS.md](./DEVELOPER-HOOKS.md) (6 filters, multiple actions)

### Customization Examples

#### Change Card Density
```css
.sfb-product-card {
  padding: 16px 18px 14px; /* More spacious */
}

.sfb-products-grid {
  gap: 1.5rem; /* Larger gap between cards */
}
```

#### Custom Badge Colors
```css
.badge--type {
  background: #ff6b35;
  color: white;
}

.crumb--category {
  color: #666;
  font-style: italic;
}
```

#### Disable Animations
```css
.sfb-product-card-selected::after {
  animation: none !important;
}

.sfb-product-card {
  transition: none !important;
}
```

#### Custom Selection Color
```css
:root {
  --sfb-ok: #10b981; /* Different green */
  --sfb-ok-bg: #d1fae5; /* Light green tint */
  --sfb-ok-border: #6ee7b7; /* Border color */
}
```

**Full Customization Guide:**
→ [UI-POLISH-GUIDE.md](./UI-POLISH-GUIDE.md) (300+ lines)

---

## FAQ

### General Questions

**Q: Is this plugin free?**
A: Yes, the core version is free on WordPress.org. Pro version available with additional features (drafts, advanced analytics, priority support).

**Q: What's the difference between Free and Pro?**
A: Pro includes:
- Save drafts for later
- Advanced analytics dashboard
- Priority email support
- White-label options
- Custom PDF templates
- API access for integrations

**Q: Can I use this on multiple sites?**
A: Free version: unlimited sites. Pro version: license-based (1, 5, or unlimited sites).

**Q: Is it compatible with page builders?**
A: Yes! Compatible with:
- Elementor (use shortcode widget)
- Beaver Builder (use HTML module)
- Divi (use code module)
- Gutenberg (use shortcode block)
- Classic Editor (use shortcode)

### Technical Questions

**Q: How many products can I add?**
A: No hard limit. Tested with 1,000+ products. Performance depends on server resources.

**Q: What file formats for import?**
A: CSV and Excel (.xlsx). Tab-delimited and pipe-delimited also supported.

**Q: Can I export products?**
A: Yes. Go to Products → Export. Choose CSV or Excel format.

**Q: Is it translation-ready?**
A: Yes! POT file included. Compatible with WPML, Polylang, and TranslatePress.

**Q: Does it work with WooCommerce?**
A: Not directly. This is for submittal packets, not eCommerce. Can integrate via custom code.

**Q: Can I customize the PDF layout?**
A: Limited customization in settings. Pro version supports custom templates.

**Q: Is the PDF generation server-side?**
A: Yes. Uses DomPDF library. No external services or API calls.

### Troubleshooting Questions

**Q: PDF generation is slow. Why?**
A: Common causes:
- Too many products (try smaller batches)
- Low PHP memory (increase to 256MB)
- Slow server (upgrade hosting)
- Large logo file (optimize/compress)

**Q: Products aren't showing up. Why?**
A: Check:
- Products are published (not drafts)
- Category is assigned
- Shortcode is correct: `[submittal_builder]`
- JavaScript not blocked by firewall/plugin

**Q: Search returns no results. Why?**
A: Try:
- Rebuild search index (Settings → Advanced)
- Check search terms (exact model numbers work best)
- Verify products have searchable data

**Q: Logo doesn't appear in PDF. Why?**
A: Check:
- File uploaded successfully (Settings → Branding)
- File format is PNG, JPG, or SVG
- File size under 2MB
- Permissions on uploads directory

**Q: Colors look different in PDF than on screen. Why?**
A: PDF uses different color space (CMYK vs RGB). Slight variations normal. For exact colors, use CMYK hex values.

---

## Support

### Documentation

**Complete Documentation:**
- [User Guide](#user-guide) - This page
- [API Reference](./API-REFERENCE.md) - REST API docs
- [Developer Hooks](./DEVELOPER-HOOKS.md) - Filters and actions
- [UI Polish Guide](./UI-POLISH-GUIDE.md) - Customization guide

### Getting Help

**Free Support:**
- **WordPress.org Forum:** https://wordpress.org/support/plugin/submittal-builder/
- **Response Time:** 24-48 hours
- **Coverage:** Installation, bugs, compatibility

**Pro Support (Pro Users Only):**
- **Email:** support@webstuffguylabs.com
- **Response Time:** 4-8 hours (business days)
- **Coverage:** Everything + customization help

**Before Requesting Support:**

1. **Check Documentation** - Answer may be in this guide
2. **Search Forums** - Someone may have had same issue
3. **Enable Debug Mode** - See [Troubleshooting](#troubleshooting)
4. **Check System Health** - WordPress → Tools → Site Health

**When Requesting Support, Include:**

- WordPress version
- PHP version
- Plugin version
- Theme name
- Description of issue
- Steps to reproduce
- Screenshots (if applicable)
- Error messages from debug.log

### Feature Requests

Have an idea for a feature? We'd love to hear it!

**Submit Feature Requests:**
- GitHub Issues (if public repo exists)
- WordPress.org Forum (tag: [Feature Request])
- Email: features@webstuffguylabs.com

**What Happens Next:**
1. We review all requests
2. Popular requests get prioritized
3. Major features considered for Pro version
4. Updates posted in changelog

### Bug Reports

Found a bug? Please report it!

**How to Report:**
1. Check if already reported (search forums)
2. Gather details (see "When Requesting Support" above)
3. Submit via WordPress.org or email
4. Include steps to reproduce

**Bug Fix Timeline:**
- **Critical Bugs** - Hotfix within 24-48 hours
- **Major Bugs** - Fix in next minor version (2-3 weeks)
- **Minor Bugs** - Fix in next major version (2-3 months)

### Stay Updated

**Release Notes:**
- WordPress.org changelog
- Email notifications (opt-in)
- Admin notice for major updates

**Social Media:**
- Twitter: @webstuffguylabs (if exists)
- Facebook: /webstuffguylabs (if exists)
- LinkedIn: /webstuffguylabs (if exists)

### Resources

**Official Links:**
- **Website:** https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/
- **Documentation:** https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/documentation/
- **Changelog:** https://wordpress.org/plugins/submittal-builder/#developers
- **Download:** https://wordpress.org/plugins/submittal-builder/

---

## About

**Submittal & Spec Sheet Builder** is developed by WebStuff Guy Labs, a team dedicated to creating professional WordPress solutions for manufacturers, distributors, and construction industry professionals.

**Version:** 1.0.2
**Last Updated:** October 9, 2025
**License:** GPL v2 or later

---

© 2025 WebStuff Guy Labs. All rights reserved.

