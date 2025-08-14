<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php include(plugin_dir_path(__FILE__) . 'document-export-grades-subject.php'); ?>

<div id="template_subject" style=" margin-top: 30px;">
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
            <div>Score Record P.A.: <?= strtoupper($projections_result['academic_period']->name) ?></div>
            <div>Cod. Val.: </div>
        </div>
    </div>

    <table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px">
        <thead>
            <tr>
                <th colspan="8"><strong>CODE:</strong> <?= $projections_result['subject']->code_subject ?></th>
                <th colspan="4"><strong>DATE:</strong> <?= date("Y/m/d") ?></th>
            </tr>
            <tr>
                <th colspan="8"><strong>COURSE:</strong> <?= strtoupper($projections_result['subject']->name) ?></th>
                <th colspan="4"><strong>SECTION:</strong> <?= $projections_result['academic_period_cut'] ?></th>
            </tr>
            <tr>
                <th colspan="8"><strong>PROFESSOR:</strong>
                    <?= isset($projections_result['teacher']) ? (strtoupper($projections_result['teacher']->name) . ' ' . strtoupper($projections_result['teacher']->middle_name) . ' ' . strtoupper($projections_result['teacher']->last_name) . ' ' . strtoupper($projections_result['teacher']->middle_last_name)) : 'N/A' ?>
                </th>
                <th colspan="4"><strong>ID:</strong>
                    <?= isset($projections_result['teacher']) ? (strtoupper($projections_result['teacher']->id_document)) : 'N/A' ?>
                </th>
            </tr>
        </thead>
    </table>
    <br>
    <table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px">
        <thead>
            <tr>
                <th colspan="12" style="text-align: center"><strong>STUDENT DATA</strong></th>
            </tr>
            <tr>
                <th colspan="1">#</th>
                <th colspan="2">ID</th>
                <th colspan="4">SURNAME AND NAMES</th>
                <th colspan="2">Percentage</th>
                <th colspan="2">Quality Points</th>
                <th colspan="1">Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projections_result['students'] as $key => $student) { ?>
                <tr>
                    <td colspan="1"><?= $key + 1 ?></td>
                    <td colspan="2"><?= $student['student']->id_document ?></td>
                    <td colspan="4">
                        <?= $student['student']->last_name . ' ' . $student['student']->middle_last_name . ' ' . $student['student']->name . ' ' . $student['student']->middle_name ?>
                    </td>
                    <td colspan="2"><?= $student['calification'] ?></td>
                    <td colspan="2"><?= get_calc_note($student['calification']) ?></td>
                    <td colspan="1"><?= get_literal_note($student['calification']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div style="text-align: center">
    <input type="hidden" name="name_document"
        value="<?= strtoupper($projections_result['subject']->name) . ' - ' . strtoupper($projections_result['academic_period']->code) ?>">
    <button type="button" class="button button-success" id="preview-grades"
        style="margin: 10px"><?= __('Export PDF', 'edusystem'); ?></button>
</div>