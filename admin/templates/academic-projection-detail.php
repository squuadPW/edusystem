<div class="wrap">
    <?php if (isset($projection) && !empty($projection)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Academic projection of ' . $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name . ' (' . $student->id_document . ')', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Not found', 'aes'); ?></h2>
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
            href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content'); ?>"><?= __('Back', 'aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=save_academic_projection'); ?>">

                            <div>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Academic projection', 'aes'); ?></b>
                                </h3>

                                <input type="hidden" name="projection_id" value="<?php echo $projection->id ?>">

                                <?php foreach (json_decode($projection->projection) as $key => $projection_for) { ?>
                                    <div style="display: flex; width: 100%;">
                                        <div style="flex: 1; padding: 5px; align-content: center;">
                                            <input type="checkbox" name="completed[<?= $key ?>]" <?php echo $projection_for->is_completed ? 'checked' : '' ?>>
                                            <label for="input_id"><b><?= __($projection_for->subject, 'aes'); ?></b></label><br>

                                            <input type="checkbox" name="this_cut[<?= $key ?>]" <?php echo $projection_for->this_cut ? 'checked' : '' ?>>
                                            <label for="input_id"><b><?= __('This cut', 'aes'); ?></b></label><br>
                                        </div>
                                        
                                        <div style="flex: 1; padding: 5px;">
                                            <label for="input_id"><b><?= __('Period', 'aes'); ?></b></label><br>
                                            <select name="academic_period[<?= $key ?>]" required>
                                                <option value="" selected>Select academic period to filter</option>
                                                <?php foreach ($periods as $period) { ?>
                                                    <option value="<?php echo $period->code; ?>" <?= ($projection_for->code_period == $period->code) ? 'selected' : ''; ?>>
                                                        <?php echo $period->name; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <div style="flex: 1; padding: 5px;">
                                            <label for="input_id"><b><?= __('Cut', 'aes'); ?></b></label><br>
                                            <select name="academic_period_cut[<?= $key ?>]" required>
                                                <option value="">Select academic period cut</option>
                                                <option value="A" <?= ($projection_for->cut == 'A') ? 'selected' : ''; ?>>A</option>
                                                <option value="B" <?= ($projection_for->cut == 'B') ? 'selected' : ''; ?>>B</option>
                                                <option value="C" <?= ($projection_for->cut == 'C') ? 'selected' : ''; ?>>C</option>
                                                <option value="D" <?= ($projection_for->cut == 'D') ? 'selected' : ''; ?>>D</option>
                                                <option value="E" <?= ($projection_for->cut == 'E') ? 'selected' : ''; ?>>E</option>
                                            </select>
                                        </div>

                                        <div style="flex: 1; padding: 5px;">
                                            <label for="input_id"><b><?= __('Calification', 'aes'); ?></b></label><br>
                                            <input type="number" name="calification[<?= $key ?>]" value="<?= $projection_for->calification ?? ''; ?>" required>
                                        </div>
                                        
                                    </div>
                                <?php } ?>
                            </div>

                            <div>
                                <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Inscriptions', 'aes'); ?></b>
                                </h3>

                                <table class="wp-list-table widefat fixed striped posts"
                                    style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class=" manage-column column">
                                                <?= __('Status', 'aes'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column column-primary">
                                                <?= __('Student', 'aes'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column">
                                                <?= __('Subject - Code', 'aes'); ?>
                                            </th>
                                            <th scope="col" class=" manage-column">
                                                <?= __('Period - cut', 'aes'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($inscriptions as $key => $inscription) { ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                        switch ($inscription->status_id) {
                                                            case 1:
                                                                echo '<div style="color: blue; font-weight: 600">'. strtoupper('Active') . '</div>';
                                                                break;
                                                            case 0:
                                                                echo '<div style="color: gray; font-weight: 600">'. strtoupper('To begin') . '</div>';
                                                                break;
                                                            case 2:
                                                                echo '<div style="color: red; font-weight: 600">'. strtoupper('Unsubscribed') . '</div>';
                                                                break;
                                                            case 3:
                                                                echo '<div style="color: green; font-weight: 600">'. strtoupper('Completed') . '</div>';
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
                                            </tr>
                                       <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (isset($projection) && !empty($projection)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>