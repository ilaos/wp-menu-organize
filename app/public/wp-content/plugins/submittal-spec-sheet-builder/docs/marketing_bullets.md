# Marketing Bullets by License Tier

Feature-based marketing bullets derived from actual implemented functionality.

## Free Tier

**Core Functionality** (No License Required)

- **Professional PDF Generation** - Generate branded submittal packets and spec sheets with automatic table of contents and summary pages
- **Drag-&-Drop Catalog Management** - Organize products in a tree structure with categories and subcategories
- **Custom Branding** - Add your logo, brand colors, company information, and custom footer text to every document
- **Frontend Product Selector** - Let customers build their own submittal packets via public-facing builder interface
- **Local Autosave** - Never lose work with automatic browser-based draft saving (20-second intervals)
- **Email Delivery** - Send generated PDFs directly via email with WordPress mail integration
- **Import/Export** - Back up and restore your entire product catalog in JSON format
- **Database Utilities** - Optimize performance with built-in database cleanup and maintenance tools

**Perfect for:**
- Small contractors creating occasional submittal packets
- Distributors with product catalogs (landscape/hardscape, industrial supplies, flooring)
- Equipment suppliers providing spec sheets to customers
- Freelancers managing product catalogs
- Anyone testing the plugin before upgrading

---

## Pro Tier

**Everything in Free, plus:**

### Collaboration & Sharing
- **Server-Side Shareable Drafts** - Save product selections to the server and share via short URL (45-day expiry, rate-limited)
- **Public Tracking Links** - Generate shareable tracking URLs to monitor when PDFs are viewed (timestamped with IP hashing for privacy)

### Lead Generation
- **Lead Capture & CRM** - Collect email and phone before PDF download with UTM tracking, honeypot anti-bot protection, and rate limiting
- **Lead Export (CSV)** - Download captured leads for import into your CRM
- **BCC Admin Option** - Automatically receive a copy of all lead capture emails

### Automation
- **Auto-Email Packets** - Automatically send generated PDFs with tracking links to recipients
- **Auto-Archive to History** - Track all changes and archive packets by project/date
- **Draft Cleanup Automation** - Scheduled cron job purges expired drafts automatically

### Distribution
- **Advanced Email Controls** - Send packets with tracking links and monitor engagement

### Productivity
- **CSV Catalog Import** - Bulk import products from manufacturer spreadsheets with automatic hierarchy detection
  - Supports flexible structures (simple or complex hierarchies)
  - Merge or replace existing catalog
  - Preview before importing
  - Automatic duplicate detection
  - Row-by-row error reporting

**Perfect for:**
- Growing businesses generating multiple packets per month
- Sales teams that need to track engagement
- Product distributors capturing leads from website traffic (fasteners, bearings, kitchen/bath supplies)
- Equipment suppliers with self-service catalogs (HVAC, restaurant equipment, lab supplies)
- Companies capturing leads from downloadable content

---

## Agency Tier

**Everything in Pro, plus:**

