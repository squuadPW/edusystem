<?php

function automatically_enrollment($student_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d", $student_id));

    if (!$student) {
        return;
    }
    if ($student->status_id == 0 || $student->status_id > 3) {
        return;
    }

    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $full_name_student = student_names_lastnames_helper($student->id);
    $user = get_user_by('email', $student->email);
    $user_id = $user ? $user->ID : 0;

    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE status != 'blocked' AND student_id = %d AND academic_period = %s AND academic_period_cut = %s ORDER BY term_index ASC, term_position ASC",
        $student->id,
        $code,
        $cut
    ));

    // Primero procesar las materias previstas en la matriz del estudiante para este periodo
    if (!empty($expected_rows)) {
        foreach ($expected_rows as $row) {
            $subject_id = $row->subject_id;
            if (empty($subject_id)) {
                edusystem_get_log('Empty subject ID found for student ' . $full_name_student . ' in expected rows', 'Automatically enrollment', $user_id);
                continue;
            }

            $subject = get_subject_details($subject_id);
            if (!$subject) {
                edusystem_get_log('Subject not found for ID ' . $subject_id . ' for student ' . $full_name_student, 'Automatically enrollment', $user_id);
                continue;
            }

            // Verificar si el estudiante puede inscribirse en la materia
            $available_inscription_subject = available_inscription_subject($student->id, $subject->id);
            if ($available_inscription_subject !== true) {

                $log_message = '';

                switch ($available_inscription_subject) {
                    case 'active_or_approved':
                        // Corresponde a status_id 1 (Activo) o 3 (Aprobado)
                        $log_message = 'The student ' . $full_name_student . ' already has an active or approved enrollment for the subject ' . $subject->name . ' (' . $subject->id . ')';
                        break;

                    case 'max_retries_reached':
                        // Corresponde a status_id 4 (Fallido) con 2 o más registros
                        $log_message = 'The student ' . $full_name_student . ' has already failed the subject ' . $subject->name . ' (' . $subject->id . ') the maximum number of allowed times.';
                        break;

                    default:
                        // Cualquier otro caso de error no previsto
                        $log_message = 'The student ' . $full_name_student . ' cannot enroll in the subject ' . $subject->name . ' (' . $subject->id . ') due to an unknown status restriction: ' . $available_inscription_subject;
                        break;
                }

                edusystem_get_log($log_message, 'Automatically enrollment', $user_id);
                continue;
            }

            // Verificar oferta disponible en este periodo
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if (!$offer_available_to_enroll) {
                // Si no hay oferta, no inscribimos esta materia
                edusystem_get_log('No offer available for subject ' . $subject->name . ' (' . $subject->id . ')' . ' for student ' . $full_name_student . ' in the period ' . $code . ' - ' . $cut, 'Automatically enrollment', $user_id);
                continue;
            }

            // Insertar inscripción
            $section = load_section_available($subject->id, $code, $cut);
            $wpdb->insert($table_student_period_inscriptions, [
                'status_id' => 1,
                'section' => $section,
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'code_subject' => $subject->code_subject,
                'code_period' => $code,
                'cut_period' => $cut,
                'type' => $subject->type
            ]);

            // Update student's projection so the subject appears as this cut
            update_projection_after_enrollment($student->id, $subject->id, $code, $cut, 1);
            update_expected_matrix_after_enrollment($student->id, $subject->id, $code, $cut);
            edusystem_get_log('Enrolled in subject ' . $subject->name . ' (' . $subject->id . ')' . ' for student: ' . $full_name_student, 'Automatically enrollment', $user_id);
            edusystem_get_log('Projection updated for subject ' . $subject->name . ' (' . $subject->id . ')' . ' for student: ' . $full_name_student, 'Automatically enrollment', $user_id);

            // mandamos a moodle
            $offer = get_offer_filtered($subject->id, $code, $cut, $section);
            if ($offer && isset($offer->moodle_course_id) && get_option('auto_enroll_regular')) {
                $enrollments = courses_enroll_student($student->id, [(int) $offer->moodle_course_id]);
                if (!empty($enrollments)) {
                    enroll_student($enrollments);
                    edusystem_get_log('Student ' . $full_name_student . ' enrolled in Moodle Course ID: ' . $offer->moodle_course_id, 'Automatically enrollment', $user_id);
                }
            }
        }
    } else {
        edusystem_get_log('No expected rows found by student ' . $full_name_student . ' for the period ' . $code . '-' . $cut, 'Automatically enrollment', $user_id);
    }

    update_max_upload_at($student->id);
}

function load_inscriptions_electives($student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $conditions = array();
    $params = array();

    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    $conditions[] = "type = %s";
    $params[] = 'elective';

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function load_inscriptions_electives_valid_arr($student, $status = "(status_id = 1 OR status_id = 3)")
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';

    $offers = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE `type` = 'elective'");
    $electives_ids = [];
    $electives_codes = [];
    foreach ($offers as $key => $elective) {
        array_push($electives_ids, $elective->id);
        array_push($electives_codes, $elective->code_subject);
    }

    if (empty($electives_ids) && empty($electives_codes)) {
        return 0;
    }

    $conditions = array();
    $params = array();

    // Crear condiciones para subject_id y code_subject
    $or_conditions = array();

    if (!empty($electives_ids)) {
        $or_conditions[] = "subject_id IN (" . implode(',', array_fill(0, count($electives_ids), '%d')) . ")";
        $params = array_merge($params, $electives_ids);
    }

    if (!empty($electives_codes)) {
        $or_conditions[] = "code_subject IN (" . implode(',', array_fill(0, count($electives_codes), '%s')) . ")";
        $params = array_merge($params, $electives_codes);
    }

    // Agregar la condición de OR si hay condiciones
    if (!empty($or_conditions)) {
        $conditions[] = "(" . implode(" OR ", $or_conditions) . ")";
    }

    // Agregar la condición de student_id
    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    // Agregar la condición de status
    $conditions[] = $status;

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return $inscriptions;
}

