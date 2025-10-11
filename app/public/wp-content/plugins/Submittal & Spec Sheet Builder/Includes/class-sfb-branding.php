<?php
/**
 * SFB_Branding - Brand settings management (Phase 7 refactor)
 *
 * Handles saving and managing brand settings. Extracted from main plugin file
 * to improve organization while keeping all behavior identical.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Branding {

  /**
   * Initialize branding hooks (if any needed in future)
   */
  public static function init() {
    // Currently no hooks needed - branding is saved via AJAX
    // This class exists as an organizational wrapper
  }

  /**
   * Save brand settings
   *
   * Takes raw POST data, validates, sanitizes, and saves to database.
   * Returns structured response for AJAX handler.
   *
   * @param array $data Raw brand settings data from frontend
   * @return array Response array with 'success', 'data', 'message'
   */
  public static function save($data) {
    try {
      // Ensure branding helpers are loaded
      if (!function_exists('sfb_sanitize_brand_settings')) {
        require_once plugin_dir_path(__FILE__) . 'branding-helpers.php';
      }

      // Sanitize using helper function
      $sanitized = sfb_sanitize_brand_settings($data);

      // Save to database
      $saved = update_option('sfb_brand_settings', $sanitized, false);

      error_log('[SFB] Brand settings saved: ' . ($saved ? 'success' : 'no change'));

      return [
        'success' => true,
        'data' => [
          'saved' => $saved,
          'settings' => $sanitized,
        ],
        'message' => __('Brand settings saved successfully', 'submittal-builder')
      ];

    } catch (\Throwable $e) {
      error_log('[SFB] Brand save error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

      return [
        'success' => false,
        'data' => null,
        'message' => __('Server error saving brand settings', 'submittal-builder')
      ];
    }
  }
}
