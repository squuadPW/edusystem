<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Enrollments', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_enrollments_content&section_tab=add_enrollment'); ?>"
				class="button button-outline-primary"><?= __('Create Enrollment', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<input type="search" placeholder="Search Student"
					id="search-box-id-search-input" name="s" value="<?= (!empty($_GET['s'])) ? $_GET['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="academic_period_cut">
					<option value="">__('Select academic period cut', 'edusystem')</option>
					<option value="A" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
					<option value="B" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
					<option value="C" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
					<option value="D" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
					<option value="E" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="academic_period">
					<option value="" selected>__('Select academic period to filter')</option>
					<?php foreach ($periods as $key => $period) { ?>
						<option value="<?php echo $period->code; ?>" <?= !empty($_GET['academic_period']) ? (($_GET['academic_period'] == $period->code) ? 'selected' : '') : ''; ?>>
							<?php echo $period->name; ?>
						</option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="code_subject">
					<option value="" selected>Select subject to filter</option>
					<?php foreach ($subjects as $key => $subject) { ?>
						<option value="<?php echo $subject->code_subject; ?>" <?= !empty($_GET['code_subject']) ? (($_GET['code_subject'] == $subject->code_subject) ? 'selected' : '') : ''; ?>>
							<?php echo $subject->name; ?> (<?php echo $subject->code_subject; ?>)
						</option>
					<?php } ?>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<select name="status_id">
					<option value="">Select a status</option>
					<option value="0" <?= $_GET['status_id'] != '' ? (($_GET['status_id'] == '0') ? 'selected' : '') : ''; ?>>To begin</option>
					<option value="1" <?= $_GET['status_id'] != '' ? (($_GET['status_id'] == '1') ? 'selected' : '') : ''; ?>>Active</option>
					<option value="2" <?= $_GET['status_id'] != '' ? (($_GET['status_id'] == '2') ? 'selected' : '') : ''; ?>>Unsubscribed</option>
					<option value="3" <?= $_GET['status_id'] != '' ? (($_GET['status_id'] == '3') ? 'selected' : '') : ''; ?>>Completed</option>
				</select>
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_enrollments->display() ?>
		</form>
	</div>
</div>