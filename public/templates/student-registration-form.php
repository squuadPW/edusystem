<form method="POST"
    action="<?php the_permalink(); ?>?action=save_student"
    class="form-aes" autocomplete="off">
    <div id="loading"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); text-align: center; font-weight: 600; font-style: italic; z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="loader"></div>
        </div>
    </div>

    <input type="hidden" name="crm_token" id="x-api-key" value="<?= get_option('crm_token') ?? ''; ?>">
    <input type="hidden" name="crm_url" id="x-api-url" value="<?= get_option('crm_url') ?? ''; ?>">
    <input type="hidden" name="crm_id" id="x-api-id" value="<?= $_GET['crm_id'] ?? ''; ?>">
    <input type="hidden" name="crm_api" id="x-api" value="contacts">
    <input type="hidden" name="squuad_stripe_selected_client_id" id="squuad_stripe_selected_client_id" value="<?= $connected_account ?>">
    <input type="hidden" id="product_id_input" name="product_id">

    <!-- DATOS DEL ESTUDIANTE -->
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Student details', 'edusystem'); ?></div>
        </div>
        <!-- <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('Year', 'edusystem'); ?><span class="required">*</span></label>
                <select id="year-select" class="year-select"></select>
            </div> -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date_student"><?= __('Date of birth', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_student"
                name="birth_date_student" required>
            <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult"
                value="0">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="document_type"><?= __('Type document', 'edusystem'); ?><span class="required">*</span></label>
            <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument(); validateIDs(false)" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="passport"><?= __('Passport', 'edusystem'); ?></option>
                <option value="identification_document"><?= __('Identification Document', 'edusystem'); ?></option>
                <option value="ssn"><?= __('SSN', 'edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="id_document"><?= __('ID document', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document"
                oninput="sendAjaxIdDocument(); validateIDs(false)" required>
            <span id="exisstudentid"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user', 'edusystem'); ?></span>
            <span class="sameids"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same ID as the student', 'edusystem'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name_student"><?= __('Name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" id="name_student" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="middle_name_student"><?= __('Second name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname_student"><?= __('Last name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" id="lastname_student" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="middle_last_name_student"><?= __('Second last name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="number_phone"><?= __('Contact number', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="student-email-detail">
            <div id="student-email">
                <label for="email_student"><?= __('Email address', 'edusystem'); ?><span class="required">*</span></label>
                <input class="formdata" type="email" name="email_student" autocomplete="off"
                    oninput="sendAjaxStudentEmailDocument()" required>
                <span id="existstudentemail"
                    style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user', 'edusystem'); ?></span>
                <span id="sameemailstudent"
                    style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The student cannot share the same email as the representative', 'edusystem'); ?></span>
            </div>
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

        <!-- DATOS DEL PADRE -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10" id="parent-title"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Parent details', 'edusystem'); ?></div>
        </div>
        <div id="parent_birth_date_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date_parent"><?= __('Date of birth', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata flatpickr" autocomplete="off" type="date" id="birth_date_parent"
                name="birth_date_parent" required>
        </div>

        <div id="parent_document_type_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="document_type"><?= __('Type document', 'edusystem'); ?><span class="required">*</span></label>
            <select id="parent_document_type" name="parent_document_type" autocomplete="off" oninput="validateIDs(false)" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="passport"><?= __('Passport', 'edusystem'); ?></option>
                <option value="identification_document"><?= __('Identification Document', 'edusystem'); ?></option>
                <option value="ssn"><?= __('SSN', 'edusystem'); ?></option>
            </select>
        </div>

        <div id="parent_id_document_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="id_document_parent"><?= __('ID document', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="text" id="id_document_parent" name="id_document_parent"
                oninput="validateIDs(false)" required>
            <span class="sameids"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same ID as the student', 'edusystem'); ?></span>
        </div>
        <div id="parent_name_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_name"><?= __('Name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="agent_name" autocomplete="off" id="agent_name"
                required>
        </div>
        <div id="parent-lastname-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_last_name"><?= __('Last name', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" type="text" name="agent_last_name" autocomplete="off"
                id="agent_last_name" required>
        </div>
        <div id="parent-phone-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="number_partner"><?= __('Contact number', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_partner" autocomplete="off" id="number_partner"
                name="number_partner" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="parent-gender-field">
            <label for="gender_parent"><?= __('Gender', 'edusystem'); ?><span class="required">*</span></label>
            <select class="form-control" id="gender_parent" required name="gender_parent">
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <option value="male"><?= __('Male', 'edusystem'); ?></option>
                <option value="female"><?= __('Female', 'edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country', 'edusystem'); ?><span class="required">*</span></label>
            <select name="country" autocomplete="off" id="country-select">
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($countries as $key => $country) { ?>
                    <option value="<?= $key ?>"><?= $country; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="city" autocomplete="off">
        </div>

        <!-- DATOS DE ACCESO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center" id="access_data"><?= __('Platform access data of parent', 'edusystem'); ?>
            </div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="student-email-access"></div>
        <div id="parent-email-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('Email address', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="email" name="email_partner" autocomplete="off" id="email_partner"
                oninput="sendAjaxPartnerEmailDocument()" required>
            <span id="existparentemail"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user', 'edusystem'); ?></span>
            <span id="sameemailparent"
                style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same email as the student', 'edusystem'); ?></span>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="password"><?= __('Password of access', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="password" name="password" autocomplete="off" required>
        </div>

        <!-- DATOS DEL GRADO -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10"
            style="margin-top: 30px !important; background: rgb(223 223 223); color: black">
            <div class="subtitle text-align-center"><?= __('Degree details', 'edusystem'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="program"><?= __('Program of your interest', 'edusystem'); ?><span class="required">*</span></label>
            <select name="program" id="program" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($programs as $program): ?>
                    <option value="<?= $program->identificator; ?>"><?= $program->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" id="institute-id-select" style="display:none">
            <label for="name_institute"><?= __('Name of School or Institution with Agreement', 'edusystem'); ?><span
                    id="institute_id_required" class="required">*</span></label>
            <select name="institute_id" autocomplete="off" id="institute_id" required>
                <option value="" selected="selected" data-others="1"><?= __('Select an option', 'edusystem'); ?></option>
                <?php foreach ($institutes as $institute): ?>
                    <option value="<?= $institute->id; ?>" data-others="0" data-country="<?= $institute->country; ?>">
                        <?= $institute->name; ?>
                    </option>
                <?php endforeach; ?>
                <option value="other"><?= __('Other', 'edusystem'); ?></option>
            </select>
        </div>
        <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6"
            style="display:none;">
            <label for="name_institute"><?= __('Name Institute', 'edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
        </div>
        <div id="grade_select" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="display: none">
            <label for="grade" id="grade_tooltip"><?= __('Grade', 'edusystem'); ?> <span style="color: #002fbd"
                    class="dashicons dashicons-editor-help"></span><span class="required">*</span></label>
            <select name="grade" id="grade" autocomplete="off" required>
                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
            </select>
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
            <button class="submit" id="buttonsave"><?= __('Continue', 'edusystem'); ?></button>
        </div>
    </div>
</form>