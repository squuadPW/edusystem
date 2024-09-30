<div class="wrap">
    <?php if (isset($period) && !empty($period)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Period Details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Period', 'aes'); ?></h2>
    <?php endif; ?>

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message']; ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php } ?>
    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php } ?>
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content'); ?>"><?= __('Back', 'aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content&action=save_period_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Period Information', 'aes'); ?></b>
                                </h3>
                                <div style="text-align: center; margin: 18px">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <input type="hidden" name="period_id" id="period_id"
                                                value="<?= $period->id; ?>">
                                            <div>
                                                <label for="status_id"><b><?= __('Active', 'aes'); ?></b></label>
                                                <input type="checkbox" name="status_id" id="status_id" value="1"
                                                    <?= ($period->status_id == 1) ? 'checked' : ''; ?>>
                                            </div>
                                        <?php else: ?>
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                            <div>
                                                <label for="status_id"><b><?= __('Active', 'aes'); ?></b></label>
                                                <input type="checkbox" name="status_id" id="status_id" value="1" checked>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Period', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="code" value="<?= ucwords($period->code); ?>">
                                            <input type="hidden" name="period_id" id="period_id"
                                                value="<?= $period->id; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Period', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="code" value="" required>
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Description', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="name" value="<?= ucwords($period->name); ?>">
                                            <input type="hidden" name="period_id" id="period_id"
                                                value="<?= $period->id; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Description', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="name" value="" required>
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Year', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="number" name="year" value="<?= $period->year; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Year', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="number" name="year" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date" value="<?= $period->start_date; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date" value="<?= $period->end_date; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Start and end dates for period cuts', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date Cut A', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_A" value="<?= $period->start_date_A; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date Cut A', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_A" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date Cut A', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_A" value="<?= $period->end_date_A; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date Cut A', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_A" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date Cut B', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_B" value="<?= $period->start_date_B; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date Cut B', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_B" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date Cut B', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_B" value="<?= $period->end_date_B; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date Cut A', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_B" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date Cut C', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_C" value="<?= $period->start_date_C; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date Cut C', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_C" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date Cut C', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_C" value="<?= $period->end_date_C; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date Cut C', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_C" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date Cut D', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_D" value="<?= $period->start_date_D; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date Cut D', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_D" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date Cut D', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_D" value="<?= $period->end_date_D; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date Cut D', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_D" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date Cut E', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_E" value="<?= $period->start_date_E; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date Cut E', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_E" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date Cut E', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_E" value="<?= $period->end_date_E; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date Cut E', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_E" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Inscriptions', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_inscriptions" value="<?= $period->start_date_inscription; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_inscriptions" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_inscriptions" value="<?= $period->end_date_inscription; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_inscriptions" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Pre-Inscriptions', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('Start Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="start_date_pre_inscriptions" value="<?= $period->start_date_pre_inscription; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Start Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="start_date_pre_inscriptions" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="input_id"><b><?= __('End Date', 'aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="date" name="end_date_pre_inscriptions" value="<?= $period->end_date_pre_inscription; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('End Date', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="date" name="end_date_pre_inscriptions" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($period) && !empty($period)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add Period', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>