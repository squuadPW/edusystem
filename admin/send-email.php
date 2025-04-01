<?php

function add_admin_form_send_email_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_students = $wpdb->prefix . 'students';

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'send_email') {

            if ($_POST['type'] == '1') {
                $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
                $academic_period = $_POST['academic_period'];
                $cut = $_POST['academic_period_cut'];
                if ($_POST['academic_period_cut_filter'] == 1) {
                    $cut_student_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE academic_period = '{$academic_period}' AND initial_cut = '$cut'");
                } else {
                    $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '{$academic_period}' AND cut_period = '{$cut}'");
                    $cut_student_ids = array_merge($cut_student_ids, $wpdb->get_col("SELECT id FROM {$table_students} WHERE elective = 1"));
                }

                $subject = wp_kses_post($_POST['subject']);
                $message = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';

                $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', $cut_student_ids) . ")");
                $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];

                foreach ($students as $key => $student) {
                    $message = set_variables_message($message, $student);
                    $email_student->trigger($student, $subject, $message);

                    if (isset($_POST['email_parent']) && $_POST['email_parent'] == 'on') {
                        $parent = get_user_by('id', $student->partner_id);
                        $email_user->trigger($parent, $subject, $message);
                    }
                }

                setcookie('message', __('Email sent successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                exit;
            } else {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email = '" . $_POST['email_student'] . "'");
                if ($student) {

                    $subject = wp_kses_post($_POST['subject']);
                    $message = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';
                    $message = set_variables_message($message, $student);
                    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                    $email_student->trigger($student, $subject, $message);

                    if ($_POST['email_parent'] == 'on') {
                        $parent = get_user_by('id', $student->partner_id);

                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger($parent, $subject, $message);
                    }

                    setcookie('message', __('Email sent successfully.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                    exit;
                } else {
                    setcookie('message-error', __("This student don't exist.", 'edusystem'), time() + 3600, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
                    exit;
                }
            }

        } else if ($_GET['action'] == 'send_pending_payments_email') {
            send_pending_payments_email();
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content'));
        }
    }

    $variables = get_variables_documents() ?? [];
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    include(plugin_dir_path(__FILE__) . 'templates/send-email.php');
}

function send_pending_payments_email()
{
    $orders = wc_get_orders(array(
        'status' => 'pending-payment'
    ));

    $sent_customers = array(); // Almacena IDs de clientes ya notificados

    foreach ($orders as $key => $order) {
        $customer_id = $order->get_customer_id();

        // Verificar si ya se notificó a este cliente
        if (!in_array($customer_id, $sent_customers)) {
            $user_customer = get_user_by('id', $customer_id);

            if ($user_customer) {
                $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                $email_user->trigger(
                    $user_customer,
                    'You have pending payments',
                    'We invite you to log in to our platform as soon as possible to make your pending payments and avoid being suspended from the virtual classroom.'
                );

                // Registrar al cliente como notificado
                $sent_customers[] = $customer_id;
            }
        }
    }
}

function set_variables_message($message, $student)
{

    $load = load_current_cut();
    $academic_period = get_period_details_code($load['code']);
    $start_academic_period = date('F d, Y', strtotime($academic_period->start_date));
    $end_academic_period = date('F d, Y', strtotime($academic_period->end_date));
    $span_open = '<span class="text-uppercase">';
    $span_close = '</span>';
    $academic_period_name = $academic_period->name;
    if (strpos($message, '{{academic_year}}') !== false) {
        $message = str_replace('{{academic_year}}', $span_open . $academic_period_name . $span_close, $message);
    }

    if (strpos($message, '{{start_academic_year}}') !== false) {
        $message = str_replace('{{start_academic_year}}', $start_academic_period, $message);
    }

    if (strpos($message, '{{end_academic_year}}') !== false) {
        $message = str_replace('{{end_academic_year}}', $end_academic_period, $message);
    }

    $student_name = $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name;
    if (strpos($message, '{{student_name}}') !== false) {
        $message = str_replace('{{student_name}}', $span_open . $student_name . $span_close, $message);
    }

    $student_short_name = $student->name . ' ' . $student->last_name;
    if (strpos($message, '{{student_short_name}}') !== false) {
        $message = str_replace('{{student_short_name}}', $span_open . $student_short_name . $span_close, $message);
    }

    $id_student = $student->id_document;
    if (strpos($message, '{{id_student}}') !== false) {
        $message = str_replace('{{id_student}}', $span_open . $id_student . $span_close, $message);
    }

    $program = get_name_program($student->program_id);
    if (strpos($message, '{{program}}') !== false) {
        $message = str_replace('{{program}}', $span_open . $program . $span_close, $message);
    }

    $today = date('M d, Y');
    if (strpos($message, '{{today}}') !== false) {
        $message = str_replace('{{today}}', $today, $message);
    }

    if (strpos($message, '{{table_notes}}') !== false) {
        $message = str_replace('{{table_notes}}', table_notes_html($student->id), $message);
    }

    if (strpos($message, '{{table_notes_summary}}') !== false) {
        $message = str_replace('{{table_notes_summary}}', table_notes_summary_html($student->id), $message);
    }

    return $message;
}

function get_summary_email()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $academic_period = $_POST['academic_period'];
    $cut = $_POST['cut'];
    $filter = $_POST['filter'];
    $email_student = $_POST['email_student'];
    $type = $_POST['type'];
    $students = [];

    if ($type == 1) {
        if ($filter == 1) {
            $cut_student_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE academic_period = '{$academic_period}' AND initial_cut = '$cut'");
        } else {
            $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '{$academic_period}' AND cut_period = '{$cut}'");
            $cut_student_ids = array_merge($cut_student_ids, $wpdb->get_col("SELECT id FROM {$table_students} WHERE elective = 1"));
        }
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', $cut_student_ids) . ")");
    } else {
        $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE email = '{$email_student}'");
    }

    wp_send_json(array('success' => true, 'students' => $students));
    die();
}

add_action('wp_ajax_nopriv_summary_email', 'get_summary_email');
add_action('wp_ajax_summary_email', 'get_summary_email');