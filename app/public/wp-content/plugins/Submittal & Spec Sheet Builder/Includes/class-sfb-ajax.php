<?php
/**
 * SFB_Ajax - AJAX hook registration (Phase 5 refactor)
 *
 * Centralizes all AJAX hook registration (wp_ajax_* and wp_ajax_nopriv_*).
 * Forwards to existing handler methods in the main plugin file.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Ajax {

  /**
   * Initialize AJAX hooks
   */
  public static function init() {
    global $sfb_plugin;

    if (!$sfb_plugin || !($sfb_plugin instanceof SFB_Plugin)) {
      return;
    }

    // Admin AJAX hooks
    self::register_admin_hooks($sfb_plugin);

    // Frontend AJAX hooks (public + logged in)
    self::register_frontend_hooks($sfb_plugin);

    // Lead Capture AJAX hooks (Pro feature - public + logged in)
    self::register_lead_capture_hooks();
  }

  /**
   * Register admin AJAX hooks
   *
   * @param SFB_Plugin $plugin Main plugin instance
   */
  private static function register_admin_hooks($plugin) {
    // Dismiss welcome notice (onboarding)
    add_action('wp_ajax_sfb_dismiss_welcome', [$plugin, 'dismiss_welcome_notice']);

    // Utilities page handlers
    add_action('wp_ajax_sfb_purge_expired_drafts', [$plugin, 'ajax_purge_expired_drafts']);
    add_action('wp_ajax_sfb_run_smoke_test', [$plugin, 'ajax_run_smoke_test']);
    add_action('wp_ajax_sfb_test_email', [$plugin, 'ajax_test_email']);
    add_action('wp_ajax_sfb_clear_tracking', [$plugin, 'ajax_clear_tracking']);
    add_action('wp_ajax_sfb_optimize_db', [$plugin, 'ajax_optimize_db']);
    add_action('wp_ajax_sfb_clean_orphans', [$plugin, 'ajax_clean_orphans']);

    // Branding save handler
    add_action('wp_ajax_sfb_save_brand', [$plugin, 'ajax_save_brand']);

    // Brand Presets (Agency feature)
    self::register_brand_preset_hooks();

    // Agency Packs (Agency feature)
    self::register_agency_pack_hooks($plugin);

    // Lead Routing (Agency feature)
    self::register_lead_routing_hooks();
  }

  /**
   * Register brand preset AJAX hooks (Agency feature)
   */
  private static function register_brand_preset_hooks() {
    add_action('wp_ajax_sfb_preset_create', ['SFB_Branding', 'ajax_create_preset']);
    add_action('wp_ajax_sfb_preset_list', ['SFB_Branding', 'ajax_list_presets']);
    add_action('wp_ajax_sfb_preset_apply', ['SFB_Branding', 'ajax_apply_preset']);
    add_action('wp_ajax_sfb_preset_rename', ['SFB_Branding', 'ajax_rename_preset']);
    add_action('wp_ajax_sfb_preset_delete', ['SFB_Branding', 'ajax_delete_preset']);
    add_action('wp_ajax_sfb_preset_set_default', ['SFB_Branding', 'ajax_set_default_preset']);
  }

  /**
   * Register agency pack AJAX hooks (Agency feature)
   *
   * @param SFB_Plugin $plugin Main plugin instance
   */
  private static function register_agency_pack_hooks($plugin) {
    add_action('wp_ajax_sfb_pack_export', [$plugin, 'ajax_export_pack']);
  }

  /**
   * Register frontend AJAX hooks (public + logged in)
   *
   * @param SFB_Plugin $plugin Main plugin instance
   */
  private static function register_frontend_hooks($plugin) {
    // List products (used by frontend builder)
    add_action('wp_ajax_sfb_list_products', [$plugin, 'ajax_list_products']);
    add_action('wp_ajax_nopriv_sfb_list_products', [$plugin, 'ajax_list_products']);

    // Generate PDF from frontend (Phase 6: route through SFB_Pdf facade)
    add_action('wp_ajax_sfb_generate_frontend_pdf', ['SFB_Pdf', 'generate_frontend_pdf']);
    add_action('wp_ajax_nopriv_sfb_generate_frontend_pdf', ['SFB_Pdf', 'generate_frontend_pdf']);
  }

  /**
   * Register lead capture AJAX hooks (Pro feature - public + logged in)
   */
  private static function register_lead_capture_hooks() {
    // Lead capture submission
    add_action('wp_ajax_sfb_submit_lead', ['SFB_Lead_Capture', 'ajax_submit_lead']);
    add_action('wp_ajax_nopriv_sfb_submit_lead', ['SFB_Lead_Capture', 'ajax_submit_lead']);
  }

  /**
   * Register lead routing AJAX hooks (Agency feature)
   */
  private static function register_lead_routing_hooks() {
    add_action('wp_ajax_sfb_routing_save', ['SFB_Agency_Lead_Routing_Ajax', 'save_settings']);
    add_action('wp_ajax_sfb_routing_test', ['SFB_Agency_Lead_Routing_Ajax', 'test_rule']);
    add_action('wp_ajax_sfb_routing_clear_log', ['SFB_Agency_Lead_Routing_Ajax', 'clear_log']);
  }
}

/**
 * Lead Routing AJAX handlers
 */
class SFB_Agency_Lead_Routing_Ajax {

  /**
   * Save routing settings (rules + fallback + enabled status)
   */
  public static function save_settings() {
    // Security checks
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sfb_lead_routing')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }

    if (!sfb_is_agency_license()) {
      wp_send_json_error(['message' => 'Agency license required'], 403);
    }

    // Get POST data
    $enabled = !empty($_POST['enabled']);
    $rules = isset($_POST['rules']) ? json_decode(stripslashes($_POST['rules']), true) : [];
    $fallback = isset($_POST['fallback']) ? json_decode(stripslashes($_POST['fallback']), true) : [];

    // Validate
    if (!is_array($rules) || !is_array($fallback)) {
      wp_send_json_error(['message' => 'Invalid data format'], 400);
    }

    // Save settings
    update_option('sfb_lead_routing_enabled', $enabled, false);
    SFB_Agency_Lead_Routing::save_rules($rules);
    SFB_Agency_Lead_Routing::save_fallback($fallback);

    wp_send_json_success([
      'message' => 'Routing settings saved successfully',
      'enabled' => $enabled,
      'rules' => SFB_Agency_Lead_Routing::get_rules(),
      'fallback' => SFB_Agency_Lead_Routing::get_fallback(),
    ]);
  }

  /**
   * Test a routing rule
   */
  public static function test_rule() {
    // Security checks
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sfb_lead_routing')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }

    if (!sfb_is_agency_license()) {
      wp_send_json_error(['message' => 'Agency license required'], 403);
    }

    // Get rule data
    $rule = isset($_POST['rule']) ? json_decode(stripslashes($_POST['rule']), true) : [];

    if (!is_array($rule)) {
      wp_send_json_error(['message' => 'Invalid rule data'], 400);
    }

    // Test the rule
    $result = SFB_Agency_Lead_Routing::test_rule($rule);

    wp_send_json_success($result);
  }

  /**
   * Clear delivery log
   */
  public static function clear_log() {
    // Security checks
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sfb_lead_routing')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }

    if (!sfb_is_agency_license()) {
      wp_send_json_error(['message' => 'Agency license required'], 403);
    }

    // Clear log
    $cleared = SFB_Agency_Lead_Routing::clear_log();

    if ($cleared) {
      wp_send_json_success(['message' => 'Delivery log cleared']);
    } else {
      wp_send_json_error(['message' => 'Failed to clear log'], 500);
    }
  }
}
