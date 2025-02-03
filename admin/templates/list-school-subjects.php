<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All School Subjects','aes'); ?></h1>
		</div>
		<!-- <div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_school_subjects_content&action=update_matrices'); ?>" class="button button-outline-primary"><?= __('Update all matrices','aes'); ?></a>
		</div> -->
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_school_subjects_content&section_tab=add_subject'); ?>" class="button button-outline-primary"><?= __('Add Subject','aes'); ?></a>
			</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','aes').':'; ?></label>
				<input value="<?= $_GET['s'] ?>" type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for Subject','aes'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Type', 'aes') . ':'; ?></label>
				<select name="subject_type">
					<option value="">All</option>
					<option value="1" <?= isset($_GET['subject_type']) ? (($_GET['subject_type'] == '1') ? 'selected' : '') : ''; ?>>Only electives</option>
					<option value="0" <?= isset($_GET['subject_type']) ? (($_GET['subject_type'] == '0') ? 'selected' : '') : ''; ?>>Only no electives</option>
				</select>
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_school_subjects->display() ?>
		</form>  
	</div>
</div>