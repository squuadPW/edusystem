<?php

function add_admin_form_admission_content()
{

    global $current_user, $wpdb;
    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'save_users_details') {
            global $wpdb;

            //STUDENT
            $id = $_POST['id'];
            $program = $_POST['program'];
            $grade = $_POST['grade'];
            $document_type = $_POST['document_type'];
            $id_document = $_POST['id_document'];
            $academic_period = $_POST['academic_period'];
            $username = $_POST['username'] ?? null;
            $new_password = $_POST['new_password'] ?? null;
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];
            $middle_last_name = $_POST['middle_last_name'];
            $birth_date = $_POST['birth_date'];
            $gender = $_POST['gender'];
            $country = $_POST['country'];
            $city = $_POST['city'];
            $postal_code = $_POST['postal_code'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $old_email = $_POST['old_email'];

            //TABLE STUDENTS
            $table_students = $wpdb->prefix . 'students';
            $wpdb->update(
                $table_students,
                array(
                    'type_document' => $document_type,
                    'id_document' => $id_document,
                    'academic_period' => $academic_period,
                    'name' => $first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'middle_last_name' => $middle_last_name,
                    'birth_date' => date('Y-m-d', strtotime($birth_date)),
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'city' => $city,
                    'country' => $country,
                    'postal_code' => $postal_code,

                ),
                array('ID' => $id),
                array('%s', '%s', '%s'),
                array('%d')
            );

            //TABLE USERS
            $table_users = $wpdb->prefix . 'users';
            $user_student = $wpdb->get_row("SELECT * FROM {$table_users} WHERE user_email='" . $old_email . "'");
            if (isset($user_student)) {
                $wpdb->update(
                    $wpdb->users,
                    array(
                        'user_email' => $email,
                        'user_login' => $email,
                        'user_nicename' => $username,
                        'display_name' => $first_name . ' ' . $last_name,
                    ),
                    array('ID' => $user_student->ID),
                    array('%s', '%s', '%s'),
                    array('%d')
                );
            }

            if ($new_password && isset($user_student)) {
                $user_id = $user_student->ID; // Replace with the ID of the user you want to update
                wp_set_password($new_password, $user_student->ID);
            }

            //METAADATA
            update_user_meta($id, 'first_name', $first_name);
            update_user_meta($id, 'last_name', $last_name);
            update_user_meta($id, 'nickname', $username);
            update_user_meta($id, 'birth_date', $birth_date);
            update_user_meta($id, 'gender', $gender);
            update_user_meta($id, 'billing_email', $email);
            update_user_meta($id, 'billing_phone', $phone);

            //PARENT
            $parent_id = $_POST['parent_id'];
            $parent_document_type = $_POST['parent_document_type'];
            $parent_id_document = $_POST['parent_id_document'];
            $parent_username = $_POST['parent_username'];
            $parent_first_name = $_POST['parent_first_name'];
            $parent_last_name = $_POST['parent_last_name'];
            $parent_birth_date = $_POST['parent_birth_date'];
            $parent_gender = $_POST['parent_gender'];
            $parent_country = $_POST['parent_country'];
            $parent_city = $_POST['parent_city'];
            $parent_postal_code = $_POST['parent_postal_code'];
            $parent_email = $_POST['parent_email'];
            $parent_phone = $_POST['parent_phone'];
            $parent_occupation = $_POST['parent_occupation'];

            //TABLA USERS
            $wpdb->update(
                $wpdb->users,
                array(
                    'user_email' => $parent_email,
                    'user_login' => $parent_email,
                    'user_nicename' => $parent_username,
                    'display_name' => $parent_first_name . ' ' . $parent_last_name,
                ),
                array('ID' => $parent_id),
                array('%s', '%s', '%s'),
                array('%d')
            );

            //METAADATA
            update_user_meta($parent_id, 'first_name', $parent_first_name);
            update_user_meta($parent_id, 'billing_first_name', $parent_first_name);
            update_user_meta($parent_id, 'last_name', $parent_last_name);
            update_user_meta($parent_id, 'billing_last_name', $parent_last_name);
            update_user_meta($parent_id, 'nickname', $parent_username);
            update_user_meta($parent_id, 'birth_date', $parent_birth_date);
            update_user_meta($parent_id, 'gender', $parent_gender);
            update_user_meta($parent_id, 'billing_country', $parent_country);
            update_user_meta($parent_id, 'billing_city', $parent_city);
            update_user_meta($parent_id, 'billing_postcode', $parent_postal_code);
            update_user_meta($parent_id, 'billing_email', $parent_email);
            update_user_meta($parent_id, 'billing_phone', $parent_phone);
            update_user_meta($parent_id, 'occupation', $parent_occupation);
            update_user_meta($parent_id, 'document_type', $parent_document_type);
            update_user_meta($parent_id, 'id_document', $parent_id_document);


            wp_redirect(admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $id));
            exit;
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {

        /*
        if($_GET['section_tab'] == 'document_review'){

            $list_students = new TT_document_review_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__).'templates/list-student-documents.php');
         */
        if ($_GET['section_tab'] == 'all_students') {
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $list_students = new TT_all_student_List_Table;
            $list_students->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-student-documents.php');
        } else if ($_GET['section_tab'] == 'student_details') {

            $roles = $current_user->roles;
            $documents = get_documents($_GET['student_id']);
            $student = get_student_detail($_GET['student_id']);
            $countries = get_countries();
            $partner = get_userdata($student->partner_id);
            $table_users = $wpdb->prefix . 'users';
            $table_academic_periods = $wpdb->prefix . 'academic_periods';
            $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
            $user_student = $wpdb->get_row("SELECT * FROM {$table_users} WHERE user_email='" . $student->email . "'");
            include(plugin_dir_path(__FILE__) . 'templates/student-details.php');
        }

    } else {
        $table_academic_periods = $wpdb->prefix . 'academic_periods';
        $periods = $wpdb->get_results("SELECT * FROM {$table_academic_periods} ORDER BY created_at ASC");
        $list_students = new TT_document_review_List_Table;
        $list_students->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-student-documents.php');
    }
}

