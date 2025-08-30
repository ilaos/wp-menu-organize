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
 * Render color picker for menu items.
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
    
    // Add this check to prevent empty slugs
    if (empty($sanitized_slug)) {
        error_log('WMO: Warning - Empty slug provided for menu item: ' . $title);
        return; // Don't render the color picker if slug is empty
    }
    
    // Create unique identifier to prevent conflicts between different sections
    // Use a hash of the slug to ensure consistent IDs between button and expanded content
    $unique_slug = $sanitized_slug . '_' . substr(md5($sanitized_slug), 0, 8);
    
    // Debug: Log the unique slug for troubleshooting
    error_log('WMO Debug: Generated unique_slug: ' . $unique_slug . ' for sanitized_slug: ' . $sanitized_slug);
    
    // Get badge data
    $menu_badges = wmo_get_settings('badges');
    $badge_data = isset($menu_badges[$sanitized_slug]) ? $menu_badges[$sanitized_slug] : array();
    $badge_text = $badge_data['text'] ?? '';
    $badge_color = $badge_data['color'] ?? '#ffffff';
    $badge_background = $badge_data['background'] ?? '#0073aa';
    $badge_enabled = $badge_data['enabled'] ?? false;
    
    // Get typography data
    $menu_typography = wmo_get_settings('typography');
    $typography_data = isset($menu_typography[$sanitized_slug]) ? $menu_typography[$sanitized_slug] : array();
    $typography_enabled = $typography_data['enabled'] ?? false;
    $font_family = $typography_data['font_family'] ?? '';
    $font_size = $typography_data['font_size'] ?? '';
    $font_weight = $typography_data['font_weight'] ?? '';
