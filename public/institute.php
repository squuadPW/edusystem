<?php

function form_register_agreement($atts)
{
    $atts = shortcode_atts(
        array(
            'title' => __('Institution Registration','edusystem'),
            'alliance_mode' => false
        ),
        $atts,
        'form_register_agreement'
    );

    extract($atts, EXTR_SKIP);
    $countries = get_countries();
    include(plugin_dir_path(__FILE__) . 'templates/register-agreement.php');
}

add_shortcode('form_register_agreement', 'form_register_agreement');

function save_institute()
{

    if (isset($_POST['action']) && !empty($_POST['action'])) {

        if ($_POST['action'] == 'save_institute') {
            global $wpdb;
            $table_institutes = $wpdb->prefix . 'institutes';

            // --- Validación de campos requeridos y formatos ---

            $alliance_mode_val = isset($_POST['alliance_mode']) ? $_POST['alliance_mode'] : '';
            $is_alliance = ($alliance_mode_val === 'true');

            // Campos requeridos comunes
            $required_fields = [
                'current_email' => __('Email is required', 'edusystem'),
                'country' => __('Country is required', 'edusystem'),
                'state' => __('State is required', 'edusystem'),
                'city' => __('City is required', 'edusystem'),
                'address' => __('Address is required', 'edusystem'),
                'level' => __('Education level is required', 'edusystem'),
                'rector_name' => __('Rector name is required', 'edusystem'),
                'rector_lastname' => __('Rector lastname is required', 'edusystem'),
                'number_rector_phone_hidden' => __('Rector phone is required', 'edusystem')
            ];
            // Solo requerir estos campos si NO es modo alianza
            if (!$is_alliance) {
                $required_fields['name_institute'] = __('Institute name is required', 'edusystem');
                $required_fields['number_phone_hidden'] = __('Phone number is required', 'edusystem');
                $required_fields['business_name'] = __('Business name is required', 'edusystem');
            }

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

            // Validar números telefónicos
            $phone_pattern = '/^\+?[0-9]{7,15}$/';
            $phones_to_check = [];
            if (!$is_alliance) {
                $phones_to_check['number_phone_hidden'] = __('Invalid phone number format', 'edusystem');
            }
            $phones_to_check['number_rector_phone_hidden'] = __('Invalid rector phone number format', 'edusystem');
            foreach ($phones_to_check as $field => $msg) {
                if (!empty($_POST[$field]) && !preg_match($phone_pattern, $_POST[$field])) {
                    $errors[] = $msg;
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    wc_add_notice($error, 'error');
                }
                return;
            }

            // --- Sanitización y preparación de datos ---
            // Asignar level y type_calendar a 1 si es modo alianza
            $fields = [
                'name' => strtoupper($_POST['name_institute']),
                'phone' => $_POST['number_phone_hidden'],
                'email' => $_POST['current_email'],
                'country' => $_POST['country'],
                'state' => $_POST['state'],
                'city' => strtolower($_POST['city']),
                'address' => $_POST['address'],
                'level_id' => $is_alliance ? 1 : $_POST['level'],
                'name_rector' => strtolower($_POST['rector_name']),
                'lastname_rector' => strtolower($_POST['rector_lastname']),
                'phone_rector' => $_POST['number_rector_phone_hidden'],
                'reference' => $_POST['reference'],
                'description' => $_POST['description'],
                'business_name' => $_POST['business_name'],
                'type_calendar' => $is_alliance ? 1 : $_POST['type_calendar'],
                'alliance_mode' => $_POST['alliance_mode']
            ];

            // Sanitizar
            $fields['name'] = sanitize_text_field($fields['name']);
            $fields['phone'] = sanitize_text_field($fields['phone']);
            $fields['email'] = sanitize_email($fields['email']);
            $fields['country'] = sanitize_text_field($fields['country']);
            $fields['state'] = sanitize_text_field($fields['state']);
            $fields['city'] = sanitize_text_field($fields['city']);
            $fields['address'] = sanitize_textarea_field($fields['address']);
            $fields['level_id'] = intval($fields['level_id']);
            $fields['name_rector'] = sanitize_text_field($fields['name_rector']);
            $fields['lastname_rector'] = sanitize_text_field($fields['lastname_rector']);
            $fields['phone_rector'] = sanitize_text_field($fields['phone_rector']);
            $fields['reference'] = sanitize_textarea_field($fields['reference']);
            $fields['description'] = sanitize_textarea_field($fields['description']);
            $fields['business_name'] = sanitize_text_field($fields['business_name']);
            // Si es modo alianza, type_calendar ya es 1 (int), si no, sanitizar
            $fields['type_calendar'] = $is_alliance ? 1 : sanitize_text_field($fields['type_calendar']);
            $fields['alliance_mode'] = sanitize_text_field($fields['alliance_mode']);

            // Verificar si el email ya existe en la tabla de institutos
            $existing_institute = $wpdb->get_var($wpdb->prepare(
                "SELECT email FROM $table_institutes WHERE email = %s",
                $fields['email']
            ));
            if ($existing_institute) {
                wc_add_notice(__('Email already registered for another institute', 'edusystem'), 'error');
                return;
            }

            // Verificar usuario existente
            $user = get_user_by('email', $fields['email']);
            if ($user) {
                wc_add_notice(__('Existing email, please enter another email.', 'edusystem'), 'error');
                return;
            }

            // --- Preparar datos para inserción ---
            $data = [
                'name' => $fields['name'],
                'phone' => $fields['phone'],
                'email' => $fields['email'],
                'country' => $fields['country'],
                'state' => $fields['state'],
                'city' => $fields['city'],
                'address' => $fields['address'],
                'level_id' => $fields['level_id'],
                'type_calendar' => $fields['type_calendar'],
                'name_rector' => $fields['name_rector'],
                'lastname_rector' => $fields['lastname_rector'],
                'phone_rector' => $fields['phone_rector'],
                'reference' => $fields['reference'],
                'description' => $fields['description'],
                'status' => 0,
                'fee' => 5.0,
                'created_at' => current_time('mysql', 1)
            ];

            if ($fields['alliance_mode'] === 'true') {
                $data['name'] = $fields['name_rector'] . ' ' . $fields['lastname_rector'];
                $data['phone'] = $fields['phone_rector'];
                $data['business_name'] = $fields['name_rector'] . ' ' . $fields['lastname_rector'];
            } else {
                $data['business_name'] = $fields['business_name'];
            }

            $result = $wpdb->insert($table_institutes, $data);

            if ($result === false) {
                wc_add_notice(__('Database error. Please try again.', 'edusystem'), 'error');
                return;
            }

            $institute_id = $wpdb->insert_id;

            $new_institute = WC()->mailer()->get_emails()['WC_Registered_Institution_Email'];
            if ($new_institute) {
                $new_institute->trigger($institute_id);
            }

            wc_add_notice(__('Registration sent. Wait for confirmation.', 'edusystem'), 'success');
        }
    }
}

