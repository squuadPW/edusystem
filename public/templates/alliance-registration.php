<div class="title">
   <?= __('Registration of new alliances interested in entering into agreements with FGU and AES','aes'); ?>
</div>
<form method="POST" action="<?= the_permalink(); ?>">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name"><?= __('Name','aes'); ?></label>
            <input class="formdata" type="text" name="first_name" required>
            <input type="hidden" name="action" value="save_alliances">
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Last name','aes'); ?></label>
            <input class="formdata" type="text" name="last_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname"><?= __('Name of legal representative','aes'); ?></label>
            <input class="formdata" type="text" name="name_legal" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone"><?= __('Contact Number','aes'); ?></label>
            <input class="formdata number_phone" type="tel" id="number_phone" name="number_phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email"><?= __('Email','aes'); ?></label>
            <input class="formdata" type="email" name="current_email" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('Country','aes'); ?></label>
            <select name="country">
            <?php foreach($countries as $key => $country){ ?>
                <option value="<?= $key ?>"><?= $country;?></option> 
            <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('State','aes'); ?></label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('City','aes'); ?></label>
            <input class="formdata" type="text" name="city" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city"><?= __('Address','aes'); ?></label>
            <input class="formdata" type="text" name="address" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="terms" class="checkboxes">
                <input type="checkbox" id="terms" name="terms" required>
               <?= __('I accept the terms and conditions','aes'); ?>
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="politic" class="checkboxes">
                <input type="checkbox"  id="politic" name="politic" required>    
               <?= __('I accept the Data Processing Policy','aes'); ?>
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 text-align-center">
            <button class="submit"><?= __('Send','aes'); ?></button>
        </div>
    </div>
</form>
