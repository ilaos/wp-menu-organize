<?php
/**
 * Frontend Builder Template - 3-Step Guided Flow
 *
 * This template renders the public-facing submittal builder interface.
 * Flow: Pick Products → Review & Brand → Generate PDF (with optional lead capture)
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

if (!defined('ABSPATH')) exit;

// Get settings
$lead_capture_enabled = get_option('sfb_lead_capture_enabled', false);
$branding = [
  'company_name'    => get_option('sfb_settings')['company_name'] ?? get_bloginfo('name'),
  'company_address' => get_option('sfb_settings')['company_address'] ?? '',
  'company_phone'   => get_option('sfb_settings')['company_phone'] ?? '',
  'company_website' => get_option('sfb_settings')['company_website'] ?? '',
  'logo_url'        => get_option('sfb_settings')['logo_url'] ?? '',
  'primary_color'   => get_option('sfb_settings')['primary_color'] ?? '#7B61FF',
];

// Generate nonce for security
$nonce = wp_create_nonce('sfb_frontend_builder');
?>

<div id="sfb-builder-app"
     class="sfb-builder-wrapper"
     data-nonce="<?php echo esc_attr($nonce); ?>"
     data-lead-capture="<?php echo $lead_capture_enabled ? '1' : '0'; ?>"
     data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
     data-rest-url="<?php echo esc_url(rest_url('sfb/v1/')); ?>"
     data-branding="<?php echo esc_attr(wp_json_encode($branding)); ?>">

  <!-- Progress Header -->
  <?php include plugin_dir_path(__FILE__) . 'partials/header.php'; ?>

  <!-- Step Container -->
  <div class="sfb-steps-container">

    <!-- Step 1: Pick Products -->
    <div id="sfb-step-1" class="sfb-step sfb-step-active" data-step="1">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-products.php'; ?>
    </div>

    <!-- Step 2: Review & Brand -->
    <div id="sfb-step-2" class="sfb-step" data-step="2">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-review.php'; ?>
    </div>

    <!-- Step 3: Generate (Lead Capture Modal if enabled) -->
    <div id="sfb-step-3" class="sfb-step" data-step="3">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-generate.php'; ?>
    </div>

  </div>

  <!-- Live Selected Products Tray -->
  <?php include plugin_dir_path(__FILE__) . 'partials/selected-tray.php'; ?>

  <!-- Lead Capture Modal (only shown if enabled) -->
  <?php if ($lead_capture_enabled): ?>
    <?php include plugin_dir_path(__FILE__) . 'partials/modal-lead-capture.php'; ?>
  <?php endif; ?>

  <!-- Loading Overlay -->
  <div id="sfb-loading-overlay" class="sfb-loading-overlay" style="display: none;">
    <div class="sfb-spinner"></div>
    <p><?php esc_html_e('Generating your PDF...', 'submittal-builder'); ?></p>
  </div>

</div>

<?php
// Enqueue frontend assets
wp_enqueue_style('sfb-frontend');
wp_enqueue_script('sfb-frontend');
?>
