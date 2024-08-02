<?php 
    if (is_user_logged_in()) { 
        ?>
    <div class="title">
        <?= __('New applicant','aes'); ?>
    </div>

    <?php 
    
    global $wpdb;
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $table_students = $wpdb->prefix.'students';
    
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_students WHERE email = %s", $email));
    if (isset($result)) {
        ?>
            <form method="POST" action="<?= the_permalink().'?action=new_applicant_others'; ?>" class="form-aes" id="form-others">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('Date of birth','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" autocomplete="off" type="date" id="birth_date_student" name="birth_date_student" required>
                        <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult" value="1">
                        <span id="dontBeAdult" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('Another student of legal age cannot register','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('Type document','aes'); ?><span class="required">*</span></label>
                        <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument()" required>
                            <option value="passport"><?= __('Passport','aes'); ?></option>
                            <option value="identification_document"><?= __('Identification Document','aes'); ?></option>
                            <option value="ssn"><?= __('SSN','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('ID document','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document" oninput="sendAjaxIdDocument()" required>
                        <span id="exisstudentid" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="name"><?= __('Student name','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student second name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                        <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student last name','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student second last name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                        <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="phone"><?= __('Student contact number','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="email"><?= __('Student email address','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" type="email" name="email_student" autocomplete="off" oninput="sendAjaxStudentEmailDocument()" required>
                        <span id="existstudentemail" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user','aes'); ?></span>
                        <span id="sameemailstudent" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The student cannot share the same email as the representative','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="grade"><?= __('Student grade','aes'); ?><span class="required">*</span></label>
                        <select name="grade" autocomplete="off" required>
                            <?php foreach($grades as $grade): ?>
                                <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="program"><?= __('Program of your interest','aes'); ?><span class="required">*</span></label>
                        <select name="program" autocomplete="off" required>
                            <option value="aes"><?= __('Dual diploma','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="name_institute"><?= __('Name of School or Institution with Agreement','aes'); ?><span id="institute_id_required" class="required">*</span></label>
                        <select name="institute_id" autocomplete="off" id="institute_id" required>
                            <option value=""><?= __('Select a institute','aes'); ?></option>
                            <?php foreach($institutes as $institute): ?>
                                <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                            <?php endforeach; ?>
                            <option value="other"><?= __('Other','aes'); ?></option>
                        </select>
                    </div>
                    <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="display:none;">
                        <label for="name_institute"><?= __('Name Institute','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <input type="checkbox" id="terms" name="terms" required>
                        <?= __('Accept ','aes');?>
                        <a href="https://americanelite.dreamhosters.com/terms-and-conditions/" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                            <?= __('Terms and Conditions','aes');?>
                        </a>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                        <button class="submit" id="buttonsave"><?= __('Send','aes'); ?></button>
                    </div>
                </div>
            </form>

        <?php
    } else {
        ?>
            <section class="segment">
                <div class="segment-button active" data-option="me"><?= __('Me','aes'); ?></div>
                <div class="segment-button" data-option="others"><?= __('Others','aes'); ?></div>
            </section>

            <form method="POST" action="<?= the_permalink().'?action=new_applicant_me'; ?>" class="form-aes" id="form-me">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('Type document','aes'); ?><span class="required">*</span></label>
                        <select value="<?php echo get_user_meta(get_current_user_id(),'type_document',true) ?>"  name="document_type" autocomplete="off" oninput="sendAjaxIdDocument()" required>
                            <option value="passport"><?= __('Passport','aes'); ?></option>
                            <option value="identification_document"><?= __('Identification Document','aes'); ?></option>
                            <option value="ssn"><?= __('SSN','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="id_document"><?= __('ID document','aes'); ?><span class="required">*</span></label>
                        <input value="<?php echo get_user_meta(get_current_user_id(),'id_document',true) ?>"  class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document" oninput="sendAjaxIdDocument()" required>
                        <span id="exisstudentid" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date_student"><?= __('Date of birth','aes'); ?><span class="required">*</span></label>
                        <input value="<?php echo get_user_meta(get_current_user_id(),'birth_date',true) ?>"  class="formdata" autocomplete="off" type="date" id="birth_date_student" name="birth_date_student" required>
                        <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult" value="0">
                        <span id="dontBeAdult" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('Another student of legal age cannot register','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="grade"><?= __('Student grade','aes'); ?><span class="required">*</span></label>
                        <select name="grade" autocomplete="off" required>
                            <?php foreach($grades as $grade): ?>
                                <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="program"><?= __('Program of your interest','aes'); ?><span class="required">*</span></label>
                        <select name="program" autocomplete="off" required>
                            <option value="aes"><?= __('Dual diploma','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="name_institute"><?= __('Name of School or Institution with Agreement','aes'); ?><span id="institute_id_required" class="required">*</span></label>
                        <select name="institute_id" autocomplete="off" id="institute_id" required>
                            <option value=""><?= __('Select a institute','aes'); ?></option>
                            <?php foreach($institutes as $institute): ?>
                                <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                            <?php endforeach; ?>
                            <option value="other"><?= __('Other','aes'); ?></option>
                        </select>
                    </div>
                    <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="display:none;">
                        <label for="name_institute"><?= __('Name Institute','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <input type="checkbox" id="terms" name="terms" required>
                        <?= __('Accept ','aes');?>
                        <a href="https://americanelite.dreamhosters.com/terms-and-conditions/" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                            <?= __('Terms and Conditions','aes');?>
                        </a>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                        <button class="submit" id="buttonsave"><?= __('Send','aes'); ?></button>
                    </div>
                </div>
            </form>

            <form method="POST" action="<?= the_permalink().'?action=new_applicant_others'; ?>" class="form-aes" id="form-others" style="display:none">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('Date of birth','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" autocomplete="off" type="date" id="birth_date_student" name="birth_date_student" required>
                        <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult" value="0">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('Type document','aes'); ?><span class="required">*</span></label>
                        <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument()" required>
                            <option value="passport"><?= __('Passport','aes'); ?></option>
                            <option value="identification_document"><?= __('Identification Document','aes'); ?></option>
                            <option value="ssn"><?= __('SSN','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="birth_date"><?= __('ID document','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document" oninput="sendAjaxIdDocument()" required>
                        <span id="exisstudentid" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="name"><?= __('Student name','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student second name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                        <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student last name','aes'); ?><span class="required">*</span></label>
                        <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname"><?= __('Student second last name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                        <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="phone"><?= __('Student contact number','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" required>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="email"><?= __('Student email address','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" type="email" name="email_student" autocomplete="off" oninput="sendAjaxStudentEmailDocument()" required>
                        <span id="existstudentemail" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user','aes'); ?></span>
                        <span id="sameemailstudent" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same email as the student','aes'); ?></span>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="grade"><?= __('Student grade','aes'); ?><span class="required">*</span></label>
                        <select name="grade" autocomplete="off" required>
                            <?php foreach($grades as $grade): ?>
                                <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="program"><?= __('Program of your interest','aes'); ?><span class="required">*</span></label>
                        <select name="program" autocomplete="off" required>
                            <option value="aes"><?= __('Dual diploma','aes'); ?></option>
                        </select>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="name_institute"><?= __('Name of School or Institution with Agreement','aes'); ?><span id="institute_id_required" class="required">*</span></label>
                        <select name="institute_id" autocomplete="off" id="institute_id" required>
                            <option value=""><?= __('Select a institute','aes'); ?></option>
                            <?php foreach($institutes as $institute): ?>
                                <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                            <?php endforeach; ?>
                            <option value="other"><?= __('Other','aes'); ?></option>
                        </select>
                    </div>
                    <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="display:none;">
                        <label for="name_institute"><?= __('Name Institute','aes'); ?><span class="required">*</span></label>
                        <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <input type="checkbox" id="terms" name="terms" required>
                        <?= __('Accept ','aes');?>
                        <a href="https://americanelite.dreamhosters.com/terms-and-conditions/" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                            <?= __('Terms and Conditions','aes');?>
                        </a>
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                        <button class="submit" id="buttonsave"><?= __('Send','aes'); ?></button>
                    </div>
                </div>
            </form>
        <?php
    }

    ?>
<?php } else { ?>
    <div class="title">
        <?= __('Student applicant','aes'); ?>
    </div>
    <form method="POST" action="<?php the_permalink();?>?action=save_student&idbitrix=<?php echo $_GET['idbitrix'] ?? null ?>" class="form-aes">
        <div id="loading" style="display: none; text-align: center; font-weight: 600; font-style: italic; padding: 20px">
            <p><span class="loading-dots" style="margin-right: 8px"></span> Loading student information <span class="loading-dots"></span></p>
        </div>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('Date of birth','aes'); ?><span class="required">*</span></label>
                <input class="formdata" autocomplete="off" type="date" id="birth_date_student" name="birth_date_student" required>
                <input class="formdata" autocomplete="off" type="hidden" id="dont_allow_adult" name="dont_allow_adult" value="0">
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('Type document','aes'); ?><span class="required">*</span></label>
                <select name="document_type" autocomplete="off" oninput="sendAjaxIdDocument()" required>
                    <option value="passport"><?= __('Passport','aes'); ?></option>
                    <option value="identification_document"><?= __('Identification Document','aes'); ?></option>
                    <option value="ssn"><?= __('SSN','aes'); ?></option>
                </select>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="birth_date"><?= __('ID document','aes'); ?><span class="required">*</span></label>
                <input class="formdata capitalize" autocomplete="off" type="text" id="id_document" name="id_document" oninput="sendAjaxIdDocument()" required>
                <span id="exisstudentid" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This ID is already associated with a user','aes'); ?></span>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="name"><?= __('Student name','aes'); ?><span class="required">*</span></label>
                <input class="formdata capitalize" type="text" name="name_student" autocomplete="off" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="lastname"><?= __('Student second name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                <input class="formdata capitalize" type="text" name="middle_name_student" autocomplete="off">
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="lastname"><?= __('Student last name','aes'); ?><span class="required">*</span></label>
                <input class="formdata capitalize" type="text" name="lastname_student" autocomplete="off" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="lastname"><?= __('Student second last name','aes'); ?>&nbsp;<span>(<?= __('optional','aes') ?>)</span></label>
                <input class="formdata capitalize" type="text" name="middle_last_name_student" autocomplete="off">
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="phone"><?= __('Student contact number','aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="tel" id="number_phone" name="number_phone" autocomplete="off" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="email"><?= __('Student email address','aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="email" name="email_student" autocomplete="off" oninput="sendAjaxStudentEmailDocument()" required>
                <span id="existstudentemail" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user','aes'); ?></span>
                <span id="sameemailstudent" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The student cannot share the same email as the representative','aes'); ?></span>
            </div>
            <div id="parent_name_field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="agent_name"><?= __('Parent\'s name','aes'); ?><span class="required">*</span></label>
                <input class="formdata capitalize" type="text" name="agent_name" autocomplete="off" id="agent_name" required>
            </div>
            <div id="parent-lastname-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="agent_name"><?= __('Parent\'s last name','aes'); ?><span class="required">*</span></label>
                <input class="formdata capitalize" type="text" name="agent_last_name" autocomplete="off" id="agent_last_name" required>
            </div>
            <div id="parent-country-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="phone"><?= __('Parent\'s contact number','aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="tel" id="number_partner" autocomplete="off" id="number_partner" name="number_partner" required>
            </div>
            <div id="parent-email-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="email"><?= __('Parent\'s email address','aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="email" name="email_partner" autocomplete="off" id="email_partner" oninput="sendAjaxPartnerEmailDocument()" required>
                <span id="existparentemail" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('This email is already associated with a user','aes'); ?></span>
                <span id="sameemailparent" style="font-style: italic; color: red; font-size: 12px; display: none"><?= __('The representative cannot share the same email as the student','aes'); ?></span>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="country"><?= __('Country','form-plugin'); ?><span class="required">*</span></label>
                <select name="country" autocomplete="off" required>
                <?php foreach($countries as $key => $country){ ?>
                    <option value="<?= $key ?>"><?= $country;?></option> 
                <?php } ?>
                </select>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="city"><?= __('City','aes'); ?><span class="required">*</span></label>
                <input class="formdata" type="text" name="city" autocomplete="off" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="grade"><?= __('Student grade','aes'); ?><span class="required">*</span></label>
                <select name="grade" autocomplete="off" required>
                    <?php foreach($grades as $grade): ?>
                        <option value="<?= $grade->id; ?>"><?= $grade->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="program"><?= __('Program of your interest','aes'); ?><span class="required">*</span></label>
                <select name="program" autocomplete="off" required>
                    <option value="aes"><?= __('Dual diploma','aes'); ?></option>
                </select>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="name_institute"><?= __('Name of School or Institution with Agreement','aes'); ?><span id="institute_id_required" class="required">*</span></label>
                <select name="institute_id" autocomplete="off" id="institute_id" required>
                    <option value=""><?= __('Select a institute','aes'); ?></option>
                    <?php foreach($institutes as $institute): ?>
                        <option value="<?= $institute->id; ?>"><?= $institute->name; ?></option>
                    <?php endforeach; ?>
                    <option value="other"><?= __('Other','aes'); ?></option>
                </select>
            </div>
            <div id="name-institute-field" class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="display:none;">
                <label for="name_institute"><?= __('Name Institute','aes'); ?><span class="required">*</span></label>
                <input class="formdata" autocomplete="off" type="text" id="name_institute" name="name_institute">
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <input type="checkbox" id="terms" name="terms" required>
                <?= __('Accept ','aes');?>
                <a href="https://americanelite.dreamhosters.com/terms-and-conditions/" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                    <?= __('Terms and Conditions','aes');?>
                </a>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                <button class="submit" id="buttonsave"><?= __('Send','aes'); ?></button>
            </div>
        </div>
    </form>
<?php } ?>