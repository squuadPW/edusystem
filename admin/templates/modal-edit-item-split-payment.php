<?php 
	
// en caso de que sea un split payment
$order_main = $order->get_meta('split_payment_main_order', true);
if ( $order_main ) : ?>

	<?php	
		$order_main = wc_get_order( $order_main );
		$split_payment_method = $order_main->get_meta('split_payment_method');
		$split_payment_method = json_decode( $split_payment_method, true );
		
		$amount_pending = $split_payment_method['pending_amount'] ?? 0;
	?>

	<div id='modalEditItemSplitPayment' class='modal'>
		<div class='modal-content'>
			<div class="modal-header">
				<h3 style="font-size:20px;"><?= __('Split payment editing notice', 'edusystem') ?></h3>

				<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>

			</div>
				
				<div class="modal-body" style="margin: 10px; padding: 0px">
					
					<p><?= __('Outstanding amount', 'edusystem') ?>: <strong><?= wc_price($amount_pending, [ 'currency' => $order->get_currency() ]) ?></strong> </p>
					
					<?php if($previous_amount): ?>
						<p><?= __('Previous amount', 'edusystem') ?>: <strong><?= wc_price($previous_amount, [ 'currency' => $order->get_currency() ]) ?></strong> </p>
					<?php endif; ?>

					<p><?= __('Amount entered', 'edusystem') ?>: <strong id="amount_entered">0</strong> </p>
					
					<p><?= __('The amount deposited exceeds the outstanding balance.', 'edusystem') ?></p>

					<p><?= sprintf(__('Do you want to record the excess %s as a balance in favor of the user?', 'edusystem'), '<strong id="excess_amount"></strong>' ) ?></p>

					<input type="hidden" id="input_amount_pending" name="amount_pending" value="<?= $amount_pending ?>" data-currency="<?= $order->get_currency() ?>">
					<input type="hidden" id="input_excess_amount" name="excess_amount" value="">

				</div>
				<div class="modal-footer" >
					
					<a id="modalEditItemSplitPaymentYes" class="button button-primary"><?= __('Yes', 'edusystem'); ?></a>

					<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>

				</div>
		</div>
	</div>	
	
<?php endif ?>
