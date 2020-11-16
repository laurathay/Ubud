<?php
/**
 * Making Bayleaf theme compatible with various plugins.
 *
 * Compatibility for : "The Events Calendar", "MainChimp for WordPress".
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Bayleaf theme's compatibility with various plugins.
 *
 * @since  1.0.0
 */
class Plugins_Compat {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Compatibility to The Events Calendar Plugin.
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			add_filter( 'bayleaf_markup_entry_index_wrapper', [ self::get_instance(), 'display_events' ] );
			add_filter( 'bayleaf_template_page_header', [ self::get_instance(), 'disable_page_header' ] );
			add_filter( 'bayleaf_template_entry_author', [ self::get_instance(), 'disable_entry_author' ] );
			add_filter( 'bayleaf_markup_entry_main_content', [ self::get_instance(), 'remove_header' ] );
			add_filter( 'bayleaf_display_posts_excerpt', [ self::get_instance(), 'modify_dp_excerpts' ], 10, 2 );
			add_filter( 'bayleaf_dp_style_args', [ self::get_instance(), 'modify_dp_items' ] );
			add_filter( 'bayleaf_dp_entry_classes', [ self::get_instance(), 'entry_classes' ], 12, 2 );
			add_filter( 'bayleaf_after_dp_widget_title', [ self::get_instance(), 'dp_wid_title' ], 10, 2 );
			add_action( 'bayleaf_display_dp_item', [ self::get_instance(), 'display_dp_item' ] );
		}

		// Compatibility to "MailChimp for WordPress" plugin.
		if ( defined( 'MC4WP_VERSION' ) || defined( 'MAILOPTIN_VERSION_NUMBER' ) ) {
			add_filter( 'bayleaf_widgetlayer_widget_options', [ self::get_instance(), 'widget_options' ] );
			add_filter( 'bayleaf_before_widget_content', [ self::get_instance(), 'display_widget_image' ], 10, 2 );
			add_filter( 'bayleaf_after_widget_content', [ self::get_instance(), 'widget_wrapper_close' ], 10, 2 );
		}

		// Compatibility to "MailChimp for WordPress" plugin.
		if ( defined( 'SIMPLIFIED_FONT_MANAGER_VERSION' ) ) {
			add_filter( 'simplified_font_manager_theme_options', [ self::get_instance(), 'font_options' ] );
			add_filter( 'bayleaf_fonts', '__return_empty_array' );
		}
	}

	/**
	 * Display all events on events archive pages.
	 *
	 * @param  array $callbacks Array of display functions.
	 * @return array Revised array of display functions.
	 */
	public function display_events( $callbacks ) {
		if ( is_post_type_archive( 'tribe_events' ) ) {
			$callbacks = [ [ 'bayleaf_get_template_partial', 'template-parts/post', 'entry-content' ] ];
		}

		return $callbacks;
	}

	/**
	 * Disable page header.
	 *
	 * @param  str $file Template file name.
	 * @return string
	 */
	public function disable_page_header( $file ) {
		if ( is_post_type_archive( 'tribe_events' ) ) {
			$file = '';
		}

		return $file;
	}

	/**
	 * Disable page header.
	 *
	 * @param  str $file Template file name.
	 * @return string
	 */
	public function disable_entry_author( $file ) {
		if ( is_singular( 'tribe_events' ) ) {
			$file = '';
		}

		return $file;
	}

	/**
	 * Remove entry header from 'tribe_events' post type.
	 *
	 * @param  array $functions Array of display functions.
	 * @return array Revised array of display functions.
	 */
	public function remove_header( $functions ) {
		if ( is_singular( [ 'tribe_events' ] ) ) {
			$key = array_search( 'bayleaf_entry_header_wrapper', $functions, true );
			if ( false !== $key ) {
				unset( $functions[ $key ] );
			}
		}
		return $functions;
	}

	/**
	 * Modify display posts excerpts to show event details.
	 *
	 * @param bool $return Returning true will short circuit original function.
	 * @param str  $style  Display posts style.
	 * @return bool
	 */
	public function modify_dp_excerpts( $return, $style ) {
		if ( 'tribe_events' === get_post_type() ) {
			bayleaf_get_template_partial( 'add-on/plugins-compat/templates', 'venue' );
			return true;
		}
		return $return;
	}

	/**
	 * Modify display posts items for 'tribe_events' post type.
	 *
	 * @param arr $d     Post items display instructions.
	 * @return bool
	 */
	public function modify_dp_items( $d ) {
		if ( 'tribe_events' === get_post_type() ) {
			$d['grid-view1'] = [ 'thumbnail-medium', [ 'title', 'event-time' ] ];
			$d['grid-view2'] = [ 'thumbnail-medium', [ 'title', 'event-time' ] ];
			$d['grid-view3'] = [ 'thumbnail-medium', [ 'title', 'event-time' ] ];
		}
		return $d;
	}

	/**
	 * Show display posts items for 'tribe_events' post type.
	 *
	 * @param str $item Post item to be displayed.
	 */
	public function display_dp_item( $item ) {
		switch ( $item ) {
			case 'event-time':
				echo tribe_events_event_schedule_details( null, '<div class="dp-categories"', '</div>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			default:
				break;
		}
	}

	/**
	 * Register widget display posts entry classes.
	 *
	 * @param str   $classes  Comma separated entry posts classes.
	 * @param array $instance Settings for the current widget instance.
	 * @return str Entry posts classes.
	 */
	public function entry_classes( $classes, $instance ) {
		if ( 'tribe_events' === get_post_type() ) {
			$classes[] = 'dp-event no-zig';
		}
		return $classes;
	}

	/**
	 * Add items to widget title area.
	 *
	 * @param array $after_title Items before closing of widget title.
	 * @param array $instance    Settings for the current widget instance.
	 * @return str
	 */
	public function dp_wid_title( $after_title, $instance ) {
		$link_html = '';

		// Change only if theme specific after_title args has not been altered.
		if ( '</span></h3>' !== $after_title ) {
			return $after_title;
		}

		if ( 'tribe_events' === $instance['post_type'] ) {
			$link_html = sprintf( '<span class="dp-term-links"><a class="term-link" href="%1$s">%2$s %3$s</a></span>', esc_url( tribe_get_events_link() ), esc_html__( 'Calendar', 'bayleaf' ), bayleaf_get_icon( array( 'icon' => 'long-arrow-right' ) ) );
		}

		return '</span>' . $link_html . '</h3>';
	}

	/**
	 * Get array of all widget options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Array of widget options.
	 */
	public function widget_options( $options ) {

		return array_merge(
			$options,
			[
				'bayleaf_widget_featured_image' => [
					'setting' => 'bayleaf_widget_featured_image',
					'label'   => esc_html__( 'Widget Featured Image', 'bayleaf' ),
					'type'    => 'image_upload',
					'id_base' => [ 'mc4wp_form_widget', 'mo_optin_widgets' ],
				],
			]
		);
	}

	/**
	 * Display featured image before mc4wp widget (if any).
	 *
	 * @since 1.0.0
	 *
	 * @param str   $markup Widget customized content markup.
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return string Widget customized content markup.
	 */
	public function display_widget_image( $markup, $widget_data ) {

		$instance = $widget_data[2];
		$id_base  = $widget_data[3];

		$widget_img = false;

		if ( 'mc4wp_form_widget' === $id_base && isset( $instance['bayleaf_widget_featured_image'] ) ) {
			$widget_img = true;
		}

		if ( 'mo_optin_widgets' === $id_base && isset( $instance['bayleaf_widget_featured_image'] ) ) {
			$widget_img = true;
		}

		// Generate markup for text widget featured image.
		if ( true === $widget_img ) {
			$image_id = absint( $instance['bayleaf_widget_featured_image'] );
			if ( $image_id ) {
				$classes    = [];
				$image_size = apply_filters( 'bayleaf_widget_image_size', 'bayleaf-large', $widget_data );
				$classes[]  = 'widget-bg-featured-image';
				$classes    = apply_filters( 'bayleaf_widget_image_classes', $classes, $widget_data );
				$img_markup = wp_get_attachment_image( $image_id, $image_size, false, [ 'class' => join( ' ', $classes ) ] );

				$markup = sprintf( '<div class="custom-widget-thumbnail"><div class="thumb-wrapper">%s</div></div><div class="custom-widget-content"><div class="custom-content-wrapper">', $img_markup );
			}
		}

		return $markup;
	}

	/**
	 * Markup after text widget (if any).
	 *
	 * @since 1.0.0
	 *
	 * @param str   $markup Widget customized content markup.
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type int   $widget_pos Widget position in widgetlayer widget-area.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return string Widget customized content markup.
	 */
	public function widget_wrapper_close( $markup, $widget_data ) {

		$instance = $widget_data[2];
		$id_base  = $widget_data[3];

		$widget_img = false;

		if ( 'mc4wp_form_widget' === $id_base && isset( $instance['bayleaf_widget_featured_image'] ) ) {
			$widget_img = true;
		}

		if ( 'mo_optin_widgets' === $id_base && isset( $instance['bayleaf_widget_featured_image'] ) ) {
			$widget_img = true;
		}

		// Generate markup for text widget featured image.
		if ( true === $widget_img ) {
			$image_id = absint( $instance['bayleaf_widget_featured_image'] );
			if ( $image_id ) {

				$markup = '</div></div>';
			}
		}

		return $markup;
	}

	/**
	 * Submit theme default options for simplified font manager.
	 *
	 * @return array Array of theme default font options.
	 */
	public function font_options() {
		return array(
			array(
				'family'    => array(
					'montserrat',
					'Montserrat',
					'goo-sans-serif',
					'Montserrat',
				),
				'weights'   => array( '500', '500italic', '600', '600italic' ),
				'selectors' => 'body',
			),
			array(
				'family'    => array(
					'poppins',
					'Poppins',
					'goo-serif',
					'Poppins',
				),
				'weights'   => array( '400', '600', '400italic', '600italic' ),
				'selectors' => 'h1,h2,h3,h4,h5,h6,.site-title',
			),
		);
	}
}
Plugins_Compat::init();
