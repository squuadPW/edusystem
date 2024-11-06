<h2 style="font-size:24px;text-align:center;"><?= __('Califications', 'form-plugin'); ?></h2>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
    style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                    class="nobr"><?= __('Course', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Assignment', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Start date', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Due date', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Calification', 'aes'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($formatted_assignments)): ?>
            <?php foreach ($formatted_assignments as $row): ?>

                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order" style="background-color: blue !important">
                    <td style="background-color: #f8f8f8;" class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                        data-title="<?= __('Course', 'aes'); ?>">
                        <strong><?= strtoupper($row['course']) ?></strong>
                    </td>
                    <td style="background-color: #f8f8f8;" class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                        data-title="<?= __('Assignment', 'aes'); ?>">
                    </td>
                    <td style="background-color: #f8f8f8;" class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Start date', 'aes') ?>">
                    </td>
                    <td style="background-color: #f8f8f8;" class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('End date', 'aes') ?>">
                    </td>
                    <td style="background-color: #f8f8f8;" class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Calification', 'aes') ?>">
                    </td>
                </tr>

                <?php foreach ($row['assignments'] as $key => $assignment) { ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"  style="background-color: green !important">
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                            data-title="<?= __('Course', 'aes'); ?>">
                            
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                            data-title="<?= __('Assignment', 'aes'); ?>">
                            <?= $assignment['name'] ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                            data-title="<?= __('Start date', 'aes') ?>">
                            <?= $assignment['start_date'] ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                            data-title="<?= __('End date', 'aes') ?>">
                            <?= $assignment['end_date'] ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                            data-title="<?= __('Calification', 'aes') ?>">
                            <?= $assignment['grade'] ?> / <?= $assignment['max_grade'] ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>