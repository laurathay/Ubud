<?php
/**
 * The template part for displaying categories of current post
 *
 * @package Bayleaf
 * @since 1.0.0
 */

$bayleaf_categories_list = get_the_category_list( esc_html_x( ', ', 'Used between category list items, there is a space after the comma.', 'bayleaf' ) );

if ( $bayleaf_categories_list ) :
	?>
	<span<?php bayleaf_attr( 'meta-categories' ); ?>>
		<?php
		if ( is_singular() ) {
			printf(
				'<span class="meta-title">%s</span>',
				bayleaf_get_icon( [ 'icon' => 'folder' ] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		echo $bayleaf_categories_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</span><!-- .meta-categories -->
	<?php
endif;
