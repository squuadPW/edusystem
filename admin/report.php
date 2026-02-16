<?php

function tt_add_active_students_per_page_option()
{
    $screen = get_current_screen();

    if (!is_object($screen) || $screen->id !== 'report_page_report-students') {
        return;
    }

    $args = array(
        'label' => __('Students to list', 'edusystem'),
        'default' => 20,
        'option' => 'tt_students_per_page' // This is the unique key
    );

    add_screen_option('per_page', $args);
}
add_action('load-report_page_report-students', 'tt_add_active_students_per_page_option');

function tt_save_students_per_page_option($status, $option, $value)
{
    if ('tt_students_per_page' === $option) {
        return $value;
    }
    return $status;
}
add_filter('set-screen-option', 'tt_save_students_per_page_option', 10, 3);

function add_admin_form_report_content()
{
    include(plugin_dir_path(__FILE__) . 'templates/report-blade.php');
}

function show_report_sales()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'payment-detail') {
            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }
    } else {
        global $current_user;
        include(plugin_dir_path(__FILE__) . 'templates/report-sales.php');
    }
}

function show_report_sales_product()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'payment-detail') {
            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }
    } else {
        global $current_user;
        include(plugin_dir_path(__FILE__) . 'templates/report-sales-product.php');
    }
}

function show_report_accounts_receivables()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'payment-detail') {
            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }
    } else {
        global $current_user;
        include(plugin_dir_path(__FILE__) . 'templates/report-accounts-receivables.php');
    }
}

function show_report_students()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'payment-detail') {
            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['payment_id'];
            $order = wc_get_order($order_id);
            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        }
    } else {
        global $current_user, $wpdb;
        $table_academic_periods = $wpdb->prefix . 'academic_periods';
        $table_grades = $wpdb->prefix . 'grades';
        $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
        $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");
        include(plugin_dir_path(__FILE__) . 'templates/report-students.php');
    }
}

function show_report_current_students()
{
    global $current_user, $wpdb;
    $table_academic_periods = $wpdb->prefix . 'academic_periods';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $table_grades = $wpdb->prefix . 'grades';
    $total_count_current = (int) get_students_current_count();
    $total_count_active = (int) get_students_active_count();
    $total_count_pending_electives = (int) get_students_pending_elective_count();
    $total_count_non_enrolled = (int) get_students_non_enrolled_count();
    $total_count_pending_graduation = (int) get_students_pending_graduation_count();
    $total_count_pending_documents = (int) get_students_pending_documents_count();
    $total_count_graduated = (int) get_students_graduated_count();
    $total_count_retired = (int) get_students_retired_count();
    $total_count_pending_matrix = (int) get_students_pending_matrix_count();
    $total_count_scholarships = (int) get_students_scholarships_count();
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    $periods_cuts = $wpdb->get_results("SELECT * FROM {$table_academic_periods_cut}  ORDER BY created_at ASC");
    $load = load_current_cut();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");
    $countries = get_countries();
    $institutes = get_all_institutes_active();

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'documents_active_students') {
            $list_students = new TT_Documents_Active_Student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'enrollment_active_students') {
            $list_students = new TT_Enrollments_Active_Student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'current') {
            $list_students = new TT_Current_Student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'pending_electives') {
            $list_students = new TT_Pending_Elective_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'non-enrolled') {
            $list_students = new TT_Non_Enrolled_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'pending-documents') {
            $list_students = new TT_Pending_Documents_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'pending-graduation') {
            $list_students = new TT_Pending_Graduation_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'graduated') {
            $list_students = new TT_Graduated_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'retired') {
            $list_students = new TT_Retired_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'pending_matrix') {
            $list_students = new TT_Pending_Matrix_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'report_electives') {
            $list_students = new TT_Report_Electives_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'scholarships') {
            $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
            $scholarships_availables = $wpdb->get_results("SELECT * FROM {$table_scholarships_availables} ORDER BY id DESC");
            $list_students = new TT_Scholarships_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        }
    } else {
        $list_students = new TT_Active_Student_List_Table;
        $list_students->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
    }
}

function show_report_comissions()
{
    $date_array = array();
    if ($_POST['custom']) {
        $date = str_replace([' to ', ' a '], ',', $_POST['custom']);
        $date_array = explode(',', $date);
    }

    $filter = $_POST['typeFilter'] ?? 'this-month';
    $custom = $_POST['custom'] ?? false;
    $dates = get_dates_search($filter, $custom);
    $data = get_student_payments_table_data($dates[0], $dates[1]);
    $payments_data = $data['payments_data'];
    $alliances_headers = $data['alliances_headers'];

    $data_new = get_new_student_payments_table_data($dates[0], $dates[1]);
    $payments_data_new = $data_new['payments_data'];
    $alliances_headers_new = $data_new['alliances_headers'];

    $list_comissions_institute = new TT_Summary_Comissions_Institute_List_Table;
    $list_comissions_institute->prepare_items();

    $list_comissions_alliances = new TT_Summary_Comissions_Alliance_List_Table;
    $list_comissions_alliances->prepare_items();

    $list_comissions = new TT_Non_Enrolled_List_Table;
    $list_comissions->prepare_items();
    include(plugin_dir_path(__FILE__) . 'templates/report-comissions.php');
}

function show_report_billing_ranking()
{
    $date_array = array();
    if ($_POST['custom']) {
        $date = str_replace([' to ', ' a '], ',', $_POST['custom']);
        $date_array = explode(',', $date);
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'institutes') {
            $list_data = new TT_Ranking_Institutes_List_Table;
            $list_data->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-billing-ranking.php');
        }
    } else {
        $list_data = new TT_Ranking_Alliances_List_Table;
        $list_data->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/report-billing-ranking.php');
    }
}

// GET ORDERS
function get_orders($start, $end)
{
    global $wpdb;

    // Inicializar totales
    $institute_fee = 0.00;
    $alliance_fee = 0.00;
    $gross = 0.00;
    $discount = 0.00;
    $receivable = 0.00;
    $expense_amount = 0.00;
    $tax = 0.00;
    $fee_payment = 0.00;
    $fee_system = 0.00;
    $payment_methods = [];
    $data_fees = []; // Para almacenar los datos de las órdenes procesadas
    $cuotes_array = []; // Para almacenar las cuotas procesadas
    $expenses_array = []; // Para almacenar los gastos procesados

    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    // --- Obtención y Procesamiento de Órdenes ---
    $args = [
        'limit' => -1,
        'status' => 'wc-completed',
        'date_created' => $strtotime_start . '...' . $strtotime_end,
    ];
    $orders = wc_get_orders($args);

    // Pre-cargar datos de usuarios y estudiantes para evitar consultas N+1
    $customer_ids = [];
    $student_emails = [];
    $student_ids_from_meta = [];

    foreach ($orders as $order) {
        $customer_ids[] = $order->get_customer_id();
        $student_id_meta = $order->get_meta('student_id');
        if ($student_id_meta) {
            $student_ids_from_meta[] = $student_id_meta;
        }
    }

    // Obtener todos los clientes en una sola consulta Y SUS METADATOS
    $customers_data = [];
    if (!empty($customer_ids)) {
        $customer_ids_in = implode(',', array_filter(array_unique($customer_ids)));
        if ($customer_ids_in) {
            $users_results = $wpdb->get_results("SELECT ID, user_email FROM {$wpdb->users} WHERE ID IN ({$customer_ids_in})", OBJECT_K);

            // --- A J U S T E   C R U C I A L   A Q U Í ---
            $user_meta_data = [];
            if (!empty($users_results)) {
                $user_ids_for_meta = implode(',', array_keys($users_results));
                $meta_results = $wpdb->get_results(
                    "SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id IN ({$user_ids_for_meta}) AND meta_key IN ('first_name', 'last_name')",
                    OBJECT
                );
                foreach ($meta_results as $meta) {
                    $user_meta_data[$meta->user_id][$meta->meta_key] = $meta->meta_value;
                }
            }

            foreach ($users_results as $user_id => $user) {
                // Adjuntar los meta-datos al objeto de usuario
                $user->first_name = isset($user_meta_data[$user_id]['first_name']) ? $user_meta_data[$user_id]['first_name'] : '';
                $user->last_name = isset($user_meta_data[$user_id]['last_name']) ? $user_meta_data[$user_id]['last_name'] : '';
                $customers_data[$user_id] = $user;
            }
        }
    }

    // Obtener todos los estudiantes por ID en una sola consulta
    $students_data_by_id = [];
    if (!empty($student_ids_from_meta)) {
        $student_ids_in = implode(',', array_filter(array_unique($student_ids_from_meta)));
        if ($student_ids_in) {
            $table_students = $wpdb->prefix . 'students';
            $students_results = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN ({$student_ids_in})", OBJECT_K);
            foreach ($students_results as $student) {
                $students_data_by_id[$student->id] = $student;
                $student_emails[] = $student->email;
            }
        }
    }

    // Obtener todos los usuarios de estudiantes por email en una sola consulta
    $user_students_data = [];
    if (!empty($student_emails)) {
        $student_emails_in = "'" . implode("','", array_filter(array_unique($student_emails))) . "'";
        if ($student_emails_in !== "''") {
            $user_students_results = $wpdb->get_results("SELECT ID, user_email FROM {$wpdb->users} WHERE user_email IN ({$student_emails_in})", OBJECT_K);
            foreach ($user_students_results as $user_student) {
                $user_students_data[$user_student->user_email] = $user_student;
            }
        }
    }

    foreach ($orders as $order) {
        $customer = isset($customers_data[$order->get_customer_id()]) ? $customers_data[$order->get_customer_id()] : null;
        $student_id = $order->get_meta('student_id');
        $student = isset($students_data_by_id[$student_id]) ? $students_data_by_id[$student_id] : null;
        $user_student = $student && isset($user_students_data[$student->email]) ? $user_students_data[$student->email] : null;

        $data_fees[] = [
            'order_id' => $order->get_id(),
            'customer' => $customer,
            'student' => $student ? (array) $student : null,
            'student_id' => $user_student ? $user_student->ID : null,
            'total' => $order->get_total(),
            'created_at' => $order->get_date_created()->format('F j, Y g:i a'),
        ];

        $institute_fee += (float) $order->get_meta('institute_fee');
        $alliance_fee += (float) $order->get_meta('alliance_fee');
        $tax += $order->get_total_tax();
        $gross += ($order->get_subtotal() ?: 0);
        $discount += ($order->get_total_discount() ?: 0);

        if ($order->get_fees()) {
            foreach ($order->get_fees() as $fee) {
                $fee_payment += (float) $fee->get_amount();
            }
        }

        $payment_method = $order->get_payment_method_title();
        // if ($payment_method !== 'Credit Card') {
        //     $fee_system += ($order->get_total() * 0.014);
        // } else {
        //     $fee_system += (float) $order->get_meta('fee_squuad');
        // }

        $fee_system += (float) $order->get_meta('fee_squuad');

        if (!isset($payment_methods[$payment_method])) {
            $payment_methods[$payment_method] = 0;
        }
        $payment_methods[$payment_method] += $order->get_total();
    }

    // --- Obtención y Procesamiento de Cuotas ---
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $cuotes = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_payments} WHERE status_id = %d AND date_next_payment BETWEEN %s AND %s ORDER BY date_next_payment ASC",
        0,
        $start,
        $end
    ));

    $student_ids_for_cuotes = [];
    $product_ids_for_cuotes = [];
    $order_ids_for_cuotes = [];

    foreach ($cuotes as $cuote) {
        $student_ids_for_cuotes[] = $cuote->student_id;
        $product_ids_for_cuotes[] = $cuote->variation_id ?: $cuote->product_id;
        if (isset($cuote->order_id)) {
            $order_ids_for_cuotes[] = $cuote->order_id;
        }
    }

    // Pre-cargar detalles de estudiantes para cuotas
    $students_detail_for_cuotes = [];
    if (!empty($student_ids_for_cuotes)) {
        $student_ids_in = implode(',', array_filter(array_unique($student_ids_for_cuotes)));
        if ($student_ids_in) {
            $table_students = $wpdb->prefix . 'students';
            $results = $wpdb->get_results("SELECT * FROM {$table_students} WHERE id IN ({$student_ids_in})", OBJECT_K);
            foreach ($results as $s_data) {
                $students_detail_for_cuotes[$s_data->id] = $s_data;
                if ($s_data->partner_id) {
                    $customer_ids[] = $s_data->partner_id;
                }
                $student_emails[] = $s_data->email;
            }
        }
    }

    // RE-ACTUALIZAR la pre-carga de clientes y usuarios estudiantes
    // Esto es necesario si se encontraron nuevos customer_ids de las cuotas
    $unique_customer_ids = array_unique($customer_ids);
    if (!empty($unique_customer_ids)) {
        $customer_ids_in_re = implode(',', array_filter($unique_customer_ids));
        if ($customer_ids_in_re) {
            $users_results_re = $wpdb->get_results("SELECT ID, user_email FROM {$wpdb->users} WHERE ID IN ({$customer_ids_in_re})", OBJECT_K);

            // --- A J U S T E   C R U C I A L   A Q U Í (repetido para cuotas) ---
            $user_meta_data_re = [];
            if (!empty($users_results_re)) {
                $user_ids_for_meta_re = implode(',', array_keys($users_results_re));
                $meta_results_re = $wpdb->get_results(
                    "SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id IN ({$user_ids_for_meta_re}) AND meta_key IN ('first_name', 'last_name')",
                    OBJECT
                );
                foreach ($meta_results_re as $meta) {
                    $user_meta_data_re[$meta->user_id][$meta->meta_key] = $meta->meta_value;
                }
            }

            foreach ($users_results_re as $user_id => $user) {
                // Adjuntar los meta-datos al objeto de usuario
                $user->first_name = isset($user_meta_data_re[$user_id]['first_name']) ? $user_meta_data_re[$user_id]['first_name'] : '';
                $user->last_name = isset($user_meta_data_re[$user_id]['last_name']) ? $user_meta_data_re[$user_id]['last_name'] : '';
                $customers_data[$user_id] = $user; // Actualizar el array principal de clientes
            }
        }
    }

    $unique_student_emails = array_unique($student_emails);
    $student_emails_in_re = "'" . implode("','", array_filter($unique_student_emails)) . "'";
    if ($student_emails_in_re !== "''") {
        $new_user_students_results = $wpdb->get_results("SELECT ID, user_email FROM {$wpdb->users} WHERE user_email IN ({$student_emails_in_re})", OBJECT_K);
        foreach ($new_user_students_results as $user_student) {
            $user_students_data[$user_student->user_email] = $user_student;
        }
    }

    // Pre-cargar productos para cuotas
    $products_for_cuotes = [];
    if (!empty($product_ids_for_cuotes)) {
        foreach (array_unique($product_ids_for_cuotes) as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $products_for_cuotes[$product_id] = $product->get_name();
            }
        }
    }

    // Pre-cargar órdenes para cuotas (para el fix del descuento)
    $orders_for_cuotes = [];
    if (!empty($order_ids_for_cuotes)) {
        foreach (array_unique($order_ids_for_cuotes) as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $orders_for_cuotes[$order_id] = $order;
            }
        }
    }


    foreach ($cuotes as $cuote) {
        $student = isset($students_detail_for_cuotes[$cuote->student_id]) ? $students_detail_for_cuotes[$cuote->student_id] : null;
        if ($student) {
            $customer = isset($customers_data[$student->partner_id]) ? $customers_data[$student->partner_id] : null;
            $user_student = isset($user_students_data[$student->email]) ? $user_students_data[$student->email] : null;

            $cuote->student = (array) $student;
            $cuote->customer = $customer; // Esto ya debería ser el objeto con first_name/last_name
            $cuote->student_id = $user_student ? $user_student->ID : null;

            $cuote->product = isset($products_for_cuotes[$cuote->variation_id ?: $cuote->product_id]) ? $products_for_cuotes[$cuote->variation_id ?: $cuote->product_id] : '';

            $fee_inscription_id = get_fee_product_id($cuote->student_id, 'registration');
            $fee_graduation_id = get_fee_product_id($cuote->student_id, 'graduation');
            if ($cuote->product_id != $fee_inscription_id) {
                $created_at = $cuote->created_at;
                $month = date('n', strtotime($created_at));
                if ($month == 8) { // August is the 8th month
                    $order_id = $cuote->order_id;
                    $order = isset($orders_for_cuotes[$order_id]) ? $orders_for_cuotes[$order_id] : null;
                    if ($order) {
                        $discount_order = $order->get_total_discount();
                        $value = ($cuote->amount - $discount_order);
                        $cuote->amount = $value < 0 ? $cuote->amount : $value;
                    }
                }
            }

            $cuotes_array[] = $cuote;
            $receivable += $cuote->amount;
        }
    }

    // --- Obtención y Procesamiento de Gastos ---
    $table_expenses = $wpdb->prefix . 'expenses';
    $expenses = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_expenses} WHERE apply_to BETWEEN %s AND %s ORDER BY apply_to ASC",
        $start,
        $end
    ));

    foreach ($expenses as $expense) {
        $expenses_array[] = $expense;
        $expense_amount += $expense->amount;
    }

    // --- Cálculos Finales y Formato ---

    // Primero, elimina los métodos de pago con valor 0
    $payment_methods = array_filter($payment_methods, function ($total) {
        return $total > 0; // Mantén solo los totales que sean mayores a 0
    });

    // Luego, formatea los totales de los métodos de pago restantes
    $payment_methods = array_map(function ($total) {
        return wc_price($total);
    }, $payment_methods);

    $total_fees = $institute_fee + $alliance_fee + $tax + $fee_payment + $fee_system;
    $net = ($gross - $discount) - $total_fees;
    $profit_margin = ($gross - $discount) - $total_fees - $expense_amount;

    return [
        'institute_fee' => wc_price($institute_fee),
        'alliance_fee' => wc_price($alliance_fee),
        'tax' => wc_price($tax),
        'fee_payment' => wc_price($fee_payment),
        'fee_system' => wc_price($fee_system),
        'fees' => wc_price($total_fees),
        'gross' => wc_price($gross),
        'net' => wc_price($net),
        'profit_margin' => wc_price($profit_margin),
        'adjusted_gross' => wc_price($gross - $discount),
        'discount' => wc_price($discount),
        'receivable' => wc_price($receivable),
        'expense' => wc_price($expense_amount),
        'payment_methods' => (array) $payment_methods,
        'cuotes' => $cuotes_array,
        'expenses' => $expenses_array,
        'orders' => $data_fees,
    ];
}

