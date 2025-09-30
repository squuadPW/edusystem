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

  <div class="card" style="max-width: 100% !important;">
    <div class="card-header">
      <h3><?= sprintf(__('Settings for %s', 'edusystem'), get_bloginfo('name')); ?></h3>
    </div>
    <div class="card-body-configuration">

      <?php
      $segment_options = array(
        'admission' => __('Admis.', 'edusystem'),       // Original: Admission (or 'Enroll.')
        'administration' => __('Admin.', 'edusystem'),      // Original: Administration
        'moodle' => __('Moodle', 'edusystem'),       // Already short
        'crm' => __('CRM', 'edusystem'),          // Already short
        'offers' => __('Offers', 'edusystem'),       // Original: Offers (or 'Deals')
        'inscriptions' => __('Enroll.', 'edusystem'),      // Original: Inscriptions (or 'Reg.')
        'notifications' => __('Emails', 'edusystem'),       // Original: Notifications (or 'Notif.')
        'design' => __('Design', 'edusystem'),       // Original: Design
      );

      // You can define an initial active option if you like:
      $active_option = 'admission';
      ?>

      <section class="segment" style="display: flex; margin: 0px 20px 30px 20px;">
        <?php foreach ($segment_options as $data_option => $label): ?>
          <div class="segment-button <?php echo ($data_option === $active_option) ? 'active' : ''; ?>"
            data-option="<?php echo esc_attr($data_option); ?>">
            <?php echo esc_html($label); ?>
          </div>
        <?php endforeach; ?>
      </section>

      <form method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_configuration_options_content&action=save_options'); ?>"
        enctype="multipart/form-data">
        <input type="hidden" name="type">
        <div class="form-group" id="by_admission">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="documents-ok"><?= __('Days elapsed to display documents in green (less than)'); ?></label> <br>
            <span>
              < </span><input type="number" id="documents-ok" name="documents_ok"
                  value="<?php echo get_option('documents_ok') ?>" required>
          </div>
          <div class="form-group" style="padding: 10px">
            <label
              for="documents-warning"><?= __('Days elapsed to display documents in warning (less than)'); ?></label>
            <br>
            <span>
              < </span><input type="number" id="documents-warning" name="documents_warning"
                  value="<?php echo get_option('documents_warning') ?>" required>
          </div>
          <div class="form-group" style="padding: 10px">
            <label for="documents-red"><?= __('Days elapsed to display documents in red (greater than)'); ?></label>
            <br>
            <span>></span><input type="number" id="documents-red" name="documents_red"
              value="<?php echo get_option('documents_red') ?>" required>
          </div>
        </div>
        <div id="by_administration" style="display: none">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label
              for="payment-due"><?= __('Days elapsed after payment due to block access to the site (greater than)'); ?></label>
            <br>
            <span>></span><input type="number" id="payment-due" name="payment_due"
              value="<?php echo get_option('payment_due') ?>" required>
          </div>
          <div class="form-group">
            <a style="margin: 5px"
              href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=set_max_access_date') ?>"
              class="button button-outline-primary" onclick="return confirm('Are you sure?');">
              <?= __('Update max date students', 'edusystem'); ?>
            </a>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label
              for="payment-due"><?= __('Days allotted to upload proof of studies since first enrollment (greater than)'); ?></label>
            <br>
            <span>></span><input type="number" id="proof-due" name="proof_due"
              value="<?php echo get_option('proof_due') ?>" required>
          </div>
          <div class="form-group">
            <a style="margin: 5px"
              href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=set_max_date_upload_at') ?>"
              class="button button-outline-primary" onclick="return confirm('Are you sure?');">
              <?= __('Update max date upload at', 'edusystem'); ?>
            </a>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label
              for="default-lang-site"><?= __('Default site language'); ?></label>
            <br>
            <input type="text" id="default-lang-site" name="default_lang_site"
              value="<?php echo get_option('default_lang_site') ?>" required>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="virtual-access" name="virtual_access" <?php echo get_option('virtual_access') == 'on' ? 'checked' : '' ?>>
            <label for="virtual-access"><?= __('Show button for virtual classroom'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="auto-enroll-regular" name="auto_enroll_regular" <?php echo get_option('auto_enroll_regular') == 'on' ? 'checked' : '' ?>>
            <label for="auto-enroll-regular"><?= __('Auto enroll regular student'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="auto-enroll-elective" name="auto_enroll_elective" <?php echo get_option('auto_enroll_elective') == 'on' ? 'checked' : '' ?>>
            <label for="auto-enroll-elective"><?= __('Auto enroll elective student'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="show-modal-elective" name="show_modal_electives" <?php echo get_option('show_modal_electives') == 'on' ? 'checked' : '' ?>>
            <label for="show-modal-elective"><?= __('Show modal select elective'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="aditional-electives" name="use_elective_aditional" <?php echo get_option('use_elective_aditional') == 'on' ? 'checked' : '' ?>>
            <label for="aditional-electives"><?= __('Use aditional electives'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="show-equivalence" name="show_equivalence_projection" <?php echo get_option('show_equivalence_projection') == 'on' ? 'checked' : '' ?>>
            <label for="show-equivalence"><?= __('Show equivalency subjects in student projection'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="show-table-subjects-coursing" name="show_table_subjects_coursing" <?php echo get_option('show_table_subjects_coursing') == 'on' ? 'checked' : '' ?>>
            <label for="show-table-subjects-coursing"><?= __('Show table subjects coursing'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="disabled-redirect" name="disabled_redirect" <?php echo get_option('disabled_redirect') == 'on' ? 'checked' : '' ?>>
            <label for="disabled-redirect"><?= __('Disable redirection for institutes and alliances to the administrator'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="disabled-switch-language" name="disable_switch_language" <?php echo get_option('disable_switch_language') == 'on' ? 'checked' : '' ?>>
            <label for="disabled-switch-language"><?= __('Disable switch language'); ?></label>
          </div>
          <div class="form-group" style="padding: 10px">
            <input type="checkbox" id="hide-grade-student" name="hide_grade_student" <?php echo get_option('hide_grade_student') == 'on' ? 'checked' : '' ?>>
            <label for="hide-grade-student"><?= __('Hide grade student'); ?></label>
          </div>
        </div>
        <div id="by_moodle" style="display: none">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_coordination"><?= __('Moodle URL', 'edusystem'); ?></label> <br>
            <input class="full-input" name="moodle_url" type="text" id="moodle_url"
              value="<?= get_option('moodle_url'); ?>" required>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_academic_management"><?= __('Moodle Token', 'edusystem'); ?></label> <br>
            <input class="full-input" name="moodle_token" type="text" id="moodle_token"
              value="<?= get_option('moodle_token'); ?>" required>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="offer_complete"><?= __('Public information course', 'edusystem'); ?></label> <br>
            <select name="public_course_id" class="js-example-basic" style="width: 100%;">
              <option value="" <?= get_option('public_course_id') == '' ? 'selected' : ''; ?>>
                <?= __('Without course', 'edusystem'); ?>
              </option>
              <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id']; ?>" <?= (get_option('public_course_id') == $course['id']) ? 'selected' : ''; ?>>
                  <?= $course['fullname']; ?> (<?= $course['shortname']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div id="by_crm" style="display: none">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="crm_url"><?= __('CRM URL (example: https://crm.example.com/api/v1/)', 'edusystem'); ?></label>
            <br>
            <input class="full-input" name="crm_url" type="text" id="crm_url" value="<?= get_option('crm_url'); ?>"
              required>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="crm_token"><?= __('CRM Token', 'edusystem'); ?></label> <br>
            <input class="full-input" name="crm_token" type="text" id="crm_token"
              value="<?= get_option('crm_token'); ?>" required>
          </div>
        </div>
        <div id="by_offers" style="display: none">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="offer_complete"><?= __('Coupon registration fee in complete payment', 'edusystem'); ?></label>
            <br>
            <select class="full-input" name="offer_complete" id="offer_complete">
              <option value="" <?= get_option('offer_complete') == '' ? 'selected' : ''; ?>>Without offer</option>
              <option value="50% Registration fee" <?= get_option('offer_complete') == '50% Registration fee' ? 'selected' : ''; ?>>50% Registration fee</option>
              <option value="100% Registration fee" <?= get_option('offer_complete') == '100% Registration fee' ? 'selected' : ''; ?>>100% Registration fee</option>
            </select>
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label
              for="offer_quote"><?= __('Coupon for registration fee with installment payments', 'edusystem'); ?></label>
            <br>
            <select class="full-input" name="offer_quote" id="offer_quote">
              <option value="" <?= get_option('offer_quote') == '' ? 'selected' : ''; ?>>Without offer</option>
              <option value="50% Registration fee" <?= get_option('offer_quote') == '50% Registration fee' ? 'selected' : ''; ?>>50% Registration fee</option>
              <option value="100% Registration fee" <?= get_option('offer_quote') == '100% Registration fee' ? 'selected' : ''; ?>>100% Registration fee</option>
            </select>
          </div>
          <div class="form-group" style="padding: 10px">
            <label for="max-date-offer"><?= __('Max date'); ?></label>
            <input type="date" id="max-date-offer" name="max_date_offer" value="<?php
            $saved_timestamp = get_option('max_date_offer');
            $date_value = '';
            if (!empty($saved_timestamp)) {
              // Crear un objeto DateTime a partir del timestamp (que es UTC)
              $datetime_obj = new DateTimeImmutable('@' . $saved_timestamp);

              // Establecer la zona horaria al objeto DateTime para que represente
              // la fecha y hora en la zona horaria de WordPress
              $datetime_obj = $datetime_obj->setTimezone(new DateTimeZone(wp_timezone_string()));

              // Formatear para el input type="date"
              $date_value = $datetime_obj->format('Y-m-d');
            }
            echo esc_attr($date_value);
            ?>">
          </div>
        </div>
        <div id="by_notifications" style="display: none">
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_coordination"><?= __('Email academic coordination'); ?></label> <br>
            <input class="full-input" type="email" id="email_coordination" name="email_coordination"
              value="<?php echo get_option('email_coordination') ?>">
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_academic_management"><?= __('Email academic management'); ?></label> <br>
            <input class="full-input" type="email" id="email_academic_management" name="email_academic_management"
              value="<?php echo get_option('email_academic_management') ?>">
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_manager"><?= __('Email national and international manager'); ?></label> <br>
            <input class="full-input" type="email" id="email_manager" name="email_manager"
              value="<?php echo get_option('email_manager') ?>">
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_administration"><?= __('Email administration'); ?></label> <br>
            <input class="full-input" type="email" id="email_administration" name="email_administration"
              value="<?php echo get_option('email_administration') ?>">
          </div>
          <div class="form-group" style="padding: 0px 10px 10px 10px;">
            <label for="email_admission"><?= __('Email admission'); ?></label> <br>
            <input class="full-input" type="email" id="email_admission" name="email_admission"
              value="<?php echo get_option('email_admission') ?>">
          </div>
        </div>
        <div id="inscriptions" style="display: none">
          <div style="text-align: center;">
            <div>
              <a style="margin: 5px; text-wrap: auto;"
                href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=clear_electives') ?>"
                class="button button-outline-primary" onclick="return confirm('Are you sure?');">
                <?= __('Deactivate the elective selector and mark that they have not seen electives during this period <br>that they were not due (this will advance them in the screening).', 'edusystem'); ?>
              </a>
            </div>
            <div>
              <a style="margin: 5px"
                href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_academic_projections') ?>"
                class="button button-outline-primary" onclick="return confirm('Are you sure?');">
                <?= __('Generate pending academic projections', 'edusystem'); ?>
              </a>
            </div>
          </div>
        </div>
        <div id="by_design" style="display: none">
          <div class="logo-upload-section">
            <div class="form-group">
              <label for="logo_admin"><?= __('Administrator Logo', 'edusystem'); ?></label>
              <input type="file" name="logo_admin" id="logo-admin" accept="image/*">
            </div>
            <div class="logo-preview">
              <?= wp_get_attachment_image(get_option('logo_admin'), 'medium'); ?>
            </div>
          </div>

          <div class="logo-upload-section">
            <div class="form-group">
              <label for="logo_admin_login"><?= __('Administrator Login Logo'); ?></label>
              <input type="file" name="logo_admin_login" id="logo-admin-login" accept="image/*">
            </div>
            <div class="logo-preview">
              <?= wp_get_attachment_image(get_option('logo_admin_login'), 'medium'); ?>
            </div>
          </div>
        </div>
        <div class="form-group" id="save-configuration" style="text-align: center">
          <button type="submit" class="btn btn-primary"><?= __('Save settings', 'edusystem'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>