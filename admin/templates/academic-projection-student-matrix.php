<div class="wrap">

    <h2 style="margin-bottom:15px;"><?= __('Matrix', 'edusystem'); ?></h2>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div style="display:flex; justify: start; gap: 10px;">
            <?php
                // Using the referer URL is risky, let's use the student list page or admin_url() as a default
                $referer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url();
            ?>
            <a class="button button-outline-primary" href="<?= esc_url($referer_url); ?>"><?= __('Back', 'edusystem'); ?></a>
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

                        <?php if ( !empty($matrix) && is_array($matrix) ): ?>

                            <form method="post"
                                action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&action=update_matrix'); ?>">

                               <input type="hidden" name="student_id" value="<?= esc_attr($student_id); ?>" />

                                <div style="overflow-x: auto;">

                                    <table class="wp-list-table widefat fixed striped">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 5%;">#</th>
                                                <th scope="col" style="width: 30%;"><?= __('Subject', 'edusystem'); ?></th>
                                                <th scope="col" style="width: 30%;"><?= __('Academic Period', 'edusystem'); ?></th>
                                                <th scope="col" style="width: 30%;"><?= __('Academic Cut', 'edusystem'); ?>
                                                </th>
                                                <th scope="col" style="width: 15%;"><?= __('Status', 'edusystem'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ( $matrix as $key => $expected ): ?>

                                                <?php

                                                    // Para Periodo y Corte (valores únicos para el grupo) se extrae del índice 0 si es multi.
                                                    $code_period = $expected->academic_period;
                                                    $cut = $expected->academic_period_cut;

                                                    // verifica si la expected esta completada o en curso
                                                    $is_group_disabled = $expected->status == 'activa' || $expected->status == 'aprobada';

                                                    switch ( $expected->status ) {

                                                        case 'pendiente':
                                                            $status_text =  __('Pendiente', 'edusystem');
                                                            break;

                                                        case 'activa':
                                                            $status_text =  __('Activa', 'edusystem');
                                                            break;

                                                        case 'aprobada':
                                                            $status_text =  __('Aprobada', 'edusystem');
                                                            break;

                                                        case 'blocked':
                                                            $status_text = __('Blocked', 'edusystem');
                                                            break;

                                                        default:
                                                            $status_text = $expected->status;
                                                            break;
                                                    }
                                                ?>

                                                <tr id="matrix-row-<?= $expected->id ?>" <?= $expected->status == 'blocked' ? 'style="background: #eba3a3  !important; "' : ''?> >
                                                    
                                                    <input type="hidden" name="matrix[<?= $expected->id ?>][update_disable]" value="<?= esc_attr($is_group_disabled) ?>" />
                                                    <input type="hidden" name="matrix[<?= $expected->id ?>][subject_id]" value="<?= esc_attr($expected->subject_id) ?>" />

                                                    <td scope="row"><?= $key + 1; ?></td>
                                                        
                                                    <td>
                                                        <?php 
                                                            foreach ( $subjects as $subject ): 
                                                                if ( $expected->subject_id == $subject->id ) {
                                                                    echo esc_html($subject->name);
                                                                    echo "<input type='hidden' name='matrix[{$expected->id}][subject_name]' value='{$subject->name}' />";
                                                                    break;
                                                                }
                                                            endforeach; 
                                                        ?>
                                                    </td>

                                                    <td >
                                                        <?php if ($is_group_disabled): ?>
                                                            <input type="hidden" name="matrix[<?= $expected->id ?>][code_period]" value="<?= esc_attr($code_period) ?>" />
                                                        <?php endif; ?>

                                                        <select name="matrix[<?= $expected->id ?>][code_period]" class="academic-period-select"
                                                            <?= $is_group_disabled ? 'disabled' : '' ?> style="width: 100%; min-width: 150px;" >
                                                            
                                                            <option value="" <?= selected( $code_period, '' ) ?>><?= __('Select Period', 'edusystem') ?></option>
                                                            
                                                            <?php foreach ( $periods as $period ) { ?>
                                                                <option value="<?= esc_attr($period->code) ?>" <?= selected( $code_period, $period->code ) ?> >
                                                                    <?= esc_html($period->name) ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <td >

                                                        <?php if ($is_group_disabled): ?>
                                                            <input type="hidden" name="matrix[<?= $expected->id ?>][cut]" value="<?= esc_attr($cut) ?>" />
                                                        <?php endif; ?>

                                                        <select name="matrix[<?= $expected->id ?>][cut]" class="academic-cut-select"
                                                            <?= $is_group_disabled ? 'disabled' : '' ?> style="width: 100%; min-width: 150px;" >

                                                            <option value="" <?= selected($cut,'') ?>><?= __('Select cut', 'edusystem') ?></option>
                                                                    
                                                            <?php foreach ($cuts as $c) { ?>
                                                                <option value="<?= esc_attr($c) ?>" <?= selected($cut, $c)?>>
                                                                    <?= esc_html($c) ?>
                                                                </option>
                                                            <?php } ?>
                                                            
                                                        </select>
                                                        
                                                    </td>
                                                            
                                                        
                                                    <td><?= $status_text ?></td>
                                                </tr>

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