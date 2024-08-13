<?php

function add_admin_form_send_email_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix.'academic_periods';
    $table_students = $wpdb->prefix.'students';

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'send_email') {

            $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE academic_period = " . $_POST['academic_period']);
            foreach ($students as $key => $student) {
                $email_welcome_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                $email_welcome_student->trigger($student, $_POST['subject'], $_POST['message']);
            }

            setcookie('message', __('Email sent successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
            exit;
        }
    }

    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include (plugin_dir_path(__FILE__) . 'templates/send-email.php');
}