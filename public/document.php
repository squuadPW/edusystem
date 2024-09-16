<?php 

add_action('woocommerce_account_student-documents_endpoint', function() {

    /*
        0: no enviado
        1: enviado
        2: procesando
        3: rechazado
        4 vencido
        5: aprobado
    */

    global $current_user;
    $roles = $current_user->roles;
    if(!in_array('parent',$roles) && in_array('student',$roles)){
        $student_id = get_user_meta(get_current_user_id(),'student_id',true);
        if($student_id){
            $students = get_student_from_id($student_id);
        }else{
            $students = get_student(get_current_user_id());
        }
    }

    if (in_array('parent',$roles) && in_array('student',$roles) || in_array('parent',$roles) && !in_array('student',$roles)) {
        $students = get_student(get_current_user_id());
    }
    
    include(plugin_dir_path(__FILE__).'templates/documents.php');
});

function save_document(){
    if(isset($_GET['actions']) && !empty($_GET['actions'])){

        if($_GET['actions'] == 'save_documents'){

            global $wpdb,$current_user;
            $roles = $current_user->roles;
            $table_student_documents = $wpdb->prefix.'student_documents';
            $table_students = $wpdb->prefix.'students';
            $table_users_signatures = $wpdb->prefix.'users_signatures';
            $missing_documents = [];
            $user_signature = null;
            $pending_required_documents = false;
            if(isset($_POST['students']) && !empty($_POST['students'])){

                $students = $_POST['students'];

                /* foreach student */
                foreach($students as $student_id){
                    $files = $_POST['file_student_'.$student_id.'_id'];

                    foreach($files as $file_id){
                        
                        $status = $_POST['status_file_'.$file_id.'_student_id_'.$student_id];

                        if(isset($_FILES['document_'.$file_id.'_student_id_'.$student_id]) && !empty($_FILES['document_'.$file_id.'_student_id_'.$student_id])){
                            $file_temp = $_FILES['document_'.$file_id.'_student_id_'.$student_id];
                        }else{
                            $file_temp = [];
                        }

                        if($status == 0 || $status == 3 || $status == 4){

                            if(!empty($file_temp['tmp_name'])){
                                
                                $upload_data = wp_handle_upload($file_temp,array('test_form' => FALSE) );
                            
                                if ($upload_data && !is_wp_error($upload_data)) {
                                    
                                    $attachment = array(
                                        'post_mime_type' => $upload_data['type'],
                                        'post_title' => $file_id,
                                        'post_content' => '',
                                        'post_status' => 'inherit'
                                    );
                                    
                                    $attach_id = wp_insert_attachment($attachment, $upload_data['file']);
                                    $deleted = wp_delete_attachment($upload_data['file'], true );
                                    $attach_data = wp_generate_attachment_metadata($attach_id, $upload_data['file']);
                                    wp_update_attachment_metadata($attach_id, $attach_data);
                                    $wpdb->update($table_student_documents,['status' => 1,'attachment_id' => $attach_id, 'upload_at' => date('Y-m-d H:i:s')],['student_id' => $student_id,'id' => $file_id ]);
                                }
                            } else {
                                $file_is_required = $_POST['file_is_required'.$file_id.'_student_id_'.$student_id];
                                if ($file_is_required == 1 && !$pending_required_documents) {
                                    $pending_required_documents = true;
                                }

                                if (!in_array($student_id, $missing_documents)) {
                                    array_push($missing_documents, $student_id);
                                }

                            }
                        }
                    }

                    if (sizeof($missing_documents) > 0) {
                        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
                        $user_student = get_user_by('email', $student->email);
                        $user_signature = $wpdb->get_row("SELECT * FROM {$table_users_signatures} WHERE user_id = {$user_student->ID} AND document_id='MISSING DOCUMENTS'");
                    } else {
                        $email_update_document = WC()->mailer()->get_emails()['WC_Update_Document_Email'];
                        $email_update_document->trigger($student_id);
    
                        $access_virtual = true;
    
                        $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");
    
                        if($documents_student){
                            foreach($documents_student as $document){
                                if($document->status != 5){
                                    $access_virtual = false;
                                }
                            }
    
                            // VER  IFICAR FEE DE INSCRIPCION
                            global $wpdb;
                            $table_student_payment = $wpdb->prefix.'student_payments';
                            $table_students = $wpdb->prefix.'students';
                            $partner_id = get_current_user_id();
                            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
                            $student_id = $student->id;
                            $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = ". AES_FEE_INSCRIPTION);
                            // VERIFICAR FEE DE INSCRIPCION
    
                            //virtual classroom
                            if($access_virtual && isset($paid)){
                                $table_name = $wpdb->prefix . 'students'; // assuming the table name is "wp_students"
                                $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $student_id));
                                $type_document = array(
                                    'identification_document' => 1,
                                    'passport' => 2,
                                    'ssn' => 4,
                                )[$student->type_document];
                
                                $files_to_send = array();
                                $type_document = '';
                                switch ($student->type_document) {
                                    case 'identification_document':
                                        $type_document = 1;
                                        break;
                                    case 'passport':
                                        $type_document = 2;
                                        break;
                                    case 'ssn':
                                        $type_document = 4;
                                        break;
                                }
                
                                $type_document_re = '';
                                switch (get_user_meta($student->partner_id, 'type_document', true)) {
                                    case 'identification_document':
                                        $type_document_re = 1;
                                        break;
                                    case 'passport':
                                        $type_document_re = 2;
                                        break;
                                    case 'ssn':
                                        $type_document_re = 4;
                                        break;
                                }
                
                                
                                $gender = '';
                                switch ($student->gender) {
                                    case 'male':
                                        $gender = 'M';
                                        break;
                                    case 'female':
                                        $gender = 'F';
                                        break;
                                }
                
                                
                                $gender_re = '';
                                switch (get_user_meta($student->partner_id, 'gender', true)) {
                                    case 'male':
                                        $gender_re = 'M';
                                        break;
                                    case 'female':
                                        $gender_re = 'F';
                                        break;
                                }
                
                                $grade = '';
                                switch ($student->grade_id) {
                                    case 1:
                                        $grade = 9;
                                        break;
                                    case 2:
                                        $grade = 10;
                                        break;
                                    case 3:
                                        $grade = 11;
                                        break;
                                    case 4:
                                        $grade = 12;
                                        break;
                                }
                                $fields_to_send = array(
                                    // DATOS DEL ESTUDIANTE
                                    'id_document' => $student->id_document,
                                    'type_document' => $type_document,
                                    'firstname' => $student->name . ' ' . $student->middle_name,
                                    'lastname' => $student->last_name . ' ' . $student->middle_last_name,
                                    'birth_date' => $student->birth_date,
                                    'phone' => $student->phone,
                                    'email' => $student->email,
                                    'etnia' => $student->ethnicity,
                                    'grade' => $grade,
                                    'gender' => $gender,
                                    'cod_period' => $student->academic_period,
    
                                    // PADRE
                                    'id_document_re' => get_user_meta($student->partner_id, 'id_document', true), 
                                    'type_document_re' => $type_document_re,
                                    'firstname_re' => get_user_meta($student->partner_id, 'first_name', true),
                                    'lastname_re' => get_user_meta($student->partner_id, 'last_name', true),
                                    'birth_date_re' =>  get_user_meta($student->partner_id, 'birth_date', true),
                                    'phone_re' => get_user_meta($student->partner_id, 'billing_phone', true),
                                    'email_re' => get_user_meta($student->partner_id, 'billing_email', true),
                                    'gender_re' => $gender_re,
                
                                    'cod_program' => AES_PROGRAM_ID,
                                    'cod_tip' => AES_TYPE_PROGRAM,
                                    'address' => get_user_meta($student->partner_id, 'billing_address_1', true),
                                    'country' => get_user_meta($student->partner_id, 'billing_country', true),
                                    'city' => get_user_meta($student->partner_id, 'billing_city', true),
                                    'postal_code' => get_user_meta($student->partner_id, 'billing_postcode', true),
                                );
                
                                $all_documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
                                $documents_to_send = [];
                                foreach ($all_documents_student as $document) {
                                    if ($document->attachment_id) {
                                        array_push($documents_to_send, $document);
                                    }
                                }
                
                                foreach ($documents_to_send as $key => $doc) {
                                    $id_requisito = $wpdb->get_var($wpdb->prepare("SELECT id_requisito FROM {$wpdb->prefix}documents WHERE name = %s", $doc->document_id));
                                    $attachment_id = $doc->attachment_id;
                                    $attachment_path = get_attached_file($attachment_id);
                                    if ($attachment_path) {
                                        $file_name = basename($attachment_path);
                                        $file_type = mime_content_type($attachment_path);
                
                                        $files_to_send[] = array(
                                            'file' => curl_file_create($attachment_path, $file_type, $file_name),
                                            'id_requisito' => $id_requisito
                                        );
                                    }
                                }
                
                                create_user_laravel(array_merge($fields_to_send, array('files' => $files_to_send)));
                
                                update_status_student($student_id, 2);
    
                                if(in_array('parent',$roles) && !in_array('student',$roles)){
                                    create_user_student($student_id);
                                }
    
                                $exist = is_search_student_by_email($student_id);
                            
                                if(!$exist){
                                    create_user_moodle($student_id);
                                }else{
                                    $wpdb->update($table_students,['moodle_student_id' => $exist[0]['id']],['id' => $student_id]);
    
                                    $is_exist_password = is_password_user_moodle($student_id);
    
                                    if(!$is_exist_password){
                                        
                                        $password = generate_password_user();
                                        $wpdb->update($table_students,['moodle_password' => $password],['id' => $student_id]);
                                        change_password_user_moodle($student_id);
                                    }
                                }
                            }
                        }
                    }
        
                }

                
            }

            if ($pending_required_documents) {
                $missing_documents = [];
            }
            wc_add_notice( __( 'Documents saved successfully.', 'form-plugin' ), 'success' );
            $url = wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id')));
            if (count($missing_documents) > 0 && !$user_signature) {
                $url .= "?missing=". json_encode($missing_documents) . ""; // append data as query parameters
            }
            wp_redirect($url);
            exit;
        }
    }

    if($_GET['missing']) {
        global $wpdb,$current_user;
        $roles = $current_user->roles;
        $table_students = $wpdb->prefix.'students';
        $table_users_signatures = $wpdb->prefix.'users_signatures';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $user_signature = null;

        foreach (json_decode($_GET['missing']) as $key => $student_id) {
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
            $user_student = get_user_by('email', $student->email);
            $user_signature = $wpdb->get_row("SELECT * FROM {$table_users_signatures} WHERE user_id = {$user_student->ID} AND document_id='MISSING DOCUMENTS'");
            $document_was_created = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} and document_id = 'MISSING DOCUMENTS' ORDER BY id DESC");
        }

        if ($user_signature || !$document_was_created) {
            $url = wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id')));
            wp_redirect($url);
            exit;
        }
    }
}

