<?php
$countries = get_countries();
$institutes = get_list_institutes_active();
$grades = get_grades();

?>

<div class="wrap">

    <?php if (in_array('institutes', $roles)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Student details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Applicant details', 'aes'); ?></h2>
    <?php endif; ?>
    <div style="diplay:flex;width:100%;">
    <?php if (in_array('institutes', $roles)): ?>
        <a class="button button-outline-primary"
            href="<?php echo admin_url('/admin.php?page=list_admin_institutes_student_registered_content') ?>"><?= __('Back') ?></a>
    <?php else: ?>
        <a class="button button-outline-primary"
            href="<?php echo admin_url('/admin.php?page=add_admin_form_admission_content') ?>"><?= __('Back') ?></a>
    <?php endif; ?>
    </div>
    <div style="display:flex;width:100%;justify-content:end;">
        <button data-id="<?= $student->id; ?>" id="button-export-xlsx"
            class="button button-primary"><?= __('Export Excel', 'aes'); ?></button>
        <!-- <?php 
            global $current_user;
            $roles = $current_user->roles;
            if (in_array('administrator', $roles)) {
        ?>
            <a href="<?php echo admin_url('user-edit.php?user_id=') . $user_student->ID ?>" target="_blank">
                <button class="button button-success" style="margin-left: 10px"><?= __('View user', 'aes'); ?></button>
            </a>
        <?php } ?> -->
    </div>
    <form id="student-form" method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=save_users_details'); ?>">
        <div id="dashboard-widgets" class="metabox-holder">
            <div id="postbox-container-1" style="width:100%!important;">
                <div id="normal-sortables">
                    <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                        <div class="inside">
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <p style="text-align: center; padding: 12px !important">
                                            <?php 
                                                $moodleActive = isset($student->moodle_student_id) ? 'Yes' : 'No';
                                                $moodleActiveStyle = $moodleActive == 'Yes' ? 'style="background-color: #f98012; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px; cursor: pointer;padding: 8px"' : 'style="background-color: #dfdedd; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px;padding: 8px; cursor: not-allowed"';
                                            ?>
                                            <span class="moodle-active" data-moodle="<?php echo $moodleActive ?>" data-student_id="<?php $student->id ?>" <?php echo $moodleActiveStyle ?>><?php echo $moodleActive == 'Yes' ? 'This student has access to moodle' : 'This student does not have moodle' ?></span>
                                        </p>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label for="program"><b><?php _e('Program', 'aes'); ?></b></label><br>
                                            <input readonly type="text" id="program" name="program"
                                                value="<?php echo get_name_program($student->program_id); ?>"
                                                style="width:100%">
                                        </th>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label for="grade"><b><?php _e('Grade', 'aes'); ?></b></label><br>
                                            <select name="grade" autocomplete="off" required style="width: 100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($grades as $grade): ?>
                                                    <option value="<?= $grade->id; ?>" <?php echo $student->grade_id == $grade->id ? 'selected' : '' ?>><?= $grade->name; ?> <?= $grade->description; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </th>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label for="name_institute"><b><?php _e('Institute', 'aes'); ?></b></label><br>
                                            <select name="institute_id" autocomplete="off" id="institute_id" required style="width: 100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($institutes as $institute): ?>
                                                    <option value="<?= $institute->id; ?>" <?php echo isset($student->institute_id) && $institute->id == $student->institute_id ? 'selected' : ''; ?>>
                                                        <?= $institute->name; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <option value="other" <?php echo !isset($student->institute_id) ? 'selected' : ''; ?>><?= __('Other', 'aes'); ?></option>
                                            </select>
                                        </th>
                                    </tr>
                                    <?php if(isset($student->institute_id)) { 
                                        $style_dos = 'display: none';
                                    }
                                    ?>
                                    <tr id="institute_down" style="<?php echo $style_dos ?>">
                                        <th scope="row" colspan="3" style="font-weight:400; text-align: center">
                                            <label for="name_institute"><b><?php _e('Institute', 'aes'); ?></b></label><br>
                                            <input type="text" id="name_institute" name="name_institute"
                                                value="<?php echo strtoupper($student->name_institute); ?>"
                                                style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 style="margin-bottom:0px;text-align:center;">
                                <b><?php _e('Student Information', 'aes'); ?></b></h3>
                                <div>
                                    <?php 
                                        global $current_user;
                                        $roles = $current_user->roles;
                                        if (in_array('administrator', $roles)) {
                                    ?>
                                        <p style="text-align: center">
                                            <a href="<?php echo admin_url('user-edit.php?user_id=') . $user_student->ID ?>" target="_blank">
                                                <button type="button" class="button button-success" style="margin-left: 10px"><?= __('View user student', 'aes'); ?></button>
                                            </a>
                                        </p>
                                    <?php } ?>
                                </div>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <td scope="row" style="width: auto !important">
                                            <label
                                                for="document_type"><b><?php _e('Document Type', 'aes'); ?></b></label><br>
                                            <select name="document_type" id="document_type"
                                                value="<?php echo get_name_type_document($student->type_document); ?>"
                                                style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="identification_document"
                                                    <?= ($student->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'aes'); ?></option>
                                                <option value="passport" <?= ($student->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'aes'); ?></option>
                                                <option value="ssn" <?= ($student->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SNN', 'aes'); ?></option>
                                            </select>
                                        </td>
                                        <td style="width: auto !important">
                                            <label
                                                for="id_document"><b><?php _e('ID Document', 'aes'); ?></b></label><br>
                                            <input type="text" id="id_document" name="id_document"
                                                value="<?php echo $student->id_document; ?>" style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="id" name="id" value="<?php echo $student->id; ?>"
                                                style="width:100%" required>
                                        </td>
                                        <?php if ($user_student) { ?>
                                            <td style="width: auto !important">
                                                <label for="username"><b><?php _e('Username', 'aes'); ?></b></label><br>
                                                <input type="text" id="username" name="username"
                                                    value="<?php echo $user_student->user_nicename; ?>" style="width:100%"
                                                    required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        <?php } ?>
                                        <td style="width: auto !important">
                                            <label for="birth_date"><b><?php _e('Birth date', 'aes'); ?></b></label><br>
                                            <input type="text" id="birth_date" name="birth_date"
                                                value="<?php echo date('m/d/Y', strtotime($student->birth_date)); ?>"
                                                required style="width:100%; background-color: white;"
                                                class="birth_date" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: auto !important">
                                            <label for="first_name"><b><?php _e('First name', 'aes'); ?></b></label><br>
                                            <input type="text" id="first_name" name="first_name"
                                                value="<?php echo $student->name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td style="width: auto !important">
                                            <label
                                                for="middle_name"><b><?php _e('Middle name', 'aes'); ?></b></label><br>
                                            <input type="text" id="middle_name" name="middle_name"
                                                value="<?php echo $student->middle_name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td style="width: auto !important">
                                            <label for="last_name"><b><?php _e('Last name', 'aes'); ?></b></label><br>
                                            <input type="text" id="last_name" name="last_name"
                                                value="<?php echo $student->last_name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td style="width: auto !important">
                                            <label
                                                for="middle_last_name"><b><?php _e('Middle last name', 'aes'); ?></b></label><br>
                                            <input type="text" id="middle_last_name" name="middle_last_name"
                                                value="<?php echo $student->middle_last_name; ?>" style="width:100%"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: auto !important">
                                            <label for="gender"><b><?php _e('Gender', 'aes'); ?></b></label><br>
                                            <select name="gender" id="gender"
                                                value="<?php echo get_gender($student->gender); ?>" style="width:100%"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="male" <?= ($student->gender == 'male') ? 'selected' : ''; ?>><?= __('Male', 'aes'); ?></option>
                                                <option value="female" <?= ($student->gender == 'female') ? 'selected' : ''; ?>><?= __('Female', 'aes'); ?></option>
                                            </select>
                                        </td>

                                        <td style="width: auto !important">
                                            <label for="country"><b><?php _e('Country', 'aes'); ?></b></label><br>
                                            <select id="country" name="country"
                                                value="<?php echo get_name_country($student->country); ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($countries as $key => $country) { ?>
                                                    <option value="<?= $key ?>"
                                                        <?= (get_name_country($student->country == $key)) ? 'selected' : ''; ?>><?= $country; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>

                                        <td style="width: auto !important">
                                            <label for="city"><b><?php _e('City', 'aes'); ?></b></label><br>
                                            <input type="text" id="city" name="city"
                                                value="<?php echo $student->city; ?>" style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>

                                        <td style="width: auto !important">
                                            <label
                                                for="postal_code"><b><?php _e('Postal Code', 'aes'); ?></b></label><br>
                                            <input type="text" id="postal_code" name="postal_code"
                                                value="<?php echo $student->postal_code; ?>" style="width:100%;"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                        <td style="width: auto !important">
                                            <label for="email"><b><?php _e('Email', 'aes'); ?></b></label><br>
                                            <input type="text" id="email" name="email"
                                                value="<?php echo $student->email; ?>" style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="old_email" name="old_email"
                                                value="<?php echo $student->email; ?>" style="width:100%;">
                                        </td>
                                        <td style="width: auto !important">
                                            <label for="phone"><b><?php _e('Phone', 'aes'); ?></b></label><br>
                                            <input type="text" id="phone" name="phone"
                                                value="<?php echo $student->phone; ?>" style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>

                                        <td style="width: auto !important">
                                            <label
                                                for="academic_period"><b><?php _e('Academic period', 'aes'); ?></b></label><br>
                                            <select name="academic_period" required style="width:100%;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($periods as $key => $period) { ?>
                                                    <option value="<?php echo $period->code; ?>"
                                                        <?= ($student->academic_period == $period->code) ? 'selected' : ''; ?>>
                                                        <?php echo $period->name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td style="width: auto !important">
                                            <label
                                                for="new_password"><b><?php _e('New password for student', 'aes'); ?></label><br>
                                            <input type="password" id="new_password" name="new_password"
                                                style="width:100%; background-color: white;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 style="margin-bottom:0px;text-align:center;">
                                <b><?php _e('Parent Information', 'aes'); ?></b></h3>
                                <div>
                                    <?php 
                                        global $current_user;
                                        $roles = $current_user->roles;
                                        if (in_array('administrator', $roles)) {
                                    ?>
                                        <p style="text-align: center">
                                            <a href="<?php echo admin_url('user-edit.php?user_id=') . $partner->ID ?>" target="_blank">
                                                <button  type="button" class="button button-success" style="margin-left: 10px"><?= __('View user parent', 'aes'); ?></button>
                                            </a>
                                        </p>
                                    <?php } ?>
                                </div>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label
                                                for="parent_document_type"><b><?php _e('Document Type', 'aes'); ?></b></label><br>
                                            <select name="parent_document_type" id="parent_document_type"
                                                value="<?php echo get_user_meta($partner->ID, 'type_document', true); ?>"
                                                style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="identification_document"
                                                    <?= (get_user_meta($partner->ID, 'type_document', true) == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'aes'); ?>
                                                </option>
                                                <option value="passport"
                                                    <?= (get_user_meta($partner->ID, 'type_document', true) == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'aes'); ?></option>
                                                <option value="ssn"
                                                    <?= (get_user_meta($partner->ID, 'type_document', true) == 'ssn') ? 'selected' : ''; ?>><?= __('SNN', 'aes'); ?></option>
                                            </select>
                                        </th>
                                        <td>
                                            <label
                                                for="parent_id_document"><b><?php _e('ID Document', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_id_document" name="parent_id_document"
                                                value="<?php echo get_user_meta($partner->ID, 'id_document', true); ?>"
                                                style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="parent_id" name="parent_id"
                                                value="<?php echo $partner->ID; ?>" style="width:100%" required>
                                        </td>
                                        <td>
                                            <label
                                                for="parent_username"><b><?php _e('Username', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_username" name="parent_username"
                                                value="<?php echo $partner->user_nicename; ?>" style="width:100%"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label
                                                for="parent_first_name"><b><?php _e('First name', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_first_name" name="parent_first_name"
                                                value="<?php echo $partner->first_name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </th>
                                        <td>
                                            <label
                                                for="parent_last_name"><b><?php _e('Last name', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_last_name" name="parent_last_name"
                                                value="<?php echo $partner->last_name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td>
                                            <label
                                                for="parent_birth_date"><b><?php _e('Birth date', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_birth_date" name="parent_birth_date"
                                                value="<?php echo get_user_meta($partner->ID, 'birth_date', true); ?>"
                                                style="width:100%; background-color: white;" class="birth_date"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label for="parent_gender"><b><?php _e('Gender', 'aes'); ?></b></label><br>
                                            <select name="parent_gender" id="parent_gender"
                                                value="<?php echo get_user_meta($partner->ID, 'gender', true); ?>"
                                                style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="male"
                                                    <?= (get_user_meta($partner->ID, 'gender', true) == 'male') ? 'selected' : ''; ?>><?= __('Male', 'aes'); ?></option>
                                                <option value="female"
                                                    <?= (get_user_meta($partner->ID, 'gender', true) == 'female') ? 'selected' : ''; ?>><?= __('Female', 'aes'); ?></option>
                                            </select>
                                        </th>
                                        <td>
                                            <label for="parent_country"><b><?php _e('Country', 'aes'); ?></b></label><br>
                                            <select id="parent_country" name="parent_country"
                                                value="<?php echo get_name_country(get_user_meta($partner->ID, 'billing_country', true)); ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($countries as $key => $country) { ?>
                                                    <option value="<?= $key ?>"
                                                        <?= (get_name_country(get_user_meta($partner->ID, 'billing_country', true) == $key)) ? 'selected' : ''; ?>><?= $country; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label for="parent_city"><b><?php _e('City', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_city" name="parent_city"
                                                value="<?php echo get_user_meta($partner->ID, 'billing_city', true) ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label
                                                for="parent_postal_code"><b><?php _e('Postal Code', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_postal_code" name="parent_postal_code"
                                                value="<?php echo get_user_meta($partner->ID, 'billing_postcode', true) ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </th>
                                        <td>
                                            <label for="parent_email"><b><?php _e('Email', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_email" name="parent_email"
                                                value="<?php echo $partner->user_email ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="parent_old_email" name="parent_old_email"
                                                value="<?php echo $partner->user_email; ?>">
                                        </td>
                                        <td>
                                            <label for="parent_phone"><b><?php _e('Phone', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_phone" name="parent_phone"
                                                value="<?php echo get_user_meta($partner->ID, 'billing_phone', true) ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label
                                                for="parent_occupation"><b><?php _e('Occupation', 'aes'); ?></b></label><br>
                                            <input type="text" id="parent_occupation" name="parent_occupation"
                                                value="<?php echo get_user_meta($partner->ID, 'occupation', true) ?>"
                                                style="width:100%;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if (!in_array('institutes', $roles) && !current_user_can('only_read_admission_aes')): ?>
                                <p style="text-align: end">
                                <input type="submit" value="<?php _e('Save Changes', 'aes'); ?>"
                                    class="button button-primary">
                            </p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php if (!in_array('institutes', $roles) && !current_user_can('only_read_admission_aes')): ?>
        <h2 style="margin-bottom:15px;"><?= __('Documents', 'aes'); ?></h2>
        <div id="notice-status" class="notice-custom notice-info" style="display:none;">
            <p><?= __('Status change successfully', 'aes'); ?></p>
        </div>
        <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary column-title"><?= __('Document', 'aes') ?></th>
                    <th scope="col" class="manage-column column-title-translate"><?= __('Status', 'aes') ?></th>
                    <th scope="col" class="manage-column column-price"><?= __('Actions', 'aes') ?></th>
                </tr>
            </thead>
            <tbody id="table-documents">
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $document): ?>
                        <tr id="<?= 'tr_document_' . $document->id; ?>">
                            <td class="column-primary">
                                <?= $name = get_name_document($document->document_id); ?>
                                <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                            </td>
                            <td id="<?= 'td_document_' . $document->document_id; ?>" data-colname="<?= __('Status', 'aes'); ?>">
                                <b>
                                    <?= $status = get_status_document($document->status); ?>
                                </b>
                            </td>
                            <td data-colname="<?= __('Actions', 'aes'); ?>">
                                <?php if ($document->status > 0): ?>
                                    <a target="_blank" onclick='watchDetails(<?= json_encode($document) ?>)'><button type="button" class="button button-primary-outline other-buttons-document"><?= __('View detail', 'aes'); ?></button></a>
                                    <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>"><button type="button" class="button button-primary other-buttons-document"><?= __('View documment', 'aes'); ?></button></a>
                                    <?php if ($document->status != 1) { ?>
                                        <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                            data-status="1" class="button change-status button-warning"><?= __('Revert', 'aes'); ?></button>
                                    <?php } ?>
                                    <?php if ($document->status != 5 && $document->status != 3): ?>
                                        <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                            data-status="5" class="button change-status button-success"><?= __('Approve', 'aes'); ?></button>
                                    <?php endif; ?>
                                    <?php if ($document->status != 5 && $document->status != 3): ?>
                                        <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                            data-status="3" class="button change-status button-danger"><?= __('Decline', 'aes'); ?></button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (in_array('administrator', haystack: $roles) || in_array('admision', haystack: $roles)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Payments', 'aes'); ?></h2>
        <div id="notice-status" class="notice-custom notice-info" style="display:none;">
            <p><?= __('Status change successfully', 'aes'); ?></p>
        </div>
        <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" style="text-align: center" class="manage-column column-primary column-title"><?= __('Fee registration', 'aes') ?></th>
                    <th scope="col" style="text-align: center" class="manage-column column-title-translate"><?= __('Product payment', 'aes') ?></th>
                    <th scope="col" style="text-align: center" class="manage-column column-price"><?= __('Fee graduation', 'aes') ?></th>
                </tr>
            </thead>
            <tbody id="table-documents">
                <tr id="<?= 'tr_payment_'; ?>">
                    <td style="text-align: center" id="<?= 'td_payment_fee'; ?>" data-colname="<?= __('Fee registration', 'aes'); ?>">
                        <b>
                            <?= isset($fee_payment_ready) && $fee_payment_ready ? '<span class="dashicons dashicons-yes-alt" style="color: green"></span>' : '<span class="dashicons dashicons-dismiss" style="color: red"></span>'; ?>
                        </b>
                    </td>
                    <td style="text-align: center" id="<?= 'td_payment_product'; ?>" data-colname="<?= __('Product payment', 'aes'); ?>">
                        <b>
                            <?= isset($product_ready) && $product_ready ? '<span class="dashicons dashicons-yes-alt" style="color: green"></span>' : '<span class="dashicons dashicons-dismiss" style="color: red"></span>'; ?>
                        </b>
                    </td>
                    <td style="text-align: center" id="<?= 'td_payment_graduation'; ?>" data-colname="<?= __('Fee graduation', 'aes'); ?>">
                        <b>
                            <?= isset($fee_graduation_ready) && $fee_graduation_ready ? '<span class="dashicons dashicons-yes-alt" style="color: green"></span>' : '<span class="dashicons dashicons-dismiss" style="color: red"></span>'; ?>
                        </b>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<div id='decline-modal' class='modal' style='display:none'>
	<div class='modal-content'>
		<div class="modal-header">
		<h3 style="font-size:20px;"><?= __('Decline Document') ?></h3>
			<span id="decline-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<div class="modal-body" style="margin-top:10px;padding:0px;">
            <div>
                <span>This same text will be shown to the user in their notifications section, please create a message addressed to the user</span>
            </div>
            <div>
                <label for="decline-description"><b><?= __('Reason why it is declined','aes'); ?></b><span class="text-danger">*</span></label><br>
                <textarea name="decline-description" type="text" style="width: 100%;"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button id="decline-save" type="submit" class="button button-danger"><?= __('Decline','aes'); ?></button>
            <button id="decline-exit-button" type="button" class="button button-outline-primary modal-close"><?= __('Exit','aes'); ?></button>
        </div>
	</div>
</div>


<div id='detail-modal' class='modal' style='display:none'>
	<div class='modal-content' style="width: 70%;">
		<div class="modal-header">
		<h3 style="font-size:20px;"><?= __('Detail Document') ?></h3>
			<span id="detail-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
		</div>
		<div class="modal-body" style="padding:10px;">
            <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class=" manage-column column"><?= __('Date user registered', 'aes'); ?></th>
                    <th scope="col" class=" manage-column column-primary"><?= __('Date upload documents', 'aes'); ?></th>
                    <th scope="col" class=" manage-column column-email"><?= __('Date status change', 'aes'); ?></th>
                    <th scope="col" class=" manage-column column-email"><?= __('Status changed by', 'aes'); ?></th>
                    <th scope="col" class=" manage-column column-email"><?= __('Reason of decline', 'aes'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td" id="date_user_registered"></td>
                    <td class="td" id="date_upload_documents"></td>
                    <td class="td" id="date_status_change"></td>
                    <td class="td" id="status_changed_by"></td>
                    <td class="td" id="description_status_changed"></td>
                </tr>
            </tbody>
        </table>
        </div>
        <div class="modal-footer">
            <button id="detail-exit-button" type="button" class="button button-outline-primary modal-close"><?= __('Exit','aes'); ?></button>
        </div>
	</div>
</div>

<script>
    let institute_id = document.getElementById('institute_id');
    institute_id.addEventListener('change', function (e) {
        document.querySelector('input[name=name_institute]').value = '';
        if (e.target.value == 'other') {
            document.getElementById('institute_down').style.display = 'contents';
            document.querySelector('input[name=name_institute]').required = true;
        } else {
            document.getElementById('institute_down').style.display = 'none';
            document.querySelector('input[name=name_institute]').required = false;
        }
        
    })
</script>