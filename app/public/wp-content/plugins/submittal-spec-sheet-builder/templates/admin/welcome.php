<?php
/**
 * Welcome Screen - Modern branded onboarding
 *
 * @package SubmittalBuilder
 * @since 1.0.4
 */

if (!defined('ABSPATH')) exit;

// Use robust logo URL with plugin constant
$logo_files = ['webstuffguy-labs.png', 'custom-wordpress-plugins-1.png'];
$logo_url = '';
foreach ($logo_files as $logo_file) {
  $logo_path = plugin_dir_path(SFB_PLUGIN_FILE) . 'assets/img/' . $logo_file;
  if (file_exists($logo_path)) {
    $logo_url = plugins_url('assets/img/' . $logo_file, SFB_PLUGIN_FILE);
    break;
  }
}

// Resolve admin links
$builder_url   = admin_url('admin.php?page=sfb');
$branding_url  = admin_url('admin.php?page=sfb-branding');
$settings_url  = admin_url('admin.php?page=sfb-settings');
$utilities_url = admin_url('admin.php?page=sfb-tools');
$upgrade_url   = 'https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder-pro/';
$license_url   = admin_url('admin.php?page=sfb-license');

// Check license tiers
$is_pro = function_exists('sfb_is_pro_active') && sfb_is_pro_active();
$is_agency = function_exists('sfb_is_agency_license') && sfb_is_agency_license();

// Determine user tier for display
$user_tier = 'free';
if ($is_agency) {
  $user_tier = 'agency';
} elseif ($is_pro) {
  $user_tier = 'pro';
}

// Get plugin version
$plugin_data = get_plugin_data(SFB_PLUGIN_FILE);
$plugin_version = $plugin_data['Version'];

// Get quick stats
global $wpdb;
$stats = [
  'products' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}sfb_nodes WHERE node_type = 'model'"),
  'categories' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}sfb_nodes WHERE node_type = 'category'"),
  'pdfs_month' => 0, // Legacy wp_sfb_shares table removed in v1.0.2 - using custom post type 'sfb_draft' instead
];

