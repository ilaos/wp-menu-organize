<?php
/**
 * PDF Template: Cover Page
 *
 * Available variables:
 * @var array $brand - Branding settings (logo_url, company_name, company_address, company_phone, company_website, primary_color, footer_text, theme, watermark)
 * @var array $meta - Project metadata (project, contractor, submittal, include_leed)
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
<div style="text-align:center; padding:80px 40px; color:<?= esc_attr($accent); ?>;">
  <?php $logo_url = sfb_text($brand['logo_url'] ?? ''); ?>
  <?php if ($logo_url !== ''): ?>
    <img src="<?= esc_url($logo_url); ?>" style="max-height:120px; margin-bottom:30px;">
  <?php endif; ?>
  <?php $company_name = sfb_text($brand['company_name'] ?? ''); ?>
  <h1 style="font-size:32px; font-weight:700; margin:0 0 10px;"><?= esc_html($company_name !== '' ? $company_name : 'Submittal Package'); ?></h1>
  <h2 style="font-size:22px; color:#444; font-weight:600; margin:0 0 20px;">Specification & Submittal Packet</h2>

  <?php
  $project = sfb_text($meta['project'] ?? '');
  $contractor = sfb_text($meta['contractor'] ?? '');
  $submittal = sfb_text($meta['submittal'] ?? '');
  ?>
  <?php if ($project !== '' || $contractor !== '' || $submittal !== ''): ?>
    <div style="margin-top:40px; font-size:16px; color:#333;">
      <?php if ($project !== ''): ?>
        <p style="margin:8px 0;"><strong>Project:</strong> <?= esc_html($project); ?></p>
      <?php endif; ?>
      <?php if ($contractor !== ''): ?>
        <p style="margin:8px 0;"><strong>Contractor:</strong> <?= esc_html($contractor); ?></p>
      <?php endif; ?>
      <?php if ($submittal !== ''): ?>
        <p style="margin:8px 0;"><strong>Submittal #:</strong> <?= esc_html($submittal); ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div style="margin-top:40px; font-size:14px; color:#666;">
    <p style="margin:4px 0;"><strong>Date:</strong> <?= esc_html(sfb_text($meta['date'] ?? '') ?: date('F j, Y')); ?></p>
    <p style="margin:4px 0;"><strong>Total Products:</strong> <?= count($items); ?></p>
  </div>

  <?php if (!empty($meta['include_leed'])): ?>
    <div style="margin-top:30px; padding:10px 20px; background:#f0f9ff; border:2px solid <?= esc_attr($bar); ?>; border-radius:8px; display:inline-block;">
      <strong style="color:<?= esc_attr($bar); ?>;">✓ LEED Information Included</strong>
    </div>
  <?php endif; ?>

  <?php
  $company_address = sfb_text($brand['company_address'] ?? '');
  $company_phone = sfb_text($brand['company_phone'] ?? '');
  $company_website = sfb_text($brand['company_website'] ?? '');
  ?>
  <?php if ($company_address !== '' || $company_phone !== '' || $company_website !== ''): ?>
    <div style="margin-top:80px; font-size:13px; color:#666; line-height:1.6;">
      <?php if ($company_address !== ''): ?>
        <?= nl2br(esc_html($company_address)); ?><br>
      <?php endif; ?>
      <?php if ($company_phone !== ''): ?>
        <?= esc_html($company_phone); ?><br>
      <?php endif; ?>
      <?php if ($company_website !== ''): ?>
        <?= esc_html($company_website); ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php $footer_text = sfb_text($brand['footer_text'] ?? ''); ?>
  <?php if ($footer_text !== ''): ?>
    <div style="margin-top:40px; font-size:11px; color:#999;">
      <?= esc_html($footer_text); ?>
    </div>
  <?php endif; ?>
</div>
<div style="page-break-after: always;"></div>
