<?php
/**
 * SFB_Drafts - Draft management utilities (Phase 7 refactor)
 *
 * Handles draft purging and statistics. Extracted from main plugin file
 * to improve organization while keeping all behavior identical.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Drafts {

  /**
   * Initialize drafts hooks (if any needed in future)
   */
  public static function init() {
    // Currently no hooks needed - drafts are managed via AJAX
    // This class exists as an organizational wrapper
  }

  /**
   * Purge expired drafts
   *
   * Finds and deletes all drafts that have passed their expiration date.
   * Returns count of purged drafts and updated statistics.
   *
   * @return array Response array with 'success', 'data', 'message'
   */
  public static function purge_expired() {
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

    // Delete each expired draft
    foreach ($expired_ids as $post_id) {
      wp_delete_post($post_id, true);
    }

    if (defined('WP_DEBUG') && WP_DEBUG && !empty($expired_ids)) {
      error_log(sprintf('SFB: Purged %d expired drafts', count($expired_ids)));
    }

    $purged_count = count($expired_ids);

    // Get updated stats
    $stats = self::get_stats();

    // Build message
    $message = $purged_count > 0
      ? sprintf(__('✅ Purged %d expired draft(s) at %s', 'submittal-builder'), $purged_count, current_time('g:i A'))
      : __('⚠️ Nothing to purge — all clear', 'submittal-builder');

    return [
      'success' => true,
      'data' => [
        'purged' => $purged_count,
        'total' => $stats['total'],
        'expired' => $stats['expired'],
        'stats_text' => $stats['text'],
      ],
      'message' => $message
    ];
  }

  /**
   * Get draft statistics
   *
   * Returns total drafts count, expired count, and formatted text.
   *
   * @return array Stats with 'total', 'expired', 'text' keys
   */
  public static function get_stats() {
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
}
