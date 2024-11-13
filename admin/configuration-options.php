<?php

function add_admin_form_configuration_options_content()
{
    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'save_options') {

            // admission
            $documents_ok = sanitize_text_field($_POST['documents_ok']) ?? get_option('documents_ok');
            $documents_warning = sanitize_text_field($_POST['documents_warning']) ?? get_option('documents_warning');
            $documents_red = sanitize_text_field($_POST['documents_red']) ?? get_option('documents_red');

            // administration
            $payment_due = sanitize_text_field($_POST['payment_due']) ?? get_option('payment_due');
            $student_continue = sanitize_text_field($_POST['student_continue']) ?? get_option('student_continue');

            // notifications
            $email_1 = sanitize_text_field($_POST['email_1']) ?? get_option('email_1');
            $email_2 = sanitize_text_field($_POST['email_2']) ?? get_option('email_2');
            $email_3 = sanitize_text_field($_POST['email_3']) ?? get_option('email_3');

            // Update the options
            update_option('documents_ok', $documents_ok);
            update_option('documents_warning', $documents_warning);
            update_option('documents_red', $documents_red);
            update_option('payment_due', $payment_due);
            update_option('student_continue', $student_continue);
            update_option('email_1', $email_1);
            update_option('email_2', $email_2);
            update_option('email_3', $email_3);
        
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