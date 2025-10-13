<?php
/**
 * Agency Lead Routing - Rules Engine + Generic Webhook (Agency Feature)
 *
 * Handles automated lead routing based on configurable rules:
 * - Email domain matching
 * - UTM parameter matching
 * - Top category matching
 * - Email delivery to rule recipients
 * - Webhook delivery with retry mechanism
 * - Delivery logging
 *
 * @package SubmittalBuilder
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class SFB_Agency_Lead_Routing {

  /**
   * Initialize routing hooks
   */
  public static function init() {
    // Only initialize for Agency license holders
    if (!sfb_is_agency_license()) {
      return;
    }

    // Hook into lead capture
    add_action('sfb_lead_captured', [__CLASS__, 'process_lead_routing'], 10, 3);

    // Schedule webhook retries
    add_action('sfb_retry_webhook_delivery', [__CLASS__, 'retry_webhook_delivery'], 10, 3);
  }

  /**
   * Check if lead routing is enabled
   */
  public static function is_enabled(): bool {
    if (!sfb_is_agency_license()) {
      return false;
    }
    return (bool) get_option('sfb_lead_routing_enabled', false);
  }

  /**
   * Get all routing rules
   *
   * @return array Array of rule objects
   */
  public static function get_rules(): array {
    $rules = get_option('sfb_lead_routing_rules', []);
    return is_array($rules) ? $rules : [];
  }

  /**
   * Get fallback configuration
   *
   * @return array Fallback email and webhook
   */
  public static function get_fallback(): array {
    $fallback = get_option('sfb_lead_routing_fallback', [
      'email' => '',
      'webhook' => '',
    ]);
    return is_array($fallback) ? $fallback : ['email' => '', 'webhook' => ''];
  }

  /**
   * Save routing rules
   *
   * @param array $rules Array of rule objects
   * @return bool Success
   */
  public static function save_rules(array $rules): bool {
    // Validate and sanitize each rule
    $sanitized = [];
    foreach ($rules as $rule) {
      $sanitized[] = self::sanitize_rule($rule);
    }
    return update_option('sfb_lead_routing_rules', $sanitized);
  }

  /**
   * Save fallback configuration
   *
   * @param array $fallback Fallback email and webhook
   * @return bool Success
   */
  public static function save_fallback(array $fallback): bool {
    $sanitized = [
      'email' => sanitize_text_field($fallback['email'] ?? ''),
      'webhook' => esc_url_raw($fallback['webhook'] ?? ''),
    ];
    return update_option('sfb_lead_routing_fallback', $sanitized);
  }

  /**
   * Sanitize a single rule
   *
   * @param array $rule Raw rule data
   * @return array Sanitized rule
   */
  private static function sanitize_rule(array $rule): array {
    return [
      'id' => sanitize_text_field($rule['id'] ?? uniqid('rule_')),
      'name' => sanitize_text_field($rule['name'] ?? ''),
      'email_domains' => sanitize_text_field($rule['email_domains'] ?? ''),
      'utm_source' => sanitize_text_field($rule['utm_source'] ?? ''),
      'utm_medium' => sanitize_text_field($rule['utm_medium'] ?? ''),
      'utm_campaign' => sanitize_text_field($rule['utm_campaign'] ?? ''),
      'top_category' => sanitize_text_field($rule['top_category'] ?? ''),
      'then_email' => sanitize_text_field($rule['then_email'] ?? ''),
      'then_webhook' => esc_url_raw($rule['then_webhook'] ?? ''),
      'enabled' => !empty($rule['enabled']),
    ];
  }

  /**
   * Process lead routing (called after lead capture)
   *
   * @param int $lead_id Lead database ID
   * @param string $email Lead email address
   * @param array $lead_data Additional lead data
   */
  public static function process_lead_routing(int $lead_id, string $email, array $lead_data) {
    // Only run if routing is enabled
    if (!self::is_enabled()) {
      return;
    }

    // Check if this lead has already been routed (de-dup)
    $routed_leads = get_option('sfb_routed_lead_ids', []);
    if (in_array($lead_id, $routed_leads, true)) {
      return;
    }

    // Build context
    $context = self::build_context($lead_id, $email, $lead_data);

    // Evaluate rules
    $matched_rule = self::evaluate_rules($context);

    // Route the lead
    if ($matched_rule) {
      self::route_lead($context, $matched_rule);
    } else {
      // Try fallback
      $fallback = self::get_fallback();
      if (!empty($fallback['email']) || !empty($fallback['webhook'])) {
        self::route_lead($context, [
          'name' => 'fallback',
          'then_email' => $fallback['email'],
          'then_webhook' => $fallback['webhook'],
        ], false);
      }
    }

    // Mark lead as routed
    $routed_leads[] = $lead_id;
    update_option('sfb_routed_lead_ids', $routed_leads);
  }

  /**
   * Build routing context from lead data
   *
   * @param int $lead_id Lead database ID
   * @param string $email Lead email address
   * @param array $lead_data Additional lead data
   * @return array Context object
   */
  private static function build_context(int $lead_id, string $email, array $lead_data): array {
    // Get UTM data
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';
    $lead = $wpdb->get_row($wpdb->prepare(
      "SELECT * FROM $table WHERE id = %d",
      $lead_id
    ), ARRAY_A);

    $utm_data = [];
    if ($lead && !empty($lead['utm_json'])) {
      $utm_data = json_decode($lead['utm_json'], true) ?: [];
    }

    return [
      'lead_id' => $lead_id,
      'email' => $email,
      'email_domain' => self::extract_domain($email),
      'phone' => $lead_data['phone'] ?? '',
      'project_name' => $lead_data['project_name'] ?? '',
      'num_items' => $lead_data['num_items'] ?? 0,
      'top_category' => $lead_data['top_category'] ?? '',
      'utm' => [
        'source' => $utm_data['source'] ?? '',
        'medium' => $utm_data['medium'] ?? '',
        'campaign' => $utm_data['campaign'] ?? '',
        'term' => $utm_data['term'] ?? '',
        'content' => $utm_data['content'] ?? '',
      ],
      'created_at' => $lead['created_at'] ?? current_time('mysql'),
      'site_url' => get_site_url(),
      'site_name' => get_bloginfo('name'),
    ];
  }

  /**
   * Extract domain from email address
   *
   * @param string $email Email address
   * @return string Domain (lowercase)
   */
  private static function extract_domain(string $email): string {
    $parts = explode('@', $email);
    return isset($parts[1]) ? strtolower($parts[1]) : '';
  }

  /**
   * Evaluate rules and return first match
   *
   * @param array $context Routing context
   * @return array|null Matched rule or null
   */
  private static function evaluate_rules(array $context): ?array {
    $rules = self::get_rules();

    foreach ($rules as $rule) {
      // Skip disabled rules
      if (empty($rule['enabled'])) {
        continue;
      }

      if (self::rule_matches($rule, $context)) {
        return $rule;
      }
    }

    return null;
  }

  /**
   * Check if a rule matches the context
   *
   * @param array $rule Rule configuration
   * @param array $context Routing context
   * @return bool True if rule matches
   */
  private static function rule_matches(array $rule, array $context): bool {
    $matched = false;

    // Email domain matching
    if (!empty($rule['email_domains'])) {
      $domains = array_map('trim', explode(',', strtolower($rule['email_domains'])));
      foreach ($domains as $domain) {
        if (!empty($domain) && strpos($context['email_domain'], $domain) !== false) {
          $matched = true;
          break;
        }
      }
    }

    // UTM source matching
    if (!empty($rule['utm_source'])) {
      $sources = array_map('trim', explode(',', strtolower($rule['utm_source'])));
      foreach ($sources as $source) {
        if (!empty($source) && stripos($context['utm']['source'], $source) !== false) {
          $matched = true;
          break;
        }
      }
    }

    // UTM medium matching
    if (!empty($rule['utm_medium'])) {
      $mediums = array_map('trim', explode(',', strtolower($rule['utm_medium'])));
      foreach ($mediums as $medium) {
        if (!empty($medium) && stripos($context['utm']['medium'], $medium) !== false) {
          $matched = true;
          break;
        }
      }
    }

    // UTM campaign matching
    if (!empty($rule['utm_campaign'])) {
      $campaigns = array_map('trim', explode(',', strtolower($rule['utm_campaign'])));
      foreach ($campaigns as $campaign) {
        if (!empty($campaign) && stripos($context['utm']['campaign'], $campaign) !== false) {
          $matched = true;
          break;
        }
      }
    }

    // Top category matching (exact match)
    if (!empty($rule['top_category']) && !empty($context['top_category'])) {
      if ($rule['top_category'] === $context['top_category']) {
        $matched = true;
      }
    }

    return $matched;
  }

  /**
   * Route lead to email and/or webhook
   *
   * @param array $context Routing context
   * @param array $rule Matched rule
   * @param bool $is_rule True if actual rule, false if fallback
   */
  private static function route_lead(array $context, array $rule, bool $is_rule = true) {
    $log_entry = [
      'timestamp' => current_time('mysql'),
      'lead_id' => $context['lead_id'],
      'rule_name' => $rule['name'],
      'is_rule' => $is_rule,
      'email_status' => null,
      'webhook_status' => null,
      'webhook_code' => null,
    ];

    // Email routing
    if (!empty($rule['then_email'])) {
      $result = self::send_routing_email($context, $rule);
      $log_entry['email_status'] = $result ? 'success' : 'failed';
    }

    // Webhook routing
    if (!empty($rule['then_webhook'])) {
      $result = self::send_webhook($context, $rule);
      $log_entry['webhook_status'] = $result['status'];
      $log_entry['webhook_code'] = $result['code'];
    }

    // Log the delivery
    self::add_log_entry($log_entry);
  }

  /**
   * Send routing email
   *
   * @param array $context Routing context
   * @param array $rule Matched rule
   * @return bool Success
   */
  private static function send_routing_email(array $context, array $rule): bool {
    // Parse recipients
    $recipients = array_map('trim', explode(',', $rule['then_email']));
    $recipients = array_filter($recipients, 'is_email');

    if (empty($recipients)) {
      return false;
    }

    // Get branding settings for white-label
    $brand_settings = sfb_get_brand_settings();

    // Apply white-label email settings if enabled
    if (sfb_is_white_label_enabled()) {
      add_filter('wp_mail_from_name', function($from_name) use ($brand_settings) {
        $custom_from_name = $brand_settings['white_label']['email_from_name'] ?? '';
        return !empty($custom_from_name) ? $custom_from_name : $from_name;
      }, 999);

      add_filter('wp_mail_from', function($from_email) use ($brand_settings) {
        $custom_from_address = $brand_settings['white_label']['email_from_address'] ?? '';
        return !empty($custom_from_address) && is_email($custom_from_address) ? $custom_from_address : $from_email;
      }, 999);
    }

    // Build subject
    $project = !empty($context['project_name']) ? $context['project_name'] : $context['email'];
    $subject = sprintf('[%s] New Lead — %s', $context['site_name'], $project);

    // Build body
    $message = sprintf(
      "New lead captured via Submittal Builder:\n\n" .
      "Email: %s\n" .
      "Phone: %s\n" .
      "Project: %s\n" .
      "Products Selected: %d\n" .
      "Top Category: %s\n\n" .
      "UTM Source: %s\n" .
      "UTM Medium: %s\n" .
      "UTM Campaign: %s\n\n" .
      "Captured: %s\n" .
      "Site: %s\n\n" .
      "---\n" .
      "Routed by rule: %s\n",
      $context['email'],
      $context['phone'] ?: 'Not provided',
      $context['project_name'] ?: 'Not provided',
      $context['num_items'],
      $context['top_category'] ?: 'Not specified',
      $context['utm']['source'] ?: 'Direct',
      $context['utm']['medium'] ?: 'None',
      $context['utm']['campaign'] ?: 'None',
      $context['created_at'],
      $context['site_url'],
      $rule['name']
    );

    // Add subtle branding credit for white-label if enabled
    if (sfb_is_white_label_enabled()) {
      $credit = sfb_brand_credit_plain('email');
      if (!empty($credit)) {
        $message .= "\n" . $credit;
      }
    } else {
      $message .= "\n" . sfb_brand_credit_plain('email');
    }

    // Send email
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    $sent = wp_mail($recipients, $subject, $message, $headers);

    if (!$sent) {
      error_log('SFB Lead Routing: Failed to send email to ' . implode(', ', $recipients));
    }

    return $sent;
  }

  /**
   * Send webhook with retry on failure
   *
   * @param array $context Routing context
   * @param array $rule Matched rule
   * @param int $attempt Current attempt number (1-4)
   * @return array Status and HTTP code
   */
  public static function send_webhook(array $context, array $rule, int $attempt = 1): array {
    $webhook_url = $rule['then_webhook'];

    // Validate HTTPS
    if (strpos($webhook_url, 'https://') !== 0) {
      return ['status' => 'failed', 'code' => 0, 'message' => 'Webhook URL must use HTTPS'];
    }

    // Build payload
    $payload = [
      'event' => 'lead.captured',
      'site' => [
        'url' => $context['site_url'],
        'name' => $context['site_name'],
        'plugin_version' => defined('SFB_VERSION') ? SFB_VERSION : '1.0.0',
      ],
      'lead' => [
        'id' => $context['lead_id'],
        'created_at' => gmdate('Y-m-d\TH:i:s\Z', strtotime($context['created_at'])),
        'email' => $context['email'],
        'phone' => $context['phone'],
        'project_name' => $context['project_name'],
        'num_items' => $context['num_items'],
        'top_category' => $context['top_category'],
        'utm' => $context['utm'],
      ],
      'routing' => [
        'rule_name' => $rule['name'],
        'matched' => $rule['name'] !== 'fallback',
      ],
    ];

    // Send POST request
    $response = wp_remote_post($webhook_url, [
      'timeout' => 15,
      'blocking' => true,
      'body' => wp_json_encode($payload),
      'headers' => [
        'Content-Type' => 'application/json',
        'User-Agent' => 'SubmittalBuilder/' . (defined('SFB_VERSION') ? SFB_VERSION : '1.0.0'),
      ],
    ]);

    // Check response
    if (is_wp_error($response)) {
      $error_message = $response->get_error_message();
      error_log("SFB Lead Routing: Webhook failed (attempt $attempt) - $error_message");

      // Schedule retry if attempts remain
      if ($attempt < 4) {
        self::schedule_retry($context, $rule, $attempt);
      }

      return ['status' => 'failed', 'code' => 0, 'message' => $error_message];
    }

    $code = wp_remote_retrieve_response_code($response);

    // Success is 2xx
    if ($code >= 200 && $code < 300) {
      return ['status' => 'success', 'code' => $code, 'message' => 'OK'];
    }

    // Non-2xx - retry
    error_log("SFB Lead Routing: Webhook returned $code (attempt $attempt)");

    if ($attempt < 4) {
      self::schedule_retry($context, $rule, $attempt);
    }

    return ['status' => 'failed', 'code' => $code, 'message' => "HTTP $code"];
  }

  /**
   * Schedule webhook retry via wp_cron
   *
   * @param array $context Routing context
   * @param array $rule Matched rule
   * @param int $attempt Current attempt number
   */
  private static function schedule_retry(array $context, array $rule, int $attempt) {
    // Backoff: 30s, 2m, 10m
    $delays = [30, 120, 600];
    $delay = $delays[$attempt - 1] ?? 600;

    $timestamp = time() + $delay;

    wp_schedule_single_event($timestamp, 'sfb_retry_webhook_delivery', [
      $context,
      $rule,
      $attempt + 1,
    ]);
  }

  /**
   * Retry webhook delivery (called by wp_cron)
   *
   * @param array $context Routing context
   * @param array $rule Matched rule
   * @param int $attempt Current attempt number
   */
  public static function retry_webhook_delivery(array $context, array $rule, int $attempt) {
    $result = self::send_webhook($context, $rule, $attempt);

    // Update log with retry result
    self::update_log_retry($context['lead_id'], $rule['name'], $result, $attempt);
  }

  /**
   * Add entry to delivery log
   *
   * @param array $entry Log entry data
   */
  private static function add_log_entry(array $entry) {
    $log = get_option('sfb_lead_routing_log', []);

    // Prepend new entry
    array_unshift($log, $entry);

    // Keep only last 20
    $log = array_slice($log, 0, 20);

    update_option('sfb_lead_routing_log', $log);
  }

  /**
   * Update log entry with retry result
   *
   * @param int $lead_id Lead ID
   * @param string $rule_name Rule name
   * @param array $result Webhook result
   * @param int $attempt Attempt number
   */
  private static function update_log_retry(int $lead_id, string $rule_name, array $result, int $attempt) {
    $log = get_option('sfb_lead_routing_log', []);

    // Find the entry for this lead/rule
    foreach ($log as &$entry) {
      if ($entry['lead_id'] === $lead_id && $entry['rule_name'] === $rule_name) {
        $entry['webhook_status'] = $result['status'];
        $entry['webhook_code'] = $result['code'];
        $entry['webhook_attempts'] = $attempt;
        $entry['last_retry'] = current_time('mysql');
        break;
      }
    }

    update_option('sfb_lead_routing_log', $log);
  }

  /**
   * Get delivery log
   *
   * @param int $limit Number of entries to return
   * @return array Log entries
   */
  public static function get_log(int $limit = 20): array {
    $log = get_option('sfb_lead_routing_log', []);
    return array_slice($log, 0, $limit);
  }

  /**
   * Clear delivery log
   *
   * @return bool Success
   */
  public static function clear_log(): bool {
    return update_option('sfb_lead_routing_log', []);
  }

  /**
   * Test routing with last captured lead
   *
   * @param array $rule Rule to test
   * @return array Test results
   */
  public static function test_rule(array $rule): array {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';

    // Get last lead
    $lead = $wpdb->get_row(
      "SELECT * FROM $table ORDER BY created_at DESC LIMIT 1",
      ARRAY_A
    );

    if (!$lead) {
      return [
        'success' => false,
        'message' => 'No leads found in database. Capture a lead first to test routing.',
      ];
    }

    // Build context
    $utm_data = json_decode($lead['utm_json'] ?? '{}', true) ?: [];
    $context = [
      'lead_id' => $lead['id'],
      'email' => $lead['email'],
      'email_domain' => self::extract_domain($lead['email']),
      'phone' => $lead['phone'],
      'project_name' => $lead['project_name'],
      'num_items' => $lead['num_items'],
      'top_category' => $lead['top_category'],
      'utm' => [
        'source' => $utm_data['source'] ?? '',
        'medium' => $utm_data['medium'] ?? '',
        'campaign' => $utm_data['campaign'] ?? '',
        'term' => $utm_data['term'] ?? '',
        'content' => $utm_data['content'] ?? '',
      ],
      'created_at' => $lead['created_at'],
      'site_url' => get_site_url(),
      'site_name' => get_bloginfo('name'),
    ];

    // Test matching
    $matches = self::rule_matches($rule, $context);

    return [
      'success' => true,
      'matches' => $matches,
      'lead' => [
        'email' => $lead['email'],
        'project_name' => $lead['project_name'],
        'top_category' => $lead['top_category'],
        'created_at' => $lead['created_at'],
      ],
      'context' => $context,
    ];
  }

  /**
   * Get all unique top categories from captured leads
   *
   * @return array Array of category names
   */
  public static function get_top_categories(): array {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';

    $results = $wpdb->get_col(
      "SELECT DISTINCT top_category FROM $table
       WHERE top_category IS NOT NULL AND top_category != ''
       ORDER BY top_category ASC"
    );

    return $results ?: [];
  }
}
