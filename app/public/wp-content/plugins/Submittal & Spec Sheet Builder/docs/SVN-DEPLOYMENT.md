# SVN Deployment Guide
## Submittal & Spec Sheet Builder

This guide walks through the process of deploying your plugin to the WordPress.org plugin repository using SVN.

---

## Prerequisites

1. **SVN Client Installed**
   - Windows: [TortoiseSVN](https://tortoisesvn.net/)
   - Mac: `brew install svn`
   - Linux: `sudo apt-get install subversion`

2. **WordPress.org Account**
   - Create account at https://wordpress.org/support/register.php
   - Plugin must be approved before you receive SVN credentials

3. **Plugin Ready**
   - All files tested and working
   - readme.txt validated at https://wordpress.org/plugins/developers/readme-validator/
   - Banner and icon images created (see `assets/PLACEHOLDER-IMAGES-README.txt`)
   - Version number updated in plugin header and readme.txt

---

## Initial SVN Setup

### 1. Request Plugin Slug

1. Go to https://wordpress.org/plugins/developers/add/
2. Fill out the form:
   - Plugin Name: Submittal & Spec Sheet Builder
   - Plugin Description: (Copy from readme.txt)
   - Plugin URL: (Your GitHub/plugin homepage)
3. Submit and wait for approval (5-14 days)

### 2. Receive SVN Credentials

Once approved, you'll receive an email with:
- Your SVN repository URL: `https://plugins.svn.wordpress.org/submittal-builder/`
- Credentials (your WordPress.org username/password)

### 3. Checkout SVN Repository

```bash
# Create a local directory for SVN
mkdir wp-svn
cd wp-svn

# Checkout the repository
svn co https://plugins.svn.wordpress.org/submittal-builder/ submittal-builder

# Enter your WordPress.org username and password when prompted
```

---

## Repository Structure

Your local SVN directory will have this structure:

```
submittal-builder/
├── trunk/          # Development version (latest code)
├── tags/           # Release versions (1.0.0, 1.0.1, etc.)
├── assets/         # WordPress.org assets (banner, icon, screenshots)
└── branches/       # Optional development branches
```

**Important:**
- `trunk/` = Latest development code
- `tags/` = Stable releases only (tagged versions)
- `assets/` = Images for WordPress.org directory ONLY (not included in plugin ZIP)

---

## First Deployment (v1.0.0)

### Step 1: Copy Plugin Files to trunk/

```bash
# Navigate to your SVN directory
cd wp-svn/submittal-builder

# Copy all plugin files to trunk/ (exclude .git, node_modules, etc.)
cp -r /path/to/your/plugin/* trunk/

# Remove development files
rm -rf trunk/.git
rm -rf trunk/node_modules
rm -rf trunk/.github
rm trunk/.gitignore
rm trunk/SVN-DEPLOYMENT.md
```

**Files to include in trunk/:**
- submittal-form-builder.php (main plugin file)
- readme.txt
- uninstall.php
- includes/ (all PHP files)
- templates/ (all template files)
- assets/ (CSS, JS - NOT images for WordPress.org)
- languages/ (POT file)
- lib/ (Dompdf, FPDI libraries)

**Files to exclude:**
- .git, .github, .gitignore
- node_modules, package.json
- Development docs (this file, checklists)
- Test files

### Step 2: Add Assets to assets/

```bash
# Copy WordPress.org images to assets/ directory
cp banner-772x250.png assets/
cp banner-1544x500.png assets/  # Optional high-res version
cp icon-256x256.png assets/
cp icon-128x128.png assets/      # Optional retina version
cp screenshot-1.png assets/      # All screenshots
cp screenshot-2.png assets/
# ... etc
```

**Note:** Assets directory is separate from trunk and NOT included in plugin ZIP.

### Step 3: Add Files to SVN

```bash
# Add all new files
svn add trunk/* --force
svn add assets/* --force

# Check status
svn status
```

Output will show:
- `A` = Added files
- `?` = Untracked files (add with `svn add filename`)

### Step 4: Commit to trunk/

```bash
# Commit with a message
svn ci -m "Initial commit of Submittal & Spec Sheet Builder v1.0.0"

# Enter your WordPress.org password when prompted
```

### Step 5: Create Tag for v1.0.0

```bash
# Copy trunk to tags/1.0.0
svn cp trunk tags/1.0.0

# Commit the tag
svn ci -m "Tagging version 1.0.0"
```

**Important:** The `Stable tag` field in readme.txt must match the tag folder name!

---

## Updating the Plugin (Future Releases)

### For Version 1.0.1 (Example)

1. **Update Version Numbers:**
   - `submittal-form-builder.php` header: `Version: 1.0.1`
   - `readme.txt`: `Stable tag: 1.0.1`
   - `readme.txt` Changelog: Add v1.0.1 entry

2. **Update trunk/ with Changes:**

```bash
cd wp-svn/submittal-builder

# Copy updated files to trunk/
cp -r /path/to/your/plugin/* trunk/

# Check what changed
svn status

# Add any new files
svn add trunk/new-file.php

# Delete removed files
svn delete trunk/old-file.php

# Commit changes to trunk
svn ci -m "Update to version 1.0.1: Bug fixes and improvements"
```

3. **Create New Tag:**

```bash
# Copy trunk to tags/1.0.1
svn cp trunk tags/1.0.1

# Commit the tag
svn ci -m "Tagging version 1.0.1"
```

4. **WordPress.org will automatically:**
   - Update plugin in directory
   - Offer update to existing users
   - Show new version on plugin page

---

## Updating Assets Only

If you only need to update banner/icon/screenshots:

```bash
cd wp-svn/submittal-builder

# Copy new images to assets/
cp banner-772x250.png assets/

# Commit assets
svn ci -m "Update banner image" assets/
```

**Note:** Asset updates don't require a new version or tag.

---

## Best Practices

### Before Every Release:

1. ✅ Test plugin thoroughly on fresh WordPress install
2. ✅ Update version numbers in plugin header AND readme.txt
3. ✅ Update changelog in readme.txt
4. ✅ Validate readme.txt: https://wordpress.org/plugins/developers/readme-validator/
5. ✅ Run PHPCS: `vendor/bin/phpcs` (no errors)
6. ✅ Test uninstall.php cleanup
7. ✅ Update screenshots if UI changed

### SVN Best Practices:

- **Always commit to trunk first**, then tag
- **Never edit a tag** after committing (create a new version instead)
- **Use meaningful commit messages**
- **Test locally before committing**
- **Don't commit sensitive data** (API keys, .env files)

### Versioning:

- **Major release:** 1.0.0 → 2.0.0 (breaking changes)
- **Minor release:** 1.0.0 → 1.1.0 (new features)
- **Patch release:** 1.0.0 → 1.0.1 (bug fixes)

---

## Common SVN Commands

```bash
# Check status
svn status

# Update local copy from server
svn update

# Add new file
svn add filename.php

# Delete file
svn delete filename.php

# Revert local changes
svn revert filename.php

# View differences
svn diff filename.php

# View commit history
svn log

# Move/rename file
svn move old.php new.php
```

---

## Troubleshooting

### "File already exists in repository"

```bash
# Delete local file and update from server
rm filename.php
svn update
```

### "Working copy locked"

```bash
svn cleanup
```

### Forgot to update version in readme.txt

1. Fix readme.txt locally
2. Copy to trunk/
3. Commit: `svn ci -m "Update stable tag to 1.0.1" trunk/readme.txt`
4. If already tagged, update tag: `svn ci -m "Update stable tag" tags/1.0.1/readme.txt`

### Plugin not showing in directory after commit

- Check that `Stable tag` in readme.txt matches tag folder name
- Verify tag was committed: `svn ls https://plugins.svn.wordpress.org/submittal-builder/tags/`
- Wait 15-30 minutes for WordPress.org to update
- Check for errors in WordPress.org support forum

---

## Quick Reference

### Initial Release

```bash
# 1. Checkout
svn co https://plugins.svn.wordpress.org/submittal-builder/

# 2. Copy files
cp -r /path/to/plugin/* trunk/
cp banner.png icon.png assets/

# 3. Add and commit
svn add trunk/* assets/* --force
svn ci -m "Initial commit v1.0.0"

# 4. Tag release
svn cp trunk tags/1.0.0
svn ci -m "Tagging v1.0.0"
```

### Update Release

```bash
# 1. Update trunk
cp -r /path/to/plugin/* trunk/
svn status
svn ci -m "Update to v1.0.1"

# 2. Tag release
svn cp trunk tags/1.0.1
svn ci -m "Tagging v1.0.1"
```

---

## Resources

- **SVN Primer:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Detailed Guide:** https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/
- **readme.txt Validator:** https://wordpress.org/plugins/developers/readme-validator/
- **Plugin Handbook:** https://developer.wordpress.org/plugins/
- **Support Forum:** https://wordpress.org/support/plugin/submittal-builder/

---

**Last Updated:** 2025-01-10
**Version:** 1.0.0
**Plugin:** Submittal & Spec Sheet Builder
