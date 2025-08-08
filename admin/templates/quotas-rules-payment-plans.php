<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

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
        <a class="button button-outline-primary" href="<?= admin_url("/admin.php?page=add_admin_form_payments_plans_content&section_tab=program_details&program_id=$program_id" ) ?? $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="quota-rules-programs" class="metabox-holder admin-add-offer " style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_payments_plans_content&action=save_quotas_rules'); ?>">
                            
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
                                                <input type="hidden" name="rules[<?= $i ?>][position]" value="<?= $rule['position'] ?? 0 ?>" >

                                                <div class="id" ><?= $rule['id'] ?></div>

                                                <div class="group-input" >

                                                    <div class="space-offer active">
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

                                                <div class="seccion-input" >

                                                    <label class="seccion-title" >
                                                        <b><?= __('Initial payment', 'edusystem'); ?></b>
                                                    </label>

                                                    
                                                    <div class="group-input" >

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][initial_payment]">
                                                                <b><?= __('Regular price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" name="rules[<?= $i ?>][initial_payment]" value="<?= $rule['initial_payment'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][initial_payment_sale]">
                                                                <b><?= __('Sale price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" name="rules[<?= $i ?>][initial_payment_sale]" value="<?= $rule['initial_payment_sale'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="seccion-input" >

                                                    <label class="seccion-title" >
                                                        <b><?= __('Final payment', 'edusystem'); ?></b>
                                                    </label>

                                                    <div class="group-input" >

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][final_payment]">
                                                                <b><?= __('Regular price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" name="rules[<?= $i ?>][final_payment]" value="<?= $rule['final_payment'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][final_payment_sale]">
                                                                <b><?= __('Sale price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" name="rules[<?= $i ?>][final_payment_sale]" value="<?= $rule['final_payment_sale'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                    </div>

                                                </div>
                                                
                                                <div class="seccion-input" >

                                                    <label class="seccion-title" >
                                                        <b><?= __('Quotas', 'edusystem'); ?></b>
                                                    </label>

                                                    <div class="group-input" >

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][quote_price]">
                                                                <b><?= __('Regular price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <input type="number" name="rules[<?= $i ?>][quote_price]" value="<?= $rule['quote_price'] ?? 0.00 ?>" step="0.01" min="0" onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][quote_price_sale]">
                                                                <b><?= __('Sale price', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" name="rules[<?= $i ?>][quote_price_sale]" value="<?= $rule['quote_price_sale'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required>
                                                        </div>

                                                    </div>

                                                    <div class="group-input" >

                                                        <div class="space-offer">
                                                            <label for="rules[<?= $i ?>][quotas_quantity]">
                                                                <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <input type="number" name="rules[<?= $i ?>][quotas_quantity]" value="<?= $rule['quotas_quantity'] ?? 0 ?>" min="0" step="1" onkeydown="return !['.', '-', 'e'].includes(event.key)" required >
                                                        </div>

                                                        <div class="space-offer">

                                                            <label for="rules[<?= $i ?>][frequency_value]">
                                                                <b><?= __('Frequency', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-frequency" >
                                                                <input type="number" name="rules[<?= $i ?>][frequency_value]" value="<?= $rule['frequency_value'] ?? 0 ?>" step="1" min="0" onkeydown="return !['.', '-', 'e'].includes(event.key)" required >

                                                                <select name="rules[<?= $i ?>][type_frequency]" >
                                                                    <option value="" <?= ($rule['type_frequency'] == '') ? 'selected' : '' ?> ><?= __('Frequency type','edusystem') ?></option>
                                                                    <option value="day" <?= ($rule['type_frequency'] == 'day') ? 'selected' : '' ?> ><?= __('Day','edusystem') ?></option>
                                                                    <option value="month" <?= ($rule['type_frequency'] == 'month') ? 'selected' : '' ?> ><?= __('Month','edusystem') ?></option>
                                                                    <option value="year" <?= ($rule['type_frequency'] == 'year') ? 'selected' : '' ?> ><?= __('Year','edusystem') ?></option>
                                                                </select>

                                                            </div>
                                                            
                                                        </div>
                                                        
                                                    </div>

                                                </div>

                                                <div class="container-button" >
                                                    <button type="button" data-rule_id="<?= $rule['id'] ?>" class="button button-secondary " onclick="modal_delete_quota_rule_js( this )" ><?= __('Delete', 'edusystem'); ?></button>
                                                </div>

                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>

                                    <div id="template-quota-rule" class="rule" >

                                        <input type="hidden" name="rules[][position]" >

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

                                        <div class="seccion-input" >

                                            <label class="seccion-title" >
                                                <b><?= __('Initial payment', 'edusystem'); ?></b>
                                            </label>
                                                    
                                            <div class="group-input" >

                                                <div class="space-offer">

                                                    <label for="rules[][initial_payment]">
                                                        <b><?= __('Regular price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="rules[][initial_payment]" value="0.00" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                                <div class="space-offer">

                                                    <label for="rules[][initial_payment_sale]">
                                                        <b><?= __('Sale price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="rules[][initial_payment_sale]" value="0.00" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="seccion-input" >

                                            <label class="seccion-title" >
                                                <b><?= __('Final payment', 'edusystem'); ?></b>
                                            </label>

                                            <div class="group-input" >

                                                <div class="space-offer">

                                                    <label for="rules[][final_payment]">
                                                        <b><?= __('Regular price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="rules[][final_payment]" value="0.00" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                                <div class="space-offer">

                                                    <label for="rules[][final_payment_sale]">
                                                        <b><?= __('Sale price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="rules[][final_payment_sale]" value="0.00" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                            </div>

                                        </div>
                                                
                                        <div class="seccion-input" >

                                            <label class="seccion-title" >
                                                <b><?= __('Quotas', 'edusystem'); ?></b>
                                            </label>

                                            <div class="group-input" >

                                                <div class="space-offer">

                                                    <label for="rules[][quote_price]">
                                                        <b><?= __('Regular price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="number" name="rules[][quote_price]" value="0.00" step="0.01" min="0" onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                                <div class="space-offer">

                                                    <label for="rules[][quote_price_sale]">
                                                        <b><?= __('Sale price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="rules[][quote_price_sale]" value="0.00" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" disabled required>
                                                </div>

                                            </div>

                                            <div class="group-input" >

                                                <div class="space-offer">
                                                    <label for="rules[][quotas_quantity]">
                                                        <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="number" name="rules[][quotas_quantity]" value="0" min="0" step="1" onkeydown="return !['.', '-', 'e'].includes(event.key)" disabled required >
                                                </div>

                                                <div class="space-offer">

                                                    <label for="rules[][frequency_value]">
                                                        <b><?= __('Frequency', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <div class="input-frequency" >
                                                        <input type="number" name="rules[][frequency_value]" value="0" step="1" min="0" onkeydown="return !['.', '-', 'e'].includes(event.key)" disabled required >

                                                        <select name="rules[][type_frequency]" disabled >
                                                            <option value="" ><?= __('Frequency type','edusystem') ?></option>
                                                            <option value="day" ><?= __('Day','edusystem') ?></option>
                                                            <option value="month" ><?= __('Month','edusystem') ?></option>
                                                            <option value="year" ><?= __('Year','edusystem') ?></option>
                                                        </select>

                                                    </div>
                                                            
                                                </div>
                                                        
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


