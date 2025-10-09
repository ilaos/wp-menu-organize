# Enable HTML5 Parser for DomPDF

The plugin now **conditionally enables** the HTML5 parser if the required dependencies are installed.

## Current Status

✅ **Code Updated** - All 3 DomPDF instances now check for HTML5 parser availability
⚠️ **Dependencies Missing** - HTML5 parser requires composer packages
📦 **composer.json Created** - Ready for installation

---

## What's HTML5 Parser?

The HTML5 parser provides better support for:
- Modern HTML5 elements and attributes
- Better handling of clickable links in PDFs
- Improved CSS parsing
- More robust HTML parsing

**Currently using:** HTML4 parser (built-in, works fine for most PDFs)
**To enable HTML5:** Follow installation instructions below

---

## Installation Instructions

### Option 1: Using Command Line (Recommended)

```bash
# Navigate to plugin directory
cd "C:\Users\ishla\Local Sites\my-playground\app\public\wp-content\plugins\Submittal & Spec Sheet Builder"

# Run composer install
composer install --no-dev --ignore-platform-reqs
```

### Option 2: Using Local by Flywheel

Since Local's PHP doesn't have OpenSSL enabled, use your system's composer:

1. **Install Composer globally** (if not already installed):
   - Download from: https://getcomposer.org/download/
   - Run installer
   - Verify: `composer --version`

2. **Navigate to plugin directory in Command Prompt**:
   ```cmd
   cd "C:\Users\ishla\Local Sites\my-playground\app\public\wp-content\plugins\Submittal & Spec Sheet Builder"
   ```

3. **Install dependencies**:
   ```cmd
   composer install --no-dev --ignore-platform-reqs
   ```

### Option 3: Manual Installation (Alternative)

If composer won't work, manually download the packages:

1. **Download DomPDF 2.x**:
   - Visit: https://github.com/dompdf/dompdf/releases
   - Download latest v2.x release
   - Extract to: `vendor/dompdf/dompdf/`

2. **Download Masterminds HTML5**:
   - Visit: https://github.com/Masterminds/html5-php/releases
   - Download latest v2.8.x release
   - Extract to: `vendor/masterminds/html5/`

3. **Update vendor/autoload.php** to load these manually

---

## Verification

After installation, the plugin will automatically detect and enable HTML5 parser.

### Check if HTML5 Parser is Active

1. **Generate a PDF** from the frontend builder
2. **Check debug.log**:
   - If you see: `[SFB] HTML5 parser not available - using HTML4 parser`
     → HTML5 parser is **NOT** installed (using fallback)
   - If you DON'T see this message:
     → HTML5 parser is **ACTIVE** ✅

### Test the Parser

```php
// Add to your theme's functions.php temporarily
add_action('init', function() {
    if (class_exists('Masterminds\\HTML5')) {
        error_log('[TEST] HTML5 parser IS available');
    } else {
        error_log('[TEST] HTML5 parser NOT available');
    }
});
```

---

## What Happens Without HTML5 Parser?

✅ **PDFs still work perfectly** - The built-in HTML4 parser is sufficient
✅ **No fatal errors** - Plugin gracefully falls back
⚠️ **Some HTML5 features** may not be fully supported (e.g., complex nested links)

**Bottom line:** HTML5 parser is **optional but recommended** for best results.

---

## Troubleshooting

### Composer: "openssl extension is required"

**Solution 1:** Use `--ignore-platform-reqs` flag:
```bash
composer install --no-dev --ignore-platform-reqs
```

**Solution 2:** Enable OpenSSL in PHP:
- Edit `php.ini`
- Uncomment: `extension=openssl`
- Restart PHP/web server

**Solution 3:** Use system composer instead of Local's PHP

### Permission Errors

```bash
# Windows: Run Command Prompt as Administrator
# Then run composer install
```

### "Class not found" after installation

```bash
# Regenerate autoload files
composer dump-autoload --no-dev
```

---

## Files Modified

| File | Change |
|------|--------|
| `composer.json` | Added dompdf 2.x and masterminds/html5 dependencies |
| `vendor/autoload.php` | Updated to load composer packages if available |
| `submittal-form-builder.php` (line ~3710) | Conditional HTML5 parser check |
| `submittal-form-builder.php` (line ~4219) | Conditional HTML5 parser check |
| `submittal-form-builder.php` (line ~5865) | Conditional HTML5 parser check |

---

## Support

If you have issues installing composer packages, the plugin will continue to work with the HTML4 parser. No action required unless you specifically need HTML5 features.

For help, check `wp-content/debug.log` for `[SFB]` entries.
