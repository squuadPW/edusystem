<?php

function crm_request($api, $query = '', $method, $body) {
    // 1. Validar configuración inicial: token y URL
    $crm_token = get_option('crm_token');
    $crm_url = get_option('crm_url');

    if (empty($crm_token) || empty($crm_url)) {
        // Mejor devolver un WP_Error aquí también para consistencia en el manejo de errores
        return new WP_Error(
            'crm_config_missing',
            __('La configuración del servicio CRM (token o URL) está incompleta.', 'text-domain'),
            array('status' => 500)
        );
    }

    $api_key = $crm_token; // Ya sabemos que no está vacío por la validación anterior.

    // 2. Configurar la solicitud HTTP
    $endpoint = trailingslashit($crm_url) . $api; // Usa trailingslashit para asegurar el slash al final de la URL base
    if (!empty($query)) {
        $endpoint .= '/' . $query; // Añade el query si existe
    }

    $args = array(
        'method'  => strtoupper($method), // Asegurarse de que el método esté en mayúsculas
        'timeout' => 30, // Añadir un timeout para evitar que la solicitud se cuelgue indefinidamente
        'headers' => array(
            'x-api-key'    => $api_key,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ),
    );

    // Solo incluir el 'body' si el método es POST o PUT
    if (in_array($args['method'], ['POST', 'PUT'])) {
        $args['body'] = json_encode($body);
    }

    // 3. Realizar la solicitud HTTP
    $response = wp_remote_request($endpoint, $args);

    // 4. Manejar errores de conexión o HTTP generales
    if (is_wp_error($response)) {
        return new WP_Error(
            'http_connection_error',
            __('Error de conexión con el servicio externo: ', 'text-domain') . $response->get_error_message(),
            array('status' => 500) // Generalmente un error 500 para problemas de conexión
        );
    }

    // 5. Analizar la respuesta HTTP
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    // 6. Decodificar el cuerpo de la respuesta (intentar siempre, incluso en error para mensajes detallados)
    $decoded_response = json_decode($response_body, true);

    // 7. Manejar códigos de estado HTTP no exitosos
    if ($response_code !== 200) {
        $error_message_detail = 'Error desconocido.';
        if (is_array($decoded_response) && isset($decoded_response['message'])) {
            $error_message_detail = $decoded_response['message'];
        } elseif (!empty($response_body)) {
            $error_message_detail = $response_body; // Si no es JSON o no tiene 'message'
        }

        $error_code_prefix = 'api_error';
        if ($response_code === 403) {
            $error_code_prefix = 'api_forbidden';
        }

        return new WP_Error(
            $error_code_prefix,
            sprintf(__('Error del servicio externo (%d): %s', 'text-domain'), $response_code, $error_message_detail),
            array('status' => $response_code, 'raw_body' => $response_body) // Incluir el cuerpo original para depuración
        );
    }

    // 8. Devolver los datos exitosos
    // Si la respuesta es 200 OK, pero el JSON no es válido o está vacío.
    if (json_last_error() !== JSON_ERROR_NONE && !empty($response_body)) {
        return new WP_Error(
            'invalid_json_response',
            __('La respuesta del servicio externo no es un JSON válido.', 'text-domain'),
            array('status' => 500, 'raw_body' => $response_body)
        );
    }

    return $decoded_response; // Devuelve el array decodificado o null si el body estaba vacío
}

?>