<?php
/**
 * Debug: Check node positions in database
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

global $wpdb;
$table = $wpdb->prefix . 'sfb_nodes';

echo "<h1>Node Positions for Parent 1748</h1>\n";
$nodes = $wpdb->get_results("SELECT id, title, node_type, parent_id, position FROM {$table} WHERE parent_id = 1748 ORDER BY position ASC, id ASC");

echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>ID</th><th>Title</th><th>Type</th><th>Parent ID</th><th>Position</th></tr>\n";
foreach ($nodes as $node) {
    echo "<tr>";
    echo "<td>{$node->id}</td>";
    echo "<td>" . esc_html($node->title) . "</td>";
    echo "<td>{$node->node_type}</td>";
    echo "<td>{$node->parent_id}</td>";
    echo "<td>{$node->position}</td>";
    echo "</tr>\n";
}
echo "</table>\n";

echo "<h2>All Nodes (showing position column)</h2>\n";
$all_nodes = $wpdb->get_results("SELECT id, title, node_type, parent_id, position FROM {$table} ORDER BY position ASC, id ASC LIMIT 50");

echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>ID</th><th>Title</th><th>Type</th><th>Parent ID</th><th>Position</th></tr>\n";
foreach ($all_nodes as $node) {
    $style = $node->parent_id == 1748 ? "background: yellow;" : "";
    echo "<tr style='$style'>";
    echo "<td>{$node->id}</td>";
    echo "<td>" . esc_html($node->title) . "</td>";
    echo "<td>{$node->node_type}</td>";
    echo "<td>{$node->parent_id}</td>";
    echo "<td>{$node->position}</td>";
    echo "</tr>\n";
}
echo "</table>\n";
