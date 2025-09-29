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
                                        <input type="email" name="email"
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
                                                <option value="<?= $payment_plan->identificator; ?>" <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->payment_plan_identificator == $payment_plan->identificator) ? 'selected' : ''; ?>><?= $payment_plan->name; ?> (<?= $payment_plan->identificator; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <?php
                                        // Asume que $current_user está disponible y tiene los roles cargados
                                        if (function_exists('wp_get_current_user')) {
                                            $current_user = wp_get_current_user();
                                            $is_manager = in_array('manager', (array) $current_user->roles);
                                        } else {
                                            $is_manager = false;
                                        }
                                        ?>
                                        <?php if (!$is_manager): ?>
                                            <label for="payment_plan_identificator"><b><?= __('Manager', 'edusystem'); ?></b><span class="required">*</span></label>
                                            <select name="manager_id" autocomplete="off" required>
                                                <option value="" selected="selected"><?= __('Select an option', 'edusystem'); ?></option>
                                                <?php foreach ($managers as $manager): ?>
                                                    <option value="<?= esc_attr($manager->ID) ?>"
                                                        <?= (isset($dynamic_link) && !empty($dynamic_link) && $dynamic_link->manager_id == $manager->ID) ? 'selected' : ''; ?>>
                                                        <?= esc_html($manager->first_name) ?> <?= esc_html($manager->last_name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <input type="hidden" name="manager_id" value="<?= esc_attr($current_user->ID); ?>">
                                        <?php endif; ?>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <input type="checkbox" id="transfer_cr" style="width: auto !important;" name="transfer_cr" value="1" <?= (isset($dynamic_link) && $dynamic_link->transfer_cr == 1) ? 'checked' : ''; ?>>
                                        <label for="transfer_cr"><b><?= __('Transfer CR', 'edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                <button onclick='return confirm("Are you sure?");' type="submit"
                                    class="button button-success" name="save_and_send_email" value="1"><?= __('Save and send email', 'edusystem'); ?></button>
                                <button type="submit"
                                    class="button button-primary" name="just_save" value="1"><?= __('Just save', 'edusystem'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($dynamic_link)) : ?>
        <div id="dashboard-widgets" class="metabox-holder admin-add-offer">
            <div id="postbox-container-1" style="width:100% !important;">
                <div id="normal-sortables">
                    <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                        <div class="inside">
                            <h3 style="margin-top:20px; text-align:center; border-bottom: 1px solid #8080805c;">
                                <b><?= __('Email Send History', 'edusystem'); ?></b>
                            </h3>
                            <?php if (!empty($dynamic_links_email_log) && is_array($dynamic_links_email_log)): ?>
                                <div style="overflow-x:auto; margin-top: 15px;">
                                    <table class="wp-list-table widefat fixed striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th><?= __('Date', 'edusystem'); ?></th>
                                                <th><?= __('Email', 'edusystem'); ?></th>
                                                <th><?= __('Send by', 'edusystem'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($dynamic_links_email_log as $log): ?>
                                                <?php
                                                    $send_by_user = get_user_by('id', $log->created_by);
                                                ?>
                                                <tr>
                                                    <td><?= esc_html(isset($log->created_at) ? $log->created_at : ''); ?></td>
                                                    <td><?= esc_html(isset($log->email) ? $log->email : ''); ?></td>
                                                    <td><?= esc_html($send_by_user ? $send_by_user->first_name . ' ' . $send_by_user->last_name : ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p style="text-align:center; margin-top:15px;">
                                    <?= __('No email send records found.', 'edusystem'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>