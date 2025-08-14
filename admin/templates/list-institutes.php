<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Institutes for review','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('All Approved Institutes','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_declined_institutes'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_declined_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('All Declined Institutes','edusystem'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_suspended_institutes'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_suspended_institutes') ? 'nav-tab-active' : ''; ?>"><?= __('All Suspended Institutes','edusystem'); ?></a>
</h2>
<div class="tabs-content">
	<div class="wrap">
        <div style="text-align:start;">
            <?php if(!isset($_GET['section_tab'])): ?>
                <h1 class="wp-heading-line"><?= __('Institutes for Review','edusystem'); ?></h1>
            <?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_institutes'): ?>
                <h1 class="wp-heading-line"><?= __('All Approved Institutes','edusystem'); ?></h1>
            <?php endif; ?>
        </div>
		<?php if(isset($_COOKIE['message-delete']) && !empty($_COOKIE['message-delete'])): ?>
			<div class="notice notice-error is-dismissible"><p><b><?= $_COOKIE['message-delete']; ?></b> <?= __('was eliminated','edusystem'); ?></p></div>
			<?php setcookie('message-delete','',time(),'/'); ?>
		<?php endif; ?>
		<?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])): ?>
			<div class="notice notice-success is-dismissible"><p><b><?= $_COOKIE['message']; ?></b> <?= __('was created','edusystem'); ?></p></div>
			<?php setcookie('message','',time(),'/'); ?>
		<?php endif; ?>
		<?php if(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_institutes'): ?>
			<div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
				<a href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=add_institute'); ?>" class="button button-outline-primary"><?= __('Add Institute','edusystem'); ?></a>
			</div>
		<?php endif; ?>
        <form action="" id="post-filter" method="post">
			<p class="search-box">
				<label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
				<input type="search" id="search-box-id-search-input" name="s" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>" placeholder="<?= __('Search by name, email, recto\'s first name or rector\'s last name','edusystem'); ?>">
				<input type="submit" id="search-submit" class="button" value="Search">
			</p>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $list_institutes->display() ?>
		</form>
    </div>
<div>