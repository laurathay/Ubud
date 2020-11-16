<?php
/**
 * Bayleaf Theme back compat functionality
 *
 * Prevents bayleaf from running on PHP versions prior to 5.6,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in PHP 5.6.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Prevent switching to bayleaf on old versions of PHP.
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
 * bayleaf on PHP versions prior to 5.6.
 *
 * @since 1.0.0
 *
 * @global const PHP_VERSION PHP version.
 */
function bayleaf_upgrade_notice() {
	$message = sprintf(
		/* translators: %s: Installed PHP version */
		esc_html__( 'Bayleaf requires at least PHP version 5.6. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
		PHP_VERSION
	);
	printf( '<div class="error"><p>%s</p></div>', $message ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prevents the Customizer from being loaded on PHP versions prior to 5.6.
 *
 * @since 1.0.0
 *
 * @global const PHP_VERSION PHP version.
 */
function bayleaf_prevent_customize_load() {
	wp_die(
		sprintf(
			/* translators: %s: Installed PHP version */
			esc_html__( 'bayleaf requires at least PHP version 5.6. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
			PHP_VERSION
		),
		'',
		array( 'back_link' => true )
	); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'load-customize.php', 'bayleaf_prevent_customize_load' );

/**
 * Prevents the Theme Preview from being loaded on PHP versions prior to 5.6.
 *
 * @since 1.0.0
 *
 * @global const PHP_VERSION PHP version.
 */
function bayleaf_prevent_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die(
			sprintf(
				/* translators: %s: Installed PHP version */
				esc_html__( 'bayleaf requires at least PHP version 5.6. You are running version %s. Please upgrade and try again.', 'bayleaf' ),
				PHP_VERSION
			)
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'template_redirect', 'bayleaf_prevent_preview' );
