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
                <option value="today"><?= __('Today', 'restaurant-system-app'); ?></option>
                <option value="tomorrow"><?= __('Tomorrow', 'restaurant-system-app'); ?></option>
                <option value="this-week"><?= __('This week', 'restaurant-system-app'); ?></option>
                <option value="next-week"><?= __('Next week', 'restaurant-system-app'); ?></option>
                <option value="this-month" selected><?= __('This month', 'restaurant-system-app'); ?></option>
                <option value="next-month"><?= __('Next month', 'restaurant-system-app'); ?></option>
                <option value="custom"><?= __('Custom', 'restaurant-system-app'); ?></option>
            </select>
            <?php if (wp_is_mobile()) { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                    style="display:none;width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
            <?php } ?>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data_accounts_receivable" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_accounts_receivable"
                    class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column"><?= __('Parent', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Student', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Product', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Amount', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Number cuote', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Total cuotes', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Date', 'restaurant-system-app'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">
            <?php if (!empty($orders['cuotes'])) { ?>
                <?php foreach ($orders['cuotes'] as $order) { ?>
                    <tr>
                    <td class="column" data-colname="<?= __('Parent', 'restaurant-system-app'); ?>">
                            <?= $order['customer']; ?>
                        </td>
                    <td class="column" data-colname="<?= __('Student', 'restaurant-system-app'); ?>">
                            <?= $order['student']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Product', 'restaurant-system-app'); ?>">
                            <?= $order['product']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Amount', 'restaurant-system-app'); ?>">
                            <?= wc_price($order['amount']); ?>
                        </td>
                        <td class="column" data-colname="<?= __('Number cuote', 'restaurant-system-app'); ?>">
                            <?= $order['cuote']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Total cuotes', 'restaurant-system-app'); ?>">
                            <?= $order['num_cuotes']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Date', 'restaurant-system-app'); ?>">
                            <b><?= $order['date_next_payment']; ?></b>
                        </td>
                        <!-- <td class="column" data-colname="<?= __('Action', 'restaurant-system-app'); ?>">
                            
                        </td> -->
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan='7' style='text-align:center;'><?= __('There are not records', 'aes') ?></td>
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