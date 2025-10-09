<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'wp-menu-organize'));
}

// Declare global $menu early
global $menu;

// Enhanced PHP debugging
? 'YES' : 'NO'));
? count($menu) : 'NOT AN ARRAY'));

// Check if $menu is empty or not an array
if (!is_array($menu) || empty($menu)) {
    echo '<div class="wrap"><div class="notice notice-error"><p><strong>Error:</strong> Global $menu is empty or not an array. This indicates a WordPress core issue or plugin conflict.</p></div></div>';
    return;
}

// Get current menu order from settings (flat structure)
$saved_order = wmo_get_settings('menu_order');
$saved_order = is_array($saved_order) ? $saved_order : array();

);
);

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

);

// Apply saved order if available - improved logic
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
    . ' items');
}

// Log final rendered order for debugging
$final_slugs = array();
foreach ($valid_menu_items as $item) {
    $final_slugs[] = $item[2];
}
);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="wmo-instructions">
        <p><strong>âœ¨ Optimize your workflow:</strong> Drag items to arrange your menu in the order that works best for you. Your most-used pages should be easily accessible.</p>
        <p><em>Tip: Click and drag the menu item titles to reorder them. Changes are saved automatically when you drag items.</em></p>
    </div>

    <?php if (empty($valid_menu_items)): ?>
        <div class="notice notice-warning">
            <p><strong>No menu items found to reorder.</strong> This might be due to insufficient permissions or the menu not being loaded properly.</p>
            <p>Debug info: Processed <?php echo $processed_count; ?> items, found <?php echo count($valid_menu_items); ?> valid items.</p>
            <p>Check global $menu: <?php echo is_array($menu) ? 'Array with ' . count($menu) . ' items' : 'Not an array'; ?></p>
        </div>
    <?php else: ?>
        <form method="post" id="wmo-reorder-form">
            <?php wp_nonce_field('wmo_save_menu_order', 'wmo_nonce'); ?>
            
            <!-- Main sortable container with proper class -->
            <div class="menu-items-list">
                <ul id="wmo-sortable-menu">
                    <?php foreach ($valid_menu_items as $menu_item): ?>
                        <?php
                        // Extract menu item data - preserve exact slugs
                        $menu_title = strip_tags($menu_item[0]);
                        $menu_slug = $menu_item[2]; // Use exact slug (e.g., 'index.php', 'edit.php')
                        $menu_icon = isset($menu_item[6]) ? $menu_item[6] : 'dashicons-admin-generic';
                        
                        // Skip if no title or slug
                        if (empty($menu_title) || empty($menu_slug)) {
                            continue;
                        }
                        ?>
                        <li class="menu-item" data-slug="<?php echo esc_attr($menu_slug); ?>">
                            <div class="menu-item-handle">
                                <span class="dashicons <?php echo esc_attr($menu_icon); ?>"></span>
                                <span class="item-title"><?php echo esc_html($menu_title); ?></span>
                                <span class="item-slug"><?php echo esc_html($menu_slug); ?></span>
                                <span class="item-controls">
                                    <span class="drag-hint"><?php _e('Drag to reorder', 'wp-menu-organize'); ?></span>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="wmo-actions">
                <p class="submit">
                    <button type="button" id="wmo-refresh-page" class="button button-primary">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Refresh Page to Apply Changes', 'wp-menu-organize'); ?>
                    </button>
                    <button type="button" id="wmo-reset-order" class="button button-secondary">
                        <span class="dashicons dashicons-image-rotate"></span>
                        <?php _e('Reset to Default', 'wp-menu-organize'); ?>
                    </button>
                    <span id="wmo-save-status" class="wmo-status"></span>
                </p>
                <p class="description">
                    <em><?php _e('Changes are saved automatically when you drag items. Click "Refresh Page to Apply Changes" to see the new order in the WordPress admin sidebar.', 'wp-menu-organize'); ?></em>
                </p>
            </div>
        </form>

        <div class="wmo-debug-info" style="display: none;">
            <h3><?php _e('Debug Information', 'wp-menu-organize'); ?></h3>
            <p><strong><?php _e('Total menu items:', 'wp-menu-organize'); ?></strong> <?php echo count($valid_menu_items); ?></p>
            <p><strong><?php _e('Saved order items:', 'wp-menu-organize'); ?></strong> <?php echo count($saved_order); ?></p>
            <p><strong><?php _e('Current page:', 'wp-menu-organize'); ?></strong> <?php echo esc_html($_GET['page'] ?? 'unknown'); ?></p>
            <p><strong><?php _e('Global menu count:', 'wp-menu-organize'); ?></strong> <?php echo count($menu); ?></p>
            <p><strong><?php _e('Final order slugs:', 'wp-menu-organize'); ?></strong> <?php echo esc_html(implode(', ', $final_slugs)); ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
.menu-items-list {
    max-width: 800px;
    margin: 20px 0;
}

#wmo-sortable-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
}

.menu-item {
    border-bottom: 1px solid #f0f0f0;
    background: #fff;
    transition: background-color 0.2s ease;
}

.menu-item:last-child {
    border-bottom: none;
}

