<div class="wrap">
    <?php if (isset($institute) && !empty($institute)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Institute Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Institute', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>
    <?php if (isset($institute) && !empty($institute)): ?>
        <?php if ($institute->status == 1): ?>
            <div style="display:flex;width:100%;justify-content:end;">
                <button data-id="<?= $_GET['institute_id']; ?>" id="button-delete-institute-alliance"
                    class="button button-danger"><span
                        class="dashicons dashicons-trash"></span><?= __('Delete', 'edusystem'); ?></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=save_institute_details'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                <b><?= __('Institution Information', 'edusystem'); ?></b>
                            </h3>
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Name', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="name"
                                                value="<?= isset($institute) ? ucwords($institute->name) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                            <input type="hidden" name="institute_id" id="institute_id"
                                                value="<?= isset($institute) ? $institute->id : ''; ?>">
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Business name', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="business_name"
                                                value="<?= isset($institute) ? ucwords($institute->business_name) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Level', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <?php if (isset($institute) && $institute->status == 0): ?>
                                                <input type="text" name="text"
                                                    value="<?= get_name_level($institute->level_id); ?>" readonly>
                                            <?php else: ?>
                                                <select name="level" required>
                                                    <option value="1" <?= (isset($institute) && $institute->level_id == 1) ? 'selected' : ''; ?>><?= __('Primary', 'edusystem'); ?></option>
                                                    <option value="2" <?= (isset($institute) && $institute->level_id == 2) ? 'selected' : ''; ?>><?= __('High School', 'edusystem'); ?></option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" id="phone" name="phone"
                                                value="<?= isset($institute) ? $institute->phone : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <input type="hidden" name="phone_hidden" id="phone_hidden"
                                                value="<?= isset($institute) ? $institute->phone : ''; ?>">
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Email', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="email" name="email"
                                                value="<?= isset($institute) ? $institute->email : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required'; ?>>
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Type calendar', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <?php if (isset($institute) && $institute->status == 0): ?>
                                                <input type="text" name="text"
                                                    value="<?= get_type_calendar((int) $institute->type_calendar); ?>"
                                                    readonly>
                                            <?php else: ?>
                                                <select name="type_calendar" required>
                                                    <option value="1" <?= (isset($institute) && $institute->type_calendar == 1) ? 'selected' : ''; ?>><?= get_type_calendar(1); ?></option>
                                                    <option value="2" <?= (isset($institute) && $institute->type_calendar == 2) ? 'selected' : ''; ?>><?= get_type_calendar(2); ?></option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('Country', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <?php if (isset($institute) && $institute->status == 0): ?>
                                                <input type="text" name="country"
                                                    value="<?= get_name_country($institute->country); ?>" readonly>
                                            <?php else: ?>
                                                <select name="country" required id="country-selector">
                                                    <?php foreach ($countries as $key => $country) { ?>
                                                        <option value="<?= $key; ?>" <?= (isset($institute) && $institute->country == $key) ? 'selected' : ''; ?>><?= $country ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <div id="state-td" <?= (!isset($institute) || $institute->status == 0) ? 'style="display: none"' : ''; ?>>
                                                <label
                                                    for="input_id"><b><?= __('State', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <?php if (isset($institute) && $institute->status == 0): ?>
                                                    <input type="text" name="country"
                                                        value="<?= get_name_state($institute->country, $institute->state); ?>"
                                                        readonly>
                                                <?php else: ?>
                                                    <select id="state-selector" name="state">
                                                        <?php foreach ($states as $key => $state) { ?>
                                                            <option value="<?= $key; ?>" <?= (isset($institute) && $key == $institute->state) ? 'selected' : ''; ?>><?= $state ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="font-weight:400; width: 33%;">
                                            <label
                                                for="input_id"><b><?= __('City', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="city"
                                                value="<?= isset($institute) ? ucwords($institute->city) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required'; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight:400; width: 25%;">
                                            <label
                                                for="input_id"><b><?= __('Lower text', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="lower_text"
                                                value="<?= isset($institute) ? ucwords($institute->lower_text) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                        </td>
                                        <td style="font-weight:400; width: 25%;">
                                            <label
                                                for="input_id"><b><?= __('Middle text', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="middle_text"
                                                value="<?= isset($institute) ? ucwords($institute->middle_text) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                        </td>
                                        <td style="font-weight:400; width: 25%;">
                                            <label
                                                for="input_id"><b><?= __('Upper text', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="upper_text"
                                                value="<?= isset($institute) ? ucwords($institute->upper_text) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                        </td>
                                        <td style="font-weight:400; width: 25%;">
                                            <label
                                                for="input_id"><b><?= __('Graduated text', 'edusystem'); ?></b><?= (isset($institute) && $institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="graduated_text"
                                                value="<?= isset($institute) ? ucwords($institute->graduated_text) : ''; ?>"
                                                <?= (isset($institute) && $institute->status == 0) ? 'readonly' : 'required' ?>>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Address', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <textarea name="address" row="8" style="resize:none; width: 100%"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>><?= $institute->address; ?></textarea>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Address', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <textarea name="address" row="8" style="resize:none; width: 100%"
                                                    required></textarea>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Description', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <textarea name="description" row="8" style="resize:none; width: 100%"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>><?= $institute->description; ?></textarea>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Description', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <textarea name="description" row="8" style="resize:none; width: 100%"
                                                    required></textarea>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <?php if ($institute->status == 1): ?>
                                                <td>
                                                    <label style="font-weight:400;"><?= __('Fee', 'edusystem') . ' (%)'; ?><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" id="fee" name="fee" value="<?= $institute->fee; ?>">
                                                </td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <td>
                                                <label style="font-weight:400;"><?= __('Fee', 'edusystem') . ' (%)'; ?><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" id="fee" name="fee" value="10">
                                            </td>
                                        <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                <b><?= __('Rector Information', 'edusystem'); ?></b>
                            </h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Rector Name', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="rector_name"
                                                    value="<?= ucwords($institute->name_rector); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Rector Name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="rector_name" value="" required>
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Rector Lastname', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="rector_last_name"
                                                    value="<?= ucwords($institute->lastname_rector); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Rector Lastname', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="rector_last_name" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" id="rector_phone" name="rector_phone"
                                                    value="<?= ucwords($institute->phone_rector); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <input type="hidden" id="rector_phone_hidden" name="rector_phone_hidden"
                                                    value="<?= ucwords($institute->phone_rector); ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" id="rector_phone" name="rector_phone" value="" required>
                                                <input type="hidden" id="rector_phone_hidden" name="rector_phone_hidden"
                                                    value="">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                <b><?= __('Contact Information', 'edusystem'); ?></b>
                            </h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Contact Name', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="contact_name"
                                                    value="<?= ucwords($institute->name_contact); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Contact Name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="contact_name" value="" required>
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Contact Lastname', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="contact_last_name"
                                                    value="<?= ucwords($institute->lastname_contact); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label
                                                    for="input_id"><b><?= __('Contact Lastname', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="contact_last_name" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <label
                                                    for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" id="contact_phone" name="contact_phone"
                                                    value="<?= ucwords($institute->phone_contact); ?>"
                                                    <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <input type="hidden" id="contact_phone_hidden" name="contact_phone_hidden"
                                                    value="<?= ucwords($institute->phone_contact); ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" id="contact_phone" name="contact_phone" value=""
                                                    required>
                                                <input type="hidden" id="contact_phone_hidden" name="contact_phone_hidden"
                                                    value="">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if (isset($institute) && !empty($institute)): ?>
                                <?php if ($institute->status == 0): ?>
                                    <h3 style="margin-bottom:0px;text-align:center;">
                                        <b><?= __('Reference', 'edusystem'); ?></b>
                                    </h3>
                                    <table class="form-table" style="margin-top:0px;">
                                        <tbody>
                                            <tr>
                                                <th scope="row" style="font-weight:400;">
                                                    <label
                                                        for="input_id"><b><?= __('Reference', 'edusystem'); ?></b></label><br>
                                                    <input type="text" name="reference"
                                                        value="<?= get_name_reference($institute->reference); ?>" readonly>
                                                </th>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            <?php else: ?>
                                <h3 style="margin-bottom:0px;text-align:center;">
                                    <b><?= __('Reference', 'edusystem'); ?></b>
                                </h3>
                                <table class="form-table" style="margin-top:0px;">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="font-weight:400;">
                                                <label for="input_id"><b><?= __('Reference', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <select name="reference">
                                                    <option value="3"><?= __('Email', 'edusystem'); ?></option>
                                                    <option value="4"><?= __('Internet search', 'edusystem'); ?></option>
                                                    <option value="5"><?= __('On-site event', 'edusystem'); ?></option>
                                                </select>
                                            </th>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <?php if (isset($institute) && !empty($institute)): ?>
                                <?php if ($institute->status == 1): ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="submit"
                                            class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add Institute', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>