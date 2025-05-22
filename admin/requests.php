<?php

function add_admin_form_requests_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'request_details') {
            global $wpdb;
            $request_id = $_GET['request_id'];
            $request = get_request_details($request_id);
            $partner = get_user_by('id', $request->partner_id);
            $student = get_student_detail($request->student_id);
            include (plugin_dir_path(__FILE__) . 'templates/request-detail.php');
        } else if ($_GET['section_tab'] == 'type_details') {
            global $wpdb;
            $type_id = $_GET['type_id'];
            $type = get_type_request_details($type_id);
            include (plugin_dir_path(__FILE__) . 'templates/type-request-detail.php');
        } else if ($_GET['section_tab'] == 'all_requests') {
            $list_requests = new TT_All_Requests_List_Table;
            $list_requests->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-requests.php');
        } else if ($_GET['section_tab'] == 'types') {
            $list_requests = new TT_Types_Requests_List_Table;
            $list_requests->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-requests.php');
        }

    } else {
        if ($_GET['action'] == 'change_status_request') {
            global $wpdb;
            $table_requests = $wpdb->prefix . 'requests';

            $request_id = sanitize_text_field($_POST['request_id']);
            $description = sanitize_text_field($_POST['description']);
            $status_id = sanitize_text_field($_POST['status_id']);

            if (isset($request_id) && !empty($request_id)) {
                $wpdb->update($table_requests, [
                    'status_id' => $status_id,
                    'response' => $description ?? 'N/A'
                ], ['id' => $request_id]);
            }

            $title = 'Response to request: ';
            $request = get_request_details($request_id);
            send_notification_user($request->partner_id, $title . $description, ($status_id == 2 ? 3 : 1), 'requests');

            $student = get_student_detail($request->student_id);
            if ($student) {
                $user_student = get_user_by('email', $student->email);
                send_notification_user($user_student->ID, $title . $description, ($status_id == 2 ? 3 : 1), 'requests');
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_requests_content'));
            exit;
        } else if ($_GET['action'] == 'save_type_details') {
            global $wpdb;
            $table_type_requests = $wpdb->prefix . 'type_requests';

            $type_id = sanitize_text_field($_POST['type_id']);
            $type = sanitize_text_field($_POST['type']);

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            if (isset($type_id) && !empty($type_id)) {
                $wpdb->update($table_type_requests, [
                    'type' => $type,
                ], ['id' => $type_id]);
                wp_redirect(admin_url('admin.php?page=add_admin_form_requests_content&section_tab=type_details&type_id='.$type_id));
            } else {
                $wpdb->insert($table_type_requests, [
                    'type' => $type,
                ]);
                wp_redirect(admin_url('admin.php?page=add_admin_form_requests_content&section_tab=types'));
            }

            exit;
        }  else if ($_GET['action'] == 'delete_type') {
            global $wpdb;
            $table_type_requests = $wpdb->prefix . 'type_requests';
            $type_id = sanitize_text_field($_GET['type_id']);

            $wpdb->delete($table_type_requests, ['id' => $type_id]);
            wp_redirect(admin_url('admin.php?page=add_admin_form_requests_content&section_tab=types'));
            exit;
        } else {
            $list_requests = new TT_Pending_Requests_List_Table;
            $list_requests->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-requests.php');
        }
    }
}

class TT_Pending_Requests_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_requests_content&section_tab=request_details&request_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'type' => __('Request', 'edusystem'),
            'partner' => __('Partner', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_requests_pending()
    {
        global $wpdb;
        $requests_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_requests = $wpdb->prefix . 'requests';
        $requests = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_requests} WHERE status_id = 0 ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($requests) {
            foreach ($requests as $request) {
                $user = get_user_by('id', $request['partner_id']);
                $student = get_student_detail($request['student_id']);
                array_push($requests_array, [
                    'id' => $request['id'],
                    'type' => get_type_request_details($request['type_id'])->type,
                    'partner' => $user->first_name . ' ' . $user->last_name,
                    'student' => $student ? $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name : 'N/A',
                    'created_at' => $request['created_at'],
                ]);
            }
        }

        return ['data' => $requests_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_requests_pending();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_requests['data'];
        $total_count = (int) $data_requests['total_count'];

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

class TT_All_Requests_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_requests_content&section_tab=request_details&request_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'type' => __('Request', 'edusystem'),
            'partner' => __('Partner', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_requests()
    {
        global $wpdb;
        $requests_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_requests = $wpdb->prefix . 'requests';
        $requests = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_requests} WHERE status_id <> 0 ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($requests) {
            foreach ($requests as $request) {
                $user = get_user_by('id', $request['partner_id']);
                $student = get_student_detail($request['student_id']);
                array_push($requests_array, [
                    'id' => $request['id'],
                    'type' => get_type_request_details($request['type_id'])->type,
                    'partner' => $user->first_name . ' ' . $user->last_name,
                    'student' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
                    'created_at' => $request['created_at'],
                    'status' => get_request_status($request['status_id']),
                ]);
            }
        }

        return ['data' => $requests_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_requests();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_requests['data'];
        $total_count = (int) $data_requests['total_count'];

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

class TT_Types_Requests_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_requests_content&section_tab=type_details&type_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_requests_content&action=delete_type&type_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
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
            'type' => __('Request', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_types_request()
    {
        global $wpdb;
        $types_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_type_requests = $wpdb->prefix . 'type_requests';
        $types = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_type_requests} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($types) {
            foreach ($types as $type) {
                array_push($types_array, [
                    'type' => $type['type'],
                    'id' => $type['id'],
                ]);
            }
        }

        return ['data' => $types_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_types_request();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_requests['data'];
        $total_count = (int) $data_requests['total_count'];

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

function get_request_details($request_id)
{
    global $wpdb;
    $table_requests = $wpdb->prefix . 'requests';

    $request = $wpdb->get_row("SELECT * FROM {$table_requests} WHERE id={$request_id}");
    return $request;
}

function get_type_request_details($type_id)
{
    global $wpdb;
    $table_type_requests = $wpdb->prefix . 'type_requests';

    $type = $wpdb->get_row("SELECT * FROM {$table_type_requests} WHERE id={$type_id}");
    return $type;
}

function get_requests_user($partner_id, $student_id, $type, $status_id = '', $status_id_two = '')
{
    global $wpdb;
    $table_requests = $wpdb->prefix . 'requests';

    $query = "SELECT * FROM {$table_requests} WHERE partner_id={$partner_id} AND student_id={$student_id} AND `type`='{$type}'";
    if ($status_id != '' && $status_id_two == '') {
        $query .= "AND status_id={$status_id}";
    }

    if ($status_id != '' && $status_id_two != '') {
        $query .= "AND (status_id={$status_id} OR status_id={$status_id_two})";
    }

    $request = $wpdb->get_results($query);
    return $request;
}

function get_request_status($status) {
    $text = '';
    switch ($status) {
        case 0:
            $text = 'Pending for review';
            break;
        case 2:
            $text = 'Declined';
            break;
        case 3:
            $text = 'Approved';
            break;
    }
    return $text;
}