<?php
if ($_GET['page'] == 'add_admin_form_payments_content') {
	include(plugin_dir_path(__FILE__) . 'topbar-payments.php');
}
?>

<div class="tabs-content">
	<div class="wrap">
		<?php
			include(plugin_dir_path(__FILE__) . 'cookie-message.php');
		?>

		<div style="text-align:start;">
			<?php if ($_GET['page'] == 'add_admin_form_payments_content') { ?>
				<?php if (!isset($_GET['section_tab'])): ?>
					<h1 class="wp-heading-line"><?= __('Payments for Review', 'edusystem'); ?></h1>
				<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments'): ?>
					<h1 class="wp-heading-line"><?= __('All Payments', 'edusystem'); ?></h1>
				<?php endif; ?>
			<?php } else { ?>
				<h1 class="wp-heading-line"><?= __('Payment Plans', 'edusystem'); ?></h1>
			<?php } ?>
		</div>
		<?php if ($_GET['page'] == 'add_admin_form_payments_plans_content') { ?>
			<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details'); ?>"
					class="button button-outline-primary"><?= __('Add program', 'edusystem'); ?></a>
			</div>
		<?php } ?>
		<form action="" id="post-filter" method="post">
			<?php if ($_GET['page'] == 'add_admin_form_payments_content') { ?>
				<p class="search-box">
					<label class="screen-reader-text"
						for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
					<input type="search" id="search-box-id-search-input" name="s"
						placeholder="<?= __('Search for student', 'edusystem'); ?>"
						value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
					<input type="submit" id="search-submit" class="button" value="Search">
				</p>
				<?php } ?>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_payments->display() ?>
		</form>
	</div>
</div>