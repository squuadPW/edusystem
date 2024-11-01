<?php

function list_admin_institutes_invoice_content()
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
        $orders = get_order_institutes($date[0], $date[1]);
        include(plugin_dir_path(__FILE__) . '../templates/list-invoice-institutes.php');
    }
}

function list_admin_institutes_payments_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'payment-detail') {

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
        $orders = get_order_institutes($date[0], $date[1]);
        include(plugin_dir_path(__FILE__) . '../templates/list-payments-institutes.php');
    }
}

class TT_all_Payment_Institutes_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'payment',
            'plural' => 'payments',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'order_id':
                $order_id = '#' . $item['order_id'];
                return $order_id;
            case 'fee':
                return get_woocommerce_currency_symbol() . number_format($item[$column_name], 2, '.', ',');
            case 'created_at':
                return $item[$column_name];
            case 'view_details':
                if (isset($_GET['institute_id']) && !empty($_GET['institute_id'])) {
                    return "<a class='button button-primary' href='" . admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id=' . $item['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                } else {
                    return "<a class='button button-primary' href='" . admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id=' . $item['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                }
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
            'order_id' => __('Payment ID', 'aes'),
            'fee' => __('Fee', 'aes'),
            'created_at' => __('Created at', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
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

        if (isset($_GET['institute_id']) && !empty($_GET['institute_id'])) {
            $dates = get_dates_search('this-month', null);
            $data_payment = get_order_institutes($dates[0], $dates[1],$_GET['institute_id']);
        } else {
            $data_payment = get_order_institutes();
        }

        $per_page = 100;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_payment;

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

    function extra_tablenav($which)
    {
        if ($which == "top") {
            ?>
            <div class="alignleft actions bulkactions">
                <select name="filter-date" id="filter-date">
                    <option value=""><?= __('All Payments', 'aes'); ?></option>
                    <option value="today"><?= __('Today', 'aes'); ?></option>
                    <option value="yesterday"><?= __('Yesterday', 'aes'); ?></option>
                    <option value="this-week"><?= __('This week', 'aes'); ?></option>
                    <option value="last-week"><?= __('Last Week', 'aes'); ?></option>
                    <option value="this-month"><?= __('This Month', 'aes'); ?></option>
                    <option value="last-month"><?= __('Last Month', 'aes'); ?></option>
                    <option value="custom"><?= __('Custom', 'aes'); ?></option>
                </select>
                <input type="text" id="input-date" style="display:none;">
                <input type="submit" class="button" value="<?= __('Search', 'aes'); ?>">
            </div>
            <?php
        }
    }

}

function get_order_institutes($start, $end, $id = "")
{

    $data_fees = [];
    $total = 0.00;
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $institute_id = get_user_meta(get_current_user_id(), 'institute_id', true);


    if (empty($institute_id)) {
        $institute_id = $_GET['institute_id'];
    }

    if (!empty($id)) {
        $institute_id = $id;
    }

    if (empty($institute_id)) {
        return [];
    }

    $args['institute_id'] = $institute_id;
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);


    foreach ($orders as $order) {

        if ($order->get_meta('institute_id') == $institute_id) {

            array_push($data_fees, [
                'order_id' => $order->get_id(),
                'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'fee' => $order->get_meta('institute_fee'),
                'created_at' => $order->get_date_created()->format('F j, Y g:i a')
            ]);

            $total += $order->get_meta('institute_fee');
        }
    }

    return ['total' => wc_price($total), 'orders' => $data_fees];
}

function get_invoices_institutes($start, $end, $id = "")
{

    $data_fees = [];
    $total = 0.00;
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $institute_id = get_user_meta(get_current_user_id(), 'institute_id', true);


    if (empty($institute_id)) {
        $institute_id = $_GET['institute_id'];
    }

    if (!empty($id)) {
        $institute_id = $id;
    }

    if (empty($institute_id)) {
        return [];
    }

    $args['institute_id'] = $institute_id;
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);


    foreach ($orders as $order) {

        if ($order->get_meta('institute_id') == $institute_id) {

            array_push($data_fees, [
                'order_id' => $order->get_id(),
                'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'fee' => $order->get_meta('institute_fee'),
                'created_at' => $order->get_date_created()->format('F j, Y g:i a')
            ]);

            $total += (float)$order->get_meta('institute_fee');
        }
    }

    return ['total' => $total, 'orders' => $data_fees];
}

