<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get built-in templates
 * This function MUST be defined before any calls to it
 * @return array
 */
function wmo_get_builtin_templates() {
    error_log('WMO: wmo_get_builtin_templates called successfully');
    return array(
        'default' => array(
            'name' => 'Default Layout',
            'title' => 'Default Layout',
            'description' => 'The standard WordPress admin menu layout',
            'desc' => 'The standard WordPress admin menu layout',
            'category' => 'default',
            'preview' => 'Standard WordPress admin menu with all default items',
            'layout' => array()
        ),
        'minimal' => array(
            'name' => 'Minimal',
            'title' => 'Minimal',
            'description' => 'A simplified menu with only essential items',
            'desc' => 'A simplified menu with only essential items',
            'category' => 'business',
            'preview' => 'Clean, minimal layout focusing on core functionality',
            'layout' => array(
                'dashboard' => 0,
                'posts' => 10,
                'media' => 20,
                'pages' => 30,
                'users' => 40,
                'settings' => 50
            )
        ),
        'content-focused' => array(
            'name' => 'Content Focused',
            'title' => 'Content Focused',
            'description' => 'Prioritizes content management items',
            'desc' => 'Prioritizes content management items',
            'category' => 'creative',
            'preview' => 'Optimized for content creators and editors',
            'layout' => array(
                'dashboard' => 0,
                'posts' => 10,
                'pages' => 20,
                'media' => 30,
                'comments' => 40,
                'appearance' => 50,
                'plugins' => 60,
                'users' => 70,
                'tools' => 80,
                'settings' => 90
            )
        ),
        'dark-mode' => array(
            'id' => 'dark-mode',
            'name' => 'Dark Mode Pro',
            'title' => 'Dark Mode Pro',
            'category' => 'MODERN',
            'description' => 'Sleek dark theme for reduced eye strain',
            'desc' => 'Sleek dark theme for reduced eye strain',
            'preview' => 'Perfect for developers and night workers',
            'layout' => array()
        ),
        'wordpress-classic' => array(
            'id' => 'wordpress-classic',
            'name' => 'WordPress Classic',
            'title' => 'WordPress Classic',
            'category' => 'CLASSIC',
            'description' => 'Traditional WordPress admin colors',
            'desc' => 'Traditional WordPress admin colors',
            'preview' => 'The familiar WordPress blue theme',
            'layout' => array()
        ),
        'developer' => array(
            'id' => 'developer',
            'name' => 'Developer',
            'title' => 'Developer',
            'category' => 'TECHNICAL',
            'description' => 'Terminal-inspired green on black',
            'desc' => 'Terminal-inspired green on black',
            'preview' => 'Code editor inspired theme',
            'layout' => array()
        ),
        'agency' => array(
            'id' => 'agency',
            'name' => 'Agency',
            'title' => 'Agency',
            'category' => 'PROFESSIONAL',
            'description' => 'Professional gradients and modern design',
            'desc' => 'Professional gradients and modern design',
            'preview' => 'Impress clients with sleek styling',
            'layout' => array()
        ),
        'ecommerce' => array(
            'id' => 'ecommerce',
            'name' => 'E-commerce',
            'title' => 'E-commerce',
            'category' => 'BUSINESS',
            'description' => 'Optimized for WooCommerce stores',
            'desc' => 'Optimized for WooCommerce stores',
            'preview' => 'Shop-focused menu organization',
            'layout' => array()
        ),
        'blogger' => array(
            'id' => 'blogger',
            'name' => 'Blogger',
            'title' => 'Blogger',
            'category' => 'CREATIVE',
            'description' => 'Content-first with pastel accents',
            'desc' => 'Content-first with pastel accents',
            'preview' => 'Perfect for content creators',
            'layout' => array()
        )
    );
}

function wmo_save_menu_order()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $new_order = isset($_POST['order']) ? $_POST['order'] : array();
    
    // Validate order array
    if (!is_array($new_order)) {
        wp_send_json_error('Invalid order data format');
        return;
    }
    
    // Validate each item is a string
    foreach ($new_order as $slug) {
        if (!is_string($slug) || empty($slug)) {
            wp_send_json_error('Invalid slug in order array');
            return;
        }
    }
    
    if (!empty($new_order)) {
        // Save to flat menu_order structure (not nested under admin_customizations)
        $update_result = wmo_update_settings('menu_order', $new_order);
        
        if ($update_result !== false) {
            error_log('WMO: Menu order saved successfully: ' . print_r($new_order, true));
            wp_send_json_success(array(
                'message' => 'Menu order saved successfully',
                'order' => $new_order
            ));
        } else {
            error_log('WMO: Failed to save menu order');
            wp_send_json_error('Failed to save menu order');
        }
    } else {
        error_log('WMO: No order data received');
        wp_send_json_error('No order data received');
    }
}
add_action('wp_ajax_wmo_save_menu_order', 'wmo_save_menu_order');

function wmo_reset_menu_order()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    // Remove the menu order setting
    $settings = wmo_get_settings();
    if (isset($settings['menu_order'])) {
        unset($settings['menu_order']);
        $update_result = update_option('wmo_settings', $settings);
        
        if ($update_result !== false) {
            wp_send_json_success(array(
                'message' => 'Menu order reset to default successfully'
            ));
        } else {
            wp_send_json_error('Failed to reset menu order');
        }
    } else {
        wp_send_json_success(array(
            'message' => 'Menu order was already at default'
        ));
    }
}
add_action('wp_ajax_wmo_reset_menu_order', 'wmo_reset_menu_order');

