<?php
/**
 * Standardized Error Handling for WP Menu Organize
 * 
 * This file provides consistent error handling across the plugin
 * with proper logging, user feedback, and security measures.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WMO Error Handler Class
 */
class WMO_Error_Handler {
    
    /**
     * Error types and their corresponding HTTP status codes
     */
    const ERROR_TYPES = [
        'security' => 403,
        'validation' => 400,
        'permission' => 403,
        'not_found' => 404,
        'server_error' => 500,
        'database' => 500,
        'file_system' => 500
    ];
    
    /**
     * User-friendly error messages
     */
    const ERROR_MESSAGES = [
        'security_check_failed' => 'Security verification failed. Please refresh the page and try again.',
        'insufficient_permissions' => 'You do not have permission to perform this action.',
        'invalid_data_format' => 'The provided data format is invalid.',
        'invalid_color_format' => 'Please provide a valid color in hex format (e.g., #ff0000).',
        'invalid_menu_id' => 'Please provide a valid menu identifier.',
        'file_not_found' => 'The requested file could not be found.',
        'database_error' => 'A database error occurred. Please try again.',
        'import_failed' => 'Failed to import configuration. Please check the file format.',
        'export_failed' => 'Failed to export configuration. Please try again.',
        'save_failed' => 'Failed to save changes. Please try again.',
        'template_not_found' => 'The requested template could not be found.',
        'invalid_json' => 'Invalid JSON format provided.',
        'missing_required_field' => 'Required field is missing.',
        'file_upload_failed' => 'File upload failed. Please try again.',
        'invalid_file_type' => 'Invalid file type. Please upload a valid file.',
        'file_too_large' => 'File is too large. Please choose a smaller file.',
        'network_error' => 'Network error occurred. Please check your connection.',
        'timeout_error' => 'Request timed out. Please try again.',
        'unknown_error' => 'An unexpected error occurred. Please try again.'
    ];
    
    /**
     * Handle AJAX errors with consistent response format
     * 
     * @param string $error_code Error code from ERROR_MESSAGES
     * @param string $error_type Error type from ERROR_TYPES
     * @param array $additional_data Additional data to include in response
     * @param bool $log_error Whether to log the error
     */
    public static function handle_ajax_error($error_code, $error_type = 'validation', $additional_data = [], $log_error = true) {
        $message = self::get_error_message($error_code);
        $status_code = self::ERROR_TYPES[$error_type] ?? 400;
        
        $response = [
            'success' => false,
            'error' => [
                'code' => $error_code,
                'message' => $message,
                'type' => $error_type
            ]
        ];
        
        if (!empty($additional_data)) {
            $response['error']['data'] = $additional_data;
        }
        
        if ($log_error) {
            self::log_error($error_code, $error_type, $additional_data);
        }
        
        wp_send_json($response, $status_code);
    }
    
    /**
     * Handle AJAX success with consistent response format
     * 
     * @param array $data Response data
     * @param string $message Success message
     * @param int $status_code HTTP status code
     */
    public static function handle_ajax_success($data = [], $message = '', $status_code = 200) {
        $response = [
            'success' => true,
            'data' => $data
        ];
        
        if (!empty($message)) {
            $response['message'] = $message;
        }
        
        wp_send_json($response, $status_code);
    }
    
    /**
     * Validate AJAX request with security checks
     * 
     * @param string $nonce_action Nonce action name
     * @param string $capability Required capability
     * @return bool|WP_Error True if valid, WP_Error if invalid
     */
    public static function validate_ajax_request($nonce_action = 'wmo_ajax_nonce', $capability = 'manage_options') {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return new WP_Error('not_logged_in', 'User not logged in', 'security');
        }
        
        // Check user capabilities
        if (!current_user_can($capability)) {
            return new WP_Error('insufficient_permissions', 'Insufficient permissions', 'permission');
        }
        
        // Verify nonce
        if (!check_ajax_referer($nonce_action, 'nonce', false)) {
            return new WP_Error('security_check_failed', 'Security check failed', 'security');
        }
        
