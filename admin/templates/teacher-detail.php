<div class="wrap">
    <?php if (isset($teacher) && !empty($teacher)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Teacher Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Teacher', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_teachers_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
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
                                    <b><?= __('Teacher Information', 'edusystem'); ?></b>
                                </h3>
                            </div>

                            <div style="text-align: center;">
                                <div style="padding: 20px 0px 10px 0px;">
                                    <input type="checkbox" id="status" name="status" <?= ($teacher->status == 1) ? 'checked' : ''; ?>>
                                    <label for="status"><b><?php _e('Active', 'edusystem'); ?></b></label><br>
                                </div>
                            </div>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <label
                                                for="type_document"><b><?php _e('Type document', 'edusystem'); ?></b></label><br>
                                            <select name="type_document" autocomplete="off" required>
                                                <option value="" selected="selected">
                                                    <?= __('Select an option', 'edusystem'); ?>
                                                </option>
                                                <option value="passport" <?= ($teacher->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'edusystem'); ?></option>
                                                <option value="identification_document"
                                                    <?= ($teacher->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'edusystem'); ?></option>
                                                <option value="ssn" <?= ($teacher->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SSN', 'edusystem'); ?></option>
                                            </select>
                                        </th>
                                        <th scope="row">
                                            <label
                                                for="id_document"><b><?php _e('ID document', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="id_document" name="id_document"
                                                value="<?php echo $teacher->id_document; ?>">
                                        </th>

                                        <th scope="row">
                                            <label
                                                for="birth_date"><b><?php _e('Birth date', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="date" id="birth_date" name="birth_date"
                                                value="<?php echo $teacher->birth_date; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="gender"><b><?php _e('Gender', 'edusystem'); ?></b></label><br>
                                            <select class="form-control" id="gender" required name="gender">
                                                <option value="" selected="selected">
                                                    <?= __('Select an option', 'edusystem'); ?>
                                                </option>
                                                <option value="male" <?= ($teacher->gender == 'male') ? 'selected' : ''; ?>><?= __('Male', 'edusystem'); ?></option>
                                                <option value="female" <?= ($teacher->gender == 'female') ? 'selected' : ''; ?>><?= __('Female', 'edusystem'); ?></option>
                                            </select>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="name"><b><?php _e('Name', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="name" name="name"
                                                value="<?php echo $teacher->name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label
                                                for="middle_name"><b><?php _e('Middle name', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="middle_name" name="middle_name"
                                                value="<?php echo $teacher->middle_name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label
                                                for="last_name"><b><?php _e('Last name', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="last_name" name="last_name"
                                                value="<?php echo $teacher->last_name; ?>">
                                        </th>
                                        <th scope="row">
                                            <label
                                                for="middle_last_name"><b><?php _e('Middle last name', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="middle_last_name"
                                                name="middle_last_name"
                                                value="<?php echo $teacher->middle_last_name; ?>">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="email"><b><?php _e('Email', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="email" name="email"
                                                value="<?php echo $teacher->email; ?>">
                                            <input autocomplete="off" type="hidden" id="old_email" name="old_email"
                                                value="<?php echo $teacher->email; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="phone"><b><?php _e('Phone', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="phone" name="phone"
                                                value="<?php echo $teacher->phone; ?>">
                                            <input type="hidden" name="phone_hidden" id="phone_hidden"
                                                value="<?= $teacher->phone; ?>">
                                        </th>
                                        <th scope="row">
                                            <label for="address"><b><?php _e('Address', 'edusystem'); ?></b></label><br>
                                            <input autocomplete="off" type="text" id="address" name="address"
                                                value="<?php echo $teacher->address; ?>">
                                        </th>
                                        <th scope="row">
                                            <label
                                                for="password"><b><?php _e('Password', 'edusystem'); ?></b></label><br>
                                            <input type="password" id="password" name="password" autocomplete="off"
                                                <?= !isset($teacher) ? 'required' : '' ?>>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if (isset($teacher) && !empty($teacher)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add teacher', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>

                        <?php if (isset($documents) && count($documents) > 0) { ?>
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Teacher Information', 'edusystem'); ?></b>
                                </h3>
                            </div>

                            <div>
                                <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="manage-column column-primary column-document-header">
                                                <?= __('Document', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class="manage-column column-upload-header">
                                                <?= __('Upload at', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class="manage-column column-status-changed-by-header">
                                                <?= __('Status changed by', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class="manage-column column-actions-header">
                                                <?= __('Actions', 'edusystem'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $key => $document) { ?>
                                            <form method="post"
                                                action="<?= admin_url('admin.php?page=add_admin_form_teachers_content&action=update_document_teacher'); ?>">
                                                <input type="hidden" name="document_id" value="<?= $document->id ?>">
                                                <input type="hidden" name="teacher_id" value="<?= $teacher->id ?>">
                                                <?php
                                                $approved_by = $document->approved_by ? get_user_by('id', $document->approved_by) : false;
                                                ?>
                                                <tr>
                                                    <td class="column-primary"
                                                        data-colname="<?= __('Document', 'edusystem'); ?>">
                                                        <?php
                                                        switch ($document->status) {
                                                            case 5:
                                                                echo '<span class="dashicons dashicons-yes-alt" style="color: green; margin-top: -5px;"></span>';
                                                                break;

                                                            case 3:
                                                                echo '<span class="declined-document" data-tippy-content="' . $document->description . '"><span class="dashicons dashicons-dismiss" style="color: red; margin-top: -5px;"></span>';
                                                                break;

                                                            case 1:
                                                                echo '<span class="dashicons dashicons-info-outline" style="color: yellow; margin-top: -5px;"></span>';
                                                                break;

                                                            default:
                                                                echo '<span class="dashicons dashicons-minus" style="color: gray; margin-top: -5px;"></span>';
                                                                break;
                                                        }
                                                        ?>
                                                        <span><?php echo $document->document_id ?></span>
                                                        <button type='button' class='toggle-row'><span
                                                                class='screen-reader-text'></span></button>
                                                    </td>
                                                    <td data-colname="<?= __('Upload at', 'edusystem'); ?>">
                                                        <?php echo $document->upload_at ?? 'N/A' ?>
                                                    </td>
                                                    <td data-colname="<?= __('Status changed by', 'edusystem'); ?>">
                                                        <?php echo $approved_by ? $approved_by->first_name . ' ' . $approved_by->last_name : 'N/A' ?>
                                                    </td>
                                                    <td data-colname="<?= __('Actions', 'edusystem'); ?>">
                                                        <?php echo $document->attachment_id != 0 ? "
    <a href=" . wp_get_attachment_url($document->attachment_id) . " target='_blank'><button type='button' class='button button-primary'>View document</button></a>" .
                                                            (($document->status) == 1 ? "
    <button name='status_id' value='3' type='button' class='button button-danger' onclick='declineDocument(" . json_encode($document) . ")'>Decline</button>
    <button name='status_id' value='5' type='submit' class='button button-success'>Approve</button>" : '') .
                                                            (($document->status) != 1 ? "
    <button name='status_id' value='1' type='submit' class='button button-warning'>Revert</button>" : '') : 'N/A' ?>
                                                    </td>
                                                </tr>
                                            </form>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id='decline-modal' class='modal' style='display:none'>
    <div class='modal-content'>
        <form method="post"
            action="<?= admin_url('admin.php?page=add_admin_form_teachers_content&action=update_document_teacher'); ?>">
            <div class="modal-header">
                <h3 style="font-size:20px;"><?= __('Decline Document') ?></h3>
                <span id="decline-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
            </div>
            <div class="modal-body" style="margin-top:10px;padding:0px;">
                <div>
                    <span>This same text will be shown to the user in their notifications section, please create a
                        message
                        addressed to the user</span>
                </div>
                <div>
                    <label for="description"><b><?= __('Reason why it is declined', 'edusystem'); ?></b><span
                            class="text-danger">*</span></label><br>
                    <textarea name="description" type="text" style="width: 100%;" required></textarea>
                    <input type="hidden" name="document_id_decline">
                    <input type="hidden" name="teacher_id_decline">
                    <input type="hidden" name="status_id_decline" value="3">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="button button-danger"><?= __('Decline', 'edusystem'); ?></button>
                <button id="decline-exit-button" type="button"
                    class="button button-outline-primary modal-close"><?= __('Exit', 'edusystem'); ?></button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    // With the above scripts loaded, you can call `tippy()` with a CSS
    // selector and a `content` prop:
    tippy('.declined-document', {
        allowHTML: true
    });
</script>