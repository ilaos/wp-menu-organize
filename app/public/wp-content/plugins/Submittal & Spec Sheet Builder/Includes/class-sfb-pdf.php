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
   * Generate a PDF packet
   *
   * Thin wrapper around existing PDF generation logic.
   * Forwards to the Professional_PDF_Generator class.
   *
   * @param array $products Array of selected products
   * @param array $meta Metadata (project name, branding, etc.)
   * @return array Result with 'success', 'url', 'path', etc.
   */
  public static function generate($products, $meta = []) {
    // Ensure PDF generator is loaded
    if (!class_exists('Professional_PDF_Generator')) {
      require_once plugin_dir_path(__FILE__) . 'pdf-generator.php';
    }

    // Forward to existing generator
    $generator = new Professional_PDF_Generator();
    return $generator->generate($products, $meta);
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
