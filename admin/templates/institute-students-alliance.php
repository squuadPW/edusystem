<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Students','edusystem'); ?></h1>
		</div>

		<?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
			<div class="notice notice-success is-dismissible">
				<p><?= $_COOKIE['message']; ?></p>
			</div>
			<?php setcookie('message', '', time(), '/'); ?>
		<?php } ?>
		<?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?= $_COOKIE['message-error']; ?></p>
			</div>
			<?php setcookie('message-error', '', time(), '/'); ?>
		<?php } ?>
        <div style="display:flex;width:100%;">
            <a class="button button-outline-primary"
                href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
        </div>
		<form action="" id="post-filter" method="get">
			<!-- <p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<select name="academic_period_cut">
					<option value="">Select academic period cut</option>
					<option value="A" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
					<option value="B" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
					<option value="C" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
					<option value="D" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
					<option value="E" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
				</select>
			</p>
			<p class="search-box" style="margin-right: 10px">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<select name="academic_period">
						<option value="" selected>Select academic period to filter</option>
					<?php foreach ($periods as $key => $period) { ?>
						<option value="<?php echo $period->code; ?>" <?= !empty($_GET['academic_period']) ? (($_GET['academic_period'] == $period->code) ? 'selected' : '') : ''; ?>>
							<?php echo $period->name; ?>
						</option>
					<?php } ?>
				</select>
			</p> -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students_institute->display() ?>
		</form>  
	</div>
</div>