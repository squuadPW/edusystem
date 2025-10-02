<h2 class="nav-tab-wrapper">

	<!--<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('New Applicants','edusystem'); ?></a>-->
	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Documents for review','edusystem'); ?></a>
    <a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=all_students') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students') ? 'nav-tab-active' : ''; ?>"><?= __('All Applicants','edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Document for Review','edusystem'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students'): ?>
				<h1 class="wp-heading-line"><?= __('All Applicants','edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" placeholder="Search for student" id="search-box-id-search-input" name="s" value="<?= (!empty($_GET['s'])) ? $_GET['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<select name="academic_period_cut" data-textoption="Select term to filter">
					<option value="" selected><?= __('Select term to filter', 'edusystem') ?></option>
					<?php foreach ($periods_cuts as $key => $cut) { ?>
						<option value="<?= $cut->code ?>" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == $cut->code) ? 'selected' : '') : ''; ?>><?= $cut->cut ?></option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<select name="academic_period">
						<option value="" selected><?= __('Select academic period to filter', 'edusystem') ?></option>
					<?php foreach ($periods as $key => $period) { ?>
						<option value="<?php echo $period->code; ?>" <?= !empty($_GET['academic_period']) ? (($_GET['academic_period'] == $period->code) ? 'selected' : '') : ''; ?>>
							<?php echo $period->name; ?>
						</option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Date','edusystem').':'; ?></label>
				<select name="date_selected">
						<option value="" selected><?= __('Select date range', 'edusystem') ?></option>
						<option value="1" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 1) ? 'selected' : '') : ''; ?>>Last 15 days</option>
						<option value="2" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 2) ? 'selected' : '') : ''; ?>>Last 35 days</option>
						<option value="3" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 3) ? 'selected' : '') : ''; ?>>More than 35 days</option>
				</select>
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php if($_GET['section_tab']) { ?>
				<input type="hidden" name="section_tab" value="<?php echo $_GET['section_tab'] ?>" />
			<?php } ?>
			<?php $list_students->display() ?>
		</form>  
    </div>
</div>