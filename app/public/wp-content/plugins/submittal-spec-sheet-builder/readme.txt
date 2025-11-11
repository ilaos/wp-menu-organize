=== Submittal & Spec Sheet Builder ===
Contributors: webstuffguy
Tags: submittals, spec-sheets, pdf, construction, leads, catalog, branding
Requires at least: 6.1
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create professional PDF submittal packets and spec sheets. Build branded PDFs, organize product catalogs, and capture leads.

== Description ==

**Submittal & Spec Sheet Builder** helps manufacturers, suppliers, contractors, and distributors create professional submittal packets and product spec sheets in minutes — right inside WordPress.

Whether you're creating construction submittals or managing a complex product catalog with thousands of SKUs (landscape supplies, industrial fasteners, equipment, flooring, etc.), this plugin lets customers browse your catalog and build their own spec sheets.

Forget clunky spreadsheets and manual formatting. With this plugin you can:

- Organize products into categories, types, and models (handle hundreds or thousands of SKUs)
- Let visitors select items and instantly **generate polished PDF packets**
- Add your company logo, brand colors, and contact info automatically
- Collect project details or contact info before download for **lead capture**
- Manage and export leads directly from the admin panel
- Track who views your PDFs and follow up with qualified leads

### Key Features

- 🧩 **Visual Builder:** Browse by category → product → type → model, with inline specifications.
- 🧾 **PDF Generation:** Includes cover, table of contents, and product spec sheets.
- 🎨 **Branding Tools:** Add logos, colors, and custom footer text for consistent presentation.
- 📨 **Lead Capture:** Optional form before PDF download; export leads as CSV.
- 📦 **Product Catalog Manager:** Manage all product data inside WordPress — no developer needed.
- ⚙️ **Agency Tools (optional):** White-label branding, analytics, preset themes, and team handoff features.

> Fast, flexible, and built for the real world — ideal for construction professionals creating submittals, distributors with complex catalogs (landscape supplies, industrial parts, equipment), and any B2B business with hundreds of products that need professional spec sheets.

== Privacy & External Services ==

This plugin stores lead form submissions and analytics data locally in your WordPress database by default.

**License Activation (All Tiers):**
- When activating a Pro or Agency license, this plugin sends your license key and email to the license server at the domain configured in your license settings
- This is required to verify your purchase and check license status
- Data sent: license key, email address, site URL
- You can view license activation code in: `Includes/admin/license-api.php`

**Agency Analytics (Agency Tier Only):**
- If you have an Agency license AND if an analytics aggregator URL is configured in your license settings, this plugin will send usage statistics to that external server
- Data sent: site ID (hashed site URL), site URL, plugin version, PHP version, WordPress version, PDF generation counts, lead capture counts
- Data is sent via non-blocking HTTP POST requests to: `{aggregator_url}/analytics/event` and `{aggregator_url}/analytics/heartbeat`
- Daily heartbeat pings are sent automatically if configured
- This feature ONLY activates if: (1) you have an active Agency license AND (2) an `aggregator_url` is present in your license data
- If no aggregator URL is configured, all data stays local
- You can view analytics code in: `Includes/agency-analytics.php`

**Lead Routing Webhooks (Agency Tier Only):**
- If you configure webhook URLs for lead routing, lead data will be sent to those external endpoints when leads are captured
- This is optional and requires manual configuration in Settings → Agency
- You control what data is sent via webhook configuration

**Your Data Rights:**
- All local data can be deleted by deactivating the plugin
- External analytics only occur for Agency tier when explicitly configured
- No tracking pixels, cookies, or third-party scripts are loaded on your site
- No personally identifiable information (PII) is sent to analytics servers

== Installation ==

1. Upload the plugin ZIP via Plugins → Add New → Upload Plugin, then activate it.
2. Create a new page and add the shortcode: `[submittal_builder]`
3. Configure branding under Settings → Branding (logo, colors, footer).
4. Add or import your products in Catalog Builder.
5. Test the front-end builder by selecting products → Review → Generate PDF.

== Screenshots ==

Product browser – browse catalog by category and specification.

Selection step – add items to submittal with live count and visual feedback.

Review screen – confirm selections, add project info, and generate PDFs.

Optional lead capture – collect contact info before download.

Generated PDF – includes cover page, summary, and all selected spec sheets.

Branded cover – automatically styled with your logo and company details.

Product sheet – includes detailed specification fields for compliance.

Catalog builder – manage product hierarchy inside WordPress.

Custom fields – support for HVAC, Electrical, Plumbing, and Steel industries.

Quick add – add new models or types directly from the builder interface.

== Frequently Asked Questions ==

= Does this plugin send data to any third parties? =
Free and Pro tiers: No external data transmission except for license activation requests.

Agency tier: If you configure an analytics aggregator URL in your license settings, usage statistics (PDF counts, lead counts, version info) will be sent to that external server. See the "Privacy & External Services" section for full details.

All lead data and product catalogs are always stored locally in your WordPress database.

= Can I customize the PDF? =
Yes — you can control branding colors, footer text, and logo. Additional layout presets are available for agencies.

= Can I import or reuse catalogs? =
Yes — you can import existing data or use pre-made industry packs for faster setup.

= Is it compatible with page builders? =
Yes. The shortcode works inside Elementor, Gutenberg, and most other editors.

== Third-Party Libraries ==

This plugin includes the following open-source libraries:

= DomPDF =
* License: LGPL 2.1
* Used for: PDF generation
* Source: https://github.com/dompdf/dompdf

= php-font-lib =
* License: LGPL 2.1
* Used for: Font handling in PDFs
* Source: https://github.com/dompdf/php-font-lib

= php-svg-lib =
* License: LGPL 3.0
* Used for: SVG rendering in PDFs
* Source: https://github.com/dompdf/php-svg-lib

= HTML5-PHP (Masterminds) =
* License: MIT
* Used for: HTML parsing
* Source: https://github.com/Masterminds/html5-php

All libraries are GPL-compatible and included with this plugin.

== Changelog ==

= 1.0.0 =
Initial WordPress.org release

* Complete submittal and spec sheet PDF generation system
* Visual product catalog builder with category/product/model hierarchy
* Customizable branding (logo, colors, footer text)
* Lead capture forms with CSV export
* REST API for frontend PDF generation
* Pro features: PDF themes, watermarks, approval signature blocks
* Agency features: White-label mode, analytics, lead routing, webhooks
* Multi-tier licensing system (Free, Pro, Agency)
* Internationalization ready (i18n)
* Freemium architecture with feature gating

== Upgrade Notice ==

= 1.0.0 =
Initial release of Submittal & Spec Sheet Builder for WordPress.org. Create professional PDF submittal packets with full branding and lead capture capabilities.