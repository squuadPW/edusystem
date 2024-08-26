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
        array_push($data_fees, [
            'order_id' => $order->get_id(),
            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'student' => $order->get_meta('student_data') ? $order->get_meta('student_data')['name_student'] . ' ' . $order->get_meta('student_data')['middle_name_student'] . ' ' . $order->get_meta('student_data')['last_name_student'] . ' ' . $order->get_meta('student_data')['middle_last_name_student'] : 'N/A',
            'total' => $order->get_total(),
            'created_at' => $order->get_date_created()->format('F j, Y g:i a')
        ]);

        $institute_fee += (float) $order->get_meta('institute_fee');
        $alliance_fee += (float) $order->get_meta('alliance_fee');
        $fee_payment += 0;
        $fee_system += ($order->get_total() * 0.01); // 1% del total de la orden
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
        if (!isset($payment_methods[$payment_method])) {
            $payment_methods[$payment_method] = 0;
        }
        $payment_methods[$payment_method] += $order->get_total();
    }

    // formateamos los totales de los métodos de pago
    $payment_methods = array_map(function ($total) {
        return get_woocommerce_currency_symbol() . number_format($total, 2, '.', ',');
    }, $payment_methods);

    $table_student_payments = $wpdb->prefix.'student_payments';
    $cuotes = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE status_id=0");
    foreach ($cuotes as $cuote) {
        $created_at = $cuote->created_at;
        $month = date('n', strtotime($created_at)); // extract month from date string
        if ($month == 8) { // August is the 8th month
            $order_id = $cuote->order_id;
            $order = wc_get_order($order_id);
            $discount = $order->get_total_discount();
            $receivable += ($cuote->amount - $discount);
        } else {
            $receivable += $cuote->amount;
        }
    }

    $total_fees = (((($institute_fee + $alliance_fee) + $tax) + $fee_payment) + $fee_system);
    return [
        'institute_fee' => get_woocommerce_currency_symbol() . number_format($institute_fee, 2, '.', ','),
        'alliance_fee' => get_woocommerce_currency_symbol() . number_format($alliance_fee, 2, '.', ','),
        'tax' => get_woocommerce_currency_symbol() . number_format($tax, 2, '.', ','),
        'fee_payment' => get_woocommerce_currency_symbol() . number_format($fee_payment, 2, '.', ','),
        'fee_system' => get_woocommerce_currency_symbol() . number_format($fee_system, 2, '.', ','),
        'fees' => get_woocommerce_currency_symbol() . number_format($total_fees, 2, '.', ','),
        'gross' => get_woocommerce_currency_symbol() . number_format($gross, 2, '.', ','),
        'net' => get_woocommerce_currency_symbol() . number_format((($gross - $discount) - $total_fees), 2, '.', ','),
        'receivable' => get_woocommerce_currency_symbol() . number_format($receivable, 2, '.', ','),
        'payment_methods' => (array) $payment_methods,
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

        if (!empty($orders['orders'])) {

            foreach ($orders['orders'] as $order) {
                $html .= "<tr>";
                $html .= "<td class='column column-primary' data-colname='" . __('Payment ID', 'aes') . "'>";
                $html .= '#' . $order['order_id'];
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";
                $html .= "<td class='column' data-colname='" . __('Customer', 'restaurant-system-app') . "'>" . $order['customer'] . "</td>";
                $html .= "<td class='column' data-colname='" . __('Student', 'restaurant-system-app') . "'>" . $order['student'] . "</td>";
                $html .= "<td class='column' data-colname='" . __('Total', 'restaurant-system-app') . "'>" . get_woocommerce_currency_symbol() . number_format($order['total'], 2, '.', ',') . "</td>";
                $html .= "<td class='column' data-colname='" . __('Created', 'restaurant-system-app') . "'><b>" . $order['created_at'] . "</b></td>";
                $html .= "<td class='column' data-colname='" . __('Action', 'restaurant-system-app') . "'>";

                $html .= "<a class='button button-primary' href='" . admin_url('admin.php?page=report-sales&section_tab=payment-detail&payment_id=' . $order['order_id']) . "'>" . __('View details', 'aes') . "</a>";


                $html .= "</td>";
                $html .= "</tr>";
            }

        } else {
            $html .= "<tr>";
            $html .= "<td colspan='5' style='text-align:center;'>" . __('There are not records', 'aes') . "</td>";
            $html .= "</tr>";
        }

        echo json_encode(['status' => 'success', 'html' => $html, 'data' => $orders]);
        exit;
    }
}

add_action('wp_ajax_nopriv_list_orders_sales', 'get_list_orders_sales');
add_action('wp_ajax_list_orders_sales', 'get_list_orders_sales');


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