function wmo_apply_custom_menu_order($menu_order)
{
    global $menu;
    
            $saved_order = wmo_get_settings('menu_order');
    
    if (empty($saved_order)) {
        return $menu_order;
    }
    
    $ordered_menu = array();
    $unordered_items = array();
    
    // First pass: collect items that have defined positions
    foreach ($menu as $key => $item) {
        $slug = isset($item[2]) ? $item[2] : '';
        
        if (isset($saved_order[$slug])) {
            $position = intval($saved_order[$slug]);
            $ordered_menu[$position] = $item;
        } else {
            $unordered_items[] = $item;
        }
    }
    
    // Sort by position
    ksort($ordered_menu);
    
    // Add unordered items at the end
    $ordered_menu = array_merge($ordered_menu, $unordered_items);
    
    return array_keys($ordered_menu);
}

function wmo_save_menu_colors()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $colors = isset($_POST['colors']) ? $_POST['colors'] : array();
    
    if (!empty($colors)) {
        $sanitized_colors = array();
        foreach ($colors as $slug => $color) {
            $sanitized_slug = wmo_validate_menu_id($slug);
            $sanitized_color = wmo_validate_color($color);
            if ($sanitized_slug && $sanitized_color) {
                $sanitized_colors[$sanitized_slug] = $sanitized_color;
            }
        }
        
        $update_result = wmo_update_settings('colors', $sanitized_colors);
        
        if ($update_result !== false) {
            wp_send_json_success('Colors saved successfully');
        } else {
            wp_send_json_error('Failed to save colors');
        }
    } else {
        wp_send_json_error('No color data received');
    }
}
add_action('wp_ajax_wmo_save_menu_colors', 'wmo_save_menu_colors');

function wmo_save_color()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $item_id = isset($_POST['id']) ? wmo_validate_menu_id($_POST['id']) : false;
    $color = isset($_POST['color']) ? wmo_validate_color($_POST['color']) : '';
    
    if (!$item_id) {
        wp_send_json_error('Invalid item ID provided');
        return;
    }
    
    $menu_colors = wmo_get_settings('colors');
    
    if (empty($color)) {
        // Remove color if empty
        if (isset($menu_colors[$item_id])) {
            unset($menu_colors[$item_id]);
        }
    } else {
        // Set color
        $menu_colors[$item_id] = $color;
    }
    
    $update_result = wmo_update_settings('colors', $menu_colors);
    
    if ($update_result !== false) {
        wp_send_json_success(array(
            'message' => 'Color saved successfully',
            'item_id' => $item_id,
            'color' => $color
        ));
    } else {
        wp_send_json_error('Failed to save color');
    }
}
add_action('wp_ajax_wmo_save_color', 'wmo_save_color');

function wmo_save_background_color()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'wmo_ajax_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $item_id = sanitize_text_field($_POST['id']);
    $color = sanitize_hex_color($_POST['color']);
    
    if (empty($item_id)) {
        wp_send_json_error('Invalid item ID');
    }
    
    // Get existing background colors
    $background_colors = get_option('wmo_menu_background_colors', array());
    
    // Debug logging
    error_log("WMO: AJAX save background color - Item ID: {$item_id}, Color: {$color}");
    error_log("WMO: Existing background colors: " . (is_array($background_colors) ? count($background_colors) : 'none'));
    
    if (!is_array($background_colors)) {
        $background_colors = array();
    }
    
    // Update the background color for this item
    if (!empty($color)) {
        $background_colors[$item_id] = $color;
        error_log("WMO: Added/updated background color for {$item_id}: {$color}");
    } else {
        // Remove the background color if empty
        unset($background_colors[$item_id]);
        error_log("WMO: Removed background color for {$item_id}");
    }
    
    // Save the updated background colors
    $update_result = update_option('wmo_menu_background_colors', $background_colors);
    error_log("WMO: Update result: " . ($update_result ? 'success' : 'failed'));
    error_log("WMO: Final background colors count: " . count($background_colors));
    
    if ($update_result !== false) {
        wp_send_json_success(array(
            'message' => 'Background color saved successfully',
            'item_id' => $item_id,
            'color' => $color
        ));
    } else {
        wp_send_json_error('Failed to save background color');
    }
}
add_action('wp_ajax_wmo_save_background_color', 'wmo_save_background_color');

function wmo_apply_menu_colors()
{
    $menu_colors = wmo_get_settings('colors');
    
    if (!empty($menu_colors) && is_array($menu_colors)) {
        $css_rules = '';
        foreach ($menu_colors as $slug => $color) {
            if (!empty($color)) {
                // FIXED - Target parent menu items only, not submenu items
                $css_rules .= "
                    /* Target parent menu items only */
                    body #adminmenu li#menu-{$slug} > a,
                    body #adminmenu li#toplevel_page_{$slug} > a,
                    body #adminmenu li[id='menu-{$slug}'] > a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] > a,
                    body #adminmenu li#menu-{$slug} .wp-menu-name,
                    body #adminmenu li#toplevel_page_{$slug} .wp-menu-name,
                    body #adminmenu li#menu-{$slug} .wp-menu-image:before,
                    body #adminmenu li#toplevel_page_{$slug} .wp-menu-image:before { 
                        color: " . esc_attr($color) . " !important; 
                    }
                    
                    /* EXPLICITLY EXCLUDE submenu items from inheriting parent colors */
                    body #adminmenu li#menu-{$slug} .wp-submenu li a,
                    body #adminmenu li#toplevel_page_{$slug} .wp-submenu li a,
                    body #adminmenu li[id='menu-{$slug}'] .wp-submenu li a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] .wp-submenu li a { 
                        color: inherit !important; 
                    }
                ";
            }
        }
        
        if (!empty($css_rules)) {
            wp_add_inline_style('wp-menu-organize-style', $css_rules);
        }
    }
}

