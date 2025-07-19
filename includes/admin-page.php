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
    }

    public function render_settings_page()
    {
        global $menu, $submenu;
        $customizations = get_option('wmo_admin_customizations', array());
        include WMO_PLUGIN_PATH . 'templates/admin-settings-page.php';
    }

    public function handle_actions()
    {
        if (isset($_POST['wmo_reset_customizations']) && check_admin_referer('wmo_reset_customizations', 'wmo_reset_nonce')) {
            delete_option('wmo_admin_customizations');
            add_settings_error('wmo_messages', 'wmo_message', 'All admin menu customizations have been reset to default.', 'updated');
        }

        if (isset($_POST['wmo_export_customizations']) && check_admin_referer('wmo_export_customizations', 'wmo_export_nonce')) {
            $customizations = get_option('wmo_admin_customizations', array());
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=wp-menu-organize-settings.json');
            echo json_encode($customizations);
            exit;
        }

        if (isset($_POST['wmo_import_customizations']) && check_admin_referer('wmo_import_customizations', 'wmo_import_nonce')) {
            $this->handle_import_customizations();
        }

        settings_errors('wmo_messages');
    }

    private function handle_import_customizations()
    {
        if ($_FILES['wmo_import_file']['error'] == UPLOAD_ERR_OK) {
            $filename = $_FILES['wmo_import_file']['name'];
            $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

            if (strtolower($file_ext) === 'json') {
                $file_content = file_get_contents($_FILES['wmo_import_file']['tmp_name']);
                $import_data = json_decode($file_content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    update_option('wmo_admin_customizations', $import_data);
                    add_settings_error('wmo_messages', 'wmo_message', 'Admin menu customizations imported successfully.', 'updated');
                } else {
                    $json_error_message = $this->get_json_error_message();
                    add_settings_error('wmo_messages', 'wmo_message', 'Invalid JSON format: ' . $json_error_message, 'error');
                }
            } else {
                add_settings_error('wmo_messages', 'wmo_message', 'Invalid file type. Only JSON files are allowed.', 'error');
            }
        } else {
            $error_message = $this->get_file_upload_error_message();
            add_settings_error('wmo_messages', 'wmo_message', $error_message, 'error');
        }
    }

    private function get_json_error_message()
    {
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }

    private function get_file_upload_error_message()
    {
        switch ($_FILES['wmo_import_file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the allowed size.';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension.';
            default:
                return 'Error uploading file.';
        }
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

    public function enqueue_scripts($hook_suffix)
    {
        error_log('WMO: enqueue_scripts called for hook: ' . $hook_suffix);
        
        // Check for the correct hook names
        if ($hook_suffix !== 'toplevel_page_wp-menu-organize-settings' && 
            $hook_suffix !== 'menu-organize_page_wp-menu-organize-reorder') {
            error_log('WMO: Not loading scripts for hook: ' . $hook_suffix);
            return;
        }

        error_log('WMO: Loading scripts for hook: ' . $hook_suffix);

        // Dashicons (for admin menu icons)
        wp_enqueue_style('dashicons');
        error_log('WMO: Dashicons enqueued');
        
        // jQuery UI CSS
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css');
        error_log('WMO: jQuery UI CSS enqueued');
        
        // Try WordPress built-in jQuery UI sortable first
        wp_enqueue_script('jquery-ui-sortable');
        error_log('WMO: WordPress jQuery UI sortable enqueued');
        
        // Fallback: Load full jQuery UI from CDN as backup
        wp_enqueue_script(
            'jquery-ui-cdn-fallback',
            'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
            array('jquery'),
            '1.13.2',
            true
        );
        error_log('WMO: jQuery UI CDN fallback enqueued');
        
        // Enqueue WordPress color picker CSS
        wp_enqueue_style('wp-color-picker');
        error_log('WMO: WordPress color picker CSS enqueued');
        
        // Enqueue our custom CSS file
        wp_enqueue_style(
            'wmo-admin-styles',
            WMO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            '1.0.0',
            'all'
        );
        error_log('WMO: Plugin CSS enqueued');

        // Enqueue our custom script AFTER jQuery UI
        wp_enqueue_script(
            'wmo-admin-script',
            WMO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-cdn-fallback', 'wp-color-picker'), // Dependencies
            '1.0.8',
            true // Load in footer
        );
        error_log('WMO: Plugin script enqueued with proper dependencies');
        
        // Enqueue color picker script
        wp_enqueue_script(
            'wmo-color-picker-script',
            WMO_PLUGIN_URL . 'assets/js/color-picker.js',
            array('jquery', 'wp-color-picker', 'wmo-admin-script'), // Dependencies
            '1.0.0',
            true // Load in footer
        );
        error_log('WMO: Color picker script enqueued');

        wp_localize_script('wmo-admin-script', 'wmo_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wmo_ajax_nonce'),
            'menuColors' => get_option('wmo_menu_colors', array())
        ));
        error_log('WMO: Script localized with AJAX data');

        wp_add_inline_style('wp-color-picker', $this->get_inline_styles());
        error_log('WMO: Inline styles added');
        
        // Debug: Check what scripts are actually enqueued
        error_log('WMO: Final script check - jquery-ui-sortable enqueued: ' . (wp_script_is('jquery-ui-sortable', 'enqueued') ? 'YES' : 'NO'));
        error_log('WMO: Final script check - jquery-ui-cdn-fallback enqueued: ' . (wp_script_is('jquery-ui-cdn-fallback', 'enqueued') ? 'YES' : 'NO'));
        
        // Add manual jQuery UI injection as last resort
        add_action('admin_footer', array($this, 'inject_jquery_ui_manual'));
    }
    
    public function inject_jquery_ui_manual() {
        // Only inject on our specific pages
        $screen = get_current_screen();
        if ($screen && ($screen->id === 'toplevel_page_wp-menu-organize-settings' || 
                       $screen->id === 'menu-organize_page_wp-menu-organize-reorder' ||
                       $screen->id === 'menu-colors_page_wp-menu-organize-reorder')) {
            echo '<script>
                console.log("WMO: Checking jQuery UI availability...");
                if (typeof jQuery.fn.sortable === "undefined") {
                    console.log("WMO: jQuery UI sortable not available, injecting manually...");
                    var script = document.createElement("script");
                    script.src = "https://code.jquery.com/ui/1.13.2/jquery-ui.min.js";
                    script.onload = function() {
                        console.log("WMO: Manual jQuery UI injection successful");
                        if (typeof jQuery.fn.sortable !== "undefined") {
                            console.log("WMO: Sortable is now available");
                            // Re-initialize our script
                            if (typeof wmoInitializeSortable === "function") {
                                wmoInitializeSortable();
                            }
                        }
                    };
                    document.head.appendChild(script);
                } else {
                    console.log("WMO: jQuery UI sortable is already available");
                }
            </script>';
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
        
        /* Legacy styles for other pages */
        .wmo-layout {
            display: flex;
            flex-wrap: wrap;
            margin-right: -20px;
        }
        .wmo-main-content {
            flex: 1;
            min-width: 60%;
            margin-right: 20px;
        }
        .wmo-sidebar {
            width: 250px;
        }
        .wmo-color-groups {
            display: flex;
            flex-wrap: wrap;
            margin-right: -20px;
        }
        .wmo-color-group {
            flex: 1 0 45%;
            margin-right: 20px;
            margin-bottom: 20px;
        }
        .wmo-color-picker-wrapper {
            margin-bottom: 10px;
        }
        .wmo-widget {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            margin-bottom: 20px;
            padding: 10px;
        }
        #adminmenu .toplevel_page_wp-menu-organize-settings .wp-menu-name {
            color: yellow !important;
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
