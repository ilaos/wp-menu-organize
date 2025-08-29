<?php
require_once('wp-load.php');
require_once('wp-content/plugins/wp-menu-organize/includes/ajax-handlers.php');

if (function_exists('wmo_get_builtin_templates')) {
    echo "Function EXISTS!<br>";
    $templates = wmo_get_builtin_templates();
    echo "<pre>";
    print_r($templates);
    echo "</pre>";
} else {
    echo "Function NOT FOUND";
}
