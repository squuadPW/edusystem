<?php 

/**
 * Maneja la solicitud AJAX para agregar un nuevo producto y guardar los metadatos de la 
 * solicitud de documento en el carrito. Primero, limpia el carrito existente y luego 
 * agrega el nuevo producto con los datos necesarios. Se asegura de que todos los datos 
 * requeridos estén presentes antes de proceder.
 *
 * @uses WC_Cart Para vaciar el carrito y agregar nuevos productos
 *
 * @global WC_Cart $cart Instancia actual del carrito de WooCommerce
 *
 * @var int $product_id   ID del producto a agregar al carrito
 * @var int $partner_id   ID del partner asociado a la solicitud
 * @var int $student_id   ID del estudiante asociado a la solicitud
 * @var int $by           ID del usuario que realiza la acción
 * @var int $type_id      ID del tipo de documento solicitado
 * @var string $reason    Razón o descripción de la solicitud de documento
 *
 * @return json Respuesta JSON con:
 *              - success: URL de redirección a la página de checkout y mensaje de éxito
 *              - error: Mensaje de error si faltan datos requeridos
 */
add_action('wp_ajax_save_document_request_metadata_cart_item', 'save_document_request_metadata_cart_item');
add_action('wp_ajax_nopriv_save_document_request_metadata_cart_item', 'save_document_request_metadata_cart_item');
function save_document_request_metadata_cart_item() {

    // Obtener los datos del POST
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $partner_id = (int) ($_POST['partner_id'] ?? 0);
    $student_id = (int) ($_POST['student_id'] ?? 0);
    $by         = (int) ($_POST['by'] ?? get_current_user_id());
    $type_id    = (int) ($_POST['type_id'] ?? 0);
    $reason     = sanitize_text_field($_POST['reason'] ?? '');

    // Verificar que todos los datos requeridos estén presentes
    if (empty($product_id) || empty($partner_id) || empty($student_id) || empty($by) || empty($type_id)) {
        wp_send_json_error(['message' => __('Required data is missing.', 'edusystem')]);
        exit;
    }

    // Vaciar el carrito antes de agregar el nuevo producto
    if ( WC()->cart ) WC()->cart->empty_cart();

    // Definir el metadato document-request
    $document_request = [
        'document_request' => [
            'partner_id'  => $partner_id,
            'student_id'  => $student_id,
            'description' => $reason,
            'by'          => $by,
            'type_id'     => $type_id,
        ],
    ];

    // Añadir el producto al carrito con metadatos
    $add_to_cart = WC()->cart->add_to_cart($product_id, 1, 0, [], $document_request);

    // Verificar si el producto se agregó correctamente
    if ($add_to_cart) {
        wp_send_json_success([
            'url' => home_url('/checkout/'),
            'message' => __('The product has been added successfully.', 'edusystem')
        ]);
    } else {
        wp_send_json_error(['message' => __('The product could not be added.', 'edusystem')]);
    }
    exit;
}

/**
 * Guarda los metadatos de la solicitud de documento en el ítem de la orden durante el checkout.
 * Si el ítem del carrito contiene el metadato 'document_request', este se transfiere al ítem
 * de la orden para su posterior procesamiento.
 *
 * @hook woocommerce_checkout_create_order_line_item
 *
 * @param WC_Order_Item_Product $item        Objeto del ítem de la orden
 * @param string $cart_item_key              Clave del ítem en el carrito
 * @param array $values                      Valores del ítem
 * @param WC_Order $order                    Objeto de la orden completa
 *
 * @var array $document_request Metadata que contiene:
 *              - partner_id: ID del partner asociado
 *              - student_id: ID del estudiante solicitante
 *              - description: Descripción de la solicitud
 *              - by: ID del usuario que realiza la solicitud
 *              - type_id: ID del tipo de documento solicitado
 */