add_action('wp_loaded', 'save_institute');

function get_list_institutes_active($manager_user_id = false)
{
    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';

    if ($manager_user_id) {
        $table_managers_by_institute = $wpdb->prefix . 'managers_by_institutes';
        $institute_ids_of_user = $wpdb->get_col($wpdb->prepare(
            "SELECT institute_id FROM {$table_managers_by_institute} WHERE user_id = %d",
            $manager_user_id
        ));

        // If no institute IDs are found for the user, return an empty array.
        if (empty($institute_ids_of_user)) {
            return [];
        }

        // Prepare the IN clause for the SQL query and filter by status = 1.
        $ids_placeholder = implode(',', array_fill(0, count($institute_ids_of_user), '%d'));
        $list_institutes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_institutes} WHERE id IN ({$ids_placeholder}) AND status = 1",
            ...$institute_ids_of_user
        ));
        return $list_institutes;

    } else {
        // Fetch all active institutes where status = 1.
        $list_institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 1");
        return $list_institutes;
    }
}

function get_alliances_from_institute($institute_id)
{
    global $wpdb;
    $table_alliances_by_institute = $wpdb->prefix . 'alliances_by_institutes';
    $table_alliances = $wpdb->prefix . 'alliances';
    $alliances = [];
    $rows = $wpdb->get_results("SELECT * FROM {$table_alliances_by_institute} WHERE institute_id={$institute_id}");
    foreach ($rows as $key => $row) {
        $alliance = $wpdb->get_row("SELECT * FROM {$table_alliances} WHERE id={$row->alliance_id}");
        $alliances[] = $alliance;
    }

    return $alliances;
}

function set_institute_in_order($order, $id = null): void
{
    global $wpdb;

    // 1. Obtener y validar el ID del instituto.
    $institute_id = $id ?? null;
    if ( empty($institute_id) || !$id ) return;

    $student_id = $order->get_meta('student_id') ?? null;
    if ( empty($student_id) || !$student_id ) return;

    $product_id_registration = get_fee_product_id($student_id, 'registration');
    $product_id_graduation = get_fee_product_id($student_id, 'graduation');

    // 2. Obtener datos del instituto de forma segura.
    $table_institutes = $wpdb->prefix . 'institutes';
    $institute_data = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, fee FROM {$table_institutes} WHERE id = %d",
            $institute_id
        )
    );

    if (empty($institute_data) || !isset($institute_data->fee)) {
        return; // Retornar si no se encuentra el instituto o no tiene una tarifa.
    }

    // 3. Almacenar el ID del instituto en la orden.
    $order->update_meta_data('institute_id', $institute_id);

    // 4. Calcular el porcentaje de la tarifa del instituto.
    $institute_fee_percentage = (float) $institute_data->fee;

    // 5. Calcular el total relevante para la tarifa (total de productos excluyendo específicos).
    $total_for_fee_calculation = 0.0;
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        if (!in_array($product_id, [$product_id_registration, $product_id_graduation])) {
            $total_for_fee_calculation += (float) $item->get_total();
        }
    }

    // 6. Calcular la tarifa final del instituto.
    $total_institute_fee = 0.0;
    // Solo calcular la tarifa si la orden no es una beca.
    if (!(bool) $order->get_meta('is_scholarship')) {
        $total_institute_fee = ($institute_fee_percentage * $total_for_fee_calculation) / 100;
    }
    $order->update_meta_data('institute_fee', $total_institute_fee);

    // 7. Guardar los cambios en la orden.
    $order->save();
}