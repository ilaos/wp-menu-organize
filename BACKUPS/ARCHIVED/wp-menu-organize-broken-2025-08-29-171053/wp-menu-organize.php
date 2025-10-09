<?php
/*
Plugin Name: WP Menu Organize
Description: A plugin to customize and organize the WordPress Admin Menu.
Version: 3.1.7
Author: Ish Laos
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('WMO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WMO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once WMO_PLUGIN_PATH . 'includes/error-handler.php';
require_once WMO_PLUGIN_PATH . 'includes/helper-functions.php';
require_once WMO_PLUGIN_PATH . 'includes/admin-page.php';
require_once WMO_PLUGIN_PATH . 'includes/ajax-handlers.php';

// Initialize the plugin
function wmo_init()
{
    WP_Menu_Organize::get_instance();
}
add_action('plugins_loaded', 'wmo_init');

// Debug: Log the admin menu
function wmo_log_admin_menu()
{
    global $menu;
    // Removed debug logging for production
}
add_action('admin_menu', 'wmo_log_admin_menu', 100);

// Add settings link on plugin page
function wmo_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=wp-menu-organize-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'wmo_settings_link');

// Apply custom admin menu order
function wmo_apply_admin_menu_order($menu_order)
{
    // Get custom order from flat structure (not nested under admin_customizations)
    $custom_order = wmo_get_settings('menu_order');
    
    if (empty($custom_order) || !is_array($custom_order)) {
        return $menu_order;
    }
    
    // Get all current menu slugs from global $menu
    global $menu;
    $current_menu_slugs = array();
    
    if (is_array($menu)) {
        foreach ($menu as $menu_item) {
            if (!empty($menu_item[2]) && $menu_item[2] !== 'separator') {
                $current_menu_slugs[] = $menu_item[2];
            }
        }
    }
    
    // Merge: Start with custom_order, append any missing slugs from current menu in default order
    $merged_order = $custom_order;
    
    // Add any missing slugs from current menu that aren't in custom order
    foreach ($current_menu_slugs as $slug) {
        if (!in_array($slug, $merged_order)) {
            $merged_order[] = $slug;
        }
    }
    
    return $merged_order;
}
add_filter('menu_order', 'wmo_apply_admin_menu_order', 10);
add_filter('custom_menu_order', '__return_true');

// Apply custom labels and visibility to admin menu
function wmo_apply_admin_menu_customizations()
{
    global $menu, $submenu;
    $customizations = wmo_get_settings('admin_customizations');
    
    if (empty($customizations['items'])) {
        return;
    }
    
    // Get current user roles
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;
    
    // Apply customizations to main menu items
    foreach ($menu as $key => $menu_item) {
        if (empty($menu_item[2])) {
            continue;
        }
        
        $menu_slug = $menu_item[2];
        $item_customizations = isset($customizations['items'][$menu_slug]) ? $customizations['items'][$menu_slug] : array();
        
        // Apply custom label
        if (isset($item_customizations['label']) && !empty($item_customizations['label'])) {
            $menu[$key][0] = $item_customizations['label'];
        }
        
        // Apply visibility (hide items)
        if (isset($item_customizations['visible']) && !$item_customizations['visible']) {
            unset($menu[$key]);
            continue; // Skip role check if already hidden
        }
        
        // Apply role-based visibility
        if (isset($item_customizations['roles']) && !empty($item_customizations['roles'])) {
            $allowed_roles = $item_customizations['roles'];
            $user_has_access = false;
            
            foreach ($user_roles as $user_role) {
                if (in_array($user_role, $allowed_roles)) {
                    $user_has_access = true;
                    break;
                }
            }
            
            if (!$user_has_access) {
                unset($menu[$key]);
                continue; // Skip submenu processing if parent is hidden
            }
        }
        
        // Hide submenu items if parent is hidden for current user role
        if (isset($submenu[$menu_slug])) {
            foreach ($submenu[$menu_slug] as $submenu_key => $submenu_item) {
                if (empty($submenu_item[2])) {
                    continue;
                }
                
                $submenu_slug = $submenu_item[2];
                $submenu_customizations = isset($customizations['submenu'][$menu_slug][$submenu_slug]) ? $customizations['submenu'][$menu_slug][$submenu_slug] : array();
                
                // Apply custom label
                if (isset($submenu_customizations['label']) && !empty($submenu_customizations['label'])) {
                    $submenu[$menu_slug][$submenu_key][0] = $submenu_customizations['label'];
                }
                
                // Apply visibility (hide items)
                if (isset($submenu_customizations['visible']) && !$submenu_customizations['visible']) {
                    unset($submenu[$menu_slug][$submenu_key]);
                    continue;
                }
                
                // Apply role-based visibility for submenu items
                if (isset($submenu_customizations['roles']) && !empty($submenu_customizations['roles'])) {
                    $allowed_roles = $submenu_customizations['roles'];
                    $user_has_access = false;
                    
                    foreach ($user_roles as $user_role) {
                        if (in_array($user_role, $allowed_roles)) {
                            $user_has_access = true;
                            break;
                        }
                    }
                    
                    if (!$user_has_access) {
                        unset($submenu[$menu_slug][$submenu_key]);
                    }
                }
            }
        }
    }
}
add_action('admin_menu', 'wmo_apply_admin_menu_customizations', 100);

// Apply custom colors and icons to admin menu via CSS
function wmo_apply_admin_menu_styles()
{
    $customizations = wmo_get_settings('admin_customizations');
    
    if (empty($customizations['items'])) {
        return;
    }
    
    echo '<style type="text/css">';
    
    foreach ($customizations['items'] as $menu_slug => $item_customizations) {
        // Apply custom colors - DISABLED to prevent conflict with main color picker
        // if (isset($item_customizations['color']) && !empty($item_customizations['color'])) {
        //     $color = $item_customizations['color'];
        //     echo "#toplevel_page_{$menu_slug} > a { color: {$color} !important; }";
        //     echo "#toplevel_page_{$menu_slug} > a .wp-menu-image:before { color: {$color} !important; }";
        // }
        
        // Apply custom icons
        if (isset($item_customizations['icon']) && !empty($item_customizations['icon'])) {
            $icon = $item_customizations['icon'];
            // Map common icon names to Dashicon codes
            $icon_codes = array(
                'admin-appearance' => 'f100',
                'admin-collapse' => 'f148',
                'admin-comments' => 'f101',
                'admin-generic' => 'f110',
                'admin-home' => 'f102',
                'admin-links' => 'f103',
                'admin-media' => 'f104',
                'admin-network' => 'f112',
                'admin-page' => 'f105',
                'admin-plugins' => 'f106',
                'admin-post' => 'f109',
                'admin-settings' => 'f108',
                'admin-site' => 'f319',
                'admin-tools' => 'f107',
                'admin-users' => 'f110',
                'plus' => 'f502',
                'edit' => 'f464',
                'trash' => 'f2ed',
                'visibility' => 'f177',
                'yes' => 'f147',
                'no' => 'f158',
                'arrow-up' => 'f142',
                'arrow-down' => 'f140',
                'arrow-left' => 'f139',
                'arrow-right' => 'f141',
                'star-filled' => 'f155',
                'star-empty' => 'f154',
                'star-half' => 'f459',
                'heart' => 'f487',
                'info' => 'f348',
                'warning' => 'f534',
                'flag' => 'f227',
                'search' => 'f179',
                'filter' => 'f536',
                'update' => 'f463',
                'upload' => 'f317',
                'download' => 'f316',
                'lock' => 'f160',
                'unlock' => 'f528',
                'calendar' => 'f469',
                'clock' => 'f469',
                'location' => 'f230',
                'menu' => 'f333',
                'menu-alt' => 'f329',
                'menu-alt2' => 'f226',
                'menu-alt3' => 'f227',
                'menu-alt4' => 'f228',
                'menu-alt5' => 'f229',
                'menu-alt6' => 'f230',
                'menu-alt7' => 'f231',
                'menu-alt8' => 'f232',
                'menu-alt9' => 'f233',
                'menu-alt10' => 'f234',
                'menu-alt11' => 'f235',
                'menu-alt12' => 'f236',
                'menu-alt13' => 'f237',
                'menu-alt14' => 'f238',
                'menu-alt15' => 'f239',
                'menu-alt16' => 'f240',
                'menu-alt17' => 'f241',
                'menu-alt18' => 'f242',
                'menu-alt19' => 'f243',
                'menu-alt20' => 'f244',
                'menu-alt21' => 'f245',
                'menu-alt22' => 'f246',
                'menu-alt23' => 'f247',
                'menu-alt24' => 'f248',
                'menu-alt25' => 'f249',
                'menu-alt26' => 'f250',
                'menu-alt27' => 'f251',
                'menu-alt28' => 'f252',
                'menu-alt29' => 'f253',
                'menu-alt30' => 'f254',
                'menu-alt31' => 'f255',
                'menu-alt32' => 'f256',
                'menu-alt33' => 'f257',
                'menu-alt34' => 'f258',
                'menu-alt35' => 'f259',
                'menu-alt36' => 'f260',
                'menu-alt37' => 'f261',
                'menu-alt38' => 'f262',
                'menu-alt39' => 'f263',
                'menu-alt40' => 'f264',
                'menu-alt41' => 'f265',
                'menu-alt42' => 'f266',
                'menu-alt43' => 'f267',
                'menu-alt44' => 'f268',
                'menu-alt45' => 'f269',
                'menu-alt46' => 'f270',
                'menu-alt47' => 'f271',
                'menu-alt48' => 'f272',
                'menu-alt49' => 'f273',
                'menu-alt50' => 'f274',
                'menu-alt51' => 'f275',
                'menu-alt52' => 'f276',
                'menu-alt53' => 'f277',
                'menu-alt54' => 'f278',
                'menu-alt55' => 'f279',
                'menu-alt56' => 'f280',
                'menu-alt57' => 'f281',
                'menu-alt58' => 'f282',
                'menu-alt59' => 'f283',
                'menu-alt60' => 'f284',
                'menu-alt61' => 'f285',
                'menu-alt62' => 'f286',
                'menu-alt63' => 'f287',
                'admin-menu' => 'f333'
            );
            
            $icon_code = isset($icon_codes[$icon]) ? $icon_codes[$icon] : 'f333'; // Default to admin-menu
            echo "#toplevel_page_{$menu_slug} > a .wp-menu-image:before { content: '\\f{$icon_code}' !important; }";
        }
    }
    
    echo '</style>';
}
add_action('admin_head', 'wmo_apply_admin_menu_styles');

// Get inline styles
function wmo_get_inline_styles()
{
    return '
        /* Modern Card Design - Complete UI Overhaul */
        .menu-items-list {
            margin: 30px 0;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }
        
        #wmo-sortable-menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        #wmo-sortable-menu li {
            margin-bottom: 16px;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
            min-height: 60px;
            display: flex;
            align-items: center;
        }
        
        #wmo-sortable-menu li:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }
        
        #wmo-sortable-menu li.dragging {
            background: #e3f2fd;
            border-color: #2196f3;
            box-shadow: 0 8px 24px rgba(33, 150, 243, 0.3);
            transform: rotate(2deg) scale(1.02);
            z-index: 1000;
        }
        
        #wmo-sortable-menu .menu-item-handle {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            cursor: grab;
            gap: 16px;
        }
        
        #wmo-sortable-menu .menu-item-handle:hover {
            background: transparent;
        }
        
        #wmo-sortable-menu .menu-item-handle:active {
            cursor: grabbing;
        }
        
        /* Hierarchical Structure Styles */
        .menu-item-indented {
            margin-left: 20px;
            border-left: 3px solid #e9ecef;
            padding-left: 16px;
        }
        
        .submenu {
            list-style-type: none;
            padding: 0;
            margin: 8px 0 0 0;
            border-left: 2px solid #f0f0f0;
            margin-left: 20px;
        }
        
        .submenu li {
            margin-bottom: 8px;
            background: #fafbfc;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        
        .submenu li:hover {
            background: #f5f7fa;
            border-color: #d1d5db;
        }
        
        .submenu .menu-item-handle {
            padding: 16px 20px;
            font-size: 0.95em;
        }
        
        /* Inline Controls Styling */
        .item-inline-controls {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        
        .item-inline-controls button {
            padding: 4px 8px;
            font-size: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .item-inline-controls button:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        /* Edit Label Button */
        .edit-label {
            padding: 6px !important;
            border: 1px solid #d1d5db !important;
            background: #ffffff !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 32px !important;
            height: 32px !important;
            color: #0073aa !important;
        }
        
        .edit-label:hover {
            background: #f3f4f6 !important;
            border-color: #0073aa !important;
            color: #005a87 !important;
        }
        
        .edit-label:focus {
            outline: 2px solid #0073aa !important;
            outline-offset: 2px !important;
        }
        
        /* Visibility Toggle */
        .toggle-label {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 12px !important;
            color: #374151 !important;
            cursor: pointer !important;
        }
        
        .visibility-toggle {
            margin: 0 !important;
            cursor: pointer !important;
        }
        
        /* Inline Edit Input */
        .inline-edit-input {
            width: 100% !important;
            padding: 8px 12px !important;
            border: 2px solid #0073aa !important;
            border-radius: 4px !important;
            font-size: inherit !important;
            font-family: inherit !important;
            background: #ffffff !important;
            color: #374151 !important;
        }
        
        .inline-edit-input:focus {
            outline: none !important;
            border-color: #005a87 !important;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1) !important;
        }
        
        /* Loading States */
        .edit-loading {
            opacity: 0.7 !important;
            pointer-events: none !important;
        }
        
        .edit-spinner {
            margin-left: 8px !important;
            color: #0073aa !important;
            animation: spin 1s linear infinite !important;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Feedback Messages */
        .edit-success, .edit-error {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            padding: 8px 12px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            z-index: 1000 !important;
            opacity: 0 !important;
            transform: translateY(-10px) !important;
            transition: all 0.3s ease !important;
        }
        
        .edit-success {
            background: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0 !important;
        }
        
        .edit-error {
            background: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fecaca !important;
        }
        
        .edit-success.show, .edit-error.show {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        /* Color Swatch */
        .color-swatch {
            width: 24px !important;
            height: 24px !important;
            border-radius: 50% !important;
            border: 2px solid #d1d5db !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-block !important;
            position: relative !important;
            background-color: transparent !important;
        }
        
        .color-swatch:hover {
            border-color: #0073aa !important;
            transform: scale(1.1) !important;
        }
        
        .color-swatch:focus {
            outline: 2px solid #0073aa !important;
            outline-offset: 2px !important;
        }
        
        /* Icon Selector */
        .icon-selector {
            width: 32px !important;
            height: 32px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            color: #0073aa !important;
            font-size: 16px !important;
        }
        
        .icon-selector:hover {
            background: #f3f4f6 !important;
            border-color: #0073aa !important;
            color: #005a87 !important;
        }
        
        .icon-selector:focus {
            outline: 2px solid #0073aa !important;
            outline-offset: 2px !important;
        }
        
        /* Icon Picker Popup */
        .wmo-icon-picker-popup {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
            padding: 20px !important;
            z-index: 100000 !important;
            max-width: 90vw !important;
            max-height: 80vh !important;
            overflow: auto !important;
        }
        
        .wmo-icon-search {
            width: 100% !important;
            padding: 8px 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            margin-bottom: 16px !important;
            font-size: 14px !important;
        }
        
        .wmo-icon-search:focus {
            outline: none !important;
            border-color: #0073aa !important;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1) !important;
        }
        
        .wmo-icon-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)) !important;
            gap: 8px !important;
            max-height: 400px !important;
            overflow-y: auto !important;
        }
        
        .wmo-icon-choice {
            width: 40px !important;
            height: 40px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            color: #374151 !important;
            font-size: 18px !important;
            transition: all 0.2s ease !important;
        }
        
        .wmo-icon-choice:hover {
            background: #f3f4f6 !important;
            border-color: #0073aa !important;
            color: #0073aa !important;
        }
        
        .wmo-icon-choice.selected {
            background: #0073aa !important;
            border-color: #0073aa !important;
            color: #ffffff !important;
        }
        
        .wmo-icon-close {
            position: absolute !important;
            top: 8px !important;
            right: 8px !important;
            width: 32px !important;
            height: 32px !important;
            border: none !important;
            background: transparent !important;
            cursor: pointer !important;
            font-size: 20px !important;
            color: #6b7280 !important;
            border-radius: 4px !important;
        }
        
        .wmo-icon-close:hover {
            background: #f3f4f6 !important;
            color: #374151 !important;
        }
        
        /* Color Picker Integration */
        .wmo-color-picker {
            position: absolute !important;
            left: -9999px !important;
            visibility: hidden !important;
        }
        
        /* Role Selector */
        .role-selector-container {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 0 8px !important;
        }
        
        .role-label {
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #6b7280 !important;
            white-space: nowrap !important;
        }
        
        .role-selector {
            min-width: 120px !important;
            max-width: 200px !important;
            height: 28px !important;
            padding: 4px 8px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            background: #ffffff !important;
            color: #374151 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
        
        .role-selector:hover {
            border-color: #0073aa !important;
        }
        
        .role-selector:focus {
            outline: none !important;
            border-color: #0073aa !important;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1) !important;
        }
        
        .role-selector option {
            padding: 4px 8px !important;
            font-size: 12px !important;
        }
        
        .role-selector option:checked {
            background: #0073aa !important;
            color: #ffffff !important;
        }
        
        .role-selector option:hover {
            background: #f3f4f6 !important;
        }
        
        /* Toggle Submenu Arrow */
        .toggle-submenu {
            width: 24px !important;
            height: 24px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            color: #0073aa !important;
            font-size: 14px !important;
            margin-right: 8px !important;
        }
        
        .toggle-submenu:hover {
            background: #f3f4f6 !important;
            border-color: #0073aa !important;
            color: #005a87 !important;
        }
        
        .toggle-submenu:focus {
            outline: 2px solid #0073aa !important;
            outline-offset: 2px !important;
        }
        
        /* Collapsible Submenu */
        .submenu {
            margin-left: 20px !important;
            border-left: 2px solid #e5e7eb !important;
            padding-left: 16px !important;
            transition: all 0.3s ease !important;
        }
        
        .submenu .menu-item {
            margin-bottom: 12px !important;
        }
        
        .submenu .menu-item:last-child {
            margin-bottom: 0 !important;
        }
        
        /* Collapse All Controls */
        .wmo-collapse-controls {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        
        .wmo-collapse-controls .button {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        
        .wmo-collapse-controls .dashicons {
            font-size: 16px !important;
        }
        
        /* Search/Filter Controls */
        .wmo-search-controls {
            position: relative !important;
        }
        
        .wmo-search-controls input {
            transition: all 0.2s ease !important;
        }
        
        .wmo-search-controls input:focus {
            outline: none !important;
            border-color: #0073aa !important;
            box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.1) !important;
        }
        
        .wmo-search-controls .dashicons-search {
            pointer-events: none !important;
        }
        
        /* Search Highlight */
        .search-highlight {
            background-color: #fef3c7 !important;
            font-weight: bold !important;
            padding: 1px 2px !important;
            border-radius: 2px !important;
        }
        
        /* Search Status */
        #wmo-search-status {
            font-style: italic !important;
        }
        
        /* Edit Label Button */
        .edit-label {
            padding: 6px !important;
            border: 1px solid #d1d5db !important;
            background: #ffffff !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 32px !important;
            height: 32px !important;
            color: #0073aa !important;
        }
        
        .edit-label:hover {
            background: #f3f4f6 !important;
            border-color: #0073aa !important;
            color: #005a87 !important;
        }
        
        .edit-label:focus {
            outline: 2px solid #0073aa !important;
            outline-offset: 2px !important;
        }
        
        .edit-label .dashicons {
            font-size: 14px !important;
            width: 14px !important;
            height: 14px !important;
        }
        
        /* Toggle Label */
        .toggle-label {
            margin-left: 10px !important;
            display: flex !important;
            align-items: center !important;
            cursor: pointer !important;
            font-size: 13px !important;
            color: #6b7280 !important;
            user-select: none !important;
        }
        
        .toggle-label:hover {
            color: #374151 !important;
        }
        
        .toggle-label input[type="checkbox"] {
            margin-right: 6px !important;
            cursor: pointer !important;
        }
        
        /* Visibility Toggle */
        .visibility-toggle {
            display: flex !important;
            align-items: center !important;
            cursor: pointer !important;
            padding: 6px !important;
            border: 1px solid #d1d5db !important;
            background: #ffffff !important;
            border-radius: 4px !important;
            transition: all 0.2s ease !important;
            min-width: 32px !important;
            height: 32px !important;
            position: relative !important;
        }
        
        .visibility-toggle:hover {
            background: #f3f4f6 !important;
            border-color: #10b981 !important;
        }
        
        .visibility-toggle:focus-within {
            outline: 2px solid #10b981 !important;
            outline-offset: 2px !important;
        }
        
        .visibility-checkbox {
            position: absolute !important;
            opacity: 0 !important;
            width: 1px !important;
            height: 1px !important;
            margin: -1px !important;
            padding: 0 !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .visibility-icon {
            font-size: 14px !important;
            width: 14px !important;
            height: 14px !important;
            color: #6b7280 !important;
            transition: color 0.2s ease !important;
        }
        
        .visibility-checkbox:checked + .visibility-icon {
            color: #10b981 !important;
        }
        
        .visibility-checkbox:not(:checked) + .visibility-icon {
            color: #9ca3af !important;
        }
        
        /* Inline Edit Input */
        .inline-edit-input {
            border: 2px solid #3b82f6 !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            background: #ffffff !important;
            color: #374151 !important;
            min-width: 120px !important;
            max-width: 200px !important;
        }
        
        .inline-edit-input:focus {
            outline: none !important;
            border-color: #1d4ed8 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        /* Success Feedback */
        .edit-success {
            position: absolute !important;
            top: -30px !important;
            right: 0 !important;
            background: #10b981 !important;
            color: #ffffff !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
            z-index: 1000 !important;
        }
        
        .edit-success.show {
            opacity: 1 !important;
        }
        
        /* Error Feedback */
        .edit-error {
            position: absolute !important;
            top: -30px !important;
            right: 0 !important;
            background: #ef4444 !important;
            color: #ffffff !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
            z-index: 1000 !important;
        }
        
        .edit-error.show {
            opacity: 1 !important;
        }
        
        /* Loading State */
        .edit-loading {
            position: relative !important;
        }
        
        .edit-loading::after {
            content: "" !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            width: 12px !important;
            height: 12px !important;
            margin: -6px 0 0 -6px !important;
            border: 2px solid #f3f4f6 !important;
            border-top: 2px solid #3b82f6 !important;
            border-radius: 50% !important;
            animation: spin 1s linear infinite !important;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Disabled State */
        .edit-label:disabled,
        .visibility-toggle:has(.visibility-checkbox:disabled) {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .menu-items-list {
                padding: 16px;
                margin: 20px 0;
            }
            
            #wmo-sortable-menu .menu-item-handle {
                padding: 16px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .item-inline-controls {
                margin-left: 0;
                margin-top: 8px;
                width: 100%;
                justify-content: flex-start;
                gap: 12px;
            }
            
            .edit-label,
            .visibility-toggle {
                min-width: 36px;
                height: 36px;
            }
            
            .edit-label .dashicons,
            .visibility-icon {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }
            
            .toggle-label {
                margin-left: 8px;
                font-size: 14px;
            }
            
            .toggle-label input[type="checkbox"] {
                margin-right: 8px;
                transform: scale(1.2);
            }
            
            .submenu {
                margin-left: 16px;
            }
        }
        
        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .edit-label,
            .visibility-toggle {
                border: 2px solid #000000 !important;
            }
            
            .edit-label:hover,
            .visibility-toggle:hover {
                background: #000000 !important;
                color: #ffffff !important;
            }
        }
        
        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            .edit-label,
            .visibility-toggle,
            .inline-edit-input,
            .edit-success,
            .edit-error {
                transition: none !important;
            }
            
            .edit-loading::after {
                animation: none !important;
            }
        }
        
        /* Performance Optimizations */
        .menu-item {
            will-change: transform;
            backface-visibility: hidden;
        }
        
        .menu-item.dragging {
            transition: none !important;
            transform: translate3d(0,0,0);
        }
        
        .sortable-placeholder {
            background: #e3f2fd;
            border: 2px dashed #2196f3;
            border-radius: 8px;
            height: 60px;
            margin-bottom: 16px;
        }
        
        /* Status Messages */
        .wmo-status-message {
            margin: 16px 0;
            padding: 12px 16px;
            border-radius: 6px;
            border-left: 4px solid;
        }
        
        .wmo-status-message.notice-success {
            background: #f0f9ff;
            border-left-color: #0ea5e9;
            color: #0c4a6e;
        }
        
        .wmo-status-message.notice-error {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #7f1d1d;
        }
        
        /* Hardware Acceleration for Smooth Dragging */
        .ui-sortable-helper {
            transform: translate3d(0,0,0) !important;
            backface-visibility: hidden !important;
            perspective: 1000px !important;
        }
        
        /* Submenu Visual Indicators */
        .menu-item:has(.submenu) {
            border-left: 4px solid #3b82f6;
        }
        
        .menu-item:has(.submenu) .menu-item-handle::after {
            content: "ðŸ“";
            margin-left: 8px;
            font-size: 14px;
            opacity: 0.7;
        }
        
        /* Drag Handle Improvements */
        .menu-item-handle::before {
            content: "â‹®â‹®";
            margin-right: 12px;
            color: #9ca3af;
            font-size: 16px;
            cursor: grab;
        }
        
        .menu-item-handle:active::before {
            cursor: grabbing;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .menu-items-list {
                padding: 16px;
                margin: 20px 0;
            }
            
            #wmo-sortable-menu .menu-item-handle {
                padding: 16px 20px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .item-inline-controls {
                margin-left: 0;
                margin-top: 8px;
                width: 100%;
                justify-content: flex-start;
            }
            
            .submenu {
                margin-left: 16px;
            }
        }
        
        /* Accessibility Improvements */
        .menu-item-handle:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Loading States */
        #wmo-save-order:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Large Menu Performance Warning */
        .wmo-performance-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 16px 0;
            color: #92400e;
        }
        
        .wmo-performance-warning strong {
            color: #78350f;
        }
        
        /* Debug Styles (for testing) */
        .wmo-debug-drag {
            border: 2px dashed #ff0000 !important;
        }
        
        .wmo-debug-outline {
            outline: 2px solid #00ff00 !important;
        }
        
        /* Enhanced Visual Feedback */
        .menu-item.ui-sortable-helper {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            transform: rotate(2deg) scale(1.02);
        }
        
        .menu-item.ui-sortable-placeholder {
            opacity: 0.5;
            background: #e3f2fd;
            border: 2px dashed #2196f3;
        }
        
        /* Submenu Drop Zone Highlighting */
        .submenu.ui-sortable-helper {
            background: rgba(33, 150, 243, 0.1);
            border: 2px dashed #2196f3;
        }
        
        /* Item Title Styling */
        .item-title {
            font-weight: 500;
            color: #374151;
            flex: 1;
            }
            
            .item-controls {
            color: #6b7280;
            font-size: 0.875em;
            font-style: italic;
        }
        
        /* Smooth Transitions */
        .menu-item,
        .menu-item-handle,
        .item-inline-controls button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Focus States for Keyboard Navigation */
        .menu-item-handle:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
            background: #f0f9ff;
        }
        
        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            .menu-item {
                border: 2px solid #000000;
            }
            
            .submenu {
                border-left: 2px solid #000000;
            }
            
            .sortable-placeholder {
                border: 2px dashed #000000;
                background: #ffffff;
            }
        }
        
        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .menu-item,
            .menu-item-handle,
            .item-inline-controls button {
                transition: none;
            }
            
            .menu-item:hover {
                transform: none;
            }
            
            .menu-item.dragging {
                transform: none;
            }
        }
    ';
}

