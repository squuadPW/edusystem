<div id='modalDownloadMoodleNotes' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;"><?= __('Download Moodle Notes') ?></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="POST" action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=get_moodle_notes'); ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<p><?= __('The scores will be assigned to the bids for the ') . $code_last_cut . ' - ' . $cut_last_cut ?></p>
				<p><?= __('Are you sure you want to download the Moodle notes now?', 'edusystem'); ?></p>
				<p><?= __('Please select the subject for which you want to download the notes.', 'edusystem'); ?></p>
				<label for="subject_id"><?= __('Subject', 'edusystem') ?></label>
				<select name="subject_id" id="subject-id">
					<?php foreach ($available_offers as $offer) { ?>
						<option value="<?= $offer->id ?>"><?= $offer->name . ' - ' . $offer->code_subject ?></option>
					<?php } ?>
				</select>
				<input type="hidden" name="code" value="<?= $code_last_cut ?>" />
				<input type="hidden" name="cut" value="<?= $cut_last_cut ?>" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Go', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('Cancel', 'edusystem'); ?></button>
			</div>
		</form>
	</div>
</div>