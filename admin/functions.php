<?php

require plugin_dir_path( __FILE__ ) . 'roles.php';
require plugin_dir_path( __FILE__ ) . 'admission.php';
require plugin_dir_path( __FILE__ ) . 'report.php';
require plugin_dir_path( __FILE__ ) . 'payments.php';
require plugin_dir_path( __FILE__ ) . 'scholarships.php';
require plugin_dir_path( __FILE__ ) . 'academic_periods.php';
require plugin_dir_path( __FILE__ ) . 'send-email.php';
require plugin_dir_path( __FILE__ ) . 'staff.php';
require plugin_dir_path( __FILE__ ) . 'institute.php';
require plugin_dir_path( __FILE__ ) . 'alliances.php';
require plugin_dir_path( __FILE__ ) . 'departments.php';
require plugin_dir_path( __FILE__ ) . 'bitrix/sdk/crest.php';
require plugin_dir_path( __FILE__ ) . 'emails/function.php';
require plugin_dir_path( __FILE__ ) . 'user.php';
require plugin_dir_path( __FILE__ ) . 'moodle/rest.php';
require plugin_dir_path( __FILE__ ) . 'moodle.php';
require plugin_dir_path( __FILE__ ) . 'laravelRequests.php';
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

    if(isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'add_admin_institutes_content' || $_GET['page'] == 'list_admin_institutes_partner_registered_content' || $_GET['page'] == 'list_admin_institutes_payments_content' || $_GET['page'] == 'list_admin_institutes_invoice_content')){
        wp_enqueue_script('institute',plugins_url('aes').'/admin/assets/js/institute.js',array('jquery'),'1.0.0',true);

        wp_localize_script('institute','list_fee_institute',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_fee_institute' 
        ]);
    }

    if(isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'report-sales' || $_GET['page'] == 'add_admin_form_report_content') || $_GET['page'] == 'report-accounts-receivables' || $_GET['page'] == 'report-students' || $_GET['page'] == 'report-sales-product'){
        wp_enqueue_script('report',plugins_url('aes').'/admin/assets/js/report.js',array('jquery'),'1.0.0',true);
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');

        wp_localize_script('report','list_orders_sales',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_orders_sales' 
        ]);

        wp_localize_script('report','list_sales_product',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_sales_product' 
        ]);

        wp_localize_script('report','list_accounts_receivables',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_accounts_receivables' 
        ]);

        wp_localize_script('report','list_report_students',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'list_report_students' 
        ]);

        wp_localize_script('report','load_chart_data',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'load_chart_data' 
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

        wp_localize_script('student-documents','last_access_moodle',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'last_access_moodle' 
        ]);

        wp_localize_script('student-documents','get_approved_by',[
            'url' => admin_url( 'admin-ajax.php' ),
            'action' => 'get_approved_by' 
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

        
        add_menu_page(
            __('Invoice','aes'),
            __('Invoice','aes'),
            'read',
            'list_admin_institutes_invoice_content',
            'list_admin_institutes_invoice_content', 
            'dashicons-admin-page',
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
        __('Report','aes'),
        __('Report','aes'),
        'manager_report_aes', 
        'add_admin_form_report_content',
        'add_admin_form_report_content', 
        'dashicons-list-view', 
        7
    );

    add_submenu_page('add_admin_form_report_content',__('Sales','aes'),__('Sales','aes'),'manager_sales_aes','report-sales','show_report_sales', 10);
    add_submenu_page('add_admin_form_report_content',__('Accounts receivable','aes'),__('Accounts receivable','aes'),'manager_accounts_receivables_aes','report-accounts-receivables','show_report_accounts_receivables', 10);
    add_submenu_page('add_admin_form_report_content',__('Students','aes'),__('Students','aes'),'manager_report_students_aes','report-students','show_report_students', 10);
    add_submenu_page('add_admin_form_report_content',__('Sales by product','aes'),__('Sales by product','aes'),'manager_report_sales_product','report-sales-product','show_report_sales_product', 10);

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
        __('Academic periods','aes'),
        __('Academic periods','aes'),
        'manager_academic_periods_aes', 
        'add_admin_form_academic_periods_content',
        'add_admin_form_academic_periods_content', 
        'dashicons-welcome-learn-more', 
        7
    );

    add_menu_page( 
        __('Send email','aes'),
        __('Send email','aes'),
        'manager_send_email_aes', 
        'add_admin_form_send_email_content',
        'add_admin_form_send_email_content', 
        'dashicons-email-alt2', 
        8
    );

    add_menu_page( 
        __('Staff','aes'),
        __('Staff','aes'),
        'manager_staff_aes', 
        'add_admin_form_staff_content',
        'add_admin_form_staff_content', 
        'dashicons-buddicons-buddypress-logo', 
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
    $role->add_cap('manager_academic_periods_aes');
    $role->add_cap('manager_send_email_aes');
    $role->add_cap('manager_staff_aes');
    $role->add_cap('manager_admission_aes');
    $role->add_cap('manager_report_aes');
    $role->add_cap('manager_report_students_aes');
    $role->add_cap('manager_report_sales_product');
    $role->add_cap('manager_sales_aes');
    $role->add_cap('manager_accounts_receivables_aes');
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

function get_dates_search($filter,$custom){

    if($filter == 'today'){
        $start = get_gmt_from_date(wp_date('Y-m-d').'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d').'23:59','Y-m-d H:i');

    }else if($filter == 'yesterday'){
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('-1 days')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',).'00:00','Y-m-d H:i');

    }else if($filter == 'tomorrow'){
        $start = get_gmt_from_date(wp_date('Y-m-d').'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('+1 days')).'00:00','Y-m-d H:i');

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
        

    }else if($filter == 'next-week'){
        
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('this week')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('next week -1 days')).'23:59','Y-m-d H:i');
        

    }else if($filter == 'this-month'){
        
        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('first day of this month')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('last day of this month')).'23:59','Y-m-d H:i');
        
    }else if($filter == 'last-month'){

        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('first day of last month')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('last day of last month')).'23:59','Y-m-d H:i');
        
       
    }else if($filter == 'next-month'){

        $start = get_gmt_from_date(wp_date('Y-m-d',strtotime('first day of next month')).'00:00','Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d',strtotime('last day of next month')).'23:59','Y-m-d H:i');
        
       
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

