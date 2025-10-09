<?php
/**
 * PDF Template: Table of Contents
 *
 * Available variables:
 * @var array $brand - Branding settings (theme, watermark, primary_color)
 * @var array $meta - Project metadata
 * @var array $items - Array of selected products
 */
if (!defined('ABSPATH')) exit;

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
<div style="max-width:800px; margin:0 auto; padding:28px 34px;">
  <h2 style="color:<?= esc_attr($bar); ?>; font-size:24px; font-weight:700; margin:0 0 8px; border-bottom:2px solid <?= esc_attr($bar); ?>; padding-bottom:10px;">
    Table of Contents
  </h2>

  <ol style="font-size:14px; color:#333; margin:0; padding-left:18px; line-height:1.6;">
    <?php foreach ($items as $i => $it): ?>
      <?php
      $anchor_id = 'prod-' . intval($it['id']);
      $item_title = sfb_text($it['title'] ?? '');
      $item_crumbs = sfb_text_list($it['path'] ?? []);
      ?>
      <li style="margin:3px 0; padding:2px 0;">
        <a href="#<?= esc_attr($anchor_id); ?>" style="color:#1f2937; text-decoration:none;">
          <strong><?= esc_html($item_title); ?></strong> — <span style="color:#6b7280;"><?= esc_html(implode(' › ', $item_crumbs)); ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>

  <?php $footer_text = sfb_text($brand['footer_text'] ?? ''); ?>
  <?php if ($footer_text !== ''): ?>
    <div style="margin-top:40px; font-size:11px; color:#999; text-align:center;">
      <?= esc_html($footer_text); ?>
    </div>
  <?php endif; ?>
</div>
<div style="page-break-after: always;"></div>
