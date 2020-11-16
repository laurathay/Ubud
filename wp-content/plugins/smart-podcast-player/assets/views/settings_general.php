<?php
// Default values for the general settings page
$defaults = array(
		'license_key' => '',
	);
$saved_options = get_option( 'spp_player_general', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
?>

<table class="form-table"><tbody><tr>
	<th scope="row">License Key:</th>
	<td><input type="text" name="spp_player_general[license_key]" value="<?php echo $license_key ?>" size="65"></td>
</tr></tbody></table>
<p class="description"><small>
	Your Fusebox downloads are accessible in the customer center at <a href="https://my.fusebox.fm" target="_blank">my.Fusebox.fm</a>. If you have any difficulty accessing your downloads, please email us at <a href="mailto:help@fusebox.fm">help@fusebox.fm</a>.
</small></p>
