<?php

function add_admin_form_academic_projection_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'academic_projection_details') {
            global $wpdb;
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_grades = $wpdb->prefix . 'grades';
            $projection_id = $_GET['projection_id'];
            $projection = get_projection_details($projection_id);
            $student = get_student_detail($projection->student_id);
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id} AND code_subject IS NOT NULL AND code_subject <> '' ORDER BY code_period ASC, cut_period ASC");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $grades = $wpdb->get_results("SELECT * FROM {$table_grades}");
            $load = load_current_cut_enrollment();
            $current_period = $load['code'];
            $current_cut = $load['cut'];
            $download_grades = get_status_approved('CERTIFIED NOTES HIGH SCHOOL', $student->id);

            $load_current_cut = load_current_cut();
            $code_current_cut = $load_current_cut['code'];
            $cut_current_cut = $load_current_cut['cut'];
            if (get_option('send_welcome_email_ready') != $code_current_cut . ' - ' . $cut_current_cut) {
                update_option('send_welcome_email_ready', '');
            }

            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-detail.php');
        }

        if ($_GET['section_tab'] == 'validate_enrollments') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $table_students = $wpdb->prefix . 'students';
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects}");
            $projections_result = [];

            $academic_period = $_POST['academic_period'];
            $academic_period_cut = $_POST['academic_period_cut'];
            if ((isset($academic_period) && !empty($academic_period)) && (isset($academic_period_cut) && !empty($academic_period_cut))) {
                foreach ($subjects as $subject) {
                    $count = 0;
                    $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE code_period = '{$academic_period}' AND cut_period = '{$academic_period_cut}' AND (subject_id = {$subject->id} OR code_subject = '{$subject->code_subject}')");
                    $added_student_ids = array(); // Array para rastrear IDs de estudiantes agregados
                    foreach ($inscriptions as $key => $inscription) {
                        $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$inscription->student_id}");
                        if ($student) {
                            // Verificar si el estudiante ya fue agregado
                            if (!in_array($student->id, $added_student_ids)) {
                                $count++;
                                $added_student_ids[] = $student->id; // Registrar el ID del estudiante
                            }
                        }
                    }

                    // Solo agregar al resultado si hay coincidencias
                    if ($count > 0) {
                        $projections_result[$subject->name] = ['count' => $count, 'subject_id' => $subject->id, 'academic_period' => $academic_period, 'academic_period_cut' => $academic_period_cut]; // Usa el nombre del subject como clave
                    }
                }
            }

            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-validation.php');
        }

        if ($_GET['section_tab'] == 'validate_enrollment_subject') {
            global $wpdb;
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $table_students = $wpdb->prefix . 'students';
            $table_teachers = $wpdb->prefix . 'teachers';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $projections_result = [];
            $students = [];

            $academic_period = $_GET['academic_period'];
            $academic_period_cut = $_GET['academic_period_cut'];
            $subject_id = $_GET['subject_id'];
            $section = $_GET['section'] ?? 1;
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$subject_id}");
            $teacher = null;
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE code_period = '{$academic_period}' AND cut_period = '{$academic_period_cut}' AND (subject_id = {$subject_id} OR code_subject = '{$subject->code_subject}') AND section = {$section}");
            if ((isset($academic_period) && !empty($academic_period)) && (isset($academic_period_cut) && !empty($academic_period_cut))) {
                $added_student_ids = array(); // Array para rastrear IDs de estudiantes agregados
                foreach ($inscriptions as $key => $inscription) {
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$inscription->student_id}");
                    if ($student) {
                        // Verificar si el estudiante ya fue agregado
                        if (!in_array($student->id, $added_student_ids)) {
                            array_push($students, ['student' => $student, 'calification' => $inscription->calification ?? 0]);
                            $added_student_ids[] = $student->id; // Registrar el ID del estudiante
                        }
                    }
                }

                $academic_period_result = $wpdb->get_row("SELECT * FROM {$table_academic_periods} WHERE code = {$academic_period}");
                $offer = get_offer_filtered($subject_id, $academic_period, $academic_period_cut);
                if ($offer) {
                    $teacher = $wpdb->get_row("SELECT * FROM {$table_teachers} WHERE id = {$offer->teacher_id}");
                }
            }

            $projections_result = [
                'students' => $students,
                'subject' => $subject,
                'teacher' => $teacher,
                'academic_period' => $academic_period_result,
                'academic_period_cut' => $academic_period_cut
            ];

            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-validation-subject.php');
        }

        if (isset($_GET['section_tab']) && $_GET['section_tab'] === 'student_matrix') {

            if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
                return;
            }

            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at DESC");
            $student_id = (int) $_GET['student_id'];
            $student = get_student_detail($student_id);
            $projection = get_projection_by_student($student_id);
            $cuts = ['A', 'B', 'C', 'D', 'E'];
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1");
            // Use the relational table as canonical source for the student's expected matrix.
            if ($projection) {
                $matrix = get_expected_matrix_by_student($projection->student_id);
            } else {
                $matrix = [];
            }
            // $matrix is already an array reconstructed from the relational table.

            $lastNameParts = array_filter([$student->last_name, $student->middle_last_name]);
            $firstNameParts = array_filter([$student->name, $student->middle_name]);
            $student_full_name = '';

            if (!empty($lastNameParts)) {
                $student_full_name .= implode(' ', $lastNameParts);
            }

            if (!empty($firstNameParts)) {
                if (!empty($student_full_name)) {
                    $student_full_name .= ', ';
                }
                $student_full_name .= implode(' ', $firstNameParts);
            }

            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-student-matrix.php');
        }
    } else {

        if (isset($_GET['action']) && $_GET['action'] == 'generate_academic_projections') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';

            $students = $wpdb->get_results("SELECT * FROM {$table_students} ORDER BY id DESC");

            foreach ($students as $key => $student) {
                generate_projection_student($student->id);
            }

            setcookie('message', __('Successfully generated all missing academic projections for the students.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_student_payments_record') {
            $students = get_active_students();
            foreach ($students as $key => $student) {
                $args = [
                    'status' => ['wc-completed'],
                    'limit' => -1,
                    'customer' => $student->partner_id
                ];
                $orders = wc_get_orders($args);
                foreach ($orders as $order) {
                    foreach ($order->get_items() as $item) {
                        process_payments($student->id, $order, $item);
                    }
                }
            }

            setcookie('message', __('Successfully generated all missing payments records for the students.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_academic_projection_student') {
            $student_id = $_GET['student_id'];
            generate_projection_student($student_id, true);

            setcookie('message', __('Successfully generated academic projections for the student.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'withdraw_student') {
            $student_id = $_GET['student_id'];
            update_status_student($student_id, 6);

            setcookie('message', __('Student successfully withdrawn.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_virtual_classroom') {
            $student_id = $_GET['student_id'];
            sync_student_with_moodle($student_id);

            setcookie('message', __('Successfully generated virtual classroom for the student.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_admin') {
            $student_id = $_GET['student_id'];
            if (has_action('portal_create_user_external')) {
                do_action('portal_create_user_external', $student_id);
            }

            setcookie('message', __('Successfully generated admin for the student.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_enrollments_moodle') {
            generate_enroll_student();
            setcookie('message', __('Students successfully enrolled in moodle.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'enroll_public_course') {
            generate_enroll_public_course();
            setcookie('message', __('Students successfully enrolled in public course.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'auto_enroll') {
            global $wpdb;
            $student_id = $_GET['student_id'];
            $projection_id = $_GET['projection_id'];
            automatically_enrollment($student_id);
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'student_elective_change') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $student_id = $_GET['student_id'];
            $projection_id = $_GET['projection_id'];
            $status = $_GET['status'];
            $wpdb->update($table_students, [
                'elective' => $status
            ], ['id' => $student_id]);
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'send_welcome_email') {
            $load = load_current_cut();
            $code = $load['code'];
            $cut = $load['cut'];
            update_option('send_welcome_email_ready', $code . ' - ' . $cut);

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_students} WHERE status_id != %d ORDER BY id DESC",
                5
            );
            $students = $wpdb->get_results($query);
            foreach ($students as $key => $student) {
                send_welcome_subjects($student->id);
            }

            setcookie('message', __('Mails sent.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'clear_electives') {
            clear_students_electives();
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'set_max_date_upload_at') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_results("SELECT * FROM {$table_students} WHERE initial_cut = 'E' ORDER BY id DESC");
            foreach ($students as $key => $student) {
                // update_max_upload_at($student->id);
            }
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'get_moodle_notes') {
            $subject_id = $_POST['subject_id'];
            $code = $_POST['code_current_cut'];
            $cut = $_POST['cut_current_cut'];

            get_moodle_notes($subject_id, $code, $cut);
            setcookie('message', __('Successfully updated notes for the students.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'save_academic_projection') {
            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $projection_id = $_POST['projection_id'];
            $completed = $_POST['completed'] ?? [];
            $academic_period = $_POST['academic_period'] ?? [];
            $academic_period_cut = $_POST['academic_period_cut'] ?? [];
            $calification = $_POST['calification'] ?? [];
            $this_cut = $_POST['this_cut'] ?? [];
            $action = $_POST['action'] ?? 'save';
            $projection = get_projection_details(projection_id: $projection_id);
            $projection_obj = json_decode($projection->projection);
            $old_count_enroll = 0;
            $count_enroll = 0;
            $errors = '';

            foreach ($projection_obj as $key => $value) {
                if ($projection_obj[$key]->this_cut) {
                    $old_count_enroll++;
                }
            }

            // Procesar los datos
            foreach ($projection_obj as $key => $value) {
                $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_obj[$key]->subject_id}");

                $is_completed = isset($completed[$key]) ? true : false;
                $is_this_cut = ($this_cut[$key] === "1" || strtolower($this_cut[$key]) === "true") ? true : false;
                $period = $academic_period[$key] ?? null;
                $cut = $academic_period_cut[$key] ?? null;
                $calification_value = $calification[$key] ?? null;

                if ($calification_value) {
                    $is_this_cut = false;
                }

                if ($is_completed && $is_this_cut) {
                    $offer_available_to_enroll = offer_available_to_enroll($subject->id, $period, $cut);
                    if (!$offer_available_to_enroll) {
                        $errors .= $subject->name . ' could not be enrolled because no academic offers were found for the selected period and cut-off, or there is no space available. <br>';
                        continue;
                    }
                }

                $status_id = $is_this_cut ? 1 : ($calification_value >= $subject->min_pass ? 3 : 4);
                if ($status_id != 4) {
                    $projection_obj[$key]->is_completed = $is_completed;
                    $projection_obj[$key]->this_cut = $is_this_cut;
                    $projection_obj[$key]->code_period = $period;
                    $projection_obj[$key]->cut = $cut;
                    $projection_obj[$key]->calification = $calification_value;
                } else {
                    $projection_obj[$key]->is_completed = false;
                    $projection_obj[$key]->this_cut = false;
                    $projection_obj[$key]->code_period = '';
                    $projection_obj[$key]->cut = '';
                    $projection_obj[$key]->calification = '';
                }

                if ($is_completed) {
                    $student_id = intval($projection->student_id); // Asegúrate de que sea un entero
                    $code_subject = $wpdb->esc_like($projection_obj[$key]->code_subject); // Escapa la cadena

                    $query = $wpdb->prepare(
                        "SELECT * FROM {$table_student_period_inscriptions} 
                        WHERE student_id = %d 
                        AND code_subject = %s 
                        AND status_id = %d",
                        $student_id,
                        $code_subject,
                        1
                    );

                    $exist = $wpdb->get_row($query);
                    if (!isset($exist)) {
                        $query = $wpdb->prepare(
                            "SELECT * FROM {$table_student_period_inscriptions} 
                            WHERE student_id = %d 
                            AND code_subject = %s 
                            AND status_id = %d",
                            $student_id,
                            $code_subject,
                            $status_id
                        );
                        $exist = $wpdb->get_row($query);
                        if (!isset($exist)) {
                            $wpdb->insert($table_student_period_inscriptions, [
                                'status_id' => $status_id,
                                'student_id' => $projection->student_id,
                                'subject_id' => $projection_obj[$key]->subject_id,
                                'code_subject' => $projection_obj[$key]->code_subject,
                                'code_period' => $period,
                                'cut_period' => $cut,
                                'calification' => $calification_value,
                                'type' => $subject->type
                            ]);
                            update_expected_matrix_after_enrollment($projection->student_id, $projection_obj[$key]->subject_id, $period, $cut);
                        }
                    } else {
                        $wpdb->update($table_student_period_inscriptions, [
                            'status_id' => $status_id,
                            'student_id' => $projection->student_id,
                            'subject_id' => $projection_obj[$key]->subject_id,
                            'code_subject' => $projection_obj[$key]->code_subject,
                            'code_period' => $period,
                            'cut_period' => $cut,
                            'calification' => $calification_value,
                            'type' => $subject->type
                        ], ['id' => $exist->id]);
                        update_expected_matrix_after_enrollment($projection->student_id, $projection_obj[$key]->subject_id, $period, $cut);
                    }

                    if ($is_this_cut) {
                        $count_enroll++;
                    }
                }

                // Verificamos si status_id es 4 y si is_elective existe y es true
                if ($status_id == 4 && isset($projection_obj[$key]->is_elective) && $projection_obj[$key]->is_elective) {
                    // Si se cumplen ambas condiciones, eliminamos el elemento del array
                    unset($projection_obj[$key]);
                    continue; // Saltamos al siguiente elemento del bucle
                }
            }

            if ($count_enroll != $old_count_enroll) {
                update_count_moodle_pending();
            }

            $wpdb->update($table_student_academic_projection, [
                'projection' => json_encode($projection_obj) // Ajusta el valor de 'projection' según sea necesario
            ], ['id' => $projection->id]);

            if ($action == 'send_email') {
                send_welcome_subjects($projection->student_id, true);
            }

            update_max_upload_at($projection->student_id);
            setcookie('message', __('Projection adjusted successfully.', 'edusystem'), time() + 10, '/');
            setcookie('message-error', $errors, time() + 3600, '/');
            wp_redirect(admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'delete_inscription') {
            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $projection_id = $_GET['projection_id'];
            $inscription_id = $_GET['inscription_id'] ?? [];
            $enrollment = get_enrollment_details($inscription_id);
            $projection = get_projection_details(projection_id: $projection_id);
            $projection_obj = json_decode($projection->projection);

            if ($enrollment->status_id != 4) {
                $subjectIds = array_column($projection_obj, 'code_subject');
                $indexToEdit = array_search($enrollment->code_subject, $subjectIds);
                if ($indexToEdit !== false) {
                    $projection_obj[$indexToEdit]->cut = '';
                    $projection_obj[$indexToEdit]->this_cut = false;
                    $projection_obj[$indexToEdit]->code_period = '';
                    $projection_obj[$indexToEdit]->calification = '';
                    $projection_obj[$indexToEdit]->is_completed = false;
                    $projection_obj[$indexToEdit]->welcome_email = false;
                }

                $wpdb->update($table_student_academic_projection, [
                    'projection' => json_encode($projection_obj)
                ], ['id' => $projection->id]);
            }

            if ($enrollment->status_id = 1) {
                $enrollments = [];
                $offer = get_offer_filtered($enrollment->subject_id, $enrollment->code_period, $enrollment->cut_period);
                if ($offer) {
                    $enrollments = array_merge($enrollments, courses_unenroll_student($enrollment->student_id, (int) $offer->moodle_course_id));
                    unenroll_student($enrollments);
                }
            }

            $wpdb->delete($table_student_period_inscriptions, ['id' => $inscription_id]);
            update_expected_matrix_after_enrollment($enrollment->student_id, $enrollment->subject_id, $enrollment->code_period, $enrollment->cut_period);

            setcookie('message', __('Projection adjusted successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'set_max_access_date') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';

            $students = $wpdb->get_results("SELECT * FROM {$table_students} ORDER BY id DESC");
            foreach ($students as $key => $student) {
                set_max_date_student($student->id);
            }
            wp_redirect(admin_url('/admin.php?page=add_admin_form_configuration_options_content'));
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'update_matrix') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $projection_id = isset($_POST['projection_id']) ? intval($_POST['projection_id']) : 0;
            $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
            $new_matrix_data_raw = isset($_POST['matrix']) ? $_POST['matrix'] : [];

            $errors = [];

            if ($projection_id <= 0) {
                $errors[] = __('Invalid projection ID.', 'edusystem');
            }

            // 3. Process and Save Data
            if (empty($errors)) {
                foreach ($new_matrix_data_raw as $key => &$new_m) {
                    $new_m['completed'] = false;
                    if ($new_m['code_period'] && $new_m['cut'] && $new_m['subject_id']) {
                        $inscription = get_inscriptions_by_student_subject($student_id, $new_m['code_period'], $new_m['cut'], $new_m['subject_id']);
                        if ($inscription) {
                            $new_m['completed'] = true;
                        }
                    }
                }
                // Persist the submitted matrix into the normalized table.
                // Clear previous rows to avoid duplicates, then insert new ones.
                clear_expected_matrix_for_student($student_id);
                persist_expected_matrix($student_id, $new_matrix_data_raw);
                setcookie('message', __('Projection matrix updated successfully.', 'edusystem'), time() + 10, '/');
            } else {
                // Handle validation errors
                setcookie('message-error', implode('<br>', $errors), time() + 10, '/');
            }

            // 4. Redirect
            $redirect_url = admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=student_matrix&student_id=' . $student_id);
            wp_redirect($redirect_url);
            exit;
        } else {
            $load = load_current_cut_enrollment();
            $code = $load['code'];
            $cut = $load['cut'];

            $load_current_cut = load_current_cut();
            $code_current_cut = $load_current_cut['code'];
            $cut_current_cut = $load_current_cut['cut'];
            if (get_option('send_welcome_email_ready') != $code_current_cut . ' - ' . $cut_current_cut) {
                update_option('send_welcome_email_ready', '');
            }

            $current_enroll_text = 'Current period and cutoff ' . $code . ' - ' . $cut;
            $enroll_moodle_count = get_count_moodle_pending();
            $pending_emails = get_count_email_pending();
            $pending_emails_count = $pending_emails['count'];
            $pending_emails_students = $pending_emails['students'];
            $list_academic_projection = new TT_academic_projection_all_List_Table;
            $list_academic_projection->prepare_items();

            $available_offers = get_offers_availables_by_code($code_current_cut, $cut_current_cut);

            include(plugin_dir_path(__FILE__) . 'templates/list-academic-projection.php');
        }
    }
}

class TT_academic_projection_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'academic_projection_',
                'plural' => 'academic_projection_s',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'student':
            case 'grade_id':
            case 'initial_cut':
                return '<label class="text-uppercase">' . strtoupper($item[$column_name]) . '</label>';
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $item['academic_projection_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
            default:
                return ucwords($item[$column_name]);
        }
    }

    function column_name($item)
    {

        return ucwords($item['name']);
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'edusystem'),
            'initial_cut' => __('Initial period - cut', 'edusystem'),
            'grade_id' => __('Grade', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_academic_projections()
    {
        global $wpdb;
        $academic_projections_array = [];
        $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
        $table_students = $wpdb->prefix . 'students';

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        // Sanitize and retrieve the search term
        $search = sanitize_text_field($_GET['s'] ?? '');

        $conditions = array();
        $params = array();

        // 1. Smart Search Condition (applied to the students table)
        if (!empty($search)) {
            $search_term_like = '%' . $wpdb->esc_like($search) . '%';

            $search_sub_conditions = [];
            $search_sub_params = [];

            // Combined search for names and surnames (CONCAT_WS)
            // Using alias 's' for table_students
            $combined_fields = [
                'CONCAT_WS(" ", s.name, s.last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name, s.last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name, s.last_name, s.middle_last_name)',
                'CONCAT_WS(" ", s.last_name, s.name)',
                'CONCAT_WS(" ", s.last_name, s.middle_last_name)',
                'CONCAT_WS(" ", s.name, s.middle_name)',
                'CONCAT_WS(" ", s.last_name, s.middle_last_name)'
            ];

            foreach ($combined_fields as $field_combination) {
                $search_sub_conditions[] = "{$field_combination} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Direct search in individual fields
            $individual_fields = ['s.name', 's.middle_name', 's.last_name', 's.middle_last_name', 's.id_document', 's.email']; // Added s.email
            foreach ($individual_fields as $field) {
                $search_sub_conditions[] = "{$field} LIKE %s";
                $search_sub_params[] = $search_term_like;
            }

            // Add the main search condition to the general conditions array
            if (!empty($search_sub_conditions)) {
                $conditions[] = "(" . implode(" OR ", $search_sub_conditions) . ")";
                $params = array_merge($params, $search_sub_params);
            }
        }

        // 2. Build the main query using a JOIN
        // This allows filtering students and fetching projections in a single database call.
        $query = "
        SELECT SQL_CALC_FOUND_ROWS sap.*,
               s.name, s.middle_name, s.last_name, s.middle_last_name, s.academic_period, s.initial_cut, s.grade_id
        FROM {$table_student_academic_projection} AS sap
        JOIN {$table_students} AS s ON sap.student_id = s.id
    ";

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY sap.id DESC"; // Order by academic projection ID

        // Add LIMIT and OFFSET parameters
        $query .= " LIMIT %d OFFSET %d";
        $params[] = $per_page;
        $params[] = $offset;

        // Execute the query
        $academic_projections = $wpdb->get_results($wpdb->prepare($query, $params), "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // 3. Process the results
        if ($academic_projections) {
            foreach ($academic_projections as $projection) {
                // Student details are now directly available in the $projection array from the JOIN
                $student_full_name = '<span class="text-uppercase">' . $projection['last_name'] . ' ' . ($projection['middle_last_name'] ?? '') . ' ' . $projection['name'] . ' ' . ($projection['middle_name'] ?? '') . '</span>';

                $academic_projections_array[] = [
                    'student' => $student_full_name,
                    'student_id' => $projection['student_id'],
                    'initial_cut' => $projection['academic_period'] . ' - ' . $projection['initial_cut'],
                    'academic_projection_id' => $projection['id'],
                    'grade_id' => function_exists('get_name_grade') ? get_name_grade($projection['grade_id']) : $projection['grade_id']
                ];
            }
        }

        return ['data' => $academic_projections_array, 'total_count' => $total_count];
    }

    function get_sortable_columns()
    {
        $sortable_columns = [];
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = [];
        return $actions;
    }

    function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }

    function prepare_items()
    {

        $data_academic_projections = $this->get_academic_projections();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_academic_projections['data'];
        $total_count = (int) $data_academic_projections['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }
}

function get_projection_details($projection_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';

    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE id={$projection_id}");
    return $projection;
}

function get_projection_by_student($student_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';

    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id={$student_id}");
    return $projection;
}

function get_inscriptions_by_student($student_id)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student_id} AND code_subject IS NOT NULL AND code_subject <> ''");
    return $inscriptions;
}

function get_inscriptions_by_student_period($student_id, $code_period, $cut_period)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student_id} AND code_period = '{$code_period}' AND cut_period = '{$cut_period}' AND code_subject IS NOT NULL AND code_subject <> ''");
    return $inscriptions;
}

function get_inscriptions_by_student_subject($student_id, $code_period, $cut_period, $subject_id)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $inscriptions = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student_id} AND code_period = '{$code_period}' AND cut_period = '{$cut_period}' AND subject_id = {$subject_id}");
    return $inscriptions;
}

function get_inscriptions_by_subject_period($subject_id, $code_subject, $code_period, $cut_period, $status)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    // Determinar los status_id según el valor de $status
    $status_ids = [];
    if ($status === 'current') {
        $status_ids[] = 1;
    } elseif ($status === 'history') {
        $status_ids[] = 2;
        $status_ids[] = 3;
        $status_ids[] = 4;
    } else {
        // Manejar un caso por defecto o lanzar un error si $status no es 'current' ni 'history'
        return false;
    }

    // Convertir el array de IDs a una cadena para la cláusula IN de SQL
    $status_ids_in_clause = implode(',', array_map('intval', $status_ids));

    // Usamos $wpdb->prepare() para proteger contra la inyección SQL.
    // Los paréntesis son cruciales para asegurar la lógica correcta del OR.
    $query = $wpdb->prepare(
        "SELECT * FROM {$table_student_period_inscriptions} 
         WHERE (subject_id = %d OR code_subject = %s) 
           AND code_period = %s 
           AND cut_period = %s
           AND status_id IN ({$status_ids_in_clause})", // Usamos IN para múltiples valores
        $subject_id,
        $code_subject,
        $code_period,
        $cut_period
    );

    $inscriptions = $wpdb->get_results($query);
    return $inscriptions;
}

function get_inscriptions_by_student_automatically_enrollment($subject_id, $code_subject, $status, $student_id = null, $code_period = null, $cut_period = null)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    // Determinar los status_id según el valor de $status
    $status_ids = [];
    if ($status === 'current') {
        $status_ids[] = 1;
    } elseif ($status === 'history') {
        $status_ids[] = 2;
        $status_ids[] = 3;
        $status_ids[] = 4;
    } else {
        return false;
    }

    // Convertir el array de IDs a una cadena para la cláusula IN de SQL
    $status_ids_in_clause = implode(',', array_map('intval', $status_ids));

    // Iniciar la consulta base y el array de argumentos con los parámetros requeridos
    $query = "SELECT * FROM {$table_student_period_inscriptions} 
              WHERE (subject_id = %d OR code_subject = %s) 
                AND status_id IN ({$status_ids_in_clause})";

    $args = [
        $subject_id,
        $code_subject
    ];

    // Verificar si $code_period está presente y añadir la condición y el argumento
    if ($code_period !== null) {
        $query .= " AND code_period = %s";
        $args[] = $code_period;
    }

    // Verificar si $cut_period está presente y añadir la condición y el argumento
    if ($cut_period !== null) {
        $query .= " AND cut_period = %s";
        $args[] = $cut_period;
    }

    // Verificar si $student_id está presente y añadir la condición y el argumento
    if ($student_id !== null) {
        $query .= " AND student_id = %d";
        $args[] = $student_id;
    }

    // Usar $wpdb->prepare() con el query y los argumentos
    $prepared_query = $wpdb->prepare($query, ...$args);
    // Obtener los resultados
    $inscriptions = $wpdb->get_results($prepared_query);
    
    return $inscriptions;
}

function generate_enroll_student()
{
    try {
        global $wpdb;
        $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
        $table_students = $wpdb->prefix . 'students';

        // Get current enrollment period
        $load = load_current_cut();
        if (empty($load['code']) || empty($load['cut'])) {
            throw new Exception('Invalid enrollment period');
        }

        $code = $load['code'];
        $cut = $load['cut'];

        // Get all projections with student data in a single query
        $query = $wpdb->prepare(
            "SELECT p.*, s.last_name, s.middle_last_name, s.name, s.middle_name 
            FROM {$table_student_academic_projection} p
            INNER JOIN {$table_students} s ON p.student_id = s.id"
        );

        $projections = $wpdb->get_results($query);
        if (empty($projections)) {
            return;
        }

        $enrollments = [];
        $errors = [];
        $errors_count = 0;

        foreach ($projections as $projection) {
            $projection_obj = json_decode($projection->projection);
            if (!is_array($projection_obj)) {
                continue;
            }

            // Filter subjects for current period
            $filteredArray = array_filter($projection_obj, function ($item) use ($code, $cut) {
                return isset($item->this_cut) &&
                    $item->this_cut === true &&
                    $item->code_period == $code &&
                    $item->cut == $cut;
            });

            if (empty($filteredArray)) {
                continue;
            }

            foreach ($filteredArray as $projection_filtered) {
                if (!isset($projection_filtered->subject_id)) {
                    continue;
                }

                $inscription = get_inscriptions_by_student_subject($projection->student_id, $code, $cut, $projection_filtered->subject_id);
                $offer = get_offer_filtered($projection_filtered->subject_id, $code, $cut, $inscription->section);
                if ($offer && isset($offer->moodle_course_id)) {
                    $enrollments = array_merge(
                        $enrollments,
                        courses_enroll_student($projection->student_id, [(int) $offer->moodle_course_id])
                    );
                } else {
                    $student_name = sprintf(
                        '%s %s %s %s',
                        $projection->last_name,
                        $projection->middle_last_name,
                        $projection->name,
                        $projection->middle_name
                    );

                    $errors[] = sprintf(
                        'The student %s could not be enrolled because no offers were found for the current period (%s)',
                        $student_name,
                        $cut
                    );
                    $errors_count++;
                }
            }
        }

        // Process enrollments
        if (!empty($enrollments)) {
            enroll_student($enrollments);
            update_count_moodle_pending($errors_count);
        }

        // Set error message if any
        if (!empty($errors)) {
            setcookie('message-error', implode('<br>', $errors), time() + 3600, '/');
        }
    } catch (Exception $e) {
        setcookie('message-error', 'An error occurred while processing enrollments', time() + 3600, '/');
    }
}

function generate_enroll_public_course()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $enrollments = [];
    $students = get_active_students();
    foreach ($students as $key => $student) {
        $enrollments = array_merge($enrollments, courses_enroll_student($student->id, [(int) get_option('public_course_id')]));
    }

    enroll_student_public_course($enrollments);
}

function get_moodle_notes($subject_id, $academic_period, $cut)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    try {
        if (empty($cut)) {
            return;
        }

        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT student_id 
            FROM {$table_student_period_inscriptions} 
            WHERE code_period = %s 
            AND cut_period = %s 
            AND subject_id = %d",
            $academic_period,
            $cut,
            $subject_id
        ));

        if (empty($cut_student_ids)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($cut_student_ids), '%d'));

        $students = $wpdb->get_results($wpdb->prepare(
            "SELECT id, moodle_student_id FROM {$table_students} 
            WHERE id IN ($placeholders)
            AND moodle_student_id IS NOT NULL",
            $cut_student_ids
        ));

        if (empty($students)) {
            return;
        }

        $projections = $wpdb->get_results($wpdb->prepare(
            "SELECT id, student_id, projection FROM {$table_student_academic_projection} 
            WHERE student_id IN ($placeholders)",
            $cut_student_ids
        ));

        $projections_lookup = [];
        foreach ($projections as $projection) {
            $projections_lookup[$projection->student_id] = $projection;
        }

        $wpdb->query('START TRANSACTION');

        foreach ($students as $student) {
            if (empty($student->moodle_student_id)) {
                continue;
            }

            $assignments = student_assignments_moodle_only_grades_optimized($student->id);
            if (empty($assignments['grades'])) {
                continue;
            }

            $projection_student = $projections_lookup[$student->id] ?? null;
            if (!$projection_student) {
                continue;
            }

            $projection_obj = json_decode($projection_student->projection);
            $updates_needed = false;

            foreach ($assignments['grades'] as $grade) {
                $course_id = (int) $grade['course_id'];
                
                $offer = get_offer_by_moodle_optimized($course_id);
                if (!$offer) {
                    continue;
                }

                foreach ($grade['grades'] as $grade_item) {
                    $grade_items = $grade_item['gradeitems'];

                    $filtered_grade_items = array_filter($grade_items, function ($item) {
                        return !isset($item['cmid']);
                    });
                    
                    if (empty($filtered_grade_items)) {
                        continue;
                    }
                    
                    $filtered_grade_items = array_values($filtered_grade_items);
                    $grade_value = (float) $filtered_grade_items[0]['gradeformatted'];
                    $total_grade = $grade_value > 100 ? 100 : $grade_value;

                    $subject = get_subject_details_optimized($offer->subject_id);
                    if (!$subject) {
                        continue;
                    }

                    $status_id = $total_grade >= $subject->min_pass ? 3 : 4;

                    foreach ($projection_obj as $prj) {
                        if ($prj->subject_id == $subject->id) {
                            $prj->calification = $total_grade;
                            $prj->this_cut = false;

                            if ($status_id == 4) {
                                $prj->is_completed = false;
                                $prj->this_cut = false;
                                $prj->cut = '';
                                $prj->code_period = '';
                                $prj->calification = '';
                                $prj->welcome_email = false;
                            } else {
                                $prj->cut = $offer->cut_period;
                                $prj->code_period = $offer->code_period;
                                $prj->is_completed = true;
                                $prj->welcome_email = true;
                            }

                            $wpdb->query($wpdb->prepare(
                                "UPDATE {$table_student_period_inscriptions} 
                                SET status_id = %d, 
                                    calification = %f 
                                WHERE student_id = %d 
                                    AND code_period = %s 
                                    AND cut_period = %s 
                                    AND (subject_id = %d OR code_subject = %s)",
                                $status_id,
                                $total_grade,
                                $student->id,
                                $offer->code_period,
                                $offer->cut_period,
                                $subject->id,
                                $subject->code_subject
                            ));

                            $updates_needed = true;
                            break;
                        }
                    }
                }
            }

            if ($updates_needed) {
                $wpdb->update(
                    $table_student_academic_projection,
                    ['projection' => json_encode($projection_obj)],
                    ['id' => $projection_student->id]
                );
            }
        }
        
        $wpdb->query('COMMIT');

    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
    }
}

function student_assignments_moodle_only_grades_optimized($student_id) {
    try {
        global $wpdb;
        static $moodle_config = null;

        if ($moodle_config === null) {
            $moodle_config = [
                'url' => get_option('moodle_url'),
                'token' => get_option('moodle_token')
            ];
        }

        if (empty($moodle_config['url']) || empty($moodle_config['token'])) {
            return ['grades' => []];
        }

        $table_students = $wpdb->prefix.'students';
        $data_student = $wpdb->get_row($wpdb->prepare("SELECT moodle_student_id FROM {$table_students} WHERE id=%d", $student_id));
        
        if (empty($data_student) || empty($data_student->moodle_student_id)) {
            return ['grades' => []];
        }

        $courses = is_enrolled_in_courses_optimized($student_id);
        
        if (empty($courses)) {
            return ['grades' => []];
        }

        $grades = [];
        $moodle_student_id = $data_student->moodle_student_id;

        foreach ($courses as $course) {
            if ($course['visible']) {
                $grades_course = course_grade((int)$course['id']);
                
                if (isset($grades_course['usergrades'])) {
                    $user_grades = $grades_course['usergrades'];
                    $filtered_grades = [];
                    
                    foreach ($user_grades as $entry) {
                        if ($entry['userid'] == $moodle_student_id) {
                            $filtered_grades[] = $entry;
                        }
                    }
    
                    if (!empty($filtered_grades)) {
                         $grades[] = ['course_id' => (int)$course['id'], 'grades' => $filtered_grades];
                    }
                }
            }
        }

        return ['grades' => $grades];

    } catch (\Throwable $th) {
        return ['grades' => []];
    }
}

function is_enrolled_in_courses_optimized($student_id) {
    try {
        global $wpdb;
        static $moodle_config = null;

        if ($moodle_config === null) {
            $moodle_config = [
                'url' => get_option('moodle_url'),
                'token' => get_option('moodle_token')
            ];
        }

        if (empty($moodle_config['url']) || empty($moodle_config['token'])) {
             return [];
        }

        $table_students = $wpdb->prefix.'students';
        $data_student = $wpdb->get_row($wpdb->prepare("SELECT moodle_student_id FROM {$table_students} WHERE id=%d", $student_id));
    
        if (!empty($data_student) && $data_student->moodle_student_id) {
            $MoodleRest = new MoodleRest($moodle_config['url'].'webservice/rest/server.php', $moodle_config['token']);

            $enrolments = [
                'userid' => $data_student->moodle_student_id,
            ];

            $enrolled_courses = $MoodleRest->request('core_enrol_get_users_courses', $enrolments);

            return empty($enrolled_courses) ? [] : $enrolled_courses;
        }
    
        return [];
    } catch (\Throwable $th) {
        return [];
    }
}

function get_offer_by_moodle_optimized($moodle_course_id)
{
    global $wpdb;
    static $cache = [];

    if (isset($cache[$moodle_course_id])) {
        return $cache[$moodle_course_id];
    }

    $table_academic_offers = $wpdb->prefix . 'academic_offers';
    $offer = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_academic_offers} WHERE moodle_course_id=%d ORDER BY id DESC LIMIT 1", $moodle_course_id));
    
    $cache[$moodle_course_id] = $offer;
    return $offer;
}

function get_subject_details_optimized($subject_id)
{
    global $wpdb;
    static $cache = [];

    if (isset($cache[$subject_id])) {
        return $cache[$subject_id];
    }

    $table = $wpdb->prefix . 'school_subjects';
    $subject = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id=%d", $subject_id));
    
    $cache[$subject_id] = $subject;
    return $subject;
}

function get_literal_note($calification)
{
    // Accept numeric values (including '0' or '0.00'). Treat NULL or empty string as unknown.
    if ($calification === null || $calification === '') {
        return '-';
    }

    if (!is_numeric($calification)) {
        return '-';
    }

    $calification = (float) $calification;
    $note = 'A+';

    if ($calification >= 95) {
        $note = 'A+';
    } elseif ($calification >= 90) {
        $note = 'A-';
    } elseif ($calification >= 83) {
        $note = 'B+';
    } elseif ($calification >= 80) {
        $note = 'B-';
    } elseif ($calification >= 73) {
        $note = 'C+';
    } elseif ($calification >= 70) {
        $note = 'C-';
    } elseif ($calification >= 67) {
        $note = 'D+';
    } elseif ($calification >= 60) {
        $note = 'D-';
    } else {
        $note = 'F';
    }

    return $note;
}

function get_calc_note($calification)
{
    // Accept numeric values (including '0' or '0.00'). Treat NULL or empty string as unknown.
    if ($calification === null || $calification === '') {
        return '-';
    }

    if (!is_numeric($calification)) {
        return '-';
    }

    $calification = (float) $calification;
    $note = 0;

    if ($calification >= 95) {
        $note = 4.00;
    } elseif ($calification >= 90) {
        $note = 3.75;
    } elseif ($calification >= 87) {
        $note = 3.50;
    } elseif ($calification >= 83) {
        $note = 3.00;
    } elseif ($calification >= 80) {
        $note = 2.75;
    } elseif ($calification >= 77) {
        $note = 2.50;
    } elseif ($calification >= 73) {
        $note = 2.00;
    } elseif ($calification >= 70) {
        $note = 1.75;
    } elseif ($calification >= 67) {
        $note = 1.50;
    } elseif ($calification >= 60) {
        $note = 1.00;
    } else {
        $note = 0.00;
    }

    return $note;
}

function get_count_moodle_pending()
{
    global $wpdb;
    $table_count_pending_student = $wpdb->prefix . 'count_pending_student';
    $pending = $wpdb->get_row("SELECT * FROM {$table_count_pending_student} WHERE id = 1");
    return $pending->count;
}

function get_count_email_pending()
{
    try {
        global $wpdb;
        $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
        $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
        $count = 0;
        $students = [];

        foreach ($projections as $key => $projection) {
            $projection_obj = json_decode($projection->projection);
            $filteredArray = array_filter($projection_obj, function ($item) {
                return $item->this_cut === true && !$item->welcome_email;
            });
            $filteredArray = array_values($filteredArray);

            if (count($filteredArray) > 0) {
                $count++;
                array_push($students, get_student_detail($projection->student_id));
            }
        }

        return ['count' => $count, 'students' => $students];
    } catch (\Throwable $th) {
        return ['count' => 0, 'students' => []];
    }
}

function update_count_moodle_pending($count_fixed = '')
{
    global $wpdb;
    $table_count_pending_student = $wpdb->prefix . 'count_pending_student';
    $count = $count_fixed != '' ? $count_fixed : (get_count_moodle_pending() + 1);
    $wpdb->update($table_count_pending_student, [
        'count' => $count
    ], ['id' => 1]);
}

function table_notes_html($student_id, $projection)
{
    // Usar la sintaxis de Heredoc para un HTML más limpio y legible
    $html = <<<HTML
<table class='wp-list-table widefat fixed posts striped' style='margin-top: 20px; border: 1px dashed #c3c4c7;' id="tablenotcustom">
    <thead>
        <tr>
            <th style='width: 70px;'>CODE</th>
            <th>COURSE</th>
            <th style='width: 40px;'>CH</th>
            <th style='width: 40px;'>0-100</th>
            <th style='width: 40px;'>0-4</th>
            <th style='width: 150px;'>PERIOD</th>
        </tr>
    </thead>
    <tbody>
HTML;

    // Decodificar la proyección una sola vez
    $projections = json_decode($projection->projection);
    if (empty($projections)) {
        return $html . "</tbody></table>";
    }

    // Cargar datos de manera eficiente antes del bucle para evitar consultas repetitivas
    $is_approved = get_status_approved('CERTIFIED NOTES HIGH SCHOOL', $student_id);

    // Mapear los detalles de los sujetos y períodos para evitar consultas repetidas dentro del bucle
    $subject_details_map = [];
    $period_details_map = [];

    foreach ($projections as $item) {
        $subject_id = $item->subject_id;
        if (!isset($subject_details_map[$subject_id])) {
            $subject_details_map[$subject_id] = get_subject_details($subject_id);
        }

        $period_code = $item->code_period;
        if (!isset($period_details_map[$period_code])) {
            $period_details_map[$period_code] = get_period_details_code($period_code);
        }
    }

    // Generar las filas de la tabla
    foreach ($projections as $projection_for) {
        $subject = $subject_details_map[$projection_for->subject_id];
        $period = $period_details_map[$projection_for->code_period];
        $period_name = $period ? $period->name : '-';
        $is_equivalence = ($subject && $subject->type == 'equivalence');
        $tr_or_dash = $is_approved ? 'TR' : '-';

        $ch_value = '';
        if (!$is_equivalence) {
            $ch_value = $projection_for->hc;
        } else {
            $ch_value = $tr_or_dash;
        }

        $note_100_value = '';
        if (isset($projection_for->calification) && !empty($projection_for->calification)) {
            $note_100_value = $projection_for->calification;
        } else {
            $note_100_value = !$is_equivalence ? '-' : $tr_or_dash;
        }

        $note_4_value = '';
        if (!$is_equivalence) {
            $note_4_value = get_calc_note($projection_for->calification);
        } else {
            $note_4_value = $tr_or_dash;
        }

        // Usar sprintf() para una inyección de variables más segura y clara
        $html .= sprintf(
            '<tr>
                <td>%s</td>
                <td>%s%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>',
            esc_html($projection_for->code_subject),
            esc_html($projection_for->subject),
            (isset($projection_for->is_elective) && $projection_for->is_elective ? ' (ELECTIVE)' : ''),
            esc_html($ch_value),
            esc_html($note_100_value),
            esc_html($note_4_value),
            esc_html($period_name)
        );
    }

    $html .= "</tbody></table>";

    return $html;
}

function get_payment_method_table_html($student): string
{
    // Define payment methods with WooCommerce/plugin IDs as keys.
    $payment_methods = [
        'woo_squuad_stripe' => 'CREDIT CARD',
        'check' => 'CHECK (US$)',
        'money' => 'MONEY ORDER',
        'aes_payment' => 'WIRE TRANSFER',
        'coupon' => 'COUPON',
        'zelle_payment' => 'ZELLE'
    ];

    $selected_method_index = '';

    $args = [
        'customer_id' => $student->partner_id,
        'status' => ['wc-completed'],
        'limit' => 1,
        // Adjusted: Use 'ASC' (Ascending) to get the oldest order (the first one).
        'orderby' => 'date',
        'order' => 'ASC',
        // CRUCIAL: Force the function to return standard WC_Order objects, resolving the Fatal Error.
        'return' => 'objects',
    ];

    $orders_completed = wc_get_orders($args);

    if (!empty($orders_completed) && is_array($orders_completed)) {
        // Since we limited the query to 1 and ordered by ASC, the first element is the oldest.
        $first_order = reset($orders_completed);

        // Validation: Ensure the element is truly a WC_Order object before calling its methods.
        if (is_a($first_order, 'WC_Order')) {
            $selected_method_index = $first_order->get_payment_method();
        }
    }

    // Check if the extracted method ID exists in our array of methods.
    $is_valid_selection = (array_key_exists($selected_method_index, $payment_methods));

    // Start output buffering to capture the HTML.
    ob_start();

?>
    <table style="width: 50%; border-collapse: collapse; margin: 0 auto">
        <thead>
            <tr style="background-color: #dcdcdc">
                <th colspan="2" style="
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center;
                    ">
                    <?php echo esc_html(__('METHOD OF PAYMENT', 'text-domain')); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($payment_methods as $index => $method_name):
                $is_selected = ($is_valid_selection && $index === $selected_method_index);
                $mark_content = $is_selected ? 'X' : '';
                $mark_style = 'border: 1px solid black; padding: 8px; text-align: center;';
                if ($is_selected) {
                    $mark_style .= ' font-weight: bold;';
                }
            ?>
                <tr>
                    <td style="border: 1px solid black; padding: 8px; width: 80%">
                        <?php echo esc_html($method_name); ?>
                    </td>
                    <td style="<?php echo $mark_style; ?>">
                        <?php echo $mark_content; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php

    // Capture the buffer content and return it as a string.
    return ob_get_clean();
}

function new_table_notes_html($student_id, $projection)
{
    $is_certified_approved = get_status_approved('CERTIFIED NOTES HIGH SCHOOL', $student_id);
    $projections = json_decode($projection->projection, false);

    if (empty($projections)) {
        return '';
    }

    $rows = [];
    foreach ($projections as $item) {
        $subject = get_subject_details($item->subject_id);
        $is_equivalence = ($subject->type === 'equivalence');

        // Determinar valores de forma clara y sin duplicación de lógica
        $period_name = $is_equivalence
            ? ($is_certified_approved ? 'Transfer Credit Evaluated' : '-')
            : (get_period_details_code($item->code_period)->name ?? '-');

        $status = $is_equivalence ? ($is_certified_approved ? 'ATT' : '-') : 'T';

        $note_grade = $is_equivalence ? ($is_certified_approved ? 'A' : '-') : get_literal_note($item->calification);

        $note_gpa = $is_equivalence ? ($is_certified_approved ? '*' : '-') : get_calc_note($item->calification);

        $course_title = $item->code_subject . ' - ' . $item->subject;
        if (isset($item->is_elective) && $item->is_elective) {
            $course_title .= ' (ELECTIVE)';
        }

        // Construir la fila
        $rows[] = <<<ROW
            <tr>
                <td>{$period_name}</td>
                <td>{$course_title}</td>
                <td>{$status}</td>
                <td>{$note_grade}</td>
                <td>{$note_gpa}</td>
            </tr>
        ROW;
    }

    $html_rows = implode('', $rows);

    $html = <<<HTML
        <table class='wp-list-table widefat fixed posts striped' style='margin-top: 20px; border: 1px dashed #c3c4c7;' id="tablenotcustom">
            <thead>
                <tr>
                    <th style='width: 70px;'>Semester / Academic Year</th>
                    <th>Course Code and Title</th>
                    <th style='width: 40px;'>Status</th>
                    <th style='width: 40px;'>Grade</th>
                    <th style='width: 40px;'>GPA</th>
                </tr>
            </thead>
            <tbody>
                {$html_rows}
            </tbody>
        </table>
    HTML;

    return $html;
}

function table_inscriptions_html($inscriptions)
{
    // Mapeo de estados para reutilizar la lógica
    $status_map = [
        0 => ['color' => 'gray', 'label' => __('To begin', 'edusystem')],
        1 => ['color' => 'blue', 'label' => __('Active', 'edusystem')],
        2 => ['color' => 'red', 'label' => __('Unsubscribed', 'edusystem')],
        3 => ['color' => 'green', 'label' => __('Approved', 'edusystem')],
        4 => ['color' => 'red', 'label' => __('Reproved', 'edusystem')],
    ];

    // Pre-carga de detalles de materias para evitar consultas repetidas
    $subject_details_cache = [];
    $processed_inscriptions = [];

    foreach ($inscriptions as $inscription) {
        $code_subject = $inscription->code_subject;
        if (!isset($subject_details_cache[$code_subject])) {
            $subject_details_cache[$code_subject] = get_subject_details_code($code_subject);
        }
        $processed_inscriptions[] = $inscription;
    }

    // Inicio de la plantilla HTML usando Heredoc para mejor legibilidad
    $html = <<<HTML
<table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px; border: 1px dashed #c3c4c7;" id="tablenotcustom">
    <thead>
        <tr>
            <th scope="col" class="manage-column" style="width: 90px;">Status</th>
            <th scope="col" class="manage-column">Subject - Code</th>
            <th scope="col" class="manage-column" style="width: 80px;">Period - cut</th>
            <th scope="col" class="manage-column" style="width: 80px;">Calification</th>
        </tr>
    </thead>
    <tbody>
HTML;

    foreach ($processed_inscriptions as $inscription) {
        $subject = $subject_details_cache[$inscription->code_subject];
        $name_subject = $subject ? "{$subject->name} - {$subject->code_subject}" : 'N/A';

        $status_info = $status_map[$inscription->status_id] ?? ['color' => 'black', 'label' => 'N/A'];

        $calification = isset($inscription->calification) && is_numeric($inscription->calification)
            ? number_format((float) $inscription->calification, 2)
            : 'N/A';

        // Usar sprintf para construir la fila, sanando los datos
        $html .= sprintf(
            '<tr>
                <td><div style="color: %s; font-weight: 600">%s</div></td>
                <td>%s</td>
                <td>%s - %s</td>
                <td>%s</td>
            </tr>',
            esc_attr($status_info['color']),
            strtoupper(esc_html($status_info['label'])),
            esc_html($name_subject),
            esc_html($inscription->code_period),
            esc_html($inscription->cut_period),
            esc_html($calification)
        );
    }

    $html .= <<<HTML
    </tbody>
</table>
HTML;

    return $html;
}

function table_notes_period_html($inscriptions)
{
    // Mapeo de estados para reutilizar la lógica y evitar el switch
    $status_map = [
        0 => ['color' => 'gray', 'label' => __('To begin', 'edusystem')],
        1 => ['color' => 'blue', 'label' => __('Active', 'edusystem')],
        2 => ['color' => 'red', 'label' => __('Unsubscribed', 'edusystem')],
        3 => ['color' => 'green', 'label' => __('Approved', 'edusystem')],
        4 => ['color' => 'red', 'label' => __('Reproved', 'edusystem')],
    ];

    // Pre-carga de detalles de materias para evitar consultas repetidas
    $subject_details_cache = [];
    foreach ($inscriptions as $inscription) {
        $code_subject = $inscription->code_subject;
        if (!isset($subject_details_cache[$code_subject])) {
            $subject_details_cache[$code_subject] = get_subject_details_code($code_subject);
        }
    }

    // Inicio de la plantilla HTML usando Heredoc para mejor legibilidad
    $html = <<<HTML
<table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px; border: 1px dashed #c3c4c7;" id="tablenotcustom">
    <thead>
        <tr>
            <th scope="col" class="manage-column">Subject - Code</th>
            <th scope="col" class="manage-column">Calification</th>
            <th scope="col" class="manage-column">Status</th>
        </tr>
    </thead>
    <tbody>
HTML;

    foreach ($inscriptions as $inscription) {
        $subject = $subject_details_cache[$inscription->code_subject];
        $name_subject = $subject ? "{$subject->name} - {$subject->code_subject}" : 'N/A';

        $status_info = $status_map[$inscription->status_id] ?? ['color' => 'black', 'label' => 'N/A'];

        $calification = isset($inscription->calification) && is_numeric($inscription->calification)
            ? number_format((float) $inscription->calification, 2)
            : 'N/A';

        // Usar sprintf para construir la fila, sanando los datos
        $html .= sprintf(
            '<tr>
                <td>%s</td>
                <td>%s</td>
                <td><div style="color: %s; font-weight: 600">%s</div></td>
            </tr>',
            esc_html($name_subject),
            esc_html($calification),
            esc_attr($status_info['color']),
            strtoupper(esc_html($status_info['label']))
        );
    }

    $html .= <<<HTML
    </tbody>
</table>
HTML;

    return $html;
}

function table_notes_summary_html($projection)
{
    $sum_quality = 0.0;
    $earned_ch = 0;
    $total_completed_courses = 0;

    // Decodificar la proyección una sola vez y manejar un posible error
    $projections_data = json_decode($projection->projection);
    if (empty($projections_data)) {
        return '<p>' . __('No data to show.', 'edusystem') . '</p>';
    }

    foreach ($projections_data as $projection_for) {
        $earned_ch += (int) $projection_for->hc;

        // Validar que la calificación sea un valor numérico y el curso esté completado
        if (
            isset($projection_for->is_completed) && $projection_for->is_completed &&
            isset($projection_for->calification) && is_numeric($projection_for->calification)
        ) {
            $total_completed_courses++;
            $sum_quality += get_calc_note((float) $projection_for->calification);
        }
    }

    // Calcular el GPA de forma segura
    $gpa = ($total_completed_courses > 0) ? round($sum_quality / $total_completed_courses, 2) : 0;

    // Usar la sintaxis de Heredoc para una plantilla HTML más limpia y segura
    $html = <<<HTML
<table class='wp-list-table widefat fixed posts striped' style='margin-top: 20px; border: 1px dashed #c3c4c7;' id="tablenotcustom">
    <tbody>
        <tr><td colspan='12'>Total Quality Points: %s</td></tr>
        <tr><td colspan='12'>Earned CH: %s</td></tr>
        <tr><td colspan='12'>GPA: %s</td></tr>
    </tbody>
</table>
HTML;

    // Sanear y formatear los datos antes de inyectarlos en el HTML
    return sprintf(
        $html,
        esc_html($sum_quality),
        esc_html($earned_ch),
        esc_html($gpa)
    );
}

function get_ethnicity_selected_html(int|string|null $selected_ethnicity_index): string
{
    // Define las etnicidades y (opcionalmente) un mapeo de índices
    $ethnicities = [
        1 => 'AFRICAN AMERICAN',
        2 => 'ASIAN',
        3 => 'CAUCASIAN',
        4 => 'HISPANIC',
        5 => 'NATIVE AMERICAN',
        6 => 'OTHER',
        7 => 'CHOOSE NOT TO RESPOND'
    ];

    // Convertir a int si es un string que representa un número
    if (is_string($selected_ethnicity_index) && ctype_digit($selected_ethnicity_index)) {
        $selected_ethnicity_index = (int) $selected_ethnicity_index;
    }

    if (empty($selected_ethnicity_index) || !is_int($selected_ethnicity_index) || !array_key_exists($selected_ethnicity_index, $ethnicities)) {
        // En lugar de devolver un HTML simple, es mejor empezar con el buffering y manejar la condición dentro
        ob_start();
    ?>
        <p><?php echo esc_html(__('No data to show.', 'edusystem')); ?></p>
    <?php
        return ob_get_clean();
    }

    // 1. Iniciar el buffering de salida para capturar el HTML
    ob_start();

    // 2. Escribir el HTML directamente, usando la sintaxis de plantillas de PHP
    ?>
    <p>
        <?php
        // Usamos 'endforeach;' y 'endif;' para un HTML más limpio y legible
        foreach ($ethnicities as $index => $ethnicity_name):
            // 3. La lógica de comparación es clara: el índice actual vs. el índice seleccionado
            $is_selected = ($index === $selected_ethnicity_index);

            // Agregar un separador, excepto antes del primer elemento
            if ($index > 0) {
                echo ' - ';
            }

            if ($is_selected):
                // **IMPORTANTE**: Usar funciones de escape (ej: esc_html) si el valor viene de una fuente no confiable
                // Aunque en este caso viene del array local, es buena práctica para texto
                // Usamos <strong> y <u> en lugar de estilos en línea
        ?>
                <strong style="text-decoration: underline;"><?php echo esc_html($ethnicity_name); ?></strong>
            <?php else: ?>
                <?php echo esc_html($ethnicity_name); ?>
        <?php
            endif;
        endforeach;
        ?>
    </p>
    <?php

    // 4. Capturar el contenido del buffer y devolverlo como un string
    return ob_get_clean();
}

function get_language_selected_html(string|null $selected_lang_index): string
{
    // Define las etnicidades y (opcionalmente) un mapeo de índices
    $langs = [
        'en_EN' => 'ENGLISH',
        'es_ES' => 'SPANISH'
    ];

    if (empty($selected_lang_index) || !array_key_exists($selected_lang_index, $langs)) {
        // En lugar de devolver un HTML simple, es mejor empezar con el buffering y manejar la condición dentro
        ob_start();
    ?>
        <p><?php echo esc_html(__('No data to show.', 'edusystem')); ?></p>
    <?php
        return ob_get_clean();
    }

    // 1. Iniciar el buffering de salida para capturar el HTML
    ob_start();

    // 2. Escribir el HTML directamente, usando la sintaxis de plantillas de PHP
    ?>
    <p>
        <?php
        // Usamos 'endforeach;' y 'endif;' para un HTML más limpio y legible
        foreach ($langs as $index => $lang_name):
            // 3. La lógica de comparación es clara: el índice actual vs. el índice seleccionado
            $is_selected = ($index === $selected_lang_index);

            // Agregar un separador, excepto antes del primer elemento
            echo ' - ';

            if ($is_selected):
                // **IMPORTANTE**: Usar funciones de escape (ej: esc_html) si el valor viene de una fuente no confiable
                // Aunque en este caso viene del array local, es buena práctica para texto
                // Usamos <strong> y <u> en lugar de estilos en línea
        ?>
                <strong style="text-decoration: underline;"><?php echo esc_html($lang_name); ?></strong>
            <?php else: ?>
                <?php echo esc_html($lang_name); ?>
        <?php
            endif;
        endforeach;
        ?>
    </p>
<?php

    // 4. Capturar el contenido del buffer y devolverlo como un string
    return ob_get_clean();
}

function get_signature_section($student): string
{
    $lastNameParts = array_filter([$student->last_name, $student->middle_last_name]);
    $firstNameParts = array_filter([$student->name, $student->middle_name]);

    $student_full_name = '';

    if (!empty($lastNameParts)) {
        $student_full_name .= implode(' ', $lastNameParts);
    }

    if (!empty($firstNameParts)) {
        if (!empty($student_full_name)) {
            $student_full_name .= ', ';
        }
        $student_full_name .= implode(' ', $firstNameParts);
    }
    $student_short_name = implode(' ', array_filter([$student->name, $student->last_name]));
    $user_partner = get_user_by('id', $student->partner_id);
    $parent_full_name = $user_partner ? trim($user_partner->first_name . ' ' . $user_partner->last_name) : '';
    $age = floor((time() - strtotime($student->birth_date)) / 31536000);
    $show_parent_info = 1;
    if ($age >= 18) {
        $show_parent_info = 0;
    }
    ob_start();
?>
    <input type="hidden" name="auto_signature_student" value="0">
    <div class="signatures_squares">
        <div class="signature_square_field">
            <div>
                <div style="padding: 8px; text-align: center"><strong><?= __('Signature of applicant:', 'edusystem') ?></strong>
                    <br> <?= $student_full_name ?>
                </div>
            </div>
            <div style="position: relative; padding: 8px;" id="signature-pad-student">
                <canvas id="signature-student" width="100%" height="200"
                    style="border: 1px solid gray; margin: auto !important; background-color: #ffff005c"></canvas>
                <div id="sign-here-student"
                    style="pointer-events: none;position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; padding: 10px; color: #4f4e4e7a; font-size: 20px;">
                    <span><?= __('SIGN HERE', 'edusystem'); ?></span>
                </div>
            </div>
            <button id="clear-student" style="width: 100%;"><?= __('Clear', 'edusystem'); ?></button>
            <button id="generate-signature-student" style="width: 100%;"
                onclick="autoSignature('signature-pad-student', 'signature-text-student', 'generate-signature-student', 'clear-student')"><?= __('Generate signature automatically', 'edusystem') ?></button>
            <div style="position: relative; padding: 8px; text-align: center; width: 70%; margin: 8px auto; border-bottom: 1px solid gray; font-family: Great Vibes, cursive; font-size: 28px; display: block; height: 120px; display: none"
                id="signature-text-student">
                <div style="bottom: 0; position: absolute; text-align: center; width: 100%;">
                    <?= $student_short_name ?>
                </div>
            </div>
            <button id="clear-student-signature"
                style="width: 100%; display: none"><?= __('Cancel', 'edusystem') ?></button>
        </div>
        <?php if ($show_parent_info == 1) { ?>
            <input type="hidden" name="auto_signature_parent" value="0">
            <div class="signature_square_field">
                <div>
                    <div style="padding: 8px; text-align: center"><strong><?= __('Signature of Parent/Legal Guardian:', 'edusystem') ?></strong>
                        <br> <?= $parent_full_name ?>
                    </div>
                </div>
                <div style="position: relative; padding: 8px;" id="signature-pad-parent">
                    <canvas id="signature-parent" width="100%" height="200"
                        style="border: 1px solid gray; margin: auto !important;  background-color: #ffff005c"></canvas>
                    <div id="sign-here-parent"
                        style="pointer-events: none;position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; padding: 10px; color: #4f4e4e7a; font-size: 20px;">
                        <span><?= __('SIGN HERE', 'edusystem') ?></span>
                    </div>
                </div>
                <button id="clear-parent" style="width: 100%;"><?= __('Clear', 'edusystem') ?></button>
                <button id="generate-signature-parent" style="width: 100%;"
                    onclick="autoSignature('signature-pad-parent', 'signature-text-parent', 'generate-signature-parent', 'clear-parent')"><?= __('Generate signature automatically', 'edusystem') ?></button>
                <div style="    position: relative; padding: 8px; text-align: center; width: 70%; margin: 8px auto; border-bottom: 1px solid gray; font-family: Great Vibes, cursive; font-size: 28px; display: block; height: 120px; display: none"
                    id="signature-text-parent">
                    <div style="bottom: 0; position: absolute; text-align: center; width: 100%;">
                        <?= $parent_full_name ?>
                    </div>
                </div>
                <button id="clear-parent-signature"
                    style="width: 100%; display: none"><?= __('Cancel', 'edusystem') ?></button>
            </div>
        <?php } ?>
    </div>
<?php

    return ob_get_clean();
}

function get_signature_section_fgu($student): string
{
    ob_start();
?>
    <div>
        <div style="padding: 8px; text-align: center"><strong><?= __('Signature of FGU Official:', 'edusystem') ?></strong></div>
        <img style="width: 160px; margin: 25px auto;" src="http://portal.floridaglobal.university/wp-content/uploads/2025/11/signature-admission-fgu.png" alt="">
    </div>
<?php

    return ob_get_clean();
}

function get_payment_plan_table(int $student_id): string
{
    global $wpdb;
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $program_data = get_program_data_student($student_id);
    $payments = $wpdb->get_results("SELECT * FROM {$table_student_payments} WHERE student_id={$student_id} ORDER BY cuote ASC");
    $program = $program_data['program'][0];
    $plan = $program_data['plan'][0];
    $fees = get_fees_associated_plan_complete($plan->identificator);
    // 1. Initialize variables for dynamic prices
    $tuition_price = 0.00;
    $registration_fee_price = 0.00;
    $graduation_fee_price = 0.00;
    $undergraduate_program_total = 0.00;
    $adendum_scholarship_price = 0.00;
    $tech_library_fees = 0.00;

    foreach ($payments as $key => $payment) {
        $product_id = (isset($payment->variation_id) && $payment->variation_id != 0) ? $payment->variation_id : $payment->product_id;
        $product = wc_get_product($product_id);
        if ($product) {
            // Check if the product belongs to the 'programs' category
            if (has_term('programs', 'product_cat', $product_id)) {
                $tuition_price = $payment->original_amount;
                $adendum_scholarship_price = $payment->discount_amount;
            }
        }
    }

    // 2. Iterate and filter the $fees array
    // We sum the prices for 'registration' and 'graduation' types.
    foreach ($fees as $fee) {
        if (is_object($fee) && property_exists($fee, 'type_fee') && property_exists($fee, 'price')) {
            $price = (float) $fee->price;

            switch ($fee->type_fee) {
                case 'registration':
                    $registration_fee_price += $price;
                    break;
                case 'graduation':
                    $graduation_fee_price += $price;
                    break;
                    // Other fee types like 'others' (100.00 in the log) are ignored 
                    // for this specific table structure as they don't have a dedicated row.
            }
        }
    }

    $undergraduate_program_total = ($tuition_price + $registration_fee_price + $graduation_fee_price + $tech_library_fees) - $adendum_scholarship_price;

    ob_start();

?>
    <table style="width: 100%; border-collapse: collapse; margin: 0 !important">
        <thead>
            <tr style="background-color: #dcdcdc">
                <th style="
                        border: 1px solid black;
                        padding: 8px;
                        text-align: left;
                        width: 40%;
                    "></th>
                <th style="
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center;
                        width: 30%;
                    ">
                    <strong>Undergraduate Program:</strong>
                </th>
                <th style="
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center;
                        width: 30%;
                    ">
                    <strong>Graduate Program:</strong>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid black; padding: 8px">
                    Tuition
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'undergraduated' ? wc_price($tuition_price) : '-' ?></strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'graduated' ? wc_price($tuition_price) : '-' ?></strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px">
                    Application for Admission Fee (non-refundable)
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'undergraduated' ? wc_price($registration_fee_price) : '-' ?></strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'graduated' ? wc_price($registration_fee_price) : '-' ?></strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px">
                    Technology Fee
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong>-</strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong>-</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px">Library Fee</td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong>-</strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong>-</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px">
                    Graduation Fee
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'undergraduated' ? wc_price($graduation_fee_price) : '-' ?></strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'graduated' ? wc_price($graduation_fee_price) : '-' ?></strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 8px">
                    Adendum (Scholarship)
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'undergraduated' ? wc_price($adendum_scholarship_price) : '-' ?></strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'graduated' ? wc_price($adendum_scholarship_price) : '-' ?></strong>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #f0f0f0">
                <td style="border: 1px solid black; padding: 8px">
                    <strong>Total:</strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'undergraduated' ? wc_price($undergraduate_program_total) : '-' ?></strong>
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center">
                    <strong><?= $program->type === 'graduated' ? wc_price($undergraduate_program_total) : '-' ?></strong>
                </td>
            </tr>
        </tbody>
    </table>
<?php

    return ob_get_clean();
}

function get_educational_background_information_table(int $student_id, $form_filled = null): string
{
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];
    $type = $program->type;

    ob_start();
?>
    <?php if ($type == 'undergraduated') { ?>
        <div style="
            padding: 8px 15px;
            font-weight: bold;
            border: 1px solid gray;
            border-top: none;
            background-color: #f0f0f0;
        ">
            HIGH SCHOOL
        </div>
        <table style="width: 100%; border-collapse: collapse; margin: 0 !important">
            <thead style="background-color: #dcdcdc">
                <tr>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 50%;
                ">
                        Name of Secondary School/ City/ Country:
                    </th>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 30%;
                ">
                        Major:
                    </th>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 20%;
                ">
                        Degree awarded (year):
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; padding: 8px; vertical-align: top">
                        <div style="font-weight: bold">Name:</div>
                        <?= ($form_filled && $type == 'undergraduated') ? $form_filled['step_3']['institution'] : 'N/A' ?>
                        <div style="font-weight: bold; margin-top: 20px">
                            City & Country:
                        </div>
                        <?= ($form_filled && $type == 'undergraduated') ? $form_filled['step_3']['city'] . ' / ' . $form_filled['step_3']['institution_country_residence'] : 'N/A' ?>
                    </td>
                    <td style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                vertical-align: middle;
                ">
                        <?= ($form_filled && $type == 'undergraduated') ? $form_filled['step_3']['title_obtained'] : 'N/A' ?>
                    </td>
                    <td style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                vertical-align: middle;
                ">
                        <?= ($form_filled && $type == 'undergraduated') ? $form_filled['step_3']['graduation_year'] : 'N/A' ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php } ?>

    <?php if ($type == 'graduated') { ?>
        <div style="
            padding: 8px 15px;
            font-weight: bold;
            border: 1px solid gray;
            border-top: none;
            background-color: #f0f0f0;
        ">
            COLLEGES & UNIVERSITIES
        </div>
        <table style="width: 100%; border-collapse: collapse; margin: 0 !important">
            <thead style="background-color: #dcdcdc">
                <tr>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 50%;
                ">
                        Name of Secondary School/ City/ Country:
                    </th>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 30%;
                ">
                        Major:
                    </th>
                    <th style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                width: 20%;
                ">
                        Degree awarded (year):
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; padding: 8px; vertical-align: top">
                        <div style="font-weight: bold">Name:</div>
                        <?= ($form_filled && $type == 'graduated') ? $form_filled['step_3']['institution'] : 'N/A' ?>
                        <div style="font-weight: bold; margin-top: 20px">
                            City & Country:
                        </div>
                        <?= ($form_filled && $type == 'graduated') ? $form_filled['step_3']['city'] . ' / ' . $form_filled['step_3']['institution_country_residence'] : 'N/A' ?>
                    </td>
                    <td style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                vertical-align: middle;
                ">
                        <?= ($form_filled && $type == 'graduated') ? $form_filled['step_3']['title_obtained'] : 'N/A' ?>
                    </td>
                    <td style="
                border: 1px solid black;
                padding: 8px;
                text-align: center;
                vertical-align: middle;
                ">
                        <?= ($form_filled && $type == 'graduated') ? $form_filled['step_3']['graduation_year'] : 'N/A' ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php } ?>
<?php
    return ob_get_clean();
}

function get_admission_requirements_table(int $student_id): string
{
    $program_data = get_program_data_student($student_id);
    $program = $program_data['program'][0];
    $type = $program->type;

    ob_start();
?>
    <section>
        <div style="padding: 8px; border: 1px solid gray; border-top: none">
            <strong style="display: block; margin-bottom: 10px">Admission Requirements:</strong>
            <strong style="display: block; margin-bottom: 5px"><?= ucfirst($type) ?>:</strong>
            <?php if ($type === 'undergraduated') { ?>
                <ul style="list-style-type: none; padding-left: 0; margin-top: 0">
                    <li style="margin-bottom: 5px">
                        1. GOVERNMENT PHOTO ID. (IDENTITY DOCUMENT OR PASSPORT OR DRIVER'S
                        LICENSE OR IDENTITY CARD)
                    </li>
                    <li style="margin-bottom: 5px">
                        2. ORIGINAL UNDERGRADUATE DEGREE FROM A STATE LICENSED, OR A
                        GOVERNMENT RECOGNIZED U.S COLLEGE OR UNIVERSITY, OR AN EQUIVALENT
                        DEGREE FROM COLLEGE OR UNIVERSITY OUTSIDE OF THE UNITED STATES
                    </li>
                    <li style="margin-bottom: 5px">
                        3. ORIGINAL HIGH SCHOOL DIPLOMA, GED, OR PROOF OF SECONDARY
                        EDUCATION
                    </li>
                    <li style="margin-bottom: 5px">
                        4. OFFICIAL TRANSCRIPTS ORIGINAL HIGH SCHOOL GED
                    </li>
                    <li style="margin-bottom: 5px">
                        5. TRANSLATION OR EQUIVALENT HIGH SCHOOL OR GED BY RECOGNIZED
                        INSTITUTION
                    </li>
                    <li style="margin-bottom: 5px">6. STUDENT APPLICATION</li>
                    <li style="margin-bottom: 5px">
                        7. PAYMENT RECEIVED (Application Fee)
                    </li>
                    <li style="margin-bottom: 5px">8. ONLINE REQUIREMENTS</li>
                    <li style="margin-bottom: 5px">
                        9. MISSING DOCUMENT COMMITMENT LETTER
                    </li>
                    <li style="margin-bottom: 5px">10. SCHOLARSHIP REQUEST FORM</li>
                    <li style="margin-bottom: 5px">
                        11. CERTIFICATION LETTER (AGREEMENT) (_____________________)
                    </li>
                    <li style="margin-bottom: 5px">12. RECORD OF CLOSING OF FILE</li>
                    <li style="margin-bottom: 5px">13. ENROLLMENT</li>
                    <li style="margin-bottom: 5px">14. ACCEPTANCE LETTER SPANISH</li>
                    <li style="margin-bottom: 5px">15. ACCEPTANCE LETTER</li>
                    <li style="margin-bottom: 5px">
                        16. ASSOCIATE DEGREE DIPLOMA (TSU OR TECHNICAL) FROM A NATIONAL OR
                        FOREIGN HIGHER EDUCATION INSTITUTION
                    </li>
                    <li style="margin-bottom: 5px">
                        17. REPORT OF OFFICIAL GRADES OF THE COURSES APPROVED IN A NATIONAL
                        OR FOREIGN HIGHER EDUCATION INSTITUTION.
                    </li>
                    <li style="margin-bottom: 5px">18. ACADEMIC TITLE - SCAN PAPER</li>
                    <li style="margin-bottom: 5px">19. PASSPORT OR PASSPORT PHOTO</li>
                </ul>
            <?php } else { ?>
                <ul style="list-style-type: none; padding-left: 0; margin-top: 0">
                    <li style="margin-bottom: 5px">
                        1. GOVERNMENT PHOTO ID. (IDENTITY DOCUMENT OR PASSPORT OR DRIVER'S LICENSE OR IDENTITY CARD)
                    </li>
                    <li style="margin-bottom: 5px">
                        2. ORIGINAL UNDERGRADUATE DEGREE FROM A STATE LICENSED, OR A GOVERNMENT RECOGNIZED U.S COLLEGE OR
                        UNIVERSITY, OR AN EQUIVALENT DEGREE FROM COLLEGE OR UNIVERSITY OUTSIDE OF THE UNITED STATES
                    </li>
                    <li style="margin-bottom: 5px">
                        3. OFFICIAL TRANSCRIPTS ORIGINAL HIGH SCHOOL GED OR UNDERGRADUATE DIPLOMA
                    </li>
                    <li style="margin-bottom: 5px">
                        4. TRASLATION OR EQUIVALENT HIGH SCHOOL OR UNDERGRADUATE DEGREE BY RECOGNIZED INSTITUTION
                    </li>
                    <li style="margin-bottom: 5px">
                        5. STUDENT APPLICATION
                    </li>
                    <li style="margin-bottom: 5px">6. PAYMENT RECEIVED (Application Fee)</li>
                    <li style="margin-bottom: 5px">
                        7. ONLINE REQUERIMENTS
                    </li>
                    <li style="margin-bottom: 5px">8. MISSING DOCUMENT COMMITMENT LETTER</li>
                    <li style="margin-bottom: 5px">9. SCHOLARSHIP REQUEST FORM</li>
                    <li style="margin-bottom: 5px">
                        10. CERTIFICATION LETTER (AGREEMENT) (_____________________)
                    </li>
                    <li style="margin-bottom: 5px">11. ENROLLMENT</li>
                    <li style="margin-bottom: 5px">12. ACCEPTANCE LETTER</li>
                    <li style="margin-bottom: 5px">13. ACCEPTANCE LETTER SPANISH</li>
                    <li style="margin-bottom: 5px">14. RECORD OF CLOSING OF FILE</li>
                    <li style="margin-bottom: 5px">15. ACADEMIC TITLE - SCAN PAPER</li>
                    <li style="margin-bottom: 5px">16. PASSPORT OR PASSPORT PHOTO</li>
                </ul>
            <?php } ?>
        </div>
    </section>
<?php
    return ob_get_clean();
}


function get_academic_ready($student_id)
{
    $student = get_student_detail($student_id);
    $regular_count = load_inscriptions_regular_valid($student, 'status_id = 3');
    $elective_count = load_inscriptions_electives_valid($student, 'status_id = 3');
    if ($regular_count >= 6 && $elective_count >= 2) {
        return true;
    }

    return false;
}

function update_max_upload_at($student_id)
{
    try {
        global $wpdb;
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $inscription = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id={$student_id} ORDER BY id ASC LIMIT 1");

        if (!$inscription || !$inscription->created_at) {
            return;
        }

        $today = date('Y-m-d');
        $date = date('Y-m-d', strtotime($inscription->created_at));
        $days = (int) get_option('proof_due');
        $max_date_proof = date('Y-m-d', strtotime("$date + $days days"));

        $wpdb->update($table_student_documents, [
            'max_date_upload' => $max_date_proof
        ], [
            'document_id' => 'PROOF OF STUDY',
            'student_id' => $student_id,
            'status' => ['<', 5]
        ]);
    } catch (\Throwable $th) {
        $wpdb->update($table_student_documents, [
            'max_date_upload' => NULL
        ], [
            'document_id' => 'PROOF OF STUDY',
            'student_id' => $student_id,
            'status' => ['<', 5]
        ]);
    }
}
