<div id='modalStatusPayment' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-status-payment"></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=change_status_payment') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<p id="message-modal-status-payment"></p>
				<?php 
					$filteredArray = array_filter($payments, function ($item) {
						return $item->status == 'on-hold';
					});
					$filteredArray = array_values($filteredArray);
				?>
				<?php if ($split_payment && count($filteredArray) > 0) { ?>
					<div>
						<label for="payment_confirm">Choose a payment to confirm:</label><br>
						<select name="payment_confirm" id="payment-confirm">
							<?php foreach ($payments as $key => $pay) {
								if ($pay->status == 'on-hold') { ?>
									<option value="<?php echo $pay->id ?>">
										<?php echo $pay->method ?>			<?php echo $pay->payment_method ? ' - ' . $pay->payment_method : '' ?>
										<?php echo $pay->transaction_id ? '- (' . $pay->transaction_id . ')' : '' ?></option>
								<?php }
							}
							?>
						</select>
					</div><br>
				<?php } ?>
				<div class="display:flex">
					<div>
						<label for="description">You can add a description about the payment <strong><span id="text-modal-status-payment"></span></strong> if you
							wish</label>
						<textarea style="width: 100%" name="description" value=""></textarea>
					</div>
					<input type="hidden" id="order_id" name="order_id" value="">
					<input type="hidden" id="status_id" name="status_id" value="completed">
					<input type="hidden" id="split_payment" name="split_payment" value="<?= $order->get_meta('split_payment') ?>">
				</div>
				<?php if($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
					<div style="padding: 10px 10px;background-color: #2271b1;border-radius: 10px;margin: 10px 0px;color: white;">
						<p>If you want to finalize the order, because it is a payment agreement and no more payments are needed, you can check this option, which will mark the order as completed and will mark all payments made with the split as completed.</p>
						<div style="display: flex; align-items: center; margin-botom: 10px">
							<input type="checkbox" name="finish_order" style="margin: 0 5px 0px 0px;" />
							<label for="finish_order">Finish order</label>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'aes'); ?></button>
				<button type="button"
					class="button button-outline-primary modal-close"><?= __('No', 'aes'); ?></button>
			</div>
		</form>
	</div>
</div>