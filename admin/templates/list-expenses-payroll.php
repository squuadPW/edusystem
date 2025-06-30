<?php
include(plugin_dir_path(__FILE__) . 'topbar-payments.php');
?>

<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<?php if (!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Payments for Review', 'edusystem'); ?></h1>
			<?php elseif (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'expenses_payroll'): ?>
				<h1 class="wp-heading-line"><?= __('Expenses and payroll', 'edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=add_expenses_payroll'); ?>"
				class="button button-outline-primary"><?= __('Add expense', 'edusystem'); ?></a>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_payments->display() ?>
		</form>
	</div>
</div>