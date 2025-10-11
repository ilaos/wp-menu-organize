<?php
/**
 * SFB_Tools - Admin tools and diagnostics (Phase 7 refactor)
 *
 * Handles smoke tests and other diagnostic tools. Extracted from main plugin file
 * to improve organization while keeping all behavior identical.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Tools {

  /**
   * Initialize tools hooks (if any needed in future)
   */
  public static function init() {
    // Currently no hooks needed - tools are run via AJAX
    // This class exists as an organizational wrapper
  }

  /**
   * Run smoke test for draft system
   *
   * Creates a test draft, verifies it can be retrieved, then cleans up.
   * Returns success/failure result with message.
   *
   * @return array Response array with 'success', 'data', 'message'
   */
  public static function run_smoke_test() {
    try {
      // Access plugin instance for helper methods
      global $sfb_plugin;

      if (!$sfb_plugin || !($sfb_plugin instanceof SFB_Plugin)) {
        return [
          'success' => false,
          'data' => null,
          'message' => 'Plugin instance not available'
        ];
      }

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

      // Use reflection to access private methods
      $reflection = new ReflectionClass($sfb_plugin);

      $validate_method = $reflection->getMethod('validate_draft_payload');
      $validate_method->setAccessible(true);
      $validation = $validate_method->invoke($sfb_plugin, $test_payload);

      if (!$validation['valid']) {
        return [
          'success' => false,
          'data' => null,
          'message' => 'Validation failed: ' . implode('; ', $validation['errors'])
        ];
      }

      $rand_id_method = $reflection->getMethod('sfb_rand_id');
      $rand_id_method->setAccessible(true);
      $draft_id = $rand_id_method->invoke($sfb_plugin, 12);

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
          'data' => null,
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
          'data' => null,
          'message' => 'Failed to retrieve test draft'
        ];
      }

      $payload = get_post_meta($post_id, '_sfb_draft_payload', true);
      if (empty($payload) || !isset($payload['items'])) {
        wp_delete_post($post_id, true);
        return [
          'success' => false,
          'data' => null,
          'message' => 'Failed to retrieve draft payload'
        ];
      }

      // Delete test draft
      wp_delete_post($post_id, true);

      // Get updated stats
      $stats = SFB_Drafts::get_stats();

      return [
        'success' => true,
        'data' => [
          'draft_id' => $draft_id,
          'total' => $stats['total'],
          'expired' => $stats['expired'],
          'stats_text' => $stats['text'],
        ],
        'message' => __('✅ Smoke test passed at ', 'submittal-builder') . current_time('g:i A')
      ];

    } catch (\Throwable $e) {
      return [
        'success' => false,
        'data' => null,
        'message' => 'Test failed with exception: ' . $e->getMessage()
      ];
    }
  }
}
