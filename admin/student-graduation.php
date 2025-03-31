<?php

function add_admin_form_student_graduated_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'graduated') {
            $institute = 1;
            $list_student_graduated = new TT_All_Student_Graduated_List_Table;
            $list_student_graduated->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-student-graduation.php');
        }
    } else {
        $institute = 0;
        $list_student_graduated = new TT_All_Student_Pending_Graduated_List_Table;
        $list_student_graduated->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-student-graduation.php');
    }
}

class TT_All_Student_Pending_Graduated_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
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
            'student' => __('Student', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_students_pending_graduate()
    {
        global $wpdb;
        $pending_array = [];

        $table_students = $wpdb->prefix . 'students';
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE status_id != 5 ORDER BY id DESC", "ARRAY_A");

        if ($students) {
            foreach ($students as $student) {
                $academic_ready = get_academic_ready($student['id']);
                if($academic_ready) {
                    array_push($pending_array, [
                        'id' => $student['id'],
                        'student' => $student['last_name'] . ' ' . $student['middle_last_name'] . ' ' . $student['name'] . ' ' . $student['middle_name']
                    ]);
                }
            }
        }

        return ['data' => $pending_array];
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

        $data_requests = $this->get_students_pending_graduate();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_requests['data'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $this->items = $data;
    }

}

class TT_All_Student_Graduated_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'institute':
                return $item[$column_name] ?? 'N/A';
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
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
            'student' => __('Student', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_student_graduated()
    {
        global $wpdb;
        $graduated_array = [];

        $table_students = $wpdb->prefix . 'students';
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE status_id = 5 ORDER BY id DESC", "ARRAY_A");

        if ($students) {
            foreach ($students as $student) {
                array_push($graduated_array, [
                    'id' => $student['id'],
                    'student' => $student['last_name'] . ' ' . $student['middle_last_name'] . ' ' . $student['name'] . ' ' . $student['middle_name']
                ]);
            }
        }

        return ['data' => $graduated_array];
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

        $data_requests = $this->get_student_graduated();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_requests['data'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $this->items = $data;
    }

}