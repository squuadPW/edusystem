<h2 class="nav-tab-wrapper nav-scroll-h">
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=current') ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'current') ? 'nav-tab-active' : ''; ?>"><?= __('Currently Enrolled', 'edusystem'); ?>
        (<strong><?= $total_count_current ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students') ?>"
        class="nav-tab <?= !isset($_GET['section_tab']) ? 'nav-tab-active' : ''; ?>"><?= __('All Active Students', 'edusystem'); ?>
        (<strong><?= $total_count_active ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=pending-documents'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending-documents') ? 'nav-tab-active' : ''; ?>"><?= __('Documents Missing', 'edusystem'); ?>
        (<strong><?= $total_count_pending_documents ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=documents_active_students') ?>"
        class="nav-tab <?= !empty($_GET['section_tab']) && $_GET['section_tab'] == 'documents_active_students' ? 'nav-tab-active' : ''; ?>"><?= __('Active Student Documents', 'edusystem'); ?>
        (<strong><?= $total_count_active ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=enrollment_active_students') ?>"
        class="nav-tab <?= !empty($_GET['section_tab']) && $_GET['section_tab'] == 'enrollment_active_students' ? 'nav-tab-active' : ''; ?>"><?= __('Enrollment of active students', 'edusystem'); ?>
        (<strong><?= $total_count_active ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=pending_electives'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending_electives') ? 'nav-tab-active' : ''; ?>"><?= __('Electives Pending', 'edusystem'); ?>
        (<strong><?= $total_count_pending_electives ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=non-enrolled'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'non-enrolled') ? 'nav-tab-active' : ''; ?>"><?= __('Non-Enrolled Students', 'edusystem'); ?>
        (<strong><?= $total_count_non_enrolled ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=scholarships'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('Scholarship Recipients', 'edusystem'); ?>
        (<strong><?= $total_count_scholarships ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=pending-graduation'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending-graduation') ? 'nav-tab-active' : ''; ?>"><?= __('Graduation Review', 'edusystem'); ?>
        (<strong><?= $total_count_pending_graduation ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=graduated'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'graduated') ? 'nav-tab-active' : ''; ?>"><?= __('Graduates', 'edusystem'); ?>
        (<strong><?= $total_count_graduated ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-students&section_tab=retired'); ?>"
        class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'retired') ? 'nav-tab-active' : ''; ?>"><?= __('Withdrawn Students', 'edusystem'); ?>
        (<strong><?= $total_count_retired ?></strong>)</a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h4 class="wp-heading-line"><?= __('The academic term in progress is', 'edusystem'); ?>
				<?= $academic_period ?> - <?= $cut ?>
			</h4>
			<?php
			global $wpdb;
	        $table_documents = $wpdb->prefix . 'documents';

			$heading_text = '';
			$name_document = '';
			$headers = [];
			switch ($_GET['section_tab']) {
				case 'current':
					$heading_text = __('Students seeing classes in the current term.', 'edusystem');
					$name_document = __('Students studying.xlsx', 'edusystem');
					$headers = ['Student', 'Subjects'];
					break;
				case 'documents_active_students':
					$heading_text = __('Documents active students', 'edusystem');
					$name_document = __('Documents active students.xlsx', 'edusystem');
					$documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id = 4", OBJECT);
					$headers = ['Student', 'ID', 'Email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					foreach ($documents as $document) {
						// Apply strtolower and then ucfirst to the document name for display.
						$display_name = ucfirst(strtolower($document->name));

						// Convert to lowercase.
						$name_lower = strtolower($document->name);

						// Remove all non-alphanumeric characters (except spaces) for a clean key.
						// This removes special characters like periods, parentheses, commas, etc.
						$name_sanitized = preg_replace('/[^a-z0-9\s]/', '', $name_lower);

						// Replace spaces with underscores to create the final array key.
						$key = str_replace(' ', '_', $name_sanitized);

						// Use the modified name for the column header.
						$headers[] = __($display_name, 'edusystem');
					}
					break;
				case 'enrollment_active_students':
					$heading_text = __('Enrollment history of active students ', 'edusystem');
					$name_document = __('Enrollments of active students.xlsx', 'edusystem');
					$documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id = 4", OBJECT);
					$headers = ['Student', 'ID', 'Email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				case 'pending_electives':
					$heading_text = __('Pending students to select electives', 'edusystem');
					$name_document = __('Pending electives.xlsx', 'edusystem');
					$headers = ['Student', 'ID', 'Student email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				case 'non-enrolled':
					$heading_text = __('Students who are not seeing classes in the current term', 'edusystem');
					$name_document = __('Non-enrolled.xlsx', 'edusystem');
					$headers = ['Student', 'ID', 'Student email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				case 'pending-graduation':
					$heading_text = __('Students academically ready, awaiting graduation', 'edusystem');
					$name_document = __('Pending graduation.xlsx', 'edusystem');
					$headers = ['Student', 'Country', 'Institute', 'Academic ready', 'Documents', 'Fee registration', 'Program payment', 'Fee graduation'];
					break;
				case 'pending-documents':
					$heading_text = __('Students with pending documents', 'edusystem');
					$name_document = __('Pending documents.xlsx', 'edusystem');
					$headers = ['Student', 'Documents'];
					break;
				case 'graduated':
					$heading_text = __('Graduated students', 'edusystem');
					$name_document = __('Graduated students.xlsx', 'edusystem');
					$headers = ['Income', 'Term', 'ID', 'Student', 'Student email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				case 'retired':
					$heading_text = __('Retired students', 'edusystem');
					$name_document = __('Retired students.xlsx', 'edusystem');
					$headers = ['Income', 'Term', 'ID', 'Student', 'Student email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				case 'scholarships':
					$heading_text = __('Scholarship students', 'edusystem');
					$name_document = __('Scholarship students.xlsx', 'edusystem');
					$headers = ['Scholarship', 'Student', 'ID', 'Student email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
				default:
					$heading_text = __('All students with active status', 'edusystem');
					$name_document = __('Active students.xlsx', 'edusystem');
					$headers = ['Student', 'ID', 'Email', 'Parent', 'Parent email', 'Country', 'Grade', 'Institute'];
					break;
			}
			?>
			<h1 class='wp-heading-line'><?= $heading_text ?></h1>
			<input type="hidden" name="name_document" id="name-document" value="<?= $name_document ?>">
			<input type="hidden" name="headers" id="headers-document"
				value="<?= esc_attr(wp_json_encode($headers)); ?>">
		</div>
		<form method="post"
			action="<?= admin_url('admin.php?page=report-students') . ($_GET['section_tab'] ? '&section_tab=' . $_GET['section_tab'] : ''); ?>">
			<div class="filter-controls">
				<?php if ($_GET['section_tab'] != 'current' && $_GET['section_tab'] != 'pending_electives') { ?>
					<select name="academic_period" style="width: 100%;">
						<option value="" selected>Select an academic period</option>
						<?php foreach ($periods as $key => $period) { ?>
							<option value="<?= $period->code ?>" <?= $_POST['academic_period'] == $period->code ? 'selected' : '' ?>>
								<?= $period->name ?>
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
						<option value="<?= $key ?>" <?= $_POST['country'] == $key ? 'selected' : ''; ?>><?= $country; ?>
						</option>
					<?php } ?>
				</select>
				<select name="institute" id="institute">
					<option value=""><?= __('Select institute', 'edusystem') ?></option>
					<?php foreach ($institutes as $key => $institute) { ?>
						<option value="<?= $institute->id ?>" <?= $_POST['institute'] == $institute->id ? 'selected' : ''; ?>>
							<?= $institute->name; ?>
						</option>
					<?php } ?>
				</select>
				<input type="search" id="search-box-id-search-input" name="s"
					placeholder="<?= __('Search for student', 'edusystem'); ?>"
					value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">

				<div class="filter-actions">
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
			</div>
			<?php if ($_GET['section_tab'] != 'current' && $_GET['section_tab'] != 'pending_electives') { ?>
				<div style="text-align: right; margin-top: 20px;">
					<strong><?= !$_GET['section_tab'] ? __('Filter applies to student enrollments.', 'edusystem') : __('The filter applies to when the student enters the platform.', 'edusystem'); ?></strong>
				</div>
			<?php } ?>

		</form>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<div class="table-scroll-container">
				<?php $list_students->display() ?>
			</div>
		</form>
	</div>
</div>