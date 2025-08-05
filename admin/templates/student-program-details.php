<div class="wrap">
    <?php if (isset($program) && !empty($program)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Program details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Create program', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer container-programs" style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_student_program_content&action=save_program_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Program Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">

                                    <input type="hidden" name="program_id" value="<?= $program->id ?>">

                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                        <div>
                                            <input style="width: auto !important;" type="checkbox" name="is_active" id="is_active" <?= ( !isset( $program->is_active ) || $program->is_active == 1 ) ? 'checked' : ''; ?>>
                                            <label for="is_active"><b><?= __('Active', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="identificator">
                                            <b><?= __('Identificator', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>
                                        
                                        <br>

                                        <div>
                                            <input type="text" name="identificator" oninput="validate_input(this, '^[A-Z0-9-]*$', true),check_student_program_identificator_exists_js(this)" value="<?= $program->identificator; ?>" <?= $program->identificator ? 'readonly' : 'required' ?> >
                                            <span id="error-identificator" class="input-error" ></span>
                                        </div>
                                        
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Name', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="name" value="<?= $program->name; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="description"><b><?= __('Description', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <textarea style="width: 100%" name="description" id="description" rows="4" required><?= $program->description; ?></textarea>
                                    </div>

                                    <div style="font-weight:400;">
                                        <label for="hc"><b><?= __('Associated payment plans', 'edusystem'); ?></b></label><br>
                                        
                                        <!-- Se agrega el atributo 'multiple' y se cambia el nombre a un array (associated_plans[]) -->
                                        <select name="associated_plans[]" multiple style="min-height: 150px; width: 100%;" required>
                                            <?php foreach ($payment_plans as $key => $plan) { ?>
                                                <!-- La lógica de 'selected' ahora revisa si el identificador del plan está en el array $associated_plans_ids -->
                                                <option 
                                                    value="<?= $plan->identificator ?>" 
                                                    <?= in_array($plan->identificator, $associated_plans_ids) ? 'selected' : ''; ?>>
                                                    <?= $plan->name ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($program) && !empty($program)): ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add program', 'edusystem'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(plugin_dir_path(__FILE__).'modal-delete-subprogram.php'); ?>


