<div class="wrap">
    <h1>WP Menu Organize Settings</h1>
    <div class="wmo-layout">
        <div class="wmo-main-content">
            <form method="post" action="options.php">
                <?php
                settings_fields('wmo_settings_group');
                do_settings_sections('wp-menu-organize-settings');
                ?>
                <div class="wmo-color-groups">
                    <div class="wmo-color-group">
                        <h3>Content Management</h3>
                        <?php
                        wmo_render_color_picker($menu_colors, 'dashboard', 'Dashboard');
                        wmo_render_color_picker($menu_colors, 'posts', 'Posts');
                        wmo_render_color_picker($menu_colors, 'media', 'Media');
                        wmo_render_color_picker($menu_colors, 'pages', 'Pages');
                        wmo_render_color_picker($menu_colors, 'comments', 'Comments');
                        ?>
                    </div>
                    <div class="wmo-color-group">
                        <h3>Appearance and Plugins</h3>
                        <?php
                        wmo_render_color_picker($menu_colors, 'appearance', 'Appearance');
                        wmo_render_color_picker($menu_colors, 'plugins', 'Plugins');
                        ?>
                    </div>
                    <div class="wmo-color-group">
                        <h3>System</h3>
                        <?php
                        wmo_render_color_picker($menu_colors, 'users', 'Users');
                        wmo_render_color_picker($menu_colors, 'tools', 'Tools');
                        wmo_render_color_picker($menu_colors, 'settings', 'Settings');
                        ?>
                    </div>
                    <div class="wmo-color-group">
                        <h3>Other</h3>
                        <?php
                        global $submenu;
                        foreach ($menu as $item) {
                            $menu_slug = sanitize_title(strip_tags($item[0]));
                            if (empty($menu_slug) || in_array($menu_slug, ['dashboard', 'posts', 'media', 'pages', 'comments', 'appearance', 'plugins', 'users', 'tools', 'settings'])) continue;

                            if (strpos($menu_slug, 'comments') !== false) {
                                $menu_title = 'Comments';
                            } else {
                                $menu_title = preg_replace('/\d+/', '', strip_tags($item[0]));
                                $menu_title = trim($menu_title);
                            }

                            wmo_render_color_picker($menu_colors, $menu_slug, $menu_title);

                            // Handle submenus
                            if (isset($submenu[$item[2]])) {
                                foreach ($submenu[$item[2]] as $subitem) {
                                    $submenu_slug = sanitize_title(strip_tags($subitem[0]));
                                    $submenu_title = strip_tags($subitem[0]);

                                    // Display submenu with indentation
                                    wmo_render_color_picker($menu_colors, $submenu_slug, '— ' . $submenu_title, true);
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
        <div class="wmo-sidebar">
            <div class="wmo-widget">
                <h3>Additional Actions</h3>
                <form method="post" action="">
                    <?php wp_nonce_field('wmo_reset_colors', 'wmo_reset_nonce'); ?>
                    <input type="submit" name="wmo_reset_colors" class="button button-secondary" value="Reset to Default Colors" onclick="return confirm('Are you sure you want to reset all colors to default?');" />
                </form>

            </div>
            <div class="wmo-widget">
                <h3>Export Settings</h3>
                <form method="post" action="">
                    <?php wp_nonce_field('wmo_export_colors', 'wmo_export_nonce'); ?>
                    <input type="submit" name="wmo_export_colors" class="button button-secondary" value="Export Color Settings" />
                </form>
            </div>
            <div class="wmo-widget">
                <h3>Import Settings</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field('wmo_import_colors', 'wmo_import_nonce'); ?>
                    <input type="file" name="wmo_import_file" accept=".json" />
                    <input type="submit" name="wmo_import_colors" class="button button-secondary" value="Import Color Settings" />
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .wmo-layout {
        display: flex;
        gap: 20px;
    }

    .wmo-main-content {
        flex: 1;
    }

    .wmo-sidebar {
        width: 250px;
    }

    .wmo-color-groups {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .wmo-color-group {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .wmo-color-picker-wrapper {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .wmo-color-picker-wrapper label {
        flex: 1;
        font-weight: bold;
        margin-right: 10px;
    }

    .wmo-color-picker-wrapper input[type="text"] {
        width: 100px;
    }

    .wmo-widget {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>