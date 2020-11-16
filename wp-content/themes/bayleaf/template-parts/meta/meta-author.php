<?php
/**
 * The template part for displaying current post author name
 *
 * This template can be used outside main loop.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

global $post;
?>

<span<?php bayleaf_attr( 'meta-author' ); ?>>
	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ) ); ?>"<?php bayleaf_attr( 'url' ); ?>><span<?php bayleaf_attr( 'name', false ); ?>> <?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></span></a>
</span><!-- .meta-author -->
