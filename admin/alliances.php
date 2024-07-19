<?php 

function add_admin_partners_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'change_status_alliance'){

            global $wpdb;
            $table_alliances =  $wpdb->prefix.'alliances';

            $status_id = $_POST['status_id'];
            $alliance_id = $_POST['status_alliance_id'];

            $wpdb->update($table_alliances,[
                'status' => $status_id,
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'id' => $alliance_id
            ]);

            if($status_id == 1){
                $email_approved_alliance = WC()->mailer()->get_emails()['WC_Approved_Partner_Email'];
                $email_approved_alliance->trigger($alliance_id);

                $alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$alliance_id}");
                create_user_alliance($alliance);
            }else if($status_id == 2){
                $email_approved_alliance = WC()->mailer()->get_emails()['WC_Rejected_Partner_Email'];
                $email_approved_alliance->trigger($alliance_id);
            }

            wp_redirect(admin_url('admin.php?page=add_admin_partners_content'));
            exit;

        }else if($_GET['action'] == 'save_setting_alliance'){

            global $wpdb;
            $table_alliances =  $wpdb->prefix.'alliances';
            $alliance_id = $_POST['alliance_id'];

            $name = $_POST['name'];
            $last_name = $_POST['last_name'];
            $legal_name = $_POST['legal_name'];
            $code = $_POST['code'];
            $type = $_POST['type'];
            $email = $_POST['email'];
            $phone_hidden = $_POST['phone_hidden'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $address = $_POST['address'];
            $description = $_POST['description'];
            $fee = str_replace('%','',$_POST['fee']);

            if(isset($_POST['alliance_id']) && !empty($_POST['alliance_id'])){

                $wpdb->update($table_alliances,[
                    'code' => $code,
                    'type' => $type,
                    'name' => $name,
                    'last_name' => $last_name,
                    'name_legal' => $legal_name,
                    'phone' => $phone_hidden,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'address' => $address,
                    'description' => $description,
                    'fee' => $fee,
                    'updated_at' => date('Y-m-d H:i:s')
                ],[
                    'id' => $alliance_id
                ]);

                setcookie('message',__('Changes saved successfully.','aes'),time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id='.$alliance_id.'&message='.__('Changes saved successfully','aes')));
                exit;

            }else{

                $user = get_user_by('email',$email);

                if(!$user){

                    $wpdb->insert($table_alliances,[
                        'code' => $code,
                        'type' => $type,
                        'name' => $name,
                        'last_name' => $last_name,
                        'name_legal' => $legal_name,
                        'phone' => $phone_hidden,
                        'email' => $email,
                        'country' => $country,
                        'state' => $state,
                        'city' => $city,
                        'address' => $address,
                        'description    ' => $description   ,
                        'status' => 1,
                        'fee' => floatval($fee),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $alliance_id = $wpdb->insert_id;
                    $alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$alliance_id}");
                    create_user_alliance($alliance);

                    $email_approved_alliance = WC()->mailer()->get_emails()['WC_Approved_Partner_Email'];
                    $email_approved_alliance->trigger($alliance_id);
                    setcookie('message',$name.' '.$last_name,time() + 3600,'/');
                    wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'));
                    exit;

                }else{
                    setcookie('message-error',__( 'Existing email, please enter another email', 'aes' ),time() + 3600,'/');
                    wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=add_alliance','aes'));
                    exit;
                }
            }

            exit;

        }else if($_GET['action'] == 'delete_alliance'){

            global $wpdb;
            $table_alliances =  $wpdb->prefix.'alliances';
            $delete_alliance = $_POST['delete_alliance_id'];

            $data_alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$delete_alliance}");

            $wpdb->delete($table_alliances,['id' => $delete_alliance]);
            setcookie('message-delete',$data_alliance->name.' '.$data_alliance->last_name,time() + 3600,'/');
            wp_redirect(admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'));
            exit;
        }
    }

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){

        if($_GET['section_tab'] == 'all_alliances'){

            $list_alliances = new TT_alliances_List_Table;
            $list_alliances->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-alliances.php');

        }else if($_GET['section_tab'] == 'alliance_details'){
          
            $alliance_id = $_GET['alliance_id'];
            $alliance = get_alliance_detail($alliance_id);

            $list_alliances = new TT_alliances_List_Table;
            $list_alliances->prepare_items();
            $countries = get_countries();
            $institutes = get_institutes_from_alliance($alliance_id);
            include(plugin_dir_path(__FILE__).'templates/alliance-details.php');

        }else if($_GET['section_tab'] == 'add_alliance'){

            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'templates/alliance-details.php');

        }else if($_GET['section_tab'] == 'fee_alliance'){

            global $current_user;
            $roles = $current_user->roles;

            $alliance_id = $_GET['alliance_id'];
            $alliance = get_alliance_detail($alliance_id);
            $date = get_dates_search('today','');
            $start_date = date('m/d/Y',strtotime('today'));
            $orders = get_order_alliance($date[0],$date[1]);
            include(plugin_dir_path(__FILE__).'templates/list-payment-alliance.php');

        }else if($_GET['section_tab'] == 'payment-detail'){

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__).'templates/payment-details.php');
        }

    }else{  
        $list_alliances = new TT_alliances_review_List_Table;
        $list_alliances->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-alliances.php');
    }
}

