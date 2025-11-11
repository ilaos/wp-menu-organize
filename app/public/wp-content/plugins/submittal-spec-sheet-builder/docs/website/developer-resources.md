# Developer Resources

[← Back to Documentation](./index.md)

**Technical documentation for developers integrating with the plugin**

---

## Overview

This guide provides technical information for developers who want to extend, customize, or integrate with Submittal & Spec Sheet Builder. It covers the REST API, WordPress hooks, template overrides, and advanced customization options.

**Target Audience:**
- Theme developers
- Plugin developers
- Technical implementers
- Advanced users with coding experience

---

## REST API

The plugin exposes a comprehensive REST API for accessing and managing product data.

### API Base URL

```
https://yoursite.com/wp-json/sfb/v1/
```

### Authentication

**Public Endpoints:**
- Product retrieval (GET requests)
- No authentication required

**Protected Endpoints:**
- Product creation/editing (POST, PUT, DELETE)
- Requires authentication via nonce or OAuth

**Using Nonces:**
```javascript
const nonce = sfbData.nonce; // Available in localized script data

fetch('/wp-json/sfb/v1/nodes', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': nonce
  },
  body: JSON.stringify(data)
});
```

---

### Available Endpoints

**Full API reference available at:** [API-REFERENCE.md](../../API-REFERENCE.md)

#### Core Endpoints

**Get All Nodes (Product Tree)**
```
GET /wp-json/sfb/v1/nodes
```

Returns complete product hierarchy.

**Response:**
```json
{
  "categories": [
    {
      "id": "c-studs",
      "name": "C-Studs",
      "type": "category",
      "children": [...]
    }
  ]
}
```

---

**Get Single Product**
```
GET /wp-json/sfb/v1/nodes/{composite_key}
```

**Example:**
```
GET /wp-json/sfb/v1/nodes/c-studs:steel-studs:20-gauge:362s162-20
```

**Response:**
```json
{
  "id": "c-studs:steel-studs:20-gauge:362s162-20",
  "name": "362S162-20",
  "type": "model",
  "category": "C-Studs",
  "product": "Steel Studs",
  "productType": "20 Gauge",
  "specs": {
    "Size": "3.625\"",
    "Flange": "1.625\"",
    "Thickness": "20 GA",
    "KSI": "33"
  }
}
```

---

**Create Node**
```
POST /wp-json/sfb/v1/nodes
```

**Request Body:**
```json
{
  "name": "New Product",
  "type": "model",
  "parent": "c-studs:steel-studs:20-gauge",
  "specs": {
    "Size": "6\"",
    "Thickness": "20 GA"
  }
}
```

---

**Update Node**
```
PUT /wp-json/sfb/v1/nodes/{composite_key}
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "specs": {
    "Size": "6.5\""
  }
}
```

---

**Delete Node**
```
DELETE /wp-json/sfb/v1/nodes/{composite_key}
```

---

**Search Products**
```
GET /wp-json/sfb/v1/search?q={query}
```

**Example:**
```
GET /wp-json/sfb/v1/search?q=362
```

Returns all products matching query.

---

**Get Field Definitions**
```
GET /wp-json/sfb/v1/field-definitions
```

Returns current specification field names.

**Response:**
```json
{
  "fields": [
    {"name": "Size", "type": "text"},
    {"name": "Thickness", "type": "text"},
    {"name": "KSI", "type": "number"}
  ]
}
```

---

### Using the API

**Example: Fetch All Products**
```javascript
async function fetchProducts() {
  const response = await fetch('/wp-json/sfb/v1/nodes');
  const data = await response.json();
  console.log(data);
}
```

**Example: Search Products**
```javascript
async function searchProducts(query) {
  const response = await fetch(`/wp-json/sfb/v1/search?q=${encodeURIComponent(query)}`);
  const results = await response.json();
  return results;
}
```

**Example: Create Product**
```javascript
async function createProduct(productData) {
  const response = await fetch('/wp-json/sfb/v1/nodes', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': sfbData.nonce
    },
    body: JSON.stringify(productData)
  });

  return await response.json();
}
```

---

## WordPress Hooks

The plugin provides numerous hooks for customization.

