<?php

function filter_woocommerce_new_customer_data($args)
{

    if (is_checkout()) {

        if (isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])) {
            $args['role'] = 'student';
        } else {
            $args['role'] = 'parent';
        }
    }

    return $args;
}

add_filter('woocommerce_new_customer_data', 'filter_woocommerce_new_customer_data', 10, 1);



add_action('woocommerce_order_status_changed', 'procesar_pedido_y_crear_usuario', 10, 4);

/**
 * Crea y loguea un usuario si el pago es exitoso, usando datos de las cookies.
 *
 * @param int      $order_id   ID del pedido.
 * @param string   $old_status Estado anterior.
 * @param string   $new_status Nuevo estado.
 * @param WC_Order $order      Objeto del pedido.
 */
function procesar_pedido_y_crear_usuario($order_id, $old_status, $new_status, $order)
{
    // Notificación para pagos que requieren aprobación manual.
    if ('on-hold' === $new_status) {
        send_notification_staff_particular('New payment received for approval', 'There is a new payment waiting for approval, please login to the platform as soon as possible.', 3);
    }

    // Salir si el estado no es uno de los que indican un pago válido.
    $estados_validos = ['on-hold', 'processing', 'completed'];
    if (!in_array($new_status, $estados_validos, true)) {
        return;
    }

    // Salir si el cliente ya tiene una cuenta registrada con ese email.
    $email = $order->get_billing_email();
    if (email_exists($email)) {
        return;
    }

    // 1. CREACIÓN DE USUARIO MÁS EFICIENTE
    // Se usa wp_insert_user para crear y asignar datos en una sola operación.
    $nombre = $order->get_billing_first_name();
    $apellido = $order->get_billing_last_name();
    $usuario = sanitize_user(current(explode('@', $email)), true);
    $password = wp_generate_password(); // Genera una contraseña segura por defecto.

    $user_data = [
        'user_login' => $usuario,
        'user_pass'  => $password,
        'user_email' => $email,
        'first_name' => $nombre,
        'last_name'  => $apellido,
        'role'       => 'customer', // Rol por defecto.
    ];

    // Asignar rol 'parent' si la cookie existe.
    if (isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])) {
        $user_data['role'] = 'parent';
    }

    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
        // Podrías registrar el error si lo necesitas.
        // error_log('Fallo al crear usuario para pedido ' . $order_id . ': ' . $user_id->get_error_message());
        return;
    }

    // 2. ACTUALIZACIÓN DE METADATOS REFACTORIZADA
    // Agrupamos la lógica repetitiva en un array para un código más limpio.
    $cookie_to_meta_map = [
        'id_document_parent'   => 'id_document',
        'parent_document_type' => 'type_document',
        'birth_date_parent'    => 'birth_date',
        'gender_parent'        => 'gender',
        'ethnicity_parent'     => 'ethnicity',
    ];

    foreach ($cookie_to_meta_map as $cookie_name => $meta_key) {
        if (isset($_COOKIE[$cookie_name]) && !empty($_COOKIE[$cookie_name])) {
            // Se sanitiza el valor de la cookie antes de guardarlo.
            update_user_meta($user_id, $meta_key, sanitize_text_field($_COOKIE[$cookie_name]));
        }
    }
    
    // Añade el metadato de estado de registro.
    if (!get_user_meta($user_id, 'status_register', true)) {
        update_user_meta($user_id, 'status_register', 0);
    }
    
    // 3. LÓGICA DE COOKIES ORIGINAL (CON ADVERTENCIA)
    // !! ADVERTENCIA DE SEGURIDAD !!
    // Establecer una contraseña desde una cookie es una práctica extremadamente insegura.
    // Un atacante podría manipular la cookie para establecer una contraseña conocida.
    if (isset($_COOKIE['password']) && !empty($_COOKIE['password'])) {
        wp_set_password($_COOKIE['password'], $user_id);
    }

    // Asocia el nuevo usuario con el pedido.
    $order->set_customer_id($user_id);

    // Lógica para crear estudiante si existen las cookies necesarias.
    if (
        !empty($_COOKIE['name_student']) && !empty($_COOKIE['last_name_student']) &&
        !empty($_COOKIE['birth_date']) && !empty($_COOKIE['initial_grade']) &&
        !empty($_COOKIE['program_id']) && !empty($_COOKIE['email_partner']) &&
        !empty($_COOKIE['number_partner'])
    ) {
        $student_id = insert_student($user_id);
        insert_register_documents($student_id, $_COOKIE['initial_grade']);

        if (!$order->meta_exists('student_id')) {
            $order->update_meta_data('student_id', $student_id);
        }

        if(isset($_COOKIE['id_bitrix'])) {
            $order->update_meta_data('id_bitrix', $_COOKIE['id_bitrix']);
        }

        $email_new_student = WC()->mailer()->get_emails()['WC_New_Applicant_Email'];
        if ($email_new_student) {
            $email_new_student->trigger($student_id);
        }
        
        insert_data_student($order);
        if (isset($_COOKIE['is_scholarship']) && !empty($_COOKIE['is_scholarship'])) {
            save_scholarship();
        }
    }

    if (isset($_COOKIE['fee_student_id']) && !empty($_COOKIE['fee_student_id'])) {
        if (!$order->meta_exists('student_id')) {
            $order->update_meta_data('student_id', $_COOKIE['fee_student_id']);
        }
    }

    // Guarda los cambios en el pedido.
    $order->save();

    // Ejecuta el resto de tus funciones personalizadas.
    set_institute_in_order($order);
    $status_register = get_user_meta($user_id, 'status_register', true);
    process_payments_student($order, $order_id, $user_id, $status_register);
    update_process_payments_student($order, $order_id, $user_id);
    clear_all_cookies();

    // Intenta loguear al usuario. (Ver advertencia abajo)
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
}

