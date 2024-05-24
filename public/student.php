<?php

add_action('woocommerce_account_student_endpoint', function(){

    $student = get_student(get_current_user_id());
    include(plugin_dir_path(__FILE__).'templates/student.php');
});

function get_student($partner_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id={$partner_id}");
    return $data;
}  

function insert_student($customer_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $wpdb->insert($table_students,[
        'name' => $_COOKIE['name_student'],
        'last_name' => $_COOKIE['last_name_student'],
        'birth_date' => wp_date('Y-m-d',strtotime($_COOKIE['birth_date'])),
        'grade_id' => $_COOKIE['initial_grade'],
        'name_institute' => $_COOKIE['name_institute'],
        'program_id' => $_COOKIE['program_id'],
        'partner_id' => $customer_id, 
        'phone' => $_COOKIE['billing_phone'],
        'email' => $_COOKIE['billing_email'],
        'status_id' => 0,
        'created' => date('Y-m-d H:i:s'),
    ]);

    $student_id = $wpdb->insert_id;

    return $student_id;
}

function update_status_student($student_id,$status_id){
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $wpdb->update($table_students,['status_id' => $status_id],['id' => $student_id]);
}

function insert_register_documents($student_id){

    global $wpdb;
    $table_student_documents = $wpdb->prefix.'student_documents';

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'certified_notes_high_school',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'high_school_diploma',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'id_parents',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    
    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'id_student',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'photo_student_card',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'proof_of_grades',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'proof_of_study',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $wpdb->insert($table_student_documents,[
        'student_id' => $student_id,
        'document_id' => 'vaccunation_card',
        'status' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

function get_documents($student_id){

    global $wpdb;
    $table_student_documents = $wpdb->prefix.'student_documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
    return $documents;
}

function delete_user(){

    
}