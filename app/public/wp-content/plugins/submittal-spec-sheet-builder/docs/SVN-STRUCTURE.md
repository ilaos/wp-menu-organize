# SVN Repository Structure for WordPress.org

## Overview

WordPress.org plugins are hosted on **SVN (Subversion)**, not Git. You'll need to check out your plugin's SVN repository and commit files to it for public release.

---

## Standard SVN Directory Structure

```
submittal-builder/               (SVN root)
├── trunk/                       (Development version - always latest code)
│   ├── submittal-form-builder.php
│   ├── readme.txt
│   ├── uninstall.php
│   ├── assets/
│   │   ├── admin.css
│   │   ├── admin.js
│   │   ├── app.css
│   │   └── app.js
│   ├── includes/
│   │   ├── pro/
│   │   │   └── registry.php
│   │   └── ...
│   ├── languages/
│   │   └── submittal-builder.pot
│   ├── templates/
│   │   ├── admin/
│   │   │   ├── builder.php
│   │   │   ├── branding.php
│   │   │   ├── onboarding.php
│   │   │   └── upgrade.php
│   │   └── pdf/
│   │       ├── cover.html.php
│   │       ├── toc.html.php
│   │       ├── summary.html.php
│   │       └── model-sheet.html.php
│   └── vendor/                  (DOMPDF library)
│       └── ...
├── tags/                        (Stable releases - copied from trunk)
│   ├── 1.0.0/
│   │   ├── (same structure as trunk, frozen snapshot)
│   ├── 1.0.1/
│   │   └── ...
│   └── 1.1.0/
│       └── ...
└── assets/                      (WordPress.org display assets - NOT in plugin ZIP)
    ├── banner-772x250.png       (Plugin page header banner)
    ├── icon-256x256.png         (Plugin icon, large)
    ├── icon-128x128.png         (Plugin icon, medium - optional)
    ├── screenshot-1.png         (Builder interface)
    ├── screenshot-2.png         (Sidebar details)
    ├── screenshot-3.png         (Branding settings)
    ├── screenshot-4.png         (Upgrade page)
    ├── screenshot-5.png         (PDF packet preview)
    └── screenshot-6.png         (PDF product sheet)
```

---

## Key Directories Explained

### 1. `trunk/` (Development Branch)
- **Purpose:** Contains the latest, actively developed version of your plugin
- **Contents:** All plugin files (PHP, CSS, JS, templates, vendor libraries)
- **Usage:** Always commit new changes here first
- **Important:** This is what users see when they install the "latest version" from WordPress.org

**Files to Include:**
- Main plugin file: `submittal-form-builder.php`
- `readme.txt` (WordPress.org plugin description)
- `uninstall.php` (cleanup script)
- All plugin directories: `assets/`, `includes/`, `templates/`, `languages/`, `vendor/`

**Files to EXCLUDE:**
- `.git/`, `.gitignore` (Git-specific files)
- `node_modules/` (if using Node/npm - should be built before SVN commit)
- `.DS_Store`, `Thumbs.db` (OS-specific files)
- Development-only files (e.g., `.editorconfig`, `phpcs.xml`)

---

### 2. `tags/` (Stable Releases)
- **Purpose:** Frozen snapshots of each released version
- **Contents:** Copy of `trunk/` at the time of release
- **Naming:** Use semantic versioning (1.0.0, 1.0.1, 1.1.0, etc.)
- **Usage:** Created when you're ready to release a new stable version

**How to Create a Tag:**
```bash
# Copy trunk to new version tag
svn cp trunk tags/1.0.0

# Commit the tag
svn commit -m "Tagging version 1.0.0"
```

**Version Workflow:**
1. Develop in `trunk/`
2. When ready to release, copy `trunk/` → `tags/X.Y.Z/`
3. Update `Stable tag` in `trunk/readme.txt` to match tag version
4. WordPress.org serves the version specified in `Stable tag` field

---

### 3. `assets/` (WordPress.org Display Assets)
- **Purpose:** Images shown on your WordPress.org plugin page
- **Contents:** Banners, icons, screenshots
- **Important:** These files are NOT included in the downloadable plugin ZIP
- **Location:** Root level of SVN (same level as `trunk/` and `tags/`)

