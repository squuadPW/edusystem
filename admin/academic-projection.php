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
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id} AND code_subject IS NOT NULL AND code_subject <> ''");
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
            $section = $_GET['section'];
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
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_academic_projection_student') {
            $student_id = $_GET['student_id'];
            generate_projection_student($student_id, true);

            setcookie('message', __('Successfully generated academic projections for the student.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_virtual_classroom') {
            $student_id = $_GET['student_id'];
            sync_student_with_moodle($student_id);

            setcookie('message', __('Successfully generated virtual classroom for the student.', 'edusystem'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=') . $student_id);
            exit;
        }
        if (isset($_GET['action']) && $_GET['action'] == 'generate_enrollments_moodle') {
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
        } else if (isset($_GET['action']) && $_GET['action'] == 'fix_projections') {
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_results("SELECT * FROM {$table_students} ORDER BY id DESC");
            foreach ($students as $key => $student) {
                fix_projections($student->id);
            }
            wp_redirect(admin_url('admin.php?page=add_admin_form_configuration_options_content'));
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
            get_moodle_notes();
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
            enroll_student($enrollments, $errors_count);
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

function get_moodle_notes()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    try {
        // Get last cut information
        $load = load_last_cut();
        $academic_period = $load['code'];
        $cut = $load['cut'];

        if (empty($cut)) {
            return;
        }

        // Get all relevant student IDs in one query
        $cut_student_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT student_id 
            FROM {$table_student_period_inscriptions} 
            WHERE code_period = %s 
            AND cut_period = %s 
            AND code_subject IS NOT NULL 
            AND code_subject <> ''",
            $academic_period,
            $cut
        ));

        if (empty($cut_student_ids)) {
            return;
        }

        // Get all students with moodle IDs in one query
        $students = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_students} 
            WHERE id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")
            AND moodle_student_id IS NOT NULL",
            $cut_student_ids
        ));

        if (empty($students)) {
            return;
        }

        // Get all projections for these students in one query
        $projections = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_student_academic_projection} 
            WHERE student_id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")",
            $cut_student_ids
        ));

        // Create a lookup array for projections
        $projections_lookup = array();
        foreach ($projections as $projection) {
            $projections_lookup[$projection->student_id] = $projection;
        }

        // Process each student
        foreach ($students as $student) {
            if (empty($student->moodle_student_id)) {
                continue;
            }

            // Get student assignments from Moodle
            $assignments = student_assignments_moodle_only_grades($student->id);
            if (empty($assignments['grades'])) {
                continue;
            }

            $projection_student = $projections_lookup[$student->id] ?? null;
            if (!$projection_student) {
                continue;
            }

            $projection_obj = json_decode($projection_student->projection);
            $updates_needed = false;

            // Process each course assignment
            foreach ($assignments['grades'] as $grade) {
                $course_id = (int) $grade['course_id'];
                $offer = get_offer_by_moodle($course_id);
                if (!$offer) {
                    continue;
                }

                foreach ($grade['grades'] as $grade_item) {
                    $grade_items = $grade_item['gradeitems'];

                    // Filter grade items without cmid
                    $filtered_grade_items = array_filter($grade_items, function ($item) {
                        return !isset($item['cmid']);
                    });
                    $filtered_grade_items = array_values($filtered_grade_items);

                    if (empty($filtered_grade_items)) {
                        continue;
                    }

                    $grade_value = (float) $filtered_grade_items[0]['gradeformatted'];
                    $total_grade = $grade_value > 100 ? 100 : $grade_value;

                    $subject = get_subject_details($offer->subject_id);
                    if (!$subject) {
                        continue;
                    }

                    $status_id = $total_grade >= $subject->min_pass ? 3 : 4;

                    // Update projection object
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

                            // Update inscription
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

            // Update projection if changes were made
            if ($updates_needed) {
                $wpdb->update(
                    $table_student_academic_projection,
                    ['projection' => json_encode($projection_obj)],
                    ['id' => $projection_student->id]
                );
            }
        }
    } catch (Exception $e) {
        // Log error or handle it appropriately
    }
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