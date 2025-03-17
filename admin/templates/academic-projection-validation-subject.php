<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

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
    <button type="button" class="button button-success" id="download"
        style="margin: 10px"><?= __('Export PDF', 'aes'); ?></button>
</div>

<script>
    document.getElementById('download').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        document.getElementById('download').disabled = true;
        html2canvas(document.getElementById('template_subject'), {
            scale: 3,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png', 1.0);
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            // Dimensiones de la página A4 en mm
            const pageWidth = 210;
            const pageHeight = 297;

            // Márgenes
            const margin = 10;
            const contentWidth = pageWidth - 2 * margin;
            const contentHeight = pageHeight - 2 * margin;

            // Calcular dimensiones manteniendo relación de aspecto
            let imgWidth = canvas.width * 0.264583;
            let imgHeight = canvas.height * 0.264583;

            // Ajustar al ancho máximo del contenido
            if (imgWidth > contentWidth) {
                const ratio = contentWidth / imgWidth;
                imgWidth = contentWidth;
                imgHeight = imgHeight * ratio;
            }

            // Ajustar altura si es necesario (priorizando el ancho)
            if (imgHeight > contentHeight) {
                const ratio = contentHeight / imgHeight;
                imgHeight = contentHeight;
                imgWidth = imgWidth * ratio;
            }

            // Posición en la parte superior con margen
            const xPos = margin; // Margen izquierdo
            const yPos = margin; // Margen superior

            pdf.addImage(imgData, 'PNG', xPos, yPos, imgWidth, imgHeight);
            pdf.save(`${document.querySelector('input[name=name_document]').value}.pdf`);
            document.getElementById('download').disabled = false;
        });
    });
</script>