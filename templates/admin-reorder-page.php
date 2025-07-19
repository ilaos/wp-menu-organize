<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="wmo-instructions">
        <p><strong>✨ Optimize your workflow:</strong> Drag items to arrange your menu in the order that works best for you. Your most-used pages should be easily accessible.</p>
    </div>
    
    <!-- Main sortable container with proper class -->
    <div class="menu-items-list">
        <ul id="wmo-sortable-menu">
            <?php
            global $menu;
            foreach ($menu as $menu_item) {
                if (!empty($menu_item[0])) {
                    $menu_name = preg_replace('/\d+/', '', strip_tags($menu_item[0]));
                    $menu_slug = isset($menu_item[2]) ? $menu_item[2] : '';
                    $menu_icon = isset($menu_item[6]) ? $menu_item[6] : 'dashicons-admin-generic';
                    
                    echo '<li class="menu-item" data-slug="' . esc_attr($menu_slug) . '">';
                    echo '<div class="menu-item-handle">';
                    echo '<span class="item-title">' . esc_html($menu_name) . '</span>';
                    echo '<span class="item-controls">' . __('Drag to reorder', 'wp-menu-organize') . '</span>';
                    echo '</div>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>
    
    <p class="submit">
        <button type="button" id="wmo-save-order" class="button button-primary"><?php _e('Save Menu Order', 'wp-menu-organize'); ?></button>
    </p>
</div>