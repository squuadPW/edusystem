<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Subjects', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_school_subjects_content&section_tab=add_subject'); ?>" class="button button-outline-primary"><?= __('Add Subject', 'edusystem'); ?></a>
		</div>
	</div>
	<form action="" id="post-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
			<input value="<?= $_GET['s'] ?>" type="search" id="search-box-id-search-input" name="s"
				placeholder="<?= __('Search for Subject', 'edusystem'); ?>"
				value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
			<input type="submit" id="search-submit" class="button" value="Search">
		</p>
		<p class="search-box" style="margin-right: 10px">
			<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Type', 'edusystem') . ':'; ?></label>
			<select name="subject_type">
				<option value="">All</option>
				<option value="regular" <?= isset($_GET['subject_type']) ? (($_GET['subject_type'] == 'regular') ? 'selected' : '') : ''; ?>>Regular</option>
				<option value="elective" <?= isset($_GET['subject_type']) ? (($_GET['subject_type'] == 'elective') ? 'selected' : '') : ''; ?>>Electives</option>
				<option value="equivalence" <?= isset($_GET['subject_type']) ? (($_GET['subject_type'] == 'equivalence') ? 'selected' : '') : ''; ?>>Equivalence</option>
			</select>
		</p>
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $list_school_subjects->display() ?>
	</form>
</div>
</div>