<h2 style="font-size:24px;text-align:center;"><?= __('My tickets', 'form-plugin'); ?></h2>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
    style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                    class="nobr"><?= __('Ticket ID', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Email', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Subject', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Message', 'aes'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                        data-title="<?= __('Ticket ID', 'aes'); ?>">
                        <a href="https://soporte.american-elite.us/view.php" target="blank" style="text-decoration: underline !important; color: #002fbd;"><?= $row->ticket_id; ?></a>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                        data-title="<?= __('Email', 'aes'); ?>">
                        <?= $row->email; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Subject', 'aes') ?>">
                        <?= $row->subject; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Message', 'aes') ?>">
                        <?= $row->message; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>