function wmo_apply_menu_background_colors()
{
    $background_colors = get_option('wmo_menu_background_colors', array());
    
    // Debug logging
    error_log('WMO: Global background colors function called');
    error_log('WMO: Background colors found: ' . (is_array($background_colors) ? count($background_colors) : 'none'));
    if (is_array($background_colors) && !empty($background_colors)) {
        error_log('WMO: Background color slugs: ' . implode(', ', array_keys($background_colors)));
    }
    
    // Debug: Log current admin menu structure to understand the real IDs
    global $menu;
    if (!empty($menu)) {
        error_log('WMO: Current admin menu structure:');
        foreach ($menu as $key => $item) {
            if (isset($item[2])) {
                error_log("WMO: Menu item - Key: {$key}, Slug: {$item[2]}, Title: {$item[0]}");
            }
        }
    }
    
    if (!empty($background_colors) && is_array($background_colors)) {
        $css_rules = '';
        foreach ($background_colors as $slug => $color) {
            if (!empty($color)) {
                // FIXED - Target parent menu items only, explicitly exclude submenu items
                $css_rules .= "
                    /* Primary selectors - parent menu items only */
                    body #adminmenu li#menu-{$slug} > a,
                    body #adminmenu li#toplevel_page_{$slug} > a,
                    body #adminmenu li[id='menu-{$slug}'] > a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] > a,
                    
                    /* Hover states - parent menu items only */
                    body #adminmenu li#menu-{$slug} > a:hover,
                    body #adminmenu li#toplevel_page_{$slug} > a:hover,
                    body #adminmenu li[id='menu-{$slug}'] > a:hover,
                    body #adminmenu li[id='toplevel_page_{$slug}'] > a:hover,
                    
                    /* Current/highlighted states - parent menu items only */
                    body #adminmenu li#menu-{$slug}.current > a,
                    body #adminmenu li#toplevel_page_{$slug}.current > a,
                    body #adminmenu li[id='menu-{$slug}'].current > a,
                    body #adminmenu li[id='toplevel_page_{$slug}'].current > a,
                    body #adminmenu li#menu-{$slug}.wp-has-current-submenu > a,
                    body #adminmenu li#toplevel_page_{$slug}.wp-has-current-submenu > a,
                    body #adminmenu li[id='menu-{$slug}'].wp-has-current-submenu > a,
                    body #adminmenu li[id='toplevel_page_{$slug}'].wp-has-current-submenu > a,
                    
                    /* Alternative selectors - parent menu items only */
                    body #adminmenu li#menu-{$slug} a,
                    body #adminmenu li#toplevel_page_{$slug} a,
                    body #adminmenu li[id='menu-{$slug}'] a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] a,
                    
                    /* Direct menu item targeting - parent menu items only */
                    body #adminmenu #menu-{$slug} > a,
                    body #adminmenu #toplevel_page_{$slug} > a,
                    body #adminmenu li[id='menu-{$slug}'] > a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] > a { 
                        background-color: " . esc_attr($color) . " !important; 
                    }
                    
                    /* EXPLICITLY EXCLUDE submenu items from inheriting parent background colors */
                    body #adminmenu li#menu-{$slug} .wp-submenu li a,
                    body #adminmenu li#toplevel_page_{$slug} .wp-submenu li a,
                    body #adminmenu li[id='menu-{$slug}'] .wp-submenu li a,
                    body #adminmenu li[id='toplevel_page_{$slug}'] .wp-submenu li a { 
                        background-color: inherit !important; 
                    }
                ";
                error_log("WMO: Added CSS rule for slug: {$slug}, color: {$color}");
            }
        }
        
        if (!empty($css_rules)) {
            // Use wp-admin style which is always loaded on admin pages
            wp_add_inline_style('wp-admin', $css_rules);
            error_log('WMO: Background color CSS rules added to wp-admin style');
            error_log('WMO: Generated CSS rules: ' . $css_rules);
            
            // Also add as a separate style with higher priority
            wp_register_style('wmo-background-colors', false);
            wp_enqueue_style('wmo-background-colors');
            wp_add_inline_style('wmo-background-colors', $css_rules);
            error_log('WMO: Background color CSS also added as separate style with higher priority');
        } else {
            error_log('WMO: No background color CSS rules to add');
        }
    } else {
        error_log('WMO: No background colors found or not an array');
    }
}

