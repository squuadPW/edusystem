<div id="document_view" class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Document','edusystem'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=admission-documents'); ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder" >
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" id="form_document" action="<?= admin_url('admin.php?page=admission-documents&action=update_document'); ?>">
                            
                            <input type="hidden" name="document_id" value="<?= $document->id ?? 0 ?>">

                            <h3 class="title" >
                                <b><?= __('Document Information', 'edusystem'); ?></b>
                            </h3>

                            <div>
                                <label for="input_id">
                                    <b><?= __('Name','edusystem'); ?></b>

                                    <input type="text" name="name" value="<?= $document->name ?? ''; ?>" style="width:100%">

                                </label>
                                
                            </div>
                            
                            <div>
                                <label for="checkbox_id">
                                    <input name="is_required" type="checkbox" <?= ($document->is_required == 1) ? 'checked' : ''; ?> >
                                    
                                    <b><?= __('Required to access the virtual classroom in all areas','edusystem'); ?></b>
                                </label>
                            </div>

                            <div>
                                <h3 class="title" >
                                    <b><?= __('Scope of application','edusystem') ?></b>
                                </h3>

                                <?php
                                    global $wpdb;
                                    $payment_plans = $wpdb->get_results(
                                        $wpdb->prepare(
                                            "SELECT identificator, `name`, `description`, subprogram 
                                            FROM {$wpdb->prefix}programs "
                                        )
                                    );
                                ?>

                                <select id="select_scope" multiple >
                                    <?php if( $payment_plans ): ?>
                                        <?php foreach ($payment_plans as $payment_plan): ?>

                                            <optgroup label="<?= esc_attr($payment_plan->name) ?>">

                                                <option value="<?= esc_attr($payment_plan->identificator)?>" <?= selected( in_array( $payment_plan->identificator, json_decode($fee['programs'], true) ?? [] ) ); ?>>
                                                    <?= esc_html($payment_plan->name) ?> (<?= esc_html($payment_plan->description)?>)
                                                </option>

                                                <?php $payment_subplans = json_decode($payment_plan->subprogram, true); ?>
                                                <?php if($payment_subplans): ?>
                                                    <?php foreach ($payment_subplans as $payment_subplan_id => $payment_subplan): ?>
                                                        <option value="<?= esc_attr($payment_plan->identificator . '_' . $payment_subplan_id) ?>" <?= selected(in_array($payment_plan->identificator . '_' . $payment_subplan_id, json_decode($fee['programs'], true) ?? [])); ?>>
                                                            <?= esc_html($payment_subplan['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </optgroup>

                                        <?php endforeach ?>
                                    <?php endif; ?>
                                </select>
  
                                <div id="selected_list_header" style="display:flex;justify-content:space-between;margin-top:15px;font-weight:bold;">
                                    <span><?= __('Name','edusystem') ?></span>
                                    <span><?= __('Is it required?','edusystem') ?></span>
                                </div>

                                <div id="selected_list"></div>
                                
                            </div>

                            <div style="display:flex;width:100%;justify-content:end;">
                                <button class="button button-primary" type="submit"><?= __('Save Changes','edusystem'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>