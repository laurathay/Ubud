<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bayleaf
 * @since 1.0.0
 */

		/**
		 * Fires immediately after site content area.
		 *
		 * @since 1.0.0
		 *
		 * @param str $calledby Hook by which the function has been called.
		 */
		do_action( 'bayleaf_after_site_content', 'after_site_content' );
?>

		<footer id="colophon"<?php bayleaf_attr( 'site-footer' ); ?>>

				<?php
				/**
				 * Fires immediately after opening site footer tag.
				 *
				 * @since 1.0.0
				 *
				 * @param str $calledby Hook by which the function has been called.
				 */
				do_action( 'bayleaf_inside_footer', 'inside_footer' );
				?>

		</footer><!-- #colophon -->

		<?php
		/**
		 * Must be fired immediately before end of body tag.
		 *
		 * @since 1.0.0
		 */
		wp_footer();
		?>
	</body>
</html>
