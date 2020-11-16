<?php
// Default values for the timestamps page
$defaults = array(
		'show_times' => 'true',
		'feed_url' => '',
		'future_urls' => array(),
	);
$saved_options = get_option( 'spp_player_timestamps', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
?>

<div class="nav-tabs">
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab spp-nav-tab nav-tab-active" href="#spp-tab-current">Current episodes</a>
		<a class="nav-tab spp-nav-tab" href="#spp-tab-future">Future episodes</a>
	</h2>
</div>
<div class="spp-tab-content">
	<div id="spp-tab-current" class="spp-tab-pane spp-tab-active">
		<h4>Create clickable timestamps for your podcast episodes. Set up the timestamps
		once and they will be available for use on any page within your website. <a
		href="https://support.fusebox.fm/article/242-how-to-set-up-clickable-timestamps"
		target="_blank">Learn more about this feature.</a></h4>
		<br>

		<table class="form-table"><tbody>
			<tr><th scope="row">RSS feed URL: </th>
			<td><input type="text" name="spp_player_timestamps[feed_url]" id="spp-timestamps-feed-url"
					value="<?php echo $feed_url ?>" size="50"></td></tr>
			<tr><th scope="row">Include time in brackets:</th>
			<td><input type="checkbox" name="spp_player_timestamps[show_times]" <?php checked($show_times, 'true') ?>></td></tr>
		</tbody></table>
		<em>Check this box to include the time in brackets before each clickable timestamp.</em>
		<br><br><br>

		<div class="spp-loading-feed">Loading feed, please wait...</div>
		<div class="spp-feed-error">There was an error loading the feed at "<?php echo $feed_url; ?>".
			<input type="button" id="spp-reload-feed" class="button button-secondary" value="Reload feed">
		</div>

		Select the episode from your feed. <span class="spp-track-selector-helper">Click "Save Changes"
		or "Revert" to enable.</span>
		<br><div class="spp-timestamp-table"></div><br>

		To give your episode a custom name, enter a short text string (such as SPI352) in the box below.
		Otherwise, we will name it for you.
		<table class="form-table spp-timestamp-reference-options"><tbody><tr>
			<th scope="row">Reference:</th>
			<td><input type="text" class="spp-timestamp-ref" size="10"></td>
		</tr></tbody></table>
		Copy the shortcode below and place it where you would like your clickable timestamps to appear.
		<table class="form-table spp-timestamp-reference-options"><tbody><tr>
			<th scope="row">Shortcode to use:</th>
			<td>
				<pre class="spp-timestamp-shortcode"></pre>
				<button type="button" class="spp-copy-timestamp-shortcode button button-secondary">
					Copy to clipboard
				</button>
			</td>
		</tr></tbody></table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"/>
			<input type="button" id="spp-timestamps-revert" class="button button-secondary" value="Revert">
		</p>

		<pre style="display: none;">
			<?php
				$adv = get_option('spp_player_advanced');
				if ($adv && isset($adv['debug_output']) && $adv['debug_output'] == 'true') {
					print_r($processed_options);
				}
			?>
		</pre>
	</div>
	<div id="spp-tab-future" class="spp-tab-pane spp-tab">
		<h4>If you would like to define timestamps for an upcoming episode of your podcast (i.e. if
		your episode has been scheduled to publish at a future date in your podcast host), enter
		the URL of the MP3 file below.  That URL will then appear in the dropdown menu of the
		"Current episodes" tab and you can add timestamps as usual.</h4>
		<br>
		<table class="form-table"><tbody>
			<tr><th scope="row">URL of MP3 file: </th>
			<td><input type="text" name="spp_player_timestamps[future_url]" id="spp-timestamps-future-url"
					size="90"></td></tr>
		</tbody></table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Submit"/>
		</p>
		<?php if (count($future_urls) > 0) { ?>
			<fieldset id="spp-current-future-urls">
				<legend>Currently defined future URLs</legend>
				<h4>These are the future URLs you have defined.</h4>
				If you've made a typo, you can delete them here.
				You shouldn't need to do this normally, as these definitions will be removed when the episode's
				URL is added to your RSS feed.<br>
				<table class="form-table" id="spp-future-urls-table"><tbody>
				</tbody></table>
			</fieldset>
		<?php } ?>
	</div>
</div>
