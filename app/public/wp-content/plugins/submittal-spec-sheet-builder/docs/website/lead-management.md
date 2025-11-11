# Lead Management (Pro) - Documentation

**Elementor-ready structure for lead capture, tracking, and routing**

**URL:** https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/documentation/lead-management/

---

## Page Structure

---

## Section 1: Hero

**Heading:** Lead Management & Capture
**Subheading:** Turn PDF downloads into qualified sales leads (Pro & Agency)

---

## Section 2: Overview

### What is Lead Capture?

Lead Capture is a **Pro feature** that collects contact information from visitors before they download PDF submittal packets. Instead of anonymous downloads, you'll know exactly who's interested in your products.

**How it works:**
1. Customer selects products on your builder page
2. Clicks "Generate PDF"
3. **Modal appears** asking for email and phone
4. Customer enters info and submits
5. PDF is generated and downloaded
6. **You receive email notification** with lead details
7. Lead is saved to your database for follow-up

**Perfect for:**
- Distributors who want to know who's browsing their catalog
- Sales teams who need qualified leads
- Businesses tracking marketing ROI
- Anyone who wants to follow up with potential customers

---

## Section 3: Getting Started (3-Step Setup)

### Step 1: Enable Lead Capture

**Path:** `Submittal Builder → Settings → Lead Capture`

