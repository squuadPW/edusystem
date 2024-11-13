<?php

function add_admin_form_scholarships_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'change_status_scholarship'){
            try {
                    global $wpdb;
                    $scholarship_id = $_POST['scholarship_id'];
                    $fee_inscription = $_POST['fee_inscription'];
                    $program = $_POST['program'];
                    $fee_graduation = $_POST['fee_graduation'];
                    $scholarship = $wpdb->get_row("SELECT * FROM wp_student_scholarship_application WHERE id = {$scholarship_id}");

                    // GENERAMOS USUARIO PARA EL PARTNER
                    $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = {$scholarship->partner_id}");
                    $username = $partner->email;
                    $user_email = $partner->email;
                    $password = $partner->password;
                    if ( username_exists( $username ) ) {
                        $user_partner_id = username_exists( $username );
                        $user_partner = new WP_User($user_partner_id);
                        $user_partner->set_role( 'parent' );
                    } else {
                        $user_partner_id = wp_create_user($username, $password, $user_email);
                        $user_partner = new WP_User($user_partner_id);
                        $user_partner->set_role( 'parent' );
                    }

                    
                    update_user_meta($user_partner_id, 'first_name', $partner->name);
                    update_user_meta($user_partner_id, 'billing_first_name', $partner->name);
                    update_user_meta($user_partner_id, 'last_name', $partner->last_name);
                    update_user_meta($user_partner_id, 'billing_last_name', $partner->last_name);
                    update_user_meta($user_partner_id, 'nickname', $username);
                    update_user_meta($user_partner_id, 'birth_date', $partner->birth_date);
                    update_user_meta($user_partner_id, 'gender', $partner->gender);
                    update_user_meta($user_partner_id, 'billing_email', $partner->email);
                    update_user_meta($user_partner_id, 'billing_phone', $partner->phone);
                    update_user_meta($user_partner_id, 'document_type', $partner->type_document);
                    update_user_meta($user_partner_id, 'type_document', $partner->type_document);
                    update_user_meta($user_partner_id, 'id_document', $partner->id_document);
                    update_user_meta($user_partner_id, 'status_register', 1);
                    update_user_meta($user_partner_id, 'is_scholarship', 1);
                    // GENERAMOS USUARIO PARA EL PARTNER

                    // CREAMOS REGISTRO EN TABLA STUDENTS
                    $is_parent = false;
                    $pre_students_table = $wpdb->prefix . 'pre_students';
                    $students_table = $wpdb->prefix . 'students';
                    $pre_student_row = $wpdb->get_row("SELECT * FROM $pre_students_table WHERE id = {$scholarship->student_id}");                   
                    if ($user_email == $pre_student_row->email) {
                        $is_parent = true;
                    }
                    $wpdb->insert(
                        $students_table,
                        array(
                            'type_document' => $pre_student_row->type_document,
                            'id_document' => $pre_student_row->id_document,
                            'name' => $pre_student_row->name,
                            'middle_name' => $pre_student_row->middle_name,
                            'last_name' => $pre_student_row->last_name,
                            'middle_last_name' => $pre_student_row->middle_last_name,
                            'birth_date' => $pre_student_row->birth_date,
                            'ethnicity' => $pre_student_row->ethnicity,
                            'academic_period' => $pre_student_row->academic_period,
                            'phone' => $pre_student_row->phone,
                            'email' => $pre_student_row->email,
                            'gender' => $pre_student_row->gender,
                            'country' => $pre_student_row->country,
                            'city' => $pre_student_row->city,
                            'postal_code' => $pre_student_row->postal_code,
                            'grade_id' => $pre_student_row->grade_id,
                            'name_institute' => strtoupper($pre_student_row->name_institute),
                            'institute_id' => $pre_student_row->institute_id,
                            'program_id' => $pre_student_row->program_id,
                            'partner_id' => $user_partner_id,
                            'status_id' => 1,
                            'moodle_student_id' => $pre_student_row->moodle_student_id,
                            'moodle_password' => $pre_student_row->moodle_password,
                        )
                    );
                    $student_id = $wpdb->insert_id; // Get the ID of the last inserted record
                    // CREAMOS REGISTRO EN TABLA STUDENTS

                    // GENERAMOS USUARIO PARA EL ESTUDIANTE
                    $username = $pre_student_row->email;
                    $user_email = $pre_student_row->email;
                    if ( username_exists( $username ) ) {
                        $user_student_id = username_exists( $username );
                        $user_student = new WP_User($user_student_id);
                        $user_student->set_role( 'student' );
                        if ($is_parent) {
                            $user_student->set_role( 'parent' );
                        }
                    } else {
                        $user_student_id = wp_create_user($username, $is_parent ? $password : generate_password_user(), $user_email);
                        $user_student = new WP_User($user_student_id);
                        $user_student->set_role( 'student' );
                        if ($is_parent) {
                            $user_student->set_role( 'parent' );
                        }
                    }

                    update_user_meta($user_student_id, 'first_name', $pre_student_row->name);
                    update_user_meta($user_student_id, 'last_name', $pre_student_row->last_name);
                    update_user_meta($user_student_id, 'billing_phone', $pre_student_row->phone);
                    update_user_meta($user_student_id, 'billing_email', $pre_student_row->email);
                    update_user_meta($user_student_id, 'birth_date', $pre_student_row->birth_date);
                    update_user_meta($user_student_id, 'student_id', $student_id);
                    update_user_meta($user_student_id, 'is_scholarship', 1);

                    update_user_meta($user_partner_id, 'billing_country', $pre_student_row->country);
                    update_user_meta($user_partner_id, 'billing_city', $pre_student_row->city);

                    insert_register_documents($student_id, $pre_student_row->grade_id);
                    // GENERAMOS USUARIO PARA EL ESTUDIANTE

                    // CREAMOS REGISTRO EN TABLA STUDENT_PAYMENTS
                    if ($fee_inscription == 1) {
                        $product_id = 63;
                        $product = wc_get_product($product_id);
                        $amount = $product->get_price();

                        $data = array(
                            'status_id' => 1, // Replace with the actual status ID
                            'student_id' => $student_id, // Replace with the actual student ID
                            'product_id' => $product_id, // Replace with the actual product ID
                            'amount' => $amount, // Replace with the actual amount
                            'type_payment' => 1, // Replace with the actual payment type
                            'cuote' => 1, // Replace with the actual num coute
                            'num_cuotes' => 1, // Replace with the num total of coutes
                            'date_payment' => date('Y-m-d'), // Replace with the date of first payment
                            'date_next_payment' => date('Y-m-d'), // Replace with the date of next payment
                        );
    
                        $wpdb->insert($wpdb->prefix.'student_payments', $data);
                    }

                    if ($program == 1) {
                        $product_id = 51;
                        $product = wc_get_product($product_id);
                        $amount = $product->get_price();

                        $data = array(
                            'status_id' => 1, // Replace with the actual status ID
                            'student_id' => $student_id, // Replace with the actual student ID
                            'product_id' => $product_id, // Replace with the actual product ID
                            'amount' => $amount, // Replace with the actual amount
                            'type_payment' => 1, // Replace with the actual payment type
                            'cuote' => 1, // Replace with the actual num coute
                            'num_cuotes' => 1, // Replace with the num total of coutes
                            'date_payment' => date('Y-m-d'), // Replace with the date of first payment
                            'date_next_payment' => date('Y-m-d'), // Replace with the date of next payment
                        );
    
                        $wpdb->insert($wpdb->prefix.'student_payments', $data);
                    }
                    // CREAMOS REGISTRO EN TABLA STUDENT_PAYMENTS

                    // GUARDAMOS EL STATUS
                    $wpdb->update(
                        'wp_student_scholarship_application',
                        array(
                            'status_id' => 1
                        ),
                        array(
                            'id' => $scholarship_id
                        )
                    );
                    // GUARDAMOS EL STATUS

                    update_status_student($student_id, 1);

                    $email_request_documents = WC()->mailer()->get_emails()['WC_Request_Documents_Email'];
                    $email_request_documents->trigger($student_id);

                    wp_new_user_notification($user_partner_id, null, 'both' );
                    wp_redirect(admin_url('admin.php?page=add_admin_form_scholarships_content'));
                    exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        }
    }

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){


        if($_GET['section_tab'] == 'all_scholarships'){
            $list_scholarships = new TT_scholarship_all_List_Table;
            $list_scholarships->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-scholarships.php');
        }else if($_GET['section_tab'] == 'scholarship_detail'){

            global $current_user;
            global $wpdb;
            $roles = $current_user->roles;
            $scholarship_id = $_GET['scholarship_id'];
            $scholarship = $wpdb->get_row("SELECT * FROM wp_student_scholarship_application WHERE id = ".$scholarship_id);
            $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = ".$scholarship->student_id);
            $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = ".$scholarship->partner_id);
            $institute = $wpdb->get_row("SELECT * FROM wp_institutes WHERE id = ".$student->institute_id);
            $grade = $wpdb->get_row("SELECT * FROM wp_grades WHERE id = ".$student->grade_id);

            include(plugin_dir_path(__FILE__).'templates/scholarship-detail.php');
        }

    }else{
        $list_scholarships = new TT_scholarship_pending_List_Table;
        $list_scholarships->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-scholarships.php');
    }
}


