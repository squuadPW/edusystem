<?php

function form_register_agreement()
{
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
        
            // Definir campos requeridos y sus mensajes de error
            $required_fields = [
                'name_institute' => __('Institute name is required', 'edusystem'),
                'number_phone_hidden' => __('Phone number is required', 'edusystem'),
                'current_email' => __('Email is required', 'edusystem'),
                'country' => __('Country is required', 'edusystem'),
                'state' => __('State is required', 'edusystem'),
                'city' => __('City is required', 'edusystem'),
                'address' => __('Address is required', 'edusystem'),
                'level' => __('Education level is required', 'edusystem'),
                'rector_name' => __('Rector name is required', 'edusystem'),
                'rector_lastname' => __('Rector lastname is required', 'edusystem'),
                'number_rector_phone_hidden' => __('Rector phone is required', 'edusystem'),
                'business_name' => __('Business name is required', 'edusystem')
            ];
        
            // Validar campos requeridos
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
        
            // Validar números telefónicos (ejemplo básico)
            $phone_pattern = '/^\+?[0-9]{7,15}$/';
            if (!empty($_POST['number_phone_hidden']) && !preg_match($phone_pattern, $_POST['number_phone_hidden'])) {
                $errors[] = __('Invalid phone number format', 'edusystem');
            }
        
            if (!empty($_POST['number_rector_phone_hidden']) && !preg_match($phone_pattern, $_POST['number_rector_phone_hidden'])) {
                $errors[] = __('Invalid rector phone number format', 'edusystem');
            }
        
            // Si hay errores, mostrarlos y abortar
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    wc_add_notice($error, 'error');
                }
                return;
            }
        
            // Sanitizar y preparar datos
            $name = sanitize_text_field(strtoupper($_POST['name_institute']));
            $phone = sanitize_text_field($_POST['number_phone_hidden']);
            $email = sanitize_email($_POST['current_email']);
            $country = sanitize_text_field($_POST['country']);
            $state = sanitize_text_field($_POST['state']);
            $city = sanitize_text_field(strtolower($_POST['city']));
            $address = sanitize_textarea_field($_POST['address']);
            $level = intval($_POST['level']);
            $rector_name = sanitize_text_field(strtolower($_POST['rector_name']));
            $rector_lastname = sanitize_text_field(strtolower($_POST['rector_lastname']));
            $rector_phone = sanitize_text_field($_POST['number_rector_phone_hidden']);
            $reference = sanitize_textarea_field($_POST['reference']);
            $description = sanitize_textarea_field($_POST['description']);
            $business_name = sanitize_text_field($_POST['business_name']);
            $type_calendar = sanitize_text_field($_POST['type_calendar']);
        
            // Verificar si el email ya existe en la tabla de institutos
            $existing_institute = $wpdb->get_var($wpdb->prepare(
                "SELECT email FROM $table_institutes WHERE email = %s",
                $email
            ));
        
            if ($existing_institute) {
                wc_add_notice(__('Email already registered for another institute', 'edusystem'), 'error');
                return;
            }
        
            // Verificar usuario existente
            $user = get_user_by('email', $email);
            if ($user) {
                wc_add_notice(__('Existing email, please enter another email.', 'edusystem'), 'error');
                return;
            }
        
            // Insertar en la base de datos
            $result = $wpdb->insert($table_institutes, [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'address' => $address,
                'level_id' => $level,
                'type_calendar' => $type_calendar,
                'name_rector' => $rector_name,
                'lastname_rector' => $rector_lastname,
                'phone_rector' => $rector_phone,
                'reference' => $reference,
                'description' => $description,
                'business_name' => $business_name,
                'status' => 0,
                'fee' => 5.0,
                'created_at' => current_time('mysql', 1)
            ]);
        
            // Manejar resultado de la inserción
            if ($result === false) {
                wc_add_notice(__('Database error. Please try again.', 'edusystem'), 'error');
                return;
            }
        
            // Obtener ID del nuevo registro
            $institute_id = $wpdb->insert_id;
        
            // Enviar email de confirmación
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

/**
 * Establece el instituto y calcula las tarifas asociadas en una orden de WooCommerce.
 *
 * @param WC_Order $order El objeto de la orden de WooCommerce.
 * @param int|null $id Opcional. El ID del instituto. Si no se proporciona, se toma de la cookie 'institute_id'.
 * @return void
 */
function set_institute_in_order(WC_Order $order, ?int $id = null): void
{
    global $wpdb;

    // 1. Obtener y validar el ID del instituto.
    $institute_id = $id ?? null;

    if ( empty($institute_id) ) return;

    // 2. Obtener datos del instituto de forma segura.
    $table_institutes = $wpdb->prefix . 'institutes';
    $institute_data   = $wpdb->get_row(
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
        // Asegúrate de que FEE_INSCRIPTION y FEE_GRADUATION estén definidos como constantes
        // o reemplaza con los IDs de producto reales si no lo son.
        if (!in_array($product_id, [FEE_INSCRIPTION, FEE_GRADUATION])) {
            // Usamos get_total() del item, que ya considera la cantidad y el precio.
            $total_for_fee_calculation += (float) $item->get_total();
        }
    }

    // 6. Calcular la tarifa final del instituto.
    $total_institute_fee = 0.0;
    // Solo calcular la tarifa si la orden no es una beca.
    if (! (bool) $order->get_meta('is_scholarship')) {
        $total_institute_fee = ($institute_fee_percentage * $total_for_fee_calculation) / 100;
    }
    $order->update_meta_data('institute_fee', $total_institute_fee);

    // 7. Guardar los cambios en la orden.
    $order->save();
}