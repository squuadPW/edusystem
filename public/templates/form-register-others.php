<form method="POST" action="<?= the_permalink() . '?action=new_applicant_others'; ?>" class="form-aes" id="form-others" autocomplete="off">
    <div class="grid grid-cols-12 gap-4">
        <!-- DATOS DEL ESTUDIANTE -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Student details', 'edusystem'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('Date of birth', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_student"
                name="birth_date_student" required>
            <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult"
                value="1">
            <span id="dontBeAdult"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('Another student of legal age cannot register', 'edusystem'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('Type document', 'edusystem'); ?><span class="required">*</span></label>
            <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument()" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="passport"><?= __('Passport', 'edusystem'); ?></option>
                <option value="identification_document"><?= __('Identification Document', 'edusystem'); ?></option>
                <option value="ssn"><?= __('SSN', 'edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('ID document', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document"
                oninput="sendAjaxIdDocument()" required>
            <span id="exisstudentid"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user', 'edusystem'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('First name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Middle name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('First surname', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Second surname', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('Contact number', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="gender"><?= __('Gender', 'edusystem'); ?><span class="required">*</span></label>
            <select class="form-control" id="gender" required name="gender">
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="male"><?= __('Male', 'edusystem'); ?></option>
                <option value="female"><?= __('Female', 'edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="etnia"><?= __('Ethnicity', 'edusystem'); ?><span class="required">*</span></label>
            <select class="form-control" id="etnia" required name="etnia">
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="1"><?= __('African American', 'edusystem'); ?></option>
                <option value="2"><?= __('Asian', 'edusystem'); ?></option>
                <option value="3"><?= __('Caucasian', 'edusystem'); ?></option>
                <option value="4"><?= __('Hispanic', 'edusystem'); ?></option>
                <option value="5"><?= __('Native American', 'edusystem'); ?></option>
                <option value="7"><?= __('Choose Not To Respond', 'edusystem'); ?></option>
            </select>
        </div>

        <!-- DATOS DE ACCESO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center" id="access_data">
                <?= __('Platform access data of student', 'edusystem'); ?>
            </div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('Email address', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="email" name="email_student" autocomplete="off"
                oninput="sendAjaxStudentEmailDocument()" required>
            <span id="existstudentemail"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user', 'edusystem'); ?></span>
            <span id="sameemailstudent"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The student cannot share the same email as the representative', 'edusystem'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="password"><?= __('Please establish your password', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="password" name="password" autocomplete="off" required>
        </div>

        <!-- DATOS DEL GRADO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Degree details', 'edusystem'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="grade" id="grade_tooltip"><?= __('Grade', 'edusystem'); ?> <span style="color: #002fbd"
                    class="dashicons dashicons-editor-help"></span><span class="required">*</span></label>
            <select name="grade" autocomplete="off">
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?= $grade->id; ?>"><?= $grade->name; ?>     <?= $grade->description; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="program_select">
            <label for="program"><?= __('Program of your interest', 'edusystem'); ?><span class="required">*</span></label>
            <select name="program" id="program" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($programs as $program): ?>
                    <option value="<?= $program->id; ?>"><?= $program->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name_institute"><?= __('Name of School or Institution with Agreement', 'edusystem'); ?><span
                    id="institute_id_required" class="required">*</span></label>
            <select name="institute_id" autocomplete="off" id="institute_id" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($institutes as $institute): ?>
                    <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                <?php endforeach; ?>
                <option value="other"><?= __('Other', 'edusystem'); ?></option>
            </select>
        </div>
        <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="display:none;">
            <label for="name_institute"><?= __('Name Institute', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="checkbox" id="terms" name="terms" required>
            <?= __('Accept ', 'edusystem'); ?>
            <a href="<?= home_url() . '/terms-and-conditions' ?>" target="_blank"
                style="text-decoration: underline!important; color: #0a1c5c;">
                <?= __('Terms and Conditions', 'edusystem'); ?>
            </a>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
            <button class="submit" id="buttonsave"><?= __('Send', 'edusystem'); ?></button>
        </div>
    </div>
</form>