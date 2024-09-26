<?php

function add_admin_form_payments_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_payment') {

            global $current_user;
            $name = get_user_meta($current_user->ID, 'first_name', true) . ' ' . get_user_meta($current_user->ID, 'last_name', true);
            $order_id = $_POST['order_id'];
            $description = $_POST['description'];
            $split_payment = $_POST['split_payment'] ?? null;
            $payment_confirm = $_POST['payment_confirm'] ?? null;

            $order = wc_get_order($order_id);

            if ($split_payment) {
                $split_method = $order->get_meta('split_method');
                $split_method = json_decode($split_method);
                $index = array_search($payment_confirm, array_column($split_method, 'id'));
                if ($index !== false) {
                    $split_method[$index]->status = 'completed';
                    $order->update_meta_data('split_method', json_encode($split_method));
                }

                $order->add_order_note('Payment approved by '. $name . '. Description: ' .($description != '' ? $description : 'N/A'), 2); // 2 = admin note

                $split_method_updated = $order->get_meta('split_method');
                $split_method_updated = json_decode($split_method_updated);
                $on_hold_found = false;
                foreach ($split_method_updated as $method) {
                    if ($method->status === 'on-hold') {
                        $on_hold_found = true;
                        break;
                    }
                }

                if (!$on_hold_found) {
                    $total = 0.00;
                    $total_gross = 0.00;
                    foreach ($split_method_updated as $key => $split) {
                        $total += $split->amount;
                        $total_gross += $split->gross_total;
                    }

                    $total_paid_meta = $order->get_meta('total_paid');
                    if ($total_paid_meta) {
                        $order->update_meta_data('total_paid', $total);
                    } else {
                        $order->add_meta_data('total_paid', $total);
                    }

                    $total_paid_meta = $order->get_meta('total_paid_gross');
                    if ($total_paid_meta) {
                        $order->update_meta_data('total_paid_gross', $total_gross);
                    } else {
                        $order->add_meta_data('total_paid_gross', $total_gross);
                    }
                    
                    $pending_payment_meta = $order->get_meta('pending_payment');
                    if ($pending_payment_meta) {
                        $order->update_meta_data('pending_payment', (($order->get_subtotal() - $order->get_total_discount()) - $total));
                    } else {
                        $order->add_meta_data('pending_payment', (($order->get_subtotal() - $order->get_total_discount()) - $total));
                    }

                    $order->update_status('completed');
                }
            } else {
                $order->update_status('completed');
                $order->add_order_note('Payment approved by '. $name . '. Description: ' .($description != '' ? $description : 'N/A'), 2); // 2 = admin note
                $order->update_meta_data('payment_approved_by', $current_user->ID);
            }

            $order->save();

            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content'));
            exit;
        } else if ($_GET['action'] == 'generate_payment') {
            $cancel = $_POST['cancel'];
            if (isset($cancel) && $cancel == 1) {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'));
                exit;
            }

            global $wpdb;
            $id_document = $_POST['id_document'];
            $generate = $_POST['generate'];
            $table_students = $wpdb->prefix.'students';
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id_document='{$id_document}'");

            if ($generate) {
                $amount = $_POST['amount'];
                $product_id = $_POST['product_id'];
                $partner_id = $student->partner_id;
            
                $order_data = array(
                    'user_id' => $partner_id,
                    'product_id' => $product_id,
                    'amount' => $amount
                );
            
                $quantity = 1;
                $product = wc_get_product($product_id);
            
                // Crea el pedido
                $order_args = array(
                    'customer_id' => $partner_id,
                    'status' => 'pending-payment',
                );

                $order = wc_create_order($order_args);
                $order->add_product($product, $quantity, array('subtotal' => $amount, 'total' => $amount));
                $order->set_total($amount);
                $order->update_meta_data('student_id', $student->id);
                $order->save();
            
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&success_advance_payment=true'));
                exit;
            }

            if ($student) {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document='.$id_document));
            } else {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=0&id_document='.$id_document));
            }
        } else if ($_GET['action'] == 'generate_order') {
            global $wpdb;
            $amount_order = $_POST['amount_order'];
            $date_order = $_POST['date_order'];
            $order_id = $_POST['order_id_old'];

            if (!isset($order_id)) {    
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content'));
                exit;
            }

            $order_old = wc_get_order($order_id);
            $order_old->add_meta_data('split_complete', 1);
            $order_old->update_status('on-hold'); 
            $order_old->save();

            $order_old->update_status('completed'); 
            $order_old->save();

            // Obtener el primer item de la orden vieja
            $old_order_items = $order_old->get_items();
            $first_item = reset($old_order_items);
            $customer_id = $order_old->get_customer_id();

            $order_args = array(
                'customer_id' => $customer_id,
                'status' => 'pending-payment',
            );
            
            $new_order = wc_create_order($order_args);
            $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
            $new_order->add_meta_data('institute_id', $order_old->get_meta('institute_id'));
            $new_order->add_meta_data('is_vat_exempt', $order_old->get_meta('is_vat_exempt'));
            $new_order->add_meta_data('pending_payment', $order_old->get_meta('pending_payment'));
            $new_order->add_meta_data('student_id', $order_old->get_meta('student_id'));
            $new_order->set_date_created($date_order);
            $product = $first_item->get_product();
            $product->set_price($amount_order);
            $new_order->add_product($product, $first_item->get_quantity());
            $new_order->calculate_totals();
            if ($order_old->get_address('billing')) {
                $billing_address = $order_old->get_address('billing');
                $new_order->set_billing_first_name($billing_address['first_name']);
                $new_order->set_billing_last_name($billing_address['last_name']);
                $new_order->set_billing_company($billing_address['company']);
                $new_order->set_billing_address_1($billing_address['address_1']);
                $new_order->set_billing_address_2($billing_address['address_2']);
                $new_order->set_billing_city($billing_address['city']);
                $new_order->set_billing_state($billing_address['state']);
                $new_order->set_billing_postcode($billing_address['postcode']);
                $new_order->set_billing_country($billing_address['country']);
                $new_order->set_billing_email($billing_address['email']);
                $new_order->set_billing_phone($billing_address['phone']);
            }
            $new_order->save();

        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {


        if ($_GET['section_tab'] == 'all_payments') {
            $list_payments = new TT_all_payments_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
        } else if ($_GET['section_tab'] == 'order_detail') {

            global $current_user;
            $roles = $current_user->roles;
            $order_id = $_GET['order_id'];
            $order = wc_get_order($order_id);

            include(plugin_dir_path(__FILE__) . 'templates/payment-details.php');
        } else if ($_GET['section_tab'] == 'invoices_alliances') {

            if ($_GET['id_payment']) {
                global $wpdb;
                $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
                $wpdb->update($table_alliances_payments, ['status_id' => 1], ['id' => $_GET['id_payment']]);
            }

            $list_payments = new TT_Invoices_Alliances_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-invoices-alliance.php');
        } else if ($_GET['section_tab'] == 'invoices_institutes') {

            if ($_GET['id_payment']) {
                global $wpdb;
                $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
                $wpdb->update($table_institutes_payments, ['status_id' => 1], ['id' => $_GET['id_payment']]);
            }

            $list_payments = new TT_Invoices_Institutes_List_Table();
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-invoices-institutes.php');
        } else if ($_GET['section_tab'] == 'generate_advance_payment') {
            global $wpdb;
            $id_document = $_GET['id_document'];
            $generate = $_GET['generate'];
            $table_students = $wpdb->prefix.'students';
            $table_student_payments = $wpdb->prefix.'student_payments';
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id_document='{$id_document}'");
            if ($student) {
                $payment = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE student_id='{$student->id}' AND status_id = 0 ORDER BY cuote ASC");
            }
            include(plugin_dir_path(__FILE__) . 'templates/generate-advance-payment.php');
        }


    } else {
        $list_payments = new TT_payment_pending_List_Table;
        $list_payments->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
    }
}

