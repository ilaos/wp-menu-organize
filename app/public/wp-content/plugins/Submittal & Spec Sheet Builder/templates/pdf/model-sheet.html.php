<?php
/**
 * PDF Template: Model/Product Sheet
 *
 * Available variables:
 * @var array $brand - Branding settings (theme, watermark, primary_color, footer_text)
 * @var array $product - Single product data (id, title, meta, path)
 * @var array $meta - Project metadata (for approve_block, approved_by, etc.)
 */
if (!defined('ABSPATH')) exit;

// Create anchor for TOC linking
$anchor_id = 'prod-' . intval($product['id']);
$product_title = sfb_text($product['title'] ?? '');
$crumbs = sfb_text_list($product['path'] ?? []);

// Theme-based color system
$accent = sfb_text($brand['primary_color'] ?: '#111827');
$theme = sfb_text($brand['theme'] ?? 'engineering');
$bar = ($theme === 'architectural') ? '#0ea5e9' :
       (($theme === 'corporate')    ? '#10b981' : $accent);
$watermark = sfb_text($brand['watermark'] ?? '');
?>
<?php if ($watermark !== ''): ?>
  <div style="position: fixed; top: 38%; left: 10%; right: 10%; text-align:center;
              font-size:64px; color:rgba(0,0,0,0.06); transform: rotate(-20deg); z-index:0;">
    <?= esc_html($watermark); ?>
  </div>
<?php endif; ?>
<a id="<?= esc_attr($anchor_id); ?>"></a>
<div style="max-width:800px; margin:0 auto; padding:28px 34px;">
  <h2 style="color:<?= esc_attr($bar); ?>; font-size:22px; font-weight:700; margin:0 0 6px;">
    <?= esc_html($product_title); ?>
  </h2>

  <?php if (count($crumbs) > 0): ?>
    <p style="font-size:13px; color:#6b7280; margin:0 0 10px; padding:0;">
      <?= esc_html(implode(' › ', $crumbs)); ?>
    </p>
  <?php endif; ?>

  <?php if (!empty($product['meta']) && is_array($product['meta'])): ?>
    <?php
      // Separate notes from other meta fields
      $notes = $product['meta']['notes'] ?? '';
      $specs = $product['meta'];
      if (isset($specs['notes'])) unset($specs['notes']);
      if (isset($specs['pdf_urls'])) unset($specs['pdf_urls']); // Skip pdf_urls in table

      // Auto-compact tables with many rows
      $row_count = count($specs);
      $compact = $row_count >= 12;
      $cell_pad = $compact ? '4px' : '6px';
      $fs = $compact ? '12px' : '13px';
    ?>
    <?php if (!empty($specs)): ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
      style="border-collapse:collapse; font-size:<?= $fs ?>; margin-top:6px; margin-bottom:20px;">
      <thead>
        <tr>
          <th style="width:42%; text-align:left; font-weight:700; background:<?= esc_attr($bar); ?>; color:#fff; padding:<?= $cell_pad ?> 8px; border:1px solid <?= esc_attr($bar); ?>;">
            Specification
          </th>
          <th style="text-align:left; font-weight:700; background:<?= esc_attr($bar); ?>; color:#fff; padding:<?= $cell_pad ?> 8px; border:1px solid <?= esc_attr($bar); ?>;">
            Value
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($specs as $k => $v): ?>
          <tr>
            <td style="font-weight:600; background:#f7f7f7; padding:<?= $cell_pad ?>; border:1px solid #e5e7eb; color:#374151;">
              <?= esc_html(ucwords(str_replace('_', ' ', sfb_text($k)))); ?>
            </td>
            <td style="padding:<?= $cell_pad ?>; border:1px solid #e5e7eb; color:#111827;">
              <?= esc_html(sfb_text($v)); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <?php $notes_text = sfb_text($notes); ?>
    <?php if ($notes_text !== ''): ?>
      <div style="margin-top:10px; margin-bottom:10px; font-size:12px;">
        <strong>Notes:</strong><br>
        <div style="padding:6px; background:#fafafa; border:1px solid #e5e7eb; margin-top:4px;">
          <?= nl2br(esc_html($notes_text)) ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (!empty($meta['approve_block'])): ?>
    <?php
    $approved_by = sfb_text($meta['approved_by'] ?? '');
    $approved_title = sfb_text($meta['approved_title'] ?? '');
    $approved_date = sfb_text($meta['approved_date'] ?? '');
    ?>
    <div style="margin-top:18px; border-top:1px solid #e5e7eb; padding-top:10px; font-size:12px;">
      <strong>Approval</strong>
      <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px; font-size:12px;">
        <tr>
          <td style="width:45%; padding:4px;">Approved By: <?= esc_html($approved_by) ?></td>
          <td style="width:35%; padding:4px;">Title: <?= esc_html($approved_title) ?></td>
          <td style="width:20%; padding:4px;">Date: <?= esc_html($approved_date) ?></td>
        </tr>
      </table>
    </div>
  <?php endif; ?>

  <?php $footer_text = sfb_text($brand['footer_text'] ?? ''); ?>
  <?php if ($footer_text !== ''): ?>
    <div style="margin-top:22px; font-size:11px; color:#888; text-align:center; border-top:1px solid #e5e7eb; padding-top:8px;">
      <?= esc_html($footer_text); ?>
    </div>
  <?php endif; ?>
</div>
<div style="page-break-after: always;"></div>
