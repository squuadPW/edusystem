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
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="quota-rules-programs" class="metabox-holder admin-add-offer " style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_program_content&action=save_program_details'); ?>">
                            
                            <div class="quotas_rules"  >
                                        
                                <h3 class="title" >
                                    <b><?= __('Rules for quotas', 'edusystem'); ?></b>
                                </h3>

                                <div id="rules" data-rules_count="0" >

                                    <input type="hidden" name="program_id" value="<?= $program_id ?>" >

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
                                                <input type="number" name="rules[][initial_price]" value="0" disabled required>
                                            </div>

                                            <div class="space-offer">
                                                <label for="rules[][quantity]">
                                                    <b><?= __('Quotas quantity ', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number" name="rules[][quantity]" value="1"  min="1" step="1" disabled required >
                                            </div>

                                            <div class="space-offer">

                                                <label for="rules[][price]">
                                                    <b><?= __('Price', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="number" name="rules[][price]" value="0" disabled required>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <div class="container-button" >
                                    <button id="add-rule-button" type="button" class="button button-secondary" >
                                        <?=__('Add quota rule', 'edusystem')?>
                                    </button>
                                </div>

                            </div>

                            <?php if (isset($program) && !empty($program)): ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
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



