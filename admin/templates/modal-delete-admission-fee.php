<div id='modalDeleteAdmissionFee' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;"><?= __('Delete Fee') ?></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('/admin.php?page=fees_content&action=delete_fee'); ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<div class="display:flex">
					<p><?= __('Do you want to delete this fee?', 'edusystem'); ?></p>
					<input type="hidden" id="delete_fee_input" name="fee_id" value="">
					<input type="hidden" id="delete_product_id_input" name="product_id" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>
	</div>
</div>