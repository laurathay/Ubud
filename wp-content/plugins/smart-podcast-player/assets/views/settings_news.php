<?php
// Default values for the email portal settings page
$defaults = array(
		'portal'                => 'none',
		'service'               => 'other',
		'outer_button_text'     => 'Sign me up!',
		'button_bg_color'       => '#60b86c',
		'button_text_color'     => '#ffffff',
		'embed_html'            => '',
		'embed_js'              => '',
		'embed_sharpspring'     => '',
		'use_spp_cta'           => 'false',
		'cta_open'              => 'manual',
		'cta_elapsed_seconds'   => 60,
		'cta_remaining_seconds' => 60,
		'button_function'       => 'email',
		'link'                  => '',
		'js_function'           => '',
	);
$saved_options = get_option( 'spp_player_email', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
?>
<h4>
Use the Featured Button to allow listeners to join your email list, to send traffic to another web page, or to initiate a Javascript function. <a href="https://support.fusebox.fm/article/243-featured-button" target="_blank">Learn more about this feature.</a>
</h4>

<div class="spp_email_portal_options">
	<label for="portal">Featured button status</label>
	<select name="spp_player_email[portal]" id="portal">
		<option value="none" <?php selected($portal, 'none');?>>Disable feature</option>
		<option value="enable" <?php selected($portal, 'enable');?>>Enable feature</option>
	</select>
</div>

<?php // These options are common to all the emailers except "None" ?>
<div class="emailer_options emailer_options_enable">

	<table class="form-table"><tbody>
	
		<tr><th scope="row"><label for="outer_button_text">Button text</label></th>
		<td><input type="text" name="spp_player_email[outer_button_text]" id="outer_button_text"
			value="<?php echo $outer_button_text; ?>"></td></tr>
	
		<tr><th scope="row"><label for="button_bg_color">Button background color</label></th>
		<td><div class="spp-color-picker">
		    <input type="text" class="color-field" name="spp_player_email[button_bg_color]"
			id="button_bg_color" value="<?php echo $button_bg_color; ?>"></div></td></tr>
	
		<tr><th scope="row"><label for="button_text_color">Button text color</label></th>
		<td><div class="spp-color-picker">
		    <input type="text" class="color-field" name="spp_player_email[button_text_color]"
			id="button_text_color" value="<?php echo $button_text_color; ?>"></div></td></tr>
			
		<tr><th scope="row"><label for="spp_player_email[cta_open]">Open Call to Action</label></th>
		<td>
			<input type="radio" name="spp_player_email[cta_open]"
					value="manual" <?php checked($cta_open, "manual") ?>>
			Only when user clicks the button<br>
			<input type="radio" name="spp_player_email[cta_open]"
					value="elapsed" <?php checked($cta_open, "elapsed") ?>>
			Automatically after
			<input type="text" name="spp_player_email[cta_elapsed_seconds]"
					size="6" value="<?php echo $cta_elapsed_seconds; ?>">
			seconds in each episode<br>
			<input type="radio" name="spp_player_email[cta_open]"
					value="remaining" <?php checked($cta_open, "remaining") ?>>
			Automatically when
			<input type="text" name="spp_player_email[cta_remaining_seconds]"
					size="6" value="<?php echo $cta_remaining_seconds; ?>">
			seconds remain in each episode
		</td></tr>
	</tbody></table>

	<div class="spp_featured_function_options">
		<label for="button_function">Featured button function</label>
		<select name="spp_player_email[button_function]" id="button_function">
			<option value="email" <?php selected($button_function, 'email');?>>Email integration</option>
			<option value="link" <?php selected($button_function, 'link');?>>Hyperlink</option>
			<option value="javascript" <?php selected($button_function, 'javascript');?>>Javascript</option>
		</select>
	</div>
	
	<div class="spp-featured-cta-email">
		<h4><a href="https://support.fusebox.fm/article/157-email-capture" target="_blank">
		Learn more about the Email Integration feature.</a></h4>
		<fieldset><legend>Call to Action</legend>
			<label for="service">Email service provider</label>
			<select name="spp_player_email[service]" id="service">
				<option value="other" <?php selected($service, 'other');?>>Most providers</option>
				<option value="ctct" <?php selected($service, 'ctct');?>>Constant Contact</option>
				<option value="shsp" <?php selected($service, 'shsp');?>>Sharpspring</option>
			</select>
			
			<?php
			$safe_embed_html      = str_replace( "</textarea>", "&lt/textarea&gt;", $embed_html );
			$safe_embed_html_ctct = str_replace( "</textarea>", "&lt/textarea&gt;", $embed_html_ctct );
			$safe_embed_shsp      = str_replace( "</textarea>", "&lt/textarea&gt;", $embed_shsp );
			?>
			
			<div class="embed_options embed_options_other">
				<table class="form-table dialog_options_embed"><tbody>
					<tr><th scope="row"><label for="embed_html">HTML to embed from your email provider<br><br>
						<em>If your email service provider offers a choice between
						Javascript and HTML embeds, use the HTML embed.</em></label></th>
					<td><textarea name="spp_player_email[embed_html]" id="embed_html"
							rows="20" cols="80"><?php echo $safe_embed_html; ?></textarea></td></tr>
				</tbody></table>
			</div>
			<div class="embed_options embed_options_ctct">
				<br><strong><em>See the support documentation at <a href="https://support.fusebox.fm/article/225-how-to-enable-email-integration-with-constant-contact" target="_blank">
				support.fusebox.fm</a></em></strong>
				<table class="form-table dialog_options_embed"><tbody>
					<tr><th scope="row"><label for="embed_html_ctct">Inline code from Constant Contact</label></th>
					<td><textarea name="spp_player_email[embed_html_ctct]" id="embed_html_ctct"
							rows="5" cols="80"><?php echo $safe_embed_html_ctct; ?></textarea></td></tr>
				</tbody></table>
				<table class="form-table dialog_options_embed_js"><tbody>
					<tr><th scope="row"><label for="embed_js">Universal code from Constant Contact</label></th>
					<td><textarea name="spp_player_email[embed_js]" id="embed_js"
							rows="8" cols="80"><?php echo $embed_js; ?></textarea></td></tr>
				</tbody></table>
			</div>
			<div class="embed_options embed_options_shsp">
				<table class="form-table dialog_options_embed"><tbody>
					<tr><th scope="row"><label for="embed_html">HTML from Sharpspring<br><br></label></th>
					<td><textarea name="spp_player_email[embed_shsp]" id="embed_shsp"
							rows="20" cols="80"><?php echo $safe_embed_shsp; ?></textarea></td></tr>
				</tbody></table>
			</div>
		</div>
	</fieldset>
	
	<div class="spp-featured-cta-link">
		<h4>Enter a relevant URL, such as a link to your resources page, your company services, or your store.</h4>
		<table class="form-table"><tbody>
			<tr><th scope="row"><label for="link">URL</label></th>
			<td><input type="text" name="spp_player_email[link]" id="link" size="80"
				value="<?php echo $link; ?>"></td></tr>
		</tbody></table>
	</div>
	
	<div class="spp-featured-cta-javascript">
		<h4>This feature is for advanced users. Enter the name of the Javascript function
		you would like to initiate upon button click.</h4>
		<table class="form-table"><tbody>
			<tr><th scope="row"><label for="js_function">Javascript function</label></th>
			<td><input type="text" name="spp_player_email[js_function]" id="js_function" size="50"
				value="<?php echo $js_function; ?>"></td></tr>
		</tbody></table>
	</div>
	
</div>
