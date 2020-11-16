<?php
/**
 * The main template file
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bayleaf
 * @since 1.0.0
 */

get_header(); ?>

<div id="content"<?php bayleaf_attr( 'site-content' ); ?>>

	<?php
	/**
	 * Fires immediately after opening of main site content tag.
	 *
	 * @since 1.0.0
	 *
	 * @param str $calledby Hook by which the function has been called.
	 */
	do_action( 'bayleaf_on_top_of_site_content', 'on_top_of_site_content' );
	?>

	<div id="primary"<?php bayleaf_attr( 'content-area' ); ?>>

		<?php
		/**
		 * Fires immediately after opening of primary content area.
		 *
		 * @since 1.0.0
		 *
		 * @param str $calledby Hook by which the function has been called.
		 */
		do_action( 'bayleaf_before_main_content', 'before_main_content' );
		?>

		<main id="main"<?php bayleaf_attr( 'site-main' ); ?>>

			<?php
			if ( have_posts() ) :

				/**
				 * Conditionally fires for displaying primary content.
				 *
				 * @since 1.0.0
				 *
				 * @param str $calledby Hook by which the function has been called.
				 */
				do_action( 'bayleaf_inside_main_content', 'inside_main_content' );

			else :

				/**
				 * Include template if no content is available.
				 */
				get_template_part( 'template-parts/content/content', 'none' );
			endif;
			?>

		</main><!-- #main -->

		<?php
		/**
		 * Fires immediately before closing primary content area.
		 *
		 * @since 1.0.0
		 *
		 * @param str $calledby Hook by which the function has been called.
		 */
		do_action( 'bayleaf_after_main_content', 'after_main_content' );
		?>

	</div><!-- #primary -->

	<?php
	get_sidebar();

	/**
	 * Fires at the bottom of site content area.
	 *
	 * @since 1.0.0
	 *
	 * @param str $calledby Hook by which the function has been called.
	 */
	do_action( 'bayleaf_bottom_of_site_content', 'bottom_of_site_content' );
	?>

</div><!-- #content -->

<?php
get_footer();
