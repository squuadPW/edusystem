<div class="title">
    <?= __('Student applicant','aes'); ?>
</div>
<form method="POST" action="<?= the_permalink().'?action=save_student'; ?>" class="form-aes">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date"><?= __('Date of birth','aes'); ?><span class="required">*</span></label>
            <input class="formdata capitalize" autocomplete="off" type="date" id="birth_date_student" name="birth_date_student" required>
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
            <input class="formdata" type="email" name="email_student" autocomplete="off" required>
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
            <input class="formdata" type="email" name="email_partner" autocomplete="off" id="email_partner" required>
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
                <option value="aes">Dual Diploma</option>
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
            <button class="submit"><?= __('Send','aes'); ?></button>
        </div>
    </div>
</form>
    
