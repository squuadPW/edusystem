<?php
    $student_id = $student->id;
    $email = $student->email;
    $projection = get_projection_by_student($student_id);
?>

<?php if($student_id) { ?>
    <div style="margin-left: 5px; margin-right: 5px;">
        <?php if($projection && current_user_can('manager_academic_projection_aes') && $_GET['page'] != 'add_admin_form_academic_projection_content') { ?>
            <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=') . $projection->id ?>"
                class="button button-outline-primary">
                <?= __('Academic projection', 'edusystem'); ?>
            </a>
        <?php } ?>
        <?php if($_GET['page'] != 'add_admin_form_admission_content') { ?>
        <a href="<?= admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id; ?>"
            class="button button-outline-primary">
            <?= __('Admission', 'edusystem'); ?>
        </a>
        <?php } ?>
        <?php if (current_user_can('manager_payments_aes') && $_GET['page'] != 'add_admin_form_payments_content') { ?>
            <a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=') . $email ?>"
                class="button button-outline-primary">
                <?= __('Payments', 'edusystem'); ?>
            </a>
        <?php } ?>
    </div>
<?php } ?>