**Required Files:**
- `banner-772x250.png` - Plugin page header banner
- `icon-256x256.png` - Plugin icon (search results, admin)

**Optional Files:**
- `icon-128x128.png` - Retina icon (WordPress auto-generates if missing)
- `screenshot-1.png` through `screenshot-6.png` - Feature screenshots

**Image Specifications:**
- Banner: 772×250px, PNG
- Icon: 256×256px, PNG (square)
- Screenshots: 1200-2000px wide, PNG or JPG

---

## SVN Workflow (Step-by-Step)

### Initial Setup (One-Time)

1. **Request SVN Access**
   - Submit your plugin to WordPress.org
   - Wait for approval (1-2 weeks)
   - You'll receive SVN repository URL: `https://plugins.svn.wordpress.org/submittal-builder/`

2. **Checkout SVN Repository**
   ```bash
   svn co https://plugins.svn.wordpress.org/submittal-builder submittal-builder-svn
   cd submittal-builder-svn
   ```

3. **Verify Structure**
   ```bash
   ls -la
   # Should see: trunk/, tags/, assets/
   ```

---

### Adding Your Plugin (First Release)

1. **Copy Plugin Files to `trunk/`**
   ```bash
   # Copy all plugin files from your development directory
   cp -R /path/to/your/plugin/* trunk/

   # EXCLUDE Git files
   rm -rf trunk/.git trunk/.gitignore
   ```

2. **Add Assets to `assets/`**
   ```bash
   # Copy banner and icon
   cp /path/to/banner-772x250.png assets/
   cp /path/to/icon-256x256.png assets/

   # Copy screenshots
   cp /path/to/screenshot-*.png assets/
   ```

3. **Add Files to SVN**
   ```bash
   svn add trunk/*
   svn add assets/*
   ```

4. **Commit to SVN**
   ```bash
   svn commit -m "Initial commit: Submittal Builder v1.0.0"
   ```

5. **Create First Release Tag**
   ```bash
   svn cp trunk tags/1.0.0
   svn commit -m "Tagging version 1.0.0"
   ```

6. **Update `readme.txt` Stable Tag**
   - Edit `trunk/readme.txt`
   - Set `Stable tag: 1.0.0`
   - Commit:
     ```bash
     svn commit -m "Update stable tag to 1.0.0"
     ```

---

### Releasing a New Version (Updates)

1. **Make Changes in `trunk/`**
   ```bash
   cd trunk/
   # Edit files as needed
   ```

2. **Update Version Numbers**
   - `submittal-form-builder.php`: Change `Version: 1.0.1` in plugin header
   - `readme.txt`: Update `Stable tag: 1.0.1` and add changelog entry

3. **Commit Changes to `trunk/`**
   ```bash
   svn commit -m "Version 1.0.1: Bug fixes and improvements"
   ```

4. **Create New Tag**
   ```bash
   svn cp trunk tags/1.0.1
   svn commit -m "Tagging version 1.0.1"
   ```

5. **WordPress.org Auto-Updates**
   - Within 15 minutes, users will see update notification
   - Downloads will serve files from `tags/1.0.1/`

---

### Updating Assets (Banner/Icon/Screenshots)

Assets can be updated independently of plugin code:

```bash
cd assets/

# Replace existing files
cp /path/to/new-banner.png banner-772x250.png
cp /path/to/new-screenshot-1.png screenshot-1.png

# Commit changes
svn commit -m "Update banner and screenshot 1"
```

**Note:** Asset updates appear on WordPress.org within 15 minutes (no tag required).

---

## SVN Commands Cheat Sheet

| Command | Purpose |
|---------|---------|
| `svn co URL` | Checkout (clone) repository |
| `svn add FILE` | Stage new file for commit |
| `svn delete FILE` | Remove file from repository |
| `svn commit -m "message"` | Commit changes with message |
| `svn update` | Pull latest changes from server |
| `svn status` | Show modified/new files |
| `svn diff` | Show changes since last commit |
| `svn cp SRC DEST` | Copy (used for tagging) |
| `svn revert FILE` | Undo local changes |

---

## Common SVN Scenarios

