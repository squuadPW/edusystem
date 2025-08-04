<?php

function add_admin_form_student_program_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'program_details') {

            if ($_GET['from'] == 'programs') {
                global $wpdb;
                $program_id = $_GET['program_id'];
                $program = get_program_details($program_id);
                include(plugin_dir_path(__FILE__) . 'templates/student-program-details.php');
            } else if ($_GET['from'] == 'careers') {
                global $wpdb;
                $career_id = $_GET['career_id'];
                $career = get_career_details($career_id);
                $programs = get_student_programs();
                include(plugin_dir_path(__FILE__) . 'templates/student-career-details.php');
            } else if ($_GET['from'] == 'mentions') {
                global $wpdb;
                $mention_id = $_GET['mention_id'];
                $mention = get_mention_details($mention_id);
                $careers = get_careers();
                include(plugin_dir_path(__FILE__) . 'templates/student-mention-details.php');
            }

        } else if ($_GET['section_tab'] == 'quotas_rules_programs') {

            global $wpdb;
            $program_id = $_GET['program_id'];
            $identificator = $_GET['identificator'];
            $rules = get_quotas_rules_programs($identificator);
            include(plugin_dir_path(__FILE__) . 'templates/quotas-rules-programs.php');

        } else if ($_GET['section_tab'] == 'careers') {

            $list_program = new TT_All_Careers_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-student-program.php');

        } else if ($_GET['section_tab'] == 'mentions') {

            $list_program = new TT_All_Mentions_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-student-program.php');

        }

    } else {

        if ($_GET['action'] == 'save_program_details') {
            global $wpdb;
            $table_student_program = $wpdb->prefix . 'student_program';

            // Sanitizar valores
            $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $program_identificator = strtoupper(sanitize_text_field($_POST['program_identificator']));
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $description = strtoupper(sanitize_text_field($_POST['description']));
            $is_active = isset($_POST['is_active']) ? true : false;

            // Comprobar si el identificador ya existe
            $query_check = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_student_program WHERE identificator = %s AND id != %d",
                $identificator,
                $program_id
            );
            $identificator_exists = $wpdb->get_var($query_check);

            if ($identificator_exists > 0) {
                // Si el identificador ya existe, establece un mensaje de error y redirige.
                setcookie('message', __('Error: The identifier already exists.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'));
                exit;
            }

            // Prepara los datos a insertar o actualizar
            $data = [
                'is_active' => $is_active,
                'program_identificator' => $program_identificator,
                'identificator' => $identificator,
                'name' => $name,
                'description' => $description,
            ];

            // crea o actualiza el sub programa
            if (!empty($program_id)) {
                // Actualizar el registro
                $wpdb->update($table_student_program, $data, ['id' => $program_id]);
                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            } else {
                // Insertar un nuevo registro
                $wpdb->insert($table_student_program, $data);
                setcookie('message', __('New record added successfully.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'));
            exit;
        } else if ($_GET['action'] == 'save_quotas_rules') {

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';

            // Sanitizar 
            $program_id = $_POST['program_id'] ?? '';
            $identificator = isset($_POST['identificator']) ? sanitize_text_field($_POST['identificator']) : '';

            if (!empty($identificator)) {
                $rules_post = $_POST['rules'] ?? '';

                foreach ($rules_post as $rule) {

                    $rule_id = $rule['id'] ?? '';
                    $is_active = $rule['is_active'] ? true : false;
                    $name = $rule['name'];
                    $initial_price = $rule['initial_price'];
                    $quantity = $rule['quantity'];
                    $price = $rule['price'];
                    $frequency_value = $rule['frequency_value'];
                    $type_frequency = $rule['type_frequency'];
                    $position = $rule['position'] ?? 0;

                    // crea o actualiza el sub programa
                    if (!empty($rule_id)) {

                        $wpdb->update($table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'position' => $position,
                        ], ['id' => $rule_id]);

                    } else {

                        $wpdb->insert($table_quota_rules, [
                            'is_active' => $is_active,
                            'name' => $name,
                            'initial_price' => $initial_price,
                            'quotas_quantity' => $quantity,
                            'quote_price' => $price,
                            'program_id' => $identificator,
                            'frequency_value' => $frequency_value,
                            'type_frequency' => $type_frequency,
                            'position' => $position,
                        ]);
                    }
                }

                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect($_SERVER['HTTP_REFERER']);

            } else {
                setcookie('message-error', __('Identifier not found', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url("admin.php?page=add_admin_form_student_program_content&section_tab=program_details"));
            }

            exit;

        } else if ($_GET['action'] == 'delete_quota_rule') {

            global $wpdb;
            $table_quota_rules = $wpdb->prefix . 'quota_rules';

            $rule_id = $_POST['quota_rule_id'];

            $deleted = $wpdb->delete(
                $table_quota_rules,
                ['id' => $rule_id],
                ['%d']
            );

            if ($deleted) {
                setcookie('message', __('The quota rule has been deleted successfully.', 'edusystem'), time() + 10, '/');
            } else {
                setcookie('message-error', __('The quota rule has not been deleted correctly.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'delete_subprogram') {

            $subprogram_id = $_POST['subprogram_id'];

            global $wpdb;
            $table_students = $wpdb->prefix . 'students';
            $students = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s",
                $subprogram_id
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ($students == 0) {

                $separacion = strpos($subprogram_id, '_');
                if ($separacion !== false) {
                    $program_id = substr($subprogram_id, 0, $separacion);
                    $subprogram_indice = substr($subprogram_id, $separacion + 1);
                }

                $subprogram_data = get_subprogram_by_identificador_program($program_id);

                // obtiene el id del producto a eliminar
                $product_id = $subprogram_data[$subprogram_indice]['product_id'];

                // elimina el producto
                wp_delete_post($product_id, true);

                // elemina el subprograma
                unset($subprogram_data[$subprogram_indice]);

                //guardar la subprogramas
                $table_programs = $wpdb->prefix . 'programs';
                $update = $wpdb->update($table_programs, [
                    'subprogram' => json_encode($subprogram_data) ?? null,
                ], ['identificator' => $program_id]);

                if ($update) {
                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'delete_program') {

            $program_id = $_POST['program_id'];

            global $wpdb;
            $table_programs = $wpdb->prefix . 'programs';
            $table_quotas_rules = $wpdb->prefix . 'quota_rules';
            $table_students = $wpdb->prefix . 'students';

            $program_data = $wpdb->get_row($wpdb->prepare(
                "SELECT identificator, product_id FROM $table_programs WHERE id = %d ",
                $program_id,
            ));

            $students = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_students WHERE program_id LIKE %s || program_id LIKE %s",
                $program_data->identificator,
                $program_data->identificator . '_%'
            ));

            // Si no hay registros en table_y, proceder a eliminar
            if ($students == 0) {

                $product = wc_get_product($program_data->product_id);
                if ($product)
                    $product->delete(true);

                $deleted = $wpdb->delete(
                    $table_programs,
                    ['id' => $program_id],
                    ['%d']
                );



                if ($deleted) {

                    // eliminar las reglas de los quotas
                    $wpdb->query($wpdb->prepare(
                        "DELETE FROM $table_quotas_rules WHERE program_id = %s OR program_id LIKE %s",
                        $program_data->identificator,
                        $program_data->identificator . '_%'
                    ));

                    setcookie('message', __('The subprogram has been successfully removed.', 'edusystem'), time() + 10, '/');
                } else {
                    setcookie('message-error', __('The subprogram was not removed correctly.', 'edusystem'), time() + 10, '/');
                }

            } else {
                setcookie('message-error', __('The subprogram contains enrolled students.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;

        } else if ($_GET['action'] == 'save_career_details') {
            global $wpdb;
            $table_careers_by_program = $wpdb->prefix . 'careers_by_program';

            // Sanitizar valores
            $career_id = isset($_POST['career_id']) ? sanitize_text_field($_POST['career_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $program_identificator = strtoupper(sanitize_text_field($_POST['program_identificator']));
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $description = strtoupper(sanitize_text_field($_POST['description']));
            $is_active = isset($_POST['is_active']) ? true : false;

            // Comprobar si el identificador ya existe
            $query_check = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_careers_by_program WHERE identificator = %s AND id != %d",
                $identificator,
                $career_id
            );
            $identificator_exists = $wpdb->get_var($query_check);

            if ($identificator_exists > 0) {
                // Si el identificador ya existe, establece un mensaje de error y redirige.
                setcookie('message', __('Error: The identifier already exists.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'));
                exit;
            }

            // Prepara los datos a insertar o actualizar
            $data = [
                'is_active' => $is_active,
                'program_identificator' => $program_identificator,
                'identificator' => $identificator,
                'name' => $name,
                'description' => $description,
            ];

            // crea o actualiza el sub programa
            if (!empty($career_id)) {
                // Actualizar el registro
                $wpdb->update($table_careers_by_program, $data, ['id' => $career_id]);
                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            } else {
                // Insertar un nuevo registro
                $wpdb->insert($table_careers_by_program, $data);
                setcookie('message', __('New record added successfully.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'));
            exit;
        } else if ($_GET['action'] == 'save_mention_details') {
            global $wpdb;
            $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';

            // Sanitizar valores
            $mention_id = isset($_POST['mention_id']) ? sanitize_text_field($_POST['mention_id']) : '';
            $identificator = strtoupper(sanitize_text_field($_POST['identificator']));
            $career_identificator = strtoupper(sanitize_text_field($_POST['career_identificator']));
            $name = strtoupper(sanitize_text_field($_POST['name']));
            $description = strtoupper(sanitize_text_field($_POST['description']));
            $is_active = isset($_POST['is_active']) ? true : false;

            // Comprobar si el identificador ya existe
            $query_check = $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_mentions_by_career WHERE identificator = %s AND id != %d",
                $identificator,
                $mention_id
            );
            $identificator_exists = $wpdb->get_var($query_check);

            if ($identificator_exists > 0) {
                // Si el identificador ya existe, establece un mensaje de error y redirige.
                setcookie('message', __('Error: The identifier already exists.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=careers'));
                exit;
            }

            // Prepara los datos a insertar o actualizar
            $data = [
                'is_active' => $is_active,
                'career_identificator' => $career_identificator,
                'identificator' => $identificator,
                'name' => $name,
                'description' => $description,
            ];

            // crea o actualiza el sub programa
            if (!empty($mention_id)) {
                // Actualizar el registro
                $wpdb->update($table_mentions_by_career, $data, ['id' => $mention_id]);
                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
            } else {
                // Insertar un nuevo registro
                $wpdb->insert($table_mentions_by_career, $data);
                setcookie('message', __('New record added successfully.', 'edusystem'), time() + 10, '/');
            }

            wp_redirect(admin_url('admin.php?page=add_admin_form_student_program_content&section_tab=mentions'));
            exit;
        } else {
            $list_program = new TT_All_Student_Program_List_Table;
            $list_program->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-student-program.php');

            // modal de eleiminar un programa
            include(plugin_dir_path(__FILE__) . '/templates/modal-delete-program.php');
        }
    }
}

class TT_All_Student_Program_List_Table extends WP_List_Table
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
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_student_program_content&section_tab=program_details&from=programs&program_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a class='button button-danger' data-program_id='" . $item['id'] . "' onclick='modal_delete_program_js ( this )' ><span class='dashicons dashicons-trash'></span></a>";
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

        $table_student_program = $wpdb->prefix . 'student_program';
        $programs = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_student_program} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($programs) {
            foreach ($programs as $pensum) {
                array_push($programs_array, [
                    'id' => $pensum['id'],
                    'identificator' => $pensum['identificator'],
                    'status' => $pensum['is_active'] ? 'Active' : 'Inactive',
                    'name' => $pensum['name'],
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

class TT_All_Careers_List_Table extends WP_List_Table
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
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_student_program_content&section_tab=program_details&from=careers&career_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a class='button button-danger' data-program_id='" . $item['id'] . "' onclick='modal_delete_program_js ( this )' ><span class='dashicons dashicons-trash'></span></a>";
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
            'program' => __('Program', 'edusystem'),
            'identificator' => __('Identificator', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $careers_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_careers_by_program = $wpdb->prefix . 'careers_by_program';
        $careers = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_careers_by_program} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($careers) {
            foreach ($careers as $career) {
                $program = get_student_program_details_by_identificator($career['program_identificator']);
                array_push($careers_array, [
                    'id' => $career['id'],
                    'program' => $program->name,
                    'identificator' => $career['identificator'],
                    'status' => $career['is_active'] ? 'Active' : 'Inactive',
                    'name' => $career['name'],
                    'description' => $career['description'],
                    'created_at' => $career['created_at'],
                ]);
            }
        }

        return ['data' => $careers_array, 'total_count' => $total_count];
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

class TT_All_Mentions_List_Table extends WP_List_Table
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
                $buttons .= "<a href='" . admin_url('/admin.php?page=add_admin_form_student_program_content&section_tab=program_details&from=mentions&mention_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a class='button button-danger' data-program_id='" . $item['id'] . "' onclick='modal_delete_program_js ( this )' ><span class='dashicons dashicons-trash'></span></a>";
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
            'career' => __('Career', 'edusystem'),
            'identificator' => __('Identificator', 'edusystem'),
            'name' => __('Name', 'edusystem'),
            'status' => __('Status', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pensum()
    {
        global $wpdb;
        $mentions_array = [];

        // PAGINATION
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);
        // PAGINATION

        $table_mentions_by_career = $wpdb->prefix . 'mentions_by_career';
        $mentions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_mentions_by_career} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ($mentions) {
            foreach ($mentions as $mention) {
                $career = get_career_details_by_identificator($mention['career_identificator']);
                array_push($mentions_array, [
                    'id' => $mention['id'],
                    'identificator' => $mention['identificator'],
                    'career' => $career->name,
                    'status' => $mention['is_active'] ? 'Active' : 'Inactive',
                    'name' => $mention['name'],
                    'description' => $mention['description'],
                    'created_at' => $mention['created_at'],
                ]);
            }
        }

        return ['data' => $mentions_array, 'total_count' => $total_count];
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

function get_student_programs()
{
    global $wpdb;
    $table_student_program = $wpdb->prefix . 'student_program';

    $programs = $wpdb->get_results("SELECT * FROM {$table_student_program} WHERE is_active=1");
    return $programs;
}

function get_careers()
{
    global $wpdb;
    $table_careers_by_program = $wpdb->prefix . 'careers_by_program';

    $careers = $wpdb->get_results("SELECT * FROM {$table_careers_by_program} WHERE is_active=1");
    return $careers;
}