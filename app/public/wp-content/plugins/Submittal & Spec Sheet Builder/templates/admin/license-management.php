<?php
/**
 * Admin Template: License Management (Expired/Invalid Licenses)
 *
 * Displays license reactivation form and troubleshooting for non-active licenses.
 */
if (!defined('ABSPATH')) exit;

$lic = sfb_get_license_details();
$links = sfb_get_links();
$message = '';
$message_type = '';

// Handle license activation
if (isset($_POST['sfb_activate_license']) && wp_verify_nonce($_POST['sfb_license_nonce'], 'sfb_license_action')) {
  $new_key = sanitize_text_field($_POST['license_key'] ?? '');
  $new_email = sanitize_email($_POST['license_email'] ?? '');

  if ($new_key && $new_email) {
    $result = sfb_activate_license($new_key, $new_email);

    if (is_wp_error($result)) {
      $message = $result->get_error_message();
      $message_type = 'error';
    } else {
      $message = $result['message'];
      $message_type = 'success';

      // Refresh license details
      $lic = sfb_get_license_details();
    }
  } else {
    $message = __('Please enter both license key and email address.', 'submittal-builder');
    $message_type = 'error';
  }
}

// Handle license deactivation
if (isset($_POST['sfb_deactivate_license']) && wp_verify_nonce($_POST['sfb_license_nonce'], 'sfb_license_action')) {
  $result = sfb_deactivate_license();

  if (is_wp_error($result)) {
    $message = $result->get_error_message();
    $message_type = 'error';
  } else {
    $message = $result['message'];
    $message_type = 'success';

    // Refresh license details
    $lic = sfb_get_license_details();
  }
}
?>
<div class="wrap sfb-license-management-wrap">
  <h1><?php esc_html_e('Manage License', 'submittal-builder'); ?></h1>
  <p class="sfb-sub"><?php esc_html_e('Your license needs attention. Reactivate or update your license below.', 'submittal-builder'); ?></p>

  <?php if ($message): ?>
    <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
      <p><?php echo esc_html($message); ?></p>
    </div>
  <?php endif; ?>

  <!-- Current License Status -->
  <div class="sfb-license-status" style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; margin:24px 0; max-width:800px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
      <span style="display:inline-block; padding:8px 16px; background:<?php echo esc_attr($lic['status_color']); ?>; color:#fff; border-radius:6px; font-size:14px; font-weight:600;">
        <?php echo esc_html($lic['status_label']); ?>
      </span>
      <h2 style="margin:0; font-size:18px;"><?php esc_html_e('Current License Status', 'submittal-builder'); ?></h2>
    </div>

    <?php if ($lic['status'] === 'expired'): ?>
      <div class="notice notice-warning inline" style="margin:16px 0;">
        <p>
          <strong><?php esc_html_e('Your license has expired.', 'submittal-builder'); ?></strong>
          <?php esc_html_e('Renew now to continue receiving updates and support.', 'submittal-builder'); ?>
        </p>
      </div>
    <?php elseif ($lic['status'] === 'invalid'): ?>
      <div class="notice notice-error inline" style="margin:16px 0;">
        <p>
          <strong><?php esc_html_e('Your license key is invalid.', 'submittal-builder'); ?></strong>
          <?php esc_html_e('Please check your key or contact support for assistance.', 'submittal-builder'); ?>
        </p>
      </div>
    <?php else: ?>
      <div class="notice notice-info inline" style="margin:16px 0;">
        <p>
          <?php esc_html_e('Your license is currently inactive. Enter a valid license key below to activate Pro features.', 'submittal-builder'); ?>
        </p>
      </div>
    <?php endif; ?>

    <?php if ($lic['has_key']): ?>
      <table class="form-table" style="margin:16px 0 0 0;">
        <tr>
          <th scope="row" style="width:180px;"><?php esc_html_e('Current License Key', 'submittal-builder'); ?></th>
          <td>
            <code style="background:#f3f4f6; padding:6px 10px; border-radius:4px; font-size:14px;">
              <?php echo esc_html($lic['key_masked']); ?>
            </code>
          </td>
        </tr>
        <?php if ($lic['email']): ?>
        <tr>
          <th scope="row"><?php esc_html_e('License Email', 'submittal-builder'); ?></th>
          <td><?php echo esc_html($lic['email']); ?></td>
        </tr>
        <?php endif; ?>
      </table>
    <?php endif; ?>
  </div>

  <!-- Reactivation Form -->
  <div class="sfb-license-form" style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; margin:24px 0; max-width:800px;">
    <h2 style="margin:0 0 16px 0; font-size:18px;">
      <?php echo $lic['status'] === 'expired' ? esc_html__('Renew License', 'submittal-builder') : esc_html__('Activate License', 'submittal-builder'); ?>
    </h2>

    <form method="post" action="">
      <?php wp_nonce_field('sfb_license_action', 'sfb_license_nonce'); ?>

      <table class="form-table">
        <tr>
          <th scope="row">
            <label for="license_key"><?php esc_html_e('License Key', 'submittal-builder'); ?> <span style="color:#dc2626;">*</span></label>
          </th>
          <td>
            <input
              type="text"
              name="license_key"
              id="license_key"
              class="regular-text"
              value="<?php echo esc_attr($lic['key']); ?>"
              placeholder="<?php esc_attr_e('SFB-XXXX-XXXX-XXXX-XXXX', 'submittal-builder'); ?>"
              required
            >
            <p class="description">
              <?php esc_html_e('Enter the license key you received after purchase.', 'submittal-builder'); ?>
            </p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="license_email"><?php esc_html_e('License Email', 'submittal-builder'); ?> <span style="color:#dc2626;">*</span></label>
          </th>
          <td>
            <input
              type="email"
              name="license_email"
              id="license_email"
              class="regular-text"
              value="<?php echo esc_attr($lic['email']); ?>"
              placeholder="<?php esc_attr_e('your@email.com', 'submittal-builder'); ?>"
              required
            >
            <p class="description">
              <?php esc_html_e('The email address used during purchase (required for activation).', 'submittal-builder'); ?>
            </p>
          </td>
        </tr>
      </table>

      <p class="submit" style="display:flex; gap:12px; align-items:center;">
        <button type="submit" name="sfb_activate_license" class="button button-primary">
          <?php echo $lic['status'] === 'expired' ? esc_html__('Renew & Activate', 'submittal-builder') : esc_html__('Activate License', 'submittal-builder'); ?>
        </button>

        <?php if ($lic['status'] === 'active'): ?>
          <button type="submit" name="sfb_deactivate_license" class="button" onclick="return confirm('<?php esc_attr_e('Are you sure you want to deactivate this license on this site?', 'submittal-builder'); ?>');">
            <?php esc_html_e('Deactivate License', 'submittal-builder'); ?>
          </button>
        <?php endif; ?>
      </p>
    </form>
  </div>

  <!-- Quick Actions -->
  <div class="sfb-quick-actions" style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px; margin:24px 0; max-width:800px;">
    <h3 style="margin:0 0 16px 0; font-size:16px;"><?php esc_html_e('Quick Actions', 'submittal-builder'); ?></h3>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:12px;">
      <?php if ($lic['status'] === 'expired'): ?>
        <?php
        $renew_url = sfb_get_link('renew');
        if ($renew_url && $lic['key']) {
          $renew_url = add_query_arg('key', $lic['key'], $renew_url);
        }
        echo sfb_render_link_button('renew', __('Renew License Now', 'submittal-builder'), 'button button-primary');
        ?>
      <?php else: ?>
        <?php echo sfb_render_link_button('pricing', __('Get a License Key', 'submittal-builder'), 'button button-primary'); ?>
      <?php endif; ?>

      <?php echo sfb_render_link_button('account', __('View Account', 'submittal-builder'), 'button'); ?>

      <?php
      $support_url = sfb_get_link('support');
      if ($support_url && strpos($support_url, 'mailto:') === 0 && $lic['key_masked']) {
        $support_url = $support_url . '?subject=' . rawurlencode('License Issue - ' . $lic['key_masked']);
      }
      ?>
      <a href="<?php echo $support_url ? esc_url($support_url) : '#'; ?>" class="button" <?php if (!$support_url): ?>disabled title="<?php esc_attr_e('Coming soon', 'submittal-builder'); ?>"<?php endif; ?>>
        <?php esc_html_e('Contact Support', 'submittal-builder'); ?>
      </a>
    </div>
  </div>

  <!-- Troubleshooting -->
  <div class="sfb-troubleshooting" style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:24px; margin:24px 0; max-width:800px;">
    <h3 style="margin:0 0 12px 0; font-size:16px; color:#111827;"><?php esc_html_e('Troubleshooting', 'submittal-builder'); ?></h3>

    <details style="margin:12px 0; cursor:pointer;">
      <summary style="font-weight:600; padding:8px 0; color:#374151;">
        <?php esc_html_e('License key not working?', 'submittal-builder'); ?>
      </summary>
      <div style="padding:12px 0 8px 16px; color:#6b7280; line-height:1.6;">
        <ul style="margin:0; padding-left:20px;">
          <li><?php esc_html_e('Ensure you copied the entire license key without extra spaces', 'submittal-builder'); ?></li>
          <li><?php esc_html_e('Check that your license hasn\'t exceeded the site activation limit', 'submittal-builder'); ?></li>
          <li><?php esc_html_e('Verify your license is still active in your account dashboard', 'submittal-builder'); ?></li>
          <li><?php esc_html_e('Try deactivating on other sites to free up an activation slot', 'submittal-builder'); ?></li>
        </ul>
      </div>
    </details>

    <details style="margin:12px 0; cursor:pointer;">
      <summary style="font-weight:600; padding:8px 0; color:#374151;">
        <?php esc_html_e('License expired or need to renew?', 'submittal-builder'); ?>
      </summary>
      <div style="padding:12px 0 8px 16px; color:#6b7280; line-height:1.6;">
        <p style="margin:0 0 8px 0;">
          <?php esc_html_e('Renewals ensure you continue receiving:', 'submittal-builder'); ?>
        </p>
        <ul style="margin:0; padding-left:20px;">
          <li><?php esc_html_e('Software updates and new features', 'submittal-builder'); ?></li>
          <li><?php esc_html_e('Priority support access', 'submittal-builder'); ?></li>
          <li><?php esc_html_e('Security patches and bug fixes', 'submittal-builder'); ?></li>
        </ul>
        <?php
        $renew_url = sfb_get_link('renew');
        if ($renew_url):
          if ($lic['key']) {
            $renew_url = add_query_arg('key', $lic['key'], $renew_url);
          }
        ?>
        <p style="margin:12px 0 0 0;">
          <a href="<?php echo esc_url($renew_url); ?>" target="_blank" rel="noopener">
            <?php esc_html_e('Renew your license now →', 'submittal-builder'); ?>
          </a>
        </p>
        <?php endif; ?>
      </div>
    </details>

    <details style="margin:12px 0; cursor:pointer;">
      <summary style="font-weight:600; padding:8px 0; color:#374151;">
        <?php esc_html_e('Can\'t find your license key?', 'submittal-builder'); ?>
      </summary>
      <div style="padding:12px 0 8px 16px; color:#6b7280; line-height:1.6;">
        <?php
        $account_url = sfb_get_link('account');
        if ($account_url):
        ?>
        <p style="margin:0;">
          <?php esc_html_e('Check the email receipt from your purchase, or log in to your account at', 'submittal-builder'); ?>
          <a href="<?php echo esc_url($account_url); ?>" target="_blank" rel="noopener"><?php echo esc_html(parse_url($account_url, PHP_URL_HOST)); ?></a>
          <?php esc_html_e('to retrieve your license key.', 'submittal-builder'); ?>
        </p>
        <?php else: ?>
        <p style="margin:0;">
          <?php esc_html_e('Check the email receipt from your purchase to retrieve your license key.', 'submittal-builder'); ?>
        </p>
        <?php endif; ?>
      </div>
    </details>

    <details style="margin:12px 0; cursor:pointer;">
      <summary style="font-weight:600; padding:8px 0; color:#374151;">
        <?php esc_html_e('Still having issues?', 'submittal-builder'); ?>
      </summary>
      <div style="padding:12px 0 8px 16px; color:#6b7280; line-height:1.6;">
        <?php
        $support_url = sfb_get_link('support');
        if ($support_url):
          $support_display = strpos($support_url, 'mailto:') === 0 ? str_replace('mailto:', '', $support_url) : esc_html__('our support team', 'submittal-builder');
        ?>
        <p style="margin:0;">
          <?php esc_html_e('Contact our support team at', 'submittal-builder'); ?>
          <a href="<?php echo esc_url($support_url); ?>"><?php echo esc_html($support_display); ?></a>
          <?php esc_html_e('with your license key (last 4 digits) and we\'ll help you get activated.', 'submittal-builder'); ?>
        </p>
        <?php else: ?>
        <p style="margin:0;">
          <?php esc_html_e('Contact support with your license key (last 4 digits) and we\'ll help you get activated.', 'submittal-builder'); ?>
        </p>
        <?php endif; ?>
      </div>
    </details>
  </div>

  <p style="color:#9ca3af; margin-top:24px; font-size:12px; text-align:center;">
    <?php esc_html_e('Need help? Our support team is here to assist you.', 'submittal-builder'); ?>
  </p>
</div>
