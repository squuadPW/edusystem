<?php if(in_array('student',$roles)): ?>
    <h2 style="font-size:24px;text-align:center;"><?= __('Student Information','form-plugin'); ?></h2>
<?php elseif(in_array('parent',$roles)): ?>
    <h2 style="font-size:24px;text-align:center;"><?= __('Students Information','form-plugin'); ?></h2>
<?php endif; ?>
<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" style="margin-top:20px;">
    <thead>
        <tr>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?= __('Full Name','aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Grade','aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Program(s)','aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Email','aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Virtual Classroom','aes'); ?></span></th>
            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?= __('Actions','aes'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($student)): ?>
            <?php foreach($student as $row): ?>
                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Full Name','aes'); ?>">
                        <?= $row->name.' '.$row->last_name; ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?= __('Grade','aes'); ?>">
                       <?= $grade = get_name_grade($row->grade_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Program','aes') ?>">
                        <?= $program = get_name_program($row->program_id); ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Email','aes') ?>">
                        <?= $row->email; ?>
                    </td>
                    <!--
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Program','aes') ?>">
                        <?php if(!empty($row->moodle_password)): ?>
                        <form class="woocommerce-form woocommerce-form-login login mt-4">
                            <input class="woocommerce-Input woocommerce-Input--text input-text input-no-style" type="password" name="password" id="password" value="<?= $row->moodle_password; ?>">
                        </form>
                        <?php endif; ?>
                    </td>
                    -->
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Virtual Classroom','aes'); ?>">
                        <?php if($row->moodle_student_id > 0){ ?>
                            <a target="_blank" class="button" href="<?= home_url('?action=access_moodle_url&student_id='.$row->id); ?>" disabled><?= __('Access','aes') ?></a>
                        <?php }else{ ?>
                            <button class="button" disabled><?= __('Access','aes') ?></button>
                        <?php } ?>
                    </td>
                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="<?= __('Edit','aes'); ?>">
                        <a href="<?= wc_get_account_endpoint_url('student-details').'/?student='.$row->id; ?>" class="button button-primary"><?= __('Edit','aes'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>