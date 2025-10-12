<?php
/**
 * Frontend Builder - Progress Header
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;
?>

<div class="sfb-builder-header">
  <div class="sfb-header-left">
    <h1 class="sfb-builder-title">
      <?php
      // Pro feature: Show site name when lead capture is enabled
      if (class_exists('SFB_Lead_Capture') && SFB_Lead_Capture::is_enabled()) {
        $site_name = get_bloginfo('name');
        /* translators: %s: Site name */
        printf(esc_html__('%s\'s Submittal & Spec Sheet Builder', 'submittal-builder'), esc_html($site_name));
      } else {
        esc_html_e('Submittal & Spec Sheet Builder', 'submittal-builder');
      }
      ?>
    </h1>
    <div class="sfb-project-field-compact">
      <label for="sfb-header-project" class="sfb-sr-only"><?php esc_html_e('Project Name', 'submittal-builder'); ?></label>
      <input
        type="text"
        id="sfb-header-project"
        name="project_name"
        placeholder="<?php esc_attr_e('Project Name (optional)', 'submittal-builder'); ?>"
        class="sfb-input-compact"
        maxlength="100"
      />
    </div>
  </div>

  <div class="sfb-header-center">
    <div class="sfb-progress-pills">
      <div class="sfb-pill sfb-pill-active" data-step="1">
        <span class="sfb-pill-number">1</span>
        <span class="sfb-pill-label"><?php esc_html_e('Products', 'submittal-builder'); ?></span>
      </div>
      <div class="sfb-pill-arrow">→</div>
      <div class="sfb-pill" data-step="2">
        <span class="sfb-pill-number">2</span>
        <span class="sfb-pill-label"><?php esc_html_e('Review', 'submittal-builder'); ?></span>
      </div>
      <div class="sfb-pill-arrow">→</div>
      <div class="sfb-pill" data-step="3">
        <span class="sfb-pill-number">3</span>
        <span class="sfb-pill-label"><?php esc_html_e('Generate', 'submittal-builder'); ?></span>
      </div>
    </div>
  </div>

  <div class="sfb-header-right">
    <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/documentation/" target="_blank" rel="noopener noreferrer" class="sfb-help-link">
      <span class="sfb-icon-help">?</span>
      <?php esc_html_e('Need help?', 'submittal-builder'); ?>
    </a>
  </div>
</div>
