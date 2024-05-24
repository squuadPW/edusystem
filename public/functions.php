<?php

require plugin_dir_path( __FILE__ ) . 'student.php';
require plugin_dir_path( __FILE__ ) . 'document.php';

function form_plugin_scripts(){
    wp_enqueue_style('admin-flatpickr',plugins_url('form-plugin').'/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('style-public',plugins_url('form-plugin').'/public/assets/css/style.css');
    wp_enqueue_script('tailwind','https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-flatpickr',plugins_url('form-plugin').'/public/assets/js/flatpickr.js');
    wp_enqueue_script('masker-js',plugins_url('form-plugin').'/public/assets/js/vanilla-masker.min.js');
    wp_enqueue_script('checkout',plugins_url('form-plugin').'/public/assets/js/checkout.js');
    wp_enqueue_script('form-register',plugins_url('form-plugin').'/public/assets/js/form-register.js');

}

add_action( 'wp_enqueue_scripts', 'form_plugin_scripts');

function removed_hooks(){
    remove_action('storefront_footer', 'storefront_credit', 20 );
    remove_action('storefront_header','storefront_header_cart',60);
}

add_action('init','removed_hooks');

function save_student(){

    if(
        isset($_GET['action']) && !empty($_GET['action'])
    ){
        if($_GET['action'] == 'save_student'){

            global $woocommerce;

            $name = $_POST['name_student'];
            $last_name = $_POST['lastname_student'];
            $number_phone = $_POST['number_phone'];
            $number_partner = $_POST['number_partner']; 
            $email_student = $_POST['email_student'];
            $email_partner = $_POST['email_partner'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $birth_date = $_POST['birth_date'];
            $agent_name = $_POST['agent_name'];
            $agent_last_name = $_POST['agent_last_name'];
            $program = $_POST['program'];
            $grade = $_POST['grade'];
            $name_institute = $_POST['name_institute'];

            /* set cookie */
            setcookie('name_student',ucwords($name),time() + 3600);
            setcookie('last_name_student',ucwords($last_name),time() + 3600);
            setcookie('billing_city',$city,time() + 3600);
            setcookie('billing_country',$country,time() + 3600);
            setcookie('billing_phone',$number_phone,time() + 3600);
            setcookie('billing_email',$email_student,time() + 3600);
            setcookie('initial_grade',$grade,time() + 3600);
            setcookie('name_institute',$name_institute,time() + 3600);
            setcookie('birth_date',$birth_date,time() + 3600);
            setcookie('grade',$grade,time() + 3600);
            setcookie('name_institute',$name_institute,time() + 3600);
            setcookie('program_id',$program,time() + 3600);
            setcookie('agent_name',$agent_name,time() + 3600);
            setcookie('agent_last_name',$agent_last_name,time() + 3600);
            setcookie('email_partner',$email_partner,time() + 3600);
            setcookie('number_partner',$number_partner,time() + 3600);

            //clear cart
            $woocommerce->cart->empty_cart(); 

            //add program to cart
            if($program == 'aes'){
                $woocommerce->cart->add_to_cart(103,1);
            }else if($program == 'psp'){
                $woocommerce->cart->add_to_cart(102,1);
            }else if($program == 'aes_psp'){
                $woocommerce->cart->add_to_cart(103,1);
                $woocommerce->cart->add_to_cart(102,1);
            }

            wp_redirect(wc_get_checkout_url());
            exit;
        };

    }
}

add_action('wp_loaded','save_student');

function form_asp_psp(){

    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/asp-psp-registration.php');
}

add_shortcode('form_asp_psp','form_asp_psp');

function form_register_agreement(){
    include(plugin_dir_path(__FILE__).'templates/register-agreement.php');
}

add_shortcode('form_register_agreement', 'form_register_agreement');


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

add_filter( 'default_checkout_billing_country', 'change_default_checkout_country', 10, 1 );

function change_default_checkout_country($country){
    if(isset($_COOKIE['billing_country']) && !empty($_COOKIE['billing_country'])){
       return $_COOKIE['billing_country'];
    }
}

function woocommerce_checkout_order_created_action($order){
    $customer_id = $order->get_customer_id();
    update_user_meta($customer_id,'status_register',0);

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
        + array( 'student' => __('Student','form-plugin') )
        + array_slice( $menu_links, 2, NULL, true );
    }

    return $menu_links;
}

add_action('init', function() {
	add_rewrite_endpoint('student-documents', EP_ROOT | EP_PAGES);
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
    if(isset( $_POST['billing_city'])){
        update_user_meta( $user_id,'billing_city',sanitize_text_field($_POST['billing_city']));
    }

    if(isset( $_POST['billing_country'])){
        update_user_meta( $user_id,'billing_country',sanitize_text_field( $_POST['billing_country']));
    }

    if(isset( $_POST['number_phone'])){
        update_user_meta( $user_id,'billing_phone',sanitize_text_field( $_POST['number_phone']));
    }
}
add_action( 'woocommerce_save_account_details', 'save_account_details' );


