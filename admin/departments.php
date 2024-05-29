<?php

function list_admin_form_department_content(){
    $list_departments = new TT_all_departments_List_Table;
    $list_departments->prepare_items();
    include(plugin_dir_path(__FILE__).'templates/list-departments.php');
}

class TT_all_departments_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'department',    
            'plural'    => 'departments',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'name':
                return $item['name'];
            case 'description':
                return $item['description'];
            case 'created_at':   
                return $item['created_at'];
            case 'view_details':
                return "<a href='#' class='button button-primary'>".__('View Details','form-plugin')."</a>";
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
            'name'     => __('Name','form-plugin'),
            'description'     => __('Description','form-plugin'),
            'created_at' => __('Created at','form-plugin'),
            'view_details' => __('Actions','form-plugin'),
        );

        return $columns;
    }

    function get_departments(){

        global $wpdb;
        $table_departments = $wpdb->prefix.'departments';

        $data = $wpdb->get_results("SELECT * FROM {$table_departments} ORDER BY created_at ASC","ARRAY_A");
        return $data;
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

		$data_categories = $this->get_departments();

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

function add_admin_form_department_content(){
    include(plugin_dir_path(__FILE__).'templates/register-departments.php');
}

function save_departments() {
    // Check if the form has been submitted
    if (isset($_POST['action']) && $_POST['action'] == 'save_departments') {
        // Get the form data
        $name = sanitize_text_field($_POST['name']);
        $description = sanitize_textarea_field($_POST['description']);

        // Save the data to the database or perform any other desired action
        // For example, let's assume you want to save the data to a custom table
        global $wpdb;
        $table_name = $wpdb->prefix. 'departments';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'description' => $description
            )
        );

        wp_redirect(admin_url('admin.php?page=list_admin_form_department_content'));
        exit;
    }
}