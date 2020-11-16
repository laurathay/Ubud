<?php
/**
 * Bayleaf theme functions and definitions
 *
 * This file defines content width, add theme support for various WordPress
 * features, load required stylesheets and scripts, register menus and widget
 * areas and load other required files to extend theme functionality.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Bayleaf
 * @since 1.0.0
 */

/**
 * Bayleaf only works with PHP 5.6 or later.
 */
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	require get_parent_theme_file_path( '/lib/php-backcompat.php' );
	return;
}

/**
 * Bayleaf only works with WordPress 4.9 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.9', '<' ) ) {
	require get_parent_theme_file_path( '/lib/wp-backcompat.php' );
	return;
}

/**
 * Save theme version as a constant.
 * To be used to increase version of theme scripts & styles. (for Browser Cache-Busting).
 */
define( 'BAYLEAF_THEME_VERSION', esc_html( wp_get_theme( get_template() )->get( 'Version' ) ) );

/**
 * Register theme features.
 *
 * Set up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 */
function bayleaf_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'bayleaf', get_template_directory() . '/lang' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'bayleaf-small', 320, 9999, false );
	add_image_size( 'bayleaf-smlmed', 450, 9999, false );
	add_image_size( 'bayleaf-medium', 720, 9999, false );
	add_image_size( 'bayleaf-large', 1280, 9999, false );

	// Set the default content width.
	$GLOBALS['content_width'] = 1280;

	// Allows the use of valid HTML5 markup.
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Enable support for Post Formats.
	add_theme_support(
		'post-formats',
		array(
			'image',
			'video',
			'quote',
			'gallery',
			'audio',
		)
	);

	/**
	 * Filter navigation menu locations.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $locations Associative array of menu location identifiers (like a slug) and descriptive text.
	 */
	$locations = apply_filters(
		'bayleaf_nav_menus',
		array(
			'primary' => esc_html__( 'Primary Menu', 'bayleaf' ),
			'social'  => esc_html__( 'Social Menu', 'bayleaf' ),
		)
	);

	// Register nav menu locations.
	register_nav_menus( $locations );

	/**
	 * Filter custom background args.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $bg_args Array of extra arguments for custom background.
	 */
	$bg_args = apply_filters(
		'bayleaf_custom_bg_args',
		array(
			'default-image'          => '',
			'default-preset'         => 'default',
			'default-position-x'     => 'left',
			'default-position-y'     => 'top',
			'default-size'           => 'auto',
			'default-repeat'         => 'repeat',
			'default-attachment'     => 'scroll',
			'default-color'          => 'fff',
			'wp-head-callback'       => '_custom_background_cb',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		)
	);

	/*
	 * This theme designed to work with plain white background. Therefore, custom backgrounds
	 * are  not supported. However, You can use following line of code to set up the WordPress
	 * core custom background feature.
	 * add_theme_support( 'custom-background', $bg_args );
	 */

	/**
	 * Filter custom logo args.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $logo_args Array of extra arguments for custom logo.
	 */
	$logo_args = apply_filters(
		'bayleaf_custom_logo_args',
		array(
			'width'       => 120,
			'height'      => 120,
			'flex-width'  => true,
			'flex-height' => false,
			'header_text' => '',
		)
	);
	// Set up the WordPress core custom logo feature.
	add_theme_support( 'custom-logo', $logo_args );

	/**
	 * Filter custom header args.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $header_args Array of extra arguments for custom header.
	 */
	$header_args = apply_filters(
		'bayleaf_custom_header_args',
		array(
			'default-image'          => '',
			'random-default'         => false,
			'width'                  => 1280,
			'height'                 => 500,
			'flex-width'             => false,
			'flex-height'            => false,
			'default_text_color'     => '',
			'header-text'            => false,
			'uploads'                => true,
			'wp-head-callback'       => '',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
			'video'                  => false,
			'video-active-callback'  => 'is_front_page',
		)
	);

	add_theme_support( 'custom-header', $header_args );

	// Load core files. These files provide basic functionality to this theme.
	require_once get_parent_theme_file_path( 'lib/icons/class-icon-loader.php' );
	require_once get_parent_theme_file_path( 'lib/multiuse-functions.php' );
	require_once get_parent_theme_file_path( 'lib/default-filters.php' );
	require_once get_parent_theme_file_path( 'lib/customizer/customize-frontend.php' );

	// Load files to build and customize website's front-end.
	require_once get_parent_theme_file_path( 'inc/controller.php' );
	require_once get_parent_theme_file_path( 'inc/modifier.php' );
	require_once get_parent_theme_file_path( 'inc/constructor.php' );

	/**
	 * Filter list of activated custom addon features for this theme.
	 *
	 * @since 1.0.0
	 *
	 * @param arrray $addons Array of addon features for this theme.
	 */
	$addons = apply_filters(
		'bayleaf_theme_support',
		array(
			'widgetlayer',
			'display-posts',
			'gutenberg',
			'woocommerce',
			'plugins-compat',
			'related-posts',
		)
	);
	foreach ( $addons as $addon ) {
		$file_path = "add-on/{$addon}/class-{$addon}.php";

		/**
		 * Filter add-on file path.
		 *
		 * @since 1.0.0
		 *
		 * @param string $file_path File path for the addon.
		 * @param string $addon     The Addon.
		 */
		$file_path = apply_filters( 'bayleaf_theme_support_file_path', $file_path, $addon );
		include_once get_parent_theme_file_path( $file_path );
	}

	// Add custom styles for visual editor to resemble the theme style.
	add_editor_style( array( 'assets/admin/css/editor-style.css', esc_url( bayleaf_font_url() ) ) );

	// Load theme customizer initiation file at last.
	require_once get_parent_theme_file_path( 'lib/customizer/customize-register.php' );
}
add_action( 'after_setup_theme', 'bayleaf_setup', 5 );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * @since 1.0.0
 *
 * @global int $content_width
 */
