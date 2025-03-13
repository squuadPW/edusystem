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

function form_plugin_scripts()
{
    global $wp;
    $version = '1.9.7';
    wp_enqueue_style('dashicons');
    wp_enqueue_style('admin-flatpickr', plugins_url('aes') . '/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('intel-css', plugins_url('aes') . '/public/assets/css/intlTelInput.css');
    wp_enqueue_style('style-public', plugins_url('aes') . '/public/assets/css/style.css', array(), $version, 'all');
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com');
    wp_enqueue_script('admin-flatpickr', plugins_url('aes') . '/public/assets/js/flatpickr.js');
    wp_enqueue_script('masker-js', plugins_url('aes') . '/public/assets/js/vanilla-masker.min.js');
    wp_enqueue_script('intel-js', plugins_url('aes') . '/public/assets/js/intlTelInput.min.js');
    wp_enqueue_script('checkout', plugins_url('aes') . '/public/assets/js/checkout.js');
    wp_enqueue_script('form-register', plugins_url('aes') . '/public/assets/js/form-register.js');
    wp_enqueue_script('int-tel', plugins_url('aes') . '/public/assets/js/int-tel.js');

    // PAYMENTS PARTS
    wp_register_script('payment-parts-update', plugins_url('aes') . '/public/assets/js/payment-parts-update.js', array('jquery'), $version, true);
    wp_localize_script(
        'payment-parts-update',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('payment-parts-update');

    // form-register
    wp_register_script('form-register', plugins_url('aes') . '/public/assets/js/form-register.js', array('jquery'), $version, true);
    wp_localize_script(
        'form-register',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('form-register');

    wp_register_script('create-password', plugins_url('aes') . '/public/assets/js/create-password.js', array('jquery'), $version, true);
    wp_localize_script(
        'create-password',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('create-password');

    wp_register_script('create-enrollment', plugins_url('aes') . '/public/assets/js/create-enrollment.js', array('jquery'), $version, true);
    wp_localize_script(
        'create-enrollment',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('create-enrollment');
    
    if (str_contains(home_url($wp->request), 'califications')) {
        wp_register_script('califications', plugins_url('aes') . '/public/assets/js/califications.js', array('jquery'), $version, true);
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
        wp_enqueue_script('document', plugins_url('aes') . '/public/assets/js/document.js', array('jquery'), $version, true);
        wp_localize_script('document', 'save_documents', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'save_documents'
        ]);
    }

    if (str_contains(home_url($wp->request), 'orders')) {
        wp_enqueue_script('next-payment', plugins_url('aes') . '/public/assets/js/next-payment.js', array('jquery'), $version, true);
        wp_localize_script('next-payment', 'ajax_object', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }

    if (str_contains(home_url($wp->request), 'my-requests')) {
        wp_register_script('requests', plugins_url('aes') . '/public/assets/js/requests.js', array('jquery'), $version, true);
        wp_localize_script(
            'requests',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );
        wp_enqueue_script('requests');
    }

    wp_register_script('student-continue', plugins_url('aes') . '/public/assets/js/student-continue.js', array('jquery'), $version, true);
    wp_localize_script(
        'student-continue',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('student-continue');

    wp_register_script('previous-form', plugins_url('aes') . '/public/assets/js/previous-form.js', array('jquery'), $version, true);
    wp_localize_script(
        'previous-form',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('previous-form');
}

add_action('wp_enqueue_scripts', 'form_plugin_scripts');

function removed_hooks()
{
    remove_action('storefront_footer', 'storefront_credit', 20);
    remove_action('storefront_header', 'storefront_header_cart', 60);
}

add_action('init', 'removed_hooks');

function form_asp_psp()
{
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
    add_action('wp_footer', 'modal_continue_checkout');
    include(plugin_dir_path(__FILE__) . 'templates/asp-psp-registration.php');
}

add_shortcode('form_asp_psp', 'form_asp_psp');

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
        // Redirigir a la URL especificada
        // $url = redirect_to_checkout($_COOKIE['program_id'], $_COOKIE['initial_grade'], false, false, true);
        wp_send_json_success(array('redirect' => 'close'));
        exit;
    } else {
        clear_all_cookies();
        wp_send_json_success(array('success' => true));
        exit;
    }
}

add_action('wp_ajax_send_request', 'send_request_callback');
add_action('wp_ajax_nopriv_send_request', 'send_request_callback');

function send_request_callback()
{
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $table_students = $wpdb->prefix . 'students';
    $table_requests = $wpdb->prefix . 'requests';
    
    $type_id = sanitize_text_field($_POST['type_id']);
    $reason = sanitize_text_field($_POST['reason']);
    $student_id = sanitize_text_field($_POST['student_id']);
    $partner_id = sanitize_text_field($_POST['partner_id']);

    if (!$partner_id) {
        if (in_array('parent', $roles) && !in_array('student', $roles)) {
            $partner_id = $current_user->ID;
        } else if (in_array('parent', $roles) && in_array('student', $roles)) {
            $partner_id = $current_user->ID;
        } else if (!in_array('parent', $roles) && in_array('student', $roles)) {
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
            $partner_id = $student->partner_id;
        }    
    }

    $wpdb->insert($table_requests, [
        'partner_id' => $partner_id,
        'student_id' => $student_id,
        'description' => $reason,
        'type_id' => $type_id,
        'status_id' => 0,
    ]);
    wp_send_json_success(array('success' => true));
    exit;
}

function one_time_payment()
{
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
    include(plugin_dir_path(__FILE__) . 'templates/one-time-payment-registration.php');
}

add_shortcode('one_time_payment', 'one_time_payment');

function custom_registration_pay()
{
    $grades = get_grades();
    include(plugin_dir_path(__FILE__) . 'templates/custom-registration-pay.php');
}

add_shortcode('custom_registration_pay', 'custom_registration_pay');

function form_scholarship_application()
{
    $countries = get_countries();
    $institutes = get_list_institutes_active();
    $grades = get_grades();
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

function woocommerce_checkout_order_created_action($order)
{
    $customer_id = $order->get_customer_id();

    if (!get_user_meta($customer_id, 'status_register', true)) {
        update_user_meta($customer_id, 'status_register', 0);
    }

    if (
        isset($_COOKIE['name_student']) && !empty($_COOKIE['name_student']) &&
        isset($_COOKIE['last_name_student']) && !empty($_COOKIE['last_name_student']) &&
        isset($_COOKIE['birth_date']) && !empty($_COOKIE['birth_date']) &&
        isset($_COOKIE['initial_grade']) && !empty($_COOKIE['initial_grade']) &&
        isset($_COOKIE['program_id']) && !empty($_COOKIE['program_id']) &&
        isset($_COOKIE['email_partner']) && !empty($_COOKIE['email_partner']) &&
        isset($_COOKIE['number_partner']) && !empty($_COOKIE['number_partner'])
    ) {
        $student_id = insert_student($customer_id);
        insert_register_documents($student_id, $_COOKIE['initial_grade']);

        $order->update_meta_data('student_id', $student_id);
        $order->update_meta_data('id_bitrix', $_COOKIE['id_bitrix']);
        $order->save();

        $email_new_student = WC()->mailer()->get_emails()['WC_New_Applicant_Email'];
        $email_new_student->trigger($student_id);

        insert_data_student($order);
        if (isset($_COOKIE['is_scholarship']) && !empty($_COOKIE['is_scholarship'])) {
            save_scholarship();
        }
    }

    if (isset($_COOKIE['is_older']) && !empty($_COOKIE['is_older'])) {
        add_role_user($customer_id, 'parent');
    }

    if (isset($_COOKIE['id_document_parent']) && !empty($_COOKIE['id_document_parent'])) {
        update_user_meta($customer_id, 'id_document', $_COOKIE['id_document_parent']);
    }

    if (isset($_COOKIE['parent_document_type']) && !empty($_COOKIE['parent_document_type'])) {
        update_user_meta($customer_id, 'type_document', $_COOKIE['parent_document_type']);
    }

    if (isset($_COOKIE['birth_date_parent']) && !empty($_COOKIE['birth_date_parent'])) {
        update_user_meta($customer_id, 'birth_date', $_COOKIE['birth_date_parent']);
    }

    if (isset($_COOKIE['gender_parent']) && !empty($_COOKIE['gender_parent'])) {
        update_user_meta($customer_id, 'gender', $_COOKIE['gender_parent']);
    }

    if (isset($_COOKIE['ethnicity_parent']) && !empty($_COOKIE['ethnicity_parent'])) {
        update_user_meta($customer_id, 'ethnicity', $_COOKIE['ethnicity_parent']);
    }

    if (isset($_COOKIE['password']) && !empty($_COOKIE['password'])) {
        global $wpdb;

        $user_data = array(
            'ID' => $customer_id,
            'user_pass' => $_COOKIE['password'],
            'user_pass_reset' => 1
        );

        wp_update_user($user_data);
    }

    //validate cookie and set metadata
    if (isset($_COOKIE['fee_student_id']) && !empty($_COOKIE['fee_student_id'])) {
        $order->update_meta_data('student_id', $_COOKIE['fee_student_id']);
        $order->save();
    }

    set_institute_in_order($order);

    if ($order->get_payment_method_title() != 'Credit Card') {
        send_notification_staff_particular('New payment received for approval', 'Please be informed that we have received a new student that your payment must be manually approved, please login to the platform to confirm.', 3);
    }

    // clear_all_cookies();
}

add_action('woocommerce_checkout_order_created', 'woocommerce_checkout_order_created_action');

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
        'dashboard' => __('Dashboard', 'form-plugin'),
    ];

    // Eliminar enlaces no deseados
    unset($menu_links['downloads']);
    unset($menu_links['edit-address']);
    unset($menu_links['payment-methods']);
    unset($menu_links['customer-logout']);

    // Agregar "Payments" para el rol "parent"
    if (in_array('parent', $roles)) {
        $menu_links['orders'] = __('Payments', 'form-plugin');
    }

    // Agregar "Documents" para el rol "teacher"
    if (in_array('teacher', $roles)) {
        $menu_links['teacher-documents'] = __('Documents', 'form-plugin');
    }

    // Lógica para roles "parent" y "student"
    if (in_array('parent', $roles) || in_array('student', $roles)) {
        // Agregar "Documents" si el usuario tiene el estado de registro correcto
        if (in_array('parent', $roles)) {
            if (get_user_meta($user_id, 'status_register', true) == 1) {
                $menu_links['student-documents'] = __('Documents', 'form-plugin');
            }
        }

        if (in_array('student', $roles)) {
            $student_id = get_user_meta($user_id, 'student_id', true);
            $student = get_student_detail($student_id);
            if (get_user_meta($student->partner_id, 'status_register', true) == 1) {
                $menu_links['student-documents'] = __('Documents', 'form-plugin');
            }
        }

        // Agregar "Student Information"
        $menu_links['student'] = __('Student Information', 'aes');

        // Agregar "Califications"
        $menu_links['califications'] = __('Califications', 'form-plugin');

        // Agregar "Requests"
        $menu_links['my-requests'] = __('Requests', 'form-plugin');

        // Agregar "Tickets"
        $menu_links['my-tickets'] = __('Tickets', 'form-plugin');
    }

    // Agregar "Account Details" al final
    $menu_links['edit-account'] = __('Account Details', 'woocommerce');

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

function auto_complete_free_orders($order_id) {
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
    $columns['order-number'] = __('Payment ID', 'form-plugin');
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
            $color = '#002fbd !important';
            $count = "";
        }
        if ($args->theme_location == 'primary') {
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/notifications"><span style="vertical-align: baseline; font-size: 28px; width: 26px; color: ' . $color . '; cursor: pointer;" class="dashicons dashicons-bell"></span>' . $count . '</a></li>';
        } else {
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/notifications">' . __('Notifications', 'form-plugin') . '</a></li>';
        }


        $birthday = get_user_meta($current_user->ID, 'birth_date', true);
        $age = floor((time() - strtotime($birthday)) / 31556926);
        if ($age >= 18) {
            $items .= '<li><a href="' . home_url() . '">' . __('New applicant', 'form-plugin') . '</a></li>';
        }

        $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '">' . __('Dashboard', 'form-plugin') . '</a></li>';
        if ($args->theme_location != 'primary') {
            if ($age >= 18) {
                $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/orders">' . __('Payments', 'form-plugin') . '</a></li>';
            }
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student">' . __('Students information', 'form-plugin') . '</a></li>';
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents">' . __('Documents', 'form-plugin') . '</a></li>';
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/edit-account">' . __('Account details', 'form-plugin') . '</a></li>';
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/my-tickets">' . __('My tickets', 'form-plugin') . '</a></li>';
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/my-requests">' . __('My requests', 'form-plugin') . '</a></li>';
            $items .= '<li><a href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '/califications">' . __('Califications', 'form-plugin') . '</a></li>';
        }

        $logout_link = wp_logout_url(get_home_url());
        $items .= '<li>
            <div class="logout-button-container" >
                <div style="padding: 5px 20px !important; text-align: start; border-radius: 20px; color: white !important; font-size: 14px;">
                ' . $current_user->first_name . ' ' . $current_user->last_name . ' 
                </div>
                <div>
                    <a class="button-primary" style="font-size:14px;" href="' . $logout_link . '">' . __('Log out', 'form-plugin') . '</a>
                </div>
            </div>
        </li>';
    } elseif (!is_user_logged_in()) {
        $items .= '<li style="margin-bottom: 17px; !important"><a class="button-primary" style="font-size:14px;" href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '">' . __('Log in', 'form-plugin') . '</a></li>';
    }

    return $items;
}

function status_changed_payment($order_id, $old_status, $new_status)
{
    global $wpdb, $current_user;
    $order = wc_get_order($order_id);
    $customer_id = $order->get_customer_id();
    $status_register = get_user_meta($customer_id, 'status_register', true);

    if ($order->get_status() != 'failed' && $order->get_status() != 'pending') {
        clear_all_cookies();
    }

    if ($order->get_status() == 'completed') {
        status_order_completed($order, $order_id, $customer_id, $status_register);
    } else {
        status_order_not_completed($order, $order_id, $customer_id, $status_register);
    }
}


add_action('woocommerce_order_status_changed', 'status_changed_payment', 10, 3);

function status_order_completed($order, $order_id, $customer_id, $status_register)
{
    global $wpdb, $current_user;
    $table_student_payment = $wpdb->prefix . 'student_payments';

    if (isset($status_register)) {
        update_user_meta($customer_id, 'status_register', 1);
    }

    update_user_meta($customer_id, 'cuote_pending', 0);

    if ($order->get_meta('student_id')) {
        $student_id = $order->get_meta('student_id');
        create_user_student($student_id);

        $query = $wpdb->prepare("
            UPDATE {$table_student_payment} AS main
            INNER JOIN (
                SELECT product_id, MIN(id) AS min_id
                FROM {$table_student_payment}
                WHERE student_id = %d
                AND status_id = 0
                GROUP BY product_id
            ) AS sub ON main.product_id = sub.product_id AND main.id = sub.min_id
            SET main.status_id = 1, main.order_id = %d, main.date_payment = CURDATE()
        ", $student_id, $order_id);
        
        $wpdb->query($query);

        if ($order->get_meta('id_bitrix')) {
            sendOrderbitrix(floatval($order->get_meta('id_bitrix')), $order_id, $order->get_status());
        }

        $student = get_student_detail($student_id);
        if ($student->status_id < 2) {
            update_status_student($student_id, 1);
            $email_request_documents = WC()->mailer()->get_emails()['WC_Request_Documents_Email'];
            $email_request_documents->trigger($student_id);
        }

        set_max_date_student($student_id);

        return null;
    }
}

function status_order_not_completed($order, $order_id, $customer_id, $status_register)
{
    global $wpdb, $current_user;
    $table_student_payment = $wpdb->prefix . 'student_payments';

    if ($status_register != 1 && $status_register != '1') {
        update_user_meta($customer_id, 'status_register', 0);
    }


    // FOR PROGRAM PAYMENT (AES PROGRAM)
    $items = $order->get_items();
    $date = new DateTime();
    $date = $date->format('Y-m-d');
    $student_id = $order->get_meta('student_id');

    foreach ($items as $item) {
        $cuotes = 1;
        $date_calc = '';
        $product_id = $item->get_product_id(); // Get the product ID
        $variation_id = $item->get_variation_id(); // Get the variation ID
        $is_variable = $item->get_product()->is_type('variation');
        $price = $item->get_product()->get_price($variation_id); // Get the price of the selected variation
        $discount = wc_get_order_item_meta($item->get_id(), '_line_subtotal') - wc_get_order_item_meta($item->get_id(), '_line_total');
        $price -= $discount;
        $exist = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = {$product_id} and order_id = {$order_id}");
        if (!$exist) {

            if ($is_variable) {
                $product = $item->get_product();
                $variation_attributes = $product->get_variation_attributes();
                foreach ($variation_attributes as $attribute_taxonomy => $term_slug) {
                    $taxonomy = str_replace('attribute_', '', $attribute_taxonomy);
                    $attribute_name = wc_attribute_label($taxonomy, $product);
                    if (taxonomy_exists($taxonomy)) {
                        $attribute_value = get_term_by('slug', $term_slug, $taxonomy)->name;
                    } else {
                        $attribute_value = $term_slug;
                    }
                }

                switch ($attribute_value) {
                    case 'Annual':
                        $date_calc = '+1 year';
                        break;
                    case 'Semiannual':
                        $date_calc = '+6 months';
                        break;
                }

                $cuotes = $product->get_meta('num_cuotes_text') ? $product->get_meta('num_cuotes_text') : 1;
            }

            for ($i = 0; $i < $cuotes; $i++) {
                $date = $i > 0 ? date('Y-m-d', strtotime($date_calc, strtotime($date))) : $date;
                $data = array(
                    'status_id' => 0,
                    'order_id' => $order_id,
                    'student_id' => $student_id,
                    'product_id' => $product_id,
                    'variation_id' => $variation_id,
                    'amount' => $price,
                    'type_payment' => $cuotes > 1 ? 1 : 2,
                    'cuote' => ($i + 1),
                    'num_cuotes' => $cuotes,
                    'date_payment' => $i == 0 ? date('Y-m-d') : null,
                    'date_next_payment' => $date,
                );

                // Busca si ya existe una fila con los mismos valores
                $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}student_payments WHERE student_id = %d AND product_id = %d AND variation_id = %d AND cuote = %d", $student_id, $product_id, $variation_id, ($i + 1)));

                if (!$existing_row) {
                    // Si no se encuentra ninguna fila, inserta la nueva fila
                    $wpdb->insert($wpdb->prefix . 'student_payments', $data);
                }
            }
        }
    }
    // FOR PROGRAM PAYMENT (AES PROGRAM)

    // FEE DE INSCRIPCION
    $product = null;
    foreach ($items as $item) {
        $product = $item->get_product()->get_id() == AES_FEE_INSCRIPTION ? $item->get_product() : $product;
    }
    if (isset($product)) {
        $roles = $current_user->roles;
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $table_student_payment = $wpdb->prefix . 'student_payments';
        $access_virtual = true;
        $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");

        if ($documents_student) {
            foreach ($documents_student as $document) {
                if ($document->status != 5) {
                    $access_virtual = false;
                }
            }

            // VERIFICAR FEE DE INSCRIPCION
            $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = " . AES_FEE_INSCRIPTION);
            // VERIFICAR FEE DE INSCRIPCION

            //virtual classroom
            if ($access_virtual && isset($paid)) {
                $table_name = $wpdb->prefix . 'students'; // assuming the table name is "wp_students"
                $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $student_id));
                $type_document = array(
                    'identification_document' => 1,
                    'passport' => 2,
                    'ssn' => 4,
                )[$student->type_document];

                $files_to_send = array();

                $type_document = '';
                switch ($student->type_document) {
                    case 'identification_document':
                        $type_document = 1;
                        break;
                    case 'passport':
                        $type_document = 2;
                        break;
                    case 'ssn':
                        $type_document = 4;
                        break;
                }

                $type_document_re = '';
                if (get_user_meta($student->partner_id, 'type_document', true)) {
                    switch (get_user_meta($student->partner_id, 'type_document', true)) {
                        case 'identification_document':
                            $type_document_re = 1;
                            break;
                        case 'passport':
                            $type_document_re = 2;
                            break;
                        case 'ssn':
                            $type_document_re = 4;
                            break;
                    }
                } else {
                    $type_document_re = 1;
                }


                $gender = '';
                switch ($student->gender) {
                    case 'male':
                        $gender = 'M';
                        break;
                    case 'female':
                        $gender = 'F';
                        break;
                }


                $gender_re = '';
                if (get_user_meta($student->partner_id, 'gender', true)) {
                    switch (get_user_meta($student->partner_id, 'gender', true)) {
                        case 'male':
                            $gender_re = 'M';
                            break;
                        case 'female':
                            $gender_re = 'F';
                            break;
                    }
                } else {
                    $gender_re = 'M';
                }

                $grade = '';
                switch ($student->grade_id) {
                    case 1:
                        $grade = 9;
                        break;
                    case 2:
                        $grade = 10;
                        break;
                    case 3:
                        $grade = 11;
                        break;
                    case 4:
                        $grade = 12;
                        break;
                }
                $user_partner = get_user_by('id', $student->partner_id);
                $fields_to_send = array(
                    // DATOS DEL ESTUDIANTE
                    'id_document' => $student->id_document,
                    'type_document' => $type_document,
                    'firstname' => $student->name . ' ' . $student->middle_name,
                    'lastname' => $student->last_name . ' ' . $student->middle_last_name,
                    'birth_date' => $student->birth_date,
                    'phone' => $student->phone,
                    'email' => $student->email,
                    'etnia' => $student->ethnicity,
                    'grade' => $grade,
                    'gender' => $gender,
                    'cod_period' => $student->academic_period,

                    // PADRE
                    'id_document_re' => get_user_meta($student->partner_id, 'id_document', true) ? get_user_meta($student->partner_id, 'id_document', true) : '000000',
                    'type_document_re' => $type_document_re,
                    'firstname_re' => get_user_meta($student->partner_id, 'first_name', true),
                    'lastname_re' => get_user_meta($student->partner_id, 'last_name', true),
                    'birth_date_re' => get_user_meta($student->partner_id, 'birth_date', true),
                    'phone_re' => get_user_meta($student->partner_id, 'billing_phone', true),
                    'email_re' => $user_partner->user_email,
                    'gender_re' => $gender_re,

                    'cod_program' => AES_PROGRAM_ID,
                    'cod_tip' => AES_TYPE_PROGRAM,
                    'address' => get_user_meta($student->partner_id, 'billing_address_1', true),
                    'country' => get_user_meta($student->partner_id, 'billing_country', true),
                    'city' => get_user_meta($student->partner_id, 'billing_city', true),
                    'postal_code' => get_user_meta($student->partner_id, 'billing_postcode', true) ? get_user_meta($student->partner_id, 'billing_postcode', true) : '-',
                );

                $all_documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
                $documents_to_send = [];
                foreach ($all_documents_student as $document) {
                    if ($document->attachment_id) {
                        array_push($documents_to_send, $document);
                    }
                }

                foreach ($documents_to_send as $key => $doc) {
                    $id_requisito = $wpdb->get_var($wpdb->prepare("SELECT id_requisito FROM {$wpdb->prefix}documents WHERE name = %s", $doc->document_id));
                    $attachment_id = $doc->attachment_id;
                    $attachment_path = get_attached_file($attachment_id);
                    if ($attachment_path) {
                        $file_name = basename($attachment_path);
                        $file_type = mime_content_type($attachment_path);

                        $files_to_send[] = array(
                            'file' => curl_file_create($attachment_path, $file_type, $file_name),
                            'id_requisito' => $id_requisito
                        );
                    }
                }

                create_user_laravel(array_merge($fields_to_send, array('files' => $files_to_send)));

                if ($order->get_meta('id_bitrix')) {
                    sendOrderbitrix(floatval($order->get_meta('id_bitrix')), $order_id, $order->get_status());
                }

                update_status_student($student_id, 2);

                if (in_array('parent', $roles) && !in_array('student', $roles)) {
                    create_user_student($student_id);
                }

                $exist = is_search_student_by_email($student_id);

                if (!$exist) {
                    create_user_moodle($student_id);
                } else {
                    $table_students = $wpdb->prefix . 'students';
                    $wpdb->update($table_students, ['moodle_student_id' => $exist[0]['id']], ['id' => $student_id]);

                    $is_exist_password = is_password_user_moodle($student_id);

                    if (!$is_exist_password) {

                        $password = generate_password_user();
                        $wpdb->update($table_students, ['moodle_password' => $password], ['id' => $student_id]);
                        change_password_user_moodle($student_id);
                    }
                }
            }
        }
    }
    // FEE DE INSCRIPCION
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

add_action('woocommerce_after_checkout_billing_form', 'payments_parts');

function split_payment() {
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

function totals_split($order_id)
{
    echo '1uiasdi0;';
}
add_action('woocommerce_pay_order_after_totals', 'totals_split');

function payments_parts()
{
    include(plugin_dir_path(__FILE__) . 'templates/payment-parts.php');
}

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
        // Remover el cupón con la clave "fee_inscription" de la matriz $applied_coupons
        $applied_coupons = array_diff($applied_coupons, array(strtolower(get_option('offer_complete'))));

        $offer_quote = strtolower(get_option('offer_quote'));

        if (!empty($offer_quote)) {
            // Verifica si el valor ya existe en el array
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
    $id = AES_FEE_INSCRIPTION;

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
    $id = AES_FEE_INSCRIPTION;
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
    foreach ($applied_coupons as $key => $coupon) {
        if ($coupon == 'latam scholarship') {
            $has_scholarship = true;
        }
    }
    ?>
    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-5 mb-5" style="text-align:center;">
        <?php if ($has_scholarship): ?>
                                <button id="apply-scholarship-btn" type="button"
                                    disabled><?php echo (isset($_COOKIE['from_webinar']) && !empty($_COOKIE['from_webinar'])) ? 'Special webinar offer already applied' : 'Scholarship already applied' ?></button>
        <?php else: ?>
                                <button id="apply-scholarship-btn"
                                    type="button"><?php echo (isset($_COOKIE['from_webinar']) && !empty($_COOKIE['from_webinar'])) ? 'Special webinar offer' : 'Activate scholarship' ?></button>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();
    echo $html;
    wp_die();
}

add_action('wp_ajax_nopriv_apply_scholarship', 'apply_scholarship');
add_action('wp_ajax_apply_scholarship', 'apply_scholarship');

function apply_scholarship()
{
    global $woocommerce;
    $cart = $woocommerce->cart;

    $coupon_code = 'Latam Scholarship';
    $cart->apply_coupon($coupon_code);

    // Calculate totals
    $woocommerce->cart->calculate_totals();
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


add_filter('woocommerce_account_dashboard', 'fee_inscription_button', 0);
function fee_inscription_button()
{
    // VERIFICAR FEE DE INSCRIPCION
    global $wpdb;
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    $partner_id = get_current_user_id();
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
    foreach ($students as $key => $student) {
        $paid = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id = {$student->id} and product_id = " . AES_FEE_INSCRIPTION);
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

function redirect_after_login($redirect_to, $user) {
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

    if(get_option('auto_enroll_elective')) {
        $offer = get_offer_filtered($subject->id, $code, $cut);
        $enrollments = [];
        $enrollments = array_merge($enrollments, courses_enroll_student($projection->student_id, [(int) $offer->moodle_course_id]));
        enroll_student($enrollments);
    } else {
        update_count_moodle_pending();
    }

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

add_action('wp', 'verificar_contraseña');
function verificar_contraseña()
{
    // Verifica si el usuario está en la página de "Mi cuenta"
    if (is_account_page()) {
        // Verifica si el usuario está conectado
        if (is_user_logged_in()) {
            global $current_user, $wpdb;
            $roles = $current_user->roles;
            $table_students = $wpdb->prefix . 'students';

            $students = [];
            if (in_array('student', $roles)) {
                $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
            } else if (in_array('parent', $roles)) {
                $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");
            }

            if ((count($students) == 0 && !get_user_meta($current_user->ID, 'pay_application_password')) && (in_array('student', $roles) || in_array('parent', $roles))) {
                add_action('wp_footer', 'modal_fill_info');
            } else if (count($students) > 0) {
                update_user_meta($current_user->ID, 'pay_application_password', 0);
                // Obtiene las órdenes con estado "pending payment" del usuario actual
                $orders = wc_get_orders(array(
                    'status' => 'pending',
                    'customer_id' => $current_user->ID,
                ));

                if (!empty($orders)) {
                    // Redirige al checkout con la orden pendiente de pago
                    $order_id = $orders[0]->get_id(); // Get the first pending order ID
                    $order = wc_get_order($order_id);
                    if ($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1 && !$order->get_meta('split_complete')) {
                        $checkout_url = wc_get_checkout_url() . 'order-pay/' . $order_id . '/?pay_for_order=true&key=' . $orders[0]->get_order_key();
                        $split_method = json_decode($order->get_meta('split_method'));
                        $total = 0.00;
                        $total_gross = 0.00;
                        foreach ($split_method as $key => $split) {
                            $total += $split->amount;
                            $total_gross += $split->gross_total;
                        }

                        $total_paid_meta = $order->get_meta('total_paid');
                        if ($total_paid_meta) {
                            $order->update_meta_data('total_paid', $total);
                        } else {
                            $order->add_meta_data('total_paid', $total);
                        }

                        $total_paid_meta = $order->get_meta('total_paid_gross');
                        if ($total_paid_meta) {
                            $order->update_meta_data('total_paid_gross', $total_gross);
                        } else {
                            $order->add_meta_data('total_paid_gross', $total_gross);
                        }

                        $pending_payment_meta = $order->get_meta('pending_payment');
                        if ($pending_payment_meta) {
                            $order->update_meta_data('pending_payment', ($order->get_total() - $total));
                        } else {
                            $order->add_meta_data('pending_payment', ($order->get_total() - $total));
                        }

                        // $order->set_total($order->get_total() - $total); // Set the total amount of the order
                        $complete = false;
                        $pending_payment = $order->get_meta('pending_payment');
                        if ($pending_payment <= 0) {
                            $split_method_updated = $order->get_meta('split_method');
                            $split_method_updated = json_decode($split_method_updated);
                            $on_hold_found = false;
                            foreach ($split_method_updated as $method) {
                                if ($method->status === 'on-hold') {
                                    $on_hold_found = true;
                                    break;
                                }
                            }

                            if (!$on_hold_found) {
                                $order->update_status('completed');
                            } else {
                                $order->update_status('on-hold');
                            }
                            $complete = true;
                        }
                        $order->save();

                        if (!$complete && !$order->get_meta('one_time_payment')) {
                            // wc_add_notice(__('Your payment has been received. Please continue with the remaining amount.', 'your-text-domain'), 'success');
                            wp_redirect($checkout_url);
                            exit;
                        }

                        if ($order->get_meta('one_time_payment')) {
                            $order->update_status('completed');
                            $order->save();

                            $order->update_status('pending-payment');
                            $order->save();
                        }
                    }
                }

                // Obtiene el ID del usuario actual y creamos la contrasena
                $table_user_signatures = $wpdb->prefix . 'users_signatures';
                $table_student_documents = $wpdb->prefix . 'student_documents';
                $table_student_payments = $wpdb->prefix . 'student_payments';
                $user_enrollment_signature = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$current_user->ID} and document_id = 'ENROLLMENT' ORDER BY id DESC");
                $user_missing_signature = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$current_user->ID} and document_id = 'MISSING DOCUMENT' ORDER BY id DESC");
                $user_take_elective = false;
                $set_password = false;

                if (in_array('student', $roles)) {
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
                    $pending_payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id={$student->id} AND status_id = 0 AND date_next_payment <= NOW()");
                    $document_was_created = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} and document_id = 'ENROLLMENT' ORDER BY id DESC");
                    $user_take_elective = $student->elective;

                    if (!$student->set_password) {
                        $set_password = true;
                    }

                    $user_enrollment_signature_other = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$student->partner_id} and document_id = 'ENROLLMENT' ORDER BY id DESC");
                    $user_missing_signature_other = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$student->partner_id} and document_id = 'MISSING DOCUMENT' ORDER BY id DESC");
                } else if (in_array('parent', $roles)) {
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id='{$current_user->ID}'");
                    $pending_payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id={$student->id} AND status_id = 0 AND date_next_payment <= NOW()");
                    $document_was_created = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} and document_id = 'ENROLLMENT' ORDER BY id DESC");
                    $user_take_elective = $student->elective;

                    $user_student = get_user_by('email', $student->email);
                    $user_enrollment_signature_other = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$user_student->ID} and document_id = 'ENROLLMENT' ORDER BY id DESC");
                    $user_missing_signature_other = $wpdb->get_row("SELECT * FROM {$table_user_signatures} WHERE user_id={$user_student->ID} and document_id = 'MISSING DOCUMENT' ORDER BY id DESC");
                }

                if (!$set_password) {
                    if ($user_take_elective == 1) {
                        add_action('wp_footer', 'modal_take_elective');
                    } else {
                        if ($document_was_created && ((!isset($user_enrollment_signature) || !isset($user_enrollment_signature_other)) && !$pending_payments) && (in_array('student', $roles, true) || in_array('parent', $roles, true))) {
                            add_action('wp_footer', 'modal_enrollment_student');
                        } else if ($document_was_created && (isset($user_enrollment_signature) && (!isset($user_missing_signature) || !isset($user_missing_signature_other)) && !$pending_payments) && (in_array('student', $roles, true) || in_array('parent', $roles, true))) {
                            add_action('wp_footer', 'modal_missing_student');
                        }
                    }
                } else {
                    add_action('wp_footer', 'modal_create_password');
                }
            }
        }
    }
}

