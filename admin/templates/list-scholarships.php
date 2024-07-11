<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Scholarships for Review','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=all_scholarships'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships') ? 'nav-tab-active' : ''; ?>"><?= __('All Scholarships','form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('Scholarships for Review','aes'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_scholarships'): ?>
				<h1 class="wp-heading-line"><?= __('All Scholarships','aes'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','aes').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" placeholder="<?= __('Search for scholarship ID','aes'); ?>" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_scholarships->display() ?>
		</form>  
	</div>
</div>