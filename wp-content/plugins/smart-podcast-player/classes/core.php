<?php
/**
 * Smart Podcast Player
 * 
 * @package   SPP_Core
 * @author    jonathan@redplanet.io
 * @link      http://www.smartpodcastplayer.com
 * @copyright 2015 SPI Labs, LLC
 */

/**
 * @package SPP_Core
  * @author Jonathan Wondrusch <jonathan@redplanet.io?
 */
class SPP_Core {
	
	private static $instance = null;
	public static function get_instance() {
		if ( null == self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.12.0';

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.8.0
	 *
	 * @var      string
	 */
	const PLUGIN_SLUG = 'smart-podcast-player';


	/**
	 * Default (Green) Color for SPP/STP
	 *
	 * @since   1.0.2
	 *
	 * @var     string
	 */
	const SPP_DEFAULT_PLAYER_COLOR = '#60b86c';

	/**
	 * Soundcloud API URL 
	 *
	 * @since   1.0.3
	 *
	 * @var     string
	 */
	const SPP_SOUNDCLOUD_API_URL = 'https://api.soundcloud.com';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'wp_enqueue_scripts', array( 'SPP_Core', 'register_js' ) );

		add_shortcode( 'smart_track_player', array( 'SPP_Shortcodes', 'shortcode_smart_track_player' ) );
		add_shortcode( 'smart_track_player_latest', array( 'SPP_Shortcodes', 'shortcode_smart_track_player_latest' ) );
		add_shortcode( 'smart_podcast_player', array( 'SPP_Shortcodes', 'shortcode_smart_podcast_player' ) );
		add_shortcode( 'smart_podcast_player_assets', array( $this, 'enqueue_assets' ) );
		add_shortcode( 'stp_timestamp', array( 'SPP_Shortcodes', 'shortcode_stp_timestamp' ) );
		add_shortcode( 'stp_timestamps', array( 'SPP_Shortcodes', 'shortcode_stp_timestamps' ) );

		add_shortcode( 'fusebox_track_player', array( 'SPP_Shortcodes', 'shortcode_smart_track_player' ) );
		add_shortcode( 'fusebox_track_player_latest', array( 'SPP_Shortcodes', 'shortcode_smart_track_player_latest' ) );
		add_shortcode( 'fusebox_full_player', array( 'SPP_Shortcodes', 'shortcode_smart_podcast_player' ) );
		add_shortcode( 'fusebox_player_assets', array( $this, 'enqueue_assets' ) );
		add_shortcode( 'ftp_timestamp', array( 'SPP_Shortcodes', 'shortcode_stp_timestamp' ) );
		add_shortcode( 'ftp_timestamps', array( 'SPP_Shortcodes', 'shortcode_stp_timestamps' ) );
		
		add_action( 'wp_ajax_nopriv_get_stp_tracks_new', array( 'SPP_Ajax_Tracks', 'ajax_get_stp_new' ) );
		add_action( 'wp_ajax_get_stp_tracks_new',        array( 'SPP_Ajax_Tracks', 'ajax_get_stp_new' ) );
		
		add_action( 'wp_ajax_nopriv_get_spplayer_tracks', array( 'SPP_Ajax_Feed', 'ajax_get_tracks' ) );
		add_action( 'wp_ajax_get_spplayer_tracks',        array( 'SPP_Ajax_Feed', 'ajax_get_tracks' ) );
		
		add_action( 'wp_ajax_nopriv_get_spplayer_tracks_array', array( 'SPP_Ajax_Feed', 'ajax_get_tracks_array' ) );
		add_action( 'wp_ajax_get_spplayer_tracks_array',        array( 'SPP_Ajax_Feed', 'ajax_get_tracks_array' ) );
		
		add_action( 'wp_ajax_nopriv_spp_newsletter_subscribe', array( 'SPP_Ajax_Newsletter', 'spp_newsletter_subscribe' ) );
		add_action( 'wp_ajax_spp_newsletter_subscribe',        array( 'SPP_Ajax_Newsletter', 'spp_newsletter_subscribe' ) );
		
		add_action( 'wp_ajax_nopriv_spp_get_jsobj', array( 'SPP_Ajax_Jsobj', 'get_js_obj' ) );
		add_action( 'wp_ajax_spp_get_jsobj',        array( 'SPP_Ajax_Jsobj', 'get_js_obj' ) );
		
		add_action( 'wp_ajax_spp_get_migration_tracks', array( 'SPP_Ajax_Migration', 'get_migration_tracks' ) );
		add_action( 'wp_ajax_spp_add_migration_tracks', array( 'SPP_Ajax_Migration', 'add_migration_tracks' ) );
		add_action( 'wp_ajax_spp_search_stps', array( 'SPP_Ajax_Migration', 'search_stps' ) );
		add_action( 'wp_ajax_spp_remove_replacements', array( 'SPP_Ajax_Migration', 'remove_replacements' ) );
		add_action( 'wp_ajax_spp_add_migration_tracks_B', array( 'SPP_Ajax_Migration', 'add_migration_tracks_B' ) );

		add_action( 'template_redirect', array( $this, 'force_download' ), 1 );
		
		add_action( 'init', array( $this, 'cache_bust' ), 1 );
		add_action( 'wp_loaded', 'SPP_Core::license_check' );

		// Use shortcodes in text widgets.
		add_filter('widget_text', 'do_shortcode');
		
		add_action( 'wp_footer', array( 'SPP_Core', 'maybe_add_sticky_player_footer' ), 10 );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.8.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return self::PLUGIN_SLUG;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.8.0
	 */
	public function load_plugin_textdomain() {

		$domain = self::PLUGIN_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	public static function register_js() {
	
		$js_file = 'main-' . self::VERSION . '.min.js';
		$version_string = null;
		$advanced_options = get_option( 'spp_player_advanced' );
		if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
			$js_file = 'main.min.js';
			$version_string = self::VERSION;
		}
		
		wp_register_script(
				self::PLUGIN_SLUG . '-plugin-script',
				SPP_ASSETS_URL . 'js/' . $js_file,
				array( 'jquery' ),
				$version_string,
				true );

		// If we're on an Optimize Press pagebuilder, we actually enqueue the script at this point
		global $post;
		if( is_object( $post ) && get_post_meta( $post->ID, '_optimizepress_pagebuilder', true ) == 'Y' ) {
			self::enqueue_assets();
		}
	}
	
	public static function enqueue_assets( $html_assets = false ) {
		// Enqueue our fonts
		wp_enqueue_style( self::PLUGIN_SLUG . '-plugin-fonts',
				'https://fonts.googleapis.com/css?family=Roboto:300,400italic,600italic,700italic,400,600,700',
				array(), self::VERSION);
		
		// If the user wants HTML assets, we do that instead of the right way
		if( $html_assets == 'true' ) {
			if( ! self::is_thrive_content_builder() ) {
				add_action( 'wp_footer', array( 'SPP_Core', 'add_assets_to_html' ) );
				return;
			}
		}
		
		// If we're using Constant Contact, we manually add the JS (similar to HTML assets)
		$ctct_html = '';
		$email_options = get_option('spp_player_email');
		if ($email_options && isset($email_options['portal']) && $email_options['portal'] === 'enable'
				&& isset($email_options['service']) && $email_options['service'] === 'ctct') {
			add_action( 'wp_footer', array( 'SPP_Core', 'add_ctct_js_to_html' ), 100 );
			// Also, the HTML needs to be put in the global options below
			if (isset($email_options['embed_html_ctct'])) {
				$ctct_html = $email_options['embed_html_ctct'];
			}
		}
			
		// Get the Soundcloud key if set, or just use our own
		$soundcloud_options = get_option( 'spp_player_soundcloud' );
		if( isset( $soundcloud_options['consumer_key'] ) && !empty( $soundcloud_options['consumer_key'] ) ) {
			$soundcloud_key = $soundcloud_options['consumer_key'];
		} else {
			$soundcloud_key = 'b38b3f6ee1cdb01e911c4d393c1f2f6e';
		}

		// Put the JS object with our general settings onto the page
		$advanced_options = get_option( 'spp_player_advanced' );
		$importantStr = self::get_css_important_str() === ' !important' ? 'important' : '';
		$iom = isset($advanced_options['init_on_mutation']) && $advanced_options['init_on_mutation'] === 'true';
		wp_localize_script( self::PLUGIN_SLUG . '-plugin-script', 'AP_Player', array(
			'homeUrl' => home_url(),
			'baseUrl' => SPP_ASSETS_URL . 'js/',
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'soundcloudConsumerKey' => $soundcloud_key,
			'version' => self::VERSION,
			'importantStr' => $importantStr,
			'licensed' => self::is_paid_version(),
			'debug_output' => self::debug_output(),
			'ctct_html' => $ctct_html,
			'init_on_mutation' => $iom,
		));
	
		// Enqueue our CSS file.  If the important option is set, use the override file
		$css_file = "css/style";
		if ( "important" == $importantStr )
			$css_file = $css_file . "-override";
		if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
			$css_file = $css_file . '.css';
			$version_string = self::VERSION;
		} else {
			$css_file = $css_file . '-' . self::VERSION . '.css';
			$version_string = null;
		}
		wp_enqueue_style( self::PLUGIN_SLUG . '-plugin-styles',
				SPP_ASSETS_URL . $css_file,
				array(), $version_string );
		
		// Enqueue the Javascript file, unless this is Thrive Content Builder (HS 3831)
		if( ! self::is_thrive_content_builder() ) {
			wp_enqueue_script( self::PLUGIN_SLUG . '-plugin-script' );
		}
	}
	
