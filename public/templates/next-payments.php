<?php 
    $student_payments = $student_payments;
    foreach ($student_payments as $student_id => $payments) { ?>
        <h2 class="mb-4" style="font-size:24px;text-align:center;"><?= __('Next payments for ','aes') . get_student_from_id($student_id)[0]->name . ' ' . get_student_from_id($student_id)[0]->last_name; ?></h2>
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Payment number</span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Date</span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr">Total</span></th>
                    </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment) { ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-pending">
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total"><?php echo $payment->cuote ?></td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Date"><?php echo date('F d, Y', strtotime($payment->date_next_payment)) ?></td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total"><?php echo wc_price($payment->amount) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
        }
    ?>