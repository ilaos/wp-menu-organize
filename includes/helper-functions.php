<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get current menu slugs and titles.
 *
 * @return array An array of menu slugs and titles.
 */
function wmo_get_current_menu_slugs()
{
    global $menu, $submenu;
    $slugs = array();

    // Handle top-level menus
    foreach ($menu as $item) {
        $menu_slug = sanitize_title(strip_tags($item[0]));
        if (!empty($menu_slug)) {
            $slugs[$menu_slug] = strip_tags($item[0]);
        }
    }

    // Handle submenus
    foreach ($submenu as $parent_slug => $subitems) {
        foreach ($subitems as $subitem) {
            $submenu_slug = sanitize_title(strip_tags($subitem[0]));
            if (!empty($submenu_slug)) {
                $slugs[$submenu_slug] = strip_tags($subitem[0]);
            }
        }
    }

    return $slugs;
}

/**
 * Render color picker for a menu item.
 *
 * @param array  $menu_colors Array of menu colors.
 * @param string $slug        Menu item slug.
 * @param string $title       Menu item title.
 * @param bool   $is_submenu  Whether the item is a submenu item.
 */
function wmo_render_color_picker($menu_colors, $slug, $title, $is_submenu = false)
{
    $color = isset($menu_colors[$slug]) ? esc_attr($menu_colors[$slug]) : '';
    $sanitized_slug = sanitize_title($slug);
?>
    <div class="wmo-color-picker-wrapper <?php echo $is_submenu ? 'wmo-submenu-wrapper' : ''; ?>">
        <label for="wmo_menucolors<?php echo esc_attr($sanitized_slug); ?>"><?php echo esc_html($title); ?> Color</label>
        <input type="text"
            id="wmo_menucolors<?php echo esc_attr($sanitized_slug); ?>"
            name="wmo_menu_colors[<?php echo esc_attr($sanitized_slug); ?>]"
            value="<?php echo $color; ?>"
            class="wmo-color-field"
            data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
            data-is-submenu="<?php echo $is_submenu ? 'true' : 'false'; ?>" />
    </div>
<?php
}

/**
 * Render color pickers for other menu items.
 *
 * @param array $menu_colors Array of menu colors.
 */
function wmo_render_other_menu_items($menu_colors)
{
    global $menu;
    $predefined_items = ['dashboard', 'posts', 'media', 'pages', 'comments', 'appearance', 'plugins', 'users', 'tools', 'settings'];

    foreach ($menu as $item) {
        $menu_slug = sanitize_title(strip_tags($item[0]));
        $menu_title = strip_tags($item[0]);

        // Skip predefined items and empty slugs
        if (empty($menu_slug) || in_array($menu_slug, $predefined_items)) {
            continue;
        }

        // Handle special cases for certain menu items
        if ($menu_slug === 'comments') {
            $menu_title = 'Comments';
        } else {
            $menu_title = preg_replace('/\d+/', '', $menu_title); // Remove numbers
            $menu_title = trim($menu_title); // Trim whitespace
        }

        wmo_render_color_picker($menu_colors, $menu_slug, $menu_title);
    }
}

/**
 * Get sanitized menu colors.
 *
 * @param array $input Raw menu colors input.
 * @return array Sanitized menu colors.
 */
function wmo_sanitize_menu_colors($input)
{
    $sanitized_colors = array();
    foreach ($input as $slug => $color) {
        $sanitized_colors[sanitize_title($slug)] = sanitize_hex_color($color);
    }
    return $sanitized_colors;
}
