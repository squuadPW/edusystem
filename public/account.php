<?php

/**
 * Maneja el pago de la cuota de inscripción.
 *
 * Esta función procesa la solicitud para el pago de la cuota de inscripción,
 * establece una cookie con el ID del estudiante, añade el producto de inscripción
 * al carrito de WooCommerce y aplica un cupón si hay ofertas activas.
 * Finalmente, redirige al usuario a la página de checkout.
 */
function fee_inscription_payment() {
    // 1. Verificación de acción y seguridad
    // Usamos 'admin_init' para acciones que manipulan datos o redirigen
    // y 'wp_loaded' para funciones que se ejecutan después de cargar WordPress completamente.
    // Aquí 'wp_loaded' es adecuado si no hay hooks de WP que necesiten ser ejecutados antes.
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'fee_inscription_payment' ) {
        return; // Salir si la acción no coincide
    }

    // Asegurarse de que el ID del estudiante esté presente y sea válido
    if ( ! isset( $_GET['fee_student_id'] ) || empty( $_GET['fee_student_id'] ) ) {
        // Redirigir a una página de error o inicio si el ID del estudiante no es válido
        // o manejar el error de otra forma, por ejemplo, mostrando un mensaje.
        wp_redirect( home_url() ); // O una URL más específica
        exit;
    }

    // 2. Saneamiento y validación de datos
    // Filtrar el ID del estudiante para prevenir inyecciones SQL y otros ataques.
    $fee_student_id = absint( $_GET['fee_student_id'] ); // Asegura que sea un entero positivo

    if ( $fee_student_id <= 0 ) {
        // Manejo de error si el ID no es válido después de absint
        wp_redirect( home_url() );
        exit;
    }

    // 3. Establecer la cookie de forma segura
    // Usar 'COOKIEPATH' para la ruta y 'COOKIE_DOMAIN' para el dominio para mayor compatibilidad.
    setcookie( 'fee_student_id', $fee_student_id, time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    // 'is_ssl()' para 'secure' y 'true' para 'httponly' para mayor seguridad.

    // 4. Acceso a las variables globales de forma segura
    global $woocommerce, $wpdb;

    // Verificar si WooCommerce está activo
    if ( ! class_exists( 'WooCommerce' ) || ! isset( $woocommerce->cart ) ) {
        // Considerar redirigir o mostrar un error al usuario.
        wp_redirect( home_url() );
        exit;
    }

    // 5. Definición de constantes (si no están ya definidas globalmente)
    // Asumo que FEE_INSCRIPTION y FEE_GRADUATION son constantes definidas en otro lugar.
    // Si no lo están, podrías definirlas aquí o pasarlas como argumentos si es posible.
    $fee_inscription  = defined( 'FEE_INSCRIPTION' ) ? FEE_INSCRIPTION : 0; // Asegura un valor por defecto
    $fee_graduation   = defined( 'FEE_GRADUATION' ) ? FEE_GRADUATION : 0;   // Asegura un valor por defecto

    // Si las constantes no tienen un valor válido, salimos.
    if ( $fee_inscription <= 0 || $fee_graduation <= 0 ) {
        wp_redirect( home_url() );
        exit;
    }

    // 6. Manipulación del carrito de WooCommerce
    $woocommerce->cart->empty_cart();
    $woocommerce->cart->add_to_cart( $fee_inscription, 1 );

    // 7. Consulta a la base de datos de forma segura
    $table_student_payments = $wpdb->prefix . 'student_payments';

    // Usar prepare para consultas SQL seguras (evita inyección SQL)
    $payment = $wpdb->get_row( $wpdb->prepare(
        "SELECT type_payment FROM {$table_student_payments} WHERE student_id = %d AND product_id NOT IN (%d, %d)",
        $fee_student_id,
        $fee_inscription,
        $fee_graduation
    ) );

    // Verificar si se encontró un resultado y acceder a 'type_payment' de forma segura
    $type = isset( $payment->type_payment ) ? (int) $payment->type_payment : 0;

    // 8. Lógica de aplicación de cupones con refactorización
    $max_date_timestamp = (int) get_option( 'max_date_offer' ); // Asegura que sea un entero
    $current_timestamp  = current_time( 'timestamp' );

    $offer_coupon = '';

    if ( $type === 1 ) { // Cuotas
        $offer_coupon = get_option( 'offer_quote' );
    } else { // Completo o cualquier otro caso
        $offer_coupon = get_option( 'offer_complete' );
    }

    // Aplicar el cupón si es válido y la oferta está activa
    if ( ! empty( $offer_coupon ) && $max_date_timestamp >= $current_timestamp ) {
        $woocommerce->cart->apply_coupon( $offer_coupon );
    }

    // 9. Redirección final
    wp_redirect( wc_get_checkout_url() );
    exit; // Siempre llama a exit después de wp_redirect
}

add_action( 'wp_loaded', 'fee_inscription_payment' );

function trigger_elective_modal() {
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'trigger_elective_modal' ) {
        return;
    }

    if ( ! isset( $_GET['elective_student_id'] ) || empty( $_GET['elective_student_id'] ) ) {
        wp_redirect( home_url() );
        exit;
    }

    $elective_student_id = absint( $_GET['elective_student_id'] );

    if ( $elective_student_id <= 0 ) {
        wp_redirect( home_url() );
        exit;
    }

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $wpdb->update($table_students, [
        'elective' => 1
    ], ['id' => $elective_student_id]);
    

    wp_redirect( home_url() );
    exit;
}

add_action( 'wp_loaded', 'trigger_elective_modal' );