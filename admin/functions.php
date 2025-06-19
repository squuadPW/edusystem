<?php

require plugin_dir_path(__FILE__) . 'roles.php';
require plugin_dir_path(__FILE__) . 'admission.php';
require plugin_dir_path(__FILE__) . 'report.php';
require plugin_dir_path(__FILE__) . 'payments.php';
require plugin_dir_path(__FILE__) . 'scholarships.php';
require plugin_dir_path(__FILE__) . 'academic_periods.php';
require plugin_dir_path(__FILE__) . 'school_subjects.php';
require plugin_dir_path(__FILE__) . 'academic-projection.php';
require plugin_dir_path(__FILE__) . 'teachers.php';
require plugin_dir_path(__FILE__) . 'enrollments.php';
require plugin_dir_path(__FILE__) . 'configuration-options.php';
require plugin_dir_path(__FILE__) . 'send-email.php';
require plugin_dir_path(__FILE__) . 'send-notification.php';
require plugin_dir_path(__FILE__) . 'staff.php';
require plugin_dir_path(__FILE__) . 'institute.php';
require plugin_dir_path(__FILE__) . 'alliances.php';
require plugin_dir_path(__FILE__) . 'departments.php';
require plugin_dir_path(__FILE__) . 'bitrix/sdk/crest.php';
require plugin_dir_path(__FILE__) . 'emails/function.php';
require plugin_dir_path(__FILE__) . 'user.php';
require plugin_dir_path(__FILE__) . 'moodle/rest.php';
require plugin_dir_path(__FILE__) . 'moodle.php';
require plugin_dir_path(__FILE__) . 'laravelRequests.php';
require plugin_dir_path(__FILE__) . 'documents.php';
require plugin_dir_path(__FILE__) . '/institutes/student-registered.php';
require plugin_dir_path(__FILE__) . '/institutes/payments.php';
require plugin_dir_path(__FILE__) . 'alliance/institutes-registered.php';
require plugin_dir_path(__FILE__) . 'alliance/payments.php';
require plugin_dir_path(__FILE__) . 'academic-offers.php';
require plugin_dir_path(__FILE__) . 'requests.php';
require plugin_dir_path(__FILE__) . 'pensum.php';
require plugin_dir_path(__FILE__) . 'program.php';
require plugin_dir_path(__FILE__) . 'student-graduation.php';
require plugin_dir_path(__FILE__) . 'feed.php';
require plugin_dir_path(__FILE__) . 'auto-inscription.php';
require plugin_dir_path(__FILE__) . 'templates-emails.php';

function admin_form_plugin_scripts()
{
    wp_enqueue_style('style-admin', plugins_url('edusystem') . '/admin/assets/css/style.css');
}

add_action('wp_enqueue_scripts', 'admin_form_plugin_scripts');

