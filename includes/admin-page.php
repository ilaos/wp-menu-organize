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
        add_action('admin_footer', array($this, 'apply_menu_colors'));
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
            'WP Menu Organize Settings',
            'Menu Organize',
            'manage_options',
            'wp-menu-organize-settings',
            array($this, 'render_settings_page'),
            'dashicons-admin-generic'
        );
    }

    public function settings_init()
    {
        register_setting('wmo_settings_group', 'wmo_menu_colors', array($this, 'sanitize_menu_colors'));
    }

    public function render_settings_page()
    {
        global $menu;
        $menu_colors = get_option('wmo_menu_colors', array());
        include WMO_PLUGIN_PATH . 'templates/admin-settings-page.php';
    }

    public function handle_actions()
    {
        if (isset($_POST['wmo_reset_colors']) && check_admin_referer('wmo_reset_colors', 'wmo_reset_nonce')) {
            delete_option('wmo_menu_colors');
            add_settings_error('wmo_messages', 'wmo_message', 'All colors have been reset to default.', 'updated');
        }

        if (isset($_POST['wmo_export_colors']) && check_admin_referer('wmo_export_colors', 'wmo_export_nonce')) {
            $menu_colors = get_option('wmo_menu_colors', array());
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=wp-menu-organize-settings.json');
            echo json_encode($menu_colors);
            exit;
        }

        if (isset($_POST['wmo_import_colors']) && check_admin_referer('wmo_import_colors', 'wmo_import_nonce')) {
            if ($_FILES['wmo_import_file']['error'] == UPLOAD_ERR_OK) {
                $filename = $_FILES['wmo_import_file']['name'];
                $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (strtolower($file_ext) === 'json') {
                    $file_content = file_get_contents($_FILES['wmo_import_file']['tmp_name']);
                    $import_data = json_decode($file_content, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $current_menu = wmo_get_current_menu_slugs();
                        $unused_settings = array_diff_key($import_data, array_flip($current_menu));
                        $applied_settings = array_intersect_key($import_data, array_flip($current_menu));

                        update_option('wmo_menu_colors', $applied_settings);

                        $message = 'Color settings imported successfully.';
                        if (!empty($unused_settings)) {
                            $message .= ' Some settings were not applied because the corresponding menu items are not present:';
                            $message .= ' ' . implode(', ', array_keys($unused_settings));
                        }
                        add_settings_error('wmo_messages', 'wmo_message', $message, 'updated');
                    } else {
                        $json_error_message = 'Unknown error';
                        switch (json_last_error()) {
                            case JSON_ERROR_DEPTH:
                                $json_error_message = 'Maximum stack depth exceeded';
                                break;
                            case JSON_ERROR_STATE_MISMATCH:
                                $json_error_message = 'Underflow or the modes mismatch';
                                break;
                            case JSON_ERROR_CTRL_CHAR:
                                $json_error_message = 'Unexpected control character found';
                                break;
                            case JSON_ERROR_SYNTAX:
                                $json_error_message = 'Syntax error, malformed JSON';
                                break;
                            case JSON_ERROR_UTF8:
                                $json_error_message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                                break;
                        }
                        add_settings_error('wmo_messages', 'wmo_message', 'Invalid JSON format: ' . $json_error_message, 'error');
                    }
                } else {
                    add_settings_error('wmo_messages', 'wmo_message', 'Invalid file type. Only JSON files are allowed.', 'error');
                }
            } else {
                $error_message = 'Error uploading file.';
                switch ($_FILES['wmo_import_file']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_message = 'The uploaded file exceeds the allowed size.';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error_message = 'The uploaded file was only partially uploaded.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error_message = 'No file was uploaded.';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $error_message = 'Missing a temporary folder.';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $error_message = 'Failed to write file to disk.';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $error_message = 'File upload stopped by extension.';
                        break;
                }
                add_settings_error('wmo_messages', 'wmo_message', $error_message, 'error');
            }
        }

        settings_errors('wmo_messages');
    }

    public function apply_menu_colors()
    {
        $menu_colors = get_option('wmo_menu_colors', array());
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var menuColors = <?php echo json_encode($menu_colors); ?>;
                console.log('Menu Colors:', menuColors);

                function applyColor(slug, color, isSubmenu, isPreview = false) {
                    console.log('Applying color:', slug, color, isSubmenu, isPreview ? '(preview)' : '');
                    var menuItems = [];

                    if (slug === 'all-in-one-wp-migration') {
                        menuItems = [document.querySelector('#toplevel_page_ai1wm_export > a')];
                    } else if (isSubmenu) {
                        // Find submenu items within their parent menu
                        var parentMenus = document.querySelectorAll('#adminmenu > li.menu-top');
                        parentMenus.forEach(function(parentMenu) {
                            var submenuItems = parentMenu.querySelectorAll('.wp-submenu li a');
                            submenuItems.forEach(function(item) {
                                if (item.textContent.trim().toLowerCase() === slug.replace(/-/g, ' ').toLowerCase()) {
                                    menuItems.push(item);
                                }
                            });
                        });
                    } else {
                        menuItems = [document.querySelector('#menu-' + slug + ' > a, #toplevel_page_' + slug + ' > a')];
                        if (!menuItems[0]) {
                            menuItems = Array.from(document.querySelectorAll('#adminmenu > li > a')).filter(item =>
                                item.textContent.trim().toLowerCase() === slug.replace(/-/g, ' ').toLowerCase()
                            );
                        }
                    }

                    menuItems.forEach(function(menuItem) {
                        if (menuItem) {
                            console.log('Applying color to:', menuItem);
                            menuItem.style.color = color;
                        }
                    });

                    if (menuItems.length === 0) {
                        console.log('Menu item not found for slug:', slug);
                    }

                    // If it's a parent menu item and not a preview, update its submenu items
                    if (!isSubmenu && !isPreview) {
                        var parentItem = document.querySelector('#toplevel_page_ai1wm_export, #menu-' + slug);
                        if (parentItem) {
                            var submenuItems = parentItem.querySelectorAll('.wp-submenu li a');
                            submenuItems.forEach(function(submenuItem) {
                                var submenuSlug = submenuItem.textContent.trim().toLowerCase().replace(/\s+/g, '-');
                                if (menuColors[submenuSlug]) {
                                    submenuItem.style.color = menuColors[submenuSlug];
                                } else {
                                    submenuItem.style.color = '#f0f0f1'; // Default WordPress admin menu text color
                                }
                            });
                        }
                    }
                }

                // Initial application of stored colors
                for (var slug in menuColors) {
                    if (menuColors[slug]) {
                        var isSubmenu = slug.includes('-');
                        applyColor(slug, menuColors[slug], isSubmenu);
                    }
                }

                // Real-time color updates
                jQuery(document).ready(function($) {
                    $(".wmo-color-field").wpColorPicker({
                        change: function(event, ui) {
                            var slug = $(this).data("menu-slug");
                            var color = ui.color.toString();
                            var isSubmenu = $(this).data("is-submenu") === 'true';
                            console.log('Color picker changed:', slug, color, isSubmenu);
                            applyColor(slug, color, isSubmenu, true);

                            // Additional check for submenu items
                            if (isSubmenu) {
                                var parentMenus = document.querySelectorAll('#adminmenu > li.menu-top');
                                parentMenus.forEach(function(parentMenu) {
                                    var submenuItems = parentMenu.querySelectorAll('.wp-submenu li a');
                                    submenuItems.forEach(function(item) {
                                        if (item.textContent.trim().toLowerCase() === slug.replace(/-/g, ' ').toLowerCase()) {
                                            item.style.color = color;
                                        }
                                    });
                                });
                            }
                        }
                    });

                    // Handle form submission (unchanged)
                    $('form').on('submit', function(e) {
                        // ... (keep the existing form submission code)
                    });
                });
            });
        </script>
<?php
    }
    public function sanitize_menu_colors($input)
    {
        $sanitized_input = array();
        foreach ($input as $key => $value) {
            $sanitized_input[$key] = sanitize_hex_color($value);
        }
        return $sanitized_input;
    }

    public function enqueue_scripts($hook_suffix)
    {
        if ('toplevel_page_wp-menu-organize-settings' !== $hook_suffix) return;

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('wmo-admin-script', WMO_PLUGIN_URL . 'assets/js/admin.js', array('wp-color-picker'), false, true);

        wp_add_inline_style('wp-color-picker', '
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
        ');
    }
}

WP_Menu_Organize::get_instance();
