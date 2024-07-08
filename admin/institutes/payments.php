<?php 

function list_admin_institutes_payments_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'payment-detail'){

            global $current_user;
            $roles = $current_user->roles;
            $order = wc_get_order($_GET['payment_id']);
            include(plugin_dir_path(__FILE__).'../templates/payment-details.php');
        }

    }else{

        global $current_user;
        $roles = $current_user->roles;
        $date = get_dates_search('today','');
        $start_date = date('m/d/Y',strtotime('today'));
        $orders = get_order_institutes($date[0],$date[1]);
        include(plugin_dir_path(__FILE__).'../templates/list-payments-institutes.php');
    }
}

class TT_all_Payment_Institutes_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'payment',    
            'plural'    => 'payments',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'order_id':
                $order_id = '#'.$item['order_id'];
                return $order_id;
            case 'fee':
                return get_woocommerce_currency_symbol().number_format($item[$column_name],2,'.',',');
            case 'created_at':   
                return $item[$column_name];
            case 'view_details':
                if(isset($_GET['institute_id']) && !empty($_GET['institute_id'])){
                    return "<a class='button button-primary' href='".admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id='.$item['order_id'])."'>".__('View details','aes')."</a>";
                }else{
                    return "<a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id='.$item['order_id'])."'>".__('View details','aes')."</a>";
                }
			default:
				return print_r($item,true);
        }
    }

	function column_name($item){
        return ucwords($item['name'].' '.$item['last_name']);
    }

	function column_cb($item){
        return '';
    }

	function get_columns(){

        $columns = array(
            'order_id'     => __('Payment ID','aes'),
            'fee'          => __('Fee','aes'),
            'created_at'   => __('Created at','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

   

	function get_sortable_columns() {
        $sortable_columns = [];
        return $sortable_columns;
    }

	
	function get_bulk_actions() {
        $actions = [];
        return $actions;
    }

	function process_bulk_action(){
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }  
    }

	function prepare_items(){

        if(isset($_POST['institute_id']) && !empty($_POST['institute_id'])){
            $data_payment = get_order_institutes($_GET['institute_id']);
        }else{
            $data_payment = get_order_institutes();
        }

		$per_page = 100;

		  
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_payment;

		function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
            $result = strcmp($a[$orderby], $b[$orderby]); 
            return ($order==='asc') ? $result : -$result;
        }
       
		$current_page = $this->get_pagenum();
    
        $total_items = count($data);
		
        $this->items = $data;
	}

    function extra_tablenav( $which ) {
        if ( $which == "top" ){
            ?>
            <div class="alignleft actions bulkactions">
                <select name="filter-date" id="filter-date">
                    <option value=""><?= __('All Payments','aes'); ?></option>
                    <option value="today"><?= __('Today','aes'); ?></option>
                    <option value="yesterday"><?= __('Yesterday','aes'); ?></option>
                    <option value="this-week"><?= __('This week','aes'); ?></option>
                    <option value="last-week"><?= __('Last Week','aes'); ?></option>
                    <option value="this-month"><?= __('This Month','aes'); ?></option>
                    <option value="last-month"><?= __('Last Month','aes'); ?></option>
                    <option value="custom"><?= __('Custom','aes'); ?></option>
                </select>
                <input type="text" id="input-date" style="display:none;">
                <input type="submit" class="button" value="<?= __('Search','aes'); ?>">
            </div>
                <?php
        }
    }

}

function get_order_institutes($start,$end,$id = ""){

    $data_fees = [];
    $total = 0.00;
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $institute_id = get_user_meta(get_current_user_id(),'institute_id',true);
    

    if(empty($institute_id)){
        $institute_id = $_GET['institute_id'];
    }

    if(!empty($id)){
        $institute_id = $id;
    }

    if(empty($institute_id)){
        return [];
    }

    $args['institute_id'] = $institute_id;
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start.'...'.$strtotime_end;
    $orders = wc_get_orders($args);


    foreach($orders as $order){
    
        if($order->get_meta('institute_id') == $institute_id){

            array_push($data_fees,[
                'order_id' => $order->get_id(),  
                'customer' => $order->get_billing_first_name().' '.$order->get_billing_last_name(),
                'fee' => $order->get_meta('institute_fee'),
                'created_at' => $order->get_date_created()->format('F j, Y g:i a')
            ]);

            $total += $order->get_meta('institute_fee');
        }
    }

    return ['total' => number_format($total,2,'.',','),'orders' => $data_fees];
}   

function handle_custom_query_meta_institute( $query, $query_vars ) {
	if(!empty( $query_vars['institute_id'])){
		$query['meta_query'][] = array(
			'key' => 'institute_id',
			'value' => esc_attr( $query_vars['institute_id'] ),
		);
	}

	return $query;
}

add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'handle_custom_query_meta_institute', 10, 2 );

function get_list_fee_institute(){

    if(isset($_POST['filter']) && !empty($_POST['filter'])){

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];
        $institute_id = $_POST['institute_id'];
        
        $html = "";
        $dates = get_dates_search($filter,$custom);
        $orders =  get_order_institutes($dates[0],$dates[1],$institute_id);

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

                if(!empty($institute_id)){
                    $html .= "<a class='button button-primary' href='".admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id='.$order['order_id'])."'>".__('View details','aes')."</a>";
                }else{
                    $html .= "<a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id='.$order['order_id'])."'>".__('View details','aes')."</a>";
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

add_action( 'wp_ajax_nopriv_list_fee_institute', 'get_list_fee_institute');
add_action( 'wp_ajax_list_fee_institute', 'get_list_fee_institute');