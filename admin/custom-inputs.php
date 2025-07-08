<?php

function add_admin_custom_input_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'custom_input_details') {
            global $wpdb;
            $custom_input_id = $_GET['custom_input_id'];
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $teachers = get_teachers_active();
            $custom_input = get_custom_inputs_details($custom_input_id);
            $courses = get_courses_moodle();
            include(plugin_dir_path(__FILE__) . 'templates/custom-inputs-detail.php');
        } else if ($_GET['section_tab'] == 'add_custom_input') {
            global $wpdb;
            $table_school_subjects = $wpdb->prefix . 'school_subjects';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $subjects = $wpdb->get_results("SELECT * FROM {$table_school_subjects} WHERE is_active = 1");
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $teachers = get_teachers_active();
            $courses = get_courses_moodle();
            include(plugin_dir_path(__FILE__) . 'templates/custom-inputs-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_custom_input_details') {
            global $wpdb;
            $table_custom_inputs = $wpdb->prefix . 'custom_inputs';

            $custom_input_id = sanitize_text_field($_POST['custom_input_id']);
            $label = sanitize_text_field($_POST['label']);
            $page = sanitize_text_field($_POST['page']);
            $input_mode = sanitize_text_field($_POST['input_mode']);
            $input_name = sanitize_text_field($_POST['input_name']);
            $input_id = sanitize_text_field($_POST['input_id']);
            $input_type = sanitize_text_field($_POST['input_type']);
            $input_required = sanitize_text_field($_POST['input_required']);
            $input_options = sanitize_text_field($_POST['input_options']);

            if (isset($custom_input_id) && !empty($custom_input_id)) {
                $wpdb->update($table_custom_inputs, [
                    'label' => $label,
                    'page' => $page,
                    'input_mode' => $input_mode,
                    'input_name' => $input_name,
                    'input_id' => $input_id,
                    'input_type' => $input_type,
                    'input_required' => $input_required,
                    'input_options' => $input_options,
                ], ['id' => $custom_input_id]);
            } else {
                $wpdb->insert($table_custom_inputs, [
                    'label' => $label,
                    'page' => $page,
                    'input_mode' => $input_mode,
                    'input_name' => $input_name,
                    'input_id' => $input_id,
                    'input_type' => $input_type,
                    'input_required' => $input_required,
                    'input_options' => $input_options,
                ]);
            }

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_custom_input_content'));
            exit;
        } else if ($_GET['action'] == 'custom_input_delete') {
            global $wpdb;
            $table_custom_inputs = $wpdb->prefix . 'custom_inputs';
            $custom_input_id = $_GET['custom_input_id'];
            $wpdb->delete($table_custom_inputs, ['id' => $custom_input_id]);

            setcookie('message', __('Custom input deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_custom_input_content'));
            exit;
        } else {
            global $wpdb;
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $list_custom_inputs = new TT_Custom_Inputs_List_Table;
            $list_custom_inputs->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-custom-inputs.php');
        }
    }
}

class TT_Custom_Inputs_List_Table extends WP_List_Table
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
                $buttons .= "<a style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_custom_input_content&section_tab=custom_input_details&custom_input_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_custom_input_content&action=custom_input_delete&custom_input_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
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
            'label' => __('Label', 'edusystem'),
            'input_mode' => __('Type', 'edusystem'),
            'page' => __('Page', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_custom_inputs()
    {
        global $wpdb;
        $custom_inputs_array = [];
        $period = isset($_GET['academic_period']) ? sanitize_text_field($_GET['academic_period']) : '';
        $cut = isset($_GET['academic_period_cut']) ? sanitize_text_field($_GET['academic_period_cut']) : '';

        // PAGINATION
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_custom_inputs = $wpdb->prefix . 'custom_inputs';

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

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_custom_inputs}";

        if (!empty($where_clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        $sql .= ' ORDER BY id DESC LIMIT %d OFFSET %d';
        $params[] = $per_page;
        $params[] = $offset;

        // Preparar y ejecutar consulta segura
        $prepared_sql = $wpdb->prepare($sql, $params);
        $custom_inputs = $wpdb->get_results($prepared_sql, ARRAY_A);

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($custom_inputs) {
            foreach ($custom_inputs as $input) {
                array_push($custom_inputs_array, [
                    'id' => $input['id'],
                    'label' => $input['label'],
                    'page' => $input['page'],
                    'input_mode' => $input['input_mode']
                ]);
            }
        }

        return ['data' => $custom_inputs_array, 'total_count' => $total_count];
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

        $data_custom_inputs = $this->get_custom_inputs();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_custom_inputs['data'];
        $total_count = (int) $data_custom_inputs['total_count'];

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


function get_custom_inputs_details($custom_input_id)
{
    global $wpdb;
    $table_custom_inputs = $wpdb->prefix . 'custom_inputs';

    $custom_input = $wpdb->get_row("SELECT * FROM {$table_custom_inputs} WHERE id={$custom_input_id}");
    return $custom_input;
}

function get_custom_inputs_page($page)
{
    global $wpdb;
    $table_custom_inputs = $wpdb->prefix . 'custom_inputs';

    $custom_inputs = $wpdb->get_results("SELECT * FROM {$table_custom_inputs} WHERE page='{$page}'");
    return $custom_inputs;
}
