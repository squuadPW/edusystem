<?php

function add_admin_institutes_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'change_status_institute'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';

            $institute_id = $_POST['change_status_institute_id'];
            $status_id = $_POST['change_status_id'];

            $wpdb->update($table_institutes,[
                'status' => $status_id,
                'updated_at' => date('Y-m-d H:i:s')
            ],['id' => $institute_id]);

            if($status_id == 1){
                $email_approved_institute = WC()->mailer()->get_emails()['WC_Approved_Institution_Email'];
                $email_approved_institute->trigger($institute_id);

                $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
                create_user_institute($data_institute);          
            }else if($status_id == 2){
                $email_rejected_institute = WC()->mailer()->get_emails()['WC_Rejected_Institution_Email'];
                $email_rejected_institute->trigger($institute_id);
            }

            wp_redirect(admin_url('admin.php?page=add_admin_institutes_content'));
            exit;
        }

        if($_GET['action'] == 'save_institute_details'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';
            $institute_id = $_POST['institute_id'];

            $name = $_POST['name'];
            $phone = $_POST['phone_hidden'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $level = $_POST['level'];
            $fee = str_replace('%','',$_POST['fee']);
            $rector_name = $_POST['rector_name'];
            $rector_last_name = $_POST['rector_last_name'];
            $rector_phone = $_POST['rector_phone_hidden'];
            $address = $_POST['address'];
            $description = $_POST['description'];
            $reference = $_POST['reference'];
            $business_name = $_POST['business_name'];

            //update
            if(isset($institute_id) && !empty($institute_id)){

                $wpdb->update($table_institutes,[
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'level_id' => $level,
                    'fee' => $fee,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_last_name,
                    'phone_rector' => $rector_phone,
                    'address' => $address,
                    'description' => $description,
                    'business_name' => $business_name,
                    'updated_at' => date('Y-m-d H:i:s')
                ],[ 'id' => $institute_id]);
                
                setcookie('message',__('Changes saved successfully.','aes'),time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id='.$institute_id));
                exit;

                
            //insert
            }else{

                $user = get_user_by('email',$email);

                if(!$user){

                    $wpdb->insert($table_institutes,[
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'country' => $country,
                        'state' => $state,
                        'city' => $city,
                        'level_id' => $level,
                        'fee' => $fee,
                        'name_rector' => $rector_name,
                        'lastname_rector' => $rector_last_name,
                        'phone_rector' => $rector_phone,
                        'reference' => $reference,
                        'address' => $address,
                        'description' => $description,
                        'business_name' => $business_name,
                        'status' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $institute_id = $wpdb->insert_id;
                    $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
                    create_user_institute($data_institute); 

                    $email_approved_institute = WC()->mailer()->get_emails()['WC_Approved_Institution_Email'];
                    $email_approved_institute->trigger($institute_id);
                    setcookie('message',$name,time() + 3600,'/');
                    wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'));
                    exit;

                }else{
                    setcookie('message-error',__( 'Existing email, please enter another email', 'aes' ),time() + 3600,'/');
                    wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=add_institute'));
                    exit;
                }

            }
        }

        if($_GET['action'] == 'delete_institute'){

            global $wpdb;
            $table_institutes =  $wpdb->prefix.'institutes';

            $institute_id = $_POST['delete_institute_id'];

            $data_institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");

            $user = get_user_by('email',$data_institute->email);
            
            if($user){
                wp_delete_user($user->ID);
            }

            $wpdb->delete($table_institutes,['id' => $institute_id]);
            setcookie('message-delete',$data_institute->name,time() + 3600,'/');
            wp_redirect(admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'));
            exit;
        }
    }

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){

        if($_GET['section_tab'] == 'all_institutes'){
            $list_institutes = new TT_institutes_List_Table;
            $list_institutes->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-institutes.php');
        }

        if($_GET['section_tab'] == 'institute_details'){
            $institute_id =$_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'templates/institute-details.php');
        }  
        
        if($_GET['section_tab'] == 'fee_institute'){

            global $current_user;
            $roles = $current_user->roles;
            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $date = get_dates_search('today','');
            $start_date = date('m/d/Y',strtotime('today'));
            $orders = get_order_institutes($date[0],$date[1]);
            include(plugin_dir_path(__FILE__).'templates/list-payments-institutes.php');
        }

        if($_GET['section_tab'] == 'payment-detail'){
            
            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__).'templates/payment-details.php');
        }

        if($_GET['section_tab'] == 'add_institute'){
            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'templates/institute-details.php');
        }

    }else{
        $list_institutes = new TT_institutes_review_List_Table;
        $list_institutes->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-institutes.php');
    }
}

