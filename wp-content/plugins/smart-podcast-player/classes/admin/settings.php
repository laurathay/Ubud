<?php

class SPP_Admin_Settings {

	public $plugin_slug;
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'wp_ajax_spp_delete_future_url', array( $this, 'delete_future_url' ) );
	}


	public function register() {
		
		$plugin = SPP_Core::get_instance();
		$plugin->upgrade_options();
		
		register_setting( 'spp-player', 'spp_player_social' );
		register_setting( 'spp-player-general', 'spp_player_general',
				array( 'SPP_Admin_Settings', 'general_sanitize' ) );
		register_setting( 'spp-player-defaults', 'spp_player_defaults',
				array( 'SPP_Admin_Settings', 'defaults_sanitize' ) );
		register_setting( 'spp-player-email', 'spp_player_email',
				array( 'SPP_Admin_Settings', 'email_sanitize' ) );
		register_setting( 'spp-player-sticky', 'spp_player_sticky',
				array( 'SPP_Admin_Settings', 'sticky_sanitize' ) );
		register_setting( 'spp-player-advanced', 'spp_player_advanced' );
		register_setting( 'spp-player-timestamps', 'spp_player_timestamps',
				array( 'SPP_Admin_Settings', 'timestamps_sanitize' ) );
		
		add_options_page( 'Fusebox Player Settings', 'Fusebox Player',
				'manage_options', 'spp-player', array( $this, 'settings_page' ) );

	}

	public function settings_page() {
		require_once( SPP_ASSETS_PATH . 'views/settings.php' );
	}
	
	public static function general_sanitize( $general_opts ) {
		SPP_Admin_Core::license_key_reset();
		return $general_opts;
	}
	
	public static function email_sanitize( $news_opts ) {
		$checkbox_names = array(
				'cta_request_first_name',
				'cta_require_first_name',
				'cta_request_last_name',
				'cta_require_last_name',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $news_opts[ $name ] ) && $news_opts[ $name ] === 'true' ) {
				$news_opts[ $name ] = 'true';
			} else {
				$news_opts[ $name ] = 'false';
			}
		}
		
		// Add "http://" to links without it
		if (isset($news_opts['link']))
			if (preg_match('/^https?:\/\//', $news_opts['link']) === 0)
				$news_opts['link'] = "http://" . $news_opts['link'];
		return $news_opts;
	}
	
	public static function sticky_sanitize( $sticky_opts ) {
		// Change checkbox options to strings
		$checkbox_names = array(
				'social_twitter',
				'social_facebook',
				'social_gplus',
				'social_linkedin',
				'social_pinterest',
				'social_email',
				'post_type_page',
				'post_type_post',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $sticky_opts[ $name ] ) ) {
				$sticky_opts[ $name ] = 'true';
			} else {
				$sticky_opts[ $name ] = 'false';
			}
		}
		// If it's the unpaid version, there are hidden options.
		// Make sure the previously set ones don't change
		$prev_sticky_opts = get_option('spp_player_sticky', array());
		if (!SPP_Core::is_paid_version()) {
			$paid_opts = array(
					'color',
					'download',
					'social_twitter',
					'social_facebook',
					'social_gplus',
					'social_linkedin',
					'social_pinterest',
					'social_email',
				);
			foreach( $paid_opts as $opt ) {
				if (array_key_exists($opt, $prev_sticky_opts))
					$sticky_opts[$opt] = $prev_sticky_opts[$opt];
			}
		}
		return $sticky_opts;
	}
	
	public static function defaults_sanitize( $defaults ) {
		$checkbox_names = array(
				'subscribe_in_stp',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $defaults[ $name ] ) && $defaults[ $name ] === 'true' || $defaults[ $name ] === 'on' ) {
				$defaults[ $name ] = 'true';
			} else {
				$defaults[ $name ] = 'false';
			}
		}
		return $defaults;
	}
	
	public static function timestamps_sanitize( $timestamps ) {
		
		$adv = get_option('spp_player_advanced');
		if ($adv && isset($adv['timestamps_input']) && $adv['timestamps_input'] == 'true')
			$timestamps = self::timestamps_input_workaround();
		
		$checkbox_names = array(
				'show_times',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $timestamps[ $name ] ) && $timestamps[ $name ] === 'true' || $timestamps[ $name ] === 'on' ) {
				$timestamps[ $name ] = 'true';
			} else {
				$timestamps[ $name ] = 'false';
			}
		}
		
		// When the timestamps have changed, we invalidate the cache
		$timestamps['invalid_cache'] = 'true';
		
		// Change the times from with colons to seconds
		foreach ($timestamps['stamps'] as $url => $ts_array) {
			foreach ($ts_array as $time => $text) {
				unset($timestamps['stamps'][$url][$time]);
				$parts = array_reverse(explode(":", $time));
				$sec = $parts[0];
				if (isset($parts[1]))
					$sec += 60 * $parts[1];
				if (isset($parts[2]))
					$sec += 3600 * $parts[2];
				$timestamps['stamps'][$url][$sec] = $text;
				// If a timestamp for "0" is present without text, remove it
				// (Fixes bug when users try to delete "0:00" timestamps)
				if ($time == "0" && $text == "")
					unset($timestamps['stamps'][$url][0]);
			}
		}
		
		// Swap key and value for references
		if (isset($timestamps['refs'])) {
			foreach ($timestamps['refs'] as $url => $ref) {
				$timestamps['refs'][$ref] = $url;
				unset($timestamps['refs'][$url]);
			}
		}
		
		// Save which timestamp we're setting, so we can select it in the dropdown
		// Older PHP versions don't allow the construct array_keys(...)[0]
		$current_url = '';
		$stamps_keys = array_keys($timestamps['stamps']);
		if (count($stamps_keys) > 0)
			$current_url = $stamps_keys[0];
		$current_ref = '';
		$refs_keys = array_keys($timestamps['refs']);
		if (count($refs_keys) > 0)
			$current_ref = $refs_keys[0];
		$timestamps['last_set'] = $current_url;
		
		// The frontend is only sending the stamps and refs for one track, so we have
		// to put back in all of the other tracks
		$old_ts = get_option('spp_player_timestamps');
		if (!$old_ts)
			return $timestamps;
		
		if (isset($old_ts['stamps']))
			foreach ($old_ts['stamps'] as $url => $ts)
				if (!isset($timestamps['stamps'][$url]))
					$timestamps['stamps'][$url] = $ts;
				
		if (isset($old_ts['refs'])) {
			foreach ($old_ts['refs'] as $ref => $url) {
				if (isset($timestamps['refs'][$ref]) && $timestamps['refs'][$ref] !== $url) {
					// This ref already exists for a different URL.
					// Add a number until it's a unique ref.
					$i = 1;
					$orig_ref = $ref;
					while (in_array($ref, array_keys($old_ts['refs']))) {
						$ref = $orig_ref . "-" . strval($i);
						$i = $i + 1;
					}
					add_settings_error('spp-player-timestamps', 'used-ref',
						'The reference "' . $orig_ref . '" has already been used.'
						. '  The new reference "' . $ref . '" has been substitued.');
					$timestamps['refs'][$ref] = $timestamps['refs'][$orig_ref];
					$timestamps['refs'][$orig_ref] = $url;
				} else if ($url == $current_url) {
					// We're changing the ref for this URL.
					$timestamps['refs'][$current_ref] = $url;
				} else {
					// Just copy it from the old entry.
					$timestamps['refs'][$ref] = $url;
				}
			}
		}
		
		// Copy all the old future URLs over
		if (isset($old_ts['future_urls']) && is_array($old_ts['future_urls']))
			$timestamps['future_urls'] = $old_ts['future_urls'];
		else
			$timestamps['future_urls'] = array();
			
		// Add the new future URL to the array 'future_urls'
		// Single URLs come in from the settings page in 'future_url'
		// 'future_url' does not get stored; only 'future_urls'
		if (isset($timestamps['future_url']) && $timestamps['future_url'] !== "") {
			$new_future_url = self::urlCanon($timestamps['future_url']);
			if (!in_array($new_future_url, $timestamps['future_urls']))
				$timestamps['future_urls'][] = $new_future_url;
			unset($timestamps['future_url']);
			$timestamps['last_set'] = $new_future_url;
		}
		
		return $timestamps;
	}
	
	public static function urlCanon($url) {
		$url = preg_replace('/^https?:\/\//', '', $url); // Remove protocol
		$url = preg_replace('/\?.*$/', '', $url);        // Remove query string
		return $url;
	}
	
	// HS 10331 had an issue where timestamp settings wouldn't take.  The settings in the
	// multidimensional arrays spp_player_timestamps[refs] and spp_player_timestamps[stamps]
	// were not present in $_POST, but did make it into php://input.  This function uses that
	// instead of the normal way (Wordpress settings API uses $_POST).  My best guess is his
	// use of the Suhosin extension.  Much of this is from https://stackoverflow.com/questions/5077969/
	public static function timestamps_input_workaround() {
		$pairs = explode("&", file_get_contents("php://input"));
		$vars = array();
		$refs = array();
		$stamps = array();
		foreach ($pairs as $pair) {
			$nv = explode("=", $pair);
			$name = urldecode($nv[0]);
			$value = urldecode($nv[1]);
			if (preg_match('/spp_player_timestamps\[refs\]\[(.*)\]/', $name, $matches)) {
				$url = $matches[1];
				$refs[$url] = $value;
			} else if (preg_match('/spp_player_timestamps\[stamps\]\[(.*)\]\[(.*)\]/', $name, $matches)) {
				$url = $matches[1];
				$time = $matches[2];
				if ($time === "")  // To copy a quirk
					$time = "0";   // of the normal way
				$stamps[$url][$time] = $value;
			} else {
				$vars[$name] = $value;
			}
		}
		$fixed_opt = array(
			'feed_url' => $vars['spp_player_timestamps[feed_url]'],
			'show_times' => $vars['spp_player_timestamps[show_times]'],
			'refs' => $refs,
			'stamps' => $stamps,
			'future_url' => $vars['spp_player_timestamps[future_url]'],
		);
		return $fixed_opt;
	}
	
	public function delete_future_url() {
		header('Content-Type: application/json');
		
		$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
		$timestamps = get_option('spp_player_timestamps');
		if (!$timestamps || !isset($timestamps['future_urls'])) {
			echo json_encode('No future URLs found.');
			exit;
		}
		$key = array_search($url, $timestamps['future_urls']);
		if ($key === FALSE) {
			echo json_encode('Future URL not found.');
			exit;
		}
		unset($timestamps['future_urls'][$key]);
		$timestamps['future_urls'] = array_values($timestamps['future_urls']);
		update_option('spp_player_timestamps', $timestamps);
		
		// Return future URLs, with timestamps if applicable
		$future_stamps = array();
		foreach ($timestamps['future_urls'] as $fu)
			if (isset($timestamps['stamps']) && isset($timestamps['stamps'][$fu]))
				$future_stamps[$fu] = $timestamps['stamps'][$fu];
			else
				$future_stamps[$fu] = array();
		echo json_encode($future_stamps);
		exit;
	}

}
