<?php

require plugin_dir_path(__FILE__) . 'student-scholarship.php';
require plugin_dir_path(__FILE__) . 'student.php';
require plugin_dir_path(__FILE__) . 'account.php';
require plugin_dir_path(__FILE__) . 'institute.php';
require plugin_dir_path(__FILE__) . 'document.php';
require plugin_dir_path(__FILE__) . 'alliances.php';
require plugin_dir_path(__FILE__) . 'user.php';
require plugin_dir_path(__FILE__) . 'notes.php';
require plugin_dir_path(__FILE__) . 'academic_services.php';
require plugin_dir_path(__FILE__) . 'endpoint.php';
require plugin_dir_path(__FILE__) . 'moodle.php';
require plugin_dir_path(__FILE__) . 'automatically_enrollment.php';
require plugin_dir_path(__FILE__) . 'escala/rest.php';
require plugin_dir_path(__FILE__) . 'document-request.php';

function form_plugin_scripts()
{
    global $wp;
    $version = VERSIONS_JS;
    wp_enqueue_style('dashicons');
    wp_enqueue_style('admin-flatpickr', plugins_url('edusystem') . '/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('intel-css', plugins_url('edusystem') . '/public/assets/css/intlTelInput.css');
    wp_enqueue_style('style-public', plugins_url('edusystem') . '/public/assets/css/style.css', array(), $version, 'all');
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-flatpickr', plugins_url('edusystem') . '/public/assets/js/flatpickr.js');
    wp_enqueue_script('masker-js', plugins_url('edusystem') . '/public/assets/js/vanilla-masker.min.js');
    wp_enqueue_script('intel-js', plugins_url('edusystem') . '/public/assets/js/intlTelInput.min.js');
    wp_enqueue_script('form-register', plugins_url('edusystem') . '/public/assets/js/form-register.js');
    wp_enqueue_script('int-tel', plugins_url('edusystem') . '/public/assets/js/int-tel.js');

    // PAYMENTS PARTS
    wp_register_script('payment-parts-update', plugins_url('edusystem') . '/public/assets/js/payment-parts-update.js', array('jquery'), $version, true);
    wp_localize_script(
        'payment-parts-update',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('payment-parts-update');

    // form-register
    wp_register_script('form-register', plugins_url('edusystem') . '/public/assets/js/form-register.js', array('jquery'), $version, true);
    wp_localize_script(
        'form-register',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('form-register');

    wp_register_script('create-password', plugins_url('edusystem') . '/public/assets/js/create-password.js', array('jquery'), $version, true);
    wp_localize_script(
        'create-password',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('create-password');

    wp_register_script('create-enrollment', plugins_url('edusystem') . '/public/assets/js/create-enrollment.js', array('jquery'), $version, true);
    wp_localize_script(
        'create-enrollment',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('create-enrollment');

    if (str_contains(home_url($wp->request), 'califications')) {
        wp_register_script('califications', plugins_url('edusystem') . '/public/assets/js/califications.js', array('jquery'), $version, true);
        wp_localize_script(
            'califications',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );
        wp_enqueue_script('califications');
    }

    if (str_contains(home_url($wp->request), 'student-documents')) {
        wp_enqueue_script('document', plugins_url('edusystem') . '/public/assets/js/document.js', array('jquery'), $version, true);
        wp_localize_script('document', 'save_documents', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'save_documents'
        ]);
    }

    if (str_contains(home_url($wp->request), 'teacher-documents')) {
        wp_enqueue_script('document', plugins_url('edusystem') . '/public/assets/js/teacher-document.js', array('jquery'), $version, true);
        wp_localize_script('document', 'save_documents_teacher', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'save_documents_teacher'
        ]);
    }

    if (str_contains(home_url($wp->request), 'orders')) {
        wp_enqueue_script('next-payment', plugins_url('edusystem') . '/public/assets/js/next-payment.js', array('jquery'), $version, true);
        wp_localize_script('next-payment', 'ajax_object', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }

    if (str_contains(home_url($wp->request), 'my-requests')) {
        wp_register_script('requests', plugins_url('edusystem') . '/public/assets/js/requests.js', array('jquery'), $version, true);
        wp_localize_script(
            'requests',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );
        wp_enqueue_script('requests');
    }

    wp_register_script('student-continue', plugins_url('edusystem') . '/public/assets/js/student-continue.js', array('jquery'), $version, true);
    wp_localize_script(
        'student-continue',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('student-continue');

    wp_register_script('previous-form', plugins_url('edusystem') . '/public/assets/js/previous-form.js', array('jquery'), $version, true);
    wp_localize_script(
        'previous-form',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('previous-form');

    wp_register_script('functions-theme', plugins_url('edusystem') . '/public/assets/js/functions-theme.js', array('jquery'), $version, true);
    wp_localize_script(
        'functions-theme',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('functions-theme');
}

add_action('wp_enqueue_scripts', 'form_plugin_scripts');

function removed_hooks()
{
    remove_action('storefront_footer', 'storefront_credit', 20);
    remove_action('storefront_header', 'storefront_header_cart', 60);
}

add_action('init', 'removed_hooks');

function form_asp_psp($atts)
{
    // Define los atributos por defecto
    $atts = shortcode_atts(
        array(
            'connected_account' => '',
            'coupon_code' => '',
            'flywire_portal_code' => 'FGY',
            'manager_user_id' => '',
            'zelle_account' => '',
            'bank_transfer_account' => '',
            'register_psp' => false,
            'hidden_payment_methods' => '',
            'fixed_fee_inscription' => false,
            'styles_shortcode' => 'margin-top: 30px !important; background: rgb(223 223 223); color: black',
            'max_age' => 18,
            'limit_age' => 21,
            'program' => '',
            'career' => '',
            'mention' => '',
            'plan' => ''
        ),
        $atts,
        'form_asp_psp'
    );

    $connected_account = $atts['connected_account'];
    $coupon_code = $atts['coupon_code'];
    $flywire_portal_code = $atts['flywire_portal_code'];
    $manager_user_id = $atts['manager_user_id'];
    $zelle_account = $atts['zelle_account'];
    $register_psp = $atts['register_psp'];
    $bank_transfer_account = $atts['bank_transfer_account'];
    $hidden_payment_methods = $atts['hidden_payment_methods'];
    $styles_shortcode = $atts['styles_shortcode'];
    $fixed_fee_inscription = $atts['fixed_fee_inscription'];
    $max_age = $atts['max_age'];
    $limit_age = $atts['limit_age'];
    $program = $atts['program'];
    $career = $atts['career'];
    $mention = $atts['mention'];
    $plan = $atts['plan'];

    $countries = get_countries();
    $institutes = get_list_institutes_active($manager_user_id);
    $grades = get_grades();
    $programs = get_student_program();
    add_action('wp_footer', 'modal_continue_checkout');
    include(plugin_dir_path(__FILE__) . 'templates/asp-psp-registration.php');
}

add_shortcode('form_asp_psp', 'form_asp_psp');

function student_registration_form($atts)
{
    // Define los atributos por defecto
    $atts = shortcode_atts(
        array(
            'connected_account' => '',
            'coupon_code' => '',
            'flywire_portal_code' => 'FGY',
            'manager_user_id' => '',
            'zelle_account' => '',
            'bank_transfer_account' => '',
            'register_psp' => false,
            'hidden_payment_methods' => '',
            'fixed_fee_inscription' => false,
            'styles_shortcode' => 'margin-top: 30px !important; background: rgb(223 223 223); color: black',
            'max_age' => 18,
            'limit_age' => 21,
            'program' => '',
            'career' => '',
            'mention' => '',
            'plan' => ''
        ),
        $atts,
        'student_registration_form'
    );

    $connected_account = $atts['connected_account'];
    $coupon_code = $atts['coupon_code'];
    $flywire_portal_code = $atts['flywire_portal_code'];
    $manager_user_id = $atts['manager_user_id'];
    $zelle_account = $atts['zelle_account'];
    $register_psp = $atts['register_psp'];
    $bank_transfer_account = $atts['bank_transfer_account'];
    $hidden_payment_methods = $atts['hidden_payment_methods'];
    $styles_shortcode = $atts['styles_shortcode'];
    $fixed_fee_inscription = $atts['fixed_fee_inscription'];
    $max_age = $atts['max_age'];
    $limit_age = $atts['limit_age'];
    $program = $atts['program'];
    $career = $atts['career'];
    $mention = $atts['mention'];
    $plan = $atts['plan'];
    
    $countries = get_countries();
    $institutes = get_list_institutes_active($manager_user_id);
    $grades = get_grades();
    $programs = get_student_program();
    $careers = [];
    $mentions = [];

    add_action('wp_footer', 'modal_continue_checkout');
    include(plugin_dir_path(__FILE__) . 'templates/student-registration-form-structure.php');
}

add_shortcode('student_registration_form', 'student_registration_form');


add_filter('woocommerce_available_payment_gateways', 'hide_payment_gateways_from_cookie');

function hide_payment_gateways_from_cookie($available_gateways)
{
    // Verificar si la cookie existe
    if (!isset($_COOKIE['student_registration_hidden_payments']) || empty($_COOKIE['student_registration_hidden_payments'])) {
        return $available_gateways;
    }

    // Obtener los IDs de la cookie y convertirlos a un array
    $hidden_payment_methods_str = sanitize_text_field($_COOKIE['student_registration_hidden_payments']);
    $hidden_payment_methods = array_map('trim', explode(',', $hidden_payment_methods_str));

    foreach ($available_gateways as $gateway_id => $gateway) {
        if (in_array($gateway_id, $hidden_payment_methods)) {
            unset($available_gateways[$gateway_id]);
        }
    }
    return $available_gateways;
}

function modal_continue_checkout()
{
    include(plugin_dir_path(__FILE__) . 'templates/modal-continue-checkout.php');
}

add_action('wp_ajax_use_previous_form_aes', 'use_previous_form_aes_callback');
add_action('wp_ajax_nopriv_use_previous_form_aes', 'use_previous_form_aes_callback');

function use_previous_form_aes_callback()
{
    $use_previous_form = $_POST['use'];
    if ($use_previous_form == 1) {
        wp_send_json_success(array('redirect' => 'close'));
        exit;
    } else {
        clear_all_cookies();
        wp_send_json_success(array('success' => true));
        exit;
    }
}

add_action('wp_ajax_load_grades_institute', 'load_grades_institute_callback');
add_action('wp_ajax_nopriv_load_grades_institute', 'load_grades_institute_callback');

function load_grades_institute_callback()
{
    $institute_id = $_POST['institute_id'];
    $institute = get_institute_details($institute_id);
    $default_grades = get_grades(); // Siempre obtenemos las calificaciones por defecto

    $institute_grades = []; // Inicializamos el array de calificaciones del instituto

    // Mapeamos los nombres de los campos del instituto a los IDs de las calificaciones
    $institute_field_map = [
        1 => 'lower_text',
        2 => 'middle_text',
        3 => 'upper_text',
        4 => 'graduated_text',
    ];

    if ($institute) {
        foreach ($default_grades as $grade) {
            if (isset($institute_field_map[$grade->id])) {
                $field_name = $institute_field_map[$grade->id];
                $institute_text = $institute->$field_name;

                // Preparamos las versiones en minúsculas para la comparación
                $default_grade_name_lower = strtolower($grade->name);
                $institute_text_lower = strtolower($institute_text);

                // Verificamos si hay una personalización real:
                // 1. El texto del instituto no está vacío.
                // 2. El texto del instituto (ignorando mayúsculas/minúsculas) es diferente al nombre por defecto.
                if (!empty($institute_text) && $default_grade_name_lower !== $institute_text_lower) {
                    // Si hay una personalización, creamos un nuevo objeto para el instituto
                    // y lo agregamos al array institute_grades.
                    $custom_grade = clone $grade; // Clonamos el objeto default
                    $custom_grade->name = $institute_text; // Aplicamos el nombre personalizado
                    $custom_grade->description = ''; // La descripción se limpia según tu lógica original
                    $institute_grades[] = $custom_grade;
                }
            }
        }
    }

    wp_send_json_success(array(
        'default_grades' => $default_grades,
        'institute_grades' => $institute_grades // Este array contendrá solo las calificaciones personalizadas o estará vacío
    ));
    exit;
}

function one_time_payment()
{
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
    $programs = get_student_program();
    include(plugin_dir_path(__FILE__) . 'templates/one-time-payment-registration.php');
}

add_shortcode('one_time_payment', 'one_time_payment');

function custom_registration_pay()
{
    $grades = get_grades();
    $programs = get_student_program();
    include(plugin_dir_path(__FILE__) . 'templates/custom-registration-pay.php');
}

add_shortcode('custom_registration_pay', 'custom_registration_pay');

function form_scholarship_application()
{
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
    $programs = get_student_program();
    include(plugin_dir_path(__FILE__) . 'templates/scholarship-application.php');
}

add_shortcode('form_scholarship_application', 'form_scholarship_application');

function get_countries()
{
    $wc_countries = new WC_Countries();
    $countries = $wc_countries->get_countries();
    return $countries;
}

function get_states_by_country_code($country_code)
{
    $wc_countries = new WC_Countries();
    $states = $wc_countries->get_states($country_code);
    return $states;
}

add_filter('woocommerce_checkout_fields', 'removed_custom_checkout_fields');

function removed_custom_checkout_fields($fields)
{

    unset($fields['billing']['billing_company']);
    // unset( $fields['billing']['billing_address_1']);
    // unset( $fields['billing']['billing_address_2']);
    // unset( $fields['billing']['billing_state']);
    // unset( $fields['billing']['billing_postcode']);
    // unset($fields['billing']['billing_email']);
    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    unset($fields['shipping']['shipping_address_1']);
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['order']['order_comments']);

    return $fields;
}

function change_default_checkout_country($country)
{

    if (isset($_COOKIE['billing_country']) && !empty($_COOKIE['billing_country'])) {
        $country = $_COOKIE['billing_country'];
    }
    return $country;
}

add_filter('default_checkout_billing_country', 'change_default_checkout_country');

function change_default_checkout_state($state)
{

    if (isset($_COOKIE['billing_state']) && !empty($_COOKIE['billing_state'])) {
        $state = $_COOKIE['billing_state'];
    }
    return $state;
}

add_filter('default_checkout_billing_state', 'change_default_checkout_state');

// function woocommerce_checkout_order_created_action($order)
// {
//     $customer_id = $order->get_customer_id();

//     if (!get_user_meta($customer_id, 'status_register', true)) {
//         update_user_meta($customer_id, 'status_register', 0);
//     }

//     if (
//         isset($_COOKIE['name_student']) && !empty($_COOKIE['name_student']) &&
//         isset($_COOKIE['last_name_student']) && !empty($_COOKIE['last_name_student']) &&
//         isset($_COOKIE['birth_date']) && !empty($_COOKIE['birth_date']) &&
//         isset($_COOKIE['initial_grade']) && !empty($_COOKIE['initial_grade']) &&
//         isset($_COOKIE['program_id']) && !empty($_COOKIE['program_id']) &&
//         isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner']) &&
//         isset($_COOKIE['number_partner']) && !empty($_COOKIE['number_partner'])
//     ) {
//         $student_id = insert_student($customer_id);
//         insert_register_documents($student_id, $_COOKIE['initial_grade']);

//         if (!$order->meta_exists('student_id')) {
//             $order->update_meta_data('student_id', $student_id);
//         }

//         $order->update_meta_data('id_bitrix', $_COOKIE['id_bitrix']);
//         $order->save();

//         $email_new_student = WC()->mailer()->get_emails()['WC_New_Applicant_Email'];
//         $email_new_student->trigger($student_id);

//         insert_data_student($order);
//         if (isset($_COOKIE['is_scholarship']) && !empty($_COOKIE['is_scholarship'])) {
//             save_scholarship();
//         }
//     }

//     if (isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])) {
//         add_role_user($customer_id, 'parent');
//     }

//     if (isset($_COOKIE['id_document_parent']) && !empty($_COOKIE['id_document_parent'])) {
//         update_user_meta($customer_id, 'id_document', $_COOKIE['id_document_parent']);
//     }

//     if (isset($_COOKIE['parent_document_type']) && !empty($_COOKIE['parent_document_type'])) {
//         update_user_meta($customer_id, 'type_document', $_COOKIE['parent_document_type']);
//     }

//     if (isset($_COOKIE['birth_date_parent']) && !empty($_COOKIE['birth_date_parent'])) {
//         update_user_meta($customer_id, 'birth_date', $_COOKIE['birth_date_parent']);
//     }

//     if (isset($_COOKIE['gender_parent']) && !empty($_COOKIE['gender_parent'])) {
//         update_user_meta($customer_id, 'gender', $_COOKIE['gender_parent']);
//     }

//     if (isset($_COOKIE['ethnicity_parent']) && !empty($_COOKIE['ethnicity_parent'])) {
//         update_user_meta($customer_id, 'ethnicity', $_COOKIE['ethnicity_parent']);
//     }

//     if (isset($_COOKIE['password']) && !empty($_COOKIE['password'])) {
//         global $wpdb;

//         $user_data = array(
//             'ID' => $customer_id,
//             'user_pass' => $_COOKIE['password'],
//             'user_pass_reset' => 1
//         );

//         wp_update_user($user_data);
//     }

//     //validate cookie and set metadata
//     if (isset($_COOKIE['fee_student_id']) && !empty($_COOKIE['fee_student_id'])) {
//         if (!$order->meta_exists('student_id')) {
//             $order->update_meta_data('student_id', $_COOKIE['fee_student_id']);
//         }
//         $order->save();
//     }

//     set_institute_in_order($order);
// }

// add_action('woocommerce_checkout_order_created', 'woocommerce_checkout_order_created_action');

add_filter('woocommerce_checkout_fields', 'custom_override_value_checkout_fields');

function custom_override_value_checkout_fields($fields)
{

    if (isset($_COOKIE['agent_name']) && !empty($_COOKIE['agent_name'])) {
        $fields['billing']['billing_first_name']['default'] = $_COOKIE['agent_name'];
    }

    if (isset($_COOKIE['agent_last_name']) && !empty($_COOKIE['agent_last_name'])) {
        $fields['billing']['billing_last_name']['default'] = $_COOKIE['agent_last_name'];
    }

    if (isset($_COOKIE['billing_city']) && !empty($_COOKIE['billing_city'])) {
        $fields['billing']['billing_city']['default'] = $_COOKIE['billing_city'];
    }

    if (isset($_COOKIE['billing_address_1']) && !empty($_COOKIE['billing_address_1'])) {
        $fields['billing']['billing_address_1']['default'] = $_COOKIE['billing_address_1'];
    }

    if (isset($_COOKIE['billing_postcode']) && !empty($_COOKIE['billing_postcode'])) {
        $fields['billing']['billing_postcode']['default'] = $_COOKIE['billing_postcode'];
    }

    if (isset($_COOKIE['billing_country']) && !empty($_COOKIE['billing_country'])) {
        $fields['billing']['billing_country']['default'] = $_COOKIE['billing_country'];
    }

    if (isset($_COOKIE['number_partner']) && !empty($_COOKIE['number_partner'])) {
        $fields['billing']['billing_phone']['default'] = sanitize_text_field($_COOKIE['number_partner']);
        $fields['billing']['billing_phone_hidden']['default'] = sanitize_text_field($_COOKIE['number_partner']);
    }

    if (isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner'])) {
        $fields['billing']['billing_email']['default'] = $_COOKIE['email_partner'];
    }

    if (isset($_COOKIE['billing_state']) && !empty($_COOKIE['billing_state'])) {
        $fields['billing']['billing_state']['default'] = $_COOKIE['billing_state'];
    }

    return $fields;
}

function change_billing_phone_checkout_field_value($order)
{

    if ($_POST['aes_split_payment'] == 'on' && (!$_POST['aes_amount_split'] || $_POST['aes_amount_split'] == 0)) {
        wc_add_notice(__('You must specify a split payment amount', 'woocommerce'), 'error');
        exit; // o exit; si deseas detener la ejecución del código
    }

    $order->add_meta_data('split_payment', ($_POST['aes_split_payment'] == 'on' ? 1 : 0));
    $order->add_meta_data('pending_payment', ($_POST['aes_split_payment'] == 'on' ? ($order->get_subtotal() - $order->get_total_discount()) : 0));

    if (isset($_POST['billing_phone_hidden']) && !empty($_POST['billing_phone_hidden'])) {
        $order->set_billing_phone($_POST['billing_phone_hidden']);
        update_user_meta($order->get_customer_id(), 'billing_phone', $_POST['billing_phone_hidden']);
    }

    if (isset($_COOKIE['from_webinar']) && !empty($_COOKIE['from_webinar'])) {
        $order->add_meta_data('from_webinar', 1);
    }

    if (isset($_COOKIE['one_time_payment']) && !empty($_COOKIE['one_time_payment'])) {
        $order->add_meta_data('one_time_payment', 1);
    }

    if (isset($_COOKIE['is_scholarship']) && !empty($_COOKIE['is_scholarship'])) {
        $order->add_meta_data('is_scholarship', 1);
    }

    if (isset($_COOKIE['crm_id']) && !empty($_COOKIE['crm_id'])) {
        $order->add_meta_data('crm_id', $_COOKIE['crm_id']);
    }

    $order->save();
}

add_action('woocommerce_checkout_create_order', 'change_billing_phone_checkout_field_value', 10);

add_filter('woocommerce_account_menu_items', 'remove_my_account_links');

function remove_my_account_links($menu_links)
{
    global $current_user;
    $roles = $current_user->roles;
    $user_id = $current_user->ID;

    // Definir el orden base del menú
    $menu_links = [
        'dashboard' => __('Dashboard', 'edusystem'),
    ];

    // Eliminar enlaces no deseados
    unset($menu_links['downloads']);
    unset($menu_links['edit-address']);
    unset($menu_links['payment-methods']);
    unset($menu_links['customer-logout']);

    // Agregar "Payments" para el rol "parent"
    if (in_array('parent', $roles)) {
        $menu_links['orders'] = __('Payments', 'edusystem');
    }

    // Agregar "Documents" para el rol "teacher"
    if (in_array('teacher', $roles)) {
        $menu_links['teacher-documents'] = __('Documents', 'edusystem');
        $menu_links['teacher-courses'] = __('My courses', 'edusystem');
    }

    // Lógica para roles "parent" y "student"
    if (in_array('parent', $roles) || in_array('student', $roles)) {

        if (MODE != 'UNI') {
            // Agregar "Student Information"
            $menu_links['student'] = __('Student Information', 'edusystem');

            // Agregar "Califications"
            $menu_links['califications'] = __('Califications', 'edusystem');

            if (in_array('parent', $roles)) {
                if (get_user_meta($user_id, 'status_register', true) == 1) {
                    $menu_links['student-documents'] = __('Documents', 'edusystem');
                }
            }

            if (in_array('student', $roles)) {
                $student_id = get_user_meta($user_id, 'student_id', true);
                $student = get_student_detail($student_id);
                if (get_user_meta($student->partner_id, 'status_register', true) == 1) {
                    $menu_links['student-documents'] = __('Documents', 'edusystem');
                }
            }
        }
    }

    $menu_links['edit-account'] = __('Account', 'woocommerce');

    if (in_array('parent', $roles) || in_array('student', $roles)) {
        $menu_links['my-tickets'] = __('Support Tickets', 'edusystem');

        if (MODE != 'UNI') {
            $menu_links['my-requests'] = __('Requests', 'edusystem');
        }

    }

    return $menu_links;
}

add_action('init', function () {
    add_rewrite_endpoint('student-documents', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('teacher-documents', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student-details', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('student', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('my-tickets', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('my-requests', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('califications', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('teacher-courses', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('teacher-course-students', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('notes', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('academic-services', EP_ROOT | EP_PAGES);
});

function redirect_to_my_account()
{

    global $current_user;
    $roles = $current_user->roles;

    if (in_array('parent', $roles) || in_array('student', $roles)) {
        wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')) . '/orders');
        exit();
    }
}

add_action('woocommerce_thankyou', 'redirect_to_my_account', 10, 1);
add_action('woocommerce_thankyou', 'auto_complete_free_orders', 9, 1);

function auto_complete_free_orders($order_id)
{
    // Obtener el objeto de la orden
    $order = wc_get_order($order_id);

    // Verificar si es una orden válida y con total 0
    if ($order && $order->get_total() == 0) {

        // Verificar que no esté ya completada
        if (!$order->has_status('completed')) {

            // Actualizar estado y agregar nota
            $order->update_status('completed', __('Orden marcada como completada automáticamente por monto cero', 'your-textdomain'));

            // Opcional: Limpiar carrito si es necesario
            if (WC()->cart) {
                WC()->cart->empty_cart();
            }
        }
    }
}

function create_ticket($email, $ticket_id, $subject, $message)
{
    if (is_user_logged_in()) {
        global $current_user, $wpdb;
        $table_tickets_created = $wpdb->prefix . 'tickets_created';
        $wpdb->insert($table_tickets_created, [
            'user_id' => $current_user->ID,
            'ticket_id' => $ticket_id,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);
    }
}

add_action('ticket_created', 'create_ticket', 10, 4);

function modify_columns_orders($columns = [])
{
    $columns['order-number'] = __('Payment ID', 'edusystem');
    return $columns;
}

add_filter('woocommerce_account_orders_columns', 'modify_columns_orders');

add_filter('wp_nav_menu_items', 'add_loginout_link', 10, 2);

function add_loginout_link($items, $args)
{

    if (is_user_logged_in()) {

        global $current_user, $wpdb;
        $table_users_notices = $wpdb->prefix . 'users_notices';
        $notices = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_users_notices} WHERE `read` = %d AND user_id = %d ORDER BY created_at DESC", 0, $current_user->ID));
        if (sizeof($notices) > 0) {
            $color = '#12e354 !important';
            $count = "(" . sizeof($notices) . ")";
        } else {
            $color = 'var(--primary-color)';
            $count = "";
        }
        if ($args->theme_location == 'primary') {
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/notifications"><span style="vertical-align: baseline; font-size: 28px; width: 26px; color: ' . $color . '; cursor: pointer;" class="dashicons dashicons-bell"></span>' . $count . '</a></li>';
        }


        $birthday = get_user_meta($current_user->ID, 'birth_date', true);
        $age = floor((time() - strtotime($birthday)) / 31556926);
        if ($age >= 18) {
            $items .= '<li><a href="' . home_url() . '">' . __('New applicant', 'edusystem') . '</a></li>';
        }

        $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '">' . __('Dashboard', 'edusystem') . '</a></li>';


        if ($args->theme_location != 'primary') {
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/notifications">' . __('Notifications', 'edusystem') . '</a></li>';
        }


        if ($args->theme_location != 'primary') {
            if ($age >= 18) {
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/orders">' . __('Payments', 'edusystem') . '</a></li>';
            }

            if (MODE != 'UNI') {
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student">' . __('Student information', 'edusystem') . '</a></li>';
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/califications">' . __('Califications', 'edusystem') . '</a></li>';
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents">' . __('Documents', 'edusystem') . '</a></li>';
            }

            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/edit-account">' . __('Account', 'edusystem') . '</a></li>';

            if (MODE != 'UNI') {
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/my-tickets">' . __('Suppor tickets', 'edusystem') . '</a></li>';
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/my-requests">' . __('Requests', 'edusystem') . '</a></li>';
            }
        }

        $logout_link = wp_logout_url(get_home_url());
        $items .= '<li>
            <div class="logout-button-container" >
                <div style="padding: 5px 20px !important; text-align: start; border-radius: 20px; color: white !important; font-size: 14px;">
                ' . $current_user->first_name . ' ' . $current_user->last_name . ' 
                </div>
                <div>
                    <a class="button-primary" style="font-size:14px;" href="' . $logout_link . '">' . __('Log out', 'edusystem') . '</a>
                </div>
            </div>
        </li>';
    } elseif (!is_user_logged_in()) {
        $items .= '<li style="margin-bottom: 17px; !important"><a class="button-primary" style="font-size:14px;" href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '">' . __('Log in', 'edusystem') . '</a></li>';
    }

    return $items;
}

function status_changed_payment($order_id, $status_transition_from, $current_status, $that)
{
    $order = wc_get_order($order_id);
    $customer_id = $order->get_customer_id();
    $status_register = get_user_meta($customer_id, 'status_register', true);

    // Limpiar cookies solo para estados válidos
    // if (!in_array($current_status, ['failed', 'pending'])) {
    //     clear_all_cookies();
    // }

    if (in_array($current_status, ['on-hold'])) {
        clear_all_cookies(); // borrar cookies cuando el pago esta on hold
        send_notification_staff_particular('New payment received for approval', 'There is a new payment waiting for approval, please login to the platform as soon as possible.', 3);
    }

    // Determinar qué función ejecutar
    if ($current_status === 'completed') {
        status_order_completed($order, $order_id, $customer_id);
        clear_all_cookies(); // se completa la orden, borramos todo
    } elseif (!in_array($current_status, ['failed', 'pending'])) {
        status_order_not_completed($order, $order_id, $customer_id, $status_register);
    }
}

add_action('woocommerce_order_status_changed', 'status_changed_payment', 11, 4);

/**
 * Orquesta las acciones cuando una orden se marca como "completada".
 * Optimizado para mayor claridad y eficiencia.
 *
 * @param WC_Order $order Objeto de la orden de WooCommerce.
 * @param int      $order_id ID de la orden.
 * @param int      $customer_id ID del cliente.
 */
function status_order_completed($order, $order_id, $customer_id)
{
    // 1. Actualiza los metadatos del usuario de forma directa.
    update_user_meta($customer_id, 'status_register', 1);
    update_user_meta($customer_id, 'cuote_pending', 0);

    $student_id = $order->get_meta('student_id');

    // 2. Salir temprano si no hay un ID de estudiante asociado.
    if (empty($student_id)) {
        return;
    }

    // Asegura que el usuario de WordPress para el estudiante exista.
    create_user_student($student_id);

    // 3. Itera sobre los artículos para actualizar o crear registros de pago.
    foreach ($order->get_items() as $item) {
        update_or_create_payment_record($item, $student_id, $order_id);
    }

    // 4. Agrupa las llamadas a sistemas externos para mayor claridad.
    handle_crm_updates($order);
    update_student_status_and_notify($student_id);
    set_max_date_student($student_id);
}

/**
 * Actualiza o crea un registro de pago para un artículo dado en una orden.
 *
 * Esta función intenta primero actualizar el pago pendiente más antiguo para un estudiante
 * y producto específico. Si no se encuentra un pago pendiente, crea un nuevo registro
 * con la información del pago completado, incluyendo los detalles de instituto y alianzas.
 *
 * @param WC_Order_Item_Product $item       Objeto del ítem de la orden.
 * @param int                   $student_id ID del estudiante asociado al pago.
 * @param int                   $order_id   ID de la orden de WooCommerce.
 * @return void
 */
function update_or_create_payment_record(WC_Order_Item_Product $item, int $student_id, int $order_id): void
{
    global $wpdb;
    $table_student_payment = $wpdb->prefix . 'student_payments';
    $product_id = $item->get_product_id();

    // --- Obtención de datos de instituto y alianzas (replicado de process_program_payments) ---
    $institute_id = null;
    $institute = null;
    $alliances = [];
    $manager_user_id = 0;
    $current_item_alliances_json = json_encode([]); // Por defecto, JSON vacío

    $student_data = get_student_detail($student_id);

    if ($student_data && isset($student_data->institute_id) && !empty($student_data->institute_id)) {
        $institute_id = $student_data->institute_id;
        $institute = get_institute_details($institute_id);
        $selected_manager_user_ids = get_managers_institute($institute_id);
        $manager_user_id = isset($selected_manager_user_ids) ? $selected_manager_user_ids[0] : 0;

        if ($institute) { // Solo si el instituto existe, intenta obtener las alianzas.
            $alliances = get_alliances_from_institute($institute_id);
            // Asegurarse de que $alliances sea un array si la función de origen retorna algo diferente
            if (!is_array($alliances)) {
                $alliances = [];
            }
        }
    }

    // Calcular las tarifas de alianzas para este ítem específico
    $current_item_alliances_fees = [];
    // Asegúrate de que FEE_INSCRIPTION y FEE_GRADUATION estén definidas como constantes globales
    $is_fee_product = in_array($product_id, [FEE_INSCRIPTION, FEE_GRADUATION]);
    // Asumiendo que get_post_meta para 'is_scholarship' ya fue validado en la función principal o que siempre retorna un valor booleano o false.
    $is_scholarship = (bool) get_post_meta($order_id, 'is_scholarship', true);

    if (!empty($alliances) && is_array($alliances)) {
        foreach ($alliances as $alliance) {
            $alliance_id = $alliance->id ?? null;
            $alliance_data = ($alliance_id) ? get_alliance_detail($alliance_id) : null;
            $alliance_fee_percentage = (float) ($alliance->fee ?? ($alliance_data->fee ?? 0));

            $total_alliance_fee = 0.0;
            // Las tarifas de alianzas se aplican si NO es un producto tipo FEE y NO es una beca.
            if (!$is_fee_product && !$is_scholarship) {
                $total_alliance_fee = ($alliance_fee_percentage * (float) $item->get_total()) / 100;
            }

            if ($alliance_id) {
                $current_item_alliances_fees[] = [
                    'id' => $alliance_id,
                    'fee_percentage' => $alliance_fee_percentage,
                    'calculated_fee_amount' => $total_alliance_fee,
                ];
            }
        }
    }

    $current_item_alliances_json = json_encode($current_item_alliances_fees);
    if ($current_item_alliances_json === false) {
        $current_item_alliances_json = json_encode([]); // Asegurarse de que sea un JSON válido en caso de error
    }

    // Calcular el institute_fee para este ítem específico
    $current_item_institute_fee = 0.0;
    // El fee del instituto se aplica si el instituto existe, NO es un producto tipo FEE y NO es una beca.
    if ($institute && !$is_fee_product && !$is_scholarship) {
        $institute_fee_percentage = (float) ($institute->fee ?? 0);
        $current_item_institute_fee = ($institute_fee_percentage * (float) $item->get_total()) / 100;
    }
    // --- Fin de obtención de datos ---


    // 5. Intenta actualizar el pago pendiente más antiguo (cuota más baja).
    // Incluye las nuevas columnas en la sentencia UPDATE
    $update_data = [
        'status_id' => 1,
        'order_id' => $order_id,
        'date_payment' => current_time('mysql', true), // Usar current_time para la fecha de pago
        'institute_id' => $institute_id,         // ¡Nueva columna en UPDATE!
        'institute_fee' => $current_item_institute_fee, // ¡Nueva columna en UPDATE!
        'alliances' => $current_item_alliances_json,   // ¡Nueva columna en UPDATE!
        'manager_id' => $manager_user_id
    ];

    // No se puede usar LIMIT y ORDER BY directamente con $wpdb->update().
    // Para actualizar solo la 'cuota' más baja, necesitamos seleccionarla primero.
    $oldest_pending_payment = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM {$table_student_payment} WHERE student_id = %d AND product_id = %d AND status_id = 0 ORDER BY cuote ASC LIMIT 1",
        $student_id,
        $product_id
    ));

    $rows_updated = 0;
    if ($oldest_pending_payment) {
        $rows_updated = $wpdb->update(
            $table_student_payment,
            $update_data, // Datos a actualizar
            ['id' => $oldest_pending_payment->id] // Cláusula WHERE específica para el ID encontrado
        );
    }

    // 6. LÓGICA CLAVE: Si no se actualizó ninguna fila, es porque no había pagos pendientes. Se crea uno nuevo.
    if ($rows_updated === 0) {
        $total = $item->get_total(); // Precio final pagado por este artículo en esta orden.
        $original_price = (float) ($item->get_subtotal() / $item->get_quantity());

        $data = [
            'status_id' => 1, // Nace como pago completado.
            'order_id' => $order_id,
            'student_id' => $student_id,
            'product_id' => $product_id,
            'variation_id' => $item->get_variation_id(),
            'manager_id' => $manager_user_id,
            'institute_id' => $institute_id,
            'institute_fee' => $current_item_institute_fee,
            'alliances' => $current_item_alliances_json,
            'amount' => $total,
            'original_amount_product' => $original_price,
            'total_amount' => $total,
            'original_amount' => $original_price,
            'discount_amount' => $original_price - $total,
            'type_payment' => 2, // Pago único.
            'cuote' => 1,
            'num_cuotes' => 1,
            'date_payment' => current_time('mysql', true),
            'date_next_payment' => null,
        ];

        $wpdb->insert($table_student_payment, $data);
    }
}


/**
 * Maneja las actualizaciones en el CRM externo.
 *
 * @param WC_Order $order Objeto de la orden.
 */
function handle_crm_updates($order)
{
    $crm_id = $order->get_meta('crm_id');
    if ($crm_id) {
        crm_request('contacts', $crm_id, 'PUT', ['status' => 'client']);

        $activity_data = [
            'contactId' => $crm_id,
            'duration' => 15,
            'startAt' => current_time('Y-m-d\TH:i:s'),
            'title' => 'Payment completed',
            'type' => 'SALE',
            'status' => 'completed'
        ];
        crm_request('activities', '', 'POST', $activity_data);
    }
}


/**
 * Actualiza el estado del estudiante y le notifica si es necesario.
 *
 * @param int $student_id ID del estudiante.
 */
function update_student_status_and_notify($student_id)
{
    $student = get_student_detail($student_id);

    // 7. Comprobación más segura del estado del estudiante.
    if ($student && $student->status_id < 2) {
        update_status_student($student_id, 1);

        // Dispara el correo de solicitud de documentos.
        $mailer = WC()->mailer();
        $email = $mailer->get_emails()['WC_Request_Documents_Email'];
        if ($email) {
            $email->trigger($student_id);
        }
    }
}

/**
 * Procesa la orden para crear registros de pago del estudiante y manejar la inscripción.
 *
 * @param WC_Order $order Objeto de la orden de WooCommerce.
 * @param int      $order_id ID de la orden.
 * @param int      $customer_id ID del cliente.
 * @param mixed    $status_register Estado de registro del usuario.
 */
function status_order_not_completed($order, $order_id, $customer_id, $status_register)
{
    // 1. Usa comparaciones estrictas y evita la globalización innecesaria.
    if ($status_register != 1) {
        update_user_meta($customer_id, 'status_register', 0);
    }

    // 2. Procesa los pagos del programa (AES PROGRAM).
    process_program_payments($order, $order_id);

    // 3. Procesa la cuota de inscripción y las acciones asociadas.
    process_inscription_fee($order, $order_id);
}

/**
 * Crea registros de pago para cada artículo en la orden, incluyendo tarifas de alianzas.
 *
 * @param WC_Order $order    Objeto de la orden.
 * @param int      $order_id ID de la orden.
 * @return void
 */
function process_program_payments(WC_Order $order, int $order_id): void
{
    global $wpdb;
    $table_student_payment = $wpdb->prefix . 'student_payments';
    $student_id = $order->get_meta('student_id');

    if (empty($student_id)) {
        return; // Salir si no hay ID de estudiante, ya que es un dato crítico.
    }

    $student_data = get_student_detail($student_id);

    // Validar si $student_data existe y tiene un institute_id
    if (!$student_data || !isset($student_data->institute_id) || empty($student_data->institute_id)) {
        $institute_id = null; // No hay un institute_id válido
        $institute = null;
        $alliances = []; // Array de alianzas vacío
        $manager_user_id = 0;
    } else {
        $institute_id = $student_data->institute_id;
        $institute = get_institute_details($institute_id);
        $selected_manager_user_ids = get_managers_institute($institute_id);
        $manager_user_id = isset($selected_manager_user_ids) ? $selected_manager_user_ids[0] : 0;

        // Si el instituto no existe, el fee será 0 y las alianzas vacías, pero no salimos.
        if (!$institute) {
            $alliances = [];
        } else {
            // Obtener las alianzas del instituto si el instituto existe.
            $alliances = get_alliances_from_institute($institute_id);
            // Si get_alliances_from_institute devuelve null o no es un array, se inicializa como vacío.
            if (!is_array($alliances)) {
                $alliances = [];
            }
        }
    }

    $is_scholarship = (bool) $order->get_meta('is_scholarship'); // Obtener el meta para la beca.

    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();

        if (!$product)
            continue;

        // obtiene el id del producto, de la variacion si lo tiene y el id de la regla si lo tiene
        $product_id = $item->get_product_id();
        $variation_id = $item->get_variation_id() ?? 0;

        // Determinar si este producto es un FEE de inscripción o graduación.
        // Asegúrate de que FEE_INSCRIPTION y FEE_GRADUATION estén definidos como constantes.
        $is_fee_product = in_array($product_id, [FEE_INSCRIPTION, FEE_GRADUATION]);

        // Evita la redundancia procesando solo si no existe un registro previo para este producto en esta orden.
        $existing_record_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_student_payment} WHERE student_id = %d AND product_id = %d",
            $student_id,
            $product_id
        ));

        // salta el producto si encuentra un registro previo
        if ($existing_record_count > 0)
            continue;

        // --- Recalcular tarifas de alianzas para este producto específico ---
        $current_item_alliances_fees = [];
        if (!empty($alliances) && is_array($alliances)) {
            foreach ($alliances as $alliance) {
                $alliance_id = $alliance->id ?? null;
                $alliance_data = ($alliance_id) ? get_alliance_detail($alliance_id) : null;
                $alliance_fee_percentage = (float) ($alliance->fee ?? ($alliance_data->fee ?? 0));

                $total_alliance_fee = 0.0;
                if (!$is_fee_product && !$is_scholarship) {
                    $total_alliance_fee = ($alliance_fee_percentage * (float) $item->get_total()) / 100;
                }

                if ($alliance_id) {
                    $current_item_alliances_fees[] = [
                        'id' => $alliance_id,
                        'fee_percentage' => $alliance_fee_percentage,
                        'calculated_fee_amount' => $total_alliance_fee,
                    ];
                }
            }
        }

        $current_item_alliances_json = json_encode($current_item_alliances_fees);
        if ($current_item_alliances_json === false)
            $current_item_alliances_json = json_encode([]);

        $installments = 1;

        // reglas de las quotas a aplicar
        $rule_id = $item->get_meta('quota_rule_id');
        if ($rule_id) {

            $data_quota_rule = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}quota_rules` WHERE id = %d",
                (int) $rule_id
            ));

            if ($data_quota_rule) {

                $quotas_quantity_rule = (int) $data_quota_rule->quotas_quantity;
                $initial_price = (double) $data_quota_rule->initial_price;
                $quote_price = (double) $data_quota_rule->quote_price;
                $type_frequency = $data_quota_rule->type_frequency;
                $frequency_value = $data_quota_rule->frequency_value;

                $total = (double) ($quotas_quantity_rule * $quote_price) + $initial_price;

                $installments = $quotas_quantity_rule;

                if ($initial_price > 0)
                    $installments++;

                $discount_value = 0;
                $applied_coupons = $order->get_used_coupons();
                if (!empty($applied_coupons)) {
                    foreach ($applied_coupons as $coupon_code) {
                        $coupon = new WC_Coupon($coupon_code);

                        // Validar si el cupón es aplicable al producto y si es un descuento porcentual
                        if ($coupon->is_valid_for_product($product) && $coupon->get_discount_type() == 'percent') {
                            $discount_value += (double) $coupon->get_amount();
                        }
                    }
                }
            }

        } else {

            // --- Cálculos de precios ---
            $original_price = (double) ($item->get_subtotal() / $item->get_quantity());
            $amount = (double) ($item->get_total() / $item->get_quantity());
            $total_amount_to_pay = $amount * $installments;
            $total_original_amount = $original_price * $installments;
            $total_discount_amount = $original_price - $amount;
        }

        // --- Lógica de fechas ---
        $needs_next_payment = !$is_fee_product;
        $start_date = new DateTime();
        $payment_date_obj = clone $start_date;

        for ($i = 0; $i < $installments; $i++) {

            $next_payment_date = null;
            if ($needs_next_payment) {

                if ($data_quota_rule) {

                    $original_price = ($i == 0 && $initial_price > 0) ? $initial_price : $quote_price;
                    $amount = $original_price - (($original_price * $discount_value) / 100);
                    $total_amount_to_pay = $total - (($total * $discount_value) / 100);
                    $total_original_amount = $total;
                    $total_discount_amount = $original_price - $amount;

                    if ($i > 0 && $type_frequency) {
                        switch ($type_frequency) {
                            case 'day':
                                $payment_date_obj->modify("+{$frequency_value} days");
                                break;
                            case 'month':
                                $payment_date_obj->modify("+{$frequency_value} months");
                                break;
                            case 'year':
                                $payment_date_obj->modify("+{$frequency_value} years");
                                break;
                        }
                    }
                }

                $next_payment_date = $payment_date_obj->format('Y-m-d');
            }

            // Calcular el institute_fee para este item específico
            $current_item_institute_fee = 0.0;

            // Solo se calcula la tarifa del instituto si el instituto existe y no es un producto FEE o beca.
            if ($institute && !$is_fee_product && !$is_scholarship) {
                $institute_fee_percentage = (float) ($institute->fee ?? 0);
                $current_item_institute_fee = ($institute_fee_percentage * (float) $item->get_total()) / 100;
            }

            $data = [
                'status_id' => 0,
                'order_id' => ($i + 1) == 1 ? $order_id : null,
                'student_id' => $student_id,
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'manager_id' => ($i + 1) == 1 ? $manager_user_id : null,
                'institute_id' => ($i + 1) == 1 ? $institute_id : null,
                'institute_fee' => ($i + 1) == 1 ? $current_item_institute_fee : 0,
                'alliances' => ($i + 1) == 1 ? $current_item_alliances_json : null,
                'amount' => $amount,
                'original_amount_product' => $original_price,
                'total_amount' => $total_amount_to_pay,
                'original_amount' => $total_original_amount,
                'discount_amount' => $total_discount_amount,
                'type_payment' => $installments > 1 ? 1 : 2,
                'cuote' => ($i + 1),
                'num_cuotes' => $installments,
                'date_payment' => $i == 0 ? $start_date->format('Y-m-d') : null,
                'date_next_payment' => $next_payment_date,
            ];

            $wpdb->insert($table_student_payment, $data);
        }

    }
}

/**
 * Procesa la cuota de inscripción, crea usuarios y envía datos a sistemas externos.
 *
 * @param WC_Order $order Objeto de la orden.
 * @param int      $order_id ID de la orden.
 */
function process_inscription_fee($order, $order_id)
{
    // 10. Búsqueda eficiente del producto de inscripción.
    $inscription_item = null;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() == FEE_INSCRIPTION) {
            $inscription_item = $item;
            break;
        }
    }

    if (!$inscription_item) {
        return;
    }

    global $wpdb;
    $student_id = $order->get_meta('student_id');

    if (empty($student_id) || !are_required_documents_approved($student_id)) {
        return;
    }

    // 11. Crea los usuarios de WordPress y Moodle solo cuando sea necesario.
    $current_user = wp_get_current_user();
    if (in_array('parent', $current_user->roles) && !in_array('student', $current_user->roles)) {
        create_user_student($student_id);
    }

    // Esta única función ahora busca, actualiza el ID si lo encuentra, o crea el usuario si no existe.
    sync_student_with_moodle($student_id);

    // 12. Centraliza la obtención de datos para la API.
    if (MODE != 'UNI') {
        $api_data = prepare_data_for_laravel_api($student_id);
        create_user_laravel($api_data);
    }

    if ($order->get_meta('id_bitrix')) {
        sendOrderbitrix(floatval($order->get_meta('id_bitrix')), $order_id, $order->get_status());
    }

    update_status_student($student_id, 2);
}

/**
 * Verifica si todos los documentos requeridos del estudiante están aprobados.
 *
 * @param int $student_id ID del estudiante.
 * @return bool
 */
function are_required_documents_approved($student_id)
{
    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';

    // 13. Consulta SQL más eficiente.
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_student_documents} WHERE student_id = %d AND is_required = 1 AND status != 5",
        $student_id
    );

    $unapproved_docs_count = $wpdb->get_var($query);
    return $unapproved_docs_count == 0;
}

/**
 * Prepara el array de datos para enviar a la API de Laravel.
 *
 * @param int $student_id ID del estudiante.
 * @return array
 */
function prepare_data_for_laravel_api($student_id)
{
    global $wpdb;
    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}students WHERE id = %d", $student_id));
    $partner_id = $student->partner_id;
    $partner_meta = get_user_meta($partner_id);
    $partner_user = get_user_by('id', $partner_id);

    // 14. Mapeo para conversiones limpias.
    $doc_type_map = ['identification_document' => 1, 'passport' => 2, 'ssn' => 4];
    $gender_map = ['male' => 'M', 'female' => 'F'];
    $grade_map = [1 => 9, 2 => 10, 3 => 11, 4 => 12];

    // 15. Operador de coalescencia nula para valores por defecto.
    $fields = [
        // Datos del Estudiante
        'id_document' => $student->id_document,
        'type_document' => $doc_type_map[$student->type_document] ?? null,
        'firstname' => $student->name . ' ' . $student->middle_name,
        'lastname' => $student->last_name . ' ' . $student->middle_last_name,
        'birth_date' => $student->birth_date,
        'phone' => $student->phone,
        'email' => $student->email,
        'etnia' => $student->ethnicity,
        'grade' => $grade_map[$student->grade_id] ?? null,
        'gender' => $gender_map[$student->gender] ?? null,
        'cod_period' => $student->academic_period,

        // Datos del Representante (Padre/Madre)
        'id_document_re' => $partner_meta['id_document'][0] ?? '000000',
        'type_document_re' => $doc_type_map[$partner_meta['type_document'][0] ?? ''] ?? 1,
        'firstname_re' => $partner_meta['first_name'][0] ?? '',
        'lastname_re' => $partner_meta['last_name'][0] ?? '',
        'birth_date_re' => $partner_meta['birth_date'][0] ?? '',
        'phone_re' => $partner_meta['billing_phone'][0] ?? '',
        'email_re' => $partner_user->user_email ?? '',
        'gender_re' => $gender_map[$partner_meta['gender'][0] ?? ''] ?? 'M',

        // Datos del Programa y Dirección
        'cod_program' => PROGRAM_ID,
        'cod_tip' => TYPE_PROGRAM,
        'address' => $partner_meta['billing_address_1'][0] ?? '',
        'country' => $partner_meta['billing_country'][0] ?? '',
        'city' => $partner_meta['billing_city'][0] ?? '',
        'postal_code' => $partner_meta['billing_postcode'][0] ?? '-',
    ];

    $fields['files'] = get_student_files_for_api($student_id);

    return $fields;
}

/**
 * Obtiene los archivos del estudiante formateados para la API.
 *
 * @param int $student_id ID del estudiante.
 * @return array
 */
function get_student_files_for_api($student_id)
{
    global $wpdb;
    $documents = $wpdb->get_results($wpdb->prepare("SELECT doc.document_id, doc.attachment_id, d.id_requisito FROM {$wpdb->prefix}student_documents doc JOIN {$wpdb->prefix}documents d ON doc.document_id = d.name WHERE doc.student_id = %d AND doc.attachment_id IS NOT NULL", $student_id));

    $files_to_send = [];
    foreach ($documents as $doc) {
        $attachment_path = get_attached_file($doc->attachment_id);
        if ($attachment_path && file_exists($attachment_path)) {
            $files_to_send[] = [
                'file' => curl_file_create($attachment_path, mime_content_type($attachment_path), basename($attachment_path)),
                'id_requisito' => $doc->id_requisito,
            ];
        }
    }
    return $files_to_send;
}

function insert_data_student($order)
{

    if (isset($_COOKIE['institute_id']) && !empty($_COOKIE['institute_id'])) {

        $institute = get_institute_details($_COOKIE['institute_id']);

        $data_student = [
            'birth_date' => $_COOKIE['birth_date'],
            'gender' => $_COOKIE['gender'],
            'ethnicity' => $_COOKIE['ethnicity'],
            'name_student' => $_COOKIE['name_student'],
            'middle_name_student' => $_COOKIE['middle_name_student'],
            'last_name_student' => $_COOKIE['last_name_student'],
            'middle_last_name_student' => $_COOKIE['middle_last_name_student'],
            'phone_student' => $_COOKIE['phone_student'],
            'email_student' => $_COOKIE['email_student'],
            'initial_grade' => get_name_grade($_COOKIE['initial_grade']),
            'program' => get_name_program($_COOKIE['program_id']),
            'name_institute' => strtoupper($institute->name),
            'country' => get_name_country($_POST['billing_country']),
            'state' => $_POST['billing_city'],
            'parent_name' => $_POST['agent_name'],
            'parent_last_name' => $_POST['agent_last_name'],
            'parent_email' => $_POST['email_partner'],
            'parent_number' => $_POST['number_partner'],
        ];

    } else {

        $data_student = [
            'birth_date' => $_COOKIE['birth_date'],
            'gender' => $_COOKIE['gender'],
            'ethnicity' => $_COOKIE['ethnicity'],
            'name_student' => $_COOKIE['name_student'],
            'middle_name_student' => $_COOKIE['middle_name_student'],
            'last_name_student' => $_COOKIE['last_name_student'],
            'middle_last_name_student' => $_COOKIE['middle_last_name_student'],
            'phone_student' => $_COOKIE['phone_student'],
            'email_student' => $_COOKIE['email_student'],
            'initial_grade' => get_name_grade($_COOKIE['initial_grade']),
            'program' => get_name_program($_COOKIE['program_id']),
            'name_institute' => strtoupper($_COOKIE['name_institute']),
            'country' => get_name_country($_POST['billing_country']),
            'state' => $_POST['billing_city'],
            'parent_name' => $_POST['agent_name'],
            'parent_last_name' => $_POST['agent_last_name'],
            'parent_email' => $_POST['email_partner'],
            'parent_number' => $_POST['number_partner'],
        ];
    }

    $order->update_meta_data('student_data', $data_student);
    $order->save();
}

function split_payment()
{
    $cart = WC()->cart;

    if (!$cart || $cart->is_empty()) {
        // Carrito vacío
        include(plugin_dir_path(__FILE__) . 'templates/split-payment.php');
        return;
    }

    $total = $cart->total;
    if ($total > 0) {
        include(plugin_dir_path(__FILE__) . 'templates/split-payment.php');
    }
}
add_action('woocommerce_review_order_before_payment', 'split_payment');
add_action('woocommerce_pay_order_before_payment', 'split_payment');

function payments_parts()
{
    include(plugin_dir_path(__FILE__) . 'templates/payment-parts.php');
}

add_action('woocommerce_after_checkout_billing_form', 'payments_parts');

add_action('wp_ajax_nopriv_woocommerce_update_cart', 'woocommerce_update_cart');
add_action('wp_ajax_woocommerce_update_cart', 'woocommerce_update_cart');

function woocommerce_update_cart()
{
    global $woocommerce;
    $coupon_code = '';
    $has_coupon = false;
    $cart = $woocommerce->cart;
    $applied_coupons = $woocommerce->cart->get_applied_coupons();
    $products_id = [];
    if (count($applied_coupons) > 0) {
        $has_coupon = true;
    }
    $applied_coupons = array_map('strtolower', $applied_coupons);

    $value = $_POST['option'];
    foreach ($cart->get_cart() as $key => $product) {
        array_push($products_id, $product['product_id']);
    }

    $woocommerce->cart->empty_cart();
    $variation = '';

    foreach ($products_id as $key => $product_id) {
        $variations = [];
        $variations_product = [];
        $product = wc_get_product($product_id);
        if ($product->is_type('variable')) {
            $variations_product = $product->get_available_variations();
            foreach ($variations_product as $key => $variation) {
                array_push($variations, ['id' => $variations_product[$key]['variation_id'], 'name' => $variations_product[$key]['attributes']['attribute_payments']]);
            }

            $column = 'name';
            $value = $value;
            $keys = array_keys(array_column($variations, $column));
            $key = array_search($value, array_column($variations, $column));

            $woocommerce->cart->add_to_cart($product_id, 1, $variations[$keys[$key]]['id']);
            $variation = $variations[$keys[$key]]['name'];
        } else {
            $woocommerce->cart->add_to_cart($product_id, 1);
        }
    }

    if ($variation != 'Complete') {
        $applied_coupons = array_diff($applied_coupons, array(strtolower(get_option('offer_complete'))));

        $offer_quote = strtolower(get_option('offer_quote'));

        if (!empty($offer_quote)) {
            if (in_array($offer_quote, $applied_coupons)) {
                // Si existe, lo eliminamos
                $applied_coupons = array_diff($applied_coupons, [$offer_quote]);
            }

            // Agregamos el valor al array
            array_push($applied_coupons, $offer_quote);
        }
    } else {
        // Agregar el cupón con la clave "fee_inscription" a la matriz $applied_coupons
        if (!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) {
            if (!empty(get_option('offer_complete'))) {
                $applied_coupons = array_diff($applied_coupons, array(strtolower(get_option('offer_quote'))));
            }

            if (!empty(get_option('offer_complete'))) {
                array_push($applied_coupons, strtolower(get_option('offer_complete')));
            }
        }
    }

    // Aplicar los cupones restantes en la matriz $applied_coupons
    foreach ($applied_coupons as $key => $coupon) {
        if ($coupon == strtolower(get_option('offer_complete')) || $coupon == strtolower(get_option('offer_quote'))) {
            $max_date_timestamp = get_option('max_date_offer');
            if ($max_date_timestamp >= current_time('timestamp')) {
                $woocommerce->cart->apply_coupon($coupon);
            }
        } else {
            $woocommerce->cart->apply_coupon($coupon);
        }
    }

    // Calculate totals
    $woocommerce->cart->calculate_totals();

    wp_send_json(array('applied_coupons' => $applied_coupons, 'offer' => get_option('offer_complete'), 'quote' => get_option('offer_quote')));
    die();
}

add_action('wp_ajax_nopriv_fee_update', 'fee_update');
add_action('wp_ajax_fee_update', 'fee_update');

function fee_update()
{
    global $woocommerce;
    $value = $_POST['option'];
    $id = FEE_INSCRIPTION;

    if ($value == 'true') {
        $woocommerce->cart->add_to_cart($id, 1);
        $is_complete = returnIsComplete();

        if ($is_complete) {
            if (!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) {
                if (!empty(get_option('offer_complete'))) {
                    $woocommerce->cart->apply_coupon(get_option('offer_complete'));
                }
            }
        } else {
            if (!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) {
                if (!empty(get_option('offer_quote'))) {
                    $woocommerce->cart->apply_coupon(get_option('offer_quote'));
                }
            }
        }

    } else {
        $woocommerce->cart->remove_cart_item($woocommerce->cart->generate_cart_id($id));
        $is_complete = returnIsComplete();

        if ($is_complete) {
            if (!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) {
                if (!empty(get_option('offer_complete'))) {
                    $woocommerce->cart->remove_coupon(get_option('offer_complete'));
                }
            }
        } else {
            if (!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) {
                if (!empty(get_option('offer_quote'))) {
                    $woocommerce->cart->remove_coupon(get_option('offer_quote'));
                }
            }
        }
    }

    $woocommerce->cart->calculate_totals();
}

function returnIsComplete()
{
    global $woocommerce;
    $products_id = [];
    $is_complete = false;

    foreach ($woocommerce->cart->get_cart() as $key => $product) {
        array_push($products_id, $product['variation_id'] ? $product['variation_id'] : $product['product_id']);
    }

    foreach ($products_id as $key => $product_id) {
        $product = wc_get_product($product_id);
        $product_name = $product->get_name();
        if (str_contains($product_name, 'Complete')) {
            $is_complete = true;
        }
    }

    return $is_complete;
}

add_action('wp_ajax_nopriv_load_signatures_data', 'load_signatures_data');
add_action('wp_ajax_load_signatures_data', 'load_signatures_data');

function load_signatures_data()
{
    // Imprime el contenido del archivo modal-reset-password.php
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $document = $_POST['document'] ?? 'ENROLLMENT';
    if (in_array('student', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
        $partner_id = $student->partner_id;
        $student_id = $current_user->ID;
    } else if (in_array('parent', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");
        $user_student = get_user_by('email', $student->email);
        $student_id = $user_student->ID;
        $partner_id = $current_user->ID;
    }
    $table_signatures = $wpdb->prefix . 'users_signatures';
    $student_signature = $wpdb->get_row("SELECT * FROM {$table_signatures} WHERE user_id='{$student_id}' AND document_id='{$document}'");
    $parent_signature = $wpdb->get_row("SELECT * FROM {$table_signatures} WHERE user_id='{$partner_id}' AND document_id='{$document}'");
    if ($parent_signature) {
        $grade_selected = $parent_signature->grade_selected ? $parent_signature->grade_selected : null;
    } else if ($student_signature) {
        $grade_selected = $student_signature->grade_selected ? $student_signature->grade_selected : null;
    }
    wp_send_json(array('grade_selected' => $grade_selected, 'parent_signature' => $parent_signature ? json_decode($parent_signature->signature) : [], 'student_signature' => $student_signature ? json_decode($student_signature->signature) : []));
}

/**
 * Actualiza el precio de un producto en el carrito según la regla de cuota seleccionada.
 * 
 * Esta función recibe el ID de un producto y el ID de una regla de cuota a través de una
 * solicitud AJAX. Consulta la base de datos para obtener el precio correspondiente y actualiza
 * el precio del producto en el carrito de WooCommerce.
 * 
 * @return void
 * 
 * @uses WPDB
 * @uses WC_Cart
 */
add_action('wp_ajax_update_price_product_cart_quota_rule', 'update_price_product_cart_quota_rule');
add_action('wp_ajax_nopriv_update_price_product_cart_quota_rule', 'update_price_product_cart_quota_rule');
function update_price_product_cart_quota_rule()
{
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $rule_id = isset($_POST['rule_id']) ? intval($_POST['rule_id']) : 0;

    if ($product_id <= 0 || $rule_id <= 0) {
        wp_send_json_error(__('Invalid data', 'edusystem'));
        exit;
    }

    global $wpdb;
    $price = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT 
                CASE 
                    WHEN initial_price > 0 THEN initial_price 
                    ELSE quote_price 
                END AS price 
            FROM {$wpdb->prefix}quota_rules
            WHERE id = %d",
            $rule_id
        )
    );

    if (!$price) {
        wp_send_json_error(__('Rule not found in database', 'edusystem'));
        exit;
    }

    // Get the quotas_quantity
    $quotas_quantity = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT quotas_quantity
            FROM {$wpdb->prefix}quota_rules
            WHERE id = %d",
            $rule_id
        )
    );

    $cookie_name = 'fixed_fee_inscription';
    $cookie_does_not_exist = !isset($_COOKIE[$cookie_name]);
    $cookie_exists_and_condition_met = (
        isset($_COOKIE[$cookie_name]) &&
        !empty($_COOKIE[$cookie_name]) &&
        $_COOKIE[$cookie_name] !== 'true'
    );

    if (($cookie_does_not_exist || $cookie_exists_and_condition_met)) {
        // Get the coupon codes to potentially remove
        $offer_complete_coupon = get_option('offer_complete');
        $offer_quote_coupon = get_option('offer_quote');
        
        // Remove 'offer_complete' coupon if it's applied
        if (WC()->cart->has_discount($offer_complete_coupon)) {
            WC()->cart->remove_coupon($offer_complete_coupon);
        }

        // Remove 'offer_quote' coupon if it's applied
        if (WC()->cart->has_discount($offer_quote_coupon)) {
            WC()->cart->remove_coupon($offer_quote_coupon);
        }

        if ( $quotas_quantity == 1 ) {
            WC()->cart->apply_coupon($offer_complete_coupon);
        }

        if ( $quotas_quantity > 1 ) {
            WC()->cart->apply_coupon($offer_quote_coupon);
        }
    }

    $cart = WC()->cart;
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        // Verificar si el ID del producto o el ID de la variación coinciden
        if ($cart_item['product_id'] === $product_id || (isset($cart_item['variation_id']) && $cart_item['variation_id'] === $product_id)) {
            $cart_item['data']->set_price($price);
            $cart_item['data']->set_sale_price($price);

            // Almacenar el nuevo precio en el array del artículo del carrito
            $cart_item['custom_price'] = $price; // Aquí se almacena el nuevo precio

            // Guarda el id de la regla de la cuota
            $cart_item['quota_rule_id'] = $rule_id;

            // Actualizar el artículo del carrito
            $cart->cart_contents[$cart_item_key] = $cart_item;

            // Actualizar el carrito
            WC()->cart->set_session();

            wp_send_json_success(__('Precio actualizado', 'edusystem'));
            exit;
        }
    }

    // en caso de no encontrar el producto
    wp_send_json_error(__('Product not found in cart', 'edusystem'));
    exit;
}

/**
 * Guarda metadatos personalizados para los ítems de la orden durante el proceso de checkout.
 * 
 * Esta función se ejecuta cuando se crea un ítem de la orden a partir de los artículos del carrito.
 * Si el producto en el carrito tiene un `quota_rule_id`, este se guarda como metadato en el ítem
 * de la orden.
 * 
 * @param WC_Order_Item_Product $item El ítem de la orden que se está creando.
 * @param string $cart_item_key La clave del artículo en el carrito.
 * @param array $values Los valores del artículo del carrito, que pueden incluir metadatos personalizados.
 * @param WC_Order $order La orden a la que pertenece el ítem.
 * 
 * @return void
 * 
 * @uses WC_Order_Item_Product
 * @uses WC_Order
 */
add_action('woocommerce_checkout_create_order_line_item', 'save_metadata_checkout_create_order_item', 10, 4);
function save_metadata_checkout_create_order_item($item, $cart_item_key, $values, $order)
{

    // Si el producto en el carrito tiene un quote_rule_id, lo guarda en el ítem de la orden
    if (isset($values['quota_rule_id'])) {
        $item->add_meta_data('quota_rule_id', $values['quota_rule_id']);
    }
}



add_action('wp_ajax_nopriv_reload_payment_table', 'reload_payment_table');
add_action('wp_ajax_reload_payment_table', 'reload_payment_table');

function reload_payment_table()
{
    ob_start();
    ?>
        <?php
        $value = $_POST['option'];
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        $id = FEE_INSCRIPTION;
        $filtered_products = array_filter($cart, function ($product) use ($id) {
            return $product['product_id'] != $id;
        });

        $cart_total = 0;
        $product_id = null;
        foreach ($filtered_products as $key => $product) {
            $product_id = $product['product_id'];
            $cart_total = $product['line_total'];
            // $price = $product['line_total']; 
        }
        if (isset($product_id)) {
            $product = wc_get_product($product_id);
            if ($product->is_type('variable')) {
                $variations = $product->get_available_variations();
                $date = new DateTime();
                $date = $date->format('Y-m-d');
                foreach ($variations as $key => $variation) {
                    if ($variation['attributes']['attribute_payments'] === $value) {
                        ?>
                                                                                                                                        <table class="payment-parts-table mt-5">
                                                                                                                                            <tr>
                                                                                                                                                <th class="payment-parts-table-header">Payment</th>
                                                                                                                                                <th class="payment-parts-table-header">Next date payment</th>
                                                                                                                                                <th class="payment-parts-table-header">Amount</th>
                                                                                                                                            </tr>
                                                                                                                                            <?php
                                                                                                                                            $date_calc = '';
                                                                                                                                            switch ($value) {
                                                                                                                                                case 'Annual':
                                                                                                                                                    $date_calc = '+1 year';
                                                                                                                                                    break;
                                                                                                                                                case 'Semiannual':
                                                                                                                                                    $date_calc = '+6 months';
                                                                                                                                                    break;
                                                                                                                                            }
                                                                                                                                            $cuotes = get_post_meta($variation['variation_id'], 'num_cuotes_text', true);
                                                                                                                                            for ($i = 0; $i < $cuotes; $i++) {
                                                                                                                                                $date = $i > 0 ? date('Y-m-d', strtotime($date_calc, strtotime($date))) : $date;
                                                                                                                                                ?>
                                                                                                                                                                                                <tr class="payment-parts-table-row">
                                                                                                                                                                                                    <td class="payment-parts-table-data"><?php echo ($i + 1) ?></td>
                                                                                                                                                                                                    <td class="payment-parts-table-data">
                                                                                                                                                                                                        <?php echo ($i === 0 ? date('F d, Y') . ' (Current)' : date('F d, Y', strtotime($date))) ?>
                                                                                                                                                                                                    </td>
                                                                                                                                                                                                    <td class="payment-parts-table-data"><?php echo wc_price($cart_total) ?></td>
                                                                                                                                                                                                </tr>
                                                                                                                                                                                                <?php
                                                                                                                                            }
                                                                                                                                            ?>
                                                                                                                                            <tr>
                                                                                                                                                <th class="payment-parts-table-header text-end" colspan="3">Total</th>
                                                                                                                                            </tr>
                                                                                                                                            <tr class="payment-parts-table-row">
                                                                                                                                                <td class="payment-parts-table-data text-end" colspan="3"><?php echo wc_price(($cart_total * $cuotes)) ?></td>
                                                                                                                                            </tr>
                                                                                                                                        </table>
                                                                                                                                        <?php
                    }
                }
            }
        }
        $html = ob_get_clean();
        echo $html;
        wp_die();
}

add_action('wp_ajax_nopriv_reload_button_schoolship', 'reload_button_schoolship');
add_action('wp_ajax_reload_button_schoolship', 'reload_button_schoolship');
function reload_button_schoolship()
{
    ob_start();
    global $woocommerce;
    $has_scholarship = false;
    $applied_coupons = $woocommerce->cart->get_applied_coupons();

    foreach ($applied_coupons as $coupon) {
        if (strtolower($coupon) == 'latam scholarship') {
            $has_scholarship = true;
            break;
        }
    }

    // Check if the 'name_institute' cookie is NOT set or is empty
    /* if (!isset($_COOKIE['name_institute']) || empty($_COOKIE['name_institute'])) { */
    ?>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-5 mb-5" style="text-align:center;">
            <?php if ($has_scholarship): ?>
                <button id="apply-scholarship-btn" type="button" disabled>
                <?php echo (isset($_COOKIE['from_webinar']) && !empty($_COOKIE['from_webinar'])) ? 'Special webinar offer already applied' : 'Scholarship already applied' ?>
                </button>
            <?php else: ?>
                <button id="apply-scholarship-btn" type="button">
                <?php echo (isset($_COOKIE['from_webinar']) && !empty($_COOKIE['from_webinar'])) ? 'Special webinar offer' : 'Activate scholarship' ?>
                </button>
            <?php endif; ?>
            </div>
        <?php
        /* } */

        $html = ob_get_clean();
        echo $html;
        wp_die();
}

add_action('wp_ajax_nopriv_apply_scholarship', 'apply_scholarship');
add_action('wp_ajax_apply_scholarship', 'apply_scholarship');
function apply_scholarship()
{

    $product_id = $_POST['product_id'] ?? 0;

    global $woocommerce;
    $cart = $woocommerce->cart;

    $coupon_code = 'Latam Scholarship';
    $cart->apply_coupon($coupon_code);

    // Calcular totales
    $cart->calculate_totals();

    // Obtener los cupones aplicados
    $coupons = $cart->get_coupons();

    // Definir el producto a validar 
    $discount_value = 0;
    $product = wc_get_product($product_id);
    if ($product && !empty($coupons)) {

        // Verificar si hay cupones aplicados
        foreach ($coupons as $code => $coupon) {
            // Validar si el cupón es aplicable al producto y si es un descuento porcentual
            if ($coupon->is_valid_for_product($product) && $coupon->get_discount_type() == 'percent') {
                $discount_value += $coupon->get_amount();
            }
        }
    }

    // Devolver el valor neto del descuento
    wp_send_json_success(['discount_value' => $discount_value]);
    exit;

}

function woocommerce_custom_price_to_cart_item($cart_object)
{
    if (!WC()->session->__isset("reload_checkout")) {
        foreach ($cart_object->cart_contents as $key => $value) {
            if (isset($value["custom_price"])) {
                //for woocommerce version lower than 3
                //$value['data']->price = $value["custom_price"];
                //for woocommerce version +3
                $value['data']->set_price($value["custom_price"]);
            }
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'woocommerce_custom_price_to_cart_item', 99);


add_filter('woocommerce_account_dashboard', 'fee_inscription_button', 2);
function fee_inscription_button()
{
    // VERIFICAR FEE DE INSCRIPCION
    global $wpdb;
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    $partner_id = get_current_user_id();
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
    foreach ($students as $key => $student) {
        $paid = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id = {$student->id} and product_id = " . FEE_INSCRIPTION);
        if ($paid) {
            unset($students[$key]);
        }
    }
    // VERIFICAR FEE DE INSCRIPCION
    include(plugin_dir_path(__FILE__) . 'templates/fee-inscription-payment.php');
}

function custom_coupon_applied_notice($message)
{
    wc_clear_notices();

    $applied_coupons = WC()->cart->get_applied_coupons();
    if (!empty($applied_coupons)) {
        $coupon_list = implode(', ', array_map('ucwords', $applied_coupons));
        wc_add_notice(__($coupon_list . ' applied successfully', 'woocommerce'), 'success');
    }
}
add_action('woocommerce_applied_coupon', 'custom_coupon_applied_notice');

// Hook to update coupon label individually for each coupon
function update_coupon_label_individually($coupon_html, $coupon)
{
    // $coupon_html = str_replace('Coupon:', $coupon, $coupon_html);
    // $coupon_html = str_replace($coupon, '', $coupon_html);
    return ucfirst($coupon->code);
}

// Apply the hook
add_filter('woocommerce_cart_totals_coupon_label', 'update_coupon_label_individually', 10, 2);

function redirect_after_login($redirect_to, $user)
{
    if (is_wp_error($user) || !isset($user->roles)) {
        return $redirect_to;
    }

    $roles = $user->roles;
    // Redirigir a home_url() si tiene AL MENOS UNO de estos roles
    if (in_array('student', $roles) || in_array('parent', $roles) || in_array('teacher', $roles)) {
        return home_url('my-account'); // Usuarios no-admin
    } else {
        return admin_url(); // Administradores/editores/otros roles
    }
}

add_filter('woocommerce_login_redirect', 'redirect_after_login', 999, 2);

function custom_logout_redirect($redirect_to, $request, $user)
{
    $redirect_to = home_url('my-account/'); // Redirect to My Account page
    return $redirect_to;
}
add_filter('logout_redirect', 'custom_logout_redirect', 10, 3);

function custom_cart_item_name($item_name, $cart_item, $cart_item_key)
{
    // Check if we're on the checkout page
    if (is_checkout()) {
        // Get the product ID
        $product_id = $cart_item['product_id'];

        // // Get the custom product name (e.g. from a custom field)
        // $product = wc_get_product($product_id);
        // $custom_name = $product->get_meta('num_cuotes_text') ? $product->get_meta('num_cuotes_text') : 1;

        // // If custom name exists, use it, otherwise use the default name
        // if ($custom_name > 1) {
        //     $item_name = 'First payment of program';
        // } else {
        //     $item_name = $item_name;
        // }

        $item_name = $item_name;
        if (str_contains($item_name, 'Annual') || str_contains($item_name, 'Semiannual')) {
            $item = explode("-", $item_name);
            $item_name = $item[0] . '- 1 cuote';
        }
    }

    return $item_name;
}
add_filter('woocommerce_cart_item_name', 'custom_cart_item_name', 10, 3);

function hide_checkout_cart_item_quantity($quantity, $cart_item, $cart_item_key)
{
    // Check if we're on the checkout page
    if (is_checkout()) {
        $quantity = ''; // Return an empty string to hide the quantity
    }
    return $quantity;
}
add_filter('woocommerce_checkout_cart_item_quantity', 'hide_checkout_cart_item_quantity', 10, 3);

add_action('wp_ajax_nopriv_exist_user_email', 'exist_user_email');
add_action('wp_ajax_exist_user_email', 'exist_user_email');
function exist_user_email()
{
    global $wpdb;
    $email = $_POST['option'];
    $scholarship = $_POST['scholarship'] ?? null;
    $table_student = $wpdb->prefix . 'students';
    $table_users = $wpdb->prefix . 'users';
    $table_pre_users = $wpdb->prefix . 'pre_users';
    $table_pre_students = $wpdb->prefix . 'pre_students';

    $students = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_student WHERE email = %s", array($email)));
    $users = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_users WHERE user_email = %s", array($email)));

    $pre_students = array();
    $pre_users = array();
    if ($scholarship == 1) {
        $pre_students = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_pre_students WHERE email = %s", array($email)));
        $pre_users = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_pre_users WHERE email = %s", array($email)));
    }

    if (!empty($students) || !empty($users) || !empty($pre_students) || !empty($pre_users)) {
        echo 1;
        exit;
    } else {
        echo 0;
        exit;
    }
}

add_action('wp_ajax_nopriv_get_states_country', 'get_states_country');
add_action('wp_ajax_get_states_country', 'get_states_country');
function get_states_country()
{
    $country = $_POST['option'];
    $states = WC()->countries->get_states($country);

    wp_send_json(array('states' => $states));
    exit;
}

add_action('wp_ajax_nopriv_exist_user_id', 'exist_user_id');
add_action('wp_ajax_exist_user_id', 'exist_user_id');
function exist_user_id()
{
    global $wpdb;
    $id = $_POST['option'];
    $type = $_POST['type'];
    $scholarship = isset($_POST['scholarship']) ? $_POST['scholarship'] : null;
    $table_student = $wpdb->prefix . 'students';
    $table_pre_student = $wpdb->prefix . 'pre_students';
    $students = $wpdb->get_results("SELECT * FROM {$table_student} WHERE type_document = '{$type}' AND id_document = '{$id}'");

    $pre_students = [];
    if ($scholarship == 1) {
        $pre_students = $wpdb->get_results("SELECT * FROM {$table_pre_student} WHERE type_document = '{$type}' AND id_document = '{$id}'");
    }
    if (sizeof($students) > 0 || sizeof($pre_students) > 0) {
        echo 1;
        exit;
    } else {
        echo 0;
        exit;
    }
}

add_action('wp_ajax_nopriv_check_scholarship', 'check_scholarship');
add_action('wp_ajax_check_scholarship', 'check_scholarship');
function check_scholarship()
{
    global $wpdb;
    $id = $_POST['option'];
    $type = $_POST['type'];
    $table_pre_scholarship = $wpdb->prefix . 'pre_scholarship';
    $table_students = $wpdb->prefix . 'students';

    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE type_document = '{$type}' AND id_document = '{$id}'");
    if ($student) {
        echo 0;
        exit;
    }

    $pre_scholarship = $wpdb->get_row("SELECT * FROM {$table_pre_scholarship} WHERE document_type = '{$type}' AND document_id = '{$id}'");
    if ($pre_scholarship) {
        $scholarship = get_scholarship_details($pre_scholarship->scholarship_type);
        echo $scholarship->name;
        exit;
    } else {
        echo 0;
        exit;
    }
}

add_action('wp_ajax_nopriv_student_unsubscribe', 'student_unsubscribe_callback');
add_action('wp_ajax_student_unsubscribe', 'student_unsubscribe_callback');
function student_unsubscribe_callback()
{
    global $current_user, $wpdb;
    $reason = $_POST['reason'];
    $student_id = $_POST['student_id'];
    $student = get_student_detail($student_id);
    $description = 'Unsubscription request: ' . $reason;
    $user_student = get_user_by('email', $student->email);
    $user_parent = get_user_by('id', $student->partner_id);
    send_notification_user($user_student->ID, $description, 3, 'unsubscribe');
    send_notification_user($user_parent->ID, $description, 3, 'unsubscribe');

    $table_requests = $wpdb->prefix . 'requests';
    $wpdb->insert($table_requests, [
        'partner_id' => $user_parent->ID,
        'student_id' => $student_id,
        'description' => $reason,
        'type' => 'Unsubscription request',
        'status_id' => 0,
    ]);
    wp_send_json(array('success' => true));
    exit;
}

add_action('wp_ajax_nopriv_select_elective', 'select_elective_callback');
add_action('wp_ajax_select_elective', 'select_elective_callback');
function select_elective_callback()
{
    global $current_user, $wpdb;
    $roles = $current_user->roles;
    $elective = $_POST['elective'];
    $table_students = $wpdb->prefix . 'students';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $student_id = null;
    if (in_array('parent', $roles)) {
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id={$current_user->ID}");
        $student_id = $student->id;
    } else if (in_array('student', $roles)) {
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
        $student_id = $student->id;
    }

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$elective}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student_id}");
    $projection_obj = json_decode($projection->projection);

    array_push($projection_obj, [
        'code_subject' => $subject->code_subject,
        'subject_id' => $subject->id,
        'subject' => $subject->name,
        'hc' => $subject->hc,
        'cut' => $cut,
        'code_period' => $code,
        'calification' => "",
        'is_completed' => true,
        'this_cut' => true,
        'is_elective' => true,
        'welcome_email' => true,
    ]);

    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj),
    ], ['id' => $projection->id]);

    $wpdb->update($table_students, [
        'elective' => 0,
    ], ['id' => $student_id]);

    $section = load_section_available($subject->id, $code, $cut);

    $wpdb->insert($table_student_period_inscriptions, [
        'status_id' => $projection_obj[count($projection_obj) - 1]['this_cut'] ? 1 : 3,
        'type' => 'elective',
        'section' => $section,
        'student_id' => $projection->student_id,
        'subject_id' => $projection_obj[count($projection_obj) - 1]['subject_id'],
        'code_subject' => $projection_obj[count($projection_obj) - 1]['code_subject'],
        'code_period' => $projection_obj[count($projection_obj) - 1]['code_period'],
        'cut_period' => $projection_obj[count($projection_obj) - 1]['cut']
    ]);

    if (get_option('auto_enroll_elective')) {
        $offer = get_offer_filtered($subject->id, $code, $cut);
        $enrollments = [];
        $enrollments = array_merge($enrollments, courses_enroll_student($projection->student_id, [(int) $offer->moodle_course_id]));
        enroll_student($enrollments);
    } else {
        update_count_moodle_pending();
    }

    update_max_upload_at($projection->student_id);
    wp_send_json(array('success' => true));
    exit;
}

function get_next_cut($period, $period_cut)
{
    $code = 'noperiod';
    $cut = 'nocut';
    if ($period && $period_cut) {
        switch ($period_cut->cut_period) {
            case 'A':
                $cut = 'B';
                $code = $period->code;
                break;
            case 'B':
                $cut = 'C';
                $code = $period->code;
                break;
            case 'C':
                $cut = 'D';
                $code = $period->code;
                break;
            case 'D':
                $cut = 'E';
                $code = $period->code;
                break;
            case 'E':
                $cut = 'A';
                $code = $period->code_next;
                break;
        }
    }

    return ['code' => $code, 'cut' => $cut];

}

function redirect_logged_in_users_to_my_account()
{
    if (is_user_logged_in() && is_front_page()) {
        $current_user = wp_get_current_user();
        $birthday = get_user_meta($current_user->ID, 'birth_date', true);
        $age = floor((time() - strtotime($birthday)) / 31556926);
        if ($age < 18) {
            $page_id = wc_get_page_id('myaccount');
            $url = get_permalink($page_id);
            wp_redirect($url);
            exit;
        }
    }
}
add_action('template_redirect', 'redirect_logged_in_users_to_my_account');

function sendOrderbitrix($id_bitrix, $id_order, $status)
{
    // Define los datos del body
    $body = array(
        'id_bitrix' => $id_bitrix,
        'order_id' => $id_order, // reemplaza con el valor real
        'status_id' => $status // reemplaza con el valor real
    );

    // Construct the API URL
    $url = 'https://api.luannerkerton.com/api/addNewOrderAes';

    // Use WordPress's built-in HTTP API to make a POST request
    $response = wp_remote_post(
        $url,
        array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body)
        )
    );

    // Check if the response was successful
    if (wp_remote_retrieve_response_code($response) === 200) {
        // Get the JSON data from the response
        $data = json_decode(wp_remote_retrieve_body($response), true);
        // Do something with the data...
    } else {
        // Handle the error

    }
}


add_action('wp', 'verificar_acciones_mi_cuenta_optimizado');

function verificar_acciones_mi_cuenta_optimizado()
{
    // 1. SALIDAS TEMPRANAS: Salimos de inmediato si no cumplimos las condiciones básicas.
    // Esto evita procesar innecesariamente en páginas que no son "Mi Cuenta".
    if (!is_account_page() || !is_user_logged_in()) {
        return;
    }

    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $roles = (array) $current_user->roles;

    // 2. LÓGICA CONSOLIDADA: Se obtiene el estudiante asociado una sola vez.
    $student = null;
    if (in_array('student', $roles, true)) {
        $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}students WHERE email = %s", $current_user->user_email));
    } elseif (in_array('parent', $roles, true)) {
        $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}students WHERE partner_id = %d", $user_id));
    }

    // --- Flujo Lógico Principal ---

    // Caso 1: El usuario no tiene un registro de estudiante y necesita llenar información inicial.
    if (!$student && !get_user_meta($user_id, 'pay_application_password', true) && (in_array('student', $roles) || in_array('parent', $roles))) {
        add_action('wp_footer', 'modal_fill_info');
        return; // Termina la ejecución aquí.
    }

    // Caso 2: El usuario ya tiene un registro de estudiante.
    if ($student) {
        // Limpia un metadato que parece ser temporal.
        update_user_meta($user_id, 'pay_application_password', 0);

        // Separamos la lógica en funciones más claras.
        _mi_cuenta_handle_split_payments($user_id);
        _mi_cuenta_determine_modal_action($student, $roles, $user_id);
    }
}

/**
 * Función auxiliar para manejar órdenes con pago dividido.
 * @param int $user_id
 */
function _mi_cuenta_handle_split_payments($user_id)
{
    $pending_orders = wc_get_orders(['status' => 'split-payment', 'customer_id' => $user_id, 'limit' => 1]);

    if (empty($pending_orders)) {
        return;
    }

    $order = $pending_orders[0];

    // 3. LÓGICA SIMPLIFICADA: Solo procesar si es un pago dividido no completado.
    if (!$order->get_meta('split_payment') || $order->get_meta('split_complete')) {
        return;
    }

    $split_method = json_decode($order->get_meta('split_method', true));
    $total_paid = array_sum(wp_list_pluck($split_method, 'amount'));
    $total_paid_gross = array_sum(wp_list_pluck($split_method, 'gross_total'));
    $pending_payment = $order->get_total() - $total_paid;

    // 4. ACTUALIZACIÓN EFICIENTE DE METADATOS: `update_meta_data` crea el metadato si no existe.
    $order->update_meta_data('total_paid', $total_paid);
    $order->update_meta_data('total_paid_gross', $total_paid_gross);
    $order->update_meta_data('pending_payment', $pending_payment);

    $is_payment_complete = $pending_payment <= 0;

    if ($is_payment_complete) {
        $on_hold_found = false;
        foreach ($split_method as $method) {
            if ($method->status === 'on-hold') {
                $on_hold_found = true;
                break;
            }
        }
        $order->update_status($on_hold_found ? 'on-hold' : 'completed');
    }

    $order->save();

    if (!$is_payment_complete && !$order->get_meta('one_time_payment')) {
        $checkout_url = $order->get_checkout_payment_url();
        wp_redirect($checkout_url);
        exit;
    }
}

/**
 * Función auxiliar para determinar qué modal mostrar al usuario.
 * @param object $student
 * @param array $roles
 * @param int $current_user_id
 */
function _mi_cuenta_determine_modal_action($student, $roles, $current_user_id)
{
    global $wpdb;

    // Si se necesita crear contraseña, esta es la acción prioritaria.
    if (in_array('student', $roles, true) && $student->set_password == 0) {
        add_action('wp_footer', 'modal_create_password');
        return;
    }

    // Si debe elegir una electiva, es la siguiente prioridad.
    if (in_array('student', $roles, true) && $student->elective == 1) {
        add_action('wp_footer', 'modal_take_elective');
        return;
    }

    // 5. CONSULTAS SEGURAS Y EFICIENTES
    if (MODE != 'UNI') {
        $table_user_signatures = $wpdb->prefix . 'users_signatures';
        $student_user = in_array('student', $roles, true) ? get_user_by('id', $current_user_id) : get_user_by('email', $student->email);
        $parent_user_id = $student->partner_id;

        // Verificar firmas
        $parent_enrollment_sig = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_user_signatures} WHERE user_id = %d AND document_id = 'ENROLLMENT'", $parent_user_id));
        $student_enrollment_sig = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_user_signatures} WHERE user_id = %d AND document_id = 'ENROLLMENT'", $student_user->ID));

        // Verificar si el documento de inscripción fue creado
        $document_was_created = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}student_documents WHERE student_id = %d AND document_id = 'ENROLLMENT'", $student->id));

        // Verificar pagos pendientes
        $has_pending_payments = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}student_payments WHERE student_id = %d AND status_id = 0 AND date_next_payment <= NOW()", $student->id));

        // 6. CONDICIONALES LEGIBLES
        $all_signatures_missing = !$parent_enrollment_sig || !$student_enrollment_sig;

        if ($document_was_created && $all_signatures_missing && !$has_pending_payments) {
            add_action('wp_footer', 'modal_enrollment_student');
            return;
        }

        $parent_missing_sig = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_user_signatures} WHERE user_id = %d AND document_id = 'MISSING DOCUMENT'", $parent_user_id));
        $student_missing_sig = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_user_signatures} WHERE user_id = %d AND document_id = 'MISSING DOCUMENT'", $student_user->ID));
        $all_missing_sigs_done = !$parent_missing_sig || !$student_missing_sig;

        if ($document_was_created && !$all_signatures_missing && $all_missing_sigs_done && !$has_pending_payments) {
            add_action('wp_footer', 'modal_missing_student');
        }
    }
}
function modal_take_elective()
{
    global $wpdb, $current_user;

    $roles = $current_user->roles;
    if (!in_array('student', $roles) || !get_option('show_modal_electives')) {
        return;
    }

    $table_students = $wpdb->prefix . 'students';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_academic_offers = $wpdb->prefix . 'academic_offers';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $conditions = array();
    $params = array();

    $electives_ids = $wpdb->get_col("SELECT subject_id FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id} AND status_id != 4 AND code_subject IS NOT NULL AND code_subject <> '' AND subject_id IS NOT NULL AND subject_id <> ''");
    if ($electives_ids) {
        $conditions[] = "id NOT IN (" . implode(',', array_fill(0, count($electives_ids), '%d')) . ")";
    }
    $conditions[] = "type = 'elective'";
    $params = array_merge($params, $electives_ids);

    $query = "SELECT * FROM {$table_school_subjects}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $electives = $wpdb->get_results($wpdb->prepare($query, $params));
    $available_electives = [];
    foreach ($electives as $key => $elective) {
        $offer = get_offer_filtered($elective->id, $code, $cut);
        if ($offer) {
            array_push($available_electives, $elective);
        }
    }

    if (count($available_electives) == 0) {
        return;
    }

    $electives = $available_electives;
    include(plugin_dir_path(__FILE__) . 'templates/modal-select-elective.php');
}

function modal_missing_student()
{
    // Imprime el contenido del archivo modal-reset-password.php
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $show_parent_info = 1;
    if (in_array('student', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");

        $partner_id = $student->partner_id;
        $student_id = $current_user->ID;

        $birth_date = get_user_meta($current_user->ID, 'birth_date', true);
        $birth_date_timestamp = strtotime($birth_date);
        $current_timestamp = time();
        $age = floor(($current_timestamp - $birth_date_timestamp) / 31536000); // 31536000 es el número de segundos en un año
        if ($age >= 18) {
            $show_parent_info = 0;
        }
    } else if (in_array('parent', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");

        $user_student = get_user_by('email', $student->email);
        $student_id = $user_student->ID;
        $partner_id = $current_user->ID;
    }

    $user_partner = get_user_by('id', $student->partner_id);
    $user = [
        'student_full_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
        'student_signature' => $student->name . ' ' . $student->last_name,
        'parent_full_name' => get_user_meta($student->partner_id, 'first_name', true) . ' ' . get_user_meta($student->partner_id, 'last_name', true),
        'today' => date('Y-m-d'),
    ];
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE `status` != 5 AND is_visible=1 AND is_required = 0 AND student_id={$student->id}");
    $documents_required_not_approved = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE `status` != 5 AND is_visible=1 AND is_required = 1 AND student_id={$student->id}");
    $today = date('m-d-Y');
    if (count($documents_required_not_approved) == 0) {
        include plugin_dir_path(__FILE__) . 'templates/create-missing-documents.php';
    }
}

function modal_fill_info()
{
    include plugin_dir_path(__FILE__) . 'templates/fill-info.php';
}

function modal_enrollment_student()
{
    // Imprime el contenido del archivo modal-reset-password.php
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $show_parent_info = 1;
    if (in_array('student', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $table_student_payments = $wpdb->prefix . 'student_payments';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
        $payment = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id='{$student->id}' ORDER BY id DESC");
        $partner_id = $student->partner_id;
        $student_id = $current_user->ID;
        $institute_id = $student->institute_id;
        $age = floor((strtotime($student->created_at) - strtotime($student->birth_date)) / 31536000); // 31536000 es el número de segundos en un año
        if ($age >= 18) {
            $show_parent_info = 0;
        }
    } else if (in_array('parent', $roles)) {
        $table_students = $wpdb->prefix . 'students';
        $table_student_payments = $wpdb->prefix . 'student_payments';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");
        $user_student = get_user_by('email', $student->email);
        $payment = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id='{$student->id}' ORDER BY id DESC");
        $student_id = $user_student->ID;
        $partner_id = $current_user->ID;
        $institute_id = $student->institute_id;
    }

    $institute = $institute_id ? get_institute_details($institute_id) : null;
    $institute_name = $student->name_institute;
    $user_partner = get_user_by('id', $student->partner_id);

    $user = [
        'student_full_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
        'student_signature' => $student->name . ' ' . $student->last_name,
        'student_created_at' => date('Y-m-d', strtotime($student->created_at)),
        'student_grade' => $student->grade_id,
        'student_payment' => $payment->type_payment,
        'student_birth_date' => $student->birth_date,
        'student_gender' => ucfirst($student->gender),
        'student_address' => get_user_meta($partner_id, 'billing_address_1', true),
        'student_country' => get_user_meta($partner_id, 'billing_country', true),
        'student_phone' => $student->phone,
        'parent_cell' => get_user_meta($partner_id, 'billing_phone', true),
        'parent_identification' => get_user_meta($partner_id, 'id_document', true),
        'student_identification' => $student->id_document,
        'parent_full_name' => get_user_meta($student->partner_id, 'first_name', true) . ' ' . get_user_meta($student->partner_id, 'last_name', true),
        'parent_email' => $user_partner->user_email,
        'student_email' => $student->email,
        'today' => date('Y-m-d'),
    ];
    include plugin_dir_path(__FILE__) . 'templates/create-enrollment.php';
}

function modal_create_password()
{
    include plugin_dir_path(__FILE__) . 'templates/create-password.php';
}

add_action('wp_ajax_nopriv_create_password', 'create_password');
add_action('wp_ajax_create_password', 'create_password');

function create_password()
{
    // Verifica si el usuario está conectado
    if (is_user_logged_in()) {
        // Obtiene el ID del usuario actual
        global $current_user;

        // Obtiene la contraseña y la confirmación de la contraseña
        $password = sanitize_text_field($_POST['password']);
        $confirm_password = sanitize_text_field($_POST['confirm_password']);

        // Verifica si las contraseñas coinciden
        if ($password === $confirm_password) {
            // Actualiza la contraseña del usuario
            wp_set_password($password, $current_user->ID);

            // Actualiza la columna user_pass_reset en la tabla wp_users
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $wpdb->update($table_students, array('set_password' => 1), array('email' => $current_user->user_email));

            // Cierra la sesión del usuario
            wp_logout();

            $my_account_page = get_permalink(wc_get_page_id('myaccount'));
            wp_send_json(array('success' => true, 'redirect' => $my_account_page));
            exit;
        } else {
            // Envía un mensaje de error
            wp_send_json(array('success' => false, 'error' => 'Las contraseñas no coinciden'));
        }
    } else {
        // Envía un mensaje de error
        wp_send_json(array('success' => false, 'error' => 'Debes estar conectado para cambiar la contraseña'));
    }
}


add_action('wp_ajax_create_enrollment_document', 'create_enrollment_document_callback');
add_action('wp_ajax_nopriv_create_enrollment_document', 'create_enrollment_document_callback');

function create_enrollment_document_callback()
{
    // Check if the request is valid
    if (!isset($_POST['action']) || $_POST['action'] !== 'create_enrollment_document') {
        wp_send_json_error('Invalid request');
    }

    // Get the uploaded file
    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $table_users_signatures = $wpdb->prefix . 'users_signatures';
    $table_students = $wpdb->prefix . 'students';
    $file = $_FILES['document'];
    $signature_parent = json_decode(json_decode('"' . $_POST['signature_parent'] . '"', true));
    $signature_student = json_decode(json_decode('"' . $_POST['signature_student'] . '"', true));
    $partner_user_id = $_POST['partner_user_id'];
    if ($_POST['id_student_to_use']) {
        $student_id = $_POST['id_student_to_use'];
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
        $user_student = get_user_by('email', $student->email);
        $student_user_id = $user_student->ID;
    } else {
        $student_user_id = $_POST['student_user_id'];
    }
    $grade_selected = $_POST['grade_selected'] ?? null;
    $document_id = $_POST['document_id'] ?? 'ENROLLMENT';

    //SAVE THE SIGNATURE OF STUDENT
    if (sizeof($signature_student) > 0) {
        $data = array(
            'user_id' => $student_user_id,
            'signature' => json_encode($signature_student),
            'document_id' => $document_id,
            'grade_selected' => $grade_selected
        );

        $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_users_signatures} WHERE user_id = %d AND document_id = %s", $student_user_id, $document_id));
        if (!$existing_row) {
            $wpdb->insert($table_users_signatures, $data);
        }
    }
    //SAVE THE SIGNATURE OF STUDENT

    //SAVE THE SIGNATURE OF PARENT
    if (isset($signature_parent) && sizeof($signature_parent) > 0) {
        $data = array(
            'user_id' => $partner_user_id,
            'signature' => json_encode($signature_parent),
            'document_id' => $document_id,
            'grade_selected' => $grade_selected
        );

        $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_users_signatures} WHERE user_id = %d AND document_id = %s", $partner_user_id, $document_id));
        if (!$existing_row) {
            $wpdb->insert($table_users_signatures, $data);
        }
    }
    //SAVE THE SIGNATURE OF PARENT

    // SAVE THE DOCUMENT
    if ($file) {
        if ($file['type'] !== 'application/pdf') {
            wp_send_json_error('Invalid file type');
        }

        $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
        if (!$upload || is_wp_error($upload)) {
            wp_send_json_error('Failed to upload file');
        }

        $attachment = array(
            'post_mime_type' => $upload['type'],
            'post_title' => $file['name'],
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        $user_student = get_user_by('id', $student_user_id);
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$user_student->data->user_email}'");
        $wpdb->update($table_student_documents, ['status' => 1, 'attachment_id' => $attach_id, 'upload_at' => date('Y-m-d H:i:s')], ['student_id' => $student->id, 'document_id' => $document_id]);
    }
    // SAVE THE DOCUMENT

    // Return the media ID
    wp_send_json_success(['media_id' => $attach_id, 'upload' => $upload, 'file' => $file]);
}

add_action('wp_ajax_get_student_missing_documents', 'get_student_missing_documents_callback');
add_action('wp_ajax_nopriv_get_student_missing_documents', 'get_student_missing_documents_callback');

function get_student_missing_documents_callback()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_document = $wpdb->prefix . 'student_documents';
    $student_id = $_POST['student_id'];
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
    $documents = $wpdb->get_results("SELECT * FROM {$table_student_document} WHERE student_id = {$student_id} AND attachment_id=0 AND is_visible=1");
    wp_send_json_success(['student' => $student, 'documents' => $documents]);
}

add_action('woocommerce_after_account_orders', 'custom_content_after_orders');
function custom_content_after_orders()
{
    global $wpdb, $current_user;
    $pending_payments = [];
    $payments = [];
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    $partner_id = get_current_user_id();
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");

    // Group payments by student_id and status_id
    $student_payments = [];
    foreach ($students as $key => $student) {
        $payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id = {$student->id} AND status_id = 0 AND cuote != 1");
        if (sizeof($payments) > 0) {
            $student_payments[$student->id] = $payments;
        }
    }

    $pending_orders = customer_pending_orders($current_user->ID);

    include(plugin_dir_path(__FILE__) . 'templates/next-payments.php');
}

add_action('woocommerce_cart_calculate_fees', 'yaycommerce_add_checkout_fee_for_gateway');
function yaycommerce_add_checkout_fee_for_gateway()
{
    if (!isset($_COOKIE['from_webinar']) || empty($_COOKIE['from_webinar'])) {
        $chosen_gateway = WC()->session->get('chosen_payment_method');
        if ($chosen_gateway == 'aes_payment') {
            WC()->cart->add_fee('Bank Transfer Fee', 35);
        }

        if ($chosen_gateway == 'woo_squuad_stripe') {
            $stripe_fee_percentage = 4.5; // 4.5% fee
            $cart_subtotal = WC()->cart->get_subtotal();
            $discount = WC()->cart->get_cart_discount_total();
            $stripe_fee_amount = (($cart_subtotal - $discount) / 100) * $stripe_fee_percentage;
            WC()->cart->add_fee('Credit Card Fee', $stripe_fee_amount);
        }

        if ($chosen_gateway == 'ppcp-gateway') {
            $stripe_fee_percentage = 4.5; // 4.5% fee
            $cart_subtotal = WC()->cart->get_subtotal();
            $discount = WC()->cart->get_cart_discount_total();
            $stripe_fee_amount = (($cart_subtotal - $discount) / 100) * $stripe_fee_percentage;
            WC()->cart->add_fee('PayPal Fee', $stripe_fee_amount);
        }
    }
}
add_action('woocommerce_after_checkout_form', 'yaycommerce_refresh_checkout_on_payment_methods_change');
function yaycommerce_refresh_checkout_on_payment_methods_change()
{
    wc_enqueue_js("
      $( 'form.checkout' ).on( 'change', 'input[name^=\'payment_method\']', function() {
         $('body').trigger('update_checkout');
        });
   ");
}

/**
 * Calcula y aplica tarifas de pago dinámicamente al carrito de WooCommerce o a un pedido existente.
 *
 * Esta función maneja la lógica para aplicar tarifas basadas en el método de pago seleccionado,
 * tanto en la página del carrito/checkout como en una página de pago de pedido dividido.
 *
 * @return void Envía una respuesta JSON con la tarifa calculada y el total pendiente/del carrito.
 */
function loadFeesSplit() {
    // Verificar si es una solicitud AJAX y si el nonce es válido para mayor seguridad.
    // Aunque no está en la función original, es una buena práctica para funciones AJAX.
    // if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
    //     wp_die( 'Acceso directo no permitido.' );
    // }
    // if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'your_custom_nonce_action' ) ) {
    //     wp_die( 'Security check failed.' );
    // }

    $chosen_gateway = isset( $_POST['option'] ) ? sanitize_text_field( $_POST['option'] ) : '';
    $payment_page   = isset( $_POST['payment_page'] ) ? (int) $_POST['payment_page'] : 0;
    $fee            = 0;
    $order          = null;
    $cart           = WC()->cart;

    // --- Manejo de Usuario y Pedido ---
    if ( is_user_logged_in() ) {
        $current_user_id = get_current_user_id();
        $orders = wc_get_orders( array(
            'status'      => 'wc-split-payment', // Asumiendo que 'wc-split-payment' es un estado de pedido personalizado.
            'customer_id' => $current_user_id,
            'limit'       => 1,
            'orderby'     => 'date',
            'order'       => 'DESC', // Obtener el pedido más reciente si hay varios
        ) );

        if ( ! empty( $orders ) ) {
            $order = $orders[0];
        }
    } else {
        // En caso de que el usuario no esté logueado y se acceda a esta función,
        // puedes manejarlo aquí, por ejemplo, redirigiendo o mostrando un error.
        // Para este contexto, simplemente no habrá un objeto $order.
    }

    // --- Lógica para omitir la aplicación de tarifas basada en la cookie ---
    if ( isset( $_COOKIE['from_webinar'] ) && ! empty( $_COOKIE['from_webinar'] ) ) {
        // Si la cookie está presente y no vacía, no aplicamos ninguna tarifa.
        wp_send_json( array(
            'fee'     => (float) number_format( $fee, 2 ),
            'pending' => $order ? (float) $order->get_meta( 'pending_payment' ) : ( $cart ? (float) $cart->get_total( false ) : 0 ),
        ) );
        return;
    }

    // --- Eliminación de tarifas existentes del pedido si aplica ---
    // Esto asegura que no se dupliquen las tarifas si la función se llama varias veces.
    if ( $order ) {
        $fees_to_remove = ['Bank Transfer Fee', 'Credit Card Fee', 'PayPal Fee'];
        $order_modified = false;

        foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
            if ( in_array( $item_fee->get_name(), $fees_to_remove ) ) {
                $order->remove_item( $item_id );
                $order_modified = true;
            }
        }

        if ( $order_modified ) {
            $order->calculate_totals();
            $order->save();
        }
    }

    // --- Determinación de la base para el cálculo de la tarifa ---
    $base_amount_for_fee = 0;
    if ( $payment_page == 0 ) {
        // Calculando para el carrito
        $base_amount_for_fee = $cart->get_subtotal() - $cart->get_cart_discount_total();
    } elseif ( $order ) {
        // Calculando para un pedido existente (pago dividido)
        $pending_payment = (float) $order->get_meta( 'pending_payment' );
        if ( $pending_payment > 0 ) {
            $base_amount_for_fee = $pending_payment;
        } else {
            // Si no hay 'pending_payment' o es 0, usamos el subtotal del pedido
            $base_amount_for_fee = $order->get_subtotal();
        }
    }

    // --- Lógica de aplicación de tarifas basada en el gateway ---
    $fee_percentage = 0;
    $fee_name       = '';
    $is_fixed_fee   = false;

    switch ( $chosen_gateway ) {
        case 'aes_payment':
            $fee            = 35; // Cuota fija
            $fee_name       = 'Bank Transfer Fee';
            $is_fixed_fee   = true;
            break;
        case 'woo_squuad_stripe':
            $fee_percentage = 4.5;
            $fee_name       = 'Credit Card Fee';
            break;
        case 'ppcp-gateway':
            $fee_percentage = 4.5;
            $fee_name       = 'PayPal Fee';
            break;
        default:
            // No se aplica ninguna tarifa si el gateway no es reconocido
            break;
    }

    if ( $is_fixed_fee ) {
        // La tarifa ya está establecida en $fee para pagos fijos.
    } elseif ( $fee_percentage > 0 && $base_amount_for_fee > 0 ) {
        $fee = ( $base_amount_for_fee / 100 ) * $fee_percentage;
    }

    // --- Aplicación de la tarifa al carrito o al pedido ---
    if ( $fee > 0 && ! empty( $fee_name ) ) {
        if ( $payment_page == 0 ) {
            // Aplicar al carrito de WooCommerce
            $cart->add_fee( $fee_name, $fee );
        } elseif ( $order ) {
            // Aplicar al pedido si no es un pago dividido y la tarifa debe ser aplicada
            $split_payment_meta = $order->get_meta('split_payment');
            // La lógica original solo agregaba la tarifa si 'split_payment' != 1.
            // Asegúrate de que esta lógica sea la deseada.
            if ( $split_payment_meta != 1 ) {
                $item_fee_payment_method = new WC_Order_Item_Fee();
                $item_fee_payment_method->set_name( $fee_name );
                $item_fee_payment_method->set_amount( $fee );
                $item_fee_payment_method->set_tax_class( '' );
                $item_fee_payment_method->set_tax_status( 'none' );
                $item_fee_payment_method->set_total( $fee ); // total debe ser igual a amount para tarifas sin impuestos
                $order->add_item( $item_fee_payment_method );
                $order->calculate_totals();
                $order->save();
            }
        }
    }

    // --- Respuesta JSON ---
    $pending_total = 0;
    if ( $order ) {
        $pending_total = (float) $order->get_meta( 'pending_payment' );
        if ( $pending_total === 0.0 && $order->get_total() > 0 ) { // Si pending_payment es 0, usamos el total del pedido si no está vacío
            $pending_total = (float) $order->get_total();
        }
    } elseif ( $cart ) {
        $pending_total = (float) $cart->get_total( false );
    }

    wp_send_json( array(
        'fee'     => (float) number_format( $fee, 2 ),
        'pending' => $pending_total,
    ) );
}

add_action('wp_ajax_nopriv_load_cart_for_split', 'loadFeesSplit');
add_action('wp_ajax_load_cart_for_split', 'loadFeesSplit');

function student_password_Reset($user)
{
    global $wpdb;
    $user_id = $user->get('ID');
    $wpdb->update($wpdb->users, array('user_pass_reset' => 1), array('ID' => $user_id));
}
add_action('password_reset', 'student_password_Reset');

function users_notifications()
{
    global $wpdb, $current_user;
    $table_users_notices = $wpdb->prefix . 'users_notices';
    // Update all notifications to mark them as read
    $wpdb->update(
        $table_users_notices,
        array('read' => 1),
        array('user_id' => $current_user->ID)
    );

    $notices = $wpdb->get_results("SELECT * FROM {$table_users_notices} WHERE user_id = {$current_user->ID} ORDER BY created_at DESC");
    include(plugin_dir_path(__FILE__) . 'templates/users-notifications.php');
}

add_shortcode('users_notifications', 'users_notifications');

function select_payment_aes()
{
    $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
    $countries = get_countries();
    include(plugin_dir_path(__FILE__) . 'templates/select-payment-aes.php');
}

add_shortcode('select_payment_aes', 'select_payment_aes');

function clear_all_cookies($force = false)
{
    foreach ($_COOKIE as $cookie_name => $cookie_value) {
        if ($force) {
            setcookie($cookie_name, '', time() - 3600, '/');
        } else {
            if (!str_contains($cookie_name, 'wordpress') && !str_contains($cookie_name, 'woocommerce') && !str_contains($cookie_name, 'sbjs') && !str_contains($cookie_name, 'stripe')) {
                setcookie($cookie_name, '', time() - 3600, '/');
            }
        }
    }
}

function custom_new_user_notification($send, $user)
{
    $password = wp_generate_password(12);
    $user_student = get_user_by('email', $user->user_email);
    wp_set_password($password, $user_student->ID);

    $content = '';
    $content .= '<div>Welcome ' . $user_student->first_name . ' ' . $user_student->last_name . ' to the American Elite School platform, we have assigned you a password that you can use to log in and change it immediately.</div><br>';
    $content .= '<div>Access information</div>';
    $content .= '<ul>';
    $content .= '<li><strong>Email</strong>: ' . $user->user_email . '</li>';
    $content .= '<li><strong>Password</strong>: ' . $password . '</li>';
    $content .= '</ul>';
    $content .= '<div style="text-align: center"><a href="https://portal.americanelite.school/my-account" target="_blank"><button style="border: 0; background: none; border-color: #43454b; cursor: pointer; text-decoration: none; font-weight: 600; text-shadow: none; display: inline-block; -webkit-appearance: none; padding: 5px 20px !important; text-align: center; background-color: #002fbd !important; border-radius: 20px; color: white !important; font-size: 18px; cursor: pointer !important">My Account</button></a></div><br>';
    $content .= '<div> Additionally, we would like to remind you of the relevant links and contacts: </div>';
    $content .= '<ul>';
    $content .= '<li>Website: <a href="https://americanelite.school/" target="_blank">https://americanelite.school/</a></li>';
    $content .= '<li>Virtual classroom: <a href="https://portal.americanelite.school/" target="_blank">https://portal.americanelite.school/</a></li>';
    $content .= '<li>Contact us: <a href="https://support.americanelite.school" target="_blank">https://support.americanelite.school</a></li>';
    $content .= '</ul>';
    $content .= '<div>Best regards.</div>';

    $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
    $email_user->trigger($user_student, 'WELCOME', $content);
    return false;
}
add_filter('wp_send_new_user_notification_to_user', 'custom_new_user_notification', 10, 2);

function load_current_cut_enrollment()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $current_time = current_time('mysql');
    $code = 'out';
    $cut = 'out';

    // 1. Buscar el periodo académico actual (basado en fechas ajustadas)
    $period_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_academic_periods} 
        WHERE DATE_SUB(`start_date`, INTERVAL 2 MONTH) <= %s 
        AND `end_date` >= %s
        ORDER BY start_date ASC
        LIMIT 1",
        array($current_time, $current_time)
    ));

    if ($period_data) {
        $code = $period_data->code;

        // 2. Buscar cortes activos en el periodo actual (max_date > ahora)
        $cut_query = $wpdb->prepare(
            "SELECT * FROM {$table_academic_periods_cut} 
            WHERE code = %s 
            AND DATE_SUB(`start_date`, INTERVAL 2 MONTH) <= %s 
            AND `end_date` >= %s
            AND `max_date` >= %s
            ORDER BY start_date ASC
            LIMIT 1",
            array(
                $code,
                $current_time,
                $current_time,
                $current_time,
            )
        );
        $active_cut = $wpdb->get_row($cut_query);

        if ($active_cut) {
            $cut = $active_cut->cut;
            return ['cut' => $cut, 'code' => $code];
        }

        // 3. Si no hay cortes activos, buscar en el siguiente periodo académico
        $next_period_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_academic_periods} 
            WHERE code = {$period_data->code_next} 
            ORDER BY start_date ASC 
            LIMIT 1",
            $period_data->start_date
        ));

        if ($next_period_data) {
            // 4. Buscar cortes activos en el siguiente periodo
            $next_cut_query = $wpdb->prepare(
                "SELECT * FROM {$table_academic_periods_cut} 
                WHERE code = %s 
                AND `max_date` >= %s
                ORDER BY start_date ASC
                LIMIT 1",
                array(
                    $next_period_data->code,
                    $current_time
                )
            );
            $next_active_cut = $wpdb->get_row($next_cut_query);

            if ($next_active_cut) {
                $code = $next_period_data->code;
                $cut = $next_active_cut->cut;
            }
        }
    }

    return ['cut' => $cut, 'code' => $code];
}

function load_current_cut()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $current_time = current_time('mysql');
    $code = 'out';
    $cut = 'out';

    $period_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_academic_periods} 
        WHERE `start_date` <= %s 
        AND `end_date` >= %s",
        array($current_time, $current_time)
    ));

    if ($period_data) {
        $code = $period_data->code;

        $period_data_cut = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_academic_periods_cut} 
            WHERE `start_date` <= %s 
            AND `end_date` >= %s",
            array($current_time, $current_time)
        ));

        if ($period_data_cut) {
            $cut = $period_data_cut->cut;
        } else {
            $period_data_cut = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_academic_periods_cut} 
                WHERE `start_date` >= %s",
                array($current_time)
            ));
            $cut = $period_data_cut->cut;
        }
    }

    return ['cut' => $cut, 'code' => $code];
}

function load_last_cut()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $current_time = current_time('mysql');

    $period_data_cut = $wpdb->get_row($wpdb->prepare(
        "SELECT cut, code, end_date 
        FROM {$table_academic_periods_cut} 
        WHERE end_date <= %s 
        ORDER BY end_date DESC 
        LIMIT 1",
        array($current_time)
    ));

    if ($period_data_cut) {
        $cut = $period_data_cut->cut;
        $code = $period_data_cut->code;
        $end_date = $period_data_cut->end_date;
    }

    return ['cut' => $cut, 'code' => $code, 'end_date' => $end_date];
}

add_action('woocommerce_account_orders_endpoint', 'detect_orders_endpoint');
function detect_orders_endpoint()
{
    global $current_user, $wpdb;
    if ($current_user) {
        $order = null;
        $orders = wc_get_orders(array(
            'status' => 'pending',
            'customer_id' => $current_user->ID,
        ));

        if (count($orders) > 0) {
            $order_id = $orders[0]->get_id(); // Get the first pending order ID
            $order = wc_get_order($order_id);
        }

        if ($order) {
            foreach ($order->get_items('fee') as $item_id => $item_fee) {
                if ($item_fee->get_name() === 'Bank Transfer Fee' || $item_fee->get_name() === 'Credit Card Fee' || $item_fee->get_name() === 'PayPal Fee') {
                    $order->remove_item($item_id);
                }
            }

            $order->calculate_totals();
            $order->save();
        }
    }
}

function clear_students_electives()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $wpdb->query("UPDATE {$table_students} SET elective = 0, skip_cut = 1 WHERE elective = 1");
}

// add_filter('woocommerce_available_payment_gateways', 'hide_other_payment_methods', 0);
// function hide_other_payment_methods($available_gateways) {
//     $payment_method_selected = isset($_COOKIE['payment_method_selected']) ? $_COOKIE['payment_method_selected'] : '';
//     if (empty($payment_method_selected)) {
//         return $available_gateways;
//     }
//     if (!empty($payment_method_selected) && isset($available_gateways[$payment_method_selected])) {
//         return [$payment_method_selected => $available_gateways[$payment_method_selected]];
//     }

//     return $available_gateways;
// }

function send_notification_user($user_id, $description, $importance, $type)
{
    if ($user_id) {
        global $wpdb;
        $table_users_notices = $wpdb->prefix . 'users_notices';

        $data = [
            'user_id' => $user_id,
            'message' => $description,
            'importance' => $importance,
            'type_notice' => $type,
        ];

        $wpdb->insert($table_users_notices, $data);
    }
}

/**
 * Verifica si un usuario tiene órdenes con estado "pending-payment".
 *
 * @param int $user_id (opcional) ID del usuario. Si no se proporciona, usa el usuario actual.
 * @return bool
 */
function customer_pending_orders($user_id = null)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    // Si no hay usuario, retornar falso.
    if ($user_id === 0) {
        return false;
    }

    // Argumentos para buscar órdenes del cliente.
    $args = array(
        'customer_id' => $user_id,
        'status' => array('pending', 'on-hold'),
        'limit' => 1, // Solo necesitamos saber si existe al menos 1 orden.
        'return' => 'ids', // Es más eficiente obtener solo los IDs.
    );

    // Obtener las órdenes que coincidan.
    $orders = wc_get_orders($args);

    // Retorna true si se encontró al menos una orden, de lo contrario false.
    return !empty($orders);
}

add_action('template_redirect', 'redirect_admins_from_my_account_to_admin');

function redirect_admins_from_my_account_to_admin()
{
    // Verificar si estamos en la página "My Account" de WooCommerce
    if (is_account_page()) {
        if (is_user_logged_in()) {
            global $current_user;
            $roles = $current_user->roles;

            // Redirigir al panel de administración
            if (!in_array('student', $roles) && !in_array('parent', $roles) && !in_array('teacher', $roles)) {
                wp_redirect(admin_url());
                exit();
            }
        }
    }
}

function upload_file_attchment_edusystem($upload_data, $document_name)
{
    $attachment = array(
        'post_mime_type' => $upload_data['type'],
        'post_title' => $document_name,
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $upload_data['file']);
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload_data['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);
    return $attach_id;
}

function custom_inputs_edusystem($atts)
{
    // Define los atributos por defecto.
    // 'page_slug' es el nombre del atributo que esperarás en el shortcode.
    // 'default_page' es el valor que tendrá si el usuario no especifica 'page_slug'.
    $a = shortcode_atts(array(
        'page_slug' => 'default_page',
        'use_form' => 0,
        'action' => ''
    ), $atts);

    // Ahora puedes acceder al valor del atributo así: $a['page_slug']
    $page_to_get = $a['page_slug'];
    $use_form = $a['use_form'] ?? 0;
    $action_loaded = $a['action'];
    $action = home_url('?action=' . $action_loaded);

    // Llama a tu función para obtener los custom inputs filtrados por el atributo
    $custom_inputs_list = get_custom_inputs_page($page_to_get);

    ob_start(); // Inicia el almacenamiento en búfer de salida para capturar el HTML

    // Incluye tu plantilla. El contenido de la plantilla ahora tendrá acceso a $custom_inputs_list
    // Asumo que 'templates/custom-inputs.php' es donde generas el HTML de los inputs.
    // Necesitas ajustar el path si no es correcto.
    // Ojo: Si 'templates/custom-inputs.php' es el archivo que refactorizamos con la lógica de los inputs dinámicos,
    // entonces su nombre correcto debería ser 'custom-inputs-form-elements.php' o similar para evitar confusiones
    // con el archivo custom-inputs.php que es el controlador.
    // Usaré un placeholder para la ruta, asegúrate de que sea la correcta.
    $template_path = plugin_dir_path(__FILE__) . 'templates/custom-inputs-form-elements.php'; // RUTA AJUSTADA
    if (file_exists($template_path)) {
        include($template_path);
    } else {
        echo '<p style="color:red;">Error: Custom inputs template not found at ' . esc_html($template_path) . '</p>';
    }

    return ob_get_clean(); // Devuelve el HTML capturado
}

add_shortcode('custom_inputs_edusystem', 'custom_inputs_edusystem');

add_action('wp_ajax_load_subprograms_by_program', 'load_subprograms_by_program_callback');
add_action('wp_ajax_nopriv_load_subprograms_by_program', 'load_subprograms_by_program_callback');

function load_subprograms_by_program_callback()
{
    $program_identificator = $_GET['program_id'];
    $subprograms = get_subprogram_by_identificador_program($program_identificator);
    $product_id = get_product_id_by_identificador_program($program_identificator);
    $subprograms_as_array = array_values($subprograms);

    wp_send_json_success(array('subprograms' => $subprograms_as_array, 'product_id' => $product_id));
    exit;
}

add_action('wp_ajax_load_data_program', 'load_data_program_callback');
add_action('wp_ajax_nopriv_load_data_program', 'load_data_program_callback');

function load_data_program_callback()
{
    $program_identificator = $_GET['program_identificator'];
    $careers = get_career_by_program($program_identificator);
    $payment_plans = get_associated_all_plans_by_program_id($program_identificator);

    wp_send_json_success(array('careers' => $careers, 'payment_plans' => $payment_plans));
    exit;
}

add_action('wp_ajax_load_mentions_by_career', 'load_mentions_by_career_callback');
add_action('wp_ajax_nopriv_load_mentions_by_career', 'load_mentions_by_career_callback');

function load_mentions_by_career_callback()
{
    $career_identificator = $_GET['career_identificator'];
    $mentions = get_mentions_by_career($career_identificator);

    wp_send_json_success(array('mentions' => $mentions));
    exit;
}

add_action('wp_ajax_load_grades_by_country', 'load_grades_by_country_callback');
add_action('wp_ajax_nopriv_load_grades_by_country', 'load_grades_by_country_callback');

function load_grades_by_country_callback()
{
    $country = $_POST['country'];
    $grades = get_grades_by_country_code($country);

    wp_send_json_success(array('grades' => $grades));
    exit;
}