function checkout_set_customer_id($current_user_id)
{
    if (!$current_user_id) {
        $user = get_user_by('email', $_POST['billing_email']);
        if ($user) {
            $current_user_id = $user->ID;
        }
    }
    return $current_user_id;
}

add_filter('woocommerce_checkout_customer_id', 'checkout_set_customer_id');

function save_account_details($user_id)
{

    global $current_user;
    $roles = $current_user->roles;

    if (in_array('parent', $roles) && !in_array('student', $roles)) {


        if (isset($_POST['billing_city']) && !empty($_POST['billing_city'])) {
            update_user_meta($user_id, 'billing_city', sanitize_text_field($_POST['billing_city']));
        }

        if (isset($_POST['billing_country']) && !empty($_POST['billing_country'])) {
            update_user_meta($user_id, 'billing_country', sanitize_text_field($_POST['billing_country']));
        }

        if (isset($_POST['number_phone_hidden']) && !empty($_POST['number_phone_hidden'])) {
            update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['number_phone_hidden']));
        }

        if (isset($_POST['gender']) && !empty($_POST['gender'])) {
            update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
        }

        if (isset($_POST['id_document']) && !empty($_POST['id_document'])) {
            update_user_meta($user_id, 'id_document', sanitize_text_field($_POST['id_document']));
        }

        if (isset($_POST['birth_date']) && !empty($_POST['birth_date'])) {
            update_user_meta($user_id, 'birth_date', sanitize_text_field($_POST['birth_date']));
        }

        if (isset($_POST['document_type']) && !empty($_POST['document_type'])) {
            update_user_meta($user_id, 'document_type', sanitize_text_field($_POST['document_type']));
            update_user_meta($user_id, 'type_document', sanitize_text_field($_POST['type_document']));
        }

        if (isset($_POST['billing_postcode']) && !empty($_POST['billing_postcode'])) {
            update_user_meta($user_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
        }

        if (isset($_POST['occupation']) && !empty($_POST['occupation'])) {
            update_user_meta($user_id, 'occupation', sanitize_text_field($_POST['occupation']));
        }
    }
}

add_action('woocommerce_save_account_details', 'save_account_details');

function validated_account_details_required_fields($required_fields)
{

    global $current_user;
    $roles = $current_user->roles;

    if (in_array('parent', $roles) && !in_array('student', $roles)) {

        $required_fields['billing_city'] = __('Billing city', 'edusystem');
        $required_fields['billing_country'] = __('Billing country', 'edusystem');
        $required_fields['number_phone_account'] = __('Number phone', 'edusystem');
        $required_fields['gender'] = __('Gender', 'edusystem');
        $required_fields['birth_date'] = __('Birth Date', 'edusystem');
        $required_fields['id_document'] = __('ID Document', 'edusystem');
        $required_fields['document_type'] = __('Type document', 'edusystem');
        $required_fields['billing_postcode'] = __('Post Code', 'edusystem');
        $required_fields['occupation'] = __('Occupation', 'edusystem');

    }

    return $required_fields;
}

add_filter('woocommerce_save_account_details_required_fields', 'validated_account_details_required_fields');

function is_password_user_moodle($student_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    if ($data_student) {

        if (!empty($data_student->moodle_password)) {
            return true;
        }
    }

    return false;
}

function generate_password_user()
{
    $password = wp_generate_password(12);
    return $password;
}

function add_role_user($user_id, $role)
{

    $user = new WP_User($user_id);

    if ($user) {
        $user->add_role($role);
    }
}
