<?php
/**
 * The template part for displaying tags of current post
 *
 * @package Bayleaf
 * @since 1.0.0
 */

$bayleaf_tags_list = get_the_tag_list( '', esc_html_x( ', ', 'Used between tags list items, there is a space after the comma.', 'bayleaf' ) );

if ( $bayleaf_tags_list ) :
	?>
	<span<?php bayleaf_attr( 'meta-tags' ); ?>>
		<?php
		printf(
			'<span class="meta-title">%s</span>',
			bayleaf_get_icon( [ 'icon' => 'tags' ] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo $bayleaf_tags_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</span><!-- .meta-tags -->
	<?php
endif;
