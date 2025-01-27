<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All academic projections','aes'); ?></h1>
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
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollments'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to validate enrollments?');"><?= __('ENROLLMENTS','aes'); ?></a>
			<a style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_academic_projections'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to generate the academic projections?');"><?= __('GENERATE ACADEMIC PROJECTIONS','aes'); ?></a>
			<a style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_enrollments_moodle'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to generate enrollments in Moodle?');"><?= __('GENERATE REGISTRATIONS IN MOODLE','aes'); ?></a>
			<a style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=get_moodle_notes'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to update the notes from Moodle?');"><?= __('UPDATE NOTES FROM MOODLE','aes'); ?></a>
		</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','aes').':'; ?></label>
				<input value="<?= isset($_GET['s']) ? $_GET['s'] : '' ?>" type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for Student','aes'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_academic_projection->display() ?>
		</form>  
	</div>
</div>