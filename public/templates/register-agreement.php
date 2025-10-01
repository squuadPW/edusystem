<div class="title">
    <?= $title; ?>
</div>
<form method="POST" action="<?= the_permalink(); ?>">
    <div class="grid grid-cols-12 gap-4">
        <!-- Section -->
        <input type="hidden" name="action" value="save_institute">
        <input type="hidden" name="alliance_mode" value="<?= $alliance_mode; ?>">
        <?php if (!$alliance_mode) { ?>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name of School or Institution','edusystem'); ?><span class="required">*</span></label>
                <input class="formdata" type="text" name="name_institute" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="name"><?= __('Business name','edusystem'); ?><span class="required">*</span></label>
                <input class="formdata" type="text" name="business_name" required>
            </div>
            <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                <label for="phone"><?= __('School or Institution Phone','edusystem'); ?><span class="required">*</span></label>
                <input class="formdata" type="tel" id="number_phone" name="number_phone" required>
            </div>
        <?php } ?>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email">
                <?php if ($alliance_mode !== false) {
                    echo __('Mail','edusystem');
                } else {
                    echo __('School or Institution Mail','edusystem');
                } ?>
                <span class="required">*</span>
            </label>
            <input class="formdata" type="email" name="current_email" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country','edusystem'); ?><span class="required">*</span></label>
            <select name="country">
            <?php foreach($countries as $key => $country){ ?>
                <option value="<?= $key ?>"><?= $country;?></option> 
            <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="type_calendar"><?= __('Type calendar','edusystem'); ?><span class="required">*</span></label>
            <select name="type_calendar">
                <option value="1"><?= get_type_calendar(1); ?></option> 
                <option value="2"><?= get_type_calendar(2); ?></option> 
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="state"><?= __('State','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="city" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="address"><?= __('Address'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="address" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level"><?= __('Educational level','edusystem'); ?></label>
            <select name="level">
                <option value="1"><?= __('Primary','edusystem'); ?></option>
                <option value="2" ><?= __('High School','edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Description','edusystem'); ?><span class="required">*</span></label>
            <textarea class="formdata" style="resize: none" type="text" name="description" required></textarea>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle text-align-center"><?= __('Contact','edusystem'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_name">
                <?php if ($alliance_mode !== false) {
                    echo __('Name','edusystem');
                } else {
                    echo __('Rector\'s name','edusystem');
                } ?>
                <span class="required">*</span>
            </label>
            <input class="formdata" type="text" name="rector_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_lastname">
                <?php if ($alliance_mode !== false) {
                    echo __('Last name','edusystem');
                } else {
                    echo __('Rector\'s last name','edusystem');
                } ?>
                <span class="required">*</span>
            </label>
            <input class="formdata" type="text" name="rector_lastname" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_phone"><?= __('Phone','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata number_phone" type="tel" id="rector_phone" name="rector_phone" required>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle text-align-center"><?= __('References','edusystem'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level"><?= __('How did you obtain information?','edusystem'); ?></label>
            <select name="reference">
                <option value="3"><?= __('Email','edusystem'); ?></option>
                <option value="4"><?= __('Internet search','edusystem'); ?></option>
                <option value="5"><?= __('On-site event','edusystem'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="checkbox" id="terms" name="terms" required>
            <?= __('Accept ','edusystem');?>
            <a href="<?= home_url() . '/terms-and-conditions' ?>" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                <?= __('Terms and Conditions','edusystem');?>
            </a>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
            <button class="submit"><?= __('Send','edusystem'); ?></button>
        </div>
    </div>
</form>