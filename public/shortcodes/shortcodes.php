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
    add_action( 'woocommerce_review_order_before_payment', function () {
        ?>
            <div id="quota-description-container" class="quota-descripcion" ></div>
        <?php
    }, 1 );

});

// Shortcode para mostrar llama la seccion de comprar materias reprobadas
/* add_shortcode('buy_failed_subjects', function () {

    $student_id = get_user_meta( get_current_user_id(),'student_id',true );
    if( !$student_id ) return;

    global $wpdb;
    $student_inscriptions = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM `{$wpdb->prefix}student_period_inscriptions` 
        WHERE student_id=%d", 
        $student_id
    ));

    if( !$student_inscriptions ) return;

    $subject_ids = array_unique(wp_list_pluck($student_inscriptions, 'subject_id'));
    $placeholders = implode(',', array_fill(0, count($subject_ids), '%d'));

    $resultados = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}school_subjects WHERE id IN ($placeholders)",
        $subject_ids
    ));

    var_dump($resultados);
    
    ?>

        <div id="buy-failed-subjects-container" class="seccion-dashboard">

            <div class="seccion-dashboard-header">
                <h4><?= __('Buy failed subjects', 'edusystem') ?></h4>
            </div>
            
            <div id="failed-subjects-content" >
                <p><?= __('No failed subjects available.', 'edusystem') ?></p>
            </div>

        </div>

    <?php
}); */

/* add_shortcode('buy_failed_subjects', function () {

    $current_user_id = get_current_user_id();
    if ( !$current_user_id ) return;

    $student_id = get_user_meta($current_user_id, 'student_id', true);
    if (!$student_id) return;

    global $wpdb;
    $table_inscriptions = "{$wpdb->prefix}student_period_inscriptions";
    $table_subjects = "{$wpdb->prefix}school_subjects";

    // Busca materias reprobadas que NO tengan un registro de aprobado
    $failed_inscriptions = $wpdb->get_results($wpdb->prepare(
        "SELECT DISTINCT subject_id 
         FROM $table_inscriptions t1
         WHERE student_id = %d 
            AND status_id = 4
            AND NOT EXISTS (
                SELECT 1 FROM $table_inscriptions t2 
                WHERE t2.student_id = t1.student_id 
                AND t2.subject_id = t1.subject_id 
                AND t2.status_id = 3
             )", 
        $student_id
    ));

    if ( !$failed_inscriptions ) return;

    $subject_ids = wp_list_pluck($failed_inscriptions, 'subject_id');
    $placeholders = implode(',', array_fill(0, count($subject_ids), '%d'));

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_subjects WHERE id IN ($placeholders)",
        $subject_ids
    ));

    ob_start(); 
    ?>
        <div id="buy-failed-subjects-container" class="seccion-dashboard">

            <div class="seccion-dashboard-header">
                <h4><?= __('Buy failed subjects', 'edusystem'); ?></h4>
            </div>
            
            <div>
                <?php if($results): ?>
                    <ul class="list-failed-subjects">
                        <?php foreach ($results as $material): ?>
                            <li style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span><?= esc_html($material->name); ?></span>
                                <button class="button button-small"><?= __('Pay Remedial', 'edusystem'); ?></button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    <?php
    return ob_get_clean();
    
}); */

add_shortcode('buy_failed_subjects', function () {

    $current_user_id = get_current_user_id();
    if (!$current_user_id) return;

    $student_id = get_user_meta($current_user_id, 'student_id', true);
    if (!$student_id) return;

    global $wpdb;
    $table_inscriptions = "{$wpdb->prefix}student_period_inscriptions";
    $table_subjects = "{$wpdb->prefix}school_subjects";

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT `sub`.id, `sub`.name, `sub`.price, `sub`.currency, `sub`.retake_limit, COUNT(`ins`.id) as total_reprobadas 
        FROM wp_student_period_inscriptions `ins`
        INNER JOIN wp_school_subjects `sub` ON `ins`.subject_id = `sub`.id 
        WHERE `ins`.student_id = %d AND `ins`.status_id = 4 AND 
            NOT EXISTS ( 
                SELECT 1 FROM wp_student_period_inscriptions `t2`
                WHERE `t2`.student_id = `ins`.student_id AND `t2`.subject_id = `ins`.subject_id AND `t2`.status_id = 3 
            ) 
        GROUP BY `ins`.subject_id
        HAVING total_reprobadas >= COALESCE(`sub`.retake_limit, 0);", 
        $student_id
    ));

    if ( !$results )  return; 

    ob_start(); 
    ?>
        <div id="buy-failed-subjects-container" class="seccion-dashboard">

            <div class="seccion-dashboard-header">
                <h4><?= __('Buy failed subjects', 'edusystem'); ?></h4>
            </div>
            
            <div>
                <ul class="list-failed-subjects">
                    <?php foreach ($results as $material): ?>
                        <li>
                            <span><?= esc_html($material->name); ?></span>
                            <button class="button button-primary button-small"><?= __('Pay Remedial', 'edusystem'); ?></button>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </div>
    <?php
    return ob_get_clean();
});





    