<?php
/**
 * Admin Template: License & Support (Pro & Agency Users)
 *
 * Displays license information, renewal links, support resources, and changelog for active Pro/Agency users.
 */
if (!defined('ABSPATH')) exit;

$lic = sfb_get_license_details();
$reg =& sfb_pro_registry();
$links = sfb_get_links();

// Check license tiers
$is_pro = function_exists('sfb_is_pro_active') && sfb_is_pro_active();
$is_agency = function_exists('sfb_is_agency_license') && sfb_is_agency_license();

// Determine user tier for display
$user_tier = 'free';
$tier_label = 'Pro';
if ($is_agency) {
  $user_tier = 'agency';
  $tier_label = 'Agency';
} elseif ($is_pro) {
  $user_tier = 'pro';
  $tier_label = 'Pro';
}

// Subtitle messaging
$subtitle_messages = [
  'pro' => __('Your Pro license is active. Manage your subscription and get support.', 'submittal-spec-sheet-builder'),
  'agency' => __('Your Agency license is active. Manage your subscription and get priority support.', 'submittal-spec-sheet-builder'),
];
$subtitle = $subtitle_messages[$user_tier] ?? $subtitle_messages['pro'];
?>
<div class="wrap sfb-license-support-wrap">
  <h1><?php esc_html_e('License & Support', 'submittal-spec-sheet-builder'); ?></h1>
  <p class="sfb-sub"><?php echo esc_html($subtitle); ?></p>

  <!-- License Summary Banner Card -->
  <div class="sfb-license-banner" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:32px; margin:35px 0 24px 0; max-width:1000px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:24px; flex-wrap:wrap;">
      <!-- Left: License Info -->
      <div style="flex:1; min-width:300px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
          <h2 style="margin:0; font-size:20px; font-weight:600; color:#111827;">
            <?php /* translators: %s: license tier label (Free, Pro, Agency) */ ?>
            <?php echo esc_html(sprintf(__('Submittal & Spec Sheet Builder %s', 'submittal-spec-sheet-builder'), $tier_label)); ?>
          </h2>
          <span class="sfb-status-badge" style="display:inline-block; padding:6px 14px; background:<?php echo esc_attr($lic['status_color']); ?>; color:#fff; border-radius:6px; font-size:12px; font-weight:600; letter-spacing:0.5px;">
            ✓ <?php echo esc_html($lic['status_label']); ?>
          </span>
        </div>

        <div style="margin-bottom:12px;">
          <label style="display:block; font-size:13px; font-weight:500; color:#6b7280; margin-bottom:6px;">
            <?php esc_html_e('License Key', 'submittal-spec-sheet-builder'); ?>
          </label>
          <div style="display:flex; align-items:center; gap:10px;">
            <code id="sfb-license-key-display" style="background:#f3f4f6; padding:10px 14px; border-radius:6px; font-size:14px; font-family:monospace; color:#374151; flex:1;">
              <?php echo esc_html($lic['key_masked'] ?: __('Not available', 'submittal-spec-sheet-builder')); ?>
            </code>
            <button type="button" data-sfb-copy-key data-key="<?php echo esc_attr($lic['key'] ?? ''); ?>" class="button button-small" style="padding:8px 12px; font-size:12px; white-space:nowrap;">
              <?php esc_html_e('Copy Key', 'submittal-spec-sheet-builder'); ?>
            </button>
          </div>
        </div>

        <?php if ($lic['email']): ?>
        <div>
          <label style="display:block; font-size:13px; font-weight:500; color:#6b7280; margin-bottom:6px;">
            <?php esc_html_e('License Email', 'submittal-spec-sheet-builder'); ?>
          </label>
          <div style="color:#374151; font-size:14px;">
            <?php echo esc_html($lic['email']); ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right: Action Buttons -->
      <div style="display:flex; flex-direction:column; gap:10px; min-width:200px;">
        <?php echo sfb_render_link_button('account', __('Manage Subscription', 'submittal-spec-sheet-builder'), 'button button-primary', ['style' => 'text-align:center; padding:10px 24px; font-size:14px; font-weight:500;']); ?>
        <?php echo sfb_render_link_button('invoices', __('Download Invoices', 'submittal-spec-sheet-builder'), 'button', ['style' => 'text-align:center; padding:10px 24px; font-size:14px;']); ?>
      </div>
    </div>
  </div>

  <!-- Support & Resources Grid -->
  <div class="sfb-support-resources" style="margin:24px 0; max-width:1000px;">
    <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:600; color:#111827;"><?php esc_html_e('Support & Resources', 'submittal-spec-sheet-builder'); ?></h2>

    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px;">
      <!-- Documentation -->
      <?php
      $docs_url = sfb_get_link('docs');
      $card_class = 'sfb-resource-card';
      $card_style = 'display:block; background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:24px; text-decoration:none; transition:all 0.2s ease;';
      if (!$docs_url): $card_style .= ' opacity:0.6; cursor:not-allowed;'; endif;
      ?>
      <a href="<?php echo $docs_url ? esc_url($docs_url) : '#'; ?>" <?php if ($docs_url): ?>target="_blank" rel="noopener"<?php else: ?>onclick="return false;" title="<?php esc_attr_e('Coming soon', 'submittal-spec-sheet-builder'); ?>"<?php endif; ?> class="<?php echo esc_attr($card_class); ?>" style="<?php echo esc_attr($card_style); ?>">
        <div style="font-size:32px; margin-bottom:12px;">📄</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Documentation', 'submittal-spec-sheet-builder'); ?>
          <?php if (!$docs_url): ?><span style="font-size:11px; color:#9ca3af; font-weight:400;"> (<?php esc_html_e('Coming soon', 'submittal-spec-sheet-builder'); ?>)</span><?php endif; ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Browse guides, tutorials, and FAQs to get the most out of the plugin.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </a>

      <!-- Tutorials -->
      <?php
      $tutorials_url = sfb_get_link('tutorials');
      $card_style = 'display:block; background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:24px; text-decoration:none; transition:all 0.2s ease;';
      if (!$tutorials_url): $card_style .= ' opacity:0.6; cursor:not-allowed;'; endif;
      ?>
      <a href="<?php echo $tutorials_url ? esc_url($tutorials_url) : '#'; ?>" <?php if ($tutorials_url): ?>target="_blank" rel="noopener"<?php else: ?>onclick="return false;" title="<?php esc_attr_e('Coming soon', 'submittal-spec-sheet-builder'); ?>"<?php endif; ?> class="<?php echo esc_attr($card_class); ?>" style="<?php echo esc_attr($card_style); ?>">
        <div style="font-size:32px; margin-bottom:12px;">🎓</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Tutorials / How-tos', 'submittal-spec-sheet-builder'); ?>
          <?php if (!$tutorials_url): ?><span style="font-size:11px; color:#9ca3af; font-weight:400;"> (<?php esc_html_e('Coming soon', 'submittal-spec-sheet-builder'); ?>)</span><?php endif; ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Watch step-by-step video tutorials and learn best practices.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </a>

      <!-- Roadmap -->
      <?php
      $roadmap_url = sfb_get_link('roadmap');
      $card_style = 'display:block; background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:24px; text-decoration:none; transition:all 0.2s ease;';
      if (!$roadmap_url): $card_style .= ' opacity:0.6; cursor:not-allowed;'; endif;
      ?>
      <a href="<?php echo $roadmap_url ? esc_url($roadmap_url) : '#'; ?>" <?php if ($roadmap_url): ?>target="_blank" rel="noopener"<?php else: ?>onclick="return false;" title="<?php esc_attr_e('Coming soon', 'submittal-spec-sheet-builder'); ?>"<?php endif; ?> class="<?php echo esc_attr($card_class); ?>" style="<?php echo esc_attr($card_style); ?>">
        <div style="font-size:32px; margin-bottom:12px;">🗺️</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Roadmap / Feature Requests', 'submittal-spec-sheet-builder'); ?>
          <?php if (!$roadmap_url): ?><span style="font-size:11px; color:#9ca3af; font-weight:400;"> (<?php esc_html_e('Coming soon', 'submittal-spec-sheet-builder'); ?>)</span><?php endif; ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('See what is coming next and submit your feature ideas.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </a>

      <!-- Priority Support -->
      <?php
      $support_url = sfb_get_link('support');
      $card_style = 'display:block; background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:24px; text-decoration:none; transition:all 0.2s ease;';
      if (!$support_url): $card_style .= ' opacity:0.6; cursor:not-allowed;'; endif;
      ?>
      <a href="<?php echo $support_url ? esc_url($support_url) : '#'; ?>" <?php if ($support_url && strpos($support_url, 'mailto:') !== 0): ?>target="_blank" rel="noopener"<?php elseif (!$support_url): ?>onclick="return false;" title="<?php esc_attr_e('Coming soon', 'submittal-spec-sheet-builder'); ?>"<?php endif; ?> class="<?php echo esc_attr($card_class); ?>" style="<?php echo esc_attr($card_style); ?>">
        <div style="font-size:32px; margin-bottom:12px;">🛠️</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Priority Support', 'submittal-spec-sheet-builder'); ?>
          <?php if (!$support_url): ?><span style="font-size:11px; color:#9ca3af; font-weight:400;"> (<?php esc_html_e('Coming soon', 'submittal-spec-sheet-builder'); ?>)</span><?php endif; ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Get help from our support team with priority response times.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </a>
    </div>
  </div>

  <!-- Agency Features / Upgrade to Agency (for Pro users) -->
  <?php if ($user_tier === 'pro'): ?>
  <div class="sfb-upgrade-agency-banner" style="margin:24px 0; max-width:1000px; background:linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius:12px; padding:32px; color:#fff; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap;">
      <div style="flex:1; min-width:300px;">
        <h2 style="margin:0 0 12px 0; font-size:22px; font-weight:700; color:#fff;">
          <?php esc_html_e('Upgrade to Agency', 'submittal-spec-sheet-builder'); ?>
        </h2>
        <p style="margin:0 0 16px 0; font-size:15px; line-height:1.6; color:rgba(255,255,255,0.95);">
          <?php esc_html_e('Take your business to the next level with Agency features:', 'submittal-spec-sheet-builder'); ?>
        </p>
        <ul style="margin:0; padding-left:20px; list-style:disc; font-size:14px; line-height:1.8; color:rgba(255,255,255,0.9);">
          <li><?php esc_html_e('White-Label Mode - Remove all plugin branding', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Lead Routing & Webhooks - Automated lead distribution', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Agency Analytics - Track performance across clients', 'submittal-spec-sheet-builder'); ?></li>
          <li><?php esc_html_e('Multi-Client Support - Manage multiple installations', 'submittal-spec-sheet-builder'); ?></li>
        </ul>
      </div>
      <div style="min-width:200px;">
        <a href="https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/" target="_blank" rel="noopener" class="button button-hero" style="display:inline-block; padding:14px 32px; background:#fff; color:#6d28d9; border:none; border-radius:8px; font-size:16px; font-weight:600; text-decoration:none; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.15); transition:all 0.2s ease;">
          <?php esc_html_e('See Agency Features', 'submittal-spec-sheet-builder'); ?> →
        </a>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($user_tier === 'agency'): ?>
  <!-- Agency Features Info -->
  <div class="sfb-agency-features" style="margin:24px 0; max-width:1000px;">
    <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:600; color:#111827;"><?php esc_html_e('Your Agency Features', 'submittal-spec-sheet-builder'); ?></h2>
    <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:16px;">

      <div style="background:#fff; border:2px solid #8b5cf6; border-radius:10px; padding:24px;">
        <div style="font-size:32px; margin-bottom:12px;">🎨</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('White-Label Mode', 'submittal-spec-sheet-builder'); ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Remove all plugin branding from PDFs and admin interface for seamless client experiences.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </div>

      <div style="background:#fff; border:2px solid #8b5cf6; border-radius:10px; padding:24px;">
        <div style="font-size:32px; margin-bottom:12px;">📧</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Lead Routing & Webhooks', 'submittal-spec-sheet-builder'); ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Automated lead distribution and CRM integration via webhooks for instant notifications.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </div>

      <div style="background:#fff; border:2px solid #8b5cf6; border-radius:10px; padding:24px;">
        <div style="font-size:32px; margin-bottom:12px;">📊</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Agency Analytics', 'submittal-spec-sheet-builder'); ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Track performance, conversions, and engagement across all your client installations.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </div>

      <div style="background:#fff; border:2px solid #8b5cf6; border-radius:10px; padding:24px;">
        <div style="font-size:32px; margin-bottom:12px;">👥</div>
        <h3 style="margin:0 0 8px 0; font-size:16px; font-weight:600; color:#111827;">
          <?php esc_html_e('Multi-Client Management', 'submittal-spec-sheet-builder'); ?>
        </h3>
        <p style="margin:0; color:#6b7280; font-size:14px; line-height:1.5;">
          <?php esc_html_e('Manage multiple client accounts and installations from a centralized dashboard.', 'submittal-spec-sheet-builder'); ?>
        </p>
      </div>

    </div>
  </div>
  <?php endif; ?>

  <!-- What's New (Collapsible) -->
  <div class="sfb-whats-new-container" style="margin:24px 0; max-width:1000px;">
    <div class="sfb-whats-new-card" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <!-- Header (clickable) -->
      <button type="button" id="sfb-whats-new-toggle" style="width:100%; background:transparent; border:none; padding:24px 32px; text-align:left; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:background 0.2s ease;">
        <h2 style="margin:0; font-size:18px; font-weight:600; color:#111827;">
          <?php esc_html_e("What's New", 'submittal-spec-sheet-builder'); ?>
        </h2>
        <span id="sfb-whats-new-icon" style="font-size:20px; color:#6b7280; transition:transform 0.3s ease;">▼</span>
      </button>

      <!-- Content (collapsible) -->
      <div id="sfb-whats-new-content" style="padding:0 32px 24px 32px; display:block;">
        <?php
        $changelog = $reg['changelog'];
        usort($changelog, fn($a,$b)=>version_compare($b['version'],$a['version']));
        ?>

        <ul style="margin:0; padding:0; list-style:none;">
          <?php foreach (array_slice($changelog, 0, 5) as $entry): ?>
            <li style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #f3f4f6;">
              <div style="display:flex; align-items:baseline; gap:10px; margin-bottom:8px;">
                <strong style="color:#111827; font-size:15px; font-weight:600;">v<?php echo esc_html($entry['version']); ?></strong>
                <span style="color:#9ca3af; font-size:13px;"><?php echo esc_html($entry['date']); ?></span>
              </div>
              <?php if (!empty($entry['notes'])): ?>
                <ul style="margin:4px 0 0 0; padding-left:20px; list-style:disc;">
                  <?php foreach ($entry['notes'] as $n): ?>
                    <li style="color:#374151; font-size:14px; line-height:1.6; margin:4px 0;"><?php echo esc_html($n); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <?php
        $changelog_url = sfb_get_link('docs');
        if ($changelog_url):
        ?>
        <p style="margin-top:16px; text-align:center;">
          <a href="<?php echo esc_url($changelog_url); ?>" target="_blank" rel="noopener" style="color:#667eea; font-weight:500; text-decoration:none;">
            <?php esc_html_e('View Full Changelog →', 'submittal-spec-sheet-builder'); ?>
          </a>
        </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <p style="color:#9ca3af; margin-top:32px; font-size:13px; text-align:center;">
    <?php
    $thank_you_messages = [
      'pro' => __('Thank you for being a Pro member!', 'submittal-spec-sheet-builder'),
      'agency' => __('Thank you for being an Agency partner!', 'submittal-spec-sheet-builder'),
    ];
    echo esc_html($thank_you_messages[$user_tier] ?? $thank_you_messages['pro']);
    ?>
  </p>

  <!-- Feedback Footer -->
  <div style="margin-top: 40px; padding: 16px 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; text-align: center;">
    <p style="margin: 0; color: #6b7280; font-size: 14px;">
      <?php esc_html_e('Found a bug? Have a suggestion?', 'submittal-spec-sheet-builder'); ?>
      <a href="https://webstuffguylabs.com/support/" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: none; font-weight: 600;">
        <?php esc_html_e('Tell us about it', 'submittal-spec-sheet-builder'); ?> →
      </a>
    </p>
  </div>
</div>

<!-- Copy Toast Notification -->
<div id="sfb-copy-toast" style="position:fixed; bottom:24px; right:24px; background:#10b981; color:#fff; padding:12px 20px; border-radius:8px; font-size:14px; font-weight:500; box-shadow:0 4px 12px rgba(0,0,0,0.15); opacity:0; transform:translateY(20px); transition:all 0.3s ease; pointer-events:none; z-index:100000;">
  ✓ <?php esc_html_e('License key copied to clipboard', 'submittal-spec-sheet-builder'); ?>
</div>

<style>
/* Resource Card Hover Effects */
.sfb-resource-card:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
  transform: translateY(-2px);
}

