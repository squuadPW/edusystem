<?php 

	global $wpdb;
	$table_student_payments = $wpdb->prefix . 'student_payments';

	$amount = 0;
	$student_id = $order->get_meta('student_id');
	$cuote_payment = $order->get_meta('cuote_payment') ?? 0;

	// obtiene el balance
	$student_balance = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, balance FROM `{$wpdb->prefix}student_balance` WHERE student_id = %d",
        $student_id
    ));
	$balance = $student_balance->balance ?? 0;

	// obtiene el valor actual a pagar
	if ($cuote_payment !== 0) {

		$amount = $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE( amount, 0) FROM {$table_student_payments} 
			WHERE id = %d", 
		$cuote_payment) );

	} else {
		$cuote_payment = $wpdb->get_row( $wpdb->prepare(
            "SELECT id, COALESCE( SUM(amount), 0) AS amount_pay FROM {$table_student_payments}
             WHERE student_id = %d AND status_id = 0 AND cuote = (
                        SELECT MIN(cuote) 
                        FROM {$table_student_payments} 
                        WHERE student_id = %d AND status_id = 0
                    )
                ORDER BY num_cuotes DESC
                LIMIT 1;", 
             $student_id,
             $student_id) );
		
		$amount = $cuote_payment->amount_pay;
		$cuote_payment = $cuote_payment->id;
	}
	
	$payments = $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM {$table_student_payments} WHERE student_id = %d AND status_id = 0 AND id != %d",
		$student_id,
		$cuote_payment
	));

?>


<div id='modalStatusPayment' class='modal' style='<?= $balance > 0 ? "display:block" : "display:none" ?>'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-status-payment"></h3>

			<?php if( $balance == 0 || ( $balance > 0 && count($payments) == 0 ) ): ?>
				<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
			<?php endif; ?>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=change_status_payment') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">

				<?php if( $balance == 0 ): ?>
					
					<div class="amount_container" >
						<p><?= __('Amount to pay', 'edusystem') ?>: <strong><?= wc_price($amount) ?></strong> </p>
						<p><?= __('Amount received', 'edusystem') ?>: <strong><?= wc_price( $order->get_total() ) ?></strong> </p>
					</div>

					<p id="message-modal-status-payment"></p>

					<?php
						$filteredArray = [];
						if ( $payments ) {
							$filteredArray = array_filter($payments, function ($item) {
								return $item->status == 'on-hold';
							});
							$filteredArray = array_values($filteredArray);
						}
					?>
					<?php if ($split_payment && count($filteredArray) > 0): ?>
						<div>
							<label for="payment_confirm"><?= __('Choose a payment to confirm:', 'edusystem') ?></label><br>
							<select name="payment_confirm" id="payment-confirm">
								<?php foreach ( $payments as $key => $pay ) {
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
					<?php endif; ?>

				<?php endif; ?>
				
				<?php
					$more_amount = ( $balance > 0 || $order->get_total() > $amount ) && count($payments) > 0;
					$less_amount = $order->get_total() < $amount; 
				?>
				<?php if ( $more_amount || $less_amount ) { ?>
					<div>

						<div id="cuote-credit-select" >
							<label for="cuote_credit">

								<?php if( $more_amount ): ?>

									<?php $amount_credit = ($balance > 0 ) ? $balance : wc_price($order->get_total() - $amount); ?>
									<?= sprintf(__('There is an amount of %s due to the student. The surplus will be credited to:', 'edusystem'), "<strong>" .  $amount_credit . "</strong>" ) ?>
								
								<?php elseif( $less_amount ): ?>
									<?= sprintf(__('An amount below the required threshold has been detected; the outstanding amount of %s will be requested in:', 'edusystem'), "<strong>" . wc_price( $amount - $order->get_total()) . "</strong>" ) ?>
								
								<?php endif; ?>
							</label>
							<br>
							<select name="cuote_credit" id="cuote-credit" style="width: 100%">

								<?php foreach ($payments as $key => $pay) { ?>
									<option value="<?php echo $pay->id ?>">
										<?= __('Quota N°', 'edusystem') ?> <?php echo $pay->cuote . ' - ' . wc_price($pay->amount) ?>
									</option>
									<?php
								}
								?>

								<?php if( $less_amount )?> <option value="new_cuote"> <?= __('New cuote','edusystem') ?> </option>
								
							</select>
						</div>
						
						<?php if( $less_amount ): ?>
							<br/>
							
							<div id="new_coute_date" class="hidden" >
								<label><?= __('Date new cuote','edusystem') ?></label>
								<br>
								<input type="date" name="new_coute_date" >
							</div>
						<?php endif; ?>
					</div>
					
					<br>
				<?php } ?>

				<?php if ( $balance == 0 && $order->get_status() == 'pending') { ?>
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
						<label for="payment_selected"><?= __('Select the payment method used:', 'edusystem') ?></label><br>
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
						<label for="other_payments"><?= __('Payment method used:', 'edusystem') ?></label><br>
						<input type="text" name="other_payments"  style="width: 100%">
					</div>
					<div id="transaction-id">
						<label for="transaction_id"><?= __('Transaction ID:', 'edusystem') ?></label><br>
						<input type="text" name="transaction_id"  style="width: 100%">
					</div><br>
				<?php } ?>

				
				<div class="display:flex">
					<?php if( $balance == 0 ): ?>
						<div>
							<label for="description">
								<?= __('You can add a description about the payment', 'edusystem') ?>
								<strong> <span id="text-modal-status-payment"></span> </strong>
								<?= __('if you wish', 'edusystem') ?>
							</label>
							<textarea style="width: 100%" name="description" value=""></textarea>
						</div>

					<?php endif; ?>

					<input type="hidden" id="order_id" name="order_id" value="<?= $order->get_id(); ?>">
					<input type="hidden" id="status_id" name="status_id" value="completed">
					<input type="hidden" id="split_payment" name="split_payment"
						value="<?= $order->get_meta('split_payment') ?>">
				</div>

				<?php if ( $balance == 0 && $order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
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

				<?php if( $balance == 0 ): ?>
					<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
				<?php endif; ?>

			</div>
		</form>
	</div>
</div>