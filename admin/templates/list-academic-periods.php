<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('All Academic periods','edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content&section_tab=add_period'); ?>" class="button button-outline-primary"><?= __('Add Period','edusystem'); ?></a>
			</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for Period','edusystem'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_academic_periods->display() ?>
		</form>  
	</div>
</div>