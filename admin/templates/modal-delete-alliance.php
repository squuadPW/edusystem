<div id='modalDeleteAlliance' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;"><?= __('Delete Alliance') ?></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_partners_content&action=delete_alliance'); ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p><?= __('Do you want to eliminate this alliance?', 'aes'); ?></p>
					<input type="hidden" id="delete_alliance_id" name="delete_alliance_id" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'aes'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'aes'); ?></button>
			</div>
		</form>
	</div>
</div>