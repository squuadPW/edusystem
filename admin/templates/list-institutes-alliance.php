<div class="wrap">
    <div style="text-align:start;">
        <h1 class="wp-heading-line"><?= __('Registered institutes','edusystem'); ?></h1>
    </div>
    <!-- <div style="display:flex;width:100%;justify-content:end;margin-bottom:10px;">
        <a href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=add_institute'); ?>" class="button button-outline-primary"><?= __('Add Institute','edusystem'); ?></a>
    </div> -->
    <form id="post-filter" method="get">
        <p class="search-box">
            <label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
            <input type="search" id="search-box-id-search-input" name="s" value="<?= (!empty($_GET['s'])) ? $_GET['s'] : ''; ?>" placeholder="<?= __('Search by name, email, recto\'s first name or rector\'s last name','edusystem'); ?>">
            <input type="submit" id="search-submit" class="button" value="Search" >
        </p>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $list_institutes_alliance->display(); ?>
    </form>  
</div>