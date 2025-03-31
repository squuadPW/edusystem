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
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$subject_id}");
            $teacher = null;
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE code_period = '{$academic_period}' AND cut_period = '{$academic_period_cut}' AND (subject_id = {$subject_id} OR code_subject = '{$subject->code_subject}')");
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
        } else if (isset($_GET['action']) && $_GET['action'] == 'generate_enrollments_moodle') {
            generate_enroll_student();
            setcookie('message', __('Students successfully enrolled in moodle.', 'edusystem'), time() + 3600, '/');
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
            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_results("SELECT * FROM {$table_students} ORDER BY id DESC");
            foreach ($students as $key => $student) {
                send_welcome_subjects($student->id);
            }
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
                $is_this_cut = $this_cut[$key] ?? false;
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
                send_welcome_subjects($projection->student_id);
            }

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

        $query_search = "";
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search = $_GET['s'];
            $query_search = "WHERE (`name` LIKE '{$search}%' OR middle_name LIKE '{$search}%' OR last_name LIKE '{$search}%' OR middle_last_name LIKE '{$search}%' OR id_document LIKE '{$search}%' )";
        }

        $students_id = $wpdb->get_col("SELECT id FROM {$table_students} {$query_search}");
        if (!empty($students_id)) {
            $students_id_list = implode(',', array_map('intval', $students_id)); // Asegúrate de que los IDs sean enteros
        }

        $academic_projections = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_student_academic_projection} WHERE student_id IN ($students_id_list) ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($academic_projections) {
            foreach ($academic_projections as $projection) {
                $student = get_student_detail($projection['student_id']);
                array_push($academic_projections_array, [
                    'student' => $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name,
                    'student_id' => $projection['student_id'],
                    'initial_cut' => $student->academic_period . ' - ' . $student->initial_cut,
                    'academic_projection_id' => $projection['id'],
                    'grade_id' => get_name_grade($student->grade_id)
                ]);
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

function generate_enroll_student()
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    $enrollments = [];
    $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
    $errors = '';
    $errors_count = 0;
    foreach ($projections as $key => $projection) {
        $projection_obj = json_decode($projection->projection);

        $filteredArray = array_filter($projection_obj, function ($item) {
            return $item->this_cut === true;
        });
        $filteredArray = array_values($filteredArray);

        foreach ($filteredArray as $key => $projection_filtered) {
            $offer = get_offer_filtered($projection_filtered->subject_id, $code, $cut);
            if ($offer) {
                $enrollments = array_merge($enrollments, courses_enroll_student($projection->student_id, [(int) $offer->moodle_course_id]));
            } else {
                $student = get_student_detail($projection->student_id);
                if ($student) {
                    $errors .= 'The student ' . $student->last_name . ' ' . $student->middle_last_name . ' ' . $student->name . ' ' . $student->middle_name . ' could not be enrolled because no offers were found for the current period (' . $cut . ') <br>';
                    $errors_count++;
                }
            }
        }
    }

    enroll_student($enrollments, $errors_count);
    setcookie('message-error', $errors, time() + 3600, '/');
}

function get_moodle_notes()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $load = load_last_cut();
    $academic_period = $load['code'];
    $cut = $load['cut'];

    $conditions = array();
    $params = array();

    if (!empty($cut)) {
        $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
        $cut_student_ids = $wpdb->get_col("SELECT student_id FROM {$table_student_period_inscriptions} WHERE code_period = '$academic_period' AND cut_period = '$cut' AND code_subject IS NOT NULL AND code_subject <> ''");
        $conditions[] = "id IN (" . implode(',', array_fill(0, count($cut_student_ids), '%d')) . ")";
        $params = array_merge($params, $cut_student_ids);
    }

    $query = "SELECT * FROM {$table_students}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $students = $wpdb->get_results($wpdb->prepare($query, $params));
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    foreach ($students as $key => $student) {
        $moodle_student_id = $student->moodle_student_id;

        if ($moodle_student_id) {
            $projection_student = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student->id}");
            $assignments = student_assignments_moodle($student->id);
            $assignments_course = $assignments['assignments'];
            $assignments_student = $assignments['grades'];
            $formatted_assignments = [];

            foreach ($assignments_course as $key => $assignment_c) {
                $course_id = (int) $assignment_c['id'];
                $filtered_course_student = array_filter($assignments_student, function ($entry) use ($course_id) {
                    return $entry['course_id'] == $course_id;
                });
                $filtered_course_student = array_values($filtered_course_student);

                if (isset($filtered_course_student[0])) {
                    $assignments_student_filtered = $filtered_course_student[0]['grades'][0]['gradeitems'];
                    $max_grade = 0;
                    $assignments_total = 0;
                    $total_grade = 0;

                    foreach ($assignments_student_filtered as $key => $work) {
                        if (!isset($work['cmid'])) {
                            $max_grade = (isset($work['gradeformatted']) && $work['gradeformatted'] != '') ? (float) $work['gradeformatted'] : 0;
                        } else {
                            $assignments_total = ($assignments_total + 1);
                        }
                    }

                    // $total_grade = ($max_grade / $assignments_total);
                    // usamos el total, la sumatoria de las 4 que en teoria deberia ser en base a 100
                    $total_grade = ($max_grade > 100) ? 100 : $max_grade;
                    if ($projection_student) {
                        $projection_obj = json_decode($projection_student->projection);

                        $offer = get_offer_by_moodle($course_id);
                        $subject = get_subject_details($offer->subject_id);
                        $status_id = $total_grade >= $subject->min_pass ? 3 : 4;

                        foreach ($projection_obj as $key => $prj) {
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
                                    $prj->is_completed = true;
                                    $prj->welcome_email = true;
                                }

                                $query = $wpdb->prepare(
                                    "UPDATE {$table_student_period_inscriptions} 
                                    SET status_id = %d, 
                                        calification = %d 
                                    WHERE student_id = %d 
                                        AND status_id = %d 
                                        AND (subject_id = %d OR code_subject = %s)",
                                    $status_id,
                                    $total_grade,
                                    $student->id,
                                    1,
                                    $subject->id,
                                    $subject->code_subject
                                );

                                $wpdb->query($query);
                            }
                        }

                        $wpdb->update($table_student_academic_projection, [
                            'projection' => json_encode($projection_obj)
                        ], ['id' => $projection_student->id]);
                    }
                }
            }
        }
    }
}

