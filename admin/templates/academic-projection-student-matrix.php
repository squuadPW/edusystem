<?php
global $current_user;
$roles = $current_user->roles;

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

    <div>
        <div style="display:flex; justify-content: start;">
            <?php
            $referer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url();
            ?>
            <a class="button button-outline-primary"
                href="<?= esc_url($referer_url); ?>"><?= __('Back', 'edusystem'); ?></a>
        </div>
        <div style="display:flex; justify-content: end;">
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
                        <h1 style="text-align: center;"><?= $student_full_name ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>