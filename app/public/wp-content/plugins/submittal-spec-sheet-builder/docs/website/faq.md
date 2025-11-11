# Frequently Asked Questions

[← Back to Documentation](./index.md)

**Quick answers to common questions**

---

## General Questions

### What is Submittal & Spec Sheet Builder?

A WordPress plugin that allows manufacturers, distributors, and contractors to create professional submittal packets and specification sheets. Users select products from your catalog and generate branded PDF packets instantly.

**Key Features:**
- Product catalog management
- Interactive frontend builder
- PDF generation with branding
- Lead capture (Pro)
- PDF tracking (Pro)

---

### Who is this plugin for?

**Primary Users:**
- Manufacturers (steel, HVAC, electrical, plumbing, etc.)
- Distributors and suppliers
- General contractors
- Engineers and architects
- Sales teams

**Use Cases:**
- Creating submittal packets for construction projects
- Generating specification sheets for bids
- Building product documentation for compliance
- Sales presentations and quotes

---

### How much does it cost?

**Free Version:**
- Core features
- Unlimited products
- PDF generation
- Basic branding

**Pro Single Site: $XX/year**
- All free features
- Lead capture
- PDF tracking
- Auto email
- Server drafts
- Premium support

**Pro Agency: $XXX/year**
- All Pro features
- Up to 5 sites
- White-label mode
- Custom themes
- Priority support

**Pricing:** Visit https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/

---

### Do I need coding skills?

**No.** The plugin is designed for non-technical users:
- Visual catalog builder
- Point-and-click interface
- No code required for basic use
- Optional advanced customization for developers

---

### Is it compatible with my theme?

**Yes.** The plugin works with any properly coded WordPress theme, including:
- Default WordPress themes
- Popular themes (Astra, Divi, Avada, etc.)
- Page builders (Elementor, Beaver Builder, etc.)
- Custom themes

If you experience theme conflicts, see [Troubleshooting](./troubleshooting.md).

---

### Does it work with page builders?

**Yes.** The `[submittal_builder]` shortcode works with:
- Gutenberg (WordPress Block Editor)
- Elementor
- Beaver Builder
- Divi Builder
- WPBakery (Visual Composer)
- Oxygen Builder
- Bricks Builder

Simply add the shortcode block and insert `[submittal_builder]`.

---

## Technical Questions

### What are the server requirements?

**Minimum:**
- WordPress 6.1+
- PHP 7.4+
- MySQL 5.6+
- 128 MB memory

**Recommended:**
- WordPress 6.5+ (latest)
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- 256 MB+ memory

**Required PHP Extensions:**
- mbstring
- gd or imagick
- json
- dom
- libxml

