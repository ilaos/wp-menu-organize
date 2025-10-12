# Getting Started with Submittal & Spec Sheet Builder

[← Back to Documentation](./index.md)

**Quick Start Guide** | **Version 1.0.2**

---

## What is Submittal & Spec Sheet Builder?

Submittal & Spec Sheet Builder is a WordPress plugin designed specifically for manufacturers and distributors who need to create professional submittal packets and specification sheets quickly and efficiently.

### What is a Submittal Packet?

A submittal packet is a collection of product specification sheets, typically required for:

- **Construction Projects** - Architects and contractors need detailed product specs
- **Bid Proposals** - Suppliers need to submit product documentation
- **Code Compliance** - Building inspectors require certified specifications
- **Project Documentation** - Engineers need technical data sheets

### Common Use Cases

**For Manufacturers:**
- Create submittal packets for distributors and contractors
- Generate spec sheets for sales teams
- Provide documentation for code compliance
- Support bid proposals with product data

**For Distributors:**
- Package manufacturer specs for customers
- Create custom product bundles
- Support contractor RFQs quickly
- Maintain organized product libraries

**For General Contractors:**
- Compile submittal packets for architects
- Document product selections
- Meet code requirements efficiently
- Organize subcontractor specifications

---

## How It Works

The plugin follows a simple 3-step process:

### Step 1: Build Your Product Catalog

```
Admin Dashboard → Submittal Builder → Products
```

- Add products through the admin interface
- Organize with categories and types
- Define custom specifications for each product
- Build your catalog systematically

**What You Can Add:**
- Product model numbers
- Specifications (size, thickness, KSI, etc.)
- Categories and types
- SKUs and part numbers
- Any custom data fields

### Step 2: Customers Select Products

```
Frontend Page → [submittal_builder] shortcode
```

- Interactive product browser with search
- Filter by category and type
- Select products with single click
- Real-time selection counter
- Keyboard navigation support

**User Experience:**
- Gallery or list view toggle
- Live search across all data
- Category filters
- Selected products tray
- Accessible (WCAG AA compliant)

### Step 3: Generate PDF Packets

```
Review Selection → Add Project Info → Generate PDF
```

- Professional branded PDF packet
- Cover page with your logo and colors
- Table of contents with page numbers
- Product summary table
- Individual spec sheets
- Instant download

**PDF Features:**
- Branded cover page
- Navigation bookmarks
- Print-optimized
- Email-friendly file size
- Temporary download links

---

## Quick Start (5 Minutes)

Follow these steps to get your first submittal packet:

### 1. Install & Activate (2 min)

```
WordPress Admin → Plugins → Add New → Upload Plugin
```

1. Upload plugin ZIP file
2. Click "Install Now"
3. Click "Activate"
4. You'll see "Submittal Builder" in admin menu

### 2. Configure Branding (2 min)

```
Submittal Builder → Settings → Branding
```

1. **Upload Logo** - 300×100px PNG recommended
2. **Set Brand Color** - Choose your primary color
3. **Add Company Info** - Name, website, contact details
4. Click "Save Changes"

### 3. Add Products (1 min)

```
Submittal Builder → Products → Add New
```

Add a few sample products to test with:
- Enter product model number
- Select category and type
- Add specifications (size, thickness, etc.)
- Save product

### 4. Create Builder Page (30 sec)

```
Submittal Builder → Settings → Frontend
→ Click "Auto-Create Builder Page"
```

Or manually:
```
Pages → Add New
→ Add [submittal_builder] shortcode
→ Publish
```

### 5. Test the Builder (30 sec)

1. Visit your builder page
2. Select 2-3 products
3. Click "VIEW →" or continue button
4. Add project name (optional)
5. Click "Generate PDF"
6. Download and review PDF

**🎉 Congratulations!** You've created your first submittal packet.

---

## Key Concepts

### Product Hierarchy

Products are organized in a 3-level hierarchy:

```
Category (e.g., "C-Studs")
  └── Type (e.g., "20 Gauge")
      └── Products (e.g., "362S162-20", "600S162-20")
```

**Why This Structure?**
- Mimics how contractors think
- Enables smart filtering
- Groups related products
- Supports large catalogs

**Example Hierarchy:**
```
Track (C1P1)
  ├── 20 Gauge
  │   ├── 250T125-20
  │   └── 350T125-20
  └── 25 Gauge
      ├── 250T125-25
      └── 350T125-25
```

### Composite Keys

Each product has a unique composite key:

**Format:** `category-slug:type-slug:product-slug`

**Example:** `c-studs:20-gauge:362s162-20`

**Benefits:**
- Ensures uniqueness
- Enables relationships
- Supports filtering
- Prevents duplicates

### Specifications

Products have flexible key-value specifications:

**Common Specs:**
- Size: `3-5/8"`
- Thickness: `33 mil (20 ga)`
- Flange: `1-5/8"`
- KSI: `50`

