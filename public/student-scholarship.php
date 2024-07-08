<?php

function save_scholarship(){
    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'save_scholarship'){
            
            $name = strtolower($_POST['name_student']);
            $middle_name_student = strtolower($_POST['middle_name_student']);
            $last_name = strtolower($_POST['lastname_student']);
            $middle_last_name_student = strtolower($_POST['middle_last_name_student']);
            $number_phone = $_POST['number_phone_hidden'];
            $number_partner = $_POST['number_partner_hidden']; 
            $email_student = strtolower($_POST['email_student']);
            $email_partner = strtolower($_POST['email_partner']);
            $country = $_POST['country'];
            $city = strtolower($_POST['city']);
            $birth_date = $_POST['birth_date_student'];
            $agent_name = strtolower($_POST['agent_name']);
            $agent_last_name = strtolower($_POST['agent_last_name']);
            $program = $_POST['program'];
            $grade = $_POST['grade'];
            $name_institute = strtolower($_POST['name_institute']);
            $institute_id = $_POST['institute_id'];

            global $wpdb;
            $table_pre_users = $wpdb->prefix.'pre_users';
            $wpdb->insert($table_pre_users,[
                'name' => $agent_name,
                'middle_name' => null,
                'last_name' => $agent_last_name,
                'middle_last_name' => null,
                'birth_date' => null,
                'partner_id' => null, 
                'phone' => $number_partner,
                'email' => $email_partner,
                'type' => 'partner',
            ]);

            $partner_id = $wpdb->insert_id; 

            $wpdb->insert($table_pre_users,[
                'name' => $name,
                'middle_name' => $middle_name_student,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name_student,
                'birth_date' => date_i18n('Y-m-d',strtotime($birth_date)),
                'partner_id' => $partner_id, 
                'phone' => $number_phone,
                'email' => $email_student,
                'type' => 'student',
            ]);

            $table_pre_students = $wpdb->prefix.'pre_students';
            $wpdb->insert($table_pre_students,[
                'name' => $name,
                'middle_name' => $middle_name_student,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name_student,
                'birth_date' => date_i18n('Y-m-d',strtotime($birth_date)),
                'grade_id' => $grade,
                'name_institute' => $name_institute,
                'institute_id' => $institute_id,
                'program_id' => $program,
                'partner_id' => $partner_id, 
                'phone' => $number_phone,
                'email' => $email_student,
                'status_id' => 0,
                'country' => $country,
                'city' => $city,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $student_id = $wpdb->insert_id;

            $table_student_scholarship_application = $wpdb->prefix.'student_scholarship_application';
            $wpdb->insert($table_student_scholarship_application,[
                'student_id' => $student_id,
                'partner_id' => $partner_id,
                'from_date' => null,
                'until_date' => null,
                'description' => null
            ]);

            echo '<script>alert("Process completed successfully. awaiting review.");</script>';
            echo '<script>window.location.href = document.referrer;</script>';
        };

    }
}

add_action('wp_loaded','save_scholarship');