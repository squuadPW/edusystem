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
			<h1>
				<?php
				$tab = isset($_GET['section_tab']) && $_GET['section_tab'] ? $_GET['section_tab'] : 'programs';
				$labels = [
					'programs' => __('Programs', 'edusystem'),
					'careers'  => __('Careers', 'edusystem'),
					'mentions' => __('Mentions', 'edusystem'),
				];
				$label = isset($labels[$tab]) ? $labels[$tab] : ucfirst($tab);

				printf(
					esc_html__('All %s', 'edusystem'),
					esc_html($label)
				);
				?>
			</h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=program_details&from=' . (isset($_GET['section_tab']) ? $_GET['section_tab'] : 'programs')); ?>"
				class="button button-outline-primary">
				<?php
				$tab = isset($_GET['section_tab']) && $_GET['section_tab'] ? $_GET['section_tab'] : 'programs';
				$labels = [
					'programs' => __('Program', 'edusystem'),
					'careers'  => __('Career', 'edusystem'),
					'mentions' => __('Mention', 'edusystem'),
				];
				$label = isset($labels[$tab]) ? $labels[$tab] : ucfirst($tab);

				printf(
					esc_html__('Add %s', 'edusystem'),
					esc_html($label)
				);
				?>
			</a>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_program->display() ?>
		</form>
	</div>
</div>