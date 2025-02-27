<h2 style="font-size:24px;text-align:center;"><?= __('Notifications', 'aes'); ?></h2>

<div style="margin-top: 18px">
    <div style="display: flex; justify-content: space-evenly;">
        <div style="display: flex">
            <div style="background-color: #002fbd26; width: 24px; border-radius: 50%; margin-right: 10px"></div> Notice
        </div>
        <div style="display: flex">
            <div style="background-color: #ebf177; width: 24px; border-radius: 50%; margin-right: 10px"></div> Warning
        </div>
        <div style="display: flex">
            <div style="background-color: #f17777; width: 24px; border-radius: 50%; margin-right: 10px"></div> Urgent
        </div>
    </div>
    <div class="content-notification-card">
        <?php if ($notices) { ?>
            <?php foreach ($notices as $key => $value) { ?>
                <?php
                $importance = '';
                switch ($value->importance) {
                    case 1:
                        $importance = 'Notice';
                        $class = 'notification-notice';
                        break;
                    case 2:
                        $importance = 'Warning';
                        $class = 'notification-warning';
                        break;
                    case 3:
                        $importance = 'Urgent';
                        $class = 'notification-urgent';
                        break;
                }
                if ($value->type_notice == 'documents') {
                    global $current_user;
                    $roles = $current_user->roles; ?>
                    <a
                        href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . (in_array('teacher', $roles) ? '/teacher-documents' : '/student-documents') ?>">
                        <div class="notification-card <?php echo $class ?>">
                            <p><strong><?php echo $importance ?></strong> <span
                                    style="font-size: 12px; float: right"><?php echo $value->created_at ?></span></p>
                            <p><?php echo $value->message ?></p>
                        </div>
                    </a>
                <?php } else if ($value->type_notice == 'requests') { ?>
                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/my-requests' ?>">
                            <div class="notification-card <?php echo $class ?>">
                                <p><strong><?php echo $importance ?></strong> <span
                                        style="font-size: 12px; float: right"><?php echo $value->created_at ?></span></p>
                                <p><?php echo $value->message ?></p>
                            </div>
                        </a>
                <?php } else { ?>
                        <div class="notification-card <?php echo $class ?>">
                            <p><strong><?php echo $importance ?></strong> <span
                                    style="font-size: 12px; float: right"><?php echo $value->created_at ?></span></p>
                            <p><?php echo $value->message ?></p>
                        </div>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <p style="text-align: center;"><?= __('No notifications available', 'aes'); ?></p>
        <?php } ?>
    </div>
</div>