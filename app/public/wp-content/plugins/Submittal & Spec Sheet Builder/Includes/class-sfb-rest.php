<?php
/**
 * SFB_Rest - REST API route registration (Phase 1 refactor)
 *
 * Thin wrapper that registers all REST routes under sfb/v1 namespace
 * and forwards to existing handler methods in the main plugin file.
 *
 * @package SubmittalBuilder
 * @since 1.0.3
 */

if (!defined('ABSPATH')) exit;

final class SFB_Rest {

  /**
   * Initialize REST API hooks
   */
  public static function init() {
    add_action('rest_api_init', [__CLASS__, 'register_routes']);
  }

  /**
   * Register all REST API routes
   *
   * Forwards to existing handler methods in Submittal_Form_Builder class
   */
  public static function register_routes() {
    global $sfb_plugin;

    if (!$sfb_plugin || !($sfb_plugin instanceof SFB_Plugin)) {
      return;
    }

    // Health/Status endpoints (public)
    register_rest_route('sfb/v1', '/health', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [__CLASS__, 'get_health']
    ]);

    register_rest_route('sfb/v1', '/ping', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [__CLASS__, 'get_ping']
    ]);

    register_rest_route('sfb/v1', '/status', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [__CLASS__, 'get_status']
    ]);

    // Catalog Management (Admin)
    register_rest_route('sfb/v1', '/form/seed', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_seed_sample_catalog']
    ]);

    register_rest_route('sfb/v1', '/form/wipe', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_wipe_form']
    ]);

    register_rest_route('sfb/v1', '/form/(?P<id>\d+)', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [__CLASS__, 'get_form']
    ]);

    register_rest_route('sfb/v1', '/form/(?P<id>\d+)/export', [
      'methods' => 'GET',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_export_form']
    ]);

    register_rest_route('sfb/v1', '/form/import', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_import_form']
    ]);

    // Node Operations (Admin)
    register_rest_route('sfb/v1', '/node/save', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'save_node']
    ]);

    register_rest_route('sfb/v1', '/node/create', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'create_node']
    ]);

    register_rest_route('sfb/v1', '/node/delete', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'delete_node']
    ]);

    register_rest_route('sfb/v1', '/node/reorder', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'reorder_node']
    ]);

    register_rest_route('sfb/v1', '/node/duplicate', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'duplicate_node']
    ]);

    register_rest_route('sfb/v1', '/node/move', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [__CLASS__, 'move_node']
    ]);

    register_rest_route('sfb/v1', '/node/history', [
      'methods' => 'GET',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [__CLASS__, 'get_node_history']
    ]);

    // Bulk Operations (Admin)
    register_rest_route('sfb/v1', '/bulk/delete', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_bulk_delete']
    ]);

    register_rest_route('sfb/v1', '/bulk/move', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_bulk_move']
    ]);

    register_rest_route('sfb/v1', '/bulk/duplicate', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_bulk_duplicate']
    ]);

    register_rest_route('sfb/v1', '/bulk/export', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('edit_sfb_catalog'); },
      'callback' => [$sfb_plugin, 'api_bulk_export']
    ]);

    // PDF Generation (Public - Phase 6: route through SFB_Pdf facade)
    register_rest_route('sfb/v1', '/generate', [
      'methods' => 'POST',
      'permission_callback' => '__return_true', // public submission allowed
      'callback' => ['SFB_Pdf', 'generate_packet']
    ]);

    // Drafts (Pro - Public with nonce)
    register_rest_route('sfb/v1', '/drafts', [
      'methods' => 'POST',
      'permission_callback' => '__return_true', // public with nonce
      'callback' => [$sfb_plugin, 'api_create_draft']
    ]);

    register_rest_route('sfb/v1', '/drafts/(?P<id>[A-Za-z0-9_-]{6,36})', [
      'methods' => 'GET',
      'permission_callback' => '__return_true', // public read by ID
      'callback' => [$sfb_plugin, 'api_get_draft']
    ]);

    register_rest_route('sfb/v1', '/drafts/(?P<id>[A-Za-z0-9_-]{6,36})', [
      'methods' => 'PUT',
      'permission_callback' => '__return_true', // public with nonce
      'callback' => [$sfb_plugin, 'api_update_draft']
    ]);

    // Settings (Admin)
    register_rest_route('sfb/v1', '/settings', [
      'methods' => 'GET',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [__CLASS__, 'get_settings']
    ]);

    register_rest_route('sfb/v1', '/settings', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [__CLASS__, 'save_settings']
    ]);

    // License Management (Admin)
    register_rest_route('sfb/v1', '/license', [
      'methods' => 'GET',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_get_license']
    ]);

    register_rest_route('sfb/v1', '/license', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_save_license']
    ]);
  }

  // =========================================================================
  // Phase 2: REST Handler Methods (moved from main plugin file)
  // =========================================================================

  /**
   * GET /health - Health check endpoint
   *
   * @return array Response with ok status and version
   */
  public static function get_health() {
    return ['ok' => true, 'version' => SFB_Plugin::VERSION];
  }

  /**
   * GET /ping - Simple ping endpoint
   *
   * @return array Response with ok and pong status
   */
  public static function get_ping() {
    return ['ok' => true, 'pong' => true];
  }

  /**
   * GET /status - Get plugin status and feature availability
   *
   * @return array Status information including Pro status and features
   */
  public static function get_status() {
    $opt = get_option('sfb_branding', []);
    $settings = wp_parse_args($opt, self::get_default_settings());

    return [
      'ok' => true,
      'pro_active' => sfb_is_pro_active(),
      'features' => sfb_enabled_features(),
      'drafts' => [
        'autosave_enabled' => (bool)$settings['drafts_autosave_enabled'],
        'server_enabled' => (bool)$settings['drafts_server_enabled'] && (sfb_is_pro_active() || defined('SFB_PRO_DEV')),
        'expiry_days' => (int)$settings['drafts_expiry_days'],
        'rate_limit_sec' => (int)$settings['drafts_rate_limit_sec'],
      ]
    ];
  }

  /**
   * GET /settings - Get plugin settings (branding + general)
   *
   * @return array Response with settings data
   */
  public static function get_settings() {
    $opt = get_option('sfb_branding', []);
    return ['ok' => true, 'settings' => wp_parse_args($opt, self::get_default_settings())];
  }

  /**
   * POST /settings - Save plugin settings
   *
   * @param WP_REST_Request $req Request object with settings data
   * @return array Response with saved settings
   */
  public static function save_settings($req) {
    $p = $req->get_json_params();
    $in = $p['settings'] ?? [];
    $clean = [
      'logo_url'        => esc_url_raw($in['logo_url'] ?? ''),
      'company_name'    => sanitize_text_field($in['company_name'] ?? ''),
      'company_address' => sanitize_textarea_field($in['company_address'] ?? ''),
      'company_phone'   => sanitize_text_field($in['company_phone'] ?? ''),
      'company_website' => sanitize_text_field($in['company_website'] ?? ''),
      'primary_color'   => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $in['primary_color'] ?? '') ? $in['primary_color'] : '#111827',
      'cover_default'   => !empty($in['cover_default']),
      'footer_text'     => sanitize_text_field($in['footer_text'] ?? 'Generated by Submittal & Spec Builder'),
      'theme'           => in_array($in['theme'] ?? '', ['engineering', 'architectural', 'corporate']) ? $in['theme'] : 'engineering',
      'watermark'       => sanitize_text_field($in['watermark'] ?? ''),
      // Draft settings
      'drafts_autosave_enabled' => !empty($in['drafts_autosave_enabled']),
      'drafts_server_enabled'   => !empty($in['drafts_server_enabled']) && (sfb_is_pro_active() || defined('SFB_PRO_DEV')),
      'drafts_expiry_days'      => max(1, min(365, intval($in['drafts_expiry_days'] ?? 45))),
      'drafts_rate_limit_sec'   => max(5, min(120, intval($in['drafts_rate_limit_sec'] ?? 20))),
      'drafts_privacy_note'     => sanitize_textarea_field($in['drafts_privacy_note'] ?? ''),
    ];
    update_option('sfb_branding', $clean, false);
    return ['ok' => true, 'settings' => $clean];
  }

  /**
   * Get default settings (used by get_status and get_settings)
   *
   * @return array Default settings array
   */
  private static function get_default_settings() {
    return [
      'logo_url' => '',
      'company_name' => '',
      'company_address' => '',
      'company_phone' => '',
      'company_website' => '',
      'primary_color' => '#111827',
      'brand_preset' => 'custom',  // 'modern-blue' | 'architect-gray' | 'engineering-bold' | 'clean-violet' | 'custom'
      'cover_default' => true,
      'footer_text' => 'Generated by Submittal & Spec Builder',
      // NEW: Branding themes + watermark
      'theme' => 'engineering',  // 'engineering' | 'architectural' | 'corporate'
      'watermark' => '',          // optional text watermark; empty disables
      // Draft settings
      'drafts_autosave_enabled' => true,
      'drafts_server_enabled' => sfb_is_pro_active() || defined('SFB_PRO_DEV'),
      'drafts_expiry_days' => 45,
      'drafts_rate_limit_sec' => 20,
      'drafts_privacy_note' => '',
    ];
  }

  // =========================================================================
  // Phase 3: Catalog Read-Only REST Handlers (moved from main plugin file)
  // =========================================================================

  /**
   * GET /form/{id} - Get full catalog/form data for display
   *
   * @param WP_REST_Request $req Request object with form ID
   * @return array|WP_Error Response with form and nodes, or error
   */
  public static function get_form($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $form_id = intval($req['id']);
      $forms = $wpdb->prefix.'sfb_forms';
      $nodes = $wpdb->prefix.'sfb_nodes';

      $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $forms WHERE id=%d", $form_id), ARRAY_A);
      if (!$form) return new WP_Error('not_found','Form not found', ['status'=>404]);

      $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $nodes WHERE form_id=%d ORDER BY position ASC, id ASC", $form_id), ARRAY_A);
      foreach ($rows as &$r) {
        $r['settings'] = $r['settings_json'] ? json_decode($r['settings_json'], true) : [];
        unset($r['settings_json']);
      }
      return ['ok'=>true,'form'=>$form,'nodes'=>$rows];
    } catch (\Throwable $e) {
      error_log('SFB api_get_form error: '.$e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /**
   * GET /node/history - Get node change history
   *
   * @param WP_REST_Request $req Request object with node ID
   * @return array|WP_Error Response with history data, or error
   */
  public static function get_node_history($req) {
    try {
      $id = intval($req->get_param('id'));
      if (!$id) return new WP_Error('bad_request', 'Missing id', ['status' => 400]);

      // TODO: Implement actual history tracking with a history table
      // For now, return mock data based on node modifications
      global $wpdb;
      $table = $wpdb->prefix . 'sfb_nodes';
      $node = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);

      if (!$node) {
        return new WP_Error('not_found', 'Node not found', ['status' => 404]);
      }

      // Mock history entries
      $history = [
        [
          'action' => 'Node created',
          'user' => get_userdata(get_current_user_id())->display_name ?? 'System',
          'timestamp' => current_time('mysql')
        ]
      ];

      return ['ok' => true, 'history' => $history];
    } catch (\Throwable $e) {
      error_log('SFB api_node_history error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }

  // =========================================================================
  // Phase 4: Catalog Write REST Handlers (moved from main plugin file)
  // =========================================================================

  /**
   * POST /node/save - Insert or update a node (title + settings.fields for model)
   *
   * @param WP_REST_Request $req Request object with node data
   * @return array|WP_Error Response with saved node, or error
   */
  public static function save_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $p = $req->get_json_params();

      $id        = isset($p['id']) ? intval($p['id']) : 0;
      $form_id   = intval($p['form_id'] ?? 0);
      $parent_id = intval($p['parent_id'] ?? 0);
      $node_type = sanitize_key($p['node_type'] ?? 'model');
      $title     = sanitize_text_field($p['title'] ?? '');
      $position  = intval($p['position'] ?? 0);
      $settings  = isset($p['settings']) && is_array($p['settings']) ? $p['settings'] : [];

      if (!$form_id || !$title || !in_array($node_type, ['category','product','type','model'], true)) {
        return new WP_Error('bad_request','Missing/invalid fields', ['status'=>400]);
      }

      $table = $wpdb->prefix.'sfb_nodes';
      $data = [
        'form_id'=>$form_id,
        'parent_id'=>$parent_id,
        'node_type'=>$node_type,
        'title'=>$title,
        'slug'=>sanitize_title($title),
        'position'=>$position,
        'settings_json'=> wp_json_encode($settings),
      ];

      if ($id) { $wpdb->update($table, $data, ['id'=>$id]); }
      else { $wpdb->insert($table, $data); $id = $wpdb->insert_id; }

      $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);
      $row['settings'] = $row['settings_json'] ? json_decode($row['settings_json'], true) : [];
      unset($row['settings_json']);

      return ['ok'=>true,'node'=>$row];
    } catch (\Throwable $e) {
      error_log('SFB api_save_node error: '.$e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /**
   * POST /node/create - Create a node (at end of siblings)
   *
   * @param WP_REST_Request $req Request object with node data
   * @return array|WP_Error Response with created node, or error
   */
  public static function create_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $p = $req->get_json_params();

      $form_id   = intval($p['form_id'] ?? 1);
      $parent_id = intval($p['parent_id'] ?? 0);
      $node_type = sanitize_key($p['node_type'] ?? 'category');
      $title     = sanitize_text_field($p['title'] ?? 'Untitled');
      if (!$form_id || !in_array($node_type,['category','product','type','model'],true)) {
        return new WP_Error('bad_request','Invalid form_id or node_type', ['status'=>400]);
      }

      $table = $wpdb->prefix.'sfb_nodes';
      // find next position within same parent
      $next_pos = (int)$wpdb->get_var( $wpdb->prepare(
        "SELECT COALESCE(MAX(position),0)+1 FROM $table WHERE form_id=%d AND parent_id=%d",
        $form_id, $parent_id
      ));

      $settings = [];
      if ($node_type === 'model') $settings = ['fields'=>['size'=>'','flange'=>'','thickness'=>'','ksi'=>'']];

      $wpdb->insert($table,[
        'form_id'=>$form_id,
        'parent_id'=>$parent_id,
        'node_type'=>$node_type,
        'title'=>$title,
        'slug'=>sanitize_title($title),
        'position'=>$next_pos,
        'settings_json'=> wp_json_encode($settings),
      ]);
      $id = $wpdb->insert_id;

      $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);
      $row['settings'] = $row['settings_json'] ? json_decode($row['settings_json'], true) : [];
      unset($row['settings_json']);

      return ['ok'=>true,'node'=>$row];
    } catch (\Throwable $e) {
      error_log('SFB api_create_node error: '.$e->getMessage());
      return new WP_Error('server_error',$e->getMessage(),['status'=>500]);
    }
  }

  /**
   * POST /node/delete - Delete a node and its subtree
   *
   * @param WP_REST_Request $req Request object with node ID
   * @return array|WP_Error Response with deleted IDs, or error
   */
  public static function delete_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $p = $req->get_json_params();
      $form_id = intval($p['form_id'] ?? 1);
      $node_id = intval($p['id'] ?? 0);

      if (!$node_id) {
        return new WP_Error('bad_request', 'Missing id', ['status' => 400]);
      }

      // Capture parent_id before delete so we can normalize siblings
      $nodes_table = $wpdb->prefix . 'sfb_nodes';
      $parent_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT parent_id FROM {$nodes_table} WHERE form_id=%d AND id=%d",
        $form_id, $node_id
      ) );

      // Call helper method on plugin instance
      $deleted = [];
      if ($sfb_plugin && method_exists($sfb_plugin, 'delete_node_recursive')) {
        $deleted = $sfb_plugin->delete_node_recursive( $form_id, $node_id );
      }

      // Reindex remaining siblings of the old parent
      if ($sfb_plugin && method_exists($sfb_plugin, 'normalize_sibling_positions')) {
        $sfb_plugin->normalize_sibling_positions( $form_id, (int) $parent_id );
      }

      return [
        'ok'          => true,
        'deleted_ids' => array_values( $deleted ),
      ];
    } catch (\Throwable $e) {
      error_log('SFB api_delete_node error: '.$e->getMessage());
      return new WP_Error('server_error',$e->getMessage(),['status'=>500]);
    }
  }

  /**
   * POST /node/reorder - Move a node up/down within its siblings (position swap)
   *
   * @param WP_REST_Request $req Request object with node ID and direction
   * @return array|WP_Error Response with moved status, or error
   */
  public static function reorder_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $p  = $req->get_json_params();
      $id = intval($p['id'] ?? 0);
      $dir = sanitize_key($p['direction'] ?? 'up'); // 'up' or 'down'
      if (!$id || !in_array($dir, ['up','down'], true)) {
        return new WP_Error('bad_request', 'Invalid params', ['status' => 400]);
      }

      $table = $wpdb->prefix . 'sfb_nodes';
      $node  = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);
      if (!$node) return new WP_Error('not_found', 'Node not found', ['status'=>404]);

      // Normalize parent_id: treat NULL same as 0 for sibling grouping
      $parent_id = isset($node['parent_id']) ? intval($node['parent_id']) : 0;

      // Build neighbor query safely (no string fragments into %s)
      if ($dir === 'up') {
        $sql = "SELECT * FROM $table
                WHERE form_id=%d AND parent_id=%d AND position < %d
                ORDER BY position DESC LIMIT 1";
        $neighbor = $wpdb->get_row($wpdb->prepare($sql, $node['form_id'], $parent_id, $node['position']), ARRAY_A);
      } else {
        $sql = "SELECT * FROM $table
                WHERE form_id=%d AND parent_id=%d AND position > %d
                ORDER BY position ASC LIMIT 1";
        $neighbor = $wpdb->get_row($wpdb->prepare($sql, $node['form_id'], $parent_id, $node['position']), ARRAY_A);
      }

      if (!$neighbor) {
        return ['ok' => true, 'moved' => false]; // already at edge
      }

      // Swap positions atomically-ish (two updates)
      $posA = intval($node['position']);
      $posB = intval($neighbor['position']);

      $wpdb->update($table, ['position'=>$posB], ['id'=>$node['id']]);
      $wpdb->update($table, ['position'=>$posA], ['id'=>$neighbor['id']]);

      return ['ok' => true, 'moved' => true];
    } catch (\Throwable $e) {
      error_log('SFB api_reorder_node error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /**
   * POST /node/duplicate - Duplicate a node and all its descendants
   *
   * @param WP_REST_Request $req Request object with node ID
   * @return array|WP_Error Response with new node ID, or error
   */
  public static function duplicate_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $id = intval($req->get_param('id'));
      if (!$id) return new WP_Error('bad_request','Missing id', ['status'=>400]);

      $nodes = $wpdb->prefix . 'sfb_nodes';

      // Fetch original
      $orig = $wpdb->get_row($wpdb->prepare("SELECT * FROM $nodes WHERE id=%d", $id), ARRAY_A);
      if (!$orig) return new WP_Error('not_found','Node not found', ['status'=>404]);

      // Compute next position for same parent
      $next_pos = intval($wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(MAX(position),0)+1 FROM $nodes WHERE parent_id=%d AND form_id=%d",
        $orig['parent_id'], $orig['form_id']
      )));

      // Recursive clone - call helper method on plugin instance
      $new_root_id = 0;
      if ($sfb_plugin && method_exists($sfb_plugin, 'clone_node_recursive')) {
        $map = []; // old_id => new_id
        $new_root_id = $sfb_plugin->clone_node_recursive($orig['id'], $orig['parent_id'], $next_pos, $map);
      }

      // Rename root: title (copy)
      $wpdb->update($nodes, ['title' => $orig['title'].' (copy)'], ['id' => $new_root_id]);

      return ['ok'=>true, 'new_id'=>$new_root_id];
    } catch (\Throwable $e) {
      error_log('SFB api_duplicate_node error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status'=>500]);
    }
  }

  /**
   * POST /node/move - Move node to new parent and/or position (drag & drop)
   *
   * @param WP_REST_Request $req Request object with node ID, parent ID, and position
   * @return array|WP_Error Response with ok status, or error
   */
  public static function move_node($req) {
    try {
      global $sfb_plugin, $wpdb;

      // Ensure tables exist
      if ($sfb_plugin && method_exists($sfb_plugin, 'ensure_tables')) {
        $sfb_plugin->ensure_tables();
      }

      $p = $req->get_json_params();
      $id = intval($p['id'] ?? 0);
      $new_parent_id = intval($p['parent_id'] ?? 0);
      $new_position = floatval($p['position'] ?? 0);

      if (!$id) {
        return new WP_Error('bad_request', 'Missing id', ['status' => 400]);
      }

      $table = $wpdb->prefix . 'sfb_nodes';
      $node = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id), ARRAY_A);
      if (!$node) {
        return new WP_Error('not_found', 'Node not found', ['status' => 404]);
      }

      // Get new parent (if not root)
      $new_parent = null;
      if ($new_parent_id > 0) {
        $new_parent = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $new_parent_id), ARRAY_A);
        if (!$new_parent) {
          return new WP_Error('bad_request', 'Parent node not found', ['status' => 400]);
        }
      }

      // Server-side hierarchy validation
      $node_type = $node['node_type'];
      $parent_type = $new_parent ? $new_parent['node_type'] : null;

      $allowed_children = [
        'category' => 'product',
        'product'  => 'type',
        'type'     => 'model',
        'model'    => null,
      ];

      // Reordering under same parent is always allowed
      $same_parent = (string)$node['parent_id'] === (string)$new_parent_id;

      // Nesting validation: parent must allow this child type
      $valid_nesting = false;
      if ($parent_type) {
        $valid_nesting = isset($allowed_children[$parent_type]) && $allowed_children[$parent_type] === $node_type;
      } elseif ($new_parent_id === 0) {
        // Moving to root - only categories allowed
        $valid_nesting = $node_type === 'category';
      }

      if (!$same_parent && !$valid_nesting) {
        return new WP_Error(
          'sfb_invalid_nesting',
          __('Invalid move: node type cannot be nested under the selected parent.', 'submittal-form-builder'),
          ['status' => 400]
        );
      }

      // Update node with new parent_id and position
      $wpdb->update($table, [
        'parent_id' => $new_parent_id,
        'position' => $new_position
      ], ['id' => $id]);

      // Normalize positions for siblings in the new parent
      $siblings = $wpdb->get_results($wpdb->prepare(
        "SELECT id, position FROM $table WHERE form_id=%d AND parent_id=%d ORDER BY position ASC",
        $node['form_id'], $new_parent_id
      ), ARRAY_A);

      $pos = 1;
      foreach($siblings as $sib){
        $wpdb->update($table, ['position' => $pos++], ['id' => $sib['id']]);
      }

      return ['ok' => true];
    } catch (\Throwable $e) {
      error_log('SFB api_move_node error: ' . $e->getMessage());
      return new WP_Error('server_error', $e->getMessage(), ['status' => 500]);
    }
  }
}