function get_new_student_payments_table_data($start, $end)
{
    global $wpdb;

    // Define los nombres de las tablas
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    $table_programs = $wpdb->prefix . 'programs';
    $table_grades = $wpdb->prefix . 'grades';
    $table_alliances = $wpdb->prefix . 'alliances';

    // 1. Consulta los pagos y los une con la información del estudiante, programa, etc.
    // Get arrays of IDs
    $fee_registration_ids = get_fee_product_id_all('registration');
    $fee_graduation_ids = get_fee_product_id_all('graduation');

    // Merge and sanitize IDs to ensure they are integers
    $all_excluded_ids = array_merge($fee_registration_ids, $fee_graduation_ids);
    $all_excluded_ids = array_map('intval', $all_excluded_ids);

    // Specific Logic: Handle empty array scenario to prevent SQL syntax error in 'NOT IN ()'
    if (empty($all_excluded_ids)) {
        // If no IDs to exclude, we pass a dummy impossible ID (like -1) to keep syntax valid
        $all_excluded_ids = [-1];
    }

    // Dynamically create placeholders string (e.g., "%d, %d, %d")
    $placeholders = implode(', ', array_fill(0, count($all_excluded_ids), '%d'));

    // Prepare the arguments: Start Date, End Date, followed by the list of IDs
    $query_args = array_merge([$start, $end], $all_excluded_ids);

    $sql = $wpdb->prepare(
        "SELECT 
            p.*,
            TRIM(CONCAT(COALESCE(s.last_name, ''), ' ', COALESCE(s.middle_last_name, ''), ' ', COALESCE(s.name, ''), ' ', COALESCE(s.middle_name, ''))) AS student_name_full,
            pr.name AS program_name,
            s.name_institute,
            g.name AS grade_name,
            s.country,
            s.institute_id,
            s.name_institute
        FROM {$table_student_payments} AS p
        JOIN {$table_students} AS s ON p.student_id = s.id
        LEFT JOIN {$table_programs} AS pr ON s.program_id = pr.identificator
        LEFT JOIN {$table_grades} AS g ON s.grade_id = g.id
        WHERE p.date_payment BETWEEN %s AND %s
        AND p.cuote = 1
        AND p.product_id NOT IN ($placeholders)
        ORDER BY s.last_name, s.middle_last_name, s.name, s.middle_name",
        $query_args
    );

    $payments = $wpdb->get_results($sql);

    // Inicializa los arrays para los resultados
    $payments_data = []; // Datos agrupados por estudiante
    $unique_alliance_ids = []; // IDs de alianzas únicos para la consulta de nombres
    $global_calculated_amounts = [ // Totales globales
        'fee_inscription' => 0,
        'tuition_amount' => 0,
        'total_amount' => 0,
        'alliance_fees' => [],
        'institute_fee' => 0, // Total global para institute_fee
    ];

    // Contador para los pagos de matrícula por estudiante
    $tuition_payment_counts = [];

    // Si no hay pagos, retorna la estructura vacía
    if (empty($payments)) {
        return [
            'payments_data' => [],
            'alliances_headers' => [],
            'global_calculated_amounts' => $global_calculated_amounts
        ];
    }

    // Antes de procesar los pagos, obtenemos todos los IDs de alianzas únicos
    foreach ($payments as $payment) {
        if (!empty($payment->alliances)) {
            $alliances_data = json_decode($payment->alliances, true);
            if (is_array($alliances_data)) {
                foreach ($alliances_data as $alliance) {
                    if (isset($alliance['id']) && !in_array($alliance['id'], $unique_alliance_ids)) {
                        $unique_alliance_ids[] = $alliance['id'];
                    }
                }
            }
        }
    }

    // 2. Obtiene los nombres de las alianzas para los encabezados y para mapear IDs a nombres
    $alliances_headers = [];
    $alliances_name_map = [];
    if (!empty($unique_alliance_ids)) {
        $placeholders = implode(', ', array_fill(0, count($unique_alliance_ids), '%d'));
        $sql_alliances = $wpdb->prepare(
            "SELECT id, name_legal, `name` FROM {$table_alliances} WHERE id IN ($placeholders)",
            ...$unique_alliance_ids
        );
        $alliances_data_db = $wpdb->get_results($sql_alliances, ARRAY_A);
        foreach ($alliances_data_db as $alliance) {
            $alliances_headers[$alliance['id']] = $alliance['name_legal'];
            $alliances_name_map[$alliance['id']] = $alliance['name_legal'];
        }
    }

    // 3. Procesa los resultados y los agrupa en PHP
    foreach ($payments as $payment) {
        $student_id = $payment->student_id;

        // Inicializa el array del estudiante si aún no existe
        if (!isset($payments_data[$student_id])) {
            $name_institute = '';
            if (isset($payment->institute_id) && !empty($payment->institute_id)) {
                $institute = get_institute_details($payment->institute_id);
                $name_institute = $institute->name;
            } else {
                $name_institute = $payment->name_institute;
            }

            $payments_data[$student_id] = [
                'student_info' => [
                    'student_name' => trim($payment->student_name_full),
                    'program' => $payment->program_name,
                    'institute_name' => $name_institute,
                    'grade' => $payment->grade_name,
                    'country' => $payment->country,
                    'payment_date' => '', // Inicializa la fecha de pago
                    'payment_type' => '',
                    'payment_method' => '', // Nuevo: Inicializa el método de pago
                    'alliance_names' => '', // Nuevo: Inicializa los nombres de las alianzas
                ],
                'calculated_amounts' => [
                    'initial_fee_usd' => 0,
                    'tuition_amount_usd' => 0,
                    'total_amount_usd' => 0,
                    'alliance_fees' => [],
                    'institute_fee' => 0, // Total por estudiante para institute_fee
                ],
                'payments' => [],
                'has_credit_payment' => false // Nuevo flag para pagos a crédito
            ];
            // Inicializa el contador de pagos de matrícula para el nuevo estudiante
            $tuition_payment_counts[$student_id] = 0;
        }

        $payments_data[$student_id]['payments'][] = $payment;

        if (!empty($payment->alliances)) {
            $alliances_data = json_decode($payment->alliances, true);
            if (is_array($alliances_data)) {
                $current_student_alliance_names = [];
                foreach ($alliances_data as $alliance) {
                    if (isset($alliance['id']) && isset($alliance['calculated_fee_amount'])) {
                        // Agrega la comisión de la alianza al total del estudiante y al global
                        $payments_data[$student_id]['calculated_amounts']['alliance_fees'][$alliance['id']] =
                            ($payments_data[$student_id]['calculated_amounts']['alliance_fees'][$alliance['id']] ?? 0) + (float) $alliance['calculated_fee_amount'];

                        $global_calculated_amounts['alliance_fees'][$alliance['id']] =
                            ($global_calculated_amounts['alliance_fees'][$alliance['id']] ?? 0) + (float) $alliance['calculated_fee_amount'];

                        // Nuevo: Agrega el nombre de la alianza al estudiante actual
                        if (isset($alliances_name_map[$alliance['id']])) {
                            $current_student_alliance_names[] = $alliances_name_map[$alliance['id']];
                        }
                    }
                }
                // Une los nombres de alianzas únicos con una coma
                $payments_data[$student_id]['student_info']['alliance_names'] = implode(', ', array_unique($current_student_alliance_names));
            }
        }

        $fee_inscription_id = get_fee_product_id($student_id, 'registration');
        $fee_graduation_id = get_fee_product_id($student_id, 'graduation');

        if (isset($payment->product_id) && $payment->product_id == $fee_inscription_id) {
            $payments_data[$student_id]['calculated_amounts']['initial_fee_usd'] += (float) $payment->amount;
            $global_calculated_amounts['fee_inscription'] += (float) $payment->amount;
        }

        if (isset($payment->status_id) && $payment->status_id == 1) {
            if ($payment->product_id != $fee_inscription_id && $payment->product_id != $fee_graduation_id) {
                $payments_data[$student_id]['calculated_amounts']['tuition_amount_usd'] += (float) $payment->amount;
                $global_calculated_amounts['tuition_amount'] += (float) $payment->amount;
                // Incrementa el contador de pagos de matrícula
                $tuition_payment_counts[$student_id]++;
            }

            // Agrega el `institute_fee` para los pagos completados
            if (isset($payment->institute_fee)) {
                $payments_data[$student_id]['calculated_amounts']['institute_fee'] += (float) $payment->institute_fee;
                $global_calculated_amounts['institute_fee'] += (float) $payment->institute_fee;
            }

            // Asigna la fecha de pago si el pago está completado
            $payments_data[$student_id]['student_info']['payment_date'] = $payment->date_payment;

            // Obtiene el método de pago desde la orden de WooCommerce
            if (isset($payment->order_id) && function_exists('wc_get_order')) {
                $order = wc_get_order($payment->order_id);
                if ($order) {
                    $payments_data[$student_id]['student_info']['payment_method'] = $order->get_payment_method_title();
                }
            }
        }

        // Revisa si el pago actual es un pago a crédito (cuota > 1)
        if (isset($payment->cuote) && $payment->cuote > 1) {
            $payments_data[$student_id]['has_credit_payment'] = true;
        }
    }

    // 4. Calcula los totales de los estudiantes y el tipo de pago una vez que todos los pagos han sido procesados.
    foreach ($payments_data as $student_id => &$student_data) {
        $student_data['calculated_amounts']['total_amount_usd'] =
            $student_data['calculated_amounts']['initial_fee_usd'] +
            $student_data['calculated_amounts']['tuition_amount_usd'];

        // Suma el total global solo una vez por estudiante
        $global_calculated_amounts['total_amount'] += $student_data['calculated_amounts']['total_amount_usd'];

        // Determina el tipo de pago usando el nuevo flag
        if ($student_data['has_credit_payment']) {
            $student_data['student_info']['payment_type'] = __('Credit', 'edusystem');
        } else if ($student_data['calculated_amounts']['initial_fee_usd'] > 0 && $student_data['calculated_amounts']['tuition_amount_usd'] == 0) {
            $student_data['student_info']['payment_type'] = __('Initial fee only', 'edusystem');
        } else {
            $student_data['student_info']['payment_type'] = __('Complete', 'edusystem');
        }
    }


    return [
        'payments_data' => $payments_data,
        'alliances_headers' => $alliances_headers,
        'global_calculated_amounts' => $global_calculated_amounts,
    ];
}

function get_student_payments_table_data($start, $end)
{
    global $wpdb;

    // Define los nombres de las tablas
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';
    $table_programs = $wpdb->prefix . 'programs';
    $table_grades = $wpdb->prefix . 'grades';
    $table_alliances = $wpdb->prefix . 'alliances';

    // 1. Consulta los pagos y los une con la información del estudiante, programa, etc.
    $sql = $wpdb->prepare(
        "SELECT 
            p.*,
            TRIM(CONCAT(COALESCE(s.last_name, ''), ' ', COALESCE(s.middle_last_name, ''), ' ', COALESCE(s.name, ''), ' ', COALESCE(s.middle_name, ''))) AS student_name_full,
            pr.name AS program_name,
            s.name_institute,
            g.name AS grade_name,
            s.country,
            s.institute_id,
            s.name_institute
        FROM {$table_student_payments} AS p
        JOIN {$table_students} AS s ON p.student_id = s.id
        LEFT JOIN {$table_programs} AS pr ON s.program_id = pr.identificator
        LEFT JOIN {$table_grades} AS g ON s.grade_id = g.id
        WHERE p.created_at BETWEEN %s AND %s
        ORDER BY s.last_name, s.middle_last_name, s.name, s.middle_name",
        $start,
        $end
    );

    $payments = $wpdb->get_results($sql);

    // Inicializa los arrays para los resultados
    $payments_data = []; // Datos agrupados por estudiante
    $unique_alliance_ids = []; // IDs de alianzas únicos para la consulta de nombres
    $global_calculated_amounts = [ // Totales globales
        'fee_inscription' => 0,
        'tuition_amount' => 0,
        'total_amount' => 0,
        'alliance_fees' => [],
        'institute_fee' => 0, // Total global para institute_fee
    ];

    // Contador para los pagos de matrícula por estudiante
    $tuition_payment_counts = [];

    // Si no hay pagos, retorna la estructura vacía
    if (empty($payments)) {
        return [
            'payments_data' => [],
            'alliances_headers' => [],
            'global_calculated_amounts' => $global_calculated_amounts
        ];
    }

    // Antes de procesar los pagos, obtenemos todos los IDs de alianzas únicos
    foreach ($payments as $payment) {
        if (!empty($payment->alliances)) {
            $alliances_data = json_decode($payment->alliances, true);
            if (is_array($alliances_data)) {
                foreach ($alliances_data as $alliance) {
                    if (isset($alliance['id']) && !in_array($alliance['id'], $unique_alliance_ids)) {
                        $unique_alliance_ids[] = $alliance['id'];
                    }
                }
            }
        }
    }

    // 2. Obtiene los nombres de las alianzas para los encabezados y para mapear IDs a nombres
    $alliances_headers = [];
    $alliances_name_map = [];
    if (!empty($unique_alliance_ids)) {
        $placeholders = implode(', ', array_fill(0, count($unique_alliance_ids), '%d'));
        $sql_alliances = $wpdb->prepare(
            "SELECT id, name_legal, `name` FROM {$table_alliances} WHERE id IN ($placeholders)",
            ...$unique_alliance_ids
        );
        $alliances_data_db = $wpdb->get_results($sql_alliances, ARRAY_A);
        foreach ($alliances_data_db as $alliance) {
            $alliances_headers[$alliance['id']] = $alliance['name_legal'];
            $alliances_name_map[$alliance['id']] = $alliance['name_legal'];
        }
    }

    // 3. Procesa los resultados y los agrupa en PHP
    foreach ($payments as $payment) {
        $student_id = $payment->student_id;

        // Inicializa el array del estudiante si aún no existe
        if (!isset($payments_data[$student_id])) {
            $name_institute = '';
            if (isset($payment->institute_id) && !empty($payment->institute_id)) {
                $institute = get_institute_details($payment->institute_id);
                $name_institute = $institute->name;
            } else {
                $name_institute = $payment->name_institute;
            }

            $payments_data[$student_id] = [
                'student_info' => [
                    'student_name' => trim($payment->student_name_full),
                    'program' => $payment->program_name,
                    'institute_name' => $name_institute,
                    'grade' => $payment->grade_name,
                    'country' => $payment->country,
                    'payment_date' => '', // Inicializa la fecha de pago
                    'payment_type' => '',
                    'payment_method' => '', // Nuevo: Inicializa el método de pago
                    'alliance_names' => '', // Nuevo: Inicializa los nombres de las alianzas
                ],
                'calculated_amounts' => [
                    'initial_fee_usd' => 0,
                    'tuition_amount_usd' => 0,
                    'total_amount_usd' => 0,
                    'alliance_fees' => [],
                    'institute_fee' => 0, // Total por estudiante para institute_fee
                ],
                'payments' => [],
                'has_credit_payment' => false // Nuevo flag para pagos a crédito
            ];
            // Inicializa el contador de pagos de matrícula para el nuevo estudiante
            $tuition_payment_counts[$student_id] = 0;
        }

        $payments_data[$student_id]['payments'][] = $payment;

        if (!empty($payment->alliances)) {
            $alliances_data = json_decode($payment->alliances, true);
            if (is_array($alliances_data)) {
                $current_student_alliance_names = [];
                foreach ($alliances_data as $alliance) {
                    if (isset($alliance['id']) && isset($alliance['calculated_fee_amount'])) {
                        // Agrega la comisión de la alianza al total del estudiante y al global
                        $payments_data[$student_id]['calculated_amounts']['alliance_fees'][$alliance['id']] =
                            ($payments_data[$student_id]['calculated_amounts']['alliance_fees'][$alliance['id']] ?? 0) + (float) $alliance['calculated_fee_amount'];

                        $global_calculated_amounts['alliance_fees'][$alliance['id']] =
                            ($global_calculated_amounts['alliance_fees'][$alliance['id']] ?? 0) + (float) $alliance['calculated_fee_amount'];

                        // Nuevo: Agrega el nombre de la alianza al estudiante actual
                        if (isset($alliances_name_map[$alliance['id']])) {
                            $current_student_alliance_names[] = $alliances_name_map[$alliance['id']];
                        }
                    }
                }
                // Une los nombres de alianzas únicos con una coma
                $payments_data[$student_id]['student_info']['alliance_names'] = implode(', ', array_unique($current_student_alliance_names));
            }
        }

        $fee_inscription_id = get_fee_product_id($student_id, 'registration');
        $fee_graduation_id = get_fee_product_id($student_id, 'graduation');

        if (isset($payment->product_id) && $payment->product_id == $fee_inscription_id) {
            $payments_data[$student_id]['calculated_amounts']['initial_fee_usd'] += (float) $payment->amount;
            $global_calculated_amounts['fee_inscription'] += (float) $payment->amount;
        }

        if (isset($payment->status_id) && $payment->status_id == 1) {
            if ($payment->product_id != $fee_inscription_id && $payment->product_id != $fee_graduation_id) {
                $payments_data[$student_id]['calculated_amounts']['tuition_amount_usd'] += (float) $payment->amount;
                $global_calculated_amounts['tuition_amount'] += (float) $payment->amount;
                // Incrementa el contador de pagos de matrícula
                $tuition_payment_counts[$student_id]++;
            }

            // Agrega el `institute_fee` para los pagos completados
            if (isset($payment->institute_fee)) {
                $payments_data[$student_id]['calculated_amounts']['institute_fee'] += (float) $payment->institute_fee;
                $global_calculated_amounts['institute_fee'] += (float) $payment->institute_fee;
            }

            // Asigna la fecha de pago si el pago está completado
            $payments_data[$student_id]['student_info']['payment_date'] = $payment->date_payment;

            // Obtiene el método de pago desde la orden de WooCommerce
            if (isset($payment->order_id) && function_exists('wc_get_order')) {
                $order = wc_get_order($payment->order_id);
                if ($order) {
                    $payments_data[$student_id]['student_info']['payment_method'] = $order->get_payment_method_title();
                }
            }
        }

        // Revisa si el pago actual es un pago a crédito (cuota > 1)
        if (isset($payment->cuote) && $payment->cuote > 1) {
            $payments_data[$student_id]['has_credit_payment'] = true;
        }
    }

    // 4. Calcula los totales de los estudiantes y el tipo de pago una vez que todos los pagos han sido procesados.
    foreach ($payments_data as $student_id => &$student_data) {
        $student_data['calculated_amounts']['total_amount_usd'] =
            $student_data['calculated_amounts']['initial_fee_usd'] +
            $student_data['calculated_amounts']['tuition_amount_usd'];

        // Suma el total global solo una vez por estudiante
        $global_calculated_amounts['total_amount'] += $student_data['calculated_amounts']['total_amount_usd'];

        // Determina el tipo de pago usando el nuevo flag
        if ($student_data['has_credit_payment']) {
            $student_data['student_info']['payment_type'] = __('Credit', 'edusystem');
        } else if ($student_data['calculated_amounts']['initial_fee_usd'] > 0 && $student_data['calculated_amounts']['tuition_amount_usd'] == 0) {
            $student_data['student_info']['payment_type'] = __('Initial fee only', 'edusystem');
        } else {
            $student_data['student_info']['payment_type'] = __('Complete', 'edusystem');
        }
    }


    return [
        'payments_data' => $payments_data,
        'alliances_headers' => $alliances_headers,
        'global_calculated_amounts' => $global_calculated_amounts,
    ];
}

