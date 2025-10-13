=== Submittal & Spec Sheet Builder ===
Contributors: your-wp-org-username
Tags: submittals, specs, pdf, construction, leads, catalog, branding
Requires at least: 6.1
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create professional submittal packets from a product catalog in minutes. Capture leads, generate branded PDFs, and share tracking links. Agency-ready.

== Description ==

**Submittal & Spec Sheet Builder** helps contractors, manufacturers, and agencies create polished submittal packets fast:

- Select products, review specs inline, and **generate a single PDF**.
- Optional **lead capture** gate before download.
- **Branding controls** for colors, footer text, and cover page.
- Share **public tracking links** (Pro/Agency) and see engagement.

### Highlights

- 🧩 **Builder UI:** category → product → type → model, with inline specs.
- 🧾 **PDF Engine:** cover, summary/TOC, model sheets.
- 📨 **Leads:** modal form, admin list, CSV export, weekly export (Agency).
- 🎨 **Branding:** colors, footer, cover; **presets** (Agency) + preview.
- 📦 **Agency Library:** save current catalog as a reusable Pack (Agency).
- 🕵️ **White-Label Mode:** remove plugin credit; custom email sender (Agency).
- 🤝 **Client Handoff Mode:** hide agency panels + Operator permissions (Agency).
- 📈 **Analytics (light):** PDFs & leads counts, top products (Agency).
- 🔀 **Advanced Lead Routing:** rules + generic webhook (Agency).

### Tiers

- **Free:** Builder, PDF, basic branding, leads list + CSV export.
- **Pro (site license):** Tracking links, expanded branding/PDF options.
- **Agency (org license):** Branding presets & preview, Agency Library (Packs), Weekly lead export, White-Label Mode, Client Handoff (with Operator role), Analytics (light), Advanced Lead Routing.

> This plugin is fully translatable. Use the included text domain.

== Privacy ==

This plugin stores submitted lead data in your WordPress database.
**Optional Agency analytics** store non-PII usage events (counts, product names) locally; remote pings can be disabled via a filter.
**Webhooks/Lead Routing** send only the fields you configure to your chosen endpoint(s).

- Filter to disable remote analytics: `add_filter('sfb_enable_remote_analytics', '__return_false');`

== Installation ==

1. Upload the plugin ZIP and activate it.
2. Create a page and add the shortcode: `[submittal_builder]`.
3. (Optional) In **Settings → Branding**, set your colors and footer.
4. (Optional) Enable **Leads** (Pro/Agency features live under Branding/Settings).
5. Generate a submittal: pick products → Review → **Generate PDF**.

== Screenshots ==

1. Builder flow (select items with inline specs)
2. Review step (grouped items + brand preset pill)
3. Lead capture modal (before PDF)
4. Leads admin list with CSV export
5. Agency Library (Packs) with "Save as Pack"
6. Branding → Presets & White-Label card (Agency)
7. Client Handoff Mode banner (admin)
8. Agency Analytics (light) dashboard
9. Advanced Lead Routing rules (Agency)
10. Example PDF cover + summary page

== Frequently Asked Questions ==

= Does the plugin send data to third parties? =
By default, no. Agency analytics can optionally ping a remote aggregator with **non-PII** counts; you can disable with a filter. Lead webhooks post only to the URLs **you** configure.

= Can I brand the PDFs? =
Yes. Set colors and footer. Agency tier adds **Brand Presets** and **White-Label Mode** (remove credit, custom email sender).

= Can I import a catalog? =
Yes. Use **Industry Packs** (included) or Agency **Packs** you save from any site.

= Do I need Pro/Agency? =
Free covers core building and PDFs. Pro/Agency unlock lead and sharing automation, branding presets, and agency ops.

== Changelog ==

= 1.0.0 =
**New:**
- Review step polish: inline specs, sticky headers, accessible controls.
- Leads admin with CSV export; **Weekly Lead Export** (Agency).
- Branding: **Presets (A–C)** with default-to-PDF toggle, Review preset switcher (Agency).
- **Agency Library (Packs)**: save, export JSON, seed other sites.
- **White-Label Mode** (Agency): remove credit; custom PDF/email sender.
- **Client Handoff Mode** (Agency): hide panels + **Operator** role/caps.
- **Agency Analytics (light)**: PDFs/leads counts, top products, heartbeat.
- **Advanced Lead Routing**: rules + generic webhook with retries.
- Stability: multi-phase internal refactor for maintainability.

== Upgrade Notice ==

1.0.0 — First public release with Agency features. Review **Branding → White-Label** and **Agency Settings** after upgrade.
