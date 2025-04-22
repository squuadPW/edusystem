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

function set_institute_in_order($order, $id = NULL)
{

    $institute_id = $id ? $id : $_COOKIE['institute_id'];
    if (isset($institute_id) && !empty($institute_id)) {

        global $wpdb;
        $table_institutes = $wpdb->prefix . 'institutes';
        $table_alliances = $wpdb->prefix . 'alliances';

        $data = $wpdb->get_row("SELECT id,fee,alliance_id FROM {$table_institutes} WHERE id={$institute_id}");

        if ($data) {

            $order->update_meta_data('institute_id', $institute_id);

            $fee_institute = $data->fee;
            $coupons = $order->get_coupons();
            $order_items = $order->get_items();
            $subtotal = 0;

            foreach ($order_items as $item) {
                $product_id = $item->get_product_id();
                $subtotal = !in_array($product_id, [FEE_INSCRIPTION, FEE_GRADUATION]) ? $item->get_subtotal() : $subtotal;
            }

            // Ahora puedes recorrer los cupones de descuento
            $discounts = [];
            foreach ($coupons as $coupon) {
                $discount_amount = $coupon->get_discount();
                $discount_code = $coupon->get_code();
                array_push($discounts, ['code' => $discount_code, 'amount' => $discount_amount]);
            }

            // Buscar el descuento con el código "latam scholarship"
            $latam_scholarship = array_filter($discounts, function ($discount) {
                return $discount['code'] == 'latam scholarship';
            });

            // Si se encontró el descuento, obtener el monto
            if (!empty($latam_scholarship)) {
                $latam_scholarship_amount = reset($latam_scholarship)['amount'];
                // Restar el monto de descuento al subtotal
                $total_for_fee = $subtotal - $latam_scholarship_amount;
            } else {
                // Si no se encontró el descuento, no aplicar descuento
                $total_for_fee = $subtotal;
            }

            // Calcular la tarifa del instituto
            if ($order->get_meta('is_scholarship')) {
                $total_institute_fee = 0;
            } else {
                $total_institute_fee = ($fee_institute * $total_for_fee) / 100;
            }

            $order->update_meta_data('institute_fee', $total_institute_fee);

            // si tiene alianza
            if ($data->alliance_id != '') {

                $alliance_id = $data->alliance_id;
                $order->update_meta_data('alliance_id', $data->alliance_id);
                $data_alliance = $wpdb->get_row("SELECT fee FROM {$table_alliances} WHERE id={$alliance_id}");

                if (!empty($data_alliance)) {
                    if ($order->get_meta('is_scholarship')) {
                        $total_alliance_fee = 0;
                    } else {
                        $fee_alliance = $data_alliance->fee;
                        $total_alliance_fee = ($fee_alliance * $total_for_fee) / 100;
                    }
        
                    $order->update_meta_data('alliance_fee', $total_alliance_fee);
                }
            }

            $order->save();
        }
    }
}