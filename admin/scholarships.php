<?php

function add_admin_form_scholarships_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {

        if ($_GET['action'] == 'change_status_scholarship') {
            try {
                global $wpdb;
                $scholarship_id = $_POST['scholarship_id'];
                $fee_inscription = $_POST['fee_inscription'];
                $program = $_POST['program'];
                $fee_graduation = $_POST['fee_graduation'];
                $scholarship = $wpdb->get_row("SELECT * FROM wp_student_scholarship_application WHERE id = {$scholarship_id}");

                // GENERAMOS USUARIO PARA EL PARTNER
                $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = {$scholarship->partner_id}");
                $username = $partner->email;
                $user_email = $partner->email;
                $password = $partner->password;
                if (username_exists($username)) {
                    $user_partner_id = username_exists($username);
                    $user_partner = new WP_User($user_partner_id);
                    $user_partner->set_role('parent');
                } else {
                    $user_partner_id = wp_create_user($username, $password, $user_email);
                    $user_partner = new WP_User($user_partner_id);
                    $user_partner->set_role('parent');
                }


                update_user_meta($user_partner_id, 'first_name', $partner->name);
                update_user_meta($user_partner_id, 'billing_first_name', $partner->name);
                update_user_meta($user_partner_id, 'last_name', $partner->last_name);
                update_user_meta($user_partner_id, 'billing_last_name', $partner->last_name);
                update_user_meta($user_partner_id, 'nickname', $username);
                update_user_meta($user_partner_id, 'birth_date', $partner->birth_date);
                update_user_meta($user_partner_id, 'gender', $partner->gender);
                update_user_meta($user_partner_id, 'billing_email', $partner->email);
                update_user_meta($user_partner_id, 'billing_phone', $partner->phone);
                update_user_meta($user_partner_id, 'document_type', $partner->type_document);
                update_user_meta($user_partner_id, 'type_document', $partner->type_document);
                update_user_meta($user_partner_id, 'id_document', $partner->id_document);
                update_user_meta($user_partner_id, 'status_register', 1);
                update_user_meta($user_partner_id, 'is_scholarship', 1);
                // GENERAMOS USUARIO PARA EL PARTNER

                // CREAMOS REGISTRO EN TABLA STUDENTS
                $is_parent = false;
                $pre_students_table = $wpdb->prefix . 'pre_students';
                $students_table = $wpdb->prefix . 'students';
                $pre_student_row = $wpdb->get_row("SELECT * FROM $pre_students_table WHERE id = {$scholarship->student_id}");
                if ($user_email == $pre_student_row->email) {
                    $is_parent = true;
                }
                $wpdb->insert(
                    $students_table,
                    array(
                        'type_document' => $pre_student_row->type_document,
                        'id_document' => $pre_student_row->id_document,
                        'name' => $pre_student_row->name,
                        'middle_name' => $pre_student_row->middle_name,
                        'last_name' => $pre_student_row->last_name,
                        'middle_last_name' => $pre_student_row->middle_last_name,
                        'birth_date' => $pre_student_row->birth_date,
                        'ethnicity' => $pre_student_row->ethnicity,
                        'academic_period' => $pre_student_row->academic_period,
                        'phone' => $pre_student_row->phone,
                        'email' => $pre_student_row->email,
                        'gender' => $pre_student_row->gender,
                        'country' => $pre_student_row->country,
                        'city' => $pre_student_row->city,
                        'postal_code' => $pre_student_row->postal_code,
                        'grade_id' => $pre_student_row->grade_id,
                        'name_institute' => strtoupper($pre_student_row->name_institute),
                        'institute_id' => $pre_student_row->institute_id,
                        'program_id' => $pre_student_row->program_id,
                        'partner_id' => $user_partner_id,
                        'status_id' => 1,
                        'moodle_student_id' => $pre_student_row->moodle_student_id,
                        'moodle_password' => $pre_student_row->moodle_password,
                    )
                );
                $student_id = $wpdb->insert_id; // Get the ID of the last inserted record
                // CREAMOS REGISTRO EN TABLA STUDENTS

                // GENERAMOS USUARIO PARA EL ESTUDIANTE
                $username = $pre_student_row->email;
                $user_email = $pre_student_row->email;
                if (username_exists($username)) {
                    $user_student_id = username_exists($username);
                    $user_student = new WP_User($user_student_id);
                    $user_student->set_role('student');
                    if ($is_parent) {
                        $user_student->set_role('parent');
                    }
                } else {
                    $user_student_id = wp_create_user($username, $is_parent ? $password : generate_password_user(), $user_email);
                    $user_student = new WP_User($user_student_id);
                    $user_student->set_role('student');
                    if ($is_parent) {
                        $user_student->set_role('parent');
                    }
                }

                update_user_meta($user_student_id, 'first_name', $pre_student_row->name);
                update_user_meta($user_student_id, 'last_name', $pre_student_row->last_name);
                update_user_meta($user_student_id, 'billing_phone', $pre_student_row->phone);
                update_user_meta($user_student_id, 'billing_email', $pre_student_row->email);
                update_user_meta($user_student_id, 'birth_date', $pre_student_row->birth_date);
                update_user_meta($user_student_id, 'student_id', $student_id);
                update_user_meta($user_student_id, 'is_scholarship', 1);

                update_user_meta($user_partner_id, 'billing_country', $pre_student_row->country);
                update_user_meta($user_partner_id, 'billing_city', $pre_student_row->city);

                insert_register_documents($student_id, $pre_student_row->grade_id);
                // GENERAMOS USUARIO PARA EL ESTUDIANTE

                // CREAMOS REGISTRO EN TABLA STUDENT_PAYMENTS
                if ($fee_inscription == 1) {
                    $product_id = FEE_INSCRIPTION;
                    $product = wc_get_product($product_id);
                    $amount = $product->get_price();

                    $data = array(
                        'status_id' => 1, // Replace with the actual status ID
                        'student_id' => $student_id, // Replace with the actual student ID
                        'product_id' => $product_id, // Replace with the actual product ID
                        'amount' => $amount, // Replace with the actual amount
                        'type_payment' => 1, // Replace with the actual payment type
                        'cuote' => 1, // Replace with the actual num coute
                        'num_cuotes' => 1, // Replace with the num total of coutes
                        'date_payment' => date('Y-m-d'), // Replace with the date of first payment
                        'date_next_payment' => date('Y-m-d'), // Replace with the date of next payment
                    );

                    $wpdb->insert($wpdb->prefix . 'student_payments', $data);
                }

                if ($program == 1) {
                    $product_id = 51;
                    $product = wc_get_product($product_id);
                    $amount = $product->get_price();

                    $data = array(
                        'status_id' => 1, // Replace with the actual status ID
                        'student_id' => $student_id, // Replace with the actual student ID
                        'product_id' => $product_id, // Replace with the actual product ID
                        'amount' => $amount, // Replace with the actual amount
                        'type_payment' => 1, // Replace with the actual payment type
                        'cuote' => 1, // Replace with the actual num coute
                        'num_cuotes' => 1, // Replace with the num total of coutes
                        'date_payment' => date('Y-m-d'), // Replace with the date of first payment
                        'date_next_payment' => date('Y-m-d'), // Replace with the date of next payment
                    );

                    $wpdb->insert($wpdb->prefix . 'student_payments', $data);
                }
                // CREAMOS REGISTRO EN TABLA STUDENT_PAYMENTS

                // GUARDAMOS EL STATUS
                $wpdb->update(
                    'wp_student_scholarship_application',
                    array(
                        'status_id' => 1
                    ),
                    array(
                        'id' => $scholarship_id
                    )
                );
                // GUARDAMOS EL STATUS

                update_status_student($student_id, 1);

                $email_request_documents = WC()->mailer()->get_emails()['WC_Request_Documents_Email'];
                $email_request_documents->trigger($student_id);

                wp_new_user_notification($user_partner_id, null, 'both');
                wp_redirect(admin_url('admin.php?page=add_admin_form_scholarships_content'));
                exit;
            } catch (\Throwable $th) {
                echo $th;
                exit;
            }
        } else if ($_GET['action'] == 'pre_scholarship') {
            global $wpdb;
            $table_pre_scholarship = $wpdb->prefix . 'pre_scholarship';

            // Sanitizar y validar los datos de entrada
            $document_id = sanitize_text_field($_POST['document_id']);
            $document_type = sanitize_text_field($_POST['document_type']);
            $name = sanitize_text_field($_POST['name']);
            $scholarship_type = sanitize_text_field($_POST['scholarship_type']);

            // 1. Verificar si existen registros con el mismo document_id y document_type
            $existing_records = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_pre_scholarship 
                    WHERE document_id = %s AND document_type = %s",
                    $document_id,
                    $document_type
                )
            );

            // 2. Eliminar registros existentes que coincidan en ambos campos
            if ($existing_records > 0) {
                $wpdb->delete(
                    $table_pre_scholarship,
                    array(
                        'document_id' => $document_id,
                        'document_type' => $document_type // Campo añadido
                    ),
                    array('%s', '%s') // Formatos para ambos valores (string)
                );
            }

            // 3. Insertar el nuevo registro
            $wpdb->insert(
                $table_pre_scholarship,
                array(
                    'name' => $name,
                    'document_id' => $document_id,
                    'document_type' => $document_type,
                    'scholarship_type' => $scholarship_type
                ),
                array('%s', '%s', '%s') // Formatos de los datos (todos strings)
            );

            wp_redirect(admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=pre_scholarships'));
            exit;
        } else if ($_GET['action'] == 'assign_scholarship') {
            global $wpdb;
            $table_scholarship_assigned_student = $wpdb->prefix . 'scholarship_assigned_student';

            // Sanitizar y validar los datos de entrada
            $student_id = intval($_POST['student_id']);
            $scholarship_id = intval($_POST['scholarship_type']);

            // 1. Verificar si existen registros con el mismo student_id y scholarhsip_type
            $existing_records = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_scholarship_assigned_student 
                    WHERE student_id = %d",
                    $student_id
                )
            );

            // 2. Eliminar registros existentes que coincidan en ambos campos
            if ($existing_records > 0) {
                $wpdb->delete(
                    $table_scholarship_assigned_student,
                    array(
                        'student_id' => $student_id // Campo añadido
                    ),
                    array('%d') // Formatos para ambos valores (string)
                );
            }

            // 3. Insertar el nuevo registro
            $wpdb->insert(
                $table_scholarship_assigned_student,
                array(
                    'student_id' => $student_id,
                    'scholarship_id' => $scholarship_id
                ),
                array('%d', '%d') // Formatos de los datos (todos strings)
            );

            wp_redirect(admin_url('admin.php?page=add_admin_form_scholarships_content&section_tab=pre_scholarships'));
            exit;
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {


        if ($_GET['section_tab'] == 'all_scholarships') {
            global $wpdb;
            $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
            $scholarships_availables = $wpdb->get_results("SELECT * FROM {$table_scholarships_availables} WHERE is_active = 1");

            $list_scholarships = new TT_scholarship_all_List_Table;
            $list_scholarships->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-scholarships.php');
        } else if ($_GET['section_tab'] == 'pre_scholarships') {
            global $wpdb;
            $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
            $scholarships_availables = $wpdb->get_results("SELECT * FROM {$table_scholarships_availables} WHERE is_active = 1");

            $list_scholarships = new TT_pre_scholarship_all_List_Table;
            $list_scholarships->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-scholarships.php');
        } else if ($_GET['section_tab'] == 'scholarship_detail') {

            global $current_user;
            global $wpdb;
            $roles = $current_user->roles;
            $scholarship_id = $_GET['scholarship_id'];
            $scholarship = $wpdb->get_row("SELECT * FROM wp_student_scholarship_application WHERE id = " . $scholarship_id);
            $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = " . $scholarship->student_id);
            $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = " . $scholarship->partner_id);
            $institute = $wpdb->get_row("SELECT * FROM wp_institutes WHERE id = " . $student->institute_id);
            $grade = $wpdb->get_row("SELECT * FROM wp_grades WHERE id = " . $student->grade_id);

            include(plugin_dir_path(__FILE__) . 'templates/scholarship-detail.php');
        }

    } else {
        global $wpdb;
        $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
        $scholarships_availables = $wpdb->get_results("SELECT * FROM {$table_scholarships_availables} WHERE is_active = 1");

        $list_scholarships = new TT_scholarship_pending_List_Table;
        $list_scholarships->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-scholarships.php');
    }
}

