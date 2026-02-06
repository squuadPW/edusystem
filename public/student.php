<?php

add_action('woocommerce_checkout_order_created', 'save_essential_data_order', 8, 1);
function save_essential_data_order($order)
{

    /* // Verificar si algún producto es de la categoría "programs"
    $has_program = false;
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ( $product && has_term('programs', 'product_cat', $product->get_id()) ) {
            $has_program = true;
            break;
        }
    }

    // Solo continuar si hay al menos un producto de la categoría "programs"
    if ( !$has_program ) return; */

    // sino exite la cooki de guardar estudiantes entonces no guarda los datos 
    if (!isset($_COOKIE['save_student'])) return;

    // datos generales
    $order->update_meta_data('payment_method_selected', $_COOKIE['payment_method_selected'] ?? null);
    $order->update_meta_data('is_scholarship', $_COOKIE['is_scholarship'] ?? null);
    $order->update_meta_data('one_time_payment', $_COOKIE['one_time_payment'] ?? null);
    $order->update_meta_data('from_webinar', $_COOKIE['from_webinar'] ?? null);
    $order->update_meta_data('crm_id', $_COOKIE['crm_id'] ?? null);

    // obtiene el nombre del instituto
    if (isset($_COOKIE['institute_id']) && !empty($_COOKIE['institute_id'])) {
        $institute = get_institute_details($_COOKIE['institute_id']);
        $name_institute = $institute->name ?? null;
    } else {
        $name_institute = $_COOKIE['name_institute'] ?? null;
    }

    // datos del registro del estudiante
    $registration_data = json_encode([
        "student" => [
            "name" => $_COOKIE['name_student'] ?? null,
            "middle_name" => $_COOKIE['middle_name_student'] ?? null,
            "last_name" => $_COOKIE['last_name_student'] ?? null,
            "middle_last_name" => $_COOKIE['middle_last_name_student'] ?? null,
            "type_document" => $_COOKIE['document_type'] ?? null,
            "id_document" => $_COOKIE['id_document'] ?? null,
            "birth_date" => $_COOKIE['birth_date'] ?? null,
            "gender" => $_COOKIE['gender'] ?? null,
            "ethnicity" => $_COOKIE['ethnicity'] ?? null,
            "email" => $_COOKIE['email_student'] ?? null,
            "phone" => $_COOKIE['phone_student'] ?? null,
            "is_older" => $_COOKIE['is_older'] ?? null,
            "locale" => $_COOKIE['locale'] ?? null
        ],
        "parent" => [
            "agent_name" => $_COOKIE['agent_name'] ?? null,
            "agent_last_name" => $_COOKIE['agent_last_name'] ?? null,
            "type_document" => $_COOKIE['parent_document_type'] ?? null,
            "id_document" => $_COOKIE['id_document_parent'] ?? null,
            "birth_date" => $_COOKIE['birth_date_parent'] ?? null,
            "gender" => $_COOKIE['gender_parent'] ?? null,
            "ethnicity" => $_COOKIE['ethnicity_parent'] ?? null,
            "email" => $_COOKIE['email_partner'] ?? null,
            "phone" => $_COOKIE['number_partner'] ?? null,
            "locale" => $_COOKIE['locale'] ?? null
        ],
        "program" => [
            "program_id" => $_COOKIE['program_id'] ?? null,
            "career_id" => $_COOKIE['career_id'] ?? null,
            "mention_id" => $_COOKIE['mention_id'] ?? null,
            "plan_id" => $_COOKIE['plan_id'] ?? null,
            "initial_grade" => $_COOKIE['initial_grade'] ?? null,
            "institute_id" => $_COOKIE['institute_id'] ?? null,
            "name_institute" => $name_institute,
            "expected_graduation_date" => $_COOKIE['expected_graduation_date'] ?? null
        ],
        "access" => [
            "password" => base64_encode(sanitize_text_field(wp_unslash($_COOKIE['password']))) ?? null
        ]
    ]);

    $order->update_meta_data('registration_data', $registration_data);

    $order->save();
}


