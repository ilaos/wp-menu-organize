<?php
/**
 * Industry Pack Helper Functions
 *
 * Provides centralized industry pack management for both Demo Tools
 * and Builder modal. Single source of truth for available packs.
 *
 * @package SubmittalBuilder
 * @since 1.0.4
 */

if (!defined('ABSPATH')) exit;

/**
 * Get available industry packs
 *
 * Scans assets/demo/*.json files and returns array of key => title
 *
 * @return array Industry packs with key => title pairs
 */
function sfb_get_industry_packs() {
  $plugin_dir = dirname(dirname(__FILE__));
  $demo_dir = $plugin_dir . '/assets/demo/';
  $packs = [];

  if (!is_dir($demo_dir)) {
    return $packs;
  }

  $files = glob($demo_dir . '*.json');
  foreach ($files as $file) {
    $key = basename($file, '.json');
    $data = json_decode(file_get_contents($file), true);
    $packs[$key] = $data['title'] ?? ucfirst($key);
  }

  // Sort alphabetically by title
  asort($packs);

  return $packs;
}

/**
 * Get default industry pack key
 *
 * Returns the first available pack, or 'electrical' if it exists
 *
 * @return string Default pack key
 */
function sfb_get_default_industry_pack() {
  $packs = sfb_get_industry_packs();

  if (empty($packs)) {
    return 'electrical';
  }

  // Prefer 'electrical' if available
  if (isset($packs['electrical'])) {
    return 'electrical';
  }

  // Otherwise return first pack
  $keys = array_keys($packs);
  return $keys[0];
}

/**
 * Get user's last selected industry pack
 *
 * Returns the last pack selected by the current user, or default if none
 *
 * @return string Industry pack key
 */
function sfb_get_user_last_industry_pack() {
  $user_id = get_current_user_id();

  if (!$user_id) {
    return sfb_get_default_industry_pack();
  }

  $last_pack = get_user_meta($user_id, 'sfb_last_industry_pack', true);

  // Verify the pack still exists
  if ($last_pack) {
    $packs = sfb_get_industry_packs();
    if (isset($packs[$last_pack])) {
      return $last_pack;
    }
  }

  return sfb_get_default_industry_pack();
}

/**
 * Save user's last selected industry pack
 *
 * @param string $pack Industry pack key
 * @return bool True on success, false on failure
 */
function sfb_save_user_last_industry_pack($pack) {
  $user_id = get_current_user_id();

  if (!$user_id) {
    return false;
  }

  // Verify pack exists
  $packs = sfb_get_industry_packs();
  if (!isset($packs[$pack])) {
    return false;
  }

  return update_user_meta($user_id, 'sfb_last_industry_pack', sanitize_text_field($pack));
}
