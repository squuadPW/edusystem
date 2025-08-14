<h2 style="font-size:24px;text-align:center;"><?= __('Support tickets', 'edusystem'); ?></h2>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
    style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                    class="nobr"><?= __('Ticket ID', 'edusystem'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Email', 'edusystem'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Subject', 'edusystem'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Message', 'edusystem'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                        data-title="<?= __('Ticket ID', 'edusystem'); ?>">
                        <a href="https://support.americanelite.school/view.php" target="blank"
                            style="text-decoration: underline !important; color: #002fbd;"><?= $row->ticket_id; ?></a>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                        data-title="<?= __('Email', 'edusystem'); ?>">
                        <?= $row->email; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Subject', 'edusystem') ?>">
                        <?= $row->subject; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Message', 'edusystem') ?>">
                        <?php
                        $message = $row->message;
                        $maxLength = 100; // Define el número máximo de caracteres antes de añadir los puntos suspensivos
                
                        if (strlen($message) > $maxLength) {
                            echo substr($message, 0, $maxLength) . '...';
                        } else {
                            echo $message;
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>