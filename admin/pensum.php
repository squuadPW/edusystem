<?php

function add_admin_form_pensum_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'pensum_details') {
            global $wpdb;
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_institutes = $wpdb->prefix . 'institutes';
            $pensum_id = $_GET['pensum_id'];
            $institute = $_GET['institute'];
            $pensum = get_pensum_details($pensum_id);
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1 AND `type` <> 'elective'");
            $institutes = $wpdb->get_results("SELECT * FROM {$table_institutes} WHERE `status` = 1");
            include(plugin_dir_path(__FILE__) . 'templates/pensum-details.php');
        }

        if ($_GET['section_tab'] == 'pensum_institute') {
            $institute = 1;
            $list_pensum = new TT_All_Pensum_Institute_List_Table;
            $list_pensum->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-pensum.php');
        }

    } else {
        if ($_GET['action'] == 'save_pensum_details') {
            global $wpdb;
            $table_pensum = $wpdb->prefix . 'pensum';
            
            // Sanitizar valores
            $pensum_id = isset($_POST['pensum_id']) ? sanitize_text_field($_POST['pensum_id']) : '';
            $program_institute = isset($_POST['program_institute']) ? sanitize_text_field($_POST['program_institute']) : '';
            $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
            $status = ($status == 'on' ? 1 : 0);
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : null;
            $institute_id = isset($_POST['institute_id']) ? sanitize_text_field($_POST['institute_id']) : null;
            $subjects = isset($_POST['subjects']) ? array_map('sanitize_text_field', $_POST['subjects']) : array(); // Array sanitizado
            $type = $program_institute ? 'institute' : 'program';
            $matrix = [];

            foreach ($subjects as $key => $subject) {
                $sub = get_subject_details($subject);
                if ($sub) {
                    $sub_payload = [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'code_subject' => $sub->code_subject,
                        'type' => ucwords($sub->type),
                    ];
                    array_push($matrix, $sub_payload);
                }
            }

            if ($status == 1) {
                $wpdb->update(
                    $table_pensum, // Nombre de la tabla
                    array('status' => 0), // Datos a actualizar
                    array('status' => 1, 'type' => $type) // Condición (opcional, si quieres actualizar solo donde status es 1)
                );
            }

            if (!empty($pensum_id)) {
                $wpdb->update($table_pensum, [
                    'name' => $name,
                    'matrix' => json_encode($matrix), // Codifica el array a JSON
                    'type' => $type,
                    'institute_id' => $institute_id,
                    'program_id' => $program_id,
                    'status' => $status
                ], ['id' => $pensum_id]);
            } else {
                $wpdb->insert($table_pensum, [
                    'name' => $name,
                    'matrix' => json_encode($matrix), // Codifica el array a JSON
                    'type' => $type,
                    'institute_id' => $institute_id,
                    'program_id' => $program_id,
                    'status' => $status
                ]);
            }
            
            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            if ($program_institute) {
                wp_redirect(admin_url('admin.php?page=add_admin_form_pensum_content&section_tab=pensum_institute'));
            } else {
                wp_redirect(admin_url('admin.php?page=add_admin_form_pensum_content'));
            }
            
            exit;
        } else {
            $institute = 0;
            $list_pensum = new TT_All_Pensum_List_Table;
            $list_pensum->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-pensum.php');
        }
    }
}

