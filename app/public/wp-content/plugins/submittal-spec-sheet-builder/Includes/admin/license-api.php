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
define('SFB_LICENSE_VERSION', '1.0.0');

// WooCommerce Product IDs (actual post IDs from WooCommerce)
define('SFB_WC_PRODUCT_ID_PRO', '199'); // Submittal Builder Pro
define('SFB_WC_PRODUCT_ID_AGENCY', '225'); // Submittal Builder Agency

// Product identifiers for internal use (returned by WooCommerce API)
define('SFB_PRODUCT_ID_PRO', 'SUBMITTAL-BUILDER-PRO');
define('SFB_PRODUCT_ID_AGENCY', 'SUBMITTAL-BUILDER-AGENCY');

/**
 * Get current license data from WordPress options
 *
 * @return array License data with keys: key, email, status, expires, last_check, error, product_id
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
		'product_id' => '', // Product identifier from WooCommerce API response
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
 * @param array $args Additional arguments (license_key, email, product_id)
 * @return array|WP_Error Response data or error
 */
function sfb_remote_license_request($action, $args = []) {
	$defaults = [
		'license_key' => '',
		'email'       => '',
		'product_id'  => '',
	];

	$args = wp_parse_args($args, $defaults);

	// Build API URL with all required parameters
	$api_params = [
		'request'    => $action,
		'email'      => $args['email'],
		'license_key' => $args['license_key'],
		'instance'   => sfb_get_instance_id(),
		'version'    => SFB_LICENSE_VERSION,
	];

	// Add product_id if provided (required for activation)
	if (!empty($args['product_id'])) {
		$api_params['product_id'] = $args['product_id'];
	}

	$url = add_query_arg($api_params, SFB_LICENSE_API_URL);

	// DEBUG: Log API request details
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('--- API REQUEST DETAILS ---');
		error_log('Action: ' . $action);
		error_log('Full URL (with params): ' . $url);
		error_log('API Parameters being sent:');
		error_log('  - request: ' . $action);
		error_log('  - email: ' . $args['email']);
		error_log('  - license_key: ' . $args['license_key']);
		error_log('  - instance: ' . sfb_get_instance_id());
		error_log('  - version: ' . SFB_LICENSE_VERSION);
		error_log('  - product_id: ' . (!empty($args['product_id']) ? $args['product_id'] : 'NOT SET'));
		error_log('API Base URL: ' . SFB_LICENSE_API_URL);
	}

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
			__('Could not connect to license server. Please check your internet connection and try again.', 'submittal-spec-sheet-builder'),
			['original_error' => $response->get_error_message()]
		);
	}

	$code = wp_remote_retrieve_response_code($response);
	$body = wp_remote_retrieve_body($response);

	// DEBUG: Log API response
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('--- API RESPONSE DETAILS ---');
		error_log('HTTP Status Code: ' . $code);
		error_log('Response Headers: ' . print_r(wp_remote_retrieve_headers($response), true));
		error_log('Response Body (raw): ' . $body);
	}

	// Parse JSON response
	$data = json_decode($body, true);

	// DEBUG: Log parsed data
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('Parsed JSON data: ' . print_r($data, true));
		if (json_last_error() !== JSON_ERROR_NONE) {
			error_log('JSON Parse Error: ' . json_last_error_msg());
		}
	}

	if ($code !== 200) {
		return new WP_Error(
			'api_error',
			__('License server returned an error. Please try again later.', 'submittal-spec-sheet-builder'),
			['code' => $code, 'body' => $body]
		);
	}

	if (json_last_error() !== JSON_ERROR_NONE) {
		return new WP_Error(
			'invalid_response',
			__('Invalid response from license server.', 'submittal-spec-sheet-builder'),
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
	// DEBUG: Log activation attempt
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('=== SFB LICENSE ACTIVATION ATTEMPT ===');
		error_log('License Key (full): ' . $license_key);
		error_log('Email: ' . $email);
		error_log('Site Instance ID: ' . sfb_get_instance_id());
		error_log('Site URL: ' . get_site_url());
		error_log('NOTE: NOT sending product_id - WooCommerce will determine from license key');
	}

	// Make activation request (no product_id needed - WooCommerce determines from license key)
	$response = sfb_remote_license_request('activation', [
		'license_key' => $license_key,
		'email'       => $email,
	]);

	// DEBUG: Log activation result
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('Activation response type: ' . (is_wp_error($response) ? 'WP_Error' : 'Array'));
		if (is_wp_error($response)) {
			error_log('Activation error message: ' . $response->get_error_message());
		} else {
			error_log('Activation response data: ' . print_r($response, true));
		}
	}

	if (is_wp_error($response)) {
		return $response;
	}

	// WooCommerce Software API returns different structures
	// Check for success indicators
	if (isset($response['activated']) && $response['activated'] === true) {
		// Successful activation - get product identifier from API response
		$product_identifier = isset($response['product_id']) ? $response['product_id'] : '';

		$license_data = [
			'key'    => $license_key,
			'email'  => $email,
			'status' => 'active',
			'expires' => isset($response['expire_date']) ? $response['expire_date'] : '',
			'activations_remaining' => isset($response['activations_remaining']) ? $response['activations_remaining'] : null,
			'product_id' => $product_identifier, // Product identifier from WooCommerce
			'error'  => '',
		];

		sfb_update_license_data($license_data);

		// Clear cache
		delete_transient('sfb_license_check_cache');

		// DEBUG: Log successful activation
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('=== LICENSE ACTIVATION SUCCESSFUL ===');
			error_log('License data saved: ' . print_r($license_data, true));
		}

		// Log the successful activation
		sfb_log_license_operation('activate', $license_key, $email, 'success');

		return [
			'success' => true,
			'message' => __('License activated successfully!', 'submittal-spec-sheet-builder'),
			'data'    => $license_data,
		];
	}

	// Check for error messages
	if (isset($response['error'])) {
		$error_msg = $response['error'];

		// DEBUG: Log activation failure details
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('=== LICENSE ACTIVATION FAILED ===');
			error_log('Error Message: ' . $error_msg);
			error_log('Error Code: ' . (isset($response['code']) ? $response['code'] : 'N/A'));
			error_log('Full API Response: ' . print_r($response, true));
			error_log('License Key Used: ' . $license_key);
			error_log('Email Used: ' . $email);
		}

		// Log the failed activation
		sfb_log_license_operation('activate', $license_key, $email, 'failed', $error_msg);

		// Map common errors to user-friendly messages
		if (stripos($error_msg, 'activation limit') !== false) {
			$error_msg = __('This license has reached its activation limit. Please deactivate it on another site or upgrade your license.', 'submittal-spec-sheet-builder');
		} elseif (stripos($error_msg, 'expired') !== false) {
			$error_msg = __('This license has expired. Please renew your license to continue receiving updates.', 'submittal-spec-sheet-builder');
		} elseif (stripos($error_msg, 'invalid') !== false) {
			// Keep the original error message for now to help with debugging
			$error_msg = __('Invalid license key or email address. ', 'submittal-spec-sheet-builder') . ' ' . __('API Error: ', 'submittal-spec-sheet-builder') . $error_msg;
		}

		return new WP_Error('activation_failed', $error_msg);
	}

	// Unknown response format
	$error_wp = new WP_Error(
		'unknown_response',
		__('Unexpected response from license server. Please contact support.', 'submittal-spec-sheet-builder'),
		$response
	);

	// Log the failed activation
	sfb_log_license_operation('activate', $license_key, $email, 'failed', 'Unknown response format from license server');

	return $error_wp;
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
		return new WP_Error('missing_key', __('No license key provided.', 'submittal-spec-sheet-builder'));
	}

	// Make deactivation request (no product_id needed - WooCommerce determines from license key)
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

		// Log the successful deactivation
		sfb_log_license_operation('deactivate', $license_key, $email, 'success');

		return [
			'success' => true,
			'message' => __('License deactivated successfully.', 'submittal-spec-sheet-builder'),
		];
	}

	// Check for errors
	if (isset($response['error'])) {
		// Log the failed deactivation
		sfb_log_license_operation('deactivate', $license_key, $email, 'failed', $response['error']);

		return new WP_Error('deactivation_failed', $response['error']);
	}

	// Log the failed deactivation
	sfb_log_license_operation('deactivate', $license_key, $email, 'failed', 'Unknown response from license server');

	return new WP_Error(
		'unknown_response',
		__('Unexpected response from license server.', 'submittal-spec-sheet-builder'),
		$response
	);
}

