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

    // Tools page handlers
    add_action('wp_ajax_sfb_purge_expired_drafts', [$plugin, 'ajax_purge_expired_drafts']);
    add_action('wp_ajax_sfb_run_smoke_test', [$plugin, 'ajax_run_smoke_test']);

    // Branding save handler
    add_action('wp_ajax_sfb_save_brand', [$plugin, 'ajax_save_brand']);

    // Brand Presets (Agency feature)
    self::register_brand_preset_hooks();

    // Agency Packs (Agency feature)
    self::register_agency_pack_hooks($plugin);
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
}
