<?php
		
class SPP_Ajax_Feed {
	
	/**
	 * Get track data via AJAX
	 * @return 	json 	JSON object representing all tracks
	 */
	public static function ajax_get_tracks() {

		$url = isset( $_POST['stream'] ) ? $_POST['stream'] : '';
		$episode_limit = isset( $_POST['episode_limit'] ) ? $_POST['episode_limit'] : 0;
		$delay = isset( $_POST['delay'] ) ? $_POST['delay'] : 0;
		$get_ep_num = isset( $_POST['get_ep_num'] ) ? $_POST['get_ep_num'] : false;
		
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}

		header('Content-Type: application/json');
		
		if ( empty( $url ) ) {
			echo( json_encode( null ) ); // Better: report an error
			exit;
		}
		
		$data = self::get_and_cache_tracks( $url, $episode_limit, false, $get_ep_num );
		
		if( is_wp_error( $data[ 'tracks' ] ) ) {
			trigger_error( $data[ 'tracks' ]->get_error_message() );
		}
		
		sleep($delay);
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $data[ 'tracks' ], JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $data[ 'tracks' ] );
		}
		echo $ret;
		exit;

	}
	
	public static function ajax_get_tracks_array() {
		
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}
		
		$url_array = isset( $_POST['streams'] ) ? $_POST['streams'] : '';
		$episode_limit_array = isset( $_POST['episode_limits'] ) ? $_POST['episode_limits'] : 0;
		$uid_array = isset( $_POST['uids'] ) ? $_POST['uids'] : false;

		if( !$url_array || !$uid_array || count($url_array) !== count($uid_array) ) {
			trigger_error( 'URLs and UIDs required' );
			die();
		}
		
		$response_array = array();
		$len = count($url_array);
		for( $i = 0; $i < $len; $i++ ) {
			$data = self::get_and_cache_tracks( $url_array[$i], $episode_limit_array[$i] );
			if( is_wp_error( $data[ 'tracks' ] ) ) {
				trigger_error( $data[ 'tracks' ]->get_error_message() );
			}
			$response_array[] = $data[ 'tracks' ];
			$response_array[$i]['uid'] = $uid_array[$i];
		}
		
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $response_array, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $response_array );
		}
		header('Content-Type: application/json');
		echo $ret;
		exit;
	}
	
	public static function get_and_cache_tracks( $url, $episode_limit, $no_cache = false, $get_ep_num = false ) {
		
		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'tracks from feed url',
				'url' => $url,
				'episode_limit' => $episode_limit ) );
		$data = get_transient( $transient_name );
		
		if( ( ( false === $data ) || !isset( $data['tracks'] ) ) || $no_cache ) {

			$data = array(
				'url' => $url,
				'numTracks' => 0,
				'tracks' => array(),
			);
				
			// Limit the free version to ten tracks
			if( !SPP_Core::is_paid_version() ) {
				$episode_limit = 10;
			}
			
			if( strpos( $url, 'http://soundcloud.com/' ) !== false || strpos( $url, 'http://soundcloud.com/sets/' ) !== false || strpos( $url, 'https://soundcloud.com/' ) !== false || strpos( $url, 'https://soundcloud.com/sets/' ) !== false ) {

				$tracks = self::get_soundcloud_tracks( $url, $episode_limit );

			} else {
				$tracks = self::get_rss_tracks( $url, $episode_limit, $get_ep_num );
			}
			$tracks = self::add_timestamps($tracks);
			
			$data[ 'tracks' ] = $tracks;
			if( ! is_wp_error( $tracks ) && is_array( $tracks ) && !empty( $tracks ) ) {
				$data[ 'numTracks' ] = count( $data[ 'tracks' ] );
				if ($timeout > 0) {
					set_transient( $transient_name, $data, $timeout );
				}
			} else {
				// Prevent crazy load and re-fetching
				set_transient( $transient_name, $data, MINUTE_IN_SECONDS);
			}
		} else {
			// We have cached data, but maybe need to fix the timestamps
			// $timestamps = get_option('spp_player_timestamps');
			// if (isset($timestamps['invalid_cache']) && $timestamps['invalid_cache'] == 'true')
				$data['tracks'] = self::add_timestamps($data['tracks']);
		}
		
		return $data;
	}
	
	// Returns the tracks if they're cached, otherwise null
	public static function get_cached_tracks( $url, $episode_limit ) {
		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'tracks from feed url',
				'url' => $url,
				'episode_limit' => $episode_limit ) );
		return get_transient( $transient_name );
	}

	/**
	 * Called by SPP_Core::ajax_get_tracks, specifically to retrieve SoundCloud tracks
	 * @param  string 	$url 		URL of SoundCloud feed
	 * @return array 	$tracks		Array of tracks
	 */
	public static function get_soundcloud_tracks( $url, $episode_limit ) {
		
		$tracks = array();
		$api_options = get_option( 'spp_player_soundcloud', array( 'consumer_key' => '' ) );
		$api_consumer_key = isset( $api_options['consumer_key'] ) ? $api_options['consumer_key'] : '';
		if( $api_consumer_key == '' ) {
			$api_consumer_key = 'b38b3f6ee1cdb01e911c4d393c1f2f6e';
		}

		// Determine if it's a feed URL
		if( strpos( $url, '/sets/' ) === false ) {
			
			$user_id = '';

			$url_prof = SPP_Core::SPP_SOUNDCLOUD_API_URL . '/resolve?url=' . urlencode( $url ) . '&format=json&consumer_key=' . $api_consumer_key;
			$transient = 'spp_cachep_' . substr( preg_replace("/[^a-zA-Z0-9]/", '', md5( $url_prof ) ), -32 );
			
			if(  false === ( $profile = get_transient( $transient ) )  ) {

				$response = wp_remote_get( $url_prof );
				if( !is_wp_error( $response ) && ( $response['response']['code'] < 400 ) ) {

					$profile = json_decode( $response['body'] );

					if ( !empty ( $profile  ) && isset( $profile->id ) )
							set_transient( $transient, $profile, 5 * MINUTE_IN_SECONDS );

				}

			}

			$user_id = $profile->id;
			$track_count = $profile->track_count;

			if ( !is_numeric( $track_count ) || $track_count <= 0 )
				$track_count = 1;

			$offset = 0;
			$limit = 200;
			$tracks_arr = array();
			
			// Limit the number of episodes
			if( $episode_limit > 0 && $episode_limit < $track_count ) {
				$track_count = $episode_limit;
				$limit = $episode_limit;
			}

			$transient = 'spp_caches_' . substr( preg_replace("/[^a-zA-Z0-9]/", '', SPP_Core::VERSION . $url . (string)$track_count ), -32 );

			if(  false === ( $tracks = get_transient ( $transient ) ) ) {

				$url = SPP_Core::SPP_SOUNDCLOUD_API_URL . '/users/' . $user_id . '/tracks?format=json&client_id=' . $api_consumer_key . '&limit=' . $limit .'&linked_partitioning=1';

				while ( $track_count > $offset ) {

						$response = wp_remote_get( $url );
						if( !is_wp_error( $response ) && ( $response['response']['code'] < 400 ) ) {

							$json_obj = json_decode( $response['body'] );
							$tracks_arr[] = json_encode( $json_obj->collection );

							if ( empty( $json_obj->next_href ) )
								break;
							
							$url =  $json_obj->next_href; 
							
						}

					$offset += 200;

				}

				if ( is_array( $tracks_arr ) && !empty( $tracks_arr ) ) {

					if ( empty($tracks) && ( count( $tracks_arr ) == 1 ) ) {
							$tracks = json_decode( $tracks_arr[0] );
					}

					else {
							
						foreach($tracks_arr as $val) {

							if ( empty($tracks) ) {
								$tracks = $val;
							}
							else {
									if ( is_array( $tracks ) )
										$tracks = array_merge( $tracks, json_decode( $val, true ) ); 
									else
										$tracks = array_merge( json_decode( $tracks, true ), json_decode( $val, true ) ); 
							}
						}

						$tracks = json_decode( json_encode( $tracks ) );

					}

					if ( !empty ( $tracks ) )
						set_transient( $transient, $tracks , 4 * HOUR_IN_SECONDS );
				}
			}

		// Or if it's a profile URL
		} else {

			$url = SPP_Core::SPP_SOUNDCLOUD_API_URL . '/resolve?url=' . urlencode( $url ) . '&format=json&consumer_key=' . $api_consumer_key;
			$transient = 'spp_cachesu_' . substr( preg_replace("/[^a-zA-Z0-9]/", '', md5( $url ) . (string)$track_count ), -32 );
			
			if(  false === ( $tracks = get_transient( $transient ) )  ) {

				$response = wp_remote_get( $url );
				if( !is_wp_error( $response ) && ( $response['response']['code'] < 400 ) ) {

					$playlist = json_decode( $response['body'] );
					$tracks = $playlist->tracks;
					
					// Limit the number of episodes
					if( $episode_limit > 0 && count( $tracks ) > $episode_limit ) {
						$tracks = array_slice( $tracks, 0, $episode_limit );
					}

					if ( !empty ( $tracks ) )
						set_transient( $transient, $tracks , 5 * MINUTE_IN_SECONDS );

				}

			}

		}

		if ( !empty( $tracks ) ) {
			// Linkify and truncate the show notes for display
			foreach( $tracks as $track ) {
				$track->description = self::linkify_show_notes( $track->description );
				$track->truncated_show_notes_140 = self::truncate_show_notes( $track->description, 140, 10 );
				$track->truncated_show_notes_280 = self::truncate_show_notes( $track->description, 280, 10 );
				$track->truncated_show_notes_420 = self::truncate_show_notes( $track->description, 420, 10 );
				$track->truncated_show_notes_560 = self::truncate_show_notes( $track->description, 560, 10 );
				$track->truncated_show_notes_700 = self::truncate_show_notes( $track->description, 700, 10 );
			}
			return $tracks;
		}
		else
		{
			for( $track_count = 0; $track_count < 10; ++$track_count ) {
				$transient = 'spp_caches_' . substr( preg_replace("/[^a-zA-Z0-9]/", '', SPP_Core::VERSION . $url . substr( $track_count, -1 ) ), -32 );
				$tracks = null;
				if( ( $tracks = get_transient ( $transient ) ) && !empty( $tracks ) ) {
					return $tracks;
				}
			}
			return null;
		}
	}
	
	// Changes to fetch_feed
	public static $spoof_user_agent = false;
	public static function add_fetch_feed_options($feed) {
		
		$url = $feed->feed_url;
		
		// extend for slow feed generation/hosts
		$feed->set_timeout(15);

		// If the user has cleared the cache, it was marked in this transient
		if( get_transient( 'spp_cache_clear_simplepie' ) == true ) {
			$feed->enable_cache( false );
		} else {
			$feed->set_cache_duration( 5 * MINUTE_IN_SECONDS );
		}

		// The Wordpress 4.5 update broke lots of feeds by setting the type
		// to application/octet-stream instead of application/rss+xml.
		// I don't know the root cause, but I do know the fix: force_feed.
		$feed->force_feed(true);
		
		// Squarespace returns a 403 Forbidden with the default SimplePie user agent.
		if( self::$spoof_user_agent || strpos( $url, 'squarespace.com' ) !== false ) {
			$feed->set_useragent( 'iTunes/9.1.1' );
		}
		
		// Set the parser to a class that accepts more feeds
		require_once( SPP_PLUGIN_BASE . 'classes/simplepie-ext.php' );
		$feed->set_parser_class( 'SPP_SimplePie_Parser_Ext' );
	}

	/**
	 * Retrieve track data from RSS feeds
	 * 
	 * @param  string $url URL of the RSS feed
	 * @return array 	Data for all of the tracks
	 */
	public static function get_rss_tracks( $url, $episode_limit, $get_ep_num = false ) {

		add_action('wp_feed_options', array('SPP_Ajax_Feed', 'add_fetch_feed_options'));
		$rss = fetch_feed( $url );

		if( is_wp_error( $rss ) ) {
			// Try again with iTunes user agent
			self::$spoof_user_agent = true;
			$rss = fetch_feed( $url );
			self::$spoof_user_agent = false;
			if( is_wp_error( $rss ) ) {
				remove_action('wp_feed_options', array('SPP_Ajax_Feed', 'add_fetch_feed_options'));
				return $rss;
			}
		}
		// Play nice with anything else that might be using fetch_feed
		remove_action('wp_feed_options', array('SPP_Ajax_Feed', 'add_fetch_feed_options'));

		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'xml from feed url',
				'url' => $url ) );
		$data = get_transient( $transient_name );
		
		// See if RAW XML is already available from SimplePie. Indicates when feed new/changed too.
		if ( $rss->get_raw_data() ) {
				$data = $rss->get_raw_data();
				set_transient( $transient_name, $data, $timeout );
		}
		else {	
			if( false === ( $data = get_transient($transient_name) ) ) {
				$data = wp_remote_retrieve_body ( wp_remote_get( $url ) );

				if ( !empty ( $data ) && !is_wp_error( $data )  )
					set_transient( $transient_name, $data, $timeout );
			}
		}
		
		// Cut whitespace from start of raw data
		$whitespace = strspn($data, "\x09\x0A\x0D\x20");
		if( $whitespace > 0 ) {
			$data = substr( $data, $whitespace );
		}

		// Get the number of episodes, image URL, and show name.  Prior to 2.3.28, we used SimpleXML
		// always.  Some people don't have this library, so we have the SimplePie functions as a
		// backup.  They should be the primary and only way, but I'm leaving in the old way just in
		// case it's different for some people.  Probably remove the SimpleXML part in the future.
		if( function_exists( 'simplexml_load_string' ) && function_exists( 'simplexml_load_file' ) ) {
			if ( !empty ( $data ) )
				$xml = simplexml_load_string( $data );	// URL file-access is disabled? HS1438

			if ( empty( $xml ) || empty( $data ) )
				$xml = simplexml_load_file( $url ); 	// Raw xml so we can fetch other data
			$channel = $xml->channel;
			$items = $channel->item;
			$episode_number = count( $items );
			$image_url = (string) $channel->image->url;
			$show_name = (string) $channel->title;;
		} else {
			$episode_number = $rss->get_item_quantity();
			$image_url = $rss->get_image_url();
			$show_name = $rss->get_title();
		}

		$tracks = array();
		$i = 0;

		if( !is_wp_error( $rss ) ) {
		
			require_once( SPP_PLUGIN_BASE . 'classes/download.php' );
			$all_timestamps = get_option('spp_player_timestamps');

			foreach ( $rss->get_items() as $item) {
				
				$enclosures = $item->get_enclosures();
				$enclosure = $item->get_enclosure();

				foreach( $enclosures as $enc ) {
					if( $enc->handler == 'mp3' ) {
						$enclosure = $enc;
					}
				}

	 			$track = new StdClass;
				$date = new DateTime( $item->get_date() );

				$track->id = $i;
				$track->title = $item->get_title();
				
				// Set the show notes based on user's selected option
				$advanced_options = get_option( 'spp_player_advanced');
				$show_notes = isset( $advanced_options['show_notes'] )
							? $advanced_options['show_notes'] : "description";
				switch ($show_notes) {
					case "description":
						$description = $item->get_description();
						break;
					case "content":
						$description = $item->get_content();
						break;
					case "itunes_summary":
						$description = $item->get_item_tags(
								SIMPLEPIE_NAMESPACE_ITUNES, 'summary');
						$description = strip_shortcodes($description[0]["data"]);
						break;
					case "itunes_subtitle":
						$description = $item->get_item_tags(
								SIMPLEPIE_NAMESPACE_ITUNES, 'subtitle');
						$description = strip_shortcodes($description[0]["data"]);
						break;
					default:
						$description = $item->get_description();
						break;
				}
				$description = strip_tags( $description, '<p><a><br>' );
				$description = self::scrub_html( $description );
				$description = self::linkify_show_notes( $description );
				$track->description = $description;
				
				// If we have keywords, set those.
				// Use the Soundcloud style (with quotes) for historical reasons
				$tags = $enclosure->get_keywords();
				$track->tag_list = "";
				if( is_array( $tags ) ) {
					foreach( $tags as $tag ) {
						if( strlen( $tag ) > 0 ) {
							$track->tag_list = $track->tag_list . ' "' . $tag . '" ';
						}
					}
				}
				
				// Truncate the show notes for display
				$track->truncated_show_notes_140 = self::truncate_show_notes( $track->description, 140, 10 );
				$track->truncated_show_notes_280 = self::truncate_show_notes( $track->description, 280, 10 );
				$track->truncated_show_notes_420 = self::truncate_show_notes( $track->description, 420, 10 );
				$track->truncated_show_notes_560 = self::truncate_show_notes( $track->description, 560, 10 );
				$track->truncated_show_notes_700 = self::truncate_show_notes( $track->description, 700, 10 );
				
				// HS4058 and 4428: The string '?#' was being appended to the enclosure's link
				if( substr( $enclosure->link, -2) == "?#" ) {
					$enclosure->link = substr( $enclosure->link, 0, -2 );
				}

				$item_link = $item->get_link();
				$track->permalink_url = is_null($item_link) ? "" : $item_link;
				$track->uri = is_null($item_link) ? "" : $item_link;
				$track->stream_url = $enclosure->link;
				$track->download_url = $enclosure->link;
				$track->duration = $enclosure->duration;
				$track->created_at = $date->format( 'Y/m/d h:i:s O' );
				$track->artwork_url = $image_url;
				
				if ( stripos( $track->artwork_url, "http://i1.sndcdn.com" ) !== FALSE )
					$track->artwork_url = str_replace( "http://i1.sndcdn.com", "//i1.sndcdn.com", $track->artwork_url );
				
				if( $track->artwork_url == '' || empty( $track->artwork_url ) ) {

					if( is_array( $enclosure->thumbnails ) && !empty( $enclosure->thumbnails[0] ) && $enclosure->thumbnails[0] != '' ) {
						
						$track->artwork_url = $enclosure->thumbnails[0];

					} else {

						$itunes_image = $rss->get_channel_tags( SIMPLEPIE_NAMESPACE_ITUNES, 'image' );

						if( is_array( $itunes_image ) ) {
							$track->artwork_url = $itunes_image[0]['attribs']['']['href'];	
						}

					}					

				}
				
				// For normal SPP purposes, the episode number is set below.  It's just the count
				// in the feed.  Here, we set based on the itunes:episode, which is used for host migration.
				if ($get_ep_num) {
					$raw_epnum = $item->get_item_tags(
							SIMPLEPIE_NAMESPACE_ITUNES, 'episode');
					$epnum = $raw_epnum[0]['data'];
					if (!is_null($epnum))
						$track->itunes_episode_number = $epnum;
				}
				
				$track->show_name = $show_name;
				$track->episode_number = $episode_number;
				$track->download_id = SPP_Download::save_download_id($enclosure->link);

				if( !empty( $track->stream_url ) && $track->stream_url != '' ) {
					$tracks[] = $track;	
					$episode_number--;
				} else {}

				$i++;
				
				// Limit the number of episodes
				if( $episode_limit > 0 && $i >= $episode_limit )
					break;
				
			}
		}

		return $tracks;
		
	}

	/**
	 * Scrub the HTML passed in for any attributes we don't want, like class, style, and ID
	 * 
	 * @param  string $input Can be any valid HTML text
	 * @return string $output Scrubbed HTML output, minus the doctype
	 */
	public static function scrub_html( $input ) {

		if( !extension_loaded( 'libxml' ) || !extension_loaded( 'dom' ) || empty( $input ) )
			return $input;

		if (function_exists("mb_convert_encoding")) {
			require_once( dirname( __FILE__ ) . '/vendor/SmartDOMDocument.php' );
			$dom = new SmartDOMDocument;
		} else {
			$dom = new DOMDocument;
		}
		
		$dom->loadHTML( $input );

		$xpath = new DOMXPath( $dom );
		$nodes = $xpath->query('//@*');

		foreach ($nodes as $node) {
			if( $node->nodeName == 'style' || $node->nodeName == 'class' || $node->nodeName == 'id' ) {
			    $node->parentNode->removeAttribute($node->nodeName);
			}
		}

		$links = $dom->getElementsByTagName('a');

		foreach ( $links as $item ) {
			$item->setAttribute('target','_blank');  
		}
		
		$output = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML() ); // Extract w/o the doctype and html/body tags
		
		return $output;

	}
	
	/**
	 * Truncate the show notes to something shorter.
	 *
	 * @param  string $input     The original show notes, stripped of all tags except <p> and <a>
	 * @param  int    $maxLength The optimal length after truncation
	 * @param  int    $variance  How far away from optimal the truncation is allowed to be
	 * @return string $output    The truncated show notes
	 */
	public static function truncate_show_notes( $input, $maxLength, $variance ) {
	
		$input_len = strlen( $input );
		$output = "";
		$output_len = 0;  // The number of visible characters in the output. (Don't count HTML tags.)
		$in_anchor = false;
		$i = 0;
		
		while( $i < $input_len ) {
			// If we're starting an HTML tag
			if( $input[$i] === '<' ) {
				if( $i + 2 > $input_len ) {
					// Something is amiss.  Return now to avoid crashing.
					return $output;
				}
				// If we're starting an anchor tag
				if( $input[ $i+1 ] === 'a' ) {
					$in_anchor = true;
				// If we're ending an anchor tag
				} else if( substr( $input, $i+1, 2 ) === '/a' ) {
					$in_anchor = false;
				}
				// Add everything within <> to the output, but don't count it as visible
				while( $i < $input_len && $input[$i] != '>' ) {
					$output .= $input[$i];
					$i++;
				}
				// Also include the closing '>'
				if( $i < $input_len ) {
					$output .= $input[$i];
					$i++;
				}
			
			// Otherwise, we're not starting an HTML tag
			} else {
			
				// If we're starting an HTML entity (like &amp;)
				if( $input[$i] === '&' ) {
					// Add everything from here to ';' to the output, but don't count it as visible
					while( $i < $input_len && $input[$i] != ';' ) {
						$output .= $input[$i];
						$i++;
					}
					// Also include the closing ';'
					if( $i < $input_len ) {
						$output .= $input[$i];
						$i++;
					}
				} else {
					// It's a regular character.  Add it to the output.
					$output .= $input[$i];
					$i++;
				}
				// Count one character added to the output
				$output_len++;
				
				// If we've reached our window to end the string
				if( $output_len > $maxLength - $variance ) {
					// Go until the next ' ', or the next '<', or until the hard max
					while( $i < $input_len ) {
						if( $input[$i] === '<' || $input[$i] === ' '
						        || $output_len >= $maxLength + $variance ) {
							break;
						} else {
							// If we're starting an HTML entity (like &amp;)
							if( $input[$i] === '&' ) {
								// Add everything from here to ';' to the output, but don't count it as visible
								while( $i < $input_len && $input[$i] != ';' ) {
									$output .= $input[$i];
									$i++;
								}
								// Also include the closing ';'
								if( $i < $input_len ) {
									$output .= $input[$i];
									$i++;
								}
							} else {
								// It's a regular character.  Add it to the output.
								$output .= $input[$i];
								$i++;
							}
							// Count one character added to the output
							$output_len++;
						}
					}
					// Close off open anchors
					if( $in_anchor ) {
						$output .= "</a>";
					}
					break;
				}
			}
		}

		// Remove any trailing '</p>' so our "more" button goes in the right place
		if( substr( $output, -4 ) == "</p>" ) {
			$output = substr( $output, 0, -4 );
		}
		return $output;
	}
	
	/**
	 * Replace raw URLs with HTML links.  Uses the Autolink library.
	 *
	 * @param $notes The input text
	 */
	public static function linkify_show_notes( $notes ) {
		require_once( SPP_PLUGIN_BASE . 'classes/vendor/lib_autolink-master/lib_autolink.php' );
		return autolink( $notes, 30, ' target="_blank"' );
	}
	
	public static function add_timestamps($tracks) {
		$timestamps = get_option('spp_player_timestamps');
		
		// Timestamp cache will now be valid, whether or not there are timestamps set.
		// If there aren't any, we just mark the cache valid and return
		if (!$timestamps || !isset($timestamps['stamps'])) {
			$timestamps = array('invalid_cache' => 'false');
			update_option('spp_player_timestamps', $timestamps);
			return $tracks;
		} else {
			$timestamps['invalid_cache'] = 'false';
			update_option('spp_player_timestamps', $timestamps);
		}
		
		foreach ($tracks as $track) {
			$ts_url = $track->stream_url;
			$ts_url = preg_replace('/^https?:\/\//', '', $track->stream_url);
			$ts_url = preg_replace('/\?.*$/', '', $ts_url);
			if (isset($timestamps['stamps'][$ts_url])) {
				$track->timestamps = $timestamps['stamps'][$ts_url];
			}
		}
		
		return $tracks;
	}

}
