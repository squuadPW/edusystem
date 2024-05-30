<?php

require plugin_dir_path( __FILE__ ) . 'student.php';
require plugin_dir_path( __FILE__ ) . 'institute.php';
require plugin_dir_path( __FILE__ ) . 'document.php';
require plugin_dir_path( __FILE__ ) . 'partner.php';

function form_plugin_scripts(){
    wp_enqueue_style('dashicons');
    wp_enqueue_style('admin-flatpickr',plugins_url('aes').'/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('style-public',plugins_url('aes').'/public/assets/css/style.css');
    wp_enqueue_script('tailwind','https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-flatpickr',plugins_url('aes').'/public/assets/js/flatpickr.js');
    wp_enqueue_script('masker-js',plugins_url('aes').'/public/assets/js/vanilla-masker.min.js');
    wp_enqueue_script('checkout',plugins_url('aes').'/public/assets/js/checkout.js');
    wp_enqueue_script('form-register',plugins_url('aes').'/public/assets/js/form-register.js');

}

add_action( 'wp_enqueue_scripts', 'form_plugin_scripts');

function removed_hooks(){
    remove_action('storefront_footer', 'storefront_credit', 20 );
    remove_action('storefront_header','storefront_header_cart',60);
}

add_action('init','removed_hooks');

function form_asp_psp(){
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    include(plugin_dir_path(__FILE__).'templates/asp-psp-registration.php');
}

add_shortcode('form_asp_psp','form_asp_psp');

function get_countries(){
    $wc_countries = new WC_Countries();
    $countries = $wc_countries->get_countries();
    return $countries;
}

add_filter( 'woocommerce_checkout_fields', 'removed_custom_checkout_fields');

function removed_custom_checkout_fields($fields){

    unset( $fields['billing']['billing_company']);
    unset( $fields['billing']['billing_address_1']);
    unset( $fields['billing']['billing_address_2']);
    unset( $fields['billing']['billing_state']);
    unset( $fields['billing']['billing_postcode']);
    unset( $fields['shipping']['shipping_first_name']);
    unset( $fields['shipping']['shipping_last_name']);
    unset( $fields['shipping']['shipping_address_1']);
    unset( $fields['shipping']['shipping_address_2']);
    unset( $fields['shipping']['shipping_city']);
    unset( $fields['shipping']['shipping_state']);
    unset( $fields['shipping']['shipping_postcode']);
    unset( $fields['shipping']['shipping_country']);   
    unset( $fields['order']['order_comments']);   
        
    return $fields;
}

function change_default_checkout_country($country){
    if(isset($_COOKIE['billing_country']) && !empty($_COOKIE['billing_country'])){
       return $_COOKIE['billing_country'];
    }
}

function woocommerce_checkout_order_created_action($order){
    $customer_id = $order->get_customer_id();

    if(!get_user_meta($customer_id,'status_register',true)){
        update_user_meta($customer_id,'status_register',0);
    }

    if(
        isset($_COOKIE['name_student']) && !empty($_COOKIE['name_student']) &&
        isset($_COOKIE['last_name_student']) && !empty($_COOKIE['last_name_student']) &&
        isset($_COOKIE['birth_date']) && !empty($_COOKIE['birth_date']) && 
        isset($_COOKIE['name_institute']) && !empty($_COOKIE['name_institute']) &&
        isset($_COOKIE['initial_grade']) && !empty($_COOKIE['initial_grade']) &&
        isset($_COOKIE['program_id']) && !empty($_COOKIE['program_id']) &&
        isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner']) &&
        isset($_COOKIE['number_partner']) && !empty($_COOKIE['number_partner'])
    ){

        $student_id = insert_student($customer_id);
        insert_register_documents($student_id);

        $order->update_meta_data('student_id',$student_id);
        $order->save();
    }

    setcookie('name_student','',time());
    setcookie('last_name_student','',time());
    setcookie('billing_city','',time());
    setcookie('billing_country','',time());
    setcookie('billing_phone','',time());
    setcookie('billing_email','',time());
    setcookie('initial_grade','',time());
    setcookie('name_institute','',time());
    setcookie('birth_date','',time());
    setcookie('program_id','',time());
    setcookie('agent_name',$agent_name,time() + 3600);
    setcookie('agent_last_name',$agent_last_name,time() + 3600);
    setcookie('email_partner',$email_partner,time() + 3600);
    setcookie('number_partner',$number_partner,time() + 3600);
}

add_action( 'woocommerce_checkout_order_created', 'woocommerce_checkout_order_created_action' );

add_filter( 'woocommerce_checkout_fields', 'custom_override_value_checkout_fields');

function custom_override_value_checkout_fields($fields){

    if(isset($_COOKIE['agent_name']) && !empty($_COOKIE['agent_name'])){
        $fields['billing']['billing_first_name']['default'] = $_COOKIE['agent_name'];
    }

    if(isset($_COOKIE['agent_last_name']) && !empty($_COOKIE['agent_last_name'])){
        $fields['billing']['billing_last_name']['default'] = $_COOKIE['agent_last_name'];
    }

    if(isset($_COOKIE['billing_city']) && !empty($_COOKIE['billing_city'])){
        $fields['billing']['billing_city']['default'] = $_COOKIE['billing_city'];
    }

    if(isset($_COOKIE['billing_country']) && !empty($_COOKIE['billing_country'])){
        $fields['billing']['billing_country']['default'] = $_COOKIE['billing_country'];
    }

    if(isset($_COOKIE['number_partner']) && !empty($_COOKIE['number_partner'])){
        $fields['billing']['billing_phone']['default'] = $_COOKIE['number_partner'];
    }

    if(isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner'])){
        $fields['billing']['billing_email']['default'] = $_COOKIE['email_partner'];
    }
  
    return $fields;
}



function display_in_order_detail($order){

    if($order->get_meta('student_id')){

        $user = get_userdata($order->get_meta('student_id'));

        if($user){
            echo '<p><strong>'.__('Student','form-plugin').':</strong> <a href="'.admin_url('user-edit.php?user_id='.$order->get_meta('student_id')).'">' .$user->first_name.' '.$user->last_name. '</a></p>';
        }else{
            echo '<p><strong>'.__('Student','form-plugin').':</strong> ' .$order->get_meta('student_id'). '</p>';
        }
    }

    if($order->get_meta('grade')){

        $grade = $order->get_meta('grade');

        $value = match($grade){
            '1' => '9no (antepenúltimo)',
            '2' => '10mo (penúltimo)',
            '3' => '11vo (último)',
            '4' => 'Bachiller (graduado)'
        };

        echo '<p><strong>'.__('Grade','form-plugin').':</strong> ' .$value. '</p>';
    }

	if($order->get_meta('name_institute')){

        echo '<p><strong>'.__('Name Institute','form-plugin').':</strong> ' .$order->get_meta('name_institute'). '</p>';
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_in_order_detail', 10, 1 );

add_filter( 'woocommerce_account_menu_items', 'remove_my_account_links' );

function remove_my_account_links( $menu_links ){

    global $current_user;
    $roles = $current_user->roles;
    $user_id = $current_user->ID;

    if(in_array('partner',$roles)){
        $menu_links['dashboard'] = __('Partner Panel','form-plugin');
        $menu_links['orders'] = __('Payments','form-plugin');
        unset($menu_links['downloads']);
        unset($menu_links['edit-address']);
        unset($menu_links['payment-methods']);

        if(get_user_meta($user_id,'status_register',true) == 1 || get_user_meta($user_id,'status_register',true) == '1'){

            $menu_links = array_slice( $menu_links, 0,2 , true )
            + array( 'student-documents' => __('Documents','form-plugin') )
            + array_slice( $menu_links, 2, NULL, true );
        }

        $menu_links = array_slice( $menu_links, 0,2 , true )
        + array( 'student' => __('Students','form-plugin') )
        + array_slice( $menu_links, 2, NULL, true );
    }

    return $menu_links;
}

add_action('init', function() {
	add_rewrite_endpoint('student-documents', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student-details', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student', EP_ROOT | EP_PAGES);
});

function redirect_to_my_account(){

    global $current_user;
    $roles= $current_user->roles;

    if(in_array('partner',$roles)){
        wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id')).'/orders' );
        exit();
    }
}

add_action('woocommerce_thankyou', 'redirect_to_my_account', 10, 1);


function modify_columns_orders($columns = []){
    $columns['order-number'] = __( 'Payment ID', 'form-plugin');
    return $columns;
}

add_filter( 'woocommerce_account_orders_columns', 'modify_columns_orders' );

add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 2 );

function add_loginout_link( $items, $args ){

    if(is_user_logged_in() && $args->theme_location == 'primary') {
        $logout_link = wp_logout_url( get_home_url() );
        $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'">'.__('Partner Panel','form-plugin').'</a></li>';
        $items .= '<li><a href="'.$logout_link.'">'.__('Log out','form-plugin').'</a></li>';
    }

    elseif (!is_user_logged_in() && $args->theme_location == 'primary') {
        $items .= '<li><a href="' .get_permalink( get_option('woocommerce_myaccount_page_id') ). '">'.__('Log in','form-plugin').'</a></li>';
    }

    return $items;
}

function filter_woocommerce_new_customer_data( $args ) {

    if (is_checkout()){
        $args['role'] = 'partner';
    }

    return $args;
}
add_filter( 'woocommerce_new_customer_data', 'filter_woocommerce_new_customer_data', 10, 1 );

function status_changed_payment($order_id, $old_status, $new_status){

    $order = wc_get_order($order_id);
    $customer_id = $order->get_customer_id();
    $status_register = get_user_meta($customer_id,'status_register',true);

    if($order->get_status() == 'completed'){
        update_user_meta($customer_id,'status_register',1);

        if($order->get_meta('student_id')){

            $student_id = $order->get_meta('student_id');
            update_status_student($student_id,1);
        }

    }else{

        if($status_register != 1 && $status_register != '1'){
            update_user_meta($customer_id,'status_register',0);
        }

    }
    
}

add_action('woocommerce_order_status_changed', 'status_changed_payment', 10, 3);

function save_account_details( $user_id ) {
    if(isset( $_POST['billing_city']) && !empty($_POST['billing_city'])){
        update_user_meta( $user_id,'billing_city',sanitize_text_field($_POST['billing_city']));
    }

    if(isset( $_POST['billing_country']) && !empty($_POST['billing_country'])){
        update_user_meta( $user_id,'billing_country',sanitize_text_field( $_POST['billing_country']));
    }

    if(isset( $_POST['number_phone']) && !empty($_POST['number_phone'])){
        update_user_meta( $user_id,'billing_phone',sanitize_text_field( $_POST['number_phone']));
    }

    if(isset( $_POST['gender']) && !empty($_POST['gender'])){
        update_user_meta( $user_id,'gender',sanitize_text_field( $_POST['gender']));
    }

    if(isset( $_POST['id_document']) && !empty($_POST['id_document'])){
        update_user_meta( $user_id,'id_document',sanitize_text_field( $_POST['id_document']));
    }

    if(isset( $_POST['birth_date']) && !empty($_POST['birth_date'])){
        update_user_meta( $user_id,'birth_date',sanitize_text_field( $_POST['birth_date']));
    }

    if(isset( $_POST['document_type']) && !empty($_POST['document_type'])){
        update_user_meta( $user_id,'document_type',sanitize_text_field( $_POST['document_type']));
    }

    if(isset( $_POST['billing_postcode']) && !empty($_POST['billing_postcode'])){
        update_user_meta( $user_id,'billing_postcode',sanitize_text_field( $_POST['billing_postcode']));
    }

    if(isset( $_POST['occupation']) && !empty($_POST['occupation'])){
        update_user_meta( $user_id,'occupation',sanitize_text_field( $_POST['occupation']));
    }
}
add_action( 'woocommerce_save_account_details', 'save_account_details' );

function validated_account_details_required_fields( $required_fields ){    
    $required_fields['billing_city'] = __('Billing city','aes');
    $required_fields['billing_country'] = __('Billing country','aes');
    $required_fields['number_phone'] = __('Number phone','aes');
    $required_fields['gender'] = __('Gender','aes');
    $required_fields['birth_date'] = __('Birth Date','aes');
    $required_fields['id_document'] = __('ID Document','aes');
    $required_fields['document_type'] = __('Type document','aes');
    $required_fields['billing_postcode'] = __('Post Code','aes');
    $required_fields['occupation'] = __('Occupation','aes');
    return $required_fields;
}
add_filter('woocommerce_save_account_details_required_fields', 'validated_account_details_required_fields');


function save_student_details(){

    if(isset($_POST['action']) && !empty($_POST['action'])){


        if($_POST['action'] == 'save_student_details'){

            global $wpdb;
            $table_students = $wpdb->prefix.'students';
           
            $student_id = $_POST['student_id'];
            $document_type = $_POST['document_type'];
            $id_document = $_POST['id_document'];
            $first_name = $_POST['account_first_name'];
            $last_name = $_POST['account_last_name'];
            $email = $_POST['account_email'];
            $phone = $_POST['number_phone'];
            $gender = $_POST['gender'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $postal_code = $_POST['postal_code'];

            $wpdb->update($table_students,[
                'type_document' => $document_type,
                'id_document' => $id_document,
                'name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'country' => $country,
                'city' => $city,
                'postal_code' => $postal_code,
            ],[
                'id' => $student_id
            ]);

            wc_add_notice(__( 'information changed successfully.', 'aes' ), 'success' );
            wp_redirect(wc_get_account_endpoint_url('student-details').'/?student='.$student_id);
            exit;
        }
    }
}


add_action('wp_loaded','save_student_details');