class TT_All_Pensum_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'institute':
                return $item[$column_name] ?? 'N/A';
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_pensum_content&section_tab=pensum_details&pensum_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'current' => __('Current', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'program' => __('Program', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $pensum_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_pensum = $wpdb->prefix . 'pensum';
        $pensums = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_pensum} WHERE `type` = 'program' ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($pensums) {
            foreach ($pensums as $pensum) {
                $program = get_name_program($pensum['program_id']);
                array_push($pensum_array, [
                    'current' => $pensum['status'] == 1 ? 'Yes' : 'No',
                    'id' => $pensum['id'],
                    'name' => $pensum['name'],
                    'program' => $program,
                    'created_at' => $pensum['created_at'],
                ]);
            }
        }

        return ['data' => $pensum_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_pensum();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_requests['data'];
        $total_count = (int) $data_requests['total_count'];

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

class TT_All_Pensum_Institute_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'request',
                'plural' => 'requests',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'institute':
                return $item[$column_name] ?? 'N/A';
            case 'view_details':
                $buttons = '';
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_pensum_content&section_tab=pensum_details&institute=1&pensum_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'current' => __('Current', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'institute' => __('Institute', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $pensum_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_pensum = $wpdb->prefix . 'pensum';
        $pensums = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_pensum} WHERE `type` = 'institute' ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($pensums) {
            foreach ($pensums as $pensum) {
                $institute = get_institute_details($pensum['institute_id']);
                array_push($pensum_array, [
                    'current' => $pensum['status'] == 1 ? 'Yes' : 'No',
                    'id' => $pensum['id'],
                    'name' => $pensum['name'],
                    'institute' => $institute->name,
                    'created_at' => $pensum['created_at'],
                ]);
            }
        }

        return ['data' => $pensum_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_pensum();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_requests['data'];
        $total_count = (int) $data_requests['total_count'];

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

function get_pensum_details($id)
{
    global $wpdb;
    $table_pensum = $wpdb->prefix . 'pensum';

    $pensum = $wpdb->get_row("SELECT * FROM {$table_pensum} WHERE id={$id}");
    return $pensum;
}

function get_current_pensum()
{
    global $wpdb;
    $table_pensum = $wpdb->prefix . 'pensum';
    $pensum = $wpdb->get_row("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1");
    $matrix = json_decode($pensum->matrix);

    $subjects = [];
    foreach ($matrix as $key => $m) {
        $subject = get_subject_details($m->id);
        array_push($subjects, $subject);
    }

    return $subjects;
}

function only_pensum_regular($program_id)
{
    global $wpdb;
    $table_pensum = $wpdb->prefix . 'pensum';
    
    // Obtener el pensum
    $pensum = $wpdb->get_row("SELECT * FROM {$table_pensum} WHERE `type`='program' AND `status` = 1 AND program_id = '{$program_id}'");
    
    // Decodificar la matriz como un array de objetos
    $matrix = json_decode($pensum->matrix);
    
    // Array para almacenar las materias regulares
    $subjects = [];
    
    // Recorrer la matriz
    foreach ($matrix as $key => $m) {
        // Verificar si el tipo es 'REGULAR'
        if (strtoupper($m->type) == 'REGULAR') {
            // Obtener los detalles de la materia
            $subject = get_subject_details($m->id);
            
            // Crear un objeto stdClass para el payload
            $payload = new stdClass();
            $payload->subject = $subject->name;
            $payload->subject_id = $subject->id;
            $payload->type = $subject->type;
            
            // Agregar el payload al array de subjects
            array_push($subjects, $payload);
        }
    }

    return $subjects;
}

function pensum_institute($institute_id)
{
    global $wpdb;
    $table_pensum = $wpdb->prefix . 'pensum';
    $pensum = $wpdb->get_row("SELECT * FROM {$table_pensum} WHERE `type`='institute' AND `status` = 1 AND institute_id = '{$institute_id}'");
    $matrix = json_decode($pensum->matrix);
    return $matrix;
}

function update_equivalence_califications($student_id) {
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $student = get_student_detail($student_id);
    $pensum_institute = pensum_institute($student->institute_id);
    $projection = get_projection_by_student($student_id);
    $load = load_current_cut_enrollment();

    $projection_obj = json_decode($projection->projection);
    $current_period = $load['code'];

    if (!$pensum_institute) {
        foreach ($projection_obj as $key => $value) {
            if ($projection_obj[$key]->type == 'equivalence') {
                $projection_obj[$key]->calification = 'TR';
                $projection_obj[$key]->code_period = $current_period;
            }
        }
    } else {
        foreach ($projection_obj as $key => $value) {
            if ($projection_obj[$key]->type == 'equivalence') {
                $projection_obj[$key]->calification = 'TR';
                $projection_obj[$key]->code_period = $current_period;
            }
        }
    }
    
    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj),
    ], ['id' => $projection->id]);
}