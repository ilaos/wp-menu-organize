<?php
/**
 * Lead Capture - Pro Feature
 *
 * Handles lead capture modal submission, storage, and email delivery.
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

if (!defined('ABSPATH')) exit;

class SFB_Lead_Capture {

  /**
   * Check if lead capture is enabled
   */
  public static function is_enabled(): bool {
    return (bool) get_option('sfb_lead_capture_enabled', false);
  }

  /**
   * AJAX handler for lead submission
   */
  public static function ajax_submit_lead() {
    // Verify nonce
    if (!check_ajax_referer('sfb_frontend_builder', 'nonce', false)) {
      wp_send_json_error(['message' => 'Security check failed.'], 403);
    }

    // Honeypot check (anti-bot)
    if (!empty($_POST['sfb_website'])) {
      // Bot detected - silently fail
      wp_send_json_error(['message' => 'Invalid submission.'], 400);
    }

    // Get form data
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $consent = !empty($_POST['consent']) ? 1 : 0;
    $project_name = sanitize_text_field($_POST['project_name'] ?? '');
    $num_items = intval($_POST['num_items'] ?? 0);
    $top_category = sanitize_text_field($_POST['top_category'] ?? '');

    // Validate required fields
    if (empty($email) || !is_email($email)) {
      wp_send_json_error(['message' => 'Valid email address is required.'], 400);
    }

    // Rate limiting check
    $rate_limit_result = self::check_rate_limit($email);
    if (!$rate_limit_result['allowed']) {
      wp_send_json_error([
        'message' => 'Too many submissions. Please try again later.',
        'retry_after' => $rate_limit_result['retry_after']
      ], 429);
    }

    // Capture UTM parameters
    $utm_data = [
      'source' => sanitize_text_field($_POST['utm_source'] ?? ''),
      'medium' => sanitize_text_field($_POST['utm_medium'] ?? ''),
      'campaign' => sanitize_text_field($_POST['utm_campaign'] ?? ''),
      'term' => sanitize_text_field($_POST['utm_term'] ?? ''),
      'content' => sanitize_text_field($_POST['utm_content'] ?? ''),
    ];
    $utm_json = wp_json_encode(array_filter($utm_data));

    // Hash IP for privacy
    $ip_hash = hash('sha256', self::get_client_ip());

    // Store lead in database
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';

    $inserted = $wpdb->insert(
      $table,
      [
        'email' => $email,
        'phone' => $phone,
        'project_name' => $project_name,
        'num_items' => $num_items,
        'top_category' => $top_category,
        'consent' => $consent,
        'utm_json' => $utm_json,
        'ip_hash' => $ip_hash,
        'created_at' => current_time('mysql'),
      ],
      ['%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
      error_log('SFB Lead Capture: Database error - ' . $wpdb->last_error);
      wp_send_json_error(['message' => 'Failed to save your information. Please try again.'], 500);
    }

    $lead_id = $wpdb->insert_id;

    // Track lead capture for Agency Analytics (non-PII)
    if (class_exists('SFB_Agency_Analytics')) {
      SFB_Agency_Analytics::track_lead_captured([
        'phone' => $phone,
        'num_items' => $num_items,
        'top_category' => $top_category,
      ]);
    }

    // Send lead notification email to admin
    $email_sent = self::send_lead_notification_email($email, $phone, $project_name, $num_items, $top_category);

    // Log action
    do_action('sfb_lead_captured', $lead_id, $email, [
      'phone' => $phone,
      'project_name' => $project_name,
      'num_items' => $num_items,
      'top_category' => $top_category,
      'consent' => $consent,
    ]);

    wp_send_json_success([
      'message' => 'Thank you! Your PDF is being generated.',
      'lead_id' => $lead_id,
      'email_sent' => $email_sent,
    ]);
  }

  /**
   * Send lead notification email to admin (not to customer)
   */
  public static function send_lead_notification_email(string $customer_email, string $phone = '', string $project_name = '', int $num_items = 0, string $top_category = ''): bool {
    if (!is_email($customer_email)) {
      return false;
    }

    // Get notification email from settings (fallback to admin email)
    $notification_email = get_option('sfb_lead_notification_email', get_option('admin_email'));

    if (!is_email($notification_email)) {
      error_log('SFB Lead Capture: Invalid notification email address');
      return false;
    }

    // Get branding settings
    $settings = get_option('sfb_settings', []);
    $company_name = $settings['company_name'] ?? get_bloginfo('name');

    // Email subject - Clear and actionable for admin
    /* translators: %s: project name or default "Submittal Request" */
    $subject = sprintf(
      __('🔔 New Lead: %s', 'submittal-spec-sheet-builder'),
      !empty($project_name) ? $project_name : __('Submittal Request', 'submittal-spec-sheet-builder')
    );

    // Build lead details
    $lead_details = [];
    $lead_details[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
    $lead_details[] = __('CONTACT INFORMATION:', 'submittal-spec-sheet-builder');
    $lead_details[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
    /* translators: %s: customer email address */
    $lead_details[] = sprintf(__('Email: %s', 'submittal-spec-sheet-builder'), $customer_email);

    if (!empty($phone)) {
      /* translators: %s: customer phone number */
      $lead_details[] = sprintf(__('Phone: %s', 'submittal-spec-sheet-builder'), $phone);
    }

    $lead_details[] = '';
    $lead_details[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━';
    $lead_details[] = __('REQUEST DETAILS:', 'submittal-spec-sheet-builder');
    $lead_details[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━';

    if (!empty($project_name)) {
      /* translators: %s: project name */
      $lead_details[] = sprintf(__('Project Name: %s', 'submittal-spec-sheet-builder'), $project_name);
    }

    if ($num_items > 0) {
      /* translators: %d: number of products selected */
      $lead_details[] = sprintf(__('Products Selected: %d', 'submittal-spec-sheet-builder'), $num_items);
    }

    if (!empty($top_category)) {
      /* translators: %s: primary product category name */
      $lead_details[] = sprintf(__('Primary Category: %s', 'submittal-spec-sheet-builder'), $top_category);
    }

    /* translators: %s: submission date and time */
    $lead_details[] = sprintf(__('Submitted: %s', 'submittal-spec-sheet-builder'), current_time('F j, Y g:i a'));

    $lead_details[] = '';
    $lead_details[] = '━━━━━━━━━━━━━━━━━━━━━━━━━━━━';

    // Build complete message
    /* translators: 1: lead details, 2: customer email, 3: dashboard URL, 4: company name */
    $message = sprintf(
      __('You\'ve received a new lead from your submittal builder!

%1$s

NEXT STEPS:
→ Reply to this lead at: %2$s
→ View all leads in your dashboard: %3$s

---
%4$s', 'submittal-spec-sheet-builder'),
      implode("\n", $lead_details),
      $customer_email,
      admin_url('admin.php?page=submittal-builder-leads'),
      $company_name
    );

    // Set headers
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    // Set Reply-To as the customer's email for easy response
    $headers[] = 'Reply-To: ' . $customer_email;

    // Send email to admin/notification address
    $sent = wp_mail($notification_email, $subject, $message, $headers);

    if (!$sent) {
      error_log('SFB Lead Capture: Failed to send notification email to ' . $notification_email);
    } else {
      error_log('SFB Lead Capture: Lead notification sent to ' . $notification_email . ' for customer: ' . $customer_email);
    }

    return $sent;
  }

  /**
   * Rate limiting - max 5 submissions per hour per email or IP
   */
  private static function check_rate_limit(string $email): array {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';
    $ip_hash = hash('sha256', self::get_client_ip());
    $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

    // Count submissions in the last hour
    $count = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM $table
       WHERE (email = %s OR ip_hash = %s)
       AND created_at > %s",
      $email,
      $ip_hash,
      $one_hour_ago
    ));

    $limit = 5; // Max 5 per hour
    $allowed = ($count < $limit);

    return [
      'allowed' => $allowed,
      'count' => (int) $count,
      'limit' => $limit,
      'retry_after' => $allowed ? 0 : 3600, // seconds
    ];
  }

  /**
   * Get client IP address (handles proxies)
   */
  private static function get_client_ip(): string {
    $ip_keys = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR'
    ];

    foreach ($ip_keys as $key) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          $ip = trim($ip);
          if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return $ip;
          }
        }
      }
    }

    return '0.0.0.0';
  }

  /**
   * Get all leads (for admin page)
   */
  public static function get_leads(int $limit = 100, int $offset = 0): array {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';

    $results = $wpdb->get_results($wpdb->prepare(
      "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
      $limit,
      $offset
    ), ARRAY_A);

    return $results ?: [];
  }

  /**
   * Get total lead count
   */
  public static function get_total_leads(): int {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
  }

  /**
   * Export leads to CSV
   */
  public static function export_csv() {
    if (!current_user_can('manage_options')) {
      wp_die('Unauthorized', 'Error', ['response' => 403]);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';
    $leads = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A);

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sfb-leads-' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, ['ID', 'Created At', 'Email', 'Phone', 'Project Name', 'Num Items', 'Top Category', 'Consent', 'UTM Data', 'IP Hash']);

    // CSV rows
    foreach ($leads as $lead) {
      fputcsv($output, [
        $lead['id'],
        $lead['created_at'],
        $lead['email'],
        $lead['phone'],
        $lead['project_name'],
        $lead['num_items'],
        $lead['top_category'],
        $lead['consent'] ? 'Yes' : 'No',
        $lead['utm_json'],
        $lead['ip_hash'],
      ]);
    }

    fclose($output);
    exit;
  }
}
