<?php
/**
 * The template part for displaying current post thumbnail on index pages.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( has_post_thumbnail() ) :
	$bayleaf_thumb_size = is_singular() ? 'bayleaf-large' : 'bayleaf-medium';
	?>
	<div<?php bayleaf_attr( 'entry-thumbnail' ); ?>>
		<?php the_post_thumbnail( $bayleaf_thumb_size ); ?>
	</div><!-- .entry-thumbnail -->
	<?php
endif;
