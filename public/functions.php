<?php

require plugin_dir_path( __FILE__ ) . 'student-scholarship.php';
require plugin_dir_path( __FILE__ ) . 'student.php';
require plugin_dir_path( __FILE__ ) . 'account.php';
require plugin_dir_path( __FILE__ ) . 'institute.php';
require plugin_dir_path( __FILE__ ) . 'document.php';
require plugin_dir_path( __FILE__ ) . 'alliances.php';
require plugin_dir_path( __FILE__ ) . 'user.php';
require plugin_dir_path( __FILE__ ) . 'notes.php';
require plugin_dir_path( __FILE__ ) . 'academic_services.php';

function form_plugin_scripts(){
    wp_enqueue_style('dashicons');
    wp_enqueue_style('admin-flatpickr',plugins_url('aes').'/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('intel-css',plugins_url('aes').'/public/assets/css/intlTelInput.css');
    wp_enqueue_style('style-public',plugins_url('aes').'/public/assets/css/style.css', '1.0', 'all');
    wp_enqueue_script('tailwind','https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-flatpickr',plugins_url('aes').'/public/assets/js/flatpickr.js');
    wp_enqueue_script('masker-js',plugins_url('aes').'/public/assets/js/vanilla-masker.min.js');
    wp_enqueue_script('intel-js',plugins_url('aes').'/public/assets/js/intlTelInput.min.js');
    wp_enqueue_script('checkout',plugins_url('aes').'/public/assets/js/checkout.js');
    wp_enqueue_script('form-register',plugins_url('aes').'/public/assets/js/form-register.js');
    wp_enqueue_script('int-tel',plugins_url('aes').'/public/assets/js/int-tel.js');

    // PAYMENTS PARTS
    wp_register_script('payment-parts-update',plugins_url('aes').'/public/assets/js/payment-parts-update.js', array('jquery'), '1.0.0', true);
    wp_localize_script('payment-parts-update', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
    wp_enqueue_script('payment-parts-update');
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
    $grades = get_grades();
    include(plugin_dir_path(__FILE__).'templates/asp-psp-registration.php');
}

add_shortcode('form_asp_psp','form_asp_psp');

function form_scholarship_application(){
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
    include(plugin_dir_path(__FILE__).'templates/scholarship-application.php');
}

add_shortcode('form_scholarship_application','form_scholarship_application');

function get_countries(){
    $wc_countries = new WC_Countries();
    $countries = $wc_countries->get_countries();
    return $countries;
}

add_filter( 'woocommerce_checkout_fields', 'removed_custom_checkout_fields');

function removed_custom_checkout_fields($fields){

    unset( $fields['billing']['billing_company']);
    // unset( $fields['billing']['billing_address_1']);
    // unset( $fields['billing']['billing_address_2']);
    // unset( $fields['billing']['billing_state']);
    // unset( $fields['billing']['billing_postcode']);
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
       $country = $_COOKIE['billing_country'];
    }
    return $country;
}

add_filter('default_checkout_billing_country','change_default_checkout_country');

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
        insert_register_documents($student_id,$_COOKIE['initial_grade']);
        
        $order->update_meta_data('student_id',$student_id);
        $order->save();

        $email_new_student = WC()->mailer()->get_emails()['WC_New_Applicant_Email'];
        $email_new_student->trigger($student_id);

        insert_data_student($order);
    }

    if(isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])){
        add_role_user($customer_id,'parent');
    }


    //validate cookie and set metadata
    if (isset($_COOKIE['fee_student_id']) && !empty($_COOKIE['fee_student_id'])) {
        $order->update_meta_data('student_id', $_COOKIE['fee_student_id']);
        $order->save();
    }

    set_institute_in_order($order);

    setcookie('name_student','',time());
    setcookie('middle_name_student','',time());
    setcookie('middle_last_name_student','',time());
    setcookie('last_name_student','',time());
    setcookie('billing_city','',time());
    setcookie('billing_country','',time());
    setcookie('billing_phone','',time());
    setcookie('billing_email','',time());
    setcookie('initial_grade','',time());
    setcookie('institute_name','',time());
    setcookie('institute_id','',time());
    setcookie('birth_date','',time());
    setcookie('program_id','',time());
    setcookie('agent_name','',time());
    setcookie('agent_last_name','',time());
    setcookie('email_partner','',time());
    setcookie('number_partner','',time());
    setcookie('name_institute','',time());
    setcookie('is_older','',time());
    setcookie('fee_student_id','',time()); // Don't forget to delete the cookie after using it
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
        $fields['billing']['billing_phone']['default'] = sanitize_text_field($_COOKIE['number_partner']);
        $fields['billing']['billing_phone_hidden']['default'] = sanitize_text_field($_COOKIE['number_partner']);
    }

    if(isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner'])){
        $fields['billing']['billing_email']['default'] = $_COOKIE['email_partner'];
    }
  
    return $fields;
}

