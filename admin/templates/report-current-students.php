<div class="wrap">
    <div id="card-totals-sales" class="grid-container-report-2">
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #97d5ff;">
            <div>Students currently enrolled</div>
            <div style="margin-top: 10px"><strong id="current-students"></strong></div>
        </div>
        <div class="card-report-sales tooltip" title="All orders" style="background-color: #ff9797;">
            <div>Students not currently enrolled</div>
            <div style="margin-top: 10px"><strong id="not-current-students"></strong></div>
        </div>
    </div>
    <div style="width:100%;text-align:center;padding-top:10px;">
    <?php if (wp_is_mobile()): ?>
                <input type="hidden" id="update_data_report_current_students"></input>
                <button type="button" id="export_excel_students" class="button button-success"
                    style="width:100%;"></span><?= __('Export excel', 'aes'); ?></button>
            <?php else: ?>
                <input type="hidden" id="update_data_report_current_students"></input>
                <button type="button" id="export_excel_students"
                    class="button button-success"></span><?= __('Export excel', 'aes'); ?></button>
            <?php endif; ?>
    </div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column"><?= __('Academic period - cut', 'aes'); ?></th>
                <th scope="col" class=" manage-column column-primary"><?= __('Student', 'aes'); ?></th>
                <th scope="col" class=" manage-column"><?= __('Subjects', 'aes'); ?></th>
            </tr>
        </thead>
        <tbody id="table-current-student">

        </tbody>
    </table>
</div>