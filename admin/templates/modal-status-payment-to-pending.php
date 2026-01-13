<div id='modalStatusPaymentToPending' class='modal' style='display:none'>
	<div class='modal-content'>

		<div class="modal-header">
			<h3 style="font-size:20px;"><?=__('Payment to pending','edusystem')?></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>

		<form id="payment_to_pending_from">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<input type="hidden" id="payment_id" name="payment_id" value="">
				<p for="type"><?= __('Are you sure you want to change the status of this payment to pending? Please state why you want to change it.','edusystem')?></p>
				<textarea id="description" placeholder="<?=__('Write the reason for the change here.','edusystem') ?>" rows="3" style="width: 100%;" required ></textarea>
			</div>

			<div class="modal-footer">
				<button id="payment_to_pending_submit" type="submit" class="button button-primary"><?= __('Yes', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>

	</div>
</div>