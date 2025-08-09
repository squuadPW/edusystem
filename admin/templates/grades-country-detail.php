<div class="wrap">
    <?php if (isset($grades_country) && !empty($grades_country)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Grades by Country Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Grades by Country', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    // Assuming cookie-message.php exists and handles displaying messages
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_grades_country_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_grades_country_content&action=save_grades_country_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Grades Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="grade_country_id"
                                        value="<?= isset($grades_country->id) ? $grades_country->id : '' ?>">

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="country"><b><?= __('Country', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <select id="country" name="country"
                                            value="<?php echo get_name_country($grades_country->country); ?>"
                                            style="width:100%;" required>
                                            <?php foreach ($countries as $key => $country) { ?>
                                                <option value="<?= $key ?>"
                                                    <?= (get_name_country($grades_country->country == $key)) ? 'selected' : ''; ?>><?= $country; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="grades"><b><?= __('Grades (separated by commas)', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <textarea style="width: 100%" name="grades" id="grades" required><?= isset($grades_country->grades) ? esc_attr($grades_country->grades) : '' ?></textarea>
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($grades_country) && !empty($grades_country)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add Grades', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>