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
                $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE cut_period = '$cut'");
                $subject = wp_kses_post($_POST['subject']); // Sanitizar el asunto
                $message = wp_kses_post($_POST['message']); // Sanitizar el mensaje

                $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE academic_period = " . $_POST['academic_period'] . " WHERE IN (" . $cut_student_ids . ")");
                foreach ($students as $key => $student) {
                    $email_welcome_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                    $email_welcome_student->trigger($student, $subject, $message);
                }

                setcookie('message', __('Email sent successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                exit;
            } else {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email = '" . $_POST['email_student'] . "'");
                if ($student) {
                    $email_welcome_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];

                    // Asegúrate de que el asunto y el mensaje estén correctamente definidos y que el mensaje sea HTML
                    $subject = wp_kses_post($_POST['subject']); // Sanitizar el asunto
                    $message = wp_kses_post($_POST['message']); // Sanitizar el mensaje
                    
                    // Si el mensaje contiene HTML, puedes usar el siguiente código para enviar el correo
                    $email_welcome_student->trigger($student, $subject, $message, true); // El último parámetro indica que es HTML

                    if ($_POST['email_parent'] == 'on') {
                        $parent = get_user_by('id', $student->partner_id);

                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger($parent, $subject, $message);
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