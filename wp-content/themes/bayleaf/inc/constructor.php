<?php
/**
 * Display site contents
 *
 * Call appropriate functions to display various site contents.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/*
 * Register hooks for displaying site items.
 *
 * 'bayleaf_add_markup_for()' is a wrapper function for WordPress core 'add_action()' to make
 * it slightly more redable. Following is an example,
 * bayleaf_add_markup_for( 'what_to_display', 'where_to_display', priority (optional), args (optional) )
 *
 * 'what_to_display' should be a function ( theme slug will be added programatically ) i.e., bayleaf_what_to_display()
 * 'where_to_display' should be a theme action hook ( theme slug will be added programatically ) i.e., bayleaf_where_to_display
 *
 * Above function will register an action hook as follows,
 * add_action( 'bayleaf_where_to_display', 'bayleaf_what_to_display', priority (optional), args (optional) )
 */

// Display site header items.
bayleaf_add_markup_for( 'skip_link', 'inside_header', 0 );
bayleaf_add_markup_for( 'header_items', 'inside_header' );
bayleaf_add_markup_for( 'breadcrumbs', 'inside_header' );
bayleaf_add_markup_for( 'header_image_area', 'after_header' );
bayleaf_add_markup_for( 'home_above_content_area', 'after_header' );

// Display primary site content.
bayleaf_add_markup_for( 'blog_title', 'before_main_content' );
bayleaf_add_markup_for( 'page_header', 'inside_main_content' );
bayleaf_add_markup_for( 'page_entry_title', 'on_top_of_site_content' );
bayleaf_add_markup_for( 'page_entry_thumbnail', 'inside_main_content' );
bayleaf_add_markup_for( 'main_loop', 'inside_main_content' );
bayleaf_add_markup_for( 'hentry', 'inside_entry' );

// Display secondary site content.
bayleaf_add_markup_for( 'post_author', 'inside_entry' );
bayleaf_add_markup_for( 'image_navigation', 'inside_entry' );
bayleaf_add_markup_for( 'comments_toggle', 'before_comments' );
bayleaf_add_markup_for( 'post_navigation', 'inside_main_content' );
bayleaf_add_markup_for( 'post_pagination', 'after_main_content' );
bayleaf_add_markup_for( 'sidebar', 'inside_sidebar' );
bayleaf_add_markup_for( 'home_below_content_area', 'after_site_content' );

// Display site footer items.
bayleaf_add_markup_for( 'footer_widgets', 'inside_footer' );
bayleaf_add_markup_for( 'footer_items', 'inside_footer' );
bayleaf_add_markup_for( 'scroll_to_top', 'inside_footer' );

/*
 * Wrapper functions for displaying site items.
 *
 * WordPress core 'add_action()' does not allow to
 * 1. Pass variables to the called function (Predefined variables in do_action()
 * can only be passed).
 * 2. No provision for conditional check before calling a function.
 *
 * Therefore, we have to create wrapper functions. Also, these wrapper functions
 * make code more redable.
 */

/**
 * Conditionally display skip link.
 *
 * @since 1.0.0
 */
function bayleaf_skip_link() {

	printf(
		'<a class="skip-link screen-reader-text" href="#content">%s</a>',
		esc_html__( 'Skip to content', 'bayleaf' )
	);
}

/**
 * Header items wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_header_items() {
	bayleaf_markup(
		'header-items',
		[
			'bayleaf_site_branding',
			'bayleaf_main_navigation',
			'bayleaf_user_action_items',
		]
	);
}

/**
 * Custom header image markup.
 *
 * @since 1.0.0
 */
function bayleaf_header_image_area() {

	if ( ! is_home() && ! is_front_page() ) {
		return;
	}

	if ( is_singular() && has_post_thumbnail() ) {
		$img = [ 'the_post_thumbnail', 'bayleaf-featured' ];
	} elseif ( get_header_image() ) {
		$img = 'the_header_image_tag';
	} else {
		return;
	}

	echo '<div class="header-image-wrapper wrapper">';

	bayleaf_markup( 'header-image', [ $img ] );

	if ( bayleaf_get_mod( 'bayleaf_display_site_desc', 'none' ) ) {
		bayleaf_get_template_partial( 'template-parts/header', 'site-desc' );
	}

	echo '</div>';
}

