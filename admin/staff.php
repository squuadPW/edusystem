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
            include (plugin_dir_path(__FILE__) . 'templates/staff-detail.php');
        }
        if ($_GET['section_tab'] == 'add_staff') {
            include (plugin_dir_path(__FILE__) . 'templates/staff-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_staff_details') {
            $staff_id = $_POST['staff_id'];
            $user_login = $_POST['user_login'] ?? null;
            $email = $_POST['email'];
            $password = $_POST['password'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $display_name = $_POST['display_name'];
            $roles = $_POST['user_roles'];

            if (isset($staff_id) && !empty($staff_id)) {
                $user_data = array(
                    'ID' => $staff_id,
                    'display_name' => $display_name,
                    'user_email' => $email,
                );

                if ($password != '') {
                    $user_data['user_pass'] = $password;
                }
                wp_update_user($user_data);

                $user = new WP_User($staff_id);
                foreach ($roles as $key => $role) {
                    $user->set_role($role);
                }
                update_user_meta($staff_id, 'first_name', $first_name);
                update_user_meta($staff_id, 'last_name', $last_name);
                update_user_meta($staff_id, 'billing_email', $email);
                setcookie('message', __('Changes saved successfully.', 'aes'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content&section_tab=staff_details&staff_id=' . $staff_id));
                exit;
            } else {
                // Crear usuario
                $user_id = wp_create_user($user_login, $password, $email);
                $user = new WP_User($user_id);
                foreach ($roles as $key => $role) {
                    $user->set_role($role);
                }
                update_user_meta($user_id, 'first_name', $first_name);
                update_user_meta($user_id, 'last_name', $last_name);
                update_user_meta($user_id, 'billing_email', $email);
                wp_redirect(admin_url('admin.php?page=add_admin_form_staff_content'));
                exit;
            }
        } else {
            $list_staff = new TT_staff_all_List_Table;
            $list_staff->prepare_items();
            include (plugin_dir_path(__FILE__) . 'templates/list-staff.php');
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
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_staff_content&section_tab=staff_details&staff_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
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
            'user_login' => __('User login', 'aes'),
            'email' => __('Email', 'aes'),
            'display_name' => __('Display name', 'aes'),
            'names' => __('Names', 'aes'),
            'roles' => __('Roles', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_staff()
    {
        global $wpdb;
        $staff_array = [];

        $args = array(
            'role__in' => ROLES_OF_STAFF,
        );

        $staffs = get_users($args);

        if ($staffs) {
            foreach ($staffs as $staff) {
                $user_data = get_userdata($staff->ID);
                $roles = $user_data->roles;
                $roles = array_map(function($value) {
                    return str_replace("administrador", "manager", $value);
                }, $roles);

                array_push($staff_array, [
                    'id' => $staff->ID,
                    'user_login' => $staff->user_login,
                    'display_name' => $staff->display_name,
                    'names' => get_user_meta($staff->ID, 'first_name', true) . ' ' . get_user_meta($staff->ID, 'last_name', true),
                    'email' => $staff->user_email,
                    'roles' => implode(', ', $roles),
                ]);
            }

            usort($staff_array, function($a, $b) {
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