<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Requests for Review','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content&section_tab=all_requests'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_requests') ? 'nav-tab-active' : ''; ?>"><?= __('All Requests','form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Requests for Review','aes'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_requests'): ?>
				<h1 class="wp-heading-line"><?= __('All requests','aes'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_requests->display() ?>
		</form>  
	</div>
</div>