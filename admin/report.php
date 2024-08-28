<?php

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

function get_orders($start, $end)
{
    global $wpdb;
    $data_fees = [];
    $institute_fee = 0.00;
    $alliance_fee = 0.00;
    $gross = 0.00;
    $discount = 0.00;
    $receivable = 0.00;
    $tax = 0.00;
    $fee_payment = 0.00;
    $fee_system = 0.00;
    $payment_methods = []; // array para almacenar los métodos de pago y sus totales
    $strtotime_start = strtotime($start);
    $strtotime_end = strtotime($end);

    $args['limit'] = -1;
    $args['status'] = 'wc-completed';
    $args['date_created'] = $strtotime_start . '...' . $strtotime_end;
    $orders = wc_get_orders($args);

    foreach ($orders as $order) {
        $customer = get_user_by('id', $order->get_customer_id());
        $student_mail = $order->get_meta('student_data') ? $order->get_meta('student_data')['email_student'] : null;
        $table_students = $wpdb->prefix.'students';
        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$student_mail}'");
        $user_student = get_user_by('email', $student_mail);
        array_push($data_fees, [
            'order_id' => $order->get_id(),
            'customer' => $customer,
            'student' => (array)$student,
            'student_id' => $user_student ? $user_student->ID : null,
            'total' => $order->get_total(),
            'created_at' => $order->get_date_created()->format('F j, Y g:i a')
        ]);

        $institute_fee += (float) $order->get_meta('institute_fee');
        $alliance_fee += (float) $order->get_meta('alliance_fee');
        $fee_payment += 0;
        $tax += $order->get_total_tax(); // obtenemos el tax de la orden
        $gross += ($order->get_subtotal() ? $order->get_subtotal() : 0);
        $discount += ($order->get_total_discount() ? $order->get_total_discount() : 0);
        if ($order->get_fees()) {
            foreach ( $order->get_fees() as $fee ) {
                $fee_payment += $fee->get_amount();
            }
        }

        // obtenemos el método de pago y sumamos el total del pedido a su total
        $payment_method = $order->get_payment_method_title();
        if ($payment_method != 'Credit Card') {
            $fee_system += ($order->get_total() * 0.014); // 1% del total de la orden
        } else {
            $fee_system += (float) $order->get_meta('fee_squuad'); // 1% del total de la orden
        }
        if (!isset($payment_methods[$payment_method])) {
            $payment_methods[$payment_method] = 0;
        }
        $payment_methods[$payment_method] += $order->get_total();
    }

    // formateamos los totales de los métodos de pago
    $payment_methods = array_map(function ($total) {
        return wc_price($total);
    }, $payment_methods);

    $table_student_payments = $wpdb->prefix.'student_payments';
    $cuotes = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_student_payments} WHERE status_id = %s AND date_next_payment BETWEEN %s AND %s ORDER BY date_next_payment ASC", 0, $start, $end));
    $cuotes_array = array();
    foreach ($cuotes as $cuote) {
        $student = get_student_detail($cuote->student_id);
        $customer = get_user_by('id', $student->partner_id);
        $user_student = get_user_by('email', $student->email);

        $cuote->student = (array)$student;
        $cuote->customer = (array)$customer;
        $cuote->student_id = $user_student ? $user_student->ID : null;

        $product = wc_get_product($cuote->variation_id ? $cuote->variation_id : $cuote->product_id);
        $cuote->product = $product->get_name();

        // for fix
        if ($cuote->product_id != AES_FEE_INSCRIPTION) {
            $created_at = $cuote->created_at;
            $month = date('n', strtotime($created_at)); // extract month from date string
            if ($month == 8) { // August is the 8th month
                $order_id = $cuote->order_id;
                $order = wc_get_order($order_id);
                if ($order) {
                    $discount = $order->get_total_discount();
                    $value = ($cuote->amount - $discount);
                    $cuote->amount = $value < 0 ? $cuote->amount : $value;
                }
            }
        }

        $cuotes_array[] = $cuote;
        $receivable += $cuote->amount;
    }

    $total_fees = (((($institute_fee + $alliance_fee) + $tax) + $fee_payment) + $fee_system);
    return [
        'institute_fee' => wc_price($institute_fee),
        'alliance_fee' => wc_price($alliance_fee),
        'tax' => wc_price($tax),
        'fee_payment' => wc_price($fee_payment),
        'fee_system' => wc_price($fee_system),
        'fees' => wc_price($total_fees),
        'gross' => wc_price($gross),
        'net' => wc_price((($gross - $discount) - $total_fees)),
        'receivable' => wc_price($receivable),
        'payment_methods' => (array) $payment_methods,
        'cuotes' => $cuotes_array,
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
    $discount = 0.00;
    $orders = wc_get_orders($args);
    foreach ($orders as $order) {
        $institute_fee += (float) $order->get_meta('institute_fee');
        $alliance_fee += (float) $order->get_meta('alliance_fee');
        $gross += ($order->get_subtotal() ? $order->get_subtotal() : 0);
        $discount += ($order->get_total_discount() ? $order->get_total_discount() : 0);
    }

    return [
        'gross' => $gross,
        'net' => (($gross - $discount) - ($institute_fee + $alliance_fee)),
    ];
}


