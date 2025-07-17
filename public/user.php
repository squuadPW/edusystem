<?php

function filter_woocommerce_new_customer_data( $args ) {

    if (is_checkout()){

        if(isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])){
            $args['role'] = 'student';
        }else{
            $args['role'] = 'parent';
        }
    }

    return $args;
}

add_filter( 'woocommerce_new_customer_data', 'filter_woocommerce_new_customer_data', 10, 1 );



add_action('woocommerce_order_status_changed', 'crear_y_loguear_usuario_si_pago_exitoso', 10, 4);
function crear_y_loguear_usuario_si_pago_exitoso($order_id, $old_status, $new_status, $order) {
    $estados_validos = ['on-hold', 'processing', 'completed'];

    if (!in_array($new_status, $estados_validos)) {
        return;
    }

    $email = $order->get_billing_email();
    if (email_exists($email)) {
        return; // Ya existe, no lo creamos ni logueamos
    }

    $nombre = $order->get_billing_first_name();
    $apellido = $order->get_billing_last_name();
    $usuario = sanitize_user(current(explode('@', $email)), true);
    $password = wp_generate_password();

    $user_id = wp_create_user($usuario, $password, $email);

    if (!is_wp_error($user_id)) {
        wp_update_user([
            'ID' => $user_id,
            'first_name' => $nombre,
            'last_name' => $apellido,
        ]);

        $user = new WP_User($user_id);
        $user->set_role('customer');

        // Loguear al usuario automáticamente
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true); // true = sesión persistente

        // Redirigir al área de pedidos
        /* wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')) . '/orders');
        exit; */
    }
}

function checkout_set_customer_id( $current_user_id ) { 
	if(!$current_user_id ){
		$user = get_user_by('email', $_POST['billing_email']);
		if ($user){
			$current_user_id = $user->ID;
		}
	}
	return $current_user_id;
} 

add_filter('woocommerce_checkout_customer_id', 'checkout_set_customer_id');

function save_account_details( $user_id ) {

    global $current_user;
    $roles = $current_user->roles;

    if( in_array('parent',$roles) && !in_array('student',$roles) ){


        if(isset( $_POST['billing_city']) && !empty($_POST['billing_city'])){
            update_user_meta( $user_id,'billing_city',sanitize_text_field($_POST['billing_city']));
        }

        if(isset( $_POST['billing_country']) && !empty($_POST['billing_country'])){
            update_user_meta( $user_id,'billing_country',sanitize_text_field( $_POST['billing_country']));
        }

        if(isset( $_POST['number_phone_hidden']) && !empty($_POST['number_phone_hidden'])){
            update_user_meta( $user_id,'billing_phone',sanitize_text_field( $_POST['number_phone_hidden']));
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
            update_user_meta( $user_id,'type_document',sanitize_text_field( $_POST['type_document']));
        }

        if(isset( $_POST['billing_postcode']) && !empty($_POST['billing_postcode'])){
            update_user_meta( $user_id,'billing_postcode',sanitize_text_field( $_POST['billing_postcode']));
        }

        if(isset( $_POST['occupation']) && !empty($_POST['occupation'])){
            update_user_meta( $user_id,'occupation',sanitize_text_field( $_POST['occupation']));
        }
    }
}

add_action( 'woocommerce_save_account_details', 'save_account_details' );

function validated_account_details_required_fields( $required_fields ){ 
    
    global $current_user;
    $roles = $current_user->roles;

    if(in_array('parent',$roles) && !in_array('student',$roles)){

        $required_fields['billing_city'] = __('Billing city','edusystem');
        $required_fields['billing_country'] = __('Billing country','edusystem');
        $required_fields['number_phone_account'] = __('Number phone','edusystem');
        $required_fields['gender'] = __('Gender','edusystem');
        $required_fields['birth_date'] = __('Birth Date','edusystem');
        $required_fields['id_document'] = __('ID Document','edusystem');
        $required_fields['document_type'] = __('Type document','edusystem');
        $required_fields['billing_postcode'] = __('Post Code','edusystem');
        $required_fields['occupation'] = __('Occupation','edusystem');

    }

    return $required_fields;
}

add_filter('woocommerce_save_account_details_required_fields', 'validated_account_details_required_fields');

function is_password_user_moodle($student_id){

    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    if($data_student){

        if(!empty($data_student->moodle_password)){
            return true;
        }
    }

    return false;
}

function generate_password_user(){
    $password = wp_generate_password(12);
    return $password;
}

function add_role_user($user_id,$role){

    $user = new WP_User($user_id);

    if($user){
        $user->add_role($role);
    }
}
