<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('EduSystem Autoenrollment', 'edusystem'); ?></h2>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <h4>For code and cut <?= $code . ' - ' . $cut ?></h4>
    <ul>
        <?php foreach ($expected_rows as $expected) : ?>
            <li>
                <?= $expected->student->first_name . ' ' . $expected->student->last_name . ' - ' . $expected->subject->name ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <button id="enroll-all-button">Enroll All</button>
</div>