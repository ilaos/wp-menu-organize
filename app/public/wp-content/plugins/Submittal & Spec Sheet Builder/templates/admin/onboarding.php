<?php
/**
 * Onboarding Wizard
 * First-run setup page for Submittal Builder
 */

if (!defined('ABSPATH')) exit;

// Check for success message from redirect (form handler is in main plugin file)
$setup_done = isset($_GET['setup']) && $_GET['setup'] === 'done';

// Get current branding settings and onboarding status
$brand = get_option('sfb_branding', []);
$company_name = $brand['company_name'] ?? '';
$primary_color = $brand['primary_color'] ?? '#111827';
$logo_url = $brand['logo_url'] ?? '';
$onboarding_completed = get_option('sfb_onboarding_completed', false);

// Check if user is returning (completed onboarding and has branding)
$is_returning = $onboarding_completed && !empty($company_name);
?>

<div class="wrap sfb-onboard" style="max-width: 800px; margin: 40px auto; --sfb-accent: <?php echo esc_attr($primary_color); ?>;">
  <div style="text-align: center; margin-bottom: 40px;">
    <h1 style="font-size: 32px; margin-bottom: 8px;"><?php echo esc_html__('👋 Welcome to Submittal & Spec Sheet Builder!', 'submittal-builder'); ?></h1>
    <p style="font-size: 16px; color: #6b7280;"><?php echo esc_html__('Let\'s set up your brand and create your first submittal packet.', 'submittal-builder'); ?></p>
  </div>

  <?php if ($is_returning): ?>
    <!-- Returning User: Collapsed Success State -->
    <div id="sfb-returning" class="notice notice-success" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
      <div>✅ <?php esc_html_e('You\'re all set. Your branding is saved.', 'submittal-builder'); ?></div>
      <div style="display:flex;gap:8px;">
        <a href="#" id="sfb-edit-branding-toggle" class="button button-secondary"><?php esc_html_e('Edit Branding', 'submittal-builder'); ?></a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>" class="button button-primary" id="sfb-start-builder"><?php esc_html_e('Open Builder', 'submittal-builder'); ?></a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Setup Form (always present, but hidden if returning user) -->
  <div id="sfb-branding-form" class="sfb-card" <?php echo $is_returning ? 'hidden' : ''; ?>>
    <h2 class="sfb-accent-title"><?php echo esc_html__('Quick Setup', 'submittal-builder'); ?></h2>

    <!-- 3-Step Instructions -->
    <div class="sfb-steps" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 24px;">
      <div class="sfb-step" style="background: #fafafa; border: 1px solid #eef0f3; border-radius: 6px; padding: 12px;">
        <div style="font-size: 20px; margin-bottom: 6px;">🎨</div>
        <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px;"><?php echo esc_html__('Set your brand', 'submittal-builder'); ?></div>
        <div style="font-size: 12px; color: #6b7280; line-height: 1.4;"><?php echo esc_html__('Company name, color, and logo appear on your PDFs.', 'submittal-builder'); ?></div>
      </div>
      <div class="sfb-step" style="background: #fafafa; border: 1px solid #eef0f3; border-radius: 6px; padding: 12px;">
        <div style="font-size: 20px; margin-bottom: 6px;">📦</div>
        <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px;"><?php echo esc_html__('Build your catalog', 'submittal-builder'); ?></div>
        <div style="font-size: 12px; color: #6b7280; line-height: 1.4;"><?php echo esc_html__('Add categories, products, and spec fields in the Builder.', 'submittal-builder'); ?></div>
      </div>
      <div class="sfb-step" style="background: #fafafa; border: 1px solid #eef0f3; border-radius: 6px; padding: 12px;">
        <div style="font-size: 20px; margin-bottom: 6px;">🚀</div>
        <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px;"><?php echo esc_html__('Use the shortcode', 'submittal-builder'); ?></div>
        <div style="font-size: 12px; color: #6b7280; line-height: 1.4;"><?php echo esc_html__('Add [submittal_builder] to any page to publish the form.', 'submittal-builder'); ?></div>
      </div>
    </div>

    <form method="post" action="">
      <?php wp_nonce_field('sfb_quick_setup'); ?>
      <input type="hidden" name="sfb_quick_setup" value="1">

      <!-- Company Name -->
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151;">
          <?php echo esc_html__('Company Name', 'submittal-builder'); ?>
        </label>
        <input
          type="text"
          name="company_name"
          value="<?php echo esc_attr($company_name); ?>"
          placeholder="<?php esc_attr_e('e.g., Acme Construction', 'submittal-builder'); ?>"
          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
        >
        <p style="margin: 6px 0 0; font-size: 12px; color: #6b7280;">
          <?php echo esc_html__('Appears on PDF cover pages and headers.', 'submittal-builder'); ?>
        </p>
      </div>

      <!-- Brand Color -->
      <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151;">
          <?php echo esc_html__('Brand Color', 'submittal-builder'); ?>
        </label>
        <div style="display: flex; gap: 12px; align-items: center;">
          <input
            type="color"
            name="primary_color"
            value="<?php echo esc_attr($primary_color); ?>"
            style="width: 60px; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;"
          >
          <input
            type="text"
            name="primary_color_text"
            value="<?php echo esc_attr($primary_color); ?>"
            placeholder="#111827"
            pattern="^#[0-9A-Fa-f]{6}$"
            style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px; font-family: monospace;"
          >
        </div>
        <p style="margin: 6px 0 0; font-size: 12px; color: #6b7280;">
          <?php echo esc_html__('Used for headers and accents in PDFs.', 'submittal-builder'); ?>
        </p>
      </div>

      <!-- Logo URL -->
      <div style="margin-bottom: 24px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151;">
          <?php echo esc_html__('Logo URL (Optional)', 'submittal-builder'); ?>
        </label>
        <input
          type="url"
          name="logo_url"
          value="<?php echo esc_attr($logo_url); ?>"
          placeholder="https://example.com/logo.png"
          style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 14px;"
        >
        <p style="margin: 6px 0 0; font-size: 12px; color: #6b7280;">
          <?php printf(
            esc_html__('Upload in %s and paste URL here.', 'submittal-builder'),
            '<a href="' . esc_url(admin_url('upload.php')) . '" target="_blank">' . esc_html__('Media Library', 'submittal-builder') . '</a>'
          ); ?>
        </p>
      </div>

      <!-- Actions -->
      <div style="display: flex; gap: 12px; justify-content: flex-end; align-items: center; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <button
          type="submit"
          class="button button-primary button-large"
          style="background: #7c3aed; border-color: #7c3aed; padding: 8px 24px; font-size: 14px;"
        >
          <?php echo esc_html__('Complete Setup', 'submittal-builder'); ?>
        </button>
        <a
          href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>"
          id="sfb-start-builder"
          class="button button-secondary button-large"
          style="padding: 8px 24px; font-size: 14px;"
        >
          <?php echo esc_html__('Start in Builder', 'submittal-builder'); ?>
        </a>
      </div>
    </form>
  </div>

  <!-- Divider -->
  <div class="sfb-divider" style="border-top: 1px solid #e5e7eb; margin: 32px 0;"></div>

  <!-- What's Next -->
  <div class="sfb-card" style="background: #f9fafb;">
    <h3 class="sfb-accent-title" style="font-size: 16px;">
      <?php echo esc_html__('🚀 What\'s Next?', 'submittal-builder'); ?>
    </h3>
    <ul style="margin: 0; padding-left: 20px; color: #374151; line-height: 1.8; list-style: none;">
      <li style="margin-bottom: 8px;">
        <span class="dashicons dashicons-admin-tools" style="color: var(--sfb-accent, #7c3aed); font-size: 16px; margin-right: 8px;"></span>
        <?php echo esc_html__('Build your product catalog in the', 'submittal-builder'); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>"><?php echo esc_html__('Builder', 'submittal-builder'); ?></a>
      </li>
      <li style="margin-bottom: 8px;">
        <span class="dashicons dashicons-admin-appearance" style="color: var(--sfb-accent, #7c3aed); font-size: 16px; margin-right: 8px;"></span>
        <?php echo esc_html__('Adjust branding in', 'submittal-builder'); ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-branding')); ?>"><?php echo esc_html__('Settings → Branding', 'submittal-builder'); ?></a>
      </li>
      <li style="margin-bottom: 8px;">
        <span class="dashicons dashicons-admin-page" style="color: var(--sfb-accent, #7c3aed); font-size: 16px; margin-right: 8px;"></span>
        <?php echo esc_html__('Publish the form using the [submittal_builder] shortcode', 'submittal-builder'); ?>
        <div style="display: flex; gap: 8px; align-items: center; margin-top: 6px; margin-left: 32px;">
          <input
            type="text"
            value="[submittal_builder]"
            readonly
            id="sfb-shortcode-input"
            style="flex: 1; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-family: monospace; font-size: 13px; background: #fff;"
          >
          <button
            type="button"
            class="button button-secondary"
            onclick="navigator.clipboard.writeText('[submittal_builder]'); this.textContent='<?php esc_attr_e('Copied!', 'submittal-builder'); ?>'; setTimeout(()=>this.textContent='<?php esc_attr_e('Copy', 'submittal-builder'); ?>',1500);"
            style="white-space: nowrap;"
          >
            <?php echo esc_html__('Copy', 'submittal-builder'); ?>
          </button>
        </div>
      </li>
      <li>
        <span class="dashicons dashicons-star-filled" style="color: var(--sfb-accent, #7c3aed); font-size: 16px; margin-right: 8px;"></span>
        <?php echo esc_html__('Explore Pro features later', 'submittal-builder'); ?>
        <?php if (function_exists('sfb_is_pro_active') && sfb_is_pro_active()): ?>
          <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-license')); ?>"><?php echo esc_html__('License & Support', 'submittal-builder'); ?></a>
        <?php else: ?>
          <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-upgrade')); ?>"><?php echo esc_html__('Upgrade', 'submittal-builder'); ?></a>
        <?php endif; ?>
      </li>
    </ul>
  </div>

