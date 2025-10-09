<?php
/**
 * Homepage Hero Section
 * Marketing landing page template for Submittal & Spec Sheet Builder
 *
 * Usage: Copy this HTML to your theme's homepage, landing page template,
 * or use as Gutenberg blocks (HTML block or custom blocks).
 */

if (!defined('ABSPATH')) exit;
?>

<!-- Hero Section -->
<section class="sfb-hero" style="padding:64px 0;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <h1 style="font-size:44px;line-height:1.1;margin:0 0 16px;">
      Build professional submittal & spec packets — in minutes
    </h1>
    <p style="font-size:20px;color:#4b5563;max-width:820px;margin:0 0 24px;">
      Not a shopping cart. A project documentation tool. Let visitors (or your team) select items from your catalog and export a branded PDF packet — cover, summary, TOC, and detailed spec sheets — ready for approvals, RFQs, and field teams.
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
      <a href="#demo" class="btn btn-primary" style="padding:12px 18px;border-radius:8px;text-decoration:none;background:#7c3aed;color:#fff;font-weight:600;">See Live Demo</a>
      <a href="#features" class="btn btn-secondary" style="padding:12px 18px;border-radius:8px;text-decoration:none;border:1px solid #e5e7eb;color:#374151;font-weight:600;">Explore Features</a>
    </div>
    <ul style="display:flex;gap:18px;flex-wrap:wrap;color:#374151;margin:0;padding:0;list-style:none;">
      <li>Architects & Engineers</li>
      <li>Manufacturers & Reps</li>
      <li>Distributors</li>
      <li>Contractors/Subs</li>
    </ul>
  </div>
</section>

<!-- Social Proof -->
<section id="social-proof" style="padding:8px 0 0;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <p style="color:#6b7280;margin:0;">Trusted for technical selections, RFQs, and approvals.</p>
  </div>
</section>

<!-- Outcomes Section -->
<section id="outcomes" style="padding:32px 0;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;">
      <div>
        <h3 style="margin:0 0 8px;font-size:18px;color:#111827;">Fewer back-and-forths</h3>
        <p style="color:#4b5563;margin:0;line-height:1.6;">Standardized packets reduce clarifications and delays.</p>
      </div>
      <div>
        <h3 style="margin:0 0 8px;font-size:18px;color:#111827;">On-brand PDFs</h3>
        <p style="color:#4b5563;margin:0;line-height:1.6;">Your logo, colors, headers/footers, page numbers.</p>
      </div>
      <div>
        <h3 style="margin:0 0 8px;font-size:18px;color:#111827;">Faster approvals</h3>
        <p style="color:#4b5563;margin:0;line-height:1.6;">Clear specs for owners, reviewers, and field teams.</p>
      </div>
    </div>
  </div>
</section>

<!-- Feature Snack -->
<section id="features" style="padding:40px 0;background:#f9fafb;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <h2 style="font-size:28px;margin:0 0 24px;color:#111827;">Core Features</h2>
    <ul style="margin:0;padding:0;list-style:none;display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;">
      <li style="display:flex;gap:12px;">
        <span style="color:#7c3aed;font-weight:bold;">•</span>
        <span style="color:#374151;">Packet mode: Cover, Summary, Table of Contents</span>
      </li>
      <li style="display:flex;gap:12px;">
        <span style="color:#7c3aed;font-weight:bold;">•</span>
        <span style="color:#374151;">Clickable TOC links & anchors; page X of Y</span>
      </li>
      <li style="display:flex;gap:12px;">
        <span style="color:#7c3aed;font-weight:bold;">•</span>
        <span style="color:#374151;">Spec tables auto-render from your catalog meta</span>
      </li>
      <li style="display:flex;gap:12px;">
        <span style="color:#7c3aed;font-weight:bold;">•</span>
        <span style="color:#374151;">Branding: logo, colors, footer, headers/footers</span>
      </li>
      <li style="display:flex;gap:12px;">
        <span style="color:#7c3aed;font-weight:bold;">•</span>
        <span style="color:#374151;">Local autosave (Free) + Save & Share drafts (Pro)</span>
      </li>
    </ul>
  </div>
</section>

<!-- CTAs Section -->
<section id="cta" style="padding:64px 0;background:#7c3aed;color:#fff;text-align:center;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <h2 style="font-size:32px;margin:0 0 16px;color:#fff;">Ready to streamline your submittal process?</h2>
    <p style="font-size:18px;margin:0 0 32px;opacity:0.95;">Start building professional PDF packets today.</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
      <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-demo-tools')); ?>" class="btn" style="padding:14px 28px;border-radius:8px;text-decoration:none;background:#fff;color:#7c3aed;font-weight:600;font-size:16px;">Generate a sample packet</a>
      <a href="<?php echo esc_url(admin_url('admin.php?page=sfb')); ?>" class="btn" style="padding:14px 28px;border-radius:8px;text-decoration:none;background:rgba(255,255,255,0.2);color:#fff;font-weight:600;font-size:16px;border:2px solid rgba(255,255,255,0.4);">Try the live builder</a>
    </div>
    <p style="margin:24px 0 0;font-size:14px;opacity:0.8;">
      <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-upgrade')); ?>" style="color:#fff;text-decoration:underline;">Save & Share drafts (Pro)</a>
    </p>
  </div>
</section>

<!-- Pro Upsell Strip -->
<section id="pro-upsell" style="padding:24px 0;background:#111827;color:#d1d5db;text-align:center;">
  <div class="container" style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <p style="margin:0;font-size:15px;">
      Want server drafts, white-label exports, and automation?
      <a href="<?php echo esc_url(admin_url('admin.php?page=sfb-upgrade')); ?>" style="color:#a78bfa;font-weight:600;text-decoration:none;">Upgrade to Pro →</a>
    </p>
  </div>
</section>

<?php
/**
 * GUTENBERG BLOCKS VERSION
 *
 * If using Gutenberg, create blocks with this structure:
 *
 * 1. HTML Block (Hero):
 *    - Copy the <section class="sfb-hero">...</section> HTML above
 *
 * 2. HTML Block (Social Proof):
 *    - Copy the <section id="social-proof">...</section> HTML above
 *
 * 3. HTML Block (Outcomes):
 *    - Copy the <section id="outcomes">...</section> HTML above
 *
 * 4. HTML Block (Features):
 *    - Copy the <section id="features">...</section> HTML above
 *
 * 5. HTML Block (CTA):
 *    - Copy the <section id="cta">...</section> HTML above
 *
 * 6. HTML Block (Pro Upsell):
 *    - Copy the <section id="pro-upsell">...</section> HTML above
 *
 * OR create custom Gutenberg blocks using @wordpress/create-block
 */
?>