// Temporary icon reset functionality
add_action('admin_init', function() {
    if (isset($_GET['reset_wmo_icons']) && current_user_can('manage_options')) {
        // Clear the saved icons
        delete_option('wmo_menu_icons');
        
        // Also clear any other related icon options that might exist
        delete_option('wmo_saved_icons');
        
        // Add a success message
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>WMO Icons Reset:</strong> All saved menu icons have been cleared successfully.</p>';
            echo '</div>';
        });
        
        // Redirect back to admin to avoid the parameter staying in URL
        wp_redirect(admin_url());
        exit;
    }
});

// Version control and cleanup system
function wmo_version_check() {
    $current_version = get_option('wmo_version', '0');
    $plugin_version = '2.0.0'; // New optimized version
    
    if (version_compare($current_version, $plugin_version, '<')) {
        // Run upgrade routines
        wmo_cleanup_old_data();
        wmo_migrate_options(); // Ensure migration runs
        update_option('wmo_version', $plugin_version);
        
        // Log the upgrade
        // Removed debug logging for production
    }
}
add_action('admin_init', 'wmo_version_check');

// Cleanup old data and options
function wmo_cleanup_old_data() {
    // Clean up any old options that might still exist
    $old_options = array(
        'wmo_menu_colors',
        'wmo_menu_badges', 
        'wmo_menu_typography',
        'wmo_saved_icons',
        'wmo_custom_css',
        'wmo_menu_order',
        'wmo_admin_customizations',
        'wmo_theme_preference',
        'wmo_templates',
        'wmo_dark_mode'
    );
    
    foreach ($old_options as $option) {
        delete_option($option);
    }
    
    // Clear any old transients
    delete_transient('wmo_debug_data');
    delete_transient('wmo_menu_cache');
    
    // Clear any old error logs
    $upload_dir = wp_upload_dir();
    $debug_file = $upload_dir['basedir'] . '/wmo-debug.log';
    if (file_exists($debug_file)) {
        unlink($debug_file);
    }
}