function load_inscriptions_electives_valid($student, $status = "(status_id = 1 OR status_id = 3)")
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';

    $offers = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE `type` = 'elective'");
    $electives_ids = [];
    $electives_codes = [];
    foreach ($offers as $key => $elective) {
        array_push($electives_ids, $elective->id);
        array_push($electives_codes, $elective->code_subject);
    }

    if (empty($electives_ids) && empty($electives_codes)) {
        return 0;
    }

    $conditions = array();
    $params = array();

    // Crear condiciones para subject_id y code_subject
    $or_conditions = array();

    if (!empty($electives_ids)) {
        $or_conditions[] = "subject_id IN (" . implode(',', array_fill(0, count($electives_ids), '%d')) . ")";
        $params = array_merge($params, $electives_ids);
    }

    if (!empty($electives_codes)) {
        $or_conditions[] = "code_subject IN (" . implode(',', array_fill(0, count($electives_codes), '%s')) . ")";
        $params = array_merge($params, $electives_codes);
    }

    // Agregar la condición de OR si hay condiciones
    if (!empty($or_conditions)) {
        $conditions[] = "(" . implode(" OR ", $or_conditions) . ")";
    }

    // Agregar la condición de student_id
    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    // Agregar la condición de status
    $conditions[] = $status;

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function load_inscriptions_regular_valid($student, $status = "(status_id = 1 OR status_id = 3)")
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $matrix_regular = only_pensum_regular($student->program_id);

    $regulars_ids = [];
    foreach ($matrix_regular as $key => $regular) {
        array_push($regulars_ids, $regular->subject_id);
    }

    if (empty($regulars_ids)) {
        return 0;
    }

    $conditions = array();
    $params = array();

    $conditions[] = "subject_id IN (" . implode(',', array_fill(0, count($regulars_ids), '%d')) . ")";
    $params = array_merge($params, $regulars_ids);

    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    $conditions[] = $status;

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function generate_projection_student( $student_id ) {

    // Validar el ID del estudiante
    if ( !is_numeric($student_id) || $student_id <= 0 ) return false;

    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $table_expected_matrix_school = $wpdb->prefix . 'expected_matrix_school';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $table_students = $wpdb->prefix . 'students';
    $table_pensum = $wpdb->prefix . 'pensum';

    // Obtener información del estudiante incluyendo expected_graduation_date y academic_period
    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, academic_period, initial_cut FROM {$table_students} WHERE id = %d",
        $student_id
    ));
    if ( !$student ) return false;

    // Obtener pensum del programa y proyección actual
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];

    // Obtener el pensum activo para el programa (matriz completa)
    $pensum = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = %s", $program->identificator));
    if (!$pensum) return false;

    // ontiene las materias del pensum
    $pensum_matrix = json_decode( $pensum->matrix );
    if ( empty($pensum_matrix) ) return false;

    // Obtener inscripciones del estudiante
    $inscriptions = get_inscriptions_by_student($student_id);

    // recuento de las veces que se inscribio esa materia
    $attempts_count = array_count_values(array_column($inscriptions, 'subject_id'));
    $inscriptions_by_code = [];
    $elective_inscriptions = [];
    
    // Se procesan todas las inscripciones del estudiante y se alamacenan las aprovadas y activas
    if ( !empty($inscriptions) ) {

        foreach ( $inscriptions as $inscription ) {

            $status_id = (int) $inscription->status_id;

            if ( $inscription->type === 'elective' && ( $status_id == 1 || $status_id == 3 ) ) {
                
                // Las electivas se mantienen separadas para el procesamiento posterior de solo las completadas.
                $elective_inscriptions[] = $inscription;

            } else if ( $status_id == 1 || $status_id == 3  ) {

                $code = $inscription->code_subject;
                $inscriptions_by_code[$code] = $inscription;
            }
        }
    }

    // Generar proyección base usando la matriz COMPLETA del pensum (incluye regulares y otros tipos)
    $projection = [];
    foreach ( $pensum_matrix as $subject_matrix ) {

        $subject = get_subject_details( (int) $subject_matrix->id) ?? get_subject_details_code($subject_matrix->code_subject);
        if ( !$subject ) continue;

        $inscription = $inscriptions_by_code[ $subject->code_subject ] ?? null;
        $status_id = $inscription ? (int) $inscription->status_id : null;

        // Determinar si la materia está 'completada' (solo si está APROBADA = 3)
        $is_completed = ($status_id === 3);

        // Determinar si es 'this_cut' (solo si está ACTIVA = 1)
        $is_this_cut = ($status_id === 1);

        $projection[] = [
            'hc' => $subject->hc ?? 0,
            'cut' => $is_completed || $is_this_cut ? $inscription->cut_period : "",
            'type' => $subject->type,
            'subject_id' => (int) $subject->id,
            'subject' => $subject->name,
            'this_cut' => $is_this_cut,
            'code_period' => $is_completed || $is_this_cut ? $inscription->code_period : "",
            'calification' => $is_completed ? $inscription->calification : "",
            'code_subject' => $subject->code_subject,
            'is_completed' => $is_completed, 
            'welcome_email' => $is_completed || $is_this_cut, // True si está Approved/Active
            'assigned_slots' => (int) $subject->retake_limit ?? 0,
            'attempts_count' => (int) $attempts_count[$subject->id] ?? 0,
        ];
    }

    // Agregar materias electivas a la proyección
    foreach ( $elective_inscriptions as $inscription ) {

        $subject = get_subject_details($inscription->subject_id) ?? get_subject_details_code($inscription->code_subject);
        if ( !$subject ) continue;

        if ( $subject ) {

            $projection[] = [
                'code_subject' => $subject->code_subject,
                'subject_id' => $subject->id,
                'subject' => $subject->name,
                'hc' => $subject->hc,
                'cut' => $inscription->cut_period,
                'code_period' => $inscription->code_period,
                'calification' => $inscription->calification ?? 0,
                'is_completed' => $inscription->status_id == 3 ? true : false,
                'this_cut' => $inscription->status_id == 1 ? true : false,
                'welcome_email' => true,
                'type' => 'elective',
                'assigned_slots' => $subject->retake_limit ?? 0,
                'attempts_count' => $attempts_count[$subject->id] ?? 0,
            ];
        }
    }

    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_student_academic_projection WHERE student_id = %d",
        $student_id
    ));

    if ( $exists ) {
        // Actualizar proyección existente
        $result = $wpdb->update( $table_student_academic_projection,
            [ 'projection' => json_encode($projection), ],
            ['student_id' => $student_id],
        );

    } else {
        // Insertar nueva proyección sino existe
        $result = $wpdb->insert($table_student_academic_projection, [
            'student_id' => $student_id,
            'projection' => json_encode($projection)
        ]);
    }

    // Genera la matris esperada del estudiante y inserta los registro en DB
    generate_expectation_matrix( $student, $projection, $pensum );
}

