<?php

function add_admin_form_staff_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_staff') {
            try {
                wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content'));
                exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'staff_details') {
            $staff_id = $_GET['staff_id'];
            $staff = get_staff_details($staff_id);
            $managers = get_users_managers();
            include(plugin_dir_path(__FILE__) . 'templates/staff-detail.php');
        }

        if ($_GET['section_tab'] == 'add_staff') {
            $managers = get_users_managers();
            include(plugin_dir_path(__FILE__) . 'templates/staff-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_staff_details') {

            // 1. Sanitización y validación de datos de entrada
            $staff_id = isset($_POST['staff_id']) ? absint($_POST['staff_id']) : 0;
            $user_login = isset($_POST['user_login']) ? sanitize_user($_POST['user_login']) : null;
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : ''; // La contraseña se manejará por WP, no se sanitiza aquí.
            $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
            $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
            $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
            $rol = isset($_POST['user_rol']) ? $_POST['user_rol'] : '';
            $manager_user_id = isset($_POST['manager_user_id']) ? absint($_POST['manager_user_id']) : 0;

            // Pequeña validación básica
            if (empty($email) || (empty($staff_id) && empty($user_login))) {
                setcookie('message', __('Error: Datos incompletos para procesar la solicitud.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content'));
                exit;
            }

            $is_update = ($staff_id > 0);
            $user_id = $staff_id;

            if ($is_update) {
                // 2. Actualizar usuario existente
                $user_data = [
                    'ID' => $user_id,
                    'display_name' => $display_name,
                    'user_email' => $email,
                ];

                if (!empty($password)) {
                    $user_data['user_pass'] = $password;
                }

                $result = wp_update_user($user_data);

                if (is_wp_error($result)) {
                    setcookie('message', sprintf(__('Error al actualizar el usuario: %s', 'edusystem'), $result->get_error_message()), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content&section_tab=staff_details&staff_id=' . $staff_id));
                    exit;
                }

            } else {

                // 3. Crear nuevo usuario
                if (empty($user_login) || empty($password)) {
                    setcookie('message', __('Error: Nombre de usuario y contraseña son requeridos para crear un nuevo usuario.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content'));
                    exit;
                }

                $user_id = wp_create_user($user_login, $password, $email);

                if (is_wp_error($user_id)) {
                    setcookie('message', sprintf(__('Error al crear el usuario: %s', 'edusystem'), $user_id->get_error_message()), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content'));
                    exit;
                }

            }

            // 4. Manejo de roles (aplicable tanto a creación como actualización)
            $user_obj = new WP_User($user_id);
            if (!empty($rol)) {
                
                // Eliminar roles existentes antes de asignar los nuevos para evitar duplicados o roles no deseados
                $user_obj->set_role($rol);
                
            } else {
                // Si no se envían roles, establecer un rol predeterminado o eliminar todos los roles
                $user_obj->set_role(get_option('default_role'));
            }

            // 5. Actualización de meta-datos de usuario (consolidado)
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'billing_email', $email); // ¿Es 'billing_email' el campo correcto para el email general?
            update_user_meta($user_id, 'manager_user_id', $manager_user_id);

            // 6. Redirección y mensaje de éxito
            $redirect_url = admin_url('admin.php?page=add_admin_form_staff_content');
            if ($is_update) {
                setcookie('message', __('Cambios guardados exitosamente.', 'edusystem'), time() + 10, '/');
                $redirect_url = admin_url('admin.php?page=add_admin_form_staff_content&section_tab=staff_details&staff_id=' . $user_id);
            } else {
                setcookie('message', __('Usuario creado exitosamente.', 'edusystem'), time() + 10, '/');
            }   

            // wp_redirect($redirect_url);
            exit;
        } else {
            $list_staff = new TT_staff_all_List_Table;
            $list_staff->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-staff.php');
        }
    }
}

class TT_staff_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'staff',
                'plural' => 'staffs',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'id':
                return '#' . $item[$column_name];
            case 'user_login':
                return $item[$column_name];
            case 'display_name':
                return $item[$column_name];
            case 'email':
                return $item[$column_name];
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_staff_content&section_tab=staff_details&staff_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
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
            'user_login' => __('User login', 'edusystem'),
            'email' => __('Email', 'edusystem'),
            'display_name' => __('Display name', 'edusystem'),
            'names' => __('Names', 'edusystem'),
            'roles' => __('Roles', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_staff()
    {
        global $wpdb;
        $staff_array = [];

        // Si viene un rol desde el select 
        if (!empty($_POST['role'])) { 
            $selected_role = sanitize_text_field($_POST['role']); 
            $args['role'] = $selected_role; // filtra por un único rol 
        } else {
            $args = array(
                'role__in' => ROLES_OF_STAFF,
            );
        }

        if( !empty($_POST['s']) ) {
            $search_term = sanitize_text_field($_POST['s']);
            $args['search'] = '*' . esc_attr($search_term) . '*';
            $args['search_columns'] = array('user_login', 'user_email', 'display_name');

        }

        $staffs = get_users($args);

        if ($staffs) {
            foreach ( $staffs as $staff ) {
                $user_data = get_userdata($staff->ID);
                $roles = $user_data->roles;
                /* $roles = array_map(function ($value) {
                    return str_replace("administrador", "manager", $value);
                }, $roles); */

                array_push($staff_array, [
                    'id' => $staff->ID,
                    'user_login' => $staff->user_login,
                    'display_name' => $staff->display_name,
                    'names' => get_user_meta($staff->ID, 'first_name', true) . ' ' . get_user_meta($staff->ID, 'last_name', true),
                    'email' => $staff->user_email,
                    'roles' => implode(', ', $roles),
                ]);
            }

            usort($staff_array, function ($a, $b) {
                return strnatcasecmp($a['roles'], $b['roles']);
            });
        }

        return $staff_array;
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

        $data_staff = $this->get_staff();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_staff;

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $this->items = $data;
    }

}

function get_staff_details($staff_id)
{

    global $wpdb;
    $table_staffs = $wpdb->prefix . 'users';

    $staff = $wpdb->get_row("SELECT * FROM {$table_staffs} WHERE id={$staff_id}");
    return $staff;
}