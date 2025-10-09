# REST API Reference
## Submittal & Spec Sheet Builder v1.0.0

Complete documentation for all 27 REST API endpoints available in the plugin.

---

## Table of Contents

- [Authentication](#authentication)
- [Error Handling](#error-handling)
- [Health & Status](#health--status)
- [Form Management](#form-management)
- [Node Operations](#node-operations)
- [Bulk Operations](#bulk-operations)
- [Import/Export](#importexport)
- [PDF Generation](#pdf-generation)
- [Draft Management (Pro)](#draft-management-pro)
- [Settings](#settings)
- [License Management](#license-management)

---

## Authentication

All REST API requests use WordPress's built-in authentication system.

### Admin Endpoints
Endpoints requiring `manage_options` capability use WordPress cookie authentication:
- Must be logged in as Administrator
- Nonce automatically handled by `wp.apiFetch` in JavaScript
- For external tools, use [Application Passwords](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/)

### Public Endpoints
Some endpoints are publicly accessible (no authentication required):
- `/health`, `/ping` - Health checks
- `/generate` - PDF generation (client-facing form)
- `/drafts/{id}` (GET) - Retrieve shared drafts

### Making Authenticated Requests

**JavaScript (wp.apiFetch):**
```javascript
wp.apiFetch({
  path: '/sfb/v1/form/1',
  method: 'GET'
}).then(data => console.log(data));
```

**External API (Application Password):**
```bash
curl -X GET https://yoursite.com/wp-json/sfb/v1/form/1 \
  -u username:application_password
```

---

## Error Handling

All endpoints return standard WordPress REST API error responses.

### Error Response Format
```json
{
  "code": "error_code",
  "message": "Human-readable error message",
  "data": {
    "status": 400
  }
}
```

### Common HTTP Status Codes
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `410` - Gone (resource expired, used for drafts)
- `500` - Internal Server Error

### Common Error Codes
- `rest_forbidden` - User lacks required permissions
- `invalid_param` - Required parameter missing or invalid
- `sfb_feature_disabled` - Pro feature not available (license inactive)
- `sfb_rate_limited` - Too many requests (draft creation)
- `sfb_draft_expired` - Draft has expired (deleted)

---

## Health & Status

### GET /sfb/v1/health
Health check endpoint returning plugin version and status.

**Permission:** Public (no authentication required)

**Parameters:** None

**Response:**
```json
{
  "ok": true,
  "plugin": "Submittal & Spec Sheet Builder",
  "version": "1.0.0"
}
```

**Use Cases:**
- Monitoring plugin availability
- Version verification
- Automated health checks

**Example:**
```javascript
wp.apiFetch({ path: '/sfb/v1/health' })
  .then(data => console.log('Plugin version:', data.version));
```

---

### GET /sfb/v1/ping
Simple connectivity test endpoint.

**Permission:** Public

**Parameters:** None

**Response:**
```json
{
  "pong": true,
  "time": 1704076800
}
```

**Use Cases:**
- Testing REST API connectivity
- Latency checks
- Uptime monitoring

---

### GET /sfb/v1/status
Public status endpoint showing Pro activation state and available features.

**Permission:** Public

**Parameters:** None

**Response:**
```json
{
  "ok": true,
  "pro_active": false,
  "features": ["summary", "toc"],
  "drafts": {
    "autosave_enabled": true,
    "server_enabled": false,
    "expiry_days": 45,
    "rate_limit_sec": 20
  }
}
```

**Response Fields:**
- `pro_active` (boolean) - Whether Pro license is active
- `features` (array) - List of enabled feature keys
- `drafts.autosave_enabled` (boolean) - Local browser autosave enabled
- `drafts.server_enabled` (boolean) - Server-side drafts enabled (Pro)
- `drafts.expiry_days` (int) - Days until drafts expire
- `drafts.rate_limit_sec` (int) - Seconds between draft saves

**Use Cases:**
- Frontend feature detection
- Conditionally show/hide UI elements
- Check draft settings before saving

**Example:**
```javascript
wp.apiFetch({ path: '/sfb/v1/status' })
  .then(data => {
    if (data.drafts.server_enabled) {
      // Show "Save & Share" button
    }
  });
```

---

## Form Management

### POST /sfb/v1/form/seed
Generate sample catalog data for testing/demo purposes.

**Permission:** Admin (`manage_options`)

**Request Body:**
```json
{
  "size": "medium",
  "mode": "replace",
  "branding": true,
  "draft": false
}
```

**Parameters:**
- `size` (string, required) - Catalog size: `small` (3 cats), `medium` (5 cats), `large` (8 cats)
- `mode` (string, optional) - `replace` (wipe existing) or `merge` (append). Default: `replace`
- `branding` (boolean, optional) - Apply branding preset. Default: `false`
- `draft` (boolean, optional) - Create demo draft with selections. Default: `false`

**Response:**
```json
{
  "ok": true,
  "stats": {
    "categories": 5,
    "products": 18,
    "types": 42,
    "models": 89,
    "seconds": 0.482
  },
  "draft_url": "https://yoursite.com/submittal-form?sfb_draft=abc123def456"
}
```

**Response Fields:**
- `stats` (object) - Count of nodes created
- `draft_url` (string, optional) - Shareable draft URL (if `draft: true` and Pro active)

**Use Cases:**
- Testing with realistic data
- Demo environments
- Quick setup for trials

**Example:**
```javascript
wp.apiFetch({
  path: '/sfb/v1/form/seed',
  method: 'POST',
  data: { size: 'medium', mode: 'replace', branding: true }
}).then(data => console.log('Created', data.stats.models, 'models'));
```

---

### POST /sfb/v1/form/wipe
Delete all catalog data (nodes) for a form. **Destructive operation.**

**Permission:** Admin

**Request Body:**
```json
{
  "form_id": 1
}
```

**Parameters:**
- `form_id` (int, required) - Form ID to wipe. Usually `1`.

**Response:**
```json
{
  "ok": true,
  "deleted": 127
}
```

**Response Fields:**
- `deleted` (int) - Number of nodes removed

**Use Cases:**
- Resetting demo environments
- Clearing test data
- Starting fresh catalog

**Example:**
```javascript
if (confirm('Delete all catalog data?')) {
  wp.apiFetch({
    path: '/sfb/v1/form/wipe',
    method: 'POST',
    data: { form_id: 1 }
  });
}
```

---

### GET /sfb/v1/form/{id}
Retrieve complete form with all nodes (categories, products, types, models).

**Permission:** Admin for full data, Public for basic catalog

**Parameters:**
- `id` (int, URL parameter) - Form ID. Usually `1`.

**Response:**
```json
{
  "ok": true,
  "form_id": 1,
  "nodes": [
    {
      "id": 1,
      "parent_id": null,
      "node_type": "category",
      "title": "Framing",
      "position": 1,
      "settings": {},
      "children": []
    },
    {
      "id": 2,
      "parent_id": 1,
      "node_type": "product",
      "title": "Standard Studs",
      "position": 1,
      "settings": {},
      "children": []
    }
  ]
}
```

**Response Fields:**
- `nodes` (array) - Flat array of all nodes in hierarchical order
- `node.id` (int) - Unique node ID
- `node.parent_id` (int|null) - Parent node ID (null for categories)
- `node.node_type` (string) - `category`, `product`, `type`, or `model`
- `node.title` (string) - Node display name
- `node.position` (int) - Sort order within parent
- `node.settings` (object) - Node metadata (fields for models)

**Use Cases:**
- Loading catalog in admin builder
- Displaying product tree in frontend
- Exporting catalog structure

**Example:**
```javascript
wp.apiFetch({ path: '/sfb/v1/form/1' })
  .then(data => {
    const categories = data.nodes.filter(n => n.node_type === 'category');
    console.log('Found', categories.length, 'categories');
  });
```

---

## Node Operations

### POST /sfb/v1/node/save
Update an existing node's fields (title, settings, etc.).

**Permission:** Admin

**Request Body:**
```json
{
  "id": 42,
  "title": "Updated Title",
  "settings": {
    "fields": {
      "size": "3.5\"",
      "thickness": "43 mil",
      "flange": "1.625\"",
      "ksi": "33"
    }
  }
}
```

**Parameters:**
- `id` (int, required) - Node ID to update
- `title` (string, optional) - New node title
- `settings` (object, optional) - Node metadata (typically `settings.fields` for models)

**Response:**
```json
{
  "ok": true,
  "node_id": 42
}
```

**Use Cases:**
- Editing product titles
- Updating specification fields
- Saving inline edits

**Example:**
```javascript
wp.apiFetch({
  path: '/sfb/v1/node/save',
  method: 'POST',
  data: {
    id: 42,
    title: 'Cold-Formed Steel Stud - 3.5"',
    settings: { fields: { size: '3.5"', thickness: '43 mil' } }
  }
});
```

---

### POST /sfb/v1/node/create
Create a new node (category, product, type, or model).

**Permission:** Admin

**Request Body:**
```json
{
  "form_id": 1,
  "parent_id": 5,
  "node_type": "product",
  "title": "New Product"
}
```

**Parameters:**
- `form_id` (int, required) - Form ID. Usually `1`.
- `parent_id` (int|null, required) - Parent node ID (null for top-level categories)
- `node_type` (string, required) - `category`, `product`, `type`, or `model`
- `title` (string, required) - Node display name
- `settings` (object, optional) - Initial metadata

**Response:**
```json
{
  "ok": true,
  "node_id": 127
}
```

**Response Fields:**
- `node_id` (int) - ID of newly created node

**Use Cases:**
- Adding new categories/products
- Building catalog structure
- Importing data programmatically

**Example:**
```javascript
wp.apiFetch({
  path: '/sfb/v1/node/create',
  method: 'POST',
  data: {
    form_id: 1,
    parent_id: 3,
    node_type: 'product',
    title: 'Heavy Duty Fasteners'
  }
}).then(data => console.log('Created node', data.node_id));
```

---

### POST /sfb/v1/node/delete
Delete a node and all its descendants (cascade delete).

**Permission:** Admin

**Request Body:**
```json
{
  "id": 42
}
```

**Parameters:**
- `id` (int, required) - Node ID to delete

**Response:**
```json
{
  "ok": true,
  "deleted": 15
}
```

**Response Fields:**
- `deleted` (int) - Total nodes removed (including descendants)

**Use Cases:**
- Removing unwanted categories/products
- Cleaning up catalog
- Deleting test data

**Example:**
```javascript
if (confirm('Delete this product and all its children?')) {
  wp.apiFetch({
    path: '/sfb/v1/node/delete',
    method: 'POST',
    data: { id: 42 }
  });
}
```

---

### POST /sfb/v1/node/reorder
Change a node's position within its parent.

**Permission:** Admin

**Request Body:**
```json
{
  "id": 42,
  "new_position": 3
}
```

**Parameters:**
- `id` (int, required) - Node ID to reorder
- `new_position` (int, required) - New position (1-based index)

**Response:**
```json
{
  "ok": true
}
```

**Use Cases:**
- Manual sort order adjustment
- Moving items up/down in lists
- Keyboard-based reordering

---

### POST /sfb/v1/node/duplicate
Clone a node and all its descendants.

**Permission:** Admin

**Request Body:**
```json
{
  "id": 42,
  "title_suffix": " (Copy)"
}
```

**Parameters:**
- `id` (int, required) - Node ID to duplicate
- `title_suffix` (string, optional) - Text appended to cloned titles. Default: ` (Copy)`

**Response:**
```json
{
  "ok": true,
  "new_id": 128,
  "cloned": 5
}
```

**Response Fields:**
- `new_id` (int) - ID of cloned root node
- `cloned` (int) - Total nodes cloned (including descendants)

**Use Cases:**
- Creating product variations
- Template-based catalog building
- Quick duplication of complex structures

**Example:**
```javascript
wp.apiFetch({
  path: '/sfb/v1/node/duplicate',
  method: 'POST',
  data: { id: 42, title_suffix: ' - V2' }
}).then(data => console.log('Cloned', data.cloned, 'nodes'));
```

---

### POST /sfb/v1/node/move
Drag & drop node repositioning (change parent and/or position).

**Permission:** Admin

**Request Body:**
```json
{
  "id": 42,
  "new_parent_id": 10,
  "new_position": 2
}
```

**Parameters:**
- `id` (int, required) - Node ID to move
- `new_parent_id` (int|null, required) - New parent node ID
- `new_position` (int, required) - Position within new parent (1-based)

**Response:**
```json
{
  "ok": true
}
```

**Use Cases:**
- Drag & drop UI implementation
- Reorganizing catalog structure
- Moving products between categories

**Example:**
```javascript
// User drags product ID 42 to category ID 10, position 2
wp.apiFetch({
  path: '/sfb/v1/node/move',
  method: 'POST',
  data: { id: 42, new_parent_id: 10, new_position: 2 }
});
```

---

### GET /sfb/v1/node/history
Retrieve change history for a node (placeholder - structure exists but not fully implemented).

**Permission:** Admin

**Parameters:**
- `id` (int, query parameter) - Node ID

**Response:**
```json
{
  "ok": true,
  "history": []
}
```

**Status:** Endpoint exists but history tracking not yet implemented.

---

## Bulk Operations

### POST /sfb/v1/bulk/delete
Delete multiple nodes at once.

**Permission:** Admin

**Request Body:**
```json
{
  "ids": [42, 43, 44]
}
```

**Parameters:**
- `ids` (array of int, required) - Array of node IDs to delete

**Response:**
```json
{
  "ok": true,
  "deleted": 12
}
```

**Response Fields:**
- `deleted` (int) - Total nodes removed (including descendants)

**Use Cases:**
- Multi-select delete in admin UI
- Batch cleanup operations
- Removing multiple test items

**Example:**
```javascript
const selectedIds = [42, 43, 44, 45];
wp.apiFetch({
  path: '/sfb/v1/bulk/delete',
  method: 'POST',
  data: { ids: selectedIds }
}).then(data => console.log('Deleted', data.deleted, 'nodes'));
```

---

### POST /sfb/v1/bulk/move
Move multiple nodes to a new parent.

**Permission:** Admin

**Request Body:**
```json
{
  "ids": [42, 43, 44],
  "new_parent_id": 10
}
```

**Parameters:**
- `ids` (array of int, required) - Array of node IDs to move
- `new_parent_id` (int, required) - New parent node ID

**Response:**
```json
{
  "ok": true,
  "moved": 3
}
```

**Use Cases:**
- Reorganizing multiple products at once
- Moving selections to different category
- Batch catalog restructuring

---

### POST /sfb/v1/bulk/duplicate
Clone multiple nodes at once.

**Permission:** Admin

**Request Body:**
```json
{
  "ids": [42, 43, 44],
  "title_suffix": " (Backup)"
}
```

**Parameters:**
- `ids` (array of int, required) - Array of node IDs to duplicate
- `title_suffix` (string, optional) - Text appended to cloned titles

**Response:**
```json
{
  "ok": true,
  "cloned": 15
}
```

**Use Cases:**
- Creating backup copies
- Batch product variations
- Template duplication

---

### POST /sfb/v1/bulk/export
Export selected nodes as JSON.

**Permission:** Admin

**Request Body:**
```json
{
  "ids": [42, 43, 44]
}
```

**Parameters:**
- `ids` (array of int, required) - Array of node IDs to export

**Response:**
```json
{
  "ok": true,
  "nodes": [
    {
      "id": 42,
      "title": "Product Name",
      "node_type": "product",
      "settings": {}
    }
  ]
}
```

**Use Cases:**
- Selective data export
- Moving nodes between sites
- Creating catalog backups

---

## Import/Export

### GET /sfb/v1/form/{id}/export
Export entire form catalog as JSON file.

**Permission:** Admin

**Parameters:**
- `id` (int, URL parameter) - Form ID to export. Usually `1`.

**Response:**
```json
{
  "ok": true,
  "form_id": 1,
  "exported": 127,
  "nodes": [...]
}
```

**Response Fields:**
- `exported` (int) - Number of nodes exported
- `nodes` (array) - Complete node tree

**Use Cases:**
- Full catalog backup
- Migrating catalog to another site
- Version control for catalog data

**Example:**
```javascript
wp.apiFetch({ path: '/sfb/v1/form/1/export' })
  .then(data => {
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'catalog-export.json';
    a.click();
  });
```

---

### POST /sfb/v1/form/import
Import catalog from JSON file (replaces existing data).

**Permission:** Admin

**Request Body:**
```json
{
  "form_id": 1,
  "nodes": [
    {
      "title": "Category Name",
      "node_type": "category",
      "children": []
    }
  ]
}
```

**Parameters:**
- `form_id` (int, required) - Target form ID. Usually `1`.
- `nodes` (array, required) - Array of nodes with hierarchical structure

**Response:**
```json
{
  "ok": true,
  "imported": 127
}
```

**Response Fields:**
- `imported` (int) - Number of nodes created

**Use Cases:**
- Restoring from backup
- Migrating catalog from another site
- Loading pre-built catalog templates

**Example:**
```javascript
// User uploads JSON file
const fileInput = document.querySelector('input[type="file"]');
fileInput.addEventListener('change', async (e) => {
  const file = e.target.files[0];
  const text = await file.text();
  const data = JSON.parse(text);

  wp.apiFetch({
    path: '/sfb/v1/form/import',
    method: 'POST',
    data: { form_id: 1, nodes: data.nodes }
  }).then(result => console.log('Imported', result.imported, 'nodes'));
});
```

---

## PDF Generation

### POST /sfb/v1/generate
Generate a PDF packet from selected products.

**Permission:** Public (client-facing form uses this)

**Request Body:**
```json
{
  "form_id": 1,
  "items": [
    {
      "id": 42,
      "title": "Product Name",
      "meta": {
        "size": "3.5\"",
        "thickness": "43 mil"
      },
      "path": ["Framing", "Studs", "Cold-Formed"]
    }
  ],
  "meta": {
    "project": "Downtown Office Tower",
    "contractor": "ABC Construction",
    "submittal": "SUB-001",
    "preset": "packet",
    "format": "pdf",
    "include_cover": true,
    "include_leed": false
  }
}
```

**Parameters:**
- `form_id` (int, required) - Form ID. Usually `1`.
- `items` (array, required) - Array of selected products with metadata
  - `id` (int) - Product/model node ID
  - `title` (string) - Product display name
  - `meta` (object) - Specification fields
  - `path` (array) - Breadcrumb hierarchy
- `meta` (object, required) - Project metadata
  - `project` (string, optional) - Project name
  - `contractor` (string, optional) - Contractor name
  - `submittal` (string, optional) - Submittal number
  - `preset` (string, optional) - Layout: `technical`, `branded`, or `packet`. Default: `packet`
  - `format` (string, optional) - Output format: `pdf` or `html`. Default: `pdf`
  - `include_cover` (boolean, optional) - Include cover page. Default: `true`
  - `include_leed` (boolean, optional) - Include LEED note (Pro). Default: `false`

**Response:**
```json
{
  "ok": true,
  "url": "https://yoursite.com/wp-content/uploads/sfb/Downtown-Office-Tower-20250108-123456.pdf",
  "filename": "Downtown-Office-Tower-20250108-123456.pdf"
}
```

**Response Fields:**
- `url` (string) - Download URL for generated PDF
- `filename` (string) - Generated filename

**Use Cases:**
- Frontend form submission
- Generating submittals for clients
- Creating spec packets for projects

**Example:**
```javascript
const selectedItems = [/* array of selected products */];
const projectInfo = {
  project: 'My Project',
  contractor: 'Smith Co.',
  submittal: 'SUB-001',
  preset: 'packet'
};

wp.apiFetch({
  path: '/sfb/v1/generate',
  method: 'POST',
  data: {
    form_id: 1,
    items: selectedItems,
    meta: projectInfo
  }
}).then(data => {
  window.location.href = data.url; // Download PDF
});
```

---

## Draft Management (Pro)

**Note:** All draft endpoints require Pro license to be active.

### POST /sfb/v1/drafts
Create a new server-side shareable draft.

**Permission:** Public (with rate limiting)

**Pro Feature:** Requires active license or `SFB_PRO_DEV` constant

**Request Body:**
```json
{
  "items": [
    {
      "id": 42,
      "title": "Product Name",
      "meta": {},
      "path": ["Category"]
    }
  ],
  "meta": {
    "project": "Project Name",
    "contractor": "Contractor",
    "submittal": "SUB-001",
    "preset": "packet",
    "format": "pdf"
  }
}
```

**Parameters:**
- `items` (array, required) - Array of selected products
- `meta` (object, required) - Form metadata (project info)

**Response:**
```json
{
  "ok": true,
  "draft_id": "abc123def456",
  "share_url": "https://yoursite.com/submittal-form?sfb_draft=abc123def456",
  "expires_at": "2025-02-22T00:00:00Z"
}
```

**Response Fields:**
- `draft_id` (string) - Unique draft identifier (12 chars)
- `share_url` (string) - Shareable URL to restore draft
- `expires_at` (string) - ISO 8601 expiration timestamp

**Rate Limiting:**
- Default: 20 seconds between saves per IP
- Configurable in Settings

**Use Cases:**
- Save progress for later
- Share selections with team members
- Resume on different device

**Example:**
```javascript
const saveDraftBtn = document.querySelector('#save-draft');
saveDraftBtn.addEventListener('click', () => {
  wp.apiFetch({
    path: '/sfb/v1/drafts',
    method: 'POST',
    data: {
      items: selectedItems,
      meta: formMetadata
    }
  }).then(data => {
    alert('Draft saved! Share this link: ' + data.share_url);
    navigator.clipboard.writeText(data.share_url);
  });
});
```

---

### GET /sfb/v1/drafts/{id}
Retrieve a saved draft by ID.

**Permission:** Public

**Pro Feature:** Requires active license

**Parameters:**
- `id` (string, URL parameter) - Draft ID (6-36 characters)

**Response:**
```json
{
  "ok": true,
  "draft_id": "abc123def456",
  "payload": {
    "items": [...],
    "meta": {...}
  },
  "created_at": "2025-01-08T10:00:00Z",
  "expires_at": "2025-02-22T00:00:00Z"
}
```

**Response Fields:**
- `payload` (object) - Original draft data (items + meta)
- `created_at` (string) - ISO 8601 creation timestamp
- `expires_at` (string) - ISO 8601 expiration timestamp

**Error Responses:**
- `404` - Draft not found
- `410` - Draft expired (auto-deleted)

**Use Cases:**
- Loading shared drafts
- Restoring saved progress
- Pre-populating form from URL

**Example:**
```javascript
// Extract draft ID from URL: ?sfb_draft=abc123def456
const urlParams = new URLSearchParams(window.location.search);
const draftId = urlParams.get('sfb_draft');

if (draftId) {
  wp.apiFetch({ path: `/sfb/v1/drafts/${draftId}` })
    .then(data => {
      // Restore selections and form fields
      selectedItems = data.payload.items;
      formMetadata = data.payload.meta;
      renderUI();
    })
    .catch(err => {
      if (err.data.status === 410) {
        alert('This draft has expired.');
      }
    });
}
```

---

### PUT /sfb/v1/drafts/{id}
Update an existing draft.

**Permission:** Public (with rate limiting)

**Pro Feature:** Requires active license

**Parameters:**
- `id` (string, URL parameter) - Draft ID to update

**Request Body:**
```json
{
  "items": [...],
  "meta": {...}
}
```

**Response:**
```json
{
  "ok": true,
  "draft_id": "abc123def456",
  "updated_at": "2025-01-08T11:00:00Z"
}
```

**Use Cases:**
- Updating shared draft
- Saving incremental changes
- Collaborative editing

---

## Settings

### GET /sfb/v1/settings
Retrieve all plugin settings (branding, drafts, links).

**Permission:** Admin

**Parameters:** None

**Response:**
```json
{
  "ok": true,
  "branding": {
    "logo_url": "https://yoursite.com/logo.png",
    "company_name": "ABC Corp",
    "company_address": "123 Main St",
    "company_phone": "(555) 123-4567",
    "company_website": "https://abccorp.com",
    "primary_color": "#7c3aed",
    "brand_preset": "engineering-bold",
    "cover_default": true,
    "footer_text": "Professional Submittal Packet",
    "theme": "engineering",
    "watermark": ""
  },
  "drafts": {
    "autosave_enabled": true,
    "server_enabled": false,
    "expiry_days": 45,
    "rate_limit_sec": 20,
    "privacy_note": ""
  },
  "links": {
    "account": "https://example.com/account",
    "docs": "https://example.com/docs"
  }
}
```

**Use Cases:**
- Loading settings in admin UI
- Applying branding to frontend
- Feature configuration checks

---

### POST /sfb/v1/settings
Update plugin settings.

**Permission:** Admin

**Request Body:**
```json
{
  "branding": {
    "company_name": "Updated Corp",
    "primary_color": "#0ea5e9"
  },
  "drafts": {
    "expiry_days": 60
  }
}
```

**Parameters:** Partial settings object (only changed fields)

**Response:**
```json
{
  "ok": true
}
```

**Use Cases:**
- Saving branding changes
- Updating draft settings
- Configuring external links

---

## License Management

### GET /sfb/v1/license
Get current license status.

**Permission:** Admin

**Parameters:** None

**Response:**
```json
{
  "ok": true,
  "license": {
    "key": "XXXX-XXXX-XXXX-XXXX",
    "key_masked": "••••••••-1234",
    "email": "user@example.com",
    "status": "active",
    "status_label": "Active",
    "status_color": "#46b450",
    "is_active": true,
    "has_key": true,
    "expires": "2026-01-08",
    "activations_remaining": 2
  }
}
```

**Response Fields:**
- `status` (string) - `active`, `expired`, `invalid`, or `inactive`
- `expires` (string|null) - Expiration date (ISO 8601)
- `activations_remaining` (int|null) - Available activations

**Use Cases:**
- Displaying license info in admin
- Checking activation status
- Warning users about expiration

---

### POST /sfb/v1/license
Activate or deactivate Pro license.

**Permission:** Admin

**Request Body (Activation):**
```json
{
  "action": "activate",
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "email": "user@example.com"
}
```

**Request Body (Deactivation):**
```json
{
  "action": "deactivate"
}
```

**Parameters:**
- `action` (string, required) - `activate` or `deactivate`
- `license_key` (string, required for activation) - License key
- `email` (string, required for activation) - Email address

**Response (Success):**
```json
{
  "ok": true,
  "status": "active",
  "message": "License activated successfully"
}
```

**Response (Error):**
```json
{
  "ok": false,
  "error": "Invalid license key",
  "code": "invalid_key"
}
```

**Error Codes:**
- `invalid_key` - License key not found
- `expired` - License has expired
- `max_activations` - Activation limit reached
- `connection_error` - Cannot reach license server

**Use Cases:**
- License activation flow
- Deactivating before site migration
- Managing license seats

**Example:**
```javascript
// Activate license
wp.apiFetch({
  path: '/sfb/v1/license',
  method: 'POST',
  data: {
    action: 'activate',
    license_key: 'XXXX-XXXX-XXXX-XXXX',
    email: 'user@example.com'
  }
}).then(data => {
  if (data.ok) {
    alert('License activated!');
  }
}).catch(err => {
  alert('Error: ' + err.message);
});
```

---

## Rate Limiting

Some endpoints implement rate limiting to prevent abuse:

### Draft Endpoints
- **Limit:** Configurable seconds between requests per IP (default: 20s)
- **Scope:** Per IP address
- **Storage:** WordPress transients
- **Applies To:** `POST /drafts`, `PUT /drafts/{id}`

**Error Response:**
```json
{
  "code": "sfb_rate_limited",
  "message": "Please wait 15 seconds before saving again",
  "data": {
    "status": 429
  }
}
```

---

## Pagination

Currently, no endpoints support pagination. All results are returned in full.

**Future Consideration:**
For large catalogs (1000+ nodes), pagination may be added to `/form/{id}` endpoint using:
- `per_page` (int) - Items per page
- `page` (int) - Page number
- `offset` (int) - Skip N items

---

## API Version History

### v1 (Current)
- Initial release
- 27 endpoints
- Pro feature gating
- Draft management
- Full CRUD operations

---

## Common Integration Patterns

### Loading Catalog in React
```javascript
import { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

function CatalogBrowser() {
  const [nodes, setNodes] = useState([]);

  useEffect(() => {
    apiFetch({ path: '/sfb/v1/form/1' })
      .then(data => setNodes(data.nodes));
  }, []);

  return <div>{/* Render tree */}</div>;
}
```

### Generating PDF from Selection
```javascript
async function generatePDF(selectedItems, projectInfo) {
  const response = await wp.apiFetch({
    path: '/sfb/v1/generate',
    method: 'POST',
    data: {
      form_id: 1,
      items: selectedItems,
      meta: projectInfo
    }
  });

  // Trigger download
  window.location.href = response.url;
}
```

### Checking Pro Status Before Showing Feature
```javascript
wp.apiFetch({ path: '/sfb/v1/status' })
  .then(data => {
    if (data.drafts.server_enabled) {
      document.querySelector('.save-draft-btn').style.display = 'block';
    }
  });
```

---

## External API Access

For headless/external integrations, use WordPress Application Passwords:

1. Go to **Users → Profile**
2. Scroll to **Application Passwords**
3. Generate new password
4. Use in Authorization header:

```bash
curl -X GET https://yoursite.com/wp-json/sfb/v1/form/1 \
  -u "username:xxxx xxxx xxxx xxxx xxxx xxxx"
```

---

## Support

For API issues or questions:
- Check [Plugin Documentation](https://docs.example.com)
- Visit [Support Forum](https://wordpress.org/support/plugin/submittal-builder/)
- Contact: support@webstuffguylabs.com

---

**Last Updated:** 2025-01-08
**Plugin Version:** 1.0.0
**API Version:** v1