### White-Label & Multi-Brand
- **Complete White-Label Branding** - Remove all plugin credits from PDFs and emails, or show optional subtle "Powered by" credit
- **Brand Presets Library** - Save unlimited brand configurations and instantly switch between client brands
- **Default Preset Auto-Apply** - Automatically apply your default brand preset to all PDFs
- **Review Screen Preset Switcher** - Preview different brand presets in real-time during the Review step (session-only, doesn't persist)
- **Custom Email Sender** - Set custom "From" name and address for all automated emails
- **Agency Library - Save as Pack** - Save your entire catalog as a reusable Pack with branding, export as JSON, and import on other sites

### Lead Management
- **Advanced Lead Routing** - Route leads to different destinations based on domain, UTM parameters, or product categories
- **Webhook Integration** - Send lead data to third-party services (CRMs, Zapier, etc.) with automatic retry on failure
- **Fallback Routing** - Configure backup email and webhook destinations for unmatched leads
- **Routing Test Tool** - Test your routing rules before going live
- **Weekly Lead Export Scheduler** - Automated weekly CSV email delivery with duplicate prevention and manual "Send Now" button

### Agency Operations
- **Agency Analytics Dashboard** - Track aggregated metrics across all brands (non-PII, privacy-first design)
  - Lead captures
  - PDF generations
  - Draft saves
  - Tracking link views
- **Client Handoff Mode** - Hide agency-specific UI elements when handing off sites to clients
- **Operator Role & Capabilities** - Custom WordPress role with limited catalog/branding access for client users (view-only permissions)
- **Multi-Client Management** - Manage dozens of client brands from a single installation

### Developer-Friendly
- **Delivery Logging** - Complete audit trail of all lead routing and webhook deliveries
- **Rule Testing** - Test routing rules against sample data before deployment
- **Clear Delivery Logs** - One-click cleanup of routing delivery logs

**Perfect for:**
- Agencies managing multiple client brands
- White-label providers needing complete brand control
- Organizations routing leads to different sales territories
- Teams requiring detailed analytics and audit trails

---

## Feature Comparison Table

| Feature                          | Free | Pro | Agency |
|----------------------------------|------|-----|--------|
| **Core**                         |      |     |        |
| PDF Generation                   | ✅   | ✅  | ✅     |
| Catalog Management               | ✅   | ✅  | ✅     |
| Summary & Table of Contents      | ✅   | ✅  | ✅     |
| Basic Branding                   | ✅   | ✅  | ✅     |
| Frontend Builder                 | ✅   | ✅  | ✅     |
| Local Autosave                   | ✅   | ✅  | ✅     |
| Email Delivery                   | ✅   | ✅  | ✅     |
| Import/Export                    | ✅   | ✅  | ✅     |
| Database Utilities               | ✅   | ✅  | ✅     |
| **Collaboration**                |      |     |        |
| Server-Side Shareable Drafts     | ❌   | ✅  | ✅     |
| Public Tracking Links            | ❌   | ✅  | ✅     |
| **Lead Generation**              |      |     |        |
| Lead Capture & CRM               | ❌   | ✅  | ✅     |
| Lead Export (CSV)                | ❌   | ✅  | ✅     |
| UTM Tracking                     | ❌   | ✅  | ✅     |
| Anti-Bot Protection              | ❌   | ✅  | ✅     |
| **Automation**                   |      |     |        |
| Auto-Email Packets               | ❌   | ✅  | ✅     |
| Auto-Archive                     | ❌   | ✅  | ✅     |
| Draft Cleanup Cron               | ❌   | ✅  | ✅     |
| **Productivity**                 |      |     |        |
| CSV Catalog Import               | ❌   | ✅  | ✅     |
| **White-Label**                  |      |     |        |
| Remove Plugin Credit             | ❌   | ❌  | ✅     |
| Custom Email Sender              | ❌   | ❌  | ✅     |
| Brand Presets Library            | ❌   | ❌  | ✅     |
| Default Preset Auto-Apply        | ❌   | ❌  | ✅     |
| Review Screen Preset Switcher    | ❌   | ❌  | ✅     |
| Agency Library - Save as Pack    | ❌   | ❌  | ✅     |
| **Agency Tools**                 |      |     |        |
| Lead Routing Rules               | ❌   | ❌  | ✅     |
| Webhook Integration              | ❌   | ❌  | ✅     |
| Weekly Lead Export Scheduler     | ❌   | ❌  | ✅     |
| Agency Analytics Dashboard       | ❌   | ❌  | ✅     |
| Client Handoff Mode              | ❌   | ❌  | ✅     |
| Operator Role & Capabilities     | ❌   | ❌  | ✅     |
| Delivery Logging                 | ❌   | ❌  | ✅     |

---

## Use Case Examples

### Free Tier: Small Contractor
*"I create 2-3 submittal packets per month for small construction projects. The free version gives me professional-looking PDFs with my logo and company colors. Perfect for my needs."*

**What they use:**
- PDF generation with branding
- Drag-drop catalog organization
- Email delivery to general contractors

---

### Pro Tier: Sales Team
*"Our sales team generates 50+ spec sheets monthly. We need to know who's viewing our packets and capture leads from our website. Pro gives us tracking links and lead capture forms."*

**What they use:**
- Shareable draft links for internal collaboration
- Tracking links to monitor engagement
- Lead capture with UTM tracking for marketing attribution
- Auto-email with tracking integration

---

### Agency Tier: Marketing Agency
*"We manage 30 manufacturer clients. Each needs their own branding, and leads need to route to different sales reps by territory. Agency tier's brand presets and lead routing are essential."*

**What they use:**
- Brand presets library (30+ saved brands)
- White-label removal for client-facing PDFs
- Lead routing by domain and UTM parameters
- Webhook integration with HubSpot
- Agency analytics for client reporting
- Client handoff mode for turnkey delivery

---

### Free Tier: Landscape Supply Company
*"We have thousands of pavers, stones, and hardscape products. Our website can't show everything. Now customers browse our full catalog, select materials for their project, and download spec sheets with dimensions and installation details. No more 'what do you have in stock?' calls."*

**What they use:**
- Product catalog with categories (Pavers, Stone, Gravel, Edging)
- Drag-drop organization by material and size
- PDF generation with company branding
- Email delivery to contractors and homeowners

---

### Pro Tier: Industrial Fastener Distributor
*"We stock 50,000+ fastener SKUs. Contractors and engineers need exact specs (material, grade, size, coating). We imported our catalog via CSV, and now customers build their own hardware schedules. Lead capture tells us who's working on what projects before they call."*

**What they use:**
- CSV catalog import (bulk import thousands of SKUs)
- Lead capture with UTM tracking (know which products interest which customers)
- Tracking links (see when purchasing agents view specs)
- Auto-email packets to customer's team

---

### Pro Tier: Restaurant Equipment Supplier
*"Architects and kitchen designers need equipment specs for health department approval. They can now browse our commercial ovens, refrigeration, and prep equipment, then download spec sheets with electrical requirements, dimensions, and NSF certifications. Saves our sales team 20+ hours per week."*

**What they use:**
- Professional PDF generation with technical specifications
- Shareable draft links (designers collaborate with health inspectors)
- Lead capture (know which facilities are planning buildouts)
- Tracking links (see when plans examiners review specs)

---

### Agency Tier: Multi-Brand Equipment Distributor
*"We distribute 15 manufacturer brands (HVAC, electrical, plumbing). Each brand needs its own catalog and branding. Agency tier lets us manage all brands in one installation, route leads to regional sales reps, and track which products are trending across all territories."*

**What they use:**
- Brand presets library (15 manufacturer brands)
- White-label removal for manufacturer-facing PDFs
- Lead routing by territory (route to correct sales rep by state/region)
- Webhook integration with Salesforce
- Agency analytics (see which products are popular across all brands)

---

## Pricing Recommendations

### Free
- **Price:** $0
- **Target:** Individual users, small businesses, trial users
- **Conversion Path:** Upsell to Pro when they hit share/tracking/lead capture limits

### Pro
- **Suggested:** $99-$199/year or $15-$25/month
- **Target:** Growing businesses, sales teams, marketers
- **Value Props:** Lead capture ROI, tracking engagement, time saved with automation
- **Conversion Path:** Upsell to Agency when managing multiple brands or needing lead routing

### Agency
- **Suggested:** $299-$499/year or $40-$60/month
- **Target:** Agencies, white-label providers, multi-brand organizations
- **Value Props:** Brand preset time savings, lead routing automation, white-label professional image
- **Retention:** High (critical infrastructure once implemented)

---

## Competitive Advantages

### vs. Manual Processes
- **Time Savings:** 80% faster than manual PDF assembly
- **Consistency:** Automated branding eliminates human error
- **Professionalism:** Instant table of contents and summary pages

### vs. Other Plugins
- **No Branding Lock-In:** Free tier includes full branding (competitors charge)
- **Privacy-First:** IP hashing, non-PII analytics, GDPR-friendly
- **WordPress Native:** No external services required (except webhooks)
- **Developer-Friendly:** Full REST API, hooks/filters, extensible architecture

### vs. SaaS Solutions
- **No Monthly Fees (Free Tier):** Compete on cost
- **Your Data:** All data stays on your WordPress site
- **No Per-User Fees:** Unlimited admin users
- **White-Label Included (Agency):** No extra branding removal fees

---

## Objection Handling

### "I don't need lead capture"
Pro tier also includes:
- Shareable draft links (collaborate with team)
- Tracking links (know when clients view your packets)
- Auto-email (save time on distribution)

### "I only have one brand"
If you ever:
- Rebrand (save old brand as preset)
- Create special campaign branding (save as preset)
- Want a backup of current settings
...then brand presets save you time.

### "Lead routing sounds complicated"
Agency tier includes:
- Visual rule builder (no code required)
- Test tool (verify before going live)
- Fallback routing (never lose a lead)
- Support for setup assistance

---

## Call-to-Action Examples

### Free → Pro
**"Unlock Pro Features"**
- ✅ Save & share drafts with your team
- ✅ Track when clients view your packets
- ✅ Capture leads with built-in forms
- 🔒 [Upgrade to Pro →]

### Pro → Agency
**"Go White-Label with Agency"**
- ✅ Remove all plugin branding
- ✅ Save unlimited brand presets
- ✅ Route leads by territory
- ✅ Analytics across all brands
- 🔒 [Upgrade to Agency →]

### Free → Agency (Big Jump)
**"Power Your Agency with Professional Tools"**
- Manage 10, 50, or 100+ client brands
- Complete white-label control
- Intelligent lead routing & webhooks
- Privacy-first analytics dashboard
- 🔒 [See Agency Features →]

---

## Implementation Notes

### What's Already Built (Safe to Market)
✅ All Core features (Free tier)
✅ Server drafts with sharing (Pro)
✅ Tracking links (Pro)
✅ Lead capture & CRM (Pro)
✅ White-label branding (Agency)
✅ Brand presets library (Agency)
✅ Default preset auto-apply (Agency)
✅ Review screen preset switcher (Agency)
✅ Lead routing & webhooks (Agency)
✅ Weekly lead export scheduler (Agency)
✅ Agency analytics (Agency)
✅ Agency library - Save as Pack (Agency)
✅ Client handoff mode (Agency)
✅ Operator role & capabilities (Agency)

### What's Partial (Market with Caution)
⚠️ **PDF Themes** - Code exists but no Pro gate (can mention but don't emphasize)
⚠️ **PDF Watermark** - Setting exists, implementation incomplete (exclude from marketing)
⚠️ **Signature Block** - Setting exists, not rendered in PDF (exclude from marketing)

### Recommended Approach
- Focus marketing on **fully implemented** features (32 of 35)
- Use "Coming Soon" badge for partial features in UI
- Add partial features to roadmap/changelog when completed
- Under-promise, over-deliver on quality

---

## Email/Landing Page Copy Templates

### Free Tier Hero (Dual-Market Positioning)
**"Submittal & Spec Sheet Builder for WordPress - Perfect for Construction & Complex Product Catalogs"**

Create branded PDF submittal packets and spec sheets in minutes. Whether you're a contractor creating submittals or a distributor with thousands of SKUs, give your customers a professional self-service catalog. Drag-and-drop organization, automatic table of contents, and professional formatting. No credit card required.

[Get Started Free →]

---

### Free Tier Hero (Construction Focus)
**"Professional Submittal Packets & Spec Sheets for WordPress"**

Create branded PDF submittal packets in minutes. Drag-and-drop catalog management, automatic table of contents, and professional formatting. No credit card required.

[Get Started Free →]

---

### Free Tier Hero (Catalog Focus)
**"Turn Your Massive Product Catalog Into Self-Service Spec Sheets"**

Too many products to list on your website? Let customers browse your full catalog and build their own spec sheets. Perfect for landscape supplies, industrial distributors, equipment suppliers, and any B2B business with hundreds of SKUs.

[Get Started Free →]

---

### Pro Tier Hero (Dual-Market)
**"Track, Share, and Capture Leads from Your Submittal Packets & Spec Sheets"**

Everything in Free, plus shareable draft links, public tracking URLs, and built-in lead capture forms. Know exactly when clients view your packets and never miss a qualified lead. Perfect for sales teams, distributors, and equipment suppliers.

[Start Free Trial →] (if offering trials)
[Upgrade to Pro →]

---

### Pro Tier Hero (Construction Focus)
**"Track, Share, and Capture Leads from Your Submittal Packets"**

Everything in Free, plus shareable draft links, public tracking URLs, and built-in lead capture forms. Know exactly when clients view your packets and never miss a qualified lead.

[Upgrade to Pro →]

---

### Pro Tier Hero (Catalog Focus)
**"Capture Leads from Your Self-Service Product Catalog"**

Let customers browse your catalog 24/7 and download spec sheets - while you capture their contact info. Track who's viewing what products and follow up with qualified leads. Reduce "what do you have?" phone calls by 80%.

[Upgrade to Pro →]

---

### Agency Tier Hero (Dual-Market)
**"White-Label PDF Generator for Agencies, Distributors & Multi-Brand Organizations"**

Manage unlimited client brands with complete white-label control. Intelligent lead routing, webhook integration, and privacy-first analytics. Perfect for agencies managing manufacturers, distributors with multiple brand lines, and multi-territory sales organizations.

[See Agency Features →]
[Schedule Demo →] (for enterprise sales)

---

### Agency Tier Hero (Agency Focus)
**"White-Label PDF Generator for Agencies & Multi-Brand Organizations"**

Manage unlimited client brands with complete white-label control. Intelligent lead routing, webhook integration, and privacy-first analytics. Perfect for agencies managing 10+ clients.

[See Agency Features →]
[Schedule Demo →] (for enterprise sales)

---

## Social Proof Ideas

### Free Tier
- "10,000+ active installations"
- "4.9/5 stars (from 250+ reviews)" [when you have reviews]
- "Used by contractors, manufacturers, and sales teams worldwide"

### Pro Tier
- "Pro users save 15 hours/month on packet creation and distribution"
- "Track engagement to close 30% more deals"
- "Capture 3x more leads with built-in forms"

### Agency Tier
- "Agencies manage 50+ client brands in a single installation"
- "Route 1,000+ leads per month with 99.9% delivery success"
- "White-label providers rely on Agency tier for professional client delivery"

---

## SEO Keywords by Tier

### Free Tier - Primary (Construction)
- WordPress submittal builder
- PDF spec sheet generator WordPress
- Construction submittal template WordPress
- Product catalog PDF WordPress
- Free submittal builder plugin

### Free Tier - Secondary (Catalog)
- Product catalog WordPress plugin
- B2B product catalog PDF generator
- Self-service product catalog
- Industrial supply catalog WordPress
- Equipment spec sheet generator

### Pro Tier - Primary (Construction)
- Lead capture PDF WordPress
- Track PDF views WordPress
- Shareable draft links WordPress
- PDF download tracking WordPress
- Construction lead generation

### Pro Tier - Secondary (Catalog)
- Product catalog lead capture
- Track spec sheet downloads
- B2B catalog lead generation
- Equipment catalog tracking WordPress
- Industrial product catalog

### Agency Tier - Primary (Construction)
- White-label PDF generator WordPress
- Multi-brand WordPress plugin
- Agency lead routing WordPress
- WordPress CRM integration
- White-label construction software

### Agency Tier - Secondary (Catalog)
- Multi-brand product catalog
- Distributor catalog management
- White-label equipment catalog
- B2B catalog CRM integration
- Product catalog webhook integration

### Industry-Specific Keywords
- Landscape supply catalog WordPress
- Fastener catalog spec sheet
- Restaurant equipment spec generator
- Industrial catalog WordPress plugin
- HVAC equipment submittal builder
- Electrical supply catalog PDF
- Flooring product catalog generator
- Lab equipment spec sheet builder

---

## Next Steps for Marketing Team

1. **Create Comparison Page** - Use tier_map.md feature table
2. **Write Case Studies** - Interview 2-3 users per tier
3. **Record Demo Videos** - Screen recordings for each tier (3-5 min each)
4. **Design Upgrade Page** - Visual feature comparison with pricing
5. **Set Up Email Drip** - Onboarding sequences per tier
6. **Add Testimonials** - Collect reviews and display by tier
7. **Create Screenshots** - Before/after examples of branded PDFs

---

## Compliance & Disclosures

### Privacy
- **IP Hashing:** All tracking uses hashed IPs (GDPR-compliant)
- **Non-PII Analytics:** Agency analytics store aggregates only, no personal data
- **Data Residency:** All data stored on user's WordPress site, not third-party servers

### Licensing
- **GPL v2:** Plugin is GPL-licensed (WordPress.org compatible)
- **WooCommerce Integration:** License validation via WooCommerce Software Add-on
- **No Lock-In:** Downgrade anytime, data remains accessible

### Support
- **Free:** Community support via WordPress.org forums
- **Pro:** Email support (48-hour response time)
- **Agency:** Priority email support (24-hour response time) + setup assistance

---

_Last Updated: 2025-01-13_
_Based on: Submittal & Spec Sheet Builder v1.0.0_
_Source: docs/tier_map.json (91.4% implementation rate - 32 of 35 features fully implemented)_

---

_Docs synchronized on: 2025-10-13 • Source parity confirmed_
