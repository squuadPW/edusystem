<div class="wrap">
    <?php if (isset($custom_input) && !empty($custom_input)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Custom Input Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Custom Input', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    // Assuming cookie-message.php exists and handles displaying messages
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_custom_input_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer"> <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_custom_input_content&action=save_custom_input_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Custom Input Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="custom_input_id" value="<?= isset($custom_input->id) ? $custom_input->id : '' ?>">

                                    <div style="font-weight:400;" class="space-offer"> <label for="label"><b><?= __('Label', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="label" value="<?= isset($custom_input->label) ? esc_attr($custom_input->label) : '' ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="page"><b><?= __('Page', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="page" value="<?= isset($custom_input->page) ? esc_attr($custom_input->page) : '' ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="input_mode"><b><?= __('Input Mode (HTML Tag Type)', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <select name="input_mode" id="input_mode_select" required>
                                            <option value="">Select HTML Tag Type</option>
                                            <option value="input" <?= (isset($custom_input->input_mode) && $custom_input->input_mode == 'input') ? 'selected' : ''; ?>>Input</option>
                                            <option value="select" <?= (isset($custom_input->input_mode) && $custom_input->input_mode == 'select') ? 'selected' : ''; ?>>Select</option>
                                            <option value="textarea" <?= (isset($custom_input->input_mode) && $custom_input->input_mode == 'textarea') ? 'selected' : ''; ?>>Textarea</option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="input_name"><b><?= __('Input Name', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="input_name" value="<?= isset($custom_input->input_name) ? esc_attr($custom_input->input_name) : '' ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="input_id"><b><?= __('Input ID', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="input_id" value="<?= isset($custom_input->input_id) ? esc_attr($custom_input->input_id) : '' ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer" id="input_type_field">
                                        <label for="input_type"><b><?= __('Input Type (for Input Tag)', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <select name="input_type" id="input_type_select" required>
                                            <option value="">Select an input type</option>
                                            <option value="text" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'text') ? 'selected' : ''; ?>>Text</option>
                                            <option value="number" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'number') ? 'selected' : ''; ?>>Number</option>
                                            <option value="email" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'email') ? 'selected' : ''; ?>>Email</option>
                                            <option value="date" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'date') ? 'selected' : ''; ?>>Date</option>
                                            <option value="radio" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'radio') ? 'selected' : ''; ?>>Radio Buttons</option>
                                            <option value="checkbox" <?= (isset($custom_input->input_type) && $custom_input->input_type == 'checkbox') ? 'selected' : ''; ?>>Checkbox</option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="input_required"><b><?= __('Required', 'edusystem'); ?></b></label><br>
                                        <select name="input_required">
                                            <option value="0" <?= (isset($custom_input->input_required) && $custom_input->input_required == '0') ? 'selected' : ''; ?>>No</option>
                                            <option value="1" <?= (isset($custom_input->input_required) && $custom_input->input_required == '1') ? 'selected' : ''; ?>>Yes</option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="input_is_metadata"><b><?= __('Is metadata?', 'edusystem'); ?></b></label><br>
                                        <select name="input_is_metadata">
                                            <option value="0" <?= (isset($custom_input->input_is_metadata) && $custom_input->input_is_metadata == '0') ? 'selected' : ''; ?>>No</option>
                                            <option value="1" <?= (isset($custom_input->input_is_metadata) && $custom_input->input_is_metadata == '1') ? 'selected' : ''; ?>>Yes</option>
                                        </select>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer" id="input_options_field">
                                        <label for="input_options"><b><?= __('Input Options (for Select and Radio)', 'edusystem'); ?></b><br><small>Comma-separated values (e.g., Option1,Option2,Option3)</small></label><br>
                                        <textarea name="input_options" id="input_options_textarea" rows="4" cols="50"><?= isset($custom_input->input_options) ? esc_textarea($custom_input->input_options) : '' ?></textarea>
                                    </div>

                                </div>
                            </div>

                            <?php if (isset($custom_input) && !empty($custom_input)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add Custom Input', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>