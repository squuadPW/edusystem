<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Auto enroll', 'edusystem'); ?></h2>

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?= nl2br($_COOKIE['message']); ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php } ?>
    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php } ?>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_auto_inscription_content&action=save_auto_inscription_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Inscription Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="academic_period"><?= __('School year in which students entered', 'edusystem'); ?></label><br>
                                        <select name="academic_period" style="width: 100%">
                                            <option value="">Select an option</option>
                                            <?php foreach ($periods as $key => $period): ?>
                                                <option value="<?php echo $period->code; ?>"><?php echo $period->name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="academic_period"><?= __('Period in which students entered', 'edusystem'); ?></label><br>
                                        <select name="academic_period_cut" style="width: 100%">
                                            <option value="">Select an option</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:center;gap:5px; display: none" id="enroll-button">
                                <button type="submit"
                                    class="button button-primary" onclick="return confirm('<?= __('Are you sure you want to enroll students?', 'edusystem'); ?>')"><?= __('Enroll', 'edusystem'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>