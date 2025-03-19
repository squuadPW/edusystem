<h2 style="font-size:24px;text-align:center;"><?= __('Califications', 'edusystem'); ?></h2>

<section class="segment" style="margin-top: 20px">
    <div class="segment-button-history active" data-option="current"><?= __('Current', 'edusystem'); ?></div>
    <div class="segment-button-history" data-option="history"><?= __('History', 'edusystem'); ?></div>
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
                                    class="nobr"><?= __('Course', 'edusystem'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Assignment', 'edusystem'); ?></span></th>
                            <!-- <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                            class="nobr"><?= __('Max date', 'edusystem'); ?></span></th> -->
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Calification', 'edusystem'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student['formatted_assignments'])): ?>
                            <?php foreach ($student['formatted_assignments'] as $row): ?>

                                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                    style="background-color: blue !important">
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Course', 'edusystem'); ?>">
                                        <strong><?= strtoupper($row['course']) ?></strong>
                                    </td>
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                        data-title="<?= __('Assignment', 'edusystem'); ?>">
                                    </td>
                                    <!-- <td style="background-color: #f8f8f8;"
                                class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                data-title="<?= __('Max date', 'edusystem') ?>">
                            </td> -->
                                    <td style="background-color: #f8f8f8;"
                                        class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Calification', 'edusystem') ?>">
                                    </td>
                                </tr>

                                <?php foreach ($row['assignments'] as $key => $assignment) { ?>
                                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                        style="background-color: green !important">
                                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                            data-title="<?= __('Course', 'edusystem'); ?>">

                                        </td>
                                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                            data-title="<?= __('Assignment', 'edusystem'); ?>">
                                            <?= $assignment['name'] ?>
                                        </td>
                                        <!-- <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                    data-title="<?= __('Max date', 'edusystem') ?>">
                                    <?= $assignment['max_date'] ?>
                                </td> -->
                                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                            data-title="<?= __('Calification', 'edusystem') ?>">
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
                                class="nobr"><?= __('Status', 'edusystem'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span
                                    class="nobr"><?= __('Code', 'edusystem'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Subject', 'edusystem'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                class="nobr"><?= __('Calification', 'edusystem'); ?></span></th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Academic period', 'edusystem'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student['formatted_assignments_history'])): ?>
                            <?php foreach ($student['formatted_assignments_history'] as $row): ?>

                                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                                    style="background-color: blue !important">
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Status', 'edusystem'); ?>">
                                        <?php
                                            switch ($row['status_id']) {
                                                case 0:
                                                    echo '<div style="color: gray; font-weight: 600">'. strtoupper('To begin') . '</div>';
                                                    break;
                                                case 1:
                                                    echo '<div style="color: blue; font-weight: 600">'. strtoupper('Active') . '</div>';
                                                    break;
                                                case 2:
                                                    echo '<div style="color: red; font-weight: 600">'. strtoupper('Unsubscribed') . '</div>';
                                                    break;
                                                case 3:
                                                    echo '<div style="color: green; font-weight: 600">'. strtoupper('Approved') . '</div>';
                                                    break;
                                                case 4:
                                                    echo '<div style="color: red; font-weight: 600">'. strtoupper('Reproved') . '</div>';
                                                    break;
                                            }
                                        ?>
                                    </td>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Code', 'edusystem'); ?>">
                                        <?= strtoupper($row['code_subject']) ?>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status"
                                        data-title="<?= __('Subject', 'edusystem'); ?>">
                                        <?= strtoupper($row['subject']) ?>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Calification', 'edusystem') ?>">
                                        <strong><?= strtoupper($row['calification']) ?></strong>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                        data-title="<?= __('Academic period', 'edusystem') ?>">
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