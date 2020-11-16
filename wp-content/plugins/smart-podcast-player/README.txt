# Fusebox Player
Requires at least: 3.9.4
Tested up to: 5.4.2
Stable tag: 2.12.0
Copyright: SPI Labs, LLC

An audio player built for podcasters by podcasters.  Most web-based podcast players are dumb. Not this one.  The Fusebox Player is the best way to engage your listeners and grow your audience.

## Changelog

### v2.12.0, 2020-08-11
* Added "start" shortcode option for custom start times (pro feature)
* Fix to color of "..." button in dark mode sticky player
* Fix for Buzzsprout analytics
* Fix for some frontends when using Underscore Javascript library
* Added "newline" shortcode option to manual timestamps
* Hid pro options from free version in post editor
* Added warning when using _latest shortcode in shortcode builders

### v2.11.0, 2020-07-07
* Support for Buzzsprout advanced analytics
* Show notes now include <br> tags
* Fix for websites that use Lodash
* Fix for volume slider overlap issues
* Fix for dark mode email newsletter text color
* Eliminated warnings from PHP compatibilty checker
* Optimized subscribe/share image sprite

### v2.10.0, 2020-05-05
* Added volume control
* Added controls for future episodes' timestamps
* Added podcast host migration helper feature
* Updated Facebook sharing URL
* Improved detection of progress bar hover
* On pages with multiple track players, playing one pauses the others
* Fix for download/share buttons sometimes not appearing
* Fix for certain admin buttons not responding
* Fix for mobile download button
* Fix for zero byte downloads

### v2.9.0, 2020-02-14
* Rebranded to Fusebox Player
* Buffering indicator while file is loading
* Fix for featured button showing up when it shouldn't
* Added Turkish translations

### v2.8.0, 2019-04-15
* Added seven new subscription services
* Added support for Sharpspring email
* Updated logos for iTunes and Play Music
* Removed Google+ due to service shutdown
* Fixed Soundcloud download issue
* Fixes for featured button

### v2.7.0, 2019-01-29
* Added clickable timestamps feature
* Additional options for the featured button
* Option to specify an episode for the sticky player
* Capability to add the sticky player to an individual page
* Fixed issues playing Soundcloud tracks
* Fix for featured episode when sorting by oldest
* Fix for social sharing email button in sticky player
* Fixed missing download button with certain share/subscribe settings

### v2.6.0, 2018-11-25
* Added new sticky player option
* Support for new Gutenberg editor
* Added background color options for mobile track player
* Fixed email embed code bug
* Workaround for resizing bug in Safari
* Fixed sound duplication bug
* Fixed bug where STP fails to restart after finishing a track
* Fixed filenames of downloaded MP3s
* Added option to not cache feeds

### v2.5.0, 2018-08-29
* Major upgrade to mobile STP design
* Added additional speed options
* Added subscription buttons to STP
* Fixed text selection bug when clicking controls
* Fixed downloading issue for some browsers
* Fixed issue with Constant Contact email integration
* Fixed conflict with Ninja Forms
* Option to hide SPP logo while loading full player

### v2.4.0, 2018-05-24
* Updated email capture feature for GDPR requirements
* Improved loading performance of latest episode track player
* Increased amount of show notes displayed in full player
* Added support for RSS files with extra whitespace at start
* Fix to RSS subscription link in full player
* Fix to search functionality
* Added fix for Squarespace feeds
* Reduced database usage
* Fix for some design issues in iOS
* Added Finnish support and updated French translation

### v2.3.0, 2017-09-12
* New email list signup feature
* Show notes take up more space in full player
* Track player data retrieved preferentially from RSS feed
* Download issue on Safari/iOS fixed
* Added option to turn off track player marquee
* Added option to hide tags/keywords
* Added time-based callbacks to Javascript API
* Better loading procedure on pages with multiple track players
* Fix for themes that alter _.template's delimiters
* Fix for Episode Details not appearing in full in mobile full player
* Fixed for " characters in show name/artist name
* Fix for running with the Beaver Builder plugin
* Fix for the UX Editor in the Flatsome theme
* Fix for track players where title is a number

### v2.2.0, 2017-03-30
* New design and animation for play/pause button
* Autolinkification of URLs in show notes
* Added STP background options to shortcode builder
* Streamlined update and license checks
* Added framework for error reporting
* Loads correctly when using Cloudflare Rocket Loader
* Moved JS/CSS version number from query string to file name
* Support for M4A in STP
* Added version to JS object and version checking
* Fix for RSS link in full player
* Fix for dynamic loading of SPP using initialize()
* Fix for editor in Kallyas themes and Thrive Content Builder
* Fix for featured episode in cache and after first page
* Fix for Soundcloud with secret tokens

### v2.1.0, 2016-12-13
* Improved loading speed for full player
* Added Twitter sharing options: via username, tweet text
* Fixes to permalink and hashtag options when social sharing
* Assets are only loaded on pages that include a player
* Added Javascript API with initialize() function
* Fix to speed change when advancing to next track
* Added fallback for failed license checks
* When no options are under it, "..." button gives show notes
* Fix for Safari bug with blurred background
* Multiple Fixes to CSS

