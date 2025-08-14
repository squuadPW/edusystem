<?php

function wp_api()
{
    register_rest_route('api', '/adjust-projection-student', array(
        'methods' => 'POST',
        'callback' => 'adjust_projection_student',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'wp_api');

function adjust_projection_student(WP_REST_Request $request)
{
    global $current_user, $wpdb;

    $body = $request->get_body();

    // Decodificar el JSON
    $data = json_decode($body, true);

    $subject_id = $data['subject_id'];
    $student_id = $data['student_id'];
    $cut = $data['cut'];
    $code_period = $data['code_period'];
    $calification = (float) $data['calification'];

    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$subject_id}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student_id}");
    $projection_obj = json_decode($projection->projection);

    $status_id = ($calification >= $subject->min_pass ? 3 : 4);
    $exists = false;
    foreach ($projection_obj as $item) {
        if ($item->subject_id === $subject->id) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        array_push($projection_obj, [
            'code_subject' => $subject->code_subject,
            'subject_id' => $subject->id,
            'subject' => $subject->name,
            'hc' => $subject->hc,
            'cut' => $status_id == 4 ? '' : $cut,
            'code_period' => $status_id == 4 ? '' : $code_period,
            'calification' => $status_id == 4 ? '' : $calification,
            'is_completed' => $status_id == 4 ? false : true,
            'this_cut' => false
        ]);
    }

    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj),
    ], ['id' => $projection->id]);

    $wpdb->insert($table_student_period_inscriptions, [
        'status_id' => $status_id,
        'student_id' => $projection->student_id,
        'code_subject' => $subject->code_subject,
        'code_period' => $code_period,
        'cut_period' => $cut,
        'calification' => $calification,
    ]);

    update_max_upload_at($projection->student_id);
    wp_send_json(array('success' => true));
    exit;
}
