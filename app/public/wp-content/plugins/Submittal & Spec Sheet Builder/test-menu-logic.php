<?php
/**
 * Test script to verify menu logic for different license states
 *
 * Run this script to see what menus would appear for each license state
 */

// Simulate license states
$license_states = ['free', 'expired', 'pro', 'agency'];

foreach ($license_states as $state) {
  echo "\n========================================\n";
  echo "License State: " . strtoupper($state) . "\n";
  echo "========================================\n";

  // Demo Tools visibility
  $show_demo_tools = false; // SFB_SHOW_DEMO_TOOLS is false by default
  echo "Demo Tools: " . ($show_demo_tools ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";

  // Upgrade menu visibility
  $show_upgrade = in_array($state, ['free', 'expired'], true);
  echo "⭐ Upgrade: " . ($show_upgrade ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";

  // License & Support visibility
  $show_license_support = in_array($state, ['pro', 'agency'], true);
  echo "License & Support: " . ($show_license_support ? "✅ VISIBLE" : "❌ HIDDEN") . "\n";

  // Standard menus (always visible)
  echo "\nAlways Visible:\n";
  echo "  - Welcome\n";
  echo "  - Submittal Builder\n";
  echo "  - Branding\n";
  echo "  - Settings\n";
  echo "  - Utilities\n";

  // Pro-only menus
  if (in_array($state, ['pro', 'agency'], true)) {
    echo "\nPro Features:\n";
    echo "  - Tracking (if active)\n";
    echo "  - Leads (if enabled)\n";
  }

  // Agency-only menus
  if ($state === 'agency') {
    echo "\nAgency Features:\n";
    echo "  - 💼 Agency\n";
    echo "  - 📊 Agency Analytics\n";
  }
}

echo "\n========================================\n";
echo "With SFB_SHOW_DEMO_TOOLS = true\n";
echo "========================================\n";
echo "FREE: Demo Tools will be hidden (safeguard)\n";
echo "EXPIRED: Demo Tools will be hidden (safeguard)\n";
echo "PRO: Demo Tools will be VISIBLE\n";
echo "AGENCY: Demo Tools will be VISIBLE\n";
