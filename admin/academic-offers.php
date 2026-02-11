<?php

function add_admin_form_academic_offers_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'offer_details') {
            global $wpdb;
            $offer_id = $_GET['offer_id'];
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $teachers = get_teachers_active();
            $offer = get_academic_offer_details($offer_id);
            $courses = get_courses_moodle();
            include(plugin_dir_path(__FILE__) . 'templates/academic-offer-detail.php');
        } else if ($_GET['section_tab'] == 'add_offer') {
            global $wpdb;
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $teachers = get_teachers_active();
            $courses = get_courses_moodle();
            include(plugin_dir_path(__FILE__) . 'templates/academic-offer-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_offer_details') {
            global $wpdb;
            $table_academic_offers = $wpdb->prefix . 'academic_offers';

            $offer_id = sanitize_text_field($_POST['offer_id']);
            $subject_id = sanitize_text_field($_POST['subject_id']);
            $old_subject_id = sanitize_text_field($_POST['old_subject_id']);
            $code_period = sanitize_text_field($_POST['code_period']);
            $cut_period = sanitize_text_field($_POST['cut_period']);
            $teacher_id = sanitize_text_field($_POST['teacher_id']);
            $max_students = sanitize_text_field($_POST['max_students']);
            $moodle_course_id = sanitize_text_field($_POST['moodle_course_id']);
            $old_moodle_course_id = sanitize_text_field($_POST['old_moodle_course_id']);
            $new_section = ($moodle_course_id != $old_moodle_course_id);
            $section = load_next_section($subject_id, $code_period, $cut_period, $offer_id, $new_section);
            $subject = get_subject_details($subject_id);
            $type = $subject->type;

            if (isset($offer_id) && !empty($offer_id)) {
                $wpdb->update($table_academic_offers, [
                    'section' => $section,
                    'subject_id' => $subject_id,
                    'type' => $type,
                    'code_period' => $code_period,
                    'cut_period' => $cut_period,
                    'teacher_id' => $teacher_id,
                    'max_students' => $max_students,
                    'moodle_course_id' => $moodle_course_id,
                ], ['id' => $offer_id]);
            } else {
                $wpdb->insert($table_academic_offers, [
                    'section' => $section,
                    'subject_id' => $subject_id,
                    'type' => $type,
                    'code_period' => $code_period,
                    'cut_period' => $cut_period,
                    'teacher_id' => $teacher_id,
                    'max_students' => $max_students,
                    'moodle_course_id' => $moodle_course_id,
                ]);
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_offers_content'));
            exit;
        } else if ($_GET['action'] == 'offer_delete') {
            global $wpdb;
            $table_academic_offers = $wpdb->prefix . 'academic_offers';
            $offer_id = $_GET['offer_id'];
            $wpdb->delete($table_academic_offers, ['id' => $offer_id]);

            setcookie('message', __('Offer deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_academic_offers_content'));
            exit;
        } else {
            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $list_academic_offers = new TT_Academic_Offers_List_Table;
            $list_academic_offers->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-academic-offer.php');
        }
    }
}