function aes_scripts_admin()
{
    $version = VERSIONS_JS;
    wp_enqueue_style('flatpickr', plugins_url('edusystem') . '/public/assets/css/flatpickr.min.css');
    wp_enqueue_style('intel-css', plugins_url('edusystem') . '/public/assets/css/intlTelInput.css');
    wp_enqueue_style('style-admin', plugins_url('edusystem') . '/admin/assets/css/style.css', array(), $version, 'all');
    wp_enqueue_script('xlsx-js', plugins_url('edusystem') . '/admin/assets/js/xlsx.full.min.js', array('jquery'), $version, true);
    wp_enqueue_script('admin-flatpickr', plugins_url('edusystem') . '/public/assets/js/flatpickr.js', array('jquery'), $version, true);
    wp_enqueue_script('admin-flatpickr', plugins_url('edusystem') . '/public/assets/js/flatpickr.js', array('jquery'), $version, true);
    wp_enqueue_script('intel-js', plugins_url('edusystem') . '/public/assets/js/intlTelInput.min.js');
    wp_enqueue_script('masker-js', plugins_url('edusystem') . '/public/assets/js/vanilla-masker.min.js');

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_payments_content') {
        wp_enqueue_script('student-payment', plugins_url('edusystem') . '/admin/assets/js/payment.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_pensum_content' && $_GET['section_tab'] == 'pensum_details') {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery']);

        // Especifica jQuery como dependencia y usa la versión empaquetada con WordPress
        wp_enqueue_script(
            'pensum',
            plugins_url('edusystem') . '/admin/assets/js/pensum.js',
            ['jquery', 'select2'], // Asegura que jQuery y Select2 se carguen primero
            $version,
            true
        );
    }

    if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_payments_content' && $_GET['section_tab'] === 'generate_advance_payment') {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery']);

        // Especifica jQuery como dependencia y usa la versión empaquetada con WordPress
        wp_enqueue_script(
            'manage-payments',
            plugins_url('edusystem') . '/admin/assets/js/manage-payments.js',
            ['jquery', 'select2'], // Asegura que jQuery y Select2 se carguen primero
            $version,
            true
        );

        wp_localize_script('manage-payments', 'manage_payments', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'manage_payments_search_student'
        ]);
    }

    if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_academic_offers_content') {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery']);

        // Especifica jQuery como dependencia y usa la versión empaquetada con WordPress
        wp_enqueue_script(
            'academic-offers',
            plugins_url('edusystem') . '/admin/assets/js/academic-offers.js',
            ['jquery', 'select2'], // Asegura que jQuery y Select2 se carguen primero
            $version,
            true
        );
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_scholarships_content') {
        wp_enqueue_script('student-payment', plugins_url('edusystem') . '/admin/assets/js/scholarship.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_send_email_content') {
        wp_enqueue_script('send-email', plugins_url('edusystem') . '/admin/assets/js/send-email.js', array('jquery'), $version, true);

        wp_localize_script('send-email', 'summary_email', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'summary_email'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_send_notification_content') {
        wp_enqueue_script('send-notification', plugins_url('edusystem') . '/admin/assets/js/send-notification.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_feed_content') {
        wp_enqueue_script('feed', plugins_url('edusystem') . '/admin/assets/js/feed.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_configuration_options_content') {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery']);

        // Especifica jQuery como dependencia y usa la versión empaquetada con WordPress
        wp_enqueue_script(
            'configuration',
            plugins_url('edusystem') . '/admin/assets/js/configuration.js',
            ['jquery', 'select2'], // Asegura que jQuery y Select2 se carguen primero
            $version,
            true
        );
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_teachers_content') {
        wp_enqueue_script('teacher', plugins_url('edusystem') . '/admin/assets/js/teacher.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'report-sales' || $_GET['page'] == 'add_admin_form_report_content') || $_GET['page'] == 'report-accounts-receivables' || $_GET['page'] == 'report-students' || $_GET['page'] == 'report-current-students' || $_GET['page'] == 'report-sales-product') {
        wp_enqueue_script('report', plugins_url('edusystem') . '/admin/assets/js/report.js', array('jquery'), $version, true);
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');

        wp_localize_script('report', 'list_orders_sales', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_orders_sales'
        ]);

        wp_localize_script('report', 'list_sales_product', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_sales_product'
        ]);

        wp_localize_script('report', 'list_accounts_receivables', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_accounts_receivables'
        ]);

        wp_localize_script('report', 'list_report_students', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_report_students'
        ]);

        wp_localize_script('report', 'list_report_current_students', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_report_current_students'
        ]);

        wp_localize_script('report', 'load_chart_data', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'load_chart_data'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_partners_content' || isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'list_admin_partner_payments_content' || $_GET['page'] == 'list_admin_partner_invoice_content') {
        wp_enqueue_script('alliance', plugins_url('edusystem') . '/admin/assets/js/alliance.js', array('jquery'), $version, true);


        wp_localize_script('alliance', 'list_fee_alliance', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_fee_alliance'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_requests_content') {
        wp_enqueue_script('request', plugins_url('edusystem') . '/admin/assets/js/request.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_department_content') {
        wp_enqueue_script('department', plugins_url('edusystem') . '/admin/assets/js/department.js', array('jquery'), $version, true);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_enrollments_content') {
        wp_enqueue_script('enrollment', plugins_url('edusystem') . '/admin/assets/js/enrollment.js', array('jquery'), $version, true);

        wp_localize_script('enrollment', 'search_student_id_document', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'search_student_id_document'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_admission_content') {
        wp_enqueue_script('student-documents', plugins_url('edusystem') . '/admin/assets/js/document.js', array('jquery'), $version, true);

        wp_localize_script('student-documents', 'update_status_documents', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'update_status_documents'
        ]);

        wp_localize_script('student-documents', 'get_student_details', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'update_status_documents'
        ]);

        wp_localize_script('student-documents', 'get_student_details', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'get_student_details'
        ]);

        wp_localize_script('student-documents', 'last_access_moodle', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'last_access_moodle'
        ]);

        wp_localize_script('student-documents', 'generate_document', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'generate_document'
        ]);

        wp_localize_script('student-documents', 'get_approved_by', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'get_approved_by'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'add_admin_institutes_content' || $_GET['page'] == 'list_admin_institutes_partner_registered_content' || $_GET['page'] == 'list_admin_institutes_payments_content' || $_GET['page'] == 'list_admin_institutes_invoice_content')) {
        wp_enqueue_script('institute', plugins_url('edusystem') . '/admin/assets/js/institute.js', array('jquery'), $version, true);

        wp_localize_script('institute', 'list_fee_institute', [
            'url' => admin_url('admin-ajax.php'),
            'action' => 'list_fee_institute'
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_academic_projection_content') {
        wp_enqueue_script('academic-projection', plugins_url('edusystem') . '/admin/assets/js/academic-projection.js', array('jquery'), $version, true);

        wp_localize_script('academic-projection', 'ajax_object', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }

    if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == 'add_admin_form_auto_inscription_content') {
        wp_enqueue_script('auto-enroll', plugins_url('edusystem') . '/admin/assets/js/auto-enroll.js', array('jquery'), $version, true);

        wp_localize_script('auto-enroll', 'ajax_object', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }

}

add_action('admin_enqueue_scripts', 'aes_scripts_admin', 3);

function add_custom_admin_page()
{

    global $current_user;
    $roles = $current_user->roles;

    $subscription_status = get_option('site_status_subscription');
    if ($subscription_status != 'expired') {
        if (in_array('institutes', $roles)) {

            add_menu_page(
                __('Students', 'edusystem'),
                __('Students', 'edusystem'),
                'read',
                'list_admin_institutes_student_registered_content',
                'list_admin_institutes_student_registered_content',
                'dashicons-groups',
                10
            );

            add_menu_page(
                __('Fees', 'edusystem'),
                __('Fees', 'edusystem'),
                'read',
                'list_admin_institutes_payments_content',
                'list_admin_institutes_payments_content',
                'dashicons-money-alt',
                11
            );


            add_menu_page(
                __('Invoice', 'edusystem'),
                __('Invoice', 'edusystem'),
                'read',
                'list_admin_institutes_invoice_content',
                'list_admin_institutes_invoice_content',
                'dashicons-admin-page',
                11
            );
        }

        if (in_array('alliance', $roles)) {

            add_menu_page(
                __('Institutes', 'edusystem'),
                __('Institutes', 'edusystem'),
                'read',
                'list_admin_institutes_partner_registered_content',
                'list_admin_institutes_partner_registered_content',
                'dashicons-groups',
                10
            );

            add_menu_page(
                __('Fees', 'edusystem'),
                __('Fees', 'edusystem'),
                'read',
                'list_admin_partner_payments_content',
                'list_admin_partner_payments_content',
                'dashicons-money-alt',
                11
            );

            add_menu_page(
                __('Students', 'edusystem'),
                __('Students', 'edusystem'),
                'read',
                'list_admin_partner_students_content',
                'list_admin_partner_students_content',
                'dashicons-admin-users',
                11
            );

            add_menu_page(
                __('Invoice', 'edusystem'),
                __('Invoice', 'edusystem'),
                'read',
                'list_admin_partner_invoice_content',
                'list_admin_partner_invoice_content',
                'dashicons-admin-page',
                11
            );
        }

        if (current_user_can('manager_admission_aes') || current_user_can('only_read_admission_aes')) {
            add_menu_page(
                __('Admission', 'edusystem'),
                __('Admission', 'edusystem'),
                'manage_options',
                'add_admin_form_admission_content',
                'add_admin_form_admission_content',
                'dashicons-groups',
                4
            );
            add_submenu_page('add_admin_form_admission_content', __('Required Documents', 'edusystem'), __('Required Documents', 'edusystem'), 'manager_documents_aes', 'admission-documents', 'show_admission_documents', 10);
        }

        add_menu_page(
            __('Academic', 'edusystem'),
            __('Academic', 'edusystem'),
            'manager_academic_aes',
            'add_admin_form_academic_content',
            'add_admin_form_academic_content',
            'dashicons-welcome-learn-more',
            4
        );
        add_submenu_page('add_admin_form_academic_content', __('Academic periods', 'edusystem'), __('Academic periods', 'edusystem'), 'manager_academic_periods_aes', 'add_admin_form_academic_periods_content', 'add_admin_form_academic_periods_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Academic offers', 'edusystem'), __('Academic offers', 'edusystem'), 'manager_academic_offers_aes', 'add_admin_form_academic_offers_content', 'add_admin_form_academic_offers_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Academic projection', 'edusystem'), __('Academic projection', 'edusystem'), 'manager_academic_projection_aes', 'add_admin_form_academic_projection_content', 'add_admin_form_academic_projection_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Autoenrollment', 'edusystem'), __('Autoenrollment', 'edusystem'), 'manager_automatically_inscriptions', 'add_admin_form_auto_inscription_content', 'add_admin_form_auto_inscription_content', 10);
        // add_submenu_page('add_admin_form_academic_content', __('Enrollments', 'edusystem'), __('Enrollments', 'edusystem'), 'manager_enrollments_aes', 'add_admin_form_enrollments_content', 'add_admin_form_enrollments_content', 10);
        // add_submenu_page('add_admin_form_academic_content', __('Student graduations', 'edusystem'), __('Student graduations', 'edusystem'), 'manager_graduations_aes', 'add_admin_form_student_graduated_content', 'add_admin_form_student_graduated_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Requests', 'edusystem'), __('Requests', 'edusystem'), 'manager_requests_aes', 'add_admin_form_requests_content', 'add_admin_form_requests_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Scholarship students', 'edusystem'), __('Scholarship students', 'edusystem'), 'manager_scholarship_aes', 'add_admin_form_scholarships_content', 'add_admin_form_scholarships_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Available scholarships', 'edusystem'), __('Available scholarships', 'edusystem'), 'manager_availables_scholarship_aes', 'add_admin_form_available_scholarships_content', 'add_admin_form_available_scholarships_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Pensum', 'edusystem'), __('Pensum', 'edusystem'), 'manager_pensums', 'add_admin_form_pensum_content', 'add_admin_form_pensum_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Program', 'edusystem'), __('Program', 'edusystem'), 'manager_programs', 'add_admin_form_program_content', 'add_admin_form_program_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('School subjects', 'edusystem'), __('School subjects', 'edusystem'), 'manager_school_subjects_aes', 'add_admin_form_school_subjects_content', 'add_admin_form_school_subjects_content', 10);
        add_submenu_page('add_admin_form_academic_content', __('Student banners', 'edusystem'), __('Student banners', 'edusystem'), 'manager_feed', 'add_admin_form_feed_content', 'add_admin_form_feed_content', 10);
        remove_submenu_page('add_admin_form_academic_content', 'add_admin_form_academic_content');

        add_menu_page(
            __('Payments', 'edusystem'),
            __('Payments', 'edusystem'),
            'manager_payments_aes',
            'add_admin_form_payments_content',
            'add_admin_form_payments_content',
            'dashicons-money-alt',
            5
        );

        add_menu_page(
            __('Staff', 'edusystem'),
            __('Staff', 'edusystem'),
            'manager_staff_menu_aes',
            'add_admin_form_staff_menu_content',
            'add_admin_form_staff_menu_content',
            'dashicons-buddicons-buddypress-logo',
            6
        );
        add_submenu_page('add_admin_form_staff_menu_content', __('Staff', 'edusystem'), __('Staff', 'edusystem'), 'manager_staff_aes', 'add_admin_form_staff_content', 'add_admin_form_staff_content', 10);
        add_submenu_page('add_admin_form_staff_menu_content', __('Institutes', 'edusystem'), __('Institutes', 'edusystem'), 'manager_institutes_aes', 'add_admin_institutes_content', 'add_admin_institutes_content', 10);
        add_submenu_page('add_admin_form_staff_menu_content', __('Alliances', 'edusystem'), __('Alliances', 'edusystem'), 'manager_alliances_aes', 'add_admin_partners_content', 'add_admin_partners_content', 10);
        add_submenu_page('add_admin_form_staff_menu_content', __('Teachers', 'edusystem'), __('Teachers', 'edusystem'), 'manager_teachers_aes', 'add_admin_form_teachers_content', 'add_admin_form_teachers_content', 10);
        remove_submenu_page('add_admin_form_staff_menu_content', 'add_admin_form_staff_menu_content');

        add_menu_page(
            __('Communications', 'edusystem'),
            __('Communications', 'edusystem'),
            'manager_communications_aes',
            'add_admin_form_communications_content',
            'add_admin_form_communications_content',
            'dashicons-email-alt2',
            7
        );
        add_submenu_page('add_admin_form_communications_content', __('Send email', 'edusystem'), __('Send email', 'edusystem'), 'manager_send_email_aes', 'add_admin_form_send_email_content', 'add_admin_form_send_email_content', 10);
        add_submenu_page('add_admin_form_communications_content', __('Email to staff', 'edusystem'), __('Email to staff', 'edusystem'), 'manager_send_notification_aes', 'add_admin_form_send_notification_content', 'add_admin_form_send_notification_content', 10);
        add_submenu_page('add_admin_form_communications_content', __('Template emails', 'edusystem'), __('Template emails', 'edusystem'), 'manager_templates_emails', 'add_admin_form_templates_emails_content', 'add_admin_form_templates_emails_content', 10);
        remove_submenu_page('add_admin_form_communications_content', 'add_admin_form_communications_content');

        add_menu_page(
            __('Report', 'edusystem'),
            __('Report', 'edusystem'),
            'manager_report_aes',
            'add_admin_form_report_content',
            'add_admin_form_report_content',
            'dashicons-list-view',
            8
        );
        add_submenu_page('add_admin_form_report_content', __('Sales', 'edusystem'), __('Sales', 'edusystem'), 'manager_sales_aes', 'report-sales', 'show_report_sales', 10);
        add_submenu_page('add_admin_form_report_content', __('Accounts receivable', 'edusystem'), __('Accounts receivable', 'edusystem'), 'manager_accounts_receivables_aes', 'report-accounts-receivables', 'show_report_accounts_receivables', 10);
        // add_submenu_page('add_admin_form_report_content', __('Students', 'edusystem'), __('Students', 'edusystem'), 'manager_report_students_aes', 'report-students', 'show_report_students', 10);
        add_submenu_page('add_admin_form_report_content', __('Students', 'edusystem'), __('Students', 'edusystem'), 'manager_report_students_aes', 'report-current-students', 'show_report_current_students', 10);
        add_submenu_page('add_admin_form_report_content', __('Sales by product', 'edusystem'), __('Sales by product', 'edusystem'), 'manager_report_sales_product', 'report-sales-product', 'show_report_sales_product', 10);

        add_menu_page(
            __('Settings', 'edusystem'),
            __('Settings', 'edusystem'),
            'manager_settings_aes',
            'add_admin_form_settings_content',
            'add_admin_form_settings_content',
            'dashicons-admin-generic',
            30
        );
        add_submenu_page('add_admin_form_settings_content', __('Settings', 'edusystem'), __('Settings', 'edusystem'), 'manager_configuration_options_aes', 'add_admin_form_configuration_options_content', 'add_admin_form_configuration_options_content', 10);
        add_submenu_page('add_admin_form_settings_content', __('Departments', 'edusystem'), __('Departments', 'edusystem'), 'manager_departments_aes', 'add_admin_department_content', 'list_admin_form_department_content', 10);
        remove_submenu_page('add_admin_form_settings_content', 'add_admin_form_settings_content');
    }

    if ($subscription_status == 'expired' && !in_array('administrator', $roles)) {
        remove_menu_page('users.php');
        remove_menu_page('upload.php');
    }
}

