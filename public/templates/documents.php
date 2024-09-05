<?php

global $wpdb;
$current_user = wp_get_current_user();
$table_students = $wpdb->prefix . 'students';
$student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
?>

<?php if (!$student->moodle_password) { ?>
    <div class="text-center info-box">
        <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'aes'); ?></h2>
        <p>To access the <a style="text-decoration: underline !important; color: #091c5c;"
                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student' ?>">virtual
                classroom</a>, please ensure you complete the following steps:</p>
        <ul class="info-list">
            <li>
                <i class="fas fa-upload"></i>
                Once your payment is approved, the option to upload all required documents is enabled in your registration
                form.
            </li>
            <li>
        </ul>
        <!-- <p class="info-note">Once both steps are complete, you will receive an email with instructions on how to access the virtual classroom. Please note that access will only be granted once all required documents have been received and your payment has been processed.</p> -->
    </div>
<?php } else { ?>
    <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'aes'); ?></h2>
<?php } ?>


<?php if (!empty($students)): ?>

    <form method="post"
        action="<?= wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))) . '?actions=save_documents'; ?>"
        enctype="multipart/form-data">
        <?php foreach ($students as $student): ?>
            <input type="hidden" name="students[]" value="<?= $student->id; ?>">
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <caption style="text-align:start;">
                    Documents of <?= $student->name . ' ' . $student->last_name; ?>
                </caption>
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-document"><span
                                class="nobr"><?= __('Document', 'aes'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span
                                class="nobr"><?= __('Status', 'aes'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action"><span
                                class="nobr"><?= __('action', 'aes'); ?></span></th>
                </thead>
                <tbody>
                    <?php $documents = get_documents($student->id); ?>
                    <?php foreach ($documents as $document): ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                data-title="<?= __('Document', 'aes'); ?>">
                                <input type="hidden" name="<?= 'file_student_' . $student->id . '_id[]'; ?>"
                                    value="<?= $document->id; ?>">
                                <?php $name = get_name_document($document->document_id); ?>

                                <?php if ($document->is_required): ?>
                                    <?php $name = $name . "<span class='required' style='font-size:24px;'>*</span>"; ?>
                                <?php endif; ?>

                                <?= $name; ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                                data-title="<?= __('Status', 'aes'); ?>">
                                <input type="hidden" name="<?= 'status_file_' . $document->id . '_student_id_' . $student->id; ?>"
                                    value="<?= $document->status; ?>">
                                <?= $status = get_status_document($document->status); ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                data-title="<?= __('Action', 'aes'); ?>">
                                <?php if ($document->status == 0 || $document->status == 3 || $document->status == 4) { ?>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"
                                            name="<?= 'document_' . $document->id . '_student_id_' . $student->id; ?>" accept=".pdf">
                                        <span class="custom-file-label">Select file</span>
                                    </div>
                                <?php } else { ?>
                                    <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                        class="button">View Document</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                            data-title="<?= __('Document', 'aes'); ?>">
                            <button type="button" id="missing_document_button">Missing document</button>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                            data-title="<?= __('Status', 'aes'); ?>">

                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                            data-title="<?= __('Action', 'aes'); ?>">

                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
        <div style="display:block;text-align:center;">
            <button class="submit" type="submit"><?= __('Send Documents', 'aes'); ?></button>
        </div>
    </form>
<?php endif; ?>

<!-- The Modal -->
<div id="missing_document_modal" class="modal modal-missing-documents">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Missing Document</h2>
        <div style="margin-bottom: 10px; padding: 10px">
            I, CORDERO ALVARADO, SOFIA ISABEL, identified with the identification document N °.
            0150058006, I agree to deliver to American Elite School, the missing documents and / or
            requirements for the admission process, since these are an integral part of my file, the
            documents are as follows:
        </div>

        <div style="margin-bottom: 10px; padding: 20px">
            <div>
                1. CERTIFIED NOTES HIGH SCHOOL
            </div>
            <div>
                2. HIGH SCHOOL DIPLOMA
            </div>
            <div>
                3. PROOF OF GRADES
            </div>
        </div>

        <div style="margin-bottom: 10px; padding: 10px">
            The academic HIGH SCHOOL program will begin the ACADEMIC YEAR 2024-2025
            (2024/08/12) and since it is my responsibility to deliver the documents specified above, I will
            have a rank of forty-five (45) business days top from the start of my classes. In accordance
            with the previous American Elite School, it may extend for an additional rank of fifteen (15)
            more business days if the student needs them. For this process, the student must send an
            email with the reasons why he needs this extension to the email account
            admissions@american-elite.us Failure to comply with the foregoing, the student may be
            penalized for being withdrawn from the program until he delivers the missing document or
            documents.
        </div>

        <div style="text-align: center">
            <div style="padding: 150px 0px;">
                Signature of Applicant
                <canvas id="canvas" width="400" height="200"></canvas>
                <button id="clear-btn" class="submit"
                    style="margin: 10px; background-color: #091c5c85 !important;">Limpiar</button>
                <button id="save-btn" class="submit">Guardar</button>
            </div>
        </div>
        </d>
    </div>

    <script>
        document.getElementById("missing_document_button").addEventListener("click", function () {
            var modal = document.getElementById("missing_document_modal");
            modal.style.display = "block";
        });

        document.getElementsByClassName("close")[0].addEventListener("click", function () {
            var modal = document.getElementById("missing_document_modal");
            modal.style.display = "none";
        });


        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const clearBtn = document.getElementById('clear-btn');
        const saveBtn = document.getElementById('save-btn');

        if (window.innerWidth < 768) { // Small screens (mobile)
            canvas.width = 300;
        }
        // Configuración del lápiz
        ctx.lineWidth = 5; // Ancho de línea más fino
        ctx.lineCap = 'round'; // Extremos redondeados
        ctx.lineJoin = 'round'; // Uniones redondeadas
        ctx.strokeStyle = '#000'; // Color de línea gris oscuro
        ctx.setLineDash([0]); // Establecer estilo de línea continua
        ctx.imageSmoothingEnabled = true; // Activar anti-aliasing

        // Función para dibujar en el canvas
        let lastX, lastY;

        function draw(e) {
            if (lastX && lastY) {
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
            }
            lastX = e.offsetX;
            lastY = e.offsetY;
        }

        function drawTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            const x = touch.clientX - rect.left;
            const y = touch.clientY - rect.top;
            if (lastX && lastY) {
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(x, y);
                ctx.stroke();
            }
            lastX = x;
            lastY = y;
        }

        function resetLastPoint() {
            lastX = null;
            lastY = null;
        }

        // Eventos para dibujar y limpiar
        canvas.addEventListener('mousedown', (e) => {
            canvas.addEventListener('mousemove', draw);
        });

        canvas.addEventListener('mouseup', () => {
            canvas.removeEventListener('mousemove', draw);
        });

        canvas.addEventListener('mouseup', resetLastPoint);

        canvas.addEventListener('touchstart', (e) => {
            canvas.addEventListener('touchmove', drawTouch);
        });

        canvas.addEventListener('touchend', () => {
            canvas.removeEventListener('touchmove', drawTouch);
        });

        canvas.addEventListener('touchend', resetLastPoint);

        clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Función para guardar la firma como imagen
        saveBtn.addEventListener('click', () => {
            const imageData = canvas.toDataURL();
            const link = document.createElement('a');
            link.href = imageData;
            link.download = 'firma-digital.png';
            link.click();
        });
    </script>