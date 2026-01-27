<div class="wrap">
    <?php if (isset($program) && !empty($program)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Payment method for payment plan', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Payment method for payment plan', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER'] ?? admin_url("/admin.php?page=add_admin_form_payments_plans_content") ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer container-programs" style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_payments_plans_content&action=save_program_details_payment_method'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Payment method information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">

                                    <input type="hidden" name="program_id" value="<?= $program->id ?>">

                                    <div style="font-weight:400; margin-bottom: 10px;">
                                        <div>
                                            <input style="width: auto !important;" type="checkbox" name="fee_inscription" id="fee_inscription">
                                            <label for="fee_inscription"><b><?= __('Fee inscription paied', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="payment_method"><b><?= __('Payment method', 'edusystem'); ?></b><span class="required">*</span></label><br>
                                        <select name="payment_method" id="payment-method" autocomplete="off" required>
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                            <?php foreach ($available_gateways as $id => $gateway): ?>
                                                <option value="<?= $id; ?>"><?= $gateway->get_title(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="payment_method"><b><?= __('Account identifier', 'edusystem'); ?></b></label><br>
                                        <input type="text" name="payment_method_identifier">
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($program) && !empty($program)): ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add program', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>