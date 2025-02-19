<?php

function add_admin_form_send_notification_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix.'academic_periods';
    $table_students = $wpdb->prefix.'students';

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'send_notification') {

            send_notification_staff($_POST['subject'], $_POST['message']);
            setcookie('message', __('Email sent successfully.', 'aes'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_send_notification_content'));
            exit;
        }
    }

    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include (plugin_dir_path(__FILE__) . 'templates/send-notification.php');
}

function send_notification_staff($subject, $message) {
    for ($i=0; $i < 5; $i++) { 
        $email = '';
        switch ($i) {
            case 0:
                $email = get_option('email_coordination');
                break;
            case 1:
                $email = get_option('email_academic_management');
                break;
            case 2:
                $email = get_option('email_manager');
                break;
            case 3:
                $email = get_option('email_administration');
                break;
            case 4:
                $email = get_option('email_admission');
                break;
        }
        if ($email && !empty($email)) {
            $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
            $sender_email->trigger($email, $subject, $message);
        }
    }
}

function send_notification_staff_particular($subject, $message, $i) {
    $email = '';
    switch ($i) {
        case 0:
            $email = get_option('email_coordination');
            break;
        case 1:
            $email = get_option('email_academic_management');
            break;
        case 2:
            $email = get_option('email_manager');
            break;
        case 3:
            $email = get_option('email_administration');
            break;
        case 4:
            $email = get_option('email_admission');
            break;
    }
    if ($email && !empty($email)) {
        $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
        $sender_email->trigger($email, $subject, $message);
    }
}