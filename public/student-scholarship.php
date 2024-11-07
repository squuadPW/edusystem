<?php

function save_scholarship(){
    // if(
    //     isset($_GET['action']) && !empty($_GET['action'])
    // ){
    //     if($_GET['action'] == 'save_scholarship'){
            global $wpdb;
            $table_pre_users = $wpdb->prefix.'pre_users';

            // // Datos del estudiante
            // $birth_date = isset($_POST['birth_date_student']) ? $_POST['birth_date_student'] : null;
            // $document_type = isset($_POST['document_type']) ? $_POST['document_type'] : null;
            // $id_document = isset($_POST['id_document']) ? $_POST['id_document'] : null;
            // $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
            // $name = isset($_POST['name_student']) ? strtolower($_POST['name_student']) : null;
            // $middle_name_student = isset($_POST['middle_name_student']) ? strtolower($_POST['middle_name_student']) : null;
            // $last_name = isset($_POST['lastname_student']) ? strtolower($_POST['lastname_student']) : null;
            // $middle_last_name_student = isset($_POST['middle_last_name_student']) ? strtolower($_POST['middle_last_name_student']) : null;
            // $number_phone = isset($_POST['number_phone']) ? $_POST['number_phone'] : (isset($_POST['number_phone_hidden']) ? $_POST['number_phone_hidden'] : null);
            // $email_student = isset($_POST['email_student']) ? strtolower($_POST['email_student']) : null;
            // $ethnicity = isset($_POST['etnia']) ? $_POST['etnia'] : null;

            // // Datos del padre
            // $birth_date_parent = isset($_POST['birth_date_parent']) ? $_POST['birth_date_parent'] : null;
            // $parent_document_type = isset($_POST['parent_document_type']) ? $_POST['parent_document_type'] : null;
            // $id_document_parent = isset($_POST['id_document_parent']) ? $_POST['id_document_parent'] : null;
            // $gender_parent = isset($_POST['gender_parent']) ? $_POST['gender_parent'] : null;
            // $agent_name = isset($_POST['agent_name']) ? strtolower($_POST['agent_name']) : null;
            // $agent_last_name = isset($_POST['agent_last_name']) ? strtolower($_POST['agent_last_name']) : null;
            // $number_partner = isset($_POST['number_partner_hidden']) ? $_POST['number_partner_hidden'] : null;
            // $email_partner = isset($_POST['email_partner']) ? strtolower($_POST['email_partner']) : null;

            // // DATOS EXTRAS
            // $country = isset($_POST['country']) ? $_POST['country'] : null;
            // $city = isset($_POST['city']) ? strtolower($_POST['city']) : null;
            // $program = isset($_POST['program']) ? $_POST['program'] : null;
            // $grade = isset($_POST['grade']) ? $_POST['grade'] : null;
            // $institute_id = isset($_POST['institute_id']) ? $_POST['institute_id'] : null;
            // $password = isset($_POST['password']) ? $_POST['password'] : null;

            // Datos del estudiante
            $birth_date = isset($_COOKIE['birth_date']) ? $_COOKIE['birth_date'] : null;
            $document_type = isset($_COOKIE['document_type']) ? $_COOKIE['document_type'] : null;
            $id_document = isset($_COOKIE['id_document']) ? $_COOKIE['id_document'] : null;
            $gender = isset($_COOKIE['gender']) ? $_COOKIE['gender'] : null;
            $name = isset($_COOKIE['name_student']) ? strtolower($_COOKIE['name_student']) : null;
            $middle_name_student = isset($_COOKIE['middle_name_student']) ? strtolower($_COOKIE['middle_name_student']) : null;
            $last_name = isset($_COOKIE['last_name_student']) ? strtolower($_COOKIE['last_name_student']) : null;
            $middle_last_name_student = isset($_COOKIE['middle_last_name_student']) ? strtolower($_COOKIE['middle_last_name_student']) : null;
            $number_phone = isset($_COOKIE['phone_student']) ? $_COOKIE['phone_student'] : (isset($_COOKIE['number_phone_hidden']) ? $_COOKIE['number_phone_hidden'] : null);
            $email_student = isset($_COOKIE['email_student']) ? strtolower($_COOKIE['email_student']) : null;
            $ethnicity = isset($_COOKIE['ethnicity']) ? $_COOKIE['ethnicity'] : null;

            // Datos del padre
            $birth_date_parent = isset($_COOKIE['birth_date_parent']) ? $_COOKIE['birth_date_parent'] : null;
            $parent_document_type = isset($_COOKIE['parent_document_type']) ? $_COOKIE['parent_document_type'] : null;
            $id_document_parent = isset($_COOKIE['id_document_parent']) ? $_COOKIE['id_document_parent'] : null;
            $gender_parent = isset($_COOKIE['gender_parent']) ? $_COOKIE['gender_parent'] : null;
            $agent_name = isset($_COOKIE['agent_name']) ? strtolower($_COOKIE['agent_name']) : null;
            $agent_last_name = isset($_COOKIE['agent_last_name']) ? strtolower($_COOKIE['agent_last_name']) : null;
            $number_partner = isset($_COOKIE['number_partner']) ? $_COOKIE['number_partner'] : null;
            $email_partner = isset($_COOKIE['email_partner']) ? strtolower($_COOKIE['email_partner']) : null;

            // DATOS EXTRAS
            $country = isset($_COOKIE['billing_country']) ? $_COOKIE['billing_country'] : null;
            $city = isset($_COOKIE['billing_city']) ? strtolower($_COOKIE['billing_city']) : null;
            $program = isset($_COOKIE['program_id']) ? $_COOKIE['program_id'] : null;
            $grade = isset($_COOKIE['initial_grade']) ? $_COOKIE['initial_grade'] : null;
            $institute_id = isset($_COOKIE['institute_id']) ? $_COOKIE['institute_id'] : null;
            $password = isset($_COOKIE['password']) ? $_COOKIE['password'] : null;

            if (!empty($institute_id) && $institute_id != 'other') {
                $institute = get_institute_details($institute_id);
                $name_institute = strtolower($institute->name);
                setcookie('institute_id', $institute_id, time() + 3600);
            } else {
                $name_institute = isset($_COOKIE['name_institute']) ? strtolower($_COOKIE['name_institute']) : null;
            }

            $partner_id = null;
            $is_parent = false;
            if (!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner) && !empty($birth_date_parent) && !empty($parent_document_type) && !empty($id_document_parent)) {
                $wpdb->insert($table_pre_users,[
                    'type_document' => $parent_document_type,
                    'id_document' => $id_document_parent,
                    'name' => $agent_name,
                    'middle_name' => null,
                    'last_name' => $agent_last_name,
                    'middle_last_name' => null,
                    'birth_date' => date_i18n('Y-m-d',strtotime($birth_date_parent)),
                    'gender' => $gender_parent,
                    'ethnicity' => null, 
                    'partner_id' => null, 
                    'phone' => $number_partner,
                    'email' => $email_partner,
                    'is_parent' => true,
                    'password' => $password,
                    'type' => 'partner',
                ]);
                $partner_id = $wpdb->insert_id; 
            }

            if (!$partner_id) {
                $is_parent = true;
            }

            $wpdb->insert($table_pre_users,[
                'type_document' => $document_type,
                'id_document' => $id_document,
                'name' => $name,
                'middle_name' => $middle_name_student,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name_student,
                'birth_date' => date_i18n('Y-m-d',strtotime($birth_date)),
                'gender' => $gender,
                'ethnicity' => $ethnicity,
                'partner_id' => $partner_id, 
                'phone' => $number_phone,
                'email' => $email_student,
                'is_parent' => $is_parent,
                'password' => $is_parent ? $password : null, 
                'type' => 'student',
            ]);

            if (!$partner_id) {
                $partner_id = $wpdb->insert_id;
            }

            $table_pre_students = $wpdb->prefix.'pre_students';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $current_time = current_time('mysql');
            $code = 'noperiod';
            $period_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_academic_periods} WHERE `start_date` <= %s AND end_date >= %s", array($current_time, $current_time)));
            if ($period_data) {
                $code = $period_data->code;
            }
            $wpdb->insert($table_pre_students,[
                'type_document' => $document_type,
                'id_document' => $id_document,
                'ethnicity' => $ethnicity,
                'academic_period' => $code,
                'name' => $name,
                'middle_name' => $middle_name_student,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name_student,
                'birth_date' => date_i18n('Y-m-d',strtotime($birth_date)),
                'phone' => $number_phone,
                'email' => $email_student,
                'gender' => $gender,
                'country' => $country,
                'city' => $city,
                'grade_id' => $grade,
                'name_institute' => $name_institute,
                'institute_id' => $institute_id,
                'program_id' => $program,
                'partner_id' => $partner_id, 
                'status_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $student_id = $wpdb->insert_id;

            $table_student_scholarship_application = $wpdb->prefix.'student_scholarship_application';
            $wpdb->insert($table_student_scholarship_application,[
                'student_id' => $student_id,
                'partner_id' => $partner_id,
                'status_id' => 1,
                'from_date' => null,
                'until_date' => null,
                'description' => null
            ]);
    //     };

    // }
}

// add_action('wp_loaded','save_scholarship');