### Scenario 1: Fix Typo in `readme.txt`
```bash
cd trunk/
# Edit readme.txt
svn commit -m "Fix typo in readme.txt"
```
**Note:** No new tag needed for readme-only changes.

---

### Scenario 2: Add New Screenshot
```bash
cd assets/
cp /path/to/screenshot-7.png screenshot-7.png
svn add screenshot-7.png
svn commit -m "Add screenshot 7"

# Update readme.txt to describe it
cd ../trunk/
# Edit readme.txt, add caption for screenshot 7
svn commit -m "Add screenshot 7 caption to readme"
```

---

### Scenario 3: Emergency Bug Fix
```bash
cd trunk/
# Fix bug in submittal-form-builder.php
# Update version to 1.0.2 in plugin header
# Update readme.txt stable tag to 1.0.2

svn commit -m "Emergency bug fix: Resolve fatal error on PHP 8.1"

# Tag immediately
svn cp trunk tags/1.0.2
svn commit -m "Tagging version 1.0.2"
```

---

### Scenario 4: Rollback to Previous Version
```bash
# Copy old tag back to trunk
svn cp tags/1.0.0 trunk --force
svn commit -m "Rollback to 1.0.0 due to critical bug in 1.0.1"

# Update stable tag
cd trunk/
# Edit readme.txt, set Stable tag: 1.0.0
svn commit -m "Set stable tag back to 1.0.0"
```

---

## WordPress.org Version Control Logic

**How WordPress.org determines which version to serve:**

1. Reads `trunk/readme.txt` → `Stable tag:` field
2. Serves files from `tags/{stable-tag}/`
3. If tag doesn't exist, falls back to `trunk/`

**Example:**
```
trunk/readme.txt:  Stable tag: 1.0.3
tags/1.0.3/  (exists)
→ Users download from tags/1.0.3/
```

**Important:** Always ensure:
- `Stable tag` in `readme.txt` matches an existing tag
- Version in `submittal-form-builder.php` header matches tag version

---

## Validation Checklist Before First SVN Commit

- [ ] `trunk/readme.txt` exists and passes [WordPress.org validator](https://wordpress.org/plugins/developers/readme-validator/)
- [ ] Plugin header in `submittal-form-builder.php` has correct version
- [ ] `Stable tag` in `readme.txt` matches plugin header version
- [ ] All 6 screenshots exist in `assets/` and are referenced in `readme.txt`
- [ ] Banner (`banner-772x250.png`) and icon (`icon-256x256.png`) exist in `assets/`
- [ ] No `.git/`, `node_modules/`, or development files in `trunk/`
- [ ] `languages/submittal-builder.pot` exists (even if empty placeholder)
- [ ] `uninstall.php` exists and cleans up options/uploads
- [ ] `Text Domain: submittal-builder` in plugin header matches slug
- [ ] No PHP errors when activating plugin

---

## Troubleshooting

### "SVN: No such revision"
**Cause:** Trying to copy from non-existent tag
**Fix:** Ensure you're copying from `trunk/` or an existing tag

### "SVN: File already exists"
**Cause:** Adding a file that's already tracked
**Fix:** Use `svn commit` instead of `svn add`

### "Plugin not updating for users"
**Cause:** Stable tag mismatch
**Fix:** Verify `trunk/readme.txt` → `Stable tag:` matches tag directory name

### "Banner/icon not showing on WordPress.org"
**Cause:** Files not in `assets/` directory or wrong naming
**Fix:** Ensure files are in SVN root `assets/`, not `trunk/assets/`

---

## Resources

- **SVN Tutorial:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Plugin Handbook:** https://developer.wordpress.org/plugins/
- **Readme Validator:** https://wordpress.org/plugins/developers/readme-validator/
- **Asset Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
- **SVN Cheat Sheet:** https://tortoisesvn.net/docs/release/TortoiseSVN_en/tsvn-quickstart.html

---

## Next Steps

1. Wait for WordPress.org plugin approval email (includes SVN URL)
2. Checkout SVN repository locally
3. Copy plugin files to `trunk/`
4. Copy assets to `assets/`
5. Commit initial version to SVN
6. Create version tag (`tags/1.0.0/`)
7. Monitor WordPress.org for plugin page to go live (15-30 min after commit)
