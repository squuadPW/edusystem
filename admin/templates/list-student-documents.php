<h2 class="nav-tab-wrapper">

	<!--<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= esc_html__('New Applicants','edusystem'); ?></a>-->
	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= esc_html__('Documents for review','edusystem'); ?></a>
    <a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=all_students') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students') ? 'nav-tab-active' : ''; ?>"><?= esc_html__('All Applicants','edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= esc_html__('Document for Review','edusystem'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students'): ?>
				<h1 class="wp-heading-line"><?= esc_html__('All Applicants','edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= esc_html__('Search','edusystem') . ':'; ?></label>
				<input type="search" placeholder="<?= esc_html__('Search for student','edusystem') ?>" id="search-box-id-search-input" name="s" value="<?= (!empty($_GET['s'])) ? $_GET['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="<?= esc_html__('Search','edusystem') ?>">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= esc_html__('Search','edusystem') . ':'; ?></label>
				<select name="academic_period_cut" data-textoption="<?= esc_html__('Select term to filter', 'edusystem') ?>" data-initial-cut="<?= !empty($_GET['academic_period_cut']) ? esc_attr($_GET['academic_period_cut']) : ''; ?>">
					<option value="" selected><?= esc_html__('Select term to filter', 'edusystem') ?></option>
					<?php foreach ($periods_cuts as $key => $cut) { ?>
						<option value="<?= $cut->code ?>" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == $cut->code) ? 'selected' : '') : ''; ?>><?= $cut->cut ?></option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= esc_html__('Search','edusystem') . ':'; ?></label>
				<select name="academic_period">
						<option value="" selected><?= esc_html__('Select academic period to filter', 'edusystem') ?></option>
					<?php foreach ($periods as $key => $period) { ?>
						<option value="<?php echo $period->code; ?>" <?= !empty($_GET['academic_period']) ? (($_GET['academic_period'] == $period->code) ? 'selected' : '') : ''; ?>>
							<?php echo $period->name; ?>
						</option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= esc_html__('Date','edusystem') . ':'; ?></label>
				<select name="date_selected">
						<option value="" selected><?= esc_html__('Select date range', 'edusystem') ?></option>
						<option value="1" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 1) ? 'selected' : '') : ''; ?>><?= esc_html__('Last 15 days','edusystem') ?></option>
						<option value="2" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 2) ? 'selected' : '') : ''; ?>><?= esc_html__('Last 35 days','edusystem') ?></option>
						<option value="3" <?= !empty($_GET['date_selected']) ? (($_GET['date_selected'] == 3) ? 'selected' : '') : ''; ?>><?= esc_html__('More than 35 days','edusystem') ?></option>
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