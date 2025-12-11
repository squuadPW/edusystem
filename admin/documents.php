<?php

function show_admission_documents()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'update_document') {

            global $wpdb;
            $table_documents = $wpdb->prefix . 'documents';

            $document_id = isset($_POST['document_id']) ? intval($_POST['document_id']) : 0;
            $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';

            $academic_scope = isset($_POST['academic_scope']) && is_array($_POST['academic_scope'])
                ? array_map('sanitize_text_field', $_POST['academic_scope'])
                : [];

            $scope_required = isset($_POST['scope_required']) && is_array($_POST['scope_required'])
                ? array_map('sanitize_text_field', $_POST['scope_required'])
                : [];

            $type_file = isset($_POST['type_file']) ? sanitize_text_field($_POST['type_file']) : '';
            $type_file_array = array_filter(array_map('trim', explode(',', $type_file)));

            // Lista de extensiones permitidas (igual que en el input accept)
            $allowed_extensions = ['pdf','docx','jpg','png'];
            foreach ($type_file_array as $ext) {
                $ext_clean = strtolower(ltrim($ext, '.'));
                if (!in_array($ext_clean, $allowed_extensions, true)) {

                    /* 
                    * HAY QUE PONER UNA NOTIFICACION DE ERROR
                    */
                    wp_die(__('Invalid file type. Allowed formats: .pdf, .docx, .jpg, .png','edusystem'));
                }
            }

            $id_requisito = sanitize_text_field($_POST['id_requisito']) ?? '';;


            if (isset($_POST['is_required']) && !empty($_POST['is_required'])) {
                $is_required = 1;
            } else {
                $is_required = 0;
            }

            // guarda el array de los programas del documento
            $academic_department = [];
            foreach ($academic_scope as $scope) {
                $academic_department[$scope] = [
                    'required' => in_array($scope, $scope_required)
                ];
            }
            // Convertir a JSON (aunque esté vacío)
            $academic_department_json = json_encode($academic_department, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $wpdb->update($table_documents, [
                'name' => $name, 
                'is_required' => $is_required, 
                'academic_department' => $academic_department_json,
                'type_file' => $type_file,
                'id_requisito' => $id_requisito,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $document_id]);

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