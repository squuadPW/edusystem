<?php

function add_admin_form_send_email_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix.'academic_periods';
    $table_students = $wpdb->prefix.'students';

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'send_email') {

            if ($_POST['type'] == '1') {
                $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
                $cut = $_POST['academic_period_cut'];
                if ($_POST['academic_period_cut_filter'] == 1) {
                    $cut_student_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE initial_cut = '$cut'");
                } else {
                    $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE cut_period = '$cut'");
                }

                $subject = wp_kses_post($_POST['subject']); // Sanitizar el asunto
                $message = wp_kses_post($_POST['message']); // Sanitizar el mensaje

                $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE academic_period = '" . $_POST['academic_period'] . "' AND id IN (" . implode(',', $cut_student_ids) . ")");
                $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];

                foreach ($students as $key => $student) {
                    $email_student->trigger($student, $subject, $message, true);

                    if (isset($_POST['email_parent']) && $_POST['email_parent'] == 'on') {
                        $parent = get_user_by('id', $student->partner_id);
                        $email_user->trigger($parent, $subject, $message, true);
                    }
                }

                setcookie('message', __('Email sent successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                exit;
            } else {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email = '" . $_POST['email_student'] . "'");
                if ($student) {

                    $subject = wp_kses_post($_POST['subject']);
                    $message = wp_kses_post($_POST['message']); 

                    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                    $email_student->trigger($student, $subject, $message, true);

                    if ($_POST['email_parent'] == 'on') {
                        $parent = get_user_by('id', $student->partner_id);

                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger($parent, $subject, $message, true);
                    }

                    setcookie('message', __('Email sent successfully.', 'aes'), time() + 3600, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                    exit;
                } else {
                    setcookie('message-error', __("This student don't exist.", 'aes'), time() + 3600, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                    exit;
                }
            }

        }
    }

    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include (plugin_dir_path(__FILE__) . 'templates/send-email.php');
}