function add_admin_form_available_scholarships_content()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'save_scholarship') {
            global $wpdb;
            $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';
            $scholarship_id = $_POST['scholarship_id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $fee_registration = isset($_POST['fee_registration']) ? ($_POST['fee_registration'] == 'on' ? 1 : 0) : 0;
            $percent_registration = $_POST['percent_registration'];
            $program = isset($_POST['program']) ? ($_POST['program'] == 'on' ? 1 : 0) : 0;
            $percent_program = $_POST['percent_program'];
            $fee_graduation = isset($_POST['fee_graduation']) ? ($_POST['fee_graduation'] == 'on' ? 1 : 0) : 0;
            $percent_graduation = $_POST['percent_graduation'];
            $is_active = isset($_POST['is_active']) ? ($_POST['is_active'] == 'on' ? 1 : 0) : 0;
            $coupons = [];

            if ($fee_registration) {
                $available_coupons = obtener_cupones_porcentuales_producto([FEE_INSCRIPTION], $percent_registration);
                foreach ($available_coupons as $key => $coupon) {
                    array_push($coupons, $coupon);
                }

                if (count($available_coupons) == 0) {
                    $coupon_code = 'Discount registration fee ' . $percent_registration . '%';
                    $coupon = new WC_Coupon();
                    $coupon->set_code($coupon_code);
                    $coupon->set_discount_type('percent');
                    $coupon->set_amount($percent_registration);
                    $coupon->set_product_ids(array(FEE_INSCRIPTION));
                    $coupon->save();
                    array_push($coupons, $coupon_code);
                }
            }

            if ($program) {
                $product_ids = get_all_woocommerce_product_ids(true);
                $product_ids = array_diff($product_ids, [FEE_INSCRIPTION]);
                $available_coupons = obtener_cupones_porcentuales_producto($product_ids, $percent_program);
                foreach ($available_coupons as $key => $coupon) {
                    array_push($coupons, $coupon);
                }

                if (count($available_coupons) == 0) {
                    $coupon_code = 'Discount program ' . $percent_program . '%';
                    $coupon = new WC_Coupon();
                    $coupon->set_code($coupon_code);
                    $coupon->set_discount_type('percent');
                    $coupon->set_amount($percent_program);
                    $coupon->set_product_ids($product_ids);
                    $coupon->save();
                    array_push($coupons, $coupon_code);
                }
            }

            if (isset($scholarship_id) && !empty($scholarship_id)) {
                $wpdb->update($table_scholarships_availables, [
                    'name' => $name,
                    'description' => $description,
                    'coupons' => json_encode($coupons),
                    'fee_registration' => $fee_registration,
                    'percent_registration' => $percent_registration,
                    'program' => $program,
                    'percent_program' => $percent_program,
                    'fee_graduation' => $fee_graduation,
                    'percent_graduation' => $percent_graduation,
                    'is_active' => $is_active,
                ], ['id' => $scholarship_id]);

            } else {
                $wpdb->insert($table_scholarships_availables, [
                    'name' => $name,
                    'description' => $description,
                    'coupons' => json_encode($coupons),
                    'fee_registration' => $fee_registration,
                    'percent_registration' => $percent_registration,
                    'program' => $program,
                    'percent_program' => $percent_program,
                    'fee_graduation' => $fee_graduation,
                    'percent_graduation' => $percent_graduation,
                    'is_active' => $is_active,
                ]);
            }

            if (isset($scholarship_id) && !empty($scholarship_id)) {
                setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_available_scholarships_content&section_tab=available_scholarship_detail&scholarship_id=' . $scholarship_id));
            } else {
                wp_redirect(admin_url('admin.php?page=add_admin_form_available_scholarships_content'));
            }
        }
    }

    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'available_scholarship_detail') {
            $scholarship_id = $_GET['scholarship_id'];
            $scholarship = get_scholarship_details($scholarship_id);
            include(plugin_dir_path(__FILE__) . 'templates/available-scholarship-detail.php');
        }

    } else {
        $list_availables_scholarships = new TT_availables_scholarships_List_Table;
        $list_availables_scholarships->prepare_items();
        include(plugin_dir_path(__FILE__) . 'templates/list-available-scholarships.php');
    }
}