**Complete hook reference:** [DEVELOPER-HOOKS.md](../../DEVELOPER-HOOKS.md)

### Action Hooks

**After PDF Generated**
```php
do_action('sfb_pdf_generated', $pdf_path, $product_ids, $project_name);
```

**Example:**
```php
add_action('sfb_pdf_generated', function($pdf_path, $product_ids, $project_name) {
  // Send notification email
  wp_mail('admin@site.com', 'New PDF Generated', "Project: $project_name");
}, 10, 3);
```

---

**Before Product Created**
```php
do_action('sfb_before_product_create', $product_data);
```

**Example:**
```php
add_action('sfb_before_product_create', function($product_data) {
  // Log product creation
  error_log('Creating product: ' . $product_data['name']);
});
```

---

**After Product Created**
```php
do_action('sfb_after_product_create', $product_id, $product_data);
```

---

**Before Settings Saved**
```php
do_action('sfb_before_settings_save', $settings);
```

---

### Filter Hooks

**Modify PDF Output**
```php
apply_filters('sfb_pdf_html', $html, $products, $branding);
```

**Example:**
```php
add_filter('sfb_pdf_html', function($html, $products, $branding) {
  // Add custom watermark
  $watermark = '<div class="watermark">DRAFT</div>';
  return str_replace('</body>', $watermark . '</body>', $html);
}, 10, 3);
```

---

**Modify Product Data**
```php
apply_filters('sfb_product_data', $product, $composite_key);
```

**Example:**
```php
add_filter('sfb_product_data', function($product, $key) {
  // Add custom field
  $product['custom_field'] = 'Custom value';
  return $product;
}, 10, 2);
```

---

**Modify Search Results**
```php
apply_filters('sfb_search_results', $results, $query);
```

**Example:**
```php
add_filter('sfb_search_results', function($results, $query) {
  // Boost certain products
  usort($results, function($a, $b) {
    return strcmp($a['name'], $b['name']);
  });
  return $results;
}, 10, 2);
```

---

**Modify Branding Settings**
```php
apply_filters('sfb_branding_settings', $branding);
```

**Example:**
```php
add_filter('sfb_branding_settings', function($branding) {
  // Override logo for specific conditions
  if (is_user_logged_in()) {
    $branding['logo_url'] = 'https://example.com/alternate-logo.png';
  }
  return $branding;
});
```

---

**Customize PDF Filename**
```php
apply_filters('sfb_pdf_filename', $filename, $project_name, $timestamp);
```

**Example:**
```php
add_filter('sfb_pdf_filename', function($filename, $project, $time) {
  // Add company prefix
  return 'acme-' . $filename;
}, 10, 3);
```

---

## Template Overrides

Customize the frontend builder appearance by overriding templates.

### Template Hierarchy

Plugin templates located in:
```
wp-content/plugins/submittal-spec-sheet-builder/templates/
```

**Override in your theme:**
```
wp-content/themes/your-theme/submittal-builder/
```

### Available Templates

**Main Builder Template**
```
templates/builder.php
```

Override:
```
your-theme/submittal-builder/builder.php
```

---

**Product Card**
```
templates/partials/product-card.php
```

Override:
```
your-theme/submittal-builder/partials/product-card.php
```

---

**Product List Row**
```
templates/partials/product-row.php
```

---

**Review Panel**
```
templates/partials/review-panel.php
```

---

**PDF Cover Page**
```
templates/pdf/cover.php
```

---

**PDF Spec Sheet**
```
templates/pdf/spec-sheet.php
```

---

### Template Variables

Templates receive these variables:

**In builder.php:**
- `$products` - Array of all products
- `$branding` - Branding settings
- `$settings` - Plugin settings

**In product-card.php:**
- `$product` - Product data object
- `$specs` - Product specifications
- `$category` - Category name
- `$type` - Type name

**In PDF templates:**
- `$products` - Selected products
- `$project_name` - Project name
- `$branding` - Branding settings
- `$date` - Generation date

---

### Example: Custom Product Card

Create `your-theme/submittal-builder/partials/product-card.php`:

