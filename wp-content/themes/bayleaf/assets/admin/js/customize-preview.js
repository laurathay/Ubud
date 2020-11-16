// Make Theme Customizer preview reload changes asynchronously.
( function ( $ ) {
	var displayToggler = function ( value, selector, trueValue ) {
		if ( trueValue == value ) {
			$( selector ).css( {
				'position': 'absolute',
				'clip': 'rect(1px, 1px, 1px, 1px)'
			} );
		}
		else {
			$( selector ).css( {
				'position': 'static',
				'clip': 'auto'
			} );
		}
	}

	wp.customize( 'bayleaf_display_site_title', function ( value ) {
		value.bind( function ( to ) {
			displayToggler( to, '.site-title', '' );
		} );
	} );

	wp.customize( 'bayleaf_display_site_desc', function ( value ) {
		value.bind( function ( to ) {
			displayToggler( to, '.site-description', '' );
		} );
	} );

} )( jQuery );
