<div id='modalStatusScholarship' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-status-scholarship"></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&action=change_status_scholarship') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p id="message-modal-status-scholarship"></p>
					<input type="hidden" id="scholarship_id" name="scholarship_id" value="">
				</div>
				<div>
					<label for="type">Please indicate what the scholarship covers for the student</label><br><br>
					<input type="checkbox" name="fee_inscription" value="1"> Fee inscription<br>
					<input type="checkbox" name="program" value="1"> Program<br>
					<input type="checkbox" name="fee_graduation" value="1"> Fee graduation<br>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'aes'); ?></button>
				<button type="button"
					class="button button-outline-primary modal-close"><?= __('No', 'aes'); ?></button>
			</div>
		</form>
	</div>
</div>