.menu-item:hover {
    background: #f9f9f9;
}

.menu-item.dragging {
    background: #e7f3ff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.menu-item-handle {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    cursor: move;
    user-select: none;
}

.menu-item-handle .dashicons {
    margin-right: 10px;
    color: #666;
    width: 20px;
    height: 20px;
    font-size: 16px;
}

.item-title {
    flex: 1;
    font-weight: 500;
    color: #333;
}

.item-slug {
    color: #666;
    font-size: 12px;
    margin-left: 10px;
    font-family: monospace;
}

.item-controls {
    margin-left: auto;
}

.drag-hint {
    color: #999;
    font-size: 12px;
    font-style: italic;
}

.sortable-placeholder {
    height: 50px;
    background: #f0f8ff;
    border: 2px dashed #0073aa;
    margin: 5px 0;
    border-radius: 4px;
}

.wmo-actions {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.wmo-status {
    margin-left: 10px;
    font-weight: 500;
}

.wmo-status.success {
    color: #46b450;
}

.wmo-status.error {
    color: #dc3232;
}

.wmo-status.loading {
    color: #0073aa;
}

.wmo-instructions {
    background: #fff;
    border-left: 4px solid #0073aa;
    padding: 15px;
    margin: 20px 0;
    border-radius: 0 4px 4px 0;
}

.wmo-instructions p {
    margin: 0 0 10px 0;
}

.wmo-instructions p:last-child {
    margin-bottom: 0;
}

.wmo-debug-info {
    margin-top: 30px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.wmo-debug-info h3 {
    margin-top: 0;
    color: #666;
}

.wmo-notice {
    position: fixed;
    top: 32px;
    right: 20px;
    z-index: 9999;
    max-width: 300px;
    padding: 10px 15px;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    animation: wmo-slide-in 0.3s ease-out;
}

.wmo-notice.success {
    background: #46b450;
    color: white;
}

.wmo-notice.error {
    background: #dc3232;
    color: white;
}

@keyframes wmo-slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // COORDINATION FLAGS - Prevent double initialization
    if (window.wmoTemplateInitialized) {
        console.log('WMO: Template already initialized, skipping duplicate initialization');
        return;
    }
    window.wmoTemplateInitialized = true;
    
    // Global saveMenuOrder function - define at the top
    window.saveMenuOrder = function() {
        try {
            var $status = $('#wmo-save-status');
            
            $status.removeClass('success error').addClass('loading').text('Saving...');
            
            // Collect the full order as array of slugs
            var order = [];
            $('#wmo-sortable-menu li').each(function() {
                var slug = $(this).data('slug');
                if (slug && slug.trim() !== '') {
                    order.push(slug);
                }
            });
            
            console.log('WMO: Saving menu order:', order);
            
            // Check if wmo_ajax is defined, fallback to admin-ajax.php
            var ajaxUrl = (typeof wmo_ajax !== 'undefined' && wmo_ajax.ajax_url) ? wmo_ajax.ajax_url : ajaxurl;
            var nonce = (typeof wmo_ajax !== 'undefined' && wmo_ajax.nonce) ? wmo_ajax.nonce : '<?php echo wp_create_nonce('wmo_ajax_nonce'); ?>';
            
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wmo_save_menu_order',
                    order: order,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').text('Menu order saved successfully!');
                        
                        // Show success notice
                        var notice = $('<div class="wmo-notice success">Menu order saved! Refresh page to see changes in admin sidebar.</div>');
                        $('body').append(notice);
                        setTimeout(function() {
                            notice.remove();
                        }, 3000);
                        
                        setTimeout(function() {
                            $status.removeClass('success').text('');
                        }, 3000);
                    } else {
                        $status.removeClass('loading success').addClass('error').text('Error saving menu order: ' + (response.data || 'Unknown error'));
                    }
                },
                error: function() {
                    $status.removeClass('loading success').addClass('error').text('Network error while saving menu order');
                },
                complete: function() {
                    // Remove loading state
                }
            });
        } catch (error) {
            console.error('WMO: Error in saveMenuOrder:', error);
            alert('Error saving menu order: ' + error.message);
        }
    };
    
    // Debug function to check if elements are present
    function wmoDebugElements() {
        console.log('WMO Debug: Checking elements...');
        console.log('Menu items list found:', $('.menu-items-list').length);
        console.log('Sortable menu found:', $('#wmo-sortable-menu').length);
        console.log('Menu items found:', $('#wmo-sortable-menu li').length);
        console.log('Menu item handles found:', $('.menu-item-handle').length);
        
        return {
            container: $('.menu-items-list').length,
            menu: $('#wmo-sortable-menu').length,
            items: $('#wmo-sortable-menu li').length,
            handles: $('.menu-item-handle').length
        };
    }
    
    // Initialize debug on page load
    wmoDebugElements();
    
    // Reset order function
    function resetMenuOrder() {
        if (!confirm('<?php _e('Are you sure you want to reset the menu order to default? This action cannot be undone.', 'wp-menu-organize'); ?>')) {
            return;
        }
        
        var $status = $('#wmo-save-status');
        var $resetButton = $('#wmo-reset-order');
        
        $status.removeClass('success error').addClass('loading').text('Resetting...');
        $resetButton.prop('disabled', true);
        
        // Check if wmo_ajax is defined, fallback to admin-ajax.php
        var ajaxUrl = (typeof wmo_ajax !== 'undefined' && wmo_ajax.ajax_url) ? wmo_ajax.ajax_url : ajaxurl;
        var nonce = (typeof wmo_ajax !== 'undefined' && wmo_ajax.nonce) ? wmo_ajax.nonce : '<?php echo wp_create_nonce('wmo_ajax_nonce'); ?>';
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'wmo_reset_menu_order',
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.removeClass('loading error').addClass('success').text('Menu order reset successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $status.removeClass('loading success').addClass('error').text('Error resetting menu order: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                $status.removeClass('loading success').addClass('error').text('Network error while resetting menu order');
            },
            complete: function() {
                $resetButton.prop('disabled', false);
            }
        });
    }
    
    // Event handlers
    $('#wmo-refresh-page').on('click', function() {
        if (confirm('<?php _e('Refresh the page to apply menu order changes to the WordPress admin sidebar?', 'wp-menu-organize'); ?>')) {
            window.location.reload();
        }
    });
    $('#wmo-reset-order').on('click', resetMenuOrder);
    
    // Function to initialize sortable with enhanced retry mechanism
    function initializeSortableWithRetry(maxRetries = 10, delay = 300) {
        // COORDINATION CHECK - Prevent double sortable initialization
        if (window.wmoSortableInitialized) {
            console.log('WMO: Sortable already initialized, skipping duplicate initialization');
            return;
        }
        var retryCount = 0;
        
        function tryInitialize() {
            console.log('WMO: Attempting to initialize sortable (attempt ' + (retryCount + 1) + ' of ' + maxRetries + ')');
            
            // Check if elements exist
            var $container = $('.menu-items-list');
            var $menu = $('#wmo-sortable-menu');
            var $items = $menu.find('li');
            
            console.log('WMO: Container found:', $container.length);
            console.log('WMO: Menu found:', $menu.length);
            console.log('WMO: Menu items found:', $items.length);
            
            if ($container.length && $menu.length && $items.length > 0) {
                console.log('WMO: Elements found, initializing sortable');
                
                // COORDINATION CHECK - Mark as initialized
                window.wmoSortableInitialized = true;
                
                // Try to use the existing wmoInitializeSortable function first
                if (typeof window.wmoInitializeSortable === 'function') {
                    console.log('WMO: Using existing wmoInitializeSortable function');
                    try {
                        window.wmoInitializeSortable();
                    } catch (error) {
                        console.error('WMO: Error in wmoInitializeSortable:', error);
                        // Fall back to our own initialization
                        initializeSortableFallback($menu);
                    }
                } else {
                    // Fallback to our own initialization
                    console.log('WMO: wmoInitializeSortable not found, using fallback initialization');
                    initializeSortableFallback($menu);
                }
                
                // Post-init check
                if ($menu.hasClass('ui-sortable')) {
                    console.log('WMO: Sortable ready');
                }
                
                return true; // Success
            } else {
                console.log('WMO: Elements not found yet, retrying...');
                
                retryCount++;
                if (retryCount < maxRetries) {
                    setTimeout(tryInitialize, delay);
                } else {
                    console.error('WMO: Failed to initialize sortable after', maxRetries, 'attempts');
                    alert('Sortable menu not found. Please refresh or check console.');
                    $('.wmo-instructions').append('<div class="notice notice-error"><p>Failed to initialize drag and drop functionality. Please refresh the page and try again.</p></div>');
                }
                return false;
            }
        }
        
        // Fallback sortable initialization
        function initializeSortableFallback($menu) {
            // Initialize sortable if jQuery UI is available
            if (typeof $.fn.sortable !== 'undefined') {
                try {
                    $menu.sortable({
                        items: '> li',
                        handle: '.menu-item-handle',
                        placeholder: 'sortable-placeholder',
                        tolerance: 'pointer',
                        cursor: 'move',
                        axis: 'y',
                        opacity: 0.8,
                        zIndex: 1000,
                        start: function(event, ui) {
                            ui.item.addClass('dragging');
                        },
                        stop: function(event, ui) {
                            ui.item.removeClass('dragging');
                        },
                        update: function(event, ui) {
                            console.log('WMO: Menu order updated');
                            if (typeof window.saveMenuOrder === 'function') {
                                window.saveMenuOrder();
                            } else {
                                console.error('WMO: saveMenuOrder function not found');
                            }
                        }
                    }).disableSelection();
                    
                    console.log('WMO: Sortable initialized successfully (fallback)');
                } catch (error) {
                    console.error('WMO: Error initializing sortable:', error);
                }
            } else {
                console.error('WMO: jQuery UI sortable not available');
                $('.wmo-instructions').append('<div class="notice notice-error"><p>jQuery UI sortable is not available. Please check if jQuery UI is properly loaded.</p></div>');
            }
        }
        
        // Start the retry process
        tryInitialize();
    }
    
    // Initialize with enhanced retry mechanism
    initializeSortableWithRetry();
});
</script>