add_action('admin_menu', 'add_custom_admin_page');

function add_cap_to_administrator()
{

    $role = get_role('administrator');
    $role->add_cap('manage_administrator_aes');
    $role->add_cap('manager_departments_aes');
    $role->add_cap('manager_scholarship_aes');
    $role->add_cap('manager_availables_scholarship_aes');
    $role->add_cap('manager_academic_periods_aes');
    $role->add_cap('manager_school_subjects_aes');
    $role->add_cap('manager_academic_projection_aes');
    $role->add_cap('manager_automatically_inscriptions');
    $role->add_cap('manager_teachers_aes');
    $role->add_cap('manager_enrollments_aes');
    $role->add_cap('can_regenerate_projection');
    $role->add_cap('manager_graduations_aes');
    $role->add_cap('manager_pensums');
    $role->add_cap('manager_programs');
    $role->add_cap('manager_feed');
    $role->add_cap('manager_templates_emails');
    $role->add_cap('manager_academic_offers_aes');
    $role->add_cap('manager_requests_aes');
    $role->add_cap('manager_configuration_options_aes');
    $role->add_cap('manager_send_email_aes');
    $role->add_cap('manager_send_notification_aes');
    $role->add_cap('manager_notifications_aes');
    $role->add_cap('manager_staff_aes');
    $role->add_cap('manager_admission_aes');
    $role->add_cap('manager_report_aes');
    $role->add_cap('manager_report_students_aes');
    $role->add_cap('manager_report_current_students_aes');
    $role->add_cap('manager_report_sales_product');
    $role->add_cap('manager_sales_aes');
    $role->add_cap('manager_accounts_receivables_aes');
    $role->add_cap('manager_documents_aes');
    $role->add_cap('manager_payments_aes');
    $role->add_cap('manager_alliances_aes');
    $role->add_cap('manager_institutes_aes');
    $role->add_cap('manager_moodle_aes');
    $role->add_cap('manager_moodle_settings_aes');
}

