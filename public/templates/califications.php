<h2 style="font-size:24px;text-align:center;"><?= __('Califications', 'form-plugin'); ?></h2>

<section class="segment" style="margin-top: 20px">
    <div class="segment-button-history active" data-option="current"><?= __('Current', 'aes'); ?></div>
    <div class="segment-button-history" data-option="history"><?= __('History', 'aes'); ?></div>
</section>

<div>
    <div id="current">
        <?php if (!empty($students_formatted)): ?>
            <?php foreach ($students_formatted as $key => $student) { ?>
                <table
                    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                    style="margin-top:20px;">
                    <caption style="text-align:start;">
                        Califications of <?php echo $student['student']->name ?>         <?php echo $student['student']->last_name ?>
                    </caption>
                    <thead>
                        <tr>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                                    class="nobr"><?= __('Course', 'aes'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Assignment', 'aes'); ?></span></th>
                            <!-- <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                            class="nobr"><?= __('Max date', 'aes'); ?></span></th> -->
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Calification', 'aes'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student['formatted_assignments'])): ?>
                            <?php foreach ($student['formatted_assignments'] as $row): ?>

                                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                    style="background-color: blue !important">
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Course', 'aes'); ?>">
                                        <strong><?= strtoupper($row['course']) ?></strong>
                                    </td>
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                        data-title="<?= __('Assignment', 'aes'); ?>">
                                    </td>
                                    <!-- <td style="background-color: #f8f8f8;"
                                class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                data-title="<?= __('Max date', 'aes') ?>">
                            </td> -->
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Calification', 'aes') ?>">
                                    </td>
                                </tr>

                                <?php foreach ($row['assignments'] as $key => $assignment) { ?>
                                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                        style="background-color: green !important">
                                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                            data-title="<?= __('Course', 'aes'); ?>">

                                        </td>
                                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                            data-title="<?= __('Assignment', 'aes'); ?>">
                                            <?= $assignment['name'] ?>
                                        </td>
                                        <!-- <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                    data-title="<?= __('Max date', 'aes') ?>">
                                    <?= $assignment['max_date'] ?>
                                </td> -->
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
            <?php } ?>
        <?php else: ?>
            <div style="margin: 0 auto; text-align: center; padding: 18px;">
                <span>Oops! it looks like you don't have any students logged into moodle yet. Please come back when you have
                    access to moodle 👋</span>
            </div>
        <?php endif; ?>
    </div>
    <div id="history" style="display: none">
    <?php if (!empty($students_formatted_history)): ?>
            <?php foreach ($students_formatted_history as $key => $student) { ?>
                <table
                    class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                    style="margin-top:20px;">
                    <caption style="text-align:start;">
                        Califications of <?php echo $student['student']->name ?>         <?php echo $student['student']->last_name ?>
                    </caption>
                    <thead>
                        <tr>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                                    class="nobr"><?= __('Code', 'aes'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Subject', 'aes'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                class="nobr"><?= __('Calification', 'aes'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Academic period', 'aes'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student['formatted_assignments_history'])): ?>
                            <?php foreach ($student['formatted_assignments_history'] as $row): ?>

                                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                    style="background-color: blue !important">
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Code', 'aes'); ?>">
                                        <?= strtoupper($row['code_subject']) ?>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                        data-title="<?= __('Subject', 'aes'); ?>">
                                        <?= strtoupper($row['subject']) ?>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Calification', 'aes') ?>">
                                        <strong><?= strtoupper($row['calification']) ?></strong>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Academic period', 'aes') ?>">
                                        <?= strtoupper($row['code_period']) . ' - ' . strtoupper($row['cut']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php else: ?>
            <div style="margin: 0 auto; text-align: center; padding: 18px;">
                <span>Oh you're new here! apparently you don't have any note history that we can provide you, as soon as we have it you'll see it here 😁</span>
            </div>
        <?php endif; ?>
    </div>
</div>