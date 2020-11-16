<?php
/**
 * The template part for displaying page-header for home, search or archive pages.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

if ( is_home() && ! is_front_page() ) :?>

	<header<?php bayleaf_attr( 'page-header', [ 'class' => 'screen-reader-text' ] ); ?>>
		<h1<?php bayleaf_attr( 'page-header-title' ); ?>><?php single_post_title(); ?></h1>
	</header><!-- .page-header -->

	<?php
elseif ( is_archive() ) :
	?>

	<header<?php bayleaf_attr( 'page-header', [ 'class' => 'archive-page-header' ] ); ?>>
		<?php
		the_archive_title( sprintf( '<h1%1$s><span%2$s>', bayleaf_get_attr( 'page-header-title' ), bayleaf_get_attr( 'page-header-title-text' ) ), sprintf( '</span>%1$s</h1>', bayleaf_archive_description_toggle() ) );
		the_archive_description( sprintf( '<div%1$s>', bayleaf_get_attr( 'page-header-description' ) ), '</div>' );
		?>
	</header><!-- .page-header -->

	<?php
elseif ( is_search() ) :
	?>

	<header<?php bayleaf_attr( 'page-header' ); ?>>
		<h1<?php bayleaf_attr( 'page-header-title' ); ?>>
			<?php
			printf(
				/* translators: %s: Search term */
				esc_html__( 'Search Results for: %s', 'bayleaf' ),
				'<span>' . get_search_query() . '</span>'
			);
			?>
		</h1>
	</header><!-- .page-header -->

	<?php
endif;
