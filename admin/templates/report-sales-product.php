<div class="wrap">
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
                <button type="button" id="update_data_sales_product" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_sales_product"
                    class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Product ID', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Product', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Quantity', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Subtotal', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Discount', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Tax', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Total', 'restaurant-system-app'); ?></th>
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