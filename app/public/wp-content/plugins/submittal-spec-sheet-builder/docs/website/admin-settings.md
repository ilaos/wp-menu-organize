# Admin Settings

[← Back to Documentation](./index.md)

**Complete guide to configuring Submittal & Spec Sheet Builder**

---

## Overview

The Settings page allows administrators to configure all aspects of the plugin including branding, PDF generation, frontend behavior, and Pro features.

**Access Settings:**
```
WordPress Admin → Submittal Builder → Settings
```

**Required Capability:** `manage_options` (Administrator role)

---

## Settings Navigation

Settings are organized into tabs for easy navigation:

| Tab | Purpose |
|-----|---------|
| **General** | Core plugin settings |
| **Branding** | Logo, colors, company info |
| **Frontend** | Builder page and user interface |
| **PDF** | PDF generation and formatting |
| **Pro** | License and Pro feature configuration |
| **Advanced** | Developer options and debugging |

---

## General Settings

Core configuration options that affect overall plugin behavior.

### Plugin Status

**Enable/Disable Plugin**

Temporarily disable the plugin without deactivating it.

- **Enabled** (default): Plugin fully functional
- **Disabled**: Builder page shows maintenance message

**Use Case:**
- Maintenance periods
- Catalog updates
- Temporary downtime

---

### Catalog Settings

**Default View Mode**

Choose how products display by default on the frontend builder.

**Options:**
- **Gallery View** (default) - Cards with thumbnails
- **List View** - Compact table format

**Tip:** Users can still toggle between views; this just sets the default.

---

**Products Per Page**

Number of products to display before pagination.

**Default:** 50
**Range:** 10-200
**Recommended:** 50-100 for best performance

**Considerations:**
- **Lower numbers:** Faster page load, more pagination
- **Higher numbers:** Fewer page loads, slower initial render
- **Large catalogs (500+ products):** Use 50-75

---

**Enable Search**

Allow users to search products on frontend builder.

**Enabled by default** (recommended)

**When to Disable:**
- Small catalogs (< 20 products)
- Custom search implementation
- Performance issues on very large catalogs

---

**Enable Category Filters**

Allow users to filter products by category.

**Enabled by default** (recommended)

**When to Disable:**
- Single-category catalogs
- Custom filtering implementation

---

### PDF Storage

**PDF Retention Period**

How long generated PDFs are stored before automatic cleanup.

**Default:** 24 hours
**Range:** 1 hour - 7 days

**Considerations:**
- **Shorter periods:** Less disk space used, users must download quickly
- **Longer periods:** More user-friendly, more disk space used
- **Recommended:** 24-48 hours

**Note:** PDFs are stored in `/wp-content/uploads/submittal-builder/` and cleaned up automatically via cron job.

---

## Branding Settings

Customize the appearance of generated PDFs to match your brand.

### Logo

**Upload Company Logo**

Your logo appears on the PDF cover page.

**Recommendations:**
- **Format:** PNG with transparent background (preferred) or JPG
- **Size:** 300×100 pixels (optimal)
- **Max file size:** 2 MB
- **Aspect ratio:** ~3:1 (horizontal)

**Upload Steps:**
1. Click "Upload Logo" button
2. Select image from media library or upload new
3. Crop if needed
4. Click "Select"
5. Save settings

**Preview:**
Logo preview appears below upload button showing how it will look in PDFs.

**To Remove Logo:**
Click "Remove Logo" button below preview.

---

### Brand Colors

**Primary Brand Color**

Main color used throughout PDFs (cover page, headings, accents).

**Input Methods:**
- Color picker (visual selection)
- Hex code (e.g., `#0066CC`)
- RGB values

**Recommendations:**
- Use your company's primary brand color
- Ensure good contrast with white text
- Avoid very light colors (hard to read)

**Default:** `#0073aa` (WordPress blue)

**Preview:**
Color preview box shows selected color immediately.

---

**Secondary Color (Pro)**

Accent color for tables, borders, and secondary elements.

**Default:** Darker shade of primary color
**Available in:** Pro and Agency licenses

---

### Company Information

Information displayed on PDF cover page and footer.

**Company Name** (required)

Your company's legal or trade name.

**Example:** "Acme Steel & Supply Co."

**Appears:**
- PDF cover page (large)
- PDF footer (small)
- Email signatures (Pro)

---

**Contact Information**

Optional fields for contact details:

- **Phone:** (555) 123-4567
- **Email:** info@company.com
- **Website:** https://company.com
- **Address:** 123 Main St, City, ST 12345

