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
            include (plugin_dir_path(__FILE__) . 'templates/academic-projection-detail.php');
        }

        if ($_GET['section_tab'] == 'validate_enrollments') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
            $table_students = $wpdb->prefix.'students';

            $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");    
            $history = [];
            $government = [];
            $english_tree = [];
            $english_four = [];
            $economic = [];
            $precalc = [];
            foreach ($projections as $key => $projection) {
                $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$projection->student_id}");
                $projection_obj = json_decode($projection->projection);
                $history_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'USH0914' && $item->this_cut == true;
                });
                if(count(array_values($history_arr)) > 0) {
                    array_push($history, $student);
                }

                $government_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'GOV1016' && $item->this_cut == true;
                });
                if(count(array_values($government_arr)) > 0) {
                    array_push($government, $student);
                }

                $english_tree_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'ENG1114' && $item->this_cut == true;
                });
                if(count(array_values($english_tree_arr)) > 0) {
                    array_push($english_tree, $student);
                }

                $english_four_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'EOSENG4' && $item->this_cut == true;
                });
                if(count(array_values($english_four_arr)) > 0) {
                    array_push($english_four, $student);
                }

                $economic_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'EFL1216' && $item->this_cut == true;
                });
                if(count(array_values($economic_arr)) > 0) {
                    array_push($economic, $student);
                }

                $precalc_arr = array_filter($projection_obj, function($item) {
                    return $item->code_subject === 'PCL1211' && $item->this_cut == true;
                });
                if(count(array_values($precalc_arr)) > 0) {
                    array_push($precalc, $student);
                }

            }
            include (plugin_dir_path(__FILE__) . 'templates/academic-projection-validation.php');
        }
    } else {

        if ($_GET['action'] == 'generate_academic_projections') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
            $table_students = $wpdb->prefix.'students';

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
        } else if($_GET['action'] == 'generate_enrollments_moodle') {
            generate_enroll_student();
            setcookie('message', __('Successfully generated all missing academic projections for the students.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else if($_GET['action'] == 'save_academic_projection') {
            global $wpdb;
            $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
            $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
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
                $is_completed = isset($completed[$key]) ? true : false;
                $is_this_cut = isset($this_cut[$key]) ? true : false;
                $period = $academic_period[$key] ?? null;
                $cut = $academic_period_cut[$key] ?? null;
                $calification_value = $calification[$key] ?? null;

                $projection_obj[$key]->is_completed = $is_completed;
                $projection_obj[$key]->this_cut = $is_this_cut;
                $projection_obj[$key]->code_period = $period;
                $projection_obj[$key]->cut = $cut;
                $projection_obj[$key]->calification = $calification_value;

                if (!$is_completed) {
                    $wpdb->delete($table_student_period_inscriptions,['code_subject' => $projection_obj[$key]->code_subject, 'student_id' => $projection->student_id]);

                    //borramos inscripcion moodle
                    if ($action != 'send_email') {
                        $projection_obj[$key]->this_cut = false;
                        unenroll_student($projection->student_id, [$projection_obj[$key]->moodle_course_id]);
                    }
                } else {
                    $exist = $wpdb->get_row("SELECT * FROM {$table_student_period_inscriptions} WHERE student_id = {$projection->student_id} AND code_subject = '{$projection_obj[$key]->code_subject}'");
                    if (!isset($exist)) {
                        $wpdb->insert($table_student_period_inscriptions, [
                            'status_id' => $projection_obj[$key]->this_cut ? 1 : 3,
                            'student_id' => $projection->student_id,
                            'code_subject' => $projection_obj[$key]->code_subject,
                            'code_period' => $projection_obj[$key]->code_period,
                            'cut_period' => $projection_obj[$key]->cut
                        ]);
                    } else {
                        $wpdb->update($table_student_period_inscriptions, [
                            'status_id' => $projection_obj[$key]->this_cut ? 1 : 3,
                            'student_id' => $projection->student_id,
                            'code_subject' => $projection_obj[$key]->code_subject,
                            'code_period' => $projection_obj[$key]->code_period,
                            'cut_period' => $projection_obj[$key]->cut
                        ], ['id' => $exist->id]);
                    }

                    //generamos inscripcion moodle
                    if ($action != 'send_email') {
                        $table_school_subjects = $wpdb->prefix . 'school_subjects';
                        if ($projection_obj[$key]->this_cut) {
                            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_obj[$key]->subject_id}");
                            enroll_student($projection->student_id, [$subject->moodle_course_id]);
                        } else {
                            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_obj[$key]->subject_id}");
                            unenroll_student($projection->student_id, [$subject->moodle_course_id]);
                        }
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

                $filteredArray = array_filter($projection_obj, function($item) {
                    return $item->this_cut === true;
                });
                $filteredArray = array_values($filteredArray);

                $text = '';
                $text .= '<div>
                    Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', '.  strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ' on behalf of the academic team of American Elite School, based in the city of Doral, Florida-USA, we are pleased to announce the beginning of Period C, corresponding to the School Year 2024 – 2025.
                </div><br>';

                $text .= '<div>';
                $text .= '<div><strong>START DATE:</strong> Monday, December 2, 2024</div>';
                $text .= '<div><strong>END DATE:</strong> Sunday, February 2, 2025</div>';
                $text .= '</div>';

                $text .= '<br>';

                $text .= '<div> Listed below is your <strong>Academic Load</strong> of mandatory courses registered for this Period C: </div>';

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
                    $text .= '<td style="border: 1px solid gray;">'.$subject->code_subject .'</td>';
                    $text .= '<td style="border: 1px solid gray;">'.$subject->name .'</td>';
                    $text .= '<td style="border: 1px solid gray;"></td>';
                    $text .= '<td style="border: 1px solid gray;"></td>';
                    $text .= '<td style="border: 1px solid gray;">C</td>';
                    $text .= '</tr>';
                }
                $text .= '</tbody>';
                $text .= '</table>';

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
                    Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', '.  strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ' en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos complace anunciarle el inicio del Periodo C correspondiente al Año Escolar 2024 – 2025.
                </div><br>';

                $text .= '<div>';
                $text .= '<div><strong>FECHA DE INICIO:</strong> lunes 2 de diciembre de 2024</div>';
                $text .= '<div><strong>FECHA DE CULMINACIÓN:</strong> domingo 2 de febrero de 2025</div>';
                $text .= '</div>';

                $text .= '<br>';

                $text .= '<div> A continuación, detallamos su <strong>Carga Académica</strong> de cursos ofertados para este periodo C: </div>';

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
                    $text .= '<td style="border: 1px solid gray;">'.$subject->code_subject .'</td>';
                    $text .= '<td style="border: 1px solid gray;">'.$subject->name .'</td>';
                    $text .= '<td style="border: 1px solid gray;"></td>';
                    $text .= '<td style="border: 1px solid gray;"></td>';
                    $text .= '<td style="border: 1px solid gray;">C</td>';
                    $text .= '</tr>';
                }
                $text .= '</tbody>';
                $text .= '</table>';

                $text .= '<br>';
                $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

                $text .= '<ul>';
                $text .= '<li>Página web: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
                $text .= '<li>Aula virtual: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
                $text .= '<li>Contact: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
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
    
            setcookie('message', __('Projection adjusted successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_projection_content'));
            exit;
        } else {
            $list_academic_projection = new TT_academic_projection_all_List_Table;
            $list_academic_projection->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-academic-projection.php');
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
            ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'student':
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
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_academic_projections()
    {
        global $wpdb;
        $academic_projections_array = [];
        $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
        $table_students = $wpdb->prefix.'students';

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $query_search = "";
        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search = $_GET['s'];
            $query_search  = "WHERE (`name` LIKE '{$search}%' OR middle_name LIKE '{$search}%' OR last_name LIKE '{$search}%' OR middle_last_name LIKE '{$search}%' OR id_document LIKE '{$search}%' )";
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
    $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';

    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE id={$projection_id}");
    return $projection;
}

function generate_projection_student($student_id, $grade_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
    $table_school_subjects = $wpdb->prefix.'school_subjects';
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
    $table_student_academic_projection = $wpdb->prefix.'student_academic_projection';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';

    $projections = $wpdb->get_results("SELECT * FROM {$table_student_academic_projection}");
    foreach ($projections as $key => $projection) {
        $projection_obj = json_decode($projection->projection);

        $filteredArray = array_filter($projection_obj, function($item) {
            return $item->this_cut === true;
        });
        $filteredArray = array_values($filteredArray);

        foreach ($filteredArray as $key => $projection_filtered) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$projection_filtered->subject_id}");
            enroll_student($projection->student_id, [(int)$subject->moodle_course_id]);
        }
    }
}