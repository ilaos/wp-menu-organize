<?php
/**
 * Dompdf autoloader for WordPress plugin (buildless)
 * Loads Dompdf + dependencies without Composer
 */

if (!defined('ABSPATH')) exit;

spl_autoload_register(function ($class) {
    // PSR-4 mappings
    $prefixes = [
        'Dompdf\\' => __DIR__ . '/src/',
        'Masterminds\\HTML5\\' => __DIR__ . '/../masterminds/html5/src/HTML5/',
        'Masterminds\\' => __DIR__ . '/../masterminds/html5/src/',  // For Masterminds\HTML5
        'FontLib\\' => __DIR__ . '/php-font-lib/src/FontLib/',      // Fixed: within dompdf dir
        'Svg\\' => __DIR__ . '/php-svg-lib/src/Svg/',                // Fixed: within dompdf dir
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return true;
        }
    }

    return false;
});

// Load lib classes (Cpdf, etc.)
$lib_dir = __DIR__ . '/lib';
if (is_dir($lib_dir)) {
    // Load Cpdf.php if it exists
    $cpdf_file = $lib_dir . '/Cpdf.php';
    if (file_exists($cpdf_file)) {
        require_once $cpdf_file;
    }
    // Also load any .cls.php files for backwards compatibility
    foreach (glob($lib_dir . '/*.cls.php') as $file) {
        require_once $file;
    }
}
