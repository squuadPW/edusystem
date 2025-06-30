<div class="wrap">
    <?php if (isset($expense) && !empty($expense)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Expense Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Expense', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php 
        include(plugin_dir_path(__FILE__).'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=expenses_payroll'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=save_expense_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Expense Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="expense_id" value="<?= $expense->id ?>">

                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="motive"><b><?= __('Reason for the amount of the expense', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="motive"
                                            value="<?= $expense->motive; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="apply_to"><b><?= __('To what date does this expense apply?', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="date" name="apply_to"
                                            value="<?= $expense->apply_to; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="amount"><b><?= __('Amount', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="number" step="0.01" name="amount"
                                            value="<?= $expense->amount; ?>" required>
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($expense) && !empty($expense)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add expense', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>