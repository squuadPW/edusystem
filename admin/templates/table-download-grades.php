<div id="template_certificate" style="display: none">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="margin-left: 10px">
            <?php include(plugin_dir_path(__FILE__) . 'img-logo.php'); ?>
        </div>
        <div style="text-align: center;">
            <div>United States of America</div>
            <div>American Elite School</div>
            <div>Miami, Florida</div>
        </div>
        <div style="text-align: center; margin-right: 10px">
            <div>Academic summary</div>
        </div>
    </div>

    <table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px">
        <thead>
            <tr>
                <th colspan="12" class="text-uppercase">
                    <strong>Complete Name:</strong>
                    <?= strtoupper(__($student->last_name . ' ' . $student->middle_last_name . ', ' . $student->name . ' ' . $student->middle_name, 'aes')); ?>
                </th>
            </tr>
            <tr>
                <th colspan="12" class="text-uppercase">
                    <strong>Student ID:</strong> <?= $student->id_document; ?>
                </th>
            </tr>
            <tr>
                <th colspan="12" class="text-uppercase">
                    <strong>Date issue:</strong> <?= date('m/d/Y'); ?>
                </th>
            </tr>
            <tr>
                <th colspan="12" class="text-uppercase">
                    <strong>Program:</strong> <?= get_name_program($student->program_id); ?>
                </th>
            </tr>
        </thead>
    </table>
    <br>
    <table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px">
        <thead>
            <tr>
                <th colspan="2">CODE</th>
                <th colspan="4">COURSE</th>
                <th colspan="1">CH</th>
                <th colspan="1">0-100</th>
                <th colspan="1">0-4</th>
                <th colspan="3">PERIOD</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (json_decode($projection->projection) as $key => $projection_for) { ?>
                <?php 
                    $subject = get_subject_details($projection_for->subject_id);
                    if ($projection_for->is_completed) {
                        $period = get_period_details_code($projection_for->code_period); 
                    }
                ?>
                <tr>
                    <td colspan="2"><?= $projection_for->code_subject ?></td>
                    <td colspan="4"><?= $projection_for->subject ?>     <?= $projection_for->is_elective ? '(ELECTIVE)' : '' ?>
                    </td>
                    <td colspan="1"><?= $subject->type != 'equivalence' ? $projection_for->hc : 'TR' ?></td>
                    <td colspan="1"><?= $projection_for->calification ?></td>
                    <td colspan="1"><?= $subject->type != 'equivalence' ? get_calc_note($projection_for->calification) : 'TR' ?></td>
                    <td colspan="3"><?= $period->name ?? 'N/A' ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>