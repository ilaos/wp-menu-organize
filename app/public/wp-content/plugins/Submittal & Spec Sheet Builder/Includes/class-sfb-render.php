<?php
/**
 * SFB_Render - Frontend shortcode and asset registration (Phase 1 refactor)
 *
 * Thin wrapper that registers the [submittal_builder] shortcode and
 * forwards to existing render callback in the main plugin file.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Render {

  /**
   * Initialize frontend hooks
   */
  public static function init() {
    add_shortcode('submittal_builder', [__CLASS__, 'render_shortcode']);
  }

  /**
   * Render the submittal builder shortcode
   *
   * Forwards to existing shortcode_render method in main plugin class
   *
   * @param array $atts Shortcode attributes
   * @return string HTML output
   */
  public static function render_shortcode($atts = []) {
    global $sfb_plugin;

    if (!$sfb_plugin || !($sfb_plugin instanceof Submittal_Form_Builder)) {
      return '<p>' . esc_html__('Submittal Builder is not properly initialized.', 'submittal-builder') . '</p>';
    }

    // Forward to existing method in main class
    return $sfb_plugin->shortcode_render($atts);
  }
}
