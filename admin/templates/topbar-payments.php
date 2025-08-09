<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content') ?>"
		class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Payments for Review', 'edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=all_payments'); ?>"
		class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments') ? 'nav-tab-active' : ''; ?>"><?= __('All Payments', 'edusystem'); ?></a>
	<?php global $current_user;
	$roles = $current_user->roles;
	if (!in_array('webinar-aliance', $roles)) { ?>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances'); ?>"
			class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_alliances') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for alliances', 'edusystem'); ?></a>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes'); ?>"
			class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for institutes', 'edusystem'); ?></a>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'); ?>"
			class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'generate_advance_payment') ? 'nav-tab-active' : ''; ?>"><?= __('Manage payments', 'edusystem'); ?></a>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=expenses_payroll'); ?>"
			class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'expenses_payroll') ? 'nav-tab-active' : ''; ?>"><?= __('Expenses and payroll', 'edusystem'); ?></a>
		<a href="<?= admin_url('admin.php?page=report-accounts-receivables'); ?>"
			class="nav-tab"><?= __('Upcoming quotas', 'edusystem'); ?></a>
	<?php } ?>
</h2>