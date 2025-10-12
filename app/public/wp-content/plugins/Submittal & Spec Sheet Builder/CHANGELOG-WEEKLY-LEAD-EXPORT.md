# CHANGELOG: Weekly Lead Export Scheduler

## Phase B: Initial Implementation (2025-10-11)

### Overview
Implemented automatic weekly email delivery of new leads in CSV format for Agency license holders. This feature enables project managers to receive regular lead reports without manual exports, with built-in duplicate prevention and flexible scheduling.

### New Features

#### Settings UI
- **New settings card:** "📅 Weekly Lead Export (Agency)" in Settings page
- **Configuration options:**
  - Enable/disable toggle for weekly exports
  - Recipient email address field
  - Day of week selector (Monday-Sunday)
  - Time of day picker (24-hour format in site timezone)
- **Manual "Send Now" button:**
  - Test export functionality before scheduled run
  - Real-time AJAX feedback
  - Shows success/error messages inline
- **License gating:** Card only visible to Agency license holders

#### Database Schema
- **Added column:** `last_export_sent` to `wp_sfb_leads` table
  - Type: DATETIME NULL
  - Purpose: Track which leads have been included in exports
  - Index: Added for query performance
  - Updated by: Automated export process

#### Cron Job
- **Hook name:** `sfb_weekly_lead_export`
- **Schedule:** Weekly, respects site timezone
- **Smart scheduling:**
  - Calculates next occurrence based on selected day/time
  - Handles timezone conversions properly
  - Automatically reschedules after each run
- **Auto-enable/disable:**
  - Schedules when toggle turned on
  - Unschedules when toggle turned off
  - Requires Agency license to schedule

#### Email Delivery
- **Attachment:** CSV file with lead data
- **Filename format:** `sfb-leads-weekly-YYYY-MM-DD-HHmmss.csv`
- **Email subject:** `[Site Name] Weekly Lead Export - N New Leads`
- **Email body includes:**
  - Total new leads count
  - Date range of included leads
  - Export generation timestamp
  - Site information
- **CSV columns match Leads page:**
  - Date, Email, Phone, Project Name, Items, Top Category
  - Consent, UTM Source/Medium/Campaign/Term/Content
  - IP Hash (partial, first 8 chars)

#### Duplicate Prevention
- **Query filter:** `WHERE last_export_sent IS NULL`
- **Auto-marking:** Updates `last_export_sent` after successful send
- **Guarantees:** Each lead only exported once
- **Manual reset:** Can be cleared via database if re-export needed

### Files Modified

#### Settings Page
- **submittal-form-builder.php** (lines 2413-2568)
  - Added Weekly Lead Export card with all settings
  - Timezone-aware day/time pickers
  - "Send Now" button with AJAX handler
  - Inline result display

#### CSS Styles
- **submittal-form-builder.php** (lines 2759-2790)
  - Added `.sfb-text-input` styles
  - Added `.sfb-select-input` styles
  - Consistent focus states with brand color
  - Responsive width handling

#### Database Schema
- **submittal-form-builder.php** (lines 218-235)
  - Added `last_export_sent DATETIME NULL` column
  - Added index on `last_export_sent`
  - Schema update via dbDelta (safe for existing installations)

#### Hook Registration
- **submittal-form-builder.php** (lines 119-122)
  - `add_action('wp', 'schedule_weekly_lead_export_cron')`
  - `add_action('sfb_weekly_lead_export', 'cron_send_weekly_export')`
  - `add_action('wp_ajax_sfb_send_weekly_export_now', 'ajax_send_weekly_export_now')`

#### Core Functions
- **submittal-form-builder.php** (lines 4546-4757)
  - `ajax_send_weekly_export_now()` - AJAX handler for manual send
  - `cron_send_weekly_export()` - Cron callback
  - `send_weekly_lead_export()` - Core email sending logic
  - `generate_csv_content()` - CSV generation from leads array

#### Cron Scheduling
- **submittal-form-builder.php** (lines 7608-7677)
  - `schedule_weekly_lead_export_cron()` - Schedule/unschedule logic
  - `calculate_next_weekly_run()` - Timezone-aware next occurrence calculator

### Technical Details

#### Cron Scheduling Logic
The scheduler respects WordPress site timezone and user-selected day/time:

```php
// Example: Schedule for Monday at 9:00 AM
1. Get current time in site timezone
2. Calculate days until next Monday
3. Set time to 09:00
4. Convert to Unix timestamp
5. Schedule with wp_schedule_event()
```

