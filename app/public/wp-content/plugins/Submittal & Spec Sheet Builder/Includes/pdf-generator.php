<?php
/**
 * Professional PDF Generator for Submittal & Spec Sheet Builder
 *
 * Generates branded, print-ready PDF packets with:
 * - Cover page
 * - Table of contents (with internal links)
 * - Summary page (grouped by category)
 * - Product specification pages
 * - Professional headers/footers
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

if (!defined('ABSPATH')) exit;

class SFB_PDF_Generator {

  /**
   * Generate a professional PDF packet
   *
   * @param array $args Configuration array with branding, products, project data
   * @return string HTML for DomPDF to convert
   */
  public static function generate_packet($args = []) {
    $defaults = [
      'products'      => [],
      'project_name'  => '',
      'project_notes' => '',
      'branding'      => [],
      'pro_active'    => false,
      'paper_size'    => 'letter', // letter or a4
    ];

    $args = wp_parse_args($args, $defaults);

    // Get branding settings
    $branding = self::get_branding_settings($args['branding']);

    // Get paper size preference
    $paper_size = $args['paper_size'];

    // Group products by category
    $grouped_products = self::group_products_by_category($args['products']);

    // Start output buffer
    ob_start();

    self::render_html_head($branding, $paper_size);
    echo '<body>';

    // Render cover page
    self::render_cover_page($branding, $args['project_name']);

    // Render table of contents
    self::render_table_of_contents($grouped_products);

    // Render summary page
    self::render_summary_page($grouped_products, $branding, $args['project_notes']);

    // Render product specification pages
    self::render_product_pages($grouped_products, $branding, $args['project_name']);

    // Page numbering is now handled via canvas->page_script() in submittal-form-builder.php
    // No longer using inline <script type="text/php"> method

    echo '</body></html>';

    return ob_get_clean();
  }

  /**
   * Get branding settings with fallbacks
   *
   * @param array $custom_branding Custom branding overrides
   * @return array Complete branding configuration
   */
  private static function get_branding_settings($custom_branding = []) {
    // Use new brand settings structure from helper function
    $brand = sfb_get_brand_settings();

    $branding = [
      'company_name'    => $custom_branding['company_name'] ?? $brand['company']['name'] ?? get_bloginfo('name'),
      'company_address' => $custom_branding['company_address'] ?? $brand['company']['address'] ?? '',
      'company_phone'   => $custom_branding['company_phone'] ?? $brand['company']['phone'] ?? '',
      'company_website' => $custom_branding['company_website'] ?? $brand['company']['website'] ?? get_site_url(),
      'logo_url'        => $custom_branding['logo_url'] ?? $brand['company']['logo_url'] ?? '',
      'primary_color'   => $custom_branding['primary_color'] ?? $brand['visual']['primary_color'] ?? '#0E45E9',
      'footer_text'     => $custom_branding['footer_text'] ?? $brand['visual']['footer_text'] ?? '',
    ];

    // Ensure primary color has # prefix
    if (!empty($branding['primary_color']) && $branding['primary_color'][0] !== '#') {
      $branding['primary_color'] = '#' . $branding['primary_color'];
    }

    return $branding;
  }

  /**
   * Group products by category
   *
   * @param array $products Product array
   * @return array Products grouped by category
   */
  private static function group_products_by_category($products) {
    $grouped = [];

    foreach ($products as $product) {
      $category = $product['category'] ?? $product['path'][0] ?? __('Uncategorized', 'submittal-builder');
      $grouped[$category][] = $product;
    }

    return $grouped;
  }

  /**
   * Render HTML head with styles
   *
   * @param array $branding Branding settings
   * @param string $paper_size Paper size (letter or a4)
   */
  private static function render_html_head($branding, $paper_size = 'letter') {
    $primary = esc_attr($branding['primary_color']);
    $company = esc_attr($branding['company_name']);

    // Determine margins based on paper size
    // Comfortable margins for safe area with good readability
    if ($paper_size === 'a4') {
      $page_margin = 'margin: 20mm 15mm 20mm 15mm;';
      $page_size = 'size: A4;';
      $header_footer_offset = '15mm';
    } else {
      $page_margin = 'margin: 0.8in 0.6in 0.8in 0.6in;';
      $page_size = 'size: letter;';
      $header_footer_offset = '0.6in';
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php echo $company; ?> — Submittal Packet</title>
  <style>
    :root {
      --sfb-accent: <?php echo $primary; ?>;
    }

    /* Page margins with comfortable safe area */
    @page {
      <?php echo $page_size; ?>

      <?php echo $page_margin; ?>

    }

    /* Override margins for cover page only */
    @page cover {
      margin: 0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
      font-size: 10pt;
      line-height: 1.45;
      color: #1f2937;
      padding: 0 12pt; /* Add horizontal padding to all content */
    }

    /* ========== Cover Page ========== */
    .sfb-cover {
      page: cover;
      page-break-after: always;
      position: relative;
      text-align: center;
      padding-top: 80mm;
      margin: 0 -12pt; /* Negative margin to counteract body padding */
      padding-left: 0;
      padding-right: 0;
    }

    .cover-brand-bar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 60mm;
      background: <?php echo $primary; ?>;
      z-index: 0;
    }

    .cover-content {
      position: relative;
      z-index: 1;
    }

    .cover-logo {
      max-width: 200px;
      max-height: 150px;
      margin: 0 auto 30px;
      display: block;
    }

    .cover-company-name {
      font-size: 42pt;
      font-weight: 700;
      color: #111827;
      margin-bottom: 10px;
      line-height: 1.1;
    }

    .cover-project-name {
      font-size: 24pt;
      font-weight: 400;
      color: #374151;
      margin-bottom: 35px;
      line-height: 1.3;
    }

    .cover-subtitle {
      font-size: 16pt;
      font-weight: 600;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 1.5pt;
      margin-bottom: 25px;
    }

    .cover-date {
      font-size: 12pt;
      color: #9ca3af;
      margin-top: 40px;
    }

    .cover-footer {
      position: absolute;
      bottom: 20mm;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 9pt;
      color: #9ca3af;
    }

    /* ========== Table of Contents ========== */
    .sfb-toc {
      margin-top: 18pt;
      /* Removed page-break-after - will naturally break before Summary */
    }

    .page-title {
      font-size: 24pt;
      font-weight: 700;
      color: <?php echo $primary; ?>;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 3px solid <?php echo $primary; ?>;
      page-break-after: avoid;
    }

    .toc-section {
      margin-bottom: 20px;
    }

    .toc-section-title {
      font-size: 14pt;
      font-weight: 700;
      color: #374151;
      margin: 15px 0 10px;
      padding: 8px 12px;
      background: #f3f4f6;
      border-left: 4px solid <?php echo $primary; ?>;
      page-break-after: avoid;
    }

    /* TOC list with proper alignment after margin changes */
    .sfb-toc ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sfb-toc li {
      display: flex;
      gap: 8pt;
      padding: 6pt 0;
      border-bottom: 1px dotted #d1d5db;
    }

    .sfb-toc li:last-child {
      border-bottom: none;
    }

    .sfb-toc .sfb-toc-title {
      flex: 1 1 auto;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      font-size: 10.5pt;
      color: #1f2937;
    }

    .sfb-toc .sfb-toc-dots {
      flex: 0 0 auto;
      letter-spacing: 2px;
      color: #d1d5db;
    }

    .sfb-toc .sfb-toc-page {
      flex: 0 0 auto;
      min-width: 22pt;
      text-align: right;
      font-size: 9.5pt;
      color: #6b7280;
      font-weight: 600;
    }

    /* TOC links styling - clickable internal anchors */
    .sfb-toc a {
      text-decoration: none;
      color: inherit;
    }

    .sfb-toc a:hover {
      text-decoration: underline;
    }

    /* ========== Summary Page ========== */
    .sfb-summary {
      page-break-after: always;
      margin-top: 18pt;
    }

    .summary-intro {
      font-size: 10pt;
      color: #6b7280;
      margin-bottom: 20px;
      padding: 10pt 14pt;
      line-height: 1.45;
      background: #f9fafb;
      border-radius: 4pt;
    }

    .sfb-section {
      margin-top: 10pt;
      margin-bottom: 25px;
    }

    .sfb-section-title {
      font-size: 13pt;
      font-weight: 700;
      color: <?php echo $primary; ?>;
      margin: 14pt 0 8pt;
      padding: 10pt 14pt;
      background: linear-gradient(to right, <?php echo $primary; ?>15, transparent);
      border-left: 4px solid <?php echo $primary; ?>;
      page-break-after: avoid;
    }

    .sfb-avoid-break {
      page-break-inside: avoid;
    }

    .sfb-page-break-before {
      page-break-before: always;
    }

    /* ========== Tables with Generous Padding ========== */
    table.sfb-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      margin: 10pt 0;
      font-size: 9.5pt;
    }

    table.sfb-table thead {
      display: table-header-group;
    }

    table.sfb-table th {
      background: var(--sfb-accent, <?php echo $primary; ?>);
      color: #fff;
      font-weight: 700;
      padding: 11pt 12pt 8pt 12pt;
      text-align: left;
      text-transform: uppercase;
      letter-spacing: 0.5pt;
      font-size: 9pt;
      border: 1px solid var(--sfb-accent, <?php echo $primary; ?>);
      line-height: 1.45;
    }

    table.sfb-table tbody {
      display: table-row-group;
    }

    table.sfb-table tbody tr {
      page-break-inside: avoid;
    }

    table.sfb-table td {
      padding: 8pt 12pt;
      border: 1px solid #e5e7eb;
      vertical-align: top;
      word-wrap: break-word;
      line-height: 1.45;
    }

    table.sfb-table tr:nth-child(even) td {
      background: #F8F9FA;
    }

    table.sfb-table tr:nth-child(odd) td {
      background: white;
    }

    /* Product page body copy */
    .product-body p,
    .product-description p {
      line-height: 1.45;
      margin-bottom: 8pt;
    }

    /* ========== Product Pages ========== */
    .product-page {
      page-break-inside: avoid;
      /* Allow multiple products per page - only break when content doesn't fit */
      margin-bottom: 30pt;
    }

    .product-page:first-of-type {
      page-break-before: always; /* Force first product to start on new page after summary */
    }

    .product-header {
      margin-top: 0;
      margin-bottom: 15px;
      padding-bottom: 12px;
      border-bottom: 2px solid #e5e7eb;
      clear: both;
    }

    .product-category-badge {
      display: inline-block;
      padding: 5px 14px;
      background: <?php echo $primary; ?>;
      color: white;
      font-size: 8pt;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5pt;
      margin-bottom: 12px;
      border-radius: 3px;
    }

    .product-title {
      font-size: 20pt;
      font-weight: 700;
      color: #111827;
      margin-bottom: 8px;
      line-height: 1.2;
    }

    .product-subtitle {
      font-size: 11pt;
      color: #6b7280;
      margin-bottom: 15px;
    }

    .spec-section {
      margin: 20px 0;
    }

    .spec-section-title {
      font-size: 12pt;
      font-weight: 700;
      color: #374151;
      margin-bottom: 10px;
      padding-bottom: 5px;
      border-bottom: 1px solid #d1d5db;
    }

    .spec-table {
      margin: 10px 0;
    }

    .spec-table td:first-child {
      font-weight: 600;
      color: #4b5563;
      width: 35%;
      background: #f9fafb;
    }

    .product-description {
      margin: 15px 0;
      padding: 10pt 14pt;
      background: #f9fafb;
      border-left: 3px solid <?php echo $primary; ?>;
      font-size: 10pt;
      line-height: 1.45;
      color: #374151;
      border-radius: 4pt;
    }

    .product-image {
      max-width: 100%;
      height: auto;
      margin: 15px 0;
      border: 1px solid #e5e7eb;
    }

    /* ========== Spacers for Canvas Footer (prevents footer-only pages) ========== */
    .sfb-spacer-top {
      height: 0; /* Reduced since header is now relative, not fixed */
    }

    .sfb-spacer-bottom {
      height: 0; /* Removed - no longer needed with efficient page breaks */
    }

    .sfb-content-spacer-bottom {
      height: 28pt;
    }

    .sfb-header {
      position: relative; /* Changed from fixed to respect page margins */
      font-size: 8.5pt;
      color: #6b7280;
      padding-bottom: 6pt;
      margin-bottom: 12pt;
      border-bottom: 1px solid #e5e7eb;
    }

    .sfb-header-left {
      float: left;
      font-weight: 600;
    }

    .sfb-header-right {
      float: right;
      font-style: italic;
    }

    /* ========== Footer with Page Numbers ========== */
    .sfb-page-footer {
      /* Footer rendered via DomPDF script - no visible HTML element */
      display: none;
    }

    /* ========== External Links ========== */
    a {
      color: inherit;
    }

    a[href^="http"] {
      color: <?php echo $primary; ?>;
      text-decoration: underline;
    }

    .cover-footer a {
      color: inherit;
      text-decoration: none;
    }

    .cover-footer a:hover {
      text-decoration: underline;
    }

    /* ========== Utilities ========== */
    .text-center {
      text-align: center;
    }

    .text-muted {
      color: #6b7280;
    }

    .mb-10 {
      margin-bottom: 10px;
    }

    .mb-15 {
      margin-bottom: 15px;
    }

    .mt-20 {
      margin-top: 20px;
    }
  </style>
