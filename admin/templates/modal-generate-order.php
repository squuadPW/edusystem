<div id='modalGenerateOrder' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
			<h3 style="font-size:20px;" id="title-modal-generate-order-split"></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=generate_order') ?>">
			<div class="modal-body" style="margin: 10px; padding: 0px">
				<p id="message-modal-generate-order-split"></p>

				<div>
					<!-- <label for="amount_order">Amount of order</label><br> -->
					<input type="hidden" name="amount_order" id="amount-order" required step="0.01">
					<input type="hidden" name="order_id_old" id="order_id_old" value="">
				</div>

				<div>
					<label for="date_order">Date of order</label><br>
					<input type="date" name="date_order" id="date-order" value="" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Generate', 'restaurant-system-app'); ?></button>
				<button type="button"
					class="button button-outline-primary modal-close"><?= __('Cancel', 'restaurant-system-app'); ?></button>
			</div>
		</form>
	</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        flatpickr(document.getElementById('date-order'), {
            dateFormat: "m/d/Y",
        });

    });
</script>