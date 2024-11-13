<?php

function add_admin_form_send_notification_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix.'academic_periods';
    $table_students = $wpdb->prefix.'students';

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'send_notification') {

            for ($i=0; $i < 3; $i++) { 
                $email = '';
                switch ($i) {
                    case 0:
                        $email = get_option('email_1');
                        break;
                    case 1:
                        $email = get_option('email_2');
                        break;
                    case 2:
                        $email = get_option('email_3');
                        break;
                }
                $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
                $sender_email->trigger($email, $_POST['subject'], $_POST['message']);
            }

            setcookie('message', __('Email sent successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_send_notification_content'));
            exit;
        }
    }

    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include (plugin_dir_path(__FILE__) . 'templates/send-notification.php');
}