// Plugin health check function
function wmo_health_check() {
    $issues = array();
    $warnings = array();
    
    // Check if settings exist
    if (!get_option('wmo_settings')) {
        $issues[] = 'Settings not initialized';
    }
    
    // Check if migration completed
    if (!get_option('wmo_migrated_v2')) {
        $issues[] = 'Database migration not completed';
    }
    
    // Check file sizes and existence
    $files = array(
        'assets/css/admin.css' => 50000, // 50KB limit
        'assets/js/admin.js' => 50000,   // 50KB limit
        'assets/js/color-picker.js' => 20000 // 20KB limit
    );
    
    foreach ($files as $file => $size_limit) {
        $path = plugin_dir_path(__FILE__) . $file;
        if (!file_exists($path)) {
            $issues[] = $file . ' not found';
        } elseif (filesize($path) > $size_limit) {
            $warnings[] = $file . ' is large (' . round(filesize($path)/1024) . 'KB)';
        }
    }
    
    // Check for minified files
    $minified_files = array(
        'assets/css/admin.min.css',
        'assets/js/admin.min.js',
        'assets/js/color-picker.min.js'
    );
    
    foreach ($minified_files as $file) {
        $path = plugin_dir_path(__FILE__) . $file;
        if (!file_exists($path)) {
            $warnings[] = 'Minified file not found: ' . $file;
        }
    }
    
    // Check for error_log statements (should be removed)
    $php_files = array(
        'wp-menu-organize.php',
        'includes/ajax-handlers.php',
        'includes/admin-page.php',
        'includes/helper-functions.php'
    );
    
    foreach ($php_files as $file) {
        $path = plugin_dir_path(__FILE__) . $file;
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (strpos($content, 'error_log(') !== false) {
                $warnings[] = 'Debug logging found in ' . $file;
            }
        }
    }
    
    return array(
        'issues' => $issues,
        'warnings' => $warnings,
        'status' => empty($issues) ? 'healthy' : 'needs_attention'
    );
}

