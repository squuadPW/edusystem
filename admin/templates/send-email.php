<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Send email', 'aes'); ?></h2>

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

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_send_email_content&action=send_email'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                <b><?= __('Email Information', 'aes'); ?></b>
                            </h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                        <td>
                                            <label for="academic_period">
                                                <b><?= esc_html(__('Academic period of students to send', 'aes')); ?></b>
                                            </label>
                                            <p>
                                                <select name="academic_period" required>
                                                    <?php foreach ($periods as $key => $period) { ?>
                                                        <option value="<?php echo $period->code; ?>">
                                                            <?php echo $period->name; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </p>
                                        </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="subject"><b><?= __('Subject', 'aes'); ?></b></label><br>
                                                <input type="text" name="subject" id="subject" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="message"><b><?= __('Message', 'aes'); ?></b></label><br>
                                                <textarea type="text" name="message" id="message" required></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div
                                    style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Send', 'aes'); ?></button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    input,
    textarea {
        width: 100%;
    }
</style>