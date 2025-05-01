<?php


function crm_request($api, $query, $method, $body) {
    $api_key = get_option('crm_token') ?? '';
    
    if (empty($api_key)) {
        error_log('Error Escala API: API Token no definido');
        return new WP_Error('missing_api_key', __('Configuración del servicio incompleta', 'text-domain'), array('status' => 500));
    }

    // 3. Configurar solicitud
    $endpoint = get_option('crm_url').$api.'/'.$query;
    error_log($endpoint);
    $args = array(
        'method' => $method,
        'headers' => array(
            'x-api-key' => $api_key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        )
    );

    // Solo incluir body si el método es POST o PUT
    if (in_array(strtoupper($method), ['POST', 'PUT'])) {
        $args['body'] = json_encode($body);
    }
    
    // 5. Realizar solicitud
    $response = wp_remote_request($endpoint, $args);

    // 6. Manejar errores HTTP
    if (is_wp_error($response)) {
        error_log('Error en la solicitud: ' . $response->get_error_message());
        return new WP_Error('http_error', __('Error de conexión con el servicio', 'text-domain'), array('status' => 500));
    }

    // 7. Analizar respuesta
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    // 8. Manejar 403 Forbidden específicamente
    if ($response_code === 403) {
        $decoded_response = json_decode($response_body, true);
        $error_detail = $decoded_response['message'] ?? 'Acceso no autorizado';
        
        return new WP_Error(
            'api_forbidden', 
            __('Acceso denegado: ', 'text-domain') . $error_detail,
            array('status' => 403)
        );
    }

    // 9. Manejar otros errores
    if ($response_code !== 200) {
        return new WP_Error(
            'api_error', 
            __('Error en el servicio externo: ', 'text-domain') . $response_body,
            array('status' => $response_code)
        );
    }

    // 10. Devolver datos exitosos
    return json_decode($response_body, true);
}