        return true;
    }
    
    /**
     * Validate and sanitize input data
     * 
     * @param array $data Input data
     * @param array $rules Validation rules
     * @return array|WP_Error Validated data or WP_Error
     */
    public static function validate_input($data, $rules) {
        $validated = [];
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = isset($data[$field]) ? $data[$field] : null;
            
            // Check if required
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[] = sprintf('Field "%s" is required', $field);
                continue;
            }
            
            // Skip validation if field is empty and not required
            if (empty($value) && !isset($rule['required'])) {
                continue;
            }
            
            // Type validation
            if (isset($rule['type'])) {
                $valid = self::validate_type($value, $rule['type']);
                if (!$valid) {
                    $errors[] = sprintf('Field "%s" must be of type %s', $field, $rule['type']);
                    continue;
                }
            }
            
            // Sanitize value
            if (isset($rule['sanitize'])) {
                $value = self::sanitize_value($value, $rule['sanitize']);
            }
            
            // Custom validation
            if (isset($rule['validate']) && is_callable($rule['validate'])) {
                $result = call_user_func($rule['validate'], $value);
                if (is_wp_error($result)) {
                    $errors[] = $result->get_error_message();
                    continue;
                }
                $value = $result;
            }
            
            $validated[$field] = $value;
        }
        
        if (!empty($errors)) {
            return new WP_Error('validation_failed', implode('; ', $errors), 'validation');
        }
        
        return $validated;
    }
    
    /**
     * Validate data type
     * 
     * @param mixed $value Value to validate
     * @param string $type Expected type
     * @return bool
     */
    private static function validate_type($value, $type) {
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'integer':
                return is_numeric($value) && floor($value) == $value;
            case 'float':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value) || in_array($value, ['0', '1', 'true', 'false'], true);
            case 'array':
                return is_array($value);
            case 'email':
                return is_email($value);
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL);
            case 'color':
                return preg_match('/^#[a-f0-9]{6}$/i', $value);
            default:
                return true;
        }
    }
    
    /**
     * Sanitize value based on type
     * 
     * @param mixed $value Value to sanitize
     * @param string $type Sanitization type
     * @return mixed Sanitized value
     */
    private static function sanitize_value($value, $type) {
        switch ($type) {
            case 'text':
                return sanitize_text_field($value);
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'email':
                return sanitize_email($value);
            case 'url':
                return esc_url_raw($value);
            case 'int':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'bool':
                return (bool) $value;
            case 'array':
                return is_array($value) ? array_map('sanitize_text_field', $value) : [];
            case 'color':
                return sanitize_hex_color($value);
            case 'slug':
                return sanitize_title($value);
            default:
                return $value;
        }
    }
    
    /**
     * Get user-friendly error message
     * 
     * @param string $error_code Error code
     * @return string Error message
     */
    public static function get_error_message($error_code) {
        return self::ERROR_MESSAGES[$error_code] ?? self::ERROR_MESSAGES['unknown_error'];
    }
    
    /**
     * Log error for debugging (only in development)
     * 
     * @param string $error_code Error code
     * @param string $error_type Error type
     * @param array $additional_data Additional data
     */
    private static function log_error($error_code, $error_type, $additional_data = []) {
        // Only log in development or when WP_DEBUG is enabled
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $log_message = sprintf(
            '[WMO Error] Code: %s, Type: %s, Data: %s',
            $error_code,
            $error_type,
            json_encode($additional_data)
        );
        
        error_log($log_message);
    }
    
    /**
     * Handle exceptions gracefully
     * 
     * @param Exception $exception Exception to handle
     * @param string $context Context where exception occurred
     */
    public static function handle_exception($exception, $context = '') {
        $error_data = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'context' => $context
        ];
        
        // Log the exception
        self::log_error('exception', 'server_error', $error_data);
        
        // Return user-friendly error
        return self::handle_ajax_error('unknown_error', 'server_error', $error_data, false);
    }
}

/**
 * Convenience functions for backward compatibility
 */

/**
 * Validate AJAX request with standard checks
 */
function wmo_validate_ajax_request($nonce_action = 'wmo_ajax_nonce', $capability = 'manage_options') {
    return WMO_Error_Handler::validate_ajax_request($nonce_action, $capability);
}

/**
 * Handle AJAX error with standard format
 */
function wmo_ajax_error($error_code, $error_type = 'validation', $additional_data = [], $log_error = true) {
    return WMO_Error_Handler::handle_ajax_error($error_code, $error_type, $additional_data, $log_error);
}

/**
 * Handle AJAX success with standard format
 */
function wmo_ajax_success($data = [], $message = '', $status_code = 200) {
    return WMO_Error_Handler::handle_ajax_success($data, $message, $status_code);
}

/**
 * Validate and sanitize input data
 */
function wmo_validate_input($data, $rules) {
    return WMO_Error_Handler::validate_input($data, $rules);
}
