# Add Templates tab to WP Menu Organize

$adminPageFile = "wp-content\plugins\wp-menu-organize\includes\admin-page.php"
$content = Get-Content $adminPageFile

# Add Templates submenu
$content = $content -replace "add_submenu_page\(", @"

        // Add Templates submenu page
        add_submenu_page(
            'wp-menu-organize-settings',
            'Menu Templates',
            'Templates',
            'manage_options',
            'wp-menu-organize-templates',
            array(`$this, 'render_templates_page')
        );

        add_submenu_page("@

# Add Templates page method before the closing brace
$insertIndex = ($content | Select-String "include WMO_PLUGIN_PATH . 'templates/admin-reorder-page.php'").LineNumber
$templatesMethod = @"
    }

    /**
     * Render Templates page
     */
    public function render_templates_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_style(
            'wmo-admin-style',
            WMO_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            '1.0.0'
        );

        wp_enqueue_script(
            'wmo-templates-script',
            WMO_PLUGIN_URL . 'assets/js/templates.js',
            array('jquery', 'wp-color-picker'),
            '1.0.0',
            true
        );

        wp_localize_script('wmo-templates-script', 'wmo_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wmo_ajax_nonce')
        ));

        include WMO_PLUGIN_PATH . 'templates/admin-templates-page.php';
"@

$content[$insertIndex-1] = $templatesMethod

# Save the updated file
$content | Set-Content $adminPageFile -Encoding UTF8

Write-Output "✅ Templates tab added successfully!"
Write-Output "📁 Templates page template created: templates/admin-templates-page.php"
Write-Output "🎨 Templates functionality is now available in its own tab!" 