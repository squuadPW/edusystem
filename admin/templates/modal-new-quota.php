<div id='modalNewQuota' class='modal' style='display:none'>
	<div class='modal-content'>

		<div class="modal-header">
			<h3 style="font-size:20px;"><?=__('New quota','edusystem')?></h3>
			<span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>

		<form method="POST" action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=new_quota_student'); ?>" >

			<input type="hidden" id="student_id" name="student_id" value="" />
			<input type="hidden" id="product_id" name="product_id" value="" />
			<input type="hidden" id="variation_id" name="variation_id" value="" />

			<div class="modal-body">

				<div class="seccion" >
					<div class="group-input" >
						<label><?= __('Amount','edusystem') ?></label>
						<input type="number" id="amount" name="amount" min="0" />
					</div>

					<div class="group-input currency" >
						<label><?= __('Currency','edusystem') ?></label>
						<select name="currency" id="currency" >
							<option value=""><?= __('select a currency','edusystem') ?></option>
							<?php foreach ( get_woocommerce_currencies() as $code => $name ): ?>
								<option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($name . ' (' . $code . ')'); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="group-input" >
					<label><?= __('Date next payment','edusystem') ?></label>
					<input type="date" id="date_next_payment" name="date_next_payment" min="0" />
				</div>
				
			</div>

			<div class="modal-footer">
				<button type="submit" class="button button-primary"><?= __('Add quota', 'edusystem'); ?></button>
				<button type="button" class="button button-outline-primary modal-close"><?= __('No', 'edusystem'); ?></button>
			</div>
		</form>

	</div>
</div>

