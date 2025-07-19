<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function wmo_save_menu_order()
{
    error_log('WMO: save_menu_order called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $new_order = isset($_POST['order']) ? $_POST['order'] : array();
    error_log('WMO: Received order data: ' . print_r($new_order, true));

    if (!empty($new_order)) {
        $update_result = update_option('wmo_menu_order', $new_order);
        error_log('WMO: Update result: ' . ($update_result ? 'success' : 'failed'));

        if ($update_result) {
            error_log('WMO: Menu order saved successfully');
            wp_send_json_success(array(
                'message' => 'Menu order saved successfully',
                'order' => $new_order
            ));
        } else {
            error_log('WMO: Failed to save menu order');
            wp_send_json_error('Failed to save menu order');
        }
    } else {
        error_log('WMO: No order data received');
        wp_send_json_error('No order data received');
    }
}
add_action('wp_ajax_wmo_save_menu_order', 'wmo_save_menu_order');

function wmo_apply_custom_menu_order($menu_order)
{
    $custom_order = get_option('wmo_menu_order', array());

    if (empty($custom_order)) {
        return $menu_order;
    }

    $final_menu = array();
    $default_menu_lookup = array_flip($menu_order);

    foreach ($custom_order as $slug) {
        if (isset($default_menu_lookup[$slug])) {
            $final_menu[] = $slug;
            unset($default_menu_lookup[$slug]);
        }
    }

    // Add any remaining default items that were not in the custom order
    foreach ($menu_order as $slug) {
        if (!in_array($slug, $final_menu)) {
            $final_menu[] = $slug;
        }
    }

    return $final_menu;
}
add_filter('menu_order', 'wmo_apply_custom_menu_order', 10, 1);
add_filter('custom_menu_order', '__return_true');

function wmo_save_menu_colors()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
        return;
    }

    $menu_colors = isset($_POST['menu_colors']) ? $_POST['menu_colors'] : array();

    if (!empty($menu_colors)) {
        $sanitized_colors = array_map('sanitize_hex_color', $menu_colors);
        update_option('wmo_menu_colors', $sanitized_colors);
        wp_send_json_success(array(
            'message' => 'Menu colors saved successfully',
            'colors' => $sanitized_colors
        ));
    } else {
        wp_send_json_error('No color data received');
    }
}
add_action('wp_ajax_wmo_save_menu_colors', 'wmo_save_menu_colors');

function wmo_apply_menu_colors()
{
    $menu_colors = get_option('wmo_menu_colors', array());
    if (!empty($menu_colors)) {
        echo '<style type="text/css">';
        foreach ($menu_colors as $slug => $color) {
            echo "#menu-{$slug} > a, #toplevel_page_{$slug} > a { color: {$color} !important; }";
            echo "#menu-{$slug} > a .wp-menu-image:before, #toplevel_page_{$slug} > a .wp-menu-image:before { color: {$color} !important; }";
        }
        echo '</style>';
    }
}
add_action('admin_head', 'wmo_apply_menu_colors');