**Appears:**
- PDF cover page
- Contact section
- Footer (abbreviated)

---

**Footer Text**

Custom text displayed at bottom of every PDF page.

**Default:** "{Company Name} | {Website} | Page {X} of {Y}"

**Variables Available:**
- `{company}` - Company name
- `{website}` - Website URL
- `{phone}` - Phone number
- `{page}` - Current page number
- `{total}` - Total pages
- `{date}` - Generation date

**Example:**
```
Acme Steel & Supply | www.acme.com | Page {page} of {total}
```

---

## Frontend Settings

Configure the builder page and user-facing interface.

### Builder Page

**Builder Page**

Select which page contains the `[submittal_builder]` shortcode.

**Options:**
- Dropdown showing all published pages
- "Auto-Create Page" button

**Auto-Create Page:**
1. Click "Auto-Create Builder Page" button
2. Plugin creates new page titled "Spec Sheet Builder"
3. Adds shortcode automatically
4. Publishes page
5. Sets as builder page in settings

**Manual Setup:**
1. Create page yourself
2. Add `[submittal_builder]` shortcode
3. Publish page
4. Select from dropdown in settings

---

### User Interface

**Show Product Thumbnails**

Display product images/thumbnails in cards (if available).

**Enabled by default**

**Requirements:**
- Products must have featured images set
- Thumbnails generated automatically

**When to Disable:**
- No product images available
- Faster page load desired
- Bandwidth concerns

---

**Enable View Toggle**

Allow users to switch between gallery and list views.

**Enabled by default** (recommended)

**When to Disable:**
- Force specific view mode
- Simplified interface desired

---

**Show Specifications in Cards**

Display key specs directly on product cards in gallery view.

**Enabled by default** (recommended)

**Specs Shown:** First 4-5 key specifications (Size, Thickness, KSI, etc.)

**When to Disable:**
- Cleaner card appearance desired
- Specs not critical for selection

---

### Lead Capture (Pro)

**Enable Lead Capture**

Show form before PDF download to collect user information.

**Available in:** Pro and Agency licenses

**Fields Collected:**
- Name
- Email (required)
- Company
- Phone
- Project name

**Use Cases:**
- Lead generation
- Customer tracking
- Email marketing list building
- Project documentation

**Privacy:**
- Complies with GDPR/CCPA (users opt-in)
- Data stored locally in WordPress
- Exportable as CSV

**To Configure:**
1. Enable checkbox
2. Choose required vs optional fields
3. Set custom success message
4. Configure auto-email (optional)

---

## PDF Settings

Configure PDF generation, formatting, and appearance.

### PDF Generation

**PDF Engine**

Choose which library generates PDFs.

**Options:**
- **DomPDF** (default) - Faster, good compatibility
- **TCPDF** - More features, slower

**Recommendation:** Use DomPDF unless you need specific TCPDF features.

---

**Page Size**

Paper size for generated PDFs.

**Options:**
- **Letter** (8.5" × 11") - US standard, default
- **A4** (210mm × 297mm) - International standard
- **Legal** (8.5" × 14") - US legal documents

**Choose based on:**
- Geographic region
- Industry standards
- Client requirements

---

**Page Orientation**

Layout orientation for PDF pages.

**Options:**
- **Portrait** (default) - Vertical, standard for most documents
- **Landscape** - Horizontal, better for wide tables

**Recommendation:** Portrait for most use cases.

---

### PDF Content

**Include Cover Page**

Generate branded cover page with logo, project name, and date.

**Enabled by default** (recommended)

**Cover Page Includes:**
- Logo
- Project name
- Date generated
- Company information
- Brand colors

**When to Disable:**
- Minimal PDFs desired
- Cover page created separately

---

**Include Table of Contents**

Generate clickable table of contents with page numbers.

**Enabled by default** (recommended)

**TOC Features:**
- Lists all products
- Page numbers
- Clickable navigation (in PDF viewers)

**When to Disable:**
- Small packets (< 5 products)
- Minimal formatting desired

---

**Include Summary Table**

Generate overview table with all products and key specs.

**Enabled by default** (recommended)

**Summary Table Shows:**
- Product model numbers
- Categories and types
- Key specifications (Size, Thickness, KSI)
- Compact reference on single page

**Use Case:**
- Quick reference for architects
- Bid comparison
- Project planning

---

**Spec Sheet Format**

How individual product spec sheets are formatted.

**Options:**
- **Full Page** (default) - One product per page, all specs visible
- **Compact** - Multiple products per page, condensed format

