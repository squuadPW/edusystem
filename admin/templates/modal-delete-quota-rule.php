<div id='modalDeleteQuotaRule' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;"><?= __('Delete quota rule') ?></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('/admin.php?page=add_admin_form_payments_plans_content&action=delete_quota_rule'); ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p><?= __('Do you want to delete this rule for the quota?', 'edusystem'); ?></p>
					<input type="hidden" id="delete_quota_rule_input" name="quota_rule_id" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>
	</div>
</div>