function get_literal_note($calification)
{
    if (!$calification) {
        return '-';
    }
    $calification = (int) $calification;
    $note = 'A+';
    switch ($calification) {
        case $calification >= 95:
            $note = 'A+';
            break;
        case $calification >= 90 && $calification <= 94:
            $note = 'A-';
            break;
        case $calification >= 83 && $calification <= 89:
            $note = 'B+';
            break;
        case $calification >= 80 && $calification <= 82:
            $note = 'B-';
            break;
        case $calification >= 73 && $calification <= 79:
            $note = 'C+';
            break;
        case $calification >= 70 && $calification <= 72:
            $note = 'C-';
            break;
        case $calification >= 67 && $calification <= 69:
            $note = 'D+';
            break;
        case $calification >= 60 && $calification <= 66:
            $note = 'D-';
            break;
        case $calification <= 59:
            $note = 'F';
            break;
    }
    return $note;
}

function get_calc_note($calification)
{
    if (!$calification) {
        return '-';
    }
    $calification = (int) $calification;
    $note = 'abc';
    switch ($calification) {
        case $calification >= 95:
            $note = 4;
            break;
        case $calification >= 90 && $calification <= 94:
            $note = 3.75;
            break;
        case $calification >= 87 && $calification <= 89:
            $note = 3.50;
            break;
        case $calification >= 83 && $calification <= 86:
            $note = 3;
            break;
        case $calification >= 80 && $calification <= 82:
            $note = 2.75;
            break;
        case $calification >= 77 && $calification <= 79:
            $note = 2.50;
            break;
        case $calification >= 73 && $calification <= 76:
            $note = 2;
            break;
        case $calification >= 70 && $calification <= 72:
            $note = 1.75;
            break;
        case $calification >= 67 && $calification <= 69:
            $note = 1.50;
            break;
        case $calification >= 60 && $calification <= 66:
            $note = 1;
            break;
        case $calification <= 59:
            $note = 0;
            break;
    }
    return $note;
}

function get_count_moodle_pending() {
    global $wpdb;
    $table_count_pending_student = $wpdb->prefix . 'count_pending_student';
    $pending = $wpdb->get_row("SELECT * FROM {$table_count_pending_student} WHERE id = 1");
    return $pending->count;
}