function modal_take_elective()
{
    global $wpdb, $current_user;

    $roles = $current_user->roles;
    if (!in_array('student', $roles)) {
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
    $conditions[] = "is_elective = 1";
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
        $birth_date = get_user_meta($student_id, 'birth_date', true);
        $birth_date_timestamp = strtotime($birth_date);
        $current_timestamp = time();
        $age = floor(($current_timestamp - $birth_date_timestamp) / 31536000); // 31536000 es el número de segundos en un año
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
        'student_created_at' => $student->created_at,
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

function loadFeesSplit()
{
    global $current_user, $wpdb;
    $orders = wc_get_orders(array(
        'status' => 'pending',
        'customer_id' => $current_user->ID,
    ));

    if (count($orders) > 0) {
        $order_id = $orders[0]->get_id(); // Get the first pending order ID
        $order = wc_get_order($order_id);
    }

    $cart = WC()->cart;
    $chosen_gateway = $_POST['option'];
    $payment_page = $_POST['payment_page'];
    $fee = 0;

    if (!isset($_COOKIE['from_webinar']) || empty($_COOKIE['from_webinar'])) {

        if ($order) {
            foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
                if ( $item_fee->get_name() === 'Bank Transfer Fee' || $item_fee->get_name() === 'Credit Card Fee' ) {
                    $order->remove_item( $item_id );
                }
            }

            $order->calculate_totals();
            $order->save();
        }

        if ($chosen_gateway == 'aes_payment') {
            $fee = 35;
            if ($payment_page == 0) {
                $cart->add_fee('Bank Transfer Fee', $fee);
            } else {
                $cart_subtotal = (float) $order->get_meta('pending_payment');
                $is_total = false;
                if (!$cart_subtotal || $cart_subtotal == 0) {
                    $is_total = true;
                    $cart_subtotal = $order->get_subtotal();
                }

                if ($is_total) {
                    $item_fee_payment_method = new WC_Order_Item_Fee();
                    $item_fee_payment_method->set_name("Bank Transfer Fee");
                    $item_fee_payment_method->set_amount($fee);
                    $item_fee_payment_method->set_tax_class('');
                    $item_fee_payment_method->set_tax_status('none');
                    $item_fee_payment_method->set_total($fee);
                    $order->add_item($item_fee_payment_method);
                    $order->calculate_totals();
                    $order->save();
                }
            }
        } 
        
        if ($chosen_gateway == 'woo_squuad_stripe') {
            if ($payment_page == 0) {
                $stripe_fee_percentage = 4.5; // 4.5% fee
                $cart_subtotal = $cart->get_subtotal();
                $discount = $cart->get_cart_discount_total();
                $stripe_fee_amount = (($cart_subtotal - $discount) / 100) * $stripe_fee_percentage;
                $fee = $stripe_fee_amount;

                // Solo agregar fee si es mayor que 0
                if ($stripe_fee_amount > 0) {
                    $cart->add_fee('Credit Card Fee', $stripe_fee_amount);
                }
            } else {
                $stripe_fee_percentage = 4.5; // 4.5% fee
                $cart_subtotal = (float) $order->get_meta('pending_payment');
                $is_total = false;
                if (!$cart_subtotal || $cart_subtotal == 0) {
                    $is_total = true;
                    $cart_subtotal = $order->get_subtotal();
                }
                $stripe_fee_amount = ($cart_subtotal / 100) * $stripe_fee_percentage;
                $fee = $stripe_fee_amount;

                if ($is_total) {
                    $item_fee_payment_method = new WC_Order_Item_Fee();
                    $item_fee_payment_method->set_name("Credit Card Fee");
                    $item_fee_payment_method->set_amount($fee);
                    $item_fee_payment_method->set_tax_class('');
                    $item_fee_payment_method->set_tax_status('none');
                    $item_fee_payment_method->set_total($fee);
                    $order->add_item($item_fee_payment_method);
                    $order->calculate_totals();
                    $order->save();
                }
            }
        }
    }

    wp_send_json(array('fee' => (float) number_format($fee, 2), 'pending' => isset($order) ? (float) $order->get_meta('pending_payment') : 0));
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
    $content .= '<div>Welcome to the American Elite School platform, we have assigned you a password that you can use to log in and change it immediately.</div><br>';
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
    $code = 'noperiod';
    $cut = 'nocut';

    $period_data = $wpdb->get_row($wpdb->prepare(
        "
        SELECT * FROM {$table_academic_periods} 
        WHERE DATE_SUB(`start_date`, INTERVAL 1 MONTH) <= %s 
        AND DATE_ADD(`end_date`, INTERVAL -1 MONTH) >= %s",
        array($current_time, $current_time)
    ));

    if ($period_data) {
        $code = $period_data->code;

        $period_data_cut = $wpdb->get_row($wpdb->prepare(
            "
            SELECT * FROM {$table_academic_periods_cut} 
            WHERE DATE_SUB(`start_date`, INTERVAL 1 MONTH) <= %s 
            AND DATE_ADD(`end_date`, INTERVAL -1 MONTH) >= %s
            AND `max_date` > %s",
            array($current_time, $current_time, $current_time)
        ));

        if ($period_data_cut) {
            $cut = $period_data_cut->cut;
        } else {
            $period_data_cut = $wpdb->get_row($wpdb->prepare(
                "
                SELECT * FROM {$table_academic_periods_cut} 
                WHERE `start_date` >= %s
                AND `max_date` > %s",
                array($current_time, $current_time, $current_time)
            ));
            $cut = $period_data_cut->cut;
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
    $code = 'noperiod';
    $cut = 'nocut';

    $period_data = $wpdb->get_row($wpdb->prepare(
        "
        SELECT * FROM {$table_academic_periods} 
        WHERE `start_date` <= %s 
        AND `end_date` >= %s",
        array($current_time, $current_time)
    ));

    if ($period_data) {
        $code = $period_data->code;

        $period_data_cut = $wpdb->get_row($wpdb->prepare(
            "
            SELECT * FROM {$table_academic_periods_cut} 
            WHERE `start_date` <= %s 
            AND `end_date` >= %s",
            array($current_time, $current_time)
        ));

        if ($period_data_cut) {
            $cut = $period_data_cut->cut;
        } else {
            $period_data_cut = $wpdb->get_row($wpdb->prepare(
                "
                SELECT * FROM {$table_academic_periods_cut} 
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
    $code = 'noperiod';
    $cut = 'nocut';

    $period_data = $wpdb->get_row($wpdb->prepare(
        "
        SELECT * FROM {$table_academic_periods} 
        WHERE `start_date` <= %s 
        AND `end_date` >= %s",
        array($current_time, $current_time)
    ));

    if ($period_data) {
        $code = $period_data->code;

        $period_data_cut = $wpdb->get_row($wpdb->prepare(
            "
            SELECT * FROM {$table_academic_periods_cut} 
            WHERE `end_date` <= %s ORDER BY id DESC",
            array($current_time)
        ));

        if ($period_data_cut) {
            $cut = $period_data_cut->cut;
        } else {
            $period_data_cut = $wpdb->get_row($wpdb->prepare(
                "
                SELECT * FROM {$table_academic_periods_cut} 
                WHERE `start_date` >= %s",
                array($current_time)
            ));
            $cut = $period_data_cut->cut;
        }
    }

    return ['cut' => $cut, 'code' => $code];
}

add_action('woocommerce_account_orders_endpoint', 'detect_orders_endpoint');
function detect_orders_endpoint() {
    global $current_user, $wpdb;
    if ($current_user) {
        $orders = wc_get_orders(array(
            'status' => 'pending',
            'customer_id' => $current_user->ID,
        ));
    
        if (count($orders) > 0) {
            $order_id = $orders[0]->get_id(); // Get the first pending order ID
            $order = wc_get_order($order_id);
        }
    
        if ($order) {
            foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
                if ( $item_fee->get_name() === 'Bank Transfer Fee' || $item_fee->get_name() === 'Credit Card Fee' ) {
                    $order->remove_item( $item_id );
                }
            }
    
            $order->calculate_totals();
            $order->save();
        }
    }
}

function clear_students_electives() {
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

function send_notification_user($user_id, $description, $importance, $type) {
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

/**
 * Verifica si un usuario tiene órdenes con estado "pending-payment".
 *
 * @param int $user_id (opcional) ID del usuario. Si no se proporciona, usa el usuario actual.
 * @return bool
 */
function customer_pending_orders($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    // Si no hay usuario, retornar falso
    if ($user_id === 0) {
        return false;
    }

    // Argumentos para buscar órdenes
    $args = array(
        'customer_id' => $user_id,
        'status'      => 'pending',
        'limit'       => 1, // Solo necesitamos al menos 1 orden
        'return'      => 'ids',
    );

    // Obtener órdenes
    $orders = wc_get_orders($args);
    return !empty($orders);
}