?>
    <div class="wmo-menu-item-wrapper <?php echo $is_submenu ? 'wmo-submenu-wrapper' : ''; ?>" data-menu-slug="<?php echo esc_attr($unique_slug); ?>" data-original-slug="<?php echo esc_attr($sanitized_slug); ?>">
        <!-- DEBUG: Toggle element being rendered for <?php echo esc_html($title); ?> -->
        <!-- Compact Header - REMOVED color picker, added wmo-toggle-header class -->
        <div class="wmo-menu-header wmo-toggle-header" data-toggle-slug="<?php echo esc_attr($unique_slug); ?>">
            <div class="wmo-menu-title"><?php echo esc_html($title); ?></div>
            <div class="wmo-menu-actions">
                <!-- Status Indicators -->
                <div class="wmo-status-indicators">
                    <?php if ($typography_enabled): ?>
                        <span class="wmo-status-badge wmo-status-typography">Font</span>
                    <?php endif; ?>
                    <?php if ($badge_enabled): ?>
                        <span class="wmo-status-badge wmo-status-badge-enabled">Badge</span>
                    <?php endif; ?>
                    <?php if (!empty($color)): ?>
                        <span class="wmo-status-badge wmo-status-color">Color</span>
                    <?php endif; ?>
                </div>
                
                <!-- Expand Toggle -->
                <button type="button" class="wmo-expand-toggle" data-menu-slug="<?php echo esc_attr($unique_slug); ?>">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
            </div>
        </div>
        
        <!-- Expanded Content -->
        <div class="wmo-expanded-content" id="wmo-expanded-<?php echo esc_attr($unique_slug); ?>" style="display: none;">
            <!-- Typography Section -->
            <div class="wmo-typography-wrapper">
                <div class="wmo-typography-toggle">
                    <label>
                        <input type="checkbox" 
                               name="wmo_menu_typography[<?php echo esc_attr($sanitized_slug); ?>][enabled]" 
                               value="1"
                               class="wmo-typography-enable"
                               data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                               <?php checked($typography_enabled); ?> />
                        <span class="wmo-typography-label">Custom Typography</span>
                    </label>
                </div>
                
                <div class="wmo-typography-controls">
                    <!-- Color Picker - MOVED from header to typography section -->
                    <div class="wmo-color-picker-wrapper">
                        <label for="wmo_menucolors<?php echo esc_attr($unique_slug); ?>">Menu Color</label>
                        <input type="text"
                            id="wmo_menucolors<?php echo esc_attr($unique_slug); ?>"
                            name="wmo_menu_colors[<?php echo esc_attr($sanitized_slug); ?>]"
                            value="<?php echo $color; ?>"
                            class="wmo-color-field"
                            data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                            data-is-submenu="<?php echo $is_submenu ? 'true' : 'false'; ?>" />
                    </div>
                    
                    <div class="wmo-typography-fields" style="<?php echo $typography_enabled ? '' : 'display: none;'; ?>">
                        <div class="wmo-typography-field">
                            <label for="wmo_typography_family_<?php echo esc_attr($unique_slug); ?>">Font Family</label>
                            <select id="wmo_typography_family_<?php echo esc_attr($unique_slug); ?>"
                                    name="wmo_menu_typography[<?php echo esc_attr($sanitized_slug); ?>][font_family]"
                                    class="wmo-typography-family"
                                    data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                                <option value="">Default</option>
                                <option value="Arial, sans-serif" <?php selected($font_family, 'Arial, sans-serif'); ?>>Arial</option>
                                <option value="Helvetica, sans-serif" <?php selected($font_family, 'Helvetica, sans-serif'); ?>>Helvetica</option>
                                <option value="Georgia, serif" <?php selected($font_family, 'Georgia, serif'); ?>>Georgia</option>
                                <option value="Times New Roman, serif" <?php selected($font_family, 'Times New Roman, serif'); ?>>Times New Roman</option>
                                <option value="Verdana, sans-serif" <?php selected($font_family, 'Verdana, sans-serif'); ?>>Verdana</option>
                                <option value="Trebuchet MS, sans-serif" <?php selected($font_family, 'Trebuchet MS, sans-serif'); ?>>Trebuchet MS</option>
                                <option value="Impact, sans-serif" <?php selected($font_family, 'Impact, sans-serif'); ?>>Impact</option>
                                <option value="Comic Sans MS, cursive" <?php selected($font_family, 'Comic Sans MS, cursive'); ?>>Comic Sans MS</option>
                                <option value="Courier New, monospace" <?php selected($font_family, 'Courier New, monospace'); ?>>Courier New</option>
                                <option value="Lucida Console, monospace" <?php selected($font_family, 'Lucida Console, monospace'); ?>>Lucida Console</option>
                            </select>
                        </div>
                        
                        <div class="wmo-typography-field">
                            <label for="wmo_typography_size_<?php echo esc_attr($unique_slug); ?>">Font Size</label>
                            <select id="wmo_typography_size_<?php echo esc_attr($unique_slug); ?>"
                                    name="wmo_menu_typography[<?php echo esc_attr($sanitized_slug); ?>][font_size]"
                                    class="wmo-typography-size"
                                    data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                                <option value="">Default</option>
                                <option value="10px" <?php selected($font_size, '10px'); ?>>10px</option>
                                <option value="11px" <?php selected($font_size, '11px'); ?>>11px</option>
                                <option value="12px" <?php selected($font_size, '12px'); ?>>12px</option>
                                <option value="13px" <?php selected($font_size, '13px'); ?>>13px</option>
                                <option value="14px" <?php selected($font_size, '14px'); ?>>14px</option>
                                <option value="15px" <?php selected($font_size, '15px'); ?>>15px</option>
                                <option value="16px" <?php selected($font_size, '16px'); ?>>16px</option>
                                <option value="18px" <?php selected($font_size, '18px'); ?>>18px</option>
                                <option value="20px" <?php selected($font_size, '20px'); ?>>20px</option>
                                <option value="24px" <?php selected($font_size, '24px'); ?>>24px</option>
                            </select>
                        </div>
                        
                        <div class="wmo-typography-field">
                            <label for="wmo_typography_weight_<?php echo esc_attr($unique_slug); ?>">Font Weight</label>
                            <select id="wmo_typography_weight_<?php echo esc_attr($unique_slug); ?>"
                                    name="wmo_menu_typography[<?php echo esc_attr($sanitized_slug); ?>][font_weight]"
                                    class="wmo-typography-weight"
                                    data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                                <option value="">Default</option>
                                <option value="100" <?php selected($font_weight, '100'); ?>>100 (Thin)</option>
                                <option value="200" <?php selected($font_weight, '200'); ?>>200 (Extra Light)</option>
                                <option value="300" <?php selected($font_weight, '300'); ?>>300 (Light)</option>
                                <option value="400" <?php selected($font_weight, '400'); ?>>400 (Normal)</option>
                                <option value="500" <?php selected($font_weight, '500'); ?>>500 (Medium)</option>
                                <option value="600" <?php selected($font_weight, '600'); ?>>600 (Semi Bold)</option>
                                <option value="700" <?php selected($font_weight, '700'); ?>>700 (Bold)</option>
                                <option value="800" <?php selected($font_weight, '800'); ?>>800 (Extra Bold)</option>
                                <option value="900" <?php selected($font_weight, '900'); ?>>900 (Black)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Typography Preview -->
                    <div class="wmo-typography-preview" style="<?php echo $typography_enabled ? '' : 'display: none;'; ?>">
                        <span class="wmo-typography-sample" 
                              data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                              style="font-family: <?php echo esc_attr($font_family); ?>; font-size: <?php echo esc_attr($font_size); ?>; font-weight: <?php echo esc_attr($font_weight); ?>;">
                            <?php echo esc_html($title); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Icon Section -->
                <div class="icon-section" data-original-slug="<?php echo esc_attr($sanitized_slug); ?>" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd;">
                    <label>
                        <input type="checkbox" class="enable-custom-icon" data-menu-slug="<?php echo esc_attr($unique_slug); ?>" data-original-slug="<?php echo esc_attr($sanitized_slug); ?>">
                        <strong>Custom Icon</strong>
                    </label>
                    
                    <div class="icon-settings" style="display: none; margin-top: 10px;">
                        <!-- Icon type selector -->
                        <div style="margin-bottom: 10px;">
                            <label>Icon Type:</label>
                            <select class="icon-type-selector" data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                                <option value="emoji">Emoji</option>
                                <option value="dashicon">Dashicon</option>
                            </select>
                        </div>
                        
                        <!-- Search bar -->
                        <div class="icon-search" style="margin-bottom: 10px;">
                            <input type="text" class="icon-search-input" placeholder="Search icons..." style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
                        </div>
                        
                        <!-- Emoji picker -->
                        <div class="emoji-picker" style="display: block;">
                            <div class="icon-picker-grid">
                                <!-- Common -->
                                <div class="emoji-category" data-category="common">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Common</div>
                                    <span class="emoji-option" data-emoji="🏠" data-category="common">🏠</span>
                                    <span class="emoji-option" data-emoji="📊" data-category="common">📊</span>
                                    <span class="emoji-option" data-emoji="📝" data-category="common">📝</span>
                                    <span class="emoji-option" data-emoji="📁" data-category="common">📁</span>
                                    <span class="emoji-option" data-emoji="🎨" data-category="common">🎨</span>
                                    <span class="emoji-option" data-emoji="🔧" data-category="common">🔧</span>
                                    <span class="emoji-option" data-emoji="⚙️" data-category="common">⚙️</span>
                                    <span class="emoji-option" data-emoji="👥" data-category="common">👥</span>
                                    <span class="emoji-option" data-emoji="📧" data-category="common">📧</span>
                                    <span class="emoji-option" data-emoji="💬" data-category="common">💬</span>
                                </div>
                                
                                <!-- Business -->
                                <div class="emoji-category" data-category="business">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Business</div>
                                    <span class="emoji-option" data-emoji="💼" data-category="business">💼</span>
                                    <span class="emoji-option" data-emoji="📈" data-category="business">📈</span>
                                    <span class="emoji-option" data-emoji="📉" data-category="business">📉</span>
                                    <span class="emoji-option" data-emoji="💰" data-category="business">💰</span>
                                    <span class="emoji-option" data-emoji="💳" data-category="business">💳</span>
                                    <span class="emoji-option" data-emoji="🏢" data-category="business">🏢</span>
                                    <span class="emoji-option" data-emoji="📋" data-category="business">📋</span>
                                    <span class="emoji-option" data-emoji="✅" data-category="business">✅</span>
                                    <span class="emoji-option" data-emoji="📌" data-category="business">📌</span>
                                    <span class="emoji-option" data-emoji="🎯" data-category="business">🎯</span>
                                </div>
                                
                                <!-- Tech -->
                                <div class="emoji-category" data-category="tech">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Tech</div>
                                    <span class="emoji-option" data-emoji="💻" data-category="tech">💻</span>
                                    <span class="emoji-option" data-emoji="🖥️" data-category="tech">🖥️</span>
                                    <span class="emoji-option" data-emoji="📱" data-category="tech">📱</span>
                                    <span class="emoji-option" data-emoji="⌨️" data-category="tech">⌨️</span>
                                    <span class="emoji-option" data-emoji="🖱️" data-category="tech">🖱️</span>
                                    <span class="emoji-option" data-emoji="💾" data-category="tech">💾</span>
                                    <span class="emoji-option" data-emoji="💿" data-category="tech">💿</span>
                                    <span class="emoji-option" data-emoji="🔌" data-category="tech">🔌</span>
                                    <span class="emoji-option" data-emoji="🔋" data-category="tech">🔋</span>
                                    <span class="emoji-option" data-emoji="📡" data-category="tech">📡</span>
                                </div>
                                
                                <!-- Creative -->
                                <div class="emoji-category" data-category="creative">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Creative</div>
                                    <span class="emoji-option" data-emoji="✏️" data-category="creative">✏️</span>
                                    <span class="emoji-option" data-emoji="🖌️" data-category="creative">🖌️</span>
                                    <span class="emoji-option" data-emoji="🎭" data-category="creative">🎭</span>
                                    <span class="emoji-option" data-emoji="🎪" data-category="creative">🎪</span>
                                    <span class="emoji-option" data-emoji="🎬" data-category="creative">🎬</span>
                                    <span class="emoji-option" data-emoji="🎤" data-category="creative">🎤</span>
                                    <span class="emoji-option" data-emoji="🎧" data-category="creative">🎧</span>
                                    <span class="emoji-option" data-emoji="🎵" data-category="creative">🎵</span>
                                    <span class="emoji-option" data-emoji="🎸" data-category="creative">🎸</span>
                                    <span class="emoji-option" data-emoji="🎹" data-category="creative">🎹</span>
                                </div>
                                
                                <!-- Tools -->
                                <div class="emoji-category" data-category="tools">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Tools</div>
                                    <span class="emoji-option" data-emoji="🔨" data-category="tools">🔨</span>
                                    <span class="emoji-option" data-emoji="🔩" data-category="tools">🔩</span>
                                    <span class="emoji-option" data-emoji="⚒️" data-category="tools">⚒️</span>
                                    <span class="emoji-option" data-emoji="🛠️" data-category="tools">🛠️</span>
                                    <span class="emoji-option" data-emoji="🔐" data-category="tools">🔐</span>
                                    <span class="emoji-option" data-emoji="🔓" data-category="tools">🔓</span>
                                    <span class="emoji-option" data-emoji="🔑" data-category="tools">🔑</span>
                                    <span class="emoji-option" data-emoji="🗝️" data-category="tools">🗝️</span>
                                    <span class="emoji-option" data-emoji="🔍" data-category="tools">🔍</span>
                                    <span class="emoji-option" data-emoji="🔎" data-category="tools">🔎</span>
                                </div>
                                
                                <!-- Nature -->
                                <div class="emoji-category" data-category="nature">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Nature</div>
                                    <span class="emoji-option" data-emoji="🌟" data-category="nature">🌟</span>
                                    <span class="emoji-option" data-emoji="⭐" data-category="nature">⭐</span>
                                    <span class="emoji-option" data-emoji="✨" data-category="nature">✨</span>
                                    <span class="emoji-option" data-emoji="⚡" data-category="nature">⚡</span>
                                    <span class="emoji-option" data-emoji="🔥" data-category="nature">🔥</span>
                                    <span class="emoji-option" data-emoji="💧" data-category="nature">💧</span>
                                    <span class="emoji-option" data-emoji="🌊" data-category="nature">🌊</span>
                                    <span class="emoji-option" data-emoji="🍃" data-category="nature">🍃</span>
                                    <span class="emoji-option" data-emoji="🌿" data-category="nature">🌿</span>
                                    <span class="emoji-option" data-emoji="🌳" data-category="nature">🌳</span>
                                </div>
                                
                                <!-- Arrows -->
                                <div class="emoji-category" data-category="arrows">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Arrows</div>
                                    <span class="emoji-option" data-emoji="⬆️" data-category="arrows">⬆️</span>
                                    <span class="emoji-option" data-emoji="➡️" data-category="arrows">➡️</span>
                                    <span class="emoji-option" data-emoji="⬇️" data-category="arrows">⬇️</span>
                                    <span class="emoji-option" data-emoji="⬅️" data-category="arrows">⬅️</span>
                                    <span class="emoji-option" data-emoji="↗️" data-category="arrows">↗️</span>
                                    <span class="emoji-option" data-emoji="↘️" data-category="arrows">↘️</span>
                                    <span class="emoji-option" data-emoji="↙️" data-category="arrows">↙️</span>
                                    <span class="emoji-option" data-emoji="↖️" data-category="arrows">↖️</span>
                                    <span class="emoji-option" data-emoji="↔️" data-category="arrows">↔️</span>
                                    <span class="emoji-option" data-emoji="↕️" data-category="arrows">↕️</span>
                                </div>
                            </div>
                            <input type="hidden" class="selected-emoji" value="">
                        </div>
                        
                        <!-- Dashicon picker -->
                        <div class="dashicon-picker" style="display: none;">
                            <div class="icon-picker-grid">
                                <!-- Admin Menu -->
                                <div class="dashicon-category" data-category="admin">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Admin Menu</div>
                                    <span class="dashicon-option" data-dashicon="dashicons-dashboard" data-category="admin">
                                        <span class="dashicons dashicons-dashboard"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-post" data-category="admin">
                                        <span class="dashicons dashicons-admin-post"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-media" data-category="admin">
                                        <span class="dashicons dashicons-admin-media"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-links" data-category="admin">
                                        <span class="dashicons dashicons-admin-links"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-page" data-category="admin">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-comments" data-category="admin">
                                        <span class="dashicons dashicons-admin-comments"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-appearance" data-category="admin">
                                        <span class="dashicons dashicons-admin-appearance"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-plugins" data-category="admin">
                                        <span class="dashicons dashicons-admin-plugins"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-users" data-category="admin">
                                        <span class="dashicons dashicons-admin-users"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-tools" data-category="admin">
                                        <span class="dashicons dashicons-admin-tools"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-settings" data-category="admin">
                                        <span class="dashicons dashicons-admin-settings"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-network" data-category="admin">
                                        <span class="dashicons dashicons-admin-network"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-home" data-category="admin">
                                        <span class="dashicons dashicons-admin-home"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-generic" data-category="admin">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-collapse" data-category="admin">
                                        <span class="dashicons dashicons-admin-collapse"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-admin-customizer" data-category="admin">
                                        <span class="dashicons dashicons-admin-customizer"></span>
                                    </span>
                                </div>
                                
                                <!-- Common -->
                                <div class="dashicon-category" data-category="common">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Common</div>
                                    <span class="dashicon-option" data-dashicon="dashicons-filter" data-category="common">
                                        <span class="dashicons dashicons-filter"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-analytics" data-category="common">
                                        <span class="dashicons dashicons-analytics"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-chart-pie" data-category="common">
                                        <span class="dashicons dashicons-chart-pie"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-chart-bar" data-category="common">
                                        <span class="dashicons dashicons-chart-bar"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-chart-line" data-category="common">
                                        <span class="dashicons dashicons-chart-line"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-chart-area" data-category="common">
                                        <span class="dashicons dashicons-chart-area"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-groups" data-category="common">
                                        <span class="dashicons dashicons-groups"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-businessman" data-category="common">
                                        <span class="dashicons dashicons-businessman"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-portfolio" data-category="common">
                                        <span class="dashicons dashicons-portfolio"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-book" data-category="common">
                                        <span class="dashicons dashicons-book"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-book-alt" data-category="common">
                                        <span class="dashicons dashicons-book-alt"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-download" data-category="common">
                                        <span class="dashicons dashicons-download"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-upload" data-category="common">
                                        <span class="dashicons dashicons-upload"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-backup" data-category="common">
                                        <span class="dashicons dashicons-backup"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-clock" data-category="common">
                                        <span class="dashicons dashicons-clock"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-lightbulb" data-category="common">
                                        <span class="dashicons dashicons-lightbulb"></span>
                                    </span>
                                </div>
                                
                                <!-- Tech -->
                                <div class="dashicon-category" data-category="tech">
                                    <div style="font-weight: bold; margin-bottom: 5px; color: #666;">Tech</div>
                                    <span class="dashicon-option" data-dashicon="dashicons-microphone" data-category="tech">
                                        <span class="dashicons dashicons-microphone"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-desktop" data-category="tech">
                                        <span class="dashicons dashicons-desktop"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-laptop" data-category="tech">
                                        <span class="dashicons dashicons-laptop"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-tablet" data-category="tech">
                                        <span class="dashicons dashicons-tablet"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-smartphone" data-category="tech">
                                        <span class="dashicons dashicons-smartphone"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-phone" data-category="tech">
                                        <span class="dashicons dashicons-phone"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-store" data-category="tech">
                                        <span class="dashicons dashicons-store"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-album" data-category="tech">
                                        <span class="dashicons dashicons-album"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-camera" data-category="tech">
                                        <span class="dashicons dashicons-camera"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-camera-alt" data-category="tech">
                                        <span class="dashicons dashicons-camera-alt"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-video-alt" data-category="tech">
                                        <span class="dashicons dashicons-video-alt"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-video-alt2" data-category="tech">
                                        <span class="dashicons dashicons-video-alt2"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-vault" data-category="tech">
                                        <span class="dashicons dashicons-vault"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-shield" data-category="tech">
                                        <span class="dashicons dashicons-shield"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-shield-alt" data-category="tech">
                                        <span class="dashicons dashicons-shield-alt"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-lock" data-category="tech">
                                        <span class="dashicons dashicons-lock"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-unlock" data-category="tech">
                                        <span class="dashicons dashicons-unlock"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-key" data-category="tech">
                                        <span class="dashicons dashicons-key"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-cloud" data-category="tech">
                                        <span class="dashicons dashicons-cloud"></span>
                                    </span>
                                    <span class="dashicon-option" data-dashicon="dashicons-database" data-category="tech">
                                        <span class="dashicons dashicons-database"></span>
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" class="selected-dashicon" value="">
                        </div>
                        
                        <!-- Preview -->
                        <div class="icon-preview" style="margin-top: 10px; padding: 10px; background: #23282d; color: white; border-radius: 4px;">
                            Preview: <span class="preview-icon"></span> <span class="menu-name"><?php echo esc_html($title); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Badge Section -->
            <div class="wmo-badge-wrapper" data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                <div class="wmo-badge-toggle">
                    <label>
                        <input type="checkbox" 
                               name="wmo_menu_badges[<?php echo esc_attr($sanitized_slug); ?>][enabled]" 
                               value="1"
                               class="wmo-badge-enable"
                               data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                               <?php checked($badge_enabled); ?> />
                        <span class="wmo-badge-label">Enable Badge</span>
                    </label>
                </div>
                
                <div class="wmo-badge-controls" style="<?php echo $badge_enabled ? '' : 'display: none;'; ?>">
                    <div class="wmo-badge-field">
                        <label for="wmo_badge_text_<?php echo esc_attr($unique_slug); ?>">Badge Text</label>
                        <input type="text"
                               id="wmo_badge_text_<?php echo esc_attr($unique_slug); ?>"
                               name="wmo_menu_badges[<?php echo esc_attr($sanitized_slug); ?>][text]"
                               value="<?php echo esc_attr($badge_text); ?>"
                               class="wmo-badge-text"
                               data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                               placeholder="e.g., New, 5, Beta"
                               maxlength="10" />
                    </div>
                    
                    <div class="wmo-badge-colors">
                        <div class="wmo-badge-color-field">
                            <label for="wmo_badge_color_<?php echo esc_attr($unique_slug); ?>">Text Color</label>
                            <input type="text"
                                   id="wmo_badge_color_<?php echo esc_attr($unique_slug); ?>"
                                   name="wmo_menu_badges[<?php echo esc_attr($sanitized_slug); ?>][color]"
                                   value="<?php echo esc_attr($badge_color); ?>"
                                   class="wmo-badge-color-picker"
                                   data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>" />
                        </div>
                        
                        <div class="wmo-badge-color-field">
                            <label for="wmo_badge_bg_<?php echo esc_attr($unique_slug); ?>">Background</label>
                            <input type="text"
                                   id="wmo_badge_bg_<?php echo esc_attr($unique_slug); ?>"
                                   name="wmo_menu_badges[<?php echo esc_attr($sanitized_slug); ?>][background]"
                                   value="<?php echo esc_attr($badge_background); ?>"
                                   class="wmo-badge-bg-picker"
                                   data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>" />
                        </div>
                    </div>
                    
                    <!-- Live Preview -->
                    <div class="wmo-badge-preview">
                        <span class="wmo-badge-sample" 
                              data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                              style="background-color: <?php echo esc_attr($badge_background); ?>; color: <?php echo esc_attr($badge_color); ?>;">
                            <?php echo esc_html($badge_text ?: 'Preview'); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Deactivate Submenu Section -->
            <div class="wmo-deactivate-wrapper" data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>">
                <div class="wmo-deactivate-toggle">
                    <label title="Warning: Adds 'Deactivate' link on hover—use cautiously. This feature allows quick plugin deactivation from the admin menu.">
                        <input type="checkbox" 
                               class="wmo-deactivate-enable"
                               data-menu-slug="<?php echo esc_attr($sanitized_slug); ?>"
                               <?php checked(wmo_get_deactivate_setting($sanitized_slug)); ?> />
                        <span class="wmo-deactivate-label">Add Quick Deactivate Submenu</span>
                    </label>
                    <p class="wmo-deactivate-warning" style="margin-top: 5px; color: #d63638; font-size: 12px; font-style: italic;">
                        ⚠️ Warning: This will add a "Deactivate" option to the submenu for quick plugin management. Use cautiously as this action cannot be undone.
                    </p>
                    <p class="wmo-deactivate-note" style="margin-top: 3px; color: #666; font-size: 11px; font-style: italic;">
                        💡 Tip: The deactivation requires confirmation and proper permissions.
                    </p>
                </div>
            </div>
        </div>
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

