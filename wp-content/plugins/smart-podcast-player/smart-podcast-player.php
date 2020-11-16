<?php
/**
 * Fusebox Player
 *
 * Formerly Smart Podcast Player. The WordPress media player for the future—specializing in podcasts using Soundcloud, Libsyn, and any other podcast feed that works in iTunes.
 *
 * @package   SPP
 * @author    Fusebox <help@fusebox.fm>
 * @link      http://fusebox.fm
 * @copyright 2015 SPI Labs, LLC
 * 
 * @wordpress-plugin
 * Plugin Name:       Fusebox Player
 * Plugin URI:        http://support.fusebox.fm
 * Description:       Formerly Smart Podcast Player. The WordPress media player for the future—specializing in podcasts using Soundcloud, Libsyn, and any other podcast feed that works in iTunes.
 * Version:           2.12.0
 * Author:            Fusebox
 * Author URI:        http://fusebox.fm
 * Text Domain:       smart-podcast-player
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define course constants
if ( ! defined( 'SPP_PLUGIN_BASE' ) ) {
	define( 'SPP_PLUGIN_BASE', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'SPP_PLUGIN_URL' ) ) {
	define( 'SPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SPP_ASSETS_URL' ) ) {
	define( 'SPP_ASSETS_URL', SPP_PLUGIN_URL . 'assets' . '/' );
}

if ( ! defined( 'SPP_ASSETS_PATH' ) ) {
	define( 'SPP_ASSETS_PATH', SPP_PLUGIN_BASE . 'assets' . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'SPP_INCLUDES_PATH' ) ) {
	define( 'SPP_INCLUDES_PATH', SPP_PLUGIN_BASE . 'includes' . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'SPP_SETTINGS_PAGE' ) ) {
	define( 'SPP_SETTINGS_PAGE', 'options-general.php?page=spp-player' );
}

if ( ! defined( 'SPP_SETTINGS_URL' ) ) {
	define( 'SPP_SETTINGS_URL', admin_url( SPP_SETTINGS_PAGE ) );
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( SPP_PLUGIN_BASE . 'classes/core.php' );
require_once( SPP_PLUGIN_BASE . 'classes/ajax-feed.php' );
require_once( SPP_PLUGIN_BASE . 'classes/ajax-jsobj.php' );
require_once( SPP_PLUGIN_BASE . 'classes/ajax-migration.php' );
require_once( SPP_PLUGIN_BASE . 'classes/ajax-tracks.php' );
require_once( SPP_PLUGIN_BASE . 'classes/dynamic-css.php' );
require_once( SPP_PLUGIN_BASE . 'classes/shortcodes.php' );
require_once( SPP_PLUGIN_BASE . 'classes/transients.php' );
require_once( SPP_PLUGIN_BASE . 'classes/utils/color.php' );

add_action( 'plugins_loaded', array( 'SPP_Core', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() ) {
	require_once( SPP_PLUGIN_BASE . 'classes/admin/core.php' );
	add_action( 'plugins_loaded', array( 'SPP_Admin_Core', 'get_instance' ) );
}
