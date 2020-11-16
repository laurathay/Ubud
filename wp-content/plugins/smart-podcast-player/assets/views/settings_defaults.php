<?php
// Default values for the player defaults settings page
$defaults = array(
		'url'                   => '',
		'subscribe_acast'       => '',
		'subscribe_itunes'      => '',
		'subscribe_buzzsprout'  => '',
		'subscribe_googleplay'  => '',
		'subscribe_googlepodcasts' => '',
		'subscribe_iheartradio' => '',
		'subscribe_playerfm'    => '',
		'subscribe_pocketcasts' => '',
		'subscribe_soundcloud'  => '',
		'subscribe_spotify'     => '',
		'subscribe_spreaker'    => '',
		'subscribe_stitcher'    => '',
		'subscribe_tunein'      => '',
		'subscribe_overcast'    => '',
		'subscribe_in_stp'      => 'true',
		'show_name'             => '',
		'artist_name'           => '',
		'style'                 => 'light',
		'bg_color'              => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
		'spp_background'        => 'default',
		'stp_background'        => 'default',
		'stp_background_color'  => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
		'stp_image'             => '',
		'sort_order'            => 'newest',
		'download'              => 'true',
		'episode_limit'         => '',
		'show_tags'             => 'true',
		'volume'                => 'true',
		'playback_timer'        => 'true', // No longer used
	);
$saved_options = get_option( 'spp_player_defaults', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );

// For display, these two need to HTML-escape double quotes
$show_name   = str_replace( '"', '&#34;', $show_name );
$artist_name = str_replace( '"', '&#34;', $artist_name );

function text_input($name, $value, $class, $size = 0) {
	echo '<input type="text" name="' . $name . '" class="' . $class . '" value="' . $value . '"';
	if ($size > 0)
		echo 'size="' . $size . '"';
	echo '>';
}

function checkbox_input($name, $value, $class) {
	echo '<input type="checkbox" name="' . $name . '" class="' . $class . '" ' . checked($value, 'true', false) . '>';
}

function select_input($name, $value, $options, $class = NULL) {
	?>
	<select name="<?php echo $name ?>" class="<?php echo $class ?>">
	<?php foreach ($options as $opt_name => $opt_text) { ?>
		<option value="<?php echo $opt_name ?>" <?php selected($value, $opt_name); ?> >
			<?php echo $opt_text ?>
		</option>
	<?php } ?>
	</select>
	<?php
}

function subscription_option($service, $var_name, $value) {
	?>
	<div class="spp-indented-option spp-subscription-option spp-subscription-option-<?php echo $var_name?> 
			<?php if (!empty($value)) echo 'spp-subscription-option-enabled'?>">
		<span class="spp-subscription-option-header">
			<span class="spp-subscription-option-arrow dashicons dashicons-arrow-right-alt2"></span>
			<span class="spp-subscription-option-service"><?php echo $service?></span>
		</span>
		<div class="spp-subscription-option-main">
			<table class="form-table spp-indented-option"><tbody><tr>
				<th scope="row" class="spp-wider-column"><?php echo $service ?> subscription URL:</th>
				<td><?php text_input("spp_player_defaults[subscribe_" . $var_name . "]", $value,
									 "spp-wider-left-column spp-subscription-input", 40) ?></td>
			</tr></tbody></table>
			<span class="">
				Leave this blank if you're not on <?php echo $service ?>.  Help me <a target="_blank" href="http://support.fusebox.fm/article/40-setting-up-the-subscription-button#subscription-link"> find my subscription URL</a>.
			</span>
		</div>
		<br>
	</div>
	<?php
}
?>

<h4>
	Save yourself time! Put in your default information so that you
	don’t have to add it each time you create a new shortcode.
</h4>
<h2>
	Podcast Feed Settings
</h2>
<table class="form-table"><tbody><tr>
	<th scope="row" class="spp-wider-column">Podcast RSS Feed URL:</th>
	<td><?php text_input("spp_player_defaults[url]", $url, "spp-wider-left-column", 40) ?></td>
</tr></tbody></table>
Help me <a target="_blank" href="http://support.fusebox.fm/article/54-getting-started-6-finding-your-rss-feed"> find my podcast feed URL</a>.

<h2>
	Subscription URLs
</h2>
<?php subscription_option("Acast", "acast", $subscribe_acast) ?>
<?php subscription_option("iTunes", "itunes", $subscribe_itunes) ?>
<?php subscription_option("Buzzsprout", "buzzsprout", $subscribe_buzzsprout) ?>
<?php subscription_option("Google Podcasts", "googlepodcasts", $subscribe_googlepodcasts) ?>
<?php subscription_option("iHeartRadio", "iheartradio", $subscribe_iheartradio) ?>
<?php subscription_option("Play Music", "googleplay", $subscribe_googleplay) ?>
<?php subscription_option("Player.FM", "playerfm", $subscribe_playerfm) ?>
<?php subscription_option("PocketCasts", "pocketcasts", $subscribe_pocketcasts) ?>
<?php subscription_option("Soundcloud", "soundcloud", $subscribe_soundcloud) ?>
<?php subscription_option("Spotify", "spotify", $subscribe_spotify) ?>
<?php subscription_option("Spreaker", "spreaker", $subscribe_spreaker) ?>
<?php subscription_option("Stitcher", "stitcher", $subscribe_stitcher) ?>
<?php subscription_option("TuneIn", "tunein", $subscribe_tunein) ?>
<?php subscription_option("Overcast", "overcast", $subscribe_overcast) ?>
<table class="form-table spp-indented-option"><tbody><tr>
	<th scope="row" class="spp-wider-column">Include subscription option in track players: </th>
	<td><?php checkbox_input("spp_player_defaults[subscribe_in_stp]", $subscribe_in_stp,
	                         "spp-wider-left-column spp-indent-ancestor-table") ?></td>