// Display health status in admin
function wmo_display_health_status() {
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'wp-menu-organize') === false) {
        return;
    }
    
    $health = wmo_health_check();
    
    if (!empty($health['issues']) || !empty($health['warnings'])) {
        echo '<div class="notice notice-' . ($health['status'] === 'healthy' ? 'warning' : 'error') . ' is-dismissible">';
        echo '<h3>ðŸ” WMO Plugin Health Check</h3>';
        
        if (!empty($health['issues'])) {
            echo '<p><strong>Issues found:</strong></p>';
            echo '<ul>';
            foreach ($health['issues'] as $issue) {
                echo '<li>âŒ ' . esc_html($issue) . '</li>';
            }
            echo '</ul>';
        }
        
        if (!empty($health['warnings'])) {
            echo '<p><strong>Warnings:</strong></p>';
            echo '<ul>';
            foreach ($health['warnings'] as $warning) {
                echo '<li>âš ï¸ ' . esc_html($warning) . '</li>';
            }
            echo '</ul>';
        }
        
        echo '<p><em>These are optimization suggestions and won\'t affect functionality.</em></p>';
        echo '</div>';
    } else {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>âœ… <strong>WMO Plugin Health Check:</strong> All systems optimal! Plugin is running at peak performance.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'wmo_display_health_status');