add_action('admin_init', 'add_cap_to_administrator');

function list_departments_admin_page_callback()
{
    echo do_shortcode('[list_departments]');
}

add_action('admin_menu', 'add_custom_admin_page');

function get_dates_search($filter, $custom)
{

    if ($filter == 'today') {
        $start = get_gmt_from_date(wp_date('Y-m-d') . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d') . '23:59', 'Y-m-d H:i');

    } else if ($filter == 'yesterday') {
        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('-1 days')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', ) . '00:00', 'Y-m-d H:i');

    } else if ($filter == 'tomorrow') {
        $start = get_gmt_from_date(wp_date('Y-m-d') . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('+1 days')) . '00:00', 'Y-m-d H:i');

    } else if ($filter == 'this-week') {

        $date = Datetime::createFromFormat('Y-m-d', wp_date('Y-m-d'));

        if ($date->format('w') == 1) {
            $start = get_gmt_from_date(wp_date('Y-m-d') . '00:00', 'Y-m-d H:i');
        } else {
            $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('last tuesday')) . '00:00', 'Y-m-d H:i');
        }

        if ($date->format('w') == 1) {
            $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('next saturday', strtotime('+1 days'))) . '23:59', 'Y-m-d H:i');
        } else {
            $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('next sunday')) . '23:59', 'Y-m-d H:i');
        }

    } else if ($filter == 'last-week') {

        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('last week')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('this week -1 days')) . '23:59', 'Y-m-d H:i');


    } else if ($filter == 'next-week') {

        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('this week')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('next week -1 days')) . '23:59', 'Y-m-d H:i');


    } else if ($filter == 'this-month') {

        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('first day of this month')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('last day of this month')) . '23:59', 'Y-m-d H:i');

    } else if ($filter == 'last-month') {

        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('first day of last month')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('last day of last month')) . '23:59', 'Y-m-d H:i');


    } else if ($filter == 'next-month') {

        $start = get_gmt_from_date(wp_date('Y-m-d', strtotime('first day of next month')) . '00:00', 'Y-m-d H:i');
        $end = get_gmt_from_date(wp_date('Y-m-d', strtotime('last day of next month')) . '23:59', 'Y-m-d H:i');


    } else if ($filter == 'custom') {

        $date = str_replace([' to ', ' a '], ',', $custom);
        $date_array = explode(',', $date);

        $start = str_replace('/', '-', $date_array[0]);

        if (isset($date_array[1]) && !empty($date_array[1])) {
            $end = str_replace('/', '-', $date_array[1]);
        } else {
            $end = str_replace('/', '-', $date_array[0]);
        }

        $startDatetime = Datetime::createFromFormat('m-d-Y', $start);
        $endDatetime = Datetime::createFromFormat('m-d-Y', $end);

        $start = get_gmt_from_date($startDatetime->format('Y-m-d') . '07:00', 'Y-m-d H:i');
        $end = get_gmt_from_date($endDatetime->modify('+1 day')->format('Y-m-d') . '06:59', 'Y-m-d H:i');
        /*
        if($sales){

            $dayStart = $startDatetime->format('w');

            if(get_option('restaurant_system_schedule_'.$dayStart.'_checkbox') == 'true'){

                $start_time = get_option('restaurant_system_schedule_'.$dayStart.'_start_time');

                if(get_option('restaurant_system_schedule_'.$dayStart.'_interday') == 'true'){
                    $start = get_gmt_from_date($startDatetime->format('Y-m-d').$start_time,'Y-m-d H:i');
                }
            }

        }

        $end = get_gmt_from_date($endDatetime->format('Y-m-d').'23:59','Y-m-d H:i');

        if($sales){

            $dayEnd = $endDatetime->format('w');

            if(get_option('restaurant_system_schedule_'.$dayEnd.'_checkbox') == 'true'){

                $end_time = get_option('restaurant_system_schedule_'.$dayEnd.'_end_time');

                if(get_option('restaurant_system_schedule_'.$dayEnd.'_interday') == 'true'){

                    $end = get_gmt_from_date($endDatetime->format('Y-m-d').$end_time,'Y-m-d H:i');
                }
            }
        }
        */
    }

    return [$start, $end];
}