function get_institute_payments_data($start, $end)
{
    global $wpdb;

    // Define los nombres de las tablas
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_students = $wpdb->prefix . 'students';

    // Consulta los pagos filtrando por fecha y solo para pagos completados
    $sql = $wpdb->prepare(
        "SELECT
            p.institute_fee,
            s.institute_id,
            s.name_institute
        FROM {$table_student_payments} AS p
        JOIN {$table_students} AS s ON p.student_id = s.id
        WHERE p.created_at BETWEEN %s AND %s
        AND p.status_id = %d",
        $start,
        $end,
        1
    );

    $payments = $wpdb->get_results($sql);

    // Inicializa el array para los resultados de los institutos y el total global
    $institute_fees = [];
    $global_institute_fee_total = 0;

    // Procesa los resultados y los agrupa por instituto
    foreach ($payments as $payment) {
        $name_institute = '';
        if (isset($payment->institute_id) && !empty($payment->institute_id)) {
            $institute = get_institute_details($payment->institute_id);
            $name_institute = $institute->name;
        } else {
            $name_institute = $payment->name_institute;
        }

        $institute_name = $name_institute;
        $fee_amount = (float) $payment->institute_fee;

        // Suma la comisión del pago al total del instituto
        // Si el instituto aún no está en el array, lo inicializa
        $institute_fees[$institute_name] = ($institute_fees[$institute_name] ?? 0) + $fee_amount;

        // Suma al total global
        $global_institute_fee_total += $fee_amount;
    }

    // Formatea los datos y los limita a 2 decimales
    $formatted_data = [];
    foreach ($institute_fees as $name => $total_fee) {
        $formatted_data[] = [
            'institute_name' => $name,
            'institute_fee' => number_format($total_fee, 2, '.', '')
        ];
    }

    // Agrega el total global como el último elemento del array
    $formatted_data[] = [
        'institute_name' => __('Total', 'edusystem'),
        'institute_fee' => number_format($global_institute_fee_total, 2, '.', '')
    ];

    return $formatted_data;
}

function get_alliance_payments_data($start, $end)
{
    global $wpdb;

    // Define los nombres de las tablas
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_alliances = $wpdb->prefix . 'alliances';

    // 1. Consulta los pagos que tienen datos de alianzas y están completados
    $sql = $wpdb->prepare(
        "SELECT 
            p.alliances
        FROM {$table_student_payments} AS p
        WHERE p.created_at BETWEEN %s AND %s
        AND p.status_id = %d",
        $start,
        $end,
        1
    );

    $payments = $wpdb->get_results($sql);

    // Si no hay pagos, retorna la estructura vacía
    if (empty($payments)) {
        return [
            'alliance_fees' => [],
            'global_alliance_fee_total' => '0.00' // Formatea el total a 0.00
        ];
    }

    // 2. Extrae y mapea todos los IDs de alianzas únicos de los pagos
    $unique_alliance_ids = [];
    foreach ($payments as $payment) {
        if (!empty($payment->alliances)) {
            $alliances_data = json_decode($payment->alliances, true);
            if (is_array($alliances_data)) {
                foreach ($alliances_data as $alliance) {
                    if (isset($alliance['id']) && !in_array($alliance['id'], $unique_alliance_ids)) {
                        $unique_alliance_ids[] = $alliance['id'];
                    }
                }
            }
        }
    }

    // Si no hay IDs de alianzas, retorna la estructura vacía
    if (empty($unique_alliance_ids)) {
        return [
            'alliance_fees' => [],
            'global_alliance_fee_total' => '0.00' // Formatea el total a 0.00
        ];
    }

    // 3. Obtiene los nombres de las alianzas
    $placeholders = implode(', ', array_fill(0, count($unique_alliance_ids), '%d'));
    $sql_alliances = $wpdb->prepare(
        "SELECT id, `name`, name_legal FROM {$table_alliances} WHERE id IN ($placeholders)",
        ...$unique_alliance_ids
    );
    $alliances_name_map = $wpdb->get_results($sql_alliances, OBJECT_K);

    // 4. Procesa los pagos y acumula las comisiones
    $alliance_fees = [];
    $global_alliance_fee_total = 0;

    foreach ($payments as $payment) {
        $alliances_data = json_decode($payment->alliances, true);
        if (is_array($alliances_data)) {
            foreach ($alliances_data as $alliance) {
                if (isset($alliance['id']) && isset($alliance['calculated_fee_amount'])) {
                    $alliance_id = $alliance['id'];
                    $fee_amount = (float) $alliance['calculated_fee_amount'];

                    if (!isset($alliance_fees[$alliance_id])) {
                        $alliance_fees[$alliance_id] = 0;
                    }

                    $alliance_fees[$alliance_id] += $fee_amount;
                    $global_alliance_fee_total += $fee_amount;
                }
            }
        }
    }

    // 5. Formatea los datos finales con los nombres de las alianzas y limita a 2 decimales
    $formatted_data = [];
    foreach ($alliance_fees as $id => $total_fee) {
        $alliance_name = isset($alliances_name_map[$id]) ? $alliances_name_map[$id]->name_legal : 'Unknown Alliance';
        $formatted_data[] = [
            'alliance_name' => $alliance_name,
            'alliance_fee' => number_format($total_fee, 2, '.', '') // Aplica el formato aquí
        ];
    }

    $formatted_data[] = [
        'alliance_name' => __('Total', 'edusystem'),
        'alliance_fee' => number_format($global_alliance_fee_total, 2, '.', '') // Aplica el formato aquí
    ];

    return $formatted_data;
}


function get_orders_by_date($date)
{
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $date;
    $institute_fee = 0.00;
    $alliance_fee = 0.00;
    $gross = 0.00;
    $net = 0.00;
    $orders = wc_get_orders($args);
    foreach ($orders as $order) {
        $institute_fee += (float) $order->get_meta('institute_fee');
        $alliance_fee += (float) $order->get_meta('alliance_fee');
        $gross += ($order->get_subtotal() ? (float) $order->get_subtotal() : 0);
        $net += ($order->get_total() ? (float) $order->get_total() : 0);
    }

    return [
        'gross' => $gross,
        'net' => $net
    ];
}

function get_only_orders_by_date($start, $end)
{
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);

    return $orders;
}

function get_products_by_order($start, $end)
{
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $args = [
        'limit' => -1,
        'status' => 'wc-completed',
        'date_created' => $strtotime_start . '...' . $strtotime_end,
    ];

    $orders = wc_get_orders($args);

    $fee_registration_ids = get_fee_product_id_all('registration');
    $fee_graduation_ids = get_fee_product_id_all('graduation');

    if (!is_array($fee_registration_ids))
        $fee_registration_ids = [];
    if (!is_array($fee_graduation_ids))
        $fee_graduation_ids = [];

    $excluded_fee_ids = array_merge($fee_registration_ids, $fee_graduation_ids);

    $product_quantities = array();
    $product_subtotals = array();
    $product_discounts = array();
    $product_totals = array();
    $product_taxs = array();

    $product_quantities_variation = array();
    $product_subtotals_variation = array();
    $product_taxs_variation = array();
    $product_discounts_variation = array();
    $product_totals_variation = array();

    $orders_count = count($orders);

    foreach ($orders as $order) {
        $order_items = $order->get_items();

        foreach ($order_items as $item) {
            $use_product_id = $item->get_product_id();
            $use_variation_id = $item->get_variation_id();
            $quantity = $item->get_quantity();

            // In WooCommerce: Line Subtotal (pre-discount) - Line Total (post-discount) = Line Discount
            // Note: These values exclude tax.
            $subtotal = $item->get_subtotal();
            $total = $item->get_total();
            $tax = $item->get_total_tax();

            // Calculate the REAL discount for this specific item
            $item_discount_amount = $subtotal - $total;

            // Avoid negative precision errors (floating point math)
            if ($item_discount_amount < 0)
                $item_discount_amount = 0;

            $product_id = $use_product_id;

            if (!isset($product_quantities[$product_id]))
                $product_quantities[$product_id] = 0;
            if (!isset($product_subtotals[$product_id]))
                $product_subtotals[$product_id] = 0;
            if (!isset($product_discounts[$product_id]))
                $product_discounts[$product_id] = 0;
            if (!isset($product_totals[$product_id]))
                $product_totals[$product_id] = 0;
            if (!isset($product_taxs[$product_id]))
                $product_taxs[$product_id] = 0;

            if (!isset($product_quantities_variation[$product_id][$use_variation_id])) {
                $product_quantities_variation[$product_id][$use_variation_id] = 0;
                $product_subtotals_variation[$product_id][$use_variation_id] = 0;
                $product_taxs_variation[$product_id][$use_variation_id] = 0;
                $product_discounts_variation[$product_id][$use_variation_id] = 0;
                $product_totals_variation[$product_id][$use_variation_id] = 0;
            }

            $should_apply_discount = !in_array($product_id, $excluded_fee_ids);

            // If it's an excluded fee, we force discount to 0 (even if WC applied one)
            // Otherwise use the calculated item discount
            $discount_value = ($should_apply_discount) ? $item_discount_amount : 0;

            $product_quantities[$product_id] += $quantity;
            $product_quantities_variation[$product_id][$use_variation_id] += $quantity;

            $product_subtotals[$product_id] += $subtotal;
            $product_subtotals_variation[$product_id][$use_variation_id] += $subtotal;

            $product_taxs[$product_id] += $tax;
            $product_taxs_variation[$product_id][$use_variation_id] += $tax;

            $product_discounts[$product_id] += $discount_value;
            $product_discounts_variation[$product_id][$use_variation_id] += $discount_value;

            // We sum the total from the item, but if we forced discount to 0 for fees,
            // we might want to adjust the total here? 
            // Usually $total is what was paid. If you want the report to reflect "what should have been paid without discount" for fees, 
            // you would add $discount_value back. 
            // But for accurate financial reporting of what happened:
            $product_totals[$product_id] += $total;
            $product_totals_variation[$product_id][$use_variation_id] += $total;
        }
    }

    return [
        'product_quantities' => $product_quantities,
        'product_quantities_variation' => $product_quantities_variation,
        'product_subtotals' => $product_subtotals,
        'product_subtotals_variation' => $product_subtotals_variation,
        'product_discounts' => $product_discounts,
        'product_discounts_variation' => $product_discounts_variation,
        'product_taxs' => $product_taxs,
        'product_taxs_variation' => $product_taxs_variation,
        'product_totals' => $product_totals,
        'product_totals_variation' => $product_totals_variation,
        'orders_count' => $orders_count,
        'orders_total' => 0
    ];
}

function get_students_report($academic_period = null, $cut = null)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    $conditions = array();
    $params = array();

    if (!empty($cut)) {
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '$academic_period' AND cut_period = '$cut' AND code_subject IS NOT NULL AND code_subject <> ''");
        $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
        $params = array_merge($params, $cut_student_ids);
    }

    $query = "SELECT * FROM {$table_students}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $students = $wpdb->get_results($wpdb->prepare($query, $params));

    return $students;
}

function get_students_report_offset($academic_period = null, $cut = null, $search, $country = null, $institute = null)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $conditions = array();
    $params = array();

    // 1. Manejo del filtro por "corte" (cut)
    if (!empty($cut)) {
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s AND code_subject IS NOT NULL AND code_subject <> ''",
            $academic_period,
            $cut
        ));

        if (!empty($cut_student_ids)) {
            $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
            $params = array_merge($params, $cut_student_ids);
        } else {
            // Si no hay estudiantes para el corte, retornamos un array vacío de inmediato.
            return array();
        }
    }

    // 2. Manejo de la búsqueda inteligente
    if (!empty($search)) {
        $search_term = '%' . $wpdb->esc_like($search) . '%'; // Para búsqueda general
        $search_terms_array = explode(' ', $search); // Dividimos la búsqueda por espacios

        $search_sub_conditions = [];
        $search_sub_params = [];

        // Búsqueda combinada: name y last_name, etc.
        // Esto permite buscar "jose mora" donde "jose" esté en name y "mora" en last_name
        $combined_fields = [
            'CONCAT_WS(" ", name, last_name)',
            'CONCAT_WS(" ", name, middle_name, last_name)',
            'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)',
            'CONCAT_WS(" ", last_name, name)',
            'CONCAT_WS(" ", last_name, middle_last_name)'
        ];

        foreach ($combined_fields as $field_combination) {
            $search_sub_conditions[] = "{$field_combination} LIKE %s";
            $search_sub_params[] = $search_term;
        }

        // Búsqueda directa en campos individuales
        $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
        foreach ($individual_fields as $field) {
            $search_sub_conditions[] = "{$field} LIKE %s";
            $search_sub_params[] = $search_term;
        }

        // Añadimos la condición de búsqueda principal usando OR
        if (!empty($search_sub_conditions)) {
            $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            $params = array_merge($params, $search_sub_params);
        }
    }

    $conditions[] = "condition_student = %d";
    $params[] = 1;
    $conditions[] = "status_id <> %d";
    $params[] = 5;
    if ($country && !empty($country)) {
        $conditions[] = "country = %s";
        $params[] = $country;
    }

    if ($institute && !empty($institute)) {
        $conditions[] = "institute_id = %s";
        $params[] = $institute;
    }

    // 3. Construcción y ejecución de la consulta
    $query = "SELECT * FROM {$table_students}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $students = $wpdb->get_results($wpdb->prepare($query, $params));

    return $students;
}

function get_students_active_report($academic_period = null, $cut = null)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    $conditions = array();
    $params = array();

    // Agregar condición fija para status_id
    $conditions[] = "status_id IN (0, 1, 2)";

    if (!empty($cut)) {
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '$academic_period' AND cut_period = '$cut' AND code_subject IS NOT NULL AND code_subject <> ''");
        if (!empty($cut_student_ids)) {
            $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
            $params = array_merge($params, $cut_student_ids);
        } else {
            // Si no hay IDs, asegurar que la consulta no retorne resultados
            $conditions[] = "1 = 0";
        }
    }

    $query = "SELECT * FROM {$table_students}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $students = $wpdb->get_results($wpdb->prepare($query, $params));

    return $students;
}
// GET ORDERS
function get_list_orders_sales()
{
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $filter = sanitize_text_field($_POST['filter']);
        $custom = isset($_POST['custom']) ? sanitize_text_field($_POST['custom']) : '';

        $html = "";
        $chart_data = [];

        $dates = get_dates_search($filter, $custom);

        $orders_data = get_orders($dates[0], $dates[1]);

        foreach ($orders_data['orders'] as $order) {
            $html .= "<tr>";
            $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'edusystem') . "'>";
            $html .= '#' . $order['order_id'];
            $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
            $html .= "</td>";

            if ($order['customer']) {
                $customer_display_name = (isset($order['customer']->last_name) ? $order['customer']->last_name : '') . ' ' . (isset($order['customer']->first_name) ? $order['customer']->first_name : '');
                $html .= "<td class='column text-uppercase' data-colname='" . __('Customer', 'edusystem') . "'>" . esc_html($customer_display_name) . "</td>";
            } else {
                $html .= "<td class='column text-uppercase' data-colname='" . __('Customer', 'edusystem') . "'>" . __('N/A', 'edusystem') . "</td>";
            }

            if ($order['student']) {
                $student_display_name = '';
                $student_display_name = isset($order['student']['last_name']) ? $order['student']['last_name'] . ' ' : '';
                $student_display_name .= isset($order['student']['middle_last_name']) ? $order['student']['middle_last_name'] . ' ' : '';
                $student_display_name .= isset($order['student']['name']) ? $order['student']['name'] . ' ' : '';
                $student_display_name .= isset($order['student']['middle_name']) ? $order['student']['middle_name'] : '';
                $html .= "<td class='column text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . esc_html(trim($student_display_name)) . "</td>";
            } else {
                $html .= "<td class='column text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . __('N/A', 'edusystem') . "</td>";
            }

            $html .= "<td class='column' data-colname='" . __('Total', 'edusystem') . "'>" . wc_price($order['total']) . "</td>";
            $html .= "<td class='column' data-colname='" . __('Created', 'edusystem') . "'><b>" . esc_html($order['created_at']) . "</b></td>";
            $html .= "<td class='column' data-colname='" . __('Action', 'edusystem') . "'>";
            $html .= "<a class='button button-primary' href='" . esc_url(admin_url('admin.php?page=report-summary&section_tab=payment-detail&payment_id=' . $order['order_id'])) . "'>" . __('View', 'edusystem') . "</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }

        $date1 = new DateTime($dates[0]);
        $date2 = new DateTime($dates[1]);
        $interval = $date1->diff($date2);

        $labels = [];
        $net_sale_count = [];
        $gross_sale_counte = [];

        for ($i = 0; $i <= $interval->days; $i++) {
            $currentDate = clone $date1;
            $currentDate->modify("+$i days");
            $labels[] = $currentDate->format('M j, Y');

            $info_orders_daily = get_orders_by_date($currentDate->format('Y-m-d'));
            $net_sale_count[] = floatval(str_replace(['$', ','], '', $info_orders_daily['net']));
            $gross_sale_counte[] = floatval(str_replace(['$', ','], '', $info_orders_daily['gross']));
        }

        $chart_data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Gross sale',
                    'data' => $gross_sale_counte,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Net sale',
                    'data' => $net_sale_count,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    } else {
        $html = "";
        $html .= "<tr>";
        $html .= "<td colspan='6' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
        $html .= "</tr>";
    }

    echo json_encode([
        'status' => 'success',
        'chart_data' => $chart_data,
        'html' => $html,
        'data' => $orders_data
    ]);
    exit;
}