1. Toggle **"Enable Lead Capture"** to ON
2. Set **Notification Email** (where you'll receive alerts)
   - Default: Your WordPress admin email
   - Can use multiple emails: `sales@company.com, manager@company.com`
3. Click **"Save Changes"**

**That's it!** The lead capture modal now appears before PDF downloads.

---

### Step 2: Test It

1. Visit your builder page on the frontend
2. Select a few products
3. Click "Generate PDF"
4. **Lead capture modal should appear**
5. Fill in your email and phone
6. Submit the form
7. Check your notification email inbox

**Expected result:**
- You receive an email with lead details
- Lead appears in `Submittal Builder → Leads` page
- PDF downloads automatically after form submission

---

### Step 3: Review Your Leads

**Path:** `Submittal Builder → Leads`

You'll see a table with:
- Email address
- Phone number
- Project name
- Number of products selected
- Top category (most-selected product category)
- Date captured
- UTM tracking data (if visitor came from marketing campaigns)

**Actions:**
- **Export to CSV** - Download all leads for import to your CRM
- **View details** - Click on any lead to see full information
- **Reply directly** - Click email address to send a follow-up

---

## Section 4: Lead Capture Features (Card Grid - 2x3)

### Card 1: Email Notifications

**Icon:** 📧

**Headline:** Instant Email Alerts

**Description:**
Get notified immediately when someone captures a lead. Email includes:
- Contact info (email & phone)
- Project name
- Products selected
- Primary category of interest
- UTM campaign data
- Direct reply-to address

**Customize:**
Set notification email in Settings → Lead Capture

---

### Card 2: CSV Export

**Icon:** 📊

**Headline:** Export to Your CRM

**Description:**
Download all leads as CSV file for import into:
- Salesforce
- HubSpot
- Excel/Google Sheets
- Any CRM system

**Includes:**
Email, phone, project name, product count, category, consent status, UTM data, timestamp

**Export anytime** from the Leads page.

---

### Card 3: UTM Tracking

**Icon:** 🎯

**Headline:** Campaign Attribution

**Description:**
Automatically captures UTM parameters from your marketing campaigns:
- utm_source (Google, Facebook, email)
- utm_medium (cpc, social, email)
- utm_campaign (spring-promo-2025)
- utm_term (landscape supplies)
- utm_content (ad-variant-A)

**Use case:**
Know which ads/campaigns drive the most leads.

---

### Card 4: Rate Limiting

**Icon:** 🛡️

**Headline:** Anti-Spam Protection

**Description:**
Built-in protection against abuse:
- Max 5 submissions per hour per email
- Max 5 submissions per hour per IP address
- Honeypot field catches bots
- Error messages for duplicate submissions

**Privacy:**
IP addresses are hashed (SHA-256) for privacy compliance.

---

### Card 5: GDPR Consent

**Icon:** ✅

**Headline:** Optional Consent Checkbox

**Description:**
Lead capture modal includes optional consent checkbox:

*"Email me updates about products and projects"*

**Stored in database:**
- Consent = Yes/No
- Included in CSV export
- Shows GDPR compliance

**Customizable:**
Can be removed via filter if not needed.

---

### Card 6: Project Context

**Icon:** 📋

**Headline:** Know What They're Building

**Description:**
Each lead includes:
- Project name (if entered)
- Number of products selected
- Top category (e.g., "Steel Studs")
- Product list (visible in notification email)

**Follow-up advantage:**
"I see you're specifying steel studs for [Project Name]..."

---

## Section 5: Lead Routing (Agency Feature)

### What is Lead Routing?

**Agency feature** that automatically routes leads to the right sales rep or system based on configurable rules.

**Example scenarios:**
- Route leads from `@gmail.com` to retail sales team
- Route leads from `@contractor.com` to commercial team
- Route leads with UTM source "google-ads" to marketing team
- Route leads selecting "HVAC" category to HVAC specialist
- Send all leads to Zapier webhook for CRM integration

---

### How Lead Routing Works

**Path:** `Submittal Builder → Settings → Lead Routing`

**1. Create Rules**

Each rule has:
- **Name** (e.g., "Commercial Contractors")
- **Conditions** (when to match):
  - Email domain (e.g., `contractor.com, builder.com`)
  - UTM source (e.g., `google, facebook`)
  - UTM medium (e.g., `cpc, social`)
  - UTM campaign (e.g., `spring-promo`)
  - Top category (e.g., "Steel Studs")
- **Actions** (what to do):
  - Send email to: `sales-team@company.com`
  - Send webhook to: `https://hooks.zapier.com/...`

**2. Set Priority**

Rules are evaluated **top to bottom**. First match wins.

Drag to reorder rules by priority.

**3. Set Fallback**

If no rules match, lead goes to fallback:
- Fallback email (optional)
- Fallback webhook (optional)

---

### Creating Your First Routing Rule

**Example: Route contractor emails to commercial team**

1. Go to `Settings → Lead Routing`
2. Click **"+ Add Rule"**
3. Fill in:
   - **Name:** "Commercial Contractors"
   - **Email Domains:** `contractor.com, builder.com, construction.com`
   - **Then Email:** `commercial-sales@mycompany.com`
   - **Enabled:** ✅ ON
4. Click **"Save Rule"**

**Result:**
Any lead from `john@contractor.com` will route to `commercial-sales@mycompany.com`

---

### Webhook Integration

**Send leads to external systems** like:
- Zapier
- Make (Integromat)
- Your custom CRM
- Slack notifications
- Any webhook endpoint

**Webhook payload:**
```json
{
  "event": "lead.captured",
  "site": {
    "url": "https://yoursite.com",
    "name": "Your Company",
    "plugin_version": "1.0.2"
  },
  "lead": {
    "id": 123,
    "created_at": "2025-01-27T10:30:00Z",
    "email": "john@contractor.com",
    "phone": "(555) 123-4567",
    "project_name": "Downtown Office Build",
    "num_items": 12,
    "top_category": "Steel Studs",
    "utm": {
      "source": "google",
      "medium": "cpc",
      "campaign": "spring-promo"
    }
  },
  "routing": {
    "rule_name": "Commercial Contractors",
    "matched": true
  }
}
```

**Webhook features:**
- HTTPS only (security)
- Automatic retry on failure (4 attempts with backoff: 30s, 2m, 10m)
- Delivery logging
- Test webhook with last captured lead

---

### Testing Rules

**Path:** `Settings → Lead Routing → Test Rule`

1. Select a rule to test
2. Plugin uses **last captured lead** as test data
3. Shows:
   - ✅ Would match (or ❌ Would NOT match)
   - Lead details used for test
   - Which conditions matched

**Use case:**
Verify rules work before enabling them.

---

## Section 6: Weekly Lead Export (Agency)

### Automated CSV Email

**Agency feature** that sends weekly CSV exports automatically.

**Path:** `Submittal Builder → Leads → Weekly Export`

**Setup:**
1. Toggle **"Enable Weekly Export"** to ON
2. Set **Recipient Email(s)** (comma-separated)
3. Choose **Day of Week** (Monday - Sunday)
4. Choose **Time** (midnight - 11pm)
5. Click **"Save Settings"**

**What happens:**
- Every [Day] at [Time], plugin exports all leads to CSV
- Sends email to recipients with CSV attached
- Subject: `[Your Site] Weekly Lead Export - [Date Range]`

**Manual trigger:**
Click **"Send Now"** to get immediate export (doesn't wait for schedule).

**CSV includes:**
- All leads captured since last export (or all-time if first run)
- Same fields as manual CSV export
- Attachment filename: `leads-YYYY-MM-DD.csv`

---

## Section 7: Viewing & Managing Leads

### Leads Dashboard

**Path:** `Submittal Builder → Leads`

**Table columns:**
- **Email** - Click to send email
- **Phone** - Click to call (if mobile)
- **Project** - Project name entered
- **Products** - Number of items selected
- **Category** - Top category of interest
- **Source** - UTM source (marketing attribution)
- **Date** - When lead was captured

**Sorting:**
- Click column headers to sort
- Default: Newest first

**Pagination:**
- 20 leads per page
- Navigate with page numbers

**Search:**
- Search by email, phone, or project name
- Real-time filter

---

### Lead Details View

Click on any lead to see full details:

**Contact Information:**
- Email
- Phone
- Consent status (opted in for updates?)

**Request Details:**
- Project name
- Number of products selected
- Top category
- Full product list (if available)
- Date/time captured

**Marketing Attribution:**
- UTM Source
- UTM Medium
- UTM Campaign
- UTM Term
- UTM Content
- IP Hash (privacy-safe)

**Actions:**
- Email lead (opens mailto link)
- Export single lead to CSV
- Delete lead
- View related PDFs (if tracking enabled)

---

## Section 8: Privacy & Compliance

### GDPR Compliance

**Data collected:**
- Email address (required)
- Phone number (optional)
- Project name (optional)
- Consent checkbox (optional)
- UTM parameters (automatic)
- IP address hash (not reversible)

**Privacy features:**
- IP addresses hashed with SHA-256 (not stored raw)
- Consent checkbox for marketing opt-in
- Clear privacy notice in modal
- Data export available (CSV)
- Data deletion available (per lead)

**User rights:**
- Request data export
- Request data deletion
- Withdraw consent anytime

---

### Data Retention

**Default retention:**
- Leads stored indefinitely until manually deleted
- No automatic purge

**Recommended policy:**
1. Export leads monthly to your CRM
2. Delete leads older than 12 months
3. Document in your privacy policy

**Manual cleanup:**
Settings → Database → Clean up old leads

---

## Section 9: Troubleshooting

### Modal Not Appearing

**Problem:** Lead capture modal doesn't show before PDF download.

**Solutions:**
1. Check `Settings → Lead Capture → Enable Lead Capture` is ON
2. Clear browser cache and refresh page
3. Check for JavaScript errors in browser console (F12)
4. Disable conflicting plugins temporarily
5. Test with default WordPress theme

---

### Email Notifications Not Sending

**Problem:** No email alert when lead is captured.

**Solutions:**
1. Check `Settings → Lead Capture → Notification Email` is correct
2. Test email delivery: `Settings → Utilities → Test Email`
3. Check spam/junk folder
4. Verify WordPress can send email (try WP Mail SMTP plugin)
5. Check server email logs for errors

---

### Webhooks Failing

**Problem:** Webhook not receiving lead data.

**Solutions:**
1. Verify webhook URL uses HTTPS (HTTP not allowed)
2. Check webhook endpoint is accessible (not behind firewall)
3. View delivery log: `Settings → Lead Routing → Delivery Log`
4. Test webhook with RequestBin or webhook.site
5. Check webhook endpoint accepts POST requests with JSON

**Webhook retry:**
Plugin retries failed webhooks 4 times with exponential backoff (30s, 2m, 10m).

---

### Leads Not Saving

**Problem:** Form submits but lead doesn't appear in database.

**Solutions:**
1. Check database table exists: `wp_sfb_leads`
2. Check PHP error logs for database errors
3. Verify database user has INSERT permissions
4. Check rate limiting (max 5 per hour per email/IP)
5. Test with different email address

---

### CSV Export Empty

**Problem:** CSV export downloads but has no data.

**Solutions:**
1. Verify leads exist in `Leads` page
2. Check date range filter (if any)
3. Try exporting single lead first
4. Check browser download folder
5. Verify you have `manage_options` capability (admin)

---

## Section 10: Best Practices

### For Maximum Lead Conversion

✅ **Do:**
- Keep form simple (email + phone only)
- Use clear value proposition: "Get your PDF"
- Make phone field optional (reduces friction)
- Add consent checkbox for GDPR compliance
- Test modal on mobile devices
- Send follow-up email within 24 hours

❌ **Don't:**
- Ask for too many fields (reduces conversions)
- Make phone required (not everyone has it handy)
- Hide privacy notice
- Send generic follow-ups (personalize based on products selected)

---

### For Lead Routing Rules

✅ **Do:**
- Order rules by specificity (most specific first)
- Use email domain matching for B2B leads
- Use UTM matching for campaign-specific routing
- Test rules before enabling
- Set up fallback routing
- Monitor delivery log regularly

❌ **Don't:**
- Create overlapping rules (first match wins)
- Route to invalid email addresses
- Use HTTP webhooks (HTTPS only)
- Forget to enable rules after testing
- Ignore delivery failures

---

### For Follow-Up

✅ **Do:**
- Reply within 24 hours (leads go cold fast)
- Reference specific products they selected
- Mention project name in subject line
- Provide value (answer questions, offer help)
- Track leads in your CRM

❌ **Don't:**
- Send generic "Thanks for your interest" emails
- Ignore UTM data (tells you how they found you)
- Wait more than 48 hours to follow up
- Spam them with sales pitches
- Forget to personalize based on their selection

---

## Section 11: Pricing & Upgrades

### Feature Availability

| Feature                  | Free | Pro | Agency |
|--------------------------|------|-----|--------|
| Lead Capture Modal       | ❌   | ✅  | ✅     |
| Email Notifications      | ❌   | ✅  | ✅     |
| CSV Export               | ❌   | ✅  | ✅     |
| UTM Tracking             | ❌   | ✅  | ✅     |
| Lead Routing             | ❌   | ❌  | ✅     |
| Webhook Integration      | ❌   | ❌  | ✅     |
| Weekly Auto-Export       | ❌   | ❌  | ✅     |

**Upgrade to Pro:** [View Pricing →]

---

## Section 12: Related Documentation

**📖 Getting Started:**
- [Quick Setup Guide](/documentation/getting-started/quick-setup/)
- [Enable Lead Capture in 3 Steps](#getting-started)

**📧 Email Configuration:**
- [Email Settings](/documentation/admin-settings/#email-settings)
- [White-Label Email Sender (Agency)](/documentation/branding-pdfs/#white-label-mode)

**🎯 Tracking & Analytics:**
- [Tracking Links (Pro)](/documentation/tracking/)
- [Agency Analytics (Agency)](/documentation/agency-analytics/)

**🔧 Advanced:**
- [Developer Hooks - Lead Capture](/documentation/developer/#hooks-lead-capture)
- [REST API - Leads Endpoint](/documentation/developer/#api-leads)

---

## FAQs

**Q: Can I customize the lead capture form fields?**

A: Not via settings, but you can use filters to customize:
- Field labels
- Privacy text
- Consent checkbox text
- Required fields

See [Developer Documentation](/documentation/developer/#lead-capture-filters)

---

**Q: Can I turn off lead capture for specific pages?**

A: Yes, use this filter in your theme's `functions.php`:

```php
add_filter('sfb_lead_capture_enabled', function($enabled) {
  if (is_page('demo')) {
    return false; // Disable on demo page
  }
  return $enabled;
});
```

---

**Q: Are leads stored forever?**

A: Yes, until you manually delete them. You can:
- Delete individual leads from Leads page
- Bulk delete old leads from Settings → Database
- Export to CSV and delete from WordPress

Recommended: Export monthly and delete leads older than 12 months.

---

**Q: Can I import leads from another system?**

A: Not via UI, but you can import via database or WP-CLI. Contact support for assistance.

---

**Q: What happens if someone enters a fake email?**

A: The form validates email format (must be valid email address), but cannot verify if email exists.

**Best practice:** Send follow-up email immediately. Bounced emails indicate fake addresses.

---

**Q: Can I integrate with my CRM automatically?**

A: Yes, use webhooks (Agency feature) to send leads to:
- Zapier (connects to 5000+ apps)
- Make/Integromat
- Your custom CRM API

See [Webhook Integration](#webhook-integration)

---

**Q: Do I get charged per lead?**

A: No. Unlimited leads included with Pro/Agency license.

---

**Q: Can I add custom fields to the lead capture form?**

A: Custom fields require code customization. Use filters:

```php
add_filter('sfb_lead_capture_fields', function($fields) {
  $fields['company'] = [
    'type' => 'text',
    'label' => 'Company Name',
    'required' => false,
  ];
  return $fields;
});
```

See [Developer Documentation](/documentation/developer/#custom-lead-fields)

---

## Elementor Build Notes

**Sections:** 12 total
**Time:** 60-90 minutes
**Complexity:** Medium (lots of content, but straightforward)

**No "Need Help" section at bottom** (per your request).

**Widgets needed:**
- Heading
- Text Editor
- Icon Box (for feature cards)
- Table (for feature comparison)
- Code Block (for webhook JSON example)
- Accordion (optional, for FAQs)

**Layout:**
- Hero: Single column, centered
- Overview: Single column
- 3-step setup: Numbered list or 3 columns
- Feature cards: 2×3 grid (desktop), stacks on mobile
- Lead routing: Single column with code example
- Troubleshooting: Single column, bulleted lists
- Best practices: Two-column checklist layout
- Pricing table: Simple 3-column table
- FAQs: Accordion or plain text

**Color coding:**
- ✅ Green for "Do" items
- ❌ Red for "Don't" items
- 💡 Blue for tips
- ⚠️ Yellow for warnings

---

**Last Updated:** 2025-01-27
**For:** Lead Management Documentation Page
**Tiers:** Pro & Agency
