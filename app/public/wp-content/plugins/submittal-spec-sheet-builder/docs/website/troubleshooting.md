# Troubleshooting Guide

[← Back to Documentation](./index.md)

**Solutions to common issues and problems**

---

## Overview

This guide provides solutions to the most common issues users encounter with Submittal & Spec Sheet Builder. Issues are organized by category for easy navigation.

---

## Quick Diagnostics

Before diving into specific issues, try these general troubleshooting steps:

### 1. Clear Browser Cache

Many issues resolve after clearing cache:
- **Chrome:** Ctrl+Shift+Delete → Clear browsing data
- **Firefox:** Ctrl+Shift+Delete → Clear data
- **Safari:** Cmd+Option+E → Empty caches
- **Edge:** Ctrl+Shift+Delete → Clear browsing data

### 2. Disable Browser Extensions

Extensions can interfere with plugin functionality:
1. Open browser in incognito/private mode
2. Test if issue persists
3. If resolved, disable extensions one by one to find culprit

### 3. Check WordPress Debug Log

Enable WordPress debugging to see error messages:
1. Edit `wp-config.php`
2. Add these lines:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```
3. Check `/wp-content/debug.log` for errors

### 4. Test with Default Theme

Theme conflicts are common:
1. Switch to Twenty Twenty-Five (or another default theme)
2. Test if issue persists
3. If resolved, contact theme developer

### 5. Disable Other Plugins

Plugin conflicts can cause issues:
1. Deactivate all other plugins
2. Test if issue persists
3. Reactivate plugins one by one to identify conflict

---

## PDF Generation Issues

### PDF Generation Fails

**Symptoms:**
- Error message appears
- Loading spinner never stops
- "Failed to generate PDF" message

**Common Causes & Solutions:**

---

#### Cause 1: Server Timeout

**Error:** "Maximum execution time exceeded"

**Solution:**
Increase PHP execution time:

**Via .htaccess:**
```apache
php_value max_execution_time 120
```

**Via php.ini:**
```ini
max_execution_time = 120
```

**Via WordPress:**
Add to `wp-config.php`:
```php
set_time_limit(120);
```

**Contact hosting support** if you don't have access to these files.

---

#### Cause 2: Memory Limit

**Error:** "Allowed memory size exhausted"

**Solution:**
Increase PHP memory limit:

**Via wp-config.php** (add near top):
```php
define('WP_MEMORY_LIMIT', '256M');
```

**Via .htaccess:**
```apache
php_value memory_limit 256M
```

**Via php.ini:**
```ini
memory_limit = 256M
```

**Recommended:** 256MB minimum, 512MB for large catalogs

---

#### Cause 3: Missing PHP Extensions

**Error:** "Required PHP extension missing"

**Required Extensions:**
- mbstring
- gd or imagick
- dom
- libxml

**Check Installed Extensions:**
```php
<?php phpinfo(); ?>
```

**Solution:**
Contact hosting provider to enable missing extensions.

---

#### Cause 4: File Permission Issues

**Error:** "Failed to write file" or "Permission denied"

**Solution:**

Check folder permissions:
```bash
# Should be 755
chmod 755 wp-content/uploads
chmod 755 wp-content/uploads/submittal-builder
```

Check file permissions:
```bash
# Should be 644
chmod 644 wp-content/uploads/submittal-builder/*.pdf
```

**On Windows servers:** Ensure IIS user has write permissions.

---

### PDF Generation is Very Slow

**Symptoms:**
- Takes 30+ seconds to generate
- Server becomes unresponsive
- Browser times out

**Solutions:**

**1. Reduce Products Per Packet**
- Generate smaller packets (10-15 products max)
- Split large submittals into multiple PDFs

**2. Optimize Server Resources**
- Upgrade hosting plan
- Increase PHP memory/execution limits
- Use dedicated server or VPS instead of shared hosting

**3. Disable Product Images**
- Go to Settings → Frontend
- Uncheck "Show Product Thumbnails"
- Images increase PDF generation time significantly

**4. Use DomPDF Engine**
- Go to Settings → PDF
- Select "DomPDF" as PDF engine
- Faster than TCPDF for most use cases

---

### PDF is Blank or Missing Content

**Symptoms:**
- PDF downloads but shows blank pages
- Missing product data
- Only cover page appears

**Solutions:**

**1. Check Product Data**
- Verify products have specifications filled in
- Ensure models exist under types, types under products, etc.
- Test with different products to isolate issue

**2. Verify Branding Settings**
- Ensure logo uploaded successfully
- Check brand color is set
- Verify company information is complete

**3. Test with Minimal Setup**
- Generate PDF with just 1-2 products
- If successful, issue is with specific products or large packets

**4. Check Console for Errors**
- Open browser DevTools (F12)
- Look for JavaScript errors
- Check Network tab for failed requests

---

### PDF Formatting Issues

**Symptoms:**
- Text overlaps
- Tables cut off
- Poor alignment
- Fonts look wrong

**Solutions:**

**1. Page Size Mismatch**
- Go to Settings → PDF → Page Size
- Ensure correct size selected (Letter vs A4)
- Regenerate PDF

**2. Long Product Names**
- Shorten product model names if excessively long
- Use abbreviations where appropriate
- Check if text wraps properly in preview

**3. Browser-Specific Rendering**
- Try opening PDF in different browser
- Download PDF and open in Adobe Reader
- Browser PDF viewers can render differently

**4. Try Different Theme (Pro)**
- Go to Settings → PDF
- Select different PDF theme
- Some themes handle long content better

---

## Frontend Builder Issues

### Products Not Displaying

**Symptoms:**
- Builder page shows "No products found"
- Product gallery is empty
- Search returns no results

**Solutions:**

**1. Verify Products Exist**
- Go to Admin → Submittal Builder → Catalog Builder
- Ensure products are added and visible
- Check that models have specifications filled in

**2. Check Product Hierarchy**
- Products must have complete hierarchy: Category → Product → Type → Model
- Missing levels will cause products not to appear

**3. Clear Cache**
- Clear browser cache
- If using caching plugin (WP Super Cache, W3 Total Cache):
  - Go to plugin settings
  - Clear all caches
- Regenerate page

**4. Check Shortcode**
- Verify page contains `[submittal_builder]`
- Check for typos in shortcode
- Ensure shortcode is not inside quote marks

**5. JavaScript Errors**
- Open browser console (F12)
- Look for red error messages
- Common issue: Other plugins/theme causing conflicts

---

### Search Not Working

**Symptoms:**
- Typing in search box does nothing
- Results don't filter
- Search box grayed out

**Solutions:**

**1. Enable Search in Settings**
```
Admin → Settings → General → Enable Search
```
Ensure checkbox is checked.

**2. Check for JavaScript Errors**
- Open browser console (F12)
- Look for errors related to search
- Conflicting scripts can break search

**3. Update Browser**
- Ensure browser is updated to latest version
- Try different browser to test

**4. Disable Conflicting Plugins**
- Some plugins interfere with AJAX search
- Temporarily disable other plugins to test

---

### Can't Select Products

**Symptoms:**
- Clicking products doesn't select them
- Selection count doesn't update
- "Continue" button grayed out

**Solutions:**

**1. Enable JavaScript**
- Verify JavaScript is enabled in browser
- Check browser settings
- Some security plugins disable JavaScript

**2. Clear Browser Cache**
- Often resolves selection issues
- Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

**3. Check Console for Errors**
- Open DevTools (F12)
- Look for JavaScript errors
- May indicate conflict with theme or other plugin

**4. Test Different Browser**
- Try Chrome, Firefox, or Edge
- If works in one browser, issue is browser-specific

---

### Images/Thumbnails Not Showing

**Symptoms:**
- Product cards show broken image icons
- Thumbnails missing in gallery view

**Solutions:**

**1. Ensure Feature Enabled**
```
Settings → Frontend → Show Product Thumbnails
```
Enable checkbox.

**2. Upload Product Images**
- Products need featured images set
- Go to Catalog Builder → click product → upload image

**3. Check Image Permissions**
- Verify `/wp-content/uploads/` folder is readable
- Permissions should be 755

**4. Regenerate Thumbnails**
- Use plugin like "Regenerate Thumbnails"
- Rebuild all image sizes

---

## Admin Interface Issues

### Catalog Builder Not Loading

**Symptoms:**
- Tree view doesn't appear
- Spinner loads forever
- Blank admin page

**Solutions:**

**1. Check for JavaScript Errors**
- Open browser console (F12)
- Look for errors
- Note error messages for support

**2. Increase Memory Limit**
- Large catalogs require more memory
- Increase to 256MB minimum (see memory solution above)

**3. Disable Other Plugins**
- Admin conflicts are common
- Deactivate other plugins temporarily

**4. Try Different Browser**
- Test in Chrome, Firefox, Edge
- Clear browser cache first

---

### Can't Add/Edit Products

**Symptoms:**
- "Add Product" button doesn't work
- Inspector panel won't open
- Changes don't save

**Solutions:**

**1. Check User Permissions**
- Ensure logged in as Administrator
- Must have `manage_options` capability

**2. Clear Browser Cache**
- Hard refresh: Ctrl+F5 or Cmd+Shift+R
- Clear all cached data

**3. Check for AJAX Errors**
- Open browser console
- Look for network errors (red in Network tab)
- May indicate server issue or permission problem

**4. Verify REST API Working**
- Visit: `yoursite.com/wp-json/sfb/v1/nodes`
- Should see JSON data, not error
- If error, REST API may be disabled or blocked

---

### Settings Won't Save

**Symptoms:**
- Click "Save Changes" but settings revert
- No success message appears
- Changes don't persist

**Solutions:**

**1. Check for Error Messages**
- Look for red error notices at top of admin page
- Check browser console for JavaScript errors

**2. Verify User Permissions**
- Must be Administrator
- Check user role in Users → Your Profile

**3. Disable Security Plugins Temporarily**
- Security plugins can block POST requests
- Temporarily disable to test

**4. Check for Plugin Conflicts**
- Deactivate other plugins one by one
- Reactivate after identifying conflict

---

## License & Activation Issues

### License Key Won't Activate

**Symptoms:**
- "Invalid license key" error
- Activation fails
- Pro features not unlocking

**Solutions:**

**1. Verify License Key**
- Copy key carefully from purchase email
- Avoid extra spaces before/after
- Check for missing characters

**2. Check Site URL**
- License tied to specific domain
- Verify you're on correct site
- Contact support to change domain if needed

**3. Maximum Activations Reached**
- Pro Single Site: 1 site max
- Pro Agency: 5 sites max
- Deactivate on unused sites first

**4. License Expired**
- Check renewal date in purchase email
- Renew license to restore access
- Expired licenses show as "Expired" status

**5. Check Internet Connection**
- Activation requires connection to license server
- Verify server can reach external APIs
- Some firewalls block outbound requests

---

### Pro Features Not Working

**Symptoms:**
- License shows active but features disabled
- Tracking page not accessible
- Lead capture not showing

**Solutions:**

**1. Verify License Tier**
- Free: No Pro features
- Pro Single Site: Pro features only
- Pro Agency: All features

**2. Reactivate License**
- Deactivate license
- Wait 30 seconds
- Reactivate
- Force refresh of feature availability

**3. Clear Cache**
- Clear WordPress object cache
- Clear browser cache
- Deactivate caching plugins temporarily

**4. Check Feature Settings**
- Some Pro features require manual enablement
- Go to Settings → Pro
- Enable specific features

---

## Performance Issues

### Slow Page Load Times

**Symptoms:**
- Frontend builder takes 5+ seconds to load
- Admin pages slow
- Browser becomes unresponsive

**Solutions:**

**1. Enable Caching**
```
Settings → Advanced → Enable Caching
```

**2. Reduce Products Per Page**
```
Settings → General → Products Per Page
```
Lower to 25-50 for large catalogs

**3. Optimize Images**
```
Settings → Advanced → Optimize Images
```
Enable to compress product images

**4. Use Caching Plugin**
- Install WP Super Cache or W3 Total Cache
- Configure page caching
- Exclude builder page if dynamic features needed

**5. Upgrade Hosting**
- Shared hosting struggles with large catalogs
- VPS or managed WordPress hosting recommended
- At least 2GB RAM for 500+ products

---

### High Memory Usage

**Symptoms:**
- "Memory exhausted" errors
- Server crashes
- Very slow performance

**Solutions:**

**1. Increase PHP Memory**
(See memory limit solutions above)

**2. Reduce Catalog Size**
- Remove unused products
- Archive old products
- Split into multiple catalogs if possible

**3. Disable Product Images**
- Images consume significant memory
- Disable in Settings → Frontend

**4. Optimize Database**
- Use plugin like WP-Optimize
- Clean up post revisions and transients

---

## Browser-Specific Issues

### Issues in Internet Explorer

**Status:** Internet Explorer is not supported.

**Solution:** Use modern browser:
- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari

---

### Issues in Safari

**Common Issues:**
- PDF download prompts multiple times
- Visual glitches in builder

**Solutions:**
- Update Safari to latest version
- Clear Safari cache: Cmd+Option+E
- Disable Safari extensions
- Try Chrome or Firefox as alternative

---

### Issues on Mobile

**Common Issues:**
- Touch targets too small
- Drag and drop not working
- PDF generation slow

**Solutions:**
- Use tablet instead of phone when possible
- Switch to landscape mode
- Reduce number of products per PDF
- Download PDF via desktop computer for best results

---

## Error Messages Explained

### "Nonce verification failed"

**Meaning:** Security token expired or invalid.

**Solution:**
- Refresh page and try again
- Clear browser cache
- Log out and log back in

---

### "You do not have permission"

**Meaning:** User lacks required capabilities.

**Solution:**
- Ensure logged in as Administrator
- Check user role in Users → Your Profile
- Contact site admin if not administrator

---

### "REST API error"

**Meaning:** WordPress REST API not accessible.

**Solution:**
- Verify REST API enabled in Settings → Advanced
- Check if security plugin blocking API
- Test: visit `yoursite.com/wp-json/`
- Should see JSON response, not error

---

### "File upload failed"

**Meaning:** Server couldn't save uploaded file.

**Solution:**
- Check file permissions (see solutions above)
- Verify disk space available
- Check PHP upload limits:
```ini
upload_max_filesize = 64M
post_max_size = 64M
```

---

## Getting Additional Help

If issues persist after trying solutions above:

### Before Contacting Support

1. **Note Error Messages**
   - Exact wording of errors
   - When they occur
   - Steps to reproduce

2. **Check Debug Log**
   - Enable WP_DEBUG
   - Check `/wp-content/debug.log`
   - Copy relevant error lines

3. **Gather System Info**
   - WordPress version
   - PHP version
   - Plugin version
   - Active theme
   - Active plugins

4. **Test in Isolation**
   - Default theme
   - Minimal plugins
   - Does issue persist?

### Support Channels

**WordPress.org Forum:**
https://wordpress.org/support/plugin/submittal-builder/

**Pro Email Support:**
support@webstuffguylabs.com (Pro license holders)

**Include in Support Request:**
- Description of issue
- Steps to reproduce
- Error messages (exact wording)
- System information
- Screenshots if applicable

---

## Prevention Tips

### Regular Maintenance

✅ **Do:**
- Keep WordPress updated
- Keep PHP updated
- Update plugin when new version released
- Regular database optimization
- Weekly backups

❌ **Don't:**
- Use outdated software
- Ignore update notices
- Skip backups
- Use unsupported PHP versions

---

### Best Practices

✅ **Do:**
- Use modern browsers
- Clear cache regularly
- Test changes on staging site first
- Monitor error logs
- Use quality hosting

❌ **Don't:**
- Use Internet Explorer
- Install conflicting plugins
- Ignore performance issues
- Disable security features without reason

---

## Next Steps

### Related Documentation

- [Installation Guide](./installation.md) - Setup and requirements
- [Admin Settings](./admin-settings.md) - Configuration reference
- [FAQ](./faq.md) - Frequently asked questions
- [Developer Resources](./developer-resources.md) - Advanced customization

### Still Need Help?

- **Documentation:** [Full Documentation](./index.md)
- **WordPress.org Forum:** https://wordpress.org/support/plugin/submittal-builder/
- **Email Support:** support@webstuffguylabs.com (Pro users)

---

[← Back to Documentation](./index.md) | [Next: Developer Resources →](./developer-resources.md)

---

© 2025 Webstuffguy Labs. All rights reserved.
