<?php

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
        $alliance_id = $_POST['alliance_id'];
        
        $html = "";
        $dates = get_dates_search($filter,$custom);
        $orders = get_order_alliance($dates[0],$dates[1],$alliance_id);

        if(!empty($orders['orders'])){

            foreach($orders['orders'] as $order){
                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='".__('Payment ID','aes')."'>";
                $html .= '#'.$order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='".__('Customer','restaurant-system-app')."'>".$order['customer']."</td>";
                $html .= "<td class='column' data-colname='".__('Fee','restaurant-system-app')."'>".get_woocommerce_currency_symbol().number_format($order['fee'],2,'.',',')."</td>";
                $html .= "<td class='column' data-colname='".__('Created','restaurant-system-app')."'><b>".$order['created_at']."</b></td>";
                $html .= "<td class='column' data-colname='".__('Action','restaurant-system-app')."'>";
                if(isset($_GET['institute_id']) && !empty($_GET['institute_id'])) {
                    $html .= "<a class='button button-primary' href=". admin_url('admin.php?page=add_admin_partners_content&section_tab=payment-detail&payment_id='.$order['order_id']) .">".__('View details','aes')."</a>";
                } else {
                    $html .= "<a class='button button-primary' href=". admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id='.$order['order_id']) .">".__('View details','aes')."</a>";
                }
                $html .= "</td>";
                $html .= "</tr>";
            }

        }else{
            $html .= "<tr>";
            $html .= "<td colspan='5' style='text-align:center;'>".__('There are not records','aes')."</td>";
            $html .= "</tr>";
        }
        
        echo json_encode(['status' => 'success','html' => $html,'data' => $orders]);
        exit;
    }
}

add_action( 'wp_ajax_nopriv_list_fee_alliance', 'get_list_fee_alliance');
add_action( 'wp_ajax_list_fee_alliance', 'get_list_fee_alliance');