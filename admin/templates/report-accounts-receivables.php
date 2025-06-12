<div class="wrap">
    <div id="card-totals-sales" class="grid-container-report-2">
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
            <div>Total orders</div>
            <div style="margin-top: 10px"><strong id="orders"></strong></div>
        </div>
        <div style="background-color: #c5f3c5;" class="card-report-sales tooltip"
            title="All upcoming accounts receivable (pending quotes)">
            <div>Accounts receivable </div>
            <div style="margin-top: 10px"><strong id="receivable"></strong></div>
        </div>
    </div>
    <div style="width:100%;text-align:center;padding-top:10px;">

        <?php if (wp_is_mobile()) { ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <select id="typeFilter" name="typeFilter" autocomplete="off">
                <?php } ?>
                <option value="today"><?= __('Today', 'edusystem'); ?></option>
                <option value="tomorrow"><?= __('Tomorrow', 'edusystem'); ?></option>
                <option value="this-week"><?= __('This week', 'edusystem'); ?></option>
                <option value="next-week"><?= __('Next week', 'edusystem'); ?></option>
                <option value="this-month" selected><?= __('This month', 'edusystem'); ?></option>
                <option value="next-month"><?= __('Next month', 'edusystem'); ?></option>
                <option value="custom"><?= __('Custom', 'edusystem'); ?></option>
            </select>
            <?php if (wp_is_mobile()) { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                    style="display:none;width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
            <?php } ?>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data_accounts_receivable" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'edusystem'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_accounts_receivable"
                    class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
            <?php endif; ?>
    </div>

    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-primary column-student-header">
                    <?= __('Student', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-parent-header">
                    <?= __('Parent', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-product-header">
                    <?= __('Product', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-amount-header">
                    <?= __('Amount', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-number-cuote-header">
                    <?= __('Number cuote', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-total-cuotes-header">
                    <?= __('Total cuotes', 'edusystem'); ?>
                </th>
                <th scope="col" class="manage-column column-date-header">
                    <?= __('Date', 'edusystem'); ?>
                </th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">
            <?php if (!empty($orders['cuotes'])) { ?>
                <?php foreach ($orders['cuotes'] as $order) { ?>
                    <tr>
                        <td class="column-primary" data-colname="<?= __('Student', 'edusystem'); ?>">
                            <?= $order['student']; ?>
                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                        </td>
                        <td data-colname="<?= __('Product', 'edusystem'); ?>">
                            <?= $order['product']; ?>
                        </td>
                        <td data-colname="<?= __('Amount', 'edusystem'); ?>">
                            <?= wc_price($order['amount']); ?>
                        </td>
                        <td data-colname="<?= __('Cuote', 'edusystem'); ?>">
                            <?= $order['cuote']; ?>
                        </td>
                        <td data-colname="<?= __('Total cuotes', 'edusystem'); ?>">
                            <?= $order['num_cuotes']; ?>
                        </td>
                        <td data-colname="<?= __('Date', 'edusystem'); ?>">
                            <b><?= $order['date_next_payment']; ?></b>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan='7' style='text-align:center;'><?= __('There are not records', 'edusystem') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        flatpickr(document.getElementById('inputStartDate'), {
            mode: "range",
            dateFormat: "m/d/Y",
            defaultDate: ['<?= $start_date ?>', '<?= $start_date ?>'],
        });

    });
</script>