<div class="wrap">
    <div style="width:100%;text-align:center;padding-top:10px;">

        <?php if (wp_is_mobile()) { ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <select id="typeFilter" name="typeFilter" autocomplete="off">
                <?php } ?>
                <option value="today"><?= __('Today', 'aes'); ?></option>
                <option value="yesterday"><?= __('yesterday', 'aes'); ?></option>
                <option value="this-week"><?= __('This week', 'aes'); ?></option>
                <option value="last-week"><?= __('Last week', 'aes'); ?></option>
                <option value="this-month" selected><?= __('This month', 'aes'); ?></option>
                <option value="last-month"><?= __('Last month', 'aes'); ?></option>
                <option value="custom"><?= __('Custom', 'aes'); ?></option>
            </select>
            <?php if (wp_is_mobile()) { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate"
                    style="display:none;width:100%;margin-bottom:5px;">
            <?php } else { ?>
                <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
            <?php } ?>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data_sales_product" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'aes'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_sales_product"
                    class="button button-primary"></span><?= __('Update data', 'aes'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Product ID', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Product', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Quantity', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Subtotal', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Discount', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Tax', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Total', 'aes'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">

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