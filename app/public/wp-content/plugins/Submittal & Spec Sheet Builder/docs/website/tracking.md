# PDF Tracking & Analytics (Pro)

[← Back to Documentation](./index.md)

**Track when customers view your submittal packets** | **Pro Feature**

---

## Overview

The Tracking feature allows you to monitor when recipients open and view the PDF submittal packets you send them. This is especially valuable for manufacturers and distributors who need to know if clients have actually received and reviewed their product documentation.

### What You Can Track

- **View counts** - How many times each packet was viewed
- **View timestamps** - Exact date and time of each view
- **Recipient information** - Who you sent the packet to
- **Engagement metrics** - Which packets have been viewed vs ignored
- **Project details** - What project each packet relates to

### License Requirement

**Pro or Agency License Required**

Tracking is a Pro feature available to both Pro Single Site and Pro Agency license holders. It works automatically once enabled.

---

## How Tracking Works

### Automatic Link Generation

When you use the **Auto Email Packet** feature (Pro), tracking links are automatically created for every PDF you generate and email. No manual setup required.

**Typical Flow:**
1. Customer generates PDF on your frontend builder
2. Lead Capture modal collects their email (if enabled)
3. Auto Email sends PDF with tracking link
4. Recipient clicks link to view/download PDF
5. View is logged with timestamp and details
6. You see activity on Tracking dashboard

### Privacy & Security

- **IP Hashing:** Visitor IPs are hashed (SHA-256) for privacy compliance
- **No Personal Data:** Only view counts and timestamps are stored
- **Recipient Transparency:** Recipients are not notified they're being tracked
- **Secure Links:** Each tracking URL is unique and non-guessable

---

## Accessing the Tracking Dashboard

### Navigation

```
WordPress Admin → Submittal Builder → Tracking
```

**Requirements:**
- Pro or Agency license active
- `manage_options` capability (Administrator role)
- At least one packet with tracking enabled

### Dashboard Overview

The Tracking page displays:

**Summary Statistics (Top Cards)**
- **Total Packets:** How many tracking links have been created
- **Total Views:** Cumulative views across all packets
- **Average Views:** Average views per packet
- **Unique Recipients:** Count of distinct email addresses

**Tracking Table (Main View)**
- Project name
- Recipient email address
- Date created
- Last viewed timestamp
- View count
- Tracking URL with copy button
- Visual engagement indicators

---

## Understanding the Dashboard

### Summary Cards

#### Total Packets
Shows the total number of PDF packets you've generated with tracking enabled.

**What This Tells You:**
- Volume of packets being sent
- Activity level over time
- How many trackable links exist

#### Total Views
Cumulative view count across all packets.

**What This Tells You:**
- Overall engagement level
- How often packets are being accessed
- Repeat views (same packet opened multiple times)

#### Average Views per Packet
Total views divided by total packets.

**What This Tells You:**
- **< 1:** Most packets never viewed (red flag!)
- **1-2:** Typical engagement (viewed once or twice)
- **> 3:** High engagement (reviewed multiple times)

#### Unique Recipients
Count of distinct email addresses that received packets.

**What This Tells You:**
- How many different people/companies you're serving
- Customer reach
- Repeat customers (if recipients < packets)

---

### Tracking Table Columns

#### Project Name
The project name entered when the PDF was generated.

**Example:** "Miller Hall – West Wing"

**Use Case:** Quickly identify which project a packet relates to.

#### Recipient Email
Email address of the person who received the packet.

**Example:** "contractor@acmeconstruction.com"

**Use Case:** Know who you sent the packet to.

#### Date Created
When the tracking link was first generated.

**Example:** "2025-10-10 09:15 AM"

**Use Case:** See how old the packet is, track time elapsed.

#### Last Viewed
Most recent timestamp when someone opened the link.

**Displays:**
- **"Never"** - Packet has not been viewed yet (gray text)
- **Timestamp** - Last view date/time (e.g., "2025-10-10 10:30 AM")

