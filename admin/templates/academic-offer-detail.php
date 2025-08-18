<div class="wrap">
    <?php if (isset($offer) && !empty($offer)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Offer Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Offer', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_academic_offers_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_offers_content&action=save_offer_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Offer Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="offer_id" value="<?= $offer->id ?>">
                                    <input type="hidden" name="old_subject_id" value="<?= $offer->subject_id ?>">
                                    <input type="hidden" name="old_moodle_course_id" value="<?= $offer->moodle_course_id ?>">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="hc"><b><?= __('Subject', 'edusystem'); ?></b></label><br>
                                        <select name="subject_id" required>
                                            <option value="" selected>Assigns a subject to the offer</option>
                                            <?php foreach ($subjects as $subject) { ?>
                                                <option value="<?php echo $subject->id; ?>"
                                                    <?= ($offer->subject_id == $subject->id) ? 'selected' : ''; ?>>
                                                    <?= $subject->name ?> (<?= $subject->code_subject ?>)
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="hc"><b><?= __('School year', 'edusystem'); ?></b></label><br>
                                        <select name="code_period" required>
                                            <option value="" selected>Assigns a school year to the offer</option>
                                            <?php foreach ($periods as $period) { ?>
                                                <option value="<?php echo $period->code; ?>"
                                                    <?= ($offer->code_period == $period->code) ? 'selected' : ''; ?>>
                                                    <?= $period->name ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="hc"><b><?= __('Cut', 'edusystem'); ?></b></label><br>
                                        <select name="cut_period" required>
                                            <option value="">Assigns a cut to the offer</option>
                                            <option value="A" <?= (($offer->cut_period == 'A') ? 'selected' : '') ?>>A
                                            </option>
                                            <option value="B" <?= (($offer->cut_period == 'B') ? 'selected' : '') ?>>B
                                            </option>
                                            <option value="C" <?= (($offer->cut_period == 'C') ? 'selected' : '') ?>>C
                                            </option>
                                            <option value="D" <?= (($offer->cut_period == 'D') ? 'selected' : '') ?>>D
                                            </option>
                                            <option value="E" <?= (($offer->cut_period == 'E') ? 'selected' : '') ?>>E
                                            </option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="hc"><b><?= __('Teacher or person responsible', 'edusystem'); ?></b></label><br>
                                        <select name="teacher_id" required>
                                            <option value="" selected>Assigns a teacher to the offer</option>
                                            <?php foreach ($teachers as $teacher) { ?>
                                                <option value="<?php echo $teacher->id; ?>"
                                                    <?= ($offer->teacher_id == $teacher->id) ? 'selected' : ''; ?>>
                                                    <?php echo $teacher->name; ?>     <?php echo $teacher->middle_name; ?>
                                                    <?php echo $teacher->last_name; ?>
                                                    <?php echo $teacher->middle_last_name; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="max_students"><b><?= __('Maximum students enrolled', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="number" step="0" name="max_students"
                                            value="<?= $offer->max_students; ?>" required>
                                    </div>
                                    <div style="font-weight:400;" class="space-offer">
                                        <label
                                            for="moodle_course_id"><b><?= __('Moodle course ID', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <select name="moodle_course_id" class="js-example-basic" style="width: 100%;"
                                            required>
                                            <option value=""><?= __('Select a course', 'edusystem'); ?></option>
                                            <?php foreach ($courses as $course): ?>
                                                <option value="<?= $course['id']; ?>"
                                                    <?= ($offer->moodle_course_id == $course['id']) ? 'selected' : ''; ?>>
                                                    <?= $course['fullname']; ?> (<?= $course['shortname']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($offer) && !empty($offer)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add offer', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>