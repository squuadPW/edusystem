<h2 class="nav-tab-wrapper">

	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('New Applicants','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=document_review') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'document_review') ? 'nav-tab-active' : ''; ?>"><?= __('Documents for review','form-plugin'); ?></a>
    <a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=all_students') ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students') ? 'nav-tab-active' : ''; ?>"><?= __('All Applicants','form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
			<?php if(!isset($_GET['section_tab'])): ?>
				<h1 class="wp-heading-line"><?= __('New Applicants','restaurant-system-app'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'document_review'): ?>
				<h1 class="wp-heading-line"><?= __('Document for Review','restaurant-system-app'); ?></h1>
			<?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_students'): ?>
				<h1 class="wp-heading-line"><?= __('All Applicants','restaurant-system-app'); ?></h1>
			<?php endif; ?>
		</div>
		<form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input">Search:</label>
				<input type="search" id="search-box-id-search-input" name="s" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_students->display() ?>
		</form>  
    </div>
</div>