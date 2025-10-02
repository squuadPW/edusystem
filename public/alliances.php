<?php

function form_alliance_registration($atts){
    $atts = shortcode_atts(
        array(
            'title' => __('Alliance Registration','edusystem'),
            'ambassador_mode' => false
        ),
        $atts,
        'form_register_agreement'
    );

    extract($atts, EXTR_SKIP);

    $countries = get_countries();
    include(plugin_dir_path(__FILE__).'templates/alliance-registration.php');
}

add_shortcode('form_alliance_registration','form_alliance_registration');

function save_partner(){

    if(isset($_POST['action']) && !empty($_POST['action'])){

        if ($_POST['action'] == 'save_alliances') {
            global $wpdb;
            $table_alliances = $wpdb->prefix . 'alliances';
        
            // Campos requeridos y mensajes de error
            $required_fields = [
                'first_name' => __('First name is required', 'edusystem'),
                'last_name' => __('Last name is required', 'edusystem'),
                'name_legal' => __('Legal name is required', 'edusystem'),
                'number_phone_hidden' => __('Phone number is required', 'edusystem'),
                'current_email' => __('Email is required', 'edusystem'),
                'country' => __('Country is required', 'edusystem'),
                'state' => __('State is required', 'edusystem'),
                'city' => __('City is required', 'edusystem'),
                'address' => __('Address is required', 'edusystem')
            ];
        
            // Validar campos obligatorios
            $errors = [];
            foreach ($required_fields as $field => $message) {
                if (empty($_POST[$field])) {
                    $errors[] = $message;
                }
            }
        
            // Validar formato de email
            if (!empty($_POST['current_email']) && !is_email($_POST['current_email'])) {
                $errors[] = __('Invalid email format', 'edusystem');
            }
        
            // Validar formato de teléfono
            $phone_pattern = '/^\+?[0-9]{7,15}$/';
            if (!empty($_POST['number_phone_hidden']) && !preg_match($phone_pattern, $_POST['number_phone_hidden'])) {
                $errors[] = __('Invalid phone number format', 'edusystem');
            }
        
            // Mostrar errores
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    wc_add_notice($error, 'error');
                }
                return;
            }
        
            // Sanitizar datos
            $name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $name_legal = sanitize_text_field($_POST['name_legal']);
            $number_phone = sanitize_text_field($_POST['number_phone_hidden']);
            $email = sanitize_email($_POST['current_email']);
            $country = sanitize_text_field($_POST['country']);
            $state = sanitize_text_field($_POST['state']);
            $city = sanitize_text_field($_POST['city']);
            $address = sanitize_textarea_field($_POST['address']);
            $description = sanitize_textarea_field($_POST['description']);
        
            // Verificar email en tabla de alianzas
            $existing_alliance = $wpdb->get_var($wpdb->prepare(
                "SELECT email FROM $table_alliances WHERE email = %s",
                $email
            ));
        
            if ($existing_alliance) {
                wc_add_notice(__('Email already registered for another alliance', 'edusystem'), 'error');
                return;
            }
        
            // Verificar usuario existente
            $user = get_user_by('email', $email);
            if ($user) {
                wc_add_notice(__('Existing email, please enter another email', 'edusystem'), 'error');
                return;
            }
        
            // Insertar en base de datos
            $result = $wpdb->insert($table_alliances, [
                'name' => $name,
                'last_name' => $last_name,
                'name_legal' => $name_legal,
                'phone' => $number_phone,
                'email' => $email,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'address' => $address,
                'description' => $description,
                'type' => 0,
                'status' => 0,
                'created_at' => current_time('mysql', 1)
            ]);
        
            // Manejar error de inserción
            if ($result === false) {
                wc_add_notice(__('Error saving information. Please try again.', 'edusystem'), 'error');
                return;
            }
        
            // Enviar email de confirmación
            $new_alliance = WC()->mailer()->get_emails()['WC_Registered_Partner_Email'];
            if ($new_alliance) {
                $new_alliance->trigger($wpdb->insert_id);
            }
        
            wc_add_notice(__('Registration sent. Wait for confirmation.', 'edusystem'), 'success');
        }

    }
}

add_action('wp_loaded','save_partner');