function save_student()
{
    if (
        isset($_GET['action']) && !empty($_GET['action']) && ($_GET['action'] == 'save_student' || $_GET['action'] == 'new_applicant_others' || $_GET['action'] == 'new_applicant_me' || $_GET['action'] == 'save_student_custom' || $_GET['action'] == 'save_student_info' || $_GET['action'] == 'save_student_scholarship')
    ) {

        setcookie('save_student', 'save_student', time() + 864000, '/');

        $action = $_GET['action'];
        global $woocommerce;

        setcookie('name_institute', '', time(), '/');
        setcookie('from_webinar', '', time(), '/');
        setcookie('one_time_payment', '', time(), '/');
        setcookie('is_scholarship', '', time(), '/');
        setcookie('payment_method_selected', '', time(), '/');

        // Datos del estudiante
        $birth_date = isset($_POST['birth_date_student']) ? $_POST['birth_date_student'] : null;
        $document_type = isset($_POST['document_type']) ? $_POST['document_type'] : null;
        $id_document = isset($_POST['id_document']) ? $_POST['id_document'] : null;
        $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
        $name = isset($_POST['name_student']) ? strtolower($_POST['name_student']) : null;
        $middle_name_student = isset($_POST['middle_name_student']) ? strtolower($_POST['middle_name_student']) : null;
        $last_name = isset($_POST['lastname_student']) ? strtolower($_POST['lastname_student']) : null;
        $middle_last_name_student = isset($_POST['middle_last_name_student']) ? strtolower($_POST['middle_last_name_student']) : null;
        $number_phone = isset($_POST['number_phone']) ? $_POST['number_phone'] : (isset($_POST['number_phone_hidden']) ? $_POST['number_phone_hidden'] : null);
        $email_student = isset($_POST['email_student']) ? strtolower($_POST['email_student']) : null;
        $ethnicity = isset($_POST['etnia']) ? $_POST['etnia'] : null;

        // Datos del padre
        $birth_date_parent = isset($_POST['birth_date_parent']) ? $_POST['birth_date_parent'] : null;
        $parent_document_type = isset($_POST['parent_document_type']) ? $_POST['parent_document_type'] : null;
        $id_document_parent = isset($_POST['id_document_parent']) ? $_POST['id_document_parent'] : null;
        $gender_parent = isset($_POST['gender_parent']) ? $_POST['gender_parent'] : null;
        $agent_name = isset($_POST['agent_name']) ? strtolower($_POST['agent_name']) : null;
        $agent_last_name = isset($_POST['agent_last_name']) ? strtolower($_POST['agent_last_name']) : null;
        $number_partner = isset($_POST['number_partner_hidden']) ? $_POST['number_partner_hidden'] : null;
        $email_partner = isset($_POST['email_partner']) ? strtolower($_POST['email_partner']) : null;

        // DATOS EXTRAS
        $country = isset($_POST['country']) ? $_POST['country'] : null;
        $city = isset($_POST['city']) ? strtolower($_POST['city']) : null;
        $program = isset($_POST['program']) ? $_POST['program'] : null;
        $coupon_complete = isset($_POST['coupon_complete']) ? $_POST['coupon_complete'] : null;
        $coupon_credit = isset($_POST['coupon_credit']) ? $_POST['coupon_credit'] : null;
        $career = isset($_POST['career']) ? $_POST['career'] : null;
        $mention = isset($_POST['mention']) ? $_POST['mention'] : null;
        $plan = isset($_POST['plan']) ? $_POST['plan'] : null;
        $fees = get_fees_associated_plan_complete($plan);

        // $program = get_identificator_by_id_program($program_id);
        $grade = isset($_POST['grade']) && !empty($_POST['grade']) ? $_POST['grade'] : 4;
        $institute_id = isset($_POST['institute_id']) ? $_POST['institute_id'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $from_webinar = isset($_POST['from_webinar']) ? true : false;
        $one_time_payment = isset($_POST['one_time_payment']) ? true : false;
        $is_scholarship = isset($_POST['is_scholarship']) ? true : false;
        $crm_id = isset($_POST['crm_id']) ? $_POST['crm_id'] : false;
        $squuad_stripe_selected_client_id = isset($_POST['squuad_stripe_selected_client_id']) ? $_POST['squuad_stripe_selected_client_id'] : false;
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : false;
        $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : false;
        $flywire_portal_code = isset($_POST['flywire_portal_code']) ? $_POST['flywire_portal_code'] : false;
        $zelle_account = isset($_POST['zelle_account']) ? $_POST['zelle_account'] : false;
        $bank_transfer_account = isset($_POST['bank_transfer_account']) ? $_POST['bank_transfer_account'] : false;
        $student_registration_hidden_payments = isset($_POST['hidden_payment_methods']) ? $_POST['hidden_payment_methods'] : false;
        $fixed_fee_inscription = isset($_POST['fixed_fee_inscription']) ? $_POST['fixed_fee_inscription'] : false;
        $expected_graduation_date = isset($_POST['expected_graduation_date']) ? $_POST['expected_graduation_date'] : null;
        $locale = isset($_POST['current_lang']) ? $_POST['current_lang'] : null;
        $separate_program_fee = isset($_POST['separate_program_fee']) ? $_POST['separate_program_fee'] : null;
        $fee_payment_completed = isset($_POST['fee_payment_completed']) ? $_POST['fee_payment_completed'] : null;

        if (!$crm_id) {
            if (get_option('crm_token') && get_option('crm_url') && $email_partner) {
                $crm_exist = crm_request('contacts', '?email=' . $email_partner, 'GET', null);
                // Check if $crm_exist is a WP_Error object
                if (!is_wp_error($crm_exist) && isset($crm_exist['items']) && count($crm_exist['items']) > 0) {
                    setcookie('crm_id', $crm_exist['items'][0]['id'], time() + 864000, '/');
                }
            }
        } else {
            setcookie('crm_id', $crm_id, time() + 864000, '/');
        }

        if ($one_time_payment) {
            setcookie('one_time_payment', 1, time() + 864000, '/');
        }

        if (isset($email_partner) && ($email_partner === $email_student)) {
            wc_add_notice(__('Emails can\'t be the same', 'edusystem'), 'error');
            return;
        }

        setcookie('is_older', '', time() + 864000, '/');
        setcookie('ethnicity', $ethnicity, time() + 864000, '/');
        setcookie('billing_city', ucwords($city), time() + 864000, '/');
        setcookie('billing_country', $country, time() + 864000, '/');
        setcookie('initial_grade', $grade, time() + 864000, '/');
        setcookie('program_id', $program, time() + 864000, '/');
        setcookie('coupon_complete', $coupon_complete, time() + 864000, '/');
        setcookie('coupon_credit', $coupon_credit, time() + 864000, '/');
        setcookie('career_id', $career, time() + 864000, '/');
        setcookie('mention_id', $mention, time() + 864000, '/');
        setcookie('plan_id', $plan, time() + 864000, '/');
        // setcookie('program_id_number', $program_id, time() + 864000, '/');
        setcookie('phone_student', $number_phone, time() + 864000, '/');
        setcookie('id_document', $id_document, time() + 864000, '/');
        setcookie('document_type', $document_type, time() + 864000, '/');
        setcookie('email_student', $email_student, time() + 864000, '/');
        setcookie('name_student', ucwords($name), time() + 864000, '/');
        setcookie('middle_name_student', ucwords($middle_name_student), time() + 864000, '/');
        setcookie('last_name_student', ucwords($last_name), time() + 864000, '/');
        setcookie('middle_last_name_student', ucwords($middle_last_name_student), time() + 864000, '/');
        setcookie('birth_date', $birth_date, time() + 864000, '/');
        setcookie('gender', $gender, time() + 864000, '/');
        setcookie('password', $password, time() + 864000, '/');
        setcookie('squuad_stripe_selected_client_id', $squuad_stripe_selected_client_id, time() + 864000, '/');
        setcookie('flywire_portal_code', $flywire_portal_code, time() + 864000, '/');
        setcookie('zelle_account', $zelle_account, time() + 864000, '/');
        setcookie('bank_transfer_account', $bank_transfer_account, time() + 864000, '/');
        setcookie('student_registration_hidden_payments', $student_registration_hidden_payments, time() + 864000, '/');
        setcookie('fixed_fee_inscription', $fixed_fee_inscription, time() + 864000, '/');
        setcookie('expected_graduation_date', $expected_graduation_date, time() + 864000, '/');
        setcookie('locale', $locale, time() + 864000, '/');
        setcookie('separate_program_fee', $separate_program_fee, time() + 864000, '/');
        setcookie('fee_payment_completed', $fee_payment_completed, time() + 864000, '/');
        setcookie('fees', json_encode($fees), time() + 864000, '/');

        if (!empty($institute_id) && $institute_id != 'other') {
            $institute = get_institute_details($institute_id);
            $name_institute = strtoupper($institute->name);
            setcookie('institute_id', $institute_id, time() + 864000, '/');
        } else {
            $name_institute = isset($_POST['name_institute']) ? strtoupper($_POST['name_institute']) : null;
            setcookie('name_institute', strtoupper($name_institute), time() + 864000, '/');
        }

        switch ($action) {
            case 'save_student_scholarship':
            case 'save_student':
                if (!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner) && !empty($birth_date_parent) && !empty($parent_document_type) && !empty($id_document_parent)) {
                    setcookie('agent_name', ucwords($agent_name), time() + 864000, '/');
                    setcookie('agent_last_name', ucwords($agent_last_name), time() + 864000, '/');
                    setcookie('email_partner', $email_partner, time() + 864000, '/');
                    setcookie('number_partner', $number_partner, time() + 864000, '/');
                    setcookie('birth_date_parent', $birth_date_parent, time() + 864000, '/');
                    setcookie('parent_document_type', $parent_document_type, time() + 864000, '/');
                    setcookie('id_document_parent', $id_document_parent, time() + 864000, '/');
                    setcookie('gender_parent', $gender_parent, time() + 864000, '/');
                } else {
                    setcookie('agent_name', ucwords($name), time() + 864000, '/');
                    setcookie('agent_last_name', ucwords($last_name), time() + 864000, '/');
                    setcookie('email_partner', $email_student, time() + 864000, '/');
                    setcookie('number_partner', $number_phone, time() + 864000, '/');
                    setcookie('birth_date_parent', $birth_date, time() + 864000, '/');
                    setcookie('parent_document_type', $document_type, time() + 864000, '/');
                    setcookie('id_document_parent', $id_document, time() + 864000, '/');
                    setcookie('gender_parent', $gender, time() + 864000, '/');
                }

                redirect_to_checkout($from_webinar, $is_scholarship ? $id_document : false, false, $product_id, $coupon_code, $fixed_fee_inscription, $fees, $fee_payment_completed, $institute_id, $coupon_complete);
                // wp_redirect(home_url('/select-payment'));
                break;

            case 'save_student_custom':
                $current_user = wp_get_current_user();
                wp_update_user(array('ID' => $current_user->ID, 'user_pass' => $password));
                update_user_meta($current_user->ID, 'pay_application_password', 1);
                update_user_meta($current_user->ID, 'from_webinar', 1);

                wp_redirect(get_permalink(wc_get_page_id('myaccount')));
                exit;
                break;

            case 'save_student_info':
                if (!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner) && !empty($birth_date_parent) && !empty($parent_document_type) && !empty($id_document_parent)) {
                    setcookie('agent_name', ucwords($agent_name), time() + 864000, '/');
                    setcookie('agent_last_name', ucwords($agent_last_name), time() + 864000, '/');
                    setcookie('email_partner', $email_partner, time() + 864000, '/');
                    setcookie('number_partner', $number_partner, time() + 864000, '/');
                    setcookie('birth_date_parent', $birth_date_parent, time() + 864000, '/');
                    setcookie('parent_document_type', $parent_document_type, time() + 864000, '/');
                    setcookie('id_document_parent', $id_document_parent, time() + 864000, '/');
                    setcookie('gender_parent', $gender_parent, time() + 864000, '/');
                } else {
                    setcookie('agent_name', ucwords($name), time() + 864000, '/');
                    setcookie('agent_last_name', ucwords($last_name), time() + 864000, '/');
                    setcookie('email_partner', $email_student, time() + 864000, '/');
                    setcookie('number_partner', $number_phone, time() + 864000, '/');
                    setcookie('birth_date_parent', $birth_date, time() + 864000, '/');
                    setcookie('parent_document_type', $document_type, time() + 864000, '/');
                    setcookie('id_document_parent', $id_document, time() + 864000, '/');
                    setcookie('gender_parent', $gender, time() + 864000, '/');
                }

                wp_redirect(wp_get_referer() . '?action=fill_data');
                exit;
                break;

            case 'new_applicant_me':

                global $current_user;
                $document_type = $parent_document_type ? $parent_document_type : strtolower(get_user_meta(get_current_user_id(), 'type_document', true));
                $id_document = $id_document_parent ? $id_document_parent : strtolower(get_user_meta(get_current_user_id(), 'id_document', true));
                $number_phone = get_user_meta(get_current_user_id(), 'billing_phone', true);
                $email_student = strtolower($current_user->user_email);
                $birth_date = $birth_date_parent ? $birth_date_parent : strtolower(get_user_meta(get_current_user_id(), 'birth_date', true));
                $gender = $gender_parent ? $gender_parent : strtolower(get_user_meta(get_current_user_id(), 'gender', true));
                $ethnicity = $ethnicity ? $ethnicity : strtolower(get_user_meta(get_current_user_id(), 'ethnicity', true));

                setcookie('ethnicity_parent', $ethnicity, time() + 864000, '/');
                setcookie('phone_student', $number_phone, time() + 864000, '/');
                setcookie('id_document', $id_document, time() + 864000, '/');
                setcookie('document_type', $document_type, time() + 864000, '/');
                setcookie('email_student', $email_student, time() + 864000, '/');
                setcookie('birth_date', $birth_date, time() + 864000, '/');
                setcookie('gender', $gender, time() + 864000, '/');
                setcookie('agent_name', ucwords(get_user_meta(get_current_user_id(), 'first_name', true)), time() + 864000, '/');
                setcookie('agent_last_name', ucwords(get_user_meta(get_current_user_id(), 'last_name', true)), time() + 864000, '/');
                setcookie('email_partner', $email_student, time() + 864000, '/');
                setcookie('number_partner', get_user_meta(get_current_user_id(), 'billing_phone', true), time() + 864000, '/');
                setcookie('birth_date_parent', get_user_meta(get_current_user_id(), 'birth_date', true), time() + 864000, '/');
                setcookie('parent_document_type', get_user_meta(get_current_user_id(), 'type_document', true), time() + 864000, '/');
                setcookie('id_document_parent', get_user_meta(get_current_user_id(), 'id_document', true), time() + 864000, '/');
                setcookie('gender_parent', get_user_meta(get_current_user_id(), 'gender_parent', true), time() + 864000, '/');

                redirect_to_checkout($from_webinar, $is_scholarship, false, $product_id, $coupon_code, $fixed_fee_inscription, $fees, $fee_payment_completed, $institute_id, $coupon_complete);
                // wp_redirect(home_url('/select-payment'));
                break;

            default:

                global $current_user;
                setcookie('agent_name', ucwords(get_user_user_meta(get_current_user_id(), 'first_name', true)), time() + 864000, '/');
                setcookie('agent_last_name', ucwords(get_user_meta(get_current_user_id(), 'last_name', true)), time() + 864000, '/');
                setcookie('email_partner', $current_user->user_email, time() + 864000, '/');
                setcookie('number_partner', get_user_meta(get_current_user_id(), 'billing_phone', true), time() + 864000, '/');
                setcookie('birth_date_parent', get_user_meta(get_current_user_id(), 'birth_date', true), time() + 864000, '/');
                setcookie('parent_document_type', get_user_meta(get_current_user_id(), 'type_document', true), time() + 864000, '/');
                setcookie('id_document_parent', get_user_meta(get_current_user_id(), 'id_document', true), time() + 864000, '/');
                setcookie('gender_parent', get_user_meta(get_current_user_id(), 'gender_parent', true), time() + 864000, '/');

                redirect_to_checkout($from_webinar, $is_scholarship, false, $product_id, $coupon_code, $fixed_fee_inscription, $fees, $fee_payment_completed, $institute_id, $coupon_complete);
                // wp_redirect(home_url('/select-payment'));
                break;
        }
    }

    if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == 'fill_data') {
        global $current_user;
        $orders = wc_get_orders(array(
            'customer_id' => $current_user->ID,
        ));

        $order_id = $orders[0]->get_id();
        $order = wc_get_order($order_id);
        $customer_id = $order->get_customer_id();
        $status_register = get_user_meta($customer_id, 'status_register', true);
        woocommerce_checkout_order_created_action($order);

        status_order_not_completed($order, $order_id, $customer_id, $status_register);

        status_order_completed($order, $order_id, $customer_id, $status_register);
    }

    if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == 'select_payment') {
        $country = isset($_POST['country']) ? $_POST['country'] : null;
        $payment_method_selected = isset($_POST['payment_method_selected']) ? $_POST['payment_method_selected'] : null;
        $city = isset($_POST['city']) ? strtolower($_POST['city']) : null;
        $billing_address_1 = isset($_POST['billing_address_1']) ? strtolower($_POST['billing_address_1']) : null;
        $billing_state = isset($_POST['billing_state']) ? strtolower($_POST['billing_state']) : null;
        $billing_postcode = isset($_POST['billing_postcode']) ? strtolower($_POST['billing_postcode']) : null;

        // Establecer cookies
        setcookie('payment_method_selected', $payment_method_selected, time() + 864000, '/');
        setcookie('billing_city', ucwords($city), time() + 864000, '/');
        setcookie('billing_country', strtoupper($country), time() + 864000, '/');
        setcookie('billing_address_1', ucwords($billing_address_1), time() + 864000, '/');
        setcookie('billing_state', strtoupper($billing_state), time() + 864000, '/');
        setcookie('billing_postcode', ucwords($billing_postcode), time() + 864000, '/');

        // Redirigir al checkout
        redirect_to_checkout(false, false, false, $product_id, $coupon_code, $fixed_fee_inscription, $fees, $fee_payment_completed, $institute_id, $coupon_complete);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'pay_graduation_fee') {

        $student = get_student_detail($_GET['student_id']);
        setcookie('fee_student_id', $student->id, time() + 864000, '/');
        setcookie('institute_id', $student->institute_id, time() + 864000, '/');

        // Vaciar carrito existente
        WC()->cart->empty_cart();

        // Agregar nuevo producto
        $fee_graduation_id = get_fee_product_id($student->id, 'graduation');
        WC()->cart->add_to_cart($fee_graduation_id, 1);

        // Redireccionar a checkout
        wp_redirect(wc_get_checkout_url());
        exit;
    }

    if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == 'change_payment_method') {
        setcookie('payment_method_selected', '', time() - 3600, '/');
        wp_redirect(home_url('/select-payment'));
        exit;
    }
}

