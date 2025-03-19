<?php

function add_admin_form_school_subjects_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'subject_details') {
            $subject_id = $_GET['subject_id'];
            $subject = get_subject_details($subject_id);
            $teachers = get_teachers_active();
            include (plugin_dir_path(__FILE__) . 'templates/school-subject-detail.php');
        }
        if ($_GET['section_tab'] == 'add_subject') {
            $teachers = get_teachers_active();
            include (plugin_dir_path(__FILE__) . 'templates/school-subject-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_subject_details') {
            global $wpdb;

            $table = $wpdb->prefix . 'school_subjects';   
            $subject_id = $_POST['subject_id'];
            $name = strtoupper($_POST['name']);
            $code_subject = strtoupper($_POST['code_subject']) ?? null;
            $description = $_POST['description'];
            $hc = $_POST['hc'];
            $moodle_course_id = $_POST['moodle_course_id'];
            $type = $_POST['type'];
            $is_active = $_POST['is_active'];
            $is_open = $_POST['is_open'];
            $min_pass = $_POST['min_pass'];
            $matrix_position = $_POST['matrix_position'];
            $max_students = $_POST['max_students'];
            $teacher_id = $_POST['teacher_id'];

            //update
            if (isset($subject_id) && !empty($subject_id)) {
                $wpdb->update($table, [
                    'name' => $name,
                    'code_subject' => $code_subject,
                    'description' => $description,
                    'min_pass' => $min_pass,
                    'matrix_position' => $matrix_position,
                    'max_students' => $max_students,
                    'hc' => $hc,
                    'moodle_course_id' => $moodle_course_id,
                    'teacher_id' => $teacher_id,
                    'type' => $type,
                    'is_active' => $is_active == 'on' ? 1 : 0,
                    'is_open' => $is_open == 'on' ? 1 : 0
                ], ['id' => $subject_id]);
            } else {
                $wpdb->insert($table, [
                    'name' => $name,
                    'code_subject' => $code_subject,
                    'description' => $description,
                    'min_pass' => $min_pass,
                    'matrix_position' => $matrix_position,
                    'max_students' => $max_students,
                    'hc' => $hc,
                    'moodle_course_id' => $moodle_course_id,
                    'teacher_id' => $teacher_id,
                    'type' => $type,
                    'is_active' => $is_active == 'on' ? 1 : 0,
                    'is_open' => $is_open == 'on' ? 1 : 0
                ]);
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_school_subjects_content'));
            exit;
        } else {
            $list_school_subjects = new TT_school_subjects_all_List_Table;
            $list_school_subjects->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-school-subjects.php');
        }
    }
}

class TT_school_subjects_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'code_subject':
                return ucwords($item[$column_name]);
            case 'name':
                return ucwords($item[$column_name]);
            case 'type':
                return ucwords($item[$column_name]);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_school_subjects_content&section_tab=subject_details&subject_id=' . $item['school_subject_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'code_subject' => __('Subject code', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'hc' => __('HC', 'edusystem'),
            'type' => __('Type', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_school_subject_pendings()
    {
        global $wpdb;
        $school_subjects_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $query_search = "";
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search = $_GET['s'];
            $query_search  = "WHERE (`name` LIKE '%{$search}%' || code_subject LIKE '%{$search}%')";
        }

        $query_electives = "";
        if (isset($_GET['subject_type']) && $_GET['subject_type'] != '') {
            $search = $_GET['subject_type'];
            if ($query_search != '') {
                $query_electives  = "AND (`type` = '{$search}')";
            } else {
                $query_electives  = "WHERE (`type` = '{$search}')";
            }
        }

        $school_subjects = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM wp_school_subjects {$query_search} {$query_electives} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($school_subjects) {
            foreach ($school_subjects as $subject) {
                array_push($school_subjects_array, [
                    'code_subject' => $subject['code_subject'],
                    'school_subject_id' => $subject['id'],
                    'name' => $subject['name'],
                    'hc' => $subject['hc'],
                    'type' => $subject['type'],
                ]);
            }
        }

        return ['data' => $school_subjects_array, 'total_count' => $total_count];
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

        $data_school_subjects = $this->get_school_subject_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_school_subjects['data'];
        $total_count = (int) $data_school_subjects['total_count'];

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

function get_subject_details($subject_id)
{
    global $wpdb;
    $table = $wpdb->prefix.'school_subjects';

    $subject = $wpdb->get_row("SELECT * FROM {$table} WHERE id={$subject_id}");
    return $subject;
}

function get_subject_details_code($code_subject)
{
    global $wpdb;
    $table_school_subjects = $wpdb->prefix.'school_subjects';

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE code_subject='{$code_subject}'");
    return $subject;
}