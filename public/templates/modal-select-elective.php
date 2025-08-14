<div id="modal-continue" class="modal" style="display: block">
    <div class="modal-content" style="overflow: auto; padding: 0 !important">
    <div class="modal-header p-5">
        <h3 style="font-weight: 600">Select elective to study</h3>
        <span class="modal-close" id="close-continue"><span class="dashicons dashicons-no-alt"></span></span>
      </div>
      <div class="modal-body">
          <div>
            <p style="font-size: 12px; font-style: italic; text-align: center">During this period you will be able to take an elective of your choice. Please choose one of the available electives:</p>
          </div>
          <div>
            <div>
              <label for="elective"><?= __('Elective', 'edusystem'); ?><span class="required">*</span></label>
              <select class="form-control" name="elective" autocomplete="off" required>
                <?php foreach($electives as $elective) { ?>
                  <option value="<?php echo $elective->id ?>"><?php echo $elective->name ?> (<?php echo $elective->code_subject ?>)</option>
                <?php } ?>
              </select>
            </div>
          </div>

          <!-- DATOS DEL GRADO -->
          <div style="text-align:center; margin-top: 3rem">
            <button type="button" class="submit button-success" id="send-continue"><?= __('Select', 'edusystem'); ?></button>
          </div>
      </div>
    </div>
  </div>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
  // With the above scripts loaded, you can call `tippy()` with a CSS
  // selector and a `content` prop:
  tippy('#continue', {
    content: 'By clicking here the student will be asked to choose an elective subject to be subsequently enrolled in the next academic cut.',
  });
</script>