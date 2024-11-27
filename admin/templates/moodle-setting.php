<div class="wrap">
	<h1 class="wp-heading-line"><?= __('Moodle Settings','aes'); ?></h1>
	<form method="post" action="<?= admin_url('admin.php?page=moodle-setting&action=save_setting') ?>"> 
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="input_id"><?= __('Moodle URL','aes'); ?></label></th>
					<td><input name="moodle_url" type="text" id="moodle_url" value="<?= get_option('moodle_url'); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="input_id"><?= __('Moodle Token','aes'); ?></label></th>
					<td><input name="moodle_token" type="text" id="moodle_token" value="<?= get_option('moodle_token'); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td style="text-align:right;">
						<button class="button button-primary" type="submit"><?= __('Save Changes','aes'); ?></button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>