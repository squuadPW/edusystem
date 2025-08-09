<?php

function list_admin_partner_invoice_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'invoice-detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order = wc_get_order($_GET['payment_id']);
            include(plugin_dir_path(__FILE__) . '../templates/payment-details.php');
        }

    } else {

        global $current_user;
        $roles = $current_user->roles;
        $date = get_dates_search('today', '');
        $start_date = date('m/d/Y', strtotime('today'));
        $orders = [];
        include(plugin_dir_path(__FILE__) . '../templates/list-invoice-alliance.php');
    }
}

function list_admin_partner_payments_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'payment-detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . '../templates/payment-details.php');
        }

    } else {

        global $current_user;
        $roles = $current_user->roles;
        $date = get_dates_search('today', '');
        $start_date = date('m/d/Y', strtotime('today'));
        $orders = get_order_alliance($date[0], $date[1]);
        include(plugin_dir_path(__FILE__) . '../templates/list-payment-alliance.php');
    }
}

function list_admin_partner_students_content()
{
    $list_students_institute = new TT_All_Institute_Students_List_Table;
    $list_students_institute->prepare_items();
    include(plugin_dir_path(__FILE__) . '../templates/list-all-students-institute.php');
}

function handle_custom_query_meta_alliance($query, $query_vars)
{
    if (!empty($query_vars['alliance_id'])) {
        $query['meta_query'][] = array(
            'key' => 'alliance_id',
            'value' => esc_attr($query_vars['alliance_id']),
        );
    }

    return $query;
}
add_filter('woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_meta_alliance', 10, 2);

function get_order_alliance($start_date, $end_date, $id = "")
{

    $data_fees = [];
    $alliance_id = get_user_meta(get_current_user_id(), 'alliance_id', true);
    $total = 0.00;

    if (isset($_GET['alliance_id']) && !empty($_GET['alliance_id'])) $alliance_id = $_GET['alliance_id'];

    if (!empty($id)) $alliance_id = $id;

    if (empty($alliance_id)) return [];

    $date = "";
    if ( !empty($start_date) && !empty($end_date) ) {

        // Convertir fechas a formato MySQL (YYYY-MM-DD)
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $date = " AND date_payment BETWEEN '$start_date' AND '$end_date' ";
    }

    global $wpdb;
    $sql = $wpdb->prepare(
        "SELECT *
        FROM `{$wpdb->prefix}student_payments`
        WHERE status_id = 1 AND JSON_CONTAINS(`alliances`, JSON_OBJECT('id', %s)) $date
        ORDER BY date_payment DESC",
        $alliance_id
    );

    $payments = $wpdb->get_results($sql);

    foreach ( $payments as $payment ) {

        // Inicializar el fee_amount
        $fee_amount = 0;

        foreach ( json_decode($payment->alliances, true) as $alliance) {
            if ( $alliance['id'] == $alliance_id ) {
                $fee_amount = $alliance['calculated_fee_amount'];
                break;
            }
        }

        // $order_id = $payment->order_id;
        $order = wc_get_order( $payment->order_id );

        array_push($data_fees, [
            'order_id' => $order->get_id(),
            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'fee' => $fee_amount,
            'created_at' => $payment->date_payment
        ]);

        $total += (float) $fee_amount;

    }
    
    return ['total' => $total, 'orders' => $data_fees];

}