/**
 * Site branding wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_site_branding() {
	$site_branding = [ 'the_custom_logo' ];

	if ( bayleaf_get_mod( 'bayleaf_display_site_title', 'none' ) ) {
		array_push(
			$site_branding,
			[ 'bayleaf_get_template_partial', 'template-parts/header', 'site-title' ]
		);
	}

	bayleaf_markup( 'site-branding', $site_branding );
}

/**
 * Main navigation markup.
 *
 * @since 1.0.0
 */
function bayleaf_main_navigation() {
	if ( ! bayleaf_get_mod( 'bayleaf_primary_nav', 'none' ) ) {
		return;
	}

	printf(
		'<button aria-expanded="false" class="menu-toggle"><span class="bar"><span class="screen-reader-text">%s</span></span></button>',
		esc_html__( 'Site Navigation', 'bayleaf' )
	);

	bayleaf_nav_menu(
		'site-navigation',
		esc_html__( 'Site Navigation', 'bayleaf' ),
		[
			'menu_id'         => 'primary-menu',
			'menu_class'      => 'nav-menu nav-menu--primary',
			'container'       => 'div',
			'container_id'    => 'menu-container',
			'container_class' => 'menu-container',
			'theme_location'  => 'primary',
		]
	);
}

/**
 * Sidebar widgets wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_user_action_items() {
	bayleaf_markup(
		'header-widgets',
		[
			'bayleaf_header_search',
			'bayleaf_header_widgets',
		]
	);
}

/**
 * Sidebar widgets wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_header_search() {
	if ( ! bayleaf_get_mod( 'bayleaf_header_search', 'none' ) ) {
		return;
	}

	printf(
		'<button class="search-toggle"><span class="screen-reader-text">%s</span>%s%s</button>',
		esc_html__( 'Show secondary sidebar', 'bayleaf' ),
		bayleaf_get_icon( [ 'icon' => 'search' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		bayleaf_get_icon( [ 'icon' => 'close' ] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	echo '<div id="header-search-wrapper" class="header-search-wrapper">';
	echo '<div class="header-search-container">';
	get_search_form();
	echo '</div></div>';
}

/**
 * Sidebar widgets wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_header_widgets() {
	if ( ! is_active_sidebar( 'sidebar' ) ) {
		return;
	}
	printf(
		'<button class="action-toggle"><span class="bar"><span class="screen-reader-text">%s</span></span></button>',
		esc_html__( 'Show secondary sidebar', 'bayleaf' )
	);
	echo '<div id="header-widget-area" class="header-widget-area">';
	bayleaf_widgets(
		'header-widget-wrapper',
		'header-widget-wrapper',
		esc_html__( 'Header Widget Wrapper', 'bayleaf' ),
		'sidebar'
	);
	echo '</div>';
}

/**
 * Display sidebar widgets.
 *
 * @since 1.0.0
 */
function bayleaf_sidebar() {
	if ( is_singular( [ 'post', 'page' ] ) ) {

		if ( is_page_template( 'page-templates/full-width.php' ) ) {
			return;
		}

		if ( is_single() && 'no-sidebar' === bayleaf_get_mod( 'bayleaf_post_sidebar', 'none' ) ) {
			return;
		}

		if ( is_page() && 'no-sidebar' === bayleaf_get_mod( 'bayleaf_page_sidebar', 'none' ) ) {
			return;
		}

		bayleaf_widgets(
			'sidebar-widget-area',
			'sidebar-widget-area',
			esc_html__( 'Sidebar Widget Area', 'bayleaf' ),
			'sidebar-1'
		);
	}
}

/**
 * Social navigation markup.
 *
 * @since 1.0.0
 */
function bayleaf_social_menu() {

	if ( ! has_nav_menu( 'social' ) ) {
		return;
	}

	bayleaf_nav_menu(
		'social-navigation',
		esc_html__( 'Social Navigation', 'bayleaf' ),
		[
			'menu_id'        => 'social-menu',
			'menu_class'     => 'nav-menu nav-menu--social',
			'theme_location' => 'social',
			'depth'          => 1,
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>' . bayleaf_get_icon( [ 'icon' => 'chain' ] ),
		]
	);
}

/**
 * Display Homepage widgets 1 (widget area).
 *
 * @since 1.0.0
 */
