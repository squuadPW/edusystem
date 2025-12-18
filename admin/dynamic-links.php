<?php

function add_admin_form_dynamic_link_content()
{
    global $wpdb;
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        $payment_plans = [];
        $programs = get_student_program();
        $current_user = wp_get_current_user();
        $roles = (array) $current_user->roles;
        $is_manager = in_array('manager', $roles);
        $is_admin = in_array('administrator', $roles);
        if (!$is_admin && !$is_manager) {
            $manager_user_id = get_user_meta($current_user->ID, 'manager_user_id', true);
            if (!empty($manager_user_id)) {
                $all_managers = get_users_managers();
                $managers = array_filter($all_managers, function ($m) use ($manager_user_id) {
                    return $m->ID == $manager_user_id;
                });
            } else {
                $managers = [];
            }
        } else {
            $managers = get_users_managers();
        }
        $dynamic_links_email_log = array();

        if ($_GET['section_tab'] == 'dynamic_link_details') {
            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $dynamic_link = get_dynamic_link_detail($dynamic_link_id);
            if ($dynamic_link) {
                $table_programs = $wpdb->prefix . 'programs';
                $associateds = get_associated_plans_by_program_id($dynamic_link->program_identificator);
                foreach ($associateds as $key => $plan) {
                    $plan = $wpdb->get_row("SELECT * FROM {$table_programs} WHERE identificator='{$plan}'");
                    if ($plan) {
                        $plan->subprograms = json_decode($plan->subprograms_json);
                        $payment_plans[] = $plan;
                    }
                }
            }
            $dynamic_links_email_log = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_dynamic_links_email_log} WHERE dynamic_link_id=%d ORDER BY id DESC", $dynamic_link_id));
            include(plugin_dir_path(__FILE__) . 'templates/dynamic-links-detail.php');
        }

        if ($_GET['section_tab'] == 'add_dynamic_link') {
            include(plugin_dir_path(__FILE__) . 'templates/dynamic-links-detail.php');
        }
    } else {

        if ($_GET['action'] == 'save_dynamic_link_details') {
            global $wpdb;

            $table = $wpdb->prefix . 'dynamic_links';
            $dynamic_link_id = $_POST['dynamic_link_id'] ?? null; // Usar ?? para evitar notices
            $type_document = sanitize_text_field($_POST['type_document']);
            $id_document = sanitize_text_field($_POST['id_document']);
            $name = sanitize_text_field($_POST['name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $email = sanitize_email($_POST['email']); // Mejor usar sanitize_email
            $program_identificator = sanitize_text_field($_POST['program_identificator']);
            $subprogram_identificator = intval($_POST['subprogram_id']);
            $payment_plan_identificator = sanitize_text_field($_POST['payment_plan_identificator']);
            $save_and_send_email = sanitize_text_field($_POST['save_and_send_email']);
            $manager_id = intval($_POST['manager_id']); // Sanitizar como entero
            $current_user = wp_get_current_user();
            $created_by = $current_user->ID;
            $transfer_cr = $_POST['transfer_cr'] ?? 0;
            $fee_payment_completed = $_POST['fee_payment_completed'] ?? 0;

            // << CAMBIO 1: Declaramos la variable $link aquí, pero no le asignamos valor aún.
            $link = '';

            setcookie('message', __('Changes saved successfully.', 'edusystem'), time() + 10, '/');

            // Si existe un ID, estamos ACTUALIZANDO
            if (isset($dynamic_link_id) && !empty($dynamic_link_id)) {

                // << CAMBIO 2: Recuperamos el link existente de la BD.
                // Esto es importante por si se necesita enviar el email con el link original.
                $link = $wpdb->get_var($wpdb->prepare("SELECT link FROM $table WHERE id = %d", $dynamic_link_id));

                // << CAMBIO 3: Eliminamos 'link' => $link del array, para no sobreescribirlo.
                $wpdb->update($table, [
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'name' => $name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'program_identificator' => $program_identificator,
                    'subprogram_identificator' => $subprogram_identificator,
                    'payment_plan_identificator' => $payment_plan_identificator,
                    'transfer_cr' => $transfer_cr,
                    'fee_payment_completed' => $fee_payment_completed,
                    'manager_id' => $manager_id,
                    'created_by' => $created_by,
                ], ['id' => $dynamic_link_id]);

                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=') . $dynamic_link_id);

                // Si no existe ID, estamos CREANDO un nuevo registro
            } else {

                // << CAMBIO 4: Generamos el link SÓLO al crear un nuevo registro.
                $link = substr(bin2hex(random_bytes(6)), 0, 10);

                $wpdb->insert($table, [
                    'link' => $link, // Aquí sí lo insertamos
                    'type_document' => $type_document,
                    'id_document' => $id_document,
                    'name' => $name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'program_identificator' => $program_identificator,
                    'subprogram_identificator' => $subprogram_identificator,
                    'payment_plan_identificator' => $payment_plan_identificator,
                    'transfer_cr' => $transfer_cr,
                    'fee_payment_completed' => $fee_payment_completed,
                    'manager_id' => $manager_id,
                    'created_by' => $created_by,
                ]);

                $dynamic_link_id = $wpdb->insert_id;
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=') . $dynamic_link_id);
            }

            // El resto del código para enviar el email funciona igual,
            // ya que la variable $link tendrá el valor correcto (el antiguo si se actualiza, o el nuevo si se crea).
            if ($save_and_send_email == '1') {
                $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
                $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
                $html .= '<p>' . __('We are pleased to inform you that a payment link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
                $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
                $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
                $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
                $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
                $sender_email->trigger($email, 'Link', $html);

                $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
                $wpdb->insert($table_dynamic_links_email_log, [
                    'dynamic_link_id' => $dynamic_link_id,
                    'email' => $email,
                    'created_by' => $created_by,
                ]);
            }

            exit;
        } else if ($_GET['action'] == 'upload_document') {
            global $wpdb;

            if (isset($_FILES['document_upload_file']) && !empty($_FILES['document_upload_file'])) {
                $file_temp = $_FILES['document_upload_file'];
                if (($handle = fopen($file_temp['tmp_name'], 'r')) !== false) {
                    // Detectar delimitador automáticamente (coma o punto y coma)
                    $first_line = fgets($handle);
                    rewind($handle);
                    $delimiter = ',';
                    if (substr_count($first_line, ';') > substr_count($first_line, ',')) {
                        $delimiter = ';';
                    }
                    $header = fgetcsv($handle, 0, $delimiter);
                    // Esperados: type_document, id_document, name, last_name, email, program_identificator, payment_plan_identificator, transfer_cr, send_email
                    $expected = ['type_document', 'id_document', 'name', 'last_name', 'email', 'program_identificator', 'payment_plan_identificator', 'transfer_cr', 'send_email'];
                    $header_map = array_flip($header);

                    $missing = array_diff($expected, $header);
                    if (!empty($missing)) {
                        setcookie('message', __('CSV missing columns: ', 'edusystem') . implode(', ', $missing), time() + 10, '/');
                        wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                        exit;
                    }
                    $current_user = wp_get_current_user();
                    $created_by = $current_user->ID;
                    $manager_user_id = get_user_meta($created_by, 'manager_user_id', true);
                    $manager_id = !empty($manager_user_id) ? $manager_user_id : 0;
                    $table = $wpdb->prefix . 'dynamic_links';
                    $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
                    $count_inserted = 0;
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        // Limpiar y mapear valores
                        foreach ($header_map as $col => $idx) {
                            $row[$idx] = isset($row[$idx]) ? trim($row[$idx]) : '';
                        }
                        $type_document = sanitize_text_field($row[$header_map['type_document']]);
                        $id_document = sanitize_text_field($row[$header_map['id_document']]);
                        $name = sanitize_text_field($row[$header_map['name']]);
                        $last_name = sanitize_text_field($row[$header_map['last_name']]);
                        $email = sanitize_text_field($row[$header_map['email']]);
                        $program_identificator = sanitize_text_field($row[$header_map['program_identificator']]);
                        $payment_plan_identificator = sanitize_text_field($row[$header_map['payment_plan_identificator']]);
                        $transfer_cr = isset($row[$header_map['transfer_cr']]) ? intval($row[$header_map['transfer_cr']]) : 0;
                        $send_email = isset($row[$header_map['send_email']]) ? $row[$header_map['send_email']] : '0';
                        $link = substr(bin2hex(random_bytes(6)), 0, 10);
                        $wpdb->insert($table, [
                            'link' => $link,
                            'type_document' => $type_document,
                            'id_document' => $id_document,
                            'name' => $name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'program_identificator' => $program_identificator,
                            'payment_plan_identificator' => $payment_plan_identificator,
                            'transfer_cr' => $transfer_cr,
                            'manager_id' => $manager_id,
                            'created_by' => $created_by,
                        ]);
                        $dynamic_link_id = $wpdb->insert_id;
                        if ($send_email == '1') {
                            $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
                            $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
                            $html .= '<p>' . __('We are pleased to inform you that a payment link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
                            $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
                            $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
                            $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
                            $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
                            $sender_email->trigger($email, 'Link', $html);
                            $wpdb->insert($table_dynamic_links_email_log, [
                                'dynamic_link_id' => $dynamic_link_id,
                                'email' => $email,
                                'created_by' => $created_by,
                            ]);
                        }
                        $count_inserted++;
                    }
                    fclose($handle);
                    setcookie('message', sprintf(__('CSV processed. %d records inserted.', 'edusystem'), $count_inserted), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                    exit;
                } else {
                    setcookie('message', __('Could not open CSV file.', 'edusystem'), time() + 10, '/');
                    wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                    exit;
                }
            } else {
                setcookie('message', __('No file uploaded.', 'edusystem'), time() + 10, '/');
                wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
                exit;
            }
        } else if ($_GET['action'] == 'delete_dynamic_link') {
            global $wpdb;
            $table = $wpdb->prefix . 'dynamic_links';
            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $wpdb->delete($table, ['id' => $dynamic_link_id]);
            $wpdb->delete($table_dynamic_links_email_log, ['dynamic_link_id' => $dynamic_link_id]);

            setcookie('message', __('Payment link deleted.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
            exit;
        } else if ($_GET['action'] == 'send_email') {
            global $wpdb;
            $table = $wpdb->prefix . 'dynamic_links';
            $dynamic_link_id = $_GET['dynamic_link_id'];
            $dynamic_link_data = get_dynamic_link_detail($dynamic_link_id);
            $link = $dynamic_link_data->link;
            $name = $dynamic_link_data->name;
            $last_name = $dynamic_link_data->last_name;
            $email = $dynamic_link_data->email;
            $current_user = wp_get_current_user();
            $created_by = $current_user->ID;

            $sender_email = WC()->mailer()->get_emails()['WC_Email_Sender_Email'];
            $html = '<p>' . __('Dear', 'edusystem') . ' ' . $name . ' ' . $last_name . ',</p>';
            $html .= '<p>' . __('We are pleased to inform you that a payment link has been created for you to complete your enrollment process. Please click on the link below to access your personalized portal.', 'edusystem') . '</p>';
            $html .= '<p><a href="' . site_url('/registration-link?token=' . $link) . '">' . site_url('/registration-link?token=' . $link) . '</a></p>';
            $html .= '<p>' . __('If you have any questions or need assistance, please do not hesitate to contact us. We are here to help you with whatever you need.', 'edusystem') . '</p>';
            $html .= '<p>' . __('Best regards,', 'edusystem') . '</p>';
            $html .= '<p>' . sprintf(__('%s Team', 'edusystem'), get_bloginfo('name')) . '</p>';
            $sender_email->trigger($email, 'Link', $html);

            $table_dynamic_links_email_log = $wpdb->prefix . 'dynamic_links_email_log';
            $wpdb->insert($table_dynamic_links_email_log, [
                'dynamic_link_id' => $dynamic_link_id,
                'email' => $email,
                'created_by' => $created_by
            ]);

            setcookie('message', __('Payment link send to email.', 'edusystem'), time() + 10, '/');
            wp_redirect(admin_url('admin.php?page=add_admin_form_dynamic_link_content'));
            exit;
        } else {
            $list_dynamic_links = new TT_Dynamic_all_List_Table;
            $list_dynamic_links->prepare_items();
            include(plugin_dir_path(__FILE__) . 'templates/list-dynamic-links.php');
        }
    }
}

class TT_Dynamic_all_List_Table extends WP_List_Table
{

    function __construct()
    {
        global $status, $page, $categories;

        parent::__construct(
            array(
                'singular' => 'dynamic_link',
                'plural' => 'dynamic_links',
                'ajax' => true
            )
        );
    }

    function column_default($item, $column_name)
    {
        global $current_user;
        switch ($column_name) {
            case 'img_desktop':
                return $item['img_desktop'] ? '<img src="' . $item['img_desktop'] . '" width="100px" height="50px" />' : 'N/A';
            case 'img_mobile':
                return $item['img_mobile'] ? '<img src="' . $item['img_mobile'] . '" width="100px" height="50px" />' : 'N/A';
            case 'view_details':
                $buttons = "<a href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&section_tab=dynamic_link_details&dynamic_link_id=' . $item['id']) . "' class='button button-primary'>" . __('View Details', 'edusystem') . "</a>";
                $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&action=delete_dynamic_link&dynamic_link_id=' . $item['id']) . "' class='button button-danger'>" . __('Delete', 'edusystem') . "</a>";
                if ($item['email']) {
                    $buttons .= "<a onclick='return confirm(\"Are you sure?\");' style='margin-left: 4px' href='" . admin_url('/admin.php?page=add_admin_form_dynamic_link_content&action=send_email&dynamic_link_id=' . $item['id']) . "' class='button button-success'>" . __('Send Email', 'edusystem') . "</a>";
                }
                // Copiar link al portapapeles usando JS (usando el campo 'link' como token)
                $dynamic_link_token = isset($item['link']) ? $item['link'] : '';
                $dynamic_link_url = site_url('/registration-link?token=' . $dynamic_link_token);
                $buttons .= "<a href='javascript:void(0);' onclick=\"copyToClipboard('{$dynamic_link_url}', this)\" style='margin-left: 4px' class='button button-secondary'>" . __('Copy Link', 'edusystem') . "</a>";
                return $buttons;
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
            'program' => __('Program', 'edusystem'),
            'student' => __('Student or description', 'edusystem'),
            'payment_plan' => __('Scholarship', 'edusystem'),
            'created_by' => __('Created by', 'edusystem'),
            'created_at' => __('Created at', 'edusystem'),
            'view_details' => __('Actions', 'edusystem'),
        );

        return $columns;
    }

    function get_dynamic_links()
    {
        global $wpdb;

        // --- CONFIGURATION ---
        $per_page = 20; // number of items per page
        $table_links = $wpdb->prefix . 'dynamic_links';
        $table_programs = $wpdb->prefix . 'programs';
        $table_student_programs = $wpdb->prefix . 'student_program';

        // --- PAGINATION ---
        $pagenum = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
        $offset = (($pagenum - 1) * $per_page);

        $where = [];
        $args = [];
        $search_term = isset($_POST['s']) ? trim($_POST['s']) : '';

        // --- SEARCH LOGIC (Corrected) ---
        if (!empty($search_term)) {
            $search = $wpdb->esc_like($search_term);
            $like = "%{$search}%";
            $found_identificators = [];
            $search_args = [$like, $like, $like];

            // 1. Search in wp_programs (for payment_plan_identificator)
            $sql_program_search = $wpdb->prepare(
                "SELECT `identificator` FROM {$table_programs} WHERE `identificator` LIKE %s OR `name` LIKE %s OR `description` LIKE %s",
                $search_args
            );
            $program_identificators = $wpdb->get_col($sql_program_search);

            if (!empty($program_identificators)) {
                $found_identificators['payment_plan_identificator'] = $program_identificators;
            }

            // 2. Search in wp_student_programs (for program_identificator)
            $sql_student_program_search = $wpdb->prepare(
                "SELECT `identificator` FROM {$table_student_programs} WHERE `identificator` LIKE %s OR `name` LIKE %s OR `description` LIKE %s",
                $search_args
            );
            $student_program_identificators = $wpdb->get_col($sql_student_program_search);

            if (!empty($student_program_identificators)) {
                $found_identificators['program_identificator'] = $student_program_identificators;
            }

            // 3. Direct Search on dynamic_links (Always included in search logic)
            // We include name, last_name, and email to make the search more useful
            $where_link_search = "(`name` LIKE %s OR `last_name` LIKE %s OR `email` LIKE %s)";
            $search_link_args = [$like, $like, $like]; // 4 arguments

            // 4. Construct the final search WHERE clause
            $identificator_conditions = [];
            $identificator_values = [];

            if (isset($found_identificators['program_identificator']) && !empty($found_identificators['program_identificator'])) {
                $placeholders = implode(', ', array_fill(0, count($found_identificators['program_identificator']), '%s'));
                $identificator_conditions[] = "`program_identificator` IN ({$placeholders})";
                $identificator_values = array_merge($identificator_values, $found_identificators['program_identificator']);
            }

            if (isset($found_identificators['payment_plan_identificator']) && !empty($found_identificators['payment_plan_identificator'])) {
                $placeholders = implode(', ', array_fill(0, count($found_identificators['payment_plan_identificator']), '%s'));
                $identificator_conditions[] = "`payment_plan_identificator` IN ({$placeholders})";
                $identificator_values = array_merge($identificator_values, $found_identificators['payment_plan_identificator']);
            }

            // Combine all search conditions (Direct OR External Identificators)
            $full_search_conditions = array_merge([$where_link_search], $identificator_conditions);
            $full_search_args = array_merge($search_link_args, $identificator_values);

            // Add the combined search condition to the main WHERE array
            $where[] = '(' . implode(' OR ', $full_search_conditions) . ')';
            $args = array_merge($args, $full_search_args);
        }


        // --- USER ROLE FILTERING ---
        $current_user = wp_get_current_user();
        $roles = (array) $current_user->roles;
        $is_manager = in_array('manager', $roles);
        $is_admin = in_array('administrator', $roles);

        if (!$is_admin) {
            if ($is_manager) {
                // Manager: sees links they created OR links assigned to them
                $where[] = "(created_by = %d OR manager_id = %d)";
                $args[] = $current_user->ID;
                $args[] = $current_user->ID;
            } else {
                // Other roles: sees links they created OR links assigned to their manager
                $manager_user_id = get_user_meta($current_user->ID, 'manager_user_id', true);
                $user_filter = "created_by = %d";
                $user_filter_args = [$current_user->ID];

                if (!empty($manager_user_id)) {
                    $user_filter = "(created_by = %d OR manager_id = %d)";
                    $user_filter_args = [$current_user->ID, $manager_user_id];
                }
                $where[] = $user_filter;
                $args = array_merge($args, $user_filter_args);
            }
        }

        // --- FINAL QUERY CONSTRUCTION ---
        $where_sql = '';
        if (!empty($where)) {
            $where_sql = 'WHERE (' . implode(') AND (', $where) . ')'; // Grouping ALL WHERE conditions
        }

        // Add pagination arguments to the list for preparation
        $args[] = $per_page;
        $args[] = $offset;

        // Use a single, safe prepare statement for the main query
        $sql = $wpdb->prepare(
            "SELECT SQL_CALC_FOUND_ROWS * FROM {$table_links} {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d",
            $args
        );

        $dynamic_links = $wpdb->get_results($sql, ARRAY_A);
        $total_count = $wpdb->get_var("SELECT FOUND_ROWS()");

        // --- DATA PROCESSING ---
        $dynamic_links_array = [];
        if ($dynamic_links) {
            foreach ($dynamic_links as $dynamic_links_val) {
                $payment_plan = get_program_details_by_identificator($dynamic_links_val['payment_plan_identificator']);
                $program = get_student_program_details_by_identificator($dynamic_links_val['program_identificator']);
                $created_by_user = get_user_by('id', $dynamic_links_val['created_by']);

                // Check if objects are returned before trying to access properties
                $program_name = $program && isset($program->name) ? $program->name : 'N/A';
                $student_name_desc = $dynamic_links_val['name'] ? $dynamic_links_val['name'] . ' ' . $dynamic_links_val['last_name'] : ($program && isset($program->description) ? $program->description : 'N/A');
                $payment_plan_detail = $payment_plan && isset($payment_plan->name) ? $payment_plan->name . ' (' . $payment_plan->description . ')' : 'N/A';
                $created_by_name = $created_by_user ? $created_by_user->first_name . ' ' . $created_by_user->last_name : 'Unknown User';

                $dynamic_links_array[] = [
                    'id' => $dynamic_links_val['id'],
                    'program' => $program_name,
                    'student' => $student_name_desc,
                    'payment_plan' => $payment_plan_detail,
                    'link' => $dynamic_links_val['link'],
                    'created_by' => $created_by_name,
                    'created_at' => $dynamic_links_val['created_at'],
                    'email' => $dynamic_links_val['email']
                ];
            }
        }

        return ['data' => $dynamic_links_array, 'total_count' => $total_count];
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

        $data_dynamic_link = $this->get_dynamic_links();

        $per_page = 10;


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $data = $data_dynamic_link['data'];
        $total_count = (int) $data_dynamic_link['total_count'];

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

function get_dynamic_link_detail($dynamic_link_id)
{
    global $wpdb;

    $table = $wpdb->prefix . 'dynamic_links';
    $data = $wpdb->get_row("SELECT * FROM {$table} WHERE id={$dynamic_link_id}");
    return $data;
}

function get_dynamic_link_detail_by_link($dynamic_link)
{
    global $wpdb;

    $table = $wpdb->prefix . 'dynamic_links';
    $data = $wpdb->get_row("SELECT * FROM {$table} WHERE link='{$dynamic_link}'");
    return $data;
}

/**
 * Obtiene los métodos de pago no configurados para un plan específico y las cuentas asociadas.
 *
 * @param string $payment_plan_identificator El identificador del plan de pago.
 * @return array Un array con los métodos ocultos y las cuentas conectadas.
 */
function get_hidden_payment_methods_by_plan(string $payment_plan_identificator, $fee_payment_completed = false): array
{
    global $wpdb;

    // 1. Verificación inicial: Si WooCommerce no está activo, no hay nada que hacer.
    if (!function_exists('WC')) {
        // Devuelve una estructura vacía para mantener la consistencia del tipo de retorno.
        return [
            'hidden_methods' => [],
            'hidden_methods_csv' => '',
            'connected_account' => '',
            'flywire_portal_code' => '',
            'zelle_account' => '',
            'bank_transfer_account' => '',
        ];
    }

    // 2. Obtener solo las columnas necesarias de la base de datos.
    $table_name = $wpdb->prefix . 'payment_methods_by_plan';

    // El valor booleano se convierte a entero (0 o 1) para la base de datos.
    $fee_completed_int = (int) $fee_payment_completed;

    $plan_methods_raw = $wpdb->get_results(
        $wpdb->prepare(
            // CONSULTA MODIFICADA: Agrega la condición para fee_payment_complete
            "SELECT payment_method_identificator, account_identificator FROM {$table_name} WHERE payment_plan_identificator = %s AND fee_payment_complete = %d",
            $payment_plan_identificator,
            $fee_completed_int // Nuevo parámetro agregado
        )
    );

    // 3. Procesar los resultados de la DB de manera más eficiente.
    // Usamos array_column para crear un array asociativo [method_id => row_object].
    $plan_methods = [];
    if (!empty($plan_methods_raw)) {
        $plan_methods = array_column($plan_methods_raw, null, 'payment_method_identificator');
    }

    // 4. Obtener todos los gateways de WooCommerce.
    // Usamos un bloque try-catch por si ocurre un error inesperado al obtener los gateways.
    try {
        $all_gateways = WC()->payment_gateways()->payment_gateways();
        if (!is_array($all_gateways)) {
            $all_gateways = [];
        }
    } catch (Exception $e) {
        $all_gateways = [];
    }

    // 5. Encontrar los métodos faltantes usando funciones de array nativas.
    // array_diff_key encuentra las claves (IDs de gateway) que están en $all_gateways pero no en $plan_methods.
    $missing_gateways = array_diff_key($all_gateways, $plan_methods);
    $hidden_methods = array_keys($missing_gateways);
    $hidden_methods_csv = implode(',', $hidden_methods);

    // 6. Extraer las cuentas de forma más flexible, sin un 'switch' rígido.
    // Se define un mapa para que sea fácil de extender en el futuro.
    $account_map = [
        'woo_squuad_stripe' => 'connected_account',
        'flywire' => 'flywire_portal_code',
        'zelle_payment' => 'zelle_account',
        'aes_payment' => 'bank_transfer_account',
    ];

    $accounts = [
        'connected_account' => '',
        'flywire_portal_code' => '',
        'zelle_account' => '',
        'bank_transfer_account' => '',
    ];

    foreach ($plan_methods as $method_id => $method_data) {
        if (isset($account_map[$method_id])) {
            $account_key = $account_map[$method_id];
            $accounts[$account_key] = $method_data->account_identificator;
        }
    }

    // 7. Devolver el resultado combinado.
    return array_merge([
        'hidden_methods' => $hidden_methods,
        'hidden_methods_csv' => $hidden_methods_csv,
    ], $accounts);
}

function edusystem_dynamic_links_copy_js()
{
    if (isset($_GET['page']) && $_GET['page'] === 'add_admin_form_dynamic_link_content') {
?>
        <script>
            function copyToClipboard(text, el) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(function() {
                        el.innerText = 'Copied!';
                        setTimeout(function() {
                            el.innerText = '<?php echo esc_js(__('Copy Link', 'edusystem')); ?>';
                        }, 1500);
                    });
                } else {
                    var tempInput = document.createElement('input');
                    tempInput.value = text;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    el.innerText = 'Copied!';
                    setTimeout(function() {
                        el.innerText = '<?php echo esc_js(__('Copy Link', 'edusystem')); ?>';
                    }, 1500);
                }
            }
        </script>
<?php
    }
}
add_action('admin_footer', 'edusystem_dynamic_links_copy_js');
