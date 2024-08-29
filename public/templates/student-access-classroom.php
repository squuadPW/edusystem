<?php 
    $url = URL_LARAVEL_PPADMIN;
?>
<div class="content-dashboard">
    <h4 style="font-size:18px;text-align:center; margin-bottom: 20px"><?= __('Virtual Classroom','aes'); ?></h4>

    <div style="display:flex">
        <div style="width: 50%; text-align: end">
            <a style="width: 70% !important;" target="_blank" href="<?= home_url('?action=access_moodle_url&student_id='.$data->id); ?>" class="button button-primary"><?= __('Access','aes'); ?></a>
        </div>
        <div style="width: 50%; text-align: start">
            <a style="width: 70% !important;" target="_blank" href="<?= $url.'login-student?user='.$data->id_document; ?>" class="button button-primary"><?= __('Student area','aes'); ?></a>
        </div>
    </div>
</div>