add_action('wp_loaded','save_document');


function view_pending_documents(){
    
    global $current_user;
    $roles = $current_user->roles;

    $student_status = get_user_meta($current_user->ID,'status_register',true);

    if(!in_array('parent',$roles) && in_array('student',$roles)){
        $student_id = get_user_meta(get_current_user_id(),'student_id',true);
        if($student_id){
            $students = get_student_from_id($student_id);
        }else{
            $students = get_student(get_current_user_id());
        }
    }

    if (in_array('parent',$roles) && in_array('student',$roles) || in_array('parent',$roles) && !in_array('student',$roles)) {
        $students = get_student(get_current_user_id());
    }

    $solvency_administrative = true;

    if(in_array('parent',$roles) && in_array('student',$roles)){

        if($student_status == 1 || $student_status == '1'){

            foreach($students as $student){
                $documents = get_documents($student->id);

                foreach($documents as $document){

                    if($document->status != 5){
                        $solvency_administrative = false;
                    }
                }
            }
        
            if(!$solvency_administrative){
                include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
            }
        }

    }else if(in_array('parent',$roles) && !in_array('student',$roles)){

        if($student_status == 1 || $student_status == '1'){

            foreach($students as $student){
                $documents = get_documents($student->id);

                foreach($documents as $document){

                    if($document->status != 5){
                        $solvency_administrative = false;
                    }
                }
            }
        
            if(!$solvency_administrative){
                include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
            }
        }

    }else if(!in_array('parent',$roles) && in_array('student',$roles)){
        include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
    }

}

