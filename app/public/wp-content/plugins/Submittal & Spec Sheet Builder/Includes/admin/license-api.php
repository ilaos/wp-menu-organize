<?php
/**
 * WooCommerce Software Add-on License API Integration
 *
 * Handles license activation, deactivation, and validation with WooCommerce Software API.
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;

// WooCommerce Software API Configuration
define('SFB_LICENSE_API_URL', 'https://webstuffguylabs.com/?wc-api=software-api');
define('SFB_LICENSE_PRODUCT_ID', 'SUBMITTAL-BUILDER');
define('SFB_LICENSE_VERSION', '1.0.2');

/**
 * Get current license data from WordPress options
 *
 * @return array License data with keys: key, email, status, expires, last_check, error
 */
function sfb_get_license_data() {
	$defaults = [
		'key'        => '',
		'email'      => '',
		'status'     => 'inactive', // inactive, active, expired, invalid
		'expires'    => '',
		'last_check' => 0,
		'error'      => '',
		'activations_remaining' => null,
		'instance'   => sfb_get_instance_id(),
	];

	$data = get_option('sfb_license_data', $defaults);

	// Ensure all keys exist
	return wp_parse_args($data, $defaults);
}

/**
 * Update license data in WordPress options
 *
 * @param array $data License data to save
 * @return bool Success
 */
function sfb_update_license_data($data) {
	$current = sfb_get_license_data();
	$updated = array_merge($current, $data);
	$updated['last_check'] = time();

	return update_option('sfb_license_data', $updated, false);
}

/**
 * Get unique instance identifier for this site
 *
 * @return string Hashed site URL
 */
function sfb_get_instance_id() {
	return md5(get_site_url());
}

/**
 * Make remote API request to WooCommerce Software Add-on
 *
 * @param string $action Request type: activation, deactivation, check
 * @param array $args Additional arguments (license_key, email)
 * @return array|WP_Error Response data or error
 */
function sfb_remote_license_request($action, $args = []) {
	$defaults = [
		'license_key' => '',
		'email'       => '',
	];

	$args = wp_parse_args($args, $defaults);

	// Build API URL
	$url = add_query_arg([
		'request'    => $action,
		'email'      => $args['email'],
		'license_key' => $args['license_key'],
		'product_id' => SFB_LICENSE_PRODUCT_ID,
		'instance'   => sfb_get_instance_id(),
		'version'    => SFB_LICENSE_VERSION,
	], SFB_LICENSE_API_URL);

	// Make request with timeout
	$response = wp_remote_get($url, [
		'timeout' => 15,
		'sslverify' => true,
		'user-agent' => 'Submittal Builder/' . SFB_LICENSE_VERSION . '; ' . get_site_url(),
	]);

	// Check for errors
	if (is_wp_error($response)) {
		return new WP_Error(
			'api_connection_failed',
			__('Could not connect to license server. Please check your internet connection and try again.', 'submittal-builder'),
			['original_error' => $response->get_error_message()]
		);
	}

	$code = wp_remote_retrieve_response_code($response);
	$body = wp_remote_retrieve_body($response);

	// Parse JSON response
	$data = json_decode($body, true);

	if ($code !== 200) {
		return new WP_Error(
			'api_error',
			__('License server returned an error. Please try again later.', 'submittal-builder'),
			['code' => $code, 'body' => $body]
		);
	}

	if (json_last_error() !== JSON_ERROR_NONE) {
		return new WP_Error(
			'invalid_response',
			__('Invalid response from license server.', 'submittal-builder'),
			['body' => $body]
		);
	}

	return $data;
}

/**
 * Activate license with WooCommerce API
 *
 * @param string $license_key License key
 * @param string $email Customer email
 * @return array|WP_Error Result with success/error
 */