**Custom Specs:**
- Add any fields you need
- No limit on spec count
- Searchable across all specs
- Displayed in cards and PDFs

---

## Understanding the Interface

### Admin Dashboard

**Main Menu Items:**
- **Dashboard** - Overview and quick stats
- **Products** - Manage your catalog
- **Categories** - Organize products
- **Types** - Define product types
- **Settings** - Configure plugin
- **Documentation** - Help and guides

### Frontend Builder

**Three Steps:**

1. **Products** - Browse and select
   - Gallery/list view toggle
   - Search and filters
   - Selection counter
   - Product cards with specs

2. **Review** - Customize and organize
   - Drag to reorder
   - Remove unwanted items
   - Add project info
   - Preview branding

3. **Generate** - Create PDF
   - Loading progress
   - Success page
   - Open/download PDF
   - Start over option

---

## Best Practices

### For Setting Up

✅ **Do:**
- Import all products before going live
- Test PDF generation with different product counts
- Add comprehensive specifications
- Use high-quality logo (PNG, transparent background)
- Choose brand color with good contrast
- Test on mobile devices

❌ **Don't:**
- Skip branding setup
- Use low-resolution logos
- Import duplicate products
- Forget to test PDF output
- Overlook mobile experience

### For Organizing Products

✅ **Do:**
- Use consistent category names
- Follow industry standard naming
- Include SKU/part numbers
- Add all relevant specifications
- Keep model numbers clean and consistent

❌ **Don't:**
- Create too many categories (causes confusion)
- Use vague type names
- Forget specifications
- Mix naming conventions
- Import unsorted data

### For Your Users

✅ **Do:**
- Place builder on prominent page
- Add clear instructions for users
- Test with real users
- Monitor generated PDFs
- Clean up old PDFs regularly

❌ **Don't:**
- Hide builder on deep page
- Assume users know how to use it
- Ignore user feedback
- Let PDFs accumulate forever
- Skip testing with actual contractors

---

## Common Workflows

### Workflow 1: Contractor Creates Submittal

```
1. Contractor visits your builder page
2. Searches for products by model or specs
3. Selects needed products
4. Adds project name and notes
5. Generates branded PDF
6. Downloads for submittal packet
7. Emails to architect/inspector
```

**Time Saved:** 2-3 hours vs manual process

### Workflow 2: Sales Team Creates Quote

```
1. Sales rep receives RFQ
2. Logs into builder
3. Filters by category/type
4. Selects quoted products
5. Adds customer project name
6. Generates PDF
7. Includes in quote package
```

**Time Saved:** 30-45 minutes per quote

### Workflow 3: Engineer Creates Spec Book

```
1. Engineer reviews project requirements
2. Uses search to find compliant products
3. Selects full product line
4. Reorders by installation sequence
5. Adds detailed project notes
6. Generates comprehensive PDF
7. Includes in spec book
```

**Time Saved:** 4-6 hours vs manual compilation

---

## What's Next?

### Recommended Reading Order

1. ✅ **Getting Started** (you are here)
2. 📖 [Installation Guide](./installation.md) - Detailed setup
3. 📖 [User Guide](./user-guide.md) - Complete walkthrough
4. 📖 [Admin Settings](./admin-settings.md) - Configuration options
5. 📖 [Troubleshooting](./troubleshooting.md) - Common issues

### Quick Links

- [Import Products →](./product-management.md#bulk-import)
- [Customize Branding →](./branding-pdfs.md#branding-customization)
- [Keyboard Shortcuts →](./user-guide.md#keyboard-shortcuts)
- [FAQ →](./faq.md)

### Need Help?

- **WordPress.org Forum:** [Community Support](https://wordpress.org/support/plugin/submittal-builder/)
- **Documentation:** [Full Documentation](./index.md)
- **Email:** support@webstuffguylabs.com (Pro users)

---

## Features at a Glance

### ✅ Product Management
- Add products via admin interface
- Unlimited products
- Hierarchical organization
- Category management
- Custom specifications

### ✅ Frontend Builder
- Interactive product browser
- Live search and filtering
- Gallery and list views
- Keyboard accessible
- Mobile responsive

### ✅ PDF Generation
- Branded cover page
- Table of contents
- Summary page
- Individual spec sheets
- Navigation bookmarks

### ✅ Customization
- Logo upload
- Brand colors
- Contact information
- Custom specifications
- Template overrides

### ✅ Performance
- Fast PDF generation
- Optimized search
- Smart caching
- Responsive design
- Progressive enhancement

### ✅ Accessibility
- WCAG AA compliant
- Keyboard navigation
- Screen reader support
- High contrast
- Focus indicators

---

[← Back to Documentation](./index.md) | [Next: Installation Guide →](./installation.md)

---

© 2025 WebStuff Guy Labs. All rights reserved.