function redirect_to_checkout($from_webinar = false, $is_scholarship = false, $return_url = false, $product_id = false, $coupon_code = false, $fixed_fee_inscription = false, $fees = [], $fee_payment_completed = null, $institute_id = null, $coupon_complete = null)
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();

    $woocommerce->cart->add_to_cart($product_id, 1);
    if (!$fee_payment_completed) {
        foreach ($fees as $key => $fee) {
            if ($fee->type_fee == 'registration') {
                $woocommerce->cart->add_to_cart($fee->product_id, 1);
            }
        }
    }

    if (isset($coupon_code) && !empty($coupon_code)) {
        $woocommerce->cart->apply_coupon($coupon_code);
    }

    if (!$from_webinar && !$is_scholarship) {

        // Obtener la fecha máxima desde las opciones
        $max_date_timestamp = get_option('max_date_offer');

        // Verificar si el cupón está vigente
        if (!empty(get_option('offer_complete')) && $max_date_timestamp >= current_time('timestamp')) {
            // Aplicar cupón si NO ha expirado
            if (!$fixed_fee_inscription) {
                $woocommerce->cart->apply_coupon(get_option('offer_complete'));
            }
        }

        if (!empty($coupon_complete)) {
            if (!$fixed_fee_inscription) {
                $woocommerce->cart->apply_coupon($coupon_complete);
            }
        }


        if ($_SERVER['HTTP_HOST'] === 'portal.americanelite.school') {
            if ($institute_id == 84) {
                $woocommerce->cart->apply_coupon("100% registration fee");
            }
        }

    } else if ($is_scholarship) {
        global $wpdb;
        $table_pre_scholarship = $wpdb->prefix . 'pre_scholarship';
        $pre_scholarship = $wpdb->get_row("SELECT * FROM {$table_pre_scholarship} WHERE document_id = {$is_scholarship}");
        if ($pre_scholarship) {
            $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
            $asigned_scholarship = $wpdb->get_row("SELECT * FROM {$table_scholarships_availables} WHERE id = {$pre_scholarship->scholarship_type}");

            $coupons = json_decode($asigned_scholarship->coupons);
            foreach ($coupons as $key => $coupon) {
                $woocommerce->cart->apply_coupon($coupon);
            }
        } else {
            $woocommerce->cart->apply_coupon('Honor Excellent AES');
        }

        setcookie('is_scholarship', 1, time() + 3600, '/');
    } else if ($from_webinar) {
        $woocommerce->cart->apply_coupon(__('100% Registration fee', 'edusystem'));
        setcookie('from_webinar', 1, time() + 3600, '/', '/');
    }

    if (is_user_logged_in()) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $partner_id = $current_user->ID;
        $table_students = $wpdb->prefix . 'students';
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_students WHERE partner_id = %s", $partner_id));
        if (sizeof($result) > 1) {
            $woocommerce->cart->apply_coupon('Brothers discount');
        } else if (sizeof($result) == 1) {
            $woocommerce->cart->apply_coupon('Brothers discount initial');
        }
    }

    if ($return_url) {
        return wc_get_checkout_url();
    } else {
        wp_redirect(wc_get_checkout_url());
        exit;
    }
}

