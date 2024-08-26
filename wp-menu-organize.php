<?php
/*
Plugin Name: WP Menu Organize
Description: A plugin to customize and organize the WordPress Admin Menu.
Version: 3.0.3
Author: Ish Laos
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('WMO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WMO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once WMO_PLUGIN_PATH . 'includes/helper-functions.php'; // Ensure this is included first
include_once WMO_PLUGIN_PATH . 'includes/admin-page.php';

// Initialize the plugin
function wmo_init() {
    WP_Menu_Organize::get_instance();
}
add_action('plugins_loaded', 'wmo_init');

function wmo_save_colors() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    $colors = isset($_POST['wmo_menu_colors']) ? $_POST['wmo_menu_colors'] : array();
    $sanitized_colors = array();

    foreach ($colors as $slug => $color) {
        $sanitized_colors[sanitize_text_field($slug)] = sanitize_hex_color($color);
    }

    update_option('wmo_menu_colors', $sanitized_colors);
    wp_send_json_success($sanitized_colors);
}
add_action('wp_ajax_save_wmo_colors', 'wmo_save_colors');