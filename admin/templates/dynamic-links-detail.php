<?php
$selected_programs = [];
$selected_plans = [];
if (isset($dynamic_link) && !empty($dynamic_link)) {
    $selected_programs = array_filter(array_map('trim', explode(',', (string) $dynamic_link->program_identificator)), 'strlen');
    $selected_plans = array_filter(array_map('trim', explode(',', (string) $dynamic_link->payment_plan_identificator)), 'strlen');
}
$selected_plan_for_details = count($selected_plans) === 1 ? $selected_plans[0] : '';
?>

<script>
    window.initialPlansData = <?= json_encode($payment_plans); ?>;
    window.selectedSubprogramId = "<?= isset($dynamic_link->subprogram_identificator) ? $dynamic_link->subprogram_identificator : ''; ?>";
    window.selectedProgramIds = <?= json_encode(array_values($selected_programs)); ?>;
    window.selectedPlanIds = <?= json_encode(array_values($selected_plans)); ?>;
</script>

<?php if (!$multiple_accounts) { ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modeSelect = document.getElementById("accounts-mode");
            const feeWrapper = document.getElementById("fee_payment_completed_wrapper");
            const sameAccountHidden = document.getElementById("same_account_hidden");

            if (!modeSelect || !feeWrapper || !sameAccountHidden) {
                return;
            }

            const toggleFeeMode = () => {
                const isSeparate = modeSelect.value === "separate";
                feeWrapper.style.display = isSeparate ? "block" : "none";
                sameAccountHidden.disabled = isSeparate;
            };

            toggleFeeMode();
            modeSelect.addEventListener("change", toggleFeeMode);
        });
    </script>
<?php } ?>

