<?php

function delete_data_student($user_id){

    global $wpdb;
    $table_student_documents = $wpdb->prefix.'student_documents';
    $table_students = $wpdb->prefix.'students';

    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id={$user_id}");

    if($students){

        foreach($students as $student){

            $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id}");

            foreach($documents as $document){

                $wpdb->delete($table_student_documents,['id' => $document->id]);
            }

            $wpdb->delete($table_students,['id' => $student->id]);
        }
    }
}

add_action('delete_user','delete_data_student');