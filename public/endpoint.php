<?php

function wp_api()
{
    register_rest_route('api', '/assign-documents-students', array(
        'methods' => 'GET',
        'callback' => 'assign_documents_students',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('api', '/assign-academic-period-cut-student', array(
        'methods' => 'GET',
        'callback' => 'assign_academic_period_cut_student',
        'permission_callback' => '__return_true'
    ));

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
    $calification = $data['calification'];

    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$subject_id}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student_id}");
    $projection_obj = json_decode($projection->projection);

    array_push($projection_obj, [
        'code_subject' => $subject->code_subject,
        'subject_id' => $subject->id,
        'subject' => $subject->name,
        'hc' => $subject->hc,
        'cut' => $cut,
        'code_period' => $code_period,
        'calification' => $calification,
        'is_completed' => true,
        'this_cut' => false
    ]);

    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj),
    ], ['id' => $projection->id]);

    $wpdb->insert($table_student_period_inscriptions, [
        'status_id' => 3,
        'student_id' => $projection->student_id,
        'code_subject' => $projection_obj[count($projection_obj) - 1]['code_subject'],
        'code_period' => $projection_obj[count($projection_obj) - 1]['code_period'],
        'cut_period' => $projection_obj[count($projection_obj) - 1]['cut'],
    ]);

    wp_send_json(array('success' => true));
    exit;
}


function assign_academic_period_cut_student()
{
    // global $wpdb;
    // $table_students = $wpdb->prefix.'students';
    // $students = $wpdb->get_results("SELECT * FROM {$table_students} where academic_period = '20242025'");
    // $users_affected = [];
    // foreach ($students as $key => $student) {
    //     $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    //     $inscription_cut = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id={$student->id} AND code_period = '20242025' AND cut_period = 'A'");

    //     if (empty($inscription_cut)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_period_inscriptions,
    //             array(
    //                 'status_id' => 1,
    //                 'student_id' => $student->id,
    //                 'code_period' => '20242025',
    //                 'cut_period' => 'A',
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }
    // }

    // wp_send_json(array('studens_affected' => $users_affected));
}

function assign_documents_students()
{
    // global $wpdb;
    // $table_students = $wpdb->prefix.'students';
    // $students = $wpdb->get_results("SELECT * FROM {$table_students}");
    // $users_affected = [];
    // foreach ($students as $key => $student) {
    //     $table_student_documents = $wpdb->prefix.'student_documents';
    //     $missing_documents = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'MISSING DOCUMENTS'");

    //     if (empty($missing_documents)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_documents,
    //             array(
    //                 'student_id' => $student->id,
    //                 'document_id' => 'MISSING DOCUMENTS',
    //                 'attachment_id' => 0,
    //                 'approved_by' => NULL,
    //                 'status' => 0,
    //                 'description' => NULL,
    //                 'is_required' => 0,
    //                 'is_visible' => 0,
    //                 'upload_at' => NULL,
    //                 'created_at' => current_time('mysql'), // Add this line
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }

    //     $enrollment_document = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'ENROLLMENT'");

    //     if (empty($enrollment_document)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_documents,
    //             array(
    //                 'student_id' => $student->id,
    //                 'document_id' => 'ENROLLMENT',
    //                 'attachment_id' => 0,
    //                 'approved_by' => NULL,
    //                 'status' => 0,
    //                 'description' => NULL,
    //                 'is_required' => 1,
    //                 'is_visible' => 0,
    //                 'upload_at' => NULL,
    //                 'created_at' => current_time('mysql'), // Add this line
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }
    // }

    // wp_send_json(array('studens_affected' => $users_affected));
}
