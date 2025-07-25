<?php

function add_admin_grades_country_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'grades_country_details') {
            global $wpdb;
            $grade_country_id = $_GET['grade_country_id'];
            $grades_country = get_grades_country_details($grade_country_id);
            $countries = get_countries();

            include(plugin_dir_path(__FILE__) . 'templates/grades-country-detail.php');
        } else if ($_GET['section_tab'] == 'add_grade_country') {
            $countries = get_countries();

            include(plugin_dir_path(__FILE__) . 'templates/grades-country-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_grades_country_details') {
            global $wpdb;
            $table_grades_by_country = $wpdb->prefix . 'grades_by_country';

            $grade_country_id = sanitize_text_field($_POST['grade_country_id']);
            $country = sanitize_text_field($_POST['country']);
            $grades = sanitize_text_field($_POST['grades']);

            if (isset($grade_country_id) && !empty($grade_country_id)) {
                // Si grade_country_id existe, estamos actualizando un registro existente.
                // En este caso, no necesitamos la validación de existencia del país,
                // ya que estamos modificando uno que ya está.
                $wpdb->update($table_grades_by_country, [
                    'country' => $country,
                    'grades' => $grades
                ], ['id' => $grade_country_id]);

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');

            } else {
                // Si grade_country_id NO existe, estamos insertando un nuevo registro.
                // Aquí es donde aplicamos la validación.

                // 1. Validar si ya existe un país con esas calificaciones
                $existing_country = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $table_grades_by_country WHERE country = %s",
                    $country
                ));

                if ($existing_country) {
                    // Si $existing_country no es nulo, significa que ya existe una entrada para ese país.
                    setcookie('message-error', __('There are already degrees registered for this country.', 'edusystem'), time() + 10, '/');
                } else {
                    // Si no existe, procedemos con la inserción.
                    $wpdb->insert($table_grades_by_country, [
                        'country' => $country,
                        'grades' => $grades
                    ]);
                    setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                }
            }

            wp_redirect(admin_url('admin.php?page=add_admin_grades_country_content'));
            exit;
        } else if ($_GET['action'] == 'custom_input_delete') {
            global $wpdb;
            $table_grades_by_country = $wpdb->prefix . 'grades_by_country';
            $grade_country_id = $_GET['grade_country_id'];
            $wpdb->delete($table_grades_by_country, ['id' => $grade_country_id]);

            setcookie('message', __('Custom input deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_grades_country_content'));
            exit;
        } else {
            $list_grades_country = new TT_Grades_Country_List_Table;
            $list_grades_country->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-grades-country.php');
        }
    }
}

class TT_Grades_Country_List_Table extends WP_List_Table
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
                $buttons .= "<a style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_grades_country_content&section_tab=grades_country_details&grade_country_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin: 1px' href='" . admin_url('/admin.php?page=add_admin_grades_country_content&action=custom_input_delete&grade_country_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
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
            'country' => __('Country', 'edusystem'),
            'grades' => __('Grades', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_grades_country()
    {
        global $wpdb;
        $grades_country_array = [];
        // PAGINATION
        $per_page = 20;
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_grades_by_country = $wpdb->prefix . 'grades_by_country';

        // Construir WHERE dinámicamente
        $where_clauses = array();
        $params = array();

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_grades_by_country}";

        if (!empty($where_clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        $sql .= ' ORDER BY id DESC LIMIT %d OFFSET %d';
        $params[] = $per_page;
        $params[] = $offset;

        // Preparar y ejecutar consulta segura
        $prepared_sql = $wpdb->prepare($sql, $params);
        $grades_country = $wpdb->get_results($prepared_sql, ARRAY_A);

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($grades_country) {
            foreach ($grades_country as $input) {
                array_push($grades_country_array, [
                    'id' => $input['id'],
                    'country' => $input['country'],
                    'grades' => $input['grades']
                ]);
            }
        }

        return ['data' => $grades_country_array, 'total_count' => $total_count];
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

        $info = $this->get_grades_country();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $info['data'];
        $total_count = (int) $info['total_count'];

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


function get_grades_country_details($id)
{
    global $wpdb;
    $table_grades_by_country = $wpdb->prefix . 'grades_by_country';

    $grades = $wpdb->get_row("SELECT * FROM {$table_grades_by_country} WHERE id={$id}");
    return $grades;
}

function get_grades_by_country_code($country)
{
    global $wpdb;
    $table_grades_by_country = $wpdb->prefix . 'grades_by_country';

    // It's good practice to sanitize the input country code
    $country = esc_sql($country); 

    $result = $wpdb->get_row("SELECT grades FROM {$table_grades_by_country} WHERE country='{$country}'");

    $grades_array = [];

    // Check if a result was found and if the 'grades' property exists
    if ($result && isset($result->grades)) {
        // Explode the comma-separated string into an array
        $grades_string = $result->grades;
        
        // Trim whitespace from each grade and remove empty entries
        $grades_array = array_map('trim', explode(',', $grades_string));
        $grades_array = array_filter($grades_array, function($value) {
            return $value !== ''; // Remove any empty strings resulting from multiple commas or leading/trailing commas
        });
        
        // Re-index the array if necessary after filtering
        $grades_array = array_values($grades_array);
    }

    return $grades_array;
}
