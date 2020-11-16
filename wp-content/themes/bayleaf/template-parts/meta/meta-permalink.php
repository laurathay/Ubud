<?php
/**
 * The template part for displaying post permalink with quick action toggle.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

?>
<a href="<?php the_permalink(); ?>"<?php bayleaf_attr( 'post-permalink' ); ?>>
	<span class="screen-reader-text"><?php the_title(); ?></span>
</a>