/* function generate_projection_student($student_id, $force = false)
{
    global $wpdb;

    // Validar el ID del estudiante
    if (!is_numeric($student_id) || $student_id <= 0) {
        return false;
    }

    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_students = $wpdb->prefix . 'students';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $table_expected_matrix_school = $wpdb->prefix . 'expected_matrix_school';

    // Verificar si existe proyección y si no es forzada
    if (!$force) {
        $existing_projection = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_student_academic_projection} WHERE student_id = %d",
            $student_id
        ));
        if ($existing_projection) {
            return false;
        }
    }

    // Obtener información del estudiante incluyendo expected_graduation_date y academic_period
    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, academic_period, initial_cut FROM {$table_students} WHERE id = %d",
        $student_id
    ));

    if (!$student) {
        return false;
    }

    // Obtener pensum del programa y proyección actual
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];
    $table_pensum = $wpdb->prefix . 'pensum';

    // Obtener el pensum activo para el programa (matriz completa)
    $pensum = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = %s", $program->identificator));
    if (!$pensum) {
        return false;
    }

    $pensum_matrix = json_decode($pensum->matrix);
    if (empty($pensum_matrix)) {
        return false;
    }
    // También obtener la lista de regulares (necesaria para build_detailed_matrix)
    $matrix_regular = only_pensum_regular($program->identificator);

    // Calcular matriz basada en expected_graduation_date
    $calculated_matrix = null;
    $terms_available = null;

    if ($student && !empty($student->expected_graduation_date)) {
        try {
            // Convertir expected_graduation_date de MM/YYYY a fecha
            list($month, $year) = explode('/', $student->expected_graduation_date);
            $graduation_date = new DateTime("$year-$month-01");
            $graduation_date->modify('last day of this month');

            // Crear rango desde academic_period hasta expected_graduation_date
            $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
            $registration_date = new DateTime($period->start_date);

            // Contar períodos académicos únicos en ese rango
            $periods_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$table_academic_periods_cut} 
                 WHERE start_date >= %s AND max_date <= %s",
                $registration_date->format('Y-m-d'),
                $graduation_date->format('Y-m-d')
            ));

            // Aplicar límites: min 5, max 15
            $terms_available = min(15, max(5, intval($periods_count)));

            // Obtener matriz correspondiente de expected_matrix_school
            $matrix_config = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_expected_matrix_school} 
                 WHERE terms_available = %d",
                $terms_available
            ));

            if ($matrix_config) {
                $terms_config_decoded = json_decode($matrix_config->terms_config, true);
                // Build detailed matrix (in-memory array). We will persist it to `student_expected_matrix` later.
                $detailed_matrix = build_detailed_matrix($terms_config_decoded, $matrix_config->terms_available, $matrix_regular, $student_id);
                // Keep an encoded copy for backward compatibility if other code reads it; but we will not store it as primary source.
                $calculated_matrix = !empty($detailed_matrix) ? json_encode($detailed_matrix) : null;
            }
        } catch (Exception $e) {
            // Si hay error en el cálculo, continuar sin matriz
            $calculated_matrix = null;
            $terms_available = null;
        }
    } else {
        $full_name_student = student_names_lastnames_helper($student_id);
        edusystem_get_log('Expected graduation date is empty for student: ' . $full_name_student, 'Automatically enrollment');
    }

    // Obtener inscripciones del estudiante
    $inscriptions = get_inscriptions_by_student($student_id);
    $inscriptions_by_code = [];
    $elective_inscriptions = [];

    // Se procesan todas las inscripciones del estudiante y se elige la de mayor precedencia (3=Aprobada, luego 1=Activa, luego 4=Reprobada) para cada 'code_subject'.
    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            // Obtener los detalles de la materia
            $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

            if ($subject && $subject->type === 'elective') {
                // Las electivas se mantienen separadas para el procesamiento posterior de solo las completadas.
                $elective_inscriptions[] = $inscription;
            } else {
                $code = $inscription->code_subject;
                $current_status = (int) $inscription->status_id;

                if (isset($inscriptions_by_code[$code])) {
                    $existing_status = (int) $inscriptions_by_code[$code]->status_id;

                    // Priorizar estado Aprobado (3) sobre cualquier otro.
                    if ($existing_status === 3) {
                        continue; // Ya tenemos el mejor estado posible.
                    }

                    // Si el nuevo estado es Aprobado (3), sobrescribir inmediatamente.
                    if ($current_status === 3) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si el estado existente es Activo (1) y el nuevo es Reprobado (4), mantener Activo (1).
                    if ($existing_status === 1 && $current_status === 4) {
                        continue;
                    }

                    // Si el nuevo estado es Activo (1) y el existente es Reprobado (4) o To begin (0), sobrescribir con Activo (1).
                    if ($current_status === 1 && ($existing_status === 4 || $existing_status === 0)) {
                        $inscriptions_by_code[$code] = $inscription;
                        continue;
                    }

                    // Si ambos son Reprobado (4), To begin (0), o si el nuevo estado es mejor que el existente, tomar el nuevo.
                    if ($current_status > $existing_status && $current_status !== 2) {
                        $inscriptions_by_code[$code] = $inscription;
                    }
                } else {
                    // Si no existe entrada, simplemente agregar, siempre que no sea Unsubscribed (2)
                    if ($current_status !== 2) {
                        $inscriptions_by_code[$code] = $inscription;
                    }
                }
            }
        }
    }

    // Generar proyección base usando la matriz COMPLETA del pensum (incluye regulares y otros tipos)
    $projection = [];
    foreach ($pensum_matrix as $m) {
        // $m contiene elementos con 'id', 'name', 'code_subject', 'type', etc. (según cómo se guardó la matriz)
        $subject_details = get_subject_details($m->id);
        if (!$subject_details) {
            // Si no existe la materia en school_subjects, saltar
            continue;
        }

        $inscription = $inscriptions_by_code[$subject_details->code_subject] ?? null;
        $status_id = $inscription ? (int) $inscription->status_id : null;

        // Determinar si la materia está 'completada' (solo si está APROBADA = 3)
        $is_completed = ($status_id === 3);

        // Determinar si es 'this_cut' (solo si está ACTIVA = 1)
        $is_this_cut = ($status_id === 1);

        $projection[] = [
            'hc' => isset($m->hc) ? $m->hc : (isset($subject_details->hc) ? $subject_details->hc : ''),
            // La información de corte solo se llena si está Aprobada (3) o Activa (1).
            'cut' => $is_completed || $is_this_cut ? $inscription->cut_period : "",
            'type' => isset($m->type) ? strtolower($m->type) : strtolower($subject_details->type),
            'subject' => $subject_details->name,
            'this_cut' => $is_this_cut, // Solo Activa
            'subject_id' => (string) $subject_details->id,
            'code_period' => $is_completed || $is_this_cut ? $inscription->code_period : "",
            // La calificación solo se llena si está Aprobada (3).
            'calification' => $is_completed ? $inscription->calification : "",
            'code_subject' => $subject_details->code_subject,
            'is_completed' => $is_completed, // Solo Aprobada
            'welcome_email' => $is_completed || $is_this_cut, // True si está Approved/Active
            'assigned_slots' => $subject_details->retake_limit ?? 0,
        ];
    }

    // Agregar materias electivas a la proyección
    foreach ($elective_inscriptions as $inscription) {
        // abandonar si es reprobada
        if ((int) $inscription->status_id === 4) {
            continue;
        }

        // Obtener detalles de la materia electiva
        $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

        if ($subject) {
            $projection[] = [
                'code_subject' => $subject->code_subject,
                'subject_id' => $subject->id,
                'subject' => $subject->name,
                'hc' => $subject->hc,
                'cut' => $inscription->cut_period,
                'code_period' => $inscription->code_period,
                'calification' => $inscription->calification ?? 0,
                'is_completed' => $inscription->status_id == 3 ? true : false,
                'this_cut' => $inscription->status_id == 1 ? true : false,
                'welcome_email' => true,
                'type' => 'elective',
                'assigned_slots' => $subject->retake_limit ?? 0,
            ];
        }
    }

    // Si es forzado, actualizar registros
    if ($force) {
        $wpdb->query('START TRANSACTION');

        try {
            // Actualizar estudiante y eliminar proyecciones existentes en una sola transacción
            $wpdb->update($table_students, ['elective' => 0, 'terms_available' => $terms_available], ['id' => $student_id]);

            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_student_academic_projection WHERE student_id = %d",
                $student_id
            ));

            if ( $exists ) {
                // Actualizar proyección existente
                $result = $wpdb->update( $table_student_academic_projection,
                    [
                        'projection' => json_encode($projection),
                        'matrix'     => $calculated_matrix,
                    ],
                    ['student_id' => $student_id],
                );
            } else {
                // Insertar nueva proyección sino existe (terms_available now stored on student)
                $result = $wpdb->insert($table_student_academic_projection, [
                    'student_id' => $student_id,
                    'projection' => json_encode($projection),
                    'matrix' => $calculated_matrix
                ]);
            }

            if ( $result === false ) {
                throw new Exception('Error al insertar la proyección');
            }

            // Persist expected matrix rows if available
            if (!empty($detailed_matrix)) {
                clear_expected_matrix_for_student($student_id);
                persist_expected_matrix($student_id, $detailed_matrix);
            }

            // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

            // 1. Inscripciones regulares (ya con precedencia aplicada)
            foreach ($inscriptions_by_code as $inscription) {
                // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
                if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            // 2. Inscripciones electivas (solo si son Aprobadas, ya que son el único estado que se incluye)
            foreach ($elective_inscriptions as $inscription) {
                if ((int) $inscription->status_id === 3) {
                    update_expected_matrix_after_enrollment(
                        $student_id,
                        (int) $inscription->subject_id,
                        $inscription->code_period,
                        $inscription->cut_period
                    );
                }
            }

            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    // Store terms_available on student record (projection no longer holds it)
    if (!is_null($terms_available)) {
        $wpdb->update($table_students, ['terms_available' => $terms_available], ['id' => $student_id]);
    }

    // Insertar nueva proyección sin forzar con matriz calculada
    $result = $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection),
        'matrix' => $calculated_matrix
    ]);

    if ($result !== false) {

        // If we have a detailed matrix, persist it into `student_expected_matrix`.
        if (!empty($detailed_matrix)) {
            // If this operation was forced, clear previous expected matrix rows for the student to avoid duplicates.
            if ($force === true) {
                clear_expected_matrix_for_student($student_id);
            }
            persist_expected_matrix($student_id, $detailed_matrix);
        }

        // Sincronizar el estado de la matriz de expectativa después de persistir la matriz detallada.

        // 1. Inscripciones regulares (ya con precedencia aplicada)
        foreach ($inscriptions_by_code as $inscription) {
            // Actualizar solo si el estado es relevante para la matriz (Activa, Aprobada, Reprobada)
            if (in_array((int) $inscription->status_id, [1, 3, 4])) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        // 2. Inscripciones electivas (solo si son Aprobadas)
        foreach ($elective_inscriptions as $inscription) {
            if ((int) $inscription->status_id === 3) {
                update_expected_matrix_after_enrollment(
                    $student_id,
                    (int) $inscription->subject_id,
                    $inscription->code_period,
                    $inscription->cut_period
                );
            }
        }

        return true;
    }

    return false;
} */

