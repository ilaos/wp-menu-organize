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
$upgrade_url   = admin_url('admin.php?page=sfb-upgrade');
$license_url   = admin_url('admin.php?page=sfb-license');

// Check license
$is_pro = function_exists('sfb_is_pro_active') && sfb_is_pro_active();

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
          <h1><?php esc_html_e('Submittal & Spec Sheet Builder', 'submittal-builder'); ?></h1>
          <p class="sfb-welcome-tagline"><?php esc_html_e('Built by Webstuffguy Labs', 'submittal-builder'); ?></p>
        </div>
      </div>
      <div class="sfb-welcome-hero-actions">
        <a href="<?php echo esc_url($builder_url); ?>" class="button button-primary button-hero">
          <span class="dashicons dashicons-admin-site-alt3"></span>
          <?php esc_html_e('Launch Builder', 'submittal-builder'); ?>
        </a>
        <?php if (!$is_pro): ?>
          <a href="<?php echo esc_url($upgrade_url); ?>" class="button button-secondary button-hero">
            <span class="dashicons dashicons-star-filled"></span>
            <?php esc_html_e('Upgrade to Pro', 'submittal-builder'); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="sfb-welcome-content">

    <!-- Quick Actions -->
    <div class="sfb-welcome-section sfb-welcome-quick-actions">
      <h2><?php esc_html_e('Quick Actions', 'submittal-builder'); ?></h2>
      <div class="sfb-quick-action-grid">

        <a href="<?php echo esc_url($builder_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-builder">
            <span class="dashicons dashicons-editor-table"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Build Your Catalog', 'submittal-builder'); ?></h3>
            <p><?php esc_html_e('Add products, categories, and specifications', 'submittal-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($branding_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-branding">
            <span class="dashicons dashicons-art"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Customize Branding', 'submittal-builder'); ?></h3>
            <p><?php esc_html_e('Set your logo, colors, and company details', 'submittal-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($settings_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-settings">
            <span class="dashicons dashicons-admin-settings"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Configure Settings', 'submittal-builder'); ?></h3>
            <p><?php esc_html_e('Manage features, PDFs, and preferences', 'submittal-builder'); ?></p>
          </div>
          <span class="dashicons dashicons-arrow-right-alt2 sfb-quick-action-arrow"></span>
        </a>

        <a href="<?php echo esc_url($utilities_url); ?>" class="sfb-quick-action">
          <div class="sfb-quick-action-icon sfb-icon-utilities">
            <span class="dashicons dashicons-admin-tools"></span>
          </div>
          <div class="sfb-quick-action-content">
            <h3><?php esc_html_e('Run Utilities', 'submittal-builder'); ?></h3>
            <p><?php esc_html_e('Database cleanup, diagnostics, and tools', 'submittal-builder'); ?></p>
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
          <h2><?php esc_html_e('Getting Started', 'submittal-builder'); ?></h2>
        </div>
        <div class="sfb-welcome-checklist">
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">1</span>
            <div>
              <h4><?php esc_html_e('Build your product catalog', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Add categories, products, and specifications to your catalog.', 'submittal-builder'); ?></p>
              <a href="<?php echo esc_url($builder_url); ?>"><?php esc_html_e('Open Builder', 'submittal-builder'); ?> →</a>
            </div>
          </div>
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">2</span>
            <div>
              <h4><?php esc_html_e('Set up your branding', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Upload your logo and choose your brand colors.', 'submittal-builder'); ?></p>
              <a href="<?php echo esc_url($branding_url); ?>"><?php esc_html_e('Branding Settings', 'submittal-builder'); ?> →</a>
            </div>
          </div>
          <div class="sfb-checklist-item">
            <span class="sfb-checklist-number">3</span>
            <div>
              <h4><?php esc_html_e('Publish on your site', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Add the shortcode to any page or post:', 'submittal-builder'); ?></p>
              <div class="sfb-shortcode-box">
                <code id="sfb-shortcode">[submittal_builder]</code>
                <button class="button button-small" id="sfb-copy-btn" type="button">
                  <span class="dashicons dashicons-admin-page"></span>
                  <?php esc_html_e('Copy', 'submittal-builder'); ?>
                </button>
              </div>
              <span class="sfb-copy-feedback" id="sfb-copy-feedback" style="display: none;"><?php esc_html_e('✓ Copied!', 'submittal-builder'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Resources / Support -->
      <div class="sfb-welcome-section sfb-welcome-card">
        <div class="sfb-welcome-card-header">
          <span class="dashicons dashicons-book-alt"></span>
          <h2><?php esc_html_e('Resources & Support', 'submittal-builder'); ?></h2>
        </div>
        <div class="sfb-resource-list">
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-media-document"></span>
            <div>
              <h4><?php esc_html_e('Documentation', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Learn how to use all features', 'submittal-builder'); ?></p>
              <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/documentation/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('View Docs', 'submittal-builder'); ?> ↗
              </a>
            </div>
          </div>
          <div class="sfb-resource-item">
            <span class="dashicons dashicons-sos"></span>
            <div>
              <h4><?php esc_html_e('Get Support', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Need help? Contact our support team', 'submittal-builder'); ?></p>
              <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Contact Support', 'submittal-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php if (!$is_pro): ?>
          <div class="sfb-resource-item sfb-resource-highlight">
            <span class="dashicons dashicons-star-filled"></span>
            <div>
              <h4><?php esc_html_e('Upgrade to Pro or Agency', 'submittal-builder'); ?></h4>
              <p><?php esc_html_e('Unlock tracking, lead capture, and more', 'submittal-builder'); ?></p>
              <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('See Pro Features', 'submittal-builder'); ?> ↗
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>
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