### v2.0.0, 2016-10-12
* Complete redesign of front end for more intuitive, modern look
* Added search functionality to playlist
* Added roll back/roll forward buttons
* Improved share/subscribe links
* More options for player colors/backgrounds
* Use SVGs for most images for better resolution
* Option to use mobile players on desktop browsers
* Compatible with PHP 7

### v1.5.4, 2016-09-09
* Compatible with iOS 10
* Fix for timestamps slightly off
* Fix for double loading of Javascript
* Fix for errors while running with PHP errors shown
* Feed fetching is now done asynchronously in full player

### v1.5.0, 2016-07-11
* Added drag-to-seek functionality on mobile devices
* Added timestamp when hovering (desktop) or dragging (mobile)
* Added tags to full player for all feeds with <itunes:keywords>
* Fix for phantom download issue from mobile devices
* Improved page load performance of smart_track_player_latest
* Fix for show notes button not working on mobile devices
* Fix for '&' in show title when using email social option
* Style bugfixes: image border radius, button colors, text in dark theme
* Fix for marquee jump on OS X browsers
* Added advanced option for injecting assets into HTML
* Added workaround for servers with older versions of cURL
* Streamlined package by removing unused assets

### v1.4.0, 2016-04-15
* Added options for episode timer: up, down, or not shown
* Added option to not display episode numbers in SPP playlist
* Added options for STP progress bar color when loading and playing
* Added ability to stick an STP to the top of the page while scrolling
* Added partial support for M4A podcasts
* Added workaround for broken feeds in Wordpress 4.5 update
* Fixed STP width bug when resizing windows
* Fixed STP width bug when using players of multiple widths
* Improved performance of dynamically-generated CSS
* Improved performance of pages with multiple Soundcloud STPs
* Fix for missing artist in track player
* Added option for adding assets directly to HTML
* Fixed text color bug in show notes
* Fixed permalink option for email sharing
* Added workaround for certain Feedpress feeds
* Added workaround for pages that load jQuery more than once

### v1.3.0, 2016-01-27
* Added ability to feature an episode in Smart Podcast Player
* Enabled scrolling for long title or artist in Smart Track Player
* Added option to limit the number of episodes shown
* Improved performance of latest track player
* Added option to hide the listen counter in SPP
* Mitigated compatibility issues with Thrive Content Builder
* Specified zero padding around images
* Fixed some naming conflicts with Javascript function 'style'
* Fixed bug in latest track player when using feeds.soundcloud.com


### v1.2.0, 2015-11-24
* Added functionality for Smart Track Player to play the latest track from a feed
* Overhauled the player default settings page
* Improved the shortcode builders
* Added SPP cache busting button to settings page
* Added a selector for where in the RSS feed the show notes come from
* Option to override theme's CSS within player
* Mitigated some themes'/plugins' conflicts with WP Color Picker
* Added workaround for servers without PHP mb_string
* Fixed scope resolution bug affecting some servers
* Fixed SPP cache clearing bug
* Improved visual design when more than four sharing buttons are in SPP

### v1.1.2, 2015-10-15
* Fixed bug that caused the SPP cache not to clear on a manual clearing operation.

### v1.1.1, 2015-10-02
* Fixed syntax error which resulted in a variety of failures for some users.

### v1.1.0, 2015-09-29
* Added option for default image in Smart Track Player.
* Allow for all seven social sharing options at once.
* Fixed download button for web hosts that reject URLs in query string.
* More robust to other plugins/themes that inject Javascript into the page.
* Fixed timeout issue when getting file data on Windows hosts.
* Enlarged text boxes in some settings fields for ease of use.
* Fixed disappearing controls when some shortcode options were utilized.
* Cache busting clears all SPP transients, not just some of them.
* Fixed bug that ignored email social sharing option in STP.

### v1.0.3, 2015-08-04

* Mobile sharer links revised. FB sharer and Twitter sharer links updated to be in line with API changes.
* Soundcloud API compatibility fix for 200+ track full players - offset deprecated thus pagination per API spec implemented.
* Changing sort order will now reset the page to the one containing the track whose details are being shown.
* Update state variables to currently playing when the title bar is clicked that may prevent pausing.
* Extended fetching timeout for feeds that load slowly which could result in an intermittent full player.
* Speed improvements for fetching remote feeds including better feed change detection to improve performance.
* Fixed edge case that could prevent license check/update request due to web host configuration issue for WP request via SSL.
* Assets directory separator corrected for Windows based wordpress installs.
* Brief full player "green" flash as CSS default background color on load eliminated.
* Phased out need for single track player AJAX CSS custom colors request if default color is utlized.

### v1.0.2, 2015-04-07

* New UI image set for SPP including pagination arrows, download, subscribe, and sort.
* Featured image along the left side of the Smart Track Player can be set via the shortcode tool.
* Added three additional social sharing sites: LinkedIn, Pinterest, StumbleUpon.
* Social sharing sites can be selected per Smart Track Player/Smart Podcast Player via the shortcode tool.
* Social sharing can now be selectively toggled off by shortcode.
* Speed control can now be selectively toggled off by shortcode.
* Added checks to identify permalink's in feeds that are null.
* Compatability support for the new WP 4.2 "update now" AJAX call from the plugins page.
* More visible WP admin notifications to ensure support for future updates.

