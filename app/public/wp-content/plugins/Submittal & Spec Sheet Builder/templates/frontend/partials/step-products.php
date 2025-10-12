<?php
/**
 * Frontend Builder - Step 1: Pick Products
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;
?>

<div class="sfb-step-content">
  <div class="sfb-step-header">
    <h2><?php esc_html_e('Pick Products', 'submittal-builder'); ?></h2>
    <p class="sfb-step-description">
      <?php esc_html_e('Select the products you want to include in your submittal packet.', 'submittal-builder'); ?>
    </p>
    <p class="sfb-help" style="margin:6px 0 14px; color:#6b7280; font-size:13px;">
      <?php esc_html_e('After you pick items, the Review screen lets you set quantities and add notes before you download your PDF.', 'submittal-builder'); ?>
    </p>
  </div>

  <div class="sfb-products-layout">
    <!-- Left Sidebar: Category Filters -->
    <aside class="sfb-sidebar">
      <div class="sfb-search-box">
        <label for="sfb-product-search" class="sfb-sr-only"><?php esc_html_e('Search products', 'submittal-builder'); ?></label>
        <input
          type="search"
          id="sfb-product-search"
          class="sfb-search-input"
          placeholder="<?php esc_attr_e('Search products, SKU, specs...', 'submittal-builder'); ?>"
          autocomplete="off"
        />
        <span class="sfb-search-icon">🔍</span>
      </div>

      <div class="sfb-category-filters">
        <h3 class="sfb-filter-title"><?php esc_html_e('Categories', 'submittal-builder'); ?></h3>
        <div id="sfb-category-list" class="sfb-category-accordion">
          <!-- Categories loaded via JS -->
          <div class="sfb-loading-placeholder">
            <?php esc_html_e('Loading categories...', 'submittal-builder'); ?>
          </div>
        </div>
      </div>

      <div class="sfb-filter-actions">
        <button type="button" id="sfb-clear-filters" class="sfb-btn-text">
          <?php esc_html_e('Clear all filters', 'submittal-builder'); ?>
        </button>
      </div>
    </aside>

    <!-- Main Content: Product Grid -->
    <main class="sfb-products-main">
      <div class="sfb-products-toolbar">
        <div class="sfb-toolbar__search">
          <!-- Search moved here for better alignment -->
        </div>

        <div class="sfb-toolbar__count">
          <span id="sfb-results-count"><?php esc_html_e('Loading products...', 'submittal-builder'); ?></span>
        </div>

        <!-- Sticky Selection Counter -->
        <div id="sfb-selection-counter" class="sfb-selection-counter" style="display: none;">
          <span class="sfb-selection-count-text">
            <?php esc_html_e('Selected:', 'submittal-builder'); ?> <strong id="sfb-selection-count-number">0</strong>
          </span>
          <button type="button" id="sfb-selection-view-btn" class="sfb-selection-view-btn">
            <?php esc_html_e('View', 'submittal-builder'); ?> →
          </button>
        </div>

        <div class="sfb-toolbar__viewtoggle sfb-view-toggle">
          <button type="button" class="sfb-view-btn sfb-view-grid active" data-view="grid" aria-label="<?php esc_attr_e('Grid view', 'submittal-builder'); ?>">
            <span class="sfb-icon-grid">⊞</span>
          </button>
          <button type="button" class="sfb-view-btn sfb-view-list" data-view="list" aria-label="<?php esc_attr_e('List view', 'submittal-builder'); ?>">
            <span class="sfb-icon-list">☰</span>
          </button>
        </div>
      </div>

      <div id="sfb-products-grid" class="sfb-products-grid" data-view="grid">
        <!-- Products loaded via JS -->
        <div class="sfb-loading-placeholder">
          <div class="sfb-spinner-small"></div>
          <p><?php esc_html_e('Loading products...', 'submittal-builder'); ?></p>
        </div>
      </div>

      <div id="sfb-products-empty" class="sfb-empty-state" style="display: none;">
        <div class="sfb-empty-icon">📦</div>
        <h3><?php esc_html_e('No products found', 'submittal-builder'); ?></h3>
        <p><?php esc_html_e('Try adjusting your search or filters.', 'submittal-builder'); ?></p>
      </div>
    </main>
  </div>
</div>
