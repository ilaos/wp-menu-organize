<?php
/**
 * PDF Template: Summary Page
 *
 * Groups products by top-level category and displays key specifications
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

// Group items by top-level category (path[0])
$groups = [];
foreach ($items as $it) {
  $path = sfb_text_list($it['path'] ?? []);
  $cat = $path[0] ?? 'Miscellaneous';
  if (!isset($groups[$cat])) {
    $groups[$cat] = [];
  }
  $groups[$cat][] = $it;
}
?>
<?php if ($watermark !== ''): ?>
  <div style="position: fixed; top: 38%; left: 10%; right: 10%; text-align:center;
              font-size:64px; color:rgba(0,0,0,0.06); transform: rotate(-20deg); z-index:0;">
    <?= esc_html($watermark); ?>
  </div>
<?php endif; ?>
<div style="max-width:800px; margin:0 auto; padding:28px 34px;">
  <h2 style="color:<?= esc_attr($bar); ?>; font-size:24px; font-weight:700; margin:0 0 8px; border-bottom:2px solid <?= esc_attr($bar); ?>; padding-bottom:10px;">
    Summary
  </h2>
  <p style="font-size:13px; color:#6b7280; margin:0 0 20px; padding:0;">
    <?= count($items); ?> item<?= count($items) === 1 ? '' : 's'; ?> total • <?= count($groups); ?> categor<?= count($groups) === 1 ? 'y' : 'ies'; ?>
  </p>

  <?php foreach ($groups as $cat => $rows): ?>
    <h3 style="margin:16px 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
      <?= esc_html($cat); ?> <span style="color:#6b7280; font-weight:400;">(<?= count($rows); ?>)</span>
    </h3>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; font-size:12px; margin-bottom:16px;">
      <thead>
        <tr>
          <th style="text-align:left; padding:6px 8px; background:<?= esc_attr($bar); ?>; color:#fff; width:42%; border:1px solid <?= esc_attr($bar); ?>; font-weight:600;">
            Item
          </th>
          <th style="text-align:left; padding:6px 8px; background:<?= esc_attr($bar); ?>; color:#fff; border:1px solid <?= esc_attr($bar); ?>; font-weight:600;">
            Key Specifications
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $it): ?>
          <?php
          $m = $it['meta'] ?? [];
          $item_title = sfb_text($it['title'] ?? '');
          $item_path = sfb_text_list($it['path'] ?? []);
          ?>
          <tr>
            <td style="padding:6px 8px; border:1px solid #e5e7eb; background:#fff; vertical-align:top;">
              <strong style="color:#111827;"><?= esc_html($item_title); ?></strong><br>
              <span style="color:#6b7280; font-size:11px;"><?= esc_html(implode(' › ', $item_path)); ?></span>
            </td>
            <td style="padding:6px 8px; border:1px solid #e5e7eb; background:#fafafa; vertical-align:top;">
              <?php
                $bits = [];
                if (!empty($m['size']))      $bits[] = 'Size: ' . esc_html(sfb_text($m['size']));
                if (!empty($m['thickness'])) $bits[] = 'Thickness: ' . esc_html(sfb_text($m['thickness']));
                if (!empty($m['flange']))    $bits[] = 'Flange: ' . esc_html(sfb_text($m['flange']));
                if (!empty($m['ksi']))       $bits[] = 'KSI: ' . esc_html(sfb_text($m['ksi']));

                if (!empty($bits)) {
                  echo implode(' • ', $bits);
                } else {
                  echo '<span style="color:#9ca3af;">No specifications available</span>';
                }
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endforeach; ?>

  <?php if (!empty($brand['footer_text'])): ?>
    <div style="margin-top:40px; font-size:11px; color:#999; text-align:center;">
      <?= esc_html($brand['footer_text']); ?>
    </div>
  <?php endif; ?>
</div>
<div style="page-break-after: always;"></div>