function get_list_fee_alliance()
{

    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];
        $alliance_id = $_POST['alliance_id'] || $_GET['alliance_id'];

        $html = "";
        $html_transactions = "";
        $dates = get_dates_search($filter, $custom);
        $current_invoice_date = get_dates_search('this-month', null);
        $filtered_invices = get_invoices_alliances($dates[0], $dates[1], $alliance_id);
        $current_invoice = get_invoices_alliances($current_invoice_date[0], $current_invoice_date[1], $alliance_id);
        $transactions_pending = get_transactions_alliances($dates[0], $dates[1], $alliance_id, 0);
        $transactions_complete = get_transactions_alliances($dates[0], $dates[1], $alliance_id, 1);

        if ( !empty( $filtered_invices['orders'] ) ) {

            foreach ( $filtered_invices['orders'] as $order ) {
                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'edusystem') . "'>";
                $html .= '#' . $order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='" . __('Customer', 'edusystem') . "'>" . $order['customer'] . "</td>";
                $html .= "<td class='column' data-colname='" . __('Fee', 'edusystem') . "'>" . wc_price($order['fee']) . "</td>";
                $html .= "<td class='column' data-colname='" . __('Created', 'edusystem') . "'><b>" . $order['created_at'] . "</b></td>";
                $html .= "<td class='column' data-colname='" . __('Action', 'edusystem') . "'>";

                if ($alliance_id) {
                    $html .= "<a class='button button-primary' href=" . admin_url('admin.php?page=add_admin_partners_content&section_tab=payment-detail&payment_id=' . $order['order_id']) . ">" . __('View details', 'edusystem') . "</a>";
                } else {
                    $html .= "<a class='button button-primary' href=" . admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id=' . $order['order_id']) . ">" . __('View details', 'edusystem') . "</a>";
                }

                $html .= "</td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='5' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
            $html .= "</tr>";
        }

        $transactions['total_pending'] = wc_price( (float) $transactions_pending['total']);
        $transactions['total_paid'] = wc_price( (float) $transactions_complete['total']);
        $transactions['total'] = wc_price( (float) ($transactions_complete['total'] + (float) $transactions_pending['total']) );
        $transactions['orders'] = array_merge($transactions_pending['orders'], $transactions_complete['orders']);
        if (!empty($transactions['orders'])) {

            foreach ($transactions['orders'] as $order) {
                $html_transactions .= "<tr>";
                $html_transactions .= "<td class='column column-primary' data-colname='" . __('Status', 'edusystem') . "'>";
                $html_transactions .= $order['status'];
                $html_transactions .= "</td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Month', 'edusystem') . "'><b>" . $order['month'] . "</b></td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Amount', 'edusystem') . "'>" . wc_price($order['amount']) . "</td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Total orders', 'edusystem') . "'><b>" . $order['total_orders'] . "</b></td>";
                $html_transactions .= "</tr>";
            }

        } else {
            $html_transactions .= "<tr>";
            $html_transactions .= "<td colspan='4' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
            $html_transactions .= "</tr>";
        }

        $logger = wc_get_logger();
        $logger->info('pruebas', [ 'total' => $filtered_invices['total'], "price" => wc_price( $filtered_invices['total']) ] );

        $current_invoice['total'] = wc_price( (float) $current_invoice['total']);
        $filtered_invices['total'] = wc_price( (float) $filtered_invices['total']);

        
        
        echo json_encode(['status' => 'success', 'html' => $html, 'html_transactions' => $html_transactions, 'data' => $filtered_invices, 'current_invoice' => $current_invoice, 'transactions' => $transactions]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_fee_alliance', 'get_list_fee_alliance');
add_action('wp_ajax_list_fee_alliance', 'get_list_fee_alliance');

function get_invoices_alliances($start, $end, $id = "")
{

    $data_fees = [];
    $alliance_id = get_user_meta(get_current_user_id(), 'alliance_id', true);
    $total = 0.00;

    if (isset($_GET['alliance_id']) && !empty($_GET['alliance_id'])) $alliance_id = $_GET['alliance_id'];

    if (!empty($id)) $alliance_id = $id;

    if (empty($alliance_id)) return [];

    $date = "";
    if ( !empty($start) && !empty($end) ) {

        // Convertir fechas a formato MySQL (YYYY-MM-DD)
        $start_date = date('Y-m-d', strtotime($start));
        $end_date = date('Y-m-d', strtotime($end));

        $date = " AND date_payment BETWEEN '$start_date' AND '$end_date' ";
    }

    global $wpdb;
    $sql = $wpdb->prepare(
        "SELECT *
        FROM `wp_student_payments`
        WHERE status_id = 1 AND JSON_CONTAINS(`alliances`, JSON_OBJECT('id', %s)) $date
        ORDER BY date_payment DESC",
        $alliance_id
    );

    $payments = $wpdb->get_results($sql);

    foreach ( $payments as $payment ) {

        // Inicializar el fee_amount
        $fee_amount = 0;

        foreach ( json_decode($payment->alliances, true) as $alliance) {
            if ( $alliance['id'] == $alliance_id ) {
                $fee_amount = $alliance['calculated_fee_amount'];
                break;
            }
        }

        // $order_id = $payment->order_id;
        $order = wc_get_order( $payment->order_id );

        array_push($data_fees, [
            'order_id' => $order->get_id(),
            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'fee' => $fee_amount,
            'created_at' => $payment->date_payment
        ]);

        $total += (float) $fee_amount;

    }

    return ['total' => $total, 'orders' => $data_fees];
}

function get_transactions_alliances($start, $end, $id = "", $status = 0)
{
    global $wpdb;
    $data_fees = [];
    $total = 0.00;
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $alliance_id = get_user_meta(get_current_user_id(), 'alliance_id', true);

    if (empty($alliance_id)) {
        $alliance_id = $_GET['alliance_id'];
    }

    if (!empty($id)) {
        $alliance_id = $id;
    }

    if (empty($alliance_id)) {
        return [];
    }

    $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
    $start_date = date('Y-m-d H:i:s', $strtotime_start);
    $end_date = date('Y-m-d H:i:s', $strtotime_end);
    $transactions = $wpdb->get_results("SELECT * FROM {$table_alliances_payments} 
                                    WHERE alliance_id={$alliance_id} 
                                    AND status_id={$status} 
                                    AND created_at BETWEEN '{$start_date}' AND '{$end_date}'");

    foreach ($transactions as $order) {
        array_push($data_fees, [
            'status' => get_status_payment_institute($order->status_id),
            'month' => $order->month,
            'amount' => $order->amount,
            'total_orders' => $order->total_orders,
        ]);

        $total += $order->amount;
    }

    return ['total' => $total, 'orders' => $data_fees];
}

class TT_All_Institute_Students_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'student':
                $student = $item['last_name'] . ' ' . $item['middle_last_name'] . ' ' . $item['name'] . ' ' . $item['middle_name'];
                return $student;
            case 'initial':
                $initial = $item['academic_period'] . ' - ' . $item['initial_cut'];
                return $initial;
            default:
                return $item[$column_name];
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
            'email' => __('Email', 'edusystem'),
            'initial' => __('Initial term', 'edusystem'),
            'institute_name' => __('Institute', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
        );

        return $columns;
    }

    function get_students()
    {
        global $wpdb, $current_user;

        // PAGINACIÓN
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = ($pagenum - 1) * $per_page;

        // Tablas
        $table_students = $wpdb->prefix . 'students';
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_alliances = $wpdb->prefix . 'alliances';
        $email = $current_user->user_email;

        // Si no se tiene email, se retorna un conjunto vacío
        if (empty($email)) {
            return ['data' => [], 'total_count' => 0];
        }

        /* 
         * Realizamos un único query que une las tres tablas.
         * Se asume que:
         * - En "alliances" existe una columna "email" que relaciona la alianza con el usuario actual.
         * - "institutes" tiene la columna "alliance_id" para relacionarse con "alliances".
         * - "students" tiene la columna "institute_id" para relacionarse con "institutes".
         */
        $sql = "SELECT SQL_CALC_FOUND_ROWS s.*, i.name AS institute_name
            FROM {$table_students} AS s
            INNER JOIN {$table_institutes} AS i ON s.institute_id = i.id
            INNER JOIN {$table_alliances} AS a ON i.alliance_id = a.id
            WHERE a.email = %s
            ORDER BY s.id DESC
            LIMIT %d OFFSET %d";

        $prepared_sql = $wpdb->prepare($sql, $email, $per_page, $offset);
        $students = $wpdb->get_results($prepared_sql, ARRAY_A);

        // Obtenemos el total de resultados sin el LIMIT
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        return ['data' => $students, 'total_count' => $total_count];
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

        $data_get = $this->get_students();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_get['data'];
        $total_count = (int) $data_get['total_count'];

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