add_action('woocommerce_account_dashboard','view_pending_documents');

function get_name_document($document_id){
    /*
    $name = match ($document_id) {
        'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','aes'),
        'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','aes'),
        'id_parents' => __('ID OR CI OF THE PARENTS','aes'),
        'id_student' => __('ID STUDENTS','aes'),
        'photo_student_card' => __('PHOTO OF STUDENT CARD','aes'),
        'proof_of_grades' => __('PROOF OF GRADE','aes'),
        'proof_of_study' => __('PROOF OF STUDY','aes'),
        'vaccunation_card' => __('VACCUNATION CARD','aes'),
        default => '',
    };

    return $name;
    */
    return $document_id;
}

function get_status_document($status_id){

    $status = match ($status_id){
        '0' => __('No sent','aes'),
        '1' => __('Sent','aes'),
        '2' => __('Processing','aes'),
        '3' => __('Declined','aes'),
        '4' => __('Expired','aes'),
        '5' => __('Approved','aes'),
        default => '',
    };

    return $status;
}

function get_name_type_document($type_document){

    $type_document_parent = match($type_document){
        'passport' => __('Passport','aes'),
        'identification_document' => __('Identification Document','aes'),
        'ssn' => __('SSN'),
        default => '',
    };

    return $type_document_parent;
}