function wmo_apply_typography_globally()
{
    // Only apply on admin pages, not on plugin settings pages where JavaScript handles it
    if (isset($_GET['page']) && strpos($_GET['page'], 'wp-menu-organize') !== false) {
        return;
    }
    
    $menu_typography = wmo_get_settings('typography');
    
    if (!empty($menu_typography) && is_array($menu_typography)) {
        $css_rules = '';
        foreach ($menu_typography as $slug => $typography_data) {
            if (is_array($typography_data) && isset($typography_data['enabled']) && $typography_data['enabled']) {
                $rule_parts = array();
                
                if (!empty($typography_data['font_family'])) {
                    $rule_parts[] = "font-family: " . esc_attr($typography_data['font_family']) . " !important";
                }
                if (!empty($typography_data['font_size'])) {
                    $rule_parts[] = "font-size: " . esc_attr($typography_data['font_size']) . " !important";
                }
                if (!empty($typography_data['font_weight'])) {
                    $rule_parts[] = "font-weight: " . esc_attr($typography_data['font_weight']) . " !important";
                }
                if (!empty($typography_data['font_style'])) {
                    $rule_parts[] = "font-style: " . esc_attr($typography_data['font_style']) . " !important";
                }
                if (!empty($typography_data['text_transform'])) {
                    $rule_parts[] = "text-transform: " . esc_attr($typography_data['text_transform']) . " !important";
                }
                if (!empty($typography_data['letter_spacing'])) {
                    $rule_parts[] = "letter-spacing: " . esc_attr($typography_data['letter_spacing']) . " !important";
                }
                
                if (!empty($rule_parts)) {
                    $css_rules .= "#menu-{$slug} > a, #toplevel_page_{$slug} > a { " . implode('; ', $rule_parts) . "; }";
                }
            }
        }
        
        // Ensure badges maintain their own font size regardless of parent typography
        $css_rules .= "#adminmenu .wmo-menu-badge { font-size: 10px !important; font-weight: 600 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; }";
        
        if (!empty($css_rules)) {
            wp_add_inline_style('wp-menu-organize-style', $css_rules);
        }
    }
}

function wmo_apply_badges_globally()
{
    // Only apply on admin pages, not on plugin settings pages where JavaScript handles it
    // UNCOMMENT THE LINE BELOW TO TEST BADGES ON SETTINGS PAGE
    // if (isset($_GET['page']) && strpos($_GET['page'], 'wp-menu-organize') !== false) {
    //     return;
    // }
    
    $menu_badges = wmo_get_settings('badges');
    
    // Debug logging
    error_log('WMO: Badge application - Found badges: ' . (is_array($menu_badges) ? count($menu_badges) : 'none'));
    if (is_array($menu_badges) && !empty($menu_badges)) {
        error_log('WMO: Badge application - Badge slugs: ' . implode(', ', array_keys($menu_badges)));
    }
    
    if (!empty($menu_badges) && is_array($menu_badges)) {
        // Ensure the CSS is loaded
        echo '<link rel="stylesheet" href="' . WMO_PLUGIN_URL . 'assets/css/admin.css" type="text/css" />';
        
        echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                var menuBadges = ' . json_encode($menu_badges) . ';
                for (var slug in menuBadges) {
                    if (menuBadges[slug].enabled && menuBadges[slug].text) {
                        // Find menu items using the same selectors as the original function
                        var selectors = [
                            "#menu-" + slug + " > a .wp-menu-name",
                            "#toplevel_page_" + slug + " > a .wp-menu-name",
                            "#adminmenu li[id*=\"" + slug + "\"] > a .wp-menu-name"
                        ];
                        
                        var found = false;
                        selectors.forEach(function(selector) {
                            var $menuItems = $(selector);
                            if ($menuItems.length > 0) {
                                $menuItems.each(function() {
                                    var $menuName = $(this);
                                    // Remove existing badge
                                    $menuName.find(".wmo-menu-badge").remove();
                                    // Add new badge
                                    var $badge = $("<span class=\"wmo-menu-badge\"></span>")
                                        .text(menuBadges[slug].text)
                                        .css({
                                            "color": menuBadges[slug].color,
                                            "background-color": menuBadges[slug].background
                                        });
                                    $menuName.append($badge);
                                    found = true;
                                });
                            }
                        });
                        
                        // Fallback: try text-based matching if no menu names found
                        if (!found) {
                            $("#adminmenu > li > a").each(function() {
                                var $link = $(this);
                                var linkText = $link.text().trim().toLowerCase();
                                var slugText = slug.replace(/-/g, " ").toLowerCase();
                                
                                if (linkText.includes(slugText)) {
                                    var $menuName = $link.find(".wp-menu-name");
                                    if ($menuName.length === 0) {
                                        $menuName = $link;
                                    }
                                    
                                    // Remove existing badge
                                    $menuName.find(".wmo-menu-badge").remove();
                                    // Add new badge
                                    var $badge = $("<span class=\"wmo-menu-badge\"></span>")
                                        .text(menuBadges[slug].text)
                                        .css({
                                            "color": menuBadges[slug].color,
                                            "background-color": menuBadges[slug].background
                                        });
                                    $menuName.append($badge);
                                }
                            });
                        }
                    }
                }
            });
        </script>';
    }
}







function wmo_apply_theme_preference()
{
    $dark_mode = wmo_get_settings('theme_preference') === 'dark';
    
    if ($dark_mode) {
        // Add JavaScript to apply theme class
        wp_add_inline_script('wmo-admin-script', '
            document.addEventListener("DOMContentLoaded", function() {
                document.body.classList.add("wmo-dark-theme");
                console.log("WMO: Applied dark theme globally from server preference");
            });
        ');
        
        // Add CSS for dark theme
        $dark_theme_css = '
            /* Dark theme styles for admin menu */
            .wmo-dark-theme #adminmenu {
                background-color: #1e1e1e !important;
            }
            .wmo-dark-theme #adminmenu li a {
                color: #e1e1e1 !important;
            }
            .wmo-dark-theme #adminmenu li a:hover {
                background-color: #2c2c2c !important;
            }
            .wmo-dark-theme #adminmenu li.current a {
                background-color: #0073aa !important;
            }
            .wmo-dark-theme #adminmenu .wp-menu-image:before {
                color: #e1e1e1 !important;
            }
        ';
        
        wp_add_inline_style('wp-menu-organize-style', $dark_theme_css);
    }
}

