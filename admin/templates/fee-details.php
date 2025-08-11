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
                                            <input type="number" name="price" value="<?= $fee['price'] ?? 0.00 ?>" step="0.01" min="1"  onkeydown="return !['-', 'e'].includes(event.key)" required >
                                        </div>
                                    </div>
                                    
                                    <div class="space-offer">
                                        <label for="description">
                                            <b><?= __('Description', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="description" ><?= $fee['description'] ?? '' ?></textarea>
                                    </div>

                                    <div class="space-offer">

                                            <label for="products">
                                                <b><?= __('Programs', 'edusystem'); ?></b>
                                                <span class="text-danger">*</span>
                                            </label>

                                            <?php
                                                global $wpdb;
                                                $programs = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}student_program");
                                            ?>
                                            
                                            <select name="programs[]" multiple required id="programas_select" >
                                                <?php if( $programs ): ?>
                                                    <?php foreach ($programs as $program): ?>
                                                        <option value="<?= esc_attr($program->id)?>" <?= selected( in_array( $program->id, json_decode($fee['programs'], true) ?? [] ) ); ?>><?= esc_html($program->name) ?></option>
                                                            <?= esc_html($program->name) ?>
                                                        </option>
                                                    <?php endforeach ?>
                                                <?php endif; ?>
                                            </select>
                                                        
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