```php
<?php
/**
 * Custom Product Card Template
 */
?>
<div class="custom-product-card" data-id="<?php echo esc_attr($product['id']); ?>">
  <h3><?php echo esc_html($product['name']); ?></h3>

  <?php if (!empty($product['image'])): ?>
    <img src="<?php echo esc_url($product['image']); ?>" alt="">
  <?php endif; ?>

  <div class="specs">
    <?php foreach ($product['specs'] as $key => $value): ?>
      <div class="spec-item">
        <strong><?php echo esc_html($key); ?>:</strong>
        <?php echo esc_html($value); ?>
      </div>
    <?php endforeach; ?>
  </div>

  <button class="select-product">Select</button>
</div>
```

---

## Extending Functionality

### Add Custom Product Fields

**Example: Add "Manufacturer" field**

```php
add_filter('sfb_product_fields', function($fields) {
  $fields[] = [
    'name' => 'manufacturer',
    'label' => 'Manufacturer',
    'type' => 'text'
  ];
  return $fields;
});
```

---

### Custom PDF Sections

**Example: Add compliance section**

```php
add_filter('sfb_pdf_html', function($html, $products) {
  $compliance = '<div class="compliance-section">';
  $compliance .= '<h2>Compliance Information</h2>';
  $compliance .= '<p>All products meet ASTM standards.</p>';
  $compliance .= '</div>';

  // Insert before closing body tag
  return str_replace('</body>', $compliance . '</body>', $html);
}, 10, 2);
```

---

### Integrate with Third-Party Services

**Example: Send data to CRM**

```php
add_action('sfb_pdf_generated', function($pdf_path, $products, $project) {
  // Send to external CRM
  wp_remote_post('https://crm.example.com/api/leads', [
    'body' => [
      'project' => $project,
      'products' => json_encode($products),
      'pdf_url' => $pdf_path
    ]
  ]);
}, 10, 3);
```

---

## JavaScript API

Interact with the builder via JavaScript.

### Global Objects

**sfbData**
Localized script data available on frontend builder pages.

```javascript
console.log(sfbData);
// {
//   ajaxUrl: '/wp-admin/admin-ajax.php',
//   nonce: 'abc123...',
//   restUrl: '/wp-json/sfb/v1/',
//   settings: {...},
//   branding: {...}
// }
```

---

### Events

**Product Selected**
```javascript
document.addEventListener('sfb:product-selected', function(event) {
  console.log('Selected product:', event.detail.product);
});
```

---

**Product Deselected**
```javascript
document.addEventListener('sfb:product-deselected', function(event) {
  console.log('Deselected product:', event.detail.product);
});
```

---

**PDF Generation Started**
```javascript
document.addEventListener('sfb:pdf-generating', function(event) {
  console.log('Generating PDF for:', event.detail.products);
});
```

---

**PDF Generation Complete**
```javascript
document.addEventListener('sfb:pdf-complete', function(event) {
  console.log('PDF ready:', event.detail.url);
});
```

---

### Methods

**Get Selected Products**
```javascript
const selected = window.SFB.getSelected();
console.log(selected);
```

---

**Select Product Programmatically**
```javascript
window.SFB.selectProduct('c-studs:steel-studs:20-gauge:362s162-20');
```

---

**Deselect Product**
```javascript
window.SFB.deselectProduct('c-studs:steel-studs:20-gauge:362s162-20');
```

---

**Clear All Selections**
```javascript
window.SFB.clearSelections();
```

---

**Generate PDF**
```javascript
window.SFB.generatePDF({
  products: ['key1', 'key2'],
  project: 'My Project'
});
```

---

## CSS Customization

Override default styles for complete design control.

### CSS Classes

**Builder Container**
```css
.sfb-builder {
  /* Main builder wrapper */
}
```

**Product Grid**
```css
.sfb-product-grid {
  /* Product card container */
}
```

**Product Card**
```css
.sfb-product-card {
  /* Individual product card */
}

.sfb-product-card.selected {
  /* Selected state */
}
```

**Search Box**
```css
.sfb-search {
  /* Search input */
}
```

**Category Filters**
```css
.sfb-filters {
  /* Filter container */
}
```

---

### Example: Custom Styling

Add to your theme's `style.css` or custom CSS:

