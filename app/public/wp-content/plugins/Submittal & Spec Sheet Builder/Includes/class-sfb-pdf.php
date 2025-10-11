<?php
/**
 * SFB_Pdf - PDF generation wrapper (Phase 1 refactor)
 *
 * Thin wrapper around the existing PDF generator. Provides a structured
 * entry point while keeping all business logic in Includes/pdf-generator.php.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Pdf {

  /**
   * Initialize PDF hooks (if any needed in future)
   */
  public static function init() {
    // Currently no hooks needed - PDF generation is called directly
    // This class exists as a facade for future enhancements
  }

  /**
   * Generate PDF from frontend AJAX (Phase 6 facade)
   *
   * Centralized entry point for frontend PDF generation.
   * Delegates to the plugin instance's ajax_generate_frontend_pdf() method.
   *
   * @return void Sends JSON response via wp_send_json_success/error
   */
  public static function generate_frontend_pdf() {
    global $sfb_plugin;

    if ($sfb_plugin && method_exists($sfb_plugin, 'ajax_generate_frontend_pdf')) {
      $sfb_plugin->ajax_generate_frontend_pdf();
    } else {
      wp_send_json_error(['message' => __('PDF generator not available', 'submittal-builder')], 500);
    }
  }

  /**
   * Generate PDF packet from REST API (Phase 6 facade)
   *
   * Centralized entry point for REST API PDF generation.
   * Delegates to the plugin instance's api_generate_packet() method.
   *
   * @param WP_REST_Request $req Request object with packet data
   * @return array|WP_Error Response or error
   */
  public static function generate_packet($req) {
    global $sfb_plugin;

    if ($sfb_plugin && method_exists($sfb_plugin, 'api_generate_packet')) {
      return $sfb_plugin->api_generate_packet($req);
    }

    return new WP_Error('pdf_unavailable', __('PDF generator not available', 'submittal-builder'), ['status' => 500]);
  }

  /**
   * Get PDF upload directory
   *
   * @return string Path to PDF upload directory
   */
  public static function get_upload_dir() {
    $upload_dir = wp_upload_dir();
    $sfb_dir = trailingslashit($upload_dir['basedir']) . 'sfb/';

    if (!file_exists($sfb_dir)) {
      wp_mkdir_p($sfb_dir);
    }

    return $sfb_dir;
  }

  /**
   * Get PDF upload URL
   *
   * @return string URL to PDF upload directory
   */
  public static function get_upload_url() {
    $upload_dir = wp_upload_dir();
    return trailingslashit($upload_dir['baseurl']) . 'sfb/';
  }
}