function change_billing_phone_checkout_field_value($order,$data){
    
    if(isset($_POST['billing_phone_hidden']) && !empty($_POST['billing_phone_hidden'])){ 
        $order->set_billing_phone($_POST['billing_phone_hidden']);
        update_user_meta($order->get_customer_id(),'billing_phone',$_POST['billing_phone_hidden']);
    }
}

add_action( 'woocommerce_checkout_create_order', 'change_billing_phone_checkout_field_value', 10, 2 );

add_filter( 'woocommerce_account_menu_items', 'remove_my_account_links' );

function remove_my_account_links( $menu_links ){

    global $current_user;
    $roles = $current_user->roles;
    $user_id = $current_user->ID;

    if(in_array('parent',$roles) || in_array('student',$roles)){

        $menu_links['dashboard'] = __('Dashboard','form-plugin');

        if(in_array('parent',$roles)){
            $menu_links['orders'] = __('Payments','form-plugin');
        }else{
            unset($menu_links['orders']);
        }


        unset($menu_links['downloads']);
        unset($menu_links['edit-address']);
        unset($menu_links['payment-methods']);

        if(in_array('parent',$roles) && in_array('student',$roles)){

            if(get_user_meta($user_id,'status_register',true) == 1 || get_user_meta($user_id,'status_register',true) == '1'){

                $menu_links = array_slice( $menu_links, 0,2 , true )
                + array( 'student-documents' => __('Documents','form-plugin') )
                + array_slice( $menu_links, 2, NULL, true );
            }

        }else if(in_array('parent',$roles) && !in_array('student',$roles)){

            if(get_user_meta($user_id,'status_register',true) == 1 || get_user_meta($user_id,'status_register',true) == '1'){

                $menu_links = array_slice( $menu_links, 0,2 , true )
                + array( 'student-documents' => __('Documents','form-plugin') )
                + array_slice( $menu_links, 2, NULL, true );
            }

        }else if(!in_array('parent',$roles) && in_array('student',$roles)){

            $menu_links = array_slice( $menu_links, 0,1 , true )
            + array( 'student-documents' => __('Documents','aes') )
            + array_slice( $menu_links,1, NULL, true );
        }

        if(in_array('parent',$roles) && in_array('student',$roles)){

            $menu_links = array_slice( $menu_links, 0,2 , true )
            + array( 'student' => __('Student Information','aes') )
            + array_slice( $menu_links, 2, NULL, true );

        }else if(in_array('parent',$roles) && !in_array('student',$roles)){

            $menu_links = array_slice( $menu_links, 0,2 , true )
            + array( 'student' => __('Students Information','aes') )
            + array_slice( $menu_links, 2, NULL, true );

        }else if(!in_array('parent',$roles) && in_array('student',$roles)){

            $menu_links = array_slice( $menu_links, 0,1 , true )
            + array( 'student' => __('Student Information','aes') )
            + array_slice( $menu_links, 1, NULL, true );
        }
        /*
        if(in_array('student',$roles) && in_array('parent',$roles)){

            $menu_links = array_slice( $menu_links, 0,3 , true )
            + array( 'notes' => __('Notes','aes') )
            + array_slice( $menu_links, 3, NULL, true );

        }else if(!in_array('parent',$roles) && in_array('student',$roles)){
            
            $menu_links = array_slice( $menu_links, 0,3 , true )
            + array( 'notes' => __('Notes','aes') )
            + array_slice( $menu_links, 2, NULL, true );
        }

        if(in_array('student',$roles) && in_array('parent',$roles)){

            $menu_links = array_slice( $menu_links, 0,3 , true )
            + array( 'academic-services' => __('Academic Services','aes') )
            + array_slice( $menu_links, 3, NULL, true );

        }else if(!in_array('parent',$roles) && in_array('student',$roles)){
            
            $menu_links = array_slice( $menu_links, 0,3 , true )
            + array( 'academic-services' => __('Academic Services','aes') )
            + array_slice( $menu_links, 2, NULL, true );
        }
        */
    }

    return $menu_links;
}