add_action('wp_loaded', 'save_student');

add_action('woocommerce_account_student_endpoint', function () {

    global $current_user;
    $roles = $current_user->roles;

    if (!in_array('parent', $roles) && in_array('student', $roles)) {
        $student_id = get_user_meta(get_current_user_id(), 'student_id', true);
        if ($student_id) {
            $student = get_student_from_id($student_id);
        } else {
            $student = get_student(get_current_user_id());
        }
    }

    if (in_array('parent', $roles) && in_array('student', $roles) || in_array('parent', $roles) && !in_array('student', $roles)) {
        $student = get_student(get_current_user_id());
    }


    include(plugin_dir_path(__FILE__) . 'templates/student.php');
});

add_action('woocommerce_account_my-tickets_endpoint', function () {

    global $current_user, $wpdb;
    $table_tickets_created = $wpdb->prefix . 'tickets_created';
    $tickets = $wpdb->get_results("SELECT * FROM {$table_tickets_created}  WHERE user_id = {$current_user->ID} ORDER BY id DESC");

    include(plugin_dir_path(__FILE__) . 'templates/my-tickets.php');
});

add_action('woocommerce_account_my-requests_endpoint', function () {

    global $current_user, $wpdb;
    $roles = $current_user->roles;
    $table_requests = $wpdb->prefix . 'requests';
    $table_students = $wpdb->prefix . 'students';
    $table_type_requests = $wpdb->prefix . 'type_requests';

    $partner_id = null;
    $student_id = null;
    $students = [];
    if (in_array('parent', $roles) && !in_array('student', $roles)) {
        $partner_id = $current_user->ID;
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id='{$partner_id}'");
    } else if (in_array('parent', $roles) && in_array('student', $roles)) {
        $partner_id = $current_user->ID;
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id='{$partner_id}'");
    } else if (!in_array('parent', $roles) && in_array('student', $roles)) {
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
        $partner_id = $student->partner_id;
        $student_id = $student->id;
    }

    $requests = $wpdb->get_results("SELECT * FROM {$table_requests} WHERE partner_id = {$partner_id} ORDER BY id DESC");
    $types = $wpdb->get_results("SELECT * FROM {$table_type_requests}");

    include(plugin_dir_path(__FILE__) . 'templates/my-requests.php');
});


add_action('woocommerce_account_califications_endpoint', function () {

    global $current_user, $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $roles = $current_user->roles;
    $students = [];
    $students_formatted = [];
    $students_formatted_history = [];

    if (!in_array('parent', $roles) && in_array('student', $roles)) {
        $student_id = get_user_meta($current_user->ID, 'student_id', true);
        $students = get_student_from_id($student_id);
    }

    if (in_array('parent', $roles) && !in_array('student', $roles) || in_array('parent', $roles) && in_array('student', $roles)) {
        $students = get_student($current_user->ID);
    }

    foreach ($students as $key => $student) {
        $moodle_student_id = $student->moodle_student_id;
        $formatted_assignments_history = [];

        if ($moodle_student_id) {
            $assignments = student_assignments_moodle($student->id);
            $assignments_course = $assignments['assignments'];
            $assignments_student = $assignments['grades'];
            $formatted_assignments = [];

            // print_r($assignments);
            foreach ($assignments_course as $key => $assignment_c) {
                $course_id = (int) $assignment_c['id'];

                $assignments_coursing = $assignment_c['assignments'];
                $assignments_work = [];

                $filtered_course_student = array_filter($assignments_student, function ($entry) use ($course_id) {
                    return $entry['course_id'] == $course_id;
                });
                $filtered_course_student = array_values($filtered_course_student);

                if ($filtered_course_student[0]) {
                    $assignments_student_filtered = $filtered_course_student[0]['grades'][0]['gradeitems'];

                    foreach ($assignments_student_filtered as $key => $work) {
                        if (isset($work['cmid']) && $work['gradeishidden'] == 0) {
                            $cmid = $work['cmid'];
                            $filtered_assignments_coursing = array_filter($assignments_coursing, function ($entry) use ($cmid) {
                                return $entry['cmid'] == $cmid;
                            });
                            $filtered_assignments_coursing = array_values($filtered_assignments_coursing);

                            array_push($assignments_work, [
                                'name' => $work['itemname'],
                                'max_grade' => $work['grademax'],
                                'grade' => (isset($work['gradeformatted']) && $work['gradeformatted'] != '') ? $work['gradeformatted'] : '-',
                                'max_date' => isset($filtered_assignments_coursing[0]) && $filtered_assignments_coursing[0]['duedate'] != 0 ? wp_date('Y-m-d', $filtered_assignments_coursing[0]['duedate']) : '-',
                            ]);
                        }
                    }

                    array_push($formatted_assignments, [
                        'course_id' => $course_id,
                        'course' => $assignment_c['fullname'],
                        'assignments' => $assignments_work
                    ]);
                }
            }

            array_push($students_formatted, [
                'student' => $student,
                'formatted_assignments' => $formatted_assignments
            ]);
        }

        $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id} AND code_subject IS NOT NULL AND code_subject <> ''");
        if ($inscriptions) {

            foreach ($inscriptions as $key => $inscription) {
                if ($inscription->status_id == 3 || $inscription->status_id == 4) {
                    if ($inscription->subject_id) {
                        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$inscription->subject_id}");
                    } else {
                        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE code_subject = '{$inscription->code_subject}'");
                    }
                    array_push($formatted_assignments_history, [
                        $key => $subject->name,
                        'code_subject' => $subject->code_subject,
                        'subject' => $subject->name,
                        'code_period' => $inscription->code_period,
                        'cut' => $inscription->cut_period,
                        'hc' => $subject->hc,
                        'calification' => $inscription->calification,
                        'status_id' => $inscription->status_id,
                    ]);
                }
            }

            array_push($students_formatted_history, [
                'student' => $student,
                'formatted_assignments_history' => $formatted_assignments_history
            ]);
        }
    }

    $admin_virtual_access = get_option('virtual_access');
    include(plugin_dir_path(__FILE__) . 'templates/califications.php');

    // Successful login actividad de acceso
    $first_name   = get_user_meta($current_user->ID, 'first_name', true);
    $last_name = get_user_meta($current_user->ID, 'last_name', true);
    $message = sprintf(__('The student %s saw grades.', 'edusystem'), $first_name . ' ' . $last_name);
    edusystem_get_log($message, 'califications', $current_user->ID);
});

add_action('woocommerce_account_teacher-course-students_endpoint', function () {
    global $wpdb, $current_user;
    $offer_id = $_GET['offer_id'];
    $status = $_GET['status'];
    $offer = get_academic_offer_details($offer_id);
    $subject = get_subject_details($offer->subject_id);
    $inscriptions = get_inscriptions_by_subject_period($subject->id, $subject->code_subject, $offer->code_period, $offer->cut_period, $status);

    $students = [];
    foreach ($inscriptions as $key => $inscription) {
        $student = get_student_detail($inscription->student_id);
        $student->inscription_at = $inscription->created_at;
        $student->inscription_status_id = $inscription->status_id;
        array_push($students, $student);
    }

    // Sort the $students array by last_name
    usort($students, function ($a, $b) {
        return strcmp($a->last_name, $b->last_name);
    });

    include(plugin_dir_path(__FILE__) . 'templates/teacher-course-students.php');
});

