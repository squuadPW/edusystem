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
        <?php setcookie('message', '', time(), '/') ?>
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

            <div class="capability-group" >

                <h3><?= __('Admission', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                            
                        <?php render_capability_checkbox('manager_admission_aes', __('Required Documents', 'edusystem'), $capabilities) ?>
                        
                        <ul>

                            <li  class="capability-item ">
                                <?php render_capability_checkbox('manager_documents_aes', __('Manage Admission', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item ">
                                <?php render_capability_checkbox('updating_student_documents', __('Updating student documents', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item ">
                                <?php render_capability_checkbox('manager_statusbar_student', __('Can see status bar of student', 'edusystem'), $capabilities) ?>
                            </li>

                        </ul>

                    </li>

                    <li  class="capability-item ">
                        <?php render_capability_checkbox('only_read_admission_aes', __('Read Admission', 'edusystem'), $capabilities) ?>
                    </li>

                </ul>
            </div>

            <div class="capability-group">

                <h3><?= __('Certifications', 'edusystem'); ?></h3>

                <ul>

                    <li class="capability-item">

                        <?php render_capability_checkbox('manager_certificates', __('Certificates', 'edusystem'), $capabilities) ?>

                        <ul>

                            <li  class="capability-item ">
                                <?php render_capability_checkbox('manager_documents_certificates', __('Documents of certificates', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item ">  
                                <?php render_capability_checkbox('manager_users_signatures_certificate', __('Users and signatures', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item ">
                                <?php render_capability_checkbox('manager_id_card', __('ID Card of students', 'edusystem'), $capabilities) ?>
                            </li>
                            
                            <li  class="capability-item ">
                                <?php render_capability_checkbox('manager_configuration_certificates', __('Configuration of certificates', 'edusystem'), $capabilities) ?> 
                            </li>
                        </ul>

                    </li>

                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Academic', 'edusystem'); ?></h3>

                <ul>

                    <li class="capability-item">

                        <?php render_capability_checkbox('manager_academic_aes', __('Academic', 'edusystem'), $capabilities) ?>

                        <ul>

                            <li  class="capability-item" >
                                <?php render_capability_checkbox('manager_academic_periods_aes', __('Academic periods', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item" >
                                <?php render_capability_checkbox('manager_academic_offers_aes', __('Academic offers', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item" >
                                <?php render_capability_checkbox('manager_academic_projection_aes', __('Academic projection', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item" >
                                <?php render_capability_checkbox('manager_automatically_inscriptions', __('Autoenrollment', 'edusystem'), $capabilities) ?>
                            </li>

                            <li  class="capability-item" >
                                <?php render_capability_checkbox('manager_graduations_aes', __('Student graduations', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_requests_aes', __('Requests', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_scholarship_aes', __('Scholarship students', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_availables_scholarship_aes', __('Available scholarships', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_pensums', __('Pensum', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_programs', __('Program', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_enrollments_aes', __('Enrollments', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('can_regenerate_projection', __('Can regenerate projection', 'edusystem'), $capabilities) ?>
                            </li>
                            
                            <li class="capability-item" >
                                <?php render_capability_checkbox('withdraw_student', __('Can withdraw student', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_school_subjects', __('School subjects', 'edusystem'), $capabilities) ?>      

                                <ul>

                                    <li class="capability-item" >
                                        <?php render_capability_checkbox('manager_school_subjects_aes', __('List of school subjects', 'edusystem'), $capabilities) ?> 
                                    </li>

                                    <li class="capability-item" >
                                        <?php render_capability_checkbox('manager_edit_school_subjects', __('Edit and create school subjects', 'edusystem'), $capabilities) ?> 
                                    </li>

                                </ul>

                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_feed', __('Student banners', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_dynamic_links', __('Payment link', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item" >
                                <?php render_capability_checkbox('manager_student_matrix', __('Manager student matrix', 'edusystem'), $capabilities) ?>
                            </li>
                        </ul>

                    </li>

                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Payments', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_payments_read_aes', __('Only read payments', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">

                        <?php render_capability_checkbox('manager_payments_aes', __('Payments', 'edusystem'), $capabilities) ?>

                        <ul>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_payment_plans', __('Payment plans', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_payment_school_subjects', __('Payment school subjects', 'edusystem'), $capabilities, ['manager_school_subjects_aes']) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_payment_fees', __('Fees', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_payment_comissions', __('Comissions', 'edusystem'), $capabilities) ?>
                            </li>

                        </ul>

                    </li>

                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Staff', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_staff_menu_aes', __('Staff', 'edusystem'), $capabilities) ?>
                    
                        <ul>
                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_staff_aes', __('Manage staff', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_institutes_aes', __('Institutes', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_alliances_aes', __('Alliances', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_teachers_aes', __('Teachers', 'edusystem'), $capabilities) ?>
                            </li>

                        </ul>
                    </li>
                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Communications', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">

                        <?php render_capability_checkbox('manager_communications_aes', __('Communications', 'edusystem'), $capabilities) ?>
                    
                        <ul>
                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_send_email_aes', __('Email to students', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_send_notification_aes', __('Email to staff', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_templates_emails', __('Templates emails', 'edusystem'), $capabilities) ?>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="capability-group">

                <h3><?= __('Report', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_sales_aes', __('Summary', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_accounts_receivables_aes', __('Accounts receivable', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_report_sales_product', __('Sales by product', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_comissions_aes', __('Comissions', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_report_billing_ranking_aes', __('Billing ranking', 'edusystem'), $capabilities) ?>
                    </li>

                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_report_students_aes', __('Students', 'edusystem'), $capabilities) ?>
                    </li>

                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Configurations', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_settings_aes', __('Settings menu', 'edusystem'), $capabilities) ?>
                    
                        <ul>
                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_departments_aes', __('Departments', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_configuration_options_aes', __('Configurations of the site', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_custom_inputs', __('Custom inputs', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_grades_by_country', __('Grades by Country', 'edusystem'), $capabilities) ?>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="capability-group">

                <h3><?= __('Media', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_media_aes', __('Media', 'edusystem'), $capabilities) ?>
                    </li>
                </ul>

            </div>
            
            <div class="capability-group">

                <h3><?= __('Users', 'edusystem'); ?></h3>

                <ul>

                    <li class="capability-item">

                        <?php render_capability_checkbox('manager_users_aes', __('Users', 'edusystem'), $capabilities) ?>
                    
                        <ul>

                            <li class="capability-item">
                                <?php render_capability_checkbox('manager_documents_certificates', __('List users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('create_users', __('create users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('edit_users', __('Edit users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('delete_users', __('Delete users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('promote_users', __('Promote users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('remove_users', __('Remove users', 'edusystem  '), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('switch_users', __('Switch users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('list_users', __('List users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('create_users', __('Create users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('edit_users', __('Edit users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('delete_users', __('Delete users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('promote_users', __('Promote users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('remove_users', __('Remove users', 'edusystem'), $capabilities) ?>
                            </li>

                            <li class="capability-item">
                                <?php render_capability_checkbox('switch_users', __('Switch users', 'edusystem'), $capabilities) ?>
                            </li>
                        </ul>
                    </li>

                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Subscription', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_epc', __('Subscription', 'edusystem'), $capabilities) ?>
                    </li>
                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Edusystem Logs', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('manager_logs', __('Edusystem Logs', 'edusystem'), $capabilities) ?>
                    </li>
                </ul>

            </div>

            <div class="capability-group">

                <h3><?= __('Split Payment Method', 'edusystem'); ?></h3>

                <ul>
                    <li class="capability-item">
                        <?php render_capability_checkbox('split_payment_method', __('Split Payment Method', 'edusystem'), $capabilities) ?>
                    </li>
                </ul>
            </div>

        </div>

        <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
            <button class="button button-primary"><?= __('Save Changes', 'edusystem'); ?></button>
        </div>
        
    </form>

</div>
<?php
    include(plugin_dir_path(__FILE__) . 'modal-delete-department.php');
    
    function render_capability_checkbox($cap_key, $label, $capabilities, $activates = [] ) {

        $departments_subscription = get_option('site_departments_subscription') ? json_decode(get_option('site_departments_subscription')) : [];

        // determina si el checkbox debe estar deshabilitado por no venir en la suscripción
        $disabled = !in_array($cap_key, $departments_subscription);
        $disabled_attribute = $disabled ? 'capability-disabled' : '';

        // determina si el checkbox debe estar marcado
        $checked = ( isset($capabilities) && !empty($capabilities) && isset($capabilities[$cap_key]) ) ?? false;
        $checked_attribute = $checked && $disabled ? 'checked' : '';

        // atributo data-activates para indicar qué checkboxes activa
        $activates_attribute = !empty($activates) ? 'data-activates="' . implode(',', $activates) . '"' : '';

        ?>
            <label for="<?= $cap_key ?>" <?= $disabled_attribute ?> >

                <input type="checkbox" id="<?= $cap_key ?>" name="capabilities[]" value="<?= $cap_key ?>"  
                    <?= $checked_attribute ?> <?= $disabled_attribute ?> <?= $activates_attribute ?>
                >
                <span><?= $label ?></span>
            </label>
        <?php
    }
?>