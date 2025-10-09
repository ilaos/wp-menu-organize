<?php
/**
 * Uninstall Script for Submittal & Spec Sheet Builder
 *
 * This file is executed when the plugin is deleted from WordPress.
 * Handles license deactivation and optionally removes all plugin data.
 *
 * @package Submittal_Builder
 * @version 1.0.2
 */

// Exit if accessed directly or if not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Security check: Ensure we're in the admin and user has permissions
if (!current_user_can('activate_plugins')) {
    exit;
}

/**
 * Minimal remote license deactivation call
 *
 * Fallback function for when main plugin files are not available during uninstall.
 * Makes a direct API call to WooCommerce Software Add-on to release the license seat.
 *
 * @return void
 */
function sfb_uninstall_remote_deactivate() {
  $data = get_option('sfb_license_data');

  // No license data, nothing to deactivate
  if (empty($data) || empty($data['key']) || empty($data['email'])) {
    return;
  }

  // Get instance ID (site hash)
  $instance = !empty($data['instance']) ? $data['instance'] : md5(get_site_url());

  // Build deactivation request URL
  $url = add_query_arg([
    'request'     => 'deactivation',
    'email'       => $data['email'],
    'license_key' => $data['key'],
    'product_id'  => 'SUBMITTAL-BUILDER',
    'instance'    => $instance,
  ], 'https://webstuffguylabs.com/?wc-api=software-api');

  // Make remote request (non-blocking to avoid hanging uninstall)
  $response = wp_remote_get($url, [
    'timeout'   => 10,
    'sslverify' => true,
    'blocking'  => true,
  ]);

  // Log result if WP_DEBUG is enabled
  if (defined('WP_DEBUG') && WP_DEBUG) {
    if (is_wp_error($response)) {
      error_log('[SFB Uninstall] License deactivation API error: ' . $response->get_error_message());
    } else {
      $code = wp_remote_retrieve_response_code($response);
      error_log('[SFB Uninstall] License deactivation completed. Response code: ' . $code);
    }
  }
}

// ============================================================================
// STEP 1: License Deactivation
// ============================================================================

// Skip license deactivation in dev mode
if (!(defined('SFB_PRO_DEV') && SFB_PRO_DEV)) {
  // Check if auto-deactivation is enabled (default: true)
  $auto_deactivate = get_option('sfb_auto_deactivate_on_deactivate', true);

  if ($auto_deactivate) {
    // Try to use the main function if available (it won't be during normal uninstall)
    // This is here for consistency, but we'll use the fallback
    if (function_exists('sfb_deactivate_license')) {
      try {
        $result = sfb_deactivate_license();
        if (defined('WP_DEBUG') && WP_DEBUG) {
          if (is_wp_error($result)) {
            error_log('[SFB Uninstall] License deactivation error: ' . $result->get_error_message());
          } else {
            error_log('[SFB Uninstall] License deactivated successfully');
          }
        }
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
          error_log('[SFB Uninstall] License deactivation exception: ' . $e->getMessage());
        }
      }
    } else {
      // Fallback: make direct API call (this is the normal path during uninstall)
      sfb_uninstall_remote_deactivate();
    }
  }
}

// Clear license transient cache (always)
delete_transient('sfb_license_check_cache');

// ============================================================================
// STEP 2: Data Removal (Optional, based on settings)
// ============================================================================

// Check if user wants to remove all data (default: false to preserve settings)
$remove_all_data = get_option('sfb_remove_data_on_uninstall', false);

if ($remove_all_data) {
  /**
   * Remove plugin options
   */
  $options_to_remove = [
    // Legacy options
    'sfb_branding',
    'sfb_license',
    'sfb_packets',

    // Current options
    'sfb_settings',
    'sfb_license_data',

    // Behavioral settings
    'sfb_auto_deactivate_on_deactivate',
    'sfb_remove_data_on_uninstall',

    // External links
    'sfb_link_account',
    'sfb_link_invoices',
    'sfb_link_docs',
    'sfb_link_tutorials',
    'sfb_link_roadmap',
    'sfb_link_support',
    'sfb_link_renew',
    'sfb_link_pricing',
    'sfb_link_agency_license',
    'sfb_link_single_license',

    // Onboarding
    'sfb_onboarding_completed',
    'sfb_activation_redirect',
  ];

  foreach ($options_to_remove as $option) {
    delete_option($option);
  }

  /**
   * Remove user meta (welcome notice dismissals, etc.)
   */
  delete_metadata('user', 0, 'sfb_welcome_dismissed', '', true);

  /**
   * Remove custom post type data (drafts)
   */
  $draft_posts = get_posts([
    'post_type'      => 'sfb_draft',
    'posts_per_page' => -1,
    'post_status'    => 'any',
    'fields'         => 'ids',
  ]);

  foreach ($draft_posts as $post_id) {
    wp_delete_post($post_id, true); // Force delete, bypass trash
  }

  /**
   * Drop custom database tables
   */
  global $wpdb;

  $tables = [
      $wpdb->prefix . 'sfb_forms',
      $wpdb->prefix . 'sfb_nodes',
      $wpdb->prefix . 'sfb_shares',
  ];

  foreach ($tables as $table) {
      $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
  }

  /**
   * Remove uploaded files and directories
   *
   * Note: This removes ALL generated PDFs and archives.
   */
  $upload_dir = wp_upload_dir();
  $sfb_base_dir = trailingslashit($upload_dir['basedir']) . 'sfb/';

  if (is_dir($sfb_base_dir)) {
      // Remove all files in sfb directory and subdirectories
      $iterator = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($sfb_base_dir, RecursiveDirectoryIterator::SKIP_DOTS),
          RecursiveIteratorIterator::CHILD_FIRST
      );

      foreach ($iterator as $file) {
          if ($file->isDir()) {
              @rmdir($file->getRealPath());
          } else {
              @unlink($file->getRealPath());
          }
      }

      // Remove base directory
      @rmdir($sfb_base_dir);
  }

  /**
   * Clear any cached data
   */
  wp_cache_flush();

  // Log data removal
  if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[SFB Uninstall] All plugin data removed. Deleted ' . count($draft_posts) . ' drafts, ' . count($tables) . ' tables, and all uploaded files.');
  }
} else {
  // Data preservation mode - only remove behavioral settings
  // (Keep license data and settings so user can reinstall without reconfiguring)
  if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[SFB Uninstall] Data removal disabled - preserving plugin settings and license data for potential reinstall.');
  }
}
