<?php
/**
 * License Operation Logs Admin Page
 *
 * Displays audit trail of license activations and deactivations
 *
 * @package SubmittalBuilder
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Render license logs page
 */
function sfb_render_license_logs_page() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'submittal-spec-sheet-builder'));
	}

	global $wpdb;
	$table = $wpdb->prefix . 'sfb_license_log';

	// Handle clear logs action
	if (isset($_POST['sfb_clear_logs']) && wp_verify_nonce($_POST['sfb_logs_nonce'], 'sfb_clear_logs_action')) {
		$wpdb->query("TRUNCATE TABLE $table");
		echo '<div class="notice notice-success"><p>' . __('License logs cleared successfully.', 'submittal-spec-sheet-builder') . '</p></div>';
	}

	// Pagination
	$per_page = 50;
	$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
	$offset = ($current_page - 1) * $per_page;

	// Get total count
	$total_logs = $wpdb->get_var("SELECT COUNT(*) FROM $table");
	$total_pages = ceil($total_logs / $per_page);

	// Get logs
	$logs = $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
		$per_page,
		$offset
	), ARRAY_A);

	?>
	<div class="wrap">
		<h1><?php esc_html_e('License Operation Logs', 'submittal-spec-sheet-builder'); ?></h1>
		<p><?php esc_html_e('Audit trail of all license activation and deactivation attempts.', 'submittal-spec-sheet-builder'); ?></p>

		<div class="tablenav top">
			<div class="alignleft actions">
				<form method="post" style="display:inline;" onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to clear all logs? This action cannot be undone.', 'submittal-spec-sheet-builder'); ?>');">
					<?php wp_nonce_field('sfb_clear_logs_action', 'sfb_logs_nonce'); ?>
					<input type="hidden" name="sfb_clear_logs" value="1">
					<button type="submit" class="button">
						<?php esc_html_e('Clear All Logs', 'submittal-spec-sheet-builder'); ?>
					</button>
				</form>
			</div>
			<div class="tablenav-pages">
				<?php
				if ($total_pages > 1) {
					echo paginate_links([
						'base' => add_query_arg('paged', '%#%'),
						'format' => '',
						'prev_text' => __('&laquo;', 'submittal-spec-sheet-builder'),
						'next_text' => __('&raquo;', 'submittal-spec-sheet-builder'),
						'total' => $total_pages,
						'current' => $current_page
					]);
				}
				?>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="width:50px;"><?php esc_html_e('ID', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:150px;"><?php esc_html_e('Date/Time', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:100px;"><?php esc_html_e('Action', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:100px;"><?php esc_html_e('Status', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:150px;"><?php esc_html_e('License Key Hash', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:150px;"><?php esc_html_e('Email Hash', 'submittal-spec-sheet-builder'); ?></th>
					<th><?php esc_html_e('Error Message', 'submittal-spec-sheet-builder'); ?></th>
					<th style="width:200px;"><?php esc_html_e('User Agent', 'submittal-spec-sheet-builder'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($logs)): ?>
					<tr>
						<td colspan="8" style="text-align:center;padding:40px;">
							<?php esc_html_e('No license operation logs found.', 'submittal-spec-sheet-builder'); ?>
						</td>
					</tr>
				<?php else: ?>
					<?php foreach ($logs as $log): ?>
						<tr>
							<td><?php echo esc_html($log['id']); ?></td>
							<td><?php echo esc_html($log['created_at']); ?></td>
							<td>
								<span class="sfb-log-action sfb-log-action-<?php echo esc_attr($log['action']); ?>">
									<?php echo esc_html(ucfirst($log['action'])); ?>
								</span>
							</td>
							<td>
								<span class="sfb-log-status sfb-log-status-<?php echo esc_attr($log['status']); ?>">
									<?php echo esc_html(ucfirst($log['status'])); ?>
								</span>
							</td>
							<td style="font-family:monospace;font-size:11px;">
								<?php echo esc_html(substr($log['license_key_hash'], 0, 16)) . '...'; ?>
							</td>
							<td style="font-family:monospace;font-size:11px;">
								<?php echo esc_html(substr($log['email_hash'], 0, 16)) . '...'; ?>
							</td>
							<td>
								<?php if (!empty($log['error_message'])): ?>
									<span style="color:#dc3232;">
										<?php echo esc_html($log['error_message']); ?>
									</span>
								<?php else: ?>
									<span style="color:#46b450;">—</span>
								<?php endif; ?>
							</td>
							<td style="font-size:11px;">
								<?php echo esc_html($log['user_agent'] ? substr($log['user_agent'], 0, 50) . '...' : '—'); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<?php
				if ($total_pages > 1) {
					echo paginate_links([
						'base' => add_query_arg('paged', '%#%'),
						'format' => '',
						'prev_text' => __('&laquo;', 'submittal-spec-sheet-builder'),
						'next_text' => __('&raquo;', 'submittal-spec-sheet-builder'),
						'total' => $total_pages,
						'current' => $current_page
					]);
				}
				?>
			</div>
		</div>

		<style>
			.sfb-log-action {
				display: inline-block;
				padding: 4px 10px;
				border-radius: 3px;
				font-size: 12px;
				font-weight: 600;
			}
			.sfb-log-action-activate {
				background: #e7f3ff;
				color: #0073aa;
			}
			.sfb-log-action-deactivate {
				background: #fff3e7;
				color: #d68a00;
			}
			.sfb-log-status {
				display: inline-block;
				padding: 4px 10px;
				border-radius: 3px;
				font-size: 12px;
				font-weight: 600;
			}
			.sfb-log-status-success {
				background: #ecf7ed;
				color: #46b450;
			}
			.sfb-log-status-failed {
				background: #fef7f7;
				color: #dc3232;
			}
		</style>

		<div class="sfb-logs-info" style="margin-top:30px;padding:20px;background:#f9f9f9;border-left:4px solid #0073aa;">
			<h3 style="margin-top:0;"><?php esc_html_e('About License Logs', 'submittal-spec-sheet-builder'); ?></h3>
			<p><?php esc_html_e('This audit trail records all license activation and deactivation attempts for compliance and debugging purposes.', 'submittal-spec-sheet-builder'); ?></p>
			<ul>
				<li><?php esc_html_e('License keys and emails are hashed (SHA-256) for privacy compliance (GDPR).', 'submittal-spec-sheet-builder'); ?></li>
				<li><?php esc_html_e('IP addresses are also hashed and cannot be reversed.', 'submittal-spec-sheet-builder'); ?></li>
				<li><?php esc_html_e('Logs can be used for security audits and troubleshooting activation issues.', 'submittal-spec-sheet-builder'); ?></li>
				<li><?php esc_html_e('Logs are stored indefinitely unless manually cleared.', 'submittal-spec-sheet-builder'); ?></li>
			</ul>
		</div>
	</div>
	<?php
}
