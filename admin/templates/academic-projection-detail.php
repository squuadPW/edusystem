<?php
    global $current_user;
    $roles = $current_user->roles;
    
?>

<?php include(plugin_dir_path(__FILE__) . 'table-download-grades.php'); ?>
<div class="wrap">
    <?php if (isset($projection) && !empty($projection)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Academic projection', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Not found', 'edusystem'); ?></h2>
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
    <div>
        <div style="display:flex; justify-content: start;">
            <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
        </div>
        <div style="display:flex; justify-content: end;">
            <?php 
                include(plugin_dir_path(__FILE__).'connections-student.php');
            ?>
            <?php if (in_array('administrator', $roles)) { ?>
                <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=auto_enroll&student_id=') . $student->id . '&projection_id='.$projection->id ?>" class="button button-outline-primary" onclick="return confirm('Estas seguro de inscribir en base a la matriz de proyeccion academica?');"><?= __('Auto-enroll','edusystem'); ?></a>
            <?php } ?>
        </div>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <p style="text-align: center; padding: 12px !important">
                                        <label for="grade" style="font-size: 24px;" class="text-uppercase"><b><?= strtoupper(__($student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name, 'edusystem')); ?></b></label>
                                    </p>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400; text-align: center">
                                        <label for="grade"><b><?php _e('Grade', 'edusystem'); ?></b></label><br>
                                        <?php foreach ($grades as $grade): ?>
                                            <?php if($student->grade_id == $grade->id) { ?>
                                                <label for="grade"><b><?= $grade->name; ?> <?= $grade->description; ?></b></label>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </th>
                                    <th scope="row" style="font-weight:400; text-align: center">
                                        <label for="grade"><b><?php _e('Initial period and cut', 'edusystem'); ?></b></label><br>
                                        <label for="grade"><b><?= $student->academic_period . ' - ' . $student->initial_cut ?></b></label>
                                    </th>
                                </tr>
                            </tbody>
                        </table>

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=save_academic_projection'); ?>">

                            <div>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Academic projection', 'edusystem'); ?></b>
                                </h3>

                                <input type="hidden" name="projection_id" value="<?= $projection->id ?>">
                                <input type="hidden" name="current_period" value="<?= $current_period ?>">
                                <input type="hidden" name="current_cut" value="<?= $current_cut ?>">

                                <?php foreach (json_decode($projection->projection) as $key => $projection_for) { ?>
                                    <?php if(($projection_for->type == 'equivalence' && get_option('show_equivalence_projection')) || $projection_for->type != 'equivalence') { ?>
                                        <div id="row[<?=$key?>]" <?= ($projection_for->this_cut) ? 'class="current-period row-projection"' : 'class="row-projection"'; ?>>
                                            <div style="flex: 1; padding: 5px; align-content: center;">
                                                <?php if($projection_for->type != 'equivalence') { ?>
                                                    <input type="checkbox" name="completed[<?= $key ?>]" <?php echo $projection_for->is_completed ? 'checked style="pointer-events: none !important; background-color: #80808038;"' : '' ?>>
                                                    <input type="hidden" name="this_cut[<?= $key ?>]" value="<?= $projection_for->this_cut ? 1 : 0 ?>">
                                                <?php } ?>

                                                <label for="input_id"><b><?= __($projection_for->subject, 'edusystem'); ?></b></label>
                                            </div>

                                            <?php if($projection_for->type == 'equivalence') { ?>
                                                <div style="flex: 1; padding: 5px; align-content: center; text-align: end; font-style: italic;">
                                                    <label for="input_id"><b><?= __('Equivalence', 'edusystem'); ?></b></label>
                                                </div>
                                            <?php } ?>
                                            
                                            <?php if($projection_for->type != 'equivalence') { ?>
                                                <div style="flex: 1; padding: 5px;">
                                                    <label for="input_id"><b><?= __('Period', 'edusystem'); ?></b></label><br>
                                                    <select onchange="academic_period_changed(<?= $key ?>)" name="academic_period[<?= $key ?>]" <?php echo $projection_for->is_completed ? 'style="pointer-events: none !important; background-color: #80808038;"' : '' ?>>
                                                        <option value="" selected>Select academic period to filter</option>
                                                        <?php foreach ($periods as $period) { ?>
                                                            <option <?= ($period->code == $current_period) ? 'class="current-period"' : ''; ?> value="<?php echo $period->code; ?>" <?= ($projection_for->code_period == $period->code) ? 'selected' : ''; ?>>
                                                                <?php echo $period->name; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                
                                                <div style="flex: 1; padding: 5px;">
                                                    <label for="input_id"><b><?= __('Cut', 'edusystem'); ?></b></label><br>
                                                    <select onchange="academic_period_changed(<?= $key ?>)" name="academic_period_cut[<?= $key ?>]" <?php echo $projection_for->is_completed ? 'style="pointer-events: none !important; background-color: #80808038;"' : '' ?>>
                                                        <option value="">Select academic period cut</option>
                                                        <option <?= ($current_cut == 'A') ? 'class="current-period"' : ''; ?> value="A" <?= ($projection_for->cut == 'A') ? 'selected' : ''; ?>>A</option>
                                                        <option <?= ($current_cut == 'B') ? 'class="current-period"' : ''; ?> value="B" <?= ($projection_for->cut == 'B') ? 'selected' : ''; ?>>B</option>
                                                        <option <?= ($current_cut == 'C') ? 'class="current-period"' : ''; ?> value="C" <?= ($projection_for->cut == 'C') ? 'selected' : ''; ?>>C</option>
                                                        <option <?= ($current_cut == 'D') ? 'class="current-period"' : ''; ?> value="D" <?= ($projection_for->cut == 'D') ? 'selected' : ''; ?>>D</option>
                                                        <option <?= ($current_cut == 'E') ? 'class="current-period"' : ''; ?> value="E" <?= ($projection_for->cut == 'E') ? 'selected' : ''; ?>>E</option>
                                                    </select>
                                                </div>

                                                <div style="flex: 1; padding: 5px;">
                                                    <label for="input_id"><b><?= __('Calification', 'edusystem'); ?></b></label><br>
                                                    <input type="number" step="0.01" name="calification[<?= $key ?>]" value="<?= $projection_for->calification ?? ''; ?>" <?php echo $projection_for->is_completed && !$projection_for->this_cut ? 'style="pointer-events: none !important; background-color: #80808038;"' : '' ?>>
                                                </div>
                                            <?php } ?>
                                            
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <?php if($student->elective == 1) { ?>
                                    <div style="display: block; width: 100%; text-align: center">
                                        <div style="flex: 1; padding: 20px;">
                                            <label><b><?= __('This student has yet to select an elective.', 'edusystem'); ?></b></label><br>
                                        </div>
                                        <div>
                                            <a onclick="return confirm('Are you sure?');" style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=student_elective_change&student_id=') . $student->id . '&projection_id='.$projection->id . '&status=0' ?>" class="button button-danger" onclick="return confirm('Are you sure to desactivate the elective for this student?');">
                                                <?= __('Desactivate student elective','edusystem'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if($student->elective == 0) { ?>
                                    <div style="display: flex; width: 100%; text-align: center">
                                        <div style="flex: 1; padding: 20px;">
                                            <a onclick="return confirm('Are you sure?');" style="margin-left: 10px" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=student_elective_change&student_id=') . $student->id . '&projection_id='.$projection->id . '&status=1' ?>" class="button button-outline-primary" onclick="return confirm('Are you sure to activate the elective for this student?');">
                                                <?= __('Activate student elective','edusystem'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Inscriptions', 'edusystem'); ?></b>
                                </h3>

                                <table class="wp-list-table widefat fixed striped posts"
                                    style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class=" manage-column column">
                                                <?= __('Status', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column column-primary">
                                                <?= __('Student', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column">
                                                <?= __('Subject - Code', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column">
                                                <?= __('Period - cut', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column">
                                                <?= __('Calification', 'edusystem'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column" style="text-align: end">
                                                <?= __('Action', 'edusystem'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($inscriptions as $key => $inscription) { ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                        switch ($inscription->status_id) {
                                                            case 0:
                                                                echo '<div style="color: gray; font-weight: 600">'. strtoupper('To begin') . '</div>';
                                                                break;
                                                            case 1:
                                                                echo '<div style="color: blue; font-weight: 600">'. strtoupper('Active') . '</div>';
                                                                break;
                                                            case 2:
                                                                echo '<div style="color: red; font-weight: 600">'. strtoupper('Unsubscribed') . '</div>';
                                                                break;
                                                            case 3:
                                                                echo '<div style="color: green; font-weight: 600">'. strtoupper('Approved') . '</div>';
                                                                break;
                                                            case 4:
                                                                echo '<div style="color: red; font-weight: 600">'. strtoupper('Reproved') . '</div>';
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ' ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name); ?>
                                                </td>
                                                <td>
                                                    <?php $subject = get_subject_details_code($inscription->code_subject); ?>
                                                    <?php echo $subject ? ($subject->name . ' - ' . $subject->code_subject) : 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <?php echo $inscription->code_period . ' - ' . $inscription->cut_period; ?>
                                                </td>
                                                <td>
                                                    <?php echo isset($inscription->calification) ? number_format($inscription->calification, 2) : 'N/A'; ?>
                                                </td>
                                                <td style="text-align: end">
                                                    <?php if(current_user_can('manager_enrollments_aes')) { ?>
                                                        <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=delete_inscription&inscription_id=' . $inscription->id . '&projection_id=' . $projection->id); ?>" class="button button-danger" onclick="return confirm('Are you sure?');"><span class='dashicons dashicons-trash'></span> </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                       <?php } ?>
                                    </tbody>
                                </table>

                                
                                    <div style="display: flex; width: 100%; text-align: center">
                                        <div style="flex: 1; padding: 20px;">
                                            <button type="button" class="button button-outline-primary" id="preview-grades">
                                                <?= __('Preview grades','edusystem'); ?>
                                            </button>
                                        </div>
                                    </div>
                                
                            </div>

                            <?php if (isset($projection) && !empty($projection)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                <button type="submit"
                                    class="button button-primary" name="action" value="send_email" onclick="return confirm('Are you sure?');"><?= __('Save and send email', 'edusystem'); ?></button>
                                    <button type="submit"
                                        class="button button-success" name="action" value="save" onclick="return confirm('Are you sure?');"><?= __('Only saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>