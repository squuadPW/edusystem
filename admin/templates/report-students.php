<div class="wrap">
    <div id="card-totals-sales" class="grid-container-report">
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
            <div>Total Students</div>
            <div style="margin-top: 10px"><strong id="students"></strong></div>
        </div>
    </div>
    <div style="width:100%;text-align:center;padding-top:10px;">
            <select id="academic_period">
            <option value="" selected="selected"><?= __('Select an academic period', 'aes'); ?></option>
                <?php foreach ($periods as $key => $period) { ?>
                    <option value="<?php echo $period->code; ?>" <?= ($student->academic_period == $period->code) ? 'selected' : ''; ?>>
                        <?php echo $period->name; ?>
                    </option>
                <?php } ?>
            </select>
            <select name="academic_period_cut" id="academic_period_cut">
                <option value="">Select academic period cut</option>
                <option value="A" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
                <option value="B" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
                <option value="C" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
                <option value="D" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
                <option value="E" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
            </select>
            <select id="grade">
                    <option value="" selected="selected"><?= __('Select a grade', 'aes'); ?></option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data_report_students" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_report_students"
                    class="button button-primary"></span><?= __('Update data', 'restaurant-system-app'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column"><?= __('Academic period', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Student', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column"><?= __('Student email', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Parent', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Country', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Grade', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Program', 'restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Institute', 'restaurant-system-app'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">

        </tbody>
    </table>
</div>