function get_list_orders_sales()
{

    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];

        $html = "";
        $dates = get_dates_search($filter, $custom);
        $orders = get_orders($dates[0], $dates[1]);
        $url = admin_url('user-edit.php?user_id=');

        if (!empty($orders['orders'])) {

            foreach ($orders['orders'] as $order) {

                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'aes') . "'>";
                $html .= '#' . $order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                if ($order['customer']) {
                    $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>" . '<a href="'. $url . $order['customer']->data->ID .'" target="_blank">' . get_user_meta($order['customer']->data->ID, 'first_name', true) . ' ' . get_user_meta($order['customer']->data->ID, 'last_name', true) . "</a></td>";
                } else {
                    $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>N/A</td>";
                }
                if ($order['student']){
                    $html .= "<td class='column' data-colname='" . __('Student', 'restaurant-system-app') . "'>" . '<a href="'. $url . $order['student_id'] .'" target="_blank">' . $order['student']['name'] . ' ' . $order['student']['middle_name'] . ' ' . $order['student']['last_name'] . ' ' . $order['student']['middle_last_name'] . "</a></td>";
                } else {
                    $html .= "<td class='column' data-colname='" . __('Student', 'restaurant-system-app') . "'>N/A</td>";
                }
                $html .= "<td class='column' data-colname='" . __('Total', 'restaurant-system-app') . "'>" . wc_price($order['total']) . "</td>";
                $html .= "<td class='column' data-colname='" . __('Created', 'restaurant-system-app') . "'><b>" . $order['created_at'] . "</b></td>";
                $html .= "<td class='column' data-colname='" . __('Action', 'restaurant-system-app') . "'>";

                $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=report-sales&section_tab=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";


                $html .= "</td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='6' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html .= "</tr>";
        }

        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_orders_sales', 'get_list_orders_sales');
add_action('wp_ajax_list_orders_sales', 'get_list_orders_sales');

function list_accounts_receivables()
{

    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];

        $html = "";
        $dates = get_dates_search($filter, $custom);
        $orders = get_orders($dates[0], $dates[1]);
        $url = admin_url('user-edit.php?user_id=');

        if (!empty($orders['cuotes'])) {

            foreach ($orders['cuotes'] as $order) {
                $html .= "<tr>";
                if ($order->customer['data']){
                    $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>" . '<a href="'. $url . $order->customer['data']->ID .'" target="_blank">' . get_user_meta($order->customer['data']->ID, 'first_name', true) . ' ' . get_user_meta($order->customer['data']->ID, 'last_name', true) . "</a></td>";
                } else {
                    $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>N/A</td>";
                }
                if ($order->student){
                    $html .= "<td class='column' data-colname='" . __('Student', 'restaurant-system-app') . "'>" . '<a href="'. $url . $order->student_id .'" target="_blank">' . $order->student['name'] . ' ' . ($order->student['middle_name'] ?? '') . ' ' . $order->student['last_name'] . ' ' . ($order->student['middle_last_name'] ?? '') . "</a></td>";
                } else {
                    $html .= "<td class='column' data-colname='" . __('Student', 'restaurant-system-app') . "'>N/A</td>";
                }
                $html .= "<td class='column' data-colname='" . __('Product', 'restaurant-system-app') . "'>" . $order->product . "</td>";
                $html .= "<td class='column' data-colname='" . __('Amount', 'restaurant-system-app') . "'>" . wc_price($order->amount) . "</td>";
                $html .= "<td class='column' data-colname='" . __('Number cuote', 'restaurant-system-app') . "'>" . $order->cuote . "</td>";
                $html .= "<td class='column' data-colname='" . __('Total cuotes', 'restaurant-system-app') . "'>" . $order->num_cuotes . "</td>";
                $html .= "<td class='column' data-colname='" . __('Date', 'restaurant-system-app') . "'><b>" . $order->date_next_payment . "</b></td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='7' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html .= "</tr>";
        }

        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_accounts_receivables', 'list_accounts_receivables');
add_action('wp_ajax_list_accounts_receivables', 'list_accounts_receivables');


function add_admin_form_report_content()
{
    include(plugin_dir_path(__FILE__) . 'templates/report-blade.php');
}


function get_load_chart_data()
{
    if (isset($_POST['filter']) && !empty($_POST['filter'])) {

        $filter = $_POST['filter'];
        $custom = $_POST['custom'];

        $dates = get_dates_search($filter, $custom);
        $orders = get_orders($dates[0], $dates[1]);

        $date1 = new DateTime($dates[0]);
        $date2 = new DateTime($dates[1]);

        $interval = $date1->diff($date2);

        $labels = array();
        $net_sale_count = array();
        $gross_sale_counte = array();

        for ($i = 0; $i <= $interval->days; $i++) {
            $currentDate = clone $date1;
            $currentDate->modify("+$i days");
            array_push($labels, $currentDate->format('M j, Y'));

            $info_orders = get_orders_by_date($currentDate->format('Y-m-d'));
            array_push($net_sale_count, $info_orders['net']);
            array_push($gross_sale_counte, $info_orders['gross']);
        }

        $chart_data = array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => 'Gross sale',
                    'data' => $gross_sale_counte,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ),
                array(
                    'label' => 'Net sale',
                    'data' => $net_sale_count,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                )
            )
        );
        echo json_encode(['status' => 'success', 'chart_data' => $chart_data, 'orders' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_load_chart_data', 'get_load_chart_data');
add_action('wp_ajax_load_chart_data', 'get_load_chart_data');