function sfb_activate_license($license_key, $email) {
	$response = sfb_remote_license_request('activation', [
		'license_key' => $license_key,
		'email'       => $email,
	]);

	if (is_wp_error($response)) {
		return $response;
	}

	// WooCommerce Software API returns different structures
	// Check for success indicators
	if (isset($response['activated']) && $response['activated'] === true) {
		// Successful activation
		$license_data = [
			'key'    => $license_key,
			'email'  => $email,
			'status' => 'active',
			'expires' => isset($response['expire_date']) ? $response['expire_date'] : '',
			'activations_remaining' => isset($response['activations_remaining']) ? $response['activations_remaining'] : null,
			'error'  => '',
		];

		sfb_update_license_data($license_data);

		// Clear cache
		delete_transient('sfb_license_check_cache');

		return [
			'success' => true,
			'message' => __('License activated successfully!', 'submittal-builder'),
			'data'    => $license_data,
		];
	}

	// Check for error messages
	if (isset($response['error'])) {
		$error_msg = $response['error'];

		// Map common errors to user-friendly messages
		if (stripos($error_msg, 'activation limit') !== false) {
			$error_msg = __('This license has reached its activation limit. Please deactivate it on another site or upgrade your license.', 'submittal-builder');
		} elseif (stripos($error_msg, 'expired') !== false) {
			$error_msg = __('This license has expired. Please renew your license to continue receiving updates.', 'submittal-builder');
		} elseif (stripos($error_msg, 'invalid') !== false) {
			$error_msg = __('Invalid license key or email address. Please check your credentials and try again.', 'submittal-builder');
		}

		return new WP_Error('activation_failed', $error_msg);
	}

	// Unknown response format
	return new WP_Error(
		'unknown_response',
		__('Unexpected response from license server. Please contact support.', 'submittal-builder'),
		$response
	);
}

/**
 * Deactivate license with WooCommerce API
 *
 * @param string $license_key License key (optional, uses stored if empty)
 * @param string $email Customer email (optional, uses stored if empty)
 * @return array|WP_Error Result with success/error
 */
function sfb_deactivate_license($license_key = '', $email = '') {
	// Use stored credentials if not provided
	$current = sfb_get_license_data();

	if (empty($license_key)) {
		$license_key = $current['key'];
	}
	if (empty($email)) {
		$email = $current['email'];
	}

	if (empty($license_key)) {
		return new WP_Error('missing_key', __('No license key provided.', 'submittal-builder'));
	}

	$response = sfb_remote_license_request('deactivation', [
		'license_key' => $license_key,
		'email'       => $email,
	]);

	if (is_wp_error($response)) {
		return $response;
	}

	// Check for success
	if (isset($response['reset']) && $response['reset'] === true) {
		// Successful deactivation
		$license_data = [
			'status' => 'inactive',
			'error'  => '',
		];

		sfb_update_license_data($license_data);

		// Clear cache
		delete_transient('sfb_license_check_cache');

		return [
			'success' => true,
			'message' => __('License deactivated successfully.', 'submittal-builder'),
		];
	}

	// Check for errors
	if (isset($response['error'])) {
		return new WP_Error('deactivation_failed', $response['error']);
	}

	return new WP_Error(
		'unknown_response',
		__('Unexpected response from license server.', 'submittal-builder'),
		$response
	);
}

/**
 * Validate/check license status with WooCommerce API
 *
 * Uses cached response if available and not expired (12 hours default).
 *
 * @param bool $force_check Force fresh API check (bypass cache)
 * @return array|WP_Error License status data
 */
function sfb_check_license_status($force_check = false) {
	$current = sfb_get_license_data();

	// If no license key, return inactive
	if (empty($current['key'])) {
		return [
			'status'  => 'inactive',
			'message' => __('No license key entered.', 'submittal-builder'),
		];
	}

	// Check cache first (12 hours)
	$cache_key = 'sfb_license_check_cache';
	$cache_duration = 12 * HOUR_IN_SECONDS;

	if (!$force_check) {
		$cached = get_transient($cache_key);
		if ($cached !== false) {
			return $cached;
		}
	}

	// Make API request
	$response = sfb_remote_license_request('check', [
		'license_key' => $current['key'],
		'email'       => $current['email'],
	]);

	// Handle connection errors - fallback to last known status
	if (is_wp_error($response)) {
		// If we have a previously active license, give benefit of the doubt
		if ($current['status'] === 'active') {
			return [
				'status'  => 'active',
				'message' => __('Using cached license status (server temporarily unreachable).', 'submittal-builder'),
				'cached'  => true,
				'error'   => $response->get_error_message(),
			];
		}

		// Otherwise return error
		sfb_update_license_data(['error' => $response->get_error_message()]);

		return $response;
	}

	// Parse response
	$status_data = sfb_parse_license_check_response($response);

	// Update stored data
	sfb_update_license_data([
		'status'  => $status_data['status'],
		'expires' => $status_data['expires'] ?? '',
		'error'   => '',
		'activations_remaining' => $status_data['activations_remaining'] ?? null,
	]);

	// Cache the result
	set_transient($cache_key, $status_data, $cache_duration);

	return $status_data;
}

/**
 * Parse license check API response into standardized format
 *
 * @param array $response API response data
 * @return array Standardized license status
 */
