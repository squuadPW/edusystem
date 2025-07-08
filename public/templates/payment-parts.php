<div>    
    <?php
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();

        // excluye los productos de fee
        $fee_inscription = FEE_INSCRIPTION;
        $fee_graduation = FEE_GRADUATION;
        $filtered_products = array_filter($cart, function($product) use($FEE_INSCRIPTION, $fee_graduation) {
            return ( $product['product_id'] != $fee_inscription ) || ( $product['product_id'] != $fee_inscription );
        });

        // obtiene los cupones
        $cupones = $woocommerce->cart->get_coupons();

        foreach ($filtered_products as $key => $product) {

            if( $product['variation_id'] ) { 
                $product_id =$product['variation_id'];
            } else {
                $product_id = $product['product_id'];
            }
            
            $product = wc_get_product($product_id);
            break;
        }

    ?>
    
    <?php if ( $product && !isset($_COOKIE['is_scholarship']) ) : ?>


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

        <?php 
            $product_id = $product->get_id();
        ?>
        
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
                        ))
                ORDER BY position ASC",
                $product_id
            ));

        ?>
        
        <div>
            <div class="radio-group text-center">
                <label class="m-5"><?=__('Program Payments','edusystem')?></label>

                <div class="radio-group options-quotas">

                    <?php
                        $discount_value = 0;
                        // valida el precio del progrma con los cupones
                        if ( !empty($cupones) ) {

                            foreach ( $cupones as $codigo => $cupon ) {
                                if( $cupon->is_valid_for_product($product) && $cupon->get_discount_type() == 'percent' ) {
                                    $discount_value = $cupon->get_amount();
                                }
                            }
                        }

                    ?>

                    <input type="hidden" id="discount_value" value="<?= $discount_value ?? 0 ?>" />

                    <?php foreach ( $quotas_rules AS $rule ):?>

                        <div id="option-rule-<?=$rule->id?>" class="radio-input option-quota" data-id="<?= $rule->id ?>" >
                            
                            <input id="data-rule-<?= $rule->id ?>" class="form-check-input data-rule" type="radio" name="data_rule" value="<?= htmlspecialchars(json_encode($rule)) ?>">

                            <input class="form-check-input option-rule" type="radio" name="quota_rule" value="<?= $rule->id ?>">
                            
                            <label class="form-check-label" for="<?= $rule->name ?>" >
                                <?= $rule->name ?> 
                            </label>
                        </div>
                    <?php endforeach ?>

                </div>
            </div>
                
            <div id="table-payment" data-product_id="<?= $product_id ?>" data-text_table_headers="<?= htmlspecialchars(json_encode([__('Payment','edusystem'), __('Next date payment','edusystem'), __('Amount','edusystem')])) ?>" data-text_total="<?=__('Total','edusystem')?>"  > </div>
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

    /* // Obtener todas las propiedades del cupón
                                    $cupon_data = array(
                                        'monto' => $cupon->get_amount(),
                                        'tipo_descuento' => $cupon->get_discount_type(),
                                        'fecha_expiracion' => $cupon->get_date_expires() ? $cupon->get_date_expires()->date('Y-m-d H:i:s') : 'No expira',
                                        'uso_individual' => $cupon->get_individual_use(),
                                        'limite_uso' => $cupon->get_usage_limit(),
                                        'usos_actuales' => $cupon->get_usage_count(),
                                        'limite_por_usuario' => $cupon->get_usage_limit_per_user(),
                                        'productos_aplicables' => $cupon->get_product_ids(),
                                        'productos_excluidos' => $cupon->get_excluded_product_ids(),
                                        'categorias_aplicables' => $cupon->get_product_categories(),
                                        'categorias_excluidas' => $cupon->get_excluded_product_categories(),
                                        'importe_minimo' => $cupon->get_minimum_amount(),
                                        'importe_maximo' => $cupon->get_maximum_amount(),
                                        'correos_restrictivos' => $cupon->get_email_restrictions(),
                                        'excluir_ofertas' => $cupon->get_exclude_sale_items(),
                                        'restricciones' => array(
                                            'aplica_a_productos' => $cupon->is_valid_for_product($producto_a_validar, $valores),
                                            'aplica_a_carrito' => $cupon->is_valid_for_cart(),
                                        )
                                    ); */
                                    
                                   /*  echo "<pre>";
                                    var_dump($cupon_data);
                                    echo "</pre>"; */
?>
</div>   