function get_count_email_pending() {
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
}

function update_count_moodle_pending($count_fixed = '') {
    global $wpdb;
    $table_count_pending_student = $wpdb->prefix . 'count_pending_student';
    $count = $count_fixed != '' ? $count_fixed : (get_count_moodle_pending() + 1) ;
    $wpdb->update($table_count_pending_student, [
        'count' => $count
    ], ['id' => 1]);
} 

function table_notes_html($student_id) {
    $projection = get_projection_by_student($student_id);
    $html = '';
    
    $html .= "<table class='wp-list-table widefat fixed posts striped' style='margin-top: 20px; border: 1px dashed #c3c4c7 !important;'>
                <thead>
                    <tr>
                        <th colspan='2'>CODE</th>
                        <th colspan='4'>COURSE</th>
                        <th colspan='1'>CH</th>
                        <th colspan='1'>0-100</th>
                        <th colspan='1'>0-4</th>
                        <th colspan='3'>PERIOD</th>
                    </tr>
                </thead>
                <tbody>";

    // Procesar cada proyección
    foreach(json_decode($projection->projection) as $key => $projection_for) {
        $download_grades = get_status_approved('CERTIFIED NOTES HIGH SCHOOL', $student_id);
        $subject = get_subject_details($projection_for->subject_id);
        $period_name = '';
        $period = get_period_details_code($projection_for->code_period);
        if($period) $period_name = $period->name;
        
        // Construir fila
        $html .= "<tr>
                    <td colspan='2'>".$projection_for->code_subject."</td>
                    <td colspan='4'>".$projection_for->subject;
        
        // Electivo
        $html .= (isset($projection_for->is_elective) && $projection_for->is_elective ? '(ELECTIVE)' : '')."</td>";
        
        // CH
        $html .= "<td colspan='1'>";
        if($subject->type != 'equivalence') {
            $html .= $projection_for->hc;
        } else {
            $html .= $download_grades ? 'TR' : '-';
        }
        $html .= "</td>";
        
        // Nota 0-100
        $html .= "<td colspan='1'>";
        if(isset($projection_for->calification) && !empty($projection_for->calification)) {
            $html .= $projection_for->calification;
        } else {
            $html .= ($subject->type != 'equivalence') ? '-' : ($download_grades ? 'TR' : '-');
        }
        $html .= "</td>";
        
        // Nota 0-4
        $html .= "<td colspan='1'>";
        $html .= ($subject->type != 'equivalence') ? get_calc_note($projection_for->calification) : ($download_grades ? 'TR' : '-');
        $html .= "</td>";
        
        // Periodo
        $html .= "<td colspan='3'>".(!empty($period_name) ? $period_name : '-')."</td>
                </tr>";
    }

    $html .= "</tbody></table>";
    
    return $html;
}

function table_notes_summary_html($student_id) {
    $projection = get_projection_by_student($student_id);
    
    $sum_quality = 0;
    $earned_ch = 0;
    $total_quality = 0;
    $gpa = 0;

    foreach(json_decode($projection->projection) as $key => $projection_for) {
        if ($projection_for->is_completed && (int)$projection_for->calification) {
            $total_quality++;
            $earned_ch += (int)$projection_for->hc;
            $sum_quality += get_calc_note((int)$projection_for->calification);
        }
    }

    // Calcular GPA solo si hay materias completadas
    $gpa = ($total_quality > 0) ? ($sum_quality / $total_quality) : 0;

    $html = '';
    $html .= "<table class='wp-list-table widefat fixed posts striped' style='margin-top: 20px; border: 1px dashed #c3c4c7 !important;'>
                <tbody>
                    <tr><td colspan='12'>Total Quality Points: " . $sum_quality . "</td></tr>
                    <tr><td colspan='12'>Earned CH: " . $earned_ch . "</td></tr>
                    <tr><td colspan='12'>GPA: " . $gpa . "</td></tr>
                </tbody>
            </table>";
    
    return $html;
}

function get_academic_ready($student_id){
    $student = get_student_detail($student_id);
    $regular_count = load_inscriptions_regular_valid($student, 'status_id = 3');
    $elective_count = load_inscriptions_electives_valid($student, 'status_id = 3');
    if ($regular_count >= 6 && $elective_count >= 2) {
        return true;
    }

    return false;
}