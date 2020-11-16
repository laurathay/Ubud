<?php

class SPP_MP3 {
	
	const DEFAULT_BUFFER = 786432; // 768 kb

	public static function get_data( $url_in ) {

		$method = self::get_method( $url_in );

		$data = array();

		$url = self::safe_spp_url( $url_in );

		if ( empty( $url ) )
			return $data;
				
		if( $method == 'curl' ) {
			$data = self::get_curl_data( $url );
		}

		if( $method == 'fopen' ) {
			$data = self::get_fopen_data( $url );
		}
		
		return $data;

	}

	public static function safe_spp_url ( $url = null ) {

		/*if ( preg_match( '/\.php$/i', $url ) )
		return false;*/

		if( strpos( strtolower( $url ), 'http' ) !== 0 ) {
			$clean = trailingslashit( home_url() ). $url;
			$url = $clean;
		}
		return esc_url_raw( str_replace('../', '', $url ), array('http','https') );

	}

	private static function get_method( $url_in ) {
		
		// Buzzsprout was returning a 403 when accessed by Curl.  I don't know why.
		if (strpos($url_in, 'http://buzzsprout.com') === 0
				|| strpos($url_in, 'https://buzzsprout.com') === 0
				|| strpos($url_in, 'http://www.buzzsprout.com') === 0
				|| strpos($url_in, 'https://www.buzzsprout.com') === 0) {
			if( ini_get( 'allow_url_fopen' ) ) {
				return 'fopen';
			}
		}
	
		// Curl is the preferred method.  In order to use it, it has to exist,
		// be above version 7.19.4, and PHP has to be greater than version
		// 5.4.7.  Both version restrictions have to do with bugs in
		// curl_copy_handle; the PHP bug is #62839.
		if ( function_exists( 'curl_version' ) ) {
			$version = curl_version();
			if( $version['version_number'] > 0x071304 ) {
				if( version_compare( phpversion(), '5.4.7', '>' ) ) {
					return 'curl';
				}
			}
		}
		
		if( ini_get( 'allow_url_fopen' ) ) {
			return 'fopen';
		}
		
		return false;
			
	}

	private static function get_curl_data( $url ) {

		$data = array();
		$file = self::get_curl_file_segment( $url );
		
		if( $file ) {
			$data = self::get_id3_data( $file );
			unlink( $file );
		}

		return $data;

	}

	private static function get_fopen_data( $url ) {

		$data = array();
		$file = self::get_fopen_file_segment( $url );

		if( $file ) {
			$data = self::get_id3_data( $file );
			unlink( $file );
		}

		return $data;

	}

