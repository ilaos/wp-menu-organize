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

      // Save "use default preset" toggle (Agency feature - Phase B)
      if (isset($data['use_default_preset'])) {
        update_option('sfb_brand_use_default_on_pdf', !empty($data['use_default_preset']), false);
      }

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

  // ========================================================================
  // BRAND PRESETS (Agency Feature - Phase A)
  // ========================================================================

  /**
   * Get all brand presets
   *
   * @return array Array of presets with id, name, data, is_default, updated_at
   */
  public static function get_presets() {
    $presets = get_option('sfb_brand_presets', []);

    // Ensure it's an array
    if (!is_array($presets)) {
      return [];
    }

    return $presets;
  }

  /**
   * Get a single preset by ID
   *
   * @param string $id Preset ID (UUID)
   * @return array|null Preset data or null if not found
   */
  public static function get_preset($id) {
    $presets = self::get_presets();

    foreach ($presets as $preset) {
      if ($preset['id'] === $id) {
        return $preset;
      }
    }

    return null;
  }

  /**
   * Create a new preset from current brand settings
   *
   * @param string $name Preset name
   * @return array|WP_Error Created preset data or error
   */
  public static function create_preset($name) {
    // Validate name
    $name = trim($name);
    if (empty($name)) {
      return new WP_Error('invalid_name', __('Preset name is required.', 'submittal-builder'));
    }

    // Ensure branding helpers are loaded
    if (!function_exists('sfb_get_brand_settings')) {
      require_once plugin_dir_path(__FILE__) . 'branding-helpers.php';
    }

    // Get current brand settings
    $current_brand = sfb_get_brand_settings();

    // Generate unique ID
    $id = wp_generate_uuid4();

    // Create preset
    $preset = [
      'id'         => $id,
      'name'       => sanitize_text_field($name),
      'data'       => $current_brand,
      'is_default' => false,
      'updated_at' => current_time('mysql'),
    ];

    // Add to presets list
    $presets = self::get_presets();
    $presets[] = $preset;

    // Save
    update_option('sfb_brand_presets', $presets, false);

    return $preset;
  }

  /**
   * Apply a preset to current brand settings
   *
   * @param string $id Preset ID
   * @return bool|WP_Error True on success or error
   */
  public static function apply_preset($id) {
    $preset = self::get_preset($id);

    if (!$preset) {
      return new WP_Error('preset_not_found', __('Preset not found.', 'submittal-builder'));
    }

    // Apply preset data to current brand settings
    $preset_data = $preset['data'];

    // Update meta timestamp
    if (!isset($preset_data['_meta'])) {
      $preset_data['_meta'] = [];
    }
    $preset_data['_meta']['updated_at'] = current_time('mysql');
    $preset_data['_meta']['applied_from_preset'] = $preset['name'];

    // Save as current brand settings
    update_option('sfb_brand_settings', $preset_data, false);

    return true;
  }

  /**
   * Rename a preset
   *
   * @param string $id Preset ID
   * @param string $new_name New preset name
   * @return bool|WP_Error True on success or error
   */
  public static function rename_preset($id, $new_name) {
    // Validate name
    $new_name = trim($new_name);
    if (empty($new_name)) {
      return new WP_Error('invalid_name', __('Preset name is required.', 'submittal-builder'));
    }

    $presets = self::get_presets();
    $found = false;

    foreach ($presets as &$preset) {
      if ($preset['id'] === $id) {
        $preset['name'] = sanitize_text_field($new_name);
        $preset['updated_at'] = current_time('mysql');
        $found = true;
        break;
      }
    }
    unset($preset);

    if (!$found) {
      return new WP_Error('preset_not_found', __('Preset not found.', 'submittal-builder'));
    }

    // Save
    update_option('sfb_brand_presets', $presets, false);

    return true;
  }

  /**
   * Delete a preset
   *
   * @param string $id Preset ID
   * @return bool|WP_Error True on success or error
   */
  public static function delete_preset($id) {
    $presets = self::get_presets();
    $found = false;
    $filtered = [];

    foreach ($presets as $preset) {
      if ($preset['id'] === $id) {
        $found = true;
        // Skip this preset (delete it)
        continue;
      }
      $filtered[] = $preset;
    }

    if (!$found) {
      return new WP_Error('preset_not_found', __('Preset not found.', 'submittal-builder'));
    }

    // Save filtered list
    update_option('sfb_brand_presets', $filtered, false);

    return true;
  }

  /**
   * Set/unset a preset as default
   *
   * @param string $id Preset ID
   * @param bool $is_default Whether to set as default
   * @return bool|WP_Error True on success or error
   */
  public static function set_default_preset($id, $is_default) {
    $presets = self::get_presets();
    $found = false;

    foreach ($presets as &$preset) {
      if ($preset['id'] === $id) {
        $preset['is_default'] = (bool) $is_default;
        $preset['updated_at'] = current_time('mysql');
        $found = true;
      } else if ($is_default) {
        // If setting a new default, unset all others
        $preset['is_default'] = false;
      }
    }
    unset($preset);

    if (!$found) {
      return new WP_Error('preset_not_found', __('Preset not found.', 'submittal-builder'));
    }

    // Save
    update_option('sfb_brand_presets', $presets, false);

    return true;
  }

  /**
   * Get the default preset (if any)
   *
   * @return array|null Default preset or null
   */
  public static function get_default_preset() {
    $presets = self::get_presets();

    foreach ($presets as $preset) {
      if (!empty($preset['is_default'])) {
        return $preset;
      }
    }

    return null;
  }

  /**
   * AJAX: Create preset
   */
  public static function ajax_create_preset() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    // Get name
    $name = $_POST['name'] ?? '';

    // Create preset
    $result = self::create_preset($name);

    if (is_wp_error($result)) {
      wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success([
      'message' => __('Preset created successfully.', 'submittal-builder'),
      'preset' => $result,
    ]);
  }

  /**
   * AJAX: List presets
   */
  public static function ajax_list_presets() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    $presets = self::get_presets();

    wp_send_json_success(['presets' => $presets]);
  }

  /**
   * AJAX: Apply preset
   */
  public static function ajax_apply_preset() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    // Get ID
    $id = $_POST['id'] ?? '';

    // Apply preset
    $result = self::apply_preset($id);

    if (is_wp_error($result)) {
      wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success(['message' => __('Preset applied successfully. Refreshing page...', 'submittal-builder')]);
  }

  /**
   * AJAX: Rename preset
   */
  public static function ajax_rename_preset() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    // Get params
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';

    // Rename preset
    $result = self::rename_preset($id, $name);

    if (is_wp_error($result)) {
      wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success(['message' => __('Preset renamed successfully.', 'submittal-builder')]);
  }

  /**
   * AJAX: Delete preset
   */
  public static function ajax_delete_preset() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    // Get ID
    $id = $_POST['id'] ?? '';

    // Delete preset
    $result = self::delete_preset($id);

    if (is_wp_error($result)) {
      wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success(['message' => __('Preset deleted successfully.', 'submittal-builder')]);
  }

  /**
   * AJAX: Set default preset
   */
  public static function ajax_set_default_preset() {
    // Security checks
    if (!current_user_can('access_sfb_agency')) {
      wp_send_json_error(['message' => __('Unauthorized.', 'submittal-builder')], 403);
    }

    check_ajax_referer('sfb_brand_presets', 'nonce');

    // Check Agency license
    if (!self::is_agency_license()) {
      wp_send_json_error(['message' => __('Brand Presets require an Agency license.', 'submittal-builder')], 403);
    }

    // Get params
    $id = $_POST['id'] ?? '';
    $is_default = !empty($_POST['is_default']);

    // Set default
    $result = self::set_default_preset($id, $is_default);

    if (is_wp_error($result)) {
      wp_send_json_error(['message' => $result->get_error_message()], 400);
    }

    wp_send_json_success(['message' => __('Default preset updated.', 'submittal-builder')]);
  }

  /**
   * Check if current license is Agency tier
   *
   * @return bool True if Agency license
   */
  public static function is_agency_license() {
    // Dev override
    if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV) {
      return true;
    }

    // Check via WooCommerce license API if available
    if (function_exists('sfb_get_license_data')) {
      $license = sfb_get_license_data();

      // Check if license has agency tier flag
      if (!empty($license['tier']) && $license['tier'] === 'agency') {
        return true;
      }

      // Fallback: Check product variation or SKU if available
      if (!empty($license['product_name']) && stripos($license['product_name'], 'agency') !== false) {
        return true;
      }
    }

    // Final fallback: Check license option directly
    $lic = get_option('sfb_license', []);
    if (!empty($lic['tier']) && $lic['tier'] === 'agency') {
      return true;
    }

    return false;
  }
}
