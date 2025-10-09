<?php
/**
 * Frontend Builder - Step 2: Review & Brand
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;

$branding = get_option('sfb_settings', []);
$company_name = $branding['company_name'] ?? get_bloginfo('name');
$company_phone = $branding['company_phone'] ?? '';
$company_website = $branding['company_website'] ?? '';
$logo_url = $branding['logo_url'] ?? '';
$primary_color = $branding['primary_color'] ?? '#7B61FF';
$footer_note = $branding['footer_note'] ?? 'Professional submittal documentation';
?>

<div class="sfb-step-content">
  <div class="sfb-step-header">
    <h2><?php esc_html_e('Review & Brand', 'submittal-builder'); ?></h2>
    <p class="sfb-step-description">
      <?php esc_html_e('Review your selected products and add project details.', 'submittal-builder'); ?>
    </p>
  </div>

  <div class="sfb-review-layout">
    <!-- Selected Products with Grouping -->
    <div class="sfb-card">
      <div class="sfb-card-header sfb-review-header">
        <h3>
          <?php esc_html_e('Selected Products', 'submittal-builder'); ?>
          <span class="sfb-badge" id="sfb-review-count">(0)</span>
        </h3>
        <button type="button" id="sfb-review-clear-all" class="sfb-btn-text sfb-review-clear">
          <?php esc_html_e('Clear all', 'submittal-builder'); ?>
        </button>
      </div>
      <div class="sfb-card-body">
        <div id="sfb-review-products-list" class="sfb-review-products">
          <!-- Populated by JS with grouped items -->
        </div>
        <div id="sfb-review-empty" class="sfb-review-empty" style="display: none;">
          <div class="sfb-review-empty__icon">📦</div>
          <p class="sfb-review-empty__text"><?php esc_html_e('No products selected.', 'submittal-builder'); ?></p>
          <button type="button" id="sfb-return-to-products" class="sfb-btn sfb-btn-secondary">
            <span class="sfb-icon-arrow-left">←</span>
            <?php esc_html_e('Return to Products', 'submittal-builder'); ?>
          </button>
        </div>
      </div>
    </div>

    <!-- Brand Preview (Realistic) -->
    <div class="sfb-card sfb-brand-preview-card">
      <div class="sfb-card-header">
        <h3 title="<?php esc_attr_e('This is how your branding will appear in the generated PDF.', 'submittal-builder'); ?>" class="sfb-brand-preview-title">
          <?php esc_html_e('Brand Preview', 'submittal-builder'); ?>
        </h3>
      </div>
      <div class="sfb-card-body">
        <div class="sfb-brand-preview">
          <!-- Header band with primary color -->
          <div class="sfb-brand-preview__header" style="background-color: <?php echo esc_attr($primary_color); ?>;">
            <?php if ($logo_url): ?>
              <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company_name); ?>" class="sfb-brand-preview__logo" />
            <?php else: ?>
              <div class="sfb-brand-preview__logo-placeholder">
                <span><?php echo esc_html(substr($company_name, 0, 1)); ?></span>
              </div>
            <?php endif; ?>
          </div>

          <!-- Company details -->
          <div class="sfb-brand-preview__body">
            <p class="sfb-brand-preview__name"><?php echo esc_html($company_name); ?></p>
            <?php if ($company_phone): ?>
              <p class="sfb-brand-preview__detail"><?php echo esc_html($company_phone); ?></p>
            <?php endif; ?>
            <?php if ($company_website): ?>
              <p class="sfb-brand-preview__detail"><?php echo esc_html($company_website); ?></p>
            <?php endif; ?>

            <!-- Color swatch -->
            <div class="sfb-brand-preview__swatch">
              <span class="sfb-brand-preview__swatch-label"><?php esc_html_e('Primary Color:', 'submittal-builder'); ?></span>
              <span class="sfb-brand-preview__swatch-color" style="background-color: <?php echo esc_attr($primary_color); ?>;" title="<?php echo esc_attr($primary_color); ?>"></span>
              <span class="sfb-brand-preview__swatch-hex"><?php echo esc_html($primary_color); ?></span>
            </div>
          </div>

          <!-- Footer sample -->
          <div class="sfb-brand-preview__footer">
            <small><?php echo esc_html($footer_note); ?></small>
          </div>
        </div>

        <!-- Live sync note -->
        <p class="sfb-brand-note"><?php esc_html_e('Changes made in Branding are reflected automatically in your next PDF.', 'submittal-builder'); ?></p>

        <!-- Edit Branding link (admins only) -->
        <?php if (current_user_can('manage_options')): ?>
          <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-branding')); ?>" target="_blank" class="sfb-branding-edit">
            <?php esc_html_e('Edit Branding', 'submittal-builder'); ?> →
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Project Details -->
    <div class="sfb-card">
      <div class="sfb-card-header sfb-project-details-header">
        <h3><?php esc_html_e('Project Details', 'submittal-builder'); ?></h3>
      </div>
      <div class="sfb-card-body">
        <div class="sfb-form-group">
          <label for="sfb-project-name"><?php esc_html_e('Project Name', 'submittal-builder'); ?></label>
          <input
            type="text"
            id="sfb-project-name"
            name="project_name"
            class="sfb-input"
            placeholder="<?php esc_attr_e('e.g., Smith Building Renovation', 'submittal-builder'); ?>"
            maxlength="100"
          />
          <small class="sfb-field-hint"><?php esc_html_e('Appears on cover page subtitle', 'submittal-builder'); ?></small>
        </div>
        <div class="sfb-form-group">
          <label for="sfb-project-notes"><?php esc_html_e('Notes', 'submittal-builder'); ?> <span class="sfb-optional"><?php esc_html_e('(optional)', 'submittal-builder'); ?></span></label>
          <textarea
            id="sfb-project-notes"
            name="project_notes"
            class="sfb-textarea"
            rows="3"
            placeholder="<?php esc_attr_e('Add any notes or special instructions...', 'submittal-builder'); ?>"
            maxlength="500"
          ></textarea>
          <small class="sfb-field-hint"><?php esc_html_e('Appears in "Project Notes" section before specs', 'submittal-builder'); ?></small>
        </div>
      </div>
    </div>
  </div>

  <!-- Sticky Action Bar -->
  <div class="sfb-sticky-actions" id="sfb-sticky-actions">
    <button type="button" id="sfb-back-to-products-sticky" class="sfb-btn sfb-btn-secondary">
      <span class="sfb-icon-arrow-left">←</span>
      <?php esc_html_e('Back', 'submittal-builder'); ?>
    </button>
    <button type="button" id="sfb-generate-pdf-sticky" class="sfb-btn sfb-btn-primary">
      <?php esc_html_e('Generate PDF', 'submittal-builder'); ?>
      <span class="sfb-icon-arrow-right">→</span>
    </button>
  </div>
</div>
