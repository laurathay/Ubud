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
  * @package SPP_Admin_Core
  * @author Jonathan Wondrusch <jonathan@redplanet.io?
 */

class SPP_Admin_Core {

	protected $_settings = array();

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;


	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = SPP_Core::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( dirname(__FILE__) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		add_action( 'init', array( $this, 'settings' ) );
		add_action( 'init', array( 'SPP_Admin_Core', 'update_check' ) );
		add_action( 'init', array( 'SPP_Admin_Core', 'enqueue_block_assets' ) );
		
		add_action( 'admin_post_clear_spp_cache', 'SPP_Admin_Core::clear_spp_cache_fn' );
		add_action( 'wp_ajax_spp_dismiss_mbstring_notice', array( $this, 'dismiss_mbstring_notice' ) );

		global $pagenow;

		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ||  $pagenow == 'options-general.php' || $pagenow != 'widgets.php' || current_user_can('publish_posts') ) {

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			
			// Pass the settings variable to JS
			add_action( 'admin_head', array( 'SPP_Admin_Core', 'output_settings_var' ) );

			// add new buttons
			add_filter('mce_buttons', array( $this, 'register_buttons' ) );
			add_filter('mce_external_plugins', array( $this, 'register_tinymce_javascript' ) );
			add_action( 'admin_head', array( $this, 'fb_add_tinymce' ) );
			add_action( 'admin_head', array( $this, 'admin_css' ) );

		}


	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
		
	}

	/**
	 * Register and enqueue admin-specific style sheet and Javascript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_assets() {
		
		// The filename depends on whether versions are in the filename or the query string
		$admin_js_file = 'admin-spp-' . SPP_Core::VERSION . '.min.js';
		$version_string = null;
		$advanced_options = get_option( 'spp_player_advanced' );
		if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
			$admin_js_file = 'admin-spp.min.js';
			$version_string = SPP_Core::VERSION;
		}

		// Check whether we're running Gutenberg
		$gutenberg = function_exists('register_block_type');
		
		// Register the CSS file
		wp_register_style(
				SPP_Core::PLUGIN_SLUG . '-admin-styles',
				SPP_ASSETS_URL . 'css/admin.css',
				$gutenberg ? array('wp-edit-blocks') : array(),
				$version_string,
				false);

		// Register the JS file
		$js_deps = array('jquery', 'underscore');
		$advanced_options = get_option( 'spp_player_advanced');
		$color_pickers = isset( $advanced_options['color_pickers'] ) ? $advanced_options['color_pickers'] : "true";
		if ("true" == $color_pickers) {
			$js_deps[] = 'wp-color-picker';
			wp_enqueue_style('wp-color-picker');
		}
		wp_register_script(
				$this->plugin_slug . '-admin-script',
				SPP_ASSETS_URL . 'js/admin/' . $admin_js_file,
				$js_deps,
				$version_string,
				false);
		
		// Put any future timestamps into their own variable
		$timestamps = get_option('spp_player_timestamps');
		$future_stamps = array();
		if ($timestamps && isset($timestamps['future_urls']))
			foreach ($timestamps['future_urls'] as $fu)
				if (isset($timestamps['stamps']) && isset($timestamps['stamps'][$fu]))
					$future_stamps[$fu] = $timestamps['stamps'][$fu];
				else
					$future_stamps[$fu] = array();
				
		// Put some necessary information on the page
		wp_localize_script( SPP_Core::PLUGIN_SLUG . '-admin-script', 'SmartPodcastPlayerAdmin', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'user_settings' => get_option( 'spp_player_defaults' ),
			'licensed' => SPP_Core::is_paid_version(),
			'timestamp_refs' => $timestamps && isset($timestamps['refs'])
					? $timestamps['refs'] : array(),
			'last_timestamp_set' => $timestamps && isset($timestamps['last_set'])
					? $timestamps['last_set'] : '',
			'future_timestamps' => $future_stamps,
		));
		
		// Enqueue the CSS and JS
		wp_enqueue_style(SPP_Core::PLUGIN_SLUG . '-admin-styles');
		wp_enqueue_script(SPP_Core::PLUGIN_SLUG . '-admin-script');

	}
	
	public static function enqueue_block_assets() {

		// Check whether we're running Gutenberg.  If not, skip the whole thing
		if (! function_exists('register_block_type'))
			return;
		
		// The filename depends on whether versions are in the filename or the query string
		$blocks_js_file = 'blocks-' . SPP_Core::VERSION . '.min.js';
		$version_string = null;
		$advanced_options = get_option( 'spp_player_advanced' );
		if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
			$blocks_js_file = 'blocks.min.js';
			$version_string = SPP_Core::VERSION;
		}
		
		wp_register_script(
				SPP_Core::PLUGIN_SLUG . '-block-script',
				SPP_ASSETS_URL . 'js/admin/' . $blocks_js_file,
				array('jquery', 'underscore', 'wp-blocks', 'wp-element'),
				$version_string,
				false);
		register_block_type(SPP_Core::PLUGIN_SLUG . '/spp', array(
				'editor_script' => SPP_Core::PLUGIN_SLUG . '-block-script',
			));
		register_block_type(SPP_Core::PLUGIN_SLUG . '/stp', array(
				'editor_script' => SPP_Core::PLUGIN_SLUG . '-block-script',
			));
	}
	
	public static function output_settings_var() {
		if ( function_exists( 'json_encode' ) ) {
			$js_var = new StdClass();
			$js_var->ajax_url = admin_url( 'admin-ajax.php' );
			$js_var->user_settings = get_option( 'spp_player_defaults' );
			?>
				<script type="text/javascript">
					var smart_podcast_player_user_settings = 
					<?php echo json_encode(get_option( 'spp_player_defaults' ) ); ?>;
				</script>
			<?php
		}
	}

	public function settings() {

		require_once( SPP_PLUGIN_BASE . 'classes/admin/settings.php' );
		$this->settings = new SPP_Admin_Settings();
		
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}
	
	public static function dismiss_mbstring_notice() {
		$gen = get_option('spp_player_general');
		if (!$gen)
			$gen = array();
		$gen['mbstring_notice_dismissed'] = true;
		update_option('spp_player_general', $gen);
	}
	
	// Filter to set sslverify to false in the plugin update checker
	public static function puc_filter( $options ) {
		$options[ 'sslverify' ] = false;
		return $options;
	}
	// Filter to add the site's URL to the plugin update checker
	public static function puc_filter_add_query_args( $query_args ) {
		$query_args[ 'site_url' ] = site_url();
		$query_args[ 'plugin' ] = 'smart-podcast-player';
		return $query_args;
	}
	
	public static function puc_filter_request_info_result($pluginInfo, $result) {
		update_option('spp_puc_last_result', array(
			'pluginInfo' => $pluginInfo,
			'result' => $result,
		));
		return $pluginInfo;
	}
	
	// Performs an update check.  Returns whether the check completed successfully,
	// regardless of whether updates are available.  This also serves as a license
	// check, since the server at my.fusebox.fm will not return information
	// if the license is invalid.
	public static function update_check( $license_key = null ) {
		if( empty( $license_key ) ) {
			$license_key = SPP_Core::get_license_key();
			if( empty( $license_key ) )
				return false;
		}
		
		$update_server = 'https://my.fusebox.fm';
		if( ( $util_opt = get_option( 'spp_util_general' ) )
				&& isset( $util_opt[ 'update_server' ] )
				&& defined( 'ABSPATH' )
				&& include_once( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
			if( function_exists( 'is_plugin_active' )
					&& (is_plugin_active('smart-podcast-player-utilities/smart-podcast-player-utilities.php')
					|| is_plugin_active('fusebox-utilities/fusebox-utilities.php'))) {
				$update_server = $util_opt[ 'update_server' ];
			}
		}
		
		require_once( SPP_PLUGIN_BASE . 'classes/vendor/plugin-update-checker-1.6.1/plugin-update-checker.php' );
		$puc = new PluginUpdateChecker_1_6_for_SmartPodcastPlayer (
			$update_server . '/license/check/' . trim($license_key) . '/',
			SPP_PLUGIN_BASE . 'smart-podcast-player.php',
			'smart-podcast-player',
			24
		);
		$puc->addHttpRequestArgFilter( array( 'SPP_Admin_Core', 'puc_filter' ) );
		$puc->addQueryArgFilter( array( 'SPP_Admin_Core', 'puc_filter_add_query_args' ) );
		add_filter('puc_request_info_result-'.$puc->slug, array('SPP_Admin_Core', 'puc_filter_request_info_result'), 10, 3);
		if (true == get_transient('spp_force_puc_check')) {
			delete_transient('spp_force_puc_check');
			add_filter('puc_check_now-'.$puc->slug, array('SPP_Admin_Core', 'force_puc_check'));
		}
		if( current_user_can( 'update_plugins' ) ) {
			$puc->maybeCheckForUpdates();
		}
		$state = $puc->getUpdateState();
		if( $state !== null && $state->update !== null )
			return true;
		return false;
	}
	
	public static function force_puc_check() {
		return true;
	}

	public function register_buttons($buttons) {
	   array_push( $buttons, 'separator', 'spp' );
	   array_push( $buttons, 'separator', 'stp' );
	   return $buttons;
	}

	public function register_tinymce_javascript( $plugin_array ) {
	   $plugin_array['spp'] = SPP_PLUGIN_URL . 'assets/js/admin/mce-spp.js' . '?v=' . SPP_Core::VERSION;
	   $plugin_array['stp'] = SPP_PLUGIN_URL . 'assets/js/admin/mce-stp.js' . '?v=' . SPP_Core::VERSION;
	   return $plugin_array;
	}

	public function fb_add_tinymce() {
	    global $typenow;
	    global $pagenow;

	    // only on Post Type: post and page
	    if( ! in_array( $typenow, array( 'post', 'page' ) ) && $pagenow != 'post.php' && $pagenow != 'post-new.php' )
	        return ;

	    add_filter( 'mce_external_plugins', array( $this, 'fb_add_tinymce_plugin' ) );
	    // Add to line 1 form WP TinyMCE
	    add_filter( 'mce_buttons', array( $this, 'fb_add_tinymce_button' ) );

	}

	// inlcude the js for tinymce
	public function fb_add_tinymce_plugin( $plugin_array ) {

	    $plugin_array['spp'] = SPP_PLUGIN_URL . 'assets/js/admin/mce-spp.js' . '?v=' . SPP_Core::VERSION;
	    $plugin_array['stp'] = SPP_PLUGIN_URL . 'assets/js/admin/mce-stp.js' . '?v=' . SPP_Core::VERSION;
	    
	    return $plugin_array;
	}

	// Add the button key for address via JS
	public function fb_add_tinymce_button( $buttons ) {

	    array_push( $buttons, 'spp_button_key' );
	    array_push( $buttons, 'stp_button_key' );

	    return $buttons;
	    
	}

	public function admin_css() {
		?>
			<style>
			i.mce-i-stp-icon {
				background-image: url("<?php echo SPP_PLUGIN_URL ?>assets/images/stp-icon.png" );
			}
			i.mce-i-spp-icon {
				background-image: url("<?php echo SPP_PLUGIN_URL ?>assets/images/spp-icon.png" );
			}
			</style>
		<?php
	}
	
	public static function clear_spp_cache_fn() {
	
		if ( ! wp_verify_nonce( $_POST[ 'clear_spp_cache_nonce' ], 'clear_spp_cache' ) )
            die( 'Invalid nonce.' . var_export( $_POST, true ) );
		
		SPP_Core::clear_cache();
		
		if ( ! isset ( $_POST['_wp_http_referer'] ) )
            die( 'Missing target.' );
		
		$url = add_query_arg( 'spp_cache', 'cleared', urldecode( $_POST['_wp_http_referer'] ) );
        wp_safe_redirect( $url );
        exit;
	}
	
	// Invalidate any previous license checks
	public static function license_key_reset() {
		delete_option('spp_license_check');
		set_transient('spp_force_puc_check', true, 10);
	}

}
