<!-- Your modal content here -->
<input class="formdata" autocomplete="off" type="hidden" id="modal_open" name="modal_open" value="1">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">

<div class="modal-content modal-enrollment" id="modal-content">
    <div id="modal-contraseña" class="modal" style="overflow: auto; padding: 0 !important">
        <div class="modal-content modal-enrollment">
            <span id="close-modal-enrollment" style="float: right; cursor: pointer"><span
                    class='dashicons dashicons-no-alt'></span></span>
            <div class="modal-body" id="content-pdf">
                <input class="formdata" autocomplete="off" type="hidden" id="modal_open" name="modal_open" value="1" />
                <input type="hidden" name="student_user_id" value="<?= $student_id ?>" />
                <input type="hidden" name="parent_user_id" value="<?= $partner_id ?>" />
                <input type="hidden" name="show_parent_info" value="<?= $show_parent_info ?>" />
                <input type="hidden" name="document_id" value="<?= $document->document_identificator ?>">
                <input type="hidden" name="document_name" value="<?= $document->title ?>">
                <?= $html ?>
            </div>
            <div class="modal-footer" style="text-align: center; display: block">
                <button type="button" class="submit button-create-enrollment" id="saveSignatures"><?= __('Save', 'edusystem') ?></button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</div>