function wmo_save_badge()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $raw_slug = isset($_POST['slug']) ? $_POST['slug'] : '';
    $slug = wmo_validate_menu_id($raw_slug);
    $enabled = isset($_POST['enabled']) && ($_POST['enabled'] === 'true' || $_POST['enabled'] === '1');
    $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
    $color = isset($_POST['color']) ? wmo_validate_color($_POST['color']) : '#ffffff';
    $background = isset($_POST['background']) ? wmo_validate_color($_POST['background']) : '#0073aa';
    
    // Debug logging
    error_log('WMO: Badge save attempt - Raw slug: ' . $raw_slug . ', Validated slug: ' . ($slug ?: 'FALSE'));
    
    if (!$slug) {
        error_log('WMO: Badge save failed - Invalid slug: ' . $raw_slug);
        wp_send_json_error('Invalid slug provided: ' . $raw_slug);
        return;
    }
    
    $menu_badges = wmo_get_settings('badges');
    
    if ($enabled && !empty($text)) {
        $menu_badges[$slug] = array(
            'enabled' => true,
            'text' => $text,
            'color' => $color,
            'background' => $background
        );
    } else {
        // Remove badge if disabled or empty text
        if (isset($menu_badges[$slug])) {
            unset($menu_badges[$slug]);
        }
    }
    
    $update_result = wmo_update_settings('badges', $menu_badges);
    
    if ($update_result !== false) {
        error_log('WMO: Badge saved successfully for: ' . $slug);
        wp_send_json_success(array(
            'message' => 'Badge saved successfully',
            'slug' => $slug,
            'badge' => isset($menu_badges[$slug]) ? $menu_badges[$slug] : null
        ));
    } else {
        error_log('WMO: Failed to save badge for: ' . $slug);
        wp_send_json_error('Failed to save badge');
    }
}
add_action('wp_ajax_wmo_save_badge', 'wmo_save_badge');

function wmo_save_theme()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $dark_mode = isset($_POST['dark_mode']) && $_POST['dark_mode'] === 'true';
    
    $update_result = wmo_update_settings('theme_preference', $dark_mode ? 'dark' : 'light');
    
    if ($update_result !== false) {
        wp_send_json_success(array(
            'message' => 'Theme preference saved successfully',
            'dark_mode' => $dark_mode
        ));
    } else {
        wp_send_json_error('Failed to save theme preference');
    }
}
add_action('wp_ajax_wmo_save_theme', 'wmo_save_theme');

function wmo_save_typography()
{
    // Use new validation helper
    wmo_validate_ajax_request();

    $slug = isset($_POST['slug']) ? wmo_validate_menu_id($_POST['slug']) : '';
    $enabled = isset($_POST['enabled']) && ($_POST['enabled'] === 'true' || $_POST['enabled'] === '1');
    $font_family = isset($_POST['font_family']) ? sanitize_text_field($_POST['font_family']) : '';
    $font_size = isset($_POST['font_size']) ? sanitize_text_field($_POST['font_size']) : '';
    $font_weight = isset($_POST['font_weight']) ? sanitize_text_field($_POST['font_weight']) : '';
    $font_style = isset($_POST['font_style']) ? sanitize_text_field($_POST['font_style']) : '';
    $text_transform = isset($_POST['text_transform']) ? sanitize_text_field($_POST['text_transform']) : '';
    $letter_spacing = isset($_POST['letter_spacing']) ? sanitize_text_field($_POST['letter_spacing']) : '';
    
    if (empty($slug)) {
        wp_send_json_error('No slug provided');
        return;
    }
    
    $menu_typography = wmo_get_settings('typography');
    
    if ($enabled) {
        $typography_settings = array(
            'enabled' => true
        );
        
        if (!empty($font_family)) {
            $typography_settings['font_family'] = $font_family;
        }
        
        if (!empty($font_size)) {
            $typography_settings['font_size'] = $font_size;
        }
        
        if (!empty($font_weight)) {
            $typography_settings['font_weight'] = $font_weight;
        }
        
        if (!empty($font_style)) {
            $typography_settings['font_style'] = $font_style;
        }
        
        if (!empty($text_transform)) {
            $typography_settings['text_transform'] = $text_transform;
        }
        
        if (!empty($letter_spacing)) {
            $typography_settings['letter_spacing'] = $letter_spacing;
        }
        
        $menu_typography[$slug] = $typography_settings;
    } else {
        // Remove typography if disabled
        if (isset($menu_typography[$slug])) {
            unset($menu_typography[$slug]);
        }
    }
    
    $update_result = wmo_update_settings('typography', $menu_typography);
    
    if ($update_result !== false) {
        wp_send_json_success(array(
            'message' => 'Typography saved successfully',
            'slug' => $slug,
            'typography' => isset($menu_typography[$slug]) ? $menu_typography[$slug] : null
        ));
    } else {
        wp_send_json_error('Failed to save typography');
    }
}
add_action('wp_ajax_wmo_save_typography', 'wmo_save_typography');

