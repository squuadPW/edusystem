<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=report-current-students') ?>"
		class="nav-tab <?= !isset($_GET['section_tab']) ? 'nav-tab-active' : ''; ?>"><?= __('Active students', 'edusystem'); ?>
		(<strong><?= $total_count_active ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=current') ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'current') ? 'nav-tab-active' : ''; ?>"><?= __('Students studying', 'edusystem'); ?>
		(<strong><?= $total_count_current ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=pending_electives'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending_electives') ? 'nav-tab-active' : ''; ?>"><?= __('Pending electives', 'edusystem'); ?>
		(<strong><?= $total_count_pending_electives ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=non-enrolled'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'non-enrolled') ? 'nav-tab-active' : ''; ?>"><?= __('Non-enrolled students', 'edusystem'); ?>
		(<strong><?= $total_count_non_enrolled ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=pending-graduation'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending-graduation') ? 'nav-tab-active' : ''; ?>"><?= __('Pending graduation', 'edusystem'); ?>
		(<strong><?= $total_count_pending_graduation ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=graduated'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'graduated') ? 'nav-tab-active' : ''; ?>"><?= __('Graduated', 'edusystem'); ?>
		(<strong><?= $total_count_graduated ?></strong>)</a>
</h2>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h4 class="wp-heading-line"><?= __('The academic term in progress is', 'edusystem'); ?>
				<?= $academic_period ?> - <?= $cut ?>
			</h4>
			<?php
			$heading_text = ''; // Initialize an empty variable for the heading content
			
			switch ($_GET['section_tab']) {
				case 'current':
					$heading_text = __('Students seeing classes in the current term.', 'edusystem');
					break;
				case 'pending_electives':
					$heading_text = __('Pending students to select electives', 'edusystem');
					break;
				case 'non-enrolled':
					$heading_text = __('Students who are not seeing classes in the current term', 'edusystem');
					break;
				case 'pending-graduation':
					$heading_text = __('Students academically ready, awaiting graduation', 'edusystem');
					break;
				case 'graduated':
					$heading_text = __('Graduated students', 'edusystem');
					break;
				default:
					$heading_text = __('All students with active status', 'edusystem');
					break;
			}
			?>
			<h1 class='wp-heading-line'><?= $heading_text ?></h1>
		</div>
		<div style="width:100%;text-align:right;padding-top:10px;">
			<?php if (wp_is_mobile()): ?>
				<button type="button" id="export_excel_students" class="button button-success"
					style="width:100%;"></span><?= __('Export excel', 'edusystem'); ?></button>
			<?php else: ?>
				<button type="button" id="export_excel_students"
					class="button button-success"></span><?= __('Export excel', 'edusystem'); ?></button>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students->display() ?>
		</form>
	</div>
</div>