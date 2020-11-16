<?php

class SPP_Transients {
	
	public static function spp_transient_info( $args ) {
		if( !isset( $args['purpose'] ) ) {
			return array( null, 1 );
		}
		
		$transient_name = null;
		$timeout = 1;
		if( $args['purpose'] === 'tracks from feed url' ) {
			$settings = get_option( 'spp_player_advanced' );
			$val = isset( $settings['cache_timeout'] ) ? $settings['cache_timeout'] : '15';
			if ( $val > 1440 || $val < 5 || !is_numeric( $val ) )
				$val = 15;
			$timeout = $val * MINUTE_IN_SECONDS;
			if( isset( $args['url'] ) && isset( $args['episode_limit'] ) ) {
				$transient_name = 'spp_cachea_' . md5(
						SPP_Core::VERSION . $args['url'] . (string) $args['episode_limit'] );
			}
		} else if( $args['purpose'] === 'jsobj from uid' ) {
			$timeout = 5 * MINUTE_IN_SECONDS;
			if( isset( $args['uid'] ) ) {
				$transient_name = 'spp_cacheuid_' . md5( SPP_Core::VERSION . $args['uid'] );
			}
		} else if( $args['purpose'] === 'xml from feed url' ) {
			$timeout = 5 * MINUTE_IN_SECONDS;
			if( isset( $args['url'] ) ) {
				$transient_name = 'spp_cachesx_' . md5( SPP_Core::VERSION . $args['url'] );
			}
		} else if( $args['purpose'] === 'track data from track url' ) {
			$timeout = YEAR_IN_SECONDS;
			if( isset( $args['url'] ) ) {
				$transient_name = 'spp_cachem_' . md5( SPP_Core::VERSION . $args['url'] );
			}
		} else if( $args['purpose'] === 'fallback response from track url' ) {
			$timeout = HOUR_IN_SECONDS;
			if( isset( $args['url'] ) ) {
				$transient_name = 'spp_cachef_' . md5( SPP_Core::VERSION . $args['url'] );
			}
		} else if( $args['purpose'] === 'soundcloud data from track url' ) {
			$timeout = YEAR_IN_SECONDS;
			if( isset( $args['url'] ) ) {
				$transient_name = 'spp_cachet_' . md5( SPP_Core::VERSION . $args['url'] );
			}
		}
		// Partial list of other transients:
		//    spp_license_check
		//    spp_cachep_* Soundcloud "sets"
		//    spp_caches_* Soundcloud tracks  (all four in get_soundcloud_tracks)
		//    spp_caches_* Soundcloud fallback?
		//    spp_cacheu_* Soundcloud profile

		
		return array( $transient_name, $timeout );
	}
}
