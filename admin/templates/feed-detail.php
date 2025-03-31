<div class="wrap">
    <?php if (isset($feed) && !empty($feed)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Feed Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Feed', 'edusystem'); ?></h2>
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
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_feed_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_feed_content&action=save_feed_details'); ?>"
                            enctype="multipart/form-data">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Feed Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="feed_id" value="<?= $feed->id ?>">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="title"><b><?= __('Title', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="text" step="0" name="title"
                                            value="<?= $feed->title; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="attach_id_desktop"><b><?= __('Image for desktop', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="file" step="0" name="attach_id_desktop" <?= $feed ? '' : 'required' ?>>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="attach_id_mobile"><b><?= __('Image for mobile', 'edusystem'); ?></b> <span class="text-gray">(optional)</span></label><br>
                                        <input type="file" step="0" name="attach_id_mobile" <?= $feed ? '' : 'required' ?>>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <input type="checkbox" name="use_max_date" id="use_max_date"
                                        <?= $feed->max_date ? 'checked' : ''; ?> style="width: auto">
                                        <label for="use_max_date"><b><?= __('Hide post after', 'edusystem'); ?></b></label>
                                    </div>

                                    <?php
                                        $style = ($feed->max_date) ? '' : 'display: none';
                                    ?>
                                    <div style="font-weight:400; <?= $style ?>" class="space-offer" id="use_max_date_input">
                                        <label for="max_date"><b><?= __('Max date visible', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="date" step="0" name="max_date"
                                            value="<?= $feed->max_date; ?>">
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($feed) && !empty($feed)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add offer', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <h3
                            style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                            <b><?= __('Preview', 'edusystem'); ?></b>
                        </h3>

                        <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: start">
                                        <div style="text-align: center">
                                            Desktop
                                        </div>
                                        <?php
                                        echo wp_get_attachment_image($feed->attach_id_desktop, 'medium');
                                        ?>
                                    </div>
                                    <div style="font-weight:400; text-align: start">
                                        <div style="text-align: center">
                                            Mobile
                                        </div>
                                        <?php
                                        echo wp_get_attachment_image($feed->attach_id_mobile, 'medium');
                                        ?>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>