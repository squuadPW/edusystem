<?php 
    $url = URL_LARAVEL_PPADMIN;
    $access = is_enrolled_in_courses($data->id);
?>
<div class="content-dashboard">
    <h4 style="font-size:18px;text-align:center; margin-bottom: 20px"><?= __('Access','aes'); ?></h4>

    <div style="display:flex">
        <div style="width: 50%; text-align: end">
            <a style="width: 70% !important; <?php echo count($access) == 0 ? 'background-color: #091c5c75 !important; pointer-events: none;' : '' ?>" target="_blank" href="<?= home_url('?action=access_moodle_url&student_id='.$data->id); ?>" class="button button-primary"><?= __('Virtual Classroom','aes'); ?></a>
        </div>
        <div style="width: 50%; text-align: start">
            <a style="width: 70% !important; <?php echo count($access) == 0 ? 'background-color: #091c5c75 !important; pointer-events: none;' : '' ?>" target="_blank" href="<?= $url.'login-student?user='.$data->id_document; ?>" class="button button-primary"><?= __('Student area','aes'); ?></a>
        </div>
    </div>
</div>