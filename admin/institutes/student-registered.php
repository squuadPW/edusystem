<?php

function list_admin_institutes_student_registered_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'student-details') {

            global $current_user;
            $roles = $current_user->roles;
            $student = get_student_detail($_GET['student_id']);
            $partner = get_userdata($student->partner_id);
            include(plugin_dir_path(__FILE__) . '../templates/student-details.php');
        }

    } else {

        $list_student_institutes = new TT_all_Student_Institutes_List_Table;
        $list_student_institutes->prepare_items();
        include(plugin_dir_path(__FILE__) . '../templates/list-student-institutes.php');
    }
}

class TT_all_Student_Institutes_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'name':
                return $item['name'] . ' ' . $item['last_name'];
            case 'email':
                return $item['email'];
            case 'grade':
                return get_name_grade($item['grade_id']);
            case 'created_at':
                $datetime = Datetime::createFromFormat('Y-m-d H:i:s', $item['created_at']);

                return $datetime->format('F j, Y');
            case 'view_details':
                return "<a class='button button-primary' href='" . admin_url('admin.php?page=list_admin_institutes_student_registered_content&action=student-details&student_id=' . $item['id']) . "'>" . __('View details', 'edusystem') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {
        return ucwords($item['name'] . ' ' . $item['last_name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'name' => __('First name', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_departments()
    {

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $institute_id = get_user_meta(get_current_user_id(), 'institute_id', true);

        if (isset($_GET['s']) && !empty($_GET['s'])) {

            $search = $_GET['s'];

            $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE institute_id={$institute_id} AND (name LIKE '{$search}%' OR last_name LIKE '{$search}%' OR email LIKE '{$_GET['s']}%') ORDER BY created_at ASC", "ARRAY_A");
        } else {
            $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE institute_id={$institute_id} ORDER BY created_at ASC", "ARRAY_A");
        }

        return $data;
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

        $data_categories = $this->get_departments();

        $per_page = 100;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_categories;

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $this->items = $data;
    }

}