```css
/* Custom product cards */
.sfb-product-card {
  border: 2px solid #0066CC;
  border-radius: 8px;
  padding: 20px;
  transition: transform 0.2s;
}

.sfb-product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.sfb-product-card.selected {
  background: #E6F2FF;
  border-color: #0052A3;
}

/* Custom search box */
.sfb-search {
  border: 2px solid #0066CC;
  border-radius: 25px;
  padding: 10px 20px;
  font-size: 16px;
}
```

---

## Code Examples

### Example 1: Automatic Email After PDF Generation

```php
add_action('sfb_pdf_generated', function($pdf_path, $products, $project) {
  $to = 'sales@company.com';
  $subject = "New Submittal Generated: $project";
  $message = "A new submittal packet has been generated.\n\n";
  $message .= "Project: $project\n";
  $message .= "Products: " . count($products) . "\n";
  $message .= "Download: $pdf_path\n";

  wp_mail($to, $subject, $message);
}, 10, 3);
```

---

### Example 2: Add Custom Data to Products

```php
add_filter('sfb_product_data', function($product) {
  // Add stock status from external system
  $sku = $product['specs']['SKU'] ?? '';
  $stock = get_stock_from_external_api($sku);

  $product['stock_status'] = $stock;
  return $product;
});
```

---

### Example 3: Custom PDF Watermark (Pro Users Only)

```php
add_filter('sfb_pdf_html', function($html) {
  // Add "SAMPLE" watermark for non-logged-in users
  if (!is_user_logged_in()) {
    $style = '<style>.watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; opacity: 0.1; }</style>';
    $watermark = '<div class="watermark">SAMPLE</div>';
    $html = str_replace('</head>', $style . '</head>', $html);
    $html = str_replace('</body>', $watermark . '</body>', $html);
  }
  return $html;
});
```

---

### Example 4: Integrate with Google Analytics

```javascript
// Track product selections
document.addEventListener('sfb:product-selected', function(event) {
  gtag('event', 'product_selected', {
    'product_name': event.detail.product.name,
    'product_category': event.detail.product.category
  });
});

// Track PDF generations
document.addEventListener('sfb:pdf-complete', function(event) {
  gtag('event', 'pdf_generated', {
    'project_name': event.detail.project,
    'product_count': event.detail.products.length
  });
});
```

---

## Security Considerations

### Sanitization

Always sanitize data when extending the plugin:

```php
$name = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$url = esc_url_raw($_POST['url']);
```

---

### Nonce Verification

Verify nonces for all form submissions:

```php
if (!wp_verify_nonce($_POST['nonce'], 'sfb_action')) {
  wp_die('Security check failed');
}
```

---

### Capability Checks

Check user capabilities before allowing actions:

```php
if (!current_user_can('manage_options')) {
  wp_die('Insufficient permissions');
}
```

---

## Performance Optimization

### Caching

Use transients for expensive operations:

```php
$products = get_transient('sfb_all_products');

if (false === $products) {
  $products = fetch_all_products(); // Expensive operation
  set_transient('sfb_all_products', $products, HOUR_IN_SECONDS);
}
```

---

### Lazy Loading

Lazy load product images:

```html
<img src="placeholder.jpg"
     data-src="product-image.jpg"
     loading="lazy"
     alt="Product">
```

---

## Additional Resources

### Official Documentation

- **Complete API Reference:** [API-REFERENCE.md](../../API-REFERENCE.md)
- **WordPress Hooks:** [DEVELOPER-HOOKS.md](../../DEVELOPER-HOOKS.md)
- **UI Customization:** [UI-POLISH-GUIDE.md](../../UI-POLISH-GUIDE.md)

### External Resources

- **WordPress Plugin Handbook:** https://developer.wordpress.org/plugins/
- **REST API Handbook:** https://developer.wordpress.org/rest-api/
- **WordPress Coding Standards:** https://developer.wordpress.org/coding-standards/

---

## Support for Developers

**Questions? Issues?**

- **GitHub Issues:** (if available)
- **Email Support:** developers@webstuffguylabs.com
- **WordPress Forum:** https://wordpress.org/support/plugin/submittal-builder/

---

[← Back to Documentation](./index.md) | [Next: FAQ →](./faq.md)

---

© 2025 Webstuffguy Labs. All rights reserved.