add_action('init', function() {
	add_rewrite_endpoint('student-documents', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student-details', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('notes', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('academic-services', EP_ROOT | EP_PAGES);
});

function redirect_to_my_account(){

    global $current_user;
    $roles= $current_user->roles;

    if(in_array('parent',$roles) || in_array('student',$roles)){
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

    if(is_user_logged_in()) {
        $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'">'.__('Dashboard','form-plugin').'</a></li>';
        if ($args->theme_location != 'primary') {
            $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'/orders">'.__('Payments','form-plugin').'</a></li>';
            $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'/student">'.__('Students information','form-plugin').'</a></li>';
            $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'/student-documents">'.__('Documents','form-plugin').'</a></li>';
            $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'/edit-account">'.__('Account details','form-plugin').'</a></li>';
        }
        $logout_link = wp_logout_url( get_home_url() );
        $items .= '<li><a class="button-primary" style="font-size:14px;" href="'.$logout_link.'">'.__('Log out','form-plugin').'</a></li>';
    }

    elseif (!is_user_logged_in()) {
        $items .= '<li><a class="button-primary" style="font-size:14px;" href="' .get_permalink( get_option('woocommerce_myaccount_page_id') ). '">'.__('Log in','form-plugin').'</a></li>';
    }

    return $items;
}

function status_changed_payment($order_id, $old_status, $new_status){
    global $wpdb, $current_user;
    $order = wc_get_order($order_id);
    $customer_id = $order->get_customer_id();
    $status_register = get_user_meta($customer_id,'status_register',true);
    $table_student_payment = $wpdb->prefix.'student_payments';

    if($order->get_status() == 'completed'){

        if (isset($status_register)) {
            update_user_meta($customer_id,'status_register',1);
        }

        if($order->get_meta('student_id')){
            $student_id = $order->get_meta('student_id');

            $wpdb->query("UPDATE {$table_student_payment} SET status_id = 1 WHERE student_id = {$student_id} and order_id = {$order_id}");

            update_status_student($student_id,1);
            
            $email_request_documents = WC()->mailer()->get_emails()['WC_Request_Documents_Email'];
            $email_request_documents->trigger($student_id);
            
            return $data->url;
        }   

    }else{

        if($status_register != 1 && $status_register != '1'){
            update_user_meta($customer_id,'status_register',0);
        }


        // FOR PROGRAM PAYMENT (AES PROGRAM)
        $items = $order->get_items();
        $date = new DateTime('August 12');
        $date = $date->format('Y-m-d');
        $student_id = $order->get_meta('student_id');

        foreach ($items as $item) {
            $cuotes = 1;
            $date_calc = '';
            $product_id = $item->get_product()->get_id();
            $is_variable = $item->get_product()->is_type('variation');
            $price = $item->get_product()->get_price();

            $exist = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = {$product_id} and order_id = {$order_id}");
            if (!$exist) {

                if($is_variable) {
                    $product = $item->get_product();
                    $variation_attributes = $product->get_variation_attributes();
                    foreach($variation_attributes as $attribute_taxonomy => $term_slug ){
                        $taxonomy = str_replace('attribute_', '', $attribute_taxonomy );
                        $attribute_name = wc_attribute_label( $taxonomy, $product );
                        if( taxonomy_exists($taxonomy) ) {
                            $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                        } else {
                            $attribute_value = $term_slug;
                        }
                    }
    
                    switch ($attribute_value) {
                        case 'Annual':
                            $date_calc = '+1 year';
                            break;
                        case 'Semiannual':
                            $date_calc = '+6 months';
                            break;
                    }

                    $cuotes = $product->get_meta('num_cuotes_text') ? $product->get_meta('num_cuotes_text') : 1;
                }

                $data = array(
                    'status_id' => 0, 
                    'order_id' => $order_id, 
                    'student_id' => $student_id, 
                    'product_id' => $product_id, // Use the new variable here
                    'amount' => $price, 
                    'type_payment' => $cuotes > 1 ? 1 : 2, 
                    'cuote' => 1, 
                    'num_cuotes' => $cuotes, 
                    'date_payment' => date('Y-m-d'), 
                    'date_next_payment' => $cuotes > 1 ? date('Y-m-d', strtotime($date_calc, strtotime($date))) : date('Y-m-d'), 
                );
                $wpdb->insert($wpdb->prefix.'student_payments', $data);
            }
        }
        // FOR PROGRAM PAYMENT (AES PROGRAM)

        // FEE DE INSCRIPCION
        $product = null;
        foreach ($items as $item) {
            $product = $item->get_product()->get_id() == AES_FEE_INSCRIPTION ? $item->get_product() : $product;
        }
        if (isset($product)) {
            $roles = $current_user->roles;
            $table_student_documents = $wpdb->prefix.'student_documents';
            $table_student_payment = $wpdb->prefix.'student_payments';
            $access_virtual = true;
            $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");

            if($documents_student){
                foreach($documents_student as $document){
                    if($document->status != 5){
                        $access_virtual = false;
                    }
                }

                // VERIFICAR FEE DE INSCRIPCION
                $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = ". AES_FEE_INSCRIPTION);
                // VERIFICAR FEE DE INSCRIPCION

                //virtual classroom
                if($access_virtual && isset($paid)){

                    update_status_student($student_id,2);

                    if(in_array('parent',$roles) && !in_array('student',$roles)){
                        create_user_student($student_id);
                    }

                    $exist = is_search_student_by_email($student_id);
                
                    if(!$exist){
                        create_user_moodle($student_id);
                    }else{
                        $wpdb->update($table_students,['moodle_student_id' => $exist[0]['id']],['id' => $student_id]);

                        $is_exist_password = is_password_user_moodle($student_id);

                        if(!$is_exist_password){
                            
                            $password = generate_password_user();
                            $wpdb->update($table_students,['moodle_password' => $password],['id' => $student_id]);
                            change_password_user_moodle($student_id);
                        }
                    }
                }
            }
        }
        // FEE DE INSCRIPCION

    }  
}
            

add_action('woocommerce_order_status_changed', 'status_changed_payment', 10, 3);

function insert_data_student($order){

    if(isset($_COOKIE['institute_id']) && !empty($_COOKIE['institute_id'])){

        $institute = get_institute_details($institute_id);

        $data_student = [
            'birth_date' => $_COOKIE['birth_date'],
            'name_student' => $_COOKIE['name_student'],
            'middle_name_student' => $_COOKIE['middle_name_student'],
            'last_name_student' => $_COOKIE['middle_name_student'],
            'phone_student' => $_COOKIE['phone_student'],
            'email_student' => $_COOKIE['email_student'],
            'initial_grade' => get_name_grade($_COOKIE['initial_grade']),
            'program' => get_name_program($_COOKIE['program_id']),
            'name_institute' => $institute->name,
            'country' => get_name_country($_POST['billing_country']),
            'state' => $_POST['billing_city'],
            'parent_name' => $_POST['agent_name'],
            'parent_last_name' => $_POST['agent_last_name'],
            'parent_email' => $_POST['email_partner'],
            'parent_number' => $_POST['number_partner'],
        ];

    }else{

        $data_student = [
            'birth_date' => $_COOKIE['birth_date'],
            'name_student' => $_COOKIE['name_student'],
            'middle_name_student' => $_COOKIE['middle_name_student'],
            'last_name_student' => $_COOKIE['middle_name_student'],
            'phone_student' => $_COOKIE['phone_student'],
            'email_student' => $_COOKIE['email_student'],
            'initial_grade' => get_name_grade($_COOKIE['initial_grade']),
            'program' => get_name_program($_COOKIE['program_id']),
            'name_institute' => $_COOKIE['name_institute'],
            'country' => get_name_country($_POST['billing_country']),
            'state' => $_POST['billing_city'],
            'parent_name' => $_POST['agent_name'],
            'parent_last_name' => $_POST['agent_last_name'],
            'parent_email' => $_POST['email_partner'],
            'parent_number' => $_POST['number_partner'],
        ];
    }

    $order->update_meta_data('student_data',$data_student);
    $order->save();
}

add_action('woocommerce_after_checkout_billing_form', 'payments_parts');
function payments_parts() {
    include(plugin_dir_path(__FILE__).'templates/payment-parts.php');
}

add_action( 'wp_ajax_nopriv_woocommerce_update_cart', 'woocommerce_update_cart');
add_action( 'wp_ajax_woocommerce_update_cart', 'woocommerce_update_cart');

function woocommerce_update_cart() {
    global $woocommerce;
    $coupon_code = '';
    $has_coupon = false;
    $cart =  $woocommerce->cart;
    $applied_coupons =  $woocommerce->cart->get_applied_coupons();
    $products_id = [];
    if ( count( $applied_coupons ) > 0 ) {
        $has_coupon = true;
        foreach ($applied_coupons as $key => $coupon) {
            $coupon_code = $coupon;
        }
    }

    $value = $_POST['option'];
    foreach ($cart->get_cart() as $key => $product) {
        array_push($products_id, $product['product_id']);
    }

    $woocommerce->cart->empty_cart();

    foreach ($products_id as $key => $product_id) {
        $variations = [];
        $variations_product = [];
        $product = wc_get_product($product_id);
        if ($product->is_type('variable')) {
            $variations_product = $product->get_available_variations();
            foreach ($variations_product as $key => $variation) {
                array_push($variations, ['id' => $variations_product[$key]['variation_id'], 'name' => $variations_product[$key]['attributes']['attribute_pagos']]);
            }
        
            $column = 'name';
            $value =  $value;    
            $keys = array_keys(array_column($variations, $column));
            $key = array_search($value, array_column($variations, $column));

            $woocommerce->cart->add_to_cart($product_id, 1, $variations[$keys[$key]]['id']);
        } else {
            $woocommerce->cart->add_to_cart($product_id, 1);
        }
    }

    if ($has_coupon) {
        $woocommerce->cart->apply_coupon( $coupon_code );
    }
    // Calculate totals
    $woocommerce->cart->calculate_totals();
}

add_action( 'wp_ajax_nopriv_fee_update', 'fee_update');
add_action( 'wp_ajax_fee_update', 'fee_update');

function fee_update() {
    global $woocommerce;
    $value = $_POST['option'];
    $id = AES_FEE_INSCRIPTION;
    if ($value == 'true') {
        $woocommerce->cart->add_to_cart($id, 1);
        $woocommerce->cart->calculate_totals();
    } else {
        $woocommerce->cart->remove_cart_item($woocommerce->cart->generate_cart_id($id));
        $woocommerce->cart->calculate_totals();
    }
}

add_action( 'wp_ajax_nopriv_reload_payment_table', 'reload_payment_table');
add_action( 'wp_ajax_reload_payment_table', 'reload_payment_table');

function reload_payment_table() {
    ob_start();
    ?>
        <?php
        $value = $_POST['option'];
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        $id = AES_FEE_INSCRIPTION;
        $filtered_products = array_filter($cart, function($product) use($id) {
            return $product['product_id'] != $id;
        });

        $cart_total = 0;
        $product_id = null;
        foreach ($filtered_products as $key => $product) {
            $product_id = $product['product_id'];
            $cart_total = $product['line_total'];
            // $price = $product['line_total']; 
        }
        if(isset($product_id)) {
            $product = wc_get_product($product_id);
            if ($product->is_type('variable')) {
                $variations = $product->get_available_variations();
                $date = new DateTime('August 12');
                $date = $date->format('Y-m-d');
                foreach ($variations as $key => $variation) {
                    if ($variation['attributes']['attribute_pagos'] === $value) {
                    ?>
                    <table class="payment-parts-table mt-5">
                    <tr>
                    <th class="payment-parts-table-header">Payment</th>
                    <th class="payment-parts-table-header">Date</th>
                    <th class="payment-parts-table-header">Amount</th>
                    </tr>
                    <?php
                    $cuotes = get_post_meta($variation['variation_id'], 'num_cuotes_text', true );
                    for ($i=0; $i < $cuotes; $i++) { 
                ?>
                    <tr class="payment-parts-table-row">
                        <td class="payment-parts-table-data"><?php echo ($i + 1)?></td>
                        <td class="payment-parts-table-data"><?php echo ($i === 0? date('Y-m-d') : (($value === 'Annual')? date('Y-m-d', strtotime('+'.$i.' year', strtotime($date))) : date('Y-m-d', strtotime('+'.($i*6).' months', strtotime($date)))))?></td>
                        <td class="payment-parts-table-data"><?php echo wc_price($cart_total)?></td>
                    </tr>
                    <?php
                    }
                ?>
                    <tr>
                        <th class="payment-parts-table-header text-end" colspan="3">Total</th>
                    </tr>
                    <tr class="payment-parts-table-row">
                        <td class="payment-parts-table-data text-end" colspan="3"><?php echo wc_price(($cart_total * $cuotes))?></td>
                    </tr>
                    </table>
                <?php
                    }
                }
            }
        }
        $html = ob_get_clean();
        echo $html;
        wp_die();
}

add_action( 'wp_ajax_nopriv_reload_button_schoolship', 'reload_button_schoolship');
add_action( 'wp_ajax_reload_button_schoolship', 'reload_button_schoolship');

function reload_button_schoolship() {
    ob_start();
    global $woocommerce;
    $cart =  $woocommerce->cart;
    ?>
    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-5 mb-5" style="text-align:center;">
        <?php if ( count( $cart->get_applied_coupons() ) > 0 ) :?>
            <button id="apply-scholarship-btn" type="button" disabled>Scholarship already applied</button>
        <?php else :?>
            <button id="apply-scholarship-btn" type="button">Activate scholarship</button>
        <?php endif;?>
    </div>
    <?php
    $html = ob_get_clean();
    echo $html;
    wp_die();
}

add_action( 'wp_ajax_nopriv_apply_scholarship', 'apply_scholarship');
add_action( 'wp_ajax_apply_scholarship', 'apply_scholarship');

function apply_scholarship() {
    global $woocommerce;
    $cart =  $woocommerce->cart;

    $coupon_code = 'Latam Schoolarship';
    $cart->apply_coupon( $coupon_code );

    // Calculate totals
    $woocommerce->cart->calculate_totals();
}

function woocommerce_custom_price_to_cart_item( $cart_object ) {  
    if( !WC()->session->__isset( "reload_checkout" )) {
        foreach ( $cart_object->cart_contents as $key => $value ) {
            if( isset( $value["custom_price"] ) ) {
                //for woocommerce version lower than 3
                //$value['data']->price = $value["custom_price"];
                //for woocommerce version +3
                $value['data']->set_price($value["custom_price"]);
            }
        }  
    }  
}
add_action( 'woocommerce_before_calculate_totals', 'woocommerce_custom_price_to_cart_item', 99 );


add_filter( 'woocommerce_account_dashboard', 'fee_inscription_button', 0);
function fee_inscription_button(){
    // VERIFICAR FEE DE INSCRIPCION
    global $wpdb;
    $table_student_payments = $wpdb->prefix.'student_payments';
    $table_students = $wpdb->prefix.'students';
    $partner_id = get_current_user_id();
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
    foreach ($students as $key => $student) {
        $paid = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id = {$student->id} and product_id = ". AES_FEE_INSCRIPTION);
        if($paid) {
            unset($students[$key]);
        }
    }
    // VERIFICAR FEE DE INSCRIPCION
    include(plugin_dir_path(__FILE__).'templates/fee-inscription-payment.php');
}

function custom_coupon_applied_notice($message) {
    wc_clear_notices();

    $applied_coupons = WC()->cart->get_applied_coupons();
    if ( ! empty( $applied_coupons ) ) {
        $current_coupon = reset( $applied_coupons );
    }
    wc_add_notice(__($current_coupon.' applied successfully!', 'woocommerce'), 'success');
}
add_action('woocommerce_applied_coupon', 'custom_coupon_applied_notice');

add_filter( 'woocommerce_cart_totals_coupon_label', 'remove_coupon_text' );
function remove_coupon_text( $label ) {
    $applied_coupons = WC()->cart->get_applied_coupons();
    if ( ! empty( $applied_coupons ) ) {
        $current_coupon = reset( $applied_coupons );
    }
    return $current_coupon; // Return an empty string to remove the label
}

function custom_login_redirect($redirect_to, $request, $user) {
    $roles = isset($user->roles) ? $user->roles : [];

    if (in_array('admision', $roles, true)) {
        $redirect_to = admin_url('admin.php?page=add_admin_form_admission_content'); // Redirect admision to a custom dashboard
    }

    if (in_array('finanzas', $roles, true)) {
        $redirect_to = admin_url('admin.php?page=add_admin_form_payments_content'); // Redirect admision to a custom dashboard
    }

    if (in_array('administration', $roles, true)) {
        $redirect_to = admin_url('admin.php?page=add_admin_institutes_content'); // Redirect admision to a custom dashboard
    }

    return $redirect_to;
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);

function custom_logout_redirect($redirect_to, $request, $user) {
    $redirect_to = home_url('my-account/'); // Redirect to My Account page
    return $redirect_to;
}
add_filter('logout_redirect', 'custom_logout_redirect', 10, 3);

function custom_cart_item_name($item_name, $cart_item, $cart_item_key) {
    // Check if we're on the checkout page
    if (is_checkout()) {
        // Get the product ID
        $product_id = $cart_item['product_id'];

        // // Get the custom product name (e.g. from a custom field)
        // $product = wc_get_product($product_id);
        // $custom_name = $product->get_meta('num_cuotes_text') ? $product->get_meta('num_cuotes_text') : 1;

        // // If custom name exists, use it, otherwise use the default name
        // if ($custom_name > 1) {
        //     $item_name = 'First payment of program';
        // } else {
        //     $item_name = $item_name;
        // }

        $item_name = $item_name;
        if (str_contains($item_name, 'Annual') || str_contains($item_name, 'Semiannual')) {
            $item = explode("-", $item_name);
            $item_name = $item[0] . '- 1 cuote';
        }
    } 

    return $item_name;
}
add_filter('woocommerce_cart_item_name', 'custom_cart_item_name', 10, 3);

function hide_checkout_cart_item_quantity($quantity, $cart_item, $cart_item_key) {
    // Check if we're on the checkout page
    if (is_checkout()) {
        $quantity = ''; // Return an empty string to hide the quantity
    }
    return $quantity;
}
add_filter('woocommerce_checkout_cart_item_quantity', 'hide_checkout_cart_item_quantity', 10, 3);