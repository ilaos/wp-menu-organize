<?php
/**
 * Submittal & Spec Sheet Builder — Pro Registry
 * Finalized registry with versioning, grouping, and dynamic add-on registration.
 */
if (!defined('ABSPATH')) exit;

define('SFB_PRO_REGISTRY_VERSION', '1.0.0');

/**
 * Returns a singleton registry array by reference.
 * Structure:
 * [
 *   'version'   => '1.0.0',
 *   'features'  => [ key => [ 'label'=>..., 'group'=>..., 'pro'=>bool, 'desc'=>..., 'since'=>... ] ],
 *   'changelog' => [ [ 'date'=>..., 'version'=>..., 'notes'=>[...] ], ... ],
 * ]
 */
function &sfb_pro_registry() {
  static $REG;
  if (!isset($REG)) {
    $REG = [
      'version' => SFB_PRO_REGISTRY_VERSION,
      'features' => [],
      'changelog' => [],
    ];
  }
  return $REG;
}

/** Register or override a feature. */
function sfb_register_feature(string $key, array $args): void {
  $defaults = [
    'label' => $key,
    'group' => 'Core', // Core | Automation | Branding | Data | Distribution
    'pro'   => false,
    'desc'  => '',
    'since' => '1.0.0',
  ];
  $args = array_merge($defaults, $args);
  $reg =& sfb_pro_registry();
  $reg['features'][$key] = $args;
}

/** Bulk register features (assoc array). */
function sfb_register_features(array $map): void {
  foreach ($map as $k => $v) sfb_register_feature($k, $v);
}

/** Add changelog entry. */
function sfb_add_changelog(string $version, string $date, array $notes): void {
  $reg =& sfb_pro_registry();
  $reg['changelog'][] = [
    'version' => $version,
    'date'    => $date,
    'notes'   => array_values(array_filter($notes, 'strlen')),
  ];
}

/** Pro active? (dev override or license) */
// NOTE: This function is now defined in Includes/admin/license-api.php
// Kept here as a comment for reference - do not uncomment or PHP will fatal error

/** Get license details with formatted display values */
function sfb_get_license_details(): array {
  // Use new WooCommerce license API if available
  if (function_exists('sfb_get_license_status')) {
    return sfb_get_license_status();
  }

  // Fallback to old method
  $lic = get_option('sfb_license', []);
  $key = $lic['key'] ?? '';
  $email = $lic['email'] ?? '';
  $status = $lic['status'] ?? 'inactive';

  // Mask license key (show last 4 chars)
  $key_masked = $key ? '••••••••' . substr($key, -4) : '';

  // Status badge
  $status_label = [
    'active'   => __('Active', 'submittal-spec-sheet-builder'),
    'expired'  => __('Expired', 'submittal-spec-sheet-builder'),
    'invalid'  => __('Invalid', 'submittal-spec-sheet-builder'),
    'inactive' => __('Inactive', 'submittal-spec-sheet-builder'),
  ][$status] ?? __('Unknown', 'submittal-spec-sheet-builder');

  $status_color = [
    'active'   => '#46b450',
    'expired'  => '#f59e0b',
    'invalid'  => '#dc2626',
    'inactive' => '#999',
  ][$status] ?? '#999';

  return [
    'key'           => $key,
    'key_masked'    => $key_masked,
    'email'         => $email,
    'status'        => $status,
    'status_label'  => $status_label,
    'status_color'  => $status_color,
    'is_active'     => $status === 'active',
    'has_key'       => !empty($key),
  ];
}

