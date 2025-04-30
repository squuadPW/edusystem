<?php
/**
 * Obtiene un contacto de Escala API
 * 
 * @param int|string $contact_id ID del contacto a obtener
 * @return array|WP_Error Respuesta de la API o error
 */
function get_escala_contact($contact_id = 15) {
    // Configuración base
    $api_url = 'https://public-api.escala.com/v1/crm/contacts/';
    $api_key = ESCALA_TOKEN; // Almacenar la API Key en opciones de WordPress

    // Verificar que tenemos la API Key
    if (empty($api_key)) {
        return new WP_Error('missing_api_key', __('API Key de Escala no configurada', 'text-domain'));
    }

    // Validar ID del contacto
    if (empty($contact_id) || !is_numeric($contact_id)) {
        return new WP_Error('invalid_contact_id', __('ID de contacto inválido', 'text-domain'));
    }

    // Construir URL final
    $endpoint = $api_url . $contact_id;

    // Configurar headers
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ),
        'timeout' => 15
    );

    // Hacer la solicitud GET
    $response = wp_remote_get($endpoint, $args);

    // Manejar respuesta
    if (is_wp_error($response)) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    // Manejar códigos de error
    if ($response_code !== 200) {
        $error_message = isset($body['message']) ? $body['message'] : __('Error desconocido', 'text-domain');
        return new WP_Error('api_error', sprintf(__('Error %d: %s', 'text-domain'), $response_code, $error_message));
    }

    return $body;
}