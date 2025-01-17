<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script> -->

<div>
    <form method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollments'); ?>">

        <div>
            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                <b><?= __('Filter', 'aes'); ?></b>
            </h3>

            <div style="text-align: center;">
                <label for="input_id"><b><?= __('Period', 'aes'); ?></b></label><br>
                <select name="academic_period">
                    <option value="" selected>Select academic period to filter</option>
                    <?php foreach ($periods as $period) { ?>
                        <option value="<?php echo $period->code; ?>" <?= ($projection_for->code_period == $period->code) ? 'selected' : ''; ?>>
                            <?php echo $period->name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div style="text-align: center;">
                <label for="input_id"><b><?= __('Cut', 'aes'); ?></b></label><br>
                <select name="academic_period_cut">
                    <option value="">Select academic period cut</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

        </div>

        <div style="margin-top:20px;text-align:center">
            <button type="submit" class="button button-success" name="action" value="save"><?= __('Search', 'aes'); ?></button>
        </div>
    </form>
</div>

<div>
    <div style="padding: 20px !important">
        <?php foreach ($projections_result as $key => $projection) { ?>
            <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollment_subject&subject_id='.$projection['subject_id']).'&academic_period='.$projection['academic_period'].'&academic_period_cut='.$projection['academic_period_cut']; ?>">
                <table class="wp-list-table widefat fixed posts striped projection-report-template">
                    <thead>
                        <tr>
                            <th colspan="6">STUDENTS OF <?= $key ?> (<?= $projection['count'] ?>)</th>
                        </tr>
                    </thead>
                </table>
            </a>
            <br>
        <?php } ?>
    </div>
</div>

<!-- <script>
    document.getElementById('download').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;

        // Use html2canvas to take a screenshot of the table
        html2canvas(document.getElementById('history')).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF();
            const imgWidth = 190; // Adjust width as needed
            const pageHeight = pdf.internal.pageSize.height;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            const heightLeft = imgHeight;

            let position = 0;

            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            position += heightLeft;

            // Add a new page if the content is too long
            if (heightLeft >= pageHeight) {
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            }

            pdf.save('table.pdf');
        });
    });
</script> -->