// Security validation helper functions
function wmo_validate_ajax_request() {
    // Check nonce
    if (!check_ajax_referer('wmo_ajax_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Security check failed']);
        wp_die();
    }
    
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Insufficient permissions']);
        wp_die();
    }
    
    return true;
}

/**
 * Get deactivate setting for a specific menu item.
 *
 * @param string $menu_slug The menu slug.
 * @return bool Whether deactivate submenu is enabled.
 */
function wmo_get_deactivate_setting($menu_slug) {
    $deactivate_settings = wmo_get_settings('deactivate_submenus');
    return isset($deactivate_settings[$menu_slug]) ? (bool) $deactivate_settings[$menu_slug] : false;
}

/**
 * Update deactivate setting for a specific menu item.
 *
 * @param string $menu_slug The menu slug.
 * @param bool $enabled Whether to enable deactivate submenu.
 * @return bool Success status.
 */
function wmo_update_deactivate_setting($menu_slug, $enabled) {
    $deactivate_settings = wmo_get_settings('deactivate_submenus');
    $deactivate_settings[$menu_slug] = $enabled;
    return wmo_update_settings('deactivate_submenus', $deactivate_settings);
}

/**
 * Get the deactivate URL with proper nonce for a menu item.
 *
 * @param string $menu_slug The menu slug.
 * @return string The deactivate URL with nonce.
 */
