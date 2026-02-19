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
        <div style="display:flex; justify: start; gap: 10px;">
            <?php
            // Using the referer URL is risky, let's use the student list page or admin_url() as a default
            $referer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url();
            ?>
            <a class="button button-outline-primary"
                href="<?= esc_url($referer_url); ?>"><?= __('Back', 'edusystem'); ?></a>
        </div>
        <div style="display:flex; justify: end; gap: 10px;">
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
                        <p style="text-align: center; margin-bottom: 30px;"><?= sprintf(__('The student was created with %s terms available.', 'edusystem'), $student->terms_available); ?></p>
                        <p style="text-align: center; margin-bottom: 30px;"><?= sprintf(__('Now, the student has %s terms left.', 'edusystem'), $terms_available_left); ?></p>

                        <?php if (!empty($matrix) && is_array($matrix)): ?>

                            <form method="post"
                                action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=update_matrix'); ?>">

                                <input type="hidden" name="action" value="update_academic_matrix" />
                                <input type="hidden" name="student_id" value="<?= esc_attr($student_id); ?>" />
                                <input type="hidden" name="projection_id" value="<?= esc_attr($projection->id ?? ''); ?>" />
                                
                                <?php // wp_nonce_field('update_matrix_action', 'update_matrix_nonce'); ?>

                                <div style="overflow-x: auto;">
                                    <table class="wp-list-table widefat fixed striped">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 30%;"><?= __('Subject', 'edusystem'); ?></th>
                                                <!-- <th scope="col" style="width: 15%;"><?= __('Type', 'edusystem'); ?></th> -->
                                                <th scope="col" style="width: 30%;">
                                                    <?= __('Academic Period', 'edusystem'); ?></th>
                                                <th scope="col" style="width: 30%;"><?= __('Academic Cut', 'edusystem'); ?>
                                                </th>
                                                <th scope="col" style="width: 15%;"><?= __('Status', 'edusystem'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $real_index = 0; // Índice de la fila real en la tabla 
                                            ?>
                                            <?php foreach ($matrix as $matrix_index => $item): ?>
                                                <?php 
                                                
                                                    // Asegura que es un array para el acceso seguro 
                                                    $item = (array) $item; 

                                                    // Determina si los datos son un array (múltiples registros) o un valor simple
                                                    $is_multi_entry = is_array($item['subject_id'] ?? null);
                                                    // Obtiene el número de filas a iterar
                                                    $entry_count = $is_multi_entry ? count($item['subject_id']) : 1;
                                                    // Obtiene el estado de completado del grupo para deshabilitar el Periodo/Corte
                                                    $is_group_completed = $is_multi_entry ? in_array(true, (array)$item['completed']) : ($item['completed'] ?? false);
                                                    // Obtiene el estado de aprobada para el grupo (si alguna subentrada tiene 'aprobada')
                                                    $is_group_approved = false;
                                                    if ($is_multi_entry) {
                                                        $statuses = (array)($item['status'] ?? []);
                                                        $is_group_approved = in_array('aprobada', $statuses, true) || in_array('en curso', $statuses, true);
                                                    } else {
                                                        $is_group_approved = (isset($item['status']) && ($item['status'] === 'aprobada' || $item['status'] === 'en curso'));
                                                    }

                                                    // Si el grupo está completado o aprobado, se deshabilitan los selects
                                                    $is_group_disabled = $is_group_completed || $is_group_approved;
                                                ?>

                                                <?php for ($sub_index = 0; $sub_index < $entry_count; $sub_index++, $real_index++): ?>
                                                    <?php
                                                        // --- Extracción de valores por fila ---
                                                        $type = $is_multi_entry ? ($item['type'][$sub_index] ?? 'R') : ($item['type'] ?? 'R');
                                                        $type_label = $type === 'R' ? __('Regular', 'edusystem') : ($type === 'E' ? __('Elective', 'edusystem') : 'N/A');

                                                        // Para Periodo y Corte (valores únicos para el grupo) se extrae del índice 0 si es multi.
                                                        $code_period = $is_multi_entry ? ($item['code_period'][0] ?? '') : ($item['code_period'] ?? '');
                                                        $cut = $is_multi_entry ? ($item['cut'][0] ?? '') : ($item['cut'] ?? '');

                                                        // Subject, completed y status (valores por sub-ítem)
                                                        $subject_id = $is_multi_entry ? ($item['subject_id'][$sub_index] ?? '') : ($item['subject_id'] ?? '');
                                                        $completed = $is_multi_entry ? ($item['completed'][$sub_index] ?? false) : ($item['completed'] ?? false);
                                                        $status = $is_multi_entry ? ($item['status'][$sub_index] ?? 'pendiente') : ($item['status'] ?? 'pendiente');
                                                    ?>

                                                    <tr id="matrix-row-<?= $real_index; ?>" <?= $status == 'blocked' ? 'style="background: #eba3a3  !important; "' : ''?> >
                                                        <th scope="row"><?= $real_index + 1; ?></th>
                                                        
                                                        <td>
                                                            <input type="hidden" name="matrix[<?= $matrix_index ?>][subject_id][<?= $sub_index ?>]"
                                                                value="<?= esc_attr($subject_id) ?>" />
                                                            <input type="hidden" name="matrix[<?= $matrix_index ?>][completed][<?= $sub_index ?>]"
                                                                value="<?= esc_attr($completed ? '1' : '0') ?>" />
                                                            <input type="hidden" name="matrix[<?= $matrix_index ?>][status][<?= $sub_index ?>]"
                                                                value="<?= esc_attr($status) ?>" />

                                                            <!-- <select name="matrix[<?= $matrix_index ?>][subject_id][<?= $sub_index ?>]" class="subject-select"
                                                                style="width: 100%; min-width: 200px;"
                                                                <?= $completed || $is_group_disabled ? 'disabled' : '' ?>>
                                                                <option value="" <?= empty($subject_id) ? 'selected' : '' ?>><?= __('Select Subject', 'edusystem') ?></option>
                                                                <?php foreach ($subjects_regular as $subject) { ?>
                                                                    <option value="<?= $subject->id ?>" <?= ($subject_id == $subject->id) ? 'selected' : '' ?>>
                                                                        <?= esc_html($subject->name) ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select> -->

                                                            <?php 
                                                                foreach ( $subjects_regular as $subject ): 
                                                                    if ( $subject_id == $subject->id ) {
                                                                        echo esc_html($subject->name);
                                                                    }
                                                                endforeach; 
                                                            ?>
                                                        </td>

                                                        <?php if ($sub_index === 0): ?>
                                                            <!-- <td rowspan="<?= $entry_count ?>"> -->
                                                                
                                                                <input type="hidden" name="matrix[<?= $matrix_index ?>][type]"
                                                                    value="<?= esc_attr($type) ?>" />
                                                            <!-- </td> -->

                                                            <td rowspan="<?= $entry_count ?>">
                                                                <?php if ($is_group_disabled): ?>
                                                                    <input type="hidden" name="matrix[<?= $matrix_index ?>][code_period]"
                                                                        value="<?= esc_attr($code_period) ?>" />
                                                                <?php endif; ?>

                                                                <select name="matrix[<?= $matrix_index ?>][code_period]"
                                                                    class="academic-period-select"
                                                                    style="width: 100%; min-width: 150px;"
                                                                    <?= $is_group_disabled ? 'disabled' : '' ?>>
                                                                    <option value="" <?= empty($code_period) ? 'selected' : '' ?>><?= __('Select Period', 'edusystem') ?></option>
                                                                    <?php foreach ($periods as $period) { ?>
                                                                        <option value="<?= esc_attr($period->code) ?>"
                                                                            <?= ($code_period == $period->code) ? 'selected' : '' ?>>
                                                                            <?= esc_html($period->name) ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>

                                                            <td rowspan="<?= $entry_count ?>">
                                                                <?php if ($is_group_disabled): ?>
                                                                    <input type="hidden" name="matrix[<?= $matrix_index ?>][cut]"
                                                                        value="<?= esc_attr($cut) ?>" />
                                                                <?php endif; ?>

                                                                <select name="matrix[<?= $matrix_index ?>][cut]" class="academic-cut-select"
                                                                    style="width: 100%; min-width: 150px;" <?= $is_group_disabled ? 'disabled' : '' ?>>
                                                                    <option value="" <?= empty($cut) ? 'selected' : '' ?>><?= __('Select cut', 'edusystem') ?></option>
                                                                    <?php foreach ($cuts as $c) { ?>
                                                                        <option value="<?= esc_attr($c) ?>" <?= ($cut == $c) ? 'selected' : '' ?>>
                                                                            <?= esc_html($c) ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                        <?php endif; ?>
                                                        <td>
                                                            <?= esc_html(ucfirst($status)); ?>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="margin-top: 20px; text-align: right;">
                                    <button type="submit"
                                        class="button button-primary button-large"><?= __('Save changes', 'edusystem'); ?></button>
                                </div>
                            </form>
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