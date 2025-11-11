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
   * Map license status to license state
   *
   * @param string $license_status License status from sfb_license option
   * @return string License state: 'free', 'expired', 'pro', or 'agency'
   */
  private static function get_license_state($license_status) {
    // Check for agency license first
    if (sfb_is_agency_license()) {
      return 'agency';
    }

    // Check license status
    if ($license_status === 'active' || (function_exists('sfb_is_pro_active') && sfb_is_pro_active())) {
      return 'pro';
    }

    if ($license_status === 'expired' || $license_status === 'invalid') {
      return 'expired';
    }

    // Default to free
    return 'free';
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

    // Get license information
    $lic = get_option('sfb_license', []);
    $license_status = $lic['status'] ?? '';

    // Map license status to license state (free|expired|pro|agency)
    $license_state = self::get_license_state($license_status);

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
      __('Welcome', 'submittal-spec-sheet-builder'),
      __('Welcome', 'submittal-spec-sheet-builder'),
      'manage_options',
      'sfb-onboarding',
      [$sfb_plugin, 'render_onboarding_page'],
      0
    );

    // 3. Tracking (Pro - Monitor customer engagement)
    $show_tracking = ($license_status === 'active') ||
                     (defined('SFB_PRO_DEV') && SFB_PRO_DEV) ||
                     (function_exists('sfb_is_pro_active') && sfb_is_pro_active());
    if ($show_tracking) {
      add_submenu_page(
        'sfb',
        __('Tracking', 'submittal-spec-sheet-builder'),
        __('Tracking', 'submittal-spec-sheet-builder'),
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
        __('Leads', 'submittal-spec-sheet-builder'),
        __('Leads', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-leads',
        [$sfb_plugin, 'render_leads_page'],
        4
      );
    }

    // ========================================
    // Configuration Section (Setup)
    // ========================================

    // 4.5. Import Catalog (Pro/Agency - Bulk import from CSV)
    $show_import = ($license_status === 'active' || (defined('SFB_PRO_DEV') && SFB_PRO_DEV) || (function_exists('sfb_is_pro_active') && sfb_is_pro_active()));
    if ($show_import) {
      add_submenu_page(
        'sfb',
        __('Import Catalog', 'submittal-spec-sheet-builder'),
        __('Import Catalog', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-import',
        [$sfb_plugin, 'render_import_page'],
        4.5
      );
    }

    // 5. Branding (Logo, colors, company info)
    add_submenu_page(
      'sfb',
      __('Branding', 'submittal-spec-sheet-builder'),
      __('Branding', 'submittal-spec-sheet-builder'),
      'manage_options',
      'sfb-branding',
      [$sfb_plugin, 'render_branding_page'],
      5
    );

    // 6. Settings (Feature toggles, configuration)
    add_submenu_page(
      'sfb',
      __('Settings', 'submittal-spec-sheet-builder'),
      __('Settings', 'submittal-spec-sheet-builder'),
      'manage_options',
      'sfb-settings',
      [$sfb_plugin, 'render_settings_page'],
      6
    );

    // 7. Agency (Agency-specific settings and library)
    if (sfb_is_agency_license()) {
      add_submenu_page(
        'sfb',
        __('Agency', 'submittal-spec-sheet-builder'),
        __('💼 Agency', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-agency',
        [$sfb_plugin, 'render_agency_page'],
        7
      );

      // 8. Agency Analytics (Agency monitoring and insights)
      add_submenu_page(
        'sfb',
        __('Agency Analytics', 'submittal-spec-sheet-builder'),
        __('📊 Agency Analytics', 'submittal-spec-sheet-builder'),
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
      __('Utilities', 'submittal-spec-sheet-builder'),
      __('Utilities', 'submittal-spec-sheet-builder'),
      'manage_options',
      'sfb-tools',
      [$sfb_plugin, 'render_utilities_page'],
      9
    );

    // 10. Demo Tools (controlled by SFB_SHOW_DEMO_TOOLS constant)
    // Only show when constant is true AND user has admin capabilities
    if (SFB_SHOW_DEMO_TOOLS && current_user_can('manage_options')) {
      add_submenu_page(
        'sfb',
        __('Demo Tools', 'submittal-spec-sheet-builder'),
        __('Demo Tools', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-demo-tools',
        [$sfb_plugin, 'render_demo_tools_page'],
        10
      );
    }

    // 11. Last slot: License & Support (Adaptive based on license state)
    $last_position = 999;

    // Show "⭐ Upgrade" only for free and expired states
    $show_upgrade = in_array($license_state, ['free', 'expired'], true);

    if ($license_state === 'expired') {
      // Expired license - show manage license page AND upgrade option
      add_submenu_page(
        'sfb',
        __('⭐ Upgrade', 'submittal-spec-sheet-builder'),
        __('⭐ Upgrade', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-upgrade',
        [$sfb_plugin, 'render_upgrade_page'],
        $last_position
      );
    } elseif (in_array($license_state, ['pro', 'agency'], true)) {
      // Pro/Agency user: License & Support hub (no upgrade option)
      add_submenu_page(
        'sfb',
        __('License & Support', 'submittal-spec-sheet-builder'),
        __('License & Support', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-license',
        [$sfb_plugin, 'render_license_support_page'],
        $last_position
      );

      // License Tier Testing Page (for testing license tier detection)
      add_submenu_page(
        'sfb',
        __('License Test', 'submittal-spec-sheet-builder'),
        __('🧪 License Test', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-license-test',
        [$sfb_plugin, 'render_license_test_page'],
        $last_position + 1
      );

      // License Logs (audit trail for Pro/Agency users)
      add_submenu_page(
        'sfb',
        __('License Logs', 'submittal-spec-sheet-builder'),
        __('License Logs', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-license-logs',
        'sfb_render_license_logs_page',
        $last_position + 2
      );
    } elseif ($license_state === 'free') {
      // Free user: show upgrade option
      add_submenu_page(
        'sfb',
        __('⭐ Upgrade', 'submittal-spec-sheet-builder'),
        __('⭐ Upgrade', 'submittal-spec-sheet-builder'),
        'manage_options',
        'sfb-upgrade',
        [$sfb_plugin, 'render_upgrade_page'],
        $last_position
      );
    }

    // Belt-and-suspenders safeguard: Remove Demo Tools for Free/Expired even if constant is enabled
    // This ensures production safety even if someone accidentally enables the constant
    add_action('admin_head', function() use ($license_state) {
      if (in_array($license_state, ['free', 'expired'], true)) {
        remove_submenu_page('sfb', 'sfb-demo-tools');
      }
    });
  }
}
