<div class="wrap">
    <div id="card-totals-sales" class="grid-container-report">
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
            <div>Total orders</div>
            <div style="margin-top: 10px"><strong id="orders"></strong></div>
        </div>
        <div style="background-color: #d6ecfb;" class="card-report-sales tooltip" title="Gross sales of orders">
            <div>Gross sales of orders</div>
            <div style="margin-top: 10px"><strong id="gross"></strong></div>
        </div>
        <div style="background-color: #ffe0e6;" class="card-report-sales tooltip"
            title="The subtraction of the institution fee, alliance fee, payment fee, system fee and the order tax is applied">
            <div>Net Sale</div>
            <div style="margin-top: 10px"><strong id="net"></strong></div>
        </div>
        <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for alliances">
            <div>Alliances fee</div>
            <div style="margin-top: 10px"><strong id="a_fee"></strong></div>
        </div>
        <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for institutes">
            <div>Institutes fee</div>
            <div style="margin-top: 10px"><strong id="i_fee"></strong></div>
        </div>
        <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for payments">
            <div>Payments fee</div>
            <div style="margin-top: 10px"><strong id="p_fees"></strong></div>
        </div>
        <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for system (Edusof)">
            <div>Edusof fee</div>
            <div style="margin-top: 10px"><strong id="e_fees"></strong></div>
        </div>
        <div style="background-color: #c791c7;" class="card-report-sales tooltip" title="Total taxes of all orders">
            <div>Tax total </div>
            <div style="margin-top: 10px"><strong id="tax"></strong></div>
        </div>
        <div style="background-color: #c5f3c5;" class="card-report-sales tooltip" title="All upcoming accounts receivable (pending quotes)">
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
                <option value="yesterday"><?= __('yesterday', 'restaurant-system-app'); ?></option>
                <option value="this-week"><?= __('This week', 'restaurant-system-app'); ?></option>
                <option value="last-week"><?= __('Last week', 'restaurant-system-app'); ?></option>
                <option value="this-month" selected><?= __('This month', 'restaurant-system-app'); ?></option>
                <option value="last-month"><?= __('Last month', 'restaurant-system-app'); ?></option>
                <option value="custom"><?= __('Custom', 'restaurant-system-app'); ?></option>
            </select>
            <?php if (wp_is_mobile()) { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                    style="display:none;width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
            <?php } ?>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data"
                    class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Payment ID', 'restaurant-system-app'); ?>
                </th>
                <th scope="col" class=" manage-column column-email"><?= __('Parent', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Student', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Total', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Created', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Actions', 'restaurant-system-app'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">
            <?php if (!empty($orders['orders'])) { ?>
                <?php foreach ($orders['orders'] as $order) { ?>
                    <tr>
                        <td class="column column-primary" data-colname="<?= __('Payment ID', 'restaurant-system-app'); ?>">
                            <?= '#' . $order['order_id']; ?>
                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                        </td>
                        <td class="column" data-colname="<?= __('Parent', 'restaurant-system-app'); ?>">
                            <?= $order['customer']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Student', 'restaurant-system-app'); ?>">
                            <?= $order['student']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Total', 'restaurant-system-app'); ?>">
                            <?= get_woocommerce_currency_symbol() . $order['total']; ?>
                        </td>
                        <td class="column" data-colname="<?= __('Created', 'restaurant-system-app'); ?>">
                            <b><?= $order['created_at']; ?></b>
                        </td>
                        <td class="column" data-colname="<?= __('Action', 'restaurant-system-app'); ?>">
                            <a class='button button-primary'
                                href="<?= admin_url('admin.php?page=report-sales&section_tab=payment-detail&payment_id=' . $order['order_id']) ?>"><?= __('View details', 'aes'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan='5' style='text-align:center;'><?= __('There are not records', 'aes') ?></td>
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