<div class="wrap">    

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message']; ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php endif; ?>

    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])): ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><?= __('Send email for staff', 'aes'); ?></h3>
        </div>
        <div class="card-body">
            <div class="content-notification-staff">
                <?= __('An email notification will be sent to the X, X and X departments.')?> <br>
                <?= __('This module is essential to keep all departments aligned and updated, thus improving collaboration and response to situations that require immediate attention.', 'aes'); ?>
            </div>
            <form method="post"
                action="<?= admin_url('admin.php?page=add_admin_form_send_notification_content&action=send_notification'); ?>">
                <div class="form-group">
                    <label for="subject"><?= __('Subject', 'aes'); ?></label>
                    <input type="text" name="subject" id="subject" required>
                </div>
                <div class="form-group">
                    <label for="message"><?= __('Message', 'aes'); ?></label>
                    <textarea name="message" id="message" required></textarea>
                </div>
                <div class="form-group" style="text-align: center">
                    <button type="submit" class="btn btn-primary"><?= __('Send', 'aes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>