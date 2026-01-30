<div id='modalForceEnrollmentSubjects' class='modal' style='display:none'>
	<div class='modal-content'>

		<div class="modal-header">
			<h3 style="font-size:20px;"><?=__('Confirmation of Academic Exception','edusystem')?></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>

		<div class="modal-body" style="margin: 10px; padding: 0px">

			<p><?=__('The student has reached the registration limit for the following subjects:','edusystem')?></p>

			<b>
				<ul id="modal-force-enrollment-subjects-list" ></ul>
			</b>

			<p><?=__('Processing this action will generate a <b>manual exception</b> registration, ignoring the subject registration restrictions.','edusystem')?></p>

			<p for="type"><b><?= __('Do you want to generate the registration anyway?','edusystem')?></b></p>
		</div>

		<div class="modal-footer">
			<button type="submit" class="button button-primary" onclick="confirm_force_enrollment_subjects(this)"><?= __('Yes', 'edusystem'); ?></button>
			<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
		</div>

	</div>
</div>