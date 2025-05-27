<?php

function save_student()
{
    if (
        isset($_GET['action']) && !empty($_GET['action']) && ($_GET['action'] == 'save_student' || $_GET['action'] == 'new_applicant_others' || $_GET['action'] == 'new_applicant_me' || $_GET['action'] == 'save_student_custom' || $_GET['action'] == 'save_student_info' || $_GET['action'] == 'save_student_scholarship')
    ) {

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
        $grade = isset($_POST['grade']) ? $_POST['grade'] : null;
        $institute_id = isset($_POST['institute_id']) ? $_POST['institute_id'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;
        $from_webinar = isset($_POST['from_webinar']) ? true : false;
        $one_time_payment = isset($_POST['one_time_payment']) ? true : false;
        $is_scholarship = isset($_POST['is_scholarship']) ? true : false;
        $crm_id = isset($_POST['crm_id']) ? $_POST['crm_id'] : false;

        if (!$crm_id) {
            if (get_option('crm_token') && get_option('crm_url') && $email_partner) {
                $crm_exist = crm_request('contacts', '?email=' . $email_partner, 'GET', null);
                if (isset($crm_exist['items']) && count($crm_exist['items']) > 0) {
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

                redirect_to_checkout($program, $grade, $from_webinar, $is_scholarship ? $id_document : false);
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

                redirect_to_checkout($program, $grade, $from_webinar, $is_scholarship);
                // wp_redirect(home_url('/select-payment'));
                break;

            default:

                global $current_user;
                setcookie('agent_name', ucwords(get_user_meta(get_current_user_id(), 'first_name', true)), time() + 864000, '/');
                setcookie('agent_last_name', ucwords(get_user_meta(get_current_user_id(), 'last_name', true)), time() + 864000, '/');
                setcookie('email_partner', $current_user->user_email, time() + 864000, '/');
                setcookie('number_partner', get_user_meta(get_current_user_id(), 'billing_phone', true), time() + 864000, '/');
                setcookie('birth_date_parent', get_user_meta(get_current_user_id(), 'birth_date', true), time() + 864000, '/');
                setcookie('parent_document_type', get_user_meta(get_current_user_id(), 'type_document', true), time() + 864000, '/');
                setcookie('id_document_parent', get_user_meta(get_current_user_id(), 'id_document', true), time() + 864000, '/');
                setcookie('gender_parent', get_user_meta(get_current_user_id(), 'gender_parent', true), time() + 864000, '/');

                redirect_to_checkout($program, $grade, $from_webinar, $is_scholarship);
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
        redirect_to_checkout($_COOKIE['program_id'], $_COOKIE['initial_grade'], false, false);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'pay_graduation_fee') {

        $student = get_student_detail($_GET['student_id']);
        setcookie('fee_student_id', $student->id, time() + 864000, '/');
        setcookie('institute_id', $student->institute_id, time() + 864000, '/');

        // Vaciar carrito existente
        WC()->cart->empty_cart();

        // Agregar nuevo producto
        WC()->cart->add_to_cart(FEE_GRADUATION, 1);

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

function redirect_to_checkout($program, $grade, $from_webinar = false, $is_scholarship = false, $return_url = false)
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();

    if ($program == 'aes') {
        switch ($grade) {
            case '1':
                $variation = wc_get_product(DUAL_9NO_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(DUAL_9NO, 1, DUAL_9NO_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(FEE_INSCRIPTION, 1);
                break;

            case '2':
                $variation = wc_get_product(DUAL_10MO_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(DUAL_10MO, 1, DUAL_10MO_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(FEE_INSCRIPTION, 1);
                break;

            default:
                $variation = wc_get_product(DUAL_DEFAULT_VARIABLE);
                $metadata = $variation->get_meta_data();
                $woocommerce->cart->add_to_cart(DUAL_DEFAULT, 1, DUAL_DEFAULT_VARIABLE, $metadata);
                $woocommerce->cart->add_to_cart(FEE_INSCRIPTION, 1);
                break;
        }

    }

    if (!$from_webinar && !$is_scholarship) {

        // Obtener la fecha máxima desde las opciones
        $max_date_timestamp = get_option('max_date_offer');

        // Verificar si el cupón está vigente
        if (!empty(get_option('offer_complete')) && $max_date_timestamp >= current_time('timestamp')) {
            // Aplicar cupón si NO ha expirado
            $woocommerce->cart->apply_coupon(get_option('offer_complete'));
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
        $woocommerce->cart->apply_coupon('100% Registration fee');
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
    $tickets = $wpdb->get_results("SELECT * FROM {$table_tickets_created}  WHERE user_id = {$current_user->ID}");

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
});

add_action('woocommerce_account_teacher-califications_endpoint', function () {
    global $wpdb;

    $current_user_id = get_current_user_id();
    if (!$current_user_id) {
        wc_print_notice(__('You must be logged in to view this page.', 'edusystem'), 'error');
        return;
    }

    $teacher = get_teacher_details_by_user_id($current_user_id);
    if (is_wp_error($teacher)) {
        wc_print_notice($teacher->get_error_message(), 'error');
        return;
    }
    if (!$teacher) {
        wc_print_notice(__('Teacher profile not found.', 'edusystem'), 'error');
        return;
    }

    $admin_virtual_access = true;
    $load = load_current_cut();
    $code = $load['code'];
    $cut = $load['cut'];
    $history = get_teacher_offers($teacher->id, $code, $cut, 'history');
    $current = get_teacher_offers($teacher->id, $code, $cut, 'current');

    $subject_ids = array_unique(array_column($history, 'subject_id'));
    $subjects = get_subjects_details_multiple($subject_ids);

    $subjects_map = [];
    if (!is_wp_error($subjects) && !empty($subjects)) {
        foreach ($subjects as $subject) {
            $subjects_map[$subject->id] = $subject;
        }
    }

    foreach ($history as $key => $offer) {
        // Assign subject details and code_subject for the query
        $subject_name = __('N/A', 'edusystem');
        $subject_code = __('N/A', 'edusystem');

        if (isset($subjects_map[$offer->subject_id])) {
            $subject = $subjects_map[$offer->subject_id];
            $subject_name = $subject->name;
            $subject_code = $subject->code_subject;
        }

        $history[$key]->subject = $subject_name;
        $history[$key]->code_subject = $subject_code;

        // Get average calification directly from the database
        $average_calification_data = get_average_calification_for_subject_period(
            $offer->subject_id,
            $subject_code, // Pass the subject code here
            $offer->code_period,
            $offer->cut_period
        );

        $history[$key]->prom_calification = $average_calification_data ? (float) $average_calification_data->average_calification : 0;
    }

    foreach ($current as $key => $offer) {
        // Assign subject details and code_subject for the query
        $subject_name = __('N/A', 'edusystem');
        $subject_code = __('N/A', 'edusystem');

        if (isset($subjects_map[$offer->subject_id])) {
            $subject = $subjects_map[$offer->subject_id];
            $subject_name = $subject->name;
            $subject_code = $subject->code_subject;
        }

        $history[$key]->subject = $subject_name;
        $history[$key]->code_subject = $subject_code;
    }

    include(plugin_dir_path(__FILE__) . 'templates/teacher-califications.php');
});

function get_average_calification_for_subject_period($subject_id, $code_subject, $code_period, $cut_period)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    // At least one of subject_id or code_subject must be provided
    if (empty($subject_id) && empty($code_subject)) {
        return new WP_Error('missing_subject_identifier', __('Either subject ID or subject code must be provided.', 'edusystem'));
    }

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
    // Modificación aquí: Usar ROUND(AVG(calification), 2) para limitar a 2 decimales
    $query = "SELECT ROUND(AVG(calification), 2) as average_calification
              FROM {$table_student_period_inscriptions}
              WHERE ({$subject_condition})
              AND code_period = %s
              AND cut_period = %s
              AND `status_id` = 3"; // Assuming 'status' is an integer

    $prepare_args[] = $code_period;
    $prepare_args[] = $cut_period;

    $avg_calification = $wpdb->get_row($wpdb->prepare(
        $query,
        ...$prepare_args // Use the spread operator to pass all arguments
    ));

    // El resultado ya vendrá formateado con 2 decimales desde la base de datos
    return $avg_calification;
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

function insert_student($customer_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $birth_date = date_i18n('Y-m-d', strtotime($_COOKIE['birth_date']));
    $today = new DateTime();
    $age = $today->diff(new DateTime($birth_date))->y;

    $exist = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email = '{$_COOKIE['email_student']}'");
    if (!$exist) {
        $wpdb->insert($table_students, [
            'name' => $_COOKIE['name_student'],
            'type_document' => $_COOKIE['document_type'],
            'id_document' => $_COOKIE['id_document'],
            'academic_period' => $code,
            'initial_cut' => $cut,
            'middle_name' => $_COOKIE['middle_name_student'],
            'last_name' => $_COOKIE['last_name_student'],
            'middle_last_name' => $_COOKIE['middle_last_name_student'],
            'birth_date' => date_i18n('Y-m-d', strtotime($_COOKIE['birth_date'])),
            'grade_id' => $_COOKIE['initial_grade'],
            'name_institute' => strtoupper($_COOKIE['name_institute']),
            'institute_id' => $_COOKIE['institute_id'],
            'postal_code' => $_POST['billing_postcode'],
            'gender' => $_COOKIE['gender'],
            'program_id' => $_COOKIE['program_id'],
            'partner_id' => $customer_id,
            'phone' => $_COOKIE['phone_student'],
            'email' => $_COOKIE['email_student'],
            'status_id' => 0,
            'set_password' => ($age >= 18 ? 1 : 0),
            'country' => $_POST['billing_country'],
            'city' => $_POST['billing_city'],
            'ethnicity' => $_COOKIE['ethnicity'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

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

function update_elective_student($student_id, $status_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $wpdb->update($table_students, [
        'elective' => $status_id
    ], ['id' => $student_id]);
}

function insert_register_documents($student_id, $grade_id)
{
    global $wpdb;

    $student = get_student_detail($student_id);
    $birthDate = new DateTime($student->birth_date);
    $today = new DateTime();
    $legal_age = ($today->diff($birthDate)->y >= 18);

    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_documents = $wpdb->prefix . 'documents';

    // Query segura con prepared statement
    $documents = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$table_documents} WHERE grade_id = %d", $grade_id)
    );

    if (!$documents)
        return;

    foreach ($documents as $document) {
        // Verificar existencia usando el ID del documento
        $exist = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 1 FROM {$table_student_documents} 
                WHERE student_id = %d AND document_id = %s",
                $student_id,
                $document->name // Asumiendo que hay un campo id único
            )
        );

        if ($exist)
            continue;

        // Lógica de is_required mejorada
        $isRequired = 0;
        $isVisible = $document->is_visible;
        if ($document->is_required) {
            $isRequired = ($document->name === 'ID OR CI OF THE PARENTS' && $legal_age) ? 0 : 1;
            $isVisible = ($document->name === 'ID OR CI OF THE PARENTS' && $legal_age) ? 0 : 1;
        }

        // Inserción segura
        $wpdb->insert($table_student_documents, [
            'student_id' => $student_id,
            'document_id' => $document->name,
            'is_required' => $isRequired,
            'is_visible' => $isVisible,
            'status' => 0,
            'created_at' => current_time('mysql')
        ]);
    }
}

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
        $products = ['FEE_INSCRIPTION', 'FEE_GRADUATION'];
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

function get_name_program($program_id)
{

    $program = match ($program_id) {
        'aes' => __('AES (Dual Diploma)', 'edusystem'),
        default => "",
    };

    return $program;
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
    $table_students = $wpdb->prefix . 'students';
    $roles = $current_user->roles;
    $url = URL_LARAVEL_PPADMIN;
    $student_access = false;
    $error_access = false;

    $student_id = get_user_meta($current_user->ID, 'student_id', true);
    if (!$student_id) {
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id={$current_user->ID}");
    } else {
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    }

    if (in_array('student', $roles)) {
        $access = is_enrolled_in_courses($student->id);
        $student_access = true;
    }

    if (!$student->moodle_student_id) {
        $student_access = false;
    }

    if ($student->moodle_student_id && $student->status_id < 2) {
        $student_access = false;
        $error_access = 'Some of your documents required for classroom access have been declined, please check the documents area for more information.';
    }

    $today = date('Y-m-d');
    if ($student->max_access_date && $student->max_access_date < $today) {
        $student_access = false;
        $error_access = 'Classroom access has been removed because you have overdue payments. Please pay the outstanding fees in order to continue to have access to the classroom.';
    }

    $expired_documents = expired_documents($student->id);
    if ($expired_documents) {
        $student_access = false;
        $error_access = 'The deadline for uploading some documents has expired, removing your access to the virtual classroom. We invite you to access your documents area for more information.';
    }

    $show_table_subjects_coursing = get_option('show_table_subjects_coursing');
    $projection = get_projection_by_student($student->id);
    if ($projection && $show_table_subjects_coursing) {
        $projection_obj = json_decode($projection->projection);
        $subjects_coursing = array_filter($projection_obj, function ($item) {
            return $item->this_cut == true;
        });
        $subjects_coursing = array_values($subjects_coursing);
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

function set_max_date_student($student_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $next_payment = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE status_id = 0 AND student_id = {$student_id} AND date_payment IS NULL ORDER BY cuote ASC");
    if ($next_payment) {
        $date = $next_payment->date_next_payment;
        $days = (int) get_option('payment_due');
        $max_date = date('Y-m-d', strtotime("$date + $days days"));
        $wpdb->update($table_students, [
            'max_access_date' => $max_date
        ], ['id' => $student_id]);
    } else {
        $wpdb->update($table_students, [
            'max_access_date' => NULL
        ], ['id' => $student_id]);
    }
}

function helper_get_student_logged()
{
    global $wpdb, $current_user;
    $table_students = $wpdb->prefix . 'students';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
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