function wmo_export_configuration()
{
    error_log('WMO: wmo_export_configuration function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for export configuration');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }

    $export_colors = isset($_POST['export_colors']) && $_POST['export_colors'] === 'true';
    $export_typography = isset($_POST['export_typography']) && $_POST['export_typography'] === 'true';
    $export_badges = isset($_POST['export_badges']) && $_POST['export_badges'] === 'true';
    $export_theme = isset($_POST['export_theme']) && $_POST['export_theme'] === 'true';
    
    error_log('WMO: Export options - Colors: ' . ($export_colors ? 'yes' : 'no') . ', Typography: ' . ($export_typography ? 'yes' : 'no') . ', Badges: ' . ($export_badges ? 'yes' : 'no') . ', Theme: ' . ($export_theme ? 'yes' : 'no'));
    
    $export_data = array(
        'version' => '1.0',
        'timestamp' => current_time('c'),
        'data' => array()
    );
    
    if ($export_colors) {
        $menu_colors = wmo_get_settings('colors');
        if (!empty($menu_colors)) {
            $export_data['data']['colors'] = $menu_colors;
        }
    }
    
    if ($export_typography) {
        $menu_typography = wmo_get_settings('typography');
        if (!empty($menu_typography)) {
            $export_data['data']['typography'] = $menu_typography;
        }
    }
    
    if ($export_badges) {
        $menu_badges = wmo_get_settings('badges');
        if (!empty($menu_badges)) {
            $export_data['data']['badges'] = $menu_badges;
        }
    }
    
    if ($export_theme) {
        $dark_mode = wmo_get_settings('theme_preference') === 'dark';
        $export_data['data']['theme'] = array(
            'dark_mode' => $dark_mode
        );
    }
    
    error_log('WMO: Export data prepared successfully');
    wp_send_json_success(array(
        'message' => 'Configuration exported successfully',
        'data' => json_encode($export_data, JSON_PRETTY_PRINT)
    ));
}
add_action('wp_ajax_wmo_export_configuration', 'wmo_export_configuration');

function wmo_preview_import()
{
    error_log('WMO: wmo_preview_import function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for preview import');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }

    $import_data_raw = isset($_POST['import_data']) ? $_POST['import_data'] : '';
    
    if (empty($import_data_raw)) {
        wp_send_json_error('No import data provided');
        return;
    }
    
    $import_data = json_decode(stripslashes($import_data_raw), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
        return;
    }
    
    if (!isset($import_data['data']) || !is_array($import_data['data'])) {
        wp_send_json_error('Invalid configuration format: missing data section');
        return;
    }
    
    $preview = array(
        'colors' => array(),
        'typography' => array(),
        'badges' => array(),
        'theme' => array(),
        'summary' => array()
    );
    
    // Preview colors
    if (isset($import_data['data']['colors']) && is_array($import_data['data']['colors'])) {
        $preview['colors'] = $import_data['data']['colors'];
        $preview['summary'][] = count($import_data['data']['colors']) . ' menu colors';
    }
    
    // Preview typography
    if (isset($import_data['data']['typography']) && is_array($import_data['data']['typography'])) {
        $preview['typography'] = $import_data['data']['typography'];
        $preview['summary'][] = count($import_data['data']['typography']) . ' typography settings';
    }
    
    // Preview badges
    if (isset($import_data['data']['badges']) && is_array($import_data['data']['badges'])) {
        $preview['badges'] = $import_data['data']['badges'];
        $preview['summary'][] = count($import_data['data']['badges']) . ' badges';
    }
    
    // Preview theme
    if (isset($import_data['data']['theme']) && is_array($import_data['data']['theme'])) {
        $preview['theme'] = $import_data['data']['theme'];
        $preview['summary'][] = 'Theme settings';
    }
    
    if (empty($preview['summary'])) {
        wp_send_json_error('No valid configuration data found in import');
        return;
    }
    
    error_log('WMO: Import preview generated successfully');
    wp_send_json_success(array(
        'message' => 'Import preview generated successfully',
        'preview' => $preview,
        'summary' => implode(', ', $preview['summary'])
    ));
}
add_action('wp_ajax_wmo_preview_import', 'wmo_preview_import');

function wmo_import_configuration()
{
    error_log('WMO: wmo_import_configuration function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for import configuration');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }

    $import_data_raw = isset($_POST['import_data']) ? $_POST['import_data'] : '';
    
    if (empty($import_data_raw)) {
        wp_send_json_error('No import data provided');
        return;
    }
    
    $import_data = json_decode(stripslashes($import_data_raw), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
        return;
    }
    
    if (!isset($import_data['data']) || !is_array($import_data['data'])) {
        wp_send_json_error('Invalid configuration format: missing data section');
        return;
    }
    
    $imported_items = array();
    $errors = array();
    
    // Import each data type
    foreach ($import_data['data'] as $type => $data) {
        try {
            switch ($type) {
                case 'colors':
                    if (is_array($data) && !empty($data)) {
                        $sanitized_colors = array();
                        foreach ($data as $slug => $color) {
                            $sanitized_colors[sanitize_text_field($slug)] = sanitize_hex_color($color);
                        }
                        update_option('wmo_menu_colors', $sanitized_colors);
                        $imported_items[] = 'Colors (' . count($sanitized_colors) . ' items)';
                    }
                    break;
                    
                case 'typography':
                    if (is_array($data) && !empty($data)) {
                        $sanitized_typography = array();
                        foreach ($data as $slug => $settings) {
                            if (is_array($settings)) {
                                $sanitized_settings = array();
                                foreach ($settings as $key => $value) {
                                    $sanitized_settings[sanitize_text_field($key)] = sanitize_text_field($value);
                                }
                                $sanitized_typography[sanitize_text_field($slug)] = $sanitized_settings;
                            }
                        }
                        update_option('wmo_menu_typography', $sanitized_typography);
                        $imported_items[] = 'Typography (' . count($sanitized_typography) . ' items)';
                    }
                    break;
                    
                case 'badges':
                    if (is_array($data) && !empty($data)) {
                        $sanitized_badges = array();
                        foreach ($data as $slug => $badge) {
                            if (is_array($badge)) {
                                $sanitized_badge = array();
                                foreach ($badge as $key => $value) {
                                    if ($key === 'enabled') {
                                        $sanitized_badge[$key] = (bool)$value;
                                    } elseif (in_array($key, ['color', 'background'])) {
                                        $sanitized_badge[$key] = sanitize_hex_color($value);
                                    } else {
                                        $sanitized_badge[$key] = sanitize_text_field($value);
                                    }
                                }
                                $sanitized_badges[sanitize_text_field($slug)] = $sanitized_badge;
                            }
                        }
                        update_option('wmo_menu_badges', $sanitized_badges);
                        $imported_items[] = 'Badges (' . count($sanitized_badges) . ' items)';
                    }
                    break;
                    
                case 'theme':
                    if (is_array($data)) {
                        if (isset($data['dark_mode'])) {
                            update_option('wmo_dark_mode', (bool)$data['dark_mode']);
                            $imported_items[] = 'Theme settings';
                        }
                    }
                    break;
                    
                default:
                    $errors[] = 'Unknown data type: ' . $type;
            }
        } catch (Exception $e) {
            $errors[] = 'Error importing ' . $type . ': ' . $e->getMessage();
            error_log('WMO: Import error for ' . $type . ': ' . $e->getMessage());
        }
    }
    
    if (!empty($imported_items)) {
        error_log('WMO: Import completed successfully: ' . implode(', ', $imported_items));
        wp_send_json_success(array(
            'message' => 'Configuration imported successfully',
            'imported_items' => $imported_items,
            'errors' => $errors,
            'summary' => implode(', ', $imported_items)
        ));
    } else {
        wp_send_json_error('No valid data found to import');
    }
}
add_action('wp_ajax_wmo_import_configuration', 'wmo_import_configuration');

