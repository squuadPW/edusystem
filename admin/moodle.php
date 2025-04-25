<?php

function show_moodle_setting(){

    if(isset($_GET['action']) && !empty($_GET['action'])){
        if($_GET['action'] == 'save_setting'){

            if(isset($_POST['moodle_url']) && !empty($_POST['moodle_url'])){
                update_option('moodle_url',$_POST['moodle_url']);
            }else{
                update_option('moodle_url','');
            }

            if(isset($_POST['moodle_token']) && !empty($_POST['moodle_token'])){
                update_option('moodle_token',$_POST['moodle_token']);
            }else{
                update_option('moodle_token','');
            }
        }

        wp_redirect(admin_url('admin.php?page=moodle-setting'));
        exit;

    }else{
        include(plugin_dir_path(__FILE__).'templates/moodle-setting.php');
    }
}

function create_user_moodle($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    $address = get_user_meta($data_student->partner_id, 'billing_address_1', true);

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if(!empty($moodle_url) && !empty($moodle_token)){

        $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);
        $password = wp_generate_password(12);
        
        $users = ['users' => [
            [
                'username' => strtolower((string)$data_student->id_document),
                'firstname' => $data_student->name,
                'lastname' => $data_student->last_name,
                'email' => $data_student->email,
                'password' => $password,
                'city' => $data_student->city,
                'country' => $data_student->country,
                'middlename' => $data_student->middle_name,
                'idnumber' => $data_student->id_document,
                'phone2' => $data_student->phone,
                'institution' => $data_student->name_institute,
                'address' => $address,
                'lang' => 'en'
            ]
        ]];

        $create_user = $MoodleRest->request('core_user_create_users',$users,MoodleRest::METHOD_POST);
        $wpdb->update($table_students,[
            'moodle_student_id' => $create_user[0]['id'],
            'moodle_password' => $password
        ],['id' => $student_id]);

        generate_projection_student($student_id);

        if (get_option('auto_enroll_regular')) {
            automatically_enrollment($student_id);
        }

        return $create_user;
    }

    return;
}

/**
 * status: 
 * 1) nologin - can not access
 * 2) manual - can access
 *  
*/ 

function change_status_student($student_id,$status = 'manual'){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    if(!empty($moodle_url) && !empty($moodle_token)){

        if(!$data_student){

            $moodle_student_id = $data_student->moodle_student_id;
    
            if(!empty($moodle_student_id)){
                
                $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);

                $users = ['users' => [
                        [
                            'id' => $moodle_student_id,
                            'auth' => $status,
                        ]
                    ]
                ];

                $update_user = $MoodleRest->request('core_user_update_users',$users,MoodleRest::METHOD_POST);
                return $update_user;
            }
    
        }
    }

    return;
}

function is_search_student_by_email($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if(!empty($data_student)){

        if(!empty($moodle_url) && !empty($moodle_token)){

            $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);

            $search = [
                'field' => 'email',
                'values' => [
                    $data_student->email
                ]
            ];

            $search_user = $MoodleRest->request('core_user_get_users_by_field',$search);

            if(empty($search_user)){
                return [];
            }else{
                return $search_user;
            }
        }
    }
}

function get_courses_moodle(){
    $courses = [];
    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if(!empty($moodle_url) && !empty($moodle_token)){
        $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);
        $courses = $MoodleRest->request('core_course_get_courses');

        return $courses;
    }
}

function change_password_user_moodle($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if(!empty($moodle_url) && !empty($moodle_token)){

        if($data_student){

            $moodle_student_id = $data_student->moodle_student_id;

            if(!empty($moodle_student_id)){
                
                $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);

                $users = ['users' => [
                        [
                            'id' => $moodle_student_id,
                            'password' => $data_student->moodle_password,
                        ]
                    ]
                ];

                $update_user = $MoodleRest->request('core_user_update_users',$users,MoodleRest::METHOD_POST);
                return $update_user;
            }
        }
    }
}

function get_url_login($email){

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if(!empty($moodle_url) && !empty($moodle_token)){

        $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php',$moodle_token);

        $data = ['user' => ['email' => $email]];

        $url = $MoodleRest->request('auth_userkey_request_login_url',$data,MoodleRest::METHOD_POST);
        return $url['loginurl'];
    }
}

function courses_enroll_student($student_id, $courses = []) {
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    $enrollments = [];

    if (!empty($data_student) && $data_student->moodle_student_id) {
        foreach ($courses as $key => $course_id) {    
            array_push($enrollments, [
                'userid' => $data_student->moodle_student_id,
                'courseid' => $course_id,
                'roleid' => ROLE_ID_STUDENT_MOODLE,
            ]);
        }
    }

    return $enrollments;
}

function enroll_student($enrollments = [], $errors_count = '') {
    global $wpdb;

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);
    
    // Dividir el array en chunks de 50 elementos
    $chunks = array_chunk($enrollments, 50);
    $all_responses = [];

    foreach ($chunks as $chunk) {
        $response = $MoodleRest->request('enrol_manual_enrol_users', ['enrolments' => $chunk]);
        if (!empty($response)) {
            $all_responses = array_merge($all_responses, $response);
        }
    }

    update_count_moodle_pending($errors_count);
    if (empty($all_responses)) {
        return [];
    } else {
        return $all_responses;
    }
}

function courses_unenroll_student($student_id, $course_id) {
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    $enrollments = [];

    if (!empty($data_student) && $data_student->moodle_student_id) {
        array_push($enrollments, [
            'userid' => $data_student->moodle_student_id,
            'courseid' => $course_id
        ]);
    }

    return $enrollments;
}

function unenroll_student($enrollments = []) {
    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);
    $unenrolled_courses = $MoodleRest->request('enrol_manual_unenrol_users', ['enrolments' => $enrollments]);
    if (empty($unenrolled_courses)) {
        return [];
    } else {
        return $unenrolled_courses;
    }
}