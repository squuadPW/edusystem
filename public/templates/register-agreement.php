<div class="title">
    <?= __('Institution Registration','aes'); ?>
</div>
<form method="POST" action="<?= the_permalink(); ?>">
    <div class="grid grid-cols-12 gap-4">
        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name of School or Institution','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="name_institute" required>
            <input type="hidden" name="action" value="save_institute">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('School or Institution Phone','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="tel" id="number_phone" name="number_phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('School or Institution Mail','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="email" name="current_email" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country','aes'); ?><span class="required">*</span></label>
            <select name="country">
            <?php foreach($countries as $key => $country){ ?>
                <option value="<?= $key ?>"><?= $country;?></option> 
            <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="state"><?= __('State','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="city" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="address"><?= __('Address'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="address" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level"><?= __('Educational level','aes'); ?></label>
            <select name="level">
                <option value="1"><?= __('Primary','aes'); ?></option>
                <option value="2" ><?= __('High School','aes'); ?></option>
            </select>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle text-align-center"><?= __('Contact','aes'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_name"><?= __('Rector\'s name','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="rector_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_lastname"><?= __('Rector\'s last name','aes'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="rector_lastname" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_phone"><?= __('Phone','aes'); ?><span class="required">*</span></label>
            <input class="formdata number_phone" type="tel" id="rector_phone" name="rector_phone" required>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle text-align-center"><?= __('References','aes'); ?></div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level"><?= __('How did you obtain information?','aes'); ?></label>
            <select name="reference">
                <option value="3"><?= __('Email','aes'); ?></option>
                <option value="4"><?= __('Internet search','aes'); ?></option>
                <option value="5"><?= __('On-site event','aes'); ?></option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="policy" class="checkboxes">
                <input type="checkbox" id="policy" name="policy" required>
                <?= __('I accept the Data Processing Policy','aes'); ?>
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
            <button class="submit"><?= __('Send','aes'); ?></button>
        </div>
    </div>
</form>