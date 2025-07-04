<div>    
    <?php
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        $id = FEE_INSCRIPTION;

        $filtered_products = array_filter($cart, function($product) use($id) {
            return $product['product_id'] != $id;
        });

        foreach ($filtered_products as $key => $product) {

            if( isset( $product['variation_id'] ) ) { 
                $product_id =$product['variation_id'];
            } else {
                $product_id = $product['product_id'];
            }
            $product = wc_get_product($product_id);
        }

        // Check if the product is a variable product
    ?>
    
    <?php if ( isset($product) && !isset($_COOKIE['is_scholarship']) ) : ?>


        <!-- <div>
        <div class="back-select-payment">
            <a href="<?= the_permalink() . '?action=change_payment_method&time='.date('H:i:s'); ?>"><span class='dashicons dashicons-arrow-left-alt dashiconaes'></span><?= __('Change payment method', 'edusystem'); ?></a>
        </div>
        </div> -->

        <?php if(!isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar'])) : ?>
            
            <div >
                <div style="margin-bottom: 10px !important; text-align: center">
                    <?php
                        $product_fee = wc_get_product(FEE_INSCRIPTION);
                        $product_price = $product_fee->get_price();
                    ?>
                    <label class="fee-container">
                        <strong><?=__('Registration fee','edusystem')?></strong> 
                        <br>
                        <span><?=__('(You can pay it before starting classes in your account)','edusystem')?></span>
                        
                        <input name="fee" type="checkbox" checked="checked">
                        <span class="checkmark"></span>
                    </label>
                </div>
            </div>
        <?php endif ?>

        <div class="text-center" style="padding: 18px 0px;">
            <label><?=__('Apply to get the discount','edusystem')?></label>
            <div id="button-schoolship"></div>
        </div>

        <?php $product_id = $product->get_id(); ?>
        
        <?php
            global $wpdb;
            $quotas_rules = $wpdb->get_results($wpdb->prepare(
               "SELECT `qr`.*
                FROM `{$wpdb->prefix}quota_rules` AS `qr`
                INNER JOIN `{$wpdb->prefix}programs` AS `p` 
                    ON (`qr`.program_id = `p`.identificator AND `p`.product_id = %1\$d) 
                    OR `qr`.program_id = CONCAT(`p`.identificator, '_', 
                        REGEXP_SUBSTR( JSON_UNQUOTE(JSON_SEARCH(`p`.subprogram, 'one', %1\$d, NULL, '$.*.product_id')),
                            '[0-9]+'
                        ))",
                $product_id
            ));

        ?>
        
        <div>
            <div class="radio-group text-center">
                <label class="m-5"><?=__('Program Payments','edusystem')?></label>

                <div class="radio-group options-quotas">

                    <?php foreach ( $quotas_rules AS $rule ):?>
                        <div class="radio-input option-quota" data-id="<?= $rule->id ?>" >
                            
                            <input id="data-rule-<?= $rule->id ?>" class="form-check-input" type="radio" name="data_rule" value="<?= htmlspecialchars(json_encode($rule)) ?>">

                            <input class="form-check-input" type="radio"  name="option" value="<?= $rule->id ?>">
                            
                            <label class="form-check-label" for="<?= $rule->name ?>" >
                                <?= $rule->name ?> 
                            </label>
                        </div>
                    <?php endforeach ?>

                </div>
            </div>
                
            <div id="table-payment" data-text_table_headers="<?= htmlspecialchars(json_encode([__('Payment','edusystem'), __('Next date payment','edusystem'), __('Amount','edusystem')])) ?>" data-text_total="<?=__('Total','edusystem')?>"  > </div>
        </div>
        
        <input type="hidden" name="submit" value="Apply Scholarship">
    <?php endif; ?>

<?php 
    // Registrar el shortcode
    add_shortcode('payment_table_product_by_quotas', 'payment_table_product_by_quotas');
    function payment_table_product_by_quotas($atts) {

        $atts = shortcode_atts(['product_id' => 0], $atts);
        $product_id = (int)$atts['product_id'];

        echo "hola";
                
    }
?>
</div>   






