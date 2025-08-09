<?php
if (is_user_logged_in()) {
    ?>
    <div class="title">
        <?= __('Applicant', 'edusystem'); ?>
    </div>

    <?php

    global $wpdb;
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $table_students = $wpdb->prefix . 'students';

    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_students WHERE email = %s", $email));
    if (isset($result)) {
        include(plugin_dir_path(__FILE__) . 'form-register-others.php');
    } else {
        ?>
        <section class="segment">
            <div class="segment-button active" data-option="me"><?= __('Me', 'edusystem'); ?></div>
            <div class="segment-button" data-option="others"><?= __('Others', 'edusystem'); ?></div>
        </section>

        <?php
        include(plugin_dir_path(__FILE__) . 'form-register-me.php');
        include(plugin_dir_path(__FILE__) . 'form-register-others-not-student.php');
    }

?>
<?php } else { ?>
    <div class="title">
        <?= __('Applicant', 'edusystem'); ?>
    </div>
    <?php
    include(plugin_dir_path(__FILE__) . 'student-registration-form.php');
?>
<?php } ?>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    // With the above scripts loaded, you can call `tippy()` with a CSS
    // selector and a `content` prop:
    tippy('#grade_tooltip', {
        content: 'Please select the grade you are currently studying',
    });
</script>