<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=report-comissions') ?>"
		class="nav-tab <?= !isset($_GET['section_tab']) ? 'nav-tab-active' : ''; ?>"><?= __('Summary of commissions', 'edusystem'); ?></a>
	<!-- <a href="<?= admin_url('admin.php?page=report-comissions&section_tab=college_allies_comissions') ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'college_allies_comissions') ? 'nav-tab-active' : ''; ?>"><?= __('College commissions & allies', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=report-comissions&section_tab=new_registrations'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'new_registrations') ? 'nav-tab-active' : ''; ?>"><?= __('New registrations', 'edusystem'); ?></a> -->
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
							name="custom">
					<?php } else { ?>
						<input type="text" value="<?= $start_date; ?>" id="inputStartDate"
							style="display: <?= $_POST['typeFilter'] == 'custom' ? 'unset' : 'none' ?>;width:200px;"
							name="custom">
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

		<h3 class='wp-heading-line' style="margin-bottom: -20px;">
			<?= __('Commissions payable to schools', 'edusystem') ?></h3>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_comissions_institute->display() ?>
		</form>

		<h3 class='wp-heading-line' style="margin-bottom: -20px;"><?= __('Allied Commissions', 'edusystem') ?></h3>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_comissions_alliances->display() ?>
		</form>
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