<?php

function show_admission_documents()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'update_document') {

            global $wpdb;
            $table_documents = $wpdb->prefix . 'documents';

            $document_id = $_POST['document_id'];
            $name = $_POST['name'];

            if (isset($_POST['is_required']) && !empty($_POST['is_required'])) {
                $is_required = 1;
            } else {
                $is_required = 0;
            }

            $wpdb->update($table_documents, ['name' => $name, 'is_required' => $is_required, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $document_id]);

            wp_redirect(admin_url('admin.php?page=admission-documents'));
            exit;

        } else if ($_GET['action'] == 'edit') {
            $document_id = $_GET['document_id'];
            $document = get_document_from_grade($document_id);
            include(plugin_dir_path(__FILE__) . 'templates/edit-document.php');
        }

    } else {

        $grades = get_grades();
        $documents = get_list_grades_documents();
        include(plugin_dir_path(__FILE__) . 'templates/list-grades.php');
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