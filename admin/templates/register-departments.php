<div class="wrap">
    <?php if(isset($message) && !empty($message)){ ?>
        <div class="notice notice-error is-dismissible"><p><?= $message; ?></p></div>
    <?php } ?>
    <?php if(isset($_COOKIE['message_success']) && !empty($_COOKIE['message_success'])): ?>
      
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message_success']; ?></p></div>
        <?php setcookie('message_success','',time(),'/') ?>
    <?php endif; ?>
    <?php if(isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <h2 style="margin-bottom:15px;"><?= __('Edit Departments','edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Departments','edusystem'); ?></h2>
    <?php endif; ?>
    <div style="display:flex;width:100%;justify-content:start;margin-top:10px;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_department_content') ?>"><?= __('Back','edusystem'); ?></a>
    </div>
    <?php if(isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
        <a id="delete_department" data-id="<?= $_GET['department_id']; ?>" class="button button-danger"><?= __('Delete','edusystem'); ?></a>
        </div>   
    <?php endif; ?>
    <form method="post" action="<?= admin_url('admin.php?page=add_admin_department_content&action=add_department'); ?>">
       <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Name','edusystem'); ?></label></th>
                    <td>
                        <input name="name" style="width:100%" type="text" value="<?= (isset($name) && !empty($name)) ? $name : ''; ?>" class="regular-text" required <?= (isset($name) && !empty($name)) ? 'readonly' : ''; ?>>
                        <input type="hidden" name="department_id" value="<?= (isset($department_id) && !empty($department_id) ? $department_id : ''); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Description','edusystem'); ?></label></th>
                    <td>
                        <textarea name="description" row="4" style="width:100%;resize:none;" type="text" class="regular-text" required><?= (isset($description) && !empty($description)) ? $description : ''; ?></textarea>
                    </td>
                </tr>
            </tbody>
       </table>
       <h3 style="margin-bottom:15px;"><?= __('Modules','edusystem'); ?></h3>
       <table class="form-table">
            <tbody>
                <!-- ADMISSION -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_admission_aes" id="manager_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_admission_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_admission_aes"><?= __('Admission','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_documents_aes" id="manager_documents_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_documents_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_documents_aes" style="margin-left:20px;"><?= __('Required Documents','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="only_read_admission_aes" id="only_read_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['only_read_admission_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="only_read_admission_aes"><?= __('Read Admission','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- ADMISSION -->

                <!-- CERTIFICATIONS -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" id="manager_certificates" value="manager_certificates" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_certificates'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_certificates"><?= __('Certificates','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_documents_certificates" value="manager_documents_certificates" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_documents_certificates'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_documents_certificates" style="margin-left:20px;"><?= __('Documents of certificates','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_users_signatures_certificate" value="manager_users_signatures_certificate" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_users_signatures_certificate'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_users_signatures_certificate" style="margin-left:20px;"><?= __('Users and signatures','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_configuration_certificates" value="manager_id_card" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_id_card'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_id_card" style="margin-left:20px;"><?= __('ID Card of students','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_configuration_certificates" value="manager_configuration_certificates" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_configuration_certificates'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_configuration_certificates" style="margin-left:20px;"><?= __('Configuration of certificates','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- CERTIFICATIONS -->
            
                <!-- ACADEMIC -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_academic_aes" id="manager_academic_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_academic_aes"><?= __('Academic','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_academic_projection_aes" id="manager_academic_projection_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_projection_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_academic_projection_aes" style="margin-left:20px;"><?= __('Academic projection','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_school_subjects_aes" id="manager_school_subjects_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_school_subjects_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_school_subjects_aes" style="margin-left:20px;"><?= __('School subjects','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_academic_periods_aes" id="manager_academic_periods_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_periods_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_academic_periods_aes" style="margin-left:20px;"><?= __('Academic periods','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_scholarship_aes" id="manager_scholarship_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_scholarship_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_scholarship_aes" style="margin-left:20px;"><?= __('Scholarship application','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_availables_scholarship_aes" id="manager_availables_scholarship_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_availables_scholarship_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_availables_scholarship_aes" style="margin-left:20px;"><?= __('Availables scholarships','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_enrollments_aes" id="manager_enrollments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_enrollments_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_enrollments_aes" style="margin-left:20px;"><?= __('Enrollments','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_academic_offers_aes" id="manager_academic_offers_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_offers_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_academic_offers_aes" style="margin-left:20px;"><?= __('Academic offers','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_requests_aes" id="manager_requests_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_requests_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_requests_aes" style="margin-left:20px;"><?= __('Requests','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_graduations_aes" id="manager_graduations_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_graduations_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_graduations_aes" style="margin-left:20px;"><?= __('Student graduations','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_pensums" id="manager_pensums" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_pensums'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_pensums" style="margin-left:20px;"><?= __('Pensum','edusystem'); ?></label></td>
                    </tr>
                    
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_feed" id="manager_feed" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_feed'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_feed" style="margin-left:20px;"><?= __('Feed','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- ACADEMIC -->

                <!-- PAYMENTS -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_payments_aes" id="manager_payments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_payments_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_payments_aes"><?= __('Payments','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- PAYMENTS -->

                <!-- STAFF -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_staff_menu_aes" id="manager_staff_menu_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_staff_menu_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_staff_menu_aes"><?= __('Staff','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_staff_aes" id="manager_staff_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_staff_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_staff_aes" style="margin-left:20px;"><?= __('Manage staff','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_institutes_aes" id="manager_institutes_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_institutes_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_institutes_aes" style="margin-left:20px;"><?= __('Institutes','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_alliances_aes" id="manager_alliances_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_alliances_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_alliances_aes" style="margin-left:20px;"><?= __('Alliances','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_teachers_aes" id="manager_teachers_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_teachers_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_teachers_aes" style="margin-left:20px;"><?= __('Teachers','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- STAFF -->

                <!-- COMMUNICATIONS -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" id="manager_communications_aes" value="manager_communications_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_communications_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_communications_aes"><?= __('Communications','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_send_email_aes" value="manager_send_email_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_send_email_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_send_email_aes" style="margin-left:20px;"><?= __('Email to students','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_send_notification_aes" value="manager_send_notification_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_send_notification_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_send_notification_aes" style="margin-left:20px;"><?= __('Email to staff','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- COMMUNICATIONS -->
                
                <!-- REPORT -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_report_aes" id="manager_report_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_report_aes"><?= __('Report','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_sales_aes" id="manager_sales_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_sales_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_sales_aes" style="margin-left:20px;"><?= __('Sales','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_accounts_receivables_aes" id="manager_accounts_receivables_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_accounts_receivables_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_accounts_receivables_aes" style="margin-left:20px;"><?= __('Accounts receivable','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_report_students_aes" id="manager_report_students_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_students_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_report_students_aes" style="margin-left:20px;"><?= __('Report students','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_report_current_students_aes" id="manager_report_current_students_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_current_students_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_report_current_students_aes" style="margin-left:20px;"><?= __('Report current students enrolled','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_report_sales_product" id="manager_report_sales_product" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_sales_product'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_report_sales_product" style="margin-left:20px;"><?= __('Report sales by product','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- REPORT -->

                <!-- CONFIGURATIONS -->
                <div>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" name="capabilities[]" value="manager_settings_aes" id="manager_settings_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_settings_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_settings_aes"><?= __('Settings menu','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_departments_aes" id="manager_departments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_departments_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_departments_aes" style="margin-left:20px;"><?= __('Departments','edusystem'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width:10px;padding:3px;">
                            <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_configuration_options_aes" id="manager_configuration_options_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_configuration_options_aes'])) ? 'checked' : ''; ?>>
                        </th>
                        <td style="display:table-cell;padding:3px;"><label for="manager_configuration_options_aes" style="margin-left:20px;"><?= __('Configurations of the site','edusystem'); ?></label></td>
                    </tr>
                </div>
                <!-- CONFIGURATIONS -->

                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_users_aes" value="manager_users_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_users_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_users_aes"><?= __('Users','edusystem'); ?></label></td>
                </tr>

            </tbody>
       </table>
       <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
            <button class="button button-primary"><?= __('Save Changes','edusystem'); ?></button>
       </div>
    </form>

</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-delete-department.php');
?>