// Template application handler
function wmo_apply_template() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce');
    }
    
    $template_id = sanitize_text_field($_POST['template_id']);
    $template_colors = $_POST['colors'];
    
    // Get all current menu items
    global $menu;
    $all_menu_items = array();
    
    // Common menu item IDs to color
    $menu_ids = array(
        'dashboard', 'posts', 'media', 'pages', 'comments',
        'appearance', 'plugins', 'users', 'tools', 'settings',
        'menu-organize', 'smartnav-wp', 'speed-optimizer',
        'hello-elementor', 'sg-cachepress', 'themes', 'widgets',
        'menus', 'theme-editor', 'plugin-editor', 'admin',
        'options-general', 'options-writing', 'options-reading'
    );
    
    // Convert template's 4 colors to array
    $color_array = array_values($template_colors);
    $color_count = count($color_array);
    
    // Apply colors in rotating pattern
    $final_colors = array();
    foreach ($menu_ids as $index => $menu_id) {
        // Rotate through the 4 template colors
        $color_index = $index % $color_count;
        $final_colors[$menu_id] = $color_array[$color_index];
    }
    
    // Save the complete color set
    $result = update_option('wmo_menu_colors', $final_colors);
    
    if ($result) {
        wp_send_json_success('Template applied successfully');
    } else {
        wp_send_json_error('Failed to apply template');
    }
}
add_action('wp_ajax_wmo_apply_template', 'wmo_apply_template');

// Icon system handler
function wmo_save_icon() {
    error_log('WMO: wmo_save_icon called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Icon save - insufficient permissions');
        wp_send_json_error('Insufficient permissions');
    }
    
    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }
    
    $menu_id = sanitize_key($_POST['menu_id']); // Use sanitize_key for IDs
    error_log('WMO: Icon save - original slug received: ' . $menu_id); // Log the plain slug being saved
    $icon_type = sanitize_text_field($_POST['icon_type']);
    
    // Handle emoji with proper UTF-8 encoding
    if ($_POST['icon_type'] === 'emoji') {
        $emoji = $_POST['icon_value'];
        // Ensure proper UTF-8 encoding
        $emoji = mb_convert_encoding($emoji, 'UTF-8', 'UTF-8');
        // Store as JSON-encoded string to preserve encoding
        $icon_value = json_encode($emoji, JSON_UNESCAPED_UNICODE);
        // Remove quotes from JSON encoding
        $icon_value = trim($icon_value, '"');
        error_log('WMO: Storing emoji as: ' . $icon_value);
    } else {
        $icon_value = sanitize_text_field($_POST['icon_value']);
    }
    
    error_log('WMO: Saving icon - menu_id: ' . $menu_id . ', type: ' . $icon_type . ', value: ' . $icon_value);
    
    if (empty($menu_id)) {
        error_log('WMO: Icon save - empty menu_id');
        wp_send_json_error('No menu ID provided');
    }
    
    $icons = wmo_get_settings('icons');
    $icons[$menu_id] = array(
        'type' => $icon_type,
        'value' => $icon_value
    );
    
    // Use update_option with autoload set to yes for better performance
    $result = wmo_update_settings('icons', $icons);
    
    error_log('WMO: Icon save result: ' . ($result ? 'success' : 'failed'));
    error_log('WMO: Saved icons: ' . print_r($icons, true));
    
    if ($result !== false) {  // Changed from if ($result) to handle case where value doesn't change
        wp_send_json_success('Icon saved successfully');
    } else {
        wp_send_json_error('Failed to save icon');
    }
}
add_action('wp_ajax_wmo_save_icon', 'wmo_save_icon');

