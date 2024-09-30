<?php

function add_admin_form_academic_periods_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_academic_period') {
            try {
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'period_details') {
            $period = $_GET['period_id'];
            $period = get_period_details($period);
            include (plugin_dir_path(__FILE__) . 'templates/academic-period-detail.php');
        }
        if ($_GET['section_tab'] == 'add_period') {
            include (plugin_dir_path(__FILE__) . 'templates/academic-period-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_period_details') {
            global $wpdb;
            $table_periods = $wpdb->prefix . 'academic_periods';
            $period_id = $_POST['period_id'];
            $name = $_POST['name'];
            $code = $_POST['code'];
            $year = $_POST['year'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $start_date_A = $_POST['start_date_A'];
            $end_date_A = $_POST['end_date_A'];
            $start_date_B = $_POST['start_date_B'];
            $end_date_B = $_POST['end_date_B'];
            $start_date_C = $_POST['start_date_C'];
            $end_date_C = $_POST['end_date_C'];
            $start_date_D = $_POST['start_date_D'];
            $end_date_D = $_POST['end_date_D'];
            $start_date_E = $_POST['start_date_E'];
            $end_date_E = $_POST['end_date_E'];
            $start_date_inscriptions = $_POST['start_date_inscriptions'];
            $end_date_inscriptions = $_POST['end_date_inscriptions'];
            $start_date_pre_inscriptions = $_POST['start_date_pre_inscriptions'];
            $end_date_pre_inscriptions = $_POST['end_date_pre_inscriptions'];
            $status_id = $_POST['status_id'] ?? 0;

            //update
            if (isset($period_id) && !empty($period_id)) {

                $wpdb->update($table_periods, [
                    'name' => $name,
                    'code' => $code,
                    'year' => $year,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date_A' => $start_date_A,
                    'end_date_A' => $end_date_A,
                    'start_date_B' => $start_date_B,
                    'end_date_B' => $end_date_B,
                    'start_date_C' => $start_date_C,
                    'end_date_C' => $end_date_C,
                    'start_date_D' => $start_date_D,
                    'end_date_D' => $end_date_D,
                    'start_date_E' => $start_date_E,
                    'end_date_E' => $end_date_E,
                    'status_id' => $status_id,
                    'start_date_inscription' => $start_date_inscriptions,
                    'end_date_inscription' => $end_date_inscriptions,
                    'start_date_pre_inscription' => $start_date_pre_inscriptions,
                    'end_date_pre_inscription' => $end_date_pre_inscriptions,
                ], ['id' => $period_id]);

                // if ($status_id == 1) {
                //     $args = array(
                //         'role' => 'parent',
                //     );

                //     $users = get_users($args);

                //     foreach ($users as $user) {
                //         $customer_id = $user->ID;

                //         // Get student IDs from wp_students table where partner_id is customer_id
                //         $student_ids = $wpdb->get_col("SELECT id FROM wp_students WHERE partner_id = '$customer_id'");

                //         // Get payments from wp_student_payments table where student_id is in student_ids and status_id is 0
                //         $payments = [];
                //         if (sizeof($student_ids) > 0) {
                //             $payments = $wpdb->get_results("
                //                 SELECT DISTINCT product_id, variation_id, student_id, amount 
                //                 FROM wp_student_payments 
                //                 WHERE student_id IN (" . implode(',', $student_ids) . ") 
                //                 AND status_id = 0
                //             ");
                //         }

                //         foreach ($payments as $key => $payment) {
                //             if ($payment->product_id && $payment->variation_id) {
                //                 $product_id = $payment->product_id;
                //                 $variation_id = $payment->variation_id;
                //                 $total = wc_price($payment->amount);
                //                 $quantity = 1;

                //                 // Obtiene el objeto de producto variación
                //                 $variation = wc_get_product($variation_id);

                //                 // Crea el pedido
                //                 $order_args = array(
                //                     'customer_id' => $customer_id,
                //                     'status' => 'pending-payment',
                //                 );
                //                 $order = wc_create_order($order_args);
                //                 $order->add_product($variation, $quantity);
                //                 $order->set_total($total);
                //                 $order->update_meta_data('student_id', $payment->student_id);
                //                 $order->save();
                //             }
                //         }
                //     }
                // }

                setcookie('message', __('Changes saved successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $period_id));
                exit;
            } else {

                $wpdb->insert($table_periods, [
                    'name' => $name,
                    'code' => $code,
                    'year' => $year,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_date_A' => $start_date_A,
                    'end_date_A' => $end_date_A,
                    'start_date_B' => $start_date_B,
                    'end_date_B' => $end_date_B,
                    'start_date_C' => $start_date_C,
                    'end_date_C' => $end_date_C,
                    'start_date_D' => $start_date_D,
                    'end_date_D' => $end_date_D,
                    'start_date_E' => $start_date_E,
                    'end_date_E' => $end_date_E,
                    'start_date_inscription' => $start_date_inscriptions,
                    'end_date_inscription' => $end_date_inscriptions,
                    'start_date_pre_inscription' => $start_date_pre_inscriptions,
                    'end_date_pre_inscription' => $end_date_pre_inscriptions,
                    'status_id' => $status_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // if ($status_id == 1) {
                //     $args = array(
                //         'role' => 'parent',
                //     );

                //     $users = get_users($args);

                //     foreach ($users as $user) {
                //         $customer_id = $user->ID;

                //         // Get student IDs from wp_students table where partner_id is customer_id
                //         $student_ids = $wpdb->get_col("SELECT id FROM wp_students WHERE partner_id = '$customer_id'");

                //         // Get payments from wp_student_payments table where student_id is in student_ids and status_id is 0
                //         $payments = [];
                //         if (sizeof($student_ids) > 0) {
                //             $payments = $wpdb->get_results("
                //                 SELECT DISTINCT product_id, variation_id, student_id 
                //                 FROM wp_student_payments 
                //                 WHERE student_id IN (" . implode(',', $student_ids) . ") 
                //                 AND status_id = 0
                //             ");
                //         }

                //         foreach ($payments as $key => $payment) {
                //             if ($payment->product_id && $payment->variation_id) {
                //                 $product_id = $payment->product_id;
                //                 $variation_id = $payment->variation_id;
                //                 $total = wc_get_product($product_id)->get_price();
                //                 $quantity = 1;

                //                 // Obtiene el objeto de producto variación
                //                 $variation = wc_get_product($variation_id);

                //                 // Crea el pedido
                //                 $order_args = array(
                //                     'customer_id' => $customer_id,
                //                     'status' => 'pending-payment',
                //                 );
                //                 $order = wc_create_order($order_args);
                //                 $order->add_product($variation, $quantity);
                //                 $order->set_total($total);
                //                 $order->update_meta_data('student_id', $payment->student_id);
                //                 $order->save();
                //             }
                //         }
                //     }
                // }

                wp_redirect(admin_url('admin.php?page=add_admin_form_academic_periods_content'));
                exit;

            }
        } else {
            $list_academic_periods = new TT_academic_period_all_List_Table;
            $list_academic_periods->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-academic-periods.php');
        }
    }
}

class TT_academic_period_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'academic_period_',
                'plural' => 'academic_period_s',
                'ajax' => true
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'academic_period_id':
                return '#' . $item[$column_name];
            case 'name':
                return ucwords($item[$column_name]);
            case 'status_id':
                switch ($item[$column_name]) {
                    case 1:
                        return 'Active';
                        break;

                    default:
                        return 'Inactive';
                        break;
                }
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_academic_periods_content&section_tab=period_details&period_id=' . $item['academic_period_id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
            default:
                return ucwords($item[$column_name]);
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
            'academic_period_code' => __('Period', 'aes'),
            'name' => __('Description', 'aes'),
            'status_id' => __('Status', 'aes'),
            'date' => __('Created at', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_academic_period_pendings()
    {
        global $wpdb;
        $academic_periods_array = [];

        $academic_periods = $wpdb->get_results("SELECT * FROM wp_academic_periods");

        if ($academic_periods) {
            foreach ($academic_periods as $academic_period) {
                array_push($academic_periods_array, [
                    'academic_period_code' => $academic_period->code,
                    'academic_period_id' => $academic_period->id,
                    'name' => $academic_period->name,
                    'status_id' => $academic_period->status_id,
                    'date' => $academic_period->created_at,
                ]);
            }
        }

        return $academic_periods_array;
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

        $data_academic_periods = $this->get_academic_period_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_academic_periods;

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $this->items = $data;
    }

}

function get_period_details($period_id)
{

    global $wpdb;
    $table_periods = $wpdb->prefix . 'academic_periods';

    $period = $wpdb->get_row("SELECT * FROM {$table_periods} WHERE id={$period_id}");
    return $period;
}