	public static function is_thrive_content_builder() {
		// TCB used to not work, so we removed the JS on those pages.  It
		// looks like it works fine for version 1.500.21, so I assume they
		// fixed something, and this function now returns false for TCB
		// versions at least 1.500.21.

		if ( filter_input( INPUT_GET, 'tve' )
				&& defined( ABSPATH )
				&& include_once( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
			if( is_plugin_active( 'thrive-visual-editor/thrive-visual-editor.php' ) ) {
				$p1 = plugin_dir_path(__FILE__);
				$p2 = $p1 . '/../../thrive-visual-editor/thrive-visual-editor.php';
				if( file_exists( $p2 ) ) {
					$thrive_data = get_plugin_data( $p2 );
					if( isset( $thrive_data[ 'Version' ] )
							&& version_compare( $vers, '1.500.21', '<' ) )
						return true;
				} else {
					return true;
				}
			}
		}
		
		// Also check for Kallyas theme's editor (HS 6794)
		$zne = filter_input( INPUT_GET, 'zn_pb_edit' );
		if( $zne == 'true' ) {
			return true;
		}
		return false;
	}
	
	public static function maybe_add_sticky_player_footer() {
		echo do_shortcode(self::maybe_add_sticky_player(""));
	}
	
	public static function maybe_add_sticky_player($content) {
		
		$sticky_settings = get_option( 'spp_player_sticky' );
		$dbg = self::debug_output();
		if (!$sticky_settings || !isset($sticky_settings['enabled']) || $sticky_settings['enabled'] !== 'true')
			return $content . $dbg ? '<!-- SPP sticky: Not enabled. -->' : '';
		
		global $post;
		global $wp;
		if(!$post instanceof WP_Post)
			return $content . $dbg ? '<!-- SPP sticky: $post is not WP_Post. -->' : '';
		
		if (isset($sticky_settings['post_types']))
			$type_array = preg_split("/\s+/", $sticky_settings['post_types']);
		else
			$type_array = array();
		if (isset($sticky_settings['post_type_page']) && $sticky_settings['post_type_page'] == 'false') {
		} else {
			$type_array[] = 'page';
		}
		if (isset($sticky_settings['post_type_post']) && $sticky_settings['post_type_post'] == 'false') {
		} else {
			$type_array[] = 'post';
		}
		
		if (in_array($post->post_type, $type_array)) {
			if (isset($sticky_settings['excluded_pages'])) {
				$current_url = trailingslashit(home_url($wp->request));
				$struct = get_option('permalink_structure');
				if (!$struct) {
					$current_url = add_query_arg($wp->query_vars, $current_url);
				}
				$current_url =  preg_replace("/^http(s?):\/\//", "", $current_url);
				$excl_array = preg_split("/\s+/", $sticky_settings['excluded_pages']);
				foreach ($excl_array as $excl) {
					$excl = str_replace(array("\r", "\n"), '', $excl);
					$excl =  preg_replace("/^http(s?):\/\//", "", $excl);
					if ($current_url === $excl) {
						return $content . $dbg ? '<!-- SPP sticky: Page excluded. -->' : '';
					}
					$excl = trailingslashit($excl);
					if ($current_url === $excl) {
						return $content . $dbg ? '<!-- SPP sticky: Page excluded. -->' : '';
					}
				}
			}
			return self::add_sticky_player($content);
		} else {
			return $content . $dbg ? '<!-- SPP sticky: Post type is ' . $post->post_type . ' -->' : '';
		}
	}
	
	public static function add_sticky_player($content) {
		$sticky_settings = get_option( 'spp_player_sticky' );
		if ($sticky_settings
				&& isset($sticky_settings['latest'])
				&& $sticky_settings['latest'] == 'false'
				&& isset($sticky_settings['episode_url'])) {
			$sc = '[smart_track_player sticky="true" url="' . $sticky_settings['episode_url'] . '" ';
		} else {
			$sc = '[smart_track_player_latest sticky="true" ';
		}
		if ($sticky_settings && isset($sticky_settings['shortcode_options'])) {
			$sc_opts = $sticky_settings['shortcode_options'];
			$sc_opts = str_replace(array("\r", "\n"), ' ', $sc_opts);
			$sc = $sc . $sc_opts;
		}
		$sc = $sc . ']';
		$bumper = '<div class="spp-sticky-bumper"></div>';
		return $content . $sc . $bumper;
	}

	/**
	 * Use the SPP_Download class to force file downloads based on methods available
	 * 
	 * @return void
	 */
	public function force_download() {
		if( isset( $_GET['spp_download'] ) ) {
			require_once( SPP_PLUGIN_BASE . 'classes/download.php' );
			$download_id = $_GET['spp_download'];
			$download = new SPP_Download( $download_id );
			$download->get_file();
			exit;
		}
	}

	/**
	 * Delete the internal spp_cache when the URL variables are present
	 * 
	 * @return void
	 */
	public function cache_bust() {
		$bust = filter_input( INPUT_GET, 'spp_cache' );
		if( $bust == 'bust' ) {
			self::clear_cache();
		}
	}
	
	public static function clear_cache() {
	
		if (current_user_can( 'update_plugins' ) ) {

			global $wpdb;

			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE autoload='no' AND option_name LIKE %s", '%spp\_cache%' ) );
			
			delete_option( 'spp_license_check' );
			delete_option( 'external_updates-smart-podcast-player' );
			
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE autoload='no' AND option_name LIKE %s", '%spp\_feed_%' ) );

			
			// Mark that for the next 5 minutes, we're not using SimplePie's cache
			set_transient( 'spp_cache_clear_simplepie', true, 5 * MINUTE_IN_SECONDS );
		}
	
	}
	
	// Returns the license key from the user settings.  If the key has not been
	// entered, null is returned.
	public static function get_license_key() {
		$settings = get_option( 'spp_player_general' );
		if( !isset( $settings[ 'license_key' ] ) || empty( $settings[ 'license_key' ] ) ) {
			return null;
		}
		return $settings[ 'license_key' ];
	}
	
	/**
	 *  There's an old saying, "Locks exist to keep honest people honest."  Anyone with
	 *  a bit of knowhow can get by most locks - this one included.  Our software is
	 *  not a necessity.  If you don't want to pay for it, you don't need to use it.
	 *                                  Thanks,
	 *                                  A hardworking software dev
	 */
	public static function license_check() {
		// Look at our license check option.  If it exists and has not expired,
		// we don't need to perform a license check
		$option_name = 'spp_license_check';
		$check = get_option( $option_name );
		if( is_array( $check )&& isset( $check[ 'expiration' ] ) && $check[ 'expiration' ] > time() ) {
			return;
		}
		
		// Set the option now (to failed), just in case the new code below errors out
		update_option( $option_name, array(
				'result' => 'fail',
				'expiration' => time() + MINUTE_IN_SECONDS ) );
		
		// Grab the license key from the user settings.  If it's blank, fail.
		$license_key = self::get_license_key();
		if( !isset( $license_key ) ) {
			update_option( $option_name, array(
					'result' => 'fail',
					'expiration' => time() + WEEK_IN_SECONDS ) );
			return;
		}
		
		// Perform an update check to actually check the license
		require_once( SPP_PLUGIN_BASE . 'classes/admin/core.php' );
		if( SPP_Admin_Core::update_check( $license_key ) ) {
			// License is good
			$message = '';
			if (false !== ($last_result = get_option('spp_puc_last_result'))
					&& array_key_exists('pluginInfo', $last_result)) {
				$message = $last_result['pluginInfo']->grace_message;
			}
			update_option( $option_name, array(
					'result' => 'success',
					'expiration' => time() + WEEK_IN_SECONDS,
					'message' => $message,
					));
			return;
		}

		// Fallback method: use curl directly to check the same URL
		if( function_exists( 'curl_init' ) ) {
			$temp_url = 'https://my.fusebox.fm/license/check/'
					  . trim($license_key)
					  . '/?checking_for_updates=1&installed_version='
					  . SPP_Core::VERSION;
			$temp_ch = curl_init();
			curl_setopt( $temp_ch, CURLOPT_URL, $temp_url );
			curl_setopt( $temp_ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt( $temp_ch, CURLOPT_RETURNTRANSFER, true);
			$temp_header = curl_exec( $temp_ch );
			curl_close( $temp_ch );
			if( strpos( $temp_header, 'Smart Podcast Player' ) !== false
					|| strpos( $temp_header, 'Fusebox Player') !== false ) {
				// License is good
				update_option( $option_name, array(
						'result' => 'success',
						'expiration' => time() + WEEK_IN_SECONDS ) );
				return;
			}
		}
		
		// License check has failed.
		$message = '';
		if (false !== ($last_result = get_option('spp_puc_last_result'))) {
			$result = $last_result['result'];
			if ( is_wp_error($result) ) {
				$message .= "WP HTTP error: " . $result->get_error_message();
			} else if (isset($result['body']) && $result['body'] !== '""') {
				$message .= $result['body'];
			} else {
				$message = 'Your license key is invalid. Please check your license key at '
						.'<a href="https://my.fusebox.fm" target="_blank">https://my.fusebox.fm</a>. ';
			}
		}
		update_option( $option_name, array(
				'result' => 'fail',
				'expiration' => time() + WEEK_IN_SECONDS ,
				'message' => $message,
				));
		return;

	}
	
	/**
	 * Tells whether this version is the paid or free version
	 *
	 * @return true if this is the paid version of the player, false otherwise
     *
	 * @since 1.0.2
	 */

	public static function is_paid_version() {
		$check = get_option( 'spp_license_check' );
		if( is_array( $check ) && isset( $check[ 'result' ] ) && $check[ 'result' ] === 'success' )
			return true;
		return false;
	}
	
	/**
	 * Gets an array of the colors included in the free version
	 *
	 * @return an array of the colors included in the free version
	 *
	 * @since 1.0.20
	 */
	public static function get_free_colors() {
		return array( 'green' => self::SPP_DEFAULT_PLAYER_COLOR ,
				'blue' => '#006cb5',
				'yellow' => '#f0af00',
				'orange' => '#e7741b',
				'red' => '#dc1a26',
				'purple' => '#943f93' );
	}

	public static function upgrade_options() {

	    $version = get_option( 'spp_version' );

	    if ( $version != self::VERSION ) {

			// Update the version number
	    	update_option( 'spp_version', self::VERSION );
			
			// If the options don't exist, create them
			if( ! get_option( 'spp_player_general' ) )
				add_option( 'spp_player_general', array() );
			if( ! get_option( 'spp_player_defaults' ) )
				add_option( 'spp_player_defaults', array() );
			if( ! get_option( 'spp_player_advanced' ) )
				add_option( 'spp_player_advanced', array() );
			if( ! get_option( 'spp_player_soundcloud' ) )
				add_option( 'spp_player_soundcloud', array() );
			// Sticky options set below
	        
	        // Migrate ap_player options to spp_player options.
			// This change happened around August 2014.
	        if(( 
	        	!get_option( 'spp_player_general' ) || 
	        	!get_option( 'spp_player_defaults' ) || 
	        	!get_option( 'spp_player_soundcloud' ) 
	        	) && ( 
	        	get_option( 'ap_player_general' ) !== false || 
	        	get_option( 'ap_player_defaults' ) !== false || 
	        	get_option( 'ap_player_soundcloud' ) !== false 
	        )) { 
				$options = array(
					'ap_player_general' => 'spp_player_general',
					'ap_player_default' => 'spp_player_defaults',
					'ap_player_soundcloud' => 'spp_player_soundcloud'
				);
				foreach( $options as $old => $new ) {
					$option = get_option( $old );
					if( get_option( $new ) == false && $option !== false ) {
						add_option( $new, $option );
					}
					delete_option( $old );
				}
	        }
			
			// Before version 2.0, there was an option called "Subscription URL (usually iTunes)".
			// This was put into the link containing the RSS icon.  From version 2.0, there are
			// four+ options (iTunes, Stitcher, Soundcloud, Buzzsprout, +) plus regular RSS.  This function
			// populates the new options with data from the old option, if available, to try to
			// give the users a seamless transition.
			$options = get_option( 'spp_player_defaults' );
			if( $options !== false && array_key_exists( 'subscription', $options ) ) {
				$old = $options['subscription'];
				if( strpos( $old, 'itunes' ) !== false && ! array_key_exists( 'subscribe_itunes', $options ) ) {
					$options['subscribe_itunes'] = $old;
				} else if( strpos( $old, 'stitcher' ) !== false && ! array_key_exists( 'subscribe_stitcher', $options ) ) {
					$options['subscribe_stitcher'] = $old;
				} else if( strpos( $old, 'soundcloud' ) !== false && ! array_key_exists( 'subscribe_soundcloud', $options ) ) {
					$options['subscribe_soundcloud'] = $old;
				} else if( strpos( $old, 'buzzsprout' ) !== false && ! array_key_exists( 'subscribe_buzzsprout', $options ) ) {
					$options['subscribe_buzzsprout'] = $old;
				} else if( strpos( $old, 'play.google' ) !== false && ! array_key_exists( 'subscribe_googleplay', $options ) ) {
					$options['subscribe_googleplay'] = $old;
				} else if( strpos( $old, 'iheartradio' ) !== false && ! array_key_exists( 'subscribe_iheartradio', $options ) ) {
					$options['subscribe_iheartradio'] = $old;
				} else if( strpos( $old, 'pocketcasts' ) !== false  && ! array_key_exists( 'subscribe_pocketcasts', $options )) {
					$options['subscribe_pocketcasts'] = $old;
				}
				unset( $options['subscription'] );
				update_option( 'spp_player_defaults', $options );
			}
			
			// If this is a brand new install, set css_important to "true"
			// Otherwise, use the old default of "false" if not set
			$adv = get_option( 'spp_player_advanced' );
			if( $version === false ) { // New install
				$adv['css_important'] = true;
				update_option( 'spp_player_advanced', $adv );
			} else {
				if( ! isset( $adv['css_important'] ) ) {
					$adv['css_important'] = false;
					update_option( 'spp_player_advanced', $adv );
				}
			}
			
			// Version 2.4: Zapier removed; change to "none".  Basic CTA also removed.
			// Portals changed to just 'none' or 'enable'.  MC/CK embed code moved to embed_html.
			$email_opts = get_option( 'spp_player_email', array() );
			if( $email_opts !== false ) {
				if (array_key_exists( 'portal', $email_opts )) {
					if ($email_opts['portal'] === 'zp') {
						$email_opts['portal'] = 'none';
					} else if ($email_opts['portal'] === 'ck') {
						$email_opts['portal'] = 'enable';
						if (!array_key_exists('embed_html', $email_opts) && array_key_exists('ck_html', $email_opts)) {
							$email_opts['embed_html'] = $email_opts['ck_html'];
						}
					} else if ($email_opts['portal'] === 'mc') {
						$email_opts['portal'] = 'enable';
						if (!array_key_exists('embed_html', $email_opts) && array_key_exists('mc_html', $email_opts)) {
							$email_opts['embed_html'] = $email_opts['mc_html'];
						}
					}
				}
				$email_opts['email_use_spp_cta'] = false;
			}
			
			// Version 2.5: embed_html split into ctct and other
			if (!array_key_exists('embed_html_ctct', $email_opts)) {
				$email_opts['embed_html_ctct'] = $email_opts['embed_html'];
			}
			if( $email_opts !== false ) {
				update_option( 'spp_player_email' , $email_opts );
			}
			
			// Version 2.6: Pull all the sticky player options from the defaults
			$def = get_option( 'spp_player_defaults' );
			$stk = get_option( 'spp_player_sticky' );
			if (!$stk) {
				add_option( 'spp_player_sticky', array() );
				$stk = array();
				if ($def) {
					if (isset( $def['url'] ) )
						$stk['url'] = $def['url'];
					if (isset( $def['show_name'] ) )
						$stk['show_name'] = $def['show_name'];
					if (isset( $def['bg_color'] ) )
						$stk['color'] = $def['bg_color'];
					if (isset( $def['download'] ) )
						$stk['download'] = $def['download'];
					if (isset( $def['stp_image'] ) )
						$stk['image'] = $def['stp_image'];
					if (isset( $def['style'] ) )
						$stk['style'] = $def['style'];
				}
				update_option( 'spp_player_sticky', $stk );
			}
			
			// Version 2.7: Pull the feed URL from the defaults
			$ts = get_option('spp_player_timestamps');
			if (!$ts)
				$ts = array();
			if (!isset($ts['feed_url']) && isset($def['url']))
				$ts['feed_url'] = $def['url'];
			update_option('spp_player_timestamps', $ts);
	    }

	}
	
	public static function get_css_important_str() {
	
		// Use the old "false" default here, in case a user is updating to 2.3.0 from an
		// earlier version and does not have css_important set and does not load an admin page
		// (thus invoking upgrade_options) immediately after updating
		$advanced_options = get_option( 'spp_player_advanced');
		$css_important = isset( $advanced_options['css_important'] ) ? $advanced_options['css_important'] : "false";
		if ("true" == $css_important) {
			$important_str = " !important";
		} else {
			// Regular styles
			$important_str = "";
		}
		return $important_str;
	}
	
	public static function get_sticky_z_index() {
		$sticky_options = get_option( 'spp_player_sticky');
		if ($sticky_options && isset($sticky_options['z_index']))
			return $sticky_options['z_index'];
		return 1000;
	}
	
	public static function debug_output() {
		$advanced_options = get_option( 'spp_player_advanced' );
		if( isset( $advanced_options[ 'debug_output' ] )
				&& $advanced_options[ 'debug_output' ] === 'true' )
			return true;
		return false;
	}
	
	// Write the assets straight to the HTML.  This functionality should be done via
	// wp_localize_script and wp_enqueue_script, but sometimes it isn't (HS 3933).
	public static function add_assets_to_html() {
	
		$soundcloud = get_option( 'spp_player_soundcloud' );
		$key = isset( $soundcloud[ 'consumer_key' ] ) ? $soundcloud[ 'consumer_key' ] : '';
		$importantStr = self::get_css_important_str() === ' !important' ? 'important' : '';
		$advanced_options = get_option( 'spp_player_advanced' );
		$versioned_assets = true;
		if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
			$versioned_assets = false;
		}
		if( $versioned_assets ) {
			$js_file = 'main-' . self::VERSION . '.min.js';
		} else {
			$js_file = 'main.min.js?ver=' . self::VERSION;
		}
		$css_file = 'css/style';
		if( $importantStr === 'important' ) {
			$css_file .= '-override';
		}
		if( $versioned_assets ) {
			$css_file .= '-' . self::VERSION . '.css';
		} else {
			$css_file .= '.css?ver=' . self::VERSION;
		}
		
		$iom = isset($advanced_options['init_on_mutation']) && $advanced_options['init_on_mutation'] === 'true';
		$output = '';
		$output .= '<script type="text/javascript">';
		$output .=    '/* <![CDATA[ */';
		$output .=    'var AP_Player = {';
		$output .=       str_replace('/', '\\/', '"homeUrl":"' . home_url() . '",');
		$output .=       str_replace('/', '\\/', '"baseUrl":"' . SPP_ASSETS_URL .'js/') . '",';
		$output .=       str_replace('/', '\\/', '"ajaxurl":"' . admin_url( 'admin-ajax.php' ) ) .'",';
		$output .=       '"soundcloudConsumerKey":"' . $key . '",';
		$output .=       '"version":"' . self::VERSION . '",';
		$output .=       '"importantStr":"' . $importantStr . '",';
		$output .=       '"licensed":"' . self::is_paid_version() . '",';
		$output .=       '"debug_output":"' . self::debug_output() . '",';
		$output .=       '"html_assets":"' . 'true' . '",';
		$output .=       '"init_on_mutation":"' . $iom . '",';
		$output .=    '};';
		$output .=    '/* ]]> */';
		
		$output .= '</script>';
		$output .= '<script type="text/javascript" src="';
		$output .=    SPP_ASSETS_URL . 'js/' . $js_file . '"></script>';
		
		$output .= '<link rel="stylesheet" id="smart-podcast-player-plugin-styles-css" href="';
		$output .=    SPP_ASSETS_URL . $css_file . '" type="text/css" media="all">';
		
		echo $output;

	}
	
	// Add whatever the user put into the CTCT JS box to the page
	public static function add_ctct_js_to_html() {
		$email_options = get_option('spp_player_email');
		if ($email_options && isset($email_options['embed_js'])) {
			echo $email_options['embed_js'];
		}
	}
	
   public static function err_handle( $errno, $errstr, $errfile, $errline) {
		   echo 'Error ' . $errno . ' at line ' . $errline . ' of ' . $errfile . ': ' . $errstr;
		   return true;
   }

   public static function check_for_fatal() {
		   $error = error_get_last();
		   if ( $error["type"] == E_ERROR ) {
				   self::err_handle( $error["type"], $error["message"], $error["file"], $error["line"] );
		   }
		   exit();
   }


}
