<?php
/**
 * Recommend plugins to be used with this theme.
 *
 * @package Bayleaf
 * @since 1.0.0
 */

namespace bayleaf;

/**
 * Recommend plugins to be used with this theme.
 *
 * @since  1.0.1
 */
class TGM_Activation {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.1
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.1
	 */
	public function __construct() {
		require_once get_template_directory() . '/add-on/tgm-activation/class-tgm-plugin-activation.php';
	}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.1
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
	 * @since 1.0.1
	 */
	public static function init() {
		add_action( 'tgmpa_register', [ self::get_instance(), 'register_recommended_plugins' ] );
	}

	/**
	 * Register recommended plugins.
	 *
	 * @since 1.0.0
	 */
	public static function register_recommended_plugins() {
		$plugins = [
			[
				'name'     => 'Simplified Font Manager',
				'slug'     => 'simplified-font-manager',
				'required' => false,
			],
			[
				'name'     => 'WP Gallery Enhancer',
				'slug'     => 'wp-gallery-enhancer',
				'required' => false,
			],
		];

		$config = [
			'id'           => 'bayleaf',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
		];

		tgmpa( $plugins, $config );
	}
}

TGM_Activation::init();
