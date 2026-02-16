<?php

function add_admin_form_configuration_options_content()
{
    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'save_options') {

            // admission
            $documents_ok = sanitize_text_field($_POST['documents_ok']) ?? get_option('documents_ok');
            $documents_warning = sanitize_text_field($_POST['documents_warning']) ?? get_option('documents_warning');
            $documents_red = sanitize_text_field($_POST['documents_red']) ?? get_option('documents_red');
            update_option('documents_ok', $documents_ok);
            update_option('documents_warning', $documents_warning);
            update_option('documents_red', $documents_red);

            // administration
            $payment_due = sanitize_text_field($_POST['payment_due']) ?? get_option('payment_due');
            $proof_due = sanitize_text_field($_POST['proof_due']) ?? get_option('proof_due');
            $multiple_accounts = sanitize_text_field($_POST['multiple_accounts']) ?? get_option('multiple_accounts');
            $virtual_access = sanitize_text_field($_POST['virtual_access']) ?? get_option('virtual_access');
            $auto_enroll_elective = sanitize_text_field($_POST['auto_enroll_elective']) ?? get_option('auto_enroll_elective');
            $auto_enroll_regular = sanitize_text_field($_POST['auto_enroll_regular']) ?? get_option('auto_enroll_regular');
            $use_elective_aditional = sanitize_text_field($_POST['use_elective_aditional']) ?? get_option('use_elective_aditional');
            $show_modal_electives = sanitize_text_field($_POST['show_modal_electives']) ?? get_option('show_modal_electives');
            $show_equivalence_projection = sanitize_text_field($_POST['show_equivalence_projection']) ?? get_option('show_equivalence_projection');
            $show_table_subjects_coursing = sanitize_text_field($_POST['show_table_subjects_coursing']) ?? get_option('show_table_subjects_coursing');
            $disabled_redirect = sanitize_text_field($_POST['disabled_redirect']) ?? get_option('disabled_redirect');
            $disable_switch_language = sanitize_text_field($_POST['disable_switch_language']) ?? get_option('disable_switch_language');
            $hide_grade_student = sanitize_text_field($_POST['hide_grade_student']) ?? get_option('hide_grade_student');
            $hide_grades_names = sanitize_text_field($_POST['hide_grades_names']) ?? get_option('hide_grades_names');
            $hide_contact_section_email = sanitize_text_field($_POST['hide_contact_section_email']) ?? get_option('hide_contact_section_email');
            $auto_approve_institute = sanitize_text_field($_POST['auto_approve_institute']) ?? get_option('auto_approve_institute');
            $hide_fee_checkbox_checkout = sanitize_text_field($_POST['hide_fee_checkbox_checkout']) ?? get_option('hide_fee_checkbox_checkout');
            $default_lang_site = sanitize_text_field($_POST['default_lang_site']) ?? get_option('default_lang_site');
            $site_mode = sanitize_text_field($_POST['site_mode']) ?? get_option('site_mode');
            update_option('payment_due', $payment_due);
            update_option('proof_due', $proof_due);
            update_option('virtual_access', $virtual_access);
            update_option('multiple_accounts', $multiple_accounts);
            update_option('auto_enroll_elective', $auto_enroll_elective);
            update_option('auto_enroll_regular', $auto_enroll_regular);
            update_option('use_elective_aditional', $use_elective_aditional);
            update_option('show_modal_electives', $show_modal_electives);
            update_option('show_equivalence_projection', $show_equivalence_projection);
            update_option('show_table_subjects_coursing', $show_table_subjects_coursing);
            update_option('disabled_redirect', $disabled_redirect);
            update_option('disable_switch_language', $disable_switch_language);
            update_option('hide_grade_student', $hide_grade_student);
            update_option('hide_grades_names', $hide_grades_names);
            update_option('hide_contact_section_email', $hide_contact_section_email);
            update_option('hide_fee_checkbox_checkout', $hide_fee_checkbox_checkout);
            update_option('auto_approve_institute', $auto_approve_institute);
            update_option('default_lang_site', $default_lang_site);
            update_option('site_mode', $site_mode);

            // moodle
            $moodle_url = sanitize_text_field($_POST['moodle_url']) ?? get_option('moodle_url');
            $moodle_token = sanitize_text_field($_POST['moodle_token']) ?? get_option('moodle_token');
            $public_course_id = sanitize_text_field($_POST['public_course_id']) ?? get_option('public_course_id');
            update_option('moodle_url', $moodle_url);
            update_option('moodle_token', $moodle_token);
            update_option('public_course_id', $public_course_id);

            // crm
            $crm_url = sanitize_text_field($_POST['crm_url']) ?? get_option('crm_url');
            $crm_token = sanitize_text_field($_POST['crm_token']) ?? get_option('crm_token');
            update_option('crm_url', $crm_url);
            update_option('crm_token', $crm_token);

            // offers
            try {
                // Recuperar y sanitizar los valores de los campos de texto
                $offer_complete = sanitize_text_field($_POST['offer_complete'] ?? get_option('offer_complete'));
                $offer_quote = sanitize_text_field($_POST['offer_quote'] ?? get_option('offer_quote'));
                update_option('offer_complete', $offer_complete);
                update_option('offer_quote', $offer_quote);

                // --- Manejo de la fecha de la oferta ---
                if (isset($_POST['max_date_offer']) && !empty($_POST['max_date_offer'])) {
                    $date_string = sanitize_text_field($_POST['max_date_offer']);
                    $timezone = new DateTimeZone(wp_timezone_string());
                    $expiration_date = DateTime::createFromFormat('Y-m-d H:i:s', $date_string . ' 23:59:59', $timezone);

                    if ($expiration_date === false) {
                        wc_add_notice(__('Invalid date format for max date offer.', 'your-text-domain'), 'error');
                        return;
                    }

                    $expiration_timestamp = $expiration_date->getTimestamp();
                    update_option('max_date_offer', $expiration_timestamp);

                    $coupons = array_filter([$offer_complete, $offer_quote]);
                    foreach ($coupons as $coupon_code) {
                        if (!empty($coupon_code)) {
                            $coupon = new WC_Coupon($coupon_code);
                            if ($coupon->get_id()) {
                                $coupon->set_date_expires($expiration_timestamp);
                                $coupon->save();
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Log error and handle gracefully
                wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content&error=' . urlencode($e->getMessage())));
                exit;
            }

            // notifications
            $email_coordination = sanitize_text_field($_POST['email_coordination']);
            $email_administration = sanitize_text_field($_POST['email_administration']);
            $email_academic_management = sanitize_text_field($_POST['email_academic_management']);
            $email_manager = sanitize_text_field($_POST['email_manager']);
            $email_admission = sanitize_text_field($_POST['email_admission']);
            update_option('email_coordination', $email_coordination);
            update_option('email_administration', $email_administration);
            update_option('email_academic_management', $email_academic_management);
            update_option('email_manager', $email_manager);
            update_option('email_admission', $email_admission);

            // design
            if (isset($_FILES['logo_admin']) && !empty($_FILES['logo_admin'])) {
                $file_temp_logo = $_FILES['logo_admin'];
            } else {
                $file_temp = [];
            }

            if (!empty($file_temp_logo['tmp_name'])) {
                $upload_data = wp_handle_upload($file_temp_logo, array('test_form' => FALSE));
                if ($upload_data && !is_wp_error($upload_data)) {
                    $logo_attach = upload_file_attchment_edusystem($upload_data, 'LOGO ADMIN');
                }
            }

            if ($logo_attach) {
                update_option('logo_admin', $logo_attach);
            }

            if (isset($_FILES['logo_admin_login']) && !empty($_FILES['logo_admin_login'])) {
                $file_temp_logo_login = $_FILES['logo_admin_login'];
            } else {
                $file_temp = [];
            }

            if (!empty($file_temp_logo_login['tmp_name'])) {
                $upload_data = wp_handle_upload($file_temp_logo_login, array('test_form' => FALSE));
                if ($upload_data && !is_wp_error($upload_data)) {
                    $logo_attach_login = upload_file_attchment_edusystem($upload_data, 'LOGO ADMIN');
                }
            }

            if ($logo_attach_login) {
                update_option('logo_admin_login', $logo_attach_login);
            }

            // others
            $default_price_electives = sanitize_text_field($_POST['default_price_electives']);
            $default_price_regular_courses = sanitize_text_field ($_POST['default_price_regular_courses']);
            update_option('default_price_electives', $default_price_electives);
            update_option('default_price_regular_courses', $default_price_regular_courses);

            // Redirect to the same page with a success message
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content&success=true'));
        }
    }

    // En este punto cargo los cursos de moodle
    $raw_moodle_response = get_courses_moodle();
    if (is_array($raw_moodle_response) && isset($raw_moodle_response['exception'])) {
        $courses = [];
    } else {
        $courses = $raw_moodle_response;
    }

    // Ahora $courses es SIEMPRE un array (o un array de cursos o un array vacío)
    include(plugin_dir_path(__FILE__) . 'templates/configuration-options.php');
}

// Add a success message if the options have been saved
function add_success_message()
{
    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Options saved successfully!</p>
        </div>
        <?php
    }
}

// Add the success message to the admin_notices action
add_action('admin_notices', 'add_success_message');