add_action('woocommerce_account_teacher-courses_endpoint', function () {
    global $wpdb, $current_user;

    $teacher = get_teacher_details($current_user->user_email);
    if (is_wp_error($teacher)) {
        wc_print_notice($teacher->get_error_message(), 'error');
        return;
    }
    if (!$teacher) {
        wc_print_notice(__('Teacher profile not found.', 'edusystem'), 'error');
        return;
    }

    $load = load_current_cut();
    $code = $load['code'];
    $cut = $load['cut'];

    $history = get_teacher_offers($teacher->id, $code, $cut, 'history');
    $current = get_teacher_offers($teacher->id, $code, $cut, 'current');

    // Combina todos los subject_id de 'history' y 'current'
    $all_offers = array_merge($history, $current);
    $subject_ids = array_unique(array_column($all_offers, 'subject_id'));

    $subjects_map = [];
    if (!empty($subject_ids)) {
        $subjects = get_subjects_details_multiple($subject_ids);
        if (!is_wp_error($subjects) && !empty($subjects)) {
            foreach ($subjects as $subject) {
                $subjects_map[$subject->id] = $subject;
            }
        }
    }

    // Función auxiliar para procesar las ofertas
    // Se añade un nuevo parámetro $status_to_check para la función de calificación
    $process_offers = function (&$offers, $subjects_map, $status_to_check) {
        foreach ($offers as $key => $offer) {
            $subject_name = __('N/A', 'edusystem');
            $subject_code = __('N/A', 'edusystem');

            if (isset($subjects_map[$offer->subject_id])) {
                $subject = $subjects_map[$offer->subject_id];
                $subject_name = $subject->name;
                $subject_code = $subject->code_subject;
            }

            $offers[$key]->subject = $subject_name;
            $offers[$key]->code_subject = $subject_code;

            // Obtener calificación promedio
            $average_calification_data = get_average_calification_for_subject_period(
                $offer->subject_id,
                $subject_code,
                $offer->code_period,
                $offer->cut_period,
                $status_to_check // Pasar el status_id aquí
            );
            $offers[$key]->prom_calification = ($average_calification_data && $average_calification_data->average_calification !== null) ? (float) $average_calification_data->average_calification : 0;
            $offers[$key]->count_students = ($average_calification_data && $average_calification_data->inscription_count !== null) ? (int) $average_calification_data->inscription_count : 0;
        }
    };

    // Procesar las ofertas de historial (status_id = 3) y actuales (status_id = 1)
    $process_offers($history, $subjects_map, 'history'); // Para historial, usar status_id 2,3,4
    $process_offers($current, $subjects_map, 'current'); // Para current, usar status_id 1

    include(plugin_dir_path(__FILE__) . 'templates/teacher-courses.php');
});

function get_average_calification_for_subject_period($subject_id, $code_subject, $code_period, $cut_period, $status)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    // At least one of subject_id or code_subject must be provided
    if (empty($subject_id) && empty($code_subject)) {
        return new WP_Error('missing_subject_identifier', __('Either subject ID or subject code must be provided.', 'edusystem'));
    }

    // Determinar los status_id según el valor de $status
    $status_ids = [];
    if ($status === 'current') {
        $status_ids[] = 1;
    } elseif ($status === 'history') {
        $status_ids[] = 2;
        $status_ids[] = 3;
        $status_ids[] = 4;
    } else {
        // Manejar un caso por defecto o lanzar un error si $status no es 'current' ni 'history'
        return new WP_Error('invalid_status', __('Invalid status provided. Must be "current" or "history".', 'edusystem'));
    }

    // Convertir el array de IDs a una cadena para la cláusula IN de SQL
    $status_ids_in_clause = implode(',', array_map('intval', $status_ids));

    $where_clauses = [];
    $prepare_args = [];

    if (!empty($subject_id) && filter_var($subject_id, FILTER_VALIDATE_INT)) {
        $where_clauses[] = 'subject_id = %d';
        $prepare_args[] = $subject_id;
    }

    if (!empty($code_subject)) {
        $where_clauses[] = 'code_subject = %s';
        $prepare_args[] = $code_subject;
    }

    // Combine conditions with OR
    $subject_condition = implode(' OR ', $where_clauses);

    // Add period and status conditions
    // NOTA: status_id IN ({$status_ids_in_clause}) no se pasa como un placeholder de prepare
    // porque ya está sanitizado con implode y array_map('intval').
    $query = "SELECT ROUND(AVG(calification), 2) as average_calification, COUNT(*) as inscription_count
              FROM {$table_student_period_inscriptions}
              WHERE ({$subject_condition})
              AND code_period = %s
              AND cut_period = %s
              AND `status_id` IN ({$status_ids_in_clause})";

    $prepare_args[] = $code_period;
    $prepare_args[] = $cut_period;

    $results = $wpdb->get_row($wpdb->prepare(
        $query,
        ...$prepare_args // Use the spread operator to pass all arguments
    ));

    // Si no hay resultados (ej. ninguna inscripción con ese status_id), AVG() y COUNT() devuelven NULL.
    // Aseguramos que average_calification y inscription_count existan en el objeto incluso si son NULL.
    if (null === $results) {
        $results = (object) ['average_calification' => null, 'inscription_count' => 0]; // Cambié null a 0 para count
    }

    return $results;
}

add_action('woocommerce_account_student-details_endpoint', function () {

    $student = get_student_detail($_GET['student']);
    include(plugin_dir_path(__FILE__) . 'templates/student-details.php');
});

function get_student($partner_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id={$partner_id}");
    return $data;
}

function get_student_from_id($student_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $data = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id={$student_id}");
    return $data;
}

