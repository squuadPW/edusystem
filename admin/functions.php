<?php

require plugin_dir_path( __FILE__ ) . 'roles.php';
require plugin_dir_path( __FILE__ ) . 'admission.php';
require plugin_dir_path( __FILE__ ) . 'payments.php';
require plugin_dir_path( __FILE__ ) . 'scholarships.php';
require plugin_dir_path( __FILE__ ) . 'institute.php';
require plugin_dir_path( __FILE__ ) . 'alliances.php';
require plugin_dir_path( __FILE__ ) . 'departments.php';
require plugin_dir_path( __FILE__ ) . 'bitrix/sdk/crest.php';
require plugin_dir_path( __FILE__ ) . 'emails/function.php';
require plugin_dir_path( __FILE__ ) . 'user.php';
require plugin_dir_path( __FILE__ ) . 'moodle/rest.php';
require plugin_dir_path( __FILE__ ) . 'moodle.php';
require plugin_dir_path( __FILE__ ) . 'documents.php';

// modules institutes
require plugin_dir_path( __FILE__ ) . '/institutes/student-registered.php';
require plugin_dir_path( __FILE__ ) . '/institutes/payments.php';

//modules alliance
require plugin_dir_path( __FILE__ ) . 'alliance/institutes-registered.php';
require plugin_dir_path( __FILE__ ) . 'alliance/payments.php';

function admin_form_plugin_scripts(){
  wp_enqueue_style('style-admin',plugins_url('aes').'/admin/assets/css/style.css');
}

add_action( 'wp_enqueue_scripts', 'admin_form_plugin_scripts');

function aes_scripts_admin(){

    wp_enqueue_style('flatpickr',plugins_url('aes').'/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('intel-css',plugins_url('aes').'/public/assets/css/intlTelInput.css');
    wp_enqueue_style('style-admin',plugins_url('aes').'/admin/assets/css/style.css');
    wp_enqueue_script('xlsx-js',plugins_url('aes').'/admin/assets/js/xlsx.full.min.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('admin-flatpickr',plugins_url('aes').'/public/assets/js/flatpickr.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('admin-flatpickr',plugins_url('aes').'/public/assets/js/flatpickr.js',array('jquery'),'1.0.0',true);
    wp_enqueue_script('intel-js',plugins_url('aes').'/public/assets/js/intlTelInput.min.js');
    wp_enqueue_script('masker-js',plugins_url('aes').'/public/assets/js/vanilla-masker.min.js');

    if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_payments_content'){
        wp_enqueue_script('student-payment',plugins_url('aes').'/admin/assets/js/payment.js',array('jquery'),'1.0.0',true);    
    }

    if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_scholarships_content'){
        wp_enqueue_script('student-payment',plugins_url('aes').'/admin/assets/js/scholarship.js',array('jquery'),'1.0.0',true);    
    }

    if(isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'add_admin_institutes_content' || $_GET['page'] == 'list_admin_institutes_partner_registered_content' || $_GET['page'] == 'list_admin_institutes_payments_content')){
        wp_enqueue_script('institute',plugins_url('aes').'/admin/assets/js/institute.js',array('jquery'),'1.0.0',true);

        wp_localize_script('institute','list_fee_institute',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_fee_institute' 
        ]);
    }
    
    if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_partners_content' || isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'list_admin_partner_payments_content'){
        wp_enqueue_script('alliance',plugins_url('aes').'/admin/assets/js/alliance.js',array('jquery'),'1.0.0',true);


        wp_localize_script('alliance','list_fee_alliance',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_fee_alliance' 
        ]);
    }

    if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_department_content'){
        wp_enqueue_script('department',plugins_url('aes').'/admin/assets/js/department.js',array('jquery'),'1.0.0',true);
    }

    if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_admission_content'){
        wp_enqueue_script('student-documents',plugins_url('aes').'/admin/assets/js/document.js',array('jquery'),'1.0.0',true);

        wp_localize_script('student-documents','update_status_documents',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'update_status_documents' 
        ]);
    
        wp_localize_script('student-documents','get_student_details',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'update_status_documents' 
        ]);
    
        wp_localize_script('student-documents','get_student_details',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'get_student_details' 
        ]);
    }
    
}

add_action( 'admin_enqueue_scripts', 'aes_scripts_admin',3 );

