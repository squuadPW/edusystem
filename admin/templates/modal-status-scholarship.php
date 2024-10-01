<div id='modalStatusScholarship' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-status-scholarship"></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&action=change_status_scholarship') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p id="message-modal-status-scholarship"></p>
					<input type="hidden" id="scholarship_id" name="scholarship_id" value="">
				</div>
				<div>
					<label for="type">Type of scholarship</label><br>
					<select name="type" id="type">
						<option value="1">Complete</option>
						<option value="2">Only fee inscription</option>
						<option value="3">Only fee graduation</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'restaurant-system-app'); ?></button>
				<button type="button"
					class="button button-outline-primary modal-close"><?= __('No', 'restaurant-system-app'); ?></button>
			</div>
		</form>
	</div>
</div>