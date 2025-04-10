<div class="tabs-content">
    <div class="wrap">
        <div style="text-align:start;">
            <h1 class="wp-heading-line"><?= __('All Templates emails', 'edusystem'); ?></h1>
        </div>

        <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
            <div class="notice notice-success is-dismissible">
                <p><?= $_COOKIE['message']; ?></p>
            </div>
            <?php setcookie('message', '', time(), '/'); ?>
        <?php } ?>
        <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
            <div class="notice notice-error is-dismissible">
                <p><?= $_COOKIE['message-error']; ?></p>
            </div>
            <?php setcookie('message-error', '', time(), '/'); ?>
        <?php } ?>
        <form action="" id="post-filter" method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php $list_templates_emails->display() ?>
        </form>
    </div>
</div>