// Remove the temporary minification function after use
function wmo_remove_minification_function() {
    // This function will be called after minification is complete
    // We'll keep it for now but can remove it later
}

// Database migration function to consolidate options
function wmo_migrate_options() {
    // Check if migration is needed
    if (get_option('wmo_migrated_v2')) {
        return;
    }
    
    // Gather all old options
    $settings = array(
        'colors' => get_option('wmo_menu_colors', array()),
        'badges' => get_option('wmo_menu_badges', array()),
        'typography' => get_option('wmo_menu_typography', array()),
        'icons' => get_option('wmo_menu_icons', array()),
        'saved_icons' => get_option('wmo_saved_icons', array()),
        'custom_css' => get_option('wmo_custom_css', ''),
        'menu_order' => get_option('wmo_menu_order', array()),
        'admin_customizations' => get_option('wmo_admin_customizations', array()),
        'theme_preference' => get_option('wmo_theme_preference', 'light'),
        'templates' => get_option('wmo_templates', array())
    );
    
    // Save as single option
    update_option('wmo_settings', $settings);
    
    // Clean up old options
    delete_option('wmo_menu_colors');
    delete_option('wmo_menu_badges');
    delete_option('wmo_menu_typography');
    delete_option('wmo_menu_icons');
    delete_option('wmo_saved_icons');
    delete_option('wmo_custom_css');
    delete_option('wmo_menu_order');
    delete_option('wmo_admin_customizations');
    delete_option('wmo_theme_preference');
    delete_option('wmo_templates');
    
    // Mark as migrated
    update_option('wmo_migrated_v2', true);
    
    // Log migration for debugging
    // Removed debug logging for production
}
add_action('admin_init', 'wmo_migrate_options');

// Helper function to get settings with fallback
function wmo_get_settings($key = null) {
    $settings = get_option('wmo_settings', array());
    
    if ($key === null) {
        return $settings;
    }
    
    return isset($settings[$key]) ? $settings[$key] : array();
}

// Helper function to update settings
function wmo_update_settings($key, $value) {
    $settings = wmo_get_settings();
    $settings[$key] = $value;
    update_option('wmo_settings', $settings);
}

// Cleanup function to remove old debug data
function wmo_cleanup_debug_data() {
    // Clear any transients
    delete_transient('wmo_debug_data');
    
    // Clear any old error logs specific to your plugin
    $upload_dir = wp_upload_dir();
    $debug_file = $upload_dir['basedir'] . '/wmo-debug.log';
    if (file_exists($debug_file)) {
        unlink($debug_file);
    }
}
add_action('admin_init', 'wmo_cleanup_debug_data');

