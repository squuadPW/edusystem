<div style="margin: 20px auto">    

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

    <div class="card" style="max-width: 90% !important;">
        <div class="card-header">
            <h3><?= __('Settings for AES', 'aes'); ?></h3>
        </div>
        <div class="card-body-configuration">

            <section class="segment" style="display: flex; margin: 0px 20px 30px 20px;">
                <div class="segment-button active" data-option="admission"><?= __('Admission', 'aes'); ?></div>
                <div class="segment-button" data-option="administration"><?= __('Administration', 'aes'); ?></div>
                <div class="segment-button" data-option="moodle"><?= __('Moodle', 'aes'); ?></div>
                <div class="segment-button" data-option="offers"><?= __('Offers', 'aes'); ?></div>
                <div class="segment-button" data-option="inscriptions"><?= __('Inscriptions', 'aes'); ?></div>
                <div class="segment-button" data-option="notifications"><?= __('Notifications', 'aes'); ?></div>
            </section>

            <form method="post"
                action="<?= admin_url('admin.php?page=add_admin_form_configuration_options_content&action=save_options'); ?>">
                <input type="hidden" name="type">
                <div class="form-group" id="by_admission">
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="documents-ok"><?= __('Days elapsed to display documents in green (less than)'); ?></label> <br>
                    <span><</span><input type="number" id="documents-ok" name="documents_ok" value="<?php echo get_option('documents_ok') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 10px">
                    <label for="documents-warning"><?= __('Days elapsed to display documents in warning (less than)'); ?></label> <br>
                    <span><</span><input type="number" id="documents-warning" name="documents_warning" value="<?php echo get_option('documents_warning') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 10px">
                    <label for="documents-red"><?= __('Days elapsed to display documents in red (greater than)'); ?></label> <br>
                    <span>></span><input type="number" id="documents-red" name="documents_red" value="<?php echo get_option('documents_red') ?>" required>
                  </div>
                </div>
                <div id="by_administration" style="display: none">
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="payment-due"><?= __('Days elapsed after payment due to block access to the site (greater than)'); ?></label> <br>
                    <span>></span><input type="number" id="payment-due" name="payment_due" value="<?php echo get_option('payment_due') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 10px">
                    <input type="checkbox" id="student-continue" name="student_continue" <?php echo get_option('student_continue') == 'on' ? 'checked' : '' ?>>
                    <label for="student-continue"><?= __('Show button for students to register for the next academic cut'); ?></label>
                  </div>
                </div>
                <div id="by_moodle" style="display: none">
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_coordination"><?= __('Moodle URL','aes'); ?></label> <br>
                    <input class="full-input" name="moodle_url" type="text" id="moodle_url" value="<?= get_option('moodle_url'); ?>" required>
                  </div>
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_academic_management"><?= __('Moodle Token','aes'); ?></label> <br>
                    <input class="full-input" name="moodle_token" type="text" id="moodle_token" value="<?= get_option('moodle_token'); ?>" required>
                  </div>
                </div>
                <div id="by_offers" style="display: none">
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_coordination"><?= __('Coupon registration fee in complete payment (empty is disabled)','aes'); ?></label> <br>
                    <input class="full-input" name="offer_complete" type="text" id="offer_complete" value="<?= get_option('offer_complete'); ?>">
                  </div>
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_academic_management"><?= __('Coupon for registration fee with installment payments (empty is disabled)','aes'); ?></label> <br>
                    <input class="full-input" name="offer_quote" type="text" id="offer_quote" value="<?= get_option('offer_quote'); ?>">
                  </div>
                </div>
                <div id="by_notifications" style="display: none">
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_coordination"><?= __('Email academic coordination and admission'); ?></label> <br>
                    <input class="full-input" type="email" id="email_coordination" name="email_coordination" value="<?php echo get_option('email_coordination') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_academic_management"><?= __('Email academic management'); ?></label> <br>
                    <input class="full-input" type="email" id="email_academic_management" name="email_academic_management" value="<?php echo get_option('email_academic_management') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_manager"><?= __('Email national and international manager'); ?></label> <br>
                    <input class="full-input" type="email" id="email_manager" name="email_manager" value="<?php echo get_option('email_manager') ?>" required>
                  </div>
                  <div class="form-group" style="padding: 0px 10px 10px 10px;">
                    <label for="email_administration"><?= __('Email administration'); ?></label> <br>
                    <input class="full-input" type="email" id="email_administration" name="email_administration" value="<?php echo get_option('email_administration') ?>" required>
                  </div>
                </div>
                <div id="inscriptions" style="display: none">
                  <div class="form-group" style="text-align: center">
                      <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=clear_electives') ?>"><button type="button" class="btn btn-primary"><?= __('Disable elective selector for all students', 'aes'); ?></button></a>
                  </div>
                  <div class="form-group" style="text-align: center">
                      <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_academic_projections') ?>"><button type="button" class="btn btn-primary"><?= __('Generate pending academic projections', 'aes'); ?></button></a>
                  </div>
                  <div class="form-group" style="text-align: center">
                      <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=automatically_enrollment&cut=C') ?>"><button type="button" class="btn btn-primary"><?= __('Enrollment process for students of the C cut - 20242025', 'aes'); ?></button></a>
                  </div>
                  <div class="form-group" style="text-align: center">
                      <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=automatically_enrollment&cut=D') ?>"><button type="button" class="btn btn-primary"><?= __('Enrollment process for students of the D cut - 20242025', 'aes'); ?></button></a>
                  </div>
                </div>
                <div class="form-group" id="save-configuration" style="text-align: center">
                    <button type="submit" class="btn btn-primary"><?= __('Save settings', 'aes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>