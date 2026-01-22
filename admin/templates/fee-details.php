<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

<div class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Fee', 'edusystem'); ?></h2>

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
        <a class="button button-outline-primary" href="<?= admin_url("/admin.php?page=fees_content" ) ?? $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="admission_fees" class="metabox-holder admin-add-offer " style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=fees_content&action=save_fee'); ?>">
                            
                            <div class="fees_body"  >
                                        
                                <h3 class="title" >
                                    <b><?= __('Fee', 'edusystem'); ?></b>
                                </h3>

                                <div>

                                    <input type="hidden" name="fee_id" value="<?= $fee_id ?? 0 ?>" >
                                    <input type="hidden" name="product_id" value="<?= $fee['product_id'] ?? 0 ?>" >
                                    
                                    <div class="space-offer active">
                                        <label for="is_active">
                                            <b><?= __('Active', 'edusystem'); ?></b>
                                        </label>
                                        <br/>
                                        <input type="checkbox" name="is_active" <?= ( $fee && $fee['is_active'] == 0 ) ? '' : 'checked'; ?> >
                                    </div>
                                        
                                    <div class="group-input" >
                                        
                                        <div class="space-offer">
                                            <label for="name">
                                                <b><?= __('Name', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="name" value="<?= $fee['name'] ?? '' ?>" required>
                                        </div>

                                        <div class="space-offer">

                                            <label for="price">
                                                <b><?= __('Price', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="price" value="<?= $fee['price'] ?? 0.00 ?>" step="0.01" min="0"  onkeydown="return !['-', 'e'].includes(event.key)" required >
                                        </div>
                                    </div>
                                    
                                    <div class="space-offer">
                                        <label for="description">
                                            <b><?= __('Description', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" ><?= $fee['description'] ?? '' ?></textarea>
                                    </div>

                                    <?php do_action( 'edusystem_after_description_fee_fields', $fee_id ); ?>

                                    <div class="group-input" >

                                        <div class="space-offer" style="flex: 2;">

                                            <label for="products">
                                                <b><?= __('Programs', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>

                                            <?php
                                                global $wpdb;

                                                $where = "";
                                                if( is_plugin_active( 'dynamic-currency-edusystem/dynamic-currency-edusystem.php' ) ) {
                                                    
                                                    $currency = $fee['currency'] ?? get_woocommerce_currency();
                                                    $where = " WHERE currency = '{$currency}'";
                                                } 

                                                $payment_plans = $wpdb->get_results(
                                                    $wpdb->prepare(
                                                        "SELECT identificator, `name`, `description`, subprogram 
                                                        FROM {$wpdb->prefix}programs 
                                                        {$where}"
                                                    )
                                                );
                                            ?>
                                            
                                            <select name="programs[]" multiple id="programas_select" >
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
                                                        
                                        </div>

                                        <div class="space-offer" style="flex: 1;">

                                            <label for="type_fee">
                                                <b><?= __('Type', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>
                                            
                                            <select name="type_fee" required >
                                                <option value="" disabled <?= selected($fee['type_fee'], ''); ?>> <?= __('Select a type', 'edusystem'); ?> </option>
                                                <option value="registration" <?= selected($fee['type_fee'], 'registration'); ?>> <?= __('Registration', 'edusystem'); ?> </option>
                                                <option value="graduation" <?= selected($fee['type_fee'], 'graduation'); ?>> <?= __('Graduation', 'edusystem'); ?> </option>
                                                <option value="others" <?= selected($fee['type_fee'], 'others'); ?>> <?= __('Others', 'edusystem'); ?> </option>
                                            </select>
                                                        
                                        </div>

                                    </div>

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
                                        class="button button-primary"><?= __('Add fee', 'edusystem'); ?></button>
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


