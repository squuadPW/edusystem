<?php

function wp_api()
{
    register_rest_route('api', '/assign-documents-students', array(
        'methods' => 'GET',
        'callback' => 'assign_documents_students',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('api', '/assign-academic-period-cut-student', array(
        'methods' => 'GET',
        'callback' => 'assign_academic_period_cut_student',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('api', '/adjust-projection-student', array(
        'methods' => 'POST',
        'callback' => 'adjust_projection_student',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('api', '/probando/escala', array(
        'methods' => 'GET',
        'callback' => 'probando_escala',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'wp_api');

function adjust_projection_student(WP_REST_Request $request)
{
    global $current_user, $wpdb;

    $body = $request->get_body();

    // Decodificar el JSON
    $data = json_decode($body, true);

    $subject_id = $data['subject_id'];
    $student_id = $data['student_id'];
    $cut = $data['cut'];
    $code_period = $data['code_period'];
    $calification = (float) $data['calification'];

    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$subject_id}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student_id}");
    $projection_obj = json_decode($projection->projection);

    $status_id = ($calification >= $subject->min_pass ? 3 : 4);
    $exists = false;
    foreach ($projection_obj as $item) {
        if ($item->subject_id === $subject->id) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        array_push($projection_obj, [
            'code_subject' => $subject->code_subject,
            'subject_id' => $subject->id,
            'subject' => $subject->name,
            'hc' => $subject->hc,
            'cut' => $status_id == 4 ? '' : $cut,
            'code_period' => $status_id == 4 ? '' : $code_period,
            'calification' => $status_id == 4 ? '' : $calification,
            'is_completed' => $status_id == 4 ? false : true,
            'this_cut' => false
        ]);
    }

    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj),
    ], ['id' => $projection->id]);

    $wpdb->insert($table_student_period_inscriptions, [
        'status_id' => $status_id,
        'student_id' => $projection->student_id,
        'code_subject' => $subject->code_subject,
        'code_period' => $code_period,
        'cut_period' => $cut,
        'calification' => $calification,
    ]);

    wp_send_json(array('success' => true));
    exit;
}

function probando_escala(WP_REST_Request $request) {
    // 1. Verificar API Key
    $api_key = defined('ESCALA_TOKEN') ? ESCALA_TOKEN : '';
    
    if (empty($api_key)) {
        error_log('Error Escala API: API Token no definido');
        return new WP_Error('missing_api_key', __('Configuración del servicio incompleta', 'text-domain'), array('status' => 500));
    }

    // 2. Validar y sanitizar ID
    $contact_id = '7eafad48-2602-11f0-8ee7-3b55edc7adf9'; // Ejemplo estático para pruebas

    // 3. Configurar solicitud
    $endpoint = 'https://public-api.escala.com/v1/crm/contacts/' . $contact_id;
    
    $args = array(
        'headers' => array(
            'x-api-key' => $api_key,
            'Accept' => 'application/json'
        )
    );

    // 4. Debugging: Log de la solicitud
    error_log('Intentando acceder a: ' . $endpoint);
    error_log('Headers enviados: ' . print_r($args['headers'], true));

    // 5. Realizar solicitud
    $response = wp_remote_get($endpoint, $args);

    // 6. Manejar errores HTTP
    if (is_wp_error($response)) {
        error_log('Error en la solicitud: ' . $response->get_error_message());
        return new WP_Error('http_error', __('Error de conexión con el servicio', 'text-domain'), array('status' => 500));
    }

    // 7. Analizar respuesta
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('Respuesta recibida - Código: ' . $response_code);
    error_log('Contenido de respuesta: ' . $response_body);

    // 8. Manejar 403 Forbidden específicamente
    if ($response_code === 403) {
        $decoded_response = json_decode($response_body, true);
        $error_detail = $decoded_response['message'] ?? 'Acceso no autorizado';
        
        error_log('Error 403 Detallado: ' . print_r($decoded_response, true));
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


function assign_academic_period_cut_student()
{
    // global $wpdb;
    // $table_students = $wpdb->prefix.'students';
    // $students = $wpdb->get_results("SELECT * FROM {$table_students} where academic_period = '20242025'");
    // $users_affected = [];
    // foreach ($students as $key => $student) {
    //     $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    //     $inscription_cut = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id={$student->id} AND code_period = '20242025' AND cut_period = 'A'");

    //     if (empty($inscription_cut)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_period_inscriptions,
    //             array(
    //                 'status_id' => 1,
    //                 'student_id' => $student->id,
    //                 'code_period' => '20242025',
    //                 'cut_period' => 'A',
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }
    // }

    // wp_send_json(array('studens_affected' => $users_affected));
}

function assign_documents_students()
{
    // global $wpdb;
    // $table_students = $wpdb->prefix.'students';
    // $students = $wpdb->get_results("SELECT * FROM {$table_students}");
    // $users_affected = [];
    // foreach ($students as $key => $student) {
    //     $table_student_documents = $wpdb->prefix.'student_documents';
    //     $missing_documents = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'MISSING DOCUMENTS'");

    //     if (empty($missing_documents)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_documents,
    //             array(
    //                 'student_id' => $student->id,
    //                 'document_id' => 'MISSING DOCUMENTS',
    //                 'attachment_id' => 0,
    //                 'approved_by' => NULL,
    //                 'status' => 0,
    //                 'description' => NULL,
    //                 'is_required' => 0,
    //                 'is_visible' => 0,
    //                 'upload_at' => NULL,
    //                 'created_at' => current_time('mysql'), // Add this line
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }

    //     $enrollment_document = $wpdb->get_row("SELECT * FROM {$table_student_documents} WHERE student_id={$student->id} AND document_id = 'ENROLLMENT'");

    //     if (empty($enrollment_document)) {
    //         // Insert new row if no document was found
    //         $wpdb->insert(
    //             $table_student_documents,
    //             array(
    //                 'student_id' => $student->id,
    //                 'document_id' => 'ENROLLMENT',
    //                 'attachment_id' => 0,
    //                 'approved_by' => NULL,
    //                 'status' => 0,
    //                 'description' => NULL,
    //                 'is_required' => 1,
    //                 'is_visible' => 0,
    //                 'upload_at' => NULL,
    //                 'created_at' => current_time('mysql'), // Add this line
    //             )
    //         );
    //         if (!in_array($student->id, $users_affected)) {
    //             array_push($users_affected, $student->id);
    //         }
    //     }
    // }

    // wp_send_json(array('studens_affected' => $users_affected));
}
