<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Payments for Review', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=all_payments'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments') ? 'nav-tab-active' : ''; ?>"><?= __('All Payments', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_alliances') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for alliances', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for institutes', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'generate_advance_payment') ? 'nav-tab-active' : ''; ?>"><?= __('Manage payments', 'edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Generate user invoice', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end">
			<?php
			include(plugin_dir_path(__FILE__) . 'connections-student.php');
			?>
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=generate_payment'); ?>">
			<div class="form-group" style="padding: 0px 10px 10px 10px; text-align: center">
				<label for="id_document">Search by student email or id document</label> <br>
				<input style="width: 100%" type="text" id="id_document" name="id_document" <?php echo $_GET['student_available'] == 1 ? 'readonly' : '' ?> value="<?php echo $_GET['id_document'] ?>"
					required> <br>
				<input type="hidden" id="generate" name="generate"
					value="<?php echo $_GET['student_available'] == 1 && isset($payments) ? true : false ?>" required>

				<?php if ($_GET['student_available']) { ?>
					<button type="submit" class="button button-primary-outline" name="cancel" value="1"
						style="margin: 10px">Cancel</button>
				<?php } else { ?>
					<button type="submit" class="button button-primary"
						style="margin: 10px"><?php echo $_GET['student_available'] && isset($payments) ? 'Generate order' : 'Search student' ?></button>
				<?php } ?>
			</div>

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

				<div style="padding: 10px">
					<table class="wp-list-table widefat fixed posts striped">
						<thead>
							<tr>
								<th colspan="2">Payments</th>
								<th colspan="10" style="text-align: end">
									<?php 
									$fee_registration_exists = get_payments($student_id, FEE_INSCRIPTION);
									$fee_graduation_exists = get_payments($student_id, FEE_GRADUATION);
									
									if (!$fee_registration_exists) { ?>
										<button type="submit" class="button button-secondary"
											style="margin: 4px" onclick="return confirm('Are you sure?');" name="generate_fee_registration" value="1">Generate fee registration</button>
									<?php } ?>
									
									<?php if (!$fee_graduation_exists) { ?>
										<button type="submit" class="button button-secondary"
											style="margin: 4px" onclick="return confirm('Are you sure?');" name="generate_fee_graduation" value="1">Generate fee graduation</button>
									<?php } ?>
									<button type="submit" class="button button-success"
										style="margin: 4px" onclick="return confirm('Are you sure?');">Generate next quota order</button>
									<input type="hidden" id="amount" name="amount"
										value="<?php echo $order_amount ?>" required>
									<input type="hidden" id="product_id" name="product_id"
										value="<?php echo $order_variation_id ? $order_variation_id : $order_product_id ?>"
										required>
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
							<?php foreach ($payments as $key => $payment) { ?>
								<tr>
									<td colspan="4">
									<?php 
									$product = wc_get_product($payment->variation_id ? $payment->variation_id : $payment->product_id);
									$name_product = $product->get_name();
									?>
									#<?= $payment->cuote ?> - <?= $name_product ?>
									</td>
									<td colspan="2" style="text-align: center;">
										<?php if($payment->status_id == 1) { ?>
											<?= date('m/d/Y', strtotime(in_array($payment->product_id, [FEE_INSCRIPTION, FEE_GRADUATION]) ? $payment->date_payment : $payment->date_next_payment)); ?>
											<input type="hidden" name="date_payment[]" class="date_payment" value="<?= in_array($payment->product_id, [FEE_INSCRIPTION, FEE_GRADUATION]) ? $payment->date_payment : $payment->date_next_payment; ?>" />
										<?php } else { ?>
											<input type="date" name="date_payment[]" class="date_payment" value="<?= in_array($payment->product_id, [FEE_INSCRIPTION, FEE_GRADUATION]) ? $payment->date_payment : $payment->date_next_payment; ?>" />
										<?php } ?>
									</td>
									<td colspan="2" style="text-align: center;"><?= $payment->status_id == 1 ? date('m/d/Y', strtotime($payment->date_payment)) : 'N/A'; ?>
									</td>
									<td colspan="2" style="text-align: center;">
										<?php if($payment->status_id == 1) { ?>
											<?= wc_price($payment->amount) ?>
											<input type="hidden" name="amount_payment[]" class="amount_payment" value="<?= $payment->amount ?>" />
										<?php } else { ?>
											<input type="number" step="0.01" name="amount_payment[]" class="amount_payment" value="<?= $payment->amount ?>" />
										<?php } ?>
									</td>
									<td style="text-align: center;" colspan="2"><?= $payment->status_id == 1 ? '<a target="_blank" href="' . admin_url('admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $payment->order_id) . '"><span style="color: green">View payment</span></a>' : '<span style="color: gray">To pay</span>'; ?></td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="6">Payments</th>
								<th colspan="6" style="text-align: end">
									<div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
										<div style="flex: 1;">
											<textarea name="description_payment_log" id="description_payment_log" placeholder="Description" style="width: 100%; min-width: 300px;"></textarea>
										</div>
										<div style="flex-shrink: 0;">
											<button type="submit" class="button button-success" name="save_changes" value="1"
											style="margin: 10px" onclick="return confirm('Are you sure?');">Save changes</button>
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
											<?= $payment_log->difference >= 0 ? '+' : ''?><?= wc_price($payment_log->difference) ?>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
});
</script>