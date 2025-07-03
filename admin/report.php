<?php

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
    $table_grades = $wpdb->prefix . 'grades';
    $total_count_current = (int) get_students_current_count();
    $total_count_active = (int) get_students_active_count();
    $total_count_pending_electives = (int) get_students_pending_elective_count();
    $total_count_non_enrolled = (int) get_students_non_enrolled_count();
    $total_count_pending_graduation = (int) get_students_pending_graduation_count();
    $total_count_graduated = (int) get_students_graduated_count();
    $total_count_scholarships = (int) get_students_scholarships_count();
    $load = load_current_cut();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
    $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'pending_electives') {
            $list_students = new TT_Pending_Elective_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'non-enrolled') {
            $list_students = new TT_Non_Enrolled_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'current') {
            $list_students = new TT_Current_Student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-current-students.php');
        } else if ($_GET['section_tab'] == 'active') {
            $list_students = new TT_Active_Student_List_Table;
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
        } else if ($_GET['section_tab'] == 'scholarships') {
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
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'college_allies_comissions') {
            $list_comissions = new TT_Pending_Elective_List_Table;
            $list_comissions->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-comissions.php');
        } else if ($_GET['section_tab'] == 'new_registrations') {
            $list_comissions = new TT_Non_Enrolled_List_Table;
            $list_comissions->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/report-comissions.php');
        }
    } else {
        $list_comissions_institute = new TT_Summary_Comissions_Institute_List_Table;
        $list_comissions_institute->prepare_items();

        $list_comissions_alliances = new TT_Summary_Comissions_Alliance_List_Table;
        $list_comissions_alliances->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/report-comissions.php');
    }
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

            // Fix para el descuento
            if (defined('FEE_INSCRIPTION') && $cuote->product_id != FEE_INSCRIPTION) {
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
    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);

    $product_quantities = array();
    $product_subtotals = array();
    $product_discounts = array();
    $product_totals = array();
    $product_taxs = array();
    $orders_count = count($orders);

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $order_items = $order->get_items();
        $discount = $order->get_total_discount();

        foreach ($order_items as $item) {
            $use_product_id = $item->get_product_id();
            $use_variation_id = $item->get_variation_id();
            $quantity = $item->get_quantity();
            $subtotal = $item->get_subtotal();
            $tax = $item->get_total_tax();
            $total = $item->get_total();

            $product_id = $use_product_id;

            if (!isset($product_quantities[$product_id])) {
                $product_quantities[$product_id] = 0;
            }
            if (!isset($product_subtotals[$product_id])) {
                $product_subtotals[$product_id] = 0;
            }
            if (!isset($product_discounts[$product_id])) {
                $product_discounts[$product_id] = 0;
            }
            if (!isset($product_totals[$product_id])) {
                $product_totals[$product_id] = 0;
            }
            if (!isset($product_taxs[$product_id])) {
                $product_taxs[$product_id] = 0;
            }

            $product_quantities[$product_id] += $quantity;
            $product_quantities_variation[$product_id][$use_variation_id] += $quantity;
            $product_subtotals[$product_id] += $subtotal;
            $product_subtotals_variation[$product_id][$use_variation_id] += $subtotal;
            $product_taxs[$product_id] += $tax;
            $product_taxs_variation[$product_id][$use_variation_id] += $tax;
            $product_discounts[$product_id] += ($product_id != FEE_INSCRIPTION) ? $discount : 0;
            $product_discounts_variation[$product_id][$use_variation_id] += ($product_id != FEE_INSCRIPTION) ? $discount : 0;
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

function get_students_report_offset($academic_period = null, $cut = null, $search)
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
                $product_name = $product->get_name();
                $calculated_totals_initial = ($orders['product_subtotals'][$product_id] - ($orders['product_discounts'][$product_id] - $orders['product_taxs'][$product_id]));
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

                uasort($orders['product_quantities_variation'][$product_id], function ($a, $b) {
                    return $b <=> $a;
                });
                foreach ($orders['product_quantities_variation'][$product_id] as $key => $variation) {
                    if ($key > 0) {
                        $product = wc_get_product($key);
                        $product_name = $product->get_name();
                        $ex_product_name = explode(' - ', $product_name);
                        $calculated_total = ($orders['product_subtotals_variation'][$product_id][$key] - ($orders['product_discounts_variation'][$product_id][$key] - $orders['product_taxs_variation'][$product_id][$key]));
                        $orders_total += $calculated_total;

                        $html .= "<tr style='background-color: #ffffff;'>";
                        $html .= "<td class='column column-primary' data-colname='" . __('Program', 'edusystem') . "'>" . $ex_product_name[1];
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
            $html .= "<td data-colname='" . __('Program', 'edusystem') . "'>" . get_name_program($student->program_id) . "</td>";
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

    function get_students_pending_elective_report()
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $students_array = [];
        $conditions = array();
        $params = array();

        // Get the search term from $_POST
        $search = $_POST['s'] ?? '';

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // 1. Fixed condition: elective students (elective = 1)
        $conditions[] = "elective = %d";
        $params[] = 1;

        // 2. Smart search condition
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Combined search for names and surnames using CONCAT_WS
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

            // Direct search in individual fields
            $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
            foreach ($individual_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Add the main search condition to the general conditions array
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            }
        }

        // 3. Building and executing the main query
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC LIMIT %d OFFSET %d"; // Add placeholders for LIMIT and OFFSET
        $params[] = $per_page;
        $params[] = $offset;

        // Execute the student query
        $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // 4. Processing the results
        if ($students) {
            foreach ($students as $student) {
                $parent = get_user_by('id', $student['partner_id']);
                $parent_full_name = '';
                $parent_email = '';
                if ($parent) {
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
                    $parent_email = $parent->user_email;
                }

                $student_full_name = '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>';

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

    function prepare_items()
    {

        $data_student = $this->get_students_pending_elective_report();

        $per_page = 10;


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

        $per_page = 20; // items per page
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

    function get_students_current_report()
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $table_school_subjects = $wpdb->prefix . 'school_subjects';

        $students_array = [];
        $conditions = array();
        $params = array();

        // Obtener el término de búsqueda de $_POST
        $search = $_POST['s'] ?? '';

        // Cargar el período académico y el corte actual
        $load = load_current_cut();
        $academic_period = $load['code'];
        $cut = $load['cut'];

        // 1. Condición de filtro por "corte" (cut)
        // Asegúrate de escapar las variables para prevenir inyección SQL
        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s AND status_id = 1 AND code_subject IS NOT NULL AND code_subject <> ''",
            $academic_period,
            $cut
        ));

        if (!empty($cut_student_ids)) {
            $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
            $params = array_merge($params, $cut_student_ids);
        } else {
            // Si no hay estudiantes para el corte, retornamos un array vacío de inmediato para evitar una consulta costosa sin resultados.
            return ['data' => [], 'total_count' => 0];
        }

        // 2. Condición de búsqueda inteligente (como en la función anterior)
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%'; // Para búsqueda general con comodines

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Búsqueda combinada de nombres y apellidos (CONCAT_WS es ideal para esto)
            $combined_fields = [
                'CONCAT_WS(" ", name, last_name)',
                'CONCAT_WS(" ", name, middle_name, last_name)',
                'CONCAT_WS(" ", name, middle_name, last_name, middle_last_name)',
                'CONCAT_WS(" ", last_name, name)',
                'CONCAT_WS(" ", last_name, middle_last_name)',
                'CONCAT_WS(" ", name, middle_name)', // Para buscar solo nombres si no se proporciona apellido
                'CONCAT_WS(" ", last_name, middle_last_name)' // Para buscar solo apellidos si no se proporciona nombre
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
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // 3. Construcción y ejecución de la consulta principal de estudiantes
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC LIMIT %d OFFSET %d"; // Añadimos placeholders para LIMIT y OFFSET
        $params[] = $per_page;
        $params[] = $offset;

        // Ejecutar la consulta de estudiantes
        $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // 4. Procesamiento de los resultados (no se necesita modificar aquí, ya que el search se aplicó antes)
        $url = admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=');
        foreach ($students as $key => $student) {
            // Reset conditions and params for the inner query to avoid carrying over from the main student query
            $inner_conditions = array();
            $inner_params = array();

            // Obtener subject_ids
            $subject_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT subject_id FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s AND status_id = 1 AND student_id = %d AND subject_id IS NOT NULL",
                $academic_period,
                $cut,
                $student['id']
            ));

            if (!empty($subject_ids)) {
                $inner_conditions[] = "id IN (" . implode(',', array_fill(0, count($subject_ids), '%d')) . ")";
                $inner_params = array_merge($inner_params, $subject_ids);
            }

            // Obtener subject_codes
            $subject_codes = $wpdb->get_col($wpdb->prepare(
                "SELECT code_subject FROM {$table_student_period_inscriptions} WHERE code_period = %s AND cut_period = %s AND status_id = 1 AND student_id = %d AND code_subject IS NOT NULL AND code_subject <> ''",
                $academic_period,
                $cut,
                $student['id']
            ));

            if (!empty($subject_codes)) {
                $inner_conditions[] = "code_subject IN (" . implode(',', array_fill(0, count($subject_codes), '%s')) . ")";
                $inner_params = array_merge($inner_params, $subject_codes);
            }

            $inner_query = "SELECT * FROM {$table_school_subjects}";

            if (!empty($inner_conditions)) {
                $inner_query .= " WHERE " . implode(" AND ", $inner_conditions);
            }

            // Manejo de errores
            $subjects = $wpdb->get_results($wpdb->prepare($inner_query, $inner_params));
            $subjects_text = '';
            foreach ($subjects as $subject) { // Simplify iteration if $key is not needed
                $subjects_text .= $subject->name . ', ';
            }
            $subjects_text = rtrim($subjects_text, ', '); // Remove trailing comma and space

            array_push($students_array, [
                'id' => $student['id'],
                'student' => '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>',
                'subjects' => '<span class="text-upper">' . $subjects_text . '</span>'
            ]);
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {

        $data_student = $this->get_students_current_report();

        $per_page = 10;


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

        $per_page = 20; // items per page
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

    function get_students_active_report()
    {
        $students_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $academic_period = $_POST['academic_period'] ?? '';
        $academic_period_cut = $_POST['academic_period_cut'] ?? '';
        $search = $_POST['s'] ?? '';

        $students = get_students_report_offset($academic_period, $academic_period_cut, $search);
        $total_count = count($students);
        $students_filtered = array_slice($students, $offset, $per_page);

        foreach ($students_filtered as $student) {
            $parent = get_user_by('id', $student->partner_id);
            $student_full_name = '<span class="text-uppercase">' . $student->last_name . ' ' . ($student->middle_last_name ?? '') . ' ' . $student->name . ' ' . ($student->middle_name ?? '') . '</span>';
            $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
            $students_array[] = ['student' => $student_full_name, 'id' => $student->id, 'id_document' => $student->id_document, 'email' => $student->email, 'parent' => $parent_full_name, 'parent_email' => $parent->user_email, 'country' => $student->country, 'grade' => get_name_grade($student->grade_id), 'institute' => $student->institute_id ? get_name_institute($student->institute_id) : $student->name_institute];
        }

        return ['data' => $students_array, 'total_count' => $total_count];
    }

    function prepare_items()
    {

        $data_student = $this->get_students_active_report();

        $per_page = 10;


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

        $per_page = 20; // items per page
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
            'name' => __('Institute', 'edusystem'),
            'amount' => __('Amount USD', 'edusystem'),
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
        $institute_generated_fees = array(); // Almacenará la suma de 'institute_fee' por instituto
        $total_sum_all_institutes = 0; // Variable para almacenar la suma total de todos los institutos

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
            // Asegúrate de que el objeto order tenga los métodos necesarios
            if (!is_object($order) || !method_exists($order, 'get_meta')) {
                continue;
            }

            $institute_id = $order->get_meta('institute_id');
            $institute_fee = $order->get_meta('institute_fee'); // Accedemos directamente al meta de la orden

            // Validaciones básicas para institute_id
            if (empty($institute_id) || !is_scalar($institute_id)) {
                continue;
            }

            // Sumar el 'institute_fee' de la orden
            if (is_numeric($institute_fee)) {
                $current_fee = (float) $institute_fee;
                if (isset($institute_generated_fees[$institute_id])) {
                    $institute_generated_fees[$institute_id] += $current_fee;
                } else {
                    $institute_generated_fees[$institute_id] = $current_fee;
                }
                $total_sum_all_institutes += $current_fee; // Sumar al total general
            }
        }

        $institutes_with_data = array();
        foreach ($institute_generated_fees as $institute_id => $total_generated) {
            if (empty($institute_id) || !is_scalar($institute_id)) {
                continue;
            }

            $institute = get_institute_details($institute_id); // Cambiado a $institute y get_institute_details

            if (!$institute || !is_object($institute)) { // Cambiado a $institute
                continue;
            }

            $institute_data = (array) $institute; // Cambiado a $institute_data y $institute
            $institute_data['amount'] = wc_price($total_generated);
            // Se elimina el campo 'students_registered'

            $institutes_with_data[] = $institute_data;
        }

        $total_record = [
            'name' => 'TOTAL',
            'amount' => wc_price($total_sum_all_institutes),
        ];

        $institutes_with_data[] = $total_record;

        return $institutes_with_data;
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
            'name_legal' => __('Alliance', 'edusystem'),
            'amount' => __('Amount USD', 'edusystem'),
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
        $alliance_generated_fees = array(); // Almacenará la suma de 'institute_fee' por instituto
        $total_sum_all_alliances = 0; // Variable para almacenar la suma total de todas las alianzas

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
            // Asegúrate de que el objeto order tenga los métodos necesarios
            if (!is_object($order) || !method_exists($order, 'get_meta')) {
                continue;
            }

            $alliance_id = $order->get_meta('alliance_id');
            $aliance_fee = $order->get_meta('alliance_fee'); // Accedemos directamente al meta de la orden

            // Validaciones básicas para alliance_id
            if (empty($alliance_id) || !is_scalar($alliance_id)) {
                continue;
            }

            // Sumar el 'aliance_fee' de la orden
            if (is_numeric($aliance_fee)) {
                $current_fee = (float) $aliance_fee;
                if (isset($alliance_generated_fees[$alliance_id])) {
                    $alliance_generated_fees[$alliance_id] += $current_fee;
                } else {
                    $alliance_generated_fees[$alliance_id] = $current_fee;
                }
                $total_sum_all_alliances += $current_fee; // Sumar al total general
            }
        }

        $alliances_with_data = array();
        foreach ($alliance_generated_fees as $alliance_id => $total_generated) {
            if (empty($alliance_id) || !is_scalar($alliance_id)) {
                continue;
            }

            $alliance = get_alliance_detail($alliance_id);

            if (!$alliance || !is_object($alliance)) {
                continue;
            }

            $alliance_data = (array) $alliance;
            $alliance_data['amount'] = wc_price($total_generated);

            $alliances_with_data[] = $alliance_data;
        }

        $total_record = [
            'name_legal' => 'TOTAL',
            'amount' => wc_price($total_sum_all_alliances),
        ];

        $alliances_with_data[] = $total_record;

        return $alliances_with_data;
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

    function get_students_pending_graduation_report()
    {
        global $wpdb;
        $students_array = [];
        $conditions = array();
        $params = array();
        $table_students = $wpdb->prefix . 'students';

        // Obtener el término de búsqueda de $_POST
        $search = $_POST['s'] ?? '';

        // Obtener el período académico y el corte del POST
        $academic_period_student = $_POST['academic_period'] ?? '';
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';

        // 1. Condición de estado del estudiante
        $conditions[] = "status_id != %d";
        $params[] = 5; // Assuming 5 is the status_id for not pending graduation

        // 2. Condición de filtro por período académico (si está presente)
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // 3. Condición de filtro por corte de período (si está presente)
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student; // Corrected variable name
        }

        // 4. Condición de búsqueda inteligente
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Búsqueda combinada de nombres y apellidos (CONCAT_WS es ideal para esto)
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

        // Construcción de la consulta principal para obtener todos los estudiantes que coinciden con las condiciones (antes de academic_ready)
        $query = "SELECT * FROM {$table_students}"; // SQL_CALC_FOUND_ROWS is not needed here since we filter in PHP

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC";

        // Obtener todos los estudiantes que cumplen las condiciones iniciales (incluida la búsqueda)
        $all_students_from_db = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");

        // Filtrar los estudiantes usando get_academic_ready()
        $filtered_students = [];
        if (!empty($all_students_from_db)) {
            foreach ($all_students_from_db as $student) {
                if (function_exists('get_academic_ready') && get_academic_ready($student['id'])) {
                    $filtered_students[] = $student;
                }
            }
        }

        // Aplicar paginación a los estudiantes REALMENTE filtrados
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $total_academic_ready_students = count($filtered_students);
        $paginated_students = array_slice($filtered_students, $offset, $per_page);

        // Procesar los estudiantes paginados
        foreach ($paginated_students as $student) {
            // Asegúrate de que get_user_by y get_user_meta existen o manejas su ausencia
            $parent = get_user_by('id', $student['partner_id']);
            $parent_full_name = '';
            $parent_email = '';
            if ($parent) {
                $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
                $parent_email = $parent->user_email;
            }

            $student_full_name = '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>';

            $students_array[] = [
                'student' => $student_full_name,
                'id' => $student['id'],
                'id_document' => $student['id_document'],
                'email' => $student['email'],
                'parent' => $parent_full_name,
                'parent_email' => $parent_email,
                'country' => $student['country'],
                'grade' => function_exists('get_name_grade') ? get_name_grade($student['grade_id']) : $student['grade_id'], // Fallback if function doesn't exist
                'institute' => (function_exists('get_name_institute') && $student['institute_id']) ? get_name_institute($student['institute_id']) : ($student['name_institute'] ?? '') // Fallback for institute
            ];
        }

        return ['data' => $students_array, 'total_count' => $total_academic_ready_students];
    }

    function prepare_items()
    {

        $data_student = $this->get_students_pending_graduation_report();

        $per_page = 10;


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

        $per_page = 20; // items per page
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

    function get_student_graduated()
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $students_array = [];
        $conditions = array();
        $params = array();

        // Obtener el término de búsqueda de $_POST
        $search = $_POST['s'] ?? '';

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // Obtener el período académico y el corte del POST
        $academic_period_student = $_POST['academic_period'] ?? '';
        $academic_period_cut_student = $_POST['academic_period_cut'] ?? '';

        // 1. Condición de estado: estudiantes graduados (status_id = 5)
        $conditions[] = "status_id = %d";
        $params[] = 5;

        // 2. Condición de filtro por período académico (si está presente)
        if (!empty($academic_period_student)) {
            $conditions[] = "academic_period = %s";
            $params[] = $academic_period_student;
        }

        // 3. Condición de filtro por corte de período (si está presente)
        if (!empty($academic_period_cut_student)) {
            $conditions[] = "initial_cut = %s";
            $params[] = $academic_period_cut_student;
        }

        // 4. Condición de búsqueda inteligente
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

        // 5. Construcción y ejecución de la consulta principal
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC LIMIT %d OFFSET %d"; // Añadimos placeholders para LIMIT y OFFSET
        $params[] = $per_page;
        $params[] = $offset;

        // Ejecutar la consulta de estudiantes
        $students = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // 6. Procesamiento de los resultados
        if ($students) {
            foreach ($students as $student) {
                $parent = get_user_by('id', $student['partner_id']);
                $parent_full_name = '';
                $parent_email = '';
                if ($parent) {
                    $parent_full_name = "<span class='text-uppercase' data-colname='" . __('Parent', 'edusystem') . "'>" . strtoupper(get_user_meta($parent->ID, 'last_name', true) . ' ' . get_user_meta($parent->ID, 'first_name', true)) . "</span>";
                    $parent_email = $parent->user_email;
                }

                $student_full_name = '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>';

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

    function prepare_items()
    {

        $data_student = $this->get_student_graduated();

        $per_page = 10;


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

        $per_page = 20; // items per page
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


    function get_student_scholarships()
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
        $academic_period_student = sanitize_text_field($_POST['academic_period'] ?? '');
        $academic_period_cut_student = sanitize_text_field($_POST['academic_period_cut'] ?? '');

        // 1. Student status condition
        $conditions[] = "s.status_id != %d";
        $params[] = 5; // Assuming 5 is the status_id for not pending graduation

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
        $per_page = 20;
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
        $params[] = $per_page;
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

                $student_full_name = '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>';

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

        $data_student = $this->get_student_scholarships();

        $per_page = 10;


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

        $per_page = 20; // items per page
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

    function get_students_non_enrolled_report()
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $students_array = [];
        $conditions = array();
        $params = array();

        // Obtener el término de búsqueda de $_POST
        $search = $_POST['s'] ?? '';

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
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // 6. Construcción y ejecución de la consulta principal
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students}";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id DESC LIMIT %d OFFSET %d"; // Añadimos placeholders para LIMIT y OFFSET
        $params[] = $per_page;
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

                $student_full_name = '<span class="text-uppercase">' . $student['last_name'] . ' ' . ($student['middle_last_name'] ?? '') . ' ' . $student['name'] . ' ' . ($student['middle_name'] ?? '') . '</span>';

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

        $data_student = $this->get_students_non_enrolled_report();

        $per_page = 10;


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

        $per_page = 20; // items per page
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