// AGREGAR NUEVO CAMPO DE VARIACION DE PRODUCTO PARA JUGAR CON LOS VALORES DE LAS CUOTAS EN LOS PROGRAMAS
add_action('woocommerce_product_after_variable_attributes', 'num_cuotes', 10, 3);
function num_cuotes($loop, $variation_data, $variation)
{
    woocommerce_wp_text_input(
        array(
            'id' => 'text_field[' . $loop . ']',
            'label' => 'Num cuotes',
            'wrapper_class' => 'form-row',
            'placeholder' => 'Num cuotes for program',
            'desc_tip' => true,
            'description' => 'Number of installments to pay for a product.',
            'value' => get_post_meta($variation->ID, 'num_cuotes_text', true)
        )
    );
}

add_action('woocommerce_save_product_variation', 'save_num_cuotes', 10, 2);
function save_num_cuotes($variation_id, $i)
{
    if (isset($_POST['text_field'][$i])) {
        update_post_meta($variation_id, 'num_cuotes_text', sanitize_text_field($_POST['text_field'][$i]));
    }
}
// AGREGAR NUEVO CAMPO DE VARIACION DE PRODUCTO PARA JUGAR CON LOS VALORES DE LAS CUOTAS EN LOS PROGRAMAS

// functions.php

function add_logo_dashboard() {
    $logo_id = get_option('logo_admin');

    // Exit early if no logo ID is set or URL can't be retrieved
    if ( ! $logo_id || ! ( $logo_url = wp_get_attachment_image_url( $logo_id, 'full' ) ) ) {
        return;
    }

    // Inject the logo URL as a CSS variable using your 'style-admin' handle
    wp_add_inline_style( 'style-admin', ":root { --admin-logo-url: url('{$logo_url}'); }" );
}
add_action('admin_enqueue_scripts', 'add_logo_dashboard');

function aes_logo()
{
    add_menu_page('logo', 'logo', 'read', 'logo_based_menu', '', '', 1);
}
add_action('admin_menu', 'aes_logo');

function custom_login_store()
{
    if (get_option('blog_img_logo')) {
        $url = 'https://portal.americanelite.school/wp-content/uploads/2025/01/cropped-cropped-cropped-AMERICAN-ELITE-SCHOOL_LOGOTIPO-COLOR-3.png';
        echo '
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(' . $url . ');
        background-size: cover;
        background-repeat: no-repeat;
        width:110px;
        height:110px;
        background-color:white;
        border-radius:50%;
        }
    </style>';
    }
}
add_action('login_enqueue_scripts', 'custom_login_store');

function remove_text_admin_bar_profile($wp_admin_bar)
{
    $avatar = get_avatar(get_current_user_id(), 16);
    if (!$wp_admin_bar->get_node('my-account')) {
        return;
    }
    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => sprintf('%s', wp_get_current_user()->user_firstname . ' ' . wp_get_current_user()->user_lastname) . $avatar,
    ));
}
add_action('admin_bar_menu', 'remove_text_admin_bar_profile');

function hide_notices()
{
    if (!is_super_admin()) {
        remove_all_actions('user_admin_notices');
        remove_all_actions('admin_notices');
    }
}
add_action('in_admin_header', 'hide_notices', 99);

add_action('login_enqueue_scripts', 'aes_change_login_logo');
function aes_change_login_logo()
{ ?>
    <style type="text/css">
        #login h1 a {
            background: url('https://portal.americanelite.school/wp-content/uploads/2025/01/cropped-cropped-AMERICAN-ELITE-SCHOOL_LOGOTIPO-VERTICAL_COLOR.png') no-repeat center center;
            background-size: 100px;
            height: 100px;
            margin: 0 auto;
            width: 100px;
        }
    </style>
<?php }

add_filter('login_headerurl', 'aes_login_redirect_url');
function aes_login_redirect_url()
{
    return 'https://portal.americanelite.school/'; // Replace with your desired URL
}

// Add a custom action to the user list
add_filter('user_row_actions', 'add_welcome_student_action', 10, 2);
function add_welcome_student_action($actions, $user_object)
{
    // Get the user roles
    $user_roles = $user_object->roles;

    // Check if the user has the "student" role
    if (in_array('student', $user_roles)) {
        $actions['welcome_student'] = '<a href="#" onclick="welcomeStudent(' . $user_object->ID . ')">Welcome Student</a>';
    }
    return $actions;
}

