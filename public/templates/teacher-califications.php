<h2 style="font-size:24px;text-align:center;"><?= __('Califications', 'edusystem'); ?></h2>

<section class="segment" style="margin-top: 20px">
    <div class="segment-button-history active" data-option="current"><?= __('Current', 'edusystem'); ?></div>
    <div class="segment-button-history" data-option="history"><?= __('History', 'edusystem'); ?></div>
</section>

<div>
    <div id="current" style="display: block">
        <?php if (!empty($current)): ?>
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-type"><span
                                class="nobr"><?= __('Type', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subject-code"><span
                                class="nobr"><?= __('Subject - Code', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-section"><span
                                class="nobr"><?= __('Section', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-term"><span
                                class="nobr"><?= __('Term', 'edusystem'); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current as $row): ?>
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
                <span>It looks like you don't have any courses registered in moodle yet. Please check back shortly when you
                    are assigned 👋</span>
            </div>
        <?php endif; ?>
    </div>
    <div id="history" style="display: none">
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