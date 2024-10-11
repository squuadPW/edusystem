<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Payments for Review','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=all_payments'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments') ? 'nav-tab-active' : ''; ?>"><?= __('All Payments','form-plugin'); ?></a>
	<?php global $current_user;
	$roles = $current_user->roles;
		if(!in_array('webinar-aliance', $roles)){?>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_alliances') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for alliances','form-plugin'); ?></a>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'invoices_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('Invoices for institutes','form-plugin'); ?></a>
		<a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'generate_advance_payment') ? 'nav-tab-active' : ''; ?>"><?= __('Generate advance payment','form-plugin'); ?></a>
	<?php } ?>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Payments for Review','aes'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_payments'): ?>
				<h1 class="wp-heading-line"><?= __('All Payments','aes'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','aes').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for payment ID','aes'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_payments->display() ?>
		</form>  
	</div>
</div>