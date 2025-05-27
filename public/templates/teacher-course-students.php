<h2 style="font-size:24px;text-align:center;"><?= __('Students', 'edusystem'); ?></h2>

<div style="margin-top:20px;">
    <a href="<?= wc_get_account_endpoint_url('teacher-courses') ?>" class="button button-primary" style="width:auto"><?= __('Back','edusystem'); ?></a>
</div>

<div>
    <table
        class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
        style="margin-top:20px;">
        <thead>
            <tr>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-names"><span
                        class="nobr"><?= __('Names(s)', 'edusystem'); ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-email"><span
                        class="nobr"><?= __('Email', 'edusystem'); ?></span></th>
                <th class="woocommerce-orders-table__header woocommerce-orders-table__header-inscription-date"><span
                        class="nobr"><?= __('Inscription date', 'edusystem'); ?></span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                    style="background-color: blue !important">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-names"
                        data-title="<?= __('Name(s)', 'edusystem'); ?>">
                        <?= $row->last_name . ' ' . $row->middle_last_name . ' ' . $row->name . ' ' . $row->middle_name ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-email"
                        data-title="<?= __('Email', 'edusystem'); ?>">
                        <?= $row->email ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-inscription-date"
                        data-title="<?= __('Inscription date', 'edusystem'); ?>">
                        <?php
                            echo date('m/d/Y', strtotime($row->inscription_at));
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>