// functions.php

function add_logo_dashboard(){
    $url = 'https://online.american-elite.us/wp-content/uploads/2024/06/cropped-cropped-cropped-American-elite-LOGO-1-600x188-1-2.png';
    $url_small = 'https://online.american-elite.us/wp-content/uploads/2024/06/cropped-cropped-cropped-American-elite-LOGO-1-600x188-1-2.png';
    echo '<style>
    @media screen and (min-width:992px){
        #toplevel_page_logo_based_menu {
            background-image: url('.$url.');
            background-size: 90%;
            background-repeat: no-repeat;
            margin: 10px !important;
            background-color: #ffffff;
            padding: 10px !important;
            border-radius: 10px;
            background-position: center;
        }
    }
    @media screen and (min-width:783px) and (max-width:991px){
        #toplevel_page_logo_based_menu {
            background-image: url('.$url_small.');
            background-size: 80%;
            background-repeat: no-repeat;
            margin: 5px !important;
            background-color: #ffffff;
            padding: 0px !important;
            border-radius: 6px;
            background-position: center;
            width: 70% !important;
        }
    }
    @media screen and (max-width:782px){
        #toplevel_page_logo_based_menu {
            background-image: url('.$url_small.');
            background-size: 20%;
            background-repeat: no-repeat;
            background-color: #ffffff !important;
            margin: 10px !important;
            width: 90% !important;
            border-radius: 10px;
            background-position: center;
        }
    }
    a.wp-first-item.wp-not-current-submenu.menu-top.menu-icon-generic.toplevel_page_logo_based_menu.menu-top-last{
        visibility:hidden;
    }
    a.toplevel_page_logo_based_menu{
        visibility: hidden !important;
    }
 </style>';
}
add_action('admin_enqueue_scripts', 'add_logo_dashboard');

function custom_login_store(){
if(get_option('blog_img_logo')){
    $url = 'https://online.american-elite.us/wp-content/uploads/2024/06/cropped-cropped-cropped-American-elite-LOGO-1-600x188-1-2.png';
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
}
add_action( 'login_enqueue_scripts', 'custom_login_store' );

function remove_text_admin_bar_profile( $wp_admin_bar ){
    $avatar = get_avatar( get_current_user_id(), 16 );
    if (!$wp_admin_bar->get_node( 'my-account' )){
        return;
    }
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => sprintf( '%s', wp_get_current_user()->user_firstname.' '.wp_get_current_user()->user_lastname ) . $avatar,
    ) );
}
add_action( 'admin_bar_menu', 'remove_text_admin_bar_profile' );

