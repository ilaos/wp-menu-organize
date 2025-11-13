<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'wp-admin-menu-maestro'));
}

// Declare global $menu and $submenu
global $menu, $submenu;

// Enhanced PHP debugging
error_log('WAMM Reorder: Rendering reorder page');
error_log('WAMM Reorder: Global $menu is array: ' . (is_array($menu) ? 'YES' : 'NO'));
error_log('WAMM Reorder: Global $submenu is array: ' . (is_array($submenu) ? 'YES' : 'NO'));
error_log('WAMM Reorder: Global menu count: ' . (is_array($menu) ? count($menu) : 'NOT AN ARRAY'));

// Check if $menu is empty or not an array
if (!is_array($menu) || empty($menu)) {
    error_log('WAMM Reorder: ERROR - Global $menu is empty or not an array');
    echo '<div class="wrap"><div class="notice notice-error"><p><strong>Error:</strong> Global $menu is empty or not an array. This indicates a WordPress core issue or plugin conflict.</p></div></div>';
    return;
}

// Get current menu order from settings (flat structure)
$saved_order = wamm_get_settings('menu_order');
$saved_order = is_array($saved_order) ? $saved_order : array();

// Filter out separators and empty items, preserve exact slugs
$valid_menu_items = array();
$processed_count = 0;

foreach ($menu as $menu_item) {
    $processed_count++;

    // Skip separators and empty items
    if (empty($menu_item[0]) || empty($menu_item[2]) || $menu_item[2] === 'separator') {
        continue;
    }
    $valid_menu_items[] = $menu_item;
}

error_log('WAMM Reorder: Processed ' . $processed_count . ' menu items');
error_log('WAMM Reorder: Valid menu items count: ' . count($valid_menu_items));

// Apply saved order if available
if (!empty($saved_order) && count($saved_order) > 0) {
    $ordered_items = array();
    $unordered_items = array();

    // First, add items in saved order (preserve exact slugs)
    foreach ($saved_order as $slug) {
        foreach ($valid_menu_items as $key => $item) {
            if ($item[2] === $slug) {
                $ordered_items[] = $item;
                unset($valid_menu_items[$key]);
                break;
            }
        }
    }

    // Then add any remaining items in their original order
    foreach ($valid_menu_items as $item) {
        $unordered_items[] = $item;
    }

    $valid_menu_items = array_merge($ordered_items, $unordered_items);
    error_log('WAMM Reorder: Applied saved order to ' . count($ordered_items) . ' items');
}
?>