function wmo_get_deactivate_url($menu_slug) {
    return wp_nonce_url(
        admin_url('admin.php?page=wmo_deactivate_plugin&deactivate=1'),
        'wmo_deactivate_plugin',
        'nonce'
    );
}

/**
 * Callback function for deactivate submenu action.
 * This is a fallback in case the admin_init hook doesn't catch the action.
 */
function wmo_deactivate_callback() {
    // Security check - ensure user can activate/deactivate plugins
    if (!current_user_can('activate_plugins')) {
        error_log('WMO: Deactivation attempt blocked - insufficient permissions for user: ' . wp_get_current_user()->user_login);
        wp_die('Sorry, you cannot deactivate plugins.');
    }
    
    // Check if deactivation is requested with valid nonce and parameters
    if (isset($_GET['wmo_action']) && $_GET['wmo_action'] === 'deactivate_plugin' && 
        isset($_GET['deactivate']) && $_GET['deactivate'] === '1' && 
        isset($_GET['plugin']) && isset($_GET['nonce']) && 
        wp_verify_nonce($_GET['nonce'], 'wmo_deactivate_plugin')) {
        
        $plugin_file = sanitize_text_field($_GET['plugin']);
        
        // Validate the plugin file exists and is active
        if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file) || !is_plugin_active($plugin_file)) {
            error_log('WMO: Invalid plugin file or plugin not active: ' . $plugin_file);
            wp_die('Invalid plugin or plugin not active.');
        }
        
        // Prevent deactivation of our own plugin through this interface
        if ($plugin_file === 'wp-menu-organize/wp-menu-organize.php') {
            error_log('WMO: Attempt to deactivate wp-menu-organize blocked');
            wp_die('This plugin cannot deactivate itself through this interface. Please use the standard WordPress plugin management.');
        }
        
        // Prevent deactivation of critical plugins
        $critical_plugins = array(
            'akismet/akismet.php',
            'hello-dolly/hello.php'
        );
        
        if (in_array($plugin_file, $critical_plugins)) {
            error_log('WMO: Attempt to deactivate critical plugin blocked: ' . $plugin_file);
            wp_die('Critical plugins cannot be deactivated through this interface. Please use the standard WordPress plugin management.');
        }
        
        // Log the deactivation attempt
        error_log('WMO: Plugin deactivation initiated by user: ' . wp_get_current_user()->user_login . ' for plugin: ' . $plugin_file);
        
        // Deactivate the specified plugin
        deactivate_plugins($plugin_file);
        
        // Redirect to plugins page with deactivate parameter and plugin name
        wp_redirect(admin_url('plugins.php?deactivate=true&plugin=' . urlencode($plugin_file)));
        exit;
    } else {
        // Invalid request
        echo 'Invalid request.';
        error_log('WMO: Invalid deactivation request - missing parameters or invalid nonce');
        error_log('WMO: GET parameters: ' . print_r($_GET, true));
    }
}

