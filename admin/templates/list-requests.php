<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Requests for Review','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content&section_tab=all_requests'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_requests') ? 'nav-tab-active' : ''; ?>"><?= __('All Requests','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content&section_tab=types'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'types') ? 'nav-tab-active' : ''; ?>"><?= __('Types','edusystem'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Requests for Review','edusystem'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_requests'): ?>
				<h1 class="wp-heading-line"><?= __('All requests','edusystem'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'types'): ?>
				<h1 class="wp-heading-line"><?= __('Types','edusystem'); ?></h1>
			<?php endif; ?>
		</div>
		<?php if(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'types'): ?>
			<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_requests_content&section_tab=type_details'); ?>" class="button button-outline-primary"><?= __('Add Type','edusystem'); ?></a>
			</div>
		<?php endif; ?>
		<form action="" id="post-filter" method="post">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_requests->display() ?>
		</form>  
	</div>
</div>