<?php
/**
 * Plugin Name: Submittal & Spec Sheet Builder
 * Plugin URI:  https://example.com/submittal-builder
 * Description: Generate professional submittal and spec sheet PDFs with full branding, summaries, and TOCs. Perfect for construction, manufacturing, and professional services.
 * Version:     1.2.0
 * Author:      Webstuffguy
 * Author URI:  https://example.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: submittal-builder
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

// Define plugin main file constant for reliable path resolution
if (!defined('SFB_PLUGIN_FILE')) {
  define('SFB_PLUGIN_FILE', __FILE__);
}

// Global toggle for Demo Tools (production: false, development: true)
if (!defined('SFB_SHOW_DEMO_TOOLS')) {
  define('SFB_SHOW_DEMO_TOOLS', false);
}

// Load Pro registry (feature gating)
require_once plugin_dir_path(__FILE__) . 'Includes/pro/registry.php';

// Load centralized external links configuration
require_once plugin_dir_path(__FILE__) . 'includes/admin/links.php';

// Load WooCommerce license API integration
require_once plugin_dir_path(__FILE__) . 'includes/admin/license-api.php';

// Load professional PDF generator
require_once plugin_dir_path(__FILE__) . 'Includes/pdf-generator.php';

// Load branding helpers
require_once plugin_dir_path(__FILE__) . 'Includes/branding-helpers.php';

// Load industry pack helpers
require_once plugin_dir_path(__FILE__) . 'Includes/industry-pack-helpers.php';

// Load agency analytics (Agency feature)
require_once plugin_dir_path(__FILE__) . 'Includes/agency-analytics.php';

// Load agency lead routing (Agency feature)
require_once plugin_dir_path(__FILE__) . 'Includes/agency-lead-routing.php';

// Load lead capture (Pro feature)
require_once plugin_dir_path(__FILE__) . 'Includes/lead-capture.php';

// Phase 1 Refactor: Load modular classes
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-admin.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-render.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-rest.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-pdf.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-ajax.php';

// Phase 7 Refactor: Load admin action classes
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-branding.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-drafts.php';
require_once plugin_dir_path(__FILE__) . 'Includes/class-sfb-tools.php';

/**
 * Helper function to ensure string type (prevents null deprecation warnings in PHP 8.1+)
 * @param mixed $v Value to convert to string
 * @return string Always returns a string, never null
 */
function sfb_text($v): string {
  return is_string($v) ? $v : (string) ($v ?? '');
}

/**
 * Map an array to strings (useful for breadcrumbs, paths, etc.)
 * @param mixed $arr Array to convert
 * @return array Array of strings
 */
function sfb_text_list($arr): array {
  if (!is_array($arr)) return [];
  return array_map(static fn($x) => sfb_text($x), $arr);
}

final class SFB_Plugin {
  const VERSION = '1.2.0';
  private static $instance = null;

  static function instance() { return self::$instance ?: self::$instance = new self; }

  private function __construct() {
    register_activation_hook(__FILE__, [$this, 'activate']);

    // Phase 1 Refactor: These hooks are now handled by modular classes
    // See SFB_Admin::init(), SFB_Render::init(), SFB_Rest::init()
    // add_action('admin_menu', [$this, 'admin_menu']); // Now in SFB_Admin
    // add_shortcode('submittal_builder', [$this, 'shortcode_render']); // Now in SFB_Render
    // add_action('rest_api_init', [$this, 'register_routes']); // Now in SFB_Rest

    add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);
    add_action('template_redirect', [$this, 'handle_tracking_redirect']); // tracking links

    // Admin notices
    // Removed: Client Handoff Mode banner - feature toggle remains functional on Agency page
    // add_action('admin_notices', [$this, 'show_handoff_mode_banner']);

    // Custom capability enforcement
    add_filter('map_meta_cap', [$this, 'map_sfb_capabilities'], 10, 4);

    // Load translations
    add_action('init', [$this, 'load_textdomain']);

    // Bootstrap Pro registry (features + changelog)
    add_action('init', function(){
      // Force feature map & changelog to be constructed and filterable
      sfb_features();
      sfb_bootstrap_changelog();
    });

    // Register custom post type for drafts
    add_action('init', [$this, 'register_draft_cpt']);

    // Schedule cron job for purging expired drafts
    add_action('wp', [$this, 'schedule_draft_purge_cron']);
    add_action('sfb_purge_expired_drafts', [$this, 'purge_expired_drafts']);

    // Settings API for Drafts
    add_action('admin_init', [$this, 'register_draft_settings']);

    // Onboarding: activation redirect
    add_action('admin_init', [$this, 'maybe_redirect_to_onboarding']);

    // Onboarding: form submission handler
    add_action('admin_init', [$this, 'handle_onboarding_setup']);

    // Leads CSV export handler (must run early before any output)
    add_action('admin_init', [$this, 'handle_leads_csv_export']);

    // Weekly Lead Export (Agency feature)
    add_action('wp', [$this, 'schedule_weekly_lead_export_cron']);
    add_action('sfb_weekly_lead_export', [$this, 'cron_send_weekly_export']);
    add_action('wp_ajax_sfb_send_weekly_export_now', [$this, 'ajax_send_weekly_export_now']);

    // Onboarding: welcome notice (dismissible, per-user)
    add_action('admin_notices', [$this, 'show_welcome_notice']);
    // Phase 5 Refactor: AJAX dismiss hook moved to SFB_Ajax::init()

    // License status notices
    add_action('admin_notices', [$this, 'show_license_notices']);

    // Phase 5 Refactor: All AJAX hooks moved to SFB_Ajax class
    // Admin AJAX: sfb_dismiss_welcome, sfb_purge_expired_drafts, sfb_run_smoke_test, sfb_save_brand
    // Frontend AJAX: sfb_list_products, sfb_generate_frontend_pdf (both with nopriv)
    // Lead Capture AJAX: sfb_submit_lead (with nopriv)

    // Test PDF generation handler
    add_action('admin_post_sfb_test_pdf', [$this, 'generate_test_pdf']);

    // Plugin row action links
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links']);

    // Admin bar shortcut
    add_action('admin_bar_menu', [$this, 'admin_bar_menu'], 100);

    // Help tabs
    add_action('load-toplevel_page_sfb', [$this, 'add_help_tabs']);
    add_action('load-submittal-builder_page_sfb-branding', [$this, 'add_help_tabs']);
    add_action('load-submittal-builder_page_sfb-upgrade', [$this, 'add_help_tabs']);
  }

  /** Load plugin translations */
  function load_textdomain() {
    load_plugin_textdomain(
      'submittal-builder',
      false,
      dirname(plugin_basename(__FILE__)) . '/languages/'
    );
  }

  /** Create DB tables we'll use later */
  function activate() {
    $this->ensure_tables();
    $this->ensure_operator_role();
    // Set flag for first-time activation redirect
    update_option('sfb_just_activated', 1, false);
  }

  /** Create SFB Operator role if missing */
  function ensure_operator_role() {
    // Check if role already exists
    if (get_role('sfb_operator')) {
      return; // Already created
    }

    // Create Operator role with limited capabilities
    add_role(
      'sfb_operator',
      __('Submittal Builder Operator', 'submittal-builder'),
      [
        'read' => true, // Basic WordPress capability
        // Custom SFB capabilities (enforced via map_meta_cap)
        'use_sfb_builder' => true,
        'view_sfb_leads' => true,
        'view_sfb_tracking' => true,
        'edit_sfb_branding' => false,
        'edit_sfb_catalog' => false,
        'access_sfb_agency' => false,
      ]
    );

    error_log('[SFB] Created sfb_operator role');
  }

  /** Create SFB tables if missing (safe + idempotent) */
  function ensure_tables() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();

    $forms  = $wpdb->prefix . 'sfb_forms';
    $nodes  = $wpdb->prefix . 'sfb_nodes';
    $leads  = $wpdb->prefix . 'sfb_leads';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // 1) Forms
    $sql_forms = "
      CREATE TABLE $forms (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(190) NOT NULL,
        settings_json LONGTEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
      ) $charset;
    ";
    dbDelta($sql_forms);
    if (!empty($wpdb->last_error)) {
      error_log('SFB ensure_tables FORMS error: ' . $wpdb->last_error);
    }

    // 2) Nodes
    $sql_nodes = "
      CREATE TABLE $nodes (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        form_id BIGINT UNSIGNED NOT NULL,
        parent_id BIGINT UNSIGNED NULL,
        node_type ENUM('category','product','type','model') NOT NULL,
        title VARCHAR(190) NOT NULL,
        slug VARCHAR(190) NULL,
        position DECIMAL(10,2) DEFAULT 0.00,
        settings_json LONGTEXT NULL,
        PRIMARY KEY  (id),
        KEY form_id (form_id),
        KEY parent_id (parent_id),
        KEY node_type (node_type),
        KEY form_parent_pos (form_id, parent_id, position),
        KEY form_type (form_id, node_type)
      ) $charset;
    ";
    dbDelta($sql_nodes);
    if (!empty($wpdb->last_error)) {
      error_log('SFB ensure_tables NODES error: ' . $wpdb->last_error);
    }

    // 3) Shares table REMOVED - Shareable Drafts uses custom post type 'sfb_draft' instead
    // Legacy table removed in v1.0.2 - was created but never used

    // 4) Leads (Pro feature: Lead Capture)
    $sql_leads = "
      CREATE TABLE $leads (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        email VARCHAR(190) NOT NULL,
        phone VARCHAR(50) NULL,
        project_name VARCHAR(190) NULL,
        num_items INT UNSIGNED DEFAULT 0,
        top_category VARCHAR(190) NULL,
        consent TINYINT(1) DEFAULT 0,
        utm_json TEXT NULL,
        ip_hash VARCHAR(64) NULL,
        last_export_sent DATETIME NULL,
        PRIMARY KEY  (id),
        KEY email (email),
        KEY created_at (created_at),
        KEY ip_hash (ip_hash),
        KEY last_export_sent (last_export_sent)
      ) $charset;
    ";
    dbDelta($sql_leads);
    if (!empty($wpdb->last_error)) {
      error_log('SFB ensure_tables LEADS error: ' . $wpdb->last_error);
    }
  }

  /** Handle tracking link redirects */
  function handle_tracking_redirect() {
    if (!isset($_GET['sfb_view'])) return;
    $token = sanitize_text_field($_GET['sfb_view']);
    $all = get_option('sfb_packets', []);
    if (!isset($all[$token])) {
      status_header(404);
      wp_die('Tracking link not found.', 'Not Found', ['response' => 404]);
    }
    $rec = $all[$token];

    // Track this view
    if (!isset($rec['views'])) {
      $rec['views'] = [];
    }
    $rec['views'][] = [
      'timestamp' => current_time('mysql'),
      'ip' => $this->hash_ip($_SERVER['REMOTE_ADDR'] ?? ''),
      'user_agent' => substr(sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255)
    ];

    // Update view count
    if (!isset($rec['view_count'])) {
      $rec['view_count'] = 0;
    }
    $rec['view_count']++;
    $rec['last_viewed'] = current_time('mysql');

    // Save updated record
    $all[$token] = $rec;
    update_option('sfb_packets', $all, false);

    // Redirect to file
    wp_redirect($rec['file']);
    exit;
  }

  /** Hash IP for privacy (SHA-256) */
  private function hash_ip($ip) {
    return hash('sha256', $ip . wp_salt('auth'));
  }

  /** License management */
  function api_get_license() {
    $lic = get_option('sfb_license', []);
    return [
      'ok' => true,
      'license' => [
        'key' => $lic['key'] ?? '',
        'status' => $lic['status'] ?? 'inactive',
      ],
      'pro_active' => sfb_is_pro_active(),
    ];
  }

  function api_save_license($req) {
    $p = $req->get_json_params();
    $key = sanitize_text_field($p['key'] ?? '');

    // Simple activation: any non-empty key = active
    // TODO: Replace with remote validation
    $status = !empty($key) ? 'active' : 'inactive';

    $lic = [
      'key' => $key,
      'status' => $status,
    ];

    update_option('sfb_license', $lic, false);

    return [
      'ok' => true,
      'license' => $lic,
      'pro_active' => sfb_is_pro_active(),
    ];
  }

  /** Branding settings helpers */
  private function option_key(){ return 'sfb_branding'; }
  private function default_settings(){
    return [
      'logo_url' => '',
      'company_name' => '',
      'company_address' => '',
      'company_phone' => '',
      'company_website' => '',
      'primary_color' => '#111827',
      'brand_preset' => 'custom',  // 'modern-blue' | 'architect-gray' | 'engineering-bold' | 'clean-violet' | 'custom'
      'cover_default' => true,
      'footer_text' => 'Generated by Submittal & Spec Builder',
      // NEW: Branding themes + watermark
      'theme' => 'engineering',  // 'engineering' | 'architectural' | 'corporate'
      'watermark' => '',          // optional text watermark; empty disables
      // Draft settings
      'drafts_autosave_enabled' => true,
      'drafts_server_enabled' => sfb_is_pro_active() || defined('SFB_PRO_DEV'),
      'drafts_expiry_days' => 45,
      'drafts_rate_limit_sec' => 20,
      'drafts_privacy_note' => '',
    ];
  }

  // Phase 2 Refactor: These REST handlers moved to SFB_Rest class
  // - api_get_settings() → SFB_Rest::get_settings()
  // - api_save_settings() → SFB_Rest::save_settings()
  // - api_get_status() → SFB_Rest::get_status()


  /** Check if Dompdf is available */
  private function dompdf_available() {
    // 1) Register Dompdf's native autoloader if present
    $auto_native = __DIR__ . '/lib/dompdf/src/Autoloader.php';
    if (file_exists($auto_native)) {
      require_once $auto_native;
      if (class_exists('\\Dompdf\\Autoloader')) {
        \Dompdf\Autoloader::register();
      }
    }

    // 2) Legacy Dompdf autoload (if your package included it)
    $auto_legacy = __DIR__ . '/lib/dompdf/autoload.inc.php';
    if (file_exists($auto_legacy)) {
      require_once $auto_legacy;
    }

    // 3) Register PSR-4 for Masterminds HTML5 (if vendored)
    //    Expected path: lib/masterminds/html5/src/HTML5.php (and friends)
    $mm_src = __DIR__ . '/lib/masterminds/html5/src/';
    if (is_dir($mm_src)) {
      spl_autoload_register(function($class) use ($mm_src) {
        $prefix = 'Masterminds\\HTML5\\';
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
          $rel = substr($class, strlen($prefix));
          $path = $mm_src . str_replace('\\', '/', $rel) . '.php';
          if (file_exists($path)) require_once $path;
        }
      }, true, true);

      // If Dompdf looks for the legacy class "\Masterminds\HTML5",
      // create an alias to the actual "\Masterminds\HTML5\HTML5" class.
      if (!class_exists('\\Masterminds\\HTML5') && class_exists('\\Masterminds\\HTML5\\HTML5')) {
        class_alias('\\Masterminds\\HTML5\\HTML5', '\\Masterminds\\HTML5');
      }
    }

    // 4) Belt & suspenders: globally disable HTML5 parser in older Dompdf builds
    if (!defined('DOMPDF_ENABLE_HTML5PARSER')) {
      define('DOMPDF_ENABLE_HTML5PARSER', false);
    }

    return class_exists('\\Dompdf\\Dompdf');
  }

  /** Admin Menu */
  function admin_menu() {
    // Top-level: points to the Builder page
    $top_slug = 'sfb';
    $capability = 'manage_options';

    add_menu_page(
      'Submittal Builder',
      'Submittal Builder',
      $capability,
      $top_slug,
      [$this, 'render_builder_page'],
      'dashicons-category',
      56
    );

    // 1. Welcome to Submittal Builder (first-run intro)
    add_submenu_page(
      'sfb',
      __('Welcome to Submittal Builder', 'submittal-builder'),
      __('Welcome to Submittal Builder', 'submittal-builder'),
      'manage_options',
      'sfb-onboarding',
      [$this, 'render_onboarding_page'],
      0
    );

    // 2. Tools
    add_submenu_page(
      'sfb',
      __('Tools', 'submittal-builder'),
      __('Tools', 'submittal-builder'),
      'manage_options',
      'sfb-tools',
      [$this, 'render_tools_page'],
      2
    );

    // 3. Demo Tools (internal/testing only - not shown in production)
    if (defined('SFB_SHOW_DEMO_TOOLS') && SFB_SHOW_DEMO_TOOLS) {
      add_submenu_page(
        'sfb',
        __('Demo Tools', 'submittal-builder'),
        __('Demo Tools', 'submittal-builder'),
        'manage_options',
        'sfb-demo-tools',
        [$this, 'render_demo_tools_page'],
        3
      );
    }

    // 3.5 Tracking (Pro feature)
    // Check license directly to work with Demo Tools
    $lic = get_option('sfb_license', []);
    $license_status = $lic['status'] ?? '';
    $show_tracking = ($license_status === 'active') ||
                     (defined('SFB_PRO_DEV') && SFB_PRO_DEV) ||
                     (function_exists('sfb_is_pro_active') && sfb_is_pro_active());
    if ($show_tracking) {
      add_submenu_page(
        'sfb',
        __('Tracking', 'submittal-builder'),
        __('Tracking', 'submittal-builder'),
        'manage_options',
        'sfb-tracking',
        [$this, 'render_tracking_page'],
        3
      );
    }

    // 3.6 Leads (Pro feature - only show if lead capture is enabled)
    $show_leads = ($license_status === 'active' || (defined('SFB_PRO_DEV') && SFB_PRO_DEV) || (function_exists('sfb_is_pro_active') && sfb_is_pro_active())) &&
                  get_option('sfb_lead_capture_enabled', false);
    if ($show_leads) {
      add_submenu_page(
        'sfb',
        __('Leads', 'submittal-builder'),
        __('Leads', 'submittal-builder'),
        'manage_options',
        'sfb-leads',
        [$this, 'render_leads_page'],
        3
      );
    }

    // 4. Settings
    add_submenu_page(
      'sfb',
      __('Settings', 'submittal-builder'),
      __('Settings', 'submittal-builder'),
      'manage_options',
      'sfb-settings',
      [$this, 'render_settings_page'],
      4
    );

    // 5. Branding
    add_submenu_page(
      'sfb',
      __('Branding', 'submittal-builder'),
      __('Branding', 'submittal-builder'),
      'manage_options',
      'sfb-branding',
      [$this, 'render_branding_page'],
      5
    );

    // 6. Last slot: Adaptive based on license state (always position 999)
    $last_position = 999;
    $lic = get_option('sfb_license', []);
    $license_status = $lic['status'] ?? '';

    // Check actual license status FIRST, before sfb_is_pro_active()
    // This allows expired/invalid licenses to show "Manage License"
    if ($license_status === 'expired' || $license_status === 'invalid') {
      // License exists but expired/invalid - let user manage it
      add_submenu_page(
        'sfb',
        __('Manage License', 'submittal-builder'),
        __('Manage License', 'submittal-builder'),
        'manage_options',
        'sfb-license',
        [$this, 'render_license_management_page'],
        $last_position
      );
    } elseif ($license_status === 'active' || (function_exists('sfb_is_pro_active') && sfb_is_pro_active())) {
      // Pro user: License & Support hub
      add_submenu_page(
        'sfb',
        __('License & Support', 'submittal-builder'),
        __('License & Support', 'submittal-builder'),
        'manage_options',
        'sfb-license',
        [$this, 'render_license_support_page'],
        $last_position
      );
    } else {
      // Free user: upsell
      add_submenu_page(
        'sfb',
        __('Upgrade to Pro', 'submittal-builder'),
        __('⭐ Upgrade', 'submittal-builder'),
        'manage_options',
        'sfb-upgrade',
        [$this, 'render_upgrade_page'],
        $last_position
      );
    }
  }

  /** Builder Page Renderer */
  function render_builder_page() {
    ?>
    <div class="wrap sfb-builder-wrap">
      <div class="sfb-builder-header">
        <div class="sfb-builder-header-content">
          <div class="sfb-builder-title-group">
            <h1><?php esc_html_e('Product Catalog', 'submittal-builder'); ?></h1>
            <p class="sfb-builder-subtitle">
              <?php esc_html_e('Manage your product catalog. Add categories, products, and specifications that contractors can select when building submittal packets.', 'submittal-builder'); ?>
            </p>
          </div>
        </div>
      </div>

      <div class="sfb-builder-content">
        <div id="sfb-admin-root" data-view="builder"></div>
      </div>
    </div>

    <style>
    /* Modern Builder Page Styles */
    .sfb-builder-wrap {
      max-width: 1400px;
      margin: 20px 20px 20px 0;
      background: #f9fafb;
      min-height: calc(100vh - 60px);
    }

    .sfb-builder-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 32px 40px;
      margin-bottom: 24px;
      box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
    }

    .sfb-builder-header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 24px;
    }

    .sfb-builder-title-group h1 {
      color: #fff;
      font-size: 32px;
      font-weight: 700;
      margin: 0 0 8px 0;
      line-height: 1.2;
    }

    .sfb-builder-subtitle {
      color: rgba(255, 255, 255, 0.95);
      font-size: 15px;
      margin: 0;
      line-height: 1.5;
      max-width: 800px;
    }

    .sfb-builder-content {
      background: #fff;
      border-radius: 12px;
      padding: 32px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      min-height: 400px;
    }

    /* Enhance React app container */
    #sfb-admin-root {
      position: relative;
    }

    /* Modern button overrides for the builder */
    .sfb-builder-wrap .button,
    .sfb-builder-wrap .button-primary,
    .sfb-builder-wrap .button-secondary {
      border-radius: 6px;
      padding: 8px 16px 10px 16px;
      font-size: 14px;
      font-weight: 500;
      border: none;
      transition: all 0.2s ease;
      line-height: 1.4;
    }

    /* Ensure toolbar buttons have proper padding */
    .sfb-builder-wrap .sfb-toolbar .button {
      padding: 8px 16px 10px 16px;
      min-height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .sfb-builder-wrap .button-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      box-shadow: 0 2px 6px rgba(102, 126, 234, 0.25);
    }

    .sfb-builder-wrap .button-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.35);
      background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
    }

    .sfb-builder-wrap .button-secondary {
      background: #fff;
      color: #667eea;
      border: 2px solid #e5e7eb;
    }

    .sfb-builder-wrap .button-secondary:hover {
      border-color: #667eea;
      color: #5568d3;
      transform: translateY(-1px);
    }

    /* Modern input fields */
    .sfb-builder-wrap input[type="text"],
    .sfb-builder-wrap input[type="email"],
    .sfb-builder-wrap input[type="number"],
    .sfb-builder-wrap textarea,
    .sfb-builder-wrap select {
      border: 2px solid #e5e7eb;
      border-radius: 6px;
      padding: 8px 12px;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .sfb-builder-wrap input[type="text"]:focus,
    .sfb-builder-wrap input[type="email"]:focus,
    .sfb-builder-wrap input[type="number"]:focus,
    .sfb-builder-wrap textarea:focus,
    .sfb-builder-wrap select:focus {
      border-color: #667eea;
      outline: none;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sfb-builder-wrap {
        margin: 10px;
      }

      .sfb-builder-header {
        padding: 24px;
      }

      .sfb-builder-title-group h1 {
        font-size: 24px;
      }

      .sfb-builder-subtitle {
        font-size: 14px;
      }

      .sfb-builder-content {
        padding: 20px;
      }
    }
    </style>
    <?php
    $this->render_feedback_footer();
  }

  /** Branding Page Renderer */
  function render_branding_page() {
    // Get current settings
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());

    // Check if settings were just saved
    $settings_saved = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
    ?>
    <div class="wrap sfb-branding-wrap">
      <h1><?php echo esc_html__('Branding', 'submittal-builder'); ?></h1>
      <p style="color: #6b7280; margin-top: -8px; margin-bottom: 24px;">
        <?php echo esc_html__('Customize your company branding for PDF submittals and spec sheets.', 'submittal-builder'); ?>
      </p>

      <?php if ($settings_saved): ?>
      <div class="notice notice-success is-dismissible" style="margin-bottom: 20px;">
        <p><strong>✅ <?php echo esc_html__('Branding settings saved successfully!', 'submittal-builder'); ?></strong></p>
      </div>
      <?php endif; ?>

      <div class="sfb-branding-grid">
        <!-- Left Column: Form -->
        <div class="sfb-branding-form">
          <form method="post" action="options.php" id="sfb-branding-form">
            <?php settings_fields('sfb_settings_group'); ?>

            <!-- Company Identity Card -->
            <div class="sfb-card">
              <h2>🏢 <?php echo esc_html__('Company Identity', 'submittal-builder'); ?></h2>
              <p class="sfb-muted">
                <?php echo esc_html__('Your company information will appear on all PDF documents.', 'submittal-builder'); ?>
              </p>

              <!-- Logo -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-logo-url">
                  <?php esc_html_e('Company Logo', 'submittal-builder'); ?>
                </label>
                <p class="sfb-field-hint">
                  <?php esc_html_e('Upload your logo to appear on PDF cover pages and headers. Recommended: 300x80px PNG with transparent background.', 'submittal-builder'); ?>
                </p>
                <div class="sfb-logo-upload">
                  <input type="hidden"
                         id="sfb-logo-url"
                         name="<?php echo esc_attr($this->option_key()); ?>[logo_url]"
                         value="<?php echo esc_attr($options['logo_url']); ?>">
                  <button type="button" class="button sfb-media-button" id="sfb-upload-logo">
                    <?php esc_html_e('Select from Media Library', 'submittal-builder'); ?>
                  </button>
                  <?php if (!empty($options['logo_url'])): ?>
                    <button type="button" class="button sfb-remove-logo" id="sfb-remove-logo">
                      <?php esc_html_e('Remove Logo', 'submittal-builder'); ?>
                    </button>
                  <?php endif; ?>
                  <div class="sfb-logo-preview" id="sfb-logo-preview">
                    <?php if (!empty($options['logo_url'])): ?>
                      <img src="<?php echo esc_url($options['logo_url']); ?>" alt="Logo preview">
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- Company Name -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-company-name">
                  <?php esc_html_e('Company Name', 'submittal-builder'); ?>
                </label>
                <input type="text"
                       id="sfb-company-name"
                       name="<?php echo esc_attr($this->option_key()); ?>[company_name]"
                       value="<?php echo esc_attr($options['company_name']); ?>"
                       class="sfb-text-input"
                       placeholder="<?php esc_attr_e('e.g., Acme Construction Supply', 'submittal-builder'); ?>">
              </div>

              <!-- Address -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-company-address">
                  <?php esc_html_e('Company Address', 'submittal-builder'); ?>
                </label>
                <textarea id="sfb-company-address"
                          name="<?php echo esc_attr($this->option_key()); ?>[company_address]"
                          rows="3"
                          class="sfb-textarea"
                          placeholder="<?php esc_attr_e('123 Main Street&#10;Suite 100&#10;City, State 12345', 'submittal-builder'); ?>"><?php echo esc_textarea($options['company_address']); ?></textarea>
              </div>

              <!-- Phone -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-company-phone">
                  <?php esc_html_e('Phone Number', 'submittal-builder'); ?>
                </label>
                <input type="text"
                       id="sfb-company-phone"
                       name="<?php echo esc_attr($this->option_key()); ?>[company_phone]"
                       value="<?php echo esc_attr($options['company_phone']); ?>"
                       class="sfb-text-input"
                       placeholder="<?php esc_attr_e('(555) 123-4567', 'submittal-builder'); ?>">
              </div>

              <!-- Website -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-company-website">
                  <?php esc_html_e('Website', 'submittal-builder'); ?>
                </label>
                <input type="text"
                       id="sfb-company-website"
                       name="<?php echo esc_attr($this->option_key()); ?>[company_website]"
                       value="<?php echo esc_attr($options['company_website']); ?>"
                       class="sfb-text-input"
                       placeholder="<?php esc_attr_e('www.example.com', 'submittal-builder'); ?>">
              </div>
            </div>

            <!-- Brand Presets Card -->
            <div class="sfb-card">
              <h2>
                🎛️ <?php echo esc_html__('Brand Presets', 'submittal-builder'); ?>
                <span class="sfb-custom-badge" id="sfb-custom-badge" style="display: <?php echo $options['brand_preset'] === 'custom' ? 'inline-flex' : 'none'; ?>;">
                  <?php esc_html_e('Custom', 'submittal-builder'); ?>
                </span>
              </h2>
              <p class="sfb-muted">
                <?php echo esc_html__('Choose a professional color scheme to get started, then customize as needed.', 'submittal-builder'); ?>
              </p>

              <div class="sfb-preset-grid">
                <!-- Modern Blue -->
                <div class="sfb-preset-card <?php echo $options['brand_preset'] === 'modern-blue' ? 'sfb-preset-selected' : ''; ?>" data-preset="modern-blue">
                  <div class="sfb-preset-thumbnail">
                    <div class="sfb-preset-header" style="background-color: #1F4B99; border-bottom: 3px solid #1F4B99;"></div>
                    <div class="sfb-preset-title" style="color: #1F4B99;">Submittal Packet</div>
                    <div class="sfb-preset-lines">
                      <span style="background: #e5e7eb;"></span>
                      <span style="background: #e5e7eb; width: 70%;"></span>
                      <span style="background: #e5e7eb;"></span>
                    </div>
                  </div>
                  <div class="sfb-preset-info">
                    <h4><?php esc_html_e('Modern Blue', 'submittal-builder'); ?></h4>
                    <p><?php esc_html_e('Professional and trustworthy', 'submittal-builder'); ?></p>
                    <button type="button" class="button sfb-preset-button" data-preset="modern-blue">
                      <?php esc_html_e('Use Preset', 'submittal-builder'); ?>
                    </button>
                  </div>
                  <?php if ($options['brand_preset'] === 'modern-blue'): ?>
                  <span class="sfb-selected-badge">
                    <span class="dashicons dashicons-yes"></span>
                  </span>
                  <?php endif; ?>
                </div>

                <!-- Architect Gray -->
                <div class="sfb-preset-card <?php echo $options['brand_preset'] === 'architect-gray' ? 'sfb-preset-selected' : ''; ?>" data-preset="architect-gray">
                  <div class="sfb-preset-thumbnail">
                    <div class="sfb-preset-header sfb-preset-rule" style="border-bottom: 1px solid #374151;"></div>
                    <div class="sfb-preset-title" style="color: #374151;">Submittal Packet</div>
                    <div class="sfb-preset-lines">
                      <span style="background: #e5e7eb;"></span>
                      <span style="background: #e5e7eb; width: 70%;"></span>
                      <span style="background: #e5e7eb;"></span>
                    </div>
                  </div>
                  <div class="sfb-preset-info">
                    <h4><?php esc_html_e('Architect Gray', 'submittal-builder'); ?></h4>
                    <p><?php esc_html_e('Minimal and sophisticated', 'submittal-builder'); ?></p>
                    <button type="button" class="button sfb-preset-button" data-preset="architect-gray">
                      <?php esc_html_e('Use Preset', 'submittal-builder'); ?>
                    </button>
                  </div>
                  <?php if ($options['brand_preset'] === 'architect-gray'): ?>
                  <span class="sfb-selected-badge">
                    <span class="dashicons dashicons-yes"></span>
                  </span>
                  <?php endif; ?>
                </div>

                <!-- Engineering Bold -->
                <div class="sfb-preset-card <?php echo $options['brand_preset'] === 'engineering-bold' ? 'sfb-preset-selected' : ''; ?>" data-preset="engineering-bold">
                  <div class="sfb-preset-thumbnail">
                    <div class="sfb-preset-header" style="background-color: #0B5D3B; border-bottom: 3px solid #0B5D3B;"></div>
                    <div class="sfb-preset-title sfb-preset-bold" style="color: #0B5D3B;">Submittal Packet</div>
                    <div class="sfb-preset-lines">
                      <span style="background: #e5e7eb;"></span>
                      <span style="background: #e5e7eb; width: 70%;"></span>
                      <span style="background: #e5e7eb;"></span>
                    </div>
                  </div>
                  <div class="sfb-preset-info">
                    <h4><?php esc_html_e('Engineering Bold', 'submittal-builder'); ?></h4>
                    <p><?php esc_html_e('Strong and confident', 'submittal-builder'); ?></p>
                    <button type="button" class="button sfb-preset-button" data-preset="engineering-bold">
                      <?php esc_html_e('Use Preset', 'submittal-builder'); ?>
                    </button>
                  </div>
                  <?php if ($options['brand_preset'] === 'engineering-bold'): ?>
                  <span class="sfb-selected-badge">
                    <span class="dashicons dashicons-yes"></span>
                  </span>
                  <?php endif; ?>
                </div>

                <!-- Clean Violet -->
                <div class="sfb-preset-card <?php echo $options['brand_preset'] === 'clean-violet' ? 'sfb-preset-selected' : ''; ?>" data-preset="clean-violet">
                  <div class="sfb-preset-thumbnail">
                    <div class="sfb-preset-header" style="background-color: #7B61FF; border-bottom: 3px solid #7B61FF;"></div>
                    <div class="sfb-preset-title" style="color: #7B61FF;">Submittal Packet</div>
                    <div class="sfb-preset-lines">
                      <span style="background: #e5e7eb;"></span>
                      <span style="background: #e5e7eb; width: 70%;"></span>
                      <span style="background: #e5e7eb;"></span>
                    </div>
                  </div>
                  <div class="sfb-preset-info">
                    <h4><?php esc_html_e('Clean Violet', 'submittal-builder'); ?></h4>
                    <p><?php esc_html_e('Creative and modern', 'submittal-builder'); ?></p>
                    <button type="button" class="button sfb-preset-button" data-preset="clean-violet">
                      <?php esc_html_e('Use Preset', 'submittal-builder'); ?>
                    </button>
                  </div>
                  <?php if ($options['brand_preset'] === 'clean-violet'): ?>
                  <span class="sfb-selected-badge">
                    <span class="dashicons dashicons-yes"></span>
                  </span>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Hidden field to store preset selection -->
              <input type="hidden"
                     id="sfb-brand-preset"
                     name="<?php echo esc_attr($this->option_key()); ?>[brand_preset]"
                     value="<?php echo esc_attr($options['brand_preset']); ?>">
            </div>

            <!-- Visual Branding Card -->
            <div class="sfb-card">
              <h2>🎨 <?php echo esc_html__('Visual Branding', 'submittal-builder'); ?></h2>
              <p class="sfb-muted">
                <?php echo esc_html__('Customize the visual appearance of your PDF documents.', 'submittal-builder'); ?>
              </p>

              <!-- Primary Color -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-primary-color">
                  <?php esc_html_e('Primary Brand Color', 'submittal-builder'); ?>
                </label>
                <p class="sfb-field-hint">
                  <?php esc_html_e('Used for PDF headers, accents, and section dividers.', 'submittal-builder'); ?>
                </p>
                <div class="sfb-color-picker-group">
                  <input type="color"
                         id="sfb-primary-color"
                         name="<?php echo esc_attr($this->option_key()); ?>[primary_color]"
                         value="<?php echo esc_attr($options['primary_color']); ?>"
                         class="sfb-color-input">
                  <input type="text"
                         id="sfb-primary-color-text"
                         value="<?php echo esc_attr($options['primary_color']); ?>"
                         class="sfb-text-input sfb-color-text"
                         pattern="^#[0-9A-Fa-f]{6}$"
                         placeholder="#111827">
                  <span class="sfb-color-preview-box" style="background-color: <?php echo esc_attr($options['primary_color']); ?>"></span>
                </div>
              </div>

              <!-- Cover Sheet Default -->
              <div class="sfb-field-group">
                <label class="sfb-checkbox-wrapper">
                  <input type="checkbox"
                         name="<?php echo esc_attr($this->option_key()); ?>[cover_default]"
                         value="1"
                         <?php checked(!empty($options['cover_default'])); ?>>
                  <span class="sfb-checkbox-label">
                    <strong><?php esc_html_e('Include cover sheet by default', 'submittal-builder'); ?></strong>
                    <small><?php esc_html_e('Automatically add a branded cover page to new PDF submittals.', 'submittal-builder'); ?></small>
                  </span>
                </label>
              </div>

              <!-- Footer Text -->
              <div class="sfb-field-group">
                <label class="sfb-field-label" for="sfb-footer-text">
                  <?php esc_html_e('PDF Footer Text', 'submittal-builder'); ?>
                </label>
                <p class="sfb-field-hint">
                  <?php esc_html_e('Custom text displayed at the bottom of each PDF page.', 'submittal-builder'); ?>
                </p>
                <input type="text"
                       id="sfb-footer-text"
                       name="<?php echo esc_attr($this->option_key()); ?>[footer_text]"
                       value="<?php echo esc_attr($options['footer_text']); ?>"
                       class="sfb-text-input"
                       placeholder="<?php esc_attr_e('Generated by Submittal &amp; Spec Builder', 'submittal-builder'); ?>">
              </div>
            </div>

            <?php
            // White-Label Mode Card (Pro + Agency)
            $is_pro_or_agency = sfb_is_pro_active();
            if ($is_pro_or_agency):
              $white_label_settings = $options['white_label'] ?? sfb_brand_defaults()['white_label'];
            ?>
            <!-- White-Label Mode (Pro Feature) -->
            <div class="sfb-card sfb-white-label-card">
              <h2>
                🏷️ <?php echo esc_html__('White-Label Mode', 'submittal-builder'); ?>
                <span class="sfb-pro-badge">PRO</span>
              </h2>
              <p class="sfb-muted">
                <?php echo esc_html__('Remove plugin branding from PDFs, emails, and frontend. Perfect for professionals presenting to clients.', 'submittal-builder'); ?>
              </p>

              <!-- Enable White-Label Toggle -->
              <div class="sfb-field-group">
                <label class="sfb-checkbox-wrapper">
                  <input type="checkbox"
                         id="sfb-white-label-enabled"
                         name="<?php echo esc_attr($this->option_key()); ?>[white_label][enabled]"
                         value="1"
                         <?php checked(!empty($white_label_settings['enabled'])); ?>>
                  <span class="sfb-checkbox-label">
                    <strong><?php esc_html_e('Enable White-Label Mode', 'submittal-builder'); ?></strong>
                    <small><?php esc_html_e('Hide all "Generated with Submittal & Spec Sheet Builder" credits from PDFs, emails, and frontend.', 'submittal-builder'); ?></small>
                  </span>
                </label>
              </div>

              <!-- White-Label Settings (shown when enabled) -->
              <div id="sfb-white-label-settings" style="display: <?php echo !empty($white_label_settings['enabled']) ? 'block' : 'none'; ?>; padding-left: 24px; margin-top: 16px; border-left: 3px solid #7c3aed;">

                <!-- Custom Footer Text -->
                <div class="sfb-field-group">
                  <label class="sfb-field-label" for="sfb-white-label-footer">
                    <?php esc_html_e('Custom PDF Footer Text', 'submittal-builder'); ?>
                    <span class="sfb-field-optional">(<?php esc_html_e('optional', 'submittal-builder'); ?>)</span>
                  </label>
                  <p class="sfb-field-hint">
                    <?php esc_html_e('Replace the default footer with your own text. Leave blank to remove footer text entirely.', 'submittal-builder'); ?>
                  </p>
                  <input type="text"
                         id="sfb-white-label-footer"
                         name="<?php echo esc_attr($this->option_key()); ?>[white_label][custom_footer]"
                         value="<?php echo esc_attr($white_label_settings['custom_footer']); ?>"
                         class="sfb-text-input"
                         placeholder="<?php esc_attr_e('e.g., Prepared by Your Company', 'submittal-builder'); ?>">
                </div>

                <!-- Email From Name -->
                <div class="sfb-field-group">
                  <label class="sfb-field-label" for="sfb-white-label-email-name">
                    <?php esc_html_e('Email From Name', 'submittal-builder'); ?>
                    <span class="sfb-field-optional">(<?php esc_html_e('optional', 'submittal-builder'); ?>)</span>
                  </label>
                  <p class="sfb-field-hint">
                    <?php esc_html_e('Customize the sender name for lead capture emails. Leave blank to use site name.', 'submittal-builder'); ?>
                  </p>
                  <input type="text"
                         id="sfb-white-label-email-name"
                         name="<?php echo esc_attr($this->option_key()); ?>[white_label][email_from_name]"
                         value="<?php echo esc_attr($white_label_settings['email_from_name']); ?>"
                         class="sfb-text-input"
                         placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>">
                </div>

                <!-- Email From Address -->
                <div class="sfb-field-group">
                  <label class="sfb-field-label" for="sfb-white-label-email-address">
                    <?php esc_html_e('Email From Address', 'submittal-builder'); ?>
                    <span class="sfb-field-optional">(<?php esc_html_e('optional', 'submittal-builder'); ?>)</span>
                  </label>
                  <p class="sfb-field-hint">
                    <?php esc_html_e('Customize the sender email address. Leave blank to use WordPress default.', 'submittal-builder'); ?>
                  </p>
                  <input type="email"
                         id="sfb-white-label-email-address"
                         name="<?php echo esc_attr($this->option_key()); ?>[white_label][email_from_address]"
                         value="<?php echo esc_attr($white_label_settings['email_from_address']); ?>"
                         class="sfb-text-input"
                         placeholder="<?php echo esc_attr(get_option('admin_email')); ?>">
                </div>

                <!-- Show Subtle Credit Toggle -->
                <div class="sfb-field-group">
                  <label class="sfb-checkbox-wrapper">
                    <input type="checkbox"
                           id="sfb-white-label-show-credit"
                           name="<?php echo esc_attr($this->option_key()); ?>[white_label][show_subtle_credit]"
                           value="1"
                           <?php checked(!empty($white_label_settings['show_subtle_credit'])); ?>>
                    <span class="sfb-checkbox-label">
                      <strong><?php esc_html_e('Show subtle credit', 'submittal-builder'); ?></strong>
                      <small><?php esc_html_e('Display a small "Powered by" credit in PDFs and emails (not shown on frontend).', 'submittal-builder'); ?></small>
                    </span>
                  </label>
                </div>

              </div>
            </div>
            <?php endif; ?>

            <!-- Save Section -->
            <div class="sfb-save-section">
              <p class="sfb-save-message">
                💾 <?php echo esc_html__('Save your brand settings to apply them across all submittal PDFs.', 'submittal-builder'); ?>
              </p>
              <div class="sfb-save-button-wrapper">
                <button type="submit" class="button button-primary button-large sfb-save-button" id="sfb-save-branding">
                  <?php esc_html_e('Save Branding', 'submittal-builder'); ?>
                </button>
                <span class="sfb-save-success" id="sfb-save-success">
                  <span class="dashicons dashicons-yes-alt"></span>
                </span>
              </div>
            </div>
          </form>
        </div>

        <!-- Right Column: Live Preview -->
        <div class="sfb-branding-preview">
          <div class="sfb-card sfb-preview-card">
            <h3><?php esc_html_e('Live Preview', 'submittal-builder'); ?></h3>
            <p class="sfb-muted" style="margin-bottom: 16px;">
              <?php esc_html_e('See how your branding will appear on PDFs', 'submittal-builder'); ?>
            </p>

            <div class="sfb-pdf-preview" id="sfb-pdf-preview">
              <div class="sfb-pdf-header" id="sfb-preview-header">
                <div class="sfb-pdf-logo" id="sfb-preview-logo">
                  <?php if (!empty($options['logo_url'])): ?>
                    <img src="<?php echo esc_url($options['logo_url']); ?>" alt="Logo">
                  <?php else: ?>
                    <div class="sfb-logo-placeholder">Your Logo</div>
                  <?php endif; ?>
                </div>
                <div class="sfb-pdf-company" id="sfb-preview-company">
                  <?php echo !empty($options['company_name']) ? esc_html($options['company_name']) : esc_html__('Your Company Name', 'submittal-builder'); ?>
                </div>
              </div>
              <div class="sfb-pdf-body">
                <div class="sfb-pdf-title" id="sfb-preview-title">
                  <?php esc_html_e('Submittal Packet', 'submittal-builder'); ?>
                </div>
                <div class="sfb-pdf-content">
                  <div class="sfb-pdf-sample-line"></div>
                  <div class="sfb-pdf-sample-line short"></div>
                  <div class="sfb-pdf-sample-line"></div>
                </div>
              </div>
              <div class="sfb-pdf-footer" id="sfb-preview-footer">
                <?php echo !empty($options['footer_text']) ? esc_html($options['footer_text']) : esc_html__('Generated by Submittal &amp; Spec Builder', 'submittal-builder'); ?>
              </div>
            </div>
            <p class="sfb-preview-note">
              <span class="dashicons dashicons-update-alt"></span>
              <?php esc_html_e('Your changes will reflect automatically in your next PDF.', 'submittal-builder'); ?>
            </p>
          </div>
        </div>
      </div>

      <?php
      // Brand Presets Section (Agency only - hidden in handoff mode)
      $is_agency = SFB_Branding::is_agency_license();
      $handoff_mode = sfb_is_client_handoff_mode();
      if ($is_agency && !$handoff_mode):
      ?>
      <!-- Brand Presets (Agency Feature) -->
      <div class="sfb-card" style="margin-top: 32px;">
        <h2>💼 <?php esc_html_e('Brand Presets', 'submittal-builder'); ?> <span style="background:#7c3aed;color:#fff;font-size:11px;padding:4px 8px;border-radius:4px;font-weight:600;margin-left:8px;">AGENCY</span></h2>
        <p class="sfb-muted">
          <?php esc_html_e('Save and manage multiple brand configurations. Perfect for agencies managing multiple clients.', 'submittal-builder'); ?>
        </p>

        <!-- Create Preset -->
        <div style="margin-bottom: 24px;">
          <div style="display:flex;gap:12px;align-items:center;">
            <input type="text"
                   id="sfb-new-preset-name"
                   placeholder="<?php esc_attr_e('Preset name (e.g., Client A)', 'submittal-builder'); ?>"
                   style="flex:1;max-width:300px;">
            <button type="button" class="button button-primary" id="sfb-create-preset">
              <?php esc_html_e('Save Current as Preset', 'submittal-builder'); ?>
            </button>
          </div>
          <p style="color:#6b7280;font-size:13px;margin:8px 0 0 0;">
            <?php esc_html_e('Saves your current branding settings with a name you can apply later.', 'submittal-builder'); ?>
          </p>
        </div>

        <!-- Presets List -->
        <div id="sfb-presets-list">
          <p style="text-align:center;color:#9ca3af;padding:20px;">
            <?php esc_html_e('Loading presets...', 'submittal-builder'); ?>
          </p>
        </div>

        <!-- Auto-Apply Default Preset Toggle -->
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #e5e7eb;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input type="checkbox"
                   id="sfb-use-default-preset"
                   name="<?php echo esc_attr($this->option_key()); ?>[use_default_preset]"
                   value="1"
                   <?php checked(get_option('sfb_brand_use_default_on_pdf', false)); ?>>
            <strong><?php esc_html_e('Use default preset automatically', 'submittal-builder'); ?></strong>
          </label>
          <p style="color:#6b7280;font-size:13px;margin:8px 0 0 28px;">
            <?php esc_html_e('When a default Brand Preset is set, use it for the Review preview and generated PDFs. You can still change branding anytime.', 'submittal-builder'); ?>
          </p>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <script>
    jQuery(document).ready(function($) {
      let hasUnsavedChanges = false;

      // Track unsaved changes
      function markUnsaved() {
        if (!hasUnsavedChanges) {
          hasUnsavedChanges = true;
          $('#sfb-save-branding').addClass('sfb-unsaved-pulse');
        }
      }

      // Media uploader
      let mediaUploader;
      $('#sfb-upload-logo').on('click', function(e) {
        e.preventDefault();
        if (mediaUploader) {
          mediaUploader.open();
          return;
        }
        mediaUploader = wp.media({
          title: '<?php esc_html_e('Select Company Logo', 'submittal-builder'); ?>',
          button: { text: '<?php esc_html_e('Use this logo', 'submittal-builder'); ?>' },
          multiple: false,
          library: { type: 'image' }
        });
        mediaUploader.on('select', function() {
          const attachment = mediaUploader.state().get('selection').first().toJSON();
          $('#sfb-logo-url').val(attachment.url);
          $('#sfb-logo-preview').html('<img src="' + attachment.url + '" alt="Logo preview">');
          $('#sfb-preview-logo').html('<img src="' + attachment.url + '" alt="Logo">');
          $('#sfb-remove-logo').show();
          markUnsaved();
        });
        mediaUploader.open();
      });

      // Remove logo
      $('#sfb-remove-logo').on('click', function(e) {
        e.preventDefault();
        $('#sfb-logo-url').val('');
        $('#sfb-logo-preview').empty();
        $('#sfb-preview-logo').html('<div class="sfb-logo-placeholder">Your Logo</div>');
        $(this).hide();
        markUnsaved();
      });

      // Live preview updates
      $('#sfb-company-name').on('input', function() {
        const val = $(this).val() || '<?php esc_html_e('Your Company Name', 'submittal-builder'); ?>';
        $('#sfb-preview-company').text(val);
        markUnsaved();
      });

      $('#sfb-company-address, #sfb-company-phone, #sfb-company-website').on('input', function() {
        markUnsaved();
      });

      $('#sfb-footer-text').on('input', function() {
        const val = $(this).val() || '<?php esc_html_e('Generated by Submittal &amp; Spec Builder', 'submittal-builder'); ?>';
        $('#sfb-preview-footer').text(val);
        markUnsaved();
      });

      // Color picker sync
      $('#sfb-primary-color').on('input', function() {
        const color = $(this).val();
        $('#sfb-primary-color-text').val(color);
        $('.sfb-color-preview-box').css('background-color', color);
        $('#sfb-preview-header').css('border-color', color);
        $('#sfb-preview-title').css('color', color);
        markUnsaved();
      });

      $('#sfb-primary-color-text').on('input', function() {
        const color = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
          $('#sfb-primary-color').val(color);
          $('.sfb-color-preview-box').css('background-color', color);
          $('#sfb-preview-header').css('border-color', color);
          $('#sfb-preview-title').css('color', color);
          markUnsaved();
        }
      });

      // Checkbox tracking
      $('input[type="checkbox"]').on('change', function() {
        markUnsaved();
      });

      // Show success checkmark after save
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('settings-updated') === 'true') {
        $('#sfb-save-success').addClass('sfb-show-success');
        setTimeout(function() {
          $('#sfb-save-success').removeClass('sfb-show-success');
        }, 3000);
      }

      // Preset definitions
      const SFB_PRESETS = {
        'modern-blue':     { color:'#1F4B99', style:'solid' },
        'architect-gray':  { color:'#374151', style:'rule'  },
        'engineering-bold':{ color:'#0B5D3B', style:'bold'  },
        'clean-violet':    { color:'#7B61FF', style:'solid' },
      };

      // Preset selection
      $('.sfb-preset-button').on('click', function(e) {
        e.preventDefault();
        const presetKey = $(this).data('preset');
        const preset = SFB_PRESETS[presetKey];

        if (!preset) return;

        // Update color inputs
        $('#sfb-primary-color').val(preset.color);
        $('#sfb-primary-color-text').val(preset.color);
        $('.sfb-color-preview-box').css('background-color', preset.color);

        // Update live preview
        $('#sfb-preview-header').css('border-color', preset.color);
        $('#sfb-preview-title').css('color', preset.color);

        // Apply header style
        applyPreviewHeaderStyle(preset.style);

        // Update hidden preset field
        $('#sfb-brand-preset').val(presetKey);

        // Update UI selection states
        $('.sfb-preset-card').removeClass('sfb-preset-selected');
        $('.sfb-selected-badge').remove();
        $('.sfb-preset-card[data-preset="' + presetKey + '"]').addClass('sfb-preset-selected');
        $('.sfb-preset-card[data-preset="' + presetKey + '"]').append('<span class="sfb-selected-badge"><span class="dashicons dashicons-yes"></span></span>');

        // Hide custom badge
        $('#sfb-custom-badge').hide();

        // Mark as unsaved
        markUnsaved();

        // Show toast notification
        showPresetToast();
      });

      // Detect manual color changes
      $('#sfb-primary-color, #sfb-primary-color-text').on('input', function() {
        const currentColor = $('#sfb-primary-color').val().toUpperCase();
        let matchesPreset = false;

        // Check if color matches any preset
        for (const [key, preset] of Object.entries(SFB_PRESETS)) {
          if (preset.color.toUpperCase() === currentColor) {
            matchesPreset = true;
            break;
          }
        }

        // If color doesn't match any preset, mark as custom
        if (!matchesPreset && $('#sfb-brand-preset').val() !== 'custom') {
          $('#sfb-brand-preset').val('custom');
          $('.sfb-preset-card').removeClass('sfb-preset-selected');
          $('.sfb-selected-badge').remove();
          $('#sfb-custom-badge').show();
        }
      });

      // Apply header style to preview
      function applyPreviewHeaderStyle(style) {
        const $header = $('#sfb-preview-header');
        const $title = $('#sfb-preview-title');

        // Reset all styles
        $header.removeClass('sfb-preview-rule sfb-preview-bold');
        $title.removeClass('sfb-preview-title-bold');

        switch(style) {
          case 'rule':
            $header.addClass('sfb-preview-rule');
            break;
          case 'bold':
            $header.addClass('sfb-preview-bold');
            $title.addClass('sfb-preview-title-bold');
            break;
          case 'solid':
          default:
            // Default solid style
            break;
        }
      }

      // Show preset toast notification
      function showPresetToast() {
        // Remove existing toast if any
        $('.sfb-preset-toast').remove();

        // Create toast
        const $toast = $('<div class="sfb-preset-toast">' +
          '<span class="dashicons dashicons-yes-alt"></span> ' +
          '<?php esc_html_e('Preset applied — remember to Save Branding to keep changes.', 'submittal-builder'); ?>' +
        '</div>');

        $('body').append($toast);

        // Show toast
        setTimeout(function() {
          $toast.addClass('sfb-show-toast');
        }, 10);

        // Hide and remove toast after 4 seconds
        setTimeout(function() {
          $toast.removeClass('sfb-show-toast');
          setTimeout(function() {
            $toast.remove();
          }, 300);
        }, 4000);
      }

      // Initialize header style on page load
      const initialPreset = $('#sfb-brand-preset').val();
      if (initialPreset && SFB_PRESETS[initialPreset]) {
        applyPreviewHeaderStyle(SFB_PRESETS[initialPreset].style);
      }

      // ========================================================================
      // BRAND PRESETS (Agency Feature)
      // ========================================================================

      <?php if ($is_agency): ?>
      const presetNonce = '<?php echo wp_create_nonce('sfb_brand_presets'); ?>';

      // Load presets on page load
      function loadPresets() {
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_list',
            nonce: presetNonce
          },
          success: function(response) {
            if (response.success && response.data.presets) {
              renderPresets(response.data.presets);
            } else {
              $('#sfb-presets-list').html('<p style="text-align:center;color:#dc2626;padding:20px;">Error loading presets.</p>');
            }
          },
          error: function() {
            $('#sfb-presets-list').html('<p style="text-align:center;color:#dc2626;padding:20px;">Failed to load presets.</p>');
          }
        });
      }

      // Render presets list
      function renderPresets(presets) {
        if (!presets || presets.length === 0) {
          $('#sfb-presets-list').html('<p style="text-align:center;color:#9ca3af;padding:20px;"><?php esc_html_e('No presets saved yet. Save your current branding as a preset above.', 'submittal-builder'); ?></p>');
          return;
        }

        let html = '<table class="widefat striped" style="margin-top:16px;"><thead><tr>';
        html += '<th><?php esc_html_e('Name', 'submittal-builder'); ?></th>';
        html += '<th><?php esc_html_e('Updated', 'submittal-builder'); ?></th>';
        html += '<th><?php esc_html_e('Actions', 'submittal-builder'); ?></th>';
        html += '<th style="width:80px;text-align:center;"><?php esc_html_e('Default', 'submittal-builder'); ?></th>';
        html += '</tr></thead><tbody>';

        presets.forEach(function(preset) {
          const updatedDate = new Date(preset.updated_at).toLocaleDateString();
          html += '<tr>';
          html += '<td><strong>' + escapeHtml(preset.name) + '</strong></td>';
          html += '<td>' + updatedDate + '</td>';
          html += '<td>';
          html += '<button class="button button-small sfb-apply-preset" data-id="' + preset.id + '" style="margin-right:4px;"><?php esc_html_e('Apply', 'submittal-builder'); ?></button>';
          html += '<button class="button button-small sfb-rename-preset" data-id="' + preset.id + '" data-name="' + escapeHtml(preset.name) + '" style="margin-right:4px;"><?php esc_html_e('Rename', 'submittal-builder'); ?></button>';
          html += '<button class="button button-small button-link-delete sfb-delete-preset" data-id="' + preset.id + '" data-name="' + escapeHtml(preset.name) + '"><?php esc_html_e('Delete', 'submittal-builder'); ?></button>';
          html += '</td>';
          html += '<td style="text-align:center;"><input type="checkbox" class="sfb-default-preset" data-id="' + preset.id + '" ' + (preset.is_default ? 'checked' : '') + '></td>';
          html += '</tr>';
        });

        html += '</tbody></table>';
        $('#sfb-presets-list').html(html);
      }

      // HTML escape helper
      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // Create preset
      $('#sfb-create-preset').on('click', function() {
        const name = $('#sfb-new-preset-name').val().trim();
        if (!name) {
          alert('<?php esc_html_e('Please enter a preset name.', 'submittal-builder'); ?>');
          return;
        }

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_create',
            nonce: presetNonce,
            name: name
          },
          beforeSend: function() {
            $('#sfb-create-preset').prop('disabled', true).text('<?php esc_html_e('Saving...', 'submittal-builder'); ?>');
          },
          success: function(response) {
            if (response.success) {
              $('#sfb-new-preset-name').val('');
              loadPresets();
              alert(response.data.message);
            } else {
              alert(response.data.message || '<?php esc_html_e('Failed to create preset.', 'submittal-builder'); ?>');
            }
          },
          error: function() {
            alert('<?php esc_html_e('Server error. Please try again.', 'submittal-builder'); ?>');
          },
          complete: function() {
            $('#sfb-create-preset').prop('disabled', false).text('<?php esc_html_e('Save Current as Preset', 'submittal-builder'); ?>');
          }
        });
      });

      // Apply preset
      $(document).on('click', '.sfb-apply-preset', function() {
        const id = $(this).data('id');
        if (!confirm('<?php esc_html_e('Apply this preset? Your current branding will be replaced.', 'submittal-builder'); ?>')) {
          return;
        }

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_apply',
            nonce: presetNonce,
            id: id
          },
          beforeSend: function() {
            $('.sfb-apply-preset').prop('disabled', true);
          },
          success: function(response) {
            if (response.success) {
              alert(response.data.message);
              location.reload();
            } else {
              alert(response.data.message || '<?php esc_html_e('Failed to apply preset.', 'submittal-builder'); ?>');
            }
          },
          error: function() {
            alert('<?php esc_html_e('Server error. Please try again.', 'submittal-builder'); ?>');
          },
          complete: function() {
            $('.sfb-apply-preset').prop('disabled', false);
          }
        });
      });

      // Rename preset
      $(document).on('click', '.sfb-rename-preset', function() {
        const id = $(this).data('id');
        const oldName = $(this).data('name');
        const newName = prompt('<?php esc_html_e('Enter new name:', 'submittal-builder'); ?>', oldName);

        if (!newName || newName === oldName) {
          return;
        }

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_rename',
            nonce: presetNonce,
            id: id,
            name: newName
          },
          success: function(response) {
            if (response.success) {
              loadPresets();
            } else {
              alert(response.data.message || '<?php esc_html_e('Failed to rename preset.', 'submittal-builder'); ?>');
            }
          },
          error: function() {
            alert('<?php esc_html_e('Server error. Please try again.', 'submittal-builder'); ?>');
          }
        });
      });

      // Delete preset
      $(document).on('click', '.sfb-delete-preset', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        if (!confirm('<?php esc_html_e('Delete preset', 'submittal-builder'); ?> "' + name + '"?')) {
          return;
        }

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_delete',
            nonce: presetNonce,
            id: id
          },
          success: function(response) {
            if (response.success) {
              loadPresets();
            } else {
              alert(response.data.message || '<?php esc_html_e('Failed to delete preset.', 'submittal-builder'); ?>');
            }
          },
          error: function() {
            alert('<?php esc_html_e('Server error. Please try again.', 'submittal-builder'); ?>');
          }
        });
      });

      // Set default preset
      $(document).on('change', '.sfb-default-preset', function() {
        const id = $(this).data('id');
        const isDefault = $(this).is(':checked');

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'sfb_preset_set_default',
            nonce: presetNonce,
            id: id,
            is_default: isDefault ? 1 : 0
          },
          success: function(response) {
            if (response.success) {
              loadPresets();
            } else {
              alert(response.data.message || '<?php esc_html_e('Failed to update default preset.', 'submittal-builder'); ?>');
            }
          },
          error: function() {
            alert('<?php esc_html_e('Server error. Please try again.', 'submittal-builder'); ?>');
          }
        });
      });

      // Load presets on page load
      loadPresets();
      <?php endif; ?>

    });
    </script>

    <style>
      .sfb-branding-wrap {
        max-width: 1200px;
      }

      .sfb-branding-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        margin-top: 20px;
      }

      .sfb-branding-wrap .sfb-card {
        background: #fff;
        border: 1px solid #e9edf3;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        padding: 20px 24px;
        margin-bottom: 20px;
      }

      .sfb-branding-wrap .sfb-card h2 {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        margin: 0 0 8px;
        color: #111827;
      }

      .sfb-branding-wrap .sfb-card h3 {
        font-size: 15px;
        margin: 0 0 4px;
        color: #111827;
      }

      .sfb-branding-wrap .sfb-muted {
        color: #6b7280;
        font-size: 13px;
        margin: 0 0 20px;
        line-height: 1.5;
      }

      .sfb-field-group {
        margin-bottom: 20px;
      }

      .sfb-field-group:last-child {
        margin-bottom: 0;
      }

      .sfb-field-label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        margin-bottom: 6px;
      }

      /* Custom Badge */
      .sfb-custom-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        border-radius: 12px;
        margin-left: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      /* Preset Grid */
      .sfb-preset-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 16px;
      }

      .sfb-preset-card {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
      }

      .sfb-preset-card:hover {
        border-color: #7c3aed;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
        transform: translateY(-2px);
      }

      .sfb-preset-card.sfb-preset-selected {
        border-color: #7c3aed;
        background: #faf8ff;
      }

      .sfb-preset-thumbnail {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 12px;
        overflow: hidden;
      }

      .sfb-preset-header {
        height: 8px;
        background: #f9fafb;
        margin-bottom: 10px;
        border-radius: 2px;
      }

      .sfb-preset-header.sfb-preset-rule {
        height: auto;
        background: transparent;
        padding-bottom: 8px;
      }

      .sfb-preset-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        text-align: center;
      }

      .sfb-preset-title.sfb-preset-bold {
        font-weight: 900;
      }

      .sfb-preset-lines {
        display: flex;
        flex-direction: column;
        gap: 4px;
      }

      .sfb-preset-lines span {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        display: block;
      }

      .sfb-preset-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin: 0 0 4px 0;
      }

      .sfb-preset-info p {
        font-size: 12px;
        color: #6b7280;
        margin: 0 0 10px 0;
        line-height: 1.4;
      }

      .sfb-preset-button {
        width: 100%;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        color: #374151;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .sfb-preset-button:hover {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
      }

      .sfb-preset-card.sfb-preset-selected .sfb-preset-button {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
      }

      .sfb-selected-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #10b981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
      }

      .sfb-selected-badge .dashicons {
        color: #fff;
        font-size: 16px;
        width: 16px;
        height: 16px;
      }

      .sfb-field-hint {
        color: #6b7280;
        font-size: 12px;
        margin: -2px 0 8px 0;
        line-height: 1.4;
      }

      .sfb-text-input,
      .sfb-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        line-height: 1.5;
        transition: border-color 0.15s;
      }

      .sfb-text-input:focus,
      .sfb-textarea:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }

      .sfb-textarea {
        resize: vertical;
      }

      .sfb-logo-upload {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .sfb-media-button {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
        font-weight: 600;
        transition: all 0.2s;
      }

      .sfb-media-button:hover {
        background: #6d28d9;
        border-color: #6d28d9;
        color: #fff;
      }

      .sfb-remove-logo {
        color: #dc2626;
        border-color: #dc2626;
      }

      .sfb-remove-logo:hover {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #b91c1c;
      }

      .sfb-logo-preview {
        width: 100%;
        margin-top: 12px;
      }

      .sfb-logo-preview img {
        max-width: 300px;
        max-height: 80px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px;
        background: #fff;
      }

      .sfb-color-picker-group {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .sfb-color-input {
        width: 60px;
        height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        cursor: pointer;
        padding: 2px;
      }

      .sfb-color-text {
        width: 120px;
        text-transform: uppercase;
        font-family: monospace;
      }

      .sfb-color-preview-box {
        width: 44px;
        height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        flex-shrink: 0;
      }

      .sfb-checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
      }

      .sfb-checkbox-wrapper input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        cursor: pointer;
      }

      .sfb-checkbox-label {
        flex: 1;
        line-height: 1.5;
      }

      .sfb-checkbox-label strong {
        display: block;
        color: #111827;
        font-size: 14px;
        margin-bottom: 2px;
      }

      .sfb-checkbox-label small {
        display: block;
        color: #6b7280;
        font-size: 12px;
      }

      .sfb-save-section {
        margin-top: 24px;
        padding: 20px 24px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
      }

      .sfb-save-message {
        color: #374151;
        font-size: 14px;
        margin: 0;
        flex: 1;
      }

      .sfb-save-button-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .sfb-save-button {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        transition: all 0.2s ease;
        flex-shrink: 0;
      }

      .sfb-save-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35);
      }

      /* Unsaved changes pulse animation */
      @keyframes sfb-pulse {
        0%, 100% {
          box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        }
        50% {
          box-shadow: 0 2px 20px rgba(124, 58, 237, 0.6), 0 0 30px rgba(124, 58, 237, 0.3);
        }
      }

      .sfb-save-button.sfb-unsaved-pulse {
        animation: sfb-pulse 2s ease-in-out infinite;
      }

      /* Success checkmark */
      .sfb-save-success {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #10b981;
        color: #fff;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
      }

      .sfb-save-success .dashicons {
        font-size: 20px;
        width: 20px;
        height: 20px;
      }

      .sfb-save-success.sfb-show-success {
        opacity: 1;
        transform: scale(1);
      }

      /* Preview Card */
      .sfb-preview-card {
        position: sticky;
        top: 32px;
      }

      .sfb-pdf-preview {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      }

      .sfb-pdf-header {
        padding: 16px;
        border-bottom: 3px solid <?php echo esc_attr($options['primary_color']); ?>;
        background: #f9fafb;
        transition: border-color 0.3s, border-width 0.3s;
      }

      .sfb-pdf-header.sfb-preview-rule {
        border-bottom-width: 1px;
      }

      .sfb-pdf-header.sfb-preview-bold {
        border-bottom-width: 4px;
      }

      .sfb-pdf-logo {
        margin-bottom: 10px;
        text-align: center;
      }

      .sfb-pdf-logo img {
        max-width: 200px;
        max-height: 50px;
      }

      .sfb-logo-placeholder {
        padding: 12px 24px;
        background: #e5e7eb;
        color: #6b7280;
        border-radius: 6px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
      }

      .sfb-pdf-company {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        text-align: center;
      }

      .sfb-pdf-body {
        padding: 20px;
      }

      .sfb-pdf-title {
        font-size: 18px;
        font-weight: 700;
        color: <?php echo esc_attr($options['primary_color']); ?>;
        margin-bottom: 16px;
        transition: color 0.3s, font-weight 0.3s;
      }

      .sfb-pdf-title.sfb-preview-title-bold {
        font-weight: 900;
      }

      .sfb-pdf-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }

      .sfb-pdf-sample-line {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
      }

      .sfb-pdf-sample-line.short {
        width: 70%;
      }

      .sfb-pdf-footer {
        padding: 12px 16px;
        background: #f3f4f6;
        border-top: 1px solid #e5e7eb;
        text-align: center;
        font-size: 11px;
        color: #6b7280;
      }

      /* Preview note */
      .sfb-preview-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
        padding: 10px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 6px;
        color: #1e40af;
        font-size: 12px;
        line-height: 1.4;
      }

      .sfb-preview-note .dashicons {
        font-size: 16px;
        width: 16px;
        height: 16px;
        color: #3b82f6;
      }

      /* Preset Toast */
      .sfb-preset-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #111827;
        color: #fff;
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        z-index: 999999;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        max-width: 400px;
      }

      .sfb-preset-toast.sfb-show-toast {
        opacity: 1;
        transform: translateY(0);
      }

      .sfb-preset-toast .dashicons {
        color: #10b981;
        font-size: 20px;
        width: 20px;
        height: 20px;
        flex-shrink: 0;
      }

      @media (max-width: 1024px) {
        .sfb-branding-grid {
          grid-template-columns: 1fr;
        }

        .sfb-preview-card {
          position: static;
        }

        .sfb-save-section {
          flex-direction: column;
          align-items: stretch;
        }

        .sfb-save-button-wrapper {
          width: 100%;
          justify-content: center;
        }

        .sfb-save-button {
          flex: 1;
        }
      }

      @media (max-width: 768px) {
        .sfb-preset-grid {
          grid-template-columns: 1fr;
        }

        .sfb-color-picker-group {
          flex-wrap: wrap;
        }

        .sfb-color-text {
          width: 100%;
        }

        .sfb-logo-upload {
          flex-direction: column;
          align-items: flex-start;
        }

        .sfb-save-section {
          padding: 16px;
        }

        .sfb-tooltip-popup {
          max-width: 180px;
          font-size: 11px;
        }

        .sfb-preset-toast {
          bottom: 16px;
          right: 16px;
          left: 16px;
          max-width: none;
          font-size: 13px;
          padding: 12px 16px;
        }
      }

      /* Hide WordPress admin footer elements */
      .sfb-branding-wrap ~ #wpfooter,
      #footer-thankyou,
      #footer-upgrade {
        display: none !important;
      }
    </style>
    <?php
    $this->render_feedback_footer();
    ?>
    </div><!-- .wrap -->
    <?php
  }

  /** Consolidated Agency Page Renderer (Settings + Library) */
  function render_agency_page() {
    // Security check
    if (!sfb_is_agency_license()) {
      wp_die(__('This feature requires an Agency license.', 'submittal-builder'));
    }

    // Handle operator role assignment form submission
    if (isset($_POST['sfb_save_operator_roles']) && isset($_POST['sfb_operator_roles_nonce']) && wp_verify_nonce($_POST['sfb_operator_roles_nonce'], 'sfb_assign_operator_roles')) {
      $selected_operator_ids = isset($_POST['operator_users']) ? array_map('intval', $_POST['operator_users']) : [];
      $all_non_admin_users = get_users(['role__not_in' => ['administrator']]);

      foreach ($all_non_admin_users as $user) {
        $should_be_operator = in_array($user->ID, $selected_operator_ids, true);
        $current_roles = $user->roles;
        $is_currently_operator = in_array('sfb_operator', $current_roles, true);

        if ($should_be_operator && !$is_currently_operator) {
          // Assign operator role
          $user->set_role('sfb_operator');
          error_log('[SFB] Assigned sfb_operator role to user ' . $user->ID);
        } elseif (!$should_be_operator && $is_currently_operator) {
          // Remove operator role - restore to subscriber (or previous role)
          $user->set_role('subscriber');
          error_log('[SFB] Removed sfb_operator role from user ' . $user->ID);
        }
      }

      $operator_roles_saved = true;
    }

    // Handle handoff mode form submission
    if (isset($_POST['sfb_agency_settings_nonce']) && wp_verify_nonce($_POST['sfb_agency_settings_nonce'], 'sfb_save_agency_settings')) {
      $old_handoff_mode = get_option('sfb_client_handoff_mode', false);
      $new_handoff_mode = !empty($_POST['sfb_client_handoff_mode']);

      // Save handoff mode setting
      update_option('sfb_client_handoff_mode', $new_handoff_mode, false);

      // Handle default role switching when handoff mode changes
      if ($old_handoff_mode !== $new_handoff_mode) {
        if ($new_handoff_mode) {
          // Enabling handoff mode: switch to sfb_operator
          $current_default_role = get_option('default_role');

          // Save the current default role so we can restore it later
          update_option('sfb_handoff_previous_role', $current_default_role, false);

          // Set default role to sfb_operator
          update_option('default_role', 'sfb_operator');

          error_log('[SFB] Handoff Mode enabled: Default role changed from ' . $current_default_role . ' to sfb_operator');
        } else {
          // Disabling handoff mode: restore previous role
          $previous_role = get_option('sfb_handoff_previous_role', 'subscriber');

          // Restore previous default role
          update_option('default_role', $previous_role);

          // Clean up the stored previous role
          delete_option('sfb_handoff_previous_role');

          error_log('[SFB] Handoff Mode disabled: Default role restored to ' . $previous_role);
        }
      }

      $settings_saved = true;
    }

    // Handle delete pack action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['pack_id']) && check_admin_referer('sfb_delete_pack_' . $_GET['pack_id'])) {
      $pack_id = sanitize_text_field($_GET['pack_id']);
      $packs = get_option('sfb_agency_packs', []);
      $packs = array_filter($packs, function($pack) use ($pack_id) {
        return $pack['id'] !== $pack_id;
      });
      update_option('sfb_agency_packs', array_values($packs), false);

      // Redirect to remove query params
      wp_redirect(admin_url('admin.php?page=sfb-agency&deleted=1'));
      exit;
    }

    // Get current settings
    $handoff_mode = get_option('sfb_client_handoff_mode', false);
    $packs = get_option('sfb_agency_packs', []);

    ?>
    <div class="wrap sfb-agency-wrap">
      <h1 class="wp-heading-inline">
        💼 <?php esc_html_e('Agency', 'submittal-builder'); ?>
        <span class="sfb-agency-badge">AGENCY</span>
      </h1>

      <p class="description" style="margin-top:12px;margin-bottom:24px;">
        <?php esc_html_e('Agency-specific tools: save reusable catalog Packs, manage brand presets, configure white-label mode, and control client handoff settings.', 'submittal-builder'); ?>
      </p>

      <?php if (isset($settings_saved)): ?>
      <div class="notice notice-success is-dismissible" style="margin-bottom: 20px;">
        <p><strong>✅ <?php echo esc_html__('Agency settings saved successfully!', 'submittal-builder'); ?></strong></p>
      </div>
      <?php endif; ?>

      <?php if (isset($operator_roles_saved)): ?>
      <div class="notice notice-success is-dismissible" style="margin-bottom: 20px;">
        <p><strong>✅ <?php echo esc_html__('Operator roles updated successfully!', 'submittal-builder'); ?></strong></p>
      </div>
      <?php endif; ?>

      <?php if (isset($_GET['deleted'])): ?>
      <div class="notice notice-success is-dismissible" style="margin-bottom: 20px;">
        <p><?php esc_html_e('Pack deleted successfully.', 'submittal-builder'); ?></p>
      </div>
      <?php endif; ?>

      <!-- Client Handoff Mode Section -->
      <form method="post" action="">
        <?php wp_nonce_field('sfb_save_agency_settings', 'sfb_agency_settings_nonce'); ?>

        <div class="sfb-card sfb-handoff-card">
          <h2>
            🤝 <?php echo esc_html__('Client Handoff Mode', 'submittal-builder'); ?>
          </h2>
          <p class="sfb-muted">
            <?php echo esc_html__('One-click toggle to make the site safe for client use. Hides agency-specific features while maintaining full functionality for clients.', 'submittal-builder'); ?>
          </p>

          <!-- Enable Handoff Mode Toggle -->
          <div class="sfb-field-group" style="margin-top:24px;">
            <label class="sfb-checkbox-wrapper">
              <input type="checkbox"
                     id="sfb-client-handoff-mode"
                     name="sfb_client_handoff_mode"
                     value="1"
                     <?php checked($handoff_mode); ?>>
              <span class="sfb-checkbox-label">
                <strong><?php esc_html_e('Enable Client Handoff Mode', 'submittal-builder'); ?></strong>
                <small><?php esc_html_e('Hide Agency Packs and Brand Presets management from the admin interface.', 'submittal-builder'); ?></small>
              </span>
            </label>
          </div>

          <!-- What Changes Info Box -->
          <div class="sfb-info-box" style="margin-top:24px;">
            <div class="sfb-info-box-header">
              <strong>ℹ️ <?php esc_html_e('What changes when enabled?', 'submittal-builder'); ?></strong>
            </div>
            <div class="sfb-info-box-content">
              <ul class="sfb-info-list">
                <li>
                  <span class="sfb-info-icon">📦</span>
                  <strong><?php esc_html_e('Agency Packs', 'submittal-builder'); ?>:</strong>
                  <?php esc_html_e('Section below hidden (your saved Packs remain safe)', 'submittal-builder'); ?>
                </li>
                <li>
                  <span class="sfb-info-icon">🎨</span>
                  <strong><?php esc_html_e('Brand Presets', 'submittal-builder'); ?>:</strong>
                  <?php esc_html_e('Management panel hidden on Branding page (active preset still works)', 'submittal-builder'); ?>
                </li>
                <li>
                  <span class="sfb-info-icon">✅</span>
                  <strong><?php esc_html_e('Client Access', 'submittal-builder'); ?>:</strong>
                  <?php esc_html_e('Full access to Builder, Settings, Branding, Tracking, and Leads', 'submittal-builder'); ?>
                </li>
                <li>
                  <span class="sfb-info-icon">🌐</span>
                  <strong><?php esc_html_e('Frontend', 'submittal-builder'); ?>:</strong>
                  <?php esc_html_e('No changes - frontend remains identical', 'submittal-builder'); ?>
                </li>
              </ul>
              <p class="sfb-info-note">
                <?php esc_html_e('Toggle off anytime to instantly restore full agency access. All data remains intact.', 'submittal-builder'); ?>
              </p>
            </div>
          </div>

          <!-- Save Button -->
          <div style="margin-top:24px;">
            <button type="submit" class="button button-primary button-large">
              <?php esc_html_e('Save Handoff Settings', 'submittal-builder'); ?>
            </button>
          </div>
        </div>
      </form>

      <!-- Assign Operator Role Tool -->
      <div class="sfb-card" style="margin-top:32px; border: 2px solid #e0e7ff; background: linear-gradient(to bottom, #f5f7ff, #fff);">
        <h2>👥 <?php esc_html_e('Assign Operator Role', 'submittal-builder'); ?></h2>
        <p class="sfb-muted">
          <?php esc_html_e('Assign the Operator role to users who should have limited access. Operators can use the Builder, view Tracking & Leads, but cannot edit Catalog, Branding, or Agency features.', 'submittal-builder'); ?>
        </p>

        <?php
        // Get all users except admins
        $users = get_users(['role__not_in' => ['administrator']]);
        $operator_users = get_users(['role' => 'sfb_operator']);
        $operator_ids = wp_list_pluck($operator_users, 'ID');
        ?>

        <?php if (empty($users)): ?>
          <div style="padding:24px;text-align:center;background:#f9fafb;border-radius:8px;margin-top:24px;">
            <p style="color:#6b7280;margin:0;">
              <?php esc_html_e('No non-administrator users found.', 'submittal-builder'); ?>
            </p>
          </div>
        <?php else: ?>
          <form method="post" action="" style="margin-top:24px;">
            <?php wp_nonce_field('sfb_assign_operator_roles', 'sfb_operator_roles_nonce'); ?>

            <div style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:6px;padding:16px;background:#fff;">
              <?php foreach ($users as $user):
                $is_operator = in_array($user->ID, $operator_ids, true);
                $current_role = !empty($user->roles) ? ucfirst($user->roles[0]) : 'No Role';
              ?>
                <label style="display:flex;align-items:center;padding:12px;margin-bottom:8px;background:#f9fafb;border-radius:6px;cursor:pointer;transition:background 0.2s;" class="sfb-operator-checkbox-wrapper">
                  <input
                    type="checkbox"
                    name="operator_users[]"
                    value="<?php echo esc_attr($user->ID); ?>"
                    <?php checked($is_operator); ?>
                    style="margin:0 12px 0 0;">
                  <div style="flex:1;">
                    <strong><?php echo esc_html($user->display_name); ?></strong>
                    <span style="color:#64748b;font-size:13px;margin-left:8px;">(<?php echo esc_html($user->user_email); ?>)</span>
                    <br>
                    <span style="color:#64748b;font-size:12px;">
                      <?php echo esc_html(sprintf(__('Current role: %s', 'submittal-builder'), $current_role)); ?>
                    </span>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>

            <div style="margin-top:16px;padding:12px;background:#fef3c7;border:1px solid #fbbf24;border-radius:6px;">
              <p style="margin:0;font-size:13px;color:#78350f;">
                ℹ️ <?php esc_html_e('Assigning the Operator role will replace the user\'s current role. Use this in combination with Handoff Mode to restrict user access.', 'submittal-builder'); ?>
              </p>
            </div>

            <div style="margin-top:24px;">
              <button type="submit" name="sfb_save_operator_roles" class="button button-primary">
                <?php esc_html_e('Update Operator Roles', 'submittal-builder'); ?>
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <?php if (!$handoff_mode): ?>
      <!-- Agency Packs Section (hidden in handoff mode) -->
      <div class="sfb-card" style="margin-top:32px;">
        <h2>📦 <?php esc_html_e('Agency Packs', 'submittal-builder'); ?></h2>
        <p class="sfb-muted">
          <?php esc_html_e('Reusable catalog Packs created from your sites. Use these to quickly seed new sites with pre-configured catalogs and branding.', 'submittal-builder'); ?>
        </p>

        <?php if (empty($packs)): ?>
          <!-- Empty state -->
          <div style="padding:48px;text-align:center;background:#f9fafb;border-radius:8px;margin-top:24px;">
            <div style="font-size:48px;margin-bottom:16px;opacity:0.3;">📦</div>
            <h3 style="margin:0 0 12px 0;color:#374151;">
              <?php esc_html_e('No Agency Packs yet', 'submittal-builder'); ?>
            </h3>
            <p style="color:#6b7280;margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;">
              <?php esc_html_e('Create reusable Packs from your Builder catalog. Go to Builder → Save as Pack to get started.', 'submittal-builder'); ?>
            </p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>" class="button button-primary">
              <?php esc_html_e('Go to Builder', 'submittal-builder'); ?>
            </a>
          </div>
        <?php else: ?>
          <!-- Packs list table -->
          <table class="wp-list-table widefat fixed striped" style="margin-top:24px;">
            <thead>
              <tr>
                <th style="width:40%;"><?php esc_html_e('Pack Name', 'submittal-builder'); ?></th>
                <th style="width:15%;"><?php esc_html_e('Products', 'submittal-builder'); ?></th>
                <th style="width:15%;"><?php esc_html_e('Branding', 'submittal-builder'); ?></th>
                <th style="width:15%;"><?php esc_html_e('Updated', 'submittal-builder'); ?></th>
                <th style="width:15%;"><?php esc_html_e('Actions', 'submittal-builder'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($packs as $pack):
                $product_count = $pack['counts']['products'] ?? 0;
                $has_branding = !empty($pack['has_branding']);
                $updated = $pack['updated_at'] ?? '';
                $pack_id = $pack['id'];
                $pack_name = $pack['name'] ?? 'Unnamed Pack';
              ?>
                <tr>
                  <td>
                    <strong><?php echo esc_html($pack_name); ?></strong>
                  </td>
                  <td>
                    <?php echo esc_html(number_format($product_count)); ?>
                  </td>
                  <td>
                    <?php if ($has_branding): ?>
                      <span style="color:#10b981;">✓ <?php esc_html_e('Included', 'submittal-builder'); ?></span>
                    <?php else: ?>
                      <span style="color:#9ca3af;">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $updated ? esc_html(human_time_diff(strtotime($updated), current_time('timestamp')) . ' ago') : '—'; ?>
                  </td>
                  <td>
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=sfb_pack_export&pack_id=' . urlencode($pack_id)), 'sfb_export_pack_' . $pack_id)); ?>"
                       class="button button-small"
                       download="<?php echo esc_attr(sanitize_file_name($pack_name) . '.json'); ?>">
                      <?php esc_html_e('Export JSON', 'submittal-builder'); ?>
                    </a>
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=sfb-agency&action=delete&pack_id=' . urlencode($pack_id)), 'sfb_delete_pack_' . $pack_id)); ?>"
                       class="button button-small button-link-delete"
                       onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this Pack? This cannot be undone.', 'submittal-builder'); ?>');">
                      <?php esc_html_e('Delete', 'submittal-builder'); ?>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Info box -->
          <div style="margin-top:24px;padding:16px;background:#f0f9ff;border-left:4px solid #0ea5e9;border-radius:8px;">
            <p style="margin:0;color:#0c4a6e;font-size:13px;">
              <strong><?php esc_html_e('💡 Using Packs:', 'submittal-builder'); ?></strong>
              <?php esc_html_e('Export a Pack as JSON and use it during onboarding on another site. Go to Welcome → Load Sample Catalog → Upload JSON to seed.', 'submittal-builder'); ?>
            </p>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Advanced Lead Routing Section (Agency only, hidden in handoff mode) -->
      <?php if (!$handoff_mode): ?>
      <div class="sfb-card" style="margin-top:32px;">
        <h2>🔀 <?php esc_html_e('Advanced Lead Routing', 'submittal-builder'); ?></h2>
        <p class="sfb-muted">
          <?php esc_html_e('Automatically route leads to email recipients and/or webhooks based on rules (email domain, UTM parameters, category).', 'submittal-builder'); ?>
        </p>

        <div id="sfb-lead-routing-app">
          <p style="text-align:center;padding:40px;color:#9ca3af;">
            <?php esc_html_e('Loading...', 'submittal-builder'); ?>
          </p>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <style>
      .sfb-agency-wrap {
        max-width: 1200px;
      }
      .sfb-handoff-card {
        border: 2px solid #dbeafe !important;
        background: linear-gradient(to bottom, #eff6ff, #fff) !important;
      }
      .sfb-info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 16px 20px;
      }
      .sfb-info-box-header {
        color: #0c4a6e;
        margin-bottom: 12px;
        font-size: 14px;
      }
      .sfb-info-box-content {
        color: #0369a1;
      }
      .sfb-info-list {
        list-style: none;
        padding: 0;
        margin: 0 0 16px 0;
      }
      .sfb-info-list li {
        padding: 8px 0;
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        line-height: 1.6;
      }
      .sfb-info-icon {
        flex-shrink: 0;
        font-size: 16px;
      }
      .sfb-info-note {
        margin: 0;
        padding: 12px;
        background: rgba(14, 165, 233, 0.1);
        border-left: 3px solid #0ea5e9;
        border-radius: 4px;
        font-size: 13px;
      }
      .button-link-delete {
        color: #dc2626 !important;
        border-color: #dc2626 !important;
      }
      .button-link-delete:hover {
        background: #dc2626 !important;
        color: white !important;
      }
    </style>

    <script>
      // Localize routing data for JavaScript
      window.SFB_Routing = <?php echo wp_json_encode([
        'enabled' => SFB_Agency_Lead_Routing::is_enabled(),
        'rules' => SFB_Agency_Lead_Routing::get_rules(),
        'fallback' => SFB_Agency_Lead_Routing::get_fallback(),
        'log' => SFB_Agency_Lead_Routing::get_log(20),
        'categories' => SFB_Agency_Lead_Routing::get_top_categories(),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('sfb_lead_routing'),
      ]); ?>;
    </script>
    <?php
    $this->render_feedback_footer();
    ?>
    </div><!-- .wrap -->
    <?php
  }

  /** Settings Page Renderer */
  function render_settings_page() {
    // Get current settings
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $is_pro = sfb_is_pro_active() || defined('SFB_PRO_DEV');

    // Check if settings were just saved
    $settings_saved = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
    ?>
    <div class="wrap sfb-settings-wrap">
      <h1><?php echo esc_html__('Settings', 'submittal-builder'); ?></h1>
      <p style="color: #6b7280; margin-top: -8px; margin-bottom: 24px;">
        <?php echo esc_html__('Configure plugin features including draft saving, lead capture, auto-email delivery, and data retention policies.', 'submittal-builder'); ?>
      </p>

      <?php if ($settings_saved): ?>
      <div class="notice notice-success is-dismissible" style="margin-bottom: 20px;">
        <p><strong>✅ <?php echo esc_html__('Settings saved successfully!', 'submittal-builder'); ?></strong></p>
      </div>
      <?php endif; ?>

      <form method="post" action="options.php" id="sfb-settings-form">
        <?php settings_fields('sfb_settings_group'); ?>

        <!-- Draft Settings Card -->
        <div class="sfb-card">
          <h2>💾 <?php echo esc_html__('Draft Settings', 'submittal-builder'); ?></h2>
          <p class="sfb-muted">
            <?php echo esc_html__('Control how and where user selections are saved during the form-building process.', 'submittal-builder'); ?>
          </p>

          <!-- Local Autosave -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">📱</div>
            <div class="sfb-setting-content">
              <label class="sfb-checkbox-label">
                <input type="checkbox"
                       name="<?php echo esc_attr($this->option_key()); ?>[drafts_autosave_enabled]"
                       value="1"
                       <?php checked(!empty($options['drafts_autosave_enabled'])); ?>>
                <span class="sfb-setting-title"><?php esc_html_e('Enable local autosave', 'submittal-builder'); ?></span>
              </label>
              <p class="sfb-setting-desc">
                <?php esc_html_e('Automatically saves user selections to browser localStorage. Selections persist across sessions without requiring server storage.', 'submittal-builder'); ?>
              </p>
            </div>
          </div>

          <!-- Server Drafts -->
          <div class="sfb-setting-row <?php echo !$is_pro ? 'sfb-setting-disabled' : ''; ?>">
            <div class="sfb-setting-icon">☁️</div>
            <div class="sfb-setting-content">
              <label class="sfb-checkbox-label">
                <input type="checkbox"
                       name="<?php echo esc_attr($this->option_key()); ?>[drafts_server_enabled]"
                       value="1"
                       <?php checked(!empty($options['drafts_server_enabled'])); ?>
                       <?php disabled(!$is_pro); ?>>
                <span class="sfb-setting-title">
                  <?php esc_html_e('Enable server-side shareable drafts', 'submittal-builder'); ?>
                  <?php if (!$is_pro): ?>
                    <span class="sfb-pro-badge"><?php esc_html_e('PRO', 'submittal-builder'); ?></span>
                  <?php endif; ?>
                </span>
              </label>
              <p class="sfb-setting-desc">
                <?php esc_html_e('Allows users to save progress to the server and generate a shareable URL. Ideal for collaboration or accessing drafts from multiple devices.', 'submittal-builder'); ?>
              </p>
              <?php if (!$is_pro): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-upgrade')); ?>" class="sfb-upgrade-link">
                  <?php esc_html_e('Upgrade to Pro to unlock →', 'submittal-builder'); ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Privacy & Retention Card -->
        <div class="sfb-card">
          <h2>🔒 <?php echo esc_html__('Privacy & Retention', 'submittal-builder'); ?></h2>
          <p class="sfb-muted">
            <?php echo esc_html__('Configure data retention and privacy policies for server-stored drafts.', 'submittal-builder'); ?>
          </p>

          <!-- Draft Expiry -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">⏱️</div>
            <div class="sfb-setting-content">
              <label class="sfb-setting-title" for="sfb-expiry-days">
                <?php esc_html_e('Draft expiry period', 'submittal-builder'); ?>
              </label>
              <p class="sfb-setting-desc" style="margin-top: 4px;">
                <?php esc_html_e('Server drafts are automatically deleted after this many days. Range: 1-365 days.', 'submittal-builder'); ?>
              </p>
              <div class="sfb-input-group">
                <input type="number"
                       id="sfb-expiry-days"
                       name="<?php echo esc_attr($this->option_key()); ?>[drafts_expiry_days]"
                       value="<?php echo esc_attr($options['drafts_expiry_days']); ?>"
                       min="1"
                       max="365"
                       class="sfb-number-input">
                <span class="sfb-input-suffix"><?php esc_html_e('days', 'submittal-builder'); ?></span>
              </div>
            </div>
          </div>

          <!-- Privacy Note -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">📝</div>
            <div class="sfb-setting-content">
              <label class="sfb-setting-title" for="sfb-privacy-note">
                <?php esc_html_e('Privacy notice for users', 'submittal-builder'); ?>
              </label>
              <p class="sfb-setting-desc" style="margin-top: 4px;">
                <?php esc_html_e('Optional message displayed to users when saving drafts (e.g., data retention policy, GDPR notice).', 'submittal-builder'); ?>
              </p>
              <textarea id="sfb-privacy-note"
                        name="<?php echo esc_attr($this->option_key()); ?>[drafts_privacy_note]"
                        rows="3"
                        class="sfb-textarea"
                        placeholder="<?php esc_attr_e('e.g., Draft data is stored for 45 days and automatically deleted.', 'submittal-builder'); ?>"><?php echo esc_textarea($options['drafts_privacy_note']); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Performance Card -->
        <div class="sfb-card">
          <h2>⚡ <?php echo esc_html__('Performance', 'submittal-builder'); ?></h2>
          <p class="sfb-muted">
            <?php echo esc_html__('Control save request throttling to prevent server overload.', 'submittal-builder'); ?>
          </p>

          <!-- Rate Limit -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">🚦</div>
            <div class="sfb-setting-content">
              <label class="sfb-setting-title" for="sfb-rate-limit">
                <?php esc_html_e('Save rate limit', 'submittal-builder'); ?>
              </label>
              <p class="sfb-setting-desc" style="margin-top: 4px;">
                <?php esc_html_e('Minimum time between save requests from the same user. Prevents excessive server load. Range: 5-120 seconds.', 'submittal-builder'); ?>
              </p>
              <div class="sfb-input-group">
                <input type="number"
                       id="sfb-rate-limit"
                       name="<?php echo esc_attr($this->option_key()); ?>[drafts_rate_limit_sec]"
                       value="<?php echo esc_attr($options['drafts_rate_limit_sec']); ?>"
                       min="5"
                       max="120"
                       class="sfb-number-input">
                <span class="sfb-input-suffix"><?php esc_html_e('seconds', 'submittal-builder'); ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Lead Capture Card (Pro) -->
        <div class="sfb-card">
          <h2>📧 <?php echo esc_html__('Lead Capture (Pro)', 'submittal-builder'); ?></h2>
          <p class="sfb-muted">
            <?php echo esc_html__('Capture user contact information before allowing PDF downloads. Stores leads in the database and sends email notifications.', 'submittal-builder'); ?>
          </p>

          <!-- Enable Lead Capture -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">🎯</div>
            <div class="sfb-setting-content">
              <label class="sfb-checkbox-label">
                <!-- Hidden field ensures unchecked checkbox updates the option to false -->
                <input type="hidden" name="sfb_lead_capture_enabled" value="0">
                <input type="checkbox"
                       name="sfb_lead_capture_enabled"
                       value="1"
                       <?php checked(get_option('sfb_lead_capture_enabled', false)); ?>>
                <span class="sfb-setting-title"><?php esc_html_e('Enable lead capture modal', 'submittal-builder'); ?></span>
              </label>
              <p class="sfb-setting-desc">
                <?php esc_html_e('When enabled, users must enter their email (and optionally phone) before downloading PDFs. Leads are stored in the database with timestamp, IP hash, and UTM tracking.', 'submittal-builder'); ?>
              </p>
            </div>
          </div>

          <!-- BCC Admin Email -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">📬</div>
            <div class="sfb-setting-content">
              <label class="sfb-checkbox-label">
                <input type="checkbox"
                       name="sfb_lead_bcc_admin"
                       value="1"
                       <?php checked(get_option('sfb_lead_bcc_admin', false)); ?>>
                <span class="sfb-setting-title"><?php esc_html_e('BCC admin on lead emails', 'submittal-builder'); ?></span>
              </label>
              <p class="sfb-setting-desc">
                <?php printf(esc_html__('When enabled, all lead notification emails will BCC %s', 'submittal-builder'), '<code>' . esc_html(get_option('admin_email')) . '</code>'); ?>
              </p>
            </div>
          </div>
        </div>

        <!-- Weekly Lead Export Card (Agency) -->
        <?php if (sfb_is_agency_license()): ?>
        <div class="sfb-card">
          <h2>📅 <?php echo esc_html__('Weekly Lead Export (Agency)', 'submittal-builder'); ?></h2>
          <p class="sfb-muted">
            <?php echo esc_html__('Automatically send a CSV of new leads each week to a configured email address. Ideal for project managers who need regular lead reports.', 'submittal-builder'); ?>
          </p>

          <!-- Enable Weekly Export -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">✉️</div>
            <div class="sfb-setting-content">
              <label class="sfb-checkbox-label">
                <input type="checkbox"
                       name="sfb_lead_weekly_export_enabled"
                       value="1"
                       id="sfb-weekly-export-enabled"
                       <?php checked(get_option('sfb_lead_weekly_export_enabled', false)); ?>>
                <span class="sfb-setting-title"><?php esc_html_e('Enable weekly lead CSV email', 'submittal-builder'); ?></span>
              </label>
              <p class="sfb-setting-desc">
                <?php esc_html_e('When enabled, a CSV file with new leads will be emailed weekly. Only leads that haven\'t been sent before will be included.', 'submittal-builder'); ?>
              </p>
            </div>
          </div>

          <!-- Email Address -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">📧</div>
            <div class="sfb-setting-content">
              <label class="sfb-setting-title" for="sfb-weekly-export-email">
                <?php esc_html_e('Recipient email address', 'submittal-builder'); ?>
              </label>
              <p class="sfb-setting-desc" style="margin-top: 4px;">
                <?php esc_html_e('Email address to receive weekly lead exports. Typically your project manager or sales team.', 'submittal-builder'); ?>
              </p>
              <input type="email"
                     id="sfb-weekly-export-email"
                     name="sfb_lead_weekly_export_email"
                     value="<?php echo esc_attr(get_option('sfb_lead_weekly_export_email', '')); ?>"
                     class="sfb-text-input"
                     placeholder="<?php esc_attr_e('project-manager@yourcompany.com', 'submittal-builder'); ?>">
            </div>
          </div>

          <!-- Day and Time -->
          <div class="sfb-setting-row">
            <div class="sfb-setting-icon">🕐</div>
            <div class="sfb-setting-content">
              <label class="sfb-setting-title">
                <?php esc_html_e('Schedule (site timezone)', 'submittal-builder'); ?>
              </label>
              <p class="sfb-setting-desc" style="margin-top: 4px;">
                <?php
                $tz = wp_timezone_string();
                printf(
                  esc_html__('Choose when to send the weekly export. Time is in %s timezone.', 'submittal-builder'),
                  '<strong>' . esc_html($tz) . '</strong>'
                );
                ?>
              </p>
              <div style="display: flex; gap: 12px; margin-top: 8px;">
                <div style="flex: 1;">
                  <label for="sfb-weekly-export-day" style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                    <?php esc_html_e('Day of week', 'submittal-builder'); ?>
                  </label>
                  <select id="sfb-weekly-export-day"
                          name="sfb_lead_weekly_export_day"
                          class="sfb-select-input">
                    <?php
                    $current_day = get_option('sfb_lead_weekly_export_day', 'monday');
                    $days = [
                      'monday' => __('Monday', 'submittal-builder'),
                      'tuesday' => __('Tuesday', 'submittal-builder'),
                      'wednesday' => __('Wednesday', 'submittal-builder'),
                      'thursday' => __('Thursday', 'submittal-builder'),
                      'friday' => __('Friday', 'submittal-builder'),
                      'saturday' => __('Saturday', 'submittal-builder'),
                      'sunday' => __('Sunday', 'submittal-builder'),
                    ];
                    foreach ($days as $value => $label) {
                      printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected($current_day, $value, false),
                        esc_html($label)
                      );
                    }
                    ?>
                  </select>
                </div>
                <div style="flex: 1;">
                  <label for="sfb-weekly-export-time" style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                    <?php esc_html_e('Time of day', 'submittal-builder'); ?>
                  </label>
                  <input type="time"
                         id="sfb-weekly-export-time"
                         name="sfb_lead_weekly_export_time"
                         value="<?php echo esc_attr(get_option('sfb_lead_weekly_export_time', '09:00')); ?>"
                         class="sfb-text-input">
                </div>
              </div>
            </div>
          </div>

          <!-- Manual Send Button -->
          <div class="sfb-setting-row" style="background: #eef2ff; border: 1px solid #c7d2fe;">
            <div class="sfb-setting-icon">🚀</div>
            <div class="sfb-setting-content">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <div class="sfb-setting-title"><?php esc_html_e('Test weekly export', 'submittal-builder'); ?></div>
                  <p class="sfb-setting-desc" style="margin-top: 4px;">
                    <?php esc_html_e('Send a test email now with all new leads. Use this to verify your configuration before the scheduled run.', 'submittal-builder'); ?>
                  </p>
                </div>
                <button type="button"
                        id="sfb-send-now-btn"
                        class="button button-secondary"
                        style="white-space: nowrap;">
                  <?php esc_html_e('Send Now', 'submittal-builder'); ?>
                </button>
              </div>
              <div id="sfb-send-now-result" style="margin-top: 12px; display: none;"></div>
            </div>
          </div>
        </div>

        <script>
        jQuery(function($) {
          $('#sfb-send-now-btn').on('click', function() {
            var btn = $(this);
            var result = $('#sfb-send-now-result');

            btn.prop('disabled', true).text('<?php esc_html_e('Sending...', 'submittal-builder'); ?>');
            result.hide();

            $.post(ajaxurl, {
              action: 'sfb_send_weekly_export_now',
              nonce: '<?php echo wp_create_nonce('sfb_send_weekly_export_now'); ?>'
            }, function(response) {
              btn.prop('disabled', false).text('<?php esc_html_e('Send Now', 'submittal-builder'); ?>');

              if (response.success) {
                result.html('<div class="notice notice-success inline" style="margin: 0; padding: 8px 12px;"><p style="margin: 0;"><strong>✅ ' + response.data.message + '</strong></p></div>').show();
              } else {
                result.html('<div class="notice notice-error inline" style="margin: 0; padding: 8px 12px;"><p style="margin: 0;"><strong>❌ ' + response.data + '</strong></p></div>').show();
              }
            }).fail(function() {
              btn.prop('disabled', false).text('<?php esc_html_e('Send Now', 'submittal-builder'); ?>');
              result.html('<div class="notice notice-error inline" style="margin: 0; padding: 8px 12px;"><p style="margin: 0;"><strong>❌ Failed to send. Please try again.</strong></p></div>').show();
            });
          });
        });
        </script>
        <?php endif; ?>

        <!-- Save Button -->
        <div class="sfb-save-section">
          <button type="submit" class="button button-primary button-large">
            <span class="dashicons dashicons-saved" style="margin-top: 3px;"></span>
            <?php esc_html_e('Save Changes', 'submittal-builder'); ?>
          </button>
        </div>
      </form>
    </div>

    <style>
      /* Hide WordPress admin footer elements */
      .sfb-settings-wrap ~ #wpfooter,
      #footer-thankyou,
      #footer-upgrade {
        display: none !important;
      }

      .sfb-settings-wrap {
        max-width: 880px;
      }

      .sfb-settings-wrap .sfb-card {
        background: #fff;
        border: 1px solid #e9edf3;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        padding: 20px 24px;
        margin-bottom: 20px;
      }

      .sfb-settings-wrap .sfb-card h2 {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        margin: 0 0 8px;
        color: #111827;
      }

      .sfb-settings-wrap .sfb-muted {
        color: #6b7280;
        font-size: 13px;
        margin: 0 0 20px;
        line-height: 1.5;
      }

      .sfb-setting-row {
        display: flex;
        gap: 16px;
        padding: 16px;
        border-radius: 8px;
        background: #f9fafb;
        margin-bottom: 12px;
        transition: background 0.15s;
      }

      .sfb-setting-row:last-child {
        margin-bottom: 0;
      }

      .sfb-setting-row:hover {
        background: #f3f4f6;
      }

      .sfb-setting-row.sfb-setting-disabled {
        opacity: 0.7;
      }

      .sfb-setting-icon {
        font-size: 24px;
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .sfb-setting-content {
        flex: 1;
        min-width: 0;
      }

      .sfb-checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        margin-bottom: 0;
      }

      .sfb-checkbox-label input[type="checkbox"] {
        margin: 0;
        width: 18px;
        height: 18px;
        cursor: pointer;
      }

      .sfb-setting-title {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        display: inline-flex;
        align-items: center;
        gap: 6px;
      }

      .sfb-setting-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
        margin: 6px 0 0 0;
      }

      .sfb-pro-badge {
        display: inline-block;
        padding: 2px 8px;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: #fff;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);
      }

      .sfb-upgrade-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 10px;
        padding: 8px 14px;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        transition: all 0.2s ease;
      }

      .sfb-upgrade-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35);
        color: #fff;
      }

      .sfb-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
      }

      .sfb-number-input {
        width: 100px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.15s;
      }

      .sfb-number-input:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }

      .sfb-input-suffix {
        color: #6b7280;
        font-size: 14px;
      }

      .sfb-textarea {
        width: 100%;
        max-width: 600px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        line-height: 1.5;
        resize: vertical;
        margin-top: 10px;
        transition: border-color 0.15s;
      }

      .sfb-textarea:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }

      .sfb-text-input {
        width: 100%;
        max-width: 400px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.15s;
      }

      .sfb-text-input:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }

      .sfb-select-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.15s;
      }

      .sfb-select-input:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
      }

      .sfb-save-section {
        display: flex;
        justify-content: flex-start;
        padding: 24px 0 24px 24px;
        margin-top: 12px;
      }

      .sfb-save-section .button-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        transition: all 0.2s ease;
        cursor: pointer;
      }

      .sfb-save-section .button-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.4);
      }

      .sfb-save-section .button-primary .dashicons {
        font-size: 18px;
        width: 18px;
        height: 18px;
      }

      @media (max-width: 768px) {
        .sfb-setting-row {
          flex-direction: column;
          gap: 12px;
        }

        .sfb-setting-icon {
          align-self: flex-start;
        }

        .sfb-save-section {
          justify-content: center;
        }

        .sfb-save-section .button-primary {
          width: 100%;
          justify-content: center;
        }
      }
    </style>
    </div><!-- .wrap -->
    <?php
  }

  /** Register Draft Settings (Settings API) */
  function register_draft_settings() {
    // Register setting
    register_setting(
      'sfb_settings_group',
      $this->option_key(),
      [
        'sanitize_callback' => [$this, 'sanitize_settings'],
        'default' => $this->default_settings()
      ]
    );

    // Add Drafts section
    add_settings_section(
      'sfb_section_drafts',
      __('Drafts', 'submittal-builder'),
      [$this, 'render_drafts_section_desc'],
      'sfb_settings_page'
    );

    // Add fields
    add_settings_field(
      'drafts_autosave_enabled',
      __('Local Autosave', 'submittal-builder'),
      [$this, 'render_autosave_field'],
      'sfb_settings_page',
      'sfb_section_drafts'
    );

    add_settings_field(
      'drafts_server_enabled',
      __('Server Drafts', 'submittal-builder'),
      [$this, 'render_server_drafts_field'],
      'sfb_settings_page',
      'sfb_section_drafts'
    );

    add_settings_field(
      'drafts_expiry_days',
      __('Draft Expiry', 'submittal-builder'),
      [$this, 'render_expiry_field'],
      'sfb_settings_page',
      'sfb_section_drafts'
    );

    add_settings_field(
      'drafts_rate_limit_sec',
      __('Save Rate Limit', 'submittal-builder'),
      [$this, 'render_rate_limit_field'],
      'sfb_settings_page',
      'sfb_section_drafts'
    );

    add_settings_field(
      'drafts_privacy_note',
      __('Privacy Note', 'submittal-builder'),
      [$this, 'render_privacy_note_field'],
      'sfb_settings_page',
      'sfb_section_drafts'
    );

    // Add License Behavior section
    add_settings_section(
      'sfb_section_license_behavior',
      __('License & Cleanup', 'submittal-builder'),
      [$this, 'render_license_behavior_section_desc'],
      'sfb_settings_page'
    );

    // Auto-deactivate license on plugin deactivation
    register_setting('sfb_settings_group', 'sfb_auto_deactivate_on_deactivate', [
      'sanitize_callback' => function($value) {
        return !empty($value);
      },
      'default' => true
    ]);

    add_settings_field(
      'sfb_auto_deactivate_on_deactivate',
      __('Auto-deactivate License', 'submittal-builder'),
      [$this, 'render_auto_deactivate_field'],
      'sfb_settings_page',
      'sfb_section_license_behavior'
    );

    // Remove all data on uninstall
    register_setting('sfb_settings_group', 'sfb_remove_data_on_uninstall', [
      'sanitize_callback' => function($value) {
        return !empty($value);
      },
      'default' => false
    ]);

    add_settings_field(
      'sfb_remove_data_on_uninstall',
      __('Remove Data on Uninstall', 'submittal-builder'),
      [$this, 'render_remove_data_field'],
      'sfb_settings_page',
      'sfb_section_license_behavior'
    );

    // Add External Links section
    add_settings_section(
      'sfb_section_external_links',
      __('External Links', 'submittal-builder'),
      [$this, 'render_external_links_section_desc'],
      'sfb_settings_page'
    );

    // Register individual link settings
    $link_fields = [
      'account'   => __('Account Dashboard URL', 'submittal-builder'),
      'invoices'  => __('Invoices/Downloads URL', 'submittal-builder'),
      'docs'      => __('Documentation URL', 'submittal-builder'),
      'tutorials' => __('Tutorials URL', 'submittal-builder'),
      'roadmap'   => __('Roadmap/Feature Requests URL', 'submittal-builder'),
      'support'   => __('Support URL or Email', 'submittal-builder'),
      'renew'     => __('Renew License URL', 'submittal-builder'),
      'pricing'   => __('Pricing/Get License URL', 'submittal-builder'),
    ];

    foreach ($link_fields as $key => $label) {
      register_setting('sfb_settings_group', 'sfb_link_' . $key, [
        'sanitize_callback' => function($value) use ($key) {
          if (empty($value)) return '';
          // Allow mailto: for support
          if ($key === 'support' && strpos($value, 'mailto:') === 0) {
            return sanitize_email(str_replace('mailto:', '', $value)) ? $value : '';
          }
          return esc_url_raw($value);
        },
        'default' => ''
      ]);

      add_settings_field(
        'sfb_link_' . $key,
        $label,
        [$this, 'render_link_field'],
        'sfb_settings_page',
        'sfb_section_external_links',
        ['key' => $key, 'label' => $label]
      );
    }

    // Add Lead Capture section (Pro feature)
    add_settings_section(
      'sfb_section_lead_capture',
      __('Lead Capture (Pro)', 'submittal-builder'),
      [$this, 'render_lead_capture_section_desc'],
      'sfb_settings_page'
    );

    // Lead capture enabled toggle
    register_setting('sfb_settings_group', 'sfb_lead_capture_enabled', [
      'sanitize_callback' => function($value) {
        // Convert to boolean: "1" or 1 = true, anything else = false
        return $value === '1' || $value === 1 || $value === true;
      },
      'default' => false
    ]);

    add_settings_field(
      'sfb_lead_capture_enabled',
      __('Enable Lead Capture', 'submittal-builder'),
      [$this, 'render_lead_capture_enabled_field'],
      'sfb_settings_page',
      'sfb_section_lead_capture'
    );

    // BCC admin email toggle
    register_setting('sfb_settings_group', 'sfb_lead_bcc_admin', [
      'sanitize_callback' => function($value) {
        return !empty($value);
      },
      'default' => false
    ]);

    add_settings_field(
      'sfb_lead_bcc_admin',
      __('BCC Admin on Lead Emails', 'submittal-builder'),
      [$this, 'render_lead_bcc_admin_field'],
      'sfb_settings_page',
      'sfb_section_lead_capture'
    );

    // Weekly Lead Export settings (Agency)
    register_setting('sfb_settings_group', 'sfb_lead_weekly_export_enabled', [
      'sanitize_callback' => function($value) {
        return !empty($value);
      },
      'default' => false
    ]);

    register_setting('sfb_settings_group', 'sfb_lead_weekly_export_email', [
      'sanitize_callback' => 'sanitize_email',
      'default' => ''
    ]);

    register_setting('sfb_settings_group', 'sfb_lead_weekly_export_day', [
      'sanitize_callback' => function($value) {
        $valid_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        return in_array($value, $valid_days) ? $value : 'monday';
      },
      'default' => 'monday'
    ]);

    register_setting('sfb_settings_group', 'sfb_lead_weekly_export_time', [
      'sanitize_callback' => function($value) {
        // Validate time format (HH:MM)
        if (preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
          return $value;
        }
        return '09:00';
      },
      'default' => '09:00'
    ]);
  }

  /** Sanitize settings callback */
  function sanitize_settings($input) {
    $defaults = $this->default_settings();
    $sanitized = [];

    // Branding fields (keep existing)
    $sanitized['logo_url'] = isset($input['logo_url']) ? esc_url_raw($input['logo_url']) : $defaults['logo_url'];
    $sanitized['company_name'] = isset($input['company_name']) ? sanitize_text_field($input['company_name']) : $defaults['company_name'];
    $sanitized['company_address'] = isset($input['company_address']) ? sanitize_textarea_field($input['company_address']) : $defaults['company_address'];
    $sanitized['company_phone'] = isset($input['company_phone']) ? sanitize_text_field($input['company_phone']) : $defaults['company_phone'];
    $sanitized['company_website'] = isset($input['company_website']) ? sanitize_text_field($input['company_website']) : $defaults['company_website'];
    $sanitized['primary_color'] = isset($input['primary_color']) && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $input['primary_color']) ? $input['primary_color'] : $defaults['primary_color'];
    $sanitized['brand_preset'] = isset($input['brand_preset']) && in_array($input['brand_preset'], ['modern-blue', 'architect-gray', 'engineering-bold', 'clean-violet', 'custom']) ? $input['brand_preset'] : $defaults['brand_preset'];
    $sanitized['cover_default'] = !empty($input['cover_default']);
    $sanitized['footer_text'] = isset($input['footer_text']) ? sanitize_text_field($input['footer_text']) : $defaults['footer_text'];

    // Theme - enforce Pro/Agency license gate for premium themes
    $is_pro = sfb_is_pro_active() || defined('SFB_PRO_DEV');
    $requested_theme = isset($input['theme']) && in_array($input['theme'], ['engineering', 'architectural', 'corporate']) ? $input['theme'] : $defaults['theme'];
    // Free/Expired users can only use 'engineering' theme
    if (!$is_pro && in_array($requested_theme, ['architectural', 'corporate'], true)) {
      $sanitized['theme'] = 'engineering'; // Force fallback to default theme
    } else {
      $sanitized['theme'] = $requested_theme;
    }

    $sanitized['watermark'] = isset($input['watermark']) ? sanitize_text_field($input['watermark']) : $defaults['watermark'];

    // Draft settings
    $sanitized['drafts_autosave_enabled'] = !empty($input['drafts_autosave_enabled']);
    $sanitized['drafts_server_enabled'] = !empty($input['drafts_server_enabled']) && (sfb_is_pro_active() || defined('SFB_PRO_DEV'));
    $sanitized['drafts_expiry_days'] = isset($input['drafts_expiry_days']) ? max(1, min(365, intval($input['drafts_expiry_days']))) : $defaults['drafts_expiry_days'];
    $sanitized['drafts_rate_limit_sec'] = isset($input['drafts_rate_limit_sec']) ? max(5, min(120, intval($input['drafts_rate_limit_sec']))) : $defaults['drafts_rate_limit_sec'];
    $sanitized['drafts_privacy_note'] = isset($input['drafts_privacy_note']) ? sanitize_textarea_field($input['drafts_privacy_note']) : $defaults['drafts_privacy_note'];

    return $sanitized;
  }

  /** Section description */
  function render_drafts_section_desc() {
    ?>
    <p class="description">
      <?php echo esc_html__( 'Local autosave stores selections in your browser. "Save progress" (Pro) creates a private, unlisted link you can share. Drafts auto-expire after 45 days (configurable).', 'submittal-builder' ); ?>
    </p>
    <?php
  }

  /** Field: Autosave enabled */
  function render_autosave_field() {
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $checked = !empty($options['drafts_autosave_enabled']) ? 'checked' : '';
    ?>
    <label>
      <input type="checkbox" name="<?php echo esc_attr($this->option_key()); ?>[drafts_autosave_enabled]" value="1" <?php echo $checked; ?>>
      <?php esc_html_e('Enable local autosave (recommended)', 'submittal-builder'); ?>
    </label>
    <span class="sfb-field-desc"><?php esc_html_e('Automatically saves selections to browser localStorage while users work.', 'submittal-builder'); ?></span>
    <?php
  }

  /** Field: Server drafts enabled */
  function render_server_drafts_field() {
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $is_pro = sfb_is_pro_active() || defined('SFB_PRO_DEV');
    $checked = !empty($options['drafts_server_enabled']) ? 'checked' : '';
    $disabled = !$is_pro ? 'disabled' : '';
    ?>
    <label>
      <input type="checkbox" name="<?php echo esc_attr($this->option_key()); ?>[drafts_server_enabled]" value="1" <?php echo $checked; ?> <?php echo $disabled; ?>>
      <?php esc_html_e('Enable server-side shareable drafts', 'submittal-builder'); ?>
      <?php if (!$is_pro): ?>
        <span class="sfb-pro-badge"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-upgrade')); ?>" style="margin-left:6px;">
          <?php esc_html_e('Upgrade to Pro', 'submittal-builder'); ?>
        </a>
      <?php endif; ?>
    </label>
    <span class="sfb-field-desc"><?php esc_html_e('Allows users to save selections to the server and share via URL.', 'submittal-builder'); ?></span>
    <?php
  }

  /** Field: Expiry days */
  function render_expiry_field() {
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $value = isset($options['drafts_expiry_days']) ? intval($options['drafts_expiry_days']) : 45;
    ?>
    <input type="number" name="<?php echo esc_attr($this->option_key()); ?>[drafts_expiry_days]" value="<?php echo esc_attr($value); ?>" min="1" max="365" style="width:80px;">
    <span><?php esc_html_e('days', 'submittal-builder'); ?></span>
    <span class="sfb-field-desc"><?php esc_html_e('How long server drafts remain accessible before auto-deletion (1-365 days).', 'submittal-builder'); ?></span>
    <?php
  }

  /** Field: Rate limit */
  function render_rate_limit_field() {
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $value = isset($options['drafts_rate_limit_sec']) ? intval($options['drafts_rate_limit_sec']) : 20;
    ?>
    <input type="number" name="<?php echo esc_attr($this->option_key()); ?>[drafts_rate_limit_sec]" value="<?php echo esc_attr($value); ?>" min="5" max="120" style="width:80px;">
    <span><?php esc_html_e('seconds', 'submittal-builder'); ?></span>
    <span class="sfb-field-desc"><?php esc_html_e('Minimum time between save requests per user (5-120 seconds).', 'submittal-builder'); ?></span>
    <?php
  }

  /** Field: Privacy note */
  function render_privacy_note_field() {
    $options = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());
    $value = isset($options['drafts_privacy_note']) ? $options['drafts_privacy_note'] : '';
    ?>
    <textarea name="<?php echo esc_attr($this->option_key()); ?>[drafts_privacy_note]" rows="3" style="width:100%;max-width:500px;"><?php echo esc_textarea($value); ?></textarea>
    <span class="sfb-field-desc"><?php esc_html_e('Optional note shown near the Save button to inform users about privacy/retention policies.', 'submittal-builder'); ?></span>
    <?php
  }

  /** License Behavior section description */
  function render_license_behavior_section_desc() {
    ?>
    <p class="description">
      <?php esc_html_e('Control what happens when you deactivate or uninstall this plugin.', 'submittal-builder'); ?>
    </p>
    <?php if (defined('SFB_PRO_DEV') && SFB_PRO_DEV): ?>
      <div class="notice notice-warning inline" style="margin:12px 0; padding:8px 12px;">
        <p style="margin:0;">
          <strong><?php esc_html_e('Dev Override Active:', 'submittal-builder'); ?></strong>
          <?php esc_html_e('SFB_PRO_DEV is enabled. License checks and auto-deactivation are bypassed.', 'submittal-builder'); ?>
        </p>
      </div>
    <?php endif; ?>
    <?php
  }

  /** Auto-deactivate license field */
  function render_auto_deactivate_field() {
    $value = get_option('sfb_auto_deactivate_on_deactivate', true);
    ?>
    <label>
      <input
        type="checkbox"
        name="sfb_auto_deactivate_on_deactivate"
        value="1"
        <?php checked($value, true); ?>
      >
      <?php esc_html_e('Automatically deactivate license when plugin is deactivated or uninstalled', 'submittal-builder'); ?>
    </label>
    <p class="description">
      <?php esc_html_e('When enabled, we\'ll release your license seat on this site automatically when you deactivate or delete the plugin. This frees up an activation for use on another site.', 'submittal-builder'); ?>
    </p>
    <p class="description" style="color:#666; font-size:12px;">
      <?php esc_html_e('Note: Your license key and settings are preserved when you deactivate (only cleared on uninstall if "Remove Data" is enabled below).', 'submittal-builder'); ?>
    </p>
    <?php
  }

  /** Remove data on uninstall field */
  function render_remove_data_field() {
    $value = get_option('sfb_remove_data_on_uninstall', false);
    ?>
    <label>
      <input
        type="checkbox"
        name="sfb_remove_data_on_uninstall"
        value="1"
        <?php checked($value, true); ?>
      >
      <?php esc_html_e('Remove all plugin data when uninstalling', 'submittal-builder'); ?>
    </label>
    <p class="description">
      <?php esc_html_e('When enabled, all plugin settings, license data, and cached information will be permanently deleted when you uninstall (delete) the plugin.', 'submittal-builder'); ?>
    </p>
    <p class="description" style="color:#d63638; font-size:12px;">
      <strong><?php esc_html_e('Warning:', 'submittal-builder'); ?></strong>
      <?php esc_html_e('This action cannot be undone. You will need to re-enter your license key and reconfigure all settings if you reinstall later.', 'submittal-builder'); ?>
    </p>
    <?php
  }

  /** External Links section description */
  function render_external_links_section_desc() {
    ?>
    <p class="description">
      <?php esc_html_e('Configure external URLs used in License & Support pages. Leave blank to use default placeholder URLs or show "Coming soon" state.', 'submittal-builder'); ?>
    </p>
    <p class="description">
      <?php esc_html_e('For Support URL, you can use either a URL (https://...) or email address (mailto:support@example.com).', 'submittal-builder'); ?>
    </p>
    <?php
  }

  /** Render individual link field */
  function render_link_field($args) {
    $key = $args['key'];
    $value = get_option('sfb_link_' . $key, '');
    $placeholder = '';

    // Set helpful placeholders (use defaults from sfb_get_links)
    $defaults = sfb_get_links();
    $placeholder = isset($defaults[$key]) ? $defaults[$key] : '';

    ?>
    <input
      type="text"
      name="sfb_link_<?php echo esc_attr($key); ?>"
      id="sfb_link_<?php echo esc_attr($key); ?>"
      value="<?php echo esc_attr($value); ?>"
      placeholder="<?php echo esc_attr($placeholder); ?>"
      class="regular-text"
      style="width: 100%; max-width: 500px;"
    >
    <?php if ($key === 'support'): ?>
      <span class="sfb-field-desc"><?php esc_html_e('Enter a URL or email address (e.g., mailto:support@example.com)', 'submittal-builder'); ?></span>
    <?php endif; ?>
    <?php
  }

  /** Lead Capture Section Description */
  function render_lead_capture_section_desc() {
    ?>
    <p><?php esc_html_e('Capture user contact information before allowing PDF downloads. Stores leads in the database and sends email notifications.', 'submittal-builder'); ?></p>
    <?php
  }

  /** Lead Capture Enabled Field */
  function render_lead_capture_enabled_field() {
    $enabled = get_option('sfb_lead_capture_enabled', false);
    $checked = !empty($enabled) ? 'checked' : '';
    ?>
    <!-- Hidden field ensures unchecked checkbox updates the option to false -->
    <input type="hidden" name="sfb_lead_capture_enabled" value="0">
    <label>
      <input type="checkbox" name="sfb_lead_capture_enabled" value="1" <?php echo $checked; ?>>
      <?php esc_html_e('Show lead capture modal before PDF generation', 'submittal-builder'); ?>
    </label>
    <span class="sfb-field-desc"><?php esc_html_e('When enabled, users must enter their email (and optionally phone) before downloading PDFs. Leads are stored in the database.', 'submittal-builder'); ?></span>
    <?php
  }

  /** BCC Admin Field */
  function render_lead_bcc_admin_field() {
    $enabled = get_option('sfb_lead_bcc_admin', false);
    $checked = !empty($enabled) ? 'checked' : '';
    ?>
    <label>
      <input type="checkbox" name="sfb_lead_bcc_admin" value="1" <?php echo $checked; ?>>
      <?php esc_html_e('BCC site admin email on lead notification emails', 'submittal-builder'); ?>
    </label>
    <span class="sfb-field-desc"><?php printf(esc_html__('When enabled, all lead notification emails will BCC %s', 'submittal-builder'), get_option('admin_email')); ?></span>
    <?php
  }

  /** Upgrade Page Renderer */
  function render_upgrade_page() {
    include plugin_dir_path(__FILE__) . 'templates/admin/upgrade.php';
  }

  /** License & Support Page Renderer (Pro users) */
  function render_license_support_page() {
    include plugin_dir_path(__FILE__) . 'templates/admin/license-support.php';
  }

  /** License Management Page Renderer (Expired/Invalid licenses) */
  function render_license_management_page() {
    include plugin_dir_path(__FILE__) . 'templates/admin/license-management.php';
  }

  /** Onboarding Page Renderer */
  function render_onboarding_page() {
    // Use constant for reliable path resolution
    $welcome_template = plugin_dir_path(SFB_PLUGIN_FILE) . 'templates/admin/welcome.php';
    $onboarding_template = plugin_dir_path(SFB_PLUGIN_FILE) . 'templates/admin/onboarding.php';

    // Try new welcome template first
    if (file_exists($welcome_template)) {
      include $welcome_template;
    } elseif (file_exists($onboarding_template)) {
      // Fallback to old onboarding template
      include $onboarding_template;
    } else {
      // Error state - neither template found
      echo '<div class="wrap"><div class="notice notice-error"><p>';
      echo esc_html__('Welcome template not found. Please reinstall the plugin.', 'submittal-builder');
      echo '</p></div></div>';
    }
  }

  /** Tools Page Renderer */
  function render_utilities_page() {
    // Get plugin version
    $plugin_version = self::VERSION;

    // Get Pro status
    $pro_active = sfb_is_pro_active();
    $pro_status_label = $pro_active ? __('Active', 'submittal-builder') : __('Free', 'submittal-builder');
    $is_agency = sfb_is_agency_license();

    // Get shareable drafts status
    $shareable_enabled = sfb_feature_enabled('server_drafts');
    $shareable_label = $shareable_enabled ? __('Enabled', 'submittal-builder') : __('Disabled', 'submittal-builder');

    // Get draft statistics
    $draft_stats = $this->get_draft_stats();

    // Get tracking statistics (Pro)
    $tracking_stats = ['total' => 0, 'viewed' => 0];
    if ($pro_active) {
      $packets = get_option('sfb_packets', []);
      $tracking_stats['total'] = count($packets);
      $tracking_stats['viewed'] = count(array_filter($packets, function($p) {
        return !empty($p['views']);
      }));
    }

    // Get cron status
    $next_run = wp_next_scheduled('sfb_purge_expired_drafts');
    if ($next_run) {
      $next_time = wp_date(get_option('time_format'), $next_run);
      $cron_status_html = '🟢 <strong>' . sprintf(__('Next: %s', 'submittal-builder'), $next_time) . '</strong>';
    } else {
      $cron_status_html = '🔴 <strong>' . __('Not scheduled', 'submittal-builder') . '</strong>';
    }

    ?>
    <div class="wrap sfb-utilities">
      <h1><?php echo esc_html__('Utilities', 'submittal-builder'); ?></h1>
      <p class="description"><?php esc_html_e('Maintenance and testing tools. Clean up old data, test email delivery, optimize database, and run system diagnostics.', 'submittal-builder'); ?></p>

      <!-- Draft Management Card -->
      <div class="sfb-card">
        <h2>🧹 <?php echo esc_html__('Draft Management', 'submittal-builder'); ?></h2>
        <p class="sfb-muted"><?php echo esc_html__('Clean up temp drafts and verify the system is healthy.', 'submittal-builder'); ?></p>

        <div class="sfb-actions">
          <button id="sfb-purge-btn"
                  class="button button-primary sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_purge')); ?>">
            <?php esc_html_e('Purge Expired Drafts', 'submittal-builder'); ?>
          </button>

          <button id="sfb-smoke-btn"
                  class="button sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_smoke')); ?>">
            <?php esc_html_e('Run Smoke Test', 'submittal-builder'); ?>
          </button>
        </div>

        <div id="sfb-drafts-status" class="sfb-status">
          <?php echo esc_html__('Idle — ready when you are.', 'submittal-builder'); ?>
        </div>

        <div class="sfb-grid" style="margin-top:10px;">
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Cron Status', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-cron-status">
              <?php echo wp_kses_post($cron_status_html); ?>
            </div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Draft Statistics', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-draft-stats">
              <?php echo esc_html($draft_stats['text']); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- System Information Card -->
      <div class="sfb-card">
        <h2>🧩 <?php echo esc_html__('System Information', 'submittal-builder'); ?></h2>
        <div class="sfb-grid">
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Plugin Version', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($plugin_version); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Pro Status', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($pro_status_label); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Shareable Drafts', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($shareable_label); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('WordPress', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html(get_bloginfo('version')); ?></div>
          </div>
        </div>
      </div>

      <!-- Email Testing Card -->
      <div class="sfb-card">
        <h2>📧 <?php echo esc_html__('Email Testing', 'submittal-builder'); ?></h2>
        <p class="sfb-muted"><?php echo esc_html__('Test email delivery to verify SMTP configuration is working correctly.', 'submittal-builder'); ?></p>

        <div class="sfb-actions">
          <input type="email"
                 id="sfb-test-email-input"
                 placeholder="<?php esc_attr_e('recipient@example.com', 'submittal-builder'); ?>"
                 style="width: 300px; margin-right: 10px;"
                 value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" />

          <button id="sfb-test-email-btn"
                  class="button button-primary sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_test_email')); ?>">
            <?php esc_html_e('Send Test Email', 'submittal-builder'); ?>
          </button>
        </div>

        <div id="sfb-email-status" class="sfb-status">
          <?php echo esc_html__('Enter recipient email and click Send Test Email.', 'submittal-builder'); ?>
        </div>
      </div>

      <?php if ($pro_active): ?>
      <!-- Tracking Data Management (Pro) -->
      <div class="sfb-card">
        <h2>📊 <?php echo esc_html__('Tracking Data Management', 'submittal-builder'); ?> <span class="sfb-pro-badge">PRO</span></h2>
        <p class="sfb-muted"><?php echo esc_html__('Clear old tracking links and view statistics. This does not delete PDFs, only tracking data.', 'submittal-builder'); ?></p>

        <div class="sfb-grid" style="margin-bottom:16px;">
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Total Tracking Links', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-tracking-total">
              <?php echo esc_html($tracking_stats['total']); ?>
            </div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Links with Views', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-tracking-viewed">
              <?php echo esc_html($tracking_stats['viewed']); ?>
            </div>
          </div>
        </div>

        <div class="sfb-actions">
          <button id="sfb-clear-tracking-btn"
                  class="button button-secondary sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_clear_tracking')); ?>">
            <?php esc_html_e('Clear All Tracking Data', 'submittal-builder'); ?>
          </button>
        </div>

        <div id="sfb-tracking-status" class="sfb-status">
          <?php echo esc_html__('Tracking data helps monitor customer engagement.', 'submittal-builder'); ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Database Cleanup Card -->
      <div class="sfb-card">
        <h2>🗄️ <?php echo esc_html__('Database Cleanup', 'submittal-builder'); ?></h2>
        <p class="sfb-muted"><?php echo esc_html__('Optimize database tables and remove orphaned data. Safe to run anytime.', 'submittal-builder'); ?></p>

        <div class="sfb-actions">
          <button id="sfb-optimize-db-btn"
                  class="button sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_optimize_db')); ?>">
            <?php esc_html_e('Optimize Database', 'submittal-builder'); ?>
          </button>

          <button id="sfb-clean-orphans-btn"
                  class="button sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_clean_orphans')); ?>">
            <?php esc_html_e('Remove Orphaned Data', 'submittal-builder'); ?>
          </button>
        </div>

        <div id="sfb-db-status" class="sfb-status">
          <?php echo esc_html__('Database is healthy.', 'submittal-builder'); ?>
        </div>
      </div>

      <?php if (false): ?> <!-- Disabled for production -->
      <!-- Advanced Tools Link (Dev Mode Only) -->
      <div style="margin-top: 20px; padding: 10px; background: #f0f0f1; border-radius: 4px; text-align: center;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-demo-tools')); ?>" style="font-size: 12px; color: #646970;">
          <?php echo esc_html__('Advanced', 'submittal-builder'); ?> →
        </a>
      </div>
      <?php endif; ?>

      <style>
        /* Hide WordPress admin footer elements */
        .sfb-agency-wrap ~ #wpfooter,
        #footer-thankyou,
        #footer-upgrade {
          display: none !important;
        }
      </style>

      <?php $this->render_feedback_footer(); ?>
    </div>
    <?php
  }

  /** Agency Analytics Page Renderer (Agency) */
  function render_agency_analytics_page() {
    // Security check
    if (!sfb_is_agency_license()) {
      wp_die(__('This feature requires an Agency license.', 'submittal-builder'));
    }

    // Get filter (default to 30 days)
    $days = isset($_GET['days']) ? intval($_GET['days']) : 30;
    if (!in_array($days, [7, 30, 90], true)) {
      $days = 30;
    }

    // Get analytics data
    $analytics = SFB_Agency_Analytics::get_analytics($days);

    ?>
    <div class="wrap sfb-analytics-wrap">
      <h1 class="wp-heading-inline">
        📊 <?php esc_html_e('Agency Analytics', 'submittal-builder'); ?>
        <span class="sfb-agency-badge">AGENCY</span>
      </h1>

      <p class="description" style="margin-top:12px;margin-bottom:24px;">
        <?php esc_html_e('Monitor client activity across your sites. Track PDF generation, lead captures, and popular products to optimize your catalog.', 'submittal-builder'); ?>
      </p>

      <!-- Date Range Filter -->
      <div style="margin-bottom:24px;">
        <label for="sfb-analytics-days" style="margin-right:8px;font-weight:600;">
          <?php esc_html_e('Date Range:', 'submittal-builder'); ?>
        </label>
        <select id="sfb-analytics-days" onchange="window.location.href='<?php echo esc_js(admin_url('admin.php?page=sfb-agency-analytics&days=')); ?>' + this.value;">
          <option value="7" <?php selected($days, 7); ?>><?php esc_html_e('Last 7 days', 'submittal-builder'); ?></option>
          <option value="30" <?php selected($days, 30); ?>><?php esc_html_e('Last 30 days', 'submittal-builder'); ?></option>
          <option value="90" <?php selected($days, 90); ?>><?php esc_html_e('Last 90 days', 'submittal-builder'); ?></option>
        </select>
      </div>

      <!-- Stats Cards -->
      <div class="sfb-stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:32px;">
        <!-- PDFs Generated -->
        <div class="sfb-card" style="text-align:center;padding:24px;border:2px solid #e0e7ff;background:linear-gradient(to bottom,#f5f7ff,#fff);">
          <div style="font-size:48px;font-weight:700;color:#4f46e5;margin-bottom:8px;">
            <?php echo esc_html(number_format($analytics['pdf_count'])); ?>
          </div>
          <div style="font-size:14px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">
            <?php esc_html_e('PDFs Generated', 'submittal-builder'); ?>
          </div>
        </div>

        <!-- Leads Captured -->
        <div class="sfb-card" style="text-align:center;padding:24px;border:2px solid #dcfce7;background:linear-gradient(to bottom,#f0fdf4,#fff);">
          <div style="font-size:48px;font-weight:700;color:#16a34a;margin-bottom:8px;">
            <?php echo esc_html(number_format($analytics['lead_count'])); ?>
          </div>
          <div style="font-size:14px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">
            <?php esc_html_e('Leads Captured', 'submittal-builder'); ?>
          </div>
        </div>

        <!-- Last Heartbeat -->
        <div class="sfb-card" style="text-align:center;padding:24px;">
          <div style="font-size:16px;font-weight:600;color:#374151;margin-bottom:8px;">
            <?php if ($analytics['last_heartbeat']): ?>
              <?php echo esc_html(human_time_diff(strtotime($analytics['last_heartbeat']), current_time('timestamp')) . ' ago'); ?>
            <?php else: ?>
              <?php esc_html_e('Never', 'submittal-builder'); ?>
            <?php endif; ?>
          </div>
          <div style="font-size:14px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">
            <?php esc_html_e('Last Heartbeat', 'submittal-builder'); ?>
          </div>
        </div>
      </div>

      <!-- Current Site Info -->
      <div class="sfb-card" style="margin-bottom:32px;">
        <h2><?php esc_html_e('📍 Current Site', 'submittal-builder'); ?></h2>
        <table class="widefat" style="margin-top:16px;">
          <tbody>
            <tr>
              <td style="width:200px;font-weight:600;"><?php esc_html_e('Site URL:', 'submittal-builder'); ?></td>
              <td><?php echo esc_html($analytics['site_url']); ?></td>
            </tr>
            <tr>
              <td style="font-weight:600;"><?php esc_html_e('Plugin Version:', 'submittal-builder'); ?></td>
              <td><?php echo esc_html($analytics['version']); ?></td>
            </tr>
            <tr>
              <td style="font-weight:600;"><?php esc_html_e('Site ID:', 'submittal-builder'); ?></td>
              <td><code><?php echo esc_html(substr($analytics['site_id'], 0, 16) . '...'); ?></code></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Top Products -->
      <?php if (!empty($analytics['top_products'])): ?>
      <div class="sfb-card">
        <h2><?php esc_html_e('🏆 Top 5 Products', 'submittal-builder'); ?></h2>
        <p class="sfb-muted">
          <?php echo esc_html(sprintf(__('Most frequently selected products in the last %d days', 'submittal-builder'), $days)); ?>
        </p>

        <div style="margin-top:24px;display:flex;flex-wrap:wrap;gap:12px;">
          <?php foreach ($analytics['top_products'] as $product): ?>
            <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:#f3f4f6;border:1px solid #d1d5db;border-radius:20px;">
              <span style="font-weight:600;color:#374151;">
                <?php echo esc_html($product['title']); ?>
              </span>
              <span style="display:inline-block;padding:2px 8px;background:#4f46e5;color:#fff;border-radius:12px;font-size:11px;font-weight:700;">
                <?php echo esc_html(number_format($product['count'])); ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php else: ?>
      <div class="sfb-card">
        <h2><?php esc_html_e('🏆 Top 5 Products', 'submittal-builder'); ?></h2>
        <div style="padding:48px;text-align:center;background:#f9fafb;border-radius:8px;margin-top:24px;">
          <div style="font-size:48px;margin-bottom:16px;opacity:0.3;">📦</div>
          <p style="color:#6b7280;margin:0;">
            <?php esc_html_e('No product data yet. Products will appear here once PDFs are generated.', 'submittal-builder'); ?>
          </p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Info Box -->
      <div style="margin-top:32px;padding:16px;background:#fef3c7;border-left:4px solid #fbbf24;border-radius:8px;">
        <p style="margin:0;font-size:13px;color:#78350f;">
          <strong><?php esc_html_e('ℹ️ Privacy Notice:', 'submittal-builder'); ?></strong>
          <?php esc_html_e('Analytics track counts only - no personally identifiable information (PII) from leads is stored or transmitted. Product names and counts are aggregated for insights.', 'submittal-builder'); ?>
        </p>
      </div>
    </div>

    <style>
      .sfb-analytics-wrap {
        max-width: 1200px;
      }
      .sfb-analytics-wrap .sfb-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 24px;
      }
      .sfb-analytics-wrap .sfb-card h2 {
        margin: 0 0 12px 0;
        font-size: 20px;
        font-weight: 600;
        color: #111827;
      }
      .sfb-analytics-wrap .sfb-muted {
        color: #6b7280;
        margin: 0;
        font-size: 14px;
      }
      .sfb-agency-badge {
        display: inline-block;
        background: #7c3aed;
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        margin-left: 8px;
      }

      /* Hide WordPress admin footer elements */
      .sfb-analytics-wrap ~ #wpfooter,
      #footer-thankyou,
      #footer-upgrade {
        display: none !important;
      }
    </style>
    <?php
    $this->render_feedback_footer();
    ?>
    </div><!-- .wrap -->
    <?php
  }

  /** Tracking Page Renderer (Pro) */
  function render_tracking_page() {
    // Get all tracking links
    $all_packets = get_option('sfb_packets', []);

    // Calculate comprehensive stats
    $total_packets = count($all_packets);
    $total_views = 0;
    $packets_with_views = 0;
    $unique_recipients = [];
    $recent_views = [];

    foreach ($all_packets as $token => $packet) {
      $view_count = $packet['view_count'] ?? 0;
      $total_views += $view_count;
      if ($view_count > 0) {
        $packets_with_views++;
      }

      // Track unique recipients
      $email = $packet['email_to'] ?? '';
      if ($email && !in_array($email, $unique_recipients)) {
        $unique_recipients[] = $email;
      }

      // Collect recent views
      if (!empty($packet['views']) && is_array($packet['views'])) {
        foreach ($packet['views'] as $view) {
          $recent_views[] = [
            'timestamp' => $view['timestamp'] ?? '',
            'project' => $packet['project'] ?? __('(No Project Name)', 'submittal-builder'),
            'token' => $token
          ];
        }
      }
    }

    // Sort recent views by timestamp (newest first)
    usort($recent_views, function($a, $b) {
      return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    $recent_views = array_slice($recent_views, 0, 5); // Top 5 most recent

    // Calculate engagement rate
    $engagement_rate = $total_packets > 0 ? ($packets_with_views / $total_packets) * 100 : 0;

    // Sort packets by creation date (newest first)
    uasort($all_packets, function($a, $b) {
      return strtotime($b['created'] ?? '') - strtotime($a['created'] ?? '');
    });

    ?>
    <div class="wrap sfb-tracking">
      <style>
        .sfb-tracking .sfb-stat-card {
          background: #fff;
          border: 1px solid #e5e5e5;
          border-radius: 8px;
          padding: 20px;
          box-shadow: 0 1px 3px rgba(0,0,0,0.04);
          transition: all 0.2s ease;
        }
        .sfb-tracking .sfb-stat-card:hover {
          box-shadow: 0 4px 12px rgba(0,0,0,0.08);
          transform: translateY(-2px);
        }
        .sfb-tracking .sfb-stat-number {
          font-size: 32px;
          font-weight: 700;
          color: #2271b1;
          line-height: 1;
          margin: 10px 0 5px 0;
        }
        .sfb-tracking .sfb-stat-label {
          font-size: 13px;
          color: #646970;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          font-weight: 600;
        }
        .sfb-tracking .sfb-stat-icon {
          font-size: 24px;
          margin-bottom: 5px;
        }
        .sfb-tracking .sfb-stat-sublabel {
          font-size: 12px;
          color: #999;
          margin-top: 5px;
        }
        .sfb-tracking .sfb-activity-item {
          padding: 12px 0;
          border-bottom: 1px solid #f0f0f1;
          display: flex;
          align-items: center;
          gap: 12px;
        }
        .sfb-tracking .sfb-activity-item:last-child {
          border-bottom: none;
        }
        .sfb-tracking .sfb-activity-time {
          font-size: 11px;
          color: #999;
          min-width: 140px;
        }
        .sfb-tracking .sfb-activity-project {
          font-weight: 500;
          color: #2271b1;
        }
        .sfb-tracking .sfb-engagement-bar {
          height: 8px;
          background: #f0f0f1;
          border-radius: 4px;
          overflow: hidden;
          margin-top: 8px;
        }
        .sfb-tracking .sfb-engagement-fill {
          height: 100%;
          background: linear-gradient(90deg, #2271b1, #72aee6);
          transition: width 0.6s ease;
        }
        .sfb-tracking .sfb-btn-copy {
          padding: 4px 12px;
          font-size: 12px;
          border-radius: 4px;
          background: #f0f0f1;
          border: 1px solid #c3c4c7;
          cursor: pointer;
          transition: all 0.2s;
        }
        .sfb-tracking .sfb-btn-copy:hover {
          background: #2271b1;
          color: white;
          border-color: #2271b1;
        }
        .sfb-tracking .sfb-btn-copy.copied {
          background: #00a32a;
          color: white;
          border-color: #00a32a;
        }
        .sfb-tracking .sfb-view-badge {
          display: inline-flex;
          align-items: center;
          gap: 5px;
          padding: 5px 10px;
          border-radius: 12px;
          font-size: 13px;
          font-weight: 600;
        }
        .sfb-tracking .sfb-view-badge.high {
          background: #d4edda;
          color: #155724;
        }
        .sfb-tracking .sfb-view-badge.medium {
          background: #fff3cd;
          color: #856404;
        }
        .sfb-tracking .sfb-view-badge.low {
          background: #f8f9fa;
          color: #666;
        }
        .sfb-tracking .sfb-empty-state {
          text-align: center;
          padding: 60px 20px;
          background: #f9f9f9;
          border-radius: 8px;
          border: 2px dashed #ddd;
        }
        .sfb-tracking .sfb-empty-icon {
          font-size: 48px;
          margin-bottom: 15px;
          opacity: 0.5;
        }
      </style>

      <h1><?php echo esc_html__('Tracking', 'submittal-builder'); ?></h1>
      <p class="description">
        <?php echo esc_html__('Monitor customer engagement. See when recipients view your submittal packets and track PDF download activity.', 'submittal-builder'); ?>
      </p>

      <!-- Enhanced Summary Stats -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 25px;">
        <div class="sfb-stat-card">
          <div class="sfb-stat-icon">🔗</div>
          <div class="sfb-stat-label"><?php esc_html_e('Total Links', 'submittal-builder'); ?></div>
          <div class="sfb-stat-number"><?php echo esc_html(number_format($total_packets)); ?></div>
          <div class="sfb-stat-sublabel">
            <?php printf(esc_html__('%d active tracking links', 'submittal-builder'), $total_packets); ?>
          </div>
        </div>

        <div class="sfb-stat-card">
          <div class="sfb-stat-icon">👁️</div>
          <div class="sfb-stat-label"><?php esc_html_e('Total Views', 'submittal-builder'); ?></div>
          <div class="sfb-stat-number"><?php echo esc_html(number_format($total_views)); ?></div>
          <div class="sfb-stat-sublabel">
            <?php printf(esc_html__('%s avg per link', 'submittal-builder'), $total_packets > 0 ? number_format($total_views / $total_packets, 1) : '0'); ?>
          </div>
        </div>

        <div class="sfb-stat-card">
          <div class="sfb-stat-icon">✅</div>
          <div class="sfb-stat-label"><?php esc_html_e('Engagement Rate', 'submittal-builder'); ?></div>
          <div class="sfb-stat-number"><?php echo esc_html(number_format($engagement_rate, 0)); ?>%</div>
          <div class="sfb-engagement-bar">
            <div class="sfb-engagement-fill" style="width: <?php echo esc_attr($engagement_rate); ?>%;"></div>
          </div>
          <div class="sfb-stat-sublabel">
            <?php printf(esc_html__('%d of %d links viewed', 'submittal-builder'), $packets_with_views, $total_packets); ?>
          </div>
        </div>

        <div class="sfb-stat-card">
          <div class="sfb-stat-icon">📧</div>
          <div class="sfb-stat-label"><?php esc_html_e('Recipients', 'submittal-builder'); ?></div>
          <div class="sfb-stat-number"><?php echo esc_html(number_format(count($unique_recipients))); ?></div>
          <div class="sfb-stat-sublabel">
            <?php esc_html_e('unique email addresses', 'submittal-builder'); ?>
          </div>
        </div>
      </div>

      <?php if (!empty($recent_views)): ?>
      <!-- Recent Activity -->
      <div class="sfb-card" style="margin-top: 30px;">
        <h2>⚡ <?php echo esc_html__('Recent Activity', 'submittal-builder'); ?></h2>
        <div style="margin-top: 15px;">
          <?php foreach ($recent_views as $view): ?>
            <div class="sfb-activity-item">
              <span class="sfb-activity-time"><?php echo esc_html(human_time_diff(strtotime($view['timestamp']), current_time('timestamp')) . ' ago'); ?></span>
              <span style="color: #999;">•</span>
              <span class="sfb-activity-project"><?php echo esc_html($view['project']); ?></span>
              <span style="color: #999;">viewed</span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Tracking Links Table -->
      <div class="sfb-card" style="margin-top: 30px;">
        <h2>🔗 <?php echo esc_html__('All Tracking Links', 'submittal-builder'); ?></h2>

        <?php if (empty($all_packets)): ?>
          <div class="sfb-empty-state">
            <div class="sfb-empty-icon">📭</div>
            <h3><?php esc_html_e('No Tracking Links Yet', 'submittal-builder'); ?></h3>
            <p style="color: #666; max-width: 500px; margin: 10px auto;">
              <?php esc_html_e('Tracking links are automatically created when you use Auto-Email to send PDFs. Start sending PDFs to see engagement analytics here.', 'submittal-builder'); ?>
            </p>
          </div>
        <?php else: ?>
          <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
            <thead>
              <tr>
                <th style="width: 20%;"><?php esc_html_e('Project', 'submittal-builder'); ?></th>
                <th style="width: 18%;"><?php esc_html_e('Recipient', 'submittal-builder'); ?></th>
                <th style="width: 12%;"><?php esc_html_e('Created', 'submittal-builder'); ?></th>
                <th style="width: 10%;"><?php esc_html_e('Engagement', 'submittal-builder'); ?></th>
                <th style="width: 12%;"><?php esc_html_e('Last Viewed', 'submittal-builder'); ?></th>
                <th style="width: 28%;"><?php esc_html_e('Tracking Link', 'submittal-builder'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_packets as $token => $packet): ?>
                <?php
                $project = $packet['project'] ?? __('(No Project Name)', 'submittal-builder');
                $email_to = $packet['email_to'] ?? __('(Unknown)', 'submittal-builder');
                $created = $packet['created'] ?? '';
                $view_count = $packet['view_count'] ?? 0;
                $last_viewed = $packet['last_viewed'] ?? null;
                $tracking_url = add_query_arg(['sfb_view' => $token], home_url('/'));

                // Format dates
                $created_formatted = $created ? wp_date('M j, Y', strtotime($created)) : '—';
                $last_viewed_formatted = $last_viewed ? human_time_diff(strtotime($last_viewed), current_time('timestamp')) . ' ago' : '—';

                // Determine badge style
                $badge_class = $view_count >= 5 ? 'high' : ($view_count >= 1 ? 'medium' : 'low');
                $badge_icon = $view_count >= 5 ? '🔥' : ($view_count >= 1 ? '✓' : '○');
                ?>
                <tr>
                  <td><strong><?php echo esc_html($project); ?></strong></td>
                  <td style="font-size: 13px;"><?php echo esc_html($email_to); ?></td>
                  <td style="font-size: 13px; color: #666;"><?php echo esc_html($created_formatted); ?></td>
                  <td>
                    <span class="sfb-view-badge <?php echo esc_attr($badge_class); ?>">
                      <?php echo $badge_icon; ?> <?php echo esc_html($view_count); ?>
                    </span>
                  </td>
                  <td style="font-size: 13px; color: #666;"><?php echo esc_html($last_viewed_formatted); ?></td>
                  <td>
                    <div style="display: flex; gap: 8px; align-items: center;">
                      <input type="text" readonly value="<?php echo esc_attr($tracking_url); ?>"
                             class="sfb-tracking-url"
                             style="flex: 1; font-size: 11px; font-family: monospace; padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;"
                             onclick="this.select();">
                      <button type="button" class="sfb-btn-copy" data-url="<?php echo esc_attr($tracking_url); ?>">
                        <?php esc_html_e('Copy', 'submittal-builder'); ?>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
      // Copy to clipboard functionality
      $('.sfb-btn-copy').on('click', function() {
        var btn = $(this);
        var url = btn.data('url');

        // Create temporary input
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(url).select();
        document.execCommand('copy');
        temp.remove();

        // Visual feedback
        var originalText = btn.text();
        btn.addClass('copied').text('<?php esc_html_e('Copied!', 'submittal-builder'); ?>');

        setTimeout(function() {
          btn.removeClass('copied').text(originalText);
        }, 2000);
      });
    });
    </script>
    <?php
    $this->render_feedback_footer();
    ?>
    </div><!-- .wrap -->
    <?php
  }

  /** Leads Page Renderer */
  function render_leads_page() {
    // Get filter parameters
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 25;
    $offset = ($paged - 1) * $per_page;

    // Get filtered leads
    $leads_data = $this->get_filtered_leads($search, $date_from, $date_to, $per_page, $offset);
    $leads = $leads_data['leads'];
    $total_leads = $leads_data['total'];
    $total_pages = ceil($total_leads / $per_page);

    // Build export URL with current filters
    $export_url = add_query_arg([
      'page' => 'sfb-leads',
      'action' => 'export_csv',
      'nonce' => wp_create_nonce('sfb_export_leads'),
      's' => $search,
      'date_from' => $date_from,
      'date_to' => $date_to,
    ], admin_url('admin.php'));

    ?>
    <div class="wrap sfb-leads-wrap">
      <h1><?php esc_html_e('Leads', 'submittal-builder'); ?></h1>
      <p class="description" style="margin-top:-8px;margin-bottom:24px;">
        <?php esc_html_e('View and export captured lead information from your submittal forms. Track contact details, project names, and download activity.', 'submittal-builder'); ?>
      </p>

      <!-- Stats Summary -->
      <div class="sfb-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 20px 0;">
        <div class="sfb-stat-card" style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
          <div style="font-size: 32px; font-weight: 700; color: #2271b1;"><?php echo esc_html($total_leads); ?></div>
          <div style="color: #666; font-size: 13px; margin-top: 4px;"><?php esc_html_e('Total Leads', 'submittal-builder'); ?></div>
        </div>
      </div>

      <!-- Filters -->
      <form method="get" class="sfb-leads-filters" style="background: white; padding: 16px; border: 1px solid #ddd; border-radius: 8px; margin: 20px 0;">
        <input type="hidden" name="page" value="sfb-leads">

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
          <div>
            <label for="sfb-search" style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px;">
              <?php esc_html_e('Search', 'submittal-builder'); ?>
            </label>
            <input
              type="text"
              id="sfb-search"
              name="s"
              value="<?php echo esc_attr($search); ?>"
              placeholder="<?php esc_attr_e('Email, Project, UTM...', 'submittal-builder'); ?>"
              style="width: 100%;">
          </div>

          <div>
            <label for="date-from" style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px;">
              <?php esc_html_e('From Date', 'submittal-builder'); ?>
            </label>
            <input type="date" id="date-from" name="date_from" value="<?php echo esc_attr($date_from); ?>" style="width: 100%;">
          </div>

          <div>
            <label for="date-to" style="display: block; margin-bottom: 4px; font-weight: 600; font-size: 13px;">
              <?php esc_html_e('To Date', 'submittal-builder'); ?>
            </label>
            <input type="date" id="date-to" name="date_to" value="<?php echo esc_attr($date_to); ?>" style="width: 100%;">
          </div>

          <div style="display: flex; gap: 8px;">
            <button type="submit" class="button button-primary"><?php esc_html_e('Filter', 'submittal-builder'); ?></button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-leads')); ?>" class="button"><?php esc_html_e('Reset', 'submittal-builder'); ?></a>
          </div>
        </div>
      </form>

      <!-- Export Button -->
      <div style="margin: 16px 0;">
        <a href="<?php echo esc_url($export_url); ?>" class="button">
          <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
          <?php esc_html_e('Export to CSV', 'submittal-builder'); ?>
        </a>
      </div>

      <!-- Leads Table -->
      <?php if (empty($leads)): ?>
        <div class="sfb-empty-state" style="background: white; padding: 60px 20px; text-align: center; border: 1px solid #ddd; border-radius: 8px;">
          <p style="font-size: 18px; color: #666; margin: 0;">
            <?php echo $search || $date_from || $date_to
              ? esc_html__('No leads found matching your filters.', 'submittal-builder')
              : esc_html__('No leads captured yet.', 'submittal-builder'); ?>
          </p>
        </div>
      <?php else: ?>
        <table class="wp-list-table widefat fixed striped" style="margin-top: 16px;">
          <thead>
            <tr>
              <th style="width: 140px;"><?php esc_html_e('Date', 'submittal-builder'); ?></th>
              <th><?php esc_html_e('Email', 'submittal-builder'); ?></th>
              <th><?php esc_html_e('Phone', 'submittal-builder'); ?></th>
              <th><?php esc_html_e('Project', 'submittal-builder'); ?></th>
              <th style="width: 80px; text-align: center;"><?php esc_html_e('Items', 'submittal-builder'); ?></th>
              <th><?php esc_html_e('Top Category', 'submittal-builder'); ?></th>
              <th style="width: 80px; text-align: center;"><?php esc_html_e('Consent', 'submittal-builder'); ?></th>
              <th><?php esc_html_e('UTM', 'submittal-builder'); ?></th>
              <th style="width: 100px;"><?php esc_html_e('Actions', 'submittal-builder'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($leads as $lead):
              $utm_data = json_decode($lead['utm_json'], true);
              $utm_display = [];
              if (!empty($utm_data['source'])) $utm_display[] = 'src:' . $utm_data['source'];
              if (!empty($utm_data['medium'])) $utm_display[] = 'med:' . $utm_data['medium'];
              if (!empty($utm_data['campaign'])) $utm_display[] = 'camp:' . $utm_data['campaign'];
              $utm_str = !empty($utm_display) ? implode(' / ', $utm_display) : '—';
            ?>
              <tr>
                <td><?php echo esc_html(date('M j, Y g:ia', strtotime($lead['created_at']))); ?></td>
                <td><strong><?php echo esc_html($lead['email']); ?></strong></td>
                <td><?php echo esc_html($lead['phone'] ?: '—'); ?></td>
                <td><?php echo esc_html($lead['project_name'] ?: '—'); ?></td>
                <td style="text-align: center;"><?php echo esc_html($lead['num_items']); ?></td>
                <td><?php echo esc_html($lead['top_category'] ?: '—'); ?></td>
                <td style="text-align: center;">
                  <?php echo $lead['consent'] ? '<span style="color: #46b450;">✓</span>' : '—'; ?>
                </td>
                <td style="font-size: 11px; color: #666;"><?php echo esc_html($utm_str); ?></td>
                <td>
                  <button type="button" class="button button-small sfb-view-details" data-lead-id="<?php echo esc_attr($lead['id']); ?>">
                    <?php esc_html_e('Details', 'submittal-builder'); ?>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
          <div class="tablenav" style="margin-top: 16px;">
            <div class="tablenav-pages">
              <span class="displaying-num"><?php printf(_n('%s lead', '%s leads', $total_leads, 'submittal-builder'), number_format_i18n($total_leads)); ?></span>
              <?php
              $page_links = paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $total_pages,
                'current' => $paged,
              ]);
              if ($page_links) {
                echo '<span class="pagination-links">' . $page_links . '</span>';
              }
              ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- Details Modal -->
    <div id="sfb-lead-details-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 100000; align-items: center; justify-content: center;">
      <div style="background: white; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
          <h2 style="margin: 0;"><?php esc_html_e('Lead Details', 'submittal-builder'); ?></h2>
          <button type="button" class="sfb-close-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        <div id="sfb-lead-details-content" style="padding: 20px;">
          <!-- Content loaded via JS -->
        </div>
      </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
      // View details
      $('.sfb-view-details').on('click', function() {
        var leadId = $(this).data('lead-id');
        var lead = <?php echo wp_json_encode($leads); ?>[leadId - 1]; // Simple lookup

        // Find the lead by ID
        var leadData = null;
        <?php foreach ($leads as $lead): ?>
          if (<?php echo (int)$lead['id']; ?> === leadId) {
            leadData = <?php echo wp_json_encode($lead); ?>;
          }
        <?php endforeach; ?>

        if (!leadData) return;

        var utm = JSON.parse(leadData.utm_json || '{}');
        var ipHash = leadData.ip_hash ? leadData.ip_hash.substring(0, 8) + '...' : '—';

        var html = '<table class="form-table">';
        html += '<tr><th><?php esc_html_e('Date', 'submittal-builder'); ?></th><td>' + new Date(leadData.created_at).toLocaleString() + '</td></tr>';
        html += '<tr><th><?php esc_html_e('Email', 'submittal-builder'); ?></th><td><strong>' + leadData.email + '</strong></td></tr>';
        html += '<tr><th><?php esc_html_e('Phone', 'submittal-builder'); ?></th><td>' + (leadData.phone || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('Project Name', 'submittal-builder'); ?></th><td>' + (leadData.project_name || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('Items', 'submittal-builder'); ?></th><td>' + leadData.num_items + '</td></tr>';
        html += '<tr><th><?php esc_html_e('Top Category', 'submittal-builder'); ?></th><td>' + (leadData.top_category || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('Consent', 'submittal-builder'); ?></th><td>' + (leadData.consent ? '<?php esc_html_e('Yes', 'submittal-builder'); ?>' : '<?php esc_html_e('No', 'submittal-builder'); ?>') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('UTM Source', 'submittal-builder'); ?></th><td>' + (utm.source || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('UTM Medium', 'submittal-builder'); ?></th><td>' + (utm.medium || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('UTM Campaign', 'submittal-builder'); ?></th><td>' + (utm.campaign || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('UTM Term', 'submittal-builder'); ?></th><td>' + (utm.term || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('UTM Content', 'submittal-builder'); ?></th><td>' + (utm.content || '—') + '</td></tr>';
        html += '<tr><th><?php esc_html_e('IP Hash (first 8)', 'submittal-builder'); ?></th><td><code>' + ipHash + '</code></td></tr>';
        html += '</table>';

        $('#sfb-lead-details-content').html(html);
        $('#sfb-lead-details-modal').css('display', 'flex');
      });

      // Close modal
      $('.sfb-close-modal, #sfb-lead-details-modal').on('click', function(e) {
        if (e.target === this) {
          $('#sfb-lead-details-modal').hide();
        }
      });
    });
    </script>

    <style>
    .sfb-stats-grid .sfb-stat-card {
      transition: transform 0.2s;
    }
    .sfb-stats-grid .sfb-stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    </style>
    <?php
    $this->render_feedback_footer();
    ?>
    </div><!-- .wrap -->
    <?php
  }

  /** Get filtered leads with search and date range */
  private function get_filtered_leads($search = '', $date_from = '', $date_to = '', $limit = 25, $offset = 0) {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_leads';

    // Build WHERE clause
    $where = ['1=1'];
    $params = [];

    // Search filter
    if (!empty($search)) {
      $where[] = '(email LIKE %s OR project_name LIKE %s OR utm_json LIKE %s)';
      $search_term = '%' . $wpdb->esc_like($search) . '%';
      $params[] = $search_term;
      $params[] = $search_term;
      $params[] = $search_term;
    }

    // Date range filters
    if (!empty($date_from)) {
      $where[] = 'created_at >= %s';
      $params[] = $date_from . ' 00:00:00';
    }
    if (!empty($date_to)) {
      $where[] = 'created_at <= %s';
      $params[] = $date_to . ' 23:59:59';
    }

    $where_sql = implode(' AND ', $where);

    // Get total count
    $count_sql = "SELECT COUNT(*) FROM $table WHERE $where_sql";
    if (!empty($params)) {
      $count_sql = $wpdb->prepare($count_sql, $params);
    }
    $total = (int) $wpdb->get_var($count_sql);

    // Get paginated results
    $leads_sql = "SELECT * FROM $table WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $leads_params = array_merge($params, [$limit, $offset]);
    $leads = $wpdb->get_results($wpdb->prepare($leads_sql, $leads_params), ARRAY_A);

    return [
      'leads' => $leads ?: [],
      'total' => $total,
    ];
  }

  /** Demo Tools Page Renderer */
  function render_demo_tools_page() {
    // Get plugin version
    $plugin_data = get_file_data(__FILE__, ['Version' => 'Version']);
    $plugin_version = $plugin_data['Version'] ?? '1.0.0';

    // Get Pro status
    $pro_active = sfb_is_pro_active();
    $pro_status_label = $pro_active ? __('Active', 'submittal-builder') : __('Free', 'submittal-builder');

    // Get shareable drafts status
    $shareable_enabled = sfb_feature_enabled('server_drafts');
    $shareable_label = $shareable_enabled ? __('Enabled', 'submittal-builder') : __('Disabled', 'submittal-builder');

    // Get draft statistics
    $draft_stats = $this->get_draft_stats();

    // Get cron status
    $next_run = wp_next_scheduled('sfb_purge_expired_drafts');
    if ($next_run) {
      $next_time = wp_date(get_option('time_format'), $next_run);
      $cron_status_html = '🟢 <strong>' . sprintf(__('Next: %s', 'submittal-builder'), $next_time) . '</strong>';
    } else {
      $cron_status_html = '🔴 <strong>' . __('Not scheduled', 'submittal-builder') . '</strong>';
    }

    // Handle actions
    $result = null;
    $action = '';
    $license_test_result = null;

    // License State Tester Handler
    if (isset($_POST['sfb_test_license_state']) && check_admin_referer('sfb_test_license_state')) {
      $state = sanitize_text_field($_POST['license_state'] ?? '');

      switch ($state) {
        case 'free':
          // Completely remove license - set to empty array then delete
          update_option('sfb_license', [], false);
          delete_option('sfb_license');
          wp_cache_delete('sfb_license', 'options');
          $license_test_result = __('License set to Free (empty). Menu will show "⭐ Upgrade".', 'submittal-builder');

          // Check for dev constant overrides
          $dev_warnings = [];
          if (defined('SFB_PRO_DEV') && SFB_PRO_DEV) {
            $dev_warnings[] = 'SFB_PRO_DEV';
          }
          if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV) {
            $dev_warnings[] = 'SFB_AGENCY_DEV';
          }
          if (!empty($dev_warnings)) {
            $license_test_result .= ' ' . sprintf(__('WARNING: %s constant(s) active - overriding license checks!', 'submittal-builder'), implode(', ', $dev_warnings));
          }
          break;

        case 'expired':
          update_option('sfb_license', [
            'key' => 'DEMO-EXPIRED-KEY-' . substr(md5(time()), 0, 8),
            'email' => 'demo@example.com',
            'status' => 'expired'
          ], false);
          wp_cache_delete('sfb_license', 'options');
          $license_test_result = __('License set to Expired. Menu will show "Manage License".', 'submittal-builder');

          // Check for dev constant overrides
          $dev_warnings = [];
          if (defined('SFB_PRO_DEV') && SFB_PRO_DEV) {
            $dev_warnings[] = 'SFB_PRO_DEV';
          }
          if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV) {
            $dev_warnings[] = 'SFB_AGENCY_DEV';
          }
          if (!empty($dev_warnings)) {
            $license_test_result .= ' ' . sprintf(__('WARNING: %s constant(s) active - may override this state!', 'submittal-builder'), implode(', ', $dev_warnings));
          }
          break;

        case 'active':
          update_option('sfb_license', [
            'key' => 'DEMO-PRO-KEY-' . substr(md5(time()), 0, 8),
            'email' => 'pro@example.com',
            'status' => 'active'
          ], false);
          wp_cache_delete('sfb_license', 'options');
          $license_test_result = __('License set to Active (Pro). Pro features enabled.', 'submittal-builder');

          // Check for agency dev constant
          if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV) {
            $license_test_result .= ' ' . __('WARNING: SFB_AGENCY_DEV is active - Agency features will still show!', 'submittal-builder');
          }
          break;

        case 'agency':
          update_option('sfb_license', [
            'key' => 'DEMO-AGENCY-KEY-' . substr(md5(time()), 0, 8),
            'email' => 'agency@example.com',
            'status' => 'active',
            'tier' => 'agency'
          ], false);
          wp_cache_delete('sfb_license', 'options');
          $license_test_result = __('License set to Active (Agency). All Pro + Agency features enabled.', 'submittal-builder');
          break;
      }
    }

    if (isset($_POST['sfb_seed_pack']) && check_admin_referer('sfb_seed_pack')) {
      $pack = sanitize_text_field($_POST['industry_pack'] ?? '');
      $create_draft = isset($_POST['create_draft']);
      $result = $this->seed_industry_pack($pack, $create_draft);
      $action = 'seed';
    }

    if (isset($_POST['sfb_reset_demo']) && check_admin_referer('sfb_reset_demo')) {
      // Verify SFB_PRO_DEV constant
      if (!defined('SFB_PRO_DEV') || SFB_PRO_DEV !== true) {
        wp_die(
          '<h1>' . esc_html__('Development Mode Required', 'submittal-builder') . '</h1>' .
          '<p>' . esc_html__('This action requires SFB_PRO_DEV to be enabled.', 'submittal-builder') . '</p>' .
          '<p>' . esc_html__('Add this line to your wp-config.php file:', 'submittal-builder') . '</p>' .
          '<pre style="background:#f5f5f5;padding:10px;border-radius:4px">define(\'SFB_PRO_DEV\', true);</pre>' .
          '<p><a href="' . esc_url(admin_url('admin.php?page=sfb-demo-tools')) . '">' . esc_html__('← Back to Demo Tools', 'submittal-builder') . '</a></p>',
          esc_html__('Permission Denied', 'submittal-builder'),
          ['back_link' => true]
        );
      }

      // Verify checkbox confirmation
      if (empty($_POST['sfb_confirm_demo_reset'])) {
        wp_die(
          '<h1>' . esc_html__('Confirmation Required', 'submittal-builder') . '</h1>' .
          '<p>' . esc_html__('Please confirm the reset checkbox before proceeding.', 'submittal-builder') . '</p>' .
          '<p><a href="' . esc_url(admin_url('admin.php?page=sfb-demo-tools')) . '">' . esc_html__('← Back to Demo Tools', 'submittal-builder') . '</a></p>',
          esc_html__('Confirmation Required', 'submittal-builder'),
          ['back_link' => true]
        );
      }

      $result = $this->reset_demo_content();
      $action = 'reset';
    }

    // Generate Demo Data Handler
    if (isset($_POST['sfb_generate_demo_data']) && check_admin_referer('sfb_generate_demo_data')) {
      $result = $this->generate_screenshot_demo_data();
      $action = 'generate_demo';
    }

    // Clear Demo Data Handler
    if (isset($_POST['sfb_clear_demo_data']) && check_admin_referer('sfb_generate_demo_data')) {
      $result = $this->clear_screenshot_demo_data();
      $action = 'clear_demo';
    }

    // Get available packs
    $packs = $this->get_available_packs();

    // Get frontend page with shortcode
    $frontend_url = $this->get_frontend_page_url();

    // Check if drafts are enabled
    $drafts_enabled = sfb_feature_enabled('server_drafts');

    ?>
    <div class="wrap sfb-tools">
      <h1><?php echo esc_html__('Demo Tools', 'submittal-builder'); ?></h1>
      <p class="description" style="margin-top:-8px;margin-bottom:24px;">
        <?php esc_html_e('Developer tools for testing and demonstration. Load sample catalogs, preview PDFs, test license states, and reset data.', 'submittal-builder'); ?>
      </p>

      <!-- Draft Management Card -->
      <div class="sfb-card">
        <h2>🧹 <?php echo esc_html__('Draft Management', 'submittal-builder'); ?></h2>
        <p class="sfb-muted"><?php echo esc_html__('Clean up temp drafts and verify the system is healthy.', 'submittal-builder'); ?></p>

        <div class="sfb-actions">
          <button id="sfb-purge-btn"
                  class="button button-primary sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_purge')); ?>">
            <?php esc_html_e('Purge Expired Drafts', 'submittal-builder'); ?>
          </button>

          <button id="sfb-smoke-btn"
                  class="button sfb-btn"
                  data-nonce="<?php echo esc_attr(wp_create_nonce('sfb_smoke')); ?>">
            <?php esc_html_e('Run Smoke Test', 'submittal-builder'); ?>
          </button>
        </div>

        <div id="sfb-drafts-status" class="sfb-status">
          <?php echo esc_html__('Idle — ready when you are.', 'submittal-builder'); ?>
        </div>

        <div class="sfb-grid" style="margin-top:10px;">
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Cron Status', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-cron-status">
              <?php echo wp_kses_post($cron_status_html); ?>
            </div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Draft Statistics', 'submittal-builder'); ?></div>
            <div class="v" id="sfb-draft-stats">
              <?php echo esc_html($draft_stats['text']); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- System Information Card -->
      <div class="sfb-card">
        <h2>🧩 <?php echo esc_html__('System Information', 'submittal-builder'); ?></h2>
        <div class="sfb-grid">
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Plugin Version', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($plugin_version); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Pro Status', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($pro_status_label); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('Shareable Drafts', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html($shareable_label); ?></div>
          </div>
          <div class="sfb-kv">
            <div class="k"><?php esc_html_e('WordPress', 'submittal-builder'); ?></div>
            <div class="v"><?php echo esc_html(get_bloginfo('version')); ?></div>
          </div>
        </div>
      </div>

      <?php if ($result !== null): ?>
        <?php if ($result['success']): ?>
          <div class="notice notice-success is-dismissible">
            <p><?php echo wp_kses_post($result['message']); ?></p>
            <?php if (!empty($result['stats'])): ?>
              <p>
                <?php
                printf(
                  esc_html__('Created %d categories, %d types, %d items.', 'submittal-builder'),
                  (int)$result['stats']['categories'],
                  (int)$result['stats']['types'],
                  (int)$result['stats']['items']
                );
                ?>
              </p>
            <?php endif; ?>
            <?php if (!empty($result['draft_url'])): ?>
              <p>
                <strong><?php
                $expiry_days = get_option('sfb_branding')['drafts_expiry_days'] ?? 45;
                printf(
                  esc_html__('Demo draft link (expires in %d days):', 'submittal-builder'),
                  $expiry_days
                );
                ?></strong><br>
                <a href="<?php echo esc_url($result['draft_url']); ?>" target="_blank"><?php echo esc_html($result['draft_url']); ?></a>
              </p>
            <?php endif; ?>
            <?php if (!empty($result['admin_url'])): ?>
              <p>
                <strong><?php echo esc_html__('Quick Links:', 'submittal-builder'); ?></strong><br>
                <a href="<?php echo esc_url($result['admin_url']); ?>"><?php echo esc_html__('View in Admin Builder', 'submittal-builder'); ?></a>
                <?php if (!empty($result['frontend_url'])): ?>
                  | <a href="<?php echo esc_url($result['frontend_url']); ?>" target="_blank"><?php echo esc_html__('View Frontend Page', 'submittal-builder'); ?></a>
                <?php endif; ?>
              </p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html($result['message']); ?></p>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($license_test_result): ?>
        <div class="notice notice-success is-dismissible">
          <p>
            <span class="dashicons dashicons-yes" style="color:#46b450;"></span>
            <strong><?php echo esc_html__('Saved:', 'submittal-builder'); ?></strong>
            <?php echo esc_html($license_test_result); ?>
            <em><?php echo esc_html__('(Refresh the page to see the menu update)', 'submittal-builder'); ?></em>
          </p>
        </div>
        <script>
          // Auto-refresh after 1.5 seconds to show menu change
          setTimeout(function() {
            window.location.reload();
          }, 1500);
        </script>
      <?php endif; ?>

      <?php if (!$drafts_enabled): ?>
        <div class="notice notice-info">
          <p>
            <?php echo esc_html__('Server drafts are currently disabled.', 'submittal-builder'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-settings')); ?>"><?php echo esc_html__('Enable in Settings → Drafts', 'submittal-builder'); ?></a>
          </p>
        </div>
      <?php endif; ?>

      <!-- License State Tester (Temp) -->
      <div class="card" style="max-width: 800px; background: #fff3cd; border-left: 4px solid #ffc107;">
        <h2 style="margin-top: 0;">
          <?php echo esc_html__('License State Tester (Temp)', 'submittal-builder'); ?>
          <span style="font-size: 12px; font-weight: normal; color: #856404;">
            — <?php echo esc_html__('Admin Only', 'submittal-builder'); ?>
          </span>
        </h2>
        <p class="description" style="margin-bottom: 16px;">
          <?php echo esc_html__('Test different license states to see how the admin menu adapts. Changes take effect immediately.', 'submittal-builder'); ?>
        </p>

        <form method="post" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
          <?php wp_nonce_field('sfb_test_license_state'); ?>

          <button type="submit" name="sfb_test_license_state" value="1" class="button" onclick="document.querySelector('input[name=license_state]').value='free'">
            <?php echo esc_html__('Set to Free', 'submittal-builder'); ?>
          </button>

          <button type="submit" name="sfb_test_license_state" value="1" class="button" onclick="document.querySelector('input[name=license_state]').value='expired'">
            <?php echo esc_html__('Set to Expired', 'submittal-builder'); ?>
          </button>

          <button type="submit" name="sfb_test_license_state" value="1" class="button" onclick="document.querySelector('input[name=license_state]').value='active'">
            <?php echo esc_html__('Set to Pro', 'submittal-builder'); ?>
          </button>

          <button type="submit" name="sfb_test_license_state" value="1" class="button button-primary" onclick="document.querySelector('input[name=license_state]').value='agency'" style="background: #7c3aed; border-color: #7c3aed;">
            <?php echo esc_html__('Set to Agency', 'submittal-builder'); ?>
          </button>

          <input type="hidden" name="license_state" value="">

          <div style="width: 100%; margin-top: 8px; color: #856404; font-size: 12px;">
            <?php
            $current_lic = get_option('sfb_license', []);
            $current_status = $current_lic['status'] ?? 'none';
            $current_tier = $current_lic['tier'] ?? '';

            // Display current state
            if (empty($current_lic)) {
              echo '📍 <strong>' . esc_html__('Current: Free (no license)', 'submittal-builder') . '</strong>';
            } else {
              if ($current_status === 'active' && $current_tier === 'agency') {
                echo '📍 <strong>' . esc_html__('Current: Active (Agency)', 'submittal-builder') . '</strong>';
              } elseif ($current_status === 'active') {
                echo '📍 <strong>' . esc_html__('Current: Active (Pro)', 'submittal-builder') . '</strong>';
              } else {
                echo '📍 <strong>' . sprintf(esc_html__('Current: %s', 'submittal-builder'), esc_html(ucfirst($current_status))) . '</strong>';
              }
            }

            // Show dev constant warnings
            $dev_warnings = [];
            if (defined('SFB_PRO_DEV') && SFB_PRO_DEV) {
              $dev_warnings[] = 'SFB_PRO_DEV';
            }
            if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV) {
              $dev_warnings[] = 'SFB_AGENCY_DEV';
            }
            if (!empty($dev_warnings)) {
              echo '<br><strong style="color: #dc2626;">⚠️ ' . sprintf(esc_html__('%s constant(s) ACTIVE - overriding all license checks!', 'submittal-builder'), implode(', ', $dev_warnings)) . '</strong>';
              echo '<br><span style="color: #856404;">' . esc_html__('Remove from wp-config.php to test license states properly.', 'submittal-builder') . '</span>';
            }
            ?>
          </div>
        </form>
      </div>

      <!-- License Debug Info (Temp) -->
      <div class="card" style="max-width: 800px; background: #e0f2fe; border-left: 4px solid #0284c7;">
        <h2 style="margin-top: 0;">
          <?php echo esc_html__('🔍 License Debug Info', 'submittal-builder'); ?>
          <span style="font-size: 12px; font-weight: normal; color: #0c4a6e;">
            — <?php echo esc_html__('Temp Debugging', 'submittal-builder'); ?>
          </span>
        </h2>

        <?php
        $debug_lic = get_option('sfb_license', []);
        $is_agency_check = function_exists('sfb_is_agency_license') ? sfb_is_agency_license() : 'Function not found';
        $is_pro_check = function_exists('sfb_is_pro_active') ? sfb_is_pro_active() : 'Function not found';
        ?>

        <div style="font-family: monospace; font-size: 12px;">
          <p><strong>Raw License Option:</strong></p>
          <pre style="background: #f0f9ff; padding: 10px; border: 1px solid #0284c7; overflow-x: auto;"><?php print_r($debug_lic); ?></pre>

          <p><strong>Function Checks:</strong></p>
          <ul style="list-style: none; padding-left: 0;">
            <li>sfb_is_agency_license(): <strong style="color: <?php echo $is_agency_check === true ? '#16a34a' : '#dc2626'; ?>"><?php echo $is_agency_check === true ? '✅ TRUE' : ($is_agency_check === false ? '❌ FALSE' : '⚠️ ' . esc_html($is_agency_check)); ?></strong></li>
            <li>sfb_is_pro_active(): <strong style="color: <?php echo $is_pro_check === true ? '#16a34a' : '#dc2626'; ?>"><?php echo $is_pro_check === true ? '✅ TRUE' : ($is_pro_check === false ? '❌ FALSE' : '⚠️ ' . esc_html($is_pro_check)); ?></strong></li>
          </ul>

          <p><strong>Expected Values for Agency:</strong></p>
          <ul style="list-style: none; padding-left: 0;">
            <li>status = 'active': <?php echo isset($debug_lic['status']) && $debug_lic['status'] === 'active' ? '✅' : '❌'; ?></li>
            <li>tier = 'agency': <?php echo isset($debug_lic['tier']) && $debug_lic['tier'] === 'agency' ? '✅' : '❌'; ?></li>
          </ul>
        </div>
      </div>

      <!-- Preview Sample PDF -->
      <div class="card" style="max-width: 800px;">
        <h2><?php echo esc_html__('Preview Sample PDF', 'submittal-builder'); ?></h2>
        <p class="description" style="margin-bottom: 16px;">
          <?php echo esc_html__('Generate a sample submittal packet PDF using your current branding settings (company name, logo, and brand color).', 'submittal-builder'); ?>
        </p>
        <a
          href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=sfb_test_pdf'), 'sfb_test_pdf')); ?>"
          class="button button-primary"
          style="background: #7c3aed; border-color: #7c3aed;"
        >
          📄 Preview Sample PDF
        </a>
      </div>

      <div class="card" style="max-width: 800px;">
        <h2><?php echo esc_html__('Seed Industry Catalog', 'submittal-builder'); ?></h2>

        <form method="post">
          <?php wp_nonce_field('sfb_seed_pack'); ?>

          <table class="form-table">
            <tr>
              <th scope="row">
                <label for="industry_pack"><?php echo esc_html__('Industry Pack', 'submittal-builder'); ?></label>
              </th>
              <td>
                <select name="industry_pack" id="industry_pack" class="regular-text">
                  <?php foreach ($packs as $key => $label): ?>
                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <p class="description">
                  <?php echo esc_html__('Choose an industry-specific demo catalog. Each pack includes 3-5 categories with realistic products and metadata.', 'submittal-builder'); ?>
                </p>
              </td>
            </tr>

            <tr>
              <th scope="row"><?php echo esc_html__('Options', 'submittal-builder'); ?></th>
              <td>
                <label>
                  <input type="checkbox" name="create_draft" value="1" <?php checked(!$drafts_enabled, false); ?> <?php disabled(!$drafts_enabled); ?>>
                  <?php echo esc_html__('Also create a demo draft', 'submittal-builder'); ?>
                </label>
                <p class="description">
                  <?php echo esc_html__('Automatically creates a server draft with 6-10 preselected items and returns the shareable URL.', 'submittal-builder'); ?>
                  <?php if (!$drafts_enabled): ?>
                    <br><strong><?php echo esc_html__('(Requires server drafts to be enabled)', 'submittal-builder'); ?></strong>
                  <?php endif; ?>
                </p>
              </td>
            </tr>
          </table>

          <p class="submit">
            <button type="submit" name="sfb_seed_pack" class="button button-primary">
              <?php echo esc_html__('Seed Selected Pack', 'submittal-builder'); ?>
            </button>
          </p>
        </form>
      </div>

      <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php echo esc_html__('Reset Demo Content', 'submittal-builder'); ?></h2>

        <style>
          .sfb-danger {color:#dc2626;font-weight:600}
        </style>

        <div class="notice notice-error" style="border-left-color:#dc2626;margin: 15px 0;">
          <p><strong><?php echo esc_html__('Danger zone:', 'submittal-builder'); ?></strong> <?php echo esc_html__('This removes ONLY demo content created by the seeder (items tagged', 'submittal-builder'); ?> <code>_sfb_demo_seed=1</code>). <?php echo esc_html__('Your own catalogs are not touched.', 'submittal-builder'); ?></p>
          <p style="margin-top:6px"><?php echo esc_html__('To proceed, check the box and click Reset.', 'submittal-builder'); ?></p>
          <?php if (!defined('SFB_PRO_DEV') || SFB_PRO_DEV !== true): ?>
            <p style="margin-top:10px;padding-top:10px;border-top:1px solid #ddd">
              <strong><?php echo esc_html__('Note:', 'submittal-builder'); ?></strong> <?php echo esc_html__('This action requires', 'submittal-builder'); ?> <code>define('SFB_PRO_DEV', true)</code> <?php echo esc_html__('in your wp-config.php file.', 'submittal-builder'); ?>
            </p>
          <?php endif; ?>
        </div>

        <form method="post">
          <?php wp_nonce_field('sfb_reset_demo'); ?>

          <table class="form-table">
            <tr>
              <th scope="row"><?php echo esc_html__('Confirmation', 'submittal-builder'); ?></th>
              <td>
                <label>
                  <input type="checkbox" name="sfb_confirm_demo_reset" value="1" required>
                  <span class="sfb-danger"><?php echo esc_html__('I understand this will delete all demo-seeded content.', 'submittal-builder'); ?></span>
                </label>
              </td>
            </tr>
          </table>

          <p class="submit">
            <button type="submit" name="sfb_reset_demo" class="button button-secondary" style="border-color: #d63638; color: #d63638;">
              <?php echo esc_html__('Reset Seeded Demo Content', 'submittal-builder'); ?>
            </button>
          </p>
        </form>
      </div>

      <!-- Demo Data Generator (for Screenshots) -->
      <div class="card" style="max-width: 800px; margin-top: 20px; background: #f0f9ff; border-left: 4px solid #0ea5e9;">
        <h2 style="margin-top: 0;">
          📸 <?php echo esc_html__('Demo Data Generator (for Screenshots)', 'submittal-builder'); ?>
        </h2>

        <p><?php echo esc_html__('Generate realistic demo data for Tracking, Agency, and Analytics pages to help with WordPress plugin submission screenshots.', 'submittal-builder'); ?></p>

        <div class="notice notice-info" style="margin: 15px 0;">
          <p><strong><?php echo esc_html__('What will be created:', 'submittal-builder'); ?></strong></p>
          <ul style="margin-left: 20px; list-style: disc;">
            <li><?php echo esc_html__('10 tracking links with realistic project names, views, and timestamps', 'submittal-builder'); ?></li>
            <li><?php echo esc_html__('5 Agency Packs with sample catalog items (Electrical, HVAC, Plumbing, etc.)', 'submittal-builder'); ?></li>
            <li><?php echo esc_html__('30 days of analytics activity data for the Agency Analytics dashboard', 'submittal-builder'); ?></li>
          </ul>
          <p style="margin-top: 10px;"><em><?php echo esc_html__('All demo data is tagged and can be easily cleared with the "Clear Demo Data" button below.', 'submittal-builder'); ?></em></p>
        </div>

        <form method="post" style="margin-top: 20px;">
          <?php wp_nonce_field('sfb_generate_demo_data'); ?>
          <p class="submit" style="margin-top: 0;">
            <button type="submit" name="sfb_generate_demo_data" class="button button-primary">
              <?php echo esc_html__('Generate Demo Data', 'submittal-builder'); ?>
            </button>
            <button type="submit" name="sfb_clear_demo_data" class="button button-secondary" style="margin-left: 10px;">
              <?php echo esc_html__('Clear Demo Data', 'submittal-builder'); ?>
            </button>
          </p>
        </form>
      </div>

      <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php echo esc_html__('Quick Links', 'submittal-builder'); ?></h2>
        <table class="form-table">
          <tr>
            <th scope="row"><?php echo esc_html__('Frontend Page', 'submittal-builder'); ?></th>
            <td>
              <?php if ($frontend_url): ?>
                <a href="<?php echo esc_url($frontend_url); ?>" target="_blank"><?php echo esc_html($frontend_url); ?></a>
              <?php else: ?>
                <em><?php echo esc_html__('No page found with [submittal_builder] shortcode', 'submittal-builder'); ?></em>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Admin Builder', 'submittal-builder'); ?></th>
            <td>
              <a href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>"><?php echo esc_html__('Submittal Builder → Builder', 'submittal-builder'); ?></a>
            </td>
          </tr>
        </table>
      </div>

      <?php $this->render_feedback_footer(); ?>
    </div>
    <?php
  }

  /** Run smoke test for draft system */
  private function run_draft_smoke_test() {
    try {
      // Create test draft
      $test_payload = [
        'items' => [
          ['id' => 1, 'title' => 'Test Item', 'meta' => [], 'path' => ['Test', 'Category', 'Type']]
        ],
        'meta' => [
          'project' => 'Smoke Test',
          'contractor' => 'Test Contractor',
          'submittal' => 'TEST-001',
          'preset' => 'packet',
          'format' => 'pdf',
          'include_cover' => true,
          'include_leed' => false
        ]
      ];

      $validation = $this->validate_draft_payload($test_payload);
      if (!$validation['valid']) {
        return [
          'success' => false,
          'message' => 'Validation failed: ' . implode('; ', $validation['errors'])
        ];
      }

      $draft_id = $this->sfb_rand_id(12);
      $created_at = current_time('mysql');
      $expires_at = date('Y-m-d H:i:s', strtotime('+45 days'));

      // Create post
      $post_id = wp_insert_post([
        'post_type' => 'sfb_draft',
        'post_title' => 'SFB Draft ' . $draft_id . ' (TEST)',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
      ]);

      if (is_wp_error($post_id)) {
        return [
          'success' => false,
          'message' => 'Failed to create test draft: ' . $post_id->get_error_message()
        ];
      }

      // Store metadata
      update_post_meta($post_id, '_sfb_draft_id', $draft_id);
      update_post_meta($post_id, '_sfb_draft_payload', [
        'version' => 1,
        'items' => $validation['data']['items'],
        'meta' => $validation['data']['meta'],
        'created_at' => $created_at,
        'expires_at' => $expires_at,
      ]);
      update_post_meta($post_id, '_sfb_draft_created_at', $created_at);
      update_post_meta($post_id, '_sfb_draft_expires_at', $expires_at);

      // Retrieve draft
      global $wpdb;
      $found_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = '_sfb_draft_id'
         AND meta_value = %s
         LIMIT 1",
        $draft_id
      ));

      if ($found_id != $post_id) {
        wp_delete_post($post_id, true);
        return [
          'success' => false,
          'message' => 'Failed to retrieve test draft'
        ];
      }

      $payload = get_post_meta($post_id, '_sfb_draft_payload', true);
      if (empty($payload) || !isset($payload['items'])) {
        wp_delete_post($post_id, true);
        return [
          'success' => false,
          'message' => 'Failed to retrieve draft payload'
        ];
      }

      // Delete test draft
      wp_delete_post($post_id, true);

      return [
        'success' => true,
        'message' => '✓ Smoke test passed! Created draft ID: ' . $draft_id . ', retrieved payload, and cleaned up successfully.'
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => 'Test failed with exception: ' . $e->getMessage()
      ];
    }
  }

  /** Weekly Lead Export: AJAX handler for "Send Now" button */
  function ajax_send_weekly_export_now() {
    // Security checks
    if (!check_admin_referer('sfb_send_weekly_export_now', 'nonce')) {
      wp_send_json_error('Security check failed');
    }

    if (!current_user_can('manage_options')) {
      wp_send_json_error('Unauthorized');
    }

    if (!sfb_is_agency_license()) {
      wp_send_json_error('This feature requires an Agency license');
    }

    // Send the export
    $result = $this->send_weekly_lead_export();

    if ($result['success']) {
      wp_send_json_success(['message' => $result['message']]);
    } else {
      wp_send_json_error($result['message']);
    }
  }

  /** Weekly Lead Export: Cron callback */
  function cron_send_weekly_export() {
    // Check if feature is enabled
    if (!get_option('sfb_lead_weekly_export_enabled', false)) {
      return;
    }

    // Check Agency license
    if (!sfb_is_agency_license()) {
      error_log('[SFB] Weekly export skipped: Agency license required');
      return;
    }

    // Send the export
    $result = $this->send_weekly_lead_export();

    if ($result['success']) {
      error_log('[SFB] Weekly lead export sent successfully: ' . $result['message']);
    } else {
      error_log('[SFB] Weekly lead export failed: ' . $result['message']);
    }
  }

  /** Weekly Lead Export: Core sending logic */
  function send_weekly_lead_export() {
    global $wpdb;

    // Check if feature is enabled
    if (!get_option('sfb_lead_weekly_export_enabled', false)) {
      return [
        'success' => false,
        'message' => 'Weekly export is not enabled. Enable it in Settings first.'
      ];
    }

    // Get recipient email
    $recipient_email = get_option('sfb_lead_weekly_export_email', '');
    if (empty($recipient_email) || !is_email($recipient_email)) {
      return [
        'success' => false,
        'message' => 'Invalid recipient email address. Please configure in Settings.'
      ];
    }

    // Get new leads (where last_export_sent is NULL)
    $leads_table = $wpdb->prefix . 'sfb_leads';
    $new_leads = $wpdb->get_results(
      "SELECT * FROM $leads_table
       WHERE last_export_sent IS NULL
       ORDER BY created_at ASC",
      ARRAY_A
    );

    if (empty($new_leads)) {
      return [
        'success' => false,
        'message' => 'No new leads to export. All leads have already been sent.'
      ];
    }

    // Generate CSV content
    $csv_content = $this->generate_csv_content($new_leads);

    // Generate filename
    $filename = 'sfb-leads-weekly-' . date('Y-m-d-His') . '.csv';

    // Email subject and body
    $site_name = get_bloginfo('name');
    $subject = sprintf('[%s] Weekly Lead Export - %d New Leads', $site_name, count($new_leads));

    $body = sprintf(
      "Hello,\n\n" .
      "Attached is your weekly lead export from %s.\n\n" .
      "Summary:\n" .
      "- Total new leads: %d\n" .
      "- Date range: %s to %s\n" .
      "- Export generated: %s\n\n" .
      "This export includes only leads that haven't been sent before.\n\n" .
      "---\n" .
      "Submittal & Spec Sheet Builder\n" .
      "%s",
      $site_name,
      count($new_leads),
      $new_leads[0]['created_at'],
      end($new_leads)['created_at'],
      current_time('mysql'),
      home_url()
    );

    // Prepare email with attachment
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    // Create temporary file for attachment
    $temp_file = wp_tempnam($filename);
    file_put_contents($temp_file, $csv_content);

    // Send email
    $sent = wp_mail(
      $recipient_email,
      $subject,
      $body,
      $headers,
      [$temp_file]
    );

    // Log email attempt for debugging
    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log(sprintf(
        '[SFB Weekly Export] Email attempt - To: %s, Subject: %s, Sent: %s, Attachment: %s',
        $recipient_email,
        $subject,
        $sent ? 'YES' : 'NO',
        $temp_file
      ));
    }

    // Clean up temp file
    @unlink($temp_file);

    if (!$sent) {
      return [
        'success' => false,
        'message' => 'Failed to send email. Check your WordPress email configuration.'
      ];
    }

    // Mark leads as sent
    $lead_ids = wp_list_pluck($new_leads, 'id');
    $lead_ids_str = implode(',', array_map('intval', $lead_ids));
    $now = current_time('mysql');

    $wpdb->query(
      "UPDATE $leads_table
       SET last_export_sent = '$now'
       WHERE id IN ($lead_ids_str)"
    );

    return [
      'success' => true,
      'message' => sprintf(
        'Successfully sent %d leads to %s',
        count($new_leads),
        $recipient_email
      )
    ];
  }

  /** Generate CSV content from leads array */
  function generate_csv_content($leads) {
    // Create memory stream
    $output = fopen('php://memory', 'w');

    // CSV headers (matching the leads page export)
    fputcsv($output, [
      'Date',
      'Email',
      'Phone',
      'Project Name',
      'Items',
      'Top Category',
      'Consent',
      'UTM Source',
      'UTM Medium',
      'UTM Campaign',
      'UTM Term',
      'UTM Content',
      'IP Hash (partial)',
    ]);

    // CSV rows
    foreach ($leads as $lead) {
      $utm = json_decode($lead['utm_json'], true) ?: [];
      $ip_partial = !empty($lead['ip_hash']) ? substr($lead['ip_hash'], 0, 8) . '...' : '';

      fputcsv($output, [
        $lead['created_at'],
        $lead['email'],
        $lead['phone'] ?? '',
        $lead['project_name'] ?? '',
        $lead['num_items'] ?? 0,
        $lead['top_category'] ?? '',
        !empty($lead['consent']) ? 'Yes' : 'No',
        $utm['source'] ?? '',
        $utm['medium'] ?? '',
        $utm['campaign'] ?? '',
        $utm['term'] ?? '',
        $utm['content'] ?? '',
        $ip_partial,
      ]);
    }

    // Get CSV content
    rewind($output);
    $csv_content = stream_get_contents($output);
    fclose($output);

    return $csv_content;
  }

  /** Redirect to onboarding page on first activation */
  function maybe_redirect_to_onboarding() {
    // Only redirect if flag is set
    if (!get_option('sfb_just_activated')) return;

    // Clear flag
    delete_option('sfb_just_activated');

    // Don't redirect on bulk activations
    if (isset($_GET['activate-multi'])) return;

    // Redirect to onboarding page
    wp_safe_redirect(admin_url('admin.php?page=sfb-onboarding'));
    exit;
  }

  /** Handle onboarding setup form submission */
  function handle_onboarding_setup() {
    // Only process if form was submitted
    if (!isset($_POST['sfb_quick_setup'])) return;

    // Verify nonce
    if (!check_admin_referer('sfb_quick_setup')) return;

    // Save branding settings
    $new_brand = [
      'company_name' => sanitize_text_field($_POST['company_name'] ?? ''),
      'primary_color' => sanitize_text_field($_POST['primary_color'] ?? '#111827'),
      'logo_url' => esc_url_raw($_POST['logo_url'] ?? ''),
    ];

    // Merge with existing branding to preserve other fields
    $existing = get_option('sfb_branding', []);
    update_option('sfb_branding', array_merge($existing, $new_brand));

    // Mark onboarding as completed
    update_option('sfb_onboarding_completed', true, false);

    // Redirect to same page with success message
    wp_safe_redirect(admin_url('admin.php?page=sfb-onboarding&setup=done'));
    exit;
  }

  /** Handle leads CSV export (runs early in admin_init to avoid header issues) */
  function handle_leads_csv_export() {
    // Check if this is a leads CSV export request
    if (!isset($_GET['page']) || $_GET['page'] !== 'sfb-leads') return;
    if (!isset($_GET['action']) || $_GET['action'] !== 'export_csv') return;
    if (!isset($_GET['nonce'])) return;

    // Verify nonce
    if (!check_admin_referer('sfb_export_leads', 'nonce')) {
      wp_die('Security check failed', 'Error', ['response' => 403]);
    }

    // Check capability
    if (!current_user_can('manage_options')) {
      wp_die('Unauthorized', 'Error', ['response' => 403]);
    }

    // Get filter parameters
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

    // Get ALL filtered leads (no pagination for export)
    $leads_data = $this->get_filtered_leads($search, $date_from, $date_to, 999999, 0);
    $leads = $leads_data['leads'];

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sfb-leads-' . date('Y-m-d-His') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, [
      'Date',
      'Email',
      'Phone',
      'Project Name',
      'Items',
      'Top Category',
      'Consent',
      'UTM Source',
      'UTM Medium',
      'UTM Campaign',
      'UTM Term',
      'UTM Content',
      'IP Hash (partial)',
    ]);

    // CSV rows
    foreach ($leads as $lead) {
      $utm = json_decode($lead['utm_json'], true) ?: [];
      $ip_partial = !empty($lead['ip_hash']) ? substr($lead['ip_hash'], 0, 8) . '...' : '';

      fputcsv($output, [
        $lead['created_at'],
        $lead['email'],
        $lead['phone'],
        $lead['project_name'],
        $lead['num_items'],
        $lead['top_category'],
        $lead['consent'] ? 'Yes' : 'No',
        $utm['source'] ?? '',
        $utm['medium'] ?? '',
        $utm['campaign'] ?? '',
        $utm['term'] ?? '',
        $utm['content'] ?? '',
        $ip_partial,
      ]);
    }

    fclose($output);
    exit;
  }

  /** Show welcome notice (dismissible, per-user) */
  function show_welcome_notice() {
    // Only show on onboarding page
    $page = isset($_GET['page']) ? (string) sanitize_key($_GET['page']) : '';

    // Return early if not the onboarding page
    if ($page !== 'sfb-onboarding') return;

    // Check if user dismissed
    $user_id = get_current_user_id();
    if (get_user_meta($user_id, 'sfb_welcome_dismissed', true)) return;

    ?>
    <div class="notice notice-info is-dismissible" id="sfb-welcome-notice">
      <p>
        <strong>Welcome to Submittal & Spec Builder!</strong>
        Need help getting started?
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-onboarding')); ?>">Run the setup wizard</a>
        or <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-branding')); ?>">configure your branding</a>.
      </p>
    </div>
    <script>
    jQuery(function($){
      $('#sfb-welcome-notice').on('click', '.notice-dismiss', function(){
        $.post(ajaxurl, {action: 'sfb_dismiss_welcome'});
      });
    });
    </script>
    <?php
  }

  /** Dismiss welcome notice (AJAX handler) */
  function dismiss_welcome_notice() {
    $user_id = get_current_user_id();
    update_user_meta($user_id, 'sfb_welcome_dismissed', 1);
    wp_die();
  }

  /** Show license status notices */
  function show_license_notices() {
    // Only show to admins
    if (!current_user_can('manage_options')) {
      return;
    }

    // Don't show on license management page (they already see the status there)
    $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
    if ($page === 'sfb-license') {
      return;
    }

    // Get license status
    if (!function_exists('sfb_get_license_status')) {
      return;
    }

    $license = sfb_get_license_status();

    // Expired license notice
    if ($license['status'] === 'expired') {
      $renew_url = sfb_get_link('renew');
      if (!$renew_url) {
        $renew_url = admin_url('admin.php?page=sfb-license');
      }
      ?>
      <div class="notice notice-warning">
        <p>
          <strong><?php esc_html_e('Submittal Builder License Expired', 'submittal-builder'); ?></strong> —
          <?php esc_html_e('Your license has expired. Renew to continue receiving updates and support.', 'submittal-builder'); ?>
          <a href="<?php echo esc_url($renew_url); ?>" class="button button-small" style="margin-left:10px;">
            <?php esc_html_e('Renew License', 'submittal-builder'); ?>
          </a>
        </p>
      </div>
      <?php
    }

    // Invalid license notice
    elseif ($license['status'] === 'invalid' && $license['has_key']) {
      ?>
      <div class="notice notice-error">
        <p>
          <strong><?php esc_html_e('Submittal Builder License Invalid', 'submittal-builder'); ?></strong> —
          <?php esc_html_e('Your license key is invalid. Please check your license settings.', 'submittal-builder'); ?>
          <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-license')); ?>" class="button button-small" style="margin-left:10px;">
            <?php esc_html_e('Manage License', 'submittal-builder'); ?>
          </a>
        </p>
      </div>
      <?php
    }

    // Connection error notice (temporary)
    elseif (!empty($license['error']) && $license['status'] !== 'active') {
      ?>
      <div class="notice notice-info is-dismissible">
        <p>
          <strong><?php esc_html_e('Submittal Builder License Check Failed', 'submittal-builder'); ?></strong> —
          <?php esc_html_e('Could not connect to license server. This is usually temporary.', 'submittal-builder'); ?>
          <?php if ($license['status'] === 'active'): ?>
            <?php esc_html_e('Using cached license status.', 'submittal-builder'); ?>
          <?php endif; ?>
        </p>
      </div>
      <?php
    }
  }

  /** AJAX handler for purging expired drafts (Phase 7: delegates to SFB_Drafts) */
  function ajax_purge_expired_drafts() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_purge');

    // Delegate to SFB_Drafts class (Phase 7 refactor)
    $result = SFB_Drafts::purge_expired();

    wp_send_json_success([
      'message' => $result['message'],
      'stats_text' => $result['data']['stats_text'],
      'purged' => $result['data']['purged'],
      'total' => $result['data']['total'],
      'expired' => $result['data']['expired']
    ]);
  }

  /** AJAX handler for smoke test (Phase 7: delegates to SFB_Tools) */
  function ajax_run_smoke_test() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_smoke');

    // Delegate to SFB_Tools class (Phase 7 refactor)
    $result = SFB_Tools::run_smoke_test();

    if (!$result['success']) {
      wp_send_json_error(['message' => '❌ ' . $result['message']]);
    }

    wp_send_json_success([
      'message' => $result['message'],
      'stats_text' => $result['data']['stats_text'],
      'total' => $result['data']['total'],
      'expired' => $result['data']['expired']
    ]);
  }

  /** AJAX handler: Test email delivery */
  function ajax_test_email() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_test_email');

    // Get recipient email
    $to = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    if (empty($to) || !is_email($to)) {
      wp_send_json_error(['message' => __('Invalid email address', 'submittal-builder')]);
    }

    // Prepare test email
    $subject = __('Test Email from Submittal & Spec Sheet Builder', 'submittal-builder');
    $message = __('This is a test email to verify SMTP configuration.', 'submittal-builder') . "\n\n";
    $message .= sprintf(__('Sent at: %s', 'submittal-builder'), wp_date('Y-m-d H:i:s')) . "\n";
    $message .= sprintf(__('From site: %s', 'submittal-builder'), get_bloginfo('name')) . "\n";

    // Send email
    $sent = wp_mail($to, $subject, $message);

    if ($sent) {
      wp_send_json_success([
        'message' => sprintf(__('✅ Test email sent successfully to %s', 'submittal-builder'), $to)
      ]);
    } else {
      wp_send_json_error([
        'message' => __('❌ Failed to send test email. Check SMTP configuration.', 'submittal-builder')
      ]);
    }
  }

  /** AJAX handler: Clear tracking data (Pro) */
  function ajax_clear_tracking() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_clear_tracking');

    // Check Pro status
    if (!sfb_is_pro_active()) {
      wp_send_json_error(['message' => __('Pro license required', 'submittal-builder')], 403);
    }

    // Get current tracking data
    $packets = get_option('sfb_packets', []);
    $count = count($packets);

    // Clear all tracking data
    update_option('sfb_packets', []);

    wp_send_json_success([
      'message' => sprintf(__('✅ Cleared %d tracking links', 'submittal-builder'), $count),
      'total' => 0,
      'viewed' => 0
    ]);
  }

  /** AJAX handler: Optimize database */
  function ajax_optimize_db() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_optimize_db');

    global $wpdb;

    // Get plugin tables
    $tables = [
      $wpdb->prefix . 'sfb_forms',
      $wpdb->prefix . 'sfb_nodes',
      $wpdb->prefix . 'sfb_leads',
      $wpdb->prefix . 'sfb_analytics_events',
    ];

    $optimized = 0;
    foreach ($tables as $table) {
      // Check if table exists
      $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
      if ($exists) {
        $wpdb->query("OPTIMIZE TABLE `{$table}`");
        $optimized++;
      }
    }

    wp_send_json_success([
      'message' => sprintf(__('✅ Optimized %d database tables', 'submittal-builder'), $optimized)
    ]);
  }

  /** AJAX handler: Clean orphaned data */
  function ajax_clean_orphans() {
    // Check permission
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => __('Unauthorized', 'submittal-builder')], 403);
    }

    // Verify nonce
    check_ajax_referer('sfb_clean_orphans');

    global $wpdb;

    $cleaned = 0;

    // Clean orphaned nodes (nodes without a form)
    $forms_table = $wpdb->prefix . 'sfb_forms';
    $nodes_table = $wpdb->prefix . 'sfb_nodes';

    $orphaned_nodes = $wpdb->query("
      DELETE n FROM {$nodes_table} n
      LEFT JOIN {$forms_table} f ON n.form_id = f.id
      WHERE f.id IS NULL
    ");
    $cleaned += (int) $orphaned_nodes;

    // Clean orphaned post meta (from deleted drafts)
    $orphaned_meta = $wpdb->query("
      DELETE pm FROM {$wpdb->postmeta} pm
      LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
      WHERE p.ID IS NULL
      AND pm.meta_key LIKE '_sfb_%'
    ");
    $cleaned += (int) $orphaned_meta;

    wp_send_json_success([
      'message' => sprintf(__('✅ Removed %d orphaned records', 'submittal-builder'), $cleaned)
    ]);
  }

  /**
   * AJAX: List products for frontend builder
   * Returns all products with basic info for the product picker
   */
  function ajax_list_products() {
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[SFB] ajax_list_products called');
    }

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sfb_frontend_builder')) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[SFB] Nonce verification failed');
      }
      wp_send_json_error(['message' => __('Invalid security token', 'submittal-builder')], 403);
      wp_die(); // Explicit die to ensure clean exit
    }

    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';

    // Check if table exists
    $table_exists = $wpdb->get_var($wpdb->prepare(
      "SHOW TABLES LIKE %s",
      $table
    ));

    if (!$table_exists) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[SFB] Table does not exist: ' . $table);
      }
      wp_send_json_error(['message' => __('Database table not found. Please reinstall the plugin.', 'submittal-builder')], 500);
      wp_die();
    }

    // Get all nodes for hierarchy resolution
    $all_nodes = $wpdb->get_results(
      "SELECT id, title, slug, parent_id, node_type, settings_json FROM {$table} ORDER BY position ASC",
      ARRAY_A
    );

    // Check for database errors
    if ($wpdb->last_error) {
      error_log('[SFB] Database error: ' . $wpdb->last_error);
      wp_send_json_error(['message' => __('Database error occurred', 'submittal-builder')], 500);
      wp_die();
    }

    // Build node lookup map
    $nodes_by_id = [];
    foreach ($all_nodes as $node) {
      $nodes_by_id[$node['id']] = $node;
    }

    // Process only model nodes with full lineage
    $formatted = [];
    $ordering = 0;

    foreach ($all_nodes as $node) {
      if ($node['node_type'] !== 'model') continue;

      $ordering++;
      $settings = !empty($node['settings_json']) ? json_decode($node['settings_json'], true) : [];
      $settings = is_array($settings) ? $settings : [];

      // Resolve full lineage: model -> type -> category
      $category = 'Uncategorized';
      $category_slug = 'uncategorized';
      $type_label = '';
      $type_key = '';
      $type_slug = '';

      if ($node['parent_id'] && isset($nodes_by_id[$node['parent_id']])) {
        $type_node = $nodes_by_id[$node['parent_id']];
        $type_label = $type_node['title'];
        $type_slug = $type_node['slug'] ?: sanitize_title($type_label);
        $type_key = 'T' . $type_node['id']; // Stable type key

        if ($type_node['parent_id'] && isset($nodes_by_id[$type_node['parent_id']])) {
          $category_node = $nodes_by_id[$type_node['parent_id']];
          $category = $category_node['title'];
          $category_slug = $category_node['slug'] ?: sanitize_title($category);
        }
      }

      // Extract specs from settings.fields
      $specs = [];
      if (isset($settings['fields']) && is_array($settings['fields'])) {
        $specs = $settings['fields'];
      }

      // Build model slug
      $model_slug = $node['slug'] ?: sanitize_title($node['title']);

      // Build composite_key (lowercase kebab)
      $composite_key = sanitize_key($category_slug) . ':' . sanitize_key($type_slug) . ':' . sanitize_key($model_slug);

      // Build search_tokens (model, type, category, spec values)
      $search_parts = [
        $node['title'],
        $type_label,
        $category,
      ];

      // Add spec values to search tokens
      if (!empty($specs)) {
        foreach ($specs as $key => $value) {
          $search_parts[] = $value;
          $search_parts[] = $key; // Include key names too
        }
      }

      $search_tokens = strtolower(implode(' ', array_filter($search_parts)));

      // Format response with full lineage
      $formatted[] = [
        'id' => (int) $node['id'],
        'model' => $node['title'],
        'specs' => empty($specs) ? new stdClass() : $specs,
        'category' => $category,
        'type_key' => $type_key,
        'type_label' => $type_label,
        'slug' => $model_slug,
        'composite_key' => $composite_key,
        'search_tokens' => $search_tokens,
        'ordering' => $ordering,
      ];
    }

    // Debug: Log first product with lineage
    if (defined('WP_DEBUG') && WP_DEBUG && !empty($formatted)) {
      error_log('[SFB] First product with lineage: ' . print_r($formatted[0], true));
    }

    error_log('[SFB] Returning ' . count($formatted) . ' products with lineage');

    wp_send_json_success(['products' => $formatted]);
    wp_die();
  }

  /**
   * AJAX: Generate PDF from frontend builder
   * Receives selected products and generates a submittal packet
   */
  function ajax_generate_frontend_pdf() {
    // Ensure no stray output corrupts JSON
    if (function_exists('ob_get_length') && ob_get_length()) {
      ob_clean();
    }

    try {
      // Verify nonce (using sfb_frontend_builder to match existing nonce)
      if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sfb_frontend_builder')) {
        wp_send_json_error(['message' => __('Invalid security token', 'submittal-builder')], 403);
      }

      // --- Check for new review payload format (includes quantities and notes) ---
      $review_raw = isset($_POST['review']) ? wp_unslash($_POST['review']) : '';
      $review = $review_raw ? json_decode($review_raw, true) : null;

      $project_name = '';
      $notes = '';
      $products = [];

      if ($review && is_array($review)) {
        // New format: review payload with quantities and notes
        $project_name = sanitize_text_field($review['project']['name'] ?? '');
        $notes = sanitize_textarea_field($review['project']['notes'] ?? '');
        $products = is_array($review['products'] ?? null) ? $review['products'] : [];

        error_log('[SFB] Using review payload format with ' . count($products) . ' products');
      } else {
        // Legacy format: direct products array
        $products_raw = isset($_POST['products']) ? wp_unslash($_POST['products']) : '[]';
        $products = json_decode($products_raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($products)) {
          error_log('[SFB] Invalid JSON in products payload: ' . json_last_error_msg());
          wp_send_json_error(['message' => __('Invalid products payload', 'submittal-builder')], 400);
        }

        $project_name = isset($_POST['project_name']) ? sanitize_text_field(wp_unslash($_POST['project_name'])) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';

        error_log('[SFB] Using legacy payload format with ' . count($products) . ' products');
      }

      if (empty($products)) {
        wp_send_json_error(['message' => __('No products selected', 'submittal-builder')], 400);
      }

      // --- Extract and validate product IDs safely ---
      $product_ids = [];
      foreach ($products as $p) {
        // Try multiple possible ID fields
        $id = null;
        if (isset($p['id']) && is_numeric($p['id'])) {
          $id = (int)$p['id'];
        } elseif (isset($p['product_id']) && is_numeric($p['product_id'])) {
          $id = (int)$p['product_id'];
        } elseif (isset($p['node_id']) && is_numeric($p['node_id'])) {
          $id = (int)$p['node_id'];
        }

        if ($id) {
          $product_ids[] = $id;
        } else {
          error_log('[SFB] Skipping product without valid ID: ' . print_r($p, true));
        }
      }

      if (empty($product_ids)) {
        wp_send_json_error(['message' => __('Invalid product data - no valid product IDs', 'submittal-builder')], 400);
      }

      // Load full product data from database
      global $wpdb;
      $table = $wpdb->prefix . 'sfb_nodes';
      $placeholders = implode(',', array_fill(0, count($product_ids), '%d'));
      $full_products = $wpdb->get_results(
        $wpdb->prepare(
          "SELECT * FROM {$table} WHERE id IN ($placeholders)",
          ...$product_ids
        ),
        ARRAY_A
      );

      if (empty($full_products)) {
        wp_send_json_error(['message' => __('No products found in database', 'submittal-builder')], 400);
      }

      // Create a map of product IDs to their quantity/note from review payload
      $product_meta_map = [];
      if ($review && is_array($review['products'] ?? null)) {
        foreach ($review['products'] as $rp) {
          $id = isset($rp['id']) ? (int)$rp['id'] : (isset($rp['node_id']) ? (int)$rp['node_id'] : 0);
          if ($id) {
            $product_meta_map[$id] = [
              'quantity' => isset($rp['quantity']) ? max(1, (int)$rp['quantity']) : 1,
              'note' => isset($rp['note']) ? sanitize_textarea_field($rp['note']) : ''
            ];
          }
        }
      }

      // Format products for PDF generator with safe array access
      $formatted_products = [];
      foreach ($full_products as $product) {
        $product_id = isset($product['id']) ? (int)$product['id'] : 0;

        // Safely decode the settings_json
        $settings_json = isset($product['settings_json']) ? $product['settings_json'] : '{}';
        $settings = json_decode($settings_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
          error_log('[SFB] Invalid JSON in product settings for ID ' . $product_id . ': ' . json_last_error_msg());
          $settings = [];
        }

        // Extract specs from settings
        $specs = isset($settings['fields']) ? $settings['fields'] : [];
        $base_note = isset($settings['note']) ? $settings['note'] : '';

        // Get quantity and note from review payload if available
        $quantity = 1;
        $note = $base_note;
        if (isset($product_meta_map[$product_id])) {
          $quantity = $product_meta_map[$product_id]['quantity'];
          // User note from review overrides base note if present
          if (!empty($product_meta_map[$product_id]['note'])) {
            $note = $product_meta_map[$product_id]['note'];
          }
        }

        // Get product path/category from parent hierarchy
        $path = $this->get_node_breadcrumb($product['id']);

        $formatted_products[] = [
          'id' => $product_id,
          'node_id' => $product_id,
          'name' => isset($product['title']) ? $product['title'] : __('Unnamed Product', 'submittal-builder'),
          'title' => isset($product['title']) ? $product['title'] : __('Unnamed Product', 'submittal-builder'),
          'category' => isset($path[0]) ? $path[0] : __('Uncategorized', 'submittal-builder'),
          'path' => $path,
          'specs' => $specs,
          'specifications' => $specs, // Alias for compatibility
          'note' => $note,
          'description' => $note, // Alias for compatibility
          'quantity' => $quantity,
        ];
      }

      // Get purchaser branding (always use site settings, no overrides)
      $settings = get_option('sfb_settings', []);

      // Agency - Phase B: Use default brand preset if enabled
      if (sfb_is_agency_license() && get_option('sfb_brand_use_default_on_pdf', false)) {
        $default_preset = SFB_Branding::get_default_preset();
        if ($default_preset && !empty($default_preset['data'])) {
          $settings = $default_preset['data'];
          error_log('[SFB] Using default brand preset: ' . $default_preset['name']);
        }
      }

      // --- Require composer autoload with guard ---
      $autoload = plugin_dir_path(__FILE__) . 'vendor/autoload.php';
      if (!file_exists($autoload)) {
        error_log('[SFB] Missing composer autoload at: ' . $autoload);
        wp_send_json_error(['message' => __('PDF engine not installed. Please run composer install.', 'submittal-builder')], 500);
      }
      require_once $autoload;

      // Generate PDF using existing generator
      require_once plugin_dir_path(__FILE__) . 'Includes/pdf-generator.php';

      $html = SFB_PDF_Generator::generate_packet([
        'products' => $formatted_products,
        'project_name' => $project_name,
        'project_notes' => $notes,
        'branding' => $settings,
        'pro_active' => sfb_is_pro_active(),
      ]);

      // Save PDF
      $upload_dir = wp_upload_dir();
      $sfb_dir = trailingslashit($upload_dir['basedir']) . 'sfb';

      if (!file_exists($sfb_dir)) {
        wp_mkdir_p($sfb_dir);
      }

      $filename = 'Submittal_' . sanitize_file_name($project_name ?: 'Packet') . '_' . date('Y-m-d') . '.pdf';
      $filepath = trailingslashit($sfb_dir) . $filename;

      // Configure Dompdf options
      $options = new \Dompdf\Options();
      $options->set('isRemoteEnabled', true);
      $options->set('isPhpEnabled', true); // Enable PHP scripts for page numbers

      // Enable HTML5 parser - the library is bundled in lib/masterminds/html5
      $options->set('isHtml5ParserEnabled', true);

      $options->set('defaultFont', 'Helvetica');
      $options->set('pdfBackend', 'CPDF');

      // Optional: Set chroot for security
      if (defined('WP_CONTENT_DIR')) {
        $options->setChroot(WP_CONTENT_DIR);
      }

      // Wrap Dompdf operations in try-catch to handle rendering errors gracefully
      try {
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        // Add page numbers using canvas - must be done BEFORE output
        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
          $font = $fontMetrics->getFont('helvetica', 'normal');
          $size = 9;
          $color = array(0.42, 0.45, 0.50);
          $linkColor = array(0.4, 0.49, 0.91); // Blue color for link

          $height = $canvas->get_height();
          $width = $canvas->get_width();

          // Footer text on left with clickable link
          $canvas->text(36, $height - 36, "Generated with ", $font, $size, $color);

          // Add clickable link
          $linkText = "Submittal & Spec Sheet Builder";
          $linkX = 36 + $fontMetrics->getTextWidth("Generated with ", $font, $size);
          $canvas->text($linkX, $height - 36, $linkText, $font, $size, $linkColor);

          // Add the actual hyperlink annotation
          $linkWidth = $fontMetrics->getTextWidth($linkText, $font, $size);
          $canvas->add_link("https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/", $linkX, $height - 36 - $size, $linkX + $linkWidth, $height - 36 + 2);

          // Page numbers on right
          $pageText = "Page " . $pageNumber . " of " . $pageCount;
          $canvas->text($width - 156, $height - 36, $pageText, $font, $size, $color);
        });

        $pdf_output = $dompdf->output();

        // Write PDF to file
        $bytes_written = file_put_contents($filepath, $pdf_output);

        if ($bytes_written === false) {
          throw new \Exception('Failed to write PDF file to: ' . $filepath);
        }

        // Verify file was created
        if (!file_exists($filepath)) {
          throw new \Exception('PDF file was not created at: ' . $filepath);
        }

        // Return URL
        $url = trailingslashit($upload_dir['baseurl']) . 'sfb/' . $filename;

        // Track PDF generation for Agency Analytics
        if (class_exists('SFB_Agency_Analytics')) {
          SFB_Agency_Analytics::track_pdf_generated($product_ids);
        }

        wp_send_json_success([
          'url' => $url,
          'filename' => $filename,
          'message' => __('PDF generated successfully', 'submittal-builder')
        ]);

      } catch (\Throwable $pdf_error) {
        error_log('[SFB] PDF rendering error: ' . $pdf_error->getMessage() . "\n" . $pdf_error->getTraceAsString());
        wp_send_json_error([
          'message' => sprintf(
            __('PDF engine error: %s', 'submittal-builder'),
            $pdf_error->getMessage()
          )
        ], 500);
      }

    } catch (\Throwable $e) {
      error_log('[SFB] PDF generation fatal error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
      wp_send_json_error(['message' => __('Server error during PDF generation. Please check the error log.', 'submittal-builder')], 500);
    }
  }

  /** AJAX handler for saving brand settings (Phase 7: delegates to SFB_Branding) */
  function ajax_save_brand() {
    // Clean output buffer to prevent stray output corrupting JSON
    if (function_exists('ob_get_length') && ob_get_length()) {
      ob_clean();
    }

    try {
      // Verify nonce
      if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'sfb_frontend')) {
        wp_send_json_error(['message' => __('Invalid security token', 'submittal-builder')], 403);
      }

      // Check capability
      if (!current_user_can('edit_sfb_branding')) {
        wp_send_json_error(['message' => __('You do not have permission to edit branding settings', 'submittal-builder')], 403);
      }

      // Get and decode data
      $data_raw = isset($_POST['data']) ? wp_unslash($_POST['data']) : '{}';
      $data = json_decode($data_raw, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[SFB] Invalid JSON in brand save: ' . json_last_error_msg());
        wp_send_json_error(['message' => __('Invalid JSON data', 'submittal-builder')], 400);
      }

      // Delegate to SFB_Branding class (Phase 7 refactor)
      $result = SFB_Branding::save($data);

      if ($result['success']) {
        wp_send_json_success([
          'saved' => $result['data']['saved'],
          'settings' => $result['data']['settings'],
          'message' => $result['message']
        ]);
      } else {
        wp_send_json_error(['message' => $result['message']], 500);
      }

    } catch (\Throwable $e) {
      error_log('[SFB] Brand save error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
      wp_send_json_error(['message' => __('Server error saving brand settings', 'submittal-builder')], 500);
    }
  }

  /** Agency feature: Export Pack as JSON download */
  function ajax_export_pack() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_die(__('Unauthorized.', 'submittal-builder'));
    }

    if (!sfb_is_agency_license()) {
      wp_die(__('Agency Packs require an Agency license.', 'submittal-builder'));
    }

    // Verify nonce
    $pack_id = isset($_GET['pack_id']) ? sanitize_text_field($_GET['pack_id']) : '';
    if (!$pack_id || !check_admin_referer('sfb_export_pack_' . $pack_id)) {
      wp_die(__('Invalid request.', 'submittal-builder'));
    }

    // Get pack
    $packs = get_option('sfb_agency_packs', []);
    $pack = null;
    foreach ($packs as $p) {
      if ($p['id'] === $pack_id) {
        $pack = $p;
        break;
      }
    }

    if (!$pack) {
      wp_die(__('Pack not found.', 'submittal-builder'));
    }

    // Prepare JSON
    $json = wp_json_encode($pack['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Send as download
    $filename = sanitize_file_name($pack['name']) . '.json';
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($json));
    echo $json;
    exit;
  }

  /** Generate test PDF (admin-post handler) */
  function generate_test_pdf() {
    // Verify nonce and capability
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'sfb_test_pdf')) {
      wp_die('Invalid nonce');
    }
    if (!current_user_can('manage_options')) {
      wp_die('Insufficient permissions');
    }

    // Delegate to wrapper
    $this->generate_pdf_packet();
  }

  /**
   * Wrapper used by the Welcome panel "Generate Test PDF" button.
   * Tries internal services if they exist; otherwise renders a minimal packet via DOMPDF (or HTML fallback).
   */
  public function generate_pdf_packet($data = []) {
    // 1) Security
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'sfb_test_pdf')) {
      wp_die(esc_html__('Invalid request (nonce).', 'submittal-builder'));
    }
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('Insufficient permissions.', 'submittal-builder'));
    }

    // 2) Get branding settings
    $branding      = get_option('sfb_branding', []);
    $company_name  = $branding['company_name'] ?? get_bloginfo('name');
    $primary_color = $branding['primary_color'] ?? '#111827';
    $logo_url      = $branding['logo_url'] ?? '';
    $today         = date('Y-m-d');
    $filename      = 'submittal-packet-' . date('Y-m-d') . '.pdf';

    // 3) Try to get real demo products from database, otherwise use hardcoded samples
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_items';
    $demo_products = [];

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
      $results = $wpdb->get_results(
        "SELECT title, cat_name, type_name, meta FROM $table
         WHERE demo = 1
         ORDER BY cat_name, type_name, title
         LIMIT 20",
        ARRAY_A
      );

      foreach ($results as $row) {
        $meta = !empty($row['meta']) ? json_decode($row['meta'], true) : [];
        $demo_products[] = [
          'title' => $row['title'],
          'path'  => array_filter([$row['cat_name'], $row['type_name']]),
          'specs' => is_array($meta) ? $meta : [],
          'note'  => 'Demo product from catalog',
        ];
      }
    }

    // Fallback to hardcoded samples if no demo products found
    $sample_products = !empty($demo_products) ? $demo_products : [
      [
        'title'      => 'Sample Stud 350S162-43',
        'path'       => ['Framing', 'Studs'],
        'specs'      => [
          'size'      => '3.5"',
          'flange'    => '1.625"',
          'thickness' => '43 mil (18 ga)',
          'ksi'       => '50 ksi',
        ],
        'note'       => 'Demonstration line item.',
      ],
      [
        'title'      => 'Sample Track 362T125-33',
        'path'       => ['Framing', 'Track'],
        'specs'      => [
          'size'      => '3.625"',
          'flange'    => '1.25"',
          'thickness' => '33 mil (20 ga)',
          'ksi'       => '33 ksi',
        ],
        'note'       => 'Demonstration line item.',
      ],
    ];

    // 3) Use the new professional PDF generator class
    if (class_exists('SFB_PDF_Generator')) {
      // Get paper size preference from settings
      $settings = get_option('sfb_settings', []);
      $paper_size = $settings['pdf_paper_size'] ?? 'letter';

      $html = SFB_PDF_Generator::generate_packet([
        'branding'     => $branding,
        'project_name' => 'Sample Project',
        'products'     => $sample_products,
        'pro_active'   => sfb_is_pro_active(),
        'paper_size'   => $paper_size,
      ]);

      // Debug: Add HTML comment to identify which generator was used
      $html = str_replace('<body>', '<body><!-- Generated by SFB_PDF_Generator class -->', $html);

      $this->stream_html_as_pdf_or_html_fallback($html, $filename);
      exit;
    }

    // 4) Fallback: render a minimal packet HTML directly (legacy compatibility)
    // If you see this in the PDF source, the new generator class is not loading!
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo esc_html($company_name); ?> — Submittal Packet</title>
        <style>
            @page {
                margin: 0.8in 0.6in 0.8in 0.6in;
                size: letter portrait;
            }
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
                font-size: 11pt;
                line-height: 1.5;
                color: #333;
            }
            .cover {
                page-break-after: always;
                position: relative;
                width: 210mm;
                height: 297mm;
                background: white;
            }
            .brand-bar {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 60mm;
                background: <?php echo esc_attr($primary_color); ?>;
            }
            .cover-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                width: 160mm;
            }
            .cover-logo {
                margin: 0 auto 30px;
                max-width: 180px;
                max-height: 140px;
                display: block;
            }
            .cover-title {
                font-size: 36pt;
                font-weight: 700;
                margin-bottom: 16px;
                color: #111;
                line-height: 1.2;
            }
            .cover-subtitle {
                font-size: 18pt;
                font-weight: 400;
                margin-bottom: 30px;
                color: #555;
                line-height: 1.4;
            }
            .cover-date {
                font-size: 13pt;
                color: #777;
                margin-top: 30px;
            }
            .section {
                margin: 20px 0 30px 0;
                page-break-inside: avoid;
            }
            h2 {
                font-size: 18pt;
                font-weight: 700;
                margin: 25px 0 15px;
                padding-bottom: 6px;
                border-bottom: 2px solid <?php echo esc_attr($primary_color); ?>;
                color: <?php echo esc_attr($primary_color); ?>;
                line-height: 1.3;
                page-break-after: avoid;
            }
            h3 {
                font-size: 14pt;
                font-weight: 600;
                margin: 20px 0 12px;
                color: <?php echo esc_attr($primary_color); ?>;
                line-height: 1.3;
                border-bottom: 2pt solid <?php echo esc_attr($primary_color); ?>;
                padding-bottom: 6pt;
                page-break-after: avoid;
            }
            .category-section {
                page-break-before: always;
            }
            .footer {
                position: fixed;
                bottom: 15mm;
                left: 20mm;
                right: 20mm;
                text-align: center;
                font-size: 9pt;
                color: #666;
                border-top: 1pt solid #e5e7eb;
                padding-top: 8pt;
            }
            p {
                margin: 8px 0;
                line-height: 1.6;
            }
            .summary-group {
                margin: 10px 0;
                padding: 10px 12px;
                background: #f5f5f5;
                border-left: 3px solid <?php echo esc_attr($primary_color); ?>;
            }
            .summary-group strong {
                color: <?php echo esc_attr($primary_color); ?>;
                font-size: 12pt;
            }
            .toc {
                page-break-after: always;
            }
            .toc ol {
                list-style: decimal;
                padding-left: 25px;
                margin-top: 15px;
            }
            .toc li {
                margin: 10px 0;
                line-height: 1.6;
                font-size: 11pt;
            }
            .toc a {
                color: #333;
                text-decoration: none;
                font-weight: 500;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 15px 0;
                font-size: 9.5pt;
                table-layout: fixed;
                page-break-inside: auto;
            }
            thead {
                display: table-header-group;
            }
            thead th {
                background: <?php echo esc_attr($primary_color); ?>;
                color: white;
                padding: 8px 6px;
                text-align: left;
                font-weight: 600;
                font-size: 10pt;
                line-height: 1.3;
                border: 1px solid <?php echo esc_attr($primary_color); ?>;
            }
            tbody {
                display: table-row-group;
            }
            tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            tbody td {
                border: 1px solid #ddd;
                padding: 7px 6px;
                vertical-align: top;
                line-height: 1.4;
                word-wrap: break-word;
            }
            tbody tr:nth-child(even) {
                background: #f9f9f9;
            }
            .spec-item {
                margin: 3px 0;
                font-size: 9pt;
                line-height: 1.4;
            }
            .spec-item strong {
                color: #555;
                font-weight: 600;
            }
            .path-breadcrumb {
                color: #666;
                font-size: 9pt;
                line-height: 1.3;
            }
            .note {
                color: #666;
                font-style: italic;
                font-size: 9pt;
                line-height: 1.3;
            }
            .pagebreak {
                page-break-after: always;
            }
            .small {
                font-size: 9.5pt;
                color: #666;
                line-height: 1.4;
            }
        </style>
    </head>
    <body>
        <!-- Cover Page -->
        <div class="cover">
            <div class="brand-bar"></div>
            <div class="cover-content">
                <?php if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company_name); ?> Logo" class="cover-logo" />
                <?php endif; ?>
                <div class="cover-title"><?php echo esc_html($company_name); ?></div>
                <div class="cover-subtitle"><?php esc_html_e('Submittal & Spec Sheet Packet', 'submittal-builder'); ?></div>
                <div class="cover-date">
                    <?php echo esc_html(date('F j, Y')); ?>
                </div>
            </div>
        </div>

        <!-- Footer (appears on all pages except cover) -->
        <div class="footer">
            Submittal Packet • <?php echo esc_html($company_name); ?> • Page <script type="text/php">
                if (isset($pdf)) {
                    echo $pdf->get_page_number() . " of " . $pdf->get_page_count();
                }
            </script>
        </div>

        <!-- Summary Section -->
        <div class="section summary">
            <h2><?php esc_html_e('Summary', 'submittal-builder'); ?></h2>
            <p class="small"><?php printf(esc_html__('This packet contains %d product(s) grouped by category.', 'submittal-builder'), count($sample_products)); ?></p>
            <?php
            $groups = [];
            foreach ($sample_products as $p) {
              $g = isset($p['path'][0]) ? $p['path'][0] : __('Uncategorized', 'submittal-builder');
              $groups[$g][] = $p;
            }
            foreach ($groups as $g => $items) {
              echo '<div class="summary-group">';
              echo '<strong>' . esc_html($g) . '</strong> — ' . sprintf(esc_html__('%d item(s)', 'submittal-builder'), count($items));
              echo '</div>';
            }
            ?>
        </div>

        <!-- Table of Contents -->
        <div class="section toc pagebreak">
            <h2><?php esc_html_e('Table of Contents', 'submittal-builder'); ?></h2>
            <ol>
                <li><a href="#summary"><?php esc_html_e('Summary', 'submittal-builder'); ?></a></li>
                <li><a href="#products"><?php esc_html_e('Product Specifications', 'submittal-builder'); ?></a></li>
            </ol>
            <p class="small"><?php esc_html_e('Note: Links are clickable in supported PDF viewers.', 'submittal-builder'); ?></p>
        </div>

        <!-- Products Section (Grouped by Category) -->
        <div id="products" class="section products">
            <h2><?php esc_html_e('Product Specifications', 'submittal-builder'); ?></h2>
            <?php
            // Group products by category
            $categories = [];
            foreach ($sample_products as $p) {
                $cat = isset($p['path'][0]) ? $p['path'][0] : __('Uncategorized', 'submittal-builder');
                $categories[$cat][] = $p;
            }

            $first_category = true;
            foreach ($categories as $category => $products) :
                // Add page break before each new category (except first)
                $section_class = $first_category ? '' : 'category-section';
                $first_category = false;
            ?>
                <div class="<?php echo esc_attr($section_class); ?>">
                    <h3><?php echo esc_html($category); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 35%;"><?php esc_html_e('Product', 'submittal-builder'); ?></th>
                                <th style="width: 45%;"><?php esc_html_e('Specifications', 'submittal-builder'); ?></th>
                                <th style="width: 20%;"><?php esc_html_e('Notes', 'submittal-builder'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $p) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($p['title']); ?></strong></td>
                                <td>
                                    <?php foreach ((array)($p['specs'] ?? []) as $k => $v) : ?>
                                        <div class="spec-item">
                                            <strong><?php echo esc_html(ucwords(str_replace('_', ' ', $k))); ?>:</strong>
                                            <?php echo esc_html($v); ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($p['specs'])): ?>
                                        <span class="small"><?php esc_html_e('No specifications available', 'submittal-builder'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="note"><?php echo esc_html($p['note'] ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $this->stream_html_as_pdf_or_html_fallback($html, $filename);
    exit;
  }

  /**
   * Utility: Stream HTML as PDF using bundled DOMPDF library.
   */
  private function stream_html_as_pdf_or_html_fallback($html, $filename = 'document.pdf') {
    // Load bundled DOMPDF library
    $dompdf_autoload = plugin_dir_path(__FILE__) . 'dompdf/autoload.inc.php';

    if (file_exists($dompdf_autoload)) {
      require_once $dompdf_autoload;
    }

    // Try to use DOMPDF
    if (class_exists('\Dompdf\Dompdf')) {
      try {
        // Get paper size preference from settings (default: letter)
        $settings = get_option('sfb_settings', []);
        $paper_size = $settings['pdf_paper_size'] ?? 'letter';

        $dompdf = new \Dompdf\Dompdf([
          'isRemoteEnabled'         => true,
          'isHtml5ParserEnabled'    => true, // Enabled - library bundled in lib/masterminds/html5
          'isFontSubsettingEnabled' => true,
          'chroot'                  => plugin_dir_path(__FILE__),
          'dpi'                     => 96,
          'defaultFont'             => 'DejaVu Sans',
          'fontHeightRatio'         => 1.1,
          'isPhpEnabled'            => true, // Enable PHP script for page numbers
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper_size, 'portrait');
        $dompdf->render();

        // Add page numbers using canvas - must be done BEFORE output
        $canvas = $dompdf->getCanvas();

        // Add footer text and page numbers on ALL pages
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
          $font = $fontMetrics->getFont('helvetica', 'normal');
          $size = 9;
          $color = array(0.42, 0.45, 0.50);
          $linkColor = array(0.4, 0.49, 0.91); // Blue color for link

          $height = $canvas->get_height();
          $width = $canvas->get_width();

          // Footer text on left with clickable link
          $canvas->text(36, $height - 36, "Generated with ", $font, $size, $color);

          // Add clickable link
          $linkText = "Submittal & Spec Sheet Builder";
          $linkX = 36 + $fontMetrics->getTextWidth("Generated with ", $font, $size);
          $canvas->text($linkX, $height - 36, $linkText, $font, $size, $linkColor);

          // Add the actual hyperlink annotation
          $linkWidth = $fontMetrics->getTextWidth($linkText, $font, $size);
          $canvas->add_link("https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/", $linkX, $height - 36 - $size, $linkX + $linkWidth, $height - 36 + 2);

          // Page numbers on right
          $pageText = "Page " . $pageNumber . " of " . $pageCount;
          $canvas->text($width - 156, $height - 36, $pageText, $font, $size, $color);
        });

        // Clear any previous output buffers to prevent whitespace issues
        while (ob_get_level()) {
          ob_end_clean();
        }

        // Set headers for PDF inline viewing/download
        nocache_headers();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . sanitize_file_name($filename) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        // Output PDF and exit cleanly
        echo $dompdf->output();
        exit;
      } catch (Exception $e) {
        // Log error and fall through to HTML preview
        error_log('SFB PDF Generation Error: ' . $e->getMessage());
        wp_die(
          '<h1>PDF Generation Error</h1>' .
          '<p>Failed to generate PDF: ' . esc_html($e->getMessage()) . '</p>' .
          '<p><a href="' . esc_url(admin_url('admin.php?page=sfb-onboarding')) . '">← Back to Welcome Page</a></p>',
          'PDF Error',
          ['response' => 500]
        );
      }
    }

    // DOMPDF library missing - show error
    wp_die(
      '<h1>PDF Library Missing</h1>' .
      '<p>The DOMPDF library is not properly installed in the plugin directory.</p>' .
      '<p>Expected location: <code>' . esc_html(plugin_dir_path(__FILE__) . 'dompdf/') . '</code></p>' .
      '<p><a href="' . esc_url(admin_url('admin.php?page=sfb-onboarding')) . '">← Back to Welcome Page</a></p>',
      'PDF Library Missing',
      ['response' => 500]
    );
  }

  /** Add plugin action links on plugins page */
  function plugin_action_links($links) {
    $custom_links = [
      '<a href="' . esc_url(add_query_arg('page', 'sfb', admin_url('admin.php'))) . '">Builder</a>',
      '<a href="' . esc_url(add_query_arg('page', 'sfb-branding', admin_url('admin.php'))) . '">Settings</a>',
      '<a href="' . esc_url(add_query_arg('page', 'sfb-upgrade', admin_url('admin.php'))) . '" style="color:#7c3aed;font-weight:600;">⭐ Upgrade to Pro</a>',
    ];
    return array_merge($custom_links, $links);
  }

  /** Add admin bar shortcut */
  function admin_bar_menu($wp_admin_bar) {
    // Only show for admins
    if (!current_user_can('manage_options')) return;

    // Parent menu item
    $wp_admin_bar->add_node([
      'id'    => 'sfb',
      'title' => '<span class="ab-icon dashicons-category"></span><span class="ab-label">SFB</span>',
      'href'  => add_query_arg('page', 'sfb', admin_url('admin.php')),
      'meta'  => ['title' => 'Submittal & Spec Builder'],
    ]);

    // Submenu: Builder
    $wp_admin_bar->add_node([
      'parent' => 'sfb',
      'id'     => 'sfb-builder',
      'title'  => 'Builder',
      'href'   => add_query_arg('page', 'sfb', admin_url('admin.php')),
    ]);

    // Submenu: Branding
    $wp_admin_bar->add_node([
      'parent' => 'sfb',
      'id'     => 'sfb-branding',
      'title'  => 'Branding',
      'href'   => add_query_arg('page', 'sfb-branding', admin_url('admin.php')),
    ]);

    // Submenu: Test PDF
    $wp_admin_bar->add_node([
      'parent' => 'sfb',
      'id'     => 'sfb-test-pdf',
      'title'  => '🧪 Test PDF',
      'href'   => wp_nonce_url(add_query_arg('action', 'sfb_test_pdf', admin_url('admin-post.php')), 'sfb_test_pdf'),
      'meta'   => ['title' => 'Generate a test PDF with sample data'],
    ]);

    // Submenu: Upgrade to Pro
    $wp_admin_bar->add_node([
      'parent' => 'sfb',
      'id'     => 'sfb-upgrade',
      'title'  => '⭐ Upgrade to Pro',
      'href'   => add_query_arg('page', 'sfb-upgrade', admin_url('admin.php')),
    ]);
  }

  /** Add help tabs to admin pages */
  function add_help_tabs() {
    $screen = get_current_screen();
    if (!$screen) return;

    // Overview tab
    $screen->add_help_tab([
      'id'      => 'sfb-overview',
      'title'   => 'Overview',
      'content' => '
        <h3>Submittal & Spec Builder Overview</h3>
        <p>This plugin helps you generate professional submittal and spec sheet PDFs with full branding.</p>
        <h4>Key Features:</h4>
        <ul>
          <li><strong>Product Catalog:</strong> Build a library of products with specs and notes</li>
          <li><strong>PDF Generation:</strong> Create branded packets with cover pages, summaries, and TOCs</li>
          <li><strong>Customization:</strong> Full branding control with colors, logos, and themes</li>
          <li><strong>Pro Features:</strong> Automation, tracking, white-label, and more</li>
        </ul>
      ',
    ]);

    // Getting Started tab
    $screen->add_help_tab([
      'id'      => 'sfb-getting-started',
      'title'   => 'Getting Started',
      'content' => '
        <h3>Getting Started</h3>
        <ol>
          <li><strong>Configure Branding:</strong> Go to <a href="' . esc_url(add_query_arg('page', 'sfb-branding', admin_url('admin.php'))) . '">Branding Settings</a> and add your company name, logo, and colors.</li>
          <li><strong>Build Product Catalog:</strong> Use the <a href="' . esc_url(add_query_arg('page', 'sfb', admin_url('admin.php'))) . '">Builder</a> to create categories and add products with specifications.</li>
          <li><strong>Add Shortcode:</strong> Insert <code>[submittal_builder]</code> on any page or post to display the submittal form.</li>
          <li><strong>Generate PDF:</strong> Clients select products and generate branded PDF packets.</li>
          <li><strong>Test PDF:</strong> Click the "Test PDF" button in the admin bar to preview with sample data.</li>
        </ol>
      ',
    ]);

    // Troubleshooting tab
    $screen->add_help_tab([
      'id'      => 'sfb-troubleshooting',
      'title'   => 'Troubleshooting',
      'content' => '
        <h3>Troubleshooting</h3>
        <h4>PDF Not Generating?</h4>
        <ul>
          <li>Check that Dompdf library is present in <code>lib/dompdf/</code></li>
          <li>Ensure upload directory is writable (check wp-content/uploads/sfb/)</li>
          <li>Check for PHP errors in debug.log (enable WP_DEBUG)</li>
        </ul>
        <h4>Branding Not Showing?</h4>
        <ul>
          <li>Verify logo URL is correct and accessible</li>
          <li>Check color codes are valid hex format (#000000)</li>
          <li>Clear browser cache and regenerate PDF</li>
        </ul>
        <h4>Pro Features Not Working?</h4>
        <ul>
          <li>Ensure license key is activated in Branding Settings</li>
          <li>Check that Pro registry is loaded (includes/pro/registry.php)</li>
        </ul>
      ',
    ]);

    // WordPress.org Resources tab
    $screen->add_help_tab([
      'id'      => 'sfb-resources',
      'title'   => 'Resources',
      'content' => '
        <h3>Resources</h3>
        <h4>Validation Tools:</h4>
        <ul>
          <li><a href="https://wordpress.org/plugins/developers/readme-validator/" target="_blank">readme.txt Validator</a> - Validate your readme.txt before submission</li>
          <li><a href="https://make.wordpress.org/plugins/handbook/" target="_blank">Plugin Handbook</a> - Official WordPress plugin development guide</li>
        </ul>
        <h4>Support:</h4>
        <ul>
          <li><a href="' . esc_url(add_query_arg('page', 'sfb-upgrade', admin_url('admin.php'))) . '">Upgrade to Pro</a> - Unlock advanced features</li>
          <li><a href="https://wordpress.org/support/" target="_blank">WordPress Support Forums</a></li>
        </ul>
        <h4>Developer Hooks:</h4>
        <ul>
          <li><code>sfb_features_map</code> - Register custom Pro features</li>
          <li><code>sfb_pro_changelog</code> - Add changelog entries</li>
          <li><code>sfb_branding_defaults</code> - Override branding defaults</li>
        </ul>
      ',
    ]);

    // Set sidebar
    $screen->set_help_sidebar('
      <p><strong>Submittal & Spec Builder v' . self::VERSION . '</strong></p>
      <p><a href="' . esc_url(add_query_arg('page', 'sfb', admin_url('admin.php'))) . '">Builder</a></p>
      <p><a href="' . esc_url(add_query_arg('page', 'sfb-branding', admin_url('admin.php'))) . '">Branding</a></p>
      <p><a href="' . esc_url(add_query_arg('page', 'sfb-upgrade', admin_url('admin.php'))) . '">Upgrade to Pro</a></p>
      <p><a href="' . esc_url(wp_nonce_url(add_query_arg('action', 'sfb_test_pdf', admin_url('admin-post.php')), 'sfb_test_pdf')) . '">Test PDF</a></p>
    ');
  }

  /**
   * Show Client Handoff Mode banner when active
   */
  function show_handoff_mode_banner() {
    // Only show for Agency license holders
    if (!sfb_is_agency_license()) {
      return;
    }

    // Only show if handoff mode is enabled
    if (!sfb_is_client_handoff_mode()) {
      return;
    }

    // Only show on plugin pages
    $screen = get_current_screen();
    if (!$screen || strpos($screen->base, 'sfb') === false) {
      return;
    }

    // Don't show on Agency page (they can see the toggle there)
    if (isset($_GET['page']) && $_GET['page'] === 'sfb-agency') {
      return;
    }

    ?>
    <div class="notice notice-info sfb-handoff-banner" style="display: flex; align-items: center; gap: 16px; padding: 12px 16px; border-left: 4px solid #0ea5e9; background: #f0f9ff;">
      <div style="flex: 1;">
        <p style="margin: 0; font-weight: 600; color: #0c4a6e;">
          🤝 <?php esc_html_e('Client Handoff Mode Active', 'submittal-builder'); ?>
        </p>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #0369a1;">
          <?php esc_html_e('Agency-specific features are currently hidden. All data is safe and can be restored instantly.', 'submittal-builder'); ?>
        </p>
      </div>
      <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-agency')); ?>"
         class="button button-primary"
         style="flex-shrink: 0;">
        <?php esc_html_e('Return to Agency Mode', 'submittal-builder'); ?>
      </a>
    </div>
    <?php
  }

  /**
   * Map custom SFB capabilities to WordPress primitives
   *
   * This enforces our custom capability system for the Operator role.
   * Custom caps: use_sfb_builder, view_sfb_leads, view_sfb_tracking,
   * edit_sfb_branding, edit_sfb_catalog, access_sfb_agency
   *
   * @param array  $caps    Required primitive capabilities
   * @param string $cap     Capability being checked
   * @param int    $user_id User ID
   * @param array  $args    Additional context
   * @return array Modified capabilities
   */
  function map_sfb_capabilities($caps, $cap, $user_id, $args) {
    // Define our custom SFB capabilities
    $custom_caps = [
      'use_sfb_builder',
      'view_sfb_leads',
      'view_sfb_tracking',
      'edit_sfb_branding',
      'edit_sfb_catalog',
      'access_sfb_agency',
    ];

    // Only handle our custom capabilities
    if (!in_array($cap, $custom_caps, true)) {
      return $caps;
    }

    // Get user
    $user = get_userdata($user_id);
    if (!$user || !$user->exists()) {
      return ['do_not_allow'];
    }

    // Check if user's role explicitly has this capability
    foreach ($user->roles as $role_name) {
      $role = get_role($role_name);
      if ($role && isset($role->capabilities[$cap])) {
        // If role explicitly grants or denies, respect that
        if ($role->capabilities[$cap]) {
          return ['exist']; // Grant capability
        } else {
          return ['do_not_allow']; // Deny capability
        }
      }
    }

    // Admins and editors always have access unless explicitly denied
    if ($user->has_cap('manage_options')) {
      return ['exist'];
    }

    // Default: deny
    return ['do_not_allow'];
  }

  /** Enqueue admin assets only on our pages */
  function enqueue_admin($hook) {
    // Get current screen for reliable detection
    $screen = get_current_screen();
    if (!$screen) return;

    // Check if we're on any of our plugin pages
    // Screen IDs typically look like: 'toplevel_page_sfb' or 'submittal-builder_page_sfb-onboarding'
    $is_sfb_page = (strpos($screen->id, 'sfb') !== false || strpos($screen->id, 'submittal-builder') !== false);
    if (!$is_sfb_page) return;

    // Also check page param as fallback
    $page = isset($_GET['page']) ? (string) sanitize_key($_GET['page']) : '';

    // WP deps (only on pages that need React)
    if (in_array($page, ['sfb', 'sfb-branding', 'sfb-agency'])) {
      wp_enqueue_script('wp-element');
      wp_enqueue_script('wp-api-fetch');
      wp_enqueue_media();
    }

    // Styles (shared across all admin pages) - use constant for reliable path
    wp_enqueue_style(
      'sfb-admin',
      plugins_url('assets/admin.css', SFB_PLUGIN_FILE),
      [],
      self::VERSION
    );

    // Single JS used for both pages
    wp_enqueue_script(
      'sfb-admin',
      plugins_url('assets/admin.js', __FILE__),
      ['wp-element','wp-api-fetch'],
      self::VERSION,
      true
    );

    $localized_data = [
      'restRoot'  => esc_url_raw( rest_url() ),
      'restNonce' => wp_create_nonce('wp_rest'),
      'ajax_url'  => admin_url('admin-ajax.php'),
      'nonce'     => wp_create_nonce('sfb_frontend'),
      'isAgency'  => sfb_is_agency_license(),
    ];

    // Add industry packs for builder page
    if ($page === 'sfb') {
      $localized_data['industryPacks'] = sfb_get_industry_packs();
      $localized_data['defaultIndustryPack'] = sfb_get_user_last_industry_pack();
    }

    // Add brand settings for branding page
    if ($page === 'sfb-branding') {
      $brand = sfb_get_brand_settings();

      // Ensure logo_url is populated if logo_id exists
      if (!empty($brand['company']['logo_id']) && empty($brand['company']['logo_url'])) {
        $logo_url = wp_get_attachment_image_url($brand['company']['logo_id'], 'full');
        if ($logo_url) {
          $brand['company']['logo_url'] = $logo_url;
        }
      }

      $localized_data['brand'] = $brand;
    }

    // Enqueue lead routing script on Agency page
    if ($page === 'sfb-agency' && sfb_is_agency_license()) {
      wp_enqueue_script(
        'sfb-lead-routing',
        plugins_url('assets/js/lead-routing.js', __FILE__),
        [],
        self::VERSION,
        true
      );
    }

    wp_localize_script('sfb-admin', 'SFB', $localized_data);
  }

  /** Enqueue frontend assets when shortcode is present (simple global load for now) */
  function enqueue_front() {
    wp_enqueue_style('sfb-front', plugins_url('assets/app.css', __FILE__), [], self::VERSION);
    wp_enqueue_script('sfb-front', plugins_url('assets/app.js', __FILE__), ['jquery'], self::VERSION, true);

    // Register new frontend builder assets
    wp_register_style('sfb-frontend', plugins_url('assets/css/frontend.css', __FILE__), [], self::VERSION);
    wp_register_script('sfb-frontend', plugins_url('assets/js/frontend.js', __FILE__), [], self::VERSION, true);

    // Register lead capture script (Pro feature - loaded when modal is present)
    wp_register_script('sfb-lead-capture', plugins_url('assets/js/lead-capture.js', __FILE__), ['sfb-frontend'], self::VERSION, true);
  }

  /** Shortcode: [submittal_builder id="1"] */
  function shortcode_render($atts = []) {
    $atts = shortcode_atts(['id' => 1], $atts, 'submittal_builder');
    $form_id = intval($atts['id']);

    // Load new frontend builder template
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/frontend/builder.php';
    return ob_get_clean();
  }

  /** Security check helper for write endpoints (DEPRECATED - use permission_callback instead) */
  private function check_write_permissions() {
    // Capability check only - REST nonce is handled by WP core via permission_callback
    if (!current_user_can('manage_options')) {
      return new WP_Error('forbidden', 'Insufficient permissions', ['status' => 403]);
    }

    return true;
  }

  /**
   * Return table names.
   */
  private function table_forms() { global $wpdb; return $wpdb->prefix . 'sfb_forms'; }
  private function table_nodes() { global $wpdb; return $wpdb->prefix . 'sfb_nodes'; }

  /**
   * Fetch all descendant IDs (depth-first) for a given form + seed IDs.
   *
   * @param int   $form_id
   * @param array $seed_ids
   * @return array unique list including the seeds and all descendants
   */
  private function fetch_descendant_ids( $form_id, array $seed_ids ) {
      global $wpdb;
      if ( empty( $seed_ids ) ) return [];

      $nodes_table = $this->table_nodes();
      $all = [];
      $queue = array_values( array_map( 'intval', $seed_ids ) );

      while ( ! empty( $queue ) ) {
          $batch = array_splice( $queue, 0, 100 ); // chunk
          $placeholders = implode( ',', array_fill( 0, count( $batch ), '%d' ) );
          $sql = $wpdb->prepare(
              "SELECT id FROM {$nodes_table} WHERE form_id=%d AND parent_id IN ($placeholders)",
              array_merge( [ $form_id ], $batch )
          );
          $children = $wpdb->get_col( $sql );

          // Accumulate
          foreach ( $batch as $id ) {
              $all[$id] = true;
          }
          if ( $children ) {
              foreach ( $children as $cid ) {
                  $cid = (int) $cid;
                  if ( ! isset( $all[$cid] ) ) {
                      $queue[] = $cid;
                  }
              }
          }
      }

      return array_map( 'intval', array_keys( $all ) );
  }

  /**
   * Delete a node and its entire subtree, returns array of deleted IDs (including the root).
   *
   * @param int $form_id
   * @param int $node_id
   * @return array
   */
  private function delete_node_recursive( $form_id, $node_id ) {
      global $wpdb;
      $node_id = (int) $node_id;
      if ( $node_id <= 0 ) return [];

      $nodes_table = $this->table_nodes();

      // seed = root; then add all descendants
      $to_delete = $this->fetch_descendant_ids( $form_id, [ $node_id ] );
      // Ensure the root is included even if it has no children
      if ( ! in_array( $node_id, $to_delete, true ) ) {
          $to_delete[] = $node_id;
      }

      if ( empty( $to_delete ) ) return [];

      // Delete in one statement
      $placeholders = implode( ',', array_fill( 0, count( $to_delete ), '%d' ) );
      $sql = $wpdb->prepare(
          "DELETE FROM {$nodes_table} WHERE form_id=%d AND id IN ($placeholders)",
          array_merge( [ $form_id ], $to_delete )
      );
      $wpdb->query( $sql );

      return $to_delete;
  }

  /**
   * Optional: Normalize positions for remaining siblings after deletion.
   */
  private function normalize_sibling_positions( $form_id, $parent_id ) {
      global $wpdb;
      $nodes_table = $this->table_nodes();
      $parent_id = $parent_id ? (int) $parent_id : 0;

      $rows = $wpdb->get_results( $wpdb->prepare(
          "SELECT id FROM {$nodes_table} WHERE form_id=%d AND parent_id=%d ORDER BY position ASC, id ASC",
          $form_id, $parent_id
      ), ARRAY_A );

      if ( empty( $rows ) ) return;

      $pos = 1;
      foreach ( $rows as $r ) {
          $wpdb->update(
              $nodes_table,
              [ 'position' => $pos++ ],
              [ 'id' => (int) $r['id'] ],
              [ '%d' ],
              [ '%d' ]
          );
      }
  }

  /** REST routes (stub for next step) */
  function register_routes() {
    register_rest_route('sfb/v1','/health',[
      'methods'=>'GET',
      'permission_callback'=>'__return_true',
      'callback'=>function(){ return ['ok'=>true,'version'=>self::VERSION]; }
    ]);

    // quick debug route to prove registration
    register_rest_route('sfb/v1','/ping',[
      'methods'=>'GET',
      'permission_callback'=>'__return_true',
      'callback'=>function(){ return ['ok'=>true,'pong'=>true]; }
    ]);

    register_rest_route('sfb/v1','/form/seed',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_seed_sample_catalog']
    ]);

    register_rest_route('sfb/v1','/form/wipe',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_wipe_form']
    ]);

    register_rest_route('sfb/v1','/form/(?P<id>\d+)',[
      'methods'=>'GET',
      'permission_callback'=>'__return_true',
      'callback'=>[$this,'api_get_form']
    ]);

    register_rest_route('sfb/v1','/node/save',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_save_node']
    ]);

    // Create node
    register_rest_route('sfb/v1','/node/create',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_create_node']
    ]);

    // Delete node (and descendants)
    register_rest_route('sfb/v1','/node/delete',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_delete_node']
    ]);

    // Reorder node (move up/down within parent)
    register_rest_route('sfb/v1','/node/reorder',[
      'methods'=>'POST',
      'permission_callback'=> function(){ return current_user_can('manage_options'); },
      'callback'=>[$this,'api_reorder_node']
    ]);

    // Duplicate node (and descendants)
    register_rest_route('sfb/v1','/node/duplicate', [
      'methods' => 'POST',
      'callback' => [$this,'api_duplicate_node'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);

    // Move node (drag & drop)
    register_rest_route('sfb/v1','/node/move', [
      'methods' => 'POST',
      'callback' => [$this,'api_move_node'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);

    // Node history
    register_rest_route('sfb/v1','/node/history', [
      'methods' => 'GET',
      'callback' => [$this,'api_node_history'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);

    // Bulk operations
    register_rest_route('sfb/v1','/bulk/delete', [
      'methods' => 'POST',
      'callback' => [$this,'api_bulk_delete'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);
    register_rest_route('sfb/v1','/bulk/move', [
      'methods' => 'POST',
      'callback' => [$this,'api_bulk_move'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);
    register_rest_route('sfb/v1','/bulk/duplicate', [
      'methods' => 'POST',
      'callback' => [$this,'api_bulk_duplicate'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);
    register_rest_route('sfb/v1','/bulk/export', [
      'methods' => 'POST',
      'callback' => [$this,'api_bulk_export'],
      'permission_callback' => function(){ return current_user_can('manage_options'); }
    ]);

    // Agency feature: Save Pack
    register_rest_route('sfb/v1','/pack/save', [
      'methods' => 'POST',
      'callback' => [$this,'api_save_pack'],
      'permission_callback' => function(){
        return current_user_can('manage_options') && sfb_is_agency_license();
      }
    ]);

    register_rest_route('sfb/v1','/generate',[
      'methods'  => 'POST',
      'permission_callback' => '__return_true', // public submission allowed
      'callback' => [$this,'api_generate_packet']
    ]);

    // Draft endpoints (Pro)
    register_rest_route('sfb/v1','/drafts',[
      'methods'  => 'POST',
      'permission_callback' => '__return_true', // public with nonce
      'callback' => [$this,'api_create_draft']
    ]);
    register_rest_route('sfb/v1','/drafts/(?P<id>[A-Za-z0-9_-]{6,36})',[
      'methods'  => 'GET',
      'permission_callback' => '__return_true', // public read by ID
      'callback' => [$this,'api_get_draft']
    ]);
    register_rest_route('sfb/v1','/drafts/(?P<id>[A-Za-z0-9_-]{6,36})',[
      'methods'  => 'PUT',
      'permission_callback' => '__return_true', // public with nonce
      'callback' => [$this,'api_update_draft']
    ]);

    register_rest_route('sfb/v1','/settings',[
      'methods' => 'GET',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_get_settings']
    ]);
    register_rest_route('sfb/v1','/settings',[
      'methods' => 'POST',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_save_settings']
    ]);

    // Status endpoint (public)
    register_rest_route('sfb/v1','/status',[
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [$this,'api_get_status']
    ]);

    // License management
    register_rest_route('sfb/v1','/license',[
      'methods' => 'GET',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_get_license']
    ]);
    register_rest_route('sfb/v1','/license',[
      'methods' => 'POST',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_save_license']
    ]);

    // Export form
    register_rest_route('sfb/v1','/form/(?P<id>\d+)/export',[
      'methods' => 'GET',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_export_form']
    ]);

    // Import form
    register_rest_route('sfb/v1','/form/import',[
      'methods' => 'POST',
      'permission_callback' => function(){ return current_user_can('manage_options'); },
      'callback' => [$this,'api_import_form']
    ]);
  }

  /** Seed a sample catalog with configurable size and mode */
  function api_seed_sample_catalog($req){
    global $wpdb;
    $form_id = (int) ($req->get_param('form_id') ?: 1);
    $mode = $req->get_param('mode') ?: 'merge';
    $size = $req->get_param('size') ?: 'medium';
    $with_branding = $req->get_param('with_branding');
    $with_branding = is_null($with_branding) ? true : (bool)$with_branding;

    // Agency feature: Check if loading from Agency Pack
    $agency_pack_id = $req->get_param('agency_pack_id');

    // Get industry pack parameter (defaults to user's last or default pack)
    $industry_pack = $req->get_param('industry_pack');
    if (!$industry_pack && !$agency_pack_id) {
      $industry_pack = sfb_get_user_last_industry_pack();
    }

    // Save user's last selected pack for future (only for industry packs)
    if ($industry_pack) {
      sfb_save_user_last_industry_pack($industry_pack);
    }

    // Start timer
    $t0 = microtime(true);

    // Ensure tables exist
    $this->ensure_tables();

    $nodes_table = $wpdb->prefix . 'sfb_nodes';
    $forms_table = $wpdb->prefix . 'sfb_forms';

    // Replace mode: wipe existing nodes for this form
    if ($mode === 'replace') {
      $wpdb->query( $wpdb->prepare("DELETE FROM {$nodes_table} WHERE form_id=%d", $form_id) );
      // Ensure a forms row exists
      $exists = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$forms_table} WHERE id=%d", $form_id) );
      if (!$exists) {
        $wpdb->insert($forms_table, [ 'id'=>$form_id, 'title'=>'Sample Submittal Catalog' ], ['%d','%s']);
      }
    }

    // Load pack data (Agency Pack or Industry Pack)
    if ($agency_pack_id) {
      // Load from Agency Pack - use direct node import
      $packs = get_option('sfb_agency_packs', []);
      $agency_pack = null;
      foreach ($packs as $p) {
        if ($p['id'] === $agency_pack_id) {
          $agency_pack = $p;
          break;
        }
      }

      if (!$agency_pack) {
        return new WP_Error('pack_not_found', __('Agency Pack not found', 'submittal-builder'), ['status' => 404]);
      }

      $pack_data = $agency_pack['data'];

      // If agency pack has branding, apply it (if with_branding is true)
      if ($with_branding && isset($pack_data['branding'])) {
        update_option('sfb_brand_settings', $pack_data['branding'], false);
      }

      // Agency packs store raw nodes - import them directly
      if (isset($pack_data['nodes']) && is_array($pack_data['nodes'])) {
        $nodes = $pack_data['nodes'];
        $id_map = [];
        $counts = ['categories' => 0, 'products' => 0, 'types' => 0, 'models' => 0];

        foreach ($nodes as $node) {
          $old_id = $node['id'];
          $old_parent = $node['parent_id'] ?? null;

          // Map parent ID
          $new_parent = null;
          if ($old_parent && isset($id_map[$old_parent])) {
            $new_parent = $id_map[$old_parent];
          }

          // Insert node
          $wpdb->insert($nodes_table, [
            'form_id' => $form_id,
            'parent_id' => $new_parent,
            'node_type' => $node['node_type'],
            'title' => $node['title'],
            'slug' => $node['slug'] ?? sanitize_title($node['title']),
            'position' => $node['position'] ?? 0,
            'settings_json' => is_array($node['settings']) ? json_encode($node['settings']) : ($node['settings_json'] ?? '{}')
          ]);

          $new_id = $wpdb->insert_id;
          $id_map[$old_id] = $new_id;

          // Count by type
          if ($node['node_type'] === 'category') $counts['categories']++;
          elseif ($node['node_type'] === 'product') $counts['products']++;
          elseif ($node['node_type'] === 'type') $counts['types']++;
          elseif ($node['node_type'] === 'model') $counts['models']++;
        }

        $elapsed = round((microtime(true) - $t0) * 1000);

        return [
          'ok' => true,
          'counts' => $counts,
          'elapsed_ms' => $elapsed,
          'pack_type' => 'agency'
        ];
      }

      return new WP_Error('invalid_pack', __('Invalid Agency Pack format', 'submittal-builder'), ['status' => 500]);
    }

    // Load from industry pack JSON
    $json_file = plugin_dir_path(__FILE__) . 'assets/demo/' . $industry_pack . '.json';
    if (!file_exists($json_file)) {
      return new WP_Error('pack_not_found', __('Industry pack not found', 'submittal-builder'), ['status' => 404]);
    }

    $pack_data = json_decode(file_get_contents($json_file), true);

    if (!$pack_data || !isset($pack_data['categories'])) {
      return new WP_Error('invalid_pack', __('Invalid pack format', 'submittal-builder'), ['status' => 500]);
    }

    // Size limits for how many categories/types/items to load
    $size_limits = [
      'small'  => ['max_categories' => 2, 'max_types' => 2, 'max_items' => 2, 'multiply' => 1],
      'medium' => ['max_categories' => 999, 'max_types' => 999, 'max_items' => 999, 'multiply' => 1], // Load all
      'large'  => ['max_categories' => 999, 'max_types' => 999, 'max_items' => 999, 'multiply' => 100],  // Load all + multiply by 100
    ][$size] ?? ['max_categories' => 999, 'max_types' => 999, 'max_items' => 999, 'multiply' => 1];

    // Seed from industry pack JSON
    $stats = ['categories' => 0, 'types' => 0, 'models' => 0];
    $position = 0;
    $category_count = 0;

    foreach ($pack_data['categories'] as $cat_data) {
      // Apply size limit for categories
      if ($category_count >= $size_limits['max_categories']) {
        break;
      }
      $category_count++;

      $cat_slug = sanitize_title($cat_data['title']);

      // In merge mode, check if category already exists
      $cat_id = null;
      if ($mode === 'merge') {
        $cat_id = $wpdb->get_var($wpdb->prepare(
          "SELECT id FROM {$nodes_table} WHERE form_id = %d AND node_type = 'category' AND slug = %s LIMIT 1",
          $form_id,
          $cat_slug
        ));
      }

      if (!$cat_id) {
        $wpdb->insert($nodes_table, [
          'form_id' => $form_id,
          'parent_id' => null,
          'node_type' => 'category',
          'title' => $cat_data['title'],
          'slug' => $cat_slug,
          'position' => $position++,
          'settings_json' => json_encode(['_demo_seed' => 1, '_demo_pack' => $industry_pack])
        ]);
        $cat_id = $wpdb->insert_id;
        $stats['categories']++;
      }

      // Insert types
      if (isset($cat_data['types']) && is_array($cat_data['types'])) {
        $type_pos = 0;
        $type_count = 0;

        foreach ($cat_data['types'] as $type_data) {
          // Apply size limit for types
          if ($type_count >= $size_limits['max_types']) {
            break;
          }
          $type_count++;

          $type_slug = sanitize_title($type_data['title']);

          // In merge mode, check if type already exists
          $type_id = null;
          if ($mode === 'merge') {
            $type_id = $wpdb->get_var($wpdb->prepare(
              "SELECT id FROM {$nodes_table} WHERE form_id = %d AND parent_id = %d AND node_type = 'type' AND slug = %s LIMIT 1",
              $form_id,
              $cat_id,
              $type_slug
            ));
          }

          if (!$type_id) {
            $wpdb->insert($nodes_table, [
              'form_id' => $form_id,
              'parent_id' => $cat_id,
              'node_type' => 'type',
              'title' => $type_data['title'],
              'slug' => $type_slug,
              'position' => $type_pos++,
              'settings_json' => json_encode(['_demo_seed' => 1, '_demo_pack' => $industry_pack])
            ]);
            $type_id = $wpdb->insert_id;
            $stats['types']++;
          }

          // Insert items (models)
          if (isset($type_data['items']) && is_array($type_data['items'])) {
            $item_pos = 0;
            $item_count = 0;

            foreach ($type_data['items'] as $item_data) {
              // Apply size limit for items
              if ($item_count >= $size_limits['max_items']) {
                break;
              }
              $item_count++;

              // For 'large' size, multiply items to generate more test data
              $multiply_count = $size_limits['multiply'];

              for ($variant = 1; $variant <= $multiply_count; $variant++) {
                // Base title and slug
                $base_title = $item_data['title'];
                $title = ($multiply_count > 1) ? $base_title . ' #' . $variant : $base_title;
                $item_slug = sanitize_title($title);

                // In merge mode, check if item already exists
                if ($mode === 'merge') {
                  $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$nodes_table} WHERE form_id = %d AND parent_id = %d AND node_type = 'model' AND slug = %s LIMIT 1",
                    $form_id,
                    $type_id,
                    $item_slug
                  ));

                  if ($exists) {
                    continue;
                  }
                }

                // Build settings_json with meta fields
                $settings = [
                  '_demo_seed' => 1,
                  '_demo_pack' => $industry_pack
                ];

                if (isset($item_data['meta']) && is_array($item_data['meta'])) {
                  // For variants, add slight variation to numeric fields
                  $fields = $item_data['meta'];
                  if ($multiply_count > 1 && $variant > 1) {
                    // Add variant number to make it unique
                    foreach ($fields as $key => $value) {
                      // Add variant indicator to fields that look like numbers
                      if (is_numeric(str_replace(['"', 'mil', 'lb/ft', 'x'], '', $value))) {
                        $fields[$key] = $value . ' (v' . $variant . ')';
                      }
                    }
                  }
                  $settings['fields'] = $fields;
                }

                $wpdb->insert($nodes_table, [
                  'form_id' => $form_id,
                  'parent_id' => $type_id,
                  'node_type' => 'model',
                  'title' => $title,
                  'slug' => $item_slug,
                  'position' => $item_pos++,
                  'settings_json' => json_encode($settings, JSON_UNESCAPED_SLASHES)
                ]);

                $stats['models']++;
              }
            }
          }
        }
      }
    }

    // Branding
    if ( $with_branding ) {
      $branding = [
        'company_name' => 'Acme Framing Supply',
        'address_lines' => ['123 Steel Ave', 'Suite 400', 'Metropolis, NY 10001'],
        'phone' => '(212) 555-0199',
        'email' => 'sales@acmeframing.example',
        'logo_url' => 'https://dummyimage.com/300x80/111827/ffffff&text=ACME+FRAMING',
        'primary_color' => '#0ea5e9',
        'footer_note' => 'Submittal packet auto-generated for demonstration.'
      ];
      update_option('sfb_branding', $branding, false);
    }

    $elapsed = round( (microtime(true)-$t0)*1000 );

    return [
      'ok' => true,
      'form_id' => $form_id,
      'size' => $size,
      'mode' => $mode,
      'industry_pack' => $industry_pack,
      'branding' => $with_branding,
      'elapsed_ms' => $elapsed,
      'counts' => [
        'categories' => $stats['categories'],
        'products' => 0, // No separate product level in new schema
        'types' => $stats['types'],
        'models' => $stats['models'],
      ],
    ];
  }

  /** Wipe all nodes for a form (with optional backup) */
  public function api_wipe_form( WP_REST_Request $req ) {
    // Prevent any stray echoes/notices from breaking JSON
    ob_start();
    try {
        global $wpdb;

        $form_id       = (int) ($req->get_param('form_id') ?: 1);
        $with_branding = (bool) $req->get_param('with_branding');
        $make_backup   = (bool) $req->get_param('make_backup');

        $this->ensure_tables();
        $nodes_table = $this->table_nodes();

        $backup_url = null;

        if ( $make_backup ) {
            // Collect rows first
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT id,parent_id,node_type,title,position,fields_json
                 FROM {$nodes_table}
                 WHERE form_id=%d
                 ORDER BY parent_id ASC, position ASC, id ASC",
                $form_id
            ), ARRAY_A );

            $branding = get_option('sfb_branding', null);
            $backup = [
                'version'     => 1,
                'exported_at' => gmdate('c'),
                'form_id'     => $form_id,
                'branding'    => $branding,
                'nodes'       => $rows ?: [],
            ];

            // Try writing to uploads; handle failures gracefully
            $upload_dir = wp_upload_dir();
            if ( empty($upload_dir['error']) ) {
                $dir = trailingslashit($upload_dir['basedir']) . 'sfb-backups';
                if ( ! file_exists($dir) ) {
                    // suppress warnings but check result
                    if ( ! wp_mkdir_p($dir) ) {
                        error_log('SFB wipe: failed to create backup dir: ' . $dir);
                    }
                }
                if ( is_dir($dir) && is_writable($dir) ) {
                    $fname = 'sfb-backup-form' . $form_id . '-' . time() . '.json';
                    $fpath = trailingslashit($dir) . $fname;
                    $json  = wp_json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                    if ( $json !== false && file_put_contents($fpath, $json) !== false ) {
                        $backup_url = trailingslashit($upload_dir['baseurl']) . 'sfb-backups/' . $fname;
                    } else {
                        error_log('SFB wipe: failed to write backup file: ' . $fpath);
                    }
                }
            } else {
                error_log('SFB wipe: upload_dir error: ' . $upload_dir['error']);
            }
        }

        // Count then delete
        $count_nodes = (int) $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$nodes_table} WHERE form_id=%d", $form_id) );
        $wpdb->query( $wpdb->prepare("DELETE FROM {$nodes_table} WHERE form_id=%d", $form_id) );

        if ( $with_branding ) {
            delete_option('sfb_branding');
        }

        $payload = [
            'ok'      => true,
            'form_id' => $form_id,
            'deleted' => [
                'nodes' => $count_nodes,
                'branding_cleared' => (bool) $with_branding,
            ],
            'backup_url' => $backup_url,
        ];

        // Clean any buffered output (don't include it in response)
        ob_end_clean();
        return rest_ensure_response( $payload );

    } catch ( \Throwable $e ) {
        $junk = ob_get_clean(); // discard any output
        error_log('SFB api_wipe_form fatal: ' . $e->getMessage());
        return new WP_Error(
            'sfb_wipe_failed',
            'Delete All failed on the server. Check debug.log for details.',
            [ 'status' => 500 ]
        );
    }
  }

  // Phase 3 Refactor: api_get_form() moved to SFB_Rest::get_form()
  // Phase 4 Refactor: Catalog write handlers moved to SFB_Rest class
  // - api_save_node() → SFB_Rest::save_node()
  // - api_create_node() → SFB_Rest::create_node()
  // - api_delete_node() → SFB_Rest::delete_node()
  // - api_reorder_node() → SFB_Rest::reorder_node()
  // - api_duplicate_node() → SFB_Rest::duplicate_node()
  // - api_move_node() → SFB_Rest::move_node()
  // Note: clone_node_recursive() helper still used by duplicate_node()

  // Phase 3 Refactor: api_node_history() moved to SFB_Rest::get_node_history()

  /** Bulk delete nodes (recursively deletes children) */
  function api_bulk_delete($req){
    try {
      $this->ensure_tables();
      global $wpdb;

      $p = $req->get_json_params();
      $form_id = intval($p['form_id'] ?? 1);
      $ids = $p['ids'] ?? [];

      if (!is_array($ids) || empty($ids)) {
        return new WP_Error('bad_request', 'Missing ids array', ['status' => 400]);
      }

      $nodes_table = $this->table_nodes();

      // Track parents to reindex afterwards
      $parents = [];
      foreach ( $ids as $id ) {
        $pid = $wpdb->get_var( $wpdb->prepare(
          "SELECT parent_id FROM {$nodes_table} WHERE form_id=%d AND id=%d",
          $form_id, (int) $id
        ) );
        if ( $pid !== null ) $parents[] = (int) $pid;
      }

      $all_deleted = [];
      foreach ( $ids as $id ) {
        $deleted = $this->delete_node_recursive( $form_id, (int) $id );
        $all_deleted = array_merge( $all_deleted, $deleted );
      }
      $all_deleted = array_values( array_unique( array_map( 'intval', $all_deleted ) ) );

      // Reindex siblings for each affected parent
      $parents = array_values( array_unique( $parents ) );
      foreach ( $parents as $p ) {
        $this->normalize_sibling_positions( $form_id, $p );
      }

      return [
        'ok'          => true,
        'deleted_ids' => $all_deleted,
      ];
    } catch (\Throwable $e) {
      error_log('SFB api_bulk_delete error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }

  /** Bulk move nodes to a new parent */
  function api_bulk_move($req){
    try {
      $this->ensure_tables();
      global $wpdb;

      $p = $req->get_json_params();
      $ids = $p['ids'] ?? [];
      $parent_id = intval($p['parent_id'] ?? 0);

      if (!is_array($ids) || empty($ids)) {
        return new WP_Error('bad_request', 'Missing ids array', ['status' => 400]);
      }

      $table = $wpdb->prefix . 'sfb_nodes';

      // Get next position for new parent
      $max_pos = intval($wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(MAX(position),0) FROM $table WHERE parent_id=%d",
        $parent_id
      )));

      $pos = $max_pos + 10;
      foreach($ids as $id){
        $wpdb->update($table, [
          'parent_id' => $parent_id,
          'position' => $pos
        ], ['id' => intval($id)]);
        $pos += 10;
      }

      // Normalize positions
      $siblings = $wpdb->get_results($wpdb->prepare(
        "SELECT id FROM $table WHERE parent_id=%d ORDER BY position ASC",
        $parent_id
      ), ARRAY_A);

      $normalize_pos = 1;
      foreach($siblings as $sib){
        $wpdb->update($table, ['position' => $normalize_pos++], ['id' => $sib['id']]);
      }

      return ['ok' => true];
    } catch (\Throwable $e) {
      error_log('SFB api_bulk_move error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }

  /** Bulk duplicate nodes */
  function api_bulk_duplicate($req){
    try {
      $this->ensure_tables();
      global $wpdb;

      $p = $req->get_json_params();
      $ids = $p['ids'] ?? [];

      if (!is_array($ids) || empty($ids)) {
        return new WP_Error('bad_request', 'Missing ids array', ['status' => 400]);
      }

      $table = $wpdb->prefix . 'sfb_nodes';
      $new_ids = [];

      foreach($ids as $id){
        $orig = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", intval($id)), ARRAY_A);
        if (!$orig) continue;

        $next_pos = intval($wpdb->get_var($wpdb->prepare(
          "SELECT COALESCE(MAX(position),0)+1 FROM $table WHERE parent_id=%d AND form_id=%d",
          $orig['parent_id'], $orig['form_id']
        )));

        $map = [];
        $new_id = $this->clone_node_recursive($orig['id'], $orig['parent_id'], $next_pos, $map);
        $wpdb->update($table, ['title' => $orig['title'].' (copy)'], ['id' => $new_id]);
        $new_ids[] = $new_id;
      }

      return ['ok' => true, 'new_ids' => $new_ids];
    } catch (\Throwable $e) {
      error_log('SFB api_bulk_duplicate error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }

  /** Bulk export nodes as JSON */
  function api_bulk_export($req){
    try {
      $this->ensure_tables();
      global $wpdb;

      $p = $req->get_json_params();
      $ids = $p['ids'] ?? [];

      if (!is_array($ids) || empty($ids)) {
        return new WP_Error('bad_request', 'Missing ids array', ['status' => 400]);
      }

      $table = $wpdb->prefix . 'sfb_nodes';
      $nodes = [];

      foreach($ids as $id){
        $node = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", intval($id)), ARRAY_A);
        if ($node) {
          if ($node['settings_json']) {
            $node['settings'] = json_decode($node['settings_json'], true);
            unset($node['settings_json']);
          }
          $nodes[] = $node;
        }
      }

      return ['ok' => true, 'data' => $nodes];
    } catch (\Throwable $e) {
      error_log('SFB api_bulk_export error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }

  /** Agency feature: Save current catalog as reusable Pack */
  function api_save_pack($req){
    try {
      $this->ensure_tables();
      global $wpdb;

      $p = $req->get_json_params();
      $name = sanitize_text_field($p['name'] ?? '');
      $include_branding = !empty($p['include_branding']);
      $include_notes = !empty($p['include_notes']);

      if (empty($name)) {
        return new WP_Error('bad_request', 'Pack name is required', ['status' => 400]);
      }

      // Get all nodes for form 1 (main catalog)
      $nodes_table = $wpdb->prefix . 'sfb_nodes';
      $all_nodes = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $nodes_table WHERE form_id=%d ORDER BY position ASC", 1
      ), ARRAY_A);

      // Parse settings_json and optionally strip notes
      $product_count = 0;
      foreach($all_nodes as &$node){
        $node['settings'] = json_decode($node['settings_json'] ?? '{}', true);
        unset($node['settings_json']);

        // Count products
        if (in_array($node['node_type'], ['product', 'type', 'model'], true)) {
          $product_count++;
        }

        // Strip notes if not including
        if (!$include_notes && isset($node['settings']['note'])) {
          unset($node['settings']['note']);
        }
      }
      unset($node);

      // Build Pack data
      $pack_data = [
        'form' => ['id' => 1, 'title' => 'Submittal Form 1'],
        'nodes' => $all_nodes
      ];

      // Include branding if requested
      if ($include_branding) {
        $pack_data['branding'] = sfb_get_brand_settings();
      }

      // Create Pack record
      $pack = [
        'id' => wp_generate_uuid4(),
        'name' => $name,
        'counts' => [
          'products' => $product_count,
          'nodes' => count($all_nodes)
        ],
        'has_branding' => $include_branding,
        'updated_at' => current_time('mysql'),
        'data' => $pack_data
      ];

      // Save to database
      $packs = get_option('sfb_agency_packs', []);
      $packs[] = $pack;
      update_option('sfb_agency_packs', $packs, false);

      error_log('[SFB] Agency Pack created: ' . $name . ' (' . $product_count . ' products)');

      // Return without full data (too large)
      unset($pack['data']);

      return [
        'ok' => true,
        'pack' => $pack
      ];
    } catch (\Throwable $e) {
      error_log('SFB api_save_pack error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /** Export form and all nodes as JSON */
  function api_export_form($req){
    try {
      $this->ensure_tables();
      $form_id = intval($req->get_param('id'));
      if (!$form_id) return new WP_Error('bad_request','Missing form_id', ['status'=>400]);

      global $wpdb;
      $nodes = $wpdb->prefix . 'sfb_nodes';

      // Get all nodes for this form
      $all_nodes = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $nodes WHERE form_id=%d ORDER BY position ASC", $form_id
      ), ARRAY_A);

      // Parse settings_json
      foreach($all_nodes as &$node){
        $node['settings'] = json_decode($node['settings_json'] ?? '{}', true);
        unset($node['settings_json']);
      }

      return [
        'ok' => true,
        'form' => ['id' => $form_id, 'title' => 'Submittal Form ' . $form_id],
        'nodes' => $all_nodes
      ];
    } catch (\Throwable $e) {
      error_log('SFB api_export_form error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /** Import nodes (append or replace) */
  function api_import_form($req){
    try {
      $this->ensure_tables();
      $p = $req->get_json_params();
      $form_id = intval($p['form_id'] ?? 1);
      $mode = sanitize_text_field($p['mode'] ?? 'append'); // 'append' or 'replace'
      $nodes = $p['nodes'] ?? [];

      if (!is_array($nodes) || empty($nodes)) {
        return new WP_Error('bad_request','No nodes provided', ['status'=>400]);
      }

      global $wpdb;
      $table = $wpdb->prefix . 'sfb_nodes';

      // Replace mode: delete existing nodes
      if ($mode === 'replace') {
        $wpdb->delete($table, ['form_id' => $form_id]);
      }

      // Map old IDs to new IDs
      $id_map = [];

      // If appending, find max position for root nodes
      $max_pos = 0;
      if ($mode === 'append') {
        $max_pos = intval($wpdb->get_var($wpdb->prepare(
          "SELECT COALESCE(MAX(position),0) FROM $table WHERE form_id=%d AND parent_id=0", $form_id
        )));
      }

      // Insert nodes in order, mapping parent_id
      foreach($nodes as $node){
        $old_id = intval($node['id']);
        $parent_id = intval($node['parent_id'] ?? 0);

        // Map parent_id if it was already imported
        if ($parent_id > 0 && isset($id_map[$parent_id])) {
          $parent_id = $id_map[$parent_id];
        }

        // Adjust position for root nodes in append mode
        $position = intval($node['position'] ?? 0);
        if ($parent_id === 0 && $mode === 'append') {
          $position += $max_pos;
        }

        $settings = $node['settings'] ?? [];

        $wpdb->insert($table, [
          'form_id' => $form_id,
          'parent_id' => $parent_id,
          'node_type' => sanitize_text_field($node['node_type'] ?? 'category'),
          'title' => sanitize_text_field($node['title'] ?? 'Untitled'),
          'slug' => sanitize_title($node['slug'] ?? ''),
          'position' => $position,
          'settings_json' => json_encode($settings)
        ]);

        $id_map[$old_id] = intval($wpdb->insert_id);
      }

      return ['ok' => true, 'imported' => count($nodes)];
    } catch (\Throwable $e) {
      error_log('SFB api_import_form error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /** Create a submittal packet (HTML or PDF) in uploads/sfb/ and return its URL */
  function api_generate_packet($req){
    try {
      $this->ensure_tables();
      $p = $req->get_json_params();

      $form_id = intval($p['form_id'] ?? 0);
      $items   = $p['items'] ?? [];  // [{id,title,meta:{size,flange,thickness,ksi}, path:[cat,prod,type]}]
      $meta    = $p['meta'] ?? [];   // ['project','contractor','submittal','include_cover','include_leed']
      $format  = strtolower(sanitize_text_field($p['format'] ?? 'pdf')); // 'pdf' | 'html'

      if (!$form_id || !is_array($items) || empty($items)) {
        return new WP_Error('bad_request','Missing form_id or items', ['status'=>400]);
      }

      $project    = sanitize_text_field($meta['project'] ?? '');
      $contractor = sanitize_text_field($meta['contractor'] ?? '');
      $submittal  = sanitize_text_field($meta['submittal'] ?? '');
      $include_leed  = !empty($meta['include_leed']);

      // Load branding settings
      $brand = wp_parse_args(get_option($this->option_key(), []), $this->default_settings());

      // Force all branding scalars to strings (prevents null deprecation warnings)
      foreach ($brand as $k => $v) {
        // Keep booleans/arrays/ints as-is; only cast possible null scalars to strings
        if ($v === null || (is_scalar($v) && !is_bool($v) && !is_int($v) && !is_float($v))) {
          $brand[$k] = sfb_text($v);
        }
      }

      // Sanitize meta values for template use
      $meta = [
        'project'        => sfb_text($project),
        'contractor'     => sfb_text($contractor),
        'submittal'      => sfb_text($submittal),
        'include_leed'   => $include_leed,
        'include_cover'  => array_key_exists('include_cover',$meta) ? !empty($meta['include_cover']) : !empty($brand['cover_default']),
        'include_summary'=> !empty($meta['include_summary']),
        'layout'         => sfb_text(sanitize_text_field($meta['layout'] ?? 'technical')), // 'technical', 'branded', 'packet'
        // Automation flags
        'archive'        => !empty($meta['archive']),
        'track'          => !empty($meta['track']),
        'send_email'     => !empty($meta['send_email']),
        'email_to'       => sfb_text(sanitize_email($meta['email_to'] ?? '')),
        'white_label'    => !empty($meta['white_label']),
        // Signature block
        'approve_block'  => !empty($meta['approve_block']),
        'approved_by'    => sfb_text(sanitize_text_field($meta['approved_by'] ?? '')),
        'approved_title' => sfb_text(sanitize_text_field($meta['approved_title'] ?? '')),
        'approved_date'  => sfb_text(sanitize_text_field($meta['approved_date'] ?? '')),
      ];

      // --- Pro Feature Guards ---
      // White-label
      if (!sfb_feature_enabled('white_label')) {
        $meta['white_label'] = false;
      }

      // Signature block
      if (!sfb_feature_enabled('signature')) {
        $meta['approve_block'] = false;
      }

      // Themes & watermark
      if (!sfb_feature_enabled('themes')) {
        $brand['theme'] = 'engineering';
      }
      if (!sfb_feature_enabled('watermark')) {
        $brand['watermark'] = '';
      }

      // Apply monetization filters (allows agencies to override themes/colors)
      $brand['theme'] = apply_filters('sfb_pdf_theme', $brand['theme'], $brand, $meta);
      $brand['primary_color'] = apply_filters('sfb_pdf_color', $brand['primary_color'], $brand, $meta);

      // White-label: suppress default plugin tagline if enabled
      if ($meta['white_label'] && ($brand['footer_text'] ?? '') === 'Generated by Submittal & Spec Builder') {
        $brand['footer_text'] = ''; // silence default tagline
      }

      // --- Modular Template System ---
      $template_dir = plugin_dir_path(__FILE__) . 'templates/pdf/';

      ob_start();
      ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Submittal Package</title>
  <style>
    :root{ --ink:#111827; --muted:#6b7280; --line:#e5e7eb; --bg:#ffffff; --accent:<?= esc_attr($brand['primary_color']); ?>; }
    *{ box-sizing:border-box; margin:0; padding:0; }
    body{ font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; color:var(--ink); background:#fff; line-height:1.6; }
    h1,h2,h3{ margin:0 0 10px; font-weight:700; }
    table{ border-collapse:collapse; width:100%; }
    img{ max-width:100%; height:auto; }
    @page{ margin:20mm; }
    @media print{ .noprint{ display:none !important; } body{ background:#fff; } }
  </style>
</head>
<body>
<?php
      // 1) Cover page (if enabled)
      if (!empty($meta['include_cover'])) {
        if (file_exists($template_dir . 'cover.html.php')) {
          include $template_dir . 'cover.html.php';
        }
      }

      // 2) Summary page (for 'packet' layout or explicit request)
      $include_summary = !empty($meta['include_summary']) || ($meta['layout'] === 'packet');
      if ($include_summary && file_exists($template_dir . 'summary.html.php')) {
        include $template_dir . 'summary.html.php';
      }

      // 3) Table of Contents (for 'packet' layout)
      if ($meta['layout'] === 'packet') {
        if (file_exists($template_dir . 'toc.html.php')) {
          include $template_dir . 'toc.html.php';
        }
      }

      // 4) Product sheets
      foreach ($items as $it) {
        $product = $it;
        if (file_exists($template_dir . 'model-sheet.html.php')) {
          include $template_dir . 'model-sheet.html.php';
        }
      }
      ?>
</body>
</html>
<?php
      $html = ob_get_clean();

    // Save to uploads/sfb/
    $upload = wp_upload_dir();
    $dir = trailingslashit($upload['basedir']).'sfb';
    if (!wp_mkdir_p($dir)) {
      return new WP_Error('server_error','Unable to create uploads directory', ['status'=>500]);
    }

    if ($format === 'pdf') {
      if (!$this->dompdf_available()) {
        // fallback to html
        $format = 'html';
      }
    }

    if ($format === 'pdf') {
      // ---- PDF via Dompdf ----
      // Smart filename: Submittal_<Project>_<YYYY-MM-DD>.pdf
      $proj_safe = preg_replace('~[^A-Za-z0-9-_]+~', '_', (string)($meta['project'] ?: 'Packet'));
      $today = date('Y-m-d');
      $fname = "Submittal_{$proj_safe}_{$today}.pdf";
      $path  = trailingslashit($dir).$fname;

      // Dompdf setup
      $options = new \Dompdf\Options();

      // Enable remote resources (for external images/fonts if needed)
      $options->set('isRemoteEnabled', true);

      // Enable PHP execution for page numbers
      $options->set('isPhpEnabled', true);

      // Enable HTML5 parser - library bundled in lib/masterminds/html5
      $options->set('isHtml5ParserEnabled', true);

      // Use Helvetica as fallback-safe font (built-in to PDF spec)
      $options->set('defaultFont', 'Helvetica');

      // IMPORTANT: Backend must be CPDF (not DOMPDF for v0.8.x compatibility)
      $options->set('pdfBackend', 'CPDF');

      $dompdf = new \Dompdf\Dompdf($options);
      $dompdf->setOptions($options);

      // Load HTML and render (single pass)
      $dompdf->loadHtml($html, 'UTF-8');
      $dompdf->setPaper('letter', 'portrait');
      $dompdf->render();

      // Add page numbers using canvas - must be done BEFORE output
      $canvas = $dompdf->getCanvas();
      $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
        $font = $fontMetrics->getFont('helvetica', 'normal');
        $size = 9;
        $color = array(0.42, 0.45, 0.50);
        $linkColor = array(0.4, 0.49, 0.91); // Blue color for link

        $height = $canvas->get_height();
        $width = $canvas->get_width();

        // Footer text on left with clickable link
        $canvas->text(36, $height - 36, "Generated with ", $font, $size, $color);

        // Add clickable link
        $linkText = "Submittal & Spec Sheet Builder";
        $linkX = 36 + $fontMetrics->getTextWidth("Generated with ", $font, $size);
        $canvas->text($linkX, $height - 36, $linkText, $font, $size, $linkColor);

        // Add the actual hyperlink annotation
        $linkWidth = $fontMetrics->getTextWidth($linkText, $font, $size);
        $canvas->add_link("https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/", $linkX, $height - 36 - $size, $linkX + $linkWidth, $height - 36 + 2);

        // Page numbers on right
        $pageText = "Page " . $pageNumber . " of " . $pageCount;
        $canvas->text($width - 156, $height - 36, $pageText, $font, $size, $color);
      });

      $pdf = $dompdf->output();
      file_put_contents($path, $pdf);

      $url = trailingslashit($upload['baseurl']).'sfb/'.$fname;

      // --- Automation: Archive, Tracking, Email ---
      $tracking_url = '';
      $emailed = false;
      $archived = false;

      // 1) Archive to history folder (Pro)
      if (sfb_feature_enabled('archive') && $meta['archive']) {
        $proj_slug = preg_replace('~[^A-Za-z0-9-_]+~', '_', (string)($meta['project'] ?: 'Packet'));
        $hist_dir = wp_upload_dir();
        $hist_path = trailingslashit($hist_dir['basedir']) . "sfb/history/{$proj_slug}/" . date('Y/m') . '/';
        wp_mkdir_p($hist_path);
        @copy($path, $hist_path . $fname);
        $archived = true;
      }

      // 2) Create tracking link (Pro) - Automatic when auto-email is enabled
      // Tracking is only useful when we know who's viewing (via email capture)
      if (sfb_feature_enabled('tracking') && $meta['send_email'] && !empty($meta['email_to'])) {
        $token = wp_generate_password(20, false);
        $rec = [
          'file' => $url,
          'project' => (string)($meta['project'] ?? ''),
          'created' => current_time('mysql'),
          'view_count' => 0,
          'last_viewed' => null,
          'views' => [],
          'email_to' => $meta['email_to'] // Store recipient email for reference
        ];
        $all = get_option('sfb_packets', []);
        $all[$token] = $rec;
        update_option('sfb_packets', $all, false);
        $tracking_url = add_query_arg(['sfb_view' => $token], home_url('/'));
      }

      // 3) Auto-email (Pro)
      if (sfb_feature_enabled('auto_email') && $meta['send_email'] && !empty($meta['email_to'])) {
        $to = $meta['email_to'];
        $subj = 'Submittal Packet: ' . ($meta['project'] ?: 'Project');
        $body = "Your submittal packet is attached.\n\nProject: " . ($meta['project'] ?: '') . "\n";
        if ($tracking_url) {
          $body .= "Link: " . $tracking_url . "\n";
        }
        $headers = [];
        $sent = wp_mail($to, $subj, $body, $headers, [$path]);
        $emailed = $sent !== false;
      }

      return [
        'ok' => true,
        'url' => $url,
        'format' => 'pdf',
        'tracking_url' => $tracking_url,
        'archived' => $archived,
        'emailed' => $emailed,
        'pro_active' => sfb_is_pro_active(),
        'features' => sfb_enabled_features(),
        'registry_v' => SFB_PRO_REGISTRY_VERSION,
      ];
    } else {
      // ---- HTML fallback ----
      $fname = 'submittal-'.date('Ymd-His').'-'.wp_generate_password(6,false,false).'.html';
      $path  = trailingslashit($dir).$fname;
      file_put_contents($path, $html);
      $url = trailingslashit($upload['baseurl']).'sfb/'.$fname;
      return ['ok'=>true, 'url'=>$url, 'format'=>'html'];
    }
  } catch (\Throwable $e) {
    error_log('SFB api_generate_packet error: '.$e->getMessage());
    return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
  }
  }

  // ========================================================================
  // DRAFT MANAGEMENT (Pro Feature)
  // ========================================================================

  /** Register custom post type for drafts */
  function register_draft_cpt() {
    register_post_type('sfb_draft', [
      'public' => false,
      'show_ui' => false,
      'show_in_rest' => false,
      'supports' => [],
      'capability_type' => 'post',
    ]);
  }

  /** Schedule cron for purging expired drafts */
  function schedule_draft_purge_cron() {
    if (!wp_next_scheduled('sfb_purge_expired_drafts')) {
      wp_schedule_event(time(), 'daily', 'sfb_purge_expired_drafts');
    }
  }

  /** Schedule weekly lead export cron */
  function schedule_weekly_lead_export_cron() {
    // Only schedule if feature is enabled and Agency license
    $enabled = get_option('sfb_lead_weekly_export_enabled', false);
    $is_agency = sfb_is_agency_license();

    $event_scheduled = wp_next_scheduled('sfb_weekly_lead_export');

    if ($enabled && $is_agency && !$event_scheduled) {
      // Get day and time from settings
      $day = get_option('sfb_lead_weekly_export_day', 'monday');
      $time = get_option('sfb_lead_weekly_export_time', '09:00');

      // Calculate next occurrence
      $next_run = $this->calculate_next_weekly_run($day, $time);

      // Schedule weekly event
      wp_schedule_event($next_run, 'weekly', 'sfb_weekly_lead_export');

      error_log('[SFB] Weekly lead export cron scheduled for: ' . date('Y-m-d H:i:s', $next_run));
    } elseif (!$enabled && $event_scheduled) {
      // Feature disabled - unschedule
      wp_unschedule_event($event_scheduled, 'sfb_weekly_lead_export');
      error_log('[SFB] Weekly lead export cron unscheduled (feature disabled)');
    }
  }

  /** Calculate next weekly run timestamp based on day/time */
  function calculate_next_weekly_run($day, $time) {
    // Day names to numbers (1 = Monday, 7 = Sunday)
    $day_map = [
      'monday' => 1,
      'tuesday' => 2,
      'wednesday' => 3,
      'thursday' => 4,
      'friday' => 5,
      'saturday' => 6,
      'sunday' => 7
    ];

    $target_day = $day_map[$day] ?? 1;

    // Parse time (e.g., "09:00")
    list($hour, $minute) = explode(':', $time);

    // Get site timezone
    $tz = new DateTimeZone(wp_timezone_string());

    // Current time in site timezone
    $now = new DateTime('now', $tz);
    $current_day = (int)$now->format('N'); // 1-7 (Monday-Sunday)

    // Calculate days until target day
    $days_ahead = $target_day - $current_day;
    if ($days_ahead <= 0) {
      $days_ahead += 7; // Next week
    }

    // Create target datetime
    $target = clone $now;
    $target->modify("+{$days_ahead} days");
    $target->setTime((int)$hour, (int)$minute, 0);

    // If target is in the past (same day but time passed), add 7 days
    if ($target <= $now && $days_ahead === 0) {
      $target->modify('+7 days');
    }

    return $target->getTimestamp();
  }

  /** Purge expired drafts (cron callback) */
  function purge_expired_drafts() {
    global $wpdb;
    $now = current_time('mysql');

    // Find all expired drafts
    $sql = $wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta}
       WHERE meta_key = '_sfb_draft_expires_at'
       AND meta_value < %s",
      $now
    );
    $expired_ids = $wpdb->get_col($sql);

    foreach ($expired_ids as $post_id) {
      wp_delete_post($post_id, true);
    }

    if (defined('WP_DEBUG') && WP_DEBUG && !empty($expired_ids)) {
      error_log(sprintf('SFB: Purged %d expired drafts', count($expired_ids)));
    }

    return count($expired_ids);
  }

  /** Get draft statistics */
  private function get_draft_stats() {
    global $wpdb;
    $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'sfb_draft'");
    $expired = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
       INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
       WHERE p.post_type = 'sfb_draft'
       AND pm.meta_key = '_sfb_draft_expires_at'
       AND pm.meta_value < %s",
      current_time('mysql')
    ));

    return [
      'total' => intval($total),
      'expired' => intval($expired),
      'text' => sprintf('%d total • %d expired', intval($total), intval($expired))
    ];
  }

  /** Render feedback footer for admin pages */
  private function render_feedback_footer() {
    ?>
    <div style="margin-top: 40px; padding: 16px 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; text-align: center;">
      <p style="margin: 0; color: #6b7280; font-size: 14px;">
        <?php esc_html_e('Found a bug? Have a suggestion?', 'submittal-builder'); ?>
        <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: none; font-weight: 600;">
          <?php esc_html_e('Tell us about it', 'submittal-builder'); ?> →
        </a>
      </p>
    </div>
    <?php
  }

  /** Generate random ID for draft */
  private function sfb_rand_id($len = 12) {
    $bytes = random_bytes(ceil($len * 0.75));
    $base64 = base64_encode($bytes);
    $base64url = strtr($base64, '+/', '-_');
    return substr(rtrim($base64url, '='), 0, $len);
  }

  /** Validate draft payload */
  private function validate_draft_payload($data) {
    $errors = [];

    // Check items
    if (!isset($data['items']) || !is_array($data['items'])) {
      $errors[] = 'Items must be an array';
    } elseif (count($data['items']) > 1000) {
      $errors[] = 'Too many items (max 1000)';
    } else {
      // Sanitize items
      foreach ($data['items'] as $idx => $item) {
        if (!is_array($item)) {
          $errors[] = "Item $idx must be an object";
          continue;
        }
        $data['items'][$idx] = [
          'id' => isset($item['id']) ? absint($item['id']) : 0,
          'title' => isset($item['title']) ? sanitize_text_field($item['title']) : '',
          'meta' => isset($item['meta']) && is_array($item['meta']) ? array_map('sanitize_text_field', $item['meta']) : [],
          'path' => isset($item['path']) && is_array($item['path']) ? array_map('sanitize_text_field', $item['path']) : [],
        ];
      }
    }

    // Check meta
    if (!isset($data['meta']) || !is_array($data['meta'])) {
      $errors[] = 'Meta must be an object';
    } else {
      // Sanitize meta
      $data['meta'] = [
        'project' => isset($data['meta']['project']) ? sanitize_text_field($data['meta']['project']) : '',
        'contractor' => isset($data['meta']['contractor']) ? sanitize_text_field($data['meta']['contractor']) : '',
        'submittal' => isset($data['meta']['submittal']) ? sanitize_text_field($data['meta']['submittal']) : '',
        'preset' => isset($data['meta']['preset']) ? sanitize_text_field($data['meta']['preset']) : 'packet',
        'format' => isset($data['meta']['format']) ? sanitize_text_field($data['meta']['format']) : 'pdf',
        'include_cover' => !empty($data['meta']['include_cover']),
        'include_leed' => !empty($data['meta']['include_leed']),
      ];
    }

    // Check size (1MB limit)
    $json_size = strlen(json_encode($data));
    if ($json_size > 1048576) { // 1MB
      $errors[] = 'Payload too large (max 1MB)';
    }

    return ['valid' => empty($errors), 'errors' => $errors, 'data' => $data];
  }

  /** Rate limiting check */
  private function check_draft_rate_limit($seconds = 20) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'sfb_draft_rl_' . md5($ip);

    if (get_transient($key)) {
      return false; // Rate limited
    }

    set_transient($key, 1, $seconds);
    return true;
  }

  /** Get share URL for draft */
  private function get_draft_share_url($draft_id) {
    // Try to get current page URL
    $current_url = home_url($_SERVER['REQUEST_URI'] ?? '/');

    // If we're on the form page, use it; otherwise use home
    if (strpos($current_url, 'submittal') !== false || isset($_GET['page'])) {
      $base_url = remove_query_arg(['sfb_draft'], $current_url);
    } else {
      $base_url = home_url('/');
    }

    return add_query_arg('sfb_draft', $draft_id, $base_url);
  }

  /** API: Create draft (POST /drafts) */
  function api_create_draft($req) {
    // Get settings
    $opt = get_option($this->option_key(), []);
    $settings = wp_parse_args($opt, $this->default_settings());

    // Check if server drafts are enabled
    if (!$settings['drafts_server_enabled']) {
      return [
        'ok' => false,
        'code' => 'disabled',
        'message' => 'Server drafts are currently disabled'
      ];
    }

    // Feature gate
    if (!sfb_feature_enabled('server_drafts')) {
      return [
        'ok' => false,
        'code' => 'pro_required',
        'message' => 'Shareable Drafts is a Pro feature',
        'upgrade_url' => admin_url('admin.php?page=sfb-upgrade')
      ];
    }

    // Rate limit (use setting)
    if (!$this->check_draft_rate_limit($settings['drafts_rate_limit_sec'])) {
      return new WP_Error('rate_limit', 'Too many requests. Please wait.', ['status' => 429]);
    }

    // Get and validate payload
    $body = $req->get_json_params();
    $validation = $this->validate_draft_payload($body);

    if (!$validation['valid']) {
      return new WP_Error('invalid_payload', implode('; ', $validation['errors']), ['status' => 400]);
    }

    $data = $validation['data'];
    $draft_id = $this->sfb_rand_id(12);

    // Calculate expiry (use setting)
    $created_at = current_time('mysql');
    $expires_at = date('Y-m-d H:i:s', strtotime('+' . $settings['drafts_expiry_days'] . ' days'));

    // Create post
    $post_id = wp_insert_post([
      'post_type' => 'sfb_draft',
      'post_title' => 'SFB Draft ' . $draft_id,
      'post_status' => 'publish',
      'post_author' => get_current_user_id() ?: 0,
    ]);

    if (is_wp_error($post_id)) {
      return new WP_Error('creation_failed', 'Failed to create draft', ['status' => 500]);
    }

    // Store metadata
    update_post_meta($post_id, '_sfb_draft_id', $draft_id);
    update_post_meta($post_id, '_sfb_draft_payload', [
      'version' => 1,
      'items' => $data['items'],
      'meta' => $data['meta'],
      'created_at' => $created_at,
      'expires_at' => $expires_at,
    ]);
    update_post_meta($post_id, '_sfb_draft_created_at', $created_at);
    update_post_meta($post_id, '_sfb_draft_expires_at', $expires_at);

    return [
      'ok' => true,
      'id' => $draft_id,
      'url' => $this->get_draft_share_url($draft_id),
      'expires_at' => $expires_at,
    ];
  }

  /** API: Get draft (GET /drafts/:id) */
  function api_get_draft($req) {
    $draft_id = $req->get_param('id');

    // Find post by draft ID
    global $wpdb;
    $post_id = $wpdb->get_var($wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta}
       WHERE meta_key = '_sfb_draft_id'
       AND meta_value = %s
       LIMIT 1",
      $draft_id
    ));

    if (!$post_id) {
      return new WP_Error('not_found', 'Draft not found', ['status' => 404]);
    }

    // Check if expired
    $expires_at = get_post_meta($post_id, '_sfb_draft_expires_at', true);
    if ($expires_at && strtotime($expires_at) < time()) {
      wp_delete_post($post_id, true);
      return new WP_Error('expired', 'Draft has expired', ['status' => 410]);
    }

    // Get payload
    $payload = get_post_meta($post_id, '_sfb_draft_payload', true);

    if (!$payload) {
      return new WP_Error('invalid_data', 'Draft data corrupted', ['status' => 500]);
    }

    return [
      'ok' => true,
      'items' => $payload['items'] ?? [],
      'meta' => $payload['meta'] ?? [],
      'created_at' => $payload['created_at'] ?? '',
      'expires_at' => $payload['expires_at'] ?? '',
    ];
  }

  /** API: Update draft (PUT /drafts/:id) */
  function api_update_draft($req) {
    // Get settings
    $opt = get_option($this->option_key(), []);
    $settings = wp_parse_args($opt, $this->default_settings());

    // Check if server drafts are enabled
    if (!$settings['drafts_server_enabled']) {
      return [
        'ok' => false,
        'code' => 'disabled',
        'message' => 'Server drafts are currently disabled'
      ];
    }

    // Feature gate
    if (!sfb_feature_enabled('server_drafts')) {
      return [
        'ok' => false,
        'code' => 'pro_required',
        'message' => 'Shareable Drafts is a Pro feature'
      ];
    }

    // Rate limit
    if (!$this->check_draft_rate_limit($settings['drafts_rate_limit_sec'])) {
      return new WP_Error('rate_limit', 'Too many requests. Please wait.', ['status' => 429]);
    }

    $draft_id = $req->get_param('id');

    // Find post
    global $wpdb;
    $post_id = $wpdb->get_var($wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta}
       WHERE meta_key = '_sfb_draft_id'
       AND meta_value = %s
       LIMIT 1",
      $draft_id
    ));

    if (!$post_id) {
      return new WP_Error('not_found', 'Draft not found', ['status' => 404]);
    }

    // Validate new payload
    $body = $req->get_json_params();
    $validation = $this->validate_draft_payload($body);

    if (!$validation['valid']) {
      return new WP_Error('invalid_payload', implode('; ', $validation['errors']), ['status' => 400]);
    }

    $data = $validation['data'];
    $expires_at = get_post_meta($post_id, '_sfb_draft_expires_at', true);
    $created_at = get_post_meta($post_id, '_sfb_draft_created_at', true);

    // Update payload
    update_post_meta($post_id, '_sfb_draft_payload', [
      'version' => 1,
      'items' => $data['items'],
      'meta' => $data['meta'],
      'created_at' => $created_at,
      'expires_at' => $expires_at,
    ]);

    return [
      'ok' => true,
      'id' => $draft_id,
      'url' => $this->get_draft_share_url($draft_id),
      'expires_at' => $expires_at,
    ];
  }

  /** Get available industry packs (delegates to helper function) */
  private function get_available_packs() {
    return sfb_get_industry_packs();
  }

  /** Seed industry pack from JSON */
  private function seed_industry_pack($pack, $create_draft = false) {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';
    $form_id = 1;

    try {
      // Load JSON data
      $json_file = plugin_dir_path(__FILE__) . 'assets/demo/' . $pack . '.json';
      if (!file_exists($json_file)) {
        return [
          'success' => false,
          'message' => __('Industry pack not found.', 'submittal-builder')
        ];
      }

      $data = json_decode(file_get_contents($json_file), true);
      if (!$data ||!isset($data['categories'])) {
        return [
          'success' => false,
          'message' => __('Invalid pack format.', 'submittal-builder')
        ];
      }

      $stats = ['categories' => 0, 'types' => 0, 'items' => 0, 'skipped' => 0];
      $position = 0;

      // Insert categories → types → items (idempotent - skip if exists)
      foreach ($data['categories'] as $cat_data) {
        $cat_slug = sanitize_title($cat_data['title']);

        // Check if category already exists
        $existing_cat = $wpdb->get_var($wpdb->prepare(
          "SELECT id FROM {$table} WHERE form_id = %d AND node_type = 'category' AND slug = %s LIMIT 1",
          $form_id,
          $cat_slug
        ));

        if ($existing_cat) {
          $cat_id = $existing_cat;
          $stats['skipped']++;
        } else {
          $wpdb->insert($table, [
            'form_id' => $form_id,
            'parent_id' => null,
            'node_type' => 'category',
            'title' => $cat_data['title'],
            'slug' => $cat_slug,
            'position' => $position++,
            'settings_json' => json_encode([
              '_demo_seed' => 1,
              '_demo_pack' => $pack
            ])
          ]);
          $cat_id = $wpdb->insert_id;
          $stats['categories']++;
        }

        // Insert types
        if (isset($cat_data['types'])) {
          $type_pos = 0;
          foreach ($cat_data['types'] as $type_data) {
            $type_slug = sanitize_title($type_data['title']);

            // Check if type already exists under this category
            $existing_type = $wpdb->get_var($wpdb->prepare(
              "SELECT id FROM {$table} WHERE form_id = %d AND parent_id = %d AND node_type = 'type' AND slug = %s LIMIT 1",
              $form_id,
              $cat_id,
              $type_slug
            ));

            if ($existing_type) {
              $type_id = $existing_type;
              $stats['skipped']++;
            } else {
              $wpdb->insert($table, [
                'form_id' => $form_id,
                'parent_id' => $cat_id,
                'node_type' => 'type',
                'title' => $type_data['title'],
                'slug' => $type_slug,
                'position' => $type_pos++,
                'settings_json' => json_encode([
                  '_demo_seed' => 1,
                  '_demo_pack' => $pack
                ])
              ]);
              $type_id = $wpdb->insert_id;
              $stats['types']++;
            }

            // Insert items (models)
            if (isset($type_data['items'])) {
              $item_pos = 0;
              foreach ($type_data['items'] as $item_data) {
                $item_slug = sanitize_title($item_data['title']);

                // Check if item already exists under this type
                $existing_item = $wpdb->get_var($wpdb->prepare(
                  "SELECT id FROM {$table} WHERE form_id = %d AND parent_id = %d AND node_type = 'model' AND slug = %s LIMIT 1",
                  $form_id,
                  $type_id,
                  $item_slug
                ));

                if ($existing_item) {
                  $stats['skipped']++;
                } else {
                  $settings = [
                    'fields' => $item_data['meta'] ?? [],
                    '_demo_seed' => 1,
                    '_demo_pack' => $pack
                  ];
                  $wpdb->insert($table, [
                    'form_id' => $form_id,
                    'parent_id' => $type_id,
                    'node_type' => 'model',
                    'title' => $item_data['title'],
                    'slug' => $item_slug,
                    'position' => $item_pos++,
                    'settings_json' => json_encode($settings)
                  ]);
                  $stats['items']++;
                }
              }
            }
          }
        }
      }

      // Build success message with skip info
      $message = sprintf(__('Successfully seeded "%s" catalog!', 'submittal-builder'), $data['title']);
      if ($stats['skipped'] > 0) {
        $message .= ' ' . sprintf(__('(%d items already existed and were skipped)', 'submittal-builder'), $stats['skipped']);
      }

      $result = [
        'success' => true,
        'message' => $message,
        'stats' => $stats,
        'admin_url' => admin_url('admin.php?page=sfb'),
        'frontend_url' => $this->get_or_create_frontend_page()
      ];

      // Optionally create demo draft
      if ($create_draft) {
        $draft_result = $this->create_demo_draft_from_seeded();
        if ($draft_result['success']) {
          $result['draft_url'] = $draft_result['url'];
        }
      }

      return $result;

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error seeding pack: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Create demo draft from seeded data */
  private function create_demo_draft_from_seeded() {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';
    $form_id = 1;

    try {
      // Get random models from seeded data (6-10 items)
      $limit = rand(6, 10);
      $models = $wpdb->get_results($wpdb->prepare(
        "SELECT id, title, settings_json FROM {$table}
         WHERE form_id = %d AND node_type = 'model'
         AND settings_json LIKE %s
         ORDER BY RAND()
         LIMIT %d",
        $form_id,
        '%_demo_seed%',
        $limit
      ));

      if (empty($models)) {
        return [
          'success' => false,
          'message' => __('No items found to create draft.', 'submittal-builder')
        ];
      }

      // Build items array
      $items = [];
      foreach ($models as $model) {
        $settings = json_decode($model->settings_json, true);
        $meta = $settings['fields'] ?? [];

        // Get breadcrumb path
        $path = $this->get_node_breadcrumb($model->id);

        $items[] = [
          'id' => (int)$model->id,
          'title' => $model->title,
          'meta' => $meta,
          'path' => $path
        ];
      }

      // Create draft
      $draft_id = $this->sfb_rand_id(12);
      $payload = [
        'items' => $items,
        'meta' => [
          'project' => 'Demo Project',
          'contractor' => 'ACME Construction',
          'submittal' => 'DEMO-001',
          'preset' => 'packet',
          'format' => 'pdf',
          'include_cover' => true,
          'include_leed' => false
        ],
        'version' => 1
      ];

      $expiry_days = get_option('sfb_branding')['drafts_expiry_days'] ?? 45;
      $expires_at = gmdate('Y-m-d H:i:s', strtotime("+{$expiry_days} days"));

      $post_id = wp_insert_post([
        'post_type' => 'sfb_draft',
        'post_status' => 'private',
        'post_title' => $draft_id,
        'post_content' => ''
      ]);

      if (is_wp_error($post_id)) {
        throw new \Exception($post_id->get_error_message());
      }

      update_post_meta($post_id, '_sfb_draft_payload', $payload);
      update_post_meta($post_id, '_sfb_draft_expires_at', $expires_at);
      update_post_meta($post_id, '_sfb_draft_id', $draft_id);
      update_post_meta($post_id, '_sfb_demo_draft', 1);

      // Get share URL
      $share_url = $this->get_share_url($draft_id);

      return [
        'success' => true,
        'url' => $share_url
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error creating demo draft: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Reset demo content only */
  private function reset_demo_content() {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';

    try {
      $posts_deleted = 0;
      $terms_deleted = 0;
      $files_deleted = 0;

      // Delete nodes marked as demo
      $nodes_deleted = $wpdb->query(
        "DELETE FROM {$table} WHERE settings_json LIKE '%_demo_seed%'"
      );
      $posts_deleted += (int) $nodes_deleted;

      // Delete demo drafts
      $drafts = get_posts([
        'post_type' => 'sfb_draft',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_key' => '_sfb_demo_draft',
        'meta_value' => '1'
      ]);

      foreach ($drafts as $draft_id) {
        wp_delete_post($draft_id, true);
        $posts_deleted++;
      }

      // Delete demo-tagged files from uploads/sfb directory
      $upload_dir = wp_upload_dir();
      $sfb_dir = trailingslashit($upload_dir['basedir']) . 'sfb';

      if (is_dir($sfb_dir)) {
        $files = glob($sfb_dir . '/*_demo_*');
        if (is_array($files)) {
          foreach ($files as $file) {
            if (is_file($file) && strpos(basename($file), '_demo_') !== false) {
              if (unlink($file)) {
                $files_deleted++;
              }
            }
          }
        }
      }

      return [
        'success' => true,
        'message' => sprintf(
          __('Demo reset complete. Removed %d posts, %d terms, %d files.', 'submittal-builder'),
          $posts_deleted,
          $terms_deleted,
          $files_deleted
        )
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error resetting demo content: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Generate demo data for screenshots */
  private function generate_screenshot_demo_data() {
    try {
      $tracking_count = 0;
      $pack_count = 0;
      $analytics_count = 0;

      // 1. Generate Tracking Demo Data (10 tracking links)
      $tracking_links = get_option('sfb_packets', []);

      $project_names = [
        'Downtown Office Tower - Phase 2',
        'Riverside Medical Center Expansion',
        'Tech Campus Building C',
        'Mountain View Retail Complex',
        'Harbor Front Hotel Project',
        'University Science Building',
        'Parkside Residential Development',
        'Industrial Park Warehouse 5',
        'City Hall Renovation',
        'Lakeside Conference Center'
      ];

      $client_companies = [
        'BuildCorp Construction',
        'Summit Builders LLC',
        'Metropolitan Contractors',
        'Horizon Development Group',
        'Premier Construction Co',
        'Elite Building Services',
        'Pinnacle Contracting',
        'Urban Development Partners',
        'Cornerstone Builders',
        'Skyline Construction'
      ];

      foreach ($project_names as $i => $project) {
        $token = 'demo_' . wp_generate_password(16, false);
        $created = date('Y-m-d H:i:s', strtotime("-" . (30 - $i * 3) . " days"));
        $view_count = rand(0, 15);
        $views = [];

        // Generate view history
        for ($v = 0; $v < $view_count; $v++) {
          $views[] = [
            'timestamp' => date('Y-m-d H:i:s', strtotime($created . " +" . rand(1, 72) . " hours")),
            'ip' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
            'user_agent' => 'Mozilla/5.0 (Demo Data)'
          ];
        }

        $tracking_links[$token] = [
          'project' => $project,
          'contractor' => 'John Smith',
          'email_to' => 'demo@example.com',
          'company_to' => $client_companies[$i],
          'created' => $created,
          'view_count' => $view_count,
          'views' => $views,
          '_demo_data' => true
        ];
        $tracking_count++;
      }

      update_option('sfb_packets', $tracking_links, false);

      // 2. Generate Agency Pack Demo Data (5 packs)
      $packs = get_option('sfb_agency_packs', []);

      $pack_templates = [
        [
          'name' => 'Electrical Package',
          'description' => 'Complete electrical components catalog with fixtures, panels, and controls',
          'categories' => ['Lighting Fixtures', 'Electrical Panels', 'Controls & Sensors']
        ],
        [
          'name' => 'HVAC Systems',
          'description' => 'Heating, ventilation, and air conditioning equipment catalog',
          'categories' => ['Air Handlers', 'Ductwork', 'Controls']
        ],
        [
          'name' => 'Plumbing Fixtures',
          'description' => 'Commercial plumbing fixtures and fittings',
          'categories' => ['Sinks & Faucets', 'Toilets & Urinals', 'Piping']
        ],
        [
          'name' => 'Security Systems',
          'description' => 'Access control, cameras, and monitoring equipment',
          'categories' => ['Cameras', 'Access Control', 'Alarm Systems']
        ],
        [
          'name' => 'Fire Protection',
          'description' => 'Fire suppression and detection systems',
          'categories' => ['Sprinkler Heads', 'Fire Alarms', 'Extinguishers']
        ]
      ];

      foreach ($pack_templates as $template) {
        $pack_id = 'demo_pack_' . sanitize_title($template['name']);

        $packs[] = [
          'id' => $pack_id,
          'name' => $template['name'],
          'description' => $template['description'],
          'created' => date('Y-m-d H:i:s', strtotime('-' . rand(5, 25) . ' days')),
          'node_ids' => [], // Would reference actual catalog nodes
          'settings' => [
            'include_branding' => true,
            'include_notes' => false
          ],
          '_demo_data' => true
        ];
        $pack_count++;
      }

      update_option('sfb_agency_packs', $packs, false);

      // 3. Generate Analytics Demo Data (30 days of activity)
      // Store in a custom option for analytics
      $analytics_data = [];
      $today = time();

      for ($day = 0; $day < 30; $day++) {
        $date = date('Y-m-d', strtotime("-$day days", $today));

        $analytics_data[$date] = [
          'packets_created' => rand(2, 8),
          'pdfs_generated' => rand(5, 20),
          'tracking_views' => rand(10, 50),
          'leads_captured' => rand(1, 5),
          'unique_visitors' => rand(15, 45),
          '_demo_data' => true
        ];
        $analytics_count++;
      }

      update_option('sfb_demo_analytics', $analytics_data, false);

      return [
        'success' => true,
        'message' => sprintf(
          __('✅ Demo data generated successfully! Created %d tracking links, %d agency packs, and %d days of analytics data. Visit Tracking, Agency, and Agency Analytics pages to see the results.', 'submittal-builder'),
          $tracking_count,
          $pack_count,
          $analytics_count
        )
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error generating demo data: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Clear all screenshot demo data */
  private function clear_screenshot_demo_data() {
    try {
      $cleared_count = 0;

      // 1. Clear tracking demo data
      $tracking_links = get_option('sfb_packets', []);
      $original_count = count($tracking_links);

      $tracking_links = array_filter($tracking_links, function($packet) {
        return empty($packet['_demo_data']);
      });

      update_option('sfb_packets', $tracking_links, false);
      $cleared_count += ($original_count - count($tracking_links));

      // 2. Clear agency pack demo data
      $packs = get_option('sfb_agency_packs', []);
      $original_pack_count = count($packs);

      $packs = array_filter($packs, function($pack) {
        return empty($pack['_demo_data']);
      });

      update_option('sfb_agency_packs', array_values($packs), false);
      $cleared_count += ($original_pack_count - count($packs));

      // 3. Clear analytics demo data
      delete_option('sfb_demo_analytics');
      $cleared_count += 30; // 30 days

      return [
        'success' => true,
        'message' => sprintf(
          __('✅ Demo data cleared successfully! Removed approximately %d demo items. Your real data remains intact.', 'submittal-builder'),
          $cleared_count
        )
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error clearing demo data: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Get or create frontend page with shortcode */
  private function get_or_create_frontend_page() {
    // First try to find existing page
    $url = $this->get_frontend_page_url();
    if ($url) {
      return $url;
    }

    // Create new page
    $page_id = wp_insert_post([
      'post_title' => 'Submittal & Spec Builder Demo',
      'post_content' => '[submittal_builder]',
      'post_status' => 'publish',
      'post_type' => 'page'
    ]);

    if (is_wp_error($page_id)) {
      return null;
    }

    return get_permalink($page_id);
  }

  /** Legacy: Seed demo catalog with realistic data (DEPRECATED - use seed_industry_pack) */
  private function seed_demo_catalog() {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';
    $form_id = 1;

    try {
      // Check if data already exists
      $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE form_id = %d",
        $form_id
      ));

      if ($existing > 0) {
        return [
          'success' => false,
          'message' => __('Demo data already exists. Please reset first.', 'submittal-builder')
        ];
      }

      // Categories with realistic construction products
      $categories = [
        ['title' => 'Steel Beams', 'slug' => 'steel-beams'],
        ['title' => 'Fasteners', 'slug' => 'fasteners'],
        ['title' => 'Insulation', 'slug' => 'insulation']
      ];

      // Products per category
      $products = [
        'steel-beams' => [
          ['title' => 'W-Beams', 'slug' => 'w-beams'],
          ['title' => 'I-Beams', 'slug' => 'i-beams']
        ],
        'fasteners' => [
          ['title' => 'Bolts', 'slug' => 'bolts'],
          ['title' => 'Anchors', 'slug' => 'anchors']
        ],
        'insulation' => [
          ['title' => 'Fiberglass', 'slug' => 'fiberglass']
        ]
      ];

      // Types and models with metadata
      $types_models = [
        'w-beams' => [
          'Standard' => [
            ['title' => 'W8x24', 'meta' => ['size' => '8"', 'flange' => '6.5"', 'thickness' => '0.4"', 'ksi' => '50']],
            ['title' => 'W10x30', 'meta' => ['size' => '10"', 'flange' => '8"', 'thickness' => '0.5"', 'ksi' => '50']],
            ['title' => 'W12x35', 'meta' => ['size' => '12"', 'flange' => '6.56"', 'thickness' => '0.52"', 'ksi' => '50']]
          ]
        ],
        'i-beams' => [
          'Heavy Duty' => [
            ['title' => 'S12x35', 'meta' => ['size' => '12"', 'flange' => '5.08"', 'thickness' => '0.544"', 'ksi' => '50']],
            ['title' => 'S15x50', 'meta' => ['size' => '15"', 'flange' => '5.64"', 'thickness' => '0.622"', 'ksi' => '50']]
          ]
        ],
        'bolts' => [
          'Hex Head' => [
            ['title' => '1/2" x 2"', 'meta' => ['size' => '1/2"', 'thickness' => 'N/A', 'ksi' => 'Grade 5']],
            ['title' => '5/8" x 3"', 'meta' => ['size' => '5/8"', 'thickness' => 'N/A', 'ksi' => 'Grade 8']]
          ]
        ],
        'anchors' => [
          'Wedge' => [
            ['title' => '3/8" Wedge Anchor', 'meta' => ['size' => '3/8"', 'thickness' => 'N/A', 'ksi' => '36']],
            ['title' => '1/2" Wedge Anchor', 'meta' => ['size' => '1/2"', 'thickness' => 'N/A', 'ksi' => '36']]
          ]
        ],
        'fiberglass' => [
          'Batts' => [
            ['title' => 'R-13 3.5"', 'meta' => ['size' => '3.5"', 'thickness' => '3.5"', 'ksi' => 'N/A']],
            ['title' => 'R-19 6.25"', 'meta' => ['size' => '6.25"', 'thickness' => '6.25"', 'ksi' => 'N/A']],
            ['title' => 'R-30 9.5"', 'meta' => ['size' => '9.5"', 'thickness' => '9.5"', 'ksi' => 'N/A']]
          ]
        ]
      ];

      $position = 0;

      // Insert categories
      foreach ($categories as $cat) {
        $wpdb->insert($table, [
          'form_id' => $form_id,
          'parent_id' => null,
          'node_type' => 'category',
          'title' => $cat['title'],
          'slug' => $cat['slug'],
          'position' => $position++,
          'settings_json' => '{}'
        ]);
        $cat_id = $wpdb->insert_id;

        // Insert products for this category
        if (isset($products[$cat['slug']])) {
          $prod_pos = 0;
          foreach ($products[$cat['slug']] as $prod) {
            $wpdb->insert($table, [
              'form_id' => $form_id,
              'parent_id' => $cat_id,
              'node_type' => 'product',
              'title' => $prod['title'],
              'slug' => $prod['slug'],
              'position' => $prod_pos++,
              'settings_json' => '{}'
            ]);
            $prod_id = $wpdb->insert_id;

            // Insert types and models
            if (isset($types_models[$prod['slug']])) {
              $type_pos = 0;
              foreach ($types_models[$prod['slug']] as $type_name => $models) {
                $wpdb->insert($table, [
                  'form_id' => $form_id,
                  'parent_id' => $prod_id,
                  'node_type' => 'type',
                  'title' => $type_name,
                  'slug' => sanitize_title($type_name),
                  'position' => $type_pos++,
                  'settings_json' => '{}'
                ]);
                $type_id = $wpdb->insert_id;

                // Insert models
                $model_pos = 0;
                foreach ($models as $model) {
                  $settings = json_encode(['fields' => $model['meta']]);
                  $wpdb->insert($table, [
                    'form_id' => $form_id,
                    'parent_id' => $type_id,
                    'node_type' => 'model',
                    'title' => $model['title'],
                    'slug' => sanitize_title($model['title']),
                    'position' => $model_pos++,
                    'settings_json' => $settings
                  ]);
                }
              }
            }
          }
        }
      }

      return [
        'success' => true,
        'message' => __('Demo catalog seeded successfully!', 'submittal-builder')
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error seeding catalog: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Create demo draft with preselected items */
  private function create_demo_draft() {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';
    $form_id = 1;

    try {
      // Get random models across categories
      $models = $wpdb->get_results($wpdb->prepare(
        "SELECT id, title, settings_json FROM {$table}
         WHERE form_id = %d AND node_type = 'model'
         ORDER BY RAND()
         LIMIT 8",
        $form_id
      ));

      if (empty($models)) {
        return [
          'success' => false,
          'message' => __('No models found. Please seed demo catalog first.', 'submittal-builder')
        ];
      }

      // Build items array
      $items = [];
      foreach ($models as $model) {
        $settings = json_decode($model->settings_json, true);
        $meta = $settings['fields'] ?? [];

        // Get breadcrumb path
        $path = $this->get_node_breadcrumb($model->id);

        $items[] = [
          'id' => (int)$model->id,
          'title' => $model->title,
          'meta' => $meta,
          'path' => $path
        ];
      }

      // Create draft
      $draft_id = $this->sfb_rand_id(12);
      $payload = [
        'items' => $items,
        'meta' => [
          'project' => 'Demo Project',
          'contractor' => 'ACME Construction',
          'submittal' => 'SUB-001',
          'preset' => 'packet',
          'format' => 'pdf',
          'include_cover' => true,
          'include_leed' => false
        ],
        'version' => 1
      ];

      $expiry_days = get_option('sfb_branding')['drafts_expiry_days'] ?? 45;
      $expires_at = gmdate('Y-m-d H:i:s', strtotime("+{$expiry_days} days"));

      $post_id = wp_insert_post([
        'post_type' => 'sfb_draft',
        'post_status' => 'private',
        'post_title' => $draft_id,
        'post_content' => ''
      ]);

      if (is_wp_error($post_id)) {
        throw new \Exception($post_id->get_error_message());
      }

      update_post_meta($post_id, '_sfb_draft_payload', $payload);
      update_post_meta($post_id, '_sfb_draft_expires_at', $expires_at);
      update_post_meta($post_id, '_sfb_draft_id', $draft_id);

      // Get share URL
      $share_url = $this->get_share_url($draft_id);

      return [
        'success' => true,
        'message' => sprintf(__('Demo draft created with %d items!', 'submittal-builder'), count($items)),
        'url' => $share_url
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error creating demo draft: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Get breadcrumb path for a node */
  private function get_node_breadcrumb($node_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'sfb_nodes';
    $path = [];
    $current_id = $node_id;

    while ($current_id) {
      $node = $wpdb->get_row($wpdb->prepare(
        "SELECT id, parent_id, title FROM {$table} WHERE id = %d",
        $current_id
      ));

      if (!$node) break;

      array_unshift($path, $node->title);
      $current_id = $node->parent_id;
    }

    // Remove the model title itself (last item)
    if (count($path) > 0) {
      array_pop($path);
    }

    return $path;
  }

  /** Reset all SFB data */
  private function reset_sfb_data() {
    global $wpdb;

    try {
      // Delete all nodes
      $nodes_table = $wpdb->prefix . 'sfb_nodes';
      $wpdb->query("DELETE FROM {$nodes_table}");

      // Delete all drafts
      $drafts = get_posts([
        'post_type' => 'sfb_draft',
        'posts_per_page' => -1,
        'fields' => 'ids'
      ]);
      foreach ($drafts as $draft_id) {
        wp_delete_post($draft_id, true);
      }

      // Clear SFB options
      delete_option('sfb_branding');
      delete_option('sfb_license');

      // Clear uploads folder
      $upload_dir = wp_upload_dir();
      $sfb_dir = trailingslashit($upload_dir['basedir']) . 'sfb/';
      if (is_dir($sfb_dir)) {
        $this->recursive_rmdir($sfb_dir);
      }

      return [
        'success' => true,
        'message' => __('All SFB data has been reset successfully.', 'submittal-builder')
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'message' => sprintf(__('Error resetting data: %s', 'submittal-builder'), $e->getMessage())
      ];
    }
  }

  /** Recursively delete directory */
  private function recursive_rmdir($dir) {
    if (!is_dir($dir)) return;

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
      $path = $dir . '/' . $file;
      is_dir($path) ? $this->recursive_rmdir($path) : unlink($path);
    }
    rmdir($dir);
  }

  /** Get frontend page URL with shortcode */
  private function get_frontend_page_url() {
    global $wpdb;

    $page = $wpdb->get_var($wpdb->prepare(
      "SELECT ID FROM {$wpdb->posts}
       WHERE post_status = 'publish'
       AND post_content LIKE %s
       LIMIT 1",
      '%[submittal_builder%'
    ));

    if ($page) {
      return get_permalink($page);
    }

    return null;
  }

}

// Instantiate main plugin class and store in global for modular classes
$GLOBALS['sfb_plugin'] = SFB_Plugin::instance();

// Phase 1 Refactor: Initialize modular classes on plugins_loaded
add_action('plugins_loaded', function() {
  SFB_Admin::init();
  SFB_Render::init();
  SFB_Rest::init();
  SFB_Pdf::init();
  SFB_Ajax::init();  // Phase 5: AJAX hooks
  SFB_Agency_Analytics::init();  // Agency Analytics: Event tracking & heartbeat
  SFB_Agency_Lead_Routing::init();  // Agency Lead Routing: Rules engine + webhook delivery
}, 10);

/**
 * Clean up scheduled events on deactivation
 */
register_deactivation_hook(__FILE__, function () {
  // Weekly lead export
  $ts = wp_next_scheduled('sfb_weekly_lead_export');
  if ($ts) {
    wp_unschedule_event($ts, 'sfb_weekly_lead_export');
  }

  // Webhook retry (may be scheduled with args)
  $hook = 'sfb_retry_webhook_delivery';
  $crons = _get_cron_array();
  if (is_array($crons)) {
    foreach ($crons as $t => $events) {
      if (isset($events[$hook])) {
        foreach ($events[$hook] as $sig => $evt) {
          wp_unschedule_event($t, $hook, $evt['args'] ?? []);
        }
      }
    }
  }

  // Analytics heartbeat
  $ts = wp_next_scheduled('sfb_analytics_heartbeat');
  if ($ts) {
    wp_unschedule_event($ts, 'sfb_analytics_heartbeat');
  }
});

/**
 * Plugin Deactivation Hook
 *
 * Called when the plugin is deactivated from the WordPress plugins screen.
 * Automatically releases the license seat on the WooCommerce Software API
 * if auto-deactivation is enabled.
 */
register_deactivation_hook(__FILE__, 'sfb_on_plugin_deactivate');

function sfb_on_plugin_deactivate() {
  // Skip in dev mode
  if (defined('SFB_PRO_DEV') && SFB_PRO_DEV) {
    return;
  }

  // Check if auto-deactivation is enabled (default: true)
  $auto_deactivate = get_option('sfb_auto_deactivate_on_deactivate', true);
  if (!$auto_deactivate) {
    return;
  }

  // Safely attempt remote deactivation; ignore failures
  if (function_exists('sfb_deactivate_license')) {
    try {
      $result = sfb_deactivate_license();

      // Log result if WP_DEBUG is enabled
      if (defined('WP_DEBUG') && WP_DEBUG) {
        if (is_wp_error($result)) {
          error_log('[SFB] Deactivation API error: ' . $result->get_error_message());
        } else {
          error_log('[SFB] License deactivated successfully on plugin deactivation');
        }
      }
    } catch (Throwable $e) {
      // Catch any errors to prevent blocking deactivation
      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[SFB] Deactivation exception: ' . $e->getMessage());
      }
    }
  }

  // Clear license cache (always do this, even if remote call fails)
  delete_transient('sfb_license_check_cache');

  // Note: DO NOT delete sfb_license_data here
  // Users may reactivate the plugin later and expect their license key to be preserved
}