// Helper for color validation
function wmo_validate_color($color) {
    if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
        return $color;
    }
    return false;
}

// Helper for menu ID validation
function wmo_validate_menu_id($menu_id) {
    // Allow alphanumeric, dashes, underscores, and periods (for unique slugs)
    return preg_match('/^[a-zA-Z0-9_.-]+$/', $menu_id) ? $menu_id : false;
}

// Helper for typography validation
function wmo_validate_typography_settings($settings) {
    $allowed_font_families = [
        'Arial, sans-serif',
        'Helvetica, sans-serif',
        'Georgia, serif',
        'Times New Roman, serif',
        'Verdana, sans-serif',
        'Trebuchet MS, sans-serif',
        'Impact, sans-serif',
        'Comic Sans MS, cursive',
        'Courier New, monospace',
        'Lucida Console, monospace'
    ];
    
    $allowed_font_sizes = [
        '10px', '11px', '12px', '13px', '14px', '15px', '16px', '18px', '20px', '24px'
    ];
    
    $allowed_font_weights = [
        'normal', 'bold', '100', '200', '300', '400', '500', '600', '700', '800', '900'
    ];
    
    $allowed_text_transforms = [
        'none', 'capitalize', 'uppercase', 'lowercase'
    ];
    
    $validated = [];
    
    if (isset($settings['font_family']) && in_array($settings['font_family'], $allowed_font_families)) {
        $validated['font_family'] = $settings['font_family'];
    }
    
    if (isset($settings['font_size']) && in_array($settings['font_size'], $allowed_font_sizes)) {
        $validated['font_size'] = $settings['font_size'];
    }
    
    if (isset($settings['font_weight']) && in_array($settings['font_weight'], $allowed_font_weights)) {
        $validated['font_weight'] = $settings['font_weight'];
    }
    
    if (isset($settings['text_transform']) && in_array($settings['text_transform'], $allowed_text_transforms)) {
        $validated['text_transform'] = $settings['text_transform'];
    }
    
    if (isset($settings['letter_spacing'])) {
        $letter_spacing = sanitize_text_field($settings['letter_spacing']);
        if (preg_match('/^-?\d+(\.\d+)?(px|em|rem)$/', $letter_spacing)) {
            $validated['letter_spacing'] = $letter_spacing;
        }
    }
    
    return $validated;
}

