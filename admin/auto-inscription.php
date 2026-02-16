<?php

function add_admin_form_auto_inscription_content()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

    // 1. Fetch all periods for the first selector
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");

    // 2. Determine initial Code/Cut (from GET or default)
    if (isset($_GET['academic_period']) && isset($_GET['academic_period_cut'])) {
        $code = sanitize_text_field($_GET['academic_period']);
        $cut  = sanitize_text_field($_GET['academic_period_cut']);
    } else {
        $load = load_current_cut_enrollment();
        $code = $load['code'];
        $cut  = $load['cut'];
    }

    // 3. Fetch cuts for the selected (or default) period
    $periods_cuts = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$table_academic_periods_cut} WHERE code = %s ORDER BY created_at ASC", $code)
    );

    $raw_expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE academic_period = %s AND academic_period_cut = %s AND status = %s ORDER BY term_index ASC, term_position ASC",
        $code,
        $cut,
        'pendiente'
    ));

    $grouped_expected_rows = [];
    $student_details_cache = [];
    $subject_details_cache = [];
    $unique_student_ids = [];

    foreach ($raw_expected_rows as $row) {
        $student_id = $row->student_id;
        $subject_id = $row->subject_id;
        
        $unique_student_ids[$student_id] = true;

        if (!isset($student_details_cache[$student_id])) {
            $student_detail = get_student_detail($student_id);
            $student_name = student_names_lastnames_helper($student_id); 
            $initials = mb_strtoupper(substr($student_detail->last_name, 0, 1) . substr($student_detail->name, 0, 1));
            
            $student_details_cache[$student_id] = [
                'student_id' => $student_id,
                'student_name' => $student_name,
                'initials' => $initials,
                'status' => esc_html__('Waiting', 'edusystem'),
            ];
        }

        if (!isset($subject_details_cache[$subject_id])) {
            $subject_detail = get_subject_details($subject_id);
            $subject_details_cache[$subject_id] = $subject_detail->name;
        }

        $subject_name = $subject_details_cache[$subject_id];

        if (!isset($grouped_expected_rows[$subject_name])) {
            $grouped_expected_rows[$subject_name] = [
                'subject_name' => $subject_name,
                'students' => [],
            ];
        }

        $grouped_expected_rows[$subject_name]['students'][] = (object)$student_details_cache[$student_id];
    }

    $total_unique_students = count($unique_student_ids);

    $expected_rows = array_values($grouped_expected_rows);

    include(plugin_dir_path(__FILE__) . 'templates/auto-inscription-detail.php');
}

function auto_enroll_students_bulk_callback()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    
    if (isset($_POST['academic_period']) && !empty($_POST['academic_period']) && 
        isset($_POST['academic_period_cut']) && !empty($_POST['academic_period_cut'])) {
        $code = sanitize_text_field($_POST['academic_period']);
        $cut = sanitize_text_field($_POST['academic_period_cut']);
    } else {
        $load = load_current_cut_enrollment();
        $code = $load['code'];
        $cut = $load['cut'];
    }

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
