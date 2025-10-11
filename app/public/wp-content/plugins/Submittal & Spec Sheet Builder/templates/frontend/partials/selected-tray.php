<?php
/**
 * Frontend Builder - Selected Products Tray
 * Live-updating, persistent tray for selected products
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;
?>

<!-- Live Selected Products Tray -->
<div id="sfb-selected-tray" class="sfb-selected-tray">
  <div id="sfb-tray-header" class="sfb-tray-header">
    <h3 class="sfb-tray-title">
      Selected <span id="sfb-tray-count-header">(0)</span>
    </h3>
    <button id="sfb-tray-toggle" class="sfb-tray-toggle" type="button"
            aria-label="<?php esc_attr_e('Close selection tray', 'submittal-builder'); ?>"
            aria-expanded="true"
            aria-controls="sfb-tray-body">
      <span class="sfb-tray-icon" aria-hidden="true">▼</span>
    </button>
  </div>

  <div id="sfb-tray-body" class="sfb-tray-body">
    <div id="sfb-tray-products-list" class="sfb-tray-products-list" role="list" aria-label="<?php esc_attr_e('Selected products', 'submittal-builder'); ?>">
      <div class="sfb-tray-empty">
        <p><?php esc_html_e('No products selected yet', 'submittal-builder'); ?></p>
      </div>
    </div>

    <div class="sfb-tray-actions">
      <button id="sfb-tray-clear-all" class="sfb-btn-text sfb-tray-btn-clear">
        <?php esc_html_e('Clear All', 'submittal-builder'); ?>
      </button>
      <button id="sfb-tray-continue" class="sfb-btn sfb-btn-primary" disabled>
        <?php esc_html_e('Continue to Review', 'submittal-builder'); ?>
        <span class="sfb-icon-arrow">→</span>
      </button>
    </div>
  </div>
</div>