### v1.0.1, 2015-02-24

* UI enhancement to reduce the width of download/subscribe buttons when in responsive mode (also when usable page width < 768px).
* Improved language support by reducing english word footprint for player actions (such as download, subscribe, and sort).
* "Powered by Smart Podcast Player" in full player can now be toggled off.
* Speed Boost: Phased out repetetive AJAX requests for Smart Track Player data.
* Enhanced title and artist fetching for web hosts that may not sufficiently accomodate capturing ID3 tags from remote MP3's.
* Soundcloud artwork is now protocol agnostic to further enhance support for HTTPS sites.
* Fixed issue with embedded ID3 tag artwork clogging up track data.
* Added shortcode tool button for custom WP post types as well.
* Minor tweaks for cases when full show notes should trump abbreviated show notes in the full player.
* "Smarter" title defaults for the Smart Track Player - if ID3 title isn't present, album then artist, can populate player's title.

### v1.0.0 / v0.9.5, 2015-01-29

* Added speed control functionality for faster or slower playback rates (0.5x, 1x, 1.5x, 2x, 3x).
* Added social sharing capability native to the Smart Track Player.
* Enabled functionality to prevent the default green background from flashing prior to custom colors being loaded.
* Resolved issue where PowerPress users may have abbreviated (truncated ...) show notes.
* Plugin update notification improvements.
* UI responsive enhancements for mobile browsers.
* Fixed minor edge case where ampersands in feed URLs could prevent tracks from playing.
* Updated player controls including a new UI for the Smart Track Player.
* Resolved issue where admin JS file caused a conflict with editing widgets. Plugin now loads admin JS only when required.
* Added a highly visible loading progress to the background alongside the current playing progress.
* General download button improvements to address technical nuances amongst browsers. Enhanced support for HTTPS sites.
* Fixed issue with the legacy cache directory location not exclusively being utilized.
* Tested to support Wordpress 4.0+.

### v0.9.4

* Player will now render larger soundcloud track lists (200+).
* Various performance improvements for soundcloud and feeds.
* Added advanced setting for specifying the number of minutes to cache.
* Added advanced setting to select the podcast download button's PHP method.
* Enabled two alternate download methods to improve support for the download button.
* Fixed issue getting the MP3 file size. Browser can now calculate estimated download time remaining.
* Compatibility bug fix to prevent getID3 redclare warning if its already loaded by another plugin.
* Minor bug fix to support constant usage for legacy PHP version 5.2.

### v0.9.3

* The manual check for updates link on the wordpress plugins page is now operational.
* New message box alert if Smart Track Player is added without URL via the shortcode button.
* Security improvements to safely sanitize URLs.
* License keys with trailing or leading spaces wont prevent update checks.
* Fixed minor issue with v0.9.2 still showing the previous v0.9.1 query string on assets.

### v0.9.2

* <strong>Fixed issue for shared hosting where MP3 downloads and data retrieval were not working.</strong>
* Improved internal and external documentation of the plugin.
* Improved CSS functionality.
* Added subscription button.
* Multiple minor performance bugs.

### v0.9.1

* <strong>Updated custom color handling to be universally applied in themes</strong> (including widgets, archive pages, etc).
* Adjusted Firefox background-position-y bug that caused player controls to be the wrong color.
* Solved character encoding issue where strange characters appeared in place of punctuations.
* Refined player color stylings and calculations based on custom colors.
* Ensured "Dark" styles are applied when set in the Player Defaults.
* Updated positioning of download button in narrow podcast players.

### v0.8.5

* Updated background MP3 file handling.
* Added ability for single track player to play most recent track from a feed.
* Added oldest/newest sorting to podcast player playlist.
* Added Smart Track Player shortcode builder.
* Added Smart Podcast Player shortcode builder.
* Added default settings for SPP.
* Added ability to select custom colors for podcast and track player.
* Updated settings tab layout to be more simple.


### v0.8.4

* Adjusted SPP file cache functionality.
* Added HTML filtering from RSS feeds.
* Updated size of paging buttons in playlist for better touch functionality.
* Added ability to disable social buttons via shortcode.
* Added ability to disable download in podcast player.
* Resolved conflict with Q2W3 Fixed Widget jQuery resize event.

### v0.8.3

* Admin file loading bug causing a crash of the WP admin has been resolved.
* Link handling in player has been updated for various feed sources.
* ID3 data retrieval has been updated to handle more server configurations and more intelligent buffer sizes.
* Documentation has been updated to be more copy/paste friendly.
* CSS classes have been namespaced to create more consistent results across themes.
* Updated integration with OptimizePress plugin pages.
* Updated mobile stylings to integrate with WooThemes Canvas.
* Updated remote audio file retrieval to work with a wider variety of server configurations.
* New, more robust RSS retrieval methods.
* Updated social URL sharing for RSS feeds.
* Added method for auto-detecting and scrubbing of some HTML used within shortcodes.
* Updated internal audio and feed data cache handling by SPP.
