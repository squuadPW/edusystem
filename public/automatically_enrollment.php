<?php

function automatically_enrollment($student_id)
{
    global $wpdb;

    // 1. Cargar el objeto estudiante
    $table_students = $wpdb->prefix . 'students';
    // Se utiliza $wpdb->prepare para una mejor seguridad, aunque el ID es típicamente un entero
    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d", $student_id));

    // Si el estudiante no existe o el objeto es nulo, salir inmediatamente
    if (!$student) {
        return;
    }

    // 2. Ejecutar la lógica de inscripción
    if ($student->status_id == 0 || $student->status_id > 3) {
        return;
    }

    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_student_expected_matrix = $wpdb->prefix . 'student_expected_matrix';
    $full_name_student = student_names_lastnames_helper($student->id);
    // Se asegura de que $user sea un objeto para evitar errores si no se encuentra
    $user = get_user_by('email', $student->email);
    // Usar el ID de usuario si existe, de lo contrario usar 0 o un valor predeterminado
    $user_id = $user ? $user->ID : 0; 

    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $expected_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_student_expected_matrix} WHERE student_id = %d AND academic_period = %s AND academic_period_cut = %s ORDER BY term_index ASC, term_position ASC",
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
            if (!$available_inscription_subject) {
                // Ya la esta viendo
                edusystem_get_log('The student ' . $full_name_student . ' already has an active or approved enrollment for the subject ' . $subject->name, 'Automatically enrollment', $user_id);
                continue;
            }

            // Verificar oferta disponible en este periodo
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if (!$offer_available_to_enroll) {
                // Si no hay oferta, no inscribimos esta materia
                edusystem_get_log('No offer available for subject ' . $subject->name . ' for student ' . $full_name_student . ' in the period ' . $code . ' - ' . $cut, 'Automatically enrollment', $user_id);
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
            edusystem_get_log('Enrolled in subject ' . $subject->name . ' for student: ' . $full_name_student, 'Automatically enrollment', $user_id);
            edusystem_get_log('Projection updated for subject ' . $subject->name . ' for student: ' . $full_name_student, 'Automatically enrollment', $user_id);

            // mandamos a moodle
            $offer = get_offer_filtered($subject->id, $code, $cut, $section);
            if ($offer && isset($offer->moodle_course_id)) {
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

function generate_projection_student($student_id, $force = false)
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

    // Obtener información del estudiante incluyendo expected_graduation_date y created_at
    $student = $wpdb->get_row($wpdb->prepare(
        "SELECT id, expected_graduation_date, created_at FROM {$table_students} WHERE id = %d",
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

            // Crear rango desde created_at hasta expected_graduation_date
            $registration_date = new DateTime($student->created_at);

            // Contar períodos académicos únicos en ese rango
            $periods_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) 
                 FROM {$table_academic_periods_cut} 
                 WHERE start_date >= %s AND end_date <= %s",
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

    // Crear índice de inscripciones por código para búsqueda más rápida
    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            $subject = $inscription->subject_id && $inscription->subject_id != '' ? get_subject_details($inscription->subject_id) : get_subject_details_code($inscription->code_subject);

            if ($subject->type === 'elective') {
                $elective_inscriptions[] = $inscription;
            } else {
                $inscriptions_by_code[$inscription->code_subject] = $inscription;
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
        $status_id = $inscription ? $inscription->status_id : null;

        $projection[] = [
            'hc' => isset($m->hc) ? $m->hc : (isset($subject_details->hc) ? $subject_details->hc : ''),
            'cut' => ($status_id == 3 || $status_id == 1) ? $inscription->cut_period : "",
            'type' => isset($m->type) ? strtolower($m->type) : strtolower($subject_details->type),
            'subject' => $subject_details->name,
            'this_cut' => ($status_id == 1),
            'subject_id' => (string) $subject_details->id,
            'code_period' => ($status_id == 3 || $status_id == 1) ? $inscription->code_period : "",
            'calification' => ($status_id == 3) ? $inscription->calification : "",
            'code_subject' => $subject_details->code_subject,
            'is_completed' => ($status_id == 3 || $status_id == 1),
            'welcome_email' => ($status_id == 3 || $status_id == 1)
        ];
    }

    // Agregar materias electivas a la proyección
    foreach ($elective_inscriptions as $inscription) {
        // Solo agregar materias electivas completadas
        if ($inscription->status_id != 3) {
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
                'calification' => $inscription->calification,
                'is_completed' => true,
                'this_cut' => false,
                'welcome_email' => true,
                'type' => 'elective'
            ];
        }
    }

    // Si es forzado, actualizar registros
    if ($force) {
        $wpdb->query('START TRANSACTION');

        try {
            // Actualizar estudiante y eliminar proyecciones existentes en una sola transacción
            $wpdb->update($table_students, ['elective' => 0, 'terms_available' => $terms_available], ['id' => $student_id]);
            $wpdb->delete($table_student_academic_projection, ['student_id' => $student_id]);

            // Insertar nueva proyección (terms_available now stored on student)
            $result = $wpdb->insert($table_student_academic_projection, [
                'student_id' => $student_id,
                'projection' => json_encode($projection),
                'matrix' => $calculated_matrix
            ]);

            if ($result === false) {
                throw new Exception('Error al insertar la proyección');
            }

            // Persist expected matrix rows if available
            if (!empty($detailed_matrix)) {
                clear_expected_matrix_for_student($student_id);
                persist_expected_matrix($student_id, $detailed_matrix);
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
            if (!empty($force)) {
                clear_expected_matrix_for_student($student_id);
            }
            persist_expected_matrix($student_id, $detailed_matrix);
        }

        return true;
    }

    return false;
}

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

    // Insert minimal expected row when missing
    $inserted = $wpdb->insert($table_student_expected_matrix, [
        'student_id' => $student_id,
        'term_index' => null,
        'term_position' => null,
        'subject_id' => $subject_id,
        'academic_period' => $code_period,
        'academic_period_cut' => $cut_period,
        'status' => $status
    ]);

    if ($inserted === false) {
        return false;
    }

    $new_id = $wpdb->insert_id;
    $new_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_student_expected_matrix} WHERE id = %d", $new_id));
    return $new_row ? (array) $new_row : false;
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

function build_detailed_matrix($terms_config, $terms_available, $matrix_regular, $student_id)
{
    global $wpdb;

    if (empty($terms_config) || empty($matrix_regular)) {
        return [];
    }

    $detailed_matrix = [];
    $subject_index = 0;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';

    // Obtener períodos futuros
    $future_periods = $wpdb->get_results(
        "SELECT DISTINCT code, cut FROM {$table_academic_periods_cut} 
         WHERE start_date >= CURDATE() ORDER BY start_date ASC LIMIT 20"
    );

    // Obtener inscripciones del estudiante
    $inscriptions = get_inscriptions_by_student($student_id);
    $completed_subjects = [];
    $enrolled_subjects = [];

    if (!empty($inscriptions)) {
        foreach ($inscriptions as $inscription) {
            if ($inscription->status_id == 3) {
                $completed_subjects[] = $inscription->subject_id;
            } elseif ($inscription->status_id == 1) {
                $enrolled_subjects[] = $inscription->subject_id;
            }
        }
    }

    $period_index = 0;
    for ($i = 0; $i < $terms_available; $i++) {
        $term_number = $i + 1;
        $term_type = $terms_config[$term_number] ?? 'N/A';
        $term_entry = [];
        $period_data = ($period_index < count($future_periods)) ? $future_periods[$period_index++] : null;

        if ($term_type === 'RR') {
            // 2 subjects
            $subjects_data = [];

            // FIX: Using a new counter variable ($j) for the inner loop to avoid modifying the main loop counter ($i).
            for ($j = 0; $j < 2; $j++) {
                if ($subject_index < count($matrix_regular)) {
                    $subject = $matrix_regular[$subject_index];
                    $is_completed = in_array($subject->subject_id, $completed_subjects);

                    $subjects_data[] = [
                        'subject_id' => $subject->subject_id,
                        'cut' => $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : ''),
                        'code_period' => $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : ''),
                        'completed' => $is_completed
                    ];
                    $subject_index++;
                } else {
                    $subjects_data[] = ['subject_id' => '', 'cut' => '', 'code_period' => '', 'completed' => false];
                }
            }

            $term_entry['cut'] = array_column($subjects_data, 'cut');
            $term_entry['type'] = 'R';
            $term_entry['subject_id'] = array_column($subjects_data, 'subject_id');
            $term_entry['code_period'] = array_column($subjects_data, 'code_period');
            $term_entry['completed'] = array_column($subjects_data, 'completed');
        } elseif ($term_type === 'R') {
            // 1 subject
            if ($subject_index < count($matrix_regular)) {
                $subject = $matrix_regular[$subject_index];
                // Check based on 'id' for consistency if $matrix_regular elements for 'R' should also be objects with 'id', but keeping 'subject_id' as in the original code for 'R' block if the structure is different. Assuming the original use of 'subject_id' in this block is correct.
                $is_completed = in_array($subject->subject_id, $completed_subjects);

                $term_entry['cut'] = $is_completed ? get_subject_cut($student_id, $subject->subject_id) : ($period_data ? $period_data->cut : '');
                $term_entry['type'] = 'R';
                $term_entry['subject_id'] = $subject->subject_id;
                $term_entry['code_period'] = $is_completed ? get_subject_period($student_id, $subject->subject_id) : ($period_data ? $period_data->code : '');
                $term_entry['completed'] = $is_completed;
                $subject_index++;
            } else {
                $term_entry = ['cut' => ($period_data ? $period_data->cut : ''), 'type' => 'R', 'subject_id' => '', 'code_period' => ($period_data ? $period_data->code : ''), 'completed' => false];
            }
        } else {
            // Optional: Assign term entry for 'N/A' type if needed.
            $term_entry = ['cut' => ($period_data ? $period_data->cut : ''), 'type' => 'N/A', 'subject_id' => '', 'code_period' => ($period_data ? $period_data->code : ''), 'completed' => false];
        }

        if ($term_entry) {
            $detailed_matrix[] = $term_entry;
        }
    }

    return $detailed_matrix;
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
    foreach ($flat as $pos => $entry) {
        // Omitir registros sin subject_id (subject_id === null)
        if ($entry['subject_id'] === null) {
            continue;
        }

        // Determinar el status por defecto y consultar inscripciones para ajustar si aplica
        $status_text = 'pendiente';

        // First check if there is a current inscription (in course)
        $current_ins = get_inscriptions_by_subject_period($entry['subject_id'], '', $entry['academic_period'], $entry['academic_period_cut'], 'current');
        if ($current_ins && is_array($current_ins) && count($current_ins) > 0) {
            $status_text = 'en curso';
        } else {
            // Check historical inscriptions (approved/reproved)
            $history_ins = get_inscriptions_by_subject_period($entry['subject_id'], '', $entry['academic_period'], $entry['academic_period_cut'], 'history');
            if ($history_ins && is_array($history_ins) && count($history_ins) > 0) {
                // If any history record is status_id == 3 -> aprobada, elseif any == 4 -> reprobada
                $found_approved = false;
                $found_failed = false;
                foreach ($history_ins as $h) {
                    if (isset($h->status_id)) {
                        if (intval($h->status_id) === 3) {
                            $found_approved = true;
                        } elseif (intval($h->status_id) === 4) {
                            $found_failed = true;
                        }
                    }
                }

                if ($found_approved) {
                    $status_text = 'aprobada';
                } elseif ($found_failed) {
                    $status_text = 'reprobada';
                }
            }
        }

        $wpdb->insert($table, [
            'student_id' => $student_id,
            'term_index' => $seq,
            'term_position' => intval($pos) + 1,
            'subject_id' => $entry['subject_id'],
            'academic_period' => $entry['academic_period'],
            'academic_period_cut' => $entry['academic_period_cut'],
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
