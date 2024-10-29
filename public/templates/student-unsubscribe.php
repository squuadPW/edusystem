<div style="text-align: center">
  <button type="button" class="btn button-danger" id="unsubscribe">Unsubscribe student</button>
</div>

<div id="modal-unsubscribe" class="modal" style="display: none">
    <div class="modal-content" style="overflow: auto; padding: 0 !important">
      <div class="modal-header p-5">
        <h3 style="font-weight: 600">Reason of unsubscribe</h3>
        <span class="modal-close" id="close-unsubscribe"><span class="dashicons dashicons-no-alt"></span></span>
      </div>
      <div class="modal-body">
          <div>
            <p style="font-size: 12px; font-style: italic; text-align: center">You are about to initiate the unenrollment process from your courses. Please be aware that if you proceed, all your assigned courses will be permanently removed, and you will be moved to the next academic cut-off. Are you sure you want to proceed with this action? Please confirm your decision.</p>
          </div>
          <div>
            <div>
              <label for="reason"><?= __('Reason', 'aes'); ?><span class="required">*</span></label>
              <textarea class="formdata" name="reason" autocomplete="off" required></textarea>
            </div>
          </div>

          <!-- DATOS DEL GRADO -->
          <div style="text-align:center; margin-top: 3rem">
            <button type="button" class="submit button-danger" id="send-unsubscribe"><?= __('Unsubscribe', 'aes'); ?></button>
          </div>
      </div>
    </div>
  </div>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
  // With the above scripts loaded, you can call `tippy()` with a CSS
  // selector and a `content` prop:
  tippy('#unsubscribe', {
    content: 'By clicking here the student will be deferred to the next academic cut-off, unassigning the courses from Moodle and removing their access from the student area.',
  });
</script>