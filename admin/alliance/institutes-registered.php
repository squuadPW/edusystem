<?php

function list_admin_institutes_partner_registered_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'institute-detail'){

            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'../templates/institute-details-alliance.php');

        }else if($_GET['action'] == 'add_institute'){
            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'../templates/institute-details-alliance.php');

        }else if($_GET['action'] == 'save_institute_details'){
            
            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';
            $institute_id = $_POST['institute_id'];

            $name = $_POST['name'];
            $phone = $_POST['phone_hidden'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $address = $_POST['address'];
            $city = $_POST['city'];
            $level = $_POST['level'];
            $fee = str_replace('%','',$_POST['fee']);
            $rector_name = $_POST['rector_name'];
            $rector_last_name = $_POST['rector_last_name'];
            $rector_phone = $_POST['rector_phone_hidden'];
            $reference = $_POST['reference'];

            //update
            if(isset($institute_id) && !empty($institute_id)){

                $wpdb->update($table_institutes,[
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'address' => $address,
                    'level_id' => $level,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_last_name,
                    'phone_rector' => $rector_phone,
                    'updated_at' => date('Y-m-d H:i:s')
                ],[ 'id' => $institute_id]);
                
                setcookie('message',__('Changes saved successfully.','aes'),time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$institute_id));
                exit;

            //insert
            }else{

                $wpdb->insert($table_institutes,[
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'address' => $address,
                    'level_id' => $level,
                    'fee' => 5,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_last_name,
                    'phone_rector' => $rector_phone,
                    'reference' => $reference,
                    'alliance_id' => get_user_meta(get_current_user_id(),'alliance_id',true),
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $institute_id = $wpdb->insert_id;
                $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
               
                wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content'));
                exit;
        
            }
        }else if($_GET['action'] == 'delete_institute'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';

            $institute_id = $_POST['delete_institute_id'];
            $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
            $user = get_user_by('email',$data_institute->email);
            
            if($user){
                wp_delete_user($user->ID);
            }

            $wpdb->delete($table_institutes,['id' => $institute_id]);

            wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content'));
            exit;
        }else if($_GET['action'] == 'fee-institute'){

            global $current_user;
            $roles = $current_user->roles;
            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $list_payment_institutes = new TT_all_Payment_Institutes_List_Table;
            $list_payment_institutes->prepare_items();
            include(plugin_dir_path(__FILE__).'../templates/list-payments-institutes.php');
        }

    }else{
        
        $list_institutes_alliance = new TT_all_Institutes_Alliance_List_Table;
        $list_institutes_alliance->prepare_items();
        include(plugin_dir_path(__FILE__).'../templates/list-institutes-alliance.php');
    }
}

class TT_all_Institutes_Alliance_List_Table extends WP_List_Table{

	function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'institute',    
            'plural'    => 'institutes',
            'ajax'      => true
        ) );
    }

	function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'name':
                return $item['name'].' '.$item['last_name'];
            case 'email':
                return $item['email'];
            case 'level':
                return get_name_level($item['level_id']);
            case 'status':
                return get_name_status_alliance($item['status']);
            case 'created_at':   
                $datetime = Datetime::createFromFormat('Y-m-d H:i:s',$item['created_at']);

                return $datetime->format('F j, Y');
            case 'view_details':
                if($item['status'] == 1){
                        return "
                        <a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=fee-institute&institute_id='.$item['id'])."'><span class='dashicons dashicons-money-alt'></span>".__('Fees','aes')."</a>
                        <a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$item['id'])."'><span class='dashicons dashicons-edit'></span>".__('Edit','aes')."</a>";
                }else{
                    return "<a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$item['id'])."'><span class='dashicons dashicons-edit'></span>".__('Edit','aes')."</a>";
                }
               
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
            'name'     => __('Name','aes'),
            'email'    => __('Email','aes'),
            'level'    => __('Level','aes'),
            'status'   => __('Status','aes'),
            'created_at' => __('Created at','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_institutes(){

        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institutes';
        $alliance_id = get_user_meta(get_current_user_id(),'alliance_id',true);

        if(isset($_GET['s']) && !empty($_GET['s'])){

            $search = $_GET['s'];

            $data = $wpdb->get_results("SELECT * FROM 
                {$table_institutes} 
                WHERE alliance_id={$alliance_id}
                AND 
                ( name LIKE '{$search}%' ||
                    email LIKE '{$search}%' || 
                    name_rector LIKE '{$search}%' || 
                    lastname_rector LIKE '{$search}%'
                )"
            ,"ARRAY_A");
            return $data;

        }else{

            $data = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE alliance_id={$alliance_id}","ARRAY_A");
            return $data;
        }     
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

		$data_categories = $this->get_institutes();

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