<div id="modal-cropperjs" class="modal" style="display: none; overflow:auto;">
  <div class="modal-content" style="overflow: auto; padding: 0 !important">
    <div class="modal-header p-5">
      <h3 style="font-weight: 600">Crop</h3>
      <span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
    </div>
    <div class="modal-body">
      <div id="pre-image">
            <img id="imagePreview" src="#" alt="Preview">
      </div>

      <div id="preview" style="display: none">
        <div id="result"></div>
      </div>

      <div style="display: flex; margin-top: 3rem; gap: 10px;" id="pre-image-buttons">
      <button type="button" class="submit button-warning" id="btnRestore"><?= __('Restore', 'edusystem'); ?></button>
        <button type="button" class="submit button-primary" id="btnCrop" style="width: 50%; padding: 10px 20px !important; text-align: center; background-color: #002fbd !important; border-radius: 4px; color: white; font-size: 18px; margin: auto; border-radius: 20px;"><?= __('Crop', 'edusystem'); ?></button>
      </div>

      <div style="display: flex; margin-top: 3rem; gap: 10px;; display: none;" id="preview-buttons">
        <button type="button" class="submit button-warning" id="btnBack"><?= __('Back', 'edusystem'); ?></button>
        <button type="button" class="submit button-success" style="padding: 10px 20px !important;" id="btnConfirm"><?= __('Confirm', 'edusystem'); ?></button>
      </div>
    </div>
  </div>
</div>