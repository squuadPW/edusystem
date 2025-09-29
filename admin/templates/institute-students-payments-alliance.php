<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Payments of student', 'edusystem'); ?></h1>
		</div>

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>
		<div style="display:flex;width:100%;">
			<a class="button button-outline-primary"
				href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-students&institute_id=' . $institute->id); ?>"><?= __('Back', 'edusystem'); ?></a>
		</div>
		<div>

			<div style="padding: 10px">
				<table class="wp-list-table widefat fixed posts striped">
					<thead>
						<tr>
							<th>Student details</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-businessperson'></span>
								Name: <?php echo $student->name ?>
								<?php echo $student->middle_name ?>
								<?php echo $student->last_name ?>
								<?php echo $student->middle_last_name ?>
							</td>
						</tr>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-email'></span> Email:
								<?php echo $student->email ?>
							</td>
						</tr>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-phone'></span> Phone:
								<?php echo $student->phone ?>
							</td>
						</tr>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-location-alt'></span>
								Country: <?php echo $student->country ?>
							</td>
						</tr>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-text-page'></span>
								Academic period: <?php echo $student->academic_period ?></td>
						</tr>
						<tr>
							<td><span style="margin-top: -6px;" class='dashicons dashicons-location-alt'></span> Initial
								cut: <?php echo $student->initial_cut ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding: 10px">
				<table class="wp-list-table widefat fixed posts striped">
					<thead>
						<tr>
							<th colspan="4">Payment</th>
							<th colspan="2" style="text-align: center;">Expected payment date</th>
							<th colspan="2" style="text-align: center;">Date of payment made</th>
							<th colspan="2" style="text-align: center;">Amount</th>
							<th colspan="2" style="text-align: center;">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($payments as $payment) { ?>
							<tr>
								<td colspan="4">
									<div style="display: flex; align-items: center; gap: 10px;">
										<?php
										$product = wc_get_product($payment->variation_id ?: $payment->product_id);
										$name_product = $product->get_name();
										?>
										<span>#<?= $payment->cuote ?> - <?= $name_product ?></span>
									</div>
								</td>
								<td colspan="2" style="text-align: center;">
									<?=
										date('m/d/Y', strtotime(in_array($payment->product_id, [$product_id_registration, $product_id_graduation]) ? $payment->date_payment : $payment->date_next_payment))
										?>
								</td>
								<td colspan="2" style="text-align: center;">
									<?= $payment->status_id == 1 ? date('m/d/Y', strtotime($payment->date_payment)) : 'N/A'; ?>
								</td>
								<td colspan="2" style="text-align: center;">
									<?=
										wc_price($payment->amount) . '<input type="hidden" name="amount_payment[]" class="amount_payment" value="' . $payment->amount . '" />'
										?>
								</td>
								<td colspan="2" style="text-align: center;">
									<?= $payment->status_id == 1
										? '<span style="color: green">Paid</span>'
										: '<span style="color: gray">To pay</span>';
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>