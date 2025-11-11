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
    <!-- License Activation Section (Top Priority) -->
    <div class="sfb-license-activation-section">
      <div class="sfb-activation-card">
        <div class="sfb-activation-header">
          <span class="sfb-activation-icon">🔑</span>
          <h2><?php esc_html_e('Already Purchased? Activate Your License', 'submittal-spec-sheet-builder'); ?></h2>
        </div>
        <p class="sfb-activation-intro">
          <?php esc_html_e('Enter your license key and email below to unlock Pro or Agency features.', 'submittal-spec-sheet-builder'); ?>
        </p>

        <?php
        // Get current license details
        $current_lic = get_option('sfb_license', []);
        $current_key = $current_lic['key'] ?? '';
        $current_email = $current_lic['email'] ?? '';

        // Handle license activation
        $activation_message = '';
        $activation_type = '';
        if (isset($_POST['sfb_activate_license_quick']) && wp_verify_nonce($_POST['sfb_license_nonce'], 'sfb_license_action')) {
          $new_key = sanitize_text_field($_POST['license_key'] ?? '');
          $new_email = sanitize_email($_POST['license_email'] ?? '');

          if ($new_key && $new_email) {
            $result = sfb_activate_license($new_key, $new_email);

            if (is_wp_error($result)) {
              $activation_message = $result->get_error_message();
              $activation_type = 'error';
            } else {
              $activation_message = $result['message'];
              $activation_type = 'success';
              // Refresh page to update Pro status
              echo '<script>setTimeout(function(){ window.location.reload(); }, 1500);</script>';
            }
          } else {
            $activation_message = __('Please enter both license key and email address.', 'submittal-spec-sheet-builder');
            $activation_type = 'error';
          }
        }
        ?>

        <?php if ($activation_message): ?>
          <div class="notice notice-<?php echo esc_attr($activation_type); ?> inline" style="margin:16px 0;">
            <p><?php echo esc_html($activation_message); ?></p>
          </div>
        <?php endif; ?>

        <form method="post" action="" class="sfb-activation-form">
          <?php wp_nonce_field('sfb_license_action', 'sfb_license_nonce'); ?>
          <input type="hidden" name="sfb_activate_license_quick" value="1">

          <div class="sfb-form-row">
            <label for="license_key"><?php esc_html_e('License Key', 'submittal-spec-sheet-builder'); ?> <span style="color:#dc2626;">*</span></label>
            <input
              type="text"
              name="license_key"
              id="license_key"
              value="<?php echo esc_attr($current_key); ?>"
              placeholder="<?php esc_attr_e('SFB-XXXX-XXXX-XXXX-XXXX', 'submittal-spec-sheet-builder'); ?>"
              required
            >
            <p class="sfb-field-hint">
              <?php esc_html_e('Enter the license key you received after purchase.', 'submittal-spec-sheet-builder'); ?>
            </p>
          </div>

          <div class="sfb-form-row">
            <label for="license_email"><?php esc_html_e('License Email', 'submittal-spec-sheet-builder'); ?> <span style="color:#dc2626;">*</span></label>
            <input
              type="email"
              name="license_email"
              id="license_email"
              value="<?php echo esc_attr($current_email); ?>"
              placeholder="<?php esc_attr_e('your@email.com', 'submittal-spec-sheet-builder'); ?>"
              required
            >
            <p class="sfb-field-hint">
              <?php esc_html_e('The email address used during purchase (required for activation).', 'submittal-spec-sheet-builder'); ?>
            </p>
          </div>

          <div class="sfb-form-actions">
            <button type="submit" class="button button-primary button-large sfb-activate-btn">
              <?php esc_html_e('Activate License', 'submittal-spec-sheet-builder'); ?>
            </button>
          </div>
        </form>

        <div class="sfb-activation-help">
          <p>
            <strong><?php esc_html_e('Need help?', 'submittal-spec-sheet-builder'); ?></strong>
            <?php esc_html_e('Check your purchase confirmation email for your license key, or', 'submittal-spec-sheet-builder'); ?>
            <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer">
              <?php esc_html_e('contact support', 'submittal-spec-sheet-builder'); ?>
            </a>.
          </p>
        </div>
      </div>
    </div>

    <!-- Hero Banner -->
    <div class="sfb-hero-banner">
      <div class="sfb-hero-content">
        <h1><?php esc_html_e('Upgrade to Pro or Agency', 'submittal-spec-sheet-builder'); ?></h1>
        <p class="sfb-hero-subtitle">
          <?php esc_html_e('Unlock automation, white-label PDFs, lead capture, and advanced presentation tools.', 'submittal-spec-sheet-builder'); ?>
        </p>
        <div class="sfb-hero-cta">
          <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener noreferrer" class="sfb-btn-primary">
            <?php esc_html_e('View Pricing & Plans', 'submittal-spec-sheet-builder'); ?>
          </a>
          <p class="sfb-hero-guarantee">
            <?php esc_html_e('Pro: $99/year • Agency: $299/year • 30-day money-back guarantee', 'submittal-spec-sheet-builder'); ?>
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
        <h2><?php esc_html_e('Pro is Active', 'submittal-spec-sheet-builder'); ?></h2>
        <p><?php esc_html_e('All features unlocked. Thank you for your support!', 'submittal-spec-sheet-builder'); ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Feature Categories -->

  <!-- Create & Build (6 features - all Free) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">🛠️</span>
      <?php esc_html_e('Create & Build', 'submittal-spec-sheet-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Easy Submittal Builder', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Intuitive drag-and-drop interface to build professional submittals quickly.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Instant PDF Generation', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Generate beautiful PDF packets with one click - no external tools needed.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Smart Autosave', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Never lose your work - drafts are automatically saved as you build.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Product Catalog Editor', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Build and organize your product catalog with categories and specifications.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Unlimited Itemization', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add unlimited items and line entries to your submittals without restrictions.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card free-feature <?php echo $pro ? '' : 'enabled'; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Custom Logo & Colors', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-free"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add your company logo and brand colors to all generated PDFs.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if (!$pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Pro Features (6 features) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">⭐</span>
      <?php esc_html_e('Pro Features', 'submittal-spec-sheet-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Professional PDF Themes', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Choose from Architectural, Corporate, and other premium PDF design templates.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Watermark Protection', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Add custom watermarks to protect your PDFs and mark them as drafts or confidential.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Signature & Approval Block', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Include signature blocks and approval fields for professional documentation.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Lead Capture & Notifications', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Capture client information and receive instant email notifications on form submissions.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Activity Tracking Links', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Track when clients open your PDFs and monitor engagement with tracking links.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="sfb-feature-card pro-feature <?php echo $pro ? 'enabled' : ''; ?>">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('White-Label Mode', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Remove all plugin branding for a completely white-labeled client experience.', 'submittal-spec-sheet-builder'); ?></p>
        <?php if ($pro): ?>
          <div class="sfb-feature-status">
            <span class="dashicons dashicons-yes"></span>
            <?php esc_html_e('Included', 'submittal-spec-sheet-builder'); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Agency Features (6 features) -->
  <div class="sfb-feature-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">🏢</span>
      <?php esc_html_e('Agency Features', 'submittal-spec-sheet-builder'); ?>
    </h2>
    <div class="sfb-feature-grid">
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Multi-Brand Management', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Manage multiple client brands with separate logos, colors, and settings for each.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Brand Preset Library', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Save and reuse brand presets across multiple projects and client sites.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Client Handoff Mode', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Simplified interface for client handoffs with restricted access to agency features.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Agency Analytics Dashboard', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Comprehensive analytics across all clients and projects in one centralized dashboard.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Lead Routing Rules', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Automatically route leads to different team members or clients based on custom rules.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
      <div class="sfb-feature-card pro-feature">
        <div class="sfb-feature-header">
          <h3><?php esc_html_e('Team Operator Role', 'submittal-spec-sheet-builder'); ?></h3>
          <span class="sfb-feature-badge badge-pro"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></span>
        </div>
        <p class="sfb-feature-desc"><?php esc_html_e('Grant team members limited access to build submittals without full admin privileges.', 'submittal-spec-sheet-builder'); ?></p>
      </div>
    </div>
  </div>

  <!-- Comparison Table -->
  <div class="sfb-comparison-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">📋</span>
      <?php esc_html_e('Feature Comparison', 'submittal-spec-sheet-builder'); ?>
    </h2>

    <div class="sfb-comparison-table">
      <table>
        <thead>
          <tr>
            <th class="feature-col"><?php esc_html_e('Feature', 'submittal-spec-sheet-builder'); ?></th>
            <th class="tier-col"><?php esc_html_e('Free', 'submittal-spec-sheet-builder'); ?></th>
            <th class="tier-col"><?php esc_html_e('Pro', 'submittal-spec-sheet-builder'); ?></th>
            <th class="tier-col tier-col-agency"><?php esc_html_e('Agency', 'submittal-spec-sheet-builder'); ?></th>
          </tr>
        </thead>
        <tbody>
          <!-- Create & Build -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🛠️ <?php esc_html_e('Create & Build', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Easy Submittal Builder', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Instant PDF Generation', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Smart Autosave', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Product Catalog Editor', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Unlimited Itemization', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Branding & Presentation -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🎨 <?php esc_html_e('Branding & Presentation', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Custom Logo & Colors', 'submittal-spec-sheet-builder'); ?></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Professional PDF Themes', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Watermark Protection', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Signature & Approval Block', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Collaboration & Lead Capture -->
          <tr class="category-row">
            <td colspan="4">
              <strong>📧 <?php esc_html_e('Collaboration & Lead Capture', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Shareable Draft Links', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Lead Capture & Notifications', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Automatic Email Delivery', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Weekly Lead Reports', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Automation & Tracking -->
          <tr class="category-row">
            <td colspan="4">
              <strong>📊 <?php esc_html_e('Automation & Tracking', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Activity Tracking Links', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Lead Routing Rules', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Schedule Exports', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Agency-Level Tools -->
          <tr class="category-row">
            <td colspan="4">
              <strong>🏢 <?php esc_html_e('Agency-Level Tools', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('Multi-Brand Management', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Brand Preset Library', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Default-to-PDF Branding', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Client Handoff Mode', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Team Operator Role', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Agency Analytics Dashboard', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Save as Pack / Agency Library', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>

          <!-- Support & White-Labeling -->
          <tr class="category-row">
            <td colspan="4">
              <strong>💬 <?php esc_html_e('Support & White-Labeling', 'submittal-spec-sheet-builder'); ?></strong>
            </td>
          </tr>
          <tr>
            <td><?php esc_html_e('White-Label Mode', 'submittal-spec-sheet-builder'); ?></td>
            <td class="empty-cell">—</td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
            <td class="check-cell"><span class="dashicons dashicons-yes"></span></td>
          </tr>
          <tr>
            <td><?php esc_html_e('Priority Support & Updates', 'submittal-spec-sheet-builder'); ?></td>
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
        <h2><?php esc_html_e('Ready to upgrade?', 'submittal-spec-sheet-builder'); ?></h2>
        <p><?php esc_html_e('Choose the plan that fits your needs.', 'submittal-spec-sheet-builder'); ?></p>
        <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener noreferrer" class="sfb-btn-primary">
          <?php esc_html_e('View Pricing & Plans', 'submittal-spec-sheet-builder'); ?>
        </a>
        <p class="sfb-pricing-details">
          <strong><?php esc_html_e('Pro:', 'submittal-spec-sheet-builder'); ?></strong> <?php esc_html_e('$99/year — Perfect for individual professionals', 'submittal-spec-sheet-builder'); ?><br>
          <strong><?php esc_html_e('Agency:', 'submittal-spec-sheet-builder'); ?></strong> <?php esc_html_e('$299/year — Unlimited client sites with advanced features', 'submittal-spec-sheet-builder'); ?>
        </p>
      </div>

      <!-- Benefits -->
      <div class="sfb-benefits-grid">
        <div class="sfb-benefit">
          <span class="dashicons dashicons-clock"></span>
          <h4><?php esc_html_e('Save Hours', 'submittal-spec-sheet-builder'); ?></h4>
          <p><?php esc_html_e('Automated email delivery and archiving', 'submittal-spec-sheet-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-star-filled"></span>
          <h4><?php esc_html_e('Professional Output', 'submittal-spec-sheet-builder'); ?></h4>
          <p><?php esc_html_e('White-labeling and custom themes', 'submittal-spec-sheet-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-shield"></span>
          <h4><?php esc_html_e('Client Confidence', 'submittal-spec-sheet-builder'); ?></h4>
          <p><?php esc_html_e('Tracking links and signature blocks', 'submittal-spec-sheet-builder'); ?></p>
        </div>
        <div class="sfb-benefit">
          <span class="dashicons dashicons-portfolio"></span>
          <h4><?php esc_html_e('Agency-Ready', 'submittal-spec-sheet-builder'); ?></h4>
          <p><?php esc_html_e('Perfect for billing clients and managing projects', 'submittal-spec-sheet-builder'); ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Changelog -->
  <div class="sfb-changelog-section">
    <h2 class="sfb-section-title">
      <span class="sfb-category-icon">📝</span>
      <?php esc_html_e('What\'s New', 'submittal-spec-sheet-builder'); ?>
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
      <?php esc_html_e('Found a bug? Have a suggestion?', 'submittal-spec-sheet-builder'); ?>
      <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: none; font-weight: 600;">
        <?php esc_html_e('Tell us about it', 'submittal-spec-sheet-builder'); ?> →
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

/* License Activation Section */
.sfb-license-activation-section {
  margin: 35px 0 48px 0;
}

.sfb-activation-card {
  background: linear-gradient(135deg, #fef3c7 0%, #fff 100%);
  border: 2px solid #fbbf24;
  border-radius: 16px;
  padding: 40px;
  max-width: 700px;
  margin: 0 auto;
  box-shadow: 0 4px 16px rgba(251, 191, 36, 0.15);
}

.sfb-activation-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.sfb-activation-icon {
  font-size: 40px;
  line-height: 1;
}

.sfb-activation-header h2 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #111827;
}

.sfb-activation-intro {
  color: #6b7280;
  font-size: 16px;
  margin: 0 0 24px 0;
  line-height: 1.6;
}

.sfb-activation-form {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 20px;
}

.sfb-form-row {
  margin-bottom: 20px;
}

.sfb-form-row:last-child {
  margin-bottom: 0;
}

.sfb-form-row label {
  display: block;
  font-weight: 600;
  font-size: 14px;
  color: #374151;
  margin-bottom: 8px;
}

.sfb-form-row input[type="text"],
.sfb-form-row input[type="email"] {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.2s ease;
}

.sfb-form-row input[type="text"]:focus,
.sfb-form-row input[type="email"]:focus {
  border-color: #667eea;
  outline: none;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.sfb-field-hint {
  margin: 6px 0 0 0;
  font-size: 13px;
  color: #9ca3af;
  line-height: 1.4;
}

.sfb-form-actions {
  margin-top: 24px;
  text-align: center;
}

.sfb-activate-btn {
  background: #667eea !important;
  border-color: #667eea !important;
  color: #fff !important;
  padding: 12px 32px !important;
  font-size: 16px !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25) !important;
  transition: all 0.2s ease !important;
}

.sfb-activate-btn:hover {
  background: #5568d3 !important;
  border-color: #5568d3 !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.35) !important;
}

.sfb-activation-help {
  background: #f9fafb;
  border-radius: 8px;
  padding: 16px 20px;
  text-align: center;
}

.sfb-activation-help p {
  margin: 0;
  font-size: 14px;
  color: #6b7280;
  line-height: 1.6;
}

.sfb-activation-help a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.sfb-activation-help a:hover {
  text-decoration: underline;
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

  .sfb-activation-card {
    padding: 24px 20px;
  }

  .sfb-activation-header h2 {
    font-size: 20px;
  }
}
</style>