**Use Case:** Know if/when recipient opened your packet.

#### View Count
Total number of times the packet has been viewed.

**Badge Colors:**
- **Green (1+):** Packet has been viewed
- **Gray (0):** Packet not yet viewed

**Use Case:**
- **0 views:** Follow up with recipient
- **1 view:** Recipient reviewed it
- **3+ views:** Recipient is actively referencing it

#### Tracking URL
The unique shareable link for this packet.

**Format:** `https://yoursite.com/?sfb_track={token}`

**Actions:**
- **Copy icon button:** Click to copy URL to clipboard
- **Open in new tab:** Click URL to test/verify

**Use Case:**
- Copy and manually send via email/SMS
- Share in project management tools
- Test that link is working

---

## Visual Engagement Indicators

### Color-Coded Rows

The Tracking table uses visual cues to show engagement at a glance:

#### 🟢 Green Indicator
**Viewed Packets**
- View count badge is green
- "Last Viewed" shows timestamp
- Row may have subtle green tint

**Meaning:** Recipient has opened the packet at least once.

#### ⚪ Gray Indicator
**Not Yet Viewed**
- View count shows "0" in gray
- "Last Viewed" shows "Never" in gray text
- Standard row appearance

**Meaning:** Recipient has not opened the packet yet. Consider following up.

---

## Using Tracking in Your Workflow

### Scenario 1: Following Up on Proposals

**Problem:** You sent a quote package with product specs but haven't heard back.

**Solution with Tracking:**
1. Check Tracking dashboard
2. Look for the project/recipient
3. **If viewed (1+ views):** They saw it – follow up to discuss
4. **If not viewed (0 views):** Resend or call to confirm receipt

**Result:** Smarter follow-up timing, fewer "just checking in" emails.

---

### Scenario 2: Measuring Customer Engagement

**Problem:** You want to know which customers are actively evaluating your products.

**Solution with Tracking:**
1. Sort tracking table by "View Count" (descending)
2. Identify high-view-count packets (3+ views)
3. These customers are actively referencing your specs

**Result:** Prioritize follow-up with engaged prospects.

---

### Scenario 3: Verifying Submittal Receipt

**Problem:** Contractor claims they never received your submittal packet.

**Solution with Tracking:**
1. Search tracking table for project name or recipient email
2. Check "Date Created" and "Last Viewed"
3. **If viewed:** Proof of receipt with timestamp
4. **If not viewed:** Resend with tracking URL via SMS/chat

**Result:** Accountability and proof of delivery.

---

### Scenario 4: Identifying Repeat Customers

**Problem:** You want to see which customers request multiple packets.

**Solution with Tracking:**
1. Note recipient emails appearing multiple times in table
2. Filter/search by email domain (e.g., "acmeconstruction.com")
3. Count how many packets per customer

**Result:** Identify your most active/engaged customers.

---

## Best Practices

### ✅ Do This

1. **Check tracking before follow-up**
   - Avoid bothering customers who haven't viewed yet
   - Time your follow-up after they've reviewed

2. **Monitor "not viewed" packets**
   - Follow up within 24-48 hours if not viewed
   - Confirm email was received and link works

3. **Use high view counts as buying signals**
   - 3+ views often means serious consideration
   - Prioritize these leads for sales outreach

4. **Include tracking URLs in reminders**
   - Copy tracking URL from dashboard
   - Paste into follow-up emails/SMS for easy access

5. **Review analytics weekly**
   - Identify trends (view rates improving/declining?)
   - Adjust email subject lines if open rates low

### ❌ Don't Do This

1. **Don't mention tracking to recipients**
   - It's a business intelligence tool
   - No need to tell customers they're being tracked

2. **Don't rely solely on tracking**
   - Some users may block tracking pixels
   - Firewall/security may prevent logging
   - Always use as supplementary data