function generate_expectation_matrix( $student, $projection, $pensum ) {

    if ( !$student || !$projection || !$pensum ) return false;

    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $table_academic_periods_cut = "{$wpdb->prefix}academic_periods_cut";
    $table_expected_matrix = "{$wpdb->prefix}expected_matrix";

    // lista de materias del pensum
    $pensum_matrix = json_decode( $pensum->matrix );
    if ( empty($pensum_matrix) ) return false;

    // fecha de registro del estudiante
    $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
    $registration_date = new DateTime($period->start_date);

    // data de periodos futuros a la fecha de registro
    $future_periods = $wpdb->get_results( $wpdb->prepare(
        "SELECT DISTINCT code, cut FROM `{$table_academic_periods_cut}`
        WHERE start_date >= %s
        ORDER BY start_date ASC LIMIT 20",
        $registration_date->format('Y-m-d')
    ));
    
    // lista de materias regulares a ver segun el pensum
    $subjects = only_pensum_regular($pensum->program_id);

    // obtiene la matrix de configuracion de acuerdo se necesite
    if( !empty($student->expected_graduation_date) ) {

        // Convertir expected_graduation_date de MM/YYYY a fecha
        list($month, $year) = explode('/', $student->expected_graduation_date);
        $graduation_date = new DateTime("$year-$month-01");
        $graduation_date->modify('last day of this month');

        // Contar períodos académicos únicos en ese rango
        $periods_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
            FROM `{$table_academic_periods_cut}`
            WHERE start_date >= %s AND max_date <= %s",
            $registration_date->format('Y-m-d'),
            $graduation_date->format('Y-m-d')
        ));

        // Aplicar límites: min 5, max 15 // consultar el terminos del programa
        $terms_available = min(15, max(5, intval($periods_count)));
       
        $matrix_config_json = $wpdb->get_var( $wpdb->prepare(
            "SELECT matrix_config FROM `$table_expected_matrix`
            WHERE key_condition LIKE 'terms' AND value_condition = %s ;",
            $terms_available
        ));

    } else {
        $matrix_config_json = $wpdb->get_var( $wpdb->prepare(
            "SELECT matrix_config FROM `$table_expected_matrix` WHERE id = %d ;",
            $pensum->expected_matrix_id
        ));
    }

    // matrix de configuracion
    $matrix_config = json_decode( $matrix_config_json );

    // periodo y corte actual
    $current_period_cut = load_current_cut_enrollment();
    $current_period = $current_period_cut['code'];
    $current_cut = $current_period_cut['cut'];

    // verifica si el studiante tiene pagos pendientes
    $pending_payments = get_payments($student->id) == 2 ? true : false;

    // filtra solo las materia regulares de la projeccion
    $projection_data = []; 
    foreach ( $projection as $subject ) {
        if ($subject['type'] == 'regular') {
            $projection_data[$subject['subject_id']] = $subject;
        }
    }

    // genra la matrix esperada del estudiante
    $accumulated_hc = 0;
    $matrix = [];
    foreach( $matrix_config as $key => $matrix_config_data ) {

        // Obtener datos del período que deberia o debio cursar la asignatura
        $period_index = $key - 1;
        $period_data = ( $period_index < count($future_periods) ) ? $future_periods[$period_index] : null;
        
        $registered_hc = 0; // registra la cantidad de HC()

        // Convertimos los valores a enteros para una comparación numérica limpia
        $data_code = (int)$period_data->code;
        $curr_code = (int)$current_period;

        // puede mejorar si consulta si alguna de las materias 
        // que esta en la lista de asignaturas pendientes se 
        // encuentra aprobada y alli inserta  
        
        foreach ( $projection_data as $subject ) {
                
            if ( 
                ( $subject['is_completed'] === true || $subject['this_cut'] === true ) && 
                $subject['code_period'] === $period_data->code && 
                $subject['cut'] === $period_data->cut
            ) {

                $status = $subject['is_completed'] ? 'aprobada' : 'activa';
                    
                $matrix[$key][] = [
                    'subject' => $subject['subject'],
                    'subject_id' => (int) $subject['subject_id'],
                    'code_period' => $period_data->code,
                    'cut' => $period_data->cut,
                    'type' => 'R',
                    'status' => $status
                ];
                    
                $subject_hc = (int) $subject['hc'];
                $registered_hc += $subject_hc;
                $accumulated_hc += $subject_hc;
                    
                // elimina la asignatura de la lista de asignaturas que estan pendientes
                $subjects = array_filter($subjects, function($item) use ( $subject ) {
                    return $item->subject_id !== (int) $subject['subject_id'];
                });
            }
        }

        // valida que el periodo y corte corresppndiente en $period_data es menor que el actual
        if ( ( $data_code === $curr_code && $period_data->cut >= $current_cut ) || $data_code > $curr_code ) {
            
            foreach ( $subjects as $i => $subject ) {

                if( 
                    $accumulated_hc < $matrix_config_data->max_HC_student && 
                    $accumulated_hc <= $matrix_config_data->term_HC && 
                    $registered_hc < $matrix_config_data->max_HC
                ) { 

                    $subject_projection = $projection_data[$subject->subject_id] ?? null;
                    if ( !isset( $subject->status ) && $subject_projection && $subject_projection['attempts_count'] > $subject_projection['assigned_slots']) {
                        
                        $subject_move = $subjects[$i];
                        unset($subjects[$i]);

                        $subject_move->status = 'blocked'; // si es array usa ['status']
                        $subjects[] = $subject_move;

                        continue;
                    }

                    $status = $subject->status ?? ($pending_payments ? 'blocked' : 'pendiente');

                    // inscribe materia
                    $matrix[$key][] = [
                        'subject' => $subject->subject,
                        'subject_id' => $subject->subject_id,
                        'code_period' => $period_data ? $period_data->code : '', 
                        'cut' => $period_data ? $period_data->cut : '', 
                        'type' => 'R',
                        'status' => $status
                    ];

                    $subject_hc = $subject->hc;
                    $registered_hc += $subject_hc;
                    $accumulated_hc += $subject_hc;

                    unset($subjects[$i]);

                } 
            }
        }
  
    }

    // obtienen todos los registros de la matriz esperada anterior
    $expected_matrix = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM $table_student_expected_matrix WHERE student_id = %d",
        $student->id
    ));

    //inserta los registros de la matriz esperada
    $seq = 1;
    foreach( $matrix AS $term => $subjects ) { 
        foreach ( $subjects AS $subject ) {

            $subjet_id = (int) $subject['subject_id'];

            // data a actualizar o insertar
            $data = [
                'term_index'          => $seq,
                'term_position'       => $term,
                'academic_period'     => $subject['code_period'],
                'academic_period_cut' => $subject['cut'],
                'status'              => $subject['status'],
            ];

            // Extraemos todos los subject_id en un array simple y buscamos el valor
            $index = array_search($subjet_id, array_column($expected_matrix, 'subject_id'));
            if ( $index !== false ) {

                $expectation_record = $expected_matrix[$index];

                // Ejecutamos la actualización
                $wpdb->update( $table_student_expected_matrix, $data,['id' => (int) $expected_matrix[$index]->id] );
                
                // eliminamos el registro que se actualizo
                unset($expected_matrix[$index]);
                $expected_matrix = array_values($expected_matrix);

            } else {

                $data['student_id'] = $student->id;
                $data['subject_id'] = $subject['subject_id'];

                $wpdb->insert($table_student_expected_matrix, $data);
            }
            $seq++;
        }
    }

    //elimina los registros de la matriz esperada que ya no se utilizan
    foreach ( $expected_matrix as $record ) {
        $wpdb->delete( $table_student_expected_matrix, ['id' => $record->id] );
    }

}

