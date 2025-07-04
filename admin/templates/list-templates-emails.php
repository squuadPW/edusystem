<div class="tabs-content">
    <div class="wrap">
        <div style="text-align:start;">
            <h1 class="wp-heading-line"><?= __('All Templates emails', 'edusystem'); ?></h1>
        </div>

        <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
        ?>

        <form action="" id="post-filter" method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php $list_templates_emails->display() ?>
        </form>
    </div>
</div>