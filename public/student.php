<?php

function save_student(){

    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'save_student'){

            global $woocommerce;

            $name = $_POST['name_student'];
            $last_name = $_POST['lastname_student'];
            $number_phone = $_POST['number_phone'];
            $number_partner = $_POST['number_partner']; 
            $email_student = $_POST['email_student'];
            $email_partner = $_POST['email_partner'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $birth_date = $_POST['birth_date'];
            $agent_name = $_POST['agent_name'];
            $agent_last_name = $_POST['agent_last_name'];
            $program = $_POST['program'];
            $grade = $_POST['grade'];
            $name_institute = $_POST['name_institute'];

            /* set cookie */
            setcookie('name_student',ucwords($name),time() + 3600);
            setcookie('last_name_student',ucwords($last_name),time() + 3600);
            setcookie('billing_city',$city,time() + 3600);
            setcookie('billing_country',$country,time() + 3600);
            setcookie('billing_phone',$number_phone,time() + 3600);
            setcookie('billing_email',$email_student,time() + 3600);
            setcookie('initial_grade',$grade,time() + 3600);
            setcookie('name_institute',$name_institute,time() + 3600);
            setcookie('birth_date',$birth_date,time() + 3600);
            setcookie('grade',$grade,time() + 3600);
            setcookie('name_institute',$name_institute,time() + 3600);
            setcookie('program_id',$program,time() + 3600);
            setcookie('agent_name',$agent_name,time() + 3600);
            setcookie('agent_last_name',$agent_last_name,time() + 3600);
            setcookie('email_partner',$email_partner,time() + 3600);
            setcookie('number_partner',$number_partner,time() + 3600);

            //clear cart
            $woocommerce->cart->empty_cart(); 

            //add program to cart
            if($program == 'aes'){
                $woocommerce->cart->add_to_cart(103,1);
            }else if($program == 'psp'){
                $woocommerce->cart->add_to_cart(102,1);
            }else if($program == 'aes_psp'){
                $woocommerce->cart->add_to_cart(103,1);
                $woocommerce->cart->add_to_cart(102,1);
            }

            wp_redirect(wc_get_checkout_url());
            exit;
        };

    }
}

add_action('wp_loaded','save_student');

add_action('woocommerce_account_student_endpoint', function(){

    $student = get_student(get_current_user_id());
    include(plugin_dir_path(__FILE__).'templates/student.php');
});

add_action('woocommerce_account_student-details_endpoint', function(){

    $student = get_student_detail($_GET['student']);
    include(plugin_dir_path(__FILE__).'templates/student-details.php');
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
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    $student_id = $wpdb->insert_id;

    return $student_id;
}

function update_status_student($student_id,$status_id){
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $wpdb->update($table_students,[
        'status_id' => $status_id,
        'updated_at' => date('Y-m-d H:i:s')
    ],['id' => $student_id]);
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

function get_name_grade($grade_id){

    $grade = match($row->grade_id){
        '1' => __('9no (antepenúltimo)','aes'),
        '2' => __('10mo (penúltimo)','aes'),
        '3' => __('11vo (último)','aes'),
        '4' => __('Bachiller (graduado)','aes'),
        default => ''
    };

    return $grade;
}

function get_name_program($program_id){

    $program = match($row->program_id){
        'aes' => __('AES (Dual Diploma)','aes'),
        'psp' => __('PSP (Carrera Universitaria)','aes'),
        'aes_psp' => __('AES (Dual Diploma)','aes').','.__('AES (Dual Diploma)','aes'),
        default => "",
    };

    return $program;
}