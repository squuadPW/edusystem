<?php

function add_admin_form_auto_inscription_content()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE academic_period = %s AND academic_period_cut = %s AND status = %s ORDER BY term_index ASC, term_position ASC",
        $code,
        $cut,
        'pendiente'
    ));
    foreach ($expected_rows as $key => $row) {
        $expected_rows[$key]->student = get_student_detail($row->student_id);
        $expected_rows[$key]->subject = get_subject_details($row->subject_id);
    }
    include(plugin_dir_path(__FILE__) . 'templates/auto-inscription-detail.php');
}

function auto_enroll_students_bulk_callback()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE academic_period = %s AND academic_period_cut = %s AND status = %s ORDER BY term_index ASC, term_position ASC",
        $code,
        $cut,
        'pendiente'
    ));
    foreach ($expected_rows as $key => $row) {
        automatically_enrollment($row->student_id);
    }
    wp_send_json(true);
    die();
}

add_action('wp_ajax_nopriv_auto_enroll_students_bulk', 'auto_enroll_students_bulk_callback');
add_action('wp_ajax_auto_enroll_students_bulk', 'auto_enroll_students_bulk_callback');