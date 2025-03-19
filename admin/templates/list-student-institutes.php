<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Students Registered','edusystem'); ?></h2>
    <form action="" id="post-filter" method="get">
        <p class="search-box">
            <label class="screen-reader-text" for="search-box-id-search-input"><?= __('Search','edusystem').':'; ?></label>
            <input type="search" id="search-box-id-search-input" name="s" value="<?= (isset($_GET['s']) && !empty($_GET['s'])) ? $_GET['s'] : ''; ?>">
            <input type="submit" id="search-submit" class="button" value="Search">
        </p>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $list_student_institutes->display() ?>
    </form>  
</div>