function bayleaf_home_above_content_area() {
	if ( ! is_front_page() || is_paged() ) {
		return;
	}

	bayleaf_widgets(
		'home-content-area-above',
		'home-content-area-above widgetlayer flex-wrapper',
		esc_html__( 'Homepage Above Content Widget Area', 'bayleaf' ),
		'home-widgetlayer-1'
	);
}

/**
 * Display Homepage widgets 2 (widget area).
 *
 * @since 1.0.0
 */
function bayleaf_home_below_content_area() {
	if ( ! is_front_page() || is_paged() ) {
		return;
	}

	bayleaf_widgets(
		'home-content-area-below',
		'home-content-area-below widgetlayer flex-wrapper',
		esc_html__( 'Homepage Below Content Widget Area', 'bayleaf' ),
		'home-widgetlayer-2'
	);
}

/**
 * Page Entry header wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_page_entry_title() {

	if ( is_front_page() ) {
		return;
	}

	if ( is_singular( [ 'post', 'page' ] ) ) {

		if ( is_page_template( 'page-templates/full-width.php' ) ) {
			return;
		}

		bayleaf_markup( 'page-entry-header-main-title', [ 'bayleaf_page_entry_header_items' ] );
	}
}

/**
 * Page Entry header wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_page_entry_thumbnail() {

	if ( is_front_page() ) {
		return;
	}

	if ( ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular( [ 'post', 'page' ] ) ) {

		if ( ! bayleaf_get_mod( 'bayleaf_show_featured', 'none' ) ) {
			return;
		}

		if ( is_page_template( 'page-templates/full-width.php' ) ) {
			return;
		}

		bayleaf_markup(
			'page-entry-header',
			[
				[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-thumbnail' ],
			]
		);
	}
}

/**
 * Page Entry header items markup.
 *
 * @since 1.0.0
 */
function bayleaf_page_entry_header_items() {

	$page_entry_header_items = [
		'bayleaf_entry_meta_wrapper',
		[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-title' ],
	];

	if ( has_excerpt() ) {
		$page_entry_header_items[] = [ 'bayleaf_markup', 'single-excerpt', [ 'the_excerpt' ] ];
	}

	bayleaf_markup( 'page-entry-header-items', $page_entry_header_items );
}

/**
 * Include page header display template.
 *
 * @since 1.0.0
 */
function bayleaf_page_header() {
	bayleaf_get_template_partial( 'template-parts/page', 'page-header' );
}

/**
 * Breadcrumbs display support.
 *
 * @since 1.0.0
 */
function bayleaf_breadcrumbs() {
	if ( ! is_singular( [ 'post', 'page' ] ) && ! is_archive() ) {
		return;
	}

	if ( function_exists( 'bcn_display' ) ) {
		echo '<div id="bayleaf-breadcrumbs" class="wrapper">';
		bcn_display();
		echo '</div>';
		return;
	}

	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div id="bayleaf-breadcrumbs" class="wrapper">', '</div>' );
		return;
	}

	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		echo '<div id="bayleaf-breadcrumbs" class="wrapper">';
		rank_math_the_breadcrumbs();
		echo '</div>';
	}
}

/**
 * Include blog title display template.
 *
 * @since 1.0.0
 */
function bayleaf_blog_title() {
	if ( is_home() ) {
		$blog_title = bayleaf_get_mod( 'bayleaf_blog_title', 'html' );
		if ( ! $blog_title ) {
			return;
		}
		$blog_title = explode( '-', $blog_title );
		?>
		<div class="title-wrapper wrapper">
			<h2 class="blog-title">
				<?php
				if ( isset( $blog_title[0] ) ) {
					printf( '<span class="bt-1">%s</span>', esc_html( trim( $blog_title[0] ) ) );
				}
				if ( isset( $blog_title[1] ) ) {
					printf( '<span class="bt-2">%s</span>', esc_html( trim( $blog_title[1] ) ) );
				}
				?>
			</h2>
		</div>
		<?php
	}
}

/**
 * Include main loop execution template.
 *
 * @since 1.0.0
 */
function bayleaf_main_loop() {
	bayleaf_get_template_partial( 'template-parts/loop', 'main-loop' );
}

/**
 * Include comment toggle template.
 *
 * @since 1.0.0
 */
