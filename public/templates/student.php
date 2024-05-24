<h2 style="font-size:24px;text-align:center;"><?= __('Student','form-plugin'); ?></h2>
<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?= __('Full Name','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Birth Date','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Email','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Phone','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Grade','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Program(s)','form-plugin'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Institute name','form-plugin'); ?></span></th>
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
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Full Email','form-plugin'); ?>">
                        <?= $row->email; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Phone','form-plugin'); ?>">
                        <?= $row->phone; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?= __('Grade','form-plugin'); ?>">
                       <?= 
                            $grade = match($row->grade_id){
                                '1' => __('9no (antepenúltimo)','form-plugin'),
                                '2' => __('10mo (penúltimo)','form-plugin'),
                                '3' => __('11vo (último)','form-plugin'),
                                '4' => __('Bachiller (graduado)','form-plugin')
                            }
                       ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Program','form-plugin') ?>">
                        <?= 
                            $program = match($row->program_id){
                                'aes' => __('AES (Dual Diploma)','form-plugin'),
                                'psp' => __('PSP (Carrera Universitaria)','form-plugin'),
                                'aes_psp' => __('AES (Dual Diploma)','form-plugin').','.__('AES (Dual Diploma)','form-plugin'),
                            }
                        
                        ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Institute','form-plugin'); ?>">
                        <?= $row->name_institute; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>