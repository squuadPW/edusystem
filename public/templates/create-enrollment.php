<!-- Modal container -->
<div id="modal-contraseña" class="modal">
    <div class="modal-content modal-enrollment">
        <div class="modal-body">
            <div>
                <canvas id="signature-pad" width="100%" height="200"></canvas>
                <button id="clear">Clear</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    const signaturePad = new SignaturePad(document.getElementById('signature-pad'));
    document.getElementById('clear').addEventListener('click', () => {
        signaturePad.clear();
    });

    function resizeCanvas() {
        const canvas = document.getElementById('signature-pad');
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        let width, height;

        // Set different canvas sizes based on screen sizes
        if (window.matchMedia("(max-width: 768px)").matches) { // mobile
            width = 300;
            height = 150;
        } else if (window.matchMedia("(max-width: 1200px)").matches) { // laptop
            width = 400;
            height = 200;
        } else { // desktop
            width = 600;
            height = 300;
        }

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // otherwise isEmpty() might return incorrect value
    }

    window.addEventListener("resize", resizeCanvas);
    window.addEventListener("orientationchange", resizeCanvas);

    document.addEventListener("DOMContentLoaded", function () {
        const metaViewport = document.querySelector('meta[name="viewport"]');
        if (metaViewport) {
            metaViewport.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
        }
    });

    resizeCanvas();
</script>