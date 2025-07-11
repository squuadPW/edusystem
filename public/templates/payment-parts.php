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

        <?php if( !isset($_COOKIE['from_webinar']) && empty($_COOKIE['from_webinar']) ) : ?>
            
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
        
        <?php if( $quotas_rules ): ?>
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
        <?php endif ?>
        
        <input type="hidden" name="submit" value="Apply Scholarship">
    <?php endif; ?>

<?php 

    $order_id = 676;
    $order = wc_get_order( $order_id );
    /* global $wpdb;
    $table_student_payment = $wpdb->prefix . 'student_payments';
    $student_id = $order->get_meta('student_id');

    if (empty($student_id)) {
        return; // Salir si no hay ID de estudiante, ya que es un dato crítico.
    } */

    /* $student_data = get_student_detail($student_id);

    // Validar si $student_data existe y tiene un institute_id
    if (!$student_data || !isset($student_data->institute_id) || empty($student_data->institute_id)) {
        $institute_id = null; // No hay un institute_id válido
        $institute = null;
        $alliances = []; // Array de alianzas vacío
    } else {
        $institute_id = $student_data->institute_id;
        $institute = get_institute_details($institute_id);

        // Si el instituto no existe, el fee será 0 y las alianzas vacías, pero no salimos.
        if (!$institute) {
            $alliances = [];
        } else {
            // Obtener las alianzas del instituto si el instituto existe.
            $alliances = get_alliances_from_institute($institute_id);
            // Si get_alliances_from_institute devuelve null o no es un array, se inicializa como vacío.
            if (!is_array($alliances)) {
                $alliances = [];
            }
        }
    }

    $is_scholarship = (bool) $order->get_meta('is_scholarship'); // Obtener el meta para la beca. */

    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        var_dump($item->get_meta('quota_rule_id'));
/* 
        if ( !$product ) continue;

        // obtiene el id del producto tanto si es un poroducto variable o un producto
        $product_id = $item->get_variation_id() ?? $item->get_product_id();

        $variation_id = 0;

        // Determinar si este producto es un FEE de inscripción o graduación.
        // Asegúrate de que FEE_INSCRIPTION y FEE_GRADUATION estén definidos como constantes.
        $is_fee_product = in_array( $product_id, [FEE_INSCRIPTION, FEE_GRADUATION] );

        // Evita la redundancia procesando solo si no existe un registro previo para este producto en esta orden.
        $existing_record_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_student_payment} WHERE student_id = %d AND product_id = %d",
            $student_id,
            $product_id
        ));
        
        // salta el producto si encuentra un registro previo
        if ($existing_record_count > 0) continue;

        $installments = 1;
        $interval = '';
        $attribute_value = '';

        if ( $product->is_type('variation') ) {

            $variation_attributes = $product->get_variation_attributes();
            foreach ($variation_attributes as $attribute_taxonomy => $term_slug) {
                $taxonomy = str_replace('attribute_', '', $attribute_taxonomy);
                if (taxonomy_exists($taxonomy)) {
                    $term = get_term_by('slug', $term_slug, $taxonomy);
                    if ($term) {
                        $attribute_value = $term->name;
                    }
                } else {
                    $attribute_value = $term_slug;
                }
            }

            switch ($attribute_value) {
                case 'Annual':
                    $interval = '+1 year';
                    break;
                case 'Semiannual':
                    $interval = '+6 months';
                    break;
            }
            $installments = (int) ($product->get_meta('num_cuotes_text') ?: 1);
        }

        // --- Cálculos de precios ---
        $original_price = (float) ($item->get_subtotal() / $item->get_quantity());
        $price_per_installment = (float) ($item->get_total() / $item->get_quantity());
        $total_amount_to_pay = $price_per_installment * $installments;
        $total_original_amount = $original_price * $installments;
        $total_discount_amount = $total_original_amount - $total_amount_to_pay;

        // --- Recalcular tarifas de alianzas para este producto específico ---
        $current_item_alliances_fees = [];
        if (!empty($alliances) && is_array($alliances)) {
            foreach ($alliances as $alliance) {
                $alliance_id = $alliance->id ?? null;
                $alliance_data = ($alliance_id) ? get_alliance_detail($alliance_id) : null;
                $alliance_fee_percentage = (float) ($alliance->fee ?? ($alliance_data->fee ?? 0));

                $total_alliance_fee = 0.0;
                if (!$is_fee_product && !$is_scholarship) {
                    $total_alliance_fee = ($alliance_fee_percentage * (float) $item->get_total()) / 100;
                }

                if ($alliance_id) {
                    $current_item_alliances_fees[] = [
                        'id' => $alliance_id,
                        'fee_percentage' => $alliance_fee_percentage,
                        'calculated_fee_amount' => $total_alliance_fee,
                    ];
                }
            }
        }

        $current_item_alliances_json = json_encode($current_item_alliances_fees);
        if ($current_item_alliances_json === false) {
            $current_item_alliances_json = json_encode([]);
        }

        // --- Lógica de fechas ---
        $needs_next_payment = !$is_fee_product;
        $start_date = new DateTime();

        for ($i = 0; $i < $installments; $i++) {
            $next_payment_date = null;
            if ($needs_next_payment) {
                $payment_date_obj = clone $start_date;

                if ($i > 0 && !empty($interval)) {
                    if (preg_match('/^\+(\d+)\s*(year|month)s?$/i', $interval, $matches)) {
                        $value = (int) $matches[1];
                        $unit = $matches[2];
                        $total_offset = $i * $value;
                        $payment_date_obj->modify("+$total_offset $unit");
                    }
                }
                $next_payment_date = $payment_date_obj->format('Y-m-d');
            }

            // Calcular el institute_fee para este item específico
            $current_item_institute_fee = 0.0;
            // Solo se calcula la tarifa del instituto si el instituto existe y no es un producto FEE o beca.
            if ($institute && !$is_fee_product && !$is_scholarship) {
                $institute_fee_percentage = (float) ($institute->fee ?? 0);
                $current_item_institute_fee = ($institute_fee_percentage * (float) $item->get_total()) / 100;
            }

            $data = [
                'status_id' => 0,
                'order_id' => ($i + 1) == 1 ? $order_id : null,
                'student_id' => $student_id,
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'institute_id' => ($i + 1) == 1 ? $institute_id : null,
                'institute_fee' => ($i + 1) == 1 ? $current_item_institute_fee : 0,
                'alliances' => ($i + 1) == 1 ? $current_item_alliances_json : null,
                'amount' => $price_per_installment,
                'original_amount_product' => $original_price,
                'total_amount' => $total_amount_to_pay,
                'original_amount' => $total_original_amount,
                'discount_amount' => $total_discount_amount,
                'type_payment' => $installments > 1 ? 1 : 2,
                'cuote' => ($i + 1),
                'num_cuotes' => $installments,
                'date_payment' => $i == 0 ? $start_date->format('Y-m-d') : null,
                'date_next_payment' => $next_payment_date,
            ];

            echo "<pre>";
            var_dump( $data );
            echo "</pre>"; 
            // $wpdb->insert($table_student_payment, $data);
        }*/

    }

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