class TT_Academic_Offers_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'school_subject_',
                'plural' => 'school_subject_s',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                $buttons = '';
                $buttons .= "<a style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_form_academic_offers_content&section_tab=offer_details&offer_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a target='_blank' style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollment_subject&academic_period=' . $item['academic_period']) . "&academic_period_cut=" . $item['academic_period_cut'] . "&subject_id=" . $item['subject_id'] . "&section=" . $item['section'] . "' class='button button-success'>" . __('View Notes', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_form_academic_offers_content&action=offer_delete&offer_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
                return $buttons;
            default:
                return strtoupper($item[$column_name]);
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
            'subject' => __('Subject', 'edusystem'),
            'section' => __('Section', 'edusystem'),
            'period' => __('Offer period', 'edusystem'),
            'teacher' => __('Teacher or person responsible', 'edusystem'),
            'max' => __('Max. students', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_academic_offers()
    {
        global $wpdb;
        $academic_offers_array = [];
        $period = isset($_GET['academic_period']) ? sanitize_text_field($_GET['academic_period']) : '';
        $cut = isset($_GET['academic_period_cut']) ? sanitize_text_field($_GET['academic_period_cut']) : '';
        $type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

        // PAGINATION
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_academic_offers = $wpdb->prefix . 'academic_offers';

        // Construir WHERE dinámicamente
        $where_clauses = array();
        $params = array();

        if (!empty($period)) {
            $where_clauses[] = 'code_period = %s';
            $params[] = $period;
        }

        if (!empty($cut)) {
            $where_clauses[] = 'cut_period = %s';
            $params[] = $cut;
        }

        if (!empty($type)) {
            $where_clauses[] = 'type = %s';
            $params[] = $type;
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_academic_offers}";

        if (!empty($where_clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        $sql .= ' ORDER BY id DESC LIMIT %d OFFSET %d';
        $params[] = $per_page;
        $params[] = $offset;

        // Preparar y ejecutar consulta segura
        $prepared_sql = $wpdb->prepare($sql, $params);
        $academic_offers = $wpdb->get_results($prepared_sql, ARRAY_A);

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($academic_offers) {
            foreach ($academic_offers as $offer) {
                $subject = get_subject_details($offer['subject_id']);
                $teacher = get_teacher_details($offer['teacher_id']);
                array_push($academic_offers_array, [
                    'id' => $offer['id'],
                    'subject_id' => $offer['subject_id'],
                    'academic_period' => $offer['code_period'],
                    'academic_period_cut' => $offer['cut_period'],
                    'subject' => $subject->name . ' (' . $subject->code_subject . ')',
                    'section' => $offer['section'],
                    'period' => $offer['code_period'] . ' - ' . $offer['cut_period'],
                    'max' => $offer['max_students'],
                    'teacher' => $teacher->last_name . ' ' . $teacher->middle_last_name . ' ' . $teacher->name . ' ' . $teacher->middle_name,
                ]);
            }
        }

        return ['data' => $academic_offers_array, 'total_count' => $total_count];
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

        $data_academic_offers = $this->get_academic_offers();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_academic_offers['data'];
        $total_count = (int) $data_academic_offers['total_count'];

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


function get_academic_offer_details($offer_id)
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';

    $offer = $wpdb->get_row("SELECT * FROM {$table_academic_offers} WHERE id={$offer_id}");
    return $offer;
}

function get_offer_filtered($subject_id, $code, $cut, $section = 1)
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';

    $offer = $wpdb->get_row("SELECT * FROM {$table_academic_offers} WHERE subject_id={$subject_id} AND code_period='{$code}' AND cut_period='{$cut}' AND section={$section}");
    return $offer;
}


function get_offer_filtered_all($subject_id, $code, $cut, $section = null)
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';

    $query = $wpdb->prepare(
        "SELECT * FROM {$table_academic_offers} WHERE subject_id = %d AND code_period = %s AND cut_period = %s",
        $subject_id,
        $code,
        $cut
    );

    if ($section !== null) {
        $query .= $wpdb->prepare(" AND section = %d", $section);
    }

    $query .= " ORDER BY section ASC";

    $offer = $wpdb->get_results($query);
    return $offer;
}

function get_offer_by_moodle($moodle_course_id)
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';

    $offer = $wpdb->get_row("SELECT * FROM {$table_academic_offers} WHERE moodle_course_id={$moodle_course_id} ORDER BY id DESC");
    return $offer;
}

