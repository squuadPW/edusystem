<?php

function add_admin_form_payments_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_payment') {

            global $current_user;
            $name = get_user_meta($current_user->ID, 'first_name', true) . ' ' . get_user_meta($current_user->ID, 'last_name', true);
            $order_id = $_POST['order_id'];
            $description = $_POST['description'];

            $order = wc_get_order($order_id);
            $order->update_status('completed');
            $order->add_order_note('Payment approved by '. $name . '. Description: ' .($description != '' ? $description : 'N/A'), 2); // 2 = admin note
            $order->update_meta_data('payment_approved_by', $current_user->ID);
            $order->save();

            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content'));
            exit;
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {


        if ($_GET['section_tab'] == 'all_payments') {
            $list_payments = new TT_all_payments_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
        } else if ($_GET['section_tab'] == 'order_detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['order_id'];
            $order = wc_get_order($order_id);

            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        } else if ($_GET['section_tab'] == 'invoices_alliances') {

            if ($_GET['id_payment']) {
                global $wpdb;
                $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
                $wpdb->update($table_alliances_payments, ['status_id' => 1], ['id' => $_GET['id_payment']]);
            }

            $list_payments = new TT_Invoices_Alliances_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-invoices-alliance.php');
        } else if ($_GET['section_tab'] == 'invoices_institutes') {

            if ($_GET['id_payment']) {
                global $wpdb;
                $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
                $wpdb->update($table_institutes_payments, ['status_id' => 1], ['id' => $_GET['id_payment']]);
            }

            $list_payments = new TT_Invoices_Institutes_List_Table();
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-invoices-institutes.php');
        }

    } else {
        $list_payments = new TT_payment_pending_List_Table;
        $list_payments->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
    }
}


class TT_payment_pending_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'payment_pending',
            'plural' => 'payment_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'payment_id':
                return '#' . $item[$column_name];
            case 'date':
            case 'status':
            case 'payment_method':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'payment_id' => __('Payment ID', 'aes'),
            'date' => __('Date', 'aes'),
            'partner_name' => __('Name', 'aes'),
            'total' => __('Total', 'aes'),
            'payment_method' => __('Payment Method', 'aes'),
            'status' => __('Status', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_payment_pendings()
    {
        $orders_array = [];
        $args = [];
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $args['s'] = $_POST['s'];
        }

        $args['limit'] = $per_page; // limit to 10 orders per page
        $args['offset'] = $offset; // offset to start from the first order
        $args['status'] = array('wc-pending', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                array_push($orders_array, [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created()->format('F j, Y g:i a'),
                    'partner_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'total' => wc_price($order->get_total()),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        $args_filtered['limit'] = -1;
        $args_filtered['status'] = array('wc-pending', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $total_count = wc_get_orders(array_merge($args_filtered, array('return' => 'count')));
        return ['data' => $orders_array, 'total_count' => sizeof($total_count)];
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

        $data_payments = $this->get_payment_pendings();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $data = $data_payments['data'];
        $total_count = (int) $data_payments['total_count'];
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

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

class TT_all_payments_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'payment_pending',
            'plural' => 'payment_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'payment_id':
                return '#' . $item[$column_name];
            case 'date':
            case 'payment_method':
            case 'status':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'payment_id' => __('Payment ID', 'aes'),
            'date' => __('Date', 'aes'),
            'partner_name' => __('Name', 'aes'),
            'total' => __('Total', 'aes'),
            'payment_method' => __('Payment Method', 'aes'),
            'status' => __('Status', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_payment()
    {
        $orders_array = [];
        $args = [];
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $args['s'] = $_POST['s'];
        }

        $args['limit'] = $per_page; // limit to 10 orders per page
        $args['offset'] = $offset; // offset to start from the first order
        $args['status'] = array('wc-pending', 'wc-completed', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                array_push($orders_array, [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created()->format('F j, Y g:i a'),
                    'partner_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'total' => wc_price($order->get_total()),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        $args_filtered['limit'] = -1;
        $args_filtered['status'] = array('wc-pending', 'wc-completed', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $total_count = wc_get_orders(array_merge($args_filtered, array('return' => 'count')));
        return ['data' => $orders_array, 'total_count' => sizeof($total_count)];
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

        $data_payments = $this->get_payment();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_payments['data'];
        $total_count = (int) $data_payments['total_count'];

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

class TT_Invoices_Alliances_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'invoice_alliance',
            'plural' => 'invoices_alliances',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'status_id':
                return get_status_payment_institute($item->status_id);
            case 'alliance_id':
                $alliance = get_alliance_detail($item->alliance_id);
                return "{$alliance->name} {$alliance->last_name} - {$alliance->name_legal}";
            case 'month':
                return $item->month;
            case 'amount':
                return wc_price($item->amount);
            case 'total_orders':
                return $item->total_orders;
            case 'view_details':
                if ($item->status_id == 0) {
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'form-plugin') . "</a>";
                } else {
                    return "N/A";
                }
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'status_id' => __('Status', 'aes'),
            'alliance_id' => __('Alliance', 'aes'),
            'month' => __('Month', 'aes'),
            'amount' => __('Amount', 'aes'),
            'total_orders' => __('Total Orders', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_invoices_alliances()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
        $transactions = $wpdb->get_results("SELECT * FROM {$table_alliances_payments}");

        return ['data' => $transactions, 'total_count' => sizeof($transactions)];
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

        $data_invoices = $this->get_invoices_alliances();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_invoices['data'];
        $total_count = (int) $data_invoices['total_count'];

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

class TT_Invoices_Institutes_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'invoice_institute',
            'plural' => 'invoices_institutes',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'status_id':
                return get_status_payment_institute($item->status_id);
            case 'institute_id':
                $institute = get_institute_details($item->institute_id);
                return "{$institute->name} {$institute->last_name} - {$institute->business_name}";
            case 'month':
                return $item->month;
            case 'amount':
                return wc_price($item->amount);
            case 'total_orders':
                return $item->total_orders;
            case 'view_details':
                if ($item->status_id == 0) {
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'form-plugin') . "</a>";
                } else {
                    return "N/A";
                }
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'status_id' => __('Status', 'aes'),
            'institute_id' => __('Institute', 'aes'),
            'month' => __('Month', 'aes'),
            'amount' => __('Amount', 'aes'),
            'total_orders' => __('Total Orders', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_invoices_institutes()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
        $transactions = $wpdb->get_results("SELECT * FROM {$table_institutes_payments}");

        return ['data' => $transactions, 'total_count' => sizeof($transactions)];
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

        $data_invoices = $this->get_invoices_institutes();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_invoices['data'];
        $total_count = (int) $data_invoices['total_count'];

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