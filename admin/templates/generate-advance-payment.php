<?php
include(plugin_dir_path(__FILE__) . 'topbar-payments.php');
?>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Manage payments', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end">
			<?php
			include(plugin_dir_path(__FILE__) . 'connections-student.php');
			?>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=generate_payment'); ?>">
			<?php if (!$_GET['student_available']) { ?>
				<div class="form-group" style="padding: 0px 10px 10px 10px; text-align: center">
					<label for="hc"><b><?= __('Search student', 'edusystem'); ?></b></label><br>
					<select class="js-example-basic" name="student_id"></select>
					<br>
					<button type="submit" class="button button-primary" style="margin: 10px">Load payments</button>
				</div>
			<?php } ?>

			<input type="hidden" id="id_document" name="id_document" value="<?= $_GET['id_document'] ?>" required>
			<input type="hidden" id="generate" name="generate"
				value="<?php echo $_GET['student_available'] == 1 && isset($payments) ? true : false ?>" required>

			<?php if ($_GET['student_available']) { ?>
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

				<div style="display:flex;width:100%;justify-content:center">
					<button type="submit" class="button button-primary-outline" name="cancel" value="1"
						style="margin: 10px">Change student</button>
				</div>

				<div style="padding: 10px">
					<table class="wp-list-table widefat fixed posts striped">
						<thead>
							<tr>
								<th colspan="2">Payments</th>
								<th colspan="10" style="text-align: end">
									<?php
									$payments_status = [
										'registration' => get_payments($student_id, FEE_INSCRIPTION),
										'graduation' => get_payments($student_id, FEE_GRADUATION),
										'pending' => get_payments($student_id)
									];

									$buttons = [
										['condition' => !$payments_status['registration'], 'name' => 'generate_fee_registration', 'label' => 'Generate fee registration'],
										['condition' => !$payments_status['graduation'], 'name' => 'generate_fee_graduation', 'label' => 'Generate fee graduation'],
										['condition' => $payments_status['pending'] == 2, 'name' => '', 'label' => 'Generate next quota order', 'class' => 'button-success']
									];

									foreach ($buttons as $button) {
										if ($button['condition']) { ?>
											<button type="submit" class="button <?= $button['class'] ?? 'button-secondary' ?>"
												style="margin: 4px" onclick="return confirm('Are you sure?');"
												name="<?= $button['name'] ?>" value="1">
												<?= $button['label'] ?>
											</button>
										<?php }
									} ?>

									<input type="hidden" id="amount" name="amount" value="<?= $order_amount ?>" required>
									<input type="hidden" id="product_id" name="product_id"
										value="<?= $order_variation_id ?: $order_product_id ?>" required>
								</th>
							</tr>
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
											<?php if ($payment->status_id == 0) { ?>
												<button type="submit" class="button button-danger" name="delete_quote" value="<?= $payment->id ?>" onclick="return confirm('Are you sure?');">
													<span class='dashicons dashicons-trash'></span>
												</button>
											<?php } ?>

											<?php 
											$product = wc_get_product($payment->variation_id ?: $payment->product_id);
											$name_product = $product->get_name();
											?>
											<span>#<?= $payment->cuote ?> - <?= $name_product ?></span>
										</div>
									</td>
									<td colspan="2" style="text-align: center;">
										<?= $payment->status_id == 1
											? date('m/d/Y', strtotime(in_array($payment->product_id, [FEE_INSCRIPTION, FEE_GRADUATION]) ? $payment->date_payment : $payment->date_next_payment))
											: '<input type="date" name="date_payment[]" class="date_payment" value="' . ($payment->product_id == FEE_INSCRIPTION || $payment->product_id == FEE_GRADUATION ? $payment->date_payment : $payment->date_next_payment) . '" />';
										?>
									</td>
									<td colspan="2" style="text-align: center;">
										<?= $payment->status_id == 1 ? date('m/d/Y', strtotime($payment->date_payment)) : 'N/A'; ?>
									</td>
									<td colspan="2" style="text-align: center;">
										<?= $payment->status_id == 1
											? wc_price($payment->amount) . '<input type="hidden" name="amount_payment[]" class="amount_payment" value="' . $payment->amount . '" />'
											: '<input type="number" step="0.01" name="amount_payment[]" class="amount_payment" value="' . $payment->amount . '" />';
										?>
									</td>
									<td colspan="2" style="text-align: center;">
										<?= $payment->status_id == 1
											? '<a target="_blank" href="' . admin_url('admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $payment->order_id) . '"><span style="color: green">View payment</span></a>'
											: '<span style="color: gray">To pay</span>';
										?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6">Payments</th>
								<th colspan="6" style="text-align: end">
									<div class="payment-actions">
										<div class="payment-description">
											<textarea name="description_payment_log" id="description_payment_log"
												placeholder="Description"></textarea>
										</div>
										<div>
											<button type="submit" class="button button-success" name="save_changes"
												value="1" onclick="return confirm('Are you sure?');">Save changes</button>
										</div>
									</div>
								</th>
							</tr>
						</tfoot>
					</table>
				</div>

				<div style="padding: 10px">
					<table class="wp-list-table widefat fixed posts striped">
						<thead>
							<tr>
								<th colspan="12" style="text-align: center;">Payments log</th>
							</tr>
							<tr>
								<th colspan="2" style="text-align: center;">Created at</th>
								<th colspan="2" style="text-align: center;">User</th>
								<th colspan="1" style="text-align: center;">Old amount</th>
								<th colspan="1" style="text-align: center;">New amount</th>
								<th colspan="1" style="text-align: center;">Diff</th>
								<th colspan="5" style="text-align: center;">Description</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($payments_log as $key => $payment_log) { ?>
								<tr>
									<td style="text-align: center;" colspan="2">
										<?= date('m/d/Y', strtotime($payment_log->created_at)) ?>
									</td>
									<td style="text-align: center;" colspan="2">
										<?= get_user_by('id', $payment_log->user_id)->first_name ?>
										<?= get_user_by('id', $payment_log->user_id)->last_name ?>
									</td>
									<td style="text-align: center;" colspan="1">
										<?= wc_price($payment_log->old_amount) ?>
									</td>
									<td style="text-align: center;" colspan="1">
										<?= wc_price($payment_log->new_amount) ?>
									</td>
									<td style="text-align: center;" colspan="1">
										<span style="color: <?= $payment_log->difference >= 0 ? 'green' : 'red' ?>">
											<?= $payment_log->difference >= 0 ? '+' : '' ?>
											<?= wc_price($payment_log->difference) ?>
										</span>
									</td>
									<td style="text-align: center;" colspan="5">
										<?= $payment_log->description ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } else if (!$_GET['student_available'] && $_GET['id_document']) { ?>
					<div style="padding: 10px">
						<table class="wp-list-table widefat fixed posts striped">
							<tr>
								<th>Student details</th>
							</tr>
							<tr>
								<td style="text-align: center">This student not exist</td>
							</tr>
						</table>
					</div>
			<?php } ?>
		</form>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const form = document.querySelector('form');
		form.addEventListener('keypress', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				return false;
			}
		});
	});
</script>