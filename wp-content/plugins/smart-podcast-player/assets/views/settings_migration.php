<?php

$defaults = get_option('spp_player_defaults');
if (isset($defaults['url']))
	$old_rss_url = $defaults['url'];
else
	$old_rss_url = '';

$replacements = get_option('spp_player_replacement_urls', array());
?>

<div class="nav-tabs">
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab spp-nav-tab nav-tab-active" href="#spp-tab-overview">Host Migration Overview</a>
		<a class="nav-tab spp-nav-tab" href="#spp-tab-show">Current replacements</a>
		<a class="nav-tab spp-nav-tab" href="#spp-tab-match">Match URLs via RSS feeds (best method)</a>
		<a class="nav-tab spp-nav-tab" href="#spp-tab-search">Match URLs via post search (fallback method)</a>
	</h2>
</div>
<div class="spp-tab-content">
	<div id="spp-tab-overview" class="spp-tab-pane spp-tab-active">
		<h2>What is the Host Migration feature?</h2>
		<p>The Host Migration feature has been designed to remove the hassle and human-error risk
		factor from moving a podcast feed from one host to another. For example, let’s say you started
		your podcast on Soundcloud, but after two years, you decided you would prefer to be on Buzzsprout.
		Without this feature, after you migrated to Buzzsprout, you would need to navigate through your
		show notes pages or everywhere on your WordPress site where you have a track player and update
		the .MP3 link. That’s a big pain!</p>
		<p>Avoid that pain by using the Host Migration feature. When you do, the Fusebox Track Player will
		automatically match the appropriate new host’s URL with the old, then insert the replacement URL
		into the player so that it’s used any time someone clicks “Play” or “Download”. The replacement
		happens automatically with no lag in performance, like magic!</p>	
		<br>
		<p><strong><a href="https://support.fusebox.fm/host-migration" target="_blank">Check out the
		full support doc on Host Migration.</a></strong></p>
	</div>
	<div id="spp-tab-show" class="spp-tab-pane">
		<p>On this screen, you’ll see the list of URLs you’ve previously marked for replacement. See
		“What is the Host Migration feature?” question in the Host Migration Overview tab for a full
		explanation of how this works.</p>

		<p><strong>Before clicking “Remove all replacements,”</strong> if you feel unsure about using
		the feature, email us at help@fusebox.fm for assistance. We’re happy to help.</p>
		<h2>Current Replacement URLs: 
			<span id="spp-num-replacements"><?php echo count($replacements) ?></span>
		</h2>
		<fieldset>
			<legend>Remove all replacements</legend>
			Warning: this action cannot be undone.
			<p>
			<input type="button" id="spp-enable-remove-all-replacements"
					class="button button-secondary" value="Enable this button -->">
			<input type="button" id="spp-remove-all-replacements" disabled
					class="button button-primary" value="Remove all">.
			<p>
		</fieldset>
		<?php if (count($replacements) > 0) { ?>
			<h2>Current Replacements: </h2>
		<?php } ?>
		<div class="spp-current-replacements">
			<?php
				foreach ($replacements as $old_url => $new_url) {
					echo 'Replace "' . $old_url . '"<br>';
					echo 'with "' . $new_url . '"<br><br>';
				}
			?>
		</div>
	</div>
	
	<div id="spp-tab-match" class="spp-tab-pane">
		<h2>Preferred Method for Updating Your Track Players</h2>
		<p>In this migration method, we ask you to provide your old RSS feed (the one you’ve been using)
		and your new RSS feed (the one you are switching to). </p>

		<p>Once we have those feed URLs, we will then match up the individual episodes based on the
		episode title. Once you approve the matches and click the <strong>Add Results</strong> button,
		your Fusebox Track Players will use those new .MP3 URLs. You will not need to update each individual
		track player.</p>
		
		<h2>Step 1: Enter RSS feed URLs</h2>
		<table class="form-table"><tbody>
			<tr><th scope="row"><label for="spp-old-rss-url">Old RSS feed URL:</label></th>
			<td><input type="text" name="spp_player_old_rss" id="spp-old-rss-url" size="80"
				value=""></td></tr>
			<tr><th scope="row"><label for="spp-new-rss-url">New RSS feed URL:</label></th>
			<td><input type="text" name="spp_player_new_rss" id="spp-new-rss-url" size="80"
				value=""></td></tr>
		</tbody></table>
		<input type="button" id="spp-submit-migration-urls" class="button button-primary" value="Load feeds and compare">
		<div class="spp-loading-feed">Loading feeds, please wait...</div>
		<div class="spp-feed-error">There was an error loading the feed at "<span class="spp-feed-error-url"></span>".</div>
		
		<br>
		<div class="spp-migration-add-results">
			<h2>Step 2: Add these results to the list of replacements</h2>
			<div class="spp-migration-help">Review the list below.  If it looks correct, click the button
			to add these matches to the list of replacements.</div>
			<br>
			<input type="button" id="spp-submit-migration-list" class="button button-primary" value="Add results">
			<div class="spp-submitting-list">
				<h3>Adding results to replacements.  Please wait.</h3>
			</div>
			<div class="spp-submit-list-error">
				<h3>There was an error while trying to add these results.  If this persists,
				please contact help@fusebox.fm</h3>
			</div>
			<div class="spp-migration-success">
				<h3>Results successfully added.</h3>
			</div>
		</div>
		<div class="spp-migration-results"></div>
		<br>
	</div>
	
	<div id="spp-tab-search" class="spp-tab-pane">
		<h2>Fallback Method for Updating Your Track Players</h2>
		<p>This migration method is less precise than the “Match URLs” method. Please use that
		method if you’re able. If you don’t have access to your old RSS feed (if you’ve already put
		the 301 redirect in place with your podcast host), then use this method.</p>

		<p>In this method, in <strong>Step 1</strong> we’ll search your website for all the Fusebox
		Track Players we can find, and then we’ll list all the .MP3 files links we can find here in
		the left-hand column below. They will be sorted by publication date.</p>

		<p>In <strong>Step 2</strong>, you’ll put in your new feed. This will get listed in the
		right-hand column below.</p>

		<p>To set your replacements, use the controls on the left-hand list to line up the URLs with
		their replacement on the right. This method is less elegant, but it will work in the event
		that you no longer have access to your old podcast RSS feed.</p>

		<h2>Step 1: Search all posts and pages for fusebox_player and smart_track_player shortcodes</h2>
		<input type="button" id="spp-search-stps" class="button button-primary" value="Search posts and pages">
		<h2>Step 2: Search your new RSS feed</h2>
		<table class="form-table"><tbody>
			<tr><th scope="row"><label for="spp-new-rss-url-B">New RSS feed URL:</label></th>
			<td><input type="text" name="spp_player_new_rss" id="spp-new-rss-url-B" size="80"
				value="<?php echo $old_rss_url ?>"></td></tr>
		</tbody></table>
		<input type="button" id="spp-load-feed" class="button button-primary" value="Search feed">
		<div class="spp-loading-feed-B">Loading feeds, please wait...</div>
		<div class="spp-feed-error-B">There was an error loading the feed at "<span class="spp-feed-error-url-B"></span>".</div>
		<h2>Step 3: Adjust replacements</h2>
		<div class="spp-migration-results-b">
			<div class="spp-b-stp-results"></div>
			<div class="spp-splitter"></div>
			<div class="spp-b-feed-results"></div>
		</div>
		<div class="spp-migration-add-results">
			<h2>Step 4: Add these results to the list of replacements</h2>
			<input type="button" id="spp-submit-migration-list-B" class="button button-primary" value="Add results">
			<div class="spp-submitting-list">
				<h3>Adding results to replacements.  Please wait.</h3>
			</div>
			<div class="spp-submit-list-error">
				<h3>There was an error while trying to add these results.  If this persists,
				please contact help@fusebox.fm</h3>
			</div>
			<div class="spp-migration-success">
				<h3>Results successfully added.</h3>
			</div>
		</div>
	</div>
</div>