/**
 * Log license operation for audit trail
 *
 * @param string $action Action performed ('activate' or 'deactivate')
 * @param string $license_key License key (will be hashed)
 * @param string $email Customer email (will be hashed)
 * @param string $status Operation status ('success' or 'failed')
 * @param string $error_message Error message if failed (optional)
 * @return bool True on success, false on failure
 */
function sfb_log_license_operation($action, $license_key, $email, $status, $error_message = '') {
	global $wpdb;
	$table = $wpdb->prefix . 'sfb_license_log';

	// Hash sensitive data for privacy/compliance
	$license_key_hash = hash('sha256', $license_key);
	$email_hash = hash('sha256', strtolower(trim($email)));

	// Get client IP and hash it
	$ip = sfb_get_client_ip_for_logging();
	$ip_hash = hash('sha256', $ip);

	// Get user agent
	$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '';

	// Insert log entry
	$inserted = $wpdb->insert(
		$table,
		[
			'action' => $action,
			'license_key_hash' => $license_key_hash,
			'email_hash' => $email_hash,
			'status' => $status,
			'error_message' => $error_message,
			'ip_hash' => $ip_hash,
			'user_agent' => $user_agent,
			'created_at' => current_time('mysql'),
		],
		['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
	);

	if ($inserted === false) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('SFB License Log: Failed to insert log entry - ' . $wpdb->last_error);
		}
		return false;
	}

	return true;
}