See [Installation Guide](./installation.md#server-requirements) for details.

---

### How many products can I add?

**No hard limit.** The plugin can handle:
- **Small catalogs:** 10-50 products (no issues)
- **Medium catalogs:** 50-500 products (recommended server: 256MB+ RAM)
- **Large catalogs:** 500-5,000 products (recommended: VPS or dedicated hosting, 512MB+ RAM)
- **Very large catalogs:** 5,000+ products (requires optimization, managed hosting)

**Performance Tips:**
- Use caching for large catalogs
- Optimize product images
- Use quality hosting (VPS recommended for 500+ products)

---

### What file formats do PDFs support?

**Generated PDFs:**
- Format: PDF 1.7
- Page size: Letter (8.5" × 11") or A4
- Orientation: Portrait or landscape
- Compatibility: All major PDF readers (Adobe Reader, browser viewers, etc.)

**Features:**
- Clickable table of contents
- Page numbers
- Embedded fonts
- Optimized for printing
- Email-friendly file size (~50-150 KB per product page)

---

### Can I import products from CSV or Excel?

**Coming Soon.** Bulk import feature is planned for a future update.

**Current Workaround:**
- Manually add products via Catalog Builder
- Use REST API for programmatic import (developers)

**Request Feature:**
If you need bulk import urgently, contact support@webstuffguylabs.com.

---

### Does it send data to third parties?

**No.** All data stays on your WordPress site unless you:
- Configure external webhook (optional, Agency feature)
- Enable remote analytics (optional, can be disabled)

**Privacy:**
- Lead data stored in WordPress database
- PDFs generated on your server
- No external dependencies (except license validation)

See readme.txt Privacy section for details.

---

### Is it GDPR compliant?

**Yes.** The plugin is designed with privacy in mind:
- All data stored locally
- Lead capture requires user consent (checkbox)
- IP addresses hashed (not stored in plain text)
- Data export available (CSV)
- Data deletion supported

**Your Responsibility:**
- Update privacy policy to mention lead collection
- Provide data access/deletion upon request
- Follow local regulations (GDPR, CCPA, etc.)

---

## Features & Usage

### Can users save drafts and return later?

**Yes (Pro feature).** Server-side drafts allow users to:
- Save product selections
- Resume later
- Share draft URLs with team members

**Available in:** Pro and Agency licenses

**How it Works:**
1. User selects products
2. Clicks "Save Draft"
3. Unique URL generated
4. Return to URL to resume

---

### Can I track when someone views a PDF?

**Yes (Pro feature).** PDF tracking shows:
- View count per PDF
- Timestamps of each view
- Recipient information
- Engagement analytics

**Available in:** Pro and Agency licenses

See [PDF Tracking Guide](./tracking.md) for details.

---

### Can I customize the PDF appearance?

**Yes.** Customization options:
- Upload logo
- Set brand colors
- Company information
- Custom footer text

**Pro Features:**
- PDF themes (Engineering, Architectural, Corporate)
- Custom watermarks (DRAFT, CONFIDENTIAL, etc.)
- Approval signature blocks

See [Branding Guide](./branding-pdfs.md) for details.

---

### Can I add a watermark to PDFs?

**Yes (Pro feature).** Add custom diagonal watermarks across all PDF pages.

**Common uses:**
- DRAFT - For review copies
- CONFIDENTIAL - Sensitive documents
- FOR REVIEW ONLY - Preliminary submittals
- PRELIMINARY - Early-stage documents

**How to enable:**
Settings → Branding → PDF Watermark (Pro only)

**Available in:** Pro and Agency licenses

---

### Can I add approval signature blocks?

**Yes (Pro feature).** Add a 3-column approval table at the end of each product sheet.

**Includes fields for:**
- Approved By (name)
- Title (role)
- Date

**Use cases:**
- AHJ approval
- Engineering sign-off
- Code compliance
- Quality assurance

**How to enable:**
Frontend Builder → Review page → Check "Include Approval Signature Block"

**Available in:** Pro and Agency licenses

---

### Can I collect user information before download?

**Yes (Pro feature).** Lead capture form collects:
- Name
- Email (required)
- Company
- Phone
- Project details

**Available in:** Pro and Agency licenses

**Export:** Leads exportable as CSV from admin dashboard.

---

### Can customers request multiple PDFs?

**Yes.** Users can generate unlimited PDFs:
- No restrictions on number of PDFs
- Each PDF generates unique download link
- PDFs auto-delete after retention period (default: 24 hours)

---

### Can I email PDFs automatically?

**Yes (Pro feature).** Auto-email feature:
- Sends PDF to user after generation
- Customizable email template
- Works with lead capture
- Includes tracking link

**Available in:** Pro and Agency licenses

**Configuration:** Settings → Pro → Auto Email Packets

---

### Can users share PDFs?

**Yes.** Users can:
- Download PDF to device
- Email as attachment
- Share temporary download URL
- Share tracking URL (Pro) for view analytics

**Note:** Temporary URLs expire after retention period (default: 24 hours).

---

### Does it work on mobile devices?

**Yes.** The frontend builder is fully responsive:
- Works on phones and tablets
- Touch-friendly interface
- Optimized for small screens
- PDF generation works on mobile (may be slower)

**Best Experience:** Desktop or tablet for selecting large numbers of products.

---

### Can I have multiple builder pages?

**Yes.** You can create multiple pages with `[submittal_builder]` shortcode:
- Different pages for different audiences
- Customize per page (using filters)
- No limit on number of builder pages

**Example Use Cases:**
- Public page for customers
- Private page for sales team
- Page per product category

---

## Pricing & Licensing

### What's included in the free version?

**Free Version Features:**
- Unlimited products
- Product catalog builder
- Frontend builder interface
- PDF generation
- Basic branding (logo, colors)
- Search and filtering
- Table of contents
- Summary table

**Not Included in Free:**
- Lead capture
- PDF tracking
- Auto email
- Server drafts
- White-label mode
- Custom PDF themes
- Priority support

---

### How do Pro licenses work?

**Pro Single Site:**
- License key for 1 WordPress site
- All Pro features unlocked
- 1 year of updates and support
- Renew annually for continued updates

**Pro Agency:**
- License key for up to 5 WordPress sites
- All features including white-label
- Priority support
- 1 year of updates and support

**Activation:**
1. Purchase license
2. Install plugin on site
3. Enter license key in Settings → Pro
4. Click "Activate License"
5. Pro features enabled immediately

---

### Do I need to renew my license?

**For Updates & Support:** Yes, annual renewal required.

**After License Expires:**
- Plugin continues working
- Pro features remain active
- No updates available
- No support access

**Renew To:**
- Get plugin updates
- Receive new features
- Access support
- Maintain compatibility with WordPress updates

---

### Can I upgrade from Free to Pro?

**Yes.** Easy upgrade process:
1. Purchase Pro license
2. Enter license key in existing installation
3. Pro features unlock immediately
4. All data preserved (products, settings, etc.)
5. No reinstallation needed

---

### Can I transfer my license to a new site?

**Yes.**

**Steps:**
1. Deactivate license on old site (Settings → Pro → Deactivate)
2. Install plugin on new site
3. Activate license on new site
4. License transfers immediately

**Note:** Single Site license can only be active on 1 site at a time. Agency license supports 5 sites.

---

### Do you offer refunds?

**Yes.** 30-day money-back guarantee:
- Request refund within 30 days of purchase
- No questions asked
- Full refund issued

**Contact:** support@webstuffguylabs.com

---

## Troubleshooting

### PDF generation is failing. What should I do?

**Common Solutions:**

1. **Check server requirements** (PHP version, memory, execution time)
2. **Enable WordPress debug mode** and check error logs
3. **Try generating with fewer products** to isolate issue
4. **Increase PHP memory limit** to 256MB
5. **Increase execution time** to 120 seconds
6. **Contact support** with error messages

See [Troubleshooting Guide](./troubleshooting.md#pdf-generation-fails) for detailed solutions.

---

### Products aren't showing on the frontend. Why?

**Common Causes:**

1. **Products not fully configured** (missing hierarchy levels)
2. **Specifications not filled in** (models need specs)
3. **Cache not cleared** (try hard refresh)
4. **JavaScript error** (check browser console)
5. **Shortcode missing** or misspelled

See [Troubleshooting Guide](./troubleshooting.md#products-not-displaying) for solutions.

---

### My logo isn't appearing in PDFs. How do I fix this?

**Solutions:**

1. **Re-upload logo** via Settings → Branding
2. **Check file format** (PNG or JPG, under 2MB)
3. **Verify logo saved** (success message should appear)
4. **Clear cache** and regenerate PDF
5. **Check file permissions** on uploads folder

See [Branding Guide](./branding-pdfs.md#logo-doesnt-appear-in-pdf).

---

### How do I get support?

**Free Version:**
- **WordPress.org Forum:** https://wordpress.org/support/plugin/submittal-builder/
- **Documentation:** Full docs available

**Pro Version:**
- **Email Support:** support@webstuffguylabs.com
- **Priority Response:** 24-48 hour response time
- **Advanced Assistance:** Technical troubleshooting, customization help

**Before Requesting Support:**
1. Check [Troubleshooting Guide](./troubleshooting.md)
2. Search [FAQ](./faq.md)
3. Enable debug mode and note error messages
4. Gather system info (WordPress version, PHP version, etc.)

---

## Advanced Questions

### Can I customize the plugin with code?

**Yes.** Developer-friendly features:
- REST API for programmatic access
- WordPress hooks (actions and filters)
- Template overrides
- JavaScript API
- CSS customization

See [Developer Resources](./developer-resources.md) for details.

---

### Can I white-label the plugin?

**Yes (Agency feature).** White-label mode removes:
- Plugin branding from PDFs
- Plugin name from admin interface
- "Generated by" footer text
- Plugin metadata

**Available in:** Agency license only

---

### Can I integrate with my CRM?

**Yes.** Integration options:
- **Webhook (Agency):** POST lead data to external URL
- **REST API:** Programmatically access data
- **WordPress Hooks:** Trigger custom actions on events
- **Export CSV:** Manually import to CRM

**Popular Integrations:**
- Zapier (via webhook)
- Make (formerly Integromat)
- Custom APIs
- Email forwarding

---

### Can I sell this plugin to my clients?

**Yes (with Agency license).**

**Allowed:**
- Install on client sites (up to 5)
- White-label branding
- Charge clients for setup/configuration
- Include in website packages

**Not Allowed:**
- Resell plugin itself
- Distribute plugin files publicly
- Exceed 5 site activation limit

**Questions?** Contact support@webstuffguylabs.com for commercial use inquiries.

---

### Does it work with WooCommerce?

**Not directly.** The plugin is standalone and doesn't integrate with WooCommerce product catalog.

**Possible Workaround (Developer):**
- Use WordPress hooks to sync WooCommerce products
- Custom development required
- Contact for custom integration quote

---

### Can I translate the plugin?

**Yes.** The plugin is translation-ready:
- POT file included in `/languages/` folder
- Use Loco Translate or Poedit
- All strings are translatable

**Languages:**
Currently available: English (en_US)

**Contribute Translations:**
If you create a translation, share it with us for inclusion in future releases!

---

## Roadmap & Feature Requests

### What new features are coming?

**Planned Features:**
- Bulk CSV/Excel import
- Product categories with images
- Advanced search filters
- Custom fields per product type
- Email templates customization
- Analytics dashboard improvements

**Timeline:** Features added based on user demand and development priorities.

---

### Can I request a feature?

**Yes!** We welcome feature requests:
- **WordPress Forum:** Post in support forum with "Feature Request" tag
- **Email:** support@webstuffguylabs.com
- **Provide Details:** Use case, expected behavior, priority

**Popular Requests Get Priority:** Features requested by multiple users are implemented first.

---

### Is there a demo site?

**Coming Soon.** A demo site is being prepared where you can:
- Test the frontend builder
- Generate sample PDFs
- Explore admin interface
- Try Pro features

**Interim Solution:** Install on local development site (Local by Flywheel, XAMPP, etc.) to test before purchasing.

---

## Still Have Questions?

### Documentation

Browse the complete documentation:
- [Getting Started](./getting-started.md)
- [Installation Guide](./installation.md)
- [User Guide](./user-guide.md)
- [Admin Settings](./admin-settings.md)
- [Troubleshooting](./troubleshooting.md)
- [Developer Resources](./developer-resources.md)

### Contact Support

**Free Users:**
WordPress.org Forum: https://wordpress.org/support/plugin/submittal-builder/

**Pro Users:**
Email: support@webstuffguylabs.com

**Include in Support Request:**
- WordPress version
- PHP version
- Plugin version
- Description of issue
- Steps to reproduce
- Error messages (if any)
- Screenshots (helpful)

---

[← Back to Documentation](./index.md)

---

© 2025 Webstuffguy Labs. All rights reserved.
