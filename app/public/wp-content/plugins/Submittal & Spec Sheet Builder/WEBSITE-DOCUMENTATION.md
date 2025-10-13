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
8. [Agency Features](#agency-features)
9. [PDF Generation](#pdf-generation)
10. [Keyboard Shortcuts](#keyboard-shortcuts)
11. [Troubleshooting](#troubleshooting)
12. [Developer Resources](#developer-resources)
13. [FAQ](#faq)
14. [Support](#support)

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

1. **Build Your Product Catalog** - Add products with specifications through the admin interface
2. **Customers Select Products** - Interactive product browser with search and filters
3. **Generate PDF Packets** - Professional, branded PDFs with all specifications
4. **Download & Share** - Instant download of complete submittal packets

---

## Features Overview

### 🎯 Core Features

#### Product Catalog Management
- Add products through admin interface
- Hierarchical category organization (Categories → Types → Products)
- Unlimited custom specifications per product
- Product search and filtering
- Category management

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

## Saving & Loading Drafts (Pro)

**Note:** Draft functionality requires an active Pro or Agency license.

### What are Drafts?

Drafts allow users to save their product selections and project information in-progress, then return later to complete and generate the PDF. This is perfect for complex projects that require multiple sessions or collaboration.

### Two Types of Saves

**1. Local Autosave (Browser Storage)**
- **Available:** All license tiers (Free, Pro, Agency)
- **Storage:** Saved in your browser's localStorage
- **Persistence:** Remains until you clear browser data or complete the PDF
- **Privacy:** Only accessible on the same device and browser
- **Offline:** Works without internet connection
- **Automatic:** Saves every time you select/deselect a product

**2. Server-Stored Draft (Shareable)**
- **Available:** Pro and Agency licenses only
- **Storage:** Saved in WordPress database as custom post type
- **Persistence:** Expires after configured period (default: 45 days)
- **Privacy:** Generates unique, unlisted link
- **Shareable:** Link can be sent to colleagues or clients
- **Manual:** User clicks "Save Progress" button to create

### How to Save a Draft (Pro/Agency)

**Step 1: Build Your Selection**
1. Select products on Step 1 (Product Selection)
2. Optionally add project name and notes on Step 2 (Review)
3. Click **"Save Progress"** button (appears in toolbar)

**Step 2: Get Your Draft Link**
1. Modal appears with unique draft URL
2. URL format: `yoursite.com/builder/?sfb_draft=abc123xyz`
3. Link is valid for expiry period (default: 45 days)
4. Copy link to clipboard or email it

**What Gets Saved:**
- ✅ All selected products (with IDs)
- ✅ Project name (if entered)
- ✅ Project notes (if entered)
- ✅ Product order (if reordered)
- ✅ Timestamp of when draft was created
- ❌ Branding settings (uses current site settings when loaded)
- ❌ Generated PDFs (created fresh when draft is finalized)

### How to Load a Draft

**From a Draft Link:**
1. Open the draft URL in your browser
2. Builder loads with saved selections pre-populated
3. All products automatically added to selection
4. Project name and notes restored (if saved)
5. Make any changes needed
6. Generate PDF when ready

**From Local Autosave:**
1. Return to builder page on same device/browser
2. If local autosave exists, banner appears:
   > "📝 You have unsaved changes from [time]. Resume or start fresh?"
3. Click **"Resume"** to restore selections
4. Or click **"Start Fresh"** to clear and begin new session

### Draft Expiration

**Server Drafts:**
- Expire after configured period (Settings → Draft Settings → Draft Expiry)
- Default: 45 days
- Range: 1-365 days
- Expired drafts automatically deleted daily via WordPress cron
- After expiration, draft link shows error: "Draft not found or expired"

**Local Autosave:**
- Persists indefinitely in browser localStorage
- Cleared when:
  - User clears browser data/cache
  - User clicks "Start Fresh" on resume banner
  - PDF is successfully generated (autosave deleted)

### Use Cases

**1. Multi-Day Projects**
- Start selecting products today
- Save progress and return tomorrow
- No need to remember what you selected

**2. Collaboration**
- Build initial selection
- Share draft link with colleague
- They review, modify, and generate PDF

**3. Client Approvals**
- Create draft with recommended products
- Send link to client for review
- Client can modify and download when approved

**4. Quote Preparation**
- Sales team creates draft during site visit
- Returns to office to finalize specifications
- Generates professional PDF for customer

**5. Template Reuse**
- Create draft for common project type
- Use same link for similar future projects
- Modify products as needed per project

### Privacy & Security

**Draft URLs:**
- Unlisted (not searchable or indexed)
- Use random, non-guessable tokens
- No authentication required to access
- Consider URL as "password" - protect accordingly

**Data Stored:**
- Product IDs (not actual product data)
- Project name and notes (as entered by user)
- Timestamp metadata only
- No user personal information
- No IP addresses or tracking data

**Best Practices:**
- Only share draft links with trusted recipients
- Use expiry settings appropriate for your workflow
- Purge expired drafts regularly (Settings → Draft Settings)
- Don't rely on drafts for long-term storage (use PDFs)

---

## Admin Settings

### Accessing Settings

**Location:** WordPress Admin → **Submittal Builder** → **Settings**

### General Settings

#### Product Catalog

**Manage Products:**
- Add products via admin interface
- View all products in table
- Edit products inline
- Delete products as needed

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

#### Draft Management (Pro/Agency)

**Location:** WordPress Admin → **Submittal Builder** → **Settings** → **Draft Settings**

Configure how user selections are saved and managed throughout the building process.

**Enable Local Autosave (Browser Storage)**
- **Toggle:** Enable/disable automatic saving to browser localStorage
- **Default:** Enabled
- **How it works:** Every time a user selects or deselects a product, their progress is automatically saved in their browser
- **Benefits:** Works offline, instant save, no server resources used
- **Limitations:** Only accessible on same device/browser, cleared if browser data is cleared
- **Recommendation:** Keep enabled for better user experience

**Enable Server-Stored Drafts (Shareable)**
- **Toggle:** Enable/disable "Save Progress" feature (Pro/Agency only)
- **Default:** Enabled (if Pro/Agency license active)
- **How it works:** Users can click "Save Progress" to create a shareable draft link
- **Benefits:** Can be accessed from any device, shareable with others, survives browser clearing
- **Limitations:** Requires server storage, expires after configured period
- **Recommendation:** Enable for teams or client-facing sites

**Draft Expiry Period**
- **Setting:** Number of days before server drafts are automatically deleted
- **Default:** 45 days
- **Range:** 1-365 days
- **How it works:** WordPress cron job runs daily to purge expired drafts
- **Considerations:**
  - Shorter periods (7-30 days): Better for data privacy, reduces database bloat
  - Longer periods (60-180 days): Better for long-term projects, client follow-ups
  - Maximum (365 days): For archival purposes, requires manual cleanup
- **Recommendation:** 45 days for most use cases, 14 days for high-volume sites

**Rate Limiting (Draft Creation)**
- **Setting:** Minimum seconds between draft saves per IP address
- **Default:** 20 seconds
- **Range:** 5-120 seconds
- **Purpose:** Prevent spam/abuse of draft creation
- **How it works:** Same IP address cannot create multiple drafts within this timeframe
- **Recommendation:** 20 seconds for normal sites, increase to 60+ if experiencing abuse

**Privacy Notice (Optional)**
- **Setting:** Custom text shown below "Save Progress" button
- **Default:** Empty (no notice shown)
- **Use cases:**
  - GDPR compliance notices
  - Data retention policies
  - Terms of use for shared links
- **Example:** "Draft links expire after 45 days. Do not include sensitive information."
- **Recommendation:** Add notice if handling client data or operating in EU

**Manual Actions**

**Purge Expired Drafts Now**
- **Button:** "Purge Expired Drafts" (in Settings → Draft Settings)
- **Action:** Immediately deletes all drafts past expiry date
- **Use when:**
  - You've just reduced the expiry period
  - Database cleanup before backups
  - Investigating storage issues
- **Shows:** Count of purged drafts and remaining active drafts

**Draft Statistics**
- **Display:** Total drafts and expired count
- **Example:** "142 total • 23 expired"
- **Updates:** Real-time after purge operation
- **Use for:** Monitoring draft usage and database size

#### Storage

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

#### Adding Multiple Products

**Best Practices:**
- Use consistent category/type names
- Include SKU for easier searching
- Add comprehensive specifications
- Organize products systematically

### Editing Products

#### Individual Edit

1. Go to: Submittal Builder → Products
2. Click "Edit" on any product
3. Modify fields
4. Click "Update Product"

#### Bulk Actions

1. Select multiple products (checkboxes)
2. Choose action from dropdown
3. Apply changes

**Available Actions:**
- Delete selected products
- Change category
- Change type

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

## Agency Features

**Note:** Agency features are only available with an active Agency license. Agency tier is designed for multi-site deployments and white-label resellers.

### What is an Agency Pack?

An Agency Pack is a complete, reusable catalog template that includes:
- All products, categories, types, and models
- Complete specifications and custom fields
- Optional branding settings (logo, colors, company info)
- Optional product notes and descriptions

**Perfect For:**
- **Multi-Site Deployments** - Deploy identical catalogs across client sites
- **Fast Onboarding** - Get new installations up and running in minutes
- **Standardization** - Ensure consistency across all client sites
- **White-Label Solutions** - Deploy pre-configured catalogs with client branding

---

### Agency Library

**Location:** WordPress Admin → **Submittal Builder** → **📦 Agency Library**

The Agency Library is your central hub for managing reusable catalog templates.

#### Viewing Your Packs

**Pack Table Shows:**
- **Pack Name** - Descriptive name for identification
- **Products** - Number of products included
- **Branding** - Checkmark (✓) if Pack includes branding settings
- **Updated** - Last modified date
- **Actions** - Export JSON or Delete buttons

**Empty State:**
- Appears when you haven't created any Packs yet
- "Go to Builder" button for quick access
- Instructions for creating your first Pack

#### Creating an Agency Pack

**Step 1: Build Your Catalog**
1. Go to: **Submittal Builder** (admin page)
2. Add all products, categories, and types
3. Configure branding settings (logo, colors, company info)
4. Add product notes and descriptions as needed
5. Test to ensure everything works correctly

**Step 2: Save as Pack**
1. In the Builder admin page, click **💼 Save as Pack** button (top toolbar)
2. Modal opens with save options
3. Enter Pack name (e.g., "Industrial HVAC Starter Kit")
4. Choose options:
   - **Include branding settings** - Logo, colors, company info
   - **Include product notes** - Descriptions and internal notes
5. Click **Save Pack**
6. Success message appears with product count

**What Gets Saved:**
- ✅ All categories, types, products, models
- ✅ Complete node hierarchy and relationships
- ✅ All specification fields and values
- ✅ Product positions and sort order
- ✅ Branding settings (if checkbox enabled)
- ✅ Product notes (if checkbox enabled)
- ❌ Generated PDFs (these are created fresh on each site)
- ❌ User data or license info
- ❌ WordPress settings unrelated to catalog

#### Exporting Agency Packs

**Download Pack as JSON:**
1. Go to: **Agency Library** page
2. Find the Pack you want to export
3. Click **Export JSON** button
4. JSON file downloads automatically
5. Filename: `{pack-name}.json` (e.g., `Industrial-HVAC-Starter-Kit.json`)

**JSON File Contains:**
- Complete Pack data structure
- All nodes with full metadata
- Optional branding settings
- Ready for import on any site

**File Size:**
- Typical Pack: 50-200KB
- Large Pack (100+ products): 500KB-1MB
- Depends on specification count and notes

**Security:**
- Download protected by WordPress nonce
- One-time use link per export
- Admin capability required
- Agency license validation

#### Deploying Packs to Client Sites

**Step 1: Install Plugin on Client Site**
1. Install Submittal & Spec Sheet Builder plugin
2. Activate Agency license on client site
3. Ensure `sfb_is_agency_license()` returns true

**Step 2: Import Pack JSON**
1. Upload JSON file to client site (via FTP or file manager)
2. Note the Pack ID from JSON file
3. Or: Add Pack to `sfb_agency_packs` option via import tool

**Step 3: Load Sample Catalog**
1. Go to: **Submittal Builder** admin page
2. Click **Load Sample Catalog** button
3. Select **"From Agency Pack"** tab in modal
4. Choose Pack from dropdown (shows all imported Packs)
5. Select mode:
   - **Replace** - Wipes existing catalog and imports Pack
   - **Merge** - Adds Pack to existing catalog
6. Toggle **Apply Branding** (if Pack includes branding)
7. Click **Load Catalog**
8. Wait for import to complete (usually 5-15 seconds)

**What Happens During Import:**
- Old node IDs are remapped to avoid conflicts
- Parent relationships are preserved
- Branding settings are applied (if enabled)
- Product positions are maintained
- All specifications are copied

**Result:**
- Client site has complete, ready-to-use catalog
- All products immediately available for selection
- Branding matches Pack settings (if included)
- Ready to generate PDFs immediately

#### Managing Packs

**Renaming Packs:**
- Currently not directly supported in UI
- Must delete and recreate with new name
- Or edit via WordPress options table (advanced users)

**Deleting Packs:**
1. Go to: **Agency Library**
2. Find Pack to delete
3. Click **Delete** button
4. Confirm deletion
5. Pack removed from database immediately

**Caution:**
- Deletion is permanent (no trash/undo)
- Does NOT affect sites where Pack was already deployed
- Only removes Pack from current site's library

#### Best Practices

**Pack Naming:**
- Use descriptive names: "Electrical - Full Catalog"
- Include version numbers: "HVAC Pack v2.3"
- Date for tracking: "Plumbing 2025-Q1"
- Client-specific: "ACME Corp Standard Catalog"

**Branding Strategy:**
- **Include Branding** - For white-label deployments with consistent brand
- **Exclude Branding** - When clients will provide their own branding
- Test both scenarios before mass deployment

**Notes Strategy:**
- **Include Notes** - For internal teams who need product context
- **Exclude Notes** - For client-facing sites to keep catalogs lean
- Notes can add significant file size

**Version Control:**
- Keep JSON files in version control (Git)
- Tag releases: `pack-hvac-v1.0.json`
- Document changes in commit messages
- Maintain changelog for Pack updates

**Testing Before Deployment:**
1. Create Pack on staging site
2. Export JSON
3. Import to test site
4. Verify all products loaded correctly
5. Generate test PDF to confirm branding
6. Deploy to production only after validation

---

### Agency Pack Workflow Example

**Scenario:** Deploy standard HVAC catalog to 5 client sites

**One-Time Setup:**
1. Build master HVAC catalog on agency site
2. Add all 150 products with specifications
3. Configure sample branding (optional)
4. Save as Pack: "HVAC Master Catalog v1"
5. Export JSON file
6. Store in secure location (cloud storage or Git)

**For Each Client Site:**
1. Install plugin + activate Agency license (2 minutes)
2. Upload Pack JSON to site (1 minute)
3. Load Pack via "Load Sample Catalog" (30 seconds)
4. Customize client branding (5 minutes)
5. Test PDF generation (2 minutes)
6. **Total Time:** ~10 minutes per site vs hours of manual entry

**Maintenance:**
1. Update master catalog on agency site
2. Add new products, update specs
3. Save as Pack: "HVAC Master Catalog v2"
4. Export JSON
5. Send to clients for reimport
6. Clients can merge (add new products) or replace (full update)

---

### Advanced Lead Routing

**Note:** Agency feature requiring active Agency license.

**Location:** WordPress Admin → **Submittal Builder** → **Settings** → **Lead Routing**

Create rules that automatically route new leads based on specific conditions. Each rule can match by email domain, UTM parameters, or the lead's top category. When a rule matches, the lead is sent to designated recipients via email and/or webhook.

#### Rule Conditions

Rules use **OR logic** within a single rule, meaning if any condition matches, the rule triggers:

**Email Domain Contains:**
- Match leads by email domain
- Example: `acme.com` matches `john@acme.com` or `jane@corp.acme.com`
- Case-insensitive matching
- Perfect for routing by company or organization

**UTM Source/Medium/Campaign:**
- Match leads by UTM parameters
- Examples: `google`, `facebook`, `summer-promo`
- Tracks lead source for campaign-specific routing
- Any UTM field can be used independently

**Top Category Equals:**
- Match leads by their primary product category
- Exact match only (e.g., "C-Studs" matches "C-Studs" but not "c-studs")
- Useful for routing by product line or department

#### Rule Actions

**Send to Email:**
- Comma-separated list of recipients
- Example: `sales@company.com, john@company.com`
- Standard lead notification email sent to all recipients

**Send to Webhook:**
- POST request to external URL (Zapier, Make, custom endpoint)
- Full lead data sent as JSON payload
- Includes retry logic (see below)

#### Retry Logic & De-duplication

**Webhook Retries:**
- **Attempt 1:** Immediate on lead capture
- **Attempt 2:** ~30 seconds after first failure
- **Attempt 3:** ~2 minutes after second failure
- **Attempt 4:** ~10 minutes after third failure
- Exponential backoff prevents server overload

**De-duplication:**
- Each lead ID tracked to prevent duplicate webhook deliveries
- Retries use same lead ID
- Activity log shows all attempts

**Activity Log:**
- Location: Settings → Lead Routing → Activity Log
- Shows last 50 routing events
- Includes: timestamp, rule matched, delivery status, retry attempts
- Clear log button (admin only)

#### Fallback Rule

**What It Does:**
- Catches leads that don't match any rule
- Optional (can be left empty)
- Uses same email/webhook actions as rules

**When to Use:**
- Ensure no leads are missed
- Send unmatched leads to general inbox
- Log all leads to central webhook

#### Rule Priority

**First-Match Wins:**
- Rules evaluated in display order (top to bottom)
- Once a rule matches, evaluation stops
- Fallback only used if no rule matches
- Drag to reorder rules (if UI supports)

**Best Practice:**
- Place most specific rules first
- Place broad rules last
- Test rules with "Test Rule" button

#### Testing Rules

**Test Rule Button:**
- Simulates a lead without creating actual lead
- Enter test email, UTM params, category
- Shows which rule would match
- Displays email/webhook that would be triggered
- No actual emails/webhooks sent during test

---

### Default Brand Preset → PDFs

**Note:** Agency feature requiring active Agency license.

**Location:** WordPress Admin → **Submittal Builder** → **Settings** → **Branding** → "Use default preset automatically"

Automatically apply your Default Brand Preset to the Review page and all new PDFs. When enabled, users see the default branding without manual selection.

#### How It Works

**Toggle ON + Default Preset Set:**
- Review page displays with default preset applied
- All generated PDFs use default preset branding
- Session-only changes still possible (see Review Preset Switcher)
- Current branding settings ignored for PDFs

**Toggle ON but No Default Set:**
- Falls back to current branding settings
- Warning shown in admin
- Set a default preset in Brand Presets section

**Toggle OFF:**
- Uses current branding settings only
- Presets available for manual selection
- Original behavior restored

#### Use Cases

**Consistent Branding:**
- Ensure all PDFs match company standards
- Prevent accidental branding errors
- Perfect for agencies with strict brand guidelines

**Multi-User Teams:**
- Junior staff can't accidentally break branding
- Default preset maintained by admin
- Individual users don't need branding access

**Client Handoff:**
- Set default preset before handoff
- Client gets consistent branded PDFs
- No training needed on branding system

---

### Review Preset Switcher (Preview-only)

**Note:** Agency feature visible on Review step.

**Location:** Review step (Step 2) → Brand Preset dropdown (if 2+ presets exist)

Quickly preview different Brand Presets on the Review page. Changes are **session-only** and don't affect saved settings or generated PDFs.

#### How It Works

**Selecting a Preset:**
- Click preset dropdown on Review page
- Choose any saved Brand Preset
- Preview updates instantly (logo, colors, company info)
- Changes stored in `sessionStorage` only

**Session-Only Behavior:**
- Preview changes **do not persist** after:
  - Closing browser tab
  - Refreshing page
  - Generating PDF (unless preset is default)
  - Returning to Step 1
- No database writes occur

**Generated PDFs:**
- If "Default-to-PDF" toggle is ON: Uses default preset
- If toggle is OFF: Uses current saved branding settings
- Session preview does NOT affect PDF output

#### Apply as Default

**"Apply as default" Link:**
- Click to open Branding settings page
- Selected preset loaded into form
- Click "Save Changes" to persist
- Returns to Builder after save

**When to Use:**
- You like the preview and want to keep it
- Need to update current branding to match preview
- Want to set a new default preset

#### Use Cases

**Client Presentations:**
- Preview multiple brand options for client
- Show different color schemes or logos
- No commitment until "Apply as default"

**A/B Testing:**
- Compare preset variations
- See which branding looks best
- Quick visual comparison

**Temporary Changes:**
- Preview one-off branding for special project
- Doesn't affect other users or PDFs
- Clean slate on page refresh

---

### White-Label Mode

**Note:** Agency feature requiring active Agency license.

**Location:** WordPress Admin → **Submittal Builder** → **Settings** → **White-Label**

Remove or customize plugin credits, set custom PDF footers, and customize email From name/address. Ideal for agencies who want to present the plugin as their own solution.

#### Credit Modes

**Three Modes Available:**

**1. Full Credit (Default):**
- Plugin credits appear in admin footer
- PDF footer includes "Generated with Submittal & Spec Sheet Builder"
- Email footer includes plugin name
- No changes to default behavior

**2. Subtle Credit:**
- Admin footer unchanged
- PDF footer includes small "Powered by …" link
- Email footer includes small "Powered by …" line
- User-facing only (not admin)

**3. No Credit:**
- All plugin credits removed
- PDF footer custom text only
- Email footer custom text only
- Complete white-label

#### Custom Footer Text

**PDF Footer:**
- Replaces default footer text
- Example: "© 2025 Your Company. All rights reserved."
- Supports line breaks
- Max 200 characters

**Where It Appears:**
- Bottom of every PDF page
- Does not replace page numbers (separate setting)
- Rendered in small gray text

#### Email Customization

**From Name:**
- Default: WordPress site name
- Custom: Your company name
- Example: "ACME Corp Engineering"
- Appears in recipient's inbox

**From Address:**
- Default: `wordpress@yourdomain.com`
- Custom: Your company email
- Example: `noreply@acmecorp.com`
- **Must be valid email address**
- **Must match server SPF/DKIM** (or emails may fail)

**Reply-To Address:**
- Optional
- Where replies are sent
- Example: `support@acmecorp.com`

#### White-Label Checklist

**Before Enabling:**
1. Verify Agency license active
2. Test custom email settings (send test lead)
3. Confirm SPF/DKIM records (to prevent spam folder)
4. Generate test PDF to verify footer
5. Check email delivery to inbox (not spam)

**After Enabling:**
1. Generate sample PDF and verify footer
2. Submit test lead and check email From/footer
3. Review admin pages for any missed credits
4. Test on client site before handoff

---

### Client Handoff Mode

**Note:** Agency feature requiring active Agency license.

**Location:** WordPress Admin → **Submittal Builder** → **Agency** → **Client Handoff Mode**

Temporarily hide agency-specific features from your WordPress admin to create a "client-safe" view. Perfect for when you're giving clients access to use the builder without exposing your internal agency tools and templates.

#### What is Client Handoff Mode?

Client Handoff Mode is a **one-click toggle** that hides agency-specific features from the WordPress admin interface. Think of it as a "presentation mode" for your plugin — you can safely let clients use the builder, view settings, and generate PDFs without them seeing or accidentally modifying your agency's internal resources.

**Key Point:** This is a **temporary visibility toggle only**. No data is deleted or modified. When you turn it off, everything instantly returns to normal.

#### What Gets Hidden When Enabled

When Client Handoff Mode is active, the following agency features are hidden from view:

**1. Agency Library (Agency Packs)**
- The entire "Agency Library" admin page is hidden
- Your saved Packs remain safe in the database
- "Save as Pack" button disappears from toolbar
- Clients cannot see, export, or delete your Packs

**2. Brand Presets Management**
- Brand Presets management section hidden on Branding settings page
- The active/default preset still works normally
- Clients can still use the builder with your default branding
- Preset dropdown on Review page still visible (for preview only)

**3. Demo Tools & Seeder**
- Internal development tools hidden
- Prevents accidental catalog resets
- Protects against data loss

#### What Clients CAN Still Access

Client Handoff Mode only hides agency-specific tools. Clients retain full access to:

✅ **Product Builder** - Full access to select products and generate PDFs
✅ **Settings Page** - View and modify general settings
✅ **Branding Page** - View current branding (but not manage presets)
✅ **Tracking & Analytics** - View usage data and metrics
✅ **Leads Data** - Access lead capture information
✅ **PDF Generation** - Generate and download submittal packets

#### How to Enable/Disable

**Enabling Client Handoff Mode:**
1. Go to: **Submittal Builder → Agency**
2. Scroll to: **Client Handoff Mode** section
3. Check: **"Enable Client Handoff Mode"**
4. Click: **"Save Changes"**
5. Result: Agency features instantly hidden from admin

**Disabling Client Handoff Mode:**
1. Look for the blue banner at the top of any plugin page
2. Banner says: **"🤝 Client Handoff Mode Active"**
3. Click: **"Return to Agency Mode"** button
4. Or: Go to Agency page and uncheck the toggle
5. Result: All agency features instantly reappear

#### The Client Handoff Banner

When Client Handoff Mode is enabled, you'll see a blue informational banner at the top of all plugin pages:

**Banner Text:**
> 🤝 **Client Handoff Mode Active**
> Agency-specific features are currently hidden. All data is safe and can be restored instantly.
>
> [Return to Agency Mode] ← Click to turn off

**Why the Banner?**
- Reminds you that handoff mode is active
- Prevents confusion ("where did my Agency Library go?")
- Provides quick access to turn it back off
- Only visible on plugin pages (not elsewhere in WordPress)

#### Use Cases

**1. Client Site Handoff**
- You've set up a client's catalog and branding
- Client needs admin access to generate PDFs
- Enable Client Handoff Mode before giving them login
- They see a clean, simple interface without agency tools

**2. Multi-User Teams**
- Junior staff or contractors need builder access
- Don't want them accidentally modifying Agency Packs
- Enable handoff mode for their user role
- They can use the builder but can't access agency resources

**3. Client Training Sessions**
- Walking a client through the plugin
- Don't want to confuse them with agency features
- Enable handoff mode during screen share
- Simpler interface = easier training

**4. Temporary Client Access**
- Client needs to generate PDFs for a project
- Give them temporary admin access
- Enable handoff mode to protect your agency assets
- Disable when project is complete

#### Important Notes

**Data Safety:**
- **Nothing is deleted** - All Agency Packs, Brand Presets, and settings remain in the database
- **Instant restoration** - Turn off handoff mode and everything reappears immediately
- **No user permissions changed** - This only affects visibility, not capabilities

**What It Does NOT Do:**
- ❌ Does not create separate user roles
- ❌ Does not modify database content
- ❌ Does not affect frontend builder functionality
- ❌ Does not change branding or PDF output
- ❌ Does not hide the "Agency" settings page itself (so you can turn it back off)

**Best Practice:**
- Set up a default Brand Preset before enabling (see "Default Brand Preset → PDFs")
- Test PDF generation while handoff mode is active
- Document which features are hidden for client onboarding
- Use with White-Label Mode for complete client-facing experience

#### Combining with Other Agency Features

**Client Handoff + White-Label Mode:**
- Hide agency tools (Client Handoff)
- Remove plugin branding (White-Label)
- Result: Completely client-branded experience

**Client Handoff + Default Preset:**
- Hide preset management (Client Handoff)
- Auto-apply default branding (Default Preset toggle)
- Result: Clients get consistent PDFs without seeing branding controls

**Client Handoff + Operator Role:**
- Hide agency tools (Client Handoff)
- Limit user capabilities (Operator role)
- Result: Locked-down, client-safe environment

---

### Weekly Lead Export → "Send Now"

**Note:** Agency feature requiring active Agency license.

**Location:** WordPress Admin → **Submittal Builder** → **Settings** → **Lead Export** → "Send Now" button

Automatically export all new leads weekly via email CSV. Or, trigger an immediate export with the "Send Now" button.

#### How It Works

**Weekly Schedule:**
- Runs every Monday at 9:00 AM (site timezone)
- Gathers all leads from previous 7 days
- Generates CSV file with lead data
- Sends email with CSV attachment

**Send Now Button:**
- Location: Settings → Lead Export section
- Triggers immediate export (bypasses schedule)
- Includes all leads from past 7 days
- Same CSV format as scheduled export
- Email sent to configured recipients

#### CSV Format

**Columns Included:**
- Lead ID
- Email address
- Date submitted
- Project name (if provided)
- Product count
- Top category
- UTM source/medium/campaign (if present)
- Custom fields (if any)

**File Format:**
- Standard CSV (comma-separated values)
- UTF-8 encoding
- Header row included
- Filename: `leads-export-YYYY-MM-DD.csv`

#### Configuration

**Recipients:**
- Comma-separated email list
- Example: `manager@company.com, sales@company.com`
- Validation on save

**Enable/Disable:**
- Toggle to turn weekly export on/off
- "Send Now" always available (even if disabled)
- Default: disabled

#### Use Cases

**Sales Team Reports:**
- Weekly lead summary for team meetings
- Import into CRM or spreadsheet
- Track lead volume over time

**On-Demand Exports:**
- Need leads immediately for urgent proposal
- Monthly reporting (click "Send Now" on last Monday)
- Quarter-end summaries

**Backup & Compliance:**
- Regular lead backup to email
- Audit trail for compliance
- Redundant storage outside WordPress

---

### Agency License Requirements

**To Access Agency Features:**
- Active Agency license key
- License must be activated on current site
- Or: `SFB_AGENCY_DEV` constant set to `true` (development)

**Feature Visibility:**
- **Agency Library** menu only visible to Agency license holders
- **Save as Pack** button only appears for Agency users
- Export/Delete actions require Agency validation
- Seeder accepts `agency_pack_id` only with Agency license

**License Validation:**
- Checked via `sfb_is_agency_license()` function
- Validates against license server
- Falls back to dev constant if enabled
- Failed validation: 403 Forbidden error

**Upgrading to Agency:**
- Contact sales for Agency license key
- Activate key in **License & Support** page
- Features unlock immediately upon activation
- No plugin reinstall required

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

**Q: Can I bulk import products?**
A: Products are added individually through the admin interface. For large catalogs, contact support about programmatic import options.

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

© 2025 WebStuffGuy Labs. All rights reserved.

