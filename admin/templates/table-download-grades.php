<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div id="modal-grades" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <span id="close-modal-grades" style="float: right; cursor: pointer"><span
                class='dashicons dashicons-no-alt'></span></span>
        <div class="modal-body" id="content-pdf">
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
                            <?= strtoupper(__($student->last_name . ' ' . $student->middle_last_name . ', ' . $student->name . ' ' . $student->middle_name, 'edusystem')); ?>
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
                            $download_grades = get_status_approved('CERTIFIED NOTES HIGH SCHOOL', $student->id);
                            $subject = get_subject_details($projection_for->subject_id);
                            $period_name = '';
                            $period = get_period_details_code($projection_for->code_period);
                            $period_name = $period ? $period->name : '';
                            ?>
                            <tr>
                                <td colspan="2"><?= $projection_for->code_subject ?></td>
                                <td colspan="4"><?= $projection_for->subject ?>
                                    <?= isset($projection_for->is_elective) ? ($projection_for->is_elective ? '(ELECTIVE)' : '') : '' ?>
                                </td>
                                <td colspan="1">
                                    <?= $subject->type != 'equivalence' ? $projection_for->hc : ($download_grades ? 'TR' : '-') ?>
                                </td>
                                <td colspan="1">
                                    <?= isset($projection_for->calification) && !empty($projection_for->calification) ? $projection_for->calification : ($subject->type != 'equivalence' ? '-' : ($download_grades ? 'TR' : '-')) ?>
                                </td>
                                <td colspan="1">
                                    <?= $subject->type != 'equivalence' ? get_calc_note($projection_for->calification) : ($download_grades ? 'TR' : '-') ?>
                                </td>
                                <td colspan="3"><?= isset($period_name) && !empty($period_name) ? $period_name : '-' ?></td>
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