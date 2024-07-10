<?php

function save_student(){
    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'save_student'){

            global $woocommerce;

            setcookie('is_older','',time());

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
           
            //clear cart
            $woocommerce->cart->empty_cart(); 

            //add program to cart
            if($program == 'aes'){
                // EN LOCAL JOSE MORA
                switch ($grade) {
                    case '1':
                        $variation = wc_get_product(468);
                        $metadata = $variation->get_meta_data();
                        $woocommerce->cart->add_to_cart(465, 1, 468, $metada);
                        $woocommerce->cart->add_to_cart(484, 1);
                        break;

                    case '2':
                        $variation = wc_get_product(472);
                        $metadata = $variation->get_meta_data();
                        $woocommerce->cart->add_to_cart(466, 1, 472, $metada);
                        $woocommerce->cart->add_to_cart(484, 1);
                        break;

                    default:
                        $variation = wc_get_product(475);
                        $metadata = $variation->get_meta_data();
                        $woocommerce->cart->add_to_cart(467, 1, 475, $metada);
                        $woocommerce->cart->add_to_cart(484, 1);
                        break;
                }

                // EN PRODUCTIVO
                // DESCOMENTAR CUANDO ESTE EN PRODUCTIVO, Y COMENTAR EL PRIMERO
                // switch ($grade) {
                //     case '1':
                //         $variation = wc_get_product(54);
                //         $metadata = $variation->get_meta_data();
                //         $woocommerce->cart->add_to_cart(51, 1, 54, $metada);
                //         $woocommerce->cart->add_to_cart(63, 1);
                //         break;

                //     case '2':
                //         $variation = wc_get_product(57);
                //         $metadata = $variation->get_meta_data();
                //         $woocommerce->cart->add_to_cart(52, 1, 57, $metada);
                //         $woocommerce->cart->add_to_cart(63, 1);
                //         break;

                //     default:
                //         $variation = wc_get_product(60);
                //         $metadata = $variation->get_meta_data();
                //         $woocommerce->cart->add_to_cart(53, 1, 60, $metada);
                //         $woocommerce->cart->add_to_cart(63, 1);
                //         break;
                // }
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

    global $current_user;
    $roles = $current_user->roles;

    $student_id = get_user_meta(get_current_user_id(),'student_id',true);
   
    if($student_id){
        $student = get_student_from_id($student_id);
    }else{
        $student = get_student(get_current_user_id());
    }


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

function get_student_from_id($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id={$student_id}");
    return $data;
}

function insert_student($customer_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $wpdb->insert($table_students,[
        'name' => $_COOKIE['name_student'],
        'middle_name' => $_COOKIE['middle_name_student'],
        'last_name' => $_COOKIE['last_name_student'],
        'middle_last_name' => $_COOKIE['middle_last_name_student'],
        'birth_date' => date_i18n('Y-m-d',strtotime($_COOKIE['birth_date'])),
        'grade_id' => $_COOKIE['initial_grade'],
        'name_institute' => $_COOKIE['name_institute'],
        'institute_id' => $_COOKIE['institute_id'],
        'program_id' => $_COOKIE['program_id'],
        'partner_id' => $customer_id, 
        'phone' => $_COOKIE['phone_student'],
        'email' => $_COOKIE['email_student'],
        'status_id' => 0,
        'country' => $_POST['billing_country'],
        'city' => $_POST['billing_city'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $student_id = $wpdb->insert_id;

    return $student_id;
}

function create_user_student($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    if($data){

        $user = get_user_by('email',$data->email);

        if(!$user){
            $user_id = wp_create_user($data->email,generate_password_user(),$data->email);
            $user = new WP_User($user_id);
            $user->set_role( 'student' );

            update_user_meta($user_id,'first_name',$data->name);
            update_user_meta($user_id,'last_name',$data->last_name);
            update_user_meta($user_id,'billing_phone',$data->phone);
            update_user_meta($user_id,'billing_email',$data->email);
            update_user_meta($user_id,'birth_date',$data->birth_date);
            update_user_meta($user_id,'student_id',$student_id);
            wp_new_user_notification($user_id, null, 'both' );
            return $user_id;
        }

        update_user_meta($user->ID,'student_id',$student_id);

        return $user->ID;
    }
}

function update_status_student($student_id,$status_id){
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $wpdb->update($table_students,[
        'status_id' => $status_id,
        'updated_at' => date('Y-m-d H:i:s')
    ],['id' => $student_id]);
}

function insert_register_documents($student_id,$grade_id){

    global $wpdb;
    $table_student_documents = $wpdb->prefix.'student_documents';
    $table_documents = $wpdb->prefix.'documents'; 

    $documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id={$grade_id}");

    if($documents){

        foreach($documents as $document){

            $wpdb->insert($table_student_documents,[
                'student_id' => $student_id,
                'document_id' => $document->name,
                'is_required' => $document->is_required,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}

function get_documents($student_id){

    global $wpdb;
    $table_student_documents = $wpdb->prefix.'student_documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
    return $documents;
}

function get_name_grade($grade_id){

    $grade = match($grade_id){
        '1' => __('9no (antepenultimate)','aes'),
        '2' => __('10mo (penultimate)','aes'),
        '3' => __('11vo (Last)','aes'),
        '4' => __('Bachelor (graduate)','aes'),
        default => ''
    };

    return $grade;
}

function get_name_program($program_id){

    $program = match($program_id){
        'aes' => __('AES (Dual Diploma)','aes'),
        default => "",
    };

    return $program;
}

function get_gender($gender_id){

    $gender = match($gender_id){
        'male' => __('Male','aes'),
        'female' => __('Female','aes'),
        default => "",
    };

    return $gender;

}

function save_student_details(){

    if(isset($_POST['action']) && !empty($_POST['action'])){


        if($_POST['action'] == 'save_student_details'){

            global $wpdb;
            $table_students = $wpdb->prefix.'students';
           
            $student_id = $_POST['student_id'];
            $document_type = $_POST['document_type'];
            $id_document = $_POST['id_document'];
            $first_name = $_POST['account_first_name'];
            $middle_name = $_POST['account_middle_name'];
            $middle_last_name = $_POST['account_middle_last_name'];
            $last_name = $_POST['account_last_name'];
            $email = $_POST['account_email'];
            $phone = $_POST['number_phone_hidden'];
            $gender = $_POST['gender'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $postal_code = $_POST['postal_code'];

            $wpdb->update($table_students,[
                'type_document' => $document_type,
                'id_document' => $id_document,
                'name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'country' => $country,
                'city' => $city,
                'postal_code' => $postal_code,
            ],[
                'id' => $student_id
            ]);

        
            wc_add_notice(__( 'information changed successfully.', 'aes' ), 'success' );
            wp_redirect(wc_get_account_endpoint_url('student-details').'/?student='.$student_id);
            exit;
            
        }

        if($_POST['action'] == 'save_password_moodle'){

            global $wpdb;
            $table_students = $wpdb->prefix.'students';

            $moodle_password = $_POST['password'];
            $student_id = $_POST['student_id'];
            $wpdb->update($table_students,['moodle_password' => $moodle_password],['id' => $student_id]);
            change_password_user_moodle($student_id);
            wc_add_notice(__( 'information changed successfully.', 'aes' ), 'success' );
            wp_redirect(wc_get_account_endpoint_url('student-details').'/?student='.$student_id);
            exit;
        }
    }

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'access_moodle_url'){

            global $wpdb;
            $table_students = $wpdb->prefix.'students';
            $student_id = $_GET['student_id']; 

            $data = $wpdb->get_row("SELECT * FROM {$table_students} where id={$student_id}");

            if($data){

                $data_url = get_url_login($data->email);
             
                if(isset($data_url) && !empty($data_url)){
                    nocache_headers();
                    wp_redirect($data_url);
                }else{
                    nocache_headers();
                    wp_redirect(get_option('moodle_url'));
                }

                exit;
            }
        }
    }
}

add_action('wp_loaded','save_student_details');

function view_access_classroom(){

    global $current_user,$wpdb;
    $table_students = $wpdb->prefix.'students';
    $roles = $current_user->roles;

    if(!in_array('student',$roles)){
        return;
    }

    $student_id = get_user_meta($current_user->ID,'student_id',true);

    if(!$student_id){
        $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id={$current_user->ID}");
    }else{
        $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    }

    if($data->status_id <= 1){
        return;
    }

    include(plugin_dir_path(__FILE__).'templates/student-access-classroom.php');
}

add_action('woocommerce_account_dashboard','view_access_classroom');