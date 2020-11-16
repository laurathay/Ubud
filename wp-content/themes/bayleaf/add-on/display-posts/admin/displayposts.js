( function( $ ) {
	// Add event triggers to the show/hide items.
	$('#widgets-right').on('change', 'select.bayleaf-post-type', function() {
		showPosttypeContent( $(this) );
	});

	$('#widgets-right').on('change', 'select.bayleaf-taxonomy', function() {
		showTerms( $(this) );
	});

	$( document ).on( 'click', '.dp-settings-toggle', function( event ) {
		var _this = $( this );
		event.preventDefault();
		_this.next( '.dp-settings-content' ).slideToggle('fast');
		_this.toggleClass( 'toggle-active' );
	});
	
	function showPosttypeContent( pType ) {
		var postType  = pType.val(),
			parent    = pType.parent(),
			toggle    = parent.nextAll('.dp-settings-toggle'),
			wrapper   = parent.nextAll('.dp-settings-content'),
			taxSelec  = wrapper.find( 'select.dp-taxonomy' );

		if (postType) {
			toggle.show();
			if ('page' === postType) {
				wrapper.find('.page-panel').show();
				wrapper.find('.post-panel').hide();
			} else {
				wrapper.find('.page-panel, .terms-panel').hide();
				wrapper.find('.post-panel').show();
				taxSelec.find( 'option' ).hide();
				taxSelec.find( '.' + postType ).show();
				taxSelec.find( '.always-visible' ).show();
				taxSelec.val('');
			}
			if ('post' !== postType) {
				wrapper.addClass('not-post');
			} else {
				wrapper.removeClass('not-post');
			}
		} else {
			toggle.hide();
			wrapper.hide();
		}
	}

	function showTerms( taxonomy ) {
		if ( taxonomy.val() ) {
			taxonomy.parent().next('.terms-panel').show();
			taxonomy.parent().next('.terms-panel').find( '.terms-checklist li' ).hide();
			taxonomy.parent().next('.terms-panel').find( '.terms-checklist .' + taxonomy.val() ).show();
		} else {
			taxonomy.parent().next('.terms-panel').hide();
		}
	}
}( jQuery ) );