// Helper for badge validation
function wmo_validate_badge_settings($settings) {
    $validated = [];
    
    if (isset($settings['text'])) {
        $validated['text'] = sanitize_text_field($settings['text']);
    }
    
    if (isset($settings['color']) && wmo_validate_color($settings['color'])) {
        $validated['color'] = $settings['color'];
    }
    
    if (isset($settings['background']) && wmo_validate_color($settings['background'])) {
        $validated['background'] = $settings['background'];
    }
    
    if (isset($settings['enabled'])) {
        $validated['enabled'] = (bool)$settings['enabled'];
    }
    
    return $validated;
}

// CSS sanitization function to prevent XSS attacks
function wmo_sanitize_css($css) {
    // Remove any JavaScript
    $css = preg_replace('#<script[^>]*>.*?</script>#is', '', $css);
    
    // Remove any @import statements (security risk)
    $css = preg_replace('/@import[^;]+;/i', '', $css);
    
    // Remove expressions (IE specific XSS vector)
    $css = preg_replace('/expression\s*\(/i', '', $css);
    
    // Remove javascript: protocol
    $css = preg_replace('/javascript\s*:/i', '', $css);
    
    // Remove data: URIs that could contain scripts
    $css = preg_replace('/data:[^;]+;base64[^;)]+/i', '', $css);
    
    // Remove any HTML tags
    $css = wp_strip_all_tags($css);
    
    // Remove any remaining potentially dangerous patterns
    $dangerous_patterns = array(
        '/url\s*\(\s*["\']?\s*javascript:/i',
        '/url\s*\(\s*["\']?\s*data:/i',
        '/behavior\s*:/i',
        '/-moz-binding\s*:/i',
        '/<[^>]*>/i'
    );
    
    foreach ($dangerous_patterns as $pattern) {
        $css = preg_replace($pattern, '', $css);
    }
    
    return trim($css);
}