</head>
    <?php
  }

  /**
   * Render cover page
   *
   * @param array $branding Branding settings
   * @param string $project_name Project name
   */
  private static function render_cover_page($branding, $project_name = '') {
    $today = date_i18n(get_option('date_format'));

    // Prepare website URL with proper scheme
    $website = !empty($branding['company_website']) ? trim($branding['company_website']) : '';
    if ($website && !preg_match('~^https?://~i', $website)) {
      $website = 'https://' . $website;
    }
    ?>
<div class="sfb-cover">
  <div class="cover-brand-bar"></div>
  <div class="cover-content">
    <?php if (!empty($branding['logo_url'])): ?>
      <img src="<?php echo esc_url($branding['logo_url']); ?>"
           alt="<?php echo esc_attr($branding['company_name']); ?> Logo"
           class="cover-logo" />
    <?php endif; ?>

    <div class="cover-company-name">
      <?php if (!empty($website)): ?>
        <a href="<?php echo esc_url($website); ?>" style="color: inherit; text-decoration: none;">
          <?php echo esc_html($branding['company_name']); ?>
        </a>
      <?php else: ?>
        <?php echo esc_html($branding['company_name']); ?>
      <?php endif; ?>
    </div>

    <!-- Company Contact Information -->
    <?php if (!empty($branding['company_address']) || !empty($branding['company_phone'])): ?>
      <div style="margin-top: 15px; font-size: 10pt; color: #6b7280; line-height: 1.6;">
        <?php if (!empty($branding['company_address'])): ?>
          <div><?php echo nl2br(esc_html($branding['company_address'])); ?></div>
        <?php endif; ?>
        <?php if (!empty($branding['company_phone'])): ?>
          <div style="margin-top: 5px;"><?php echo esc_html($branding['company_phone']); ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($project_name)): ?>
      <div class="cover-project-name">
        <?php echo esc_html($project_name); ?>
      </div>
    <?php endif; ?>

    <div class="cover-subtitle">
      <?php esc_html_e('Submittal & Spec Sheet Packet', 'submittal-builder'); ?>
    </div>

    <div class="cover-date">
      <?php echo esc_html($today); ?>
    </div>

    <?php if (!empty($website)): ?>
      <div style="margin-top: 25px; font-size: 11pt; color: #6b7280;">
        <a href="<?php echo esc_url($website); ?>">
          <?php echo esc_html(preg_replace('~^https?://~i', '', $branding['company_website'])); ?>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="cover-footer">
    <?php
    // Pro users with white-label can customize, otherwise show attribution with link
    if (sfb_is_white_label_enabled()) {
      // White-label mode: show custom footer or nothing
      $brand = sfb_get_brand_settings();
      $custom_footer = trim($brand['white_label']['custom_footer'] ?? '');
      if ($custom_footer) {
        echo esc_html($custom_footer);
      }
    } else {
      // Free or Pro without white-label: show attribution with link
      ?>
      <?php esc_html_e('Generated using', 'submittal-builder'); ?>
      <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/">
        <?php esc_html_e('Submittal & Spec Sheet Builder', 'submittal-builder'); ?>
      </a>
      <?php esc_html_e('for WordPress', 'submittal-builder'); ?>
      <?php
    }
    ?>
  </div>
