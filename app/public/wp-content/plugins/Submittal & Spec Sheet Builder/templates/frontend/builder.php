<?php
/**
 * Frontend Builder Template - 3-Step Guided Flow
 *
 * This template renders the public-facing submittal builder interface.
 * Flow: Pick Products → Review → Generate PDF
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

if (!defined('ABSPATH')) exit;

// Get settings
$branding = [
  'company_name'    => get_option('sfb_settings')['company_name'] ?? get_bloginfo('name'),
  'company_address' => get_option('sfb_settings')['company_address'] ?? '',
  'company_phone'   => get_option('sfb_settings')['company_phone'] ?? '',
  'company_website' => get_option('sfb_settings')['company_website'] ?? '',
  'logo_url'        => get_option('sfb_settings')['logo_url'] ?? '',
  'primary_color'   => get_option('sfb_settings')['primary_color'] ?? '#7B61FF',
];

// Check if lead capture is enabled (Pro feature)
$lead_capture_enabled = SFB_Lead_Capture::is_enabled();

// Debug: Log the actual value
error_log('[SFB Debug] Lead capture enabled check: ' . var_export($lead_capture_enabled, true));
error_log('[SFB Debug] Option value: ' . var_export(get_option('sfb_lead_capture_enabled', 'NOT_SET'), true));

// Generate nonce for security
$nonce = wp_create_nonce('sfb_frontend_builder');
?>

<div id="sfb-builder-app"
     class="sfb-builder-wrapper"
     data-nonce="<?php echo esc_attr($nonce); ?>"
     data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
     data-rest-url="<?php echo esc_url(rest_url('sfb/v1/')); ?>"
     data-branding="<?php echo esc_attr(wp_json_encode($branding)); ?>"
     data-lead-capture="<?php echo $lead_capture_enabled ? '1' : '0'; ?>">

  <!-- Progress Header -->
  <?php include plugin_dir_path(__FILE__) . 'partials/header.php'; ?>

  <!-- Step Container -->
  <div class="sfb-steps-container">

    <!-- Step 1: Pick Products -->
    <div id="sfb-step-1" class="sfb-step sfb-step-active" data-step="1">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-products.php'; ?>
    </div>

    <!-- Step 2: Review -->
    <div id="sfb-step-2" class="sfb-step" data-step="2">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-review.php'; ?>
    </div>

    <!-- Step 3: Generate -->
    <div id="sfb-step-3" class="sfb-step" data-step="3">
      <?php include plugin_dir_path(__FILE__) . 'partials/step-generate.php'; ?>
    </div>

  </div>

  <!-- Live Selected Products Tray -->
  <?php include plugin_dir_path(__FILE__) . 'partials/selected-tray.php'; ?>

  <!-- Lead Capture Modal (Pro feature - conditionally loaded) -->
  <?php include plugin_dir_path(__FILE__) . 'partials/modal-lead-capture.php'; ?>

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

// Register and enqueue review.js
wp_register_script('sfb-review', plugins_url('assets/js/review.js', dirname(dirname(__FILE__))), ['sfb-frontend'], '1.0.2', true);
wp_enqueue_script('sfb-review');

// Enqueue lead capture script if enabled (Pro feature)
if ($lead_capture_enabled) {
  wp_enqueue_script('sfb-lead-capture');
}

// Localize brand data for review.js
$brand_settings = get_option('sfb_settings', []);
$brand_data = [
  'company' => [
    'name' => $brand_settings['company_name'] ?? get_bloginfo('name'),
    'logo_url' => $brand_settings['logo_url'] ?? '',
    'address' => $brand_settings['company_address'] ?? '',
    'phone' => $brand_settings['company_phone'] ?? '',
    'website' => $brand_settings['company_website'] ?? '',
  ],
  'visual' => [
    'primary_color' => $brand_settings['primary_color'] ?? '#7861FF',
    'include_cover' => isset($brand_settings['include_cover']) ? (bool)$brand_settings['include_cover'] : true,
    'footer_text' => $brand_settings['footer_note'] ?? '',
  ]
];

// Agency - Phase B & C: Add preset data if Agency license
if (sfb_is_agency_license()) {
  // Check if default preset should be used
  $use_default = get_option('sfb_brand_use_default_on_pdf', false);
  $default_preset = SFB_Branding::get_default_preset();

  if ($use_default && $default_preset && !empty($default_preset['data'])) {
    $brand_data['useDefault'] = true;
    $brand_data['defaultPreset'] = [
      'id' => $default_preset['id'],
      'name' => $default_preset['name'],
      'data' => [
        'visual' => [
          'primary_color' => $default_preset['data']['visual']['primary_color'] ?? '#7861FF',
          'include_cover' => $default_preset['data']['visual']['include_cover'] ?? true,
          'footer_text' => $default_preset['data']['visual']['footer_text'] ?? '',
        ]
      ]
    ];
  } else {
    $brand_data['useDefault'] = false;
  }

  // Phase C: Expose all presets for switcher
  $all_presets = SFB_Branding::get_presets();
  $brand_data['presets'] = array_map(function($preset) {
    return [
      'id' => $preset['id'],
      'name' => $preset['name'],
      'is_default' => $preset['is_default'] ?? false,
      'data' => [
        'visual' => [
          'primary_color' => $preset['data']['visual']['primary_color'] ?? '#7861FF',
          'include_cover' => $preset['data']['visual']['include_cover'] ?? true,
          'footer_text' => $preset['data']['visual']['footer_text'] ?? '',
        ]
      ]
    ];
  }, $all_presets);
} else {
  $brand_data['useDefault'] = false;
  $brand_data['presets'] = [];
}

wp_localize_script('sfb-review', 'SFB_BRAND', $brand_data);
?>