/* function build_detailed_matrix($terms_config, $terms_available, $matrix_regular, $student_id)
{
    global $wpdb;

    if (empty($terms_config) || empty($matrix_regular)) {
        return [];
    }

    $detailed_matrix = [];
    $subject_index = 0;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

    // Obtener detalles del estudiante y fecha de creación
    $student = get_student_detail($student_id);
    // Verificar si $student es válido
    if (!$student) {
        // En un escenario real, deberías decidir qué hacer si el estudiante no existe.
        return [];
    }
    $period = get_period_cut_details_code($student->academic_period, $student->initial_cut);
    $registration_date = new DateTime($period->start_date);

    // Consulta optimizada y segura (usando $wpdb->prepare si se pudiera, pero aquí el dato es seguro ya que se formatea)
    $future_periods = $wpdb->get_results(
        "SELECT DISTINCT code, cut FROM {$table_academic_periods_cut} 
         WHERE start_date >= '" . $registration_date->format('Y-m-d') . "' ORDER BY start_date ASC LIMIT 20"
    );

    // Obtener inscripciones del estudiante y clasificar por estado
    $inscriptions = get_inscriptions_by_student($student_id);
    $completed_subjects = [];
    $enrolled_subjects = [];

    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->status_id == 3) {
                $completed_subjects[] = $inscription->subject_id;
            } elseif ($inscription->status_id == 1) {
                // Aunque no se usan directamente en el loop principal, se mantienen por si se necesitan.
                $enrolled_subjects[] = $inscription->subject_id; 
            }
        }
    }

    $period_index = 0;
    for ($i = 0; $i < $terms_available; $i++) {
        $term_number = $i + 1;
        $term_type = $terms_config[$term_number] ?? 'N/A';

        // Obtener datos del período futuro
        $period_data = ($period_index < count($future_periods)) ? $future_periods[$period_index] : null;

        if ($term_type === 'RR') {
            // Este es un período que contiene 2 asignaturas
            $subjects_to_process = 2;
            $new_entries = [];

            for ($j = 0; $j < $subjects_to_process; $j++) {
                if ($subject_index < count($matrix_regular)) {
                    $subject = $matrix_regular[$subject_index];
                    $is_completed = in_array($subject->subject_id, $completed_subjects);

                    $entry = [
                        'cut' => $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : ''),
                        'type' => 'R', // Se considera 'R' (regular) a nivel de asignatura
                        'subject_id' => $subject->subject_id,
                        'code_period' => $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : ''),
                        'completed' => $is_completed
                    ];
                    $new_entries[] = $entry;
                    $subject_index++; // Avanzar al siguiente sujeto
                } else {
                    // Rellenar con datos vacíos si no hay más asignaturas en $matrix_regular
                    $new_entries[] = [
                        'cut' => ($period_data ? $period_data->cut : ''), 
                        'type' => 'R', 
                        'subject_id' => '', 
                        'code_period' => ($period_data ? $period_data->code : ''), 
                        'completed' => false
                    ];
                }
            }
            
            // Si el período fue usado, avanzar el índice del período
            if ($period_data) {
                $period_index++; 
            }
            
            // Agregar las dos entradas individuales a la matriz detallada
            $detailed_matrix = array_merge($detailed_matrix, $new_entries);

        } elseif ($term_type === 'R') {
            // Período que contiene 1 asignatura
            $term_entry = [];
            if ($subject_index < count($matrix_regular)) {
                $subject = $matrix_regular[$subject_index];
                $is_completed = in_array($subject->subject_id, $completed_subjects);

                $term_entry = [
                    'cut' => $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : ''),
                    'type' => 'R',
                    'subject_id' => $subject->subject_id,
                    'code_period' => $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : ''),
                    'completed' => $is_completed
                ];
                $subject_index++; // Avanzar al siguiente sujeto
            } else {
                // Rellenar con datos vacíos si no hay más asignaturas
                $term_entry = [
                    'cut' => ($period_data ? $period_data->cut : ''), 
                    'type' => 'R', 
                    'subject_id' => '', 
                    'code_period' => ($period_data ? $period_data->code : ''), 
                    'completed' => false
                ];
            }
            
            if ($period_data) {
                $period_index++;
            }
            
            // Agregar la entrada individual a la matriz
            if ($term_entry) {
                $detailed_matrix[] = $term_entry;
            }

        } else {
            // Tipo de término no reconocido ('N/A'). Consume un período pero no una asignatura regular.
            $term_entry = [
                'cut' => ($period_data ? $period_data->cut : ''), 
                'type' => 'N/A', 
                'subject_id' => '', 
                'code_period' => ($period_data ? $period_data->code : ''), 
                'completed' => false
            ];
            
            if ($period_data) {
                $period_index++;
            }

            $detailed_matrix[] = $term_entry;
        }
    }

    return $detailed_matrix;
} */

function update_projection_after_enrollment($student_id, $subject_id, $code_period, $cut_period, $status_id = 1)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';

    // Normalize subject details
    $subject = get_subject_details($subject_id);
    if (!$subject) {
        return false;
    }

    $proj_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_student_academic_projection} WHERE student_id = %d", $student_id));
    $projection_arr = [];
    if ($proj_row && !empty($proj_row->projection)) {
        $projection_arr = json_decode($proj_row->projection, true);
        if (!is_array($projection_arr)) {
            $projection_arr = [];
        }
    }

    $found = false;
    $updated_item = null;
    foreach ($projection_arr as $idx => $item) {
        $item_subject_id = isset($item['subject_id']) ? (string)$item['subject_id'] : null;
        $item_code = isset($item['code_subject']) ? $item['code_subject'] : null;

        if (($item_subject_id !== null && (string)$item_subject_id === (string)$subject->id) || ($item_code !== null && $item_code === $subject->code_subject)) {
            // Update existing entry
            $projection_arr[$idx]['this_cut'] = ($status_id == 1);
            $projection_arr[$idx]['code_period'] = $code_period;
            $projection_arr[$idx]['cut'] = $cut_period;
            $projection_arr[$idx]['calification'] = ($status_id == 3) ? ($projection_arr[$idx]['calification'] ?? '') : '';
            $projection_arr[$idx]['is_completed'] = ($status_id == 3 || $status_id == 1);
            $projection_arr[$idx]['welcome_email'] = ($status_id == 3 || $status_id == 1) ? true : false;
            $projection_arr[$idx]['subject_id'] = (string)$subject->id;
            $projection_arr[$idx]['code_subject'] = $subject->code_subject;
            $projection_arr[$idx]['subject'] = $subject->name;
            $projection_arr[$idx]['type'] = isset($projection_arr[$idx]['type']) ? $projection_arr[$idx]['type'] : strtolower($subject->type);
            $projection_arr[$idx]['hc'] = $projection_arr[$idx]['hc'] ?? ($subject->hc ?? '');

            $found = true;
            $updated_item = $projection_arr[$idx];
            break;
        }
    }

    if (!$found) {
        // Append a minimal projection entry for this subject
        $projection_arr[] = [
            'hc' => $subject->hc ?? '',
            'cut' => ($status_id == 3 || $status_id == 1) ? $cut_period : '',
            'type' => strtolower($subject->type ?? ''),
            'subject' => $subject->name,
            'this_cut' => ($status_id == 1),
            'subject_id' => (string)$subject->id,
            'code_period' => ($status_id == 3 || $status_id == 1) ? $code_period : '',
            'calification' => ($status_id == 3) ? '' : '',
            'code_subject' => $subject->code_subject,
            'is_completed' => ($status_id == 3 || $status_id == 1),
            'welcome_email' => ($status_id == 3 || $status_id == 1) ? true : false
        ];
        $updated_item = $projection_arr[count($projection_arr) - 1];
    }

    if ($proj_row) {
        $wpdb->update($table_student_academic_projection, ['projection' => json_encode($projection_arr)], ['id' => $proj_row->id]);
        $proj_id = $proj_row->id;
    } else {
        $wpdb->insert($table_student_academic_projection, ['student_id' => $student_id, 'projection' => json_encode($projection_arr), 'matrix' => null]);
        $proj_id = $wpdb->insert_id;
    }

    // Return the item that was created/updated plus the projection row id
    if ($updated_item !== null) {
        $updated_item['_projection_id'] = $proj_id;
        return $updated_item;
    }

    return false;
}