class TT_scholarship_pending_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'scholarship_pending',
            'plural' => 'scholarship_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'scholarship_id':
                return '#' . $item[$column_name];
            case 'student_name':
                return ucwords($item[$column_name]);
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'date':
                return ucwords($item[$column_name]);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_scholarships_content&section_tab=scholarship_detail&scholarship_id=' . $item['scholarship_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
            default:
                return ucwords($item[$column_name]);
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
            'scholarship_id' => __('Scholarship ID', 'edusystem'),
            'student_name' => __('Student', 'edusystem'),
            'student_email' => __('Student email', 'edusystem'),
            'partner_name' => __('Parent', 'edusystem'),
            'partner_email' => __('Parent email', 'edusystem'),
            'date' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_scholarship_pendings()
    {
        global $wpdb;
        $scholarships_array = [];

        $scholarships = $wpdb->get_results("SELECT * FROM wp_student_scholarship_application WHERE status_id = 0 ORDER BY id DESC");

        if ($scholarships) {
            foreach ($scholarships as $scholarship) {
                $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = " . $scholarship->student_id);
                $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = " . $scholarship->partner_id);

                array_push($scholarships_array, [
                    'scholarship_id' => $scholarship->id,
                    'date' => $scholarship->created_at,
                    'student_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
                    'student_email' => $student->email,
                    'partner_name' => $partner->name . ' ' . $partner->middle_name . ' ' . $partner->last_name . ' ' . $partner->middle_last_name,
                    'partner_email' => $partner->email
                ]);
            }
        }

        return $scholarships_array;
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

        $data_scholarships = $this->get_scholarship_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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

class TT_scholarship_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'scholarship_pending',
            'plural' => 'scholarship_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'scholarship_id':
                return '#' . $item[$column_name];
            case 'student_name':
                return ucwords($item[$column_name]);
            case 'partner_name':
                return ucwords($item[$column_name]);
            case 'date':
                return ucwords($item[$column_name]);
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_scholarships_content&section_tab=scholarship_detail&scholarship_id=' . $item['scholarship_id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
            default:
                return ucwords($item[$column_name]);
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
            'scholarship_id' => __('Scholarship ID', 'edusystem'),
            'student_name' => __('Student', 'edusystem'),
            'student_email' => __('Student email', 'edusystem'),
            'partner_name' => __('Parent', 'edusystem'),
            'partner_email' => __('Parent email', 'edusystem'),
            'date' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_scholarship_pendings()
    {
        global $wpdb;
        $scholarships_array = [];

        $scholarships = $wpdb->get_results("SELECT * FROM wp_student_scholarship_application WHERE status_id = 1 ORDER BY id DESC");

        if ($scholarships) {
            foreach ($scholarships as $scholarship) {
                $student = $wpdb->get_row("SELECT * FROM wp_pre_students WHERE id = " . $scholarship->student_id);
                $partner = $wpdb->get_row("SELECT * FROM wp_pre_users WHERE id = " . $scholarship->partner_id);

                array_push($scholarships_array, [
                    'scholarship_id' => $scholarship->id,
                    'date' => $scholarship->created_at,
                    'student_name' => $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name,
                    'student_email' => $student->email,
                    'partner_name' => isset($partner) ? $partner->name . ' ' . $partner->middle_name . ' ' . $partner->last_name . ' ' . $partner->middle_last_name : 'N/A',
                    'partner_email' => isset($partner) ? $partner->email : 'N/A'
                ]);
            }
        }

        return $scholarships_array;
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

        $data_scholarships = $this->get_scholarship_pendings();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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

class TT_pre_scholarship_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'scholarship_pending',
            'plural' => 'scholarship_pendings',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                return "<a target='_blank' href='" . esc_url(home_url('/student-scholarship-application/?id=' . $item['document_id'] . '&type=' . $item['document_type'])) . "' class='button button-primary'>" . __('Share link', 'edusystem') . "</a>";
            default:
                return ucwords($item[$column_name]);
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
            'scholarship' => __('Scholarship', 'edusystem'),
            'document_type' => __('Type document', 'edusystem'),
            'document_id' => __('ID Document', 'edusystem'),
            'student' => __('Student', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_pre_scholarships()
    {
        global $wpdb;
        $table_pre_scholarship = $wpdb->prefix . 'pre_scholarship';

        $scholarships = $wpdb->get_results("SELECT * FROM {$table_pre_scholarship} ORDER BY id DESC", "ARRAY_A");
        foreach ($scholarships as $key => $scholarship) {
            $matter = get_scholarship_details(scholarship_id: $scholarship['scholarship_type']);
            $scholarships[$key]['scholarship'] = $matter->name;
            $scholarships[$key]['student'] = $scholarship['name'];
        }
        return $scholarships;
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

        $data_scholarships = $this->get_pre_scholarships();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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


class TT_availables_scholarships_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(array(
            'singular' => 'available_scholarship',
            'plural' => 'available_scholarships',
            'ajax' => true
        ));

    }

    function column_default($item, $column_name)
    {

        global $current_user;

        switch ($column_name) {
            case 'view_details':
                return "<a href='" . admin_url('/admin.php?page=add_admin_form_available_scholarships_content&section_tab=available_scholarship_detail&scholarship_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
            default:
                return ucwords($item[$column_name]);
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
            'id' => __('Scholarship ID', 'edusystem'),
            'name_scholarship' => __('Name', 'edusystem'),
            'description' => __('Description', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_availables_scholarships()
    {
        global $wpdb;
        $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';

        $scholarships = $wpdb->get_results("SELECT * FROM {$table_scholarships_availables} ORDER BY id DESC", "ARRAY_A");
        foreach ($scholarships as $key => $scholarship) {
            $scholarships[$key]['name_scholarship'] = $scholarship['name'];
        }
        return $scholarships;
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

        $data_scholarships = $this->get_availables_scholarships();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $data_scholarships;

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

function get_scholarship_details($scholarship_id)
{
    global $wpdb;
    $table_scholarships_availables = $wpdb->prefix . 'scholarships_availables';

    $scholarship = $wpdb->get_row("SELECT * FROM {$table_scholarships_availables} WHERE id={$scholarship_id}");
    return $scholarship;
}

function obtener_cupones_porcentuales_producto($product_ids, $percentage)
{
    $cupones = array();

    // Convertir a array y sanitizar IDs
    $product_ids = array_map('intval', (array) $product_ids);
    $product_ids = array_filter(array_unique($product_ids));

    if (empty($product_ids)) {
        return array();
    }

    // Construir regex para múltiples IDs (ej: 123|456)
    $regex_parts = array();
    foreach ($product_ids as $id) {
        $regex_parts[] = sprintf(
            '(^%1$d$|^%1$d,|,%1$d,|,%1$d$)',
            $id
        );
    }
    $product_ids_regex = implode('|', $regex_parts);

    $args = array(
        'post_type' => 'shop_coupon',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'discount_type',
                'value' => 'percent',
            ),
            array(
                'key' => 'product_ids',
                'value' => $product_ids_regex,
                'compare' => 'REGEXP',
            ),
            array(
                'key' => 'coupon_amount',
                'value' => (float) $percentage,
                'type' => 'DECIMAL(10,2)', // Para comparación exacta
                'compare' => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            array_push($cupones, $post->post_title);
        }
    }

    wp_reset_postdata();

    return $cupones;
}

function get_all_woocommerce_product_ids($include_variations = false, $post_statuses = ['publish'])
{
    $args = [
        'post_type' => $include_variations ? ['product', 'product_variation'] : 'product',
        'post_status' => $post_statuses,
        'posts_per_page' => -1,
        'fields' => 'ids', // Optimizado para solo obtener IDs
    ];

    $query = new WP_Query($args);

    return $query->posts ?: [];
}