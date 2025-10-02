<?php

// scripts de styles y scripts
add_action('admin_enqueue_scripts', 'PMF_plugin_scripts');
function PMF_plugin_scripts() {

    wp_enqueue_style('PMF_styles', SPM_URL . '/payment_method_fees/assets/css/styles.css');
  
}

// añadir pagina de comisiones
add_action('admin_menu', function() {
    add_menu_page(
        __('Payment Commissions','payment-method-fees'),
        __('Payment Commissions','payment-method-fees'),
        'manage_options',
        'payment-method-fees',
        'PMF_payment_method_fees_page'
    );
});

// pagina de fees de comisiones
function PMF_payment_method_fees_page() {

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commissions']) ) {

        update_option('payment_method_commissions', array_map('floatval', $_POST['commissions']) );

        ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?= __('Commissions saved successfully!','payment-method-fees'); ?></strong></p>
            </div>
        <?php
    }

    // obtine el listado de pasarelas de pago
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $commissions = get_option( 'payment_method_commissions', [] );

    ?>

        <div class="wrap PMF_page_commissions ">
            <h1 class="pmf-title"><?= __('Payment Method Fees','payment-method-fees'); ?></h1>

            <p class="pmf-description"><?= __('Set the commission percentage for each payment method. This fee will be automatically added to the order total during checkout.','payment-method-fees'); ?></p>

            <form method="post" class="pmf-form">

                <div class="commission-table-wrapper">
                    <table class="widefat striped commission-table">

                        <thead>
                            <tr>
                                <th><?= __('Payment Method & Commission','payment-method-fees'); ?></th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ( $gateways as $gateway_id => $gateway ) : ?>

                                <?php $value = isset($commissions[$gateway_id]) ? $commissions[$gateway_id] : 0 ?>
                            
                                <tr>
                                    <td>
                                        <div class='commission-row'>

                                            <div class='commission-info'>
                                                <strong><?= $gateway->title ?></strong><br>
                                                <small><?= $gateway->get_method_description() ?></small>
                                            </div>

                                            <div class='commission-input'>
                                                <label><?= __('Commission (%)','payment-method-fees'); ?></label>
                                                <input type='number' step='0.01' min='0' name='commissions[<?= $gateway_id ?>]' value='<?= $value ?>' />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>    
                        </tbody>
                    </table>
                </div>

                <p class="pmf-submit">
                    <input type="submit" class="button button-primary" value="<?= __('Save Commissions','payment-method-fees'); ?>" />
                </p>
            </form>
        </div>

    <?php
}

/* add_action('woocommerce_cart_calculate_fees', 'PMF_add_payment_method_fees');
function PMF_add_payment_method_fees(WC_Cart $cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    // Valor personalizado que quieres agregar
    $valor_extra = 15.00;

    // Nombre que aparecerá en el resumen de totales
    $nombre = 'Cargo por servicio';

    // Agrega el valor como una tarifa
    $cart->add_fee($nombre, $valor_extra, false);

    
} */

/* add_action('woocommerce_review_order_after_order_total', 'PMF_custom_message_after_total');
function PMF_custom_message_after_total() {
    // Obtener el método de pago seleccionado
    $chosen_gateway = WC()->session->get('chosen_payment_method');

    // Obtener las comisiones configuradas (como porcentaje, ej. 2.5 para 2.5%)
    $commissions = get_option('payment_method_commissions', []);
    $percentage = isset($commissions[$chosen_gateway]) ? floatval($commissions[$chosen_gateway]) : 0.00;

    // Obtener el subtotal del carrito (sin impuestos ni envío)
    $cart_subtotal = WC()->cart->get_subtotal();

    // Calcular el fee como porcentaje del subtotal
    $fee_amount = ($percentage > 0) ? ($cart_subtotal * ($percentage / 100)) : 0.00;

    // Mostrar solo si el fee es mayor a 0
    if ($fee_amount > 0) {
        echo '<tr class="payment-method-fee">';
        echo '<th>Comisión por método de pago (' . esc_html($percentage) . '%)</th>';
        echo '<td>' . wc_price($fee_amount) . '</td>';
        echo '</tr>';
    }
} */


// 
add_action( 'woocommerce_checkout_create_order', 'PMF_add_payment_method_fees', 10, 2 );
function PMF_add_payment_method_fees( $order, $data ) {

    $payment_method = $data['payment_method'];
    $commissions = get_option('payment_method_commissions', []);
    $percentage = isset($commissions[$payment_method]) ? floatval($commissions[$payment_method]) : 0;

    if ( $percentage > 0 ) {

        $fee = ( $order->get_subtotal() + $order->get_shipping_total() ) * ($percentage / 100);
        $gateway_title = WC()->payment_gateways->payment_gateways()[$payment_method]->get_title();

        $fee_item = new WC_Order_Item_Fee();
        $fee_item->set_name( __('Payment method fee','payment-method-fees')." ({$gateway_title})" );
        $fee_item->set_amount( $fee );
        $fee_item->set_total( $fee );
        $fee_item->add_meta_data( '_payment_method_fee', $fee, true );

        $order->add_item( $fee_item );
    }
}

/* add_action( 'woocommerce_checkout_order_processed', 'PMF_add_payment_method_fees', 1000, 3 );
function PMF_add_payment_method_fees( $order_id, $posted_data, $order ) {

    $payment_method = $order->get_payment_method();
    $commissions = get_option('payment_method_commissions', []);
    $percentage = isset($commissions[$payment_method]) ? floatval($commissions[$payment_method]) : 0;

    if ( $percentage > 0 ) {

        $fee = ( $order->get_subtotal() + $order->get_shipping_total() ) * ($percentage / 100);
        $gateway_title = WC()->payment_gateways->payment_gateways()[$payment_method]->get_title();

        $fee_item = new WC_Order_Item_Fee();
        $fee_item->set_name( __('Payment method fee','payment-method-fees')." ({$gateway_title})" );
        $fee_item->set_amount( $fee );
        $fee_item->set_total( $fee );
        $fee_item->add_meta_data( '_payment_method_fee', $fee, true );

        $order->add_item( $fee_item );

        $order->calculate_totals();
        $order->save();
    }
} */





