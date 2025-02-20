<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=report-current-students') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Current','form-plugin'); ?> (<strong><?= $total_count_current ?></strong>)</a>
	<a href="<?= admin_url('admin.php?page=report-current-students&section_tab=pending_electives'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'pending_electives') ? 'nav-tab-active' : ''; ?>"><?= __('Pending electives','form-plugin'); ?> (<strong><?= $total_count_pending_electives ?></strong>)</a>
    <a href="<?= admin_url('admin.php?page=report-current-students&section_tab=non-enrolled'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'non-enrolled') ? 'nav-tab-active' : ''; ?>"><?= __('Non-enrolled students','form-plugin'); ?> (<strong><?= $total_count_non_enrolled ?></strong>)</a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Current students of','aes'); ?> <?= $academic_period ?> - <?= $cut ?></h1>
		</div>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students->display() ?>
		</form>  
	</div>
</div>