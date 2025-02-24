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
            $virtual_access = sanitize_text_field($_POST['virtual_access']) ?? get_option('virtual_access');
            $auto_enroll_elective = sanitize_text_field($_POST['auto_enroll_elective']) ?? get_option('auto_enroll_elective');
            update_option('payment_due', $payment_due);
            update_option('virtual_access', $virtual_access);
            update_option('auto_enroll_elective', $auto_enroll_elective);

            // moodle
            $moodle_url = sanitize_text_field($_POST['moodle_url']) ?? get_option('moodle_url');
            $moodle_token = sanitize_text_field($_POST['moodle_token']) ?? get_option('moodle_token');
            update_option('moodle_url', $moodle_url);
            update_option('moodle_token', $moodle_token);

            // offers
            $offer_complete = sanitize_text_field($_POST['offer_complete']) ?? get_option('offer_complete');
            $offer_quote = sanitize_text_field($_POST['offer_quote']) ?? get_option('offer_quote');
            update_option('offer_complete', $offer_complete);
            update_option('offer_quote', $offer_quote);

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

    include(plugin_dir_path(__FILE__).'templates/configuration-options.php');
}

// Add a success message if the options have been saved
function add_success_message() {
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