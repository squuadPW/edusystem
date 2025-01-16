<?php

function add_admin_form_academic_projection_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'academic_projection_details') {
            global $wpdb;
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $projection_id = $_GET['projection_id'];
            $projection = get_projection_details($projection_id);
            $student = get_student_detail($projection->student_id);
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id}");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-detail.php');
        }

        if ($_GET['section_tab'] == 'validate_enrollments') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_students = $wpdb->prefix . 'students';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
            $history = [];
            $government = [];
            $english_tree = [];
            $english_four = [];
            $economic = [];
            $precalc = [];

            $academic_period = $_POST['academic_period'];
            $academic_period_cut = $_POST['academic_period_cut'];
            if ((isset($academic_period) && !empty($academic_period)) && (isset($academic_period_cut) && !empty($academic_period_cut))) {
                foreach ($projections as $key => $projection) {
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$projection->student_id}");
                    $projection_obj = json_decode($projection->projection);
                    $history_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'USH0914' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($history_arr)) > 0) {
                        $arr = array_values($history_arr);
                        array_push($history, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                    $government_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'GOV1016' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($government_arr)) > 0) {
                        $arr = array_values($government_arr);
                        array_push($government, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                    $english_tree_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'ENG1114' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($english_tree_arr)) > 0) {
                        $arr = array_values($english_tree_arr);
                        array_push($english_tree, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                    $english_four_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'EOSENG4' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($english_four_arr)) > 0) {
                        $arr = array_values($english_four_arr);
                        array_push($english_four, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                    $economic_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'EFL1216' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($economic_arr)) > 0) {
                        $arr = array_values($economic_arr);
                        array_push($economic, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                    $precalc_arr = array_filter($projection_obj, function ($item) use ($academic_period, $academic_period_cut) {
                        return $item->code_subject === 'PCL1211' && $item->code_period == $academic_period && $item->cut == $academic_period_cut;
                    });
                    if (count(array_values($precalc_arr)) > 0) {
                        $arr = array_values($precalc_arr);
                        array_push($precalc, ['student' => $student, 'calification' => $arr[0]->calification]);
                    }
    
                }
            }
            include(plugin_dir_path(__FILE__) . 'templates/academic-projection-validation.php');
        }
    } else {

        if ($_GET['action'] == 'generate_academic_projections') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_students = $wpdb->prefix . 'students';

            $students = $wpdb->get_results("SELECT * FROM {$table_students}");

            foreach ($students as $key => $student) {
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_student_academic_projection} WHERE student_id = %d",
                    $student->id
                ));

                if ($exists == 0) {
                    generate_projection_student($student->id, $student->grade_id);
                }
            }

            setcookie('message', __('Successfully generated all missing academic projections for the students.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if ($_GET['action'] == 'generate_enrollments_moodle') {
            generate_enroll_student();
            setcookie('message', __('Successfully generated all missing academic projections for the students.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        }  else if ($_GET['action'] == 'get_moodle_notes') {
            get_moodle_notes();
            setcookie('message', __('Successfully updated notes for the students.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if ($_GET['action'] == 'save_academic_projection') {
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

            // Procesar los datos
            foreach ($projection_obj as $key => $value) {
                $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_obj[$key]->subject_id}");

                $is_completed = isset($completed[$key]) ? true : false;
                $is_this_cut = isset($this_cut[$key]) ? true : false;
                $period = $academic_period[$key] ?? null;
                $cut = $academic_period_cut[$key] ?? null;
                $calification_value = $calification[$key] ?? null;

                $status_id = $is_this_cut ? 1 : ($calification_value >= $subject->min_pass ? 3 : 4);
                if ($status_id != 4) {
                    $projection_obj[$key]->is_completed = $is_completed;
                    $projection_obj[$key]->this_cut = $is_this_cut;
                    $projection_obj[$key]->code_period = $period;
                    $projection_obj[$key]->cut = $cut;
                    $projection_obj[$key]->calification = $calification_value;
                }

                if ($is_completed) {
                    $exist = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$projection->student_id} AND code_subject = '{$projection_obj[$key]->code_subject}' AND status_id != 4");
                    if (!isset($exist)) {
                        $wpdb->insert($table_student_period_inscriptions, [
                            'status_id' => $status_id,
                            'student_id' => $projection->student_id,
                            'subject_id' => $projection_obj[$key]->subject_id,
                            'code_subject' => $projection_obj[$key]->code_subject,
                            'code_period' => $period,
                            'cut_period' => $cut,
                            'calification' => $calification_value,
                        ]);
                    } else {
                        $wpdb->update($table_student_period_inscriptions, [
                            'status_id' => $status_id,
                            'student_id' => $projection->student_id,
                            'subject_id' => $projection_obj[$key]->subject_id,
                            'code_subject' => $projection_obj[$key]->code_subject,
                            'code_period' => $period,
                            'cut_period' => $cut,
                            'calification' => $calification_value,
                        ], ['id' => $exist->id]);
                    }
                }

            }

            $wpdb->update($table_student_academic_projection, [
                'projection' => json_encode($projection_obj) // Ajusta el valor de 'projection' según sea necesario
            ], ['id' => $projection->id]);

            if ($action == 'send_email') {
                $table_school_subjects = $wpdb->prefix . 'school_subjects';
                $table_students = $wpdb->prefix . 'students';
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$projection->student_id}");

                $filteredArray = array_filter($projection_obj, function ($item) {
                    return $item->this_cut === true;
                });
                $filteredArray = array_values($filteredArray);

                $text = '';
                $text .= '<div>
                    Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ' on behalf of the academic team of American Elite School, based in the city of Doral, Florida-USA, we are pleased to announce the beginning of Period C, corresponding to the School Year 2024 – 2025.
                </div><br>';

                $text .= '<div>';
                $text .= '<div><strong>START DATE:</strong> Monday, December 2, 2024</div>';
                $text .= '<div><strong>END DATE:</strong> Sunday, February 2, 2025</div>';
                $text .= '</div>';

                $text .= '<br>';

                $text .= '<div> Listed below is your <strong>Academic Load</strong> of mandatory courses registered for this Period C: </div>';

                if (count($filteredArray) > 0) {
                    $text .= '<table style="margin: 20px 0px">';
                    $text .= '<thead>
                        <tr>
                            <th style="border: 1px solid gray;">
                                <strong>CODE</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>SUBJECT</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>START DATE</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>END DATE</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>PERIOD</strong>
                            </th>
                        </tr>
                    </thead>';
                    $text .= '<tbody>';
                    foreach ($filteredArray as $key => $val) {
                        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
                        $text .= '<tr>';
                        $text .= '<td style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
                        $text .= '<td style="border: 1px solid gray;">' . $subject->name . '</td>';
                        $text .= '<td style="border: 1px solid gray;">12/02/24</td>';
                        $text .= '<td style="border: 1px solid gray;">02/02/25</td>';
                        $text .= '<td style="border: 1px solid gray;">C</td>';
                        $text .= '</tr>';
                    }
                    $text .= '</tbody>';
                    $text .= '</table>';
                }
                $text .= '<br>';
                $text .= '<div> Additionally, we would like to remind you of the relevant links and contacts: </div>';

                $text .= '<ul>';
                $text .= '<li>Website: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
                $text .= '<li>Virtual classroom: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
                $text .= '<li>Contact us: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
                $text .= '</ul>';

                // $text .= '<div> Contact us: </div>';
                // $text .= '<ul>';
                // $text .= '<li>Virtual Support: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
                // $text .= '<li>Academic Support: <a href="mailto:academic.support@american-elite.us" target="_blank">academic.support@american-elite.us</a></li>';
                // $text .= '<li>Student Services: <a href="mailto:student.services@american-elite.us" target="_blank">student.services@american-elite.us</a></li>';
                // $text .= '</ul>';

                $text .= '<div>On behalf of our institution, we thank you for your commitment and wish you a successful academic term.</div>';
                $text .= '<div style="margin: 10px 0px; border-bottom: 1px solid gray;"></div>';
                $text .= '<div>
                    Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ' en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos complace anunciarle el inicio del Periodo C correspondiente al Año Escolar 2024 – 2025.
                </div><br>';

                $text .= '<div>';
                $text .= '<div><strong>FECHA DE INICIO:</strong> lunes 2 de diciembre de 2024</div>';
                $text .= '<div><strong>FECHA DE CULMINACIÓN:</strong> domingo 2 de febrero de 2025</div>';
                $text .= '</div>';

                $text .= '<br>';

                $text .= '<div> A continuación, detallamos su <strong>Carga Académica</strong> de cursos ofertados para este periodo C: </div>';

                if (count($filteredArray) > 0) {
                    $text .= '<table style="margin: 20px 0px">';
                    $text .= '<thead>
                        <tr>
                            <th style="border: 1px solid gray;">
                               <strong>CÓDIGO</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>MATERIA</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>FECHA INICIO</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>FECHA FIN</strong>
                            </th>
                            <th style="border: 1px solid gray;">
                                <strong>PERIODO</strong>
                            </th>
                        </tr>
                    </thead>';
                    $text .= '<tbody>';
                    foreach ($filteredArray as $key => $val) {
                        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
                        $text .= '<tr>';
                        $text .= '<td style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
                        $text .= '<td style="border: 1px solid gray;">' . $subject->name . '</td>';
                        $text .= '<td style="border: 1px solid gray;">12/02/24</td>';
                        $text .= '<td style="border: 1px solid gray;">02/02/25</td>';
                        $text .= '<td style="border: 1px solid gray;">C</td>';
                        $text .= '</tr>';
                    }
                    $text .= '</tbody>';
                    $text .= '</table>';
                }
                $text .= '<br>';
                $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

                $text .= '<ul>';
                $text .= '<li>Página web: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
                $text .= '<li>Aula virtual: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
                $text .= '<li>Contacto: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
                $text .= '</ul>';

                // $text .= '<div> Contactos: </div>';
                // $text .= '<ul>';
                // $text .= '<li>Soporte Virtual: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
                // $text .= '<li>Soporte Académico: <a href="mailto:academic.support@american-elite.us" target="_blank">academic.support@american-elite.us</a></li>';
                // $text .= '<li>Servicios Estudiantiles: <a href="mailto:student.services@american-elite.us" target="_blank">student.services@american-elite.us</a></li>';
                // $text .= '</ul>';

                $text .= '<div>En nombre de nuestra institución, le agradecemos por su compromiso y le deseamos un periodo académico lleno de logros satisfactorios.</div>';

                $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
                $email_student->trigger($student, 'Welcome', $text);

                $user_parent = get_user_by('id', $student->partner_id);
                $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
                $email_student->trigger($user_parent, 'Welcome', $text);
            }

            $projection = get_projection_details($projection_id);
            $student = get_student_detail($projection->student_id);
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id}");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            setcookie('message', __('Projection adjusted successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else if ($_GET['action'] == 'delete_inscription') {
            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
            $projection_id = $_GET['projection_id'];
            $inscription_id = $_GET['inscription_id'] ?? [];
            $enrollment = get_enrollment_details($inscription_id);
            $projection = get_projection_details(projection_id: $projection_id);
            $projection_obj = json_decode($projection->projection);

            $subjectIds = array_column($projection_obj, 'code_subject');
            $indexToEdit = array_search($enrollment->code_subject, $subjectIds);
            if ($indexToEdit !== false) {
                $projection_obj[$indexToEdit]->cut = '';
                $projection_obj[$indexToEdit]->this_cut = false;
                $projection_obj[$indexToEdit]->code_period = '';
                $projection_obj[$indexToEdit]->calification = '';
                $projection_obj[$indexToEdit]->is_completed = false;
            }

            $wpdb->update($table_student_academic_projection, [
                'projection' => json_encode($projection_obj)
            ], ['id' => $projection->id]);

            $wpdb->delete($table_student_period_inscriptions, ['id' => $inscription_id]);

            $projection = get_projection_details($projection_id);
            $student = get_student_detail($projection->student_id);
            $inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id}");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            setcookie('message', __('Projection adjusted successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $projection_id));
            exit;
        } else {
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
                return strtoupper($item[$column_name]);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=academic_projection_details&projection_id=' . $item['academic_projection_id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
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
            'student' => __('Student', 'aes'),
            'initial_cut' => __('Initial period - cut', 'aes'),
            'grade_id' => __('Grade', 'aes'),
            'view_details' => __('Actions', 'aes'),
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

function generate_projection_student($student_id, $grade_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_elective = 0");

    $projection = [];

    foreach ($subjects as $key => $subject) {
        array_push($projection, ['code_subject' => $subject->code_subject, 'subject_id' => $subject->id, 'subject' => $subject->name, 'hc' => $subject->hc, 'cut' => "", 'code_period' => "", 'calification' => "", 'is_completed' => false, 'this_cut' => false]);
    }

    $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection) // Ajusta el valor de 'projection' según sea necesario
    ]);
}

