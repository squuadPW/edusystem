<div class="title">
    <?= __('Select payment', 'aes'); ?>
</div>

<div class="container">
    <?php foreach ($payment_gateways as $key => $payment) { ?>
        <?php
        global $wpdb;
        $table_commissions = $wpdb->prefix . 'commissions';
        $commission = $wpdb->get_row("SELECT * FROM {$table_commissions} WHERE woocommerce_payment_id='" . $payment->id . "'");
        ?>
        <div class="card card-select-payment" data-id="<?= $payment->id; ?>">
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
                    <?= isset($commission) ? ($commission->commission > 0 ? 'The amount equivalent to ' . (isset($commission) ? ($commission->type_commission == 1 ? get_woocommerce_currency_symbol() : '') : '') . $commission->commission . (isset($commission) ? ($commission->type_commission == 2 ? '%' : '') : '')  . ' of the amount payable for bank processing costs must be added.' : 'There is no cost or commission for bank processing fees.') : 'There is no cost or commission for bank processing fees.' ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<form method="POST" action="<?= the_permalink() . '?action=select_payment'; ?>" class="form-aes" id="form-me" style="margin-top: 3rem !important">
    <div class="grid grid-cols-12 gap-4">

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country', 'form-plugin'); ?><span class="required">*</span></label>
            <select name="country" autocomplete="off" required id="country-select-step-two">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($countries as $key => $country) { ?>
                    <option value="<?= $key ?>"><?= $country; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="billing_address_1"><?= __('Street address', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="billing_address_1" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="city" autocomplete="off" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="billing_state"><?= __('State / County', 'aes'); ?><span class="required">*</span></label>
            <select name="billing_state" id="state-select-step-two">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
            </select>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="billing_postcode"><?= __('Postcode / ZIP', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="billing_postcode" autocomplete="off" required>
        </div>

        <input class="formdata capitalize" type="hidden" name="payment_method_selected" autocomplete="off" required>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center; display: none !important">
            <button type="submit" class="submit" id="buttonsave"><?= __('Send', 'aes'); ?></button>
        </div>
    </div>
</form>

<div class="grid grid-cols-12 gap-4">
    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-5 flex justify-center space-x-4">
        <a href="<?= home_url() . '?form=1'; ?>"><button class="button" id="back_home"><?= __('Back', 'aes'); ?></button></a>
        <button class="submit" id="buttonsave_secondary"><?= __('Send', 'aes'); ?></button>
    </div>
</div>