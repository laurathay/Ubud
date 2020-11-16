( function() {
	// No button if it's the free version
	if (!SmartPodcastPlayerAdmin.licensed)
		return;
    tinymce.PluginManager.add( 'spp', function( editor, url ) {
		
		// Shorter name for the default options from the settings page
		if( typeof smart_podcast_player_user_settings === 'undefined' ) {
			return;
		}
		var def = smart_podcast_player_user_settings;

        // Add a button that opens a window
        editor.addButton( 'spp_button_key', {

            icon: 'spp-icon',
			tooltip: 'Insert Fusebox Full Player shortcode',
            onclick: function() {
				
				var colorPrompt;
				var downloadablePrompt;
				var socialPrompt;

				// For the paid version, the color goes in a text box
				colorPrompt = {
					type: 'textbox',
					name: 'color',
					label: 'Highlight color (Hex) #',
					value: def.bg_color ? def.bg_color : ''
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
								<td><input type="checkbox" id="spp_socialopt_email" checked>Email</input></td> \
							</tr> \
							</table>'
				};

                // Open window
                editor.windowManager.open( {

                    title: 'Fusebox Full Player Shortcode',
                    body: [
						{
						    type: 'container',
							name: 'header_1',
							html: '<em>For building the full podcast player. '
							    + '<a target="_blank" href="http://support.fusebox.fm/article/56-getting-started-8-adding-the-full-smart-podcast-player">Click here</a>'
								+ ' to watch a support video.</em><br><br>'
							    + '<p><em>Enter default values in Settings —> Fusebox Player.</em></p>'
								+ '<div class="spp-mce-hr"></div>'
								+ '<p style="font-weight: bold">1. Enter your podcast\'s RSS feed</p>'
						},
	                    {
	                        type: 'textbox',
	                        name: 'url',
	                        label: 'URL',
							value: def.url ? def.url : ''
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
	                        name: 'style',
	                        label: 'Style',
	                        onselect: function(e) {},
	                        values: def.style && def.style == 'dark' ?
										[{text: 'Dark', value: 'dark'}, {text: 'Light', value: 'light'}]
									:
										[{text: 'Light', value: 'light'}, {text: 'Dark', value: 'dark'}]
	                    },
	                    {
	                        type: 'textbox',
	                        name: 'show_name',
	                        label: 'Show Name',
							value: def.show_name ? def.show_name : ''
	                    },
						{
						    type: 'container',
							name: 'show_name_help',
							html: '<em>&emsp;Leave blank to get your show name from your RSS feed.</em>'
						},
						{
						    type: 'container',
							name: 'header_3',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">3. Customize the buttons</p>'
						},
	                    socialPrompt,
						socialOptionsPrompt,
	                    downloadablePrompt,
						{
							type: 'container',
							name: 'header_4',
							html: '<div class="spp-mce-hr"></div>'
							    + '<p style="font-weight: bold">4. Customize your social sharing message</p>'
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
							html: "<em>&emsp;Leave blank to use the link listed in the RSS feed, if present</em>",
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

                    	var shortcode = '[fusebox_full_player';

                    	if( e.data.url != '' && ( !def.url || def.url != e.data.url ) )
                    		shortcode += ' url="' + e.data.url + '" ';

                    	if( def.style ) {
							if( e.data.style != def.style )
								shortcode += ' style="' + e.data.style + '" ';
						} else {
							if( e.data.style != 'light' )
								shortcode += ' style="' + e.data.style + '" ';
						}

                    	if( e.data.show_name != '' && ( !def.show_name || def.show_name != e.data.show_name ) )
                    		shortcode += ' show_name="' + e.data.show_name + '" ';

						if( e.data.color != '' && ( !def.bg_color || def.bg_color != e.data.color ) )
							shortcode += ' color="' + e.data.color + '" ';

						if( e.data.social != 'on' ) {
							shortcode += ' social="false" ';
						} else {
							// Default: Twitter, Facebook, email are true, others false
							// If user selected default options, nothing is needed here;
							//   otherwise, all checked ones are needed
							if( jQuery("#spp_socialopt_twitter").is( ":checked" )
									&& jQuery("#spp_socialopt_facebook").is( ":checked" )
									&& jQuery("#spp_socialopt_email").is( ":checked" )
									&& ! jQuery("#spp_socialopt_linkedin").is( ":checked" )
									&& ! jQuery("#spp_socialopt_pinterest").is( ":checked" ) ) {
								// No shortcode atts necessary here
							} else {
								if( jQuery("#spp_socialopt_twitter").is( ":checked" ) )
									shortcode += ' social_twitter="true" ';
								if( jQuery("#spp_socialopt_facebook").is( ":checked" ) )
									shortcode += ' social_facebook="true" ';
								if( jQuery("#spp_socialopt_linkedin").is( ":checked" ) )
									shortcode += ' social_linkedin="true" ';
								if( jQuery("#spp_socialopt_pinterest").is( ":checked" ) )
									shortcode += ' social_pinterest="true" ';
								if( jQuery("#spp_socialopt_email").is( ":checked" ) )
									shortcode += ' social_email="true" ';
							}
						}

						if( def.download ) {
							if( e.data.download != def.download )
								shortcode += ' download="' + e.data.download + '" ';
						} else {
							if( e.data.download != 'true' )
								shortcode += ' download="' + e.data.download + '" ';
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
                        editor.insertContent( shortcode );
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

            }

        } );

    } );

} )();
