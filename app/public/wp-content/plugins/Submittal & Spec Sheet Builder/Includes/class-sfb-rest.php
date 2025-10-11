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

    if (!$sfb_plugin || !($sfb_plugin instanceof Submittal_Form_Builder)) {
      return;
    }

    // Health/Status endpoints (public)
    register_rest_route('sfb/v1', '/health', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => function() use ($sfb_plugin) {
        return ['ok' => true, 'version' => Submittal_Form_Builder::VERSION];
      }
    ]);

    register_rest_route('sfb/v1', '/ping', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => function() {
        return ['ok' => true, 'pong' => true];
      }
    ]);

    register_rest_route('sfb/v1', '/status', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [$sfb_plugin, 'api_get_status']
    ]);

    // Catalog Management (Admin)
    register_rest_route('sfb/v1', '/form/seed', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_seed_sample_catalog']
    ]);

    register_rest_route('sfb/v1', '/form/wipe', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_wipe_form']
    ]);

    register_rest_route('sfb/v1', '/form/(?P<id>\d+)', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => [$sfb_plugin, 'api_get_form']
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
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_save_node']
    ]);

    register_rest_route('sfb/v1', '/node/create', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_create_node']
    ]);

    register_rest_route('sfb/v1', '/node/delete', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_delete_node']
    ]);

    register_rest_route('sfb/v1', '/node/reorder', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_reorder_node']
    ]);

    register_rest_route('sfb/v1', '/node/duplicate', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_duplicate_node']
    ]);

    register_rest_route('sfb/v1', '/node/move', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_move_node']
    ]);

    register_rest_route('sfb/v1', '/node/history', [
      'methods' => 'GET',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_node_history']
    ]);

    // Bulk Operations (Admin)
    register_rest_route('sfb/v1', '/bulk/delete', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_bulk_delete']
    ]);

    register_rest_route('sfb/v1', '/bulk/move', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_bulk_move']
    ]);

    register_rest_route('sfb/v1', '/bulk/duplicate', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_bulk_duplicate']
    ]);

    register_rest_route('sfb/v1', '/bulk/export', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_bulk_export']
    ]);

    // PDF Generation (Public)
    register_rest_route('sfb/v1', '/generate', [
      'methods' => 'POST',
      'permission_callback' => '__return_true', // public submission allowed
      'callback' => [$sfb_plugin, 'api_generate_packet']
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
      'callback' => [$sfb_plugin, 'api_get_settings']
    ]);

    register_rest_route('sfb/v1', '/settings', [
      'methods' => 'POST',
      'permission_callback' => function() { return current_user_can('manage_options'); },
      'callback' => [$sfb_plugin, 'api_save_settings']
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
}