	private static function get_curl_file_segment( $url ) {

	    $file = tempnam( SPP_PLUGIN_BASE . 'classes/tmp/', "mp3_");

	    if( !is_writable( $file ) )
	    	return false;

		$fh = fopen( $file, 'w+' );

		$ch = curl_init();
		
		// If we're running under Windows, then PHP's maximum script
		// execution time includes OS calls.  This is bad news for
		// running downloads, as it will likely time out.  Here, we
		// reduce our curl timeout to something less than PHP will give
		// us. 8 seconds should be plenty to run the rest of the script.
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$timeout = ini_get('max_execution_time') - 8;
		} else {
			$timeout = 60;
		};

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt( $ch, CURLOPT_HEADER, 0 ); 
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_FILE, $fh );

		$max_redirects = 10;

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		$mr = $max_redirects;
		if ($mr > 0) { 
			$newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			
			$rcurl = curl_copy_handle($ch);
			curl_setopt($rcurl, CURLOPT_HEADER, true);
			curl_setopt($rcurl, CURLOPT_NOBODY, true);
			curl_setopt($rcurl, CURLOPT_FORBID_REUSE, false);
			curl_setopt($rcurl, CURLOPT_RETURNTRANSFER, true);

			do {
				curl_setopt($rcurl, CURLOPT_URL, $newurl);
				$header = curl_exec($rcurl);
				if (curl_errno($rcurl)) {
					$code = 0;
				} else {
					$code = curl_getinfo($rcurl, CURLINFO_HTTP_CODE);
					if ($code == 301 || $code == 302) {
						preg_match('/[Ll]ocation:(.*?)\n/', $header, $matches);
						$newurl = trim(array_pop($matches));
					} else {
						$code = 0;
					}
				}
			} while ($code && --$mr);
			
			curl_close($rcurl);
			if ($mr > 0) {
				curl_setopt($ch, CURLOPT_URL, $newurl);
			}
			
			if($mr == 0 && $max_redirects > 0) {
				$response = false;
			} else {
				$response = curl_exec($ch);
			}
		}

		curl_close( $ch );
		
		fclose( $fh );
		
		return $file;

	}

	private static function get_fopen_file_segment( $url ) {

		$file_size = self::get_fopen_file_size( $url );

		$file = tempnam( SPP_PLUGIN_BASE . 'classes/tmp/', "mp3_");

		if( !is_writable( $file ) )
	    	return false;

	    $remote_file = fopen( $url, "r" );
		if( !$remote_file )
			return false;
		
	    $mp3 = fopen( $file, "a" );

	    while( !feof( $remote_file ) ) {

			fwrite( $mp3, fread( $remote_file, 4096 ) );
		    flush();
		    ob_flush();

		}

		fclose( $remote_file );
		fclose( $mp3 );

		return $file;

	}	

	/**
	 * Get size of the file 
	 * @param  $url
	 * @return number 	Size of the file
	 */
	private static function get_fopen_file_size( $url ) {

		$head = get_headers( $url, true );
		$file_size = $head['Content-Length'] ? $head['Content-Length'] : 0;

		if( is_array( $file_size ) ) {
			
			foreach( $file_size as $fs ) {
				if( $fs > 0 ) {
					$file_size = $fs;
				}
			}

			$file_size = !is_array( $file_size ) ? $file_size : 0;

		}
		
		return $file_size;
	
	}

	/**
	 * Retrieve the data of an MP3 file using the getID3 file
	 * 
	 * @param  filestream $file A File resource, must be MP3
	 * @return array       MP3 File Data
	 */
	private static function get_id3_data( $file ) {
		
		require_once( SPP_PLUGIN_BASE . '/includes/getid3/getid3.php' );
		$id3 = new getID3;
		$info = $id3->analyze( $file );
	    $data = array();
		
		// For MP3 files, artist and title are in id3v2 tags
	    getid3_lib::CopyTagsToComments( $info ); 
	    if( isset( $info['id3v2']['comments'] ) ) {
	    	foreach( $info['id3v2']['comments'] as $key => $tag ) {
			    	if( isset( $tag[0] ) && ( is_string( $tag[0] ) || is_numeric( $tag[0] ) ) )
    					$data[$key] = $tag[0];
    				else if( isset( $tag[0] ) )
    					$data[$key] = "";

	    	}
	    }
		
		// For M4A files, artist and title are within quicktime tags
		if( isset( $info['tags'] ) && isset( $info['tags']['quicktime'] ) ) {
			if( isset( $info['tags']['quicktime']['title'] ) ) {
				$data['title'] = $info['tags']['quicktime']['title'][0];
			}
			if( isset( $info['tags']['quicktime']['artist'] ) ) {
				$data['artist'] = $info['tags']['quicktime']['artist'][0];
			}
		}
		
		// If we don't have artist, title, or album, and ID3 set an error, throw that error.
		if( !isset( $data['artist'] ) && !isset( $data['title'] ) && !isset( $data['album'] )
				&& isset( $info[ 'error' ] ) ) {
			if( SPP_Core::debug_output() )
				trigger_error( implode( $info[ 'error' ] ) );
			return null;
		}

	    return $data;

	}

}
