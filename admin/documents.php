<?php

function show_admission_documents()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'update_document') {

            global $wpdb;
            $table_documents = $wpdb->prefix . 'documents';

            $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
            $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

            $type_file = isset($_POST['type_file']) ? sanitize_text_field($_POST['type_file']) : '';
            $type_file_array = array_filter(array_map('trim', explode(',', $type_file)));

            // Lista de extensiones permitidas (igual que en el input accept)
            $allowed_extensions = ['pdf','doc','docx','xl','xls','jpg','jpeg','png','web'];
            foreach ($type_file_array as $ext) {
                $ext_clean = strtolower(ltrim($ext, '.'));
                if (!in_array($ext_clean, $allowed_extensions, true)) {

                    // Guardar los datos del formulario en la cookie
                    setcookie('form_data', json_encode($_POST), time() + 10, '/');

                    setcookie('message-error', __('Invalid file type. Allowed formats: .pdf, .doc, .docx, .xl, .xls, .jpg, .jpge, .png, .web', 'edusystem'), time() + 10, '/');
                    wp_redirect( wp_get_referer());
                    exit;
                }
            }

            $id_requisito = sanitize_text_field($_POST['id_requisito']) ?? '';

            $academic_scope = $_POST['academic_scope'] ?? [];
            $academic_department = json_encode($academic_scope, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            if (isset($_POST['is_required']) && !empty($_POST['is_required'])) {
                $is_required = 1;
            } else {
                $is_required = 0;
            }

            if( $document_id ){
                $update = $wpdb->update($table_documents, [
                    'name' => $name, 
                    'is_required' => $is_required, 
                    'academic_department' => $academic_department,
                    'type_file' => $type_file,
                    'id_requisito' => $id_requisito,
                    'is_visible' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $document_id]);

                if( $update ){
                    setcookie('message', __('The document has been updated correctly.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=admission-documents&action=edit&document_id='.$document_id));
                } else {
                    // Guardar los datos del formulario en la cookie
                    setcookie('form_data', json_encode($_POST), time() + 10, '/');

                    setcookie('message-error', __('There were problems updating the document.'), time() + 10, '/');
                    wp_redirect( wp_get_referer());
                }

            } else {
                $insert = $wpdb->insert(
                    $table_documents,
                    [
                        'name'                => $name,
                        'is_required'         => $is_required,
                        'academic_department' => $academic_department,
                        'type_file'           => $type_file,
                        'id_requisito'        => $id_requisito,
                        'is_visible' => 1,
                        'updated_at'          => date('Y-m-d H:i:s')
                    ]
                );

                if( $insert ){
                    $document_id = $wpdb->insert_id;
                    setcookie('message', __('The document has been created successfully.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=admission-documents&action=edit&document_id='.$document_id));
                
                } else {

                    // Guardar los datos del formulario en la cookie
                    setcookie('form_data', json_encode($_POST), time() + 10, '/');

                    setcookie('message-error', __('There were problems creating the document.'), time() + 10, '/');
                    wp_redirect( wp_get_referer());
                }

            }

            exit;

        } else if( $_GET['action'] == 'delete' ){

            global $wpdb;
            $removed = false;
            $table_documents = $wpdb->prefix . 'documents';

            $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
            if( $document_id ) {

                $removed = $wpdb->delete(
                    $table_documents,          
                    [ 'id' => $document_id ],  
                    [ '%d' ]                   
                );
                
            } 
            
            if ( $removed ) {
                setcookie('message', __('The document has been successfully deleted.', 'edusystem'), time() + 10, '/');
            } else {
                setcookie('message-error', __('There was a problem trying to delete the document.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect(admin_url('admin.php?page=admission-documents'));
            exit;

        } else if ($_GET['action'] == 'edit') {
            $document_id = $_GET['document_id'];
            $document = get_document_from_grade($document_id);
            include(plugin_dir_path(__FILE__) . 'templates/document_view.php');
        }

    } else {

        $grades = get_grades();
        $documents = get_list_grades_documents();
        include(plugin_dir_path(__FILE__) . 'templates/list-documents.php');
        include(plugin_dir_path(__FILE__) . 'templates/modal-delete-document.php');
    }
}

function get_document_from_grade($document_id)
{

    global $wpdb;
    $table_documents = $wpdb->prefix . 'documents';

    $data = $wpdb->get_row("SELECT * FROM {$table_documents} WHERE id={$document_id}");
    return $data;
}

function get_grades()
{

    global $wpdb;
    $table_grades = $wpdb->prefix . 'grades';

    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");

    return $grades;
}

function get_list_grades_documents($grade_id = "")
{

    global $wpdb;
    $table_documents = $wpdb->prefix . 'documents';

    if (!empty($grade_id)) {

        $documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id={$grade_id}");
        return $documents;
    }

    $documents = $wpdb->get_results("SELECT * FROM {$table_documents}");
    return $documents;
}

function get_status_approved($document_id = "", $student_id = "")
{
    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $document = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id} AND document_id='{$document_id}'");
    return $document->status == 5 ? true : false;
}

function get_documents_ready($student_id)
{
    if (!is_numeric($student_id)) {
        return false;
    }

    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';

    // Optimized query to only get required fields and exclude specific documents
    $query = $wpdb->prepare(
        "SELECT COUNT(*) as total, 
         SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as approved
         FROM {$table_student_documents} 
         WHERE student_id = %d 
         AND document_id NOT IN ('PROOF OF STUDY', 'ID OR CI OF THE PARENTS')",
        $student_id
    );

    $result = $wpdb->get_row($query);

    if (!$result || $result->total === 0) {
        return false;
    }

    // Return true only if all documents are approved (status = 5)
    return $result->total === $result->approved;
}

function expired_documents($student_id)
{
    $documents = get_documents($student_id);
    $expired_documents = false;
    $today = date('Y-m-d');
    foreach ($documents as $document) {
        if ($document->status != 5 && ($document->max_date_upload && $document->max_date_upload < $today)) {
            $expired_documents = true;
        }
    }
    return $expired_documents;
}