<div class="wrap">
    <div class="grid-container-report" id="card-totals-sales">
        <div class="card-report-sales tooltip" title="Gross sales of orders" style="background-color: #d6ecfb;">
            <div>Gross sales of orders</div>
            <div style="margin-top: 10px"><strong id="gross"></strong></div>
        </div>
        <div class="card-report-sales tooltip"
            title="The subtraction of the institution fee, alliance fee, payment fee, system fee and the order tax is applied"
            style="background-color: #ffe0e6;">
            <div>Net Sale</div>
            <div style="margin-top: 10px"><strong id="net"></strong></div>
        </div>
        <div class="card-report-sales tooltip"
            title="Total fees for all orders. Including institution fee, alliance fee, payment fee, system fee"
            style="background-color: #fff7d4;">
            <div>Sale of fees</div>
            <div style="margin-top: 10px"><strong id="fees"></strong></div>
        </div>
        <div class="card-report-sales tooltip" title="Total taxes of all orders" style="background-color: #c791c7;">
            <div>Tax total </div>
            <div style="margin-top: 10px"><strong id="tax"></strong></div>
        </div>
        <div class="card-report-sales tooltip" title="All upcoming accounts receivable (pending quotes)" style="background-color: #c5f3c5;">
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
                <button type="button" id="update_data_chart" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_chart"
                    class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php endif; ?>
    </div>

    <div style="background-color: #ffffff; padding: 18px; border-radius: 10px; margin: 20px;">
        <div id="loading" style="text-align: center !important">
            <span class='spinner is-active' style='float:none;'></span>
        </div>
        <canvas id="myChart"></canvas>
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