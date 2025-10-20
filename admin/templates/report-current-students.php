<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=report-students') ?>"
		class="nav-tab <?= !isset($_GET['section_tab']) ? 'nav-tab-active' : ''; ?>"><?= __('Active students', 'edusystem'); ?>
		(<strong><?= $total_count_active ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=current') ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'current') ? 'nav-tab-active' : ''; ?>"><?= __('Students studying', 'edusystem'); ?>
		(<strong><?= $total_count_current ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=pending_electives'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending_electives') ? 'nav-tab-active' : ''; ?>"><?= __('Pending electives', 'edusystem'); ?>
		(<strong><?= $total_count_pending_electives ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=non-enrolled'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'non-enrolled') ? 'nav-tab-active' : ''; ?>"><?= __('Non-enrolled students', 'edusystem'); ?>
		(<strong><?= $total_count_non_enrolled ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=pending-graduation'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending-graduation') ? 'nav-tab-active' : ''; ?>"><?= __('Pending graduation', 'edusystem'); ?>
		(<strong><?= $total_count_pending_graduation ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=graduated'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'graduated') ? 'nav-tab-active' : ''; ?>"><?= __('Graduated', 'edusystem'); ?>
		(<strong><?= $total_count_graduated ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-students&section_tab=scholarships'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('Scholarships', 'edusystem'); ?>
		(<strong><?= $total_count_scholarships ?></strong>)</a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h4 class="wp-heading-line"><?= __('The academic term in progress is', 'edusystem'); ?>
				<?= $academic_period ?> - <?= $cut ?>
			</h4>
			<?php
			$heading_text = '';
			$name_document = '';

			switch ($_GET['section_tab']) {
				case 'current':
					$heading_text = __('Students seeing classes in the current term.', 'edusystem');
					$name_document = __('Students studying.xlsx', 'edusystem');
					break;
				case 'pending_electives':
					$heading_text = __('Pending students to select electives', 'edusystem');
					$name_document = __('Pending electives.xlsx', 'edusystem');
					break;
				case 'non-enrolled':
					$heading_text = __('Students who are not seeing classes in the current term', 'edusystem');
					$name_document = __('Non-enrolled.xlsx', 'edusystem');
					break;
				case 'pending-graduation':
					$heading_text = __('Students academically ready, awaiting graduation', 'edusystem');
					$name_document = __('Pending graduation.xlsx', 'edusystem');
					break;
				case 'graduated':
					$heading_text = __('Graduated students', 'edusystem');
					$name_document = __('Graduated students.xlsx', 'edusystem');
					break;
				case 'scholarships':
					$heading_text = __('Scholarship students', 'edusystem');
					$name_document = __('Scholarship students.xlsx', 'edusystem');
					break;
				default:
					$heading_text = __('All students with active status', 'edusystem');
					$name_document = __('Active students.xlsx', 'edusystem');
					break;
			}
			?>
			<h1 class='wp-heading-line'><?= $heading_text ?></h1>
			<input type="hidden" name="name_document" id="name-document" value="<?= $name_document ?>">
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=report-students') . ($_GET['section_tab'] ? '&section_tab=' . $_GET['section_tab'] : ''); ?>">
			<div style="width:100%;text-align:right;padding-top:10px;">
				<?php if ($_GET['section_tab'] != 'current' && $_GET['section_tab'] != 'pending_electives') { ?>
					<select name="academic_period" id="academic_period">
						<option value="" selected="selected"><?= __('Select an academic period', 'edusystem'); ?></option>
						<?php foreach ($periods as $key => $period) { ?>
							<option value="<?php echo $period->code; ?>" <?= ($_POST['academic_period'] == $period->code) ? 'selected' : ''; ?>>
								<?php echo $period->name; ?>
							</option>
						<?php } ?>
					</select>
					<select name="academic_period_cut" id="academic_period_cut">
						<option value=""><?= __('Select academic period cut', 'edusystem') ?></option>
						<option value="A" <?= !empty($_POST['academic_period_cut']) ? (($_POST['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
						<option value="B" <?= !empty($_POST['academic_period_cut']) ? (($_POST['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
						<option value="C" <?= !empty($_POST['academic_period_cut']) ? (($_POST['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
						<option value="D" <?= !empty($_POST['academic_period_cut']) ? (($_POST['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
						<option value="E" <?= !empty($_POST['academic_period_cut']) ? (($_POST['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
					</select>
				<?php } ?>
				<select name="country" id="country">
					<option value=""><?= __('Select country', 'edusystem') ?></option>
					<?php foreach ($countries as $key => $country) { ?>
						<option value="<?= $key ?>" <?= $_POST['country'] == $key ? 'selected' : ''; ?>><?= $country; ?></option>
					<?php } ?>
				</select>
				<input type="search" id="search-box-id-search-input" name="s"
					placeholder="<?= __('Search for student', 'edusystem'); ?>"
					value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<?php if (wp_is_mobile()): ?>
					<button type="submit"
						class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
					<button type="button"
						id="<?= $_GET['section_tab'] == 'current' ? 'export_excel_students_current' : 'export_excel_students' ?>"
						class="button button-success"
						style="width:100%;"></span><?= __('Export excel', 'edusystem'); ?></button>
				<?php else: ?>
					<button type="submit"
						class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
					<button type="button"
						id="<?= $_GET['section_tab'] == 'current' ? 'export_excel_students_current' : 'export_excel_students' ?>"
						class="button button-success"></span><?= __('Export excel', 'edusystem'); ?></button>
				<?php endif; ?>
			</div>
			<?php if ($_GET['section_tab'] != 'current' && $_GET['section_tab'] != 'pending_electives') { ?>
				<div style="text-align: right; margin-top: 20px;">
					<strong><?= !$_GET['section_tab'] ? __('Filter applies to student enrollments.', 'edusystem') : __('The filter applies to when the student enters the platform.', 'edusystem'); ?></strong>
				</div>
			<?php } ?>

		</form>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students->display() ?>
		</form>
	</div>
</div>