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

                      

                        if($status == 0 || $document->status == 3 || $document->status == 4){

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

    if($student_status == 1 || $student_status == '1'){

        $students = get_student(get_current_user_id());
        include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
    }

}

add_action('woocommerce_account_dashboard','view_pending_documents');