function success_advance_payment() {
    if (isset($_GET['success_advance_payment']) && $_GET['success_advance_payment'] == 'true') {
      ?>
      <div class="notice notice-success is-dismissible">
        <p>Payment generated successfully</p>
      </div>
      <?php
    }
  }
  
  // Add the success message to the admin_notices action
  add_action('admin_notices', 'success_advance_payment');


class TT_payment_pending_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'payment_pending',
            'plural' => 'payment_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'payment_id':
                return '#' . $item[$column_name];
            case 'date':
            case 'status':
            case 'payment_method':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'payment_id' => __('Payment ID', 'aes'),
            'date' => __('Date', 'aes'),
            'partner_name' => __('Name', 'aes'),
            'total' => __('Total', 'aes'),
            'payment_method' => __('Payment Method', 'aes'),
            'status' => __('Status', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_payment_pendings()
    {
        $orders_array = [];
        $args = [];
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $args['s'] = $_POST['s'];
        }

        $args['limit'] = $per_page; // limit to 10 orders per page
        $args['offset'] = $offset; // offset to start from the first order
        $args['status'] = array('wc-pending', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                array_push($orders_array, [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created()->format('F j, Y g:i a'),
                    'partner_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'total' => wc_price($order->get_total()),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        $args_filtered['limit'] = -1;
        $args_filtered['status'] = array('wc-pending', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $total_count = wc_get_orders(array_merge($args_filtered, array('return' => 'count')));
        return ['data' => $orders_array, 'total_count' => sizeof($total_count)];
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

        $data_payments = $this->get_payment_pendings();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $data = $data_payments['data'];
        $total_count = (int) $data_payments['total_count'];
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

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

class TT_all_payments_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'payment_pending',
            'plural' => 'payment_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'payment_id':
                return '#' . $item[$column_name];
            case 'date':
            case 'payment_method':
            case 'status':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'payment_id' => __('Payment ID', 'aes'),
            'date' => __('Date', 'aes'),
            'partner_name' => __('Name', 'aes'),
            'total' => __('Total', 'aes'),
            'payment_method' => __('Payment Method', 'aes'),
            'status' => __('Status', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_payment()
    {
        $orders_array = [];
        $args = [];
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $args['s'] = $_POST['s'];
        }

        $args['limit'] = $per_page; // limit to 10 orders per page
        $args['offset'] = $offset; // offset to start from the first order
        $args['status'] = array('wc-pending', 'wc-completed', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                array_push($orders_array, [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created()->format('F j, Y g:i a'),
                    'partner_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'total' => wc_price($order->get_total()),
                    'status' => $order->get_status(),
                    'payment_method' => $order->get_payment_method_title()
                ]);
            }
        }

        $args_filtered['limit'] = -1;
        $args_filtered['status'] = array('wc-pending', 'wc-completed', 'wc-cancelled', 'wc-processing', 'wc-on-hold');
        $total_count = wc_get_orders(array_merge($args_filtered, array('return' => 'count')));
        return ['data' => $orders_array, 'total_count' => sizeof($total_count)];
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

        $data_payments = $this->get_payment();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_payments['data'];
        $total_count = (int) $data_payments['total_count'];

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

class TT_Invoices_Alliances_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'invoice_alliance',
            'plural' => 'invoices_alliances',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'status_id':
                return get_status_payment_institute($item->status_id);
            case 'alliance_id':
                $alliance = get_alliance_detail($item->alliance_id);
                return "{$alliance->name} {$alliance->last_name} - {$alliance->name_legal}";
            case 'month':
                return $item->month;
            case 'amount':
                return wc_price($item->amount);
            case 'total_orders':
                return $item->total_orders;
            case 'view_details':
                if ($item->status_id == 0) {
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'form-plugin') . "</a>";
                } else {
                    return "N/A";
                }
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'status_id' => __('Status', 'aes'),
            'alliance_id' => __('Alliance', 'aes'),
            'month' => __('Month', 'aes'),
            'amount' => __('Amount', 'aes'),
            'total_orders' => __('Total Orders', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_invoices_alliances()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
        $transactions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_alliances_payments} LIMIT {$per_page} OFFSET {$offset}");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        return ['data' => $transactions, 'total_count' => $total_count];
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

        $data_invoices = $this->get_invoices_alliances();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_invoices['data'];
        $total_count = (int) $data_invoices['total_count'];

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

class TT_Invoices_Institutes_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'invoice_institute',
            'plural' => 'invoices_institutes',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'status_id':
                return get_status_payment_institute($item->status_id);
            case 'institute_id':
                $institute = get_institute_details($item->institute_id);
                return "{$institute->name} {$institute->last_name} - {$institute->business_name}";
            case 'month':
                return $item->month;
            case 'amount':
                return wc_price($item->amount);
            case 'total_orders':
                return $item->total_orders;
            case 'view_details':
                if ($item->status_id == 0) {
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'form-plugin') . "</a>";
                } else {
                    return "N/A";
                }
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'status_id' => __('Status', 'aes'),
            'institute_id' => __('Institute', 'aes'),
            'month' => __('Month', 'aes'),
            'amount' => __('Amount', 'aes'),
            'total_orders' => __('Total Orders', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_invoices_institutes()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
        $transactions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes_payments} LIMIT {$per_page} OFFSET {$offset}");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        return ['data' => $transactions, 'total_count' => $total_count];
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

        $data_invoices = $this->get_invoices_institutes();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_invoices['data'];
        $total_count = (int) $data_invoices['total_count'];

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