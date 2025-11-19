<?php
foreach ($student_payments as $student_id => $payments) { 
    $student = get_student_detail($student_id);
    ?>
    <h2 class="mb-4" style="font-size:24px;text-align:center;">
        <?php
            $user_name = $student->name . ' ' . $student->last_name;
            $translated_text = sprintf(
                /* translators: %s: User's name */
                __('%s\'s Installments', 'edusystem'),
                $user_name
            );
            echo $translated_text;
        ?>
    </h2>
    <table
        class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
        <thead>
            <tr>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-quota"><span
                        class="nobr"><?= __('Installment', 'edusystem') ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                        class="nobr"><?= __('Date', 'edusystem') ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span
                        class="nobr"><?= __('Total', 'edusystem') ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action" style="text-align: end"><span
                        class="nobr"><?= __('Action', 'edusystem') ?></span></th>
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
                        <?= wc_price($payment->amount, [ 'currency' => $payment->currency ]) ?></td>
                    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-action" data-title="Action" style="text-align: end">
                        <?php if($key == 0 && !$pending_orders) { ?>
                            <button style="width: 70px;" type="button" class="button button-primary" id="generate-quote" data-payment-id="<?= $payment->id ?>"><?= __('Pay', 'edusystem') ?></button>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}
?>