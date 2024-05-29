<?php

function add_admin_institutes_content(){

    if(isset($_POST['section_tab']) && !empty($_POST['section_tab'])){

        if($_POST['section_tab'] == 'all_institutes'){
            $list_institutes = new TT_institutes_List_Table;
            $list_institutes->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-institutes.php');
        }

    }else{
        $list_institutes = new TT_institutes_review_List_Table;
        $list_institutes->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-institutes.php');
    }
}

function get_name_reference($reference_id){

    $reference = match($reference_id){
        '1' => __('Facebook','aes'),
        '2' => __('Instagram','aes'),
        '3' => __('Email','aes'),
        '4' => __('Busqueda por internet','aes'),
        '5' => __('Evento Presencial','aes'),
        default => '',
    };

    return $reference;
}

class TT_institutes_review_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'institute_review',    
            'plural'    => 'institute_reviews',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'name':
            case 'phone':
            case 'email':
            case 'country':
                return $item[$column_name];
            case 'name_rector':
                return $item['name_rector'].' '.$item['lastname_rector'];
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id='.$item['id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
			default:
				return print_r($item,true);
        }
    }

	function column_name($item){

        return sprintf('%1$s',
            ucwords($item['name']),
        );
    }

	function column_cb($item){
        return '';
    }

	function get_columns(){

        $columns = array(
            'name'         => __('Name','aes'),
            'phone'         => __('Phone','aes'),
            'email'         => __('Email','aes'),
            'country'         => __('Country','aes'),
            'name_rector'   => __('Name Rector','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_list_institutes_review(){
        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institutes';

        $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 0","ARRAY_A");
        return $institutes;
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

		$data_institutes = $this->get_list_institutes_review();
		$per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_institutes;

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

class TT_institutes_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'institute_review',    
            'plural'    => 'institute_reviews',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'Name':
                return '#'.$item[$column_name];
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
            'name'         => __('Name','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_list_institutes(){

        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institute';

        $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes}","ARRAY_A");

        return $institutes;
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

		$data_institutes = $this->get_list_institutes();
		$per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_institutes;

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