<div class="wrap">
    <?php if (isset($period) && !empty($period)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Period details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Create period', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div class="back-button-container">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer " style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content&action=save_period_details');  ?>">
                            <div>
                                <h3 class="form-section-title">
                                    <b><?= __('Period Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">

                                    <input type="hidden" id="period_id" name="period_id" value="<?= $period->id ?>">

                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                        <div>
                                            <input type="checkbox" id="status_id" style="width: auto !important;" name="status_id" value="1" <?= ( isset($period) || $period->status_id == 0 ) ? '' : 'checked'; ?>>
                                            <label for="status_id"><b><?= __('Active', 'edusystem'); ?></b></label>
                                        </div>
                                    </div>
                                    
                                    <div class="group-input">

                                        <div class="space-offer">
                                            <label for="code">
                                                <b><?= __('Code', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="text" id="code" name="code" minlength="3" oninput="validate_input(this, '^[A-Z0-9-]*$', true),check_periods_code_exists_js(this)" value="<?= esc_attr(ucwords($period->code ?? '')); ?>" <?= $period->code ? 'readonly' : 'required' ?> >
                                            <span id="error-period-code" class="input-error" style="display:none;" ><?=__('Code is already in use','edusystem')?></span>
                                        </div>

                                        <div class="space-offer">
                                            <label for="name">
                                                <b><?= __('Name', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="text" id="name" name="name" value="<?= esc_attr(ucwords($period->name ?? '')); ?>" >
                                        </div>
                                        
                                    </div>

                                    <div class="group-input">

                                        <div class="space-offer">
                                            <label for="start_date">
                                                <b><?= __('Start Date', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="date" id="start_date" name="start_date" value="<?= $period->start_date ?? '' ?>" required >
                                        </div>

                                        <div class="space-offer">
                                            <label for="end_date">
                                                <b><?= __('End Date', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="date" id="end_date" name="end_date" value="<?= $period->end_date ?? '' ?>" required>
                                        </div>

                                    </div>

                                    <div class="group-input">

                                        <div class="space-offer">
                                            <label for="name">
                                                <b><?= __('Year', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="number" id="year" name="year" value="<?= $period->year ?? '' ?>" required>
                                        </div>

                                        <div class="space-offer">
                                            <label for="code_next">
                                                <b><?= __('Next period code', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="text" id="code_next" name="code_next" value="<?= $period->code_next ?? '' ?>" required>
                                        </div>

                                    </div>

                                </div>

                                <br>

                                <div id="period-cuts-container" >
                                    <h3 class="form-section-title" >
                                        <b><?= __('Period cuts', 'edusystem'); ?></b>
                                    </h3>
                                    
                                    <div id="cuts" data-cuts_count="<?= count( $cuts ?? [] ) ?? 0  ?>" >
                                        
                                        <?php if( $cuts ): ?>

                                            <?php $i = 1; foreach ( $cuts AS $cut ) : ?>
                                                
                                                <div class="cut" >
                                                    
                                                    <input type="hidden" name="cuts[<?= $i ?>][id]" value="<?= $cut->id ?>" >

                                                    <div class="id" ><?= $cut->id ?></div>

                                                    <div class="space-offer">
                                                        <label for="cuts[<?= $i ?>][cut]">
                                                            <b><?= __('Name of the cut', 'edusystem'); ?></b>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <br>
                                                        <input type="text" name="cuts[<?= $i ?>][cut]" value="<?= $cut->cut ?? '' ?>" readonly >
                                                    </div>

                                                    <div class="group-input">

                                                        <div class="space-offer">
                                                            <label for="cuts[<?= $i ?>][start_date]">
                                                                <b><?= __('Start Date Cut', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <br>
                                                            <input type="date" name="cuts[<?= $i ?>][start_date]" value="<?= $cut->start_date ?? '' ?>" required >
                                                        </div>

                                                        <div class="space-offer">
                                                            <label for="cuts[<?= $i ?>][end_date]">
                                                                <b><?= __('End Date Cut', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <br>
                                                            <input type="date" name="cuts[<?= $i ?>][end_date]" value="<?= $cut->end_date ?? '' ?>" required>
                                                        </div>

                                                        <div class="space-offer">
                                                            <label for="cuts[<?= $i ?>][max_date]">
                                                                <b><?= __('Max Date Cut', 'edusystem'); ?></b>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <br>
                                                            <input type="date" name="cuts[<?= $i ?>][max_date]" value="<?= $cut->max_date ?? '' ?>" required>
                                                        </div>

                                                    </div>

                                                    <div class="container-button" >

                                                        <button type="button" class="button button-secodary" data-cut_id="<?= "{$cut->id}" ?>" data-cut="<?= "{$cut->cut}" ?>" data-period_code="<?= "{$cut->code}" ?>" onclick="modal_delete_cut_js(this)" ><?=__('Delete', 'edusystem')?></button>

                                                    </div>

                                                </div>

                                            <?php $i ++; endforeach; ?>

                                        <?php endif; ?>

                                        <div id="template-cut" class="cut"  >
                                                    
                                            <input type="hidden" name="cuts[][id]" disabled >

                                            <div class="space-offer">
                                                <label for="cuts[][cut]">
                                                    <b><?= __('Name of the cut', 'edusystem'); ?></b>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <br>
                                                <input type="text" name="cuts[][cut]" value="" oninput="validate_input(this, '^[A-Z0-9-]*$', true),check_cut_exists_js(this)" minlength="3" disabled required >
                                                <span class="input-error" style="display:none;" ><?=__('Cut is already in use','edusystem')?></span>
                                            </div>

                                            <div class="group-input">

                                                <div class="space-offer">
                                                    <label for="cuts[][start_date]">
                                                        <b><?= __('Start Date Cut', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <br>
                                                    <input type="date" name="cuts[][start_date]" disabled required >
                                                </div>

                                                <div class="space-offer">
                                                    <label for="cuts[][end_date]">
                                                        <b><?= __('End Date Cut', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <br>
                                                    <input type="date" name="cuts[][end_date]" disabled required>
                                                </div>

                                                <div class="space-offer">
                                                    <label for="cuts[][max_date]">
                                                        <b><?= __('Max Date Cut', 'edusystem'); ?></b>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <br>
                                                    <input type="date" name="cuts][max_date]" disabled required>
                                                </div>

                                            </div>

                                            <div class="container-button" >
                                                <button type="button" class="button button-secodary remove-rule-button" ><?=__('Delete', 'edusystem')?></button>
                                            </div>  
                                            
                                        </div>

                                    </div>
                                    
                                    <div >
                                        <button id="add-cuts" type="button" class="button button-secondary" ><?=__('Add cuts', 'edusystem')?></button>
                                    </div>

                                </div>

                            </div>
                           
                            <div style="padding-top: 10px;margin-top: 10px;display:flex;flex-direction:row;justify-content:end;gap:5px;border-top: 1px solid #8080805c;">
                                <button type="submit" id="save-period" class="button button-primary">
                                    <?php 
                                        if ( $cuts ): 
                                            echo __('Saves changes', 'edusystem'); 
                                        else:
                                            echo __('Add period', 'edusystem');
                                        endif; 
                                    ?>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(plugin_dir_path(__FILE__).'modal-delete-subprogram.php'); ?>


