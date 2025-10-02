<div class="title">
    <?= $title; ?>
</div>
<form method="POST" action="<?= the_permalink(); ?>">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="first_name" required>
            <input type="hidden" name="action" value="save_alliances">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Last name','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="last_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Name of legal representative','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="name_legal" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('Contact Number','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata number_phone" type="tel" id="number_phone" name="number_phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('Email','edusystem'); ?><span class="required">*</span></label>
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
            <label for="city"><?= __('State','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="city" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('Address','edusystem'); ?><span class="required">*</span></label>
            <input class="formdata" type="text" name="address" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Description','edusystem'); ?><span class="required">*</span></label>
            <textarea class="formdata" style="resize: none" type="text" name="description" required></textarea>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <input type="checkbox" id="terms" name="terms" required>
            <?= __('Accept ','edusystem');?>
            <a href="<?= home_url() . '/terms-and-conditions' ?>" target="_blank" style="text-decoration: underline!important; color: #0a1c5c;">
                <?= __('Terms and Conditions','edusystem');?>
            </a>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 text-align-center">
            <button class="submit"><?= __('Send','edusystem'); ?></button>
        </div>
    </div>
</form>
