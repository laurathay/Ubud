<?php
// Default values for the email portal settings page
$defaults = array(
		'enabled'           => 'false',
		'position'          => 'bottom',
		'url'               => '',
		'latest'            => 'true',
		'episode_url'       => '',
		'show_name'         => '',
		'color'             => '#60b86c',
		'download'          => 'true',
		'image'             => '',
		'style'             => 'light',
		'social_twitter'    => 'true',
		'social_facebook'   => 'true',
		'social_gplus'      => 'true',
		'social_linkedin'   => 'false',
		'social_pinterest'  => 'false',
		'social_email'      => 'false',
		'excluded_pages'    => '',
		'z_index'           => '1000',
		'post_type_page'    => 'true',
		'post_type_post'    => 'true',
		'post_types'        => "",
		'shortcode_options' => '',
	);
$saved_options = get_option( 'spp_player_sticky', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
$paid = SPP_Core::is_paid_version();
?>


<div class="spp-sticky-enabled-container">
	<label for="spp-sticky-enabled">Sticky player</label>
	<select name="spp_player_sticky[enabled]" id="spp-sticky-enabled">
		<option value="false" <?php selected($enabled, 'false');?>>Disable feature</option>
		<option value="true"  <?php selected($enabled, 'true');?>>Enable feature</option>
	</select>
</div>

<div class="spp-sticky-options">
	<table class="form-table"><tbody>
	
		<tr><th scope="row"><label for="spp-sticky-position">Position</label></th>
		<td><select name="spp_player_sticky[position]" id="spp-sticky-position">
			<option value="bottom" <?php selected($position, 'bottom');?>>Bottom</option>
			<option value="top"    <?php selected($position, 'top');   ?>>Top</option>
		</select></td></tr>
	
		<tr><th scope="row"><label for="spp-url">RSS Feed URL</label></th>
		<td><input type="text" name="spp_player_sticky[url]" id="spp-url" size="50"
			value="<?php echo $url; ?>"></td></tr>
	
		<tr><th scope="row"><label for="spp-latest">Episode</label></th>
		<td><select name="spp_player_sticky[latest]" id="spp-latest">
			<option value="true" <?php selected($latest, 'true');?>>
				Latest episode in your RSS feed
			</option>
			<option value="false" <?php selected($latest, 'false');?>>
				An episode from a specific URL
			</option>
		</select></td></tr>
	
		<tr id="spp-episode-url-row"><th scope="row">
			<label for="spp-episode-url">Episode URL</label></th>
		<td><input type="text" name="spp_player_sticky[episode_url]" id="spp-episode-url" size="50"
			value="<?php echo $episode_url; ?>"></td></tr>
	
		<tr><th scope="row"><label for="spp-show-name">Show name</label></th>
		<td><input type="text" name="spp_player_sticky[show_name]" id="spp-show-name" size="50"
			value="<?php echo $show_name; ?>"></td></tr>
	
		<?php if ($paid) { ?>
			<tr><th scope="row"><label for="spp-color">Highlight color</label></th>
			<td><input type="text" class="color-field" name="spp_player_sticky[color]" id="spp-color"
				value="<?php echo $color; ?>"></td></tr>
				
			<tr><th scope="row"><label for="spp-download">Download</label></th>
			<td><select name="spp_player_sticky[download]" id="spp-download" <?php echo $dis ?>>
				<option value="true"  <?php selected($download, 'true');?>>Yes</option>
				<option value="false" <?php selected($download, 'false');?>>No</option>
			</select></td></tr>
		<?php } ?>
	
		<tr><th scope="row"><label for="spp-image">Image URL</label></th>
		<td><input type="text" name="spp_player_sticky[image]" id="spp-image" size="50"
			value="<?php echo $image; ?>"></td></tr>
			
		<tr><th scope="row"><label for="spp-style">Style</label></th>
		<td><select name="spp_player_sticky[style]" id="spp-style">
			<option value="light" <?php selected($style, 'light');?>>Light</option>
			<option value="dark"  <?php selected($style, 'dark'); ?>>Dark</option>
		</select></td></tr>
	
		<?php if ($paid) { ?>
			<tr><th scope="row"><label for="spp-social">Social</label></th>
			<td>
				<table class="form-table spp-settings-inner-table" id="spp-social"><tbody>
					<tr>
						<td><input type="checkbox" name="spp_player_sticky[social_twitter]" id="spp-social-twitter"
						<?php checked($social_twitter, 'true') ?> /><label for="spp-social-twitter">Twitter</label></td>
						<td><input type="checkbox" name="spp_player_sticky[social_facebook]" id="spp-social-facebook"
						<?php checked($social_facebook, 'true') ?> /><label for="spp-social-facebook">Facebook</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="spp_player_sticky[social_linkedin]" id="spp-social-linkedin"
						<?php checked($social_linkedin, 'true') ?> /><label for="spp-social-linkedin">LinkedIn</label></td>
						<td><input type="checkbox" name="spp_player_sticky[social_pinterest]" id="spp-social-pinterest"
						<?php checked($social_pinterest, 'true') ?> /><label for="spp-social-pinterest">Pinterest</label></td>
						<td><input type="checkbox" name="spp_player_sticky[social_email]" id="spp-social-email"
						<?php checked($social_email, 'true') ?> /><label for="spp-social-email">Email</label></td>
					</tr>
				</tbody></table>
			</td></tr>
		<?php } ?>
		
	
		<?php
		$safe_excluded_pages = str_replace( "</textarea>", "&lt/textarea&gt;", $excluded_pages );
		?>
		<tr><th scope="row"><label for="spp-sticky-excluded-pages">Excluded pages<br><br>
			<em>By default, a player will appear on every page and post.  Enter here any URLs where
			a player should not appear, one URL per line.</em></label></th>
		<td><textarea name="spp_player_sticky[excluded_pages]" id="spp-sticky-excluded-pages"
				rows="6" cols="60"><?php echo $safe_excluded_pages; ?></textarea></td></tr>
	
		<tr><th scope="row"><label for="spp-show-advanced">Advanced options</label></th>
		<td><input type="button" id="spp-show-advanced" class="button button-secondary" value="Show"></td></tr>
				
	</tbody></table>
	<div class="spp-sticky-advanced-options">
		<table class="form-table"><tbody>
		
			<tr><th scope="row"><label for="spp-z-index">CSS Z Index<br><br>
				<em>Default is 1000.</em></label></th>
			<td><input type="text" name="spp_player_sticky[z_index]" id="spp-z-index" size="20"
				value="<?php echo $z_index; ?>"></td></tr>
		
			<?php
			$safe_post_types = str_replace( "</textarea>", "&lt/textarea&gt;", $post_types );
			?>
			<tr><th scope="row"><label for="spp-sticky-post-types">Post types<br><br>
				<em>The Wordpress post types on which to display the sticky player, one per line.
				We recommend displaying it on every page and post.</em></label></th>
			<td>
				<table class="form-table spp-settings-inner-table" id="spp-sticky-post-types"><tbody>
					<tr>
						<td><input type="checkbox" name="spp_player_sticky[post_type_page]" id="spp-post-type-page"
						<?php checked($post_type_page, 'true') ?> /><label for="spp-post-type-page">Page</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" name="spp_player_sticky[post_type_post]" id="spp-post-type-post"
						<?php checked($post_type_post, 'true') ?> /><label for="spp-post-type-post">Post</label></td>
					</tr>
					<tr><td><textarea name="spp_player_sticky[post_types]" id="spp-sticky-post-types2"
					rows="6" cols="60"><?php echo $safe_post_types; ?></textarea></td></tr>
				</tbody></table>
			</td>
		
			<?php
			$safe_shortcode_options = str_replace( "</textarea>", "&lt/textarea&gt;", $shortcode_options );
			?>
			<tr><th scope="row"><label for="spp-sticky-shortcode-options">Extra shortcode options<br><br>
				<em>If you wish to add additional shortcode options to your sticky
				player, enter them here, one per line.</em></label></th>
			<td><textarea name="spp_player_sticky[shortcode_options]" id="spp-sticky-shortcode-options"
					rows="6" cols="60"><?php echo $safe_shortcode_options; ?></textarea></td></tr>
					
		</tbody></table>
	</div>
</div>
