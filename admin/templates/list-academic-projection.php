<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All academic projections', 'edusystem'); ?></h1>
		</div>

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

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
		<div class="buttons-academic-projection">
			<?php if (current_user_can('manager_enrollments_aes')) { ?>
				<a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollments'); ?>"
					class="button button-outline-primary"><?= __('Enrollment history', 'edusystem'); ?></a>
				<!-- <a data-tippy-content="<?= $current_enroll_text ?>" style="margin-left: 10px; <?= $style_pending ?>"
					href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_enrollments_moodle'); ?>"
					class="button button-outline-primary  help-tooltip"
					onclick="return confirm('Are you sure you want to generate enrollments in Moodle?');"><?= __('Enroll in moodle', 'edusystem'); ?>
					(<?= $enroll_moodle_count ?>)</a>

				<?php if (get_option('public_course_id')) { ?>
					<a style="margin-left: 10px"
						href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=enroll_public_course'); ?>"
						class="button button-outline-primary"
						onclick="return confirm('Are you sure you want to enroll?');"><?= __('Enroll public course', 'edusystem'); ?></a>
				<?php } ?> -->

				<a style="margin-left: 10px"
					href="javascript:void(0);"
					class="button button-outline-primary"
					id="openModalDownloadMoodleNotes"><?= __('Download moodle notes', 'edusystem'); ?></a>
				<?php
				$text_students = '';

				if (!get_option('send_welcome_email_ready') || empty(get_option('send_welcome_email_ready'))) {
					$text_students .= 'All mailings (including loaded, unloaded or elective) will be sent.';
				}

				if (get_option('send_welcome_email_ready') == $code_current_cut . ' - ' . $cut_current_cut) {
					$text_students .= 'Only pending emails will be sent';
				}

				$text_students .= '<br>';
				$text_students .= '<br>';

				foreach ($pending_emails_students as $key => $student) {
					$text_students .= $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name . '<br>';
				}

				if (count($pending_emails_students) == 0) {
					$text_students .= 'No students pending';
				}
				?>
				<a data-tippy-content="<?= $text_students ?>" style="margin-left: 10px; <?= $style_pending_email ?>"
					href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=send_welcome_email') ?>"
					class="button button-outline-primary help-tooltip" onclick="return confirm('Are you sure?');">
					<?= __('Send welcome mailing', 'edusystem'); ?> (<?= $pending_emails_count ?>)
				</a>
			<?php } ?>
		</div>
		<form action="" id="post-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text"
					for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
				<input value="<?= isset($_GET['s']) ? $_GET['s'] : '' ?>" type="search" id="search-box-id-search-input"
					name="s" placeholder="<?= __('Search for Student', 'edusystem'); ?>"
					value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_academic_projection->display() ?>
		</form>
	</div>
</div>

<?php
	include(plugin_dir_path(__FILE__) . 'modal-download-moodle-notes.php');
?>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
	// With the above scripts loaded, you can call `tippy()` with a CSS
	// selector and a `content` prop:
	tippy('.help-tooltip', {
		allowHTML: true,
		placement: 'bottom'
	});
</script>