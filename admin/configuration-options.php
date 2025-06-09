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
            $virtual_access = sanitize_text_field($_POST['virtual_access']) ?? get_option('virtual_access');
            $auto_enroll_elective = sanitize_text_field($_POST['auto_enroll_elective']) ?? get_option('auto_enroll_elective');
            $auto_enroll_regular = sanitize_text_field($_POST['auto_enroll_regular']) ?? get_option('auto_enroll_regular');
            $use_elective_aditional = sanitize_text_field($_POST['use_elective_aditional']) ?? get_option('use_elective_aditional');
            $show_modal_electives = sanitize_text_field($_POST['show_modal_electives']) ?? get_option('show_modal_electives');
            $show_equivalence_projection = sanitize_text_field($_POST['show_equivalence_projection']) ?? get_option('show_equivalence_projection');
            $show_table_subjects_coursing = sanitize_text_field($_POST['show_table_subjects_coursing']) ?? get_option('show_table_subjects_coursing');
            update_option('payment_due', $payment_due);
            update_option('proof_due', $proof_due);
            update_option('virtual_access', $virtual_access);
            update_option('auto_enroll_elective', $auto_enroll_elective);
            update_option('auto_enroll_regular', $auto_enroll_regular);
            update_option('use_elective_aditional', $use_elective_aditional);
            update_option('show_modal_electives', $show_modal_electives);
            update_option('show_equivalence_projection', $show_equivalence_projection);
            update_option('show_table_subjects_coursing', $show_table_subjects_coursing);

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
                $offer_complete = sanitize_text_field($_POST['offer_complete'] ?? get_option('offer_complete'));
                $offer_quote = sanitize_text_field($_POST['offer_quote'] ?? get_option('offer_quote'));
                $max_date_offer = sanitize_text_field($_POST['max_date_offer'] ?? get_option('max_date_offer'));

                // Update WordPress options
                update_option('offer_complete', $offer_complete);
                update_option('offer_quote', $offer_quote);

                // Set expiration date for coupons
                $timezone = new DateTimeZone(wp_timezone_string());
                $expiration_date = DateTime::createFromFormat('Y-m-d', $max_date_offer, $timezone);

                if ($expiration_date === false) {
                    throw new Exception('Invalid date format for max_date_offer');
                }

                $expiration_date->setTime(23, 59, 59);
                $expiration_timestamp = $expiration_date->getTimestamp();

                // Update coupons expiration dates
                $coupons = array_filter([$offer_complete, $offer_quote]);
                foreach ($coupons as $coupon_code) {
                    if (!empty($coupon_code)) {
                        $coupon = new WC_Coupon($coupon_code);
                        $coupon->set_date_expires($expiration_timestamp);
                        $coupon->save();
                    }
                }

                update_option('max_date_offer', $expiration_timestamp);
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

            // Redirect to the same page with a success message
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content&success=true'));
        }
    }

    $courses = get_courses_moodle();
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