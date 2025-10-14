<?php
/**
 * Debug REST Routes
 *
 * Test if REST routes are properly registered
 * Access via: /wp-content/plugins/Submittal & Spec Sheet Builder/debug-rest-routes.php
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

// Force REST API to initialize
do_action('rest_api_init');

// Get all REST routes
$rest_server = rest_get_server();
$all_routes = array_keys($rest_server->get_routes());

// Filter for our namespace
$sfb_routes = array_filter($all_routes, function($route) {
    return strpos($route, '/sfb/v1') === 0;
});

echo "<h1>SFB REST Routes Debug</h1>\n";
echo "<h2>Plugin Status</h2>\n";
echo "<ul>\n";
echo "<li>SFB_Plugin class exists: " . (class_exists('SFB_Plugin') ? 'Yes' : 'No') . "</li>\n";
echo "<li>SFB_Rest class exists: " . (class_exists('SFB_Rest') ? 'Yes' : 'No') . "</li>\n";
echo "<li>\$GLOBALS['sfb_plugin'] isset: " . (isset($GLOBALS['sfb_plugin']) ? 'Yes' : 'No') . "</li>\n";
echo "</ul>\n";

echo "<h2>Found " . count($sfb_routes) . " SFB Routes:</h2>\n";
echo "<ul>\n";
foreach ($sfb_routes as $route) {
    echo "<li><code>" . esc_html($route) . "</code></li>\n";
}
echo "</ul>\n";

// Specifically check for /node/move
$move_route_found = in_array('/sfb/v1/node/move', $sfb_routes);
echo "<h2>Critical Route Check</h2>\n";
echo "<p>/sfb/v1/node/move found: <strong>" . ($move_route_found ? 'YES' : 'NO') . "</strong></p>\n";

if ($move_route_found) {
    // Get route details
    $routes = $rest_server->get_routes();
    $move_route_details = $routes['/sfb/v1/node/move'];
    echo "<h3>Route Details:</h3>\n";
    echo "<pre>" . print_r($move_route_details, true) . "</pre>\n";
}

// Check user capabilities
echo "<h2>Current User Capabilities</h2>\n";
echo "<ul>\n";
echo "<li>User ID: " . get_current_user_id() . "</li>\n";
echo "<li>edit_sfb_catalog: " . (current_user_can('edit_sfb_catalog') ? 'Yes' : 'No') . "</li>\n";
echo "<li>manage_options: " . (current_user_can('manage_options') ? 'Yes' : 'No') . "</li>\n";
echo "</ul>\n";
