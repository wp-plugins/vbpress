<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><?php _e("vBPress Settings", "vbpress"); ?></h2>
	<form method="post">
		<?php wp_nonce_field('vbpress_update_settings', 'vbpress_update_settings') ?>
		<h3><?php _e("General Settings", "vbpress"); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="vbpress_enabled"><?php _e('Enable vBPress', 'vbpress'); ?></label></th>
				<td>
					<input type="radio" name="vbpress_enabled" value="1" id="vbpress_enabled_yes" checked="checked" /> <?php _e('Yes', 'vbpress'); ?>&nbsp;&nbsp;
					<input type="radio" name="vbpress_enabled" value="0" id="vbpress_enabled_no" /> <?php _e('No', 'vbpress'); ?><br />
					<?php _e('Enable vBPress integration.', 'vbpress'); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="vbpress_vbulletin_path"><?php _e('vBulletin Path', 'vbpress'); ?></label></th>
				<td>
					<input type="text" name="vbpress_vbulletin_path" id="vbpress_vbulletin_path" style="width:350px;" value="" />
					<br />
					<?php _e('The absolute path of the vBulletin files. For example: /var/www/mysite/forums/', 'vbpress'); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="vbpress_select_example"><?php _e("Select Example", "vbpress"); ?></label></th>
				<td>
					<select id="vbpress_select_example" name="vbpress_select_example">
						<option value="0"><?php _e('Choose me!', 'vbpress'); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<div class="hr-divider"></div>

		<h3><?php _e("Additional Settings", "vbpress"); ?></h3>

		<p style="text-align: left;"><?php _e("This is an example of a description for a specific form section."); ?></p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="vbpress_text_1"><?php _e("Text Field 1", "vbpress"); ?></label></th>
				<td>
					<input type="text" name="vbpress_text_1" id="vbpress_text_1" style="width:350px;" value="" /><br />
					<?php _e("This is an example of text explaining the form field.", "vbpress"); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="vbpress_text_2"><?php _e("Text Field 2", "vbpress"); ?></label></th>
				<td>
					<input type="text" name="vbpress_text_2" id="vbpress_text_2" style="width:350px;" value="" /><br />
					<?php _e("This is an example of text explaining the form field.", "vbpress"); ?>
				</td>
			</tr>
		</table>

		<br/><br/>
		<p class="submit" style="text-align: left;">
			<input type="submit" name="submit" value="<?php _e("Save Settings", "vbpress"); ?>" class="button-primary gf_settings_savebutton"/>
		</p>
	</form>
</div>