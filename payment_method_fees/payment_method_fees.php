<?php

// scripts de styles y scripts
add_action('admin_enqueue_scripts', 'PMF_plugin_scripts');
function PMF_plugin_scripts() {

    wp_enqueue_style('PMF_styles', plugin_dir_url(__FILE__) . '/assets/css/styles.css');
  
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

        $payment_method_commissions = [];

        foreach ( $_POST['commissions'] as $method => $data ) {
            $type = isset($data['type']) ? $data['type'] : 'percentage';
            $value = isset($data['value']) ? floatval($data['value']) : 0;

            $payment_method_commissions[$method] = [
                'type'  => $type,
                'value' => $value
            ];
        }

        update_option('payment_method_commissions', $payment_method_commissions );

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

                                <?php 
                                    
                                    $type = 'percentage';
                                    $value = 0;
                                    if ( isset( $commissions[$gateway_id] ) ) {
                                        $type = $commissions[$gateway_id]['type'];
                                        $value = $commissions[$gateway_id]['value'];
                                    }
                                ?>
                            
                                <tr>
                                    <td>
                                        <div class='commission-row'>

                                            <div class='commission-info'>
                                                <strong><?= $gateway->title ?></strong><br>
                                                <small><?= $gateway->get_method_description() ?></small>
                                            </div>

                                            <div class='commission-input'>
                                                <label><?= __('Commission Type','payment-method-fees'); ?></label>
                                                <select name='commissions[<?= $gateway_id ?>][type]'>
                                                    <option value='percentage' <?= $type === 'percentage' ? 'selected' : '' ?>><?= __('Percentage','payment-method-fees'); ?></option>
                                                    <option value='fixed' <?= $type === 'fixed' ? 'selected' : '' ?>><?= __('Fixed Amount','payment-method-fees'); ?></option>
                                                </select>
                                            </div>

                                            <div class='commission-input'>
                                                <label><?= __('Commission','payment-method-fees'); ?></label>
                                                <input type='number' step='0.01' min='0' name='commissions[<?= $gateway_id ?>][value]' value='<?= $value ?>' />
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

// añadir fee al carrito
add_action( 'woocommerce_cart_calculate_fees', 'PMF_add_payment_method_fee_to_cart', 20, 1 );
function PMF_add_payment_method_fee_to_cart( $cart = null ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    if( $cart === null ) $cart = WC()->cart;

    // Elimina solo el fee del método de pago anterior
    PMF_remove_payment_method_fee_from_cart( $cart );

    // si no esta en el checkout no hace nada
    if ( ! is_checkout() || ( isset( $_COOKIE['PMF_payment_fee_disabled'] ) && $_COOKIE['PMF_payment_fee_disabled'] == true ) ) return;
    
    $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
    $commissions = get_option( 'payment_method_commissions', [] );
    $data = isset( $commissions[ $chosen_payment_method ] ) ? $commissions[ $chosen_payment_method ] : null;

    if ( $data && $cart->get_total() > 0 ) {

        $gateway = WC()->payment_gateways->payment_gateways()[ $chosen_payment_method ];

        // obtiene el titulo del fee actual
        $gateway_title = $gateway ? $gateway->get_title() : ucfirst( $chosen_payment_method );
        // $fee_name = __( 'Payment method fee', 'payment-method-fees' ) . " ({$gateway_title})";
        $fee_name = $gateway_title." ".__( 'Fee', 'payment-method-fees' );

        $type = $data['type'];
        $value = floatval($data['value']);

        $subtotal = $cart->get_subtotal();
        $shipping_total = $cart->get_shipping_total();

        $fee_amount = $type === 'percentage'
            ? ( $subtotal + $shipping_total ) * ( $value / 100 )
            : $value;

        if( $fee_amount > 0 ){
            
            $cart->add_fee( $fee_name, $fee_amount, false );

            WC()->session->set( 'pmf_fee_data', [
                'method'     => $chosen_payment_method,
                'type'       => $type,
                'value'      => $value,
                'amount'     => $fee_amount,
                'title'      => $fee_name
            ] );
        }
    }
}

// remover fee del carrito
function PMF_remove_payment_method_fee_from_cart( $cart = null ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    if( $cart === null ) $cart = WC()->cart;

    $pmf_fee_data = WC()->session->get( 'pmf_fee_data' );
    if ( $pmf_fee_data ) {
        foreach ( $cart->fees as $key => $fee ) {
            // Verifica si el nombre del fee coincide con el almacenado en la sesión
            if ( $fee->name === $pmf_fee_data['title'] ) {
                unset( $cart->fees[ $key ] );
            }
        }
        // elimina el meta
        WC()->session->__unset( 'pmf_fee_data' );
    }
}

// añadir meta al item fee en la orden
add_action( 'woocommerce_checkout_create_order_fee_item', 'PMF_add_meta_to_fee_item', 10, 3 );
function PMF_add_meta_to_fee_item( $item, $fee_key, $order ) {
    $fee_data = WC()->session->get( 'pmf_fee_data' );

    if ( empty( $fee_data ) ) return;

    // Verifica que este fee sea el del método de pago
    if ( $item->get_name() === $fee_data['title'] ) {
        $item->add_meta_data( '_payment_method_fee', $fee_data['amount'], true );
    }
}

// trae el fee del metodo de pago
add_action('wp_ajax_nopriv_PMF_payment_method_fee', 'PMF_payment_method_fee');
add_action('wp_ajax_PMF_payment_method_fee', 'PMF_payment_method_fee');
function PMF_payment_method_fee() {

    $chosen_gateway = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';
    
    // Obtiene las comisiones configuradas
    $commissions = get_option('payment_method_commissions', []);

    // Verifica si existe configuración para el método seleccionado
    $fee_data = isset($commissions[$chosen_gateway]) ? $commissions[$chosen_gateway] : ['type' => 'percentage', 'value' => 0.00];

    // Normaliza el valor
    $fee_value = floatval($fee_data['value']);
    $fee_type  = $fee_data['type'];

    wp_send_json(array(
        'payment_methods' => $chosen_gateway,
        'type' => $fee_type,
        'value' => $fee_value,
    ));
}

// actualiza el fee de metodo de pago en la orden por el total de la orden
add_action('PMF_update_payment_method_fee', 'PMF_update_payment_method_fee', 10, 1);
function PMF_update_payment_method_fee( $order) {

    $payment_method = $order->get_payment_method();

    // Obtiene el nombre del fee actual
    $gateway = WC()->payment_gateways->payment_gateways()[ $payment_method ] ?? null;
    $gateway_title = $gateway ? $gateway->get_title() : ucfirst( $payment_method );
    $fee_name = $gateway_title . ' ' . __( 'Fee', 'payment-method-fees' );

    $commissions = get_option( 'payment_method_commissions', [] );
    $data = $commissions[ $payment_method ] ?? null;

    if ( ! $data ) return;

    $type = $data['type'];
    $value = floatval( $data['value'] );

    $total = $order->get_total();

    $fee_amount = $type === 'percentage'
        ? ( $total ) * ( $value / 100 )
        : $value;

    // Elimina o actualiza el fee si ya existe
    $fee_found = false;
    foreach ( $order->get_items('fee') as $fee_item_id => $fee_item ) {
        if ( $fee_item->get_name() === $fee_name ) {
            if ( $fee_amount <= 0 ) {
                $order->remove_item( $fee_item_id );
            } else {
                $fee_item->set_amount( $fee_amount );
                $fee_item->set_total( $fee_amount );
                $fee_item->update_meta_data( '_payment_method_fee', $fee_amount );
                $order->add_item( $fee_item ); // Asegura que se re-agregue
            }
            $fee_found = true;
            break;
        }
    }

    // Si no se encontró el fee, lo crea
    if ( !$fee_found && $fee_amount > 0 ) {

        $item = new WC_Order_Item_Fee();
        $item->set_name( $fee_name );
        $item->set_amount( $fee_amount );
        $item->set_total( $fee_amount );
        $item->add_meta_data( '_payment_method_fee', $fee_amount, true );
        $order->add_item( $item );
    }

    // Recalcula totales y guarda
    $order->calculate_totals();
    $order->save();
}


//
/* add_action( 'woocommerce_checkout_create_order', 'PMF_add_payment_method_fees', 10, 2 );
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
} */

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

