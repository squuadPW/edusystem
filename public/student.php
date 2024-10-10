<?php

function save_student()
{
    if (
        isset($_GET['action']) && !empty($_GET['action']) && ($_GET['action'] == 'save_student' || $_GET['action'] == 'new_applicant_others' || $_GET['action'] == 'new_applicant_me' || $_GET['action'] == 'save_student_custom' || $_GET['action'] == 'save_student_info')
    ) {

        $action = $_GET['action'];
        global $woocommerce;

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
        $grade = isset($_POST['grade']) ? $_POST['grade'] : null;
        $institute_id = isset($_POST['institute_id']) ? $_POST['institute_id'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        if (isset($email_partner) && ($email_partner === $email_student)) {
            wc_add_notice(__( 'Emails can\'t be the same', 'aes' ), 'error' );
            return;
        }

        setcookie('is_older', '', time() + 3600);
        setcookie('ethnicity', $ethnicity, time() + 3600);
        setcookie('billing_city', ucwords($city), time() + 3600);
        setcookie('billing_country', $country, time() + 3600);
        setcookie('initial_grade', $grade, time() + 3600);
        setcookie('program_id', $program, time() + 3600);
        setcookie('phone_student', $number_phone, time() + 3600);
        setcookie('id_document', $id_document, time() + 3600);
        setcookie('document_type', $document_type, time() + 3600);
        setcookie('email_student', $email_student, time() + 3600);
        setcookie('name_student', ucwords($name), time() + 3600);
        setcookie('middle_name_student', ucwords($middle_name_student), time() + 3600);
        setcookie('last_name_student', ucwords($last_name), time() + 3600);
        setcookie('middle_last_name_student', ucwords($middle_last_name_student), time() + 3600);
        setcookie('birth_date', $birth_date, time() + 3600);
        setcookie('gender', $gender, time() + 3600);
        setcookie('password', $password, time() + 3600);

        $id_bitrix = $_GET['idbitrix'];
        if (isset($id_bitrix)) {
            setcookie('id_bitrix', $id_bitrix, time() + 3600);
        }

        if (!empty($institute_id) && $institute_id != 'other') {
            $institute = get_institute_details($institute_id);
            $name_institute = strtolower($institute->name);
            setcookie('institute_id', $institute_id, time() + 3600);
        } else {
            $name_institute = isset($_POST['name_institute']) ? strtolower($_POST['name_institute']) : null;
        }

        setcookie('name_institute', ucwords($name_institute), time() + 3600);
        switch ($action) {
            case 'save_student':
                if (!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner) && !empty($birth_date_parent) && !empty($parent_document_type) && !empty($id_document_parent)) {
                    setcookie('agent_name', ucwords($agent_name), time() + 3600);
                    setcookie('agent_last_name', ucwords($agent_last_name), time() + 3600);
                    setcookie('email_partner', $email_partner, time() + 3600);
                    setcookie('number_partner', $number_partner, time() + 3600);
                    setcookie('birth_date_parent', $birth_date_parent, time() + 3600);
                    setcookie('parent_document_type', $parent_document_type, time() + 3600);
                    setcookie('id_document_parent', $id_document_parent, time() + 3600);
                    setcookie('gender_parent', $gender_parent, time() + 3600);
                } else {
                    setcookie('agent_name', ucwords($name), time() + 3600);
                    setcookie('agent_last_name', ucwords($last_name), time() + 3600);
                    setcookie('email_partner', $email_student, time() + 3600);
                    setcookie('number_partner', $number_phone, time() + 3600);
                    setcookie('birth_date_parent', $birth_date, time() + 3600);
                    setcookie('parent_document_type', $document_type, time() + 3600);
                    setcookie('id_document_parent', $id_document, time() + 3600);
                    setcookie('gender_parent', $gender, time() + 3600);
                }

                redirect_to_checkout($program, $grade);
                break;

            case 'save_student_custom':
                $current_user = wp_get_current_user();
                wp_update_user(array('ID' => $current_user->ID, 'user_pass' => $password));
                update_user_meta($current_user->ID, 'pay_application_password', 1);

                wp_redirect(get_permalink(wc_get_page_id('myaccount')));
                exit;
                break;

            case 'save_student_info':
                if (!empty($agent_name) && !empty($agent_last_name) && !empty($email_partner) && !empty($number_partner) && !empty($birth_date_parent) && !empty($parent_document_type) && !empty($id_document_parent)) {
                    setcookie('agent_name', ucwords($agent_name), time() + 3600);
                    setcookie('agent_last_name', ucwords($agent_last_name), time() + 3600);
                    setcookie('email_partner', $email_partner, time() + 3600);
                    setcookie('number_partner', $number_partner, time() + 3600);
                    setcookie('birth_date_parent', $birth_date_parent, time() + 3600);
                    setcookie('parent_document_type', $parent_document_type, time() + 3600);
                    setcookie('id_document_parent', $id_document_parent, time() + 3600);
                    setcookie('gender_parent', $gender_parent, time() + 3600);
                } else {
                    setcookie('agent_name', ucwords($name), time() + 3600);
                    setcookie('agent_last_name', ucwords($last_name), time() + 3600);
                    setcookie('email_partner', $email_student, time() + 3600);
                    setcookie('number_partner', $number_phone, time() + 3600);
                    setcookie('birth_date_parent', $birth_date, time() + 3600);
                    setcookie('parent_document_type', $document_type, time() + 3600);
                    setcookie('id_document_parent', $id_document, time() + 3600);
                    setcookie('gender_parent', $gender, time() + 3600);
                }

                wp_redirect(wp_get_referer() . '?action=fill_data');
                exit;
                break;

            case 'new_applicant_me':

                $document_type = $parent_document_type ? $parent_document_type : strtolower(get_user_meta(get_current_user_id(), 'type_document', true));
                $id_document = $id_document_parent ? $id_document_parent : strtolower(get_user_meta(get_current_user_id(), 'id_document', true));
                $number_phone = get_user_meta(get_current_user_id(), 'billing_phone', true);
                $email_student = strtolower(get_user_meta(get_current_user_id(), 'billing_email', true));
                $birth_date = $birth_date_parent ? $birth_date_parent : strtolower(get_user_meta(get_current_user_id(), 'birth_date', true));
                $gender = $gender_parent ? $gender_parent : strtolower(get_user_meta(get_current_user_id(), 'gender', true));
                $ethnicity = $ethnicity ? $ethnicity : strtolower(get_user_meta(get_current_user_id(), 'ethnicity', true));

                setcookie('ethnicity_parent', $ethnicity, time() + 3600);
                setcookie('phone_student', $number_phone, time() + 3600);
                setcookie('id_document', $id_document, time() + 3600);
                setcookie('document_type', $document_type, time() + 3600);
                setcookie('email_student', $email_student, time() + 3600);
                setcookie('birth_date', $birth_date, time() + 3600);
                setcookie('gender', $gender, time() + 3600);
                setcookie('agent_name', ucwords(get_user_meta(get_current_user_id(), 'first_name', true)), time() + 3600);
                setcookie('agent_last_name', ucwords(get_user_meta(get_current_user_id(), 'last_name', true)), time() + 3600);
                setcookie('email_partner', ucwords(get_user_meta(get_current_user_id(), 'billing_email', true)), time() + 3600);
                setcookie('number_partner', get_user_meta(get_current_user_id(), 'billing_phone', true), time() + 3600);
                setcookie('birth_date_parent', get_user_meta(get_current_user_id(), 'birth_date', true), time() + 3600);
                setcookie('parent_document_type', get_user_meta(get_current_user_id(), 'type_document', true), time() + 3600);
                setcookie('id_document_parent', get_user_meta(get_current_user_id(), 'id_document', true), time() + 3600);
                setcookie('gender_parent', get_user_meta(get_current_user_id(), 'gender_parent', true), time() + 3600);

                redirect_to_checkout($program, $grade);
                break;

            default:
                setcookie('agent_name', ucwords(get_user_meta(get_current_user_id(), 'first_name', true)), time() + 3600);
                setcookie('agent_last_name', ucwords(get_user_meta(get_current_user_id(), 'last_name', true)), time() + 3600);
                setcookie('email_partner', ucwords(get_user_meta(get_current_user_id(), 'billing_email', true)), time() + 3600);
                setcookie('number_partner', get_user_meta(get_current_user_id(), 'billing_phone', true), time() + 3600);
                setcookie('birth_date_parent', get_user_meta(get_current_user_id(), 'birth_date', true), time() + 3600);
                setcookie('parent_document_type', get_user_meta(get_current_user_id(), 'type_document', true), time() + 3600);
                setcookie('id_document_parent', get_user_meta(get_current_user_id(), 'id_document', true), time() + 3600);
                setcookie('gender_parent', get_user_meta(get_current_user_id(), 'gender_parent', true), time() + 3600);

                redirect_to_checkout($program, $grade);
                break;
        }
    }

    if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == 'fill_data') {
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
}