// Secure CSS output function
function wmo_output_custom_css() {
    $settings = wmo_get_settings();
    $custom_css = isset($settings['custom_css']) ? $settings['custom_css'] : '';
    
    if (!empty($custom_css)) {
        // Use WordPress's built-in method for adding inline styles
        wp_add_inline_style('wp-menu-organize-style', $custom_css);
    }
}

// Template loader system to reduce memory usage
function wmo_get_menu_template($template_name) {
    // Define templates on-demand, not in memory
    $templates = array(
        'minimal' => array('dashboard', 'posts', 'pages', 'media', 'settings'),
        'blogger' => array('dashboard', 'posts', 'media', 'comments', 'pages', 'appearance', 'plugins', 'users', 'settings'),
        'developer' => array('dashboard', 'posts', 'pages', 'media', 'plugins', 'appearance', 'tools', 'settings'),
        'ecommerce' => array('dashboard', 'products', 'orders', 'customers', 'reports', 'marketing', 'settings'),
        'content-focused' => array('dashboard', 'posts', 'pages', 'media', 'comments', 'appearance', 'plugins', 'users', 'tools', 'settings'),
        'default' => array()
    );
    
    return isset($templates[$template_name]) ? $templates[$template_name] : array();
}

// Load template configurations from file instead of memory
function wmo_load_template_config($template_name) {
    $template_file = plugin_dir_path(__FILE__) . '../templates/' . $template_name . '.json';
    
    if (file_exists($template_file)) {
        $json = file_get_contents($template_file);
        return json_decode($json, true);
    }
    
    return false;
}

