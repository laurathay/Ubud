/*global bayleafImageUploadText */
( function( $ ){
	var file_frame,
		$document = $( document );

	$document.on( 'click', '.bayleaf-widget-img-uploader', function( event ){
		var _this = $(this);
		event.preventDefault();

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: bayleafImageUploadText.uploader_title,
			button: {
				text: bayleafImageUploadText.uploader_button_text,
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment  = file_frame.state().get('selection').first().toJSON(),
				imgUrl      = attachment.url,
				imgId       = attachment.id,
				featuredImg = document.createElement("img");
			
			featuredImg.src       = imgUrl;
			featuredImg.className = 'custom-widget-thumbnail';
			_this.html( featuredImg );
			_this.addClass( 'has-image' );
			_this.nextAll( '.bayleaf-widget-img-id' ).val( imgId ).trigger('change');
			_this.nextAll( '.bayleaf-widget-img-instruct, .bayleaf-widget-img-remover' ).removeClass( 'bayleaf-hidden' );
		});

		// Finally, open the modal
		file_frame.open();
	});

	$document.on( 'click', '.bayleaf-widget-img-remover', function( event ){
		event.preventDefault();
		$( this ).prevAll('.bayleaf-widget-img-uploader').html(bayleafImageUploadText.set_featured_img).removeClass( 'has-image' );
		$( this ).prev( '.bayleaf-widget-img-instruct' ).addClass( 'bayleaf-hidden' );
		$( this ).next( '.bayleaf-widget-img-id' ).val( '' ).trigger('change');
		$( this ).addClass( 'bayleaf-hidden' );
	});

	$document.on( 'click', '.widget-options-title', function( event ) {
		var _this = $( this );
		_this.next( '.widget-options-content' ).slideToggle('fast');
		_this.toggleClass( 'toggle-active' );
	});
} ) ( jQuery );
