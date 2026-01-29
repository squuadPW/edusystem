<?php
global $current_user;
$roles = $current_user->roles;
$hide_grade_student = get_option('hide_grade_student');

$countries = get_countries();
$institutes = get_list_institutes_active();
$grades = get_grades();
$url = wp_get_attachment_url($student->profile_picture);
function truncate_text($text, $max_length = 100)
{
    if (strlen($text) > $max_length) {
        return substr($text, 0, $max_length) . '...';
    }
    return $text;
}
// Fecha según configuración de idioma: español -> d/m/Y, por defecto -> m/d/Y
$date_format = (strpos(get_locale(), 'es') === 0) ? 'd/m/Y' : 'm/d/Y';
$datetime_format = $date_format . ' H:i:s';
?>

<div class="wrap">

    <?php if (in_array('institutes', $roles)): ?>
        <h2 style="margin-bottom:15px;"><?= esc_html__('Student details', 'edusystem') ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= esc_html__('Applicant details', 'edusystem') ?></h2>
    <?php endif; ?>
    <div style="diplay:flex;width:100%;">
        <?php if (in_array('institutes', $roles)): ?>
            <a class="button button-outline-primary"
                href="<?php echo admin_url('/admin.php?page=list_admin_institutes_student_registered_content') ?>"><?= esc_html__('Back', 'edusystem') ?></a>
        <?php else: ?>
            <a class="button button-outline-primary"
                href="<?php echo admin_url('/admin.php?page=add_admin_form_admission_content') ?>"><?= esc_html__('Back', 'edusystem') ?></a>
        <?php endif; ?>
    </div>
    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>
    <div class="action-student-admission">
        <?php
        include(plugin_dir_path(__FILE__) . 'connections-student.php');
        ?>
        <?php if (current_user_can('can_regenerate_projection')) { ?>
            <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=generate_academic_projection_student&student_id=') . $student->id . '&projection_id=' . $projection->id ?>"
                class="button button-outline-primary"
                onclick="return confirm('<?= esc_html__('Are you sure you want to regenerate academic projections?', 'edusystem') ?>');"><?= esc_html__('Re-generate projection', 'edusystem') ?></a>
        <?php } ?>
        <a style="margin-left: 5px;"
            href="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=generate_virtual_classroom&student_id=') . $student->id ?>"
            class="button button-outline-primary"
            onclick="return confirm('<?= esc_html__('Are you sure you want to create a virtual classroom for this student?', 'edusystem') ?>');"><?= esc_html__('Send to virtual classroom', 'edusystem') ?></a>
        <a style="margin-left: 5px;"
            href="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=generate_admin&student_id=') . $student->id ?>"
            class="button button-outline-primary"
            onclick="return confirm('<?= esc_html__('Are you sure you want to send this student to the admin manually?', 'edusystem') ?>');"><?= esc_html__('Send to admin', 'edusystem') ?></a>
        <button style="margin-left: 5px;" data-id="<?= $student->id; ?>" id="button-export-xlsx"
            class="button button-primary"><?= esc_html__('Export Excel', 'edusystem') ?></button>
        <!-- <?php
                global $current_user;
                $roles = $current_user->roles;
                if (in_array('administrator', $roles)) {
                ?>
            <a href="<?php echo admin_url('user-edit.php?user_id=') . $user_student->ID ?>" target="_blank">
                <button class="button button-success" style="margin-left: 10px"><?= esc_html__('View user', 'edusystem') ?></button>
            </a>
        <?php } ?> -->
    </div>

    <!-- <div class="status-student-datails" >
        <?php
            
            $background = "#dfdedd";
            $color = "#black";

            switch ($student->status_id):
                case 0:
                    $Status = esc_html__('is pending payment','edusystem');
                    break;

                case 1:
                    $Status = esc_html__('payment has been approved','edusystem');  
                    $background = "#ffc107";
                    $color = "black";
                    break;
                    
                case 2:
                    $Status = esc_html__('documents have been approved','edusystem'); 
                    $background = "#2271b1";
                    $color = "white";
                    break;
                    
                case 3:
                    $Status = esc_html__('has administrative clearance','edusystem');
                    $background = "#4caf50";
                    $color = "white";  
                    break;
                    
                case 6:
                    $Status = esc_html__('has been withdrawn','edusystem');
                    break;
                
                default:
                    $Status = esc_html__('has an unknown status','edusystem');
                    $background = "#f44336";
                    $color = "white";
            endswitch;
        ?>

        <label style="background:<?=$background?>;color:<?=$color?>;" ><?=  esc_html__('The student','edusystem')." {$Status}" ?></label>
    </div> -->


    <?php if (current_user_can('manager_statusbar_student')): ?>
        <!-- <h2 style="margin-bottom:15px; text-align: center;"><?= __('Status student', 'edusystem'); ?></h2> -->
            <div id="notice-status" class="notice-custom notice-info" style="display:none;">
            <p><?= esc_html__('Status change successfully', 'edusystem') ?></p>
        </div>
        <div id="product-status-container" class="status-grid-wrapper">
            <div class="status-grid header-row">
                <div class="status-item header">
                    <?= esc_html__('Registration fee paid', 'edusystem') ?>
                </div>
                <div class="status-item header">
                    <?= esc_html__('Paid program', 'edusystem') ?>
                </div>
                <div class="status-item header">
                    <?= esc_html__('Graduation fee paid', 'edusystem') ?>
                </div>
                <div class="status-item header">
                    <?= esc_html__('Approved documents', 'edusystem') ?>
                </div>
                <div class="status-item header">
                    <?= esc_html__('Academic requirements fulfilled', 'edusystem') ?>
                </div>
            </div>

            <div class="status-grid data-row">
                <div class="status-item data" data-colname="<?= esc_attr(esc_html__('Registration fee paid', 'edusystem')); ?>">
                    <?= isset($fee_payment_ready) && $fee_payment_ready ? '<span class="status-icon status-approved"></span>' : '<span class="status-icon status-pending"></span>'; ?>
                </div>
                <div class="status-item data" data-colname="<?= esc_attr(esc_html__('Paid program', 'edusystem')); ?>">
                    <?= isset($product_ready) && $product_ready ? ($product_ready == 1 ? '<span class="status-icon status-approved"></span>' : '<span class="status-icon status-partial"></span>') : '<span class="status-icon status-pending"></span>'; ?>
                </div>
                <div class="status-item data" data-colname="<?= esc_attr(esc_html__('Graduation fee paid', 'edusystem')); ?>">
                    <?= isset($fee_graduation_ready) && $fee_graduation_ready ? '<span class="status-icon status-approved"></span>' : '<span class="status-icon status-pending"></span>'; ?>
                </div>
                <div class="status-item data" data-colname="<?= esc_attr(esc_html__('Approved documents', 'edusystem')); ?>">
                    <?= isset($documents_ready) && $documents_ready ? '<span class="status-icon status-approved"></span>' : '<span class="status-icon status-pending"></span>'; ?>
                </div>
                <div class="status-item data"
                    data-colname="<?= esc_attr(esc_html__('Academic requirements fulfilled', 'edusystem')); ?>">
                    <?= isset($academic_ready) && $academic_ready ? '<span class="status-icon status-approved"></span>' : '<span class="status-icon status-pending"></span>'; ?>
                </div>
            </div>
        </div>
                <?php if ($student->status_id < 5 && ($fee_payment_ready && $product_ready && $fee_graduation_ready && $documents_ready && $academic_ready)) { ?>
            <form method="post"
                action="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=update_status_student&status_id=5&student_id=' . $student->id); ?>">
                <p style="text-align: center">
                            <input type="submit" value="<?= esc_html__('Graduate student', 'edusystem') ?>" class="button button-primary">
                </p>
            </form>
        <?php } ?>
    <?php endif; ?>
    <?php
    do_action('extras_student', $user_student);
    ?>
    <form id="student-form" method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=save_users_details'); ?>">
        <div id="dashboard-widgets" class="metabox-holder">
            <div id="postbox-container-1" style="width:100%!important;">
                <div id="normal-sortables">
                    <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                        <div class="inside">
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <?php if (isset($url) && $url != '') { ?>
                                        <tr>
                                            <div
                                                style="height: 100px; width: 100px; background-color: gray; margin: auto; border-radius: 100%; overflow: hidden; position: relative; border: 3px solid #E71F3B; margin: 20px auto;">
                                                <img decoding="async" src="<?= $url ?>"
                                                    style="height: auto; width: 100%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -45%);"
                                                    alt="">
                                            </div>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <p style="text-align: center; padding: 12px !important">
                                            <?php
                                            if (!function_exists('edusystem_get_student_classroom_access')) {
                                                require_once dirname(__DIR__) . '/student-access-helper.php';
                                            }

                                            $access_info = edusystem_get_student_classroom_access($student);
                                            $hasMoodleAccess = $access_info['has_moodle'];
                                            $statusText = $access_info['status_text'];
                                            $backgroundColor = $access_info['background_color'];

                                            $style = "background-color: $backgroundColor; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px; padding: 8px;";
                                            $style .= $hasMoodleAccess ? ' cursor: pointer;' : ' cursor: not-allowed;';
                                            ?>

                                            <span class="moodle-active"
                                                data-moodle="<?php echo $hasMoodleAccess ? 'Yes' : 'No'; ?>"
                                                data-student_id="<?php echo $student->id; ?>"
                                                style="<?php echo $style; ?>">
                                                <?php echo esc_html($statusText); ?>
                                            </span>
                                            <?php if (!empty($access_info['error'])): ?>
                                                <p class="moodle-access-error" style="color:#b71c1c;text-align:center;margin-top:8px;padding:0 12px;"><?php echo esc_html($access_info['error']); ?></p>
                                            <?php endif; ?>
                                        </p>
                                    </tr>
                                    <tr>
                                        <?php if ($hide_grade_student !== 'on') { ?>
                                            <th scope="row" style="font-weight:400; text-align: center">
                                                <label for="grade"><b><?= esc_html__('Grade', 'edusystem') ?></b></label><br>
                                                <select name="grade" autocomplete="off" required style="width: 100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                    <?php foreach ($grades as $grade): ?>
                                                        <option value="<?= $grade->id; ?>" <?php echo $student->grade_id == $grade->id ? 'selected' : '' ?>>
                                                            <?= $grade->name; ?> <?= $grade->description; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </th>
                                        <?php } ?>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label
                                                for="academic_period"><b><?= esc_html__('School Year', 'edusystem') ?></b></label><br>
                                            <select name="academic_period" required
                                                style="width: 100%; <?= ($student->academic_period == 'noperiod' || $student->academic_period == 'out') ? 'background-color: red; color: white;' : '' ?>"
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="" <?= ($student->academic_period == 'noperiod' || $student->academic_period == 'out') ? 'selected' : '' ?>>
                                                    <?= esc_html__('Out of school year', 'edusystem') ?>
                                                </option>
                                                <?php foreach ($periods as $key => $period) { ?>
                                                    <option value="<?= $period->code ?>"
                                                        <?= $student->academic_period == $period->code ? 'selected' : '' ?>>
                                                        <?= $period->name ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </th>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label
                                                for="academic_period_cut"><b><?= esc_html__('Student\'s entry cut', 'edusystem') ?></b></label><br>
                                            <select name="academic_period_cut" required
                                                style="width: 100%; <?= ($student->initial_cut == 'noperiod' || $student->initial_cut == 'out') ? 'background-color: red; color: white;' : '' ?>"
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>
                                                data-initial-cut="<?= htmlspecialchars($student->initial_cut) ?>">
                                                <option value="" <?= $student->initial_cut == 'nocut' || $student->initial_cut == 'out' ? 'selected' : '' ?>>
                                                    <?= esc_html__('Out of term', 'edusystem') ?>
                                                </option>
                                                <?php foreach ($periods_cuts as $key => $cut) { ?>
                                                    <option value="<?= $cut->cut ?>" <?= $student->initial_cut == $cut->cut ? 'selected' : '' ?>><?= $cut->cut ?></option>
                                                <?php } ?>
                                            </select>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight:400; text-align: center">
                                            <label for="program"><b><?= esc_html__('Program', 'edusystem') ?></b></label><br>
                                            <input readonly type="text" id="program" name="program"
                                                value="<?php echo get_name_program_student($student->id); ?>"
                                                style="width:100%">
                                        </th>
                                        <th scope="row" style="font-weight:400; text-align: center">
                                            <label
                                                for="name_institute"><b><?= esc_html__('Institute', 'edusystem') ?></b></label><br>
                                            <select name="institute_id" autocomplete="off" id="institute_id" required
                                                style="width: 100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($institutes as $institute): ?>
                                                    <option value="<?= $institute->id; ?>" <?php echo isset($student->institute_id) && $institute->id == $student->institute_id ? 'selected' : ''; ?>>
                                                        <?= $institute->name; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <option value="other" <?php echo !isset($student->institute_id) ? 'selected' : ''; ?>><?= esc_html__('Other', 'edusystem') ?></option>
                                            </select>
                                        </th>
                                    </tr>
                                    <?php if (isset($student->institute_id)) {
                                        $style_dos = 'display: none';
                                    }
                                    ?>
                                    <tr id="institute_down" style="<?php echo $style_dos ?>">
                                        <th scope="row" colspan="4" style="font-weight:400; text-align: center">
                                            <label
                                                for="name_institute"><b><?= esc_html__('Institute', 'edusystem') ?></b></label><br>
                                            <input type="text" id="name_institute" name="name_institute"
                                                value="<?php echo strtoupper($student->name_institute); ?>"
                                                style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 style="margin-bottom:0px;text-align:center;">
                                <b><?= esc_html__('Student Information', 'edusystem') ?></b>
                            </h3>
                            <div>
                                <?php
                                global $current_user;
                                $roles = $current_user->roles;
                                if (in_array('administrator', $roles)) {
                                ?>
                                    <p style="text-align: center">
                                        <a href="<?php echo admin_url('user-edit.php?user_id=') . $user_student->ID ?>"
                                            target="_blank">
                                            <button type="button" class="button button-success"
                                                style="margin-left: 10px"><?= esc_html__('View user student', 'edusystem') ?></button>
                                        </a>
                                    </p>
                                <?php } ?>
                            </div>
                            <div>
                                <?php
                                if (current_user_can('switch_users')) {
                                    $base_url = admin_url('users.php');
                                    $base_url = add_query_arg(array(
                                        'ure_switch_action' => 'switch_to',
                                        'user_id' => $user_student->ID,
                                    ), $base_url);
                                    $nonce_action = 'switch_to_user_' . $user_student->ID;
                                    $nonce_url = wp_nonce_url($base_url, $nonce_action);
                                ?>
                                    <p style="text-align: center">
                                        <a href="<?php echo $nonce_url; ?>" target="_blank">
                                            <button type="button" class="button button-primary"
                                                style="margin-left: 10px"><?= esc_html__('Switch', 'edusystem') ?></button>
                                        </a>
                                    </p>
                                <?php } ?>
                            </div>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <td colspan="1">
                                            <label
                                                for="document_type"><b><?= esc_html__('Document Type', 'edusystem') ?></b></label><br>
                                            <select name="document_type" id="document_type"
                                                value="<?php echo get_name_type_document($student->type_document); ?>"
                                                style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="identification_document"
                                                    <?= ($student->type_document == 'identification_document') ? 'selected' : ''; ?>><?= esc_html__('Identification Document', 'edusystem') ?></option>
                                                <option value="passport" <?= ($student->type_document == 'passport') ? 'selected' : ''; ?>><?= esc_html__('Passport', 'edusystem') ?></option>
                                                <option value="ssn" <?= ($student->type_document == 'ssn') ? 'selected' : ''; ?>><?= esc_html__('SNN', 'edusystem') ?></option>
                                            </select>
                                        </td>
                                        <td colspan="5">
                                            <label
                                                for="id_document"><b><?= esc_html__('ID Document', 'edusystem') ?></b></label><br>
                                            <input type="text" id="id_document" name="id_document"
                                                value="<?php echo $student->id_document; ?>" style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="id" name="id" value="<?php echo $student->id; ?>"
                                                style="width:100%" required>
                                        </td>
                                        <?php if ($user_student) { ?>
                                            <td colspan="3">
                                                <label
                                                    for="username"><b><?= esc_html__('Username', 'edusystem') ?></b></label><br>
                                                <input type="text" id="username" name="username"
                                                    value="<?php echo $user_student->user_nicename; ?>" style="width:100%"
                                                    required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        <?php } ?>
                                        <td colspan="3">
                                            <label
                                                for="birth_date"><b><?= esc_html__('Birth date', 'edusystem') ?></b></label><br>
                                            <input type="text" id="birth_date" name="birth_date"
                                                value="<?php echo date($date_format, strtotime($student->birth_date)); ?>"
                                                required style="width:100%; background-color: white;" class="birth_date"
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <label
                                                for="first_name"><b><?= esc_html__('First name', 'edusystem') ?></b></label><br>
                                            <input type="text" id="first_name" name="first_name"
                                                value="<?php echo $student->name; ?>" style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td colspan="3">
                                            <label
                                                for="middle_name"><b><?= esc_html__('Middle name', 'edusystem') ?></b></label><br>
                                            <input type="text" id="middle_name" name="middle_name"
                                                value="<?php echo $student->middle_name; ?>" style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td colspan="3">
                                            <label
                                                for="last_name"><b><?= esc_html__('Last name', 'edusystem') ?></b></label><br>
                                            <input type="text" id="last_name" name="last_name"
                                                value="<?php echo $student->last_name; ?>" style="width:100%" required
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td colspan="3">
                                            <label
                                                for="middle_last_name"><b><?= esc_html__('Middle last name', 'edusystem') ?></b></label><br>
                                            <input type="text" id="middle_last_name" name="middle_last_name"
                                                value="<?php echo $student->middle_last_name; ?>" style="width:100%"
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <label for="gender"><b><?= esc_html__('Gender', 'edusystem') ?></b></label><br>
                                            <select name="gender" id="gender"
                                                value="<?php echo get_gender($student->gender); ?>" style="width:100%"
                                                required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <option value="male" <?= ($student->gender == 'male') ? 'selected' : ''; ?>><?= esc_html__('Male', 'edusystem') ?></option>
                                                <option value="female" <?= ($student->gender == 'female') ? 'selected' : ''; ?>><?= esc_html__('Female', 'edusystem') ?></option>
                                            </select>
                                        </td>

                                        <td colspan="3">
                                            <label for="country"><b><?= esc_html__('Country', 'edusystem') ?></b></label><br>
                                            <select id="country" name="country"
                                                value="<?php echo get_name_country($student->country); ?>"
                                                style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <?php foreach ($countries as $key => $country) { ?>
                                                    <option value="<?= $key ?>"
                                                        <?= (get_name_country($student->country == $key)) ? 'selected' : ''; ?>><?= $country; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>

                                        <td colspan="3">
                                            <label for="city"><b><?= esc_html__('City', 'edusystem') ?></b></label><br>
                                            <input type="text" id="city" name="city"
                                                value="<?php echo $student->city; ?>" style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>

                                        <td colspan="3">
                                            <label
                                                for="postal_code"><b><?= esc_html__('Postal Code', 'edusystem') ?></b></label><br>
                                            <input type="text" id="postal_code" name="postal_code"
                                                value="<?php echo $student->postal_code; ?>" style="width:100%;"
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <label for="expected_graduation_date"><b><?= esc_html__('Expected graduation from high school', 'edusystem') ?></b></label><br>
                                            <input type="text" placeholder="MM/YYYY" id="expected_graduation_date" name="expected_graduation_date"
                                                value="<?php echo $student->expected_graduation_date; ?>" style="width:100%;" required
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td colspan="3">
                                            <label for="email"><b><?= esc_html__('Email', 'edusystem') ?></b></label><br>
                                            <input type="text" id="email" name="email"
                                                value="<?php echo $student->email; ?>" style="width:100%;" required
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            <input type="hidden" id="old_email" name="old_email"
                                                value="<?php echo $student->email; ?>" style="width:100%;">
                                        </td>
                                        <td colspan="3">
                                            <label for="phone"><b><?= esc_html__('Phone', 'edusystem') ?></b></label><br>
                                            <input type="text" id="phone" name="phone"
                                                value="<?php echo $student->phone; ?>" style="width:100%;" required
                                                <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                        <td colspan="3">
                                            <label
                                                for="new_password"><b><?= esc_html__('New password for student', 'edusystem') ?></label><br>
                                            <input type="password" id="new_password" name="new_password"
                                                style="width:100%; background-color: white;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if ($user_student->ID != $partner->ID) { ?>
                                <h3 style="margin-bottom:0px;text-align:center;">
                                    <b><?= esc_html__('Parent Information', 'edusystem') ?></b>
                                </h3>
                                <div>
                                    <?php
                                    global $current_user;
                                    $roles = $current_user->roles;
                                    if (in_array('administrator', $roles)) {
                                    ?>
                                        <p style="text-align: center">
                                            <a href="<?php echo admin_url('user-edit.php?user_id=') . $partner->ID ?>"
                                                target="_blank">
                                                <button type="button" class="button button-success"
                                                    style="margin-left: 10px"><?= esc_html__('View user parent', 'edusystem'); ?></button>
                                            </a>
                                        </p>
                                    <?php } ?>
                                </div>
                                <div>
                                    <?php
                                    if (current_user_can('switch_users')) {
                                        $base_url = admin_url('users.php');
                                        $base_url = add_query_arg(array(
                                            'ure_switch_action' => 'switch_to',
                                            'user_id' => $partner->ID,
                                        ), $base_url);
                                        $nonce_action = 'switch_to_user_' . $partner->ID;
                                        $nonce_url = wp_nonce_url($base_url, $nonce_action);
                                    ?>
                                        <p style="text-align: center">
                                            <a href="<?php echo $nonce_url; ?>" target="_blank">
                                                <button type="button" class="button button-primary"
                                                    style="margin-left: 10px"><?= esc_html__('Switch', 'edusystem'); ?></button>
                                            </a>
                                        </p>
                                    <?php } ?>
                                </div>
                                <table class="form-table table-customize" style="margin-top:0px;">
                                    <tbody>
                                        <tr>
                                            <td colspan="4">
                                                <label
                                                    for="parent_document_type"><b><?= esc_html__('Document Type', 'edusystem') ?></b></label><br>
                                                <select name="parent_document_type" id="parent_document_type"
                                                    value="<?php echo get_user_meta($partner->ID, 'type_document', true); ?>"
                                                    style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                    <option value="identification_document" <?= (get_user_meta($partner->ID, 'type_document', true) == 'identification_document') ? 'selected' : ''; ?>><?= esc_html__('Identification Document', 'edusystem') ?>
                                                    </option>
                                                    <option value="passport" <?= (get_user_meta($partner->ID, 'type_document', true) == 'passport') ? 'selected' : ''; ?>>
                                                        <?= esc_html__('Passport', 'edusystem') ?>
                                                    </option>
                                                    <option value="ssn" <?= (get_user_meta($partner->ID, 'type_document', true) == 'ssn') ? 'selected' : ''; ?>><?= esc_html__('SNN', 'edusystem') ?>
                                                    </option>
                                                </select>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_id_document"><b><?= esc_html__('ID Document', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_id_document" name="parent_id_document"
                                                    value="<?php echo get_user_meta($partner->ID, 'id_document', true); ?>"
                                                    style="width:100%" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <input type="hidden" id="parent_id" name="parent_id"
                                                    value="<?= $student->partner_id; ?>" style="width:100%" required>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_username"><b><?= esc_html__('Username', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_username" name="parent_username"
                                                    value="<?php echo $partner->user_nicename; ?>" style="width:100%"
                                                    required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <label
                                                    for="parent_first_name"><b><?= esc_html__('First name', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_first_name" name="parent_first_name"
                                                    value="<?php echo $partner->first_name; ?>" style="width:100%" required
                                                    <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_last_name"><b><?= esc_html__('Last name', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_last_name" name="parent_last_name"
                                                    value="<?php echo $partner->last_name; ?>" style="width:100%" required
                                                    <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_birth_date"><b><?= esc_html__('Birth date', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_birth_date" name="parent_birth_date"
                                                    value="<?php echo get_user_meta($partner->ID, 'birth_date', true); ?>"
                                                    style="width:100%; background-color: white;" class="birth_date" required
                                                    <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <label
                                                    for="parent_gender"><b><?= esc_html__('Gender', 'edusystem') ?></b></label><br>
                                                <select name="parent_gender" id="parent_gender"
                                                    value="<?php echo get_user_meta($partner->ID, 'gender', true); ?>"
                                                    style="width:100%" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                    <option value="male" <?= (get_user_meta($partner->ID, 'gender', true) == 'male') ? 'selected' : ''; ?>><?= esc_html__('Male', 'edusystem'); ?>
                                                    </option>
                                                    <option value="female" <?= (get_user_meta($partner->ID, 'gender', true) == 'female') ? 'selected' : ''; ?>>
                                                        <?= esc_html__('Female', 'edusystem'); ?>
                                                    </option>
                                                </select>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_country"><b><?= esc_html__('Country', 'edusystem') ?></b></label><br>
                                                <select id="parent_country" name="parent_country"
                                                    value="<?php echo get_name_country(get_user_meta($partner->ID, 'billing_country', true)); ?>"
                                                    style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                    <?php foreach ($countries as $key => $country) { ?>
                                                        <option value="<?= $key ?>"
                                                            <?= (get_name_country(get_user_meta($partner->ID, 'billing_country', true) == $key)) ? 'selected' : ''; ?>><?= $country; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_city"><b><?= esc_html__('City', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_city" name="parent_city"
                                                    value="<?php echo get_user_meta($partner->ID, 'billing_city', true) ?>"
                                                    style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <label
                                                    for="parent_postal_code"><b><?= esc_html__('Postal Code', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_postal_code" name="parent_postal_code"
                                                    value="<?php echo get_user_meta($partner->ID, 'billing_postcode', true) ?>"
                                                    style="width:100%;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_email"><b><?= esc_html__('Email', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_email" name="parent_email"
                                                    value="<?php echo $partner->user_email ?>" style="width:100%;" required
                                                    <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                                <input type="hidden" id="parent_old_email" name="parent_old_email"
                                                    value="<?php echo $partner->user_email; ?>">
                                            </td>
                                            <td colspan="4">
                                                <label
                                                    for="parent_phone"><b><?= esc_html__('Phone', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_phone" name="parent_phone"
                                                    value="<?php echo get_user_meta($partner->ID, 'billing_phone', true) ?>"
                                                    style="width:100%;" required <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="12">
                                                <label
                                                    for="parent_occupation"><b><?= esc_html__('Occupation', 'edusystem') ?></b></label><br>
                                                <input type="text" id="parent_occupation" name="parent_occupation"
                                                    value="<?php echo get_user_meta($partner->ID, 'occupation', true) ?>"
                                                    style="width:100%;" <?php echo in_array('institutes', $roles) ? 'disabled' : '' ?>>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <?php if (!in_array('institutes', $roles) && !current_user_can('only_read_admission_aes')): ?>
                                <p style="text-align: end">
                                    <input type="submit" value="<?= esc_html__('Save Changes', 'edusystem') ?>"
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
        <h2 style="margin-bottom:15px;"><?= esc_html__('Student Documents', 'edusystem') ?></h2>
        <div id="notice-status" class="notice-custom notice-info" style="display:none;">
            <p><?= esc_html__('Status change successfully', 'edusystem') ?></p>
        </div>
        <table id="table-products" class="wp-list-table widefat fixed striped documents-table" style="margin-top:20px;">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary column-document" style="width: 30%">
                        <?= esc_html__('Document', 'edusystem') ?>
                    </th>
                    <th scope="col" class="manage-column column-status" style="width: 10%"><?= esc_html__('Status', 'edusystem') ?>
                    </th>
                    <th scope="col" class="manage-column column-actions" style="width: 60%">
                        <?= esc_html__('Actions', 'edusystem') ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-documents">
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $document): ?>
                        <?php
                        $created_at_timestamp = strtotime($document->created_at);
                        $upload_at_timestamp = strtotime($document->upload_at);
                        $updated_at_timestamp = strtotime($document->updated_at);

                        // Convert the timestamp to the local system time with the desired format
                        $document->created_at = wp_date($datetime_format, $created_at_timestamp);
                        $document->upload_at = wp_date($datetime_format, $upload_at_timestamp);
                        $document->updated_at = wp_date($datetime_format, $updated_at_timestamp);
                        ?>
                        <tr id="<?= 'tr_document_' . $document->id; ?>">
                            <td class="column-primary" data-colname="<?= esc_html__('Document', 'edusystem'); ?>">
                                <?= get_name_document($document->document_id); ?>
                                <?php if ($document->is_required): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                                <?php if ($document->max_date_upload): ?>
                                    <span class="deadline">- <?= esc_html__('Deadline', 'edusystem') ?>: <?= date($date_format, strtotime($document->max_date_upload)) ?></span>
                                <?php endif; ?>
                                <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                            </td>
                            <td data-colname="<?= esc_html__('Status', 'edusystem'); ?>">
                                <b><?= get_status_document($document->status); ?></b>
                            </td>
                            <td data-colname="<?= esc_html__('Actions', 'edusystem'); ?>" class="column-actions-cell">
                                <div class="document-actions-wrapper">
                                    <a target="_blank"
                                        onclick='uploadDocument(<?= htmlspecialchars(json_encode($document), ENT_QUOTES) ?>)'>
                                        <button type="button" class="button button-primary-outline other-buttons-document"
                                            style="color: #149dcd; border-color: #149dcd;">
                                            <span class='dashicons dashicons-upload'></span><?= esc_html__('Upload', 'edusystem') ?>
                                        </button>
                                    </a>
                                    <?php if ($document->status > 0): ?>
                                        <a target="_blank"
                                            onclick='watchDetails(<?= htmlspecialchars(json_encode($document), ENT_QUOTES) ?>)'
                                            style="color: #737983; border-color: #737983;">
                                                <button type="button" class="button button-primary-outline other-buttons-document">
                                                <?= esc_html__('View detail', 'edusystem') ?>
                                            </button>
                                        </a>
                                        <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>">
                                            <button type="button" class="button button-primary-outline other-buttons-document"
                                                style="color: #737983; border-color: #737983;">
                                                <?= esc_html__('View document', 'edusystem') ?>
                                            </button>
                                        </a>
                                        <?php if ($document->status != 1): ?>
                                            <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                                data-status="1" class="button change-status button-warning-outline"
                                                style="color: #c7850b; border-color: #c7850b;">
                                                <?= esc_html__('Revert', 'edusystem') ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($document->status != 3 && $document->status != 6 && $document->status != 1): ?>
                                            <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                                data-status="6" class="button change-status button-secondary"
                                                style="color: purple; border-color: purple;">
                                                <?= esc_html__('Request update', 'edusystem') ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($document->status != 5 && $document->status != 6 && $document->status != 3): ?>
                                            <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                                data-status="5" class="button change-status button-success-outline"
                                                style="color: green; border-color: green;">
                                                <?= esc_html__('Approve', 'edusystem') ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($document->status != 5 && $document->status != 6 && $document->status != 3): ?>
                                            <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>"
                                                data-status="3" class="button change-status button-danger-outline"
                                                style="color: red; border-color: red;">
                                                <?= esc_html__('Decline', 'edusystem') ?>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($document->status != 5 && $document->status != 1): ?>
                                        <a target="_blank"
                                            onclick='changeDeadline(<?= htmlspecialchars(json_encode($document), ENT_QUOTES) ?>)'>
                                            <button type="button" class="button button-primary-outline other-buttons-document"
                                                style="color: #cd1414; border-color: #cd1414;">
                                                <span
                                                    class='dashicons dashicons-clock'></span><?= esc_html__('Change deadline', 'edusystem') ?>
                                            </button>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (!in_array('institutes', $roles) && !current_user_can('only_read_admission_aes') && !empty($documents_certificates)): ?>
        <h2 style="margin-bottom:15px;"><?= esc_html__('Institution Documents', 'edusystem') ?></h2>
        <div id="notice-status" class="notice-custom notice-info" style="display:none;">
            <p><?= esc_html__('Status change successfully', 'edusystem') ?></p>
        </div>

        <table class="wp-list-table widefat fixed striped documents-table" style="margin-top:20px;">
            <thead>
                <tr>
                        <th scope="col" class="manage-column column-primary column-document" style="width: 30%">
                        <?= esc_html__('Document', 'edusystem') ?>
                    </th>
                    <th scope="col" class="manage-column column-actions" style="width: 60%">
                        <?= esc_html__('Actions', 'edusystem') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($documents_certificates)): ?>
                    <?php foreach ($documents_certificates as $document): ?>
                        <?php if (!$document->graduated_required || ($document->graduated_required && $student->status_id == 5)): ?>
                            <tr>
                                <td class="column-primary" data-colname="<?= esc_html__('Document', 'edusystem'); ?>">
                                    <?= $document->title; ?>
                                    <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                </td>
                                <td data-colname="<?= esc_html__('Actions', 'edusystem'); ?>" class="column-actions-cell">
                                    <button type="button" data-documentcertificate="<?= $document->id; ?>"
                                        data-signaturerequired="<?= $document->signature_required; ?>"
                                        class="button download-document-certificate button-success"><?= esc_html__('Generate', 'edusystem') ?></button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <div class="student-details-container" style="margin-top: 10px">
        <div class="student-details-flex-container">

            <?php foreach ($sections as $key => $title): ?>
                <div class="detail-section-wrapper">
                    <h3 class="detail-heading"><?php echo esc_html($title); ?></h3>

                    <?php
                    $items = isset($program_data_student[$key]) ? $program_data_student[$key] : [];
                    $has_valid_data = false;

                    if (!empty($items)):
                        foreach ($items as $item):

                            // Safely extract name and description from the stdClass object
                            $item_name = '';
                            $item_description = '';

                            if (is_object($item)) {
                                $item_name = isset($item->name) ? $item->name : 'N/A';
                                $item_description = isset($item->description) ? $item->description : 'No description provided.';
                            } elseif (is_string($item) && !empty($item)) {
                                $item_name = $item;
                                $item_description = 'Simple information entry.';
                            }

                            // Check if the item is truly empty (like the example in 'mention')
                            if (empty(trim((string) $item_name)) && empty(trim((string) $item_description)) && $key === 'mention') {
                                continue; // Skip this completely empty entry
                            }

                            // Apply truncation logic here
                            $truncated_description = truncate_text($item_description, 30);

                            // If we reach here, we have something to display
                            $has_valid_data = true;
                    ?>
                            <div class="detail-item">
                                <div class="item-name"><?php echo esc_html($item_name); ?></div>
                                <p class="item-description"><?php echo esc_html($truncated_description); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!$has_valid_data): ?>
                        <p class="no-data"><?= sprintf(esc_html__('No data found for %s.', 'edusystem'), strtolower(esc_html($title))); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<div id='decline-modal' class='modal' style='display:none'>
    <div class='modal-content'>
        <div class="modal-header">
            <h3 style="font-size:20px;"><?= esc_html__('Decline Document', 'edusystem') ?></h3>
            <span id="decline-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
        </div>
        <div class="modal-body" style="margin-top:10px;padding:0px;">
            <div>
                <span><?= esc_html__('This same text will be shown to the user in their notifications section, please create a message addressed to the user', 'edusystem') ?></span>
            </div>
            <div>
                <label for="decline-description"><b><?= esc_html__('Reason why it is declined', 'edusystem') ?></b><span
                        class="text-danger">*</span></label><br>
                <textarea name="decline-description" type="text" style="width: 100%;"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button id="decline-save" type="submit"
                class="button button-danger"><?= esc_html__('Decline', 'edusystem') ?></button>
            <button id="decline-exit-button" type="button"
                class="button button-outline-primary modal-close"><?= esc_html__('Exit', 'edusystem') ?></button>
        </div>
    </div>
</div>

<div id='detail-modal' class='modal' style='display:none'>
    <div class='modal-content' style="width: 70%;">
        <div class="modal-header">
            <h3 style="font-size:20px;"><?= esc_html__('Detail Document', 'edusystem') ?></h3>
            <span id="detail-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
        </div>
        <div class="modal-body" style="padding:10px;">
            <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th scope="col" class=" manage-column column"><?= esc_html__('Date user registered', 'edusystem') ?></th>
                        </th>
                        <th scope="col" class=" manage-column column-primary">
                            <?= esc_html__('Date upload documents', 'edusystem') ?>
                        </th>
                        <th scope="col" class=" manage-column column-email">
                            <?= esc_html__('Date status change', 'edusystem') ?>
                        </th>
                        <th scope="col" class=" manage-column column-email"><?= esc_html__('Status changed by', 'edusystem') ?></th>
                        </th>
                        <th scope="col" class=" manage-column column-email"><?= esc_html__('Message', 'edusystem') ?></th>
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
            <button id="detail-exit-button" type="button"
                class="button button-outline-primary modal-close"><?= esc_html__('Exit', 'edusystem') ?></button>
        </div>
    </div>
</div>

<div id='upload-modal' class='modal' style='display:none'>
    <form id="upload-form" method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=upload_document'); ?>"
        enctype="multipart/form-data">
        <div class='modal-content' style="width: 70%;">
            <div class="modal-header">
                <h3 style="font-size:20px;"><?= esc_html__('Upload Document', 'edusystem') ?> <span id="document_upload_text"></span></h3>
                <span id="upload-exit-icon" class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
            </div>
            <div class="modal-body" style="padding:10px;">
                <input type="hidden" name="document_upload_id">
                <input type="hidden" name="document_upload_name">
                <input type="hidden" name="student_id" value="<?= $student->id; ?>">
                <div>
                    <label for="document_upload_file">Document</label><br>
                    <input type="file" name="document_upload_file" id="document_upload_file" required>
                </div>
            </div>
            <div class="modal-footer">
                <button id="upload-button" type="submit"
                    class="button button-outline-primary modal-close"><?= esc_html__('Upload', 'edusystem') ?></button>
                <button id="upload-exit-button" type="button"
                    class="button button-danger modal-close"><?= esc_html__('Exit', 'edusystem') ?></button>
            </div>
        </div>
    </form>
</div>

<div id='change-deadline-modal' class='modal' style='display:none'>
    <form id="change-deadline-form" method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_admission_content&action=change_deadline'); ?>"
        enctype="multipart/form-data">
        <div class='modal-content' style="width: 70%;">
            <div class="modal-header">
                <h3 style="font-size:20px;"><?= esc_html__('Change deadline', 'edusystem') ?> <span
                        id="document_change_deadline_text"></span></h3>
                <span id="change-deadline-exit-icon" class="modal-close"><span
                        class="dashicons dashicons-no-alt"></span></span>
            </div>
            <div class="modal-body" style="padding:10px;">
                <input type="hidden" name="document_change_deadline_id">
                <input type="hidden" name="document_change_deadline_name">
                <input type="hidden" name="student_id" value="<?= $student->id; ?>">
                <div id="date_input_container">
                    <label for="document_change_deadline_date">Date</label><br>
                    <input type="date" name="document_change_deadline_date" id="document_change_deadline_date" required>
                </div>
                <div style="margin-top: 10px;">
                    <input type="checkbox" id="allow_empty_date" name="allow_empty_date" onchange="toggleDateInput()">
                    <label for="allow_empty_date"><?= esc_html__('Remove deadline', 'edusystem') ?></label>
                </div>
            </div>
            <div class="modal-footer">
                <button id="change-deadline-button" type="submit"
                    class="button button-outline-primary modal-close"><?= esc_html__('Change', 'edusystem') ?></button>
                <button id="change-deadline-exit-button" type="button"
                    class="button button-danger modal-close"><?= esc_html__('Exit', 'edusystem') ?></button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($documents_certificates)) { ?>
    <div id='documentcertificate-modal' class='modal' style='display:none'>
        <div class='modal-content' style="width: 70%;">
            <div class="modal-header">
                <h3 style="font-size:20px;"><?= esc_html__('Generate Document', 'edusystem') ?></h3>
                <span id="documentcertificate-exit-icon" class="modal-close"><span
                        class="dashicons dashicons-no-alt"></span></span>
            </div>
            <div class="modal-body" style="padding:10px;">
                <input type="hidden" name="document_certificate_id">
                <input type="hidden" name="student_document_certificate_id" value="<?= $student->id; ?>">
                <div>
                    <label for="user_signature_id"><?= esc_html__('Who signed this document', 'edusystem') ?></label><br>
                    <select name="user_signature_id" required>
                        <option value="" selected><?= esc_html__('Assigns an user', 'edusystem') ?></option>
                        <?php foreach ($users_signatures_certificates as $user) {
                            $user_loaded = get_user_by('id', $user->user_id);
                        ?>
                            <option value="<?= $user->id ?>"><?= $user_loaded->first_name ?> <?= $user_loaded->last_name ?>
                                (<?= $user->charge ?>)</option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="documentcertificate-button" type="button"
                    class="button button-outline-primary modal-close"><?= esc_html__('Generate', 'edusystem') ?></button>
                <button id="documentcertificate-exit-button" type="button"
                    class="button button-danger modal-close"><?= esc_html__('Exit', 'edusystem') ?></button>
            </div>
        </div>
    </div>
    <?php include(plugin_dir_path(__FILE__) . 'document-export.php'); ?>
    <?php if ($student->status_id < 6 && current_user_can('withdraw_student')) { ?>
        <div style="text-align: center; margin: 12px">
            <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=withdraw_student&student_id=') . $student->id ?>"
                class="button button-danger"
                onclick="return confirm('<?= esc_html__('Are you sure you want to expel the student?', 'edusystem') ?>');"><?= sprintf(esc_html__('Withdraw student from %s', 'edusystem'), get_bloginfo('name')); ?></a>
        </div>
    <?php } ?>
    <div style="text-align: center;">
        <p>
            <?php
            // Obtener el timestamp en formato Unix para wp_date, si $student->created_at es una cadena de fecha/hora.
            $timestamp = strtotime($student->created_at);

            // Formatear la fecha/hora en el formato deseado
            $formatted_date = wp_date($datetime_format, $timestamp);

            // Usar sprintf para incrustar la fecha formateada en la cadena traducible
            $translated_text = sprintf(
                /* translators: %s: The creation date and time of the student (e.g., 12/31/2025 14:30:00) */
                __('Student created at %s', 'edusystem'),
                $formatted_date
            );

            echo $translated_text;
            ?>
        </p>
    </div>
<?php } ?>

<script>
    function toggleDateInput() {
        var checkbox = document.getElementById('allow_empty_date');
        var dateContainer = document.getElementById('date_input_container');
        var dateInput = document.getElementById('document_change_deadline_date');

        if (checkbox.checked) {
            dateContainer.style.display = 'none';
            dateInput.removeAttribute('required');
            dateInput.value = '';
        } else {
            dateContainer.style.display = 'block';
            dateInput.setAttribute('required', 'required');
        }
    }


    const mesAnio = document.getElementById('expected_graduation_date');
    if (mesAnio) {
        mesAnio.addEventListener('input', function(e) {
            let valor = e.target.value;

            // Eliminar cualquier caracter que no sea un número
            valor = valor.replace(/\D/g, '');

            // Limitar la longitud a 6 dígitos (2 para mes, 4 para año)
            if (valor.length > 6) {
                valor = valor.slice(0, 6);
            }

            // Formatear: agregar la barra después de los 2 primeros dígitos
            if (valor.length > 2) {
                let mes = valor.slice(0, 2);
                let anio = valor.slice(2, 6);

                // Validar que el mes no sea mayor a 12
                if (parseInt(mes) > 12) {
                    // Si es mayor a 12, lo establecemos como 12
                    mes = '12';
                }

                valor = mes + '/' + anio;
            }

            // Actualizar el valor del input
            e.target.value = valor;
        });
    }
</script>