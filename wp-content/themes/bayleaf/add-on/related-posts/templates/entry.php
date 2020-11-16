<?php
/**
 * Single post entry display Template
 *
 * @package Bayleaf
 * @since 1.0.0
 */

?>

<div class="dp-entry entry fw-tab-6 fw-tabr-4">
	<div class="entry-index-wrapper">
		<?php
		if ( bayleaf_get_mod( 'bayleaf_thumbnail_placeholder', 'none' ) || has_post_thumbnail() ) {
			$bayleaf_featured_content = [
				[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-permalink' ],
				[ 'bayleaf_markup', 'dp-thumbnail', [ [ 'the_post_thumbnail', 'bayleaf-medium' ] ] ],
			];

			bayleaf_markup( 'dp-featured-content', $bayleaf_featured_content );
		}
		?>
		<div class="sub-entry">
			<div class="dp-categories">
				<?php the_category( ', ' ); ?>
			</div>
			<?php
			if ( get_the_title() ) {
				the_title(
					sprintf(
						'<h2 class="dp-title"><a class="dp-title-link" href="%s" rel="bookmark">',
						esc_url( get_permalink() )
					),
					'</a></h2>'
				);
			}
			?>
		</div>
	</div>
</div>
