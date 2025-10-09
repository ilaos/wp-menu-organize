=== Submittal & Spec Sheet Builder ===
Contributors: Webstuffguy Labs
Donate link: https://example.com/donate
Tags: submittal, spec sheets, construction, pdf, document builder, catalog, manufacturing, architecture, engineering, rfq, approvals, proposals
Requires at least: 6.0
Tested up to: 6.6
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate professional submittal & spec sheet PDF packets from your catalog—cover, summary, TOC, and detailed specs—perfect for approvals, RFQs, and field teams.

== Description ==

Submittal & Spec Sheet Builder helps you turn product selections into polished, branded PDF packets. It's not a store or checkout—it's a documentation tool for technical selections. Visitors (or your team) pick items from your catalog, and the plugin exports a packet with a cover page, summary, table of contents, and detailed spec sheets.

**Who is it for?**

* **Manufacturers & Reps** — let contractors/architects assemble product specs to submit for approval
* **Distributors** — produce consistent spec packets for RFQs and quotes
* **Contractors/Subs** — compile submittals for architects and owners
* **Architects/Engineers** — bundle selected materials/equipment into a transmittal/spec set

**Outcomes:**

* Fewer back-and-forths and faster approvals
* Consistent, on-brand documentation
* Clear specs for procurement and field teams

**= Free =**

* **Local autosave:** selections & form details persist after refresh
* **Packet mode:** Cover, Summary, Clickable TOC, page numbers
* **Branding:** logo, colors, company details, headers/footers
* **Product Catalog Builder:** Create unlimited categories, products, and specification fields
* **Spec Tables:** Automatically format product specifications in clean, organized tables
* **Client-Facing Shortcode:** `[submittal_builder]` displays a professional form on any page
* **Responsive Design:** Works on desktop, tablet, and mobile devices

**= Pro =**

* **Save & Share progress (server drafts):** private link to resume on any device (auto-expires after 45 days)
* **White-label exports, themes, watermarks, signatures**
* **Append external PDFs, public tracking links, auto-archive, auto-email**
* **Automated Email Delivery:** Send PDF packets directly to clients and team members
* **Project Archiving:** Automatically organize and store PDFs by project name and date
* **Download Tracking:** Generate public tracking links to monitor downloads
* **Custom PDF Themes:** Architectural (blue), Corporate (green), or Engineering themes
* **Approval Workflows:** Include signature blocks with approver name, title, and date
* **Priority Support:** Dedicated email and chat support

**How It Works:**

1. Build your product catalog in WP Admin using categories, products, and types
2. Add your company branding (logo, colors, contact info)
3. Use the shortcode `[submittal_builder]` on any page
4. Visitors select products and generate branded PDF packets
5. Download or email completed submittals instantly

**Technical Features:**

* REST API architecture for modern, fast performance
* Modular PDF templates (easily customizable)
* Theme-based color systems
* Running headers/footers with page numbers
* Smart filename generation (project-based)
* FPDI support for merging external PDFs (Pro)
* Extensible registry for add-ons and custom features

**Block Editor Compatible:**

While this plugin uses a shortcode (`[submittal_builder]`) for maximum compatibility, it works seamlessly with the WordPress Block Editor (Gutenberg). Simply add a Shortcode block and paste `[submittal_builder]` to display the submittal form. The plugin also works with Classic Editor and any page builder that supports WordPress shortcodes.

== Installation ==

**Automatic Installation:**

1. Go to Plugins → Add New in your WordPress admin
2. Search for "Submittal & Spec Sheet Builder"
3. Click "Install Now" and then "Activate"

**Manual Installation:**

1. Download the plugin ZIP file
2. Upload to `/wp-content/plugins/` and extract
3. Activate through the Plugins menu in WordPress

**Setup:**

1. Navigate to **Submittal Builder → Branding** to add your company logo, name, and contact info
2. Go to **Submittal Builder** to build your product catalog
3. Add the shortcode `[submittal_builder]` to any page where you want the form
4. Start generating professional submittal packets!

**Pro Upgrade:**

1. Purchase a license key from our website
2. Go to **Submittal Builder → Upgrade**
3. Enter your license key to unlock all Pro features

**Server Requirements:**

