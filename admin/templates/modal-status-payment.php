<div id='modalStatusPayment' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
		<h3 style="font-size:20px;" id="title-modal-status-payment"></h3>
			<span class="modal-close disabled"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<div class="modal-body" style="margin-top:10px;padding:0px;">
		<form method="post" action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=change_status_payment') ?>">
				<div class="display:flex">
					<p id="message-modal-status-payment"></p>
					<input type="hidden" id="order_id" name="order_id" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes','restaurant-system-app'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No','restaurant-system-app'); ?></button>
			</div>
		</form>
	</div>
</div>