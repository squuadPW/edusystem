<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Payments for Review', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=all_payments'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments') ? 'nav-tab-active' : ''; ?>"><?= __('All Payments', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_alliances') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for alliances', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for institutes', 'form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'generate_advance_payment') ? 'nav-tab-active' : ''; ?>"><?= __('Manage payments', 'form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Generate user invoice', 'aes'); ?></h1>
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
								<th>Payments</th>
								<th colspan="5" style="text-align: end">
									<button type="submit" class="button button-primary"
										style="margin: 10px" onclick="return confirm('Are you sure?');">Generate next quota order</button>
									<input type="hidden" id="amount" name="amount"
										value="<?php echo $order_amount ?>" required>
									<input type="hidden" id="product_id" name="product_id"
										value="<?php echo $order_variation_id ? $order_variation_id : $order_product_id ?>"
										required>
								</th>
							</tr>
							<tr>
								<th>Cuote</th>
								<th>Expected payment date</th>
								<th>Date of payment made</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Order ID</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($payments as $key => $payment) { ?>
								<tr>
									<td><?= $payment->cuote ?>
									</td>
									<td><?= $payment->product_id == AES_FEE_INSCRIPTION ? $payment->date_payment : $payment->date_next_payment; ?>
									</td>
									<td><?= $payment->status_id == 1 ? $payment->date_payment : 'N/A'; ?>
									</td>
									<td><?= wc_price($payment->amount) ?>
									</td>
									<td><?= $payment->status_id == 1 ? 'Completed' : 'Pending'; ?></td>
									<td>
										<?php if($payment->status_id == 1) { ?>
											<a target="_blank"
											href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $payment->order_id) ?>"><?= $payment->order_id ?></a>
										<?php } else { ?>
											N/A
										<?php } ?>
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