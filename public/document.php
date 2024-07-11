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

    $student_id = get_user_meta(get_current_user_id(),'student_id',true);
  
    if($student_id){
        $students = get_student_from_id($student_id);
    }else{
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
                                    $wpdb->update($table_student_documents,['status' => 1,'attachment_id' => $attach_id],['student_id' => $student_id,'id' => $file_id ]);
                                }
                            }
                        }
                    }
                    
                    $email_update_document = WC()->mailer()->get_emails()['WC_Update_Document_Email'];
                    $email_update_document->trigger($student_id);

                    $access_virtual = true;

                    $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");

                    if($documents_student){

                        foreach($documents_student as $document){

                            if($document->status == 0){
                                $access_virtual = false;
                            }
                        }
                    }

                    // VERIFICAR FEE DE INSCRIPCION
                    global $wpdb;
                    $table_student_payment = $wpdb->prefix.'student_payments';
                    $table_students = $wpdb->prefix.'students';
                    $partner_id = get_current_user_id();
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
                    $student_id = $student->id;
                    $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and amount = 299.00");
                    // VERIFICAR FEE DE INSCRIPCION

                    //virtual classroom
                    if($access_virtual && isset($paid)){

                        update_status_student($student_id,2);

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

            wc_add_notice( __( 'Documents saved successfully.', 'form-plugin' ), 'success' );
            wp_redirect(wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))));
            exit;
        }
    }
}

add_action('wp_loaded','save_document');


function view_pending_documents(){
    
    global $current_user;
    $roles = $current_user->roles;

    $student_status = get_user_meta($current_user->ID,'status_register',true);
    $student_id = get_user_meta(get_current_user_id(),'student_id',true);
  
    if($student_id){
        $students = get_student_from_id($student_id);
    }else{
        $students = get_student($current_user->ID);
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