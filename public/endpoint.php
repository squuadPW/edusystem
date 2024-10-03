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
}
add_action('rest_api_init', 'wp_api');

function assign_academic_period_cut_student()
{
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $students = $wpdb->get_results("SELECT * FROM {$table_students} where academic_period = '20242025'");
    $users_affected = [];
    foreach ($students as $key => $student) {
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $inscription_cut = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id={$student->id} AND code_period = '20242025' AND cut_period = 'A'");

        if (empty($inscription_cut)) {
            // Insert new row if no document was found
            $wpdb->insert(
                $table_student_period_inscriptions,
                array(
                    'status_id' => 1,
                    'student_id' => $student->id,
                    'code_period' => '20242025',
                    'cut_period' => 'A',
                )
            );
            if (!in_array($student->id, $users_affected)) {
                array_push($users_affected, $student->id);
            }
        }
    }

    wp_send_json(array('studens_affected' => $users_affected));
}

function assign_documents_students()
{
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $students = $wpdb->get_results("SELECT * FROM {$table_students}");
    $users_affected = [];
    foreach ($students as $key => $student) {
        $table_student_documents = $wpdb->prefix.'student_documents';
        $missing_documents = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'MISSING DOCUMENTS'");

        if (empty($missing_documents)) {
            // Insert new row if no document was found
            $wpdb->insert(
                $table_student_documents,
                array(
                    'student_id' => $student->id,
                    'document_id' => 'MISSING DOCUMENTS',
                    'attachment_id' => 0,
                    'approved_by' => NULL,
                    'status' => 0,
                    'description' => NULL,
                    'is_required' => 0,
                    'is_visible' => 0,
                    'upload_at' => NULL,
                    'created_at' => current_time('mysql'), // Add this line
                )
            );
            if (!in_array($student->id, $users_affected)) {
                array_push($users_affected, $student->id);
            }
        }

        $enrollment_document = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'ENROLLMENT'");

        if (empty($enrollment_document)) {
            // Insert new row if no document was found
            $wpdb->insert(
                $table_student_documents,
                array(
                    'student_id' => $student->id,
                    'document_id' => 'ENROLLMENT',
                    'attachment_id' => 0,
                    'approved_by' => NULL,
                    'status' => 0,
                    'description' => NULL,
                    'is_required' => 1,
                    'is_visible' => 0,
                    'upload_at' => NULL,
                    'created_at' => current_time('mysql'), // Add this line
                )
            );
            if (!in_array($student->id, $users_affected)) {
                array_push($users_affected, $student->id);
            }
        }
    }

    wp_send_json(array('studens_affected' => $users_affected));
}
