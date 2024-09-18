<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Monthly invoice', 'aes'); ?></h2>
    <?php if (!in_array('institutes', $roles) && !in_array('alliance', $roles)): ?>
        <div style="diplay:flex;width:100%;">
            <a class="button button-outline-primary"
                href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'); ?>"><?= __('Back') ?></a>
        </div>
        <div style="diplay:flex;width:100%;">
            <h3><?= __('Name Institute:', 'aes') . ' ' . $institute->name; ?></h3>
        </div>
    <?php elseif (in_array('alliance', $roles)): ?>
        <div style="diplay:flex;width:100%;">
            <a class="button button-outline-primary"
                href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content'); ?>"><?= __('Back') ?></a>
        </div>
        <div style="diplay:flex;width:100%;">
            <h3><?= __('Institute:', 'aes') . ' ' . $institute->name; ?></h3>
        </div>
    <?php endif; ?>
    <div class="wrap">
        <div class="grid-container-report-4" id="card-totals-sales">
            <div class="card-report-sales tooltip" title="All orders" style="background-color: #97ffacb0;">
                <div><?= __('Balance', 'aes') . ': '; ?></div>
                <div style="margin-top: 10px"><strong id="fee-total-balance"><?= $orders['total']; ?></strong></div>
            </div>
            <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
                <div><?= __('Total paid', 'aes') . ': '; ?></div>
                <div style="margin-top: 10px"><strong id="fee-total-paid"><?= $orders['total']; ?></strong></div>
            </div>
            <div class="card-report-sales tooltip" title="All orders" style="background-color: #ffe797;">
                <div><?= __('Pending payment', 'aes') . ': '; ?></div>
                <div style="margin-top: 10px"><strong id="fee-pending-payment"><?= $orders['total']; ?></strong></div>
            </div>
            <div class="card-report-sales tooltip" title="All orders" style="background-color: rgb(195 151 255 / 53%);" id="card-invoices">
                <div><span id="length-invoices"><?= $orders['total']; ?></span> <?= __('Orders', 'aes'); ?></div>
                <div style="margin-top: 10px"><strong id="total-invoices"><?= $orders['total']; ?></strong></div>
            </div>
            <div class="card-report-sales tooltip" title="All orders" style="background-color: rgb(195 151 255 / 53%); display: none" id="card-transactions">
                <div><span id="length-transactions"><?= $orders['total']; ?></span> <?= __('Transactions', 'aes'); ?></div>
                <div style="margin-top: 10px"><strong id="total-transactions"><?= $orders['total']; ?></strong></div>
            </div>
        </div>
        <div>
            <div style="width:100%;text-align:end;padding-top:10px;">

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
                        <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                            style="display:none;width:200px;">
                    <?php } ?>
                    <?php if (wp_is_mobile()): ?>
                        <button type="button" id="update_data" class="button button-primary"
                            style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
                    <?php else: ?>
                        <button type="button" id="update_data"
                            class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
                    <?php endif; ?>
            </div>
            <input type="hidden" id="institute_id"
                value="<?= (isset($_GET['institute_id']) && !empty($_GET['institute_id'])) ? $_GET['institute_id'] : ''; ?>">
            <button type="button" id="toggle-table" class="button button-primary">Show payments</button>
            <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;" id="tab-orders">
                <thead>
                    <tr>
                        <th scope="col" class=" manage-column column-primary">
                            <?= __('Payment ID', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column-email">
                            <?= __('Customer', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column-phone"><?= __('Fee', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column"><?= __('Created', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column"><?= __('Actions', 'restaurant-system-app'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody id="table-institutes-payment">
                    <?php if (!empty($orders['orders'])) { ?>
                        <?php foreach ($orders['orders'] as $order) { ?>
                            <tr>
                                <td class="column column-primary"
                                    data-colname="<?= __('Payment ID', 'restaurant-system-app'); ?>">
                                    <?= '#' . $order['order_id']; ?>
                                    <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                </td>
                                <td class="column" data-colname="<?= __('Customer', 'restaurant-system-app'); ?>">
                                    <?= $order['customer']; ?>
                                </td>
                                <td class="column" data-colname="<?= __('Fee', 'restaurant-system-app'); ?>">
                                    <?= get_woocommerce_currency_symbol() . number_format($order['fee'], 2, '.', ','); ?>
                                </td>
                                <td class="column" data-colname="<?= __('Created', 'restaurant-system-app'); ?>">
                                    <b><?= $order['created_at']; ?></b>
                                </td>
                                <td class="column" data-colname="<?= __('Action', 'restaurant-system-app'); ?>">
                                    <?php if ($_GET['institute_id']): ?>
                                        <a class='button button-primary'
                                            href="<?= admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id=' . $order['order_id']) ?>"><?= __('View details', 'aes'); ?></a>
                                    <?php else: ?>
                                        <a class='button button-primary'
                                            href="<?= admin_url('admin.php?page=list_admin_institutes_invoice_content&action=payment-detail&payment_id=' . $order['order_id']) ?>"><?= __('View details', 'aes'); ?></a>
                                    <?php endif; ?>
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

            <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px; display: none"
                id="tab-payments">
                <thead>
                    <tr>
                        <th scope="col" class=" manage-column column-primary">
                            <?= __('Status', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column-email">
                            <?= __('Month', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column-phone">
                            <?= __('Amount', 'restaurant-system-app'); ?>
                        </th>
                        <th scope="col" class=" manage-column column">
                            <?= __('Total orders', 'restaurant-system-app'); ?></th>
                    </tr>
                </thead>
                <tbody id="table-institutes-payment-payments">
                    <?php if (!empty($orders['orders'])) { ?>
                        <?php foreach ($orders['orders'] as $order) { ?>
                            <tr>
                                <td class="column column-primary"
                                    data-colname="<?= __('Payment ID', 'restaurant-system-app'); ?>">
                                    <?= '#' . $order['order_id']; ?>
                                    <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                </td>
                                <td class="column" data-colname="<?= __('Customer', 'restaurant-system-app'); ?>">
                                    <?= $order['customer']; ?>
                                </td>
                                <td class="column" data-colname="<?= __('Fee', 'restaurant-system-app'); ?>">
                                    <?= get_woocommerce_currency_symbol() . number_format($order['fee'], 2, '.', ','); ?>
                                </td>
                                <td class="column" data-colname="<?= __('Created', 'restaurant-system-app'); ?>">
                                    <b><?= $order['created_at']; ?></b>
                                </td>
                                <td class="column" data-colname="<?= __('Action', 'restaurant-system-app'); ?>">
                                    <?php if (isset($_GET['institute_id']) && !empty($_GET['institute_id'])): ?>
                                        <a class='button button-primary'
                                            href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=payment-detail&payment_id=' . $order['order_id']) ?>"><?= __('View details', 'aes'); ?></a>
                                    <?php else: ?>
                                        <a class='button button-primary'
                                            href="<?= admin_url('admin.php?page=list_admin_institutes_payments_content&action=payment-detail&payment_id=' . $order['order_id']) ?>"><?= __('View details', 'aes'); ?></a>
                                    <?php endif; ?>
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