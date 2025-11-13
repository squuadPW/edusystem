<?php

// Registra el inicio de sesión del usuario
add_action('wp_login', function ($user_login, $user) {

    $first_name = get_user_meta($user->ID, 'first_name', true);
    $last_name  = get_user_meta($user->ID, 'last_name', true);

    $message = sprintf(__('User %s logged in', 'edusystem'), $first_name.' '.$last_name);
    edusystem_get_log( $message, 'login', $user->ID);

}, 10, 2);

// Guarda información del usuario antes de cerrar sesión
add_action('clear_auth_cookie', function () {
    $user = wp_get_current_user();
    if ($user && $user->ID) {
        // Guardar el ID en un transient temporal
        set_transient('last_logout_user', $user->ID, 60); // dura 1 minuto
    }
});

// Guarda un registro cuando el usuario cierra sesión
add_action('wp_logout', function () {
    
    // Obtener el usuario actual antes de que se cierre la sesión
    $user_id = get_transient('last_logout_user');
    if ( $user_id ) {

        $user = get_userdata($user_id);

        $first_name   = get_user_meta( $user->ID, 'first_name', true );
        $last_name = get_user_meta( $user->ID, 'last_name', true );

        // Mensaje traducible con nombre y rol
        $message = sprintf(__('The user %s session closed', 'edusystem'), $first_name.' '.$last_name);

        // Registrar el log
        edusystem_get_log($message, 'logout', $user_id);

        // Limpiar el transient
        delete_transient('last_logout_user');
    }
});

// Guarda un registro de la actualización de un estudiante
add_action('edusystem_save_student_data', function ( $student_id ) {

    $user = wp_get_current_user();
    if ($user && $user->ID) {

        $first_name   = get_user_meta( $user->ID, 'first_name', true );
        $last_name = get_user_meta( $user->ID, 'last_name', true );
        $name_user = $first_name.' '.$last_name;
        
        // Mensaje 
        $message = sprintf(__('User %s has updated the data for student', 'edusystem'), $name_user);

        $name_student = '';
        if( $student_id ){
            $first_name   = get_user_meta( (int) $student_id, 'first_name', true );
            $last_name = get_user_meta( (int) $student_id, 'last_name', true );
            $name_student = $first_name.' '.$last_name;
         
            $url = esc_url(add_query_arg(
                array(
                    'page'        => 'add_admin_form_admission_content',
                    'section_tab' => 'student_details',
                    'student_id'  => $student_id,
                ),
                admin_url('admin.php')
            ));
            $message .= " <a href='{$url}'>{$name_student}</a>";
        }

        // Registrar el log
        edusystem_get_log($message, 'save_student_data', $user->ID);
    }
});