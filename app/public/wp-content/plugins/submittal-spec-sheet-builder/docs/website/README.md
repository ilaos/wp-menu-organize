# Website Documentation Structure

This directory contains web-ready documentation pages that can be published directly to your website.

## 📁 Structure

```
docs/website/
├── README.md (this file)
├── index.md (main navigation hub)
├── getting-started.md (✅ complete)
├── installation.md (use WEBSITE-DOCUMENTATION.md lines 101-174)
├── user-guide.md (use WEBSITE-DOCUMENTATION.md lines 176-363)
├── admin-settings.md (use WEBSITE-DOCUMENTATION.md lines 366-493)
├── product-management.md (use WEBSITE-DOCUMENTATION.md lines 496-615)
├── branding-pdfs.md (use WEBSITE-DOCUMENTATION.md lines 618-824)
├── troubleshooting.md (use WEBSITE-DOCUMENTATION.md lines 881-1089)
├── developer-resources.md (use WEBSITE-DOCUMENTATION.md lines 1093-1273)
└── faq.md (use WEBSITE-DOCUMENTATION.md lines 1277-1449)
```

## 🎯 Usage

### Option 1: Individual Pages (Recommended)

Create separate pages on your website for each section:

```
yoursite.com/documentation/
├── getting-started/
├── installation/
├── user-guide/
├── admin-settings/
├── product-management/
├── branding-pdfs/
├── troubleshooting/
├── developer-resources/
└── faq/
```

### Option 2: Single Page

Use `../../WEBSITE-DOCUMENTATION.md` as one comprehensive page at:
```
yoursite.com/documentation/
```

### Option 3: Knowledge Base

Import sections into a knowledge base plugin:
- Each .md file becomes an article
- Organized by category
- Searchable
- Printable

## 📝 Completing the Structure

To finish creating individual pages, extract sections from `WEBSITE-DOCUMENTATION.md`:

### installation.md
```bash
# Extract lines 101-174
sed -n '101,174p' ../../WEBSITE-DOCUMENTATION.md > installation.md
# Add navigation header/footer
```

### user-guide.md
```bash
# Extract lines 176-363
sed -n '176,363p' ../../WEBSITE-DOCUMENTATION.md > user-guide.md
# Add navigation header/footer
```

### And so on for remaining sections...

## 🔗 Navigation

Each page should include:

**Header:**
```markdown
# Page Title
[← Back to Documentation](./index.md)
```

**Footer:**
```markdown
[← Back to Documentation](./index.md) | [Next: Section Name →](./next-section.md)
```

## 🎨 Converting to HTML

### Using Markdown Parser

**WordPress Plugins:**
- Jetpack Markdown
- WP Githuber MD
- Parsedown Party

**Command Line:**
```bash
# Using pandoc
pandoc getting-started.md -o getting-started.html

# Using markdown-it
markdown-it getting-started.md > getting-started.html
```

### CSS Styling

Add these classes to your theme:

```css
/* Documentation pages */
.doc-page { max-width: 800px; margin: 0 auto; }
.doc-nav { background: #f5f7fa; padding: 1rem; }
.doc-code { background: #f8f9fa; padding: 1rem; }
.doc-table { width: 100%; border-collapse: collapse; }
```

## 📊 Files Status

- ✅ `index.md` - Complete navigation hub
- ✅ `getting-started.md` - Complete with examples
- ⏳ `installation.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `user-guide.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `admin-settings.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `product-management.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `branding-pdfs.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `troubleshooting.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `developer-resources.md` - Content ready in WEBSITE-DOCUMENTATION.md
- ⏳ `faq.md` - Content ready in WEBSITE-DOCUMENTATION.md

## 🚀 Publishing Workflow

1. **Extract Sections** - Use line numbers above
2. **Add Navigation** - Header and footer links
3. **Convert to HTML** - Using parser of choice
4. **Upload to Site** - Via FTP or WordPress admin
5. **Test Links** - Ensure navigation works
6. **Add to Menu** - Include in site navigation

## 📧 Support

For questions about documentation structure:
- Email: developers@webstuffguylabs.com
- GitHub: (if public repo exists)

---

**Last Updated:** October 9, 2025
**Version:** 1.0.2
