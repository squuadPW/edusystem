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
        $date = get_dates_search('this-month', '');
        $start_date = date('01/m/Y', strtotime('first day of this month'));
        $orders = get_orders($date[0], $date[1]);
        include(plugin_dir_path(__FILE__) . 'templates/report-sales.php');
    }
}

function get_orders($start, $end)
{
    $data_fees = [];
    $institute_fee = 0.00;
    $alliance_fee = 0.00;
    $total = 0.00;
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
        $net_total += $order->get_total(); // sumamos el total del pedido al total general

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

    return [
        'institute_fee' => get_woocommerce_currency_symbol() . number_format($institute_fee, 2, '.', ','),
        'alliance_fee' => get_woocommerce_currency_symbol() . number_format($alliance_fee, 2, '.', ','),
        'net_total' => get_woocommerce_currency_symbol() . number_format($net_total, 2, '.', ','),
        'payment_methods' => (array) $payment_methods,
        'orders' => $data_fees
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
    echo '';
}