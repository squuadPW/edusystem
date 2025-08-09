<div class="tabs-content">
	<div class="wrap">

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All programs', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_program_content&section_tab=program_details'); ?>"
				class="button button-outline-primary"><?= __('Add program', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_program->display() ?>
		</form>
	</div>
</div>