// Add a new column to the user list
function add_last_login_column($columns)
{
    $columns['last_login'] = 'Last login';
    unset($columns['posts']);
    return $columns;
}
add_filter('manage_users_columns', 'add_last_login_column');

// Populate the last login column with user data
function populate_last_login_column($value, $column_name, $user_id)
{
    if ($column_name == 'last_login') {
        $last_login = get_user_meta($user_id, 'last_login', true);
        if ($last_login) {
            return date_i18n('Y-m-d H:i:s', $last_login);
        } else {
            return 'N/A'; // or any other default value you want to display
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'populate_last_login_column', 10, 3);

function update_last_login($user_login, $user)
{
    $current_time = current_time('timestamp');
    update_user_meta($user->ID, 'last_login', $current_time);
}
add_action('wp_login', 'update_last_login', 10, 2);

// Add a JavaScript code to trigger the welcome student function
add_action('admin_footer', 'add_welcome_student_js');
function add_welcome_student_js()
{
    ?>
    <script>
        function welcomeStudent(userId) {
            jQuery.post(ajaxurl, {
                'action': 'welcome_student',
                'user_id': userId
            }, function (response) {
            });
        }
    </script>
    <?php
}

// Handle the AJAX request to trigger the welcome student function
add_action('wp_ajax_welcome_student', 'welcome_student_ajax_handler');
function welcome_student_ajax_handler()
{
    $user_id = $_POST['user_id'];
    $user = get_userdata($user_id);
    welcome_students($user->user_email);
    wp_die();
}

function welcome_students($user_login)
{
    // Get the student ID from the user data
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$user_login}'");
    if ($student) {
        $student_id = $student->id;
        $user = get_user_by('email', $user_login);
        $reset_key = get_password_reset_key($user);
        $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

        // Get the WC_Request_Documents_Email instance
        $email_welcome_student = WC()->mailer()->get_emails()['WC_Welcome_Student_Email'];
        $email_welcome_student->trigger($student_id, $reset_url);

        // Send a copy to the parent
        $email_welcome_student_parent = WC()->mailer()->get_emails()['WC_Welcome_Student_Email'];
        $email_welcome_student_parent->trigger($student_id, $reset_url, 1);
    }
}

function admin_notice($message, $type = 'success')
{
    ?>
    <div class="notice notice-<?php echo $type; ?> is-dismissible">
        <p><?php echo $message; ?></p>
    </div>
    <?php
}

function get_states_by_country()
{
    $country_code = $_POST['country_code'];
    $wc_countries = new WC_Countries();
    $states = $wc_countries->get_states($country_code);
    echo json_encode($states);
    exit;
}

add_action('wp_ajax_get_states_by_country', 'get_states_by_country');
add_action('wp_ajax_nopriv_get_states_by_country', 'get_states_by_country');

add_action('create_invoice_instute_monthly', 'monthly_invoice_institute');
function monthly_invoice_institute()
{
    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';
    $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
    $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 1");
    $first_day_prev_month = date('Y-m-01', strtotime('first day of previous month'));
    foreach ($institutes as $key => $institute) {
        $exist = $wpdb->get_row("SELECT * FROM {$table_institutes_payments} WHERE institute_id={$institute->id} AND month='{$first_day_prev_month}'");
        if (!$exist) {

            $last_month_invoice = get_dates_search('last-month', null);
            $invoice = get_invoices_institutes($last_month_invoice[0], $last_month_invoice[1], $institute->id);

            $wpdb->insert(
                $table_institutes_payments,
                array(
                    'institute_id' => $institute->id,
                    'total_orders' => sizeof($invoice['orders']),
                    'amount' => $invoice['total'],
                    'status_id' => $invoice['total'] > 0 ? 0 : 1,
                    'month' => $first_day_prev_month,
                    'created_at' => current_time('mysql'),
                )
            );
        }
    }
    exit;
}


add_action('create_invoice_alliance_monthly', 'monthly_invoice_alliance');
function monthly_invoice_alliance()
{
    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';
    $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
    $alliances = $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 1");
    $first_day_prev_month = date('Y-m-01', strtotime('first day of previous month'));
    foreach ($alliances as $key => $alliance) {
        $exist = $wpdb->get_row("SELECT * FROM {$table_alliances_payments} WHERE alliance_id={$alliance->id} AND month='{$first_day_prev_month}'");
        if (!$exist) {

            $last_month_invoice = get_dates_search('last-month', null);
            $invoice = get_invoices_alliances($last_month_invoice[0], $last_month_invoice[1], $alliance->id);

            $wpdb->insert(
                $table_alliances_payments,
                array(
                    'alliance_id' => $alliance->id,
                    'total_orders' => sizeof($invoice['orders']),
                    'amount' => $invoice['total'],
                    'status_id' => $invoice['total'] > 0 ? 0 : 1,
                    'month' => $first_day_prev_month,
                    'created_at' => current_time('mysql'),
                )
            );
        }
    }
    exit;
}

add_action('create_pending_payment_email_weekly', 'weekly_pending_payment');
function weekly_pending_payment()
{
    send_pending_payments_email();
    exit;
}

add_action('create_pending_prepayment_email_weekly', 'weekly_pending_prepayment');
function weekly_pending_prepayment()
{
    send_pending_prepayments_email();
    exit;
}

add_action('send_email_remember_documents_student', 'send_email_remember_documents_student');
function send_email_remember_documents_student()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE status_id = 1 AND moodle_student_id IS NULL ORDER BY id DESC");
    foreach ($students as $key => $student) {
        $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
        $sender_email->trigger($student->email, 'Pending documents', 'From American Elite School, we want to remind you that you still have pending documents to upload to our platform. It is essential that you send them as soon as possible to ensure that your academic process continues smoothly. If you need assistance or have any questions about the required documents, please do not hesitate to contact us. We are here to help you with whatever you need.');
    }
    exit;
}

