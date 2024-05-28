<?php

function add_admin_form_admission_content(){

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){

        if($_GET['section_tab'] == 'document_review'){

            $list_students = new TT_document_review_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-student-documents.php');

        }else if($_GET['section_tab'] == 'all_students'){

            $list_students = new TT_all_student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-student-documents.php');
        }else if($_GET['section_tab'] == 'student_details'){
            $documents = get_documents($_GET['student_id']);
            $student = get_student_detail($_GET['student_id']);
            $countries = get_countries();
            $partner = get_userdata($student->partner_id);
            include(plugin_dir_path(__FILE__).'templates/student-details.php');
        }

    }else{
        $list_students = new TT_new_student_List_Table;
	    $list_students->prepare_items();
        include(plugin_dir_path(__FILE__).'templates/list-student-documents.php');
    }
}

class TT_new_student_List_Table extends WP_List_Table{

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
                return "<a href='".admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id='.$item['id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
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
            'full_name'     => __('Full name','form-plugin'),
            'program'     => __('Program','form-plugin'),
            'grade' => __('Grade','form-plugin'),
            'view_details' => __('Actions','form-plugin'),
        );

        return $columns;
    }

    function get_new_students(){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';
        $table_student_documents = $wpdb->prefix.'student_documents';

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $data = $wpdb->get_results("SELECT a.* 
            FROM {$table_students} as a 
            JOIN {$table_student_documents} b on b.student_id = a.id 
            WHERE status_id=1 AND b.status != 0 AND 
            (a.name  LIKE '{$search}' OR a.last_name LIKE '{$search}%' OR email LIKE '{$search}%')
            GROUP BY a.id
            ","ARRAY_A");

        }else{

            $data = $wpdb->get_results("SELECT a.* 
            FROM {$table_students} as a 
            JOIN {$table_student_documents} b on b.student_id = a.id 
            WHERE status_id=1 AND b.status != 0
            GROUP BY a.id
            ","ARRAY_A");
        }

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

		$data_categories = $this->get_new_students();

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

class TT_document_review_List_Table extends WP_List_Table{

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
            case 'pending_documents':
                return $item['count_pending_documents'];
            case 'approved_documents':
                return $item['approved_pending_documents'];
            case 'pending_review_documents':
                return $item['review_pending_documents'];
            case 'rejected_documents':
                return $item['rejected_documents'];
            case 'waiting_time':
                $updated_at = Datetime::createFromFormat('Y-m-d H:i:s',wp_date('Y-m-d H:i:s',strtotime($item['updated_at'])));
                $time_now = Datetime::createFromFormat('Y-m-d H:i:s',wp_date('Y-m-d H:i:s'));
                $diff = $updated_at->diff($time_now);
                $html = "";
              
                if($diff->days < 15){

                    if($diff->days == 1){

                        $html .= '
                            <a href="javascript:void(0)" class="button button-success" style="border-radius:9px;">
                                <span>'.$diff->days.' '.__('Day','aes').'</span>
                            </a>
                        ';

                    }else{

                        $html .= '
                            <a href="javascript:void(0)" class="button button-success" style="border-radius:9px;">
                                <span>'.$diff->days.' '.__('Days','aes').'</span>
                            </a>
                        ';
                    }

                }else if($diff->days < 35){
                    $html .= '
                        <a href="javascript:void(0)" class="button button-warning" style="border-radius:9px;">
                            <span>'.$diff->days.' '.__('Days','aes').'</span>
                        </a>
                    ';
                }else if($diff->days >= 35){
                    $html .= '
                        <a href="javascript:void(0)" class="button button-danger" style="border-radius:9px;">
                            <span>'.$diff->days.' '.__('Days','aes').'</span>
                        </a>
                    ';
                }

                return $html;
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id='.$item['id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
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
            'full_name'     => __('Full name','aes'),
            'program'     => __('Program','aes'),
            'grade' => __('Grade','aes'),
            'pending_documents' => __('Pending','aes'),
            'pending_review_documents' => __('for Review','aes'),
            'approved_documents' => __('Approved','aes'),
            'rejected_documents' => __('Rejected','aes'),
            'waiting_time' => __('Waiting Time','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_pending_students(){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';
        $table_student_documents = $wpdb->prefix.'student_documents';

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];

            $data = $wpdb->get_results("SELECT a.*,
            (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 0) AS count_pending_documents,
            (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 5) AS approved_pending_documents,
            (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 1) AS review_pending_documents,
            (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 3 OR status = 4) AS rejected_documents
            FROM {$table_students} as a 
            JOIN {$table_student_documents} b on b.student_id = a.id 
            WHERE status_id=2 AND (b.status != 5) AND 
            (a.name  LIKE '{$search}' OR a.last_name LIKE '{$search}%' OR email LIKE '{$search}%')
            GROUP BY a.id
            ORDER BY a.updated_at ASC
            ","ARRAY_A");

        }else{

                $data = $wpdb->get_results("SELECT a.*, 
                (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 0) AS count_pending_documents,
                (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 5) AS approved_pending_documents,
                (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 1) AS review_pending_documents,
                (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 3 OR status = 4) AS rejected_documents
                FROM {$table_students} as a 
                JOIN {$table_student_documents} b on b.student_id = a.id 
                WHERE status_id=2 AND (b.status != 5) 
                GROUP BY a.id
                ORDER BY a.updated_at ASC
            ","ARRAY_A");
        }
  
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

		$data_categories = $this->get_pending_students();

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

class TT_all_student_List_Table extends WP_List_Table{

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
                return "<a href='".admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id='.$item['id'])."' class='button button-primary'>".__('View Details','form-plugin')."</a>";
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
            'full_name'     => __('Full name','form-plugin'),
            'program'     => __('Program','form-plugin'),
            'grade' => __('Grade','form-plugin'),
            'view_details' => __('Actions','form-plugin'),
        );

        return $columns;
    }

    function get_students(){

        global $wpdb;
        $table_students = $wpdb->prefix.'students';

        if(isset($_POST['s']) && !empty($_POST['s'])){

            $search = $_POST['s'];
            $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE (name  LIKE '{$search}%' OR last_name LIKE '{$search}%' OR email LIKE '{$search}%') ORDER BY name ASC","ARRAY_A");
        }else{
            $data = $wpdb->get_results("SELECT * FROM {$table_students}  ORDER BY name ASC","ARRAY_A");
        }

       
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

function get_student_detail($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    return $data;
}

function update_status_documents(){

    if(isset($_POST['document_id']) && !empty($_POST['document_id']) && isset($_POST['status']) && !empty($_POST['status']) && isset($_POST['student_id']) && !empty($_POST['student_id'])){

        global $wpdb;
        $table_student_documents = $wpdb->prefix.'student_documents';
        $student_id = $_POST['student_id'];
        $status_id = $_POST['status'];
        $document_id = $_POST['document_id'];

        $wpdb->update($table_student_documents,['status' => $status_id,'updated_at' => date('Y-m-d H:i:s')],['document_id' => $document_id,'student_id' => $student_id]);

        if($document_id == 'id_student' && $status_id == 5){
            update_status_student($student_id,2);
        }

        $documents = get_documents($student_id);

        $html = "";
        $solvency_administrative = true;

        foreach($documents as $document){

            if($document->status != 5){
                $solvency_administrative = false;
            }

            if($document->document_id == $document_id){

                $html .= '<td class="column-primary">';
                    
                        $name = match ($document->document_id) {
                            'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','form-plugin'),
                            'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','form-plugin'),
                            'id_parents' => __('ID OR CI OF THE PARENTS','form-plugin'),
                            'id_student' => __('ID STUDENTS','form-plugin'),
                            'photo_student_card' => __('PHOTO OF STUDENT CARD','form-plugin'),
                            'proof_of_grades' => __('PROOF OF GRADE','form-plugin'),
                            'proof_of_study' => __('PROOF OF STUDY','form-plugin'),
                            'vaccunation_card' => __('VACCUNATION CARD','form-plugin'),
                        };

                        $html .= $name;
                    
                    $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= '<td id="'."td_document_".$document->document_id.'" data-colname="'.__('Status','aes').'">';
                    $html .= "<b>";
                    
                        $status = match ($document->status){
                            '0' => __('No sent','form-plugin'),
                            '1' => __('Sent','form-plugin'),
                            '2' => __('Processing','form-plugin'),
                            '3' => __('Declined','form-plugin'),
                            '4' => __('Expired','form-plugin'),
                            '5' => __('Approved','form-plugin'),
                        };
                        $html .= $status;
                    $html .= "</b>";
                $html .= "</td>";
                $html .= '<td data-colname="'.__('Actions','aes').'">';
                    if($document->status > 0){
                        $html .= '<a target="_blank" href="'.wp_get_attachment_url($document->attachment_id).'" class="button button-primary">'.__('View','aes').'</a>';
                        if($document->status != 5){
                            $html .= ' <button data-document-id="'.$document->document_id.'" data-student-id="'.$document->student_id.'" data-status="5" class="button change-status button-success">'.__('Approved','aes').'</button>';
                        }
                        if($document->status != 5 && $document->status != 3){
                            $html .=  ' <button data-document-id="'.$document->document_id.'" data-student-id="'.$document->student_id.'" data-status="3" class="button change-status button-danger">'.__('Declined','aes').'</button>';
                        }
                    }
                $html .= "</td>";
            }

            if($solvency_administrative){
                update_status_student($student_id,3);
            }
        }

        echo json_encode(['status' => 'success','message' => __('status changed','aes'),'html' => $html]);

    }

    exit;
}

add_action( 'wp_ajax_nopriv_update_status_documents', 'update_status_documents');
add_action( 'wp_ajax_update_status_documents', 'update_status_documents');

function update_payment(){

    if(isset($_POST['order_id']) && !empty($_POST['order_id'])){

        $order = wc_get_order($_POST['order_id']);

        $order->set_status('completed');
        $order->save();

        echo json_encode(['status' => 'success','message' => __('Status changed','aes')]);
        die();
    }

}

add_action( 'wp_ajax_nopriv_update_payment', 'update_payment');
add_action( 'wp_ajax_update_payment', 'update_payment');

function get_data_student(){

    if(isset($_POST['student_id']) && !empty($_POST['student_id'])){

        $student = get_student_detail($_POST['student_id']);
        $partner = get_userdata($student->partner_id);
        $documents = get_documents($student->id);
        $data = [];
        $data_documents = [];

        $program = match($student->program_id){
            'aes' => __('AES (Dual Diploma)','form-plugin'),
            'psp' => __('PSP (Carrera Universitaria)','form-plugin'),
            'aes_psp' => __('AES (Dual Diploma)','form-plugin').','.__('AES (Dual Diploma)','form-plugin'),
        };

        $grade = match($student->grade_id){
            '1' => __('9no (antepenúltimo)','form-plugin'),
            '2' => __('10mo (penúltimo)','form-plugin'),
            '3' => __('11vo (último)','form-plugin'),
            '4' => __('Bachiller (graduado)','form-plugin')
        };

        foreach($documents as $document){

            $name = match ($document->document_id) {
                'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','form-plugin'),
                'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','form-plugin'),
                'id_parents' => __('ID OR CI OF THE PARENTS','form-plugin'),
                'id_student' => __('ID STUDENTS','form-plugin'),
                'photo_student_card' => __('PHOTO OF STUDENT CARD','form-plugin'),
                'proof_of_grades' => __('PROOF OF GRADE','form-plugin'),
                'proof_of_study' => __('PROOF OF STUDY','form-plugin'),
                'vaccunation_card' => __('VACCUNATION CARD','form-plugin'),
            };

            $status = match ($document->status){
                '0' => __('No sent','form-plugin'),
                '1' => __('Sent','form-plugin'),
                '2' => __('Processing','form-plugin'),
                '3' => __('Declined','form-plugin'),
                '4' => __('Expired','form-plugin'),
                '5' => __('Approved','form-plugin'),
            };

            if(!empty($document->attachment_id)){
                $url = wp_get_attachment_url($document->attachment_id);
            }else{
                $url = "";
            }

            array_push($data_documents,[
                'name' => $name,
                'status' => $status,
                'url' => $url
            ]);
        }

        $type_document_parent = match(get_user_meta($partner->ID,'document_type',true)){
            'passport' => __('Passport','aes'),
            'identification_document' => __('Identification Document','aes'),
            'ssn' => __('SSN'),
            default => '',
        };

        array_push($data,[
            'id_document' => $student->id_document,
            'type_document' => $student->type_document,
            'first_name' => $student->name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'birth_date' => $student->birth_date,
            'country' => $student->country,
            'city' => $student->city,
            'postal_code' => $student->postal_code,
            'program' => $program,
            'grade' => $grade,
            'gender' => $student->gender,
            'type_document_parent' => $type_document_parent,
            'id_document_parent' => get_user_meta($partner->ID,'id_document',true),
            'first_name_parent' => $partner->first_name,
            'last_name_parent' => $partner->last_name,
            'email_parent' => $partner->user_email,
            'country_parent' => get_user_meta($partner->ID,'billing_country',true),
            'city_parent' => get_user_meta($partner->ID,'billing_city',true),
            'post_code_parent' => get_user_meta($partner->ID,'billing_postcode',true),
            'phone_parent' => get_user_meta($partner->ID,'billing_phone',true),
            'birth_date_parent' => get_user_meta($partner->ID,'birth_date',true),
            'gender_parent' => get_user_meta($partner->ID,'gender',true),
            'occupation_parent' => get_user_meta($partner->ID,'occupation',true),
            'documents' => $data_documents
        ]);

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    die();
}

add_action( 'wp_ajax_nopriv_get_student_details', 'get_data_student');
add_action( 'wp_ajax_get_student_details', 'get_data_student');