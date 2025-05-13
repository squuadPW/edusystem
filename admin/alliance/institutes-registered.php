<?php

function list_admin_institutes_partner_registered_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'institute-detail'){

            $institute_id = $_GET['institute_id'];
            $institute = get_institute_details($institute_id);
            $countries = get_countries();
            $states = get_states_by_country_code($institute->country);
            include(plugin_dir_path(__FILE__).'../templates/institute-details-alliance.php');

        }else if($_GET['action'] == 'institute-students'){

            $list_students_institute = new TT_Institute_Students_List_Table;
            $list_students_institute->prepare_items();

            include(plugin_dir_path(__FILE__).'../templates/institute-students-alliance.php');

        }else if($_GET['action'] == 'add_institute'){
            $countries = get_countries();
            include(plugin_dir_path(__FILE__).'../templates/institute-details-alliance.php');

        }else if($_GET['action'] == 'save_institute_details'){
            
            global $wpdb, $current_user;
            $table_institutes =  $wpdb->prefix.'institutes';
            $institute_id = $_POST['institute_id'];

            $name = $_POST['name'];
            $phone = $_POST['phone_hidden'];
            $email = $_POST['email'];
            $country = $_POST['country'];
            $state = $_POST['state'];
            $city = $_POST['city'];
            $level = $_POST['level'];
            $fee = str_replace('%', '', $_POST['fee']);
            $rector_name = $_POST['rector_name'];
            $rector_last_name = $_POST['rector_last_name'];
            $rector_phone = $_POST['rector_phone_hidden'];
            $contact_name = $_POST['contact_name'];
            $contact_last_name = $_POST['contact_last_name'];
            $contact_phone = $_POST['contact_phone_hidden'];
            $address = $_POST['address'];
            $description = $_POST['description'];
            $reference = $_POST['reference'];
            $business_name = $_POST['business_name'];
            $type_calendar = $_POST['type_calendar'];
            $lower_text = $_POST['lower_text'];
            $middle_text = $_POST['middle_text'];
            $upper_text = $_POST['upper_text'];
            $graduated_text = $_POST['graduated_text'];

            $alliance = get_alliance_detail_email($current_user->user_email);
            $alliance_id = $alliance->id;

            //update
            if(isset($institute_id) && !empty($institute_id)){

                $wpdb->update($table_institutes, [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'level_id' => $level,
                    'type_calendar' => $type_calendar,
                    'fee' => $fee,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_last_name,
                    'phone_rector' => $rector_phone,
                    'name_contact' => $contact_name,
                    'lastname_contact' => $contact_last_name,
                    'phone_contact' => $contact_phone,
                    'address' => $address,
                    'description' => $description,
                    'business_name' => $business_name,
                    'lower_text' => $lower_text,
                    'middle_text' => $middle_text,
                    'upper_text' => $upper_text,
                    'graduated_text' => $graduated_text,
                    'alliance_id' => $alliance_id,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $institute_id]);
                
                setcookie('message',__('Changes saved successfully.','edusystem'),time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$institute_id));
                exit;

            //insert
            }else{

                $wpdb->insert($table_institutes, [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'country' => $country,
                    'state' => $state,
                    'city' => $city,
                    'level_id' => $level,
                    'type_calendar' => $type_calendar,
                    'fee' => $fee,
                    'name_rector' => $rector_name,
                    'lastname_rector' => $rector_last_name,
                    'phone_rector' => $rector_phone,
                    'name_contact' => $contact_name,
                    'lastname_contact' => $contact_last_name,
                    'phone_contact' => $contact_phone,
                    'reference' => $reference,
                    'address' => $address,
                    'description' => $description,
                    'business_name' => $business_name,
                    'lower_text' => $lower_text,
                    'middle_text' => $middle_text,
                    'upper_text' => $upper_text,
                    'graduated_text' => $graduated_text,
                    'alliance_id' => $alliance_id,
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
                    $buttons = "<a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=fee-institute&institute_id='.$item['id'])."'><span class='dashicons dashicons-money-alt'></span>".__('Fees','edusystem')."</a>";
                    // $buttons .= "<a style='margin-left: 10px' class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$item['id'])."'><span class='dashicons dashicons-edit'></span>".__('Edit','edusystem')."</a>";
                    $buttons .= "<a style='margin-left: 4px' class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-students&institute_id='.$item['id'])."'><span class='dashicons dashicons-admin-users'></span>".__('Students','edusystem')."</a>";
                    return $buttons;
                }else{
                    // return "<a class='button button-primary' href='".admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=institute-detail&institute_id='.$item['id'])."'><span class='dashicons dashicons-edit'></span>".__('Edit','edusystem')."</a>";
                    return '';
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
            'name'     => __('Name','edusystem'),
            'email'    => __('Email','edusystem'),
            'level'    => __('Level','edusystem'),
            'status'   => __('Status','edusystem'),
            'created_at' => __('Created at','edusystem'),
            'view_details' => __('Actions','edusystem'),
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

class TT_Institute_Students_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'student':
                $student = $item['last_name'] . ' ' . $item['middle_last_name'] . ' ' . $item['name'] . ' ' . $item['middle_name'];
                return $student;
            case 'initial':
                $initial = $item['academic_period'] . ' - ' . $item['initial_cut'];
                return $initial;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {
        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'initial' => __('Initial term', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
        );

        return $columns;
    }

    function get_students()
    {
        global $wpdb;
        $students_array = [];
        $institute_id = isset($_GET['institute_id']) ? (int) $_GET['institute_id'] : '';
    
        // PAGINATION
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION
    
        $table_students = $wpdb->prefix . 'students';
    
        // Construir WHERE dinámicamente
        $where_clauses = array();
        $params = array();
    
        if (!empty($institute_id)) {
            $where_clauses[] = 'institute_id = %d';
            $params[] = $institute_id;
        }
    
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";
    
        if (!empty($where_clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }
    
        $sql .= ' ORDER BY id DESC LIMIT %d OFFSET %d';
        $params[] = $per_page;
        $params[] = $offset;
    
        // Preparar y ejecutar consulta segura
        $prepared_sql = $wpdb->prepare($sql, $params);
        $students = $wpdb->get_results($prepared_sql, ARRAY_A);
    
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
    
        return ['data' => $students, 'total_count' => $total_count];
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items()
    {

        $data_get = $this->get_students();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $data = $data_get['data'];
        $total_count = (int) $data_get['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }

}