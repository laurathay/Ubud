( function() {
	// No button if it's the free version
	if (!SmartPodcastPlayerAdmin.licensed)
		return;
    tinymce.PluginManager.add( 'stp', function( editor, url ) {
	
		var fileHelperText = 'Use this option for episode posts, and anytime you'
		                   + ' don\'t want the episode to change when your feed is updated.'
						   + ' Enter the file URL below.';
		var feedHelperText = 'Use this option to always display the latest episode'
		                   + ' from your feed.  When your feed is updated, this player'
						   + ' will automatically update as well!'
						   + ' Enter the feed URL below.';
		var savedFileUrlInput = '';
		
		// Shorter name for the default options from the settings page
		if( typeof smart_podcast_player_user_settings === 'undefined' ) {
			return;
		}
		var def = smart_podcast_player_user_settings;
		
		// Escape characters within shortcode.  Code from mustache.js
		var entityMap = {
			"<": "&lt;",
			">": "&gt;",
			'"': "&amp;quot;",
			"]": "&amp;#93;",
		};
		function escapeHtml(string) {
			return String(string).replace(/[<>"\]]/g, function (s) {
				return entityMap[s];
			});
		}

        // Add a button that opens a window
        editor.addButton( 'stp_button_key', {

            icon: 'stp-icon',
			tooltip: 'Insert Fusebox Track Player shortcode',
            onclick: function() {
				
				var colorPrompt;
				var backgroundPrompt;
				var downloadablePrompt;
				var socialPrompt;
				
				// For the paid version, the color goes in a text box
				colorPrompt = {
					type: 'textbox',
					name: 'color',
					label: 'Highlight color (Hex) #',
					value: def.bg_color ? def.bg_color : ''
				};
				backgroundPrompt = {
					type: 'textbox',
					name: 'bg',
					label: 'Background color (Hex) #',
					value: def.stp_background_color ? def.stp_background_color : ''
				};
				// The paid version has the downloadable option
				downloadablePrompt = {
					type: 'listbox',
					name: 'download',
					label: 'Display download button',
					onselect: function(e) {},
					values: def.download && def.download == 'false' ?
								[{text: 'No', value: 'false'}, {text: 'Yes', value: 'true'}]
							:
								[{text: 'Yes', value: 'true'}, {text: 'No', value: 'false'}]
				};

				socialPrompt = {
					 type: 'listbox',
					 name: 'social',
					 label: 'Display social sharing buttons',
					 onselect: function(e) {},
					 values: [{text: 'Yes', value: 'on'}, {text: 'No', value: 'off'}]
				};
				socialOptionsPrompt = {
					type: 'container',
					name: 'social_opts',
					label: 'Social sharing buttons',
					tooltip: 'Choose up to seven social sharing sites.',
					html: '<table> \
							<tr> \
								<td><input type="checkbox" id="spp_socialopt_twitter" checked>Twitter</input></td> \
								<td><input type="checkbox" id="spp_socialopt_facebook" checked>Facebook</input></td> \
							</tr> \
							<tr> \
								<td><input type="checkbox" id="spp_socialopt_linkedin">LinkedIn</input></td> \
								<td><input type="checkbox" id="spp_socialopt_pinterest">Pinterest</input></td> \
							</tr> \
							<tr> \
								<td><input type="checkbox" id="spp_socialopt_email">Email</input></td> \
							</tr> \
							</table>'
				};
				
                // Open window
                editor.windowManager.open( {

                    title: 'Fusebox Track Player Shortcode',
                    body: [
						{
						    type: 'container',
							name: 'header_1',
							html: '<em>For displaying individual podcast episodes.</em><br><br>'
							    + '<p><em>Enter default values in Settings —> Fusebox Player.</em></p>'
							    + '<div class="spp-mce-hr"></div>'
								+ '<p style="font-weight: bold">1. Choose the episode to display</p>'
						},
						{
							type: 'listbox',
							name: 'fileOrFeed',
							label: '',
							onselect: function(e) {
								var textEl = document.getElementById('fileOrFeedHelperText');
								var urlEl = document.getElementById('stp_builder_url_textbox');
								if( this.state.data.value == "file") {
									textEl.innerText = fileHelperText;
									urlEl.value = savedFileUrlInput;
								} else {
									textEl.innerText = feedHelperText;
									savedFileUrlInput = urlEl.value;
									urlEl.value = def.url ? def.url : '';
								}
							},
							values: [{
								text: 'Play a specific episode of your podcast', value: 'file'
							    }, {
								text: "Always play the most recent episode in your podcast's feed", value: 'feed'
							}]
						},
						{
						    type: 'container',
							name: 'fileOrFeedHelper',
							html: '<div id="fileOrFeedHelperText" style="width: 400px; white-space: normal">'
							      + fileHelperText + '<br>&nbsp;</div>',
						},
	                    {
	                        type: 'textbox',
	                        name: 'url',
	                        label: 'URL',
							id: 'stp_builder_url_textbox'
	                    },
						{
						    type: 'container',
							name: 'header_2',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">2. Customize the appearance</p>'
						},
	                    colorPrompt,
						{
							type: 'listbox',
							name: 'background_type',
							label: 'Background type',
							onselect: function(e) {
								if( typeof jQuery !== 'undefined' ) {
									var bgPrompt = e.target.parent().parent().parent().next();
									var $bgPrompt = jQuery( '#' + bgPrompt._id );
									if( this.value() == 'color' )
										$bgPrompt.css( 'color', '#444' );
									else
										$bgPrompt.css( 'color', '#bbb' );
								}
							},
							values: [
								{text: 'Default', value: 'default'},
								{text: 'Blurred logo', value: 'blurred_logo'},
								{text: 'Color (select below)', value: 'color'},
							],
							value: def.stp_background ? def.stp_background : 'default'
						},
	                    backgroundPrompt,
	                    {
	                        type: 'textbox',
	                        name: 'image',
	                        label: 'Image URL',
							value: def.stp_image ? def.stp_image : ''
	                    },
						{
						    type: 'container',
							name: 'header_3',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">3. Customize the track information</p>'
						},
	                    {
	                        type: 'textbox',
	                        name: 'artist',
	                        label: 'Artist',
							value: def.artist_name ? def.artist_name : ''
	                    },
	                    {
	                        type: 'textbox',
	                        name: 'title',
	                        label: 'Title'
	                    },
						{
						    type: 'container',
							name: 'header_4',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">4. Customize the buttons</p>'
						},
	                    socialPrompt,
						socialOptionsPrompt,
	                    downloadablePrompt,
						{
							type: 'container',
							name: 'header_5',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">5. Customize your social sharing message</p>'
								+ '<p><em>&emsp;For more information about these options, click '
								+ '<a href="http://support.fusebox.fm/article/143-customizing-social-sharing"'
								+ ' target="_blank">here</a></em></p>'
						},
						{
							type: 'textbox',
							name: 'permalink',
							label: 'Permalink URL',
						},
						{
							type: 'container',
							name: 'permalink_help',
							html: "<em>&emsp;Leave blank to use this page's URL</em>",
						},
						{
							type: 'textbox',
							name: 'tweet_text',
							label: 'Custom message (Twitter only)',
						},
						{
							type: 'textbox',
							name: 'hashtag',
							label: 'Hashtags (Twitter only)',
						},
						{
							type: 'container',
							name: 'permalink_help',
							html: '<em>&emsp;Include #, separate multiple hashtags with commas</em>',
						},
						{
							type: 'textbox',
							name: 'twitter_username',
							label: 'Username to include (Twitter only)',
						},
	                ],
					buttons: [ 
						{ text: "Build Shortcode", subtype: 'primary', onclick: 'submit' },
						{ text: "Cancel", onclick: 'close' }
					],
                    onsubmit: function( e ) {

                    	var shortcode = '[fusebox_track_player';
						
						if( e.data.fileOrFeed == 'feed' )
							shortcode += '_latest';

                    	if( e.data.url != '' )
                    		shortcode += ' url="' + e.data.url + '" ';

                    	if( e.data.title != '' )
                    		shortcode += ' title="' + escapeHtml( e.data.title ) + '" ';

                    	if( e.data.artist != '' && ( !def.artist_name || def.artist_name != e.data.artist ) )
                    		shortcode += ' artist="' + escapeHtml( e.data.artist ) + '" ';

                    	if( e.data.image != '' && ( !def.stp_image || def.stp_image != e.data.image ) )
                    		shortcode += ' image="' + e.data.image + '" ';

						if( e.data.color != '' && ( !def.bg_color || def.bg_color != e.data.color ) )
							shortcode += ' color="' + e.data.color + '" ';

						if( !def.stp_background
								|| ( e.data.background_type != def.stp_background )
								|| ( e.data.background_type == 'color' && e.data.bg != def.stp_background_color ) ) {
							if( e.data.background_type == 'color' ) {
								shortcode += ' background="' + e.data.bg + '" ';
							} else {
								shortcode += ' background="' + e.data.background_type + '" ';
							}
						}

						if( def.download ) {
							if( e.data.download != def.download )
								shortcode += ' download="' + e.data.download + '" ';
						} else {
							if( e.data.download != 'true' )
								shortcode += ' download="' + e.data.download + '" ';
						}

						if( e.data.social != 'on' ) {
							shortcode += ' social="false" ';
						} else {
							// Default: Twitter, Facebook true; others false
							if( ! jQuery("#spp_socialopt_twitter").is( ":checked" ) )
								shortcode += ' social_twitter="false" ';
							if( ! jQuery("#spp_socialopt_facebook").is( ":checked" ) )
								shortcode += ' social_facebook="false" ';
							if( jQuery("#spp_socialopt_linkedin").is( ":checked" ) )
								shortcode += ' social_linkedin="true" ';
							if( jQuery("#spp_socialopt_pinterest").is( ":checked" ) )
								shortcode += ' social_pinterest="true" ';
							if( jQuery("#spp_socialopt_email").is( ":checked" ) )
								shortcode += ' social_email="true" ';
						}
						
						if( e.data.permalink != '' )
							shortcode += ' permalink="' + e.data.permalink + '" ';
						if( e.data.tweet_text != '' )
							shortcode += ' tweet_text="' + e.data.tweet_text + '" ';
						if( e.data.hashtag != '' )
							shortcode += ' hashtag="' + e.data.hashtag.replace('#','') + '" ';
						if( e.data.twitter_username != '' )
							shortcode += ' twitter_username="' + e.data.twitter_username.replace('@','') + '" ';

                    	shortcode += ']';

                        // Insert content when the window form is submitted
                        if( e.data.url != '' )
                            editor.insertContent( shortcode );
                        else
                            editor.windowManager.alert("URL is required.");

                    }

                } );
				
				// Add a scroll bar to the side, and make room for it
				var mceWindows = top.tinymce.activeEditor.windowManager.getWindows();
				var win = document.getElementById(mceWindows[0]._id);
				var viewportHeight = Math.max(document.documentElement.clientHeight,
						window.innerHeight || 0);
				var currentHeight = parseInt(win.style.height);
				var currentWidth = parseInt(win.style.width) 
				win.style.height = Math.min(currentHeight + 25, viewportHeight - 20) + "px";
				win.style.width = currentWidth + 25 + "px";
				win.style.overflow = "scroll";
				
				// Gray out the background color prompt, if necessary
				if( typeof jQuery !== 'undefined' ) {
					var $bgPrompt = jQuery(win).find('.mce-label:contains("Background color")').parent().parent();
					if( def.stp_background == 'color' )
						$bgPrompt.css( 'color', '#444' );
					else
						$bgPrompt.css( 'color', '#bbb' );
				}
            }

        } );

    } );

} )();
