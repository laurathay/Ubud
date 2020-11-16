<?php
/**
 * Instance counter class.
 *
 * @since      1.0.0
 *
 * @package    Bayleaf
 */

namespace bayleaf;

/**
 * Instance counter.
 *
 * @package    bayleaf_premium
 * @author     vedathemes <contact@vedathemes.com>
 */
class Instance_Counter {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Podcast instance counter.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	private $counter = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->counter = wp_rand( 1, 1000 );
	}

	/**
	 * Return current instance of a key.
	 *
	 * @since  1.0.0
	 *
	 * @return int
	 */
	public function get() {
		return $this->counter += 1;
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
