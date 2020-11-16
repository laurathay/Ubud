<?php
class SPP_Dynamic_Css {
	
	private static $instance = null;
	private static function get_instance() {
		if ( null == self::$instance )
			self::$instance = new self;
		return self::$instance;
	}
	
	private $color_arrays = array();
	
	public static function add_color_array( $color_array ) {
		$c = self::get_instance();
		$c->color_arrays[] = $color_array;
		add_action( 'wp_footer', array( &$c, 'add_dynamic_css' ), 100 );
	}
	
	private static function parse_dynamic_css_fragment( $expr, $color_array ) {
	
		require_once( SPP_PLUGIN_BASE . 'classes/utils/color.php' );
		preg_match( '/\s*(\$?\w+),?\s*([\d\.]*)/', $expr, $matches );
		if( $matches[1] === '$color'
				|| $matches[1] === '$background_color'
				|| $matches[1] === '$play_pause_color'
				|| $matches[1] === '$email_button_bg_color'
				|| $matches[1] === '$email_button_text_color') {
			if( array_key_exists( $matches[1], $color_array ) ) {
				$color = $color_array[$matches[1]];
			} else {
				$color = str_replace( '#', '', SPP_Core::SPP_DEFAULT_PLAYER_COLOR);
			}
			if( empty( $matches[2] ) ) {
				return $color;
			} else {
				// $matches[2] is the tint value.
				$tinted = SPP_Utils_Color::tint_hex( $color, $matches[2] );
				$tinted = str_replace( '#', '', $tinted);
				return $tinted;
			}
		} else if( $matches[1] === '$white_controls_url') {
			return 'url(' . SPP_ASSETS_URL . 'images/controls-white.png)';
		} else if( $matches[1] === '$black_controls_url') {
			return 'url(' . SPP_ASSETS_URL . 'images/controls-black.png)';
		} else if( $matches[1] === '$white_subdl_url') {
			return 'url(' . SPP_ASSETS_URL . 'images/sub-dl-white.png)';
		} else if( $matches[1] === '$black_subdl_url') {
			return 'url(' . SPP_ASSETS_URL . 'images/sub-dl-black.png)';
		} else if ( $matches[1] === '$importantStr') {
			return SPP_Core::get_css_important_str();
		} else if ( $matches[1] === '$z_index') {
			return SPP_Core::get_sticky_z_index();
		} else {
			return trim($matches[0]);
		}
	}
	
	private function callback_for_generate_dynamic_css( $matches ) {
		$color_array = $this->color_array_for_generate_dynamic_css;
		$brightness = $this->brightness_for_generate_dynamic_css;
		if( $brightness < 0.2 ) {
			$expr = $matches[1];
		} else if ($brightness > 0.6 ) {
			$expr = $matches[3];
		} else {
			$expr = $matches[2];
		}
		return self::parse_dynamic_css_fragment( $expr, $color_array );
	}
	
	private function generate_dynamic_css( $color_array ) {
		
		require_once( SPP_PLUGIN_BASE . 'classes/utils/color.php' );
		$css = file_get_contents(SPP_PLUGIN_BASE . 'classes/dynamic.css');
		
		// Replace semicolon-separated parenthesized expressions
		$this->color_array_for_generate_dynamic_css = $color_array;
		$this->brightness_for_generate_dynamic_css = SPP_Utils_Color::get_brightness( $color_array['$color'] );
		$css = preg_replace_callback(
				'/\(\((.*?);(.*?);(.*?)\)\)/',
				array( $this, 'callback_for_generate_dynamic_css' ),
				$css );
		// Replace pipe-separated parenthesized expressions
		$this->brightness_for_generate_dynamic_css = SPP_Utils_Color::get_brightness( $color_array['$background_color'] );
		$css = preg_replace_callback(
				'/\(\((.*?)\|(.*?)\|(.*?)\)\)/',
				array( $this, 'callback_for_generate_dynamic_css' ),
				$css );
		// Replace other parenthesized expressions
		$this->brightness_for_generate_dynamic_css = 0;
		$css = preg_replace_callback(
				'/\(\((.*?)\)\)/',
				array( $this, 'callback_for_generate_dynamic_css' ),
				$css );
		// Remove comments
		$css = preg_replace( '/\/\*.*?\*\//m', '', $css );
		return $css;
	}

	public function add_dynamic_css() {
		
		foreach( $this->color_arrays as $color_array ) {
			
			// The generated dynamic CSS will be stored in filename
			$filename = SPP_ASSETS_PATH . 'css/custom-' . SPP_Core::VERSION;
			foreach( $color_array as &$color ) {
				$color = str_replace( '#', '', $color );
				$filename = $filename . '-' . $color;
			}
			if( SPP_Core::get_css_important_str() === " !important" ) {
				$filename = $filename . '-i';
			}
			$filename = $filename . '.css';
			
			// If we have already included this CSS on the page, we're done
			static $included_already = array();
			if( in_array( $filename, $included_already ) ) {
				continue;
			}
		
			// Get the CSS from the file, if it exists.  Otherwise, generate and save it
			// Starting in 2.0, we don't save it in a file.  It's quick enough to compute.
			$css = self::generate_dynamic_css( $color_array );

			// Put the generated CSS onto the page
			echo '<style>' . "\n\t";
			echo '/* Smart Podcast Player custom styles for color ' . $color_array['$color'] . " */\n";
			echo $css;
			echo '</style>' . "\n";
			
			// Make a note that we've put this CSS on the page
			$included_already[] = $filename;
		}
		
	}
}
