# Error Handling Standardization

## Overview

This plugin now uses a standardized error handling system that provides:
- **Consistent error responses** across all AJAX endpoints
- **User-friendly error messages** that don't expose technical details
- **Proper HTTP status codes** for different error types
- **Security-focused validation** with comprehensive input sanitization
- **Development-only logging** to avoid exposing sensitive information

## Error Types

The system categorizes errors into the following types:

| Error Type | HTTP Status | Description |
|------------|-------------|-------------|
| `security` | 403 | Security-related errors (nonce failures, unauthorized access) |
| `validation` | 400 | Input validation errors (invalid data format, missing fields) |
| `permission` | 403 | Permission-related errors (insufficient capabilities) |
| `not_found` | 404 | Resource not found errors |
| `server_error` | 500 | Server-side errors (exceptions, system failures) |
| `database` | 500 | Database-related errors |
| `file_system` | 500 | File system errors |

## Error Codes

### Security Errors
- `security_check_failed` - Nonce verification failed
- `insufficient_permissions` - User lacks required capabilities

### Validation Errors
- `invalid_data_format` - Data format is invalid
- `invalid_color_format` - Color format is invalid (should be hex)
- `invalid_menu_id` - Menu identifier is invalid
- `invalid_json` - JSON format is invalid
- `missing_required_field` - Required field is missing

### Database Errors
- `database_error` - General database error
- `save_failed` - Failed to save data
- `export_failed` - Failed to export configuration
- `import_failed` - Failed to import configuration

### File System Errors
- `file_not_found` - Requested file not found
- `file_upload_failed` - File upload failed
- `invalid_file_type` - Invalid file type
- `file_too_large` - File size exceeds limit

### Network Errors
- `network_error` - Network connection error
- `timeout_error` - Request timed out

## Usage Examples

### Basic AJAX Error Handling

```php
// Old way (inconsistent)
wp_send_json_error('Failed to save menu order');

// New way (standardized)
wmo_ajax_error('save_failed', 'database');
```

### Input Validation

```php
// Validate AJAX request
$validation = wmo_validate_ajax_request();
if (is_wp_error($validation)) {
    wmo_ajax_error($validation->get_error_code(), $validation->get_error_data());
}

// Validate input data
$rules = [
    'menu_id' => [
        'required' => true,
        'type' => 'string',
        'sanitize' => 'slug'
    ],
    'color' => [
        'required' => true,
        'type' => 'color',
        'sanitize' => 'color'
    ]
];

$validated = wmo_validate_input($_POST, $rules);
if (is_wp_error($validated)) {
    wmo_ajax_error('validation_failed', 'validation', ['errors' => $validated->get_error_message()]);
}
```

### Exception Handling

```php
try {
    // Your code here
    $result = some_risky_operation();
    wmo_ajax_success($result, 'Operation completed successfully');
} catch (Exception $e) {
    WMO_Error_Handler::handle_exception($e, 'some_risky_operation');
}
```

## Response Format

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "save_failed",
        "message": "Failed to save changes. Please try again.",
        "type": "database",
        "data": {
            "additional_info": "Optional additional data"
        }
    }
}
```

### Success Response
```json
{
    "success": true,
    "data": {
        "result": "Your data here"
    },
    "message": "Operation completed successfully"
}
```

## Security Features

### Input Sanitization
- **Text fields**: `sanitize_text_field()`
- **Textarea**: `sanitize_textarea_field()`
- **Email**: `sanitize_email()`
- **URL**: `esc_url_raw()`
- **Color**: `sanitize_hex_color()`
- **Slug**: `sanitize_title()`

### Validation Types
- **String**: Basic string validation
- **Integer**: Numeric validation
- **Float**: Numeric validation
- **Boolean**: Boolean validation
- **Array**: Array validation
- **Email**: Email format validation
- **URL**: URL format validation
- **Color**: Hex color validation

### Security Checks
- **User authentication**: Ensures user is logged in
- **Capability checks**: Verifies user permissions
- **Nonce verification**: Prevents CSRF attacks
- **Input validation**: Validates all user input
- **Output sanitization**: Sanitizes all output

## Logging

Error logging is **only enabled in development** (when `WP_DEBUG` is true) to:
- Prevent sensitive information from being logged in production
- Maintain security best practices
- Reduce server log bloat

## Migration Guide

### Updating Existing Code

1. **Replace direct error responses**:
   ```php
   // Old
   wp_send_json_error('Failed to save');
   
   // New
   wmo_ajax_error('save_failed', 'database');
   ```

2. **Add input validation**:
   ```php
   // Old
   $data = $_POST['data'];
   
   // New
   $rules = ['data' => ['required' => true, 'type' => 'string']];
   $data = wmo_validate_input($_POST, $rules);
   ```

3. **Add security checks**:
   ```php
   // Old
   if (!current_user_can('manage_options')) {
       wp_die('Unauthorized');
   }
   
   // New
   $validation = wmo_validate_ajax_request();
   if (is_wp_error($validation)) {
       wmo_ajax_error($validation->get_error_code(), $validation->get_error_data());
   }
   ```

## Benefits

1. **Consistency**: All errors follow the same format
2. **Security**: Comprehensive input validation and sanitization
3. **User Experience**: User-friendly error messages
4. **Maintainability**: Centralized error handling logic
5. **Debugging**: Development-only logging for troubleshooting
6. **Standards Compliance**: Follows WordPress coding standards

## Best Practices

1. **Always validate input** before processing
2. **Use appropriate error types** for different scenarios
3. **Provide user-friendly messages** in production
4. **Log errors only in development** to maintain security
5. **Handle exceptions gracefully** to prevent fatal errors
6. **Use consistent error codes** across the plugin
