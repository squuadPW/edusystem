<div id="modal-request" class="modal" style="display: none">
  <div class="modal-content" style="overflow: auto; padding: 0 !important">
    <div class="modal-header p-5">
      <h3 style="font-weight: 600">New request</h3>
      <span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
    </div>
    <div class="modal-body">
      <div>
        <div>
          <label for="type"><?= __('Type', 'aes'); ?><span class="required">*</span></label>
          <select class="form-control" name="type_id" autocomplete="off" required>
            <option value="" class="text-uppercase">Select an option</option>
            <?php foreach ($types as $type) { ?>
              <option value="<?php echo $type->id ?>" class="text-uppercase"><?= $type->type ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <label for="reason"><?= __('Reason', 'aes'); ?><span class="required">*</span></label>
          <textarea class="formdata" name="reason" autocomplete="off" required></textarea>
        </div>
        <?php if (!$student_id) { ?>
          <div>
            <label for="student_id"><?= __('Student', 'aes'); ?> <span style="font-size: 10px; font-style: italic;">(optional)</span></label>
            <select class="form-control" name="student_id" autocomplete="off">
              <option value="" class="text-uppercase">Select an option</option>
              <?php foreach ($students as $student) { ?>
                <option value="<?php echo $student->id ?>" class="text-uppercase"><?= $student->name ?> <?= $student->middle_name ?> <?= $student->last_name ?> <?= $student->middle_last_name ?></option>
              <?php } ?>
            </select>
          </div>
        <?php } else { ?>
          <input type="hidden" name="student_id" value="<?= $student_id ?>"></input>
          <input type="hidden" name="partner_id" value="<?= $partner_id ?>"></input>
        <?php } ?>
      </div>

      <!-- DATOS DEL GRADO -->
      <div style="text-align:center; margin-top: 3rem">
        <button type="button" class="submit button-success" id="send-request"><?= __('Save', 'aes'); ?></button>
      </div>
    </div>
  </div>
</div>