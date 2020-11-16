<?php
/**
 * The template part for displaying link to write comment in current post
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
	?>
	<span<?php bayleaf_attr( 'meta-comments-link' ); ?>>
		<?php comments_popup_link(); ?>
	</span><!-- .meta-comments-link -->
	<?php
endif;
