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

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer container-programs" style="width: 70%">
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
                                    <input type="hidden" name="product_id" value="<?= $program->product_id ?>">

                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                        <div>
                                            <input style="width: auto !important;" type="checkbox" name="is_active" id="is_active" <?= ($program->is_active == 1) ? 'checked' : ''; ?>>
                                            <label for="is_active"><b><?= __('Active', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="identificator"><b><?= __('Identificator', 'edusystem'); ?></b><span
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

                                    <?php if( $program ): ?>
                                        <div class="container-button" >
                                            <a href="<?= admin_url("/admin.php?page=add_admin_form_program_content&section_tab=quotas_rules_programs&program_id=$program->id" ) ?>" class="button button-primary" >
                                                <?=__('Quotas', 'edusystem')?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <br>

                                <div id="subprograms-container" >
                                    <h3 class="title" >
                                        <b><?= __('Subprograms', 'edusystem'); ?></b>
                                    </h3>

                                    <?php  
                                        $subprograms = json_decode( $program->subprogram, true ); 
                                        $i = 1; 
                                    ?>
                                    <div id="subprograms" data-subprogram_count="0" >

                                        <?php foreach ( $subprograms AS $subprogram_id => $subprogram ) :?>
                                            
                                            <div class="subprogram" >
                                                
                                                <input type="hidden" name="subprogram[<?= $i ?>][id]" value="<?= $subprogram_id ?>" >
                                                <input type="hidden" name="subprogram[<?= $i ?>][product_id]" value="<?= $subprogram['product_id'] ?>" >

                                                <div class="group-input" >

                                                    <div class="space-offer">
                                                        <label for="">
                                                            <b><?= __('Active', 'edusystem'); ?></b>
                                                        </label>
                                                        <br/>
                                                        <input type="checkbox" name="subprogram[<?= $i ?>][is_active]" value="<?= $subprogram['is_active'] ? 1 : ''; ?>" <?= ( $subprogram['is_active']) ? 'checked' : ''; ?> >
                                                    </div>

                                                    <div class="space-offer name">
                                                        <label for="">
                                                            <b><?= __('Name', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="text" name="subprogram[<?= $i ?>][name]" value="<?= $subprogram['name']; ?>" required>
                                                    </div>

                                                    <div class="space-offer">

                                                        <label for="">
                                                            <b><?= __('Price', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <input type="number" name="subprogram[<?= $i ?>][price]" value="<?= $subprogram['price']; ?>" required>
                                                    </div>
                                                </div>

                                                <div class="container-button" >
                                                    <button type="button" class="button button-secodary" ><?=__('Delete', 'edusystem')?></button>

                                                    <?php if( $program ): ?>
                                                        <a href="<?= admin_url("/admin.php?page=add_admin_form_program_content&section_tab=quotas_rules_programs&program_id=$program->id-$subprogram_id" ) ?>" class="button button-primary" >
                                                            <?=__('Quotas', 'edusystem')?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>

                                            </div>

                                        <?php $i ++; endforeach; ?>

                                        <div id="template-subprogram" class="subprogram"  >
                                            
                                            <div class="group-input" >

                                                <div class="space-offer">
                                                    <label for="subprogram[][is_active]">
                                                        <b><?= __('Active', 'edusystem'); ?></b>
                                                    </label>
                                                    <br/>
                                                    <input type="checkbox" name="subprogram[][is_active]" class="input-is_active" disabled checked >
                                                </div>

                                                <div class="space-offer name">
                                                    <label for="subprogram[][name]">
                                                        <b><?= __('Name', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="subprogram[][name]" class="input-name" disabled required>
                                                </div>

                                                <div class="space-offer">

                                                    <label for="subprogram[][price]">
                                                        <b><?= __('Price', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="number" name="subprogram[][price]" class="input-price" disabled required>
                                                </div>
                                            </div>

                                            <div class="container-button" >
                                                <button type="button" class="button button-secodary" ><?=__('Delete', 'edusystem')?></button>
                                            </div>

                                        </div>

                                    </div>
                                    
                                    <div >
                                        <button id="add-subprograms" type="button" class="button button-secondary" ><?=__('Add subprograms', 'edusystem')?></button>
                                    </div>

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
