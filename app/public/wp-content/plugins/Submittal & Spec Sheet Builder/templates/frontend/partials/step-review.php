<?php
/**
 * Frontend Builder - Step 2: Review (MVP - No Branding UI)
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;
?>

<div class="sfb-step-content">
  <div class="sfb-step-header">
    <h2><?php esc_html_e('Review', 'submittal-builder'); ?></h2>
    <p class="sfb-step-description">
      <?php esc_html_e('Review your selected products and add project details.', 'submittal-builder'); ?>
    </p>
  </div>

  <section class="sfb-review sfb-review--mvp" aria-label="Review your packet">
    <!-- Selected items -->
    <div class="sfb-review-main">
      <div id="sfb-selected-root"></div>

      <!-- Empty state (shown when no products selected) -->
      <div id="sfb-review-empty" class="sfb-review-empty" style="display: none;">
        <div class="sfb-review-empty__icon">📦</div>
        <p class="sfb-review-empty__text"><?php esc_html_e('No products selected.', 'submittal-builder'); ?></p>
        <button type="button" id="sfb-return-to-products" class="sfb-btn sfb-btn-secondary">
          <span class="sfb-icon-arrow-left">←</span>
          <?php esc_html_e('Return to Products', 'submittal-builder'); ?>
        </button>
      </div>

      <!-- Project details -->
      <section class="sfb-project" id="sfb-project" style="margin-top:16px;">
        <label style="display:block;margin:12px 0 6px;font-weight:800;">
          <?php esc_html_e('Project name', 'submittal-builder'); ?>
          <input
            type="text"
            id="sfb-project-name"
            placeholder="<?php esc_attr_e('e.g., Miller Hall – West Wing', 'submittal-builder'); ?>"
            style="width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;"
          />
          <span class="sfb-field-helper"><?php esc_html_e('Appears on the first page of your PDF.', 'submittal-builder'); ?></span>
        </label>
        <label style="display:block;margin:12px 0 6px;font-weight:800;">
          <?php esc_html_e('Project notes', 'submittal-builder'); ?> <span style="font-weight:400;color:#6b7280;">(<?php esc_html_e('optional', 'submittal-builder'); ?>)</span>
          <textarea
            id="sfb-project-notes"
            placeholder="<?php esc_attr_e('Add any notes or special instructions…', 'submittal-builder'); ?>"
            style="width:100%;min-height:110px;border:1px solid #e5e7eb;border-radius:8px;padding:10px;"
          ></textarea>
        </label>
      </section>
    </div>
  </section>

  <!-- Sticky Action Bar -->
  <div class="sfb-sticky-actions" id="sfb-sticky-actions">
    <button type="button" id="sfb-back-to-products-sticky" class="sfb-btn sfb-btn-secondary">
      <span class="sfb-icon-arrow-left">←</span>
      <?php esc_html_e('Back', 'submittal-builder'); ?>
    </button>
    <button type="button" id="sfb-generate-pdf-sticky" class="sfb-btn sfb-btn-primary" title="">
      <?php esc_html_e('Generate PDF', 'submittal-builder'); ?>
      <span class="sfb-icon-arrow-right">→</span>
    </button>
  </div>

  <!-- Subtle branding credit (Free tier) -->
  <div class="sfb-frontend-credit" style="text-align: center; margin-top: 16px; padding-bottom: 24px;">
    <?php echo sfb_brand_credit('frontend'); ?>
  </div>

  <!-- Toast notification container -->
  <div id="sfb-toast" class="sfb-toast" role="status" aria-live="polite"></div>

  <!-- Aria live region for screen readers -->
  <div class="sr-only" aria-live="polite" aria-atomic="true" id="sfb-review-status"></div>
</div>
