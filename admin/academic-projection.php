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
        } else if($_GET['action'] == 'save_academic_projection') {
            print_r($_POST['academic_period_cut[0]']);
            exit;
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
    // $subjects_number = 0;
    // switch ($grade_id) {
    //     case 1: // lower
    //         $subjects_number = 15;
    //         break;
    //     case 2: // upper
    //         $subjects_number = 10;
    //         break;
    //     case 3: // middle
    //     case 4: // graduated
    //         $subjects_number = 5;
    //         break;
    // }

    // $initial_cut = -1;
    // for ($i=0; $i < $subjects_number; $i++) { 
    //     $initial_cut++;
    //     $cut = ['A','B','C','D','E'];
    //     array_push($projection, ['subject_position' => $i, 'subject_code' => '', 'subject_name' => '', 'cut' => $cut[$initial_cut]]);
    //     if ($initial_cut == 4) {
    //         $initial_cut = -1;
    //     }
    // }

    foreach ($subjects as $key => $subject) {
        array_push($projection, ['code_subject' => $subject->code_subject, 'subject' => $subject->name, 'hc' => $subject->hc, 'cut' => "", 'code_period' => "", 'calification' => "", 'completed' => false]);
    }

    $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection) // Ajusta el valor de 'projection' según sea necesario
    ]);
}