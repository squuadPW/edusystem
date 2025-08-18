<div class="wrap">
    <div id="card-totals-sales" class="grid-container-report">
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
            <div>Total Students</div>
            <div style="margin-top: 10px"><strong id="students"></strong></div>
        </div>
    </div>
    <div style="width:100%;text-align:center;padding-top:10px;">
            <select id="academic_period">
            <option value="" selected="selected"><?= __('Select an academic period', 'edusystem'); ?></option>
                <?php foreach ($periods as $key => $period) { ?>
                    <option value="<?php echo $period->code; ?>" <?= ($student->academic_period == $period->code) ? 'selected' : ''; ?>>
                        <?php echo $period->name; ?>
                    </option>
                <?php } ?>
            </select>
            <select name="academic_period_cut" id="academic_period_cut">
                <option value=""><?= __('Select academic period cut', 'edusystem') ?></option>
                <option value="A" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'A') ? 'selected' : '') : ''; ?>>A</option>
                <option value="B" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'B') ? 'selected' : '') : ''; ?>>B</option>
                <option value="C" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'C') ? 'selected' : '') : ''; ?>>C</option>
                <option value="D" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'D') ? 'selected' : '') : ''; ?>>D</option>
                <option value="E" <?= !empty($_GET['academic_period_cut']) ? (($_GET['academic_period_cut'] == 'E') ? 'selected' : '') : ''; ?>>E</option>
            </select>
            <!-- <select id="grade">
                    <option value="" selected="selected"><?= __('Select a grade', 'edusystem'); ?></option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                    <?php endforeach; ?>
                </select> -->
            <?php if (wp_is_mobile()): ?>
                <button type="button" id="update_data_report_students" class="button button-primary"
                    style="width:100%;"></span><?= __('Update data', 'edusystem'); ?></button>
                <button type="button" id="export_excel_students" class="button button-success"
                    style="width:100%;"></span><?= __('Export excel', 'edusystem'); ?></button>
            <?php else: ?>
                <button type="button" id="update_data_report_students"
                    class="button button-primary"></span><?= __('Update data', 'edusystem'); ?></button>
                <button type="button" id="export_excel_students"
                    class="button button-success"></span><?= __('Export excel', 'edusystem'); ?></button>
            <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <strong>The filter of this report applies to students with subjects enrolled within the period and cutoff consulted.</strong>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Student', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column"><?= __('Student document', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column"><?= __('Student email', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Parent', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Parent email', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Country', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Grade', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Program', 'edusystem'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Institute', 'edusystem'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">

        </tbody>
    </table>
</div>