<h2 class="nav-tab-wrapper">
	<a href="<?= admin_url('admin.php?page=add_admin_partners_content') ?>" class="nav-tab <?= (!isset($_GET['section_tab'])) ? 'nav-tab-active' : ''; ?>"><?= __('Alliances for review','form-plugin'); ?></a>
	<a href="<?= admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'); ?>" class="nav-tab <?= (isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_alliances') ? 'nav-tab-active' : ''; ?>"><?= __('All Alliances','form-plugin'); ?></a>
</h2>
<div class="wrap">
    <div style="text-align:start;">
        <?php if(!isset($_GET['section_tab'])): ?>
            <h1 class="wp-heading-line"><?= __('Alliances for Review','aes'); ?></h1>
        <?php elseif(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_alliances'): ?>
            <h1 class="wp-heading-line"><?= __('All Alliances','aes'); ?></h1>
        <?php endif; ?>
    </div>
    <?php if(isset($_COOKIE['message-delete']) && !empty($_COOKIE['message-delete'])): ?>
        <div class="notice notice-error is-dismissible"><p><b><?= $_COOKIE['message-delete']; ?></b> <?= __('was eliminated','aes'); ?></p></div>
        <?php setcookie('message-delete','',time(),'/'); ?>
    <?php endif; ?>
    <?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])): ?>
        <div class="notice notice-success is-dismissible"><p><b><?= $_COOKIE['message']; ?></b> <?= __('was created','aes'); ?></p></div>
        <?php setcookie('message','',time(),'/'); ?>
    <?php endif; ?>
    <?php if(isset($_GET['section_tab']) && !empty($_GET['section_tab']) && $_GET['section_tab'] == 'all_alliances'): ?>
        <div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
            <a href="<?= admin_url('admin.php?page=add_admin_partners_content&section_tab=add_alliance'); ?>" class="button button-outline-primary"><?= __('Add Alliance','aes'); ?></a>
        </div>
	<?php endif; ?>
    <form id="post-filter" method="post">
        <p class="search-box">
            <label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','aes').':'; ?></label>
            <input type="search" id="search-box-id-search-input" name="s" value="<?= (!empty($_POST['s'])) ? $_POST['s'] : ''; ?>" placeholder="<?= __('Search by name, email','aes'); ?>">
            <input type="submit" id="search-submit" class="button" value="Search">
        </p>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $list_alliances->display() ?>
    </form>  
</div>