add_action('cuote_pendings_daily', 'daily_cuote_pendings');
function daily_cuote_pendings()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_payments = $wpdb->prefix . 'student_payments';

    $payments = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_student_payments} 
                 WHERE status_id = %d 
                 AND date_next_payment BETWEEN %s AND %s",
            0,
            date('Y-m-d'),
            date('Y-m-d', strtotime('+7 days'))
        )
    );

    foreach ($payments as $payment) {
        global $wpdb;
        $pre_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$payment->student_id}");
        if ($pre_student) {
            $user = get_user_by('email', $pre_student->email);

            $roles = $user->roles;
            $table_students = $wpdb->prefix . 'students';
            $table_student_payments = $wpdb->prefix . 'student_payments';
            $customer_id = 0;
            if (in_array('student', $roles)) {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$user->user_email}'");

                $birth_date = get_user_meta($user->ID, 'birth_date', true);
                $birth_date_timestamp = strtotime($birth_date);
                $current_timestamp = time();
                $age = floor(($current_timestamp - $birth_date_timestamp) / 31536000); // 31536000 es el número de segundos en un año
                if ($age >= 18) {
                    $customer_id = $user->ID;
                } else {
                    $customer_id = $student->partner_id;
                }
            } else if (in_array('parent', $roles)) {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id='{$user->ID}'");
                $customer_id = $user->ID;
            }

            if ($student) {
                $cuote_pending = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id={$student->id} AND status_id = 0 AND date_next_payment <= NOW()");
                if ($cuote_pending) {
                    update_user_meta($customer_id, 'cuote_pending', 1);

                    $args['customer_id'] = $customer_id;
                    $args['status'] = array('wc-pending', 'wc-on-hold');
                    $order_pendings = wc_get_orders($args);
                    if (count($order_pendings) == 0) {
                        $orders_customer = wc_get_orders(array(
                            'customer_id' => $customer_id,
                            'limit' => 1,
                            'orderby' => 'date',
                            'order' => 'ASC' // Para obtener la primera orden
                        ));
                        $order_old = $orders_customer[0];
                        $order_id = $order_old->get_id();
                        $old_order_items = $order_old->get_items();
                        $first_item = reset($old_order_items);

                        $order_args = array(
                            'customer_id' => $customer_id,
                            'status' => 'pending-payment',
                        );

                        $new_order = wc_create_order($order_args);
                        $new_order->add_meta_data('old_order_primary', $order_id);
                        $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
                        $new_order->add_meta_data('institute_id', $order_old->get_meta('institute_id'));
                        $new_order->add_meta_data('student_id', $order_old->get_meta('student_id'));
                        $new_order->add_meta_data('cuote_payment', 1);
                        $new_order->update_meta_data('_order_origin', 'Cuote pending - CronJob');
                        $product = $first_item->get_product();
                        $product->set_price($cuote_pending->amount);
                        $new_order->add_product($product, $first_item->get_quantity());
                        $new_order->calculate_totals();
                        if ($order_old->get_address('billing')) {
                            $billing_address = $order_old->get_address('billing');
                            $new_order->set_billing_first_name($billing_address['first_name']);
                            $new_order->set_billing_last_name($billing_address['last_name']);
                            $new_order->set_billing_company($billing_address['company']);
                            $new_order->set_billing_address_1($billing_address['address_1']);
                            $new_order->set_billing_address_2($billing_address['address_2']);
                            $new_order->set_billing_city($billing_address['city']);
                            $new_order->set_billing_state($billing_address['state']);
                            $new_order->set_billing_postcode($billing_address['postcode']);
                            $new_order->set_billing_country($billing_address['country']);
                            $new_order->set_billing_email($billing_address['email']);
                            $new_order->set_billing_phone($billing_address['phone']);
                        }
                        $new_order->save();

                        // hacemos el envio del email al email del customer, es decir, al que paga.
                        $user_customer = get_user_by('id', $customer_id);
                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger($user_customer, 'You have pending payments', 'We invite you to log in to our platform as soon as possible so you can see your pending payments.');
                    }
                } else {
                    update_user_meta($customer_id, 'cuote_pending', 0);
                }
            }
        }
    }

    exit;
}

add_action('current_screen', 'detect_orders_endpoint_admin');
function detect_orders_endpoint_admin()
{
    // Obtener la pantalla actual
    $screen = get_current_screen();
    if ($screen) {
        // Verificar si estamos en la pantalla deseada
        if ($screen->id === 'toplevel_page_add_admin_form_payments_content' || $screen->id === 'woocommerce_page_wc-orders') {
            $orders = wc_get_orders(array(
                'status' => 'pending'
            ));

            if (count($orders) > 0) {
                $order_id = $orders[0]->get_id(); // Obtener el ID del primer pedido pendiente
                $order = wc_get_order($order_id);
            }

            if (isset($order)) {
                foreach ($order->get_items('fee') as $item_id => $item_fee) {
                    if ($item_fee->get_name() === 'Bank Transfer Fee' || $item_fee->get_name() === 'Credit Card Fee') {
                        $order->remove_item($item_id);
                    }
                }

                $order->calculate_totals();
                $order->save();
            }
        }
    }
}