/**
 * Get client IP address for logging
 * Handles proxy headers
 *
 * @return string IP address
 */
function sfb_get_client_ip_for_logging() {
	$ip_keys = [
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR'
	];

	foreach ($ip_keys as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				$ip = trim($ip);
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}

	return '0.0.0.0';
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
			'message' => __('No license key entered.', 'submittal-spec-sheet-builder'),
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

	// Make API request (no product_id needed - WooCommerce determines from license key)
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
				'message' => __('Using cached license status (server temporarily unreachable).', 'submittal-spec-sheet-builder'),
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
		'product_id' => $status_data['product_id'] ?? '',
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
		'product_id' => '',
	];

	// Check for active license
	if (isset($response['status']) && $response['status'] === 'active') {
		$status_data['status'] = 'active';
		$status_data['message'] = __('License is active and valid.', 'submittal-spec-sheet-builder');
	} elseif (isset($response['check']) && $response['check'] === 'valid') {
		$status_data['status'] = 'active';
		$status_data['message'] = __('License is active and valid.', 'submittal-spec-sheet-builder');
	}

	// Check for expired
	if (isset($response['status']) && $response['status'] === 'expired') {
		$status_data['status'] = 'expired';
		$status_data['message'] = __('License has expired. Please renew to continue receiving updates.', 'submittal-spec-sheet-builder');
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

	// Get product_id (license tier)
	if (isset($response['product_id'])) {
		$status_data['product_id'] = $response['product_id'];
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
			$result['status_label'] = __('Active', 'submittal-spec-sheet-builder');
			$result['status_color'] = '#10b981'; // green
			$result['is_active'] = true;
			break;

		case 'expired':
			$result['status_label'] = __('Expired', 'submittal-spec-sheet-builder');
			$result['status_color'] = '#f59e0b'; // amber
			break;

		case 'invalid':
			$result['status_label'] = __('Invalid', 'submittal-spec-sheet-builder');
			$result['status_color'] = '#ef4444'; // red
			break;

		case 'inactive':
		default:
			$result['status_label'] = __('Inactive', 'submittal-spec-sheet-builder');
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
 * Returns true for both Pro and Agency licenses.
 * For backwards compatibility with existing licenses (no product_id), defaults to Pro-level access.
 *
 * @return bool True if license is active (Pro or Agency)
 */
function sfb_is_pro_active() {
	// Allow dev bypass
	if (defined('SFB_PRO_DEV') && SFB_PRO_DEV === true) {
		return true;
	}

	$license_data = sfb_get_license_data();

	// Check if license is active
	if ($license_data['status'] !== 'active') {
		return false;
	}

	// If no product_id (backwards compatibility), assume Pro-level access
	if (empty($license_data['product_id'])) {
		return true;
	}

	// Check if product_id matches Pro or Agency
	return in_array($license_data['product_id'], [
		SFB_PRODUCT_ID_PRO,
		SFB_PRODUCT_ID_AGENCY
	], true);
}

/**
 * Check if Agency features should be enabled
 *
 * Returns true ONLY for Agency licenses.
 *
 * @return bool True if license is active Agency tier
 */
function sfb_is_agency_license() {
	// Allow dev bypass
	if (defined('SFB_AGENCY_DEV') && SFB_AGENCY_DEV === true) {
		return true;
	}

	$license_data = sfb_get_license_data();

	// Check if license is active
	if ($license_data['status'] !== 'active') {
		return false;
	}

	// Check if product_id matches Agency
	return $license_data['product_id'] === SFB_PRODUCT_ID_AGENCY;
}

/**
 * Backwards compatibility alias
 *
 * @deprecated Use sfb_is_pro_active() instead
 * @return bool True if license is active
 */
function sfb_is_license_active() {
	return sfb_is_pro_active();
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