function update_expected_matrix_after_enrollment($student_id, $subject_id, $code_period, $cut_period)
{
    global $wpdb;
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    if (empty($student_id) || empty($subject_id)) {
        return false;
    }

    // Fetch inscriptions that exactly match student, subject and period/cut
    $inscriptions = $wpdb->get_results($wpdb->prepare(
        "SELECT status_id FROM {$table_student_period_inscriptions} WHERE student_id = %d AND subject_id = %d AND code_period = %s AND cut_period = %s",
        $student_id,
        $subject_id,
        $code_period,
        $cut_period
    ));

    $status = 'pendiente';
    if (!empty($inscriptions)) {
        $has_in_progress = false;
        $has_approved = false;
        foreach ($inscriptions as $ins) {
            $s = (int) $ins->status_id;
            if ($s === 1) {
                $has_in_progress = true;
                break; // in progress wins
            }
            if ($s === 3) {
                $has_approved = true;
            }
        }

        if ($has_in_progress) {
            $status = 'en curso';
        } elseif ($has_approved) {
            $status = 'aprobada';
        }
    }

    // Update existing expected row if present
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE student_id = %d AND subject_id = %d AND academic_period = %s AND academic_period_cut = %s",
        $student_id,
        $subject_id,
        $code_period,
        $cut_period
    ));

    if ($existing) {
        $updated = $wpdb->update($table_student_expected_matrix, ['status' => $status], ['id' => $existing->id]);
        if ($updated === false) {
            return false;
        }
        $existing->status = $status;
        return (array) $existing;
    }

    // // Insert minimal expected row when missing
    // $inserted = $wpdb->insert($table_student_expected_matrix, [
    //     'student_id' => $student_id,
    //     'term_index' => null,
    //     'term_position' => null,
    //     'subject_id' => $subject_id,
    //     'academic_period' => $code_period,
    //     'academic_period_cut' => $cut_period,
    //     'status' => $status
    // ]);

    // if ($inserted === false) {
    //     return false;
    // }

    // $new_id = $wpdb->insert_id;
    // $new_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_student_expected_matrix} WHERE id = %d", $new_id));
    // return $new_row ? (array) $new_row : false;
    return false;
}

function send_welcome_subjects($student_id, $force = false)
{
    global $wpdb;
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_students = $wpdb->prefix . 'students';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id={$student_id}");
    $academic_ready = get_academic_ready($student_id);
    if (!$projection || $academic_ready) {
        return;
    }

    $projection_obj = json_decode($projection->projection);
    $load = load_current_cut();
    $cut = $load['cut'];

    $subjectsPendingWelcome = array_filter($projection_obj, function ($item) {
        return $item->this_cut == true && $item->welcome_email == false;
    });
    $subjectsPendingWelcome = array_values($subjectsPendingWelcome);

    $filteredArray = array_filter($projection_obj, function ($item) {
        return $item->this_cut == true;
    });
    $filteredArray = array_values($filteredArray);

    $text = '';
    if (count($subjectsPendingWelcome) > 0) {
        $text = template_welcome_subjects($filteredArray, $student);
    } else {
        if ($force) {
            if (count($filteredArray) == 0) {
                if ($student->elective) {
                    $text = template_welcome_subjects($filteredArray, $student);
                } else {
                    if ($student->initial_cut != $cut) {
                        $text = template_not_enrolled($student);
                    }
                }
            }
        } else {
            if ((!get_option('send_welcome_email_ready') || empty(get_option('send_welcome_email_ready')))) {
                if (count($filteredArray) == 0) {
                    if ($student->elective) {
                        $text = template_welcome_subjects($filteredArray, $student);
                    } else {
                        if ($student->initial_cut != $cut) {
                            $text = template_not_enrolled($student);
                        }
                    }
                }
            }
        }
    }

    if (empty($text)) {
        return;
    }

    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
    $email_student->trigger($student, 'Welcome', $text);

    $user_parent = get_user_by('id', $student->partner_id);
    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
    $email_student->trigger($user_parent, 'Welcome', $text);

    if (count($filteredArray) > 0) {
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $subjectIds = array_column($projection_obj, 'code_subject');
            $indexToEdit = array_search($subject->code_subject, $subjectIds);
            if ($indexToEdit !== false) {
                $projection_obj[$indexToEdit]->welcome_email = true;
            }
        }

        $wpdb->update($table_student_academic_projection, [
            'projection' => json_encode($projection_obj) // Ajusta el valor de 'projection' según sea necesario
        ], ['id' => $projection->id]);
    }
}

