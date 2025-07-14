<div class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Rules for quotas', 'edusystem'); ?></h2>

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
        <a class="button button-outline-primary" href="<?= admin_url("/admin.php?page=add_admin_form_program_content&section_tab=program_details&program_id=$program_id" ) ?? $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="quota-rules-programs" class="metabox-holder admin-add-offer " style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_program_content&action=save_quotas_rules'); ?>">
                            
                            <div class="quotas_rules"  >
                                        
                                <h3 class="title" >
                                    <b><?= __('Rules for quotas', 'edusystem'); ?></b>
                                </h3>

                                <div id="rules" data-rules_count="<?= count($rules) ?? 0 ?>" >

                                    <input type="hidden" name="program_id" value="<?= $program_id ?>" >
                                    <input type="hidden" name="identificator" value="<?= $identificator ?>" >

                                    <?php if (isset($rules) && !empty($rules)): ?>
                                        <?php foreach ($rules AS $i => $rule): ?>
                                            <div class="rule" >

                                                <input type="hidden" name="rules[<?= $i ?>][id]" value="<?= $rule['id'] ?>" >

                                                <div class="group-input" >

                                                    <div class="space-offer">
                                                        <label for="rules[<?= $i ?>][is_active]">
                                                            <b><?= __('Active', 'edusystem'); ?></b>
                                                        </label>
                                                        <br/>
                                                        <input type="checkbox" name="rules[<?= $i ?>][is_active]" <?= ( $rule['is_active'] ) ? 'checked' : ''; ?> >
                                                    </div>

                                                    <div class="space-offer">
                                                        <label for="rules[<?= $i ?>][name]">
                                                            <b><?= __('Name', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="text" name="rules[<?= $i ?>][name]" value="<?= $rule['name'] ?>" required>
                                                    </div>
                                                </div>

                                                <div class="group-input" >
                                                    <div class="space-offer">

                                                        <label for="rules[<?= $i ?>][initial_price]">
                                                            <b><?= __('Initial price', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" name="rules[<?= $i ?>][initial_price]" value="<?= $rule['initial_price'] ?? 0.00 ?>" oninput="validate_input(this, '^[0-9]*\.?[0-9]*$')"  required>
                                                    </div>

                                                    <div class="space-offer">
                                                        <label for="rules[<?= $i ?>][quantity]">
                                                            <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="number" name="rules[<?= $i ?>][quantity]" value="<?= $rule['quotas_quantity'] ?? 0 ?>" oninput="validate_input(this, '^[0-9]*$')"  min="1" step="1"  required >
                                                    </div>

                                                    <div class="space-offer">

                                                        <label for="rules[<?= $i ?>][price]">
                                                            <b><?= __('Price', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="number" name="rules[<?= $i ?>][price]" value="<?= $rule['quote_price'] ?? 0.00 ?>" oninput="validate_input(this, '^[0-9]*\.?[0-9]*$')" required>
                                                    </div>

                                                </div>

                                                <div class="group-input" >
                                                    <div class="space-offer">

                                                        <label for="rules[<?= $i ?>][frequency_value]">
                                                            <b><?= __('Frequency', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <div class="input-frequency" >
                                                            <input type="number" name="rules[<?= $i ?>][frequency_value]" value="<?= $rule['frequency_value'] ?? 0 ?>" oninput="validate_input(this, '^[0-9]*$')" >

                                                            <select name="rules[<?= $i ?>][type_frequency]" required >
                                                                <option value="" <?= ($rule['type_frequency'] == '') ? 'selected' : '' ?> ><?= __('Select a frequency type','edusystem') ?></option>
                                                                <option value="day" <?= ($rule['type_frequency'] == 'day') ? 'selected' : '' ?> ><?= __('Day','edusystem') ?></option>
                                                                <option value="month" <?= ($rule['type_frequency'] == 'month') ? 'selected' : '' ?> ><?= __('Month','edusystem') ?></option>
                                                                <option value="year" <?= ($rule['type_frequency'] == 'year') ? 'selected' : '' ?> ><?= __('Year','edusystem') ?></option>
                                                            </select>

                                                        </div>
                                                        
                                                    </div>

                                                    <div class="space-offer">

                                                        <label for="rules[<?= $i ?>][position]">
                                                            <b><?= __('Position', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="number" name="rules[<?= $i ?>][position]" value="<?= $rule['position'] ?? 0 ?>" oninput="validate_input(this, '^[0-9]*$')" required>
                                                    </div>

                                                </div>

                                                <div class="container-button" >
                                                    <button type="button" data-rule_id="<?= $rule['id'] ?>" class="button button-secondary " onclick="modal_delete_quota_rule_js( this )" ><?= __('Delete', 'edusystem'); ?></button>
                                                </div>

                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>

                                    <div id="template-quota-rule" class="rule" >

                                        <div class="group-input" >

                                            <div class="space-offer">
                                                <label for="rules[][is_active]">
                                                    <b><?= __('Active', 'edusystem'); ?></b>
                                                </label>
                                                <br/>
                                                <input type="checkbox" name="rules[][is_active]" disabled checked >
                                            </div>

                                            <div class="space-offer">
                                                <label for="rules[][name]">
                                                    <b><?= __('Name', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="text" name="rules[][name]" disabled required>
                                            </div>
                                        </div>

                                        <div class="group-input" >
                                            <div class="space-offer">

                                                <label for="rules[][initial_price]">
                                                    <b><?= __('Initial price', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="rules[][initial_price]" value="0" oninput="validate_input(this, '^[0-9]*\\.?[0-9]*$')" disabled required>
                                            </div>

                                            <div class="space-offer">
                                                <label for="rules[][quantity]">
                                                    <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number" name="rules[][quantity]" value="0" oninput="validate_input(this, '^[0-9]*$')"  min="1" step="1" disabled required >
                                            </div>

                                            <div class="space-offer">

                                                <label for="rules[][price]">
                                                    <b><?= __('Price', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number" name="rules[][price]" value="0" oninput="validate_input(this, '^[0-9]*\\.?[0-9]*$')" disabled required>
                                            </div>
                                        </div>

                                        <div class="group-input" >
                                            <div class="space-offer">

                                                <label for="rules[][initial_price]">
                                                    <b><?= __('Frequency', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <div class="input-frequency" >
                                                    <input type="number" name="rules[][frequency_value]" value="0" oninput="validate_input(this, '^[0-9]*$')" disabled >

                                                    <select name="rules[][type_frequency]" disabled required >
                                                        <option value="" ><?= __('Select a frequency type','edusystem') ?></option>
                                                        <option value="day"><?= __('Day','edusystem') ?></option>
                                                        <option value="month"><?= __('Month','edusystem') ?></option>
                                                        <option value="year"><?= __('Year','edusystem') ?></option>
                                                    </select>

                                                </div>
                                                
                                            </div>

                                            <div class="space-offer">

                                                <label for="rules[][position]">
                                                    <b><?= __('Position', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number" name="rules[][position]" value="0" oninput="validate_input(this, '^[0-9]*$')" disabled required>
                                            </div>

                                        </div>

                                        <div class="container-button" >
                                            <button type="button" class="button button-secondary remove-rule-button"><?= __('Delete', 'edusystem'); ?></button>
                                        </div>

                                    </div>

                                </div>

                                <div >
                                    <button id="add-rule-button" type="button" class="button button-secondary" >
                                        <?=__('Add quota rule', 'edusystem')?>
                                    </button>
                                </div>

                            </div>

                            <?php if (isset($rules) && !empty($rules)): ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add rules', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(plugin_dir_path(__FILE__).'modal-delete-quota-rule.php'); ?>


