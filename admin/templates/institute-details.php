<div class="wrap">
    <?php if (isset($institute) && !empty($institute)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Institute Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Institute', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message']; ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php } ?>
    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php } ?>
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_institutes_content&section_tab=all_institutes'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>
    <?php if (isset($institute) && !empty($institute)): ?>
        <?php if ($institute->status == 1): ?>
            <div style="display:flex;width:100%;justify-content:end;">
                <button data-id="<?= $_GET['institute_id']; ?>" id="button-delete-institute" class="button button-danger"><span
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
                            action="<?= admin_url('admin.php?page=add_admin_institutes_content&action=save_institute_details'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                <b><?= __('Institution Information', 'edusystem'); ?></b></h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <td style="font-weight:400; width: 50%;">
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Name', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input type="text" name="name" value="<?= ucwords($institute->name); ?>"
                                                        <?= ($institute->status == 0) ? 'readonly' : 'required' ?>>
                                                    <input type="hidden" name="institute_id" id="institute_id"
                                                        value="<?= $institute->id; ?>">
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Name', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" name="name" value="" required>
                                                    <input type="hidden" name="institute_id" id="institute_id" value="">
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-weight:400; width: 50%;">
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Business name', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input type="text" name="business_name"
                                                        value="<?= ucwords($institute->business_name); ?>"
                                                        <?= ($institute->status == 0) ? 'readonly' : 'required' ?>>
                                                    <input type="hidden" name="institute_id" id="institute_id"
                                                        value="<?= $institute->id; ?>">
                                                <?php else: ?>
                                                    <label
                                                        for="input_id"><b><?= __('Business name', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" name="business_name" value="" required>
                                                    <input type="hidden" name="institute_id" id="institute_id" value="">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input type="text" id="phone" name="phone"
                                                        value="<?= $institute->phone; ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                    <input type="hidden" name="phone_hidden" id="phone_hidden"
                                                        value="<?= $institute->phone; ?>">
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" id="phone" name="phone" value="" required>
                                                    <input type="hidden" name="phone_hidden" id="phone_hidden" value="">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Email', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input type="email" name="email" value="<?= $institute->email; ?>"
                                                        <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Email', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="email" name="email" value="" required>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Country', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <?php if ($institute->status == 0): ?>
                                                        <input type="text" name="country"
                                                            value="<?= get_name_country($institute->country); ?>" readonly>
                                                    <?php elseif ($institute->status == 1): ?>
                                                        <select name="country" required id="country-selector">
                                                            <?php foreach ($countries as $key => $country) { ?>
                                                                <option value="<?= $key; ?>" <?= ($institute->country == $key) ? 'selected' : ''; ?>><?= $country ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Country', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <select name="country" required id="country-selector">
                                                        <?php foreach ($countries as $key => $country) { ?>
                                                            <option value="<?= $key; ?>"><?= $country ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <div id="state-td">
                                                        <label
                                                            for="input_id"><b><?= __('State', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                        <?php if ($institute->status == 0): ?>
                                                            <input type="text" name="country"
                                                                value="<?= get_name_state($institute->country, $institute->state); ?>"
                                                                readonly>
                                                        <?php elseif ($institute->status == 1): ?>
                                                            <select id="state-selector" name="state">
                                                                <?php foreach ($states as $key => $state) { ?>
                                                                    <option value="<?= $key; ?>" <?= $key == $institute->state ? 'selected' : ''; ?>><?= $state ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php endif; ?>
                                                        </>
                                                    <?php else: ?>
                                                        <div id="state-td" style="display: none">
                                                            <label
                                                                for="input_id"><b><?= __('State', 'edusystem'); ?></b><span
                                                                    class="text-danger">*</span></label><br>
                                                            <select id="state-selector" name="state">
                                                                <!-- states will be populated dynamically using JavaScript -->
                                                            </select>
                                                        </div>
                                                    <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('City', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input type="text" name="city" value="<?= ucwords($institute->city); ?>"
                                                        <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('City', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" name="city" value="" required>
                                                <?php endif; ?>
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

                                                    <label
                                                        for="input_id"><b><?= __('Level', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <?php if ($institute->status == 0): ?>
                                                        <input type="text" name="text"
                                                            value="<?= get_name_level($institute->level_id); ?>" readonly>
                                                    <?php elseif ($institute->status == 1): ?>
                                                        <select name="level" required>
                                                            <option value="1" <?= ($institute->level_id == 1) ? 'selected' : ''; ?>><?= __('Primary', 'edusystem'); ?></option>
                                                            <option value="2" <?= ($institute->level_id == 2) ? 'selected' : ''; ?>><?= __('High School', 'edusystem'); ?></option>
                                                        </select>
                                                    <?php endif; ?>


                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Level', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <select name="level" required>
                                                        <option value="1"><?= __('Primary', 'edusystem'); ?></option>
                                                        <option value="2"><?= __('High School', 'edusystem'); ?></option>
                                                    </select>
                                                <?php endif; ?>
                                            </th>
                                            <?php if (isset($institute) && !empty($institute)): ?>
                                                <?php if ($institute->status == 1): ?>
                                                    <td>
                                                        <label
                                                            style="font-weight:400;"><?= __('Fee', 'edusystem') . ' (%)'; ?><span
                                                                class="text-danger">*</span></label><br>
                                                        <input type="text" id="fee" name="fee" value="<?= $institute->fee; ?>">
                                                    </td>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <td>
                                                    <label
                                                        style="font-weight:400;"><?= __('Fee', 'edusystem') . ' (%)'; ?><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" id="fee" name="fee" value="10">
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                    <b><?= __('Rector Information', 'edusystem'); ?></b></h3>
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
                                                    <label
                                                        for="input_id"><b><?= __('Rector Lastname', 'edusystem'); ?></b><span
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
                                                    <input type="text" id="rector_phone" name="rector_phone" value=""
                                                        required>
                                                    <input type="hidden" id="rector_phone_hidden" name="rector_phone_hidden"
                                                        value="">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                    <b><?= __('Contact Information', 'edusystem'); ?></b></h3>
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
                                                    <input type="hidden" id="contact_phone_hidden"
                                                        name="contact_phone_hidden"
                                                        value="<?= ucwords($institute->phone_contact); ?>">
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Phone', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <input type="text" id="contact_phone" name="contact_phone" value=""
                                                        required>
                                                    <input type="hidden" id="contact_phone_hidden"
                                                        name="contact_phone_hidden" value="">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;">
                                    <b><?= __('Alliances Information', 'edusystem'); ?></b></h3>
                                <table class="form-table table-customize" style="margin-top:0px;">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="font-weight:400;">
                                                <?php if (isset($institute) && !empty($institute)): ?>
                                                    <label
                                                        for="input_id"><b><?= __('Alliance', 'edusystem'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <select name="alliance" <?= ($institute->status == 0) ? 'disabled' : 'required'; ?> style="width: 100%">
                                                        <?php foreach ($alliances as $alliance): ?>
                                                            <option value="<?= $alliance->id ?>"
                                                                <?= ($institute->alliance_id == $alliance->id) ? 'selected' : ''; ?>><?= $alliance->name ?>         <?= $alliance->last_name ?> -
                                                                <?= $alliance->code ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Alliance', 'edusystem'); ?></b><span
                                                            class="text-danger">*</span></label><br>
                                                    <select name="alliance" required style="width: 100%">
                                                        <?php foreach ($alliances as $alliance): ?>
                                                            <option value="<?= $alliance->id ?>"><?= $alliance->name ?>
                                                                <?= $alliance->last_name ?> - <?= $alliance->code ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php if (isset($institute) && !empty($institute)): ?>
                                    <?php if ($institute->status == 0): ?>
                                        <h3 style="margin-bottom:0px;text-align:center;">
                                            <b><?= __('Reference', 'edusystem'); ?></b></h3>
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
                                        <b><?= __('Reference', 'edusystem'); ?></b></h3>
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
                                    <?php if ($institute->status == 0): ?>
                                        <div
                                            style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                            <button type="button" data-id="1"
                                                data-title="<?= __('Approve Institution', 'edusystem'); ?>"
                                                data-message="<?= __('Do you want to approve this institution?', 'edusystem'); ?>"
                                                class="button button-primary change-status-institute"><?= __('Approve', 'edusystem'); ?></button>
                                            <button type="button" data-id="2"
                                                data-title="<?= __('Decline Institution', 'edusystem'); ?>"
                                                data-message="<?= __('Do pou you want to decline this institution?', 'edusystem'); ?>"
                                                class="button button-danger change-status-institute"><?= __('Declined', 'edusystem'); ?></button>
                                        </div>
                                    <?php elseif ($institute->status == 1): ?>
                                        <div
                                            style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                            <button type="submit"
                                                class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div
                                        style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
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
<?php
include(plugin_dir_path(__FILE__) . 'modal-status-institute.php');
include(plugin_dir_path(__FILE__) . 'modal-delete-institute.php');
?>