class TT_new_student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'full_name':
                return $item['name'] . ' ' . $item['last_name'];
            case 'program':
                $program = get_name_program($item['program_id']);
                return $program;
            case 'grade':
                $grade = get_name_grade($item['grade_id']);
                return $grade;
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'full_name' => __('Full name', 'aes'),
            'program' => __('Program', 'aes'),
            'grade' => __('Grade', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_new_students()
    {

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';

        if (isset($_POST['s']) && !empty($_POST['s'])) {
            $search = $_POST['s'];
            if (isset($_POST['academic_period']) && !empty($_POST['academic_period'])) {
                $period = $_POST['academic_period'];
                $data = $wpdb->get_results("SELECT a.* 
                FROM {$table_students} as a 
                JOIN {$table_student_documents} b on b.student_id = a.id 
                WHERE status_id=1 AND a.academic_period = '$period' AND b.status != 0 AND 
                (a.name  LIKE '{$search}%' OR a.last_name LIKE '{$search}%' OR email OR id_document LIKE '{$search}%')
                GROUP BY a.id
                ", "ARRAY_A");
            } else {
                $data = $wpdb->get_results("SELECT a.* 
                FROM {$table_students} as a 
                JOIN {$table_student_documents} b on b.student_id = a.id 
                WHERE status_id=1 AND b.status != 0 AND 
                (a.name  LIKE '{$search}%' OR a.last_name LIKE '{$search}%' OR email or id_document LIKE '{$search}%')
                GROUP BY a.id
                ", "ARRAY_A");
            }
        } else {
            if (isset($_POST['academic_period']) && !empty($_POST['academic_period'])) {
                $period = $_POST['academic_period'];
                $data = $wpdb->get_results("SELECT a.* 
                FROM {$table_students} as a 
                JOIN {$table_student_documents} b on b.student_id = a.id 
                WHERE status_id=1 AND a.academic_period = '$period' AND b.status != 0
                GROUP BY a.id
                ", "ARRAY_A");
            } else {
                $data = $wpdb->get_results("SELECT a.* 
                FROM {$table_students} as a 
                JOIN {$table_student_documents} b on b.student_id = a.id 
                WHERE status_id=1 AND b.status != 0
                GROUP BY a.id
                ", "ARRAY_A");
            }
        }

        return $data;
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

        $data_categories = $this->get_new_students();

        $per_page = 20;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_categories;

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

class TT_document_review_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
            'ajax' => true
        ));

    }

    function single_row($item)
    {
        static $row_class = '';
        $row_class = ($row_class == '' ? 'alternate' : '');

        // Add your custom styles here
        $style = '';
        if ($item['review_pending_documents'] > 0) {
            $style = 'style="background-color: #c6c5e0 !important;"';
        }

        echo '<tr id="user_' . $item->ID . '" class="' . $row_class . '" ' . $style . '>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'index':
                return $item['index'];
            case 'full_name':
                return $item['name'] . ' ' . $item['last_name'];
            case 'program':
                $program = get_name_program($item['program_id']);
                return $program;
            case 'grade':
                $grade = get_name_grade($item['grade_id']);
                return $grade;
            case 'pending_documents':
                return $item['count_pending_documents'];
            case 'approved_documents':
                return $item['approved_pending_documents'];
            case 'pending_review_documents':
                return $item['review_pending_documents'];
            case 'rejected_documents':
                return $item['rejected_documents'];
            case 'moodle_active':
                return $item['moodle_active'];
            case 'waiting_time':
                $html = "";
                if (isset($item['updated_at'])) {
                    $updated_at = Datetime::createFromFormat('Y-m-d H:i:s', wp_date('Y-m-d H:i:s', strtotime($item['updated_at'])));
                    $time_now = Datetime::createFromFormat('Y-m-d H:i:s', wp_date('Y-m-d H:i:s'));
                    $diff = $updated_at->diff($time_now);

                    if ($diff->days < 15) {

                        if ($diff->days == 1) {

                            $html .= '
                                <a href="javascript:void(0)" class="button button-success" style="border-radius:9px;">
                                    <span>' . $diff->days . ' ' . __('Day', 'aes') . '</span>
                                </a>
                            ';

                        } else {

                            $html .= '
                                <a href="javascript:void(0)" class="button button-success" style="border-radius:9px;">
                                    <span>' . $diff->days . ' ' . __('Days', 'aes') . '</span>
                                </a>
                            ';
                        }

                    } else if ($diff->days < 35) {
                        $html .= '
                            <a href="javascript:void(0)" class="button button-warning" style="border-radius:9px;">
                                <span>' . $diff->days . ' ' . __('Days', 'aes') . '</span>
                            </a>
                        ';
                    } else if ($diff->days >= 35) {
                        $html .= '
                            <a href="javascript:void(0)" class="button button-danger" style="border-radius:9px;">
                                <span>' . $diff->days . ' ' . __('Days', 'aes') . '</span>
                            </a>
                        ';
                    }
                } else {
                    $html .= '
                    <a href="javascript:void(0)" class="button button-danger" style="border-radius:9px;">
                        <span>' . __('N/A Days', 'aes') . '</span>
                    </a>
                ';
                }


                return $html;
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'full_name' => __('Full name', 'aes'),
            'program' => __('Program', 'aes'),
            'grade' => __('Grade', 'aes'),
            'pending_documents' => __('Pending', 'aes'),
            'pending_review_documents' => __('for Review', 'aes'),
            'approved_documents' => __('Approved', 'aes'),
            'rejected_documents' => __('Rejected', 'aes'),
            'moodle_active' => __('Moodle', 'aes'),
            'waiting_time' => __('Waiting Time', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_pending_students()
    {

        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $search = sanitize_text_field($_GET['s']);
        $period = sanitize_text_field($_GET['academic_period']);
        $date_selected = sanitize_text_field($_GET['date_selected']);

        if ($date_selected) {
            $today = date('Y-m-d'); // get today's date
            $filter_date = '';
            switch ($date_selected) {
                case '1':
                    $filter_date = date('Y-m-d', strtotime('-15 days', strtotime($today))); // 15 days ago
                    break;
                case '2':
                    $filter_date = date('Y-m-d', strtotime('-35 days', strtotime($today))); // 35 days ago
                    break;
                case '3':
                    $filter_date = date('Y-m-d', strtotime('-365 days', strtotime($today))); // more than 35 days ago
                    break;
            }
        }

        $data = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS a.*,
        (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 0) AS count_pending_documents,
        (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 5) AS approved_pending_documents,
        (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 1) AS review_pending_documents,
        (SELECT count(id) FROM {$table_student_documents} WHERE student_id = a.id AND status = 3 OR status = 4) AS rejected_documents
        FROM {$table_students} as a 
        JOIN {$table_student_documents} b on b.student_id = a.id 
        WHERE (b.status != 5)
        " . ($search ? " AND a.name  LIKE '{$search}%' OR a.last_name LIKE '{$search}%' OR email OR id_document LIKE '{$search}%'" : "") . "
        " . ($date_selected ? " AND a.created_at >= '$filter_date'" : "") . "
        " . ($period ? " AND a.academic_period = '$period'" : "") . "
        GROUP BY a.id
        ORDER BY a.updated_at DESC
        LIMIT {$per_page} OFFSET {$offset}
        ", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $data, 'total_count' => $total_count];
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

        $pending_students = $this->get_pending_students();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data_categories = $pending_students['data'];
        $data = $pending_students['data'];
        $total_count = (int) $pending_students['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $data = array();
        foreach ($data_categories as $key => $value) {
            $value['index'] = $key + 1;
            $moodleActive = isset($value['moodle_student_id']) ? 'Yes' : 'No';
            $moodleActiveStyle = $moodleActive == 'Yes' ? 'style="background-color: #f98012; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px; cursor: pointer;padding: 4px"' : 'style="background-color: #dfdedd; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px;padding: 4px; cursor: not-allowed"';
            $value['moodle_active'] = '<span class="moodle-active" data-moodle="' . $moodleActive . '" data-student_id="' . $value['id'] . '" ' . $moodleActiveStyle . '>' . $moodleActive . '</span>';
            $data[] = $value;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }

}

class TT_all_student_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'index':
                return $item['index'];
            case 'student':
                return $item['student'];
            case 'email':
                return $item['email'];
            case 'parent':
                return $item['parent'];
            case 'program':
                $program = get_name_program($item['program_id']);
                return $program;
            case 'grade':
                $grade = get_name_grade($item['grade_id']);
                return $grade;
            case 'moodle_active':
                return $item['moodle_active'];
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_admission_content&section_tab=student_details&student_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'form-plugin') . "</a>";
            default:
                return print_r($item, true);
        }
    }

    function column_name($item)
    {

        return sprintf(
            '%1$s<a href="javascript:void(0)">%2$s</a>',
            '<span data-id="' . $item['id'] . '" class="dashicons dashicons-menu handle" style="cursor:all-scroll;"></span>',
            ucwords($item['name']),
        );
    }

    function column_cb($item)
    {
        return '';
    }

    function get_columns()
    {

        $columns = array(
            'student' => __('Student', 'aes'),
            'parent' => __('Parent', 'aes'),
            'program' => __('Program', 'aes'),
            'grade' => __('Grade', 'aes'),
            'moodle_active' => __('Moodle', 'aes'),
            'view_details' => __('Actions', 'aes'),
        );

        return $columns;
    }

    function get_students()
    {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $per_page = 20; // number of items per page
        $pagenum = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $search = sanitize_text_field($_GET['s']);
        $period = sanitize_text_field($_GET['academic_period']);
        $date_selected = sanitize_text_field($_GET['date_selected']);


        if ($date_selected) {
            $today = date('Y-m-d'); // get today's date
            $filter_date = '';
            switch ($date_selected) {
                case '1':
                    $filter_date = date('Y-m-d', strtotime('-15 days', strtotime($today))); // 15 days ago
                    break;
                case '2':
                    $filter_date = date('Y-m-d', strtotime('-35 days', strtotime($today))); // 35 days ago
                    break;
                case '3':
                    $filter_date = date('Y-m-d', strtotime('-365 days', strtotime($today))); // more than 35 days ago
                    break;
            }
        }

        $data = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM {$table_students} WHERE (status_id = 2 OR status_id = 1)
        " . ($search ? " AND name  LIKE '{$search}%' OR last_name LIKE '{$search}%' OR email OR id_document LIKE '{$search}%'" : "") . "
        " . ($date_selected ? " AND created_at >= '$filter_date'" : "") . "
        " . ($period ? " AND academic_period = '$period'" : "") . " ORDER BY name ASC LIMIT {$per_page} OFFSET {$offset}", "ARRAY_A");

        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");
        return ['data' => $data, 'total_count' => $total_count];
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

        $students = $this->get_students();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $students['data'];
        $total_count = (int) $students['total_count'];

        function usort_reorder($a, $b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'order';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $data = array();
        $url = admin_url('user-edit.php?user_id=');
        global $current_user;
        $roles = $current_user->roles;
        foreach ($students['data'] as $key => $value) {
            $parent = get_user_by('id', $value['partner_id']);
            $student = get_user_by('email', $value['email']);

            $value['index'] = $key + 1;
            if (in_array('owner', $roles) || in_array('administrator', $roles)) {
                $value['student'] = '<a href="' . $url . $student->ID . '" target="_blank">' . $value['name'] . ' ' . $value['middle_name'] . ' ' . $value['last_name'] . ' ' . $value['middle_last_name'] . '</a>';
                $value['parent'] = '<a href="' . $url . $parent->ID . '" target="_blank">' . $parent->first_name . ' ' . $parent->last_name . '</a>';
            } else {
                $value['student'] = $value['name'] . ' ' . $value['middle_name'] . ' ' . $value['last_name'] . ' ' . $value['middle_last_name'];
                $value['parent'] = $parent->first_name . ' ' . $parent->last_name;
            }
            $moodleActive = isset($value['moodle_student_id']) ? 'Yes' : 'No';
            $moodleActiveStyle = $moodleActive == 'Yes' ? 'style="background-color: #f98012; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px; cursor: pointer;padding: 4px"' : 'style="background-color: #dfdedd; text-align: center; border-radius: 6px; font-weight: bold; color: #000000; width: 40px;padding: 4px; cursor: not-allowed"';
            $value['moodle_active'] = '<span class="moodle-active" data-moodle="' . $moodleActive . '" data-student_id="' . $value['id'] . '" ' . $moodleActiveStyle . '>' . $moodleActive . '</span>';
            $data[] = $value;
        }

        $per_page = 20; // items per page
        $this->set_pagination_args(array(
            'total_items' => $total_count,
            'per_page' => $per_page,
        ));

        $this->items = $data;
    }

}

function get_student_detail($student_id)
{

    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $data = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    return $data;
}

function update_status_documents()
{

    if (isset($_POST['document_id']) && !empty($_POST['document_id']) && isset($_POST['status']) && !empty($_POST['status']) && isset($_POST['student_id']) && !empty($_POST['student_id'])) {

        global $wpdb, $current_user;
        $roles = $current_user->roles;
        $table_student_documents = $wpdb->prefix . 'student_documents';
        $student_id = $_POST['student_id'];
        $status_id = $_POST['status'];
        $document_id = $_POST['document_id'];

        $wpdb->update($table_student_documents, ['approved_by' => $current_user->ID, 'status' => $status_id, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $document_id, 'student_id' => $student_id]);

        if ($status_id == 3) {
            $email_rejected_document = WC()->mailer()->get_emails()['WC_Rejected_Document_Email'];
            $email_rejected_document->trigger($student_id, $document_id);
        }

        // if ($status_id == 5) {
        //     $email_rejected_document = WC()->mailer()->get_emails()['WC_Approved_Document_Email'];
        //     $email_rejected_document->trigger($student_id, $document_id);
        // }
        /*
        if($document_id == 'id_student' && $status_id == 5){
            update_status_student($student_id,2);
        }
        */

        $documents = get_documents($student_id);

        $html = "";
        $solvency_administrative = true;

        foreach ($documents as $document) {

            if ($document->status != 5) {
                $solvency_administrative = false;
            }

            if ($document->id == $document_id) {
                $html .= '<tr id="' . 'tr_document_' . $document->id . '">';
                $html .= '<td class="column-primary">';
                $html .= get_name_document($document->document_id);
                $html .= "<button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>";
                $html .= "</td>";

                $html .= '<td id="' . 'td_document_' . $document->document_id . '" data-colname="' . __('Status', 'aes') . '">';
                $html .= "<b>";
                $html .= get_status_document($document->status);
                $html .= "</b>";
                $html .= "</td>";

                $html .= '<td id="' . 'td_upload_at_' . $document->document_id . '" data-colname="' . __('Date upload', 'aes') . '">';
                $html .= "<b>";
                $html .= $document->upload_at ?? 'N/A';;
                $html .= "</b>";
                $html .= "</td>";

                $approved_by = '';
                if ($document->updated_at) {
                    if (in_array('owner', $roles) || in_array('administrator', $roles) || in_array('administrador', $roles)) {
                        if ($document->approved_by) { 
                            $approved_by = $document->updated_at . ' by ' . get_user_meta($document->approved_by, 'first_name', true) . ' ' . get_user_meta($document->approved_by, 'last_name', true);
                        }
                    } else {
                        $approved_by = $document->updated_at;
                    }
                } else {
                    $approved_by = 'N/A';
                }

                $html .= '<td id="' . 'td_updated_at_' . $document->document_id . '" data-colname="' . __('Date updated', 'aes') . '">';
                $html .= "<b>";
                $html .= $approved_by;
                $html .= "</b>";
                $html .= "</td>";

                $html .= '<td data-colname="' . __('Actions', 'aes') . '">';
                if ($document->status > 0) {
                    $html .= '<a target="_blank" href="' . wp_get_attachment_url($document->attachment_id) . '" class="button button-primary">' . __('View', 'aes') . '</a>';

                    // Revert button
                    if ($document->status != 1) {
                        $html .= '<button data-document-id="' . $document->id . '" data-student-id="' . $document->student_id . '" data-status="1" class="button change-status button-warning" style="margin-left: 3px; margin-right: 3px;">' . __('Revert', 'aes') . '</button>';
                    }

                    // Approved button
                    if ($document->status != 5 && $document->status != 3) {
                        $html .= '<button data-document-id="' . $document->id . '" data-student-id="' . $document->student_id . '" data-status="5" class="button change-status button-success" style="margin-left: 3px; margin-right: 3px;">' . __('Approve', 'aes') . '</button>';
                    }

                    // Declined button
                    if ($document->status != 5 && $document->status != 3) {
                        $html .= '<button data-document-id="' . $document->id . '" data-student-id="' . $document->student_id . '" data-status="3" class="button change-status button-danger" style="margin-left: 3px; margin-right: 3px;">' . __('Decline', 'aes') . '</button>';
                    }
                }
                $html .= "</td>";
                $html .= "</tr>";
            }
        }

        if ($solvency_administrative) {
            update_status_student($student_id, 3);
        }

        $table_student_documents = $wpdb->prefix . 'student_documents';
        $table_student_payment = $wpdb->prefix . 'student_payments';
        $table_students = $wpdb->prefix . 'students';
        $access_virtual = true;
        $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");
        if ($documents_student) {
            foreach ($documents_student as $document) {
                if ($document->status != 5) {
                    $access_virtual = false;
                }
            }

            // VERIFICAR FEE DE INSCRIPCION
            $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = " . AES_FEE_INSCRIPTION);
            // VERIFICAR FEE DE INSCRIPCION
            //virtual classroom
            if ($access_virtual && isset($paid)) {
                $table_name = $wpdb->prefix . 'students'; // assuming the table name is "wp_students"
                $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $student_id));
                $type_document = array(
                    'identification_document' => 1,
                    'passport' => 2,
                    'ssn' => 4,
                )[$student->type_document];

                $files_to_send = array();
                $type_document = '';
                switch ($student->type_document) {
                    case 'identification_document':
                        $type_document = 1;
                        break;
                    case 'passport':
                        $type_document = 2;
                        break;
                    case 'ssn':
                        $type_document = 4;
                        break;
                }

                $type_document_re = '';
                switch (get_user_meta($student->partner_id, 'type_document', true)) {
                    case 'identification_document':
                        $type_document_re = 1;
                        break;
                    case 'passport':
                        $type_document_re = 2;
                        break;
                    case 'ssn':
                        $type_document_re = 4;
                        break;
                }


                $gender = '';
                switch ($student->gender) {
                    case 'male':
                        $gender = 'M';
                        break;
                    case 'female':
                        $gender = 'F';
                        break;
                }


                $gender_re = '';
                switch (get_user_meta($student->partner_id, 'gender', true)) {
                    case 'male':
                        $gender_re = 'M';
                        break;
                    case 'female':
                        $gender_re = 'F';
                        break;
                }

                $grade = '';
                switch ($student->grade_id) {
                    case 1:
                        $grade = 9;
                        break;
                    case 2:
                        $grade = 10;
                        break;
                    case 3:
                        $grade = 11;
                        break;
                    case 4:
                        $grade = 12;
                        break;
                }
                $fields_to_send = array(
                    // DATOS DEL ESTUDIANTE
                    'id_document' => $student->id_document,
                    'type_document' => $type_document,
                    'firstname' => $student->name . ' ' . $student->middle_name,
                    'lastname' => $student->last_name . ' ' . $student->middle_last_name,
                    'birth_date' => $student->birth_date,
                    'phone' => $student->phone,
                    'email' => $student->email,
                    'etnia' => $student->ethnicity,
                    'grade' => $grade,
                    'gender' => $gender,
                    'cod_period' => $student->academic_period,

                    // PADRE
                    'id_document_re' => get_user_meta($student->partner_id, 'id_document', true),
                    'type_document_re' => $type_document_re,
                    'firstname_re' => get_user_meta($student->partner_id, 'first_name', true),
                    'lastname_re' => get_user_meta($student->partner_id, 'last_name', true),
                    'birth_date_re' => get_user_meta($student->partner_id, 'birth_date', true),
                    'phone_re' => get_user_meta($student->partner_id, 'billing_phone', true),
                    'email_re' => get_user_meta($student->partner_id, 'billing_email', true),
                    'gender_re' => $gender_re,

                    'cod_program' => AES_PROGRAM_ID,
                    'cod_tip' => AES_TYPE_PROGRAM,
                    'address' => get_user_meta($student->partner_id, 'billing_address_1', true),
                    'country' => get_user_meta($student->partner_id, 'billing_country', true),
                    'city' => get_user_meta($student->partner_id, 'billing_city', true),
                    'postal_code' => get_user_meta($student->partner_id, 'billing_postcode', true),
                );

                $all_documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
                $documents_to_send = [];
                foreach ($all_documents_student as $document) {
                    if ($document->attachment_id) {
                        array_push($documents_to_send, $document);
                    }
                }

                foreach ($documents_to_send as $key => $doc) {
                    $id_requisito = $wpdb->get_var($wpdb->prepare("SELECT id_requisito FROM {$wpdb->prefix}documents WHERE name = %s", $doc->document_id));
                    $attachment_id = $doc->attachment_id;
                    $attachment_path = get_attached_file($attachment_id);
                    if ($attachment_path) {
                        $file_name = basename($attachment_path);
                        $file_type = mime_content_type($attachment_path);

                        $files_to_send[] = array(
                            'file' => curl_file_create($attachment_path, $file_type, $file_name),
                            'id_requisito' => $id_requisito
                        );
                    }
                }

                create_user_laravel(array_merge($fields_to_send, array('files' => $files_to_send)));

                update_status_student($student_id, 2);

                create_user_student($student_id);

                $exist = is_search_student_by_email($student_id);

                if (!$exist) {
                    create_user_moodle($student_id);
                } else {
                    $wpdb->update($table_students, ['moodle_student_id' => $exist[0]['id']], ['id' => $student_id]);

                    $is_exist_password = is_password_user_moodle($student_id);

                    if (!$is_exist_password) {
                        $password = generate_password_user();
                        $wpdb->update($table_students, ['moodle_password' => $password], ['id' => $student_id]);
                        change_password_user_moodle($student_id);
                    }
                }
            }
        }

        echo json_encode(['status' => 'success', 'message' => __('status changed', 'aes'), 'html' => $html]);

    }

    exit;
}

add_action('wp_ajax_nopriv_update_status_documents', 'update_status_documents');
add_action('wp_ajax_update_status_documents', 'update_status_documents');

function last_access_moodle()
{
    $student_id = $_POST['student_id'];
    $exist = is_search_student_by_email($student_id);
    $last_access = $exist[0]['lastaccess'];
    $date = $last_access != 0 ? date("Y-m-d H:i:s", $last_access) : null;
    wp_send_json(array('last_access' => $date));
    die();
}

add_action('wp_ajax_nopriv_last_access_moodle', 'last_access_moodle');
add_action('wp_ajax_last_access_moodle', 'last_access_moodle');

function update_payment()
{

    if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {

        $order = wc_get_order($_POST['order_id']);

        $order->set_status('completed');
        $order->save();

        echo json_encode(['status' => 'success', 'message' => __('Status changed', 'aes')]);
        die();
    }

}

add_action('wp_ajax_nopriv_update_payment', 'update_payment');
add_action('wp_ajax_update_payment', 'update_payment');

function get_data_student()
{

    if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {

        $student = get_student_detail($_POST['student_id']);
        $partner = get_userdata($student->partner_id);
        $documents = get_documents($student->id);
        $data = [];
        $data_documents = [];
        $program = get_name_program($student->program_id);
        $grade = get_name_grade($student->grade_id);

        foreach ($documents as $document) {

            $name = get_name_document($document->document_id);
            $status = get_status_document($document->status);

            if (!empty($document->attachment_id)) {
                $url = wp_get_attachment_url($document->attachment_id);
            } else {
                $url = "";
            }

            array_push($data_documents, [
                'name' => $name,
                'status' => $status,
                'url' => $url
            ]);
        }
        $type_document = get_name_type_document($student->type_document);
        $type_document_parent = get_name_type_document(get_user_meta($partner->ID, 'document_type', true));

        array_push($data, [
            'id_document' => $student->id_document,
            'type_document' => $type_document,
            'first_name' => $student->name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'birth_date' => $student->birth_date,
            'country' => $student->country,
            'city' => $student->city,
            'postal_code' => $student->postal_code,
            'program' => $program,
            'grade' => $grade,
            'gender' => $student->gender,
            'type_document_parent' => $type_document_parent,
            'id_document_parent' => get_user_meta($partner->ID, 'id_document', true),
            'first_name_parent' => $partner->first_name,
            'last_name_parent' => $partner->last_name,
            'email_parent' => $partner->user_email,
            'country_parent' => get_user_meta($partner->ID, 'billing_country', true),
            'city_parent' => get_user_meta($partner->ID, 'billing_city', true),
            'post_code_parent' => get_user_meta($partner->ID, 'billing_postcode', true),
            'phone_parent' => get_user_meta($partner->ID, 'billing_phone', true),
            'birth_date_parent' => get_user_meta($partner->ID, 'birth_date', true),
            'gender_parent' => get_user_meta($partner->ID, 'gender', true),
            'occupation_parent' => get_user_meta($partner->ID, 'occupation', true),
            'documents' => $data_documents
        ]);

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    die();
}

add_action('wp_ajax_nopriv_get_student_details', 'get_data_student');
add_action('wp_ajax_get_student_details', 'get_data_student');