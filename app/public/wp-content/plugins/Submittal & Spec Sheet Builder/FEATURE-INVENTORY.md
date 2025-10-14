# Submittal & Spec Sheet Builder - Complete Feature Inventory

**Version:** 1.0.3
**Last Updated:** 2025-10-12 (Agency features: White-Label Mode, Client Handoff Mode, Operator Role, Agency Analytics)
**Purpose:** Master reference for all features, capabilities, and infrastructure

---

## Related Documentation

**📚 For tier-specific information, see:**
- **[docs/tier_map.md](docs/tier_map.md)** - License tier feature matrix with Free/Pro/Agency access
- **[docs/tier_map.json](docs/tier_map.json)** - Machine-readable tier data for tooling
- **[docs/marketing_bullets.md](docs/marketing_bullets.md)** - Marketing copy and tier comparisons
- **[docs/TIER-AUDIT-EXECUTIVE-SUMMARY.md](docs/TIER-AUDIT-EXECUTIVE-SUMMARY.md)** - Tier audit executive summary
- **[FEATURE-STATUS.md](FEATURE-STATUS.md)** - Implementation status tracker with verification dates

---

## Table of Contents

1. [End-User Features](#end-user-features)
   - [Free Features](#free-features)
   - [Pro Features](#pro-features)
2. [Admin Features](#admin-features)
3. [Database Tables](#database-tables)
4. [REST API Endpoints](#rest-api-endpoints)
5. [AJAX Endpoints](#ajax-endpoints)
6. [Admin Pages](#admin-pages)
7. [Templates](#templates)
8. [Technical Infrastructure](#technical-infrastructure)
9. [Settings & Configuration](#settings--configuration)
10. [License Tiers](#license-tiers)

---

## End-User Features

### Free Features

#### **PDF Generation (Core)**
- ✅ **Cover Page** - Branded cover with company logo and project name
  - File: `templates/pdf/cover.html.php`
  - Customizable via branding settings

- ✅ **Summary Page** (`summary`)
  - Front summary grouped by category with key specifications
  - File: `templates/pdf/summary.html.php`
  - Registry: `Includes/pro/registry.php:137` (Free feature)

- ✅ **Table of Contents** (`toc`)
  - Clickable internal TOC with page numbers for fast navigation
  - File: `templates/pdf/toc.html.php`
  - Registry: `Includes/pro/registry.php:138` (Free feature)

- ✅ **Detailed Spec Sheets**
  - Individual model pages with full specification tables
  - File: `templates/pdf/model-sheet.html.php`
  - Automatic spec table formatting

- ✅ **Running Headers/Footers**
  - Professional page headers and footers with page numbers
  - Custom footer text support

#### **Product Catalog Management**
- ✅ **Unlimited Products** - No artificial limits on catalog size
- ✅ **Unlimited Categories** - Hierarchical organization
- ✅ **Custom Specification Fields** - Define your own spec attributes
- ✅ **4-Tier Hierarchy** - Category → Product → Type → Model
- ✅ **Spec Tables** - Clean, organized specification display
- ✅ **Bulk Operations** - Delete, move, duplicate multiple items
  - REST endpoints: `/bulk/delete`, `/bulk/move`, `/bulk/duplicate`

#### **Branding & Customization**
- ✅ **Company Logo Upload** - Display logo on all PDFs
  - Supports logo ID and URL storage
  - Admin page: Branding

- ✅ **Brand Color Presets**
  - Modern Blue (#0E45E9)
  - Architect Gray (#9AA0A6)
  - Engineering Bold (#0F5C2E)
  - Clean Violet (#7861FF)
  - Custom color picker
  - File: `Includes/branding-helpers.php:145-152`

- ✅ **Company Details**
  - Company name, address, phone, website
  - Settings migration from old to new format

- ✅ **Custom Footer Text** - Add notes to PDF footers
- ✅ **Smart Filenames** - Automatic project-based naming

#### **User Experience**
- ✅ **Simple Shortcode** - `[submittal_builder]` for any page
- ✅ **3-Step Workflow** - Products → Review → Generate
- ✅ **Responsive Design** - Desktop, tablet, mobile optimized
- ✅ **Local Autosave** - Browser localStorage persistence
  - Selections survive page refresh
  - No server storage required

- ✅ **Live Preview** - Real-time selected products display
- ✅ **Project Name Field** - Optional project naming
- ✅ **Selected Products Tray** - Sticky sidebar showing selections
  - File: `templates/frontend/partials/selected-tray.php`

- ✅ **Load Sample Catalog Modal** - Quick demo data loading from admin builder
  - Industry Pack selector (7 industry-specific catalogs)
  - Mode selection (Replace/Merge)
  - Size selection (Small/Medium/Large)
  - Optional sample branding
  - Remembers user's last selected pack
  - File: `assets/admin.js` (lines 2744-2763)

#### **Privacy & Security**
- ✅ **No Data Collection** - Zero external tracking
- ✅ **GDPR Compliant** - Full user control
- ✅ **Local Storage Only** - Free version uses browser only
- ✅ **Clean Uninstall** - Optional complete data removal
  - Setting: `sfb_remove_data_on_uninstall`

---

### Pro Features

#### **🤖 Automation**

**1. Auto Email Packet** (`auto_email`)
- Automatically email generated packets to recipients
- Includes tracking links
- Registry: `Includes/pro/registry.php:127`
- Implementation: `submittal-form-builder.php:6163`

**2. Auto Archive to History** (`archive`)
- Auto-archive packets by project name and date
- Server-side storage and organization
- Registry: `Includes/pro/registry.php:128`
- Implementation: `submittal-form-builder.php:6139`

#### **🎨 Branding**

**3. White-Label Output** (`white_label`)
- Remove plugin mentions for pure client branding
- Suppresses "Generated by..." footer text
- Registry: `Includes/pro/registry.php:130`
- Implementation: `submittal-form-builder.php:5988`

**4. Brand Themes** (`themes`)
- Architectural, Corporate, and Engineering PDF themes
- Custom accent colors applied to all PDF elements
- **Architectural:** Sky blue accent (#0ea5e9)
- **Corporate:** Green accent (#10b981)
- **Engineering:** Custom primary color from branding settings
- Affects table headers, section titles, borders, category badges
- Registry: `Includes/pro/registry.php:131`
- Implementation: `submittal-form-builder.php:5998` + ALL PDF templates
- Templates: `cover.html.php`, `toc.html.php`, `summary.html.php`, `model-sheet.html.php`

**5. PDF Watermark** (`watermark`)
- Apply custom text watermark to every page
- Semi-transparent (6% opacity), rotated -20 degrees
- Positioned center-screen on all PDF pages
- Text-based watermark (customizable via branding settings)
- Applied to: Cover, TOC, Summary, and all Model sheets
- Registry: `Includes/pro/registry.php:132`
- Implementation: `submittal-form-builder.php:6001` + ALL PDF templates
- Templates: `cover.html.php:17-24`, `toc.html.php:17-24`, `summary.html.php:19-36`, `model-sheet.html.php:22-29`

**6. Approval Signature Block** (`signature`)
- Approval/signature block with name, title, date
- For approval workflows and formal submittals
- Renders clean table with three fields:
  - **Approved By:** Name of approver
  - **Title:** Job title
  - **Date:** Approval date
- Positioned at bottom of each model sheet
- Registry: `Includes/pro/registry.php:133`
- Implementation: `submittal-form-builder.php:5993` + `templates/pdf/model-sheet.html.php:95-111`

#### **💾 Data**

**7. Shareable Drafts** (`server_drafts`)
- Save selections to server with short URL
- Share via private link across devices
- Auto-expire after 45 days (configurable 1-365 days)
- Registry: `Includes/pro/registry.php:135`
- Implementation: Multiple locations (`submittal-form-builder.php:2726, 2830, 2934, 6369, 6487`)
- Storage: Custom post type `sfb_draft` with post meta
- REST endpoints: `/drafts`, `/drafts/{id}` (GET, DELETE)
- **Note:** Uses custom post type (removed `wp_sfb_shares` table in v1.0.2)

**8. Lead Capture & CRM** (`lead_capture`)
- Modal to collect email/phone before PDF download
- **Frontend Features:**
  - ✨ **Real-time validation** - Submit disabled until email valid + consent checked (NEW 2025-10-11)
  - ✨ **Auto-populate project name** from Review page context (NEW 2025-10-11)
  - ✨ **Success message** with 1.5s delay before closing (NEW 2025-10-11)
  - Email validation and honeypot anti-bot
  - Consent checkbox tracking
  - Accessible form controls (aria-disabled)
- **Backend Features:**
  - UTM tracking (source, medium, campaign, term, content)
  - Rate limiting (5 submissions/hour per email/IP)
  - IP hashing for privacy (SHA-256)
  - BCC admin on lead emails
  - Auto-email to lead with PDF link
- **Admin Interface** (NEW 2025-10-11):
  - ✨ **Leads admin page** at Admin > Submittal Builder > Leads
  - ✨ **Lead table** with Date, Email, Phone, Project, Items, Category, Consent, UTM
  - ✨ **Search filter** (email, project, UTM data)
  - ✨ **Date range filters** (from/to)
  - ✨ **Pagination** (25 leads per page)
  - ✨ **CSV export** respecting current filters (runs in admin_init to prevent header errors)
  - ✨ **View details modal** showing full UTM + partial IP hash (8 chars)
  - ✨ **Stats summary** displaying total leads count
  - Menu only visible when Pro + feature enabled
- Files: `Includes/lead-capture.php`, `assets/js/lead-capture.js`, `submittal-form-builder.php` (admin page)
- Template: `templates/frontend/partials/modal-lead-capture.php`
- Database: `wp_sfb_leads` table
- Settings: `sfb_lead_capture_enabled`, `sfb_lead_bcc_admin`
- AJAX: `sfb_submit_lead`
- **Site Name Branding:** When enabled, title shows "{Site Name}'s Submittal & Spec Sheet Builder"
  - File: `templates/frontend/partials/header.php:16-22`
- **Recent Updates:**
  - Commit `0af2d3e`: UX enhancements to lead capture modal
  - Commit `7c2b44f`: Leads admin page with view and CSV export
  - Commit `d9a0bbb`: Fix CSV export headers already sent error

#### **📤 Distribution**

**9. Public Tracking Link** (`tracking`)
- **Automatically generates** tracking links when Auto-Email is enabled
- Works seamlessly with Lead Capture + Auto-Email workflow
- **Track downloads with full analytics:**
  - View count per link
  - Recipient email address
  - Last viewed timestamp
  - Detailed view history (timestamp, hashed IP, user agent)
  - Privacy-compliant (SHA-256 IP hashing)
- **Admin dashboard** at Admin > Tracking (Pro only)
  - Summary statistics (total links, views, averages)
  - Sortable table with project, recipient, dates, view counts
  - Visual indicators for engagement (green = viewed, gray = not viewed)
  - One-click URL copying
- **Business Use Case:** Monitor if customers/contractors opened emailed proposals
- Registry: `Includes/pro/registry.php:129`
- Implementation: `submittal-form-builder.php:6282-6299, 232-271`
- Admin page: `submittal-form-builder.php:2843-2953`
- Storage: `sfb_packets` option (array with view tracking data)
- **Documentation:** `docs/website/tracking.md` (comprehensive user guide)

---

### Agency Features (Agency License Only)

**Agency Library - Save as Pack** (NEW 2025-10-11)
- **Fast onboarding** for multi-site deployments
- **Save current catalog as reusable Pack:**
  - "💼 Save as Pack" button in Builder toolbar (Agency-gated)
  - Modal with name input and options:
    - Include branding settings (logo, colors, company info)
    - Include product notes/descriptions
  - Saves to `sfb_agency_packs` option with metadata
- **Agency Library page** (`admin.php?page=sfb-agency-library`)
  - Lists all saved Packs with name, product count, branding indicator, last updated
  - Export JSON button for each Pack (nonce-secured download)
  - Delete button with confirmation (removes from database)
  - Empty state with call-to-action to create first Pack
  - Info box explaining how to use Packs on other sites
- **Seeder integration:**
  - `/sfb/v1/form/seed` endpoint accepts `agency_pack_id` parameter
  - Loads Pack from database instead of industry pack JSON
  - Applies branding if Pack includes it and `with_branding` is true
  - Remaps node IDs on import to avoid conflicts
  - Works with existing replace/merge modes
- **Files:**
  - Admin menu: `Includes/class-sfb-admin.php:136-147`
  - Library page: `submittal-form-builder.php:2071-2212`
  - Save button: `assets/admin.js:2574-2578`
  - Modal UI: `assets/admin.js:2920-2998`
  - Save handler: `submittal-form-builder.php:6602-6683`
  - Export handler: `submittal-form-builder.php:5066-5107`
  - Seeder: `submittal-form-builder.php:6159-6260`
  - AJAX registration: `Includes/class-sfb-ajax.php:76-78`
- **Data Structure:**
  ```php
  [
    'id' => 'uuid',
    'name' => 'Pack Name',
    'counts' => ['products' => 123, 'nodes' => 456],
    'has_branding' => true/false,
    'updated_at' => '2025-10-11 12:34:56',
    'data' => [
      'form' => [...],
      'nodes' => [...],
      'branding' => [...] // optional
    ]
  ]
  ```
- **User Flow:**
  1. Create Pack: Builder → "💼 Save as Pack" → Enter name + options → Save
  2. Export JSON: Agency Library → Click "Export JSON" → Downloads `Pack-Name.json`
  3. Use on another site: Welcome → Load Sample Catalog → Upload JSON → Seeds catalog + branding
- **Security:**
  - All endpoints require `manage_options` + `sfb_is_agency_license()`
  - Nonce verification on export and delete
  - Input sanitization on all user data
  - Graceful degradation for non-Agency users (features hidden)

**Brand Presets** (Agency feature - implemented previously)
- Save multiple brand configurations as reusable presets (A, B, C, etc.)
- Apply presets to current branding with one click
- Set default preset for automatic use in Review + PDFs
- **Admin page:** Branding → Brand Presets section
- **Files:**
  - Backend: `Includes/class-sfb-branding.php` (preset CRUD methods)
  - Frontend: `assets/js/review.js:299-589` (preset switcher)
  - Template: `templates/frontend/builder.php:107-149` (data localization)
- **Storage:** `sfb_brand_presets` option

**Default-to-PDF Brand Preset** (Agency feature)
- **Status:** ✅ Implemented
- **Location:** Admin → Branding → "Use default preset automatically" toggle
- **What it does:** When enabled and a Default Preset is set, the Review preview and generated PDFs automatically use that preset's branding
- **Behavior:**
  - ✅ **Toggle ON + Default Preset set:** Review page and PDFs use default preset branding
  - ⚠️ **Toggle ON but no Default Preset:** Falls back to current branding settings
  - ❌ **Toggle OFF or Non-Agency:** No change to current behavior (uses current branding)
- **Use Cases:**
  - Agencies maintaining consistent branding across all client PDFs
  - Fast-switching between client brands without manual preset application
  - Ensuring every PDF matches the designated "primary" brand

**Review Screen Preset Switcher** (Agency feature)
- **Status:** ✅ Implemented (Phase C)
- **Location:** Review step (right sidebar)
- **What it does:** Lets Agency users preview different brand presets for the current session only
- **Important Behavior:**
  - 🔄 **Session-only:** Switcher uses `sessionStorage` (no database writes)
  - 💾 **Does not persist:** On page reload, reverts to Default Preset (if "Default-to-PDF" toggle is on) or current branding
  - 🎨 **Live preview:** Changes logo, colors, and company info in real-time on Review page
  - ⚙️ **"Apply as default" action:** Opens Branding → Presets page where admin must manually save
- **Use Cases:**
  - Quick visual comparison of multiple brand presets
  - Client approval workflows ("Which brand do you prefer?")
  - Testing preset appearance before making it default
- **Files:**
  - Frontend logic: `assets/js/review.js:299-589`
  - Session storage key: `sfb_session_preset`
  - Data localization: `templates/frontend/builder.php:107-149`

**Weekly Lead Export Scheduler** (NEW 2025-10-11)
- **Automated weekly email** delivery of new leads in CSV format
- **Settings UI** in Settings → Weekly Lead Export (Agency):
  - Enable/disable toggle
  - Recipient email address
  - Day of week selector (Monday-Sunday)
  - Time of day picker (respects site timezone)
  - 🔘 **"Send Now" button** - Manual trigger for immediate testing/QA
- **Cron job** scheduled weekly:
  - Respects site timezone
  - Auto-schedules when enabled
  - Auto-unschedules when disabled
  - Hook: `sfb_weekly_lead_export`
- **Duplicate prevention:**
  - New database column: `last_export_sent` in `wp_sfb_leads`
  - Only includes leads where `last_export_sent IS NULL`
  - Marks leads as sent after successful email
- **Email content:**
  - CSV attachment with all lead data
  - Filename: `sfb-leads-weekly-YYYY-MM-DD-HHmmss.csv`
  - Subject: `[Site Name] Weekly Lead Export - N New Leads`
  - Body includes summary, date range, generation timestamp
  - CSV columns match Leads page export
- **"Send Now" Button Details:**
  - **Purpose:** Immediate manual send for testing/validation without waiting for scheduled time
  - **What it does:** Sends CSV email with all new leads (those not previously exported)
  - **Use cases:**
    - QA: Verify configuration before waiting for cron
    - Testing: Confirm email delivery and CSV format
    - On-demand: Client requests immediate lead dump
  - **Behavior:** Uses same logic as scheduled cron (marks leads as sent to prevent duplicates)
  - **AJAX handler:** `sfb_weekly_lead_export_send_now`
  - **Security:** Nonce verification + `manage_options` capability
- **Files:**
  - Settings UI: `submittal-form-builder.php:2413-2568`
  - CSS styles: `submittal-form-builder.php:2759-2790`
  - Database schema: `submittal-form-builder.php:218-235`
  - Hook registration: `submittal-form-builder.php:119-122`
  - Core functions: `submittal-form-builder.php:4546-4757`
  - Cron scheduling: `submittal-form-builder.php:7608-7677`
- **Settings storage:**
  - `sfb_lead_weekly_export_enabled` (boolean)
  - `sfb_lead_weekly_export_email` (string)
  - `sfb_lead_weekly_export_day` (string, default: 'monday')
  - `sfb_lead_weekly_export_time` (string, default: '09:00')
- **Security:**
  - AJAX nonce verification for "Send Now"
  - Admin capability check (`manage_options`)
  - Agency license validation
  - Email format validation
- **User Flow:**
  1. Enable: Settings → Weekly Lead Export → Toggle "Enable weekly lead CSV email"
  2. Configure: Enter recipient email, select day/time
  3. Test: Click "Send Now" to verify configuration (includes only new leads since last export)
  4. Automated: Cron sends weekly emails with new leads only
  5. Monitor: Check email for weekly reports

**White-Label Mode** (NEW 2025-10-12)
- **Removes plugin branding** from PDFs, emails, and frontend
- **Settings UI** in Branding → White-Label Mode (Agency):
  - Enable/disable toggle
  - Custom footer text (replaces "Generated by..." text)
  - Email From Name (overrides WordPress default sender name)
  - Email From Address (overrides WordPress default sender email)
  - "Show subtle credit" toggle (optional "Powered by" credit)
- **Functionality:**
  - Removes plugin credit from all PDFs when enabled
  - Hides "Built with..." UI hints on frontend
  - Uses custom From name/address for all lead capture emails
  - Optional subtle "Powered by" credit (not shown on frontend)
  - Respects white-label settings across all email communications
- **Credit Behavior (3 modes):**
  - 🔴 **White-Label OFF:** Standard "Generated with Submittal & Spec Sheet Builder" credit shown everywhere
  - 🟡 **White-Label ON + Show subtle credit:** Tiny "Powered by Submittal & Spec Sheet Builder" in PDF footer and email footers only (never on frontend)
  - 🟢 **White-Label ON (no subtle credit):** No plugin credit shown anywhere
  - ✍️ **Custom footer:** When provided, replaces default credit text entirely (works in all modes)
- **Where Credits Appear:**
  - **Frontend:** "Built with..." tagline (removed when white-label ON)
  - **PDF Footer:** Bottom of every page (subtle credit appears here if enabled)
  - **Email Footer:** Lead capture emails (subtle credit appears here if enabled)
  - **Admin Pages:** Never affected (always shows plugin name)
- **Files:**
  - Settings UI: `submittal-form-builder.php:854-956`
  - Helper functions: `Includes/branding-helpers.php:207-308`
  - Email integration: `Includes/lead-capture.php:130-183`
  - CSS styles: `assets/admin.css:1622-1644`
  - JavaScript: `assets/admin.js:3560-3572`
- **Storage:**
  - `sfb_brand_settings['white_label']` array with enabled, custom_footer, email_from_name, email_from_address, show_subtle_credit

**Client Handoff Mode** (NEW 2025-10-12)
- **One-click toggle** to make sites safe for client use
- **Settings UI** in Agency → Client Handoff Mode:
  - Enable/disable toggle with instant effect
  - "What changes?" info box explaining feature hiding
  - Agency Packs section hidden when handoff mode ON
- **When Enabled:**
  - ❌ Hides Agency Library (Packs section)
  - ❌ Hides Brand Presets management panel
  - ✅ Client retains full access to Builder, Settings, Branding, Tracking, Leads
  - ✅ Frontend remains identical (no changes)
  - ✅ All data remains intact (reversible instantly)
- **Admin Banner:**
  - Blue banner shown on all plugin admin pages when handoff active
  - "Return to Agency Mode" button for quick toggle
  - Hidden on Agency page itself (toggle visible there)
- **Default Role Switching:**
  - When enabled: Saves current default role, switches to `sfb_operator`
  - When disabled: Restores previous default role
  - Logged for debugging
- **Files:**
  - Helper function: `Includes/branding-helpers.php:185-193`
  - Admin menu conditionals: `Includes/class-sfb-admin.php:69-79, 137-157`
  - Agency page: `submittal-form-builder.php:2186-2599`
  - Admin banner: `submittal-form-builder.php:6447-6486`
  - Role switching: `submittal-form-builder.php:2231-2279`
- **Storage:**
  - `sfb_client_handoff_mode` (boolean)
  - `sfb_handoff_previous_role` (string, temporary during handoff)

**Operator Role (capabilities)** (NEW 2025-10-12)
- **Custom WordPress role** for limited-access users
- **Role Slug:** `sfb_operator`
- **Display Name:** "Submittal Builder Operator"
- **Capabilities:**
  - ✅ `use_sfb_builder: true` - Can view and use the Builder
  - ✅ `view_sfb_leads: true` - Can view Leads page
  - ✅ `view_sfb_tracking: true` - Can view Tracking page
  - ❌ `edit_sfb_branding: false` - Cannot edit branding settings
  - ❌ `edit_sfb_catalog: false` - Cannot edit catalog (add/edit/delete products)
  - ❌ `access_sfb_agency: false` - Cannot access Agency Library, Presets, or Settings
- **Capability Enforcement:**
  - Custom `map_meta_cap` filter enforces capabilities across WordPress
  - All catalog write operations require `edit_sfb_catalog`
  - Branding save requires `edit_sfb_branding`
  - Agency features require `access_sfb_agency`
  - REST API endpoints check capabilities
  - AJAX handlers check capabilities
  - Admins always have access unless explicitly denied
- **User Management Tool:**
  - "Assign Operator Role" interface on Agency page
  - Multi-select checkbox UI showing all non-admin users
  - Shows current role for each user
  - Form handler processes bulk role assignments
  - Success notice after saving
- **Integration with Handoff Mode:**
  - When Handoff Mode enabled: Default role switches to `sfb_operator`
  - New user registrations automatically get Operator role during handoff
  - When Handoff Mode disabled: Default role restored
- **Files:**
  - Role creation: `submittal-form-builder.php:171-195`
  - Capability mapping: `submittal-form-builder.php:6488-6543`
  - REST capability checks: `Includes/class-sfb-rest.php:57-63, 88-119, 131-150`
  - AJAX capability checks: `submittal-form-builder.php:5747-5749, 5782`
  - Agency capability checks: `Includes/class-sfb-branding.php:305, 337, 358, 387, 417, 446`
  - User assignment UI: `submittal-form-builder.php:2372-2433`
  - Assignment handler: `submittal-form-builder.php:2223-2245`

**Agency Analytics (Light Roll-Up)** (NEW 2025-10-12)
- **Lightweight analytics** for monitoring activity across agency sites
- **Admin Page:** Agency Analytics (`admin.php?page=sfb-agency-analytics`)
  - Agency-only, requires `manage_options` capability
  - Date range filter (7/30/90 days)
  - Stats cards: PDFs Generated, Leads Captured, Last Heartbeat
  - Current site info table: Site URL, Plugin Version, Site ID (hashed)
  - Top 5 Products chips with selection counts
  - Privacy notice (counts only, no PII)
- **Data Collection:**
  - **PDF generation events**: Tracks product IDs and counts
  - **Lead capture events**: Tracks counts only (no email/phone)
  - **Daily heartbeat**: Version, PHP version, WP version, timestamp
  - All stored locally in `wp_sfb_analytics_events` table
  - Optional remote aggregation (non-blocking, fails silently)
- **Remote Aggregation (Optional):**
  - Sends to license server if `aggregator_url` configured
  - Non-blocking async calls (2s timeout for events, 5s for heartbeat)
  - Payload includes: site_id (hashed), site_url, version, event type, counts
  - Authenticated with license key in headers
  - Zero impact if offline or unconfigured
- **Local Fallback:**
  - Works perfectly on single installs without remote aggregator
  - All analytics stored locally in database table
  - Query by date range for reporting
  - Top products calculated from PDF event data
- **Privacy & Security:**
  - ✅ No PII transmitted (lead emails/phones not sent remotely)
  - ✅ Only counts and product names/IDs sent
  - ✅ Site ID hashed (SHA-256 of site URL)
  - ✅ IP addresses hashed for local storage
  - ✅ Non-blocking remote calls (fails silently)
- **Files:**
  - Analytics class: `Includes/agency-analytics.php`
  - Admin menu: `Includes/class-sfb-admin.php:148-157`
  - Page renderer: `submittal-form-builder.php:3793-3965`
  - PDF tracking: `submittal-form-builder.php:5807-5810`
  - Lead tracking: `Includes/lead-capture.php:99-106`
  - Initialization: `submittal-form-builder.php:38, 9213`
- **Database Table:** `wp_sfb_analytics_events`
  - Columns: id, site_id, event_type, event_data (JSON), created_at
  - Indexes: site_id, event_type, created_at
- **Storage:**
  - Events stored in `wp_sfb_analytics_events` table
  - Cron job: `sfb_analytics_heartbeat` (daily)

**Advanced Lead Routing** (NEW 2025-10-12)
- **Status:** ✅ Implemented
- **Location:** Admin → Agency Settings → "Advanced Lead Routing"
- **What it does:** Automatically routes newly captured leads to email recipients and/or a generic webhook (Zapier/Make compatible) based on rules
- **Rule Conditions (OR logic within a rule; first-match wins):**
  - **Email domain contains:** e.g., `acme.com` (comma-separated tokens)
  - **UTM contains:** source / medium / campaign (comma-separated tokens, case-insensitive)
  - **Top Category equals:** exact match on lead's top category
- **Actions per rule:**
  - **Email to:** comma-separated recipients (validated)
  - **Webhook URL:** HTTPS only, JSON POST
- **Fallback route:** optional email + webhook if no rule matches
- **Retries & logging:**
  - Webhooks retry 3× with exponential backoff (≈30s, 2m, 10m) via `wp_cron`
  - De-duplication per lead ID (no double sends)
  - Delivery log (last 20) with success/failure + HTTP code
  - "Test" a rule against the last lead from UI
- **Security & gating:**
  - Agency-only UI and processing
  - Nonces + `manage_options` on all AJAX operations
  - HTTPS enforced for webhooks
- **Webhook Payload Example:**
  ```json
  {
    "event": "lead.captured",
    "site": {
      "url": "https://example.com",
      "name": "Example",
      "plugin_version": "1.0.0"
    },
    "lead": {
      "id": 123,
      "created_at": "2025-10-12T14:30:00Z",
      "email": "jane@acme.com",
      "phone": "+1-555-123-4567",
      "project_name": "East Wing",
      "num_items": 14,
      "top_category": "Shaftwall",
      "utm": {
        "source": "google",
        "medium": "cpc",
        "campaign": "fall_promo",
        "term": "",
        "content": ""
      }
    },
    "routing": {
      "rule_name": "Acme Domains",
      "matched": true
    }
  }
  ```
- **Files:**
  - Routing logic: `Includes/agency-lead-routing.php`
  - Admin UI: `assets/js/lead-routing.js`
  - Integration: `Includes/lead-capture.php` (triggers routing on lead capture)
  - Settings storage: `sfb_lead_routing_rules` option

---

## Admin Features

### Admin Menu Pages

1. **Submittal Builder** (main catalog page)
   - Function: `render_builder_page()`
   - Capability: `manage_options`
   - Icon: `dashicons-category`
   - Menu position: 56

2. **Welcome to Submittal Builder** (onboarding)
   - Function: `render_onboarding_page()`
   - Slug: `sfb-onboarding`
   - First-run intro and setup wizard
   - Template: `templates/admin/onboarding.php`
   - Dismissible via AJAX: `sfb_dismiss_welcome`

3. **Tools**
   - Function: `render_tools_page()`
   - Slug: `sfb-tools`
   - Features:
     - Purge expired drafts (AJAX: `sfb_purge_expired_drafts`)
     - Run smoke test (AJAX: `sfb_run_smoke_test`)
     - Database maintenance utilities

4. **Demo Tools** (dev mode only)
   - Function: `render_demo_tools_page()`
   - Slug: `sfb-demo-tools`
   - Only visible when `SFB_DEV_MODE` constant is true
   - Features:
     - Industry Pack selector (Electrical, HVAC, Plumbing, Steel, Fasteners, Finishes, Generic Equipment)
     - Demo size selection (Small, Medium, Large)
     - Branding toggle
     - Draft creation option
     - Uses centralized industry pack registry (`Includes/industry-pack-helpers.php`)
   - Testing utilities for development

5. **Settings**
   - Function: `render_settings_page()`
   - Slug: `sfb-settings`
   - Sub-sections:
     - Drafts (autosave, server drafts, expiry, rate limiting)
     - License Behavior (auto-deactivate, data removal)
     - External Links (customizable URLs)
     - Lead Capture (toggle, BCC admin)

6. **Branding**
   - Function: `render_branding_page()`
   - Slug: `sfb-branding`
   - Configure logo, colors, company info
   - AJAX save: `sfb_save_brand`
   - Storage: `sfb_brand_settings` option

7. **Upgrade to Pro** / **License & Support** / **Manage License** (adaptive)
   - Changes based on license state:
     - **No license:** "⭐ Upgrade" → `render_upgrade_page()`
       - Template: `templates/admin/upgrade.php`
     - **Active license:** "License & Support" → `render_license_support_page()`
       - Template: `templates/admin/license-support.php`
     - **Expired/Invalid:** "Manage License" → `render_license_management_page()`
       - Template: `templates/admin/license-management.php`

### Admin Capabilities

- **Catalog Import/Export**
  - REST: `/form/{id}/export` - Export catalog as JSON
  - REST: `/form/import` - Import catalog from JSON

- **Catalog Management**
  - REST: `/form/seed` - Seed demo data from industry-specific packs
    - Supports `industry_pack` parameter (electrical, hvac, plumbing, steel, fasteners, finishes, generic-equipment)
    - Loads from JSON files in `assets/demo/*.json`
    - Persists user's last selected pack in user meta (`sfb_last_industry_pack`)
  - REST: `/form/wipe` - Delete all catalog data
  - REST: `/node/create`, `/node/save`, `/node/delete`
  - REST: `/node/reorder`, `/node/duplicate`, `/node/move`
  - REST: `/node/history` - View node change history

- **Bulk Operations**
  - REST: `/bulk/delete`, `/bulk/move`, `/bulk/duplicate`, `/bulk/export`

- **Lead Management** (Pro)
  - View all leads: `SFB_Lead_Capture::get_leads()`
  - Total count: `SFB_Lead_Capture::get_total_leads()`
  - CSV export: `SFB_Lead_Capture::export_csv()`
  - Database: `wp_sfb_leads` table

---

## Database Tables

### 1. `wp_sfb_forms`
**Purpose:** Store submittal forms/catalogs (usually just 1 per site)

**Schema:**
```sql
CREATE TABLE wp_sfb_forms (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(190) NOT NULL,
  settings_json LONGTEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
)
```

**File:** `submittal-form-builder.php:166-174`

---

### 2. `wp_sfb_nodes`
**Purpose:** Store hierarchical catalog structure (categories, products, types, models)

**Schema:**
```sql
CREATE TABLE wp_sfb_nodes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  form_id BIGINT UNSIGNED NOT NULL,
  parent_id BIGINT UNSIGNED NULL,
  node_type ENUM('category','product','type','model') NOT NULL,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NULL,
  position INT UNSIGNED DEFAULT 0,
  settings_json LONGTEXT NULL,  -- Stores specs, images, etc.
  PRIMARY KEY (id),
  KEY form_id (form_id),
  KEY parent_id (parent_id),
  KEY node_type (node_type),
  KEY form_parent_pos (form_id, parent_id, position),
  KEY form_type (form_id, node_type)
)
```

**File:** `submittal-form-builder.php:182-198`

**Node Types:**
- `category` - Top-level grouping
- `product` - Product within category
- `type` - Product variation/type
- `model` - Specific model with full specs

---

### 3. `wp_sfb_leads` (Pro: Lead Capture)
**Purpose:** Store captured leads (email/phone) before PDF download

**Schema:**
```sql
CREATE TABLE wp_sfb_leads (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(50) NULL,
  project_name VARCHAR(190) NULL,
  num_items INT UNSIGNED DEFAULT 0,     -- Number of products selected
  top_category VARCHAR(190) NULL,        -- Most common category
  consent TINYINT(1) DEFAULT 0,          -- Marketing consent checkbox
  utm_json TEXT NULL,                    -- UTM tracking params (JSON)
  ip_hash VARCHAR(64) NULL,              -- SHA-256 hashed IP (privacy)
  PRIMARY KEY (id),
  KEY email (email),
  KEY created_at (created_at),
  KEY ip_hash (ip_hash)
)
```

**File:** `submittal-form-builder.php:225-241`

**Privacy Features:**
- IP hashing (SHA-256) instead of raw IP storage
- UTM data stored as JSON: `{source, medium, campaign, term, content}`
- Rate limiting: 5 submissions/hour per email OR IP hash

---

### 4. `wp_sfb_analytics_events` (Agency: Analytics) **(NEW 2025-10-12)**
**Purpose:** Store analytics events for Agency dashboards

**Schema:**
```sql
CREATE TABLE wp_sfb_analytics_events (
  id BIGINT(20) NOT NULL AUTO_INCREMENT,
  site_id VARCHAR(64) NOT NULL,           -- SHA-256 hash of site URL
  event_type VARCHAR(50) NOT NULL,        -- pdf_generated, lead_created, heartbeat
  event_data LONGTEXT NULL,               -- JSON payload with event details
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY site_id (site_id),
  KEY event_type (event_type),
  KEY created_at (created_at)
)
```

**File:** `Includes/agency-analytics.php:292-311`

**Event Types:**
- `pdf_generated` - Tracks PDF generation with product IDs and counts
- `lead_created` - Tracks lead capture events (counts only, no PII)
- `heartbeat` - Daily ping with version, PHP version, WP version

**Privacy Features:**
- Site ID hashed (SHA-256) for privacy
- No PII stored (lead emails/phones not included)
- Only counts and product IDs tracked
- Local storage with optional remote aggregation

---

## REST API Endpoints

**Namespace:** `sfb/v1`
**Total Endpoints:** 27

### Health/Status
- `GET /health` - Basic health check
- `GET /ping` - Ping/pong test
- `GET /status` - Check draft status

### Catalog Management (Admin)
- `GET /form/{id}` - Get full catalog for display
- `POST /form/seed` - Seed demo data
- `POST /form/wipe` - Delete all catalog data
- `GET /form/{id}/export` - Export catalog as JSON
- `POST /form/import` - Import catalog from JSON

### Node Operations (Admin)
- `POST /node/save` - Create or update node
- `POST /node/create` - Create new node
- `POST /node/delete` - Delete node
- `POST /node/reorder` - Change node position
- `POST /node/duplicate` - Duplicate node
- `POST /node/move` - Move node to different parent
- `GET /node/history` - View node change history

### Bulk Operations (Admin)
- `POST /bulk/delete` - Delete multiple nodes
- `POST /bulk/move` - Move multiple nodes
- `POST /bulk/duplicate` - Duplicate multiple nodes
- `POST /bulk/export` - Export multiple nodes

### PDF Generation
- `POST /generate` - Generate PDF packet from selections

### Drafts (Pro)
- `POST /drafts` - Create new server draft
- `GET /drafts/{id}` - Load draft by token
- `DELETE /drafts/{id}` - Delete draft

### Settings & License
- `GET /settings` - Get branding/settings for frontend
- `POST /settings` - Update settings (admin)
- `POST /license` - Activate/deactivate license
- `GET /license` - Check license status

**File:** `submittal-form-builder.php:4844-5006`

---

## AJAX Endpoints

**All require nonce verification unless marked `nopriv`**

### Admin Operations
- `sfb_dismiss_welcome` - Dismiss onboarding notice
  - Handler: `dismiss_welcome_notice()`

- `sfb_purge_expired_drafts` - Manually purge old drafts
  - Handler: `ajax_purge_expired_drafts()`
  - Nonce: `sfb_purge`

- `sfb_run_smoke_test` - Run diagnostic smoke test
  - Handler: `ajax_run_smoke_test()`
  - Nonce: `sfb_smoke`

- `sfb_save_brand` - Save branding settings
  - Handler: `ajax_save_brand()`
  - Nonce: `sfb_frontend`

### Frontend Operations (Public)
- `sfb_list_products` - Get product catalog
  - Handler: `ajax_list_products()`
  - Allows: `nopriv` (public access)

- `sfb_generate_frontend_pdf` - Generate PDF from frontend
  - Handler: `ajax_generate_frontend_pdf()`
  - Allows: `nopriv` (public access)

### Pro Features
- `sfb_submit_lead` - Submit lead capture form
  - Handler: `SFB_Lead_Capture::ajax_submit_lead()`
  - Allows: `nopriv` (public access)
  - Nonce: `sfb_frontend_builder`

**File:** `submittal-form-builder.php:99-119`

---

## Admin Pages

### Main Pages (Top-Level Menu)

1. **Submittal Builder** (`admin.php?page=sfb`)
   - Catalog/product management interface
   - Hierarchical tree view
   - Drag-and-drop reordering
   - Inline editing

2. **Branding** (`admin.php?page=sfb-branding`)
   - Logo upload (media library)
   - Color picker with presets
   - Company info fields
   - Footer text customization
   - Brand Presets section (Agency only)

2.5 **Agency** (`admin.php?page=sfb-agency`) **(Agency only - NEW 2025-10-12)**
   - Consolidated Agency dashboard combining settings and library
   - **Client Handoff Mode** toggle at top (instant toggle between Agency/Client modes)
   - **Agency Packs** section (hidden when handoff mode enabled):
     - View all saved Agency Packs
     - Pack table with name, products, branding, updated date
     - Export JSON for each Pack
     - Delete Packs with confirmation
     - Empty state with "Go to Builder" CTA
   - **Assign Operator Role** tool for user management
   - Only visible to Agency license holders
   - Replaces separate Agency Library and Agency Settings pages

2.6 **Agency Analytics** (`admin.php?page=sfb-agency-analytics`) **(Agency only - NEW 2025-10-12)**
   - Lightweight analytics dashboard for monitoring activity
   - Date range filter (7/30/90 days)
   - Stats cards: PDFs Generated, Leads Captured, Last Heartbeat
   - Current site info table: Site URL, Plugin Version, Site ID (hashed)
   - Top 5 Products chips with selection counts
   - Privacy notice (counts only, no PII)
   - Only visible to Agency license holders

3. **Settings** (`admin.php?page=sfb-settings`)
   - Draft configuration
   - License behavior
   - External links
   - Lead capture settings

4. **Tools** (`admin.php?page=sfb-tools`)
   - Purge expired drafts
   - Run smoke tests
   - Database maintenance

4.5 **Tracking** (`admin.php?page=sfb-tracking`) **(Pro only)**
   - View all tracking links
   - Summary statistics (total links, views, averages)
   - Sortable table with project names, dates, view counts
   - One-click URL copying
   - Visual engagement indicators

4.6 **Leads** (`admin.php?page=sfb-leads`) **(Pro only - NEW 2025-10-11)**
   - View all captured leads in table format
   - Search filter (email, project, UTM)
   - Date range filters (from/to)
   - Pagination (25 leads per page)
   - CSV export respecting filters
   - View details modal (full UTM + partial IP hash)
   - Stats summary (total leads count)
   - Only visible when `sfb_lead_capture_enabled` is true

5. **Upgrade/License** (adaptive)
   - Free: Upgrade screen with feature comparison
   - Pro: License management and support hub

### Hidden/Conditional Pages

6. **Welcome to Submittal Builder** (`admin.php?page=sfb-onboarding`)
   - Only shown on first activation
   - Can be dismissed
   - Setup wizard

7. **Demo Tools** (`admin.php?page=sfb-demo-tools`)
   - Only visible when `define('SFB_DEV_MODE', true);`
   - Testing and debugging utilities

---

## Templates

### Frontend Templates

**Main Builder:**
- `templates/frontend/builder.php` - Main 3-step builder wrapper
- `templates/frontend-builder.php` - Alternative/deprecated(?)

**Partials:**
- `templates/frontend/partials/header.php` - Progress stepper header
- `templates/frontend/partials/step-products.php` - Product selection (Step 1)
- `templates/frontend/partials/step-review.php` - Review selections (Step 2)
- `templates/frontend/partials/step-generate.php` - Generate PDF (Step 3)
- `templates/frontend/partials/selected-tray.php` - Sticky selection sidebar
- `templates/frontend/partials/modal-lead-capture.php` - Lead capture modal (Pro)

**Scripts:**
- `assets/js/frontend.js` - Main builder JavaScript
- `assets/js/review.js` - Review step functionality
- `assets/js/lead-capture.js` - Lead capture modal (Pro)

**Styles:**
- `assets/css/frontend.css` - All frontend styles

---

### PDF Templates

All templates use PHP with inline CSS for PDF generation via DomPDF:

- `templates/pdf/cover.html.php` - Cover page
- `templates/pdf/summary.html.php` - Summary page (grouped by category)
- `templates/pdf/toc.html.php` - Table of contents
- `templates/pdf/model-sheet.html.php` - Individual product spec sheets

**Generator:** `Includes/pdf-generator.php` (Professional PDF generator class)

---

### Admin Templates

- `templates/admin/onboarding.php` - Welcome/setup wizard
- `templates/admin/upgrade.php` - Pro feature showcase (free users)
- `templates/admin/license-support.php` - License & support hub (Pro users)
- `templates/admin/license-management.php` - License renewal (expired users)

**Shared:**
- `templates/tooltip.php` - Reusable tooltip component

---

### Marketing Templates

- `templates/marketing/landing.php` - Optional landing page template

---

## Technical Infrastructure

### Core Files

**Main Plugin File:**
- `submittal-form-builder.php` - Main class, hooks, admin pages, REST/AJAX handlers

**Pro Features:**
- `Includes/pro/registry.php` - Feature registry, license gating
  - Functions: `sfb_features()`, `sfb_feature_enabled()`, `sfb_is_pro_active()`

**Admin Infrastructure:**
- `Includes/admin/links.php` - Centralized external link management
  - Function: `sfb_get_links()`, `sfb_get_link()`
  - Configurable URLs for account, docs, support, etc.

- `Includes/admin/license-api.php` - WooCommerce Software API integration
  - License activation/deactivation/validation
  - 12-hour caching with graceful fallback
  - Functions: `sfb_activate_license()`, `sfb_check_license_status()`

**Branding:**
- `Includes/branding-helpers.php` - Brand settings management
  - Functions: `sfb_get_brand_settings()`, `sfb_brand_presets()`
  - Migration from old `sfb_branding` to new `sfb_brand_settings` format

**Industry Packs:**
- `Includes/industry-pack-helpers.php` - Industry pack registry (Single source of truth)
  - Functions: `sfb_get_industry_packs()`, `sfb_get_default_industry_pack()`, `sfb_get_user_last_industry_pack()`, `sfb_save_user_last_industry_pack()`
  - Scans `assets/demo/*.json` files
  - Shared between Demo Tools panel and Builder modal
  - User preference persistence via user meta

**PDF Generation:**
- `Includes/pdf-generator.php` - Professional PDF generator
  - Uses DomPDF library
  - Handles cover, summary, TOC, spec sheets
  - Pro theme support

**Lead Capture (Pro):**
- `Includes/lead-capture.php` - Lead capture backend
  - Class: `SFB_Lead_Capture`
  - Methods: `is_enabled()`, `ajax_submit_lead()`, `get_leads()`, `export_csv()`

**Agency Analytics (Agency):**
- `Includes/agency-analytics.php` - Analytics event tracking and reporting
  - Class: `SFB_Agency_Analytics`
  - Methods: `init()`, `get_site_id()`, `track_pdf_generated()`, `track_lead_captured()`, `store_local_event()`, `send_remote_event()`, `schedule_heartbeat()`, `send_heartbeat()`, `get_analytics()`, `ensure_analytics_table()`

**Operator Role & Capabilities (Agency):**
- Role creation in `submittal-form-builder.php:171-195`
- Capability mapping in `submittal-form-builder.php:6488-6543`
- Custom capabilities: `use_sfb_builder`, `view_sfb_leads`, `view_sfb_tracking`, `edit_sfb_branding`, `edit_sfb_catalog`, `access_sfb_agency`

### Placeholder/Stub Files

These files exist but are empty (1 line each):
- `Includes/class-sfb-share.php` - Reserved for future use
- `Includes/class-sfb-permissions.php` - Reserved for future use
- `Includes/class-sfb-render.php` - Reserved for future use
- `Includes/class-sfb-pdf.php` - Reserved for future use
- `Includes/class-sfb-activator.php` - Reserved for future use
- `Includes/class-sfb-admin.php` - Reserved for future use
- `Includes/class-sfb-rest.php` - Reserved for future use
- `Includes/helpers.php` - Reserved for future use

**Note:** These are likely placeholders for future modularization. Current functionality is in main plugin file.

---

### External Libraries

**DomPDF** (included):
- Location: `dompdf/` directory
- Purpose: HTML-to-PDF conversion
- License: LGPL 2.1
- No external dependencies required

---

### Demo Data (Industry Packs)

**Available Industry Packs:**
- `assets/demo/electrical.json` - Electrical — Panels & Conduit
- `assets/demo/hvac.json` - HVAC — Duct & Diffusers
- `assets/demo/plumbing.json` - Plumbing — Pipes & Fixtures
- `assets/demo/steel.json` - Structural Steel — Beams & Columns
- `assets/demo/fasteners.json` - Fasteners & Hardware
- `assets/demo/finishes.json` - Finishes & Coatings
- `assets/demo/generic-equipment.json` - Generic Equipment

**Pack Structure:**
Each JSON file contains:
- `title` - Display name for the pack
- `categories[]` - Array of product categories
  - `title` - Category name
  - `types[]` - Array of product types
    - `title` - Type name
    - `items[]` - Array of specific models
      - `title` - Model name
      - `meta{}` - Specification key-value pairs

**Registry Management:**
- File: `Includes/industry-pack-helpers.php`
- Dynamic scanning of `assets/demo/*.json` files
- No hardcoded pack lists (fully extensible)

---

## Settings & Configuration

### WordPress Options

**Brand Settings:**
- `sfb_brand_settings` (new format, v1.1.0+)
  ```php
  [
    'company' => [
      'name' => '',
      'address' => '',
      'phone' => '',
      'website' => '',
      'logo_id' => 0,
      'logo_url' => ''
    ],
    'visual' => [
      'primary_color' => '#0E45E9',
      'include_cover' => true,
      'footer_text' => '',
      'preset_key' => 'modern_blue'
    ],
    '_meta' => [
      'version' => '1.1.0',
      'updated_at' => ''
    ]
  ]
  ```

- `sfb_branding` (deprecated, auto-migrates)

**Settings:**
- `sfb_settings` - General plugin settings
- `sfb_auto_deactivate_on_deactivate` (bool) - Auto-deactivate license on plugin deactivation
- `sfb_remove_data_on_uninstall` (bool) - Remove all data when plugin is deleted

**Draft Settings:**
- `sfb_drafts_enabled` - Enable server drafts (Pro)
- `sfb_drafts_expiry_days` - Days until drafts expire (default 45)
- `sfb_drafts_rate_limit` - Max drafts per hour
- `sfb_drafts_privacy_note` - Privacy notice text

**Lead Capture (Pro):**
- `sfb_lead_capture_enabled` (bool) - Enable lead capture modal
- `sfb_lead_bcc_admin` (bool) - BCC site admin on lead emails

**Agency Features (Agency):**
- `sfb_agency_packs` (array) - Saved Agency Packs with metadata
- `sfb_brand_presets` (array) - Brand preset configurations
- `sfb_client_handoff_mode` (bool) - Enable Client Handoff Mode
- `sfb_handoff_previous_role` (string) - Temporary storage of previous default role during handoff
- `sfb_lead_weekly_export_enabled` (bool) - Enable weekly lead CSV email
- `sfb_lead_weekly_export_email` (string) - Recipient email address
- `sfb_lead_weekly_export_day` (string) - Day of week (default: 'monday')
- `sfb_lead_weekly_export_time` (string) - Time of day (default: '09:00')

**License:**
- `sfb_license_data` - Current license data
  ```php
  [
    'key' => '',
    'email' => '',
    'status' => 'inactive', // active, expired, invalid, inactive
    'expires' => '',
    'last_check' => 0,
    'error' => '',
    'activations_remaining' => null,
    'instance' => ''
  ]
  ```

**Tracking/Packets:**
- `sfb_packets` - Array of tracking links and packet metadata

**Transients:**
- `sfb_license_check_cache` - 12-hour cache of license validation

**User Meta:**
- `sfb_last_industry_pack` - User's last selected industry pack (for persistence across sessions)

**External Links (Customizable):**
- `sfb_link_account`
- `sfb_link_invoices`
- `sfb_link_docs`
- `sfb_link_tutorials`
- `sfb_link_roadmap`
- `sfb_link_support`
- `sfb_link_renew`
- `sfb_link_pricing`
- `sfb_link_agency_license`
- `sfb_link_single_license`

---

### PHP Constants

**License API:**
- `SFB_LICENSE_API_URL` - WooCommerce Software API endpoint
- `SFB_LICENSE_PRODUCT_ID` - Product identifier
- `SFB_LICENSE_VERSION` - Current plugin version

**Pro Registry:**
- `SFB_PRO_REGISTRY_VERSION` - Registry version (1.0.0)

**Development:**
- `SFB_PRO_DEV` (bool) - Bypass license check (dev mode)
- `SFB_DEV_MODE` (bool) - Show Demo Tools page

**PDF:**
- `DOMPDF_ENABLE_REMOTE` (bool) - Allow remote font loading (can be disabled for privacy)

---

## License Tiers

### Free Version
- **Price:** $0 (forever)
- **Features:** All core features (see [Free Features](#free-features))
- **Support:** Community forums
- **Updates:** Free forever
- **Limitations:** None - fully functional
- **Best For:** Individual users, small teams, occasional use

---

### Pro - Single Site
- **Price:** $69/year
- **Features:** All free features + 9 Pro features
- **Sites:** 1 WordPress installation
- **Support:** Priority email support
- **Updates:** 1 year included
- **Best For:** Individual businesses, single websites

**Included Pro Features:**
1. Auto Email Packet
2. Auto Archive to History
3. White-Label Output
4. Brand Themes (Arch/Corp/Eng)
5. PDF Watermark
6. Approval Signature Block
7. Shareable Drafts
8. Lead Capture & CRM
9. Public Tracking Links

---

### Pro - Agency License
- **Price:** $149/year
- **Features:** Same as Single Site Pro
- **Sites:** Unlimited client installations
- **Support:** Priority email support
- **Updates:** 1 year included
- **Best For:** Agencies, developers, consultants managing multiple client sites

**Note:** Agency is a **licensing tier**, not a feature tier. Both Pro licenses unlock identical features.

---

## Summary Statistics

### Features
- **Free Features:** 11 core features
- **Pro Features:** 9 premium features
- **Total End-User Features:** 20

### Infrastructure
- **Database Tables:** 4 (wp_sfb_forms, wp_sfb_nodes, wp_sfb_leads, wp_sfb_analytics_events)
- **Custom Post Types:** 1 (sfb_draft for shareable drafts)
- **REST Endpoints:** 27
- **AJAX Endpoints:** 7
- **Admin Pages:** 11 (9 main + 2 conditional, including Pro-only Tracking & Leads, Agency-only Agency & Analytics)
- **Frontend Templates:** 10 files
- **PDF Templates:** 4 files
- **Admin Templates:** 4 files

### Code Organization
- **Main Plugin File:** 7,400+ lines
- **Include Files:** 10+ files
- **Template Files:** 18+ files
- **JavaScript Files:** 3 files
- **CSS Files:** 1 file (comprehensive)

---

## Feature Status Summary

### All Pro Features Registered ✅

All 9 Pro features are properly registered in `Includes/pro/registry.php`:

1. ✅ Auto Email Packet
2. ✅ Auto Archive to History
3. ✅ White-Label Output
4. ✅ Brand Themes
5. ✅ PDF Watermark
6. ✅ Approval Signature Block
7. ✅ Shareable Drafts
8. ✅ Lead Capture & CRM (Added 2025-10-10)
9. ✅ Public Tracking Link (Full analytics implemented 2025-10-10)

**Verification Status:**
- ✅ **9/9 Fully Implemented & Verified (100%)** - All Pro features tested and production-ready

---

## Next Steps / Recommendations

1. ✅ **~~Add Lead Capture to Registry~~** - **COMPLETED 2025-10-10**
2. **Document Agency-Specific Features** - Consider adding multi-client management tools
3. **Populate Stub Files** - Move code from main file into modular class files for better organization
4. **Create Marketing Materials** - Use this inventory to create sales pages, comparison charts
5. **API Documentation** - Expand REST endpoint documentation with request/response examples
6. **Feature Screenshots** - Create visuals for each Pro feature for marketing
7. **Tutorial Videos** - Record quick demos for complex features (drafts, lead capture, tracking)

---

**Document Maintained By:** Claude Code
**Plugin Version:** 1.0.2
**Registry Version:** 1.0.0
