<?php

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

    if(is_user_logged_in() && $args->theme_location == 'primary') {
        $logout_link = wp_logout_url( get_home_url() );
        $items .= '<li><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'">'.__('Dashboard','form-plugin').'</a></li>';
        $items .= '<li><a class="button-primary" style="font-size:14px;" href="'.$logout_link.'">'.__('Log out','form-plugin').'</a></li>';
    }

    elseif (!is_user_logged_in() && $args->theme_location == 'primary') {
        $items .= '<li><a class="button-primary" style="font-size:14px;" href="' .get_permalink( get_option('woocommerce_myaccount_page_id') ). '">'.__('Log in','form-plugin').'</a></li>';
    }

    return $items;
}

function status_changed_payment($order_id, $old_status, $new_status){

        $order = wc_get_order($order_id);
        $customer_id = $order->get_customer_id();
        $status_register = get_user_meta($customer_id,'status_register',true);

        if($order->get_status() == 'completed'){
    
            update_user_meta($customer_id,'status_register',1);

            // Get the variation product selected from the order
            $items = $order->get_items();
            $date_calc = '';

            foreach ($items as $item) {
                $product = $item->get_product();
            }

            // Only for product variation
            if( $product->is_type('variation') ){
                // Get the variation attributes
                $variation_attributes = $product->get_variation_attributes();
                // Loop through each selected attributes
                foreach($variation_attributes as $attribute_taxonomy => $term_slug ){
                    // Get product attribute name or taxonomy
                    $taxonomy = str_replace('attribute_', '', $attribute_taxonomy );
                    // The label name from the product attribute
                    $attribute_name = wc_attribute_label( $taxonomy, $product );
                    // The term name (or value) from this attribute
                    if( taxonomy_exists($taxonomy) ) {
                        $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                    } else {
                        $attribute_value = $term_slug; // For custom product attributes
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
            }

            $date = new DateTime('August 12');
            $date = $date->format('Y-m-d');

            $cuotes = $product->get_meta('num_cuotes_text') ? $product->get_meta('num_cuotes_text') : 1;
            global $wpdb;

            $data = array(
                'status_id' => $i == 0 ? 1 : 0, // Replace with the actual status ID
                'student_id' => is_user_logged_in() ? get_current_user_id() : $order->get_meta('student_id'), // Replace with the actual student ID
                'product_id' => $product->get_id(), // Replace with the actual product ID
                'amount' => $order->get_total(), // Replace with the actual amount
                'type_payment' => $cuotes > 1 ? 1 : 2, // Replace with the actual payment type
                'cuote' => ($i + 1), // Replace with the actual num coute
                'num_cuotes' => $cuotes, // Replace with the num total of coutes
                'date_payment' => date('Y-m-d'), // Replace with the date of first payment
                'date_next_payment' => $cuotes > 1 ? date('Y-m-d', strtotime($date_calc, strtotime($date))) : date('Y-m-d'), // Replace with the date of next payment
            );

            $wpdb->insert($wpdb->prefix.'student_payments', $data);
            // Get the variation product selected from the order
    
            if($order->get_meta('student_id')){
    
                $student_id = $order->get_meta('student_id');
                update_status_student($student_id,1);
                
                $email_request_documents = WC()->mailer()->get_emails()['WC_Request_Documents_Email'];
                $email_request_documents->trigger($student_id);
                
                return $data->url;
            }   
    
        }else{
    
            if($status_register != 1 && $status_register != '1'){
                update_user_meta($customer_id,'status_register',0);
            }
    
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
    if ( count( $applied_coupons ) > 0 ) {
        $has_coupon = true;
        foreach ($applied_coupons as $key => $coupon) {
            $coupon_code = $coupon;
        }
    }

    $value = $_POST['option'];
    foreach ($cart->get_cart() as $key => $product) {
        $product_id = $product['product_id'];
        // $price = $product['line_total']; 
    }

    $prices = [];
    $product = wc_get_product($product_id);
    $variations = $product->get_available_variations();
    foreach ($variations as $key => $variation) {
        array_push($prices, ['id' => $variations[$key]['variation_id'], 'name' => $variations[$key]['attributes']['attribute_pagos']]);
    }

    $woocommerce->cart->empty_cart();

    $column = 'name';
    $value =  $value;    
    $keys = array_keys(array_column($prices, $column));
    $key = array_search($value, array_column($prices, $column));

    $woocommerce->cart->add_to_cart($product_id, 1, $prices[$keys[$key]]['id']);
    if ($has_coupon) {
        $woocommerce->cart->apply_coupon( $coupon_code );
    }
    // Calculate totals
    $woocommerce->cart->calculate_totals();
}

add_action( 'wp_ajax_nopriv_reload_payment_table', 'reload_payment_table');
add_action( 'wp_ajax_reload_payment_table', 'reload_payment_table');

function reload_payment_table() {
    ob_start();
    ?>
        <?php
        $value = $_POST['option'];
        global $woocommerce;
        $cart = $woocommerce->cart;
        $cart_total = 0;
        foreach ($cart->get_cart() as $key => $product) {
            $product_id = $product['product_id'];
            $cart_total = $product['line_total'];
            // $price = $product['line_total']; 
        }
        $product = wc_get_product($product_id);
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
    include(plugin_dir_path(__FILE__).'templates/fee-inscription-payment.php');
}