**Recommendation:** Full page for compliance submittals.

---

### PDF Themes (Pro)

**Select PDF Theme**

Choose from three professionally designed color themes that apply across your entire PDF.

**Available in:** Pro and Agency licenses

**Themes Available:**

| Theme | Color | Best For |
|-------|-------|----------|
| **Engineering** (Default) | Dark Gray (#111827) | Technical submittals, construction |
| **Architectural** | Sky Blue (#0ea5e9) | Architecture, design, modern aesthetic |
| **Corporate** | Emerald Green (#10b981) | Corporate, sustainability, healthcare |

**What Changes:**
- Cover page accent colors
- Section header colors
- Table header backgrounds
- Border colors
- Overall color scheme

**To Apply:**
1. Navigate to Settings → Branding
2. Find "PDF Theme" dropdown (Pro only)
3. Select: Engineering, Architectural, or Corporate
4. Click "Save Changes"
5. Generate a new PDF to see theme applied

**Note:** Your logo and brand colors are preserved; only accent colors change.

---

### PDF Watermark (Pro)

**Add Custom Watermark**

Add a custom text watermark that appears diagonally across all pages.

**Available in:** Pro and Agency licenses

**Visual Style:**
- Diagonal orientation (-20° rotation)
- Semi-transparent (6% opacity)
- Large, prominent text (64px)
- Appears on ALL pages

**How to Enable:**
1. Navigate to Settings → Branding
2. Find "PDF Watermark" field (Pro only)
3. Enter custom text (e.g., "DRAFT", "CONFIDENTIAL")
4. Leave blank to disable watermark
5. Click "Save Changes"

**Common Uses:**
- `DRAFT` - Work in progress
- `CONFIDENTIAL` - Sensitive information
- `FOR REVIEW ONLY` - Review copies
- `PRELIMINARY` - Not finalized
- `SAMPLE` - Demo materials

**Best Practices:**
- Keep text short (under 20 characters)
- Use all caps for visibility
- Test opacity on printed pages

---

### Approval Signature Block (Pro)

**Include Signature Block**

Add a professional 3-column approval table at the end of each product sheet.

**Available in:** Pro and Agency licenses

**Note:** This option is configured during PDF generation on the Frontend Builder review page, not in Settings.

**Visual Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ APPROVED BY       │ TITLE              │ DATE           │
│ ───────────────   │ ─────────────────  │ ──────────     │
│ [Name]            │ [Job Title]        │ [Date]         │
└─────────────────────────────────────────────────────────┘
```

**How to Enable:**
1. Navigate to Frontend Builder or Review page
2. Check "Include Approval Signature Block" (Pro only)
3. Fill in fields:
   - Approved By: Name of approver
   - Title: Job title or role
   - Date: Approval date
4. Generate PDF

**Features:**
- Appears on EACH product page
- Professional table layout
- Print-friendly signature lines
- Page-break protection

**Use Cases:**
- Construction submittals requiring approval
- Compliance documentation
- Quality assurance sign-offs
- Project manager approvals
- AHJ (Authority Having Jurisdiction) approval

---

### White-Label Mode (Agency)

**Enable White-Label**

Remove all plugin branding from PDFs and admin interface.

**Available in:** Agency license only

**Removes:**
- "Generated by Submittal Builder" footer text
- Plugin branding in admin pages
- Plugin name in PDF metadata

**Use Case:**
- Agencies reselling to clients
- White-label solutions
- Complete brand control

---

## Pro Settings

Configure license and Pro features.

### License Activation

**License Key**

Enter your license key to unlock Pro features.

**Steps:**
1. Copy license key from purchase email or account
2. Paste into "License Key" field
3. Click "Activate License" button
4. Wait for verification (2-5 seconds)
5. Status updates to "Active"

**License Status:**

- **🟢 Active** - License valid, Pro features enabled
- **🟡 Expired** - License expired, renew to restore features
- **🔴 Invalid** - License key incorrect or revoked

**Troubleshooting:**
- Verify key is correct (copy/paste carefully)
- Check site URL matches activated domain
- Contact support if issues persist

---

**License Type**

Displays your current license tier:

- **Free** - Core features only
- **Pro Single Site** - Pro features on 1 site
- **Pro Agency** - Pro features on up to 5 sites

**Activated On:**
Shows site URL where license is activated.

**Deactivate License:**
Click "Deactivate" to free up activation for use on different site.

---

### Pro Features

**Server-Side Drafts**

Save product selections to server for later retrieval.

**Enabled by default** when Pro license active

**Use Case:**
- Users can save progress
- Resume later
- Share draft URLs with team

---

**PDF Tracking**

Track when recipients view generated PDFs.

**Enabled by default** when Pro license active

**Features:**
- View count per PDF
- Timestamps
- Recipient information
- Analytics dashboard

See [PDF Tracking Guide](./tracking.md) for details.

---

**Auto Email Packets**

Automatically email PDFs to users after generation.

**Configuration:**
- **Enable checkbox** - Turn feature on/off
- **From Name** - Sender name (default: site name)
- **From Email** - Sender email (default: admin email)
- **Subject Line** - Email subject (supports variables)
- **Message** - Email body (supports HTML)

**Variables Available:**
- `{project}` - Project name
- `{company}` - Company name
- `{date}` - Generation date

**Example Subject:**
```
Your Submittal Packet for {project}
```

---

**Lead Routing (Agency)**

Automatically forward leads to external CRM or email.

**Available in:** Agency license only

**Options:**
- **Email Forwarding** - Send to specific email addresses
- **Webhook** - POST to external URL (Zapier, Make, custom API)

**Webhook Configuration:**
- URL endpoint
- Authentication header (optional)
- Custom payload format

---

## Advanced Settings

Developer and debugging options.

### Debug Mode

**Enable Debug Logging**

Log detailed plugin activity for troubleshooting.

**Disabled by default**

**When Enabled:**
- Logs stored in `/wp-content/debug.log`
- Includes PDF generation details
- REST API requests/responses
- AJAX handler activity

**When to Enable:**
- Troubleshooting errors
- Support requests
- Development/testing

**Important:** Disable after debugging (log files can grow large).

---

### Performance

**Enable Caching**

Cache product data for faster frontend loading.

**Enabled by default** (recommended)

**Cache Duration:** 1 hour (adjustable)

**When to Disable:**
- Frequent product updates
- Real-time data requirements
- Cache conflicts with other plugins

---

**Optimize Images**

Automatically compress/resize product images.

**Enabled by default** (recommended)

**Optimizations:**
- Resize large images to max 800px width
- Compress to reduce file size
- Faster page loads

**When to Disable:**
- Images already optimized
- High-quality images required

---

### REST API

**Enable REST API**

Allow external applications to interact with plugin via REST API.

**Enabled by default**

**When to Disable:**
- Security concerns
- No external integrations needed

**Documentation:** See [API Reference](../../API-REFERENCE.md) for endpoints and usage.

---

### Cleanup

**Automatic Cleanup**

Automatically delete old PDFs and temporary files.

**Enabled by default** (recommended)

**Runs:** Daily via WordPress cron

**Cleans Up:**
- PDFs older than retention period
- Temporary files
- Expired draft data (Pro)

**Manual Cleanup:**
Click "Run Cleanup Now" button to immediately clean files.

---

## Saving Settings

**Important:** Always click "Save Changes" button at bottom of settings page after making changes.

**Save Indicators:**
- Success message appears
- Settings saved to database
- Changes take effect immediately

**If Changes Don't Appear:**
1. Refresh browser page
2. Clear browser cache
3. Check for JavaScript errors in console
4. Verify user has `manage_options` capability

---

## Importing/Exporting Settings

**Export Settings** (Agency)

Export all plugin settings to JSON file for backup or transfer.

**Use Case:**
- Backup before changes
- Transfer settings to another site
- Share configuration with team

**Steps:**
1. Click "Export Settings" button
2. JSON file downloads
3. Save securely

---

**Import Settings** (Agency)

Import previously exported settings.

**Steps:**
1. Click "Import Settings" button
2. Select JSON file
3. Confirm import
4. Settings applied

**Warning:** Importing overwrites current settings. Export first as backup.

---

## Troubleshooting Settings

### Changes not saving

**Cause:** Browser cache or plugin conflict.

**Solution:**
1. Clear browser cache
2. Try different browser
3. Deactivate other plugins temporarily
4. Check browser console for JavaScript errors

---

### Settings page blank

**Cause:** PHP error or theme conflict.

**Solution:**
1. Enable WordPress debug mode
2. Check error logs
3. Switch to default theme temporarily
4. Contact support with error details

---

### License key won't activate

**Cause:** Key invalid, site URL mismatch, or connection issue.

**Solution:**
1. Copy key carefully (no extra spaces)
2. Verify site URL matches purchase
3. Check internet connection
4. Contact support if issue persists

---