class TT_scholarship_pending_List_Table extends WP_List_Table{

    function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'scholarship_pending',    
            'plural'    => 'scholarship_pendings',
            'ajax'      => true
        ) );
        
    }

    function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'scholarship_id':
                return '#'.$item[$column_name];
            case 'student_name':
                return ucwords($item[$column_name]);
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'date':
                return ucwords($item[$column_name]);
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_scholarships_content&section_tab=scholarship_detail&scholarship_id='.$item['scholarship_id'])."' class='button button-primary'>".__('View Details','aes')."</a>";
            default:
                return ucwords($item[$column_name]);
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
            'scholarship_id'     => __('Scholarship ID','aes'),
            'student_name'  => __('Student','aes'),
            'student_email'  => __('Student email','aes'),
            'partner_name' => __('Parent','aes'),
            'partner_email' => __('Parent email','aes'),
            'date'     => __('Created at','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_scholarship_pendings(){
        global $wpdb;
        $scholarships_array = [];

        $scholarships = $wpdb->get_results("SELECT * FROM wp_student_scholarship_application WHERE status_id = 0 ORDER BY id DESC");

        if($scholarships){
            foreach($scholarships as $scholarship){
                $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = ".$scholarship->student_id);
                $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = ".$scholarship->partner_id);

                array_push($scholarships_array,[
                    'scholarship_id' => $scholarship->id,
                    'date' => $scholarship->created_at,
                    'student_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
                    'student_email' => $student->email,
                    'partner_name' => $partner->name . ' ' . $partner->middle_name . ' ' . $partner->last_name . ' ' . $partner->middle_last_name,
                    'partner_email' => $partner->email
                ]);
            }
        }

        return $scholarships_array;
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

        $data_scholarships = $this->get_scholarship_pendings();

        $per_page = 10;

          
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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

class TT_scholarship_all_List_Table extends WP_List_Table{

    function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'scholarship_pending',    
            'plural'    => 'scholarship_pendings',
            'ajax'      => true
        ) );
        
    }

    function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'scholarship_id':
                return '#'.$item[$column_name];
            case 'student_name':
                return ucwords($item[$column_name]);
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'date':
                return ucwords($item[$column_name]);
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_scholarships_content&section_tab=scholarship_detail&scholarship_id='.$item['scholarship_id'])."' class='button button-primary'>".__('View Details','aes')."</a>";
            default:
                return ucwords($item[$column_name]);
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
            'scholarship_id'     => __('Scholarship ID','aes'),
            'student_name'  => __('Student','aes'),
            'student_email'  => __('Student email','aes'),
            'partner_name' => __('Partner','aes'),
            'partner_email' => __('Partner email','aes'),
            'date'     => __('Created at','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_scholarship_pendings(){
        global $wpdb;
        $scholarships_array = [];

        $scholarships = $wpdb->get_results("SELECT * FROM wp_student_scholarship_application WHERE status_id = 1 ORDER BY id DESC");

        if($scholarships){
            foreach($scholarships as $scholarship){
                $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = ".$scholarship->student_id);
                $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = ".$scholarship->partner_id);

                array_push($scholarships_array,[
                    'scholarship_id' => $scholarship->id,
                    'date' => $scholarship->created_at,
                    'student_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
                    'student_email' => $student->email,
                    'partner_name' => isset($partner) ? $partner->name . ' ' . $partner->middle_name . ' ' . $partner->last_name . ' ' . $partner->middle_last_name : 'N/A',
                    'partner_email' => isset($partner) ? $partner->email : 'N/A'
                ]);
            }
        }

        return $scholarships_array;
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

        $data_scholarships = $this->get_scholarship_pendings();

        $per_page = 10;

          
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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