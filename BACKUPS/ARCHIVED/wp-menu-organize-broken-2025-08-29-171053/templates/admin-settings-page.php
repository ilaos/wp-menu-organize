<div class="wrap">
    <h1>WP Menu Organize Settings</h1>
    <div class="wmo-layout">
        <div class="wmo-main-content">
            <form method="post" action="options.php">
                <?php
                settings_fields('wmo_settings_group');
                do_settings_sections('wmo_settings_group');
                ?>

                <!-- Debug info for toggle functionality -->
                <div class="wmo-debug-info" style="display: none;">
                    <h3>Debug Information</h3>
                    <p><strong>Current page:</strong> <?php echo esc_html($_GET['page'] ?? 'unknown'); ?></p>
                    <p><strong>Hook suffix:</strong> <span id="wmo-hook-suffix">Loading...</span></p>
                    <p><strong>Menu items found:</strong> <span id="wmo-menu-count">Loading...</span></p>
                    <p><strong>Toggle headers found:</strong> <span id="wmo-toggle-count">Loading...</span></p>
                    <p><strong>Unique slug format:</strong> <code>slug_hash</code> (e.g., dashboard_dc7161be)</p>
                </div>
                
                <div class="wmo-section">
                    <h3>Menu Colors</h3>
                    <p class="description">
                        <em>⚡ Colors are automatically saved when you make changes - no need to click a save button!</em>
                    </p>
                    
                    <!-- Search/Filter Box -->
                    <div style="padding: 20px 0; margin-bottom: 20px;">
                        <input type="text" 
                               id="wmo-menu-search" 
                               placeholder="Search menu items..." 
                               style="width: 100%; max-width: 400px; padding: 10px; font-size: 14px; border: 1px solid #ddd; border-radius: 4px;">
                        <p style="margin-top: 10px; color: #666; font-style: italic;">Type to filter menu items (e.g., 'plugin', 'dashboard', 'posts')</p>
                    </div>
                    
                    <div class="wmo-color-groups">
                        <!-- Column 1 -->
                        <div class="wmo-color-column">
                            <?php
                            // Process core WordPress items that may have submenus
                            global $menu, $submenu;
                            $core_items = ['dashboard', 'posts', 'media', 'pages', 'comments'];
                            $site_mgmt_items = ['appearance', 'plugins', 'users', 'tools', 'settings'];
                            
                            // Find actual menu entries for core items
                            $core_with_submenus = [];
                            $core_without_submenus = [];
                            $site_with_submenus = [];
                            $site_without_submenus = [];
                            
                            if (isset($menu) && is_array($menu)) {
                                foreach ($menu as $item) {
                                    $menu_slug = sanitize_title(strip_tags($item[0]));
                                    if (empty($menu_slug)) continue;
                                    
                                    $menu_title = preg_replace('/\d+/', '', strip_tags($item[0]));
                                    $menu_title = trim($menu_title);
                                    $menu_file = $item[2];
                                    
                                    // Check if this item has submenus
                                    $has_submenus = isset($submenu[$menu_file]) && !empty($submenu[$menu_file]);
                                    
                                    if (in_array($menu_slug, $core_items)) {
                                        if ($has_submenus) {
                                            $core_with_submenus[] = [
                                                'slug' => $menu_slug,
                                                'title' => $menu_title,
                                                'file' => $menu_file,
                                                'submenus' => $submenu[$menu_file]
                                            ];
                                        } else {
                                            $core_without_submenus[] = [
                                                'slug' => $menu_slug,
                                                'title' => $menu_title
                                            ];
                                        }
                                    } elseif (in_array($menu_slug, $site_mgmt_items)) {
                                        if ($has_submenus) {
                                            $site_with_submenus[] = [
                                                'slug' => $menu_slug,
                                                'title' => $menu_title,
                                                'file' => $menu_file,
                                                'submenus' => $submenu[$menu_file]
                                            ];
                                        } else {
                                            $site_without_submenus[] = [
                                                'slug' => $menu_slug,
                                                'title' => $menu_title
                                            ];
                                        }
                                    }
                                }
                            }
                            
                            // Display Core WordPress items with submenus
                            foreach ($core_with_submenus as $parent_item) {
                                echo '<div class="wmo-color-group wmo-parent-menu-group">';
                                echo '<h3><span class="dashicons dashicons-dashboard"></span> ' . esc_html($parent_item['title']) . ' & Submenus</h3>';
                                echo '<div class="wmo-color-items">';
                                
                                // Parent item
                                echo '<div class="wmo-parent-item">';
                                wmo_render_color_picker($menu_colors, $parent_item['slug'], $parent_item['title'] . ' (Parent)');
                                echo '</div>';
                                
                                // Child items
                                if (!empty($parent_item['submenus'])) {
                                    echo '<div class="wmo-submenu-items">';
                                    foreach ($parent_item['submenus'] as $subitem) {
                                        $submenu_slug = sanitize_title(strip_tags($subitem[0]));
                                        $submenu_title = strip_tags($subitem[0]);
                                        wmo_render_color_picker($menu_colors, $submenu_slug, $submenu_title, true);
                                    }
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            // Display remaining core items without submenus (if any)
                            if (!empty($core_without_submenus)) {
                                echo '<div class="wmo-color-group">';
                                echo '<h3><span class="dashicons dashicons-dashboard"></span> Core WordPress (Simple)</h3>';
                                echo '<div class="wmo-color-items">';
                                foreach ($core_without_submenus as $item) {
                                    wmo_render_color_picker($menu_colors, $item['slug'], $item['title']);
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            // Display Site Management items with submenus
                            foreach ($site_with_submenus as $parent_item) {
                                echo '<div class="wmo-color-group wmo-parent-menu-group">';
                                echo '<h3><span class="dashicons dashicons-admin-appearance"></span> ' . esc_html($parent_item['title']) . ' & Submenus</h3>';
                                echo '<div class="wmo-color-items">';
                                
                                // Parent item
                                echo '<div class="wmo-parent-item">';
                                wmo_render_color_picker($menu_colors, $parent_item['slug'], $parent_item['title'] . ' (Parent)');
                                echo '</div>';
                                
                                // Child items
                                if (!empty($parent_item['submenus'])) {
                                    echo '<div class="wmo-submenu-items">';
                                    foreach ($parent_item['submenus'] as $subitem) {
                                        $submenu_slug = sanitize_title(strip_tags($subitem[0]));
                                        $submenu_title = strip_tags($subitem[0]);
                                        wmo_render_color_picker($menu_colors, $submenu_slug, $submenu_title, true);
                                    }
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            // Display remaining site management items without submenus (if any)
                            if (!empty($site_without_submenus)) {
                                echo '<div class="wmo-color-group">';
                                echo '<h3><span class="dashicons dashicons-admin-appearance"></span> Site Management (Simple)</h3>';
                                echo '<div class="wmo-color-items">';
                                foreach ($site_without_submenus as $item) {
                                    wmo_render_color_picker($menu_colors, $item['slug'], $item['title']);
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        
                        <!-- Column 2 -->
                        <div class="wmo-color-column">
                            <?php
                            // Collect plugin menu items and other items with submenus
                            $plugin_items = [];
                            $other_items_with_submenus = [];
                            
                            if (isset($menu) && is_array($menu)) {
                                foreach ($menu as $item) {
                                    $menu_slug = sanitize_title(strip_tags($item[0]));
                                    if (empty($menu_slug)) continue;
                                    
                                    // Skip core WordPress and site management items (already handled in column 1)
                                    if (in_array($menu_slug, array_merge($core_items, $site_mgmt_items))) continue;

                                    $menu_title = preg_replace('/\d+/', '', strip_tags($item[0]));
                                    $menu_title = trim($menu_title);
                                    $menu_file = $item[2];
                                    
                                    // Check if this item has submenus
                                    $has_submenus = isset($submenu[$menu_file]) && !empty($submenu[$menu_file]);
                                    
                                    if ($has_submenus) {
                                        $other_items_with_submenus[] = [
                                            'slug' => $menu_slug,
                                            'title' => $menu_title,
                                            'file' => $menu_file,
                                            'submenus' => $submenu[$menu_file]
                                        ];
                                    } else {
                                        $plugin_items[] = [
                                            'slug' => $menu_slug,
                                            'title' => $menu_title
                                        ];
                                    }
                                }
                            }
                            
                            // Display plugin items without submenus
                            if (!empty($plugin_items)) {
                                echo '<div class="wmo-color-group">';
                                echo '<h3><span class="dashicons dashicons-admin-plugins"></span> Plugin Menu Items</h3>';
                                echo '<div class="wmo-color-items">';
                                foreach ($plugin_items as $item) {
                                    wmo_render_color_picker($menu_colors, $item['slug'], $item['title']);
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            // Display other items with submenus in separate sections
                            foreach ($other_items_with_submenus as $parent_item) {
                                echo '<div class="wmo-color-group wmo-parent-menu-group">';
                                echo '<h3><span class="dashicons dashicons-menu-alt3"></span> ' . esc_html($parent_item['title']) . ' & Submenus</h3>';
                                echo '<div class="wmo-color-items">';
                                
                                // Parent item
                                echo '<div class="wmo-parent-item">';
                                wmo_render_color_picker($menu_colors, $parent_item['slug'], $parent_item['title'] . ' (Parent)');
                                echo '</div>';
                                
                                // Child items
                                if (!empty($parent_item['submenus'])) {
                                    echo '<div class="wmo-submenu-items">';
                                    foreach ($parent_item['submenus'] as $subitem) {
                                        $submenu_slug = sanitize_title(strip_tags($subitem[0]));
                                        $submenu_title = strip_tags($subitem[0]);
                                        wmo_render_color_picker($menu_colors, $submenu_slug, $submenu_title, true);
                                    }
                                    echo '</div>';
                                }
                                
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </form>
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
        gap: 25px;
        align-items: start;
    }

    .wmo-color-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .wmo-color-group {
        background: #fff;
        padding: 18px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: box-shadow 0.2s ease;
    }

    .wmo-color-group:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .wmo-color-group h3 {
        margin-top: 0;
        margin-bottom: 18px;
        color: #23282d;
        font-size: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0073aa;
    }

    .wmo-color-group h3 .dashicons {
        color: #0073aa;
        font-size: 18px;
    }

    .wmo-color-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Compact Card Layout */
    .wmo-menu-item-wrapper {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .wmo-menu-item-wrapper:hover {
        background: #fff;
        border-color: #0073aa;
        box-shadow: 0 2px 8px rgba(0,115,170,0.15);
        transform: translateY(-1px);
    }

    .wmo-menu-item-wrapper.expanded {
        background: #fff;
        border-color: #0073aa;
        box-shadow: 0 4px 12px rgba(0,115,170,0.2);
    }

    /* Compact Header */
    .wmo-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        cursor: pointer;
    }

    .wmo-menu-title {
        font-weight: 600;
        color: #23282d;
        font-size: 14px;
        flex: 1;
        margin-right: 12px;
    }

    .wmo-menu-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .wmo-color-picker-wrapper {
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .wmo-color-picker-wrapper label {
        display: none; /* Hide label in compact mode */
    }

    .wmo-color-picker-wrapper input[type="text"] {
        width: 60px;
        height: 32px;
        border: 2px solid #ddd;
        border-radius: 4px;
        padding: 4px;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .wmo-color-picker-wrapper input[type="text"]:hover {
        border-color: #0073aa;
    }

    .wmo-expand-toggle {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
        border-radius: 3px;
        transition: all 0.2s ease;
        font-size: 16px;
    }

    .wmo-expand-toggle:hover {
        background: #e9ecef;
        color: #0073aa;
    }

    .wmo-expand-toggle.expanded {
        transform: rotate(180deg);
        color: #0073aa;
    }

    /* Compact Status Indicators */
    .wmo-status-indicators {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: 8px;
    }

    .wmo-status-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .wmo-status-typography {
        background: #e3f2fd;
        color: #1976d2;
    }

    .wmo-status-badge-enabled {
        background: #e8f5e8;
        color: #2e7d32;
    }

    /* Expanded Content */
    .wmo-expanded-content {
        display: none; /* Changed from max-height to display:none for better compatibility */
        border-top: 1px solid transparent;
        margin-top: 0;
        padding-top: 12px;
    }

    .wmo-menu-item-wrapper.expanded .wmo-expanded-content {
        display: block; /* Show when expanded */
        border-top-color: #e9ecef;
        margin-top: 12px;
    }

    /* Typography Section in Expanded Mode */
    .wmo-typography-wrapper {
        margin-bottom: 16px;
    }

    .wmo-typography-toggle {
        margin-bottom: 12px;
    }

    .wmo-typography-toggle label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #23282d;
        cursor: pointer;
    }

    .wmo-typography-controls {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 12px;
        margin-top: 8px;
    }

    .wmo-typography-fields {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }

    .wmo-typography-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .wmo-typography-field select {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 13px;
    }

    .wmo-typography-preview {
        margin-top: 8px;
        padding: 8px;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 3px;
        text-align: center;
    }

    /* Badge Section in Expanded Mode */
    .wmo-badge-wrapper {
        margin-bottom: 16px;
    }

    .wmo-badge-toggle {
        margin-bottom: 12px;
    }

    .wmo-badge-toggle label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #23282d;
        cursor: pointer;
    }

    .wmo-badge-controls {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 12px;
        margin-top: 8px;
    }

    .wmo-badge-field {
        margin-bottom: 12px;
    }

    .wmo-badge-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .wmo-badge-field input[type="text"] {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 13px;
    }

    .wmo-badge-colors {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .wmo-badge-color-field input[type="text"] {
        width: 100%;
        height: 32px;
        border: 2px solid #ddd;
        border-radius: 4px;
        padding: 4px;
        cursor: pointer;
    }

    .wmo-badge-preview {
        text-align: center;
        padding: 8px;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 3px;
    }

    .wmo-badge-sample {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Parent menu group styling */
    .wmo-parent-menu-group {
        border-left: 4px solid #0073aa;
    }

    .wmo-parent-menu-group h3 {
        border-bottom-color: #0073aa;
        background: linear-gradient(90deg, rgba(0,115,170,0.05) 0%, transparent 100%);
        margin-left: -18px;
        margin-right: -18px;
        margin-top: -18px;
        padding: 12px 18px;
        border-radius: 6px 6px 0 0;
    }

    /* Parent item styling */
    .wmo-parent-item {
        background: rgba(0,115,170,0.05);
        border-left: 3px solid #0073aa;
    }

    .wmo-parent-item .wmo-menu-title {
        color: #0073aa;
        font-weight: 700;
    }

    /* Submenu items styling */
    .wmo-submenu-items {
        background: #f9f9f9;
        padding: 12px;
        border-radius: 4px;
        border: 1px solid #e5e5e5;
        position: relative;
        margin-top: 8px;
    }

    .wmo-submenu-items::before {
        content: "Child Menu Items";
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        padding-bottom: 4px;
        border-bottom: 1px solid #ddd;
    }

    .wmo-submenu-items .wmo-menu-item-wrapper {
        background: #fff;
        border: 1px solid #e0e0e0;
        margin-bottom: 8px;
    }

    .wmo-submenu-items .wmo-menu-item-wrapper:last-child {
        margin-bottom: 0;
    }

    .wmo-submenu-items .wmo-menu-item-wrapper::before {
        content: "└─";
        position: absolute;
        left: -8px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-family: monospace;
        font-weight: bold;
        font-size: 12px;
    }

    /* Responsive design */
    @media (max-width: 1200px) {
        .wmo-color-groups {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .wmo-color-group {
            padding: 15px;
        }
        
        .wmo-menu-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .wmo-menu-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .wmo-badge-colors {
            grid-template-columns: 1fr;
        }
        
        .wmo-typography-fields {
            grid-template-columns: 1fr;
        }
    }

    /* Expanded Content - More Specific Rules */
    .wmo-menu-item-wrapper .wmo-expanded-content {
        display: none !important; /* Force hide by default */
        border-top: 1px solid transparent;
        margin-top: 0;
        padding-top: 12px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-top: 8px;
    }

    .wmo-menu-item-wrapper.expanded .wmo-expanded-content {
        display: block !important; /* Force show when expanded */
        border-top-color: #e9ecef;
        margin-top: 12px;
    }

    /* Debug styles to make it obvious when expanded */
    .wmo-menu-item-wrapper.expanded {
        background: #fff !important;
        border-color: #0073aa !important;
        box-shadow: 0 4px 12px rgba(0,115,170,0.2) !important;
    }

    .wmo-menu-item-wrapper.expanded .wmo-expand-toggle {
        color: #0073aa !important;
        transform: rotate(180deg) !important;
    }
</style>
