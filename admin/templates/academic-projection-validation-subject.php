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
    const downloadBtn = document.getElementById('download');
    const { jsPDF } = window.jspdf;
    downloadBtn.disabled = true;

    // 1. Mostrar elemento temporalmente si está oculto
    const element = document.getElementById('template_subject');
    const originalDisplay = element.style.display;
    element.style.display = 'block';

    // 2. Configuración optimizada de html2canvas
    html2canvas(element, {
        scale: 1.5, // Reducido de 3 a 1.5
        useCORS: true,
        logging: false,
        backgroundColor: "#FFFFFF" // Fondo blanco para mejor compresión
    }).then(canvas => {
        // 3. Convertir a JPEG con compresión
        const imgData = canvas.toDataURL('image/jpeg', 0.7); // 70% calidad

        // 4. Crear PDF con compresión habilitada
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true // Compresión interna del PDF
        });

        // 5. Configuración de dimensiones inteligentes
        const pageWidth = 210; // Ancho A4 en mm
        const margin = 5; // Márgenes reducidos de 10 a 5mm
        const maxContentWidth = pageWidth - (2 * margin);

        // Calcular relación de aspecto
        const imgRatio = canvas.width / canvas.height;
        let imgWidth = maxContentWidth;
        let imgHeight = maxContentWidth / imgRatio;

        // 6. Posicionamiento superior con margen
        const xPos = margin; // Alineación izquierda
        const yPos = margin; // Posición superior

        pdf.addImage(
            imgData,
            'JPEG', // Cambiado de PNG a JPEG
            xPos,
            yPos,
            imgWidth,
            imgHeight
        );

        // 7. Nombre del archivo desde input
        const fileName = document.querySelector('input[name=name_document]').value;
        pdf.save(`${fileName}.pdf`);
        
        // Restaurar estado original
        element.style.display = originalDisplay;
        downloadBtn.disabled = false;
    });
});
</script>