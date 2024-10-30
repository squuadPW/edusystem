<div class="wrap">
    <?php if(isset($message) && !empty($message)){ ?>
        <div class="notice notice-error is-dismissible"><p><?= $message; ?></p></div>
    <?php } ?>
    <?php if(isset($_COOKIE['message_success']) && !empty($_COOKIE['message_success'])): ?>
      
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message_success']; ?></p></div>
        <?php setcookie('message_success','',time(),'/') ?>
    <?php endif; ?>
    <?php if(isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <h2 style="margin-bottom:15px;"><?= __('Edit Departments','aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Departments','aes'); ?></h2>
    <?php endif; ?>
    <div style="display:flex;width:100%;justify-content:start;margin-top:10px;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_department_content') ?>"><?= __('Back','aes'); ?></a>
    </div>
    <?php if(isset($_GET['department_id']) && !empty($_GET['department_id'])): ?>
        <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
        <a id="delete_department" data-id="<?= $_GET['department_id']; ?>" class="button button-danger"><?= __('Delete','aes'); ?></a>
        </div>   
    <?php endif; ?>
    <form method="post" action="<?= admin_url('admin.php?page=add_admin_department_content&action=add_department'); ?>">
       <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Name','aes'); ?></label></th>
                    <td>
                        <input name="name" style="width:100%" type="text" value="<?= (isset($name) && !empty($name)) ? $name : ''; ?>" class="regular-text" required <?= (isset($name) && !empty($name)) ? 'readonly' : ''; ?>>
                        <input type="hidden" name="department_id" value="<?= (isset($department_id) && !empty($department_id) ? $department_id : ''); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="input_id"><?= __('Description','aes'); ?></label></th>
                    <td>
                        <textarea name="description" row="4" style="width:100%;resize:none;" type="text" class="regular-text" required><?= (isset($description) && !empty($description)) ? $description : ''; ?></textarea>
                    </td>
                </tr>
            </tbody>
       </table>
       <h3 style="margin-bottom:15px;"><?= __('Modules','aes'); ?></h3>
       <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_admission_aes" id="manager_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_admission_aes'])) ? 'checked' : ''; ?>>

                        
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_admission_aes"><?= __('Admission','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="only_read_admission_aes" id="only_read_admission_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['only_read_admission_aes'])) ? 'checked' : ''; ?>>

                        
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="only_read_admission_aes"><?= __('Read Admission','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_documents_aes" id="manager_documents_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_documents_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_documents_aes" style="margin-left:20px;"><?= __('Required Documents','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_payments_aes" id="manager_payments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_payments_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_payments_aes"><?= __('Payments','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_moodle_aes" id="manager_moodle_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_moodle_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_moodle_aes"><?= __('Moodle','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_institutes_aes" id="manager_institutes_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_institutes_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_institutes_aes"><?= __('Institutes','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_alliances_aes" id="manager_alliances_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_alliances_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_alliances_aes"><?= __('Alliances','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manage_administrator_aes" id="manage_administrator_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manage_administrator_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manage_administrator_aes"><?= __('Administrations','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" id="manager_departments_aes" value="manager_departments_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_departments_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_departments_aes" style="margin-left:20px;"><?= __('Departments','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_scholarship_aes" value="manager_scholarship_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_scholarship_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_scholarship_aes" style="margin-left:20px;"><?= __('Scholarchips','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_academic_periods_aes" value="manager_academic_periods_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_academic_periods_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_academic_periods_aes" style="margin-left:20px;"><?= __('Academic periods','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_school_subjects_aes" value="manager_school_subjects_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_school_subjects_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_school_subjects_aes" style="margin-left:20px;"><?= __('School subjects','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_configuration_options_aes" value="manager_configuration_options_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_configuration_options_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_configuration_options_aes" style="margin-left:20px;"><?= __('Configuration options admission','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_send_email_aes" value="manager_send_email_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_send_email_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_send_email_aes" style="margin-left:20px;"><?= __('Send email','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_staff_aes" value="manager_staff_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_staff_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_staff_aes" style="margin-left:20px;"><?= __('Staff','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" id="manager_users_aes" value="manager_users_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_users_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_users_aes" style="margin-left:20px;"><?= __('Users','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" name="capabilities[]" value="manager_report_aes" id="manager_report_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_report_aes" style="margin-left:20px;"><?= __('Report','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_sales_aes" id="manager_sales_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_sales_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_sales_aes" style="margin-left:20px;"><?= __('Sales','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_accounts_receivables_aes" id="manager_accounts_receivables_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_accounts_receivables_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_accounts_receivables_aes" style="margin-left:20px;"><?= __('Accounts receivable','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_report_students_aes" id="manager_report_students_aes" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_students_aes'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_report_students_aes" style="margin-left:20px;"><?= __('Report students','aes'); ?></label></td>
                </tr>
                <tr>
                    <th scope="row" style="width:10px;padding:3px;">
                        <input type="checkbox" style="margin-left:20px;" name="capabilities[]" value="manager_report_sales_product" id="manager_report_sales_product" <?= (isset($capabilities) && !empty($capabilities) && isset($capabilities['manager_report_sales_product'])) ? 'checked' : ''; ?>>
                    </th>
                    <td style="display:table-cell;padding:3px;"><label for="manager_report_sales_product" style="margin-left:20px;"><?= __('Report sales by product','aes'); ?></label></td>
                </tr>
            </tbody>
       </table>
       <div style="display:flex;width:100%;justify-content:end;margin-top:10px;">
            <button class="button button-primary"><?= __('Save Changes','aes'); ?></button>
       </div>
    </form>

</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-delete-department.php');
?>

