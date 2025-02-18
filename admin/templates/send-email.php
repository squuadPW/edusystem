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

    <div class="card" style="min-width: 100% !important;">
        <div class="card-header">
            <h3><?= __('Send email for students', 'aes'); ?></h3>
        </div>
        <div class="card-body">

            <section class="segment" style="display: flex; margin: 0px 20px 30px 20px;">
                <div class="segment-button active" data-option="group"><?= __('By group', 'aes'); ?></div>
                <div class="segment-button" data-option="email"><?= __('By email', 'aes'); ?></div>
            </section>

            <form method="post"
                action="<?= admin_url('admin.php?page=add_admin_form_send_email_content&action=send_email'); ?>">
                <input type="hidden" name="type" value="1">
                <div class="form-group" id="by_group">
                    <div class="form-group">
                        <label for="academic_period"><?= __('Academic period of students to send', 'aes'); ?></label>
                        <select name="academic_period" style="width: 100%" >
                            <option value="">Select an option</option>
                            <?php foreach ($periods as $key => $period): ?>
                                <option value="<?php echo $period->code; ?>"><?php echo $period->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="academic_period"><?= __('Academic cut-off filter to be applied', 'aes'); ?></label>
                        <select name="academic_period_cut_filter" style="width: 100%" >
                            <option value="">Select an option</option>
                            <option value="1">Initial</option>
                            <option value="2">Current enrollment (and electives)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="academic_period"><?= __('Academic period cut-off', 'aes'); ?></label>
                        <select name="academic_period_cut" style="width: 100%" >
                            <option value="">Select an option</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                </div>
                <div id="by_email" style="display: none">
                    <div class="form-group">
                        <label for="email_student"><?= __('Email student', 'aes'); ?></label>
                        <input type="email" name="email_student" id="email_student">
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject"><?= __('Title', 'aes'); ?></label>
                    <input type="text" name="subject" id="subject" required>
                </div>
                <div class="form-group">
                    <label for="message"><?= __('Message', 'aes'); ?></label>
                    <ul>
                        <li>For the student's full name use: <strong>{{student}}</strong></li>
                    </ul>
                    <?= wp_editor('',
                        'message',      // ID del editor
                        array(
                            'textarea_name' => 'message', // Nombre del campo
                            'media_buttons' => false, // Botón de subir archivos
                            'teeny' => true, // Editor completo (true para versión simplificada)
                        )
                    ); ?>
                </div>
                <div class="form-group" style="display: flex">
                    <input style="margin: auto 6px auto 6px;" type="checkbox" name="email_parent" id="email_parent">
                    <label style="margin-bottom: 0px"
                        for="email_parent"><?= __('Send the same email to the parent', 'aes'); ?></label>
                </div>
                <div class="form-group" style="text-align: center">
                    <button type="button" id="summary-email" class="btn btn-primary"><?= __('Send', 'aes'); ?></button>
                    <button type="submit" id="summary-email-send" class="btn btn-primary" style="display: none"><?= __('Send', 'aes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id='summary-email-modal' class='modal' style='display:none'>
    <div class='modal-content' style="width: 70%;">
        <div class="modal-header">
        <h3 style="font-size:20px;"><?= __('Summary') ?></h3>
            <span id="summary-email-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
        </div>
        <div class="modal-body" style="padding:10px; overflow: auto; max-height: 400px;">
            <b>List of students to send email:</b>
            <ul id="list-students-email">
                <li>Loading...</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button id="summary-email-button" type="submit" class="button button-outline-primary modal-close"><?= __('Send','aes'); ?></button>
            <button id="summary-email-exit-button" type="button" class="button button-danger modal-close"><?= __('Cancel','aes'); ?></button>
        </div>
    </div>
</div>