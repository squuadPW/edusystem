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
            $type_document = $_POST['document_type'];
            $id_document = $_POST['id_document'];

            setcookie('is_older','',time());

            if(!empty($institute_id) && $institute_id != 'other'){

                $institute = get_institute_details($institute_id);
                $name_institute = strtolower($institute->name);

                setcookie('institute_id',ucwords($institute_id),time() + 3600);
            }

            if(!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner)){

                setcookie('agent_name',ucwords($agent_name),time() + 3600);
                setcookie('agent_last_name',ucwords($agent_last_name),time() + 3600);
                setcookie('email_partner',$email_partner,time() + 3600);
                setcookie('number_partner',$number_partner,time() + 3600);
            }else{

                setcookie('agent_name',ucwords($name),time() + 3600);
                setcookie('agent_last_name',ucwords($last_name),time() + 3600);
                setcookie('email_partner',$email_student,time() + 3600);
                setcookie('number_partner',$number_phone,time() + 3600);
                setcookie('is_older',true,time() + 3600);
            }

            /* set cookie */
            setcookie('phone_student',$number_phone,time() + 3600);
            setcookie('id_document',$id_document,time() + 3600);
            setcookie('document_type',$type_document,time() + 3600);
            setcookie('email_student',$email_student,time() + 3600);
            setcookie('name_student',ucwords($name),time() + 3600);
            setcookie('middle_name_student',ucwords($middle_name_student),time() + 3600);
            setcookie('last_name_student',ucwords($last_name),time() + 3600);
            setcookie('middle_last_name_student',ucwords($middle_last_name_student),time() + 3600);
            setcookie('billing_city',ucwords($city),time() + 3600);
            setcookie('billing_country',$country,time() + 3600);
            setcookie('name_institute',ucwords($name_institute),time() + 3600);
            setcookie('birth_date',$birth_date,time() + 3600);
            setcookie('initial_grade',$grade,time() + 3600);
            setcookie('program_id',$program,time() + 3600);

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
                'type_document' => $type_document,
                'id_document' => $id_document,
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