<?php global $wpdb ?>

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
				$filteredArray = [];
				if ($payments) {
					$filteredArray = array_filter($payments, function ($item) {
						return $item->status == 'on-hold';
					});
					$filteredArray = array_values($filteredArray);
				}
				?>
				<?php if ($split_payment && count($filteredArray) > 0) { ?>
					<div>
						<label for="payment_confirm">Choose a payment to confirm:</label><br>
						<select name="payment_confirm" id="payment-confirm">
							<?php foreach ($payments as $key => $pay) {
								if ($pay->status == 'on-hold') { ?>
									<option value="<?php echo $pay->id ?>">
										<?php echo $pay->method ?>
										<?php echo $pay->payment_method ? ' - ' . $pay->payment_method : '' ?>
										<?php echo $pay->transaction_id ? '- (' . $pay->transaction_id . ')' : '' ?>
									</option>
								<?php }
							}
							?>
						</select>
					</div><br>
				<?php }

				$table_student_payments = $wpdb->prefix . 'student_payments';
				$payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id = {$order->get_meta('student_id')} AND status_id = 0");
				array_shift($payments);
				?>
				<?php if (($order->get_meta('cuote_payment') && $order->get_meta('cuote_payment') == 1) && count($payments) > 0) { ?>
					<div>
						<p>It is possible that the client has paid more than the amount of the outstanding installment.
							Therefore, the amount of the installment will be applied and the remaining amount will be
							credited to the next installment.</p>
						<div style="display: flex; align-items: center; margin-bottom: 10px">
							<input type="checkbox" name="paid_more" id="paid-more" style="margin: 0 5px 0px 0px;">
							<label for="paid_more">Yes, the client paid more than the quota.</label>
						</div>
						<div id="amount-credit-input" style="display: none; margin-bottom: 10px">
							<label for="amount_credit">Amount paid:</label><br>
							<input type="number" name="amount_credit" id="amount-credit">
						</div>
						<div id="cuote-credit-select" style="display: none">
							<label for="cuote_credit">Select one installment to be paid the remaining amount:</label><br>
							<select name="cuote_credit" id="cuote-credit" style="width: 100%">
								<?php foreach ($payments as $key => $pay) { ?>
									<option value="<?php echo $pay->id ?>">
										Quota N° <?php echo $pay->cuote . ' - ' . wc_price($pay->amount) ?>
									</option>
									<?php
								}
								?>
							</select>
						</div>
					</div><br>
				<?php } ?>

				<?php
				if ($order->get_status() == 'pending') { ?>
					<?php

					$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
					$filteredArray = [];
					if ($payment_gateways) {
						$filteredArray = array_filter($payment_gateways, function ($item) {
							return $item->id != 'woo_squuad_stripe';
						});
						$filteredArray = array_values($filteredArray);
					}

					?>
					<div>
						<label for="payment_selected">Select the payment method used:</label><br>
						<select name="payment_selected" id="payment_selected" style="width: 100%">
							<?php foreach ($filteredArray as $key => $gateway) { ?>
								<option value="<?= $gateway->id ?>">
									<?= esc_html($gateway->get_title()) ?>
								</option>
								<?php
							}
							?>
						</select>
					</div>
					<div id="other-payments" style="display: none;">
						<label for="other_payments">Payment method used:</label><br>
						<input type="text" name="other_payments"  style="width: 100%">
					</div>
					<div id="transaction-id">
						<label for="transaction_id">Transaction ID:</label><br>
						<input type="text" name="transaction_id"  style="width: 100%">
					</div><br>
				<?php } ?>

				<div class="display:flex">
					<div>
						<label for="description">You can add a description about the payment <strong><span
									id="text-modal-status-payment"></span></strong> if you
							wish</label>
						<textarea style="width: 100%" name="description" value=""></textarea>
					</div>
					<input type="hidden" id="order_id" name="order_id" value="">
					<input type="hidden" id="status_id" name="status_id" value="completed">
					<input type="hidden" id="split_payment" name="split_payment"
						value="<?= $order->get_meta('split_payment') ?>">
				</div>
				<?php if ($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
					<div
						style="padding: 10px 10px;background-color: #2271b1;border-radius: 10px;margin: 10px 0px;color: white;">
						<p>If you want to finalize the order, because it is a payment agreement and no more payments are
							needed, you can check this option, which will mark the order as completed and will mark all
							payments made with the split as completed.</p>
						<div style="display: flex; align-items: center; margin-bottom: 10px">
							<input type="checkbox" name="finish_order" style="margin: 0 5px 0px 0px;" />
							<label for="finish_order">Finish order</label>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Yes', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>
	</div>
</div>