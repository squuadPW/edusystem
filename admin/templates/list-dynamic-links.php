<div class="tabs-content">
	<div class="wrap">
		<div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Dynamic links', 'edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=add_dynamic_link'); ?>" class="button button-outline-primary"><?= __('Add Dynamic Link', 'edusystem'); ?></a>
		</div>
	</div>
	<form action="" id="post-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search', 'edusystem') . ':'; ?></label>
			<input value="<?= $_GET['s'] ?>" type="search" id="search-box-id-search-input" name="s"
				placeholder="<?= __('Search for title', 'edusystem'); ?>"
				value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
			<input type="submit" id="search-submit" class="button" value="Search">
		</p>
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $list_dynamic_links->display() ?>
	</form>
</div>
</div>