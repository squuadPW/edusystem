<form method="POST"
    action="<?php the_permalink(); ?>?action=save_student&idbitrix=<?php echo $_GET['idbitrix'] ?? null ?>"
    class="form-aes" autocomplete="off">
    <div id="loading"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); text-align: center; font-weight: 600; font-style: italic; z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="loader"></div>
        </div>
    </div>

    <!-- DATOS DEL ESTUDIANTE -->
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Student details', 'aes'); ?></div>
        </div>
        <!-- <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('Year', 'aes'); ?><span class="required">*</span></label>
                <select id="year-select" class="year-select"></select>
            </div> -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('Date of birth', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_student"
                name="birth_date_student" required>
            <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult"
                value="0">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('Type document', 'aes'); ?><span class="required">*</span></label>
            <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument(); validateIDs(false)" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="passport" <?= $_COOKIE['document_type'] == 'passport' ? 'selected' : '' ?>><?= __('Passport', 'aes'); ?></option>
                <option value="identification_document" <?= $_COOKIE['document_type'] == 'passport' ? 'selected' : '' ?>><?= __('Identification Document', 'aes'); ?></option>
                <option value="ssn" <?= $_COOKIE['document_type'] == 'passport' ? 'selected' : '' ?>><?= __('SSN', 'aes'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('ID document', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document"
                oninput="sendAjaxIdDocument(); validateIDs(false)" required value="<?= $_COOKIE['id_document'] ?? '' ?>">
            <span id="exisstudentid"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user', 'aes'); ?></span>
            <span class="sameids"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same ID as the student', 'aes'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" value="<?= $_COOKIE['name_student'] ?? '' ?>" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Second name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off" value="<?= $_COOKIE['middle_name_student'] ?? '' ?>" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Last name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" value="<?= $_COOKIE['lastname_student'] ?? '' ?>" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Second last name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off" value="<?= $_COOKIE['middle_last_name_student'] ?? '' ?>" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('Contact number', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" value="<?= $_COOKIE['phone_student'] ?? '' ?>" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="student-email-detail">
            <div id="student-email">
                <label for="email"><?= __('Email address', 'aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="email" name="email_student" autocomplete="off"
                    oninput="sendAjaxStudentEmailDocument()" value="<?= $_COOKIE['email_student'] ?? '' ?>" required>
                <span id="existstudentemail"
                    style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user', 'aes'); ?></span>
                <span id="sameemailstudent"
                    style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The student cannot share the same email as the representative', 'aes'); ?></span>
            </div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="gender"><?= __('Gender', 'aes'); ?><span class="required">*</span></label>
            <select class="form-control" id="gender" required name="gender">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="male" <?= $_COOKIE['gender'] == 'male' ? 'selected' : '' ?>><?= __('Male', 'aes'); ?></option>
                <option value="female" <?= $_COOKIE['gender'] == 'female' ? 'selected' : '' ?>><?= __('Female', 'aes'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="etnia"><?= __('Ethnicity', 'aes'); ?><span class="required">*</span></label>
            <select class="form-control" id="etnia" required name="etnia">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="1" <?= $_COOKIE['ethnicity'] == '1' ? 'selected' : '' ?>><?= __('African American', 'aes'); ?></option>
                <option value="2" <?= $_COOKIE['ethnicity'] == '2' ? 'selected' : '' ?>><?= __('Asian', 'aes'); ?></option>
                <option value="3" <?= $_COOKIE['ethnicity'] == '3' ? 'selected' : '' ?>><?= __('Caucasian', 'aes'); ?></option>
                <option value="4" <?= $_COOKIE['ethnicity'] == '4' ? 'selected' : '' ?>><?= __('Hispanic', 'aes'); ?></option>
                <option value="5" <?= $_COOKIE['ethnicity'] == '5' ? 'selected' : '' ?>><?= __('Native American', 'aes'); ?></option>
                <option value="7" <?= $_COOKIE['ethnicity'] == '7' ? 'selected' : '' ?>><?= __('Choose Not To Respond', 'aes'); ?></option>
            </select>
        </div>

        <!-- DATOS DEL PADRE -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10" id="parent-title"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Parent details', 'aes'); ?></div>
        </div>
        <div id="parent_birth_date_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date_parent"><?= __('Date of birth', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_parent"
                name="birth_date_parent" value="<?= $_COOKIE['birth_date_parent'] ?? '' ?>" required>
        </div>

        <div id="parent_document_type_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="document_type"><?= __('Type document', 'aes'); ?><span class="required">*</span></label>
            <select id="parent_document_type" name="parent_document_type" autocomplete="off" oninput="validateIDs(false)" required>
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="passport"><?= __('Passport', 'aes'); ?></option>
                <option value="identification_document"><?= __('Identification Document', 'aes'); ?></option>
                <option value="ssn"><?= __('SSN', 'aes'); ?></option>
            </select>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="id_document_parent"><?= __('ID document', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="text" id="id_document_parent" name="id_document_parent"
                oninput="validateIDs(false)" required>
            <span class="sameids"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same ID as the student', 'aes'); ?></span>
        </div>
        <div id="parent_name_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_name"><?= __('Name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="agent_name" autocomplete="off" id="agent_name"
                required>
        </div>
        <div id="parent-lastname-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_name"><?= __('Last name', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="agent_last_name" autocomplete="off"
                id="agent_last_name" required>
        </div>
        <div id="parent-phone-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('Contact number', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_partner" autocomplete="off" id="number_partner"
                name="number_partner" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="parent-gender-field">
            <label for="gender_parent"><?= __('Gender', 'aes'); ?><span class="required">*</span></label>
            <select class="form-control" id="gender_parent" required name="gender_parent">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <option value="male"><?= __('Male', 'aes'); ?></option>
                <option value="female"><?= __('Female', 'aes'); ?></option>
            </select>
        </div>
        <input type="hidden" name="country" id="country-select">
        <input type="hidden" name="city">
        <!-- <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country', 'form-plugin'); ?><span class="required">*</span></label>
            <select name="country" autocomplete="off" id="country-select">
                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($countries as $key => $country) { ?>
                    <option value="<?= $key ?>"><?= $country; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="city" autocomplete="off">
        </div> -->

        <!-- DATOS DE ACCESO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center" id="access_data"><?= __('Platform access data of parent', 'aes'); ?>
            </div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="student-email-access"></div>
        <div id="parent-email-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('Email address', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="email" name="email_partner" autocomplete="off" id="email_partner"
                oninput="sendAjaxPartnerEmailDocument()" required>
            <span id="existparentemail"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user', 'aes'); ?></span>
            <span id="sameemailparent"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same email as the student', 'aes'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="password"><?= __('Password of access', 'aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="password" name="password" autocomplete="off" required>
        </div>

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
                    <option value="<?= $grade->id; ?>"><?= $grade->name; ?> <?= $grade->description; ?></option>
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
                <option value="" selected="selected" data-others="1"><?= __('Select an option', 'aes'); ?></option>
                <?php foreach ($institutes as $institute): ?>
                    <option value="<?= $institute->id; ?>" data-others="0" data-country="<?= $institute->country; ?>">
                        <?= $institute->name; ?>
                    </option>
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
            <button class="submit" id="buttonsave"><?= __('Continue', 'aes'); ?></button>
        </div>
    </div>
</form>