function sfb_parse_license_check_response($response) {
	$status_data = [
		'status'  => 'invalid',
		'message' => '',
		'expires' => '',
		'activations_remaining' => null,
	];

	// Check for active license
	if (isset($response['status']) && $response['status'] === 'active') {
		$status_data['status'] = 'active';
		$status_data['message'] = __('License is active and valid.', 'submittal-builder');
	} elseif (isset($response['check']) && $response['check'] === 'valid') {
		$status_data['status'] = 'active';
		$status_data['message'] = __('License is active and valid.', 'submittal-builder');
	}

	// Check for expired
	if (isset($response['status']) && $response['status'] === 'expired') {
		$status_data['status'] = 'expired';
		$status_data['message'] = __('License has expired. Please renew to continue receiving updates.', 'submittal-builder');
	}

	// Check for invalid
	if (isset($response['error'])) {
		$status_data['status'] = 'invalid';
		$status_data['message'] = $response['error'];
	}

	// Get expiry date
	if (isset($response['expire_date'])) {
		$status_data['expires'] = $response['expire_date'];
	} elseif (isset($response['expiry'])) {
		$status_data['expires'] = $response['expiry'];
	}

	// Get activations remaining
	if (isset($response['activations_remaining'])) {
		$status_data['activations_remaining'] = intval($response['activations_remaining']);
	}

	return $status_data;
}

/**
 * Get formatted license status for display
 *
 * Returns array with display-ready data including badges, colors, etc.
 *
 * @return array Formatted license details
 */
function sfb_get_license_status() {
	$data = sfb_get_license_data();
	$status = $data['status'];

	// Check if we need to validate
	$time_since_check = time() - $data['last_check'];
	if ($time_since_check > (12 * HOUR_IN_SECONDS) && !empty($data['key'])) {
		$check = sfb_check_license_status();
		if (!is_wp_error($check)) {
			$status = $check['status'];
		}
	}

	// Build display data
	$result = [
		'has_key'       => !empty($data['key']),
		'key'           => $data['key'],
		'key_masked'    => !empty($data['key']) ? sfb_mask_license_key($data['key']) : '',
		'email'         => $data['email'],
		'status'        => $status,
		'status_label'  => '',
		'status_color'  => '',
		'expires'       => $data['expires'],
		'expires_formatted' => '',
		'is_active'     => false,
		'error'         => $data['error'],
		'activations_remaining' => $data['activations_remaining'],
	];

	// Set status-specific display values
	switch ($status) {
		case 'active':
			$result['status_label'] = __('Active', 'submittal-builder');
			$result['status_color'] = '#10b981'; // green
			$result['is_active'] = true;
			break;

		case 'expired':
			$result['status_label'] = __('Expired', 'submittal-builder');
			$result['status_color'] = '#f59e0b'; // amber
			break;

		case 'invalid':
			$result['status_label'] = __('Invalid', 'submittal-builder');
			$result['status_color'] = '#ef4444'; // red
			break;

		case 'inactive':
		default:
			$result['status_label'] = __('Inactive', 'submittal-builder');
			$result['status_color'] = '#9ca3af'; // gray
			break;
	}

	// Format expiry date
	if (!empty($data['expires'])) {
		$expires_ts = is_numeric($data['expires']) ? $data['expires'] : strtotime($data['expires']);
		if ($expires_ts) {
			$result['expires_formatted'] = date_i18n(get_option('date_format'), $expires_ts);
		}
	}

	return $result;
}

/**
 * Mask license key for display (show first 4 and last 4 characters)
 *
 * @param string $key License key
 * @return string Masked key
 */
function sfb_mask_license_key($key) {
	if (strlen($key) <= 12) {
		return str_repeat('•', strlen($key) - 4) . substr($key, -4);
	}

	return substr($key, 0, 4) . str_repeat('•', strlen($key) - 8) . substr($key, -4);
}

/**
 * Check if Pro features should be enabled
 *
 * Replaces/enhances the existing sfb_is_pro_active() function.
 *
 * @return bool True if license is active
 */
function sfb_is_license_active() {
	// Allow dev bypass
	if (defined('SFB_PRO_DEV') && SFB_PRO_DEV === true) {
		return true;
	}

	$status = sfb_get_license_status();

	return $status['is_active'] === true;
}

/**
 * Clear license cache (useful for testing or troubleshooting)
 *
 * @return void
 */
function sfb_clear_license_cache() {
	delete_transient('sfb_license_check_cache');

	$data = sfb_get_license_data();
	$data['last_check'] = 0;
	update_option('sfb_license_data', $data, false);
}
