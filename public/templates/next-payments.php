<?php
foreach ($student_payments as $student_id => $payments) { ?>
    <h2 class="mb-4" style="font-size:24px;text-align:center;">
        <?= __('Next payments for ', 'edusystem') . get_student_from_id($student_id)[0]->name . ' ' . get_student_from_id($student_id)[0]->last_name; ?>
    </h2>
    <table
        class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
        <thead>
            <tr>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-quota"><span
                        class="nobr">Quota</span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                        class="nobr">Date</span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span
                        class="nobr">Total</span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action" style="text-align: end"><span
                        class="nobr">Action</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $key => $payment) { ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-pending">
                    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-quota" data-title="Cuote">
                        #<?= $payment->cuote ?></td>
                    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Date">
                        <?= date('F d, Y', strtotime($payment->date_next_payment)) ?></td>
                    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total">
                        <?= wc_price($payment->amount) ?></td>
                    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-action" data-title="Action" style="text-align: end">
                        <?php if($key == 0 && !$pending_orders) { ?>
                            <button style="width: 70px;" type="button" class="button button-primary" id="generate-quote" data-id="<?= $student_id ?>" data-amount="<?= $payment->amount ?>">Pay</button>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php 
    $fee_graduation_ready = get_payments($student_id, product_id: FEE_GRADUATION);
    if (!$fee_graduation_ready) { ?>
        <form method="POST" action="<?= the_permalink() . '?action=pay_graduation_fee&student_id='.$student_id; ?>" class="form-aes" style="text-align: center;">
            <button type="submit" class="submit" id="buttonsave"><?= __('Pay graduation fee', 'edusystem'); ?></button>
        </form>
    <?php } ?>
    <?php
}
?>