</div>
    <?php
  }

  /**
   * Render table of contents
   *
   * @param array $grouped_products Products grouped by category
   */
  private static function render_table_of_contents($grouped_products) {
    $page_num = 3; // Start after cover + TOC itself
    ?>
<div class="sfb-toc">
  <div class="sfb-spacer-top"></div>
  <h1 class="page-title"><?php esc_html_e('Table of Contents', 'submittal-builder'); ?></h1>

  <div class="toc-section">
    <div class="toc-section-title"><?php esc_html_e('Overview', 'submittal-builder'); ?></div>
    <ul>
      <li>
        <a href="#sfb-summary" class="sfb-toc-title">
          <?php esc_html_e('Summary', 'submittal-builder'); ?>
        </a>
        <span class="sfb-toc-dots">............</span>
        <span class="sfb-toc-page"><?php echo $page_num; ?></span>
      </li>
    </ul>
  </div>

  <div class="toc-section">
    <div class="toc-section-title"><?php esc_html_e('Product Specifications', 'submittal-builder'); ?></div>
    <ul>
      <?php
      $page_num++; // Summary page
      foreach ($grouped_products as $category => $products):
        foreach ($products as $product):
          $product_name = $product['title'] ?? $product['name'] ?? __('Unnamed Product', 'submittal-builder');
          // Use numeric ID if available, otherwise fallback to sanitized title
          $raw_id = $product['id'] ?? $product['node_id'] ?? null;
          $product_id = is_numeric($raw_id) ? intval($raw_id) : sanitize_title($product_name);
          $page_num++;
      ?>
      <li>
        <a href="#sfb-prod-<?php echo esc_attr($product_id); ?>" class="sfb-toc-link">
          <?php echo esc_html($product_name); ?>
          <span class="text-muted" style="font-size: 8.5pt;"> — <?php echo esc_html($category); ?></span>
        </a>
        <span class="sfb-toc-dots">............</span>
        <span class="sfb-toc-page"><?php echo $page_num; ?></span>
      </li>
      <?php
        endforeach;
      endforeach;
      ?>
    </ul>
  </div>

  <?php if (!sfb_is_white_label_enabled()): ?>
    <!-- Attribution link for free/non-white-label users (clickable) -->
    <div class="sfb-attrib" style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9pt; color: #9ca3af; text-align: center;">
      <?php esc_html_e('Generated using', 'submittal-builder'); ?>
      <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" style="color: #667eea; text-decoration: none;">
        <?php esc_html_e('Submittal & Spec Sheet Builder', 'submittal-builder'); ?>
      </a>
      <?php esc_html_e('for WordPress', 'submittal-builder'); ?>
    </div>
  <?php endif; ?>
</div>
    <?php
  }

  /**
   * Render summary page
   *
   * @param array $grouped_products Products grouped by category
   * @param array $branding Branding settings
   * @param string $project_notes Optional project notes
   */
  private static function render_summary_page($grouped_products, $branding, $project_notes = '') {
    $total_products = 0;
    foreach ($grouped_products as $products) {
      $total_products += count($products);
    }
    ?>
<div class="sfb-summary">
  <div class="sfb-spacer-top"></div>
  <h1 id="sfb-summary" class="page-title"><?php esc_html_e('Summary', 'submittal-builder'); ?></h1>

  <p class="summary-intro">
    <?php
    printf(
      esc_html__('This packet contains %1$d product(s) across %2$d categories.', 'submittal-builder'),
      $total_products,
      count($grouped_products)
    );
    ?>
  </p>

  <?php if (!empty($project_notes)): ?>
    <div class="product-description" style="margin: 20px 0;">
      <strong><?php esc_html_e('Project Notes:', 'submittal-builder'); ?></strong><br/>
      <?php echo nl2br(esc_html($project_notes)); ?>
    </div>
  <?php endif; ?>

  <?php foreach ($grouped_products as $category => $products):
    $category_slug = sanitize_title($category);
  ?>
    <div class="sfb-section sfb-avoid-break">
      <div id="sfb-cat-<?php echo esc_attr($category_slug); ?>" class="sfb-section-title">
        <?php echo esc_html($category); ?>
        <span class="text-muted" style="font-weight:400; font-size:10pt;">
          (<?php echo count($products); ?> <?php echo _n('item', 'items', count($products), 'submittal-builder'); ?>)
        </span>
      </div>

      <table class="sfb-table">
        <thead>
          <tr>
            <th style="width: 8%;"><?php esc_html_e('Qty', 'submittal-builder'); ?></th>
            <th style="width: 35%;"><?php esc_html_e('Product Name', 'submittal-builder'); ?></th>
            <th style="width: 27%;"><?php esc_html_e('Key Specifications', 'submittal-builder'); ?></th>
            <th style="width: 30%;"><?php esc_html_e('Notes', 'submittal-builder'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product):
            $product_name = $product['title'] ?? $product['name'] ?? __('Unnamed Product', 'submittal-builder');
            $specs = $product['specs'] ?? $product['specifications'] ?? [];
            $note = $product['note'] ?? $product['description'] ?? '';
            $quantity = $product['quantity'] ?? 1;

            // Get first 2-3 key specs for summary
            $key_specs = array_slice($specs, 0, 3, true);
            $spec_summary = [];
            foreach ($key_specs as $key => $value) {
              $spec_summary[] = ucfirst($key) . ': ' . $value;
            }
            $spec_text = implode('; ', $spec_summary);
          ?>
          <tr>
            <td style="text-align: center; font-weight: 600;"><?php echo esc_html($quantity); ?></td>
            <td><strong><?php echo esc_html($product_name); ?></strong></td>
            <td style="font-size: 9pt;"><?php echo esc_html($spec_text); ?></td>
            <td style="font-size: 9pt; font-style: italic;"><?php echo esc_html(wp_trim_words($note, 10)); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; ?>
</div>
    <?php
  }

  /**
   * Render product specification pages
   *
   * @param array $grouped_products Products grouped by category
   * @param array $branding Branding settings
   * @param string $project_name Project name
   */
  private static function render_product_pages($grouped_products, $branding, $project_name = '') {
    foreach ($grouped_products as $category => $products):
      foreach ($products as $product):
        self::render_product_page($product, $category, $branding, $project_name);
      endforeach;
    endforeach;
  }

  /**
   * Render individual product page
   *
   * @param array $product Product data
   * @param string $category Category name
   * @param array $branding Branding settings
   * @param string $project_name Project name
   */
  private static function render_product_page($product, $category, $branding, $project_name = '') {
    $product_name = $product['title'] ?? $product['name'] ?? __('Unnamed Product', 'submittal-builder');
    // Use numeric ID if available, otherwise fallback to sanitized title (must match TOC)
    $raw_id = $product['id'] ?? $product['node_id'] ?? null;
    $product_id = is_numeric($raw_id) ? intval($raw_id) : sanitize_title($product_name);
    $specs = $product['specs'] ?? $product['specifications'] ?? [];
    $description = $product['description'] ?? $product['note'] ?? '';
    $image_url = $product['image'] ?? $product['image_url'] ?? '';
    $subcategory = $product['path'][1] ?? '';
    $quantity = $product['quantity'] ?? 1;
    ?>
<div class="product-page">
  <!-- Page Header (shown on all product pages) -->
  <div class="sfb-header">
    <div class="sfb-header-left"><?php echo esc_html($branding['company_name']); ?></div>
    <?php if (!empty($project_name)): ?>
      <div class="sfb-header-right"><?php echo esc_html($project_name); ?></div>
    <?php endif; ?>
  </div>

  <div class="sfb-spacer-top"></div>

  <!-- Product Header -->
  <div class="product-header">
    <div class="product-category-badge">
      <?php echo esc_html($category); ?><?php echo $subcategory ? ' / ' . esc_html($subcategory) : ''; ?>
    </div>
    <h2 id="sfb-prod-<?php echo esc_attr($product_id); ?>" class="product-title">
      <?php echo esc_html($product_name); ?>
    </h2>
    <?php if ($quantity > 1): ?>
      <div class="product-subtitle">
        <strong><?php esc_html_e('Quantity:', 'submittal-builder'); ?></strong> <?php echo esc_html($quantity); ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Product Image (if available) -->
  <?php if (!empty($image_url)): ?>
    <div class="mb-15">
      <img src="<?php echo esc_url($image_url); ?>"
           alt="<?php echo esc_attr($product_name); ?>"
           class="product-image" />
    </div>
  <?php endif; ?>

  <!-- Product Description -->
  <?php if (!empty($description)): ?>
    <div class="product-description">
      <strong><?php esc_html_e('Description:', 'submittal-builder'); ?></strong><br/>
      <?php echo nl2br(esc_html($description)); ?>
    </div>
  <?php endif; ?>

  <!-- Specifications Table -->
  <?php if (!empty($specs)): ?>
    <div class="spec-section">
      <h3 class="spec-section-title"><?php esc_html_e('Technical Specifications', 'submittal-builder'); ?></h3>
      <table class="sfb-table spec-table">
        <tbody>
          <?php foreach ($specs as $key => $value): ?>
            <tr>
              <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?></td>
              <td><?php echo esc_html($value); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
    <?php
  }

  /**
   * Render page numbers using Dompdf PHP script in fixed footer
   *
   * @param array $branding Branding settings
   * @param bool $pro_active Whether Pro license is active
   */
  private static function render_page_numbers($branding, $pro_active = false) {
    // Get footer text - centralized via helper function
    $footerText = '';
    if ($pro_active && !empty($branding['footer_text'])) {
      $footerText = $branding['footer_text'];
    } else {
      // Use centralized brand credit helper (plain-text for PDF)
      $footerText = sfb_brand_credit_plain('pdf');
    }

    // Escape for inline PHP
    $footerTextEscaped = str_replace("'", "\\'", $footerText);
    ?>
<script type="text/php">
if (isset($pdf)) {
  $font = $fontMetrics->get_font("helvetica");
  $size = 9;
  $color = array(0.42, 0.45, 0.50);

  $w = $pdf->get_width();
  $h = $pdf->get_height();

  // Footer text on left (36pt from left edge, 36pt from bottom)
  $footerText = '<?php echo $footerTextEscaped; ?>';
  if (!empty($footerText)) {
    $pdf->text(36, $h - 36, $footerText, $font, $size, $color);
  }

  // Page numbers on right (120pt from right edge for space, 36pt from bottom)
  $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
  $pdf->text($w - 156, $h - 36, $pageText, $font, $size, $color);
}
</script>
    <?php
  }

  /**
   * Render footer (appears on all pages except cover) - DEPRECATED, kept for reference
   *
   * @param array $branding Branding settings
   * @param bool $pro_active Whether Pro license is active
   */
  private static function render_footer($branding, $pro_active = false) {
    ?>
<div class="sfb-footer">
  <div class="sfb-footer-text">
    <?php if ($pro_active && !empty($branding['footer_text'])): ?>
      <?php echo esc_html($branding['footer_text']); ?>
    <?php else: ?>
      <?php esc_html_e('Generated with Submittal & Spec Sheet Builder for WordPress', 'submittal-builder'); ?>
    <?php endif; ?>
  </div>
  <script type="text/php">
    if (isset($pdf)) {
      // Only render page number on pages 2+ (skip cover)
      if ($PAGE_NUM > 1) {
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 8;
        $pageText = "Page " . ($PAGE_NUM - 1) . " of " . ($PAGE_COUNT - 1);

        // Get page width and calculate center position
        $pageWidth = $pdf->get_width();
        $textWidth = $fontMetrics->getTextWidth($pageText, $font, $size);

        // Position: below the footer text, centered horizontally
        // Footer is at bottom 0.45in = ~32pt from bottom edge
        // Text line is at ~45pt, page number at ~57pt (12pt below text)
        $y = $pdf->get_height() - 27; // Position for page number below text
        $x = ($pageWidth - $textWidth) / 2;

        $pdf->text($x, $y, $pageText, $font, $size, [0.6, 0.64, 0.69]); // #9ca3af color
      }
    }
  </script>
</div>
    <?php
  }
}
