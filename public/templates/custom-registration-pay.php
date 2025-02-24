<div class="title">
    <?= __('Applicant', 'aes'); ?>
</div>
<form method="POST"
    action="<?php the_permalink(); ?>?action=save_student&idbitrix=<?php echo $_GET['idbitrix'] ?? null ?>"
    class="form-aes">

    <!-- DATOS DEL ESTUDIANTE -->
    <div class="grid grid-cols-12 gap-4">
        <!-- DATOS DEL GRADO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Degree details', 'aes'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="hidden" name="from_webinar" value="1">
            <label for="grade" id="grade_tooltip"><?= __('Grade', 'aes'); ?> <span style="color: #091c5c" class="dashicons dashicons-editor-help"></span><span class="required">*</span></label>
            <select name="grade" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?= $grade->id; ?>"><?= $grade->name; ?> <?= $grade->description; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="program"><?= __('Program of your interest', 'aes'); ?><span class="required">*</span></label>
            <select name="program" autocomplete="off" required>
                <option value="aes" selected="selected"><?= __('Dual diploma', 'aes'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="checkbox" id="terms" name="terms" checked required>
            <?= __('Accept ', 'aes'); ?>
            <a href="https://portal.americanelite.school/terms-and-conditions/" target="_blank"
                style="text-decoration: underline!important; color: #0a1c5c;">
                <?= __('Terms and Conditions', 'aes'); ?>
            </a>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
            <button class="submit" id="buttonsave"><?= __('Send', 'aes'); ?></button>
        </div>
    </div>
</form>


<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
      // With the above scripts loaded, you can call `tippy()` with a CSS
      // selector and a `content` prop:
      tippy('#grade_tooltip', {
        content: 'Please select the grade you are currently studying',
      });
</script>