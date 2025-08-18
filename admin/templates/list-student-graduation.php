<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_student_graduated_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Pending', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_student_graduated_content&section_tab=graduated'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'graduated') ? 'nav-tab-active' : ''; ?>"><?= __('Graduated', 'edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">

		<?php
		include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

		<div style="text-align:start;">
			<?php if (!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Pending', 'edusystem'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'graduated'): ?>
				<h1 class="wp-heading-line"><?= __('Graduated', 'edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_student_graduated->display() ?>
		</form>
	</div>
</div>