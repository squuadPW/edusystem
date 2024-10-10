<?php

global $wpdb, $current_user;
$roles = $current_user->roles;
$table_students = $wpdb->prefix . 'students';
$student_logged = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");

$students = [];
if (in_array('student', $roles)) {
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
} else if (in_array('parent', $roles)) {
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");
}
?>

<?php if (!$student_logged->moodle_password) { ?>
    <div class="text-center info-box">
        <?php if (in_array('student', $roles)): ?>
            <h2 style="font-size:24px;text-align:center;"><?= __('Student Information', 'form-plugin'); ?></h2>
        <?php elseif (in_array('parent', $roles)): ?>
            <h2 style="font-size:24px;text-align:center;"><?= __('Students Information', 'form-plugin'); ?></h2>
        <?php endif; ?>

        <p><?= __('To access the virtual classroom, please ensure you complete the following steps:', 'form-plugin'); ?></p>
        <ul class="info-list">
            <li>
                <i class="fas fa-upload"></i>
                <?= __('Upload all required documents marked with an asterisk (*)', 'form-plugin'); ?> <a
                    style="text-decoration: underline !important; color: #091c5c;"
                    href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents' ?>"><?= __('here', 'form-plugin'); ?></a>
            </li>
            <li>
                <i class="fas fa-credit-card"></i>
                <?= __('If you haven\'t already, please process the payment for your registration fee. This will enable us to finalize your registration and grant you access to the virtual classroom.', 'form-plugin'); ?>
            </li>
        </ul>
        <p class="info-note">
            <?= __('Once both steps are complete, you will receive an email with instructions on how to access the virtual classroom. Please note that access will only be granted once all required documents have been received and your payment has been processed.', 'form-plugin'); ?>
        </p>
    </div>
<?php } else { ?>
    <?php if (in_array('student', $roles)): ?>
        <h2 style="font-size:24px;text-align:center;"><?= __('Student Information', 'form-plugin'); ?></h2>
    <?php elseif (in_array('parent', $roles)): ?>
        <h2 style="font-size:24px;text-align:center;"><?= __('Students Information', 'form-plugin'); ?></h2>
    <?php endif; ?>
<?php } ?>
<?php if(count($students) == 0) { ?>
    <div style="text-align: end; margin: 10px;">
        <span><button type="button" class="submit" style="width: 150px !important" id="add_new_student">Add new</button></span>
    </div>
<?php } ?>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
    style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                    class="nobr"><?= __('Full Name', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Grade', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Program(s)', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Email', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Actions', 'aes'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($student)): ?>
            <?php foreach ($student as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                        data-title="<?= __('Full Name', 'aes'); ?>">
                        <?= $row->name . ' ' . $row->last_name; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                        data-title="<?= __('Grade', 'aes'); ?>">
                        <?= $grade = get_name_grade($row->grade_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Program', 'aes') ?>">
                        <?= $program = get_name_program($row->program_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Email', 'aes') ?>">
                        <?= $row->email; ?>
                    </td>
                    <!--
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Program', 'aes') ?>">
                        <?php if (!empty($row->moodle_password)): ?>
                        <form class="woocommerce-form woocommerce-form-login login mt-4">
                            <input class="woocommerce-Input woocommerce-Input--text input-text input-no-style" type="password" name="password" id="password" value="<?= $row->moodle_password; ?>">
                        </form>
                        <?php endif; ?>
                    </td>
                    -->
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('View', 'aes'); ?>">
                        <a href="<?= wc_get_account_endpoint_url('student-details') . '/?student=' . $row->id; ?>"
                            class="button button-primary"><?= __('View', 'aes'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if(count($students) == 0) { 
include('fill-info-student.php'); 
} ?>

<script>
    document.body.classList.remove("modal-open");
    let add_new_student = document.getElementById('add_new_student');
    if (add_new_student) {
        add_new_student.addEventListener('click', function() {
            document.getElementById('modal-content-fill-student').style.display = 'block';
            document.getElementById('modal-fill').style.display = 'block';
            document.body.classList.add("modal-open");
        })
    }
</script>