.sfb-resource-card h3 {
  transition: color 0.2s ease;
}

.sfb-resource-card:hover h3 {
  color: #667eea;
}

/* What's New Toggle Hover */
#sfb-whats-new-toggle:hover {
  background: #f9fafb;
}

/* Copy Toast Show Animation */
#sfb-copy-toast.sfb-show-toast {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

/* Responsive Mobile Styles */
@media (max-width: 768px) {
  .sfb-license-banner > div {
    flex-direction: column;
  }

  .sfb-support-resources > div {
    grid-template-columns: 1fr;
  }

  .sfb-agency-features > div {
    grid-template-columns: 1fr;
  }

  .sfb-upgrade-agency-banner > div {
    flex-direction: column;
  }

  #sfb-license-key-display {
    font-size: 12px;
  }

  #sfb-copy-toast {
    bottom: 12px;
    right: 12px;
    left: 12px;
    text-align: center;
  }
}
</style>

<script>
jQuery(document).ready(function($) {
  // Collapsible What's New Section
  const STORAGE_KEY = 'sfb_whats_new_collapsed';
  const $toggle = $('#sfb-whats-new-toggle');
  const $content = $('#sfb-whats-new-content');
  const $icon = $('#sfb-whats-new-icon');

  // Check localStorage for saved state (default: open)
  const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';

  if (isCollapsed) {
    $content.hide();
    $icon.css('transform', 'rotate(-90deg)');
  }

  // Toggle functionality
  $toggle.on('click', function() {
    const willBeCollapsed = $content.is(':visible');

    $content.slideToggle(300);
    $icon.css('transform', willBeCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)');

    // Save state to localStorage
    localStorage.setItem(STORAGE_KEY, willBeCollapsed.toString());
  });
});
</script>
