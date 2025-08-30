<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WP_Menu_Organize
{
    private static $instance = null;

    private function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_init', array($this, 'handle_actions'));
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_settings_page()
    {
        add_menu_page(
            'WP Menu Organize',
            'Menu Organize',
            'manage_options',
            'wp-menu-organize-settings',
            array($this, 'render_settings_page'),
            'dashicons-admin-generic'
        );

        // Add the main Customize Tabs submenu page (this replaces the default first submenu item)
        add_submenu_page(
            'wp-menu-organize-settings',
            'Customize Tabs',
            'Customize Tabs',
            'manage_options',
            'wp-menu-organize-settings',
            array($this, 'render_settings_page')
        );


        // Add Settings submenu page (first)
        add_submenu_page(
            'wp-menu-organize-settings',
            'Menu Organize Settings',
            'Settings',
            'manage_options',
            'wp-menu-organize-settings-page',
            array($this, 'render_settings_tab_page')
        );
        // Add Templates submenu page
        add_submenu_page(
            'wp-menu-organize-settings',
            'Menu Templates',
            'Templates',
            'manage_options',
            'wp-menu-organize-templates',
            array($this, 'render_templates_page')
        );
        add_submenu_page(
            'wp-menu-organize-settings',
            'Reorder Admin Menu',
            'Reorder Menu',
            'manage_options',
            'wp-menu-organize-reorder',
            array($this, 'render_reorder_page')
        );
    }

    public function settings_init()
    {
        register_setting('wmo_settings_group', 'wmo_admin_customizations', array($this, 'sanitize_admin_customizations'));
        register_setting('wmo_settings_group', 'wmo_menu_colors', array($this, 'sanitize_menu_colors'));
        register_setting('wmo_settings_group', 'wmo_menu_badges', array($this, 'sanitize_menu_badges'));
        register_setting('wmo_settings_group', 'wmo_menu_typography', array($this, 'sanitize_menu_typography'));
    }

    public function render_settings_page()
    {
        // Debug: Confirm template inclusion
        error_log('WMO Debug: render_settings_page() called - including admin-settings-page.php template');
        
        // Retrieve saved colors from database, or use empty array as default
        $menu_colors = wmo_get_settings('colors');
        
        // Ensure $menu_colors is always an array
        if (!is_array($menu_colors)) {
            $menu_colors = array();
        }
        
        global $menu, $submenu;
        $customizations = wmo_get_settings('admin_customizations');
        
        // Include the template - now $menu_colors will be available in the template scope
        include WMO_PLUGIN_PATH . 'templates/admin-settings-page.php';
        
        error_log('WMO Debug: admin-settings-page.php template included successfully');
    }

    public function handle_actions()
    {
        if (isset($_POST['wmo_reset_customizations']) && check_admin_referer('wmo_reset_customizations', 'wmo_reset_nonce')) {
            delete_option('wmo_admin_customizations');
            add_settings_error('wmo_messages', 'wmo_message', 'All admin menu customizations have been reset to default.', 'updated');
        }

        settings_errors('wmo_messages');
    }

    public function sanitize_admin_customizations($input)
    {
        $sanitized_input = array();
        
        if (isset($input['menu_order']) && is_array($input['menu_order'])) {
            $sanitized_input['menu_order'] = array_map('sanitize_text_field', $input['menu_order']);
        }
        
        if (isset($input['items']) && is_array($input['items'])) {
            $sanitized_input['items'] = array();
            foreach ($input['items'] as $menu_slug => $customizations) {
                $sanitized_input['items'][sanitize_text_field($menu_slug)] = array(
                    'visible' => isset($customizations['visible']) ? (bool) $customizations['visible'] : true,
                    'icon' => isset($customizations['icon']) ? sanitize_text_field($customizations['icon']) : '',
                    'color' => isset($customizations['color']) ? sanitize_hex_color($customizations['color']) : '',
                    'label' => isset($customizations['label']) ? sanitize_text_field($customizations['label']) : '',
                    'roles' => isset($customizations['roles']) && is_array($customizations['roles']) ? array_map('sanitize_text_field', $customizations['roles']) : array()
                );
            }
        }
        
        return $sanitized_input;
    }

    public function sanitize_menu_colors($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $slug => $color) {
            $sanitized[sanitize_key($slug)] = sanitize_hex_color($color);
        }

        return $sanitized;
    }
    
    public function sanitize_menu_badges($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $slug => $badge_data) {
            if (is_array($badge_data)) {
                $sanitized[sanitize_key($slug)] = array(
                    'text' => sanitize_text_field($badge_data['text'] ?? ''),
                    'color' => sanitize_hex_color($badge_data['color'] ?? '#ffffff'),
                    'background' => sanitize_hex_color($badge_data['background'] ?? '#0073aa'),
                    'enabled' => (bool)($badge_data['enabled'] ?? false)
                );
            }
        }

        return $sanitized;
    }
    
    public function sanitize_menu_typography($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        $allowed_font_families = [
            'Arial, sans-serif',
            'Helvetica, sans-serif',
            "'Times New Roman', serif",
            'Georgia, serif',
            "'Courier New', monospace",
            'Verdana, sans-serif',
            'Tahoma, sans-serif',
            "'Trebuchet MS', sans-serif",
            'Impact, sans-serif',
            "'Comic Sans MS', cursive"
        ];
        
        $allowed_font_sizes = ['10px', '12px', '13px', '14px', '16px', '18px', '20px'];
        $allowed_font_weights = ['300', '400', '500', '600', '700', '800'];
        
        foreach ($input as $slug => $typography_data) {
            if (is_array($typography_data)) {
                $font_family = $typography_data['font_family'] ?? '';
                $font_size = $typography_data['font_size'] ?? '';
                $font_weight = $typography_data['font_weight'] ?? '';
                
                $sanitized[sanitize_key($slug)] = array(
                    'enabled' => (bool)($typography_data['enabled'] ?? false),
                    'font_family' => in_array($font_family, $allowed_font_families) ? $font_family : '',
                    'font_size' => in_array($font_size, $allowed_font_sizes) ? $font_size : '',
                    'font_weight' => in_array($font_weight, $allowed_font_weights) ? $font_weight : ''
                );
            }
        }

        return $sanitized;
    }

    public function enqueue_scripts($hook_suffix) {
        // Existing debug logs...
        error_log('WMO Debug: Hook suffix = ' . $hook_suffix);
        
        if ($hook_suffix !== 'toplevel_page_wp-menu-organize-settings' && 
            $hook_suffix !== 'menu-organize_page_wp-menu-organize-reorder' &&
            $hook_suffix !== 'menu-organize_page_wp-menu-organize-templates' &&
            $hook_suffix !== 'menu-organize_page_wp-menu-organize-settings-page') {
            error_log('WMO Debug: Hook not matched, but checking for icon applier');
        } else {
            error_log('WMO Debug: Hook matched, loading scripts for toggle functionality');
            error_log('WMO Debug: Loading admin.js and admin.css for hook: ' . $hook_suffix);
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        
        if (strpos($hook_suffix, 'wp-menu-organize') !== false) {
            wp_enqueue_script('wmo-admin', wmo_get_asset_url('admin.js'), array('jquery', 'wp-color-picker', 'jquery-ui-sortable'), '1.0', true);
            wp_enqueue_script('wmo-icon-picker', wmo_get_asset_url('icon-picker.js'), array('jquery'), '1.0', true);
            wp_enqueue_style('wmo-admin', wmo_get_asset_url('admin.css'), array('wp-color-picker'), '1.0');
            
            wp_localize_script('wmo-admin', 'wmo_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wmo_ajax_nonce')
            ));
        }
        
        // Global icon applier
        $icons = wmo_get_settings('icons');
        if (!empty($icons)) {
            $decoded_icons = array();
            foreach ($icons as $menu_id => $icon_data) {
                if ($icon_data['type'] === 'emoji') {
                    $emoji_value = $icon_data['value'];
                    $emoji_value = mb_convert_encoding($emoji_value, 'UTF-8', 'UTF-8');
                    $decoded_value = json_decode('"' . $emoji_value . '"');
                    $icon_data['value'] = $decoded_value ? $decoded_value : $emoji_value;
                }
                $decoded_icons[$menu_id] = $icon_data;
            }
            wp_enqueue_script('wmo-icon-applier', plugin_dir_url(__FILE__) . '../assets/js/icon-applier.js', array('jquery'), '1.0', true);
            wp_localize_script('wmo-icon-applier', 'wmo_saved_icons', $decoded_icons);
        }
    }
    

    
    public function apply_theme_preference() {
        // Only apply on our plugin pages
        $screen = get_current_screen();
        if ($screen && ($screen->id === 'toplevel_page_wp-menu-organize-settings' || 
                       $screen->id === 'menu-organize_page_wp-menu-organize-reorder')) {
            
            $dark_mode = wmo_get_settings('theme_preference') === 'dark';
            
            if ($dark_mode) {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Apply dark theme immediately
                        document.body.classList.add("wmo-dark-theme");
                        
                        // Set the toggle state
                        var themeToggle = document.getElementById("wmo-dark-mode-toggle");
                        if (themeToggle) {
                            themeToggle.checked = true;
                        }
                        
                        console.log("WMO: Applied dark theme from server preference");
                    });
                </script>';
            }
        }
    }

    private function get_inline_styles()
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
        
        .item-title {
            font-weight: 600;
            color: #212529;
            font-size: 16px;
            line-height: 1.4;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .item-title::before {
            content: "\\f111";
            font-family: "dashicons";
            font-size: 18px;
            color: #6c757d;
            margin-right: 8px;
        }
        
        .item-controls {
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .item-controls::before {
            content: "\\f163";
            font-family: "dashicons";
            font-size: 14px;
            color: #adb5bd;
        }
        
        .sortable-placeholder {
            border: 2px dashed #2196f3;
            margin-bottom: 16px;
            height: 60px;
            background: #f0f8ff;
            border-radius: 8px;
            opacity: 0.7;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sortable-placeholder::after {
            content: "Drop here";
            color: #2196f3;
            font-weight: 500;
            font-size: 14px;
        }
        
        /* Save Button Styling */
        #wmo-save-order {
            margin-top: 24px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: #007cba;
            border-color: #007cba;
            color: #ffffff;
        }
        
        #wmo-save-order:hover {
            background: #005a87;
            border-color: #005a87;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 124, 186, 0.3);
        }
        
        #wmo-save-order:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Instructions Styling */
        .wmo-instructions {
            margin: 20px 0 24px 0;
            padding: 16px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e9ecef;
            border-radius: 8px;
            border-left: 4px solid #007cba;
        }
        
        .wmo-instructions p {
            margin: 0;
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 400;
        }
        
        .wmo-instructions strong {
            color: #212529;
            font-weight: 600;
        }
        
        /* Success Message Styling */
        #wmo-success-msg {
            margin-top: 16px;
            padding: 16px 20px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            background: #d4edda;
            color: #155724;
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
                gap: 12px;
            }
            
            .item-title {
                font-size: 14px;
            }
            
            .item-controls {
                font-size: 12px;
                align-self: flex-end;
            }
        }
        
        /* Updated layout styles without sidebar */
        .wmo-layout {
            display: block;
        }
        .wmo-main-content {
            width: 100%;
        }
        .wmo-color-groups {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .wmo-color-group {
            flex: 1 0 45%;
            margin-bottom: 20px;
        }
        .wmo-color-picker-wrapper {
            margin-bottom: 10px;
        }
    ';
    }

    public function render_reorder_page()
    {
        // Ensure we're in the admin context
        if (!is_admin()) {
            return;
        }
        
        include WMO_PLUGIN_PATH . 'templates/admin-reorder-page.php';
    }

    public function render_templates_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        include WMO_PLUGIN_PATH . 'templates/admin-templates-page.php';
    }

    public function render_settings_tab_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        include WMO_PLUGIN_PATH . 'templates/admin-settings-tab-page.php';
    }
}