/** All features (after filters). */
function sfb_features(): array {
  $reg =& sfb_pro_registry();

  // Base features (can be overridden)
  sfb_register_features([
    // Pro Features
    'auto_email'   => ['label'=>'Auto Email Packet','group'=>'Automation','pro'=>true,'desc'=>'Automatically email generated packets to recipients with tracking links.','since'=>'1.0.0'],
    'archive'      => ['label'=>'Auto Archive to History','group'=>'Automation','pro'=>true,'desc'=>'Auto-archive packets by project/date.','since'=>'1.0.0'],
    'tracking'     => ['label'=>'Public Tracking Link','group'=>'Distribution','pro'=>true,'desc'=>'Public links to share packets and verify downloads.','since'=>'1.0.0'],
    'white_label'  => ['label'=>'White-Label Output','group'=>'Branding','pro'=>true,'desc'=>'Remove plugin mentions for pure client branding.','since'=>'1.0.0'],
    'themes'       => ['label'=>'Brand Themes (Arch/Corp)','group'=>'Branding','pro'=>true,'desc'=>'Architectural and Corporate PDF themes with custom accents.','since'=>'1.0.0'],
    'watermark'    => ['label'=>'PDF Watermark','group'=>'Branding','pro'=>true,'desc'=>'Apply custom watermark to every page.','since'=>'1.0.0'],
    'signature'    => ['label'=>'Approval Signature Block','group'=>'Branding','pro'=>true,'desc'=>'Approval/signature block with name, title, date.','since'=>'1.0.0'],
    'server_drafts'=> ['label'=>'Shareable Drafts','group'=>'Data','pro'=>true,'desc'=>'Save selections to server and share via short URL. Drafts auto-expire after 45 days.','since'=>'1.0.0'],
    'lead_capture' => ['label'=>'Lead Capture & CRM','group'=>'Data','pro'=>true,'desc'=>'Collect email/phone before PDF download with UTM tracking, rate limiting, honeypot protection, and CSV export.','since'=>'1.0.2'],
    'csv_import'   => ['label'=>'CSV Catalog Import','group'=>'Data','pro'=>true,'desc'=>'Bulk import products from CSV with flexible hierarchy detection and merge/replace modes.','since'=>'1.0.0'],

    // Agency Features (Note: Most Agency features use direct sfb_is_agency_license() checks rather than registry)
    'brand_presets'     => ['label'=>'Brand Presets Library','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Save, load, and apply brand configurations with auto-apply option.','since'=>'1.0.0'],
    'lead_routing'      => ['label'=>'Lead Routing & Webhooks','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Route leads by domain/UTM rules with webhook integration.','since'=>'1.0.0'],
    'weekly_export'     => ['label'=>'Weekly Lead Export','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Automated weekly CSV email with Send Now option.','since'=>'1.0.0'],
    'agency_analytics'  => ['label'=>'Agency Analytics','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Aggregated non-PII metrics dashboard.','since'=>'1.0.0'],
    'agency_library'    => ['label'=>'Agency Library (Packs)','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Save catalogs as reusable Packs with JSON export.','since'=>'1.0.0'],
    'client_handoff'    => ['label'=>'Client Handoff Mode','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Hide agency-specific UI for client sites.','since'=>'1.0.0'],
    'operator_role'     => ['label'=>'Operator Role','group'=>'Agency','pro'=>true,'agency'=>true,'desc'=>'Custom WordPress role with limited catalog access.','since'=>'1.0.0'],

    // Free Features
    'summary'      => ['label'=>'Summary Page','group'=>'Core','pro'=>false,'desc'=>'Front summary grouped by category with key specs.','since'=>'1.0.0'],
    'toc'          => ['label'=>'Table of Contents','group'=>'Core','pro'=>false,'desc'=>'Clickable internal TOC for fast navigation.','since'=>'1.0.0'],
  ]);

  // Allow add-ons to register/modify features
  $reg['features'] = apply_filters('sfb_features_map', $reg['features']);

  return $reg['features'];
}

/** True if feature exists and is enabled for current license. */
function sfb_feature_enabled(string $key): bool {
  $all = sfb_features();
  if (!isset($all[$key])) return false;
  $def = $all[$key];

  // Check Agency-only features
  if (!empty($def['agency']) && !sfb_is_agency_license()) return false;

  // Check Pro features (Agency license includes all Pro features)
  if (!empty($def['pro']) && !sfb_is_pro_active()) return false;

  return true;
}

/** List all enabled feature keys. */
function sfb_enabled_features(): array {
  $out = [];
  foreach (sfb_features() as $k => $def) {
    if (sfb_feature_enabled($k)) $out[] = $k;
  }
  return $out;
}

/** Feature helper getters */
function sfb_feature(string $key): ?array {
  $all = sfb_features();
  return $all[$key] ?? null;
}

/** Register default changelog and filter for external entries. */
function sfb_bootstrap_changelog(): void {
  // Base entry (today's date can be dynamic if you prefer)
  sfb_add_changelog('1.0.0', date('Y-m-d'), [
    'Initial Pro registry with grouping and filters',
    'Upgrade screen badges and copy',
    'Automation, Branding, Data, Distribution categories',
  ]);
  // Let add-ons append their notes
  $reg =& sfb_pro_registry();
  $reg['changelog'] = apply_filters('sfb_pro_changelog', $reg['changelog']);
}

/**
 * Check if current license is Agency tier
 *
 * NOTE: This function is now defined in Includes/admin/license-api.php
 * Kept here as a comment for reference - do not uncomment or PHP will fatal error
 */
