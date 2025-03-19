<!-- <h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Scholarships for Review','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=all_scholarships'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('All Scholarships','edusystem'); ?></a>
</h2> -->

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<h1 class="wp-heading-line"><?= __('Available scholarships','edusystem'); ?></h1>
		</div>
		<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
			<a href="<?= admin_url('admin.php?page=add_admin_form_available_scholarships_content&section_tab=available_scholarship_detail'); ?>"><button class="button button-outline-primary"><?= __('Add scholarship','edusystem'); ?></button></a>
		</div>
		<form action="" id="post-filter" method="post">
			<!-- <p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for scholarship ID','edusystem'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p> -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_availables_scholarships->display() ?>
		</form>  
	</div>
</div>