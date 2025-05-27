<h2 style="font-size:24px;text-align:center;"><?= __('Califications', 'edusystem'); ?></h2>

<?php if ($admin_virtual_access) { ?>
    <section class="segment" style="margin-top: 20px">
        <div class="segment-button-history active" data-option="current"><?= __('Current', 'edusystem'); ?></div>
        <div class="segment-button-history" data-option="history"><?= __('History', 'edusystem'); ?></div>
    </section>
<?php } ?>

<div>
    <div id="current" style="display: <?php echo $admin_virtual_access ? 'block' : 'none'; ?>">
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
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                                    class="nobr"><?= __('Calification', 'edusystem'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($student['formatted_assignments'])): ?>
                            <?php foreach ($student['formatted_assignments'] as $row): ?>

                                <?php if (count($row['assignments']) > 0) { ?>
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
                                        <td style="background-color: #f8f8f8;"
                                            class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total"
                                            data-title="<?= __('Calification', 'edusystem') ?>">
                                        </td>
                                    </tr>
                                <?php } ?>

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
                <span>It looks like you don't have any courses registered in moodle yet. Please check back shortly when you are assigned 👋</span>
            </div>
        <?php endif; ?>
    </div>
    <div id="history" style="display: <?php echo $admin_virtual_access ? 'none' : 'block'; ?>">
        <?php if (!empty($history)): ?>
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-type"><span
                                class="nobr"><?= __('Type', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subject-code"><span
                                class="nobr"><?= __('Subject - Code', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subject-code"><span
                                class="nobr"><?= __('Prom califications', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-section"><span
                                class="nobr"><?= __('Section', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-term"><span
                                class="nobr"><?= __('Term', 'edusystem'); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order"
                            style="background-color: blue !important">
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-type"
                                data-title="<?= __('Type', 'edusystem'); ?>">
                                <?= ucwords($row->type) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-subject-code"
                                data-title="<?= __('Subject - Code', 'edusystem'); ?>">
                                <?= strtoupper($row->subject) . ' - ' . strtoupper($row->code_subject) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-section"
                                data-title="<?= __('Prom califications', 'edusystem'); ?>">
                                <?= strtoupper($row->prom_calification) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-section"
                                data-title="<?= __('Section', 'edusystem'); ?>">
                                <?= strtoupper($row->section) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-term"
                                data-title="<?= __('Term', 'edusystem') ?>">
                                <?= strtoupper($row->code_period) . ' - ' . strtoupper($row->cut_period) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="margin: 0 auto; text-align: center; padding: 18px;">
                <span>Oh you're new here! apparently you don't have any note history that we can provide you, as soon as we
                    have it you'll see it here 😁</span>
            </div>
        <?php endif; ?>
    </div>
</div>