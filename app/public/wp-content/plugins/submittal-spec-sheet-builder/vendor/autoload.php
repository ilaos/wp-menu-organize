<?php
/**
 * Vendor autoload wrapper for Submittal & Spec Sheet Builder
 *
 * This file loads the dompdf library and its dependencies.
 * Priority: composer autoload > lib/dompdf fallback
 */

$vendor_dir = __DIR__;

// First, try to load composer's autoload if packages were installed
$composer_autoload = $vendor_dir . '/autoload_real.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
    return;
}

// Fallback: Load dompdf from lib directory (without HTML5 parser)
$dompdf_autoload = dirname($vendor_dir) . '/lib/dompdf/autoload.inc.php';

if (file_exists($dompdf_autoload)) {
    require_once $dompdf_autoload;
} else {
    throw new Exception('DomPDF library not found. Expected at: ' . $dompdf_autoload);
}