* PHP 7.4 or higher (PHP 8.1+ recommended for optimal performance)
* WordPress 6.0 or higher
* PHP Extensions: `dom`, `gd`, `mbstring` (required for PDF generation)
* Recommended PHP Memory: 256MB+ (for large catalogs with 100+ products)
* Writable `/wp-content/uploads/` directory (for PDF storage)
* Modern browser (Chrome, Firefox, Safari, Edge) for admin interface

**Hosting Compatibility:**

* Tested on Apache, Nginx, and LiteSpeed servers
* Compatible with shared hosting, VPS, and dedicated servers
* Works with popular hosts: Bluehost, SiteGround, WP Engine, Kinsta
* Pretty permalinks recommended but not required

**Troubleshooting:**

* If PDF generation fails, increase `memory_limit` to 256M in `php.ini`
* If uploads fail, check directory permissions (755 for directories, 644 for files)
* For large catalogs (500+ products), consider increasing `max_execution_time` to 120 seconds

== Frequently Asked Questions ==

= Does this plugin work for industries beyond construction? =

Absolutely! While it's designed with construction submittals in mind, it's flexible enough for manufacturing, architectural firms, equipment suppliers, or any business needing branded specification documents.

= Do I need the Pro version? =

The free version works perfectly for individual users and small teams. The Pro upgrade adds automation (email, archiving, tracking), advanced branding (white-label, themes, watermarks), and data features (external PDF merging, signatures) that are essential for agencies and high-volume users.

= Can I customize the PDF templates? =

Yes! The plugin uses modular PHP templates located in `templates/pdf/` that you can override in your theme. We also provide filters for agencies to create custom theme packs.

= Does it work with page builders? =

Yes! The shortcode `[submittal_builder]` works with Gutenberg, Elementor, Divi, and any other page builder that supports shortcodes.

= What PDF library does it use? =

We use Dompdf for HTML-to-PDF conversion, which is included with the plugin (no external dependencies). Pro users can optionally install FPDI for merging external PDFs.

= Can I limit who can build submittals? =

The front-end form is public by default (great for client self-service), but you can add your own access control using membership plugins or custom code. The admin builder requires `manage_options` capability.

= Is there a limit on products or PDF size? =

No artificial limits. The only constraint is your server's PHP memory limit (we recommend at least 256MB for large catalogs with many products).

= Does it store customer data? =

The plugin only stores the product catalog and branding settings. Generated PDFs are saved to `/wp-uploads/sfb/` but no customer information is retained unless you enable Pro features like archiving or tracking (which store project names and links).

= Can I translate the plugin? =

Yes! The plugin is fully translation-ready with the text domain `submittal-builder`. You can use Loco Translate or WPML to create translations.

= Where are my selections stored? =

Free: locally in your browser (localStorage).
Pro: when you click "Save progress", we create a private, unlisted draft on the site that you can reopen from any device.

= How long do server drafts last? =

By default, drafts expire after 45 days (site admin can change this in Settings → Drafts).

= Is my draft public? =

Draft links are unlisted. Anyone with the link can view/restore it until it expires. You can disable server drafts entirely in Settings → Drafts.

= Can I turn autosave off? =

Yes. Admins can toggle local autosave and server drafts in Settings → Drafts.

== Screenshots ==

1. Submittal form interface with category toggles and product selection
2. Product selection sidebar showing detailed specifications
3. Generated PDF preview with cover page, summary, and TOC
4. Admin branding settings page with logo upload and color picker
5. Save & Share progress (Pro): copy a private draft link
6. Restore from link: resume selections on any device

== Changelog ==

= 1.0.1 =
* New: Local autosave and restore banner (Free)
* New: Shareable server drafts with private links + expiry (Pro, default 45 days)
* New: Settings → Drafts controls (enable/disable, expiry days, rate limit, privacy note)
* Accessibility: Modals with focus trap, ESC to close, aria-live toasts
* Polish: Status endpoint and improved error handling for expired/missing drafts

