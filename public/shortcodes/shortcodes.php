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
        
        // obtiene la moneda
        $config = get_option( 'dynamic_currency_edusystem_config', array( 'enabled' => true ) );
        $enabled = !empty( $config['enabled'] ?? false );
        $currency = ( $enabled ) ? $quotas_rules[0]->currency : get_woocommerce_currency();

        // obtiene el lenguaje 
        $language = explode( '_', get_locale() )[0]; 
        
        ?>
            <div id="payment-cuotes" >

                <div id="container-disable" ></div>

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

                            <?php $rule->advanced_rules = get_advanced_quota_rules( $rule->id ); ?>

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
                        
                <div id="table-payment" data-product_id="<?= $product_id ?>" 
                    data-currency="<?= $currency ?>" data-language="<?= $language ?>" data-symbol="<?= get_woocommerce_currency_symbol($currency) ?>"
                    data-text_table_headers="<?= htmlspecialchars(json_encode([__('Payment', 'edusystem'), __('Next date payment', 'edusystem'), __('Amount', 'edusystem')])) ?>"
                    data-text_total="<?= __('Total', 'edusystem') ?>">
                </div>
                    
            </div>
        <?php
    }

    // Agregar una sección personalizada antes de la parte de pago
    add_action( 'woocommerce_checkout_before_order_review', function () {
        ?>
            <div id="quota-description-container" class="quota-descripcion" ></div>
        <?php
    }, 1 );

});

add_shortcode('buy_failed_subjects', function () {

    $current_user_id = get_current_user_id();
    if (!$current_user_id) return;

    $students = get_students_detail_partner($current_user_id);
    if (!$students) return;

    foreach ( $students as $student ) {
        
        $student_id = $student->id;

        global $wpdb;
        $table_inscriptions = "{$wpdb->prefix}student_period_inscriptions";
        $table_subjects = "{$wpdb->prefix}school_subjects";

        $subjects_failed = $wpdb->get_results($wpdb->prepare(
            "SELECT `sub`.id, `sub`.name, `sub`.code_subject, `sub`.price, `sub`.currency, COALESCE(`sub`.retake_limit, 0) AS retake_limit, COUNT(`ins`.id) as total_reprobadas
            FROM {$table_inscriptions} `ins`
            INNER JOIN {$table_subjects} `sub` ON `sub`.id = `ins`.subject_id
            WHERE `ins`.student_id = %d AND (`ins`.status_id = 3 OR `ins`.status_id = 4)
            GROUP BY `sub`.id
            HAVING total_reprobadas >= retake_limit AND SUM(`ins`.status_id = 3) = 0;", 
            $student_id
        ));

        if ( !$subjects_failed )  return; 

        $load = load_current_cut_enrollment();
        $code = $load['code'];
        $cut = $load['cut'];

        $subjects_remedial = [];
        foreach ( $subjects_failed as $subject ) {
            
            // verifica que la materia tiene ofertas actuales
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if (!$offer_available_to_enroll) continue;
            
            array_push($subjects_remedial, $subject);
        }
        
        if ( !$subjects_remedial )  return; 

        ob_start(); 
        ?>
            <div id="buy-failed-subjects-container" class="seccion-dashboard">

                <div class="seccion-dashboard-header">
                    <h4><?= sprintf( __('Buy failed subjects by %s\'s', 'edusystem'), "{$student->name} {$student->last_name}" );?></h4>
                </div>
                
                <div>
                    <ul class="list-failed-subjects">
                        <?php foreach ($subjects_remedial as $subject): ?>
                            <li>
                                <span><?= esc_html($subject->name); ?></span>

                                <?php 
                                    $add_to_cart_url = add_query_arg( 
                                        array( 
                                            'add-to-cart' => get_master_subject_product_id(), 
                                            'subject_id' => $subject->id 
                                        ), 
                                        wc_get_checkout_url()
                                    );

                                ?>
                                <a href="<?= esc_url($add_to_cart_url); ?>" class="button button-primary button-small"><?= __('Pay Remedial', 'edusystem'); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        <?php
        return ob_get_clean();
    }
    
});





    