<div id="modal-request" class="modal" style="display: none">

    <div class="modal-content" style="overflow: auto; padding: 0 !important">
        <div class="modal-header p-5">
            <h3 style="font-weight: 600"><?=__('New request', 'edusystem')?></h3>
            <span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
        </div>
    
        <div class="modal-body">
            <form id="form-send-request" >

                <div>

                    <input id="product-id" type="hidden" name="product_id" value="" required/>
                    <input type="hidden" name="partner_id" value="<?= $partner_id ?>" />
                    <input type="hidden" name="by" value="<?= get_current_user_id() ?>" />

                    <div class="select-type-document" >
                        <div>
                            <label for="type"><?= __('Type', 'edusystem'); ?><span class="required">*</span></label>
                            
                            <select id="type-document" class="form-control" name="type_id" autocomplete="off" required >
                                <option value="" data-price='<?= wc_price(0, [ 'currency' => $type->currency ] ) ?>' class="text-uppercase"><?= __('Select an option', 'edusystem'); ?></option>
                                <?php foreach ($types as $type) { ?>
                                    <option value="<?= $type->id ?>" data-product_id="<?= $type->product_id ?>" data-price='<?= wc_price( $type->price, [ 'currency' => $type->currency ] ) ?>' class="text-uppercase"><?= $type->type ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        
                        <div class="price-type" >
                            <label ><?= __('Price', 'edusystem'); ?></label>
                            <span id="price-document" ><?= wc_price(0) ?></span>
                        </div>

                    </div>

                    <div>

                        <label for="reason"><?= __('Reason', 'edusystem'); ?><span class="required">*</span></label>
                        
                        <textarea id="reason" class="formdata" name="reason" autocomplete="off" required ></textarea>
                    </div>

                    <?php if (!$student_id) { ?>

                        <div>
                            <label for="student_id"><?= __('Student', 'edusystem'); ?> <span style="font-size: 10px; font-style: italic;">(optional)</span></label>
                        
                            <select id="student-id" class="form-control" name="student_id" autocomplete="off" required>
                                <option value="" class="text-uppercase"><?= __('Select an option', 'edusystem'); ?></option>

                                <?php foreach ($students as $student) { ?>
                                    <option value="<?= $student->id ?>" class="text-uppercase"><?= $student->name ?> <?= $student->middle_name ?> <?= $student->last_name ?> <?= $student->middle_last_name ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    <?php } else { ?>

                        <input type="hidden" name="student_id" value="<?= $student_id ?>" />

                    <?php } ?>

                    <div class="total" >
                        <label ><?= __('Total', 'edusystem'); ?>:</label>
                        <span id="total-document" ><?= wc_price(0) ?></span>
                    </div>

                </div>

                <!-- DATOS DEL GRADO -->
                <div style="text-align:center; margin-top: 1.5rem; margin-bottom: 5px;">
                    <button type="submit" id="send-request" class="submit button-success" name="request-document" disabled ><?= __('Proceed to Checkout', 'edusystem'); ?></button>
                </div>

            </form>
        </div>
    </div>
</div>