function get_transactions_institutes($start, $end, $id = "", $status = 0)
{
    global $wpdb;
    $data_fees = [];
    $total = 0.00;
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $institute_id = get_user_meta(get_current_user_id(), 'institute_id', true);

    if (empty($institute_id)) {
        $institute_id = $_GET['institute_id'];
    }

    if (!empty($id)) {
        $institute_id = $id;
    }

    if (empty($institute_id)) {
        return [];
    }

    $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
    $start_date = date('Y-m-d H:i:s', $strtotime_start);
    $end_date = date('Y-m-d H:i:s', $strtotime_end);
    $transactions = $wpdb->get_results("SELECT * FROM {$table_institutes_payments} 
                                    WHERE institute_id={$institute_id} 
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

function get_status_payment_institute($id) {
    $status = '';
    switch ($id) {
        case 0:
            $status = 'Pending';
            break;
        
        default:
            $status = 'Paid';
            break;
    }

    return $status;
}

function handle_custom_query_meta_institute($query, $query_vars)
{
    if (!empty($query_vars['institute_id'])) {
        $query['meta_query'][] = array(
            'key' => 'institute_id',
            'value' => esc_attr($query_vars['institute_id']),
        );
    }

    return $query;
}

add_filter('woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_meta_institute', 10, 2);

function get_list_fee_institute()
{

    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];
        $institute_id = $_POST['institute_id'];

        $html = "";
        $dates = get_dates_search($filter, $custom);
        $orders = get_order_institutes($dates[0], $dates[1], $institute_id);

        if (!empty($orders['orders'])) {

            foreach ($orders['orders'] as $order) {
                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'aes') . "'>";
                $html .= '#' . $order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>" . $order['customer'] . "</td>";
                $html .= "<td class='column' data-colname='" . __('Fee', 'restaurant-system-app') . "'>" . get_woocommerce_currency_symbol() . number_format($order['fee'], 2, '.', ',') . "</td>";
                $html .= "<td class='column' data-colname='" . __('Created', 'restaurant-system-app') . "'><b>" . $order['created_at'] . "</b></td>";
                $html .= "<td class='column' data-colname='" . __('Action', 'restaurant-system-app') . "'>";

                if (!empty($institute_id)) {
                    $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                } else {
                    $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                }


                $html .= "</td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='5' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html .= "</tr>";
        }

        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_fee_institute', 'get_list_fee_institute');
add_action('wp_ajax_list_fee_institute', 'get_list_fee_institute');


function get_invoices_institute()
{

    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        global $current_user;
        $roles = $current_user->roles;
        $filter = $_POST['filter'];
        $custom = $_POST['custom'];
        $institute_id = $_POST['institute_id'];

        $html = "";
        $html_transactions = "";
        $dates = get_dates_search($filter, $custom);
        $current_invoice_date = get_dates_search('this-month', null);
        $filtered_invices = get_invoices_institutes($dates[0], $dates[1], $institute_id);
        $current_invoice = get_invoices_institutes($current_invoice_date[0], $current_invoice_date[1], $institute_id);
        $transactions_pending = get_transactions_institutes($dates[0], $dates[1], $institute_id, 0);
        $transactions_complete = get_transactions_institutes($dates[0], $dates[1], $institute_id, 1);

        if (!empty($filtered_invices['orders'])) {

            foreach ($filtered_invices['orders'] as $order) {
                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'aes') . "'>";
                $html .= '#' . $order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>" . $order['customer'] . "</td>";
                $html .= "<td class='column' data-colname='" . __('Fee', 'restaurant-system-app') . "'>" . wc_price($order['fee']) . "</td>";
                $html .= "<td class='column' data-colname='" . __('Created', 'restaurant-system-app') . "'><b>" . $order['created_at'] . "</b></td>";
                $html .= "<td class='column' data-colname='" . __('Action', 'restaurant-system-app') . "'>";

                if (!$institute_id) {
                    $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                } else {
                    if (in_array('alliance', $roles)) {
                        $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                    } else {
                        $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";
                    }
                }


                $html .= "</td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='5' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html .= "</tr>";
        }

        $transactions['total_pending'] = wc_price($transactions_pending['total']);
        $transactions['total_paid'] = wc_price($transactions_complete['total']);
        $transactions['total'] = wc_price(($transactions_complete['total'] + $transactions_pending['total']));
        $transactions['orders'] = array_merge($transactions_pending['orders'], $transactions_complete['orders']);
        if (!empty($transactions['orders'])) {

            foreach ($transactions['orders'] as $order) {
                $html_transactions .= "<tr>";
                $html_transactions .= "<td class='column column-primary' data-colname='" . __('Status', 'aes') . "'>";
                $html_transactions .= $order['status'];
                $html_transactions .= "</td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Month', 'restaurant-system-app') . "'><b>" . $order['month'] . "</b></td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Amount', 'restaurant-system-app') . "'>" . wc_price($order['amount']) . "</td>";
                $html_transactions .= "<td class='column' data-colname='" . __('Total orders', 'restaurant-system-app') . "'><b>" . $order['total_orders'] . "</b></td>";
                $html_transactions .= "</tr>";
            }

        } else {
            $html_transactions .= "<tr>";
            $html_transactions .= "<td colspan='4' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html_transactions .= "</tr>";
        }

        $current_invoice['total'] = wc_price($current_invoice['total']);
        $filtered_invices['total'] = wc_price($filtered_invices['total']);
        echo json_encode(['status' => 'success', 'html' => $html, 'html_transactions' => $html_transactions, 'data' => $filtered_invices, 'current_invoice' => $current_invoice, 'transactions' => $transactions]);
        exit;
    }
}

add_action('wp_ajax_nopriv_invoices_institute', 'get_invoices_institute');
add_action('wp_ajax_invoices_institute', 'get_invoices_institute');