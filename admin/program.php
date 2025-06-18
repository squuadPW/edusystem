<?php

function add_admin_form_program_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'program_details') {
            global $wpdb;
            $program_id = $_GET['program_id'];
            $program = get_program_details($program_id);
            include(plugin_dir_path(__FILE__) . 'templates/program-details.php');
        }

    } else {
        if ($_GET['action'] == 'save_program_details') {
            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';
            
            // Sanitizar valores
            $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $description = strtoupper(sanitize_text_field($_POST['description']));
            $total_price = floatval( sanitize_text_field($_POST['total_price']) );
            $is_active = $_POST['is_active'] == 'on' ? true : false;

            if (!empty($program_id)) {
                $wpdb->update($table_programs, [
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active
                ], ['id' => $program_id]);
            } else {
                $wpdb->insert($table_programs, [
                    'identificator' => $identificator,
                    'name' => $name,
                    'description' => $description,
                    'total_price' => $total_price,
                    'is_active' => $is_active
                ]);
            }
            
            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_program_content'));
            exit;
        } else {
            $list_program = new TT_All_Program_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-program.php');
        }
    }
}

class TT_All_Program_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'program',
                'plural' => 'programs',
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
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_program_content&section_tab=program_details&program_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'identificator' => __('Identificator', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'price' => __('Price', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $programs_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_programs = $wpdb->prefix . 'programs';
        $programs = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_programs} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($programs) {
            foreach ($programs as $pensum) {
                array_push($programs_array, [
                    'id' => $pensum['id'],
                    'identificator' => $pensum['identificator'],
                    'status' => $pensum['is_active'] ? 'Active' : 'Inactive',
                    'name' => $pensum['name'],
                    'price' => $pensum['total_price'],
                    'description' => $pensum['description'],
                    'created_at' => $pensum['created_at'],
                ]);
            }
        }

        return ['data' => $programs_array, 'total_count' => $total_count];
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

        $other_data = $this->get_pensum();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $other_data['data'];
        $total_count = (int) $other_data['total_count'];

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

function get_program_details($id)
{
    global $wpdb;
    $table_programs = $wpdb->prefix . 'programs';

    $program = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE id={$id}");
    return $program;
}