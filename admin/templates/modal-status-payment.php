<div id='modalStatusPayment' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
		<h3 style="font-size:20px;" id="title-modal-status-payment"></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<div class="modal-body" style="margin-top:10px;padding:0px;">
		<form method="post" action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=change_status_payment') ?>">
				<p id="message-modal-status-payment"></p>
				<?php if($split_payment) { ?>
					<div>
						<input type="hidden" id="split_payment" name="split_payment" value="1">
						<label for="payment_confirm">Choose a payment to confirm:</label><br>
						<select name="payment_confirm" id="payment-confirm">
							<?php foreach ($payments as $key => $pay) { 
								if($pay->status == 'on-hold') { ?>
								<option value="<?php echo $pay->id ?>"><?php echo $pay->method ?><?php echo $pay->payment_method ? ' - ' .$pay->payment_method : ''?> <?php echo $pay->transaction_id ? '- (' . $pay->transaction_id . ')' : '' ?></option>
							<?php }
							} 
						?>
						</select>
					</div><br>
				<?php } ?>
				<div class="display:flex">
					<div>
						<label for="description">You can add a description about the payment approval if you wish</label>
						<textarea style="width: 100%" name="description" value=""></textarea>
					</div>
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