3. **Don't assume 0 views = not interested**
   - Email may be in spam folder
   - Recipient may have viewed forwarded PDF (not tracked)
   - Link may be blocked by corporate firewall

4. **Don't share tracking URLs publicly**
   - These are one-to-one links (recipient-specific)
   - Anyone with link can view the PDF
   - Keep URLs private/confidential

---

## Troubleshooting

### "Tracking page is blank/empty"

**Cause:** No tracking links have been created yet.

**Solution:**
1. Ensure Pro license is active
2. Enable **Auto Email Packet** in Settings
3. Generate a test PDF on frontend
4. Submit Lead Capture form with email
5. Check Tracking page – link should appear

---

### "View count shows 0 but I know they opened it"

**Possible Causes:**
- Recipient viewed forwarded PDF (not tracking link)
- Email client blocked tracking pixel
- Recipient used PDF from email attachment directly
- Corporate firewall/security blocked tracking

**Solution:**
- Ensure tracking URL (not PDF attachment) is clicked
- Test tracking URL yourself to verify it's working
- Some views may not be trackable – this is normal

---

### "Can't find a specific packet in the table"

**Cause:** Table may be long or packet was created before tracking enabled.

**Solution:**
1. Use browser search (Ctrl+F / Cmd+F)
2. Search by project name or recipient email
3. Sort table by "Date Created" to find recent packets
4. Check if packet was created before Pro activation

---

### "Tracking URL returns 404 error"

**Cause:** Link may be expired or malformed.

**Solution:**
1. Verify URL format: `?sfb_track={token}`
2. Check if packet was deleted (cleanup job may have removed it)
3. Check if Pro license is still active
4. Regenerate packet and get new tracking URL

---

## Privacy & Compliance

### GDPR Compliance

Tracking is designed with privacy in mind:

**What We Track:**
✅ View timestamps (when PDF was opened)
✅ View counts (how many times)
✅ Hashed IP addresses (SHA-256, not reversible)
✅ User agent (browser info)
✅ Recipient email (only what they provided)

**What We DON'T Track:**
❌ Browsing history
❌ Location data
❌ Personal details beyond email
❌ Third-party analytics cookies
❌ Cross-site tracking

### Data Retention

**Storage:**
- Tracking data stored in WordPress options table
- No external services or third-party tracking
- All data stays on your server

**Retention Policy:**
- Data retained indefinitely by default
- You can manually purge old tracking data
- No automatic expiration unless you configure it

### Recipient Transparency

**What Recipients See:**
- Normal PDF link (e.g., `yoursite.com/?sfb_track=abc123`)
- No visible indication of tracking
- Standard PDF download/view experience

**Compliance Notes:**
- Tracking is for business intelligence
- No PII beyond voluntarily provided email
- Consult your legal team for specific jurisdiction rules

---

## Technical Details

### How Tracking Links Work

1. **Link Generation:**
   - Unique token generated (e.g., `abc123def456`)
   - Format: `https://yoursite.com/?sfb_track={token}`
   - Token stored in `sfb_packets` option

2. **View Logging:**
   - Recipient clicks tracking link
   - WordPress processes `?sfb_track` query parameter
   - View recorded with timestamp, hashed IP, user agent
   - Redirect to actual PDF file

3. **Dashboard Display:**
   - Admin visits Tracking page
   - Plugin queries `sfb_packets` option
   - Displays all packets with view statistics
   - Real-time data (no caching)

### Database Storage

**Option:** `sfb_packets` (WordPress options table)

**Structure:**
```php
[
  'abc123def456' => [
    'project' => 'Miller Hall – West Wing',
    'recipient' => 'contractor@acme.com',
    'created_at' => '2025-10-10 09:15:00',
    'pdf_url' => 'https://example.com/path/to.pdf',
    'views' => [
      [
        'timestamp' => '2025-10-10 10:30:00',
        'ip_hash' => 'sha256_hash_here',
        'user_agent' => 'Mozilla/5.0...'
      ]
    ]
  ]
]
```

