<?php
require_once 'wp-load.php';

global $menu, $submenu;

echo "Menu Organize Analysis\n";
echo "======================\n\n";

// Find Menu Organize in the menu
foreach ($menu as $menu_item) {
    if (isset($menu_item[0]) && isset($menu_item[2])) {
        $title = strip_tags($menu_item[0]);
        $slug = $menu_item[2];
        
        if (stripos($title, 'menu') !== false || stripos($slug, 'menu') !== false ||
            stripos($title, 'organize') !== false || stripos($slug, 'organize') !== false) {
            echo "Found Menu Item:\n";
            echo "  Title: $title\n";
            echo "  Slug: $slug\n";
            echo "  Expected CSS selector: #toplevel_page_$slug\n\n";
        }
    }
}

// Check submenu
echo "Submenu Structure:\n";
foreach ($submenu as $parent_slug => $items) {
    if (stripos($parent_slug, 'menu') !== false || stripos($parent_slug, 'organize') !== false) {
        echo "\nParent: $parent_slug\n";
        foreach ($items as $item) {
            echo "  - " . strip_tags($item[0]) . " (URL: " . $item[2] . ")\n";
        }
    }
}

// Check stored colors
$bg_colors = get_option('wmo_menu_background_colors', array());
echo "\n\nStored Colors:\n";
echo "==============\n";
foreach ($bg_colors as $slug => $color) {
    $is_parent = wmo_is_parent_menu($slug) ? 'PARENT' : 'SUBMENU';
    echo "$slug: $color ($is_parent)\n";
}