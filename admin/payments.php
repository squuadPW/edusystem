<?php

function add_admin_form_payments_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_payment') {

            global $current_user, $wpdb;
            $name = get_user_meta($current_user->ID, 'first_name', true) . ' ' . get_user_meta($current_user->ID, 'last_name', true);
            $order_id = $_POST['order_id'];
            $status_id = $_POST['status_id'];
            $description = $_POST['description'] ?? '';
            $split_payment = $_POST['split_payment'] ?? null;
            $finish_order = $_POST['finish_order'] ?? null;
            $payment_confirm = $_POST['payment_confirm'] ?? null;
            $cuote_credit = $_POST['cuote_credit'] ?? null;
            $payment_selected = $_POST['payment_selected'] ?? null;
            $other_payments = $_POST['other_payments'] ?? null;
            $transaction_id = $_POST['transaction_id'] ?? null;
            $order = wc_get_order($order_id);

            if ($status_id == 'completed') {
                if ($split_payment) {
                    $split_method = $order->get_meta('split_method');
                    $split_method = json_decode($split_method);
                    $index = array_search($payment_confirm, array_column($split_method, 'id'));
                    if ($index !== false) {
                        $split_method[$index]->status = 'completed';
                        $order->update_meta_data('split_method', json_encode($split_method));
                    }

                    $order->add_order_note('Payment verified by ' . $name . '. Description: ' . ($description != '' ? $description : 'N/A'), 2); // 2 = admin note

                    $split_method_updated = $order->get_meta('split_method');
                    $split_method_updated = json_decode($split_method_updated);
                    $on_hold_found = false;
                    foreach ($split_method_updated as $method) {
                        if ($method->status === 'on-hold') {
                            $on_hold_found = true;
                            break;
                        }
                    }

                    $total = 0.00;
                    $total_gross = 0.00;
                    foreach ($split_method_updated as $key => $split) {
                        $total += $split->amount;
                        $total_gross += $split->gross_total;
                    }

                    // Actualizar metadatos de pago total
                    if ($order->get_meta('total_paid')) {
                        $order->update_meta_data('total_paid', $total);
                    } else {
                        $order->add_meta_data('total_paid', $total);
                    }

                    if ($order->get_meta('total_paid_gross')) {
                        $order->update_meta_data('total_paid_gross', $total_gross);
                    } else {
                        $order->add_meta_data('total_paid_gross', $total_gross);
                    }

                    if ($order->get_meta('pending_payment')) {
                        $order->update_meta_data('pending_payment', ($order->get_total() - $total));
                    } else {
                        $order->add_meta_data('pending_payment', ($order->get_total() - $total));
                    }

                    if (!$on_hold_found) {
                        if ((float) $order->get_meta('pending_payment') <= 0) {
                            if ($order->get_status() == 'pending') {
                                update_order_pending_approved($order, $payment_selected, $transaction_id, $other_payments);
                            }
                            // Cambiar a set_status() para disparar el hook
                            $order->set_status('completed');
                        } else {
                            // Cambiar a set_status() para disparar el hook
                            $order->set_status('pending-payment');
                        }
                    }

                    if ($finish_order) {
                        $discount_amount = (float) $order->get_meta('pending_payment');
                        $total = $order->get_total();
                        $new_total = ($total - $discount_amount);
                        $order->set_total($new_total);
                        $order->add_meta_data('discount_from_split', (float) $order->get_meta('pending_payment'));
                        $order->update_meta_data('pending_payment', 0);

                        foreach ($split_method_updated as $key => $split) {
                            $split->status = 'completed';
                        }

                        if ($order->get_status() == 'pending') {
                            update_order_pending_approved($order, $payment_selected, $transaction_id, $other_payments);
                        }

                        $order->update_meta_data('split_method', json_encode($split_method_updated));
                        // Cambiar a set_status() para disparar el hook
                        $order->set_status('completed');
                    }
                } else {

                    if ($order->get_status() == 'pending') {
                        update_order_pending_approved($order, $payment_selected, $transaction_id, $other_payments);
                    }

                    // Cambiar a set_status() para disparar el hook
                    $status_order = $order->get_status();
                    if ($status_order != 'completed') {
                        $order->set_status('completed');
                        $order->add_order_note('Payment verified by ' . $name . '. Description: ' . ($description != '' ? $description : 'N/A'), 2); // 2 = admin note
                        $order->update_meta_data('payment_approved_by', $current_user->ID);
                    }

                    if ($cuote_credit) {

                        // obtiene el toltal de los item de la orden sin los fee bancarios
                        $total_order_items = 0;
                        foreach ($order->get_items() as $item) {
                            $total_order_items += $item->get_total(); // subtotal sin impuestos
                        }

                        $table_student_payments = $wpdb->prefix . 'student_payments';
                        $student_id = $order->get_meta('student_id');

                        // obtiene la cuota a pagar
                        $cuote_payment_id = $order->get_meta('cuote_payment') ?? 0;
                        if ($cuote_payment_id) {

                            $cuote_payment = $wpdb->get_row($wpdb->prepare(
                                "SELECT *, COALESCE( amount, 0) AS amount_pay FROM {$table_student_payments} 
                                WHERE id = %d",
                                $cuote_payment_id
                            ));

                        } else {
                            $cuote_payment = $wpdb->get_row($wpdb->prepare(
                                "SELECT *, COALESCE( SUM(amount), 0) AS amount_pay FROM {$table_student_payments}
                                WHERE student_id = %d AND status_id = 0 AND cuote = (
                                        SELECT MIN(cuote) 
                                        FROM {$table_student_payments} 
                                        WHERE student_id = %d AND status_id = 0
                                    )
                                ORDER BY num_cuotes DESC
                                LIMIT 1;",
                                $student_id,
                                $student_id
                            ));
                        }

                        // Primero verificamos si ya existe un registro para el estudiante y obtenemos el balance actual
                        $table_student_balance = $wpdb->prefix . 'student_balance';
                        $student_balance = $wpdb->get_row($wpdb->prepare(
                            "SELECT id, balance FROM $table_student_balance WHERE student_id = %d",
                            $student_id
                        ));

                        $balance = $student_balance->balance ?? 0;
                        $balance_id = $student_balance->id ?? 0;

                        // guarda el monto de mas en el balance
                        if ($status_order != 'completed' && $total_order_items > $cuote_payment->amount_pay) {

                            $amount_credit = $total_order_items - $cuote_payment->amount_pay;

                            // nuevos valores de la cuota
                            $new_amount = $total_order_items;

                            $increased_portion = $amount_credit / $cuote_payment->amount;
                            $new_original_amount_product = $cuote_payment->original_amount_product * (1 + $increased_portion);

                            // actualiza el monto de la original
                            $wpdb->update($table_student_payments, [
                                'amount' => $new_amount,
                                'original_amount_product' => $new_original_amount_product,
                            ], ['id' => $cuote_payment->id]);

                            $balance = $balance + $amount_credit;
                            if (!$student_balance) {
                                $wpdb->insert(
                                    $wpdb->prefix . 'student_balance',
                                    [
                                        'student_id' => $student_id,
                                        'balance' => $balance ?? 0,
                                    ],
                                    ['%d', '%f']
                                );

                                $balance_id = $wpdb->insert_id;
                            }

                            // registra la cantidad de mas que fue acreditada
                            $order->update_meta_data('amount_credit', $amount_credit);
                        } elseif ($total_order_items < $cuote_payment->amount_pay) {

                            $less_amount = $cuote_payment->amount_pay - $total_order_items;

                            // Nuevos montos 
                            $new_amount = $total_order_items;

                            $paid_portion = $less_amount / $cuote_payment->amount;
                            $new_original_amount_product = $cuote_payment->original_amount_product * (1 - $paid_portion);

                            // actualiza el monto de la original
                            $wpdb->update($table_student_payments, [
                                'amount' => $new_amount,
                                'original_amount_product' => $new_original_amount_product,
                            ], ['id' => $cuote_payment->id]);

                            if ($cuote_credit == 'new_cuote') {

                                $increased_portion = $less_amount / $cuote_payment->amount;
                                $new_original_amount_product = $cuote_payment->original_amount_product * $increased_portion;

                                // obtiene la fecha del nuevo pago
                                if (isset($_POST['new_coute_date']) && !empty($_POST['new_coute_date'])) {
                                    $new_coute_date = new DateTime($_POST['new_coute_date']);
                                } else {
                                    $new_coute_date = new DateTime(); // fecha actual
                                    $new_coute_date->modify('+1 week'); // suma una semana
                                }

                                $wpdb->insert(
                                    $table_student_payments,
                                    [
                                        'status_id' => 0,
                                        'order_id' => null,
                                        'student_id' => $cuote_payment->student_id,
                                        'product_id' => $cuote_payment->product_id,
                                        'variation_id' => $cuote_payment->variation_id,
                                        'manager_id' => null,
                                        'institute_id' => null,
                                        'institute_fee' => 0,
                                        'alliances' => null,
                                        'amount' => $less_amount,
                                        'original_amount_product' => $new_original_amount_product,
                                        'total_amount' => $cuote_payment->total_amount,
                                        'original_amount' => $cuote_payment->original_amount,
                                        'discount_amount' => $cuote_payment->discount_amount,
                                        'type_payment' => 1,
                                        'cuote' => 0,
                                        'num_cuotes' => 0,
                                        'date_payment' => null,
                                        'date_next_payment' => $new_coute_date->format('Y-m-d'),
                                    ]
                                );

                                // Obtén todas las cuotas para ese estudiante, producto y variación ordenadas por fecha
                                $payments = $wpdb->get_results($wpdb->prepare(
                                    "SELECT id, date_next_payment FROM {$table_student_payments} 
                                    WHERE student_id = %d AND product_id = %d AND variation_id = %d
                                    ORDER BY date_next_payment ASC",
                                    $cuote_payment->student_id,
                                    $cuote_payment->product_id,
                                    $cuote_payment->variation_id
                                ));

                                // Recorre y actualiza el campo cuote con el orden correcto
                                $counter = 1;
                                foreach ($payments as $payment) {
                                    $wpdb->update(
                                        $table_student_payments,
                                        [
                                            'cuote' => $counter,
                                            'num_cuotes' => count($payments), // total de cuotas
                                            'type_payment' => 1
                                        ],
                                        ['id' => $payment->id]
                                    );
                                    $counter++;
                                }

                            } else {

                                $payment_row = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE id = {$cuote_credit}");

                                // nuevos valores de la cuota
                                $new_amount = $payment_row->amount + $less_amount;

                                $increased_portion = $less_amount / $payment_row->amount;
                                $new_original_amount_product = $payment_row->original_amount_product * (1 + $increased_portion);

                                $wpdb->update($table_student_payments, [
                                    'amount' => $new_amount,
                                    'original_amount_product' => $new_original_amount_product,
                                ], ['id' => $payment_row->id]);

                            }
                        }

                        $recargar = false;
                        if ($balance > 0) {

                            // nueva quota a pagar
                            $payment_row = $wpdb->get_row("SELECT id, amount, original_amount_product, cuote  FROM {$table_student_payments} WHERE id = {$cuote_credit}");
                            if ($balance < $payment_row->amount) {

                                $new_amount = $payment_row->amount - $balance;

                                // Nuevo monto original proporcional
                                $paid_portion = $balance / $payment_row->amount;
                                $new_original_amount_product = $payment_row->original_amount_product * (1 - $paid_portion);

                                $wpdb->update($table_student_payments, [
                                    'amount' => $new_amount,
                                    'original_amount_product' => $new_original_amount_product,
                                ], ['id' => $payment_row->id]);

                                $balance = 0;

                            } elseif ($balance >= $payment_row->amount) {

                                $balance = $balance - $payment_row->amount;
                                $wpdb->delete($table_student_payments, array('id' => $payment_row->id));

                                // actualiza el total de las cuotas si elimina una
                                $wpdb->query($wpdb->prepare(
                                    "UPDATE {$table_student_payments} 
                                    SET num_cuotes = num_cuotes - 1
                                    WHERE student_id = %d AND product_id = %d AND variation_id = %d AND num_cuotes > 1",
                                    $cuote_payment->student_id,
                                    $cuote_payment->product_id,
                                    $cuote_payment->variation_id
                                ));

                                // actualiza el numero de cuota si elimina una
                                $wpdb->query($wpdb->prepare(
                                    "UPDATE {$table_student_payments} 
                                    SET cuote = cuote - 1
                                    WHERE student_id = %d AND product_id = %d AND variation_id = %d AND cuote > %d ",
                                    $cuote_payment->student_id,
                                    $cuote_payment->product_id,
                                    $cuote_payment->variation_id,
                                    $payment_row->cuote,
                                ));

                                $recargar = true;
                            }

                            $wpdb->update($table_student_balance, [
                                'balance' => $balance,
                            ], ['id' => $balance_id]);

                        }
                    }
                }

                $order->save();

                if ($recargar && true) {
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $order_id));
                } else {
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content'));
                }

            } else {
                // Cambiar a set_status() para disparar el hook
                $order->set_status('cancelled');
                $order->add_order_note('Payment declined by ' . $name . '. Description: ' . ($description != '' ? $description : 'N/A'), 2); // 2 = admin note
                $order->update_meta_data('payment_declined_by', $current_user->ID);
                $order->save();

                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content'));
            }
            exit;
        } else if ($_GET['action'] == 'generate_payment') {
            $cancel = $_POST['cancel'];
            if (isset($cancel) && $cancel == 1) {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment'));
                exit;
            }

            global $wpdb, $current_user;
            $id_document = $_POST['id_document'];
            $generate = $_POST['generate'];
            $save_changes = $_POST['save_changes'];
            $delete_quote = $_POST['delete_quote'];
            $generate_fee_registration = $_POST['generate_fee_registration'];
            $generate_fee_graduation = $_POST['generate_fee_graduation'];
            $date_payment = $_POST['date_payment'] ?? [];
            $amount_payment = $_POST['amount_payment'] ?? [];
            $table_students = $wpdb->prefix . 'students';
            $table_student_payments = $wpdb->prefix . 'student_payments';
            $table_student_payments_log = $wpdb->prefix . 'student_payments_log';
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id_document='{$id_document}' OR email='{$id_document}'");
            $payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id='{$student->id}' ORDER BY cuote ASC");

            if ($delete_quote) {
                $wpdb->delete($table_student_payments, ['id' => $delete_quote]);
                set_max_date_student($student->id);

                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&success_save_changes=true'));
                exit;
            }

            if ($generate_fee_registration) {
                try {
                    // Validate student and get required data
                    if (!$student || !$student->partner_id) {
                        throw new Exception('Invalid student data');
                    }

                    $product_id = get_fee_product_id($student->id, 'registration');
                    if (!$product_id) {
                        return;
                    }
                    $customer_id = $student->partner_id;
                    $institute_id = $student->institute_id;

                    // Get first order for customer
                    $orders_customer = wc_get_orders([
                        'customer_id' => $customer_id,
                        'limit' => 1,
                        'orderby' => 'date',
                        'order' => 'ASC'
                    ]);

                    // Create new order with basic data
                    $new_order = wc_create_order([
                        'customer_id' => $customer_id,
                        'status' => 'pending-payment'
                    ]);

                    // Add product to order
                    $product = wc_get_product($product_id);
                    if (!$product) {
                        throw new Exception('Invalid product');
                    }
                    $new_order->add_product($product, 1);

                    if (empty($orders_customer)) {
                        $user_customer = get_user_by('id', $customer_id);

                        $new_order->add_meta_data('student_id', $student->id);
                        $new_order->update_meta_data('_order_origin', 'Fee graduation - Admin');

                        $new_order->set_billing_first_name($user_customer->first_name);
                        $new_order->set_billing_last_name($user_customer->last_name);
                        $new_order->set_billing_address_1($user_customer->address_1);
                        $new_order->set_billing_address_2($user_customer->address_2);
                        $new_order->set_billing_city($user_customer->city);
                        $new_order->set_billing_state($user_customer->state);
                        $new_order->set_billing_postcode($user_customer->postcode);
                        $new_order->set_billing_country($user_customer->country);
                        $new_order->set_billing_email($user_customer->email);
                        $new_order->set_billing_phone($user_customer->phone);
                    } else {
                        $order_old = $orders_customer[0];

                        $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
                        $new_order->add_meta_data('institute_id', $institute_id);
                        $new_order->add_meta_data('student_id', $student->id);

                        // Add additional metadata
                        $new_order->add_meta_data('old_order_primary', $order_old->get_id());
                        $new_order->add_meta_data('cuote_payment', 1);
                        $new_order->update_meta_data('_order_origin', 'Fee registration - Admin');

                        // Copy billing address if exists
                        if ($billing_address = $order_old->get_address('billing')) {
                            $new_order->set_address($billing_address, 'billing');
                        }

                    }

                    // Calculate totals and save order
                    $new_order->calculate_totals();
                    $new_order->save();

                    // Set institute in order
                    if ($order_old)
                        set_institute_in_order($new_order, $institute_id);


                    // Send notification email
                    $user_customer = get_user_by('id', $customer_id);
                    if ($user_customer) {
                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger(
                            $user_customer,
                            'You have pending payments',
                            'We invite you to log in to our platform as soon as possible so you can see your pending payments.'
                        );
                    }

                    // Redirect with success message
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&success_save_changes=true'));
                    exit;

                } catch (Exception $e) {
                    // Log error and redirect with error message
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&error=' . urlencode($e->getMessage())));
                    exit;
                }
            }

            if ($generate_fee_graduation) {
                try {
                    // Validate student and get required data
                    if (!$student || !$student->partner_id) {
                        throw new Exception('Invalid student data');
                    }

                    $product_id = get_fee_product_id($student->id, 'graduation');
                    if (!$product_id) {
                        return;
                    }
                    $customer_id = $student->partner_id;
                    $institute_id = $student->institute_id;

                    // Get first order for customer
                    $orders_customer = wc_get_orders([
                        'customer_id' => $customer_id,
                        'limit' => 1,
                        'orderby' => 'date',
                        'order' => 'ASC'
                    ]);

                    // Create new order with basic data
                    $new_order = wc_create_order([
                        'customer_id' => $customer_id,
                        'status' => 'pending-payment'
                    ]);

                    // Add product to order
                    $product = wc_get_product($product_id);
                    if (!$product) {
                        throw new Exception('Invalid product');
                    }
                    $new_order->add_product($product, 1);

                    if (empty($orders_customer)) {
                        $user_customer = get_user_by('id', $customer_id);

                        $new_order->add_meta_data('student_id', $student->id);
                        $new_order->update_meta_data('_order_origin', 'Fee graduation - Admin');

                        $new_order->set_billing_first_name($user_customer->first_name);
                        $new_order->set_billing_last_name($user_customer->last_name);
                        $new_order->set_billing_address_1($user_customer->address_1);
                        $new_order->set_billing_address_2($user_customer->address_2);
                        $new_order->set_billing_city($user_customer->city);
                        $new_order->set_billing_state($user_customer->state);
                        $new_order->set_billing_postcode($user_customer->postcode);
                        $new_order->set_billing_country($user_customer->country);
                        $new_order->set_billing_email($user_customer->email);
                        $new_order->set_billing_phone($user_customer->phone);
                    } else {
                        $order_old = $orders_customer[0];

                        $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
                        $new_order->add_meta_data('institute_id', $institute_id);
                        $new_order->add_meta_data('student_id', $student->id);

                        // Add additional metadata
                        $new_order->add_meta_data('old_order_primary', $order_old->get_id());
                        $new_order->add_meta_data('cuote_payment', 1);
                        $new_order->update_meta_data('_order_origin', 'Fee graduation - Admin');

                        // Copy billing address if exists
                        if ($billing_address = $order_old->get_address('billing')) {
                            $new_order->set_address($billing_address, 'billing');
                        }
                    }

                    // Calculate totals and save order
                    $new_order->calculate_totals();
                    $new_order->save();

                    // Set institute in order
                    if ($order_old)
                        set_institute_in_order($new_order, $institute_id);

                    // Send notification email
                    $user_customer = get_user_by('id', $customer_id);
                    if ($user_customer) {
                        $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                        $email_user->trigger(
                            $user_customer,
                            'You have pending payments',
                            'We invite you to log in to our platform as soon as possible so you can see your pending payments.'
                        );
                    }

                    // Redirect with success message
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&success_save_changes=true'));
                    exit;

                } catch (Exception $e) {
                    // Log error and redirect with error message
                    wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&error=' . urlencode($e->getMessage())));
                    exit;
                }
            }

            if ($save_changes) {
                $old_amount = 0;
                $new_amount = 0;
                foreach ($payments as $key => $payment) {
                    $wpdb->update($table_student_payments, ['date_next_payment' => $date_payment[$key], 'amount' => $amount_payment[$key]], ['id' => $payment->id]);
                    $old_amount += $payment->amount;
                    $new_amount += $amount_payment[$key];
                }

                $wpdb->insert($table_student_payments_log, [
                    'student_id' => $student->id,
                    'old_amount' => $old_amount,
                    'new_amount' => $new_amount,
                    'difference' => $new_amount - $old_amount,
                    'user_id' => $current_user->ID,
                    'description' => $_POST['description_payment_log']
                ]);

                set_max_date_student($student->id);
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document . '&success_save_changes=true'));
                exit;
            }

            if ($generate) {
                $amount = $_POST['amount'];
                $product_id = $_POST['product_id'];
                $customer_id = $student->partner_id;

                $orders_customer = wc_get_orders(array(
                    'customer_id' => $customer_id,
                    'limit' => 1,
                    'orderby' => 'date',
                    'order' => 'ASC' // Para obtener la primera orden
                ));
                $order_old = $orders_customer[0];
                $order_id = $order_old->get_id();
                $old_order_items = $order_old->get_items();
                $first_item = reset($old_order_items);

                $order_args = array(
                    'customer_id' => $customer_id,
                    'status' => 'pending-payment',
                );

                $new_order = wc_create_order($order_args);
                $new_order->add_meta_data('old_order_primary', $order_id);
                $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
                $new_order->add_meta_data('institute_id', $institute_id);
                $new_order->add_meta_data('student_id', $order_old->get_meta('student_id'));
                $new_order->add_meta_data('cuote_payment', 1);
                $new_order->update_meta_data('_order_origin', 'Cuote pending - Admin');
                $product = $first_item->get_product();
                $product->set_price($amount);
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

                set_institute_in_order($new_order, $institute_id);

                // hacemos el envio del email al email del customer, es decir, al que paga.
                $user_customer = get_user_by('id', $customer_id);
                $email_user = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                $email_user->trigger($user_customer, 'You have pending payments', 'We invite you to log in to our platform as soon as possible so you can see your pending payments.');

                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&success_advance_payment=true'));
                exit;
            }

            if ($student) {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=' . $id_document));
            } else {
                wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=0&id_document=' . $id_document));
            }
        } else if ($_GET['action'] == 'generate_order') {
            global $wpdb;
            $amount_order = $_POST['amount_order'];
            $date_order = $_POST['date_order'];
            $order_id = $_POST['order_id_old'];
            $name = get_user_meta($current_user->ID, 'first_name', true) . ' ' . get_user_meta($current_user->ID, 'last_name', true);

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

            $order_old->update_status('pending-payment');
            $order_old->update_meta_data('pending_payment', $amount_order);
            // $order_old->set_date_created($date_order);

            if ($order_old->get_meta('dates_next_orders')) {
                $dates = json_decode($order_old->get_meta('dates_next_orders'));
                array_push($dates, [
                    'id' => (count($dates) + 1),
                    'date' => $date_order,
                    'pending_payment' => $amount_order
                ]);
                $order_old->update_meta_data('dates_next_orders', json_encode($dates));
            } else {
                $dates = [
                    [
                        'id' => 1,
                        'date' => $date_order,
                        'pending_payment' => $amount_order
                    ]
                ];
                $order_old->add_meta_data('dates_next_orders', json_encode($dates));
            }

            $split_method = $order_old->get_meta('split_method');
            $split_method = json_decode($split_method);
            $index = array_search('on-hold', array_column($split_method, 'status'));
            if ($index !== false) {
                $split_method[$index]->status = 'completed';
                $order_old->update_meta_data('split_method', json_encode($split_method));
            }

            $order_old->add_order_note('Payment verified by ' . $name . '. Description: N/A', 2); // 2 = admin note

            $split_method_updated = $order_old->get_meta('split_method');
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

                $total_paid_meta = $order_old->get_meta('total_paid');
                if ($total_paid_meta) {
                    $order_old->update_meta_data('total_paid', $total);
                } else {
                    $order_old->add_meta_data('total_paid', $total);
                }

                $total_paid_meta = $order_old->get_meta('total_paid_gross');
                if ($total_paid_meta) {
                    $order_old->update_meta_data('total_paid_gross', $total_gross);
                } else {
                    $order_old->add_meta_data('total_paid_gross', $total_gross);
                }

                // $pending_payment_meta = $order_old->get_meta('pending_payment');
                // if ($pending_payment_meta) {
                //     $order_old->update_meta_data('pending_payment', (($order_old->get_subtotal() - $order_old->get_total_discount()) - $total));
                // } else {
                //     $order_old->add_meta_data('pending_payment', (($order_old->get_subtotal() - $order_old->get_total_discount()) - $total));
                // }

                // $order_old->update_status('completed');
            }

            $order_old->save();

            // Obtener el primer item de la orden vieja
            // $old_order_items = $order_old->get_items();
            // $first_item = reset($old_order_items);
            // $customer_id = $order_old->get_customer_id();

            // $order_args = array(
            //     'customer_id' => $customer_id,
            //     'status' => 'pending-payment',
            // );

            // $new_order = wc_create_order($order_args);
            // $new_order->add_meta_data('alliance_id', $order_old->get_meta('alliance_id'));
            // $new_order->add_meta_data('old_order_primary', $order_id);
            // $new_order->add_meta_data('institute_id', $order_old->get_meta('institute_id'));
            // $new_order->add_meta_data('is_vat_exempt', $order_old->get_meta('is_vat_exempt'));
            // $new_order->add_meta_data('pending_payment', $order_old->get_meta('pending_payment'));
            // $new_order->add_meta_data('student_id', $order_old->get_meta('student_id'));
            // $new_order->set_date_created($date_order);
            // $product = $first_item->get_product();
            // $product->set_price($amount_order);
            // $new_order->add_product($product, $first_item->get_quantity());
            // $new_order->calculate_totals();
            // if ($order_old->get_address('billing')) {
            //     $billing_address = $order_old->get_address('billing');
            //     $new_order->set_billing_first_name($billing_address['first_name']);
            //     $new_order->set_billing_last_name($billing_address['last_name']);
            //     $new_order->set_billing_company($billing_address['company']);
            //     $new_order->set_billing_address_1($billing_address['address_1']);
            //     $new_order->set_billing_address_2($billing_address['address_2']);
            //     $new_order->set_billing_city($billing_address['city']);
            //     $new_order->set_billing_state($billing_address['state']);
            //     $new_order->set_billing_postcode($billing_address['postcode']);
            //     $new_order->set_billing_country($billing_address['country']);
            //     $new_order->set_billing_email($billing_address['email']);
            //     $new_order->set_billing_phone($billing_address['phone']);
            // }
            // $new_order->save();

        } else if ($_GET['action'] == 'save_expense_details') {
            global $wpdb;
            $table_expenses = $wpdb->prefix . 'expenses';

            $expense_id = sanitize_text_field($_POST['expense_id']);
            $motive = sanitize_text_field($_POST['motive']);
            $apply_to = sanitize_text_field($_POST['apply_to']);
            $amount = sanitize_text_field($_POST['amount']);

            if (isset($expense_id) && !empty($expense_id)) {
                $wpdb->update($table_expenses, [
                    'motive' => $motive,
                    'apply_to' => $apply_to,
                    'amount' => $amount,
                ], ['id' => $expense_id]);
            } else {
                $wpdb->insert($table_expenses, [
                    'motive' => $motive,
                    'apply_to' => $apply_to,
                    'amount' => $amount,
                ]);
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=expenses_payroll'));
            exit;
        } else if ($_GET['action'] == 'delete_expense') {
            global $wpdb;
            $table_expenses = $wpdb->prefix . 'expenses';

            $id_expense = intval($_GET['id_expense']);
            $wpdb->delete($table_expenses, ['id' => $id_expense]);

            setcookie('message', __('Expense deleted successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_content&section_tab=expenses_payroll'));
            exit;
        } else if ($_GET['action'] == 'save_program_details') {
            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';

            // Sanitizar valores
            $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
            $program_product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $name = strtoupper(sanitize_text_field(stripslashes($_POST['name'])));
            $description = strtoupper(sanitize_text_field(stripslashes($_POST['description'])));
            $total_price = floatval(sanitize_text_field($_POST['total_price']));
            $is_active = $_POST['is_active'] ? true : false;
            $subprograms_post = $_POST['subprogram'] ?? '';

            $subprograms = [];// array para guardas los subprogramas

            // verifica y crea en caso de necesitar una categoria llamada programs;"
            $category_id = 0;
            $name_category = 'programs';
            $category = term_exists($name_category, 'product_cat');
            if ($category) {
                $category_id = (int) $category['term_id'];

            } else {
                // La categoría no existe, crearla
                $category = wp_insert_term($name_category, 'product_cat');
                if (!is_wp_error($category)) {
                    $category_id = (int) $category['term_id'];// Devolver el ID de la nueva categoría creada
                }
            }

            //crea o actualiza el producto
            if (!empty($program_id)) {

                wp_update_post(array(
                    'ID' => $program_product_id,
                    'post_title' => $name,
                    'post_content' => $description,
                ), true);

                update_post_meta($program_product_id, '_regular_price', $total_price);
                update_post_meta($program_product_id, '_price', $total_price);

                // guarda el stock en caso de que este activo o no
                update_post_meta($program_product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');

                // Asignar la categoría al producto
                wp_set_object_terms($program_product_id, $category_id, 'product_cat');

            } else {

                // Función para crear un producto
                $program_product_id = wp_insert_post([
                    'post_title' => $name,
                    'post_content' => $description, // Descripción del producto
                    'post_status' => 'publish',
                    'post_type' => 'product',
                ]);

                // Verificar si el producto se creó correctamente
                if (!is_wp_error($program_product_id)) {

                    update_post_meta($program_product_id, '_sku', $identificator);
                    update_post_meta($program_product_id, '_regular_price', $total_price);
                    update_post_meta($program_product_id, '_price', $total_price);

                    // guarda el stock en caso de que este activo o no
                    update_post_meta($program_product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');

                    // Asignar la categoría al producto
                    wp_set_object_terms($program_product_id, (int) $category_id, 'product_cat');
                }
            }

            // obtiene los subprogramas y crea  los productos 
            // vinculados a ellos si los 
            $product = wc_get_product((int) $program_product_id);
            if ($product && !empty($subprograms_post)) {

                $attribute_name = 'subprograms';

                if (!$product->is_type('variable')) {

                    // Establecer como producto variable
                    wp_set_object_terms($program_product_id, 'variable', 'product_type');

                    // Crear atributo "subprograma" vacío
                    $product_attributes = array(
                        $attribute_name => array(
                            'name' => __('Subprograms', 'edusystem'),
                            'value' => '',
                            'is_visible' => true,
                            'is_variation' => true,
                            'is_taxonomy' => false
                        )
                    );

                    update_post_meta($program_product_id, '_product_attributes', $product_attributes);
                }

                foreach ($subprograms_post as $subprogram) {

                    $name_subprogram = $subprogram['name'];
                    $price = $subprogram['price'];
                    $is_active_subprogram = $subprogram['is_active'] ? true : false;

                    // crea o actualiza el producto 
                    if ($subprogram['product_id']) {

                        $product_id = $subprogram['product_id'];

                        wp_update_post(array(
                            'ID' => $product_id,
                            'post_title' => $name_subprogram,
                        ), true);

                        update_post_meta($product_id, '_regular_price', $price);
                        update_post_meta($product_id, '_price', $price);

                        // guarda el stock en caso de que este activo o no
                        update_post_meta($product_id, '_stock_status', ($is_active && $is_active_subprogram) ? 'instock' : 'outofstock');

                        wp_set_object_terms($product_id, (int) $category_id, 'product_cat');

                    } else {

                        // Función para crear un producto
                        $product_id = wp_insert_post([
                            'post_title' => $name_subprogram,
                            'post_name' => $name_subprogram,
                            'post_status' => 'publish',
                            'post_type' => 'product_variation',
                            'post_parent' => $program_product_id,
                        ]);

                        // Verificar si el producto se creó correctamente
                        if (!is_wp_error($product_id)) {

                            update_post_meta($product_id, '_regular_price', $price);
                            update_post_meta($product_id, '_price', $price);
                            update_post_meta($product_id, '_stock_status', 'instock'); // Estado del stock
                            update_post_meta($product_id, 'attribute_' . $attribute_name, sanitize_title($name_subprogram));

                            wp_set_object_terms($product_id, (int) $category_id, 'product_cat');

                            // Añadir el término al atributo "subprograms"
                            wp_set_object_terms($program_product_id, $name_subprogram, $attribute_name, true);

                            // Actualizar el valor del atributo "subprogramas"
                            $current_values = get_post_meta($program_product_id, '_product_attributes', true);
                            if (isset($current_values[$attribute_name])) {
                                $current_values[$attribute_name]['value'] .= (empty($current_values[$attribute_name]['value']) ? '' : '| ') . $name_subprogram;
                            }
                            update_post_meta($program_product_id, '_product_attributes', $current_values);
                        }

                    }

                    // crea el array con los datos del subprograma
                    $subprogram_data = [
                        'is_active' => $is_active_subprogram ? 1 : 0,
                        'name' => $name_subprogram,
                        'price' => $price,
                        'product_id' => (string) $product_id ?? null,
                    ];

                    // actualiza en caso de que ya exista o anade un subprograma nuevo
                    if ($subprogram['id']) {
                        $subprograms[$subprogram['id']] = $subprogram_data;
                    } else {
                        $subprograms[(array_key_last($subprograms) ?? 0) + 1] = $subprogram_data;
                        update_post_meta($product_id, '_sku', $identificator . "-" . (array_key_last($subprograms) + 1));
                    }
                }
            }

            // crea o actualiza el sub programa
            if (!empty($program_id)) {

                $wpdb->update($table_programs, [
                    'identificator' => $identificator,
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active,
                    'subprogram' => json_encode($subprograms) ?? null,
                ], ['id' => $program_id]);

            } else {

                if (!empty($subprograms)) {
                    //pone indices a los subprogramas que serviran como ids
                    $index = range(1, count($subprograms));
                    $subprograms = array_combine($index, $subprograms);

                    $subprogram = json_encode($subprograms);
                } else {
                    $subprogram = null;
                }

                $wpdb->insert($table_programs, [
                    'identificator' => $identificator,
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active,
                    'product_id' => $program_product_id,
                    'subprogram' => $subprogram,
                ]);

                $program_id = $wpdb->insert_id;
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details&program_id=' . $program_id));
            exit;

        } else if ($_GET['action'] == 'delete_payment_method') {
            global $wpdb;
            $payment_methods_by_plan = $wpdb->prefix . 'payment_methods_by_plan';
            $program_id = intval($_GET['program_id']);
            $method_id = intval($_GET['method_id']);
            $wpdb->delete($payment_methods_by_plan, ['id' => $method_id]);
            
            setcookie('message', __('Payment method deleted successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details&program_id=' . $program_id));
            exit;
        } else if ($_GET['action'] == 'save_quotas_rules') {

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';

            // Sanitizar 
            $program_id = $_POST['program_id'] ?? '';
            $identificator = isset($_POST['identificator']) ? sanitize_text_field($_POST['identificator']) : '';

            if (!empty($identificator)) {
                $rules_post = $_POST['rules'] ?? '';

                foreach ($rules_post as $rule) {

                    $rule_id = $rule['id'] ?? '';
                    $is_active = $rule['is_active'] ? true : false;
                    $name = $rule['name'];
                    $initial_payment = $rule['initial_payment'];
                    $initial_payment_sale = $rule['initial_payment_sale'] ?? null;
                    $final_payment = $rule['final_payment'];
                    $final_payment_sale = $rule['final_payment_sale'] ?? null;
                    $quote_price = $rule['quote_price'];
                    $quote_price_sale = $rule['quote_price_sale'] ?? null;
                    $quotas_quantity = (int) $rule['quotas_quantity'];
                    $frequency_value = $rule['frequency_value'];
                    $type_frequency = $rule['type_frequency'];
                    $start_charging = $rule['start_charging'];
                    $position = $rule['position'] ?? 0;

                    // si los valores de descuento son vacios los convierte a null
                    $initial_payment_sale = ($initial_payment_sale == "") ? null : $initial_payment_sale;
                    $final_payment_sale = ($final_payment_sale == "") ? null : $final_payment_sale;
                    $quote_price_sale = ($quote_price_sale == "") ? null : $quote_price_sale;

                    // crea o actualiza el sub programa
                    if (!empty($rule_id)) {

                        $wpdb->update($table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_payment' => $initial_payment,
                            'initial_payment_sale' => $initial_payment_sale,
                            'final_payment' => $final_payment,
                            'final_payment_sale' => $final_payment_sale,
                            'quote_price' => $quote_price,
                            'quote_price_sale' => $quote_price_sale,
                            'quotas_quantity' => $quotas_quantity,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'start_charging' => $start_charging,
                            'position' => $position,
                        ], ['id' => $rule_id]);

                    } else {

                        $wpdb->insert($table_quota_rules, [
                            'is_active' => $is_active,
                            'program_id' => $identificator,
                            'name' => $name,
                            'initial_payment' => $initial_payment,
                            'initial_payment_sale' => $initial_payment_sale,
                            'final_payment' => $final_payment,
                            'final_payment_sale' => $final_payment_sale,
                            'quote_price' => $quote_price,
                            'quote_price_sale' => $quote_price_sale,
                            'quotas_quantity' => $quotas_quantity,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'start_charging' => $start_charging,
                            'position' => $position,
                        ]);
                    }
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect($_SERVER['HTTP_REFERER']);

            } else {
                setcookie('message-error', __('Identifier not found', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url("admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details"));
            }

            exit;

        } else if ($_GET['action'] == 'delete_quota_rule') {

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';

            $rule_id = $_POST['quota_rule_id'];

            $deleted = $wpdb->delete(
                $table_quota_rules,
                ['id' => $rule_id],
                ['%d']
            );

            if ($deleted) {
                setcookie('message', __('The quota rule has been deleted successfully.', 'edusystem'), time() + 10, '/');
            } else {
                setcookie('message-error', __('The quota rule has not been deleted correctly.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'delete_subprogram') {

            $subprogram_id = $_POST['subprogram_id'];

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s",
                $subprogram_id
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ($students == 0) {

                $separacion = strpos($subprogram_id, '_');
                if ($separacion !== false) {
                    $program_id = substr($subprogram_id, 0, $separacion);
                    $subprogram_indice = substr($subprogram_id, $separacion + 1);
                }

                $subprogram_data = get_subprogram_by_identificador_program($program_id);

                // obtiene el id del producto a eliminar
                $product_id = $subprogram_data[$subprogram_indice]['product_id'];

                // elimina el producto
                wp_delete_post($product_id, true);

                // elemina el subprograma
                unset($subprogram_data[$subprogram_indice]);

                //guardar la subprogramas
                $table_programs = $wpdb->prefix . 'programs';
                $update = $wpdb->update($table_programs, [
                    'subprogram' => json_encode($subprogram_data) ?? null,
                ], ['identificator' => $program_id]);

                if ($update) {
                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'delete_program') {

            $program_id = $_POST['program_id'];

            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';
            $table_quotas_rules = $wpdb->prefix . 'quota_rules';
            $table_students = $wpdb->prefix . 'students';

            $program_data = $wpdb->get_row($wpdb->prepare(
                "SELECT identificator, product_id FROM $table_programs WHERE id = %d ",
                $program_id,
            ));

            $students = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s || program_id LIKE %s",
                $program_data->identificator,
                $program_data->identificator . '_%'
            ));
            // Si no hay registros en table_y, proceder a eliminar
            if ($students == 0) {

                $product = wc_get_product($program_data->product_id);
                if ($product)
                    $product->delete(true);

                $deleted = $wpdb->delete(
                    $table_programs,
                    ['id' => $program_id],
                    ['%d']
                );


                if ($deleted) {

                    // eliminar las reglas de los quotas
                    $wpdb->query($wpdb->prepare(
                        "DELETE FROM $table_quotas_rules WHERE program_id = %s OR program_id LIKE %s",
                        $program_data->identificator,
                        $program_data->identificator . '_%'
                    ));

                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'save_fee') {


            global $wpdb;
            $table_admission_fees = $wpdb->prefix . 'admission_fees';

            // Sanitizar valores
            $fee_id = isset($_POST['fee_id']) ? (int) sanitize_text_field($_POST['fee_id']) : '';
            $product_id = isset($_POST['product_id']) ? (int) sanitize_text_field($_POST['product_id']) : '';

            $is_active = $_POST['is_active'] ? true : false;
            $name = sanitize_text_field($_POST['name']);
            $price = floatval(sanitize_text_field($_POST['price']));
            $description = sanitize_text_field($_POST['description']);
            $programs = $_POST['programs'] ?? [];
            $type_fee = $_POST['type_fee'];

            // verifica y crea en caso de necesitar una categoria llamada fees
            $category_id = 0;
            $name_category = 'fees';
            $category = term_exists($name_category, 'product_cat');
            if ($category) {
                $category_id = (int) $category['term_id'];

            } else {
                // La categoría no existe, crearla
                $category = wp_insert_term($name_category, 'product_cat');
                if (!is_wp_error($category))
                    $category_id = (int) $category['term_id'];// Devolver el ID de la nueva categoría creada

            }

            //crea o actualiza el producto
            if (!empty($fee_id) && !empty($product_id)) {

                wp_update_post(array(
                    'ID' => $product_id,
                    'post_title' => $name,
                    'post_content' => $description,
                ), true);

                update_post_meta($product_id, '_regular_price', $price);
                update_post_meta($product_id, '_price', $price);

                // guarda el stock en caso de que este activo o no
                update_post_meta($product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');

                // Asignar la categoría al producto
                wp_set_object_terms($product_id, $category_id, 'product_cat');

            } else {

                // Función para crear un producto
                $product_id = wp_insert_post([
                    'post_title' => $name,
                    'post_content' => $description, // Descripción del producto
                    'post_status' => 'publish',
                    'post_type' => 'product',
                ]);

                // Verificar si el producto se creó correctamente
                if (!is_wp_error($product_id)) {

                    update_post_meta($product_id, '_regular_price', $price);
                    update_post_meta($product_id, '_price', $price);

                    // guarda el stock en caso de que este activo o no
                    update_post_meta($product_id, '_stock_status', $is_active ? 'instock' : 'outofstock');

                    // Asignar la categoría al producto
                    wp_set_object_terms($product_id, (int) $category_id, 'product_cat');
                }
            }

            // crea o actualiza el sub programa
            if (!empty($fee_id)) {

                $wpdb->update($table_admission_fees, [
                    'is_active' => $is_active,
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'programs' => json_encode($programs),
                    'type_fee' => $type_fee,
                ], ['id' => $fee_id]);

            } else {

                $wpdb->insert($table_admission_fees, [
                    'is_active' => $is_active,
                    'name' => $name,
                    'price' => $price,
                    'product_id' => $product_id,
                    'description' => $description,
                    'programs' => json_encode($programs),
                    'type_fee' => $type_fee,
                ]);

                $fee_id = $wpdb->insert_id;
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=fees_content&section_tab=fee_details&fee_id=' . $fee_id));
            exit;

        } else if ($_GET['action'] == 'delete_fee') {

            $fee_id = (int) $_POST['fee_id'] ?? null;
            $product_id = (int) $_POST['product_id'] ?? null;

            if (!empty($fee_id) && !empty($product_id)) {

                global $wpdb;
                $deleted = $wpdb->delete(
                    "{$wpdb->prefix}admission_fees",
                    ['id' => $fee_id],
                    ['%d']
                );

                if ($deleted) {

                    $product = wc_get_product($product_id);
                    if ($product)
                        $product->delete(true);

                    setcookie('message', __('The fee has been deleted successfully.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The fee has not been deleted correctly.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'update_price_items_order') {

            // Obtener datos POST
            $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
            $items = $_POST['items'] ?? [];

            $order = wc_get_order($order_id);
            if ($order) {
                foreach ($order->get_items() as $item_id => $order_item) {

                    if (isset($items[$item_id])) {
                        $order_item->set_subtotal((float) $items[$item_id]['amount'] ?? $order_item->get_total());
                        $order_item->set_total((float) $items[$item_id]['amount'] ?? $order_item->get_total());
                        $order_item->save();
                    }
                }

                $order->calculate_totals();
                $order->save();

                setcookie('message', __('Items updated successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect("admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id={$order_id}");

            } else {
                setcookie('message-error', __('Items could not be updated.', 'edusystem'), time() + 10, '/');
                wp_redirect($_SERVER['HTTP_REFERER']);
            }
            exit;

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
            $student = get_student_detail($order->get_meta('student_id'));

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
            $table_students = $wpdb->prefix . 'students';
            $table_student_payments = $wpdb->prefix . 'student_payments';
            $table_student_payments_log = $wpdb->prefix . 'student_payments_log';
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id_document='{$id_document}' OR email='{$id_document}'");
            $order_amount = 0;
            $order_variation_id = 0;
            $order_product_id = 0;
            $url = '';
            if ($student) {
                $payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id='{$student->id}' ORDER BY cuote ASC");
                $payments_log = $wpdb->get_results("SELECT * FROM {$table_student_payments_log} WHERE student_id={$student->id} ORDER BY created_at DESC");
                foreach ($payments as $key => $payment) {
                    if ($payment->status_id == 0) {
                        $order_amount = $payment->amount;
                        $order_variation_id = $payment->variation_id;
                        $order_product_id = $payment->product_id;
                        break;
                    }
                }
                $url = wp_get_attachment_url($student->profile_picture);
            }
            include(plugin_dir_path(__FILE__) . 'templates/generate-advance-payment.php');
        } else if ($_GET['section_tab'] == 'expenses_payroll') {

            $list_payments = new TT_Expenses_Payroll_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-expenses-payroll.php');
        } else if ($_GET['section_tab'] == 'add_expenses_payroll') {
            $id_expense = intval($_GET['id_expense']);
            $expense = get_expense_detail($id_expense);
            include(plugin_dir_path(__FILE__) . 'templates/expenses-payroll-detail.php');
        } else if ($_GET['section_tab'] == 'program_details') {
            global $wpdb;
            $final_payment_methods = array();

            $program_id = $_GET['program_id'];
            $program = get_program_details($program_id);
            $raw_payment_methods = get_program_payment_method_details($program->identificator);

            if (function_exists('WC') && WC()->payment_gateways) {
                $available_gateways = WC()->payment_gateways->payment_gateways();
            } else {
                $available_gateways = array();
            }

            foreach ($raw_payment_methods as $key => $method) {
                $payment_id_woocommerce = $method->payment_method_identificator;
                $processed_method = (array) $method;
                if (isset($available_gateways[$payment_id_woocommerce])) {
                    $gateway_object = $available_gateways[$payment_id_woocommerce];

                    // Add WooCommerce gateway data to the array
                    $processed_method['woocommerce_title'] = $gateway_object->get_title();
                    $processed_method['woocommerce_description'] = $gateway_object->get_description();
                    $processed_method['woocommerce_is_enabled'] = $gateway_object->is_available();
                } else {
                    $processed_method['woocommerce_title'] = 'Gateway Not Found';
                    $processed_method['woocommerce_description'] = '';
                    $processed_method['woocommerce_is_enabled'] = false;
                }
                $final_payment_methods[] = $processed_method;
            }

            include(plugin_dir_path(__FILE__) . 'templates/payment-plans-details.php');
        } else if ($_GET['section_tab'] == 'quotas_rules_programs') {
            global $wpdb;
            $program_id = $_GET['program_id'];
            $identificator = $_GET['identificator'];
            $rules = get_quotas_rules_programs($identificator);
            include(plugin_dir_path(__FILE__) . 'templates/quotas-rules-payment-plans.php');
        } else if ($_GET['section_tab'] == 'fee_details') {
            global $wpdb;
            $fee_id = $_GET['fee_id'];
            $fee = get_admission_fee($fee_id);
            include(plugin_dir_path(__FILE__) . 'templates/fee-details.php');
        }

    } else {
        if ($_GET['page'] == 'add_admin_form_payments_content') {
            $list_payments = new TT_payment_pending_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
        } else if ($_GET['page'] == 'add_admin_form_payments_plans_content') {
            $list_payments = new TT_All_Payment_Plans_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
            include(plugin_dir_path(__FILE__) . '/templates/modal-delete-program.php');

        } else if ($_GET['page'] == 'fees_content') {

            $list_payments = new TT_All_Fees_List_Table;
            $list_payments->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-payments.php');
            include(plugin_dir_path(__FILE__) . '/templates/modal-delete-admission-fee.php');

        }
    }
}

function update_order_pending_approved($order, $payment_selected, $transaction_id, $other_payments)
{
    $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
    $filteredArray = [];
    if ($payment_gateways) {
        $filteredArray = array_filter($payment_gateways, function ($item) use ($payment_selected) {
            return $item->id == $payment_selected;
        });
        $filteredArray = array_values($filteredArray);
    }

    $order->set_payment_method($payment_selected);
    $order->update_meta_data('transaction_id', $transaction_id);
    $order->set_payment_method_title($filteredArray[0]->get_title());

    if ($payment_selected == 'other_payment') {
        $order->update_meta_data('payment_method', $other_payments);
    }

    $order->save();
}

function success_advance_payment()
{
    if (isset($_GET['success_advance_payment']) && $_GET['success_advance_payment'] == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Payment generated successfully</p>
        </div>
        <?php
    }

    if (isset($_GET['success_save_changes']) && $_GET['success_save_changes'] == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>Changes saved successfully</p>
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
            case 'student_name':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'payment_id' => __('Payment ID', 'edusystem'),
            'date' => __('Date', 'edusystem'),
            'partner_name' => __('Parent', 'edusystem'),
            'student_name' => __('Student', 'edusystem'),
            'total' => __('Total', 'edusystem'),
            'payment_method' => __('Payment Method', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_payment_pendings()
    {
        global $current_user, $wpdb;
        $roles = (array) $current_user->roles; // Cast to array for safety
        $orders_array = [];

        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $search_term = sanitize_text_field($_POST['s'] ?? ''); // Get search term safely
        $table_students = $wpdb->prefix . 'students';

        // Initialize args for wc_get_orders
        $args = [
            'limit' => $per_page,
            'offset' => $offset,
            'status' => array('wc-pending', 'wc-processing', 'wc-on-hold', 'split-payment'), // Orders with these statuses
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $meta_query = [];

        // 1. Filter by 'from_webinar' meta key for specific roles
        if (in_array('webinar-aliance', $roles) || in_array('webinaraaliance', $roles)) {
            $meta_query[] = [
                'key' => 'from_webinar',
                'value' => 1,
                'compare' => '=',
                'type' => 'NUMERIC' // Ensure proper comparison for numeric value
            ];
        }

        // 2. Smart Search for Students and then filter orders by student_id
        if (!empty($search_term)) {
            $search_term_like = '%' . $wpdb->esc_like($search_term) . '%';

            $search_conditions = [];
            $search_params = [];

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
                $search_conditions[] = "{$field_combination} LIKE %s";
                $search_params[] = $search_term_like;
            }

            // Direct search in individual fields
            $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
            foreach ($individual_fields as $field) {
                $search_conditions[] = "{$field} LIKE %s";
                $search_params[] = $search_term_like;
            }

            $student_query_sql = "SELECT id FROM {$table_students} WHERE " . implode(" OR ", $search_conditions);
            $student_ids = $wpdb->get_col($wpdb->prepare($student_query_sql, $search_params));

            if (!empty($student_ids)) {
                $meta_query[] = [
                    'key' => 'student_id',
                    'value' => array_map('intval', $student_ids), // Ensure IDs are integers
                    'compare' => 'IN',
                    'type' => 'NUMERIC' // Crucial for correct numeric comparison
                ];
            } else {
                // If no students match the search, return no orders to prevent fetching all.
                return ['data' => [], 'total_count' => 0];
            }
        }

        // Add meta_query to the main args if not empty
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
            // If there's more than one meta_query, ensure relation is set if needed (default is AND)
            // $args['meta_query']['relation'] = 'AND';
        }

        // Get orders with pagination applied
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                $student = get_student_detail($order->get_meta('student_id'));

                $student_full_name = '';
                if ($student) {
                    // Ensure properties exist before concatenating
                    $student_full_name = ($student->last_name ?? '') . ' ' .
                        ($student->middle_last_name ?? '') . ' ' .
                        ($student->name ?? '') . ' ' .
                        ($student->middle_name ?? '');
                }

                // Get billing first and last name from the order directly
                $billing_first_name = $order->get_billing_first_name();
                $billing_last_name = $order->get_billing_last_name();
                $partner_name = trim($billing_last_name . ' ' . $billing_first_name);

                $orders_array[] = [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created() ? $order->get_date_created()->format('F j, Y g:i a') : '',
                    'partner_name' => $partner_name,
                    'student_name' => '<span class="text-uppercase">' . $student_full_name . '</span>', // Apply uppercase here
                    'total' => wc_price($order->get_total()),
                    'status' => ($order->get_status() === 'pending') ? __('Payment pending', 'your-text-domain') : wc_get_order_status_name($order->get_status()), // Use wc_get_order_status_name for localized status
                    'payment_method' => $order->get_payment_method_title()
                ];
            }
        }

        // Get the total count of orders that match all filters *without* pagination limit/offset
        // wc_get_orders with 'return' => 'ids' and then count, or 'return' => 'objects' with limit -1
        // The most reliable way for total count when using WP_Query based functions is a separate call with 'limit' => -1
        $total_args = $args;
        unset($total_args['limit']);
        unset($total_args['offset']);
        $total_count = wc_get_orders(array_merge($total_args, ['return' => 'ids'])); // Get all matching IDs
        $total_orders_count = count($total_count); // Count the IDs

        return ['data' => $orders_array, 'total_count' => $total_orders_count];
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
            case 'student_name':
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'total':
                return '<b>' . $item[$column_name] . '</b>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=order_detail&order_id=' . $item['payment_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'payment_id' => __('Payment ID', 'edusystem'),
            'date' => __('Date', 'edusystem'),
            'partner_name' => __('Parent', 'edusystem'),
            'student_name' => __('Student', 'edusystem'),
            'total' => __('Total', 'edusystem'),
            'payment_method' => __('Payment Method', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_payment()
    {
        global $current_user, $wpdb;
        $roles = (array) $current_user->roles; // Cast to array for safety
        $orders_array = [];

        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $search_term = sanitize_text_field($_POST['s'] ?? ''); // Get search term safely
        $table_students = $wpdb->prefix . 'students';

        // Initialize args for wc_get_orders
        $args = [
            'limit' => $per_page,
            'offset' => $offset,
            'status' => array('wc-pending', 'wc-completed', 'wc-cancelled', 'wc-processing', 'wc-on-hold'), // All relevant statuses
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $meta_query = [];

        // 1. Filter by 'from_webinar' meta key for specific roles
        if (in_array('webinar-aliance', $roles) || in_array('webinaraaliance', $roles)) {
            $meta_query[] = [
                'key' => 'from_webinar',
                'value' => 1,
                'compare' => '=',
                'type' => 'NUMERIC' // Ensure proper comparison for numeric value
            ];
        }

        // 2. Smart Search for Students and then filter orders by student_id
        if (!empty($search_term)) {
            $search_term_like = '%' . $wpdb->esc_like($search_term) . '%';

            $search_conditions = [];
            $search_params = [];

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
                $search_conditions[] = "{$field_combination} LIKE %s";
                $search_params[] = $search_term_like;
            }

            // Direct search in individual fields
            $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
            foreach ($individual_fields as $field) {
                $search_conditions[] = "{$field} LIKE %s";
                $search_params[] = $search_term_like;
            }

            $student_query_sql = "SELECT id FROM {$table_students} WHERE " . implode(" OR ", $search_conditions);
            $student_ids = $wpdb->get_col($wpdb->prepare($student_query_sql, $search_params));

            if (!empty($student_ids)) {
                $meta_query[] = [
                    'key' => 'student_id',
                    'value' => array_map('intval', $student_ids), // Ensure IDs are integers
                    'compare' => 'IN',
                    'type' => 'NUMERIC' // Crucial for correct numeric comparison
                ];
            } else {
                // If no students match the search, return no orders to prevent fetching all.
                return ['data' => [], 'total_count' => 0];
            }
        }

        // Add meta_query to the main args if not empty
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
            // If there's more than one meta_query, ensure relation is set if needed (default is AND)
            // $args['meta_query']['relation'] = 'AND';
        }

        // Get orders with pagination applied
        $orders = wc_get_orders($args);

        if ($orders) {
            foreach ($orders as $order) {
                $student = get_student_detail($order->get_meta('student_id'));

                $student_full_name = '';
                if ($student) {
                    // Ensure properties exist before concatenating
                    $student_full_name = ($student->last_name ?? '') . ' ' .
                        ($student->middle_last_name ?? '') . ' ' .
                        ($student->name ?? '') . ' ' .
                        ($student->middle_name ?? '');
                }

                // Get billing first and last name from the order directly
                $billing_first_name = $order->get_billing_first_name();
                $billing_last_name = $order->get_billing_last_name();
                $partner_name = trim($billing_last_name . ' ' . $billing_first_name);

                $orders_array[] = [
                    'payment_id' => $order->get_id(),
                    'date' => $order->get_date_created() ? $order->get_date_created()->format('F j, Y g:i a') : '',
                    'partner_name' => $partner_name,
                    'student_name' => '<span class="text-uppercase">' . $student_full_name . '</span>', // Apply uppercase here
                    'total' => wc_price($order->get_total()),
                    'status' => wc_get_order_status_name($order->get_status()), // Use wc_get_order_status_name for localized status
                    'payment_method' => $order->get_payment_method_title()
                ];
            }
        }

        // Get the total count of orders that match all filters *without* pagination limit/offset
        // The most reliable way for total count when using WP_Query based functions is a separate call with 'limit' => -1
        $total_args = $args;
        unset($total_args['limit']);
        unset($total_args['offset']);
        $total_count = wc_get_orders(array_merge($total_args, ['return' => 'ids'])); // Get all matching IDs
        $total_orders_count = count($total_count); // Count the IDs

        return ['data' => $orders_array, 'total_count' => $total_orders_count];
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
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_alliances&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'edusystem') . "</a>";
                } else {
                    return "Paid";
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
            'status_id' => __('Status', 'edusystem'),
            'alliance_id' => __('Alliance', 'edusystem'),
            'month' => __('Month', 'edusystem'),
            'amount' => __('Amount', 'edusystem'),
            'total_orders' => __('Total Orders', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_invoices_alliances()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        $only_pending = $_POST['only_pending'] ?? false;
        $only_pending_query = '';
        if ($only_pending) {
            $only_pending_query = 'WHERE status_id = 0';
        }

        $table_alliances_payments = $wpdb->prefix . 'alliances_payments';
        $transactions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_alliances_payments} {$only_pending_query} LIMIT {$per_page} OFFSET {$offset}");
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

class TT_Expenses_Payroll_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'expense',
            'plural' => 'expenses',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {
        global $current_user;
        switch ($column_name) {
            case 'amount':
                return wc_price($item['amount']);
            case 'view_details':
                $buttons = '';
                $buttons .= "<a style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=add_expenses_payroll&id_expense=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_form_payments_content&action=delete_expense&id_expense=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
                return $buttons;
            default:
                return $item[$column_name];
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
            'motive' => __('Motive', 'edusystem'),
            'apply_to' => __('Apply to', 'edusystem'),
            'amount' => __('Amount', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_expenses_payroll()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $table_expenses = $wpdb->prefix . 'expenses';
        $transactions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_expenses} LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
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

        $data_invoices = $this->get_expenses_payroll();
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
                    return "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_content&section_tab=invoices_institutes&id_payment=' . $item->id) . "' class='button button-primary'>" . __('Pay', 'edusystem') . "</a>";
                } else {
                    return "Paid";
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
            'status_id' => __('Status', 'edusystem'),
            'institute_id' => __('Institute', 'edusystem'),
            'month' => __('Month', 'edusystem'),
            'amount' => __('Amount', 'edusystem'),
            'total_orders' => __('Total Orders', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_invoices_institutes()
    {
        global $wpdb;
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        $only_pending = $_POST['only_pending'] ?? false;
        $only_pending_query = '';
        if ($only_pending) {
            $only_pending_query = 'WHERE status_id = 0';
        }

        $table_institutes_payments = $wpdb->prefix . 'institutes_payments';
        $transactions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_institutes_payments} {$only_pending_query} LIMIT {$per_page} OFFSET {$offset}");
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

class TT_All_Payment_Plans_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'program',
                'plural' => 'programs',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details&program_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a class='button button-danger' data-program_id='" . $item['id'] . "' onclick='modal_delete_program_js ( this )' ><span class='dashicons dashicons-trash'></span></a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
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
            // 'program' => __('Program', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'identificator' => __('Identificator', 'edusystem'),
            'description' => __('Description', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'price' => __('Price', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;

        // --- CONFIGURATION ---
        $per_page = 20; // number of items per page
        $table_programs = $wpdb->prefix . 'programs';

        // --- PAGINATION ---
        // Note: Using $_GET['paged'] for pagination as in the original code.
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $where = [];
        $args = [];
        // Checking for search term, typically passed via $_GET in WordPress admin lists
        $search_term = isset($_GET['s']) ? trim($_GET['s']) : (isset($_POST['s']) ? trim($_POST['s']) : '');

        // --- SEARCH LOGIC ---
        if (!empty($search_term)) {
            $search = $wpdb->esc_like($search_term);
            $like = "%{$search}%";

            // Search against identificator, name, and description in the programs table
            $where[] = "(`identificator` LIKE %s OR `name` LIKE %s OR `description` LIKE %s)";
            $args = array_merge($args, [$like, $like, $like]);
        }

        // --- FINAL QUERY CONSTRUCTION ---
        $where_sql = '';
        if (!empty($where)) {
            // Since there's only search logic, we just join the conditions with AND (or just use the first one)
            $where_sql = 'WHERE ' . implode(' AND ', $where);
        }

        // Add pagination arguments to the list for preparation
        $args[] = $per_page;
        $args[] = $offset;

        // Prepare the final SQL query
        $sql = $wpdb->prepare(
            "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_programs} {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d",
            $args
        );

        $programs = $wpdb->get_results($sql, ARRAY_A);
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // --- DATA PROCESSING ---
        $programs_array = [];
        if ($programs) {
            foreach ($programs as $pensum) {
                array_push($programs_array, [
                    'id' => $pensum['id'],
                    'status' => $pensum['is_active'] ? 'Active' : 'Inactive',
                    'name' => $pensum['name'],
                    'identificator' => $pensum['identificator'],
                    'description' => $pensum['description'],
                    'price' => $pensum['total_price'],
                    'created_at' => $pensum['created_at'],
                ]);
            }
        }

        return ['data' => $programs_array, 'total_count' => $total_count];
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

        $other_data = $this->get_pensum();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $other_data['data'];
        $total_count = (int) $other_data['total_count'];

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

class TT_All_Fees_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => __('Fee', 'edusystem'),
                'plural' => __('Fees', 'edusystem'),
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=fees_content&section_tab=fee_details&fee_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a class='button button-danger' data-fee_id='" . $item['id'] . "' data-product_id='" . $item['product_id'] . "' onclick='modal_delete_fee_js( this )' ><span class='dashicons dashicons-trash'></span></a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
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
            'name' => __('Name', 'edusystem'),
            'price' => __('Price', 'edusystem'),
            'description' => __('Description', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_admission_fees()
    {
        global $wpdb;
        $fees_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_admission_fees = $wpdb->prefix . 'admission_fees';
        $fees = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_admission_fees} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", ARRAY_A);

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($fees) {
            foreach ($fees as $fee) {

                array_push($fees_array, [
                    'id' => (int) $fee['id'],
                    'name' => $fee['name'],
                    'product_id' => (int) $fee['product_id'],
                    'price' => wc_price((float) $fee['price']),
                    'description' => $fee['description'],
                    'created_at' => $fee['created_at'],
                ]);
            }
        }

        return ['data' => $fees_array, 'total_count' => $total_count];
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

        $other_data = $this->get_admission_fees();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $other_data['data'];
        $total_count = (int) $other_data['total_count'];

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

function get_admission_fee($fee_id)
{
    global $wpdb;
    $fee = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM  `{$wpdb->prefix}admission_fees` WHERE id = %d",
            intval($fee_id)
        ),
        ARRAY_A
    );

    return $fee ?? null;
}



add_action('wp_ajax_nopriv_generate_quote_public', 'generate_quote_public_callback');
add_action('wp_ajax_generate_quote_public', 'generate_quote_public_callback');

function generate_quote_public_callback()
{
    // 1. Input Validation and Sanitization
    if (!isset($_POST['payment_id'])) {
        wp_send_json_error(array('message' => 'Missing required parameters.'));
        die();
    }

    global $wpdb;
    $payment_id = floatval($_POST['payment_id']);
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $payment_row = $wpdb->get_row("SELECT * FROM {$table_student_payments} WHERE id={$payment_id}");
    $student = get_student_detail($payment_row->student_id);
    $customer_id = $student->partner_id;

    // Crear una nueva orden
    $order = wc_create_order(array('customer_id' => $customer_id));

    // Añadir productos a la orden
    $product = wc_get_product($payment_row->variation_id);
    if (!$product) {
        $product = wc_get_product($payment_row->original_amount_product);
    }

    if ($product) {
        $quantity = 1;
        $product->set_price($payment_row->amount);

        if ($payment_row->num_cuotes == 1)
            $product->set_regular_price($payment_row->original_amount_product);

        $order->add_product($product, $quantity);
    }

    // Obtener información del cliente existente
    $customer = new WC_Customer($customer_id);

    // 🔥 CORREGIR OBTENCIÓN DE NOMBRES - Usar datos de usuario si no hay en billing
    $first_name = $customer->get_billing_first_name();
    $last_name = $customer->get_billing_last_name();

    // Si no hay nombre en billing, usar los datos base del usuario
    if (empty($first_name)) {
        $first_name = $customer->get_first_name();
    }
    if (empty($last_name)) {
        $last_name = $customer->get_last_name();
    }

    // Si aún no hay nombre, usar valores por defecto
    $first_name = !empty($first_name) ? $first_name : 'Nombre';
    $last_name = !empty($last_name) ? $last_name : 'Apellido';

    // Usar direcciones del cliente si existen, de lo contrario usar valores por defecto
    $billing_address = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'company' => $customer->get_billing_company(),
        'email' => $customer->get_billing_email() ?: $customer->get_email(),
        'phone' => $customer->get_billing_phone(),
        'address_1' => $customer->get_billing_address_1(),
        'address_2' => $customer->get_billing_address_2(),
        'city' => $customer->get_billing_city(),
        'state' => $customer->get_billing_state(),
        'postcode' => $customer->get_billing_postcode(),
        'country' => $customer->get_billing_country()
    );

    $shipping_address = array(
        'first_name' => $customer->get_shipping_first_name() ?: $first_name,
        'last_name' => $customer->get_shipping_last_name() ?: $last_name,
        'company' => $customer->get_shipping_company(),
        'address_1' => $customer->get_shipping_address_1(),
        'address_2' => $customer->get_shipping_address_2(),
        'city' => $customer->get_shipping_city(),
        'state' => $customer->get_shipping_state(),
        'postcode' => $customer->get_shipping_postcode(),
        'country' => $customer->get_shipping_country()
    );

    // Establecer direcciones
    $order->set_billing_address($billing_address);
    $order->set_shipping_address($shipping_address);

    // 🔥 CORREGIR METADATOS - student_id debe ser el ID del estudiante
    $order->update_meta_data('cuote_payment', $payment_row->id);
    $order->update_meta_data('student_id', $payment_row->student_id); // Corregido aquí
    $order->update_meta_data('institute_id', $payment_row->institute_id);

    // Calcular totales (ahora con el monto personalizado)
    $order->calculate_totals();

    // Guardar la orden
    $order->save();

    // Generate checkout URL
    $checkout_url = wc_get_checkout_url() . 'order-pay/' . $order->get_id() . '/?pay_for_order=true&key=' . $order->get_order_key();

    wp_send_json_success(array('url' => $checkout_url));
    die();
}

function manage_payments_search_student_callback()
{
    global $wpdb;
    $search_term = sanitize_text_field($_POST['q'] ?? ''); // Use null coalescing for safety
    $return_id = $_POST['return_id'] ?? false; // Use null coalescing for safety
    $table_students = $wpdb->prefix . 'students';

    $conditions = [];
    $params = [];

    // Smart search conditions using CONCAT_WS for combined names
    $search_term_like = '%' . $wpdb->esc_like($search_term) . '%';

    // Combined search for names and surnames (CONCAT_WS)
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
        $conditions[] = "{$field_combination} LIKE %s";
        $params[] = $search_term_like;
    }

    // Direct search in individual fields (as was originally)
    $individual_fields = ['name', 'middle_name', 'last_name', 'middle_last_name', 'email', 'id_document'];
    foreach ($individual_fields as $field) {
        $conditions[] = "{$field} LIKE %s";
        $params[] = $search_term_like;
    }

    // Build the WHERE clause
    $where_clause = '';
    if (!empty($conditions)) {
        $where_clause = " WHERE " . implode(" OR ", $conditions);
    }

    // Prepare the full SQL query securely
    $query = "SELECT * FROM {$table_students} {$where_clause}";

    $students = $wpdb->get_results($wpdb->prepare($query, $params));

    $data = [];
    if ($students) {
        foreach ($students as $student) {
            $data[] = [
                'id' => $return_id ? $student->id : $student->id_document, // Using id_document as the ID for the select option
                'description' => $student->id_document . ' - ' . ($student->email ?? ''), // Ensure email exists
                'text' => trim(
                    ($student->name ?? '') . ' ' .
                    ($student->middle_name ?? '') . ' ' .
                    ($student->last_name ?? '') . ' ' .
                    ($student->middle_last_name ?? '')
                )
            ];
        }
    }

    // Send JSON response
    wp_send_json(['items' => $data]);
}

add_action('wp_ajax_nopriv_manage_payments_search_student', 'manage_payments_search_student_callback');
add_action('wp_ajax_manage_payments_search_student', 'manage_payments_search_student_callback');