add_action('woocommerce_checkout_create_order_line_item', 'save_document_request_metadata_order', 10, 4);
function save_document_request_metadata_order($item, $cart_item_key, $values, $order) {
    // Verificar si hay metadatos en el item
    if (isset($values['document_request'])) {
        
        // Guardar los metadatos en el item de la orden
        $document_request = $values['document_request'];
        $item->add_meta_data( 'document_request', $document_request, true);
    }
}

/**
 * Procesa la solicitud de documento cuando una orden se marca como completada.
 * Evalúa si alguno de los ítems pertenece a la categoría "documents" y tiene el metadato
 * 'document_request'. Si es así, crea un nuevo registro en la base de datos de solicitudes.
 *
 * @hook woocommerce_order_status_completed
 *
 * @param int $order_id ID de la orden completada
 *
 * @global wpdb $wpdb Objeto de base de datos de WordPress
 *
 * @uses wc_get_order() Para obtener el objeto de la orden
 * @uses has_term() Para verificar la categoría del producto
 *
 * @var WC_Order $order Objeto de la orden que se está procesando
 * @var array $data Datos para insertar en la tabla de solicitudes, que incluyen:
 *      - partner_id: ID del partner asociado
 *      - student_id: ID del estudiante que realiza la solicitud
 *      - description: Descripción de la solicitud
 *      - by: ID del usuario que realiza la solicitud
 *      - type_id: ID del tipo de documento solicitado
 *      - status_id: Estado inicial de la solicitud (0 para nuevo)
 */
add_action('woocommerce_order_status_completed', 'process_document_request_completing_order', 10, 1);
function process_document_request_completing_order($order_id) {

    // Obtener la orden
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_id => $item) {

        // Obtener el producto_id y la metadata 'document_request' del item
        $product_id = $item->get_product_id();
        $document_request = $item->get_meta('document_request') ?? '';

        // Verificar si el producto pertenece a la categoría "documents" y tiene el metadato 'document_request'
        if ( has_term('documents', 'product_cat', $product_id) && !empty( $document_request ) ) {
            
            $data = [
                'partner_id'  => $document_request['partner_id'],
                'student_id'  => $document_request['student_id'],
                'description' => $document_request['description'],
                'by'          => $document_request['by'],
                'type_id'     => $document_request['type_id'],
                'status_id'   => 0,
            ];

            global $wpdb;
            $wpdb->insert("{$wpdb->prefix}requests", $document_request );
        }
    }
}

add_action('wp_ajax_send_request', 'send_request_callback');
add_action('wp_ajax_nopriv_send_request', 'send_request_callback');
function send_request_callback()
{
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $table_students = $wpdb->prefix . 'students';
    $table_requests = $wpdb->prefix . 'requests';

    $type_id = sanitize_text_field($_POST['type_id']);
    $reason = sanitize_text_field($_POST['reason']);
    $student_id = sanitize_text_field($_POST['student_id']);
    $partner_id = sanitize_text_field($_POST['partner_id']);

    if (!$partner_id) {
        if (in_array('parent', $roles) && !in_array('student', $roles)) {
            $partner_id = $current_user->ID;
        } else if (in_array('parent', $roles) && in_array('student', $roles)) {
            $partner_id = $current_user->ID;
        } else if (!in_array('parent', $roles) && in_array('student', $roles)) {
            $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}'");
            $partner_id = $student->partner_id;
        }
    }

    $wpdb->insert($table_requests, [
        'partner_id' => $partner_id,
        'student_id' => $student_id,
        'description' => $reason,
        'type_id' => $type_id,
        'status_id' => 0,
    ]);

    send_notification_staff_particular('New request', 'Please be informed that we have received a new request to the system, so please check it in the system as soon as possible in order to be able to attend it.', 0);
    send_notification_staff_particular('New request', 'Please be informed that we have received a new request to the system, so please check it in the system as soon as possible in order to be able to attend it.', 2);
    wp_send_json_success(array('success' => true));
    exit;
}



