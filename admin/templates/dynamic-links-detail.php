<div class="wrap">
    <?php if (isset($dynamic_link) && !empty($dynamic_link)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Dynamic link Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Dynamic link', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_dynamic_link_content&action=save_dynamic_link_details'); ?>"
                            enctype="multipart/form-data">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Student Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="dynamic_link_id" value="<?= $dynamic_link->id ?>">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="type_document"><b><?= __('Type document', 'edusystem'); ?></b><span class="required">*</span></label>
                                        <select name="type_document" autocomplete="off" required>
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                            <option value="passport" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'passport') ? 'selected' : ''; ?>><?= __('Passport', 'edusystem'); ?></option>
                                            <option value="identification_document" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'identification_document') ? 'selected' : ''; ?>><?= __('Identification Document', 'edusystem'); ?></option>
                                            <option value="ssn" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->type_document == 'ssn') ? 'selected' : ''; ?>><?= __('SSN', 'edusystem'); ?></option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="id_document"><b><?= __('ID document', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="text" name="id_document"
                                            value="<?= $dynamic_link->id_document; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Name', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="text" name="name"
                                            value="<?= $dynamic_link->name; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="last_name"><b><?= __('Last name', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="text" name="last_name"
                                            value="<?= $dynamic_link->last_name; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="email"><b><?= __('Email', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                        <input type="text" name="email"
                                            value="<?= $dynamic_link->email; ?>" required>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Link Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="program_identificator"><b><?= __('Program', 'edusystem'); ?></b><span class="required">*</span></label>
                                        <select name="program_identificator" autocomplete="off" required>
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                            <?php foreach ($programs as $program): ?>
                                                <option value="<?= $program->identificator; ?>" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->program_identificator == $program->identificator) ? 'selected' : ''; ?>><?= $program->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="payment_plan_identificator"><b><?= __('Scholarship', 'edusystem'); ?></b><span class="required">*</span></label>
                                        <select name="payment_plan_identificator" autocomplete="off" required>
                                            <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                            <?php foreach ($payment_plans as $payment_plan): ?>
                                                <option value="<?= $payment_plan->identificator; ?>" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->payment_plan_identificator == $payment_plan->identificator) ? 'selected' : ''; ?>><?= $payment_plan->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <input style="width: auto !important;" type="checkbox" name="transfer_cr" value="<?= $dynamic_link->transfer_cr; ?>">
                                        <label for="transfer_cr"><b><?= __('Transfer CR', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($dynamic_link) && !empty($dynamic_link)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add dynamic link', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>