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
		<?php 
			$style_pending = '';
			$style_pending_email = '';
			if ($enroll_moodle_count > 0) {
				$style_pending = 'background-color: #2271b1; color: white;';
			}

			if ($pending_emails_count > 0) {
				$style_pending_email = 'background-color: #2271b1; color: white;';
			}
		?>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollments'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to validate enrollments?');"><?= __('Enrollment history','aes'); ?></a>
			<a style="margin-left: 10px; <?= $style_pending ?>" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_enrollments_moodle'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to generate enrollments in Moodle?');"><?= __('Enroll in moodle','aes'); ?> (<?= $enroll_moodle_count ?>)</a>
			<a style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=get_moodle_notes'); ?>" class="button button-outline-primary" onclick="return confirm('Are you sure you want to update the notes from Moodle?');"><?= __('Download moodle notes','aes'); ?></a>
			<?php
				$text_students = '';
				foreach ($pending_emails_students as $key => $student) {
					$text_students .= $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name . '<br>';
				}
			?>
			<a data-tippy-content="<?= $text_students ?>"
			style="margin-left: 10px; <?= $style_pending_email ?>"
                href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=send_welcome_email') ?>"
                class="button button-outline-primary help-tooltip" onclick="return confirm('Are you sure?');">
                <?= __('Send welcome mailing', 'aes'); ?> (<?= $pending_emails_count ?>)
			</a>
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


<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
      // With the above scripts loaded, you can call `tippy()` with a CSS
      // selector and a `content` prop:
      tippy('.help-tooltip', {
        allowHTML: true
      });
</script>