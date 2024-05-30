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

    $students = get_student(get_current_user_id());
    include(plugin_dir_path(__FILE__).'templates/documents.php');
});

function save_document(){
    if(isset($_GET['actions']) && !empty($_GET['actions'])){

        if($_GET['actions'] == 'save_documents'){

            global $wpdb;
            $table_student_documents = $wpdb->prefix.'student_documents';

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
                                    $wpdb->update($table_student_documents,['status' => 1,'attachment_id' => $attach_id],['student_id' => $student_id,'document_id' => $file_id ]);
                                }
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

    $student_status = get_user_meta($current_user->ID,'status_register',true);
    $students = get_student($current_user->ID);
    $solvency_administrative = true;

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

}

add_action('woocommerce_account_dashboard','view_pending_documents');

function get_name_document($document_id){

    $name = match ($document_id) {
        'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','form-plugin'),
        'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','form-plugin'),
        'id_parents' => __('ID OR CI OF THE PARENTS','form-plugin'),
        'id_student' => __('ID STUDENTS','form-plugin'),
        'photo_student_card' => __('PHOTO OF STUDENT CARD','form-plugin'),
        'proof_of_grades' => __('PROOF OF GRADE','form-plugin'),
        'proof_of_study' => __('PROOF OF STUDY','form-plugin'),
        'vaccunation_card' => __('Vaccunation_card','form-plugin'),
        default => '',
    };

    return $name;
}

function get_status_document($status_id){

    $status = match ($status_id){
        '0' => __('No sent','form-plugin'),
        '1' => __('Sent','form-plugin'),
        '2' => __('Processing','form-plugin'),
        '3' => __('Declined','form-plugin'),
        '4' => __('Expired','form-plugin'),
        '5' => __('Approved','form-plugin'),
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