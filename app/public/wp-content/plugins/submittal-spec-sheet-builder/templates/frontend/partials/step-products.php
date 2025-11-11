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
    <h2><?php esc_html_e('Pick Products', 'submittal-spec-sheet-builder'); ?></h2>
    <p class="sfb-step-description">
      <?php esc_html_e('Select the products you want to include in your submittal packet.', 'submittal-spec-sheet-builder'); ?>
    </p>
    <p class="sfb-help" style="margin:6px 0 14px; color:#6b7280; font-size:13px;">
      <?php esc_html_e('After you pick items, the Review screen lets you set quantities and add notes before you download your PDF.', 'submittal-spec-sheet-builder'); ?>
    </p>
  </div>

  <div class="sfb-products-layout">
    <!-- Left Sidebar: Category Filters -->
    <aside class="sfb-sidebar">
      <div class="sfb-search-box">
        <label for="sfb-product-search" class="sfb-sr-only"><?php esc_html_e('Search products', 'submittal-spec-sheet-builder'); ?></label>
        <input
          type="search"
          id="sfb-product-search"
          class="sfb-search-input"
          placeholder="<?php esc_attr_e('Search products, SKU, specs...', 'submittal-spec-sheet-builder'); ?>"
          autocomplete="off"
        />
        <span class="sfb-search-icon">🔍</span>
      </div>

      <div class="sfb-category-filters">
        <h3 class="sfb-filter-title"><?php esc_html_e('Categories', 'submittal-spec-sheet-builder'); ?></h3>
        <div id="sfb-category-list" class="sfb-category-accordion">
          <!-- Categories loaded via JS -->
          <div class="sfb-loading-placeholder">
            <?php esc_html_e('Loading categories...', 'submittal-spec-sheet-builder'); ?>
          </div>
        </div>
      </div>

      <div class="sfb-filter-actions">
        <button type="button" id="sfb-clear-filters" class="sfb-btn-text">
          <?php esc_html_e('Clear all filters', 'submittal-spec-sheet-builder'); ?>
        </button>
      </div>

      <?php
      // Show branding box for free version users
      $is_pro = function_exists('sfb_is_pro_active') && sfb_is_pro_active();
      if (!$is_pro):
      ?>
      <div class="sfb-free-branding">
        <div class="sfb-free-branding__content">
          <div class="sfb-free-branding__icon">✨</div>
          <div class="sfb-free-branding__text">
            <strong><?php esc_html_e('Powered by', 'submittal-spec-sheet-builder'); ?></strong>
            <span><?php esc_html_e('Submittal Builder', 'submittal-spec-sheet-builder'); ?></span>
          </div>
          <a
            href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder-pro/"
            target="_blank"
            rel="noopener noreferrer"
            class="sfb-free-branding__link"
            aria-label="<?php esc_attr_e('Learn more about Submittal Builder Pro', 'submittal-spec-sheet-builder'); ?>"
          >
            <?php esc_html_e('Upgrade to Pro', 'submittal-spec-sheet-builder'); ?> →
          </a>
        </div>
      </div>
      <?php endif; ?>
    </aside>

    <!-- Main Content: Product Grid -->
    <main class="sfb-products-main">
      <div class="sfb-products-toolbar">
        <div class="sfb-toolbar__search">
          <!-- Search moved here for better alignment -->
        </div>

        <div class="sfb-toolbar__count">
          <span id="sfb-results-count"><?php esc_html_e('Loading products...', 'submittal-spec-sheet-builder'); ?></span>
        </div>

        <!-- Expand/Collapse All Button -->
        <button type="button" id="sfb-toggle-all-groups" class="sfb-toggle-all-btn" title="<?php esc_attr_e('Expand all product groups', 'submittal-spec-sheet-builder'); ?>">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span id="sfb-toggle-all-text"><?php esc_html_e('Expand All', 'submittal-spec-sheet-builder'); ?></span>
        </button>

        <!-- Sticky Selection Counter -->
        <div id="sfb-selection-counter" class="sfb-selection-counter" style="display: none;">
          <span class="sfb-selection-count-text">
            <?php esc_html_e('Selected:', 'submittal-spec-sheet-builder'); ?> <strong id="sfb-selection-count-number">0</strong>
          </span>
          <button type="button" id="sfb-selection-view-btn" class="sfb-selection-view-btn">
            <?php esc_html_e('View', 'submittal-spec-sheet-builder'); ?> →
          </button>
        </div>
      </div>

      <div id="sfb-products-grid" class="sfb-products-grid">
        <!-- Products loaded via JS -->
        <div class="sfb-loading-placeholder">
          <div class="sfb-spinner-small"></div>
          <p><?php esc_html_e('Loading products...', 'submittal-spec-sheet-builder'); ?></p>
        </div>
      </div>

      <div id="sfb-products-empty" class="sfb-empty-state" style="display: none;">
        <div class="sfb-empty-icon">📦</div>
        <h3><?php esc_html_e('No products found', 'submittal-spec-sheet-builder'); ?></h3>
        <p><?php esc_html_e('Try adjusting your search or filters.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
    </main>
  </div>
</div>