#### CSV Generation
Uses in-memory stream to avoid disk I/O:
```php
$output = fopen('php://memory', 'w');
fputcsv($output, $headers);
foreach ($leads as $lead) {
  fputcsv($output, $row_data);
}
rewind($output);
$csv = stream_get_contents($output);
```

#### Email Attachment
Creates temporary file for wp_mail():
```php
$temp_file = wp_tempnam($filename);
file_put_contents($temp_file, $csv_content);
wp_mail($to, $subject, $body, $headers, [$temp_file]);
@unlink($temp_file);
```

#### Duplicate Prevention
After successful email send:
```php
UPDATE wp_sfb_leads
SET last_export_sent = NOW()
WHERE id IN (exported_lead_ids)
```

### Security Considerations

1. **AJAX Handler:**
   - Nonce verification required
   - Admin capability check (`manage_options`)
   - Agency license validation

2. **Cron Callback:**
   - Public hook but checks Agency license
   - Logs failures to error_log
   - Fails gracefully if misconfigured

3. **Email Delivery:**
   - Validates recipient email format
   - Uses WordPress sanitization for all inputs
   - Temporary files cleaned up immediately

4. **Database:**
   - Prepared statements for lead queries
   - Sanitized lead IDs for UPDATE query
   - Index prevents slow queries on large tables

### Settings Storage
All settings stored as individual WordPress options:
- `sfb_lead_weekly_export_enabled` (boolean)
- `sfb_lead_weekly_export_email` (string)
- `sfb_lead_weekly_export_day` (string, default: 'monday')
- `sfb_lead_weekly_export_time` (string, default: '09:00')

### Testing Performed

✅ Settings UI displays correctly for Agency users
✅ Settings hidden from non-Agency users
✅ "Send Now" button works with new leads
✅ "Send Now" shows appropriate error when no new leads
✅ "Send Now" validates email address
✅ CSV format matches Leads page export
✅ Email attachment received successfully
✅ Leads marked as sent after email
✅ Second "Send Now" reports no new leads (duplicate prevention)
✅ Cron schedules correctly for configured day/time
✅ Cron unschedules when feature disabled
✅ Timezone handling works correctly
✅ Database column created via dbDelta

### Known Limitations

1. **No CSV customization:** Column selection hard-coded
2. **No email template:** Plain text email only
3. **No multi-recipient:** Single email address only
4. **No BCC option:** Cannot add CC/BCC recipients
5. **No retry logic:** If email fails, leads are not re-sent
6. **No export history:** Cannot view past export dates/counts
7. **Manual schedule change:** Must disable/re-enable to update schedule

### Future Enhancements (Not Implemented)

- Export history log (who sent, when, how many leads)
- Multi-recipient support (comma-separated emails)
- HTML email template with branding
- Custom column selection
- Frequency options (daily, bi-weekly, monthly)
- Conditional exports (only send if N+ new leads)
- Export preview before sending
- Webhook integration (alternative to email)
- Lead segmentation (export by category, date range)

### Acceptance Criteria Status

✅ **Settings UI:**
- Toggle to enable/disable
- Email address field
- Day and time selectors
- Site timezone displayed

✅ **Manual "Send Now":**
- Button sends test export
- Shows success/error feedback
- Works independently of schedule

✅ **Automated weekly send:**
- Cron job scheduled at configured time
- Respects site timezone
- Sends email with CSV attachment

✅ **Duplicate prevention:**
- Marks sent leads in database
- Only new leads included in each export
- "Send Now" reports when no new leads

✅ **CSV format:**
- Columns match Leads page export
- All lead data included
- Proper CSV formatting

### Deployment Notes

**Database migration:** Automatic via dbDelta when plugin loaded

**New settings:** Default values:
- Enabled: false
- Email: empty (must be configured)
- Day: Monday
- Time: 09:00

**Cron:** Will not schedule until feature enabled + Agency license active

**Backwards compatible:** Existing leads get NULL for `last_export_sent`

### Related Files

See implementation details in:
- `submittal-form-builder.php:2413-2568` (Settings UI)
- `submittal-form-builder.php:2759-2790` (CSS styles)
- `submittal-form-builder.php:218-235` (Database schema)
- `submittal-form-builder.php:119-122` (Hook registration)
- `submittal-form-builder.php:4546-4757` (Core functions)
- `submittal-form-builder.php:7608-7677` (Cron scheduling)

---

**Implementation Date:** October 11, 2025
**Implemented By:** Phase B - Weekly Lead Export Feature
**Feature Status:** ✅ Complete and Ready for Production
