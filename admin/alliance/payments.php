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

function list_admin_partner_payments_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'payment-detail'){

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__).'../templates/payment-details.php');
        }

    }else{

        global $current_user;
        $roles = $current_user->roles;
        $date = get_dates_search('today','');
        $start_date = date('m/d/Y',strtotime('today'));
        $orders = get_order_alliance($date[0],$date[1]);
        include(plugin_dir_path(__FILE__).'../templates/list-payment-alliance.php');
    }
}

function handle_custom_query_meta_alliance( $query, $query_vars ) {
	if(!empty( $query_vars['alliance_id'])){
		$query['meta_query'][] = array(
			'key' => 'alliance_id',
			'value' => esc_attr( $query_vars['alliance_id'] ),
		);
	}

	return $query;
}
add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_meta_alliance', 10, 2 );

function get_order_alliance($start_date,$end_date,$id = ""){

    $data_fees = [];
    $alliance_id = get_user_meta(get_current_user_id(),'alliance_id',true);
    $strtotime_start = strtotime($start_date);
    $strtotime_end = strtotime($end_date);
    $total = 0.00;

    if(isset($_GET['alliance_id']) && !empty($_GET['alliance_id'])){
        $alliance_id = $_GET['alliance_id'];
    }

    if(!empty($id)){
        $alliance_id = $id;
    }

    if(empty($alliance_id)){
        return [];
    }

    $args['alliance_id'] = $alliance_id;
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';    
    $args['date_created'] = $strtotime_start.'...'.$strtotime_end;
    $orders = wc_get_orders($args);
    
    foreach($orders as $order){

        if($order->get_meta('alliance_id') == $alliance_id){

            array_push($data_fees,[
                'order_id' => $order->get_id(),  
                'customer' => $order->get_billing_first_name().' '.$order->get_billing_last_name(),
                'fee' => $order->get_meta('alliance_fee'),
                'created_at' => $order->get_date_created()->format('F j, Y g:i a')
            ]);

            $total += $order->get_meta('alliance_fee');
        }
    }

    return ['total' => number_format($total,2,'.',','),'orders' => $data_fees];
} 

function get_list_fee_alliance(){

    if(isset($_POST['filter']) && !empty($_POST['filter'])){

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

                if($alliance_id) {
                    $html .= "<a class='button button-primary' href=". admin_url('admin.php?page=add_admin_partners_content&section_tab=payment-detail&payment_id='.$order['order_id']) .">".__('View details','aes')."</a>";
                } else {
                    $html .= "<a class='button button-primary' href=". admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id='.$order['order_id']) .">".__('View details','aes')."</a>";
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

add_action( 'wp_ajax_nopriv_list_fee_alliance', 'get_list_fee_alliance');
add_action( 'wp_ajax_list_fee_alliance', 'get_list_fee_alliance');

function get_invoices_alliances($start, $end, $id = ""){

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

    $args['alliance_id'] = $alliance_id;
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);


    foreach ($orders as $order) {

        if ($order->get_meta('alliance_id') == $alliance_id) {

            array_push($data_fees, [
                'order_id' => $order->get_id(),
                'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'fee' => $order->get_meta('alliance_fee'),
                'created_at' => $order->get_date_created()->format('F j, Y g:i a')
            ]);

            $total += $order->get_meta('alliance_fee');
        }
    }

    return ['total' => $total, 'orders' => $data_fees];
}

function get_transactions_alliances($start, $end, $id = "", $status = 0){
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