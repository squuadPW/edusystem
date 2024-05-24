<h2 class="nav-tab-wrapper">

	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('New Students','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=document_review') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'document_review') ? 'nav-tab-active' : ''; ?>"><?= __('Document Review','form-plugin'); ?></a>
    <a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=all_students') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students') ? 'nav-tab-active' : ''; ?>"><?= __('All Students','form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('New Students','restaurant-system-app'); ?></h1>
		</div>
		<form id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students->display() ?>
		</form>  
    </div>
</div>