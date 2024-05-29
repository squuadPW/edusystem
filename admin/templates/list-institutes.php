<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Institutes for review','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('All Institutes','form-plugin'); ?></a>
</h2>

<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
            <?php if(!isset($_GET['section_tab'])): ?>
                <h1 class="wp-heading-line"><?= __('Institutes for Review','restaurant-system-app'); ?></h1>
            <?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_institutes'): ?>
                <h1 class="wp-heading-line"><?= __('All Institutes','restaurant-system-app'); ?></h1>
            <?php endif; ?>
        </div>
        <form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input">Search:</label>
				<input type="search" id="search-box-id-search-input" name="search-order" value="<?= (!empty($_POST['search-order'])) ? $_POST['search-order'] : ''; ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_institutes->display() ?>
		</form>
    </div>
<div>