add_action('wp_ajax_nopriv_list_orders_sales', 'get_list_orders_sales');
add_action('wp_ajax_list_orders_sales', 'get_list_orders_sales');

function list_sales_product()
{
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];

        $html = "";
        $dates = get_dates_search($filter, $custom);
        $orders = get_products_by_order($dates[0], $dates[1]);
        $orders_total = 0;

        if (!empty($orders['product_quantities'])) {

            uasort($orders['product_quantities'], function ($a, $b) {
                return $b <=> $a;
            });

            foreach ($orders['product_quantities'] as $product_id => $quantity) {
                $product = wc_get_product($product_id);
                $product_name = $product ? $product->get_name() : 'Unknown Product (' . $product_id . ')';

                // Formula: Subtotal - Discount + Tax = Total
                // Your view logic was: Subtotal - (Discount - Tax) which is mathematically equivalent to Subtotal - Discount + Tax
                $calculated_totals_initial = ($orders['product_subtotals'][$product_id] - $orders['product_discounts'][$product_id] + $orders['product_taxs'][$product_id]);
                $orders_total += $calculated_totals_initial;

                $html .= "<tr style='background-color: #f6f7f7; -webkit-box-shadow: 0px -1px 0.5px 0px rgb(205 199 199 / 30%); -moz-box-shadow: 0px -1px 0.5px 0px rgb(205 199 199 / 30%); box-shadow: 0px -1px 0.5px 0px rgb(205 199 199 / 30%);'>";
                $html .= "<td class='column column-primary' data-colname='" . __('Program', 'edusystem') . "'><strong>" . $product_name . "</strong>";
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='" . __('Quantity', 'edusystem') . "'><strong>" . $quantity . "</strong></td>";
                $html .= "<td class='column' data-colname='" . __('Subtotal', 'edusystem') . "'><strong>" . wc_price($orders['product_subtotals'][$product_id]) . "</strong></td>";
                $html .= "<td class='column' data-colname='" . __('Discount', 'edusystem') . "'><strong>" . wc_price($orders['product_discounts'][$product_id]) . "</strong></td>";
                $html .= "<td class='column' data-colname='" . __('Tax', 'edusystem') . "'><strong>" . wc_price($orders['product_taxs'][$product_id]) . "</strong></td>";
                $html .= "<td class='column' data-colname='" . __('Total', 'edusystem') . "'><strong>" . wc_price($calculated_totals_initial) . "</strong></td>";
                $html .= "</tr>";

                if (isset($orders['product_quantities_variation'][$product_id])) {
                    uasort($orders['product_quantities_variation'][$product_id], function ($a, $b) {
                        return $b <=> $a;
                    });
                    foreach ($orders['product_quantities_variation'][$product_id] as $key => $variation) {
                        if ($key > 0) {
                            $product = wc_get_product($key);
                            $product_name = $product ? $product->get_name() : '';
                            $ex_product_name = explode(' - ', $product_name);
                            $display_name = isset($ex_product_name[1]) ? $ex_product_name[1] : $product_name;

                            $calculated_total = ($orders['product_subtotals_variation'][$product_id][$key] - $orders['product_discounts_variation'][$product_id][$key] + $orders['product_taxs_variation'][$product_id][$key]);

                            $html .= "<tr style='background-color: #ffffff;'>";
                            $html .= "<td class='column column-primary' data-colname='" . __('Program', 'edusystem') . "'>" . $display_name;
                            $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                            $html .= "</td>";
                            $html .= "<td class='column' data-colname='" . __('Quantity', 'edusystem') . "'>" . $orders['product_quantities_variation'][$product_id][$key] . "</td>";
                            $html .= "<td class='column' data-colname='" . __('Subtotal', 'edusystem') . "'>" . wc_price($orders['product_subtotals_variation'][$product_id][$key]) . "</td>";
                            $html .= "<td class='column' data-colname='" . __('Discount', 'edusystem') . "'>" . wc_price($orders['product_discounts_variation'][$product_id][$key]) . "</td>";
                            $html .= "<td class='column' data-colname='" . __('Tax', 'edusystem') . "'>" . wc_price($orders['product_taxs_variation'][$product_id][$key]) . "</td>";
                            $html .= "<td class='column' data-colname='" . __('Total', 'edusystem') . "'>" . wc_price($calculated_total) . "</td>";
                            $html .= "</tr>";
                        }
                    }
                }
            }
        } else {
            $html .= "<tr>";
            $html .= "<td colspan='6' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
            $html .= "</tr>";
        }

        $orders['orders_total'] = wc_price($orders_total);
        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_sales_product', 'list_sales_product');
add_action('wp_ajax_list_sales_product', 'list_sales_product');

function list_accounts_receivables()
{
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $filter = sanitize_text_field($_POST['filter']);
        $custom = isset($_POST['custom']) ? sanitize_text_field($_POST['custom']) : '';

        $html = "";
        $dates = get_dates_search($filter, $custom);
        $orders_data = get_orders($dates[0], $dates[1]);

        if (!empty($orders_data['cuotes'])) {
            foreach ($orders_data['cuotes'] as $cuote) {
                $html .= "<tr>";
                if ($cuote->student) {
                    $student_display_name = strtoupper(
                        (isset($cuote->student['last_name']) ? $cuote->student['last_name'] . ' ' : '') .
                            (isset($cuote->student['middle_last_name']) ? $cuote->student['middle_last_name'] . ' ' : '') .
                            (isset($cuote->student['name']) ? $cuote->student['name'] . ' ' : '') .
                            (isset($cuote->student['middle_name']) ? $cuote->student['middle_name'] : '')
                    );
                    $html .= "<td class='column-primary text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . esc_html(trim($student_display_name)) . "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button></td>";
                } else {
                    $html .= "<td class='column-primary text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . __('N/A', 'edusystem') . "</td>";
                }

                if ($cuote->customer) {
                    $customer_display_name = strtoupper(
                        (isset($cuote->customer->last_name) ? $cuote->customer->last_name : '') . ' ' .
                            (isset($cuote->customer->first_name) ? $cuote->customer->first_name : '')
                    );
                    $html .= "<td class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . esc_html(trim($customer_display_name)) . "</td>";
                } else {
                    $html .= "<td class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . __('N/A', 'edusystem') . "</td>";
                }

                $html .= "<td data-colname='" . __('Program', 'edusystem') . "'>" . esc_html($cuote->product) . "</td>";
                $html .= "<td data-colname='" . __('Amount', 'edusystem') . "'>" . wc_price($cuote->amount) . "</td>";
                $html .= "<td data-colname='" . __('Number cuote', 'edusystem') . "'>" . esc_html($cuote->cuote) . "</td>";
                $html .= "<td data-colname='" . __('Total cuotes', 'edusystem') . "'>" . esc_html($cuote->num_cuotes) . "</td>";
                $html .= "<td data-colname='" . __('Date', 'edusystem') . "'><b>" . esc_html($cuote->date_next_payment) . "</b></td>";
                $html .= "</tr>";
            }
        } else {
            $html .= "<tr>";
            $html .= "<td colspan='7' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
            $html .= "</tr>";
        }

        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders_data]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_accounts_receivables', 'list_accounts_receivables');
add_action('wp_ajax_list_accounts_receivables', 'list_accounts_receivables');

function list_report_students()
{

    global $current_user;
    $roles = $current_user->roles;
    $academic_period = $_POST['academic_period'] ?? '';
    $academic_period_cut = $_POST['academic_period_cut'] ?? '';
    // $grade = $_POST['period'] ?? '';

    $html = "";
    $students = get_students_report($academic_period, $academic_period_cut);
    $url = admin_url('user-edit.php?user_id=');

    if (!empty($students)) {

        foreach ($students as $student) {
            $parent = get_user_by('id', $student->partner_id);
            $user_student = get_user_by('email', $student->email);

            $html .= "<tr>";
            if (in_array('owner', $roles) || in_array('administrator', $roles)) {
                $html .= "<td class='column-primary text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . '<a href="' . $url . $user_student->ID . '" target="_blank">' . strtoupper($student->last_name . ' ' . ($student->middle_last_name ?? '') . ' ' . $student->name . ' ' . ($student->middle_name ?? '')) . "</a><button type='button' class='toggle-row'><span class='screen-reader-text'></span></button></td>";
            } else {
                $html .= "<td class='column-primary text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . strtoupper($student->last_name . ' ' . ($student->middle_last_name ?? '') . ' ' . $student->name . ' ' . ($student->middle_name ?? '')) . "</td>";
            }
            $html .= "<td data-colname='" . __('Student document', 'edusystem') . "'>" . $student->id_document . "</td>";
            $html .= "<td data-colname='" . __('Student email', 'edusystem') . "'>" . $student->email . "</td>";
            if (in_array('owner', $roles) || in_array('administrator', $roles)) {
                $html .= "<td class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . '<a href="' . $url . $parent->ID . '" target="_blank">' . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</a></td>";
            } else {
                $html .= "<td class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</a></td>";
            }
            $html .= "<td data-colname='" . __('Parent email', 'edusystem') . "'>" . $parent->user_email . "</td>";
            $html .= "<td data-colname='" . __('Country', 'edusystem') . "'>" . $student->country . "</td>";
            $html .= "<td data-colname='" . __('Grade', 'edusystem') . "'>" . get_name_grade($student->grade_id) . "</td>";
            $html .= "<td data-colname='" . __('Program', 'edusystem') . "'>" . get_name_program_student($student->id) . "</td>";
            $html .= "<td data-colname='" . __('Institute', 'edusystem') . "'>" . $student->name_institute . "</td>";
            $html .= "</tr>";
        }
    } else {
        $html .= "<tr>";
        $html .= "<td colspan='9' style='text-align:center;'>" . __('There are not records', 'edusystem') . "</td>";
        $html .= "</tr>";
    }

    echo json_encode(['status' => 'success', 'html' => $html, 'data' => $students]);
    exit;
}

add_action('wp_ajax_nopriv_list_report_students', 'list_report_students');
add_action('wp_ajax_list_report_students', 'list_report_students');

function get_load_chart_data()
{
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {
        $filter = sanitize_text_field($_POST['filter']);
        $custom = isset($_POST['custom']) ? sanitize_text_field($_POST['custom']) : '';

        $dates = get_dates_search($filter, $custom);
        $orders_data = get_orders($dates[0], $dates[1]);

        $date1 = new DateTime($dates[0]);
        $date2 = new DateTime($dates[1]);

        $interval = $date1->diff($date2);

        $labels = [];
        $net_sale_count = [];
        $gross_sale_counte = [];

        for ($i = 0; $i <= $interval->days; $i++) {
            $currentDate = clone $date1;
            $currentDate->modify("+$i days");
            $labels[] = $currentDate->format('M j, Y');

            $info_orders = get_orders_by_date($currentDate->format('Y-m-d'));
            $net_sale_count[] = floatval(str_replace(['$', ','], '', $info_orders['net']));
            $gross_sale_counte[] = floatval(str_replace(['$', ','], '', $info_orders['gross']));
        }

        $chart_data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Gross sale',
                    'data' => $gross_sale_counte,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Net sale',
                    'data' => $net_sale_count,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];

        echo json_encode(['status' => 'success', 'chart_data' => $chart_data, 'orders' => $orders_data]);
        exit;
    }
}

add_action('wp_ajax_nopriv_load_chart_data', 'get_load_chart_data');
add_action('wp_ajax_load_chart_data', 'get_load_chart_data');

