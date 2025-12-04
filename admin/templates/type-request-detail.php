<div class="wrap">
    <?php if (isset($type) && !empty($type)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Type Details', 'edusystem'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Type', 'edusystem'); ?></h2>
    <?php endif; ?>

    <?php
    include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_requests_content&section_tab=types'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-type">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_requests_content&action=save_type_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Type Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;" class="group-inputs">
                                    <input type="hidden" name="type_id" value="<?= $type->id ?>">
                                    <input type="hidden" name="type_product_id" value="<?= $type->product_id ?>">

                                    <div style="font-weight:400;" class="space-type">
                                        <label for="type"><b><?= __('Name', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <br>

                                        <input type="text" name="type" value="<?= $type->type; ?>" required>
                                    </div>

                                    <br>

                                    <div style="font-weight:400;" class="space-type">
                                        <label for="type">
                                            <b><?= __('Price', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <br>

                                        <input type="number" name="price" value="<?= $type->price; ?>" step="0.01" min="0" onkeydown="return !['-', 'e'].includes(event.key)" required>
                                    </div>

                                    <br>

                                    <?php do_action( 'edusystem_after_price_request', $type->id ); ?>

                                    <div style="font-weight:400;" class="space-type">

                                        <label for="type">
                                            <b><?= __('Documents certificates', 'edusystem'); ?></b>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <br>

                                        <?php
                                            global $wpdb;
                                            $documents_certificates = $wpdb->get_results("SELECT id, title FROM `{$wpdb->prefix}documents_certificates`");
                                        ?>

                                        <select name="document_certificate_id" required>
                                            <option value=""><?= __('Select certificates templates', 'edusystem'); ?>
                                            </option>

                                            <?php if ($documents_certificates): ?>
                                                <?php foreach ($documents_certificates as $document_certificate): ?>
                                                    <option value="<?= $document_certificate->id ?>"
                                                        <?= $type->document_certificate_id == $document_certificate->id ? 'selected' : ''; ?>><?= $document_certificate->title ?></option>
                                                <?php endforeach ?>
                                            <?php endif; ?>

                                        </select>

                                    </div>

                                </div>
                            </div>

                            <?php if (isset($type) && !empty($type)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add type', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>