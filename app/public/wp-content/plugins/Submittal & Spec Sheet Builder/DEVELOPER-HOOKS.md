# Developer Hooks & Filters
## Submittal & Spec Sheet Builder v1.0.0

Complete reference for all WordPress filters and actions available for customization and extension.

---

## Table of Contents

- [Filters](#filters)
  - [Feature Management](#feature-management)
  - [PDF Customization](#pdf-customization)
  - [License Management](#license-management)
  - [External Links](#external-links)
- [Actions](#actions)
  - [Lifecycle Hooks](#lifecycle-hooks)
  - [Admin Hooks](#admin-hooks)
  - [Cron Jobs](#cron-jobs)
- [Template System](#template-system)
- [Common Use Cases](#common-use-cases)

---

## Filters

Filters allow you to modify data before it's used by the plugin.

---

### Feature Management

#### `sfb_features_map`
Modify or add custom Pro features to the registry.

**Since:** 1.0.0

**Parameters:**
- `$features` (array) - Associative array of feature definitions

**Returns:** (array) Modified features array

**Feature Structure:**
```php
[
  'feature_key' => [
    'label'  => 'Feature Name',
    'group'  => 'Core|Automation|Branding|Data|Distribution',
    'pro'    => true|false,
    'desc'   => 'Feature description',
    'since'  => '1.0.0'
  ]
]
```

**Example - Add Custom Feature:**
```php
add_filter('sfb_features_map', function($features) {
    $features['custom_export'] = [
        'label' => 'Custom CSV Export',
        'group' => 'Data',
        'pro'   => true,
        'desc'  => 'Export product data to CSV format',
        'since' => '1.1.0'
    ];
    return $features;
});
```

**Example - Modify Existing Feature:**
```php
add_filter('sfb_features_map', function($features) {
    // Make watermark feature free
    if (isset($features['watermark'])) {
        $features['watermark']['pro'] = false;
    }
    return $features;
});
```

**Example - Add Feature Category:**
```php
add_filter('sfb_features_map', function($features) {
    $features['crm_integration'] = [
        'label' => 'CRM Integration',
        'group' => 'Integration', // Custom group
        'pro'   => true,
        'desc'  => 'Sync submittals to CRM',
        'since' => '1.2.0'
    ];
    return $features;
});
```

**Use Cases:**
- Adding custom Pro features for add-ons
- Creating white-label feature sets
- Modifying feature availability
- Building feature packs

---

#### `sfb_pro_changelog`
Add or modify changelog entries displayed on upgrade page.

**Since:** 1.0.0

**Parameters:**
- `$changelog` (array) - Array of changelog entries

**Returns:** (array) Modified changelog array

**Changelog Entry Structure:**
```php
[
  'version' => '1.0.0',
  'date'    => '2025-01-08',
  'notes'   => ['Feature 1', 'Feature 2', 'Bug fix']
]
```

**Example:**
```php
add_filter('sfb_pro_changelog', function($changelog) {
    $changelog[] = [
        'version' => '1.1.0',
        'date'    => '2025-02-01',
        'notes'   => [
            'Added CSV export',
            'Improved PDF performance',
            'Fixed draft expiry bug'
        ]
    ];
    return $changelog;
});
```

**Use Cases:**
- Add-ons announcing their own updates
- Custom changelog for white-label versions
- Version tracking for internal builds

---

### PDF Customization

#### `sfb_pdf_theme`
Override PDF theme selection before rendering.

**Since:** 1.0.0

**Parameters:**
- `$theme` (string) - Current theme: `engineering`, `architectural`, or `corporate`
- `$brand` (array) - Complete branding settings array
- `$meta` (array) - Project metadata (project name, contractor, etc.)

**Returns:** (string) Theme name

**Example - Force Theme by Project Name:**
```php
add_filter('sfb_pdf_theme', function($theme, $brand, $meta) {
    // Use architectural theme for projects containing "Office"
    if (isset($meta['project']) && strpos($meta['project'], 'Office') !== false) {
        return 'architectural';
    }
    return $theme;
}, 10, 3);
```

**Example - Dynamic Theme Based on Time:**
```php
add_filter('sfb_pdf_theme', function($theme, $brand, $meta) {
    $hour = (int) date('H');
    // Night mode theme for evening hours
    if ($hour >= 18 || $hour < 6) {
        return 'midnight'; // Custom theme
    }
    return $theme;
}, 10, 3);
```

**Use Cases:**
- Project-specific theme selection
- Client-based branding
- A/B testing themes
- Custom theme development

---

#### `sfb_pdf_color`
Modify primary brand color before PDF generation.

**Since:** 1.0.0

**Parameters:**
- `$color` (string) - Hex color code (e.g., `#7c3aed`)
- `$brand` (array) - Complete branding settings
- `$meta` (array) - Project metadata

**Returns:** (string) Hex color code

**Example - Client-Specific Colors:**
```php
add_filter('sfb_pdf_color', function($color, $brand, $meta) {
    // Map contractors to brand colors
    $contractor_colors = [
        'Smith Construction' => '#0ea5e9', // Blue
        'Jones LLC'          => '#10b981', // Green
        'ABC Corp'           => '#f59e0b', // Amber
    ];

    $contractor = $meta['contractor'] ?? '';
    if (isset($contractor_colors[$contractor])) {
        return $contractor_colors[$contractor];
    }

    return $color;
}, 10, 3);
```

**Example - Validation:**
```php
add_filter('sfb_pdf_color', function($color, $brand, $meta) {
    // Ensure color is valid hex
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        return '#111827'; // Default to dark gray
    }
    return $color;
}, 10, 3);
```

**Use Cases:**
- Multi-tenant color schemes
- Dynamic branding per client
- Color validation
- Accessibility adjustments

---

### License Management

#### `sfb_is_pro_active`
Override Pro license activation check.

**Since:** 1.0.0

**Parameters:**
- `$is_active` (bool) - Current activation status
- `$license_data` (array) - License information from options

**Returns:** (bool) True if Pro should be active

**Example - Development Override:**
```php
add_filter('sfb_is_pro_active', function($is_active, $license_data) {
    // Force Pro active on staging
    if (defined('WP_ENV') && WP_ENV === 'staging') {
        return true;
    }
    return $is_active;
}, 10, 2);
```

**Example - Beta Testing:**
```php
add_filter('sfb_is_pro_active', function($is_active, $license_data) {
    // Grant Pro to beta testers
    $beta_emails = ['beta@example.com', 'tester@example.com'];
    $user = wp_get_current_user();

    if (in_array($user->user_email, $beta_emails)) {
        return true;
    }

    return $is_active;
}, 10, 2);
```

**Example - Time-Limited Trial:**
```php
add_filter('sfb_is_pro_active', function($is_active, $license_data) {
    $install_date = get_option('sfb_install_date');
    $trial_days = 14;

    if (!$install_date) {
        update_option('sfb_install_date', time());
        return true;
    }

    $days_since_install = (time() - $install_date) / DAY_IN_SECONDS;

    if ($days_since_install < $trial_days) {
        return true; // Trial period active
    }

    return $is_active;
}, 10, 2);
```

**Use Cases:**
- Development/staging overrides
- Trial period implementation
- Beta testing programs
- White-label license management

---

### External Links

#### `sfb_links`
Customize external link URLs used throughout the plugin.

**Since:** 1.0.0

**Parameters:**
- `$links` (array) - Associative array of link URLs

**Returns:** (array) Modified links array

**Default Links:**
```php
[
  'account'       => 'https://webstuffguylabs.com/account',
  'invoices'      => 'https://webstuffguylabs.com/invoices',
  'docs'          => 'https://docs.example.com',
  'videos'        => 'https://youtube.com/@example',
  'roadmap'       => 'https://webstuffguylabs.com/roadmap',
  'support'       => 'https://webstuffguylabs.com/support',
  'renew'         => 'https://webstuffguylabs.com/renew',
  'pricing'       => 'https://webstuffguylabs.com/pricing',
  'agency'        => 'https://webstuffguylabs.com/agency',
  'single'        => 'https://webstuffguylabs.com/buy'
]
```

**Example - White-Label Links:**
```php
add_filter('sfb_links', function($links) {
    return [
        'account'  => 'https://mycompany.com/account',
        'docs'     => 'https://help.mycompany.com',
        'support'  => 'https://mycompany.com/support',
        'pricing'  => 'https://mycompany.com/pricing',
        // ... other links
    ];
});
```

**Example - Add UTM Tracking:**
```php
add_filter('sfb_links', function($links) {
    $utm = '?utm_source=plugin&utm_medium=admin&utm_campaign=upgrade';

    foreach ($links as $key => $url) {
        $links[$key] = $url . $utm;
    }

    return $links;
});
```

**Use Cases:**
- White-label deployments
- Affiliate tracking
- Custom help documentation
- Localized support resources

---

## Actions

Actions allow you to execute custom code at specific points in the plugin lifecycle.

---

### Lifecycle Hooks

#### `sfb_after_activation`
Fires immediately after plugin activation.

**Since:** 1.0.0

**Parameters:** None

**Example - Welcome Email:**
```php
add_action('sfb_after_activation', function() {
    $admin_email = get_option('admin_email');
    wp_mail(
        $admin_email,
        'Submittal Builder Activated',
        'Your submittal builder is ready to use!'
    );
});
```

**Example - Create Default Page:**
```php
add_action('sfb_after_activation', function() {
    // Check if submittal page exists
    $pages = get_posts([
        'post_type'   => 'page',
        'meta_key'    => '_sfb_page',
        'meta_value'  => '1',
        'numberposts' => 1
    ]);

    if (empty($pages)) {
        // Create new page
        $page_id = wp_insert_post([
            'post_title'   => 'Build Submittal',
            'post_content' => '[submittal_builder]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        ]);

        add_post_meta($page_id, '_sfb_page', '1', true);
    }
});
```

**Use Cases:**
- Creating default pages
- Initial configuration
- Sending notifications
- Setting up demo data

---

#### `sfb_after_deactivation`
Fires immediately after plugin deactivation.

**Since:** 1.0.0

**Parameters:** None

**Example - Log Deactivation:**
```php
add_action('sfb_after_deactivation', function() {
    error_log('Submittal Builder deactivated at ' . date('Y-m-d H:i:s'));
});
```

**Use Cases:**
- Cleanup operations
- Logging
- Notifications

---

### Admin Hooks

#### `sfb_before_settings_save`
Fires before settings are saved (validation hook).

**Since:** 1.0.0

**Parameters:**
- `$settings` (array) - Settings being saved

**Example - Validate Colors:**
```php
add_action('sfb_before_settings_save', function($settings) {
    if (isset($settings['branding']['primary_color'])) {
        $color = $settings['branding']['primary_color'];
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            wp_die('Invalid color format. Please use hex format (#RRGGBB).');
        }
    }
});
```

**Use Cases:**
- Custom validation
- Security checks
- Logging changes

---

#### `sfb_after_settings_save`
Fires after settings are successfully saved.

**Since:** 1.0.0

**Parameters:**
- `$settings` (array) - Settings that were saved

**Example - Cache Clearing:**
```php
add_action('sfb_after_settings_save', function($settings) {
    // Clear relevant caches
    wp_cache_delete('sfb_branding', 'options');
    delete_transient('sfb_settings_cache');
});
```

**Example - Sync to External Service:**
```php
add_action('sfb_after_settings_save', function($settings) {
    // Sync branding to CDN
    if (isset($settings['branding'])) {
        wp_remote_post('https://api.example.com/sync-branding', [
            'body' => json_encode($settings['branding'])
        ]);
    }
});
```

**Use Cases:**
- Cache invalidation
- External API sync
- Audit logging
- Analytics tracking

---

### Cron Jobs

#### `sfb_purge_expired_drafts`
Scheduled action that runs daily to delete expired drafts.

**Since:** 1.0.0

**Schedule:** Daily at midnight

**Parameters:** None

**Example - Extended Logging:**
```php
add_action('sfb_purge_expired_drafts', function() {
    $count = 0; // Track deletions

    // Custom logging
    error_log('[SFB] Draft purge started at ' . date('Y-m-d H:i:s'));

    // Let default handler run
}, 5); // Priority 5 runs before default (10)

add_action('sfb_purge_expired_drafts', function() use (&$count) {
    // Log after purge
    error_log('[SFB] Purged ' . $count . ' expired drafts');
}, 15); // Priority 15 runs after default
```

**Example - Notification:**
```php
add_action('sfb_purge_expired_drafts', function() {
    // Get draft count before purge
    $args = [
        'post_type'      => 'sfb_draft',
        'posts_per_page' => -1,
        'meta_query'     => [[
            'key'     => '_sfb_draft_expires_at',
            'value'   => current_time('timestamp'),
            'compare' => '<',
            'type'    => 'NUMERIC'
        ]]
    ];

    $expired = get_posts($args);

    if (count($expired) > 10) {
        // Alert admin if many expired drafts
        $admin_email = get_option('admin_email');
        wp_mail(
            $admin_email,
            'High Draft Expiry Count',
            'Warning: ' . count($expired) . ' drafts expired today.'
        );
    }
}, 5);
```

**Use Cases:**
- Monitoring draft usage
- Storage cleanup alerts
- Custom retention policies

---

## Template System

### Template Overrides

Copy templates from plugin to theme for customization:

**Plugin Path:**
```
plugins/submittal-builder/templates/pdf/
```

**Theme Override Path:**
```
your-theme/submittal-builder/pdf/
```

### Available Templates

#### `templates/pdf/cover.html.php`
PDF cover page template.

**Available Variables:**
- `$brand` (array) - Branding settings
- `$meta` (array) - Project metadata
- `$logo_url` (string) - Logo URL
- `$primary_color` (string) - Hex color

**Example Override:**
```php
<!-- your-theme/submittal-builder/pdf/cover.html.php -->
<div class="cover-page" style="background: <?= esc_attr($primary_color) ?>;">
    <h1><?= esc_html($meta['project'] ?? 'Submittal Packet') ?></h1>
    <!-- Custom layout -->
</div>
```

---

#### `templates/pdf/toc.html.php`
Table of contents template.

**Available Variables:**
- `$items` (array) - Selected products
- `$primary_color` (string) - Hex color

---

#### `templates/pdf/summary.html.php`
Summary page template.

**Available Variables:**
- `$items` (array) - Products grouped by category
- `$primary_color` (string) - Hex color

---

#### `templates/pdf/model-sheet.html.php`
Individual product specification sheet.

**Available Variables:**
- `$item` (array) - Product data
- `$primary_color` (string) - Hex color
- `$path` (array) - Breadcrumb array

---

### Template Helper Functions

#### `sfb_get_template($name, $args)`
Load a template file with variables.

**Parameters:**
- `$name` (string) - Template name (e.g., `pdf/cover.html.php`)
- `$args` (array) - Variables to extract in template

**Example:**
```php
sfb_get_template('pdf/cover.html.php', [
    'brand' => $branding,
    'meta'  => $project_info
]);
```

---

## Common Use Cases

### 1. Add Custom Pro Feature

```php
// Register feature
add_filter('sfb_features_map', function($features) {
    $features['salesforce_sync'] = [
        'label' => 'Salesforce Integration',
        'group' => 'Integration',
        'pro'   => true,
        'desc'  => 'Sync submittals to Salesforce',
        'since' => '1.2.0'
    ];
    return $features;
});

// Check if enabled
if (sfb_feature_enabled('salesforce_sync')) {
    // Your Salesforce sync code
}
```

---

### 2. Custom PDF Header

```php
add_action('sfb_before_pdf_generation', function($items, $meta, $brand) {
    // Add custom CSS
    echo '<style>
        .pdf-header {
            background: linear-gradient(to right, #667eea, #764ba2);
        }
    </style>';
}, 10, 3);
```

---

### 3. Client-Specific Branding

```php
add_filter('sfb_pdf_color', function($color, $brand, $meta) {
    // Load client from custom field
    $client = get_post_meta($meta['project_id'], 'client_name', true);

    $client_colors = [
        'ACME Corp'   => '#dc2626',
        'Beta LLC'    => '#2563eb',
        'Gamma Inc'   => '#16a34a',
    ];

    return $client_colors[$client] ?? $color;
}, 10, 3);
```

---

### 4. Draft Usage Analytics

```php
add_action('sfb_after_draft_created', function($draft_id, $payload) {
    // Log to analytics
    wp_remote_post('https://analytics.example.com/track', [
        'body' => json_encode([
            'event'    => 'draft_created',
            'draft_id' => $draft_id,
            'items'    => count($payload['items'])
        ])
    ]);
}, 10, 2);
```

---

### 5. Automated Notifications

```php
add_action('sfb_after_pdf_generated', function($pdf_url, $meta) {
    if (!empty($meta['contractor_email'])) {
        wp_mail(
            $meta['contractor_email'],
            'Your Submittal Packet is Ready',
            'Download: ' . $pdf_url
        );
    }
}, 10, 2);
```

---

### 6. Custom License Validation

```php
add_filter('sfb_is_pro_active', function($is_active, $license_data) {
    // Check custom license table
    global $wpdb;
    $site_url = get_site_url();

    $valid = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}custom_licenses
         WHERE site_url = %s AND status = 'active'",
        $site_url
    ));

    return $valid > 0;
}, 10, 2);
```

---

### 7. Force Theme by User Role

```php
add_filter('sfb_pdf_theme', function($theme, $brand, $meta) {
    $user = wp_get_current_user();

    $role_themes = [
        'architect' => 'architectural',
        'engineer'  => 'engineering',
        'corporate' => 'corporate',
    ];

    foreach ($role_themes as $role => $theme_name) {
        if (in_array($role, $user->roles)) {
            return $theme_name;
        }
    }

    return $theme;
}, 10, 3);
```

---

### 8. Log All Settings Changes

```php
add_action('sfb_after_settings_save', function($settings) {
    $user = wp_get_current_user();

    error_log(sprintf(
        '[SFB] Settings updated by %s (%s) at %s',
        $user->user_login,
        $user->user_email,
        date('Y-m-d H:i:s')
    ));

    // Optionally log to database
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'sfb_audit_log',
        [
            'user_id'    => $user->ID,
            'action'     => 'settings_update',
            'timestamp'  => current_time('mysql'),
            'data'       => json_encode($settings)
        ]
    );
});
```

---

## Debugging Hooks

Enable debug logging for all hooks:

```php
// Log all filter applications
add_filter('all', function($hook) {
    if (strpos($hook, 'sfb_') === 0) {
        error_log('[SFB Filter] ' . $hook);
    }
    return $hook;
});

// Log all action executions
add_action('all', function($hook) {
    if (strpos($hook, 'sfb_') === 0) {
        error_log('[SFB Action] ' . $hook);
    }
});
```

---

## Best Practices

### 1. Always Check Feature Availability
```php
if (sfb_feature_enabled('server_drafts')) {
    // Your Pro feature code
}
```

### 2. Validate Input in Filters
```php
add_filter('sfb_pdf_color', function($color) {
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        return '#111827'; // Safe default
    }
    return $color;
});
```

### 3. Use Appropriate Priorities
- 5 = Run before default
- 10 = Default priority
- 15 = Run after default

### 4. Escape Output in Templates
```php
<?= esc_html($title) ?>
<?= esc_attr($color) ?>
<?= esc_url($logo_url) ?>
```

### 5. Handle Missing Data Gracefully
```php
add_filter('sfb_pdf_theme', function($theme, $brand, $meta) {
    $project = $meta['project'] ?? '';
    // Always provide defaults
    return $theme;
}, 10, 3);
```

---

## Additional Resources

- [WordPress Plugin Handbook - Hooks](https://developer.wordpress.org/plugins/hooks/)
- [API Reference](./API-REFERENCE.md) - REST API documentation
- [Template Overrides Guide](./TEMPLATE-OVERRIDES.md) - Coming soon
- [Plugin Support Forum](https://wordpress.org/support/plugin/submittal-builder/)

---

## Support

For hook-specific questions:
- Email: developers@webstuffguylabs.com
- GitHub: https://github.com/webstuffguylabs/submittal-builder (if public)
- Docs: https://docs.example.com/developers

---

**Last Updated:** 2025-01-08
**Plugin Version:** 1.0.0
**Compatibility:** WordPress 6.0+, PHP 7.4+
