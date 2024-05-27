<?php

function add_admin_form_payments_content(){

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){


        if($_GET['section_tab'] == 'all_payments'){
            $list_payments = new TT_all_payments_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-payments.php');
        }else if($_GET['section_tab'] == 'order_detail'){
            $order_id = $_GET['order_id'];
            $order = wc_get_order($order_id);

            include(plugin_dir_path(__FILE__).'templates/payment-details.php');
        }

    }else{
        $list_payments = new TT_payment_pending_List_Table;
        $list_payments->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-payments.php');
    }
}


class TT_payment_pending_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'payment_pending',    
            'plural'    => 'payment_pendings',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'payment_id':
                return '#'.$item[$column_name];
            case 'date':
            case 'status':
            case 'payment_method':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>'.$item[$column_name].'</b>';
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id='.$item['payment_id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
			default:
				return print_r($item,true);
        }
    }

	function column_name($item){

        return sprintf('%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="'.$item['id'].'" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

	function column_cb($item){
        return '';
    }

	function get_columns(){

        $columns = array(
            'payment_id'     => __('Payment ID','aes'),
            'date'     => __('Date','aes'),
            'partner_name'  => __('Partner Name','aes'),
            'total' => __('Total','aes'),
            'payment_method' => __('Payment Method','aes'),
            'status' => __('Status','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_payment_pendings(){
        $orders_array = [];
        $args = [];
        $args['limit'] = -1;
        $args['status'] = array('wc-pending','wc-cancelled','wc-processing','wc-on-hold');
        $orders = wc_get_orders($args);

        if($orders){
            foreach($orders as $order){
                array_push($orders_array,[
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_paid()->format('F j, Y g:i a'),
                    'partner_name' =>  $order->get_billing_first_name().' '.$order->get_billing_last_name(),
                    'total' => get_woocommerce_currency_symbol().$order->get_total(),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        return $orders_array;
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

		$data_categories = $this->get_payment_pendings();

		$per_page = 10;

		  
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_categories;

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

}

class TT_all_payments_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'payment_pending',    
            'plural'    => 'payment_pendings',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'payment_id':
                return '#'.$item[$column_name];
            case 'date':
            case 'payment_method':
            case 'status':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>'.$item[$column_name].'</b>';
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id='.$item['payment_id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
			default:
				return print_r($item,true);
        }
    }

	function column_name($item){

        return sprintf('%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="'.$item['id'].'" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

	function column_cb($item){
        return '';
    }

	function get_columns(){

        $columns = array(
            'payment_id'     => __('Payment ID','aes'),
            'date'     => __('Date','aes'),
            'partner_name'  => __('Partner Name','aes'),
            'total' => __('Total','aes'),
            'payment_method' => __('Payment Method','aes'),
            'status' => __('Status','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_payment_pendings(){
        $orders_array = [];
        $args = [];
        $args['limit'] = -1;
        $args['status'] = array('wc-pending','wc-completed','wc-cancelled','wc-processing','wc-on-hold');
        $orders = wc_get_orders($args);

        if($orders){
            foreach($orders as $order){
                array_push($orders_array,[
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_paid()->format('F j, Y g:i a'),
                    'partner_name' =>  $order->get_billing_first_name().' '.$order->get_billing_last_name(),
                    'total' => get_woocommerce_currency_symbol().$order->get_total(),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        return $orders_array;
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

		$data_categories = $this->get_payment_pendings();

		$per_page = 10;

		  
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_categories;

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

}