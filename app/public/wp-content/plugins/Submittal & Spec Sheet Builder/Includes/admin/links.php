<?php
/**
 * Centralized External Links Configuration
 *
 * Single source of truth for all external URLs used in License & Support pages.
 * Links can be customized via WordPress options or filtered programmatically.
 *
 * @package SubmittalBuilder
 */

if (!defined('ABSPATH')) exit;

/**
 * Get all external links with fallback defaults
 *
 * @return array Associative array of link keys and URLs
 */
function sfb_get_links() {
	$links = [
		'account'    => get_option('sfb_link_account') ?: 'https://webstuffguylabs.com/my-account/',
		'invoices'   => get_option('sfb_link_invoices') ?: 'https://webstuffguylabs.com/my-account/orders/',
		'docs'       => get_option('sfb_link_docs') ?: 'https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/documentation/',
		'tutorials'  => get_option('sfb_link_tutorials') ?: 'https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/tutorials/',
		'roadmap'    => get_option('sfb_link_roadmap') ?: 'https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/roadmap-feature-requests/',
		'support'    => get_option('sfb_link_support') ?: 'https://webstuffguylabs.com/support/',
		'renew'      => get_option('sfb_link_renew') ?: 'https://webstuffguylabs.com/my-account/',
		'pricing'    => get_option('sfb_link_pricing') ?: 'https://webstuffguylabs.com/plugins/submittal-spec-sheet-builder/',
		'agency_license' => get_option('sfb_link_agency_license') ?: 'https://webstuffguylabs.com/product/submittal-spec-sheet-builder-pro-agency/',
		'single_license' => get_option('sfb_link_single_license') ?: 'https://webstuffguylabs.com/product/submittal-spec-sheet-builder-single-site/',
	];

	/**
	 * Filter external links before returning
	 *
	 * Allows themes/plugins to override link destinations.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Associative array of link keys and URLs
	 *
	 * Example usage:
	 * add_filter('sfb_links', function($links) {
	 *   $links['docs'] = 'https://custom-docs.com';
	 *   return $links;
	 * });
	 */
	return apply_filters('sfb_links', $links);
}

/**
 * Get a specific link by key
 *
 * @param string $key Link key (account, docs, support, etc.)
 * @param bool $fallback Whether to return empty string if not found
 * @return string URL or empty string
 */
function sfb_get_link($key, $fallback = true) {
	$links = sfb_get_links();

	if (isset($links[$key]) && !empty($links[$key])) {
		return $links[$key];
	}

	return $fallback ? '' : '#';
}

/**
 * Check if a link is configured (not using default)
 *
 * @param string $key Link key
 * @return bool True if link has been customized
 */
function sfb_is_link_configured($key) {
	return !empty(get_option('sfb_link_' . $key));
}

/**
 * Render a button with proper states (active, disabled, coming soon)
 *
 * @param string $key Link key
 * @param string $label Button label
 * @param string $classes Additional CSS classes
 * @param array $attrs Additional HTML attributes
 * @return string HTML button/link element
 */
function sfb_render_link_button($key, $label, $classes = 'button', $attrs = []) {
	$url = sfb_get_link($key);
	$is_disabled = empty($url) || $url === '#';

	if ($is_disabled) {
		// Disabled state with tooltip
		$attrs['disabled'] = 'disabled';
		$attrs['title'] = __('Coming soon', 'submittal-builder');
		$classes .= ' disabled';

		return sprintf(
			'<button class="%s" %s>%s</button>',
			esc_attr($classes),
			sfb_build_html_attrs($attrs),
			esc_html($label)
		);
	}

	// Active link
	$default_attrs = [
		'target' => '_blank',
		'rel' => 'noopener',
	];

	// Don't open mailto links in new tab
	if (strpos($url, 'mailto:') === 0) {
		unset($default_attrs['target']);
		unset($default_attrs['rel']);
	}

	$attrs = array_merge($default_attrs, $attrs);

	return sprintf(
		'<a href="%s" class="%s" %s>%s</a>',
		esc_url($url),
		esc_attr($classes),
		sfb_build_html_attrs($attrs),
		esc_html($label)
	);
}

/**
 * Build HTML attributes string from array
 *
 * @param array $attrs Associative array of attributes
 * @return string HTML attributes string
 */
function sfb_build_html_attrs($attrs) {
	$html = [];
	foreach ($attrs as $key => $value) {
		if ($value === true) {
			$html[] = esc_attr($key);
		} elseif ($value !== false && $value !== null) {
			$html[] = sprintf('%s="%s"', esc_attr($key), esc_attr($value));
		}
	}
	return implode(' ', $html);
}
