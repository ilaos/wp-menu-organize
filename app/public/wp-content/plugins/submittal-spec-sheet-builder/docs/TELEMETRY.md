# Telemetry System Documentation

**Anonymous usage analytics for Submittal & Spec Sheet Builder**

---

## Overview

The telemetry system collects anonymous usage data to help improve the plugin. It is:
- **Opt-in only** (disabled by default)
- **Privacy-first** (no personal data collected)
- **Transparent** (users see exactly what's collected)

---

## What Data is Collected

### Environment Data
- WordPress version
- PHP version
- MySQL version
- PHP memory limit
- PHP max execution time
- Server software
- WordPress language
- Multisite status
- Debug mode status

### Plugin Data
- Plugin version
- License tier (free/pro/agency)
- Opt-in date
- Days since activation

### Feature Usage
- Lead capture enabled (true/false)
- Lead routing enabled (true/false)
- White-label mode enabled (true/false)
- Tracking links enabled (true/false)
- Brand presets count
- Weekly export enabled (true/false)

### Catalog Statistics (Counts Only)
- Total nodes
- Category count
- Product count
- Type count
- Subtype count
- Model count

### Usage Metrics
- Total leads captured
- Total PDFs generated
- Days since plugin activation
- Admin user count

### Privacy-Safe Identifier
- **Site hash** (SHA-256 hash of site URL, not reversible)

---

## What is NOT Collected

❌ Site URL (in plain text)
❌ Email addresses
❌ Personal information
❌ Product data or customer information
❌ Any site content
❌ IP addresses

---

## How It Works

### 1. Opt-In Notice

When a user visits a plugin page for the first time, they see an admin notice explaining:
- What data is collected
- What is NOT collected
- Link to privacy policy

They can:
- **Opt in** - Enable telemetry
- **Opt out** - Decline telemetry
- **Dismiss** - Decide later

### 2. Weekly Ping

If enabled, data is sent to your API endpoint once per week via WordPress cron:
- Runs on `sfb_telemetry_weekly_ping` hook
- Non-blocking (fire and forget)
- 10-second timeout
- Errors logged but don't block execution

### 3. Data Transmission

```php
POST https://api.webstuffguylabs.com/v1/telemetry

Headers:
Content-Type: application/json
User-Agent: SubmittalBuilder/1.2.3

Body:
{
  "site_hash": "a3f8c...", // SHA-256 hash of site URL
  "plugin_version": "1.2.3",
  "license_tier": "pro",
  "wp_version": "6.4.2",
  "php_version": "8.1.0",
  "features_enabled": {
    "lead_capture": true,
    "tracking": true,
    ...
  },
  "catalog_stats": {
    "total_nodes": 250,
    "categories": 5,
    ...
  },
  "usage_stats": {
    "total_leads": 45,
    "total_pdfs_generated": 120,
    ...
  },
  "timestamp": "2025-01-27T10:30:00Z"
}
```

---

## Implementation Details

### File Location
`Includes/telemetry.php`

### Main Class
`SFB_Telemetry`

### Key Methods

**`SFB_Telemetry::is_enabled()`**
- Returns `true` if user opted in
- Returns `false` if user opted out or hasn't decided

**`SFB_Telemetry::collect_data()`**
- Gathers all telemetry data
- Returns array of anonymous metrics

**`SFB_Telemetry::send_telemetry_data()`**
- Sends data to API endpoint
- Non-blocking (doesn't wait for response)
- Logs errors if sending fails

**`SFB_Telemetry::test_ping()`**
- Manual test function for debugging
- Prints telemetry data without sending
- Only for admins

### WordPress Hooks Used

**`admin_notices`**
- Shows opt-in notice to admins

**`admin_init`**
- Handles opt-in/opt-out actions
- Registers settings

**`sfb_telemetry_weekly_ping`**
- Cron hook for weekly data transmission
- Scheduled automatically on plugin activation

**`sfb_telemetry_opted_in`**
- Fires when user opts in
- Sends initial ping immediately

---

## API Endpoint Setup

You need to create an API endpoint to receive telemetry data:

**URL:** `https://api.webstuffguylabs.com/v1/telemetry`

**Method:** POST

**Expected Response:**
- **200 OK** - Data received successfully
- **4xx/5xx** - Error (will be logged but won't retry)

### Example API Handler (Node.js/Express)

```javascript
app.post('/v1/telemetry', async (req, res) => {
  try {
    const data = req.body;

    // Validate site_hash exists
    if (!data.site_hash) {
      return res.status(400).json({ error: 'Missing site_hash' });
    }

    // Store in database
    await db.telemetry.create({
      site_hash: data.site_hash,
      plugin_version: data.plugin_version,
      license_tier: data.license_tier,
      wp_version: data.wp_version,
      php_version: data.php_version,
      features: JSON.stringify(data.features_enabled),
      catalog_stats: JSON.stringify(data.catalog_stats),
      usage_stats: JSON.stringify(data.usage_stats),
      timestamp: data.timestamp,
      received_at: new Date(),
    });

    res.status(200).json({ success: true });
  } catch (error) {
    console.error('Telemetry error:', error);
    res.status(500).json({ error: 'Internal server error' });
  }
});
```

### Database Schema Example

```sql
CREATE TABLE telemetry (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  site_hash VARCHAR(64) NOT NULL,
  plugin_version VARCHAR(20),
  license_tier ENUM('free', 'pro', 'agency'),
  wp_version VARCHAR(20),
  php_version VARCHAR(20),
  features JSON,
  catalog_stats JSON,
  usage_stats JSON,
  timestamp DATETIME,
  received_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (site_hash),
  INDEX (license_tier),
  INDEX (timestamp)
);
```

---

## Testing Telemetry

### 1. Test Data Collection

Add this to your `functions.php` or run via WP-CLI:

```php
// View telemetry data without sending
SFB_Telemetry::test_ping();
```

This will print the data that would be sent.

### 2. Test Opt-In Flow

1. Fresh WordPress install
2. Activate plugin
3. Visit plugin admin page
4. You should see opt-in notice
5. Click "Yes, help improve the plugin"
6. Check `wp_options` for `sfb_telemetry_enabled = 1`

### 3. Test Weekly Ping

Trigger manually via WP-CLI:

```bash
wp cron event run sfb_telemetry_weekly_ping
```

Or via code:

```php
do_action('sfb_telemetry_weekly_ping');
```

### 4. Check Cron Schedule

```bash
wp cron event list | grep sfb_telemetry
```

Should show:
```
sfb_telemetry_weekly_ping  [timestamp]  1 week
```

---

## Privacy Compliance

### GDPR Compliance

✅ **Opt-in by default** - Telemetry disabled until user explicitly opts in
✅ **Transparent** - Users see exactly what's collected
✅ **No personal data** - Only anonymous metrics
✅ **Easy opt-out** - Users can disable anytime in settings
✅ **Privacy policy link** - Linked in opt-in notice

### Data Retention Recommendations

For your API/database:
- Keep telemetry data for 2 years maximum
- Delete data older than 2 years automatically
- Aggregate old data (e.g., monthly summaries) then delete raw data
- Document retention policy in your privacy policy

---

## User-Facing Settings

Users can enable/disable telemetry in:

**Settings → Telemetry**

The settings field shows:
- Checkbox to enable/disable
- Explanation of what's collected
- Link to privacy policy
- Last ping timestamp (if enabled)
- Expandable details section

---

## Troubleshooting

### Telemetry Not Sending

**Check if enabled:**
```php
get_option('sfb_telemetry_enabled'); // Should be 1
```

**Check last ping time:**
```php
get_option('sfb_telemetry_last_ping'); // Should be recent date
```

**Check cron schedule:**
```bash
wp cron event list | grep sfb_telemetry
```

**Manually trigger:**
```php
SFB_Telemetry::send_weekly_ping();
```

### API Endpoint Not Receiving Data

1. Check WordPress error log for failed requests
2. Verify API endpoint is accessible (test with Postman)
3. Check CORS headers if API is on different domain
4. Verify SSL certificate is valid (HTTPS required)

### Cron Not Running

WordPress cron only runs when the site gets traffic. For low-traffic sites:

**Option 1: Real cron**
```bash
# Disable WordPress cron
define('DISABLE_WP_CRON', true);

# Add to server cron (runs every hour)
0 * * * * curl https://yoursite.com/wp-cron.php > /dev/null 2>&1
```

**Option 2: Manual trigger**
Add to your monitoring script:
```bash
wp cron event run --due-now
```

---

## What You Can Learn From Telemetry

### Environment Insights
- PHP version distribution → Decide minimum PHP requirement
- WordPress version adoption → When to drop old WP support
- Memory limit issues → Add warnings for low-memory sites

### Feature Adoption
- Which Pro features are most used → Prioritize development
- Which features are never used → Consider deprecating
- Upgrade patterns → Free → Pro vs. Free → Agency

### Catalog Size Patterns
- Average product count → Optimize for typical use case
- Large catalog performance → Identify scaling issues
- Small catalog users → Simplify UI for beginners

### Usage Patterns
- PDF generation frequency → Server resource planning
- Lead capture adoption → Marketing insights
- Time-to-first-PDF → Onboarding effectiveness

---

## Next Steps

1. **Set up API endpoint** - Create endpoint to receive data
2. **Configure database** - Store telemetry data
3. **Build dashboard** - Visualize metrics
4. **Update privacy policy** - Document telemetry in your policy
5. **Monitor data** - Review weekly to improve plugin

---

## Future Enhancements

Ideas for expanding telemetry (all privacy-safe):

- **Error tracking** - Catch bugs early (opt-in)
- **Performance monitoring** - Track slow operations
- **Feature discovery** - Which Pro features Free users click
- **Onboarding tracking** - Where users get stuck
- **A/B testing** - Test UI changes with subset of users

See main docs for more on these advanced features.

---

**Last Updated:** 2025-01-27
**File:** docs/TELEMETRY.md
