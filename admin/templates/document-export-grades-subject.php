<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div id="modal-grades" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <span id="close-modal-grades" style="float: right; cursor: pointer"><span
                class='dashicons dashicons-no-alt'></span></span>
        <div class="modal-body" id="content-pdf">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="margin-left: 10px; flex: 1">
                    <?php include(plugin_dir_path(__FILE__) . 'img-logo.php'); ?>
                </div>
                <div style="text-align: center; flex: 1">
                    <div>United States of America</div>
                    <div>American Elite School</div>
                    <div>Miami, Florida</div>
                </div>
                <div style="text-align: center; margin-right: 10px; flex: 1">
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
                        <th colspan="8"><strong>COURSE:</strong> <?= strtoupper($projections_result['subject']->name) ?>
                        </th>
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
        <div class="modal-footer" style="text-align: center; display: block">
            <button type="button" class="button button-primary" id="download-grades">Print</button>
        </div>
    </div>
</div>