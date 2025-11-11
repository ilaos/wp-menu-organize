<?php
/**
 * Agency Analytics - Data Collection & Reporting (Agency Feature)
 *
 * Handles lightweight analytics event tracking for Agency tier:
 * - PDF generation events
 * - Lead capture events
 * - Daily heartbeat pings
 * - Local storage with optional remote aggregation
 *
 * @package SubmittalBuilder
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class SFB_Agency_Analytics {

  /**
   * Initialize analytics hooks
   */
  public static function init() {
    // Only initialize for Agency license holders
    if (!sfb_is_agency_license()) {
      return;
    }

    // Schedule daily heartbeat
    add_action('wp', [__CLASS__, 'schedule_heartbeat']);
    add_action('sfb_analytics_heartbeat', [__CLASS__, 'send_heartbeat']);
  }

  /**
   * Get site identifier (consistent hash)
   *
   * @return string Hashed site identifier
   */
  public static function get_site_id() {
    $site_url = get_site_url();
    return hash('sha256', $site_url);
  }

  /**
   * Track PDF generation event
   *
   * @param array $product_ids Array of product IDs in the PDF
   * @return void
   */
  public static function track_pdf_generated($product_ids = []) {
    // Store locally
    self::store_local_event('pdf_generated', [
      'product_ids' => $product_ids,
      'product_count' => count($product_ids),
    ]);

    // Send to remote aggregator (non-blocking)
    self::send_remote_event('pdf_generated', [
      'count' => 1,
      'product_count' => count($product_ids),
    ]);
  }

  /**
   * Track lead capture event
   *
   * @param array $lead_data Lead data (non-PII only)
   * @return void
   */
  public static function track_lead_captured($lead_data = []) {
    // Store locally (no PII)
    self::store_local_event('lead_created', [
      'has_phone' => !empty($lead_data['phone']),
      'num_items' => $lead_data['num_items'] ?? 0,
      'top_category' => $lead_data['top_category'] ?? '',
    ]);

    // Send to remote aggregator (non-blocking, counts only)
    self::send_remote_event('lead_created', [
      'count' => 1,
    ]);
  }

  /**
   * Store event locally for analytics
   *
   * @param string $event_type Event type (pdf_generated, lead_created)
   * @param array $data Event data
   * @return void
   */
  private static function store_local_event($event_type, $data) {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_analytics_events';

    // Create table if it doesn't exist
    self::ensure_analytics_table();

    // Insert event
    $wpdb->insert(
      $table,
      [
        'site_id' => self::get_site_id(),
        'event_type' => $event_type,
        'event_data' => wp_json_encode($data),
        'created_at' => current_time('mysql'),
      ],
      ['%s', '%s', '%s', '%s']
    );
  }

  /**
   * Send event to remote aggregator (non-blocking)
   *
   * @param string $event_type Event type
   * @param array $data Event data (counts only, no PII)
   * @return void
   */
  private static function send_remote_event($event_type, $data) {
    // Get license server endpoint (if configured)
    $license_data = get_option('sfb_license', []);
    $aggregator_url = $license_data['aggregator_url'] ?? '';

    // Fall back to local-only if no aggregator configured
    if (empty($aggregator_url)) {
      return;
    }

    // Prepare payload
    $payload = [
      'site_id' => self::get_site_id(),
      'site_url' => get_site_url(),
      'version' => defined('SFB_VERSION') ? SFB_VERSION : '1.0.0',
      'event' => $event_type,
      'timestamp' => current_time('mysql'),
      'data' => $data,
    ];

    // Send async (non-blocking)
    wp_remote_post($aggregator_url . '/analytics/event', [
      'timeout' => 2,
      'blocking' => false,
      'body' => wp_json_encode($payload),
      'headers' => [
        'Content-Type' => 'application/json',
        'X-SFB-License-Key' => $license_data['key'] ?? '',
      ],
    ]);
  }

  /**
   * Schedule daily heartbeat
   */
  public static function schedule_heartbeat() {
    if (!wp_next_scheduled('sfb_analytics_heartbeat')) {
      wp_schedule_event(time(), 'daily', 'sfb_analytics_heartbeat');
    }
  }

  /**
   * Send daily heartbeat ping
   */
  public static function send_heartbeat() {
    // Store locally
    self::store_local_event('heartbeat', [
      'version' => defined('SFB_VERSION') ? SFB_VERSION : '1.0.0',
      'php_version' => phpversion(),
      'wp_version' => get_bloginfo('version'),
    ]);

    // Send to remote aggregator
    $license_data = get_option('sfb_license', []);
    $aggregator_url = $license_data['aggregator_url'] ?? '';

    if (empty($aggregator_url)) {
      return;
    }

    $payload = [
      'site_id' => self::get_site_id(),
      'site_url' => get_site_url(),
      'version' => defined('SFB_VERSION') ? SFB_VERSION : '1.0.0',
      'timestamp' => current_time('mysql'),
      'php_version' => phpversion(),
      'wp_version' => get_bloginfo('version'),
    ];

    wp_remote_post($aggregator_url . '/analytics/heartbeat', [
      'timeout' => 5,
      'blocking' => false,
      'body' => wp_json_encode($payload),
      'headers' => [
        'Content-Type' => 'application/json',
        'X-SFB-License-Key' => $license_data['key'] ?? '',
      ],
    ]);
  }

  /**
   * Get analytics data for date range
   *
   * @param int $days Number of days to look back (7, 30, 90)
   * @return array Analytics data
   */
  public static function get_analytics($days = 30) {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_analytics_events';

    // Ensure table exists
    self::ensure_analytics_table();

    $cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));

    // Get PDF count
    $pdf_count = (int) $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM $table WHERE event_type = 'pdf_generated' AND created_at >= %s",
      $cutoff_date
    ));

    // Get lead count
    $lead_count = (int) $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM $table WHERE event_type = 'lead_created' AND created_at >= %s",
      $cutoff_date
    ));

    // Get top products (from PDF events)
    $product_data = $wpdb->get_results($wpdb->prepare(
      "SELECT event_data FROM $table WHERE event_type = 'pdf_generated' AND created_at >= %s",
      $cutoff_date
    ), ARRAY_A);

    $product_counts = [];
    foreach ($product_data as $row) {
      $data = json_decode($row['event_data'], true);
      $product_ids = $data['product_ids'] ?? [];
      foreach ($product_ids as $product_id) {
        if (!isset($product_counts[$product_id])) {
          $product_counts[$product_id] = 0;
        }
        $product_counts[$product_id]++;
      }
    }

    // Sort by count and get top 5
    arsort($product_counts);
    $top_product_ids = array_slice(array_keys($product_counts), 0, 5);

    // Get product names
    $nodes_table = $wpdb->prefix . 'sfb_nodes';
    $top_products = [];
    foreach ($top_product_ids as $product_id) {
      $product = $wpdb->get_row($wpdb->prepare(
        "SELECT id, title FROM $nodes_table WHERE id = %d",
        $product_id
      ), ARRAY_A);
      if ($product) {
        $top_products[] = [
          'id' => $product['id'],
          'title' => $product['title'],
          'count' => $product_counts[$product_id],
        ];
      }
    }

    // Get last heartbeat
    $last_heartbeat = $wpdb->get_var(
      "SELECT MAX(created_at) FROM $table WHERE event_type = 'heartbeat'"
    );

    // Get version from last heartbeat
    $version_data = $wpdb->get_var(
      "SELECT event_data FROM $table WHERE event_type = 'heartbeat' ORDER BY created_at DESC LIMIT 1"
    );
    $version = '1.0.0';
    if ($version_data) {
      $decoded = json_decode($version_data, true);
      $version = $decoded['version'] ?? '1.0.0';
    }

    return [
      'site_id' => self::get_site_id(),
      'site_url' => get_site_url(),
      'pdf_count' => $pdf_count,
      'lead_count' => $lead_count,
      'top_products' => $top_products,
      'last_heartbeat' => $last_heartbeat,
      'version' => $version,
    ];
  }

  /**
   * Ensure analytics table exists
   */
  private static function ensure_analytics_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_analytics_events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      site_id varchar(64) NOT NULL,
      event_type varchar(50) NOT NULL,
      event_data longtext,
      created_at datetime NOT NULL,
      PRIMARY KEY (id),
      KEY site_id (site_id),
      KEY event_type (event_type),
      KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