class TT_Pending_Elective_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'pending_elective',
                'plural' => 'pending_electives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_students_pending_elective_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // Table names
        $table_students = $wpdb->prefix . 'students';
        $table_usermeta = $wpdb->prefix . 'usermeta';
        $table_users = $wpdb->prefix . 'users';

        $conditions = [];
        $params = [];
        $students_array = [];

        // 1. Data retrieval and sanitation
        $search = sanitize_text_field($_POST['s'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $institute = sanitize_text_field($_POST['institute'] ?? '');

        // 2. Pagination setup
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // 3. Building WHERE conditions

        // Fixed condition: elective students
        $conditions[] = "s.elective = %d";
        $params[] = 1;

        // Filter by country
        if (!empty($country)) {
            $conditions[] = "s.country = %s";
            $params[] = $country;
        }

        // Filter by institute
        if (!empty($institute)) {
            // Assuming institute_id is a numeric or string field, using %s for safety/flexibility
            $conditions[] = "s.institute_id = %s";
            $params[] = $institute;
        }

        // Smart search condition
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_sub_conditions = [];
            $search_sub_params = [];

            // Fields for direct search
            $individual_fields = ['s.name', 's.middle_name', 's.last_name', 's.middle_last_name', 's.email', 's.id_document'];
            foreach ($individual_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            /*
             * Optimization Note: Using CONCAT_WS() multiple times in an OR clause can lead to poor index utilization
             * and degraded performance on large datasets. While the original query used it heavily,
             * we prioritize direct field searches and simplify the combined name searches slightly for better efficiency,
             * focusing on the most common combinations. The full list of CONCAT_WS combinations is still inefficient.
             * For maximum optimization, a dedicated FULLTEXT index should be used for names.
             */
            $combined_fields = [
                'CONCAT(s.name, " ", s.last_name)',
                'CONCAT(s.last_name, " ", s.name)',
                // Additional combinations if strictly needed, but use sparingly
                // 'CONCAT(s.name, " ", s.middle_name, " ", s.last_name)',
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            }
        }

        // 4. Building and executing the main query (Joining to fetch parent data efficiently)

        // Collect all unique partner_id for later efficient retrieval of parent user_login/user_email
        // Note: We avoid JOINing to usermeta directly in the main query to prevent performance issues (EAV model)
        $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

        $count_query = "SELECT COUNT(s.id) FROM {$table_students} AS s {$where_clause}";
        $total_count = $wpdb->get_var($wpdb->prepare($count_query, $params));

        $query = "SELECT s.*, u.user_email AS parent_email, u.display_name AS parent_display_name FROM {$table_students} AS s";
        $query .= " LEFT JOIN {$table_users} AS u ON s.partner_id = u.ID"; // Join to retrieve parent email and display name directly
        $query .= $where_clause;
        $query .= " ORDER BY s.id DESC LIMIT %d OFFSET %d";

        $final_params = array_merge($params, [$per_page, $offset]); // MODIFICADO: usar $per_page

        // Execute the student query
        $students = $wpdb->get_results($wpdb->prepare($query, $final_params), "ARRAY_A");

        // 5. Pre-fetching parent names (optimization for N+1 problem)
        $parent_ids = [];
        if ($students) {
            $parent_ids = array_unique(array_filter(array_column($students, 'partner_id')));
        }

        $parent_meta = [];
        if (!empty($parent_ids)) {
            $ids_in = implode(',', array_map('absint', $parent_ids));
            // Retrieve first_name and last_name for all parents in one query
            $meta_query = $wpdb->prepare(
                "SELECT user_id, meta_key, meta_value FROM {$table_usermeta} WHERE user_id IN ({$ids_in}) AND meta_key IN (%s, %s)",
                'first_name',
                'last_name'
            );
            $results = $wpdb->get_results($meta_query, ARRAY_A);

            // Map results to an array structure: [user_id => ['first_name' => '...', 'last_name' => '...']]
            foreach ($results as $row) {
                $user_id = (int) $row['user_id'];
                $meta_key = $row['meta_key'];
                $parent_meta[$user_id][$meta_key] = $row['meta_value'];
            }
        }

        // 6. Processing the results
        if ($students) {
            // Pre-fetch all grades and institutes if helper functions exist, to avoid repeated calls inside the loop
            $grades = [];
            if (function_exists('get_name_grade')) {
                // Optimization: Get all necessary grade names in bulk if feasible, or rely on the cached version of the helper function
            }

            $institutes = [];
            if (function_exists('get_name_institute')) {
                // Optimization: Get all necessary institute names in bulk if feasible, or rely on the cached version of the helper function
            }

            foreach ($students as $student) {
                $parent_id = (int) $student['partner_id'];
                $parent_first_name = $parent_meta[$parent_id]['first_name'] ?? '';
                $parent_last_name = $parent_meta[$parent_id]['last_name'] ?? '';

                if (!empty($parent_first_name) || !empty($parent_last_name)) {
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper($parent_last_name . ' ' . $parent_first_name) . "</span>";
                } else {
                    $parent_full_name = '';
                }

                // Parent email is fetched directly from the main query (u.user_email)
                $parent_email = $student['parent_email'] ?? '';

                // Resolve grade name
                $grade_name = function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'];

                // Resolve institute name
                $institute_name = ($student['institute_id'] && function_exists('get_name_institute')) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '');
                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'parent' => $parent_full_name,
                    'parent_email' => $parent_email,
                    'country' => $student['country'],
                    'grade' => $grade_name,
                    'institute' => $institute_name
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_pending_elective_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Current_Student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'pending_elective',
                'plural' => 'pending_electives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'subjects' => __('Subjects', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_students_current_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $table_school_subjects = $wpdb->prefix . 'school_subjects';

        // Obtener filtros de $_POST
        $search = $_POST['s'] ?? '';
        $country = $_POST['country'] ?? '';
        $institute = $_POST['institute'] ?? '';

        // Cargar el período académico y el corte actual
        $load = load_current_cut(); // Se asume que esta función es eficiente
        $academic_period = $load['code'];
        $cut = $load['cut'];

        $conditions = array();
        $params = array();

        // 1. Condición de filtro por inscripción y corte
        // Se utilizará un JOIN en la consulta principal para filtrar estudiantes
        $inscription_join = "INNER JOIN {$table_student_period_inscriptions} spi ON s.id = spi.student_id";
        $conditions[] = "spi.code_period = %s";
        $params[] = $academic_period;
        $conditions[] = "spi.cut_period = %s";
        $params[] = $cut;
        $conditions[] = "spi.status_id = 1";
        // Aseguramos que el estudiante tenga al menos una inscripción con un subject válido para el corte
        $conditions[] = "(spi.code_subject IS NOT NULL AND spi.code_subject <> '')";

        // 2. Condición de búsqueda inteligente
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';

            // Búsqueda combinada de nombres y apellidos (usando CONCAT_WS directamente en SQL)
            $search_fields = [
                'CONCAT_WS(" ", s.name, s.middle_name, s.last_name, s.middle_last_name)',
                'CONCAT_WS(" ", s.last_name, s.name)',
                's.name',
                's.middle_name',
                's.last_name',
                's.middle_last_name',
                's.email',
                's.id_document'
            ];

            $search_sub_conditions = [];
            $search_sub_params = [];
            foreach ($search_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            $params = array_merge($params, $search_sub_params);
        }

        // 3. Condiciones de filtro por país e instituto
        if ($country && !empty($country)) {
            $conditions[] = "s.country = %s";
            $params[] = $country;
        }

        if ($institute && !empty($institute)) {
            $conditions[] = "s.institute_id = %s";
            $params[] = $institute;
        }

        // PAGINATION
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // 4. Construcción y ejecución de la consulta principal de estudiantes
        // Usamos DISTINCT para evitar duplicados si un estudiante está inscrito en varias asignaturas
        $query_students = "
        SELECT SQL_CALC_FOUND_ROWS DISTINCT s.*
        FROM {$table_students} s
        {$inscription_join}
    ";

        if (!empty($conditions)) {
            $query_students .= " WHERE " . implode(" AND ", $conditions);
        }

        // Agrupamos por ID para asegurar que DISTINCT funcione correctamente en el set paginado
        $query_students .= " GROUP BY s.id ORDER BY s.id DESC LIMIT %d OFFSET %d";
        $params[] = $per_page; // MODIFICADO: usar $per_page
        $params[] = $offset;

        // Ejecutar la consulta de estudiantes
        // Nota: Usamos una copia de los parámetros para el prepare y luego el original para la consulta.
        $students = $wpdb->get_results($wpdb->prepare($query_students, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if (empty($students)) {
            return ['data' => [], 'total_count' => 0];
        }

        // 5. Optimización N+1: Obtener todas las asignaturas de todos los estudiantes paginados en una sola consulta
        $student_ids = wp_list_pluck($students, 'id');
        $student_id_placeholders = implode(',', array_fill(0, count($student_ids), '%d'));

        // Parámetros para la consulta de asignaturas (periodo, corte, status, student_ids)
        $subject_params = array_merge(
            [$academic_period, $cut],
            $student_ids
        );

        // Consulta que trae ID del estudiante, ID de la asignatura y Code de la asignatura.
        $subjects_query = $wpdb->prepare("
        SELECT 
            spi.student_id, 
            ss.name
        FROM {$table_student_period_inscriptions} spi
        INNER JOIN {$table_school_subjects} ss ON 
            (ss.id = spi.subject_id AND spi.subject_id IS NOT NULL) OR 
            (ss.code_subject = spi.code_subject AND spi.code_subject IS NOT NULL AND spi.code_subject <> '')
        WHERE 
            spi.code_period = %s AND 
            spi.cut_period = %s AND 
            spi.status_id = 1 AND 
            spi.student_id IN ({$student_id_placeholders})
        GROUP BY spi.student_id, ss.name
        ORDER BY spi.student_id, ss.name
    ", $subject_params);

        $subjects_data = $wpdb->get_results($subjects_query, "ARRAY_A");

        // 6. Mapear las asignaturas a los estudiantes en PHP
        $student_subjects_map = [];
        foreach ($subjects_data as $row) {
            $student_id = $row['student_id'];
            $subject_name = $row['name'];
            if (!isset($student_subjects_map[$student_id])) {
                $student_subjects_map[$student_id] = [];
            }
            $student_subjects_map[$student_id][] = $subject_name;
        }

        // 7. Formateo de resultados
        $students_array = [];
        $url = admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=');

        foreach ($students as $student) {
            $student_id = $student['id'];

            // Obtener la lista de asignaturas del mapa
            $subjects_list = $student_subjects_map[$student_id] ?? [];
            $subjects_text = implode(', ', $subjects_list);

            // Generación del nombre completo (se mejoró la consistencia en el orden)
            $student_full_name = student_names_lastnames_helper($student['id']);

            array_push($students_array, [
                'id' => $student_id,
                'student' => '<span class="text-uppercase">' . $student_full_name . '</span>',
                'subjects' => '<span class="text-upper">' . $subjects_text . '</span>'
            ]);
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_current_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Active_Student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        // Must match the 'option' key defined in tt_add_active_students_per_page_option
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        // get_user_option retrieves the value saved by the set-screen-option filter
        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {
        return ucwords($item['student']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function fetch_students_active_data($limit, $offset)
    {
        $students_array = [];

        $academic_period = $_POST['academic_period'] ?? '';
        $academic_period_cut = $_POST['academic_period_cut'] ?? '';
        $search = $_POST['s'] ?? '';
        $country = $_POST['country'] ?? '';
        $institute = $_POST['institute'] ?? '';

        // This function should be modified for efficiency to fetch only the required data
        $students = get_students_report_offset($academic_period, $academic_period_cut, $search, $country, $institute);
        $total_count = count($students);

        // Apply pagination using array_slice
        $students_filtered = array_slice($students, $offset, $limit);

        foreach ($students_filtered as $student) {
            $parent = get_user_by('id', $student->partner_id);
            $student_full_name = "<span class='text-uppercase' data-colname='" . __('Student', 'edusystem') . "'>" . student_names_lastnames_helper($student->id) . '</span>';
            $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
            $students_array[] = [
                'student' => $student_full_name,
                'id' => $student->id,
                'id_document' => $student->id_document,
                'email' => $student->email,
                'parent' => $parent_full_name,
                'parent_email' => $parent->user_email,
                'country' => $student->country,
                'grade' => get_name_grade($student->grade_id),
                'institute' => $student->institute_id ? get_name_institute($student->institute_id) : $student->name_institute
            ];
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $per_page = $this->get_per_page();
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        // Final calculated per_page must now be 50 if the user set it.

        $data_student = $this->fetch_students_active_data($per_page, $offset);

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
            'total_pages' => ceil($total_count / $per_page),
        ));

        $this->items = $data;
    }
}

class TT_Documents_Active_Student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {
        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    /**
     * Retrieves the table columns definition, including dynamic document columns.
     *
     * @return array The array of column definitions.
     */
    function get_columns()
    {
        global $wpdb;
        $table_documents = $wpdb->prefix . 'documents';
        $documents = $wpdb->get_results("SELECT * FROM {$table_documents} WHERE grade_id = 4", OBJECT);

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem')
        );
        foreach ($documents as $document) {
            // Apply strtolower and then ucfirst to the document name for display.
            $display_name = ucfirst(strtolower($document->name));

            // Convert to lowercase.
            $name_lower = strtolower($document->name);

            // Remove all non-alphanumeric characters (except spaces) for a clean key.
            // This removes special characters like periods, parentheses, commas, etc.
            $name_sanitized = preg_replace('/[^a-z0-9\s]/', '', $name_lower);

            // Replace spaces with underscores to create the final array key.
            $key = str_replace(' ', '_', $name_sanitized);

            // Use the modified name for the column header.
            $columns[$key] = __($display_name, 'edusystem');
        }
        $columns['view_details'] = __('Actions', 'edusystem');

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_students_active_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. PREPARACIÓN DE PARÁMETROS Y TABLAS ---
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $table_documents = $wpdb->prefix . 'documents';

        // PAGINATION
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // FILTERS
        $academic_period = $_POST['academic_period'] ?? '';
        $academic_period_cut = $_POST['academic_period_cut'] ?? '';
        $search = $_POST['s'] ?? '';
        $country = $_POST['country'] ?? '';
        $institute = $_POST['institute'] ?? '';

        // --- 2. OPTIMIZACIÓN DE CONSULTAS A BD ---

        // Obtener los documentos de grado 4 solo una vez
        $documents = $wpdb->get_results("SELECT id, name FROM {$table_documents} WHERE grade_id = 4", OBJECT_K);

        // Preparar el array de nombres de documentos para la consulta SQL
        $document_names = array_column($documents, 'name');
        $documents_keys_map = [];

        foreach ($documents as $document) {
            $name_lower = strtolower($document->name);
            // Deprecation: Usar ?? '' para garantizar que $document->middle_last_name sea string
            $name_sanitized = preg_replace('/[^a-z0-9\s]/', '', $name_lower);
            $documents_keys_map[$document->name] = str_replace(' ', '_', $name_sanitized);
        }

        // Obtención de estudiantes (Se mantiene la ineficiencia forzada, pero se procesa mejor)
        $students = get_students_report_offset($academic_period, $academic_period_cut, $search, $country, $institute);
        $total_count = count($students);
        $students_filtered = array_slice($students, $offset, $per_page); // MODIFICADO: usar $per_page

        // Optimizando la obtención de datos de padres y documentos para el subset filtrado
        $student_ids = array_column($students_filtered, 'id');
        $parent_ids = array_column($students_filtered, 'partner_id');

        // Cargar los datos de los padres (WP_User objects) de una sola vez
        $parents = get_users(['include' => $parent_ids, 'fields' => ['ID', 'user_email']]);
        $parents_map = array_column($parents, null, 'ID');

        // Preparación de Placeholders para parent_ids (Números enteros)
        $parent_id_placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));

        // Cargar los metadatos de los padres (last_name, first_name) de una sola vez
        // Se utiliza vsprintf en lugar de $wpdb->prepare para la lista IN de IDs
        $parent_meta_query = $wpdb->prepare(
            "SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id IN ({$parent_id_placeholders}) AND meta_key IN ('first_name', 'last_name')",
            ...$parent_ids
        );
        $parent_meta_results = $wpdb->get_results($parent_meta_query, ARRAY_A);
        $parent_meta_map = [];

        foreach ($parent_meta_results as $meta) {
            $parent_meta_map[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
        }

        // Cargar los documentos subidos de los estudiantes filtrados de una sola vez

        // 1. Preparar Placeholders para student_id (Números enteros)
        $student_id_placeholders = implode(',', array_fill(0, count($student_ids), '%d'));

        // 2. Preparar Placeholders para document_id (Strings)
        $document_name_placeholders = implode(',', array_fill(0, count($document_names), '%s'));

        // NOTA CLAVE: Al usar $wpdb->prepare, se pasan los arrays de IDs y Nombres como argumentos separados.
        $student_documents_query = $wpdb->prepare(
            "SELECT student_id, document_id FROM {$table_student_documents} WHERE student_id IN ({$student_id_placeholders}) AND document_id IN ({$document_name_placeholders}) AND `status` = 5",
            ...$student_ids,
            ...$document_names
        );

        $student_documents_uploaded = $wpdb->get_results($student_documents_query, OBJECT);

        $uploaded_map = [];
        foreach ($student_documents_uploaded as $doc) {
            $uploaded_map[$doc->student_id][$doc->document_id] = true;
        }

        // --- 3. PROCESAMIENTO DE DATOS ---
        $students_array = [];

        // Preparar strings comunes una sola vez
        $yes_label = __('Yes', 'edusystem');
        $no_label = __('No', 'edusystem');
        $parent_label = __('Parent', 'edusystem');

        foreach ($students_filtered as $student) {
            $parent = $parents_map[$student->partner_id] ?? null;
            $parent_meta = $parent_meta_map[$student->partner_id] ?? ['first_name' => '', 'last_name' => ''];
            $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student->id) . '</span>';
            $parent_full_name_raw = $parent_meta['last_name'] . ' ' . $parent_meta['first_name'];
            $parent_full_name = "<span class='text-uppercase' data-colname='" . $parent_label . "'>" . strtoupper($parent_full_name_raw) . "</span>";

            $student_data = [
                'student' => $student_full_name,
                'id' => $student->id,
                'id_document' => $student->id_document,
                'email' => $student->email,
                'parent' => $parent_full_name,
                'parent_email' => $parent->user_email ?? '',
                'country' => $student->country,
                'grade' => get_name_grade($student->grade_id),
                'institute' => $student->institute_id ? get_name_institute($student->institute_id) : $student->name_institute
            ];

            // Mapeo eficiente de documentos subidos
            $student_uploaded_docs = $uploaded_map[$student->id] ?? [];
            foreach ($documents_keys_map as $document_name => $key) {
                $student_data[$key] = isset($student_uploaded_docs[$document_name]) ? $yes_label : $no_label;
            }

            $students_array[] = $student_data;
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_active_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Enrollments_Active_Student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {
        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    /**
     * Retrieves the table columns definition, including dynamic document columns.
     *
     * @return array The array of column definitions.
     */
    function get_columns()
    {
        global $wpdb;
        $table_school_subjects = $wpdb->prefix . 'school_subjects';
        $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1 AND `type` <> 'equivalence' ORDER BY `type` DESC, `id` ASC", OBJECT);

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem')
        );
        foreach ($subjects as $subject) {
            $display_name = $subject->name;
            $name_lower = strtolower($subject->code_subject);
            $name_sanitized = preg_replace('/[^a-z0-9\s]/', '', $name_lower);
            $key = str_replace(' ', '_', $name_sanitized);
            $columns[$key] = __($display_name, 'edusystem');
        }
        $columns['view_details'] = __('Actions', 'edusystem');

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_students_active_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. PREPARACIÓN DE PARÁMETROS Y TABLAS ---
        $table_school_subjects = $wpdb->prefix . 'school_subjects';
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

        // PAGINATION
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // FILTERS
        $academic_period = $_POST['academic_period'] ?? '';
        $academic_period_cut = $_POST['academic_period_cut'] ?? '';
        $search = $_POST['s'] ?? '';
        $country = $_POST['country'] ?? '';
        $institute = $_POST['institute'] ?? '';

        // --- 2. OPTIMIZACIÓN DE CONSULTAS A BD ---

        // Obtener las asignaturas activas. Cambiamos OBJECT_K por OBJECT para que array_column funcione correctamente.
        $subjects = $wpdb->get_results("SELECT id, code_subject FROM {$table_school_subjects} WHERE is_active = 1 and `type` <> 'equivalence' ORDER BY `type` DESC, `id` ASC", OBJECT);

        // Lista de IDs (Números enteros) y Códigos (Strings)
        $subjects_ids = array_column($subjects, 'id');
        // **USANDO TU DEFINICIÓN EXPLÍCITA**
        $subject_codes_list = array_column($subjects, 'code_subject');

        // Obtención de estudiantes
        $students = get_students_report_offset($academic_period, $academic_period_cut, $search, $country, $institute);
        $total_count = count($students);
        $students_filtered = array_slice($students, $offset, $per_page); // MODIFICADO: usar $per_page

        // Optimizando la obtención de datos para el subset filtrado
        $student_ids = array_column($students_filtered, 'id');
        $parent_ids = array_column($students_filtered, 'partner_id');

        // Cargar los datos de los padres (WP_User objects) de una sola vez
        $parents = get_users(['include' => $parent_ids, 'fields' => ['ID', 'user_email']]);
        $parents_map = array_column($parents, null, 'ID');

        // Preparación de Placeholders para parent_ids (Números enteros)
        $parent_id_placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));

        // Cargar los metadatos de los padres (last_name, first_name) de una sola vez
        $parent_meta_query = $wpdb->prepare(
            "SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id IN ({$parent_id_placeholders}) AND meta_key IN ('first_name', 'last_name')",
            ...$parent_ids
        );
        $parent_meta_results = $wpdb->get_results($parent_meta_query, ARRAY_A);
        $parent_meta_map = [];

        foreach ($parent_meta_results as $meta) {
            $parent_meta_map[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
        }

        // Cargar las inscripciones de los estudiantes filtrados de una sola vez

        // 1. Preparar Placeholders para student_id (Números enteros)
        $student_id_placeholders = implode(',', array_fill(0, count($student_ids), '%d'));

        // 2. Preparar Placeholders para subject_id (Números enteros)
        $subject_id_placeholders = implode(',', array_fill(0, count($subjects_ids), '%d'));

        // 3. Preparar Placeholders para code_subject (Strings)
        $subject_code_placeholders = implode(',', array_fill(0, count($subject_codes_list), '%s'));

        $student_enrollments_query = $wpdb->prepare(
            "SELECT student_id, code_subject, cut_period, code_period 
         FROM {$table_student_period_inscriptions} 
         WHERE student_id IN ({$student_id_placeholders}) 
         AND (subject_id IN ({$subject_id_placeholders}) OR code_subject IN ({$subject_code_placeholders})) 
         AND status_id != 1",
            ...$student_ids,
            ...$subjects_ids,
            ...$subject_codes_list
        );

        // Obtener los campos necesarios: student_id, code_subject, cut_period y code_period
        $student_enrollments = $wpdb->get_results($student_enrollments_query, OBJECT);

        $enrollment_map = [];
        foreach ($student_enrollments as $enrollment) {
            // Mapear los datos por student_id y code_subject, almacenando los valores requeridos
            $enrollment_map[$enrollment->student_id][$enrollment->code_subject] = [
                'cut_period' => $enrollment->cut_period,
                'code_period' => $enrollment->code_period
            ];
        }

        // --- 3. PROCESAMIENTO DE DATOS ---
        $students_array = [];

        // Preparar strings comunes una sola vez
        $no_label = '';
        $parent_label = __('Parent', 'edusystem');

        foreach ($students_filtered as $student) {
            $parent = $parents_map[$student->partner_id] ?? null;
            $parent_meta = $parent_meta_map[$student->partner_id] ?? ['first_name' => '', 'last_name' => ''];
            $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student->id) . '</span>';
            $parent_full_name_raw = $parent_meta['last_name'] . ' ' . $parent_meta['first_name'];
            $parent_full_name = "<span class='text-uppercase' data-colname='" . $parent_label . "'>" . strtoupper($parent_full_name_raw) . "</span>";

            $student_data = [
                'student' => $student_full_name,
                'id' => $student->id,
                'id_document' => $student->id_document,
                'email' => $student->email,
                'parent' => $parent_full_name,
                'parent_email' => $parent->user_email ?? '',
                'country' => $student->country,
                'grade' => get_name_grade($student->grade_id),
                'institute' => $student->institute_id ? get_name_institute($student->institute_id) : $student->name_institute
            ];

            // Obtener las inscripciones de este estudiante
            $student_enrollment_map = $enrollment_map[$student->id] ?? [];

            // Itera sobre la lista de todos los códigos de asignatura
            foreach ($subject_codes_list as $subject_code) {

                // Sanear la clave para el array de reporte (como lo tenías)
                $name_lower = strtolower($subject_code);
                $name_sanitized = preg_replace('/[^a-z0-9\s]/', '', $name_lower);
                $key = str_replace(' ', '_', $name_sanitized);

                $enrollment_data = $student_enrollment_map[$subject_code] ?? null;

                if ($enrollment_data) {
                    // Si la inscripción existe, concatena los valores
                    $student_data[$key] = $enrollment_data['cut_period'] . ' ' . $enrollment_data['code_period'];
                } else {
                    // Si no existe, usa la etiqueta "No"
                    $student_data[$key] = $no_label;
                }
            }

            $students_array[] = $student_data;
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_active_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Summary_Comissions_Institute_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'summary_comission_institute',
                'plural' => 'summary_comission_institutes',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {
        // switch ($column_name) {
        //     case 'view_details':
        //         $buttons = '';
        //         $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
        //         return $buttons;
        //     default:
        //         return $item[$column_name];
        // }

        return $item[$column_name];
    }

    function column_name($item)
    {
        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'institute_name' => __('Institute', 'edusystem'),
            'institute_fee' => __('Amount USD', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_summary_comissions()
    {
        $filter = $_POST['typeFilter'] ?? 'this-month';
        $custom = $_POST['custom'] ?? false;

        $dates = get_dates_search($filter, $custom);
        if (!is_array($dates) || count($dates) < 2 || empty($dates[0]) || empty($dates[1])) {
            return [];
        }

        return get_institute_payments_data($dates[0], $dates[1]);
    }

    function prepare_items()
    {

        $data = $this->get_summary_comissions();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $this->items = $data;
    }
}

class TT_Summary_Comissions_Alliance_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'summary_comission_alliance',
                'plural' => 'summary_comission_alliances',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {
        // switch ($column_name) {
        //     case 'view_details':
        //         $buttons = '';
        //         $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
        //         return $buttons;
        //     default:
        //         return $item[$column_name];
        // }

        return $item[$column_name];
    }

    function column_name($item)
    {
        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'alliance_name' => __('Alliance', 'edusystem'),
            'alliance_fee' => __('Amount USD', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_summary_comissions()
    {
        $filter = $_POST['typeFilter'] ?? 'this-month';
        $custom = $_POST['custom'] ?? false;
        $dates = get_dates_search($filter, $custom);
        if (!is_array($dates) || count($dates) < 2 || empty($dates[0]) || empty($dates[1])) {
            return [];
        }

        return get_alliance_payments_data($dates[0], $dates[1]);
    }

    function prepare_items()
    {

        $data = $this->get_summary_comissions();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $this->items = $data;
    }
}

class TT_Pending_Graduation_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'student' => __('Student', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'academic_ready' => __('Academic ready', 'edusystem'),
            'documents_ready' => __('Documents', 'edusystem'),
            'fee_payment_ready' => __('Fee registration', 'edusystem'),
            'product_ready' => __('Program payment', 'edusystem'),
            'fee_graduation_ready' => __('Fee graduation', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    /**
     * Retrieves a paginated report of students potentially pending graduation, 
     * filtered by various criteria and academic readiness.
     *
     * @return array Contains the paginated student data and the total count.
     */
    function get_students_pending_graduation_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. Data Initialization and Input Sanitization ---
        $table_students = $wpdb->prefix . 'students';

        // Get input parameters, using COALESCE for null checks and empty strings
        $search = sanitize_text_field($_POST['s'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $institute = sanitize_text_field($_POST['institute'] ?? '');
        $academic_period_student = sanitize_text_field($_POST['academic_period'] ?? '');
        $academic_period_cut_student = sanitize_text_field($_POST['academic_period_cut'] ?? '');

        $conditions = [];
        $params = [];

        // --- 2. Constructing WHERE Clause Conditions ---

        // Primary condition: Student status is not 5 (assuming 5 is 'not pending graduation')
        $conditions[] = "status_id != %d";
        $params[] = 5;

        // Filter by Academic Period
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // Filter by Academic Period Cut
        if (!empty($academic_period_cut_student)) {
            // NOTE: The original code used 'initial_cut' which might be correct, 
            // assuming it stores the cut being filtered.
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // Filter by Country
        if (!empty($country)) {
            $conditions[] = "country = %s";
            $params[] = $country;
        }

        // Filter by Institute
        if (!empty($institute)) {
            $conditions[] = "institute_id = %s";
            $params[] = $institute;
        }

        // --- 3. Smart Search Condition ---
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_sub_conditions = [];
            $search_sub_params = [];

            // Fields to search directly (individual and combined names/IDs)
            // Using CONCAT_WS is better for combined search performance than multiple ORs on individual fields
            $search_fields = [
                'name',
                'middle_name',
                'last_name',
                'middle_last_name',
                'email',
                'id_document',
                "CONCAT_WS(' ', name, last_name)",
                "CONCAT_WS(' ', last_name, name)"
            ];

            foreach ($search_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Add the combined search condition to the main conditions array
            $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            $params = array_merge($params, $search_sub_params);
        }

        // --- 4. Main Query Construction and Execution ---

        $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

        // IMPORTANT OPTIMIZATION NOTE: The bottleneck is the PHP filtering with get_academic_ready().
        // If possible, the logic inside get_academic_ready() should be refactored 
        // into a JOIN or a subquery to filter results directly in SQL.
        // Since this is not possible here, we proceed with the current structure, 
        // but the performance issue for large datasets remains.

        $query = "SELECT * FROM {$table_students}{$where_clause} ORDER BY id DESC";

        // Retrieve all students matching SQL conditions
        $all_students_from_db = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");

        // --- 5. PHP Filtering (Bottleneck) ---
        $filtered_students = [];
        if (function_exists('get_academic_ready')) {
            foreach ($all_students_from_db as $student) {
                // NOTE: get_academic_ready() is executed for every student retrieved, 
                // which can be very slow if the function involves complex lookups.
                if (get_academic_ready($student['id'])) {
                    $filtered_students[] = $student;
                }
            }
        } else {
            // Fallback: If get_academic_ready doesn't exist, we can't filter the intended way. 
            // For safety, we assume no students are "academically ready" for graduation 
            // unless defined by the function.
            $filtered_students = [];
        }

        // --- 6. Pagination ---
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $total_academic_ready_students = count($filtered_students);
        $paginated_students = array_slice($filtered_students, $offset, $per_page); // MODIFICADO: usar $per_page

        // --- 7. Final Data Processing ---
        $students_array = [];
        foreach ($paginated_students as $student) {
            // Reduced repeated calls to get_academic_ready(), now it's only called once above.

            $parent = get_user_by('id', $student['partner_id']);
            $parent_full_name = '';
            $parent_email = '';
            if ($parent) {
                $parent_last_name = get_user_meta($parent->ID, 'last_name', true);
                $parent_first_name = get_user_meta($parent->ID, 'first_name', true);
                $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper($parent_last_name . ' ' . $parent_first_name) . "</span>";
                $parent_email = $parent->user_email;
            }

            // Format Student Name
            $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

            // Get status indicators (Calls to external functions are necessary here)
            $fee_payment_ready = get_fee_paid($student['id'], 'registration');
            $product_ready = get_payments($student['id']);
            $fee_graduation_ready = get_fee_paid($student['id'], 'graduation');
            $documents_ready = get_documents_ready($student['id']);

            $students_array[] = [
                'student' => $student_full_name,
                'fee_payment_ready' => $fee_payment_ready ? 'Yes' : 'No',
                'product_ready' => $product_ready ? 'Yes' : 'No',
                'fee_graduation_ready' => $fee_graduation_ready ? 'Yes' : 'No',
                'documents_ready' => $documents_ready ? 'Yes' : 'No',
                'academic_ready' => 'Yes', // Already filtered, so it must be 'Yes' for included students
                'id' => $student['id'],
                'id_document' => $student['id_document'],
                'email' => $student['email'],
                'parent' => $parent_full_name,
                'parent_email' => $parent_email,
                'country' => $student['country'],
                'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                // Use COALESCE pattern for better readability/maintainability
                'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? $student['institute_id'] ?? '')
            ];
        }

        return ['data' => $students_array, 'total_count' => $total_academic_ready_students];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_pending_graduation_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Pending_Documents_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'student' => __('Student', 'edusystem'),
            'pending_document_ids' => __('Documents', 'edusystem'),
            // 'country' => __('Country', 'edusystem'),
            // 'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_students_pending_documents_report($per_page = 20)
    {
        global $wpdb;

        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $table_users = $wpdb->users;
        $table_usermeta = $wpdb->usermeta;

        $search = sanitize_text_field($_POST['s'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $institute = sanitize_text_field($_POST['institute'] ?? '');
        $academic_period_student = sanitize_text_field($_POST['academic_period'] ?? '');
        $academic_period_cut_student = sanitize_text_field($_POST['academic_period_cut'] ?? '');

        $pagenum = max(1, absint($_GET['paged'] ?? 1));
        $offset = (($pagenum - 1) * $per_page);

        $conditions = [];
        $params = [];

        $conditions[] = "s.status_id NOT IN (%d, %d)";
        $params[] = 5;
        $params[] = 6;

        if (!empty($country)) {
            $conditions[] = "s.country = %s";
            $params[] = $country;
        }

        if (!empty($institute)) {
            $conditions[] = "s.institute_id = %s";
            $params[] = $institute;
        }

        if (!empty($academic_period_student)) {
            $conditions[] = "s.academic_period = %s";
            $params[] = $academic_period_student;
        }

        if (!empty($academic_period_cut_student)) {
            $conditions[] = "s.initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        if (!empty($search)) {
            $search_terms = explode(' ', $search);
            $search_sub_conditions = [];
            $search_fields = ['s.name', 's.middle_name', 's.last_name', 's.middle_last_name', 's.email', 's.id_document'];

            foreach ($search_terms as $term) {
                if (strlen($term) > 1) {
                    $term_like = '%' . $wpdb->esc_like($term) . '%';
                    $term_conditions = [];
                    foreach ($search_fields as $field) {
                        $term_conditions[] = "{$field} LIKE %s";
                        $params[] = $term_like;
                    }
                    $search_sub_conditions[] = "(" . implode(" OR ", $term_conditions) . ")";
                }
            }

            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" AND ", $search_sub_conditions) . ")";
            }
        }

        $pending_logic = "(
            (d.attachment_id = 0 AND (d.is_required = 1 OR d.max_date_upload IS NOT NULL))
            OR 
            (d.attachment_id != 0 AND d.status IN (3, 6) AND (d.is_required = 1 OR d.max_date_upload IS NOT NULL))
        )";

        $conditions[] = "EXISTS (SELECT 1 FROM {$table_student_documents} AS d WHERE d.student_id = s.id AND {$pending_logic})";

        $select_cols = [
            's.*',
            'u.user_email AS parent_email',
            'um_first.meta_value AS parent_first_name',
            'um_last.meta_value AS parent_last_name',
            "(SELECT GROUP_CONCAT(document_id SEPARATOR ', ') FROM {$table_student_documents} AS d WHERE d.student_id = s.id AND {$pending_logic}) AS pending_document_ids"
        ];

        $where_sql = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $query = "
            SELECT SQL_CALC_FOUND_ROWS " . implode(', ', $select_cols) . "
            FROM {$table_students} AS s
            LEFT JOIN {$table_users} AS u ON s.partner_id = u.ID
            LEFT JOIN {$table_usermeta} AS um_first ON u.ID = um_first.user_id AND um_first.meta_key = 'first_name'
            LEFT JOIN {$table_usermeta} AS um_last ON u.ID = um_last.user_id AND um_last.meta_key = 'last_name'
            $where_sql
            ORDER BY s.id DESC 
            LIMIT %d OFFSET %d
        ";

        $params[] = $per_page;
        $params[] = $offset;

        $students = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        $students_array = [];

        if ($students) {
            foreach ($students as $student) {
                $parent_full_name = '';
                if ($student['parent_first_name'] || $student['parent_last_name']) {
                    $parent_name = strtoupper(trim($student['parent_last_name'] . ' ' . $student['parent_first_name']));
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . $parent_name . "</span>";
                }

                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';
                $grade_name = function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'];
                $institute_name = (function_exists('get_name_institute') && $student['institute_id'])
                    ? get_name_institute($student['institute_id'])
                    : ($student['name_institute'] ?? '');

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'income' => $student['academic_period'],
                    'term' => $student['initial_cut'],
                    'email' => $student['email'],
                    'parent' => $parent_full_name,
                    'parent_email' => $student['parent_email'] ?? '',
                    'country' => $student['country'],
                    'grade' => $grade_name,
                    'institute' => $institute_name,
                    'pending_document_ids' => $student['pending_document_ids'] ?? ''
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_pending_documents_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Graduated_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'income' => __('Income', 'edusystem'),
            'term' => __('Term', 'edusystem'),
            'id_document' => __('ID', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_student_graduated($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // Define table names early
        $table_students = $wpdb->prefix . 'students';
        $table_users = $wpdb->users;
        $table_usermeta = $wpdb->usermeta;

        // --- 1. Sanitization and Variable Assignment ---

        // Sanitize input variables using appropriate functions
        $search = sanitize_text_field($_POST['s'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $institute = sanitize_text_field($_POST['institute'] ?? '');
        $academic_period_student = sanitize_text_field($_POST['academic_period'] ?? '');
        $academic_period_cut_student = sanitize_text_field($_POST['academic_period_cut'] ?? '');

        // PAGINATION
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = max(1, absint($_GET['paged'] ?? 1));
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $conditions = [];
        $params = [];

        // --- 2. Filtering Conditions ---

        // Status: Graduated (status_id = 5) - Mandatory condition
        $conditions[] = "s.status_id = %d";
        $params[] = 5;

        // Filter by Country
        if (!empty($country)) {
            $conditions[] = "s.country = %s";
            $params[] = $country;
        }

        // Filter by Institute
        if (!empty($institute)) {
            $conditions[] = "s.institute_id = %s";
            $params[] = $institute;
        }

        // Filter by Academic Period
        if (!empty($academic_period_student)) {
            $conditions[] = "s.academic_period = %s";
            $params[] = $academic_period_student;
        }

        // Filter by Period Cut
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "s.initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // --- 3. Optimized Smart Search Condition (Less reliance on CONCAT/LIKE '%...%') ---

        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_terms = explode(' ', $search); // Break search string into words for better matching

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Prioritize matching full terms if possible (still using LIKE)
            $search_fields = ['s.name', 's.middle_name', 's.last_name', 's.middle_last_name', 's.email', 's.id_document'];

            // Try to match each word in the search query against fields
            foreach ($search_terms as $term) {
                if (strlen($term) > 1) { // Ignore very short terms
                    $term_like = '%' . $wpdb->esc_like($term) . '%';
                    $term_conditions = [];
                    foreach ($search_fields as $field) {
                        $term_conditions[] = "{$field} LIKE %s";
                        $search_sub_params[] = $term_like;
                    }
                    // Group OR conditions for each search term
                    $search_sub_conditions[] = "(" . implode(" OR ", $term_conditions) . ")";
                }
            }

            // Combine all search term conditions with AND
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" AND ", $search_sub_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            } else {
                // Fallback for full string search if no individual terms were long enough
                $term_conditions = [];
                foreach ($search_fields as $field) {
                    $term_conditions[] = "{$field} LIKE %s";
                    $search_sub_params[] = $search_term_like;
                }
                $conditions[] = "(" . implode(" OR ", $term_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            }
        }

        // --- 4. Main Query Construction and Execution (Including JOIN for Parent Data) ---

        // Get all required student columns, and parent data via JOIN
        // This replaces the slow get_user_by/get_user_meta calls inside the loop.
        $select_cols = [
            's.*',
            'u.user_email AS parent_email',
            // Join the usermeta to get first_name and last_name of the parent
            'um_first.meta_value AS parent_first_name',
            'um_last.meta_value AS parent_last_name',
        ];

        $query = "
            SELECT SQL_CALC_FOUND_ROWS " . implode(', ', $select_cols) . " 
            FROM {$table_students} AS s
            LEFT JOIN {$table_users} AS u ON s.partner_id = u.ID
            LEFT JOIN {$table_usermeta} AS um_first ON u.ID = um_first.user_id AND um_first.meta_key = 'first_name'
            LEFT JOIN {$table_usermeta} AS um_last ON u.ID = um_last.user_id AND um_last.meta_key = 'last_name'
        ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY s.id DESC LIMIT %d OFFSET %d";
        $params[] = $per_page; // MODIFICADO: usar $per_page
        $params[] = $offset;

        // Execute the query
        $students = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        $students_array = [];

        // --- 5. Result Processing (Highly Optimized) ---

        if ($students) {
            foreach ($students as $student) {

                // Format Parent Name (already available from the JOIN)
                $parent_full_name = '';
                if ($student['parent_first_name'] || $student['parent_last_name']) {
                    $parent_name = strtoupper(trim($student['parent_last_name'] . ' ' . $student['parent_first_name']));
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . $parent_name . "</span>";
                }

                // Format Student Name (Optimized)
                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                // External function calls only once per row
                $grade_name = function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'];
                $institute_name = (function_exists('get_name_institute') && $student['institute_id'])
                    ? get_name_institute($student['institute_id'])
                    : ($student['name_institute'] ?? '');


                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'income' => $student['academic_period'],
                    'term' => $student['initial_cut'],
                    'email' => $student['email'],
                    'parent' => $parent_full_name,
                    'parent_email' => $student['parent_email'] ?? '',
                    'country' => $student['country'],
                    'grade' => $grade_name,
                    'institute' => $institute_name
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_student_graduated($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Retired_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'income' => __('Income', 'edusystem'),
            'term' => __('Term', 'edusystem'),
            'id_document' => __('ID', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            // 'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_student_retired($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. PREPARACIÓN Y RECOLECCIÓN DE DATOS DE ENTRADA ---
        $table_students = $wpdb->prefix . 'students';
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // Obtener y sanear entradas
        $search = $_POST['s'] ?? '';
        $academic_period_student = $_POST['academic_period'] ?? '';
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';

        $conditions = [];
        $params = [];

        // --- 2. CONSTRUCCIÓN DE CONDICIONES WHERE ---

        // Condición de estado (status_id = 6 para 'retired' / 'retirado')
        $conditions[] = "status_id = %d";
        $params[] = 6;

        // Filtro por período académico
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // Filtro por corte de período
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // Condición de búsqueda inteligente
        if (!empty($search)) {
            // Usar un array para las sub-condiciones de búsqueda
            $search_sub_conditions = [];
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_fields = [
                'id_document',
                'email',
                'name',
                'middle_name',
                'last_name',
                'middle_last_name',
            ];

            // Añadir condiciones LIKE para campos individuales
            foreach ($search_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Simplificación y optimización de CONCAT_WS (Se puede limitar a las combinaciones más comunes)
            // Nota: Estas combinaciones son muy costosas y no utilizan índices. Es una necesidad de diseño actual.
            $combined_fields = [
                'CONCAT_WS(" ", name, last_name)',
                'CONCAT_WS(" ", last_name, name)',
                'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Agregamos la condición de búsqueda principal al array de condiciones generales
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            }
        }

        // --- 3. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA PRINCIPAL ---
        $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

        // Consulta principal con LIMIT y OFFSET
        $query = "
        SELECT SQL_CALC_FOUND_ROWS *
        FROM {$table_students}
        {$where_clause}
        ORDER BY id DESC
        LIMIT %d OFFSET %d
    ";

        // Añadir placeholders para LIMIT y OFFSET al final de los parámetros
        $params[] = $per_page; // MODIFICADO: usar $per_page
        $params[] = $offset;

        // Ejecutar la consulta de estudiantes
        $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");

        // Obtener el total de filas
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        $students_array = [];

        // --- 4. PROCESAMIENTO DE LOS RESULTADOS (Optimización de consultas en bucle) ---
        if ($students) {
            // Obtener una lista de todos los 'partner_id' (IDs de los padres)
            $parent_ids = array_filter(array_column($students, 'partner_id'));
            $parent_data = [];

            // Pre-cargar todos los datos de usuario y meta de los padres en una sola operación
            if (!empty($parent_ids)) {
                $parent_ids_placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));
                $table_users = $wpdb->users;
                $table_usermeta = $wpdb->usermeta;

                // 1. Obtener emails de los padres
                $user_query = "SELECT ID, user_email FROM {$table_users} WHERE ID IN ({$parent_ids_placeholders})";
                $users = $wpdb->get_results($wpdb->prepare($user_query, $parent_ids), ARRAY_A);

                foreach ($users as $user) {
                    $parent_data[$user['ID']] = ['email' => $user['user_email'], 'last_name' => '', 'first_name' => ''];
                }

                // 2. Obtener meta data (last_name y first_name)
                // Esto se podría hacer en una sola consulta para mejorar la eficiencia.
                $meta_query = "
                SELECT user_id, meta_key, meta_value 
                FROM {$table_usermeta} 
                WHERE user_id IN ({$parent_ids_placeholders}) 
                AND meta_key IN ('last_name', 'first_name')
            ";
                $metas = $wpdb->get_results($wpdb->prepare($meta_query, $parent_ids), ARRAY_A);

                foreach ($metas as $meta) {
                    if (isset($parent_data[$meta['user_id']])) {
                        $parent_data[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
                    }
                }
            }

            // El bucle ahora solo procesa los datos ya cargados
            foreach ($students as $student) {
                $partner_id = $student['partner_id'];
                $parent_full_name = '';
                $parent_email = '';

                if (isset($parent_data[$partner_id])) {
                    $parent_data_item = $parent_data[$partner_id];
                    $parent_name = strtoupper($parent_data_item['last_name'] . ' ' . $parent_data_item['first_name']);
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>{$parent_name}</span>";
                    $parent_email = $parent_data_item['email'];
                }

                // Format Student Name (Optimized)
                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'income' => $student['academic_period'],
                    'term' => $student['initial_cut'],
                    'parent' => $parent_full_name,
                    'parent_email' => $parent_email,
                    'country' => $student['country'],
                    // Se asume que get_name_grade y get_name_institute son funciones externas eficientes o almacenan datos en caché.
                    'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                    'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '')
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_student_retired($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Pending_Matrix_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'income' => __('Income', 'edusystem'),
            'term' => __('Term', 'edusystem'),
            'id_document' => __('ID', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            // 'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_student_retired($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. PREPARACIÓN Y RECOLECCIÓN DE DATOS DE ENTRADA ---
        $table_students = $wpdb->prefix . 'students';

        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

        // Obtener y sanear entradas
        $search = $_POST['s'] ?? '';
        $academic_period_student = $_POST['academic_period'] ?? '';
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';

        $conditions = [];
        $params = [];

        // --- 2. CONSTRUCCIÓN DE CONDICIONES WHERE ---

        // Condición de estado: terms_available es null
        $conditions[] = "terms_available is null";

        // Filtro por período académico
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // Filtro por corte de período
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // Condición de búsqueda inteligente
        if (!empty($search)) {
            // Usar un array para las sub-condiciones de búsqueda
            $search_sub_conditions = [];
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_fields = [
                'id_document',
                'email',
                'name',
                'middle_name',
                'last_name',
                'middle_last_name',
            ];

            // Añadir condiciones LIKE para campos individuales
            foreach ($search_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Simplificación y optimización de CONCAT_WS (Se puede limitar a las combinaciones más comunes)
            // Nota: Estas combinaciones son muy costosas y no utilizan índices. Es una necesidad de diseño actual.
            $combined_fields = [
                'CONCAT_WS(" ", name, last_name)',
                'CONCAT_WS(" ", last_name, name)',
                'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Agregamos la condición de búsqueda principal al array de condiciones generales
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            }
        }

        // --- 3. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA PRINCIPAL ---
        $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

        // Consulta para obtener TODOS los estudiantes que cumplen las condiciones base
        // (sin paginación SQL, porque necesitamos filtrar en PHP)
        $query = "
            SELECT *
            FROM {$table_students}
            {$where_clause}
            ORDER BY id DESC
        ";

        // Ejecutar la consulta de estudiantes
        if (!empty($params)) {
            $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        } else {
            $students = $wpdb->get_results($query, "ARRAY_A");
        }

        // --- 4. FILTRAR EN PHP: solo estudiantes que NO están listos académicamente ---
        $filtered_students = [];
        if ($students) {
            foreach ($students as $student) {
                // Solo incluir estudiantes que NO estén listos académicamente
                if (!get_academic_ready($student['id'])) {
                    $filtered_students[] = $student;
                }
            }
        }

        // --- 5. PAGINACIÓN MANUAL EN PHP ---
        $total_count = count($filtered_students);
        $offset = (($pagenum - 1) * $per_page);
        $paginated_students = array_slice($filtered_students, $offset, $per_page);

        $students_array = [];

        // --- 6. PROCESAMIENTO DE LOS RESULTADOS (Optimización de consultas en bucle) ---
        if ($paginated_students) {
            // Obtener una lista de todos los 'partner_id' (IDs de los padres)
            $parent_ids = array_filter(array_column($paginated_students, 'partner_id'));
            $parent_data = [];

            // Pre-cargar todos los datos de usuario y meta de los padres en una sola operación
            if (!empty($parent_ids)) {
                $parent_ids_placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));
                $table_users = $wpdb->users;
                $table_usermeta = $wpdb->usermeta;

                // 1. Obtener emails de los padres
                $user_query = "SELECT ID, user_email FROM {$table_users} WHERE ID IN ({$parent_ids_placeholders})";
                $users = $wpdb->get_results($wpdb->prepare($user_query, $parent_ids), ARRAY_A);

                foreach ($users as $user) {
                    $parent_data[$user['ID']] = ['email' => $user['user_email'], 'last_name' => '', 'first_name' => ''];
                }

                // 2. Obtener meta data (last_name y first_name)
                // Esto se podría hacer en una sola consulta para mejorar la eficiencia.
                $meta_query = "
                SELECT user_id, meta_key, meta_value 
                FROM {$table_usermeta} 
                WHERE user_id IN ({$parent_ids_placeholders}) 
                AND meta_key IN ('last_name', 'first_name')
            ";
                $metas = $wpdb->get_results($wpdb->prepare($meta_query, $parent_ids), ARRAY_A);

                foreach ($metas as $meta) {
                    if (isset($parent_data[$meta['user_id']])) {
                        $parent_data[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
                    }
                }
            }

            // El bucle ahora solo procesa los datos ya cargados
            foreach ($paginated_students as $student) {
                $partner_id = $student['partner_id'];
                $parent_full_name = '';
                $parent_email = '';

                if (isset($parent_data[$partner_id])) {
                    $parent_data_item = $parent_data[$partner_id];
                    $parent_name = strtoupper($parent_data_item['last_name'] . ' ' . $parent_data_item['first_name']);
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>{$parent_name}</span>";
                    $parent_email = $parent_data_item['email'];
                }

                // Format Student Name (Optimized)
                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'income' => $student['academic_period'],
                    'term' => $student['initial_cut'],
                    'parent' => $parent_full_name,
                    'parent_email' => $parent_email,
                    'country' => $student['country'],
                    // Se asume que get_name_grade y get_name_institute son funciones externas eficientes o almacenan datos en caché.
                    'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                    'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '')
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_student_retired($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Report_Electives_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            // 'income' => __('Income', 'edusystem'),
            // 'term' => __('Term', 'edusystem'),
            // 'id_document' => __('ID', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'electives' => __('Electives', 'edusystem'),
            // 'email' => __('Student email', 'edusystem'),
            // 'parent' => __('Parent', 'edusystem'),
            // 'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            // 'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_student_retired($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // --- 1. PREPARACIÓN Y RECOLECCIÓN DE DATOS DE ENTRADA ---
        $table_students = $wpdb->prefix . 'students';

        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

        // Obtener y sanear entradas
        $search = $_POST['s'] ?? '';
        $academic_period_student = $_POST['academic_period'] ?? '';
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';

        $conditions = [];
        $params = [];

        // --- 2. CONSTRUCCIÓN DE CONDICIONES WHERE ---

        // Condición de estado: terms_available es null
        // $conditions[] = "terms_available is null";

        // Filtro por período académico
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // Filtro por corte de período
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // Condición de búsqueda inteligente
        if (!empty($search)) {
            // Usar un array para las sub-condiciones de búsqueda
            $search_sub_conditions = [];
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_fields = [
                'id_document',
                'email',
                'name',
                'middle_name',
                'last_name',
                'middle_last_name',
            ];

            // Añadir condiciones LIKE para campos individuales
            foreach ($search_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Simplificación y optimización de CONCAT_WS (Se puede limitar a las combinaciones más comunes)
            // Nota: Estas combinaciones son muy costosas y no utilizan índices. Es una necesidad de diseño actual.
            $combined_fields = [
                'CONCAT_WS(" ", name, last_name)',
                'CONCAT_WS(" ", last_name, name)',
                'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $params[] = $search_term_like; // Agregar el parámetro
            }

            // Agregamos la condición de búsqueda principal al array de condiciones generales
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            }
        }

        // --- 3. CONSTRUCCIÓN Y EJECUCIÓN DE LA CONSULTA PRINCIPAL ---
        $where_clause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

        // Consulta para obtener TODOS los estudiantes que cumplen las condiciones base
        // (sin paginación SQL, porque necesitamos filtrar en PHP)
        $query = "
            SELECT *
            FROM {$table_students}
            {$where_clause}
            ORDER BY id DESC
        ";

        // Ejecutar la consulta de estudiantes
        if (!empty($params)) {
            $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        } else {
            $students = $wpdb->get_results($query, "ARRAY_A");
        }

        // --- 4. FILTRAR EN PHP: solo estudiantes que NO están listos académicamente ---
        $filtered_students = [];
        // if ($students) {
        //     foreach ($students as $student) {
        //         // Solo incluir estudiantes que NO estén listos académicamente
        //         if (!get_academic_ready($student['id'])) {
        //             $filtered_students[] = $student;
        //         }
        //     }
        // }

        // --- 5. PAGINACIÓN MANUAL EN PHP ---
        $total_count = count($students);
        $offset = (($pagenum - 1) * $per_page);
        $paginated_students = array_slice($students, $offset, $per_page);

        $students_array = [];

        // --- 6. PROCESAMIENTO DE LOS RESULTADOS (Optimización de consultas en bucle) ---
        if ($paginated_students) {
            // Obtener una lista de todos los 'partner_id' (IDs de los padres)
            $parent_ids = array_filter(array_column($paginated_students, 'partner_id'));
            $parent_data = [];

            // Pre-cargar todos los datos de usuario y meta de los padres en una sola operación
            if (!empty($parent_ids)) {
                $parent_ids_placeholders = implode(',', array_fill(0, count($parent_ids), '%d'));
                $table_users = $wpdb->users;
                $table_usermeta = $wpdb->usermeta;

                // 1. Obtener emails de los padres
                $user_query = "SELECT ID, user_email FROM {$table_users} WHERE ID IN ({$parent_ids_placeholders})";
                $users = $wpdb->get_results($wpdb->prepare($user_query, $parent_ids), ARRAY_A);

                foreach ($users as $user) {
                    $parent_data[$user['ID']] = ['email' => $user['user_email'], 'last_name' => '', 'first_name' => ''];
                }

                // 2. Obtener meta data (last_name y first_name)
                // Esto se podría hacer en una sola consulta para mejorar la eficiencia.
                $meta_query = "
                SELECT user_id, meta_key, meta_value 
                FROM {$table_usermeta} 
                WHERE user_id IN ({$parent_ids_placeholders}) 
                AND meta_key IN ('last_name', 'first_name')
            ";
                $metas = $wpdb->get_results($wpdb->prepare($meta_query, $parent_ids), ARRAY_A);

                foreach ($metas as $meta) {
                    if (isset($parent_data[$meta['user_id']])) {
                        $parent_data[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
                    }
                }
            }

            // El bucle ahora solo procesa los datos ya cargados
            foreach ($paginated_students as $student) {
                $partner_id = $student['partner_id'];
                $parent_full_name = '';
                $parent_email = '';

                if (isset($parent_data[$partner_id])) {
                    $parent_data_item = $parent_data[$partner_id];
                    $parent_name = strtoupper($parent_data_item['last_name'] . ' ' . $parent_data_item['first_name']);
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>{$parent_name}</span>";
                    $parent_email = $parent_data_item['email'];
                }

                // Format Student Name (Optimized)
                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';
                $electives = load_inscriptions_electives_valid_arr(get_student_detail($student['id']), 'status_id = 3');
                $electives_text = 'N/A';
                foreach ($electives as $key => $elective) {
                    $subject = get_subject_details($elective->subject_id);
                    $electives[$key] = $subject ? $subject->name : 'N/A';
                }

                if (!empty($electives)) {
                    $electives_text = implode(', ', $electives);
                }

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'electives' => $electives_text,
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'income' => $student['academic_period'],
                    'term' => $student['initial_cut'],
                    'parent' => $parent_full_name,
                    'parent_email' => $parent_email,
                    'country' => $student['country'],
                    // Se asume que get_name_grade y get_name_institute son funciones externas eficientes o almacenan datos en caché.
                    'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                    'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '')
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_student_retired($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Scholarships_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'active',
                'plural' => 'actives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'scholarship' => __('Scholarship', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }


    function get_student_scholarships($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;

        // Table names
        $table_students = $wpdb->prefix . 'students';
        $table_scholarship_assigned_student = $wpdb->prefix . 'scholarship_assigned_student';
        $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
        $table_users = $wpdb->prefix . 'users'; // Assuming WordPress users table for parents
        $table_usermeta = $wpdb->prefix . 'usermeta'; // Assuming WordPress usermeta table for parent names

        // Initialize conditions and parameters for the main query
        $conditions = array();
        $params = array();

        // Sanitize and retrieve POST data
        $search = sanitize_text_field($_POST['s'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $institute = sanitize_text_field($_POST['institute'] ?? '');
        $academic_period_student = sanitize_text_field($_POST['academic_period'] ?? '');
        $academic_period_cut_student = sanitize_text_field($_POST['academic_period_cut'] ?? '');
        $scholarship_id = sanitize_text_field($_POST['scholarship_id'] ?? '');

        // 1. Student status condition
        $conditions[] = "s.status_id != %d";
        $params[] = 5; // Assuming 5 is the status_id for not pending graduation

        if ($country && !empty($country)) {
            $conditions[] = "country = %s";
            $params[] = $country;
        }

        if ($institute && !empty($institute)) {
            $conditions[] = "institute_id = %s";
            $params[] = $institute;
        }

        // 2. Academic period filter (if present)
        if (!empty($academic_period_student)) {
            $conditions[] = "s.academic_period = %s";
            $params[] = $academic_period_student;
        }

        // 3. Academic period cut filter (if present)
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "s.initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }
        
        if (!empty($scholarship_id)) {
            $conditions[] = "sas.scholarship_id = %d";
            $params[] = (int) $scholarship_id;
        }

        // 4. Smart search condition
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';
            $search_sub_conditions = [];

            // Combined fields for search
            $combined_fields = [
                'CONCAT_WS(" ", s.name, s.last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name, s.last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name, s.last_name, s.middle_last_name)',
                'CONCAT_WS(" ", s.last_name, s.name)',
                'CONCAT_WS(" ", s.last_name, s.middle_last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name)',
                'CONCAT_WS(" ", s.last_name, s.middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $params[] = $search_term_like;
            }

            // Individual fields for search
            $individual_fields = ['s.name', 's.middle_name', 's.last_name', 's.middle_last_name', 's.email', 's.id_document'];
            foreach ($individual_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $params[] = $search_term_like;
            }

            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
            }
        }

        // Pagination setup
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        // Build the main query with JOINs
        // SQL_CALC_FOUND_ROWS is used to get the total count before LIMIT
        $query = "
        SELECT SQL_CALC_FOUND_ROWS
            s.*,
            sas.scholarship_id,
            sa.name AS scholarship_name,
            u.user_email AS parent_email,
            pm_first_name.meta_value AS parent_first_name,
            pm_last_name.meta_value AS parent_last_name
        FROM {$table_students} AS s
        INNER JOIN {$table_scholarship_assigned_student} AS sas ON s.id = sas.student_id
        INNER JOIN {$table_scholarships_availables} AS sa ON sas.scholarship_id = sa.id
        LEFT JOIN {$table_users} AS u ON s.partner_id = u.ID
        LEFT JOIN {$table_usermeta} AS pm_first_name ON u.ID = pm_first_name.user_id AND pm_first_name.meta_key = 'first_name'
        LEFT JOIN {$table_usermeta} AS pm_last_name ON u.ID = pm_last_name.user_id AND pm_last_name.meta_key = 'last_name'
    ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY s.id DESC LIMIT %d OFFSET %d";
        $params[] = $per_page; // MODIFICADO: usar $per_page
        $params[] = $offset;

        $students_from_db = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");

        // Get the total count of students that match the criteria (before pagination)
        $total_academic_ready_students = $wpdb->get_var("SELECT FOUND_ROWS()");

        $students_array = [];
        if (!empty($students_from_db)) {
            foreach ($students_from_db as $student) {
                $parent_full_name = '';
                if ($student['parent_first_name'] || $student['parent_last_name']) {
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper($student['parent_last_name'] . ' ' . $student['parent_first_name']) . "</span>";
                }

                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                $students_array[] = [
                    'student' => $student_full_name,
                    'scholarship' => $student['scholarship_name'],
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'parent' => $parent_full_name,
                    'parent_email' => $student['parent_email'],
                    'country' => $student['country'],
                    'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                    'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '')
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_academic_ready_students];
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_student_scholarships($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}
class TT_Non_Enrolled_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'pending_elective',
                'plural' => 'pending_electives',
                'ajax' => true
            )
        );
    }

    // --- NUEVO: MÉTODOS PARA PAGINACIÓN ---
    protected function get_per_page_option_name()
    {
        return 'tt_students_per_page';
    }

    protected function get_per_page()
    {
        $storage_key = 'tt_students_per_page';
        $default_value = 20;

        $per_page = (int) get_user_option($storage_key);

        if (empty($per_page) || $per_page < 1) {
            $per_page = $default_value;
        }

        return $per_page;
    }
    // --- FIN NUEVO ---

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'student' => __('Student', 'edusystem'),
            'id_document' => __('Student document', 'edusystem'),
            'email' => __('Student email', 'edusystem'),
            'parent' => __('Parent', 'edusystem'),
            'parent_email' => __('Parent email', 'edusystem'),
            'country' => __('Country', 'edusystem'),
            'grade' => __('Grade', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_students_non_enrolled_report($per_page = 20) // MODIFICADO: agregar parámetro con valor por defecto
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $students_array = [];
        $conditions = array();
        $params = array();

        // Obtener el término de búsqueda de $_POST
        $search = $_POST['s'] ?? '';
        $country = $_POST['country'] ?? '';
        $institute = $_POST['institute'] ?? '';

        // Cargar el período académico y el corte actual
        $load = load_current_cut();
        $academic_period = $load['code'];
        $cut = $load['cut'];

        // 1. Condición: Estudiantes NO inscritos en el período/corte actual
        // Asegúrate de escapar las variables para prevenir inyección SQL
        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s AND status_id = 1 AND code_subject IS NOT NULL AND code_subject <> ''",
            $academic_period,
            $cut
        ));

        if (!empty($cut_student_ids)) {
            // Si hay IDs de estudiantes inscritos, filtramos los que NO estén en esa lista
            $conditions[] = "id NOT IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
            $params = array_merge($params, $cut_student_ids);
        }
        // Si $cut_student_ids está vacío, significa que nadie está inscrito en ese corte,
        // por lo tanto, todos los estudiantes activos (condition_student = 1, elective = 0) serían "no inscritos".
        // No necesitamos un `else { return []; }` aquí a menos que quieras detener la ejecución.

        // 2. Otras condiciones fijas
        $conditions[] = "condition_student = %d";
        $params[] = 1; // Assuming 1 means active or regular student
        $conditions[] = "elective = %d";
        $params[] = 0; // Assuming 0 means not an elective student
        $conditions[] = "status_id <> %d";
        $params[] = 5; // Assuming 0 means not an elective student

        if ($country && !empty($country)) {
            $conditions[] = "country = %s";
            $params[] = $country;
        }

        if ($institute && !empty($institute)) {
            $conditions[] = "institute_id = %s";
            $params[] = $institute;
        }

        // 3. Condición de filtro por período académico (si está presente)
        $academic_period_student = $_POST['academic_period'] ?? '';
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // 4. Condición de filtro por corte de período (si está presente)
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // 5. Condición de búsqueda inteligente
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Búsqueda combinada de nombres y apellidos (CONCAT_WS)
            $combined_fields = [
                'CONCAT_WS(" ", name, last_name)',
                'CONCAT_WS(" ", name, middle_name, last_name)',
                'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)',
                'CONCAT_WS(" ", last_name, name)',
                'CONCAT_WS(" ", last_name, middle_last_name)',
                'CONCAT_WS(" ", name, middle_name)',
                'CONCAT_WS(" ", last_name, middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Búsqueda directa en campos individuales
            $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
            foreach ($individual_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Agregamos la condición de búsqueda principal al array de condiciones generales
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            }
        }

        // PAGINATION
        // MODIFICADO: usar parámetro $per_page en lugar de valor fijo
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // 6. Construcción y ejecución de la consulta principal
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC LIMIT %d OFFSET %d"; // Añadimos placeholders para LIMIT y OFFSET
        $params[] = $per_page; // MODIFICADO: usar $per_page
        $params[] = $offset;

        // Ejecutar la consulta de estudiantes
        $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // 7. Procesamiento de los resultados
        if ($students) {
            foreach ($students as $student) {
                $parent = get_user_by('id', $student['partner_id']);
                $parent_full_name = '';
                $parent_email = '';
                if ($parent) {
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
                    $parent_email = $parent->user_email;
                }

                $student_full_name = '<span class="text-uppercase">' . student_names_lastnames_helper($student['id']) . '</span>';

                $students_array[] = [
                    'student' => $student_full_name,
                    'id' => $student['id'],
                    'id_document' => $student['id_document'],
                    'email' => $student['email'],
                    'parent' => $parent_full_name,
                    'parent_email' => $parent_email,
                    'country' => $student['country'],
                    'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'],
                    'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '')
                ];
            }
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }
    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items()
    {
        // MODIFICADO: usar get_per_page() en lugar de valor fijo
        $per_page = $this->get_per_page();
        $data_student = $this->get_students_non_enrolled_report($per_page);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_student['data'];
        $total_count = (int) $data_student['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        // MODIFICADO: usar $per_page en lugar de 20
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

class TT_Ranking_Alliances_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'ranking_alliances',
                'plural' => 'ranking_alliancess',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'generated':
                $parsed = wc_price($item[$column_name]);
                return $parsed;
            default:
                return '<span class="text-uppercase">' . $item[$column_name] . '</span>';
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'name_legal' => __('Alliance', 'edusystem'),
            'students_registered' => __('Students registered', 'edusystem'),
            'generated' => __('Generated', 'edusystem')
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_ranking_report()
    {
        $alliance_totals = array();
        $alliance_students = array();

        $filter = $_POST['typeFilter'] ?? 'this-month';
        $custom = $_POST['custom'] ?? false;

        $dates = get_dates_search($filter, $custom);
        if (!is_array($dates) || count($dates) < 2 || empty($dates[0]) || empty($dates[1])) {
            return [];
        }

        $start_date = $dates[0];
        $end_date = $dates[1];

        $orders = get_only_orders_by_date($start_date, $end_date);
        if (!is_array($orders) && !($orders instanceof Traversable)) {
            return [];
        }

        foreach ($orders as $order) {
            if (!is_object($order) || !method_exists($order, 'get_meta') || !method_exists($order, 'get_total')) {
                continue;
            }

            $alliance_id = $order->get_meta('alliance_id');
            $student_id = $order->get_meta('student_id');
            $order_total = $order->get_total();

            if (empty($alliance_id) || !is_scalar($alliance_id)) {
                continue;
            }

            if (!empty($student_id) && is_scalar($student_id)) {
                if (!isset($alliance_students[$alliance_id])) {
                    $alliance_students[$alliance_id] = [];
                }
                if (!in_array($student_id, $alliance_students[$alliance_id])) {
                    $alliance_students[$alliance_id][] = $student_id;
                }
            }

            if (!is_numeric($order_total)) {
                continue;
            }

            if (isset($alliance_totals[$alliance_id])) {
                $alliance_totals[$alliance_id] += $order_total;
            } else {
                $alliance_totals[$alliance_id] = $order_total;
            }
        }

        $alliances_with_data = array();
        foreach ($alliance_totals as $alliance_id => $total_generated) {
            if (empty($alliance_id) || !is_scalar($alliance_id)) {
                continue;
            }

            $alliance = get_alliance_detail($alliance_id);

            if (!$alliance || !is_object($alliance)) {
                continue;
            }

            $alliance_data = (array) $alliance;
            $alliance_data['generated'] = $total_generated;
            $alliance_data['students_registered'] = isset($alliance_students[$alliance_id]) ? count($alliance_students[$alliance_id]) : 0;

            $alliances_with_data[] = $alliance_data;
        }

        if (!empty($alliances_with_data)) {
            usort($alliances_with_data, function ($a, $b) {
                $a_generated = isset($a['generated']) && is_numeric($a['generated']) ? $a['generated'] : 0;
                $b_generated = isset($b['generated']) && is_numeric($b['generated']) ? $b['generated'] : 0;
                return $b_generated <=> $a_generated;
            });
        }

        $top_10_alliances = array_slice($alliances_with_data, 0, 10);

        return $top_10_alliances;
    }

    function prepare_items()
    {

        $data = $this->get_ranking_report();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $this->items = $data;
    }
}

class TT_Ranking_Institutes_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'ranking_institute',
                'plural' => 'ranking_institutes',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'generated':
                $parsed = wc_price($item[$column_name]);
                return $parsed;
            default:
                return '<span class="text-uppercase">' . $item[$column_name] . '</span>';
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'name' => __('Institute', 'edusystem'),
            'students_registered' => __('Students registered', 'edusystem'),
            'generated' => __('Generated', 'edusystem')
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function get_ranking_report()
    {
        $institute_totals = array();
        $institute_students = array();

        $filter = $_POST['typeFilter'] ?? 'this-month';
        $custom = $_POST['custom'] ?? false;

        $dates = get_dates_search($filter, $custom);
        if (!is_array($dates) || count($dates) < 2 || empty($dates[0]) || empty($dates[1])) {
            return [];
        }

        $start_date = $dates[0];
        $end_date = $dates[1];

        $orders = get_only_orders_by_date($start_date, $end_date);
        if (!is_array($orders) && !($orders instanceof Traversable)) {
            return [];
        }

        foreach ($orders as $order) {
            if (!is_object($order) || !method_exists($order, 'get_meta') || !method_exists($order, 'get_total')) {
                continue;
            }

            $institute_id = $order->get_meta('institute_id');
            $student_id = $order->get_meta('student_id');
            $order_total = $order->get_total();

            if (empty($institute_id) || !is_scalar($institute_id)) {
                continue;
            }

            if (!empty($student_id) && is_scalar($student_id)) {
                if (!isset($institute_students[$institute_id])) {
                    $institute_students[$institute_id] = [];
                }
                if (!in_array($student_id, $institute_students[$institute_id])) {
                    $institute_students[$institute_id][] = $student_id;
                }
            }

            if (!is_numeric($order_total)) {
                continue;
            }

            if (isset($institute_totals[$institute_id])) {
                $institute_totals[$institute_id] += $order_total;
            } else {
                $institute_totals[$institute_id] = $order_total;
            }
        }

        $institutes_with_data = array();
        foreach ($institute_totals as $institute_id => $total_generated) {
            if (empty($institute_id) || !is_scalar($institute_id)) {
                continue;
            }

            $alliance = get_institute_details($institute_id);

            if (!$alliance || !is_object($alliance)) {
                continue;
            }

            $alliance_data = (array) $alliance;
            $alliance_data['generated'] = $total_generated;
            $alliance_data['students_registered'] = isset($institute_students[$institute_id]) ? count($institute_students[$institute_id]) : 0;

            $institutes_with_data[] = $alliance_data;
        }

        if (!empty($institutes_with_data)) {
            usort($institutes_with_data, function ($a, $b) {
                $a_generated = isset($a['generated']) && is_numeric($a['generated']) ? $a['generated'] : 0;
                $b_generated = isset($b['generated']) && is_numeric($b['generated']) ? $b['generated'] : 0;
                return $b_generated <=> $a_generated;
            });
        }

        $top_10_institutes = array_slice($institutes_with_data, 0, 10);

        return $top_10_institutes;
    }

    function prepare_items()
    {

        $data = $this->get_ranking_report();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $this->items = $data;
    }
}

function get_students_pending_elective_count()
{
    global $wpdb;
    $students_array = [];

    // PAGINATION
    $per_page = 20; // number of items per page
    $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = (($pagenum - 1) * $per_page);
    // PAGINATION

    $table_students = $wpdb->prefix . 'students';
    $students = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students} WHERE elective = 1 ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
    $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

    return $total_count;
}

function get_students_current_count()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $conditions = array();
    $params = array();
    $load = load_current_cut();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '$academic_period' AND cut_period = '$cut' AND status_id = 1 AND code_subject IS NOT NULL AND code_subject <> ''");
    $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
    $params = array_merge($params, $cut_student_ids);

    // PAGINATION
    $per_page = 20; // number of items per page
    $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = (($pagenum - 1) * $per_page);
    // PAGINATION

    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= "ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}";

    $students = $wpdb->get_results($wpdb->prepare($query, $params));
    $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

    return $total_count;
}

function get_students_active_count()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    // Contar directamente el número de filas que cumplen la condición
    $total_count = $wpdb->get_var(
        "SELECT COUNT(id) FROM {$table_students} WHERE condition_student = 1 AND status_id <> 5"
    );

    return $total_count;
}

function get_students_non_enrolled_count()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $students_array = [];
    $conditions = array();
    $params = array();
    $load = load_current_cut();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '$academic_period' AND cut_period = '$cut' AND status_id = 1 AND code_subject IS NOT NULL AND code_subject <> ''");
    $conditions[] = "id NOT IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
    $conditions[] = "condition_student = 1";
    $conditions[] = "status_id <> 5";
    $conditions[] = "elective = 0";
    $params = array_merge($params, $cut_student_ids);

    // PAGINATION
    $per_page = 20; // number of items per page
    $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = (($pagenum - 1) * $per_page);
    // PAGINATION

    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}";

    $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
    $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
    return $total_count;
}

function get_students_pending_graduation_count()
{
    global $wpdb;
    $students_array = [];
    $count = 0;
    $table_students = $wpdb->prefix . 'students';

    $query = $wpdb->prepare(
        "SELECT id, last_name, middle_last_name, `name`, middle_name
        FROM %i
        WHERE status_id != 5
        ORDER BY id DESC",
        $table_students
    );

    $students = $wpdb->get_results($query, "ARRAY_A");

    if ($students) {
        foreach ($students as $student) {
            $academic_ready = get_academic_ready($student['id']); // Asegúrate de que esta función sea eficiente
            if ($academic_ready) {
                $count++;
            }
        }
    }

    return $count;
}

function get_students_pending_documents_count()
{
    global $wpdb;

    $table_students = $wpdb->prefix . 'students';
    $table_student_documents = $wpdb->prefix . 'student_documents';

    $query = $wpdb->prepare(
        "SELECT COUNT(DISTINCT s.id)
        FROM %i AS s
        INNER JOIN %i AS d ON s.id = d.student_id
        WHERE s.status_id NOT IN (5, 6)
        AND (
            (d.attachment_id = 0 AND (d.is_required = 1 OR d.max_date_upload IS NOT NULL))
            OR 
            (d.attachment_id != 0 AND d.status IN (3, 6) AND (d.is_required = 1 OR d.max_date_upload IS NOT NULL))
        )",
        $table_students,
        $table_student_documents
    );

    $count = $wpdb->get_var($query);

    return (int) $count;
}

function get_students_graduated_count()
{
    global $wpdb;

    $table_students = $wpdb->prefix . 'students';

    // Contar directamente el número de estudiantes con status_id = 5
    $total_count = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(id) FROM %i WHERE status_id = %d",
            $table_students,
            5
        )
    );

    if ($total_count === null) {
        return 0;
    }

    return (int) $total_count; // Asegurarse de que el retorno sea un entero
}

function get_students_retired_count()
{
    global $wpdb;

    $table_students = $wpdb->prefix . 'students';

    // Contar directamente el número de estudiantes con status_id = 5
    $total_count = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(id) FROM %i WHERE status_id = %d",
            $table_students,
            6
        )
    );

    if ($total_count === null) {
        return 0;
    }

    return (int) $total_count; // Asegurarse de que el retorno sea un entero
}

function get_students_pending_matrix_count()
{
    global $wpdb;

    $table_students = $wpdb->prefix . 'students';
    $count = 0;

    // Obtener todos los estudiantes con terms_available NULL
    $students = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id FROM %i WHERE terms_available IS NULL",
            $table_students
        ),
        "ARRAY_A"
    );

    // Filtrar en PHP: solo contar los que NO están listos académicamente
    if ($students) {
        foreach ($students as $student) {
            if (!get_academic_ready($student['id'])) {
                $count++;
            }
        }
    }

    return $count;
}

function get_students_scholarships_count()
{
    global $wpdb;

    $table_scholarship_assigned_student = $wpdb->prefix . 'scholarship_assigned_student';

    // Contar directamente el número de estudiantes con status_id = 5
    $total_count = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(id) FROM %i",
            $table_scholarship_assigned_student
        )
    );

    if ($total_count === null) {
        return 0;
    }

    return (int) $total_count; // Asegurarse de que el retorno sea un entero
}
