<div id='modalDeleteCut' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;"><?= __('Delete cut','edusystem') ?></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('/admin.php?page=add_admin_form_academic_periods_content&action=delete_cut'); ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p><?= __('Do you want to remove this cut?', 'edusystem'); ?></p>
					<input type="hidden" id="delete_cut_id_input" name="cut_id" value="">
					<input type="hidden" id="delete_cut_input" name="cut" value="">
					<input type="hidden" id="delete_period_code_input" name="period_code" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>
	</div>
</div>