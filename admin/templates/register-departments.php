<div class="wrap">
    <?php if (isset($message) && !empty($message)) { ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $message; ?></p>
        </div>
    <?php } ?>
    <?php if (isset($_COOKIE['message_success']) && !empty($_COOKIE['message_success'])): ?>

        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message_success']; ?></p>
        </div>
        <?php setcookie('message_success', '', time(), '/') ?>
    <?php endif; ?>
    <?php if (isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <h2 style="margin-bottom:15px;"><?= __('Edit Departments', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Departments', 'edusystem'); ?></h2>
    <?php endif; ?>
    <div style="display:flex;width:100%;justify-content:start;margin-top:10px;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_department_content') ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>
    <?php if (isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
            <a id="delete_department" data-id="<?= $_GET['department_id']; ?>"
                class="button button-danger"><?= __('Delete', 'edusystem'); ?></a>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= admin_url('admin.php?page=add_admin_department_content&action=add_department'); ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Name', 'edusystem'); ?></label></th>
                    <td>
                        <input name="name" style="width:100%" type="text"
                            value="<?= (isset($name) && !empty($name)) ? $name : ''; ?>" class="regular-text" required
                            <?= (isset($name) && !empty($name)) ? 'readonly' : ''; ?>>
                        <input type="hidden" name="department_id"
                            value="<?= (isset($department_id) && !empty($department_id) ? $department_id : ''); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Description', 'edusystem'); ?></label></th>
                    <td>
                        <textarea name="description" row="4" style="width:100%;resize:none;" type="text"
                            class="regular-text"
                            required><?= (isset($description) && !empty($description)) ? $description : ''; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3 style="margin-bottom:15px;"><?= __('Modules', 'edusystem'); ?></h3>
        <div class="form-section">
            <div class="capability-group">
                <h3><?= __('Admission', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_admission_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_admission_aes" id="manager_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_admission_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_admission_aes" <?= !in_array('manager_admission_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Manage Admission', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_documents_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_documents_aes" id="manager_documents_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_documents_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_documents_aes" <?= !in_array('manager_documents_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Required Documents', 'edusystem'); ?></label>
                </div>
                <div class="capability-item">
                    <input <?= !in_array('only_read_admission_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="only_read_admission_aes" id="only_read_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['only_read_admission_aes'])) ? 'checked' : ''; ?>>
                    <label for="only_read_admission_aes" <?= !in_array('only_read_admission_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Read Admission', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Certifications', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_certificates" value="manager_certificates" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_certificates'])) ? 'checked' : ''; ?>>
                    <label for="manager_certificates" <?= !in_array('manager_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Certificates', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_documents_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" id="manager_documents_certificates" value="manager_documents_certificates"
                        <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_documents_certificates'])) ? 'checked' : ''; ?>>
                    <label for="manager_documents_certificates" <?= !in_array('manager_documents_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Documents of certificates', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_users_signatures_certificate', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" id="manager_users_signatures_certificate"
                        value="manager_users_signatures_certificate" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_users_signatures_certificate'])) ? 'checked' : ''; ?>>
                    <label for="manager_users_signatures_certificate"
                        <?= !in_array('manager_users_signatures_certificate', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Users and signatures', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_id_card', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_id_card" value="manager_id_card" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_id_card'])) ? 'checked' : ''; ?>>
                    <label for="manager_id_card" <?= !in_array('manager_id_card', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('ID Card of students', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_configuration_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" id="manager_configuration_certificates"
                        value="manager_configuration_certificates" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_configuration_certificates'])) ? 'checked' : ''; ?>>
                    <label for="manager_configuration_certificates" <?= !in_array('manager_configuration_certificates', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Configuration of certificates', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Academic', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_academic_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_academic_aes" id="manager_academic_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_academic_aes" <?= !in_array('manager_academic_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Academic', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_academic_periods_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_academic_periods_aes" id="manager_academic_periods_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_periods_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_academic_periods_aes" <?= !in_array('manager_academic_periods_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Academic periods', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_academic_offers_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_academic_offers_aes" id="manager_academic_offers_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_offers_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_academic_offers_aes" <?= !in_array('manager_academic_offers_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Academic offers', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_academic_projection_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" value="manager_academic_projection_aes"
                        id="manager_academic_projection_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_projection_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_academic_projection_aes" <?= !in_array('manager_academic_projection_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Academic projection', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_automatically_inscriptions', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" value="manager_automatically_inscriptions"
                        id="manager_automatically_inscriptions" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_automatically_inscriptions'])) ? 'checked' : ''; ?>>
                    <label for="manager_automatically_inscriptions" <?= !in_array('manager_automatically_inscriptions', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Autoenrollment', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_graduations_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_graduations_aes" id="manager_graduations_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_graduations_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_graduations_aes" <?= !in_array('manager_graduations_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Student graduations', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_requests_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_requests_aes" id="manager_requests_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_requests_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_requests_aes" <?= !in_array('manager_requests_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Requests', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_scholarship_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_scholarship_aes" id="manager_scholarship_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_scholarship_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_scholarship_aes" <?= !in_array('manager_scholarship_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Scholarship students', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_availables_scholarship_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" value="manager_availables_scholarship_aes"
                        id="manager_availables_scholarship_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_availables_scholarship_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_availables_scholarship_aes" <?= !in_array('manager_availables_scholarship_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Available scholarships', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_pensums', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_pensums" id="manager_pensums" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_pensums'])) ? 'checked' : ''; ?>>
                    <label for="manager_pensums" <?= !in_array('manager_pensums', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Pensum', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_programs', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_programs" id="manager_programs" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_programs'])) ? 'checked' : ''; ?>>
                    <label for="manager_programs" <?= !in_array('manager_programs', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Program', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_enrollments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_enrollments_aes" id="manager_enrollments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_enrollments_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_enrollments_aes" <?= !in_array('manager_enrollments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Enrollments', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('can_regenerate_projection', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="can_regenerate_projection" id="can_regenerate_projection" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['can_regenerate_projection'])) ? 'checked' : ''; ?>>
                    <label for="can_regenerate_projection" <?= !in_array('can_regenerate_projection', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Can regenerate projection', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_school_subjects_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_school_subjects_aes" id="manager_school_subjects_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_school_subjects_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_school_subjects_aes" <?= !in_array('manager_school_subjects_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('School subjects', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_feed', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_feed" id="manager_feed" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_feed'])) ? 'checked' : ''; ?>>
                    <label for="manager_feed" <?= !in_array('manager_feed', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Student banners', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Payments', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_payments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_payments_aes" id="manager_payments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_payments_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_payments_aes" <?= !in_array('manager_payments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Payments', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Staff', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_staff_menu_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_staff_menu_aes" id="manager_staff_menu_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_staff_menu_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_staff_menu_aes" <?= !in_array('manager_staff_menu_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Staff', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_staff_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_staff_aes" id="manager_staff_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_staff_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_staff_aes" <?= !in_array('manager_staff_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Manage staff', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_institutes_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_institutes_aes" id="manager_institutes_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_institutes_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_institutes_aes" <?= !in_array('manager_institutes_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Institutes', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_alliances_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_alliances_aes" id="manager_alliances_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_alliances_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_alliances_aes" <?= !in_array('manager_alliances_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Alliances', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_teachers_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_teachers_aes" id="manager_teachers_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_teachers_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_teachers_aes" <?= !in_array('manager_teachers_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Teachers', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Communications', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_communications_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_communications_aes" value="manager_communications_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_communications_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_communications_aes" <?= !in_array('manager_communications_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Communications', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_send_email_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_send_email_aes" value="manager_send_email_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_send_email_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_send_email_aes" <?= !in_array('manager_send_email_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Email to students', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_send_notification_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" id="manager_send_notification_aes" value="manager_send_notification_aes"
                        <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_send_notification_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_send_notification_aes" <?= !in_array('manager_send_notification_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Email to staff', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_templates_emails', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_templates_emails" id="manager_templates_emails" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_templates_emails'])) ? 'checked' : ''; ?>>
                    <label for="manager_templates_emails" <?= !in_array('manager_templates_emails', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Templates emails', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Report', 'edusystem'); ?></h3>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_sales_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_sales_aes" id="manager_sales_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_sales_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_sales_aes" <?= !in_array('manager_sales_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Summary', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_accounts_receivables_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" value="manager_accounts_receivables_aes"
                        id="manager_accounts_receivables_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_accounts_receivables_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_accounts_receivables_aes" <?= !in_array('manager_accounts_receivables_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Accounts receivable', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_report_sales_product', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_report_sales_product" id="manager_report_sales_product" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_sales_product'])) ? 'checked' : ''; ?>>
                    <label for="manager_report_sales_product" <?= !in_array('manager_report_sales_product', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Sales by product', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_report_students_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_report_students_aes" id="manager_report_students_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_students_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_report_students_aes" <?= !in_array('manager_report_students_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Students', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Configurations', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_settings_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_settings_aes" id="manager_settings_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_settings_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_settings_aes" <?= !in_array('manager_settings_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Settings menu', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_departments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox" name="capabilities[]"
                        value="manager_departments_aes" id="manager_departments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_departments_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_departments_aes" <?= !in_array('manager_departments_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Departments', 'edusystem'); ?></label>
                </div>
                <div class="capability-item indented">
                    <input <?= !in_array('manager_configuration_options_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?> type="checkbox"
                        name="capabilities[]" value="manager_configuration_options_aes"
                        id="manager_configuration_options_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_configuration_options_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_configuration_options_aes" <?= !in_array('manager_configuration_options_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none;"' : '' ?>><?= __('Configurations of the site', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Media', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_media_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_media_aes" value="manager_media_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_media_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_media_aes" <?= !in_array('manager_media_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Media', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Users', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_users_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]"
                        id="manager_users_aes" value="manager_users_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_users_aes'])) ? 'checked' : ''; ?>>
                    <label for="manager_users_aes" <?= !in_array('manager_users_aes', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Users', 'edusystem'); ?></label>
                </div>
            </div>

            <div class="capability-group">
                <h3><?= __('Subscription', 'edusystem'); ?></h3>
                <div class="capability-item">
                    <input <?= !in_array('manager_epc', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?> type="checkbox" name="capabilities[]" id="manager_epc"
                        value="manager_epc" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_epc'])) ? 'checked' : ''; ?>>
                    <label for="manager_epc" <?= !in_array('manager_epc', $departments_subscription) ? 'style="font-style: italic; color: #8080808a; pointer-events: none"' : '' ?>><?= __('Subscription', 'edusystem'); ?></label>
                </div>
            </div>
        </div>
        <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
            <button class="button button-primary"><?= __('Save Changes', 'edusystem'); ?></button>
        </div>
    </form>

</div>
<?php
include(plugin_dir_path(__FILE__) . 'modal-delete-department.php');
?>