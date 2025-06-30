<div class="wrap">
    <?php if (isset($period) && !empty($period)): ?>
        <h2><?= __('Period Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2><?= __('Add Period', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div class="back-button-container">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content&action=save_period_details'); ?>">
                            <div>
                                <h3 class="form-section-title">
                                    <b><?= __('Period Information', 'edusystem'); ?></b>
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <input type="hidden" name="period_id" id="period_id"
                                                value="<?= $period->id; ?>">
                                            <label for="status_id"><b><?= __('Active', 'edusystem'); ?></b></label>
                                            <input type="checkbox" name="status_id" id="status_id" value="1"
                                                <?= ($period->status_id == 1) ? 'checked' : ''; ?>>
                                        <?php else: ?>
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                            <label for="status_id"><b><?= __('Active', 'edusystem'); ?></b></label>
                                            <input type="checkbox" name="status_id" id="status_id" value="1" checked>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="code"><b><?= __('Period', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="text" name="code" id="code"
                                                value="<?= esc_attr(ucwords($period->code)); ?>">
                                            <input type="hidden" name="old_code" id="old_code"
                                                value="<?= esc_attr($period->code); ?>">
                                        <?php else: ?>
                                            <label for="code"><b><?= __('Period', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="code" id="code" value="" required>
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                            <input type="hidden" name="old_code" id="old_code" value="">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="name"><b><?= __('Description', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="text" name="name" id="name"
                                                value="<?= esc_attr(ucwords($period->name)); ?>">
                                        <?php else: ?>
                                            <label for="name"><b><?= __('Description', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="year"><b><?= __('Year', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="number" name="year" id="year"
                                                value="<?= esc_attr($period->year); ?>">
                                        <?php else: ?>
                                            <label for="year"><b><?= __('Year', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="year" id="year" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date"><b><?= __('Start Date', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date" id="start_date"
                                                value="<?= esc_attr($period->start_date); ?>">
                                        <?php else: ?>
                                            <label for="start_date"><b><?= __('Start Date', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date" id="start_date" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date"><b><?= __('End Date', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date" id="end_date"
                                                value="<?= esc_attr($period->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date"><b><?= __('End Date', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date" id="end_date" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3 class="form-section-title">
                                    <b><?= __('Start and end dates for period cuts', 'edusystem'); ?></b>
                                </h3>
                                <div class="form-grid"
                                    style="border-bottom: 1px solid rgba(128, 128, 128, 0.3607843137); padding-bottom: 20px;">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date_A"><b><?= __('Start Date Cut A', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date_A" id="start_date_A"
                                                value="<?= esc_attr($cuts[0]->start_date); ?>">
                                        <?php else: ?>
                                            <label
                                                for="start_date_A"><b><?= __('Start Date Cut A', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date_A" id="start_date_A" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date_A"><b><?= __('End Date Cut A', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date_A" id="end_date_A"
                                                value="<?= esc_attr($cuts[0]->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date_A"><b><?= __('End Date Cut A', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date_A" id="end_date_A" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="max_date_A"><b><?= __('Max Date Cut A', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="max_date_A" id="max_date_A"
                                                value="<?= esc_attr($cuts[0]->max_date); ?>">
                                        <?php else: ?>
                                            <label for="max_date_A"><b><?= __('Max Date Cut A', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="max_date_A" id="max_date_A" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-grid"
                                    style="border-bottom: 1px solid rgba(128, 128, 128, 0.3607843137); padding-bottom: 20px;">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date_B"><b><?= __('Start Date Cut B', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date_B" id="start_date_B"
                                                value="<?= esc_attr($cuts[1]->start_date); ?>">
                                        <?php else: ?>
                                            <label
                                                for="start_date_B"><b><?= __('Start Date Cut B', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date_B" id="start_date_B" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date_B"><b><?= __('End Date Cut B', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date_B" id="end_date_B"
                                                value="<?= esc_attr($cuts[1]->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date_B"><b><?= __('End Date Cut B', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date_B" id="end_date_B" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="max_date_B"><b><?= __('Max Date Cut B', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="max_date_B" id="max_date_B"
                                                value="<?= esc_attr($cuts[1]->max_date); ?>">
                                        <?php else: ?>
                                            <label for="max_date_B"><b><?= __('Max Date Cut B', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="max_date_B" id="max_date_B" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-grid"
                                    style="border-bottom: 1px solid rgba(128, 128, 128, 0.3607843137); padding-bottom: 20px;">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date_C"><b><?= __('Start Date Cut C', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date_C" id="start_date_C"
                                                value="<?= esc_attr($cuts[2]->start_date); ?>">
                                        <?php else: ?>
                                            <label
                                                for="start_date_C"><b><?= __('Start Date Cut C', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date_C" id="start_date_C" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date_C"><b><?= __('End Date Cut C', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date_C" id="end_date_C"
                                                value="<?= esc_attr($cuts[2]->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date_C"><b><?= __('End Date Cut C', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date_C" id="end_date_C" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="max_date_C"><b><?= __('Max Date Cut C', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="max_date_C" id="max_date_C"
                                                value="<?= esc_attr($cuts[2]->max_date); ?>">
                                        <?php else: ?>
                                            <label for="max_date_C"><b><?= __('Max Date Cut C', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="max_date_C" id="max_date_C" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-grid"
                                    style="border-bottom: 1px solid rgba(128, 128, 128, 0.3607843137); padding-bottom: 20px;">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date_D"><b><?= __('Start Date Cut D', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date_D" id="start_date_D"
                                                value="<?= esc_attr($cuts[3]->start_date); ?>">
                                        <?php else: ?>
                                            <label
                                                for="start_date_D"><b><?= __('Start Date Cut D', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date_D" id="start_date_D" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date_D"><b><?= __('End Date Cut D', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date_D" id="end_date_D"
                                                value="<?= esc_attr($cuts[3]->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date_D"><b><?= __('End Date Cut D', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date_D" id="end_date_D" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="max_date_D"><b><?= __('Max Date Cut D', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="max_date_D" id="max_date_D"
                                                value="<?= esc_attr($cuts[3]->max_date); ?>">
                                        <?php else: ?>
                                            <label for="max_date_D"><b><?= __('Max Date Cut D', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="max_date_D" id="max_date_D" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-grid"
                                    style="border-bottom: 1px solid rgba(128, 128, 128, 0.3607843137); padding-bottom: 20px;">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="start_date_E"><b><?= __('Start Date Cut E', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="start_date_E" id="start_date_E"
                                                value="<?= esc_attr($cuts[4]->start_date); ?>">
                                        <?php else: ?>
                                            <label
                                                for="start_date_E"><b><?= __('Start Date Cut E', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date_E" id="start_date_E" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="end_date_E"><b><?= __('End Date Cut E', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="end_date_E" id="end_date_E"
                                                value="<?= esc_attr($cuts[4]->end_date); ?>">
                                        <?php else: ?>
                                            <label for="end_date_E"><b><?= __('End Date Cut E', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date_E" id="end_date_E" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="max_date_E"><b><?= __('Max Date Cut E', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="date" name="max_date_E" id="max_date_E"
                                                value="<?= esc_attr($cuts[4]->max_date); ?>">
                                        <?php else: ?>
                                            <label for="max_date_E"><b><?= __('Max Date Cut E', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="max_date_E" id="max_date_E" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3 class="form-section-title">
                                    <b><?= __('Next academic period', 'edusystem'); ?></b>
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field-custom">
                                        <?php if (isset($period) && !empty($period)): ?>
                                            <label
                                                for="code_next"><b><?= __('Next period code', 'edusystem'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label>
                                            <input type="text" name="code_next" id="code_next"
                                                value="<?= esc_attr(ucwords($period->code_next)); ?>">
                                        <?php else: ?>
                                            <label for="code_next"><b><?= __('Next period code', 'edusystem'); ?></b><span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="code_next" id="code_next" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-submit-container">
                                    <input type="submit" name="submit" id="submit" class="button button-primary"
                                        value="<?= __('Save Period', 'edusystem'); ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>