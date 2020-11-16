<?php
// Default values for the advanced settings page
$defaults = array(
		'spp_branding'     => 'true',
		'show_notes'       => 'description',
		'cache_timeout'    => '15',
		'debug_output'     => 'false',
		'stp_data_source'  => 'feed',
		'downloader'       => 'fopen',
		'css_important'    => 'true',
		'color_pickers'    => 'true',
		'versioned_assets' => 'true',
		'html_assets'      => 'false',
		'timestamps_input' => 'false',
		'init_on_mutation' => 'false',
		'host_migration'   => 'false',
	);
$saved_options = get_option( 'spp_player_advanced', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );

function text_input($name, $value) {
	?>
		<input type="text" name="<?php echo $name ?>" value="<?php echo $value ?>" >
	<?php
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
?>

<table class="form-table"><tbody><tr>
	<th scope="row">Fusebox logo when loading:</th>
	<td><?php select_input("spp_player_advanced[spp_branding]", $spp_branding,
			array("true" => "Yes (Default)", "false" => "No")) ?></td>
</tr></tbody></table>
Show the Fusebox Player logo in the player loading screen.

<p><strong><em>
	We do not recommend making any changes to the items below unless you are experiencing problems. Before making changes,
	<a target="_blank" href="http://support.fusebox.fm/article/84-understanding-the-advanced-settings-menu">
	please consult this support article.</a>
</em></strong></p>

<table class="form-table"><tbody><tr>
	<th scope="row">RSS Show notes field:</th>
	<td><?php select_input("spp_player_advanced[show_notes]", $show_notes,
			array("description" => "description", "content" => "content",
				"itunes_summary" => "itunes:summary", "itunes_subtitle" => "itunes:subtitle")) ?></td>
</tr></tbody></table>
For each item in your RSS feed, FBP will look in this field for your show notes.

<table class="form-table"><tbody><tr>
	<th scope="row">Cache Timeout:</th>
	<td><?php text_input("spp_player_advanced[cache_timeout]", $cache_timeout) ?> minutes</td>
</tr></tbody></table>
This adjusts how often FBP checks your feed for new episodes.

<table class="form-table"><tbody><tr>
	<th scope="row">Show debugging output: </th>
	<td><?php select_input("spp_player_advanced[debug_output]", $debug_output,
			array("true" => "Yes", "false" => "No (Recommended)")) ?></td>
</tr></tbody></table>
Show extra debugging output in the Javascript console.

<table class="form-table"><tbody><tr>
	<th scope="row">Track player data source: </th>
	<td><?php select_input("spp_player_advanced[stp_data_source]", $stp_data_source,
			array("feed" => "RSS feed, then MP3 metadata (default)", "mp3" => "MP3 metadata, then RSS feed")) ?></td>
</tr></tbody></table>
The preferred source for artist and title information for the Fusebox Track Player.<br>
Setting the artist or title in a player's shortcode will override this.

<table class="form-table"><tbody><tr>
	<th scope="row">Download Method: </th>
	<td><?php select_input("spp_player_advanced[downloader]", $downloader, array(
			"smart" => "Automatic (Recommended)",
			"fopen" => "Stream (fopen)",
			"local" => "Local File Cache",
			"curl" => "Curl Buffer",
			)) ?></td>
</tr></tbody></table>
This adjusts how Fusebox Player requests files from your podcast host.

<table class="form-table"><tbody><tr>
	<th scope="row">Use "!important" in CSS: </th>
	<td><?php select_input("spp_player_advanced[css_important]", $css_important,
			array("true" => "Yes", "false" => "No")) ?></td>
</tr></tbody></table>
Add the CSS "!important" declaration to all of Fusebox Player's styles.

<table class="form-table"><tbody><tr>
	<th scope="row">Show color pickers: </th>
	<td><?php select_input("spp_player_advanced[color_pickers]", $color_pickers,
			array("true" => "Yes (Recommended)", "false" => "No")) ?></td>
</tr></tbody></table>
Prevent FBP from loading the Wordpress color picker Javascript in the admin settings pages.

<table class="form-table"><tbody><tr>
	<th scope="row">Javascript and CSS file versioning: </th>
	<td><?php select_input("spp_player_advanced[versioned_assets]", $versioned_assets,
			array("true" => "In filename (Recommended)", "false" => "In query string")) ?></td>
</tr></tbody></table>
The version number can be included in either the filename or as a query string.

<table class="form-table"><tbody><tr>
	<th scope="row">Inject assets into HTML: </th>
	<td><?php select_input("spp_player_advanced[html_assets]", $html_assets,
			array("true" => "Yes", "false" => "No (Recommended)")) ?></td>
</tr></tbody></table>
Echo the Javascript loading and localization strings straight to the HTML, instead of using Wordpress's enqueue functions.

<table class="form-table"><tbody><tr>
	<th scope="row">Timestamps input workaround: </th>
	<td><?php select_input("spp_player_advanced[timestamps_input]", $timestamps_input,
			array("true" => "On", "false" => "Off (Recommended)")) ?></td>
</tr></tbody></table>
A workaround for certain server settings that cause timestamps not to be updated.

<table class="form-table"><tbody><tr>
	<th scope="row">Initialize on DOM changes: </th>
	<td><?php select_input("spp_player_advanced[init_on_mutation]", $init_on_mutation,
			array("true" => "On", "false" => "Off (Recommended)")) ?></td>
</tr></tbody></table>
In addition to the normal frontend initialization, this also runs initialization code whenever the document mutates.

<table class="form-table"><tbody><tr>
	<th scope="row">Host migration feature: </th>
	<td><?php select_input("spp_player_advanced[host_migration]", $host_migration,
			array("true" => "On", "false" => "Off")) ?></td>
</tr></tbody></table>
Enable URL replacements to help in host migration.
