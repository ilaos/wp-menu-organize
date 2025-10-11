<?php
/**
 * Lead Capture Modal - Pro Feature
 *
 * Displays a modal to capture email and phone before PDF generation.
 * Only shown when sfb_lead_capture_enabled is true.
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

if (!defined('ABSPATH')) exit;

// Only render if lead capture is enabled
if (!SFB_Lead_Capture::is_enabled()) {
  return;
}
?>

<!-- Lead Capture Modal -->
<div id="sfb-lead-modal" class="sfb-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="sfb-lead-modal-title">
  <div class="sfb-modal-overlay" aria-hidden="true"></div>

  <div class="sfb-modal-content">
    <div class="sfb-modal-header">
      <h2 id="sfb-lead-modal-title"><?php esc_html_e('Get your PDF', 'submittal-builder'); ?></h2>
      <button type="button" class="sfb-modal-close" aria-label="<?php esc_attr_e('Close modal', 'submittal-builder'); ?>">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

    <div class="sfb-modal-body">
      <p class="sfb-lead-intro">
        <?php esc_html_e('Enter your information to receive your submittal packet.', 'submittal-builder'); ?>
      </p>

      <!-- Project name display (auto-populated if available) -->
      <div id="sfb-lead-project-name" class="sfb-lead-project-name" style="display: none;"></div>

      <form id="sfb-lead-form" class="sfb-lead-form">
        <!-- Hidden honeypot field (anti-bot) -->
        <input type="text" name="sfb_website" id="sfb_website" class="sfb-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

        <!-- Email (required) -->
        <div class="sfb-form-field">
          <label for="sfb-lead-email">
            <?php esc_html_e('Work Email', 'submittal-builder'); ?>
            <span class="sfb-required" aria-label="<?php esc_attr_e('required', 'submittal-builder'); ?>">*</span>
          </label>
          <input
            type="email"
            id="sfb-lead-email"
            name="email"
            required
            autocomplete="email"
            placeholder="<?php esc_attr_e('your.email@company.com', 'submittal-builder'); ?>"
            aria-required="true"
          >
        </div>

        <!-- Phone (optional) -->
        <div class="sfb-form-field">
          <label for="sfb-lead-phone">
            <?php esc_html_e('Phone', 'submittal-builder'); ?>
            <span class="sfb-optional"><?php esc_html_e('(optional)', 'submittal-builder'); ?></span>
          </label>
          <input
            type="tel"
            id="sfb-lead-phone"
            name="phone"
            autocomplete="tel"
            placeholder="<?php esc_attr_e('(555) 123-4567', 'submittal-builder'); ?>"
          >
        </div>

        <!-- Consent checkbox (optional) -->
        <div class="sfb-form-field sfb-checkbox-field">
          <label for="sfb-lead-consent">
            <input
              type="checkbox"
              id="sfb-lead-consent"
              name="consent"
              value="1"
            >
            <span><?php esc_html_e('Email me updates about products and projects', 'submittal-builder'); ?></span>
          </label>
        </div>

        <!-- Error message area -->
        <div id="sfb-lead-error" class="sfb-lead-error" role="alert" style="display: none;"></div>

        <!-- Submit button -->
        <div class="sfb-form-actions">
          <button
            type="submit"
            id="sfb-lead-submit"
            class="sfb-btn sfb-btn-primary"
            disabled
          >
            <span class="sfb-btn-text"><?php esc_html_e('Send me the PDF', 'submittal-builder'); ?></span>
            <span class="sfb-btn-spinner" style="display: none;">
              <span class="sfb-spinner-small"></span>
              <?php esc_html_e('Processing...', 'submittal-builder'); ?>
            </span>
          </button>
        </div>

        <!-- Privacy notice -->
        <p class="sfb-lead-privacy">
          <?php esc_html_e('We use your info to send the PDF and follow up about this project.', 'submittal-builder'); ?>
        </p>
      </form>
    </div>
  </div>
</div>