function get_alliance_detail($alliance_id){

    global $wpdb;
    $table_alliances =  $wpdb->prefix.'alliances';

    $data = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$alliance_id}");
    return $data;
}

class TT_alliances_review_List_Table extends WP_List_Table{

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
            case 'full_name':
                return ucwords($item['name']).' '.ucwords($item['last_name']);
            case 'phone':
            case 'email':
            case 'state':
            case 'city':
                return $item[$column_name];
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']).' '.ucwords($item['lastname_rector']);
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id='.$item['id'])."' class='button button-primary'><span class='dashicons dashicons-visibility'></span>".__('View','form-plugin')."</a>";
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
            'full_name'     => __('Full name','aes'),
            'phone'         => __('Phone','aes'),
            'email'         => __('Email','aes'),
            'country'       => __('Country','aes'),
            'state'         => __('State','aes'),
            'city'          => __('City','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_list_alliances_review(){
        global $wpdb;
        $table_alliances =  $wpdb->prefix.'alliances';
        

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $alliances = $wpdb->get_results("SELECT * 
                    FROM {$table_alliances} WHERE 
                    status = 0 AND 
                    ( name LIKE '{$search}%' || 
                     email LIKE '{$search}%' || 
                     last_name LIKE '{$search}%' || 
                     name_legal LIKE '{$search}%')"
                ,"ARRAY_A");

        }else{
            $alliances = $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 0","ARRAY_A");
        }
    
        return $alliances;
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

		$data_institutes = $this->get_list_alliances_review();
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

class TT_alliances_List_Table extends WP_List_Table{

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
            case 'full_name':
                return ucwords($item['name']).' '.ucwords($item['last_name']);
            case 'phone':
            case 'email':
                return $item[$column_name];
            case 'state':
            case 'city':
                return ucwords($item[$column_name]);
            case 'country':
                $name = get_name_country($item[$column_name]);
                return $name;
            case 'name_rector':
                return ucwords($item['name_rector']).' '.ucwords($item['lastname_rector']);
            case 'view_details':
                return "
                <a href='".admin_url('/admin.php?page=add_admin_partners_content&section_tab=fee_alliance&alliance_id='.$item['id'])."' class='button button-primary'><span class='dashicons dashicons-money-alt'></span>".__('Fees','form-plugin')."</a>
                <a href='".admin_url('/admin.php?page=add_admin_partners_content&section_tab=alliance_details&alliance_id='.$item['id'])."' class='button button-primary'></span><span class='dashicons dashicons-edit'></span>".__('Edit','form-plugin')."</a>";
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
            'full_name'     => __('Full name','aes'),
            'phone'         => __('Phone','aes'),
            'email'         => __('Email','aes'),
            'country'       => __('Country','aes'),
            'state'         => __('State','aes'),
            'city'          => __('City','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_list_alliances(){
        global $wpdb;
        $table_alliances =  $wpdb->prefix.'alliances';
        
        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $alliances = $wpdb->get_results("SELECT * 
                FROM {$table_alliances} WHERE 
                status = 0 AND 
                ( name LIKE '{$search}%' || 
                    email LIKE '{$search}%' || 
                    last_name LIKE '{$search}%' || 
                    name_legal LIKE '{$search}%')"
            ,"ARRAY_A");


        }else{
            $alliances = $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 1","ARRAY_A");
        }
        
        return $alliances;
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

		$data_alliances = $this->get_list_alliances();
		$per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
        $data = $data_alliances;

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

function get_institutes_from_alliance($alliance_id){

    global $wpdb;
    $table_institutes =  $wpdb->prefix.'institutes';
    
    $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE alliance_id={$alliance_id}");
    return $institutes;
}

function get_name_status_alliance($status_id){
    $status = match($status_id){
        '0' => __('Pending','aes'),
        '1' => __('Approved','aes'),
        '2' => __('Declined','aes'),
        default => '',
    };

    return $status;
}

function get_name_type($type_id){

    $type = match($type_id){
        '1' => __('Junior','aes'),
        '2' => __('Senior','aes'),
        default => '',
    };

    return $type;
}

function create_user_alliance($alliance){

    $user = get_user_by('email',$alliance->email);

    if(!$user){

        $password = generate_password_user();
    
        $userdata = [
            'user_login' => $alliance->email,
            'user_pass' => $password,
            'user_email' => $alliance->email,
            'first_name' => $alliance->name,
        ];

        $user_id = wp_insert_user($userdata);
        $user = new WP_User($user_id);
        $user->remove_role('subscriber');
        $user->add_role('alliance');

        update_user_meta($user_id,'alliance_id',$alliance->id);

        wp_new_user_notification($user_id, null, 'both' );
    }else{
        update_user_meta($user->id,'alliance_id',$alliance->id);
    }
}