</div>

<style>
/* Sync color picker with text input */
input[name="primary_color_text"] {
  pointer-events: auto;
}
input[name="primary_color"] {
  pointer-events: auto;
}

/* Onboarding styles */
.sfb-onboard .sfb-divider {
  border-top: 1px solid #e5e7eb;
  margin: 20px 0;
}
.sfb-onboard .sfb-steps {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 12px;
  margin-bottom: 12px;
}
.sfb-onboard .sfb-step {
  background: #fff;
  border: 1px solid #eef0f3;
  border-radius: 8px;
  padding: 10px 12px;
}
.sfb-onboard .sfb-pulse {
  animation: sfbPulse 1.5s ease-in-out 1;
}
@keyframes sfbPulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.035); }
  100% { transform: scale(1); }
}
</style>

<script>
// Sync color picker with text input
document.addEventListener('DOMContentLoaded', function() {
  const colorPicker = document.querySelector('input[name="primary_color"]');
  const colorText = document.querySelector('input[name="primary_color_text"]');

  if (colorPicker && colorText) {
    // Update text when color picker changes
    colorPicker.addEventListener('input', function() {
      colorText.value = this.value;
    });

    // Update color picker when text changes (and is valid)
    colorText.addEventListener('input', function() {
      if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        colorPicker.value = this.value;
      }
    });
  }
});
</script>
