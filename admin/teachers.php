<?php

function add_admin_form_teachers_content()
{

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'teacher_details') {
            $teacher_id = $_GET['teacher_id'];
            $teacher = get_teacher_details($teacher_id);
            $documents = get_teacher_documents($teacher_id);
            include(plugin_dir_path(__FILE__) . 'templates/teacher-detail.php');
        }
        if ($_GET['section_tab'] == 'add_teacher') {
            include(plugin_dir_path(__FILE__) . 'templates/teacher-detail.php');
        }

    } else {

        if ($_GET['action'] == 'save_teacher_details') {
            global $wpdb;
            $table_teachers = $wpdb->prefix . 'teachers';
            $table_teacher_documents = $wpdb->prefix . 'teacher_documents';
            $table_documents_for_teachers = $wpdb->prefix . 'documents_for_teachers';

            $teacher_id = $_POST['teacher_id'];
            $type_document = $_POST['type_document'];
            $id_document = $_POST['id_document'];
            $birth_date = date_i18n('Y-m-d', strtotime($_POST['birth_date']));
            $gender = $_POST['gender'];
            $name = $_POST['name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];
            $middle_last_name = $_POST['middle_last_name'];
            $email = $_POST['email'];
            $old_email = $_POST['old_email'];
            $phone = $_POST['phone'];
            $phone_hidden = $_POST['phone_hidden'];
            $address = $_POST['address'];
            $password = $_POST['password'];
            $status = (isset($_POST['status']) && $_POST['status'] == 'on') ? 1 : 0;

            //update
            if (isset($teacher_id) && !empty($teacher_id)) {

                $wpdb->update($table_teachers, [
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'birth_date' => $birth_date,
                    'gender' => $gender,
                    'name' => $name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'middle_last_name' => $middle_last_name,
                    'email' => $email,
                    'phone' => $phone_hidden,
                    'address' => $address,
                    'status' => $status,
                ], ['id' => $teacher_id]);


                //TABLE USERS
                $table_users = $wpdb->prefix . 'users';
                $user_teacher = $wpdb->get_row("SELECT * FROM {$table_users} WHERE user_email='" . $old_email . "'");
                $user_teacher_exist = $wpdb->get_row("SELECT * FROM {$table_users} WHERE user_email='" . $email . "'");
                if (isset($user_teacher) && (isset($user_teacher_exist) && $email == $old_email) || (!isset($user_teacher_exist) && $email != $old_email)) {
                    $wpdb->update(
                        $wpdb->users,
                        array(
                            'user_email' => $email,
                            'user_login' => $email,
                            'display_name' => $name . ' ' . $last_name,
                        ),
                        array('ID' => $user_teacher->ID),
                        array('%s', '%s', '%s'),
                        array('%d')
                    );

                    if ($password && isset($user_teacher)) {
                        $user_id = $user_teacher->ID; // Replace with the ID of the user you want to update
                        wp_set_password($password, $user_teacher->ID);
                    }

                    //METAADATA
                    $username = $email;
                    update_user_meta($user_teacher->ID, 'first_name', $name);
                    update_user_meta($user_teacher->ID, 'billing_first_name', $name);
                    update_user_meta($user_teacher->ID, 'last_name', $last_name);
                    update_user_meta($user_teacher->ID, 'billing_last_name', $last_name);
                    update_user_meta($user_teacher->ID, 'nickname', $username);
                    update_user_meta($user_teacher->ID, 'birth_date', $birth_date);
                    update_user_meta($user_teacher->ID, 'gender', $gender);
                    update_user_meta($user_teacher->ID, 'billing_email', $email);
                    update_user_meta($user_teacher->ID, 'billing_phone', $phone);
                    update_user_meta($user_teacher->ID, 'document_type', $type_document);
                    update_user_meta($user_teacher->ID, 'type_document', $type_document);
                    update_user_meta($user_teacher->ID, 'id_document', $id_document);
                    actualizar_avatar_usuario($user_teacher->ID, '');
                }


                setcookie('message', __('Changes saved successfully.', 'aes'), time() + 3600, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_teachers_content&section_tab=teacher_details&teacher_id=' . $teacher_id));
                exit;
            } else {

                $wpdb->insert($table_teachers, [
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'birth_date' => $birth_date,
                    'gender' => $gender,
                    'name' => $name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'middle_last_name' => $middle_last_name,
                    'email' => $email,
                    'phone' => $phone_hidden,
                    'address' => $address,
                    'status' => $status,
                ]);
                $teacher_id = $wpdb->insert_id;

                $username = $email;
                $user_email = $email;
                if (username_exists($username)) {
                    $user_teacher_id = username_exists($username);
                    $user_teacher = new WP_User($user_teacher_id);
                    $user_teacher->remove_role('subscriber');
                    $user_teacher->set_role('teacher');
                } else {
                    $user_teacher_id = wp_create_user($username, $password, $user_email);
                    $user_teacher = new WP_User($user_teacher_id);
                    $user_teacher->remove_role('subscriber');
                    $user_teacher->set_role('teacher');
                }

                update_user_meta($user_teacher_id, 'first_name', $name);
                update_user_meta($user_teacher_id, 'billing_first_name', $name);
                update_user_meta($user_teacher_id, 'last_name', $last_name);
                update_user_meta($user_teacher_id, 'billing_last_name', $last_name);
                update_user_meta($user_teacher_id, 'nickname', $username);
                update_user_meta($user_teacher_id, 'birth_date', $birth_date);
                update_user_meta($user_teacher_id, 'gender', $gender);
                update_user_meta($user_teacher_id, 'billing_email', $email);
                update_user_meta($user_teacher_id, 'billing_phone', $phone);
                update_user_meta($user_teacher_id, 'document_type', $type_document);
                update_user_meta($user_teacher_id, 'type_document', $type_document);
                update_user_meta($user_teacher_id, 'id_document', $id_document);
                update_user_meta($user_teacher_id, 'status_register', 1);
                update_user_meta($user_teacher_id, 'teacher_id', $teacher_id);

                $documents = $wpdb->get_results("SELECT * FROM {$table_documents_for_teachers}");

                if ($documents) {
                    foreach ($documents as $document) {
                        $exist = $wpdb->get_row("SELECT * FROM {$table_teacher_documents} WHERE teacher_id = {$teacher_id} AND document_id = '{$document->name}'");
                        if (!$exist) {
                            $wpdb->insert($table_teacher_documents, [
                                'teacher_id' => $teacher_id,
                                'document_id' => $document->name,
                                'is_required' => $document->is_required,
                                'is_visible' => $document->is_visible,
                                'status' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }

                wp_redirect(admin_url('admin.php?page=add_admin_form_teachers_content'));
                exit;

            }
        } else if ($_GET['action'] == 'update_document_teacher') {
            global $wpdb, $current_user;
            $teacher_id = $_POST['teacher_id'] ?? $_POST['teacher_id_decline'];
            $document_id = $_POST['document_id'] ?? $_POST['document_id_decline'];
            $status_id = $_POST['status_id'] ?? $_POST['status_id_decline'];

            $description = (!$_POST['description'] || $_POST['description'] == 'null') ? null : $_POST['description'];
            $teacher = get_teacher_details($teacher_id);
            $user_teacher = get_user_by('email', $teacher->email);
            $table_users_notices =  $wpdb->prefix.'users_notices';
            switch ($status_id) {
                case 5:
                    $description = "Document approved";
                    break;
                case 3:
                        // $description = "Document approved";
                    break;
                default:
                    $description = "Status of document changed";
                    break;
            }
            $data = [
                'user_id' => $user_teacher->ID,
                'message' => $description,
                'importance' => $status_id == 3 ? 3 : 1,
                'type_notice' => 'documents',
            ];

            $wpdb->insert($table_users_notices, $data);

            $table_teacher_documents =  $wpdb->prefix.'teacher_documents';
            $wpdb->update($table_teacher_documents, ['approved_by' => $current_user->ID, 'status' => $status_id, 'updated_at' => date('Y-m-d H:i:s'), 'description' => $description], ['id' => $document_id]);

            $document_updated = $wpdb->get_row("SELECT * FROM {$table_teacher_documents} WHERE id = {$document_id}");
            if ($document_updated->document_id == 'PHOTO') {
                if ($status_id != 5) {
                    actualizar_avatar_usuario($user_teacher->ID, '');
                } else {
                    $url = wp_get_attachment_url($document_updated->attachment_id);
                    actualizar_avatar_usuario($user_teacher->ID, $url);
                }    
            }

            setcookie('message', __('Changes saved successfully.', 'aes'), time() + 3600, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_teachers_content&section_tab=teacher_details&teacher_id=' . $teacher_id));
            exit;
        } else {
            $list_teachers = new TT_teachers_all_List_Table;
            $list_teachers->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-teachers.php');
        }
    }
}

class TT_teachers_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'teacher_',
                'plural' => 'teacher_s',
                'ajax' => true
            )
        );

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'full_name':
                return strtoupper($item[$column_name]);
            case 'email':
                return $item[$column_name];
            case 'identification':
                return ucwords($item[$column_name]);
            case 'status':
                switch ($item[$column_name]) {
                    case 1:
                        return 'Active';
                        break;

                    default:
                        return 'Inactive';
                        break;
                }
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_teachers_content&section_tab=teacher_details&teacher_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'aes') . "</a>";
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
            'identification' => __('Identification', 'aes'),
            'full_name' => __('Full name', 'aes'),
            'email' => __('Email', 'aes'),
            'status' => __('Status', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_teachers()
    {
        global $wpdb;
        $teachers_array = [];

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $search = $_POST['s'];
            $teachers = $wpdb->get_results("SELECT * FROM wp_teachers WHERE (`name` LIKE '%{$search}%' || middle_name LIKE '%{$search}%' || last_name LIKE '%{$search}%' || middle_last_name LIKE '%{$search}%' || email LIKE '%{$search}%')  ORDER BY id DESC");
        } else {
            $teachers = $wpdb->get_results("SELECT * FROM wp_teachers ORDER BY id DESC");
        }

        if ($teachers) {
            foreach ($teachers as $teacher) {
                array_push($teachers_array, [
                    'id' => $teacher->id,
                    'identification' => get_type_document_student($teacher->type_document) . ' - ' . $teacher->id_document,
                    'full_name' => $teacher->name . ' ' . $teacher->middle_name . ' ' . $teacher->last_name . ' ' . $teacher->middle_last_name,
                    'email' => $teacher->email,
                    'status' => $teacher->status
                ]);
            }
        }

        return $teachers_array;
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

        $data_teachers = $this->get_teachers();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_teachers;

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

function get_teacher_details($teacher_id)
{
    global $wpdb;
    $table_teachers = $wpdb->prefix . 'teachers';

    $teacher = $wpdb->get_row("SELECT * FROM {$table_teachers} WHERE id='{$teacher_id}' OR email='{$teacher_id}'");
    return $teacher;
}

function get_teachers_active()
{
    global $wpdb;
    $table_teachers = $wpdb->prefix . 'teachers';

    $teachers = $wpdb->get_results("SELECT * FROM {$table_teachers} WHERE `status` = 1");
    return $teachers;
}

function get_teacher_documents($teacher_id)
{
    global $wpdb;
    $table_teacher_documents = $wpdb->prefix . 'teacher_documents';

    $documents = $wpdb->get_results("SELECT * FROM {$table_teacher_documents} WHERE teacher_id={$teacher_id}");
    return $documents;
}

// Función para actualizar el avatar de un usuario
function actualizar_avatar_usuario($user_id, $avatar_url) {
    // Actualiza el meta del usuario con la URL del avatar
    update_user_meta($user_id, 'custom_avatar', esc_url($avatar_url));
}

// Función para obtener el avatar personalizado
function obtener_avatar_personalizado($avatar, $id_or_email, $size, $default, $alt) {
    $user_id = null;

    // Verifica si se trata de un ID de usuario o un objeto de usuario
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user_id = (int) $id_or_email->user_id;
    }

    // Si hay un ID de usuario, busca el avatar personalizado
    if ($user_id) {
        $custom_avatar = get_user_meta($user_id, 'custom_avatar', true);
        if ($custom_avatar) {
            return '<img alt="' . esc_attr($alt) . '" src="' . esc_url($custom_avatar) . '" class="avatar avatar-' . (int) $size . ' photo" height="' . (int) $size . '" width="' . (int) $size . '" />';
        }
    }

    // Retorna el avatar por defecto si no hay avatar personalizado
    return $avatar;
}

add_filter('get_avatar', 'obtener_avatar_personalizado', 10, 5);
