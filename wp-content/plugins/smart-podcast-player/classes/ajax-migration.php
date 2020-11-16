<?php
		
class SPP_Ajax_Migration {
	
	public static function get_migration_tracks() {
		$old_url = isset( $_POST['old_url'] ) ? $_POST['old_url'] : '';
		$new_url = isset( $_POST['new_url'] ) ? $_POST['new_url'] : '';
		
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}

		header('Content-Type: application/json');
		
		if (empty($old_url) || empty($new_url)) {
			echo( json_encode( null ) ); // Better: report an error
			exit;
		}
		
		$matches = self::find_matches($old_url, $new_url);
		
		if( is_wp_error( $matches ) ) {
			trigger_error( $matches->get_error_message() );
		}
		
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $matches, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $matches );
		}
		echo $ret;
		exit;
	}
	
	public static function find_matches($old_url, $new_url) {
		
		// Get the RSS feeds, and if there's an error, stop
		$old_data = SPP_Ajax_Feed::get_and_cache_tracks($old_url, 0);
		$old_tracks = $old_data['tracks'];
		if (is_wp_error($old_tracks))
			return $old_tracks;
		$new_data = SPP_Ajax_Feed::get_and_cache_tracks($new_url, 0);
		$new_tracks = $new_data['tracks'];
		if (is_wp_error($new_tracks))
			return $new_tracks;
		$num_old = $old_data['numTracks'];
		$num_new = $new_data['numTracks'];
		
		$matches = array();
		$old_i = 0;
		$new_i = 0;
		while ($old_i < $num_old) {
			$old_title = $old_tracks[$old_i]->title;
			for ($j = 0; $j < $num_new; $j++) {
				$new_i++;
				if ($new_i >= $num_new)
					$new_i = 0;
				$new_title = $new_tracks[$new_i]->title;
				if ($old_title == $new_title) {
					$match = new stdClass();
					$match->title = $old_title;
					$match->old_url = $old_tracks[$old_i]->stream_url;
					$match->new_url = $new_tracks[$new_i]->stream_url;
					$matches[] = $match;
					break;
				}
			}
			$old_i++;
		}
		return $matches;
	}
	
	public static function search_stps() {
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}
		
		$urls = array();
		global $wpdb;
		$query = "SELECT post_content FROM " . $wpdb->posts;
		$query = $query . " WHERE (post_content LIKE '%[smart_track_player %'";
		$query = $query . " OR post_content LIKE '%[fusebox_track_player %')";
		$query = $query . " AND post_status = 'publish'";
		$query = $query . " ORDER BY post_date DESC";
		$results = $wpdb->get_results($query);
		foreach($results as $result) {
			$content = $result->post_content;
			if (preg_match_all('/(?U)\[(fusebox_track_player|smart_track_player).*url=\"(.*)\"/', $content, $matches))
				foreach ($matches[2] as $match)
					$urls[] = $match;
		}

		header('Content-Type: application/json');
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $urls, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $urls );
		}
		echo $ret;
		exit;
	}
	
	public static function add_migration_tracks() {
		$old_url = isset( $_POST['old_url'] ) ? $_POST['old_url'] : '';
		$new_url = isset( $_POST['new_url'] ) ? $_POST['new_url'] : '';
		
		if (empty($old_url) || empty($new_url)) {
			echo( json_encode( null ) ); // Better: report an error
			exit;
		}
		$matches = self::find_matches($old_url, $new_url);
		if( is_wp_error( $matches ) ) {
			trigger_error( $matches->get_error_message() );
		}
		self::add_matches($matches);
		echo(json_encode($matches));
		exit;
	}
	
	public static function add_matches($matches) {
		$table = get_option('spp_player_replacement_urls', array());
		foreach($matches as $match)
			$table[$match->old_url] = $match->new_url;
		update_option('spp_player_replacement_urls', $table);
	}
	
	public static function add_migration_tracks_B() {
		$stp_urls = isset( $_POST['stp_urls'] ) ? $_POST['stp_urls'] : '';
		$feed_urls = isset( $_POST['feed_urls'] ) ? $_POST['feed_urls'] : '';
		
		if (empty($stp_urls) || empty($feed_urls)) {
			echo( json_encode( null ) ); // Better: report an error
			exit;
		}
		
		$matches = array();
		$len = min(count($stp_urls), count($feed_urls));
		for ($i = 0; $i < $len; $i++) {
			$match = new stdClass();
			$match->old_url = $stp_urls[$i];
			$match->new_url = $feed_urls[$i];
			if (!empty($match->old_url) && !empty($match->new_url)) {
				$matches[] = $match;
			}
		}
		
		self::add_matches($matches);
		echo(json_encode($matches));
		exit;
	}
	
	public static function remove_replacements() {
		update_option('spp_player_replacement_urls', array());
	}
}
