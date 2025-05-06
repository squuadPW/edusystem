<?php

function add_admin_form_send_email_content()
{
    global $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_templates_email = $wpdb->prefix . 'templates_email';

    if (isset($_GET['action']) && $_GET['action'] == 'send_email' && isset($_POST['type'])) {
        if (handle_email_sending($_POST['type'], $_POST)) {
            wp_redirect(admin_url('admin.php?page=add_admin_form_send_email_content'));
            exit;
        }
    }

    $variables = get_variables_documents() ?? [];
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    $templates = $wpdb->get_results("SELECT * FROM {$table_templates_email} ORDER BY id ASC");
    include(plugin_dir_path(__FILE__) . 'templates/send-email.php');
}

function send_pending_payments_email()
{
    global $wpdb;
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    
    // Obtener la fecha actual y la fecha con 3 semanas menos
    $current_date = current_time('mysql');
    $three_weeks_from_now = date('Y-m-d', strtotime($current_date . ' -3 weeks'));
    
    // Consultar pagos pendientes que vencen con 3 semanas menos
    $student_payments = $wpdb->get_results($wpdb->prepare(
        "SELECT sp.*, s.email, s.name, s.last_name, s.partner_id
         FROM {$table_student_payments} sp
         JOIN {$table_students} s ON sp.student_id = s.id
         WHERE sp.status = 0 
         AND sp.date_next_payment BETWEEN %s AND %s",
        $three_weeks_from_now,
        $current_date
    ));

    $sent_customers = [];
    foreach ($student_payments as $payment) {
        $customer_id = $payment->partner_id;
        if (!in_array($customer_id, $sent_customers) && $customer_id > 0) { // Asegúrate de que el ID del cliente sea válido
            $user_customer = get_user_by('id', $customer_id);

            if ($user_customer) {
                // Preparar el mensaje con la fecha de vencimiento
                $message = sprintf(
                    'We hope you are well. We remind you that your payment of %s is due on %s.',
                    wc_price($payment->amount),
                    date('F j, Y', strtotime($payment->date_next_payment))
                );

                $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                $email_user->trigger(
                    $user_customer,
                    'Reminder: Payment of installments due soon',
                    $message
                );

                // Registrar al cliente como notificado
                $sent_customers[] = $customer_id;
            }
        }
    }
}

function set_variables_message($message, $student, $code_period = null, $cut_period = null)
{
    $replacements = get_replacements_variables($student, $code_period, $cut_period);
    $message = process_template($message, $replacements);

    return $message;
}

function get_students_by_period($academic_period, $cut, $filter) {
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    if ($filter == 1) {
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_students} WHERE academic_period = %s AND initial_cut = %s",
            $academic_period,
            $cut
        ));
    } else {
        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s",
            $academic_period,
            $cut
        ));
        $elective_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE elective = 1");
        $all_ids = array_merge($cut_student_ids, $elective_ids);
        
        if (empty($all_ids)) {
            return [];
        }
        
        return $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', array_map('intval', $all_ids)) . ")");
    }
}

function get_student_by_email($email) {
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_students} WHERE email = %s",
        $email
    ));
}

function get_user_by_email($email) {
    $user = get_user_by('email', $email);
    if ($user) {
        $user_obj = new stdClass();
        $user_obj->id = $user->ID;
        $user_obj->email = $user->user_email;
        $user_obj->user_email = $user->user_email;
        $user_obj->name = $user->first_name;
        $user_obj->last_name = $user->last_name;
        $user_obj->username = $user->user_login;
        return $user_obj;
    }
    return null;
}

function get_alliance_by_email($email) {
    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_alliances} WHERE email = %s",
        $email
    ));
}

function get_institute_by_email($email) {
    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_institutes} WHERE email = %s",
        $email
    ));
}

function get_active_alliances() {
    global $wpdb;
    $table_alliances = $wpdb->prefix . 'alliances';
    return $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 1");
}

function get_active_institutes() {
    global $wpdb;
    $table_institutes = $wpdb->prefix . 'institutes';
    return $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 1");
}

function get_summary_email() {
    if (!isset($_POST['type'])) {
        wp_send_json(['success' => false, 'message' => 'Missing required parameters']);
        return;
    }

    $type = $_POST['type'];
    $data = [];

    switch ($type) {
        case '1':
            if (!isset($_POST['academic_period']) || !isset($_POST['cut']) || !isset($_POST['filter'])) {
                wp_send_json(['success' => false, 'message' => 'Missing required parameters for type 1']);
                return;
            }
            $data = get_students_by_period(
                $_POST['academic_period'],
                $_POST['cut'],
                $_POST['filter']
            );
            break;

        case '2':
            if (!isset($_POST['email_student'])) {
                wp_send_json(['success' => false, 'message' => 'Missing email parameter']);
                return;
            }
            
            $email = $_POST['email_student'];
            $user = get_user_by_email($email);
            
            $data = [];
            if ($user) {
                $data[] = $user;
            }
            
            if (empty($data)) {
                wp_send_json(['success' => false, 'message' => 'No records found with this email']);
                return;
            }
            break;

        case '3':
            $data = get_active_alliances();
            break;

        case '4':
            $data = get_active_institutes();
            break;

        default:
            wp_send_json(['success' => false, 'message' => 'Invalid type parameter']);
            return;
    }

    wp_send_json(['success' => true, 'students' => $data]);
}