function get_teacher_offers($teacher_id, $code_period = '', $cut_period = '', $type = 'current')
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';

    if (!is_numeric($teacher_id)) {
        return new WP_Error('invalid_teacher_id', __('Invalid teacher ID provided.', 'edusystem'));
    }

    $query = "SELECT * FROM {$table_academic_offers} WHERE teacher_id = %d";
    $prepare_args = [$teacher_id];

    // Lógica condicional basada en el tipo (current o history)
    if (!empty($code_period) && !empty($cut_period)) { // Asegurarse de tener ambos para la lógica de exclusión combinada
        if ($type === 'current') {
            $query .= " AND code_period = %s AND cut_period = %s";
            $prepare_args[] = $code_period;
            $prepare_args[] = $cut_period;
        } else { // type === 'history'
            // Para historial: traer todo lo que NO sea la combinación exacta de code_period y cut_period
            $query .= " AND NOT (code_period = %s AND cut_period = %s)";
            $prepare_args[] = $code_period;
            $prepare_args[] = $cut_period;
        }
    } elseif (!empty($code_period)) { // Si solo se proporciona code_period
        // En este caso, la lógica de 'history' no tiene sentido sin cut_period para la exclusión combinada
        // Por simplicidad, si solo hay code_period, 'history' excluirá ese code_period
        if ($type === 'current') {
            $query .= " AND code_period = %s";
        } else {
            $query .= " AND code_period <> %s";
        }
        $prepare_args[] = $code_period;
    } elseif (!empty($cut_period)) { // Si solo se proporciona cut_period
        // Similar al caso anterior, si solo hay cut_period, 'history' excluirá ese cut_period
        if ($type === 'current') {
            $query .= " AND cut_period = %s";
        } else {
            $query .= " AND cut_period <> %s";
        }
        $prepare_args[] = $cut_period;
    }

    $query .= " ORDER BY id DESC";

    $offers = $wpdb->get_results($wpdb->prepare(
        $query,
        ...$prepare_args
    ));

    return $offers;
}

function load_section_available($subject_id, $code, $cut)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $section = 1;
    $all_offers = get_offer_filtered_all($subject_id, $code, $cut);
    if (count($all_offers) > 1) {
        foreach ($all_offers as $key => $offer) {
            $active_inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE code_period='{$code}' AND cut_period='{$cut}' AND subject_id = {$subject_id} AND status_id = 1");
            if (count($active_inscriptions) >= (int) $offer->max_students) {
                continue;
            } else {
                $section = $offer->section;
                break;
            }
        }
    }

    return $section;
}

function offer_available_to_enroll($subject_id, $code, $cut)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $available = true;
    $all_offers = get_offer_filtered_all($subject_id, $code, $cut);
    if (!$all_offers) {
        $available = false;
    }

    foreach ($all_offers as $key => $offer) {
        $active_inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE code_period='{$code}' AND cut_period='{$cut}' AND section = {$offer->section} AND subject_id = {$subject_id} AND status_id = 1");
        if (count($active_inscriptions) < (int) $offer->max_students) {
            $available = true;
            break;
        } else {
            $available = false;
        }
    }

    return $available;
}

function available_inscription_subject($student_id, $subject_id)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $count_status_1_or_3 = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_student_period_inscriptions} 
            WHERE student_id = %d 
            AND subject_id = %d 
            AND (status_id = 1 OR status_id = 3)",
            $student_id,
            $subject_id
        )
    );

    if ($count_status_1_or_3 > 0) {
        return 'active_or_approved';
    }

    $count_status_4 = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_student_period_inscriptions} 
            WHERE student_id = %d 
            AND subject_id = %d 
            AND status_id = 4",
            $student_id,
            $subject_id
        )
    );

    if ($count_status_4 >= 2) {
        return 'max_retries_reached';
    }

    return true;
}

function load_next_section($subject_id, $code, $cut, $offer_id, $new_section) {
    global $wpdb;

    if ($offer_id && !$new_section) {
        // Si es una oferta existente y no se requiere una nueva sección
        $offer = get_academic_offer_details($offer_id);
        return $offer->section;
    }

    // Si es una oferta nueva o el moodle_course_id ha cambiado,
    // calcula la siguiente sección disponible.
    $all_offers = get_offer_filtered_all($subject_id, $code, $cut);
    return count($all_offers) + 1;
}

function get_offers_availables_by_code($code, $cut)
{
    global $wpdb;
    $table_academic_offers = $wpdb->prefix . 'academic_offers';
    $subjects = [];
    $offers = $wpdb->get_results("SELECT * FROM {$table_academic_offers} WHERE code_period='{$code}' AND cut_period='{$cut}' AND grades_downloaded = 0 ORDER BY section ASC");
    foreach ($offers as $key => $offer) {
        $subjects[] = get_subject_details($offer->subject_id);
    }

    return $subjects;
}