// Export/Import Settings functionality
function wmo_export_settings() {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    $settings = wmo_get_settings();
    
    // Add metadata
    $export_data = array(
        'version' => '2.0.0',
        'timestamp' => current_time('c'),
        'site_url' => get_site_url(),
        'settings' => $settings
    );
    
    // Encode and compress
    $json_data = json_encode($export_data, JSON_PRETTY_PRINT);
    $compressed = base64_encode($json_data);
    
    return $compressed;
}

function wmo_import_settings($data) {
    if (!current_user_can('manage_options')) {
        return array('success' => false, 'message' => 'Insufficient permissions');
    }
    
    // Decode the data
    $json_data = base64_decode($data);
    if ($json_data === false) {
        return array('success' => false, 'message' => 'Invalid import data format');
    }
    
    // Parse JSON
    $import_data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return array('success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Validate structure
    if (!isset($import_data['settings']) || !is_array($import_data['settings'])) {
        return array('success' => false, 'message' => 'Invalid settings structure');
    }
    
    // Version check
    if (isset($import_data['version']) && version_compare($import_data['version'], '1.0.0', '<')) {
        return array('success' => false, 'message' => 'Import data is from an older version and may not be compatible');
    }
    
    // Backup current settings
    $backup = wmo_get_settings();
    update_option('wmo_settings_backup_' . time(), $backup);
    
    // Import new settings
    $result = update_option('wmo_settings', $import_data['settings']);
    
    if ($result !== false) {
        return array(
            'success' => true, 
            'message' => 'Settings imported successfully',
            'imported_at' => current_time('c'),
            'backup_created' => true
        );
    } else {
        return array('success' => false, 'message' => 'Failed to save imported settings');
    }
}

function wmo_get_import_preview($data) {
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    // Decode the data
    $json_data = base64_decode($data);
    if ($json_data === false) {
        return false;
    }
    
    // Parse JSON
    $import_data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }
    
    if (!isset($import_data['settings']) || !is_array($import_data['settings'])) {
        return false;
    }
    
    $preview = array(
        'version' => isset($import_data['version']) ? $import_data['version'] : 'Unknown',
        'timestamp' => isset($import_data['timestamp']) ? $import_data['timestamp'] : 'Unknown',
        'site_url' => isset($import_data['site_url']) ? $import_data['site_url'] : 'Unknown',
        'summary' => array()
    );
    
    // Generate summary
    $settings = $import_data['settings'];
    
    if (!empty($settings['colors'])) {
        $preview['summary'][] = count($settings['colors']) . ' menu colors';
    }
    
    if (!empty($settings['badges'])) {
        $preview['summary'][] = count($settings['badges']) . ' badges';
    }
    
    if (!empty($settings['typography'])) {
        $preview['summary'][] = count($settings['typography']) . ' typography settings';
    }
    
    if (!empty($settings['icons'])) {
        $preview['summary'][] = count($settings['icons']) . ' custom icons';
    }
    
    if (!empty($settings['custom_css'])) {
        $preview['summary'][] = 'Custom CSS (' . strlen($settings['custom_css']) . ' characters)';
    }
    
    if (!empty($settings['theme_preference'])) {
        $preview['summary'][] = 'Theme preference: ' . $settings['theme_preference'];
    }
    
    return $preview;
}

// Asset URL helper for minified files with cache busting
function wmo_get_asset_url($filename) {
    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    $minified = plugin_dir_path(__FILE__) . '../assets/' . $extension . '/' . $name . $suffix . '.' . $extension;
    $regular = plugin_dir_path(__FILE__) . '../assets/' . $extension . '/' . $filename;
    
    // Use minified if it exists and SCRIPT_DEBUG is not enabled, otherwise use regular
    if (!defined('SCRIPT_DEBUG') && file_exists($minified)) {
        $file_path = $minified;
        $file_url = plugin_dir_url(__FILE__) . '../assets/' . $extension . '/' . $name . $suffix . '.' . $extension;
    } else {
        $file_path = $regular;
        $file_url = plugin_dir_url(__FILE__) . '../assets/' . $extension . '/' . $filename;
    }
    
    // Add cache busting using file modification time
    if (file_exists($file_path)) {
        $version = filemtime($file_path);
        return add_query_arg('ver', $version, $file_url);
    }
    
    return $file_url;
}