add_action('wp_ajax_nopriv_summary_email', 'get_summary_email');
add_action('wp_ajax_summary_email', 'get_summary_email');

function send_email_to_students($students, $subject, $message, $academic_period = null, $cut = null, $send_to_parent = false) {
    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
    $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];

    foreach ($students as $student) {
        $message_student = set_variables_message($message, $student, $academic_period, $cut);
        $email_student->trigger($student, $subject, $message_student);

        if ($send_to_parent && !empty($student->partner_id)) {
            $parent = get_user_by('id', $student->partner_id);
            if ($parent) {
                $email_user->trigger($parent, $subject, $message_student);
            }
        }
    }
}

function save_email_template($subject, $message) {
    global $wpdb;
    $table_templates_email = $wpdb->prefix . 'templates_email';
    return $wpdb->insert($table_templates_email, [
        'title' => $subject,
        'content' => $message
    ]);
}

function handle_email_sending($type, $post_data) {
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_alliances = $wpdb->prefix . 'alliances';
    $table_institutes = $wpdb->prefix . 'institutes';

    $subject = wp_unslash($post_data['subject']);
    $message = isset($post_data['message']) ? wp_unslash($post_data['message']) : '';
    $send_to_parent = isset($post_data['email_parent']) && $post_data['email_parent'] == 'on';
    $save_template = isset($post_data['save_template']) && $post_data['save_template'] == 'on';

    switch ($type) {
        case '1':
            $academic_period = $post_data['academic_period'];
            $cut = $post_data['academic_period_cut'];
            
            if ($post_data['academic_period_cut_filter'] == 1) {
                $cut_student_ids = $wpdb->get_col($wpdb->prepare(
                    "SELECT id FROM {$table_students} WHERE academic_period = %s AND initial_cut = %s",
                    $academic_period,
                    $cut
                ));
            } else {
                $cut_student_ids = $wpdb->get_col($wpdb->prepare(
                    "SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s",
                    $academic_period,
                    $cut
                ));
                $elective_ids = $wpdb->get_col("SELECT id FROM {$table_students} WHERE elective = 1");
                $cut_student_ids = array_merge($cut_student_ids, $elective_ids);
            }

            $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN (" . implode(',', array_map('intval', $cut_student_ids)) . ")");
            send_email_to_students($students, $subject, $message, $academic_period, $cut, $send_to_parent);
            break;

        case '2':
            if (!isset($_POST['email_student'])) {
                wp_send_json(['success' => false, 'message' => 'Missing email parameter']);
                return;
            }
            
            $email = $_POST['email_student'];
            $student = get_student_by_email($email);
            $user = get_user_by_email($email);
            
            $data = [];
            if ($student && $user->user_email == $student->email) {
                $data[] = $student;
                $message_student = set_variables_message($message, $student);
                send_email_to_students([$student], $subject, $message_student, null, null, $send_to_parent);
            }
            if ($user && (!$student || $user->user_email != $student->email)) {
                $data[] = $user;
                if ($user) {
                    $message_user = set_variables_message($message, $user);
                    $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                    $email_user->trigger($user, $subject, $message_user);
                }
            }
            
            if (empty($data)) {
                wp_send_json(['success' => false, 'message' => 'No records found with this email']);
                return;
            }
            break;

        case '3':
            $alliances = $wpdb->get_results("SELECT * FROM {$table_alliances} WHERE status = 1");
            foreach ($alliances as $alliance) {
                $user_alliance = get_user_by('email', $alliance->email);
                if ($user_alliance) {
                    $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                    $email_user->trigger($user_alliance, $subject, $message);
                }
            }
            break;

        case '4':
            $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE status = 1");
            foreach ($institutes as $institute) {
                $user_institute = get_user_by('email', $institute->email);
                if ($user_institute) {
                    $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                    $email_user->trigger($user_institute, $subject, $message);
                }
            }
            break;
    }

    if ($save_template) {
        save_email_template($subject, $message);
    }

    setcookie('message', __('Email sent successfully.', 'edusystem'), time() + 10, '/');
    return true;
}