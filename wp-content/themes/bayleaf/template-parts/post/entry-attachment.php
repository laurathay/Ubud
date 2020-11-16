<?php
/**
 * The template part for displaying current image attachment.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

?>

<div<?php bayleaf_attr( 'entry-attachment' ); ?>>

	<?php echo wp_get_attachment_image( get_the_ID(), 'large' ); ?>

	<?php if ( has_excerpt() ) : ?>
		<div<?php bayleaf_attr( 'entry-caption' ); ?>><?php the_excerpt(); ?></div>
	<?php endif; ?>

</div><!-- .entry-attachment -->
