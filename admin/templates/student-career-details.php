<div class="wrap">
    <?php if (isset($career) && !empty($career)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Career details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Create career', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer container-programs" style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_student_program_content&action=save_career_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Career Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">

                                    <input type="hidden" name="career_id" value="<?= $career->id ?>">

                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                        <div>
                                            <input style="width: auto !important;" type="checkbox" name="is_active"
                                                id="is_active" <?= (!isset($career->is_active) || $career->is_active == 1) ? 'checked' : ''; ?>>
                                            <label for="is_active"><b><?= __('Active', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;">
                                        <label for="hc"><b><?= __('Program to which it belongs', 'edusystem'); ?></b></label><br>
                                        <select name="program_identificator" required>
                                            <option value="" selected>Select a program</option>
                                            <?php foreach ($programs as $key => $program) { ?>
                                                <option value="<?= $program->identificator ?>" <?= ($career->program_identificator == $program->identificator) ? 'selected' : ''; ?>><?= $program->name ?> (<?= $program->description ?>)</option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="identificator">
                                            <b><?= __('Identificator', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <br>

                                        <div>
                                            <input type="text" name="identificator"
                                                oninput="validate_input(this, '^[A-Z0-9-]*$', true),check_career_identificator_exists_js(this)"
                                                value="<?= $career->identificator; ?>" <?= $career->identificator ? 'readonly' : 'required' ?>>
                                            <span id="error-identificator" class="input-error"></span>
                                        </div>

                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Name', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="name" value="<?= $career->name; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="description"><b><?= __('Description', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <textarea style="width: 100%" name="description" id="description" rows="4"
                                            required><?= $career->description; ?></textarea>
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($career) && !empty($career)): ?>
                                <div
                                    style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div
                                    style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add career', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>

                    <?php if (isset($related_mentions) && count($related_mentions) > 0) { ?>
                        <div class="inside">
                            <h3 style="margin-top:20px;margin-bottom:20px;text-align:center; border-bottom: 1px solid #8080805c; padding-bottom: 10px;">
                                <b><?= __('Related mentions', 'edusystem'); ?></b>
                            </h3>
                            <div>
                                <ul>
                                    <?php foreach($related_mentions as $key => $mention) { ?>
                                        <li class="career-item">
                                            <h4 class="career-name"><?= $mention->name; ?></h4>
                                            <span class="career-id">ID: <?= $mention->identificator ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>