// Deactivate submenu toggle handler
function wmo_save_deactivate_toggle() {
    error_log('WMO: wmo_save_deactivate_toggle called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Deactivate toggle save - insufficient permissions');
        wp_send_json_error('Insufficient permissions');
    }
    
    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_die('Security check failed');
    }
    
    $menu_slug = sanitize_key($_POST['menu_slug']);
    $enabled = (bool) $_POST['enabled'];
    
    error_log('WMO: Deactivate toggle save - menu_slug: ' . $menu_slug . ', enabled: ' . ($enabled ? 'true' : 'false'));
    
    if (empty($menu_slug)) {
        error_log('WMO: Deactivate toggle save - empty menu_slug');
        wp_send_json_error('No menu slug provided');
    }
    
    $result = wmo_update_deactivate_setting($menu_slug, $enabled);
    
    error_log('WMO: Deactivate toggle save result: ' . ($result ? 'success' : 'failed'));
    
    if ($result !== false) {
        wp_send_json_success('Deactivate toggle saved successfully');
    } else {
        wp_send_json_error('Failed to save deactivate toggle');
    }
}
add_action('wp_ajax_wmo_save_deactivate_toggle', 'wmo_save_deactivate_toggle');

// Template system handlers
function wmo_load_templates()
{
    error_log('WMO: wmo_load_templates function called');
    
    if (!current_user_can('manage_options')) {
        error_log('WMO: Insufficient permissions for load templates');
        wp_send_json_error('Insufficient permissions');
        return;
    }

    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        error_log('WMO: Invalid nonce for load templates');
        wp_send_json_error('Invalid nonce');
        return;
    }

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    
    error_log('WMO: About to call wmo_get_builtin_templates');
    $builtin_templates = wmo_get_builtin_templates();
    error_log('WMO: Got builtin templates: ' . print_r($builtin_templates, true));
    
    $custom_templates = wmo_get_settings('templates');
    error_log('WMO: Got custom templates: ' . print_r($custom_templates, true));
    
    // Combine all templates
    $all_templates = array();
    
    // Add builtin templates
    foreach ($builtin_templates as $id => $template) {
        $template['id'] = $id;
        $template['builtin'] = true;
        $all_templates[] = $template;
    }
    
    // Add custom templates
    foreach ($custom_templates as $template) {
        $template['builtin'] = false;
        $all_templates[] = $template;
    }
    
    // Filter by category if specified
    if ($category !== 'all') {
        $all_templates = array_filter($all_templates, function($template) use ($category) {
            return isset($template['category']) && $template['category'] === $category;
        });
    }
    
    error_log('WMO: Loaded ' . count($all_templates) . ' templates for category: ' . $category);
    wp_send_json_success(array(
        'templates' => array_values($all_templates),
        'category' => $category
    ));
}
add_action('wp_ajax_wmo_load_templates', 'wmo_load_templates');

// Secure custom CSS save handler
function wmo_save_custom_css() {
    wmo_validate_ajax_request();
    
    $custom_css = isset($_POST['custom_css']) ? $_POST['custom_css'] : '';
    
    // Sanitize CSS - allow only safe CSS properties
    $custom_css = wmo_sanitize_css($custom_css);
    
    $settings = wmo_get_settings();
    $settings['custom_css'] = $custom_css;
    $result = wmo_update_settings($settings);
    
    if ($result !== false) {
        wp_send_json_success(['message' => 'Custom CSS saved successfully']);
    } else {
        wp_send_json_error('Failed to save custom CSS');
    }
}
add_action('wp_ajax_wmo_save_custom_css', 'wmo_save_custom_css');

// Export settings handler
function wmo_export_settings_ajax() {
    wmo_validate_ajax_request();
    
    $export_data = wmo_export_settings();
    
    if ($export_data !== false) {
        wp_send_json_success(array(
            'message' => 'Settings exported successfully',
            'data' => $export_data,
            'filename' => 'wmo-settings-' . date('Y-m-d-H-i-s') . '.json'
        ));
    } else {
        wp_send_json_error('Failed to export settings');
    }
}
add_action('wp_ajax_wmo_export_settings', 'wmo_export_settings_ajax');

// Import settings handler
function wmo_import_settings_ajax() {
    wmo_validate_ajax_request();
    
    $import_data = isset($_POST['import_data']) ? sanitize_textarea_field($_POST['import_data']) : '';
    
    if (empty($import_data)) {
        wp_send_json_error('No import data provided');
        return;
    }
    
    $result = wmo_import_settings($import_data);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_wmo_import_settings', 'wmo_import_settings_ajax');

// Import preview handler
function wmo_import_preview_ajax() {
    wmo_validate_ajax_request();
    
    $import_data = isset($_POST['import_data']) ? sanitize_textarea_field($_POST['import_data']) : '';
    
    if (empty($import_data)) {
        wp_send_json_error('No import data provided');
        return;
    }
    
    $preview = wmo_get_import_preview($import_data);
    
    if ($preview !== false) {
        wp_send_json_success(array(
            'message' => 'Import preview generated',
            'preview' => $preview
        ));
    } else {
        wp_send_json_error('Invalid import data format');
    }
}
add_action('wp_ajax_wmo_import_preview', 'wmo_import_preview_ajax');

// Hook to apply theme preference globally on all admin pages
add_action('admin_head', 'wmo_apply_menu_colors');

// Hook to apply background colors globally on all admin pages - use very late priority
add_action('admin_head', 'wmo_apply_menu_background_colors', 999);

// Hook to apply typography globally on all admin pages
add_action('admin_head', 'wmo_apply_typography_globally');

// Hook to apply badges globally on all admin pages
add_action('admin_head', 'wmo_apply_badges_globally');

// Hook to apply theme preference globally on all admin pages
add_action('admin_head', 'wmo_apply_theme_preference');
add_action('admin_head', 'wmo_apply_theme_preference');