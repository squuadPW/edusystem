<div id="modal-continue-checkout" class="modal" style="display: block">
    <div class="modal-content" style="overflow: auto; padding: 0 !important">
    <div class="modal-header p-5">
        <h3 style="font-weight: 600">Previous form</h3>
        <!-- <span class="modal-close" id="close-continue"><span class="dashicons dashicons-no-alt"></span></span> -->
      </div>
      <div class="modal-body">
          <div>
            <p style="font-size: 12px; font-style: italic; text-align: center">We have found some information that you have used in a previous form, would you like to continue to checkout with that same information?</p>
          </div>

          <!-- DATOS DEL GRADO -->
          <div style="text-align:center; margin-top: 3rem">
            <button type="button" class="submit button-primary" id="continue-checkout"><?= __('Continue', 'aes'); ?></button>
            <button style="margin-top: 20px; background-color: #ffffff !important; border: 1px solid #2e6da4; color: #091c5c !important;" type="button" class="submit button-primary" id="quit-checkout"><?= __('Start a new one', 'aes'); ?></button>
          </div>
      </div>
    </div>
  </div>