<?php
/**
 * Font Icon Loading Class.
 *
 * @since      1.0.0
 *
 * @package    Featured_Content
 * @subpackage Featured_Content/public
 */

namespace bayleaf;

/**
 * Load required font icons.
 *
 * @since 1.4.7
 */
class Icon_Loader {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.4.7
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Holds all required font icons.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $icons = [];

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_filter( 'wp_footer', [ self::get_instance(), 'add_icons' ], 9999 );
	}

	/**
	 * Adds a font icon to icons array.
	 *
	 * @since  1.0.0
	 *
	 * @param str $icon Icon to be added.
	 */
	public function add( $icon ) {
		if ( ! in_array( $icon, $this->icons, true ) ) {
			$this->icons[] = $icon;
		}
	}

	/**
	 * Adds a font icon to footer the web page.
	 *
	 * @since  1.0.0
	 */
	public function add_icons() {
		if ( empty( $this->icons ) ) {
			return;
		}

		$icons = '<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs>';

		require_once get_parent_theme_file_path( 'lib/icons/svg-fonts.php' );
		$icons_def = bayleaf_get_font_icons_def();
		foreach ( $this->icons as $icon ) {
			if ( isset( $icons_def[ $icon ] ) ) {
				$icons .= $icons_def[ $icon ];
			}
		}

		$icons .= '</defs></svg>';
		echo $icons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

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
}

Icon_Loader::init();