</tr></tbody></table>
<span class="spp-indented-option">
	Leave box checked if you want to include subscription options in track players, including the sticky player.
</span>
<table class="form-table"><tbody><tr>
	<th scope="row" class="spp-wider-column">Show Name (for the full player):</th>
	<td><?php text_input("spp_player_defaults[show_name]", $show_name, "spp-wider-left-column", 40) ?></td>
</tr></tbody></table>
Show me <a target="_blank" href="http://support.fusebox.fm/article/30-show-name">
where the Show Name goes</a>.
<table class="form-table"><tbody><tr>
	<th scope="row" class="spp-wider-column">Artist Name (for the track player):</th>
	<td><?php text_input("spp_player_defaults[artist_name]", $artist_name, "spp-wider-left-column", 40) ?></td>
</tr></tbody></table>
Show me <a target="_blank" href="http://support.fusebox.fm/article/25-change-artist-name-episode-title">
where the Artist Name goes</a>.

<hr>


<h2>
	Player Design Settings
</h2>
<p>For more on how to customize the look of your players, visit <a target="_blank" href="http://support.fusebox.fm/article/91-start-here-customize-the-smart-podcast-player">
this support article</a>.</p>
<h4>Colors and Image</h4>
<p>Check out <a target="_blank" href="http://support.fusebox.fm/article/91-start-here-customize-the-smart-podcast-player">this guide</a> to see how you can customize different colors and themes.</p>
<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Theme Style:</th>
		<td><?php select_input("spp_player_defaults[style]", $style, array("light" => "Light", "dark" => "Dark")) ?></td>
	</tr><tr>
		<th scope="row">Highlight Color:</th>
		<td>
			<div class="spp-color-picker spp-indent-ancestor-table">
				<?php text_input("spp_player_defaults[bg_color]", $bg_color, "color-field") ?>
			</div>
		</td>
	</tr>
</tbody></table>
<em class="spp-indented-option">Previously named "Progress Bar Color"</em>
<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Full Player Background:</th>
		<td><?php select_input("spp_player_defaults[spp_background]", $spp_background,
				array("default" => "Default", "blurred_logo" => "Blurred Logo"),
				"spp-indent-ancestor-table") ?></td>
	</tr><tr>
		<th scope="row">Track Player Background:</th>
		<td>
			<div class="spp-color-picker spp-indent-ancestor-table">
				<?php select_input("spp_player_defaults[stp_background]", $stp_background,
					array("default" => "Default", "blurred_logo" => "Blurred Logo", "color" => "Color (select)"),
					"spp-indent-ancestor-table") ?>
				&nbsp;
				<?php text_input("spp_player_defaults[stp_background_color]", $stp_background_color, "color-field") ?>
			</div>
		</td>
	</tr><tr>
		<th scope="row">Track Player Image URL: </th>
		<td><?php text_input("spp_player_defaults[stp_image]", $stp_image, "spp-indent-ancestor-table", 40) ?></td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Enter a URL.  Help me <a target="_blank" href="http://support.fusebox.fm/article/28-change-player-image">
	format this image properly.</a>
</div>



<h4>Buttons and Display Styles</h4>
<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Sort Order:</th>
		<td>
			<?php select_input("spp_player_defaults[sort_order]", $sort_order,
				array("newest" => "Newest to Oldest", "oldest" => "Oldest to Newest"),
				"spp-indent-ancestor-table") ?>
		</td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Help me <a target="_blank" href="http://support.fusebox.fm/article/55-getting-started-7-setting-up-your-player-defaults#other">
	choose which to use</a>.
</div>

<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Download:</th>
		<td>
			<?php select_input("spp_player_defaults[download]", $download,
				array("true" => "Yes", "false" => "No"),
				"spp-indent-ancestor-table") ?>
		</td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Selecting "No" will remove the download button.
</div>

<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Episode Limit:</th>
		<td><?php text_input("spp_player_defaults[episode_limit]", $episode_limit, "spp-indent-ancestor-table") ?></td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Enter a number to limit the display to that many of your most recent episodes.
</div>

<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Show tags:</th>
		<td>
			<?php select_input("spp_player_defaults[show_tags]", $show_tags,
				array("true" => "Yes", "false" => "No"),
				"spp-indent-ancestor-table") ?>
		</td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Whether to display the episode's tags/keywords in the Fusebox Full Player.
</div>

<table class="form-table spp-indented-option"><tbody>
	<tr>
		<th scope="row">Volume:</th>
		<td>
			<?php select_input("spp_player_defaults[volume]", $volume,
				array("true" => "Yes", "false" => "No"),
				"spp-indent-ancestor-table") ?>
		</td>
	</tr>
</tbody></table>
<div class="spp-indented-option">
	Whether to display the volume control.
</div>