?>
<div class="wrap sfb-welcome-modern">

  <!-- Hero Header -->
  <div class="sfb-welcome-hero">
    <div class="sfb-welcome-hero-content">
      <div class="sfb-welcome-branding">
        <?php if ($logo_url): ?>
          <img src="<?php echo esc_url($logo_url); ?>" alt="Webstuffguy Labs" class="sfb-welcome-logo" />
        <?php endif; ?>
        <div class="sfb-welcome-title-group">
          <h1><?php esc_html_e('Submittal & Spec Sheet Builder', 'submittal-spec-sheet-builder'); ?></h1>
          <p class="sfb-welcome-tagline">
            <?php esc_html_e('Built by', 'submittal-spec-sheet-builder'); ?>
            <a href="https://webstuffguylabs.com/" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: none; border-bottom: 1px dotted currentColor;">
              <?php esc_html_e('Webstuffguy Labs', 'submittal-spec-sheet-builder'); ?>
            </a>
          </p>
        </div>
      </div>
      <div class="sfb-welcome-hero-actions">
        <a href="<?php echo esc_url($builder_url); ?>" class="button button-primary button-hero">
          <span class="dashicons dashicons-admin-site-alt3"></span>
          <?php esc_html_e('Launch Builder', 'submittal-spec-sheet-builder'); ?>
        </a>
        <?php if ($user_tier === 'free'): ?>
          <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary button-hero">
            <span class="dashicons dashicons-star-filled"></span>
            <?php esc_html_e('Upgrade to Pro or Agency', 'submittal-spec-sheet-builder'); ?>
          </a>
        <?php elseif ($user_tier === 'pro'): ?>
          <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary button-hero">
            <span class="dashicons dashicons-businessman"></span>
            <?php esc_html_e('Upgrade to Agency', 'submittal-spec-sheet-builder'); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
    <!-- Quick Stats -->
    <div class="sfb-welcome-stats">
      <div class="sfb-stat">
        <span class="sfb-stat-number"><?php echo esc_html(number_format_i18n($stats['products'])); ?></span>
        <span class="sfb-stat-label"><?php esc_html_e('Products', 'submittal-spec-sheet-builder'); ?></span>
      </div>
      <div class="sfb-stat">
        <span class="sfb-stat-number"><?php echo esc_html(number_format_i18n($stats['categories'])); ?></span>
        <span class="sfb-stat-label"><?php esc_html_e('Categories', 'submittal-spec-sheet-builder'); ?></span>
      </div>
      <div class="sfb-stat">
        <span class="sfb-stat-number"><?php echo esc_html(number_format_i18n($stats['pdfs_month'])); ?></span>
        <span class="sfb-stat-label"><?php esc_html_e('PDFs This Month', 'submittal-spec-sheet-builder'); ?></span>
      </div>
      <div class="sfb-stat">
        <span class="sfb-stat-number">v<?php echo esc_html($plugin_version); ?></span>
        <span class="sfb-stat-label"><?php esc_html_e('Plugin Version', 'submittal-spec-sheet-builder'); ?></span>
      </div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="sfb-welcome-content">

    <!-- Quick Actions -->
    <div class="sfb-welcome-section sfb-welcome-quick-actions">
      <h2><?php esc_html_e('Quick Actions', 'submittal-spec-sheet-builder'); ?></h2>
      <div class="sfb-quick-action-grid">

        <a href="<?php echo esc_url($builder_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-builder">
            <span class="dashicons dashicons-editor-table"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Build Your Catalog', 'submittal-spec-sheet-builder'); ?></h3>
            <p><?php esc_html_e('Add products, categories, and specifications', 'submittal-spec-sheet-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($branding_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-branding">
            <span class="dashicons dashicons-art"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Customize Branding', 'submittal-spec-sheet-builder'); ?></h3>
            <p><?php esc_html_e('Set your logo, colors, and company details', 'submittal-spec-sheet-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($settings_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-settings">
            <span class="dashicons dashicons-admin-settings"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Configure Settings', 'submittal-spec-sheet-builder'); ?></h3>
            <p><?php esc_html_e('Manage features, PDFs, and preferences', 'submittal-spec-sheet-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($utilities_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-utilities">
            <span class="dashicons dashicons-admin-tools"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Run Utilities', 'submittal-spec-sheet-builder'); ?></h3>
            <p><?php esc_html_e('Database cleanup, diagnostics, and tools', 'submittal-spec-sheet-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="sfb-welcome-two-col">

      <!-- Getting Started -->
      <div class="sfb-welcome-section sfb-welcome-card">
        <div class="sfb-welcome-card-header">
          <span class="dashicons dashicons-flag"></span>
          <h2><?php esc_html_e('Getting Started', 'submittal-spec-sheet-builder'); ?></h2>
        </div>
        <div class="sfb-welcome-checklist">
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">1</span>
            <div>
              <h4><?php esc_html_e('Set up your branding', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Upload your logo and choose your brand colors.', 'submittal-spec-sheet-builder'); ?></p>
              <a href="<?php echo esc_url($branding_url); ?>"><?php esc_html_e('Branding Settings', 'submittal-spec-sheet-builder'); ?> →</a>
            </div>
          </div>
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">2</span>
            <div>
              <h4><?php esc_html_e('Build your product catalog', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Add categories, products, and specifications to your catalog.', 'submittal-spec-sheet-builder'); ?></p>
              <a href="<?php echo esc_url($builder_url); ?>"><?php esc_html_e('Open Builder', 'submittal-spec-sheet-builder'); ?> →</a>
            </div>
          </div>
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">3</span>
            <div>
              <h4><?php esc_html_e('Publish on your site', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Add the shortcode to any page or post:', 'submittal-spec-sheet-builder'); ?></p>
              <div class="sfb-shortcode-box">
                <code id="sfb-shortcode">[submittal_builder]</code>
                <button class="button button-small" id="sfb-copy-btn" type="button">
                  <span class="dashicons dashicons-admin-page"></span>
                  <?php esc_html_e('Copy', 'submittal-spec-sheet-builder'); ?>
                </button>
              </div>
              <span class="sfb-copy-feedback" id="sfb-copy-feedback" style="display: none;"><?php esc_html_e('✓ Copied!', 'submittal-spec-sheet-builder'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Resources / Support -->
      <div class="sfb-welcome-section sfb-welcome-card">
        <div class="sfb-welcome-card-header">
          <span class="dashicons dashicons-book-alt"></span>
          <h2><?php esc_html_e('Resources & Support', 'submittal-spec-sheet-builder'); ?></h2>
        </div>
        <div class="sfb-resource-list">
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-media-document"></span>
            <div>
              <h4><?php esc_html_e('Documentation', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Learn how to use all features', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/docs/submittal-user-guide/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('View Docs', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-sos"></span>
            <div>
              <h4><?php esc_html_e('Get Support', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Need help? Contact our support team', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Contact Support', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php if ($user_tier === 'free'): ?>
          <div class="sfb-resource-item sfb-resource-highlight">
            <span class="dashicons dashicons-star-filled"></span>
            <div>
              <h4><?php esc_html_e('Upgrade to Pro or Agency', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Unlock PDF themes, watermarks, signature blocks, tracking, lead capture, and more', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder-pro/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('See Pro Features', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php elseif ($user_tier === 'pro'): ?>
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-admin-network"></span>
            <div>
              <h4><?php esc_html_e('Pro Features Guide', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Learn about themes, watermarks, and signature blocks', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/docs/submittal-user-guide/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('View Pro Docs', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <div class="sfb-resource-item sfb-resource-highlight">
            <span class="dashicons dashicons-businessman"></span>
            <div>
              <h4><?php esc_html_e('Upgrade to Agency', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Unlock white-label mode, lead routing, webhooks, analytics, and multi-client features', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder-pro/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('See Agency Features', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php elseif ($user_tier === 'agency'): ?>
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-businessman"></span>
            <div>
              <h4><?php esc_html_e('Agency Features Guide', 'submittal-spec-sheet-builder'); ?></h4>
              <p><?php esc_html_e('Learn about white-label, lead routing, webhooks, and analytics', 'submittal-spec-sheet-builder'); ?></p>
              <a href="https://webstuffguylabs.com/docs/submittal-user-guide/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('View Agency Docs', 'submittal-spec-sheet-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- What's New -->
    <div class="sfb-welcome-section sfb-welcome-whats-new">
      <div class="sfb-welcome-card-header">
        <span class="dashicons dashicons-megaphone"></span>
        <h2><?php echo esc_html(sprintf(__("What's New in Version %s", 'submittal-spec-sheet-builder'), $plugin_version)); ?></h2>
      </div>
      <div class="sfb-whats-new-grid">
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-art"></span>
          <div>
            <h4><?php esc_html_e('PDF Themes', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-pro">Pro</span></h4>
            <p><?php esc_html_e('Choose from Engineering, Architectural, or Corporate color schemes', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-welcome-view-site"></span>
          <div>
            <h4><?php esc_html_e('PDF Watermarks', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-pro">Pro</span></h4>
            <p><?php esc_html_e('Add custom diagonal watermarks like DRAFT or CONFIDENTIAL', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-yes-alt"></span>
          <div>
            <h4><?php esc_html_e('Approval Signature Blocks', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-pro">Pro</span></h4>
            <p><?php esc_html_e('Professional approval tables for AHJ and code compliance', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-shield"></span>
          <div>
            <h4><?php esc_html_e('Enhanced Security', 'submittal-spec-sheet-builder'); ?></h4>
            <p><?php esc_html_e('Pro feature gates prevent unauthorized settings saves', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <?php if ($user_tier === 'free' || $user_tier === 'pro'): ?>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-admin-generic"></span>
          <div>
            <h4><?php esc_html_e('White-Label Mode', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-agency">Agency</span></h4>
            <p><?php esc_html_e('Remove all plugin branding for client-facing deployments', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-email-alt"></span>
          <div>
            <h4><?php esc_html_e('Lead Routing & Webhooks', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-agency">Agency</span></h4>
            <p><?php esc_html_e('Automated lead distribution and CRM integration', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($user_tier === 'agency'): ?>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-chart-bar"></span>
          <div>
            <h4><?php esc_html_e('Agency Analytics', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-agency">Agency</span></h4>
            <p><?php esc_html_e('Track performance across all client installations', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <div class="sfb-whats-new-item">
          <span class="dashicons dashicons-groups"></span>
          <div>
            <h4><?php esc_html_e('Multi-Client Support', 'submittal-spec-sheet-builder'); ?> <span class="sfb-badge-agency">Agency</span></h4>
            <p><?php esc_html_e('Manage multiple client accounts from one dashboard', 'submittal-spec-sheet-builder'); ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

</div>

<script>
(function() {
  const btn = document.getElementById('sfb-copy-btn');
  const code = document.getElementById('sfb-shortcode');
  const feedback = document.getElementById('sfb-copy-feedback');

  if (btn && code && feedback) {
    btn.addEventListener('click', async () => {
      const text = code.textContent.trim();
      try {
        await navigator.clipboard.writeText(text);
        showFeedback();
      } catch(e) {
        // Fallback
        const range = document.createRange();
        range.selectNodeContents(code);
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
        try {
          document.execCommand('copy');
          showFeedback();
        } catch(e2) {
          console.error('Copy failed:', e2);
        }
        sel.removeAllRanges();
      }
    });
  }

  function showFeedback() {
    feedback.style.display = 'block';
    setTimeout(() => { feedback.style.display = 'none'; }, 2000);
  }
})();
</script>