function insert_student($order)
{
    $customer_id = $order->get_customer_id();
    $registration_data = $order->meta_exists('registration_data') ?  json_decode($order->get_meta('registration_data'), true) : null;
    $student = $registration_data['student'];
    $program = $registration_data['program'];

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $formats_to_try = ['d/m/Y', 'm/d/Y'];
    $birth_date_obj = null;
    foreach ($formats_to_try as $format) {
        // Intenta crear el objeto DateTime con el formato actual
        $birth_date_obj = DateTime::createFromFormat($format, $student['birth_date']);

        // Si se crea el objeto correctamente, sal del bucle
        if ($birth_date_obj !== false) {
            break;
        }
    }

    $today = new DateTime();
    $age = $today->diff($birth_date_obj)->y;
    $email = trim($student['email']);
    $exist = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email = '{$email}'");
    if (!$exist) {
        $wpdb->insert($table_students, [
            'name' => trim($student['name']),
            'type_document' => $student['type_document'],
            'id_document' => $student['id_document'],
            'academic_period' => $code,
            'initial_cut' => $cut,
            'middle_name' => trim($student['middle_name']),
            'last_name' => trim($student['last_name']),
            'middle_last_name' => trim($student['middle_last_name']),
            'birth_date' => date_i18n('Y-m-d', strtotime($student['birth_date'])),
            'grade_id' => (int) $program['initial_grade'],
            'name_institute' => strtoupper($program['name_institute']),
            'institute_id' => (int) $program['institute_id'],
            'postal_code' => $order->get_billing_postcode(),
            'gender' => $student['gender'],
            'program_id' => $program['program_id'],
            'expected_graduation_date' => $program['expected_graduation_date'],
            'partner_id' => $customer_id,
            'phone' => $student['phone'],
            'email' => $email,
            'status_id' => 0,
            'set_password' => ($age >= 18 ? 1 : 0),
            'country' => $order->get_billing_country(),
            'city' => $order->get_billing_city(),
            'ethnicity' => $student['ethnicity'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    $student_id = $wpdb->insert_id;
    insert_register_program($student_id, $program);
    update_metadata_student($student);
    return $student_id;
}

function update_metadata_student($student)
{
    // 1. Obtener el objeto de usuario por email
    $user_student = get_user_by('email', $student['email']);
    if ($user_student) {
        $locale = get_option('default_lang_site');
        $user_id = $user_student->ID;
        $lang_code = $student['locale'];
        $supported_languages = ['en_EN', 'es_ES'];

        if (isset($lang_code) && in_array($lang_code, $supported_languages)) {
            $locale = $lang_code;
        }

        update_user_meta($user_id, 'locale', $locale);
    }
}

function create_user_student($student_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    if ($data) {

        $user = get_user_by('email', $data->email);

        if (!$user) {
            $user_id = wp_create_user($data->email, generate_password_user(), $data->email);
            $user = new WP_User($user_id);
            $user->set_role('student');

            update_user_meta($user_id, 'first_name', $data->name);
            update_user_meta($user_id, 'last_name', $data->last_name);
            update_user_meta($user_id, 'billing_phone', $data->phone);
            update_user_meta($user_id, 'billing_email', $data->email);
            update_user_meta($user_id, 'birth_date', $data->birth_date);
            update_user_meta($user_id, 'student_id', $student_id);
            wp_new_user_notification($user_id, null, 'both');
            return $user_id;
        } else {
            $user->add_role('student');
        }

        update_user_meta($user->ID, 'student_id', $student_id);

        return $user->ID;
    }
}

function update_status_student($student_id, $status_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $wpdb->update($table_students, [
        'status_id' => $status_id,
        'updated_at' => date('Y-m-d H:i:s')
    ], ['id' => $student_id]);
}

function update_elective_student($student_id, $status_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $wpdb->update($table_students, [
        'elective' => $status_id
    ], ['id' => $student_id]);
}

/**
 * Inserta los registros de documentos requeridos para un estudiante, evitando duplicados.
 *
 * Esta versión está optimizada para usar solo dos consultas a la base de datos,
 * independientemente de la cantidad de documentos.
 *
 * @param int $student_id ID del estudiante.
 */
function insert_register_program($student_id, $program_data)
{
    global $wpdb;
    $table_programs_by_student = $wpdb->prefix . 'programs_by_student';
    $wpdb->insert($table_programs_by_student, [
        'student_id' => $student_id,
        'program_identificator' => $program_data['program_id'],
        'career_identificator' => $program_data['career_id'],
        'mention_identificator' => $program_data['mention_id'],
        'plan_identificator' => $program_data['plan_id'],
    ]);
}

/**
 * Inserta los registros de documentos requeridos para un estudiante, evitando duplicados.
 *
 * Esta versión está optimizada para usar solo dos consultas a la base de datos,
 * independientemente de la cantidad de documentos.
 *
 * @param int $student_id ID del estudiante.
 * @param int $grade_id ID del grado del estudiante.
 */
function insert_register_documents( $student_id )
{
    global $wpdb;

    $student = get_student_detail($student_id);
    if ( !$student ) return;

    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_documents = $wpdb->prefix . 'documents';
    $table_documents_certificates = $wpdb->prefix . 'documents_certificates';

    // Obtencion de los documentos por el programa, carrera o mencion
    $documents = $wpdb->get_results( $wpdb->prepare(
        "SELECT `d`.id, `d`.name, `d`.is_visible, `d`.type_file, `d`.id_requisito,
            CASE
                WHEN `d`.is_required = 1 THEN 1
                WHEN `d`.is_required = 0 THEN
                CASE
                    WHEN `ps`.mention_identificator IS NOT NULL AND `ps`.mention_identificator <> ''
                        AND JSON_CONTAINS_PATH( `d`.academic_department, 'one', 
                            CONCAT('$.mention.\"', ps.mention_identificator, '\"') 
                        ) = 1
                    THEN IF( 
                        JSON_UNQUOTE( JSON_EXTRACT( `d`.academic_department,
                            CONCAT('$.mention.\"', ps.mention_identificator, '\".required')
                        )) = 'true',
                        1, 0
                    )
                    
                    WHEN `ps`.career_identificator IS NOT NULL AND `ps`.career_identificator <> ''
                        AND JSON_CONTAINS_PATH( `d`.academic_department, 'one',
                            CONCAT('$.career.\"', ps.career_identificator, '\"')
                        ) = 1
                    THEN IF(
                        JSON_UNQUOTE( JSON_EXTRACT( `d`.academic_department,
                            CONCAT('$.career.\"', ps.career_identificator, '\".required')
                        )) = 'true',
                        1, 0
                    )

                    WHEN `ps`.program_identificator IS NOT NULL AND `ps`.program_identificator <> ''
                        AND JSON_CONTAINS_PATH( `d`.academic_department, 'one',
                            CONCAT('$.program.\"', ps.program_identificator, '\"')
                        ) = 1
                    THEN IF(
                        JSON_UNQUOTE( JSON_EXTRACT( `d`.academic_department,
                            CONCAT('$.program.\"', ps.program_identificator, '\".required')
                        )) = 'true',
                        1, 0
                    )
                    ELSE 0
                END
                ELSE 0
            END AS is_required
        FROM {$wpdb->prefix}programs_by_student ps
        CROSS JOIN {$wpdb->prefix}documents d
        WHERE `ps`.student_id = %d
            AND (
                    ( `ps`.mention_identificator IS NOT NULL AND `ps`.mention_identificator <> ''
                    AND JSON_CONTAINS_PATH(`d`.academic_department, 'one', CONCAT('$.mention.\"', ps.mention_identificator, '\"')) )
                OR ( `ps`.career_identificator IS NOT NULL AND `ps`.career_identificator <> ''
                    AND JSON_CONTAINS_PATH(`d`.academic_department, 'one', CONCAT('$.career.\"', ps.career_identificator, '\"')))
                OR (`ps`.program_identificator IS NOT NULL AND `ps`.program_identificator <> ''
                    AND JSON_CONTAINS_PATH(`d`.academic_department, 'one', CONCAT('$.program.\"', ps.program_identificator, '\"')))
                OR COALESCE(JSON_LENGTH(`d`.academic_department), 0) = 0
            ) ",
        $student_id
    ));
    if ( empty($documents) ) return;

    // Validaciones de dublicados para documentos usando 'name' como identificador
    $document_names = wp_list_pluck($documents, 'name' );
    $placeholders_grade = implode(', ', array_fill(0, count($document_names), '%s'));
    $query_params_grade = array_merge([$student_id], $document_names );
    $existing_docs_names = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT document_id FROM {$table_student_documents} WHERE student_id = %d AND document_id IN ({$placeholders_grade})",
            ...$query_params_grade
        )
    );

    // verifica si es mayor o menor de la condicion 
    $birthDate = new DateTime( $student->birth_date );
    $is_legal_age = ( $birthDate->diff(new DateTime())->y >= 18 );

    // Insercion de documentos
    foreach ( $documents as $document ) {
        
        $document_name = $document->name;
        if (is_null($document_name)) continue;

        // valida si el documento existe por el nombre
        if (in_array( $document_name, $existing_docs_names, true)) continue;

        $is_required = $document->is_required;
        $is_visible = $document->is_visible;
        $type_file = $document->type_file;
        $id_requisito = $document->id_requisito;
        $doc_id = $document->id;

        // debes poner la condicion de mayor de menor de edad
        if ($is_legal_age && $document_name == 'ID OR CI OF THE PARENTS' && false) {
            $is_required = 0;
            $is_visible = 0;
        }        

        $wpdb->insert($table_student_documents, [
            'student_id' => $student_id,
            'document_id' => $document_name, 
            'doc_id' => $doc_id,
            'is_required' => $is_required,
            'is_visible' => $is_visible,
            'type_file' => $type_file,
            'id_requisito' => $id_requisito,
            'status' => 0,
            'created_at' => current_time('mysql'),
        ]);
    }

    // Obtener documentos automaticos
    $automatic_docs = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$table_documents_certificates} WHERE `type` = %s and `status` = %d", 'automatic', 1)
    );

    // Valida duplicados de documentos automaticos
    $automatic_doc_id = wp_list_pluck($automatic_docs, 'document_identificator');
    if ( !empty($automatic_doc_id) ) {

        $placeholders_auto = implode(', ', array_fill(0, count($automatic_doc_id), '%s'));
        $query_params_auto = array_merge([$student_id], $automatic_doc_id);

        $existing_auto_doc_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT document_id FROM {$table_student_documents} WHERE student_id = %d AND document_id IN ({$placeholders_auto})",
                ...$query_params_auto
            )
        );
    } else {
        $existing_auto_doc_ids = [];
    }

    // Insertar documentos automaticos faltances
    foreach ( $automatic_docs as $doc ) {

        $document_id_to_insert = $doc->document_identificator;

        // valida si va a insertar
        if ( in_array($document_id_to_insert, $existing_auto_doc_ids, true) ) continue;

        $wpdb->insert($table_student_documents, [
            'student_id' => $student_id,
            'document_id' => $document_id_to_insert,
            'doc_id' => $doc->id,
            'is_required' => $doc->is_required,
            'is_visible' => $doc->is_visible,
            'type_file' => $doc->id_requisito ?? null,
            'id_requisito' => $doc->id_requisito ?? null,
            'automatic' => 1,
            'status' => 0,
            'created_at' => current_time('mysql'),
        ]);
    }
}

