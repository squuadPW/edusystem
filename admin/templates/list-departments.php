<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Departments','edusystem'); ?></h1>
		</div>
		<div style="text-align:end;">
			<a href="<?= admin_url('admin.php?page=add_admin_department_content&action=add'); ?>" class="button button-outline-primary"><?= __('Add Department','edusystem'); ?></a>
		</div>
		<form id="post-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_departments->display() ?>
		</form>  
    </div>
</div>