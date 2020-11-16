<?php
/**
 * Hook into WordPress to enhance default contents
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Add dropdown icon if primary menu item has children.
 *
 * @param  string $title The menu item's title.
 * @param  object $item  The current menu item.
 * @param  array  $args  An array of wp_nav_menu() arguments.
 * @param  int    $depth Depth of menu item. Used for padding.
 * @return string $title The menu item's title with dropdown icon.
 */
function bayleaf_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
	if ( 'primary' === $args->theme_location ) {
		foreach ( $item->classes as $value ) {
			if ( 'menu-item-has-children' === $value || 'page_item_has_children' === $value ) {
				$title = $title . bayleaf_get_icon( array( 'icon' => 'angle-down' ) );
			}
		}
	}

	return $title;
}
add_filter( 'nav_menu_item_title', 'bayleaf_dropdown_icon_to_menu_link', 10, 4 );

/**
 * Add dropdown icon if primary menu item has children.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string  $item_output The menu item output with social icon.
 */
function bayleaf_dropdown_btn_to_menu_item( $item_output, $item, $depth, $args ) {
	$button = sprintf(
		'<button aria-expanded="false" class="sub-menu-toggle"><span class="screen-reader-text">%1$s</span>%2$s%3$s</button>',
		esc_html__( 'Submenu Toggle', 'bayleaf' ),
		bayleaf_get_icon( [ 'icon' => 'angle-down' ] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		bayleaf_get_icon( [ 'icon' => 'angle-up' ] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);

	if ( 'primary' === $args->theme_location ) {
		foreach ( $item->classes as $value ) {
			if ( 'menu-item-has-children' === $value || 'page_item_has_children' === $value ) {
				$item_output .= $button;
			}
		}
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'bayleaf_dropdown_btn_to_menu_item', 10, 4 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link and add
 * some custom markup to match style with wp_nav_menu().
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function bayleaf_page_menu_args( $args ) {
	$args['show_home'] = true;
	if ( isset( $args['fallback_cb'] ) && 'wp_page_menu' === $args['fallback_cb'] ) {
		$args['theme_location'] = 'primary';
		$args['menu_id']        = 'menu-container';
		$args['menu_class']     = 'menu-container';
		$args['before']         = '<ul id="primary-menu" class="nav-menu nav-menu--primary">';
		unset( $args['fallback_cb'] );
	}
	return $args;
}
add_filter( 'wp_page_menu_args', 'bayleaf_page_menu_args' );

/**
 * Check for social menu in class-wp-nav-menu-widget.
 *
 * Check for social menu in widget and return appropriate social nav menu args.
 *
 * @since 1.0.0
 *
 * @param array    $nav_menu_args {
 *     An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
 *
 *     @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
 *     @type mixed         $menu        Menu ID, slug, or name.
 * }
 * @param stdClass $nav_menu      Nav menu object for the current menu.
 * @param array    $args          Display arguments for the current widget.
 * @param array    $instance      Array of settings for the current widget.
 * @return array $nav_menu_args.
 */
function bayleaf_social_menu_widget( $nav_menu_args, $nav_menu, $args, $instance ) {

	$menu_args = get_nav_menu_locations();

	if ( isset( $menu_args['social'] ) && $menu_args['social'] === $instance['nav_menu'] ) {

		$nav_menu_args = array(
			'menu_id'        => 'social-menu',
			'menu_class'     => 'nav-menu nav-menu--social',
			'theme_location' => 'social',
			'depth'          => 1,
			'link_before'    => '<span class="screen-reader-text">',
			'link_after'     => '</span>' . bayleaf_get_icon( [ 'icon' => 'chain' ] ),
			'items_wrap'     => '<nav id="social-navigation" aria-label="' . esc_attr__( 'Social Navigation', 'bayleaf' ) . '"' . bayleaf_get_attr( 'social-navigation' ) . '><ul id="%1$s" class="%2$s">%3$s</ul></nav>',
		);

	}

	return $nav_menu_args;

}
add_filter( 'widget_nav_menu_args', 'bayleaf_social_menu_widget', 10, 4 );

/**
 * Display SVG icons in social links menu.
 *
 * This function incorporates code from Twenty Seventeen WordPress Theme,
 * Copyright 2016-2017 WordPress.org. Twenty Seventeen is distributed
 * under the terms of the GNU GPL.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string  $item_output The menu item output with social icon.
 */
function bayleaf_nav_menu_social_icons( $item_output, $item, $depth, $args ) {

	/**
	 * Filter social links icons.
	 *
	 * @since 1.0.0
	 *
	 * @param array $social_links_icons Array of social links icons.
	 */
	$social_icons = apply_filters(
		'bayleaf_social_links_icons',
		[
			'behance.net'    => 'behance',
			'codepen.io'     => 'codepen',
			'deviantart.com' => 'deviantart',
			'digg.com'       => 'digg',
			'docker.com'     => 'dockerhub',
			'dribbble.com'   => 'dribbble',
			'facebook.com'   => 'facebook',
			'flickr.com'     => 'flickr',
			'github.com'     => 'github',
			'instagram.com'  => 'instagram',
			'linkedin.com'   => 'linkedin',
			'mailto:'        => 'email',
			'medium.com'     => 'medium',
			'pinterest.com'  => 'pinterest',
			'reddit.com'     => 'reddit',
			'snapchat.com'   => 'snapchat',
			'soundcloud.com' => 'soundcloud',
			'spotify.com'    => 'spotify',
			'tumblr.com'     => 'tumblr',
			'twitch.tv'      => 'twitch',
			'twitter.com'    => 'twitter',
			'vimeo.com'      => 'vimeo',
			'vk.com'         => 'vk',
			'wordpress.org'  => 'wordpress',
			'wordpress.com'  => 'wordpress',
			'yelp.com'       => 'yelp',
			'youtube.com'    => 'youtube',
		]
	);

	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'social' === $args->theme_location ) {
		foreach ( $social_icons as $attr => $value ) {
			if ( false !== strpos( $item_output, $attr ) ) {
				$item_output = str_replace( $args->link_after, '</span>' . bayleaf_get_icon( [ 'icon' => esc_attr( $value ) ] ), $item_output );
			}
		}
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'bayleaf_nav_menu_social_icons', 10, 4 );

/**
 * Add dynamic styles to TinyMCE classic editor.
 *
 * @since 1.0.0
 *
 * @param array $mceinit An array with TinyMCE config.
 * @return array
 */
function bayleaf_dynamic_editor_styles( $mceinit ) {
	/*
	 * Although, Filter 'tiny_mce_before_init' is available in Gutenberg editor screen as well
	 * ( in lib/client-assets.php ). However, it should not be relied for adding inline styles
	 * to Gutenberg edit screen. It creates unnessary confusion for using css selectors.
	 */
	if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
		return $mceinit;
	}

	$styles = apply_filters( 'bayleaf_dynamic_classic_editor_styles', '' );
	if ( ! $styles ) {
		return $mceinit;
	}

	$styles = bayleaf_prepare_css( $styles );

	if ( isset( $mceinit['content_style'] ) ) {
		$mceinit['content_style'] .= ' ' . $styles . ' ';
	} else {
		$mceinit['content_style'] = $styles . ' ';
	}
	return $mceinit;
}
add_filter( 'tiny_mce_before_init', 'bayleaf_dynamic_editor_styles' );
