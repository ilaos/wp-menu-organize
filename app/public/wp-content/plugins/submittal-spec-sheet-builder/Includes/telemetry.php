<?php
/**
 * Telemetry - Anonymous Usage Analytics (Opt-In)
 *
 * Collects anonymous usage data to help improve the plugin:
 * - WordPress/PHP versions (for compatibility)
 * - Feature usage (which features are actually used)
 * - Environment data (for optimization)
 * - License tier (to understand upgrade paths)
 *
 * Privacy-first:
 * - Opt-in only (disabled by default)
 * - No personal data collected
 * - Site URLs are hashed (not stored in plain text)
 * - User can opt-out anytime
 *
 * @package SubmittalBuilder
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

class SFB_Telemetry {

  /**
   * API endpoint for telemetry data
   */
  const API_ENDPOINT = 'https://api.webstuffguylabs.com/v1/telemetry'; // TODO: Update with your actual endpoint

  /**
   * Initialize telemetry hooks
   */
  public static function init() {
    // Admin notice for opt-in (disabled - checkbox now in settings page)
    // add_action('admin_notices', [__CLASS__, 'show_opt_in_notice']);

    // Handle opt-in/opt-out actions
    add_action('admin_init', [__CLASS__, 'handle_opt_in_action']);

    // Schedule weekly ping
    if (!wp_next_scheduled('sfb_telemetry_weekly_ping')) {
      wp_schedule_event(time(), 'weekly', 'sfb_telemetry_weekly_ping');
    }
    add_action('sfb_telemetry_weekly_ping', [__CLASS__, 'send_weekly_ping']);

    // Send initial ping on opt-in
    add_action('sfb_telemetry_opted_in', [__CLASS__, 'send_initial_ping']);

    // Add settings field
    add_action('admin_init', [__CLASS__, 'register_settings']);
  }

  /**
   * Check if telemetry is enabled
   */
  public static function is_enabled(): bool {
    return (bool) get_option('sfb_telemetry_enabled', true);
  }

  /**
   * Show opt-in notice (one time)
   */
  public static function show_opt_in_notice() {
    // Don't show if already decided
    if (get_option('sfb_telemetry_decided')) {
      return;
    }

    // Only show on plugin pages
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'submittal-spec-sheet-builder') === false) {
      return;
    }

    // Only show to admins
    if (!current_user_can('manage_options')) {
      return;
    }

    ?>
    <div class="notice notice-info is-dismissible" id="sfb-telemetry-notice">
      <h3><?php esc_html_e('Help Us Improve Submittal & Spec Sheet Builder', 'submittal-spec-sheet-builder'); ?></h3>

      <p>
        <?php esc_html_e('Would you like to share anonymous usage data to help us understand how the plugin is being used? This helps us prioritize features and improve compatibility.', 'submittal-spec-sheet-builder'); ?>
      </p>

      <p>
        <strong><?php esc_html_e('What we collect:', 'submittal-spec-sheet-builder'); ?></strong>
      </p>
      <ul style="list-style: disc; margin-left: 20px;">
        <li><?php esc_html_e('WordPress and PHP versions', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Plugin version and license tier', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Feature usage (which features you use)', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Product catalog size (total count)', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Server environment (memory limit, MySQL version)', 'submittal-spec-sheet-builder'); ?></li>
      </ul>

      <p>
        <strong><?php esc_html_e('What we DON\'T collect:', 'submittal-spec-sheet-builder'); ?></strong>
      </p>
      <ul style="list-style: disc; margin-left: 20px;">
        <li><?php esc_html_e('Your site URL (we use a privacy-safe hash)', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Your email address or personal information', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Your product data or customer information', 'submittal-spec-sheet-builder'); ?></li>
        <li><?php esc_html_e('Any content from your site', 'submittal-spec-sheet-builder'); ?></li>
      </ul>

      <p>
        <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('sfb_telemetry_action', 'opt_in'), 'sfb_telemetry_opt_in')); ?>"
           class="button button-primary">
          <?php esc_html_e('Yes, help improve the plugin', 'submittal-spec-sheet-builder'); ?>
        </a>

        <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('sfb_telemetry_action', 'opt_out'), 'sfb_telemetry_opt_out')); ?>"
           class="button">
          <?php esc_html_e('No thanks', 'submittal-spec-sheet-builder'); ?>
        </a>

        <a href="https://webstuffguylabs.com/privacy-policy" target="_blank" style="margin-left: 10px;">
          <?php esc_html_e('Privacy Policy', 'submittal-spec-sheet-builder'); ?>
        </a>
      </p>
    </div>
    <?php
  }

  /**
   * Handle opt-in/opt-out actions
   */
  public static function handle_opt_in_action() {
    if (!isset($_GET['sfb_telemetry_action'])) {
      return;
    }

    $action = sanitize_text_field($_GET['sfb_telemetry_action']);

    // Opt in
    if ($action === 'opt_in' && check_admin_referer('sfb_telemetry_opt_in')) {
      update_option('sfb_telemetry_enabled', true);
      update_option('sfb_telemetry_decided', true);
      update_option('sfb_telemetry_opted_in_date', current_time('mysql'));

      // Trigger action for initial ping
      do_action('sfb_telemetry_opted_in');

      wp_redirect(remove_query_arg(['sfb_telemetry_action', '_wpnonce']));
      exit;
    }

    // Opt out
    if ($action === 'opt_out' && check_admin_referer('sfb_telemetry_opt_out')) {
      update_option('sfb_telemetry_enabled', false);
      update_option('sfb_telemetry_decided', true);

      wp_redirect(remove_query_arg(['sfb_telemetry_action', '_wpnonce']));
      exit;
    }
  }

  /**
   * Register settings (for settings page)
   */
  public static function register_settings() {
    register_setting('sfb_settings_group', 'sfb_telemetry_enabled', [
      'type' => 'boolean',
      'default' => true,
      'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
  }

  /**
   * Send initial ping when user opts in
   */
  public static function send_initial_ping() {
    self::send_telemetry_data();
  }

  /**
   * Send weekly ping (scheduled)
   */
  public static function send_weekly_ping() {
    if (!self::is_enabled()) {
      return;
    }

    self::send_telemetry_data();
  }

  /**
   * Collect and send telemetry data
   */
  private static function send_telemetry_data() {
    $data = self::collect_data();

    // Send to API endpoint
    $response = wp_remote_post(self::API_ENDPOINT, [
      'timeout' => 10,
      'blocking' => false, // Non-blocking (fire and forget)
      'body' => wp_json_encode($data),
      'headers' => [
        'Content-Type' => 'application/json',
        'User-Agent' => 'SubmittalBuilder/' . (defined('SFB_VERSION') ? SFB_VERSION : '1.0.0'),
      ],
    ]);

    // Log last ping time (for debugging)
    update_option('sfb_telemetry_last_ping', current_time('mysql'));

    // Log if error (don't block execution)
    if (is_wp_error($response)) {
      error_log('SFB Telemetry: Failed to send data - ' . $response->get_error_message());
    }
  }

  /**
   * Collect telemetry data
   *
   * @return array Anonymous usage data
   */
  private static function collect_data(): array {
    global $wpdb;

    return [
      // Privacy-safe site identifier
      'site_hash' => self::get_site_hash(),

      // Plugin info
      'plugin_version' => defined('SFB_VERSION') ? SFB_VERSION : 'unknown',
      'license_tier' => self::get_license_tier(),
      'opted_in_date' => get_option('sfb_telemetry_opted_in_date', ''),

      // WordPress environment
      'wp_version' => get_bloginfo('version'),
      'wp_language' => get_locale(),
      'wp_multisite' => is_multisite(),
      'wp_debug_mode' => defined('WP_DEBUG') && WP_DEBUG,

      // PHP environment
      'php_version' => PHP_VERSION,
      'php_memory_limit' => ini_get('memory_limit'),
      'php_max_execution_time' => ini_get('max_execution_time'),
      'php_upload_max_filesize' => ini_get('upload_max_filesize'),

      // Server environment
      'mysql_version' => $wpdb->db_version(),
      'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',

      // Feature usage
      'features_enabled' => self::get_enabled_features(),

      // Catalog data (counts only, no actual data)
      'catalog_stats' => self::get_catalog_stats(),

      // Usage metrics
      'usage_stats' => self::get_usage_stats(),

      // Timestamp
      'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
      'timezone' => wp_timezone_string(),
    ];
  }

  /**
   * Get privacy-safe site hash
   *
   * @return string SHA-256 hash of site URL
   */
  private static function get_site_hash(): string {
    $site_url = get_site_url();
    return hash('sha256', $site_url);
  }

  /**
   * Get license tier
   *
   * @return string free|pro|agency
   */
  private static function get_license_tier(): string {
    if (function_exists('sfb_is_agency_license') && sfb_is_agency_license()) {
      return 'agency';
    }
    if (function_exists('sfb_is_pro_license') && sfb_is_pro_license()) {
      return 'pro';
    }
    return 'free';
  }

  /**
   * Get enabled features
   *
   * @return array Feature flags
   */
  private static function get_enabled_features(): array {
    return [
      'lead_capture' => (bool) get_option('sfb_lead_capture_enabled', false),
      'lead_routing' => class_exists('SFB_Agency_Lead_Routing') && SFB_Agency_Lead_Routing::is_enabled(),
      'white_label' => function_exists('sfb_is_white_label_enabled') && sfb_is_white_label_enabled(),
      'tracking' => self::has_tracking_enabled(),
      'brand_presets' => self::count_brand_presets() > 0,
      'weekly_export' => (bool) get_option('sfb_weekly_export_enabled', false),
    ];
  }

  /**
   * Get catalog statistics (counts only)
   *
   * @return array Catalog metrics
   */
  private static function get_catalog_stats(): array {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';

    $stats = [
      'total_nodes' => 0,
      'categories' => 0,
      'products' => 0,
      'types' => 0,
      'subtypes' => 0,
      'models' => 0,
    ];

    // Count by node type
    $counts = $wpdb->get_results(
      "SELECT node_type, COUNT(*) as count FROM $table GROUP BY node_type",
      ARRAY_A
    );

    if ($counts) {
      foreach ($counts as $row) {
        $type = $row['node_type'];
        $count = (int) $row['count'];

        if (isset($stats[$type . 's'])) {
          $stats[$type . 's'] = $count;
        }
        $stats['total_nodes'] += $count;
      }
    }

    return $stats;
  }

  /**
   * Get usage statistics
   *
   * @return array Usage metrics
   */
  private static function get_usage_stats(): array {
    global $wpdb;

    $stats = [
      'total_leads' => 0,
      'total_pdfs_generated' => 0,
      'days_since_activation' => 0,
      'active_users_count' => 0,
    ];

    // Lead count
    $leads_table = $wpdb->prefix . 'sfb_leads';
    if ($wpdb->get_var("SHOW TABLES LIKE '$leads_table'") === $leads_table) {
      $stats['total_leads'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
    }

    // Days since activation (from option)
    $activation_date = get_option('sfb_activation_date');
    if ($activation_date) {
      $stats['days_since_activation'] = (int) ((time() - strtotime($activation_date)) / DAY_IN_SECONDS);
    }

    // Active users (who can manage options)
    $stats['active_users_count'] = count(get_users(['role' => 'administrator']));

    // PDF generation count (if tracked)
    $stats['total_pdfs_generated'] = (int) get_option('sfb_total_pdfs_generated', 0);

    return $stats;
  }

  /**
   * Check if tracking links are enabled
   *
   * @return bool
   */
  private static function has_tracking_enabled(): bool {
    // Check if any tracking links exist
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_tracking';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
      return false;
    }

    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    return (int) $count > 0;
  }

  /**
   * Count brand presets
   *
   * @return int
   */
  private static function count_brand_presets(): int {
    $presets = get_option('sfb_brand_presets', []);
    return is_array($presets) ? count($presets) : 0;
  }

  /**
   * Add telemetry settings to admin page
   *
   * @param string $html Existing settings HTML
   * @return string Modified HTML with telemetry setting
   */
  public static function add_settings_field(string $html): string {
    $enabled = self::is_enabled();
    $last_ping = get_option('sfb_telemetry_last_ping', __('Never', 'submittal-spec-sheet-builder'));

    ob_start();
    ?>
    <div class="sfb-settings-section">
      <h3><?php esc_html_e('Anonymous Usage Tracking', 'submittal-spec-sheet-builder'); ?></h3>

      <label>
        <input
          type="checkbox"
          name="sfb_telemetry_enabled"
          value="1"
          <?php checked($enabled); ?>
        >
        <?php esc_html_e('Become a super contributor by helping us understand how you use our service to enhance your experience and improve our product.', 'submittal-spec-sheet-builder'); ?>
        <a href="https://webstuffguylabs.com/share-usage-data/" target="_blank">
          <?php esc_html_e('Learn more', 'submittal-spec-sheet-builder'); ?>
        </a>
      </label>

      <?php if ($enabled && $last_ping !== __('Never', 'submittal-spec-sheet-builder')): ?>
        <p class="description" style="margin-top: 10px;">
          <?php
          printf(
            esc_html__('Last data sent: %s', 'submittal-spec-sheet-builder'),
            esc_html($last_ping)
          );
          ?>
        </p>
      <?php endif; ?>

      <details style="margin-top: 15px;">
        <summary style="cursor: pointer; color: #2271b1;">
          <?php esc_html_e('What data is collected?', 'submittal-spec-sheet-builder'); ?>
        </summary>
        <ul style="list-style: disc; margin-left: 20px; margin-top: 10px;">
          <li><?php esc_html_e('WordPress and PHP versions', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Plugin version and license tier', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Which features are enabled (lead capture, tracking, etc.)', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Product catalog size (total count only)', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Server environment (memory limit, MySQL version)', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Usage statistics (total PDFs generated, total leads)', 'submittal-spec-sheet-builder'); ?></li>
        </ul>
        <p style="margin-top: 10px;">
          <strong><?php esc_html_e('Your site URL is hashed and never stored in plain text.', 'submittal-spec-sheet-builder'); ?></strong>
        </p>
      </details>
    </div>
    <?php
    return $html . ob_get_clean();
  }

  /**
   * Manual test ping (for debugging)
   */
  public static function test_ping() {
    if (!current_user_can('manage_options')) {
      return;
    }

    $data = self::collect_data();

    echo '<pre>';
    echo 'Telemetry Data Preview (this is what would be sent):' . "\n\n";
    print_r($data);
    echo '</pre>';
  }
}

// Initialize telemetry
SFB_Telemetry::init();