= 1.0.0 =
* Initial release
* Core PDF generator with cover, summary, and TOC
* Branding customization (logo, colors, footer text)
* Hierarchical product catalog (categories → products → types → models)
* Public-facing shortcode for client submissions
* REST API architecture with modern JS frontend
* Modular template system for easy customization
* Extensible Pro architecture with 8 advanced features
* Feature registry with grouping (Core, Automation, Branding, Data, Distribution)
* Admin upgrade screen with changelog
* Ready for WordPress.org submission

== Upgrade Notice ==

= 1.0.1 =
Adds local autosave (Free) and shareable drafts (Pro). Visit Settings → Drafts to configure.

= 1.0.0 =
First release — build branded submittal packets and spec sheets directly in WordPress. Free version includes full PDF generation with cover, summary, and TOC. Pro adds automation, white-labeling, and advanced features.

== Privacy & Data ==

**Data Collection:**

This plugin does NOT collect or transmit any personal information to external servers. All data remains on your WordPress installation.

**What Data is Stored:**

* **Product Catalog:** Categories, products, specifications (stored in custom database tables)
* **Branding Settings:** Company logo, name, contact info, colors (stored in WordPress options)
* **Local Drafts (Free):** Selections stored in browser localStorage only (never sent to server)
* **Server Drafts (Pro):** Draft selections stored temporarily on your server for sharing (auto-expire after 45 days by default)
* **Generated PDFs:** Saved to `/wp-content/uploads/sfb/` directory on your server

**No External Services:**

* No data sent to third-party services
* No tracking cookies or analytics
* No "phone home" functionality
* PDF generation happens entirely on your server using the open-source DomPDF library

**GDPR Compliance:**

* **Right to Access:** All data viewable in WordPress admin
* **Right to Erasure:** Server drafts auto-expire (configurable); uninstall removes all plugin data
* **Data Minimization:** Only essential data is stored (no personal info required for basic use)
* **User Control:** Settings page allows disabling server drafts entirely

**Data Retention:**

* **Product Catalog:** Retained until manually deleted or plugin uninstalled
* **Server Drafts (Pro):** Auto-deleted after expiry period (default 45 days, configurable 1-365 days)
* **Generated PDFs:** Retained indefinitely (can be manually deleted from uploads directory)
* **Settings:** Retained until plugin uninstalled

**On Plugin Uninstall:**

If "Remove all data on uninstall" is enabled in Settings:
* Custom database tables deleted
* All plugin options removed
* Server drafts deleted
* `/wp-content/uploads/sfb/` directory deleted (optional)

**Third-Party Font Loading:**

DomPDF may load Google Fonts from CDN for PDF rendering. This can be disabled by setting `DOMPDF_ENABLE_REMOTE = false` in your theme's functions.php.

**For Site Administrators:**

* Configure draft retention in **Settings → Drafts**
* Add privacy policy notes to users via Settings → Privacy Note field
* Regularly purge old drafts via **Tools → Purge Expired Drafts**
* Review generated PDFs in `/wp-content/uploads/sfb/` directory

== Developer Notes ==

**Filters & Hooks:**

* `sfb_features_map` - Register custom Pro features
* `sfb_pro_changelog` - Add changelog entries for add-ons
* `sfb_is_pro_active` - Override Pro activation logic
* `sfb_pdf_theme` - Filter PDF theme selection
* `sfb_pdf_color` - Filter primary color before rendering

For complete API and hooks documentation, see `API-REFERENCE.md` and `DEVELOPER-HOOKS.md` in the plugin directory.

**Template Overrides:**

Copy templates from `plugins/submittal-builder/templates/pdf/` to `your-theme/submittal-builder/` to customize.

**REST API Endpoints:**

* `GET /wp-json/sfb/v1/form/{id}` - Get product catalog
* `POST /wp-json/sfb/v1/generate` - Generate PDF packet
* `GET /wp-json/sfb/v1/settings` - Get branding settings
* `POST /wp-json/sfb/v1/license` - Activate Pro license

See `API-REFERENCE.md` for complete documentation of all 27 endpoints.

**Add-on Development:**

See our [Developer Documentation](https://example.com/docs) for creating feature packs and extensions.

== License ==

This plugin is licensed under the GPLv2 or later.

Copyright (C) 2025 Webstuffguy

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

== Credits ==

* Dompdf library for PDF generation (LGPL 2.1)
* Modern admin UI inspired by WordPress design patterns