function bayleaf_content_width() {

	$content_width = $GLOBALS['content_width'];

	/**
	 * Filter content width of the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param $content_width integer
	 */
	$GLOBALS['content_width'] = apply_filters( 'bayleaf_content_width', $content_width );
}
add_action( 'template_redirect', 'bayleaf_content_width', 0 );

/**
 * Register widget area.
 *
 * @since 1.0.0
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bayleaf_widgets_init() {

	/**
	 * Filter register widget area args.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widgets {
	 *     Array of arguments for the sidebar being registered.
	 *
	 *     @type string $name          The name or title of the sidebar.
	 *     @type string $id            The unique identifier by which the sidebar will be called.
	 *     @type string $description   Description of the sidebar.
	 * }
	 */
	$widgets = apply_filters(
		'bayleaf_register_sidebar',
		array(
			array(
				'name' => esc_html__( 'Header Toggle Widgets', 'bayleaf' ),
				'id'   => 'sidebar',
			),
			array(
				'name' => esc_html__( 'Sidebar Widgets', 'bayleaf' ),
				'id'   => 'sidebar-1',
			),
			array(
				'name'          => esc_html__( 'Homepage Above Content', 'bayleaf' ),
				'id'            => 'home-widgetlayer-1',
				'before_widget' => '<section id="%1$s" class="widget brick %2$s">',
			),
			array(
				'name'          => esc_html__( 'Homepage Below Content', 'bayleaf' ),
				'id'            => 'home-widgetlayer-2',
				'before_widget' => '<section id="%1$s" class="widget brick %2$s">',
			),
			array(
				'name'          => esc_html__( 'Footer Widgets', 'bayleaf' ),
				'id'            => 'home-widgetlayer-3',
				'before_widget' => '<section id="%1$s" class="widget brick %2$s">',
			),
		)
	);

	$defaults = array(
		'description'   => esc_html__( 'Add widgets here.', 'bayleaf' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	);

	foreach ( $widgets as $widget ) {
		register_sidebar( wp_parse_args( $widget, $defaults ) );
	}
}
add_action( 'widgets_init', 'bayleaf_widgets_init' );

/**
 * Get Google fonts url to register and enqueue.
 *
 * This function incorporates code from Twenty Fifteen WordPress Theme,
 * Copyright 2014-2016 WordPress.org & Automattic.com Twenty Fifteen is
 * distributed under the terms of the GNU GPL.
 *
 * @since 1.0.0
 *
 * @return string Google fonts URL for the theme.
 */
function bayleaf_font_url() {

	$fonts     = array();
	$fonts_url = '';
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Montserrat, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'bayleaf' ) ) {
		$fonts[] = 'Montserrat:500,600,500italic,600italic';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Poppins, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'bayleaf' ) ) {
		$fonts[] = 'Poppins:400,600,400italic,600italic';
	}

	/**
	 * Filter google fonts to be used for the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fonts Array of google fonts to be used for the theme.
	 */
	$fonts = apply_filters( 'bayleaf_fonts', $fonts );

	if ( $fonts ) {
		$fonts_url = add_query_arg(
			array(
				'family' => rawurlencode( implode( '|', $fonts ) ),
				'subset' => rawurlencode( $subsets ),
			),
			'https://fonts.googleapis.com/css'
		);
	}

	/**
	 * Filter google font url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $fonts_url Google fonts url.
	 */
	return apply_filters( 'bayleaf_font_url', esc_url_raw( $fonts_url ) );
}

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * This function incorporates code from Twenty Seventeen WordPress Theme,
 * Copyright 2016-2017 WordPress.org. Twenty Seventeen is distributed
 * under the terms of the GNU GPL.
 *
 * @since 1.0.0
 */
function bayleaf_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'bayleaf_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function bayleaf_scripts() {
	// Theme stylesheet.
	wp_enqueue_style(
		'bayleaf-style',
		get_stylesheet_uri(),
		array(),
		BAYLEAF_THEME_VERSION
	);
	wp_style_add_data( 'bayleaf-style', 'rtl', 'replace' );
	wp_add_inline_style( 'bayleaf-style', bayleaf_get_inline_css() );

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style(
		'bayleaf-fonts',
		esc_url( bayleaf_font_url() ),
		array(),
		BAYLEAF_THEME_VERSION
	);

	// Theme localize scripts data.
	$l10n = apply_filters(
		'bayleaf_localize_script_data',
		array(
			'menu' => 'primary-menu', // ID of nav-menu first UL element.
		)
	);

	// Theme scripts.
	wp_enqueue_script(
		'bayleaf-scripts',
		get_theme_file_uri( '/scripts.js' ),
		array(),
		BAYLEAF_THEME_VERSION,
		true
	);
	wp_localize_script( 'bayleaf-scripts', 'bayleafScreenReaderText', $l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bayleaf_scripts' );

/**
 * Add preconnect for Google Fonts.
 *
 * This function incorporates code from Twenty Seventeen WordPress Theme,
 * Copyright 2016-2017 WordPress.org. Twenty Seventeen is distributed
 * under the terms of the GNU GPL.
 *
 * @since 1.0.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function bayleaf_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'bayleaf-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bayleaf_resource_hints', 10, 2 );

/*
 * Note: Do not add any custom code here. Please use a custom plugin or child theme, so that your
 * customizations aren't lost during theme updates.
 */