function template_welcome_subjects($filteredArray, $student)
{
    global $wpdb;
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $load = load_current_cut_enrollment();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $period_cut = $wpdb->get_row("SELECT * FROM {$table_academic_periods_cut} WHERE code = '{$academic_period}' && cut = '{$cut}'");
    $date_start = DateTime::createFromFormat('Y-m-d', $period_cut->start_date);
    $date_end = DateTime::createFromFormat('Y-m-d', $period_cut->end_date);
    $start_date = $date_start->format('l, F j, Y');
    $end_date = $date_end->format('l, F j, Y');
    $text = '';

    /// biegin Ingles
    $text .= '<div>';
    $text .= 'Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', on behalf of the academic team of American Elite School, based in the city of Doral, Florida-USA, we are pleased to announce the beginning of Period ' . $cut . ', corresponding to the School Year ' . substr($academic_period, 0, 4) . '-' . substr($academic_period, 4);
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= '<div><strong>START DATE:</strong> ' . $start_date . ' </div>';
    $text .= '<div><strong>END DATE:</strong> ' . $end_date . ' </div>';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div> Listed below is your <strong>Academic Load</strong> of mandatory courses registered for this Period ' . $cut . ': </div>';

    if (count($filteredArray) > 0) {
        $text .= '<table style="margin: 20px 0px; border-collapse: collapse; width: 100%;">';
        $text .= '<thead>
             <tr>
                 <th colspan="4" style="border: 1px solid gray;">
                     <strong>COURSE CODE</strong>
                 </th>
                 <th colspan="8" style="border: 1px solid gray;">
                     <strong>SUBJECT</strong>
                 </th>
             </tr>
         </thead>';
        $text .= '<tbody>';
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $text .= '<tr>';
            $text .= '<td colspan="4" style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
            $text .= '<td colspan="8" style="border: 1px solid gray;">' . $subject->name . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_start->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_end->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $cut . '</td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
    }

    $text .= '<br>';

    if ($student->elective) {
        $text .= '<div>';
        $text .= '<strong>ELECTIVE ACCORDING TO YOUR SELECTION</strong>';
        $text .= '</div>';
        $text .= '<br>';
    }

    $text .= '<div> We leave at your disposal links and contacts of interest: </div>';

    $text .= '<ul>';
    $text .= '<li>Website: <a href="https://americanelite.school/" target="_blank">https://americanelite.school/</a></li>';
    $text .= '<li>Virtual classroom: <a href="https://portal.americanelite.school/my-account" target="_blank">https://portal.americanelite.school/my-account</a></li>';
    $text .= '<li>Support: <a href="https://support.americanelite.school" target="_blank">https://support.americanelite.school</a></li>';
    $text .= '</ul>';

    $text .= '<div>On behalf of our institution, we thank you for your commitment and wish you a successful academic term.</div>';

    // End en

    // Begin Divider
    $text .= '<div style="margin: 10px 0px; border-bottom: 1px solid gray;"></div>';
    //End Divider

    // Begin Es
    $text .= '<div>';
    $text .= 'Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos complace anunciarle el inicio del Periodo ' . $cut . ', correspondiente al Año Escolar ' . substr($academic_period, 0, 4) . '-' . substr($academic_period, 4);
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= '<div><strong>FECHA DE INICIO:</strong> ' . translateDateToSpanish(dateString: $start_date) . ' </div>';
    $text .= '<div><strong>FECHA DE CULMINACIÓN:</strong> ' . translateDateToSpanish($end_date) . ' </div>';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div> A continuación, detallamos su <strong>Carga Académica</strong> de cursos ofertados para este periodo ' . $cut . ': </div>';

    if (count($filteredArray) > 0) {
        $text .= '<table style="margin: 20px 0px; border-collapse: collapse; width: 100%;">';
        $text .= '<thead>
         <tr>
             <th colspan="4" style="border: 1px solid gray;">
                <strong>CÓDIGO</strong>
             </th>
             <th colspan="8" style="border: 1px solid gray;">
                 <strong>MATERIA</strong>
             </th>
         </tr>
     </thead>';
        $text .= '<tbody>';
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $text .= '<tr>';
            $text .= '<td colspan="4" style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
            $text .= '<td colspan="8" style="border: 1px solid gray;">' . $subject->name . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_start->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_end->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $cut . '</td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
    }

    $text .= '<br>';

    if ($student->elective) {
        $text .= '<div>';
        $text .= '<strong>ELECTIVA CONFORME A SU ELECCIÓN</strong>';
        $text .= '</div>';
        $text .= '<br>';
    }

    $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

    $text .= '<ul>';
    $text .= '<li>Página web: <a href="https://americanelite.school/" target="_blank">https://americanelite.school/</a></li>';
    $text .= '<li>Aula virtual: <a href="https://portal.americanelite.school/" target="_blank">https://portal.americanelite.school/</a></li>';
    $text .= '<li>Soporte: <a href="https://support.americanelite.school" target="_blank">https://support.americanelite.school</a></li>';
    $text .= '</ul>';
    $text .= '<div>En nombre de nuestra institución, le agradecemos por su compromiso y le deseamos un periodo académico lleno de logros satisfactorios.</div>';

    // End es



    return $text;
}

function translateDateToSpanish($dateString)
{
    $days = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    $months = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];

    // Reemplazar días y meses en el string
    $dateString = str_replace(array_keys($days), array_values($days), $dateString);
    $dateString = str_replace(array_keys($months), array_values($months), $dateString);

    return $dateString;
}

function template_not_enrolled($student)
{
    global $wpdb;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $load = load_current_cut_enrollment();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $period_cut = $wpdb->get_row("SELECT * FROM {$table_academic_periods_cut} WHERE code = '{$academic_period}' && cut = '{$cut}'");
    $date_start = DateTime::createFromFormat('Y-m-d', $period_cut->start_date);
    $date_end = DateTime::createFromFormat('Y-m-d', $period_cut->end_date);
    $start_date = $date_start->format('l, F j, Y');
    $end_date = $date_end->format('l, F j, Y');
    $text = '';

    $text .= '<div>';
    $text .= 'Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', on behalf of the academic team at American Elite School, located in Doral, Florida, USA, we would like to inform you that, during Period ' . $cut . ' of the ' . $academic_period . ' school year, no academic load will be assigned to you, as you currently have the academic progress corresponding to your year of admission.';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'Since Period ' . $cut . ' starts on ' . $start_date . ' and ends on ' . $end_date . ', we invite you to stay alert to your emails where you will be notified of the academic load for the following period.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div> We leave at your disposal links and contacts of interest: </div>';

    $text .= '<ul>';
    -$text .= '<li>Website: <a href="https://americanelite.school/" target="_blank">https://americanelite.school/</a></li>';
    $text .= '<li>Virtual classroom: <a href="https://portal.americanelite.school/my-account" target="_blank">https://portal.americanelite.school/my-account</a></li>';
    $text .= '<li>Support: <a href="https://support.americanelite.school" target="_blank">https://support.americanelite.school</a></li>';
    $text .= '</ul>';
    $text .= '<div>On behalf of our institution, we thank you for your commitment and wish you a pleasant rest during this period.</div>';

    $text .= '<div style="margin: 10px 0px; border-bottom: 1px solid gray;"></div>';

    $text .= '<div>';
    $text .= 'Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos permitimos anunciarle que, durante el Periodo ' . $cut . ' correspondiente al Año Escolar ' . $academic_period . ' no le será asignada carga académica ya que cuenta actualmente con el avance académico que corresponde a su año de ingreso.';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'Dado que el periodo académico ' . $cut . ' tiene fecha de inicio el ' . translateDateToSpanish(dateString: $start_date) . ' y culmina el ' . translateDateToSpanish($end_date) . ', los invitamos a estar atentos a sus correos electrónicos donde serán notificados con la carga académica correspondiente al periodo siguiente.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

    $text .= '<ul>';
    $text .= '<li>Página web: <a href="https://americanelite.school/" target="_blank">https://americanelite.school/</a></li>';
    $text .= '<li>Aula virtual: <a href="https://portal.americanelite.school/" target="_blank">https://portal.americanelite.school/</a></li>';
    $text .= '<li>Soporte: <a href="https://support.americanelite.school" target="_blank">https://support.americanelite.school</a></li>';
    $text .= '</ul>';
    $text .= '<div>En nombre de nuestra institución, le agradecemos por su compromiso y le deseamos un feliz descanso durante este periodo.</div>';

    return $text;
}

function get_subject_cut($student_id, $subject_id)
{
    $inscriptions = get_inscriptions_by_student($student_id);
    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->subject_id == $subject_id && ($inscription->status_id == 3 || $inscription->status_id == 1)) {
                return $inscription->cut_period;
            }
        }
    }
    return '';
}

function get_subject_period($student_id, $subject_id)
{
    $inscriptions = get_inscriptions_by_student($student_id);
    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->subject_id == $subject_id && ($inscription->status_id == 3 || $inscription->status_id == 1)) {
                return $inscription->code_period;
            }
        }
    }
    return '';
}

