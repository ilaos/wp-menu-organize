<?php
/**
 * License Tier Testing Page
 *
 * Displays current license status and tier detection for testing purposes.
 * Access via: WordPress Admin → Submittal Builder → License Test
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;

// Get current license data
$license_data = sfb_get_license_data();
$license_status = sfb_get_license_status();

// Test helper functions
$is_pro = sfb_is_pro_active();
$is_agency = sfb_is_agency_license();

// Get raw option data
$raw_option = get_option('sfb_license_data', []);

// Determine expected behavior based on product_id
$product_id = $license_data['product_id'] ?? '';
$expected_pro = false;
$expected_agency = false;

if ($product_id === 'SUBMITTAL-BUILDER-PRO') {
    $expected_pro = true;
    $expected_agency = false;
} elseif ($product_id === 'SUBMITTAL-BUILDER-AGENCY') {
    $expected_pro = true; // Agency includes all Pro features
    $expected_agency = true;
}

// Check if results match expectations
$pro_correct = ($is_pro === $expected_pro);
$agency_correct = ($is_agency === $expected_agency);
$all_correct = $pro_correct && $agency_correct;

?>

<div class="wrap">
    <h1><?php esc_html_e('License Tier Testing', 'submittal-spec-sheet-builder'); ?></h1>
    <p><?php esc_html_e('This page helps you test and verify license tier detection is working correctly.', 'submittal-spec-sheet-builder'); ?></p>

    <!-- Overall Status -->
    <div class="notice <?php echo $all_correct ? 'notice-success' : 'notice-error'; ?>">
        <h2 style="margin-top: 0.5em;">
            <?php if ($all_correct): ?>
                ✅ <?php esc_html_e('License Tier Detection: WORKING CORRECTLY', 'submittal-spec-sheet-builder'); ?>
            <?php else: ?>
                ❌ <?php esc_html_e('License Tier Detection: ERRORS DETECTED', 'submittal-spec-sheet-builder'); ?>
            <?php endif; ?>
        </h2>
    </div>

    <!-- Test Results -->
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php esc_html_e('Function Test Results', 'submittal-spec-sheet-builder'); ?></h2>

        <table class="widefat striped" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Function', 'submittal-spec-sheet-builder'); ?></th>
                    <th><?php esc_html_e('Actual Result', 'submittal-spec-sheet-builder'); ?></th>
                    <th><?php esc_html_e('Expected Result', 'submittal-spec-sheet-builder'); ?></th>
                    <th><?php esc_html_e('Status', 'submittal-spec-sheet-builder'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>sfb_is_pro_active()</code></td>
                    <td>
                        <strong style="color: <?php echo $is_pro ? '#16a34a' : '#dc2626'; ?>">
                            <?php echo $is_pro ? 'TRUE' : 'FALSE'; ?>
                        </strong>
                    </td>
                    <td>
                        <strong style="color: <?php echo $expected_pro ? '#16a34a' : '#dc2626'; ?>">
                            <?php echo $expected_pro ? 'TRUE' : 'FALSE'; ?>
                        </strong>
                    </td>
                    <td>
                        <?php if ($pro_correct): ?>
                            <span style="color: #16a34a;">✅ PASS</span>
                        <?php else: ?>
                            <span style="color: #dc2626;">❌ FAIL</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><code>sfb_is_agency_license()</code></td>
                    <td>
                        <strong style="color: <?php echo $is_agency ? '#16a34a' : '#dc2626'; ?>">
                            <?php echo $is_agency ? 'TRUE' : 'FALSE'; ?>
                        </strong>
                    </td>
                    <td>
                        <strong style="color: <?php echo $expected_agency ? '#16a34a' : '#dc2626'; ?>">
                            <?php echo $expected_agency ? 'TRUE' : 'FALSE'; ?>
                        </strong>
                    </td>
                    <td>
                        <?php if ($agency_correct): ?>
                            <span style="color: #16a34a;">✅ PASS</span>
                        <?php else: ?>
                            <span style="color: #dc2626;">❌ FAIL</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3 style="margin-top: 25px;"><?php esc_html_e('Expected Behavior:', 'submittal-spec-sheet-builder'); ?></h3>
        <ul style="margin-left: 20px; line-height: 1.8;">
            <li><strong>Pro License (SUBMITTAL-BUILDER-PRO):</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li><code>sfb_is_pro_active()</code> = TRUE</li>
                    <li><code>sfb_is_agency_license()</code> = FALSE</li>
                </ul>
            </li>
            <li style="margin-top: 10px;"><strong>Agency License (SUBMITTAL-BUILDER-AGENCY):</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li><code>sfb_is_pro_active()</code> = TRUE (Agency includes Pro features)</li>
                    <li><code>sfb_is_agency_license()</code> = TRUE</li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Current License Data -->
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php esc_html_e('Current License Data', 'submittal-spec-sheet-builder'); ?></h2>

        <table class="widefat" style="margin-top: 15px;">
            <tbody>
                <tr>
                    <th style="width: 30%; font-weight: 600;"><?php esc_html_e('License Key:', 'submittal-spec-sheet-builder'); ?></th>
                    <td><code><?php echo esc_html($license_status['key_masked'] ?: 'Not set'); ?></code></td>
                </tr>
                <tr>
                    <th style="font-weight: 600;"><?php esc_html_e('Email:', 'submittal-spec-sheet-builder'); ?></th>
                    <td><?php echo esc_html($license_data['email'] ?: 'Not set'); ?></td>
                </tr>
                <tr>
                    <th style="font-weight: 600;"><?php esc_html_e('Status:', 'submittal-spec-sheet-builder'); ?></th>
                    <td>
                        <span style="display: inline-block; padding: 4px 10px; background: <?php echo esc_attr($license_status['status_color']); ?>; color: white; border-radius: 4px; font-weight: 600;">
                            <?php echo esc_html($license_status['status_label']); ?>
                        </span>
                    </td>
                </tr>
                <tr style="background: #fffbeb;">
                    <th style="font-weight: 600; color: #92400e;">⚠️ <?php esc_html_e('Product ID:', 'submittal-spec-sheet-builder'); ?></th>
                    <td>
                        <code style="font-size: 14px; font-weight: 600; color: #92400e;">
                            <?php echo esc_html($product_id ?: 'Not set (backwards compatibility mode)'); ?>
                        </code>
                        <?php if (empty($product_id) && $license_data['status'] === 'active'): ?>
                            <p style="margin: 5px 0 0 0; color: #92400e; font-size: 12px;">
                                ⚠️ Missing product_id - defaulting to Pro-level access for backwards compatibility
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th style="font-weight: 600;"><?php esc_html_e('Expires:', 'submittal-spec-sheet-builder'); ?></th>
                    <td><?php echo esc_html($license_status['expires_formatted'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="font-weight: 600;"><?php esc_html_e('Activations Remaining:', 'submittal-spec-sheet-builder'); ?></th>
                    <td><?php echo esc_html($license_data['activations_remaining'] ?? 'Unlimited'); ?></td>
                </tr>
                <tr>
                    <th style="font-weight: 600;"><?php esc_html_e('Last Check:', 'submittal-spec-sheet-builder'); ?></th>
                    <td>
                        <?php
                        if ($license_data['last_check'] > 0) {
                            echo esc_html(human_time_diff($license_data['last_check'], time()) . ' ago');
                        } else {
                            echo 'Never';
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if (!empty($license_data['error'])): ?>
            <div class="notice notice-error inline" style="margin-top: 15px;">
                <p><strong><?php esc_html_e('Error:', 'submittal-spec-sheet-builder'); ?></strong> <?php echo esc_html($license_data['error']); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- WordPress Options Table Data -->
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php esc_html_e('WordPress Options Table Data', 'submittal-spec-sheet-builder'); ?></h2>
        <p><?php esc_html_e('Raw data stored in wp_options under "sfb_license_data":', 'submittal-spec-sheet-builder'); ?></p>

        <pre style="background: #f0f0f1; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; line-height: 1.6;"><?php
        echo esc_html(print_r($raw_option, true));
        ?></pre>
    </div>

    <!-- Test Instructions -->
    <div class="card" style="max-width: 800px; margin-top: 20px; background: #e0f2fe; border-left: 4px solid #0284c7;">
        <h2 style="color: #075985;"><?php esc_html_e('Testing Instructions', 'submittal-spec-sheet-builder'); ?></h2>

        <h3><?php esc_html_e('To Test Pro License:', 'submittal-spec-sheet-builder'); ?></h3>
        <ol style="line-height: 1.8;">
            <li>Go to <strong>License & Support</strong> page</li>
            <li>Enter a valid <strong>SUBMITTAL-BUILDER-PRO</strong> license key</li>
            <li>Click <strong>Activate License</strong></li>
            <li>Return to this page and verify:
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li>Product ID shows: <code>SUBMITTAL-BUILDER-PRO</code></li>
                    <li><code>sfb_is_pro_active()</code> = TRUE</li>
                    <li><code>sfb_is_agency_license()</code> = FALSE</li>
                </ul>
            </li>
        </ol>

        <h3 style="margin-top: 20px;"><?php esc_html_e('To Test Agency License:', 'submittal-spec-sheet-builder'); ?></h3>
        <ol style="line-height: 1.8;">
            <li>Go to <strong>License & Support</strong> page</li>
            <li>Enter a valid <strong>SUBMITTAL-BUILDER-AGENCY</strong> license key</li>
            <li>Click <strong>Activate License</strong></li>
            <li>Return to this page and verify:
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li>Product ID shows: <code>SUBMITTAL-BUILDER-AGENCY</code></li>
                    <li><code>sfb_is_pro_active()</code> = TRUE</li>
                    <li><code>sfb_is_agency_license()</code> = TRUE</li>
                </ul>
            </li>
        </ol>

        <h3 style="margin-top: 20px;"><?php esc_html_e('Database Verification:', 'submittal-spec-sheet-builder'); ?></h3>
        <p>You can also verify the data in your database:</p>
        <pre style="background: white; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px;">SELECT option_value FROM wp_options WHERE option_name = 'sfb_license_data';</pre>
        <p style="margin-top: 10px;">Look for the <code>product_id</code> field in the serialized data.</p>
    </div>

    <!-- Actions -->
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php esc_html_e('Quick Actions', 'submittal-spec-sheet-builder'); ?></h2>

        <p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-license')); ?>" class="button button-primary">
                <?php esc_html_e('Go to License & Support Page', 'submittal-spec-sheet-builder'); ?>
            </a>

            <a href="<?php echo esc_url(add_query_arg('sfb_clear_cache', '1')); ?>" class="button" style="margin-left: 10px;">
                <?php esc_html_e('Clear License Cache & Refresh', 'submittal-spec-sheet-builder'); ?>
            </a>
        </p>

        <?php if (isset($_GET['sfb_clear_cache'])): ?>
            <?php
            sfb_clear_license_cache();
            sfb_check_license_status(true); // Force fresh check
            ?>
            <div class="notice notice-success inline" style="margin-top: 15px;">
                <p><?php esc_html_e('License cache cleared and fresh check completed!', 'submittal-spec-sheet-builder'); ?></p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = '<?php echo esc_url(admin_url('admin.php?page=sfb-license-test')); ?>';
                }, 1500);
            </script>
        <?php endif; ?>
    </div>
</div>
