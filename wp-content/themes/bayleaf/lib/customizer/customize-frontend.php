<?php
/**
 * Help implement theme customizer modifications to front end.
 *
 * @link https://codex.wordpress.org/Theme_Customization_API
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Get dynamically generated inline css to be added to registered main stylesheet.
 *
 * @since 1.0.0
 *
 * @return string Verified css string or empty string.
 */
function bayleaf_get_inline_css() {

	/**
	 * Filter inline styles to be injected to front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param string $css String of inline styles or empty string.
	 */
	$css = apply_filters( 'bayleaf_inline_styles', '' );

	if ( ! $css ) {
		return '';
	}

	return bayleaf_prepare_css( $css );
}

/**
 * Retrieve theme modification value for the current theme.
 *
 * Wrapper function for 'get_theme_mod()' to get escaped (safe to output) theme
 * modification values (if any) or default value.
 *
 * @since 1.0.0
 *
 * @param string $name Theme modification name.
 * @param string $type Type of mod value.
 * @return mixed escaped theme modification value.
 */
function bayleaf_get_mod( $name, $type = 'html' ) {
	$mod_val = get_theme_mod( $name, bayleaf_get_default_value( $name ) );
	$mod_val = bayleaf_escape( $mod_val, $type );

	return $mod_val;
}

/**
 * Return escaped theme modification value.
 *
 * @since 1.0.0
 *
 * @param mixed  $mod  Theme modification value.
 * @param string $type Type of theme modification.
 * @return mixed
 */
function bayleaf_escape( $mod, $type ) {
	switch ( $type ) {
		case 'none':
			$escaped_mod = $mod;
			break;

		case 'html':
			$escaped_mod = esc_html( $mod );
			break;

		case 'attr':
			$escaped_mod = esc_attr( $mod );
			break;

		case 'integer':
			$escaped_mod = absint( $mod );
			break;

		case 'float':
			$escaped_mod = abs( floatval( $mod ) );
			break;

		case 'url':
			$escaped_mod = esc_url( $mod );
			break;

		case 'color':
			$escaped_mod = sanitize_hex_color( $mod ) ? $mod : false;
			break;

		case 'email':
			$escaped_mod = ( is_email( $mod ) ) ? $mod : false;
			break;

		case 'textarea':
			$escaped_mod = esc_textarea( $mod );
			break;

		case 'css_selector':
			$escaped_mod = wp_strip_all_tags( str_replace( [ "'", '@' ], '', $mod ), true );
			break;

		default:
			$escaped_mod = false;
			break;
	}

	return $escaped_mod;
}
