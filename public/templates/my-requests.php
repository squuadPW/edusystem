<h2 style="font-size:24px;text-align:center;"><?= __('Requests', 'form-plugin'); ?></h2>
<div style="text-align: center; margin: 30px;">
    <span><button type="button" class="submit" style="width: 180px !important" id="add_new_request">New
            request</button></span>
</div>
<table
    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
    style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                    class="nobr"><?= __('Request ID', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Type', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                    class="nobr"><?= __('Status', 'aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date" style="text-align: end !important"><span
                    class="nobr"><?= __('Actions', 'aes'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                        data-title="<?= __('Request ID', 'aes'); ?>">
                        <?= $row->id; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                        data-title="<?= __('Type', 'aes'); ?>">
                        <?php $type_detail = get_type_request_details($row->type_id); ?>
                        <?= $type_detail->type; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Status', 'aes') ?>">
                        <?= get_request_status($row->status_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                        data-title="<?= __('Message', 'aes') ?>" style="text-align: end">
                        <span><button type="button" class="submit" style="font-size: 12px !important; width: 120px !important;" onclick="view_details_request('<?= $type_detail->type; ?>', '<?= htmlspecialchars($row->description, ENT_QUOTES) ?>', '<?= htmlspecialchars($row->response, ENT_QUOTES) ?>', '<?= get_request_status($row->status_id); ?>')">View details</button></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
    include('modal-create-request.php');
    include('modal-detail-request.php');
?>