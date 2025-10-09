# Banner & Icon Design Specifications

## Required Files for WordPress.org Submission

### 1. Banner (Header Image)
**File:** `assets/banner-772x250.png`
- **Dimensions:** 772px × 250px
- **Format:** PNG (with transparency support)
- **Purpose:** Displays at the top of your plugin page on WordPress.org

**Design Specifications:**
- **Background:** Light neutral (#F7F7F8) or soft tint of primary color
- **Safe Area:** Keep important content within 700×220px (leave 36px margin on sides, 15px top/bottom)
- **Title:** "Submittal & Spec Sheet Builder"
  - Position: Center-left (50px from left edge)
  - Font: Inter/SF Pro/Roboto, Bold
  - Size: 42-48px
  - Color: #111827 (dark gray) or your primary brand color
- **Subtitle:** "Branded submittal packets in minutes"
  - Position: Below title (8-12px spacing)
  - Font: Inter/SF Pro/Roboto, Medium
  - Size: 18-22px
  - Color: #6B7280 (medium gray)
- **Accent Stripe:** 6–10px horizontal bar along bottom edge
  - Color: Your primary_color (e.g., #7c3aed for engineering theme)
  - Full width (772px)
- **Glyph/Icon:** Document + gear illustration
  - Position: Right side (600-720px from left)
  - Size: 100-112px height (40-45% of banner height)
  - Style: Simple, bold shapes; avoid fine details
  - Color: Matches accent stripe or subtle gradient

**Canva Template Link:** [Create 772×250px design]
**Figma Template:** Use Frame 772×250px, export as PNG

---

### 2. Plugin Icon (Square)
**File:** `assets/icon-256x256.png`
- **Dimensions:** 256px × 256px
- **Format:** PNG (with transparency)
- **Purpose:** Shows in plugin search results, admin sidebar, and plugin cards

**Design Specifications:**
- **Background:**
  - Rounded square (16px corner radius recommended)
  - Soft neutral background (#F7F7F8 or #FAFAFA)
  - OR: Subtle gradient using primary color (10-15% opacity)
- **Central Glyph:**
  - Stacked documents icon (3 layered rectangles with slight offset)
  - Add small star ⭐ or gear ⚙️ in top-right corner of stack
  - Size: 140-160px (55-62% of canvas)
  - Position: Centered
  - Color: Your primary_color or dark gray (#111827)
- **Border:**
  - Thin stroke around rounded square
  - Width: 2-3px
  - Color: Your primary_color
  - Style: Solid or subtle gradient
- **Typography:** AVOID small text on icon (WordPress will downscale to 128px and 64px)

**Optional Retina Version:**
**File:** `assets/icon-128x128.png`
- Same design as 256×256, scaled down
- WordPress auto-generates smaller sizes, so this is optional

---

## Color Palette Recommendations

Based on your plugin's theme system:

| Theme | Primary Color | Accent Bar | Background |
|-------|--------------|------------|------------|
| **Engineering** | #111827 (dark gray) | #111827 | #F7F7F8 |
| **Architectural** | #0ea5e9 (sky blue) | #0ea5e9 | #F0F9FF |
| **Corporate** | #10b981 (emerald green) | #10b981 | #F0FDF4 |

**Safe Default:** Use Engineering theme colors for universal appeal.

---

## Design Tools & Resources

### Canva (Free)
1. Go to Canva.com
2. Create Custom Size: 772×250px (banner) and 256×256px (icon)
3. Use "Elements" → Search "document icon" and "gear icon"
4. Add text with Google Fonts (Inter or Roboto)
5. Download as PNG (transparent background)

### Figma (Free)
1. Create Frame: 772×250 (banner) or 256×256 (icon)
2. Use Rectangle Tool for shapes
3. Add Text Layers with SF Pro or Inter
4. Export as PNG @ 1x (no scaling needed)

### Adobe Photoshop/Illustrator
- Use provided dimensions
- Export as PNG-24 with transparency
- Optimize with TinyPNG or ImageOptim

### Free Icon Resources
- **Icons:** Heroicons.com, Feather Icons, Font Awesome (document, file, gear, star)
- **Fonts:** Google Fonts (Inter, Roboto), SF Pro (macOS system font)

---

## WordPress.org Display Preview

Your banner and icon will appear:
- **Plugin Search Results:** Icon 128×128 next to plugin name
- **Plugin Page Header:** Banner 772×250 at top
- **Plugin Cards:** Icon 64×64 on plugin directory cards
- **Install Screen:** Icon 256×256 during installation

**Tip:** Preview your designs at different sizes (256px, 128px, 64px) to ensure readability.

---

## File Checklist

Before submitting to WordPress.org:

- [ ] `banner-772x250.png` exists in `assets/` directory
- [ ] `icon-256x256.png` exists in `assets/` directory
- [ ] Both files are PNG format (not JPG)
- [ ] Files are optimized (<100KB each recommended)
- [ ] Icon remains clear when scaled to 64×64px
- [ ] Banner text is readable on light and dark backgrounds
- [ ] Colors match your plugin's branding theme
- [ ] No copyrighted or trademarked content used

---

## Next Steps

1. Create designs using Canva or Figma (templates above)
2. Export as PNG with transparency
3. Optimize images with TinyPNG.com (optional, reduces file size)
4. Save to `assets/` directory:
   - `banner-772x250.png`
   - `icon-256x256.png`
5. Commit to SVN: Copy to `assets/` folder in SVN repository (separate from plugin trunk)

**WordPress.org Asset Guidelines:**
https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
