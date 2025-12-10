<?php

function add_admin_form_auto_inscription_content()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    // 1. Obtener todas las filas pendientes
    $raw_expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE academic_period = %s AND academic_period_cut = %s AND status = %s ORDER BY term_index ASC, term_position ASC",
        $code,
        $cut,
        'pendiente'
    ));

    $grouped_expected_rows = [];

    // 2. Agrupar las materias por estudiante
    foreach ($raw_expected_rows as $row) {
        $student_id = $row->student_id;

        // Cargar detalles del estudiante y materia solo si es necesario
        if (!isset($grouped_expected_rows[$student_id])) {
            $student_detail = get_student_detail($student_id);
            $initials = mb_strtoupper(substr($student_detail->last_name, 0, 1) . substr($student_detail->name, 0, 1));

            // Inicializar la entrada para el estudiante
            $grouped_expected_rows[$student_id] = [
                'student_id' => $student_id,
                'student_name' => student_names_lastnames_helper($student_id), // Usar la función helper existente
                'initials' => $initials,
                'subjects' => [], // Aquí guardaremos los nombres de las materias
                'status' => esc_html__('Waiting', 'edusystem'),
            ];
        }

        $subject_detail = get_subject_details($row->subject_id);

        // 3. Agregar el nombre de la materia a la lista del estudiante
        $grouped_expected_rows[$student_id]['subjects'][] = $subject_detail->name;
    }

    // 4. Convertir la lista de materias en un string separado por comas
    // Usamos array_values para reindexar el array si fuera necesario, aunque las claves son los IDs.
    // También convierte el array asociativo a un array de arrays para el template.
    $expected_rows = array_map(function ($row) {
        $row['subject_list'] = implode(', ', $row['subjects']);
        unset($row['subjects']); // Eliminamos el array original de materias
        return (object)$row; // Convertir a objeto para mantener la coherencia con el template original
    }, array_values($grouped_expected_rows));

    // El array $expected_rows ahora solo contiene una entrada por estudiante,
    // y cada entrada tiene 'subject_list' con las materias separadas por coma.

    include(plugin_dir_path(__FILE__) . 'templates/auto-inscription-detail.php');
}

function auto_enroll_students_bulk_callback()
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE academic_period = %s AND academic_period_cut = %s AND status = %s ORDER BY term_index ASC, term_position ASC",
        $code,
        $cut,
        'pendiente'
    ));
    foreach ($expected_rows as $key => $row) {
        automatically_enrollment($row->student_id);
    }
    wp_send_json(true);
    die();
}

add_action('wp_ajax_nopriv_auto_enroll_students_bulk', 'auto_enroll_students_bulk_callback');
add_action('wp_ajax_auto_enroll_students_bulk', 'auto_enroll_students_bulk_callback');
