<?php
/**
 * Frontend Builder - Step 3: Generate (Success State)
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;
?>

<div class="sfb-step-content">
  <div class="sfb-success-state">
    <div class="sfb-success-icon">✓</div>
    <h2><?php esc_html_e('Your PDF is Ready!', 'submittal-builder'); ?></h2>
    <p class="sfb-success-message">
      <?php esc_html_e('Your submittal packet has been generated successfully.', 'submittal-builder'); ?>
    </p>

    <div class="sfb-success-actions">
      <a href="#" id="sfb-open-pdf" class="sfb-btn sfb-btn-primary sfb-btn-large" target="_blank" rel="noopener">
        <?php esc_html_e('Open PDF', 'submittal-builder'); ?>
        <span class="sfb-icon-external">↗</span>
      </a>
      <button type="button" id="sfb-start-over" class="sfb-btn sfb-btn-secondary">
        <?php esc_html_e('Start Over', 'submittal-builder'); ?>
      </button>
    </div>

    <div class="sfb-pdf-info">
      <p class="sfb-hint">
        <?php esc_html_e('Tip: Save or print your PDF for future reference.', 'submittal-builder'); ?>
      </p>
    </div>
  </div>
</div>
