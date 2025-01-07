<div class="title">
    <?= __('Select payment', 'aes'); ?>
</div>

<form method="POST" action="<?= the_permalink() . '?action=new_applicant_me'; ?>" class="form-aes" id="form-me">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center; display: none !important">
            <button class="submit" id="buttonsave"><?= __('Send', 'aes'); ?></button>
        </div>
    </div>
</form>

<div class="container">
    <?php foreach ($payment_gateways as $key => $payment) { ?>
        <?php
        global $wpdb;
        $table_commissions = $wpdb->prefix . 'commissions';
        $commission = $wpdb->get_row("SELECT * FROM {$table_commissions} WHERE woocommerce_payment_id='" . $payment->id . "'");
        ?>
        <div class="card">
            <div class="card-payment-commission">
                <div><strong><?= strtoupper($payment->get_title()); ?></strong></div>
                <div style="font-size: 12px">100% ONLINE</div>
            </div>
            <div class="card-content">
                <div class="card-content-header">
                    <div class="card-content-commission"><?= isset($commission) ? ($commission->type_commission == 2 ? '%' : get_woocommerce_currency_symbol()) : '' ?><?= $commission->commission ?></div>
                    <div style="font-size: 12px;">Commission</div>
                </div>
                <div class="card-content-divisor"></div>
                <div style="padding: 20px; text-align: center;">
                    <?= isset($commission) ? ($commission->commission > 0 ? 'The amount equivalent to ' . (isset($commission) ? ($commission->type_commission == 1 ? get_woocommerce_currency_symbol() : '') : '') . $commission->commission . (isset($commission) ? ($commission->type_commission == 2 ? '%' : '') : '')  . ' of the amount payable for bank processing costs must be added.' : 'There are no fees or commissions for commissions for bank processing bank processing fees.') : 'There are no fees or commissions for commissions for bank processing bank processing fees.' ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div class="grid grid-cols-12 gap-4">
    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-5" style="text-align:center;">
        <button class="submit" id="buttonsave_secondary"><?= __('Send', 'aes'); ?></button>
    </div>
</div>