function add_custom_admin_page() {

    global $current_user;
    $roles = $current_user->roles;

    if(in_array('institutes',$roles)){

        add_menu_page(
            __('Students','aes'),
            __('Students','aes'),
            'read',
            'list_admin_institutes_student_registered_content',
            'list_admin_institutes_student_registered_content', 
            'dashicons-groups',
            10
        );

        add_menu_page(
            __('Fees','aes'),
            __('Fees','aes'),
            'read',
            'list_admin_institutes_payments_content',
            'list_admin_institutes_payments_content', 
            'dashicons-money-alt',
            11
        );
    }

    if(in_array('alliance',$roles)){

        add_menu_page(
            __('Institutes','aes'),
            __('Institutes','aes'),
            'read',
            'list_admin_institutes_partner_registered_content',
            'list_admin_institutes_partner_registered_content', 
            'dashicons-groups',
            10
        );

        add_menu_page(
            __('Fees','aes'),
            __('Fees','aes'),
            'read',
            'list_admin_partner_payments_content',
            'list_admin_partner_payments_content', 
            'dashicons-money-alt',
            11
        );
    }

    add_menu_page(
        __('Administrations','aes'),
        __('Administrations','aes'),
        'manage_administrator_aes',
        'list_admin_form_administrations_content',
        'list_admin_form_administrations_content', 
        'dashicons-admin-settings',
        10
    );

    add_submenu_page('list_admin_form_administrations_content',__('Departments','aes'),__('Departments','aes'),'manager_departments_aes','add_admin_department_content', 'list_admin_form_department_content',);
    remove_submenu_page('list_admin_form_administrations_content','list_admin_form_administrations_content');

    add_menu_page( 
        __('Admission','aes'),
        __('Admission','aes'),
        'manager_admission_aes', 
        'add_admin_form_admission_content',
        'add_admin_form_admission_content', 
        'dashicons-groups', 
        7
    );

    add_submenu_page('add_admin_form_admission_content',__('Required Documents','aes'),__('Required Documents','aes'),'manager_documents_aes','admission-documents','show_admission_documents', 10);

    add_menu_page( 
        __('Payments','aes'),
        __('Payments','aes'),
        'manager_payments_aes', 
        'add_admin_form_payments_content',
        'add_admin_form_payments_content', 
        'dashicons-money-alt', 
        7
    );

    add_menu_page( 
        __('Scholarship','aes'),
        __('Scholarship','aes'),
        'manager_scholarship_aes', 
        'add_admin_form_scholarships_content',
        'add_admin_form_scholarships_content', 
        'dashicons-media-document', 
        7
    );

    add_menu_page( 
        __('Institutes','aes'),
        __('Institutes','aes'),
        'manager_institutes_aes', 
        'add_admin_institutes_content',
        'add_admin_institutes_content', 
        ' dashicons-bank', 
        7
    );

    add_menu_page( 
        __('Alliances','aes'),
        __('Alliances','aes'),
        'manager_alliances_aes', 
        'add_admin_partners_content',
        'add_admin_partners_content', 
        'dashicons-thumbs-up', 
        7
    );

    add_menu_page( 
        __('Moodle','aes'),
        __('Moodle','aes'),
        'manager_moodle_aes', 
        'add_admin_moodle_content',
        'add_admin_moodle_content', 
        'dashicons-share-alt', 
        7
    );

    add_submenu_page('add_admin_moodle_content',__('Settings','aes'),__('Settings','aes'),'manager_moodle_settings_aes','moodle-setting','show_moodle_setting', 10);
    remove_submenu_page('add_admin_moodle_content','add_admin_moodle_content');
    
    //add_menu_page('logo', 'logo', 'read', 'logo_based_menu', '', '', 1);
}
  
add_action('admin_menu', 'add_custom_admin_page');

function add_cap_to_administrator(){

    $role = get_role('administrator');
    $role->add_cap('manage_administrator_aes');
    $role->add_cap('manager_departments_aes');
    $role->add_cap('manager_scholarship_aes');
    $role->add_cap('manager_admission_aes');
    $role->add_cap('manager_documents_aes');
    $role->add_cap('manager_payments_aes');
    $role->add_cap('manager_alliances_aes');
    $role->add_cap('manager_institutes_aes');
    $role->add_cap('manager_moodle_aes');
    $role->add_cap('manager_moodle_settings_aes');
}

add_action('admin_init','add_cap_to_administrator');

function list_departments_admin_page_callback() {
    echo do_shortcode( '[list_departments]' );
}
  
add_action('admin_menu', 'add_custom_admin_page');

function custom_login_store(){


    $url = 'https://americanelite.dreamhosters.com/wp-content/uploads/2024/05/cropped-American-elite-LOGO-1-600x188-1-1.png';
    echo '
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url('.$url.');
        background-size: cover;
        background-repeat: no-repeat;
        width:110px;
        height:110px;
        background-color:white;
        border-radius:50%;
        }
        
    </style>';

}
add_action( 'login_enqueue_scripts', 'custom_login_store' );