// respalda la funcion
/* function insert_register_documents($student_id, $grade_id)
{
    global $wpdb;

    $student = get_student_detail($student_id);
    if (!$student) {
        return;
    }

    $birthDate = new DateTime($student->birth_date);
    $is_legal_age = ($birthDate->diff(new DateTime())->y >= 18);

    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_documents = $wpdb->prefix . 'documents';
    $table_documents_certificates = $wpdb->prefix . 'documents_certificates';

    // 1. OBTENCIÓN DE DOCUMENTOS PARA EL GRADO
    $documents_for_grade = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$table_documents} WHERE grade_id = %d", $grade_id)
    );

    if (empty($documents_for_grade)) {
        return;
    }

    // 2. VALIDACIÓN DE DUPLICADOS PARA DOCUMENTOS DEL GRADO (Usando 'name' como identificador)
    $document_names_for_grade = wp_list_pluck($documents_for_grade, 'name');

    // Si no hay nombres, salimos (aunque ya validamos $documents_for_grade)
    if (empty($document_names_for_grade)) {
        return;
    }

    $placeholders_grade = implode(', ', array_fill(0, count($document_names_for_grade), '%s'));

    // Obtenemos los 'document_id' (que son los nombres) ya existentes
    $query_params_grade = array_merge([$student_id], $document_names_for_grade);
    $existing_docs_names = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT document_id FROM {$table_student_documents} WHERE student_id = %d AND document_id IN ({$placeholders_grade})",
            ...$query_params_grade
        )
    );

    // 3. INSERCIÓN DE DOCUMENTOS FALTANTES DEL GRADO
    foreach ($documents_for_grade as $document) {
        // Usar la columna 'name' como el document_id
        $document_id_to_insert = $document->name;

        // Si el documento ya existe (validación de existencia con el nombre), lo saltamos.
        if (in_array($document_id_to_insert, $existing_docs_names, true)) {
            continue;
        }

        // Si el nombre del documento es NULL por alguna razón, lo saltamos para evitar el error.
        if (is_null($document_id_to_insert)) {
            continue;
        }

        $is_required = $document->is_required;
        $is_visible = $document->is_visible;

        if ($is_legal_age && $document->name === 'ID OR CI OF THE PARENTS') {
            $is_required = 0;
            $is_visible = 0;
        }

        $wpdb->insert($table_student_documents, [
            'student_id' => $student_id,
            'document_id' => $document_id_to_insert, // CORREGIDO: Usamos $document->name
            'is_required' => $is_required,
            'is_visible' => $is_visible,
            'status' => 0,
            'created_at' => current_time('mysql'),
        ]);
    }

    // 4. OBTENCIÓN DE DOCUMENTOS AUTOMÁTICOS
    $automatic_docs = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$table_documents_certificates} WHERE `type` = %s and `status` = %d", 'automatic', 1)
    );

    // 5. VALIDACIÓN DE DUPLICADOS PARA DOCUMENTOS AUTOMÁTICOS
    $automatic_doc_identifiers = wp_list_pluck($automatic_docs, 'document_identificator');
    if (!empty($automatic_doc_identifiers)) {
        $placeholders_auto = implode(', ', array_fill(0, count($automatic_doc_identifiers), '%s'));
        $query_params_auto = array_merge([$student_id], $automatic_doc_identifiers);

        $existing_auto_doc_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT document_id FROM {$table_student_documents} WHERE student_id = %d AND document_id IN ({$placeholders_auto})",
                ...$query_params_auto
            )
        );
    } else {
        $existing_auto_doc_ids = [];
    }

    // 6. INSERCIÓN DE DOCUMENTOS AUTOMÁTICOS FALTANTES
    foreach ($automatic_docs as $doc) {
        $document_id_to_insert = $doc->document_identificator;

        if (in_array($document_id_to_insert, $existing_auto_doc_ids, true)) {
            continue;
        }

        $wpdb->insert($table_student_documents, [
            'student_id' => $student_id,
            'document_id' => $document_id_to_insert,
            'is_required' => $doc->is_required,
            'is_visible' => $doc->is_visible,
            'status' => 0,
            'created_at' => current_time('mysql'),
        ]);
    }
} */

function get_documents($student_id)
{

    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
    return $documents;
}

function get_payments($student_id, $product_id = false)
{
    global $wpdb;
    $table_student_payments = $wpdb->prefix . 'student_payments';

    if ($product_id) {
        $payments = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id={$student_id} AND product_id={$product_id}");
        return $payments;
    } else {
        $products = [get_fee_product_id($student_id, 'registration'), get_fee_product_id($student_id, 'graduation')];
        $products_list = "'" . implode("','", $products) . "'";
        $payments = $wpdb->get_row(
            "SELECT * FROM {$table_student_payments}
            WHERE student_id = {$student_id}
            AND product_id NOT IN ({$products_list})"
        );

        $has_pending_payments = $wpdb->get_results(
            "SELECT * FROM {$table_student_payments}
            WHERE student_id = {$student_id}
            AND product_id NOT IN ({$products_list})
            AND status_id = 0"
        );

        $program = $payments ? ($has_pending_payments ? 2 : 1) : 0;
        return $program;
    }
}

function get_name_grade($grade_id)
{
    global $wpdb;
    $table_grades = $wpdb->prefix . 'grades';
    $grade = $wpdb->get_row("SELECT * FROM {$table_grades} WHERE id = " . $grade_id);
    return $grade->name;
}

function get_name_program_student($student_id)
{
    global $wpdb;

    $table_programs_by_student = $wpdb->prefix . 'programs_by_student';
    $table_student_program = $wpdb->prefix . 'student_program';

    // Get all program_identificators for the given student_id
    $program_identificators = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT program_identificator FROM %i WHERE student_id = %d",
            $table_programs_by_student,
            $student_id
        )
    );

    // If no program_identificators are found, return an empty string
    if (empty($program_identificators)) {
        return '';
    }

    // Prepare the list of identificators for the SQL IN clause
    // array_map and esc_sql are used to properly escape each identificator
    $placeholders = implode(', ', array_fill(0, count($program_identificators), '%s'));
    $escaped_identificators = array_map('esc_sql', $program_identificators);

    // Get the program names from the wp_programs table
    $program_names = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT name FROM %i WHERE identificator IN ($placeholders)",
            $table_student_program,
            ...$escaped_identificators
        )
    );

    // Return the program names as a comma-separated string
    return implode(', ', $program_names);
}

function get_career_and_mention($student_id)
{
    $program_data_student = get_program_data_student($student_id);

    // Get the career name. Assuming it always exists and is an object with a 'name' property.
    $career_name = $program_data_student['career'][0]->name;

    // Safely check for the mention name. Uses null coalescing operator and a conditional check.
    // The conditional check is only executed if the mention exists in the array and is not empty.
    $mention_name = (
        isset($program_data_student['mention'][0]->name) &&
        is_array($program_data_student['mention']) &&
        !empty($program_data_student['mention'])
    ) ? $program_data_student['mention'][0]->name : '';

    // Construct the final string: "Career" + (" Mention" if it exists, otherwise "")
    // Using a simple ternary operator to conditionally add the space and the mention name.
    $result_text = $career_name . ($mention_name ? ' ' . $mention_name : '');

    return $result_text;
}

function get_term_student_entered($academic_period, $academic_period_cut)
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $period = $wpdb->get_row("SELECT * FROM {$table_academic_periods} WHERE `code` = '{$academic_period}'");
    return $period;
}

function get_name_program($identificator)
{

    global $wpdb;
    $table_student_program = $wpdb->prefix . 'student_program';

    $name = $wpdb->get_var($wpdb->prepare(
        "SELECT `name` FROM $table_student_program WHERE identificator LIKE %s",
        $identificator
    ));

    return $name;
}

function get_ethnicity($ethnicity)
{

    $program = match ($ethnicity) {
        '1' => __('African American', 'edusystem'),
        '2' => __('Asian', 'edusystem'),
        '3' => __('Caucasian', 'edusystem'),
        '4' => __('Hispanic', 'edusystem'),
        '5' => __('Native American', 'edusystem'),
        '7' => __('Choose Not To Respond', 'edusystem'),
        default => "",
    };

    return $program;
}

function get_gender($gender_id)
{

    $gender = match ($gender_id) {
        'male' => __('Male', 'edusystem'),
        'female' => __('Female', 'edusystem'),
        default => "",
    };

    return $gender;
}

