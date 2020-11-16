<?php
/**
 * The template part to run main loop
 *
 * @package Bayleaf
 * @since 1.0.0
 */

while ( have_posts() ) :

	the_post();

	/*
	 * Include the Post-Format-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
	 */
	get_template_part( 'template-parts/content/content', get_post_format() );

	if ( comments_open() || get_comments_number() ) {
		// If comments are open or we have at least one comment, load up the comment template.
		comments_template();
	}

endwhile;
