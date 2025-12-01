<?php 

// Muestra las cuotas disponibles para un programa y carga el carrito en caso de seleccionar una
add_shortcode('PROGRAM-QUOTAS', function () {
    
    global $woocommerce;
    $cart = $woocommerce->cart;

    // excluye los productos de fee
    $separate_program_fee = $_COOKIE['separate_program_fee'] ?? false;
    $fees = $_COOKIE['separate_program_fee'] ? json_decode($_COOKIE['separate_program_fee']) : [];

    // obtiene los cupones
    $cupones = $woocommerce->cart->get_coupons();

    $product_id = null;
    $program_data = [];
    $cart_item_key_programa = null;
    foreach( $cart->get_cart() as $cart_item_key => $item ) {

        $is_program = has_term('programs', 'product_cat', (int) $item['product_id']);
        if ( $is_program ) {

            $product_id = $item['product_id'];
            $variation_id = $item['variation_id'];
                
            $coupons = [];
            if (!empty($cupones)) {
                $product = wc_get_product( $variation_id ?? $product_id);
                if($product){
                    foreach ($cupones as $codigo => $cupon) {
                        if ($cupon->is_valid_for_product($product) && $cupon->get_discount_type() == 'percent') {
                            $coupons[] = $cupon->get_id();
                        }
                    }
                }
            }

            $program_data = [
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'rule_id' => 0,
                'coupons' => $coupons,
            ];

            $cart_item_key_programa = $cart_item_key;

            $product_id = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];

        } else if( in_array($item['product_id'], [$fees]) ) {

            if( isset($item['program_data'])  ){

                $program_data = $item['program_data'];
                $product_id = (int) $program_data['variation_id'] ? $program_data['variation_id'] : $program_data['product_id'];
                        
            } else if( !empty($program_data) && $separate_program_fee ) {

                $cart->cart_contents[$cart_item_key]['program_data'] = $program_data;

                // Actualizar el carrito
                $cart->set_session();

                $cart->remove_cart_item($cart_item_key_programa);
            }
        }

    }
        
    $product = wc_get_product($product_id);
    if ( !$product ) return;

    global $wpdb;
    $quotas_rules = $wpdb->get_results($wpdb->prepare(
        "SELECT `qr`.*, `p`.currency as currency
        FROM `{$wpdb->prefix}quota_rules` AS `qr`
        INNER JOIN `{$wpdb->prefix}programs` AS `p` 
        ON (`qr`.program_id = `p`.identificator AND `p`.product_id = %1\$d) 
        OR `qr`.program_id = CONCAT(`p`.identificator, '_', REGEXP_SUBSTR( 
                JSON_UNQUOTE(JSON_SEARCH(`p`.subprogram, 'one', %1\$d, NULL, '$.*.product_id')), 
            '[0-9]+' ))
        ORDER BY position ASC",
        $product_id
    ));

    if ($quotas_rules) {
        
        $currency = $quotas_rules[0]->currency ?? get_woocommerce_currency();

        ?>
            <div>
                <div class="radio-group text-center elements-quote-hidden">
                    <label class="m-5"><?= __('Program Payments', 'edusystem') ?></label>

                    <div class="radio-group options-quotas">

                        <?php
                            $discount_value = 0;
                            // valida el precio del programa con los cupones
                            if (!empty($cupones)) {

                                foreach ($cupones as $codigo => $cupon) {
                                    if ($cupon->is_valid_for_product($product) && $cupon->get_discount_type() == 'percent') {
                                        $discount_value = $cupon->get_amount();
                                    }
                                }
                            }

                        ?>

                        <input type="hidden" id="discount_value" value="<?= $discount_value ?? 0 ?>" />

                        <?php foreach ($quotas_rules as $rule): ?>

                            <div id="option-rule-<?= $rule->id ?>" class="radio-input option-quota" data-id="<?= $rule->id ?>">

                                <input id="data-rule-<?= $rule->id ?>" class="form-check-input data-rule" type="radio"
                                    name="data_rule" value="<?= htmlspecialchars(json_encode($rule)) ?>">

                                <input class="form-check-input option-rule" type="radio" name="quota_rule" value="<?= $rule->id ?>">

                                <label class="form-check-label" for="<?= $rule->name ?>">
                                    <?= $rule->name ?>
                                </label>
                            </div>
                        <?php endforeach ?>

                    </div>
                </div>

                <div id="table-payment" data-product_id="<?= $product_id ?>" data-currency="<?= $currency ?>"
                    data-text_table_headers="<?= htmlspecialchars(json_encode([__('Payment', 'edusystem'), __('Next date payment', 'edusystem'), __('Amount', 'edusystem')])) ?>"
                    data-text_total="<?= __('Total', 'edusystem') ?>">
                </div>
                    
            </div>
        <?php
    }
});

    