function get_replacements_variables($student, $code_period = null, $cut_period = null)
{
    $load = load_current_cut();
    $academic_period = get_period_details_code($load['code']);
    $start_academic_period = date('F d, Y', strtotime($academic_period->start_date));
    $end_academic_period = date('F d, Y', strtotime($academic_period->end_date));
    $billing_country = get_user_meta($student->partner_id, 'billing_country', true);
    $state_code = get_user_meta($student->partner_id, 'billing_state', true);
    $states = WC()->countries->get_states($billing_country);
    $state_name = isset($states[$state_code]) ? $states[$state_code] : $state_code;
    $countries = WC()->countries->get_countries();
    $country_code = $student->country;
    $country_name = isset($countries[$country_code]) ? $countries[$country_code] : $country_code;

    $replacements = [
        'student_name' => [
            'value' => $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name,
            'wrap' => true,
        ],
        'name' => [
            'value' => $student->name . ' ' . $student->middle_name,
            'wrap' => true,
        ],
        'last_name' => [
            'value' => $student->last_name . ' ' . $student->middle_last_name,
            'wrap' => true,
        ],
        'id_student' => [
            'value' => $student->id_document,
            'wrap' => true,
        ],
        'race' => [
            'value' => get_ethnicity($student->ethnicity),
            'wrap' => true,
        ],
        'nacionality' => [
            'value' => $student->nacionality ?? '',
            'wrap' => true,
        ],
        'birth_date' => [
            'value' => date('m/d/Y', strtotime($student->birth_date)),
            'wrap' => true,
        ],
        'gender' => [
            'value' => $student->gender,
            'wrap' => true,
        ],
        'program' => [
            'value' => get_name_program($student->program_id),
            'wrap' => true,
        ],
        'academic_year' => [
            'value' => $academic_period->name,
            'wrap' => true,
        ],
        'start_academic_year' => [
            'value' => $start_academic_period,
            'wrap' => false,
        ],
        'end_academic_year' => [
            'value' => $end_academic_period,
            'wrap' => false,
        ],
        'table_notes' => [
            'value' => function () use ($student) {
                return table_notes_html($student->id, get_projection_by_student($student->id));
            },
            'wrap' => false,
        ],
        'table_notes_summary' => [
            'value' => function () use ($student) {
                return table_notes_summary_html(get_projection_by_student($student->id));
            },
            'wrap' => false,
        ],
        'table_inscriptions' => [
            'value' => function () use ($student) {
                return table_inscriptions_html(get_inscriptions_by_student($student->id));
            },
            'wrap' => false,
        ],
        'table_notes_period' => [
            'value' => function () use ($student, $code_period, $cut_period) {
                return table_notes_period_html(get_inscriptions_by_student_period($student->id, $code_period, $cut_period));
            },
            'wrap' => false,
        ],
        'address' => [
            'value' => get_user_meta($student->partner_id, 'billing_address_1', true),
            'wrap' => true,
        ],
        'state' => [
            'value' => $state_name,
            'wrap' => true,
        ],
        'country' => [
            'value' => $country_name,
            'wrap' => true,
        ],
        'zip_code' => [
            'value' => $student->postal_code,
            'wrap' => true,
        ],
        'phone' => [
            'value' => $student->phone,
            'wrap' => true,
        ],
        'email' => [
            'value' => $student->email,
            'wrap' => true,
        ],
        'today' => [
            'value' => date('M d, Y'),
            'wrap' => false,
        ],
        'qrcode' => [
            'value' => '<div id="qrcode"></div>',
            'wrap' => false,
        ],
        'page_break' => [
            'value' => '<div class="pagebreak"></div>',
            'wrap' => false,
        ],
    ];

    return $replacements;
}

function filter_media_library_modal($query)
{
    if (current_user_can('manager_media_aes') && !current_user_can('administrator')) {
        // Get all users with manager_media_aes capability but exclude administrators
        $manager_users = get_users(array(
            'fields' => 'ID',
            'capability' => 'manager_media_aes',
            'role__not_in' => array('administrator') // Exclude administrators
        ));

        if (!empty($manager_users)) {
            $query['author__in'] = $manager_users;
        }
    }
    return $query;
}
add_filter('ajax_query_attachments_args', 'filter_media_library_modal');
function wp_add_widgets_edusof()
{
    $subscription_status = get_option('site_status_subscription');
    if ($subscription_status != 'expired') {
        // Check if the current user has the 'manager_payments_aes' capability
        if (current_user_can('manager_payments_aes')) {
            wp_add_dashboard_widget(
                'wp_widget_pending_payments', // Unique ID for your widget
                'Payments Status', // Title displayed in the widget
                'wp_widget_pending_payments_callback' // Callback function for content
            );
        }

        // Check if the current user has the 'manager_admission_aes' capability
        if (current_user_can('manager_admission_aes')) {
            wp_add_dashboard_widget(
                'wp_widget_documents_review', // Unique ID for your widget
                'Admission Status', // Title displayed in the widget
                'wp_widget_documents_review_callback' // Callback function for content
            );
        }

        // Check if the current user has the 'manager_admission_aes' capability
        if (current_user_can('manager_requests_aes')) {
            wp_add_dashboard_widget(
                'wp_widget_requests_review', // Unique ID for your widget
                'Requests Status', // Title displayed in the widget
                'wp_widget_requests_review_callback' // Callback function for content
            );
        }
    }
}
add_action('wp_dashboard_setup', 'wp_add_widgets_edusof');

function wp_widget_pending_payments_callback()
{
    // Initialize counts
    $pending_payments_count = 0;
    $payments_to_review_count = 0;

    if (class_exists('WC_Order_Query')) {
        // Query for 'pending payment' orders
        $pending_orders = wc_get_orders(array(
            'status' => 'pending', // 'pending' status usually refers to 'pending payment'
            'limit' => -1, // Get all orders with this status
            'return' => 'ids', // Return only order IDs for counting
        ));
        $pending_payments_count = count($pending_orders);

        // Query for 'on-hold' orders (often used for 'to review' or manual processing)
        $on_hold_orders = wc_get_orders(array(
            'status' => 'on-hold',
            'limit' => -1,
            'return' => 'ids',
        ));
        $payments_to_review_count = count($on_hold_orders);
    }

    $widget_data = array(
        'pending_payments_count' => $pending_payments_count,
        'payments_to_review_count' => $payments_to_review_count,
        'pending_payments_link' => admin_url('admin.php?page=add_admin_form_payments_content')
    );

    include(plugin_dir_path(__FILE__) . 'templates/widget-pending-payments.php');
}

function wp_widget_documents_review_callback()
{
    global $wpdb;
    $table_student_documents = $wpdb->prefix . 'student_documents';
    $pending_documents = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE `status`=1");
    $widget_data = array(
        'count' => count($pending_documents),
        'link' => admin_url('admin.php?page=add_admin_form_admission_content')
    );
    include(plugin_dir_path(__FILE__) . 'templates/widget-documents-review.php');
}

function wp_widget_requests_review_callback()
{
    global $wpdb;
    $table_requests = $wpdb->prefix . 'requests';
    $pending_requests = $wpdb->get_results("SELECT * FROM {$table_requests} WHERE `status_id`=0");
    $widget_data = array(
        'count' => count($pending_requests),
        'link' => admin_url('admin.php?page=add_admin_form_requests_content')
    );
    include(plugin_dir_path(__FILE__) . 'templates/widget-requests-review.php');
}