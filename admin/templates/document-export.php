<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>

<div id="modal-grades" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <span id="close-modal-grades" style="float: right; cursor: pointer"><span
                class='dashicons dashicons-no-alt'></span></span>
        <div class="modal-body" id="content-pdf">
            <div style="position: relative; width: 100%; text-align: center;">
                <!-- Contenedor del Logo (centrado) -->
                <div style="display: inline-block;">
                    <?php include(plugin_dir_path(__FILE__) . 'img-logo.php'); ?>
                </div>

                <!-- QR Code (derecha, sobrepuesto) -->
                <div style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%);">
                    <div id="qrcode"></div>
                </div>
            </div>
            <div id="modal-body-content">

            </div>
        </div>
        <div class="modal-footer" style="text-align: center; display: block">
            <button type="button" class="button button-primary" id="download-grades">Print</button>
        </div>
    </div>
</div>