<?php

function show_admission_documents(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'update_document'){

            global $wpdb;
            $table_documents = $wpdb->prefix.'documents';

            $document_id = $_POST['document_id'];
            $name = $_POST['name'];
          
            if(isset($_POST['is_required']) && !empty($_POST['is_required'])){
                $is_required = 1;
            }else{
                $is_required = 0;
            }

            $wpdb->update($table_documents,['name' => $name,'is_required' => $is_required],['id' => $document_id]);

            wp_redirect(admin_url('admin.php?page=admission-documents'));
            exit;

        }else if($_GET['action'] == 'edit'){
            $document_id = $_GET['document_id'];
            $document = get_document_from_grade($document_id);
            include(plugin_dir_path(__FILE__).'templates/edit-document.php');
        }

    }else{

        $grades = get_grades();
        $documents = get_list_grades_documents();
        include(plugin_dir_path(__FILE__).'templates/list-grades.php');
    }
}

function get_document_from_grade($document_id){

    global $wpdb;
    $table_documents = $wpdb->prefix.'documents';

    $data = $wpdb->get_row("SELECT * FROM {$table_documents} WHERE id={$document_id}");
    return $data;
}

function get_grades(){

    global $wpdb;
    $table_grades = $wpdb->prefix.'grades';

    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");

    return $grades;
}

function get_list_grades_documents($grade_id = ""){
    
    global $wpdb;
    $table_documents = $wpdb->prefix.'documents';

    if(!empty($grade_id)){
        
        $documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id={$grade_id}");
        return $documents;
    }

    $documents = $wpdb->get_results("SELECT * FROM {$table_documents}");
    return $documents;
}