<div class="wrap wamm-reorder-page">
    <!-- Header Section -->
    <div class="wamm-reorder-header">
        <h1 class="wamm-reorder-title"><?php _e('Reorder Admin Menu', 'wp-admin-menu-maestro'); ?></h1>
        <p class="wamm-reorder-subtitle">
            <?php _e('Organize your workflow. Drag and drop to reorder your WordPress admin menu. Changes save automatically.', 'wp-admin-menu-maestro'); ?>
        </p>
        <div class="wamm-save-indicator" id="wamm-save-status"></div>
    </div>

    <?php if (empty($valid_menu_items)): ?>
        <div class="notice notice-warning">
            <p><strong><?php _e('No menu items found to reorder.', 'wp-admin-menu-maestro'); ?></strong></p>
            <p><?php _e('This might be due to insufficient permissions or the menu not being loaded properly.', 'wp-admin-menu-maestro'); ?></p>
        </div>
    <?php else: ?>

        <!-- Main Menu Panel -->
        <div class="wamm-reorder-panel">
            <div id="wamm-sortable-menu" class="wamm-menu-list">
                <?php
                $item_index = 0;
                foreach ($valid_menu_items as $menu_item):
                    // Extract menu item data
                    $menu_title = strip_tags($menu_item[0]);
                    $menu_slug = $menu_item[2];
                    $menu_icon = isset($menu_item[6]) ? $menu_item[6] : 'dashicons-admin-generic';

                    // Skip if no title or slug
                    if (empty($menu_title) || empty($menu_slug)) {
                        continue;
                    }

                    // Get submenus for this parent
                    $has_children = isset($submenu[$menu_slug]) && is_array($submenu[$menu_slug]) && count($submenu[$menu_slug]) > 0;
                    $child_count = $has_children ? count($submenu[$menu_slug]) : 0;

                    $item_id = 'wamm-menu-item-' . $item_index;
                    $children_id = 'wamm-children-' . $item_index;
                ?>

                <div class="wamm-menu-item" data-slug="<?php echo esc_attr($menu_slug); ?>">
                    <div class="wamm-menu-item-header">
                        <span class="wamm-handle" aria-label="<?php esc_attr_e('Drag to reorder', 'wp-admin-menu-maestro'); ?>" role="button">
                            <span class="dashicons dashicons-menu"></span>
                        </span>
                        <div class="wamm-menu-main">
                            <span class="dashicons <?php echo esc_attr($menu_icon); ?> wamm-menu-icon"></span>
                            <span class="wamm-menu-title"><?php echo esc_html($menu_title); ?></span>
                            <span class="wamm-menu-slug"><?php echo esc_html($menu_slug); ?></span>
                        </div>
                        <?php if ($has_children): ?>
                            <button
                                type="button"
                                class="wamm-accordion-toggle"
                                aria-expanded="false"
                                aria-controls="<?php echo esc_attr($children_id); ?>"
                                data-toggle-id="<?php echo esc_attr($children_id); ?>">
                                <span class="wamm-child-count"><?php echo esc_html($child_count); ?></span>
                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($has_children): ?>
                        <div class="wamm-menu-children" id="<?php echo esc_attr($children_id); ?>" style="display: none;">
                            <?php foreach ($submenu[$menu_slug] as $submenu_item):
                                $sub_title = strip_tags($submenu_item[0]);
                                $sub_slug = $submenu_item[2];

                                // Skip empty items
                                if (empty($sub_title)) {
                                    continue;
                                }
                            ?>
                                <div class="wamm-menu-child-item" data-slug="<?php echo esc_attr($sub_slug); ?>">
                                    <span class="wamm-handle" aria-label="<?php esc_attr_e('Drag to reorder', 'wp-admin-menu-maestro'); ?>">
                                        <span class="dashicons dashicons-menu"></span>
                                    </span>
                                    <span class="wamm-menu-title"><?php echo esc_html($sub_title); ?></span>
                                    <span class="wamm-menu-slug"><?php echo esc_html($sub_slug); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $item_index++;
                endforeach; ?>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="wamm-sticky-footer">
            <div class="wamm-sticky-footer-content">
                <button type="button" id="wamm-refresh-page" class="button button-primary">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh Admin Menu', 'wp-admin-menu-maestro'); ?>
                </button>
                <button type="button" id="wamm-reset-order" class="button button-secondary">
                    <span class="dashicons dashicons-image-rotate"></span>
                    <?php _e('Reset to Default', 'wp-admin-menu-maestro'); ?>
                </button>
            </div>
        </div>

    <?php endif; ?>
</div>

