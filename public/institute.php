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

function get_list_institutes_active()
{

    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';

    $list_institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status=1");
    return $list_institutes;
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
    $institute_id = $id ?? ($_COOKIE['institute_id'] ?? null);

    // Validar que el ID del instituto sea válido
    if (empty($institute_id)) {
        return;
    }

    $table_institutes = $wpdb->prefix . 'institutes';

    // Obtener datos del instituto de forma segura
    $institute_data = $wpdb->get_row($wpdb->prepare(
        "SELECT id, fee FROM {$table_institutes} WHERE id = %d",
        $institute_id
    ));

    if (!$institute_data) {
        return;
    }

    $order->update_meta_data('institute_id', $institute_id);
    $institute_fee_percentage = (float) $institute_data->fee;

    // Calcular el subtotal de la orden excluyendo ciertos productos
    $subtotal = 0.0;
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        if (!in_array($product_id, [FEE_INSCRIPTION, FEE_GRADUATION])) {
            $subtotal += (float) $item->get_subtotal();
        }
    }

    // Calcular descuentos y la beca "latam scholarship"
    $latam_scholarship_amount = 0.0;
    foreach ($order->get_coupons() as $coupon) {
        if (strtolower($coupon->get_code()) === 'latam scholarship') {
            $latam_scholarship_amount = (float) $coupon->get_discount();
            break; // No necesitamos buscar más si ya encontramos la beca
        }
    }

    $total_for_fee_calculation = $subtotal - $latam_scholarship_amount;

    // Calcular la tarifa del instituto
    $total_institute_fee = 0.0;
    if (!$order->get_meta('is_scholarship')) {
        $total_institute_fee = ($institute_fee_percentage * $total_for_fee_calculation) / 100;
    }
    $order->update_meta_data('institute_fee', $total_institute_fee);

    // Lógica comentada para la alianza (mantener si se necesita en el futuro)
    /*
    if (!empty($institute_data->alliance_id)) {
        $table_alliances = $wpdb->prefix . 'alliances';
        $alliance_data = $wpdb->get_row($wpdb->prepare(
            "SELECT fee FROM {$table_alliances} WHERE id = %d",
            $institute_data->alliance_id
        ));

        if ($alliance_data) {
            $order->update_meta_data('alliance_id', $institute_data->alliance_id);
            $total_alliance_fee = 0.0;
            if (!$order->get_meta('is_scholarship')) {
                $alliance_fee_percentage = (float) $alliance_data->fee;
                $total_alliance_fee = ($alliance_fee_percentage * $total_for_fee_calculation) / 100;
            }
            $order->update_meta_data('alliance_fee', $total_alliance_fee);
        }
    }
    */

    $order->save();
}