<?php
/**
 * The template part for displaying current post/page content.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

the_content(
	sprintf(
		/* translators: %s: Name of current post */
		esc_html__( 'Continue reading %s', 'bayleaf' ),
		the_title( '<span class="screen-reader-text">', '</span>', false )
	)
);

wp_link_pages(
	[
		'before' => '<div' . bayleaf_get_attr( 'page-links' ) . '>' . esc_html__( 'Pages:', 'bayleaf' ),
		'after'  => '</div>',
	]
);
