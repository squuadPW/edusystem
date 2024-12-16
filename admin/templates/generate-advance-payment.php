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
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'generate_advance_payment') ? 'nav-tab-active' : ''; ?>"><?= __('Generate quota', 'form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Generate user invoice', 'aes'); ?></h1>
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

				<?php if (isset($payments)) { ?>
					<div style="padding: 10px">
						<table class="wp-list-table widefat fixed posts striped">
							<thead>
								<tr>
									<th>Invoice to generate</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span style="margin-top: -6px;" class='dashicons dashicons-saved'></span> Cuote:
										<?php echo $payments[0]->cuote ?>
									</td>
								</tr>
								<tr>
									<td><span style="margin-top: -6px;" class='dashicons dashicons-clock'></span> Date of
										payment:
										<?php echo $payments[0]->date_next_payment ?>
									</td>
								</tr>
								<tr>
									<td><span style="margin-top: -6px;" class='dashicons dashicons-money-alt'></span> Amount:
										<?php echo wc_price($payments[0]->amount) ?>
									</td>
								</tr>
								<tr>
									<td style="text-align: center">
										<?php if ($_GET['student_available'] && isset($payments)) { ?>
											<button type="submit" class="button button-primary"
												style="margin: 10px"><?php echo $_GET['student_available'] && isset($payments) ? 'Generate order' : 'Search student' ?></button>
										<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" id="amount" name="amount" value="<?php echo $payments[0]->amount ?>" required>
						<input type="hidden" id="product_id" name="product_id"
							value="<?php echo $payments[0]->variation_id ? $payments[0]->variation_id : $payments[0]->product_id ?>"
							required>
					</div>

					<?php if (count($payments) > 1) { ?>
						<div style="padding: 10px">
							<table class="wp-list-table widefat fixed posts striped">
								<thead>
									<tr>
										<th>Next payments (<?php echo (count($payments) - 1) ?> remaining quotas)</th>
										<th></th>
										<th></th>
									</tr>
									<tr>
										<th>Cuote</th>
										<th>Date of payment</th>
										<th>Amount</th>
									</tr>
								</thead>
								<?php foreach ($payments as $key => $payment) { ?>
									<?php if ($key > 0) { ?>
										<tbody>
											<tr>
												<td><?php echo $payment->cuote ?>
												</td>
												<td><?php echo $payment->date_next_payment ?>
												</td>
												<td><?php echo wc_price($payment->amount) ?>
												</td>
											</tr>
										</tbody>
									<?php } ?>
								<?php } ?>
							</table>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div style="padding: 10px">
						<table class="wp-list-table widefat fixed posts striped">
							<tr>
								<th>Payment info</th>
							</tr>
							<tr>
								<td style="text-align: center">No pending payments</td>
							</tr>
						</table>
					</div>
				<?php } ?>
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