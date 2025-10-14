<?php
/**
 * Admin Template: Upgrade to Pro
 *
 * Modern SaaS-style upgrade screen with hero banner, feature grid, and comparison table.
 */
if (!defined('ABSPATH')) exit;

$pro      = sfb_is_pro_active();
$features = sfb_features();

// Group features
$groups = ['Core'=>[], 'Automation'=>[], 'Branding'=>[], 'Data'=>[], 'Distribution'=>[]];
foreach ($features as $k => $def) {
  $g = $def['group'] ?? 'Core';
  if (!isset($groups[$g])) $groups[$g] = [];
  $groups[$g][$k] = $def;
}
$reg =& sfb_pro_registry();

// Category emojis
$category_icons = [
  'Core' => '⚡',
  'Automation' => '🤖',
  'Branding' => '🎨',
  'Data' => '📊',
  'Distribution' => '🚀'
];
?>

<div class="wrap sfb-upgrade-wrap-modern">

  <?php if (!$pro): ?>
    <!-- Hero Banner -->
    <div class="sfb-hero-banner">
      <div class="sfb-hero-content">
        <h1><?php esc_html_e('Upgrade to Pro or Agency', 'submittal-builder'); ?></h1>
        <p class="sfb-hero-subtitle">
          <?php esc_html_e('Unlock automation, white-label PDFs, lead capture, and advanced presentation tools.', 'submittal-builder'); ?>
        </p>
        <div class="sfb-hero-cta">
          <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener noreferrer" class="sfb-btn-primary">
            <?php esc_html_e('View Pricing & Plans', 'submittal-builder'); ?>
          </a>
          <p class="sfb-hero-guarantee">
            <?php esc_html_e('Pro: $99/year • Agency: $299/year • 30-day money-back guarantee', 'submittal-builder'); ?>
          </p>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Pro Active Banner -->
    <div class="sfb-pro-active-banner">
      <div class="sfb-pro-checkmark">
        <span class="dashicons dashicons-yes-alt"></span>
      </div>
      <div>
        <h2><?php esc_html_e('Pro is Active', 'submittal-builder'); ?></h2>
        <p><?php esc_html_e('All features unlocked. Thank you for your support!', 'submittal-builder'); ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Feature Categories -->

  <!-- Create & Build (6 features - all Free) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">🛠️</span>
      <?php esc_html_e('Create & Build', 'submittal-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Easy Submittal Builder', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Intuitive drag-and-drop interface to build professional submittals quickly.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Instant PDF Generation', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Generate beautiful PDF packets with one click - no external tools needed.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Smart Autosave', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Never lose your work - drafts are automatically saved as you build.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Product Catalog Editor', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Build and organize your product catalog with categories and specifications.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Unlimited Itemization', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add unlimited items and line entries to your submittals without restrictions.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Custom Logo & Colors', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add your company logo and brand colors to all generated PDFs.', 'submittal-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Pro Features (6 features) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">⭐</span>
      <?php esc_html_e('Pro Features', 'submittal-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Professional PDF Themes', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Choose from Architectural, Corporate, and other premium PDF design templates.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Watermark Protection', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add custom watermarks to protect your PDFs and mark them as drafts or confidential.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Signature & Approval Block', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Include signature blocks and approval fields for professional documentation.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Lead Capture & Notifications', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Capture client information and receive instant email notifications on form submissions.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Activity Tracking Links', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Track when clients open your PDFs and monitor engagement with tracking links.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('White-Label Mode', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Remove all plugin branding for a completely white-labeled client experience.', 'submittal-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Agency Features (6 features) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">🏢</span>
      <?php esc_html_e('Agency Features', 'submittal-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Multi-Brand Management', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Manage multiple client brands with separate logos, colors, and settings for each.', 'submittal-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Brand Preset Library', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Save and reuse brand presets across multiple projects and client sites.', 'submittal-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Client Handoff Mode', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Simplified interface for client handoffs with restricted access to agency features.', 'submittal-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Agency Analytics Dashboard', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Comprehensive analytics across all clients and projects in one centralized dashboard.', 'submittal-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Lead Routing Rules', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Automatically route leads to different team members or clients based on custom rules.', 'submittal-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Team Operator Role', 'submittal-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Grant team members limited access to build submittals without full admin privileges.', 'submittal-builder'); ?></p>
      </div>
    </div>
  </div>

  <!-- Comparison Table -->
  <div class="sfb-comparison-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">📋</span>
      <?php esc_html_e('Feature Comparison', 'submittal-builder'); ?>
    </h2>

    <div class="sfb-comparison-table">
      <table>
        <thead>
          <tr>
            <th class="feature-col"><?php esc_html_e('Feature', 'submittal-builder'); ?></th>
            <th class="tier-col"><?php esc_html_e('Free', 'submittal-builder'); ?></th>
            <th class="tier-col"><?php esc_html_e('Pro', 'submittal-builder'); ?></th>
            <th class="tier-col tier-col-agency"><?php esc_html_e('Agency', 'submittal-builder'); ?></th>
          </tr>
        </thead>
        <tbody>
          <!-- Create & Build -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🛠️ <?php esc_html_e('Create & Build', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Easy Submittal Builder', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Instant PDF Generation', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Smart Autosave', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Product Catalog Editor', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Unlimited Itemization', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Branding & Presentation -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🎨 <?php esc_html_e('Branding & Presentation', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Custom Logo & Colors', 'submittal-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Professional PDF Themes', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Watermark Protection', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Signature & Approval Block', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Collaboration & Lead Capture -->
          <tr class="category-row">
            <td colspan="4">
              <strong>📧 <?php esc_html_e('Collaboration & Lead Capture', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Shareable Draft Links', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Lead Capture & Notifications', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Automatic Email Delivery', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Weekly Lead Reports', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Automation & Tracking -->
          <tr class="category-row">
            <td colspan="4">
              <strong>📊 <?php esc_html_e('Automation & Tracking', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Activity Tracking Links', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Lead Routing Rules', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Schedule Exports', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Agency-Level Tools -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🏢 <?php esc_html_e('Agency-Level Tools', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Multi-Brand Management', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Brand Preset Library', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Default-to-PDF Branding', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Client Handoff Mode', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Team Operator Role', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Agency Analytics Dashboard', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Save as Pack / Agency Library', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Support & White-Labeling -->
          <tr class="category-row">
            <td colspan="4">
              <strong>💬 <?php esc_html_e('Support & White-Labeling', 'submittal-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('White-Label Mode', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Priority Support & Updates', 'submittal-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (!$pro): ?>
    <!-- Bottom CTA -->
    <div class="sfb-bottom-cta">
      <div class="sfb-cta-content">
        <h2><?php esc_html_e('Ready to upgrade?', 'submittal-builder'); ?></h2>
        <p><?php esc_html_e('Choose the plan that fits your needs.', 'submittal-builder'); ?></p>
        <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener noreferrer" class="sfb-btn-primary">
          <?php esc_html_e('View Pricing & Plans', 'submittal-builder'); ?>
        </a>
        <p class="sfb-pricing-details">
          <strong><?php esc_html_e('Pro:', 'submittal-builder'); ?></strong> <?php esc_html_e('$99/year — Perfect for individual professionals', 'submittal-builder'); ?><br>
          <strong><?php esc_html_e('Agency:', 'submittal-builder'); ?></strong> <?php esc_html_e('$299/year — Unlimited client sites with advanced features', 'submittal-builder'); ?>
        </p>
      </div>

      <!-- Benefits -->
      <div class="sfb-benefits-grid">
        <div class="sfb-benefit">
          <span class="dashicons dashicons-clock"></span>
          <h4><?php esc_html_e('Save Hours', 'submittal-builder'); ?></h4>
          <p><?php esc_html_e('Automated email delivery and archiving', 'submittal-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-star-filled"></span>
          <h4><?php esc_html_e('Professional Output', 'submittal-builder'); ?></h4>
          <p><?php esc_html_e('White-labeling and custom themes', 'submittal-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-shield"></span>
          <h4><?php esc_html_e('Client Confidence', 'submittal-builder'); ?></h4>
          <p><?php esc_html_e('Tracking links and signature blocks', 'submittal-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-portfolio"></span>
          <h4><?php esc_html_e('Agency-Ready', 'submittal-builder'); ?></h4>
          <p><?php esc_html_e('Perfect for billing clients and managing projects', 'submittal-builder'); ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Changelog -->
  <div class="sfb-changelog-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">📝</span>
      <?php esc_html_e('What\'s New', 'submittal-builder'); ?>
    </h2>

    <div class="sfb-changelog-list">
      <?php
      $changelog = $reg['changelog'];
      usort($changelog, fn($a,$b)=>version_compare($b['version'],$a['version']));
      foreach ($changelog as $entry):
      ?>
        <div class="sfb-changelog-entry">
          <div class="sfb-changelog-header">
            <strong>v<?php echo esc_html($entry['version']); ?></strong>
            <span class="sfb-changelog-date"><?php echo esc_html($entry['date']); ?></span>
          </div>
          <?php if (!empty($entry['notes'])): ?>
            <ul class="sfb-changelog-notes">
              <?php foreach ($entry['notes'] as $n): ?>
                <li><?php echo esc_html($n); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <p class="sfb-registry-version">
    Registry v<?php echo esc_html($reg['version']); ?>
  </p>

  <!-- Feedback Footer -->
  <div style="margin-top: 40px; padding: 16px 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; text-align: center;">
    <p style="margin: 0; color: #6b7280; font-size: 14px;">
      <?php esc_html_e('Found a bug? Have a suggestion?', 'submittal-builder'); ?>
      <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: none; font-weight: 600;">
        <?php esc_html_e('Tell us about it', 'submittal-builder'); ?> →
      </a>
    </p>
  </div>

</div>

<style>
/* Modern SaaS Upgrade Page Styles */
.sfb-upgrade-wrap-modern {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0;
}

/* Hero Banner */
.sfb-hero-banner {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 60px 40px;
  text-align: center;
  color: #fff;
  margin-top: 35px;
  margin-bottom: 48px;
  box-shadow: 0 10px 40px rgba(118, 75, 162, 0.2);
}

.sfb-hero-content h1 {
  font-size: 42px;
  font-weight: 700;
  margin: 0 0 16px 0;
  color: #fff;
  line-height: 1.2;
}

.sfb-hero-subtitle {
  font-size: 20px;
  margin: 0 0 32px 0;
  opacity: 0.95;
  font-weight: 400;
}

.sfb-hero-cta {
  margin-top: 32px;
}

.sfb-btn-primary {
  display: inline-block;
  background: #fff;
  color: #667eea;
  padding: 16px 40px;
  font-size: 18px;
  font-weight: 600;
  border-radius: 8px;
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  transition: all 0.2s ease;
}

.sfb-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3);
  color: #667eea;
}

.sfb-hero-guarantee {
  margin-top: 16px;
  font-size: 14px;
  opacity: 0.9;
  color: #fff;
}

/* Pro Active Banner */
.sfb-pro-active-banner {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 12px;
  padding: 24px 32px;
  display: flex;
  align-items: center;
  gap: 20px;
  color: #fff;
  margin-top: 35px;
  margin-bottom: 48px;
  box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2);
}

.sfb-pro-checkmark {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.sfb-pro-checkmark .dashicons {
  font-size: 32px;
  width: 32px;
  height: 32px;
  color: #fff;
}

.sfb-pro-active-banner h2 {
  margin: 0 0 4px 0;
  font-size: 24px;
  color: #fff;
}

.sfb-pro-active-banner p {
  margin: 0;
  font-size: 14px;
  opacity: 0.95;
}

/* Feature Sections */
.sfb-feature-section {
  margin-bottom: 48px;
}

.sfb-section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 24px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 24px 0;
  padding-bottom: 12px;
  border-bottom: 2px solid #e5e7eb;
}

.sfb-category-icon {
  font-size: 28px;
  line-height: 1;
}

.sfb-feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.sfb-feature-card {
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
  transition: all 0.2s ease;
}

.sfb-feature-card:hover {
  border-color: #667eea;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.1);
  transform: translateY(-2px);
}

.sfb-feature-card.pro-feature {
  border-color: #ddd6fe;
  background: linear-gradient(135deg, #faf8ff 0%, #fff 100%);
}

.sfb-feature-card.enabled {
  border-color: #10b981;
  background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
}

.sfb-feature-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
  gap: 12px;
}

.sfb-feature-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  flex: 1;
}

.sfb-feature-badge {
  display: inline-flex;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  flex-shrink: 0;
}

.badge-pro {
  background: #7c3aed;
  color: #fff;
}

.badge-free {
  background: #e5e7eb;
  color: #6b7280;
}

.sfb-feature-desc {
  color: #6b7280;
  font-size: 14px;
  line-height: 1.6;
  margin: 0 0 16px 0;
}

.sfb-feature-status {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #10b981;
  font-size: 13px;
  font-weight: 600;
}

.sfb-feature-status .dashicons {
  font-size: 18px;
  width: 18px;
  height: 18px;
}

/* Comparison Table */
.sfb-comparison-section {
  margin-bottom: 48px;
}

.sfb-comparison-table {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.sfb-comparison-table table {
  width: 100%;
  border-collapse: collapse;
}

.sfb-comparison-table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.sfb-comparison-table thead th {
  padding: 16px 20px;
  font-weight: 600;
  text-align: left;
}

.sfb-comparison-table .tier-col {
  text-align: center;
  width: 100px;
}

.sfb-comparison-table tbody .tier-col-agency {
  background: linear-gradient(135deg, #faf5ff 0%, #fff 100%);
}

.sfb-comparison-table tbody tr {
  border-bottom: 1px solid #f3f4f6;
}

.sfb-comparison-table tbody td {
  padding: 14px 20px;
  color: #374151;
  font-size: 14px;
}

.sfb-comparison-table .category-row {
  background: #f9fafb;
}

.sfb-comparison-table .category-row td {
  padding: 12px 20px;
  font-weight: 600;
  color: #111827;
}

.sfb-comparison-table .check-cell {
  text-align: center;
  color: #10b981;
}

.sfb-comparison-table .empty-cell {
  text-align: center;
  color: #d1d5db;
}

.sfb-comparison-table .dashicons {
  font-size: 20px;
  width: 20px;
  height: 20px;
}

/* Bottom CTA */
.sfb-bottom-cta {
  background: linear-gradient(135deg, #faf8ff 0%, #f3f4f6 100%);
  border-radius: 16px;
  padding: 48px 40px;
  margin-bottom: 48px;
  border: 2px solid #e5e7eb;
}

.sfb-cta-content {
  text-align: center;
  margin-bottom: 40px;
}

.sfb-cta-content h2 {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 12px 0;
}

.sfb-cta-content p {
  font-size: 18px;
  color: #6b7280;
  margin: 0 0 24px 0;
}

.sfb-pricing-details {
  margin-top: 16px !important;
  font-size: 14px !important;
  color: #9ca3af !important;
}

.sfb-benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-top: 32px;
}

.sfb-benefit {
  text-align: center;
  padding: 20px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.sfb-benefit .dashicons {
  font-size: 40px;
  width: 40px;
  height: 40px;
  color: #667eea;
  margin-bottom: 12px;
}

.sfb-benefit h4 {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 8px 0;
}

.sfb-benefit p {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
}

/* Changelog */
.sfb-changelog-section {
  margin-bottom: 32px;
}

.sfb-changelog-list {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #e5e7eb;
}

.sfb-changelog-entry {
  padding: 16px 0;
  border-bottom: 1px solid #f3f4f6;
}

.sfb-changelog-entry:last-child {
  border-bottom: none;
}

.sfb-changelog-header {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin-bottom: 8px;
}

.sfb-changelog-header strong {
  color: #111827;
  font-size: 15px;
}

.sfb-changelog-date {
  color: #9ca3af;
  font-size: 13px;
}

.sfb-changelog-notes {
  margin: 8px 0 0 0;
  padding-left: 20px;
  list-style: disc;
}

.sfb-changelog-notes li {
  color: #374151;
  line-height: 1.6;
  margin: 4px 0;
  font-size: 14px;
}

.sfb-registry-version {
  text-align: center;
  color: #9ca3af;
  font-size: 12px;
  margin: 24px 0;
}

/* Responsive */
@media (max-width: 768px) {
  .sfb-hero-banner {
    padding: 40px 24px;
  }

  .sfb-hero-content h1 {
    font-size: 32px;
  }

  .sfb-hero-subtitle {
    font-size: 16px;
  }

  .sfb-feature-grid {
    grid-template-columns: 1fr;
  }

  .sfb-benefits-grid {
    grid-template-columns: 1fr;
  }

  .sfb-comparison-table {
    overflow-x: auto;
  }

  .sfb-comparison-table table {
    min-width: 500px;
  }
}
</style>
