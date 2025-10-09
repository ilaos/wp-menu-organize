<?php
/**
 * Debug script to verify Tools page HTML
 * Access via: /wp-content/plugins/Submittal & Spec Sheet Builder/debug-tools.php
 */

// Show what the tools page HTML should look like
echo "<!DOCTYPE html><html><head><title>Debug Tools Page</title></head><body>";
echo "<h1>Expected Tools Page HTML:</h1>";
echo "<pre style='background:#f5f5f5;padding:20px;border:1px solid #ddd;'>";
echo htmlspecialchars('
<div class="wrap sfb-tools">
  <h1>Submittal Builder Tools</h1>

  <!-- Draft Management Card -->
  <div class="sfb-card">
    <h2>🧹 Draft Management</h2>
    <p class="sfb-muted">Clean up temp drafts and verify the system is healthy.</p>

    <div class="sfb-actions">
      <button id="sfb-purge-btn" class="button button-primary sfb-btn">Purge Expired Drafts</button>
      <button id="sfb-smoke-btn" class="button sfb-btn">Run Smoke Test</button>
    </div>

    <div id="sfb-drafts-status" class="sfb-status">Idle — ready when you are.</div>
  </div>
</div>
');
echo "</pre>";
echo "<h2>Check these files exist:</h2>";
echo "<ul>";
$css_path = __DIR__ . '/assets/admin.css';
$js_path = __DIR__ . '/assets/admin.js';
echo "<li>CSS: " . ($css_path) . " - " . (file_exists($css_path) ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "<li>JS: " . ($js_path) . " - " . (file_exists($js_path) ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "</ul>";

echo "<h2>Check CSS contains .sfb-tools:</h2>";
if (file_exists($css_path)) {
    $css_content = file_get_contents($css_path);
    if (strpos($css_content, '.sfb-tools') !== false) {
        echo "✅ .sfb-tools CSS found<br>";
        // Show first occurrence
        $lines = explode("\n", $css_content);
        foreach ($lines as $num => $line) {
            if (strpos($line, '.sfb-tools') !== false) {
                echo "Line " . ($num + 1) . ": " . htmlspecialchars($line) . "<br>";
                break;
            }
        }
    } else {
        echo "❌ .sfb-tools CSS NOT FOUND";
    }
}

echo "<h2>Check JS contains Tools Page Functionality:</h2>";
if (file_exists($js_path)) {
    $js_content = file_get_contents($js_path);
    if (strpos($js_content, 'Tools Page Functionality') !== false) {
        echo "✅ Tools Page JS found<br>";
        if (strpos($js_content, 'sfb-demo-tools') !== false) {
            echo "✅ sfb-demo-tools page check found<br>";
        }
        if (strpos($js_content, '#sfb-purge-btn') !== false) {
            echo "✅ #sfb-purge-btn selector found<br>";
        }
    } else {
        echo "❌ Tools Page JS NOT FOUND";
    }
}

echo "</body></html>";
