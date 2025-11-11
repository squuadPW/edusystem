<?php
global $current_user;
$roles = $current_user->roles;
$student_id = (int) $_GET['student_id']; // Variable added here for use in the form
?>

<div class="wrap">
    <?php if (isset($student) && !empty($student)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Matrix', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Not found', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div style="display:flex; justify-content: start; gap: 10px;">
            <?php
            // Using the referer URL is risky, let's use the student list page or admin_url() as a default
            $referer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url();
            ?>
            <a class="button button-outline-primary"
                href="<?= esc_url($referer_url); ?>"><?= __('Back', 'edusystem'); ?></a>
        </div>
        <div style="display:flex; justify-content: end; gap: 10px;">
            <?php
            include(plugin_dir_path(__FILE__) . 'connections-student.php');
            ?>
            <?php if (in_array('administrator', $roles)) { ?>
                <a href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=auto_enroll&student_id=') . $student->id . '&projection_id=' . $projection->id ?>"
                    class="button button-outline-primary"
                    onclick="return confirm('Estas seguro de inscribir en base a la matriz de proyeccion academica?');"><?= __('Auto-enroll', 'edusystem'); ?></a>
            <?php } ?>
        </div>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <h1 style="text-align: center; margin-bottom: 30px;"><?= $student_full_name ?></h1>

                        <?php if (!empty($matrix) && is_array($matrix)): ?>

                            <!-- FORM START: Matrix Configuration -->
                            <form method="post"
                                action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=update_matrix'); ?>">
                                <!-- Hidden fields for context -->
                                <input type="hidden" name="action" value="update_academic_matrix" />
                                <input type="hidden" name="student_id" value="<?= esc_attr($student_id); ?>" />
                                <input type="hidden" name="projection_id" value="<?= esc_attr($projection->id ?? ''); ?>" />
                                <!-- You should add a nonce field here for security -->
                                <?php // wp_nonce_field('update_matrix_action', 'update_matrix_nonce'); ?>

                                <div style="overflow-x: auto;">
                                    <table class="wp-list-table widefat fixed striped">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 15%;"><?= __('Type', 'edusystem'); ?></th>
                                                <th scope="col" style="width: 25%;">
                                                    <?= __('Academic Period', 'edusystem'); ?></th>
                                                <th scope="col" style="width: 25%;"><?= __('Academic Cut', 'edusystem'); ?>
                                                </th>
                                                <th scope="col" style="width: 30%;"><?= __('Subject', 'edusystem'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($matrix as $index => $item): ?>
                                                <?php $item = (array) $item; // Ensure it's an array for safe access ?>
                                                <tr id="matrix-row-<?= $index; ?>">
                                                    <th scope="row"><?= $index + 1; ?></th>

                                                    <!-- 1. Tipo de materia (type) -->
                                                    <td>
                                                        <?php
                                                        $type = $item['type'] ?? '';
                                                        $type_label = $type === 'R' ? __('Regular', 'edusystem') : ($type === 'E' ? __('Elective', 'edusystem') : 'N/A');
                                                        echo esc_html($type_label);
                                                        ?>
                                                        <input type="hidden" name="matrix[<?= $index ?>][type]"
                                                            value="<?= esc_attr($type) ?>" />
                                                    </td>

                                                    <!-- 2. Periodo académico (Select) -->
                                                    <td>
                                                        <?php if (!empty($item['code_period'])): ?>
                                                            <input type="hidden" name="matrix[<?= $index ?>][code_period]"
                                                                value="<?= esc_attr($item['code_period']) ?>" />
                                                        <?php endif; ?>

                                                        <select name="matrix[<?= $index ?>][code_period]"
                                                            class="academic-period-select"
                                                            style="width: 100%; min-width: 150px;"
                                                            <?= $item['completed'] ? 'disabled' : '' ?>>
                                                            <?php if (empty($item['code_period'])): ?>
                                                                <option value="" selected><?= __('Select Period', 'edusystem') ?>
                                                                </option>
                                                            <?php endif; ?>
                                                            <?php foreach ($periods as $period) { ?>
                                                                <option value="<?= esc_attr($period->code) ?>"
                                                                    <?= (isset($item['code_period']) && $item['code_period'] == $period->code) ? 'selected' : '' ?>>
                                                                    <?= esc_html($period->name) ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <!-- 3. Corte académico (Select) -->
                                                    <td>
                                                        <?php if (!empty($item['cut'])): ?>
                                                            <input type="hidden" name="matrix[<?= $index ?>][cut]"
                                                                value="<?= esc_attr($item['cut']) ?>" />
                                                        <?php endif; ?>

                                                        <select name="matrix[<?= $index ?>][cut]" class="academic-cut-select"
                                                            style="width: 100%; min-width: 150px;" <?= $item['completed'] ? 'disabled' : '' ?>>
                                                            <?php if (empty($item['cut'])): ?>
                                                                <option value="" selected><?= __('Select cut', 'edusystem') ?>
                                                                </option>
                                                            <?php endif; ?>
                                                            <?php foreach ($cuts as $cut) { ?>
                                                                <option value="<?= esc_attr($cut) ?>" <?= (isset($item['cut']) && $item['cut'] == $cut) ? 'selected' : '' ?>>
                                                                    <?= esc_html($cut) ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <!-- 4. Materia (Select) -->
                                                    <td>
                                                        <?php if (!empty($item['subject_id'])): ?>
                                                            <input type="hidden" name="matrix[<?= $index ?>][subject_id]"
                                                                value="<?= esc_attr($item['subject_id']) ?>" />
                                                        <?php endif; ?>

                                                        <select name="matrix[<?= $index ?>][subject_id]" class="subject-select"
                                                            style="width: 100%; min-width: 200px;"
                                                            <?= $item['completed'] ? 'disabled' : '' ?>>
                                                            <?php if (empty($item['subject_id'])): ?>
                                                                <option value="" selected><?= __('Select Subject', 'edusystem') ?>
                                                                </option>
                                                            <?php endif; ?>
                                                            <?php foreach ($subjects as $subject) { ?>
                                                                <option value="<?= $subject->id ?>" <?= (isset($item['subject_id']) && $item['subject_id'] == $subject->id) ? 'selected' : '' ?>>
                                                                    <?= esc_html($subject->name) ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="margin-top: 20px; text-align: right;">
                                    <button type="submit"
                                        class="button button-primary button-large"><?= __('Save Matrix Changes', 'edusystem'); ?></button>
                                </div>
                            </form>
                            <!-- FORM END -->
                        <?php else: ?>
                            <p class="description"
                                style="text-align: center; margin-top: 30px; padding: 20px; background-color: #f7f7f7; border: 1px solid #eee;">
                                <?= __('The academic projection matrix is empty for this student.', 'edusystem'); ?>
                            </p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>