<?php
/**
 * The template part for displaying link to edit current post
 *
 * @package Bayleaf
 * @since 1.0.0
 */

edit_post_link(
	sprintf(
		/* translators: %s: Name of current post */
		__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'bayleaf' ),
		get_the_title()
	),
	'<span' . bayleaf_get_attr( 'meta-edit-link' ) . '>',
	'</span>'
);