function get_name_status_institute($status_id){
    $status = match($status_id){
        '0' => __('Pending','aes'),
        '1' => __('Approved','aes'),
        '2' => __('Declined','aes'),
        default => '',
    };

    return $status;
}

function get_name_country($country_id){

    $countries = get_countries();
    $name = "";

    foreach($countries as $key => $country){
        if($key == $country_id){
            $name = $country;
        }
    }

    return $name;
}

function get_name_reference($reference_id){

    $reference = match($reference_id){
        '3' => __('Email','aes'),
        '4' => __('Internet search','aes'),
        '5' => __('On-site Event','aes'),
        default => '',
    };

    return $reference;
}

function get_institute_details($institute_id){

    global $wpdb;
    $table_institutes =  $wpdb->prefix.'institutes';

    $institute = $wpdb->get_row("SELECT * FROM {$table_institutes} WHERE id={$institute_id}");
    return $institute;
}

function get_name_level($level_id){

    $level = match($level_id){
        '1' => __('Primary','aes'),
        '2' => __('High School','aes'),
        default => "",
    };

    return $level;
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
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']).' '.ucwords($item['lastname_rector']);
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id='.$item['id'])."' class='button button-primary'><span class='dashicons dashicons-visibility'></span>".__('View','form-plugin')."</a>";
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
            'number_phone'         => __('Phone','aes'),
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

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $institutes = $wpdb->get_results("SELECT * 
                FROM {$table_institutes} WHERE 
                status = 0  AND 
                ( name LIKE '{$search}%' || 
                email LIKE '{$search}%' || 
                name_rector LIKE '{$search}%' || 
                lastname_rector LIKE '{$search}%')"
                
                ,"ARRAY_A");

        }else{
            $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 0","ARRAY_A");
        }

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
            case 'name':
                return ucwords($item[$column_name]);
            case 'number_phone':
                return $item['phone'];
            case 'email':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']).' '.ucwords($item['lastname_rector']);
                $status = get_name_status_institute($item[$column_name]);
                return $status;
            case 'view_details':
                return "
                    <a style='margin:3px;' href='".admin_url('/admin.php?page=add_admin_institutes_content&section_tab=fee_institute&institute_id='.$item['id'])."' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>".__('Fees','form-plugin')."</a>
                    <a style='margin:3px;' href='".admin_url('/admin.php?page=add_admin_institutes_content&section_tab=institute_details&institute_id='.$item['id'])."' class='button button-primary'><span class='dashicons dashicons-edit'></span>".__('Edit','form-plugin')."</a>
                ";
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
            'number_phone'         => __('Phone','aes'),
            'email'         => __('Email','aes'),
            'country'         => __('Country','aes'),
            'name_rector'   => __('Name Rector','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_list_institutes(){

        global $wpdb;
        $table_institutes =  $wpdb->prefix.'institutes';

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $institutes = $wpdb->get_results("SELECT * 
                FROM {$table_institutes}
                WHERE status=1
                AND 
                ( name LIKE '{$search}%' ||
                   email LIKE '{$search}%' || 
                   name_rector LIKE '{$search}%' || 
                   lastname_rector LIKE '{$search}%'
                )",'ARRAY_A');

        }else{
            $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status=1" ,"ARRAY_A");
        }

       

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

function create_user_institute($institute){

    $user = get_user_by('email',$institute->email);

    if(!$user){

        $password = generate_password_user();
    
        $userdata = [
            'user_login' => $institute->email,
            'user_pass' => $password,
            'user_email' => $institute->email,
            'first_name' => $institute->name,
        ];

        $user_id = wp_insert_user($userdata);
        $user = new WP_User($user_id);
        $user->remove_role('subscriber');
        $user->add_role('institutes');

        update_user_meta($user_id,'institute_id',$institute->id);

        wp_new_user_notification($user_id, null, 'both' );
    }else{
        update_user_meta($user->id,'institute_id',$institute->id);
    }
}