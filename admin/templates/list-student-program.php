<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_student_program_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Programs', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'careers') ? 'nav-tab-active' : ''; ?>"><?= __('Careers', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=mentions'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'mentions') ? 'nav-tab-active' : ''; ?>"><?= __('Mentions', 'edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All ' . (isset($_GET['section_tab']) ? ucfirst($_GET['section_tab']) : 'programs'), 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=program_details&from=' . (isset($_GET['section_tab']) ? $_GET['section_tab'] : 'programs')); ?>"
				class="button button-outline-primary"><?= __('Add ' . (isset($_GET['section_tab']) ? ucfirst($_GET['section_tab']) : 'programs'), 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_program->display() ?>
		</form>
	</div>
</div>