// Initialize the plugin
WP_Menu_Organize::get_instance();

// Debug function to check admin hooks
function debug_admin_hooks($hook) {
    error_log('WMO DEBUG: Current admin hook: ' . $hook);
}
add_action('admin_enqueue_scripts', 'debug_admin_hooks');

// Debug function to check script registration
function debug_script_registration() {
    error_log('WMO DEBUG: Checking script registration...');
    error_log('WMO DEBUG: jquery-ui-sortable registered: ' . (wp_script_is('jquery-ui-sortable', 'registered') ? 'YES' : 'NO'));
    error_log('WMO DEBUG: jquery-ui-sortable enqueued: ' . (wp_script_is('jquery-ui-sortable', 'enqueued') ? 'YES' : 'NO'));
    error_log('WMO DEBUG: jquery registered: ' . (wp_script_is('jquery', 'registered') ? 'YES' : 'NO'));
    error_log('WMO DEBUG: jquery enqueued: ' . (wp_script_is('jquery', 'enqueued') ? 'YES' : 'NO'));
}
add_action('wp_enqueue_scripts', 'debug_script_registration');
add_action('admin_enqueue_scripts', 'debug_script_registration');

// Note: Icon application is now handled by wmo_apply_menu_icons() in wp-menu-organize.php
// This replaces the CSS-based approach with direct menu array modification