function get_dates_search($filter,$custom){

    if($filter == 'today'){
        $start = get_gmt_from_date(wp_date('Y-m-d').'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d').'23:59','Y-m-d H:i');

    }else if($filter == 'yesterday'){
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('-1 days')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',).'00:00','Y-m-d H:i');

    }else if($filter == 'this-week'){

        $date = Datetime ::createFromFormat('Y-m-d',wp_date('Y-m-d'));
    
        if($date->format('w') == 1){
            $start = get_gmt_from_date(wp_date('Y-m-d').'00:00','Y-m-d H:i');
        }else{
            $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('last tuesday')).'00:00','Y-m-d H:i');
        }

        if($date->format('w') == 1){
            $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('next saturday',strtotime('+1 days'))).'23:59','Y-m-d H:i');
        }else{
            $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('next sunday')).'23:59','Y-m-d H:i');
        }
      
    }else if($filter == 'last-week'){
        
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('last week')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('this week -1 days')).'23:59','Y-m-d H:i');
        

    }else if($filter == 'this-month'){
        
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('first day of this month')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('last day of this month')).'23:59','Y-m-d H:i');
        
    }else if($filter == 'last-month'){

        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('first day of last month')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('last day of last month')).'23:59','Y-m-d H:i');
        
       
    }else if($filter == 'custom'){

        $date = str_replace([' to ',' a '],',',$custom);
        $date_array = explode(',',$date);

        $start = str_replace('/','-',$date_array[0]);

        if(isset($date_array[1]) && !empty($date_array[1])){
            $end = str_replace('/','-',$date_array[1]);
        }else{
            $end = str_replace('/','-',$date_array[0]);
        } 

        $startDatetime = Datetime ::createFromFormat('m-d-Y',$start);
        $endDatetime = Datetime ::createFromFormat('m-d-Y',$end);
    
        $start = get_gmt_from_date($startDatetime->format('Y-m-d').'07:00','Y-m-d H:i');
        $end = get_gmt_from_date($endDatetime->modify('+1 day')->format('Y-m-d').'06:59','Y-m-d H:i');
        /*
        if($sales){

            $dayStart = $startDatetime->format('w');

            if(get_option('restaurant_system_schedule_'.$dayStart.'_checkbox') == 'true'){

                $start_time = get_option('restaurant_system_schedule_'.$dayStart.'_start_time');
                
                if(get_option('restaurant_system_schedule_'.$dayStart.'_interday') == 'true'){
                    $start = get_gmt_from_date($startDatetime->format('Y-m-d').$start_time,'Y-m-d H:i');
                }
            }

        }

        $end = get_gmt_from_date($endDatetime->format('Y-m-d').'23:59','Y-m-d H:i');

        if($sales){

            $dayEnd = $endDatetime->format('w');

            if(get_option('restaurant_system_schedule_'.$dayEnd.'_checkbox') == 'true'){

                $end_time = get_option('restaurant_system_schedule_'.$dayEnd.'_end_time');

                if(get_option('restaurant_system_schedule_'.$dayEnd.'_interday') == 'true'){
                    
                    $end = get_gmt_from_date($endDatetime->format('Y-m-d').$end_time,'Y-m-d H:i');
                }
            }
        }
        */
    }
   
    return [$start,$end];
}



// AGREGAR NUEVO CAMPO DE VARIACION DE PRODUCTO PARA JUGAR CON LOS VALORES DE LAS CUOTAS EN LOS PROGRAMAS
add_action( 'woocommerce_product_after_variable_attributes', 'num_cuotes', 10, 3 );
function num_cuotes( $loop, $variation_data, $variation ) {
    woocommerce_wp_text_input(
        array(
            'id'            => 'text_field[' . $loop . ']',
            'label'         => 'Num cuotes',
            'wrapper_class' => 'form-row',
            'placeholder'   => 'Num cuotes for program',
            'desc_tip'      => true,
            'description'   => 'Number of installments to pay for a product.',
            'value'         => get_post_meta( $variation->ID, 'num_cuotes_text', true )
        )
    );
}

add_action( 'woocommerce_save_product_variation', 'save_num_cuotes', 10, 2 );
function save_num_cuotes( $variation_id, $i ) {
    if ( isset( $_POST['text_field'][$i] ) ) {
        update_post_meta( $variation_id, 'num_cuotes_text', sanitize_text_field( $_POST['text_field'][$i] ) );
    }
}
// AGREGAR NUEVO CAMPO DE VARIACION DE PRODUCTO PARA JUGAR CON LOS VALORES DE LAS CUOTAS EN LOS PROGRAMAS