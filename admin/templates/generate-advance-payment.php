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
					<button type="submit" class="button button-primary" style="margin: 10px"><?= __('Load payments', 'edusystem'); ?></button>
				</div>
			<?php } ?>

			<input type="hidden" id="id_document" name="id_document" value="<?= $_GET['id_document'] ?>" required>
			<input type="hidden" id="generate" name="generate"
				value="<?= $_GET['student_available'] == 1 && isset($payments) ? true : false ?>" required>

			<?php if ($_GET['student_available']) { ?>
				<div style="padding: 10px">
					<div
						style="font-family: Arial, sans-serif; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #ffffff;">
						<h2 style="text-align: center; color: #333; margin-bottom: 25px; font-weight: bold;"><?= __('Student Details', 'edusystem'); ?></h2>

						<div style="display: flex; align-items: center; margin-bottom: 15px;">
							<span class="dashicons dashicons-businessperson no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Name:', 'edusystem'); ?></strong>
								<?= $student->name ?>
								<?= $student->middle_name ?>
								<?= $student->last_name ?>
								<?= $student->middle_last_name ?>
							</p>
						</div>

						<div style="display: flex; align-items: center; margin-bottom: 15px;">
							<span class="dashicons dashicons-email no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Email:', 'edusystem'); ?></strong>
								<?= $student->email ?>
							</p>
						</div>

						<div style="display: flex; align-items: center; margin-bottom: 15px;">
							<span class="dashicons dashicons-phone no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Phone:', 'edusystem'); ?></strong>
								<?= $student->phone ?>
							</p>
						</div>

						<div style="display: flex; align-items: center; margin-bottom: 15px;">
							<span class="dashicons dashicons-location-alt no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Country:', 'edusystem'); ?></strong>
								<?= WC()->countries->countries[$student->country] ?? $student->country ?>
							</p>
						</div>

						<div style="display: flex; align-items: center; margin-bottom: 15px;">
							<span class="dashicons dashicons-text-page no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Academic Period:', 'edusystem'); ?></strong>
								<?= $student->academic_period ?>
							</p>
						</div>

						<div style="display: flex; align-items: center;">
							<span class="dashicons dashicons-location-alt no-vertical"
								style="color: #0073aa; font-size: 24px; margin-right: 15px;"></span>
							<p style="margin: 0; font-size: 16px; color: #555;">
								<strong style="color: #333;"><?= __('Initial Cohort:', 'edusystem'); ?></strong>
								<?= $student->initial_cut ?>
							</p>
						</div>
					</div>
				</div>

				<div style="display:flex;width:100%;justify-content:center">
					<button type="submit" class="button button-primary-outline" name="cancel" value="1"
						style="margin: 10px"><?= __('Change student', 'edusystem'); ?></button>
				</div>

				<div class="table-controls-header">
					<h2 class="section-title"><?= __('Student Payments Overview', 'edusystem'); ?></h2>

					<div class="header-actions">
						<?php
						$payments_status = [
							'registration' => get_fee_paid($student->id, 'registration'),
							'graduation' => get_fee_paid($student->id, 'graduation'),
							'pending' => get_payments($student->id)
						];

						$buttons = [
							['condition' => !$payments_status['registration'], 'name' => 'generate_fee_registration', 'label' => __('Generate registration fee order', 'edusystem')],
							['condition' => !$payments_status['graduation'], 'name' => 'generate_fee_graduation', 'label' => __('Generate graduation fee order', 'edusystem')],
							['condition' => $payments_status['pending'] == 2, 'name' => '', 'label' => __('Generate next installment order', 'edusystem'), 'class' => 'button-success']
						];

						foreach ($buttons as $button) {
							if ($button['condition']) { ?>
								<button type="submit" class="button <?= $button['class'] ?? 'button-secondary' ?>"
									onclick="return confirm('<?= __('Are you sure?', 'edusystem'); ?>');" name="<?= $button['name'] ?>" value="1">
									<?= $button['label'] ?>
								</button>
							<?php }
						}
						?>
						<input type="hidden" id="amount" name="amount" value="<?= $order_amount ?? '' ?>" required>
						<input type="hidden" id="product_id" name="product_id"
							value="<?= $order_variation_id ?: ($order_product_id ?? '') ?>" required>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped posts" style="border-radius: 0;">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-primary column-payment-header">
								<?= __('Payment', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-expected-header">
								<?= __('Expected payment date', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-date-payment-header">
								<?= __('Payment date', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-amount-header">
								<?= __('Amount', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-status-header">
								<?= __('Status', 'edusystem'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$reg_id = get_fee_product_id($student->id, 'registration');
						$grad_id = get_fee_product_id($student->id, 'graduation');
						$special_products = [$reg_id, $grad_id];

						foreach ($payments as $payment) {
							$is_paid = ($payment->status_id == 1);

							$p_id = $payment->variation_id ?: $payment->product_id;
							$product = wc_get_product($p_id);
							$name_product = $product ? $product->get_name() : '';

							$use_payment_date = in_array($payment->product_id, $special_products) || empty($payment->date_next_payment);
							$effective_date = $use_payment_date ? $payment->date_payment : $payment->date_next_payment;

							$display_date_formatted = date('m/d/Y', strtotime($effective_date));
							$input_date_formatted = date('Y-m-d', strtotime($effective_date));
							?>
							<tr>
								<td class="column-primary" data-colname="<?= __('Payment', 'edusystem'); ?>">
									<div style="display: flex; align-items: center; gap: 10px;">
										<?php if ($payment->status_id == 0) { ?>
											<button type="submit" class="button button-danger" name="delete_quote"
												value="<?= $payment->id ?>" onclick="return confirm('<?= __('Are you sure?', 'edusystem'); ?>');">
												<span class='dashicons dashicons-trash'></span>
											</button>
										<?php } ?>
										<span>#<?= $payment->cuote ?> - <?= $name_product ?></span>
									</div>
									<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
								</td>

								<td data-colname="<?= __('Expected payment date', 'edusystem'); ?>">
									<?php if ($is_paid) { ?>
										<?= $display_date_formatted; ?>
										<input type="hidden" name="date_payment[]" value="<?= $input_date_formatted; ?>" />
									<?php } else { ?>
										<input type="date" name="date_payment[]" class="date_payment"
											value="<?= $input_date_formatted; ?>" />
									<?php } ?>
								</td>

								<td data-colname="<?= __('Payment date', 'edusystem'); ?>">
									<?= $is_paid ? date('m/d/Y', strtotime($payment->date_payment)) : 'N/A'; ?>
								</td>

								<td data-colname="<?= __('Amount', 'edusystem'); ?>">
									<?php if ($is_paid) { ?>
										<?= wc_price($payment->amount); ?>
										<input type="hidden" name="amount_payment[]" class="amount_payment"
											value="<?= $payment->amount; ?>" />
									<?php } else { ?>
										<input type="number" step="0.01" name="amount_payment[]" class="amount_payment"
											value="<?= $payment->amount; ?>" />
									<?php } ?>
								</td>

								<td data-colname="<?= __('Status', 'edusystem'); ?>">
									<?php if ($is_paid) { ?>
										<a target="_blank"
											href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $payment->order_id); ?>">
											<span style="color: green"><?= __('View payment', 'edusystem') ?></span>
										</a>
									<?php } else { ?>
										<span style="color: gray"><?= __('Pending', 'edusystem') ?></span>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

				<div class="table-actions-container">
					<div class="payment-actions">
						<div class="payment-description">
							<textarea name="description_payment_log" id="description_payment_log"
								placeholder="<?= __('Description', 'edusystem'); ?>"></textarea>
						</div>
						<div>
							<button type="submit" class="button button-success" name="save_changes" value="1"
								onclick="return confirm('<?= __('Are you sure?', 'edusystem'); ?>');"><?= __('Save changes', 'edusystem') ?></button>
						</div>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
					<thead>
						<tr>
							<th style="background-color: #ffffff !important" scope="col"
								class="manage-column column-payments-log-header">
								<?= __('Payments log', 'edusystem'); ?>
							</th>
						</tr>
						<tr>
							<th scope="col" class="manage-column column-primary column-created-at-header">
								<?= __('Created at', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-user-header">
								<?= __('User', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-old-amount-header">
								<?= __('Old amount', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-new-amount-header">
								<?= __('New amount', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-diff-header">
								<?= __('Diff', 'edusystem'); ?>
							</th>
							<th scope="col" class="manage-column column-description-header">
								<?= __('Description', 'edusystem'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($payments_log as $key => $payment_log) { ?>
							<tr>
								<td class="column-primary" data-colname="<?= __('Created At', 'edusystem'); ?>">
									<?= date('m/d/Y', strtotime($payment_log->created_at)) ?>
									<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
								</td>
								<td data-colname="<?= __('User', 'edusystem'); ?>">
									<?= get_user_by('id', $payment_log->user_id)->first_name ?>
									<?= get_user_by('id', $payment_log->user_id)->last_name ?>
								</td>
								<td data-colname="<?= __('Old amount', 'edusystem'); ?>">
									<?= wc_price($payment_log->old_amount) ?>
								</td>
								<td data-colname="<?= __('New amount', 'edusystem'); ?>">
									<?= wc_price($payment_log->new_amount) ?>
								</td>
								<td data-colname="<?= __('Diff', 'edusystem'); ?>">
									<span style="color: <?= $payment_log->difference >= 0 ? 'green' : 'red' ?>">
										<?= $payment_log->difference >= 0 ? '+' : '' ?>
										<?= wc_price($payment_log->difference) ?>
									</span>
								</td>
								<td data-colname="<?= __('Description', 'edusystem'); ?>">
									<?= $payment_log->description ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } else if (!$_GET['student_available'] && $_GET['id_document']) { ?>
					<div style="padding: 10px">
						<table class="wp-list-table widefat fixed posts striped">
							<tr>
								<th><?= __('Student details', 'edusystem') ?></th>
							</tr>
							<tr>
								<td style="text-align: center"><?= __('This student does not exist', 'edusystem') ?></td>
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