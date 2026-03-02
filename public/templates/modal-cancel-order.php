<div id="modal-cancel-order" class="modal" style="display: none">

    <div class="modal-content" style="overflow: auto; padding: 0 !important">

        <div class="modal-header p-5">
            <h3 style="font-weight: 600"><?= __('Alert', 'edusystem'); ?></h3>
            <span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
        </div>

        <div class="modal-body">
            
            <div>
                <p style="font-size: 12px; font-style: italic; text-align: center"><?= __('Do you want to cancel the order?','edusystem') ?></p>
            </div>

            <div style="text-align:center; margin-top: 3rem">
                <button type="button" class="button modal-close" ><?= __('No', 'edusystem'); ?></button>
                <a class="button button-success" id="confirm_order_cancel"><?= __('Yes', 'edusystem'); ?></a>
            </div>

        </div>
    </div>
</div>
