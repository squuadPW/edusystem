<?php

function add_admin_form_academic_periods_content(){

    if(isset($_GET['action']) && !empty($_GET['action'])){

        if($_GET['action'] == 'change_status_academic_period'){
            try {
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        }
    }

    if(isset($_GET['section_tab']) && !empty($_GET['section_tab'])){
        if($_GET['section_tab'] == 'period_details'){
            $period =$_GET['period_id'];
            $period = get_period_details($period);
            include(plugin_dir_path(__FILE__).'templates/academic-period-detail.php');
        } 
        if($_GET['section_tab'] == 'add_period'){
            include(plugin_dir_path(__FILE__).'templates/academic-period-detail.php');
        }

    }else{

        if($_GET['action'] == 'save_period_details'){
            global $wpdb;
            $table_periods =  $wpdb->prefix.'academic_periods';
            $period_id = $_POST['period_id'];
            $name = $_POST['name'];
            $code = $_POST['code'];
            $status_id = $_POST['status_id'] ?? 0;

            //update
            if(isset($period_id) && !empty($period_id)){

                $wpdb->update($table_periods,[
                    'name' => $name,
                    'code' => $code,
                    'status_id' => $status_id,
                ],[ 'id' => $period_id]);
                
                setcookie('message',__('Changes saved successfully.','aes'),time() + 3600,'/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id='.$period_id));
                exit;

                
            //insert
            }else{

                $wpdb->insert($table_periods,[
                    'name' => $name,
                    'code' => $code,
                    'status_id' => $status_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;

            }
        } else {
            $list_academic_periods = new TT_academic_period_all_List_Table;
            $list_academic_periods->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-academic-periods.php');
        }
    }
}

class TT_academic_period_all_List_Table extends WP_List_Table{

    function __construct(){
        global $status, $page,$categories;
         
        parent::__construct( array(
            'singular'  => 'academic_period_',    
            'plural'    => 'academic_period_s',
            'ajax'      => true
        ) );
        
    }

    function column_default($item, $column_name){

        global $current_user;

        switch($column_name){
            case 'academic_period_id':
                return '#'.$item[$column_name];
            case 'name':
                return ucwords($item[$column_name]);
            case 'status_id':
                switch ($item[$column_name]) {
                    case 1:
                        return 'Active';
                        break;
                    
                    default:
                        return 'Inactive';
                        break;
                }
            case 'view_details':
                return "<a href='".admin_url('/admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id='.$item['academic_period_id'])."' class='button button-primary'>".__('View Details','aes')."</a>";
            default:
                return ucwords($item[$column_name]);
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
            'academic_period_code'     => __('Code','aes'),
            'name'  => __('Name','aes'),
            'status_id'  => __('Status','aes'),
            'date'     => __('Created at','aes'),
            'view_details' => __('Actions','aes'),
        );

        return $columns;
    }

    function get_academic_period_pendings(){
        global $wpdb;
        $academic_periods_array = [];

        $academic_periods = $wpdb->get_results("SELECT * FROM wp_academic_periods");

        if($academic_periods){
            foreach($academic_periods as $academic_period){
                array_push($academic_periods_array,[
                    'academic_period_code' => $academic_period->code,
                    'academic_period_id' => $academic_period->id,
                    'name' => $academic_period->name,
                    'status_id' => $academic_period->status_id,
                    'date' => $academic_period->created_at,
                ]);
            }
        }

        return $academic_periods_array;
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

        $data_academic_periods = $this->get_academic_period_pendings();

        $per_page = 10;

          
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_academic_periods;

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

function get_period_details($period_id){

    global $wpdb;
    $table_periods =  $wpdb->prefix.'academic_periods';

    $period = $wpdb->get_row("SELECT * FROM {$table_periods} WHERE id={$period_id}");
    return $period;
}