<script type="text/javascript">
(function($) {
    'use strict';

    // Prevent double initialization
    if (window.wammReorderPageInitialized) {
        console.log('WAMM Reorder: Already initialized, skipping');
        return;
    }
    window.wammReorderPageInitialized = true;

    $(document).ready(function() {
        console.log('WAMM Reorder: Initializing page');

        // Accordion toggle functionality
        $('.wamm-accordion-toggle').on('click', function() {
            var $button = $(this);
            var targetId = $button.data('toggle-id');
            var $children = $('#' + targetId);
            var isExpanded = $button.attr('aria-expanded') === 'true';

            if (isExpanded) {
                // Collapse
                $button.attr('aria-expanded', 'false');
                $button.find('.dashicons').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                $children.slideUp(200);
            } else {
                // Expand
                $button.attr('aria-expanded', 'true');
                $button.find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                $children.slideDown(200);
            }
        });

        // Save menu order function
        function saveMenuOrder() {
            var $status = $('#wamm-save-status');
            $status.removeClass('success error').addClass('loading').html('<span class="dashicons dashicons-update spin"></span> Saving...');

            // Collect order as array of parent slugs
            var order = [];
            $('#wamm-sortable-menu > .wamm-menu-item').each(function() {
                var slug = $(this).data('slug');
                if (slug && slug.trim() !== '') {
                    order.push(slug);
                }
            });

            console.log('WAMM Reorder: Saving order:', order);

            var ajaxUrl = (typeof wamm_ajax !== 'undefined' && wamm_ajax.ajax_url) ? wamm_ajax.ajax_url : ajaxurl;
            var nonce = (typeof wamm_ajax !== 'undefined' && wamm_ajax.nonce) ? wamm_ajax.nonce : '<?php echo wp_create_nonce('wamm_ajax_nonce'); ?>';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wamm_save_menu_order',
                    order: order,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').html('<span class="dashicons dashicons-yes-alt"></span> Saved');
                        setTimeout(function() {
                            $status.removeClass('success').html('');
                        }, 3000);
                    } else {
                        $status.removeClass('loading success').addClass('error').html('<span class="dashicons dashicons-warning"></span> Error: ' + (response.data || 'Unknown error'));
                    }
                },
                error: function() {
                    $status.removeClass('loading success').addClass('error').html('<span class="dashicons dashicons-warning"></span> Network error');
                }
            });
        }

        // Initialize sortable for parent items
        if (typeof $.fn.sortable !== 'undefined') {
            $('#wamm-sortable-menu').sortable({
                items: '> .wamm-menu-item',
                handle: '.wamm-menu-item-header .wamm-handle',
                placeholder: 'wamm-sortable-placeholder',
                tolerance: 'pointer',
                cursor: 'move',
                axis: 'y',
                opacity: 0.9,
                distance: 5,
                start: function(event, ui) {
                    ui.item.addClass('wamm-dragging');
                },
                stop: function(event, ui) {
                    ui.item.removeClass('wamm-dragging');
                },
                update: function(event, ui) {
                    saveMenuOrder();
                }
            }).disableSelection();

            console.log('WAMM Reorder: Parent sortable initialized');
        } else {
            console.error('WAMM Reorder: jQuery UI Sortable not available');
        }

        // Refresh page button
        $('#wamm-refresh-page').on('click', function() {
            window.location.reload();
        });

        // Reset order button
        $('#wamm-reset-order').on('click', function() {
            if (!confirm('<?php echo esc_js(__('Are you sure you want to reset the menu order to default? This action cannot be undone.', 'wp-admin-menu-maestro')); ?>')) {
                return;
            }

            var $button = $(this);
            var $status = $('#wamm-save-status');

            $button.prop('disabled', true);
            $status.removeClass('success error').addClass('loading').html('<span class="dashicons dashicons-update spin"></span> Resetting...');

            var ajaxUrl = (typeof wamm_ajax !== 'undefined' && wamm_ajax.ajax_url) ? wamm_ajax.ajax_url : ajaxurl;
            var nonce = (typeof wamm_ajax !== 'undefined' && wamm_ajax.nonce) ? wamm_ajax.nonce : '<?php echo wp_create_nonce('wamm_ajax_nonce'); ?>';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wamm_reset_menu_order',
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').html('<span class="dashicons dashicons-yes-alt"></span> Reset successful!');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $status.removeClass('loading success').addClass('error').html('<span class="dashicons dashicons-warning"></span> Error: ' + (response.data || 'Unknown error'));
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $status.removeClass('loading success').addClass('error').html('<span class="dashicons dashicons-warning"></span> Network error');
                    $button.prop('disabled', false);
                }
            });
        });

        console.log('WAMM Reorder: Page initialized successfully');
    });
})(jQuery);
</script>
