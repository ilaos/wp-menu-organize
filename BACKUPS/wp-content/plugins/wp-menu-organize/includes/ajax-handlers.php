<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get built-in templates
 * @return array
 */
function wmo_get_builtin_templates() {
    return array(
        'default' => array(
            'name' => 'Default Layout',
            'description' => 'The standard WordPress admin menu layout',
            'layout' => array()
        ),
        'minimal' => array(
            'name' => 'Minimal',
            'description' => 'A simplified menu with only essential items',
            'layout' => array(
                'dashboard' => 0,
                'posts' => 10,
                'media' => 20,
                'pages' => 30,
                'users' => 40,
                'settings' => 50
            )
        ),
        'content-focused' => array(
            'name' => 'Content Focused',
            'description' => 'Prioritizes content management items',
            'layout' => array(
                'dashboard' => 0,
                'posts' => 10,
                'pages' => 20,
                'media' => 30,
                'comments' => 40,
                'appearance' => 50,
                'plugins' => 60,
                'users' => 70,
                'tools' => 80,
                'settings' => 90
            )
        )
    );
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

add_action('wp_ajax_wmo_import_configuration', 'wmo_import_configuration');

// Template system handlers
function wmo_load_templates()
{
    error_log('WMO: wmo_load_templates START - File version: NEW');
    error_log('WMO: wmo_load_templates function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for load templates');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce for load templates');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    
    error_log('WMO: About to call wmo_get_builtin_templates');
    $builtin_templates = wmo_get_builtin_templates();
    error_log('WMO: Got builtin templates: ' . print_r($builtin_templates, true));
    
    $custom_templates = get_option('wmo_custom_templates', array());
    error_log('WMO: Got custom templates: ' . print_r($custom_templates, true));
    
    // Simple template for testing
    $templates = array(
        array(
            'id' => 'executive_suite',
            'name' => 'Executive Suite',
            'description' => 'Professional navy blue design',
            'category' => 'business',
            'preview' => 'Navy blue colors'
        )
    );
    
    error_log('WMO: Loaded ' . count($templates) . ' templates for category: ' . $category);
    wp_send_json_success(array(
        'templates' => $templates,
        'category' => $category
    ));
}
add_action('wp_ajax_wmo_load_templates', 'wmo_load_templates');

function wmo_save_template()
{
    error_log('WMO: wmo_save_template function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for save template');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce for save template');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'custom';
    $include_colors = isset($_POST['include_colors']) && $_POST['include_colors'] === 'true';
    $include_typography = isset($_POST['include_typography']) && $_POST['include_typography'] === 'true';
    $include_badges = isset($_POST['include_badges']) && $_POST['include_badges'] === 'true';
    $include_theme = isset($_POST['include_theme']) && $_POST['include_theme'] === 'true';
    
    if (empty($name)) {
        wp_send_json_error('Template name is required');
        return;
    }
    
    // Collect current settings
    $template_data = array();
    
    if ($include_colors) {
        $template_data['colors'] = get_option('wmo_menu_colors', array());
    }
    
    if ($include_typography) {
        $template_data['typography'] = get_option('wmo_menu_typography', array());
    }
    
    if ($include_badges) {
        $template_data['badges'] = get_option('wmo_menu_badges', array());
    }
    
    if ($include_theme) {
        $template_data['theme'] = array(
            'dark_mode' => get_option('wmo_dark_mode', false)
        );
    }
    
    // Create template object
    $template = array(
        'id' => 'custom_' . time() . '_' . rand(1000, 9999),
        'name' => $name,
        'description' => $description,
        'category' => $category,
        'created_at' => current_time('c'),
        'data' => $template_data,
        'preview' => wmo_generate_template_preview($template_data)
    );
    
    // Save to custom templates
    $custom_templates = get_option('wmo_custom_templates', array());
    $custom_templates[$template['id']] = $template;
    
    $saved = update_option('wmo_custom_templates', $custom_templates);
    
    if ($saved !== false) {
        error_log('WMO: Template saved successfully: ' . $name);
        wp_send_json_success(array(
            'message' => 'Template saved successfully',
            'template' => $template
        ));
    } else {
        error_log('WMO: Failed to save template: ' . $name);
        wp_send_json_error('Failed to save template');
    }
}
add_action('wp_ajax_wmo_save_template', 'wmo_save_template');

function wmo_apply_template()
{
    error_log('WMO: wmo_apply_template function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for apply template');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce for apply template');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    
    if (empty($template_id)) {
        wp_send_json_error('Template ID is required');
        return;
    }
    
    // Get template data
    $template = wmo_get_template_by_id($template_id);
    
    if (!$template) {
        wp_send_json_error('Template not found');
        return;
    }
    
    $applied_items = array();
    $errors = array();
    
    // Apply template data
    try {
        if (isset($template['data']['colors']) && !empty($template['data']['colors'])) {
            update_option('wmo_menu_colors', $template['data']['colors']);
            $applied_items[] = 'Colors (' . count($template['data']['colors']) . ' items)';
        }
        
        if (isset($template['data']['typography']) && !empty($template['data']['typography'])) {
            update_option('wmo_menu_typography', $template['data']['typography']);
            $applied_items[] = 'Typography (' . count($template['data']['typography']) . ' items)';
        }
        
        if (isset($template['data']['badges']) && !empty($template['data']['badges'])) {
            update_option('wmo_menu_badges', $template['data']['badges']);
            $applied_items[] = 'Badges (' . count($template['data']['badges']) . ' items)';
        }
        
        if (isset($template['data']['theme']) && !empty($template['data']['theme'])) {
            if (isset($template['data']['theme']['dark_mode'])) {
                update_option('wmo_dark_mode', (bool)$template['data']['theme']['dark_mode']);
                $applied_items[] = 'Theme settings';
            }
        }
        
    } catch (Exception $e) {
        $errors[] = 'Error applying template: ' . $e->getMessage();
        error_log('WMO: Template apply error: ' . $e->getMessage());
    }
    
    if (!empty($applied_items)) {
        error_log('WMO: Template applied successfully: ' . $template['name']);
        wp_send_json_success(array(
            'message' => 'Template applied successfully',
            'applied_items' => $applied_items,
            'errors' => $errors,
            'template_name' => $template['name']
        ));
    } else {
        wp_send_json_error('No valid template data found to apply');
    }
}
add_action('wp_ajax_wmo_apply_template', 'wmo_apply_template');

function wmo_delete_template()
{
    error_log('WMO: wmo_delete_template function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for delete template');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce for delete template');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    
    if (empty($template_id)) {
        wp_send_json_error('Template ID is required');
        return;
    }
    
    // Only allow deletion of custom templates
    if (!str_starts_with($template_id, 'custom_')) {
        wp_send_json_error('Cannot delete built-in templates');
        return;
    }
    
    $custom_templates = get_option('wmo_custom_templates', array());
    
    if (!isset($custom_templates[$template_id])) {
        wp_send_json_error('Template not found');
        return;
    }
    
    $template_name = $custom_templates[$template_id]['name'];
    unset($custom_templates[$template_id]);
    
    $saved = update_option('wmo_custom_templates', $custom_templates);
    
    if ($saved !== false) {
        error_log('WMO: Template deleted successfully: ' . $template_name);
        wp_send_json_success(array(
            'message' => 'Template deleted successfully',
            'template_name' => $template_name
        ));
    } else {
        error_log('WMO: Failed to delete template: ' . $template_name);
        wp_send_json_error('Failed to delete template');
    }
}
add_action('wp_ajax_wmo_delete_template', 'wmo_delete_template');

// Helper functions for templates
function wmo_get_template_by_id($template_id)
{
    // Check built-in templates first
    $builtin_templates = wmo_get_builtin_templates();
    if (isset($builtin_templates[$template_id])) {
        return $builtin_templates[$template_id];
    }
    
    // Check custom templates
    $custom_templates = get_option('wmo_custom_templates', array());
    if (isset($custom_templates[$template_id])) {
        return $custom_templates[$template_id];
    }
    
    return null;
}

function wmo_generate_template_preview($template_data)
{
    $preview_parts = array();
    
    if (isset($template_data['colors']) && !empty($template_data['colors'])) {
        $preview_parts[] = count($template_data['colors']) . ' menu colors';
    }
    
    if (isset($template_data['typography']) && !empty($template_data['typography'])) {
        $preview_parts[] = count($template_data['typography']) . ' typography settings';
    }
    
    if (isset($template_data['badges']) && !empty($template_data['badges'])) {
        $preview_parts[] = count($template_data['badges']) . ' badges';
    }
    
    if (isset($template_data['theme'])) {
        $theme_mode = $template_data['theme']['dark_mode'] ? 'dark' : 'light';
        $preview_parts[] = $theme_mode . ' theme';
    }
    
    return !empty($preview_parts) ? implode(', ', $preview_parts) : 'Custom template';
} 