<?php

function add_admin_form_auto_inscription_content()
{
    if ($_GET['action'] == 'save_auto_inscription_details') {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $academic_period = $_POST['academic_period'];
        $cut = $_POST['academic_period_cut'];

        $students_enrolled = '';
        $cut_student_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE academic_period = '{$academic_period}' AND initial_cut = '$cut'");
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', $cut_student_ids) . ")");
        foreach ($students as $key => $student) {
            automatically_enrollment($student->id);
            $students_enrolled .= $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name . PHP_EOL;
        }

        setcookie('message', __('Students affected.', 'edusystem') . PHP_EOL . $students_enrolled, time() + 10, '/');
        wp_redirect(admin_url('admin.php?page=add_admin_form_auto_inscription_content'));
        exit;
    }

    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include(plugin_dir_path(__FILE__) . 'templates/auto-inscription-detail.php');
}

function load_auto_enroll_students_callback()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $academic_period = $_POST['academic_period'];
    $cut = $_POST['academic_period_cut'];

    $cut_student_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE academic_period = '{$academic_period}' AND initial_cut = '$cut'");
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', $cut_student_ids) . ")");
    foreach ($students as $key => $student) {
        $student->next_enrollment = next_enrollment($student->id);
    }
    wp_send_json($students);
    die();
}

add_action('wp_ajax_nopriv_load_auto_enroll_students', 'load_auto_enroll_students_callback');
add_action('wp_ajax_load_auto_enroll_students', 'load_auto_enroll_students_callback');