function save_student_details()
{

    if (isset($_POST['action']) && !empty($_POST['action'])) {


        if ($_POST['action'] == 'save_student_details') {

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';

            $student_id = $_POST['student_id'];
            $document_type = $_POST['document_type'];
            $id_document = $_POST['id_document'];
            $first_name = $_POST['account_first_name'];
            $middle_name = $_POST['account_middle_name'];
            $middle_last_name = $_POST['account_middle_last_name'];
            $last_name = $_POST['account_last_name'];
            $email = $_POST['account_email'];
            $phone = $_POST['number_phone_hidden'];
            $gender = $_POST['gender'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $postal_code = $_POST['postal_code'];

            $wpdb->update($table_students, [
                'type_document' => $document_type,
                'id_document' => $id_document,
                'name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'middle_last_name' => $middle_last_name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'country' => $country,
                'city' => $city,
                'postal_code' => $postal_code,
            ], [
                'id' => $student_id
            ]);


            wc_add_notice(__('information changed successfully.', 'edusystem'), 'success');
            wp_redirect(wc_get_account_endpoint_url('student-details') . '/?student=' . $student_id);
            exit;
        }

        if ($_POST['action'] == 'save_password_moodle') {

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';

            $moodle_password = $_POST['password'];
            $student_id = $_POST['student_id'];
            $wpdb->update($table_students, ['moodle_password' => $moodle_password], ['id' => $student_id]);
            change_password_user_moodle($student_id);
            wc_add_notice(__('information changed successfully.', 'edusystem'), 'success');
            wp_redirect(wc_get_account_endpoint_url('student-details') . '/?student=' . $student_id);
            exit;
        }
    }

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'access_moodle_url') {

            global $wpdb, $current_user;
            $table_students = $wpdb->prefix . 'students';
            $student_id = $_GET['student_id'];
            $data = $wpdb->get_row("SELECT * FROM {$table_students} where id={$student_id}");

            if ($current_user && ($current_user->user_email == $data->email)) {

                if ($data) {

                    $data_url = get_url_login($data->email);

                    if (isset($data_url) && !empty($data_url)) {
                        nocache_headers();
                        wp_redirect($data_url);
                    } else {
                        nocache_headers();
                        wp_redirect(get_option('moodle_url'));
                    }

                    exit;
                }
            }
        }
    }
}

add_action('wp_loaded', 'save_student_details');

add_action('woocommerce_account_dashboard', 'view_access_classroom', 1);

function view_access_classroom()
{
    global $current_user, $wpdb;
    $roles = (array) $current_user->roles;
    
    if (!in_array('student', $roles)) {
        return;
    }

    $student_id_meta = get_user_meta($current_user->ID, 'student_id', true);
    
    $student_query = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}students WHERE id = %d OR partner_id = %d LIMIT 1",
        $student_id_meta,
        $current_user->ID
    ));

    if (!function_exists('edusystem_get_student_classroom_access')) {
        require_once dirname(plugin_dir_path(__FILE__)) . '/admin/student-access-helper.php';
    }

    $access_data = edusystem_get_student_classroom_access($student_query);
    
    $student        = $access_data['student_data'];
    $student_access = $access_data['access'];
    $error_access   = $access_data['error'];

    if ($student) {
        $access = is_enrolled_in_courses($student->id);
    }

    $subjects_coursing = [];
    $show_table_subjects_coursing = get_option('show_table_subjects_coursing');

    if ($show_table_subjects_coursing && $student) {
        $projection = get_projection_by_student($student->id);
        if ($projection) {
            $projection_obj = json_decode($projection->projection);
            if (is_array($projection_obj) || is_object($projection_obj)) {
                $subjects_coursing = array_values(array_filter((array)$projection_obj, function ($item) {
                    return isset($item->this_cut) && $item->this_cut === true;
                }));
            }
        }
    }

    $admin_virtual_access = get_option('virtual_access');

    include(plugin_dir_path(__FILE__) . 'templates/student-access-classroom.php');
}

add_filter('woocommerce_account_dashboard', 'load_feed', 0);
function load_feed()
{
    global $wpdb, $current_user;
    $table_feed = $wpdb->prefix . 'feed';
    $table_students = $wpdb->prefix . 'students';
    $today = date('Y-m-d');
    $roles = $current_user->roles;
    $subjects_coursing = [];
    $feeds = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_feed} WHERE max_date IS NULL OR max_date >= %s", $today));
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}' OR partner_id={$current_user->ID}");

    $orders = wc_get_orders(array(
        'status' => 'pending',
        'customer_id' => $current_user->ID,
    ));

    // VERIFICAR FEE DE INSCRIPCION
    include(plugin_dir_path(__FILE__) . 'templates/feed-student.php');
}

add_filter('woocommerce_account_dashboard', 'trigger_open_elective_modal', 0);
function trigger_open_elective_modal()
{
    $site_mode = get_option('site_mode');
    if (!is_user_logged_in() || $site_mode != 'SCHOOL') {
        return;
    }

    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    $user_email   = $current_user->user_email;
    $roles        = (array) $current_user->roles;
    $student      = null;

    // Obtener la información del estudiante asociado de forma eficiente
    if (in_array('student', $roles, true)) {
        // Buscar por email para roles de 'student'
        $student = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}students WHERE email = %s",
            $user_email
        ));
    } elseif (in_array('parent', $roles, true)) {
        // Buscar por partner_id (ID del padre/madre) para roles de 'parent'
        $student = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}students WHERE partner_id = %d",
            $user_id
        ));
    }

    // Si no hay estudiante asociado, no continuar la ejecución.
    if (!$student) {
        return;
    }

    if (!$student->moodle_student_id) {
        return;
    }

    // // Si el campo 'elective' no existe o es 1, no es necesario cargar el modal.
    // if (!isset($student->elective) || (int) $student->elective !== 0) {
    //     return;
    // }

    // Cargar los conteos de inscripciones
    // Asumiendo que load_inscriptions_electives_valid es una función definida en otro lugar
    $elective_count         = load_inscriptions_electives_valid($student, 'status_id = 3');
    $elective_count_current = load_inscriptions_electives_valid($student, 'status_id = 1');
    $status_elective = $elective_count < 2 && $elective_count_current === 0 && (int) $student->elective === 0 ? 0 : 1;

    if (
        in_array('student', $roles, true) && $elective_count_current === 0
    ) {
        // Usar trailingslashit y plugin_dir_path para asegurar el path correcto.
        include(trailingslashit(plugin_dir_path(__FILE__)) . 'templates/trigger-open-elective-modal.php');
    }
}

function set_max_date_student($student_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_payments = $wpdb->prefix . 'student_payments';

    // Define los IDs de productos a excluir
    $fee_inscription_id = get_fee_product_id($student_id, 'registration');
    $fee_graduation_id = get_fee_product_id($student_id, 'graduation');

    // Se busca el próximo pago pendiente (status_id = 0, date_payment IS NULL)
    // Excluyendo los product_id de inscripción y graduación
    $next_payment = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT date_next_payment, date_payment
             FROM {$table_student_payments}
             WHERE status_id = 0
               AND student_id = %d
               AND date_payment IS NULL
               AND product_id <> %d
               AND product_id <> %d
             ORDER BY cuote ASC
             LIMIT 1",
            $student_id,
            $fee_inscription_id,
            $fee_graduation_id
        )
    );

    $max_access_date = NULL; // Se inicializa a NULL por defecto

    if ($next_payment && !empty($next_payment->date_next_payment)) {
        // Se valida que date_next_payment exista y no esté vacío
        $date = $next_payment->date_next_payment;
        $days = (int) get_option('payment_due'); // Obtiene los días de gracia desde las opciones de WordPress
        $max_access_date = date('Y-m-d', strtotime("$date + $days days"));
    }

    // Se actualiza la tabla de estudiantes una sola vez
    $wpdb->update(
        $table_students,
        ['max_access_date' => $max_access_date],
        ['id' => $student_id]
    );
}

function helper_get_student_logged()
{
    global $wpdb, $current_user;
    $table_students = $wpdb->prefix . 'students';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
    return $student;
}

function helper_get_teacher_logged()
{
    global $wpdb, $current_user;
    $table_teachers = $wpdb->prefix . 'teachers';
    $student = $wpdb->get_row("SELECT * FROM {$table_teachers} WHERE email='{$current_user->user_email}'");
    return $student;
}

function get_active_students()
{
    global $wpdb;
    $students_active = [];

    $table_students = $wpdb->prefix . 'students';
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE status_id != 5 ORDER BY id DESC");

    if ($students) {
        foreach ($students as $student) {
            $academic_ready = get_academic_ready($student->id);
            if (!$academic_ready) {
                array_push($students_active, $student);
            }
        }
    }

    return $students_active;
}

/**
 * Obtiene el ID del instituto asociado a un estudiante dado su ID.
 * Utiliza la base de datos de WordPress para realizar la consulta.
 *
 * @param int $student_id ID del estudiante cuyo instituto se desea obtener.
 * 
 * @return int|null ID del instituto si se encuentra, null en caso contrario.
 */
function get_institute_by_student(?int $student_id = null)
{

    if (is_null($student_id)) return null; // Retorna null si no se proporciona un ID de estudiante

    global $wpdb;
    $institute_id = $wpdb->get_var($wpdb->prepare("SELECT institute_id FROM {$wpdb->prefix}students WHERE id = %d", $student_id));

    return (int) $institute_id ?? null;
}

