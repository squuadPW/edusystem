<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Grades by Country', 'edusystem'); ?></h1>
		</div>

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_grades_country_content&section_tab=add_grade_country'); ?>"
				class="button button-outline-primary"><?= __('Add Grades by Country', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_grades_country->display() ?>
		</form>
	</div>
</div>