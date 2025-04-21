<?php
foreach ($student_payments as $student_id => $payments) { 
    $student = get_student_detail($student_id);
    ?>
    <h2 class="mb-4" style="font-size:24px;text-align:center;">
        <?= __('Quotes for ', 'edusystem') . $student->name . ' ' . $student->last_name; ?>
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
}
?>

<?php
    $product = wc_get_product(FEE_GRADUATION);
    $name_product = $product->get_name();
    $price_product = $product->get_price();
?>
<!-- <h2 class="mb-4" style="font-size:24px;text-align:center;">
    <?= __('Student Graduation Fees', 'edusystem'); ?>
</h2>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-quota"><span
                    class="nobr">Student</span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-payment"><span
                    class="nobr">Payment</span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-price"><span
                    class="nobr">Price</span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action" style="text-align: end"><span
                    class="nobr">Action</span></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $key => $student) { ?>
            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-pending">
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-quota" data-title="Cuote">
                    <?= $student->name ?> <?= $student->last_name ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-payment" data-title="Payment">
                    <?= $name_product ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-price" data-title="Price">
                    <?= wc_price($price_product); ?>
                </td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-action" data-title="Action" style="text-align: end">
                    <?php 
                    $fee_graduation_ready = get_payments($student->id, product_id: FEE_GRADUATION);
                    if (!$fee_graduation_ready) { ?>
                        <form method="POST" action="<?= the_permalink() . '?action=pay_graduation_fee&student_id='.$student->id; ?>" style="margin-bottom: 0;">
                            <button style="width: 70px;" type="submit" class="button button-primary">Pay</button>
                        </form>
                    <?php } else { ?>
                        <button style="width: 90px; cursor: not-allowed; opacity: 0.5;" type="submit" class="button button-primary">Ready</button>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table> -->