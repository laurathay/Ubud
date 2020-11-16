<?php
/**
 * The template part for displaying entry title for current post
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( is_singular() ) {
	the_title( '<h1' . bayleaf_get_attr( 'entry-header-title' ) . '>', '</h1>' );

} else {
	the_title(
		sprintf(
			'<h2%1$s><a href="%2$s" rel="bookmark">',
			bayleaf_get_attr( 'entry-header-title' ),
			esc_url( get_permalink() )
		),
		'</a></h2>'
	);
}