function redirect_to_checkout($program, $grade)
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();
    if ($program == 'aes') {
        switch ($grade) {
            case '1':
                $variation = wc_get_product(AES_DUAL_9NO_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(AES_DUAL_9NO, 1, AES_DUAL_9NO_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(AES_FEE_INSCRIPTION, 1);
                break;

            case '2':
                $variation = wc_get_product(AES_DUAL_10MO_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(AES_DUAL_10MO, 1, AES_DUAL_10MO_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(AES_FEE_INSCRIPTION, 1);
                break;

            default:
                $variation = wc_get_product(AES_DUAL_DEFAULT_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(AES_DUAL_DEFAULT, 1, AES_DUAL_DEFAULT_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(AES_FEE_INSCRIPTION, 1);
                break;
        }

    } else if ($program == 'psp') {
        $woocommerce->cart->add_to_cart(102, 1);
    } else if ($program == 'aes_psp') {
        $woocommerce->cart->add_to_cart(103, 1);
        $woocommerce->cart->add_to_cart(102, 1);
    }

    $woocommerce->cart->apply_coupon('Registration fee discount');

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

    wp_redirect(wc_get_checkout_url());
    exit;
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

function insert_student($customer_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $periods = ['A', 'B', 'C', 'D', 'E'];

    // Obtener la fecha actual en formato MySQL
    $current_time = current_time('mysql');
    $valid_period = false;
    $code = null;

    // Iterar sobre los periodos
    foreach ($periods as $period) {
        // Preparar la consulta SQL
        $query = $wpdb->prepare(
            "SELECT * FROM {$table_academic_periods} WHERE start_date_{$period} <= %s AND end_date_{$period} >= %s",
            array($current_time, $current_time)
        );

        // Ejecutar la consulta y obtener el resultado
        $period_data = $wpdb->get_row($query);

        // Verificar si se encontró un resultado
        if ($period_data) {
            $valid_period = true;
            $code = $period_data->code;
            break;
        }
    }

    if (!$valid_period) {
        // Iterar sobre los periodos
        foreach ($periods as $period) {
            // Preparar la consulta SQL
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_academic_periods} WHERE start_date_{$period} >= %s AND end_date_{$period} >= %s",
                array($current_time, $current_time)
            );

            // Ejecutar la consulta y obtener el resultado
            $period_data = $wpdb->get_row($query);

            // Verificar si se encontró un resultado
            if ($period_data) {
                $valid_period = true;
                $code = $period_data->code;
                break;
            }
        }
    }

    $wpdb->insert($table_students, [
        'name' => $_COOKIE['name_student'],
        'type_document' => $_COOKIE['document_type'],
        'id_document' => $_COOKIE['id_document'],
        'academic_period' => $code ? $code : AES_PERIOD,
        'middle_name' => $_COOKIE['middle_name_student'],
        'last_name' => $_COOKIE['last_name_student'],
        'middle_last_name' => $_COOKIE['middle_last_name_student'],
        'birth_date' => date_i18n('Y-m-d', strtotime($_COOKIE['birth_date'])),
        'grade_id' => $_COOKIE['initial_grade'],
        'name_institute' => $_COOKIE['name_institute'],
        'institute_id' => $_COOKIE['institute_id'],
        'postal_code' => $_POST['billing_postcode'],
        'gender' => $_COOKIE['gender'],
        'program_id' => $_COOKIE['program_id'],
        'partner_id' => $customer_id,
        'phone' => $_COOKIE['phone_student'],
        'email' => $_COOKIE['email_student'],
        'status_id' => 0,
        'country' => $_POST['billing_country'],
        'city' => $_POST['billing_city'],
        'ethnicity' => $_COOKIE['ethnicity'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $student_id = $wpdb->insert_id;
    return $student_id;
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

function insert_register_documents($student_id, $grade_id)
{

    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_documents = $wpdb->prefix . 'documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id={$grade_id}");

    if ($documents) {

        foreach ($documents as $document) {

            $wpdb->insert($table_student_documents, [
                'student_id' => $student_id,
                'document_id' => $document->name,
                'is_required' => $document->is_required,
                'is_visible' => $document->is_visible,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}

function insert_period_inscriptions($student_id)
{
    global $wpdb;

    // Definir las tablas y columnas
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $periods = ['A', 'B', 'C', 'D', 'E'];

    // Obtener la fecha actual en formato MySQL
    $current_time = current_time('mysql');
    $valid_period = false;

    // Iterar sobre los periodos
    foreach ($periods as $period) {
        // Preparar la consulta SQL
        $query = $wpdb->prepare(
            "SELECT * FROM {$table_academic_periods} WHERE start_date_{$period} <= %s AND end_date_{$period} >= %s",
            array($current_time, $current_time)
        );

        // Ejecutar la consulta y obtener el resultado
        $period_data = $wpdb->get_row($query);

        // Verificar si se encontró un resultado
        if ($period_data) {
            $valid_period = true;

            // Insertar el registro en la tabla de inscripciones
            $wpdb->insert($table_student_period_inscriptions, [
                'student_id' => $student_id,
                'code_period' => $period_data->code,
                'cut_period' => $period,
                'status_id' => 1,
            ]);
            break;
        }
    }

    if (!$valid_period) {
        // Iterar sobre los periodos
        foreach ($periods as $period) {
            // Preparar la consulta SQL
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_academic_periods} WHERE start_date_{$period} >= %s AND end_date_{$period} >= %s",
                array($current_time, $current_time)
            );

            // Ejecutar la consulta y obtener el resultado
            $period_data = $wpdb->get_row($query);

            // Verificar si se encontró un resultado
            if ($period_data) {
                $valid_period = true;

                // Insertar el registro en la tabla de inscripciones
                $wpdb->insert($table_student_period_inscriptions, [
                    'student_id' => $student_id,
                    'code_period' => $period_data->code,
                    'cut_period' => $period,
                    'status_id' => 1,
                ]);
                break;
            }
        }
    }
}

function get_documents($student_id)
{

    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
    return $documents;
}

function get_name_grade($grade_id)
{

    $grade = match ($grade_id) {
        '1' => __('Lower', 'aes'),
        '2' => __('Middle', 'aes'),
        '3' => __('Upper', 'aes'),
        '4' => __('Graduate', 'aes'),
        default => ''
    };

    return $grade;
}

function get_name_program($program_id)
{

    $program = match ($program_id) {
        'aes' => __('AES (Dual Diploma)', 'aes'),
        default => "",
    };

    return $program;
}

function get_gender($gender_id)
{

    $gender = match ($gender_id) {
        'male' => __('Male', 'aes'),
        'female' => __('Female', 'aes'),
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


            wc_add_notice(__('information changed successfully.', 'aes'), 'success');
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
            wc_add_notice(__('information changed successfully.', 'aes'), 'success');
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

function view_access_classroom()
{

    global $current_user, $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $roles = $current_user->roles;

    if (!in_array('student', $roles)) {
        return;
    }

    $student_id = get_user_meta($current_user->ID, 'student_id', true);

    if (!$student_id) {
        $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id={$current_user->ID}");
    } else {
        $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    }

    if (!$data->moodle_student_id) {
        return;
    }

    include(plugin_dir_path(__FILE__) . 'templates/student-access-classroom.php');
}

add_action('woocommerce_account_dashboard', 'view_access_classroom');