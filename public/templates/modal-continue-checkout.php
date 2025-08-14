<div id="modal-continue-checkout" class="modal" style="display: none; z-index: 10000">
    <div class="modal-content" style="overflow: auto; padding: 0 !important">
    <div class="modal-header p-5">
        <h3 style="font-weight: 600">Previous form</h3>
        <!-- <span class="modal-close" id="close-continue"><span class="dashicons dashicons-no-alt"></span></span> -->
      </div>
      <div class="modal-body">
          <div>
            <p style="font-size: 12px; font-style: italic; text-align: center">We have found that you have done a previous form, do you want to continue with the same information or do you want to start again?</p>
          </div>

          <!-- DATOS DEL GRADO -->
          <div style="text-align:center; margin-top: 3rem">
            <button type="button" class="submit button-primary" id="continue-checkout"><?= __('Continue', 'edusystem'); ?></button>
            <button style="margin-top: 20px; background-color: #ffffff !important; border: 1px solid #2e6da4; color: #002fbd !important;" type="button" class="submit button-primary" id="quit-checkout"><?= __('Start a new one', 'edusystem'); ?></button>
          </div>
      </div>
    </div>
  </div>