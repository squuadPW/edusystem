<h2 style="font-size:24px;text-align:center;"><?= __('Students','form-plugin'); ?></h2>
<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?= __('Full Name','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Birth Date','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Grade','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Program(s)','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Institute name','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Actions','form-plugin'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($student)): ?>
            <?php foreach($student as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Full Name','form-plugin'); ?>">
                        <?= $row->name.' '.$row->last_name; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?= __('Date','form-plugin'); ?>">
                        <?= wp_date('F j, Y',strtotime($row->birth_date)); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?= __('Grade','form-plugin'); ?>">
                       <?= $grade = get_name_grade($row->grade_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Program','form-plugin') ?>">
                        <?= $program = get_name_program($row->program_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Institute','form-plugin'); ?>">
                        <?= $row->name_institute; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Institute','form-plugin'); ?>">
                        <a href="<?= wc_get_account_endpoint_url('student-details').'/?student='.$row->id; ?>" class="button button-primary"><?= __('Edit','aes'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>