<div class="wrap">
    <?php if (isset($dynamic_link) && !empty($dynamic_link)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Payment link Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Payment link', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content&action=save_dynamic_link_details'); ?>"
                            enctype="multipart/form-data">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Student Information (optional)', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="dynamic_link_id" value="<?= $dynamic_link->id ?>">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="type_document"><b><?= __('Type document', 'edusystem'); ?></b></label>
                                        <select name="type_document" autocomplete="off">
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                            <option value="passport" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'edusystem'); ?></option>
                                            <option value="identification_document" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'edusystem'); ?></option>
                                            <option value="ssn" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SSN', 'edusystem'); ?></option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="id_document"><b><?= __('ID document', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="id_document"
                                            value="<?= $dynamic_link->id_document; ?>">
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('First name', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="name"
                                            value="<?= $dynamic_link->name; ?>">
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="last_name"><b><?= __('First surname', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="last_name"
                                            value="<?= $dynamic_link->last_name; ?>">
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="email"><b><?= __('Email', 'edusystem'); ?></b></label><br>
                                        <input type="email" name="email"
                                            value="<?= $dynamic_link->email; ?>">
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Link Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="program_identificator"><b><?= __('Program', 'edusystem'); ?></b><span class="required">*</span></label>
                                        <select name="program_identificator[]" id="program-identificator" autocomplete="off" multiple required>
                                            <?php foreach ($programs as $program): ?>
                                                <?php $is_selected = in_array($program->identificator, $selected_programs); ?>
                                                <option value="<?= $program->identificator; ?>" <?= $is_selected ? 'selected' : ''; ?>><?= $program->name; ?> (<?= $program->description; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400; <?= empty($payment_plans) ? 'display: none;' : ''; ?>" class="space-offer" id="scholarship-element">
                                        <label for="payment_plan_identificator"><b><?= __('Scholarship', 'edusystem'); ?></b><span class="required">*</span></label>
                                        <select name="payment_plan_identificator[]" id="payment-plan-identificator" autocomplete="off" multiple required>
                                            <?php if (!empty($payment_plans_grouped)): ?>
                                                <?php foreach ($payment_plans_grouped as $group): ?>
                                                    <optgroup label="<?= esc_attr($group['program']['name'] ?? __('Program', 'edusystem')); ?>">
                                                        <?php foreach ($group['plans'] as $payment_plan): ?>
                                                            <?php $is_selected = in_array($payment_plan['plan']->identificator, $selected_plans, true); ?>
                                                            <option value="<?= $payment_plan['plan']->identificator; ?>" <?= $is_selected ? 'selected' : ''; ?>><?= $payment_plan['plan']->name; ?> (<?= $payment_plan['plan']->description; ?>) - <?= $payment_plan['plan']->total_price ? ($payment_plan['plan']->currency ? $payment_plan['plan']->currency : "$") . "" . $payment_plan['plan']->total_price : "0"; ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <?php foreach ($payment_plans as $payment_plan): ?>
                                                    <?php $is_selected = in_array($payment_plan['plan']->identificator, $selected_plans, true); ?>
                                                    <option value="<?= $payment_plan['plan']->identificator; ?>" <?= $is_selected ? 'selected' : ''; ?>><?= $payment_plan['plan']->name; ?> (<?= $payment_plan['plan']->description; ?>) - <?= $payment_plan['plan']->total_price ? ($payment_plan['plan']->currency ? $payment_plan['plan']->currency : "$") . "" . $payment_plan['plan']->total_price : "0"; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400; display: none;" class="space-offer" id="subprogram-element">
                                        <label for="subprogram_id"><b><?= __('Subprogram / Level', 'edusystem'); ?></b></label>
                                        <select name="subprogram_id" id="subprogram-id" autocomplete="off">
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400; <?= empty($payment_plans) ? 'display: none;' : ''; ?>" class="space-offer" id="details-payment-plan-element">
                                        <label for="details-payment-plan"><b><?= __('Details', 'edusystem'); ?></b></label>
                                        <div id="details-payment-plan">
                                            <?php foreach ($payment_plans as $payment_plan): ?>
                                                <?php if ($payment_plan['plan']->identificator == $selected_plan_for_details) {
                                                    $currency = $payment_plan['plan']->currency ? $payment_plan['plan']->currency : get_woocommerce_currency_symbol();
                                                ?>
                                                    <div style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                                                        <p><strong><?= __('Name:', 'edusystem') ?></strong> <?= $payment_plan['plan']->name; ?></p>
                                                        <p><strong><?= __('Description:', 'edusystem') ?></strong> <?= $payment_plan['plan']->description; ?></p>
                                                        <p><strong><?= __('Regular Price:', 'edusystem') ?></strong> <?= $currency . $payment_plan['plan']->total_price; ?></p>
                                                    </div>

                                                    <label><b><?= __('Fees', 'edusystem'); ?></b></label>
                                                    <?php foreach ($payment_plan['fees'] as $fee): ?>
                                                        <p style="margin-left:10px; font-size: 0.9em;">• <?= $fee->name; ?> - <?= $fee->currency ? $fee->currency : $currency . $fee->price; ?></p>
                                                    <?php endforeach; ?>

                                                    <label style="margin-top:10px; display:block;"><b><?= __('Payment Options', 'edusystem'); ?></b></label>
                                                    <?php foreach ($payment_plan['quote_rules'] as $quote):
                                                        $qty = (int)$quote->quotas_quantity;
                                                        $freq_val = (int)$quote->frequency_value;

                                                        // Mapeo de Starts a texto natural
                                                        $starts_map = [
                                                            'registration'    => __('By registering', 'edusystem'),
                                                            'academic_period' => __('At the beginning of the academic period', 'edusystem')
                                                        ];
                                                        $starts_text = $starts_map[$quote->start_charging] ?? $quote->start_charging;

                                                        $freq_text = ($freq_val === 0 || $qty === 1)
                                                            ? __('One-time payment', 'edusystem')
                                                            : sprintf(__('Every %d %s%s', 'edusystem'), $freq_val, $quote->type_frequency, ($freq_val > 1 ? 's' : ''));

                                                        $installment_label = ($qty === 1) ? __('Single installment', 'edusystem') : $qty . ' ' . __('Installments', 'edusystem');
                                                        $amount_label = ($qty === 1) ? __('Payment:', 'edusystem') : __('Installment:', 'edusystem');

                                                        $initial_sale = ($quote->initial_payment_sale === '' || $quote->initial_payment_sale === null) ? $quote->initial_payment : $quote->initial_payment_sale;
                                                        $quote_sale = ($quote->quote_price_sale === '' || $quote->quote_price_sale === null) ? $quote->quote_price : $quote->quote_price_sale;
                                                        $final_sale = ($quote->final_payment_sale === '' || $quote->final_payment_sale === null) ? $quote->final_payment : $quote->final_payment_sale;
                                                    ?>
                                                        <div style="background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px; margin: 10px 0;">
                                                            <div style="margin-bottom: 5px;"><strong><?= esc_html($quote->name); ?></strong> (<?= $installment_label; ?>)</div>
                                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 8px; font-size: 11px; line-height: 1.2;">
                                                                <div><strong><?= __('Frequency:', 'edusystem') ?></strong> <?= $freq_text; ?></div>
                                                                <div><strong><?= __('Starts:', 'edusystem') ?></strong> <?= esc_html($starts_text); ?></div>

                                                                <div>
                                                                    <strong><?= __('Initial:', 'edusystem') ?></strong> <?= $currency . $initial_sale ?>
                                                                    <?php if ($initial_sale != $quote->initial_payment && $quote->initial_payment_sale !== '' && $quote->initial_payment_sale !== null): ?>
                                                                        <span style="text-decoration:line-through; color: #999;"><?= $currency . $quote->initial_payment ?></span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <div>
                                                                    <strong><?= $amount_label ?></strong> <?= $currency . $quote_sale ?>
                                                                    <?php if ($quote_sale != $quote->quote_price && $quote->quote_price_sale !== '' && $quote->quote_price_sale !== null): ?>
                                                                        <span style="text-decoration:line-through; color: #999;"><?= $currency . $quote->quote_price ?></span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <div>
                                                                    <strong><?= __('Final:', 'edusystem') ?></strong> <?= $currency . $final_sale ?>
                                                                    <?php if ($final_sale != $quote->final_payment && $quote->final_payment_sale !== '' && $quote->final_payment_sale !== null): ?>
                                                                        <span style="text-decoration:line-through; color: #999;"><?= $currency . $quote->final_payment ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <?php
                                        // Asume que $current_user está disponible y tiene los roles cargados
                                        if (function_exists('wp_get_current_user')) {
                                            $current_user = wp_get_current_user();
                                            $is_manager = in_array('manager', (array) $current_user->roles);
                                        } else {
                                            $is_manager = false;
                                        }
                                        ?>
                                        <?php if (!$is_manager): ?>
                                            <label for="payment_plan_identificator"><b><?= __('Manager', 'edusystem'); ?></b><span class="required">*</span></label>
                                            <select name="manager_id" autocomplete="off" required>
                                                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                                <?php foreach ($managers as $manager): ?>
                                                    <option value="<?= esc_attr($manager->ID) ?>"
                                                        <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->manager_id == $manager->ID) ? 'selected' : ''; ?>>
                                                        <?= esc_html($manager->first_name) ?> <?= esc_html($manager->last_name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="hidden" name="manager_id" value="<?= esc_attr($current_user->ID); ?>">
                                        <?php endif; ?>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="coupon_complete"><b><?= __('Coupon complete', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="coupon_complete" value="<?= esc_attr($dynamic_link->coupon_complete ?? ''); ?>">
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="coupon_credit"><b><?= __('Coupon credit', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="coupon_credit" value="<?= esc_attr($dynamic_link->coupon_credit ?? ''); ?>">
                                    </div>

                                    <?php
                                    $accounts_mode = 'together';
                                    if (isset($dynamic_link) && !empty($dynamic_link)) {
                                        if (isset($dynamic_link->same_account)) {
                                            $accounts_mode = (int) $dynamic_link->same_account === 1 ? 'together' : 'separate';
                                        } elseif (isset($dynamic_link->fee_payment_completed) && (int) $dynamic_link->fee_payment_completed === 1) {
                                            $accounts_mode = 'separate';
                                        }
                                    }
                                    ?>

                                    <?php if (!$multiple_accounts) { ?>
                                        <div style="font-weight:400;" class="space-offer">
                                            <label for="accounts-mode"><b><?= __('Program and fee setup', 'edusystem'); ?></b><span class="required">*</span></label>
                                            <select name="accounts_mode" id="accounts-mode" autocomplete="off" required>
                                                <option value="together" <?= $accounts_mode === 'together' ? 'selected' : ''; ?>>
                                                    <?= __('Program and fee together', 'edusystem'); ?>
                                                </option>
                                                <option value="separate" <?= $accounts_mode === 'separate' ? 'selected' : ''; ?>>
                                                    <?= __('Program and fee separated', 'edusystem'); ?>
                                                </option>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <?php if ($multiple_accounts) { ?>
                                        <div style="font-weight:400;" class="space-offer">
                                            <input type="checkbox" id="fee_payment_completed" style="width: auto !important;" name="fee_payment_completed" value="1" <?= (isset($dynamic_link) && $dynamic_link->fee_payment_completed == 1) ? 'checked' : ''; ?>>
                                            <label for="fee_payment_completed"><b><?= __('Payment Fee Complete', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        </div>
                                    <?php } else { ?>
                                        <input type="hidden" name="same_account" id="same_account_hidden" value="1">
                                        <div style="font-weight:400; display:none;" class="space-offer" id="fee_payment_completed_wrapper">
                                            <input type="checkbox" id="fee_payment_completed" style="width: auto !important;" name="fee_payment_completed" value="1" <?= (isset($dynamic_link) && $dynamic_link->fee_payment_completed == 1) ? 'checked' : ''; ?>>
                                            <label for="fee_payment_completed"><b><?= __('Payment Fee Complete', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                <?php if ($dynamic_link) {
                                    $dynamic_link_token = isset($dynamic_link->link) ? $dynamic_link->link : '';
                                    $dynamic_link_url = site_url('/registration-link?token=' . $dynamic_link_token);
                                ?>
                                    <button type="button" onclick="copyToClipboard('<?= $dynamic_link_url ?>', this)" class="button button-secondary"><?= __('Copy link', 'edusystem'); ?></button>
                                <?php } ?>
                                <?php if ($dynamic_link && $dynamic_link->email) { ?>
                                    <button onclick='return confirm("Are you sure?");' type="submit"
                                        class="button button-success" name="save_and_send_email" value="1"><?= __('Save and send email', 'edusystem'); ?></button>
                                <?php } ?>
                                <button type="submit"
                                    class="button button-primary" name="just_save" value="1"><?= __('Just save', 'edusystem'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($dynamic_link)) : ?>
        <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
            <div id="postbox-container-1" style="width:100% !important;">
                <div id="normal-sortables">
                    <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                        <div class="inside">
                            <h3 style="margin-top:20px; text-align:center; border-bottom: 1px solid #8080805c;">
                                <b><?= __('Email Send History', 'edusystem'); ?></b>
                            </h3>
                            <?php if (!empty($dynamic_links_email_log) && is_array($dynamic_links_email_log)): ?>
                                <div style="overflow-x:auto; margin-top: 15px;">
                                    <table class="wp-list-table widefat fixed striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th><?= __('Date', 'edusystem'); ?></th>
                                                <th><?= __('Email', 'edusystem'); ?></th>
                                                <th><?= __('Send by', 'edusystem'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dynamic_links_email_log as $log): ?>
                                                <?php
                                                $send_by_user = get_user_by('id', $log->created_by);
                                                ?>
                                                <tr>
                                                    <td><?= esc_html(isset($log->created_at) ? $log->created_at : ''); ?></td>
                                                    <td><?= esc_html(isset($log->email) ? $log->email : ''); ?></td>
                                                    <td><?= esc_html($send_by_user ? $send_by_user->first_name . ' ' . $send_by_user->last_name : ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p style="text-align:center; margin-top:15px;">
                                    <?= __('No email send records found.', 'edusystem'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>