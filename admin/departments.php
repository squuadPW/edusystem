<?php

function list_admin_form_department_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        global $wpdb;
        $table_departments = $wpdb->prefix.'departments';

        if($_GET['action'] == 'add'){

            $departments_subscription = get_option('site_departments_subscription') ? json_decode(get_option('site_departments_subscription')) : [];
            include(plugin_dir_path(__FILE__).'templates/register-departments.php');

        }else if($_GET['action'] == 'add_department'){

            $name = strtolower($_POST['name']);
            $description = $_POST['description'];
            $capabilities = $_POST['capabilities'];
            $department_id = $_POST['department_id'];

            $role_name = str_replace('','_',$name);
            $cap = [];
            
            if(isset($_POST['capabilities']) && !empty($_POST['capabilities'])){
                
                foreach($capabilities as $capability){
                    $cap[$capability] = true;
                }
            }
            //update
            if(isset($_POST['department_id']) && !empty($_POST['department_id'])){

                $role = get_role($role_name);

                $current_capabilities = $role->capabilities;
                if($current_capabilities){

                    foreach($current_capabilities as $index => $current){

                        $role->remove_cap($index);
                    }
                }

                foreach($cap as $index =>  $c){
                    $role->add_cap($index);
                }

                $role->add_cap('edit_posts');
                $role->add_cap('manage_options');
                $role->add_cap('read');
                
                if (in_array('manager_users_aes', $capabilities)) {
                    $role->add_cap('create_users');
                    $role->add_cap('delete_users');
                    $role->add_cap('edit_users');
                    $role->add_cap('list_users');
                    $role->add_cap('promote_users');
                    $role->add_cap('remove_users');
                }

                if (in_array('manager_media_aes', $capabilities)) {
                    // Capacidades para subir y gestionar medios
                    $role->add_cap('upload_files');
                    $role->add_cap('edit_posts');
                    $role->add_cap('delete_posts');
                    $role->add_cap('edit_others_posts');
                    $role->add_cap('delete_others_posts');
                }

                $wpdb->update($table_departments,['description' => $description],['id' => $department_id]);

                $message_success = __('Department updated','edusystem');
                setcookie('message_success',$message_success,time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=add_admin_department_content&action=edit&department_id='.$department_id));
                exit;
            }

            //create

            if(wp_roles()->is_role($role_name)){
                
                $message = __('Existing department.','edusystem');
                $departments_subscription = get_option('site_departments_subscription') ? json_decode(get_option('site_departments_subscription')) : [];
                include(plugin_dir_path(__FILE__).'templates/register-departments.php');
                exit;
            }

                $wpdb->insert($table_departments,[
                    'name' => strtolower($name),
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $object_role = add_role($role_name,ucwords(strtolower($name)),$cap);
                $object_role->add_cap('edit_posts');
                $object_role->add_cap('manage_options');
                $object_role->add_cap('read');
    
            wp_redirect(admin_url('admin.php?page=add_admin_department_content'));
            exit;


        }else if($_GET['action'] == 'edit'){

            $department_id = $_GET['department_id'];
            $department = get_department($department_id);
            $name = $department->name;
            $description = $department->description;
            $role = get_role(str_replace('','_',$name));
            $name = ucwords($department->name);
            $capabilities = $role->capabilities;
            $departments_subscription = get_option('site_departments_subscription') ? json_decode(get_option('site_departments_subscription')) : [];
            include(plugin_dir_path(__FILE__).'templates/register-departments.php');

        }else if($_GET['action'] == 'delete'){

            $department_id = $_POST['delete_department_id'];
            $data = $wpdb->get_row("SELECT * FROM {$table_departments} WHERE id={$department_id}");
            $wpdb->delete($table_departments,['id' => $department_id]);

            remove_role(str_replace('','_',$data->name));

            wp_redirect(admin_url('admin.php?page=add_admin_department_content'));
            exit;
        }

    }else{

        $list_departments = new TT_all_departments_List_Table;
        $list_departments->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-departments.php');
    }
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
                $datetime = Datetime::createFromFormat('Y-m-d H:i:s',$item['created_at']);

                return $datetime->format('F j, Y');
            case 'view_details':
                return "<a href='".admin_url('admin.php?page=add_admin_department_content&action=edit&department_id='.$item['id'])."' class='button button-primary'>".__('Edit','edusystem')."</a>";
			default:
				return print_r($item,true);
        }
    }

	function column_name($item){
        return ucwords($item['name']);
    }

	function column_cb($item){
        return '';
    }

	function get_columns(){

        $columns = array(
            'name'     => __('Name','edusystem'),
            'description'     => __('Description','edusystem'),
            'created_at' => __('Created at','edusystem'),
            'view_details' => __('Actions','edusystem'),
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

		$per_page = 100;

		  
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

function get_department($id){

    global $wpdb;
    $table_departments = $wpdb->prefix. 'departments';

    $data = $wpdb->get_row("SELECT * FROM {$table_departments} WHERE id={$id}");
    return $data;
}