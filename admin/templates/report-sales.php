<div class="wrap">
    <div style="width:100%;text-align:center;padding-top:10px;">
        <?php if (wp_is_mobile()) { ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <select id="typeFilter" name="typeFilter" autocomplete="off">
                <?php } ?>
                <option value="today"><?= __('Today', 'edusystem'); ?></option>
                <option value="yesterday"><?= __('yesterday', 'edusystem'); ?></option>
                <option value="this-week"><?= __('This week', 'edusystem'); ?></option>
                <option value="last-week"><?= __('Last week', 'edusystem'); ?></option>
                <option value="this-month" selected><?= __('This month', 'edusystem'); ?></option>
                <option value="last-month"><?= __('Last month', 'edusystem'); ?></option>
                <option value="custom"><?= __('Custom', 'edusystem'); ?></option>
            </select>
            <?php if (wp_is_mobile()) { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                    style="display:none;width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
            <?php } ?>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'edusystem'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data"
                    class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
            <?php endif; ?>
    </div>
    <div id="summary_loading">
        <div id="loading" style="text-align: center !important">
            <span class='spinner is-active' style='float:none;'></span>
        </div>
    </div>
    <div id="summary_content" style="display: none">
        <div class="grid-container-report">
            <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
                <div>Total orders</div>
                <div style="margin-top: 10px"><strong id="orders"></strong></div>
            </div>
        </div>
        <div class="grid-container-report-4">
            <div style="background-color: #d6ecfb; display: none" class="card-report-sales tooltip"
                title="Gross sales of orders">
                <div>Gross sales of orders</div>
                <div style="margin-top: 10px"><strong id="gross"></strong></div>
            </div>
            <!-- <div style="background-color: #59e58291;" class="card-report-sales tooltip" title="Gross sales of orders">
            <div>Discount</div>
            <div style="margin-top: 10px"><strong id="discount"></strong></div>
        </div> -->
            <!-- <div style="background-color: #d10c0c42;" class="card-report-sales tooltip" title="Gross sales of orders">
            <div>Adjusted gross</div>
            <div style="margin-top: 10px"><strong id="adjusted_gross"></strong></div>
        </div> -->
            <!-- <div style="background-color: #ffe0e6;" class="card-report-sales tooltip"
            title="The subtraction of the institution fee, alliance fee, payment fee, system fee and the order tax is applied">
            <div>Net Sale</div>
            <div style="margin-top: 10px"><strong id="net"></strong></div>
        </div> -->
            <!-- <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for alliances">
            <div>Alliances fee</div>
            <div style="margin-top: 10px"><strong id="a_fee"></strong></div>
        </div> -->
            <!-- <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for institutes">
            <div>Institutes fee</div>
            <div style="margin-top: 10px"><strong id="i_fee"></strong></div>
        </div> -->
            <!-- <div style="background-color: #fff7d4;" class="card-report-sales tooltip" title="Total fees for payments">
            <div>Payments fee</div>
            <div style="margin-top: 10px"><strong id="p_fees"></strong></div>
        </div> -->
            <!-- <div style="background-color: #fff7d4;" class="card-report-sales tooltip"
            title="Total fees for system (Edusof)">
            <div>Edusof fee</div>
            <div style="margin-top: 10px"><strong id="e_fees"></strong></div>
        </div> -->
            <!-- <div style="background-color: #c791c7;" class="card-report-sales tooltip" title="Total taxes of all orders">
            <div>Tax total </div>
            <div style="margin-top: 10px"><strong id="tax"></strong></div>
        </div> -->
            <!-- <div style="background-color: #c5f3c5;" class="card-report-sales tooltip"
            title="All upcoming accounts receivable (pending quotes)">
            <div>Accounts receivable </div>
            <div style="margin-top: 10px"><strong id="receivable"></strong></div>
        </div> -->
        </div>
        <div style="text-align: center !important; display: none">
            <span
                style="border-bottom: 1px solid gray; width: 100% !important; font-weight: 600; padding: 10px; margin: 10px; font-size: 18px;">Payment
                methods used by users</span>
            <div id="card-totals-sales" class="grid-container-report-4"></div>
        </div>
        <div style="background-color: #ffffff; padding: 18px; border-radius: 10px; margin: 20px 0px;">
            <canvas id="myChart"></canvas>
        </div>

        <h2 style="margin-top: 18px"><?= __('Revenues', 'edusystem'); ?></h2>
        <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class=" manage-column column-primary"><?= __('Motive', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column-amount"><?= __('Amount', 'edusystem'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Adjusted gross</td>
                    <td id="adjusted_gross" class="tooltip" title="Gross sales of orders"></td>
                </tr>
                <tr>
                    <td>Net sale</td>
                    <td id="net" class="tooltip"
                        title="The subtraction of the institution fee, alliance fee, payment fee, system fee and the order tax is applied">
                    </td>
                </tr>
                <tr>
                    <td>Accounts receivable</td>
                    <td id="receivable" class="tooltip" title="All upcoming accounts receivable (pending quotes)"></td>
                </tr>
            </tbody>
        </table>

        <h2 style="margin-top: 18px"><?= __('Expenses', 'edusystem'); ?></h2>
        <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class=" manage-column column-primary"><?= __('Motive', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column-amount"><?= __('Amount', 'edusystem'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Discount</td>
                    <td id="discount" class="tooltip" title="Gross sales of orders"></td>
                </tr>
                <tr>
                    <td>Alliances fee</td>
                    <td id="a_fee" class="tooltip" title="Total fees for alliances"></td>
                </tr>
                <tr>
                    <td>Institutes fee</td>
                    <td id="i_fee" class="tooltip" title="Total fees for institutes"></td>
                </tr>
                <tr>
                    <td>Payments fee</td>
                    <td id="p_fees" class="tooltip" title="Total fees for payments"></td>
                </tr>
                <tr>
                    <td>EduSof fees</td>
                    <td id="e_fees" class="tooltip" title="Total fees for system (Edusof)"></td>
                </tr>
                <tr>
                    <td>Tax total</td>
                    <td id="tax" class="tooltip" title="Total taxes of all orders"></td>
                </tr>
            </tbody>
        </table>

        <h2 style="margin-top: 18px"><?= __('List of orders', 'edusystem'); ?></h2>
        <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class=" manage-column column-primary"><?= __('Payment ID', 'edusystem'); ?>
                    </th>
                    <th scope="col" class=" manage-column column-email"><?= __('Parent', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column-email"><?= __('Student', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column-email"><?= __('Total', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column"><?= __('Created', 'edusystem'); ?></th>
                    <th scope="col" class=" manage-column column"><?= __('Actions', 'edusystem'); ?></th>
                </tr>
            </thead>
            <tbody id="table-institutes-payment">

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