<?php
/**
 * Bayleaf Theme back compat functionality
 *
 * Prevents bayleaf from running on WordPress versions prior to 4.9,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.9.
 *
 * This file incorporates code from Twenty Fifteen WordPress Theme,
 * Copyright 2014-2016 WordPress.org & Automattic.com Twenty Fifteen is
 * distributed under the terms of the GNU GPL.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Prevent switching to bayleaf on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since 1.0.0
 */
function bayleaf_prevent_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'bayleaf_upgrade_notice' );
}
add_action( 'after_switch_theme', 'bayleaf_prevent_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * bayleaf on WordPress versions prior to 4.9.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function bayleaf_upgrade_notice() {
	$message = sprintf(
		/* translators: %s: Installed WordPress version */
		esc_html__( 'Bayleaf requires at least WordPress version 4.9. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
		$GLOBALS['wp_version']
	); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	printf( '<div class="error"><p>%s</p></div>', $message ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.9.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function bayleaf_prevent_customize_load() {
	wp_die(
		sprintf(
			/* translators: %s: Installed WordPress version */
			esc_html__( 'bayleaf requires at least WordPress version 4.9. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
			$GLOBALS['wp_version'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		),
		'',
		[ 'back_link' => true ]
	);
}
add_action( 'load-customize.php', 'bayleaf_prevent_customize_load' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.9.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function bayleaf_prevent_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die(
			sprintf(
				/* translators: %s: Installed WordPress version */
				esc_html__( 'bayleaf requires at least WordPress version 4.9. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
				$GLOBALS['wp_version'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			)
		);
	}
}
add_action( 'template_redirect', 'bayleaf_prevent_preview' );