### Integration with Other Features

**Works With:**
- ✅ Auto Email Packet (tracking auto-enabled)
- ✅ Lead Capture (recipient email captured)
- ✅ White-Label Mode (tracking still works)
- ✅ Brand Themes (doesn't affect tracking)

**Not Compatible With:**
- ❌ Direct PDF downloads (no tracking)
- ❌ Manually emailed PDFs (use tracking URL instead)

---

## Advanced Usage

### Manually Sharing Tracking URLs

**Use Case:** You want to share a packet via SMS, Slack, or project management tool.

**Steps:**
1. Generate PDF on frontend (or admin)
2. Navigate to Tracking page
3. Find your packet in table
4. Click "Copy" icon next to tracking URL
5. Paste URL wherever you want to share
6. Views will be logged when link is clicked

**Example:**
```
Hey John, here's the spec packet for Miller Hall project:
https://yoursite.com/?sfb_track=abc123def456

Let me know if you have questions!
```

---

### Tracking Multiple Recipients

**Problem:** You want to send the same packet to multiple people and track who viewed it.

**Solution:**
1. Generate packet once (gets 1 tracking URL)
2. Share same tracking URL with multiple recipients
3. Views will be aggregated (can't distinguish individuals)

**Limitation:** Single tracking URL = combined view count. For individual tracking, generate separate packets per recipient.

---

### Exporting Tracking Data

**Currently:** No built-in export feature.

**Workaround:**
1. Copy table data manually
2. Use browser DevTools to inspect `sfb_packets` option
3. Custom code to query option and export as CSV

**Future:** Export feature may be added in future versions.

---

## FAQ

### Does tracking work on all devices?

Yes, tracking works on any device (desktop, mobile, tablet) as long as the recipient clicks the tracking URL.

---

### Can recipients tell they're being tracked?

No, tracking happens transparently via the URL. Recipients see a normal link and PDF experience.

---

### What if the recipient forwards the PDF?

If they forward the PDF file itself (not the tracking link), you won't track the forwarded recipient's views. Only clicks on the original tracking URL are logged.

---

### How long are tracking links valid?

Tracking links remain valid indefinitely as long as the PDF file exists on your server and your Pro license is active.

---

### Can I delete tracking data?

Currently, there's no built-in "Delete" button per packet. You can purge all tracking data by deleting the `sfb_packets` option (requires direct database access or custom code).

---

### Does tracking work with Lead Capture disabled?

Yes, tracking works independently of Lead Capture. However, you'll need to manually note the recipient email since Lead Capture won't collect it automatically.

---

### What happens if my Pro license expires?

Tracking stops working:
- Existing tracking URLs return errors
- Dashboard becomes inaccessible
- Historical data is preserved but not viewable
- Reactivating license restores access

---

## Next Steps

### Recommended Reading

- [Lead Capture & CRM](./lead-capture.md) - Collect recipient emails automatically
- [Auto Email Packet](./auto-email.md) - Automatically send PDFs with tracking
- [Admin Settings](./admin-settings.md) - Configure Pro features
- [Upgrade to Pro](./upgrade.md) - Get tracking + 8 other Pro features

### Related Features

**Also Pro Features:**
- **Lead Capture:** Collect emails before download
- **Auto Email:** Automatically send packets with tracking
- **White-Label:** Remove plugin branding from PDFs
- **Brand Themes:** Apply architectural/corporate themes

---

[← Back to Documentation](./index.md) | [Next: Lead Capture →](./lead-capture.md)

---

**Questions about Tracking?**

- **WordPress.org Forum:** [Community Support](https://wordpress.org/support/plugin/submittal-builder/)
- **Email Support:** support@webstuffguylabs.com (Pro users)
- **Documentation:** [Full Documentation](./index.md)

---

© 2025 WebStuff Guy Labs. All rights reserved.
