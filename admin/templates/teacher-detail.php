<div class="wrap">
    <?php if (isset($teacher) && !empty($teacher)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Teacher Details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Teacher', 'aes'); ?></h2>
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
            href="<?= admin_url('admin.php?page=add_admin_form_teachers_content'); ?>"><?= __('Back', 'aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_teachers_content&action=save_teacher_details'); ?>">
                            <input type="hidden" id="teacher_id" name="teacher_id" value="<?php echo $teacher->id; ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Teacher Information', 'aes'); ?></b>
                                </h3>
                            </div>

                            <div style="text-align: center;">
                                <div style="padding: 20px 0px 10px 0px;">
                                    <input type="checkbox" id="status" name="status" <?= ($teacher->status == 1) ? 'checked' : ''; ?>>   
                                    <label for="status"><b><?php _e('Active', 'aes'); ?></b></label><br>
                                </div>
                            </div>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <label for="type_document"><b><?php _e('Type document', 'aes'); ?></b></label><br>
                                            <select name="type_document" autocomplete="off" required>
                                                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                                                <option value="passport" <?= ($teacher->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'aes'); ?></option>
                                                <option value="identification_document" <?= ($teacher->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'aes'); ?></option>
                                                <option value="ssn" <?= ($teacher->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SSN', 'aes'); ?></option>
                                            </select>
                                        </th>
                                        <th scope="row">
                                            <label for="id_document"><b><?php _e('ID document', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="id_document" name="id_document" value="<?php echo $teacher->id_document; ?>">
                                        </th>
                                        
                                        <th scope="row">
                                            <label for="birth_date"><b><?php _e('Birth date', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="date" id="birth_date" name="birth_date" value="<?php echo $teacher->birth_date; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="gender"><b><?php _e('Gender', 'aes'); ?></b></label><br>
                                            <select class="form-control" id="gender" required name="gender">
                                                <option value="" selected="selected"><?= __('Select an option', 'aes'); ?></option>
                                                <option value="male" <?= ($teacher->gender == 'male') ? 'selected' : ''; ?>><?= __('Male', 'aes'); ?></option>
                                                <option value="female" <?= ($teacher->gender == 'female') ? 'selected' : ''; ?>><?= __('Female', 'aes'); ?></option>
                                            </select>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="name"><b><?php _e('Name', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="name" name="name" value="<?php echo $teacher->name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="middle_name"><b><?php _e('Middle name', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="middle_name" name="middle_name" value="<?php echo $teacher->middle_name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="last_name"><b><?php _e('Last name', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="last_name" name="last_name" value="<?php echo $teacher->last_name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="middle_last_name"><b><?php _e('Middle last name', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="middle_last_name" name="middle_last_name" value="<?php echo $teacher->middle_last_name; ?>">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="email"><b><?php _e('Email', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="email" name="email" value="<?php echo $teacher->email; ?>">
                                            <input autocomplete="off" type="hidden" id="old_email" name="old_email" value="<?php echo $teacher->email; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="phone"><b><?php _e('Phone', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="phone" name="phone" value="<?php echo $teacher->phone; ?>">
                                            <input type="hidden" name="phone_hidden" id="phone_hidden"  value="<?= $teacher->phone; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="address"><b><?php _e('Address', 'aes'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="address" name="address" value="<?php echo $teacher->address; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="password"><b><?php _e('Password', 'aes'); ?></b></label><br>
                                            <input type="password" id="password" name="password" autocomplete="off">
                                        </th>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if (isset($teacher) && !empty($teacher)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add teacher', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {

        flatpickr(document.getElementById('birth_date'), {
            dateFormat: "m/d/Y",
        });

    });
</script> -->