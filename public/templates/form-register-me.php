<form method="POST" action="<?= the_permalink() . '?action=new_applicant_me'; ?>" class="form-aes" id="form-me" autocomplete="off">
    <div class="grid grid-cols-12 gap-4">

        <!-- DATOS DEL ESTUDIANTE -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Student details', 'aes'); ?></div>
        </div>
        <?php if (!get_user_meta(get_current_user_id(), 'birth_date', true)) { ?>
            <div id="parent_birth_date_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date_parent"><?= __('Date of birth', 'aes'); ?><span class="required">*</span></label>
                <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_parent"
                    name="birth_date_parent" required>
            </div>
        <?php } ?>

        <?php if (!get_user_meta(get_current_user_id(), 'type_document', true)) { ?>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('Type document', 'aes'); ?><span class="required">*</span></label>
                <select value="<?php echo get_user_meta(get_current_user_id(), 'type_document', true) ?>"
                    name="parent_document_type" id="parent_document_type"  autocomplete="off" oninput="sendAjaxIdDocument()" required>
                    <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                    <option value="passport"><?= __('Passport', 'aes'); ?></option>
                    <option value="identification_document"><?= __('Identification Document', 'aes'); ?></option>
                    <option value="ssn"><?= __('SSN', 'aes'); ?></option>
                </select>
            </div>
        <?php } ?>

        <?php if (!get_user_meta(get_current_user_id(), 'id_document', true)) { ?>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="id_document_parent"><?= __('ID document', 'aes'); ?><span class="required">*</span></label>
                <input value="<?php echo get_user_meta(get_current_user_id(), 'id_document', true) ?>"
                    class="formdata capitalize" autocomplete="off" type="text" id="id_document_parent"
                    name="id_document_parent" oninput="sendAjaxIdDocument()" required>
                <span id="exisstudentid"
                    style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user', 'aes'); ?></span>
            </div>
        <?php } ?>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'first_name', true) ?>"
                class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Second name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Last name', 'aes'); ?><span class="required">*</span></label>
            <input value="<?php echo get_user_meta(get_current_user_id(), 'last_name', true) ?>"
                class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Second last name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off" required>
        </div>

        <?php if (!get_user_meta(get_current_user_id(), 'gender', true)) { ?>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="gender_parent"><?= __('Gender', 'aes'); ?><span class="required">*</span></label>
                <select class="form-control" id="gender_parent" required name="gender_parent">
                    <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                    <option value="male"><?= __('Male', 'aes'); ?></option>
                    <option value="female"><?= __('Female', 'aes'); ?></option>
                </select>
            </div>
        <?php } ?>

        <?php if (!get_user_meta(get_current_user_id(), 'ethnicity', true)) { ?>
            <div id="parent_ethnicity_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="etnia"><?= __('Ethnicity', 'aes'); ?><span class="required">*</span></label>
                <select class="form-control" id="etnia" required name="etnia">
                    <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                    <option value="1"><?= __('African American', 'aes'); ?></option>
                    <option value="2"><?= __('Asian', 'aes'); ?></option>
                    <option value="3"><?= __('Caucasian', 'aes'); ?></option>
                    <option value="4"><?= __('Hispanic', 'aes'); ?></option>
                    <option value="5"><?= __('Native American', 'aes'); ?></option>

                    <option value="7"><?= __('Choose Not To Respond', 'aes'); ?></option>
                </select>
            </div>
        <?php } ?>

        <!-- DATOS DEL GRADO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Degree details', 'aes'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="grade" id="grade_tooltip"><?= __('Grade', 'aes'); ?> <span style="color: #091c5c"
                    class="dashicons dashicons-editor-help"></span><span class="required">*</span></label>
            <select name="grade" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?= $grade->id; ?>"><?= $grade->name; ?>     <?= $grade->description; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="program"><?= __('Program of your interest', 'aes'); ?><span class="required">*</span></label>
            <select name="program" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="aes"><?= __('Dual diploma', 'aes'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name_institute"><?= __('Name of School or Institution with Agreement', 'aes'); ?><span
                    id="institute_id_required" class="required">*</span></label>
            <select name="institute_id" autocomplete="off" id="institute_id" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($institutes as $institute): ?>
                    <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                <?php endforeach; ?>
                <option value="other"><?= __('Other', 'aes'); ?></option>
            </select>
        </div>
        <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="display:none;">
            <label for="name_institute"><?= __('Name Institute', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="checkbox" id="terms" name="terms" required>
            <?= __('Accept ', 'aes'); ?>
            <a href="https://online.american-elite.us/terms-and-conditions/" target="_blank"
                style="text-decoration: underline!important; color: #0a1c5c;">
                <?= __('Terms and Conditions', 'aes'); ?>
            </a>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
            <button class="submit" id="buttonsave"><?= __('Send', 'aes'); ?></button>
        </div>
    </div>
</form>