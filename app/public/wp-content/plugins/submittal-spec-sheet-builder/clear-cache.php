<?php
/**
 * Cache Clearing Utility
 *
 * This file clears OPcache and forces WordPress to reload the plugin.
 * Access via: https://u0p.327.myftpupload.com/wp-content/plugins/submittal-spec-sheet-builder/clear-cache.php
 *
 * DELETE THIS FILE after verifying the deployment works!
 */

header('Content-Type: text/plain');

echo "=== Cache Clearing Utility ===\n\n";

// Clear OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✓ OPcache cleared successfully\n";
    } else {
        echo "✗ OPcache reset failed\n";
    }
} else {
    echo "- OPcache not available\n";
}

// Clear realpath cache
clearstatcache(true);
echo "✓ Realpath cache cleared\n";

// Show plugin file modification time
$plugin_file = __DIR__ . '/submittal-form-builder.php';
if (file_exists($plugin_file)) {
    echo "\n=== Plugin File Info ===\n";
    echo "File: submittal-form-builder.php\n";
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($plugin_file)) . "\n";
    echo "Size: " . number_format(filesize($plugin_file)) . " bytes\n";

    // Read first 20 lines to check version
    $lines = file($plugin_file, FILE_IGNORE_NEW_LINES);
    foreach (array_slice($lines, 0, 20) as $line) {
        if (stripos($line, 'Version:') !== false) {
            echo "Version line: " . trim($line) . "\n";
            break;
        }
    }
}

// Check if build.txt exists
$build_file = __DIR__ . '/build.txt';
if (file_exists($build_file)) {
    echo "\n=== Build Info ===\n";
    echo file_get_contents($build_file);
} else {
    echo "\n✗ build.txt not found - deployment may have failed\n";
}

echo "\n=== Asset Files ===\n";
$assets = [
    'assets/admin.js',
    'assets/admin.css',
    'assets/app.js',
    'assets/app.css'
];

foreach ($assets as $asset) {
    $path = __DIR__ . '/' . $asset;
    if (file_exists($path)) {
        echo "✓ $asset - " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
    } else {
        echo "✗ $asset - NOT FOUND\n";
    }
}

echo "\n=== Instructions ===\n";
echo "1. Hard refresh your browser (Ctrl+Shift+R or Cmd+Shift+R)\n";
echo "2. Go to WordPress Admin > Plugins\n";
echo "3. Check if version shows: 1.2.2-subtype\n";
echo "4. Go to Product Catalog and select a Type node\n";
echo "5. You should see '+ Subtype' button\n";
echo "\nDELETE THIS FILE after verification!\n";