function bayleaf_comments_toggle() {
	$text = '';
	if ( comments_open() ) {
		if ( have_comments() ) {
			$text = esc_html__( 'Join the Conversation', 'bayleaf' );
		} else {
			$text = esc_html__( 'Leave a comment', 'bayleaf' );
		}
	} else {
		if ( have_comments() ) {
			$text = esc_html__( 'Show comments', 'bayleaf' );
		}
	}

	if ( $text ) {
		$toggle_text = sprintf(
			'<span class="toggle-text">%s</span>',
			$text // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		printf(
			'<button class="comments-toggle">%1$s%2$s%3$s</button>',
			$toggle_text, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			bayleaf_get_icon( [ 'icon' => 'angle-down' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			bayleaf_get_icon( [ 'icon' => 'angle-up' ] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
}

/**
 * Entry main content wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_main_content() {
	bayleaf_markup(
		'entry-main-content',
		[
			'bayleaf_entry_header_wrapper',
			'bayleaf_entry_content_wrapper',
			'bayleaf_entry_footer_wrapper',
		]
	);
}

/**
 * Entry header wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_header_wrapper() {
	if ( ! is_singular() ) {

		bayleaf_markup(
			'entry-header',
			[
				[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-categories' ],
				[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-title' ],
			]
		);
	} elseif ( ! is_singular( [ 'post', 'page' ] ) ) {
		bayleaf_entry_title_area();
	}
}

/**
 * Entry header title area wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_title_area() {
	bayleaf_markup(
		'entry-header-title-area',
		[
			'bayleaf_page_entry_header_items',
			[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-thumbnail' ],
		]
	);
}

/**
 * Conditionally display entry meta wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_meta_wrapper() {

	if ( ! in_array( get_post_type(), [ 'page', 'attachment' ], true ) ) {
		bayleaf_markup( 'entry-meta', [ 'bayleaf_entry_meta' ] );
	}
}

/**
 * Include entry meta display template(s).
 *
 * @since 1.0.0
 */
function bayleaf_entry_meta() {
	bayleaf_get_template_partial( 'template-parts/meta', 'meta-author' );
	printf( '<span class="meta-sep">%s</span>', esc_html__( 'on', 'bayleaf' ) );
	bayleaf_get_template_partial( 'template-parts/meta', 'meta-date' );
}

/**
 * Create featured content markup on index pages.
 *
 * @since 1.0.0
 */
function bayleaf_hentry() {
	if ( is_singular() ) {
		bayleaf_entry_main_content();
	} else {
		bayleaf_markup(
			'entry-index-wrapper',
			[
				'bayleaf_entry_featured_content',
				'bayleaf_entry_main_content',
			]
		);
	}
}

/**
 * Create featured content markup on index pages.
 *
 * @since 1.0.0
 */
function bayleaf_entry_featured_content() {
	if ( ! is_singular() && ( bayleaf_get_mod( 'bayleaf_thumbnail_placeholder', 'none' ) || has_post_thumbnail() ) ) {
		bayleaf_markup(
			'entry-featured-content',
			[
				[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-thumbnail' ],
				[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-permalink' ],
				[ 'bayleaf_sticky_post_icon' ],
			]
		);
	}
}

/**
 * Sticky Post Icon.
 *
 * @since 1.0.0
 */
function bayleaf_sticky_post_icon() {
	if ( is_sticky() ) {
		bayleaf_icon( [ 'icon' => 'sticky' ] );
	}
}

/**
 * Entry content wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_content_wrapper() {
	if ( is_attachment() && wp_attachment_is_image() ) {
		bayleaf_get_template_partial( 'template-parts/post', 'entry-attachment' );
		return;
	}

	if ( is_singular() ) {
		bayleaf_markup(
			'entry-content',
			[
				[ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-content' ],
			]
		);
	}
}

/**
 * Entry footer wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_entry_footer_wrapper() {
	if ( ! is_singular() ) {
		return;
	}

	$type = get_post_type();
	$meta = [];

	if ( 'post' === $type ) {
		$meta = [
			[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-categories' ],
			[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-tags' ],
		];
	} elseif ( 'attachment' === $type ) {
		$meta = [
			[ 'bayleaf_get_template_partial', 'template-parts/meta', 'meta-attachment' ],
		];
	}

	if ( $meta ) {
		bayleaf_markup( 'entry-footer', $meta );
	}
}

/**
 * Conditionally include post author display template.
 *
 * @since 1.0.0
 */
function bayleaf_post_author() {
	global $post;

	// No need to display author box on image attachment pages.
	if ( is_attachment() && wp_attachment_is_image() ) {
		return;
	}

	// Display author box on single posts, if author description is available.
	if ( ! is_single() || '' === get_the_author_meta( 'description', $post->post_author ) ) {
		return;
	}

	bayleaf_get_template_partial( 'template-parts/post', 'entry-author' );
}

/**
 * Display post pagination on home, archive and search pages.
 *
 * @since 1.0.0
 */
function bayleaf_post_pagination() {
	if ( is_singular() ) {
		return;
	}

	the_posts_pagination(
		[
			'mid_size'           => 2,
			'prev_text'          => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'bayleaf' ) . '</span>' . bayleaf_get_icon( array( 'icon' => 'angle-left' ) ) . '<span class="post-pagi">' . esc_html__( 'Previous', 'bayleaf' ) . '</span>',
			'next_text'          => '<span class="screen-reader-text show-on-mobile">' . esc_html__( 'Next', 'bayleaf' ) . '</span><span class="post-pagi">' . esc_html__( 'Next', 'bayleaf' ) . '</span>' . bayleaf_get_icon( array( 'icon' => 'angle-right' ) ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'bayleaf' ) . ' </span>',
		]
	);
}

/**
 * Display post navigation on single posts.
 *
 * @since 1.0.0
 */
function bayleaf_post_navigation() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	the_post_navigation(
		[
			'next_text' => '<span class="nav-text">' . esc_html__( 'Next Post ', 'bayleaf' ) . '</span><span class="post-title screen-reader-text">%title</span>',
			'prev_text' => '<span class="nav-text">' . esc_html__( ' Previous Post', 'bayleaf' ) . '</span><span class="post-title screen-reader-text">%title</span>',
		]
	);
}

/**
 * Display image navigation on image attachment pages.
 *
 * @since 1.0.0
 */
function bayleaf_image_navigation() {
	if ( ! ( is_attachment() && wp_attachment_is_image() ) ) {
		return;
	}
	?>
	<nav id="image-navigation" class="navigation image-navigation">
		<div class="nav-links">
			<div class="nav-previous"><?php previous_image_link( false, esc_html__( 'Previous Image', 'bayleaf' ) ); ?></div>
			<div class="nav-next"><?php next_image_link( false, esc_html__( 'Next Image', 'bayleaf' ) ); ?></div>
		</div><!-- .nav-links -->
	</nav><!-- .image-navigation -->
	<?php
}

/**
 * Display footer widgets.
 *
 * @since 1.0.0
 */
function bayleaf_footer_widgets() {
	bayleaf_widgets(
		'footer-widget-area',
		'footer-widget-area widgetlayer flex-wrapper',
		esc_html__( 'Footer Widget Area', 'bayleaf' ),
		'home-widgetlayer-3'
	);
}

/**
 * Site footer items wrapper markup.
 *
 * @since 1.0.0
 */
function bayleaf_footer_items() {
	bayleaf_markup(
		'footer-items',
		[
			'bayleaf_footer_text',
			'bayleaf_social_menu',
		]
	);
}

/**
 * Scroll to top button markup.
 *
 * @since 1.0.0
 */
function bayleaf_scroll_to_top() {
	printf(
		'<button class="scrl-to-top">%1$s<span class="screen-reader-text">%2$s</span></button>',
		bayleaf_get_icon( [ 'icon' => 'arrow-up' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Scroll to top of the page', 'bayleaf' )
	);
}

/**
 * Display footer text.
 *
 * Escape footer text and replace year, title and symbol placeholders with
 * proper markup.
 *
 * @since 1.0.0
 * @return void
 */
function bayleaf_footer_text() {
	// Note: Footer text is escaped via `bayleaf_escape()`.
	$footer_text = bayleaf_get_mod( 'bayleaf_footer_text', 'html' );
	if ( '' === $footer_text ) {
		return;
	}

	$output = str_replace( '[current_year]', esc_html( date_i18n( __( 'Y', 'bayleaf' ) ) ), $footer_text );
	$output = str_replace( '[site_title]', get_bloginfo( 'name', 'display' ), $output );
	$output = str_replace( '[copy_symbol]', '&copy;', $output );

	printf( '<div class="footer-text">%1$s</div>', $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
