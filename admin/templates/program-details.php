<div class="wrap">
    <?php if (isset($program) && !empty($program)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Program details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Create program', 'edusystem'); ?></h2>
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
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer" style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_program_content&action=save_program_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Program Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="program_id" value="<?= $program->id ?>">
                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                        <div>
                                            <input style="width: auto !important;" type="checkbox" name="is_active" id="is_active" <?= ($program->is_active == 1) ? 'checked' : ''; ?>>
                                            <label for="status"><b><?= __('Active', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Identificator', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="identificator" value="<?= $program->identificator; ?>" <?= $program->identificator ? 'readonly' : 'required' ?> >
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Name', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="name" value="<?= $program->name; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="total_price">
                                            <b><?= __('Price', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <br>

                                        <input type="number" name="total_price" value="<?= $program->total_price; ?>" required>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="description"><b><?= __('Description', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <textarea style="width: 100%" name="description" id="description" rows="4" required><?= $program->description; ?></textarea>
                                    </div>
                                </div>

                                <br>

                                <div class="quotas_rules"  >
                                    <h3 class="title" >
                                        <b><?= __('Rules for quotas', 'edusystem'); ?></b>
                                    </h3>

                                    <button id="add-rule-button" type="button" class="button button-primary" ><?=__('add quota rule', 'edusystem')?></button>
                                    
                                    <div id="rules" >
                                        <div class="rule" >
                                            
                                            <input type="hidden" name="" value="">

                                            <div class="group-input" >

                                                <div class="space-offer">
                                                    <label for="">
                                                        <b><?= __('Active', 'edusystem'); ?></b>
                                                    </label>
                                                    <br/>
                                                    <input type="checkbox" name="" value="" required>
                                                </div>

                                                <div class="space-offer">
                                                    <label for="">
                                                        <b><?= __('Name', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="" value="" required>
                                                </div>
                                            </div>

                                            <div class="group-input" >
                                                <div class="space-offer">

                                                    <label for="">
                                                        <b><?= __('Initial price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="" value="" required>
                                                </div>

                                                <div class="space-offer">
                                                    <label for="">
                                                        <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="number" name="" value="1" required >
                                                </div>

                                                <div class="space-offer">

                                                    <label for="">
                                                        <b><?= __('Price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="number" name="" value="" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <?php if (isset($program) && !empty($program)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add program', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    (function(){
        let ruleCount = 1;
        document.getElementById('add-rule-button').addEventListener('click', function() {
            const ruleTemplate = document.querySelector('.rule');
            const newRule = ruleTemplate.cloneNode(true);
            newRule.querySelectorAll('input, label').forEach(el => {
                if(el.name){
                    el.name = el.name.replace(/\d+/, ruleCount);
                }
                if(el.id){
                    el.id = el.id.replace(/\d+/, ruleCount);
                }
                if(el.tagName.toLowerCase() === 'input') {
                    if(el.type === 'checkbox') {
                        el.checked = true;
                    } else if(el.type === 'number') {
                    if(el.name.includes('initial_price') || el.name.includes('quotas_quantity') || el.name.includes('quote_price')) {
                            el.value = '0';
                        } else {
                            el.value = '';
                        }
                    } else if(el.type === 'text'){
                        el.value = '';
                    }
                }
            });
            const hiddenId = newRule.querySelector('input[type="hidden"]');
            if(hiddenId) hiddenId.value = '';
            document.getElementById('rules').appendChild(newRule);
            ruleCount++;
        });
    })();
</script>