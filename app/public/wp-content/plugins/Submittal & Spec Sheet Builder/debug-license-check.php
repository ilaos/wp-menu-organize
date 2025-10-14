<?php
/**
 * Debug script to check license state
 * Access via: /wp-content/plugins/Submittal & Spec Sheet Builder/debug-license-check.php
 */

// Load WordPress
require_once(__DIR__ . '/../../../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin only.');
}

echo '<h1>License Debug Information</h1>';
echo '<style>body { font-family: monospace; padding: 20px; } pre { background: #f5f5f5; padding: 10px; border: 1px solid #ccc; }</style>';

// 1. Check raw license option
echo '<h2>1. Raw License Option (get_option)</h2>';
$license = get_option('sfb_license', []);
echo '<pre>';
print_r($license);
echo '</pre>';

// 2. Check if registry file is loaded
echo '<h2>2. Registry File Check</h2>';
if (function_exists('sfb_is_agency_license')) {
    echo '✅ sfb_is_agency_license() function exists<br>';

    // Call the function
    $is_agency = sfb_is_agency_license();
    echo '<strong>Result: ' . ($is_agency ? '✅ TRUE (Agency)' : '❌ FALSE (Not Agency)') . '</strong><br>';
} else {
    echo '❌ sfb_is_agency_license() function NOT found<br>';
}

// 3. Check constants
echo '<h2>3. Dev Constants</h2>';
echo 'SFB_PRO_DEV: ' . (defined('SFB_PRO_DEV') && SFB_PRO_DEV ? '✅ TRUE' : '❌ FALSE or not defined') . '<br>';
echo 'SFB_AGENCY_DEV: ' . (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV ? '✅ TRUE' : '❌ FALSE or not defined') . '<br>';
echo 'SFB_DEV_MODE: ' . (defined('SFB_DEV_MODE') && SFB_DEV_MODE ? '✅ TRUE' : '❌ FALSE or not defined') . '<br>';

// 4. Check if SFB_Branding class exists
echo '<h2>4. Class Availability</h2>';
echo 'SFB_Branding class: ' . (class_exists('SFB_Branding') ? '✅ Exists' : '❌ Not found') . '<br>';
echo 'sfb_get_license_data function: ' . (function_exists('sfb_get_license_data') ? '✅ Exists' : '❌ Not found') . '<br>';

// 5. Manual check
echo '<h2>5. Manual Check Logic</h2>';
echo 'License tier field: ' . (isset($license['tier']) ? '<strong>' . esc_html($license['tier']) . '</strong>' : '❌ Not set') . '<br>';
echo 'License status field: ' . (isset($license['status']) ? '<strong>' . esc_html($license['status']) . '</strong>' : '❌ Not set') . '<br>';
echo 'Is tier === "agency": ' . (!empty($license['tier']) && $license['tier'] === 'agency' ? '✅ TRUE' : '❌ FALSE') . '<br>';

// 6. Check Pro status
echo '<h2>6. Pro License Check</h2>';
if (function_exists('sfb_is_pro_active')) {
    $is_pro = sfb_is_pro_active();
    echo 'sfb_is_pro_active(): ' . ($is_pro ? '✅ TRUE' : '❌ FALSE') . '<br>';
} else {
    echo '❌ sfb_is_pro_active() function NOT found<br>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=sfb-demo-tools') . '">← Back to Demo Tools</a></p>';