function generate_enroll_student()
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';

    $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
    foreach ($projections as $key => $projection) {
        $projection_obj = json_decode($projection->projection);

        $filteredArray = array_filter($projection_obj, function ($item) {
            return $item->this_cut === true;
        });
        $filteredArray = array_values($filteredArray);

        foreach ($filteredArray as $key => $projection_filtered) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_filtered->subject_id}");
            enroll_student($projection->student_id, [(int) $subject->moodle_course_id]);
        }
    }
}


function get_moodle_notes()
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $students = $wpdb->get_results("SELECT * FROM {$table_students}");
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    foreach ($students as $key => $student) {
        $moodle_student_id = $student->moodle_student_id;

        if ($moodle_student_id) {
            $assignments = student_assignments_moodle($student->id);
            $assignments_course = $assignments['assignments'];
            $assignments_student = $assignments['grades'];
            $formatted_assignments = [];

            foreach ($assignments_course as $key => $assignment_c) {
                $projection_student = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student->id}");
                $course_id = (int) $assignment_c['id'];
                $filtered_course_student = array_filter($assignments_student, function ($entry) use ($course_id) {
                    return $entry['course_id'] == $course_id;
                });
                $filtered_course_student = array_values($filtered_course_student);

                if ($filtered_course_student[0]) {
                    $assignments_student_filtered = $filtered_course_student[0]['grades'][0]['gradeitems'];
                    $max_grade = 0;
                    $assignments_total = 0;
                    $total_grade = 0;

                    foreach ($assignments_student_filtered as $key => $work) {
                        if (!isset($work['cmid'])) {
                            $max_grade = (isset($work['gradeformatted']) && $work['gradeformatted'] != '') ? (float)$work['gradeformatted'] : 0;
                        } else {
                            $assignments_total = ($assignments_total + 1);
                        }
                    }

                    // $total_grade = ($max_grade / $assignments_total);
                    // usamos el total, la sumatoria de las 4 que en teoria deberia ser en base a 100
                    $total_grade = $max_grade;
                    if ($projection_student) {
                        $projection_obj = json_decode($projection_student->projection);
            
                        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE moodle_course_id = {$course_id}");
        
                        foreach ($projection_obj as $key => $prj) {
                            if ($prj->this_cut && $prj->subject_id == $subject->id) {
                                $prj->calification = $total_grade;
                                $prj->this_cut = false;

                                $status_id = $total_grade >= $subject->min_pass ? 3 : 4;
                                $wpdb->update($table_student_period_inscriptions, [
                                    'status_id' => $status_id,
                                    'calification' => $total_grade,
                                ], ['student_id' => $student->id, 'subject_id' => $subject->id]);
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

function get_literal_note($calification) {
    if(!$calification) {
        return 'N/A';
    }
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

function get_calc_note($calification) {
    if(!$calification) {
        return 'N/A';
    }
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