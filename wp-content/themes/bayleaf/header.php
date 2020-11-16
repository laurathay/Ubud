<?php
/**
 * Header Template Part
 *
 * Template part file that contains the HTML document head and opening HTML
 * body elements as well as the site header.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bayleaf
 * @since 1.0.0
 */

?><!DOCTYPE html>

<html class="no-js no-svg" <?php language_attributes(); ?>>

<head<?php bayleaf_attr( 'head', false ); ?>>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>

</head>

<body<?php bayleaf_attr( 'body', false ); ?> <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	?>

	<header id="masthead"<?php bayleaf_attr( 'site-header' ); ?>>

		<?php
		/**
		 * Fires inside site header.
		 *
		 * @since 1.0.0
		 *
		 * @param str $calledby Hook by which the function has been called.
		 */
		do_action( 'bayleaf_inside_header', 'inside_header' );
		?>

	</header><!-- #masthead -->

	<?php
	/**
	 * Fires immediately after closing of header tag.
	 *
	 * @since 1.0.0
	 *
	 * @param str $calledby Hook by which the function has been called.
	 */
	do_action( 'bayleaf_after_header', 'after_header' );
	?>
