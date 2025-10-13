<?php
/**
 * SFB_Admin - Admin menu registration (Phase 1 refactor)
 *
 * Thin wrapper that registers admin menus and forwards to existing
 * render callbacks in the main plugin file. No business logic here.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Admin {

  /**
   * Initialize admin hooks
   */
  public static function init() {
    add_action('admin_menu', [__CLASS__, 'register_menus']);
  }

  /**
   * Register all admin menu pages
   *
   * Forwards to existing render callbacks in Submittal_Form_Builder class
   */
  public static function register_menus() {
    global $sfb_plugin;

    if (!$sfb_plugin || !($sfb_plugin instanceof SFB_Plugin)) {
      return;
    }

    // Top-level: Submittal Builder
    add_menu_page(
      'Submittal Builder',
      'Submittal Builder',
      'manage_options',
      'sfb',
      [$sfb_plugin, 'render_builder_page'],
      'dashicons-category',
      56
    );

    // ========================================
    // Content/Data Section (Daily Use)
    // ========================================

    // 1. Submittal Builder (default submenu - renamed from parent)
    // WordPress automatically creates first submenu with same slug as parent
    // We'll rename it via global $submenu later if needed

    // 2. Welcome (News & Updates)
    add_submenu_page(
      'sfb',
      __('Welcome', 'submittal-builder'),
      __('Welcome', 'submittal-builder'),
      'manage_options',
      'sfb-onboarding',
      [$sfb_plugin, 'render_onboarding_page'],
      0
    );

    // 3. Tracking (Pro - Monitor customer engagement)
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
        [$sfb_plugin, 'render_tracking_page'],
        3
      );
    }

    // 4. Leads (Pro - View captured leads)
    $show_leads = ($license_status === 'active' || (defined('SFB_PRO_DEV') && SFB_PRO_DEV) || (function_exists('sfb_is_pro_active') && sfb_is_pro_active())) &&
                  get_option('sfb_lead_capture_enabled', false);
    if ($show_leads) {
      add_submenu_page(
        'sfb',
        __('Leads', 'submittal-builder'),
        __('Leads', 'submittal-builder'),
        'manage_options',
        'sfb-leads',
        [$sfb_plugin, 'render_leads_page'],
        4
      );
    }

    // ========================================
    // Configuration Section (Setup)
    // ========================================

    // 5. Branding (Logo, colors, company info)
    add_submenu_page(
      'sfb',
      __('Branding', 'submittal-builder'),
      __('Branding', 'submittal-builder'),
      'manage_options',
      'sfb-branding',
      [$sfb_plugin, 'render_branding_page'],
      5
    );

    // 6. Settings (Feature toggles, configuration)
    add_submenu_page(
      'sfb',
      __('Settings', 'submittal-builder'),
      __('Settings', 'submittal-builder'),
      'manage_options',
      'sfb-settings',
      [$sfb_plugin, 'render_settings_page'],
      6
    );

    // 7. Agency (Agency-specific settings and library)
    if (sfb_is_agency_license()) {
      add_submenu_page(
        'sfb',
        __('Agency', 'submittal-builder'),
        __('💼 Agency', 'submittal-builder'),
        'manage_options',
        'sfb-agency',
        [$sfb_plugin, 'render_agency_page'],
        7
      );

      // 8. Agency Analytics (Agency monitoring and insights)
      add_submenu_page(
        'sfb',
        __('Agency Analytics', 'submittal-builder'),
        __('📊 Agency Analytics', 'submittal-builder'),
        'manage_options',
        'sfb-agency-analytics',
        [$sfb_plugin, 'render_agency_analytics_page'],
        8
      );
    }

    // ========================================
    // Meta/Support Section (Occasional Use)
    // ========================================

    // 9. Utilities (Maintenance and diagnostic tools)
    add_submenu_page(
      'sfb',
      __('Utilities', 'submittal-builder'),
      __('Utilities', 'submittal-builder'),
      'manage_options',
      'sfb-tools',
      [$sfb_plugin, 'render_utilities_page'],
      9
    );

    // 10. Demo Tools (dev mode only - hidden in handoff mode)
    if (defined('SFB_DEV_MODE') && SFB_DEV_MODE && !sfb_is_client_handoff_mode()) {
      add_submenu_page(
        'sfb',
        __('Demo Tools', 'submittal-builder'),
        __('Demo Tools', 'submittal-builder'),
        'manage_options',
        'sfb-demo-tools',
        [$sfb_plugin, 'render_demo_tools_page'],
        10
      );
    }

    // 11. Last slot: License & Support (Adaptive based on license state)
    $last_position = 999;

    if ($license_status === 'expired' || $license_status === 'invalid') {
      // Expired/invalid license - let user manage it
      add_submenu_page(
        'sfb',
        __('Manage License', 'submittal-builder'),
        __('Manage License', 'submittal-builder'),
        'manage_options',
        'sfb-license',
        [$sfb_plugin, 'render_license_management_page'],
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
        [$sfb_plugin, 'render_license_support_page'],
        $last_position
      );
    } else {
      // Free user: upsell
      add_submenu_page(
        'sfb',
        __('Upgrade to Pro', 'submittal-builder'),
        __('P Upgrade', 'submittal-builder'),
        'manage_options',
        'sfb-upgrade',
        [$sfb_plugin, 'render_upgrade_page'],
        $last_position
      );
    }
  }
}
