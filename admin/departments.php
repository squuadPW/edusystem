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
    $list_students = new TT_register_department_List_Table;
    $list_students->prepare_items();
    include(plugin_dir_path(__FILE__).'templates/register-departments.php');
}

class TT_register_department_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'student',    
            'plural'    => 'students',
            'ajax'      => true
        ) );
        
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'full_name':
                return $item['name'].' '.$item['last_name'];
            case 'program':
                $program = match($item['program_id']){
                    'aes' => __('AES (Dual Diploma)','form-plugin'),
                    'psp' => __('PSP (Carrera Universitaria)','form-plugin'),
                    'aes_psp' => __('AES (Dual Diploma)','form-plugin').','.__('AES (Dual Diploma)','form-plugin'),
                };

                return $program;
            case 'grade':   
                $grade = match($item['grade_id']){
                    '1' => __('9no (antepenúltimo)','form-plugin'),
                    '2' => __('10mo (penúltimo)','form-plugin'),
                    '3' => __('11vo (último)','form-plugin'),
                    '4' => __('Bachiller (graduado)','form-plugin')
                };

                return $grade;
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
            'Name'     => __('Full name','form-plugin'),
            'Description'     => __('Program','form-plugin'),
            'Created at' => __('Grade','form-plugin'),
            'view_details' => __('Actions','form-plugin'),
        );

        return $columns;
    }

    function get_students(){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';

        $data = $wpdb->get_results("SELECT * FROM {$table_students} ORDER BY name ASC","ARRAY_A");
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

		$data_categories = $this->get_students();

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