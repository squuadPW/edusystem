<div class="wrap">
    <?php if (isset($subject) && !empty($subject)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Subject Details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Subject', 'aes'); ?></h2>
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
            href="<?= admin_url('admin.php?page=add_admin_form_school_subjects_content'); ?>"><?= __('Back', 'aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_school_subjects_content&action=save_subject_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Subject Information', 'aes'); ?></b>
                                </h3>
                                <div style="text-align: center; margin: 18px">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <input type="hidden" name="subject_id" id="subject_id"
                                                value="<?= $subject->id; ?>">
                                            <div>
                                                <input type="checkbox" name="is_elective" id="is_elective"
                                                <?= ($subject->is_elective == 1) ? 'checked' : ''; ?>>
                                                <label for="is_elective"><b><?= __('Is elective', 'aes'); ?></b></label>
                                            </div>
                                        <?php else: ?>
                                            <input type="hidden" name="subject_id" id="subject_id" value="">
                                            <div>
                                            <input type="checkbox" name="is_elective" id="is_elective">
                                                <label for="is_elective"><b><?= __('Is elective', 'aes'); ?></b></label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <label
                                                for="name"><b><?= __('Subject', 'aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="name" value="<?= ucwords($subject->name); ?>">
                                        <?php else: ?>
                                            <label for="name"><b><?= __('Subject', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="name" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <label
                                                for="code_subject"><b><?= __('Subject code (the same as moodle)', 'aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="code_subject" value="<?= ucwords($subject->code_subject); ?>">
                                        <?php else: ?>
                                            <label for="code_subject"><b><?= __('Subject code (the same as moodle)', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="code_subject" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <label
                                                for="description"><b><?= __('Description', 'aes'); ?></b></label><br>
                                            <textarea name="description"><?= $subject->description; ?></textarea>
                                        <?php else: ?>
                                            <label for="description"><b><?= __('Description', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <textarea name="description"></textarea>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <label
                                                for="hc"><b><?= __('HC', 'aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="hc" value="<?= ucwords($subject->hc); ?>">
                                        <?php else: ?>
                                            <label for="hc"><b><?= __('HC', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="hc" value="" required>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($subject) && !empty($subject)): ?>
                                            <label
                                                for="hc"><b><?= __('Moodle course ID', 'aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="moodle_course_id" value="<?= ucwords($subject->moodle_course_id); ?>">
                                        <?php else: ?>
                                            <label for="hc"><b><?= __('Moodle course ID', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <input type="text" name="moodle_course_id" value="" required>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($subject) && !empty($subject)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add subject', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>