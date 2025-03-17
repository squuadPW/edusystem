<?php

function add_admin_form_equivalence_matrix_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'equivalence_matrix_details') {
            global $wpdb;
            $equivalence_id = $_GET['equivalence_id'];
            $equivalence = get_equivalence_details($equivalence_id);
            include(plugin_dir_path(__FILE__) . 'templates/equivalence-matrix-detail.php');
        }
    } else {
        if ($_GET['action'] == 'save_equivalence_details') {
            global $wpdb;
            $table_equivalence_matrix = $wpdb->prefix . 'equivalence_matrix';
            $equivalence_id = sanitize_text_field($_POST['equivalence_id']);
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $institute_id = sanitize_text_field($_POST['institute_id']) ?? NULL;
            $matrix = [];

            if (isset($equivalence_id) && !empty($equivalence_id)) {
                $wpdb->update($table_equivalence_matrix, [
                    'name' => $name,
                    'matrix' => json_encode($matrix),
                    'institute_id' => $institute_id,
                ], ['id' => $equivalence_id]);
            } else {
                $wpdb->insert($table_equivalence_matrix, [
                    'name' => $name,
                    'matrix' => json_encode($matrix),
                    'institute_id' => $institute_id
                ]);
            }

            setcookie('message', __('Changes saved successfully.', 'aes'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_equivalence_matrix_content'));
            exit;
        }else {
            $list_equivalence = new TT_All_Equivalence_Matrix_List_Table;
            $list_equivalence->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-equivalence-matrix.php');
        }
    }
}

class TT_All_Equivalence_Matrix_List_Table extends WP_List_Table
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
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_equivalence_matrix_content&section_tab=equivalence_matrix_details&equivalence_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
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
            'name' => __('Name', 'aes'),
            'institute' => __('Institute', 'aes'),
            'created_at' => __('Created at', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_equivalence_matrix()
    {
        global $wpdb;
        $equivalence_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_equivalence_matrix = $wpdb->prefix . 'equivalence_matrix';
        $equivalences = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_equivalence_matrix} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($equivalences) {
            foreach ($equivalences as $equivalence) {
                $institute = get_institute_details($equivalence['institute_id']);
                array_push($equivalence_array, [
                    'id' => $equivalence['id'],
                    'name' => $equivalence['name'],
                    'institute' => $institute->name,
                    'created_at' => $equivalence['created_at'],
                ]);
            }
        }

        return ['data' => $equivalence_array, 'total_count' => $total_count];
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

        $data_requests = $this->get_equivalence_matrix();

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

function get_equivalence_details($id)
{
    global $wpdb;
    $table_equivalence_matrix = $wpdb->prefix . 'equivalence_matrix';

    $equivalence = $wpdb->get_row("SELECT * FROM {$table_equivalence_matrix} WHERE id={$id}");
    return $equivalence;
}