function persist_expected_matrix($student_id, $detailed_matrix)
{
    global $wpdb;
    $table = $wpdb->prefix . 'student_expected_matrix';
    if (empty($detailed_matrix) || empty($student_id)) {
        return 0;
    }

    // Eliminamos registros existentes para este student_id antes de insertar los nuevos
    $wpdb->delete($table, ['student_id' => $student_id]);

    // Primero aplanamos la matriz detallada para obtener la posición real
    $flat = [];
    foreach ($detailed_matrix as $idx => $term_entry) {
        $term_index = intval($idx) + 1;

        if (isset($term_entry['subject_id']) && is_array($term_entry['subject_id'])) {
            $subject_ids = $term_entry['subject_id'];

            if (is_array($term_entry['cut'])) {
                $cuts = $term_entry['cut'];
            } else {
                $cuts = array_fill(0, count($subject_ids), isset($term_entry['cut']) ? $term_entry['cut'] : '');
            }

            if (is_array($term_entry['code_period'])) {
                $codes = $term_entry['code_period'];
            } else {
                $codes = array_fill(0, count($subject_ids), isset($term_entry['code_period']) ? $term_entry['code_period'] : '');
            }

            foreach ($subject_ids as $k => $subject_val) {
                $flat[] = [
                    'term_index' => $term_index,
                    'subject_id' => is_numeric($subject_val) ? intval($subject_val) : null,
                    'academic_period' => $codes[$k] ?? '',
                    'academic_period_cut' => $cuts[$k] ?? ''
                ];
            }
        } else {
            $subject_val = $term_entry['subject_id'] ?? null;
            $flat[] = [
                'term_index' => $term_index,
                'subject_id' => is_numeric($subject_val) ? intval($subject_val) : null,
                'academic_period' => $term_entry['code_period'] ?? '',
                'academic_period_cut' => $term_entry['cut'] ?? ''
            ];
        }
    }

    // Insertar filas basadas en la matriz aplanada.
    // term_position = posición en la matriz aplanada (1-based).
    // term_index = secuencia incremental por registro insertado (1..N) para mantener el orden de inserción.
    $inserted = 0;
    $seq = 1;
    
    // Cargar períodos futuros desde el período de inscripción actual para asignación secuencial
    $current_load = load_current_cut_enrollment();
    $current_code = $current_load['code'];
    $current_cut = $current_load['cut'];
    
    // Obtener el período actual para obtener su start_date
    $current_period_details = get_period_cut_details_code($current_code, $current_cut);
    $future_periods = [];
    $future_period_index = 0;
    
    if ($current_period_details && !empty($current_period_details->start_date)) {
        global $wpdb;
        $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
        $current_start_date = $current_period_details->start_date;
        
        // Obtener todos los períodos futuros (incluyendo el actual) ordenados
        $future_periods = $wpdb->get_results(
            "SELECT code, cut, end_date FROM {$table_academic_periods_cut} 
             WHERE start_date >= '{$current_start_date}' 
             ORDER BY start_date ASC LIMIT 50"
        );
    }
    
    foreach ($flat as $pos => &$entry) {
        // Omitir registros sin subject_id (subject_id === null)
        if ($entry['subject_id'] === null) {
            continue;
        }

        // Determinar el status por defecto y consultar inscripciones para ajustar si aplica
        $status_text = 'pendiente';

        // First check if there is a current inscription (in course)
        $current_ins = get_inscriptions_by_student_automatically_enrollment($entry['subject_id'], 'current', $student_id);
        if ($current_ins && is_array($current_ins) && count($current_ins) > 0) {
            $status_text = 'en curso';
            $entry['academic_period'] = $current_ins[0]->code_period;
            $entry['academic_period_cut'] = $current_ins[0]->cut_period;
        } else {
            // Check historical inscriptions (approved/reproved)
            $history_ins = get_inscriptions_by_student_automatically_enrollment($entry['subject_id'], 'history', $student_id);
            if ($history_ins && is_array($history_ins) && count($history_ins) > 0) {
                $found_approved = false;
                $found_failed = false;
                foreach ($history_ins as $h) {
                    if (isset($h->status_id)) {
                        if (intval($h->status_id) === 3) { // Aprobada
                            $found_approved = true;
                            $status_text = 'aprobada';
                            // Modificamos el academic_period y el academic_period_cut del entry con el del history_ins.
                            $entry['academic_period'] = $h->code_period;
                            $entry['academic_period_cut'] = $h->cut_period;
                            // Si encuentra 1 inscripcion aprobada, automaticamente se sale y ya esta.
                            break;
                        } elseif (intval($h->status_id) === 4) { // Reprobada
                            $found_failed = true;
                        }
                    }
                }

                // Si no se encontró ninguna aprobada, se verifica si hay reprobada (para establecer el estado)
                if (!$found_approved && $found_failed) {
                    $status_text = 'reprobada';
                }
            }
        }

        // Si el status es 'pendiente', verificar si el período ya pasó y asignar secuencialmente
        $final_academic_period = $entry['academic_period'];
        $final_academic_period_cut = $entry['academic_period_cut'];
        
        if ($status_text === 'pendiente' && !empty($entry['academic_period']) && !empty($entry['academic_period_cut'])) {
            // Obtener detalles del período para verificar si ya pasó
            $period_details = get_period_cut_details_code($entry['academic_period'], $entry['academic_period_cut']);
            
            if ($period_details && !empty($period_details->end_date)) {
                $end_date = new DateTime($period_details->end_date);
                $today = new DateTime('now');
                
                // Si el end_date ya pasó (es anterior a hoy), asignar el siguiente período futuro disponible
                if ($end_date < $today) {
                    if ($future_period_index < count($future_periods)) {
                        $next_period = $future_periods[$future_period_index];
                        $final_academic_period = $next_period->code;
                        $final_academic_period_cut = $next_period->cut;
                        $future_period_index++; // Avanzar al siguiente período para el próximo pendiente viejo
                    }
                }
            }
        }

        $wpdb->insert($table, [
            'student_id' => $student_id,
            'term_index' => $seq,
            'term_position' => intval($pos) + 1,
            'subject_id' => $entry['subject_id'],
            'academic_period' => $final_academic_period,
            'academic_period_cut' => $final_academic_period_cut,
            'status' => $status_text,
            'created_at' => current_time('mysql')
        ]);
        $inserted++;
        $seq++;
    }

    return $inserted;
}

function clear_expected_matrix_for_student($student_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'student_expected_matrix';
    return $wpdb->delete($table, ['student_id' => $student_id]);
}

function get_expected_matrix_by_student($student_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'student_expected_matrix';

    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE student_id=%d ORDER BY term_index ASC, id ASC", $student_id));

    $matrix = [];
    foreach ($rows as $row) {
        $idx = max(0, intval($row->term_index) - 1);
        if (!isset($matrix[$idx])) {
            $matrix[$idx] = [
                'type' => 'R',
                'subject_id' => [],
                'cut' => [],
                'code_period' => [],
                'completed' => [],
                'status' => []
            ];
        }

        // Resolve subject_id: prefer numeric subject_id stored, otherwise try to find by subject code or name
        $resolved_subject_id = null;
        if (!empty($row->subject_id) && is_numeric($row->subject_id)) {
            $resolved_subject_id = intval($row->subject_id);
        } else {
            $candidate = $row->subject ?? $row->subject_code ?? '';
            if (!empty($candidate)) {
                if (is_numeric($candidate)) {
                    $resolved_subject_id = intval($candidate);
                } else {
                    // Try to lookup by code_subject or name in school_subjects
                    $table_school_subjects = $wpdb->prefix . 'school_subjects';
                    $maybe_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table_school_subjects} WHERE code_subject = %s OR name = %s LIMIT 1", $candidate, $candidate));
                    if (!empty($maybe_id)) {
                        $resolved_subject_id = intval($maybe_id);
                    }
                }
            }
        }

        $matrix[$idx]['subject_id'][] = $resolved_subject_id !== null ? $resolved_subject_id : null;
        $matrix[$idx]['cut'][] = $row->academic_period_cut;
        $matrix[$idx]['code_period'][] = $row->academic_period;
        $matrix[$idx]['completed'][] = false;
        $matrix[$idx]['status'][] = !empty($row->status) ? $row->status : 'pendiente';
    }

    return $matrix;
}