function aes_logo() {
    add_menu_page('logo', 'logo', 'read', 'logo_based_menu', '', '', 1);
}
add_action('admin_menu','aes_logo');

function hide_notices(){
    if (!is_super_admin()) {
        remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'admin_notices' );
    }
}
add_action('in_admin_header', 'hide_notices', 99);

add_action( 'login_enqueue_scripts', 'aes_change_login_logo' );
function aes_change_login_logo() { ?>
    <style type="text/css"> 
        #login h1 a { 
            background: url('http://online.american-elite.us/wp-content/uploads/2024/06/cropped-cropped-cropped-American-elite-LOGO-1-600x188-1-2-1.png') no-repeat center center; 
            background-size: 100px; 
            height: 100px; 
            margin: 0 auto; 
            width: 100px; 
        } 
    </style>
<?php }

add_filter('login_headerurl', 'aes_login_redirect_url');
function aes_login_redirect_url() {
    return 'https://online.american-elite.us/'; // Replace with your desired URL
}

// Add a custom action to the user list
add_filter( 'user_row_actions', 'add_welcome_student_action', 10, 2 );
function add_welcome_student_action( $actions, $user_object ) {
    // Get the user roles
    $user_roles = $user_object->roles;
    
    // Check if the user has the "student" role
    if ( in_array( 'student', $user_roles ) ) {
        $actions['welcome_student'] = '<a href="#" onclick="welcomeStudent(' . $user_object->ID . ')">Welcome Student</a>';
    }
    return $actions;
}

// Add a new column to the user list
function add_last_login_column($columns) {
    $columns['last_login'] = 'Last login';
    unset($columns['posts']);
    return $columns;
}
add_filter('manage_users_columns', 'add_last_login_column');

// Populate the last login column with user data
function populate_last_login_column($value, $column_name, $user_id) {
    if ($column_name == 'last_login') {
        $last_login = get_user_meta($user_id, 'last_login', true);
        if ($last_login) {
            return date_i18n('Y-m-d H:i:s', $last_login);
        } else {
            return 'N/A'; // or any other default value you want to display
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'populate_last_login_column', 10, 3);

function update_last_login($user_login, $user) {
    $current_time = current_time('timestamp');
    update_user_meta($user->ID, 'last_login', $current_time);
}
add_action('wp_login', 'update_last_login', 10, 2);

// Add a JavaScript code to trigger the welcome student function
add_action( 'admin_footer', 'add_welcome_student_js' );
function add_welcome_student_js() {
    ?>
    <script>
        function welcomeStudent(userId) {
            jQuery.post(ajaxurl, {
                'action': 'welcome_student',
                'user_id': userId
            }, function(response) {
                // console.log(response);
            });
        }
    </script>
    <?php
}

// Handle the AJAX request to trigger the welcome student function
add_action( 'wp_ajax_welcome_student', 'welcome_student_ajax_handler' );
function welcome_student_ajax_handler() {
    $user_id = $_POST['user_id'];
    $user = get_userdata( $user_id );
    welcome_students($user->user_email);
    wp_die();
}

function welcome_students($user_login) {
    // Get the student ID from the user data
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$user_login}'");
    if ($student) {
        $student_id = $student->id;
        $user = get_user_by('email', $user_login);
        $reset_key = get_password_reset_key($user);
        $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

        // Get the WC_Request_Documents_Email instance
        $email_welcome_student = WC()->mailer()->get_emails()['WC_Welcome_Student_Email'];
    
        // Trigger the custom email with the reset URL
        $email_welcome_student->trigger($student_id, $reset_url);

        // Display a success notice to the admin
        error_log('Student welcome email sent successfully!');
    } else {
        // Display an error notice to the admin
        error_log('Failed to send student welcome email.');
    }
}

function admin_notice($message, $type = 'success') {
    ?>
    <div class="notice notice-<?php echo $type; ?> is-dismissible">
        <p><?php echo $message; ?></p>
    </div>
    <?php
}

function get_states_by_country() {
    $country_code = $_POST['country_code'];
    $wc_countries = new WC_Countries();
    $states = $wc_countries->get_states($country_code);
    echo json_encode($states);
    exit;
  }
  
  add_action('wp_ajax_get_states_by_country', 'get_states_by_country');
  add_action('wp_ajax_nopriv_get_states_by_country', 'get_states_by_country');