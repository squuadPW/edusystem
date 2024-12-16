<?php

function add_admin_form_enrollments_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        global $wpdb;
        $table_academic_periods = $wpdb->prefix . 'academic_periods';
        $table_school_subjects = $wpdb->prefix.'school_subjects';
        $table_students = $wpdb->prefix.'students';
        $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
        $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects}");
        $students = $wpdb->get_results("SELECT * FROM {$table_students}");

        if ($_GET['section_tab'] == 'enrollment_details') {
            $enrollment_id = $_GET['enrollment_id'];
            $enrollment = get_enrollment_details($enrollment_id);
            $student = get_student_detail($enrollment->student_id);
            include (plugin_dir_path(__FILE__) . 'templates/enrollment-detail.php');
        }

        if ($_GET['section_tab'] == 'add_enrollment') {
            include (plugin_dir_path(__FILE__) . 'templates/enrollment-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_enrollment_details') {
            global $wpdb;
            $table_student_period_inscriptions = $wpdb->prefix.'student_period_inscriptions';

            $enrollment_id = $_POST['enrollment_id'];
            $status_id = $_POST['status_id'];
            $student_id = $_POST['student_id'];
            $code_subject = $_POST['code_subject'];
            $code_period = $_POST['code_period'];
            $cut_period = $_POST['cut_period'];
            $calification = $_POST['calification'];

            $subject = get_subject_details_code($code_subject);

            //update
            if (isset($enrollment_id) && !empty($enrollment_id)) {

                $wpdb->update($table_student_period_inscriptions, [
                    'status_id' => $status_id,
                    'student_id' => $student_id,
                    'subject_id' => $subject->id,
                    'code_subject' => $code_subject,
                    'code_period' => $code_period,
                    'cut_period' => $cut_period,
                    'calification' => $calification,
                ], ['id' => $enrollment_id]);

                setcookie('message', __('Changes saved successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_enrollments_content&section_tab=enrollment_details&enrollment_id=' . $enrollment_id));
                exit;
            } else {

                $wpdb->insert($table_student_period_inscriptions, [
                    'status_id' => $status_id,
                    'student_id' => $student_id,
                    'subject_id' => $subject->id,
                    'code_subject' => $code_subject,
                    'code_period' => $code_period,
                    'cut_period' => $cut_period,
                    'calification' => $calification,
                ]);

                wp_redirect(admin_url('admin.php?page=add_admin_form_enrollments_content'));
                exit;

            }
        } else {

            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_school_subjects = $wpdb->prefix.'school_subjects';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects}");

            $list_enrollments = new TT_enrollments_all_List_Table;
            $list_enrollments->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-enrollments.php');
        }
    }
}

class TT_enrollments_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'enrollment_',
                'plural' => 'enrollment_s',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'calification':
                return $item[$column_name];
            case 'subject_code':
            case 'period_cut':
                return strtoupper($item[$column_name]);
            case 'full_name':
                return strtoupper($item[$column_name]);
            case 'status':
                switch ($item[$column_name]) {
                    case 1:
                        return '<div style="color: blue; font-weight: 600">'. strtoupper('Active') . '</div>';
                        break;
                    case 0:
                        return '<div style="color: gray; font-weight: 600">'. strtoupper('To begin') . '</div>';
                        break;
                    case 2:
                        return '<div style="color: red; font-weight: 600">'. strtoupper('Unsubscribed') . '</div>';
                        break;
                    case 3:
                        return '<div style="color: green; font-weight: 600">'. strtoupper('Completed') . '</div>';
                        break;
                    case 4:
                        return '<div style="color: red; font-weight: 600">'. strtoupper('Failed') . '</div>';
                        break;
                }
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_enrollments_content&section_tab=enrollment_details&enrollment_id=' . $item['enrollment_id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
            default:
                return ucwords($item[$column_name]);
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'status' => __('Status', 'aes'),
            'full_name' => __('Student', 'aes'),
            'subject_code' => __('Subject - Code of subject', 'aes'),
            'period_cut' => __('Period - Cut', 'aes'),
            'calification' => __('Calification', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_enrollments()
    {
        global $wpdb;
        $table_student_period_inscriptions = $wpdb->prefix.'student_period_inscriptions';
        $table_students = $wpdb->prefix.'students';
        $enrollments_array = [];
        $search = $_GET['s'];
        $academic_period_cut = $_GET['academic_period_cut'];
        $academic_period = $_GET['academic_period'];
        $code_subject = $_GET['code_subject'];
        $status_id = $_GET['status_id'];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $students_id = $wpdb->get_col("SELECT id FROM {$table_students} WHERE (`name` LIKE '{$search}%' OR middle_name LIKE '{$search}%' OR last_name LIKE '{$search}%' OR middle_last_name LIKE '{$search}%' OR id_document LIKE '{$search}%' )");
        if (!empty($students_id)) {
            $students_id_list = implode(',', array_map('intval', $students_id)); // Asegúrate de que los IDs sean enteros
        }

        $query_academic_period = '';
        if ($academic_period) {
            $query_academic_period = "AND code_period ='" . $academic_period . "'";
        }

        $query_academic_period_cut = '';
        if ($academic_period_cut) {
            $query_academic_period_cut = "AND cut_period ='" . $academic_period_cut . "'";
        }

        $query_code_subject = '';
        if ($code_subject) {
            $query_code_subject = "AND code_subject = '" . $code_subject . "'";
        }

        
        $query_status_id = '';
        if ($status_id != '') {
            $query_status_id = "AND status_id = " . (int)$status_id . "";
        }

        $enrollments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_student_period_inscriptions} WHERE student_id IN ($students_id_list) {$query_academic_period} {$query_academic_period_cut} {$query_code_subject} {$query_status_id} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($enrollments) {
            foreach ($enrollments as $enrollment) {

                $student = get_student_detail($enrollment['student_id']);
                $subject = get_subject_details_code($enrollment['code_subject']);
                array_push($enrollments_array, [
                    'status' => $enrollment['status_id'],
                    'full_name' => $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name,
                    'subject_code' => $enrollment['code_subject'] ? $subject->name . ' (' . $enrollment['code_subject'] . ')' : 'N/A',
                    'period_cut' => $enrollment['code_period'] . ' - ' . $enrollment['cut_period'],
                    'enrollment_id' => $enrollment['id'],
                    'calification' => isset($enrollment['calification']) ? number_format($enrollment['calification'], 2) : 'N/A',
                ]);
            }
        }

        return ['data' => $enrollments_array, 'total_count' => $total_count];
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items()
    {

        $enrollments = $this->get_enrollments();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $enrollments['data'];
        $total_count = (int) $enrollments['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }

}

function get_enrollment_details($enrollment_id)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix.'student_period_inscriptions';

    $enrollment = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE id={$enrollment_id}");
    return $enrollment;
}

function get_type_document_student($type) {
    $value_type = '';
    switch ($type) {
        case 'passport':
            $value_type = 'Passport';
            break;
        case 'identification_document':
            $value_type = 'Indentificacion Document';
            break;

        default:
            $value_type = 'SSN';
            break;
    }

    return $value_type;
}

function get_search_student_id_document(){
        global $wpdb;
        $table_students = $wpdb->prefix.'students';
        $id_document = $_POST['id_document'];

        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id_document={$id_document}");
        wp_send_json(array('student' => $student));
        die();
}

add_action( 'wp_ajax_nopriv_search_student_id_document', 'get_search_student_id_document');
add_action( 'wp_ajax_search_student_id_document', 'get_search_student_id_document');
