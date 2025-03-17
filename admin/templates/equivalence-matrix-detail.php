<div class="wrap">
    <?php if (isset($equivalence) && !empty($equivalence)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Equivalence details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Equivalence', 'aes'); ?></h2>
    <?php endif; ?>

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
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post" action="<?= admin_url('admin.php?page=add_admin_form_equivalence_matrix_content&action=save_equivalence_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Equivalence Information', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($equivalence) && !empty($equivalence)): ?>
                                            <input type="hidden" name="equivalence_id" id="equivalence_id" value="<?= $equivalence->id; ?>">
                                            <label
                                                for="name"><b><?= __('Name', 'aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="name" value="<?= ucwords($equivalence->name); ?>">
                                        <?php else: ?>
                                            <label for="name"><b><?= __('Name', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="name" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($equivalence) && !empty($equivalence)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add equivalence', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>