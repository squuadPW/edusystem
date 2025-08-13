<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=report-comissions') ?>"
		class="nav-tab <?= !isset($_GET['section_tab']) ? 'nav-tab-active' : ''; ?>"><?= __('Summary of commissions', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=report-comissions&section_tab=college_allies_comissions') ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'college_allies_comissions') ? 'nav-tab-active' : ''; ?>"><?= __('College commissions & allies', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=report-comissions&section_tab=new_registrations'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'new_registrations') ? 'nav-tab-active' : ''; ?>"><?= __('New registrations', 'edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<?php
			$heading_text = '';
			$name_document = '';

			switch ($_GET['section_tab']) {
				case 'college_allies_comissions':
					$heading_text = __('College and allied commissions', 'edusystem');
					$name_document = __('College and allied comissions.xlsx', 'edusystem');
					break;
				case 'new_registrations':
					$heading_text = __('New enrollments obtained', 'edusystem');
					$name_document = __('New registrations.xlsx', 'edusystem');
					break;
				default:
					$heading_text = __('The summary of all commissions', 'edusystem');
					$name_document = __('Summary comissions.xlsx', 'edusystem');
					break;
			}
			?>
			<h1 class='wp-heading-line'><?= $heading_text ?></h1>
			<input type="hidden" name="name_document" id="name-document" value="<?= $name_document ?>">
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=report-comissions') . ($_GET['section_tab'] ? '&section_tab=' . $_GET['section_tab'] : ''); ?>">
			<div style="width:100%;text-align:right;padding-top:10px;">
				<?php if (wp_is_mobile()) { ?>
					<select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
					<?php } else { ?>
						<select id="typeFilter" name="typeFilter" autocomplete="off">
						<?php } ?>
						<option value="today" <?= $_POST['typeFilter'] == 'today' ? 'selected' : '' ?>>
							<?= __('Today', 'edusystem'); ?>
						</option>
						<option value="yesterday" <?= $_POST['typeFilter'] == 'yesterday' ? 'selected' : '' ?>>
							<?= __('Yesterday', 'edusystem'); ?>
						</option>
						<option value="this-week" <?= $_POST['typeFilter'] == 'this-week' ? 'selected' : '' ?>>
							<?= __('This week', 'edusystem'); ?>
						</option>
						<option value="last-week" <?= $_POST['typeFilter'] == 'last-week' ? 'selected' : '' ?>>
							<?= __('Last week', 'edusystem'); ?>
						</option>
						<option value="this-month" <?= $_POST['typeFilter'] == 'this-month' ? 'selected' : (empty($_POST['typeFilter']) ? 'selected' : '') ?>><?= __('This month', 'edusystem'); ?>
						</option>
						<option value="last-month" <?= $_POST['typeFilter'] == 'last-month' ? 'selected' : '' ?>>
							<?= __('Last month', 'edusystem'); ?>
						</option>
						<option value="custom" <?= $_POST['typeFilter'] == 'custom' ? 'selected' : '' ?>>
							<?= __('Custom', 'edusystem'); ?>
						</option>
					</select>
					<?php if (wp_is_mobile()) { ?>
						<input type="text" value="<?= $start_date; ?>" id="inputStartDate"
							style="display: <?= $_POST['typeFilter'] == 'custom' ? 'unset' : 'none' ?>;width:100%;margin-bottom:5px;"
							name="custom" value="<?= $_POST['custom'] ?? '' ?>">
					<?php } else { ?>
						<input type="text" value="<?= $start_date; ?>" id="inputStartDate"
							style="display: <?= $_POST['typeFilter'] == 'custom' ? 'unset' : 'none' ?>;width:200px;"
							name="custom" value="<?= $_POST['custom'] ?? '' ?>">
					<?php } ?>
					<?php if (wp_is_mobile()): ?>
						<button type="submit"
							class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
						<button type="button" id="export_excel_summary_comissions" class="button button-success"
							style="width:100%;"></span><?= __('Export excel', 'edusystem'); ?></button>
					<?php else: ?>
						<button type="submit"
							class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
						<button type="button" id="export_excel_summary_comissions"
							class="button button-success"></span><?= __('Export excel', 'edusystem'); ?></button>
					<?php endif; ?>
			</div>
		</form>

		<?php if (!isset($_GET['section_tab'])) { ?>
			<h3 class='wp-heading-line' style="margin-bottom: -20px;">
				<?= __('Commissions payable to schools', 'edusystem') ?>
			</h3>
			<form action="" id="post-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_comissions_institute->display() ?>
			</form>

			<h3 class='wp-heading-line' style="margin-bottom: -20px;"><?= __('Allied Commissions', 'edusystem') ?></h3>
			<form action="" id="post-filter" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_comissions_alliances->display() ?>
			</form>
		<?php } ?>

		<?php if (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'college_allies_comissions') { ?>
			<div class="wp-scroll-container">
				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th scope="col" id="student_name"
								class="manage-column column-title column-primary sortable asc">
								<?= __('Nombre del alumno', 'edusystem') ?>
							</th>
							<th scope="col" id="program" class="manage-column column-title sortable asc">
								<?= __('Program', 'edusystem') ?>
							</th>
							<th scope="col" id="institution" class="manage-column column-title sortable asc">
								<?= __('Name of the educational institution', 'edusystem') ?>
							</th>
							<th scope="col" id="student_grade" class="manage-column column-title sortable asc">
								<?= __('Grado del estudiante', 'edusystem') ?>
							</th>
							<th scope="col" id="territory" class="manage-column column-title sortable asc">
								<?= __('Territory', 'edusystem') ?>
							</th>
							<th scope="col" id="partner" class="manage-column column-title sortable asc">
								<?= __('Name of the partner/vendor', 'edusystem') ?>
							</th>
							<th scope="col" id="payment_type" class="manage-column column-title sortable asc">
								<?= __('Payment type', 'edusystem') ?>
							</th>
							<th scope="col" id="initial_fee" class="manage-column column-title sortable asc">
								<?= __('Initial fee USD', 'edusystem') ?>
							</th>
							<th scope="col" id="tuition_amount" class="manage-column column-title sortable asc">
								<?= __('Tuition amount paid USD', 'edusystem') ?>
							</th>
							<th scope="col" id="total_amount" class="manage-column column-title sortable asc">
								<?= __('Total amount paid USD', 'edusystem') ?>
							</th>
							<th scope="col" id="payment_date" class="manage-column column-title sortable asc">
								<?= __('Payment date', 'edusystem') ?>
							</th>
							<th scope="col" id="income_account" class="manage-column column-title sortable asc">
								<?= __('Income account', 'edusystem') ?>
							</th>
							<?php foreach ($alliances_headers as $id => $name) { ?>
								<th scope="col" id="alliance_<?= $id ?>"><?= $name ?></th>
							<?php } ?>
							<th scope="col" id="observations" class="manage-column column-title sortable asc">
								<?= __('Administrative observations', 'edusystem') ?>
							</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php foreach ($payments_data as $key => $student) { ?>
							<tr id="post-<?= $key ?>"
								class="iedit author-self post-<?= $key ?> post-<?= $key ?> type-post status-publish has-post-thumbnail hentry">
								<td class="title column-title title column-title has-row-actions column-primary"
									data-colname="<?= __('Nombre del alumno', 'edusystem') ?>">
									<?= $student->student_name ?>
									<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
								</td>
								<td class="title column-title has-row-actions column-primary"
									data-colname="<?= __('Program', 'edusystem') ?>">
									<?= $student->program ?>
								</td>
								<td class="title column-title"
									data-colname="<?= __('Name of the educational institution', 'edusystem') ?>">
									<?= $student->institute_name ?>
								</td>
								<td class="title column-title" data-colname="<?= __('Grado del estudiante', 'edusystem') ?>">
									<?= $student->grade ?>
								</td>
								<td class="title column-title" data-colname="<?= __('Territory', 'edusystem') ?>">
									<?= $student->country ?>
								</td>
								<td class="title column-title"
									data-colname="<?= __('Name of the partner/vendor', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Payment type', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Initial fee USD', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Tuition amount paid USD', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Total amount paid USD', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Payment date', 'edusystem') ?>">
									...
								</td>
								<td class="title column-title" data-colname="<?= __('Income account', 'edusystem') ?>">
									...
								</td>
								<?php foreach ($alliances_headers as $id => $name) { ?>
									<td>
										<?= isset($payment->alliances_fees->{"alliance_" . $id}) ? $payment->alliances_fees->{"alliance_" . $id} : '0.00' ?>
									</td>
								<?php } ?>
								<td class="title column-title"
									data-colname="<?= __('Administrative observations', 'edusystem') ?>">
									...
								</td>
							</tr>
						<?php } ?>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
			</div>
		<?php } ?>

		<?php if (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'new_registrations') { ?>

		<?php } ?>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		flatpickr(document.getElementById('inputStartDate'), {
			mode: "range",
			dateFormat: "m/d/Y",
			defaultDate: ['<?= $date_array[0] ?>', '<?= $date_array[1] ?>'],
		});

	});
</script>